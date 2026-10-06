<?php
// Enlace de los correos y de la bandeja: valida la sesión, marca la notificación
// como leída y redirige al registro. Si no es tuya, te deja en tu bandeja.
require __DIR__ . '/../../app/connect.php';
require_once __DIR__ . '/../../app/models/notificaciones/cargar.php';

use Notificaciones\Aplicacion\MarcarNotificacionLeida;

const NOTIFICACIONES_RUTA_BANDEJA = '?pg=notificaciones/bandeja';

$idEmpleado = (int) ($_SESSION['id_user'] ?? 0);
$idEmpresa = (int) ($_SESSION['id_empresa'] ?? 0);
if ($idEmpleado === 0) {
    header('Location: ' . $url . 'log.php');
    exit;
}

$notificacion = MarcarNotificacionLeida::ejecutar($connect_admin, $idEmpresa, $idEmpleado, (int) ($_GET['id'] ?? 0));

// GenerarNotificacion solo acepta urls '?pg=...', así que el destino siempre es del portal.
$destino = ($notificacion['url'] ?? '') ?: NOTIFICACIONES_RUTA_BANDEJA;
header('Location: ' . $url . $destino);
exit;
