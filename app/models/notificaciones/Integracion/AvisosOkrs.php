<?php
namespace Notificaciones\Integracion;

use mysqli;
use Notificaciones\Aplicacion\GenerarNotificacion;
use Notificaciones\Dominio\CodigoNotificacion;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\OkrsLector;
use Throwable;

/**
 * Puerta de entrada de las pantallas de OKRs al módulo. Se llama justo después
 * de guardar y nunca lanza: si el aviso falla, el guardado del usuario sigue su
 * curso y el error queda en el log del servidor.
 */
final class AvisosOkrs
{
    /** Responsables del KR antes de editarlo, para avisar solo a los nuevos. */
    public static function responsablesDeResultadoClave(mysqli $mysqli, int $idResultado): array
    {
        try {
            $resultado = (new OkrsLector(new Conexion($mysqli)))->resultadoClave($idResultado);
            return $resultado !== null ? $resultado['responsables'] : [];
        } catch (Throwable $error) {
            self::registrar('responsablesDeResultadoClave', $error);
            return [];
        }
    }

    /** Tras crear o editar un KR: avisa a los responsables que no estaban antes. */
    public static function resultadoClaveGuardado(mysqli $mysqli, int $idResultado, array $responsablesAnteriores, $idAutor): void
    {
        try {
            $resultado = (new OkrsLector(new Conexion($mysqli)))->resultadoClave($idResultado);
            if ($resultado === null) {
                return;
            }
            $nuevos = array_values(array_diff($resultado['responsables'], array_map('intval', $responsablesAnteriores)));
            if (!$nuevos) {
                return;
            }
            GenerarNotificacion::ejecutar($mysqli, CodigoNotificacion::OKRS_KR_ASIGNACION, $resultado['id_empresa'], $nuevos, [
                'datos_plantilla' => ['kr_titulo' => $resultado['titulo'], 'okr_titulo' => $resultado['okr_titulo']],
                'url' => '?pg=okrs/okr/resultados&id=' . $resultado['id_okrs'] . '&id_resultado=' . $resultado['id'],
                'tipo_registro' => 'OKRS_KR',
                'id_registro' => $resultado['id'],
                'id_empleado_autor' => $idAutor ? (int) $idAutor : null,
            ]);
        } catch (Throwable $error) {
            self::registrar('resultadoClaveGuardado', $error);
        }
    }

    private static function registrar(string $donde, Throwable $error): void
    {
        error_log('[notificaciones] AvisosOkrs::' . $donde . ': ' . get_class($error) . ': ' . $error->getMessage());
    }
}
