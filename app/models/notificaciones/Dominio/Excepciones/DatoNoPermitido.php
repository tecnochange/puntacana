<?php
namespace Notificaciones\Dominio\Excepciones;

/** Un dato de la plantilla no está declarado en CodigoNotificacion::datosPermitidos() o es sensible. */
final class DatoNoPermitido extends ErrorNotificacion
{
}
