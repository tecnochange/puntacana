<?php
namespace Notificaciones\Dominio;

final class EstadoNotificacion
{
    const ABIERTA = 'abierta';
    const COMPLETADA = 'completada';
    const VENCIDA = 'vencida';
    const CANCELADA = 'cancelada';

    /** Estados a los que un llamador puede cerrar una notificación; VENCIDA la pone el despacho. */
    public static function esCierreManual(string $estado): bool
    {
        return $estado === self::COMPLETADA || $estado === self::CANCELADA;
    }
}
