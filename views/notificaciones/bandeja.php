<?php
require_once __DIR__ . '/../../app/models/notificaciones/cargar.php';
require_once __DIR__ . '/componentes/presentacion.php';

use Notificaciones\Aplicacion\ConsultarBandeja;
use Notificaciones\Dominio\EstadoNotificacion;

const NOTIFICACIONES_DIAS_VENCE_PRONTO = 3;
const NOTIFICACIONES_DIAS_URGENTE = 1;
const NOTIFICACIONES_DIAS_ESTA_SEMANA = 7;

/** Lo que la vista necesita de cada notificación, ya calculado. */
function notificacionesPresentar(array $fila, DateTimeImmutable $ahora): array
{
    $hoy = $ahora->setTime(0, 0);
    $creada = new DateTimeImmutable($fila['created_at']);
    $fechaVencimiento = $fila['fecha_vencimiento'] ? new DateTimeImmutable($fila['fecha_vencimiento']) : null;
    $estaAbierta = $fila['estado'] === EstadoNotificacion::ABIERTA;
    $diasParaVencer = $fechaVencimiento ? (int) $hoy->diff($fechaVencimiento->setTime(0, 0))->format('%r%a') : null;

    return [
        'id' => (int) $fila['id'],
        'titulo' => $fila['titulo'],
        'mensaje' => $fila['mensaje'],
        'icono' => notificacionesIconoSeguro($fila['icono']),
        'color' => notificacionesColorSeguro($fila['color']),
        'leida' => $fila['fecha_lectura'] !== null,
        'grupo' => notificacionesGrupoFecha($creada, $hoy),
        'hace' => notificacionesTiempoRelativo($creada, $ahora),
        'creada' => $creada->format('d/m/Y H:i'),
        'vencimiento' => $estaAbierta ? notificacionesEtiquetaVencimiento($diasParaVencer, $fechaVencimiento) : null,
        'vence_pronto' => $estaAbierta && $diasParaVencer !== null && $diasParaVencer <= NOTIFICACIONES_DIAS_VENCE_PRONTO,
        'registro' => notificacionesRegistroVisible($fila['tipo_registro'], $fila['id_registro']),
        'estado' => $estaAbierta ? null : notificacionesEstadoVisible($fila['estado']),
    ];
}

/** [texto, clase] del chip de vencimiento; NULL si no tiene fecha. */
function notificacionesEtiquetaVencimiento(?int $dias, ?DateTimeImmutable $fecha): ?array
{
    if ($dias === null) {
        return null;
    }
    if ($dias <= 0) {
        return ['Vence hoy', 'notif-chip--urgente'];
    }
    if ($dias <= NOTIFICACIONES_DIAS_URGENTE) {
        return ['Vence mañana', 'notif-chip--urgente'];
    }
    $clase = $dias <= NOTIFICACIONES_DIAS_VENCE_PRONTO ? 'notif-chip--pronto' : 'notif-chip--normal';
    return ["Vence en $dias días · " . $fecha->format('d/m/Y'), $clase];
}

