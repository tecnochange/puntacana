<?php
require_once __DIR__ . '/../../../app/models/notificaciones/cargar.php';
require_once __DIR__ . '/../componentes/presentacion.php';
require_once __DIR__ . '/../componentes/administracion.php';

use Notificaciones\Aplicacion\AdministrarTipos;
use Notificaciones\Dominio\ClaseNotificacion;
use Notificaciones\Dominio\EstadoTipo;
use Notificaciones\Dominio\Plantilla;
use Notificaciones\Dominio\UnidadIntervalo;
use Notificaciones\Infraestructura\Web\Peticion;

$esAdministrador = notificacionesEsAdministrador($connect_admin, $user_log);
$idEmpresa = (int) $user_log['id_empresa'];
$codigo = isset($_GET['codigo']) ? (string) $_GET['codigo'] : '';

$errores = null;
$seGuardo = $esAdministrador && Peticion::esPostDelMismoSitio() && isset($_POST['guardar_tipo']);
if ($seGuardo) {
    $errores = AdministrarTipos::guardar($connect_admin, $idEmpresa, $codigo, $_POST);
}
$tipo = $esAdministrador ? AdministrarTipos::obtener($connect_admin, $idEmpresa, $codigo) : null;
// Si no se pudo guardar, el formulario muestra lo que se escribió, no lo guardado.
$valores = $tipo !== null && $errores ? array_merge($tipo, $_POST) : $tipo;
$vistaPrevia = $tipo !== null ? AdministrarTipos::vistaPrevia($codigo, $valores) : null;

$e = 'notificacionesEscapar';
$campo = function (string $nombre) use ($valores) {
    return isset($valores[$nombre]) ? (string) $valores[$nombre] : '';
};
$marcado = function (string $nombre) use ($valores) {
    return !empty($valores[$nombre]) ? ' checked' : '';
};

require __DIR__ . '/../componentes/estilos.php';
if (!$esAdministrador) {
    notificacionesPintarSinPermiso();
    return;
}
if ($tipo === null) {
    echo '<div class="notif"><div class="notif-panel notif-vacio"><div class="notif-vacio__icono"><i class="bx bx-search-alt"></i></div>'
        . '<h5>Tipo no encontrado</h5><p><a href="?pg=notificaciones/administrar/tipos">Volver a los tipos</a></p></div></div>';
    return;
}
$marcadoresDisponibles = array_merge($tipo['datos_permitidos'], Plantilla::DATOS_BASE);
?>

