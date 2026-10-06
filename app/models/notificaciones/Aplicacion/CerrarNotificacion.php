<?php
namespace Notificaciones\Aplicacion;

use DateTimeImmutable;
use mysqli;
use Notificaciones\Dominio\EstadoNotificacion;
use Notificaciones\Dominio\Excepciones\EstadoNoPermitido;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\NotificacionesRepositorio;

/**
 * Cierra una notificación ABIERTA por su clave_evento (p. ej. cuando el registro
 * se completó o se borró). $estado: EstadoNotificacion::COMPLETADA o ::CANCELADA.
 * Sus correos pendientes se cancelan en la siguiente corrida del cron.
 * Devuelve false si no existía o ya estaba cerrada.
 */
final class CerrarNotificacion
{
    public static function ejecutar(mysqli $mysqli, int $idEmpresa, string $claveEvento, string $estado): bool
    {
        if (!EstadoNotificacion::esCierreManual($estado)) {
            throw new EstadoNoPermitido("Una notificación solo se cierra como COMPLETADA o CANCELADA, no como '$estado'.");
        }
        $notificaciones = new NotificacionesRepositorio(new Conexion($mysqli));
        return $notificaciones->cerrarPorClaveEvento($idEmpresa, $claveEvento, $estado, new DateTimeImmutable());
    }
}
