<?php
namespace Notificaciones\Infraestructura\Correo;

use Notificaciones\Aplicacion\ProveedorCorreo;
use Notificaciones\Aplicacion\RespuestaProveedor;

/**
 * Adaptador sobre app/models/brevo/Brevo.php, que se usa tal cual (tiene la
 * llave de la API y está fuera de git). Brevo responde con messageId si aceptó
 * el correo, o con code y message si lo rechazó.
 */
final class CorreoBrevo implements ProveedorCorreo
{
    /** @var \Brevo */
    private $brevo;

    public function __construct()
    {
        require_once __DIR__ . '/../../../brevo/Brevo.php';
        $this->brevo = new \Brevo();
    }

    public function enviar(string $nombreDestinatario, string $correoDestinatario, string $asunto, string $html): RespuestaProveedor
    {
        $respuesta = $this->brevo->individual($nombreDestinatario, $asunto, $correoDestinatario, $html);

        if (isset($respuesta->messageId)) {
            return RespuestaProveedor::exitosa((string) $respuesta->messageId);
        }
        $detalle = isset($respuesta->message) ? (string) $respuesta->message : 'sin respuesta de Brevo';
        return RespuestaProveedor::fallida($detalle);
    }
}
