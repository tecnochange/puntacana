<?php
namespace Notificaciones\Aplicacion;

use DateTimeImmutable;
use mysqli;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\DestinatariosRepositorio;
use Notificaciones\Infraestructura\BaseDatos\NotificacionesRepositorio;

/**
 * "Marcar como completada" desde el detalle: un destinatario cierra una TAREA
 * ABIERTA. La tarea es una sola para todos sus destinatarios, así que queda
 * completada para todos, con quién la cerró. Lo normal es que se cierre sola
 * cuando el sistema detecta la acción; esto cubre lo que no detecta.
 *
 * Devuelve false si no es destinatario, no es una tarea o ya no está abierta.
 */
final class CompletarTarea
{
    public static function ejecutar(mysqli $mysqli, int $idEmpresa, int $idEmpleado, int $idNotificacion): bool
    {
        $conexion = new Conexion($mysqli);
        $esDestinatario = (new DestinatariosRepositorio($conexion))->notificacionDeEmpleado($idEmpresa, $idEmpleado, $idNotificacion) !== null;
        if (!$esDestinatario) {
            return false;
        }
        return (new NotificacionesRepositorio($conexion))->completarTarea($idEmpresa, $idNotificacion, $idEmpleado, new DateTimeImmutable());
    }
}
