<?php
namespace Notificaciones\Aplicacion;

use DateTimeImmutable;
use mysqli;
use Notificaciones\Dominio\ClaseNotificacion;
use Notificaciones\Dominio\CodigoNotificacion;
use Notificaciones\Dominio\EstadoTipo;
use Notificaciones\Dominio\NotificacionPrueba;
use Notificaciones\Dominio\Excepciones\ErrorNotificacion;
use Notificaciones\Dominio\Plantilla;
use Notificaciones\Dominio\TipoNotificacion;
use Notificaciones\Dominio\UnidadIntervalo;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\TiposRepositorio;
use Notificaciones\Infraestructura\Correo\MarcoCorreo;

/**
 * Administración de tipos desde la plataforma. Todo lo que se guarda queda como
 * configuración propia de la empresa del administrador; la fila por defecto
 * (id_empresa = 0) solo se cambia por SQL. El código no se edita.
 */
final class AdministrarTipos
{
    private const URL_PRUEBA = '?pg=notificaciones/bandeja';
    private const DESTINATARIO_DE_EJEMPLO = 'Nombre del destinatario';
    private const DIAS_VENCIMIENTO_DE_EJEMPLO = 5;

    /** Tipos que aplican a la empresa, con si son propios y si su configuración es válida. */
    public static function listar(mysqli $mysqli, int $idEmpresa): array
    {
        $filas = (new TiposRepositorio(new Conexion($mysqli)))->filasDeEmpresa($idEmpresa);
        return array_map(function (array $fila) use ($idEmpresa) {
            $fila['es_de_la_empresa'] = (int) $fila['id_empresa'] === $idEmpresa;
            $fila['error_configuracion'] = self::errorDeConfiguracion($fila);
            return $fila;
        }, $filas);
    }

    /** La fila que aplica a la empresa para ese código; NULL si no existe. */
    public static function obtener(mysqli $mysqli, int $idEmpresa, string $codigo): ?array
    {
        $fila = (new TiposRepositorio(new Conexion($mysqli)))->filaDeEmpresa($codigo, $idEmpresa);
        if ($fila === null) {
            return null;
        }
        $fila['es_de_la_empresa'] = (int) $fila['id_empresa'] === $idEmpresa;
        $fila['datos_permitidos'] = CodigoNotificacion::existe($codigo) ? CodigoNotificacion::datosPermitidos($codigo) : [];
        $fila['error_configuracion'] = self::errorDeConfiguracion($fila);
        return $fila;
    }

    /** Guarda el formulario como configuración de la empresa. Devuelve los errores ([] = guardado). */
    public static function guardar(mysqli $mysqli, int $idEmpresa, string $codigo, array $formulario): array
    {
        $conexion = new Conexion($mysqli);
        $tipos = new TiposRepositorio($conexion);
        $actual = $tipos->filaDeEmpresa($codigo, $idEmpresa);
        if ($actual === null || !CodigoNotificacion::existe($codigo)) {
            return ["El código '$codigo' no existe."];
        }

        $valores = self::normalizar($formulario);
        $errores = self::validar($codigo, $valores);
        if ($errores) {
            return $errores;
        }
        $tipos->guardarDeEmpresa($idEmpresa, $codigo, $valores, new DateTimeImmutable());
        return [];
    }

    /**
     * Cómo se verían la bandeja y el correo con los valores del formulario y
     * datos de ejemplo. ['error' => string|null, 'titulo', 'mensaje', 'asunto', 'cuerpo_html'].
     */
    public static function vistaPrevia(string $codigo, array $formulario): array
    {
        $valores = self::normalizar($formulario);
        $errores = CodigoNotificacion::existe($codigo) ? self::validar($codigo, $valores) : ["El código '$codigo' no existe."];
        if ($errores) {
            return ['error' => implode(' ', $errores)];
        }

        $datos = self::datosDeEjemplo($codigo) + [
            'destinatario_nombre' => self::DESTINATARIO_DE_EJEMPLO,
            'fecha_vencimiento' => Plantilla::formatearFecha(new DateTimeImmutable('+' . self::DIAS_VENCIMIENTO_DE_EJEMPLO . ' days')),
            'enlace' => '#',
        ];
        return [
            'error' => null,
            'titulo' => Plantilla::renderizarTexto($valores['plantilla_titulo'], $datos),
            'mensaje' => Plantilla::renderizarTexto($valores['plantilla_mensaje'], $datos),
            'asunto' => Plantilla::renderizarTexto($valores['plantilla_asunto_correo'], $datos),
            'cuerpo_html' => MarcoCorreo::envolver(Plantilla::renderizarHtml($valores['plantilla_cuerpo_correo'], $datos)),
        ];
    }

