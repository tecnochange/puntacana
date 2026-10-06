<?php
namespace Notificaciones\Dominio;

/** Notificaciones_Tipos.estado: un tipo INACTIVO no genera ni envía notificaciones. */
final class EstadoTipo
{
    const ACTIVO = 'ACTIVO';
    const INACTIVO = 'INACTIVO';

    public static function esValido(string $estado): bool
    {
        return in_array($estado, [self::ACTIVO, self::INACTIVO], true);
    }
}
