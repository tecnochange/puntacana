<?php
namespace Notificaciones\Dominio;

/** Qué hace el despacho de correos para una empresa (configuración modo_operacion). */
final class ModoOperacion
{
    /** El despacho no toca nada: los correos pendientes esperan. */
    const APAGADO = 'APAGADO';

    /** Solo bandeja: los correos pendientes se consumen y se registran como omitidos. */
    const SOLO_BANDEJA = 'SOLO_BANDEJA';

    /** Correo únicamente a ids_empleados_prueba; al resto se le registra como omitido. */
    const PRUEBA = 'PRUEBA';

    const ACTIVO = 'ACTIVO';

    public static function valores(): array
    {
        return [self::APAGADO, self::SOLO_BANDEJA, self::PRUEBA, self::ACTIVO];
    }

    /** Un valor desconocido o vacío se trata como APAGADO: ante la duda, no enviar. */
    public static function desdeValor(string $valor): string
    {
        return in_array($valor, self::valores(), true) ? $valor : self::APAGADO;
    }
}
