<?php
namespace Notificaciones\Aplicacion;

use DateTimeImmutable;
use InvalidArgumentException;
use mysqli;
use Notificaciones\Dominio\CamposSensibles;
use Notificaciones\Dominio\CodigoNotificacion;
use Notificaciones\Dominio\Excepciones\CampoNoPermitido;
use Notificaciones\Dominio\Excepciones\UrlNoPermitida;
use Notificaciones\Dominio\Plantilla;
use Notificaciones\Dominio\TipoNotificacion;
use Notificaciones\Infraestructura\BaseDatos\Consulta;
use Notificaciones\Infraestructura\BaseDatos\DestinatariosRepositorio;
use Notificaciones\Infraestructura\BaseDatos\EmpleadosLector;
use Notificaciones\Infraestructura\BaseDatos\ErrorBaseDatos;
use Notificaciones\Infraestructura\BaseDatos\EventosRepositorio;
use Notificaciones\Infraestructura\BaseDatos\TiposRepositorio;

/**
 * Único punto de entrada para crear notificaciones.
 *
 *   GenerarNotificacion::ejecutar(
 *       $connect_admin,
 *       CodigoNotificacion::OKRS_KR_ASIGNACION,       // ejemplo; el catálogo hoy está vacío
 *       $user_log['id_empresa'],
 *       [45, 78],                                  // Empleados.id destinatarios
 *       [
 *           'datos'         => ['titulo' => '...', 'mensaje' => '...'],
 *           'url'           => '?pg=kpis/gestionar_kpi&id=10',
 *           'tipo_registro' => 'kpi',
 *           'id_registro'   => 10,
 *           'fecha_limite'  => '2026-10-31',
 *           'id_autor'      => $user_log['id'],
 *           'clave_unica'   => 'kpi:10:asignacion',
 *       ]
 *   );
 *
 * Todas las opciones son opcionales; una opción con otro nombre es un error
 * (así un error de tipeo no se pierde en silencio).
 *
 * Guarda la notificación y su bandeja; no envía correo (eso lo hace el cron).
 * Devuelve el id de la notificación, la existente si la clave_unica ya estaba,
 * o NULL si el tipo está INACTIVO o si ningún destinatario quedó válido. Un
 * tipo desactivado no rompe el flujo que llama: simplemente no notifica.
 */
final class GenerarNotificacion
{
    private const OPCIONES_VALIDAS = ['datos', 'url', 'tipo_registro', 'id_registro', 'fecha_limite', 'id_autor', 'clave_unica'];
    private const PREFIJO_URL_PORTAL = '?pg=';
    private const LARGO_MAXIMO_TITULO = 255;
    private const LARGO_MAXIMO_CUERPO = 1000;

