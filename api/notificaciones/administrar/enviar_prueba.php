<?php
// Genera una notificación de prueba del tipo, solo para el administrador que la pide.
require __DIR__ . '/acceso.php';

use Notificaciones\Aplicacion\AdministrarTipos;
use Notificaciones\Dominio\Excepciones\ErrorNotificacion;

$codigo = isset($_POST['codigo']) ? (string) $_POST['codigo'] : '';
try {
    $idNotificacion = AdministrarTipos::enviarPrueba($connect_admin, $idEmpresa, $codigo, $idEmpleado);
} catch (ErrorNotificacion $error) {
    notificacionesResponder(['status' => 'error', 'message' => 'El tipo tiene errores de configuración: ' . $error->getMessage()]);
}
if ($idNotificacion === null) {
    notificacionesResponder(['status' => 'error', 'message' => 'No se generó la prueba (tipo inexistente o tu usuario no es un destinatario válido).']);
}
notificacionesResponder(['status' => 'success', 'data' => ['id_notificacion' => $idNotificacion]]);
