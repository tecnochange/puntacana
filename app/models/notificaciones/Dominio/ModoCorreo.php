<?php
namespace Notificaciones\Dominio;

enum ModoCorreo: string
{
    /** Un correo por notificación, en la siguiente corrida del cron. */
    case INMEDIATO = 'inmediato';

    /** Se agrupa con las demás del día en un solo correo por persona. */
    case RESUMEN_DIARIO = 'resumen_diario';
}
