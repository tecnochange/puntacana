<script>
	$(document).ready(function() {
		$("#bt_comp_administrar").addClass("active");
		$("#mod_competencias").addClass("active_qa");
	});
</script>

<script src="<?php echo $url; ?>assets/js/highcharts/code/highcharts.js"></script>
<script src="<?php echo $url; ?>assets/js/highcharts/code/highcharts-more.js"></script>
<script src="<?php echo $url; ?>assets/js/highcharts/code/modules/exporting.js"></script>
<script src="<?php echo $url; ?>assets/js/highcharts/code/modules/export-data.js"></script>
<script src="<?php echo $url; ?>assets/js/highcharts/code/modules/accessibility.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<?php 

$hoy = date("Y-m-d H:i:s");
$id_evaluado = $user_log["id"];


include("views/competencias/informes/funciones.php");
include("views/competencias/modal_competencias.php");
include("views/competencias/modal_firma.php");

//OBTENEMOS EL PROMEDIO GENERAL
$VALIDACION = PromedioGeneralEvaluado($id_evaluado, $connect_valoracion, $connect_admin);
$datos_ponderar = $VALIDACION["datos_ponderar"];

$promedio_general_evaluacion = $VALIDACION["promedio"];
$promedio_general_evaluacion_porcentaje = 0;
if($promedio_general_evaluacion > 0){
    $promedio_general_evaluacion_porcentaje = $promedio_general_evaluacion*100/5;
    $promedio_general_evaluacion_porcentaje = round($promedio_general_evaluacion_porcentaje,1);
}

//OBTENEMOS LOS EVALUADORES
$datos_generales = ValidarEvaluacionesCompletas($id_evaluado, $connect_valoracion, $connect_admin);

$lista_array_evaludadores = $datos_generales["dato_evaluadores"];

$resp_promedio = PromedioCompetencias($id_evaluado, $connect_valoracion, $connect_admin);

//OBTENEMOS LOS TIPOS DE EVALUACION AGRUPADA
$array_agrupados = array();
foreach ($lista_array_evaludadores as $key => $obj_evaluador) {
	if ($obj_evaluador["promedio"] != "") {
		array_push($array_agrupados,  $obj_evaluador["tipo"]);
	}
}
$array_agrupados = array_unique($array_agrupados);

$array_final_grupos = array();
foreach ($array_agrupados as $obj) {
	array_push($array_final_grupos, array("tipo" => $obj["tipo"], "promedio" => 0, "cantidad" => 0));
}

//TODAS LAS EVALUACIONES DEL EVALUADO
$EVALUACIONES = array();
$COMPETENCIAS = array();
$fecha_evaluacion = "";

$comentarios_finales = "";
$comentarios_mejoras = "";
$id_cargo = 0;

$queryEvaluaciones = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND
    id_evaluado = '" . $id_evaluado . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND estado >= 2
	ORDER BY created_at DESC ");

$queryLider = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empleado = '" . $id_evaluado . "'");
$dataLider = mysqli_fetch_array($queryLider);

///VALIDACION PARA BUSCAR UNA EVALUACION DEL JEFE
///VALIDACION PARA BUSCAR UNA EVALUACION DEL JEFE
$queryEvaluacionesCompJefe = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND
    id_evaluado = '" . $id_evaluado . "' AND tipo_evaluacion = 5 AND anio = '" . $_SESSION['anio_ciclo'] . "' AND estado >= 2
	ORDER BY created_at DESC ");
$competenciasEvalJefe = mysqli_fetch_array($queryEvaluacionesCompJefe);
///VALIDACION PARA BUSCAR UNA EVALUACION DEL JEFE
///VALIDACION PARA BUSCAR UNA EVALUACION DEL JEFE

    

$queryEvaluacionesComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND
    id_evaluado = '" . $id_evaluado . "' AND tipo_evaluacion = 5 AND anio = '" . $_SESSION['anio_ciclo'] . "' AND estado >= 2
	ORDER BY created_at DESC ");



$competenciasEval = mysqli_fetch_array($queryEvaluacionesComp);
$keyEvaluado = 0;
$keyEvaluacion = 0;
$keyEmpresa = $_SESSION['id_empresa'];
$ciclo = $_SESSION['ciclo'];
$procesoValoracionActual = 0;
while ($dataEvaluacion = mysqli_fetch_array($queryEvaluaciones)) {
	$procesoValoracionActual = $dataEvaluacion["proceso_valoracion"];
	$keyEvaluado = $dataEvaluacion["id_evaluado"];
	$keyEvaluacion = $dataEvaluacion["id"];
	if ($dataEvaluacion["observaciones"]) {
		$comentarios_finales .= "<div>* " . $dataEvaluacion["observaciones"] . "</div>";
	}

	if ($dataEvaluacion["mejoras"]) {
		$comentarios_mejoras .= "<div>* " . $dataEvaluacion["mejoras"] . "</div>";
	}

	$id_cargo = $dataEvaluacion["id_cargo"];
	$fecha_evaluacion = $dataEvaluacion["created_at"];

	$Array_Objeto = json_decode($dataEvaluacion["obj_evaluacion"], true);
	foreach ($Array_Objeto as $respuestas) {
		array_push($COMPETENCIAS, $respuestas["competencia"]);
	}
	$datos = array(
		"id_evaluador" => $dataEvaluacion["id_evaluador"],
		"obj_evaluacion" => $dataEvaluacion["obj_evaluacion"],
		"tipo_evaluacion" => $dataEvaluacion["tipo_evaluacion"]
	);
	array_push($EVALUACIONES, $datos);
}

$COMPETENCIAS = array_unique($COMPETENCIAS);

//Traer datos Intervención GH
if ($procesoValoracionActual == 8) {
	$datosIntervencion = "SELECT id_responsable, id_evaluado, comentario, created_at FROM `Intervencion_Gh` WHERE id_empresa = $keyEmpresa AND id_evaluado = $keyEvaluado AND evaluaciones = $keyEvaluacion AND ciclo = $ciclo LIMIT 1";
	echo "<script>console.log('Valor de miVariable: " . addslashes($datosIntervencion) . "');</script>";
	$sql = mysqli_query($connect_valoracion, $datosIntervencion);
	$resultIntervencion = mysqli_fetch_array($sql);
	$comentarioGh = $resultIntervencion['comentario'];
	$fechaGh = date('d/m/Y H:i', strtotime($resultIntervencion['created_at']));
	$nombreResponsable = '';
	// Datos básicos del responsable
	$queryNombre = "SELECT id,nombre FROM Empleados WHERE id = " . $resultIntervencion['id_responsable'] . " LIMIT 1";
	$sqlResponsable = mysqli_query($connect_admin, $queryNombre);
	$resultDataResponsable = mysqli_fetch_array($sqlResponsable);
	$nombreResponsable = $resultDataResponsable['nombre'];
	$IdentResponsable = $resultDataResponsable['id'];
}

$permitir_auto = false;
$permitir_jefe = false;
$permitir_cliente = false;
$permitir_par = false;
$permitir_col = false;

foreach ($EVALUACIONES as $evaluador) {
	if ($evaluador["tipo_evaluacion"] == 1) {
		$permitir_auto = true;
	}
	if ($evaluador["tipo_evaluacion"] == 2) {
		$permitir_par = true;
	}
	if ($evaluador["tipo_evaluacion"] == 3) {
		$permitir_col = true;
	}
	if ($evaluador["tipo_evaluacion"] == 4) {
		$permitir_cliente = true;
	}
	if ($evaluador["tipo_evaluacion"] == 5) {
		$permitir_jefe = true;
	}
}
/*
// include("app/models/Collaborators.php");
// $ClassCollaborators = new Collaborators();
// $colaborador =  $ClassCollaborators->collaborator_resumen($id_evaluado, $connect_admin);
*/

