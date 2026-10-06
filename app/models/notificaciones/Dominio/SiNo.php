<?php
namespace Notificaciones\Dominio;

/** Columnas ENUM('SI','NO') de Notificaciones_Tipos (canales, notificar_autor). */
final class SiNo
{
    const SI = 'SI';
    const NO = 'NO';

    public static function esSi($valor): bool
    {
        return $valor === self::SI;
    }
}
