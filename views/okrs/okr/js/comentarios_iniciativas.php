<?php
/* VENTANAS MODALES */
include("views/okrs/componentes/modal_comentarios_iniciativas.php"); //COMENTARIOS DEL OKRS
?>
<script>
    //MODALE COMENTARIOS DEL OKRS
    let id_okr_iniciativa = null;
    let id_resultado_iniciativa = null;
    let id_empleado_iniciativa = null;
    let id_iniciativa = null;

    // Cuando se abre el modal
    $('#modal_comentarios_iniciativas').on('show.bs.modal', function(event) {
        let button = $(event.relatedTarget);
        
        id_okr_iniciativa = button.data('id_okr');
        id_resultado_iniciativa = button.data('id_resultado');
        id_empleado_iniciativa = button.data('id_empleado');
        id_iniciativa = button.data('id_iniciativa');
        descripcion = button.data('descripcion');

        $("#modal_comentarios_titulo_iniciativa").html("<h5>Comentarios para</h5>"+descripcion)
        cargarComentarios_iniciativas();
    });

    function cargarComentarios_iniciativas() {
        $("#contenedor_comentarios_iniciativa").html('<div class="text-center">Cargando...</div>');

        $.ajax({
            url: "api/okrs/cargar_comentarios_iniciativa.php",
            type: "GET",
            data: {
                id_okr: id_okr_iniciativa,
                id_resultado: id_resultado_iniciativa,
                id_iniciativa: id_iniciativa
            },
            success: function(response) {
                $("#contenedor_comentarios_iniciativa").html(response);
            },
            error: function() {
                $("#contenedor_comentarios_iniciativa").html('<div class="text-center text-secondary">No hay comentarios relacionados.</div>');
            }
        });
    }

    $("#btn_guardar_comentario_iniciativa").on("click", function() {

        let comentario = $("#input_comentario_iniciativa").val().trim();

        if (comentario === "") return;
        
        $.ajax({
            url: "api/okrs/guardar_comentarios_iniciativa.php",
            type: "POST",
            data: {
                id_okr: id_okr_iniciativa,
                id_resultado: id_resultado_iniciativa,
                id_empleado: id_empleado_iniciativa,
                id_iniciativa: id_iniciativa,
                comentario: comentario
            },
            success: function() {
                $("#input_comentario").val("");
                cargarComentarios_iniciativas();
            }
        });

    });
</script>