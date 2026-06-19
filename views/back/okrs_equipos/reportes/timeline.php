<script>
    // $("#bt_okrs_timeline").addClass("active_item");
	// $("#nav_visualizaciones").addClass("menu-is-opening menu-open");
	$(".menu_section").addClass("active");	
	// $("#nav_visualizaciones").addClass("active");
	jQuery("#menu_visualizaciones").css("display", "none");
	$("#bt_okrs_timeline").addClass("current-page");
</script>

<?php
	$hoy = date("Y-m-d H:i:s");
	$ahora = date("Y-m-d");

	if($_POST["equipo_fill"]){
		$_SESSION["id_objetivo_time"] = $_POST["equipo_fill"];
	}

	if($_POST["tipo_fill_okr"]){
		$_SESSION["tipo_reporte_timeline"] = $_POST["tipo_fill_okr"];
	}
	
	if($_POST["periodo_fill"]){
		//$_SESSION["periodo_fill"] = $_POST["periodo_fill"];
	}

	$_SESSION["Q1"] = "";
	$_SESSION["Q2"] = "";
	$_SESSION["Q3"] = "";
	$_SESSION["Q4"] = "";
	$_SESSION["Anual"] = "";
	$filtro_periodo = "";
	if ($_POST["Q1"] != "") {
		
		if ($filtro_periodo != "") {
			$filtro_periodo .= ", 'Q1'";
		} else {
			$filtro_periodo .= "'Q1'";			
		}
		$_SESSION["Q1"] = $_POST["Q1"];
	}
	if ($_POST["Q2"] != "") {
		if ($filtro_periodo != "") {
			$filtro_periodo .= ", 'Q2'";
		} else {
			$filtro_periodo .= "'Q2'";
		}
		$_SESSION["Q2"] = $_POST["Q2"];
	}
	if ($_POST["Q3"] != "") {
		if ($filtro_periodo != "") {
			$filtro_periodo .= ", 'Q3'";
		} else {
			$filtro_periodo .= "'Q3'";
		}
		$_SESSION["Q3"] = $_POST["Q3"];
	}
	if ($_POST["Q4"] != "") {
		if ($filtro_periodo != "") {
			$filtro_periodo .= ", 'Q4'";
		} else {
			$filtro_periodo .= "'Q4'";
		}
		$_SESSION["Q4"] = $_POST["Q4"];
	}
	if ($_POST["Anual"] != "") {
		if ($filtro_periodo != "") {
			$filtro_periodo .= ", 'Anual'";
		} else {
			$filtro_periodo .= "'Anual'";
		}
		$_SESSION["Anual"] = $_POST["Anual"];
	}

	$_SESSION["periodo_fill"] = $filtro_periodo != "" ? $filtro_periodo : "";

	$filtro_key = "";
	// if($_POST["periodo_fill"] ){ $filtro_key = " AND periodo = '".$_POST["periodo_fill"]."' "; }
	// $filtro_key = " AND periodo IN (".$filtro_periodo.") ";
	$filtro = "";
	// if($_POST["equipo_fill"]){ $filtro .= " AND id_okrs = '".$_POST["equipo_fill"]."' "; } 
	// if($_POST["periodo_fill"]){ $filtro .= " AND periodo = '".$_POST["periodo_fill"]."' "; }

	if($filtro_periodo){
		$filtro_key = " AND periodo IN (".$filtro_periodo.") ";
		$filtro = " AND periodo IN (".$filtro_periodo.") ";
	}

	

	$ARRAY_OKRS = array();
	$ARRAY_COLABORADORES = array();
	
	//BUSCAMOS TODOS LOS OKRS DEL COLABORADOR LOGUEADO
	$queryOkrsEquipo = mysqli_query($connect_okrs,"SELECT * FROM Equipos_Views WHERE id_empresa = '".$dtEmpleado['id_empresa']."' AND id_empleado = '".$dtEmpleado["id"]."' ");
	while($dataOkrsEquipo = mysqli_fetch_array($queryOkrsEquipo)){

		$obj = array(
			"objetivo" => $dataOkrsEquipo["objetivo_okr"],
			"id" => $dataOkrsEquipo["id_okrs"], 
			"fecha_inicia" => $dataOkrsEquipo["fecha_inicia"], 
			"fecha_termina" => $dataOkrsEquipo["fecha_termina"], 
			"tipo" => $dataOkrsEquipo["tipo"], 
		);
		array_push($ARRAY_OKRS, $obj );
		
		$queryColaboradores = mysqli_query($connect_okrs,"SELECT * FROM Okrs_Equipos WHERE id_okrs = '".$dataOkrsEquipo['id']."' GROUP BY id_empleado  ");
		while($dataColaboradores = mysqli_fetch_array($queryColaboradores)){
			
			$queryEmple = mysqli_query($connect_valentina,"SELECT * FROM Empleados 
			WHERE id = '".$dataColaboradores['id_empleado']."' ");
			$dataEmple = mysqli_fetch_array($queryEmple);
			
			$obj_c = array(
				"id" => $dataEmple["id"],
				"nombre" => $dataEmple["nombre"], 
			);
			
			array_push($ARRAY_COLABORADORES, $obj_c  );
		}
	}

	//FUNCION PARA OBTENER LOS AÑOS
	function Dias($fecha_inicia, $fecha_termina){
		$firstDate = $fecha_inicia;
		$secondDate = $fecha_termina;
		$dateDifference = abs(strtotime($secondDate) - strtotime($firstDate));
		
		$dias  = floor($dateDifference / ( 60 * 60 * 24));

		return $dias*22 ;
	}

	function DiferenciaDias( $fecha_inicia, $fecha_termina ){
		$date1 = date_create( $fecha_inicia );
		$date2 = date_create( $fecha_termina );
		$diff = date_diff($date1,$date2);
		
		if($_SESSION["tipo_reporte_timeline"] == 1){
			
			return $diff->format('%a');
		}
		if($_SESSION["tipo_reporte_timeline"] == 2){
			return $diff->format('%a')/7;
		}
		if($_SESSION["tipo_reporte_timeline"] == 3){
			return $diff->format('%m');
		}
		if($_SESSION["tipo_reporte_timeline"] == 4){
			$dato = $diff->format('%m')/6;
			$dato = ceil($dato);
			return $diff->format('%m')/6;
		}
	}

	//DATOS DEL OKRS
	$queryOKRs = mysqli_query($connect_okrs,"SELECT * FROM Okrs 
	WHERE id = '".$_SESSION["id_objetivo_time"]."' ");
	$dataOKRs = mysqli_fetch_array($queryOKRs);
	
	//DATOS DEL EMPLEADO QUE LO CREA
	$queryEmpleado = mysqli_query($connect_valentina,"SELECT * FROM Empleados WHERE id =   '".$dataOKRs["id_empleado"]."' ");
	$dataEmpleado = mysqli_fetch_array($queryEmpleado);

	//DATOS DEL TOTAL DE DIAS
	$total =  DiferenciaDias( $dataOKRs["fecha_inicia"], $dataOKRs["fecha_termina"] );
	//$total_dias =  Dias($dataOKRs["fecha_inicia"], $dataOKRs["fecha_termina"] );

	$txt_tipo = "";
	if($_SESSION["tipo_reporte_timeline"] == 1){ $txt_tipo = "Día"; $w_celda = 60; }
	if($_SESSION["tipo_reporte_timeline"] == 2){ $txt_tipo = "Semana"; $w_celda = 120; }
	if($_SESSION["tipo_reporte_timeline"] == 3){ $txt_tipo = "Mes"; $w_celda = 300; }
	if($_SESSION["tipo_reporte_timeline"] == 4){ $txt_tipo = "Semestre"; $w_celda = 600; }

	

?>

<style>
	.lista_key{
		border: 1px solid #cccccc;
		padding: 6px;
		margin-bottom: 5px;
	}
	
	.resultados{
		width: 100%;
		border-collapse: inherit;
	}
	
	.resultados td{
		background-color: #f4f6f8;
    	padding: 1px 10px;
	}
	
	.bloque_objetivo_k{
		/*background-color: #f1f1f1;*/
    	color: #365189;
		border-bottom: 1px solid #dadada;
	}
	
	.bloque_actividades{
		padding: 0px !important;
	}
	
	.table th, .table td {
		padding: 5px 10px;
		vertical-align: middle;
		border-top: 0;
	}
	
	.fill_resultados{
	}
	.fill_iniciativas{
	}
	.corporativos{
	}
	.equipos{
	}
	.sub_periodo{
		font-size: 12px;
		color: #a3a3a3;
		margin-top: 4px;
	}
	
	.master{
		overflow: auto;
	}
	
	.tablero{
		/*width: <?php echo $total*22; ?>px;*/
		width: fit-content;
    	min-width: fit-content;
		overflow: auto;
	}
	.titulo_objetivo{
		background-color: #d7e4ff;
		padding: 15px 10px;
		color: #365189;
	}
	.resultados{
		background-color: #e6e6eb;
		position: relative;
		padding: 5px 10px;
		margin: 10px 0px;
		border: 1px solid #cccccc;
		min-width: fit-content;
	}
	.iniciativas{
		background-color: #fcfcfc;
		position: relative;
		padding: 3px 10px;
		margin: 8px 0px;
		color: #1f1f1f;
		font-size: 12px;
		border-right: 3px solid #8bc34a;
		min-width: fit-content;
	}
	.fechas{
		font-size: 12px;
		color: #6F6F6F;
	}
</style>

<?php include("views/okrs/layouts/modal_crear_okr.php"); 
include("views/okrs/layouts/modal_okr.php");
$querySM102 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 10 AND id_submenu = 58");
$dataSM102 = mysqli_fetch_array($querySM102);
?>
<style>
	.card, .card-header, .card-body, .card-footer{
		background-color: white !important;
	}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col-md-12">
						<h3><i class="fas fa-tasks" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo mb_strtoupper($dataSM102["nombre"]); ?></h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br>
<div class="container-fluid">

	<form action="" method="post" id="formulario_filtro">
	<div class="row">
		
		
		
		<div class="col-md-4" >
			<select class="form-control form-control-sm" name="equipo_fill" onChange="Filtrar()">
				<option value="">Filtrar OKRs...</option>
				<?php
					foreach($ARRAY_OKRS as $okrs){
						if( $_SESSION["id_objetivo_time"] ==  $okrs["id"] ){
							echo '<option value="'.$okrs["id"].'" selected>'.$okrs["objetivo"].'</option>';
						}
						else{
							echo '<option value="'.$okrs["id"].'">'.$okrs["objetivo"].'</option>';
						}
					}
				?>
			</select>
		</div>
		
		<div class="col-md-4" >
			<select class="form-control form-control-sm" name="tipo_fill_okr" onChange="Filtrar()">
				<option value="">Seleccione tipo de reporte...</option>
				<?php
					foreach($Array_Tipo_Timeline as $tipo){
						if( $_SESSION["tipo_reporte_timeline"] ==  $tipo[0] ){
							echo '<option value="'.$tipo[0].'" selected>'.$tipo[1].'</option>';
						}
						else{
							echo '<option value="'.$tipo[0].'">'.$tipo[1].'</option>';
						}
					}
				?>
			</select>
		</div>
		
		<div class="col-md-4" >
			<!-- <select class="form-control form-control-sm" name="periodo_fill" onChange="Filtrar()">
				<option value="">Selecciona periodo...</option>
				<?php
					// foreach($Array_Periodos_Q as $periodo){
					// 	if( $_POST["periodo_fill"] ==  $periodo[0] ){
					// 		echo '<option value="'.$periodo[0].'" selected>'.$periodo[1].'</option>';
					// 	}
					// 	else{
					// 		echo '<option value="'.$periodo[0].'">'.$periodo[1].'</option>';
					// 	}
					// }
				?>
			</select> -->
			<?php
				$check1 = $_SESSION["Q1"] != "" ? "checked" : "";
				$check2 = $_SESSION["Q2"] != "" ? "checked" : "";
				$check3 = $_SESSION["Q3"] != "" ? "checked" : "";
				$check4 = $_SESSION["Q4"] != "" ? "checked" : "";
				$check5 = $_SESSION["Anual"] != "" ? "checked" : "";
			?>
			<div class="row">
				<div class="col-md-2">
					<label for="" style="color: black;">Periodo...</label>
				</div>
				<div class="col-md-2">
					<input type="checkbox" name="Q1" id="Q1" <?php echo $check1; ?> onChange="Filtrar()"> Q1
				</div>
				<div class="col-md-2">
					<input type="checkbox" name="Q2" id="Q2" <?php echo $check2; ?> onChange="Filtrar()"> Q2
				</div>
				<div class="col-md-2">
					<input type="checkbox" name="Q3" id="Q3" <?php echo $check3; ?> onChange="Filtrar()"> Q3
				</div>
				<div class="col-md-2">
					<input type="checkbox" name="Q4" id="Q4" <?php echo $check4; ?> onChange="Filtrar()"> Q4
				</div>
				<div class="col-md-2">
					<input type="checkbox" name="Anual" id="Anual" <?php echo $check5; ?> onChange="Filtrar()"> Anual
				</div>
			</div>
		</div>
		
		

	</div>
	</form>
	
	
	
	
	<div class="row">
		
		

		<div class="col-md-12" align="right" >
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="checkbox" onChange="VerSecciones(this,3)" checked>
				<label class="form-check-label" >Ver Resultados</label>
			</div>
			<div class="form-check form-check-inline">
				<input class="form-check-input" type="checkbox" onChange="VerSecciones(this,4)" checked>
				<label class="form-check-label" >Ver Iniciativas</label>
			</div>
		</div>

	</div>
	
	<style>
		/* width */
		::-webkit-scrollbar {
		  width: 8px;
			height: 8px;
		}

		/* Track */
		::-webkit-scrollbar-track {
		  background: #f1f1f1; 
		}

		/* Handle */
		::-webkit-scrollbar-thumb {
		  background: #888; 
		  border-radius: 10px;
		}

		/* Handle on hover */
		::-webkit-scrollbar-thumb:hover {
		  background: #ccc; 
		}
	</style>
	
	<style>
		.guia_dia{
			background-color: #ebebf0;
			width: <?php echo $w_celda; ?>px;
			text-align: center;
			display: table-cell;
			font-size: 12px;
			padding: 5px 0px;
			margin: 0px;
			line-height: 13px;
			border-right: 1px solid #cecece;
		}
	</style>
	
	
	
	<?php if($_SESSION["id_objetivo_time"] && $_SESSION["tipo_reporte_timeline"] ){ ?>
	<div class="row">
		
		<div class="col-md-12" style="margin-top: 20px"> 
			
			<div class="card">	
				
				<div class="card-header">
					<button type="button" class="btn btn-primary btn-sm bt_editar" onClick="VerOKRs(<?php echo $dataOKRs["id"]; ?>)" data-bs-toggle="tooltip" title="Vista rápida del OKRs" style="float: right;" >
						<i class="fa fa-check" ></i>
					</button>
					<h4>Objetivo: <?php echo $dataOKRs["objetivo_okr"]; ?></h4>
					<?php echo FechaAmigable($dataOKRs["fecha_inicia"]); ?> a <?php echo FechaAmigable($dataOKRs["fecha_termina"]); ?> ||
					Publicado por: <b><?php echo $dataEmpleado["nombre"]." ".$dataEmpleado["apellidos"]; ?></b> 
				</div>
				<div class="master">
					<div class="tablero" style="width: <?php echo $total*$w_celda; ?>px ">
						
						<table width="100%" style="background-color: #fafafa;">
							<tr>
								<td style="padding: 5px 10px"><b><?php echo FechaAmigable($dataOKRs["fecha_inicia"]); ?></b></td>
								<td style="padding: 5px 10px" align="right"><b><?php echo FechaAmigable($dataOKRs["fecha_termina"]); ?></b></td>
							</tr>
						</table>
						
						<?php
						for ($i = 1; $i <= $total; $i++) {
							echo '
							<div class="guia_dia">
								'.$txt_tipo.'<br>
								<b>'.$i.'</b>
							</div>
							';
							$i;
						}
						?>

						<?php
						//RECORREMOS LOS KR
						$queryResultados = mysqli_query($connect_okrs,"SELECT * FROM Okrs_Resultados 
						WHERE id_okrs = '".$_SESSION["id_objetivo_time"]."' ".$filtro_key." ");
						
						while($dataResultados = mysqli_fetch_array($queryResultados)){
							
							$dias_resultado =  DiferenciaDias( $dataOKRs["fecha_inicia"], $dataResultados["fecha_inicia"] );
							
							$dias_duracion =  DiferenciaDias( $dataResultados["fecha_inicia"], $dataResultados["fecha_entrega"]  );
						?>

							<div class="resultados" style=" left: <?php echo $dias_resultado*$w_celda; ?>px; width: <?php echo $dias_duracion*$w_celda; ?>px;">
								<b><?php echo $dataResultados["descripcion"]; ?></b>
								<div class="fechas"><?php echo FechaAmigable($dataResultados["fecha_inicia"]); ?> al <?php echo FechaAmigable($dataResultados["fecha_entrega"]); ?></div>

								<?php
								$queryIniciativas = mysqli_query($connect_okrs,"SELECT * FROM Okrs_Iniciativas 
								WHERE id_resultado = '".$dataResultados["id"]."' ");
								while($dataIniciativas = mysqli_fetch_array($queryIniciativas)){	
									$dias_iniciativa =  DiferenciaDias( $dataOKRs["fecha_inicia"], $dataResultados["fecha_inicia"] );
									$duracion_iniciativa =  DiferenciaDias( $dataResultados["fecha_inicia"], $dataIniciativas["fecha_entrega"] );
								?>

									<div class="iniciativas" style=" width: <?php echo $duracion_iniciativa*70; ?>px">
										<b><?php echo $dataIniciativas["descripcion"]; ?></b><br>
										Fecha de entrega: <b><?php echo FechaAmigable($dataIniciativas["fecha_entrega"]); ?></b>
									</div>

								<?php } ?>
							</div>

						<?php } ?>
								
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php } else{ ?>
	<div class="alert alert-info" role="alert" align="center">
		Aquí puede realizar un seguimiento individual en el tiempo a sus OKRs.<br><br>
	  	<b> Por favor seleccione un ORKs de la lista desplegable.</b>
	</div>
	<?php } ?>
	
</div>



<script>
	var api = '<?php echo $url; ?>api/okrs/';

	function Guardar_Avance_Iniciativa(avance, id){
		data = {
			id: id,
			avance: avance,
		};
		jQuery.ajax({
                url: api+"guardar_avance_iniciativa.php",
                type:'post',
                data: data,
                }).done(function (resp){
                    $("#xscript").html(resp);
					location.reload();
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp){
                }
		);     
	}
	
	function VerSecciones(elem, filtro){
		clase = "";
		if(filtro == 1){ clase = "corporativos"; }
		if(filtro == 2){ clase = "equipos"; }
		if(filtro == 3){ clase = "resultados"; }
		if(filtro == 4){ clase = "iniciativas"; }
		if(filtro == 5){ clase = "fill_planes"; }

		if( $(elem).prop('checked')  ){
			$("."+clase).show();
		}
		else{
			$("."+clase).hide();
		}
	}
	
	function Filtrar(){
		$("#formulario_filtro").submit();
	}
	
	function VerOKRs(id){
		jQuery.ajax({
        	url: api+"ver_okr.php",
        	type:'post',
        	data: {id: id},
        	}).done(function (resp){
        		$("#modal_okr").modal("show");
        		$("#modal_contenido").html(resp);
        	})
        	.fail(function(resp) {
        		console.log(resp);
        	})
        	.always(function(resp){
        	}
		);
		

	}
	
	function VerResultado(id){
		jQuery.ajax({
        	url: api+"ver_resultados.php",
        	type:'post',
        	data: {id: id},
        	}).done(function (resp){
        		$("#modal_okr").modal("show");
        		$("#modal_contenido").html(resp);
        	})
        	.fail(function(resp) {
        		console.log(resp);
        	})
        	.always(function(resp){
        	}
		);
	}
	
	function VerIniciativa(id, id_iniciativa){
		jQuery.ajax({
        	url: api+"editar_iniciativa.php",
        	type:'post',
        	data: {id: id, id_iniciativa: id_iniciativa, id_empresa: <?php echo $dtEmpleado["id_empresa"]; ?>},
        	}).done(function (resp){
        		$("#modal_okr").modal("show");
        		$("#modal_contenido").html(resp);
        	})
        	.fail(function(resp) {
        		console.log(resp);
        	})
        	.always(function(resp){
        	}
		);
	}
	function Editar_Resultado(id){
		jQuery.ajax({
        	url: api+"editar_resultado.php",
        	type:'post',
        	data: {id: id},
        	}).done(function (resp){
        		$("#modal_okr").modal("show");
        		$("#modal_contenido").html(resp);
        	})
        	.fail(function(resp) {
        		console.log(resp);
        	})
        	.always(function(resp){
        	}
		);
	}
	
	function Ver_Comentarios(id){
		jQuery.ajax({
        	url: api+"ver_comentarios.php",
        	type:'post',
        	data: {id: id},
        	}).done(function (resp){
        		$("#modal_okr").modal("show");
        		$("#modal_contenido").html(resp);
        	})
        	.fail(function(resp) {
        		console.log(resp);
        	})
        	.always(function(resp){
        	}
		);
	}
	
	function Ver_Planes_accion(id){
		jQuery.ajax({
        	url: api+"ver_planes_accion.php",
        	type:'post',
        	data: {id: id},
        	}).done(function (resp){
        		$("#modal_okr").modal("show");
        		$("#modal_contenido").html(resp);
        	})
        	.fail(function(resp) {
        		console.log(resp);
        	})
        	.always(function(resp){
        	}
		);
	}
</script>
