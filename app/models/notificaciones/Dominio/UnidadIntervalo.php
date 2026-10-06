<?php
namespace Notificaciones\Dominio;

/** Notificaciones_Tipos.unidad_intervalo_resumen. */
final class UnidadIntervalo
{
    const HORAS = 'HORAS';
    const DIAS = 'DIAS';
    const SEMANAS = 'SEMANAS';

    public static function esValida(string $unidad): bool
    {
        return in_array($unidad, [self::HORAS, self::DIAS, self::SEMANAS], true);
    }
}
