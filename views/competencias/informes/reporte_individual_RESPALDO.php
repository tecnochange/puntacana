<script>
    $(document).ready(function() {
        $('#menuCompetencias').collapse();
        $('#bt_competencias_reportes').addClass('active');
    });
</script>

<?php
include("app/models/competencias/Competencias.php");
$ClassCompetencias = new Competencias();
$dataCicloVal = $ClassCompetencias->Ciclo($user_log["id_empresa"], $_SESSION["anio_ciclo"]);

if (isset($_GET["e"]) && !empty($_GET["e"])) {
    $colaborador = $ClassCompetencias->Empleado($user_log["id_empresa"]);
} else {
    echo "No hay un identificador válido";
    return;
}
?>

<script src="assets/js/highcharts/code/highcharts.js"></script>
<script src="assets/js/highcharts/code/highcharts-more.js"></script>
<script src="assets/js/highcharts/code/modules/exporting.js"></script>
<script src="assets/js/highcharts/code/modules/export-data.js"></script>
<script src="assets/js/highcharts/code/modules/accessibility.js"></script>

<style>
    /* Estilo del texto dentro del círculo */
    .easy-pie-chart,
    .easy-pie-chart-acordeon {
        /* Centra el texto vertical y horizontalmente */
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 30px;
        font-weight: bold;
        color: #333;
        /* Asegúrate de que el contenedor del texto pueda tener margen */
        line-height: normal;
    }

    .okr-progress-value {
        position: absolute;
        margin: auto;
        font-size: 1.5em;
    }
</style>
<style>
    .accordion-button {
        padding: 0.75rem 1rem;
        background-color: #fff;
    }

    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
    }

    .accordion-button::after {
        margin-left: auto;
    }

    .progress {
        background-color: #e9ecef;
    }

    .progress-bar {
        font-size: 11px;
        line-height: 12px;
    }
</style>

