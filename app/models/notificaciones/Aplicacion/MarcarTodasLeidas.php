<?php
namespace Notificaciones\Aplicacion;

use DateTimeImmutable;
use mysqli;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\DestinatariosRepositorio;

/** Marca leídas todas las notificaciones de un empleado. Devuelve cuántas cambiaron. */
final class MarcarTodasLeidas
{
    public static function ejecutar(mysqli $mysqli, int $idEmpresa, int $idEmpleado): int
    {
        $destinatarios = new DestinatariosRepositorio(new Conexion($mysqli));
        return $destinatarios->marcarTodasLeidas($idEmpresa, $idEmpleado, new DateTimeImmutable());
    }
}
