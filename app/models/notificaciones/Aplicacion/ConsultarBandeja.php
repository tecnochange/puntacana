<?php
namespace Notificaciones\Aplicacion;

use mysqli;
use Notificaciones\Dominio\ClaseNotificacion;
use Notificaciones\Dominio\EstadoNotificacion;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\DestinatariosRepositorio;

/**
 * La bandeja de un empleado, separada en:
 *  - pendientes: TAREA abiertas (con o sin fecha de vencimiento)
 *  - avisos:     AVISO abiertos
 *  - historial:  todo lo cerrado (COMPLETADA, VENCIDA o CANCELADA)
 */
final class ConsultarBandeja
{
    public static function ejecutar(mysqli $mysqli, int $idEmpresa, int $idEmpleado): array
    {
        $filas = (new DestinatariosRepositorio(new Conexion($mysqli)))->listarBandeja($idEmpresa, $idEmpleado);

        $bandeja = ['pendientes' => [], 'avisos' => [], 'historial' => [], 'cantidad_no_leidas' => 0];
        foreach ($filas as $fila) {
            $estaAbierta = $fila['estado'] === EstadoNotificacion::ABIERTA;
            $esTarea = $fila['clase'] === ClaseNotificacion::TAREA;

            if (!$estaAbierta) {
                $bandeja['historial'][] = $fila;
            } elseif ($esTarea) {
                $bandeja['pendientes'][] = $fila;
            } else {
                $bandeja['avisos'][] = $fila;
            }
            if ($fila['fecha_lectura'] === null) {
                $bandeja['cantidad_no_leidas']++;
            }
        }
        return $bandeja;
    }
}
