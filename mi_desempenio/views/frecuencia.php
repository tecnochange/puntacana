<?php
$queryEmpresa = mysqli_query($connect_admin, "SELECT * FROM Empresas WHERE id = '" . $_SESSION["id_empresa_valentina"] . "'");
$dataEmpresa = mysqli_fetch_array($queryEmpresa);
$mesInicio = $dataEmpresa["mes_inicio"];
$mesFin = $dataEmpresa["mes_fin"];
$mes_actual = date("n");
$anio_actual = date("Y");
$arrayMensual = [];
$arrayMensual[1] = $dataFrecuencia["avance_1"];
$arrayMensual[2] = $dataFrecuencia["avance_2"];
$arrayMensual[3] = $dataFrecuencia["avance_3"];
$arrayMensual[4] = $dataFrecuencia["avance_4"];
$arrayMensual[5] = $dataFrecuencia["avance_5"];
$arrayMensual[6] = $dataFrecuencia["avance_6"];
$arrayMensual[7] = $dataFrecuencia["avance_7"];
$arrayMensual[8] = $dataFrecuencia["avance_8"];
$arrayMensual[9] = $dataFrecuencia["avance_9"];
$arrayMensual[10] = $dataFrecuencia["avance_10"];
$arrayMensual[11] = $dataFrecuencia["avance_11"];
$arrayMensual[12] = $dataFrecuencia["avance_12"];

