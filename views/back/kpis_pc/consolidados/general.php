<script>
	$(".menu_section").addClass("active");
	jQuery("#menu_kpi").css("display", "none");
	$("#bt_kpi_consolidados_ap").addClass("current-page");
</script>
<link rel="stylesheet" href="<?php echo $url; ?>views/okrs_equipos/reportes/styles.css">
<style>
	.progreso-bar {
		width: 130px !important;
		height: 130px !important;
	}

	.objetivo-okr {
		font-size: 1.8rem !important;
	}

	#vistaAreas:before,
	#profileOkr:before,
	#verVP:before,
	.link-detalles:before {
		content: none !important;
	}

	.iconoPA {
		display: inline-block;
		width: 25px;
		height: 25px;
		background-repeat: no-repeat;
		background-size: contain;
		margin-left: 5px;
		vertical-align: middle;
	}

	.link-detalles {
		/* background-color: #2196F3; */
		color: #2196F3;
		padding: 8px;
		font-size: 13px;
	}

	.link-detalles:hover {
		color: #2196F3 !important;
	}

	.flecha-abajo {
		background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%232196F3'%3e%3cpath d='M2 6l6 6 6-6H2z'/%3e%3c/svg%3e");
	}

	.flecha-arriba {
		background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%232196F3'%3e%3cpath d='M2 10l6-6 6 6H2z'/%3e%3c/svg%3e");
	}
</style>
<link rel="stylesheet" href="<?php echo $url; ?>css/okrs.css">
<?php
global $connect_valentina;
global $connect_okrs;
include("views/kpis_pc/etiquetas.php");
include("views/okrs_equipos/functions.php");
include("views/okrs/layouts/modal_profile.php");
include("views/kpis_pc/detalle/functions.php");

if ($_POST["anio_fill"] != "") {
	$_SESSION["anio_fill"] = $_POST["anio_fill"];
}
if ($_POST["anio_fill"] == -1) {
	$_SESSION["anio_fill"] = "";
}

if ($_POST["tipo_kpi_fill"] != "") {
	$_SESSION["tipo_kpi_fill"] = $_POST["tipo_kpi_fill"];
}
if ($_POST["tipo_kpi_fill"] == -1) {
	$_SESSION["tipo_kpi_fill"] = "";
}
if ($_SESSION["tipo_kpi_fill"] > 0) {
	$filtro .= " AND K.tipo_kpi = " . $_SESSION["tipo_kpi_fill"] . "  ";
}

if ($_POST["frecuencia_fill"] != "") {
	$_SESSION["frecuencia_fill"] = $_POST["frecuencia_fill"];
}
if ($_POST["frecuencia_fill"] == -1) {
	$_SESSION["frecuencia_fill"] = "";
}
if ($_SESSION["frecuencia_fill"] > 0) {
	$filtro .= " AND K.frecuencia = " . $_SESSION["frecuencia_fill"] . "  ";
}

if ($_POST["calculo_fill"] != "") {
	$_SESSION["calculo_fill"] = $_POST["calculo_fill"];
}
if ($_POST["calculo_fill"] == -1) {
	$_SESSION["calculo_fill"] = "";
}
if ($_SESSION["calculo_fill"] > 0) {
	$filtro .= " AND K.tipo_calculo = " . $_SESSION["calculo_fill"] . "  ";
}

if ($_POST["resultado_fill"] != "") {
	$_SESSION["resultado_fill"] = $_POST["resultado_fill"];
}
if ($_POST["resultado_fill"] == -1) {
	$_SESSION["resultado_fill"] = "";
}
if ($_SESSION["resultado_fill"] > 0) {
	$filtro .= " AND K.tipo_resultado = " . $_SESSION["resultado_fill"] . "  ";
}

