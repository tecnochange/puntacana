<?php
namespace Notificaciones\Infraestructura\BaseDatos;

/** Lee Notificaciones_Configuracion: el valor de la empresa gana sobre el por defecto (id_empresa = 0). */
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
}
