<?php
// Despacho de correos del módulo de Notificaciones. Corre por crontab cada 5 minutos:
//   */5 * * * * php /var/www/html/puntacana.goforagile.com/cron_jobs/notificaciones_despacho.php >> /var/log/notificaciones_despacho.log 2>&1
// Qué envía y a quién lo decide Notificaciones_Configuracion (modo, lista_prueba, ventana_envio, hora_resumen).

// Está bajo el docroot: por web no debe hacer nada.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

const NOTIFICACIONES_DOMINIO_PORTAL = 'puntacana.goforagile.com';
const NOTIFICACIONES_NOMBRE_CANDADO = 'notificaciones_despacho';

// connect.php arma $url con SERVER_NAME, que no existe por línea de comandos.
$_SERVER['SERVER_NAME'] = NOTIFICACIONES_DOMINIO_PORTAL;
require __DIR__ . '/../app/connect.php';
require_once __DIR__ . '/../app/models/notificaciones/cargar.php';

use Notificaciones\Aplicacion\DespacharCorreos;
use Notificaciones\Infraestructura\Correo\CorreoBrevo;

// Si la corrida anterior sigue viva (p. ej. Brevo lento), esta no arranca.
$candado = mysqli_fetch_row(mysqli_query($connect_admin, "SELECT GET_LOCK('" . NOTIFICACIONES_NOMBRE_CANDADO . "', 0)"));
if ((int) $candado[0] !== 1) {
    echo date('Y-m-d H:i:s') . " otra corrida en curso, se omite\n";
    exit;
}

$codigoSalida = 0;
try {
    $resultado = DespacharCorreos::ejecutar($connect_admin, new CorreoBrevo(), $url);
    echo date('Y-m-d H:i:s') . ' ' . json_encode($resultado) . "\n";
} catch (Throwable $error) {
    echo date('Y-m-d H:i:s') . ' ERROR ' . get_class($error) . ': ' . $error->getMessage() . "\n";
    $codigoSalida = 1;
}

mysqli_query($connect_admin, "SELECT RELEASE_LOCK('" . NOTIFICACIONES_NOMBRE_CANDADO . "')");
exit($codigoSalida);
