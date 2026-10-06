<?php
require __DIR__ . '/../../app/connect.php';
require_once __DIR__ . '/../../app/models/notificaciones/cargar.php';

use Notificaciones\Aplicacion\MarcarLeida;

header('Content-Type: application/json; charset=utf-8');

// El usuario sale de la sesión, nunca del POST.
$idEmpleado = (int) ($_SESSION['id_user'] ?? 0);
$idEmpresa = (int) ($_SESSION['id_empresa'] ?? 0);
if ($idEmpleado === 0 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code($idEmpleado === 0 ? 401 : 405);
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

$marcadas = MarcarLeida::todas($connect_admin, $idEmpresa, $idEmpleado);
echo json_encode(['status' => 'success', 'data' => ['marcadas' => $marcadas]]);
