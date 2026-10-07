<?php
// Vista previa de un tipo con los valores del formulario (sin guardar).
require __DIR__ . '/acceso.php';

use Notificaciones\Aplicacion\AdministrarTipos;

$idTipo = (int) ($_POST['id'] ?? 0);
notificacionesResponder(['status' => 'success', 'data' => AdministrarTipos::vistaPrevia($connect_admin, $idTipo, $_POST)]);
