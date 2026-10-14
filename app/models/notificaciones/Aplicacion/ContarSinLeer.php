<?php
namespace Notificaciones\Aplicacion;

use mysqli;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\DestinatariosRepositorio;

/** El número de la campana de la cabecera: notificaciones de la bandeja sin leer. */
final class ContarSinLeer
{
    public static function ejecutar(mysqli $mysqli, int $idEmpresa, int $idEmpleado): int
    {
        return (new DestinatariosRepositorio(new Conexion($mysqli)))->contarSinLeer($idEmpresa, $idEmpleado);
    }
}
