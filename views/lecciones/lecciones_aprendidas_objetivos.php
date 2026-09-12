<script>
$(document).ready(function() {
    $('#menuLecciones').collapse();
    $('#bt_lecciones_objetivos_estrategicos').addClass('active');
});
</script>


<link rel="stylesheet" href="<?php echo $url; ?>css/lecciones.css">
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<style>


</style>

<?php
include("views/lecciones_aprendidas/modal_comentario.php");
include("views/lecciones_aprendidas/modal_general.php");
include("views/okrs/layouts/modal_profile.php");
date_default_timezone_set('America/Bogota');
$tipo = $_GET["tp"];
$hoy = date("Y-m-d H:i:s");
$ahora = date("Y-m-d");
$all = $_GET["all"];
$area = "";
$estrategico = $organizacional = $areaL = $equipo = 0;

$limit = 20;
if ($all) {
    $limit = 100;
}


if (is_numeric($_SESSION['area']) || $_SESSION['area'] > 0) {
    $area = $_SESSION['area'];
} else {

    $queryAreas = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND nombre = '" . $_SESSION["area"] . "'");
    while ($areas = mysqli_fetch_array($queryAreas)) {
        $area = $areas["id"];
    }
}


if ($_POST["actualizar_comentario"] != "") {
    $comentario = str_replace('<br>', '', $_POST["comentario"]);
    mysqli_query($connect_clima, "UPDATE Comentarios_Lecciones SET comentario = '" . $comentario . "', update_at = '" . $hoy . "' WHERE id = " . $_POST["comentario_upd"] . "");

    echo '<script>
	document.addEventListener("DOMContentLoaded", function() {
		var divElement = document.getElementById("leccion_' . $_POST["id_leccion"] . '");
        var divComentario = document.getElementById("comentario_L_' . $$_POST["comentario_upd"] . '");
		
		if (divElement) {
			divElement.scrollIntoView();
            $("#boton_accordion_' . $_POST["id_leccion"] . '").removeClass("collapse");
            $("#collapse_L_' . $_POST["id_leccion"] . '").addClass("show"); }
            if (divComentario) {
                divComentario.scrollIntoView();
            }
		}
	});
</script>';
}

//PARA MOSTRAR LOS TIPO
$filtro = $filtro_kr = $filtro_area = $filtro_VP = $filtro_estrategico = $filtro_organizacional = $filtro_la = "";
$filtro_la = "area = $area";

$filtro_estrategico = " AND id_okr_estrategico IS NULL OR id_okr_estrategico = ''";
$filtro_organizacional = " AND id_okr_organizacional IS NULL OR id_okr_organizacional = '' AND 	id_okr_equipo IS NULL OR id_okr_equipo = ''";

if ($_POST["anio_fill"] != "") {
    $_SESSION["anio_fill"] = $_POST["anio_fill"];
}
if ($_POST["anio_fill"] == -1) {
    $_SESSION["anio_fill"] = "";
}

if ($_POST["leccion_fill"] != "") {
    $_SESSION["leccion_fill"] = $_POST["leccion_fill"];
    $filtro_kr .= "AND id = '" . $_POST["leccion_fill"] . "'";
}
if ($_POST["leccion_fill"] == "") {
    $_SESSION["leccion_fill"] = "";
    $filtro_kr .= "";
}

if ($_POST["estrategico_fill"] != "") {
    $_SESSION["estrategico_fill"] = $_POST["estrategico_fill"];
    $filtro_kr .= "AND id_okr_estrategico = '" . $_POST["estrategico_fill"] . "'";
}
if ($_POST["estrategico_fill"] == "") {
    $_SESSION["estrategico_fill"] = "";
    $filtro_kr .= "";
}

if ($_POST["organizacional_fill"] != "") {
    $_SESSION["organizacional_fill"] = $_POST["organizacional_fill"];
    $filtro_kr .= "AND id_okr_organizacional = '" . $_POST["organizacional_fill"] . "'";
}
if ($_POST["organizacional_fill"] == "") {
    $_SESSION["organizacional_fill"] = "";
    $filtro_kr .= "";
}

if ($_POST["area_lA"] != "") {
    $_SESSION["area_lA"] = $_POST["area_lA"];
    $filtro_area .= "AND area = '" . $_POST["area_lA"] . "'";
    $filtro_la = "";
}
if ($_POST["area_lA"] == "") {
    $_SESSION["area_lA"] = "";
    $filtro_area .= "";
}

