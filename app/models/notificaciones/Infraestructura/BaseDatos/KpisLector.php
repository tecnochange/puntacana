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

    private static function ids(array $valores): array
    {
        return array_values(array_unique(array_filter(array_map('intval', $valores), function ($id) {
            return $id > 0;
        })));
    }
}
