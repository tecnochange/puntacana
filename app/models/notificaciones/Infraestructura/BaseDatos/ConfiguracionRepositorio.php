<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use DateTimeImmutable;

/**
 * Tabla Notificaciones_Configuracion: el valor de la empresa gana sobre el por
 * defecto (id_empresa = 0). Desde la aplicación solo se escriben valores de una
 * empresa; los por defecto se cambian por SQL.
 */
final class ConfiguracionRepositorio
{
    private const EMPRESA_POR_DEFECTO = 0;

    /** @var Conexion */
    private $conexion;

    public function __construct(Conexion $conexion)
    {
        $this->conexion = $conexion;
    }

    public function valor(int $idEmpresa, string $clave, string $porDefecto = ''): string
    {
        $fila = $this->conexion->consultarUno(
            'SELECT valor FROM Notificaciones_Configuracion
             WHERE clave = ? AND id_empresa IN (?, ?)
             ORDER BY id_empresa DESC
             LIMIT 1',
            'sii',
            [$clave, self::EMPRESA_POR_DEFECTO, $idEmpresa]
        );
        return $fila['valor'] ?? $porDefecto;
    }

    /** Todas las claves que aplican a la empresa: [clave => ['valor' => ..., 'es_de_la_empresa' => bool]]. */
    public function valoresDeEmpresa(int $idEmpresa): array
    {
        $filas = $this->conexion->consultar(
            'SELECT clave, valor, id_empresa FROM Notificaciones_Configuracion
             WHERE id_empresa IN (?, ?)
             ORDER BY id_empresa',
            'ii',
            [self::EMPRESA_POR_DEFECTO, $idEmpresa]
        );
        $valores = [];
        foreach ($filas as $fila) {
            // Ordenadas por id_empresa: la fila de la empresa pisa a la por defecto.
            $valores[$fila['clave']] = [
                'valor' => (string) $fila['valor'],
                'es_de_la_empresa' => (int) $fila['id_empresa'] !== self::EMPRESA_POR_DEFECTO,
            ];
        }
        return $valores;
    }

    public function guardarDeEmpresa(int $idEmpresa, string $clave, string $valor, DateTimeImmutable $ahora): void
    {
        $fecha = Conexion::fecha($ahora);
        $this->conexion->modificar(
            'INSERT INTO Notificaciones_Configuracion (id_empresa, clave, valor, created_at)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE valor = VALUES(valor), updated_at = ?',
            'issss',
            [$idEmpresa, $clave, $valor, $fecha, $fecha]
        );
    }
}
