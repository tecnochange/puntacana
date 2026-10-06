<?php
require_once __DIR__ . '/../../app/models/notificaciones/cargar.php';
require_once __DIR__ . '/componentes/presentacion.php';

use Notificaciones\Aplicacion\ConsultarBandeja;
use Notificaciones\Dominio\EstadoNotificacion;

const NOTIFICACIONES_DIAS_PARA_ALERTA = 3;

/** Lo que la vista necesita de cada fila, ya calculado. */
function notificacionesPresentar(array $fila, DateTimeImmutable $hoy): array
{
    $fechaLimite = $fila['fecha_limite'] ? new DateTimeImmutable($fila['fecha_limite']) : null;
    $diasRestantes = $fechaLimite ? (int) $hoy->diff($fechaLimite->setTime(0, 0))->format('%r%a') : null;
    $estaAbierta = $fila['estado'] === EstadoNotificacion::ABIERTA->value;
    $venceProntoOVencida = $estaAbierta && $diasRestantes !== null && $diasRestantes <= NOTIFICACIONES_DIAS_PARA_ALERTA;

    $plazo = '';
    if ($estaAbierta && $diasRestantes !== null) {
        $plazo = $diasRestantes < 0 ? 'Vencida' : ($diasRestantes === 0 ? 'Vence hoy' : "Faltan $diasRestantes días");
    }

    return [
        'id' => (int) $fila['id'],
        'titulo' => $fila['titulo'],
        'cuerpo' => $fila['cuerpo'],
        'icono' => $fila['icono'] ?: NOTIFICACIONES_ICONO_POR_DEFECTO,
        'color' => $fila['color'] ?: '',
        'leida' => $fila['fecha_lectura'] !== null,
        'creada' => (new DateTimeImmutable($fila['created_at']))->format('d/m/Y H:i'),
        'registro' => notificacionesRegistroVisible($fila['tipo_registro'], $fila['id_registro']),
        'fecha_limite' => $fechaLimite ? $fechaLimite->format('d/m/Y') : '',
        'plazo' => $plazo,
        'plazo_clase' => $venceProntoOVencida ? 'text-danger' : 'text-muted',
        'estado' => notificacionesEstadoVisible($fila['estado']),
    ];
}

function notificacionesPintarLista(array $items, string $vacio, bool $mostrarEstado): void
{
    if (!$items) {
        echo '<p class="text-muted my-4 text-center">' . notificacionesEscapar($vacio) . '</p>';
        return;
    }
    echo '<ul class="list-group list-group-flush">';
    foreach ($items as $n) {
        $fondo = $n['leida'] ? '' : ' bg-light';
        $peso = $n['leida'] ? '' : ' fw-bold';
        echo '<li class="list-group-item d-flex align-items-start gap-3 py-3' . $fondo . '" id="notificacion_' . $n['id'] . '">';
        echo '<i class="bx ' . notificacionesEscapar($n['icono']) . ' fs-3" style="color:' . notificacionesEscapar($n['color']) . '"></i>';
        echo '<div class="flex-grow-1">';
        echo '<div class="' . $peso . '">' . notificacionesEscapar($n['titulo']) . '</div>';
        if ($n['cuerpo'] !== null && $n['cuerpo'] !== '') {
            echo '<div class="small">' . notificacionesEscapar($n['cuerpo']) . '</div>';
        }
        echo '<div class="small text-muted mt-1">' . notificacionesEscapar($n['creada']);
        if ($n['registro'] !== '') {
            echo ' · Registro: ' . notificacionesEscapar($n['registro']);
        }
        if ($n['fecha_limite'] !== '') {
            echo ' · Fecha límite: ' . notificacionesEscapar($n['fecha_limite']);
        }
        if ($n['plazo'] !== '') {
            echo ' · <span class="' . $n['plazo_clase'] . '">' . notificacionesEscapar($n['plazo']) . '</span>';
        }
        if ($mostrarEstado) {
            echo ' · <span class="badge ' . $n['estado'][1] . '">' . notificacionesEscapar($n['estado'][0]) . '</span>';
        }
        echo '</div></div>';
        echo '<div class="d-flex gap-2">';
        echo '<a class="btn btn-sm btn-primary" href="api/notificaciones/abrir.php?id=' . $n['id'] . '">Abrir</a>';
        echo '<a class="btn btn-sm btn-outline-secondary" href="?pg=notificaciones/detalle&id=' . $n['id'] . '">Detalle</a>';
        if (!$n['leida']) {
            echo '<button type="button" class="btn btn-sm btn-outline-primary bt_marcar_leida" data-id="' . $n['id'] . '">Marcar leída</button>';
        }
        echo '</div></li>';
    }
    echo '</ul>';
}

$bandeja = ConsultarBandeja::ejecutar($connect_admin, (int) $user_log['id_empresa'], (int) $user_log['id']);

$hoy = new DateTimeImmutable('today');
$presentar = fn (array $filas) => array_map(fn (array $fila) => notificacionesPresentar($fila, $hoy), $filas);
$pendientes = $presentar($bandeja['pendientes']);
$avisos = $presentar($bandeja['avisos']);
$historial = $presentar($bandeja['historial']);
?>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <div class="card mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="mb-0">Mis notificaciones</h3>
            <?php if ($bandeja['no_leidas'] > 0): ?>
                <button type="button" class="btn btn-outline-primary btn-sm" id="bt_marcar_todas">
                    Marcar todas como leídas (<?= (int) $bandeja['no_leidas'] ?>)
                </button>
            <?php endif; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_pendientes" type="button" role="tab">
                        Pendientes <span class="badge bg-primary"><?= count($pendientes) ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_avisos" type="button" role="tab">
                        Notificaciones <span class="badge bg-secondary"><?= count($avisos) ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_historial" type="button" role="tab">
                        Historial
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body tab-content">
            <div class="tab-pane fade show active" id="tab_pendientes" role="tabpanel">
                <?php notificacionesPintarLista($pendientes, 'No tienes pendientes.', false); ?>
            </div>
            <div class="tab-pane fade" id="tab_avisos" role="tabpanel">
                <?php notificacionesPintarLista($avisos, 'No tienes notificaciones.', false); ?>
            </div>
            <div class="tab-pane fade" id="tab_historial" role="tabpanel">
                <?php notificacionesPintarLista($historial, 'Sin historial.', true); ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.bt_marcar_leida').on('click', function () {
            var boton = $(this);
            $.post('api/notificaciones/marcar_leida.php', { id: boton.data('id') }, function (respuesta) {
                if (respuesta.status === 'success') {
                    $('#notificacion_' + boton.data('id')).removeClass('bg-light').find('.fw-bold').removeClass('fw-bold');
                    boton.remove();
                }
            }, 'json');
        });

        $('#bt_marcar_todas').on('click', function () {
            $.post('api/notificaciones/marcar_todas_leidas.php', {}, function (respuesta) {
                if (respuesta.status === 'success') {
                    location.reload();
                }
            }, 'json');
        });
    });
</script>
