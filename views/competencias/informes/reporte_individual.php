<?php
$array_equipo = array();
$sentenciavalidarPermiso =  "SELECT * FROM Lideres WHERE id_empresa = '" . $user_log['id_empresa'] . "' AND id_jefe = " . $user_log['id'] . " AND  id_empleado = '".$_GET["e"]."'
";

/*
//SOLO PARA LOS CASOS DONDE EL COLABORADOR SOLO TIENE AUTO Y JEFE
$queryValidarJefe = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE id_empleado = '" . $id_evaluado . "' AND id_empresa = '".$user_log["id_empresa"]."' ");
$dataValidarJefe = mysqli_fetch_array($queryValidarJefe);
*/
                               
$queryValidarPermisoVer = mysqli_query($connect_admin, $sentenciavalidarPermiso);
if($queryValidarPermisoVer->num_rows > 0){
    //PUEDE VISUALIZAR
}
else{

    $permValVista = false;
    foreach($roles_usr as $rl_menu){
		if($rl_menu == 1){
            $permValVista = true;
        }
	}

    if($user_log["permiso_relaciones_laboradores_competencias"] || $permValVista ){
        //PUEDE VISUALIZAR
    }
    else{
         echo '
        <script>
            window.location = "?pg=competencias/realizar_valoracion";
        </script>
        ';
    }
   
}
?>



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
<!-- Incluye CKEditor desde CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<?php
session_start();
$hoy = date("Y-m-d H:i:s");
$id_evaluado = $_GET["e"];
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
$tipo_ponderacion = $resp_promedio["tipo_ponderacion"];

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

$queryLider = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empleado = '" . $id_evaluado . "' AND id_empresa = '".$user_log["id_empresa"]."' ");
$dataLider = mysqli_fetch_array($queryLider);


$queryEvaluacionesComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND
    id_evaluado = '" . $id_evaluado . "' AND id_evaluador = " . $dataLider["id_jefe"] . " AND anio = '" . $_SESSION['anio_ciclo'] . "' AND estado >= 2
	ORDER BY created_at DESC ");

$competenciasEval = mysqli_fetch_array($queryEvaluacionesComp);
/** Variables para proceso Intervencion GH*/
$areaUsuario = $_SESSION['area'];
$rolUsuario = $_SESSION['role_plataforma'];
$idUser = $_SESSION['id_user'];
$keyEmpresa = $_SESSION['id_empresa'];
$procesoValoracionActual = 0;
$keyEvaluado = 0;
$keyEvaluacion = 0;
$ciclo = $_SESSION['ciclo'];
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

/**Traer datos Intervención GH */
if ($procesoValoracionActual == 8) {
	$datosIntervencion = "SELECT id_responsable, id_evaluado, comentario, created_at FROM `Intervencion_Gh` WHERE id_empresa = $keyEmpresa AND id_evaluado = $keyEvaluado AND evaluaciones = $keyEvaluacion AND ciclo = $ciclo LIMIT 1";
	$sql = mysqli_query($connect_valoracion, $datosIntervencion);
	$resultIntervencion = mysqli_fetch_array($sql);
	$comentarioGh = $resultIntervencion['comentario'];
	$fechaGh = date('d/m/Y H:i', strtotime($resultIntervencion['created_at']));
	$nombreResponsable = '';
	/**Datos básicos del responsable */
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

// include("app/models/Collaborators.php");
// $ClassCollaborators = new Collaborators();
// $colaborador =  $ClassCollaborators->collaborator_resumen($id_evaluado, $connect_admin);

$filtros = " ";
if ($_POST["nombre"]) {
	$filtros .= " AND Empleados.nombre LIKE '%" . $request["nombre"] . "%' ";
}
if ($id_evaluado) {
	$filtros .= " AND Empleados.id = '" . $id_evaluado . "' ";
}
//CONSULTA DE LOS EMPLEADOS ACTIVOS
$sentencia = "SELECT
	Empleados.id AS id, Empleados.id_empresa AS id_empresa, Empleados.nombre AS nombre, Empleados.correo AS correo_corporativo, Empleados.correo_personal AS correo_personal, Empleados.telefono_movil AS celular, Empleados.documento AS documento, Empleados.role AS role, Empleados.estado AS estado,
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

$queryEscalaInter = mysqli_query($connect_valoracion, "SELECT * FROM Escalas_Interpretacion WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ");
$dataEscalaInter = mysqli_fetch_array($queryEscalaInter);

$rangos = $dataEscalaInter;

$queryLider = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empleado = '" . $id_evaluado . "' AND id_empresa = '".$user_log["id_empresa"]."' ");
$dataLider = mysqli_fetch_array($queryLider);

if ($_POST["guardar_plan_accion"] != "") {

	// print_r($_POST);

	$queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $_POST["id_competencia"] . "' ");
	$dataComp = mysqli_fetch_array($queryComp);

    $plan_accion = str_replace("'", "", $_POST["plan_accion"]);

	if ($_POST["id_plan"] != "") {
		$sentencia_apa = "UPDATE Pdi_Competencias SET
		prioridad = " . $_POST["prioridad"] . ",
		fecha_inicia = '" . $_POST["fecha_inicia"] . "',
		fecha_finaliza = '" . $_POST["fecha_finaliza"] . "',
		estado = " . $_POST["estado"] . ",
		plan_accion = '" . $plan_accion . "',
		comentario = '" . $_POST["comentario"] . "',
		updated_at = '$hoy'
		WHERE id = " . $_POST["id_plan"] . "";
		$accion = 'ACTUALIZAR';
		$descripcion = 'Actualización de plan de acción de pdi para la competencia ' . $dataComp["nombre"] . ' del colaborador ' . $colaborador["nombre"];
	} else {
		$sentencia_apa = "INSERT INTO Pdi_Competencias (id_empresa, id_jefe, id_empleado, id_competencia, prioridad, fecha_inicia, fecha_finaliza, estado, plan_accion, created_at) VALUES
		(" . $user_log["id_empresa"] . "," . $user_log["id"] . "," . $id_evaluado . "," . $_POST["id_competencia"] . "," . $_POST["prioridad"] . ",'" . $_POST["fecha_inicia"] . "','" . $_POST["fecha_finaliza"] . "',
		1,'" . $plan_accion . "','" . $hoy . "')";

		$accion = 'CREACIÓN';
		$descripcion = 'Creación de plan de acción de pdi para la competencia ' . $dataComp["nombre"] . ' del colaborador ' . $colaborador["nombre"];
	}
	// echo $sentencia_apa;
	mysqli_query($connect_valoracion, $sentencia_apa);
	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $user_log["id_empresa"] . ", " . $user_log["id"] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);

	echo '<script> window.location.href = "?pg=competencias/informes/reporte_individual&e=' . $id_evaluado . '";</script>';
}

if ($_POST["guardar_firma"] != "") {

	// print_r($_POST);

	$queryFirma = "UPDATE Competencias_Evaluaciones_New SET firma_aprobacion = '" . $_POST["firma"] . "', proceso_valoracion = 7, estado = 3, fecha_firma = '$hoy',update_at = '$hoy' WHERE id_Evaluado = " . $id_evaluado . "";

	// echo $queryFirma;
	$accion = 'ACTUALIZAR';
	$descripcion = 'Actualización de firma de pdi por parte del colaborador ' . $colaborador["nombre"];
	mysqli_query($connect_valoracion, $queryFirma);


	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $user_log["id_empresa"] . ", " . $user_log["id"] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);

	echo '<script> window.location.href = "?pg=competencias/informes/reporte_individual&e=' . $id_evaluado . '";</script>';
}

