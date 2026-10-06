<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use Notificaciones\Dominio\EstadoTipo;
use Notificaciones\Dominio\Excepciones\TipoNoEncontrado;
use Notificaciones\Dominio\TipoNotificacion;

/** Tabla Notificaciones_Tipos. */
final class TiposRepositorio
{
    private const EMPRESA_POR_DEFECTO = 0;

    /** @var Conexion */
    private $conexion;

    public function __construct(Conexion $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * El tipo que aplica a la empresa: su propia fila gana sobre la por defecto
     * (id_empresa = 0), también cuando está INACTIVO, así una empresa puede
     * apagar un tipo solo para ella.
     *
     * NULL si está INACTIVO (apagarlo es configuración, no un error). Lanza
     * TipoNoEncontrado si el código no tiene fila: eso sí es un error de
     * despliegue (falta el INSERT del tipo).
     */
    public function buscarActivo(string $codigo, int $idEmpresa): ?TipoNotificacion
    {
        $fila = $this->conexion->consultarUno(
            'SELECT * FROM Notificaciones_Tipos
             WHERE codigo = ? AND id_empresa IN (?, ?)
             ORDER BY id_empresa DESC
             LIMIT 1',
            'sii',
            [$codigo, self::EMPRESA_POR_DEFECTO, $idEmpresa]
        );
        if ($fila === null) {
            throw new TipoNoEncontrado("El código '$codigo' no tiene fila en Notificaciones_Tipos (empresa $idEmpresa ni por defecto).");
        }
        return $fila['estado'] === EstadoTipo::ACTIVO ? TipoNotificacion::desdeFila($fila) : null;
    }

    /** El tipo con el que se generó una notificación. NULL si ya no existe o está INACTIVO. */
    public function buscarActivoPorId(int $idTipo): ?TipoNotificacion
    {
        $fila = $this->conexion->consultarUno('SELECT * FROM Notificaciones_Tipos WHERE id = ?', 'i', [$idTipo]);
        $estaActivo = $fila !== null && $fila['estado'] === EstadoTipo::ACTIVO;
        return $estaActivo ? TipoNotificacion::desdeFila($fila) : null;
    }
}
