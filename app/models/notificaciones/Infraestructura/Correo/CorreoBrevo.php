<?php
namespace Notificaciones\Infraestructura\Correo;

use Notificaciones\Aplicacion\EnviadorCorreo;
use Notificaciones\Aplicacion\ResultadoEnvio;

/**
 * Adaptador sobre app/models/brevo/Brevo.php, que se usa tal cual (tiene la
 * llave de la API y está fuera de git). Brevo responde con messageId si aceptó
 * el correo, o con code y message si lo rechazó.
 */
final class CorreoBrevo implements EnviadorCorreo
{
    private \Brevo $brevo;

    public function __construct()
    {
        require_once __DIR__ . '/../../../brevo/Brevo.php';
        $this->brevo = new \Brevo();
    }

    public function enviar(string $nombre, string $correo, string $asunto, string $html): ResultadoEnvio
    {
        $respuesta = $this->brevo->individual($nombre, $asunto, $correo, $html);

        if (isset($respuesta->messageId)) {
            return ResultadoEnvio::exitoso((string) $respuesta->messageId);
        }
        $detalle = isset($respuesta->message) ? (string) $respuesta->message : 'sin respuesta de Brevo';
        return ResultadoEnvio::fallido($detalle);
    }
}