if ($_POST["unidad_fill"] != "") {
	$_SESSION["unidad_fill"] = $_POST["unidad_fill"];
}
if ($_POST["unidad_fill"] == -1) {
	$_SESSION["unidad_fill"] = "";
}
if ($_SESSION["unidad_fill"] > 0) {
	$filtro .= " AND K.unidad_medida = " . $_SESSION["unidad_fill"] . "  ";
}

// if ($_POST["area_macro_fill_ap"] != "") {
// 	$_SESSION["area_macro_fill_ap"] = $_POST["area_macro_fill_ap"];
// 	$filtro_VP = " AND id = " . $_POST["area_macro_fill_ap"] . " ";
// }
// if ($_POST["area_macro_fill_ap"] == -1) {
// 	$_SESSION["area_macro_fill_ap"] = "";
// 	$filtro_VP = "";
// }
// if ($_SESSION["area_macro_fill_ap"] > 0) {
// 	$filtro .= " AND K.area_macro = " . $_SESSION["area_macro_fill_ap"] . "  ";
// }

// if ($_POST["area_proceso_fill_ap"] != "") {
// 	$_SESSION["area_proceso_fill_ap"] = $_POST["area_proceso_fill_ap"];
// }
// if ($_POST["area_proceso_fill_ap"] == -1) {
// 	$_SESSION["area_proceso_fill_ap"] = "";
// }
$filtroArea = "";
// if ($_SESSION["area_proceso_fill_ap"] > 0) {

// 	$queryInt = mysqli_query($connect_valentina, "SELECT DISTINCT(vicepresidencia) AS vicepresidencia FROM Estructura_Empresa WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'  AND estado = 1 AND area = '" . $_SESSION["area_proceso_fill_ap"] . "' ORDER BY area ");
// 	$data = mysqli_fetch_array($queryInt);
// 	if ($_SESSION["area_macro_fill_ap"] == "") {
// 		$filtro_VP = " AND id = " . $data["vicepresidencia"] . " ";
// 	}

// 	$filtro .= " AND K.area_proceso = '" . $_SESSION["area_proceso_fill_ap"] . "'  ";
// 	$filtroArea = " AND area = '" . $_SESSION["area_proceso_fill_ap"] . "'  ";
// }

$mesConsultaI = $mesConsultaF = $periodoFill = "";

$array_mes = array();
$array_mes[7] = ["7", "julio", ", FKP.julio AS julio, FKP.avance_7 AS avance_7", ""];
$array_mes[8] = ["8", "agosto", ", FKP.agosto AS agosto, FKP.avance_8 AS avance_8", ""];
$array_mes[9] = ["9", "septiembre", ", FKP.septiembre AS septiembre, FKP.avance_9 AS avance_9", ""];
$array_mes[10] = ["10", "octubre", ", FKP.octubre AS octubre, FKP.avance_10 AS avance_10", ""];
$array_mes[11] = ["11", "noviembre", ", FKP.noviembre AS noviembre, FKP.avance_11 AS avance_11", ""];
$array_mes[12] = ["12", "diciembre", ", FKP.diciembre AS diciembre, FKP.avance_12 AS avance_12", ""];
$array_mes[1] = ["1", "enero", ", FKP.enero AS enero, FKP.avance_1 AS avance_1", ""];
$array_mes[2] = ["2", "febrero", ", FKP.febrero AS febrero, FKP.avance_2 AS avance_2", ""];
$array_mes[3] = ["3", "marzo", ", FKP.marzo AS marzo, FKP.avance_3 AS avance_3", ""];
$array_mes[4] = ["4", "abril", ", FKP.abril AS abril, FKP.avance_4 AS avance_4", ""];
$array_mes[5] = ["5", "mayo", ", FKP.mayo AS mayo, FKP.avance_5 AS avance_5", ""];
$array_mes[6] = ["6", "junio", ", FKP.junio AS junio, FKP.avance_6 AS avance_6", ""];


