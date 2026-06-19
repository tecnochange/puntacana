<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<link rel="stylesheet" href="<?php echo $url; ?>css/okrs.css">
<script>
    $(".menu_section").addClass("active");
    jQuery("#menu_okrs").css("display", "none");
    $("#bt_okrs_planes_accion").addClass("current-page");
</script>
<style>
    .progreso-bar {
        width: 150px !important;
        height: 150px !important;
    }

    .objetivo-okr {
        font-size: 2rem !important;
    }

    .nav-tabs .nav-item.show .nav-link,
    .nav-tabs .nav-link.active {
        background-color: #007BFF !important;
        color: white !important;
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
</style>
<?php
include("views/okrs_equipos/functions.php");

$congelar_okrs = true;
if ($_SESSION["anio_fill"] == $dtEmpresa["anio_curso"]) {
    $congelar_okrs = false;
}

$hoy = date("Y-m-d H:i:s");

$queryUser = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '" . $_SESSION["id_user"] . "' ");
$dataEmpleado = mysqli_fetch_array($queryUser);

if ($_POST["anio_fill"] != "") {
    $_SESSION["anio_fill"] = $_POST["anio_fill"];
}
if ($_POST["anio_fill"] == -1) {
    $_SESSION["anio_fill"] = "";
}

// $filtro_periodo = "";
// if ($_POST["Q1"] != "" || $_SESSION["Q1"] != "") {

//     if ($filtro_periodo != "") {
//         $filtro_periodo .= ", 'Q1'";
//     } else {
//         $filtro_periodo .= "'Q1'";
//     }
//     $_SESSION["Q1"] = $_POST["Q1"];
//     $_SESSION["Q1_fill"] = 1;
// } else {
//     $_SESSION["Q1"] = "";
//     $_SESSION["Q1_fill"] = "";
// }
// if ($_POST["Q2"] != "" || $_SESSION["Q2"] != "") {
//     if ($filtro_periodo != "") {
//         $filtro_periodo .= ", 'Q2'";
//     } else {
//         $filtro_periodo .= "'Q2'";
//     }
//     $_SESSION["Q2"] = $_POST["Q2"];
//     $_SESSION["Q2_fill"] = 1;
// } else {
//     $_SESSION["Q2"] = "";
//     $_SESSION["Q2_fill"] = "";
// }
// if ($_POST["Q3"] != "" || $_SESSION["Q3"] != "") {
//     if ($filtro_periodo != "") {
//         $filtro_periodo .= ", 'Q3'";
//     } else {
//         $filtro_periodo .= "'Q3'";
//     }
//     $_SESSION["Q3"] = $_POST["Q3"];
//     $_SESSION["Q3_fill"] = 1;
// } else {
//     $_SESSION["Q3"] = "";
//     $_SESSION["Q3_fill"] = "";
// }
// if ($_POST["Q4"] != "" || $_SESSION["Q4"] != "") {
//     if ($filtro_periodo != "") {
//         $filtro_periodo .= ", 'Q4'";
//     } else {
//         $filtro_periodo .= "'Q4'";
//     }
//     $_SESSION["Q4"] = $_POST["Q4"];
//     $_SESSION["Q4_fill"] = 1;
// } else {
//     $_SESSION["Q4"] = "";
//     $_SESSION["Q4_fill"] = "";
// }
// if ($_POST["Anual"] != "" || $_SESSION["Anual"] != "") {
//     if ($filtro_periodo != "") {
//         $filtro_periodo .= ", 'Anual'";
//     } else {
//         $filtro_periodo .= "'Anual'";
//     }
//     $_SESSION["Anual"] = $_POST["Anual"];
//     $_SESSION["Anual_fill"] = 1;
// } else {
//     $_SESSION["Anual"] = "";
//     $_SESSION["Anual_fill"] = "";
// }

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

$_SESSION["vicepresidencia_fill_okr"] = "";


if ($_POST["areas_fill_okr"] != "") {
    $_SESSION["areas_fill_okr"] = $_POST["areas_fill_okr"];
}
if ($_POST["areas_fill_okr"] == -1) {
    $_SESSION["areas_fill_okr"] = "";
}

if ($_POST["iniciativas_fill"] != "") {
    $_SESSION["iniciativas_fill"] = $_POST["iniciativas_fill"];
}
if ($_POST["iniciativas_fill"] == -1) {
    $_SESSION["iniciativas_fill"] = "";
}

if ($_POST["tipo_fill_okr"] != "") {
    $_SESSION["tipo_fill_okr"] = $_POST["tipo_fill_okr"];
}
if ($_POST["tipo_fill_okr"] == -1) {
    $_SESSION["tipo_fill_okr"] = "";
}

$hoy = date("Y-m-d H:i:s");

$filtro = "";

if ($_SESSION["areas_fill_okr"] > 0) {
    $filtro .= " AND Okrs_Areas.id_area = " . $_SESSION["areas_fill_okr"] . "  ";
}

if ($_SESSION["iniciativas_fill"] > 0) {
    $filtro .= " AND OI.id = " . $_SESSION["iniciativas_fill"] . "  ";
}

if ($_SESSION["tipo_fill_okr"] > 0) {
    $filtro .= " AND O.tipo = " . $_SESSION["tipo_fill_okr"] . "  ";
}

if ($filtro_periodo) {
    $filtro .= " AND ORE.periodo IN ($filtro_periodo) ";
}

if ($_SESSION["vicepresidencia_fill_okr"] > 0) {
    $filtro .= " AND Okrs_Areas.id_vicepresidencia = " . $_SESSION["vicepresidencia_fill_okr"] . "  ";
}

$queryPlanAccion = mysqli_query($connect_okrs, "SELECT DISTINCT(OA.id) AS id, OA.id_empresa AS id_empresa, OA.id_okrs AS id_okrs, OA.id_resultado AS id_resultado, OA.id_iniciativa AS id_iniciativa, OA.id_empleado AS id_empleado, OA.id_asignado AS id_asignado, OA.ciclo AS ciclo, OA.descripcion AS descripcion, OA.prioridad AS prioridad, OA.meta AS meta, OA.progreso AS progreso, OA.estado_backlog AS estado_backlog, OA.fecha_inicia AS fecha_inicia, OA.fecha_entrega AS fecha_entrega, OA.checked AS checked
FROM Okrs_Actividades OA
LEFT JOIN Okrs_Iniciativas OI ON OI.id = OA.id_iniciativa
LEFT JOIN Okrs O ON O.id = OI.id_okrs
LEFT JOIN Okrs_Resultados ORE ON ORE.id = OI.id_resultado
LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = OI.id_okrs
WHERE OA.id_asignado = " . $_SESSION["id_user"] . " AND O.anio = " . $_SESSION["anio_fill"] . " " . $filtro . "");

$sumProgreso = 0;

while ($dataPA = mysqli_fetch_array($queryPlanAccion)) {
    $porciento_plan = round(($dataPA['progreso'] / $dataPA['meta']) * 100);
    if (is_nan($porciento_plan) || is_infinite($porciento_plan)) {
        $porciento_plan = 0;
    }

    if ($porciento_plan > 100) {
        $porciento_plan = 100;
    }
    $sumProgreso = $sumProgreso + $porciento_plan;
}

$cantidadPA = mysqli_num_rows($queryPlanAccion);

$avanceGeneral = $sumProgreso / $cantidadPA;

$porciento = round($avanceGeneral);



if (is_nan($porciento) || is_infinite($porciento)) {
    $porciento = 0;
}

if ($porciento > 100) {
    $porcentaje_barra = 100;
    $porciento = 100;
} else {
    $porcentaje_barra = $porciento;
}

$escala_pa = EscalaColor($porciento, $_SESSION["id_empresa"], $connect_valentina);

if ($_POST["crear_plan_accion"]) {
    // print_r($_POST);
    $empleado = "";
    if ($_POST["emp_interno_pa"] != "") {
        $empleado = $_POST["emp_interno_pa"];
    }
    if ($_POST["emp_ext_pa"] != "") {
        $empleado = $_POST["emp_ext_pa"];
    }

    if ($_SESSION["id_empresa"] == 1) {
        $responsables = implode(",", $_POST["responsables"]);
    } else {
        $responsables = "";
    }

    $sentencia = "INSERT INTO Okrs_Actividades (id_empresa, id_okrs, id_resultado, id_iniciativa, id_empleado, id_asignado, ciclo, descripcion, prioridad, meta, estado_backlog, fecha_inicia, fecha_entrega, created_at, aprobacion)
        VALUES (" . $_SESSION["id_empresa"] . "," . $_POST["objetivo"] . "," . $_POST["resultado_okr"] . "," . $_POST["iniciativa_okr"] . "," . $_SESSION["id_user"] . ",'$responsables','" . $_POST["ciclo_pa"] . "','" . $_POST["descripcion_pa"] . "'
        ," . $_POST["prioridad_pa"] . " ," . $_POST["meta_pa"] . ",1,'" . $_POST["fecha_inicia_pa"] . "','" . $_POST["fecha_fin_pa"] . "','$hoy',1)";

    // $sentencia = "INSERT INTO Okrs_Actividades (id_empresa, id_okrs, id_resultado, id_iniciativa, id_empleado, id_asignado, ciclo, descripcion, prioridad, meta, estado_backlog, fecha_inicia, fecha_entrega, created_at, aprobacion)
    //     VALUES (" . $_SESSION["id_empresa"] . "," . $_POST["objetivo"] . "," . $_POST["resultado_okr"] . "," . $_POST["iniciativa_okr"] . "," . $_SESSION["id_user"] . ",$empleado,'" . $_POST["ciclo_pa"] . "','" . $_POST["descripcion_pa"] . "'
    //     ," . $_POST["prioridad_pa"] . " ," . $_POST["meta_pa"] . ",1,'" . $_POST["fecha_inicia_pa"] . "','" . $_POST["fecha_fin_pa"] . "','$hoy',1)";

    // echo "<br>".$sentencia;
    if (mysqli_query($connect_okrs, $sentencia)) {
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
		( '" . $_SESSION["id_empresa"] . "', '" . $_SESSION["id_user"] . "', '" . $id_tmp . "', '" . $archivo . "', '" . $_POST["comentario_plan"] . "', '" . $hoy . "' )
		";
                mysqli_query($connect_okrs, $sentencia_doc);
            }
        }
    }

    if ($_POST["comentario_plan"] != "") {
        $sentencia = "INSERT INTO Comentarios_Plan_Accion (id_empresa, id_empleado, id_plan, comentario, created_at)
    VALUES (" . $_SESSION["id_empresa"] . "," . $_SESSION["id_user"] . ",'" . $id_tmp . "','" . $_POST["comentario_plan"] . "','$hoy')";
        mysqli_query($connect_okrs, $sentencia);
    }

    echo '<script> window.location.href = "?pg=okrs_equipos/planes_accion";</script>';
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
    echo '<script> window.location.href = "?pg=okrs_equipos/planes_accion";</script>';
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
    echo '<script> window.location.href = "?pg=okrs_equipos/planes_accion";</script>';
}

