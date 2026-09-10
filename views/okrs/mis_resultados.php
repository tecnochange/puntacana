
<script>
$(document).ready(function() {
    $('#menuOkrs').collapse();
    $('#bt_okrs_mis_resultados').addClass('active');
});
</script>

<?php
include("app/models/okrs/OkrsServicios.php");
$ClassOkrsServicios = new OkrsServicios();

if($_POST["mover_okrs"]){
    $respuesta = $ClassOkrsServicios->MoverResultadoClave($_POST["id_resultado_mover"], $_POST["id_okrs_nuevo"]);
    GuardarAuditoriaOkrs( $user_log["id_empresa"], $user_log["id"], $respuesta["accion"], $respuesta["descripcion"], $respuesta["id_okr_anterior"], 0, $respuesta["id_resultado"], $respuesta["tipo"] );
}


$mis_resultados_clave = $ClassOkrsServicios->mis_resultados_clave($user_log["id_empresa"], $user_log["id"], $user_log["id_area"], $_SESSION["anio_fill"]);

$datos_consolidado = $ClassOkrsServicios->datos_consolidado_okrs($user_log["id_empresa"], $mis_resultados_clave);
?>

<?php include("app/models/okrs/OkrsScripts.php"); ?>
<script>
    // Instancia global accesible por los inputs
    const okrsClass = new OkrsScripts();
</script>

<?php include("views/okrs/componentes/modal_ficha_okrs.php"); ?>
<?php include("views/okrs/componentes/modal_mover_resultado_clave.php"); ?>
<?php include("views/okrs/componentes/modal_duplicar_resultado_clave.php"); ?>

<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Mis Resultados Clave</h3>
        </div>
        <div class="card-body">
            <div>
                Aquí podrá gestionar los objetivos, resultados, iniciativas y planes de acción de su área o equipo, recuerde que puede utilizar los filtros para acceder de forma rápida a la información requerida.
            </div>
        </div>
    </div>

    <!-- FILTROS -->
    <?php include("views/okrs/componentes/filtros.php"); ?>

    <?php if(count($mis_resultados_clave) == 0): ?>
        <div class="card">
            <div class="card-body text-center">
                <h5 class="text-secondary">Actualmente no hay Resultados Claves asociados</h5>
            </div>
        </div>
    <?php exit; endif; ?>

    <!-- AVANCE GENERAL -->
    <div class="card mb-3">
        
        <div class="card-body">
            
            <h5>AVANCE GENERAL DE MIS OKR's</h5>
            <div class="progress" style="height: auto;">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $datos_consolidado["promedio_general"]; ?>%; background-color: <?php echo $datos_consolidado["color_general"]; ?> !important;" aria-valuenow="<?php echo $datos_consolidado["promedio_general"]; ?>" aria-valuemin="0" aria-valuemax="100">
                    <div style="font-size: 40px; color: #000000;"><b><?php echo $datos_consolidado["promedio_general"]; ?>%</b></div>
                </div>
            </div>

        </div>
    </div>

    <?php
        foreach ($mis_resultados_clave as $okrs) {
            include("views/okrs/componentes/ficha_okrs.php");
        }
    ?>
</div>

<?php include("views/okrs/javascript.php"); ?>
<?php include("views/okrs/okr/js/comentarios_kr.php"); ?>
<?php include("views/okrs/okr/js/comentarios_iniciativas.php"); ?>
<?php include("views/okrs/okr/js/comentarios_plan_accion.php"); ?>
<?php include("views/okrs/okr/js/documentos_kr.php"); ?>
<?php include("views/okrs/okr/js/documentos_plan_accion.php"); ?>

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

<script>
    function FichaMoverResultado(id_resultado){
        console.log(id_resultado);
        $("#modal_mover_okrs").modal("show");
        $("#id_resultado_mover").val(id_resultado);
        jQuery.ajax({
                url: api + "lista_okrs_mover.php",
                type: 'post',
                data: {
                    id_empresa: '<?php echo $user_log["id_empresa"] ?>', 
                    anio: '<?php echo $_SESSION["anio_fill"] ?>',
                },
                })
                .done(function(resp) {
                    $("#id_okrs_nuevo").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {}
        );
        
    }

    function FichaDuplicarResultado(id_resultado){
        console.log(id_resultado);
        $("#modal_duplicar_okrs").modal("show");
        

        jQuery.ajax({
                url: api + "ficha_resultado_duplicar.php",
                type: 'post',
                data: {
                    id_empresa: '<?php echo $user_log["id_empresa"] ?>', 
                    anio: '<?php echo $_SESSION["anio_fill"] ?>', 
                    id_resultado: id_resultado
                },
                })
                .done(function(resp) {
                    $("#cont_ficha_resultado_duplicar").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {}
        );
        
        
    }
</script>

