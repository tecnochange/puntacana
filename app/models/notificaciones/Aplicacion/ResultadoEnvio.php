<?php
namespace Notificaciones\Aplicacion;

/** Resultado de un envío. De solo lectura por convención. */
final class ResultadoEnvio
{
    /** @var bool */
    public $exitoso;
    /** @var string|null */
    public $idMensaje;
    /** @var string|null */
    public $error;

    private function __construct(bool $exitoso, ?string $idMensaje, ?string $error)
    {
        $this->exitoso = $exitoso;
        $this->idMensaje = $idMensaje;
        $this->error = $error;
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