    /**
     * Genera una notificación de ese tipo con datos de ejemplo, solo para el
     * administrador, aunque el tipo esté INACTIVO. El correo sigue las reglas
     * normales del despacho (modo_operacion, desvío de prueba).
     */
    public static function enviarPrueba(mysqli $mysqli, int $idEmpresa, string $codigo, int $idEmpleado): ?int
    {
        $fila = (new TiposRepositorio(new Conexion($mysqli)))->filaDeEmpresa($codigo, $idEmpresa);
        if ($fila === null) {
            return null;
        }
        $tipo = TipoNotificacion::desdeFila($fila);
        return GenerarNotificacion::ejecutarConTipo($mysqli, $tipo, $idEmpresa, [$idEmpleado], [
            'datos_plantilla' => self::datosDeEjemplo($codigo),
            'url' => self::URL_PRUEBA,
            'tipo_registro' => NotificacionPrueba::TIPO_REGISTRO,
            'fecha_vencimiento' => (new DateTimeImmutable('+' . self::DIAS_VENCIMIENTO_DE_EJEMPLO . ' days'))->format('Y-m-d'),
        ]);
    }

    private static function datosDeEjemplo(string $codigo): array
    {
        $datos = [];
        foreach (CodigoNotificacion::datosPermitidos($codigo) as $nombre) {
            $datos[$nombre] = "($nombre de ejemplo)";
        }
        return $datos;
    }

    /** Del formulario a los valores de las columnas: textos recortados (vacío = NULL), enteros y casillas. */
    private static function normalizar(array $formulario): array
    {
        $texto = function (string $campo) use ($formulario) {
            $valor = isset($formulario[$campo]) ? trim((string) $formulario[$campo]) : '';
            return $valor === '' ? null : $valor;
        };
        $entero = function (string $campo) use ($texto) {
            $valor = $texto($campo);
            return $valor === null ? null : (int) $valor;
        };
        $casilla = function (string $campo) use ($formulario) {
            return empty($formulario[$campo]) ? 0 : 1;
        };

        return [
            'nombre' => $texto('nombre'),
            'descripcion' => $texto('descripcion'),
            'clase' => $texto('clase'),
            'dias_antes_de_vencer' => $entero('dias_antes_de_vencer'),
            'dias_entre_recordatorios' => $entero('dias_entre_recordatorios'),
            'maximo_recordatorios' => $entero('maximo_recordatorios'),
            'incluye_autor' => $casilla('incluye_autor'),
            'muestra_en_bandeja' => $casilla('muestra_en_bandeja'),
            'envia_correo' => $casilla('envia_correo'),
            'intervalo_resumen' => $entero('intervalo_resumen'),
            'unidad_intervalo_resumen' => $texto('unidad_intervalo_resumen'),
            'plantilla_titulo' => $texto('plantilla_titulo'),
            'plantilla_mensaje' => $texto('plantilla_mensaje'),
            'plantilla_asunto_correo' => $texto('plantilla_asunto_correo'),
            'plantilla_cuerpo_correo' => $texto('plantilla_cuerpo_correo'),
            'icono' => $texto('icono'),
            'color' => $texto('color'),
            'estado' => $texto('estado'),
        ];
    }

    private static function validar(string $codigo, array $valores): array
    {
        $errores = [];
        if ($valores['nombre'] === null) {
            $errores[] = 'El nombre es obligatorio.';
        }
        if ($valores['plantilla_titulo'] === null) {
            $errores[] = 'La plantilla del título es obligatoria.';
        }
        if ($valores['estado'] === null || !EstadoTipo::esValido($valores['estado'])) {
            $errores[] = 'Estado inválido.';
        }
        if ($valores['clase'] === null || !ClaseNotificacion::esValida($valores['clase'])) {
            $errores[] = 'Clase inválida.';
        }
        if ($valores['unidad_intervalo_resumen'] !== null && !UnidadIntervalo::esValida($valores['unidad_intervalo_resumen'])) {
            $errores[] = 'Unidad del intervalo inválida.';
        }
        if ($errores) {
            return $errores;
        }

        // El resto de reglas (marcadores permitidos, intervalo completo, plantillas de correo) las aplica el dominio.
        $error = self::errorDeConfiguracion($valores + ['id' => 0, 'codigo' => $codigo]);
        return $error === null ? [] : [$error];
    }

    private static function errorDeConfiguracion(array $fila): ?string
    {
        try {
            TipoNotificacion::desdeFila($fila);
            return null;
        } catch (ErrorNotificacion $error) {
            return $error->getMessage();
        }
    }
}
