<?php
/* VENTANAS MODALES */
include("views/okrs/componentes/modal_comentarios_plan_accion.php"); //COMENTARIOS DEL OKRS
?>
<script>
    //MODALE COMENTARIOS DEL OKRS
    let id_empresa_accion = null;
    let id_okr_plan_accion = null;
    let id_resultado_plan_accion = null;
    let id_empleado_plan_accion = null;
    let id_plan_accion = null;

    // Cuando se abre el modal
    $('#modal_comentarios_plan_accion').on('show.bs.modal', function(event) {
        let button = $(event.relatedTarget);
        
        id_empresa_accion = button.data('id_empresa');
        id_okr_plan_accion = button.data('id_okr');
        id_resultado_plan_accion = button.data('id_resultado');
        id_empleado_plan_accion = button.data('id_empleado');
        id_plan_accion = button.data('id_plan_accion');
        descripcion = button.data('descripcion');

        $("#modal_comentarios_titulo_plan_accion").html("<h5>Comentarios</h5>"+descripcion)
        cargarComentarios_plan_accion();
    });

    function cargarComentarios_plan_accion() {
        $("#contenedor_comentarios_plan_accion").html('<div class="text-center">Cargando...</div>');

        $.ajax({
            url: "api/okrs/cargar_comentarios_plan_accion.php",
            type: "GET",
            data: {
                id_plan_accion: id_plan_accion
            },
            success: function(response) {              
                $("#contenedor_comentarios_plan_accion").html(response);
            },
            error: function() {
                $("#contenedor_comentarios_plan_accion").html('<div class="text-center text-secondary">No hay comentarios relacionados.</div>');
            }
        });
    }

    $("#btn_guardar_comentario_plan_accion").on("click", function() {

        let comentario = $("#input_comentario_plan_accion").val().trim();

        if (comentario === "") return;
        
        $.ajax({
            url: "api/okrs/guardar_comentarios_plan_accion.php",
            type: "POST",
            data: {
                id_empresa: id_empresa_accion,
                id_empleado: id_empleado_plan_accion,
                id_plan_accion: id_plan_accion,
                comentario: comentario
            },
            success: function(res) {
                //console.log(res);
                
                $("#input_comentario_plan_accion").val("");
                cargarComentarios_plan_accion();
            }
        });

    });
</script>