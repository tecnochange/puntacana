<?php
require_once __DIR__ . '/../../app/models/notificaciones/cargar.php';
require_once __DIR__ . '/componentes/presentacion.php';

use Notificaciones\Aplicacion\MarcarNotificacionLeida;

// Solo la devuelve si quien la pide es destinatario; abrirla la marca leída.
$notificacion = MarcarNotificacionLeida::ejecutar($connect_admin, (int) $user_log['id_empresa'], (int) $user_log['id'], (int) ($_GET['id'] ?? 0));

$e = 'notificacionesEscapar';
$fecha = function (?string $valor, string $formato) {
    return $valor ? (new DateTimeImmutable($valor))->format($formato) : '';
};
$registro = $notificacion ? notificacionesRegistroVisible($notificacion['tipo_registro'], $notificacion['id_registro']) : '';
?>

<?php require __DIR__ . '/componentes/estilos.php'; ?>
<div class="notif">
    <div class="mb-3">
        <a href="?pg=notificaciones/bandeja" class="btn btn-sm btn-outline-secondary"><i class="bx bx-arrow-back"></i> Volver a mis notificaciones</a>
    </div>

    <?php if ($notificacion === null): ?>
        <div class="card">
            <div class="card-body text-center text-muted py-5">
                La notificación no existe o no está dirigida a ti.
            </div>
        </div>
    <?php else: ?>
        <?php [$estadoTexto, $estadoClase] = notificacionesEstadoVisible($notificacion['estado']); ?>
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bx <?= notificacionesIconoSeguro($notificacion['icono']) ?> fs-3" style="color: <?= notificacionesColorSeguro($notificacion['color']) ?>"></i>
                <h4 class="mb-0 flex-grow-1"><?= $e($notificacion['titulo']) ?></h4>
                <span class="badge <?= $estadoClase ?>"><?= $e($estadoTexto) ?></span>
            </div>
            <div class="card-body">
                <?php if ($notificacion['mensaje'] !== null && $notificacion['mensaje'] !== ''): ?>
                    <p><?= nl2br($e($notificacion['mensaje'])) ?></p>
                <?php endif; ?>

                <dl class="row small mb-0">
                    <dt class="col-sm-3">Tipo</dt>
                    <dd class="col-sm-9"><?= $e($notificacion['nombre_tipo']) ?></dd>
                    <?php if ($registro !== ''): ?>
                        <dt class="col-sm-3">Registro</dt>
                        <dd class="col-sm-9"><?= $e($registro) ?></dd>
                    <?php endif; ?>
                    <dt class="col-sm-3">Recibida</dt>
                    <dd class="col-sm-9"><?= $e($fecha($notificacion['created_at'], 'd/m/Y H:i')) ?></dd>
                    <?php if ($notificacion['fecha_vencimiento']): ?>
                        <dt class="col-sm-3">Vence</dt>
                        <dd class="col-sm-9"><?= $e($fecha($notificacion['fecha_vencimiento'], 'd/m/Y')) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
            <?php if ($notificacion['url']): ?>
                <div class="card-footer text-end">
                    <a href="<?= $e($notificacion['url']) ?>" class="btn btn-primary">Ir al registro</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