//PARA ENVIAR PDI
//PARA ENVIAR PDI
//PARA ENVIAR PDI
if ($_POST["enviar_pdi"] != "") {
    $sentencia_validar = "
        SELECT j.id_jefe FROM Lideres j INNER JOIN Empleados e ON e.id = j.id_jefe LEFT JOIN Posiciones p ON p.id = e.id_posicion LEFT JOIN Cargos c ON c.id = e.id_cargo LEFT JOIN Areas a ON a.id = e.area WHERE j.id_empleado = '" . $id_evaluado . "' AND e.estado = 1 AND e.id_empresa = '".$user_log["id_empresa"]."'
    ";

	$sqlJefe = mysqli_query($connect_admin, $sentencia_validar);
	$keyJefe = mysqli_fetch_array($sqlJefe);
	$idJefe = $keyJefe['id_jefe'];
	$queryPA = mysqli_query($connect_valoracion, "SELECT * FROM Pdi_Competencias WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND id_jefe = " . $idJefe  . " AND id_empleado = '".$id_evaluado."' AND  YEAR(created_at) = '".$_SESSION['anio_ciclo']."' ");
	$dataPA = mysqli_fetch_array($queryPA);

	if (mysqli_num_rows($queryPA) >= 2) {
		//$queryEnvio = "UPDATE Competencias_Evaluaciones_New SET proceso_valoracion = 4, estado = 2, update_at = '$hoy' WHERE id_evaluado = " . $id_evaluado . " AND id_evaluador = " . $_POST["id_jefe"] . "";

		$queryEnvio = "UPDATE Competencias_Evaluaciones_New SET proceso_valoracion = 4, estado = 2, update_at = '$hoy' WHERE id_evaluado = " . $id_evaluado . " ";

		echo "SELECT * FROM Competencias_Evaluaciones_New WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND id_evaluado = $id_evaluado AND tipo_evaluacion = 1 AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND anio = " . $_SESSION['anio_ciclo'] . "";
		$queryAuto = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE id_empresa = '" .$user_log["id_empresa"] . "' AND id_evaluado = $id_evaluado AND tipo_evaluacion = 1 AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND anio = " . $_SESSION['anio_ciclo'] . "");
		if (mysqli_num_rows($queryAuto) > 0) {
			//$dataAuto = mysqli_fetch_array($queryAuto);
			//mysqli_query($connect_valoracion, "UPDATE Competencias_Evaluaciones_New SET proceso_valoracion = 4, update_at = '$hoy' WHERE id = " . $dataAuto["id"] . "");
		}
		$accion = 'ACTUALIZAR';
		$descripcion = 'Actualización envio a pdi para el colaborador ' . $colaborador["nombre"];
		mysqli_query($connect_valoracion, $queryEnvio);


		$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
		VALUES (" . $user_log["id_empresa"] . ", " . $user_log["id"] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
		// echo $auditoria;
		mysqli_query($connect_okrs, $auditoria);
		//
		echo '<script> window.location.href = "?pg=competencias/informes/reporte_individual&e=' . $id_evaluado . '";</script>';
	} else {
		echo '<script> alert("Debe agregar por lo menos dos planes de acción antes de enviar a PDI");</script>';
	}
}

if ($_POST["enviar_intervencion"] != "") {

	$queryRelacion = mysqli_query($connect_valoracion, "SELECT * FROM Relaciones_Laborales WHERE areas LIKE '%" . $_SESSION["area"] . "%' ");
	$dataRelacion = mysqli_fetch_array($queryRelacion);
	if (mysqli_num_rows($queryRelacion) == 0) {
		echo '<script> alert("No tiene asignado el responsable para Intervención de GH");</script>';
		echo '<script> window.location.href = "?pg=competencias/informes/reporte_individual&e=' . $id_evaluado . '";</script>';
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

		$queryEnvio = "UPDATE Competencias_Evaluaciones_New SET proceso_valoracion = 6, update_at = '$hoy' WHERE id_evaluado = " . $id_evaluado . " AND estado = 2";
		// echo $queryEnvio;
		$accion = 'ACTUALIZAR';
		$descripcion = 'Actualización envio de Pdi a intervención GH para el colaborador ' . $colaborador["nombre"];
		mysqli_query($connect_valoracion, $queryEnvio);


		$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $user_log["id_empresa"] . ", " . $user_log["id"] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
		// echo $auditoria;
		mysqli_query($connect_okrs, $auditoria);
		//
		echo '<script> window.location.href = "?pg=competencias/informes/reporte_individual&e=' . $id_evaluado . '";</script>';
	}
}

include 'script_informe.php'; 


function ValidarEvaluaciones($id_evaluado, $id_jefe, $id_empresa){
    global $dataCicloVal;
    global $connect_valoracion;

    $permitir = false;
    $con_auto = false;
    $sin_evaluacion_terminada = true;
    $no_evaluadores = 0;
    $evaluaciones_terminadas = 0;

    $sentenciaEvals = "
        SELECT * FROM Evaluadores
        WHERE 
        Evaluadores.id_empleado = '".$id_evaluado."' AND 
        Evaluadores.anio = '" . $dataCicloVal["anio"] . "' AND 
        Evaluadores.id_ciclo = '" . $dataCicloVal["id"] . "' AND 
        Evaluadores.tipo != 5
    ";
	$queryEvaluadores = mysqli_query($connect_valoracion, $sentenciaEvals);
	while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) { 

        $no_evaluadores++;

        $queryValidacion = mysqli_query($connect_valoracion, "SELECT * FROM  Competencias_Evaluaciones_New
		WHERE id_empresa = '".$id_empresa."'
		AND id_ciclo = '" . $dataCicloVal["id"] . "'
		AND id_evaluado = '" . $dataEvaluadores["id_empleado"] . "'
		AND id_evaluador = '" . $dataEvaluadores["id_evaluador"] . "'
		AND anio = '" . $dataCicloVal["anio"] . "'");
        $dataValidacion = mysqli_fetch_array($queryValidacion);

        if($queryValidacion->num_rows > 0){
            if($dataValidacion["estado"] >= 2){
                $evaluaciones_terminadas++;

                
            }
            else{
                $sin_evaluacion_terminada = false;
            }
        }
        else{
            $sin_evaluacion_terminada = false;
        }
    }

    /*
    if($sin_evaluacion_terminada == false){
        echo "faltan_evaluadiones";

    }
    */

    return array(
        "validacion" => $sin_evaluacion_terminada,
        "evaluaciones" => $no_evaluadores, 
        "terminadas" => $evaluaciones_terminadas
    );

 

}

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

	.btn-gestion {
		color: #fff;
		background-color: #6B21FF;
		border-color: #6B21FF;
		padding: 0.5rem;
		font-size: 1.3rem;
		border-radius: 8px;
	}
	.btn-gestion:hover{
		color: #FFF200 !important;
	}

	#gestionHumanaTitulo {
		color: #fb924e !important;
	}
</style>

<?php
$promedio_general = $datos_generales["promedio_general"];
$porcentaje_general = ($promedio_general * 100) / 4;

if ($porcentaje_general > 100) {
	$porcentaje_general = 100;
}
$color_general = RetornarColor($porcentaje_general, $rangos);
?>

