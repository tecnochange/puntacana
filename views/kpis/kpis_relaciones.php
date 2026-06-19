<script>
    $(document).ready(function() {
        $('#menuKpis').collapse();
        $('#bt_kpis_compania').addClass('active');
    });
</script>

<?php
$id_area = $_GET["id"];
$array_kpis_general = array();
$hoy = date("Y-m-d H:i:s");

include("app/models/kpis/KpisCrud.php");
$ClassKpisCrud = new KpisCrud();

if ($_POST["guardar_avance_kpi"]) {
    $ClassKpisCrud->actualizar_avance_Kpis($_POST);
    echo '<script> window.location = "?pg=kpis/mis_kpis#ref_'.$_POST["id_kpi"].'"; </script>'; 
}

//OBTENEMOS LAS RELACIONES LABORALES
//OBTENEMOS LAS RELACIONES LABORALES
//OBTENEMOS LAS RELACIONES LABORALES
$array_vicepresidencia = array();
$array_areas = array();
$queryRelaciones = mysqli_query($connect_admin, "SELECT * FROM Relaciones_Laborales 
WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND id_empleado = '".$user_log["id"]."' AND mod_kpis = 'on' ");
while($dataRelaciones = mysqli_fetch_array($queryRelaciones)){
    if($dataRelaciones["id_vp"]){
        array_push($array_vicepresidencia, $dataRelaciones["id_vp"]  );
    }
    if($dataRelaciones["id_vp"]){
        array_push($array_areas, $dataRelaciones["id_area"]  );
    }    
}
if($queryRelaciones->num_rows == 0){
    array_push($array_vicepresidencia, '-1' );
    array_push($array_areas, '-1'  );
}

//print_r( implode(",", $array_vicepresidencia) );
//print_r( implode(",",  $array_areas)  );


//DATOS DE LA VICEPRESIDENCIA
//INFORMACION DE LA BATERIA
$query = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id = '" . $id_area . "' ");
$data = mysqli_fetch_array($query);

$posicion = 0;
$longitud = 25;
if($_GET["p"]){
    $posicion = $_GET["p"]*$longitud;
}



include("app/models/kpis/KpisServicios.php");
$ClassKpisServicios = new KpisServicios($user_log["id_empresa"]);
$array_kpis_general = $ClassKpisServicios->kpis_relaciones($user_log["id_empresa"], $array_vicepresidencia, $array_areas);
$array_kpis_general_paginado = $ClassKpisServicios->kpis_relaciones_paginado($user_log["id_empresa"], $posicion, $longitud, $array_vicepresidencia, $array_areas );



$total_paginado = 0;
if(count($array_kpis_general_paginado) > 0){
    $total_paginado = count($array_kpis_general_paginado);
}

$total_pagina = 0;
if(count($array_kpis_general) > 0){
    $total_registros = count($array_kpis_general);
    $total_pagina = ceil($total_registros/$longitud);
}

//print_r($array_kpis_general);

/*
//LISTAMOS LOS KPIS
$avance_general = 0;
foreach($areas_listas as $area){
    $avance_general += $area["avance_area"];

    $array_kpis = $area["kpis_lista"];
    foreach( $array_kpis as $kpis ){ 
        array_push( $array_kpis_general, $kpis );
    }
}

if($avance_general > 0){
    $avance_general = $avance_general/count($areas_listas);
    $avance_general = round($avance_general);
}
    */

//
$datos_consolidado_kpis = $ClassKpisServicios->datos_consolidado_kpis($user_log["id_empresa"], $array_kpis_general);

?>

<style>
    /* Margen debajo de la barra de herramientas (botones) */
    .dt-buttons {
        margin-bottom: 15px !important;
    }

    .card_box {
        margin: 15px 0px;
        border: 1px solid #cccccc;
        padding: 15px;
        font-size: 12px;
        height: 82%;
        background-color: #f2f2f2;
        line-height: 15px;
    }
    .accordion-button:focus{
        border: none;
        box-shadow: none;
    }
    .accordion-button:focus{
        border: none;
        box-shadow: none;
    }
    .accordion-button:not(.collapsed){
        background-color: transparent;
    }
</style>

<div class="container-fluid">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Todos los KPIs de la Compañía para el año <?php echo $_SESSION["anio_fill"]; ?> (Relaciones Laborales)</h3>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="row">
        <div class="col-md-12">
            <?php include("views/kpis/componentes/filtros.php"); ?>
        </div>
    </div>

    <?php include("views/kpis/componentes/consolidado_general.php"); ?>

    <div class="table-responsive">
    <nav aria-label="Page navigation example">
        <ul class="pagination">
            <?php 
            for ($x = 0; $x <= $total_pagina; $x++) {
                if($x == $_GET["p"]){
                    echo '<li class="page-item active"><a class="page-link" href="?pg=kpis/kpis_empresa&p='.$x.'">'.($x+1).'</a></li>';
                }
                else{
                    echo '<li class="page-item"><a class="page-link" href="?pg=kpis/kpis_empresa&p='.$x.'">'.($x+1).'</a></li>';
                }
            }
            ?>
            <li class="page-item disabled">
                <a class="page-link" href="#">Mostrando <?= $total_paginado; ?></a>
            </li>
        </ul>
    </nav>
    </div>

    <div class="card mb-3">

        <div class="accordion accordion-flush p-3" id="accordionVicepresidencias">

            <?php foreach ($array_kpis_general_paginado as $kpis) { ?>
                <?php //dd($kpis["integrantes"]); ?>
                <div class="accordion-item card mb-2 p-2">
                    <div class="card-body" style="padding: 5px 20px;">
                        <div class="row">

                            <div class="col-md-5">
                                
                                <span style="color: #007bff;">
                                    Tipo: <b><?= $kpis["tipo_txt"]; ?></b>
                                </span>

                                <?php
                                //FRECUENCIA
                                $frecuencia = "";
                                foreach($Array_Frecuencia_PC as $frecuencia){
                                    if($frecuencia[0] == $kpis["frecuencia"]){
                                        $frecuencia = $frecuencia[1];
                                        break;
                                    }            
                                }
                                ?>

                                <h6><b><?= $frecuencia; ?>   |   <?= $kpis["indicador"]; ?></b></h6>
                                
                                <span style="color: #999999">
                                    <b>Área Macro:</b> <?= $kpis["nombre_area_macro"]; ?> <br>
                                    <b>Área Proceso:</b> <?= $kpis["nombre_area_proceso"]; ?> <br>
                                    <?= !empty($kpis["nombre_subproceso"]) ? "<b>Subproceso:</b> ".$kpis["nombre_subproceso"] : ""; ?><br>
                                </span>
                            </div>

                            <div class="col-md-2 d-flex align-items-center text-center fs-6">
                                <div>
                                    <b>Meta:</b> <br>
                                    <b><?= round($kpis["meta"], 2); ?></b>
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-center text-center fs-6">
                                <div>
                                    <b>Seguimiento</b> <br>
                                    <b><?= round($kpis["avance_plano_kpis"], 2); ?></b>
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-center">
                                <div class="text-left" style="width: 100%;">
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $kpis["avance_kpis"]; ?>%; background-color:<?= $kpis["color_avance_kpis"]; ?> !important;" aria-valuenow="<?= round($kpis["avance_kpis"]); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <b><?= round($kpis["avance_kpis"],2); ?>%</b>
                                </div>
                            </div>

                            <div class="col-md-1 d-flex align-items-center">
                                <a href="?pg=kpis/detalle/detalle_kpi&id=<?= $kpis["id_kpi"]; ?>" class="btn btn-outline-dark btn-sm ">
                                    <i class="bx bx-pencil" title="Editar Okrs"></i>
                                </a>
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#item_<?= $kpis["id_kpi"]; ?>" aria-expanded="false" aria-controls="item_<?= $vicepresidencia["id"]; ?>" style="margin-top: 14px; max-width:80px;"></button>
                            </div>

                            <!-- AQUI SE PINTA LA TABLA CON LAS AREAS -->
                            <div class="col-md-12">
                                <div id="item_<?= $kpis["id_kpi"]; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionVicepresidencias">

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="card_box">
                                                Responsables: <br>
                                                <?php
                                                $limit = 0;
                                                foreach($kpis["integrantes"] as $integrante) {
                                                    if($limit <= 8){
                                                        echo '
                                                        <img src="' . $recursos_publico . '/' . $integrante["foto"] . '"
                                                            class="foto_miniaturas"
                                                            title="' . $integrante["nombre"] . '"
                                                            style="cursor:pointer"
                                                            onclick="FichaEmpleado(' . $integrante["id_colaborador"] . ')"
                                                        >
                                                        ';
                                                    }
                                                        $limit++;
                                                }
                                                if( count($kpis["integrantes"]) > 8 ){
                                                    echo '
                                                        <button class="btn btn-warning btn-sm" onclick="VerIngrantesKpi('.$kpis["id_kpi"].')">
                                                            <i class="bx bx-plus-medical" title="Ver todos los responsables"></i>
                                                        </button>
                                                    ';
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        

                                        <div class="col-md-2">
                                            <div class="card_box">
                                                Objetivo del Indicador: <br>
                                                <b><?= $kpis["objetivo_indicador"]; ?></b>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="card_box">
                                                Fórmula de Cálculo: <br>
                                                <b><?= $kpis["formula"]; ?></b>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="card_box">
                                                Tipo de Resultado: <br>
                                                <b><?= $kpis["tipo_resultado_txt"]; ?></b>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="card_box">
                                                Tipo de Cálculo: <br>
                                                <b><?= $kpis["tipo_calculo_txt"]; ?></b>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="card_box">
                                                Unidad de Medida: <br>
                                                <b><?= $kpis["unidad_medida_txt"]; ?></b>
                                            </div>
                                        </div>
                                    </div>

                                    <form action="" method="POST">
                                        <input type="hidden" name="guardar_avance_kpi" value="true">
                                        <input type="hidden" name="id_kpi" value="<?= $kpis["id_kpi"] ?>">
                                        <input type="hidden" name="tipo" value="<?= $kpis["tipo"] ?>">
                                        <?php
                                        if ($kpis["frecuencia"] == 1) {
                                            include("views/kpis/consolidados/tabla_mensual.php");
                                        }
                                        if ($kpis["frecuencia"] == 2) {
                                            include("views/kpis/consolidados/tabla_bimestral.php");
                                        }
                                        if ($kpis["frecuencia"] == 3) {
                                            include("views/kpis/consolidados/tabla_trimestral.php");
                                        }
                                        if ($kpis["frecuencia"] == 6) {
                                            include("views/kpis/consolidados/tabla_cuatrimestral.php");
                                        }
                                        if ($kpis["frecuencia"] == 4) {
                                            include("views/kpis/consolidados/tabla_semestral.php");
                                        }
                                        if ($kpis["frecuencia"] == 5) {
                                            include("views/kpis/consolidados/tabla_anual.php");
                                        }
                                        ?>

                                        <div class="text-end">
                                            <button type="submit" class="btn btn-success mb-3">Guardar Avances</button>
                                            <a href="?pg=kpis/gestionar_kpi&id=<?= $kpis["id_kpi"] ?>&id_user=<?= $user_log["id"] ?>&role=<?= $user_log["role"]; ?>&id_empresa=<?= $user_log["id_empresa"]; ?>" target="_blank" type="button" class="btn btn-warning mb-3">Gestionar KPI</a>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php } ?>

        </div>

    </div>

</div>


<script>
    var api = '<?php echo $url; ?>api/kpis/';

    function FichaEmpleado(id_empleado) {

        var id_empresa = <?php echo $user_log["id_empresa"]; ?>

        $("#modal_empleado").modal("show");
        $("#body_empleado").html("Cargando...");

        jQuery.ajax({
                url: api + "ficha_empleado.php",
                type: 'post',
                data: {
                    id_empresa: id_empresa,
                    id_empleado: id_empleado,
                },
        })
        .done(function(resp) {
                $("#body_empleado").html(resp);
        })
        .fail(function(resp) {
                console.log(resp);
        })
        .always(function(resp) {});


    }

    function VerIngrantesKpi(id_kpi){
        var id_empresa = <?php echo $user_log["id_empresa"]; ?>

        $("#modal_general").modal("show");
        $("#modal_body").html("Cargando...");

        jQuery.ajax({
            url: api + "lista_integrantes_kpi.php",
            type: 'post',
            data: {
                id_empresa: id_empresa,
                id_kpi: id_kpi,
            },
        })
        .done(function(resp) {
            $("#modal_body").html(resp);
        })
        .fail(function(resp) {
                console.log(resp);
        })
        .always(function(resp) {});
    }

    let hash = window.location.hash.substring(1);
    if(hash.startsWith("ref_")){
        hash = hash.replace("ref_", "");
        $("#item_" + hash).addClass("show");
    }
</script>