<?php
namespace Notificaciones\Aplicacion;

use DateTimeImmutable;
use mysqli;
use Notificaciones\Dominio\CodigoNotificacion;
use Notificaciones\Dominio\EstadoNotificacion;
use Notificaciones\Dominio\PeriodosKpi;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\KpisLector;
use Notificaciones\Infraestructura\BaseDatos\NotificacionesRepositorio;
use Notificaciones\Infraestructura\BaseDatos\OkrsLector;
use Notificaciones\Infraestructura\BaseDatos\TiposRepositorio;
use Throwable;

/**
 * Crea las tareas que nacen de una fecha y no de una acción del usuario. Lo
 * corre el cron una vez al día (y el botón "Generar tareas ahora"). Se puede
 * correr las veces que sea: cada tarea lleva clave_evento y no se duplica.
 *
 *  - KR y plan de acción por vencer: desde dias_antes_de_vencer días antes de
 *    la fecha de entrega. Si la fecha cambió, la tarea anterior se cancela.
 *  - Seguimiento de KPI: desde el último día de cada periodo, si ese mes está
 *    habilitado y el periodo no tiene resultado; va a los Líderes y se cierra
 *    sola (COMPLETADA) cuando se carga el valor.
 *
 * Los recordatorios diarios los programa el despacho según
 * dias_entre_recordatorios de cada tipo.
 */
final class GenerarTareasProgramadas
{
    /** Los seguimientos de KPI arrancan con los periodos que cierran desde aquí (acordado: desde octubre de 2026). */
    const INICIO_SEGUIMIENTOS_KPI = '2026-10-01';

    /** @var mysqli */
    private $mysqli;
    /** @var int */
    private $idEmpresa;
    /** @var DateTimeImmutable */
    private $hoy;
    /** @var NotificacionesRepositorio */
    private $notificaciones;
    /** @var TiposRepositorio */
    private $tipos;
    /** @var array */
    private $totales = ['creadas' => 0, 'canceladas' => 0, 'completadas' => 0, 'errores' => 0];

    private function __construct(mysqli $mysqli, int $idEmpresa, DateTimeImmutable $ahora)
    {
        $this->mysqli = $mysqli;
        $this->idEmpresa = $idEmpresa;
        $this->hoy = $ahora->setTime(0, 0);
        $conexion = new Conexion($mysqli);
        $this->notificaciones = new NotificacionesRepositorio($conexion);
        $this->tipos = new TiposRepositorio($conexion);
    }

    public static function ejecutar(mysqli $mysqli, int $idEmpresa, ?DateTimeImmutable $ahora = null): array
    {
        $generador = new self($mysqli, $idEmpresa, $ahora ?: new DateTimeImmutable());
        $generador->vencimientosDeResultadosClave();
        $generador->vencimientosDePlanes();
        $generador->seguimientosDeKpis();
        return $generador->totales;
    }

    private function vencimientosDeResultadosClave(): void
    {
        $tipo = $this->tipos->buscarActivo(CodigoNotificacion::OKRS_KR_VENCIMIENTO, $this->idEmpresa);
        if ($tipo === null) {
            return;
        }
        $lector = new OkrsLector(new Conexion($this->mysqli));
        foreach ($lector->resultadosQueVencenEntre($this->idEmpresa, $this->fecha(0), $this->fecha((int) $tipo->diasAntesDeVencer)) as $kr) {
            $this->crearTareaDeVencimiento(CodigoNotificacion::OKRS_KR_VENCIMIENTO, $kr, [
                'datos_plantilla' => ['kr_titulo' => $kr['titulo'], 'okr_titulo' => $kr['okr_titulo']],
                'url' => '?pg=okrs/okr/resultados&id=' . $kr['id_okrs'] . '&id_resultado=' . $kr['id'],
                'tipo_registro' => 'OKRS_KR',
            ]);
        }
    }

    private function vencimientosDePlanes(): void
    {
        $tipo = $this->tipos->buscarActivo(CodigoNotificacion::OKRS_PLAN_ACCION_VENCIMIENTO, $this->idEmpresa);
        if ($tipo === null) {
            return;
        }
        $lector = new OkrsLector(new Conexion($this->mysqli));
        foreach ($lector->planesQueVencenEntre($this->idEmpresa, $this->fecha(0), $this->fecha((int) $tipo->diasAntesDeVencer)) as $plan) {
            $this->crearTareaDeVencimiento(CodigoNotificacion::OKRS_PLAN_ACCION_VENCIMIENTO, $plan, [
                'datos_plantilla' => ['plan_accion_titulo' => $plan['titulo']],
                'url' => '?pg=okrs/okr/planes_accion&id_plan_accion=' . $plan['id'],
                'tipo_registro' => 'OKRS_PLAN_ACCION',
            ]);
        }
    }

