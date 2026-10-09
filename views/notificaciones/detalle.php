<?php
require_once __DIR__ . '/../../app/models/notificaciones/cargar.php';
require_once __DIR__ . '/componentes/presentacion.php';

use Notificaciones\Aplicacion\VerNotificacion;
use Notificaciones\Dominio\ClaseNotificacion;
use Notificaciones\Dominio\EstadoNotificacion;

/**
 * Hitos de la notificación, del más antiguo al más reciente. El vencimiento
 * pendiente va al final como hito futuro.
 */
function notificacionesActividad(array $vista, DateTimeImmutable $ahora): array
{
    $n = $vista['notificacion'];
    $actividad = [['fecha' => new DateTimeImmutable($n['created_at']), 'icono' => 'bx-bell', 'clase' => 'primaria', 'texto' => 'Recibida en tu bandeja']];
    foreach ($vista['correos_enviados'] as $indice => $fechaEnvio) {
        $texto = $indice === 0 ? 'Te enviamos un correo' : 'Te enviamos un recordatorio por correo';
        $actividad[] = ['fecha' => new DateTimeImmutable($fechaEnvio), 'icono' => 'bx-envelope', 'clase' => 'neutra', 'texto' => $texto];
    }
    $fechaLectura = $n['fecha_lectura'] ? new DateTimeImmutable($n['fecha_lectura']) : $ahora;
    $actividad[] = ['fecha' => $fechaLectura, 'icono' => 'bx-show', 'clase' => 'neutra', 'texto' => $n['fecha_lectura'] ? 'La leíste' : 'La estás leyendo ahora'];
    if ($n['fecha_cierre']) {
        [$estadoTexto] = notificacionesEstadoVisible($n['estado']);
        $actividad[] = ['fecha' => new DateTimeImmutable($n['fecha_cierre']), 'icono' => 'bx-flag', 'clase' => $n['estado'] === EstadoNotificacion::COMPLETADA ? 'exito' : 'peligro', 'texto' => 'Quedó ' . mb_strtolower($estadoTexto)];
    }
    usort($actividad, function (array $a, array $b) {
        return $a['fecha'] <=> $b['fecha'];
    });
    if ($n['fecha_vencimiento'] && $n['estado'] === EstadoNotificacion::ABIERTA) {
        $actividad[] = ['fecha' => new DateTimeImmutable($n['fecha_vencimiento']), 'icono' => 'bx-calendar-exclamation', 'clase' => 'futura', 'texto' => 'Fecha de vencimiento', 'solo_dia' => true];
    }
    return $actividad;
}

/** Avance del plazo entre que llegó y su vencimiento: porcentaje, días que faltan y su color. */
function notificacionesPlazo(array $n, DateTimeImmutable $ahora): ?array
{
    if (!$n['fecha_vencimiento']) {
        return null;
    }
    $recibida = new DateTimeImmutable($n['created_at']);
    $vence = (new DateTimeImmutable($n['fecha_vencimiento']))->setTime(23, 59, 59);
    $total = max(1, $vence->getTimestamp() - $recibida->getTimestamp());
    $transcurrido = $ahora->getTimestamp() - $recibida->getTimestamp();
    $dias = notificacionesDiasEntre($ahora, $vence);

    if ($dias <= NOTIFICACIONES_DIAS_URGENTE) {
        $tono = 'urgente';
    } elseif ($dias <= NOTIFICACIONES_DIAS_VENCE_PRONTO) {
        $tono = 'pronto';
    } else {
        $tono = 'normal';
    }
    return [
        'porcentaje' => (int) round(min(100, max(0, $transcurrido * 100 / $total))),
        'dias' => $dias,
        'tono' => $tono,
        'recibida' => $recibida,
        'vence' => $vence,
    ];
}

function notificacionesTextoPlazo(int $dias): string
{
    if ($dias < 0) {
        return -$dias === 1 ? 'Venció ayer' : 'Venció hace ' . (-$dias) . ' días';
    }
    if ($dias === 0) {
        return 'Vence hoy';
    }
    return $dias === 1 ? 'Vence mañana' : "Faltan $dias días";
}

$vista = VerNotificacion::ejecutar($connect_admin, (int) $user_log['id_empresa'], (int) $user_log['id'], (int) ($_GET['id'] ?? 0));
$ahora = new DateTimeImmutable();
$e = 'notificacionesEscapar';
?>

