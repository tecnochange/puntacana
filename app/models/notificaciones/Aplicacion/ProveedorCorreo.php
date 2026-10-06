<?php
namespace Notificaciones\Aplicacion;

/** Lo que el despacho necesita de un proveedor de correo. Hoy lo implementa CorreoBrevo. */
interface ProveedorCorreo
{
    public function enviar(string $nombreDestinatario, string $correoDestinatario, string $asunto, string $html): RespuestaProveedor;
}