    /** La clave lleva la fecha: si la fecha de entrega cambia, nace otra tarea y la vieja se cancela. */
    private function crearTareaDeVencimiento(string $codigo, array $registro, array $opciones): void
    {
        $prefijo = $codigo . ':' . $registro['id'] . ':';
        $clave = $prefijo . $registro['fecha_entrega'];
        foreach ($this->notificaciones->clavesAbiertasConPrefijo($this->idEmpresa, $prefijo) as $claveAnterior) {
            if ($claveAnterior !== $clave && CerrarNotificacion::ejecutar($this->mysqli, $this->idEmpresa, $claveAnterior, EstadoNotificacion::CANCELADA)) {
                $this->totales['canceladas']++;
            }
        }
        $this->crear($codigo, $registro['responsables'], $opciones + [
            'id_registro' => $registro['id'],
            'fecha_vencimiento' => $registro['fecha_entrega'],
            'clave_evento' => $clave,
        ]);
    }

    private function seguimientosDeKpis(): void
    {
        $this->cerrarSeguimientosCargados();
        if ($this->tipos->buscarActivo(CodigoNotificacion::KPIS_KPI_SEGUIMIENTO_PERIODO, $this->idEmpresa) === null) {
            return;
        }
        $lector = new KpisLector(new Conexion($this->mysqli));
        $inicio = new DateTimeImmutable(self::INICIO_SEGUIMIENTOS_KPI);
        $lideres = null;
        $anioActual = PeriodosKpi::anioFiscal($this->hoy);
        // El año anterior también: sus periodos de junio cierran justo cuando empieza el nuevo.
        foreach ([$anioActual - 1, $anioActual] as $anio) {
            $habilitados = $lector->mesesHabilitados($this->idEmpresa, $anio);
            if (!$habilitados) {
                continue;
            }
            foreach ($lector->paraSeguimiento($this->idEmpresa, $anio) as $kpi) {
                foreach (PeriodosKpi::deFrecuencia($kpi['frecuencia'], $anio) as $periodo) {
                    $mes = $periodo['mes_cierre'];
                    $yaCerro = $periodo['fecha_cierre'] <= $this->hoy && $periodo['fecha_cierre'] >= $inicio;
                    if (!$yaCerro || !in_array($mes, $habilitados, true) || $kpi['avances'][$mes] !== '') {
                        continue;
                    }
                    if ($lideres === null) {
                        $lideres = $lector->lideresPorKpi($this->idEmpresa);
                    }
                    $this->crear(CodigoNotificacion::KPIS_KPI_SEGUIMIENTO_PERIODO, isset($lideres[$kpi['id']]) ? $lideres[$kpi['id']] : [], [
                        'datos_plantilla' => ['kpi_nombre' => $kpi['titulo'], 'periodo_nombre' => $periodo['nombre']],
                        'url' => '?pg=kpis/gestionar_kpi&id=' . $kpi['id'],
                        'tipo_registro' => 'KPIS_KPI',
                        'id_registro' => $kpi['id'],
                        'clave_evento' => self::claveSeguimiento($kpi['id'], $anio, $mes),
                    ]);
                }
            }
        }
    }

    /** "Hasta que lo realice": el seguimiento abierto se completa solo cuando su periodo ya tiene valor. */
    private function cerrarSeguimientosCargados(): void
    {
        $lector = new KpisLector(new Conexion($this->mysqli));
        $prefijo = CodigoNotificacion::KPIS_KPI_SEGUIMIENTO_PERIODO . ':';
        foreach ($this->notificaciones->clavesAbiertasConPrefijo($this->idEmpresa, $prefijo) as $clave) {
            $partes = explode(':', $clave);
            if (count($partes) !== 4) {
                continue;
            }
            $avances = $lector->avances((int) $partes[1]);
            $mes = (int) $partes[3];
            $yaCargado = isset($avances[$mes]) && $avances[$mes] !== '';
            if ($yaCargado && CerrarNotificacion::ejecutar($this->mysqli, $this->idEmpresa, $clave, EstadoNotificacion::COMPLETADA)) {
                $this->totales['completadas']++;
            }
        }
    }

    public static function claveSeguimiento(int $idKpi, int $anioFiscal, int $mesCierre): string
    {
        return CodigoNotificacion::KPIS_KPI_SEGUIMIENTO_PERIODO . ':' . $idKpi . ':' . $anioFiscal . ':' . $mesCierre;
    }

    /** Una tarea que falla no detiene las demás. */
    private function crear(string $codigo, array $destinatarios, array $opciones): void
    {
        if (!$destinatarios) {
            return;
        }
        try {
            $existia = $this->notificaciones->idPorClaveEvento($this->idEmpresa, $opciones['clave_evento']) !== null;
            $id = GenerarNotificacion::ejecutar($this->mysqli, $codigo, $this->idEmpresa, $destinatarios, $opciones);
            if ($id !== null && !$existia) {
                $this->totales['creadas']++;
            }
        } catch (Throwable $error) {
            $this->totales['errores']++;
            error_log('[notificaciones] GenerarTareasProgramadas ' . $codigo . ' ' . $opciones['clave_evento'] . ': ' . $error->getMessage());
        }
    }

    private function fecha(int $diasDesdeHoy): string
    {
        return $this->hoy->modify('+' . $diasDesdeHoy . ' days')->format('Y-m-d');
    }
}
