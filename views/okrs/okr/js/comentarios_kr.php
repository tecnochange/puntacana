<?php
/* VENTANAS MODALES */
include("views/okrs/componentes/modal_comentarios.php"); //COMENTARIOS DEL OKRS
?>
<script>
    //MODALE COMENTARIOS DEL OKRS
    let id_okr = null;
    let id_resultado = null;
    let id_empleado = null;

    // Cuando se abre el modal
    $('#modal_comentarios').on('show.bs.modal', function(event) {
        let button = $(event.relatedTarget);

        id_okr = button.data('id_okr');
        id_resultado = button.data('id_resultado');
        id_empleado = button.data('id_empleado');
        okr_descripcion = button.data('okr_descripcion');

        $("#modal_comentarios_titulo").html("<h5>Comentarios para</h5>"+okr_descripcion)
        cargarComentarios();
    });

    function cargarComentarios() {
        $("#contenedor_comentarios").html('<div class="text-center">Cargando...</div>');

        $.ajax({
            url: "api/okrs/cargar_comentarios.php",
            type: "GET",
            data: {
                id_okr: id_okr,
                id_resultado: id_resultado
            },
            success: function(response) {
                $("#contenedor_comentarios").html(response);
            },
            error: function() {
                $("#contenedor_comentarios").html('<div class="text-center text-secondary">No hay comentarios relacionados.</div>');
            }
        });
    }

    $("#btn_guardar_comentario").on("click", function() {

        let comentario = $("#input_comentario").val().trim();

        if (comentario === "") return;
        
        $.ajax({
            url: "api/okrs/guardar_comentarios.php",
            type: "POST",
            data: {
                id_okr: id_okr,
                id_resultado: id_resultado,
                id_empleado: id_empleado,
                comentario: comentario
            },
            success: function() {
                $("#input_comentario").val("");
                cargarComentarios();
            }
        });

    });
</script>