if ($dataKpis["frecuencia"] == 1) {
    $mensual = $contMensual = 0;

    if ($dataKpis["unidad_medida"] == 4) {
        $sumSeguimiento = Seguimiento_Tiempo(
            $dataFrecuencia["avance_1"],
            $dataFrecuencia["avance_2"],
            $dataFrecuencia["avance_3"],
            $dataFrecuencia["avance_4"],
            $dataFrecuencia["avance_5"],
            $dataFrecuencia["avance_6"],
            $dataFrecuencia["avance_7"],
            $dataFrecuencia["avance_8"],
            $dataFrecuencia["avance_9"],
            $dataFrecuencia["avance_10"],
            $dataFrecuencia["avance_11"],
            $dataFrecuencia["avance_12"],
            $dataFrecuencia["id"],
            $connect_kpis
        );
        if (($dataFrecuencia["avance_7"] != null || $dataFrecuencia["avance_7"] != "") && ($dataFrecuencia["julio"] != null || $dataFrecuencia["julio"] != "")) {
            $contMensual++;
        }
        if (($dataFrecuencia["avance_8"] != null || $dataFrecuencia["avance_8"] != "") && ($dataFrecuencia["agosto"] != null || $dataFrecuencia["agosto"] != "")) {
            $contMensual++;
        }
        if (($dataFrecuencia["avance_9"] != null || $dataFrecuencia["avance_9"] != "") && ($dataFrecuencia["septiembre"] != null || $dataFrecuencia["septiembre"] != "")) {
            $contMensual++;
        }
        if (($dataFrecuencia["avance_10"] != null || $dataFrecuencia["avance_10"] != "") && ($dataFrecuencia["octubre"] != null || $dataFrecuencia["octubre"] != "")) {
            $contMensual++;
        }
        if (($dataFrecuencia["avance_11"] != null || $dataFrecuencia["avance_11"] != "") && ($dataFrecuencia["noviembre"] != null || $dataFrecuencia["noviembre"] != "")) {
            $contMensual++;
        }
        if (($dataFrecuencia["avance_12"] != null || $dataFrecuencia["avance_12"] != "") && ($dataFrecuencia["diciembre"] != null || $dataFrecuencia["diciembre"] != "")) {
            $contMensual++;
        }
        if (($dataFrecuencia["avance_1"] != null || $dataFrecuencia["avance_1"] != "") && ($dataFrecuencia["enero"] != null || $dataFrecuencia["enero"] != "")) {
            $contMensual++;
        }
        if (($dataFrecuencia["avance_2"] != null || $dataFrecuencia["avance_2"] != "") && ($dataFrecuencia["febrero"] != null || $dataFrecuencia["febrero"] != "")) {
            $contMensual++;
        }
        if (($dataFrecuencia["avance_3"] != null || $dataFrecuencia["avance_3"] != "") && ($dataFrecuencia["marzo"] != null || $dataFrecuencia["marzo"] != "")) {
            $contMensual++;
        }
        if (($dataFrecuencia["avance_4"] != null || $dataFrecuencia["avance_4"] != "") && ($dataFrecuencia["abril"] != null || $dataFrecuencia["abril"] != "")) {
            $contMensual++;
        }
        if (($dataFrecuencia["avance_5"] != null || $dataFrecuencia["avance_5"] != "") && ($dataFrecuencia["mayo"] != null || $dataFrecuencia["mayo"] != "")) {
            $contMensual++;
        }
        if (($dataFrecuencia["avance_6"] != null || $dataFrecuencia["avance_6"] != "") && ($dataFrecuencia["junio"] != null || $dataFrecuencia["junio"] != "")) {
            $contMensual++;
        }

        if ($dataKpis["tipo_resultado"] == 2 || $dataKpis["tipo_resultado"] == 1) {
            $array1 = explode(":", $sumSeguimiento);
            $hora = $array1[0];
            $minuto = $array1[1];
            $segundo = $array1[2];

            $total_tiempo = ($hora * 3600) + ($minuto * 60) + $segundo;
            $sumSeguimiento = round(($total_tiempo / $contMensual), 2);

            if (is_nan($sumSeguimiento) || is_infinite($sumSeguimiento)) {
                $sumSeguimiento = "00:00:00";
            } else {
                $sumSeguimiento = intdiv($sumSeguimiento, 3600) . ":" . intdiv($sumSeguimiento % 3600, 60) . ":" . ($sumSeguimiento % 60);
            }
            if ($contMensual == 0) {
                $sumSeguimiento = "";
            }
        }
    } else {
        $variablesM = [
            $dataFrecuencia["avance_1"],
            $dataFrecuencia["avance_2"],
            $dataFrecuencia["avance_3"],
            $dataFrecuencia["avance_4"],
            $dataFrecuencia["avance_5"],
            $dataFrecuencia["avance_6"],
            $dataFrecuencia["avance_7"],
            $dataFrecuencia["avance_8"],
            $dataFrecuencia["avance_9"],
            $dataFrecuencia["avance_10"],
            $dataFrecuencia["avance_11"],
            $dataFrecuencia["avance_12"]
        ];
        $contForeach = 1;
        foreach ($variablesM as $varM) {            
            $nombreMes = strtolower(obtenerNombreMes($contForeach));
            if (($varM >= 0 || $varM < 0) && is_numeric($varM) && ($dataFrecuencia[$nombreMes] != '' || $dataFrecuencia[$nombreMes] != null)) {
                $mensual += $varM;
                $contMensual++;
            }
            $contForeach++;
        }

        if ($dataKpis["tipo_resultado"] == 2 || $dataKpis["tipo_resultado"] == 1) {
            if ($contMensual != 0) {
                $sumSeguimiento = round(($mensual / $contMensual), 2);
                if (is_nan($sumSeguimiento) || is_infinite($sumSeguimiento)) {
                    $sumSeguimiento = 0;
                }
            } else {
                $sumSeguimiento = "";
            }
            if ($contMensual == 0) {
                $sumSeguimiento = "";
            }
        } else {
            if ($contMensual == 0 && $mensual == 0) {
                $sumSeguimiento = "";
            } else {
                $sumSeguimiento = $mensual;
            }
        }

        // $sumSeguimiento = $dataFrecuencia["avance_1"] + $dataFrecuencia["avance_2"] + $dataFrecuencia["avance_3"] + $dataFrecuencia["avance_4"] + $dataFrecuencia["avance_5"] +
        //     $dataFrecuencia["avance_6"] + $dataFrecuencia["avance_7"] + $dataFrecuencia["avance_8"] + $dataFrecuencia["avance_9"] + $dataFrecuencia["avance_10"] +
        //     $dataFrecuencia["avance_11"] + $dataFrecuencia["avance_12"];
    }

    if ($dataKpis["tipo_resultado"] == 3) {
        $sumSeguimiento = null;

        for ($mes = $mes_actual; $mes >= 1; $mes--) {
            $nombreMes = strtolower(obtenerNombreMes($mes));
            if (isset($arrayMensual[$mes]) && $arrayMensual[$mes] !== '' && ($dataFrecuencia[$nombreMes] != '' || $dataFrecuencia[$nombreMes] != null)) {
                $sumSeguimiento = $arrayMensual[$mes];
                break;
            }
        }

        if ($sumSeguimiento === null) {
            for ($mes = 12; $mes >= 1; $mes--) {
                $nombreMes = strtolower(obtenerNombreMes($mes));
                if (isset($arrayMensual[$mes]) && $arrayMensual[$mes] !== '' && ($dataFrecuencia[$nombreMes] != '' || $dataFrecuencia[$nombreMes] != null)) {                    
                    $sumSeguimiento = $arrayMensual[$mes];
                    break;
                }
            }
        }
    }
} else if ($dataKpis["frecuencia"] == 5) {
    $inicioMes = explode('-', $mesInicio);

    $mes_inicial = $inicioMes[1];
    $nombreMes = strtolower(obtenerNombreMes($mes_inicial));
    $avance = "avance_" . (int)$mes_inicial;
    if($dataFrecuencia[$nombreMes] != '' || $dataFrecuencia[$nombreMes] != null){
        $sumSeguimiento = $dataFrecuencia[$avance];
    }else{
        $sumSeguimiento = '';
    }
    
} else {
    switch ($dataKpis["frecuencia"]) {
        case 2:
            $mesesBimestral = obtenerMesesPermitidos($mesInicio, $mesFin, 2);
            $avance1 = "avance_" . (int)$mesesBimestral[0][1]['mes'];
            $avance2 = "avance_" . (int)$mesesBimestral[1][1]['mes'];
            $avance3 = "avance_" . (int)$mesesBimestral[2][1]['mes'];
            $avance4 = "avance_" . (int)$mesesBimestral[3][1]['mes'];
            $avance5 = "avance_" . (int)$mesesBimestral[4][1]['mes'];
            $avance6 = "avance_" . (int)$mesesBimestral[5][1]['mes'];
            $nombreMes1 = strtolower(obtenerNombreMes((int)$mesesBimestral[0][1]['mes']));
            $nombreMes2 = strtolower(obtenerNombreMes((int)$mesesBimestral[1][1]['mes']));
            $nombreMes3 = strtolower(obtenerNombreMes((int)$mesesBimestral[2][1]['mes']));
            $nombreMes4 = strtolower(obtenerNombreMes((int)$mesesBimestral[3][1]['mes']));
            $nombreMes5 = strtolower(obtenerNombreMes((int)$mesesBimestral[4][1]['mes']));
            $nombreMes6 = strtolower(obtenerNombreMes((int)$mesesBimestral[5][1]['mes']));
            $bimestral = $contBimestral = 0;
            if ($dataKpis["unidad_medida"] == 4) {
                $sumH = $sumM = $sumS = $contBimestral = 0;
                
                if ($dataFrecuencia[$avance1] != null || $dataFrecuencia[$avance1] != "" && ($dataFrecuencia[$nombreMes1] != '' || $dataFrecuencia[$nombreMes1] != null)) {
                    $array2 = explode(":", $dataFrecuencia[$avance1]);
                    $sumH = $sumH + $array2[0];
                    $sumM = $sumM + $array2[1];
                    $sumS = $sumS + $array2[2];
                    $contBimestral++;
                }

                if ($dataFrecuencia[$avance2] != null || $dataFrecuencia[$avance2] != "" && ($dataFrecuencia[$nombreMes2] != '' || $dataFrecuencia[$nombreMes2] != null)) {
                    $array2 = explode(":", $dataFrecuencia[$avance2]);
                    $sumH = $sumH + $array2[0];
                    $sumM = $sumM + $array2[1];
                    $sumS = $sumS + $array2[2];
                    $contBimestral++;
                }

                if ($dataFrecuencia[$avance3] != null || $dataFrecuencia[$avance3] != "" && ($dataFrecuencia[$nombreMes3] != '' || $dataFrecuencia[$nombreMes3] != null)) {
                    $array2 = explode(":", $dataFrecuencia[$avance3]);
                    $sumH = $sumH + $array2[0];
                    $sumM = $sumM + $array2[1];
                    $sumS = $sumS + $array2[2];
                    $contBimestral++;
                }

                if ($dataFrecuencia[$avance4] != null || $dataFrecuencia[$avance4] != "" && ($dataFrecuencia[$nombreMes4] != '' || $dataFrecuencia[$nombreMes4] != null)) {
                    $array2 = explode(":", $dataFrecuencia[$avance4]);
                    $sumH = $sumH + $array2[0];
                    $sumM = $sumM + $array2[1];
                    $sumS = $sumS + $array2[2];
                    $contBimestral++;
                }

                if ($dataFrecuencia[$avance5] != null || $dataFrecuencia[$avance5] != "" && ($dataFrecuencia[$nombreMes5] != '' || $dataFrecuencia[$nombreMes5] != null)) {
                    $array2 = explode(":", $dataFrecuencia[$avance5]);
                    $sumH = $sumH + $array2[0];
                    $sumM = $sumM + $array2[1];
                    $sumS = $sumS + $array2[2];
                    $contBimestral++;
                }

                if ($dataFrecuencia[$avance6] != null || $dataFrecuencia[$avance6] != "" && ($dataFrecuencia[$nombreMes6] != '' || $dataFrecuencia[$nombreMes6] != null)) {
                    $array2 = explode(":", $dataFrecuencia[$avance6]);
                    $sumH = $sumH + $array2[0];
                    $sumM = $sumM + $array2[1];
                    $sumS = $sumS + $array2[2];
                    $contBimestral++;
                }

                $sumSeguimiento = $sumH . ":" . $sumM . ":" . $sumS;

                if ($dataKpis["tipo_resultado"] == 2 || $dataKpis["tipo_resultado"] == 1) {
                    $array1 = explode(":", $sumSeguimiento);
                    $hora = $array1[0];
                    $minuto = $array1[1];
                    $segundo = $array1[2];

                    $total_tiempo = ($hora * 3600) + ($minuto * 60) + $segundo;
                    $sumSeguimiento = round(($total_tiempo / $contBimestral), 2);

                    if (is_nan($sumSeguimiento) || is_infinite($sumSeguimiento)) {
                        $sumSeguimiento = "00:00:00";
                    } else {
                        $sumSeguimiento = intdiv($sumSeguimiento, 3600) . ":" . intdiv($sumSeguimiento % 3600, 60) . ":" . ($sumSeguimiento % 60);
                    }
                    if ($contBimestral == 0) {
                        $sumSeguimiento = "";
                    }
                }
            } else {
                $variablesB = [
                    $dataFrecuencia[$avance1],
                    $dataFrecuencia[$avance2],
                    $dataFrecuencia[$avance3],
                    $dataFrecuencia[$avance4],
                    $dataFrecuencia[$avance5],
                    $dataFrecuencia[$avance6]
                ];
                $contForeach = 0;
                foreach ($variablesB as $varB) {
                    $nombreMesB = strtolower(obtenerNombreMes((int)$mesesBimestral[$contForeach][1]['mes']));
                    if (($varB >= 0 || $varB < 0) && is_numeric($varB) && ($dataFrecuencia[$nombreMesB] != '' || $dataFrecuencia[$nombreMesB] != null)) {
                        $bimestral += $varB;
                        $contBimestral++;
                    }
                    $contForeach++;
                }
                if ($dataKpis["tipo_resultado"] == 2 || $dataKpis["tipo_resultado"] == 1) {
                    if ($contBimestral != 0) {
                        $sumSeguimiento = round(($bimestral / $contBimestral), 2);
                        if (is_nan($sumSeguimiento) || is_infinite($sumSeguimiento)) {
                            $sumSeguimiento = 0;
                        }
                    } else {
                        $sumSeguimiento = "";
                    }
                    if ($contBimestral == 0) {
                        $sumSeguimiento = "";
                    }
                } else {
                    if ($contBimestral == 0 && $bimestral == 0) {
                        $sumSeguimiento = "";
                    } else {
                        $sumSeguimiento = $bimestral;
                    }
                    // $sumSeguimiento = $bimestral;
                }
                // $sumSeguimiento = $dataFrecuencia["avance_2"] + $dataFrecuencia["avance_4"] + $dataFrecuencia["avance_6"] +
                //     $dataFrecuencia["avance_8"] + $dataFrecuencia["avance_10"] + $dataFrecuencia["avance_12"];
            }
            // if ($dataKpis["tipo_resultado"] == 3) {
            //     $sumSeguimiento = obtenerValorDelUltimoRango($mesInicio, $mesFin, 2, $arrayMensual);

            // }
            break;
        case 3:
            $mesesTrimestral = obtenerMesesPermitidos($mesInicio, $mesFin, 3);
            $avance1 = "avance_" . (int)$mesesTrimestral[0][2]['mes'];
            $avance2 = "avance_" . (int)$mesesTrimestral[1][2]['mes'];
            $avance3 = "avance_" . (int)$mesesTrimestral[2][2]['mes'];
            $avance4 = "avance_" . (int)$mesesTrimestral[3][2]['mes'];
            $nombreMes1 = strtolower(obtenerNombreMes((int)$mesesTrimestral[0][2]['mes']));
            $nombreMes2 = strtolower(obtenerNombreMes((int)$mesesTrimestral[1][2]['mes']));
            $nombreMes3 = strtolower(obtenerNombreMes((int)$mesesTrimestral[2][2]['mes']));
            $nombreMes4 = strtolower(obtenerNombreMes((int)$mesesTrimestral[3][2]['mes']));
            
            $trimestral = $contTrimestral = 0;
            if ($dataKpis["unidad_medida"] == 4) {

                $sumH = $sumM = $sumS = $contTrimestral = 0;

                if ($dataFrecuencia[$avance1] != null || $dataFrecuencia[$avance1] != "" && ($dataFrecuencia[$nombreMes1] != '' || $dataFrecuencia[$nombreMes1] != null)) {
                    $array2 = explode(":", $dataFrecuencia[$avance1]);
                    $sumH = $sumH + $array2[0];
                    $sumM = $sumM + $array2[1];
                    $sumS = $sumS + $array2[2];
                    $contTrimestral++;
                }

                if ($dataFrecuencia[$avance2] != null || $dataFrecuencia[$avance2] != "" && ($dataFrecuencia[$nombreMes2] != '' || $dataFrecuencia[$nombreMes2] != null)) {
                    $array2 = explode(":", $dataFrecuencia[$avance2]);
                    $sumH = $sumH + $array2[0];
                    $sumM = $sumM + $array2[1];
                    $sumS = $sumS + $array2[2];
                    $contTrimestral++;
                }

                if ($dataFrecuencia[$avance3] != null || $dataFrecuencia[$avance3] != "" && ($dataFrecuencia[$nombreMes3] != '' || $dataFrecuencia[$nombreMes3] != null)) {
                    $array2 = explode(":", $dataFrecuencia[$avance3]);
                    $sumH = $sumH + $array2[0];
                    $sumM = $sumM + $array2[1];
                    $sumS = $sumS + $array2[2];
                    $contTrimestral++;
                }

                if ($dataFrecuencia[$avance4] != null || $dataFrecuencia[$avance4] != "" && ($dataFrecuencia[$nombreMes4] != '' || $dataFrecuencia[$nombreMes4] != null)) {
                    $array2 = explode(":", $dataFrecuencia[$avance4]);
                    $sumH = $sumH + $array2[0];
                    $sumM = $sumM + $array2[1];
                    $sumS = $sumS + $array2[2];
                    $contTrimestral++;
                }

                $sumSeguimiento = $sumH . ":" . $sumM . ":" . $sumS;

                if ($dataKpis["tipo_resultado"] == 2 || $dataKpis["tipo_resultado"] == 1) {
                    $array1 = explode(":", $sumSeguimiento);
                    $hora = $array1[0];
                    $minuto = $array1[1];
                    $segundo = $array1[2];

                    $total_tiempo = ($hora * 3600) + ($minuto * 60) + $segundo;
                    $sumSeguimiento = round(($total_tiempo / $contTrimestral), 2);

                    if (is_nan($sumSeguimiento) || is_infinite($sumSeguimiento)) {
                        $sumSeguimiento = "00:00:00";
                    } else {
                        $sumSeguimiento = intdiv($sumSeguimiento, 3600) . ":" . intdiv($sumSeguimiento % 3600, 60) . ":" . ($sumSeguimiento % 60);
                    }
                    if ($contTrimestral == 0) {
                        $sumSeguimiento = "";
                    }
                }
            } else {
                $variablesT = [
                    $dataFrecuencia[$avance1],
                    $dataFrecuencia[$avance2],
                    $dataFrecuencia[$avance3],
                    $dataFrecuencia[$avance4]
                ];
                // print_r($variablesT);
                $contForeach = 0;
                foreach ($variablesT as $varT) {
                    $nombreMesT = strtolower(obtenerNombreMes((int)$mesesTrimestral[$contForeach][2]['mes']));
                    if (($varT >= 0 || $varT < 0) && is_numeric($varT) && ($dataFrecuencia[$nombreMesT] != '' || $dataFrecuencia[$nombreMesT] != null)) {
                        $trimestral += $varT;
                        $contTrimestral++;
                    }
                    $contForeach++;
                }

                

                if ($dataKpis["tipo_resultado"] == 2 || $dataKpis["tipo_resultado"] == 1) {
                    
                    if ($contTrimestral != 0) {
                        $sumSeguimiento = round(($trimestral / $contTrimestral), 2);
                        if (is_nan($sumSeguimiento) || is_infinite($sumSeguimiento)) {
                            $sumSeguimiento = 0;
                        }
                    } else {
                        $sumSeguimiento = "";
                    }
                } else {
                    
                    if ($contTrimestral == 0 && $trimestral == 0) {
                        $sumSeguimiento = "";
                    } else {
                        $sumSeguimiento = $trimestral;
                    }
                    // $sumSeguimiento = $trimestral;
                }

                
            }
            // if ($dataKpis["tipo_resultado"] == 3) {
            //     $sumSeguimiento = obtenerValorDelUltimoRango($mesInicio, $mesFin, 3, $arrayMensual);                
            // }
            
            break;
        case 4:
            $mesesSemestral = obtenerMesesPermitidos($mesInicio, $mesFin, 4);
            $avance1 = "avance_" . (int)$mesesSemestral[0][5]['mes'];
            $avance2 = "avance_" . (int)$mesesSemestral[1][5]['mes'];
            $nombreMes1 = strtolower(obtenerNombreMes((int)$mesesSemestral[0][5]['mes']));
            $nombreMes2 = strtolower(obtenerNombreMes((int)$mesesSemestral[1][5]['mes']));
            
            $semestral = $contSemestral = 0;
            switch ($_SESSION["periodo_fin_fill"]) {
                case 1:
                    $dataFrecuencia["avance_6"] = $dataFrecuencia["avance_1"];
                    break;
                case 2:
                    $dataFrecuencia["avance_6"] = $dataFrecuencia["avance_2"];
                    break;
                case 3:
                    $dataFrecuencia["avance_6"] = $dataFrecuencia["avance_3"];
                    break;
                case 4:
                    $dataFrecuencia["avance_6"] = $dataFrecuencia["avance_4"];
                    break;
                case 5:
                    $dataFrecuencia["avance_6"] = $dataFrecuencia["avance_5"];
                    break;
                case 6:
                    $dataFrecuencia["avance_6"] = $dataFrecuencia["avance_6"];
                    break;
            }
            switch ($_SESSION["periodo_ini_fill"]) {
                case 7:
                    $dataFrecuencia["avance_12"] = $dataFrecuencia["avance_7"];
                    break;
                case 8:
                    $dataFrecuencia["avance_12"] = $dataFrecuencia["avance_8"];
                    break;
                case 9:
                    $dataFrecuencia["avance_12"] = $dataFrecuencia["avance_9"];
                    break;
                case 10:
                    $dataFrecuencia["avance_12"] = $dataFrecuencia["avance_10"];
                    break;
                case 11:
                    $dataFrecuencia["avance_12"] = $dataFrecuencia["avance_11"];
                    break;
                case 12:
                    $dataFrecuencia["avance_12"] = $dataFrecuencia["avance_12"];
                    break;
            }
            if ($dataKpis["unidad_medida"] == 4) {

                $sumH = $sumM = $sumS = $contSemestral = 0;

                if ($dataFrecuencia[$avance1] != null || $dataFrecuencia[$avance1] != "" && ($dataFrecuencia[$nombreMes1] != '' || $dataFrecuencia[$nombreMes1] != null)) {
                    $array2 = explode(":", $dataFrecuencia[$avance1]);
                    $sumH = $sumH + $array2[0];
                    $sumM = $sumM + $array2[1];
                    $sumS = $sumS + $array2[2];
                    $contSemestral++;
                }

                if ($dataFrecuencia[$avance2] != null || $dataFrecuencia[$avance2] != "" && ($dataFrecuencia[$nombreMes2] != '' || $dataFrecuencia[$nombreMes2] != null)) {
                    $array2 = explode(":", $dataFrecuencia[$avance2]);
                    $sumH = $sumH + $array2[0];
                    $sumM = $sumM + $array2[1];
                    $sumS = $sumS + $array2[2];
                    $contSemestral++;
                }

                $sumSeguimiento = $sumH . ":" . $sumM . ":" . $sumS;

                if ($dataKpis["tipo_resultado"] == 2 || $dataKpis["tipo_resultado"] == 1) {
                    $array1 = explode(":", $sumSeguimiento);
                    $hora = $array1[0];
                    $minuto = $array1[1];
                    $segundo = $array1[2];

                    $total_tiempo = ($hora * 3600) + ($minuto * 60) + $segundo;
                    $sumSeguimiento = round(($total_tiempo / $contSemestral), 2);

                    if (is_nan($sumSeguimiento) || is_infinite($sumSeguimiento)) {
                        $sumSeguimiento = "00:00:00";
                    } else {
                        $sumSeguimiento = intdiv($sumSeguimiento, 3600) . ":" . intdiv($sumSeguimiento % 3600, 60) . ":" . ($sumSeguimiento % 60);
                    }
                    if ($contSemestral == 0) {
                        $sumSeguimiento = "";
                    }
                }
            } else {
                $variablesS = [
                    $dataFrecuencia[$avance1],
                    $dataFrecuencia[$avance2]
                ];

                $contForeach = 0;
                foreach ($variablesS as $varS) {
                    $nombreMesS = strtolower(obtenerNombreMes((int)$mesesSemestral[$contForeach][5]['mes']));
                    if (($varS >= 0 || $varS < 0) && is_numeric($varS) && ($dataFrecuencia[$nombreMesS] != '' || $dataFrecuencia[$nombreMesS] != null)) {
                        $semestral += $varS;
                        $contSemestral++;
                    }
                    $contForeach++;
                }
                if ($dataKpis["tipo_resultado"] == 2 || $dataKpis["tipo_resultado"] == 1) {
                    $sumSeguimiento = round(($semestral / $contSemestral), 2);
                    if (is_nan($sumSeguimiento) || is_infinite($sumSeguimiento)) {
                        $sumSeguimiento = 0;
                    }
                    if ($contSemestral == 0) {
                        $sumSeguimiento = "";
                    }
                } else {
                    if ($contSemestral == 0 && $semestral == 0) {
                        $sumSeguimiento = "";
                    } else {
                        $sumSeguimiento = $semestral;
                    }
                    // $sumSeguimiento = $semestral;
                }
                // $sumSeguimiento = $dataFrecuencia["avance_6"] + $dataFrecuencia["avance_12"];
            }
            // if ($dataKpis["tipo_resultado"] == 3) {
            //     $sumSeguimiento = obtenerValorDelUltimoRango($mesInicio, $mesFin, 4, $arrayMensual);
            //     // for ($mes = $mes_actual; $mes >= 1; $mes--) {
            //     //     if ($arrayMensual[$mes] != '') {
            //     //         $sumSeguimiento = $arrayMensual[$mes];
            //     //         break;
            //     //     }
            //     // }
            // }
            break;
        case 6:

            $mesesCuatrimestral = obtenerMesesPermitidos($mesInicio, $mesFin, 6);
            $avance1 = "avance_" . (int)$mesesCuatrimestral[0][3]['mes'];
            $avance2 = "avance_" . (int)$mesesCuatrimestral[1][3]['mes'];
            $avance3 = "avance_" . (int)$mesesCuatrimestral[2][3]['mes'];
            $nombreMes1 = strtolower(obtenerNombreMes((int)$mesesCuatrimestral[0][3]['mes']));
            $nombreMes2 = strtolower(obtenerNombreMes((int)$mesesCuatrimestral[1][3]['mes']));
            $nombreMes3 = strtolower(obtenerNombreMes((int)$mesesCuatrimestral[2][3]['mes']));
            $cuatrimestral = $contCuatrimestral = 0;
            if ($dataKpis["unidad_medida"] == 4) {

                $sumH = $sumM = $sumS = $contCuatrimestral = 0;

                if ($dataFrecuencia[$avance1] != null || $dataFrecuencia[$avance1] != "" && ($dataFrecuencia[$nombreMes] != '' || $dataFrecuencia[$nombreMes] != null)) {
                    $array2 = explode(":", $dataFrecuencia[$avance1]);
                    $sumH = $sumH + $array2[0];
                    $sumM = $sumM + $array2[1];
                    $sumS = $sumS + $array2[2];
                    $contCuatrimestral++;
                }

                if ($dataFrecuencia[$avance2] != null || $dataFrecuencia[$avance2] != "" && ($dataFrecuencia[$nombreMes2] != '' || $dataFrecuencia[$nombreMes2] != null)) {
                    $array2 = explode(":", $dataFrecuencia[$avance2]);
                    $sumH = $sumH + $array2[0];
                    $sumM = $sumM + $array2[1];
                    $sumS = $sumS + $array2[2];
                    $contCuatrimestral++;
                }

                if ($dataFrecuencia[$avance3] != null || $dataFrecuencia[$avance3] != "" && ($dataFrecuencia[$nombreMes3] != '' || $dataFrecuencia[$nombreMes3] != null)) {
                    $array2 = explode(":", $dataFrecuencia[$avance3]);
                    $sumH = $sumH + $array2[0];
                    $sumM = $sumM + $array2[1];
                    $sumS = $sumS + $array2[2];
                    $contCuatrimestral++;
                }


                $sumSeguimiento = $sumH . ":" . $sumM . ":" . $sumS;

                if ($dataKpis["tipo_resultado"] == 2 || $dataKpis["tipo_resultado"] == 1) {
                    $array1 = explode(":", $sumSeguimiento);
                    $hora = $array1[0];
                    $minuto = $array1[1];
                    $segundo = $array1[2];

                    $total_tiempo = ($hora * 3600) + ($minuto * 60) + $segundo;
                    $sumSeguimiento = round(($total_tiempo / $contCuatrimestral), 2);

                    if (is_nan($sumSeguimiento) || is_infinite($sumSeguimiento)) {
                        $sumSeguimiento = "00:00:00";
                    } else {
                        $sumSeguimiento = intdiv($sumSeguimiento, 3600) . ":" . intdiv($sumSeguimiento % 3600, 60) . ":" . ($sumSeguimiento % 60);
                    }
                    if ($contCuatrimestral == 0) {
                        $sumSeguimiento = "";
                    }
                }
            } else {
                $variablesC = [
                    $dataFrecuencia[$avance1],
                    $dataFrecuencia[$avance2],
                    $dataFrecuencia[$avance3]
                ];
                // print_r($variablesC);
                $contForeach = 0;
                foreach ($variablesC as $varC) {
                    $nombreMesC = strtolower(obtenerNombreMes((int)$mesesCuatrimestral[$contForeach][3]['mes']));
                    if (($varC >= 0 || $varC < 0) && is_numeric($varC) && ($dataFrecuencia[$nombreMesC] != '' || $dataFrecuencia[$nombreMesC] != null)) {
                    
                        $cuatrimestral += $varC;
                        $contCuatrimestral++;
                    }
                    $contForeach++;
                }

                if ($dataKpis["tipo_resultado"] == 2 || $dataKpis["tipo_resultado"] == 1) {
                    $sumSeguimiento = round(($cuatrimestral / $contCuatrimestral), 2);

                    if (is_nan($sumSeguimiento) || is_infinite($sumSeguimiento)) {
                        $sumSeguimiento = 0;
                    }
                    if ($contCuatrimestral == 0) {
                        $sumSeguimiento = "";
                    }
                } else {
                    if ($contCuatrimestral == 0 && $cuatrimestral == 0) {
                        $sumSeguimiento = "";
                    } else {
                        $sumSeguimiento = $cuatrimestral;
                    }
                    // $sumSeguimiento = $cuatrimestral;
                }

                // $sumSeguimiento = $dataFrecuencia["avance_3"] + $dataFrecuencia["avance_6"] + $dataFrecuencia["avance_9"] + $dataFrecuencia["avance_12"];
            }
            // if ($dataKpis["tipo_resultado"] == 3) {
            //     $sumSeguimiento = obtenerValorDelUltimoRango($mesInicio, $mesFin, 6, $arrayMensual);
            //     // for ($mes = $mes_actual; $mes >= 1; $mes--) {
            //     //     if ($arrayMensual[$mes] != '') {
            //     //         $sumSeguimiento = $arrayMensual[$mes];
            //     //         break;
            //     //     }
            //     // }
            // }
            break;
    }
}

