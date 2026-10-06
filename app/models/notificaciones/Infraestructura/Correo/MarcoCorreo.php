<?php
namespace Notificaciones\Infraestructura\Correo;

/** Encabezado y pie comunes de todos los correos. El contenido ya viene escapado. */
final class MarcoCorreo
{
    public static function envolver(string $contenidoHtml): string
    {
        return '<!DOCTYPE html><html><body style="margin:0;padding:0;background:#f4f6f8;">'
            . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:24px 0;">'
            . '<tr><td align="center">'
            . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:8px;font-family:Arial,Helvetica,sans-serif;color:#333333;">'
            . '<tr><td style="background:#0d6efd;color:#ffffff;padding:16px 24px;border-radius:8px 8px 0 0;font-size:18px;font-weight:bold;">Go For Agile</td></tr>'
            . '<tr><td style="padding:24px;font-size:14px;line-height:1.6;">' . $contenidoHtml . '</td></tr>'
            . '<tr><td style="padding:16px 24px;font-size:12px;color:#888888;border-top:1px solid #eeeeee;">'
            . 'Este es un correo automático, por favor no respondas a esta dirección.'
            . '</td></tr>'
            . '</table></td></tr></table></body></html>';
    }
}
