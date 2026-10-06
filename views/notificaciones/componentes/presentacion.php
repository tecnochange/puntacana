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

/** [texto, clase del badge] de un estado. */
function notificacionesEstadoVisible(string $estado): array
{
    return NOTIFICACIONES_ESTADOS_VISIBLES[$estado] ?? [$estado, 'bg-secondary'];
}