if ($_POST["vp_lA"] != "") {
    $_SESSION["vp_lA"] = $_POST["vp_lA"];
    $filtro_vp .= "AND id_vp = '" . $_POST["vp_lA"] . "'";
    $filtro_la = "";
}
if ($_POST["vp_lA"] == "") {
    $_SESSION["vp_lA"] = "";
    $filtro_vp .= "";
}

$check1 = $_POST["Q1"] != "" ? "checked" : "";
$check2 = $_POST["Q2"] != "" ? "checked" : "";
$check3 = $_POST["Q3"] != "" ? "checked" : "";
$check4 = $_POST["Q4"] != "" ? "checked" : "";
$check5 = $_POST["Anual"] != "" ? "checked" : "";

$contFiltro = 0;
$contQ = 0;

if ($_POST["Q1"] != "") {
    if ($filtro != "") {
        $filtro .= ", 'Q1'";
        $contFiltro++;
    } else {
        $filtro .= "'Q1'";
        if ($contQ == 0) {
            $contQ = 1;
        }
    }
}
if ($_POST["Q2"] != "") {
    if ($filtro != "") {
        $filtro .= ", 'Q2'";
        $contFiltro++;
    } else {
        $filtro .= "'Q2'";
        if ($contQ == 0) {
            $contQ = 2;
        }
    }
}
if ($_POST["Q3"] != "") {
    if ($filtro != "") {
        $filtro .= ", 'Q3'";
        $contFiltro++;
    } else {
        $filtro .= "'Q3'";
        if ($contQ == 0) {
            $contQ = 3;
        }
    }
}
if ($_POST["Q4"] != "") {
    if ($filtro != "") {
        $filtro .= ", 'Q4'";
        $contFiltro++;
    } else {
        $filtro .= "'Q4'";
        if ($contQ == 0) {
            $contQ = 4;
        }
    }
}
if ($_POST["Anual"] != "") {
    if ($filtro != "") {
        $filtro .= ", 'Anual'";
        $contFiltro++;
    } else {
        $filtro .= "'Anual'";
        if ($contQ == 0) {
            $contQ = 5;
        }
    }
}

if ($contFiltro > 0) {
    $contQ = 6;
}

$filtros = $filtro != "" ? $filtro : "Q";

$valorPorcentaje = 0;

switch ($contFiltro) {
    case 1:
        $valorPorcentaje = 50;
        break;
    case 2:
        $valorPorcentaje = 75;
        break;
    case 3:
        $valorPorcentaje = 100;
        break;
    case 4:
        $valorPorcentaje = 100;
        break;
    case 5:
        $valorPorcentaje = 100;
        break;
}


global $connect_admin;
global $connect_okrs;

include("views/okrs_equipos/functions.php");

$resultado_total = 0;
$conteo_total = 0;
$conteo_okr = 0;

$resultado_global = 0;
$contador_global = 0;
$filtro_vr = null;
$filtro_claves = "";
$filtroP = "";
if ($filtro) {
    $filtro_kr .= " AND anio = '" . $_SESSION["anio_fill"] . "' ";
    $filtro_claves = "AND periodo IN (" . $filtro . ") ";
} else {
    $filtro_kr .= " AND anio = '" . $_SESSION["anio_fill"] . "'";
}
// $filtro_kr .= " AND area = '$area'";

$filtro_vr = null;
if ($filtro) {
    $filtro_vr = "," . $filtro;
}

