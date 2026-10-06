<?php
// Funciones de presentación compartidas por las vistas de notificaciones.

const NOTIFICACIONES_ESTADOS_VISIBLES = [
    'ABIERTA' => ['Abierta', 'bg-primary'],
    'COMPLETADA' => ['Completada', 'bg-success'],
    'VENCIDA' => ['Vencida', 'bg-danger'],
    'CANCELADA' => ['Cancelada', 'bg-secondary'],
];
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
