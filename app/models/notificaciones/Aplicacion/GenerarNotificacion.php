<?php
namespace Notificaciones\Aplicacion;

use DateTimeImmutable;
use InvalidArgumentException;
use mysqli;
use Notificaciones\Dominio\CodigoNotificacion;
use Notificaciones\Dominio\DatosSensibles;
use Notificaciones\Dominio\Excepciones\DatoNoPermitido;
use Notificaciones\Dominio\Excepciones\UrlNoPermitida;
use Notificaciones\Dominio\Plantilla;
use Notificaciones\Dominio\TipoNotificacion;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\DestinatariosRepositorio;
use Notificaciones\Infraestructura\BaseDatos\EmpleadosLector;
use Notificaciones\Infraestructura\BaseDatos\ErrorBaseDatos;
use Notificaciones\Infraestructura\BaseDatos\NotificacionesRepositorio;
use Notificaciones\Infraestructura\BaseDatos\TiposRepositorio;

/**
 * Único punto de entrada para crear notificaciones.
 *
 *   GenerarNotificacion::ejecutar(
 *       $connect_admin,
 *       CodigoNotificacion::OKRS_KR_ASIGNACION,    // ejemplo; el catálogo hoy está vacío
 *       $user_log['id_empresa'],
 *       [45, 78],                                   // ids de Empleados destinatarios
 *       [
 *           'datos_plantilla'   => ['kr_titulo' => '...'],
 *           'url'               => '?pg=okrs/okr/resultados&id=12&id_resultado=123',
 *           'tipo_registro'     => 'OKRS_KR',
 *           'id_registro'       => 123,
 *           'fecha_vencimiento' => '2026-10-31',
 *           'id_empleado_autor' => $user_log['id'],
 *           'clave_evento'      => 'OKRS_KR_ASIGNACION:123',
 *       ]
 *   );
 *
 * Las opciones se llaman igual que las columnas de Notificaciones y todas son
 * opcionales; una opción con otro nombre es un error (un error de tipeo no se
 * pierde en silencio).
 *
 * Guarda la notificación y sus destinatarios; no envía correo (eso lo hace el
 * cron). Devuelve el id de la notificación, el de la existente si clave_evento
 * ya estaba, o NULL si el tipo está INACTIVO o ningún destinatario quedó válido.
 * Un tipo desactivado no rompe el flujo que llama: simplemente no notifica.
 */
final class GenerarNotificacion
{
    private const OPCIONES_VALIDAS = [
        'datos_plantilla', 'url', 'tipo_registro', 'id_registro', 'fecha_vencimiento', 'id_empleado_autor', 'clave_evento',
    ];
    private const PREFIJO_URL_PORTAL = '?pg=';
    private const LARGO_MAXIMO_TITULO = 255;
    private const LARGO_MAXIMO_MENSAJE = 1000;

    public static function ejecutar(mysqli $mysqli, string $codigo, int $idEmpresa, array $idsEmpleados, array $opciones = []): ?int
    {
        self::validarOpciones($opciones);
        $tipo = (new TiposRepositorio(new Conexion($mysqli)))->buscarActivo($codigo, $idEmpresa);
        if ($tipo === null) {
            return null;
        }
        return self::ejecutarConTipo($mysqli, $tipo, $idEmpresa, $idsEmpleados, $opciones);
    }

