<?php
// Entrada común de los endpoints de administración: deja en $idEmpresa y $idEmpleado
// al administrador de la sesión, o responde con error y termina. Nunca toma el
// usuario del POST.
require __DIR__ . '/../../../app/connect.php';
require_once __DIR__ . '/../../../app/models/notificaciones/cargar.php';

use Notificaciones\Aplicacion\VerificarAdministrador;
use Notificaciones\Infraestructura\Web\Peticion;

const NOTIFICACIONES_HTTP_NO_AUTENTICADO = 401;
const NOTIFICACIONES_HTTP_PROHIBIDO = 403;

header('Content-Type: application/json; charset=utf-8');

function notificacionesResponder(array $respuesta, int $codigoHttp = 200): void
{
    http_response_code($codigoHttp);
    echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
    exit;
}

$idEmpleado = (int) ($_SESSION['id_user'] ?? 0);
$idEmpresa = (int) ($_SESSION['id_empresa'] ?? 0);
if ($idEmpleado === 0) {
    notificacionesResponder(['status' => 'error', 'message' => 'Inicia sesión.'], NOTIFICACIONES_HTTP_NO_AUTENTICADO);
}
if (!Peticion::esPostDelMismoSitio()) {
    notificacionesResponder(['status' => 'error', 'message' => 'Petición no permitida.'], NOTIFICACIONES_HTTP_PROHIBIDO);
}
if (!VerificarAdministrador::ejecutar($connect_admin, $idEmpresa, $idEmpleado)) {
    notificacionesResponder(['status' => 'error', 'message' => 'Solo un administrador puede hacer esto.'], NOTIFICACIONES_HTTP_PROHIBIDO);
}
