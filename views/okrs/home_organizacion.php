<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script>
	$(".menu_section").addClass("active");
	jQuery("#menu_okrs").css("display", "none");
	$("#bt_okrs_organizacion").addClass("current-page");
</script>
<style>
	.iconoPA {
		display: inline-block;
		width: 25px;
		height: 25px;
		background-repeat: no-repeat;
		background-size: contain;
		margin-left: 5px;
		vertical-align: middle;
	}

	.tooltip-container {
		position: relative;
		display: inline-block;
	}

	/* Estilos para el tooltip */
	.tooltip-text {
		visibility: hidden;
		width: 300px;
		background-color: rgba(0, 0, 0, 0.75);
		color: #ffffff !important;
		text-align: justify;
		border-radius: 5px;
		padding: 10px;
		position: absolute;
		z-index: 1;
		bottom: 125%;
		left: 50%;
		margin-left: -150px;
		opacity: 0;
		transition: opacity 0.3s;

	}

	/* Muestra el tooltip cuando el ratón pasa por encima del input */
	.tooltip-container:hover .tooltip-text {
		visibility: visible;
		opacity: 1;
	}

	.avatar3 {
		font-size: 12px !important;
		width: 23px !important;
		height: 23px !important;
		border-radius: 50%;
		background-color: #26b99a !important;
		color: white !important;
		/* Asegúrate de que el texto sea visible */
		display: inline-flex;
		align-items: center;
		justify-content: center;
		text-align: center;
		position: relative;
		z-index: 1;
	}

	.avatar4 {
		font-size: 15px !important;
		width: 35px !important;
		height: 35px !important;
		border-radius: 50% !important;
		background-color: gray !important;
		color: white !important;
		display: inline-flex !important;
		align-items: center !important;
		justify-content: center !important;
	}

	.notificacion {
		position: relative;
		display: inline-block;
		cursor: pointer;
	}

	.badge-notificacion {
		position: absolute;
		top: -5px;
		right: -5px;
		background-color: #26b99a;
		color: white !important;
		border-radius: 50%;
		padding: 5px 8px;
		font-size: 11px;
	}

	.card-footer,
	.card,
	.card-body {
		background-color: white !important;
	}

	.planificado {
		background-color: #f8f9fa !important;
		color: black !important;
	}

	.en_progreso {
		background-color: #0d6efd !important;
		color: white !important;
	}

	.en_revision {
		background-color: #ffc107 !important;
		color: black !important;
	}

	.completado {
		background-color: #198754 !important;
		color: white !important;
	}

	.bajo {
		background-color: #f8f9fa !important;
		color: black !important;
	}

	.medio {
		background-color: #198754 !important;
		color: white !important;
	}

	.alto {
		background-color: #ffc107 !important;
		color: black !important;
	}

	.urgente {
		background-color: #dc3545 !important;
		color: white !important;
	}

	.bt_editar_pa {
		color: #000 !important;
		background-color: #f8f9fa !important;
		border-color: #f8f9fa !important;
	}

	.bt_editar_pa:hover {
		background-color: #007BFF !important;
		color: white !important;
	}

	.avatarPA {
		font-size: 15px;
		width: 35px;
		height: 35px;
		border-radius: 50%;
		background-color: gray;
		color: white;
		display: inline-flex;
		align-items: center;
		justify-content: center;
	}

	.avatarPA::after {
		content: attr(data-label);
	}
</style>
<link rel="stylesheet" href="<?php echo $url; ?>css/okrs_visual.min.css">
<link rel="stylesheet" href="<?php echo $url; ?>views/okrs_equipos/reportes/styles.css">
<?php
include("views/okrs_equipos/functions.php");

$registros_por_pagina = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$inicio = ($page - 1) * $registros_por_pagina;

$congelar_okrs = true;
// print_r($_SESSION);
if ($_SESSION["anio_fill"] == $dtEmpresa["anio_curso"]) {
	$congelar_okrs = false;
}

if ($_SESSION["id_empresa"] == 1) {
	if ($dtEmpresa["anio_curso"] == '2025' && $_SESSION["anio_fill"] == '2025') {
		$congelar_okrs = false;
	}
}


$hoy = date("Y-m-d H:i:s");

if ($_POST["filtro_okrs"] != "") {
	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '";</script>';
}

if ($_POST["mover_resultado"] != "") {

	$id_okr = $_POST["id_okrs"];
	if ($_POST["id_registro"] != "") {
		$id_okr = MoverResultado($connect_okrs, $_POST["okr_fill"], $_POST["id_registro"], $_SESSION["id_empresa"], $_SESSION["id_user"]);
	}

	// echo '<script> window.location.href = "";</script>';
	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_registro"] . '";</script>';
}

if ($_POST["reubicar_resultado"] != "") {

	$id_okr = $_POST["id_registro"];
	if ($_POST["id_registro"] != "") {
		ReubicarResultado($connect_okrs, $_POST, $id_okr, $hoy, $_SESSION["id_empresa"], $_SESSION["id_user"]);
	}

	// echo '<script> window.location.href = "";</script>';
	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_registro"] . '";</script>';
}

if ($_POST["duplicar_resultado"] != "") {

	$id_okr = $_POST["id_okrs"];
	if ($_POST["id_registro"] != "") {
		$resultado = DuplicarResultado($connect_okrs, $_POST, $id_okr, $hoy, $_SESSION["id_empresa"], $_SESSION["id_user"]);
	}

	// echo '<script> window.location.href = "";</script>';
	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $resultado . '";</script>';
}

//PARA GUARDAR RESULTADOS CLAVES
//PARA GUARDAR RESULTADOS CLAVES
if ($_POST["guardar_resultado"] != "") {
	GuardarResultado($_POST, $connect_okrs, $dtEmpleado["role"], $_SESSION["id_empresa"], $_SESSION["id_user"]);
	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_registro"] . '";</script>';
}

