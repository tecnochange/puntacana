<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use DateTimeImmutable;

/**
 * Tabla Notificaciones_Configuracion. La administración trabaja con los valores
 * generales (id_empresa = 0); la lectura en tiempo de ejecución sigue aceptando
 * un valor propio de empresa si algún día existe.
 */
final class ConfiguracionRepositorio
{
    private const EMPRESA_GENERAL = 0;

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
            [$clave, self::EMPRESA_GENERAL, $idEmpresa]
        );
        return $fila['valor'] ?? $porDefecto;
    }

    /** Valores generales: [clave => valor]. */
    public function valoresGenerales(): array
    {
        $filas = $this->conexion->consultar(
            'SELECT clave, valor FROM Notificaciones_Configuracion WHERE id_empresa = ?',
            'i',
            [self::EMPRESA_GENERAL]
        );
        return array_map('strval', array_column($filas, 'valor', 'clave'));
    }

    public function guardarGeneral(string $clave, string $valor, DateTimeImmutable $ahora): void
    {
        $fecha = Conexion::fecha($ahora);
        $this->conexion->modificar(
            'INSERT INTO Notificaciones_Configuracion (id_empresa, clave, valor, created_at)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE valor = VALUES(valor), updated_at = ?',
            'issss',
            [self::EMPRESA_GENERAL, $clave, $valor, $fecha, $fecha]
        );
    }
}
