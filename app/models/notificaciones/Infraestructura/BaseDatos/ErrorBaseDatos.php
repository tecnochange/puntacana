<?php
namespace Notificaciones\Infraestructura\BaseDatos;

/** Error de MySQL con el mismo código (errno), lance o no excepciones mysqli. */
final class ErrorBaseDatos extends \RuntimeException
{
    private const ERRNO_LLAVE_DUPLICADA = 1062;

    public function esLlaveDuplicada(): bool
    {
        return $this->getCode() === self::ERRNO_LLAVE_DUPLICADA;
    }
}
