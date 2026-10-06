<?php
namespace Notificaciones\Dominio\Excepciones;

/** Se pidió cerrar una notificación con un estado que no es de cierre manual. */
final class EstadoNoPermitido extends ErrorNotificacion
{
}