// $queryLecciones = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas");
// while ($dataL = mysqli_fetch_array($queryLecciones)) {
//     if ($dataL["celula"] == null) {
//         $queryP = mysqli_query($connect_clima, "SELECT * FROM Celula_Lecciones WHERE id_leccion = '" . $dataL["id"] . "'");
//         if ($queryP) {
//             while ($dataP = mysqli_fetch_array($queryP)) {
//                 $filtroP .= $dataP["id_empleado"] . ",";
//             }
//             $filtroP = substr($filtroP, 0, -1);
//             mysqli_query($connect_clima, "UPDATE Lecciones_Aprendidas SET celula = '$filtroP' WHERE id = '" . $dataL["id"] . "'  ");
//         }
//     }
// }
if ($_SESSION["role_plataforma"] == 1) {
    $queryAreaLecciones = mysqli_query($connect_clima, "SELECT DISTINCT(LA.area) AS area FROM Lecciones_Aprendidas AS LA INNER JOIN puntacana_admin.Areas AS Areas ON Areas.id = LA.area WHERE LA.id_empresa = '" . $_SESSION["id_empresa"] . "' AND LA.estado = 1 AND LA.anio = '" . $_SESSION["anio_fill"] . "' ORDER BY Areas.nombre");
    $queryVPLecciones = mysqli_query($connect_clima, "SELECT DISTINCT(LA.id_vp) AS id_vp FROM Lecciones_Aprendidas AS LA INNER JOIN puntacana_admin.Vicepresidencia AS VP ON VP.id = LA.id_vp WHERE LA.id_empresa = '" . $_SESSION["id_empresa"] . "' AND LA.estado = 1 AND LA.anio = '" . $_SESSION["anio_fill"] . "' ORDER BY Areas.nombre");
    $queryEstrategicoLecciones = mysqli_query($connect_clima, "SELECT DISTINCT(LA.id_okr_estrategico) AS estrategico FROM Lecciones_Aprendidas AS LA INNER JOIN puntacana_okrs.Objetivos_estrategicos AS OE ON OE.id = LA.id_okr_estrategico WHERE LA.id_empresa = '" . $_SESSION["id_empresa"] . "' AND LA.estado = 1 AND LA.anio = '" . $_SESSION["anio_fill"] . "' ORDER BY OE.objetivo");
    $queryOrganizacionalLecciones = mysqli_query($connect_clima, "SELECT DISTINCT(LA.id_okr_organizacional) AS organizacional FROM Lecciones_Aprendidas AS LA INNER JOIN puntacana_okrs.Okrs AS O ON O.id = LA.id_okr_organizacional WHERE LA.id_empresa = '" . $_SESSION["id_empresa"] . "' AND LA.estado = 1 AND LA.anio = '" . $_SESSION["anio_fill"] . "' ORDER BY O.objetivo_okr");
} else if ($_SESSION["role_plataforma"] == 2) {
    $queryAreaLecciones = mysqli_query($connect_clima, "SELECT DISTINCT(LA.area) AS area FROM Lecciones_Aprendidas AS LA INNER JOIN puntacana_admin.Areas AS Areas ON Areas.id = LA.area WHERE LA.id_empresa = '" . $_SESSION["id_empresa"] . "' AND LA.estado = 1 AND LA.anio = '" . $_SESSION["anio_fill"] . "' AND (LA.id_empleado = " . $_SESSION['id_user'] . " OR LA.celula LIKE '%," . $_SESSION['id_user'] . "%' OR LA.celula LIKE '%" . $_SESSION['id_user'] . ",%') ORDER BY Areas.nombre");
    $queryVPLecciones = mysqli_query($connect_clima, "SELECT DISTINCT(LA.id_vp) AS id_vp FROM Lecciones_Aprendidas AS LA INNER JOIN puntacana_admin.Vicepresidencia AS VP ON VP.id = LA.id_vp WHERE LA.id_empresa = '" . $_SESSION["id_empresa"] . "' AND LA.estado = 1 AND LA.anio = '" . $_SESSION["anio_fill"] . "' AND (LA.id_empleado = " . $_SESSION['id_user'] . " OR LA.celula LIKE '%," . $_SESSION['id_user'] . "%' OR LA.celula LIKE '%" . $_SESSION['id_user'] . ",%')  ORDER BY Areas.nombre");
    $queryEstrategicoLecciones = mysqli_query($connect_clima, "SELECT DISTINCT(LA.id_okr_estrategico) AS estrategico FROM Lecciones_Aprendidas AS LA INNER JOIN puntacana_okrs.Objetivos_estrategicos AS OE ON OE.id = LA.id_okr_estrategico WHERE LA.id_empresa = '" . $_SESSION["id_empresa"] . "' AND LA.estado = 1 AND LA.anio = '" . $_SESSION["anio_fill"] . "' AND (LA.id_empleado = " . $_SESSION['id_user'] . " OR LA.celula LIKE '%," . $_SESSION['id_user'] . "%' OR LA.celula LIKE '%" . $_SESSION['id_user'] . ",%')  ORDER BY OE.objetivo");
    $queryOrganizacionalLecciones = mysqli_query($connect_clima, "SELECT DISTINCT(LA.id_okr_organizacional) AS organizacional FROM Lecciones_Aprendidas AS LA INNER JOIN puntacana_okrs.Okrs AS O ON O.id = LA.id_okr_organizacional WHERE LA.id_empresa = '" . $_SESSION["id_empresa"] . "' AND LA.estado = 1 AND LA.anio = '" . $_SESSION["anio_fill"] . "' AND (LA.id_empleado = " . $_SESSION['id_user'] . " OR LA.celula LIKE '%," . $_SESSION['id_user'] . "%' OR LA.celula LIKE '%" . $_SESSION['id_user'] . ",%')  ORDER BY O.objetivo_okr");
} else {
    $queryAreaLecciones = mysqli_query($connect_clima, "SELECT DISTINCT(LA.area) AS area FROM Lecciones_Aprendidas AS LA INNER JOIN puntacana_admin.Areas AS Areas ON Areas.id = LA.area WHERE LA.id_empresa = '" . $_SESSION["id_empresa"] . "' AND LA.estado = 1 AND LA.anio = '" . $_SESSION["anio_fill"] . "' AND (LA.celula LIKE '%," . $_SESSION['id_user'] . "%' OR LA.celula LIKE '%" . $_SESSION['id_user'] . ",%') ORDER BY Areas.nombre");
    $queryVPLecciones = mysqli_query($connect_clima, "SELECT DISTINCT(LA.id_vp) AS id_vp FROM Lecciones_Aprendidas AS LA INNER JOIN puntacana_admin.Vicepresidencia AS VP ON VP.id = LA.id_vp WHERE LA.id_empresa = '" . $_SESSION["id_empresa"] . "' AND LA.estado = 1 AND LA.anio = '" . $_SESSION["anio_fill"] . "' AND (LA.celula LIKE '%," . $_SESSION['id_user'] . "%' OR LA.celula LIKE '%" . $_SESSION['id_user'] . ",%')  ORDER BY Areas.nombre");
    $queryEstrategicoLecciones = mysqli_query($connect_clima, "SELECT DISTINCT(LA.id_okr_estrategico) AS estrategico FROM Lecciones_Aprendidas AS LA INNER JOIN puntacana_okrs.Objetivos_estrategicos AS OE ON OE.id = LA.id_okr_estrategico WHERE LA.id_empresa = '" . $_SESSION["id_empresa"] . "' AND LA.estado = 1 AND LA.anio = '" . $_SESSION["anio_fill"] . "' AND (LA.celula LIKE '%," . $_SESSION['id_user'] . "%' OR LA.celula LIKE '%" . $_SESSION['id_user'] . ",%')  ORDER BY OE.objetivo");
    $queryOrganizacionalLecciones = mysqli_query($connect_clima, "SELECT DISTINCT(LA.id_okr_organizacional) AS organizacional FROM Lecciones_Aprendidas AS LA INNER JOIN puntacana_okrs.Okrs AS O ON O.id = LA.id_okr_organizacional WHERE LA.id_empresa = '" . $_SESSION["id_empresa"] . "' AND LA.estado = 1 AND LA.anio = '" . $_SESSION["anio_fill"] . "' AND (LA.celula LIKE '%," . $_SESSION['id_user'] . "%' OR LA.celula LIKE '%" . $_SESSION['id_user'] . ",%')  ORDER BY O.objetivo_okr");
}