if ($_POST["guardar_comentario_plan"] != "") {
    $sentencia = "INSERT INTO Comentarios_Plan_Accion (id_empresa, id_empleado, id_plan, comentario, created_at)
    VALUES (" . $_SESSION["id_empresa"] . "," . $_SESSION["id_user"] . ",'" . $_POST["id_plan"] . "','" . $_POST["comentario"] . "','$hoy')";
    mysqli_query($connect_okrs, $sentencia);

    echo '<script> window.location.href = "?pg=okrs_equipos/planes_accion";</script>';
}

if ($_POST["editar_comentario_plan"] != "") {
    $sentencia = "UPDATE Comentarios_Plan_Accion SET comentario = '" . $_POST["comentario_upd"] . "',updated_at = '$hoy' WHERE id = " . $_POST["id_comentario"] . "";
    mysqli_query($connect_okrs, $sentencia);

    echo '<script> window.location.href = "?pg=okrs_equipos/planes_accion";</script>';
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

    echo '<script> window.location.href = "?pg=okrs_equipos/planes_accion";</script>';
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

    echo '<script> window.location.href = "?pg=okrs_equipos/planes_accion";</script>';
}

if ($_POST["duplicar_plan_accion"] != "") {

    // print_r($_POST);
    if ($_POST["id_registro"] != "") {
        $planAccion = DuplicarPlanAccion($connect_okrs, $_POST, $hoy, $_SESSION["id_empresa"], $_SESSION["id_user"]);
    }

    echo '<script> window.location.href = "?pg=okrs_equipos/planes_accion";</script>';
}

