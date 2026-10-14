<?php
namespace Notificaciones\Integracion;

use mysqli;
use Notificaciones\Aplicacion\GenerarNotificacion;
use Notificaciones\Dominio\CodigoNotificacion;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\EmpleadosLector;
use Notificaciones\Infraestructura\BaseDatos\KpisLector;
use Throwable;

/**
 * Puerta de entrada de las pantallas de KPIs al módulo. Se llama justo después
 * de guardar y nunca lanza: si el aviso falla, el guardado del usuario sigue su
 * curso y el error queda en el log del servidor.
 *
 * Los avisos de actividad (comentario, documento, resultado, integrante
 * retirado) van solo a los Líderes del KPI.
 */
final class AvisosKpis
{
    private const MESES = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

    /** Integrantes del KPI antes de asociar, para avisar solo a los nuevos. */
    public static function integrantesDeKpi(mysqli $mysqli, int $idKpi): array
    {
        try {
            $kpi = (new KpisLector(new Conexion($mysqli)))->kpi($idKpi);
            return $kpi !== null ? $kpi['integrantes'] : [];
        } catch (Throwable $error) {
            self::registrar('integrantesDeKpi', $error);
            return [];
        }
    }

    /** Tras asociar integrantes: avisa a los que no estaban antes. */
    public static function integrantesGuardados(mysqli $mysqli, int $idKpi, array $integrantesAnteriores, $idAutor): void
    {
        try {
            $kpi = (new KpisLector(new Conexion($mysqli)))->kpi($idKpi);
            if ($kpi === null) {
                return;
            }
            $nuevos = array_values(array_diff($kpi['integrantes'], array_map('intval', $integrantesAnteriores)));
            self::avisar($mysqli, CodigoNotificacion::KPIS_KPI_ASIGNACION, $kpi, $nuevos, $idAutor, []);
        } catch (Throwable $error) {
            self::registrar('integrantesGuardados', $error);
        }
    }

    /** La fila de Kpis_Colaborador antes de borrarla: después ya no se sabe a quién retiraron. */
    public static function filaDeIntegrante(mysqli $mysqli, int $idFila): ?array
    {
        try {
            return (new KpisLector(new Conexion($mysqli)))->integrante($idFila);
        } catch (Throwable $error) {
            self::registrar('filaDeIntegrante', $error);
            return null;
        }
    }

    /** Tras retirar a un integrante: avisa a los Líderes (menos al retirado). */
    public static function integranteRetirado(mysqli $mysqli, ?array $filaRetirada, $idAutor): void
    {
        try {
            if ($filaRetirada === null) {
                return;
            }
            $conexion = new Conexion($mysqli);
            $kpi = (new KpisLector($conexion))->kpi($filaRetirada['id_kpi']);
            if ($kpi === null) {
                return;
            }
            $idRetirado = $filaRetirada['id_colaborador'];
            $nombres = (new EmpleadosLector($conexion))->nombresDeEmpresa([$idRetirado], $kpi['id_empresa']);
            $lideres = array_values(array_diff($kpi['lideres'], [$idRetirado]));
            self::avisar($mysqli, CodigoNotificacion::KPIS_KPI_INTEGRANTE_RETIRADO, $kpi, $lideres, $idAutor, [
                'integrante_nombre' => isset($nombres[$idRetirado]) ? $nombres[$idRetirado] : 'Un integrante',
            ]);
        } catch (Throwable $error) {
            self::registrar('integranteRetirado', $error);
        }
    }

    /** Tras un comentario nuevo: avisa a los Líderes. El texto del comentario no viaja. */
    public static function comentarioAgregado(mysqli $mysqli, int $idKpi, $idAutor): void
    {
        try {
            $conexion = new Conexion($mysqli);
            $kpi = (new KpisLector($conexion))->kpi($idKpi);
            if ($kpi === null) {
                return;
            }
            self::avisar($mysqli, CodigoNotificacion::KPIS_KPI_COMENTARIO, $kpi, $kpi['lideres'], $idAutor, [
                'autor_nombre' => self::nombreAutor($conexion, $idAutor, $kpi['id_empresa']),
            ]);
        } catch (Throwable $error) {
            self::registrar('comentarioAgregado', $error);
        }
    }