$Array_Periodo_Fin = array(
    array("7", "julio", ", FKP.julio AS julio, FKP.avance_7 AS avance_7", ""),
    array("8", "agosto", ", FKP.agosto AS agosto, FKP.avance_8 AS avance_8", ""),
    array("9", "septiembre", ", FKP.septiembre AS septiembre, FKP.avance_9 AS avance_9", ""),
    array("10", "octubre", ", FKP.octubre AS octubre, FKP.avance_10 AS avance_10", ""),
    array("11", "noviembre", ", FKP.noviembre AS noviembre, FKP.avance_11 AS avance_11", ""),
    array("12", "diciembre", ", FKP.diciembre AS diciembre, FKP.avance_12 AS avance_12", ""),
    array("1", "enero", ", FKP.enero AS enero, FKP.avance_1 AS avance_1", ""),
    array("2", "febrero", ", FKP.febrero AS febrero, FKP.avance_2 AS avance_2", ""),
    array("3", "marzo", ", FKP.marzo AS marzo, FKP.avance_3 AS avance_3", ""),
    array("4", "abril", ", FKP.abril AS abril, FKP.avance_4 AS avance_4", ""),
    array("5", "mayo", ", FKP.mayo AS mayo, FKP.avance_5 AS avance_5", ""),
    array("6", "junio", ", FKP.junio AS junio, FKP.avance_6 AS avance_6", ""),
);


if ($_POST["periodo_ini_fill"] != "") {
    $_SESSION["periodo_ini_fill"] = $_POST["periodo_ini_fill"];
}
if ($_POST["periodo_ini_fill"] == -1) {
    $_SESSION["periodo_ini_fill"] = "";
}
if ($_SESSION["periodo_ini_fill"] > 0) {
    switch ($_SESSION["periodo_ini_fill"]) {
        case 1:
            $mesConsultaI = ", FKP.enero AS enero, FKP.avance_1 AS avance_1";
            // $filtro .= " AND FKP.enero > 0";
            break;
        case 2:
            $mesConsultaI = ", FKP.febrero AS febrero, FKP.avance_2 AS avance_2";
            // $filtro .= " AND FKP.febrero > 0";
            break;
        case 3:
            $mesConsultaI = ", FKP.marzo AS marzo, FKP.avance_3 AS avance_3";
            // $filtro .= " AND FKP.marzo > 0";
            break;
        case 4:
            $mesConsultaI = ", FKP.abril AS abril, FKP.avance_4 AS avance_4";
            // $filtro .= " AND FKP.abril > 0";
            break;
        case 5:
            $mesConsultaI = ", FKP.mayo AS mayo, FKP.avance_5 AS avance_5";
            // $filtro .= " AND FKP.mayo > 0";
            break;
        case 6:
            $mesConsultaI = ", FKP.junio AS junio, FKP.avance_6 AS avance_6";
            // $filtro .= " AND FKP.junio > 0";
            break;
        case 7:
            $mesConsultaI = ", FKP.julio AS julio, FKP.avance_7 AS avance_7";
            // $filtro .= " AND FKP.julio > 0";
            break;
        case 8:
            $mesConsultaI = ", FKP.agosto AS agosto, FKP.avance_8 AS avance_8";
            // $filtro .= " AND FKP.agosto > 0";
            break;
        case 9:
            $mesConsultaI = ", FKP.septiembre AS septiembre, FKP.avance_9 AS avance_9";
            // $filtro .= " AND FKP.septiembre > 0";
            break;
        case 10:
            $mesConsultaI = ", FKP.octubre AS octubre, FKP.avance_10 AS avance_10";
            // $filtro .= " AND FKP.octubre > 0";
            break;
        case 11:
            $mesConsultaI = ", FKP.noviembre AS noviembre, FKP.avance_11 AS avance_11";
            // $filtro .= " AND FKP.noviembre > 0";
            break;
        case 12:
            $mesConsultaI = ", FKP.diciembre AS diciembre, FKP.avance_12 AS avance_12";
            // $filtro .= " AND FKP.diciembre > 0";
            break;
    }
}

