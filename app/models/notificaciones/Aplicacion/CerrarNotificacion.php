<?php
namespace Notificaciones\Aplicacion;

use DateTimeImmutable;
use mysqli;
use Notificaciones\Dominio\EstadoNotificacion;
use Notificaciones\Dominio\Excepciones\EstadoNoPermitido;
use Notificaciones\Infraestructura\BaseDatos\Consulta;
use Notificaciones\Infraestructura\BaseDatos\EventosRepositorio;

/**
 * Cierra una notificación abierta por su clave_unica (p. ej. cuando el registro
 * se completó o se borró). $estado: EstadoNotificacion::COMPLETADA o ::CANCELADA.
 * Deja de recordarse en la siguiente corrida del cron.
 * Devuelve false si no existía o ya estaba cerrada.
 */
final class CerrarNotificacion
{
    public static function ejecutar(mysqli $conexion, int $idEmpresa, string $claveUnica, string $estado): bool
    {
        if (!EstadoNotificacion::esCierreManual($estado)) {
            throw new EstadoNoPermitido("Una notificación solo se cierra como completada o cancelada, no como '$estado'.");
        }
        $eventos = new EventosRepositorio(new Consulta($conexion));
        return $eventos->cerrarPorClaveUnica($idEmpresa, $claveUnica, $estado, new DateTimeImmutable());
    }
}