$filtros = " ";
if ($_POST["nombre"]) {
	$filtros .= " AND Empleados.nombre LIKE '%" . $request["nombre"] . "%' ";
}
if ($id_evaluado) {
	$filtros .= " AND Empleados.id = '" . $id_evaluado . "' ";
}


//CONSULTA DE LOS EMPLEADOS ACTIVOS
$sentencia = "
SELECT
	Empleados.id AS id, Empleados.id_empresa AS id_empresa, Empleados.nombre AS nombre, Empleados.correo AS correo_corporativo, 
    Empleados.correo_personal AS correo_personal, Empleados.telefono_movil AS celular, Empleados.documento AS documento, 
    Empleados.role AS role, Empleados.estado AS estado,
	Empleados.foto AS foto, Vicepresidencia.nombre AS nombre_vp,
	Posiciones.id AS id_posicion, Cargos.id AS id_cargo, Cargos.nombre AS nombre_cargo, Areas.nombre AS nombre_area, Areas.id AS id_area
FROM Empleados
	LEFT JOIN Posiciones ON Empleados.id_posicion = Posiciones.id
	LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
	LEFT JOIN Areas ON Areas.id = Empleados.area
	LEFT JOIN Vicepresidencia ON Vicepresidencia.id = Empleados.unidad_corporativa
WHERE Empleados.id > 0 " . $filtros . " AND Empleados.id_empresa = '" . $user_log["id_empresa"] . "'
	ORDER BY Empleados.nombre ASC
";
$queryColaborador = mysqli_query($connect_admin, $sentencia);
$colaborador = mysqli_fetch_array($queryColaborador);




//DATOS DEL EVALUADO
//DATOS DEL EVALUADO
$queryEvaluado = mysqli_query($connect_admin, "SELECT Empleados.nombre AS nombre, Cargos.nombre AS nombre_cargo
    FROM Empleados
	LEFT JOIN Posiciones ON Posiciones.id = Empleados.id_posicion
    LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
    WHERE Empleados.id = '" . $id_evaluado . "'");
$dataEvaluado = mysqli_fetch_array($queryEvaluado);

$queryCargoHistorico = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE id = '" . $id_cargo . "'");
$dataCargoHistorico = mysqli_fetch_array($queryCargoHistorico);

//PERFIL DEL CARGO NIVEL COMPETENCIA
//PERFIL DEL CARGO NIVEL COMPETENCIA
$queryCargo = mysqli_query($connect_valoracion, "SELECT * FROM Perfiles_Cargos
	WHERE id = '" . $id_evaluado . "' ");
$dataCargo = mysqli_fetch_array($queryCargo);
$perfiles = explode(",", $dataCargo["perfil"]);

$queryEscalaInter = mysqli_query($connect_valoracion, "SELECT * FROM Escalas_Interpretacion WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ");
$dataEscalaInter = mysqli_fetch_array($queryEscalaInter);

$rangos = $dataEscalaInter;

$queryLider = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empleado = '" . $id_evaluado . "'");
$dataLider = mysqli_fetch_array($queryLider);







if ($_POST["guardar_plan_accion"] != "") {

	// print_r($_POST);

	$queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $_POST["id_competencia"] . "' ");
	$dataComp = mysqli_fetch_array($queryComp);

	if ($_POST["id_plan"] != "") {
		$sentencia_apa = "UPDATE Pdi_Competencias SET
		prioridad = " . $_POST["prioridad"] . ",
		fecha_inicia = '" . $_POST["fecha_inicia"] . "',
		fecha_finaliza = '" . $_POST["fecha_finaliza"] . "',
		estado = " . $_POST["estado"] . ",
		plan_accion = '" . $_POST["plan_accion"] . "',
		comentario = '" . $_POST["comentario"] . "',
		updated_at = '$hoy'
		WHERE id = " . $_POST["id_plan"] . "";
		$accion = 'ACTUALIZAR';
		$descripcion = 'Actualización de plan de acción de pdi para la competencia ' . $dataComp["nombre"] . ' del colaborador ' . $colaborador["nombre"];
	} else {
		$sentencia_apa = "INSERT INTO Pdi_Competencias (id_empresa, id_jefe, id_empleado, id_competencia, prioridad, fecha_inicia, fecha_finaliza, estado, plan_accion, created_at) VALUES
		(" . $_SESSION["id_empresa"] . "," . $user_log["id"] . "," . $id_evaluado . "," . $_POST["id_competencia"] . "," . $_POST["prioridad"] . ",'" . $_POST["fecha_inicia"] . "','" . $_POST["fecha_finaliza"] . "',
		1,'" . $_POST["plan_accion"] . "','" . $hoy . "')";

		$accion = 'CREACIÓN';
		$descripcion = 'Creación de plan de acción de pdi para la competencia ' . $dataComp["nombre"] . ' del colaborador ' . $colaborador["nombre"];
	}
	// echo $sentencia_apa;
	mysqli_query($connect_valoracion, $sentencia_apa);
	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $_SESSION["id_empresa"] . ", " . $user_log["id"] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);

	echo '<script> window.location.href = "?pg=competencias/mi_informe";</script>';
}

if ($_POST["guardar_firma"] != "") {

	// print_r($_POST);

	$queryFirma = "UPDATE Competencias_Evaluaciones_New SET firma_aprobacion = '" . $_POST["firma"] . "', proceso_valoracion = 7, estado = 3, fecha_firma = '$hoy',update_at = '$hoy' WHERE id_Evaluado = " . $id_evaluado . "";

	// echo $queryFirma;
	$accion = 'ACTUALIZAR';
	$descripcion = 'Actualización de firma de pdi por parte del colaborador ' . $colaborador["nombre"];
	mysqli_query($connect_valoracion, $queryFirma);


	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $_SESSION["id_empresa"] . ", " . $user_log["id"] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);

	echo '<script> window.location.href = "?pg=competencias/mi_informe";</script>';
}