if ($_POST["periodo_fin_fill"] != "") {
    $_SESSION["periodo_fin_fill"] = $_POST["periodo_fin_fill"];
}
if ($_POST["periodo_fin_fill"] == -1) {
    $_SESSION["periodo_fin_fill"] = "";
}
if ($_SESSION["periodo_fin_fill"] > 0) {
    if ($_SESSION["periodo_ini_fill"] > $_SESSION["periodo_fin_fill"]) {
        $periodoIni = $_SESSION["periodo_ini_fill"];

        for ($i = $periodoIni; $i <= 12; $i++) {

            if ($i >= $_SESSION["periodo_ini_fill"]) {
                $mesConsultaF .= $array_mes[$i][2];
                $queryFrecuencia .= $array_mes[$i][2];
                $filtro .= $array_mes[$i][3];
                $filtroKpi .= $array_mes[$i][3];
            }
        }

        for ($i = 1; $i <= $_SESSION["periodo_fin_fill"]; $i++) {

            if ($i <= $_SESSION["periodo_fin_fill"]) {
                $mesConsultaF .= $array_mes[$i][2];
                $queryFrecuencia .= $array_mes[$i][2];
                $filtro .= $array_mes[$i][3];
                $filtroKpi .= $array_mes[$i][3];
            } else {
                $superar = true;
            }
        }
    } else {

        foreach ($Array_Periodo_Fin as $periodo) {
            if ($periodo[0] >= $_SESSION["periodo_ini_fill"] && $periodo[0] <= $_SESSION["periodo_fin_fill"]) {
                $mesConsultaF .= $periodo[2];
                $queryFrecuencia .= $periodo[2];
                $filtro .= $periodo[3];
                $filtroKpi .= $periodo[3];
            }
        }
    }
}

$frecuencias = "5";
if ($_SESSION["periodo_ini_fill"] > 0 && $_SESSION["periodo_fin_fill"] > 0) {
    $mesConsultaI = $mesConsultaF = "";
    if ($_SESSION["periodo_ini_fill"] == $_SESSION["periodo_fin_fill"]) {
        $periodoFill = $array_mes[$_SESSION["periodo_ini_fill"]][2];
    } else {
        $periodoFill = "";
        if ($_SESSION["periodo_ini_fill"] >= $_SESSION["periodo_fin_fill"]) {
            for ($mes = $_SESSION["periodo_ini_fill"]; $mes <= 12; $mes++) {
                $periodoFill .= $array_mes[$mes][2];
            }
            for ($mes = 1; $mes <= $_SESSION["periodo_fin_fill"]; $mes++) {
                $periodoFill .= $array_mes[$mes][2];
            }
        } else {
            for ($mes = $_SESSION["periodo_ini_fill"]; $mes <= $_SESSION["periodo_fin_fill"]; $mes++) {
                $periodoFill .= $array_mes[$mes][2];
            }
        }
    }
    $periodoFill = rtrim($periodoFill, ", ");
    $diferenciaMeses = calcularDiferenciaMeses($_SESSION["periodo_ini_fill"], $_SESSION["periodo_fin_fill"]);

    if ($$diferenciaMeses >= 6) {
        $frecuencias = "5";
    } elseif ($diferenciaMeses > 4 && $diferenciaMeses < 6) {
        $frecuencias .= ", 4";
    } elseif ($diferenciaMeses >= 4 && $diferenciaMeses < 6) {
        $frecuencias .= ", 4";
    } elseif ($diferenciaMeses > 3 && $diferenciaMeses < 6) {
        $frecuencias .= ", 4";
    } elseif ($diferenciaMeses == 3) {
        $frecuencias .= ", 4, 6";
    } elseif ($diferenciaMeses > 2 && $diferenciaMeses < 4) {
        $frecuencias .= ", 4, 6, 3";
    } elseif ($diferenciaMeses <= 2) {
        $frecuencias .= ", 4, 6, 3";
    }
    if ($diferenciaMeses == 12) {
        $filtro .= "";
    } else {
        $filtro .= " AND K.frecuencia NOT IN ($frecuencias)";
    }

    $queryFrecuencia = substr($periodoFill, 1);
} else {
    $queryFrecuencia = " FKP.*";
}