<div class="notif" style="max-width: 1280px;">
    <?php notificacionesPintarEncabezadoAdministracion('tipos', $tipo['nombre'], 'Configura cómo se genera, a quién llega y cómo se ve esta notificación.'); ?>
    <?php notificacionesPintarResultado($errores, 'Configuración guardada para tu empresa.'); ?>
    <?php if ($tipo['error_configuracion'] !== null && $errores === null): ?>
        <div class="alert alert-warning"><i class="bx bx-error"></i> <?= $e($tipo['error_configuracion']) ?></div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-7">
            <form method="post" action="?pg=notificaciones/administrar/tipo&codigo=<?= urlencode($codigo) ?>" id="form_tipo" class="notif-panel">
                <input type="hidden" name="guardar_tipo" value="1">

                <div class="notif-seccion">
                    <h6>General</h6>
                    <p class="notif-ayuda">Código <span class="notif-codigo"><?= $e($codigo) ?></span> · <?= $tipo['es_de_la_empresa'] ? 'Configuración propia de tu empresa.' : 'Usa la configuración por defecto; al guardar se crea una propia de tu empresa.' ?></p>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label" for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" maxlength="150" required value="<?= $e($campo('nombre')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="estado">Estado</label>
                            <select class="form-select" id="estado" name="estado">
                                <option value="<?= EstadoTipo::ACTIVO ?>"<?= $campo('estado') === EstadoTipo::ACTIVO ? ' selected' : '' ?>>Activo</option>
                                <option value="<?= EstadoTipo::INACTIVO ?>"<?= $campo('estado') === EstadoTipo::INACTIVO ? ' selected' : '' ?>>Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="descripcion">Descripción</label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?= $e($campo('descripcion')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="clase">Clase</label>
                            <select class="form-select" id="clase" name="clase">
                                <option value="<?= ClaseNotificacion::AVISO ?>"<?= $campo('clase') === ClaseNotificacion::AVISO ? ' selected' : '' ?>>Aviso (informativa)</option>
                                <option value="<?= ClaseNotificacion::TAREA ?>"<?= $campo('clase') === ClaseNotificacion::TAREA ? ' selected' : '' ?>>Tarea (vence)</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="notif-seccion">
                    <h6>Canales</h6>
                    <p class="notif-ayuda">Dónde llega la notificación y si quien la provoca también la recibe.</p>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="notif-interruptor form-switch">
                                <input class="form-check-input" type="checkbox" name="muestra_en_bandeja" value="1"<?= $marcado('muestra_en_bandeja') ?>>
                                <span>Bandeja<small>Aparece en Mis notificaciones.</small></span>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="notif-interruptor form-switch">
                                <input class="form-check-input" type="checkbox" name="envia_correo" value="1"<?= $marcado('envia_correo') ?>>
                                <span>Correo<small>Según el modo de operación.</small></span>
                            </label>
                        </div>
                        <div class="col-md-4">
                            <label class="notif-interruptor form-switch">
                                <input class="form-check-input" type="checkbox" name="incluye_autor" value="1"<?= $marcado('incluye_autor') ?>>
                                <span>Incluir al autor<small>Si está entre los destinatarios.</small></span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="notif-seccion">
                    <h6>Correos y recordatorios</h6>
                    <p class="notif-ayuda">Sin intervalo, sale un correo por notificación. Con intervalo, se agrupan en un resumen.</p>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="intervalo_resumen">Resumen cada</label>
                            <input type="number" min="1" class="form-control" id="intervalo_resumen" name="intervalo_resumen" placeholder="Sin resumen" value="<?= $e($campo('intervalo_resumen')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="unidad_intervalo_resumen">Unidad</label>
                            <select class="form-select" id="unidad_intervalo_resumen" name="unidad_intervalo_resumen">
                                <option value="">—</option>
                                <?php foreach ([UnidadIntervalo::HORAS => 'Horas', UnidadIntervalo::DIAS => 'Días', UnidadIntervalo::SEMANAS => 'Semanas'] as $valor => $etiqueta): ?>
                                    <option value="<?= $valor ?>"<?= $campo('unidad_intervalo_resumen') === $valor ? ' selected' : '' ?>><?= $etiqueta ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <label class="form-label" for="dias_antes_de_vencer">Días antes de vencer</label>
                            <input type="number" min="0" class="form-control" id="dias_antes_de_vencer" name="dias_antes_de_vencer" placeholder="Al generarse" value="<?= $e($campo('dias_antes_de_vencer')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="dias_entre_recordatorios">Días entre recordatorios</label>
                            <input type="number" min="0" class="form-control" id="dias_entre_recordatorios" name="dias_entre_recordatorios" placeholder="Sin recordatorios" value="<?= $e($campo('dias_entre_recordatorios')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="maximo_recordatorios">Máximo de recordatorios</label>
                            <input type="number" min="0" class="form-control" id="maximo_recordatorios" name="maximo_recordatorios" placeholder="Sin límite" value="<?= $e($campo('maximo_recordatorios')) ?>">
                        </div>
                    </div>
                </div>

                <div class="notif-seccion">
                    <h6>Plantillas</h6>
                    <p class="notif-ayuda">Haz clic en un marcador para insertarlo en el campo donde estás escribiendo.</p>
                    <div class="notif-marcadores mb-3">
                        <?php foreach ($marcadoresDisponibles as $marcador): ?>
                            <code data-marcador="<?= $e($marcador) ?>">{{<?= $e($marcador) ?>}}</code>
                        <?php endforeach; ?>
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="plantilla_titulo">Título en la bandeja</label>
                            <input type="text" class="form-control notif-plantilla" id="plantilla_titulo" name="plantilla_titulo" maxlength="200" required value="<?= $e($campo('plantilla_titulo')) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="plantilla_mensaje">Mensaje en la bandeja</label>
                            <textarea class="form-control notif-plantilla" id="plantilla_mensaje" name="plantilla_mensaje" rows="2" maxlength="500"><?= $e($campo('plantilla_mensaje')) ?></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="plantilla_asunto_correo">Asunto del correo</label>
                            <input type="text" class="form-control notif-plantilla" id="plantilla_asunto_correo" name="plantilla_asunto_correo" maxlength="200" value="<?= $e($campo('plantilla_asunto_correo')) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="plantilla_cuerpo_correo">Cuerpo del correo (HTML)</label>
                            <textarea class="form-control notif-plantilla font-monospace" id="plantilla_cuerpo_correo" name="plantilla_cuerpo_correo" rows="6" style="font-size: 13px;"><?= $e($campo('plantilla_cuerpo_correo')) ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="notif-seccion">
                    <h6>Apariencia</h6>
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label" for="icono">Ícono (<a href="https://boxicons.com" target="_blank" rel="noopener">Boxicons</a>)</label>
                            <input type="text" class="form-control" id="icono" name="icono" placeholder="bx-bell" value="<?= $e($campo('icono')) ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="color">Color</label>
                            <input type="color" class="form-control form-control-color w-100" id="color" name="color" value="<?= $e(notificacionesColorSeguro($campo('color'))) ?>">
                        </div>
                    </div>
                </div>

                <div class="notif-acciones-pie">
                    <a href="?pg=notificaciones/administrar/tipos" class="btn btn-light">Volver</a>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-save"></i> Guardar</button>
                </div>
            </form>
        </div>

        <div class="col-lg-5">
            <div class="notif-panel notif-previa">
                <div class="notif-previa__bandeja">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="notif-previa__etiqueta mb-0">Vista previa · bandeja</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" id="bt_enviar_prueba">
                            <i class="bx bx-send"></i> Enviarme una prueba
                        </button>
                    </div>
                    <div class="alert alert-danger py-2 small<?= $vistaPrevia['error'] === null ? ' d-none' : '' ?>" id="previa_error"><?= $e($vistaPrevia['error']) ?></div>
                    <div id="resultado_prueba"></div>
                    <article class="notif-item notif-item--no-leida mb-0" id="previa_item" style="--notif-color: <?= notificacionesColorSeguro($campo('color')) ?>">
                        <div class="notif-item__icono"><i class="bx <?= notificacionesIconoSeguro($campo('icono')) ?>" id="previa_icono"></i></div>
                        <div class="notif-item__cuerpo">
                            <div class="notif-item__titulo"><span class="notif-punto"></span><a href="#" id="previa_titulo"><?= $e(isset($vistaPrevia['titulo']) ? $vistaPrevia['titulo'] : '') ?></a></div>
                            <p class="notif-item__mensaje" id="previa_mensaje"><?= $e(isset($vistaPrevia['mensaje']) ? $vistaPrevia['mensaje'] : '') ?></p>
                            <div class="notif-item__meta"><span><i class="bx bx-time-five"></i> hace un momento</span></div>
                        </div>
                    </article>
                </div>
                <div class="px-3 pt-3">
                    <div class="notif-previa__etiqueta">Vista previa · correo</div>
                    <div class="small mb-2"><strong>Asunto:</strong> <span id="previa_asunto"><?= $e(isset($vistaPrevia['asunto']) ? $vistaPrevia['asunto'] : '') ?></span></div>
                </div>
                <iframe id="previa_correo" sandbox="" srcdoc="<?= $e(isset($vistaPrevia['cuerpo_html']) ? $vistaPrevia['cuerpo_html'] : '') ?>"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var codigo = <?= json_encode($codigo) ?>;
        var ultimoCampo = null;

        $('.notif-plantilla').on('focus', function () { ultimoCampo = this; });
        $('.notif-marcadores code').on('click', function () {
            if (!ultimoCampo) { return; }
            var marcador = '{{' + $(this).data('marcador') + '}}';
            var inicio = ultimoCampo.selectionStart, fin = ultimoCampo.selectionEnd;
            ultimoCampo.value = ultimoCampo.value.slice(0, inicio) + marcador + ultimoCampo.value.slice(fin);
            ultimoCampo.focus();
            ultimoCampo.selectionStart = ultimoCampo.selectionEnd = inicio + marcador.length;
            actualizarVistaPrevia();
        });

        var temporizador = null;
        $('#form_tipo').on('input change', function () {
            clearTimeout(temporizador);
            temporizador = setTimeout(actualizarVistaPrevia, 400);
        });

        function actualizarVistaPrevia() {
            var datos = $('#form_tipo').serialize() + '&codigo=' + encodeURIComponent(codigo);
            $.post('api/notificaciones/administrar/vista_previa.php', datos, function (respuesta) {
                var previa = respuesta.data || {};
                $('#previa_error').toggleClass('d-none', !previa.error).text(previa.error || '');
                if (previa.error) { return; }
                $('#previa_titulo').text(previa.titulo);
                $('#previa_mensaje').text(previa.mensaje);
                $('#previa_asunto').text(previa.asunto);
                $('#previa_correo').attr('srcdoc', previa.cuerpo_html);
                $('#previa_item').css('--notif-color', $('#color').val());
                $('#previa_icono').attr('class', 'bx ' + ($('#icono').val() || 'bx-bell'));
            }, 'json');
        }

        $('#bt_enviar_prueba').on('click', function () {
            var boton = $(this).prop('disabled', true);
            $.post('api/notificaciones/administrar/enviar_prueba.php', { codigo: codigo }, function (respuesta) {
                var clase = respuesta.status === 'success' ? 'alert-success' : 'alert-danger';
                var texto = respuesta.status === 'success'
                    ? 'Prueba generada solo para ti. <a href="?pg=notificaciones/bandeja">Ver en mi bandeja</a>. El correo sale según el modo de operación.'
                    : $('<div>').text(respuesta.message).html();
                $('#resultado_prueba').html('<div class="alert ' + clase + ' py-2 small">' + texto + '</div>');
            }, 'json').always(function () { boton.prop('disabled', false); });
        });
    });
</script>
