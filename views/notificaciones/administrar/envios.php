<?php
require_once __DIR__ . '/../../../app/models/notificaciones/cargar.php';
require_once __DIR__ . '/../componentes/presentacion.php';
require_once __DIR__ . '/../componentes/administracion.php';

use Notificaciones\Aplicacion\ConsultarEnvios;
use Notificaciones\Dominio\ResultadoEnvio;

const NOTIFICACIONES_RESULTADOS_VISIBLES = [
    ResultadoEnvio::ENVIADO => ['Enviado', 'bg-success'],
    ResultadoEnvio::FALLIDO => ['Fallido', 'bg-danger'],
    ResultadoEnvio::OMITIDO_MODO => ['Omitido por modo', 'bg-secondary'],
    ResultadoEnvio::OMITIDO_EMPLEADO_INACTIVO => ['Empleado inactivo', 'bg-secondary'],
    ResultadoEnvio::OMITIDO_SIN_CORREO => ['Sin correo', 'bg-warning text-dark'],
    ResultadoEnvio::OMITIDO_CADUCADO => ['Caducado', 'bg-secondary'],
    ResultadoEnvio::OMITIDO_TIPO_INACTIVO => ['Tipo inactivo', 'bg-secondary'],
];

$esAdministrador = notificacionesEsAdministrador($connect_admin, $user_log);
$envios = $esAdministrador ? ConsultarEnvios::ejecutar($connect_admin, (int) $user_log['id_empresa']) : [];
$e = 'notificacionesEscapar';

require __DIR__ . '/../componentes/estilos.php';
if (!$esAdministrador) {
    notificacionesPintarSinPermiso();
    return;
}
?>

<div class="notif" style="max-width: 1280px;">
    <?php notificacionesPintarEncabezadoAdministracion('envios', 'Envíos de correo', 'Qué pasó con cada correo: enviado, fallido u omitido y por qué.'); ?>

    <div class="notif-panel">
        <div class="notif-barra">
            <span class="text-muted small">Últimos <?= count($envios) ?> intentos de tu empresa.</span>
            <div class="d-flex align-items-center gap-2">
                <span id="resultado_proceso" class="small"></span>
                <button type="button" class="btn btn-outline-primary btn-sm" id="bt_procesar_envios">
                    <i class="bx bx-play-circle"></i> Procesar envíos ahora
                </button>
            </div>
        </div>
        <?php if (!$envios): ?>
            <div class="notif-vacio">
                <div class="notif-vacio__icono"><i class="bx bx-envelope"></i></div>
                <h5>Sin envíos todavía</h5>
                <p>Aquí aparecerá cada intento de correo.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive p-2">
                <table class="notif-tabla" id="tabla_envios">
                    <thead>
                        <tr><th>Fecha</th><th>Empleado</th><th>Notificación</th><th>Asunto</th><th>Resultado</th><th>Detalle</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($envios as $envio): ?>
                            <?php $resultado = NOTIFICACIONES_RESULTADOS_VISIBLES[$envio['resultado']] ?? [$envio['resultado'], 'bg-secondary']; ?>
                            <tr>
                                <td class="text-nowrap" data-order="<?= $e($envio['created_at']) ?>"><?= $e((new DateTimeImmutable($envio['created_at']))->format('d/m/Y H:i')) ?></td>
                                <td><?= $e($envio['nombre_empleado']) ?></td>
                                <td><?= $e($envio['titulo']) ?></td>
                                <td class="text-muted"><?= $e($envio['asunto']) ?></td>
                                <td><span class="badge rounded-pill <?= $resultado[1] ?>"><?= $e($resultado[0]) ?></span></td>
                                <td class="small text-muted"><?= $e($envio['mensaje_error'] !== null ? $envio['mensaje_error'] : (string) $envio['id_mensaje_proveedor']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <p class="notif-aviso-alcance mt-3 mb-0">
        <i class="bx bx-info-circle"></i> "Procesar envíos ahora" hace lo mismo que el proceso automático, solo para tu empresa, y respeta el modo de operación y la ventana de envío.
    </p>
</div>

<script>
    $(document).ready(function () {
        if ($.fn.DataTable && $('#tabla_envios').length) {
            $('#tabla_envios').DataTable({ order: [[0, 'desc']], pageLength: 25, language: { search: 'Buscar:', lengthMenu: 'Mostrar _MENU_', info: '_START_ a _END_ de _TOTAL_', paginate: { previous: 'Anterior', next: 'Siguiente' }, zeroRecords: 'Sin resultados' } });
        }
        $('#bt_procesar_envios').on('click', function () {
            var boton = $(this).prop('disabled', true);
            $('#resultado_proceso').text('Procesando...');
            $.post('api/notificaciones/administrar/procesar_envios.php', {}, function (respuesta) {
                if (respuesta.status !== 'success') {
                    $('#resultado_proceso').text(respuesta.message);
                    return;
                }
                var t = respuesta.data;
                $('#resultado_proceso').text('Enviados ' + t.enviados + ' · fallidos ' + t.fallidos + ' · omitidos ' + t.omitidos + '. Recargando...');
                setTimeout(function () { location.reload(); }, 1500);
            }, 'json').always(function () { boton.prop('disabled', false); });
        });
    });
</script>
