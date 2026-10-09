<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use DateTimeImmutable;

/**
 * Busca un registro real de OKRs o KPIs para el envío de prueba de un tipo, así
 * la notificación de prueba muestra un título real y su enlace lleva a la
 * pantalla donde se revisa o se actúa. Solo lee.
 *
 * Prefiere un registro donde participa quien prueba y, para los tipos que
 * avisan antes de vencer, uno que venza dentro de ese plazo (así el correo sale
 * de inmediato y la fecha mostrada es la real).
 *
 * Las bases de OKRs y KPIs están en el mismo servidor y usuario que
 * puntacana_admin, por eso se consultan con el nombre de la base.
 */
final class RegistrosParaPrueba
{
    private const BASE_OKRS = 'puntacana_okrs';
    private const BASE_KPIS = 'puntacana_kpis';
    private const MESES = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

    /** @var Conexion */
    private $conexion;

    public function __construct(Conexion $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * ['datos' => [...], 'url' => '?pg=...', 'id_registro' => int, 'fecha_vencimiento' => ?'Y-m-d'],
     * o NULL si el código no corresponde a un registro conocido o no hay ninguno.
     * 'datos' trae todos los que conoce; quien llama se queda con los permitidos.
     */
    public function paraCodigo(string $codigo, int $idEmpresa, int $idEmpleado, string $nombreEmpleado, ?int $diasAviso): ?array
    {
        $hoy = new DateTimeImmutable('today');
        $limiteAviso = $hoy->modify('+' . (int) $diasAviso . ' days')->format('Y-m-d');
        $hoyTexto = $hoy->format('Y-m-d');

        if (strpos($codigo, 'OKRS_OBJETIVO_') === 0) {
            return $this->objetivo($idEmpresa, $idEmpleado, $hoyTexto, $limiteAviso);
        }
        if (strpos($codigo, 'OKRS_KR_') === 0) {
            return $this->resultadoClave($idEmpresa, $idEmpleado, $hoyTexto, $limiteAviso);
        }
        if (strpos($codigo, 'OKRS_INICIATIVA_') === 0) {
            return $this->iniciativa($idEmpresa, $idEmpleado, $hoyTexto, $limiteAviso);
        }
        if (strpos($codigo, 'OKRS_PLAN_ACCION_') === 0) {
            return $this->planAccion($idEmpresa, $idEmpleado, $hoyTexto, $limiteAviso);
        }
        if (strpos($codigo, 'KPIS_KPI_') === 0) {
            return $this->kpi($codigo, $idEmpresa, $idEmpleado, $nombreEmpleado, $hoy);
        }
        return null;
    }

    private function objetivo(int $idEmpresa, int $idEmpleado, string $hoy, string $limiteAviso): ?array
    {
        $fila = $this->conexion->consultarUno(
            'SELECT o.id, o.objetivo_okr, o.fecha_termina
             FROM ' . self::BASE_OKRS . '.Okrs o
             WHERE o.id_empresa = ?
             ORDER BY (o.id_empleado = ?) DESC, (o.fecha_termina BETWEEN ? AND ?) DESC, (o.fecha_termina >= ?) DESC, o.id DESC
             LIMIT 1',
            'iisss',
            [$idEmpresa, $idEmpleado, $hoy, $limiteAviso, $hoy]
        );
        if ($fila === null) {
            return null;
        }
        return [
            'datos' => ['okr_titulo' => $fila['objetivo_okr']],
            'url' => '?pg=okrs/okr/detalle&id=' . (int) $fila['id'],
            'id_registro' => (int) $fila['id'],
            'fecha_vencimiento' => $this->fechaFutura($fila['fecha_termina'], $hoy),
        ];
    }

    private function resultadoClave(int $idEmpresa, int $idEmpleado, string $hoy, string $limiteAviso): ?array
    {
        $fila = $this->conexion->consultarUno(
            'SELECT r.id, r.descripcion, r.periodo, r.fecha_entrega, o.id AS id_okr, o.objetivo_okr
             FROM ' . self::BASE_OKRS . '.Okrs_Resultados r
             JOIN ' . self::BASE_OKRS . '.Okrs o ON o.id = r.id_okrs
             WHERE r.id_empresa = ?
             ORDER BY (r.id_empleado = ? OR FIND_IN_SET(?, REPLACE(r.responsables, CHAR(32), SPACE(0))) > 0) DESC,
                      (r.fecha_entrega BETWEEN ? AND ?) DESC, (r.fecha_entrega >= ?) DESC, r.id DESC
             LIMIT 1',
            'iissss',
            [$idEmpresa, $idEmpleado, (string) $idEmpleado, $hoy, $limiteAviso, $hoy]
        );
        if ($fila === null) {
            return null;
        }
        return [
            // La plataforma no tiene etapas de KR: la etapa es el periodo del KR (Q1…Q4, Anual).
            'datos' => ['kr_titulo' => $fila['descripcion'], 'okr_titulo' => $fila['objetivo_okr'], 'etapa_nombre' => $fila['periodo']],
            'url' => '?pg=okrs/okr/resultados&id=' . (int) $fila['id_okr'] . '&id_resultado=' . (int) $fila['id'],
            'id_registro' => (int) $fila['id'],
            'fecha_vencimiento' => $this->fechaFutura($fila['fecha_entrega'], $hoy),
        ];
    }

    private function iniciativa(int $idEmpresa, int $idEmpleado, string $hoy, string $limiteAviso): ?array
    {
        $fila = $this->conexion->consultarUno(
            'SELECT i.id, i.descripcion, i.fecha_entrega, i.id_okrs, i.id_resultado, r.descripcion AS kr_titulo
             FROM ' . self::BASE_OKRS . '.Okrs_Iniciativas i
             JOIN ' . self::BASE_OKRS . '.Okrs o ON o.id = i.id_okrs
             JOIN ' . self::BASE_OKRS . '.Okrs_Resultados r ON r.id = i.id_resultado
             WHERE o.id_empresa = ?
             ORDER BY (i.id_empleado = ? OR FIND_IN_SET(?, REPLACE(i.responsables, CHAR(32), SPACE(0))) > 0) DESC,
                      (i.fecha_entrega BETWEEN ? AND ?) DESC, (i.fecha_entrega >= ?) DESC, i.id DESC
             LIMIT 1',
            'iissss',
            [$idEmpresa, $idEmpleado, (string) $idEmpleado, $hoy, $limiteAviso, $hoy]
        );
        if ($fila === null) {
            return null;
        }
        return [
            'datos' => ['iniciativa_titulo' => $fila['descripcion'], 'kr_titulo' => $fila['kr_titulo']],
            'url' => '?pg=okrs/gestionar_iniciativa&id_okrs=' . (int) $fila['id_okrs'] . '&id_resultado=' . (int) $fila['id_resultado'] . '&id_iniciativa=' . (int) $fila['id'],
            'id_registro' => (int) $fila['id'],
            'fecha_vencimiento' => $this->fechaFutura($fila['fecha_entrega'], $hoy),
        ];
    }

    private function planAccion(int $idEmpresa, int $idEmpleado, string $hoy, string $limiteAviso): ?array
    {
        $fila = $this->conexion->consultarUno(
            'SELECT a.id, a.descripcion, a.fecha_entrega, a.ciclo
             FROM ' . self::BASE_OKRS . '.Okrs_Actividades a
             WHERE a.id_empresa = ?
             ORDER BY (a.id_empleado = ? OR FIND_IN_SET(?, REPLACE(a.id_asignado, CHAR(32), SPACE(0))) > 0) DESC,
                      (a.fecha_entrega BETWEEN ? AND ?) DESC, (a.fecha_entrega >= ?) DESC, a.id DESC
             LIMIT 1',
            'iissss',
            [$idEmpresa, $idEmpleado, (string) $idEmpleado, $hoy, $limiteAviso, $hoy]
        );
        if ($fila === null) {
            return null;
        }
        $ciclo = trim((string) $fila['ciclo']);
        return [
            // La plataforma no tiene etapas de plan de acción: se usa su ciclo (texto libre) si lo tiene.
            'datos' => ['plan_accion_titulo' => $fila['descripcion'], 'etapa_nombre' => $ciclo !== '' ? $ciclo : 'Seguimiento'],
            'url' => '?pg=okrs/okr/planes_accion&id_plan_accion=' . (int) $fila['id'],
            'id_registro' => (int) $fila['id'],
            'fecha_vencimiento' => $this->fechaFutura($fila['fecha_entrega'], $hoy),
        ];
    }

    private function kpi(string $codigo, int $idEmpresa, int $idEmpleado, string $nombreEmpleado, DateTimeImmutable $hoy): ?array
    {
        $anio = (int) $hoy->format('Y');
        $esComentario = $codigo === 'KPIS_KPI_COMENTARIO';
        $esDocumento = $codigo === 'KPIS_KPI_DOCUMENTO';
        // Para comentarios y documentos, un KPI que de verdad los tenga.
        $tablaRelacionada = $esComentario ? 'Comentarios_Kpis' : ($esDocumento ? 'Documentos_Kpis' : null);
        $ordenRelacionada = $tablaRelacionada === null
            ? ''
            : 'EXISTS (SELECT 1 FROM ' . self::BASE_KPIS . '.' . $tablaRelacionada . ' x WHERE x.id_kpi = k.id) DESC, ';

        $fila = $this->conexion->consultarUno(
            'SELECT k.id, k.indicador
             FROM ' . self::BASE_KPIS . '.Kpis k
             WHERE k.id_empresa = ? AND k.anio = ?
             ORDER BY ' . $ordenRelacionada . 'EXISTS (SELECT 1 FROM ' . self::BASE_KPIS . '.Kpis_Colaborador c WHERE c.id_kpi = k.id AND c.id_colaborador = ?) DESC, k.id DESC
             LIMIT 1',
            'iii',
            [$idEmpresa, $anio, $idEmpleado]
        );
        if ($fila === null) {
            return null;
        }
        $idKpi = (int) $fila['id'];
        $datos = [
            'kpi_nombre' => $fila['indicador'],
            'periodo_nombre' => self::MESES[(int) $hoy->format('n') - 1] . ' ' . $anio,
            'integrante_nombre' => $nombreEmpleado,
            'autor_nombre' => $nombreEmpleado,
        ];
        if ($tablaRelacionada !== null) {
            $ultimo = $this->conexion->consultarUno(
                'SELECT x.id_empleado, ' . ($esDocumento ? 'x.archivo' : 'NULL AS archivo') . '
                 FROM ' . self::BASE_KPIS . '.' . $tablaRelacionada . ' x
                 WHERE x.id_kpi = ?
                 ORDER BY x.created_at DESC
                 LIMIT 1',
                'i',
                [$idKpi]
            );
            if ($ultimo !== null) {
                $autor = $this->conexion->consultarUno('SELECT nombre FROM Empleados WHERE id = ? AND id_empresa = ?', 'ii', [(int) $ultimo['id_empleado'], $idEmpresa]);
                if ($autor !== null) {
                    $datos['autor_nombre'] = $autor['nombre'];
                }
                if ($esDocumento && $ultimo['archivo'] !== null) {
                    $datos['archivo_nombre'] = basename((string) $ultimo['archivo']);
                }
            }
        }
        return [
            'datos' => $datos,
            'url' => '?pg=kpis/gestionar_kpi&id=' . $idKpi,
            'id_registro' => $idKpi,
            'fecha_vencimiento' => null,
        ];
    }

    /** La fecha si es hoy o futura; NULL si ya pasó o no es válida. */
    private function fechaFutura(?string $fecha, string $hoy): ?string
    {
        $esFecha = $fecha !== null && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha) && $fecha !== '0000-00-00';
        return $esFecha && $fecha >= $hoy ? $fecha : null;
    }
}
