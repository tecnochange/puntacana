<?php
// Corre ahora la generación de tareas programadas (lo mismo que el cron diario),
// solo para la empresa del administrador. Se puede repetir: no duplica tareas.
require __DIR__ . '/acceso.php';

use Notificaciones\Aplicacion\GenerarTareasProgramadas;

$totales = GenerarTareasProgramadas::ejecutar($connect_admin, $idEmpresa);
notificacionesResponder(['status' => 'success', 'data' => $totales]);
