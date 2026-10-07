<?php
require_once __DIR__ . '/../../../app/models/notificaciones/cargar.php';
require_once __DIR__ . '/../componentes/presentacion.php';
require_once __DIR__ . '/../componentes/administracion.php';

use Notificaciones\Aplicacion\AdministrarTipos;
use Notificaciones\Dominio\ClaseNotificacion;
use Notificaciones\Dominio\EstadoTipo;
use Notificaciones\Infraestructura\Web\Peticion;

const NOTIFICACIONES_UNIDADES_VISIBLES = ['HORAS' => 'horas', 'DIAS' => 'días', 'SEMANAS' => 'semanas'];

/** Cómo sale el correo de un tipo, en palabras. */
function notificacionesDescribirCorreo(array $tipo): string
{
    if (!$tipo['envia_correo']) {
        return 'Sin correo';
    }
    if ($tipo['intervalo_resumen'] === null) {
        return 'Uno por notificación';
    }
    $unidad = NOTIFICACIONES_UNIDADES_VISIBLES[$tipo['unidad_intervalo_resumen']] ?? $tipo['unidad_intervalo_resumen'];
    return "Resumen cada {$tipo['intervalo_resumen']} $unidad";
}

$esAdministrador = notificacionesEsAdministrador($connect_admin, $user_log);

$creacion = null;
if ($esAdministrador && Peticion::esPostDelMismoSitio() && isset($_POST['crear_tipo'])) {
    $creacion = AdministrarTipos::crear($connect_admin, $_POST);
}
$tipos = $esAdministrador ? AdministrarTipos::listar($connect_admin) : [];
$e = 'notificacionesEscapar';
$valorNuevo = function (string $campo) use ($creacion) {
    return $creacion && $creacion['errores'] && isset($_POST[$campo]) ? (string) $_POST[$campo] : '';
};

require __DIR__ . '/../componentes/estilos.php';
if (!$esAdministrador) {
    notificacionesPintarSinPermiso();
    return;
}
if ($creacion && $creacion['id'] !== null) {
    // La página ya empezó a pintarse dentro de index.php: se redirige desde el navegador.
    $destino = '?pg=notificaciones/administrar/tipo&id=' . (int) $creacion['id'];
    echo '<script>location.replace(' . json_encode($destino) . ');</script>';
    return;
}
?>

<div class="notif">
    <?php notificacionesPintarEncabezadoAdministracion('tipos', 'Tipos de notificación', 'Qué se notifica, a quién, por qué canal y con qué texto.'); ?>
    <?php notificacionesPintarResultado($creacion ? $creacion['errores'] : null, ''); ?>

    <div class="d-flex justify-content-end mb-3">
        <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#nuevo_tipo" aria-expanded="<?= $creacion ? 'true' : 'false' ?>">
            <i class="bx bx-plus"></i> Nuevo tipo
        </button>
    </div>

    <div class="collapse<?= $creacion ? ' show' : '' ?> mb-3" id="nuevo_tipo">
        <form method="post" action="?pg=notificaciones/administrar/tipos" class="notif-panel">
            <input type="hidden" name="crear_tipo" value="1">
            <div class="notif-seccion">
                <h6>Nuevo tipo</h6>
                <p class="notif-ayuda">Se crea inactivo y sin implementar. Pásale el código a desarrollo para que lo conecte al evento que lo genera; mientras tanto puedes configurar sus textos.</p>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label" for="nuevo_codigo">Código</label>
                        <input type="text" class="form-control font-monospace" id="nuevo_codigo" name="codigo" maxlength="80" required
                               placeholder="MODULO_ENTIDAD_EVENTO" pattern="[A-Za-z][A-Za-z0-9]*(_[A-Za-z0-9]+)*" value="<?= $e($valorNuevo('codigo')) ?>">
                        <div class="small text-muted mt-1">MAYÚSCULAS_CON_GUION_BAJO. No se puede cambiar después.</div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" for="nuevo_nombre">Nombre</label>
                        <input type="text" class="form-control" id="nuevo_nombre" name="nombre" maxlength="150" required value="<?= $e($valorNuevo('nombre')) ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" for="nuevo_clase">Clase</label>
                        <select class="form-select" id="nuevo_clase" name="clase">
                            <option value="<?= ClaseNotificacion::AVISO ?>">Aviso (informativa)</option>
                            <option value="<?= ClaseNotificacion::TAREA ?>"<?= $valorNuevo('clase') === ClaseNotificacion::TAREA ? ' selected' : '' ?>>Tarea (vence)</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="notif-acciones-pie">
                <button type="button" class="btn btn-light" data-bs-toggle="collapse" data-bs-target="#nuevo_tipo">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bx bx-plus"></i> Crear y configurar</button>
            </div>
        </form>
    </div>

    <div class="notif-panel">
        <?php if (!$tipos): ?>
            <div class="notif-vacio">
                <div class="notif-vacio__icono"><i class="bx bx-category"></i></div>
                <h5>No hay tipos</h5>
                <p>Crea el primero con "Nuevo tipo".</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="notif-tabla">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Clase</th>
                            <th>Canales</th>
                            <th>Correo</th>
                            <th>Implementación</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tipos as $tipo): ?>
                            <?php $estaActivo = $tipo['estado'] === EstadoTipo::ACTIVO; ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="notif-item__icono" style="--notif-color: <?= notificacionesColorSeguro($tipo['color']) ?>; width: 36px; height: 36px; font-size: 18px;">
                                            <i class="bx <?= notificacionesIconoSeguro($tipo['icono']) ?>"></i>
                                        </span>
                                        <div>
                                            <div class="fw-semibold"><?= $e($tipo['nombre']) ?></div>
                                            <span class="notif-codigo"><?= $e($tipo['codigo']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $tipo['clase'] === ClaseNotificacion::TAREA ? 'Tarea' : 'Aviso' ?></td>
                                <td>
                                    <?php if ($tipo['muestra_en_bandeja']): ?><span class="notif-chip notif-chip--normal"><i class="bx bx-bell"></i> Bandeja</span><?php endif; ?>
                                    <?php if ($tipo['envia_correo']): ?><span class="notif-chip notif-chip--normal"><i class="bx bx-envelope"></i> Correo</span><?php endif; ?>
                                </td>
                                <td class="text-muted"><?= $e(notificacionesDescribirCorreo($tipo)) ?></td>
                                <td>
                                    <?php if ($tipo['error_configuracion'] !== null): ?>
                                        <span class="notif-chip notif-chip--urgente" title="<?= $e($tipo['error_configuracion']) ?>"><i class="bx bx-error"></i> Con errores</span>
                                    <?php elseif ($tipo['implementado']): ?>
                                        <span class="notif-chip notif-chip--normal"><i class="bx bx-check"></i> Implementado</span>
                                    <?php else: ?>
                                        <span class="notif-chip notif-chip--pronto" title="Falta conectar el código en el sistema"><i class="bx bx-time-five"></i> Sin implementar</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge rounded-pill <?= $estaActivo ? 'bg-success' : 'bg-secondary' ?>"><?= $estaActivo ? 'Activo' : 'Inactivo' ?></span>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-primary" href="?pg=notificaciones/administrar/tipo&id=<?= (int) $tipo['id'] ?>">
                                        <i class="bx bx-edit-alt"></i> Configurar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
