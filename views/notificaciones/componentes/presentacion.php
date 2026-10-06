<?php
// Funciones de presentación compartidas por las vistas de notificaciones.

const NOTIFICACIONES_ESTADOS_VISIBLES = [
    'abierta' => ['Abierta', 'bg-primary'],
    'completada' => ['Completada', 'bg-success'],
    'vencida' => ['Vencida', 'bg-danger'],
    'cancelada' => ['Cancelada', 'bg-secondary'],
];
const NOTIFICACIONES_ICONO_POR_DEFECTO = 'bx-bell';

function notificacionesEscapar(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
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
