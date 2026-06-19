<?php
/* VENTANAS MODALES */
include("views/okrs/componentes/modal_documentos_plan_accion.php"); //COMENTARIOS DEL OKRS
?>
<script>
    
    let id_plan_accion_documentos = null;
    let id_empleado_plan_accion_documentos = null;
    let id_empresa_plan_accion_documentos = null;
    let descripcion_plan_accion_documentos = null;

    // Cuando se abre el modal
    $('#modal_documentos_plan_accion').on('show.bs.modal', function(event) {
        let button = $(event.relatedTarget);

        id_plan_accion_documentos = button.data('id_plan_accion');
        id_empleado_plan_accion_documentos = button.data('id_empleado');
        id_empresa_plan_accion_documentos = button.data('id_empresa');
        descripcion_plan_accion_documentos = button.data('plan_descripcion');

        $("#modal_documentos_titulo_plan_accion").html("<h5>Documentos para el Plan de Acción</h5>" + descripcion_plan_accion_documentos)
        cargarDocumentos_plan_accion();
    });

    function cargarDocumentos_plan_accion() {
        $("#contenedor_documentos_plan_accion").html('<div class="text-center">Cargando...</div>');

        $.ajax({
            url: "api/okrs/cargar_documentos_plan_accion.php",
            type: "GET",
            data: {
                id_plan_accion: id_plan_accion_documentos
            },
            success: function(response) {
                $("#contenedor_documentos_plan_accion").html(response);
            },
            error: function() {
                $("#contenedor_documentos_plan_accion").html('<div class="text-center text-secondary">No hay documentos relacionados.</div>');
            }
        });
    }

    $("#btn_guardar_documento_plan_accion").on("click", function() {

        let input = document.getElementById("input_documento_plan_accion");
        let documento = input.files[0];
        let comentario_documento = $("#input_comentario_documento_plan_accion").val().trim();

        if (!documento) {
            alert("Debes seleccionar un archivo");
            return;
        }
        if (comentario_documento === "") {
            alert("Debes incluir un comentario");
            return;
        }

        let formData = new FormData();
        formData.append("id_plan_accion", id_plan_accion_documentos);
        formData.append("id_empresa", id_empresa_plan_accion_documentos);
        formData.append("id_empleado", id_empleado_plan_accion_documentos);
        formData.append("comentario", comentario_documento);
        formData.append("documento", documento);
        
        $.ajax({
            url: "api/okrs/guardar_documentos_plan_accion.php",
            type: "POST",
            data: formData,
            processData: false, // IMPORTANTE
            contentType: false, // IMPORTANTE
            success: function(response) {
                console.log(response);
                
                $("#input_documento_plan_accion").val("");
                $("#input_comentario_documento_plan_accion").val("");
                cargarDocumentos_plan_accion();
            },
            error: function() {
                console.error("Error al subir");
            }
        });

    });
</script>