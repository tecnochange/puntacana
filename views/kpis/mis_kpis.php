<script>
    $(document).ready(function() {
        $('#menuKpis').collapse();
        $('#bt_mis_kpis').addClass('active');
    });
</script>

<?php
$id_area = $_GET["id"];
$array_kpis_general = array();
$hoy = date("Y-m-d H:i:s");

include("app/models/kpis/KpisServicios.php");
$ClassKpisServicios = new KpisServicios($user_log["id_empresa"]);

//MESES HABILTIADOS KPIS
$meses_habilitados = MesesHabilitados($user_log["id_empresa"], $_SESSION["anio_fill"]);
//dd($user_log);

//PARA GUARDAR EL AVANCE DE LOS KPIS
//PARA GUARDAR EL AVANCE DE LOS KPIS
include("app/models/kpis/KpisCrud.php");
$ClassKpisCrud = new KpisCrud();

if ($_POST["guardar_avance_kpi"]) {
    $ClassKpisCrud->actualizar_avance_Kpis($_POST);
    echo '<script> window.location = "?pg=kpis/mis_kpis#ref_'.$_POST["id_kpi"].'"; </script>'; 
}


//DATOS DE LA VICEPRESIDENCIA
//INFORMACION DE LA BATERIA
$query = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id = '" . $id_area . "' ");
$data = mysqli_fetch_array($query);

$array_kpis_general = $ClassKpisServicios->kpis_colaborador($user_log["id_empresa"], NULL, NULL, $user_log["id"]);

//PARA VALIDAR SI SE PUEDE EDITAR LOS KPIS O NO
//PARA VALIDAR SI SE PUEDE EDITAR LOS KPIS O NO
//PARA VALIDAR SI SE PUEDE EDITAR LOS KPIS O NO
//PARA VALIDAR SI SE PUEDE EDITAR LOS KPIS O NO
$read_only_kpis = '';
if ($dtEmpresa["anio_curso"] != $_SESSION["anio_fill"]) {
    $read_only_kpis = ' readonly ';
}

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

    .accordion-button:focus {
        border: none;
        box-shadow: none;
    }

    .accordion-button:focus {
        border: none;
        box-shadow: none;
    }

    .accordion-button:not(.collapsed) {
        background-color: transparent;
    }
</style>

