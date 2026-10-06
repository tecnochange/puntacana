<?php
namespace Notificaciones\Infraestructura\BaseDatos;

/**
 * Única lectura de Empleados del módulo. Solo trae id, nombre, estado y, al
 * momento de enviar, el correo. Ninguna otra columna de Empleados se lee.
 */
final class EmpleadosLector
{
    private const ESTADO_ACTIVO = 1;

    /** @var Conexion */
    private $conexion;

    public function __construct(Conexion $conexion)
    {
        $this->conexion = $conexion;
    }

    /** De los ids dados, los activos de esa empresa: [id => nombre]. */
    public function activosDeEmpresa(array $idsEmpleados, int $idEmpresa): array
    {
        if (!$idsEmpleados) {
            return [];
        }
        $marcadores = implode(',', array_fill(0, count($idsEmpleados), '?'));
        $filas = $this->conexion->consultar(
            "SELECT id, nombre FROM Empleados
             WHERE id_empresa = ? AND estado = ? AND id IN ($marcadores)",
            'ii' . str_repeat('i', count($idsEmpleados)),
            array_merge([$idEmpresa, self::ESTADO_ACTIVO], $idsEmpleados)
        );
        return array_column($filas, 'nombre', 'id');
    }

    /** Nombre, correo y si está activo; NULL si no existe en esa empresa. */
    public function paraEnvio(int $idEmpleado, int $idEmpresa): ?array
    {
        $fila = $this->conexion->consultarUno(
            'SELECT nombre, correo, estado FROM Empleados WHERE id = ? AND id_empresa = ?',
            'ii',
            [$idEmpleado, $idEmpresa]
        );
        if ($fila === null) {
            return null;
        }
        return [
            'nombre' => $fila['nombre'],
            'correo' => $fila['correo'],
            'activo' => (int) $fila['estado'] === self::ESTADO_ACTIVO,
        ];
    }
}
