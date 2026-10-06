<?php
require_once __DIR__ . '/../../../app/models/notificaciones/cargar.php';
require_once __DIR__ . '/../componentes/presentacion.php';
require_once __DIR__ . '/../componentes/administracion.php';

use Notificaciones\Aplicacion\AdministrarTipos;
use Notificaciones\Dominio\ClaseNotificacion;
use Notificaciones\Dominio\EstadoTipo;

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
$tipos = $esAdministrador ? AdministrarTipos::listar($connect_admin, (int) $user_log['id_empresa']) : [];
$e = 'notificacionesEscapar';

require __DIR__ . '/../componentes/estilos.php';
if (!$esAdministrador) {
    notificacionesPintarSinPermiso();
    return;
}
?>

<div class="notif">
    <?php notificacionesPintarEncabezadoAdministracion('tipos', 'Tipos de notificación', 'Qué se notifica, a quién, por qué canal y con qué texto.'); ?>

    <div class="notif-panel">
        <?php if (!$tipos): ?>
            <div class="notif-vacio">
                <div class="notif-vacio__icono"><i class="bx bx-category"></i></div>
                <h5>No hay tipos</h5>
                <p>Los tipos se crean junto con su código en el sistema.</p>
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
                            <th>Configuración</th>
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
                                    <?php if ($tipo['muestra_en_bandeja']): ?><span class="notif-chip notif-chip--normal" title="Bandeja"><i class="bx bx-bell"></i> Bandeja</span><?php endif; ?>
                                    <?php if ($tipo['envia_correo']): ?><span class="notif-chip notif-chip--normal" title="Correo"><i class="bx bx-envelope"></i> Correo</span><?php endif; ?>
                                </td>
                                <td class="text-muted"><?= $e(notificacionesDescribirCorreo($tipo)) ?></td>
                                <td>
                                    <?php if ($tipo['error_configuracion'] !== null): ?>
                                        <span class="notif-chip notif-chip--urgente" title="<?= $e($tipo['error_configuracion']) ?>"><i class="bx bx-error"></i> Con errores</span>
                                    <?php elseif ($tipo['es_de_la_empresa']): ?>
                                        <span class="notif-chip notif-chip--pronto">Propia de la empresa</span>
                                    <?php else: ?>
                                        <span class="notif-chip notif-chip--normal">Por defecto</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge rounded-pill <?= $estaActivo ? 'bg-success' : 'bg-secondary' ?>"><?= $estaActivo ? 'Activo' : 'Inactivo' ?></span>
                                </td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-primary" href="?pg=notificaciones/administrar/tipo&codigo=<?= urlencode($tipo['codigo']) ?>">
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
    <p class="notif-aviso-alcance mt-3 mb-0">
        <i class="bx bx-info-circle"></i> Lo que guardes aplica solo a tu empresa. La configuración por defecto, común a todas, se cambia desde el sistema.
    </p>
</div>
