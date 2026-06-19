<div class="table-responsive">
    <table border="1" id="frecuencia_<?php echo $dataKPI["id"]; ?>" class="display table" style="width:100%">
        <thead>
            <tr>
                <th>MES</th>
                <?php
                $queryEmpresa = mysqli_query($connect_admin, "SELECT * FROM Empresas WHERE id = '" . $id_empresa . "'");
                $dataEmpresa = mysqli_fetch_array($queryEmpresa);
                $mesInicio = $dataEmpresa["mes_inicio"];
                $mesFin = $dataEmpresa["mes_fin"];
                $inicio = new DateTime($dataEmpresa["mes_inicio"]);
                $mes_inicio = (int)$inicio->format('m');
                $mes_actual = date("n");
                $anio_actual = date("Y");
                // $meses_permitidos = obtenerMesesPermitidos($mes_actual, $anio_actual, $mesInicio, 4);
                $meses_permitidos = obtenerMesesPermitidos($mesInicio, $mesFin, 4);
                // print_r($meses_permitidos);
                $listadoMesFiscal = agruparMesesPorFrecuencia($mesInicio, 4);
                // print_r($listadoMesFiscal);
                $keys = array_keys($listadoMesFiscal);
                /**
                 * Validar si el avance tiene comentario
                 * */
                $comentariosMeses = [];
                $avanceFrecuenciaComentario = mysqli_query($connect_kpis, "SELECT id, id_empleado, id_kpi, frecuencia FROM Comentarios_Kpis WHERE id_empresa = '" . $id_empresa . "' AND id_kpi = " . $dataKPI["id"]."");

                while ($fila = mysqli_fetch_assoc($avanceFrecuenciaComentario)) {
                    $comentariosMeses[] = strtolower(trim($fila['frecuencia']));
                }

                $frecuencia = $dataKPI["frecuencia"];
                $botonAnalisis = "";

                // Determinar qué columnas se van a revisar según frecuencia
                switch (strtolower($frecuencia)) {
                    case '4': // SEMESTRAL
                        $bloques = [
                            1 => [7, 12], // Julio - Diciembre = avance_12
                            2 => [1, 6],  // Enero - Junio = avance_6
                        ];
                        break;
                    default:
                        $bloques = [];
                }

                // Este for recorre cada columna visual (cada <th>)
                foreach ($keys as $indice) {
                    $mes = $listadoMesFiscal[$indice]; // Ej: "Julio - Diciembre"

                    $botonAnalisis = "";

                    if ($frecuencia == '4') {
                        // Mapeamos según bloque: si contiene julio o diciembre => 12, si contiene enero o junio => 6
                        if (strpos($mes, 'Julio') !== false || strpos($mes, 'Diciembre') !== false) {
                            $avanceCampo = 'avance_12';
                        } elseif (strpos($mes, 'Enero') !== false || strpos($mes, 'Junio') !== false) {
                            $avanceCampo = 'avance_6';
                        } else {
                            $avanceCampo = null;
                        }
                        // echo "Mes actual: $mes, campo de avance: $avanceCampo, valor: " . $dataFrecuencia[$avanceCampo];

                        if ($avanceCampo && !empty($dataFrecuencia[$avanceCampo]) && $dataFrecuencia[$avanceCampo] > 0) {
                            // Hay avance, mostrar el botón correcto
                            if (in_array(strtolower($mes), $comentariosMeses)) {
                                $botonAnalisis = '<br><span class="badge bg-success" style="font-weight: 100 !important;font-size: 1em !important; color: white !important; cursor: pointer;">Análisis Finalizado</span>';
                            } else {
                                $botonAnalisis = '<br><span class="badge bg-danger" style="font-weight: 100 !important;font-size: 1em !important; color: white !important; cursor: pointer;" onClick="AgregarComentario(' . $dataKPI["id"] . ',' . $_SESSION['id_user'] . ',' . $_SESSION['role_plataforma'] . ',' . $_SESSION['id_empresa'] . ');">Pendiente Análisis</span>';
                            }
                        }
                    }

                    echo '<th>' . $mes . $botonAnalisis . '</th>';
                }
                ?>
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>META</td>
                <?php
                $listadoMesFiscal1 = agruparMesesPorFrecuencia($mesInicio, 4);
                include("tabla_fila_meta.php");
                ?>
                <td><?php if ($_SESSION["periodo_fin_fill"] > 0 && $_SESSION["periodo_ini_fill"] > 0) {
                        if ($dataKPI["tipo_resultado"] == 3) {

                            switch ($_SESSION["periodo_fin_fill"]) {
                                case 7:
                                    $sumaMetaS = $dataFrecuencia["julio"];
                                    break;
                                case 8:
                                    $sumaMetaS = $dataFrecuencia["agosto"];
                                    break;
                                case 9:
                                    $sumaMetaS = $dataFrecuencia["septiembre"];
                                    break;
                                case 10:
                                    $sumaMetaS = $dataFrecuencia["octubre"];
                                    break;
                                case 11:
                                    $sumaMetaS = $dataFrecuencia["noviembre"];
                                    break;
                                case 12:
                                    $sumaMetaS = $dataFrecuencia["diciembre"];
                                    break;
                                case 1:
                                    $sumaMetaS = $dataFrecuencia["enero"];
                                    break;
                                case 2:
                                    $sumaMetaS = $dataFrecuencia["febrero"];
                                    break;
                                case 3:
                                    $sumaMetaS = $dataFrecuencia["marzo"];
                                    break;
                                case 4:
                                    $sumaMetaS = $dataFrecuencia["abril"];
                                    break;
                                case 5:
                                    $sumaMetaS = $dataFrecuencia["mayo"];
                                    break;
                                case 6:
                                    $sumaMetaS = $dataFrecuencia["junio"];
                                    break;
                            }
                            echo $sumaMetaS;
                        } else if ($dataKPI["tipo_resultado"] == 1) {
                            echo $metaFiltro;
                        } else {
                            echo $sumaMetaS;
                        }
                    } else {
                        echo $dataKPI["meta"];
                    }
                    ?></td>
            </tr>
            <tr>
            <style>
                    .form-control:disabled,
                    .form-control[readonly] {
                        background-color: #ffffff !important;
                        opacity: 1;
                    }
                </style>
                <td>SEGUIMIENTO</td>
                <input type="hidden" name="guardar_semestral" value="true">
                <input type="hidden" name="id_frecuencia" value="<?php echo $dataKPI["obj_meses"]; ?>">
                <input type="hidden" name="id_kpi" value="<?php echo $dataKPI["id"]; ?>">
                <?php
                $queryAK = mysqli_query($connect_kpis, "SELECT * FROM Administradores_Kpi WHERE id_empleado = '" . $_SESSION["id_user"] . "' ");
                $dataAK = mysqli_fetch_array($queryAK);
                $queryEK = mysqli_query($connect_kpis, "SELECT * FROM Kpis_Colaborador WHERE id_colaborador = '" . $_SESSION["id_user"] . "' AND id_kpi = " . $dataKPI["id"] . " AND tipo = 2");
                $dataEK = mysqli_fetch_array($queryEK);
                $hidde1 = $hidde2 = $hidde3 = $hidde4 = $hidde5 = $hidde6 = $hidde7 = $hidde8 = $hidde9 = $hidde10 = $hidde11 = $hidde12 = '';
                include("permisos_rol.php");
                if ($_SESSION['role_plataforma'] == 1 || $permisoSegKPI == true) {
                // if ($_SESSION['role_plataforma'] == 1 || (mysqli_num_rows($queryAK) > 0) || (mysqli_num_rows($queryEK) > 0)) {
                    $hidde = '';
                } else {
                    $hidde = 'readonly';
                }
                // if ($dataKpis["unidad_medida"] != 4) {
                //     $listadoMesFiscal2 = agruparMesesPorFrecuencia($mesInicio, 4);
                //     include("tabla_fila_seguimiento.php");
                // } else {
                //     $listadoMesFiscal3 = agruparMesesPorFrecuencia($mesInicio, 4);
                //     include("tabla_fila_seguimiento_tiempo.php");

                // }
                include("tabla_fila_seguimiento.php");
                ?>
                <td><?php echo $sumSeguimiento; ?></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td>PROGRESO</td>
                <?php
                $listadoMesFiscal4 = agruparMesesPorFrecuencia($mesInicio, 4);
                include("tabla_fila_progreso.php");
                ?>
                <td><?php
                    if ($_SESSION["periodo_fin_fill"] > 0 && $_SESSION["periodo_ini_fill"] > 0) {

                        $meta = $metaFiltro;
                    } else {
                        $meta = $dataKPI["meta"];
                    }

                    if ($sumSeguimiento != "") {

                        $progresoF = round((($sumSeguimiento) / $meta) * 100, 2);

                        if ($dataKPI["tipo_calculo"] == 2) {
                            $progresoF = CalculoProgresoDesMes($meta, $sumSeguimiento);
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

                        if ($metaFiltro == 0 && $sumSeguimiento == 0) {
                            $progresoF = 100;
                        }
                    } else {
                        $progresoF = $progresoF1 = 0;
                    }

                    if ($dataKPI["tipo_calculo"] == 2 && $dataKPI["unidad_medida"] != 4) {

                        if ($meta >= 0 && ($sumSeguimiento === null || $sumSeguimiento === '')) {
                            $progresoF = 0; // Meta es 0 y sumSeguimiento vacío o nulo
                        } elseif ($meta >= 0 && $sumSeguimiento == 0) {
                            $progresoF = 100; // Meta y sumSeguimiento son iguales a 0
                        }  elseif ($meta == 0 && $sumSeguimiento > 0) {
                            $progresoF = max(0, 100 - ($sumSeguimiento * 10));
                        } elseif($meta == 0 && $sumSeguimiento < 0){
                            $progresoF = 100;
                        } elseif($meta > 0 && $sumSeguimiento < 0){
                            $progresoF = max(0, 100 - ($sumSeguimiento * 10));
                        }

                        if($progresoF > 100){
                            $progresoF = 100;
                        }

                    }

                    if ($dataKPI["tipo_calculo"] == 1 && $dataKPI["unidad_medida"] != 4) {

                        if ($meta == 0 && ($sumSeguimiento === null || $sumSeguimiento === '')) {
                            $progresoF = 0; // Meta es 0 y sumSeguimiento vacío o nulo
                        } elseif ($meta == 0 && $sumSeguimiento == 0) {
                            $progresoF = 100; // Meta y sumSeguimiento son iguales a 0
                        }  elseif ($meta == 0 && $sumSeguimiento > 0) {
                            // Cálculo cuando la meta es 0 y el seguimiento es mayor a 0
                            $progresoF = 100;
                        }  elseif($meta > 0 && $sumSeguimiento < 0){
                            $progresoF = max(0, 100 - ($sumSeguimiento * 10));
                        } elseif($meta == 0 && $sumSeguimiento < 0){
                            $progresoF = max(0, 100 + ($sumSeguimiento * 10));
                        }

                    }

                    if ($dataKPI["tipo_calculo"] == 3 && $dataKPI["unidad_medida"] != 4) {

                        if ($meta >= 0 && ($sumSeguimiento === null || $sumSeguimiento === '')) {
                            $progresoF = $progresoF1 = 0; // Meta es 0 y sumSeguimiento vacío o nulo
                        } elseif ($meta >= 0 && $sumSeguimiento == 0) {
                            $progresoF = $progresoF1 = 100; // Meta y sumSeguimiento son 0 (no progreso, pero la meta está cumplida)
                        } elseif ($meta == 0 && $sumSeguimiento > 0) {
                            $progresoF = $progresoF1 = 0; // Meta 0, pero seguimiento positivo (no se cumple la meta)
                        } elseif ($meta == 0 && $sumSeguimiento < 0) {
                            $progresoF = $progresoF1 = 100; // Meta 0, seguimiento negativo (se cumple la meta en este caso)
                        } elseif ($meta > 0 && $sumSeguimiento < 0) {
                            $progresoF = $progresoF1 = 0; // Meta positiva, pero seguimiento negativo (no se cumple la meta)
                        } elseif (abs($sumSeguimiento) <= $meta) {
                            $progresoF = $progresoF1 = 100; // Seguimiento dentro de los límites de la meta (cumple la meta)
                        } elseif (abs($sumSeguimiento) > $meta) {
                            $progresoF = $progresoF1 = 0; // Seguimiento mayor que la meta (no se cumple la meta)
                        }

                        if($progresoF > 100){
                            $progresoF = 100;
                        }

                    }

                    if ($sumSeguimiento != "") {
                        if ($dataKPI["unidad_medida"] == 4) {
                            $array1 = explode(":", $sumSeguimiento);
                            $array2 = explode(":", $dataKPI["meta"]);
                            $array3 = explode(":", $metaFiltro);
                            $progresoF = ProgresoHoras($array1, $array2, $dataKPI["tipo_calculo"]);
                            $progresoF1 = ProgresoHoras($array1, $array3, $dataKPI["tipo_calculo"]);
                        }
                    }

                    // echo $progreso11;
                    $escala = EscalaColor(round($progresoF), $id_empresa, $connect_valentina);
                    $back_color = "background-color:" . $escala['color_bg'] . " !important";
                    $txt_rango_meta = $escala['txt_subtitulo'];

                    $barraProgresoF = '<div class="progress" data-bs-toggle="tooltip" title="' . $txt_rango_meta . '" style="height: 15px;">
                                <div class="progress-bar bg-success progress-bar-striped active" role="progressbar" style=" color: black; width: ' . $progresoF . '%; ' . $back_color . '" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>' . $progresoF . '%';

                    echo $barraProgresoF; ?></td>
            </tr>
        </tfoot>
    </table>
</div>