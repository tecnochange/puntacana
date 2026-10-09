<?php
// "Marcar como completada" del detalle: cierra una tarea para todos sus destinatarios.
require __DIR__ . '/../../app/connect.php';
require_once __DIR__ . '/../../app/models/notificaciones/cargar.php';

use Notificaciones\Aplicacion\CompletarTarea;
use Notificaciones\Infraestructura\Web\Peticion;

header('Content-Type: application/json; charset=utf-8');

// El usuario sale de la sesión, nunca del POST; y el POST debe venir del mismo sitio.
$idEmpleado = (int) ($_SESSION['id_user'] ?? 0);
$idEmpresa = (int) ($_SESSION['id_empresa'] ?? 0);
if ($idEmpleado === 0 || !Peticion::esPostDelMismoSitio()) {
    http_response_code($idEmpleado === 0 ? 401 : 403);
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit;
}

if (!CompletarTarea::ejecutar($connect_admin, $idEmpresa, $idEmpleado, (int) ($_POST['id'] ?? 0))) {
    http_response_code(409);
    echo json_encode(['status' => 'error', 'message' => 'La tarea ya no está abierta o no es tuya.']);
    exit;
}

echo json_encode(['status' => 'success']);