    public static function ejecutar(mysqli $conexion, string $codigo, int $idEmpresa, array $destinatarios, array $opciones = []): ?int
    {
        self::validarOpciones($opciones);
        $datos = isset($opciones['datos']) ? $opciones['datos'] : [];
        $url = isset($opciones['url']) ? (string) $opciones['url'] : null;
        $idAutor = isset($opciones['id_autor']) ? (int) $opciones['id_autor'] : null;
        $claveUnica = isset($opciones['clave_unica']) ? (string) $opciones['clave_unica'] : null;

        $ahora = new DateTimeImmutable();
        $consulta = new Consulta($conexion);
        $eventos = new EventosRepositorio($consulta);

        $tipo = (new TiposRepositorio($consulta))->buscarActivo($codigo, $idEmpresa);
        if ($tipo === null) {
            return null;
        }
        self::validarDatos($codigo, $datos);
        self::validarUrl($url);
        $limite = self::interpretarFechaLimite(isset($opciones['fecha_limite']) ? (string) $opciones['fecha_limite'] : null);

        if ($claveUnica !== null) {
            $existente = $eventos->idPorClaveUnica($idEmpresa, $claveUnica);
            if ($existente !== null) {
                return $existente;
            }
        }

        $idsCandidatos = self::idsCandidatos($destinatarios, $tipo, $idAutor);
        $activos = (new EmpleadosLector($consulta))->activosDeEmpresa($idsCandidatos, $idEmpresa);
        if (!$activos) {
            return null;
        }

        $valores = $datos + ['fecha_limite' => Plantilla::formatearFecha($limite)];
        $evento = [
            'id_empresa' => $idEmpresa,
            'id_tipo' => $tipo->id,
            'codigo' => $codigo,
            'clase' => $tipo->clase,
            'titulo' => mb_substr(Plantilla::renderizarTexto($tipo->plataformaTitulo, $valores), 0, self::LARGO_MAXIMO_TITULO),
            'cuerpo' => mb_substr(Plantilla::renderizarTexto($tipo->plataformaCuerpo, $valores), 0, self::LARGO_MAXIMO_CUERPO),
            'url' => $url,
            'tipo_registro' => isset($opciones['tipo_registro']) ? (string) $opciones['tipo_registro'] : null,
            'id_registro' => isset($opciones['id_registro']) ? (int) $opciones['id_registro'] : null,
            'datos' => json_encode($datos, JSON_UNESCAPED_UNICODE),
            'fecha_limite' => $limite,
            'id_autor' => $idAutor,
            'clave_unica' => $claveUnica,
        ];
        $primerAviso = $tipo->primerAviso($limite, $ahora);
        $bandejas = new DestinatariosRepositorio($consulta);

        try {
            return $consulta->transaccion(function () use ($eventos, $bandejas, $evento, $activos, $idEmpresa, $primerAviso, $ahora) {
                $idEvento = $eventos->insertar($evento, $ahora);
                foreach (array_keys($activos) as $idEmpleado) {
                    $bandejas->insertar($idEmpresa, $idEvento, (int) $idEmpleado, $primerAviso, $ahora);
                }
                return $idEvento;
            });
        } catch (ErrorBaseDatos $error) {
            // Otra petición creó la misma clave_unica entre la consulta y el INSERT.
            $otraPeticionLaCreo = $error->esLlaveDuplicada() && $claveUnica !== null;
            if (!$otraPeticionLaCreo) {
                throw $error;
            }
            return $eventos->idPorClaveUnica($idEmpresa, $claveUnica);
        }
    }

    private static function validarOpciones(array $opciones): void
    {
        $desconocidas = array_diff(array_keys($opciones), self::OPCIONES_VALIDAS);
        if ($desconocidas) {
            throw new InvalidArgumentException('Opciones desconocidas: ' . implode(', ', $desconocidas) . '. Válidas: ' . implode(', ', self::OPCIONES_VALIDAS));
        }
    }

    private static function validarDatos(string $codigo, array $datos): void
    {
        $nombres = array_keys($datos);
        CamposSensibles::validar($nombres);

        $noDeclarados = array_diff($nombres, CodigoNotificacion::campos($codigo));
        if ($noDeclarados) {
            throw new CampoNoPermitido("'$codigo' no acepta: " . implode(', ', $noDeclarados));
        }
        foreach ($datos as $nombre => $valor) {
            if (!is_scalar($valor) && $valor !== null) {
                throw new CampoNoPermitido("El dato '$nombre' debe ser texto o número.");
            }
        }
    }

    /** Solo rutas del portal: evita que un enlace de notificación lleve a otro sitio. */
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
    private static function interpretarFechaLimite(?string $fechaLimite): ?DateTimeImmutable
    {
        if ($fechaLimite === null) {
            return null;
        }
        $soloDia = DateTimeImmutable::createFromFormat('!Y-m-d', $fechaLimite);
        if ($soloDia !== false && $soloDia->format('Y-m-d') === $fechaLimite) {
            return $soloDia->setTime(23, 59, 59);
        }
        $conHora = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $fechaLimite);
        if ($conHora !== false && $conHora->format('Y-m-d H:i:s') === $fechaLimite) {
            return $conHora;
        }
        throw new InvalidArgumentException("fecha_limite inválida: '$fechaLimite' (se espera Y-m-d o Y-m-d H:i:s).");
    }

    private static function idsCandidatos(array $destinatarios, TipoNotificacion $tipo, ?int $idAutor): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $destinatarios), function ($id) {
            return $id > 0;
        })));
        $excluirAutor = !$tipo->notificarAutor && $idAutor !== null;
        if ($excluirAutor) {
            $ids = array_values(array_diff($ids, [$idAutor]));
        }
        return $ids;
    }
}
