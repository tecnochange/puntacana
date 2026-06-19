<div class="row" style="align-items: center;">
    <div class="col-md-4" align="center" id="porcentajeTotal">
        
    </div>
    <div class="col-md-8">
        <?php
        $auto_total = 0;
        $jefe_total = 0;
        $par_total = 0;
        $colaborador_total = 0;
        $cliente_total = 0;
        $general_total = 0;

        $obj_dimensiones = '[';
        $obj_promedios = '[';
        $obj_promedios_jefe = '[';
        $obj_promedios_par = '[';
        $obj_promedios_cola = '[';
        $obj_promedios_cliente = '[';

        $competencias = $competenciasAuto = $competenciasSupervisor = "";

        foreach ($COMPETENCIAS as $competencia) {
            $queryNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $competencia . "' ");
            $dataNivel = mysqli_fetch_array($queryNivel);

            $queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataNivel["id_competencia"] . "' ");
            $dataComp = mysqli_fetch_array($queryComp);

            $competencias .= "'" . eliminar_tildes($dataComp["nombre"]) . "',";

            $nodo_comp =  ObtenerCompetenciasConsolidadas($competencia, $COMPETENCIAS, $EVALUACIONES);

            $auto = 0;
            $jefe = 0;
            $par = 0;
            $colaborador = 0;
            $cliente = 0;
            foreach ($nodo_comp["evaluadores"] as $eval) {

                $resultado_promediado = ($eval["sin_ponderacion"] / $eval["cantidad"]);

                if ($eval["tipo"] == 1) {
                    $auto = $resultado_promediado;
                    $auto_total += $resultado_promediado;
                }
                if ($eval["tipo"] == 5) {
                    $jefe = $resultado_promediado;
                    $jefe_total += $resultado_promediado;
                }
                if ($eval["tipo"] == 2) {
                    $par = $resultado_promediado;
                    $par_total += $resultado_promediado;
                }
                if ($eval["tipo"] == 3) {
                    $colaborador = $resultado_promediado;
                    $colaborador_total += $resultado_promediado;
                }
                if ($eval["tipo"] == 4) {
                    $cliente = $resultado_promediado;
                    $cliente_total += $resultado_promediado;
                }

                $general_total += ($eval["promedio"] / $eval["cantidad"]);
            }

            $auto = $auto * 100 / 5;
            if ($auto > 100) {
                $auto = 100;
            }
            $jefe = $jefe * 100 / 5;
            if ($jefe > 100) {
                $jefe = 100;
            }
            $par = $par * 100 / 5;
            $colaborador = $colaborador * 100 / 5;
            $cliente = $cliente * 100 / 5;
            $general = $nodo_comp["general"] * 100 / 5;
            $obj_dimensiones .= "'" . $dataComp["nombre"] . "' , ";
            //$obj_dimensiones .= "'auto - ".round( $general,1)."%', ";							
            $competenciasAuto .= $auto . ",";
            $competenciasSupervisor .= $jefe . ", ";
            $competenciasCliente .= $cliente . ", ";
                            $competenciasPar .= $par . ", ";
                            $competenciasColaborador .= $colaborador . ", ";




            $obj_promedios_par .= " " . $par . ", ";
            $obj_promedios_cola .= " " . $colaborador . ", ";
            $obj_promedios_cliente .= " " . $cliente . ", ";

            $queryNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $competencia . "' ");
            $dataNivel = mysqli_fetch_array($queryNivel);

            $queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataNivel["id_competencia"] . "' ");
            $dataComp = mysqli_fetch_array($queryComp);

            $nodo_comp =  ObtenerCompetenciasConsolidadas($competencia, $COMPETENCIAS, $EVALUACIONES);

            $auto = 0;
            $jefe = 0;
            $par = 0;
            $colaborador = 0;
            $cliente = 0;
            foreach ($nodo_comp["evaluadores"] as $eval) {
                $resultado_promediado = ($eval["sin_ponderacion"] / $eval["cantidad"]);
                // echo $resultado_promediado;
                if ($resultado_promediado > 100) {
                    $resultado_promediado = 100;
                }

                if ($eval["tipo"] == 1) {
                    $auto = $resultado_promediado;
                    $auto_total += $resultado_promediado;
                }
                if ($eval["tipo"] == 5) {
                    $jefe = $resultado_promediado;
                    $jefe_total += $resultado_promediado;
                }
                if ($eval["tipo"] == 2) {
                    $par = $resultado_promediado;
                    $par_total += $resultado_promediado;
                }
                if ($eval["tipo"] == 3) {
                    $colaborador = $resultado_promediado;
                    $colaborador_total += $resultado_promediado;
                }
                if ($eval["tipo"] == 4) {
                    $cliente = $resultado_promediado;
                    $cliente_total += $resultado_promediado;
                }

                $general_total += ($eval["promedio"] / $eval["cantidad"]);
            }

            $auto = $auto * 100 / 4;
            if ($auto > 100) {
                $auto = 100;
            }
            $jefe = $jefe * 100 / 4;
            if ($jefe > 100) {
                $jefe = 100;
            }
            $par = $par * 100 / 4;
            $colaborador = $colaborador * 100 / 4;
            $cliente = $cliente * 100 / 4;
            $general = $nodo_comp["general"] * 100 / 4;

            $color_competencia = RetornarColor($general, $rangos);
            $color_auto = RetornarColor($auto, $rangos);
            $color_jefe = RetornarColor($jefe, $rangos);
            $color_cliente = RetornarColor($cliente, $rangos);
            $color_par = RetornarColor($par, $rangos);
            $color_cola = RetornarColor($colaborador, $rangos);

            $comentarios_competencia = '';
            //COMENTARIOS
            foreach ($EVALUACIONES as $evaluacion) {

                $txt_tipo = "";
                if ($evaluacion["tipo_evaluacion"] == 1) {
                    $txt_tipo = "Auto";
                }
                if ($evaluacion["tipo_evaluacion"] == 5) {
                    $txt_tipo = "Supervisor";
                }
                if ($evaluacion["tipo_evaluacion"] == 2) {
                    $txt_tipo = "Par";
                }
                if ($evaluacion["tipo_evaluacion"] == 3) {
                    $txt_tipo = "Colaborador";
                }
                if ($evaluacion["tipo_evaluacion"] == 4) {
                    $txt_tipo = "Cliente";
                }

                $Array_Objeto = json_decode($evaluacion["obj_evaluacion"], true);
            }

            $lista_resultados = "";
            $lista_fortalezas = "";
            $lista_oportunidades = "";

            $queryNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $competencia . "' ");
            $dataNivel = mysqli_fetch_array($queryNivel);

            $queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataNivel["id_competencia"] . "' ");
            $dataComp = mysqli_fetch_array($queryComp);

            foreach ($nodo_comp["comportamientos"] as $comportamiento) {

                $nodo_comporta =  ObtenerComportamientosConsolidadas($competencia, $comportamiento, $COMPETENCIAS, $EVALUACIONES);

                $queryPregTmp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Preguntas WHERE id = '" . $comportamiento . "'");
                $dataPregTmp = mysqli_fetch_array($queryPregTmp);

                $auto = 0;
                $jefe = 0;
                $par = 0;
                $colaborador = 0;
                $cliente = 0;

                foreach ($nodo_comporta["evaluadores"] as $eval) {
                    $resultado_promediado = ($eval["promedio"] / $eval["cantidad"]);

                    if ($eval["tipo"] == 1) {
                        $auto = $resultado_promediado;
                    }
                    if ($eval["tipo"] == 5) {
                        $jefe = $resultado_promediado;
                    }
                    if ($eval["tipo"] == 2) {
                        $par = $resultado_promediado;
                    }
                    if ($eval["tipo"] == 3) {
                        $colaborador = $resultado_promediado;
                    }
                    if ($eval["tipo"] == 4) {
                        $cliente = $resultado_promediado;
                    }

                    $general_total += ($eval["promedio"] / $eval["cantidad"]);
                }

                $auto = $auto * 100 / 4;
                if ($auto > 100) {
                    $auto = 100;
                }
                $jefe = $jefe * 100 / 4;
                if ($jefe > 100) {
                    $jefe = 100;
                }
                $par = $par * 100 / 4;
                $colaborador = $colaborador * 100 / 4;
                $cliente = $cliente * 100 / 6;
                $general = $nodo_comporta["general"] * 100 / 4;

                $obj_dimensiones .= ']';
                $obj_promedios .= ']';
                $obj_promedios_jefe .= ']';
                $obj_promedios_par .= ']';
                $obj_promedios_cola .= ']';
                $obj_promedios_cliente .= ']';

                $por_1 = ($auto_total / count($COMPETENCIAS));
                $por_2 = ($jefe_total / count($COMPETENCIAS));
                $por_3 = ($cliente_total / count($COMPETENCIAS));
                $por_4 = ($par_total / count($COMPETENCIAS));
                $por_5 = ($colaborador_total / count($COMPETENCIAS));
                $por_6 = ($general_total / count($COMPETENCIAS));

                $por_1 = $por_1 * 100 / 4;
                $por_2 = $por_2 * 100 / 4;
                $por_3 = $por_3 * 100 / 4;
                $por_4 = $por_4 * 100 / 4;
                $por_5 = $por_5 * 100 / 4;
                $por_6 = $por_6 * 100 / 4;
            }
        }

        ?>
        <div id="spider"></div>
        <script>
            Highcharts.chart('spider', {

                chart: {
                    polar: true,
                    type: 'line'
                },

                title: {
                    text: 'Evaluación de Competencias',
                    x: -80
                },

                pane: {
                    size: '80%'
                },

                xAxis: {
                    categories: [
                        <?php echo eliminar_tildes($competencias); ?>
                    ],
                    tickmarkPlacement: 'on',
                    lineWidth: 0
                },

                yAxis: {
                    gridLineInterpolation: 'polygon',
                    lineWidth: 0,
                    min: 0
                },

                tooltip: {
                    shared: true,
                    pointFormat: '<span style="color:{series.color}">{series.name}: <b>' +
                        '{point.y:,.0f}</b><br/>'
                },

                legend: {
                    align: 'right',
                    verticalAlign: 'middle',
                    layout: 'vertical'
                },

                series: [
                    <?php if ($permitir_auto) { ?> {
                            name: 'Auto',
                            data: [<?php echo $competenciasAuto; ?>],
                            pointPlacement: 'on'
                        },
                    <?php } ?>
                    <?php if ($permitir_jefe) { ?> {
                            name: 'Supervisor',
                            data: [<?php echo $competenciasSupervisor; ?>],
                            pointPlacement: 'on'
                        },
                    <?php } ?>

                    <?php if ($permitir_cliente) { ?> {
											name: 'Cliente',
											data: [<?php echo $competenciasCliente; ?>],
											pointPlacement: 'on'
										},
									<?php } ?>

                                    <?php if ($permitir_par) { ?> {
											name: 'Par',
											data: [<?php echo $competenciasPar; ?>],
											pointPlacement: 'on'
										},
									<?php } ?>

                                    <?php if ($permitir_col) { ?> {
											name: 'Colaborador',
											data: [<?php echo $competenciasColaborador; ?>],
											pointPlacement: 'on'
										}
									<?php } ?>
                ],

                responsive: {
                    rules: [{
                        condition: {
                            maxWidth: 500
                        },
                        chartOptions: {
                            legend: {
                                align: 'center',
                                verticalAlign: 'bottom',
                                layout: 'horizontal'
                            },
                            pane: {
                                size: '70%'
                            }
                        }
                    }]
                }

            });
        </script>
        <p style="text-align:justify;">
            La gráfica que encuentra a continuación, le permitirá identificar de manera diferenciada las opiniones y percepciones por competencias de los diferentes evaluadores, analizar y comparar su autoevaluación con respecto a la percepción de los otros evaluadores, con el fin de encontrar puntos ciegos a nivel de fortalezas y áreas de oportunidad. Es importante también la homogeneidad de los resultados y niveles de dispersión entre los evaluadores.
        </p>
    </div>
</div>