if ($_SESSION["role_plataforma"] == 1) {
    $resultado = CargaLeccionesAdminObjEst($connect_clima, $connect_admin, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp);
} else if ($_SESSION["role_plataforma"] == 2) {
    $resultado = CargaLeccionesLiderObjEst($connect_clima, $connect_admin, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp, $_SESSION["id_user"]);
} else {
    $resultado = CargaLeccionesColaboradorObjEst($connect_clima, $connect_admin, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp, $_SESSION["id_user"]);
}

// print_r($resultado);

$queryLA = unique_multidim_array($resultado, 'id');

?>



<?php
function replaceLinks($s)
{
    return preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.%-=#]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>', $s);
}


function ConvertirLink($entrada)
{
    $url = '@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
    $string = preg_replace($url, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $entrada);
    return $string;
}

$querySM72 = mysqli_query($connect_admin, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 7 AND id_submenu = 42");
$dataSM72 = mysqli_fetch_array($querySM72);
?>
<link rel="stylesheet" href="<?php echo $url; ?>css/okrs.css">
<link rel="stylesheet" href="<?php echo $url; ?>views/okrs_equipos/reportes/styles.css">

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h3><?php echo $dataSM72["nombre"]; ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <?php include("views/lecciones_aprendidas/detalle/filtros_obj_estrategico.php"); ?>
    <?php

    if (empty($resultado)) {
    ?>
        <div class="row">
            <div class="col-md-12" style="margin-top: 20px">
                <div class="alert alert-warning" role="alert" style="opacity: 0.9 !important;text-align:center;color:black;">
                    No tiene lecciones aprendidas asignadas
                </div>
            </div>
        </div>
    <?php
    }
    ?>
    <br>
    <div class="row">

    <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table border="1" id="lecciones_aprendidas" class="display table" style="width:100%">
                            <thead>
                                <th>Nombre Objetivo</th>
                                <th>Líder</th>
                                <th>Lecciones Asignadas</th>
                                <th>Acciones</th>
                            </thead>
                            <tbody>
                                <?php
                                $queryVP = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'  AND estado = 1 AND anio = ".$_SESSION["anio_fill"]." $filtro_VP $areasMacro ORDER BY objetivo");
                                $contador = 0;
                                while ($dataVP = mysqli_fetch_array($queryVP)) {
                                    $nombre_lider = '';

                                    $queryLideres = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id = '" . $dataVP["id"] . "'");

                                    if (mysqli_num_rows($queryLideres) > 0) {
                                        while ($dataLideres = mysqli_fetch_array($queryLideres)) {
                                            $queryEmple = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $dataLideres["id_responsable"] . "' ");
                                            $dataEmple = mysqli_fetch_array($queryEmple);

                                            if (!$dataEmple["foto"]) {
                                                $dataEmple["foto"] = "img_default.jpg";
                                            }
                                            $nombre_lider .= '<a data-bs-toggle="tooltip" href="javascript:Profile(' . $dataLideres["id_lider"] . ',1)" class="dropdown-item" id="profileOkr"><img src="' . $url . '/recursos/' . $dataEmple["foto"] . '" class="lazyload foto_min" title="' . $dataEmple["nombre"] . '" style="width: 35px !important;height: 35px !important;"></a>';
                                        }
                                    } else {
                                        $nombre_lider = 'Sin Asignar';
                                    }

                                    $id_area_values = array_column($resultado, 'id_okr_estrategico');

                                    $id_area_count = array_count_values($id_area_values);

                                    if (isset($id_area_count[$dataVP["id"]])) {
                                        $contador = $id_area_count[$dataVP["id"]];
                                    } else {
                                        $contador = 0;
                                    }
                                    $acciones = '<button type="button" class="btn btn-primary btn-sm bt_editar" onClick="VerCelula(' . $dataVP["id"] . ','.$_SESSION["id_user"].')" data-bs-toggle="tooltip" title="Vista rápida célula">
                                                                    <i class="bx bx-group"></i>
                                                                </button>
                                                                <a class="btn btn-black btn-sm bt_editar" href="' . $url . '?pg=lecciones_aprendidas/detalle/info_obj_est&id=' . $dataVP["id"] . '" title="Vista rápida del área" id="vistaAreas" style="float: right;">
																<i class="bx bx-show" style="font-size: 1.3rem;"></i>
																</a>';
                                ?>
                                    <tr>
                                        <td><?php echo $dataVP["objetivo"]; ?></td>
                                        <td><?php echo $nombre_lider; ?></td>
                                        <td align="center"><?php echo $contador ?></td>
                                        <td align="end"><?php echo $acciones ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    $(".menu_section").addClass("active");
    // $("#nav_lecciones_aprendidas").addClass("active");
    jQuery("#menu_lecciones_aprendidas").css("display", "none");
    $("#bt_la_lecciones_objetivo").addClass("current-page");
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        var divElement = document.getElementById("leccion_<?php echo $_GET["id_leccion"]; ?>");
        var divComentario = document.getElementById("comentario_L_<?php echo $_GET["id_comentario"]; ?>");

        if (divElement) {
            divElement.scrollIntoView();
            $("#boton_accordion_<?php echo $_GET["id_leccion"]; ?>").removeClass("collapse");
            $("#collapse_L_<?php echo $_GET["id_leccion"]; ?>").addClass("show");
            if (divComentario) {
                divComentario.scrollIntoView();
            }
        }
        if (divElement) divElement.scrollIntoView(false);
        if (divComentario) divComentario.scrollIntoView(false);

    });
    window.location.hash = "";
