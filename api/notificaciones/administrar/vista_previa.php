<?php
// Vista previa de un tipo con los valores del formulario (sin guardar).
require __DIR__ . '/acceso.php';

use Notificaciones\Aplicacion\AdministrarTipos;

$codigo = isset($_POST['codigo']) ? (string) $_POST['codigo'] : '';
notificacionesResponder(['status' => 'success', 'data' => AdministrarTipos::vistaPrevia($codigo, $_POST)]);