<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Valoración por Competencias <?= $dataCicloVal["anio"]; ?> | <small>Ciclo: <?php echo $dataCicloVal["nombre"]; ?></small> </h3>
        </div>
    </div>

    <!-- COLABORADOR -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">REPORTE INDIVIDUAL</div>
                <div class="card-body">
                    <h4><?= $colaborador["nombre"]; ?></h4>
                    Documento: <b><?= $colaborador["documento"]; ?></b> <br>
                    Fecha de Valoración: <br>
                    Cargo: <b><?= $colaborador["nombre_cargo"]; ?></b> <br>
                    Vicepresidencia: <b><?= $colaborador["nombre_vicepresidencia"]; ?></b> <br>
                    Área: <b><?= $colaborador["nombre_area"]; ?></b>
                </div>
            </div>
        </div>
    </div>

    <!-- CONTENIDO -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">RESULTADO GLOBAL EVALUACIÓN DE COMPETENCIAS</div>
                <div class="card-body">
                    <div class="row">

                        <!-- GRAFICA easy-pie-chart -->
                        <div class="col-md-4 text-center d-flex align-items-center justify-content-center">
                            <div class="row circular-chart-component">
                                <div class="easy-pie-chart" data-percent="25">
                                    <div class="okr-progress-value">
                                        25%
                                    </div>
                                </div>
                                <p class="progress-label"> <b>Resultado Total</b> </p>
                            </div>
                        </div>

                        <!-- GRAFICA SPIDER -->
                        <div class="col-md-8 text-center">
                            <div class="row" style="text-align:center;">
                                <div class="col-md-12">
                                    <div id="spider"></div>
                                </div>
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
                                                'Comunicación',
                                                'Trabajo en equipo',
                                                'Liderazgo',
                                                'Responsabilidad',
                                                'Resolución de problemas',
                                                'Adaptabilidad'
                                            ],
                                            tickmarkPlacement: 'on',
                                            lineWidth: 0
                                        },

                                        yAxis: {
                                            gridLineInterpolation: 'polygon',
                                            lineWidth: 0,
                                            min: 0,
                                            max: 100
                                        },

                                        tooltip: {
                                            shared: true,
                                            pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y}%</b><br/>'
                                        },

                                        legend: {
                                            align: 'right',
                                            verticalAlign: 'middle',
                                            layout: 'vertical'
                                        },

                                        series: [{
                                                name: 'Auto',
                                                data: [85, 78, 90, 88, 76, 82],
                                                pointPlacement: 'on'
                                            },
                                            {
                                                name: 'Supervisor',
                                                data: [80, 74, 86, 90, 70, 79],
                                                pointPlacement: 'on'
                                            }
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ESCALA DE INTERPRETACION -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">ESCALA DE INTERPRETACION</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <?php include("views/competencias/componentes/comp_escala.php"); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ACORDEON -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="accordion" id="accordionCompetencias">

                                <!-- ITEM -->
                                <div class="accordion-item border mb-2">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed d-flex align-items-center"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#comp1">

                                            <div class="row w-100 text-center align-items-center">
                                                <div class="col-md-6 text-start fw-bold">
                                                    TRABAJO EN EQUIPO
                                                </div>

                                                <div class="col-md-3">
                                                    80.0%
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="progress" style="height: 12px;">
                                                        <div class="progress-bar text-dark" style="width:80%; background-color: #fff200;">
                                                            80%
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </button>
                                    </h2>
                                    <div id="comp1" class="accordion-collapse collapse" data-bs-parent="#accordionCompetencias">
                                        <div class="accordion-body">
                                            <!-- CONTENIDO INTERNO -->
                                            <h4 class="text-center">TRABAJO EN EQUIPO</h4>
                                            <div class="row">

                                                <!-- GRAFICAS -->
                                                <div class="col-md-4 text-center">
                                                    <div class="row circular-chart-component d-flex align-items-center justify-content-center">
                                                        <div class="easy-pie-chart-acordeon" data-percent="25">
                                                            <div class="okr-progress-value">
                                                                25%
                                                            </div>
                                                        </div>
                                                        <p class="progress-label"> <b>Resultado Total</b> </p>
                                                    </div>
                                                    <div class="row">
                                                        <h4 class="text-center">Gráfico Competencia</h4>
                                                        <div class="d-flex justify-content-center" style="height:200px;">
                                                            <canvas id="graficoCompetencia" height="200"></canvas>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-8 mt-4">
                                                    <h5>RESULTADOS POR COMPORTAMIENTO</h5>
                                                    <p>A continuación encontrará las fortalezas y las áreas de oportunidad por cada una de las competencias.</p>
                                                    <p>Los comportamientos por encima de 80% se consideraron como fortalezas y los que están por debajo de este porcentaje se categorizaron dentro de las áreas de oportunidad. Cabe mencionar que el sistema tomó la calificación de cada evaluador frente a cada comportamiento, generando un promedio final, el cual se tomo como base para la clasificación y la identificación del nivel de desarrollo.</p>
                                                    <p>Los análisis y recomendaciones parten de los rangos de calificación establecidos a partir de la evidencia, frecuencia y consistencia de los comportamientos asociados a cada competencia.</p>
                                                    <!-- TABLA DE RESULTADOS -->
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="table-responsive">
                                                                <table class="table" id="resultadosComportamiento">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">Comportamiento</th>
                                                                            <th scope="col">Competencia</th>
                                                                            <th scope="col">Líder/Supervisor</th>
                                                                            <th scope="col">Resultado</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td></td>
                                                                            <td></td>
                                                                            <td></td>
                                                                            <td></td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- REPLICAR ITEMS -->
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- PLAN DE DESARROLLO INDIVIDUAL -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">PLAN DE DESARROLLO INDIVIDUAL</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table" id="planDesarrolloIndividual">
                                    <thead>
                                        <tr>
                                            <th scope="col">Año</th>
                                            <th scope="col">Competencia</th>
                                            <th scope="col">Plan de Acción</th>
                                            <th scope="col">Prioridad</th>
                                            <th scope="col">Jefe</th>
                                            <th scope="col">Fecha Inicio</th>
                                            <th scope="col">Fecha Entrega</th>
                                            <th scope="col">Estado</th>
                                            <th scope="col">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RECOMENDACIONES PARA GENERAR PLANES DE ACCIÓN -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">RECOMENDACIONES PARA GENERAR PLANES DE ACCIÓN</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h6 class="fw-bold mt-3" style="color: #59008e !important;">Aprendizaje a través de la experiencia</h6>
                            <ul>
                                <li><strong>Desafíos y Nuevas Responsabilidades:</strong> Asigna a los colaboradores nuevos retos y responsabilidades que expandan sus habilidades.</li>
                                <li><strong>Exposición a Proyectos:</strong> Involucra al equipo en proyectos clave y asignaciones novedosas para que adquieran experiencia práctica en áreas críticas.</li>
                            </ul>

                            <h6 class="fw-bold mt-3" style="color: #59008e !important;">Interacción con otros</h6>
                            <ul>
                                <li><strong>Sesiones de Retroalimentación:</strong> Facilita sesiones regulares de retroalimentación con líderes, colegas y clientes para identificar áreas de mejora y celebrar los logros.</li>
                                <li><strong>Mentoría:</strong> Establece relaciones de mentoría y coaching dentro del equipo, proporcionando guía y apoyo personalizado.</li>
                                <li><strong>Observación:</strong> Fomenta la observación de mejores prácticas dentro y fuera del equipo, para aprender de ejemplos reales y aplicables.</li>
                            </ul>

                            <h6 class="fw-bold mt-3" style="color: #59008e !important;">Programas educativos</h6>
                            <ul>
                                <li><strong>Capacitaciones:</strong> Promueve la participación en capacitaciones, cursos y programas de e-learning que sean relevantes para el desarrollo profesional del colaborador.</li>
                                <li><strong>Autoestudio:</strong> Incentiva el autoestudio y la lectura de materiales especializados que complementen el conocimiento y habilidades requeridas.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FIRMA DE APROBACIÓN -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">FIRMA DE APROBACIÓN</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            Firma de Aprobación: <?= $colaborador["nombre"]; ?> <br>
                            Documento: <?= $colaborador["documento"]; ?> <br>
                            Fecha de firma:
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/easy-pie-chart@2.1.7/dist/jquery.easypiechart.min.js"></script>

<script>
    $(document).ready(function() {
        $('.easy-pie-chart').easyPieChart({
            // Opciones principales
            size: 250, // Diámetro del círculo
            lineWidth: 20, // Grosor del anillo
            barColor: '#95fa03', // Color de la parte llena (verde)
            trackColor: '#E6E6E6', // Color de la parte vacía (gris claro)
            scaleColor: false, // Oculta las marcas de escala
            lineCap: 'butt', // Estilo del final del progreso ('round' para redondeado)
            animate: 1000, // Duración de la animación en ms al cargar

            // Función para actualizar el texto si el valor es dinámico
            onStep: function(from, to, percent) {
                // Este código mantiene el número centrado y lo actualiza durante la animación
                $(this.el).find('.okr-progress-value').text(Math.round(percent) + '%');
            }
        });

        $('.easy-pie-chart-acordeon').easyPieChart({
            // Opciones principales
            size: 150, // Diámetro del círculo
            lineWidth: 20, // Grosor del anillo
            barColor: '#95fa03', // Color de la parte llena (verde)
            trackColor: '#E6E6E6', // Color de la parte vacía (gris claro)
            scaleColor: false, // Oculta las marcas de escala
            lineCap: 'butt', // Estilo del final del progreso ('round' para redondeado)
            animate: 1000, // Duración de la animación en ms al cargar

            // Función para actualizar el texto si el valor es dinámico
            onStep: function(from, to, percent) {
                // Este código mantiene el número centrado y lo actualiza durante la animación
                $(this.el).find('.okr-progress-value').text(Math.round(percent) + '%');
            }
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    const ctxPrioridad = document.getElementById("graficoCompetencia");

    // Categorías
    const labelsPrioridad = ["Porcentaje Competencias"];

    // 🔹 Datos ficticios (cantidades)
    const valuesPrioridad = [12];

    const totalPrioridad = valuesPrioridad.reduce((a, b) => a + b, 0);

    // Colores por categoría
    const colorsPA = [
        "#95fa03", // Porcentaje Competencias
    ];

    new Chart(ctxPrioridad, {
        type: 'bar',
        data: {
            labels: labelsPrioridad,
            datasets: [{
                label: "Cantidad por prioridad",
                data: valuesPrioridad,
                backgroundColor: colorsPA,
                borderWidth: 0,
                barThickness: 40
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false,
                    labels: {
                        generateLabels: function(chart) {
                            const data = chart.data;
                            return data.labels.map((label, i) => {
                                const value = data.datasets[0].data[i];
                                const pct = totalPrioridad > 0 ?
                                    Math.round((value / totalPrioridad) * 100) :
                                    0;

                                return {
                                    text: `${label} (${pct}%)`,
                                    fillStyle: colorsPA[i],
                                    strokeStyle: colorsPA[i],
                                    hidden: value === 0,
                                    index: i
                                };
                            });
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const pct = totalPrioridad > 0 ?
                                Math.round((value / totalPrioridad) * 100) :
                                0;
                            return `${context.label}: ${value} (${pct}%)`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: "Supervisor"
                    }
                },
                y: {
                    beginAtZero: true,
                    suggestedMax: Math.max(...valuesPrioridad) + 5,
                    title: {
                        display: true,
                        text: "Porcentaje"
                    }
                }
            }
        }
    });
</script>