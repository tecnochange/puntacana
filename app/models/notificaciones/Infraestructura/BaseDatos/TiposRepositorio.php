<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use Notificaciones\Dominio\Excepciones\TipoNoEncontrado;
use Notificaciones\Dominio\TipoNotificacion;

final class TiposRepositorio
{
    private const ESTADO_ACTIVO = 1;
    private const EMPRESA_POR_DEFECTO = 0;

    /** @var Consulta */
    private $consulta;

    public function __construct(Consulta $consulta)
    {
        $this->consulta = $consulta;
    }

    /**
     * La fila de la empresa gana sobre la por defecto (id_empresa = 0), también
     * cuando está inactiva: así una empresa puede apagar un tipo solo para ella.
     */
    public function buscarActivo(string $codigo, int $idEmpresa): TipoNotificacion
    {
        $fila = $this->consulta->fila(
            'SELECT * FROM Notificaciones_Tipos
             WHERE codigo = ? AND id_empresa IN (?, ?)
             ORDER BY id_empresa DESC
             LIMIT 1',
            'sii',
            [$codigo, self::EMPRESA_POR_DEFECTO, $idEmpresa]
        );

        $estaActivo = $fila !== null && (int) $fila['estado'] === self::ESTADO_ACTIVO;
        if (!$estaActivo) {
            throw new TipoNoEncontrado("No hay un tipo activo '$codigo' para la empresa $idEmpresa.");
        }
        return TipoNotificacion::desdeFila($fila, $codigo);
    }
}
