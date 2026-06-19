<?php
include("views/okrs/layouts/modal_profile.php");
include("views/okrs_equipos/functions.php");
include("views/kpis_pc/detalle/functions.php");

$registros_por_pagina = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$inicio = ($page - 1) * $registros_por_pagina;

if ($_GET["page"]) {
    $pagina = '&page=' . $_GET["page"];
    $page = $_GET["page"];
} else {
    $pagina = $page = '';
}

$filtro = "";

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
    $filtro .= " AND K.tipo_kpi = '" . $_SESSION["tipo_kpi_fill"] . "'  ";
}

if ($_POST["frecuencia_fill"] != "") {
    $_SESSION["frecuencia_fill"] = $_POST["frecuencia_fill"];
}
if ($_POST["frecuencia_fill"] == -1) {
    $_SESSION["frecuencia_fill"] = "";
}
if ($_SESSION["frecuencia_fill"] > 0) {
    $filtro .= " AND K.frecuencia = '" . $_SESSION["frecuencia_fill"] . "'  ";
}

if ($_POST["calculo_fill"] != "") {
    $_SESSION["calculo_fill"] = $_POST["calculo_fill"];
}
if ($_POST["calculo_fill"] == -1) {
    $_SESSION["calculo_fill"] = "";
}
if ($_SESSION["calculo_fill"] > 0) {
    $filtro .= " AND K.tipo_calculo = '" . $_SESSION["calculo_fill"] . "'  ";
}

if ($_POST["resultado_fill"] != "") {
    $_SESSION["resultado_fill"] = $_POST["resultado_fill"];
}
if ($_POST["resultado_fill"] == -1) {
    $_SESSION["resultado_fill"] = "";
}
if ($_SESSION["resultado_fill"] > 0) {
    $filtro .= " AND K.tipo_resultado = '" . $_SESSION["resultado_fill"] . "'  ";
}

if ($_POST["unidad_fill"] != "") {
    $_SESSION["unidad_fill"] = $_POST["unidad_fill"];
}
if ($_POST["unidad_fill"] == -1) {
    $_SESSION["unidad_fill"] = "";
}
if ($_SESSION["unidad_fill"] > 0) {
    $filtro .= " AND K.unidad_medida = '" . $_SESSION["unidad_fill"] . "'  ";
}

if ($_POST["area_macro_fill"] != "") {
    $_SESSION["area_macro_fill"] = $_POST["area_macro_fill"];
}
if ($_POST["area_macro_fill"] == -1) {
    $_SESSION["area_macro_fill"] = "";
}
if ($_SESSION["area_macro_fill"] > 0) {
    $filtro .= " AND K.area_macro = '" . $_SESSION["area_macro_fill"] . "'  ";
}

if ($_POST["area_proceso_fill"] != "") {
    $_SESSION["area_proceso_fill"] = $_POST["area_proceso_fill"];
}
if ($_POST["area_proceso_fill"] == -1) {
    $_SESSION["area_proceso_fill"] = "";
}
if ($_SESSION["area_proceso_fill"] > 0) {
    $filtro .= " AND K.area_proceso = '" . $_SESSION["area_proceso_fill"] . "'  ";
}

if ($_POST["subproceso_fill"] != "") {
    $_SESSION["subproceso_fill"] = $_POST["subproceso_fill"];
}
if ($_POST["subproceso_fill"] == -1) {
    $_SESSION["subproceso_fill"] = "";
}
if ($_SESSION["subproceso_fill"] > 0) {
    $filtro .= " AND K.subproceso = '" . $_SESSION["subproceso_fill"] . "'  ";
}

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

// if ($_SESSION["periodo_ini_fill"] > 0 && $_SESSION["periodo_fin_fill"] > 0) {
//     if ($_SESSION["periodo_ini_fill"] == $_SESSION["periodo_fin_fill"]) {
//         $periodoFill = $mesConsultaI;
//         $periodoFK = $queryFrecuenciaI;
//         $queryFrecuencia = substr($mesConsultaI, 1);
//         $mesConsultaI = $mesConsultaF = "";
//     } else {
//         $queryFrecuencia = substr($queryFrecuencia, 1);
//     }
// } else {
//     $queryFrecuencia = " FKP.*";
// }
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

// if ($mesConsultaI != "") {
//     $filtro .= " AND K.frecuencia NOT IN (5)";
// }

$hoy = date("Y-m-d H:i:s");

if ($_POST["filtro_kpis"] != "") {
    echo '<script> window.location.href = "?pg=kpis_pc/kpis&page=1";</script>';
}

if ($_POST["guardar_semestral"] != "") {
    $metaMEs = [];

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'avance_') === 0) {
            $mes_final = substr($key, 7);

            switch ($mes_final) {
                case 1:
                    $meses_bloque = [8, 9, 10, 11, 12, 1];
                    break;
                case 2:
                    $meses_bloque = [9, 10, 11, 12, 1, 2];
                    break;
                case 3:
                    $meses_bloque = [10, 11, 12, 1, 2, 3];
                    break;
                case 4:
                    $meses_bloque = [11, 12, 1, 2, 3, 4];
                    break;
                case 5:
                    $meses_bloque = [12, 1, 2, 3, 4, 4];
                    break;
                case 6:
                    $meses_bloque = [1, 2, 3, 4, 5, 6];
                    break;
                case 7:
                    $meses_bloque = [2, 3, 4, 5, 6, 7];
                    break;
                case 8:
                    $meses_bloque = [3, 4, 5, 6, 7, 8];
                    break;
                case 9:
                    $meses_bloque = [4, 5, 6, 7, 8, 9];
                    break;
                case 10:
                    $meses_bloque = [5, 6, 7, 8, 9, 10];
                    break;
                case 11:
                    $meses_bloque = [6, 7, 8, 9, 10, 11];
                    break;
                case 12:
                    $meses_bloque = [7, 8, 9, 10, 11, 12];
                    break;
                default:
                    $meses_bloque = [];
            }

            foreach ($meses_bloque as $mes) {
                $metaMEs[$mes] = $value;
            }
        }
    }

    if (!empty($metaMEs)) {
        $enero = $metaMEs[1];
        $febrero = $metaMEs[2];
        $marzo = $metaMEs[3];
        $abril = $metaMEs[4];
        $mayo = $metaMEs[5];
        $junio = $metaMEs[6];
        $julio = $metaMEs[7];
        $agosto = $metaMEs[8];
        $septiembre = $metaMEs[9];
        $octubre = $metaMEs[10];
        $noviembre = $metaMEs[11];
        $diciembre = $metaMEs[12];
    }

    $sentencia = "UPDATE Frecuencia_Kpis SET avance_7 = '$julio', avance_8 = '$agosto', avance_9 = '$septiembre', avance_10 = '$octubre', avance_11 = '$noviembre', avance_12 = '$diciembre',
    avance_1 = '$enero', avance_2 = '$febrero',avance_3 = '$marzo',avance_4 = '$abril', avance_5 = '$mayo', avance_6 = '$junio', updated_at = '$hoy' WHERE id = " . $_POST["id_frecuencia"] . "";

    mysqli_query($connect_kpis, $sentencia);

    $queryFKpi = mysqli_query($connect_kpis, "SELECT * FROM Frecuencia_Kpi_Colaborador WHERE id_frecuencia = " . $_POST["id_frecuencia"] . " AND id_empleado = " . $_SESSION['id_user'] . "");
    $dataFKpi = mysqli_num_rows($queryFKpi);
    if (mysqli_num_rows($queryFKpi) > 0) {
        $sentencia1 = "UPDATE Frecuencia_Kpi_Colaborador SET julio = '$julio', agosto = '$agosto', septiembre = '$septiembre', octubre = '$octubre', noviembre = '$noviembre', diciembre = '$diciembre',
        enero = '$enero', febrero = '$febrero',marzo = '$marzo',abril = '$abril', mayo = '$mayo', junio = '$junio', updated_at = '$hoy' WHERE id_frecuencia = " . $_POST["id_frecuencia"] . " AND id_empleado = " . $_SESSION['id_user'] . " AND id_kpi = " . $_POST["id_kpi"] . "";
    } else {
        $sentencia1 = "INSERT INTO Frecuencia_Kpi_Colaborador (id_kpi,id_empresa,id_frecuencia,id_empleado,tipo,julio,agosto,septiembre,octubre,noviembre,diciembre,enero,febrero,marzo,abril,mayo,junio,created_at)
        VALUES (" . $_POST["id_kpi"] . "," . $_SESSION["id_empresa"] . "," . $_POST["id_frecuencia"] . "," . $_SESSION['id_user'] . ",4,'$julio','$agosto','$septiembre','$octubre','$noviembre','$diciembre','$enero'
        ,'$febrero','$marzo','$abril','$mayo','$junio', '$hoy')";
    }

    mysqli_query($connect_kpis, $sentencia1);

    echo '<script> window.location.href = "?pg=kpis_pc/kpis&kpi_=' . $_POST["id_kpi"] . '' . $pagina . '";</script>';
}

