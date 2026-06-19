<script>
    $(document).ready(function() {
        $('#menuCompetencias').collapse();
        $("#bt_competencias_analitica").addClass("active");
    });
</script>

<?php
include("app/models/competencias/Competencias.php");
$ClassCompetencias = new Competencias();
$dataCicloVal = $ClassCompetencias->Ciclo($user_log["id_empresa"], $_SESSION["anio_ciclo"]);

$filtros = "";
if ($_POST["fill_area"]) {
    $filtros .= " AND id_viceprecidiencia = '" . $_POST["fill_area"] . "' ";
}

$array_gerenciasFiltro = array();
$queryGerenciasFil = mysqli_query($connect_valoracion, "SELECT * FROM aa_sincronizacion 
WHERE anio = '" . $_SESSION["anio_ciclo"] . "' AND ciclo = '" . $_SESSION["ciclo"] . "' 
AND id_empresa = '" . $_SESSION["id_empresa"] . "'  GROUP BY id_viceprecidiencia  ");
while ($dataGerenciaFill = mysqli_fetch_array($queryGerenciasFil)) {
    array_push($array_gerenciasFiltro, $dataGerenciaFill["id_viceprecidiencia"]);
}

$array_viceprecidiencia = array();
$queryVicepresidencias = mysqli_query($connect_valoracion, "SELECT * FROM aa_sincronizacion 
WHERE anio = '" . $_SESSION["anio_ciclo"] . "' AND ciclo = '" . $_SESSION["ciclo"] . "' 
AND id_empresa = '" . $_SESSION["id_empresa"] . "' " . $filtros . " GROUP BY id_viceprecidiencia ");
while ($dataVicepresidencia = mysqli_fetch_array($queryVicepresidencias)) {
    array_push($array_viceprecidiencia, $dataVicepresidencia["id_viceprecidiencia"]);
}

$array_areas_l = array();
$queryAreas = mysqli_query($connect_valoracion, "SELECT * FROM aa_sincronizacion 
WHERE anio = '" . $_SESSION["anio_ciclo"] . "' AND ciclo = '" . $_SESSION["ciclo"] . "' 
AND id_empresa = '" . $_SESSION["id_empresa"] . "' " . $filtros . " GROUP BY id_area  ");
while ($dataAreas = mysqli_fetch_array($queryAreas)) {
    array_push($array_areas_l, $dataAreas["id_area"]);
}

$array_cargos = array();
$queryCargos = mysqli_query($connect_valoracion, "SELECT * FROM aa_sincronizacion 
WHERE anio = '" . $_SESSION["anio_ciclo"] . "' AND ciclo = '" . $_SESSION["ciclo"] . "' 
AND id_empresa = '" . $_SESSION["id_empresa"] . "' " . $filtros . " GROUP BY id_cargo  ");
while ($dataCargos = mysqli_fetch_array($queryCargos)) {
    array_push($array_cargos, $dataCargos["id_cargo"]);
}

$queryEvaluados = mysqli_query($connect_valoracion, "SELECT * FROM aa_sincronizacion 
WHERE anio = '" . $_SESSION["anio_ciclo"] . "' AND ciclo = '" . $_SESSION["ciclo"] . "' 
AND id_empresa = '" . $_SESSION["id_empresa"] . "' " . $filtros . " GROUP BY id_empleado  ");
$dataEvaluados = mysqli_fetch_array($queryEvaluados);

$array_evaluaciones = array();
$array_evaluaciones_general = array();
$promedio_general = 0;
$contador_total = 0;

//PARA EL DATO GENERAL // SE HACE UN ROLBACK AL DESARROLLO SEGUN SOLICITUD DEL CLIENTE EL DÍA 30 - carlos biod
$queryRegsGeneral = mysqli_query($connect_valoracion, "SELECT * FROM aa_sincronizacion 
WHERE anio = '" . $_SESSION["anio_ciclo"] . "' AND ciclo = '" . $_SESSION["ciclo"] . "' AND id_empresa = '" . $_SESSION["id_empresa"] . "' ");
while ($dataRegsGeneral = mysqli_fetch_array($queryRegsGeneral)) {
    $promedio_general += $dataRegsGeneral["promedio"];
    $contador_total++;
}

$queryRegs = mysqli_query($connect_valoracion, "SELECT * FROM aa_sincronizacion 
WHERE anio = '" . $_SESSION["anio_ciclo"] . "' AND ciclo = '" . $_SESSION["ciclo"] . "' AND id_empresa = '" . $_SESSION["id_empresa"] . "' " . $filtros . " ");
while ($dataRegs = mysqli_fetch_array($queryRegs)) {

    array_push($array_evaluaciones, json_decode($dataRegs["objeto_competencias"], true));
    array_push($array_evaluaciones_general, $dataRegs);
}

//PROMEDIO GENERAL BARRA
$promedio_general_area  = 0;
if ($promedio_general > 0) {
    $promedio_general_area = $promedio_general / $contador_total;
}

//PARA RETORNAR UN COLOR
function RetornarColor($valor)
{
    $valor = ($valor * 100) / 5; //VALOR CONVERTIDO A PORCENTAJE
    $valor = round($valor);

    global $connect_admin;
    $queryEI = mysqli_query($connect_admin, "SELECT * FROM Escala_Medicion WHERE id_empresa = " . $_SESSION["id_empresa"] . "");
    $dataEI = mysqli_fetch_array($queryEI);

    $color = '';
    if ($valor >= $dataEI["porcentaje_uno"] && $valor <= $dataEI["porcentaje_dos"]) {
        $color = "#FF0000";
    }
    if ($valor >= $dataEI["porcentaje_tres"] && $valor <= $dataEI["porcentaje_cuatro"]) {
        $color = "#FFF200";
    }
    if ($valor >= $dataEI["porcentaje_cinco"] && $valor <= $dataEI["porcentaje_seis"]) {
        $color = "#95FA03";
    }
    if ($valor >= $dataEI["porcentaje_siete"] && $valor <= $dataEI["porcentaje_ocho"]) {
        $color = "#14F209";
    }
    if ($valor >= $dataEI["porcentaje_ocho"]) {
        $color = "#00D30A";
    }
    return $color;
}

//DATOS PROCESADOS
$array_gerencias_resumen = array();

foreach ($array_viceprecidiencia as $gerencia) {
    if ($gerencia > 0) {
        $queryAre = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id = '" . $gerencia . "' ");
        $dataAre = mysqli_fetch_array($queryAre);

        $promedio_gerencia = 0;
        $counts_gerencia = 0;
        foreach ($array_evaluaciones_general as $evals) {
            if ($evals["id_viceprecidiencia"] == $gerencia) {
                $promedio_gerencia += $evals["promedio"];
                $counts_gerencia++;
            }
        }

        $promedio_gerencia = $promedio_gerencia / $counts_gerencia;


        $porcentaje_gerencia = ($promedio_gerencia * 100) / 5;
        $color_gerencia = RetornarColor($promedio_gerencia);

        $nodo = array(
            "area" => $dataAre["nombre"],
            "promedio" => round($promedio_gerencia, 2),
            "porcentaje" => round($porcentaje_gerencia, 2),
            "color" => $color_gerencia
        );

        array_push($array_gerencias_resumen, $nodo);
    }
}

foreach ($array_gerencias_resumen as $key => $row) {
    $aux[$key] = $row['promedio'];
}
array_multisort($aux, SORT_DESC, $array_gerencias_resumen);
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .fichas {
        margin-bottom: 15px;
    }

    .titulo {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .ti_ficha {
        font-size: 16px;
        color: #818181;
        font-weight: bold;
    }

    .numero_ficha {
        font-size: 30px;
    }

    .barra_avance {
        text-align: center;
        font-weight: bold;
        padding: 11px;
        border-radius: 0px 20px 20px 0px;
    }

    .nombre_comp_barras {
        font-size: 9px;
        height: 35px;
        line-height: 11px;
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

        .right_col {
            position: relative;
            height: 100%;
            margin-left: 0px !important;
            background-color: #ffffff !important;

        }

        .bt_print {
            display: none;
        }

        .top_nav .top-nav-fixed {
            display: none !important;
        }

        .top-nav-fixed {
            display: none !important;
        }

        .left_col {
            display: none;
        }
    }
</style>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3>
                        Informes Gerencias <?= $dataCicloVal["anio"]; ?> | <small>Ciclo: <?php echo $dataCicloVal["nombre"]; ?></small>
                        <button type="button" class="btn btn-success float-end" onclick="window.print();">
                            <i class="fa fa-print"></i> Imprimir / Descargar
                        </button>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-pills justify-content-center">
                        <li class="nav-item">
                            <a class="nav-link active " href="?pg=competencias/analitica">Vicepresidencias</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="?pg=competencias/informes/areas">Áreas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?pg=competencias/informes/niveles_cargo">Niveles Cargos </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="?pg=competencias/informes/sincronizar_reportes">Sincronizar</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php include("views/competencias/componentes/comp_escala.php"); ?>

    <div class="row">

        <div class="col-md-12 mt-4">
            <form action="" method="post">
                <div class="row">
                    <div class="col-md-9 mb-3">
                        <select class="form-select multiples_responsables" name="fill_area">
                            <option value="">Filtrar por Gerencia...</option>
                            <?php
                            foreach ($array_gerenciasFiltro  as $gerencia) {
                                $queryG = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id = '" . $gerencia . "' ");
                                $dataG = mysqli_fetch_array($queryG);

                                if ($_POST["fill_area"] == $gerencia) {
                                    echo '<option value="' . $gerencia . '" selected >' . $dataG["nombre"] . '</option>';
                                } else {
                                    echo '<option value="' . $gerencia . '" >' . $dataG["nombre"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button type="submit" class="btn btn-success w-100">
                            Filtrar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- FICHA -->
        <div class="col-md-3 text-center">
            <div class="card fichas">
                <div class="card-body">
                    <div class="ti_ficha">Vicepresidencias</div>
                    <div class="numero_ficha"><?php echo $queryVicepresidencias->num_rows; ?></div>
                </div>
            </div>
        </div>

        <!-- FICHA -->
        <div class="col-md-3 text-center">
            <div class="card fichas">
                <div class="card-body">
                    <div class="ti_ficha">Áreas</div>
                    <div class="numero_ficha"><?php echo $queryAreas->num_rows; ?></div>
                </div>
            </div>
        </div>

        <!-- FICHA -->
        <div class="col-md-3 text-center">
            <div class="card fichas">
                <div class="card-body">
                    <div class="ti_ficha">Cargos</div>
                    <div class="numero_ficha"><?php echo $queryCargos->num_rows; ?></div>
                </div>
            </div>
        </div>

        <!-- FICHA -->
        <div class="col-md-3 text-center">
            <div class="card fichas">
                <div class="card-body">
                    <div class="ti_ficha">Evaluados</div>
                    <div class="numero_ficha"><?php echo $queryEvaluados->num_rows;  ?></div>
                </div>
            </div>
        </div>

        <!-- RESULTADO GENERAL -->
        <div class="col-md-12">
            <div class="card fichas">
                <div class="card-header text-center">
                    <h2>Promedio General</h2>
                </div>
                <div class="card-body">

                    <?php
                    $porcentaje_general = ($promedio_general_area * 100) / 5;
                    $color_general = RetornarColor($promedio_general_area);
                    ?>

                    <div style="width: 100%; background-color: #E9E9E9">
                        <div style="width: <?php echo $porcentaje_general; ?>%; background-color: <?php echo $color_general; ?>; text-align: center;font-weight: bold; padding: 5px; min-width: 60px;">
                            <?php echo round((($promedio_general_area * 100) / 5), 0)  ?>%
                        </div>
                    </div>

                    <div style="margin-bottom: 20px">
                        Porcentaje nivel de desarrollo: <b><?php echo round((($promedio_general_area * 100) / 5), 0)  ?>%</b><br>
                    </div>

                </div>
            </div>
        </div>

        <!-- BLOQUE -->
        <div class="col-md-12">
            <div class="card fichas">
                <div class="card-header text-center">
                    <h2>Resultado Gráfico Consolidado por Competencias</h2>
                </div>
                <div class="card-body" style="overflow-x: auto;">
                    <table width="100%">
                        <tr>
                            <?php
                            $array_competencias = array();
                            $queryCompetencias = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' 
						AND id_ciclo = '" . $_SESSION['ciclo'] . "' ORDER BY id DESC ");
                            while ($dataCompetencias = mysqli_fetch_array($queryCompetencias)) {

                                $promedios = 0;
                                $porcentanje = 0;
                                $total = 0;
                                foreach ($array_evaluaciones as $evaluacion) {
                                    foreach ($evaluacion as $eval) {
                                        if ($eval["id_g_comp"] == $dataCompetencias["id"]) {
                                            $promedios += $eval["promedio_g_comp"];
                                            $porcentanje += $eval["porcentaje_g_comp"];
                                            $total++;
                                        }
                                    }
                                }

                                if ($total > 0) {
                                    $promedios = round(($promedios / $total), 2);
                                    $porcentanje = round(($porcentanje / $total), 2);
                                }

                                $fila = array(
                                    "promedio" => $promedios,
                                    "porcentaje" => $porcentanje,
                                    "nombre" => $dataCompetencias["nombre"],
                                );

                                array_push($array_competencias, $fila);
                            }

                            $ancho = 100 / count($array_competencias);
                            foreach ($array_competencias as $competencia) {

                                $color_compt = RetornarColor($competencia["promedio"]);

                                if ($competencia["promedio"] > 0) {

                                    echo '
                                <td align="center" valign="bottom"  width="' . $ancho . '%" >

                                    <div style="font-size: 11px">' . $competencia["porcentaje"] . '%<div>
                                    <div style="width: 60px; height:200px; background-color: #E9E9E9">
                                        <div style="height: ' . (100 - $competencia["porcentaje"]) . '%; width: 60px;">
                                        </div>
                                        <div style="height: ' . $competencia["porcentaje"] . '%; width: 60px; background-color: ' . $color_compt . '">
                                        </div>
                                    </div>
                                    <div class="nombre_comp_barras">' . $competencia["nombre"] . '</div>
                                </td>
                                ';
                                }
                            }
                            ?>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- REPORTES POR GERENCIAS -->
        <div class="col-md-12">
            <div class="card fichas">
                <div class="card-header text-center">
                    <h2>Vicepresidencias</h2>
                </div>
                <div class="card-body">

                    <?php
                    foreach ($array_gerencias_resumen as $gerencia) {
                    ?>

                        <div class="mt-1">
                            <b><?php echo $gerencia["area"]; ?></b>
                        </div>
                        <div style="width: 100%; background-color: #E9E9E9">
                            <div style="width: <?php echo $gerencia["porcentaje"]; ?>%; background-color: <?php echo $gerencia["color"]; ?>; text-align: center;font-weight: bold; padding: 5px; min-width: 60px;">
                                <?php echo $gerencia["porcentaje"]; ?>%
                            </div>
                        </div>

                    <?php }  ?>

                </div>
            </div>
        </div>

        <!-- BLOQUE -->
        <div class="col-md-12">
            <div class="card fichas">
                <div class="card-header">
                    <h2>Áreas Relacionadas a este Informe</h2>
                </div>
                <div class="card-body">
                    <div align="left">
                        <?php
                        $count = 1;
                        foreach ($array_areas_l as $areas) {
                            if ($areas > 0) {
                                $queryAre = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id = '" . $areas . "' ");
                                $dataAre = mysqli_fetch_array($queryAre);

                                echo $count . ') ' . $dataAre["nombre"] . '<br>';
                                $count++;
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- BLOQUE -->
        <div class="col-md-12">
            <div class="card fichas">
                <div class="card-header">
                    <h2>Cargos Relacionados a este Informe</h2>
                </div>
                <div class="card-body">
                    <div align="left">
                        <?php
                        $count = 1;
                        foreach ($array_cargos as $cargo) {

                            if ($cargo > 0) {
                                $queryPue = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE id = '" . $cargo . "' ");
                                $dataPue = mysqli_fetch_array($queryPue);

                                echo $count . ") " . $dataPue["nombre"] . "<br>";
                                $count++;
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script>
<script>
    $(document).ready(function() {
        $('.multiples_responsables').select2();
    });
</script>