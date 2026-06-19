<script>
    // $("#bt_okrs_reportes_equipos").addClass("active_item");
    // $("#nav_visualizaciones").addClass("menu-is-opening menu-open");
    $(".menu_section").addClass("active");
    // $("#nav_visualizaciones").addClass("active");
    jQuery("#menu_estrategia").css("display", "none");
    $("#bt_okrs_ponderacion").addClass("current-page");
</script>
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
</style>
<?php
include("views/okrs_equipos/functions.php");

$avance_general_equipo = 0;
$count_avance_general_equipo = 0;

if ($_POST["anio_fill"] != "") {
    $_SESSION["anio_fill"] = $_POST["anio_fill"];
}
if ($_POST["anio_fill"] == -1) {
    $_SESSION["anio_fill"] = "";
}

$check1 = $_POST["Q1"] != "" ? "checked" : "";
$check2 = $_POST["Q2"] != "" ? "checked" : "";
$check3 = $_POST["Q3"] != "" ? "checked" : "";
$check4 = $_POST["Q4"] != "" ? "checked" : "";
$check5 = $_POST["Anual"] != "" ? "checked" : "";

$filtro = "";

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

$filtro_vr = null;
$filtro_kr = $filtro_claves = "";
if ($filtro) {
    $filtro_kr = "AND anio = '" . $_SESSION["anio_fill"] . "' ";
    $filtro_claves = "AND periodo IN (" . $filtro . ") ";
    $filtro_claves1 = "AND Okrs.periodo IN (" . $filtro . ") ";
} else {
    $filtro_kr = "AND anio = '" . $_SESSION["anio_fill"] . "'";
}
if ($filtro_claves) {
    $filtro_vr = "," . $filtro;
}

$filtro_area = "";

if ($_SESSION['role_plataforma'] == 2) {
    if ($_SESSION['area'] > 0) {
        $filtro_area = " AND area = " . $_SESSION['area'] . "";
    } else {
        $queryAreaF = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND nombre = '" . $_SESSION['area'] . "' ");
        $dataAreaF = mysqli_fetch_array($queryAreaF);
        $filtro_area = " AND area = " . $dataAreaF["id"] . "";
    }
}
$hoy = date("Y-m-d H:i:s");

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
if($_POST["guardar_ponderacion"] != ""){
    $queryOrg = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND tipo = 1  AND anio = " . $_SESSION["anio_fill"] . " AND (objetivos_estrategicos LIKE '%," . $_POST["id_registro"] . "' OR objetivos_estrategicos IN (".$_POST["id_registro"].")) $filtro_claves");
    while ($dataOrg = mysqli_fetch_array($queryOrg)) {
        $queryEstrategico = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id = " . $_POST["id_registro"] . "");
        $estragtegico = mysqli_fetch_array($queryEstrategico);

        $sentencia = "UPDATE Okrs SET ponderacion = '" . $_POST["ponderado_" . $dataOrg["id"] . ""] . "', updated_at = '$hoy' WHERE id = " . $dataOrg["id"] . "";
        // echo $sentencia;
		mysqli_query($connect_okrs, $sentencia);

        $accion = 'ACTUALIZAR';
		$descripcion = 'Actualización peso ponderado de Orks para el objetivo organizacional ' . $estragtegico["objetivo"];

		$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $_SESSION["id_empresa"] . ", " . $_SESSION["id_user"] . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $dataResultadosIni["id_okrs"] . "," . $_POST["id_registro"] . "," . $dataResultadosIni["id"] . ",'$hoy')";
		// echo $auditoria;
		mysqli_query($connect_okrs, $auditoria);
    }
}