if ($_POST["guardar_bimestral"] != "") {
    $metaMEs = [];

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'avance_') === 0) {
            $mes_final = substr($key, 7);

            switch ($mes_final) {
                case 1:
                    $meses_bloque = [12, 1];
                    break;
                case 2:
                    $meses_bloque = [1, 2];
                    break;
                case 3:
                    $meses_bloque = [2, 3];
                    break;
                case 4:
                    $meses_bloque = [3, 4];
                    break;
                case 5:
                    $meses_bloque = [4, 5];
                    break;
                case 6:
                    $meses_bloque = [5, 6];
                    break;
                case 7:
                    $meses_bloque = [6, 7];
                    break;
                case 8:
                    $meses_bloque = [7, 8];
                    break;
                case 9:
                    $meses_bloque = [8, 9];
                    break;
                case 10:
                    $meses_bloque = [9, 10];
                    break;
                case 11:
                    $meses_bloque = [10, 11];
                    break;
                case 12:
                    $meses_bloque = [11, 12];
                    break;
                default:
                    $meses_bloque = [];
            }

            foreach ($meses_bloque as $mes) {
                $metaMEs[$mes] = $value;
            }
        }
    }

    if (!empty($metaMEs)) {
        $enero = $metaMEs[1];
        $febrero = $metaMEs[2];
        $marzo = $metaMEs[3];
        $abril = $metaMEs[4];
        $mayo = $metaMEs[5];
        $junio = $metaMEs[6];
        $julio = $metaMEs[7];
        $agosto = $metaMEs[8];
        $septiembre = $metaMEs[9];
        $octubre = $metaMEs[10];
        $noviembre = $metaMEs[11];
        $diciembre = $metaMEs[12];
    }

    $sentencia = "UPDATE Frecuencia_Kpis SET avance_7 = '$julio', avance_8 = '$agosto', avance_9 = '$septiembre', avance_10 = '$octubre', avance_11 = '$noviembre', avance_12 = '$diciembre',
    avance_1 = '$enero', avance_2 = '$febrero',avance_3 = '$marzo',avance_4 = '$abril', avance_5 = '$mayo', avance_6 = '$junio', updated_at = '$hoy' WHERE id = " . $_POST["id_frecuencia"] . "";

    mysqli_query($connect_kpis, $sentencia);

    $queryFKpi = mysqli_query($connect_kpis, "SELECT * FROM Frecuencia_Kpi_Colaborador WHERE id_frecuencia = " . $_POST["id_frecuencia"] . " AND id_empleado = " . $_SESSION['id_user'] . "");
    $dataFKpi = mysqli_num_rows($queryFKpi);
    if (mysqli_num_rows($queryFKpi) > 0) {
        $sentencia1 = "UPDATE Frecuencia_Kpi_Colaborador SET julio = '$julio', agosto = '$agosto', septiembre = '$septiembre', octubre = '$octubre', noviembre = '$noviembre', diciembre = '$diciembre',
        enero = '$enero', febrero = '$febrero',marzo = '$marzo',abril = '$abril', mayo = '$mayo', junio = '$junio', updated_at = '$hoy' WHERE id_frecuencia = " . $_POST["id_frecuencia"] . " AND id_empleado = " . $_SESSION['id_user'] . " AND id_kpi = " . $_POST["id_kpi"] . "";
    } else {
        $sentencia1 = "INSERT INTO Frecuencia_Kpi_Colaborador (id_kpi,id_empresa,id_frecuencia,id_empleado,tipo,julio,agosto,septiembre,octubre,noviembre,diciembre,enero,febrero,marzo,abril,mayo,junio,created_at)
        VALUES (" . $_POST["id_kpi"] . "," . $_SESSION["id_empresa"] . "," . $_POST["id_frecuencia"] . "," . $_SESSION['id_user'] . ",2,'$julio','$agosto','$septiembre','$octubre','$noviembre','$diciembre','$enero'
        ,'$febrero','$marzo','$abril','$mayo','$junio', '$hoy')";
    }

    mysqli_query($connect_kpis, $sentencia1);

    echo '<script> window.location.href = "?pg=kpis_pc/kpis&kpi_=' . $_POST["id_kpi"] . '' . $pagina . '";</script>';
}

if ($_POST["guardar_trimestral"] != "") {

    $metaMEs = [];

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'avance_') === 0) {
            $mes_final = substr($key, 7);

            switch ($mes_final) {
                case 1:
                    $meses_bloque = [11, 12, 1];
                    break;
                case 2:
                    $meses_bloque = [12, 1, 2];
                    break;
                case 3:
                    $meses_bloque = [1, 2, 3];
                    break;
                case 4:
                    $meses_bloque = [2, 3, 4];
                    break;
                case 5:
                    $meses_bloque = [3, 4, 5];
                    break;
                case 6:
                    $meses_bloque = [4, 5, 6];
                    break;
                case 7:
                    $meses_bloque = [5, 6, 7];
                    break;
                case 8:
                    $meses_bloque = [6, 7, 8];
                    break;
                case 9:
                    $meses_bloque = [7, 8, 9];
                    break;
                case 10:
                    $meses_bloque = [8, 9, 10];
                    break;
                case 11:
                    $meses_bloque = [9, 10, 11];
                    break;
                case 12:
                    $meses_bloque = [10, 11, 12];
                    break;
                default:
                    $meses_bloque = [];
            }

            foreach ($meses_bloque as $mes) {
                $metaMEs[$mes] = $value;
            }
        }
    }

    if (!empty($metaMEs)) {
        $enero = $metaMEs[1];
        $febrero = $metaMEs[2];
        $marzo = $metaMEs[3];
        $abril = $metaMEs[4];
        $mayo = $metaMEs[5];
        $junio = $metaMEs[6];
        $julio = $metaMEs[7];
        $agosto = $metaMEs[8];
        $septiembre = $metaMEs[9];
        $octubre = $metaMEs[10];
        $noviembre = $metaMEs[11];
        $diciembre = $metaMEs[12];
    }

    $sentencia = "UPDATE Frecuencia_Kpis SET avance_7 = '$julio', avance_8 = '$agosto', avance_9 = '$septiembre', avance_10 = '$octubre', avance_11 = '$noviembre', avance_12 = '$diciembre',
    avance_1 = '$enero', avance_2 = '$febrero',avance_3 = '$marzo',avance_4 = '$abril', avance_5 = '$mayo', avance_6 = '$junio', updated_at = '$hoy' WHERE id = " . $_POST["id_frecuencia"] . "";

    mysqli_query($connect_kpis, $sentencia);

    $queryFKpi = mysqli_query($connect_kpis, "SELECT * FROM Frecuencia_Kpi_Colaborador WHERE id_frecuencia = " . $_POST["id_frecuencia"] . " AND id_empleado = " . $_SESSION['id_user'] . "");
    $dataFKpi = mysqli_num_rows($queryFKpi);
    if (mysqli_num_rows($queryFKpi) > 0) {
        $sentencia1 = "UPDATE Frecuencia_Kpi_Colaborador SET julio = '$julio', agosto = '$agosto', septiembre = '$septiembre', octubre = '$octubre', noviembre = '$noviembre', diciembre = '$diciembre',
        enero = '$enero', febrero = '$febrero',marzo = '$marzo',abril = '$abril', mayo = '$mayo', junio = '$junio', updated_at = '$hoy' WHERE id_frecuencia = " . $_POST["id_frecuencia"] . " AND id_empleado = " . $_SESSION['id_user'] . " AND id_kpi = " . $_POST["id_kpi"] . "";
    } else {
        $sentencia1 = "INSERT INTO Frecuencia_Kpi_Colaborador (id_kpi,id_empresa,id_frecuencia,id_empleado,tipo,julio,agosto,septiembre,octubre,noviembre,diciembre,enero,febrero,marzo,abril,mayo,junio,created_at)
        VALUES (" . $_POST["id_kpi"] . "," . $_SESSION["id_empresa"] . "," . $_POST["id_frecuencia"] . "," . $_SESSION['id_user'] . ",3,'$julio','$agosto','$septiembre','$octubre','$noviembre','$diciembre','$enero'
        ,'$febrero','$marzo','$abril','$mayo','$junio', '$hoy')";
    }

    mysqli_query($connect_kpis, $sentencia1);

    echo '<script> window.location.href = "?pg=kpis_pc/kpis&kpi_=' . $_POST["id_kpi"] . '' . $pagina . '";</script>';
}

