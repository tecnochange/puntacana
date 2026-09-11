<?php

?>
<div class="modal fade bd-example-modal-xl" id="modal-Comentario" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title modal-title-primary">Comentar Lección Aprendida</h4>
                <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fas fa-times pull-right"></i></button>
            </div>

            <form action="<?php echo $url; ?>?pg=lecciones_aprendidas/detalle/crear_comentario" method="post" id="comenta-form" autocomplete="off">
                <input type="hidden" name="csrf" value="<?php echo $_SESSION['token']; ?>">
                <div class="modal-body" id="cont_modal_comentar">
                    <div class="form-group">

                        <input type="hidden" name="id_leccion" id="leccion">
                        <input type="hidden" name="id_area" id="id_area">
                        <input type="hidden" name="id_vp" id="id_vp">
                        <input type="hidden" name="id_obj_est" id="id_obj_est">
                        <input type="hidden" name="tipo_leccion" id="tipo_leccion">
                        <input type="hidden" name="id_tipo" id="tipo">
                        <input type="hidden" name="area" value="<?php echo $_SESSION['area']; ?>">
                        <input type="hidden" name="usuario" value="<?php echo $_SESSION['id_user']; ?>">
                        <input type="hidden" name="empresa" value="<?php echo $_SESSION['id_empresa']; ?>">
                        <div class="row">
                            <div class="col-md-12">
                                <textarea id="comentario_leccion" name="comentario" class="form-control" placeholder="Ingrese su comentario..." rows="5" required></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-md btn-primary" onclick="Registrar_Comentar()">Comentar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-xl" id="modal-EditarComentario" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title modal-title-primary">Actualizar Comentario</h4>
                <button type="button" class="btn btn-close" data-bs-dismiss="modal" aria-label="Close" style="background: none !important;color: #fff;
    background-color: #dc3545 !important;
    border-color: #dc3545 !important;opacity: 1 !important;margin-bottom: 5px !important;
    margin-right: 5px !important;
    "><i class="fas fa-times pull-right" style="font-size: larger;"></i></button>
            </div>

            <div class="modal-body" id="modal_contenido">
                Cargando...
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Detectar si la página se ha recargado usando la API de Performance
        if (performance.navigation.type === performance.navigation.TYPE_RELOAD || performance.getEntriesByType('navigation')[0].type === 'reload') {
            // Si la página se recargó, limpiar el formulario
            document.getElementById("comenta-form").reset();
        }

        // Alternativa usando sessionStorage para detectar recarga de página
        if (sessionStorage.getItem('is_reloaded')) {
            document.getElementById("comenta-form").reset();
            sessionStorage.removeItem('is_reloaded');
        } else {
            sessionStorage.setItem('is_reloaded', true);
        }
    });
</script>