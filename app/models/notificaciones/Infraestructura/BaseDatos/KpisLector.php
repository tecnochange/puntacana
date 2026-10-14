<?php
namespace Notificaciones\Infraestructura\BaseDatos;

/**
 * Lee de la base de KPIs lo que necesitan los avisos de los flujos reales. Solo
 * lee. La base está en el mismo servidor y usuario que puntacana_admin, por eso
 * se consulta con su nombre. Kpis_Colaborador no tiene índices: cada consulta
 * va por un solo KPI o una sola fila.
 */
final class KpisLector
{
    private const BASE_KPIS = 'puntacana_kpis';
    /** Kpis_Colaborador.tipo: 1 Administrador, 2 Líder, 3 Colaborador. */
    private const TIPO_LIDER = 2;
    private const CANTIDAD_MESES = 12;

    /** @var Conexion */
    private $conexion;

    public function __construct(Conexion $conexion)
    {
        $this->conexion = $conexion;
    }

    /** ['id', 'id_empresa', 'titulo', 'integrantes' => int[], 'lideres' => int[]], o NULL si no existe. */
    public function kpi(int $idKpi): ?array
    {
        $fila = $this->conexion->consultarUno(
            'SELECT k.id, k.id_empresa, k.indicador FROM ' . self::BASE_KPIS . '.Kpis k WHERE k.id = ?',
            'i',
            [$idKpi]
        );
        if ($fila === null) {
            return null;
        }
        $integrantes = $this->conexion->consultar(
            'SELECT c.id_colaborador, c.tipo FROM ' . self::BASE_KPIS . '.Kpis_Colaborador c WHERE c.id_kpi = ?',
            'i',
            [$idKpi]
        );
        $lideres = array_filter($integrantes, function (array $integrante) {
            return (int) $integrante['tipo'] === self::TIPO_LIDER;
        });
        return [
            'id' => (int) $fila['id'],
            'id_empresa' => (int) $fila['id_empresa'],
            'titulo' => trim((string) $fila['indicador']),
            'integrantes' => self::ids(array_column($integrantes, 'id_colaborador')),
            'lideres' => self::ids(array_column($lideres, 'id_colaborador')),
        ];
    }

    /** ['id_kpi', 'id_colaborador'] de una fila de Kpis_Colaborador, o NULL. */
    public function integrante(int $idFila): ?array
    {
        $fila = $this->conexion->consultarUno(
            'SELECT c.id_kpi, c.id_colaborador FROM ' . self::BASE_KPIS . '.Kpis_Colaborador c WHERE c.id = ?',
            'i',
            [$idFila]
        );
        return $fila === null ? null : ['id_kpi' => (int) $fila['id_kpi'], 'id_colaborador' => (int) $fila['id_colaborador']];
    }

    /** Los 12 resultados del KPI, [1 => valor, ..., 12 => valor] (mes calendario); valores vacíos como ''. */
    public function avances(int $idKpi): array
    {
        $columnas = [];
        for ($mes = 1; $mes <= self::CANTIDAD_MESES; $mes++) {
            $columnas[] = 'f.avance_' . $mes;
        }
        $fila = $this->conexion->consultarUno(
            'SELECT ' . implode(', ', $columnas) . ' FROM ' . self::BASE_KPIS . '.Frecuencia_Kpis f WHERE f.id_kpi = ? LIMIT 1',
            'i',
            [$idKpi]
        );
        $avances = [];
        for ($mes = 1; $mes <= self::CANTIDAD_MESES; $mes++) {
            $avances[$mes] = $fila === null ? '' : trim((string) $fila['avance_' . $mes]);
        }
        return $avances;
    }

    /**
     * KPIs de un año fiscal con su frecuencia y sus 12 resultados:
     * [['id', 'titulo', 'frecuencia', 'avances' => [1 => valor, ...]], ...]
     */
    public function paraSeguimiento(int $idEmpresa, int $anioFiscal): array
    {
        $columnas = [];
        for ($mes = 1; $mes <= self::CANTIDAD_MESES; $mes++) {
            $columnas[] = 'f.avance_' . $mes;
        }
        $filas = $this->conexion->consultar(
            'SELECT k.id, k.indicador, f.tipo, ' . implode(', ', $columnas) . '
             FROM ' . self::BASE_KPIS . '.Kpis k
             JOIN ' . self::BASE_KPIS . '.Frecuencia_Kpis f ON f.id_kpi = k.id
             WHERE k.id_empresa = ? AND k.anio = ?',
            'ii',
            [$idEmpresa, $anioFiscal]
        );
        $kpis = [];
        foreach ($filas as $fila) {
            $avances = [];
            for ($mes = 1; $mes <= self::CANTIDAD_MESES; $mes++) {
                $avances[$mes] = trim((string) $fila['avance_' . $mes]);
            }
            $kpis[] = ['id' => (int) $fila['id'], 'titulo' => trim((string) $fila['indicador']), 'frecuencia' => (int) $fila['tipo'], 'avances' => $avances];
        }
        return $kpis;
    }

    /** [id_kpi => int[] ids de Líderes] de toda la empresa, en una sola lectura de Kpis_Colaborador. */
    public function lideresPorKpi(int $idEmpresa): array
    {
        $filas = $this->conexion->consultar(
            'SELECT c.id_kpi, c.id_colaborador FROM ' . self::BASE_KPIS . '.Kpis_Colaborador c WHERE c.id_empresa = ? AND c.tipo = ?',
            'ii',
            [$idEmpresa, self::TIPO_LIDER]
        );
        $lideres = [];
        foreach ($filas as $fila) {
            $lideres[(int) $fila['id_kpi']][] = (int) $fila['id_colaborador'];
        }
        return array_map(function (array $ids) {
            return self::ids($ids);
        }, $lideres);
    }

    /** Meses (1-12) que la administración habilitó para cargar en ese año fiscal (Habilitar_Mes = 'on'). */
    public function mesesHabilitados(int $idEmpresa, int $anioFiscal): array
    {
        $nombres = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $fila = $this->conexion->consultarUno(
            'SELECT ' . implode(', ', $nombres) . ' FROM ' . self::BASE_KPIS . '.Habilitar_Mes WHERE id_empresa = ? AND anio = ? ORDER BY id DESC LIMIT 1',
            'ii',
            [$idEmpresa, $anioFiscal]
        );
        $habilitados = [];
        foreach ($nombres as $mes => $columna) {
            if ($fila !== null && $fila[$columna] === 'on') {
                $habilitados[] = $mes;
            }
        }
        return $habilitados;
    }

    private static function ids(array $valores): array
    {
        return array_values(array_unique(array_filter(array_map('intval', $valores), function ($id) {
            return $id > 0;
        })));
    }
}
