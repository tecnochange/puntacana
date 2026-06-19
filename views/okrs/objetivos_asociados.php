<script>
$(document).ready(function() {
    $('#menuOkrs').collapse();
    $('#bt_okrs_objetivos_asociados').addClass('active');
});
</script>

<?php

include("app/models/okrs/Okrs.php");
$ClassOkrs = new Okrs();

$resultado_general = $ClassOkrs->ResultadoOkrs($user_log["id"], $user_log["id_empresa"], $_SESSION["anio_fill"]);
$bg_color = EscalaColor($resultado_general);

include("app/models/okrs/OkrsServicios.php");
$ClassOkrsServicios = new OkrsServicios();
$okrs_objetivos_asociados = $ClassOkrsServicios->objetivos_asociados($user_log["id_empresa"], $user_log["id"], $user_log["id_area"], $_SESSION["anio_fill"]);

//CONSOLIDADO
$datos_consolidado = $ClassOkrsServicios->datos_consolidado_okrs($user_log["id_empresa"], $okrs_objetivos_asociados);
?>


<script>
    var api_eliminar = '<?php echo $url; ?>api/okrs/';

    var permitir = false;
    function EliminarResultadoClave(id){

        if(permitir == false){
            $("#modal_general").modal("show");
            $("#modal_body").html("Estas a punto de eliminar un Resultado Clave, esto eliminará todos los datos relacionados con el mismo incluyendo: seguimientos, iniciativas, planes de acción, etc. esta acción  es irreversible. ¿Está seguro? <br><br> ");
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="permitir = true;EliminarResultadoClave('+id+')">Eliminar</button> <br> Nota: este esta acción será registrada en la auditoría con su nombre.');
            
        }
        else{

        

            data = {
                id_empresa: <?php echo $user_log["id_empresa"]; ?>, 
                id_user: <?php echo $user_log["id"]; ?>,
                id_resultado: id, 
                url: '?pg=okrs/objetivos_asociados'
            };
            jQuery.ajax({
                url: api_eliminar + "eliminar_resultado.php",
                type: 'post',
                data: data,
                })
                .done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {}
            );

        }
            

    }

    var permitir = false;
    function EliminarIniciativa(id_iniciativa){

        if(permitir == false){
            $("#modal_general").modal("show");
            $("#modal_body").html("Estas a punto de eliminar una iniciativa, esto eliminará todos los datos relacionados. esta acción  es irreversible. ¿Está seguro? <br><br> ");
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="permitir = true;EliminarIniciativa('+id_iniciativa+')">Eliminar Iniciativa</button> <br> Nota: este esta acción será registrada en la auditoría con su nombre.');
            
        }
        else{

            data = {
                id_empresa: <?php echo $user_log["id_empresa"]; ?>, 
                id_user: <?php echo $user_log["id"]; ?>,
                id_iniciativa: id_iniciativa, 
                url: '?pg=okrs/mis_iniciativas'
            };
            jQuery.ajax({
                url: api_eliminar + "objetivos_asociados.php",
                type: 'post',
                data: data,
                })
                .done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {}
            );
            
        }

    }
</script>

<?php include("views/okrs/componentes/modal_ficha_okrs.php"); ?>

<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Ver Objetivos Asociados</h3>
        </div>
        <div class="card-body">
            
            <div>
                Aquí podrá gestionar los objetivos, resultados, iniciativas y planes de acción de su área o equipo, recuerde que puede utilizar los filtros para acceder de forma rápida a la información requerida.
            </div>

        </div>
    </div>

    <!-- FILTROS -->
    <?php include("views/okrs/componentes/filtros.php"); ?>

    <!-- SI NO HAY RESULTADOS -->
    <?php if(count($okrs_objetivos_asociados) == 0): ?>
        <div class="card">
            <div class="card-body text-center">
                <h5 class="text-secondary">Actualmente no hay Objetivos asociados</h5>
            </div>
        </div>
    <?php exit; endif; ?>

    <!-- AVANCE GENERAL -->
    <div class="card mb-3">
        
        <div class="card-body">
            
            <h5>AVANCE GENERAL DE MIS OKR's</h5>
            <div class="progress" style="height: auto;">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $datos_consolidado["promedio_general"]; ?>%; background-color: <?php echo $datos_consolidado["color_general"]; ?> !important;" aria-valuenow="<?php echo $resultado_general; ?>" aria-valuemin="0" aria-valuemax="100">
                    <div style="font-size: 40px; color: #000000;"><b><?php echo $datos_consolidado["promedio_general"]; ?>%</b></div>
                </div>
            </div>

        </div>
    </div>

    <?php
        foreach ($okrs_objetivos_asociados as $okrs) {
            include("views/okrs/componentes/ficha_okrs.php");
        }
    ?>
</div>

