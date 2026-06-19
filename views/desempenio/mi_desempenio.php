<script>
$(document).ready(function() {
    $('#menuDesempenio').collapse();
    $('#bt_desempenio_mis_objetivos').addClass('active');
});
</script>

<style>
	.progreso-bar {
		width: 250px;
		height: 250px;
		border-radius: 50%;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.progreso-bar::before {
		/* counter-reset: percentage var(--i);
		content: counter(percentage) '%'; */
		content: var(--i-str);
	}

	.objetivo-okr {
		background:
			radial-gradient(closest-side, white 77%, transparent 80% 100%),
			conic-gradient(var(--clr) calc(var(--i) * 1%), #e9ecef 0);
		animation: objetivo-okr-progreso 2s 1 forwards;
		font-size: 3rem;
	}

	.objetivo-okr::before {
		animation: objetivo-okr-progreso 2s 1 forwards;
	}

	.progreso {
		visibility: hidden;
		width: 0;
		height: 0;
	}
</style>
<script>
	$(document).ready(function() {
		$(".menu_section").addClass("active");
		// $("#nav_desempenio").addClass("active");
		jQuery("#menu_desempenio").css("display", "none");
		$("#bt_desempenio_individual_consolidado_individual_desempenio").addClass("current-page");
	});
</script>

<?php 

if ($_POST["anio_fill"] != "") {
	$_SESSION["anio_fill"] = $_POST["anio_fill"];
}
if ($_POST["anio_fill"] == -1) {
	$_SESSION["anio_fill"] = "";
}

//DATOS DEL USUARIO
include("app/models/estructura/Colaboradores.php");
$ClassColaboradores = new Colaboradores();
$colaborador = $ClassColaboradores->colaborador( $user_log["id"] ); 

//KPIS
//KPIS
//KPIS
include("app/models/kpis/Kpis.php");
$ClassKpis = new Kpis();
$kpis = $ClassKpis->ResultadoKpis($user_log["id"], $user_log["id_empresa"], $_SESSION["anio_fill"]);

//COMPETENCIAS
//COMPETENCIAS
//COMPETENCIAS
include("views/competencias/informes/funciones.php");
$VALIDACION = PromedioGeneralEvaluado($user_log["id"], $connect_valoracion, $connect_admin);
$promedio = $VALIDACION["promedio"];

//PASAR A FUNCION GLOBAL
function ResultadoLiderCompetencias( $id_user, $anio, $ciclo ){
    
    global $connect_valoracion;
    $sentecia_val_jefe = "SELECT obj_evaluacion FROM Competencias_Evaluaciones_New WHERE anio = '".$anio."' AND id_ciclo = '".$ciclo."' AND tipo_evaluacion = 5 AND id_evaluado = '".$id_user."' ";
    $qrValjefe = mysqli_query($connect_valoracion, $sentecia_val_jefe);
    $dtValJefe = mysqli_fetch_array($qrValjefe);

    
    $prom_global = 0;
    $count_global = 0;
    $objet = json_decode( $dtValJefe["obj_evaluacion"], true);

    
    foreach($objet as $comp){

        $datos = $comp["respuestas"];
        $promedio_respuestas = 0;
        $count_resp = 0;
        
        foreach( $datos as $resp ){
            $promedio_respuestas += $resp["respuesta"];
            $count_resp++;
        }
            

        
        $promedio_general_respuestas = $promedio_respuestas/$count_resp;
        $prom_global += $promedio_general_respuestas;

        $count_global++;
        

    }

    $final = 0;
    if($prom_global > 0){
        $final = $prom_global/$count_global;
    }

    $final = ($final*100)/5;

    return $final;
    
    
}



$competencias = 0;
if($promedio > 0){
    $competencias = $promedio*100/5;
    $competencias = round($competencias,2);
}


if($VALIDACION["tipo_ponderacion"] == '180'){
    $competencias = ResultadoLiderCompetencias( $user_log["id"], $_SESSION["anio_fill"], $_SESSION['ciclo'] );
    $competencias = round($competencias,2);
}






//OKRS
//OKRS
//OKRS
include("app/models/okrs/OkrsServicios.php");
$ClassOkrsServicios = new OkrsServicios();
$okrs_objetivos_asociados = $ClassOkrsServicios->objetivos_asociados($user_log["id_empresa"], $user_log["id"], $user_log["id_area"], $_SESSION["anio_fill"]);
$datos_consolidado = $ClassOkrsServicios->datos_consolidado_okrs($user_log["id_empresa"], $okrs_objetivos_asociados);
$okrs = $datos_consolidado["promedio_general"];



//DESEMPENIO GLOBAL (NO. UNICO)
include("app/models/desempenio/Desempenio.php");
$ClassDesempenio = new Desempenio();
$numero_desempenio = $ClassDesempenio->ResultadoDesempenio($okrs, $kpis, $competencias, $colaborador["id_nivel_jerarquico"]);

$escala_home = EscalaColor(round($numero_desempenio), $_SESSION["id_empresa"], $connect_valentina);
$back_color = "background-color:" . $escala_home . " !important";

?>

<div class="container"  >

    <div class="card mb-3">
        <div class="card-body">
            <form action="" method="post">
				<div class="form-group">
					<div class="row">
						<div class="col-md-3">
							<select class="form-control" name="anio_fill">
								<option value="-1">Filtrar por Periodo...</option>
								<?php
								foreach ($Array_Anio_Desempenio as $periodo) {
									if ($_SESSION["anio_fill"] ==  $periodo[0]) {
										echo '<option value="' . $periodo[0] . '" selected>' . ((int)$periodo[1] - 1) . ' - ' . $periodo[1] . '</option>';
									} else {
										echo '<option value="' . $periodo[0] . '">' . ((int)$periodo[1] - 1) . ' - ' . $periodo[1] . '</option>';
									}
								}
								?>
							</select>
						</div>
						<div class="col-md-2" style="text-align: right;">
							<button type="submit" class="btn btn-primary w-100">Filtrar</button>
						</div>
					</div>
				</div>
			</form>
        </div>
    </div>


    <ul class="nav nav-pills justify-content-center">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="?pg=desempenio/mi_desempenio">Mi Desempeño</a>
        </li>
        <?php if ($VALIDAR_MENU["desempenio_mi_equipo"]) { $mstrr_1 = true; ?>
        <li class="nav-item">
            <a class="nav-link" href="?pg=desempenio/consolidado_equipo">Desempeño Equipo</a>
        </li>
        <?php } ?>
    </ul>

    <div class="card mb-3">
        <div class="card-header">
            <h3>MI DESEMPEÑO</h3>
        </div>
        <div class="card-body">
            <div class="row row-cols-sm-1 row-cols-md-5">
						<div class="col">
							<div class="card mb-2">
								<div class="card-header alert-success" style="color: white;text-align: center;">Vicepresidencia</div>
								<div class="card-body" style="text-align: center;"><?php echo $colaborador["nombre_vicepresidencia"]; ?></div>
							</div>
						</div>
						<div class="col">
							<div class="card mb-2">
								<div class="card-header alert-success" style="color: white;text-align: center;">Área</div>
								<div class="card-body" style="text-align: center;"><?php echo $colaborador["nombre_area"]; ?></div>
							</div>
						</div>
						<div class="col">
							<div class="card mb-2">
								<div class="card-header alert-success" style="color: white;text-align: center;">Unidad Organizativa</div>
								<div class="card-body" style="text-align: center;"><?php echo $colaborador["nombre_unidad"]; ?></div>
							</div>
						</div>
						<div class="col">
							<div class="card mb-2">
								<div class="card-header alert-success" style="color: white;text-align: center;">Nivel Jerárquico</div>
								<div class="card-body" style="text-align: center;"><?php echo $colaborador["nombre_nivel_jerarquico"]; ?></div>
							</div>
						</div>
						<div class="col">
							<div class="card mb-2">
								<div class="card-header alert-success" style="color: white;text-align: center;">Cargo</div>
								<div class="card-body" style="text-align: center;"><?php echo $colaborador["nombre_cargo"]; ?></div>
							</div>
						</div>

			</div>
 
            <div class="row">
						<div class="col-md-12" style="text-align: center;">
							<h3># Único de desempeño</h3>
						</div>
			</div>
			<div class="progresos" data-bs-toggle="tooltip" align="center" style="background-color: #e9ecef;">
						<h1 style="font-size: 3.5rem;color: black !important; font-weight: bold;"><?php echo round($numero_desempenio,2); ?>%</h1>
			</div>
			<div class="progress-bar bg-success" role="progressbar" style=" width: <?php echo $numero_desempenio; ?>%; <?php echo $back_color; ?>;z-index: 2;margin-top: -76px;height: 66px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
            </div>
        </div>

        <div class="card-body">

            <div class="row">


                            <div class="col-md-4">
								<div class="card">
									<div class="card-body" style="align-self: center;">
										<div class="progreso-bar-container"
											style="--i:<?php echo $okrs; ?>;
													--i-str:'<?php echo number_format($okrs, 2); ?>%';
													--clr:<?php echo EscalaColor(round($okrs)); ?>;
													font-weight: bold;">
											<div class="progreso-bar objetivo-okr">
												<progreso id="objetivo-okr" min="0" value="<?php echo $okrs; ?>"></progreso>
											</div>
										</div>
									</div>
									<div class="card-footer" style="text-align: center;background-color: white !important;">
										<h5>Resultado de mis OKRs</h5>
									</div>
								</div>
							</div>


							<div class="col-md-4">
								<div class="card">
									<div class="card-body" style="align-self: center;">
										<div class="progreso-bar-container" style="--i:<?php echo $kpis; ?>;
													--i-str:'<?php echo number_format($kpis, 2); ?>%';
													--clr:<?php echo EscalaColor(round($kpis)); ?>;
													font-weight: bold;">
											<div class="progreso-bar objetivo-okr">
												<progreso id="objetivo-okr" min="0" value="<?php echo round($kpis); ?>"></progreso>
											</div>
										</div>
									</div>
									<div class="card-footer" style="text-align: center;background-color: white !important;">
										<h5>Resultado de mis KPIs</h5>
									</div>
								</div>
							</div>


							<div class="col-md-4">
								<div class="card">
									<div class="card-body" style="align-self: center;">
										<div class="progreso-bar-container" style="--i:<?php echo $competencias; ?>;
													--i-str:'<?php echo number_format($competencias, 2); ?>%';
													--clr:<?php echo EscalaColor($competencias); ?>;
													font-weight: bold;">
											<div class="progreso-bar objetivo-okr">
												<progreso id="objetivo-okr" min="0" value="<?php echo $competencias; ?>"></progreso>
											</div>
										</div>
									</div>
									<div class="card-footer" style="text-align: center;background-color: white !important;">
										<h5>Resultado de mis Competencias</h5>
									</div>
								</div>
							</div>


			</div>

        </div>
    </div>
</div>



