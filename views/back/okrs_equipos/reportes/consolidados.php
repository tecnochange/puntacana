<script>
    // $("#bt_okrs_consolidados").addClass("active_item");
    // $("#nav_reportes").addClass("menu-is-opening menu-open");
    $(".menu_section").addClass("active");
    $("#nav_reportes").addClass("active");
    jQuery("#menu_reportes").css("display", "none");
    $("#bt_okrs_consolidados").addClass("current-page");
</script>

<!-- <link rel="stylesheet" href="style.css"> -->

<?php
global $connect_valentina;
global $connect_okrs;

include("views/okrs_equipos/functions.php");
include("views/okrs/layouts/modal_profile.php");

if ($_POST["anio_fill"] != "") {
    $_SESSION["anio_fill"] = $_POST["anio_fill"];
}
if ($_POST["anio_fill"] == -1) {
    $_SESSION["anio_fill"] = "";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['periodo'])) {
        $_SESSION['selected_periodo_okr'] = $_POST['periodo'];  // Guardar checkboxes seleccionados en la sesión
    } else {
        // Si no se seleccionan checkboxes, eliminar la sesión para checkboxes
        unset($_SESSION['selected_periodo_okr']);
    }
}

$filtro = "";

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


$filtros = $filtro != "" ? $filtro : "Q";

$_SESSION["periodo_fill"] = $filtro != "" ? $filtro : "";

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


$resultado_total = 0;
$conteo_total = 0;
$conteo_okr = 0;