$hoy = date("Y-m-d H:i:s");
$querySM61 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 6 AND id_submenu = 33");
$dataSM61 = mysqli_fetch_array($querySM61);

$Array_tipo_kpi_PC1 = array(
	array("1",$etiquetaKpiE ),
    array("2",$etiquetaKpiT ),
);
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #FFFFFF !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-chart-line" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM61["nombre"]; ?></h4>
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
				<div class="card-body">
					<?php include("views/kpis_pc/consolidados/filtro_area_macro.php"); ?>
				</div>
			</div>
		</div>
	</div>

	<br>

	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div id="accordionNivelJerarquico" class="accordion-icons" role="tablist">
					<?php
					$queryAK = mysqli_query($connect_kpis, "SELECT * FROM Administradores_Kpi WHERE id_empleado = '" . $_SESSION["id_user"] . "' AND estado = 1");

					if ($_SESSION["role_plataforma"] == 1 || mysqli_num_rows($queryAK) > 0) {
						$queryVP = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'  AND estado = 1 $filtro_VP ORDER BY nombre ");
					} else {
						$queryRL = mysqli_query($connect_valentina, "SELECT * FROM Relaciones_Laborales WHERE id_empleado = '" . $_SESSION["id_user"] . "' AND mod_kpis = 'on' AND estado = 1");
						$areasMacro = $areasProceso = "";
						if (mysqli_num_rows($queryRL) > 0) {
							$areasMacro = "AND id IN (";
							while ($dataRL = mysqli_fetch_array($queryRL)) {
								$areasMacro .= $dataRL["id_vp"] . ",";
							}
							$areasMacro = substr($areasMacro, 0, -1);
							$areasMacro .= ")";
							$queryVP = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 $areasMacro ORDER BY nombre");
						} else {

							$queryLV = mysqli_query($connect_valentina, "SELECT * FROM Lideres_Vicepresidencia WHERE id_lider = '" . $_SESSION["id_user"] . "' AND estado = 1");
							if (mysqli_num_rows($queryLV) > 0) {
								$areasMacro = "AND id IN (";
								while ($dataLV = mysqli_fetch_array($queryLV)) {
									$areasMacro .= $dataLV["id_vicepresidencia"] . ",";
								}
								$areasMacro = substr($areasMacro, 0, -1);
								$areasMacro .= ")";

								$queryVP = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' $areasMacro AND estado = 1 ");
							} else {
								$queryLA = mysqli_query($connect_valentina, "SELECT * FROM Lideres_Area WHERE id_lider = '" . $_SESSION["id_user"] . "' AND estado = 1");
								if (mysqli_num_rows($queryLA) > 0) {
									$areasProceso = "AND area IN (";
									while ($dataLA = mysqli_fetch_array($queryLA)) {
										$areasProceso .= $dataLA["id_area"] . ",";
									}
									$areasProceso = substr($areasProceso, 0, -1);
									$areasProceso .= ")";
									$queryEE = mysqli_query($connect_valentina, "SELECT DISTINCT(vicepresidencia) AS id_vp FROM Estructura_Empresa WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' $areasProceso AND estado = 1");
									$areasMacro = "AND id IN (";
									while ($dataLA = mysqli_fetch_array($queryEE)) {
										$areasMacro .= $dataLA["id_vp"] . ",";
									}
									$areasMacro = substr($areasMacro, 0, -1);
									$areasMacro .= ")";
									$queryVP = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' $areasMacro AND estado = 1 ");
								}
							}
						}
					}


					$contVP = 0;
					while ($dataVP = mysqli_fetch_array($queryVP)) {
						$arrayCol = array();
						$keyVp = $dataVP['id'];
						include("views/kpis_pc/consolidados/listado_areas_vp.php");

						$equipo = array_sort($arrayCol, 'promedio', SORT_DESC);
						// $colaboradores = array_slice($equipo, 0, 10);
						$nombre_lider = '';
						$queryLideres = mysqli_query($connect_valentina, "SELECT * FROM Lideres_Vicepresidencia WHERE id_vicepresidencia = '" . $dataVP["id"] . "' AND id_empresa = ".$_SESSION["id_empresa"]."");

						if (mysqli_num_rows($queryLideres) > 0) {
							while ($dataLideres = mysqli_fetch_array($queryLideres)) {
								$queryEmple = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '" . $dataLideres["id_lider"] . "' AND id_empresa = ".$_SESSION["id_empresa"]."");
								$dataEmple = mysqli_fetch_array($queryEmple);

								if (!$dataEmple["foto"]) {
									$dataEmple["foto"] = "img_default.jpg";
								}
								$nombre_lider .= '<a data-bs-toggle="tooltip" href="javascript:Profile(' . $dataLideres["id_lider"] . ',1)" class="dropdown-item" id="profileOkr"><img data-src="' . $url . '/recursos/' . $dataEmple["foto"] . '" class="lazyload foto_min" title="' . $dataEmple["nombre"] . '" style="width: 35px !important;height: 35px !important;"></a>';
							}
						} else {
							$nombre_lider = 'Sin Asignar';
						}

					?>
						<div class="card mb-0" style="margin-bottom: 10px !important;">
							<div class="card-header" role="tab" id="heading<?php echo $dataVP["id"]; ?>">
								<div class="row" style="align-items: center;">
									<div class="col-md-5">
										<h5><?php echo $dataVP["nombre"]; ?></h5>
									</div>
									<div class="col-md-2">
										Líder:&nbsp;&nbsp;<?php echo $nombre_lider; ?>

									</div>
									<div class="col-md-1">
										Kpis Asignados:<br>
										<?php

										$queryKpis1 = mysqli_query($connect_kpis, "SELECT K.* $mesConsultaI $mesConsultaF $periodoFill
										FROM Kpis K
										INNER JOIN Frecuencia_Kpis FKP ON FKP.id_kpi = K.id
										WHERE K.id_empresa = " . $_SESSION['id_empresa'] . " AND K.anio = " . $_SESSION['anio_fill'] . " AND K.area_macro = " . $dataVP["id"] . " $filtro  ORDER BY K.indicador ");
										$cantVP1 = mysqli_num_rows($queryKpis1);
										$sumSeguimiento = $sumAvance = $contKpi = $contKpiD = $sumAvanceD = $contKpiT = $contKpiE = $sumAvanceE = $sumAvanceT = $progresoAvance = 0;
										echo $cantVP1;
										?>
									</div>
									<div class="col-md-2">
										<?php include("views/kpis_pc/consolidados/area_macro_general.php");
										// echo "$sumAvanceD / $contKpiD";
										$avanceGeneralVP = $sumAvanceD / $contKpiD;

										if($_SESSION["tipo_kpi_fill"] == ""){
											if ($contKpiE > 0 && $contKpiT > 0) {
												$queryPonderacion = mysqli_query($connect_kpis, "SELECT * FROM Ponderaciones WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1");
												$dataPonderacion = mysqli_fetch_array($queryPonderacion);
												$estrategicos = round(($sumAvanceE / $contKpiE) * ($dataPonderacion["estrategico"] / 100), 2);

												$tacticos = round(($sumAvanceT / $contKpiT) * ($dataPonderacion["tactico"] / 100), 2);

												if (is_nan($estrategicos) || is_infinite($estrategicos)) {
													$estrategicos = 0;
												}
												if (is_nan($tacticos) || is_infinite($tacticos)) {
													$tacticos = 0;
												}
												$avanceGeneralVP = $estrategicos + $tacticos;
											}
										}

										$promedioVP = round($avanceGeneralVP, 2);

										if (is_nan($promedioVP) || is_infinite($promedioVP)) {
											$promedioVP = 0;
										}

										if ($promedioVP > 100) {
											$porcentaje_barraVP = 100;
											// $promedioVP = 100;
										} else {
											$porcentaje_barraVP = $promedioVP;
										}

										$escalaBarraVP = EscalaColor(round($promedioVP), $_SESSION['id_empresa'], $connect_valentina);
										$back_colorBarraVP = "background-color:" . $escalaBarraVP['color_bg'] . " !important";
										?>
										Progreso:<br>
										<div class="progress" data-bs-toggle="tooltip" title="" style="height: 15px;">
											<div class="progress-bar bg-success progress-bar-striped active" role="progressbar" style=" color: black; width: <?php echo $porcentaje_barraVP; ?>%;background-color: <?php echo $escalaBarraVP['color_bg']; ?> !important;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
										</div><?php echo $promedioVP; ?>%
									</div>
									<div class="col-md-2" style="text-align:end;">
										<?php
										$queryRL = mysqli_query($connect_valentina, "SELECT * FROM Relaciones_Laborales WHERE id_empleado = '" . $_SESSION["id_user"] . "' AND mod_kpis = 'on'  AND id_area = '' AND id_vp = " . $dataVP["id"] . " AND estado = 1");
										$dataRL = mysqli_fetch_array($queryRL);
										$queryLVP = mysqli_query($connect_valentina, "SELECT * FROM Lideres_Vicepresidencia WHERE id_vicepresidencia = '" . $dataVP["id"] . "' AND id_lider = '" . $_SESSION["id_user"] . "' AND estado = 1");
										$dataLVP = mysqli_fetch_array($queryLVP);
										$queryAK = mysqli_query($connect_kpis, "SELECT * FROM Administradores_Kpi WHERE id_empleado = '" . $_SESSION["id_user"] . "' AND estado = 1");
										if ($_SESSION['role_plataforma'] == 1 || mysqli_num_rows($queryRL) > 0  || mysqli_num_rows($queryLVP) > 0 || mysqli_num_rows($queryAK) > 0) { ?>
											<a class="btn btn-black btn-sm bt_editar" href="<?php echo $url ?>?pg=kpis_pc/consolidados/info_vp_general&id=<?php echo $dataVP["id"] ?>&page=1" title="Vista rápida de los KPIS" id="verVP">
												<i class="fa fa-eye" style="font-size: 1.3rem;"></i>
											</a>
										<?php  } ?>
										<a class="collapsed" data-toggle="collapse" href="#collapseVP_<?php echo $dataVP["id"]; ?>" aria-expanded="false" aria-controls="collapseVP_<?php echo $dataVP["id"]; ?>" id="datosVP_<?php echo $dataVP["id"]; ?>" style="color: black !important;">
										</a>
									</div>
								</div>
							</div>
							<div id="collapseVP_<?php echo $dataVP["id"]; ?>" class="collapse" role="tabpanel" aria-labelledby="heading<?php echo $dataVP["id"]; ?>" data-parent="#accordionNivelJerarquico">
								<?php include("views/kpis_pc/consolidados/areas_vp_general.php"); ?>
							</div>
						</div>
					<?php

					}

					?>

				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$(' #area_macro').DataTable({
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
			pageLength: 30,
			info: true,
			ordering: true,
			paging: true,
			searching: true,
			autoWidth: true,
			language: {
				processing: "Procesando...",
				search: "Buscar:",
				lengthMenu: "Mostrar _MENU_ registros.",
				info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
				infoEmpty: "Mostrando registros del 0 al 0 de 0 registros",
				infoFiltered: "(filtrado de un total de _MAX_ registros)",
				infoPostFix: "",
				loadingRecords: "Cargando...",
				zeroRecords: "No se encontraron áreas macro",
				emptyTable: "Ninguna iniciativa para este KR",
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
	var api = '<?php echo $url; ?>api/okrs/';



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