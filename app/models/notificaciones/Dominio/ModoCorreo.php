<?php
namespace Notificaciones\Dominio;

final class ModoCorreo
{
    /** Un correo por notificación, en la siguiente corrida del cron. */
    const INMEDIATO = 'inmediato';

    /** Se agrupa con las demás del día en un solo correo por persona. */
    const RESUMEN_DIARIO = 'resumen_diario';

    public static function esValido(string $modo): bool
    {
        return in_array($modo, [self::INMEDIATO, self::RESUMEN_DIARIO], true);
    }
}