if ($filtro_periodo) {
    $filtro_claves .= "AND periodo IN (" . $filtro_periodo . ") ";
}
include("views/okrs_equipos/etiquetas.php");
if ($_SESSION["id_empresa"] == 1) {
    $queryAuto = mysqli_query($connect_competencias_pc, "SELECT * FROM Evaluadores
    WHERE anio = '" . $_SESSION['anio_ciclo'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND id_empleado = '" . $_SESSION['id_user'] . "' AND tipo = 1 ");
    $queryJefe = mysqli_query($connect_competencias_pc, "SELECT * FROM Evaluadores
    WHERE anio = '" . $_SESSION['anio_ciclo'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND id_empleado = '" . $_SESSION['id_user'] . "' AND tipo != 1 ");

    $dataJefe = mysqli_fetch_array($queryJefe);
    if ($ciclo_cerrado == false) {
        if (mysqli_num_rows($queryAuto) > 0 || mysqli_num_rows($queryJefe) > 0) {
            $queryEval1 = mysqli_query($connect_competencias_pc, "SELECT * FROM Competencias_Evaluaciones_New
            WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'
            AND id_ciclo = '" . $_SESSION['ciclo'] . "'
            AND id_evaluado = '" . $_SESSION['id_user'] . "'
            AND id_evaluador = '" . $_SESSION['id_user'] . "'
            AND anio = '" . $_SESSION["anio_ciclo"] . "'
            AND estado != 3 AND (proceso_valoracion != 7 OR proceso_valoracion IS NULL)");
            $queryEval2 = mysqli_query($connect_competencias_pc, "SELECT * FROM Competencias_Evaluaciones_New
            WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'
            AND id_ciclo = '" . $_SESSION['ciclo'] . "'
            AND id_evaluado = '" . $_SESSION['id_user'] . "'
            AND id_evaluador = '" . $dataJefe['id_evaluador'] . "'
            AND anio = '" . $_SESSION["anio_ciclo"] . "'
            AND estado != 3 AND (proceso_valoracion != 7 OR proceso_valoracion IS NULL)");
            if (mysqli_num_rows($queryEval1) >= 0 || mysqli_num_rows($queryEval2) >= 0) {
                $dataEval1 = mysqli_fetch_array($queryEval1);
                $dataEval2 = mysqli_fetch_array($queryEval2);


?>
                <!-- Modal de Aviso -->
                <!-- <div class="modal fade" id="modalAviso" tabindex="-1" aria-labelledby="modalAvisoLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">

                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="contenidoAviso">
                                <?php //include("views/competencias_pc/notificacion_valoracion.php"); ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div> -->

<?php
            }
        }
    }
}
?>
<!-- Modal de Aviso -->
<div class="modal fade" id="modalAviso" tabindex="-1" aria-labelledby="modalAvisoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="contenidoAviso">
                <?php include("views/competencias_pc/notificacion_valoracion.php"); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="<?php echo $url; ?>css/okrs.css">
<link rel="stylesheet" href="<?php echo $url; ?>css/consolidado.min.css">
<style>
    .circleSpan {
        display: inline-block;
        width: 25px;
        height: 25px;
        background-color: #ffc107;
        text-align: center;
        line-height: 25px;
        border-radius: 50%;
        font-size: 12px;
    }
</style>
<!-- OKRS EQUIPOS -->
<!-- OKRS EQUIPOS -->
<!-- OKRS EQUIPOS -->
<div class="container-fluid">
    <div class="row" style="text-align:center;">
        <div class="col-md-12">
            <h3>DESEMPEÑO Y AVANCES ESTRATÉGICO DE <?php echo mb_strtoupper($_SESSION['nombre_empresa']); ?></h3>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <?php include("views/okrs_equipos/reportes/filtro_consolidados.php"); ?>
                </div>
            </div>
        </div>
    </div>

    <br>

    <?php
    echo '<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">';
    include("views/okrs_equipos/reportes/escala.php");
    echo "</div>
        </div>
    </div>
</div><br>";
    include("objetivos_desempenio.php"); ?>
    <br>
    <?php
    $filtro_vr = null;
    if ($filtro) {
        $filtro_vr = "," . $filtro;
    }
    include("resumen_obj_estrategico.php");
    include("resumen_tablas.php");
    include("resumen_consolidado.php");
    echo '<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">';
    include("views/okrs_equipos/reportes/escala.php");
    echo "</div>
        </div>
    </div>
</div><br>";
    ?>

    <br>


    <?php
    $queryEstrategicos = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND anio = " . $_SESSION["anio_fill"] . "");

    $cont = 0;
    $count_anios = array();
    foreach ($Array_Anio as $value) {
        $queryAnio = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = " . $value[1] . "");
        $count_anios[$cont]["anio"] = $value[1];
        $count_anios[$cont]["porcentaje"] = round(100 / (mysqli_num_rows($queryAnio)), 2);
        $cont++;
    }
    if (!isset($data["ponderacion"])) {
        foreach ($count_anios as $periodo) {
            if ($data["anio"] == $periodo["anio"]) {
                mysqli_query($connect_okrs, "UPDATE Objetivos_estrategicos SET ponderacion = " . $periodo["porcentaje"] . " WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = " . $data["anio"] . "");
                $ponderacion = $periodo["porcentaje"];
            }
        }
    } else {
        $ponderacion = $data["ponderacion"];
    }
    ?>
    <style>
        .card,
        .card-header,
        .card-body,
        .card-footer {
            background-color: white !important;
        }

        .card-header,
        .card-body,
        .card-footer {
            border-top: none;
            border-bottom: none;
        }

        .nav-tabs .nav-item.show .nav-link,
        .nav-tabs .nav-link.active {
            background-color: #007BFF !important;
            color: white !important;
        }
    </style>


</div>

<script>
    var api = '<?php echo $url; ?>api/okrs/';

    function VerOKROrganizacional(id, id_owner, anioFill, empresa, filtro1, filtro2, filtro3, filtro4, filtro5) {
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

        window.open("<?php echo $url; ?>views_okrs/okr_organizacional.php?id=" + id + "&id_owner=" + id_owner + "&anio_fill=" + anioFill + "&id_empresa=" + empresa + "&filtro1=" + filtro1 + "&filtro2=" + filtro2 + "&filtro3=" + filtro3 + "&filtro4=" + filtro4 + "&filtro5=" + filtro5, "GoForAgile", "width=1300, height=900")
    }

    function VerConsolidadoOKRS(id, id_owner, anioFill, empresa, filtro1, filtro2, filtro3, filtro4, filtro5) {
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
        var anio = <?php echo $_SESSION["anio_fill"]; ?>;
        var empresa = <?php echo $dtEmpleado['id_empresa']; ?>;
        window.open("<?php echo $url; ?>views_okrs/consolidado_organizacionales.php?id=" + id + "&id_owner=" + id_owner + "&anio_fill=" + anioFill + "&id_empresa=" + empresa + "&filtro1=" + filtro1 + "&filtro2=" + filtro2 + "&filtro3=" + filtro3 + "&filtro4=" + filtro4 + "&filtro5=" + filtro5, "GoForAgile", "width=1300, height=900")
    }

    function Editar_Resultado(id) {
        jQuery.ajax({
                url: api + "editar_resultado.php",
                type: 'post',
                data: {
                    id: id
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

    function Guardar_Avance_Iniciativa(avance, id) {
        data = {
            id: id,
            avance: avance,
            id_realiza: <?php echo $dtEmpleado["id"]; ?>
        };
        jQuery.ajax({
                url: api + "guardar_avance_iniciativa.php",
                type: 'post',
                data: data,
            }).done(function(resp) {
                $("#xscript").html(resp);
                location.reload();
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function Guardar_Avance_Resultado(avance, id) {
        data = {
            id: id,
            avance: avance,
        };
        jQuery.ajax({
                url: api + "guardar_avance_resultado.php",
                type: 'post',
                data: data,
            }).done(function(resp) {
                $("#xscript").html(resp);
                //window.location = "?pg=okrs_equipos/home#"
                location.reload();
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function Filtrar() {
        $("#formulario_filtro").submit();
    }

    function Profile(id, val) {
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
                console.log(resp);
            })
            .always(function(resp) {});
    }
</script>
<script>
    function miAlerta(evento) {
        console.log(evento);
        event.preventDefault();
        if (document.getElementById().val() == 'card_1') {
            // alert('prueba');
        }
    }
</script>
<?php if ($_SESSION["id_empresa"] == 1) { ?>
    <script>
        $(document).ready(function() {
            $('#modalAviso').modal('show');
        });
    </script>
<?php } ?>