if ($_POST["guardar_cuatrimestral"] != "") {
    $metaMEs = [];

    foreach ($_POST as $key => $value) {
        if (strpos($key, 'avance_') === 0) {
            $mes_final = substr($key, 7);

            switch ($mes_final) {
                case 1:
                    $meses_bloque = [10, 11, 12, 1];
                    break;
                case 2:
                    $meses_bloque = [11, 12, 1, 2];
                    break;
                case 3:
                    $meses_bloque = [12, 1, 2, 3];
                    break;
                case 4:
                    $meses_bloque = [1, 2, 3, 4];
                    break;
                case 5:
                    $meses_bloque = [2, 3, 4, 5];
                    break;
                case 6:
                    $meses_bloque = [3, 4, 5, 6];
                    break;
                case 7:
                    $meses_bloque = [4, 5, 6, 7];
                    break;
                case 8:
                    $meses_bloque = [5, 6, 7, 8];
                    break;
                case 9:
                    $meses_bloque = [6, 7, 8, 9];
                    break;
                case 10:
                    $meses_bloque = [7, 8, 9, 10];
                    break;
                case 11:
                    $meses_bloque = [8, 9, 10, 11];
                    break;
                case 12:
                    $meses_bloque = [9, 10, 11, 12];
                    break;
                default:
                    $meses_bloque = [];
            }

            foreach ($meses_bloque as $mes) {
                $metaMEs[$mes] = $value;
            }
        }
    }

    if (!empty($metaMEs)) {
        $enero = $metaMEs[1];
        $febrero = $metaMEs[2];
        $marzo = $metaMEs[3];
        $abril = $metaMEs[4];
        $mayo = $metaMEs[5];
        $junio = $metaMEs[6];
        $julio = $metaMEs[7];
        $agosto = $metaMEs[8];
        $septiembre = $metaMEs[9];
        $octubre = $metaMEs[10];
        $noviembre = $metaMEs[11];
        $diciembre = $metaMEs[12];
    }

    $sentencia = "UPDATE Frecuencia_Kpis SET avance_7 = '$julio', avance_8 = '$agosto', avance_9 = '$septiembre', avance_10 = '$octubre', avance_11 = '$noviembre', avance_12 = '$diciembre',
    avance_1 = '$enero', avance_2 = '$febrero',avance_3 = '$marzo',avance_4 = '$abril', avance_5 = '$mayo', avance_6 = '$junio', updated_at = '$hoy' WHERE id = " . $_POST["id_frecuencia"] . "";

    mysqli_query($connect_kpis, $sentencia);

    $queryFKpi = mysqli_query($connect_kpis, "SELECT * FROM Frecuencia_Kpi_Colaborador WHERE id_frecuencia = " . $_POST["id_frecuencia"] . " AND id_empleado = " . $_SESSION['id_user'] . "");
    $dataFKpi = mysqli_num_rows($queryFKpi);
    if (mysqli_num_rows($queryFKpi) > 0) {
        $sentencia1 = "UPDATE Frecuencia_Kpi_Colaborador SET julio = '$julio', agosto = '$agosto', septiembre = '$septiembre', octubre = '$octubre', noviembre = '$noviembre', diciembre = '$diciembre',
        enero = '$enero', febrero = '$febrero',marzo = '$marzo',abril = '$abril', mayo = '$mayo', junio = '$junio', updated_at = '$hoy' WHERE id_frecuencia = " . $_POST["id_frecuencia"] . " AND id_empleado = " . $_SESSION['id_user'] . " AND id_kpi = " . $_POST["id_kpi"] . "";
    } else {
        $sentencia1 = "INSERT INTO Frecuencia_Kpi_Colaborador (id_kpi,id_empresa,id_frecuencia,id_empleado,tipo,julio,agosto,septiembre,octubre,noviembre,diciembre,enero,febrero,marzo,abril,mayo,junio,created_at)
        VALUES (" . $_POST["id_kpi"] . "," . $_SESSION["id_empresa"] . "," . $_POST["id_frecuencia"] . "," . $_SESSION['id_user'] . ",46,'$julio','$agosto','$septiembre','$octubre','$noviembre','$diciembre','$enero'
        ,'$febrero','$marzo','$abril','$mayo','$junio', '$hoy')";
    }

    mysqli_query($connect_kpis, $sentencia1);

    echo '<script> window.location.href = "?pg=kpis_pc/kpis&kpi_=' . $_POST["id_kpi"] . '' . $pagina . '";</script>';
}

if ($_POST["guardar_mensual"] != "") {
    $sentencia = "UPDATE Frecuencia_Kpis SET avance_7 = '" . $_POST["avance_7"] . "', avance_8 = '" . $_POST["avance_8"] . "', avance_9 = '" . $_POST["avance_9"] . "', avance_10 = '" . $_POST["avance_10"] . "', avance_11 = '" . $_POST["avance_11"] . "', avance_12 = '" . $_POST["avance_12"] . "',
    avance_1 = '" . $_POST["avance_1"] . "', avance_2 = '" . $_POST["avance_2"] . "',avance_3 = '" . $_POST["avance_3"] . "',avance_4 = '" . $_POST["avance_4"] . "', avance_5 = '" . $_POST["avance_5"] . "', avance_6 = '" . $_POST["avance_6"] . "', updated_at = '$hoy' WHERE id = " . $_POST["id_frecuencia"] . "";

    mysqli_query($connect_kpis, $sentencia);

    $queryFKpi = mysqli_query($connect_kpis, "SELECT * FROM Frecuencia_Kpi_Colaborador WHERE id_frecuencia = " . $_POST["id_frecuencia"] . " AND id_empleado = " . $_SESSION['id_user'] . "");
    $dataFKpi = mysqli_num_rows($queryFKpi);
    if (mysqli_num_rows($queryFKpi) > 0) {
        $sentencia1 = "UPDATE Frecuencia_Kpi_Colaborador SET julio = '" . $_POST["avance_7"] . "', agosto = '" . $_POST["avance_8"] . "', septiembre = '" . $_POST["avance_9"] . "', octubre = '" . $_POST["avance_10"] . "', noviembre = '" . $_POST["avance_11"] . "', diciembre = '" . $_POST["avance_12"] . "',
        enero = '" . $_POST["avance_1"] . "', febrero = '" . $_POST["avance_2"] . "',marzo = '" . $_POST["avance_3"] . "',abril = '" . $_POST["avance_4"] . "', mayo = '" . $_POST["avance_5"] . "', junio = '" . $_POST["avance_6"] . "', updated_at = '$hoy' WHERE id_frecuencia = " . $_POST["id_frecuencia"] . " AND id_empleado = " . $_SESSION['id_user'] . " AND id_kpi = " . $_POST["id_kpi"] . "";
    } else {
        $sentencia1 = "INSERT INTO Frecuencia_Kpi_Colaborador (id_kpi,id_empresa,id_frecuencia,id_empleado,tipo,julio,agosto,septiembre,octubre,noviembre,diciembre,enero,febrero,marzo,abril,mayo,junio,created_at)
        VALUES (" . $_POST["id_kpi"] . "," . $_SESSION["id_empresa"] . "," . $_POST["id_frecuencia"] . "," . $_SESSION['id_user'] . ",1,'" . $_POST["avance_7"] . "','" . $_POST["avance_8"] . "','" . $_POST["avance_9"] . "','" . $_POST["avance_10"] . "','" . $_POST["avance_11"] . "','" . $_POST["avance_12"] . "','" . $_POST["avance_1"] . "'
        ,'" . $_POST["avance_2"] . "','" . $_POST["avance_3"] . "','" . $_POST["avance_4"] . "','" . $_POST["avance_5"] . "','" . $_POST["avance_6"] . "', '$hoy')";
    }

    mysqli_query($connect_kpis, $sentencia1);

    echo '<script> window.location.href = "?pg=kpis_pc/kpis&kpi_=' . $_POST["id_kpi"] . '' . $pagina . '";</script>';
}

if ($_POST["guardar_anual"] != "") {
    $sentencia = "UPDATE Frecuencia_Kpis SET avance_7 = '" . $_POST["avance_anual"] . "', avance_8 = '" . $_POST["avance_anual"] . "', avance_9 = '" . $_POST["avance_anual"] . "', avance_10 = '" . $_POST["avance_anual"] . "', avance_11 = '" . $_POST["avance_anual"] . "', avance_12 = '" . $_POST["avance_anual"] . "',
    avance_1 = '" . $_POST["avance_anual"] . "', avance_2 = '" . $_POST["avance_anual"] . "',avance_3 = '" . $_POST["avance_anual"] . "',avance_4 = '" . $_POST["avance_anual"] . "', avance_5 = '" . $_POST["avance_anual"] . "', avance_6 = '" . $_POST["avance_anual"] . "', updated_at = '$hoy' WHERE id = " . $_POST["id_frecuencia"] . "";
// echo "<br>$sentencia";
    mysqli_query($connect_kpis, $sentencia);

    $queryFKpi = mysqli_query($connect_kpis, "SELECT * FROM Frecuencia_Kpi_Colaborador WHERE id_frecuencia = " . $_POST["id_frecuencia"] . " AND id_empleado = " . $_SESSION['id_user'] . "");
    $dataFKpi = mysqli_num_rows($queryFKpi);
    if (mysqli_num_rows($queryFKpi) > 0) {
        $sentencia1 = "UPDATE Frecuencia_Kpi_Colaborador SET julio = '" . $_POST["avance_anual"] . "', agosto = '" . $_POST["avance_anual"] . "', septiembre = '" . $_POST["avance_9"] . "', octubre = '" . $_POST["avance_anual"] . "', noviembre = '" . $_POST["avance_anual"] . "', diciembre = '" . $_POST["avance_anual"] . "',
        enero = '" . $_POST["avance_anual"] . "', febrero = '" . $_POST["avance_anual"] . "',marzo = '" . $_POST["avance_anual"] . "',abril = '" . $_POST["avance_anual"] . "', mayo = '" . $_POST["avance_anual"] . "', junio = '" . $_POST["avance_anual"] . "', updated_at = '$hoy' WHERE id_frecuencia = " . $_POST["id_frecuencia"] . " AND id_empleado = " . $_SESSION['id_user'] . " AND id_kpi = " . $_POST["id_kpi"] . "";
    } else {
        $sentencia1 = "INSERT INTO Frecuencia_Kpi_Colaborador (id_kpi,id_empresa,id_frecuencia,id_empleado,tipo,julio,agosto,septiembre,octubre,noviembre,diciembre,enero,febrero,marzo,abril,mayo,junio,created_at)
        VALUES (" . $_POST["id_kpi"] . "," . $_SESSION["id_empresa"] . "," . $_POST["id_frecuencia"] . "," . $_SESSION['id_user'] . ",3,'" . $_POST["avance_7"] . "','" . $_POST["avance_anual"] . "','" . $_POST["avance_anual"] . "','" . $_POST["avance_anual"] . "','" . $_POST["avance_anual"] . "','" . $_POST["avanceavance_anual_12"] . "','" . $_POST["avance_anual"] . "'
        ,'" . $_POST["avance_anual"] . "','" . $_POST["avance_anual"] . "','" . $_POST["avance_anual"] . "','" . $_POST["avance_anual"] . "','" . $_POST["avance_anual"] . "', '$hoy')";
    }

    mysqli_query($connect_kpis, $sentencia1);

    echo '<script> window.location.href = "?pg=kpis_pc/kpis&kpi_=' . $_POST["id_kpi"] . '' . $pagina . '";</script>';
}
include("views/kpis_pc/detalle/modal_tipo_rol.php");
include("views/kpis_pc/detalle/modal_tipo.php");

