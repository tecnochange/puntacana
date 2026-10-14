<?php
// Campana de la cabecera: lleva a la bandeja y muestra cuántas notificaciones hay sin leer.
// Se incluye en views/layouts/header.php (escritorio y móvil). Si el módulo falla, la
// campana se muestra sin número: la cabecera de todo el portal no puede romperse por esto.
require_once __DIR__ . '/../../../app/models/notificaciones/cargar.php';

if (!function_exists('notificacionesSinLeerDeCabecera')) {
    function notificacionesSinLeerDeCabecera(): int
    {
        static $total = null;
        if ($total === null) {
            global $connect_admin, $user_log;
            try {
                $total = \Notificaciones\Aplicacion\ContarSinLeer::ejecutar($connect_admin, (int) $user_log['id_empresa'], (int) $user_log['id']);
            } catch (Throwable $error) {
                error_log('[notificaciones] campana: ' . $error->getMessage());
                $total = 0;
            }
        }
        return $total;
    }
}

$notificacionesSinLeer = notificacionesSinLeerDeCabecera();
$notificacionesTitulo = $notificacionesSinLeer === 0 ? 'Mis notificaciones' : ($notificacionesSinLeer === 1 ? '1 notificación sin leer' : $notificacionesSinLeer . ' notificaciones sin leer');
?>
<?php if (!defined('NOTIFICACIONES_CAMPANA_ESTILOS')): define('NOTIFICACIONES_CAMPANA_ESTILOS', true); ?>
    <style>
        .notif-campana { position: relative; display: inline-block; }
        .notif-campana__numero { position: absolute; top: -0.25rem; right: -0.375rem; min-width: 1.125rem; height: 1.125rem; padding: 0 0.3125rem; border-radius: 0.5625rem;
            background: #e03131; color: #fff; font-size: 0.6875rem; font-weight: 700; line-height: 1.125rem; text-align: center; box-shadow: 0 0 0 0.125rem #fff; pointer-events: none; }
    </style>
<?php endif; ?>
<a href="<?php echo $url; ?>?pg=notificaciones/bandeja" class="notif-campana" aria-label="<?php echo htmlspecialchars($notificacionesTitulo, ENT_QUOTES, 'UTF-8'); ?>">
    <i class="bx bx-bell icons_menu_h" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?php echo htmlspecialchars($notificacionesTitulo, ENT_QUOTES, 'UTF-8'); ?>"></i>
    <?php if ($notificacionesSinLeer > 0): ?>
        <span class="notif-campana__numero"><?php echo $notificacionesSinLeer > 99 ? '99+' : $notificacionesSinLeer; ?></span>
    <?php endif; ?>
</a>