if ($_POST["enviar_pdi"] != "") {

	$queryPA = mysqli_query($connect_valoracion, "SELECT * FROM Pdi_Competencias WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND id_jefe = " . $user_log["id"] . " AND id_empleado = $id_evaluado");
	$dataPA = mysqli_fetch_array($queryPA);

	if (mysqli_num_rows($queryPA) >= 2) {
		$queryEnvio = "UPDATE Competencias_Evaluaciones_New SET proceso_valoracion = 4, estado = 2, update_at = '$hoy' WHERE id_evaluado = " . $id_evaluado . " AND id_evaluador = " . $_POST["id_jefe"] . "";
		// echo $queryEnvio;
		$accion = 'ACTUALIZAR';
		$descripcion = 'Actualización envio a pdi para el colaborador ' . $colaborador["nombre"];
		mysqli_query($connect_valoracion, $queryEnvio);


		$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
		VALUES (" . $_SESSION["id_empresa"] . ", " . $user_log["id"] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
		// echo $auditoria;
		mysqli_query($connect_okrs, $auditoria);
		//
		echo '<script> window.location.href = "?pg=competencias/mi_informe";</script>';
	} else {
		echo '<script> alert("Debe agregar por lo menos dos planes de acción antes de enviar a PDI");</script>';
	}
}


//echo $_SESSION["unidad_corporativa"];


if ($_POST["enviar_intervencion"] != "") {

	$queryRelacion = mysqli_query($connect_admin, "SELECT * FROM Relaciones_Laborales WHERE id_vp = '" . $user_log["id_area_macro"] . "' ");
	$dataRelacion = mysqli_fetch_array($queryRelacion);


	if (mysqli_num_rows($queryRelacion) == 0) {
		echo '<script> alert("No tiene asignado el responsable para Intervención de GH");</script>';
		echo '<script> window.location.href = "?pg=competencias/mi_informe";</script>';
	} else {
		$evaluaciones = "";

		$queryEvaluaciones = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE id_evaluado = $id_evaluado AND estado = 2 AND proceso_Valoracion = 4 ");
		while ($dataEvaluaciones = mysqli_fetch_array($queryEvaluaciones)) {
			$evaluaciones .= $dataEvaluaciones["id"] . ",";
		}

		if ($evaluaciones != "") {
			$evaluaciones = substr($evaluaciones, 0, -1);
		}

		$queryIntervencion = "INSERT INTO Intervencion_Gh (id_empresa,id_responsable,id_evaluado,evaluaciones,comentario,ciclo,estado,created_at) VALUES
	(" . $user_log["id_empresa"] . "," . $dataRelacion["id_empleado"] . ",$id_evaluado,'$evaluaciones','" . $_POST["comentario"] . "'," . $_SESSION["anio_ciclo"] . ",1,'$hoy')";

		mysqli_query($connect_valoracion, $queryIntervencion);

		$queryEnvio = "UPDATE Competencias_Evaluaciones_New SET proceso_valoracion = 6, update_at = '$hoy' WHERE id_evaluado = " . $id_evaluado . " ";
		// echo $queryEnvio;
		$accion = 'ACTUALIZAR';
		$descripcion = 'Actualización envio de Pdi a intervención GH para el colaborador ' . $colaborador["nombre"];
		mysqli_query($connect_valoracion, $queryEnvio);


		$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $_SESSION["id_empresa"] . ", " . $user_log["id"] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
		// echo $auditoria;
		mysqli_query($connect_okrs, $auditoria);
		//
		echo '<script> window.location.href = "?pg=competencias/mi_informe";</script>';
	}
}

include 'script_informe.php';

?>

<style>
	.titulo {
		font-size: 18px;
		font-weight: bold;
		margin-bottom: 10px;
	}
</style>

<style>
	.checkbox_list {
		width: 18px;
		height: 18px;
		margin-left: 8px;
	}

	.base_barras {
		/*background-color: #E1E1E1;*/
		width: 91%;
		margin-bottom: 10px;
	}

	.barra_progress {
		text-align: center;
		font-weight: bold;
		padding: 5px;
		border-radius: 0px 20px 20px 0px;
		text-align: right;
		padding-right: 20px;
	}

	ul {
		list-style-type: disclosure-closed;
		color: #8e8e8e;
	}

	@media print {
		body {
			margin: 0;
			padding: 0;
			background-color: #ffffff;
			font-size: 10px;
		}

		* {
			box-sizing: border-box;
			-moz-box-sizing: border-box;
		}

		#content {
			position: relative;
			height: 100%;
			margin-left: 0px;
		}

		.bt_print {
			display: none;
		}

		.header {
			display: none;
		}

		#quick_access {
			display: none;
		}

		#sidebar {
			display: none;
		}

		#content {
			width: 100%;
			height: 100%;
			overflow: auto;
			overflow: unset;
		}

		#menu_header {
			display: none;
		}

		.chartjs-size-monitor {
			display: none;
		}

		.card-body {
			padding: 0 !important;
		}

	}

	.card,
	.card-header,
	.card-body,
	.card-footer {
		background-color: white !important;
	}

	.progreso-bar {
		width: 300px;
		height: 300px;
		border-radius: 50%;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.progreso-bar::before {
		counter-reset: percentage var(--i);
		/*content: counter(percentage) '%';*/
	}

	.objetivo-okr {
		background:
			radial-gradient(closest-side, white 77%, transparent 80% 100%),
			conic-gradient(var(--clr) calc(var(--i) * 1%), #e9ecef 0);
		animation: objetivo-okr-progreso 2s 1 forwards;
		font-size: 4.1rem;
	}

	.objetivo-okr::before {
		animation: objetivo-okr-progreso 2s 1 forwards;
	}

	.progreso {
		visibility: hidden;
		width: 0;
		height: 0;
	}

	#sig-canvas {
		border: groove;
	}
</style>

<?php
/*
$promedio_general = $datos_generales["promedio_general"];
$porcentaje_general = ($promedio_general * 100) / 4;

if ($porcentaje_general > 100) {
	$porcentaje_general = 100;
}
$color_general = RetornarColor($porcentaje_general, $rangos);
*/
$queryCicloVal = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id = '" . $_SESSION['ciclo'] . "' AND id_empresa = '" . $_SESSION['id_empresa'] . "' ");
$dataCicloVal = mysqli_fetch_array($queryCicloVal);

$nombre_ciclo = $dataCicloVal["anio"] . " - " . $dataCicloVal["nombre"];

?>

<div class="container-fluid">
	<div class="row mb-2">
		<div class="col-md-12">
			<table width="100%">
				<tr>
					<td>
						<h3>Informes Individuales<b> <?php echo $nombre_ciclo; ?></b> </h3>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="row">
		<div class="card">
			<div class="card-header" style="background-color: white !important;text-align:center;">
				<ul class="nav nav-tabs">
					<li class="nav-item">
						<a class="nav-link active" href="?pg=competencias/mi_informe">Mi Informe</a>
					</li>
					<?php if ($VALIDAR_MENU["competencias_informe_equipo"]) { ?>
						<li class="nav-item">
							<a class="nav-link" href="?pg=competencias/informe_pdi">PDI Colaboradores</a>
						</li>
					<?php } ?>
				</ul>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12" align="right">
						<h5>Valoración por Competencias <?php echo $_SESSION["anio_ciclo"]; ?></h5>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<h5>Reporte Individual</h5><br>
						<b><?php echo $colaborador["nombre"]; ?></b> <br>
						Documento: <b><?php echo $colaborador["documento"]; ?></b> <br>
						Fecha de Valoración: <b><?php echo $fecha_evaluacion; ?></b> <br>
						Cargo: <b><?php echo $colaborador["nombre_cargo"]; ?></b> <br>
						Vicepresidencia: <b><?php echo $colaborador["nombre_vp"]; ?></b> <br>
						Área: <b><?php echo $colaborador["nombre_area"]; ?></b> <br>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-12">
						<h5 style="margin-bottom: 10px; margin-top: 10px" align="center">
							RESULTADO GLOBAL EVALUACIÓN DE COMPETENCIAS
						</h5>
					</div>
				</div>
				<?php include("views/competencias/mi_informe/resultado_global.php"); ?>
				<br>
				<div class="row">
					<div class="col-md-12">
						<div align="center">
							<h5>Escala de interpretación</h5><br><br>
						</div>
						<?php include("views/competencias/informes/comp_escala.php"); ?>
					</div>
				</div>
				<br>
				<div class="row" style="font-family: Lato-Black !important;">
					<?php if ($dataLider["id_jefe"] == $user_log["id"]) { ?>
						<?php if ($permitir_auto) { ?>
							<div class="col" style="background-color: #212529;color: white;text-align:center;">COMPETENCIA</div>
							<div class="col" style="background-color: #212529;color: white;text-align:center;">Auto</div>
						<?php } else { ?>
							<div class="col" style="background-color: #212529;color: white;text-align:center;">COMPETENCIA</div>
						<?php } ?>
						<div class="col" style="background-color: #212529;color: white;text-align:center;">Líder/supervisor</div>

                        <?php if ($permitir_cliente) { ?>
                            <div class="col" style="background-color: #212529;color: white;text-align:center;">Cliente</div>
                        <?php } ?>
                        <?php if ($permitir_par) { ?>
                            <div class="col" style="background-color: #212529;color: white;text-align:center;">Par</div>
                        <?php } ?>
                        <?php if ($permitir_col) { ?>
                            <div class="col" style="background-color: #212529;color: white;text-align:center;">Colaborador</div>
                        <?php } ?>

						<div class="col" style="background-color: #212529;color: white;text-align:center;">Resultado</div>
						<div class="col" style="background-color: #212529;color: white;text-align:center;">PLAN</div>
					<?php } else { ?>
						<?php if ($permitir_auto) { ?>
							<div class="col" style="background-color: #212529;color: white;text-align:center;">COMPETENCIA</div>
							<div class="col" style="background-color: #212529;color: white;text-align:center;">Auto</div>
						<?php } else { ?>
							<div class="col" style="background-color: #212529;color: white;text-align:center;">COMPETENCIA</div>
						<?php } ?>
						<div class="col" style="background-color: #212529;color: white;text-align:center;">Líder/supervisor</div>

                        <?php if ($permitir_cliente) { ?>
                            <div class="col" style="background-color: #212529;color: white;text-align:center;">Cliente</div>
                        <?php } ?>
                        <?php if ($permitir_par) { ?>
                            <div class="col" style="background-color: #212529;color: white;text-align:center;">Par</div>
                        <?php } ?>
                        <?php if ($permitir_col) { ?>
                            <div class="col" style="background-color: #212529;color: white;text-align:center;">Colaborador</div>
                        <?php } ?>

						<div class="col" style="background-color: #212529;color: white;text-align:center;">Resultado</div>
					<?php } ?>
				</div>
				<div class="row" style="font-family: Lato-Regular!important;">
					<div class="col-md-12">
						<div id="accordionIcons" class="accordion-icons" role="tablist">
							<?php
							$auto_total = 0;
							$jefe_total = 0;
							$par_total = 0;
							$colaborador_total = 0;
							$cliente_total = 0;
							$general_total = 0;

							$obj_dimensiones = '[';
							$obj_promedios = '[';
							$obj_promedios_jefe = '[';
							$obj_promedios_par = '[';
							$obj_promedios_cola = '[';
							$obj_promedios_cliente = '[';
							$promedio_auto = $promedio_supervisor = 0;

                            $PROMEDIO_GLOBAL_REPORTE = 0;

							foreach ($COMPETENCIAS as $competencia) {
								$queryNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $competencia . "' ");
								$dataNivel = mysqli_fetch_array($queryNivel);

								$queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataNivel["id_competencia"] . "' ");
								$dataComp = mysqli_fetch_array($queryComp);

								$nodo_comp =  ObtenerCompetenciasConsolidadas($competencia, $COMPETENCIAS, $EVALUACIONES);
								// print_r($nodo_comp);
								$auto = 0;
								$jefe = 0;
								$par = 0;
								$colaborador = 0;
								$cliente = 0;
								foreach ($nodo_comp["evaluadores"] as $eval) {
									// print_r($eval);
									$resultado_promediado = ($eval["sin_ponderacion"] / $eval["cantidad"]);

									if ($eval["tipo"] == 1) {
										$auto = $resultado_promediado;
										$auto_total += $resultado_promediado;
									}
									if ($eval["tipo"] == 5) {
										$jefe = $resultado_promediado;
										$jefe_total += $resultado_promediado;
									}
									if ($eval["tipo"] == 2) {
										$par = $resultado_promediado;
										$par_total += $resultado_promediado;
									}
									if ($eval["tipo"] == 3) {
										$colaborador = $resultado_promediado;
										$colaborador_total += $resultado_promediado;
									}
									if ($eval["tipo"] == 4) {
										$cliente = $resultado_promediado;
										$cliente_total += $resultado_promediado;
									}

									$general_total += ($eval["promedio"] / $eval["cantidad"]);
								}
								// echo $auto;
								$auto = $auto * 100 / 5;
								if ($auto > 100) {
									$auto = 100;
								}
								$jefe = $jefe * 100 / 5;
								if ($jefe > 100) {
									$jefe = 100;
								}
								$par = $par * 100 / 5;
								$colaborador = $colaborador * 100 / 5;
								$cliente = $cliente * 100 / 5;
								 $general = $nodo_comp["general"] * 100 / 5;
								if ($auto > 0) {
									//$general = $jefe;
								} else {
									//$general = $jefe;
								}
								if ($jefe == 0) {
									$general = 0;
								}
								$obj_dimensiones .= "'" . $dataComp["nombre"] . "' , ";
								//$obj_dimensiones .= "'auto - ".round( $general,1)."%', ";
								$obj_promedios .= " " . $auto . ", ";
								$obj_promedios_jefe .= " " . $jefe . ", ";
								$obj_promedios_par .= " " . $par . ", ";
								$obj_promedios_cola .= " " . $colaborador . ", ";
								$obj_promedios_cliente .= " " . $cliente . ", ";
								$color_competencia = RetornarColor($general, $rangos);
								echo '
						<div class="card mb-0">
							<div class="card-header" role="tab" id="heading' . $dataComp["id"] . '" style="background-color: white !important;">
								<div class="row">';
								if ($dataLider["id_jefe"] == $user_log["id"]) {
									if ($permitir_auto) {
										echo '<div class="col">
										<a class="collapsed" data-bs-toggle="collapse" href="#collapseKr' . $dataComp["id"] . '" aria-expanded="false" aria-controls="collapseKr' . $dataComp["id"] . ';" id="datosResultados_' . $dataComp["id"] . '" style="color: black !important;">
												' . eliminar_tildes($dataComp["nombre"]) . '
											</a>
										</div>
										<div class="col" style="text-align:center;">
										' . number_format($auto, 0) . '%
										</div>';
									} else {
										echo '<div class="col">
										<a class="collapsed" data-bs-toggle="collapse" href="#collapseKr' . $dataComp["id"] . '" aria-expanded="false" aria-controls="collapseKr' . $dataComp["id"] . ';" id="datosResultados_' . $dataComp["id"] . '" style="color: black !important;">
												' . eliminar_tildes($dataComp["nombre"]) . '
											</a>
										</div>';
									}

									echo '<div class="col" style="text-align:center;">
									' . number_format($jefe, 1) . '%
									</div>';

                                    if ($permitir_cliente) {
                                        echo '<div class="col" style="text-align:center;">
                                        ' . number_format($cliente, 1) . '%
                                        </div>';
                                    }
                                    if ($permitir_par) {
                                        echo '<div class="col" style="text-align:center;">
                                        ' . number_format($par, 1) . '%
                                        </div>';
                                    }
                                    if ($permitir_col) {
                                        echo '<div class="col" style="text-align:center;">
                                        ' . number_format($colaborador, 1) . '%
                                        </div>';
                                    }

                                    echo '
									<div class="col">
									<div class="progress" data-bs-toggle="tooltip" style="height: 10px;">
														  <div class="progress-bar bg-success active" role="progressbar" style=" width: ' . round($general, 1) . '%; background-color: ' . $color_competencia . ' !important;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
													</div> ' . round($general, 1) . '%
									</div>
									<div class="col" style="text-align:center;">
									<button type="button" class="btn btn-primary" onClick="AgregarPlan(' . $dataComp["id"] . ', ' . $user_log["id"] . ',' . $id_evaluado . ', ' . $_SESSION["id_empresa"] . ')" data-bs-toggle="tooltip" title="Agregar Plan de Acción" style="border-radius: 3px !important;font-family: Lato-Regular;">
								<i class="fas fa-plus" style="color: white !important;"></i> Agregar Plan
								</button>
									</div>';
								} else {
									if ($permitir_auto) {
										echo '<div class="col">
										<a class="collapsed" data-bs-toggle="collapse" href="#collapseKr' . $dataComp["id"] . '" aria-expanded="false" aria-controls="collapseKr' . $dataComp["id"] . ';" id="datosResultados_' . $dataComp["id"] . '" style="color: black !important;">
												' . eliminar_tildes($dataComp["nombre"]) . '
											</a>
										</div>
										<div class="col" style="text-align:center;">
										' . number_format($auto, 0) . '%
										</div>';
									} else {
										echo '<div class="col">
										<a class="collapsed" data-bs-toggle="collapse" href="#collapseKr' . $dataComp["id"] . '" aria-expanded="false" aria-controls="collapseKr' . $dataComp["id"] . ';" id="datosResultados_' . $dataComp["id"] . '" style="color: black !important;">
												' . eliminar_tildes($dataComp["nombre"]) . '
											</a>
										</div>';
									}

									echo '<div class="col" style="text-align:center;">
									' . number_format($jefe, 1) . '%
									</div>';

                                    if ($permitir_cliente) {
                                        echo '<div class="col" style="text-align:center;">
                                        ' . number_format($cliente, 1) . '%
                                        </div>';
                                    }
                                    if ($permitir_par) {
                                        echo '<div class="col" style="text-align:center;">
                                        ' . number_format($par, 1) . '%
                                        </div>';
                                    }
                                    if ($permitir_col) {
                                        echo '<div class="col" style="text-align:center;">
                                        ' . number_format($colaborador, 1) . '%
                                        </div>';
                                    }

                                    echo '
									<div class="col">
									<div class="progress" data-bs-toggle="tooltip" style="height: 10px;">
														  <div class="progress-bar bg-success active" role="progressbar" style=" width: ' . round($general, 1) . '%; background-color: ' . $color_competencia . ' !important;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
													</div> ' . round($general, 1) . '%
									</div>
									';
								}

                                //PARA OBTENER LOS DATOS GENERALES POR COMPETENCIA
                                $PROMEDIO_GLOBAL_REPORTE += $general;

								$promedio_auto = $promedio_auto + $auto;
								$promedio_jefe = $promedio_jefe + $jefe;
                                $promedio_cliente = $promedio_cliente + $cliente;
                                $promedio_par = $promedio_par + $par;
                                $promedio_colaborador = $promedio_colaborador + $colaborador;

								echo '</div>
							</div>
							<div id="collapseKr' . $dataComp["id"] . '" class="collapse" role="tabpanel" aria-labelledby="heading' . $dataComp["id"] . '" data-parent="#accordionIcons">
								<div class="card-body">';

								$queryNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $competencia . "' ");
								$dataNivel = mysqli_fetch_array($queryNivel);

								$queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataNivel["id_competencia"] . "' ");
								$dataComp = mysqli_fetch_array($queryComp);

								$nodo_comp =  ObtenerCompetenciasConsolidadas($competencia, $COMPETENCIAS, $EVALUACIONES);

								$auto = 0;
								$jefe = 0;
								$par = 0;
								$colaborador = 0;
								$cliente = 0;
								foreach ($nodo_comp["evaluadores"] as $eval) {
									$resultado_promediado = ($eval["sin_ponderacion"] / $eval["cantidad"]);

									if ($eval["tipo"] == 1) {
										$auto = $resultado_promediado;
										$auto_total += $resultado_promediado;
									}
									if ($eval["tipo"] == 5) {
										$jefe = $resultado_promediado;
										$jefe_total += $resultado_promediado;
									}
									if ($eval["tipo"] == 2) {
										$par = $resultado_promediado;
										$par_total += $resultado_promediado;
									}
									if ($eval["tipo"] == 3) {
										$colaborador = $resultado_promediado;
										$colaborador_total += $resultado_promediado;
									}
									if ($eval["tipo"] == 4) {
										$cliente = $resultado_promediado;
										$cliente_total += $resultado_promediado;
									}

									$general_total += ($eval["promedio"] / $eval["cantidad"]);
								}

								$auto = $auto * 100 / 5;
								if ($auto > 100) {
									$auto = 100;
								}
								$jefe = $jefe * 100 / 5;
								if ($jefe > 100) {
									$jefe = 100;
								}
								$par = $par * 100 / 5;
								$colaborador = $colaborador * 100 / 5;
								$cliente = $cliente * 100 / 5;
								$general = $nodo_comp["general"] * 100 / 5;

								if ($auto == 0) {
									//$general = $jefe;
								}

								if ($auto > 0) {
									//$general = $jefe;
								}

								if ($general > 100) {
									$general = 100;
								}

								$color_competencia = RetornarColor($general, $rangos);
								$color_auto = RetornarColor($auto, $rangos);
								$color_jefe = RetornarColor($jefe, $rangos);
								$color_cliente = RetornarColor($cliente, $rangos);
								$color_par = RetornarColor($par, $rangos);
								$color_cola = RetornarColor($colaborador, $rangos);

								$comentarios_competencia = '';
								//COMENTARIOS
								foreach ($EVALUACIONES as $evaluacion) {

									$txt_tipo = "";
									if ($evaluacion["tipo_evaluacion"] == 1) {
										$txt_tipo = "Auto";
									}
									if ($evaluacion["tipo_evaluacion"] == 5) {
										$txt_tipo = "Supervisor";
									}
									if ($evaluacion["tipo_evaluacion"] == 2) {
										$txt_tipo = "Par";
									}
									if ($evaluacion["tipo_evaluacion"] == 3) {
										$txt_tipo = "Colaborador";
									}
									if ($evaluacion["tipo_evaluacion"] == 4) {
										$txt_tipo = "Cliente";
									}

									$Array_Objeto = json_decode($evaluacion["obj_evaluacion"], true);
									foreach ($Array_Objeto as $respuestas) {

										if ($respuestas["competencia"] == $competencia) {
											if ($respuestas["observaciones"]) {
												$observa = str_replace("u00e1", "á",  $respuestas["observaciones"]);
												$observa = str_replace("u00f3", "ó",  $observa);
												$observa = str_replace("u00e9", "é",  $observa);
												$observa = str_replace("u00f1", "ñ",  $observa);
												$observa = str_replace("u00ed", "í",  $observa);


												$comentarios_competencia .= "<li><b>" . $txt_tipo . ":</b> " . $observa . "</li>";
											}
										}
									}
								}


							?>

								<h2 align="center"><?php echo eliminar_tildes($dataComp["nombre"]); ?> </h2><br><br>






							<?php
								include("views/competencias/mi_informe/competencias.php");
								echo '
								</div>
							</div>
						</div>
					';
							}

							$obj_dimensiones .= ']';
							$obj_promedios .= ']';
							$obj_promedios_jefe .= ']';
							$obj_promedios_par .= ']';
							$obj_promedios_cola .= ']';
							$obj_promedios_cliente .= ']';

							$por_1 = ($auto_total / count($COMPETENCIAS));
							$por_2 = ($jefe_total / count($COMPETENCIAS));
							$por_3 = ($cliente_total / count($COMPETENCIAS));
							$por_4 = ($par_total / count($COMPETENCIAS));
							$por_5 = ($colaborador_total / count($COMPETENCIAS));
							$por_6 = ($general_total / count($COMPETENCIAS));

							$por_1 = $por_1 * 100 / 5;
							$por_2 = $por_2 * 100 / 5;
							$por_3 = $por_3 * 100 / 5;
							$por_4 = $por_4 * 100 / 5;
							$por_5 = $por_5 * 100 / 5;
							$por_6 = $por_6 * 100 / 5;

							$total_auto = round($promedio_auto / count($COMPETENCIAS), 2);
							$total_jefe = round($promedio_jefe / count($COMPETENCIAS), 2);
                            $total_cliente = round($promedio_cliente / count($COMPETENCIAS), 2);
                            $total_par = round($promedio_par / count($COMPETENCIAS), 2);
                            $total_colaborador = round($promedio_colaborador / count($COMPETENCIAS), 2);

							?>

						</div>
					</div>
				</div>
				<div class="row" style="font-family: Lato-Black !important;">

                    <div class="col" style="background-color: #212529;color: white;text-align:center;">TOTAL</div>

                    <?php if ($permitir_auto) { ?>
						<div class="col" style="background-color: #212529;color: white;text-align:center;"><?php echo number_format($total_auto, 1); ?>%</div>
					<?php } ?>

                    <div class="col" style="background-color: #212529;color: white;text-align:center;"><?php echo number_format($total_jefe, 1); ?>%</div>

                    <?php if ($permitir_cliente) { ?>
						<div class="col" style="background-color: #212529;color: white;text-align:center;"><?php echo number_format($total_cliente, 1); ?>%</div>
					<?php } ?>

                    <?php if ($permitir_par) { ?>
						<div class="col" style="background-color: #212529;color: white;text-align:center;"><?php echo number_format($total_par, 1); ?>%</div>
					<?php } ?>

                    <?php if ($permitir_col) { ?>
						<div class="col" style="background-color: #212529;color: white;text-align:center;"><?php echo number_format($total_colaborador, 1); ?>%</div>
					<?php } ?>


                    <?php
                        if($PROMEDIO_GLOBAL_REPORTE > 0 ){
                            $PROMEDIO_GLOBAL_REPORTE = $PROMEDIO_GLOBAL_REPORTE/count($COMPETENCIAS);
                            $PROMEDIO_GLOBAL_REPORTE = round($PROMEDIO_GLOBAL_REPORTE,1);
                        }

                        $color_general = RetornarColor( round($PROMEDIO_GLOBAL_REPORTE,1), $rangos);

                        if($tipo_ponderacion == '180'){
                            $PROMEDIO_GLOBAL_REPORTE = number_format($total_jefe, 1);
                            $color_general = RetornarColor( round($PROMEDIO_GLOBAL_REPORTE,1), $rangos);
                        }
                    ?>

                    <div class="col" style="background-color: #212529;color: white;text-align:center;"><?php echo $PROMEDIO_GLOBAL_REPORTE; ?>%</div>



					<?php if ($dataLider["id_jefe"] == $user_log["id"]) { ?>
						<?php if ($permitir_auto) { ?>
							<div class="col" style="background-color: #212529;color: white;text-align:center; display: none">TOTAL..</div>
							<div class="col" style="background-color: #212529;color: white;text-align:center; display: none"><?php echo number_format($total_auto, 1); ?>%</div>
						<?php } else { ?>
							<div class="col" style="background-color: #212529;color: white;text-align:center; display: none">TOTAL..</div>
						<?php } ?>
						<div class="col" style="background-color: #212529;color: white;text-align:center; display: none"><?php echo number_format($total_jefe, 1); ?>%</div>
						<!-- <div class="col-md-2" style="background-color: #212529;color: white;text-align:center;"><?php //echo number_format($porcentaje_general, 1);
																														?>%</div> -->
						<div class="col" style="background-color: #212529;color: white;text-align:center;"><?php echo number_format($total_jefe, 1); ?>%</div>
						<div class="col" style="background-color: #212529;color: white;"></div>
					<?php } else { ?>
						<?php if ($permitir_auto) { ?>
							<div class="col" style="background-color: #212529;color: white;text-align:center; display: none">TOTAL**</div>
							<div class="col" style="background-color: #212529;color: white;text-align:center; display: none"><?php echo number_format($total_auto, 1); ?>%</div>
						<?php } else { ?>
							<div class="col" style="background-color: #212529;color: white;text-align:center; display: none">TOTAL</div>
						<?php } ?>
						<div class="col" style="background-color: #212529;color: white;text-align:center; display: none"><?php echo number_format($total_jefe, 1); ?>%</div>
						<!-- <div class="col-md-2" style="background-color: #212529;color: white;text-align:center;"><?php //echo number_format($porcentaje_general, 1);
																														?>%</div> -->
						<div class="col" style="background-color: #212529;color: white;text-align:center; display: none"><?php echo number_format($total_jefe, 1); ?>%</div>
					<?php }
					//$color_general = RetornarColor($total_jefe, $rangos); ?>

                    <?php
