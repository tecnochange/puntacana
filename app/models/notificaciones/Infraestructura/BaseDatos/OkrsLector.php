<?php
namespace Notificaciones\Infraestructura\BaseDatos;

/**
 * Lee de la base de OKRs lo que necesitan los avisos de los flujos reales. Solo
 * lee. La base está en el mismo servidor y usuario que puntacana_admin, por eso
 * se consulta con su nombre.
 */
final class OkrsLector
{
    private const BASE_OKRS = 'puntacana_okrs';

    /** @var Conexion */
    private $conexion;

    public function __construct(Conexion $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * ['id', 'id_empresa', 'id_okrs', 'titulo', 'okr_titulo', 'fecha_entrega', 'responsables' => int[]],
     * o NULL si no existe.
     */
    public function resultadoClave(int $idResultado): ?array
    {
        $fila = $this->conexion->consultarUno(
            'SELECT r.id, r.id_empresa, r.id_okrs, r.descripcion, r.responsables, r.fecha_entrega, o.objetivo_okr
             FROM ' . self::BASE_OKRS . '.Okrs_Resultados r
             LEFT JOIN ' . self::BASE_OKRS . '.Okrs o ON o.id = r.id_okrs
             WHERE r.id = ?',
            'i',
            [$idResultado]
        );
        if ($fila === null) {
            return null;
        }
        return [
            'id' => (int) $fila['id'],
            'id_empresa' => (int) $fila['id_empresa'],
            'id_okrs' => (int) $fila['id_okrs'],
            'titulo' => trim((string) $fila['descripcion']),
            'okr_titulo' => trim((string) $fila['objetivo_okr']),
            'fecha_entrega' => $fila['fecha_entrega'],
            'responsables' => self::idsDeLista($fila['responsables']),
        ];
    }

    /**
     * ['id', 'id_empresa', 'id_okrs', 'id_resultado', 'titulo', 'kr_titulo', 'fecha_entrega', 'responsables' => int[]],
     * o NULL si no existe. La iniciativa no guarda empresa: se toma la de su OKR.
     */
    public function iniciativa(int $idIniciativa): ?array
    {
        $fila = $this->conexion->consultarUno(
            'SELECT i.id, o.id_empresa, i.id_okrs, i.id_resultado, i.descripcion, i.responsables, i.fecha_entrega, r.descripcion AS kr_titulo
             FROM ' . self::BASE_OKRS . '.Okrs_Iniciativas i
             JOIN ' . self::BASE_OKRS . '.Okrs o ON o.id = i.id_okrs
             LEFT JOIN ' . self::BASE_OKRS . '.Okrs_Resultados r ON r.id = i.id_resultado
             WHERE i.id = ?',
            'i',
            [$idIniciativa]
        );
        if ($fila === null) {
            return null;
        }
        return [
            'id' => (int) $fila['id'],
            'id_empresa' => (int) $fila['id_empresa'],
            'id_okrs' => (int) $fila['id_okrs'],
            'id_resultado' => (int) $fila['id_resultado'],
            'titulo' => trim((string) $fila['descripcion']),
            'kr_titulo' => trim((string) $fila['kr_titulo']),
            'fecha_entrega' => $fila['fecha_entrega'],
            'responsables' => self::idsDeLista($fila['responsables']),
        ];
    }

    /** Las listas de responsables se guardan como texto "12,45, 78". */
    private static function idsDeLista(?string $lista): array
    {
        $ids = array_map('intval', explode(',', (string) $lista));
        return array_values(array_unique(array_filter($ids, function ($id) {
            return $id > 0;
        })));
    }
}
