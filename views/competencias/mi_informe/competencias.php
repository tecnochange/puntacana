<div class="row">
    <div class="col-md-4">
        <div class="row" style="text-align:center;">
            <div class="col-md-12" style="text-align: -webkit-center;">


                <div class="progreso-bar-container" style="--i:<?php echo round($general); ?>;--clr:<?php echo $color_competencia; ?>">
                    <div class="progreso-bar objetivo-okr">
                        <progreso id="objetivo-okr" min="0" value="<?php echo number_format($general, 1); ?>"><?php echo number_format($general, 1); ?>%</progreso>
                    </div>
                </div>
                <div style="margin-top: 10px; margin-bottom: 20px">
                    Resultado Total
                </div>

            </div>

        </div>
        <div class="row" style="text-align:center;">
            <div class="col-md-12">
                <figure class="highcharts-figure">
                    <div id="grafica_<?php echo $dataComp["id"]; ?>"></div>
                </figure>
            </div>
            <style>
                #grafica_<?php echo $dataComp["id"]; ?> {
                    max-width: 100%;
                    margin: 1em auto;
                }
            </style>
            <script>
                Highcharts.chart('grafica_<?php echo $dataComp["id"]; ?>', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: 'Gráfico Competencia',
                        align: 'center'
                    },
                    xAxis: {
                        categories: ['Porcentaje Competencias']
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: 'Porcentaje'
                        }
                    },
                    tooltip: {
                        valueSuffix: '%'
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0
                        }
                    },
                    series: [
                        <?php if ($permitir_auto) {
                        ?> {
                                name: 'Auto',
                                data: [<?php echo round($auto, 1); ?>],
                                color: '<?php echo RetornarColor($auto, $rangos) ?>'
                            },
                        <?php } ?>
                        <?php if ($permitir_jefe) {
                        ?> {
                                name: 'Supervisor',
                                data: [<?php echo round($jefe, 1); ?>],
                                color: '<?php echo RetornarColor($jefe, $rangos) ?>'
                            },
                        <?php } ?>
                        <?php if ($permitir_cliente) {
                        ?> {
                                name: 'Cliente',
                                data: [<?php echo round($cliente, 1); ?>],
                                color: '<?php echo RetornarColor($cliente, $rangos) ?>'
                            },
                        <?php } ?>
                        <?php if ($permitir_par) {
                        ?> {
                                name: 'Par',
                                data: [<?php echo round($par, 1); ?>],
                                color: '<?php echo RetornarColor($par, $rangos) ?>'
                            },
                        <?php } ?>
                        <?php if ($permitir_col) {
                        ?> {
                                name: 'Colaborador',
                                data: [<?php echo round($colaborador, 1); ?>],
                                color: '<?php echo RetornarColor($colaborador, $rangos) ?>'
                            }
                        <?php } ?>

                    ]
                });
            </script>

        </div>
    </div>
    <div class="col-md-1"></div>
    <div class="col-md-7">
        <?php
        $lista_resultados = "";
        $lista_fortalezas = "";
        $lista_oportunidades = "";

        $queryNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $competencia . "' ");
        $dataNivel = mysqli_fetch_array($queryNivel);

        $queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataNivel["id_competencia"] . "' ");
        $dataComp = mysqli_fetch_array($queryComp);

        $VALIDACION = PromedioGeneralEvaluadoPreguntas($id_evaluado, $connect_valoracion, $connect_admin);
        //$datos_ponderar = $VALIDACION["datos_ponderar"];
        // echo "<pre>";
        // 										print_r($VALIDACION);
        // 										echo "<pre>";
        foreach ($nodo_comp["comportamientos"] as $comportamiento) {

            $nodo_comporta =  ObtenerComportamientosSinConsolidarPreguntas($competencia, $comportamiento, $COMPETENCIAS, $EVALUACIONES); 
            $nodo_comporta_dos =  ObtenerComportamientosConsolidadasPreguntas($competencia, $comportamiento, $COMPETENCIAS, $EVALUACIONES);
            // echo "<pre>";
            //print_r($nodo_comporta);
            // echo "</pre>";
            $queryPregTmp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Preguntas WHERE id = '" . $comportamiento . "'");
            $dataPregTmp = mysqli_fetch_array($queryPregTmp);
            // print_r($dataPregTmp);
            // echo "<br>";
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
            $general = $nodo_comporta_dos["general"] * 100 / 5;
            //$general = $jefe;
            if ($general > 100) {
                $general = 100;
            }

            if ($general >= 88) {
                if ($dataPregTmp["pregunta"]) {
                    $lista_fortalezas .= "<li>" . $dataPregTmp["pregunta"] . "</li>";
                }
            }

            if ($general <= 75) {
                if ($dataPregTmp["pregunta"]) {
                    $lista_oportunidades .= "<li>" . $dataPregTmp["pregunta"] . "</li>";
                }
            }

            $lista_resultados .= '
						<tr>
							<td>' . $dataPregTmp["pregunta"] . '</td>
							<td>' . $dataComp["nombre"] . '</td>
					';

            if ($permitir_auto) {
                $lista_resultados .= '<td>' . round($auto, 1) . '%</td>';
            }
            if ($permitir_jefe) {
                $lista_resultados .= '<td>' . round($jefe, 1) . '%</td>';
            }
            if ($permitir_cliente) {
                $lista_resultados .= '<td>' . round($cliente, 1) . '%</td>';
            }
            if ($permitir_par) {
                $lista_resultados .= '<td>' . round($par, 1) . '%</td>';
            }
            if ($permitir_col) {
                $lista_resultados .= '<td>' . round($colaborador, 1) . '%</td>';
            }

            $lista_resultados .= '
							<td>' . round($general, 1) . '%</td>
						</tr>
					';
        }

        ?>

        <?php if ($lista_fortalezas) { ?>
            <h2 style="margin-bottom: 20px; margin-top: 20px" align="center">
                COMPORTAMIENTOS QUE SE CONSTITUYEN EN FORTALEZA
            </h2>
            <div>
                <ul>
                    <?php echo eliminar_tildes($lista_fortalezas); ?>
                </ul>
            </div>
        <?php } ?>

        <br><br>
        <?php if ($lista_oportunidades) { ?>
            <h2 style="margin-bottom: 20px; margin-top: 20px" align="center">
                COMPORTAMIENTOS CON ÁREAS DE OPORTUNIDAD
            </h2>
            <div>
                <ul>
                    <?php echo eliminar_tildes($lista_oportunidades); ?>
                </ul>
            </div>
        <?php } ?>

        <br><br>
        <?php if ($comentarios_competencia) { ?>
            <h2 style="margin-bottom: 20px; margin-top: 20px" align="center">
                COMENTARIOS POR COMPETENCIA
            </h2>
            <div>
                <ul>
                    <?php echo eliminar_tildes($comentarios_competencia); ?>
                </ul>
            </div>
        <?php } ?>

        <br><br>
        <h2 style="margin-bottom: 20px; margin-top: 20px" align="center">
            RESULTADOS POR COMPORTAMIENTO
        </h2>
        <p>
            A continuación encontrará las fortalezas y las áreas de oportunidad por cada una de las competencias.<br><br>

            Los comportamientos por encima de 80% se consideraron como fortalezas y los que están por debajo
            de este porcentaje se categorizaron dentro de las áreas de oportunidad.
            Cabe mencionar que el sistema tomó la calificación de cada evaluador frente a cada comportamiento, generando un promedio final, el cual se tomo como base para la clasificación y la identificación del nivel de desarrollo.<br><br>

            Los análisis y recomendaciones parten de los rangos de calificación establecidos a partir de la
            evidencia, frecuencia y consistencia de los comportamientos asociados a cada competencia. <br><br>
        </p>
        <table class="table" style="margin-bottom: 50px">
            <tr class="table-dark">
                <th>Comportamiento</th>
                <th>Competencia</th>
                <?php if ($permitir_auto) { ?><th>Auto <?= $datos_ponderar["auto"]; ?>%</th><?php } ?>
                <?php if ($permitir_jefe) { ?><th>Líder/Supervisor <?= $datos_ponderar["jefe"]; ?>%</th><?php } ?>
                <?php if ($permitir_cliente) { ?><th>Cliente Interno <?= $datos_ponderar["cliente"]; ?>%</th><?php } ?>
                <?php if ($permitir_par) { ?><th>Par <?= $datos_ponderar["par"]; ?>%</th><?php } ?>
                <?php if ($permitir_col) { ?><th>Colaborador <?= $datos_ponderar["subalterno"]; ?>%</th><?php } ?>
                <th>Resultado</th>
            </tr>
            <?php echo eliminar_tildes($lista_resultados); ?>
        </table>
    </div>

</div>


