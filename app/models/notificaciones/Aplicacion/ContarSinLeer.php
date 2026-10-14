<?php
namespace Notificaciones\Aplicacion;

use mysqli;
use Notificaciones\Dominio\ModoOperacion;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\ConfiguracionRepositorio;
use Notificaciones\Infraestructura\BaseDatos\DestinatariosRepositorio;

/**
 * El número de la campana de la cabecera: notificaciones de la bandeja sin leer.
 * NULL si el módulo está APAGADO: entonces la campana no se muestra.
 */
final class ContarSinLeer
{
    public static function ejecutar(mysqli $mysqli, int $idEmpresa, int $idEmpleado): ?int
    {
        $conexion = new Conexion($mysqli);
        if ((new ConfiguracionRepositorio($conexion))->modoOperacion($idEmpresa) === ModoOperacion::APAGADO) {
            return null;
        }
        return (new DestinatariosRepositorio($conexion))->contarSinLeer($idEmpresa, $idEmpleado);
    }
}
