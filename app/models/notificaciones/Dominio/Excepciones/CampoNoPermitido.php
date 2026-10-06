<?php
namespace Notificaciones\Dominio\Excepciones;

/** Un dato no está declarado en CodigoNotificacion::campos() o es sensible. */
final class CampoNoPermitido extends ErrorNotificacion
{
}
