<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<link rel="stylesheet" href="<?php echo $url; ?>css/okrs.css">
<script>
    $(".menu_section").addClass("active");
    jQuery("#menu_okrs").css("display", "none");
    $("#bt_okrs_iniciativas").addClass("current-page");
</script>
<?php

include("views/okrs_equipos/functions.php");
$hoy = date("Y-m-d H:i:s");
if ($_POST["guardar_avance_iniciativas"] != "") {
    // print_r($_POST);
    $queryResultadosIni = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Iniciativas WHERE id = " . $_POST["id_iniciativa"] . " ");
    while ($dataKrsIni = mysqli_fetch_array($queryResultadosIni)) {
        $sentencia = "UPDATE Okrs_Iniciativas SET avance = '" . $_POST["id_iniciativa_" . $dataKrsIni["id"] . ""] . "', updated_at = '$hoy' WHERE id = " . $dataKrsIni["id"] . "";
        // mysqli_query($connect_okrs, $sentencia);

        $query2 = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $dataKrsIni["id_okrs"] . "");
        $data2 = mysqli_fetch_array($query2);

        $accion = 'ACTUALIZAR';
        $descripcion = 'Actualización de avance para la Iniciativa ' . $dataKrsIni["descripcion"];

        $auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $_SESSION["id_empresa"] . ", " . $_SESSION["id_user"] . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $dataKrsIni["id_okrs"] . "," . $_POST["id_registro"] . "," . $dataKrsIni["id"] . ",'$hoy')";
        // echo $auditoria;
        // mysqli_query($connect_okrs, $auditoria);
    }
    echo '<script> window.location.href = "?pg=okrs_equipos/mis_iniciativas";</script>';
}

if ($_POST["guardar_iniciativa"] != "") {

    GuardarInciativa($_POST, $connect_okrs, $dtEmpleado["id"], $_SESSION["id_empresa"], $_SESSION["id_user"]);

    $resultado_okr = $_POST["id_resultado"];

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
    echo '<script> window.location.href = "?pg=okrs_equipos/mis_iniciativas";</script>';
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
    // echo $sentencia_doc;
    mysqli_query($connect_okrs, $sentencia_doc);
    $accion = 'CREAR';
    $descripcion = 'Cargue de documento para la iniciativa ' . $dataIniciativa["descripcion"];
    $auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $_SESSION["id_empresa"] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion'," . $dataOkr["tipo"] . "," . $dataOkr["id"] . "," . $dataIniciativa["id_resultado"] . ",$id_iniciativa,'$hoy')";
    // echo $auditoria;
    mysqli_query($connect_okrs, $auditoria);
    echo '<script> window.location.href = "?pg=okrs_equipos/mis_iniciativas";</script>';
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
    echo '<script> window.location.href = "?pg=okrs_equipos/mis_iniciativas";</script>';
}

if ($_POST["duplicar_iniciativa"] != "") {

    if ($_POST["id_registro"] != "") {
        $iniciativa = DuplicarIniciativa($connect_okrs, $_POST, $hoy, $_SESSION["id_empresa"], $_SESSION["id_user"]);
    }

    echo '<script> window.location.href = "?pg=okrs_equipos/mis_iniciativas";</script>';
}

