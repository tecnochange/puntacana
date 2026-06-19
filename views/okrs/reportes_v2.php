<script>
    $(document).ready(function() {
        $('#menuOkrs').collapse();
        $('#bt_okrs_reportes').addClass('active');
    });
</script>

<?php
//PAGINACION
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$limite = isset($_GET["limite"]) ? (int)$_GET["limite"] : 10;
$offset = ($pagina - 1) * $limite;

$posicion = 0;
$longitud = 25;
if($_GET["p"]){
    $posicion = $_GET["p"]*$longitud;
}

include("app/models/okrs/OkrsServicios.php");
$ClassOkrsServicios = new OkrsServicios();

//OBTENEMOS LOS OKRS DEL AREA O EQUIPO DE TRABAJO Y PROCESAMOS TODOS LOS POST
$okrs_organizacion = $ClassOkrsServicios->okrs_organizacion_new( $user_log["id_empresa"] );
$okrs_organizacion_paginado = $ClassOkrsServicios->okrs_organizacion_paginado( $user_log["id_empresa"], $posicion, $longitud );

//DATO PARA ENVIAR AL FILTRO
$okrs_filtro = $okrs_organizacion;
$datos_consolidado = $ClassOkrsServicios->datos_consolidado_okrs($user_log["id_empresa"], $okrs_organizacion);

$total_paginado = 0;
if(count($okrs_organizacion_paginado) > 0){
    $total_paginado = count($okrs_organizacion_paginado);
}

$total_pagina = 0;
if(count($okrs_organizacion) > 0){
    $total_registros = count($okrs_organizacion);
    $total_pagina = $total_registros > 0 ? ceil($total_registros / $longitud) : 0;
}

/*
$okrs_organizacion_pagina = $ClassOkrsServicios->okrs_organizacion_pagina( $user_log["id_empresa"],  $posicion, $longitud );





$reporte_okrs_all = $ClassOkrsServicios->reporte_okrs_all($user_log["id_empresa"], $_SESSION["anio_fill"]);
$reporte_okrs_all_paginado = $ClassOkrsServicios->reporte_okrs_all_paginado($user_log["id_empresa"], $_SESSION["anio_fill"], $limite, $offset);




//$datos_consolidado = $ClassOkrsServicios->datos_consolidado_okrs($user_log["id_empresa"], $okrs_objetivos_equipo);

$datos_consolidado = $ClassOkrsServicios->datos_consolidado_okrs($user_log["id_empresa"], $reporte_okrs_all_paginado);
//DATOS DE LOS OKRS
include("app/models/okrs/Okrs.php");
$ClassOkrs = new Okrs();
$okrs_consolidado = $ClassOkrs->ResultadoOkrs($user_log["id"], $user_log["id_empresa"], $_SESSION["anio_fill"]);
$okrs_empresa = $ClassOkrsServicios->okrs_empresa(NULL, $user_log["id_empresa"], $_SESSION["anio_fill"]);
*/
?>

<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Ver Todos los OKRs</h3>
        </div>
        <div class="card-body">

            <div>
                Aquí podrá gestionar los Okrs de la organización.
            </div>

        </div>
    </div>

    <!-- FILTROS -->
    <?php include("views/okrs/componentes/filtros.php"); ?>

    <?php if(count($okrs_organizacion_paginado) == 0): ?>
        <div class="card">
            <div class="card-body text-center">
                <h5 class="text-secondary">Actualmente no hay Okrs asociados</h5>
            </div>
        </div>
    <?php exit; endif; ?>

    <!-- AVANCE GENERAL -->
    <div class="card mb-3">
        <div class="card-body">

            <h5>AVANCE GENERAL (<?php echo count($okrs_organizacion); ?>)</h5>
            <?php $valor = $datos_consolidado["promedio_general"]; ?>
            <?php include("views/reportes/layouts/objetivos_desempenio.php"); ?>
        </div>
    </div>

    <div class="table-responsive">
        <?php if($total_pagina > 1): ?>
            <nav aria-label="Page navigation example">
                <ul class="pagination">
                    <?php 
                    for ($x = 0; $x < $total_pagina; $x++) {
                        if($x == $_GET["p"]){
                            echo '<li class="page-item active"><a class="page-link" href="?pg=okrs/reportes_v2&p='.$x.'">'.($x+1).'</a></li>';
                        }
                        else{
                            echo '<li class="page-item"><a class="page-link" href="?pg=okrs/reportes_v2&p='.$x.'">'.($x+1).'</a></li>';
                        }
                    }
                    ?>
                    <li class="page-item disabled">
                        <a class="page-link" href="#">Mostrando <?= $total_paginado; ?></a>
                    </li>
                </ul>
            </nav>
        <?php endif; ?>
    </div>

    <!-- CONTENEDOR -->
    <div id="okrsContainer">
        <?php foreach ($okrs_organizacion_paginado as $okrs){ ?>
            <?php include("views/okrs/componentes/ficha_okrs.php"); ?>
        <?php } ?>
    </div>

    <!-- PAGINACION -->
    <?php
    $total_okrs = count($reporte_okrs_all);
    $total_paginas = ceil($total_okrs / $limite);
    ?>
    <nav aria-label="Paginación OKRs">
        <ul class="pagination justify-content-center">

            <li class="page-item mx-4 d-flex align-items-center">
                Mostrando <?= $total_paginado ?> resultados&nbsp;
                <?php if($total_okrs > 10): ?>
                    <select id="limiteSelect" class="form-select form-select-sm" style="width: 80px;">
                        <option value="10" <?= isset($_GET["limite"]) && $_GET["limite"] == 10 ? 'selected' : '' ?>>10</option>
                        <option value="50" <?= isset($_GET["limite"]) && $_GET["limite"] == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= isset($_GET["limite"]) && $_GET["limite"] == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                <?php endif; ?>
            </li>
            
            <?php if($total_okrs > 10): ?>
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                        <a class="page-link" href="?pg=okrs/reportes&pagina=<?= $i ?>&limite=<?= $limite ?>">
                            <?= $i ?>
                        </a>
                    </li>
                <?php endfor; ?>
            <?php endif; ?>

        </ul>
    </nav>

</div>

<?php include("views/okrs/javascript.php"); ?>
<?php include("app/models/okrs/OkrsScripts.php"); ?>
<?php include("views/okrs/okr/js/comentarios_kr.php"); ?>
<?php include("views/okrs/okr/js/comentarios_iniciativas.php"); ?>
<?php include("views/okrs/okr/js/comentarios_plan_accion.php"); ?>
<?php include("views/okrs/okr/js/documentos_kr.php"); ?>
<?php include("views/okrs/okr/js/documentos_plan_accion.php"); ?>
<?php //include("views/okrs/okr/js/paginacion.php"); ?>
<script>
    // Instancia global accesible por los inputs
    const okrsClass = new OkrsScripts();
</script>
<script>
document.getElementById("limiteSelect").addEventListener("change", function() {
    const limite = this.value;

    // Reinicia la página en la página 1 cuando cambia el límite
    window.location.href = "?pg=okrs/reportes&pagina=1&limite=" + limite;
});
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