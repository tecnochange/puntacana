<?php
namespace Notificaciones\Dominio;

final class EstadoNotificacion
{
    const ABIERTA = 'ABIERTA';
    const COMPLETADA = 'COMPLETADA';
    const VENCIDA = 'VENCIDA';
    const CANCELADA = 'CANCELADA';

    /** Estados a los que un llamador puede cerrar una notificación; VENCIDA la pone el despacho. */
    public static function esCierreManual(string $estado): bool
    {
        return $estado === self::COMPLETADA || $estado === self::CANCELADA;
    }
}