if ($_POST["crear_plan_accion"] != "") {

    $empleado = "";
    if ($_POST["empleado_asignado"] == 1) {
        $empleado = $_POST["emp_interno_pa"];
    }

    if ($_POST["empleado_asignado"] == 2) {
        $empleado = $_POST["emp_ext_pa"];
    }

    // print_r($_SESSION);

    $empleado = "";
    if ($_POST["empleado_asignado"] == 1) {
        $empleado = $_POST["emp_interno_pa"];
    }

    if ($_POST["empleado_asignado"] == 2) {
        $empleado = $_POST["emp_ext_pa"];
    }

    // print_r($_SESSION);
    if ($_SESSION["id_empresa"] == 1) {
        $sentencia_pa = "INSERT INTO Okrs_Actividades (id_empresa, id_okrs, id_resultado, id_iniciativa, id_empleado, id_asignado, descripcion, prioridad, meta, progreso, estado_backlog, created_at, fecha_inicia, fecha_entrega, ciclo, aprobacion) VALUES
    (" . $_SESSION["id_empresa"] . "," . $_POST["id_okrs_pa"] . "," . $_POST["id_resultado_pa"] . "," . $_POST["id_registro_pa"] . "," . $_SESSION['id_user'] . "," . $empleado . ",'" . $_POST["descripcion_pa"] . "'," . $_POST["prioridad_pa"] . ",'" . $_POST["meta_pa"] . "',0,1,'$hoy','" . $_POST["fecha_inicia_pa"] . "','" . $_POST["fecha_fin_pa"] . "','" . $_POST["ciclo_pa"] . "',1)";
    } else {
        $sentencia_pa = "INSERT INTO Okrs_Actividades (id_empresa, id_okrs, id_resultado, id_iniciativa, id_empleado, id_asignado, descripcion, prioridad, meta, progreso, estado_backlog, created_at, fecha_inicia, fecha_entrega, ciclo) VALUES
	(" . $_SESSION["id_empresa"] . "," . $_POST["id_okrs_pa"] . "," . $_POST["id_resultado_pa"] . "," . $_POST["id_registro_pa"] . "," . $_SESSION['id_user'] . "," . $empleado . ",'" . $_POST["descripcion_pa"] . "'," . $_POST["prioridad_pa"] . ",'" . $_POST["meta_pa"] . "',0,1,'$hoy','" . $_POST["fecha_inicia_pa"] . "','" . $_POST["fecha_fin_pa"] . "','" . $_POST["ciclo_pa"] . "')";
    }

    // echo "<br>$sentencia_pa";

    if (mysqli_query($connect_okrs, $sentencia_pa)) {
        $id_tmp = mysqli_insert_id($connect_okrs);
        if ($_SESSION["id_empresa"] != 1) {
            mysqli_query($connect_okrs, "INSERT INTO Notificacion_Plan_Accion (id_empresa, id_plan,id_empleado,id_asignado,estado,created_at)
		VALUES(" . $_SESSION["id_empresa"] . ",$id_tmp," . $_SESSION["id_user"] . "," . $empleado . ",4,'$hoy')");
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
    echo '<script> window.location.href = "?pg=okrs_equipos/mis_iniciativas";</script>';
}

$congelar_okrs = true;
if ($_SESSION["anio_fill"] == $dtEmpresa["anio_curso"]) {
    $congelar_okrs = false;
}

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

// $_SESSION["periodo_fill"] = $filtro_periodo != "" ? $filtro_periodo : "";

if ($_POST["areas_fill_okr"] != "") {
    $_SESSION["areas_fill_okr"] = $_POST["areas_fill_okr"];
}
if ($_POST["areas_fill_okr"] == -1) {
    $_SESSION["areas_fill_okr"] = "";
}

if ($_POST["resultados_fill"] != "") {
    $_SESSION["resultados_fill"] = $_POST["resultados_fill"];
}
if ($_POST["resultados_fill"] == -1) {
    $_SESSION["resultados_fill"] = "";
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

if ($_SESSION["resultados_fill"] > 0) {
    $filtro .= " AND ORE.id = " . $_SESSION["resultados_fill"] . "  ";
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
$empresa = $_SESSION["id_empresa"];
$anio_fill = $_SESSION["anio_fill"];
$keyUser = $_SESSION['id_user'];
$queryMisIniciativas = mysqli_query($connect_okrs, "SELECT DISTINCT(OI.id), OI.id_okrs,OI.id_resultado,OI.id_empleado,OI.responsables,OI.descripcion,OI.mes,OI.fecha_entrega,OI.meta,OI.avance,OI.tendencia, ORE.descripcion AS descripcion_kr
FROM Okrs_Iniciativas OI
LEFT JOIN Okrs O ON O.id = OI.id_okrs
LEFT JOIN Okrs_Resultados ORE ON ORE.id = OI.id_resultado
LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = OI.id_okrs
 WHERE O.id_empresa = ".$empresa ." AND O.anio = " . $anio_fill . " AND CONCAT(',',OI.responsables,',') LIKE '%," . $keyUser . ",%' " . $filtro . " ORDER BY OI.fecha_entrega ASC");

$sumProgreso = 0;

while ($dataMIni = mysqli_fetch_array($queryMisIniciativas)) {
    // $porcientoIni = ($dataMIni["avance"] * 100) / $dataMIni["meta"];
    // if ($dataMIni["tendencia"] == 2) {
    //     $porcientoIni = ($dataMIni["meta"] / $dataMIni["avance"] * 100);
    // }
    $meta = convertirSeparadorDecimal($dataMIni["meta"]);
    $avance = convertirSeparadorDecimal($dataMIni["avance"]);

    // $porciento = ($avance * 100) / $meta;
    $porcientoIni = calcularProgresoAscendente($meta, $avance);

    if ($dataMIni["tendencia"] == 2) {
        // $porciento = ($meta / $avance * 100);
        $porcientoIni = calcularProgresoDescendente($meta, $avance);
    }
    if (is_nan($porcientoIni) || is_infinite($porcientoIni)) {
        $porcientoIni = 0;
    }
    // if ($porcientoIni > 100) {
    //     $porcientoIni = 100;
    // }
    $sumProgreso = $sumProgreso + $porcientoIni;
}

$cantidadMIni = mysqli_num_rows($queryMisIniciativas);

$avanceGeneral = $sumProgreso / $cantidadMIni;

$porciento = round($avanceGeneral);

if (is_nan($porciento) || is_infinite($porciento)) {
    $porciento = 0;
}

if ($porciento > 100) {
    $porcentaje_barra = 100;
} else {
    $porcentaje_barra = $porciento;
}

$escala_pa = EscalaColor($porciento, $_SESSION["id_empresa"], $connect_valentina);
$back_color = "background-color:" . $escala_pa['color_bg'] . " !important";

include("views/okrs/layouts/modal_crear_okr.php");
include("views/okrs/layouts/modal_okr.php");
include("views/okrs/layouts/modal_profile.php");
include("views/okrs_equipos/componentes/modal_tipo_rol.php");
$querySM85 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 8 AND id_submenu = 49");
$dataSM85 = mysqli_fetch_array($querySM85);

include("views/okrs_equipos/etiquetas.php");
$Array_Tipo_OKR1 = array(
    array("1", $etiquetaOkrOO),
    array("2", $etiquetaOkrOE),
);
include("views/okrs_equipos/permisos_rol.php");
?>
<style>
    .card,
    .card-header,
    .card-body,
    .card-footer {
        background-color: #ffffff !important;
    }

    #iconCabecera {
        font-size: 24px;
        color: #007ae1;
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
<script>
    $(document).ready(function() {
        $("#progreso_desempenio").html('<div class="progresos" data-bs-toggle="tooltip" align="center">' +
            '<h1 style="font-size: 3.5rem;color: black !important;"><?php echo round($porciento, 2); ?> %</h1></div>' +
            '<div class="progress-bar bg-success" role="progressbar" style=" width: <?php echo round($porcentaje_barra, 2); ?>%; <?php echo $back_color; ?>;opacity: 0.3;z-index: 2;margin-top: -70px;height: 70px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">' +
            '</div><br>'
        );
    });
</script>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12">
                        <h4><i class="fas fa-bullseye" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM85["nombre"]; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="contaier-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <?php include("views/okrs_equipos/componentes/filtros_mis_iniciativas.php"); ?>
                </div>
                <div class="card-body">
                    <?php include("views/okrs_equipos/graficos/home_iniciativas.php"); ?>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <p style="font-size: 15px !important;">Recuerde digitar el valor de cada seguimiento y oprimir la tecla enter, después de guardar los seguimientos correspondientes, actualizar la página o dar clic en el botón que se encuentra en la parte inferior para recargar la página y visualizar los valores actualizados de los seguimientos. <br>
                                Para garantizar la correcta interpretación de los valores ingresados, por favor ten en cuenta lo siguiente:<br>
                                Separador de Miles "." (Punto)<br>
                                Ejemplo: $500.000 (quinientos mil)<br>
                                Separador de Decimales "," (Coma)<br>
                                Ejemplo: 0,7 (siete décimos) | -0,5 (menos cinco décimos)
                            </p>
                        </div>
                    </div>
                </div>
                <div class="card-body">

                    <table border="1" id="mis_iniciativas" class="display table" style="width:100%">
                        <thead>
                            <tr>
                                <th></th>
                                <th><?php echo $etiquetaOkrI; ?></th>
                                <th>Mes</th>
                                <th>Fecha Entrega</th>
                                <th>Meta</th>
                                <th>Seguimiento</th>
                                <th>Porcentaje</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <br>
                    <div class="row">
                        <div class="col-md-12" style="text-align: end;">
                            <button class="btn btn-success" type="button" onclick="GuardarAvances(<?php echo $dataKrs["id"]; ?>)">Actualizar avances</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
    function Filtrar() {
        $("#formulario_filtro").submit();
    }

    function GuardarAvances(id) {
        window.location = "?pg=okrs_equipos/mis_iniciativas";
    }


    var api = '<?php echo $url; ?>api/okrs/';

    function VerIniciativa(id, id_iniciativa, role) {
        jQuery.ajax({
                url: api + "editar_iniciativa.php",
                type: 'post',
                data: {
                    id: id,
                    id_iniciativa: id_iniciativa,
                    id_empresa: <?php echo $_SESSION["id_empresa"] ?>,
                    id_empleado: <?php echo $_SESSION["id_user"] ?>,
                    role: role
                },
            }).done(function(resp) {
                $("#modal_okr").modal("show");
                $("#modal_contenido").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    $(document).on('submit', '#iniciativa-form', function(event) {
        event.preventDefault();

        $('input[name="id_resultado"]').val($('input[name="id_resultado"]').val().trim());

        var idResultado = $('input[name="id_resultado"]').val().trim();
        console.log("Valor actualizado idResultado antes del envío:", idResultado);

        var formData = $(this).serialize();
        toastr.clear();

        $.ajax({
            url: api + "guardar_iniciativa.php",
            type: 'POST',
            data: formData,
            cache: false,
            success: function(response) {
                var res = JSON.parse(response);

                if (res.success) {
                    toastr.success(res.message);

                    $('#mis_iniciativas').DataTable().ajax.reload();

                    $("#modal_okr").modal("hide");
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Hubo un problema al guardar <?php echo $etiquetaOkrI; ?>. Intente nuevamente.");
            }
        });
    });

    $(document).on('submit', '#iniciativa-form', function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        toastr.clear();

        $.ajax({
            url: api + "guardar_iniciativa.php",
            type: 'POST',
            data: formData,
            cache: false,
            success: function(response) {
                var res = JSON.parse(response);

                if (res.success) {
                    toastr.success(res.message);

                    $('#mis_iniciativas').DataTable().ajax.reload();

                    $("#modal_okr").modal("hide");
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Hubo un problema al guardar <?php echo $etiquetaOkrI; ?>. Intente nuevamente.");
            }
        });
    });

    $(document).on('submit', '#copiar_iniciativa-form', function(event) {
        event.preventDefault();
        var formData = $(this).serialize();
        toastr.clear();

        $.ajax({
            url: api + "duplicar_iniciativa.php",
            type: 'POST',
            data: formData,
            cache: false,
            success: function(response) {
                var res = JSON.parse(response);

                if (res.success) {
                    toastr.success(res.message);
                    $('#mis_iniciativas').DataTable().ajax.reload();

                    $("#modal_okr").modal("hide");
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Hubo un problema al guardar <?php echo $etiquetaOkrI; ?>. Intente nuevamente.");
            }
        });
    });

    $(document).on('submit', '#comentarios_iniciativa-form', function(event) {
        event.preventDefault();

        var formData = $(this).serialize();
        toastr.clear();

        $.ajax({
            url: api + "comentario_iniciativa.php",
            type: 'POST',
            data: formData,
            cache: false,
            success: function(response) {
                var res = JSON.parse(response);

                if (res.success) {
                    toastr.success(res.message);

                    $('#mis_iniciativas').DataTable().ajax.reload();

                    $("#modal_okr").modal("hide");
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Hubo un problema al guardar <?php echo $etiquetaOkrI; ?>. Intente nuevamente.");
            }
        });
    });

    $(document).on('submit', '#edita_comentario_iniciativa-form', function(event) {
        event.preventDefault();

        var formData = $(this).serialize();
        toastr.clear();

        $.ajax({
            url: api + "edita_comentario_iniciativa.php",
            type: 'POST',
            data: formData,
            cache: false,
            success: function(response) {
                var res = JSON.parse(response);

                if (res.success) {
                    toastr.success(res.message);

                    $('#mis_iniciativas').DataTable().ajax.reload();

                    $("#modal_okr").modal("hide");
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Hubo un problema al guardar <?php echo $etiquetaOkrI; ?>. Intente nuevamente.");
            }
        });
    });

    $(document).on('submit', '#documento_iniciativa-form', function(event) {
        event.preventDefault();

        let formDataIni = new FormData(this);

        toastr.clear();

        $.ajax({
            url: api + "documento_iniciativa.php",
            type: 'POST',
            data: formDataIni,
            cache: false,
            processData: false,
            contentType: false,
            success: function(response) {
                var res = JSON.parse(response);

                if (res.success) {
                    toastr.success(res.message);

                    $('#mis_iniciativas').DataTable().ajax.reload();

                    $("#modal_okr").modal("hide");
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Hubo un problema al guardar <?php echo $etiquetaOkrI; ?>. Intente nuevamente.");
            }
        });
    });

    $(document).on('submit', '#edita_documento_iniciativa-form', function(event) {
        event.preventDefault();
        let formDataIni = new FormData(this);

        toastr.clear();

        $.ajax({
            url: api + "edita_documento_iniciativa.php",
            type: 'POST',
            data: formDataIni,
            cache: false,
            processData: false,
            contentType: false,
            success: function(response) {
                var res = JSON.parse(response);

                if (res.success) {
                    toastr.success(res.message);

                    $('#mis_iniciativas').DataTable().ajax.reload();

                    $("#modal_okr").modal("hide");
                } else {
                    toastr.error(res.message);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("Hubo un problema al guardar <?php echo $etiquetaOkrI; ?>. Intente nuevamente.");
            }
        });
    });

    $(document).off('submit', '#plan_accion-form').on('submit', '#plan_accion-form', function(event) {
        event.preventDefault();

        $('input[name="id_registro_pa"]').val($('input[name="id_registro_pa"]').val().trim());

        var idIni = $('input[name="id_registro_pa"]').val().trim();
        // console.log("Valor actualizado idIni antes del envío:", idIni);

        $('input[name="id_resultado_pa"]').val($('input[name="id_resultado_pa"]').val().trim());

        var idResultadoPA = $('input[name="id_resultado_pa"]').val().trim();
        // console.log("Valor actualizado idResultadoPA antes del envío:", idResultadoPA);

        let formDataPA = new FormData(this);
        toastr.clear();

        $.ajax({
            url: api + "guardar_plan_accion.php",
            type: 'POST',
            data: formDataPA,
            contentType: false, // Evita que jQuery establezca un tipo de contenido incorrecto
            processData: false, // Evita que jQuery convierta `FormData` en una cadena de consulta
            cache: false,
            success: function(response) {
                var res = JSON.parse(response);

                if (res.success) {
                    $('#mis_iniciativas').DataTable().ajax.reload();
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

    function VerIniciativaNew(id, id_iniciativa, role, id_user, congelar) {
        window.open("<?php echo $url; ?>views_okrs/gestionar_iniciativa.php?id_resultado=" + id + "&id_iniciativa=" + id_iniciativa + "&role=" + role + "&id_user=" + id_user + "&congelar=" + congelar, "GoForAgile", "width=1300, height=900")
    }

    function VerIniciativaPA(id, id_iniciativa, role, id_user, congelar) {
        window.open("<?php echo $url; ?>views_okrs/gestionar_iniciativa.php?id_resultado=" + id + "&id_iniciativa=" + id_iniciativa + "&role=" + role + "&id_user=" + id_user + "&congelar=" + congelar + "&planAccion_=" + id_iniciativa, "GoForAgile", "width=1300, height=900")
    }

    function Guardar_Avance_Iniciativa_buton() {
        console.log('guardado');
    };

    function Guardar_Avance_Iniciativa(avance, id, id_okr, id_resultado, id_empresa, id_user) {
        data = {
            id: id,
            id_okr: id_okr,
            id_resultado: id_resultado,
            id_empresa: '<?php echo $_SESSION['id_empresa']; ?>',
            id_user: id_user,
            avance: avance,
            id_realiza: '<?php echo $_SESSION['id_user']; ?>'
        };
        jQuery.ajax({
                url: api + "guardar_avance_iniciativas_propias.php",
                type: 'post',
                data: data,
            }).done(function(resp) {
                toastr.success('Actualización de seguimiento iniciativa realizado con éxito', '¡Éxito!');
                $('#mis_iniciativas').DataTable().ajax.reload();
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function PAIniciativa(id) {
        jQuery.ajax({
                url: api + "crear_plan_accion_pc.php",
                type: 'post',
                data: {
                    id: id,
                    id_user: <?php echo $_SESSION["id_user"]; ?>,
                    id_empresa: <?php echo $_SESSION["id_empresa"]; ?>,
                    id_area: <?php echo $_SESSION["area"]; ?>,
                    nombre_pa: '<?php echo $etiquetaOkrPA; ?>'

                },
            }).done(function(resp) {
                $("#modal_okr").modal("show");
                $("#modal_contenido").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    var activar = false;

    function EliminarComentarioIniciativa(id, id_empresa, id_empleado) {
        var confirmado = $("#modal_body").data("confirmado") || false;

        if (!confirmado) {
            $("#modal_okr").modal("hide");
            $("#modal_general").modal("show");
            $("#modal_body").html(
                'Está a punto de eliminar este comentario. ESTA ACCIÓN ES IRREVERSIBLE. Se perderán los datos asociados. ¿Está seguro?<br><br>'
            );
            $("#modal_body").append(
                '<button type="button" class="btn btn-danger btn-sm" id="confirmar_eliminar_comentario_iniciativa">Eliminar</button>'
            );

            $("#modal_body").data("confirmado", false);

            $("#confirmar_eliminar_comentario_iniciativa").off("click").on("click", function() {
                $("#modal_body").data("confirmado", true);
                EliminarComentarioIniciativa(id, id_empresa, id_empleado);
            });
        } else {
            jQuery.ajax({
                url: api + "eliminar_comentario_iniciativa.php",
                type: "post",
                data: {
                    id: id,
                    id_empresa: id_empresa,
                    id_empleado: id_empleado
                },
                success: function(response) {
                    var res = JSON.parse(response);

                    if (res.success) {
                        toastr.success("Comentario eliminado con éxito", "¡Éxito!");
                        $('#mis_iniciativas').DataTable().ajax.reload();
                    } else {
                        toastr.error(res.message);
                    }
                    $("#modal_general").modal("hide");
                },
                error: function(xhr, status, error) {
                    toastr.error("Hubo un problema al eliminar el comentario. Intente nuevamente.");
                }
            });

            $("#modal_body").data("confirmado", false);
        }
    }

    function Eliminar_DocumentoIniciativa(id) {
        var confirmado = $("#modal_body").data("confirmado") || false;

        if (!confirmado) {
            $("#modal_okr").modal("hide");
            $("#modal_general").modal("show");
            $("#modal_body").html(
                'Está a punto de eliminar este documento. ESTA ACCIÓN ES IRREVERSIBLE. Se perderán los datos. ¿Está seguro?<br><br>'
            );
            $("#modal_body").append(
                '<button type="button" class="btn btn-danger btn-sm" id="confirmar_eliminar_documento_iniciativa">Eliminar</button>'
            );

            $("#modal_body").data("confirmado", false);

            $("#confirmar_eliminar_documento_iniciativa").off("click").on("click", function() {
                $("#modal_body").data("confirmado", true);
                Eliminar_DocumentoIniciativa(id);
            });
        } else {
            jQuery.ajax({
                url: api + "eliminar_documento.php",
                type: "post",
                data: {
                    id: id
                },
                success: function(response) {
                    toastr.success("Documento eliminado con éxito", "¡Éxito!");
                    $('#mis_iniciativas').DataTable().ajax.reload();
                    $("#modal_general").modal("hide");
                },
                error: function(xhr, status, error) {
                    toastr.error("Hubo un problema al eliminar el documento. Intente nuevamente.");
                }
            });

            $("#modal_body").data("confirmado", false);
        }
    }

    function EliminarIniciativa(id, id_empresa, id_empleado) {
        var confirmado = $("#modal_body").data("confirmado") || false;

        if (!confirmado) {
            $("#modal_okr").modal("hide");
            $("#modal_general").modal("show");
            $("#modal_body").html(
                'Está a punto de eliminar esta iniciativa. ESTA ACCIÓN ES IRREVERSIBLE. Se perderán los datos, los planes de acción, comentarios y documentos asociados. ¿Está seguro?<br><br>'
            );
            $("#modal_body").append(
                '<button type="button" class="btn btn-danger btn-sm" id="confirmar_eliminar_iniciativa">Eliminar</button>'
            );

            $("#modal_body").data("confirmado", false);

            $("#confirmar_eliminar_iniciativa").off("click").on("click", function() {
                $("#modal_body").data("confirmado", true);
                EliminarIniciativa(id, id_empresa, id_empleado);
            });
        } else {
            jQuery.ajax({
                url: api + "elimina_iniciativa.php",
                type: "post",
                data: {
                    id: id,
                    id_empresa: id_empresa,
                    id_empleado: id_empleado
                },
                success: function(response) {
                    var res = JSON.parse(response);

                    if (res.success) {
                        toastr.success("Iniciativa eliminada correctamente", "¡Éxito!");
                        $('#mis_iniciativas').DataTable().ajax.reload();
                    } else {
                        toastr.error(res.message);
                    }
                    $("#modal_general").modal("hide");
                },
                error: function(xhr, status, error) {
                    toastr.error("Hubo un problema al eliminar la iniciativa. Intente nuevamente.");
                }
            });

            $("#modal_body").data("confirmado", false);
        }
    }

    function Ver_Documentos_Ini(id, tipo, id_user, editar, borrar, id_empresa) {
        jQuery.ajax({
                url: api + "cargar_documento_iniciativa.php",
                type: 'post',
                data: {
                    id: id,
                    tipo: tipo,
                    id_user: id_user,
                    id_empresa: id_empresa,
                    editar: editar,
                    borrar: borrar,
                    url: '<?php echo $url; ?>'
                },
            }).done(function(resp) {
                $("#modal_okr").modal("show");
                $("#modal_contenido").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function Ver_Comentarios_Iniciativa(id, id_user, editar, borrar) {
        jQuery.ajax({
                url: api + "ver_comentarios_iniciativa.php",
                type: 'post',
                data: {
                    id: id,
                    id_user: id_user,
                    editar: editar,
                    borrar: borrar,
                    id_empresa: <?php echo $_SESSION["id_empresa"]; ?>
                },
            }).done(function(resp) {
                $("#modal_okr").modal("show");
                $("#modal_contenido").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function LimpiarForm() {
        $("#formulario_filtro")[0].reset();
        $("#formulario_filtro").submit();
    }

    $(document).ready(function() {
        $('#mis_iniciativas').DataTable({
            destroy: true,
            ajax: {
                url: api + "mis_iniciativas.php",
                type: "POST",
                data: function(d) {
                    d.id_empresa = '<?php echo $_SESSION["id_empresa"]; ?>';
                    d.id_user = '<?php echo $_SESSION["id_user"]; ?>';
                    d.url = '<?php echo $url; ?>';
                    d.filtro = "<?php echo $filtro; ?>";
                    d.filtro_area = "<?php echo $filtro_area; ?>";
                    d.filtro_kr = "<?php echo $filtro_kr; ?>";
                    d.filtro_claves = "<?php echo $filtro_claves; ?>";
                    d.filtro_Vr = "<?php echo $filtro_Vr; ?>";
                    d.anio_fill = '<?php echo $_SESSION["anio_fill"]; ?>';
                    d.anio_curso = '<?php echo $dtEmpresa["anio_curso"]; ?>';
                    d.idResultado = '<?php echo $dataResultados["id"]; ?>';
                    d.rolePlataforma = '<?php echo $_SESSION["role_plataforma"]; ?>';
                    d.permisoSegIniRes = '<?php echo $permisoSegIniRes; ?>';
                    d.permisoComentarioIniRes = '<?php echo $permisoComentarioIniRes; ?>';
                    d.permisoDocIniRes = '<?php echo $permisoDocIniRes; ?>';
                    d.permisoPAIniRes = '<?php echo $permisoPAIniRes; ?>';
                    d.permisoEditIniRes = '<?php echo $permisoEditIniRes; ?>';
                    d.id_owner = '<?php echo $OKRS["id_owner"]; ?>';
                    d.tipo_role = '<?php echo $OKRS["tipo_role"]; ?>';
                    d.congelarOkrs = '<?php echo $congelar_okrs; ?>';
                    d.validarEdit = '<?php echo $validar_edit; ?>';
                    d.id_okrs = '<?php echo $OKRS["id"]; ?>';
                    d.tendendia_resultado = '<?php echo $dataResultados["tendencia"]; ?>';
                    // console.log(d);
                },
                dataSrc: 'data'
            },
            columns: [{
                    className: 'dt-control',
                    orderable: false,
                    data: null,
                    defaultContent: '',
                    width: '5%'
                },
                {
                    data: "descripcion",
                    width: '50%'
                },
                {
                    data: "nombre_mes",
                },
                {
                    data: "fecha_entrega",
                },
                {
                    data: "meta",
                },
                {
                    data: "seguimiento",
                    render: function(data, type, row) {
                        return data;
                    },
                    width: '10%'
                },
                {
                    data: "barra_avance",
                    render: function(data, type, row) {
                        return data;
                    },
                    width: '15%'
                },
                {
                    className: 'dt-right',
                    data: "acciones",
                    render: function(data, type, row) {
                        return data;
                    },
                    align: 'end'
                }
            ],
            columnDefs: [{
                    targets: 0,
                    visible: true
                },
                {
                    targets: -1,
                    createdCell: function(td) {
                        $(td).css('text-align', 'right');
                    }
                }
            ],
            autoWidth: false,
            responsive: true,
            info: true,
            ordering: true,
            paging: false,
            searching: true,
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
                emptyTable: "Ninguna iniciativa",
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
            }
        });

        $('#mis_iniciativas tbody').on('click', 'td.dt-control', function() {
            var table = $('#mis_iniciativas').DataTable();
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
            } else {
                var data = row.data();
                var extraInfo = `
            <div class="p-2 bg-light">
                <strong><b><?php echo $etiquetaOkr; ?>:</b></strong> ${data.okr} <br>
                <strong><b><?php echo $etiquetaOkrRC; ?>:</b></strong> ${data.resultado}
            </div>
        `;
                row.child(extraInfo).show();
                tr.addClass('shown');
            }
        });

    });

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


    function Editar_Documento_Iniciativa(id) {
        jQuery.ajax({
                url: api + "editar_documento_iniciativa.php",
                type: 'post',
                data: {
                    id: id,
                    id_user: <?php echo $_SESSION["id_user"]; ?>
                },
            }).done(function(resp) {
                $("#modal_okr").modal("show");
                $("#modal_contenido").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function Eliminar_Documento(id) {
        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar este documento. ESTA ACCIÓN ES IRREVERSIBLE. se perderán los datos. ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Eliminar_Documento(' + id + ')"> Eliminar </button>');

            $("#modal_okr").modal("hide");
        } else {
            jQuery.ajax({
                    url: api + "eliminar_documento.php",
                    type: 'post',
                    data: {
                        id: id,
                        url: "?pg=okrs_equipos/mis_iniciativas"
                    },
                }).done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {

                });
        }
    }

    function CopiarIniciativa(id, empresa, anio) {

        jQuery.ajax({
                url: api + "copiar_mi_iniciativa.php",
                type: 'post',
                data: {
                    id: id,
                    id_empresa: empresa,
                    id_user: <?php echo $_SESSION["id_user"]; ?>,
                    anio: anio,
                    id_area: <?php echo $_SESSION["area"]; ?>
                },
            }).done(function(resp) {
                $("#modal_okr").modal("show");
                $("#modal_contenido").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }
</script>