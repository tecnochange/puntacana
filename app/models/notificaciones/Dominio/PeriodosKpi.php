<?php
namespace Notificaciones\Dominio;

use DateTimeImmutable;

/**
 * Periodos de seguimiento de un KPI según su frecuencia (Frecuencia_Kpis.tipo).
 * Los meses son calendario (avance_1 = enero ... avance_12 = diciembre) y los
 * grupos replican cómo KpisCrud::actualizar_avance_Kpis copia el valor de un
 * periodo a todos sus meses. El año de los KPIs es fiscal: el anio 2027 va de
 * julio de 2026 a junio de 2027.
 */
final class PeriodosKpi
{
    private const MESES = [1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
    private const PRIMER_MES_FISCAL = 7;

    /** Grupos de meses por frecuencia, en orden fiscal; el último mes de cada grupo es el cierre. */
    private const GRUPOS = [
        1 => [[7], [8], [9], [10], [11], [12], [1], [2], [3], [4], [5], [6]], // Mensual
        2 => [[7, 8], [9, 10], [11, 12], [1, 2], [3, 4], [5, 6]],             // Bimestral
        3 => [[7, 8, 9], [10, 11, 12], [1, 2, 3], [4, 5, 6]],                 // Trimestral
        4 => [[7, 8, 9, 10, 11, 12], [1, 2, 3, 4, 5, 6]],                     // Semestral
        5 => [[7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6]],                       // Anual
        6 => [[7, 8, 9, 10], [11, 12, 1, 2], [3, 4, 5, 6]],                   // Cuatrimestral
    ];

    /** [['meses' => int[], 'mes_cierre' => int, 'fecha_cierre' => DateTimeImmutable, 'nombre' => string], ...] */
    public static function deFrecuencia(int $frecuencia, int $anioFiscal): array
    {
        if (!isset(self::GRUPOS[$frecuencia])) {
            return [];
        }
        $periodos = [];
        foreach (self::GRUPOS[$frecuencia] as $meses) {
            $primero = $meses[0];
            $ultimo = $meses[count($meses) - 1];
            $fechaCierre = (new DateTimeImmutable(sprintf('%04d-%02d-01', self::anioCalendario($anioFiscal, $ultimo), $ultimo)))->modify('last day of this month');
            $nombre = $primero === $ultimo
                ? self::MESES[$primero]
                : self::MESES[$primero] . ' a ' . self::MESES[$ultimo];
            $periodos[] = [
                'meses' => $meses,
                'mes_cierre' => $ultimo,
                'fecha_cierre' => $fechaCierre,
                'nombre' => $nombre . ' ' . self::anioCalendario($anioFiscal, $ultimo),
            ];
        }
        return $periodos;
    }

    /** El anio fiscal en curso: de julio en adelante ya es el del año siguiente. */
    public static function anioFiscal(DateTimeImmutable $fecha): int
    {
        $anio = (int) $fecha->format('Y');
        return (int) $fecha->format('n') >= self::PRIMER_MES_FISCAL ? $anio + 1 : $anio;
    }

    private static function anioCalendario(int $anioFiscal, int $mes): int
    {
        return $mes >= self::PRIMER_MES_FISCAL ? $anioFiscal - 1 : $anioFiscal;
    }
}