<?php include("views/okrs/javascript.php"); ?>
<?php include("app/models/okrs/OkrsScripts.php"); ?>
<?php include("views/okrs/okr/js/comentarios_kr.php"); ?>
<?php include("views/okrs/okr/js/comentarios_iniciativas.php"); ?>
<?php include("views/okrs/okr/js/comentarios_plan_accion.php"); ?>
<?php include("views/okrs/okr/js/documentos_kr.php"); ?>
<?php include("views/okrs/okr/js/documentos_plan_accion.php"); ?>
<script>
    // Instancia global accesible por los inputs
    const okrsClass = new OkrsScripts();
</script>

<script>
    const cambiarPrioridad = (id) => {

        const select = $("#select_prioridad_" + id);
        const container = select.closest("div");
        const texto = container.find(".prioridad-texto");

        // Mostrar select
        select.removeClass("d-none");

        // Evitar múltiples eventos
        select.off("change");

        select.on("change", function() {
            const prioridad = $(this).val();

            const prioridades = {
                1: '<span class="btn btn-sm btn-info bg-white">Bajo</span>',
                2: '<span class="btn btn-sm btn-success bg-success">Medio</span>',
                3: '<span class="btn btn-sm btn-warning">Alto</span>',
                4: '<span class="btn btn-sm btn-danger">Urgente</span>'
            };

            // 👇 AQUÍ EL CAMBIO
            texto.html(prioridades[prioridad]);

            $(this).addClass("d-none");

            editarPrioridad(id, prioridad);
        });
    };
    const editarPrioridad = (id, prioridad) => {
        $.ajax({
            url: "api/okrs/editar_prioridad.php",
            method: "POST",
            data: {
                id: id,
                prioridad: prioridad
            },
            success: function(res) {
                //window.location.reload();
                //console.log(res);
            },
            error: function() {
                alert("Error al actualizar prioridad");
            }
        });
    };

    const cambiarBacklog = (id) => {

        const select = $("#select_backlog_" + id);
        const container = select.closest("div");
        const texto = container.find(".backlog-texto");

        // Mostrar select
        select.removeClass("d-none");

        // Evitar múltiples eventos
        select.off("change");

        select.on("change", function() {
            const backlog = $(this).val();

            const backlogs = {
                1: '<span class="btn btn-sm btn-info bg-white">Planificado</span>',
                2: '<span class="btn btn-sm btn-success bg-success">En Progreso</span>',
                3: '<span class="btn btn-sm btn-warning">En Revisión</span>',
                4: '<span class="btn btn-sm btn-success bg-success">Completado</span>'
            };

            // 👇 AQUÍ EL CAMBIO
            texto.html(backlogs[backlog]);

            $(this).addClass("d-none");

            editarBacklog(id, backlog);
        });
    };
    const editarBacklog = (id, backlog) => {
        const select = $("#select_backlog_" + id);
        const container = select.closest("div");
        const texto = container.find(".backlog-texto");
        const backlogs = {
            1: '<span class="btn btn-sm btn-info bg-white">Planificado</span>',
            2: '<span class="btn btn-sm btn-success">En Progreso</span>',
            3: '<span class="btn btn-sm btn-warning">En Revisión</span>',
            4: '<span class="btn btn-sm btn-success bg-success">Completado</span>'
        };
        texto.html(backlogs[backlog]);

        jQuery.ajax({
            url: "api/okrs/editar_estado_backlog.php",
            type: 'post',
            data: {
                id_plan_accion: id,
                estado: backlog
            },
            success: function(res) {
                //window.location.reload();
            }
        });
    };
</script>



<script>
    var api = '<?php echo $url; ?>api/okrs/';
    var permitir = false;
    function EliminarIniciativa(id_iniciativa){

        if(permitir == false){
            $("#modal_general").modal("show");
            $("#modal_body").html("Estas a punto de eliminar una iniciativa, esto eliminará todos los datos relacionados. esta acción  es irreversible. ¿Está seguro? <br><br> ");
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="permitir = true;EliminarIniciativa('+id_iniciativa+')">Eliminar Iniciativa</button> <br> Nota: este esta acción será registrada en la auditoría con su nombre.');
            
        }
        else{

            data = {
                id_empresa: <?php echo $user_log["id_empresa"]; ?>, 
                id_user: <?php echo $user_log["id"]; ?>,
                id_iniciativa: id_iniciativa, 
                url: '?pg=okrs/objetivos_asociados'
            };
            jQuery.ajax({
                url: api + "eliminar_iniciativa.php",
                type: 'post',
                data: data,
                })
                .done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {}
            );

        }

    }

    function FichaOkrs(id_okr){

        $("#modal_ficha_okrs").modal("show");
        $("#body_modal_ficha_okrs").html("Cargando...");

        data = {
                id_empresa: <?php echo $user_log["id_empresa"]; ?>, 
                id_user: <?php echo $user_log["id"]; ?>,
                id_okr: id_okr
        };
        jQuery.ajax({
                url: api + "ficha_okrs.php",
                type: 'post',
                data: data,
                })
                .done(function(resp) {
                    $("#body_modal_ficha_okrs").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {}
        );

    }

    
</script>