<?php
namespace Notificaciones\Dominio;

/** Qué hace el despacho de correos para una empresa (Notificaciones_Configuracion.modo). */
final class ModoOperacion
{
    /** El despacho no toca nada: los avisos pendientes esperan. */
    const APAGADO = 'APAGADO';

    /** Solo bandeja: los avisos se consumen y se registran como omitidos, sin correo. */
    const SOLO_PLATAFORMA = 'SOLO_PLATAFORMA';

    /** Correo únicamente a la lista de prueba; al resto se le registra como omitido. */
    const PRUEBA = 'PRUEBA';

    const ACTIVO = 'ACTIVO';

    /** Un valor desconocido o vacío se trata como apagado: ante la duda, no enviar. */
    public static function desdeValor(string $valor): string
    {
        $validos = [self::APAGADO, self::SOLO_PLATAFORMA, self::PRUEBA, self::ACTIVO];
        return in_array($valor, $validos, true) ? $valor : self::APAGADO;
    }
}
