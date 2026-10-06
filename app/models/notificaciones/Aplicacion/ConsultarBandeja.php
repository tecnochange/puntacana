<?php
namespace Notificaciones\Aplicacion;

use mysqli;
use Notificaciones\Dominio\ClaseNotificacion;
use Notificaciones\Dominio\EstadoNotificacion;
use Notificaciones\Infraestructura\BaseDatos\Consulta;
use Notificaciones\Infraestructura\BaseDatos\DestinatariosRepositorio;

/**
 * La bandeja de una persona, separada en:
 *  - pendientes: tareas abiertas (con o sin fecha límite)
 *  - avisos:     informativas abiertas
 *  - historial:  todo lo cerrado (completado, vencido o cancelado)
 */
final class ConsultarBandeja
{
    public static function ejecutar(mysqli $conexion, int $idEmpresa, int $idEmpleado): array
    {
        $filas = (new DestinatariosRepositorio(new Consulta($conexion)))->bandeja($idEmpresa, $idEmpleado);

        $bandeja = ['pendientes' => [], 'avisos' => [], 'historial' => [], 'no_leidas' => 0];
        foreach ($filas as $fila) {
            $estaAbierta = $fila['estado'] === EstadoNotificacion::ABIERTA->value;
            $esTarea = $fila['clase'] === ClaseNotificacion::TAREA->value;

            if (!$estaAbierta) {
                $bandeja['historial'][] = $fila;
            } elseif ($esTarea) {
                $bandeja['pendientes'][] = $fila;
            } else {
                $bandeja['avisos'][] = $fila;
            }
            if ($fila['fecha_lectura'] === null) {
                $bandeja['no_leidas']++;
            }
        }
        return $bandeja;
    }
}