$querySM67 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 6 AND id_submenu = 39");
$dataSM67 = mysqli_fetch_array($querySM67);
include("views/kpis_pc/etiquetas.php");
$Array_tipo_kpi_PC1 = array(
    array("1", $etiquetaKpiT),
    array("2", $etiquetaKpiE),
);
?>
<script>
    $(".menu_section").addClass("active");
    jQuery("#menu_kpi").css("display", "none");
    $("#bt_kpis").addClass("current-page");
</script>

<link rel="stylesheet" href="<?php echo $url; ?>css/okrs.css">
<style>
    .card,
    .card-header,
    .card-body,
    .card-footer {
        background-color: white !important;
    }

    .h5,
    h5 {
        font-size: 0.9rem !important;
        color: black !important;
    }

    .h4,
    h4 {
        font-size: 1rem !important;
        color: black !important;
    }

    .dropdown-item {
        display: contents !important;
    }

    .circleSpan {
        display: inline-block;
        /* Para que el span tenga un comportamiento adecuado como bloque */
        width: 20px;
        /* Ancho del círculo */
        height: 20px;
        /* Alto del círculo */
        background-color: #ffc107;
        /* Color de fondo */
        text-align: center;
        /* Centra el texto horizontalmente */
        line-height: 20px;
        /* Centra el texto verticalmente */
        border-radius: 50%;
        /* Hace que el span sea redondo */
        font-size: 12px;
        /* Tamaño del texto */

    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #FFFFFF !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4 style="font-family: Lato-Black !important;color: #365189 !important;font-size: 1.5rem !important;"><i class="fas fa-chart-line" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM67["nombre"]; ?></h4>
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
                    <?php include("views/kpis_pc/detalle/filtro_kpis.php"); ?>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div id="accordionIconsKpis" class="accordion-icons" role="tablist">
                    <?php

                    $queryKpis = mysqli_query($connect_kpis, "SELECT K.* $mesConsultaI $mesConsultaF $periodoFill
                    FROM Kpis K
                    INNER JOIN Frecuencia_Kpis FKP ON FKP.id_kpi = K.id
                    WHERE K.id_empresa = " . $_SESSION['id_empresa'] . " AND K.anio = " . $_SESSION['anio_fill'] . " $filtro ORDER BY K.indicador ASC");
                    $total_registros = mysqli_num_rows($queryKpis);
                    $queryKpisP = mysqli_query($connect_kpis, "SELECT K.* $mesConsultaI $mesConsultaF $periodoFill
                    FROM Kpis K
                    INNER JOIN Frecuencia_Kpis FKP ON FKP.id_kpi = K.id
                    WHERE K.id_empresa = " . $_SESSION['id_empresa'] . " AND K.anio = " . $_SESSION['anio_fill'] . " $filtro ORDER BY K.indicador ASC LIMIT $inicio, $registros_por_pagina");
                    $total_paginas = ceil($total_registros / $registros_por_pagina);
                    $sumSeguimiento = $sumAvance = 0;
                    if (mysqli_num_rows($queryKpis) == 0 || mysqli_num_rows($queryKpisP) == 0) {
                        echo '
                        <div class="alert alert-primary" role="alert">
                            No se encontraron KPIS asignados para los filtros seleccionados.
                        </div>
                        ';
                    }

                    while ($dataKpis = mysqli_fetch_array($queryKpisP)) {
                        $fechaHoy = date("Y-m");
                        $mesHoy = date("m");

                        if ($mesHoy == "01" || $mesHoy == "02" || $mesHoy == "03" || $mesHoy == "04" || $mesHoy == "05" || $mesHoy == "06") {
                            $fechaenero = date("Y") . "-01";
                            $fechafebrero = date("Y") . "-02";
                            $fechamarzo = date("Y") . "-03";
                            $fechaabril = date("Y") . "-04";
                            $fechamayo = date("Y") . "-05";
                            $fechajunio = date("Y") . "-06";
                        } else {
                            $fechaenero = (date("Y") + 1) . "-01";
                            $fechafebrero = (date("Y") + 1) . "-02";
                            $fechamarzo = (date("Y") + 1) . "-03";
                            $fechaabril = (date("Y") + 1) . "-04";
                            $fechamayo = (date("Y") + 1) . "-05";
                            $fechajunio = (date("Y") + 1) . "-06";
                        }
                        $fechajulio = date("Y") . "-07";
                        $fechaagosto = date("Y") . "-08";
                        $fechaseptiembre = date("Y") . "-09";
                        $fechaoctubre = date("Y") . "-10";
                        $fechanoviembre = date("Y") . "-11";
                        $fechadiciembre = date("Y") . "-12";

                        // $mes_actual = date("n");
                        // $anio_actual = date("Y");

                        // $meses_permitidos = obtenerMesesPermitidos($mes_actual, $anio_actual, $mesInicio, (int)$dataKpis["frecuencia"]);
                        // print_r($meses_permitidos);

                        $bloqueo1 = $bloqueo2 = $bloqueo3 = $bloqueo4 = $bloqueo5 = $bloqueo6 = $bloqueo7 = $bloqueo8 = $bloqueo9 = $bloqueo10 = $bloqueo11 = $bloqueo12 = false;

                        // if ($fechaHoy == $fechaenero) {
                        //     $bloqueo1 = true;
                        // } else if ($fechaHoy < $fechaenero) {
                        //     $bloqueo1 = true;
                        // }
                        // if ($fechaHoy == $fechafebrero) {
                        //     $bloqueo2 = true;
                        // } else if ($fechaHoy < $fechafebrero) {
                        //     $bloqueo2 = true;
                        // }
                        // if ($fechaHoy == $fechamarzo) {
                        //     $bloqueo3 = true;
                        // } else if ($fechaHoy < $fechamarzo) {
                        //     $bloqueo3 = true;
                        // }
                        // if ($fechaHoy == $fechaabril) {
                        //     $bloqueo4 = true;
                        // } else if ($fechaHoy < $fechaabril) {
                        //     $bloqueo4 = true;
                        // }
                        // if ($fechaHoy == $fechamayo) {
                        //     $bloqueo5 = true;
                        // } else if ($fechaHoy < $fechamayo) {
                        //     $bloqueo5 = true;
                        // }
                        // if ($fechaHoy == $fechajunio) {
                        //     $bloqueo6 = true;
                        // } else if ($fechaHoy < $fechajunio) {
                        //     $bloqueo6 = true;
                        // }
                        // if ($fechaHoy == $fechajulio) {
                        //     $bloqueo7 = true;
                        // } else if ($fechaHoy < $fechajulio) {
                        //     $bloqueo7 = true;
                        // }
                        // if ($fechaHoy == $fechaagosto) {
                        //     $bloqueo8 = true;
                        // } else if ($fechaHoy < $fechaagosto) {
                        //     $bloqueo8 = true;
                        // }
                        // if ($fechaHoy == $fechaseptiembre) {
                        //     $bloqueo9 = true;
                        // } else if ($fechaHoy < $fechaseptiembre) {
                        //     $bloqueo9 = true;
                        // }
                        // if ($fechaHoy == $fechaoctubre) {
                        //     $bloqueo10 = true;
                        // } else if ($fechaHoy < $fechaoctubre) {
                        //     $bloqueo10 = true;
                        // }
                        // if ($fechaHoy == $fechanoviembre) {
                        //     $bloqueo11 = true;
                        // } else if ($fechaHoy < $fechanoviembre) {
                        //     $bloqueo11 = true;
                        // }
                        // if ($fechaHoy == $fechadiciembre) {
                        //     $bloqueo12 = true;
                        // } else if ($fechaHoy < $fechadiciembre) {
                        //     $bloqueo12 = true;
                        // }

                        $sumaMeta = $sumaMetaM = $sumaMetaB = $sumaMetaF = $contFiltroFecha = 0;
                        $responsables = "";
                        $queryKpisC = mysqli_query($connect_kpis, "SELECT * FROM Kpis_Colaborador WHERE id_empresa = " . $_SESSION['id_empresa'] . " AND id_kpi = " . $dataKpis["id"] . "");
                        while ($dataKpisC = mysqli_fetch_array($queryKpisC)) {

                            $colaborador = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '" . $dataKpisC['id_colaborador'] . "' id_empresa = " . $_SESSION['id_empresa']. "");
                            $dataEmpleado = mysqli_fetch_array($colaborador);

                            if (!$dataEmpleado["foto"]) {
                                $dataEmpleado["foto"] = "img_default.jpg";
                            }

                                $photo = $dataEmpleado["foto"];
                                $nombreEmpleado = $dataEmpleado["nombre"];
                                $idColaborador = $dataEmpleado["id"];
                                include("views/avatars/avatar.php");
                                // $responsables .= $avatarTablas;

                            $responsables .= '<img loading="lazy" src="' . $url . '/recursos/' . $dataEmpleado["foto"] . '" class="foto_min" title="' . $dataEmpleado["nombre"] . '" style="width: 35px !important;height: 35px !important;" onclick="javascript:Profile(' . $dataEmpleado["id"] . ',1)">';
                        }

                        $frecuencia = mysqli_query($connect_kpis, "SELECT $queryFrecuencia FROM Frecuencia_Kpis FKP WHERE FKP.id = " . $dataKpis['obj_meses'] . "$filtroKpi AND id_empresa = ". $_SESSION['id_empresa']."");
                        $dataFrecuencia = mysqli_fetch_array($frecuencia);

                        if ($_SESSION["periodo_fin_fill"] > 0 && $_SESSION["periodo_ini_fill"] > 0) {

                            if ($dataFrecuencia["julio"] != "" || $dataFrecuencia["julio"] != null) {
                                $sumaMetaF = $sumaMetaF + $dataFrecuencia["julio"];
                                $contFiltroFecha++;
                            }
                            if ($dataFrecuencia["agosto"] != "" || $dataFrecuencia["agosto"] != null) {
                                $sumaMetaF = $sumaMetaF + $dataFrecuencia["agosto"];
                                $contFiltroFecha++;
                            }
                            if ($dataFrecuencia["septiembre"] != "" || $dataFrecuencia["septiembre"] != null) {
                                $sumaMetaF = $sumaMetaF + $dataFrecuencia["septiembre"];
                                $contFiltroFecha++;
                            }
                            if ($dataFrecuencia["octubre"] != "" || $dataFrecuencia["octubre"] != null) {
                                $sumaMetaF = $sumaMetaF + $dataFrecuencia["octubre"];
                                $contFiltroFecha++;
                            }
                            if ($dataFrecuencia["noviembre"] != "" || $dataFrecuencia["noviembre"] != null) {
                                $sumaMetaF = $sumaMetaF + $dataFrecuencia["noviembre"];
                                $contFiltroFecha++;
                            }
                            if ($dataFrecuencia["diciembre"] != "" || $dataFrecuencia["diciembre"] != null) {
                                $sumaMetaF = $sumaMetaF + $dataFrecuencia["diciembre"];
                                $contFiltroFecha++;
                            }
                            if ($dataFrecuencia["enero"] != "" || $dataFrecuencia["enero"] != null) {
                                $sumaMetaF = $sumaMetaF + $dataFrecuencia["enero"];
                                $contFiltroFecha++;
                            }
                            if ($dataFrecuencia["febrero"] != "" || $dataFrecuencia["febrero"] != null) {
                                $sumaMetaF = $sumaMetaF + $dataFrecuencia["febrero"];
                                $contFiltroFecha++;
                            }
                            if ($dataFrecuencia["marzo"] != "" || $dataFrecuencia["marzo"] != null) {
                                $sumaMetaF = $sumaMetaF + $dataFrecuencia["marzo"];
                                $contFiltroFecha++;
                            }
                            if ($dataFrecuencia["abril"] != "" || $dataFrecuencia["abril"] != null) {
                                $sumaMetaF = $sumaMetaF + $dataFrecuencia["abril"];
                                $contFiltroFecha++;
                            }
                            if ($dataFrecuencia["mayo"] != "" || $dataFrecuencia["mayo"] != null) {
                                $sumaMetaF = $sumaMetaF + $dataFrecuencia["mayo"];
                                $contFiltroFecha++;
                            }
                            if ($dataFrecuencia["junio"] != "" || $dataFrecuencia["junio"] != null) {
                                $sumaMetaF = $sumaMetaF + $dataFrecuencia["junio"];
                                $contFiltroFecha++;
                            }


                            if ($dataKpis["tipo_resultado"] == 3) {

                                switch ($_SESSION["periodo_fin_fill"]) {
                                    case 7:
                                        $metaFiltro = $dataFrecuencia["julio"];
                                        break;
                                    case 8:
                                        $metaFiltro = $dataFrecuencia["agosto"];
                                        break;
                                    case 9:
                                        $metaFiltro = $dataFrecuencia["septiembre"];
                                        break;
                                    case 10:
                                        $metaFiltro = $dataFrecuencia["octubre"];
                                        break;
                                    case 11:
                                        $metaFiltro = $dataFrecuencia["noviembre"];
                                        break;
                                    case 12:
                                        $metaFiltro = $dataFrecuencia["diciembre"];
                                        break;
                                    case 1:
                                        $metaFiltro = $dataFrecuencia["enero"];
                                        break;
                                    case 2:
                                        $metaFiltro = $dataFrecuencia["febrero"];
                                        break;
                                    case 3:
                                        $metaFiltro = $dataFrecuencia["marzo"];
                                        break;
                                    case 4:
                                        $metaFiltro = $dataFrecuencia["abril"];
                                        break;
                                    case 5:
                                        $metaFiltro = $dataFrecuencia["mayo"];
                                        break;
                                    case 6:
                                        $metaFiltro = $dataFrecuencia["junio"];
                                        break;
                                }
                            } else if ($dataKpis["tipo_resultado"] == 1) {
                                $metaFiltro = round(($sumaMetaF / $contFiltroFecha), 2);
                            } else {
                                $metaFiltro = $sumaMetaF;
                            }
                        } else {
                            $metaFiltro = $dataKpis["meta"];
                        }

                        if ($dataKpis["frecuencia"] == 5) {
                            $queryEmpresa = mysqli_query($connect_valentina, "SELECT * FROM Empresas WHERE id = '" . $_SESSION["id_empresa"] . "'");
                            $dataEmpresa = mysqli_fetch_array($queryEmpresa);
                            // $mesInicio = (int)$dataEmpresa["mes_inicio"];
                            $mesInicio = $dataEmpresa["mes_inicio"];
                            $mesFin = $dataEmpresa["mes_fin"];
                            $inicioMes = new DateTime($dataEmpresa["mes_inicio"]);
                            $mes_inicio = (int)$inicioMes->format('m');

                            switch ($mes_inicio) {
                                case 7:
                                    if ($dataFrecuencia["julio"] != "" || $dataFrecuencia["julio"] != null) {
                                        $sumSeguimiento = $dataFrecuencia["avance_7"];
                                    }
                                    break;
                                case 8:
                                    if ($dataFrecuencia["agosto"] != "" || $dataFrecuencia["agosto"] != null) {
                                        $sumSeguimiento = $dataFrecuencia["avance_8"];
                                    }
                                    break;
                                case 9:
                                    if ($dataFrecuencia["septiembre"] != "" || $dataFrecuencia["septiembre"] != null) {
                                        $sumSeguimiento = $dataFrecuencia["avance_9"];
                                    }
                                    break;
                                case 10:
                                    if ($dataFrecuencia["octubre"] != "" || $dataFrecuencia["octubre"] != null) {
                                        $sumSeguimiento = $dataFrecuencia["avance_10"];
                                    }
                                    break;
                                case 11:
                                    if ($dataFrecuencia["noviembre"] != "" || $dataFrecuencia["noviembre"] != null) {
                                        $sumSeguimiento = $dataFrecuencia["avance_11"];
                                    }
                                    break;
                                case 12:
                                    if ($dataFrecuencia["diciembre"] != "" || $dataFrecuencia["diciembre"] != null) {
                                        $sumSeguimiento = $dataFrecuencia["avance_12"];
                                    }
                                    break;
                                case 1:
                                    if ($dataFrecuencia["enero"] != "" || $dataFrecuencia["enero"] != null) {
                                        $sumSeguimiento = $dataFrecuencia["avance_1"];
                                    }
                                    break;
                                case 2:
                                    if ($dataFrecuencia["febrero"] != "" || $dataFrecuencia["febrero"] != null) {
                                        $sumSeguimiento = $dataFrecuencia["avance_2"];
                                    }
                                    break;
                                case 3:
                                    if ($dataFrecuencia["marzo"] != "" || $dataFrecuencia["marzo"] != null) {
                                        $sumSeguimiento = $dataFrecuencia["avance_3"];
                                    }
                                    break;
                                case 4:
                                    if ($dataFrecuencia["abril"] != "" || $dataFrecuencia["abril"] != null) {
                                        $sumSeguimiento = $dataFrecuencia["avance_4"];
                                    }
                                    break;
                                case 5:
                                    if ($dataFrecuencia["mayo"] != "" || $dataFrecuencia["mayo"] != null) {
                                        $sumSeguimiento = $dataFrecuencia["avance_5"];
                                    }
                                    break;
                                case 6:
                                    if ($dataFrecuencia["junio"] != "" || $dataFrecuencia["junio"] != null) {
                                        $sumSeguimiento = $dataFrecuencia["avance_6"];
                                    }
                                    break;
                            }
                        } else {
                            include("views/kpis_pc/frecuencia.php");
                        }

                        include("views/kpis_pc/progreso_mes.php");

                        if ($sumSeguimiento != "") {
                            $progresoF = round((($sumSeguimiento) / $dataKpis["meta"]) * 100, 2);
                            $progresoF1 = round((($sumSeguimiento) / $metaFiltro) * 100, 2);
                            // if ($dataKpis["tipo_calculo"] == 2) {
                            //     $progresoF = round(($meta / $sumSeguimiento) * 100, 2);
                            //     $progresoF1 = round(($metaFiltro / $sumSeguimiento) * 100, 2);
                            // }

                            if ($dataKpis["tipo_calculo"] == 2) {
                                // $progresoF = round(($meta / $sumSeguimiento) * 100, 2);
                                // if (is_infinite($progresoF) || is_nan($progresoF)) {

                                $progresoF = CalculoProgresoDesMes($meta, $sumSeguimiento);
                                $progresoF1 = CalculoProgresoDesMes($metaFiltro, $sumSeguimiento);
                                // }
                            }

                            if (is_infinite($progresoF)) {
                                $progresoF = 0;
                            }
                            if (is_nan($progresoF)) {
                                $progresoF = 0;
                            }
                            if ($progresoF > 100) {
                                $progresoF = 100;
                            }
                            if (is_infinite($progresoF1)) {
                                $progresoF1 = 0;
                            }
                            if (is_nan($progresoF1)) {
                                $progresoF1 = 0;
                            }
                            if ($progresoF1 > 100) {
                                $progresoF1 = 100;
                            }
                            if ($metaFiltro == 0 && $sumSeguimiento == 0) {
                                $progresoF = $progresoF1 = 100;
                            }
                        } else {
                            $progresoF = $progresoF1 = 0;
                        }

                        if ($dataKpis["tipo_calculo"] == 2 && $dataKpis["unidad_medida"] != 4) {

                            if ($metaFiltro >= 0 && ($sumSeguimiento === null || $sumSeguimiento === '')) {
                                $progresoF = $progresoF1 = 0; // Meta es 0 y sumSeguimiento vacío o nulo
                            } elseif ($metaFiltro >= 0 && $sumSeguimiento == 0) {
                                $progresoF = $progresoF1 = 100; // Meta y sumSeguimiento son iguales a 0
                            } elseif ($metaFiltro == 0 && $sumSeguimiento > 0) {
                                $progresoF = $progresoF1 = max(0, 100 - ($sumSeguimiento * 10));
                            } elseif ($metaFiltro == 0 && $sumSeguimiento < 0) {
                                $progresoF = $progresoF1 = 100;
                            } elseif ($metaFiltro > 0 && $sumSeguimiento < 0) {
                                $progresoF = $progresoF1 = max(0, 100 - ($sumSeguimiento * 10));
                            }

                            if ($progresoF > 100) {
                                $progresoF = $progresoF1 = 100;
                            }
                        }

                        if ($dataKpis["tipo_calculo"] == 1 && $dataKpis["unidad_medida"] != 4) {

                            if ($metaFiltro == 0 && ($sumSeguimiento === null || $sumSeguimiento === '')) {
                                $progresoF = $progresoF1 = 0; // Meta es 0 y sumSeguimiento vacío o nulo
                            } elseif ($metaFiltro == 0 && $sumSeguimiento == 0) {
                                $progresoF = $progresoF1 = 100; // Meta y sumSeguimiento son iguales a 0
                            } elseif ($metaFiltro == 0 && $sumSeguimiento > 0) {
                                // Cálculo cuando la meta es 0 y el seguimiento es mayor a 0
                                $progresoF = $progresoF1 = 100;
                            } elseif ($metaFiltro > 0 && $sumSeguimiento < 0) {
                                $progresoF = $progresoF1 = max(0, 100 - ($sumSeguimiento * 10));
                            } elseif ($metaFiltro == 0 && $sumSeguimiento < 0) {
                                $progresoF = $progresoF1 = max(0, 100 + ($sumSeguimiento * 10));
                            }
                        }

                        if ($dataKpis["tipo_calculo"] == 3 && $dataKpis["unidad_medida"] != 4) {

                            if ($metaFiltro >= 0 && ($sumSeguimiento === null || $sumSeguimiento === '')) {
                                $progresoF = $progresoF1 = 0; // Meta es 0 y sumSeguimiento vacío o nulo
                            } elseif ($metaFiltro >= 0 && $sumSeguimiento == 0) {
                                $progresoF = $progresoF1 = 100; // Meta y sumSeguimiento son 0 (no progreso, pero la meta está cumplida)
                            } elseif ($metaFiltro == 0 && $sumSeguimiento > 0) {
                                $progresoF = $progresoF1 = 0; // Meta 0, pero seguimiento positivo (no se cumple la meta)
                            } elseif ($metaFiltro == 0 && $sumSeguimiento < 0) {
                                $progresoF = $progresoF1 = 100; // Meta 0, seguimiento negativo (se cumple la meta en este caso)
                            } elseif ($metaFiltro > 0 && $sumSeguimiento < 0) {
                                $progresoF = $progresoF1 = 0; // Meta positiva, pero seguimiento negativo (no se cumple la meta)
                            } elseif (abs($sumSeguimiento) <= $metaFiltro) {
                                $progresoF = $progresoF1 = 100; // Seguimiento dentro de los límites de la meta (cumple la meta)
                            } elseif (abs($sumSeguimiento) > $metaFiltro) {
                                $progresoF = $progresoF1 = 0; // Seguimiento mayor que la meta (no se cumple la meta)
                            }

                            if ($progresoF > 100) {
                                $progresoF = $progresoF1 = 100;
                            }
                        }
                        if ($sumSeguimiento != "") {
                            if ($dataKpis["unidad_medida"] == 4) {
                                $array1 = explode(":", $sumSeguimiento);
                                $array2 = explode(":", $dataKpis["meta"]);
                                $array3 = explode(":", $metaFiltro);
                                $progresoF = ProgresoHoras($array1, $array2, $dataKpis["tipo_calculo"]);
                                $progresoF1 = ProgresoHoras($array1, $array3, $dataKpis["tipo_calculo"]);
                            }
                        }

                        $escala = EscalaColor(round($progresoF), $_SESSION['id_empresa'], $connect_valentina);
                        $escalaF = EscalaColor(round($progresoF1), $_SESSION['id_empresa'], $connect_valentina);
                        $back_color = "background-color:" . $escala['color_bg'] . " !important";
                        $back_colorF = "background-color:" . $escalaF['color_bg'] . " !important";
                        $txt_rango_meta = $escala['txt_subtitulo'];

                        $sumAvance = $sumAvance + $progresoF1;

                        $barraProgresoF = '<div class="progress" data-bs-toggle="tooltip" title="' . $txt_rango_meta . '" style="height: 15px;">
                                <div class="progress-bar bg-success progress-bar-striped active" role="progressbar" style=" color: black; width: ' . $progresoF . '%; ' . $back_color . '" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>' . $progresoF . '%';

                        include("views/kpis_pc/permisos_rol.php");

                    ?>
                        <div class="card mb-0" style="margin-bottom: 10px !important;">
                            <div class="card-header" role="tab" id="heading<?php echo $dataKpis["id"]; ?>">
                                <div class="row" style="align-items: center;">
                                    <div class="col-md-5">
                                        <h5 style="color: #888888 !important;"><?php echo $etiquetaKpiTK; ?>: &nbsp; <?php foreach ($Array_tipo_kpi_PC as $tipoKpi) {if ($dataKpis["tipo_kpi"] ==  $tipoKpi[0]) { echo $tipoKpi[1];}} ?></h5>
                                        <h4 class="fill_resultados">
                                            <?php foreach ($Array_Frecuencia_PC as $frecuencia) {
                                                if ($frecuencia[0] == $dataKpis["frecuencia"]) {
                                                    echo $frecuencia[1];
                                                }
                                            }

                                            ?>&nbsp;|&nbsp;KPI&nbsp;:&nbsp;&nbsp;<?php echo $dataKpis["indicador"]; ?>
                                        </h4>
                                        <h5 style="color: #888888 !important;"><?php echo $etiquetaKpiAM; ?>: &nbsp; <?php $queryAM = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id = '" . $dataKpis["area_macro"] . "' AND id_empresa = ".$_SESSION['id_empresa']."");
                                        $dataAM = mysqli_fetch_array($queryAM);
                                         echo  $dataAM["nombre"]; ?></h5>
                                        <h5 style="color: #888888 !important;"><?php echo $etiquetaKpiAP; ?>: <?php $queryAP = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = '" . $dataKpis["area_proceso"] . "' AND id_empresa = ".$_SESSION['id_empresa']."");
                                                                                                                $dataAP = mysqli_fetch_array($queryAP);
                                                                                                                echo  $dataAP["nombre"]; ?></h5>
                                        <h5 style="color: #888888 !important;"><?php echo $etiquetaKpiSP; ?>: <?php $queryEE = mysqli_query($connect_valentina, "SELECT * FROM Estructura_Empresa WHERE vicepresidencia = '" . $dataKpis["area_macro"] . "' AND area = '" . $dataKpis["area_proceso"] . "' AND id_empresa = ".$_SESSION['id_empresa']."");
                                                                                                                while ($dataEE = mysqli_fetch_array($queryEE)) {
                                                                                                                    if ($dataEE["id"] == $dataKpis["subproceso"]) {
                                                                                                                        echo  $dataEE["unidad_organizativa"];
                                                                                                                    };
                                                                                                                } ?></h5>
                                        <?php
                                        $queryOE = mysqli_query($connect_kpis, "SELECT * FROM Kpis_Colaborador WHERE id_kpi = '" . $dataKpis["id"] . "' AND id_empresa = " . $_SESSION["id_empresa"] . " AND id_colaborador = " . $_SESSION['id_user'] . "");
                                        $dataOE = mysqli_fetch_array($queryOE);
                                        $queryRolKPI = mysqli_query($connect_kpis, "SELECT * FROM Roles_Kpis WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND id_rol = " . $dataOE["tipo"] . " AND estado = 1");
                                        $dataRolKPI = mysqli_fetch_array($queryRolKPI);
                                        if (mysqli_num_rows($queryOE)) {
                                        ?>
                                            <h5 style='color: #888888 !important;'><span class="label" onClick="VerRol(<?php echo $dataRolKPI["id"]; ?>)" data-bs-toggle="tooltip" style="color:black !important;font-size: 0.9rem !important;"><button class="btn" style="background-color: #ffc107 !important;border-radius: 50px;"><i class="fa fa-user"></i></button>&nbsp;<?php echo "Tu Rol: " . $dataRolKPI['nombre_rol']; ?>&nbsp;</span></h5>
                                        <?php
                                        }

                                        ?>
                                    </div>
                                    <div class="col-md-1"></div>
                                    <div class="col-md-2">
                                        <h4 class="fill_resultados">
                                            <?php echo $etiquetaKpiM; ?>:<br><?php echo $metaFiltro; ?>
                                        </h4>
                                    </div>
                                    <div class="col-md-2">
                                        <h4 class="fill_resultados">
                                            Seguimiento:<br> <?php echo $sumSeguimiento; ?>
                                        </h4>
                                    </div>
                                    <div class="col-md-1">

                                        <!-- <div class="progreso-bar-container" style="--i:<?php //echo $progresoF1;
                                                                                            ?>;--clr:<?php //echo $escalaF['color_bg'];
                                                                                                        ?>">
                                                    <div class="progreso-bar objetivo-okr">
                                                        <progreso id="objetivo-okr" min="0" value="<?php //echo $progresoF1;
                                                                                                    ?>"></progreso>
                                                    </div>
                                                </div> -->
                                        <div class="progress" data-bs-toggle="tooltip" title="" style="height: 15px;">
                                            <div class="progress-bar bg-success progress-bar-striped active" role="progressbar" style=" color: black; width: <?php echo $progresoF1; ?>%;<?php echo $back_colorF; ?>" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div><?php echo $progresoF1; ?>%
                                    </div>

                                    <div class="col-md-1">

                                        <div class="row" style="align-items: center;">
                                            <?php if ($_SESSION['role_plataforma'] == 1 || $permisoEditKPI == true || $permisoMetaKPI == true) { ?>
                                                <div class="col-md-9" style="text-align:end;">
                                                    <button type="button" class="btn btn-outline-dark btn-sm bt_editar" data-bs-toggle="tooltip" title="Editar KPI" onclick="location.href='<?php echo $url; ?>?pg=kpis_pc/detalle/editar&id_kpi=<?php echo $dataKpis['id']; ?>&id_frecuencia=<?php echo $dataKpis['obj_meses']; ?>&page=<?php echo $_GET['page']; ?>&menu=kpis'">
                                                        <i class="fas fa-edit" style="font-size: 15px !important;"></i>
                                                    </button>
                                                </div>
                                                <div class="col-md-3" style="text-align:end;">
                                                    <a class="collapsed" data-toggle="collapse" href="#collapseKpi<?php echo $dataKpis["id"]; ?>" aria-expanded="false" aria-controls="collapseKpi<?php echo $dataKpis["id"]; ?>" id="datosKpis_<?php echo $dataKpis["id"]; ?>" style="color: black !important;">
                                                    </a>
                                                </div>
                                            <?php } else { ?>
                                                <div class="col-md-12" style="text-align:end;">
                                                    <a class="collapsed" data-toggle="collapse" href="#collapseKpi<?php echo $dataKpis["id"]; ?>" aria-expanded="false" aria-controls="collapseKpi<?php echo $dataKpis["id"]; ?>" id="datosKpis_<?php echo $dataKpis["id"]; ?>" style="color: black !important;">
                                                    </a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="collapseKpi<?php echo $dataKpis["id"]; ?>" class="collapse" role="tabpanel" aria-labelledby="heading<?php echo $dataKpis["id"]; ?>" data-parent="#accordionIconsKpis">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <h5><b>Responsables:</b></h5>
                                                                <?php echo $responsables; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <h5><b><?php echo $etiquetaKpiOI; ?>:</b></h5>
                                                                <?php echo $dataKpis["objetivo_indicador"]; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <h5><b><?php echo $etiquetaKpiFC; ?>:</b></h5>
                                                                <?php echo $dataKpis["formula"]; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <h5><b><?php echo $etiquetaKpiTR; ?>:</b>&nbsp;<span id="spanResultado_<?php echo $dataKpis["id"]; ?>" class="circleSpan" data-bs-toggle="tooltip" style="color:#365189 !important;font-size: 11px !important;" title="Ver información de los tipos de resultado"><i class="fa fa-question"></i></span></h5>
                                                                <?php
                                                                foreach ($Array_Acumulativo_PC as $objetivo) {
                                                                    if ($objetivo[0] == $dataKpis["tipo_resultado"]) {
                                                                        echo $objetivo[1];
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <h5><b><?php echo $etiquetaKpiTC; ?>:</b>&nbsp;<span id="spanCalculo_<?php echo $dataKpis["id"]; ?>" class="circleSpan" data-bs-toggle="tooltip" style="color:#365189 !important;font-size: 11px !important;" title="Ver información de los tipos de cálculo"><i class="fa fa-question"></i></span></h5>
                                                                <?php
                                                                foreach ($Array_Tendencia_KPIS as $objetivo) {
                                                                    if ($objetivo[0] == $dataKpis["tipo_calculo"]) {
                                                                        echo $objetivo[1];
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <script>
                                                var api_kpis_pc = '<?php echo $url; ?>api/kpis_pc/';

                                                var spanResultado_<?php echo $dataKpis["id"]; ?> = document.getElementById("spanResultado_<?php echo $dataKpis["id"]; ?>");
                                                var spanCalculo_<?php echo $dataKpis["id"]; ?> = document.getElementById("spanCalculo_<?php echo $dataKpis["id"]; ?>");

                                                spanResultado_<?php echo $dataKpis["id"]; ?>.onclick = function() {
                                                    jQuery.ajax({
                                                            url: api_kpis_pc + "ver_tresultado.php",
                                                            type: 'post',
                                                            data: {
                                                                id_empresa: <?php echo $_SESSION["id_empresa"]; ?>
                                                            },
                                                        }).done(function(resp) {
                                                            $("#modal_tipo").modal("show");
                                                            $("#modal_contenido_tipo").html(resp);
                                                        })
                                                        .fail(function(resp) {
                                                            console.log(resp);
                                                        })
                                                        .always(function(resp) {});
                                                }

                                                spanCalculo_<?php echo $dataKpis["id"]; ?>.onclick = function() {
                                                    jQuery.ajax({
                                                            url: api_kpis_pc + "ver_tcalculo.php",
                                                            type: 'post',
                                                            data: {
                                                                id_empresa: <?php echo $_SESSION["id_empresa"]; ?>
                                                            },
                                                        }).done(function(resp) {
                                                            $("#modal_tipo").modal("show");
                                                            $("#modal_contenido_tipo").html(resp);
                                                        })
                                                        .fail(function(resp) {
                                                            console.log(resp);
                                                        })
                                                        .always(function(resp) {});
                                                }
                                            </script>
                                            <div class="col-md-2">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <h5><b><?php echo $etiquetaKpiUM; ?>:</b></h5>
                                                                <?php
                                                                foreach ($Array_Medicion_PC as $objetivo) {
                                                                    if ($objetivo[0] == $dataKpis["unidad_medida"]) {
                                                                        echo $objetivo[1];
                                                                    }
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <form action="" id="form-frecuencia-colaborador" method="post">
                                        <div class="card-footer">
                                            <?php

                                            switch ($dataKpis["frecuencia"]) {
                                                case 1:
                                                    include("views/kpis_pc/detalle/tabla_mensual.php");
                                                    break;
                                                case 2:
                                                    include("views/kpis_pc/detalle/tabla_bimestral.php");
                                                    break;
                                                case 3:
                                                    include("views/kpis_pc/detalle/tabla_trimestral.php");
                                                    break;
                                                case 4:
                                                    include("views/kpis_pc/detalle/tabla_semestral.php");
                                                    break;
                                                case 5:
                                                    include("views/kpis_pc/detalle/tabla_anual.php");
                                                    break;
                                                case 6:
                                                    include("views/kpis_pc/detalle/tabla_cuatrimestral.php");
                                                    break;
                                            }
                                            ?>
                                            <br>
                                            <div class="row" style="text-align: end;">
                                                <div class="col-md-12">
                                                    <?php $queryAK = mysqli_query($connect_kpis, "SELECT * FROM Administradores_Kpi WHERE id_empleado = '" . $_SESSION["id_user"] . "' ");
                                                    $dataAK = mysqli_fetch_array($queryAK);
                                                    $queryEK = mysqli_query($connect_kpis, "SELECT * FROM Kpis_Colaborador WHERE id_colaborador = '" . $_SESSION["id_user"] . "' AND id_kpi = " . $dataKpis["id"] . " AND tipo = 2");
                                                    $dataEK = mysqli_fetch_array($queryEK);

                                                    $queryCK = mysqli_query($connect_kpis, "SELECT * FROM Comentarios_Kpis WHERE id_kpi = '" . $dataKpis["id"] . "'");
                                                    $dataCK = mysqli_fetch_array($queryCK);
                                                    $queryDK = mysqli_query($connect_kpis, "SELECT * FROM Documentos_Kpis WHERE id_kpi = '" . $dataKpis["id"] . "'");
                                                    $dataDK = mysqli_fetch_array($queryDK);
                                                    $documentos = $comentarios = "";
                                                    if(mysqli_num_rows($queryCK) > 0){
                                                        $comentarios = " (".mysqli_num_rows($queryCK).")";
                                                    }
                                                    if(mysqli_num_rows($queryDK) > 0){
                                                        $documentos = " (".mysqli_num_rows($queryDK).")";
                                                    }
                                                    include("views/kpis_pc/permisos_rol.php");
                                                    if ($_SESSION['role_plataforma'] == 1 || $permisoSegKPI == true) {
                                                        // if ($_SESSION['role_plataforma'] == 1 || (mysqli_num_rows($queryAK) > 0) || (mysqli_num_rows($queryEK) > 0)) {
                                                    ?>
                                                        <!-- <button class="btn btn-success" type="button" onclick="GuardarAvances(<?php //echo $dataKpis["id"];
                                                                                                                                    ?>)">Guardar avances</button> -->
                                                        <button class="btn btn-success" type="submit">Guardar avances</button>
                                                    <?php }
                                                    if ($_SESSION['role_plataforma'] == 1 || $permisoAddComentario == true) {
                                                    ?>
                                                        <button class="btn btn-primary" type="button" onclick="AgregarComentario(<?php echo $dataKpis["id"] . "," . $_SESSION['id_user'] . "," . $_SESSION['role_plataforma'] . "," . $_SESSION['id_empresa']; ?>)" style="border-radius: 30px;" title="Agregar Comentario"><i class="fas fa-comment"></i><?php echo $comentarios; ?></button>
                                                    <?php }
                                                    if ($_SESSION['role_plataforma'] == 1 || $permisoCreateDoc == true) {
                                                    ?>
                                                        <button class="btn btn-success" type="button" onclick="AgregarDocumento(<?php echo $dataKpis["id"] . "," . $_SESSION['id_user'] . "," . $_SESSION['role_plataforma'] . "," . $_SESSION['id_empresa']; ?>)" style="border-radius: 30px;" title="Agregar Documento"><i class="fas fa-file"></i><?php echo $documentos; ?></button>
                                                    <?php }
                                                    if ($_SESSION['role_plataforma'] == 1 || $permisoGestionKPI == true) {
                                                    ?>
                                                        <button class="btn btn-warning" type="button" onclick="GestionarKPI(<?php echo $dataKpis["id"] . "," . $_SESSION['id_user'] . "," . $_SESSION['role_plataforma'] . "," . $_SESSION['id_empresa']; ?>)" style="border-radius: 30px;">Gestionar KPI</button>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- <br> -->
                    <?php $contKpi++;
                    }

                    include("views/kpis_pc/desempenio_kpis.php");

                    $avanceGeneral = $sumAvanceD / $contKpiD;

                    if ($_SESSION["tipo_kpi_fill"] == "") {
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
                            // echo "($sumAvanceT / $contKpiT)*(" . $dataPonderacion["tactico"] . "/100)<br>";
                            // echo "promedio tactico = $tacticos<br>";
                            // echo "promedio estrategico = $estrategicos<br>";
                            $avanceGeneral = $estrategicos + $tacticos;
                        }
                    }

                    $porciento = $avanceGeneral;

                    if (is_nan($porciento) || is_infinite($porciento)) {
                        $porciento = 0;
                    }

                    if ($porciento > 100) {
                        $porcentaje_barra = 100;
                        // $porciento = 100;
                    } else {
                        $porcentaje_barra = $porciento;
                    }

                    $escalaBarra = EscalaColor(round($porciento), $_SESSION['id_empresa'], $connect_valentina);
                    $back_colorBarra = "background-color:" . $escalaBarra['color_bg'] . " !important";
                    ?>
                    <script>
                        $(document).ready(function() {
                            $("#progreso_desempenio").html('<div class="progresos" data-bs-toggle="tooltip" align="center">' +
                                '<h1 style="font-size: 3.5rem;color: black !important;"><?php echo round($porciento, 2); ?> %</h1></div>' +
                                '<div class="progress-bar bg-success" role="progressbar" style=" width: <?php echo round($porcentaje_barra, 2); ?>%; <?php echo $back_colorBarra; ?>;opacity: 0.3;z-index: 2;margin-top: -70px;height: 70px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">' +
                                '</div><br>'
                            );
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12 table-responsive">
            <?php
            echo '<nav aria-label="Page navigation">';
            echo '<ul class="pagination">';

            // Enlace a la página anterior
            if ($page > 1) {
                echo '<li class="page-item"><a class="page-link" href="?pg=kpis_pc/kpis&page=' . ($page - 1) . '">Anterior</a></li>';
            }

            // Enlaces numéricos
            for ($i = 1; $i <= $total_paginas; $i++) {
                $activo = ($i == $page) ? 'active' : '';
                echo '<li class="page-item ' . $activo . '"><a class="page-link" href="?pg=kpis_pc/kpis&page=' . $i . '">' . $i . '</a></li>';
            }

            // Enlace a la página siguiente
            if ($page < $total_paginas) {
                echo '<li class="page-item"><a class="page-link" href="?pg=kpis_pc/kpis&page=' . ($page + 1) . '">Siguiente</a></li>';
            }

            echo '</ul>';
            echo '</nav>';
            ?>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var divElement = document.getElementById("heading<?php echo $_GET["kpi_"]; ?>");

        if (divElement) {
            divElement.scrollIntoView();
            $("#datosKpis_<?php echo $_GET["kpi_"]; ?>").removeClass("collapsed");
            document.getElementById("datosKpis_<?php echo $_GET["kpi_"]; ?>").setAttribute("aria-expanded", true);
            $("#collapseKpi<?php echo $_GET["kpi_"]; ?>").addClass("show");
            $('html, body, #heading<?php echo $_GET["kpi_"]; ?>').animate({
                scrollTop: $("#frecuencia_<?php echo $_GET["kpi_"]; ?>").offset().top - 200
            }, 2000);
        }

    });
    window.location.hash = "";
    window.location.hash = "";
</script>

<script>
    var api_okrs = '<?php echo $url; ?>api/okrs/';
    var api_kpis_pc = '<?php echo $url; ?>api/kpis_pc/';

    function Profile(id, val) {
        jQuery.ajax({
                url: api_okrs + "profile_empleado.php",
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

    function Guardar_Avance(avance, id, mes, id_user, tipo) {
        data = {
            id: id,
            avance: avance,
            mes: mes,
            id_user: id_user,
            tipo: tipo,
            id_empresa: <?php echo $_SESSION['id_empresa']; ?>
        };
        jQuery.ajax({
                url: api_kpis_pc + "guardar_avance.php",
                type: 'post',
                data: data,
            }).done(function(resp) {
                // $("#xscript").html(resp);
                // window.location = "?pg=okrs_equipos/home_organizacion&resultado_okr_="+id_resultado+"&iniciativa="+id;
                // location.reload();
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function Guardar_Avance_Tiempo(avance, id, mes, id_user, tipo) {
        if (avance.includes(":")) {
            cuenta = 0;
            posicion = avance.indexOf(":");
            while (posicion != -1) {
                cuenta++;
                posicion = avance.indexOf(":", posicion + 1);
            }
            if (cuenta == 2) {
                data = {
                    id: id,
                    avance: avance,
                    mes: mes,
                    id_user: id_user,
                    tipo: tipo,
                    id_empresa: <?php echo $_SESSION['id_empresa']; ?>
                };
                jQuery.ajax({
                        url: api_kpis_pc + "guardar_avance.php",
                        type: 'post',
                        data: data,
                    }).done(function(resp) {
                        // $("#xscript").html(resp);
                        // window.location = "?pg=okrs_equipos/home_organizacion&resultado_okr_="+id_resultado+"&iniciativa="+id;
                        // location.reload();
                    })
                    .fail(function(resp) {
                        console.log(resp);
                    })
                    .always(function(resp) {});
            } else {
                alert('Recuerde ingresar el formato HH:mm:ss');
            }
        } else {
            alert('Recuerde ingresar el formato HH:mm:ss');
        }

    }

    function filterFloat(evt, input) {
        // Backspace = 8, Enter = 13, ‘0′ = 48, ‘9′ = 57, ‘.’ = 46, ‘-’ = 43
        var key = window.Event ? evt.which : evt.keyCode;
        var chark = String.fromCharCode(key);
        var tempValue = input.value + chark;
        if (key >= 48 && key <= 57) {
            if (filter(tempValue) === false) {
                return false;
            } else {
                return true;
            }
        } else {
            if (key == 8 || key == 13 || key == 0) {
                return true;
            } else if (key == 46) {
                if (filter(tempValue) === false) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return false;
            }
        }
    }

    function filter(__val__) {
        var preg = /^([0-9]+\.?[0-9]{0,2})$/;
        if (preg.test(__val__) === true) {
            return true;
        } else {
            return false;
        }

    }

    function GuardarAvances(id) {
        window.location = "?pg=kpis_pc/kpis&kpi_=" + id;
    }

    function GestionarKPI(id, id_user, rol, id_empresa) {
        window.open("<?php echo $url; ?>views_kpis/gestionar_kpi.php?id=" + id + "&id_user=" + id_user + "&role=" + rol + "&id_empresa=" + id_empresa, "GoForAgile", "width=1300, height=900")
    }

    function AgregarComentario(id, id_user, rol, id_empresa) {
        window.open("<?php echo $url; ?>views_kpis/gestionar_kpi.php?id=" + id + "&id_user=" + id_user + "&role=" + rol + "&id_empresa=" + id_empresa + "&comentario_=" + id, "GoForAgile", "width=1300, height=900")
    }

    function AgregarDocumento(id, id_user, rol, id_empresa) {
        window.open("<?php echo $url; ?>views_kpis/gestionar_kpi.php?id=" + id + "&id_user=" + id_user + "&role=" + rol + "&id_empresa=" + id_empresa + "&documento_=" + id, "GoForAgile", "width=1300, height=900")
    }

    function isTime(avance) {
        if (avance.includes(":")) {
            cuenta = 0;
            posicion = avance.indexOf(":");
            while (posicion != -1) {
                cuenta++;
                posicion = avance.indexOf(":", posicion + 1);
            }
            if (cuenta != 2) {
                alert('Recuerde ingresar el formato HH:mm:ss');
            }
        } else {
            alert('Recuerde ingresar el formato HH:mm:ss');
        }
    }

    function NumerosDecimales(input) {
        // Permitir números, puntos y comas
        let valor = input.value;

        // Eliminar cualquier carácter que no sea un dígito, un punto o una coma
        valor = valor.replace(/[^0-9.,-]/g, '');

        // Si hay más de un punto o una coma, eliminar extras
        let puntos = (valor.match(/\./g) || []).length;
        let comas = (valor.match(/,/g) || []).length;

        if (puntos > 1) {
            valor = valor.replace(/\.(?=.*\.)/g, ''); // Eliminar puntos adicionales
        }

        if (comas > 1) {
            valor = valor.replace(/,(?=.*,)/g, ''); // Eliminar comas adicionales
        }

        // Actualizar el valor del input
        input.value = valor;
    }

    function VerRol(id) {
        jQuery.ajax({
                url: api_kpis_pc + "ver_rol_kpi.php",
                type: 'post',
                data: {
                    id: id
                },
            }).done(function(resp) {
                $("#modal_rol_okr").modal("show");
                $("#modal_contenido_tipo_rol").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function MostrarMensaje(id, id_empresa) {
        jQuery.ajax({
                url: api_kpis_pc + "ver_mensaje_emergente.php",
                type: 'post',
                data: {
                    id: id,
                    id_empresa: id_empresa
                },
            }).done(function(resp) {
                $("#modal_rol_okr").modal("show");
                $("#modal_contenido_tipo_rol").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }
</script>