<?php require __DIR__ . '/componentes/estilos.php'; ?>
<div class="notif">
    <div class="notif-detalle-nav">
        <a href="?pg=notificaciones/bandeja" class="btn btn-sm btn-light"><i class="bx bx-arrow-back"></i> Mis notificaciones</a>
        <?php if ($vista !== null): ?>
            <div class="btn-group" role="group" aria-label="Navegar entre notificaciones">
                <?php if ($vista['id_mas_reciente']): ?>
                    <a class="btn btn-sm btn-light" href="?pg=notificaciones/detalle&id=<?= (int) $vista['id_mas_reciente'] ?>" title="Notificación más reciente"><i class="bx bx-chevron-left"></i> Más reciente</a>
                <?php else: ?>
                    <span class="btn btn-sm btn-light disabled"><i class="bx bx-chevron-left"></i> Más reciente</span>
                <?php endif; ?>
                <?php if ($vista['id_mas_antigua']): ?>
                    <a class="btn btn-sm btn-light" href="?pg=notificaciones/detalle&id=<?= (int) $vista['id_mas_antigua'] ?>" title="Notificación más antigua">Más antigua <i class="bx bx-chevron-right"></i></a>
                <?php else: ?>
                    <span class="btn btn-sm btn-light disabled">Más antigua <i class="bx bx-chevron-right"></i></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($vista === null): ?>
        <div class="notif-panel notif-vacio">
            <div class="notif-vacio__icono"><i class="bx bx-search-alt"></i></div>
            <h5>No encontramos esta notificación</h5>
            <p>No existe o no está dirigida a ti.</p>
            <a href="?pg=notificaciones/bandeja" class="btn btn-primary btn-sm mt-2">Ir a mis notificaciones</a>
        </div>
    <?php else: ?>
        <?php
        $n = $vista['notificacion'];
        $color = notificacionesColorSeguro($n['color']);
        [$estadoTexto, $estadoClase] = notificacionesEstadoVisible($n['estado']);
        [$claseTexto, $claseIcono] = notificacionesClaseVisible($n['clase']);
        $recibida = new DateTimeImmutable($n['created_at']);
        $plazo = notificacionesPlazo($n, $ahora);
        $estaAbierta = $n['estado'] === EstadoNotificacion::ABIERTA;
        $registro = notificacionesRegistroVisible($n['tipo_registro'], $n['id_registro']);
        $tieneMensaje = $n['mensaje'] !== null && $n['mensaje'] !== '';
        ?>
        <section class="notif-detalle-cabecera" style="--notif-color: <?= $color ?>">
            <div class="notif-detalle-cabecera__icono"><i class="bx <?= notificacionesIconoSeguro($n['icono']) ?>"></i></div>
            <div class="notif-detalle-cabecera__cuerpo">
                <div class="notif-detalle-cabecera__tipo">
                    <?= $e($n['nombre_tipo']) ?> <span class="notif-separador">·</span> <i class="bx <?= $claseIcono ?>"></i> <?= $e($claseTexto) ?>
                </div>
                <h3 class="notif-detalle-cabecera__titulo"><?= $e($n['titulo']) ?></h3>
                <div class="notif-detalle-cabecera__meta">
                    <span class="badge rounded-pill <?= $estadoClase ?>"><?= $e($estadoTexto) ?></span>
                    <?php if ($plazo && $estaAbierta): ?>
                        <span class="notif-chip notif-chip--<?= $plazo['tono'] ?>"><i class="bx bx-calendar-exclamation"></i> <?= $e(notificacionesTextoPlazo($plazo['dias'])) ?></span>
                    <?php endif; ?>
                    <span title="<?= $recibida->format('d/m/Y H:i') ?>"><i class="bx bx-time-five"></i> Recibida <?= $e(notificacionesTiempoRelativo($recibida, $ahora)) ?></span>
                    <?php if ($vista['nombre_autor']): ?>
                        <span><i class="bx bx-user"></i> De <?= $e($vista['nombre_autor']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <div class="row g-3">
            <div class="col-lg-8">
                <div class="notif-panel notif-detalle-bloque">
                    <div class="notif-previa__etiqueta">Mensaje</div>
                    <?php if ($tieneMensaje): ?>
                        <p class="notif-detalle-mensaje"><?= nl2br($e($n['mensaje'])) ?></p>
                    <?php else: ?>
                        <p class="notif-detalle-mensaje text-muted">Esta notificación no trae más detalle que su título.</p>
                    <?php endif; ?>
                    <?php if ($n['url']): ?>
                        <a href="<?= $e($n['url']) ?>" class="notif-detalle-enlace">
                            <span class="notif-detalle-enlace__icono"><i class="bx bx-file"></i></span>
                            <span class="flex-grow-1">
                                <strong>Abrir el registro relacionado</strong>
                                <small><?= $registro !== '' ? $e($registro) : 'Te lleva a la pantalla donde se originó' ?></small>
                            </span>
                            <i class="bx bx-right-arrow-alt fs-4"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <div class="notif-panel notif-detalle-bloque">
                    <div class="notif-previa__etiqueta">Actividad</div>
                    <ol class="notif-linea">
                        <?php foreach (notificacionesActividad($vista, $ahora) as $hito): ?>
                            <li class="notif-linea__hito notif-linea__hito--<?= $hito['clase'] ?>">
                                <span class="notif-linea__punto"><i class="bx <?= $hito['icono'] ?>"></i></span>
                                <div>
                                    <div class="notif-linea__texto"><?= $e($hito['texto']) ?></div>
                                    <div class="notif-linea__fecha"><?= $hito['fecha']->format(empty($hito['solo_dia']) ? 'd/m/Y H:i' : 'd/m/Y') ?></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>
            </div>

            <div class="col-lg-4">
                <?php if ($plazo): ?>
                    <div class="notif-panel notif-detalle-bloque">
                        <div class="notif-previa__etiqueta">Plazo</div>
                        <?php if ($estaAbierta): ?>
                            <div class="notif-plazo__valor notif-plazo__valor--<?= $plazo['tono'] ?>"><?= $e(notificacionesTextoPlazo($plazo['dias'])) ?></div>
                            <div class="notif-plazo__barra"><span class="notif-plazo__avance--<?= $plazo['tono'] ?>" style="width: <?= $plazo['porcentaje'] ?>%"></span></div>
                        <?php else: ?>
                            <div class="notif-plazo__valor mb-3">Cerrada</div>
                        <?php endif; ?>
                        <div class="notif-plazo__extremos">
                            <span>Recibida<br><strong><?= $plazo['recibida']->format('d/m/Y') ?></strong></span>
                            <span class="text-end">Vence<br><strong><?= $plazo['vence']->format('d/m/Y') ?></strong></span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="notif-panel notif-detalle-bloque">
                    <div class="notif-previa__etiqueta">Detalles</div>
                    <dl class="notif-datos">
                        <dt>Tipo</dt>
                        <dd><?= $e($n['nombre_tipo']) ?></dd>
                        <dt>Clase</dt>
                        <dd><?= $e($claseTexto) ?><?= $n['clase'] === ClaseNotificacion::TAREA ? ' · requiere una acción' : ' · informativa' ?></dd>
                        <dt>Estado</dt>
                        <dd><?= $e($estadoTexto) ?></dd>
                        <?php if ($registro !== ''): ?>
                            <dt>Registro</dt>
                            <dd><?= $e($registro) ?></dd>
                        <?php endif; ?>
                        <?php if ($vista['nombre_autor']): ?>
                            <dt>Enviada por</dt>
                            <dd><?= $e($vista['nombre_autor']) ?></dd>
                        <?php endif; ?>
                        <dt>Recibida</dt>
                        <dd><?= $recibida->format('d/m/Y H:i') ?></dd>
                        <?php if ($n['fecha_vencimiento']): ?>
                            <dt>Vence</dt>
                            <dd><?= (new DateTimeImmutable($n['fecha_vencimiento']))->format('d/m/Y') ?></dd>
                        <?php endif; ?>
                        <?php if ($n['fecha_cierre']): ?>
                            <dt>Cerrada</dt>
                            <dd><?= (new DateTimeImmutable($n['fecha_cierre']))->format('d/m/Y H:i') ?></dd>
                        <?php endif; ?>
                        <dt>Correos</dt>
                        <dd><?= count($vista['correos_enviados']) === 0 ? 'Ninguno' : count($vista['correos_enviados']) . (count($vista['correos_enviados']) === 1 ? ' enviado' : ' enviados') ?></dd>
                        <dt>Referencia</dt>
                        <dd>#<?= (int) $n['id'] ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
