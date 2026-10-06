<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use Notificaciones\Dominio\EstadoTipo;
use Notificaciones\Dominio\Excepciones\TipoNoEncontrado;
use Notificaciones\Dominio\TipoNotificacion;

final class TiposRepositorio
{
    private const EMPRESA_POR_DEFECTO = 0;

    /** @var Consulta */
    private $consulta;

    public function __construct(Consulta $consulta)
    {
        $this->consulta = $consulta;
    }

    /**
     * La fila de la empresa gana sobre la por defecto (id_empresa = 0), también
     * cuando está INACTIVO: así una empresa puede apagar un tipo solo para ella.
     *
     * NULL si el tipo está INACTIVO (apagarlo es una decisión de configuración,
     * no un error). Lanza TipoNoEncontrado si el código no tiene fila: eso sí es
     * un error de despliegue (falta el INSERT del tipo).
     */
    public function buscarActivo(string $codigo, int $idEmpresa): ?TipoNotificacion
    {
        $fila = $this->consulta->fila(
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
        if ($fila['estado'] !== EstadoTipo::ACTIVO) {
            return null;
        }
        return TipoNotificacion::desdeFila($fila, $codigo);
    }
}
