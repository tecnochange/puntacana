<?php
namespace Notificaciones\Aplicacion;

final class ResultadoEnvio
{
    private function __construct(
        public readonly bool $exitoso,
        public readonly ?string $idMensaje,
        public readonly ?string $error,
    ) {
    }

    public static function exitoso(string $idMensaje): self
    {
        return new self(true, $idMensaje, null);
    }

    public static function fallido(string $error): self
    {
        return new self(false, null, $error);
    }
}
