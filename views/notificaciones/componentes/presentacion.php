<?php
// Funciones de presentación compartidas por las vistas de notificaciones.

const NOTIFICACIONES_ESTADOS_VISIBLES = [
    'ABIERTA' => ['Abierta', 'bg-primary'],
    'COMPLETADA' => ['Completada', 'bg-success'],
    'VENCIDA' => ['Vencida', 'bg-danger'],
    'CANCELADA' => ['Cancelada', 'bg-secondary'],
];
const NOTIFICACIONES_CLASES_VISIBLES = [
    'TAREA' => ['Tarea', 'bx-task'],
    'AVISO' => ['Aviso', 'bx-bell'],
];
const NOTIFICACIONES_DIAS_VENCE_PRONTO = 3;
const NOTIFICACIONES_DIAS_URGENTE = 1;
const NOTIFICACIONES_ICONO_POR_DEFECTO = 'bx-bell';
const NOTIFICACIONES_COLOR_POR_DEFECTO = '#0d6efd';

function notificacionesEscapar(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

/** El color del tipo va dentro de un atributo style: solo se acepta un hexadecimal. */
function notificacionesColorSeguro(?string $color): string
{
    $esHexadecimal = $color !== null && preg_match('/^#[0-9a-fA-F]{3}([0-9a-fA-F]{3})?$/', $color);
    return $esHexadecimal ? $color : NOTIFICACIONES_COLOR_POR_DEFECTO;
}

/** El ícono del tipo va dentro de class: solo se acepta un nombre de Boxicons (bx-, bxs-, bxl-). */
function notificacionesIconoSeguro(?string $icono): string
{
    $esBoxicon = $icono !== null && preg_match('/^bx[sl]?-[a-z0-9-]+$/', $icono);
    return $esBoxicon ? $icono : NOTIFICACIONES_ICONO_POR_DEFECTO;
}

/** "kr #123" si la notificación trae registro; vacío si no. */
function notificacionesRegistroVisible(?string $tipoRegistro, $idRegistro): string
{
    if ($idRegistro === null) {
        return '';
    }
    $tipo = $tipoRegistro !== null && $tipoRegistro !== '' ? "$tipoRegistro " : '';
    return $tipo . '#' . (int) $idRegistro;
}

/** [texto, clase del badge] de un estado. */
function notificacionesEstadoVisible(string $estado): array
{
    return NOTIFICACIONES_ESTADOS_VISIBLES[$estado] ?? [$estado, 'bg-secondary'];
}

/** [texto, ícono] de una clase (Tarea o Aviso). */
function notificacionesClaseVisible(string $clase): array
{
    return NOTIFICACIONES_CLASES_VISIBLES[$clase] ?? [$clase, NOTIFICACIONES_ICONO_POR_DEFECTO];
}

/** Días de calendario de $desde a $hasta: negativo si $hasta ya pasó. */
function notificacionesDiasEntre(DateTimeImmutable $desde, DateTimeImmutable $hasta): int
{
    return (int) $desde->setTime(0, 0)->diff($hasta->setTime(0, 0))->format('%r%a');
}

function notificacionesTiempoRelativo(DateTimeImmutable $fecha, DateTimeImmutable $ahora): string
{
    $segundos = $ahora->getTimestamp() - $fecha->getTimestamp();
    if ($segundos < 60) {
        return 'hace un momento';
    }
    if ($segundos < 3600) {
        return 'hace ' . intdiv($segundos, 60) . ' min';
    }
    if ($segundos < 86400) {
        return 'hace ' . intdiv($segundos, 3600) . ' h';
    }
    $dias = intdiv($segundos, 86400);
    return $dias === 1 ? 'hace 1 día' : "hace $dias días";
}

/** [texto, clase del chip] del vencimiento; NULL si no tiene fecha. */
function notificacionesEtiquetaVencimiento(?int $dias, ?DateTimeImmutable $fecha): ?array
{
    if ($dias === null) {
        return null;
    }
    if ($dias < 0) {
        $atraso = -$dias;
        return [$atraso === 1 ? 'Venció ayer' : "Venció hace $atraso días", 'notif-chip--urgente'];
    }
    if ($dias === 0) {
        return ['Vence hoy', 'notif-chip--urgente'];
    }
    if ($dias <= NOTIFICACIONES_DIAS_URGENTE) {
        return ['Vence mañana', 'notif-chip--urgente'];
    }
    $clase = $dias <= NOTIFICACIONES_DIAS_VENCE_PRONTO ? 'notif-chip--pronto' : 'notif-chip--normal';
    return ["Vence en $dias días · " . $fecha->format('d/m/Y'), $clase];
}
