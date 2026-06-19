<script>
	$(".menu_section").addClass("active");
	jQuery("#menu_okrs").css("display", "none");
	$("#bt_okrs_equipo").addClass("current-page");
</script>
<link rel="stylesheet" href="<?php echo $url; ?>css/okrs_visual.min.css">

<?php include("views/okrs/layouts/modal_crear_okr.php"); ?>
<?php include("views/okrs/layouts/modal_okr.php"); ?>
<?php include("views/okrs/layouts/modal_profile.php"); ?>


<script>
	$(".menu_section").addClass("active");
	$("#nav_reportes").addClass("active");
	jQuery("#menu_reportes").css("display", "none");
	$("#bt_okrs_consolidados_vp").addClass("current-page");
</script>
<link rel="stylesheet" href="<?php echo $url; ?>views/okrs_equipos/reportes/styles.css">
<style>
	.progreso-bar {
		width: 150px !important;
		height: 150px !important;
	}

	.objetivo-okr {
		font-size: 2rem !important;
	}

	#profileOkr:before {
		content: none !important;
	}

	.nav-tabs .nav-item.show .nav-link,
	.nav-tabs .nav-link.active {
		background-color: #007BFF !important;
		color: white !important;
	}

	#btnEditOkr:before,
	#EditarResultado:before {
		content: none !important;
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

    .bt_editar_pa{
    color: #000  !important;
    background-color: #f8f9fa  !important;
    border-color: #f8f9fa  !important;
    }
    .bt_editar_pa:hover{
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
<?php
global $connect_valentina;
global $connect_okrs;

include("views/okrs_equipos/functions.php");
include("views/okrs/layouts/modal_profile.php");

$hoy = date("Y-m-d H:i:s");
if ($_POST["equipo_fill"] > 0) {
	$_SESSION["equipo_fill"] = $_POST["equipo_fill"];
}
if ($_POST["equipo_fill"] == -1) {
	$_SESSION["equipo_fill"] = "";
}

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

if ($_POST["crear_resultado"] != "") {

	$id_okr = $_POST["id_okrs"];

	if ($_POST["id_okrs"] != "") {

		$resultado_okr = CrearResultado($connect_okrs, $_POST, $id_okr, $hoy, $_SESSION["id_empresa"], $_SESSION["id_user"]);
	}

	echo '<script> window.location.href = "?pg=okrs_equipos/home_equipo&okr_=' . $id_okr . '";</script>';
}

if ($_POST["guardar_avance_resultados"] != "") {

	$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = " . $_POST["id_registro"] . " ");
	while ($dataResultados = mysqli_fetch_array($queryResultados)) {

		$sentencia = "UPDATE Okrs_Resultados SET avance = '" . $_POST["id_resultado_" . $dataResultados["id"] . ""] . "', updated_at = '$hoy' WHERE id = " . $dataResultados["id"] . "";
		mysqli_query($connect_okrs, $sentencia);

		$query2 = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $dataResultados["id_okrs"] . "");
		$data2 = mysqli_fetch_array($query2);

		$accion = 'ACTUALIZAR';
		$descripcion = 'Actualización de avance para el resultado ' . $dataResultados["descripcion"];

		$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $_SESSION["id_empresa"] . ", " . $_SESSION["id_user"] . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $_POST["id_registro"] . "," . $dataResultados["id"] . ",0,'$hoy')";
		// echo $auditoria;
		mysqli_query($connect_okrs, $auditoria);
	}
	echo '<script> window.location.href = "?pg=okrs_equipos/home_equipo&okr_=' . $_POST["id_registro"] . '";</script>';
}