</script>

<script>
    $(document).ready(function() {

        var formulario = document.getElementById('comenta-form');

        formulario.reset();
        var url = window.location.hash;
        url = url.substr(1);

        $('#comentario_leccion').summernote({

            tabsize: 2,
            height: 250,
            minHeight: null,
            maxHeight: null,
            focus: true,

            toolbar: [
                ['para', ['ul', 'ol']]
            ],
            styleTags: [
                "p",
                "code",
                "blockquote",
                "pre",
                "h1",
                "h2",
                "h3",
                "h4",
                "h5",
                "h6",
            ],
        });

        var swiper = (typeof Swiper === "undefined") ? null : new Swiper('.swiper-container', {
            autoHeight: true, //enable auto height
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: {
                delay: 3000,
            },
        });

        $('#modal-Comentario').modal({
            backdrop: 'static',
            keyboard: false
        })
        $('#modal-EditarComentario').modal({
            backdrop: 'static',
            keyboard: false
        })

    });
</script>

<script>
    var api = "<?php echo $url; ?>api/endomarketing/";
    var api_admin = "<?php echo $url; ?>api/administrar/";
    var activar = false;


    $(function() {
        $('[data-toggle="popover"]').popover({
            trigger: 'focus',
            html: true
        });
    });

    function Comentar(val, tipo, comentario) {
        $("#leccion").val(val);
        $("#tipo").val(tipo);

    }

    function EditarComentario(id, leccion) {
        jQuery.ajax({
                url: api + "editar_comentario_leccion.php",
                type: 'post',
                data: {
                    id: id,
                    id_leccion: leccion
                },
            }).done(function(resp) {
                $("#modal-EditarComentario").modal("show");
                $("#modal_contenido").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function Filtrar_Variable(val) {
        location.href = "?pg=home&tp=<?php echo $tipo ?>" + "&var=" + val;

    }

    function Filtrar() {
        $("#formulario_filtro").submit();
    }

    function Registrar_Comentar() {

        if ($("#comentario_leccion").val() == "") {
            alert("Debes registrar un comentario");
        }
    }

    function ActualizarComentar() {

        if ($("#comentario_leccion_upd").val() == "") {
            alert("Debes registrar un comentario");
        }
    }

    function VerOKRLeccionAprendida(id, id_owner, estrategico, organizacional, equipo, id_empresa, filtro1, filtro2, filtro3, filtro4, filtro5) {
        if (!filtro1) {
            filtro1 = 0;
        }
        if (!filtro2) {
            filtro2 = 0;
        }
        if (!filtro3) {
            filtro3 = 0;
        }
        if (!filtro4) {
            filtro4 = 0;
        }
        if (!filtro5) {
            filtro5 = 0;
        }
        window.open("<?php echo $url; ?>views_okrs/okr_leccion_aprendida.php?id=" + id + "&id_owner=" + id_owner + "&estrategico=" + estrategico + "&organizacional=" + organizacional + "&equipo=" + equipo + "&empresa=" + id_empresa + "&filtro1=" + filtro1 + "&filtro2=" + filtro2 + "&filtro3=" + filtro3 + "&filtro4=" + filtro4 + "&filtro5=" + filtro5, "GoForAgile", "width=1300, height=900")
    }

    function Profile(id, val) {
        jQuery.ajax({
                url: api_admin + "profile_empleado.php",
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
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function VerCelula(id,id_user) {
		jQuery.ajax({
				url: api + "ver_celula_obj_est.php",
				type: 'post',
				data: {
					id: id,
                    id_user: id_user,
                    url_oe: "?pg=lecciones_aprendidas/detalle/administrar_participantes&id_obj_est="+id,
				},
			}).done(function(resp) {
				$("#modal_la").modal("show");
				$("#modal_contenido_la").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});


	}
</script>
<script>
    $(document).ready(function() {
        $('#lecciones_aprendidas').DataTable({
            columnDefs: [{
                    responsivePriority: 1,
                    targets: 0
                },
                {
                    responsivePriority: 2,
                    targets: -1
                }
            ],
            order: [
                [1, 'asc']
            ],
            responsive: true,
            info: false,
            paging: true,
            pageLength: 50,
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
                emptyTable: "Ningún dato disponible en esta tabla",
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

    });
</script>