<div class="container-fluid">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Ver Mis KPIs para el año <?php echo $_SESSION["anio_fill"]; ?></h3>
        </div>
    </div>

    <!-- FILTROS -->
    <div class="row">
        <div class="col-md-12">
            <?php include("views/kpis/componentes/filtros.php"); ?>
        </div>
    </div>

    <?php include("views/kpis/componentes/consolidado_general.php"); ?>

    <div class="card mb-3">

        <div class="accordion accordion-flush p-3" id="accordionVicepresidencias">

            <?php 
                foreach ($array_kpis_general as $kpis) { 
                    $meta_formato = '';
                    $seguimiento_formato = '';
                    if( $kpis["unidad_medida"] != 4 ){
                        $meta_formato = round($kpis["meta"], 2);
                        //$seguimiento_formato = round($kpis["avance_plano_kpis"], 2);
                        $seguimiento_formato = number_format($kpis["avance_plano_kpis"], 2, '.', ',');
                    }
                    else{
                        $meta_formato = $kpis["meta"]; 
                        //$seguimiento_formato = $kpis["avance_plano_kpis"];
                        $seguimiento_formato = $ClassKpisServicios->ConvertirNumeroPlanoAHorasMinutosSegundos($kpis["avance_plano_kpis"]);
                    }
                ?>

                <?php
                $sentencia = "SELECT * FROM Roles_Kpis WHERE id_empresa = '".$user_log['id_empresa']."' AND id_rol = '".$kpis['rol_colaborador']."' AND estado = 1 ";    
                $query = mysqli_query($connect_kpis, $sentencia);
                $tu_rol = "";
                $roles = [];
                if(mysqli_num_rows($query) > 0){
                    while($rol = mysqli_fetch_assoc($query)){
                        if($rol["id_rol"] == $kpis["rol_colaborador"]){
                            $tu_rol = $rol["nombre_rol"];
                        }
                    }
                }
                ?>

                <div class="accordion-item card mb-2 p-2" id="ref_<?= $kpis["id_kpi"]; ?>">
                    <div class="card-body" style="padding: 5px 20px;">
                        <div class="row">

                            <div class="col-md-4">

                                <span style="color: #007bff;">
                                    Tipo: <b><?= $kpis["tipo_txt"]; ?></b>
                                </span>

                                <h6><b><?= $kpis["txt_frecuencia"]; ?> | <?= $kpis["indicador"]; ?></b></h6>

                                <div style="color: #999999;">
                                    <span>
                                        <b>Área Macro:</b> <?= $kpis["nombre_area_macro"]; ?> <br>
                                        <b>Área Proceso:</b> <?= $kpis["area_txt"]; ?> <br>
                                        <?= !empty($kpis["subproceso_txt"]) ? "<b>Subproceso:</b> " . $kpis["subproceso_txt"] . "<br>" : ""; ?>
                                    </span><br>

                                    <?php if(isset($kpis['rol_colaborador']) && !empty($kpis['rol_colaborador'])): ?>
                                        <div>
                                            <button class="btn btn-warning btn-sm"><i class="bx bx-user"></i></button>
                                            Tu Rol: <?= $tu_rol; ?>
                                        </div>
                                    <?php endif; ?>

                                </div>

                            </div>

                            <div class="col-md-2 d-flex align-items-center text-center fs-6">
                                <div>
                                    <b>Meta:</b> <br>
                                    <b><?= $meta_formato; ?></b>
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-center text-center fs-6">
                                <div>
                                    <b>Seguimiento:</b> <br>
                                    <b><?= $seguimiento_formato; ?></b>
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-center">
                                <div class="text-left" style="width: 100%;">
                                    <div class="progress">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $kpis["avance_kpis"]; ?>%; background-color:<?= $kpis["color_avance_kpis"]; ?> !important;" aria-valuenow="<?= round($kpis["avance_kpis"]); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <b><?= round($kpis["avance_kpis"]); ?>%</b>
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-center justify-content-center">
                                <?php if($user_log["permiso_administrador_kpis"] || $VALIDAR_ROOT["editar"] ){ ?>
                                <a href="?pg=kpis/detalle/detalle_kpi&id=<?= $kpis["id_kpi"]; ?>" class="btn btn-outline-dark btn-sm mx-1">
                                    <i class="bx bx-pencil" title="Editar Okrs"></i>
                                </a>
                                <?php } ?>

                                <?php if( $user_log["permiso_administrador_kpis"] || $VALIDAR_ROOT["editar"] || $tu_rol == 'Lider KPI' ){ ?>
                                <a href="?pg=kpis/detalle/integrantes&id=<?= $kpis["id_kpi"]; ?>" class="btn btn-outline-dark btn-sm">
                                    <i class="bx bx-user-check" title="Editar Integrantes"></i>
                                </a>
                                <?php } ?>

                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#item_<?= $kpis["id_kpi"]; ?>" aria-expanded="false" aria-controls="item_<?= $vicepresidencia["id"]; ?>" style="margin-top: 14px; max-width:80px;"></button>
                            </div>

                            <!-- <div class="col-md-1 d-flex align-items-center">
									
								</div> -->

                            <!-- AQUI SE PINTA LA TABLA CON LAS AREAS -->
                            <?php
                            /*
                            if($){
                                collapse show
                            }
                                */
                            ?>
                            <div class="col-md-12">
                                <div id="item_<?= $kpis["id_kpi"]; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionVicepresidencias">

                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="card_box">
                                                Responsables: <br>
                                                <?php
                                                $limit = 0; 
                                                foreach ($kpis["integrantes"] as $integrante) {
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
                                                <b><?= $kpis["indicador"]; ?></b>
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
                                            <button type="submit" class="btn btn-success btn-sm mb-3 mx-1">Guardar Avances</button>
                                            <a href="<?php echo $url; ?>?pg=kpis/gestionar_kpi&id=<?= $kpis["id_kpi"] ?>" target="_blank"
                                                class="btn btn-warning btn-sm mb-3">
                                                Gestionar KPI
                                            </a>
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

    <div style="height: 10px;"></div>

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