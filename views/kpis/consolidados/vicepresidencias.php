<script>
	$(document).ready(function() {
		$('#menuKpis').collapse();
		$('#bt_reporte_general').addClass('active');
	});
</script>

<?php
$id_vicepresidencia = $_GET["id"];
$array_kpis_general = array();
$hoy = date("Y-m-d H:i:s");

include("app/models/kpis/KpisCrud.php");
$ClassKpisCrud = new KpisCrud();

if($_POST["guardar_avance_kpi"]){
    $ClassKpisCrud->actualizar_avance_Kpis($_POST);
}


//DATOS DE LA VICEPRESIDENCIA
//INFORMACION DE LA BATERIA
$query = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id = '".$id_vicepresidencia."' ");
$data = mysqli_fetch_array($query);

include("app/models/kpis/KpisServicios.php");
$ClassKpisServicios = new KpisServicios($user_log["id_empresa"]);
$areas_listas = $ClassKpisServicios->areas_vicepresidencia_lista($user_log["id_empresa"], $id_vicepresidencia);


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

//
$datos_consolidado_kpis = $ClassKpisServicios->datos_consolidado_kpis( $user_log["id_empresa"], $array_kpis_general);

?>

<style>
	/* Margen debajo de la barra de herramientas (botones) */
	.dt-buttons {
		margin-bottom: 15px !important;
	}

    .card_box{
        margin: 15px 0px;
        border: 1px solid #cccccc;
        padding: 15px;
        font-size: 12px;
        height: 82%;
        background-color: #f2f2f2;
        line-height: 15px;
    }
</style>

<div class="container-fluid">

    <a href="<?php echo $url; ?>?pg=kpis/generales">
        <button class="btn btn-primary mb-3">
            << Volver
        </button>
    </a>

	<!-- TITULO -->
	<div class="card mb-3">
		<div class="card-header">
			<h3>Consolidado del año 2026 para el área macro: <?php echo $data["nombre"]; ?></h3>
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

            <?php foreach($array_kpis_general as $kpis){ ?>

                <div class="accordion-item card mb-2 p-2">
				    <div class="card-body" style="padding: 5px 20px;" >
							<div class="row">

								<div class="col-md-5 ">
                                    <span style="color:#007bff;">
                                        Tipo: <b><?= $kpis["tipo_txt"]; ?></b>
                                    </span>            
									<h6><b><?= $kpis["indicador"]; ?></b></h6>
								</div>

								<div class="col-md-2 text-center fs-6">
									<b>Meta:</b> <br> 
                                    <b><?= $kpis["meta"]; ?></b>
								</div>

								<div class="col-md-2 text-center fs-6">
									<b>Seguimiento</b> <br>
                                    <b><?= number_format($kpis["avance_plano_kpis"], 0); ?>% ?></b>
								</div>

								<div class="col-md-2 d-flex align-items-center">
									<div class="text-left" style="width: 100%;">
										<div class="progress">
											<div class="progress-bar bg-success" role="progressbar" style="width: <?= $kpis["avance_kpis"]; ?>%; background-color:<?= $kpis["color_avance_kpis"]; ?> !important;" aria-valuenow="<?= round($kpis["avance_kpis"]); ?>" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
										<b><?= round($kpis["avance_kpis"]); ?>%</b>
									</div>
								</div>

								<div class="col-md-1">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#item_<?= $kpis["id_kpi"]; ?>" aria-expanded="false" aria-controls="item_<?= $vicepresidencia["id"]; ?>" style="margin-top: 14px;" ></button>
								</div>

                                <!-- AQUI SE PINTA LA TABLA CON LAS AREAS -->
								<div class="col-md-12">
									<div id="item_<?= $kpis["id_kpi"]; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionVicepresidencias">

                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="card_box">
                                                    Responsables: <br>
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
                                        if($kpis["frecuencia"] == 1 ){ include("views/kpis/consolidados/tabla_mensual.php"); }
                                        if($kpis["frecuencia"] == 2 ){ include("views/kpis/consolidados/tabla_bimestral.php"); }
                                        if($kpis["frecuencia"] == 3 ){ include("views/kpis/consolidados/tabla_trimestral.php"); }
                                        if($kpis["frecuencia"] == 6 ){ include("views/kpis/consolidados/tabla_cuatrimestral.php"); }
                                        if($kpis["frecuencia"] == 4 ){ include("views/kpis/consolidados/tabla_semestral.php"); }
                                        if($kpis["frecuencia"] == 5 ){ include("views/kpis/consolidados/tabla_anual.php"); }
                                        ?>

                                        <div class="text-end">
                                            <button type="submit" class="btn btn-success mb-3">Guardar Avances</button>
                                            <a href="<?= $kpis["url"] ?>?pg=kpis/detalle/detalle_kpi&id=<?= $kpis["id_kpi"] ?>">
                                                <button type="button" class="btn btn-warning mb-3">Gestionar KPI</button>
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

</div>


<script>
    var api = '<?php echo $url; ?>api/kpis/';
    function FichaEmpleado(id_empleado){

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
            .always(function(resp) {}
        );

        
    }
</script>