$back_color = "background-color:" . $escala_pa['color_bg'] . " !important";

$querySM86 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 8 AND id_submenu = 50");
$dataSM86 = mysqli_fetch_array($querySM86);
include("views/okrs_equipos/etiquetas.php");
$Array_Tipo_OKR1 = array(
    array("1", $etiquetaOkrOO),
    array("2", $etiquetaOkrOE),
);
include("views/okrs/layouts/modal_okr.php");
include("views/okrs/layouts/modal_profile.php");
?>

<script>
    $(document).ready(function() {
        $("#progreso_desempenio").html('<div class="progresos" data-bs-toggle="tooltip" align="center">' +
            '<h1 style="font-size: 3.5rem;color: black !important;"><?php echo round($porciento, 2); ?> %</h1></div>' +
            '<div class="progress-bar bg-success" role="progressbar" style=" width: <?php echo round($porcentaje_barra, 2); ?>%; <?php echo $back_color; ?>;opacity: 0.3;z-index: 2;margin-top: -70px;height: 70px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">' +
            '</div><br>'
        );
    });
</script>
<style>
    .card-header {
        background-color: white !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12">
                        <h4><i class="fas fa-bullseye" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM86["nombre"]; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <?php include("views/okrs_equipos/componentes/filtros_planes_accion.php"); ?>
                </div>
                <div class="card-body">
                    <?php include("views/okrs_equipos/graficos/home_planes_accion.php"); ?>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">

                    <div class="col-md-12">
                        <ul class="nav nav-fill nav-tabs" id="myTab" role="tablist" style="font-family: 'Lato-Bold';">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="listado-tab" data-bs-toggle="tab" data-bs-target="#listado-tab-pane" type="button" role="tab" aria-controls="listado-tab-pane" aria-selected="true" style="color: black;">Listado</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="kanban-tab" data-bs-toggle="tab" data-bs-target="#kanban-tab-pane" type="button" role="tab" aria-controls="kanban-tab-pane" aria-selected="false" style="color: black;">Tablero Kanban</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="time_line-tab" data-bs-toggle="tab" data-bs-target="#time_line-tab-pane" type="button" role="tab" aria-controls="time_line-tab-pane" aria-selected="false" style="color: black;">Time Line</button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="col-md-12">
                        <div class="tab-content pt-5" id="myTabContent_<?php echo $_SESSION["id_user"]; ?>">
                            <div class="tab-pane fade show active" id="listado-tab-pane" role="tabpanel" aria-labelledby="listado-tab" tabindex="0">
                                <?php include("views/okrs_equipos/planes_accion/planes_accion.php"); ?></div>
                            <div class="tab-pane fade" id="kanban-tab-pane" role="tabpanel" aria-labelledby="kanban-tab" tabindex="0">
                                <?php include("views/okrs_equipos/planes_accion/tablero_kanban.php"); ?>
                            </div>
                            <div class="tab-pane fade" id="time_line-tab-pane" role="tabpanel" aria-labelledby="time_line-tab" tabindex="0">
                                <?php include("views/okrs_equipos/planes_accion/diagrama_gantt.php"); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>

</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var divElementEB = document.getElementById("myTabContent_<?php echo $_GET["estadoBacklog_"]; ?>");
        if (divElementEB) {
            divElementEB.scrollIntoView();
            $("#listado-tab").removeClass("active");
            $("#listado-tab-pane").removeClass("active show");
            $("#kanban-tab").addClass("active");
            $("#kanban-tab-pane").addClass("active show");
        }
    });
    window.location.hash = "";

    function NumerosDecimales(input) {
        let valor = input.value;

        valor = valor.replace(/[^0-9,\.-]/g, '');

        let esNegativo = valor.indexOf('-') === 0;

        let puntos = (valor.match(/\./g) || []).length;
        if (puntos > 1) {
            valor = valor.replace(/\.(?=.*\.)/g, '');
        }

        if (valor.indexOf(',') !== -1) {
            let partes = valor.split(',');
            if (partes[1].length > 2) {
                partes[1] = partes[1].substring(0, 2);
            }
            valor = partes.join(',');
        }

        if (valor.indexOf('.') !== -1) {
            let partes = valor.split('.');
            if (partes.length > 2) {
                valor = partes[0] + '.' + partes[1];
            }

            if (partes[1] && partes[1].length < 3) {
                alert('Falta un dígito para el separador de miles.');
            }
        }

        if (parseFloat(valor) < 1 && !valor.includes(',')) {
            valor = valor.replace('.', ',');
        }

        if (esNegativo) {
            valor = '-' + valor.replace('-', '');
        }

        input.value = valor;
    }


    function GuardarAvances(id) {
        window.location = "?pg=okrs_equipos/planes_accion";
    }

    function Filtrar() {
        $("#formulario_filtro").submit();
    }

    var api = '<?php echo $url; ?>api/okrs/';

    function Guardar_Avance_Plan(avance, id, id_okr, id_resultado, id_empresa, id_user, id_iniciativa) {
        toastr.clear();
        data = {
            id: id,
            id_okr: id_okr,
            id_resultado: id_resultado,
            id_empresa: id_empresa,
            id_user: id_user,
            avance: avance,
            id_realiza: <?php echo $_SESSION['id_user']; ?>

        };
        jQuery.ajax({
                url: api + "guardar_avance_plan.php",
                type: 'post',
                data: data,
                cache: false,
            }).done(function(resp) {
                toastr.success('Actualización de seguimiento exitoso', '¡Éxito!');
                $('#planes_accion').DataTable().ajax.reload();

            })
            .fail(function(resp) {
                // console.log(resp);
            })
            .always(function(resp) {});
    }

    function Guardar_Estado_Plan(avance, id, id_okr, id_resultado, id_empresa, id_user) {
        toastr.clear();
        data = {
            id: id,
            id_okr: id_okr,
            id_resultado: id_resultado,
            id_empresa: id_empresa,
            id_user: id_user,
            avance: avance,
            id_realiza: <?php echo $_SESSION['id_user']; ?>
        };
        jQuery.ajax({
                url: api + "guardar_estado_plan.php",
                type: 'post',
                data: data,
            }).done(function(resp) {
                toastr.success('Actualización de estado backlog exitoso', '¡Éxito!');
                $('#planes_accion').DataTable().ajax.reload();
            })
            .fail(function(resp) {
                // console.log(resp);
            })
            .always(function(resp) {});
    }

    function Guardar_Prioridad_Plan(avance, id, id_okr, id_resultado, id_empresa, id_user) {
        toastr.clear();
        data = {
            id: id,
            id_okr: id_okr,
            id_resultado: id_resultado,
            id_empresa: id_empresa,
            id_user: id_user,
            avance: avance,
            id_realiza: <?php echo $_SESSION['id_user']; ?>
        };
        jQuery.ajax({
                url: api + "guardar_prioridad_plan.php",
                type: 'post',
                data: data,
            }).done(function(resp) {
                toastr.success('Actualización de prioridad exitoso', '¡Éxito!');
                $('#planes_accion').DataTable().ajax.reload();
            })
            .fail(function(resp) {
                // console.log(resp);
            })
            .always(function(resp) {});
    }
    <?php if ($permisoEditPA == true) { ?>

        function Cambiar_Estado(avance, id) {
            toastr.clear();
            data = {
                id: id,
                avance: avance
            };
            jQuery.ajax({
                    url: api + "guardar_estado_plan.php",
                    type: 'post',
                    data: data,
                }).done(function(resp) {
                    toastr.success('Actualización de estado exitosa', '¡Éxito!');
                    $('#planes_accion').DataTable().ajax.reload();
                })
                .fail(function(resp) {
                    // console.log(resp);
                })
                .always(function(resp) {});
        }
    <?php } ?>
    var api_admin = '<?php echo $url; ?>api/administrar/';

    function Profile(id, val) {
        toastr.clear();
        jQuery.ajax({
                url: api + "profile_empleado.php",
                type: 'post',
                data: {
                    id: id,
                    val: val
                },
            }).done(function(resp) {
                $("#modal_profile").modal("show");
                $("#modal_contenidos").html(resp);
            })
            .fail(function(resp) {
                // console.log(resp);
            })
            .always(function(resp) {});
    }