/*
                        if($PROMEDIO_GLOBAL_REPORTE > 0 ){
                            $PROMEDIO_GLOBAL_REPORTE = $PROMEDIO_GLOBAL_REPORTE/count($COMPETENCIAS);
                        }

                        $color_general = RetornarColor($promedio_general_evaluacion_porcentaje, $rangos);
                        */
                    ?>

					<script>
						$(document).ready(function() {
							$("#porcentajeTotal").html('<div class="progreso-bar-container" style="--i:<?php echo $PROMEDIO_GLOBAL_REPORTE; ?>;--clr:<?php echo $color_general; ?>">' +
								'<div class="progreso-bar objetivo-okr"> <?php echo $PROMEDIO_GLOBAL_REPORTE; ?>% ' +
								'<progreso id="objetivo-okr" min="0" value="<?php echo $PROMEDIO_GLOBAL_REPORTE; ?>"></progreso>' +
								'</div>' +
								'</div>' +
								'<div style="margin-top: 10px; margin-bottom: 20px">Resultado Total....</div>'
							);
						});
					</script>
				</div>
			</div>
			<div class="card-footer">
				<div class="row">
					<div class="col-md-12">
						<h2 style="margin-bottom: 10px; margin-top: 10px" align="center">PLAN DE DESARROLLO INDIVIDUAL</h2>
					</div>
				</div>
				<?php
				if ($competenciasEval["proceso_valoracion"] >= 3 || $user_log["id"] == $dataLider["id_jefe"] || $competenciasEvalJefe["proceso_valoracion"] >= 3  ) {
				?>
					<div class="row">
						<div class="col-md-12">
							<table id="pdi" class="display table" style="width:100%;">
								<thead>
									<tr>
										<th>Competencia</th>
										<th>Plan de Acción</th>
										<th>Prioridad</th>
										<th>Fecha Inicio</th>
										<th>Fecha Entrega</th>
										<th>Estado</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<?php


									$queryPDIS = mysqli_query($connect_valoracion, "SELECT * FROM Pdi_Competencias WHERE id_empleado = '" . $id_evaluado . "' AND YEAR(created_at) = '".$_SESSION["anio_ciclo"]."' ");
									while ($dataPDIS = mysqli_fetch_array($queryPDIS)) {
										$queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataPDIS["id_competencia"] . "' ");
										$dataComp = mysqli_fetch_array($queryComp);

										switch ($dataPDIS['estado']) {
											case 1:
												$txtEstado = 'Sin Iniciar';
												$colorEstado = 'badge bg-light';
												$colorBadge = 'black';
												break;
											case 2:
												$txtEstado = 'En Proceso';
												$colorEstado = 'badge bg-primary';
												$colorBadge = 'white';
												break;
											case 3:
												$txtEstado = 'En Revisión';
												$colorEstado = 'badge bg-warning';
												$colorBadge = 'black';
												break;
											case 4:
												$txtEstado = 'Completado';
												$colorEstado = 'badge bg-success';
												$colorBadge = 'white';
												break;
										}

										switch ($dataPDIS['prioridad']) {
											case 1:
												$txtPrioridad = 'Bajo';
												$colorPrioridad = 'badge bg-light';
												$colorBadgeP = 'black';
												break;
											case 2:
												$txtPrioridad = 'Medio';
												$colorPrioridad = 'badge bg-success';
												$colorBadgeP = 'white';
												break;
											case 3:
												$txtPrioridad = 'Alto';
												$colorPrioridad = 'badge bg-warning';
												$colorBadgeP = 'black';
												break;
											case 4:
												$txtPrioridad = 'Urgente';
												$colorPrioridad = 'badge bg-danger';
												$colorBadgeP = 'white';
												break;
										}

									?>
										<tr>
											<td><?php echo eliminar_tildes($dataComp["nombre"]); ?></td>
											<td><?php echo $dataPDIS["plan_accion"]; ?></td>
											<td><span class="<?php echo $colorPrioridad; ?>" style="color:<?php echo $colorBadgeP; ?>  !important;font-size: 13px !important;"><b><?php echo $txtPrioridad; ?></b></span></td>
											<td><?php echo $dataPDIS["fecha_inicia"]; ?></td>
											<td><?php echo $dataPDIS["fecha_finaliza"]; ?></td>
											<td><span class="<?php echo $colorEstado; ?>" style="color:<?php echo $colorBadge; ?>  !important;font-size: 13px !important;"><b><?php echo $txtEstado; ?></b></span></td>
											<td>
												<button type="button" class="btn btn-light btn-sm bt_editar" title="Editar Plan" onclick="EditarPlan(<?php echo $dataPDIS["id"] . "," . $user_log["id"] . "," . $id_evaluado . "," . $_SESSION["id_empresa"]; ?>)">
													<i class="bx bx-edit"></i>
												</button>
											</td>
										</tr>

									<?php
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				<?php } else { ?>
					<div class="row">
						<div class="col-md-12" style="text-align: center;">
							<h5 style="color: red !important;">En este momento no tiene planes de acción asignados</h5>
						</div>
					</div>
				<?php } ?>
				<br>
				<hr style="height: 10px;background-color: #59008e;opacity: 1;">
				<?php
				if ($procesoValoracionActual == 8) {
				?>
					<div id="contenedorIntervencion" class="row justify-content-center g-4">
						<!-- Tarjeta Intervención GH-->
						<div class="col-md-8">
							<div class="card shadow p-4 h-100">
								<h5 class="text-left mb-4 fw-bold" style="color: #2977ff !important;">
									Resumen de la intervención realizada por Gestión Humana durante el proceso de evaluación
								</h5>
								<div id="comentarioIntervencion" style="white-space: pre-wrap;"><?php echo $comentarioGh; ?></div>
							</div>
						</div>
						<!-- Tarjeta 1: Instrucciones de PDI -->
						<div class="col-md-4">
							<div class="card shadow p-4 h-100" style="box-shadow: 0 .1rem 0.4rem #fb924e!important;">
								<h5 id="gestionHumanaTitulo" class="text-center mb-4 fw-bold" style="color:#fb924e">Proceso Cerrado <br> por Gestión Humana</h5>
								<ul>
									<li>Intervención de Gestión Humana <br><strong>Responsable: </strong><?php echo $nombreResponsable; ?></li>
									<li><strong>Fecha/Hora: </strong><?php echo $fechaGh; ?></li>
							</div>
						</div>
					</div>
				<?php } else { ?>
					<div class="row">
						<div class="col-md-12">
							<h2 style="margin-bottom: 10px; margin-top: 10px;color: #59008e !important;" align="center">COMPETENCIAS A DESARROLLAR</h2>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<p style="text-align: justify;">Selecciona 2 competencias clave que desees fortalecer. Luego, analiza cada una de ellas en profundidad: identifica posibles razones o causas por las cuales es importante mantenerla o desarrollarla y reflexiona sobre su impacto futuro.</p>
						</div>
					</div>
					<div class="row" style="font-family: Lato-Regular !important;">
						<div class="col-md-12">
							<p style="text-align: justify;color: #59008e !important;">Aprendizaje a través de la experiencia</p>
							<ul style="color: black !important;">
								<li>Desafíos y Nuevas Responsabilidades: Asigna a los colaboradores nuevos retos y responsabilidades que expandan sus habilidades.</li>
								<li>Exposición a Proyectos y Asignaciones: Involucra al equipo en proyectos clave y asignaciones novedosas para que adquieran experiencia práctica en áreas críticas.</li>
							</ul>
							<p style="text-align: justify;color: #59008e !important;">Aprendizaje a través de la interacción con otros</p>
							<ul style="color: black !important;">
								<li>Sesiones de Retroalimentación: Facilita sesiones regulares de retroalimentación con líderes, colegas y clientes para identificar áreas de mejora y celebrar los logros.</li>
								<li>Mentoría y Coaching: Establece relaciones de mentoría y coaching dentro del equipo, proporcionando guía y apoyo personalizado.</li>
								<li>Actividades de Observación: Fomenta la observación de mejores prácticas dentro y fuera del equipo, para aprender de ejemplos reales y aplicables.</li>
							</ul>
							<p style="text-align: justify;color: #59008e !important;">Aprendizaje a través de programas educativos</p>
							<ul style="color: black !important;">
								<li>Capacitación y Cursos: Promueve la participación en capacitaciones, cursos y programas de e-learning que sean relevantes para el desarrollo profesional del colaborador.</li>
								<li>Autoestudio y Lecturas Especializadas: Incentiva el autoestudio y la lectura de materiales especializados que complementen el conocimiento y habilidades requeridas.</li>
							</ul>
						</div>
					</div>
				<?php } ?>
				<br>
				<!-- <div class="row">
					<div class="col-md-12">
						<h2 style="margin-bottom: 20px; margin-top: 20px" align="center">
							OBSERVACIONES Y COMENTARIOS
						</h2>
						<h3>Fortalezas</h3>
						<?php
						//echo eliminar_tildes($comentarios_finales);
						?>
						<h3 style="margin-top: 20px">Oportunidades de Mejora</h3>
						<?php
						//echo eliminar_tildes($comentarios_mejoras);
						?>
					</div>
				</div>
				<br> -->
				<?php
				$sentencia = "SELECT
				Empleados.id AS id, Empleados.id_empresa AS id_empresa, Empleados.nombre AS nombre, Empleados.correo AS correo_corporativo, Empleados.correo_personal AS correo_personal, Empleados.telefono_movil AS celular, Empleados.documento AS documento, Empleados.role AS role, Empleados.estado AS estado,
				Empleados.foto AS foto,
				Posiciones.id AS id_posicion, Cargos.id AS id_cargo, Cargos.nombre AS nombre_cargo, Areas.nombre AS nombre_area, Areas.id AS id_area
				FROM Empleados
				LEFT JOIN Posiciones ON Empleados.id_posicion = Posiciones.id
				LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
				LEFT JOIN Areas ON Areas.id = Empleados.area
				WHERE Empleados.id > 0 " . $filtros . " AND Empleados.id_empresa = '" . $_SESSION["id_empresa"] . "'
				ORDER BY Empleados.nombre ASC
				";

				$queryColaborador = mysqli_query($connect_admin, $sentencia);
				$colaborador = mysqli_fetch_array($queryColaborador);

				if ($user_log["id"] == $dataLider["id_jefe"]) {
					// print_r($competenciasEval);

					if ($dataLider["id_jefe"] != $competenciasEval["id_evaluador"]) {
						$queryEvaluacionesComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New
						WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND
						id_evaluado = '" . $id_evaluado . "' AND id_evaluador != " . $id_evaluado . " AND anio = '" . $_SESSION['anio_ciclo'] . "' AND estado >= 2
						ORDER BY created_at DESC ");

						$competenciasEval = mysqli_fetch_array($queryEvaluacionesComp);
					}

					if ($competenciasEval["proceso_valoracion"] == 3 || $competenciasEval["proceso_valoracion"] == 2) {
				?>
						<!-- <div class="row">
							<div class="col-md-12">
								<button type="button" class="btn btn-success" onClick="EnviarPdi(<?php //echo $user_log["id"] . "," . $id_evaluado . "," . $_SESSION["id_empresa"] . "," . $_GET["id_asignado"];
																									?>,4)">Envio (PDI)</button>
							</div>
						</div> -->
						<form action="" method="post">
							<div class="form-group">
								<input type="hidden" name="enviar_pdi" value="true">
								<input type="hidden" name="id_jefe" value="<?php echo $user_log["id"]; ?>">
								<div class="row">
									<div class="col-md-12">
										<button type="submit" class="btn btn-success">Envio (PDI)</button>
									</div>
								</div>
							</div>
						</form>
					<?php } else {

						if ($competenciasEval["proceso_valoracion"] == 7 && ($competenciasEval["firma_aprobacion"] != "" || $competenciasEval["firma_aprobacion"] == "")) {
							echo '<div class="row"><div class="col-md-12"><img src="' . $competenciasEval["firma_aprobacion"] . '"><br>';
							echo 'Firma de Aprobación: ' . $colaborador["nombre"] . '<br>';
							echo 'Documento: ' . $colaborador["documento"] . '<br>';
							echo 'Fecha de firma: ' . $competenciasEval["fecha_firma"] . '</div></div><br>';
						}
					} ?>
					<?php } else {
					if ($competenciasEval["proceso_valoracion"] == 4) {
						// if ($competenciasEval["proceso_valoracion"] != 5 && $competenciasEval["proceso_valoracion"] != 6) {
					?>
						<div class="row">
							<div class="col-md-12">
								<button type="button" class="btn btn-success" onClick="FirmaAprobacion(<?php echo $id_evaluado ?>)">Firmar Aprobación</button>&nbsp;&nbsp;
								<button type="button" class="btn btn-warning" onClick="IntervencionGH(<?php echo $id_evaluado ?>)">Intervención GH</button>
							</div>
						</div>
				<?php //}
					}

					if ($competenciasEval["proceso_valoracion"] == 7 && ($competenciasEval["firma_aprobacion"] != "" || $competenciasEval["firma_aprobacion"] == "")) {
						echo '<div class="row"><div class="col-md-12"><img src="' . $competenciasEval["firma_aprobacion"] . '"><br>';
						echo 'Firma de Aprobación: ' . $colaborador["nombre"] . '<br>';
						echo 'Documento: ' . $colaborador["documento"] . '<br>';
						echo 'Fecha de firma: ' . $competenciasEval["fecha_firma"] . '</div></div><br>';
					}
				} ?>
				<br>

			</div>
		</div>
	</div>
