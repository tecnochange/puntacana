<?php
/* VENTANAS MODALES */
include("views/okrs/componentes/modal_documentos.php"); //COMENTARIOS DEL OKRS
?>
<script>
    //MODALE COMENTARIOS DEL OKRS
    let id_okr_documentos = null;
    let id_resultado_documentos = null;
    let id_empleado_documentos = null;
    let id_empresa_documentos = null;

    // Cuando se abre el modal
    $('#modal_documentos').on('show.bs.modal', function(event) {
        let button = $(event.relatedTarget);

        id_okr_documentos = button.data('id_okr');
        id_resultado_documentos = button.data('id_resultado');
        id_empleado_documentos = button.data('id_empleado');
        id_empresa_documentos = button.data('id_empresa');
        resultado_descripcion = button.data('resultado_descripcion');

        $("#modal_documentos_titulo").html("<h5>Documentos</h5>" + resultado_descripcion)
        cargarDocumentos();
    });

    function cargarDocumentos() {
        $("#contenedor_documentos").html('<div class="text-center">Cargando...</div>');

        $.ajax({
            url: "api/okrs/cargar_documentos.php",
            type: "GET",
            data: {
                id_okr: id_okr_documentos,
                id_resultado: id_resultado_documentos
            },
            success: function(response) {
                $("#contenedor_documentos").html(response);
            },
            error: function() {
                $("#contenedor_documentos").html('<div class="text-center text-secondary">No hay documentos relacionados.</div>');
            }
        });
    }

    $("#btn_guardar_documento").on("click", function() {

        let input = document.getElementById("input_documento");
        let documento = input.files[0];
        let comentario_documento = $("#input_comentario_documento").val().trim();

        if (!documento) {
            alert("Debes seleccionar un archivo");
            return;
        }
        if (comentario_documento === "") {
            alert("Debes incluir un comentario");
            return;
        }

        let formData = new FormData();
        formData.append("id_empresa", id_empresa_documentos);
        formData.append("id_empleado", id_empleado_documentos);
        formData.append("id_okr", id_okr_documentos);
        formData.append("id_resultado", id_resultado_documentos);
        formData.append("comentario", comentario_documento);
        formData.append("documento", documento);

        $.ajax({
            url: "api/okrs/guardar_documentos_resultado.php",
            type: "POST",
            data: formData,
            processData: false, // IMPORTANTE
            contentType: false, // IMPORTANTE
            success: function(response) {
                $("#input_documento").val("");
                $("#input_comentario_documento").val("");
                cargarDocumentos();
            },
            error: function() {
                console.error("Error al subir");
            }
        });

    });
</script>