if ($_POST["guardar_avance_iniciativas"] != "") {
	$queryResultadosIni = mysqli_query($connect_okrs, "SELECT OI.*
		FROM Okrs_Iniciativas OI
		INNER JOIN Okrs_Resultados ORE ON ORE.id = OI.id_resultado
		INNER JOIN Okrs O ON O.id = ORE.id_okrs
		INNER JOIN Okrs_Equipos OE ON OE.id_okrs = ORE.id_okrs
		INNER JOIN Okrs_Areas OA ON OA.id_okrs = OE.id_okrs
		WHERE OE.id_empresa = '" . $_SESSION["id_empresa"] . "' AND O.anio = '" . $_SESSION["anio_fill"] . "' AND OA.id_area = " . $_POST["id_registro"] . "
		GROUP BY OI.id ORDER BY OI.descripcion ASC");
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
	echo '<script> window.location.href = "?pg=okrs_equipos/home_equipo&iniciativa_=' . $_SESSION["id_user"] . '";</script>';
}

if ($_POST["mover_resultado"] != "") {

	$id_okr = $_POST["id_okrs"];
	if ($_POST["id_registro"] != "") {
		$id_okr = MoverResultado($connect_okrs, $_POST["okr_fill"], $_POST["id_registro"], $_SESSION["id_empresa"], $_SESSION["id_user"]);
	}

	// echo '<script> window.location.href = "";</script>';
	echo '<script> window.location.href = "?pg=okrs_equipos/home_equipo&okr_=' . $id_okr . '";</script>';
}

if ($_POST["reubicar_resultado"] != "") {

	$id_okr = $_POST["id_registro"];
	if ($_POST["id_registro"] != "") {
		ReubicarResultado($connect_okrs, $_POST, $id_okr, $hoy, $_SESSION["id_empresa"], $_SESSION["id_user"]);
	}

	// echo '<script> window.location.href = "";</script>';
	echo '<script> window.location.href = "?pg=okrs_equipos/home_equipo&okr_=' . $id_okr . '";</script>';
}

if ($_POST["duplicar_resultado"] != "") {

	$id_okr = $_POST["id_okrs"];
	if ($_POST["id_registro"] != "") {
		$resultado = DuplicarResultado($connect_okrs, $_POST, $id_okr, $hoy, $_SESSION["id_empresa"], $_SESSION["id_user"]);
	}

	// echo '<script> window.location.href = "";</script>';
	echo '<script> window.location.href = "?pg=okrs_equipos/home_equipo&okr_=' . $id_okr . '";</script>';
}

if ($_POST["guardar_resultado"] != "") {
	// print_r($_POST);
	GuardarResultado($_POST, $connect_okrs, $dtEmpleado["role"], $_SESSION["id_empresa"], $_SESSION["id_user"]);
	echo '<script> window.location.href = "?pg=okrs_equipos/home_equipo&okr_=' . $_POST["id_okrs"] . '";</script>';
}

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

	echo '<script> window.location.href = "?pg=okrs_equipos/home_equipos&page=' . $page . '&resultado_okr_=' . $_POST["id_resultado"] . '&iniciativa=' . $iniciativa . '";</script>';
}

if ($_POST["duplicar_iniciativa"] != "") {

	$resultado_okr = $_POST["id_resultado"];

	if ($_POST["id_registro"] != "") {
		$iniciativa = DuplicarIniciativa($connect_okrs, $_POST, $hoy, $_SESSION["id_empresa"], $_SESSION["id_user"]);
	}

	echo '<script> window.location.href = "?pg=okrs_equipos/home_equipos&page=' . $page . '&resultado_okr_=' . $resultado_okr . '&iniciativa=' . $iniciativa . '";</script>';
}

if ($_POST["duplicar_plan_accion"] != "") {

	// print_r($_POST);
	if ($_POST["id_registro"] != "") {
		$planAccion = DuplicarPlanAccion($connect_okrs, $_POST, $hoy, $_SESSION["id_empresa"], $_SESSION["id_user"]);
	}

	echo '<script> window.location.href = "?pg=okrs_equipos/home_equipos&page=' . $page . '&resultado_okr_=' . $_POST["id_resultado"] . '&iniciativa=' . $_POST["id_iniciativa"] . '";</script>';
}

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