</div>


<style>
	.circulo {
		width: 300px;
		height: 300px;
		border-radius: 300px;
		font-size: 60px;
		font-weight: bold;
		padding: 90px 0px;
		text-align: center;
	}
</style>


<style>
	.base_vertical {
		width: 50px;
		height: 260px;
		margin-bottom: 26px;
	}

	.barra_vertical {
		width: 100%;
		display: flex;
		flex-direction: column-reverse;
	}

	.linea_abajo {
		border-top: 1px solid #cccccc;
	}
</style>


<script type="text/javascript">
	$(document).ready(function() {
		$(' #pdi').DataTable({
			pageLength: 15,
			language: {
				processing: "Procesando...",
				search: "Buscar:",
				lengthMenu: "Mostrar _MENU_ registros.",
				info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
				infoEmpty: "Mostrando registros del 0 al 0 de 0 registros",
				infoFiltered: "(filtrado de un total de _MAX_ registros)",
				infoPostFix: "",
				loadingRecords: "Cargando...",
				zeroRecords: "No se encontraron resultados",
				emptyTable: "Ningún pdi disponible en esta tabla",
				row: "Registro",
				export: "Exportar",
				paginate: {
					first: "Primero",
					previous: "Anterior",
					next: "Siguiente",
					last: "Ultimo"
				},
				aria: {
					sortAscending: ": Activar para ordenar la columna de manera ascendente",
					sortDescending: ": Activar para ordenar la columna de manera descendente"
				},
				select: {
					row: "registro",
					selected: "seleccionado"
				}
			},
			dom: 'Bfrtip',
			buttons: [{
					extend: 'collection',
					text: 'Exportar',
					buttons: ['copy', 'excel', 'csv',
						{
							extend: 'pdfHtml5',
							text: 'PDF',
							orientation: 'landscape',
							pageSize: 'LEGAL'
						},
						{
							extend: 'print',
							customize: function(win) {
								$(win.document.body)
									.css('font-size', '10pt');

								$(win.document.body).find('table')
									.addClass('compact')
									.css('font-size', 'inherit');
							}
						}
					]
				}

			]
		});

	});

	var api = '<?php echo $url; ?>api/competencias/';

	function AgregarPlan(id_competencia, id_jefe, id_empleado, id_empresa) {
		jQuery.ajax({
				url: api + "agregar_plan_accion.php",
				type: 'post',
				data: {
					id_competencia: id_competencia,
					id_jefe: id_jefe,
					id_empleado: id_empleado,
					id_empresa: id_empresa
				},
			}).done(function(resp) {
				$('#modal_competencias').modal({
					backdrop: 'static',
					keyboard: false
				});
				$("#modal_competencias").modal("show");
				$("#modal_contenido").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});
	}

	function EditarPlan(id_plan, id_jefe, id_empleado, id_empresa) {
		jQuery.ajax({
				url: api + "editar_plan_accion.php",
				type: 'post',
				data: {
					id_plan: id_plan,
					id_jefe: id_jefe,
					id_empleado: id_empleado,
					id_empresa: id_empresa
				},
			}).done(function(resp) {
				$('#modal_competencias').modal({
					backdrop: 'static',
					keyboard: false
				});
				$("#modal_competencias").modal("show");
				$("#modal_contenido").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});
	}

	function FirmaAprobacion(id_empleado) {
		jQuery.ajax({
				url: api + "firma_aprobacion.php",
				type: 'post',
				data: {
					id_empleado: id_empleado
				},
			}).done(function(resp) {
				$('#modal_firma').modal({
					backdrop: 'static',
					keyboard: false
				});
				$("#modal_firma").modal("show");
				$("#modal_contenido_f").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});
	}

	function IntervencionGH(id_empleado) {
		jQuery.ajax({
				url: api + "intervencion_gh.php",
				type: 'post',
				data: {
					id_empleado: id_empleado
				},
			}).done(function(resp) {
				$('#modal_intervencion').modal({
					backdrop: 'static',
					keyboard: false
				});
				$("#modal_intervencion").modal("show");
				$("#modal_contenido_i").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});
	}

	// function EnviarPdi(id_jefe, id_empleado, id_empresa, id, estado) {
	// 	jQuery.ajax({
	// 			url: api + "asignar_pdi.php",
	// 			type: 'post',
	// 			data: {
	// 				id_jefe: id_jefe,
	// 				id_empleado: id_empleado,
	// 				id_empresa: id_empresa,
	// 				ciclo: <?php //echo $_SESSION['anio_ciclo'];
								?>,
	// 				id: id,
	// 				estado: estado
	// 			},
	// 		}).done(function(resp) {
	// 			// console.log(resp);
	// 			location.href = "?pg=competencias/informes/reporte_individual&e=<?php //echo $_GET['e'];
																						?>";
	// 		})
	// 		.fail(function(resp) {
	// 			console.log(resp);
	// 		})
	// 		.always(function(resp) {});
	// }
</script>