function notificacionesGrupoFecha(DateTimeImmutable $fecha, DateTimeImmutable $hoy): string
{
    $diasAtras = (int) $fecha->setTime(0, 0)->diff($hoy)->format('%r%a');
    if ($diasAtras <= 0) {
        return 'Hoy';
    }
    if ($diasAtras === 1) {
        return 'Ayer';
    }
    return $diasAtras < NOTIFICACIONES_DIAS_ESTA_SEMANA ? 'Esta semana' : 'Anteriores';
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

/** Agrupa por 'Hoy', 'Ayer', ... conservando el orden (más recientes primero). */
function notificacionesAgruparPorFecha(array $notificaciones): array
{
    $grupos = [];
    foreach ($notificaciones as $notificacion) {
        $grupos[$notificacion['grupo']][] = $notificacion;
    }
    return $grupos;
}

function notificacionesPintarItem(array $n): void
{
    $e = 'notificacionesEscapar';
    $claseLeida = $n['leida'] ? '' : ' notif-item--no-leida';
    ?>
    <article class="notif-item<?= $claseLeida ?>" id="notificacion_<?= $n['id'] ?>" style="--notif-color: <?= $n['color'] ?>"
             data-buscar="<?= $e(mb_strtolower($n['titulo'] . ' ' . $n['mensaje'] . ' ' . $n['registro'])) ?>">
        <div class="notif-item__icono"><i class="bx <?= $n['icono'] ?>"></i></div>
        <div class="notif-item__cuerpo">
            <div class="notif-item__titulo">
                <?php if (!$n['leida']): ?><span class="notif-punto" title="Sin leer"></span><?php endif; ?>
                <a href="api/notificaciones/abrir.php?id=<?= $n['id'] ?>"><?= $e($n['titulo']) ?></a>
            </div>
            <?php if ($n['mensaje'] !== null && $n['mensaje'] !== ''): ?>
                <p class="notif-item__mensaje"><?= $e($n['mensaje']) ?></p>
            <?php endif; ?>
            <div class="notif-item__meta">
                <span title="<?= $e($n['creada']) ?>"><i class="bx bx-time-five"></i> <?= $e($n['hace']) ?></span>
                <?php if ($n['vencimiento']): ?>
                    <span class="notif-chip <?= $n['vencimiento'][1] ?>"><i class="bx bx-calendar-exclamation"></i> <?= $e($n['vencimiento'][0]) ?></span>
                <?php endif; ?>
                <?php if ($n['registro'] !== ''): ?>
                    <span class="notif-chip notif-chip--normal"><i class="bx bx-link-alt"></i> <?= $e($n['registro']) ?></span>
                <?php endif; ?>
                <?php if ($n['estado']): ?>
                    <span class="badge rounded-pill <?= $n['estado'][1] ?>"><?= $e($n['estado'][0]) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="notif-item__acciones">
            <a class="btn btn-sm btn-primary" href="api/notificaciones/abrir.php?id=<?= $n['id'] ?>">Abrir <i class="bx bx-right-arrow-alt"></i></a>
            <a class="btn btn-sm btn-light" href="?pg=notificaciones/detalle&id=<?= $n['id'] ?>" title="Ver detalle"><i class="bx bx-show"></i></a>
            <?php if (!$n['leida']): ?>
                <button type="button" class="btn btn-sm btn-light bt_marcar_leida" data-id="<?= $n['id'] ?>" title="Marcar como leída"><i class="bx bx-check"></i></button>
            <?php endif; ?>
        </div>
    </article>
    <?php
}

function notificacionesPintarLista(array $notificaciones, string $icono, string $tituloVacio, string $textoVacio): void
{
    if (!$notificaciones) {
        ?>
        <div class="notif-vacio">
            <div class="notif-vacio__icono"><i class="bx <?= $icono ?>"></i></div>
            <h5><?= notificacionesEscapar($tituloVacio) ?></h5>
            <p><?= notificacionesEscapar($textoVacio) ?></p>
        </div>
        <?php
        return;
    }
    foreach (notificacionesAgruparPorFecha($notificaciones) as $grupo => $items) {
        echo '<div class="notif-grupo"><div class="notif-grupo__titulo">' . notificacionesEscapar($grupo) . '</div>';
        foreach ($items as $notificacion) {
            notificacionesPintarItem($notificacion);
        }
        echo '</div>';
    }
    echo '<div class="notif-sin-resultados d-none"><i class="bx bx-search-alt"></i> Ninguna notificación coincide con la búsqueda.</div>';
}

$bandeja = ConsultarBandeja::ejecutar($connect_admin, (int) $user_log['id_empresa'], (int) $user_log['id']);

$ahora = new DateTimeImmutable();
$presentar = function (array $filas) use ($ahora) {
    return array_map(function (array $fila) use ($ahora) {
        return notificacionesPresentar($fila, $ahora);
    }, $filas);
};
$pendientes = $presentar($bandeja['pendientes']);
$avisos = $presentar($bandeja['avisos']);
$historial = $presentar($bandeja['historial']);
$cantidadVencenPronto = count(array_filter($pendientes, function (array $n) {
    return $n['vence_pronto'];
}));
?>

<?php require __DIR__ . '/componentes/estilos.php'; ?>

<div class="notif">
    <div class="notif-encabezado">
        <div>
            <h3>Mis notificaciones</h3>
            <p>Tus tareas pendientes y avisos en un solo lugar.</p>
        </div>
        <?php if ($bandeja['cantidad_no_leidas'] > 0): ?>
            <button type="button" class="btn btn-outline-primary" id="bt_marcar_todas">
                <i class="bx bx-check-double"></i> Marcar todas como leídas
            </button>
        <?php endif; ?>
    </div>

    <div class="notif-resumen">
        <div class="notif-tarjeta notif-tarjeta--pendientes">
            <div class="notif-tarjeta__icono"><i class="bx bx-task"></i></div>
            <div><div class="notif-tarjeta__valor"><?= count($pendientes) ?></div><div class="notif-tarjeta__etiqueta">Pendientes</div></div>
        </div>
        <div class="notif-tarjeta notif-tarjeta--pronto">
            <div class="notif-tarjeta__icono"><i class="bx bx-alarm-exclamation"></i></div>
            <div><div class="notif-tarjeta__valor"><?= $cantidadVencenPronto ?></div><div class="notif-tarjeta__etiqueta">Vencen en <?= NOTIFICACIONES_DIAS_VENCE_PRONTO ?> días o menos</div></div>
        </div>
        <div class="notif-tarjeta notif-tarjeta--sin-leer">
            <div class="notif-tarjeta__icono"><i class="bx bx-bell"></i></div>
            <div><div class="notif-tarjeta__valor"><?= (int) $bandeja['cantidad_no_leidas'] ?></div><div class="notif-tarjeta__etiqueta">Sin leer</div></div>
        </div>
    </div>

    <div class="notif-panel">
        <div class="notif-barra">
            <ul class="nav notif-pestanas" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_pendientes" type="button" role="tab">
                        <i class="bx bx-task"></i> Pendientes <span class="notif-contador"><?= count($pendientes) ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_avisos" type="button" role="tab">
                        <i class="bx bx-bell"></i> Notificaciones <span class="notif-contador"><?= count($avisos) ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_historial" type="button" role="tab">
                        <i class="bx bx-history"></i> Historial
                    </button>
                </li>
            </ul>
            <div class="notif-buscador">
                <i class="bx bx-search"></i>
                <input type="search" class="form-control" id="notif_buscar" placeholder="Buscar notificación...">
            </div>
        </div>

        <div class="tab-content notif-contenido">
            <div class="tab-pane fade show active" id="tab_pendientes" role="tabpanel">
                <?php notificacionesPintarLista($pendientes, 'bx-check-circle', 'Todo al día', 'No tienes tareas pendientes.'); ?>
            </div>
            <div class="tab-pane fade" id="tab_avisos" role="tabpanel">
                <?php notificacionesPintarLista($avisos, 'bx-bell-off', 'Sin notificaciones', 'Aquí verás los avisos que te lleguen.'); ?>
            </div>
            <div class="tab-pane fade" id="tab_historial" role="tabpanel">
                <?php notificacionesPintarLista($historial, 'bx-history', 'Sin historial', 'Las tareas completadas, vencidas o canceladas aparecerán aquí.'); ?>
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
                    var item = $('#notificacion_' + boton.data('id'));
                    item.removeClass('notif-item--no-leida').find('.notif-punto').remove();
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

        // Filtra en el navegador por título, mensaje y registro; oculta los grupos que quedan vacíos.
        $('#notif_buscar').on('input', function () {
            var texto = $(this).val().toLowerCase().trim();
            $('.notif-contenido .tab-pane').each(function () {
                var panel = $(this);
                panel.find('.notif-item').each(function () {
                    $(this).toggleClass('d-none', texto !== '' && String($(this).data('buscar')).indexOf(texto) === -1);
                });
                panel.find('.notif-grupo').each(function () {
                    $(this).toggleClass('d-none', $(this).find('.notif-item:not(.d-none)').length === 0);
                });
                var hayItems = panel.find('.notif-item').length > 0;
                var hayVisibles = panel.find('.notif-item:not(.d-none)').length > 0;
                panel.find('.notif-sin-resultados').toggleClass('d-none', !hayItems || hayVisibles);
            });
        });
    });
</script>
