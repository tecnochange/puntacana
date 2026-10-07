<?php
require_once __DIR__ . '/../../../app/models/notificaciones/cargar.php';
require_once __DIR__ . '/../componentes/presentacion.php';
require_once __DIR__ . '/../componentes/administracion.php';

use Notificaciones\Aplicacion\AdministrarConfiguracion;
use Notificaciones\Dominio\ModoOperacion;
use Notificaciones\Infraestructura\Web\Peticion;

const NOTIFICACIONES_MODOS_VISIBLES = [
    ModoOperacion::APAGADO => ['Apagado', 'No se envía ningún correo. Las notificaciones siguen llegando a la bandeja.'],
    ModoOperacion::SOLO_BANDEJA => ['Solo bandeja', 'Los correos pendientes se descartan y quedan registrados como omitidos.'],
    ModoOperacion::PRUEBA => ['Prueba', 'Solo reciben correo los empleados de prueba (o el correo de desvío, si lo indicas).'],
    ModoOperacion::ACTIVO => ['Activo', 'Los correos salen a todos los destinatarios.'],
];
const NOTIFICACIONES_DIAS_SEMANA = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];

$esAdministrador = notificacionesEsAdministrador($connect_admin, $user_log);
$idEmpresa = (int) $user_log['id_empresa'];

$errores = null;
if ($esAdministrador && Peticion::esPostDelMismoSitio() && isset($_POST['guardar_configuracion'])) {
    $errores = AdministrarConfiguracion::guardar($connect_admin, $idEmpresa, $_POST);
}
$configuracion = $esAdministrador ? AdministrarConfiguracion::obtener($connect_admin, $idEmpresa) : [];

$e = 'notificacionesEscapar';
$valor = function (string $clave) use ($configuracion, $errores) {
    // Si no se pudo guardar, se muestra lo que se escribió.
    if ($errores && isset($_POST[$clave])) {
        return (string) $_POST[$clave];
    }
    return isset($configuracion[$clave]) ? (string) $configuracion[$clave] : '';
};
$ventana = explode('-', $valor('ventana_envio'));
$inicioVentana = $errores && isset($_POST['inicio_ventana']) ? (int) $_POST['inicio_ventana'] : (int) $ventana[0];
$finVentana = $errores && isset($_POST['fin_ventana']) ? (int) $_POST['fin_ventana'] : (isset($ventana[1]) ? (int) $ventana[1] : 0);

require __DIR__ . '/../componentes/estilos.php';
if (!$esAdministrador) {
    notificacionesPintarSinPermiso();
    return;
}
?>

<div class="notif">
    <?php notificacionesPintarEncabezadoAdministracion('configuracion', 'Configuración de notificaciones', 'Cuándo y a quién se envían los correos.'); ?>
    <?php notificacionesPintarResultado($errores, 'Configuración guardada.'); ?>

    <form method="post" action="?pg=notificaciones/administrar/configuracion" class="notif-panel">
        <input type="hidden" name="guardar_configuracion" value="1">

        <div class="notif-seccion">
            <h6>Modo de operación</h6>
            <p class="notif-ayuda">Controla el envío de correos. La bandeja funciona en todos los modos.</p>
            <div class="row g-3">
                <?php foreach (NOTIFICACIONES_MODOS_VISIBLES as $modo => $descripcion): ?>
                    <div class="col-md-6">
                        <label class="notif-interruptor">
                            <input class="form-check-input" type="radio" name="modo_operacion" value="<?= $modo ?>"<?= $valor('modo_operacion') === $modo ? ' checked' : '' ?>>
                            <span><strong><?= $e($descripcion[0]) ?></strong><small><?= $e($descripcion[1]) ?></small></span>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="notif-seccion">
            <h6>Pruebas</h6>
            <p class="notif-ayuda">Aplica solo en modo Prueba.</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="ids_empleados_prueba">Ids de empleados de prueba</label>
                    <input type="text" class="form-control" id="ids_empleados_prueba" name="ids_empleados_prueba" placeholder="Ej.: 45, 78" value="<?= $e($valor('ids_empleados_prueba')) ?>">
                    <?php if (!empty($configuracion['empleados_prueba'])): ?>
                        <div class="small text-muted mt-1">
                            <?php foreach ($configuracion['empleados_prueba'] as $id => $nombre): ?>
                                <span class="notif-chip notif-chip--normal me-1"><?= (int) $id ?> · <?= $e($nombre) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="correo_desvio_prueba">Correo de desvío</label>
                    <input type="email" class="form-control" id="correo_desvio_prueba" name="correo_desvio_prueba" placeholder="Vacío = correo de cada empleado" value="<?= $e($valor('correo_desvio_prueba')) ?>">
                    <div class="small text-muted mt-1">Todo correo de prueba llega aquí, con el asunto marcado [PRUEBA].</div>
                </div>
            </div>
        </div>

        <div class="notif-seccion">
            <h6>Horarios</h6>
            <p class="notif-ayuda">Hora local de la plataforma.</p>
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label" for="inicio_ventana">Enviar desde</label>
                    <select class="form-select" id="inicio_ventana" name="inicio_ventana">
                        <?php for ($hora = 0; $hora <= 23; $hora++): ?>
                            <option value="<?= $hora ?>"<?= $hora === $inicioVentana ? ' selected' : '' ?>><?= sprintf('%02d:00', $hora) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="fin_ventana">Hasta</label>
                    <select class="form-select" id="fin_ventana" name="fin_ventana">
                        <?php for ($hora = 1; $hora <= 24; $hora++): ?>
                            <option value="<?= $hora ?>"<?= $hora === $finVentana ? ' selected' : '' ?>><?= sprintf('%02d:00', $hora) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="hora_resumen">Hora de los resúmenes</label>
                    <select class="form-select" id="hora_resumen" name="hora_resumen">
                        <?php for ($hora = 0; $hora <= 23; $hora++): ?>
                            <option value="<?= $hora ?>"<?= (string) $hora === $valor('hora_resumen') ? ' selected' : '' ?>><?= sprintf('%02d:00', $hora) ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label" for="dia_semana_resumen">Día de los resúmenes semanales</label>
                    <select class="form-select" id="dia_semana_resumen" name="dia_semana_resumen">
                        <?php foreach (NOTIFICACIONES_DIAS_SEMANA as $dia => $nombreDia): ?>
                            <option value="<?= $dia ?>"<?= (string) $dia === $valor('dia_semana_resumen') ? ' selected' : '' ?>><?= $nombreDia ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="notif-acciones-pie">
            <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Guardar</button>
        </div>
    </form>
</div>
