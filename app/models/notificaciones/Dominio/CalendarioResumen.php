<?php
namespace Notificaciones\Dominio;

use DateTimeImmutable;

/**
 * Cuándo sale un resumen de correos. Los horarios son fijos en el calendario (no
 * dependen de cuándo se creó cada notificación), así las del mismo tipo caen en
 * el mismo correo:
 *  - HORAS:   cada N horas contadas desde medianoche (N=2: 00:00, 02:00, 04:00...).
 *  - DIAS:    a la hora_resumen, cada N días contados desde FECHA_ANCLA.
 *  - SEMANAS: a la hora_resumen del dia_semana_resumen, cada N semanas desde FECHA_ANCLA.
 */
final class CalendarioResumen
{
    /** Un lunes: punto de partida fijo para contar días y semanas. */
    private const FECHA_ANCLA = '2026-01-05';
    private const DIAS_POR_SEMANA = 7;

    /**
     * Si $ahora cae dentro de la hora de un resumen, el inicio de ese resumen
     * (lo pendiente hasta ese momento entra en él). NULL si no es hora de resumen.
     */
    public static function inicioResumenActual(
        DateTimeImmutable $ahora,
        int $intervalo,
        string $unidad,
        int $horaResumen,
        int $diaSemanaResumen
    ): ?DateTimeImmutable {
        $hora = (int) $ahora->format('G');

        if ($unidad === UnidadIntervalo::HORAS) {
            return $hora % $intervalo === 0 ? $ahora->setTime($hora, 0) : null;
        }
        if ($hora !== $horaResumen) {
            return null;
        }

        $diasDesdeAncla = self::diasDesdeAncla($ahora);
        if ($unidad === UnidadIntervalo::DIAS) {
            return $diasDesdeAncla % $intervalo === 0 ? $ahora->setTime($horaResumen, 0) : null;
        }

        $esElDiaDeLaSemana = (int) $ahora->format('N') === $diaSemanaResumen;
        $semanasDesdeAncla = intdiv($diasDesdeAncla, self::DIAS_POR_SEMANA);
        return $esElDiaDeLaSemana && $semanasDesdeAncla % $intervalo === 0 ? $ahora->setTime($horaResumen, 0) : null;
    }

    private static function diasDesdeAncla(DateTimeImmutable $fecha): int
    {
        $soloDia = new DateTimeImmutable($fecha->format('Y-m-d'), $fecha->getTimezone());
        $ancla = new DateTimeImmutable(self::FECHA_ANCLA, $fecha->getTimezone());
        return (int) $ancla->diff($soloDia)->days;
    }
}
