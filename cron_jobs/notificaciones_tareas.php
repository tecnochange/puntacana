<?php
// Tareas programadas del módulo de Notificaciones (vencimientos de KR y planes, seguimientos
// de KPI). Corre por crontab una vez al día, antes de que abra la ventana de envío:
//   0 6 * * * php /var/www/html/puntacana.goforagile.com/cron_jobs/notificaciones_tareas.php >> /var/log/notificaciones_tareas.log 2>&1
// Los correos los manda después notificaciones_despacho.php.

// Está bajo el docroot: por web no debe hacer nada.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

const NOTIFICACIONES_DOMINIO_PORTAL = 'puntacana.goforagile.com';
const NOTIFICACIONES_NOMBRE_CANDADO = 'notificaciones_tareas';
// Solo la empresa en uso real; las demás son copias heredadas (ver memoria "una sola empresa").
const NOTIFICACIONES_EMPRESAS = [1];

// connect.php arma $url con SERVER_NAME, que no existe por línea de comandos.
$_SERVER['SERVER_NAME'] = NOTIFICACIONES_DOMINIO_PORTAL;
require __DIR__ . '/../app/connect.php';
require_once __DIR__ . '/../app/models/notificaciones/cargar.php';

use Notificaciones\Aplicacion\GenerarTareasProgramadas;

$candado = mysqli_fetch_row(mysqli_query($connect_admin, "SELECT GET_LOCK('" . NOTIFICACIONES_NOMBRE_CANDADO . "', 0)"));
if ((int) $candado[0] !== 1) {
    echo date('Y-m-d H:i:s') . " otra corrida en curso, se omite\n";
    exit;
}

$codigoSalida = 0;
foreach (NOTIFICACIONES_EMPRESAS as $idEmpresa) {
    try {
        $resultado = GenerarTareasProgramadas::ejecutar($connect_admin, $idEmpresa);
        echo date('Y-m-d H:i:s') . " empresa $idEmpresa " . json_encode($resultado) . "\n";
    } catch (Throwable $error) {
        echo date('Y-m-d H:i:s') . " empresa $idEmpresa ERROR " . get_class($error) . ': ' . $error->getMessage() . "\n";
        $codigoSalida = 1;
    }
}

mysqli_query($connect_admin, "SELECT RELEASE_LOCK('" . NOTIFICACIONES_NOMBRE_CANDADO . "')");
exit($codigoSalida);
