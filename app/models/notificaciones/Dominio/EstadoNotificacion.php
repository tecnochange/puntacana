<?php
namespace Notificaciones\Dominio;

enum EstadoNotificacion: string
{
    case ABIERTA = 'abierta';
    case COMPLETADA = 'completada';
    case VENCIDA = 'vencida';
    case CANCELADA = 'cancelada';

    /** Estados a los que un llamador puede cerrar una notificación; VENCIDA la pone el despacho. */
    public function esCierreManual(): bool
    {
        return $this === self::COMPLETADA || $this === self::CANCELADA;
    }
}