    /** Tras subir un documento: avisa a los Líderes con el nombre del archivo. */
    public static function documentoAgregado(mysqli $mysqli, int $idKpi, string $nombreArchivo, $idAutor): void
    {
        try {
            $conexion = new Conexion($mysqli);
            $kpi = (new KpisLector($conexion))->kpi($idKpi);
            if ($kpi === null) {
                return;
            }
            self::avisar($mysqli, CodigoNotificacion::KPIS_KPI_DOCUMENTO, $kpi, $kpi['lideres'], $idAutor, [
                'archivo_nombre' => trim($nombreArchivo) !== '' ? trim($nombreArchivo) : 'sin archivo adjunto',
                'autor_nombre' => self::nombreAutor($conexion, $idAutor, $kpi['id_empresa']),
            ]);
        } catch (Throwable $error) {
            self::registrar('documentoAgregado', $error);
        }
    }

    /** Resultados del KPI antes de guardar, para saber qué periodos se cargaron. */
    public static function avancesDeKpi(mysqli $mysqli, int $idKpi): array
    {
        try {
            return (new KpisLector(new Conexion($mysqli)))->avances($idKpi);
        } catch (Throwable $error) {
            self::registrar('avancesDeKpi', $error);
            return [];
        }
    }

    /** Tras guardar resultados: avisa a los Líderes si se cargó algún periodo que estaba vacío. */
    public static function avancesGuardados(mysqli $mysqli, int $idKpi, array $avancesAnteriores, $idAutor): void
    {
        try {
            if (!$avancesAnteriores) {
                return;
            }
            $lector = new KpisLector(new Conexion($mysqli));
            $mesesCargados = [];
            foreach ($lector->avances($idKpi) as $mes => $valor) {
                $estabaVacio = !isset($avancesAnteriores[$mes]) || $avancesAnteriores[$mes] === '';
                if ($estabaVacio && $valor !== '') {
                    $mesesCargados[] = $mes;
                }
            }
            if (!$mesesCargados) {
                return;
            }
            $kpi = $lector->kpi($idKpi);
            if ($kpi === null) {
                return;
            }
            self::avisar($mysqli, CodigoNotificacion::KPIS_KPI_RESULTADO_CARGADO, $kpi, $kpi['lideres'], $idAutor, [
                'periodo_nombre' => self::nombrePeriodo($mesesCargados),
            ]);
        } catch (Throwable $error) {
            self::registrar('avancesGuardados', $error);
        }
    }

    /** [7, 8, 9] → "julio a septiembre"; [3, 7] → "marzo, julio". */
    public static function nombrePeriodo(array $meses): string
    {
        sort($meses);
        $tramos = [];
        $inicio = $anterior = array_shift($meses);
        foreach (array_merge($meses, [null]) as $mes) {
            if ($mes === $anterior + 1) {
                $anterior = $mes;
                continue;
            }
            $tramos[] = $inicio === $anterior ? self::MESES[$inicio] : self::MESES[$inicio] . ' a ' . self::MESES[$anterior];
            $inicio = $anterior = $mes;
        }
        return implode(', ', $tramos);
    }

    private static function avisar(mysqli $mysqli, string $codigo, array $kpi, array $destinatarios, $idAutor, array $datos): void
    {
        if (!$destinatarios) {
            return;
        }
        GenerarNotificacion::ejecutar($mysqli, $codigo, $kpi['id_empresa'], $destinatarios, [
            'datos_plantilla' => ['kpi_nombre' => $kpi['titulo']] + $datos,
            'url' => '?pg=kpis/gestionar_kpi&id=' . $kpi['id'],
            'tipo_registro' => 'KPIS_KPI',
            'id_registro' => $kpi['id'],
            'id_empleado_autor' => $idAutor ? (int) $idAutor : null,
        ]);
    }

    private static function nombreAutor(Conexion $conexion, $idAutor, int $idEmpresa): string
    {
        $id = (int) $idAutor;
        $nombres = $id > 0 ? (new EmpleadosLector($conexion))->nombresDeEmpresa([$id], $idEmpresa) : [];
        return isset($nombres[$id]) ? $nombres[$id] : 'Alguien';
    }

    private static function registrar(string $donde, Throwable $error): void
    {
        error_log('[notificaciones] AvisosKpis::' . $donde . ': ' . get_class($error) . ': ' . $error->getMessage());
    }
}
