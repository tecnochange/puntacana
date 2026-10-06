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

<style>
    .notif { max-width: 1100px; margin: 0 auto; padding: 0 12px 40px; }
    .notif-encabezado { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; margin-bottom: 20px; }
    .notif-encabezado h3 { margin: 0; font-weight: 700; }
    .notif-encabezado p { margin: 2px 0 0; color: #6c757d; }

    .notif-resumen { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; margin-bottom: 20px; }
    .notif-tarjeta { background: #fff; border-radius: 14px; padding: 16px 18px; display: flex; align-items: center; gap: 14px;
        box-shadow: 0 2px 10px rgba(16, 24, 40, .06); border: 1px solid #eef0f4; }
    .notif-tarjeta__icono { width: 46px; height: 46px; border-radius: 12px; display: grid; place-items: center; font-size: 22px; flex-shrink: 0; }
    .notif-tarjeta__valor { font-size: 24px; font-weight: 700; line-height: 1; }
    .notif-tarjeta__etiqueta { color: #6c757d; font-size: 13px; margin-top: 4px; }
    .notif-tarjeta--pendientes .notif-tarjeta__icono { background: #e7f0ff; color: #0d6efd; }
    .notif-tarjeta--pronto .notif-tarjeta__icono { background: #fff4e5; color: #f08c00; }
    .notif-tarjeta--sin-leer .notif-tarjeta__icono { background: #f1ecff; color: #7048e8; }

    .notif-panel { background: #fff; border-radius: 16px; box-shadow: 0 2px 10px rgba(16, 24, 40, .06); border: 1px solid #eef0f4; }
    .notif-barra { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; padding: 14px 16px; border-bottom: 1px solid #eef0f4; }
    .notif-pestanas { display: inline-flex; background: #f3f5f9; border-radius: 12px; padding: 4px; gap: 2px; }
    .notif-pestanas .nav-link { border: 0; border-radius: 9px; color: #495057; padding: 7px 14px; font-size: 14px; display: flex; align-items: center; gap: 6px; }
    .notif-pestanas .nav-link.active { background: #fff; color: #0d6efd; box-shadow: 0 1px 4px rgba(16, 24, 40, .12); font-weight: 600; }
    .notif-contador { background: #e9ecef; color: #495057; border-radius: 20px; padding: 0 8px; font-size: 12px; line-height: 20px; }
    .nav-link.active .notif-contador { background: #e7f0ff; color: #0d6efd; }
    .notif-buscador { position: relative; min-width: 240px; }
    .notif-buscador i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #adb5bd; }
    .notif-buscador input { padding-left: 34px; border-radius: 10px; }

    .notif-contenido { padding: 6px 16px 16px; }
    .notif-grupo__titulo { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; color: #868e96; margin: 14px 4px 8px; }
    .notif-item { display: flex; gap: 14px; align-items: flex-start; padding: 14px; border-radius: 12px; border: 1px solid #eef0f4;
        margin-bottom: 8px; transition: background .15s, box-shadow .15s; border-left: 4px solid transparent; }
    .notif-item:hover { box-shadow: 0 4px 14px rgba(16, 24, 40, .08); }
    .notif-item--no-leida { background: #f8faff; border-left-color: var(--notif-color); }
    .notif-item__icono { width: 42px; height: 42px; border-radius: 50%; display: grid; place-items: center; font-size: 20px; flex-shrink: 0;
        color: var(--notif-color); background: #f1f3f5; background: color-mix(in srgb, var(--notif-color) 12%, #fff); }
    .notif-item__cuerpo { flex: 1; min-width: 0; }
    .notif-item__titulo { display: flex; align-items: center; gap: 8px; }
    .notif-item__titulo a { color: #212529; text-decoration: none; }
    .notif-item__titulo a:hover { color: #0d6efd; }
    .notif-item--no-leida .notif-item__titulo a { font-weight: 600; }
    .notif-punto { width: 8px; height: 8px; border-radius: 50%; background: #0d6efd; flex-shrink: 0; }
    .notif-item__mensaje { margin: 4px 0 0; color: #6c757d; font-size: 14px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .notif-item__meta { display: flex; flex-wrap: wrap; align-items: center; gap: 8px 12px; margin-top: 8px; font-size: 12.5px; color: #868e96; }
    .notif-item__meta i { vertical-align: -2px; }
    .notif-chip { border-radius: 20px; padding: 2px 10px; font-weight: 500; }
    .notif-chip--normal { background: #f1f3f5; color: #495057; }
    .notif-chip--pronto { background: #fff4e5; color: #d9480f; }
    .notif-chip--urgente { background: #ffe3e3; color: #c92a2a; }
    .notif-item__acciones { display: flex; gap: 6px; align-items: center; flex-shrink: 0; }

    .notif-vacio { text-align: center; padding: 48px 16px; color: #6c757d; }
    .notif-vacio__icono { width: 76px; height: 76px; border-radius: 50%; background: #e7f0ff; color: #0d6efd; display: grid;
        place-items: center; font-size: 36px; margin: 0 auto 14px; }
    .notif-vacio h5 { color: #343a40; font-weight: 600; margin-bottom: 4px; }
    .notif-sin-resultados { text-align: center; color: #868e96; padding: 28px 0; }

    @media (max-width: 768px) {
        .notif-resumen { grid-template-columns: 1fr; }
        .notif-buscador { min-width: 0; width: 100%; }
        .notif-item { flex-wrap: wrap; }
        .notif-item__acciones { width: 100%; justify-content: flex-end; }
    }
</style>

<div class="notif">
    <div class="notif-encabezado">
        <div>
            <h3>Mis notificaciones</h3>
            <p>Tus tareas y avisos de OKRs y KPIs en un solo lugar.</p>
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
                <?php notificacionesPintarLista($avisos, 'bx-bell-off', 'Sin notificaciones', 'Aquí verás los avisos de tus OKRs y KPIs.'); ?>
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
