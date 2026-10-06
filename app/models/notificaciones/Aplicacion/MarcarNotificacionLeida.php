<?php
namespace Notificaciones\Aplicacion;

use DateTimeImmutable;
use mysqli;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\DestinatariosRepositorio;

/**
 * Marca leída una notificación y la devuelve, solo si el empleado es
 * destinatario. NULL si no lo es: nadie abre una notificación ajena cambiando
 * el id en la URL.
 */
final class MarcarNotificacionLeida
{
    public static function ejecutar(mysqli $mysqli, int $idEmpresa, int $idEmpleado, int $idNotificacion): ?array
    {
        $destinatarios = new DestinatariosRepositorio(new Conexion($mysqli));
        $notificacion = $destinatarios->notificacionDeEmpleado($idEmpresa, $idEmpleado, $idNotificacion);
        if ($notificacion === null) {
            return null;
        }
        $destinatarios->marcarLeida($idEmpresa, $idEmpleado, $idNotificacion, new DateTimeImmutable());
        return $notificacion;
    }
}
