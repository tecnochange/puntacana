<?php
namespace Notificaciones\Dominio;

final class ClaseNotificacion
{
    /** Algo por hacer: queda pendiente en la bandeja y vence con su fecha_vencimiento. */
    const TAREA = 'TAREA';

    /** Informativa: solo leída o no leída. */
    const AVISO = 'AVISO';

    public static function esValida(string $clase): bool
    {
        return in_array($clase, [self::TAREA, self::AVISO], true);
    }
}
