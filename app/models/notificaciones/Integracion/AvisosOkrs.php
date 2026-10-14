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
        return self::responsablesAntes($mysqli, 'resultadoClave', $idResultado);
    }

    /** Tras crear o editar un KR: avisa a los responsables que no estaban antes. */
    public static function resultadoClaveGuardado(mysqli $mysqli, int $idResultado, array $responsablesAnteriores, $idAutor): void
    {
        try {
            $kr = (new OkrsLector(new Conexion($mysqli)))->resultadoClave($idResultado);
            if ($kr === null) {
                return;
            }
            self::avisarNuevos($mysqli, CodigoNotificacion::OKRS_KR_ASIGNACION, $kr, $responsablesAnteriores, $idAutor, [
                'datos_plantilla' => ['kr_titulo' => $kr['titulo'], 'okr_titulo' => $kr['okr_titulo']],
                'url' => '?pg=okrs/okr/resultados&id=' . $kr['id_okrs'] . '&id_resultado=' . $kr['id'],
                'tipo_registro' => 'OKRS_KR',
            ]);
        } catch (Throwable $error) {
            self::registrar('resultadoClaveGuardado', $error);
        }
    }

    /** Responsables de la iniciativa antes de editarla. */
    public static function responsablesDeIniciativa(mysqli $mysqli, int $idIniciativa): array
    {
        return self::responsablesAntes($mysqli, 'iniciativa', $idIniciativa);
    }

    /** Tras crear o editar una iniciativa: avisa a los responsables que no estaban antes. */
    public static function iniciativaGuardada(mysqli $mysqli, int $idIniciativa, array $responsablesAnteriores, $idAutor): void
    {
        try {
            $iniciativa = (new OkrsLector(new Conexion($mysqli)))->iniciativa($idIniciativa);
            if ($iniciativa === null) {
                return;
            }
            self::avisarNuevos($mysqli, CodigoNotificacion::OKRS_INICIATIVA_ASIGNACION, $iniciativa, $responsablesAnteriores, $idAutor, [
                'datos_plantilla' => ['iniciativa_titulo' => $iniciativa['titulo'], 'kr_titulo' => $iniciativa['kr_titulo']],
                'url' => '?pg=okrs/gestionar_iniciativa&id_okrs=' . $iniciativa['id_okrs'] . '&id_resultado=' . $iniciativa['id_resultado'] . '&id_iniciativa=' . $iniciativa['id'],
                'tipo_registro' => 'OKRS_INICIATIVA',
            ]);
        } catch (Throwable $error) {
            self::registrar('iniciativaGuardada', $error);
        }
    }

    private static function responsablesAntes(mysqli $mysqli, string $lectura, int $id): array
    {
        try {
            $registro = (new OkrsLector(new Conexion($mysqli)))->$lectura($id);
            return $registro !== null ? $registro['responsables'] : [];
        } catch (Throwable $error) {
            self::registrar('responsablesAntes(' . $lectura . ')', $error);
            return [];
        }
    }

    /** $registro trae 'id', 'id_empresa' y 'responsables'; $opciones, lo propio de cada código. */
    private static function avisarNuevos(mysqli $mysqli, string $codigo, array $registro, array $responsablesAnteriores, $idAutor, array $opciones): void
    {
        $nuevos = array_values(array_diff($registro['responsables'], array_map('intval', $responsablesAnteriores)));
        if (!$nuevos) {
            return;
        }
        GenerarNotificacion::ejecutar($mysqli, $codigo, $registro['id_empresa'], $nuevos, $opciones + [
            'id_registro' => $registro['id'],
            'id_empleado_autor' => $idAutor ? (int) $idAutor : null,
        ]);
    }

    private static function registrar(string $donde, Throwable $error): void
    {
        error_log('[notificaciones] AvisosOkrs::' . $donde . ': ' . get_class($error) . ': ' . $error->getMessage());
    }
}
