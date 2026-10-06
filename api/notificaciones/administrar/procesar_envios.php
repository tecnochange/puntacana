<?php
// Corre el despacho de correos ahora, solo para la empresa del administrador.
// Respeta modo_operacion y ventana_envio igual que el cron.
require __DIR__ . '/acceso.php';

use Notificaciones\Aplicacion\DespacharCorreos;
use Notificaciones\Infraestructura\Correo\CorreoBrevo;

$totales = DespacharCorreos::ejecutar($connect_admin, new CorreoBrevo(), $url, null, $idEmpresa);
notificacionesResponder(['status' => 'success', 'data' => $totales]);