$querySM25 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 2 AND id_submenu = 14");
$dataSM25 = mysqli_fetch_array($querySM25);
?>
<div class="row">
    <div class="col-md-12">
        <div class="card" style="padding: unset !important;">
            <div class="card-header" style="background-color: #FFFFFF !important;padding: .5rem 1rem !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-sitemap" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM25["nombre"]; ?> año <?php echo $_SESSION["anio_fill"]; ?></h4>
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
            <form action="" method="post" id="formulario_filtro">
                <div class="row">
                    <div class="col-md-2">
                        <label for="" style="color: black;">Seleccione el año</label>
                    </div>
                    <div class="col-md-1">
                        <select class="form-control form-control-sm" name="anio_fill" onChange="Filtrar()">
                            <?php
                            foreach ($Array_Anio as $periodo) {
                                if ($_SESSION["anio_fill"] ==  $periodo[0]) {
                                    echo '<option value="' . $periodo[0] . '" selected>' . $periodo[1] . '</option>';
                                } else {
                                    echo '<option value="' . $periodo[0] . '">' . $periodo[1] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <!-- </div>
				<br>
				<div class="row"> -->
                    <!-- <div class="col-md-1"></div>
                    <div class="col-md-2">
                        <label for="" style="color: black;">Seleccione el peridodo</label>
                    </div>
                    <div class="col-md-1">
                        <input type="checkbox" name="Q1" id="Q1" <?php //echo $check1; 
                                                                    ?> onChange="Filtrar()" class="form-check-input"> Q1
                    </div>
                    <div class="col-md-1">
                        <input type="checkbox" name="Q2" id="Q2" <?php //echo $check2; 
                                                                    ?> onChange="Filtrar()" class="form-check-input"> Q2
                    </div>
                    <div class="col-md-1">
                        <input type="checkbox" name="Q3" id="Q3" <?php //echo $check3; 
                                                                    ?> onChange="Filtrar()" class="form-check-input"> Q3
                    </div>
                    <div class="col-md-1">
                        <input type="checkbox" name="Q4" id="Q4" <?php //echo $check4; 
                                                                    ?> onChange="Filtrar()" class="form-check-input"> Q4
                    </div>
                    <div class="col-md-1">
                        <input type="checkbox" name="Anual" id="Anual" <?php //echo $check5; 
                                                                        ?> onChange="Filtrar()" class="form-check-input"> Anual
                    </div> -->
                </div>
            </form>
        </div>
    </div>
    <br>
    <?php while ($dataEstrategicos = mysqli_fetch_array($queryEstrategicos)) {
        $sum_resultado1 = 0;
        $queryOrganizacional1 = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND tipo = 1 AND anio = " . $_SESSION["anio_fill"] . " AND objetivos_estrategicos LIKE '%" . $dataEstrategicos["id"] . "%' $filtro_claves");
        $ponderacionOrganizacional1 = round(100 / (mysqli_num_rows($queryOrganizacional1)), 2);
        while ($dataOrganizacional1 = mysqli_fetch_array($queryOrganizacional1)) {
            $resultados = PorOkrsPonderacion($connect_okrs, $dataOrganizacional1["id"], $_SESSION['id_empresa'], $filtro_claves);
            $a_por = round($resultados["promedio"]);
            if (is_nan($a_por)) {
                $a_por = 0;
            }
            if (isset($dataOrganizacional1["ponderacion"])) {
                $ponderacionOrganizacional1 = $dataOrganizacional1["ponderacion"];
            }
            $resultado_desempenio = round(($ponderacionOrganizacional1 * $a_por) / 100, 2);

            if (is_nan($resultado_desempenio)) {
                $resultado_desempenio = 0;
            }
            $sum_resultado1 = $sum_resultado1 + $resultado_desempenio;
            $escala1 = EscalaColor($sum_resultado1, $_SESSION['id_empresa'], $connect_valentina);
            $escala2 = EscalaColor(round(($dataEstrategicos["ponderacion"] * $sum_resultado1) / 100, 2), $_SESSION['id_empresa'], $connect_valentina);
            // echo $sum_resultado1."<br>";

        } ?>
        <div class="row" style="align-items: center;">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="align-items: center;">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card" style="text-align: left;">
                                    <div class="card-body">
                                        <h4>OBJETIVO ESTRATÉGICO</h4><br>
                                        <h4 style="color: black !important;"><?php echo $dataEstrategicos["objetivo"]; ?></h4>
                                    </div>
                                </div>
                            </div>
                            <!-- </div>
                    </div>
                    <div class="card-body">
                        <div class="row"> -->
                            <div class="col-md-4">
                                <div class="card" style="text-align: center;">
                                    <div class="card-body">
                                        <h4>PESO PONDERADO DEL OE</h4><br>
                                        <h1 style="color: black !important;"><?php echo $dataEstrategicos["ponderacion"]; ?>%</h1>
                                    </div>
                                </div>
                            </div>
                            <!-- <div class="col-md-4">
                                <div class="card" style="text-align: center;">
                                    <div class="card-body">
                                        <h4>DESEMPEÑO DE LOS OKR'S</h4><br>
                                        <h1 style="color: black !important;"><?php //echo $sum_resultado1; 
                                                                                ?>%</h1> -->
                            <!-- <div class="progresos" data-bs-toggle="tooltip" align="center">
                                            <h2 style="font-size: 2.5rem;color: black !important;"><?php //echo $sum_resultado1; 
                                                                                                    ?> %</h1>
                                        </div>
                                        <div class="progress-bar bg-success" role="progressbar" style=" width: <?php //echo $sum_resultado1; 
                                                                                                                ?>%;background-color: <?php //echo $escala1["color_bg"]; 
                                                                                                                                        ?> !important;opacity: 0.3;z-index: 2;margin-top: -70px;height: 70px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        </div> -->
                            <!-- </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card" style="text-align: center;">
                                    <div class="card-body">
                                        <h4>DESEMPEÑO DEL OE</h4><br>
                                        <h1 style="color: black !important;"><?php //echo round(($dataEstrategicos["ponderacion"] * $sum_resultado1) / 100, 2); 
                                                                                ?>%</h1> -->
                            <!-- <div class="progresos" data-bs-toggle="tooltip" align="center">
                                            <h2 style="font-size: 2.5rem;color: black !important;"><?php //echo round(($dataEstrategicos["ponderacion"] * $sum_resultado1) / 100, 2); 
                                                                                                    ?> %</h1>
                                        </div>
                                        <div class="progress-bar bg-success" role="progressbar" style=" width: <?php //echo round(($dataEstrategicos["ponderacion"] * $sum_resultado1) / 100, 2); 
                                                                                                                ?>%;background-color: <?php //echo $escala2["color_bg"]; 
                                                                                                                                        ?> !important;opacity: 0.3;z-index: 2;margin-top: -70px;height: 70px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        </div> -->
                            <!-- </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="col-md-12">
                            <form action="" method="post">
                                <input type="hidden" name="guardar_ponderacion" value="true">
                                <input type="hidden" name="id_registro" value="<?php echo $dataEstrategicos["id"]; ?>">
                                <div class="table-responsive">
                                    <table border="1" class="display table" style="width:100%" id="estrategico_<?php echo $dataEstrategicos["id"]; ?>">
                                        <thead class="thead-success">
                                            <!-- <tr>
                                            <th colspan="4"></th>
                                            <th colspan="6" style="text-align: center;border-left: ridge;">Periodo</th>
                                            <th colspan="2" style="text-align: center;border-left: ridge;">KR's</th>
                                        </tr> -->
                                            <tr>
                                                <th>#</th>
                                                <th style="width:750px;">OKR Organizacionales</th>
                                                <th>Periodo</th>
                                                <th>Peso Ponderado</th>
                                                <th>Q1</th>
                                                <th>Q2</th>
                                                <th>Q3</th>
                                                <th>Q4</th>
                                                <th style="background-color: rgb(188 219 253) !important;">Desempeño por Q's</th>
                                                <th>Porcentaje de Q's</th>

                                                <th>KR Anual</th>
                                                <th>Porcentaje KR Anual</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $number = 1;
                                            $sum_resultado = 0;

                                            $queryOrganizacional = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND tipo = 1  AND anio = " . $_SESSION["anio_fill"] . " AND (objetivos_estrategicos LIKE '%," . $dataEstrategicos["id"] . "' OR objetivos_estrategicos IN (".$dataEstrategicos["id"].")) $filtro_claves");
                                            $ponderacionOrganizacional = round(100 / (mysqli_num_rows($queryOrganizacional)), 2);
                                            $sumQ1 = $sumQ2 = $sumQ3 = $sumQ4 = $sumDesempenio = $sumPonderaciones = 0;
                                            while ($dataOrganizacional = mysqli_fetch_array($queryOrganizacional)) {
                                                $resultados = PorOkrsPonderacion($connect_okrs, $dataOrganizacional["id"], $_SESSION['id_empresa'], $filtro_claves);
                                                $a_por = round($resultados["promedio"]);
                                                if (is_nan($a_por)) {
                                                    $a_por = 0;
                                                }
                                                if (isset($dataOrganizacional["ponderacion"]) && $dataOrganizacional["ponderacion"] > 0) {
                                                    $ponderacionOrganizacional = $dataOrganizacional["ponderacion"];
                                                } else {

                                                    mysqli_query($connect_okrs, "UPDATE Okrs SET ponderacion = '$ponderacionOrganizacional', updated_at = '$hoy' WHERE id = " . $dataOrganizacional['id'] . "");
                                                }
                                                $resultado_desempenio = round(($ponderacionOrganizacional * $a_por) / 100, 2);
                                                if (is_nan($resultado_desempenio)) {
                                                    $resultado_desempenio = 0;
                                                }
                                                $escala = EscalaColor($a_por, $_SESSION['id_empresa'], $connect_valentina);
                                                $color_bg = $escala['color_bg'];
                                                // $editar_ponderacion = '<input type="number" name="" class="form-control form-control-sm mb-0" value="' . $dataOrganizacional["ponderacion"] . '" onChange="Guardar_Avance_Ponderacion(this.value, ' . $dataOrganizacional["id"] . ',' . $_SESSION["id_empresa"] . ',' . $_SESSION["id_user"] . ')" >';
                                                $editar_ponderacion = '<input type="text" name="ponderado_'.$dataOrganizacional["id"].'" id="ponderado_'.$dataOrganizacional["id"].'" class="form-control form-control-sm mb-0" value="' . $dataOrganizacional["ponderacion"] . '" onkeyup="return NumerosDecimales(this)" >';

                                                $resultados1 = PorOkrsPonderacion($connect_okrs, $dataOrganizacional["id"], $_SESSION['id_empresa'], " AND periodo = 'Q1'");
                                                $a_por1 = round($resultados1["promedio"]);

                                                $resultados2 = PorOkrsPonderacion($connect_okrs, $dataOrganizacional["id"], $_SESSION['id_empresa'], " AND periodo = 'Q2'");
                                                $a_por2 = round($resultados2["promedio"]);

                                                $resultados3 = PorOkrsPonderacion($connect_okrs, $dataOrganizacional["id"], $_SESSION['id_empresa'], " AND periodo = 'Q3'");
                                                $a_por3 = round($resultados3["promedio"]);

                                                $resultados4 = PorOkrsPonderacion($connect_okrs, $dataOrganizacional["id"], $_SESSION['id_empresa'], " AND periodo = 'Q4'");
                                                $a_por4 = round($resultados4["promedio"]);

                                                $resultados5 = PorOkrsPonderacion($connect_okrs, $dataOrganizacional["id"], $_SESSION['id_empresa'], " AND periodo = 'Anual'");
                                                $a_por5 = round($resultados5["promedio"]);

                                                $ponderadoOkr1 = round(($a_por1 * $dataOrganizacional["ponderacion"]) / 100, 2);
                                                $ponderadoOkr2 = round(($a_por2 * $dataOrganizacional["ponderacion"]) / 100, 2);
                                                $ponderadoOkr3 = round(($a_por3 * $dataOrganizacional["ponderacion"]) / 100, 2);
                                                $ponderadoOkr4 = round(($a_por4 * $dataOrganizacional["ponderacion"]) / 100, 2);
                                                $ponderadoOkr5 = round(($a_por5 * $dataOrganizacional["ponderacion"]) / 100, 2);
                                                $a_por5 = round(($ponderadoOkr5 * 100) / $dataOrganizacional["ponderacion"], 2);
                                                $desempenioKr = round(($ponderadoOkr1 + $ponderadoOkr2 + $ponderadoOkr3 + $ponderadoOkr4) / 4, 2);
                                                $desmepenioPorc = round(($desempenioKr * 100) / $dataOrganizacional["ponderacion"], 2);
                                                $escala1 = EscalaColor($desmepenioPorc, $_SESSION['id_empresa'], $connect_valentina);
                                                $color_bg1 = $escala1['color_bg'];

                                                $escala2 = EscalaColor($a_por5, $_SESSION['id_empresa'], $connect_valentina);
                                                $color_bg2 = $escala1['color_bg'];
                                                if ($desmepenioPorc > 100) {
                                                    $desmepenioPorc1 = 100;
                                                } else {
                                                    $desmepenioPorc1 = $desmepenioPorc;
                                                }
                                                if (is_nan($desmepenioPorc)) {
                                                    $desmepenioPorc = 0;
                                                }

                                                if ($a_por5 > 100) {
                                                    $a_por51 = 100;
                                                } else {
                                                    $a_por51 = $a_por5;
                                                }
                                                if (is_nan($ponderadoOkr5)) {
                                                    $ponderadoOkr5 = 0;
                                                }

                                                if (is_nan($a_por5)) {
                                                    $a_por5 = 0;
                                                }

                                                echo '<tr>
                                                <td>' . $number++ . '</td>
                                                <td style="width: 750px;">' . $dataOrganizacional["objetivo_okr"] . '</td>
                                                <td>' . $dataOrganizacional["periodo"] . '</td>
                                                <td>' . $editar_ponderacion . '</td>
                                                <td>' . $ponderadoOkr1 . '%</td>
                                                <td>' . $ponderadoOkr2 . '%</td>
                                                <td>' . $ponderadoOkr3 . '%</td>
                                                <td>' . $ponderadoOkr4 . '%</td>                                                
                                                <td style="background-color: rgb(188 219 253) !important;">' . $desempenioKr . '%</td>
                                                <td>
                                                <div class="progress" title="' . $escala1["txt_subtitulo"] . '">
	<div class="progress-bar bg-success" role="progressbar" style=" min-width: 15px; width: ' . $desmepenioPorc1 . '%;background-color: ' . $escala1['color_bg'] . ' !important;color:' . $escala1["color_text"] . ' !important;opacity: 0.7 !important;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
</div>' . $desmepenioPorc . '%
                                                </td>
                                                
                                                <td style="text-align:center;">' . $ponderadoOkr5 . '%</td>
                                                <td>
                                                <div class="progress" title="' . $escala2["txt_subtitulo"] . '">
	<div class="progress-bar bg-success" role="progressbar" style=" min-width: 15px; width: ' . $a_por51 . '%;background-color: ' . $escala2['color_bg'] . ' !important;color:' . $escala2["color_text"] . ' !important;opacity: 0.7 !important;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
    
</div>' . $a_por5 . '%
                                                </td>
                                                </tr>';
                                                $sum_resultado = $sum_resultado + $resultado_desempenio;
                                                $sumQ1 = $sumQ1 + $ponderadoOkr1;
                                                $sumQ2 = $sumQ2 + $ponderadoOkr2;
                                                $sumQ3 = $sumQ3 + $ponderadoOkr3;
                                                $sumQ4 = $sumQ4 + $ponderadoOkr4;
                                                $sumDesempenio = $sumDesempenio + $desempenioKr;
                                                $sumPonderaciones = $sumPonderaciones + $dataOrganizacional["ponderacion"];
                                            }

                                            ?>
                                        </tbody>
                                        <tfoot>
                                            <?php
                                            echo '<tr>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: end;">TOTAL</td>                                        
                                        <td>' . $sumPonderaciones . '</td>
                                        <td>' . $sumQ1 . '%</td>
                                        <td>' . $sumQ2 . '%</td>
                                        <td>' . $sumQ3 . '%</td>
                                        <td>' . $sumQ4 . '%</td>
                                        <td style="background-color: rgb(188 219 253) !important;">' . $sumDesempenio . '%</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>';
                                            ?>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12" style="text-align: end;">
                                            <button class="btn btn-success" type="submit">Guardar Pesos Ponderados</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#estrategico_<?php echo $dataEstrategicos["id"]; ?>').DataTable({
                    columnDefs: [{
                            responsivePriority: 1,
                            targets: 0
                        },
                        {
                            responsivePriority: 2,
                            targets: -1
                        }
                    ],
                    responsive: true,
                    // pageLength: 50,
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
    <?php } ?>


</div>
<script>
    var api = '<?php echo $url; ?>api/okrs/';

    function VerOKRs(id) {
        jQuery.ajax({
                url: api + "ver_okr.php",
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

    function Filtrar() {
        $("#formulario_filtro").submit();
    }

    function VerOKRIndividual(id_owner, id_empresa, anio, promedio, filtro1, filtro2, filtro3, filtro4, filtro5) {
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
        window.open("<?php echo $url; ?>views_okrs/okrs_individual.php?id_owner=" + id_owner + "&id_empresa=" + id_empresa + "&anio=" + anio + "&promedio=" + promedio + "&filtro1=" + filtro1 + "&filtro2=" + filtro2 + "&filtro3=" + filtro3 + "&filtro4=" + filtro4 + "&filtro5=" + filtro5, "GoForAgile", "width=1300, height=900")
    }

    function Guardar_Avance_Ponderacion(avance, id, id_empresa, id_user) {
        data = {
            id: id,
            id_empresa: id_empresa,
            id_user: id_user,
            avance: avance,
        };
        jQuery.ajax({
                url: api + "guardar_avance_ponderacion.php",
                type: 'post',
                data: data,
            }).done(function(resp) {
                // $("#xscript").html(resp);
                // location.reload();
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function NumerosDecimales(input) {
        let valor = input.value;

        valor = valor.replace(/[^0-9.,-]/g, '');

        let puntos = (valor.match(/\./g) || []).length;
        let comas = (valor.match(/,/g) || []).length;

        if (puntos > 1) {
            valor = valor.replace(/\.(?=.*\.)/g, '');
        }

        if (comas > 1) {
            valor = valor.replace(/,(?=.*,)/g, '');
        }

        input.value = valor;
    }
</script>