$_SESSION["periodo_fill"] = $filtro_periodo != "" ? $filtro_periodo : "";

if ($_POST["anio_fill"] != "") {
	$_SESSION["anio_fill"] = $_POST["anio_fill"];
}
if ($_POST["anio_fill"] == -1) {
	$_SESSION["anio_fill"] = "";
}

if ($_POST["objestrategico_fill_equipo"] != "") {
	$_SESSION["objestrategico_fill_equipo"] = $_POST["objestrategico_fill_equipo"];
}
if ($_POST["objestrategico_fill_equipo"] == -1) {
	$_SESSION["objestrategico_fill_equipo"] = "";
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

$filtro_usuarios = "";
if ($_POST["colaborador_fill_equipo"] != "") {
	$_SESSION["colaborador_fill_equipo"] = $_POST["colaborador_fill_equipo"];
	$filtro_usuarios = "AND (responsables LIKE '%," . $_POST["colaborador_fill_equipo"] . ",%' OR responsables IN ('" . $_POST["colaborador_fill_equipo"] . "'))";
}
if ($_POST["colaborador_fill_equipo"] == -1) {
	$_SESSION["colaborador_fill_equipo"] = "";
	$filtro_usuarios = "";
}

if ($_POST["areas_fill_equipo"] != "") {
	$_SESSION["areas_fill_equipo"] = $_POST["areas_fill_equipo"];
}
if ($_POST["areas_fill_equipo"] == -1) {
	$_SESSION["areas_fill_equipo"] = "";
}

if ($_POST["equipo_fill_equipo"] != "") {
	$_SESSION["equipo_fill_equipo"] = $_POST["equipo_fill_equipo"];
}
if ($_POST["equipo_fill_equipo"] == -1) {
	$_SESSION["equipo_fill_equipo"] = "";
}

if ($_GET["id_area"] != "") {
	$_SESSION["areas_fill_equipo"] = $_GET["id_area"];
}

if ($_POST["tipo_fill_equipo"] != "") {
	$_SESSION["tipo_fill_equipo"] = $_POST["tipo_fill_equipo"];
	$_POST["tipo_fill_equipo"] = $_SESSION["tipo_fill_equipo"];
}
if ($_POST["tipo_fill_equipo"] == -1) {
	$_SESSION["tipo_fill_equipo"] = "";
}

if ($_GET["id_okr_reporte"] != "") {
	$_SESSION["equipo_fill_equipo"] = $_GET["id_okr_reporte"];
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
	echo '<script> window.location.href = "?pg=okrs_equipos/home_equipo&page=' . $page . '&resultado_okr_=' . $dataPA["id_resultado"] . '&iniciativa=' . $dataPA["id_iniciativa"] . '";</script>';
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
	$queryPA = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Actividades WHERE id = '".$_POST["id_registro_pa_edit"]."' ");
	$dataPA = mysqli_fetch_array($queryPA);
	echo '<script> window.location.href = "?pg=okrs_equipos/home_equipo&page=' . $page . '&resultado_okr_=' . $dataPA["id_resultado"] . '&iniciativa=' . $dataPA["id_iniciativa"] . '";</script>';
}

if ($_POST["guardar_avance_plan"] != "") {
	// print_r($_POST);
	$queryResultadosIni = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Actividades WHERE id = " . $_POST["id_plan"] . " ");
	while ($dataResultadosIni = mysqli_fetch_array($queryResultadosIni)) {
		$sentencia = "UPDATE Okrs_Actividades SET progreso = '" . $_POST["id_plan_" . $dataResultadosIni["id"] . ""] . "', updated_at = '$hoy' WHERE id = " . $dataResultadosIni["id"] . "";
		// mysqli_query($connect_okrs, $sentencia);

		$query2 = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $dataResultadosIni["id_okrs"] . "");
		$data2 = mysqli_fetch_array($query2);

		$accion = 'ACTUALIZAR';
		$descripcion = 'Actualización de avance para el plan de acción ' . $dataResultadosIni["descripcion"];

		$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $_SESSION["id_empresa"] . ", " . $_SESSION["id_user"] . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $dataResultadosIni["id_okrs"] . "," . $_POST["id_registro"] . "," . $dataResultadosIni["id"] . ",'$hoy')";
		// echo $auditoria;
		// mysqli_query($connect_okrs, $auditoria);
	}
	echo '<script> window.location.href = "?pg=okrs_equipos/home_equipo&plan_accion_=' . $_SESSION["id_user"] . '";</script>';
}