    /**
     * Igual que ejecutar(), pero con un tipo ya cargado y sin mirar su estado.
     * Solo para el envío de prueba de la administración, que debe funcionar
     * aunque el tipo esté INACTIVO.
     */
    public static function ejecutarConTipo(mysqli $mysqli, TipoNotificacion $tipo, int $idEmpresa, array $idsEmpleados, array $opciones = []): ?int
    {
        self::validarOpciones($opciones);
        $codigo = $tipo->codigo;
        $datosPlantilla = isset($opciones['datos_plantilla']) ? $opciones['datos_plantilla'] : [];
        $url = isset($opciones['url']) ? (string) $opciones['url'] : null;
        $idEmpleadoAutor = isset($opciones['id_empleado_autor']) ? (int) $opciones['id_empleado_autor'] : null;
        $claveEvento = isset($opciones['clave_evento']) ? (string) $opciones['clave_evento'] : null;

        $ahora = new DateTimeImmutable();
        $conexion = new Conexion($mysqli);
        $notificaciones = new NotificacionesRepositorio($conexion);

        self::validarDatosPlantilla($codigo, $datosPlantilla);
        self::validarUrl($url);
        $fechaVencimiento = self::interpretarFechaVencimiento(isset($opciones['fecha_vencimiento']) ? (string) $opciones['fecha_vencimiento'] : null);

        if ($claveEvento !== null) {
            $existente = $notificaciones->idPorClaveEvento($idEmpresa, $claveEvento);
            if ($existente !== null) {
                return $existente;
            }
        }

        $idsCandidatos = self::idsDestinatariosCandidatos($idsEmpleados, $tipo, $idEmpleadoAutor);
        $destinatariosActivos = (new EmpleadosLector($conexion))->activosDeEmpresa($idsCandidatos, $idEmpresa);
        if (!$destinatariosActivos) {
            return null;
        }

        $datosParaTitulo = $datosPlantilla + ['fecha_vencimiento' => Plantilla::formatearFecha($fechaVencimiento)];
        $notificacion = [
            'id_empresa' => $idEmpresa,
            'id_tipo' => $tipo->id,
            'clase' => $tipo->clase,
            'titulo' => mb_substr(Plantilla::renderizarTexto($tipo->plantillaTitulo, $datosParaTitulo), 0, self::LARGO_MAXIMO_TITULO),
            'mensaje' => mb_substr(Plantilla::renderizarTexto($tipo->plantillaMensaje, $datosParaTitulo), 0, self::LARGO_MAXIMO_MENSAJE),
            'url' => $url,
            'tipo_registro' => isset($opciones['tipo_registro']) ? (string) $opciones['tipo_registro'] : null,
            'id_registro' => isset($opciones['id_registro']) ? (int) $opciones['id_registro'] : null,
            'datos_plantilla' => json_encode($datosPlantilla, JSON_UNESCAPED_UNICODE),
            'fecha_vencimiento' => $fechaVencimiento,
            'id_empleado_autor' => $idEmpleadoAutor,
            'clave_evento' => $claveEvento,
        ];
        $fechaPrimerCorreo = $tipo->fechaPrimerCorreo($fechaVencimiento, $ahora);
        $destinatarios = new DestinatariosRepositorio($conexion);

        try {
            return $conexion->enTransaccion(function () use ($notificaciones, $destinatarios, $notificacion, $destinatariosActivos, $idEmpresa, $fechaPrimerCorreo, $ahora) {
                $idNotificacion = $notificaciones->insertar($notificacion, $ahora);
                foreach (array_keys($destinatariosActivos) as $idEmpleado) {
                    $destinatarios->insertar($idEmpresa, $idNotificacion, (int) $idEmpleado, $fechaPrimerCorreo, $ahora);
                }
                return $idNotificacion;
            });
        } catch (ErrorBaseDatos $error) {
            // Otra petición creó la misma clave_evento entre la consulta y el INSERT.
            $otraPeticionLaCreo = $error->esLlaveDuplicada() && $claveEvento !== null;
            if (!$otraPeticionLaCreo) {
                throw $error;
            }
            return $notificaciones->idPorClaveEvento($idEmpresa, $claveEvento);
        }
    }

    private static function validarOpciones(array $opciones): void
    {
        $desconocidas = array_diff(array_keys($opciones), self::OPCIONES_VALIDAS);
        if ($desconocidas) {
            throw new InvalidArgumentException('Opciones desconocidas: ' . implode(', ', $desconocidas) . '. Válidas: ' . implode(', ', self::OPCIONES_VALIDAS));
        }
    }

    private static function validarDatosPlantilla(string $codigo, array $datosPlantilla): void
    {
        $nombres = array_keys($datosPlantilla);
        DatosSensibles::validar($nombres);

        $noDeclarados = array_diff($nombres, CodigoNotificacion::datosPermitidos($codigo));
        if ($noDeclarados) {
            throw new DatoNoPermitido("'$codigo' no acepta: " . implode(', ', $noDeclarados));
        }
        foreach ($datosPlantilla as $nombre => $valor) {
            if (!is_scalar($valor) && $valor !== null) {
                throw new DatoNoPermitido("El dato '$nombre' debe ser texto o número.");
            }
        }
    }

    /** Solo rutas del portal: evita que el enlace de una notificación lleve a otro sitio. */
    private static function validarUrl(?string $url): void
    {
        if ($url === null) {
            return;
        }
        $empiezaComoRutaDelPortal = strpos($url, self::PREFIJO_URL_PORTAL) === 0;
        $esRutaDelPortal = $empiezaComoRutaDelPortal && !preg_match('/[\s"\'<>]/', $url);
        if (!$esRutaDelPortal) {
            throw new UrlNoPermitida("La url debe empezar por '" . self::PREFIJO_URL_PORTAL . "' y no llevar espacios ni comillas.");
        }
    }

    /** 'Y-m-d' vence al final de ese día; también acepta 'Y-m-d H:i:s'. */
    private static function interpretarFechaVencimiento(?string $fechaVencimiento): ?DateTimeImmutable
    {
        if ($fechaVencimiento === null) {
            return null;
        }
        $soloDia = DateTimeImmutable::createFromFormat('!Y-m-d', $fechaVencimiento);
        if ($soloDia !== false && $soloDia->format('Y-m-d') === $fechaVencimiento) {
            return $soloDia->setTime(23, 59, 59);
        }
        $conHora = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $fechaVencimiento);
        if ($conHora !== false && $conHora->format('Y-m-d H:i:s') === $fechaVencimiento) {
            return $conHora;
        }
        throw new InvalidArgumentException("fecha_vencimiento inválida: '$fechaVencimiento' (se espera Y-m-d o Y-m-d H:i:s).");
    }

    /** Ids únicos y positivos; sin el autor salvo que el tipo lo incluya. */
    private static function idsDestinatariosCandidatos(array $idsEmpleados, TipoNotificacion $tipo, ?int $idEmpleadoAutor): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $idsEmpleados), function ($id) {
            return $id > 0;
        })));
        $excluirAutor = !$tipo->incluyeAutor && $idEmpleadoAutor !== null;
        if ($excluirAutor) {
            $ids = array_values(array_diff($ids, [$idEmpleadoAutor]));
        }
        return $ids;
    }
}
