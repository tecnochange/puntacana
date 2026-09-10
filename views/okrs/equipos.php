<script>
    $(document).ready(function() {
        $('#menuOkrs').collapse();
        $('#bt_okrs_equipo').addClass('active');
    });
</script>



<?php
//RESETEAMOS EL OKRS EN EDICION
$_SESSION["id_okrs_edit"] = "";

include("app/models/okrs/Okrs.php");
include("app/models/okrs/OkrsServicios.php");

$ClassOkrs = new Okrs();
$ClassOkrsServicios = new OkrsServicios();

//OBTENEMOS LOS OKRS DEL AREA O EQUIPO DE TRABAJO
$okrs_objetivos_equipo = $ClassOkrsServicios->okrs_area($user_log["id"], $user_log["id_empresa"], $user_log["id_area"], $_SESSION["anio_fill"]);

//DATO PARA ENVIAR AL FILTRO
$okrs_filtro = $okrs_objetivos_equipo;

//DATOS PARA EL CONSOLIDADO PARA ORKS
$okrs_iniciativas = $ClassOkrsServicios->iniciativas($user_log["id_empresa"], $okrs_objetivos_equipo);
$okrs_planes = $ClassOkrsServicios->planes_accion($user_log["id_empresa"], $okrs_objetivos_equipo);

//DATOS CONSOLIDADOS PARA LOS RESUMENTES
$datos_consolidado = $ClassOkrsServicios->datos_consolidado_okrs($user_log["id_empresa"], $okrs_objetivos_equipo);
$datos_consolidado_iniciativas = $ClassOkrsServicios->datos_consolidado_iniciativas($user_log["id_empresa"], $okrs_iniciativas);
$datos_consolidado_planes = $ClassOkrsServicios->datos_consolidado_planes_accion($user_log["id_empresa"], $okrs_planes);

//INICIATIVAS
// = $ClassOkrsServicios->okrs_area_iniciativas($user_log["id"], $user_log["id_empresa"], $user_log["id_area"], $_SESSION["anio_fill"]);

//DATOS PARA EL CONSOLIDADO PARA ORKS
//$okrs_iniciativas_equipo = $ClassOkrsServicios->okrs_area_iniciativas($user_log["id"], $user_log["id_empresa"], $user_log["id_area"], $_SESSION["anio_fill"]);
//$okrs_planes_accion_equipo = $ClassOkrsServicios->okrs_area_planes_accion($user_log["id"], $user_log["id_empresa"], $user_log["id_area"], $_SESSION["anio_fill"]);
//$okrs_consolidado = $ClassOkrs->ResultadoOkrs($user_log["id"], $user_log["id_empresa"], $_SESSION["anio_fill"]);
//$bg_color = EscalaColor($okrs_consolidado);
?>

<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Gestionar Resultados de Mi Área</h3>
        </div>
        <div class="card-body">
            <div>Aquí podrá gestionar los objetivos, resultados, iniciativas y planes de acción de su área o equipo, recuerde que puede utilizar los filtros para acceder de forma rápida a la información requerida.</div>
        </div>
    </div>

    <!-- FILTROS -->
    <?php include("views/okrs/componentes/filtros.php"); ?>

    <?php if(count($okrs_objetivos_equipo) == 0): ?>
        <div class="card">
            <div class="card-body text-center">
                <h5 class="text-secondary">Actualmente no hay Okrs asociados a mi área</h5>
            </div>
        </div>
    <?php exit; endif; ?>

    <div class="card mb-3">

        <!-- PESTAÑAS -->
        <ul class="nav nav-tabs text-center" style="width:100%;" id="okrsTabs" role="tablist">
            <li class="nav-item" style="width:33%;" role="presentation">
                <button class="nav-link active w-100" id="tab-objetivos"
                        data-bs-toggle="tab" data-bs-target="#content-objetivos" type="button" role="tab">
                        Objetivos
                </button>
            </li>

            <li class="nav-item" style="width:33%;" role="presentation">
                <button class="nav-link w-100" id="tab-iniciativas"
                        data-bs-toggle="tab" data-bs-target="#content-iniciativas" type="button" role="tab">
                        Iniciativas
                </button>
            </li>

            <li class="nav-item" style="width:33%;" role="presentation">
                <button class="nav-link w-100" id="tab-planes"
                        data-bs-toggle="tab" data-bs-target="#content-planes" type="button" role="tab">
                        Planes de acción
                </button>
            </li>
        </ul>

        <div class="card-body">

            <!-- CONTENIDO DE LAS PESTAÑAS -->
            <div class="tab-content mt-3" id="okrsTabsContent">

                

                <!-- OBJETIVOS -->
                <div class="tab-pane fade show active" id="content-objetivos" role="tabpanel" aria-labelledby="tab-objetivos">
                    <!-- OBJETIVOS -->
                    <div class="accordion accordion-flush">
                        <?php include("views/okrs/componentes/objetivos_equipo.php"); ?>
                    </div>
                </div>

                <!-- INICIATIVAS -->
                <div class="tab-pane fade" id="content-iniciativas" role="tabpanel" aria-labelledby="tab-iniciativas">
                    <div class="accordion accordion-flush">
                        <?php include("views/okrs/componentes/iniciativas_equipo.php"); ?>
                    </div>
                </div>

                <!-- PLANES DE ACCIÓN -->
                <div class="tab-pane fade" id="content-planes" role="tabpanel" aria-labelledby="tab-planes">
                    <div class="accordion accordion-flush">
                        <?php include("views/okrs/componentes/planes_accion_equipo.php"); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("app/models/okrs/OkrsScripts.php"); ?>
<?php include("views/okrs/okr/js/comentarios_kr.php"); ?>
<?php include("views/okrs/okr/js/documentos_kr.php"); ?>
<?php include("views/okrs/okr/js/comentarios_iniciativas.php"); ?>
<?php include("views/okrs/okr/js/comentarios_plan_accion.php"); ?>
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
                url: '?pg=okrs/equipos'
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
</script>