//POR OKRS

$id = $_GET['id'];
$resultado_total = 0;
$conteo_total = 0;
$conteo_okr = 0;
$query = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = '" . $_GET['id'] . "' ");
$data = mysqli_fetch_array($query);
$queryEE = mysqli_query($connect_valentina, "SELECT * FROM Estructura_Empresa WHERE area = '" . $_GET['id'] . "' AND estado = 1");
$dataEE = mysqli_fetch_array($queryEE);
$querySM82 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 8 AND id_submenu = 46");
$dataSM82 = mysqli_fetch_array($querySM82);

include("views/okrs_equipos/etiquetas.php");
$Array_Tipo_OKR1 = array(
	array("1", $etiquetaOkrOO),
	array("2", $etiquetaOkrOE),
);
?>

<link rel="stylesheet" href="<?php echo $url; ?>css/okrs.css">
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col-md-12">
						<h4><i class="fas fa-bullseye" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM82["nombre"]; ?></h4>
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
		Aquí podrá gestionar los objetivos, resultados, iniciativas y planes de acción de su área o equipo, recuerde que puede utilizar los filtros para acceder de forma rápida a la información requerida.
	</div>
	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<?php include("views/okrs_equipos/componentes/filtros_equipo.php"); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="container-fluid">

	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<ul class="nav nav-fill nav-tabs" id="myTab" role="tablist" style="font-family: 'Lato-Bold';">
						<li class="nav-item" role="presentation">
							<button class="nav-link active" id="objetivos-tab" data-bs-toggle="tab" data-bs-target="#objetivos-tab-pane" type="button" role="tab" aria-controls="objetivos-tab-pane" aria-selected="true" style="color: black;">Objetivos</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="iniciativas-tab" data-bs-toggle="tab" data-bs-target="#iniciativas-tab-pane" type="button" role="tab" aria-controls="iniciativas-tab-pane" aria-selected="false" style="color: black;">Iniciativas</button>
						</li>
						<li class="nav-item" role="presentation">
							<button class="nav-link" id="planesAccion-tab" data-bs-toggle="tab" data-bs-target="#planesAccion-tab-pane" type="button" role="tab" aria-controls="planesAccion-tab-pane" aria-selected="false" style="color: black;">Planes Acción</button>
						</li>
					</ul>
					<div class="tab-content pt-5" id="myTabContent_<?php echo $_SESSION["id_user"]; ?>">
						<div class="tab-pane fade show active" id="objetivos-tab-pane" role="tabpanel" aria-labelledby="objetivos-tab" tabindex="0">
							<?php include("views/okrs_equipos/home_equipo/objetivos.php"); ?>
						</div>

						<div class="tab-pane fade" id="iniciativas-tab-pane" role="tabpanel" aria-labelledby="iniciativas-tab" tabindex="0">
							<?php include("views/okrs_equipos/home_equipo/iniciativas.php"); ?>
						</div>
						<div class="tab-pane fade" id="planesAccion-tab-pane" role="tabpanel" aria-labelledby="planesAccion-tab" tabindex="0">
							<?php include("views/okrs_equipos/home_equipo/planes_accion.php"); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include("views/okrs_equipos/script_equipo.php");