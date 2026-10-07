<?php
namespace Notificaciones\Aplicacion;

use DateTimeImmutable;
use mysqli;
use Notificaciones\Dominio\ClaseNotificacion;
use Notificaciones\Dominio\CodigoNotificacion;
use Notificaciones\Dominio\EstadoTipo;
use Notificaciones\Dominio\Excepciones\ErrorNotificacion;
use Notificaciones\Dominio\Excepciones\TipoSinImplementar;
use Notificaciones\Dominio\NotificacionPrueba;
use Notificaciones\Dominio\Plantilla;
use Notificaciones\Dominio\TipoNotificacion;
use Notificaciones\Dominio\UnidadIntervalo;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\TiposRepositorio;
use Notificaciones\Infraestructura\Correo\MarcoCorreo;

/**
 * Administración de tipos desde la plataforma: crear, configurar, previsualizar
 * y probar. Un tipo se puede crear antes de que su código exista en el sistema
 * ("sin implementar"); el desarrollador agrega después la constante en
 * CodigoNotificacion y la llamada a GenerarNotificacion. El código no se edita.
 */
final class AdministrarTipos
{
    private const URL_PRUEBA = '?pg=notificaciones/bandeja';
    private const DESTINATARIO_DE_EJEMPLO = 'Nombre del destinatario';
    private const DIAS_VENCIMIENTO_DE_EJEMPLO = 5;
    private const LARGO_MAXIMO_CODIGO = 80;

    /** Todos los tipos, con si están implementados y si su configuración es válida. */
    public static function listar(mysqli $mysqli): array
    {
        $filas = (new TiposRepositorio(new Conexion($mysqli)))->filasGenerales();
        return array_map(function (array $fila) {
            return self::completar($fila);
        }, $filas);
    }

    /** El tipo con sus datos permitidos; NULL si no existe. */
    public static function obtener(mysqli $mysqli, int $idTipo): ?array
    {
        $fila = (new TiposRepositorio(new Conexion($mysqli)))->filaPorId($idTipo);
        return $fila === null ? null : self::completar($fila);
    }

    /**
     * Crea un tipo nuevo, INACTIVO, con código, nombre y clase.
     * ['errores' => [...], 'id' => id del tipo creado o NULL].
     */
    public static function crear(mysqli $mysqli, array $formulario): array
    {
        $tipos = new TiposRepositorio(new Conexion($mysqli));
        $codigo = isset($formulario['codigo']) ? strtoupper(trim((string) $formulario['codigo'])) : '';
        $nombre = isset($formulario['nombre']) ? trim((string) $formulario['nombre']) : '';
        $clase = isset($formulario['clase']) ? (string) $formulario['clase'] : '';

        $errores = [];
        if (!CodigoNotificacion::tieneFormatoValido($codigo) || strlen($codigo) > self::LARGO_MAXIMO_CODIGO) {
            $errores[] = 'El código debe estar en MAYÚSCULAS_CON_GUION_BAJO, empezar con una letra y tener hasta ' . self::LARGO_MAXIMO_CODIGO . ' caracteres (p. ej. KPIS_KPI_COMENTARIO).';
        } elseif ($tipos->existeCodigo($codigo)) {
            $errores[] = "Ya existe un tipo con el código $codigo.";
        }
        if ($nombre === '') {
            $errores[] = 'El nombre es obligatorio.';
        }
        if (!ClaseNotificacion::esValida($clase)) {
            $errores[] = 'Clase inválida.';
        }
        if ($errores) {
            return ['errores' => $errores, 'id' => null];
        }

        $valores = self::normalizar([
            'nombre' => $nombre,
            'clase' => $clase,
            'muestra_en_bandeja' => 1,
            'plantilla_titulo' => $nombre,
            'estado' => EstadoTipo::INACTIVO,
        ]);
        $id = $tipos->crear($codigo, $valores, new DateTimeImmutable());
        return ['errores' => [], 'id' => $id];
    }

    /** Guarda el formulario. Devuelve los errores ([] = guardado). */
    public static function guardar(mysqli $mysqli, int $idTipo, array $formulario): array
    {
        $tipos = new TiposRepositorio(new Conexion($mysqli));
        $actual = $tipos->filaPorId($idTipo);
        if ($actual === null) {
            return ['El tipo no existe.'];
        }

        $valores = self::normalizar($formulario);
        $errores = self::validar($actual['codigo'], $valores);
        if ($errores) {
            return $errores;
        }
        $tipos->actualizar($idTipo, $valores, new DateTimeImmutable());
        return [];
    }

    /**
     * Cómo se verían la bandeja y el correo con los valores del formulario y
     * datos de ejemplo. ['error' => string|null, 'titulo', 'mensaje', 'asunto', 'cuerpo_html'].
     */
    public static function vistaPrevia(mysqli $mysqli, int $idTipo, array $formulario): array
    {
        $actual = (new TiposRepositorio(new Conexion($mysqli)))->filaPorId($idTipo);
        if ($actual === null) {
            return ['error' => 'El tipo no existe.'];
        }
        $codigo = $actual['codigo'];
        $valores = self::normalizar($formulario);
        $errores = self::validar($codigo, $valores);
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
     * normales del despacho (modo_operacion, desvío de prueba). Un tipo sin
     * implementar no se puede probar: aún no se sabe qué datos llevará.
     */
    public static function enviarPrueba(mysqli $mysqli, int $idEmpresa, int $idTipo, int $idEmpleado): ?int
    {
        $tipo = (new TiposRepositorio(new Conexion($mysqli)))->buscarPorId($idTipo);
        if ($tipo === null) {
            return null;
        }
        if (!CodigoNotificacion::existe($tipo->codigo)) {
            throw new TipoSinImplementar("El código {$tipo->codigo} aún no está implementado en el sistema.");
        }
        return GenerarNotificacion::ejecutarConTipo($mysqli, $tipo, $idEmpresa, [$idEmpleado], [
            'datos_plantilla' => self::datosDeEjemplo($tipo->codigo),
            'url' => self::URL_PRUEBA,
            'tipo_registro' => NotificacionPrueba::TIPO_REGISTRO,
            'fecha_vencimiento' => (new DateTimeImmutable('+' . self::DIAS_VENCIMIENTO_DE_EJEMPLO . ' days'))->format('Y-m-d'),
        ]);
    }

    /** Agrega a la fila lo que la administración muestra además de las columnas. */
    private static function completar(array $fila): array
    {
        $fila['implementado'] = CodigoNotificacion::existe($fila['codigo']);
        $fila['datos_permitidos'] = CodigoNotificacion::datosPermitidosSiExiste($fila['codigo']);
        $fila['error_configuracion'] = self::errorDeConfiguracion($fila);
        return $fila;
    }

    private static function datosDeEjemplo(string $codigo): array
    {
        $datos = [];
        foreach (CodigoNotificacion::datosPermitidosSiExiste($codigo) as $nombre) {
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
