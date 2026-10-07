<?php
// Genera una notificación de prueba del tipo, solo para el administrador que la pide.
require __DIR__ . '/acceso.php';

use Notificaciones\Aplicacion\AdministrarTipos;
use Notificaciones\Dominio\Excepciones\ErrorNotificacion;

$idTipo = (int) ($_POST['id'] ?? 0);
try {
    $idNotificacion = AdministrarTipos::enviarPrueba($connect_admin, $idEmpresa, $idTipo, $idEmpleado);
} catch (ErrorNotificacion $error) {
    notificacionesResponder(['status' => 'error', 'message' => $error->getMessage()]);
}
if ($idNotificacion === null) {
    notificacionesResponder(['status' => 'error', 'message' => 'No se generó la prueba (tipo inexistente o tu usuario no es un destinatario válido).']);
}
notificacionesResponder(['status' => 'success', 'data' => ['id_notificacion' => $idNotificacion]]);
