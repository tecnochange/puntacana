<?php
namespace Notificaciones\Aplicacion;

/** Lo que el despacho necesita de un proveedor de correo. Hoy lo implementa CorreoBrevo. */
interface EnviadorCorreo
{
    public function enviar(string $nombre, string $correo, string $asunto, string $html): ResultadoEnvio;
}
