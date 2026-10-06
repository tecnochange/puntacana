<?php
namespace Notificaciones\Dominio\Excepciones;

/** La url no es relativa al portal (debe empezar por ?pg=). */
final class UrlNoPermitida extends ErrorNotificacion
{
}