<div class="container-fluid">
	<div class="row">
		<div class="card">
			<div class="card-header" style="background-color: white !important;">
				<div class="row">
					<div class="col-md-12" align="right">
						<h2>Valoración por Competencias <?php echo $_SESSION["anio_ciclo"]; ?></h2>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<h2>Reporte Individual</h2><br>
						<b><?php echo $colaborador["nombre"]; ?></b> <br>
						Documento: <b><?php echo $colaborador["documento"]; ?></b> <br>
						Fecha de Valoración: <b><?php echo $fecha_evaluacion; ?></b> <br>
						Cargo: <b><?php echo $colaborador["nombre_cargo"]; ?></b> <br>
						Vicepresidencia: <b><?php echo $colaborador["nombre_vp"]; ?></b> <br>
						Área: <b><?php echo $colaborador["nombre_area"]; ?></b> <br>
					</div>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<h2 style="margin-bottom: 10px; margin-top: 10px" align="center">
							RESULTADO GLOBAL EVALUACIÓN DE COMPETENCIAS
						</h2>
					</div>
				</div>
				<div class="row" style="align-items: center;">
					<div class="col-md-4" align="center" id="porcentajeTotal">
						<!-- <div class="progreso-bar-container" style="--i:<?php //echo round($porcentaje_general);
																			?>;--clr:<?php //echo $color_general;
																						?>">
							<div class="progreso-bar objetivo-okr">
								<progreso id="objetivo-okr" min="0" value="<?php //echo round(number_format($porcentaje_general, 1));
																			?>"></progreso>
							</div>
						</div>
						<div style="margin-top: 10px; margin-bottom: 20px">
							Resultado Total
						</div> -->
					</div>
					<div class="col-md-8">
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

						$competencias = $competenciasAuto = $competenciasSupervisor = "";

						foreach ($COMPETENCIAS as $competencia) {
							$queryNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $competencia . "' ");
							$dataNivel = mysqli_fetch_array($queryNivel);

							$queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataNivel["id_competencia"] . "' ");
							$dataComp = mysqli_fetch_array($queryComp);

							$competencias .= "'" . eliminar_tildes($dataComp["nombre"]) . "',";

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
							$obj_dimensiones .= "'" . $dataComp["nombre"] . "' , ";
							//$obj_dimensiones .= "'auto - ".round( $general,1)."%', ";
							$competenciasAuto .= $auto . ",";
							$competenciasSupervisor .= $jefe . ", ";
                            $competenciasCliente .= $cliente . ", ";
                            $competenciasPar .= $par . ", ";
                            $competenciasColaborador .= $colaborador . ", ";




							$obj_promedios_par .= " " . $par . ", ";
							$obj_promedios_cola .= " " . $colaborador . ", ";
							$obj_promedios_cliente .= " " . $cliente . ", ";

							// $queryNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $competencia . "' ");
							// $dataNivel = mysqli_fetch_array($queryNivel);

							// $queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataNivel["id_competencia"] . "' ");
							// $dataComp = mysqli_fetch_array($queryComp);

							// $nodo_comp =  ObtenerCompetenciasConsolidadas($competencia, $COMPETENCIAS, $EVALUACIONES);
							// //$datos = ['nombre' => 'Sao', 'edad' => 30];
							// $auto = 0;
							// $jefe = 0;
							// $par = 0;
							// $colaborador = 0;
							// $cliente = 0;
							// foreach ($nodo_comp["evaluadores"] as $eval) {
							// 	$resultado_promediado = ($eval["sin_ponderacion"] / $eval["cantidad"]);
							// 	// echo $resultado_promediado;
							// 	if ($resultado_promediado > 100) {
							// 		$resultado_promediado = 100;
							// 	}

							// 	if ($eval["tipo"] == 1) {
							// 		$auto = $resultado_promediado;
							// 		$auto_total += $resultado_promediado;
							// 	}
							// 	if ($eval["tipo"] == 5) {
							// 		$jefe = $resultado_promediado;
							// 		$jefe_total += $resultado_promediado;
							// 	}
							// 	if ($eval["tipo"] == 2) {
							// 		$par = $resultado_promediado;
							// 		$par_total += $resultado_promediado;
							// 	}
							// 	if ($eval["tipo"] == 3) {
							// 		$colaborador = $resultado_promediado;
							// 		$colaborador_total += $resultado_promediado;
							// 	}
							// 	if ($eval["tipo"] == 4) {
							// 		$cliente = $resultado_promediado;
							// 		$cliente_total += $resultado_promediado;
							// 	}

							// 	$general_total += ($eval["promedio"] / $eval["cantidad"]);
							// }

							// $auto = $auto * 100 / 5;
							// if ($auto > 100) {
							// 	$auto = 100;
							// }
							// $jefe = $jefe * 100 / 5;
							// if ($jefe > 100) {
							// 	$jefe = 100;
							// }
							// $par = $par * 100 / 5;
							// $colaborador = $colaborador * 100 / 5;
							// $cliente = $cliente * 100 / 5;
							// $general = $nodo_comp["general"] * 100 / 5;

							// $color_competencia = RetornarColor($general, $rangos);
							// $color_auto = RetornarColor($auto, $rangos);
							// $color_jefe = RetornarColor($jefe, $rangos);
							// $color_cliente = RetornarColor($cliente, $rangos);
							// $color_par = RetornarColor($par, $rangos);
							// $color_cola = RetornarColor($colaborador, $rangos);

							// $comentarios_competencia = '';
							// //COMENTARIOS
							// foreach ($EVALUACIONES as $evaluacion) {

							// 	$txt_tipo = "";
							// 	if ($evaluacion["tipo_evaluacion"] == 1) {
							// 		$txt_tipo = "Auto";
							// 	}
							// 	if ($evaluacion["tipo_evaluacion"] == 5) {
							// 		$txt_tipo = "Supervisor";
							// 	}
							// 	if ($evaluacion["tipo_evaluacion"] == 2) {
							// 		$txt_tipo = "Par";
							// 	}
							// 	if ($evaluacion["tipo_evaluacion"] == 3) {
							// 		$txt_tipo = "Colaborador";
							// 	}
							// 	if ($evaluacion["tipo_evaluacion"] == 4) {
							// 		$txt_tipo = "Cliente";
							// 	}

							// 	$Array_Objeto = json_decode($evaluacion["obj_evaluacion"], true);
							// }

							// $lista_resultados = "";
							// $lista_fortalezas = "";
							// $lista_oportunidades = "";

							// $queryNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $competencia . "' ");
							// $dataNivel = mysqli_fetch_array($queryNivel);

							// $queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataNivel["id_competencia"] . "' ");
							// $dataComp = mysqli_fetch_array($queryComp);

							// foreach ($nodo_comp["comportamientos"] as $comportamiento) {

							// 	$nodo_comporta =  ObtenerComportamientosConsolidadas($competencia, $comportamiento, $COMPETENCIAS, $EVALUACIONES);

							// 	$queryPregTmp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Preguntas WHERE id = '" . $comportamiento . "'");
							// 	$dataPregTmp = mysqli_fetch_array($queryPregTmp);

							// 	$auto = 0;
							// 	$jefe = 0;
							// 	$par = 0;
							// 	$colaborador = 0;
							// 	$cliente = 0;

							// 	foreach ($nodo_comporta["evaluadores"] as $eval) {
							// 		$resultado_promediado = ($eval["promedio"] / $eval["cantidad"]);

							// 		if ($eval["tipo"] == 1) {
							// 			$auto = $resultado_promediado;
							// 		}
							// 		if ($eval["tipo"] == 5) {
							// 			$jefe = $resultado_promediado;
							// 		}
							// 		if ($eval["tipo"] == 2) {
							// 			$par = $resultado_promediado;
							// 		}
							// 		if ($eval["tipo"] == 3) {
							// 			$colaborador = $resultado_promediado;
							// 		}
							// 		if ($eval["tipo"] == 4) {
							// 			$cliente = $resultado_promediado;
							// 		}

							// 		$general_total += ($eval["promedio"] / $eval["cantidad"]);
							// 	}

							// 	echo "<script>console.log('ULTIMO CICL SECCIÓN 1: " . addslashes($jefe) . "');</script>";
							// 	$auto = $auto * 100 / 5;
							// 	if ($auto > 100) {
							// 		$auto = 100;
							// 	}
							// 	$jefe = $jefe * 100 / 5;
							// 	if ($jefe > 100) {
							// 		$jefe = 100;
							// 	}
							// 	echo "<script>console.log('ULTIMO CICL SECCIÓN 2: " . addslashes($jefe) . "');</script>";

							// 	$par = $par * 100 / 5;
							// 	$colaborador = $colaborador * 100 / 5;
							// 	$cliente = $cliente * 100 / 5;
							// 	$general = $nodo_comporta["general"] * 100 / 5;

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
							//}
						}

						?>
						<div id="spider"></div>
						<script>
							Highcharts.chart('spider', {

								chart: {
									polar: true,
									type: 'line'
								},

								title: {
									text: 'Evaluación de Competencias',
									x: -80
								},

								pane: {
									size: '80%'
								},

								xAxis: {
									categories: [
										<?php echo eliminar_tildes($competencias); ?>
									],
									tickmarkPlacement: 'on',
									lineWidth: 0
								},

								yAxis: {
									gridLineInterpolation: 'polygon',
									lineWidth: 0,
									min: 0
								},

								tooltip: {
									shared: true,
									pointFormat: '<span style="color:{series.color}">{series.name}: <b>' +
										'{point.y:,.0f}</b><br/>'
								},

								legend: {
									align: 'right',
									verticalAlign: 'middle',
									layout: 'vertical'
								},

								series: [
									<?php if ($permitir_auto) { ?> {
											name: 'Auto',
											data: [<?php echo $competenciasAuto; ?>],
											pointPlacement: 'on'
										},
									<?php } ?>
									<?php if ($permitir_jefe) { ?> {
											name: 'Supervisor',
											data: [<?php echo $competenciasSupervisor; ?>],
											pointPlacement: 'on'
										},
									<?php } ?>

                                    <?php if ($permitir_cliente) { ?> {
											name: 'Cliente',
											data: [<?php echo $competenciasCliente; ?>],
											pointPlacement: 'on'
										},
									<?php } ?>

                                    <?php if ($permitir_par) { ?> {
											name: 'Par',
											data: [<?php echo $competenciasPar; ?>],
											pointPlacement: 'on'
										},
									<?php } ?>

                                    <?php if ($permitir_col) { ?> {
											name: 'Colaborador',
											data: [<?php echo $competenciasColaborador; ?>],
											pointPlacement: 'on'
										}
									<?php } ?>


                                    
								],

								responsive: {
									rules: [{
										condition: {
											maxWidth: 500
										},
										chartOptions: {
											legend: {
												align: 'center',
												verticalAlign: 'bottom',
												layout: 'horizontal'
											},
											pane: {
												size: '70%'
											}
										}
									}]
								}

							});
						</script>
						<p style="text-align:justify;">
							La gráfica que encuentra a continuación, le permitirá identificar de manera diferenciada las opiniones y percepciones por competencias de los diferentes evaluadores, analizar y comparar su autoevaluación con respecto a la percepción de los otros evaluadores, con el fin de encontrar puntos ciegos a nivel de fortalezas y áreas de oportunidad. Es importante también la homogeneidad de los resultados y niveles de dispersión entre los evaluadores.
						</p>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-12">
						<div align="center">
							<h2>Escala de interpretación</h2><br><br>
						</div>
						<?php include("comp_escala.php"); ?>
					</div>
				</div>
				<br>
				<div class="row" >
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
				<div class="row" >
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
								//$general = $jefe;
								if ($auto > 0) {
									//$general = $jefe;
								} else {
									$general = $jefe;
								}
								if ($jefe == 0) {
									//$general = 0;
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
								if ($dataLider["id_jefe"] == $user_log["id"] ) {
									if ($permitir_auto) {
										echo '<div class="col">
										<i class="bi bi-caret-down-fill"></i><a class="collapsed" data-bs-toggle="collapse" href="#collapseKr' . $dataComp["id"] . '" aria-expanded="false" aria-controls="collapseKr' . $dataComp["id"] . ';" id="datosResultados_' . $dataComp["id"] . '" style="color: black !important;">
												' . eliminar_tildes($dataComp["nombre"]) . '
											</a>
										</div>
										<div class="col" style="text-align:center;">
										' . number_format($auto, 0) . '%
										</div>';
									} else {
										echo '<div class="col">
										<i class="bi bi-caret-down-fill"></i><a class="collapsed" data-bs-toggle="collapse" href="#collapseKr' . $dataComp["id"] . '" aria-expanded="false" aria-controls="collapseKr' . $dataComp["id"] . ';" id="datosResultados_' . $dataComp["id"] . '" style="color: black !important;">
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
									<button type="button" class="btn btn-primary" onClick="AgregarPlan(' . $dataComp["id"] . ', ' . $user_log["id"] . ',' . $id_evaluado . ', ' . $user_log["id_empresa"] . ')" data-bs-toggle="tooltip" title="Agregar Plan de Acción" style="border-radius: 3px !important;">
								<i class="bx bx-plus" style="color: white !important;"></i> Agregar Plan
								</button>
									</div>';
								} 

                                else {
									if ($permitir_auto) {
										echo '<div class="col">
										<i class="bi bi-caret-down-fill"></i><a class="collapsed" data-bs-toggle="collapse" href="#collapseKr' . $dataComp["id"] . '" aria-expanded="false" aria-controls="collapseKr' . $dataComp["id"] . ';" id="datosResultados_' . $dataComp["id"] . '" style="color: black !important;">
												' . eliminar_tildes($dataComp["nombre"]) . '
											</a>
										</div>
										<div class="col" style="text-align:center;">
										' . number_format($auto, 0) . '%
										</div>';
									} else {
										echo '<div class="col">
										<i class="bi bi-caret-down-fill"></i><a class="collapsed" data-bs-toggle="collapse" href="#collapseKr' . $dataComp["id"] . '" aria-expanded="false" aria-controls="collapseKr' . $dataComp["id"] . ';" id="datosResultados_' . $dataComp["id"] . '" style="color: black !important;">
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
							<div id="collapseKr' . $dataComp["id"] . '" class="collapse" role="tabpanel" aria-labelledby="heading' . $dataComp["id"] . '" data-bs-parent="#accordionIcons">
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
								//$general = $jefe;

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


								<div class="row">
									<div class="col-md-4">
										<div class="row" style="text-align:center;">
											<div class="col-md-12" style="text-align: -webkit-center;">


												<div class="progreso-bar-container" style="--i:<?php echo round($general); ?>;--clr:<?php echo $color_competencia; ?>">
													<div class="progreso-bar objetivo-okr"><?php echo number_format($general, 1); ?>%
														
													</div>
												</div>
												<div style="margin-top: 10px; margin-bottom: 20px">
													Resultado Total
												</div>

											</div>

										</div>
										<div class="row" style="text-align:center;">
											<div class="col-md-12">
												<figure class="highcharts-figure">
													<div id="grafica_<?php echo $dataComp["id"]; ?>"></div>
												</figure>
											</div>
											<style>
												#grafica_<?php echo $dataComp["id"]; ?> {
													max-width: 100%;
													margin: 1em auto;
												}
											</style>
											<script>
												Highcharts.chart('grafica_<?php echo $dataComp["id"]; ?>', {
													chart: {
														type: 'column'
													},
													title: {
														text: 'Gráfico Competencia',
														align: 'center'
													},
													xAxis: {
														categories: ['Porcentaje Competencias']
													},
													yAxis: {
														min: 0,
														title: {
															text: 'Porcentaje'
														}
													},
													tooltip: {
														valueSuffix: '%'
													},
													plotOptions: {
														column: {
															pointPadding: 0.2,
															borderWidth: 0
														}
													},
													series: [
														<?php if ($permitir_auto) {
														?> {
																name: 'Auto',
																data: [<?php echo round($auto, 1); ?>],
																color: '<?php echo RetornarColor($auto, $rangos) ?>'
															},
														<?php } ?>
														<?php if ($permitir_jefe) {
														?> {
																name: 'Supervisor',
																data: [<?php echo round($jefe, 1); ?>],
																color: '<?php echo RetornarColor($jefe, $rangos) ?>'
															},
														<?php } ?>
														<?php if ($permitir_cliente) {
														?> {
																name: 'Cliente',
																data: [<?php echo round($cliente, 1); ?>],
																color: '<?php echo RetornarColor($cliente, $rangos) ?>'
															},
														<?php } ?>
														<?php if ($permitir_par) {
														?> {
																name: 'Par',
																data: [<?php echo round($par, 1); ?>],
																color: '<?php echo RetornarColor($par, $rangos) ?>'
															},
														<?php } ?>
														<?php if ($permitir_col) {
														?> {
																name: 'Colaborador',
																data: [<?php echo round($colaborador, 1); ?>],
																color: '<?php echo RetornarColor($colaborador, $rangos) ?>'
															}
														<?php } ?>

													]
												});
											</script>

										</div>
									</div>
									<div class="col-md-1"></div>
									<div class="col-md-7">
										<?php
										$lista_resultados = "";
										$lista_fortalezas = "";
										$lista_oportunidades = "";
										$queryNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $competencia . "' ");
										$dataNivel = mysqli_fetch_array($queryNivel);

										$queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataNivel["id_competencia"] . "' ");
										$dataComp = mysqli_fetch_array($queryComp);

										$VALIDACION = PromedioGeneralEvaluadoPreguntas($id_evaluado, $connect_valoracion, $connect_admin);

                                       
										//$datos_ponderar = $VALIDACION["datos_ponderar"];
										foreach ($nodo_comp["comportamientos"] as $comportamiento) {

                                      

											$nodo_comporta = ObtenerComportamientosSinConsolidarPreguntas($competencia, $comportamiento, $COMPETENCIAS, $EVALUACIONES);
                                            $nodo_comporta_dos =  ObtenerComportamientosConsolidadasPreguntas($competencia, $comportamiento, $COMPETENCIAS, $EVALUACIONES);

                                            //print_r($nodo_comporta_dos);

											$queryPregTmp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Preguntas WHERE id = '" . $comportamiento . "'");
											$dataPregTmp = mysqli_fetch_array($queryPregTmp);

											$auto = 0;
											$jefe = 0;
											$par = 0;
											$colaborador = 0;
											$cliente = 0;

											foreach ($nodo_comporta["evaluadores"] as $eval) {

												$resultado_promediado = ($eval["promedio"] / $eval["cantidad"]);
                                                
												if ($eval["tipo"] == 1) {
													$auto = $resultado_promediado;
												}
												if ($eval["tipo"] == 5) {
                                                    
													$jefe = $resultado_promediado;
												}
												if ($eval["tipo"] == 2) {
													$par = $resultado_promediado;
												}
												if ($eval["tipo"] == 3) {
													$colaborador = $resultado_promediado;
												}
												if ($eval["tipo"] == 4) {
													$cliente = $resultado_promediado;
												}


												$general_total += ($eval["promedio"] / $eval["cantidad"]);
											}


											$auto = $auto * 100 / 5;
											if ($auto > 100) {
												//$auto = 100;
											}
											$jefe = $jefe * 100 / 5;
											if ($jefe > 100) {
												$jefe = 100;
											}

											$par = $par * 100 / 5;
											$colaborador = $colaborador * 100 / 5;
											$cliente = $cliente * 100 / 5;
											$general = $nodo_comporta_dos["general"] * 100 / 5;
											//$general = $jefe;
											if ($general > 100) {
												$general = 100;
											}

											if ($general >= 88) {
												if ($dataPregTmp["pregunta"]) {
													$lista_fortalezas .= "<li>" . $dataPregTmp["pregunta"] . "</li>";
												}
											}

											if ($general <= 75) {
												if ($dataPregTmp["pregunta"]) {
													$lista_oportunidades .= "<li>" . $dataPregTmp["pregunta"] . "</li>";
												}
											}

											$lista_resultados .= '
                                                <tr>
                                                    <td>' . $dataPregTmp["pregunta"] . '</td>
                                                    <td>' . $dataComp["nombre"] . '</td>
                                            ';

											if ($permitir_auto) {
												$lista_resultados .= '<td>' . round($auto, 1) . '%. </td>';
											}
											if ($permitir_jefe) {
												$lista_resultados .= '<td>' . round($jefe, 1) . '% </td>';
											}
											if ($permitir_cliente) {
												$lista_resultados .= '<td>' . round($cliente, 1) . '%</td>';
											}
											if ($permitir_par) {
												$lista_resultados .= '<td>' . round($par, 1) . '%</td>';
											}
											if ($permitir_col) {
												$lista_resultados .= '<td>' . round($colaborador, 1) . '%</td>';
											}

											$lista_resultados .= '<td>' . round($general, 1) . '%</td>
                                                </tr>
                                            ';
										}

										?>

										<?php if ($lista_fortalezas) { ?>
											<h2 style="margin-bottom: 20px; margin-top: 20px" align="center">
												COMPORTAMIENTOS QUE SE CONSTITUYEN EN FORTALEZA
											</h2>
											<div>
												<ul>
													<?php echo eliminar_tildes($lista_fortalezas); ?>
												</ul>
											</div>
										<?php } ?>

										<br><br>
										<?php if ($lista_oportunidades) { ?>
											<h2 style="margin-bottom: 20px; margin-top: 20px" align="center">
												COMPORTAMIENTOS CON ÁREAS DE OPORTUNIDAD
											</h2>
											<div>
												<ul>
													<?php echo eliminar_tildes($lista_oportunidades); ?>
												</ul>
											</div>
										<?php } ?>

										<br><br>
										<?php if ($comentarios_competencia) { ?>
											<h2 style="margin-bottom: 20px; margin-top: 20px" align="center">
												COMENTARIOS POR COMPETENCIA
											</h2>
											<div>
												<ul>
													<?php echo eliminar_tildes($comentarios_competencia); ?>
												</ul>
											</div>
										<?php } ?>

										<br><br>
										<h2 style="margin-bottom: 20px; margin-top: 20px" align="center">
											RESULTADOS POR COMPORTAMIENTO
										</h2>
										<p>
											A continuación encontrará las fortalezas y las áreas de oportunidad por cada una de las competencias.<br><br>

											Los comportamientos por encima de 80% se consideraron como fortalezas y los que están por debajo
											de este porcentaje se categorizaron dentro de las áreas de oportunidad.
											Cabe mencionar que el sistema tomó la calificación de cada evaluador frente a cada comportamiento, generando un promedio final, el cual se tomo como base para la clasificación y la identificación del nivel de desarrollo.<br><br>

											Los análisis y recomendaciones parten de los rangos de calificación establecidos a partir de la
											evidencia, frecuencia y consistencia de los comportamientos asociados a cada competencia. <br><br>
										</p>
										<table class="table" style="margin-bottom: 50px">
											<tr class="table-dark">
												<th>Comportamiento</th>
												<th>Competencia</th>
												<?php if ($permitir_auto) { ?><th>Auto <?= $datos_ponderar["auto"]; ?>%</th><?php } ?>
												<?php if ($permitir_jefe) { ?><th>Líder/Supervisor <?= $datos_ponderar["jefe"]; ?>%</th><?php } ?>
												<?php if ($permitir_cliente) { ?><th>Cliente Interno <?= $datos_ponderar["cliente"]; ?>%</th><?php } ?>
												<?php if ($permitir_par) { ?><th>Par <?= $datos_ponderar["par"]; ?>%</th><?php } ?>
												<?php if ($permitir_col) { ?><th>Colaborador <?= $datos_ponderar["subalterno"]; ?>%</th><?php } ?>
												<th>Resultado</th>
											</tr>
											<?php echo eliminar_tildes($lista_resultados); ?>
										</table>
									</div>

								</div>



							<?php
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

                            ///echo count($general_total);


							$total_auto = round($promedio_auto / count($COMPETENCIAS), 2);
							$total_jefe = round($promedio_jefe / count($COMPETENCIAS), 2);
                            $total_cliente = round($promedio_cliente / count($COMPETENCIAS), 2);
                            $total_par = round($promedio_par / count($COMPETENCIAS), 2);
                            $total_colaborador = round($promedio_colaborador / count($COMPETENCIAS), 2);
							?>

						</div>
					</div>
				</div>
				<div class="row" > 

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
							<div class="col" style="background-color: #212529;color: white;text-align:center; display:none">TOTAL</div>
							<div class="col" style="background-color: #212529;color: white;text-align:center; display:none"><?php echo number_format($total_auto, 1); ?>%</div>
						<?php } else { ?>
							<div class="col" style="background-color: #212529;color: white;text-align:center; display:none">TOTAL</div>
						<?php } ?>
						<div class="col" style="background-color: #212529;color: white;text-align:center;"><?php echo number_format($total_jefe, 1); ?>%</div>
						<!-- <div class="col-md-2" style="background-color: #212529;color: white;text-align:center;"><?php //echo number_format($porcentaje_general, 1);
																														?>%</div> -->
						<div class="col" style="background-color: #212529;color: white;text-align:center; display:none"><?php echo number_format($total_jefe, 1); ?>%</div>
						<div class="col" style="background-color: #212529;color: white; display:none"></div>
					<?php } else { ?>
						<?php if ($permitir_auto) { ?>
							<div class="col" style="background-color: #212529;color: white;text-align:center; display:none">TOTAL</div>
							<div class="col" style="background-color: #212529;color: white;text-align:center; display:none"><?php echo number_format($total_auto, 1); ?>%</div>
						<?php } else { ?>
							<div class="col" style="background-color: #212529;color: white;text-align:center; display:none">TOTAL</div>
						<?php } ?>
						<div class="col" style="background-color: #212529;color: white;text-align:center; display:none"><?php echo number_format($total_jefe, 1); ?>%..</div>
						<!-- <div class="col-md-2" style="background-color: #212529;color: white;text-align:center;"><?php //echo number_format($porcentaje_general, 1);
																														?>%</div> -->
						<div class="col" style="background-color: #212529;color: white;text-align:center; display:none"><?php echo number_format($total_jefe, 1); ?>%</div>
					<?php }
					//$color_general = RetornarColor($total_jefe, $rangos); ?>

                    
					<script>
						$(document).ready(function() {
							$("#porcentajeTotal").html('<div class="progreso-bar-container" style="--i:<?php echo $PROMEDIO_GLOBAL_REPORTE; ?>;--clr:<?php echo $color_general; ?>">' +
								'<div class="progreso-bar objetivo-okr"> <?php echo $PROMEDIO_GLOBAL_REPORTE; ?>%' +
								'</div>' +
								'</div>' +
								'<div style="margin-top: 10px; margin-bottom: 20px">Resultado Total</div>'
							);
						});
					</script>
				</div>
			</div>
			<div class="card-footer">
				<div class="row">
					<div class="col-md-12">
						<h5 style="margin-bottom: 10px; margin-top: 10px" align="center">PLAN DE DESARROLLO INDIVIDUAL</h5>
					</div>
				</div>
				<?php
				if ($competenciasEval["proceso_valoracion"] >= 3 || $user_log["id"] == $dataLider["id_jefe"]) {
				?>
					<div class="row">
						<div class="col-md-12">
							<table id="pdi" class="display table" style="width:100%;">
								<thead>
									<tr>
										<th>Año</th>
										<th>Competencia</th>
										<th>Plan de Acción</th>
										<th>Prioridad</th>
										<th>Jefe</th>
										<th>Fecha Inicio</th>
										<th>Fecha Entrega</th>
										<th>Estado</th>
										<th>Acciones</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$queryPDIS = mysqli_query($connect_valoracion, "SELECT *, YEAR(created_at) AS anio FROM Pdi_Competencias WHERE id_empleado = '" . $id_evaluado . "' AND YEAR(created_at) = '".$_SESSION["anio_ciclo"]."'  ");
									while ($dataPDIS = mysqli_fetch_array($queryPDIS)) {
										$queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataPDIS["id_competencia"] . "' ");
										$dataComp = mysqli_fetch_array($queryComp);
										$QRYJefes = mysqli_query($connect_admin, "SELECT nombre AS NOMBRES FROM Empleados WHERE id = '" . $dataPDIS['id_jefe'] . "'");
										$datosJefes = mysqli_fetch_array($QRYJefes);
										$nombreJefe = $datosJefes['NOMBRES'];
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
											<td><?php echo $dataPDIS['anio']; ?></td>
											<td><?php echo eliminar_tildes($dataComp["nombre"]); ?></td>
											<td><?php echo $dataPDIS["plan_accion"]; ?></td>
											<td><span class="<?php echo $colorPrioridad; ?>" style="color:<?php echo $colorBadgeP; ?>  !important;font-size: 13px !important;"><b><?php echo $txtPrioridad; ?></b></span></td>
											<td><?php echo $nombreJefe; ?></td>
											<td><?php echo $dataPDIS["fecha_inicia"]; ?></td>
											<td><?php echo $dataPDIS["fecha_finaliza"]; ?></td>
											<td><span class="<?php echo $colorEstado; ?>" style="color:<?php echo $colorBadge; ?>  !important;font-size: 13px !important;"><b><?php echo $txtEstado; ?></b></span></td>
											<td>
												<button type="button" class="btn btn-light btn-sm bt_editar" title="Editar Plan" onclick="EditarPlan(<?php echo $dataPDIS["id"] . "," . $user_log["id"] . "," . $id_evaluado . "," . $user_log["id_empresa"]; ?>)">
													<i class="bx bx-edit"></i>
												</button>

                                                <?php if ($procesoValoracionActual <= 3) { ?>
												<button type="button" class="btn btn-danger btn-sm bt_editar" title="Eliminar Plan" onclick="EliminarPlan(<?php echo $dataPDIS["id"] . "," . $user_log["id"] . "," . $id_evaluado . "," . $user_log["id_empresa"]; ?>)">
													<i class="bx bx-trash"></i>
												</button>
                                                <?php } ?>
											</td>
										</tr>

									<?php
									}
									?>
								</tbody>
							</table>
						</div>
						<!-- Modal Bootstrap -->
						<div class="modal fade" id="modalIntervencion" tabindex="-1" aria-labelledby="modalIntervencionLabel" aria-hidden="true">
							<div class="modal-dialog modal-lg">
								<div class="modal-content">

									<form action="" method="post" id="formIntervencion">
										<div class="modal-header">
											<h5 class="modal-title" id="modalIntervencionLabel">Nueva Intervención</h5>
											<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
										</div>

										<div class="modal-body">
											<div class="form-group">
												<label for="descripcion_html">Descripción (acepta HTML):</label>
												<textarea name="descripcion_html" id="descripcion_html" class="form-control" rows="6" placeholder="Registrar comentarios, observaciones o acuerdos derivados de la intervención..." maxlength="500"><?php if($comentarioGh!='' && isset($comentarioGh)){ echo $comentarioGh; } ?></textarea>
											</div>
										</div>

										<div class="modal-footer">
											<button type="submit" class="btn btn-success" data-bs-dismiss="modal">Guardar</button>
											<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
										</div>
									</form>

								</div>
							</div>
						</div>
						<div class="container mt-5">
							<?php 
							//if ($procesoValoracionActual == 6 && ($areaUsuario == 341 || $rolUsuario == 1)) { 
                            if ($procesoValoracionActual == 6 && ( $user_log["permiso_relaciones_laboradores"] || $user_log["role"] == 1 )) { 
                               
							?>
								<div class="d-flex justify-content-center mb-3" >
									<button type="button" class="btn btn-gestion btn-sm" data-bs-toggle="modal" data-bs-target="#modalIntervencion">
										Cerrar valoración desde GH
									</button>
                                    <button type="button" class="btn btn-warning btn-sm" style="font-size: 1.3rem;" onclick="AbrirDesdeGH()" >
										Reabrir para Firma
									</button>
								</div>
							<?php //Final de verificación INTERVENCIÓN GH
							}
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
											<?php if($IdentResponsable==$idUser){ ?>
											<div class="text-end">
												<button type="button" class="btn w-auto" style="color:white !important; background-color: #6B21FF; border-color: #6B21FF;" data-bs-toggle="modal" data-bs-target="#modalIntervencion">Editar</button>
											</div>
											<?php } ?>
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
								<div class="row justify-content-center g-4">
									<!-- Tarjeta 2: Competencias a Desarrollar -->
									<div class="col-md-8">
										<div class="card shadow p-4 h-100">
											<h5 class="text-center mb-4 fw-bold" style="color: #59008e !important;">RECOMENDACIONES PARA GENERAR PLANES DE ACCIÓN</h5>

											<h6 class="fw-bold mt-3" style="color: #59008e !important;">Aprendizaje a través de la experiencia</h6>
											<ul>
												<li><strong>Desafíos y Nuevas Responsabilidades:</strong> Asigna a los colaboradores nuevos retos y responsabilidades que expandan sus habilidades.</li>
												<li><strong>Exposición a Proyectos:</strong> Involucra al equipo en proyectos clave y asignaciones novedosas para que adquieran experiencia práctica en áreas críticas.</li>
											</ul>

											<h6 class="fw-bold mt-3" style="color: #59008e !important;">Interacción con otros</h6>
											<ul>
												<li><strong>Sesiones de Retroalimentación:</strong> Facilita sesiones regulares de retroalimentación con líderes, colegas y clientes para identificar áreas de mejora y celebrar los logros.</li>
												<li><strong>Mentoría:</strong> Establece relaciones de mentoría y coaching dentro del equipo, proporcionando guía y apoyo personalizado.</li>
												<li><strong>Observación:</strong> Fomenta la observación de mejores prácticas dentro y fuera del equipo, para aprender de ejemplos reales y aplicables.</li>
											</ul>

											<h6 class="fw-bold mt-3" style="color: #59008e !important;">Programas educativos</h6>
											<ul>
												<li><strong>Capacitaciones:</strong> Promueve la participación en capacitaciones, cursos y programas de e-learning que sean relevantes para el desarrollo profesional del colaborador.</li>
												<li><strong>Autoestudio:</strong> Incentiva el autoestudio y la lectura de materiales especializados que complementen el conocimiento y habilidades requeridas.</li>
											</ul>
										</div>
									</div>
									<?php $sentencia = "SELECT
									Empleados.id AS id, Empleados.id_empresa AS id_empresa, Empleados.nombre AS nombre, Empleados.correo AS correo_corporativo, Empleados.correo_personal AS correo_personal, Empleados.telefono_movil AS celular, Empleados.documento AS documento, Empleados.role AS role, Empleados.estado AS estado,
									Empleados.foto AS foto,
									Posiciones.id AS id_posicion, Cargos.id AS id_cargo, Cargos.nombre AS nombre_cargo, Areas.nombre AS nombre_area, Areas.id AS id_area
									FROM Empleados
									LEFT JOIN Posiciones ON Empleados.id_posicion = Posiciones.id
									LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
									LEFT JOIN Areas ON Areas.id = Empleados.area
									WHERE Empleados.id > 0 " . $filtros . " AND Empleados.id_empresa = '" . $user_log["id_empresa"] . "'
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

											<!-- Tarjeta 1: Instrucciones de PDI -->
											<div class="col-md-4">
												<div class="card shadow p-4 h-100" style="box-shadow: 0 .1rem 0.4rem #59008e!important;">
													<h5 class="text-center mb-4 fw-bold">Antes de enviar tu PDI, por favor ten en cuenta:</h5>
													<ul class="mb-4">
														<li>Debes haber creado <strong>al menos dos planes de acción</strong> correspondientes al <strong>año o ciclo en curso</strong>.</li>
														<li style="display:none" >Si ya tienes planes de acción, verifica que <strong>no sean del año anterior</strong>.</li>
														<li style="display:none" >Si los planes existentes pertenecen a otro periodo, <strong>crea dos nuevos planes actualizados para este ciclo</strong>.</li>
													</ul>
													<p class="mb-4">Una vez cumplas con estos requisitos, podrás realizar el envío del PDI sin inconvenientes.</p>
													<p class="mb-4">👉 Haz clic en <strong>"Enviar PDI"</strong> cuando estés listo.</p>





                                                    <?php
                                                    //REGLAS PARA EL JEFE
                                                    $permitir_jefe = ValidarEvaluaciones($id_evaluado, $user_log["id"], 1);

                                                    if(!$permitir_jefe["validacion"]){
                                                            $txt_estado = "Otros evaluadores en curso ".$permitir_jefe["terminadas"]."/".$permitir_jefe["evaluaciones"]; 
                                                            echo '
                                                            <div class="alert alert-warning" role="alert">
                                                                '.$txt_estado.'
                                                            </div>
                                                            ';
                                                    }
                                                    else{
                                                    ?>
													<div class="text-center">
														<form method="post">
															<input type="hidden" name="enviar_pdi" value="true">
															<input type="hidden" name="id_jefe" value="<?php echo $_SESSION['id_user']; ?>">
															<button type="submit" class="btn btn-success">Envío (PDI)</button>
														</form>
													</div>
                                                    <?php } ?>




												</div>
											</div>
											<?php
											/*Fin comprobación de proceso de ENVIO PDI*/
										} else {
											if ($competenciasEval["proceso_valoracion"] == 7 && ($competenciasEval["firma_aprobacion"] != "" || $competenciasEval["firma_aprobacion"] == "")) {
											?>
												<div class="col-md-4">
													<div class="card shadow p-4 h-100" style="box-shadow: 0 .1rem 0.4rem #59008e!important;">
														<img src="<?php echo $competenciasEval["firma_aprobacion"]; ?>">
														<p class="mb-4 fw-bold">Firma de Aprobación: <?php echo $colaborador["nombre"]; ?></p>
														<p class="mb-4 fw-bold">Documento: <?php echo $colaborador["documento"]; ?></p>
														<p class="mb-4 fw-bold">Fecha de firma: <?php echo $competenciasEval["fecha_firma"]; ?></p>
													</div>
												</div>
									<?php		}
										}
									}
									?>
								</div>
							<?php
								// Fin de validación en caso de ser diferente a la intervención de GH
							}
							?>
						</div>
					</div>
				<?php } else { ?>
					<div class="row">
						<div class="col-md-12" style="text-align: center;">
							<h5 style="color: red !important;">En este momento no tiene planes de acción asignados</h5>
						</div>
					</div>
				<?php } ?>
				<?php
				$sentencia = "SELECT
				Empleados.id AS id, Empleados.id_empresa AS id_empresa, Empleados.nombre AS nombre, Empleados.correo AS correo_corporativo, Empleados.correo_personal AS correo_personal, Empleados.telefono_movil AS celular, Empleados.documento AS documento, Empleados.role AS role, Empleados.estado AS estado,
				Empleados.foto AS foto,
				Posiciones.id AS id_posicion, Cargos.id AS id_cargo, Cargos.nombre AS nombre_cargo, Areas.nombre AS nombre_area, Areas.id AS id_area
				FROM Empleados
				LEFT JOIN Posiciones ON Empleados.id_posicion = Posiciones.id
				LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
				LEFT JOIN Areas ON Areas.id = Empleados.area
				WHERE Empleados.id > 0 " . $filtros . " AND Empleados.id_empresa = '" . $user_log["id_empresa"] . "'
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
				} else {
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

		$('#modalIntervencion').on('shown.bs.modal', function() {
			if (!window.editor) { // evitar múltiples inicializaciones
				ClassicEditor
					.create(document.querySelector('#descripcion_html'))
					.then(ed => {
						window.editor = ed;
						editor.model.document.on('change:data', () => {
							const text = editor.getData().replace(/<[^>]*>/g, '');
							if (text.length > 500) {
								//Delimitar a 500 caracteres
								const trimmedText = text.substring(0, 500);
								editor.setData(trimmedText);
								//Mostrar alerta de máximo de caracteres
								alert('Has alcanzado el límite de 500 caracteres.');
							}
						});
					})
					.catch(err => {
						console.error('Error al inicializar CKEditor:', err);
					});
			}
		});

		/** Enviar los comentarios de la intervención GH*/
		$('#formIntervencion').on('submit', function(e) {
			//e.preventDefault();
			if (!window.editor) {
				alert('El editor no está inicializado.');
				return;
			}
			const htmlContent = window.editor.getData(); // Usar window.editor
			/** Cargar GestionValoraciones */
			let controller = '<?php echo $url; ?>app/controllers/Competencias/';

			fetch(controller + 'GestionValoracionesController.php', {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify({
						descripcion_html: htmlContent,
						id_empresa: <?php echo $keyEmpresa; ?>,
						id_responsable: <?php echo $idUser; ?>,
						id_evaluado: <?php echo $keyEvaluado; ?>,
						id_evaluacion: <?php echo $keyEvaluacion; ?>,
						ciclo: <?php echo $ciclo; ?>
					})
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						// Cerrar modal
						$('#modalIntervencion').modal('hide');
						// Mostrar contenido en pantalla (usa data.comentario si quieres)
						$('#comentarioIntervencion').html(data.comentario || 'Intervención guardada');
						window.editor.setData(''); // limpiar editor
					} else {
						alert('Ocurrio un error al guardar.');
					}
				})
				.catch(error => {
					console.error('Error: ', error);
				});

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
				//console.log(resp);
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
				//console.log(resp);
			})
			.always(function(resp) {});
	}

	var permitir = false;

	function EliminarPlan(id_plan) {
		if (permitir == false) {
			$("#modal_general").modal("show");
			$("#modal_body").html('Está a punto de elimianr un plan individual de desarrollo. Esta acción es irreversible. ¿Está seguro?<br><br>');
			$("#modal_body").append('<button type="button" class="btn btn-danger btn-sm bt_editar"  onclick="permitir = true; EliminarPlan(' + id_plan + ')">Eliminar</button>');
		} else {

			jQuery.ajax({
					url: api + "eliminar_plan_accion.php",
					type: 'post',
					data: {
						id_plan: id_plan,
						url: '?pg=competencias/informes/reporte_individual&e=<?php echo $id_evaluado; ?>'
					},
				}).done(function(resp) {
					$('#xscript').html(resp);
				})
				.fail(function(resp) {
					//console.log(resp);
				})
				.always(function(resp) {});
		}
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
				//console.log(resp);
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
				//console.log(resp);
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

<script>
    function AbrirDesdeGH(){

        data = {
            id_empresa: <?php echo $keyEmpresa; ?>,
            id_responsable: <?php echo $idUser; ?>,
            id_evaluado: <?php echo $keyEvaluado; ?>,
            id_evaluacion: <?php echo $keyEvaluacion; ?>,
            ciclo: <?php echo $ciclo; ?>, 
            url: '?pg=competencias/informes/reporte_individual&e=<?php echo $id_evaluado; ?>'
        };


        if (permitir == false) {
			$("#modal_general").modal("show");
			$("#modal_body").html('Está a punto de Reabrir para firma. Esta acción es irreversible. ¿Está seguro?<br><br>');
			$("#modal_body").append('<button type="button" class="btn btn-danger btn-sm bt_editar"  onclick="permitir = true; AbrirDesdeGH()">Reabrir para firma</button>');
		} else {

			jQuery.ajax({
					url: api + "reabrir_para_firma.php",
					type: 'post',
					data: data,
				}).done(function(resp) {
					$('#xscript').html(resp);
				})
				.fail(function(resp) {
					//console.log(resp);
				})
				.always(function(resp) {});
		} 
        
    }
</script>