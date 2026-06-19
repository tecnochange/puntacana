<script>
    var api = "<?php echo $url ?>api/okrs/";
    function DetalleOKrs(id){
        $("#modal_okrs").modal("show");

        data = {
            id: id,
            id_user: <?php echo $user_log["id"]; ?>,
            id_empresa: <?php echo $user_log["id_empresa"]; ?>
        }
        jQuery.ajax({
            url: api + "detalle_okrs.php",
            type: 'post',
            data: data,
        }).done(function(resp) {
            $("#modal_body_okrs").html(resp);
        })
        .fail(function(resp) {
            console.log(resp);
        })
        .always(function(resp) {});
    }
    
    function Seguimiento_Resultado(id, valor){

        console.log("ingreso");

        data = {
            id: id,
            valor: valor, 
            id_user: <?php echo $user_log["id"]; ?>, 
            url: '<?php echo $_SERVER['REQUEST_URI']; ?>'
        }

        jQuery.ajax({
            url: api+"seguimiento_resultado.php",
            type: "POST",
            data: data 
            })
            .done((resp) => {
                $("#xscript").html(resp);
                //this.mostrarToast("Seguimiento actualizado correctamente");
            })
            .fail((xhr) => {
                //console.error("✗ Error en el servidor", xhr.responseText);
                //this.mostrarToast("Error al guardar", "danger");
            }).always(function(resp) {}
        );
    }
</script>