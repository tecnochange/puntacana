<?php
namespace Notificaciones\Aplicacion;

use mysqli;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\EmpleadosLector;

/**
 * Si el empleado puede usar la administración de notificaciones (rol
 * Administrador y activo). Se consulta en cada pantalla y endpoint de
 * administración, sin confiar solo en Rutas.
 */
final class VerificarAdministrador
{
    public static function ejecutar(mysqli $mysqli, int $idEmpresa, int $idEmpleado): bool
    {
        return (new EmpleadosLector(new Conexion($mysqli)))->esAdministradorActivo($idEmpleado, $idEmpresa);
    }
}