</script>
<script>
    $(document).off('submit', '#editar_plan_accion-form').on('submit', '#editar_plan_accion-form', function(event) {
        event.preventDefault();
        var formDataPA = $(this).serialize();
        toastr.clear();

        $.ajax({
            url: api + "actualizar_plan_accion.php",
            type: 'POST',
            data: formDataPA,
            cache: false,
            success: function(response) {
                var res = JSON.parse(response);

                if (res.success) {
                    toastr.success("<?php echo $etiquetaOkrPA; ?> guardado correctamente");

                    $('#planes_accion').DataTable().ajax.reload();

                    $("#modal_okr").modal("hide");
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Hubo un problema al crear el <?php echo $etiquetaOkrPA; ?>. Intente nuevamente.");
            }
        });
    });
    $(document).off('submit', '#copiar_plan_accion-form').on('submit', '#copiar_plan_accion-form', function(event) {
        event.preventDefault();

        var formDataPA = $(this).serialize();
        toastr.clear();

        $.ajax({
            url: api + "duplicar_plan_accion.php",
            type: 'POST',
            data: formDataPA,
            cache: false,
            success: function(response) {
                var res = JSON.parse(response);

                if (res.success) {
                    toastr.success("<?php echo $etiquetaOkrPA; ?> duplicado correctamente");

                    $('#planes_accion').DataTable().ajax.reload();

                    $("#modal_okr").modal("hide");
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Hubo un problema al duplicar el <?php echo $etiquetaOkrPA; ?>. Intente nuevamente.");
            }
        });
    });
    $(document).off('submit', '#comentario_plan_accion-form').on('submit', '#comentario_plan_accion-form', function(event) {
        event.preventDefault();

        var formDataPA = $(this).serialize();
        toastr.clear();

        $.ajax({
            url: api + "comentar_plan_accion.php",
            type: 'POST',
            data: formDataPA,
            cache: false,
            success: function(response) {
                var res = JSON.parse(response);

                if (res.success) {
                    toastr.success("Comentario para el <?php echo $etiquetaOkrPA; ?> guardado correctamente");

                    $('#planes_accion').DataTable().ajax.reload();

                    $("#modal_okr").modal("hide");
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Hubo un problema al crear el comentario para el <?php echo $etiquetaOkrPA; ?>. Intente nuevamente.");
            }
        });
    });
    $(document).off('submit', '#editar_comentario_plan_accion-form').on('submit', '#editar_comentario_plan_accion-form', function(event) {
        event.preventDefault();

        var formDataPA = $(this).serialize();
        toastr.clear();

        $.ajax({
            url: api + "edita_comentario_plan.php",
            type: 'POST',
            data: formDataPA,
            cache: false,
            success: function(response) {
                var res = JSON.parse(response);

                if (res.success) {
                    toastr.success("Comentario para el <?php echo $etiquetaOkrPA; ?> editado correctamente");

                    $('#planes_accion').DataTable().ajax.reload();

                    $("#modal_okr").modal("hide");
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Hubo un problema al editar el comentario para el <?php echo $etiquetaOkrPA; ?>. Intente nuevamente.");
            }
        });
    });

    $(document).off('submit', '#documento_plan_accion-form').on('submit', '#documento_plan_accion-form', function(event) {
        event.preventDefault();

        let formDataPA = new FormData(this);
        toastr.clear();

        $.ajax({
            url: api + "documento_plan_accion.php",
            type: 'POST',
            data: formDataPA,
            contentType: false, // Evita que jQuery establezca un tipo de contenido incorrecto
            processData: false, // Evita que jQuery convierta `FormData` en una cadena de consulta
            cache: false,
            success: function(response) {
                var res = JSON.parse(response);

                if (res.success) {
                    toastr.success("Documento para el <?php echo $etiquetaOkrPA; ?> cargado correctamente");

                    $('#planes_accion').DataTable().ajax.reload();

                    $("#modal_okr").modal("hide");
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Hubo un problema al cargar el documento para el <?php echo $etiquetaOkrPA; ?>. Intente nuevamente.");
            }
        });
    });

    $(document).off('submit', '#editar_documento_plan_accion-form').on('submit', '#editar_documento_plan_accion-form', function(event) {
        event.preventDefault();

        let formDataPA = new FormData(this);
        toastr.clear();

        $.ajax({
            url: api + "editar_documento_plan_accion.php",
            type: 'POST',
            data: formDataPA,
            contentType: false, // Evita que jQuery establezca un tipo de contenido incorrecto
            processData: false, // Evita que jQuery convierta `FormData` en una cadena de consulta
            cache: false,
            success: function(response) {
                var res = JSON.parse(response);

                if (res.success) {
                    toastr.success("Documento para el <?php echo $etiquetaOkrPA; ?> actualizado correctamente");

                    $('#planes_accion').DataTable().ajax.reload();

                    $("#modal_okr").modal("hide");
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Hubo un problema al actualizar el documento para el <?php echo $etiquetaOkrPA; ?>. Intente nuevamente.");
            }
        });
    });

    function EnviarSoloConEnter(event, input, id, id_okr, id_resultado, id_empresa, id_user, id_iniciativa) {
        if (event.key === "Enter") {
            event.preventDefault(); // Evita que el formulario se envíe
            event.stopPropagation(); // Evita la propagación del evento

            Guardar_Avance_Plan(input.value, id, id_okr, id_resultado, id_empresa, id_user, id_iniciativa);

            return false;
        }
    }
</script>