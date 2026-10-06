<?php
namespace Notificaciones\Aplicacion;

/** Lo que respondió el proveedor de correo a un envío. De solo lectura por convención. */
final class RespuestaProveedor
{
    /** @var bool */
    public $exitosa;
    /** @var string|null */
    public $idMensaje;
    /** @var string|null */
    public $mensajeError;

    private function __construct(bool $exitosa, ?string $idMensaje, ?string $mensajeError)
    {
        $this->exitosa = $exitosa;
        $this->idMensaje = $idMensaje;
        $this->mensajeError = $mensajeError;
    }

    public static function exitosa(string $idMensaje): self
    {
        return new self(true, $idMensaje, null);
    }

    public static function fallida(string $mensajeError): self
    {
        return new self(false, null, $mensajeError);
    }
}
