<?php
$suma_avances_kpies = 0;
$contKpi = $contKpiD = $contKpiE = $contKpiT = $sumAvanceD = $sumAvanceE = $sumAvanceT = $porciento = $metaFiltro = 0;

while ($dataKpis = mysqli_fetch_assoc($queryKpis)) {

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

    $bloqueo1 = $bloqueo2 = $bloqueo3 = $bloqueo4 = $bloqueo5 = $bloqueo6 = $bloqueo7 = $bloqueo8 = $bloqueo9 = $bloqueo10 = $bloqueo11 = $bloqueo12 = false;

    if ($fechaHoy == $fechaenero) {
        $bloqueo1 = true;
    } else if ($fechaHoy < $fechaenero) {
        $bloqueo1 = true;
    }
    if ($fechaHoy == $fechafebrero) {
        $bloqueo2 = true;
    } else if ($fechaHoy < $fechafebrero) {
        $bloqueo2 = true;
    }
    if ($fechaHoy == $fechamarzo) {
        $bloqueo3 = true;
    } else if ($fechaHoy < $fechamarzo) {
        $bloqueo3 = true;
    }
    if ($fechaHoy == $fechaabril) {
        $bloqueo4 = true;
    } else if ($fechaHoy < $fechaabril) {
        $bloqueo4 = true;
    }
    if ($fechaHoy == $fechamayo) {
        $bloqueo5 = true;
    } else if ($fechaHoy < $fechamayo) {
        $bloqueo5 = true;
    }
    if ($fechaHoy == $fechajunio) {
        $bloqueo6 = true;
    } else if ($fechaHoy < $fechajunio) {
        $bloqueo6 = true;
    }
    if ($fechaHoy == $fechajulio) {
        $bloqueo7 = true;
    } else if ($fechaHoy < $fechajulio) {
        $bloqueo7 = true;
    }
    if ($fechaHoy == $fechaagosto) {
        $bloqueo8 = true;
    } else if ($fechaHoy < $fechaagosto) {
        $bloqueo8 = true;
    }
    if ($fechaHoy == $fechaseptiembre) {
        $bloqueo9 = true;
    } else if ($fechaHoy < $fechaseptiembre) {
        $bloqueo9 = true;
    }
    if ($fechaHoy == $fechaoctubre) {
        $bloqueo10 = true;
    } else if ($fechaHoy < $fechaoctubre) {
        $bloqueo10 = true;
    }
    if ($fechaHoy == $fechanoviembre) {
        $bloqueo11 = true;
    } else if ($fechaHoy < $fechanoviembre) {
        $bloqueo11 = true;
    }
    if ($fechaHoy == $fechadiciembre) {
        $bloqueo12 = true;
    } else if ($fechaHoy < $fechadiciembre) {
        $bloqueo12 = true;
    }

    $sumaMeta = $sumaMetaM = $sumaMetaB = $sumaMetaF = $contFiltroFecha = 0;
    $frecuencia = mysqli_query($connect_kpis, "SELECT FKP.*  FROM Frecuencia_Kpis FKP WHERE FKP.id = " . $dataKpis['obj_meses'] . "$filtroKpi ");
    $dataFrecuencia = mysqli_fetch_array($frecuencia);

    if ($_SESSION["periodo_fin_fill"] > 0 && $_SESSION["periodo_ini_fill"] > 0) {

        if ($dataFrecuencia["julio"] != "") {
            $sumaMetaF = $sumaMetaF + $dataFrecuencia["julio"];
            $contFiltroFecha++;
        }
        if ($dataFrecuencia["agosto"] != "") {
            $sumaMetaF = $sumaMetaF + $dataFrecuencia["agosto"];
            $contFiltroFecha++;
        }
        if ($dataFrecuencia["septiembre"] != "") {
            $sumaMetaF = $sumaMetaF + $dataFrecuencia["septiembre"];
            $contFiltroFecha++;
        }
        if ($dataFrecuencia["octubre"] != "") {
            $sumaMetaF = $sumaMetaF + $dataFrecuencia["octubre"];
            $contFiltroFecha++;
        }
        if ($dataFrecuencia["noviembre"] != "") {
            $sumaMetaF = $sumaMetaF + $dataFrecuencia["noviembre"];
            $contFiltroFecha++;
        }
        if ($dataFrecuencia["diciembre"] != "") {
            $sumaMetaF = $sumaMetaF + $dataFrecuencia["diciembre"];
            $contFiltroFecha++;
        }
        if ($dataFrecuencia["enero"] != "") {
            $sumaMetaF = $sumaMetaF + $dataFrecuencia["enero"];
            $contFiltroFecha++;
        }
        if ($dataFrecuencia["febrero"] != "") {
            $sumaMetaF = $sumaMetaF + $dataFrecuencia["febrero"];
            $contFiltroFecha++;
        }
        if ($dataFrecuencia["marzo"] != "") {
            $sumaMetaF = $sumaMetaF + $dataFrecuencia["marzo"];
            $contFiltroFecha++;
        }
        if ($dataFrecuencia["abril"] != "") {
            $sumaMetaF = $sumaMetaF + $dataFrecuencia["abril"];
            $contFiltroFecha++;
        }
        if ($dataFrecuencia["mayo"] != "") {
            $sumaMetaF = $sumaMetaF + $dataFrecuencia["mayo"];
            $contFiltroFecha++;
        }
        if ($dataFrecuencia["junio"] != "") {
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
        } else if($dataKpis["tipo_resultado"] == 1){
            $metaFiltro = round(($sumaMetaF/$contFiltroFecha),2);
        }else{
            $metaFiltro = $sumaMetaF;
        }
    } else {
        $metaFiltro = $dataKpis["meta"];
    }


    include("views/kpis_pc/frecuencia.php");
    // include("views/kpis_pc/progreso_mes.php");

    // $progresoF = round((($sumSeguimiento) / $dataKpis["meta"]) * 100, 2);
    $progresoAvance = round((($sumSeguimiento) / $metaFiltro) * 100, 2);
    if ($dataKpis["tipo_calculo"] == 2) {
         $progresoF = round(($meta / $sumSeguimiento) * 100, 2);
         $progresoAvance = round(($metaFiltro / $sumSeguimiento) * 100, 2);
    }

    // if ($dataKpis["unidad_medida"] == 4) {
    //     $array1 = explode(":", $sumSeguimiento);
    //     $array2 = explode(":", $dataKpis["meta"]);
    //     $array3 = explode(":", $metaFiltro);
    //     $progresoF = ProgresoHoras($array1, $array2, $dataKpis["tipo_calculo"]);
    //     $progresoAvance = ProgresoHoras($array1, $array3, $dataKpis["tipo_calculo"]);
    // }

    


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


    if (is_infinite($progresoF)) {
        $progresoF = 0;
    }
    if (is_nan($progresoF)) {
        $progresoF = 0;
    }
    if ($progresoF > 100) {
        $progresoF = 100;
    }
    if (is_infinite($progresoAvance)) {
        $progresoAvance = 0;
    }
    if (is_nan($progresoAvance)) {
        $progresoAvance = 0;
    }
    if ($progresoAvance > 100) {
        $progresoAvance = 100;
    }

    

    if ($dataKpis["tipo_kpi"] == 1) {
        $sumAvanceE = $sumAvanceE + $progresoAvance;
        // echo "suma estrategico = $sumAvanceE<br>";
        $contKpiE++;
    }

    if ($dataKpis["tipo_kpi"] == 2) {
        $sumAvanceT = $sumAvanceT + $progresoAvance;
        // echo "suma tactico = $sumAvanceT<br>";
        $contKpiT++;
    }
    // echo $progreso11;
    $sumAvanceE = $sumAvanceE + $progresoAvance;

    $suma_avances_kpies += $progresoF;
        

    $contKpiE++;
}

//echo $suma_avances_kpies; 
//echo "<br>";