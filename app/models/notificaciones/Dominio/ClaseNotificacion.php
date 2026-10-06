<?php
namespace Notificaciones\Dominio;

enum ClaseNotificacion: string
{
    /** Algo por hacer: queda pendiente en la bandeja y vence con su fecha límite. */
    case TAREA = 'tarea';

    /** Informativa: solo leída o no leída. */
    case AVISO = 'aviso';
}