//PARA GUARDAR UNA INICIATIVA
//PARA GUARDAR UNA INICIATIVA
if ($_POST["guardar_iniciativa"] != "") {
	// print_r($_POST);
	$iniciativa = GuardarInciativa($_POST, $connect_okrs, $dtEmpleado["id"], $_SESSION["id_empresa"], $_SESSION["id_user"]);

	include("app/models/sendinblue/plantillas.php");
	include("app/models/sendinblue/SendEmblue.php");
	$ClassSendEmblue = new SendEmblue();

	foreach ($_POST["responsables"] as $responsable) {
		$queryEmpTmp = mysqli_query($connect_valentina, "SELECT * FROM Empleados
			WHERE id = '" . $responsable . "' ");
		$dataEmpTmp = mysqli_fetch_array($queryEmpTmp);

		$nombre = $dataEmpTmp["nombre"] . " " . $dataEmpTmp["apellidos"];
		$correo = $dataEmpTmp["correo"];
		$descripcion = $_POST["descripcion"];

		$plantilla = PlantillaResponsable($nombre, $descripcion);
		//$colaboradores =  $ClassSendEmblue->individual($nombre, "Responsable de iniciativa. - Go for Agile", $correo, $plantilla );
	}

	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_resultado"] . '&iniciativa=' . $iniciativa . '";</script>';
}

if ($_POST["duplicar_iniciativa"] != "") {

	$resultado_okr = $_POST["id_resultado"];

	if ($_POST["id_registro"] != "") {
		$iniciativa = DuplicarIniciativa($connect_okrs, $_POST, $hoy, $_SESSION["id_empresa"], $_SESSION["id_user"]);
	}

	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $resultado_okr . '&iniciativa=' . $iniciativa . '";</script>';
}

//PARA GUARDAR COMENTARIOS
if ($_POST["guardar_comentario"] != "") {

	GuardarComentario($_POST, $connect_okrs, $dtEmpleado["id"], $_SESSION["id_empresa"], $_SESSION["id_user"]);
	//NOTIFICACION A LOS PARTICIPANTES
	//NOTIFICACION A LOS PARTICIPANTES
	include("app/models/sendinblue/plantillas.php");
	include("app/models/sendinblue/SendEmblue.php");
	$ClassSendEmblue = new SendEmblue();

	$queryEquipo = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Equipos WHERE id_okrs = '" . $_POST["id_okr"] . "' ");
	while ($dataEquipo = mysqli_fetch_array($queryEquipo)) {

		$queryEmpTmp = mysqli_query($connect_valentina, "SELECT * FROM Empleados
			WHERE id = '" . $dataEquipo['id_empleado'] . "' ");
		$dataEmpTmp = mysqli_fetch_array($queryEmpTmp);
		$nombre = $dataEmpTmp["nombre"] . " " . $dataEmpTmp["apellidos"];
		$correo = $dataEmpTmp["correo"];
		$comentario = $_POST["comentario"];
		$plantilla = PlantillaComentario($nombre, $comentario);
		//$colaboradores =  $ClassSendEmblue->individual($nombre, "Nuevo Comentario OKRs. - Agile Maker", $correo, $plantilla );

	}

	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_resultado"] . '";</script>';
}

if ($_POST["editar_comentario"] != "") {

	EditarComentario($_POST, $connect_okrs, $_SESSION["id_empresa"], $_SESSION["id_user"]);

	// echo '<script> window.location.href = "?pg=okrs_equipos/home";</script>';
	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_resultado"] . '";</script>';
}

if ($_POST["guardar_comentario_iniciativa"] != "") {

	GuardarComentarioIniciativa($_POST, $connect_okrs, $dtEmpleado["id"], $_SESSION["id_empresa"], $_SESSION["id_user"]);
	//NOTIFICACION A LOS PARTICIPANTES
	//NOTIFICACION A LOS PARTICIPANTES
	include("app/models/sendinblue/plantillas.php");
	include("app/models/sendinblue/SendEmblue.php");
	$ClassSendEmblue = new SendEmblue();

	$queryEquipo = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Equipos WHERE id_okrs = '" . $_POST["id_okr"] . "' ");
	while ($dataEquipo = mysqli_fetch_array($queryEquipo)) {

		$queryEmpTmp = mysqli_query($connect_valentina, "SELECT * FROM Empleados
			WHERE id = '" . $dataEquipo['id_empleado'] . "' ");
		$dataEmpTmp = mysqli_fetch_array($queryEmpTmp);
		$nombre = $dataEmpTmp["nombre"] . " " . $dataEmpTmp["apellidos"];
		$correo = $dataEmpTmp["correo"];
		$comentario = $_POST["comentario"];
		$plantilla = PlantillaComentario($nombre, $comentario);
		//$colaboradores =  $ClassSendEmblue->individual($nombre, "Nuevo Comentario OKRs. - Agile Maker", $correo, $plantilla );

	}

	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_resultado"] . '";</script>';
}

if ($_POST["editar_comentario_iniciativa"] != "") {

	EditarComentarioIniciativa($_POST, $connect_okrs, $_SESSION["id_empresa"], $_SESSION["id_user"]);

	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_resultado"] . '";</script>';
}

if ($_POST["crear_resultado"] != "") {

	$id_okr = $_POST["id_okrs"];

	if ($_POST["id_okrs"] != "") {

		$resultado_okr = CrearResultado($connect_okrs, $_POST, $id_okr, $hoy, $_SESSION["id_empresa"], $_SESSION["id_user"]);
	}

	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $resultado_okr . '";</script>';
}

if ($_POST["guardar_avance_iniciativas"] != "") {
	$queryResultadosIni = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Iniciativas WHERE id_resultado = " . $_POST["id_registro"] . " ");
	while ($dataResultadosIni = mysqli_fetch_array($queryResultadosIni)) {
		$sentencia = "UPDATE Okrs_Iniciativas SET avance = '" . $_POST["id_iniciativa_" . $dataResultadosIni["id"] . ""] . "', updated_at = '$hoy' WHERE id = " . $dataResultadosIni["id"] . "";
		mysqli_query($connect_okrs, $sentencia);

		$query2 = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $dataResultadosIni["id_okrs"] . "");
		$data2 = mysqli_fetch_array($query2);

		$accion = 'ACTUALIZAR';
		$descripcion = 'Actualización de avance para la Iniciativa ' . $dataResultadosIni["descripcion"];

		$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $_SESSION["id_empresa"] . ", " . $_SESSION["id_user"] . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $dataResultadosIni["id_okrs"] . "," . $_POST["id_registro"] . "," . $dataResultadosIni["id"] . ",'$hoy')";
		// echo $auditoria;
		mysqli_query($connect_okrs, $auditoria);
	}
	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_registro"] . '";</script>';
}

if ($_POST["cargar_documento_iniciativa"] != "") {
	$archivo = "";

	if ($_FILES["archivo_ini"]["name"]) {

		include("app/controllers/subir_documento.php");
		$archivo = Subir_Documento($_FILES["archivo_ini"]);
	}
	$sentencia_doc = "
		INSERT INTO Okrs_Documentos ( id_empresa ,  id_okrs ,  id_resultado ,  id_iniciativa , id_empleado,  archivo , comentario,  created_at )
		VALUES
		( '" . $_SESSION["id_empresa"] . "', '" . $_POST["id_okr"] . "',  '" . $_POST["id_resultado"] . "', '" . $_POST["id_iniciativa"] . "', '" . $_SESSION["id_user"] . "', '" . $archivo . "', '" . $_POST["comentario"] . "', '" . $hoy . "' )
		";

	mysqli_query($connect_okrs, $sentencia_doc);
	$accion = 'CREAR';
	$descripcion = 'Cargue de documento para la iniciativa ' . $dataIniciativa["descripcion"];
	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $_SESSION["id_empresa"] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion'," . $dataOkr["tipo"] . "," . $dataOkr["id"] . "," . $dataIniciativa["id_resultado"] . ",$id_iniciativa,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);
	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_resultado"] . '";</script>';
}

if ($_POST["editar_documento_iniciativa"] != "") {
	$archivo = $adicional = "";
	// print_r($_POST["files"]);
	if ($_FILES["archivo_ini_edit"]["name"]) {

		include("app/controllers/subir_documento.php");
		$archivo = Subir_Documento($_FILES["archivo_ini_edit"]);
		$adicional = "archivo = '$archivo', ";
	}
	$sentencia_doc = "UPDATE Okrs_Documentos SET comentario = '" . $_POST["comentario_edit"] . "', $adicional updated_at = '$hoy' WHERE id = " . $_POST["id_registro_doc"] . "";
	// echo $sentencia_doc;
	mysqli_query($connect_okrs, $sentencia_doc);
	$accion = 'ACTUALIZAR';
	$descripcion = 'Actualización de documento para la iniciativa ' . $dataIniciativa["descripcion"];
	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $dataEmpleado["id_empresa"] . ", " . $dataEmpleado["id"] . ",'$accion','$descripcion'," . $dataOkr["tipo"] . "," . $dataOkr["id"] . "," . $dataIniciativa["id_resultado"] . ",$id_iniciativa,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);
	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_resultado"] . '";</script>';
}

if ($_POST["crear_plan_accion"] != "") {

	$empleado = "";
	if ($_POST["empleado_asignado"] == 1) {
		$empleado = $_POST["emp_interno_pa"];
	}

	if ($_POST["empleado_asignado"] == 2) {
		$empleado = $_POST["emp_ext_pa"];
	}

	if ($_SESSION["id_empresa"] == 1) {
		$responsables = implode(",", $_POST["responsables"]);
	} else {
		$responsables = "";
	}

	// print_r($_SESSION);

	$sentencia_pa = "INSERT INTO Okrs_Actividades (id_empresa, id_okrs, id_resultado, id_iniciativa, id_empleado, id_asignado, descripcion, prioridad, meta, progreso, estado_backlog, created_at, fecha_inicia, fecha_entrega, ciclo, aprobacion) VALUES
    (" . $_SESSION["id_empresa"] . "," . $_POST["id_okrs_pa"] . "," . $_POST["id_resultado_pa"] . "," . $_POST["id_registro_pa"] . "," . $_SESSION['id_user'] . ",'" . $responsables . "','" . $_POST["descripcion_pa"] . "'," . $_POST["prioridad_pa"] . ",'" . $_POST["meta_pa"] . "',0,1,'$hoy','" . $_POST["fecha_inicia_pa"] . "','" . $_POST["fecha_fin_pa"] . "','" . $_POST["ciclo_pa"] . "',1)";

	// echo "<br>$sentencia_pa";

	if (mysqli_query($connect_okrs, $sentencia_pa)) {
		$id_tmp = mysqli_insert_id($connect_okrs);
		if ($_SESSION["id_empresa"] != 1) {
			if (count($_POST["responsables"]) > 1) {
				foreach ($_POST["responsables"] as $id_resp) {
					if ($_SESSION["id_user"] == $id_resp) {

						$sentencia = " UPDATE Okrs_Actividades SET id_asignado = '" . $_SESSION["id_user"] . "' WHERE id = '" . $id_tmp . "'";
						mysqli_query($connect_okrs, $sentencia);
					} else {
						$aprobacion = 4;
						mysqli_query($connect_okrs, "INSERT INTO Notificacion_Plan_Accion (id_empresa, id_plan,id_empleado,id_asignado,estado,created_at)
			VALUES(" . $_SESSION["id_empresa"] . ",$id_tmp," . $_SESSION["id_user"] . "," . $id_resp . ",$aprobacion,'$hoy')");
					}
					// 	ECHO "INSERT INTO Notificacion_Plan_Accion (id_empresa, id_plan,id_empleado,id_asignado,estado,created_at)
					// VALUES(" . $_SESSION["id_empresa"] . ",$id," . $_SESSION["id_user"] . "," . $id_resp . ",4,'$hoy')";

				}
			} else {
				foreach ($_POST["responsables"] as $id_resp) {
					if ($_SESSION["id_user"] == $id_resp) {
						$sentencia = "UPDATE Okrs_Actividades SET id_asignado = '" . $_SESSION["id_user"] . "' WHERE id = '" . $id_tmp . "'";
						mysqli_query($connect_okrs, $sentencia);
					} else {
						mysqli_query($connect_okrs, "INSERT INTO Notificacion_Plan_Accion (id_empresa, id_plan,id_empleado,id_asignado,estado,created_at)
			VALUES(" . $_SESSION["id_empresa"] . ",$id_tmp," . $_SESSION["id_user"] . "," . $id_resp . ",4,'$hoy')");
					}
				}
				// ECHO "INSERT INTO Notificacion_Plan_Accion (id_empresa, id_plan,id_empleado,id_asignado,estado,created_at)
				// VALUES(" . $_SESSION["id_empresa"] . ",$id," . $_SESSION["id_user"] . "," . $_POST["responsables"][0] . ",4,'$hoy')";

			}
		}
	}
	include("app/controllers/subir_documento.php");
	if (count($_FILES['archivos']['name']) > 0) {
		$uploadSuccess = true;
		$maxFileSize = 10 * 1024 * 1024;
		foreach ($_FILES['archivos']['name'] as $key => $fileName) {
			$fileTmpName = $_FILES['archivos']['tmp_name'][$key];
			$fileSize = $_FILES['archivos']['size'][$key];

			if ($fileSize > $maxFileSize) {
				$uploadSuccess = false;
				break;
			}

			if ($uploadSuccess) {
				// print_r($_FILES['archivos'][$key]);
				$file = array(
					'name' => $_FILES['archivos']['name'][$key],
					'type' => $_FILES['archivos']['type'][$key],
					'tmp_name' => $_FILES['archivos']['tmp_name'][$key],
					'error' => $_FILES['archivos']['error'][$key],
					'size' => $_FILES['archivos']['size'][$key]
				);
				$archivo = Subir_Documento($file);

				$sentencia_doc = "
		INSERT INTO Documentos_Plan_Accion ( id_empresa , id_empleado, id_plan, archivo , comentario,  created_at )
		VALUES
		( '" . $_SESSION["id_empresa"] . "', '" . $_SESSION["id_user"] . "', '" . $id_tmp . "', '" . $archivo . "', '" . $_POST["comentario"] . "', '" . $hoy . "' )
		";
				mysqli_query($connect_okrs, $sentencia_doc);
			}
		}
	}

	if ($_POST["comentario"] != "") {
		$sentencia = "INSERT INTO Comentarios_Plan_Accion (id_empresa, id_empleado, id_plan, comentario, created_at)
    VALUES (" . $_SESSION["id_empresa"] . "," . $_SESSION["id_user"] . ",'" . $id_tmp . "','" . $_POST["comentario"] . "','$hoy')";
		mysqli_query($connect_okrs, $sentencia);
	}

	$accion = 'CREACIÓN';
	$descripcion = 'Creación de plan de acción ' . $_POST["descripcion_pa"] . ' para la iniciativa ' . $dataIniciativa["descripcion"];
	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $_SESSION["id_empresa"] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion'," . $dataOkr["tipo"] . "," . $dataOkr["id"] . "," . $dataIniciativa["id_resultado"] . ",$id_iniciativa,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);
	$queryPA = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Actividades WHERE id = '$id_tmp' ");
	$dataPA = mysqli_fetch_array($queryPA);
	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $dataPA["id_resultado"] . '&iniciativa=' . $dataPA["id_iniciativa"] . '&pa_iniciativa=' . $dataPA["id_iniciativa"] . '";</script>';
}

if ($_POST["editar_plan_accion"] != "") {

	$empleado = "";
	if ($_POST["empleado_asignado_pa_edit"] == 1) {
		$empleado = $_POST["emp_interno_pa_edit"];
	} else if ($_POST["empleado_asignado_pa_edit"] == 2) {
		$empleado = $_POST["emp_ext_pa_edit"];
	} else {
		$empleado = $_POST["id_asignado_pa_edit"];
	}

	if ($_SESSION["id_empresa"] == 1) {
		$responsables = implode(",", $_POST["responsables"]);
	} else {
		$responsables = "";
		$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Actividades WHERE id = '" . $_POST["id_registro_pa_edit"] . "' ");
		$data = mysqli_fetch_array($query);

		$baseDeDatos = $data["id_asignado"];
		$formulario = $_POST["responsables"];

		$arrayBaseDatos = explode(',', $baseDeDatos);

		$arrayFormulario = $_POST["responsables"];

		if (in_array($_SESSION["id_user"], $arrayFormulario)) {
			$arrayBaseDatos[] = $_SESSION["id_user"];
		}

		$eliminar = array_diff($arrayBaseDatos, $arrayFormulario);

		$agregar = array_diff($arrayFormulario, $arrayBaseDatos);

		foreach ($agregar as $numAgregar) {
			mysqli_query($connect_okrs, "INSERT INTO Notificacion_Plan_Accion (id_empresa, id_plan,id_empleado,id_asignado,estado,created_at)
			VALUES(" . $_SESSION["id_empresa"] . "," . $_POST["id_registro_pa_edit"] . "," . $_SESSION["id_user"] . "," . $numAgregar . ",4,'$hoy')");
		}

		$baseDeDatosFinal = array_intersect($arrayBaseDatos, $arrayFormulario);

		$responsables = implode(',', $baseDeDatosFinal);
	}

	if ($_POST["tipo_update_pa_edit"] == 1) {
		$sentencia_update = "UPDATE Okrs_Actividades SET id_asignado = '$responsables', descripcion = '" . $_POST["descripcion_pa_edit"] . "', prioridad = " . $_POST["prioridad_pa_edit"] . ", meta = '" . $_POST["meta"] . "',
        progreso = " . $_POST["progreso"] . ", estado_backlog = " . $_POST["estado_backlog_pa_edit"] . ", updated_at = '$hoy',
        fecha_inicia = '" . $_POST["fecha_inicia_pa_edit"] . "', fecha_entrega = '" . $_POST["fecha_fin_pa_edit"] . "',
        ciclo = '" . $_POST["ciclo_pa_edit"] . "' WHERE id = " . $_POST["id_registro_pa_edit"] . "";
	} else {
		$sentencia_update = "UPDATE Okrs_Actividades SET prioridad = " . $_POST["prioridad_pa_edit"] . ", progreso = " . $_POST["progreso_pa_edit"] . ", estado_backlog = " . $_POST["estado_backlog_pa_edit"] . ", updated_at = '$hoy' WHERE id = " . $_POST["id_registro_pa_edit"] . "";
	}
	// echo $sentencia_update;
	//
	mysqli_query($connect_okrs, $sentencia_update);
	$accion = 'ACTUALIZAR';
	$descripcion = 'Actualización de plan de acción ' . $_POST["descripcion_pa"] . ' para la iniciativa ' . $dataIniciativa["descripcion"];
	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $dataEmpleado["id_empresa"] . ", " . $dataEmpleado["id"] . ",'$accion','$descripcion'," . $dataOkr["tipo"] . "," . $dataOkr["id"] . "," . $dataIniciativa["id_resultado"] . ",$id_iniciativa,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);
	$queryPA = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Actividades WHERE id = '" . $_POST["id_registro_pa_edit"] . "' ");
	$dataPA = mysqli_fetch_array($queryPA);
	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $dataPA["id_resultado"] . '&iniciativa=' . $dataPA["id_iniciativa"] . '&pa_iniciativa=' . $dataPA["id_iniciativa"] . '";</script>';
}

if ($_POST["guardar_comentario_plan"] != "") {
	$sentencia = "INSERT INTO Comentarios_Plan_Accion (id_empresa, id_empleado, id_plan, comentario, created_at)
    VALUES (" . $_SESSION["id_empresa"] . "," . $_SESSION["id_user"] . ",'" . $_POST["id_plan"] . "','" . $_POST["comentario"] . "','$hoy')";
	mysqli_query($connect_okrs, $sentencia);

	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_resultado"] . '";</script>';
}

if ($_POST["editar_comentario_plan"] != "") {
	$sentencia = "UPDATE Comentarios_Plan_Accion SET comentario = '" . $_POST["comentario_upd"] . "',updated_at = '$hoy' WHERE id = " . $_POST["id_comentario"] . "";
	mysqli_query($connect_okrs, $sentencia);

	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_resultado"] . '";</script>';
}

if ($_POST["guardar_documento_plan"] != "") {
	$archivo = "";

	if ($_FILES["archivo_plan"]["name"]) {
		// echo 'entro';
		include("app/controllers/subir_documento.php");
		$archivo = Subir_Documento($_FILES["archivo_plan"]);
	}
	$sentencia_doc = "
		INSERT INTO Documentos_Plan_Accion ( id_empresa , id_empleado, id_plan, archivo , comentario,  created_at )
		VALUES
		( '" . $_SESSION["id_empresa"] . "', '" . $_SESSION["id_user"] . "', '" . $_POST["id_plan"] . "', '" . $archivo . "', '" . $_POST["comentario_plan"] . "', '" . $hoy . "' )
		";
	// echo $sentencia_doc;
	mysqli_query($connect_okrs, $sentencia_doc);

	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_resultado"] . '";</script>';
	// echo '<script>
	// 		toastr.success("Documento cargado con éxito", "¡Éxito!");
	// 		$("#planesAccion_'.$_POST["id_iniciativa"].'").DataTable().ajax.reload();
	// 		$("#modal_general").modal("hide");
	// 	</script>';
}

if ($_POST["editar_documento_plan"] != "") {
	$archivo = "";

	if ($_FILES["archivo_plan_upd"]["name"]) {
		// echo 'entro';
		include("app/controllers/subir_documento.php");
		$archivo = Subir_Documento($_FILES["archivo_plan_upd"]);
	}
	$sentencia_doc = "UPDATE Documentos_Plan_Accion SET archivo = '" . $archivo . "', comentario = '" . $_POST["comentario_plan_upd"] . "', updated_at = '" . $hoy . "' WHERE id = " . $_POST["id_documento"] . "";
	// echo $sentencia_doc;
	mysqli_query($connect_okrs, $sentencia_doc);

	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_resultado"] . '";</script>';
}

if ($_POST["duplicar_plan_accion"] != "") {

	// print_r($_POST);
	if ($_POST["id_registro"] != "") {
		$planAccion = DuplicarPlanAccion($connect_okrs, $_POST, $hoy, $_SESSION["id_empresa"], $_SESSION["id_user"]);
	}

	echo '<script> window.location.href = "?pg=okrs_equipos/home_organizacion&page=' . $page . '&resultado_okr_=' . $_POST["id_resultado"] . '&iniciativa=' . $_POST["id_iniciativa"] . '";</script>';
}

//TODOS LOS OKRS DE ESTE USUARIO
//TODOS LOS OKRS DE ESTE USUARIO
$ARRAY_OKRS_FILTRO_USUARIO = OkrsFiltro(0, $dtEmpleado['id_empresa'], $connect_okrs, $inicio, $registros_por_pagina);

//VALIDAMOS LOS FILTROS
if ($_POST["equipo_fill"] > 0) {
	$_SESSION["equipo_fill"] = $_POST["equipo_fill"];
}
if ($_POST["equipo_fill"] == -1) {
	$_SESSION["equipo_fill"] = "";
}

// echo "equipo ".$_POST["equipo_fill"];

// if($_POST["periodo_fill"] != ""){ $_SESSION["periodo_fill"] = $_POST["periodo_fill"]; }
// if($_POST["periodo_fill"] == -1){ $_SESSION["periodo_fill"] = ""; }

// $_SESSION["Q1"] = "";
// $_SESSION["Q2"] = "";
// $_SESSION["Q3"] = "";
// $_SESSION["Q4"] = "";
// $_SESSION["Anual"] = "";

// print_r($_SESSION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['periodo'])) {
		$_SESSION['selected_periodo_okr'] = $_POST['periodo'];  // Guardar checkboxes seleccionados en la sesión
	} else {
		// Si no se seleccionan checkboxes, eliminar la sesión para checkboxes
		unset($_SESSION['selected_periodo_okr']);
	}
}

$filtro_periodo = "";

$contFiltro = 0;
$contQ = 0;
$filtroPonderado = "";

if (isset($_SESSION['selected_periodo_okr'])) {

	$quotedOptions = array_map(function ($value) {
		return "'$value'";
	}, $_SESSION['selected_periodo_okr']);

	$contFiltro = count($_SESSION['selected_periodo_okr']);
	$filtro_periodo = implode(', ', $quotedOptions);
	$filtroPonderado = implode(', ', $quotedOptions);
}

// $_SESSION["selected_periodo_okr"] = $filtro_periodo != "" ? $_POST['periodo'] : "";
// $_SESSION["selected_periodo_okr"] = $filtro_periodo != "" ? $filtro_periodo : "";
$_SESSION["periodo_fill"] = $filtro_periodo != "" ? $filtro_periodo : "";

if ($_POST["anio_fill"] != "") {
	$_SESSION["anio_fill"] = $_POST["anio_fill"];
}
if ($_POST["anio_fill"] == -1) {
	$_SESSION["anio_fill"] = "";
}

if ($_POST["objestrategico_fill"] != "") {
	$_SESSION["objestrategico_fill"] = $_POST["objestrategico_fill"];
}
if ($_POST["objestrategico_fill"] == -1) {
	$_SESSION["objestrategico_fill"] = "";
}

if ($_POST["vicepresidencia_fill_okr"] != "") {
	$_SESSION["vicepresidencia_fill_okr"] = $_POST["vicepresidencia_fill_okr"];
}
if ($_POST["vicepresidencia_fill_okr"] == -1) {
	$_SESSION["vicepresidencia_fill_okr"] = "";
}
if ($_GET["id_vp"] != "") {
	$_SESSION["vicepresidencia_fill_okr"] = $_GET["id_vp"];
}


if ($_POST["colaborador_fill_okr"] != "") {
	$_SESSION["colaborador_fill_okr"] = $_POST["colaborador_fill_okr"];
}
if ($_POST["colaborador_fill_okr"] == -1) {
	$_SESSION["colaborador_fill_okr"] = "";
}

if ($_POST["areas_fill_okr"] != "") {
	$_SESSION["areas_fill_okr"] = $_POST["areas_fill_okr"];
}
if ($_POST["areas_fill_okr"] == -1) {
	$_SESSION["areas_fill_okr"] = "";
}

if ($_GET["id_area"] != "") {
	$_SESSION["areas_fill_okr"] = $_GET["id_area"];
}

if ($_POST["tipo_fill_okr"] != "") {
	$_SESSION["tipo_fill_okr"] = $_POST["tipo_fill_okr"];
	$_POST["tipo_fill_okr"] = $_SESSION["tipo_fill_okr"];
}
if ($_POST["tipo_fill_okr"] == -1) {
	$_SESSION["tipo_fill_okr"] = "";
}

if ($_GET["id_okr_reporte"] != "") {
	$_SESSION["equipo_fill"] = $_GET["id_okr_reporte"];
}

// if ($_POST["buscador_resultado"] != "") {
// 	$_SESSION["buscador_resultado"] = $_POST["buscador_resultado"];
// }
// if ($_POST["buscador_resultado"] == "") {
// 	$_SESSION["buscador_resultado"] = "";
// }

//TODOS LOS OKRS DE ESTE USUARIO CON FILTROS APLICADOS
//TODOS LOS OKRS DE ESTE USUARIO CON FILTROS APLICADOS
$ARRAY_OKRS_USUARIO = OkrsUsuario(0, $dtEmpleado['id_empresa'], $connect_okrs, $filtro_periodo);
$ARRAY_OKRS_USUARIO_PAGINACION = OkrsUsuarioPaginacion(0, $dtEmpleado['id_empresa'], $connect_okrs, $inicio, $registros_por_pagina, $filtro_periodo);
foreach ($ARRAY_OKRS_USUARIO_PAGINACION as $value) {
	$total_registros = $value['registros'];
}

$total_paginas = ceil($total_registros / $registros_por_pagina);
//PARA GENERAR LOS FILTROS
//PARA GENERAR LOS FILTROS
//PARA GENERAR LOS FILTROS
//PARA GENERAR LOS FILTROS
$ARRAY_COLABORADORES_FILTRO = array();
$OR_FILL = "";

$queryFiltros = mysqli_query($connect_okrs, "SELECT * FROM Equipos_Views WHERE id_empresa = '" . $dtEmpleado['id_empresa'] . "' AND id_empleado = '" . $dtEmpleado["id"] . "'  ");
while ($dataFiltros = mysqli_fetch_array($queryFiltros)) {
	if ($OR_FILL == "") {
		$OR_FILL = " OE.id_okrs = '" . $dataFiltros["id_okrs"] . "' ";
	} else {
		$OR_FILL .= " OR OE.id_okrs = '" . $dataFiltros["id_okrs"] . "' ";
	}
}

$queryColFiltro = mysqli_query($connect_okrs, "SELECT OE.id_empleado, E.nombre FROM Okrs_Equipos OE
	INNER JOIN goforagile_admin.Empleados E ON E.id = OE.id_empleado WHERE OE.id_empresa = '" . $dtEmpleado['id_empresa'] . "' AND  (" . $OR_FILL . ") GROUP BY OE.id_empleado ORDER BY E.nombre");

while ($dataColFiltro = mysqli_fetch_array($queryColFiltro)) {
	$queryEmple = mysqli_query($connect_valentina, "SELECT * FROM Empleados
			WHERE id = '" . $dataColFiltro['id_empleado'] . "' ORDER BY nombre");
	$dataEmple = mysqli_fetch_array($queryEmple);

	$obj_c = array(
		"id" => $dataEmple["id"],
		"nombre" => $dataEmple["nombre"],
	);
	array_push($ARRAY_COLABORADORES_FILTRO, $obj_c);
}


?>

<link rel="stylesheet" href="<?php echo $url; ?>css/okrs_visual.min.css">

<?php include("views/okrs/layouts/modal_crear_okr.php");
include("views/okrs/layouts/modal_okr.php");
include("views/okrs/layouts/modal_profile.php");
include("views/okrs_equipos/componentes/modal_tipo_rol.php");


$querySM87 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 8 AND id_submenu = 51");
$dataSM87 = mysqli_fetch_array($querySM87);

include("views/okrs_equipos/etiquetas.php");
$Array_Tipo_OKR1 = array(
	array("1", $etiquetaOkrOO),
	array("2", $etiquetaOkrOE),
);
// include("views/okrs_equipos/planes_accion/modal_plan.php");
include("views/okrs_equipos/planes_accion/modal_planes_accion.php");
?>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col-md-12">
						<h4><i class="fas fa-bullseye" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM87["nombre"]; ?></h4>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br>

<div class="container-fluid" id="area_trabajo">
	<?php
	if ($_GET["id_okr_reporte"] != "") {
	?>
		<br>
		<div class="row">
			<div class="col-md-12" style="text-align: end;">
				<a href="<?php echo $url ?>?pg=okrs_equipos/reportes/consolidados" class="btn btn-primary" id="btnAccion">Volver a Consolidado General</a>
			</div>
		</div>
	<?php
	}

	?>
	<div class="alert alert-info" role="alert" align="center" style="margin-top: 1rem;">
		<i class="fa fa-info-circle"></i>
		Aquí podrá gestionar los OKRs en los que está participando cómo administrador, lider o colaborador de un equipo de trabajo. recuerde que puede utilizar los filtros para acceder de forma rápida a la información requerida.
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<?php include("views/okrs_equipos/componentes/filtros.php"); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="row">

		<div class="col-md-12" style="margin-top: 20px">

			<?php
			$rutaPage = "home_organizacion";
			$avance_general_equipo = 0;
			$count_avance_general_equipo = 0;
			$tipo_modulo = 2;
			foreach ($ARRAY_OKRS_USUARIO_PAGINACION as $OKRS) {

				if ($OKRS["tipo"] == 1) {

					$queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id =   '" . $OKRS["id_empleado"] . "' ");
					$dataEmpleado = mysqli_fetch_array($queryEmpleado);

					$clas_okrs = "corporativos";
					$tipo_okrs = "Organizacional";
					include("views/okrs_equipos/componentes/ficha_okrs_new.php");
				}
			}
			?>

			<?php
			foreach ($ARRAY_OKRS_USUARIO_PAGINACION as $OKRS) {
				if ($OKRS["tipo"] == 2) {

					$queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id =   '" . $OKRS["id_empleado"] . "' ");
					$dataEmpleado = mysqli_fetch_array($queryEmpleado);

					$clas_okrs = "equipos";
					$tipo_okrs = "Equipo";
					include("views/okrs_equipos/componentes/ficha_okrs_new.php");
				}
			}
			?>

			<?php
			if (count($ARRAY_OKRS_USUARIO_PAGINACION) == 0) {
				echo '
				<div class="alert alert-primary" role="alert">
					No tiene OKRs relacionados o creados.
				</div>
				';
			}
			?>

		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<?php
			echo '<nav aria-label="Page navigation">';
			echo '<ul class="pagination">';

			// Enlace a la página anterior
			if ($page > 1) {
				echo '<li class="page-item"><a class="page-link" href="?pg=okrs_equipos/home_organizacion&page=' . ($page - 1) . '">Anterior</a></li>';
			}

			// Enlaces numéricos
			for ($i = 1; $i <= $total_paginas; $i++) {
				$activo = ($i == $page) ? 'active' : '';
				echo '<li class="page-item ' . $activo . '"><a class="page-link" href="?pg=okrs_equipos/home_organizacion&page=' . $i . '">' . $i . '</a></li>';
			}

			// Enlace a la página siguiente
			if ($page < $total_paginas) {
				echo '<li class="page-item"><a class="page-link" href="?pg=okrs_equipos/home_organizacion&page=' . ($page + 1) . '">Siguiente</a></li>';
			}

			echo '</ul>';
			echo '</nav>';
			?>
		</div>
	</div>
</div>
<?php
$viewPage = 'home_organizacion';
?>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var divElement = document.getElementById("resultado_okr_<?php echo $_GET["resultado_okr_"]; ?>");
        var divIniciativa = document.getElementById("iniciativa_kr_<?php echo $_GET["resultado_okr_"]; ?>");
        var planAccion = '<?php echo $_GET["pa_iniciativa"]; ?>';


        if (divElement) {
            divElement.scrollIntoView();
            $("#boton_accordion_kr_<?php echo $_GET["resultado_okr_"]; ?>").removeClass("collapse");
            $("#collapse_kr_<?php echo $_GET["resultado_okr_"]; ?>").addClass("show");
            if (divIniciativa) {
                divIniciativa.scrollIntoView();
                console.log(document.getElementById("planA_Iniciativa_<?php echo $_GET["pa_iniciativa"]; ?>"));
                if (planAccion != "") {
                    setTimeout(function() {
                        const enlace = document.getElementById("planA_Iniciativa_<?php echo $_GET["pa_iniciativa"]; ?>");

                        if (enlace) {
                            enlace.click();
                        }
                    }, 550);
                }
            }
        }
    });
    window.location.hash = "";
</script>
<?php include("views/okrs_equipos/script_okrs.php"); ?>


<?php
$queryEscala = mysqli_query($connect_valentina, "SELECT * FROM Escala_Medicion WHERE id_empresa = " . $_SESSION["id_empresa"] . "");
$dataEscala = mysqli_fetch_array($queryEscala);
$avance_general_equipo_1 = $count_avance_general_equipo_1 = 0;
$contOkrOrganizacion = count($ARRAY_OKRS_USUARIO);
$sumPromedio1 = $sumPromedio2 = $sumPromedio3 = $sumPromedio4 = $sumPromedio5 = 0;
foreach ($ARRAY_OKRS_USUARIO as $OKRS) {

	switch ($tipo_modulo) {
		case 1:
			$resultados = PorOkrsIndividual($connect_valentina, $connect_okrs, $OKRS["id"], $dtEmpleado['id_empresa']);
			$visualizacion_kr = $_SESSION["id_user"];
			break;
		case 2:
			$resultados = PorOkrs($connect_valentina, $connect_okrs, $OKRS["id"], $dtEmpleado['id_empresa']);
			$visualizacion_kr = $_SESSION["colaborador_fill_okr"];
			break;
	}

	$a_por = round($resultados["promedio"]);


	$escala = EscalaColor($a_por, $dtEmpleado['id_empresa'], $connect_valentina);

	$color_bg = $escala['color_bg'];
	// $color_bg = "#FF0000";

	if (is_nan($a_por) || is_infinite($a_por)) {
		$a_por = 0;
	}

	if ($a_por == 0) {
		$sumPromedio1++;
	} else {
		if ($a_por >= $dataEscala['porcentaje_uno'] && $a_por < $dataEscala['porcentaje_tres']) {
			$sumPromedio1++;
		}
		if ($a_por >= $dataEscala['porcentaje_tres'] && $a_por < $dataEscala['porcentaje_cinco']) {
			$sumPromedio2++;
		}
		if ($a_por >= $dataEscala['porcentaje_cinco'] && $a_por < $dataEscala['porcentaje_siete']) {
			$sumPromedio3++;
		}
		if ($a_por >= $dataEscala['porcentaje_siete'] && $a_por <= 100) {
			$sumPromedio4++;
		}
		if ($a_por > 100) {
			$sumPromedio5++;
		}
	}

	$avance_general_equipo_1 += $a_por;

	$count_avance_general_equipo_1++;
}

$porcentaje_final = round(($avance_general_equipo_1 / $count_avance_general_equipo_1), 2);
// $porcentaje_final = round(($avance_general_equipo / $count_avance_general_equipo), 2);
if (is_nan($porcentaje_final) || is_infinite($porcentaje_final)) {
	$porcentaje_final = 0;
}
if ($porcentaje_final > 100) {
	$porcentaje_barra = 100;
} else {
	$porcentaje_barra = $porcentaje_final;
}

$promedioOkr1 = round((($sumPromedio1 / $contOkrOrganizacion) * 100), 2);
if (is_nan($promedioOkr1) || is_infinite($promedioOkr1)) {
	$promedioOkr1 = 0;
}
if ($promedioOkr1 > 100) {
	$promedioOkr1 = 100;
}

$promedioOkr2 = round((($sumPromedio2 / $contOkrOrganizacion) * 100), 2);
if (is_nan($promedioOkr2) || is_infinite($promedioOkr2)) {
	$promedioOkr2 = 0;
}
if ($promedioOkr2 > 100) {
	$promedioOkr2 = 100;
}

$promedioOkr3 = round((($sumPromedio3 / $contOkrOrganizacion) * 100), 2);
if (is_nan($promedioOkr3) || is_infinite($promedioOkr3)) {
	$promedioOkr3 = 0;
}
if ($promedioOkr3 > 100) {
	$promedioOkr3 = 100;
}

$promedioOkr4 = round((($sumPromedio4 / $contOkrOrganizacion) * 100), 2);
if (is_nan($promedioOkr4) || is_infinite($promedioOkr4)) {
	$promedioOkr4 = 0;
}
if ($promedioOkr4 > 100) {
	$promedioOkr4 = 100;
}

$promedioOkr5 = round((($sumPromedio5 / $contOkrOrganizacion) * 100), 2);
if (is_nan($promedioOkr5) || is_infinite($promedioOkr5)) {
	$promedioOkr5 = 0;
}
if ($promedioOkr5 > 100) {
	$promedioOkr5 = 100;
}
?>
<script>
	$(document).ready(function() {
		$("#avance_equipo").html(<?php echo $porcentaje_final; ?>);
		ValidarFiltrosSecundarios();
		//$('#fecha_entrega').attr('min', moment().format('YYYY-MM-DD'));
		//$('#fecha_inicia').attr('min', moment().format('YYYY-MM-DD'));
	});
</script>
<?php
$escala_home = EscalaColor(round($porcentaje_final), $dtEmpleado['id_empresa'], $connect_valentina);
$back_color = "background-color:" . $escala_home['color_bg'] . " !important";

$tituloRes1 = $dataEscala['titulo_uno'];
$tituloRes2 = $dataEscala['titulo_tres'];
$tituloRes3 = $dataEscala['titulo_cuatro'];
$tituloRes4 = $dataEscala['titulo_cinco'];
$tituloRes5 = $dataEscala['titulo_seis'];

?>

<script>
	$(document).ready(function() {
		$("#progreso_desempenio").html('<div class="progresos" data-bs-toggle="tooltip" align="center">' +
			'<h1 style="font-size: 3.5rem;color: black !important;"><?php echo round($porcentaje_final, 2); ?> %</h1></div>' +
			'<div class="progress-bar bg-success" role="progressbar" style=" width: <?php echo round($porcentaje_barra, 2); ?>%; <?php echo $back_color; ?>;opacity: 0.3;z-index: 2;margin-top: -70px;height: 70px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">' +
			'</div><br>'
		);
		$("#cardProm1").html('<div class="inner"><h3 style="color: white !important;"><?php echo $promedioOkr1; ?>%</h3>' +
			'<p style="color: white !important;"><?php echo $tituloRes1 . " (" . $sumPromedio1 . " OKRs)"; ?></p></div>');
		$("#cardProm2").html('<div class="inner"><h3><?php echo $promedioOkr2; ?>%</h3>' +
			'<p><?php echo $tituloRes2 . " (" . $sumPromedio2 . " OKRs)"; ?></p></div>');
		$("#cardProm3").html('<div class="inner"><h3><?php echo $promedioOkr3; ?>%</h3>' +
			'<p><?php echo $tituloRes3 . " (" . $sumPromedio3 . " OKRs)"; ?></p></div>');
		$("#cardProm4").html('<div class="inner"><h3><?php echo $promedioOkr4; ?>%</h3>' +
			'<p><?php echo $tituloRes4 . " (" . $sumPromedio4 . " OKRs)"; ?></p></div>');
		$("#cardProm5").html('<div class="inner"><h3><?php echo $promedioOkr5; ?>%</h3>' +
			'<p><?php echo $tituloRes5 . " (" . $sumPromedio5 . " OKRs)"; ?></p></div>');
	});
</script>