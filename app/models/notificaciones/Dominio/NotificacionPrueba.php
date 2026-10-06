<?php
namespace Notificaciones\Dominio;

/**
 * Las notificaciones generadas con "Enviarme una prueba" llevan este
 * tipo_registro. Ignoran el estado del tipo al generarse y al enviarse, para
 * poder probar un tipo antes de activarlo.
 */
final class NotificacionPrueba
{
    const TIPO_REGISTRO = 'PRUEBA';

    public static function es(?string $tipoRegistro): bool
    {
        return $tipoRegistro === self::TIPO_REGISTRO;
    }
}
