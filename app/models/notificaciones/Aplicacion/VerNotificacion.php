<?php
namespace Notificaciones\Aplicacion;

use mysqli;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\DestinatariosRepositorio;
use Notificaciones\Infraestructura\BaseDatos\EmpleadosLector;
use Notificaciones\Infraestructura\BaseDatos\EnviosRepositorio;

/**
 * Todo lo que muestra la pantalla de detalle de una notificación. Abrirla la
 * marca leída; NULL si el empleado no es destinatario (igual que
 * MarcarNotificacionLeida).
 *
 * 'fecha_lectura' es la de antes de abrirla: NULL significa que se lee ahora.
 */
final class VerNotificacion
{
    public static function ejecutar(mysqli $mysqli, int $idEmpresa, int $idEmpleado, int $idNotificacion): ?array
    {
        $notificacion = MarcarNotificacionLeida::ejecutar($mysqli, $idEmpresa, $idEmpleado, $idNotificacion);
        if ($notificacion === null) {
            return null;
        }

        $conexion = new Conexion($mysqli);
        $destinatarios = new DestinatariosRepositorio($conexion);
        $idAutor = $notificacion['id_empleado_autor'] !== null ? (int) $notificacion['id_empleado_autor'] : null;
        $nombres = $idAutor ? (new EmpleadosLector($conexion))->nombresDeEmpresa([$idAutor], $idEmpresa) : [];

        return [
            'notificacion' => $notificacion,
            'nombre_autor' => $idAutor && isset($nombres[$idAutor]) ? $nombres[$idAutor] : null,
            'correos_enviados' => (new EnviosRepositorio($conexion))->fechasEnviadasADestinatario($idEmpresa, (int) $notificacion['id_destinatario']),
            'id_mas_reciente' => $destinatarios->vecinaEnBandeja($idEmpresa, $idEmpleado, $notificacion['created_at'], $idNotificacion, true),
            'id_mas_antigua' => $destinatarios->vecinaEnBandeja($idEmpresa, $idEmpleado, $notificacion['created_at'], $idNotificacion, false),
        ];
    }
}
