<?php
namespace Notificaciones\Aplicacion;

use mysqli;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\EmpleadosLector;
use Notificaciones\Infraestructura\BaseDatos\EnviosRepositorio;

/** Bitácora de correos de la empresa, con el nombre del empleado (no su dirección). */
final class ConsultarEnvios
{
    private const LIMITE = 500;

    public static function ejecutar(mysqli $mysqli, int $idEmpresa): array
    {
        $conexion = new Conexion($mysqli);
        $envios = (new EnviosRepositorio($conexion))->listarDeEmpresa($idEmpresa, self::LIMITE);
        $ids = array_values(array_unique(array_map('intval', array_column($envios, 'id_empleado'))));
        $nombres = (new EmpleadosLector($conexion))->nombresDeEmpresa($ids, $idEmpresa);

        return array_map(function (array $envio) use ($nombres) {
            $envio['nombre_empleado'] = isset($nombres[$envio['id_empleado']]) ? $nombres[$envio['id_empleado']] : '';
            return $envio;
        }, $envios);
    }
}
