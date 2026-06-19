<style>
    /* Estilo del texto dentro del círculo */
    .easy-pie-chart {
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

    /* Estilo para la etiqueta de abajo */
    .progress-label {
        text-align: center;
        font-size: 16px;
        color: #333;
        margin-top: 10px;
    }

    .flex-container {
        display: flex;
        flex-direction: colum;
        gap: 1rem;
    }

    .small-box {
        border-radius: .25rem;
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        display: block;
        margin-bottom: 20px;
        position: relative;
        color: white !important;
        padding: 10px 5px 10px 5px;
        text-align: center;
    }

    .small-box p {
        margin-bottom: 0;
    }
</style>


<?php
$planificado = 0;
$en_progreso = 0;
$en_revision = 0;
$completado = 0;

$bajo = 0;
$medio = 0;
$alto = 0;
$urgente = 0;

foreach($okrs_planes_accion as $okrs_plan){
    if($okrs_plan["estado_backlog"] == "1"){
        $planificado++;
    }
    if($okrs_plan["estado_backlog"] == "2"){
        $en_progreso++;
    }
    if($okrs_plan["estado_backlog"] == "3"){
        $en_revision++;
    }
    if($okrs_plan["estado_backlog"] == "4"){
        $completado++;
    }

    if($okrs_plan["prioridad"] == "1"){
        $bajo++;
    }
    if($okrs_plan["prioridad"] == "2"){
        $medio++;
    }
    if($okrs_plan["prioridad"] == "3"){
        $alto++;
    }
    if($okrs_plan["prioridad"] == "4"){
        $urgente++;
    }
}
?>

<div class="card mb-3">
    <div class="card-body">
        <div class="row">

            <div class="col-md-4 col-12 d-flex align-items-center pt-3" style="text-align: center;text-align: -webkit-center;">
                <div class="row circular-chart-component">
                    <div class="easy-pie-chart" data-percent="<?php echo $datos_consolidado_planes["promedio_general"]; ?>">
                        <div class="okr-progress-value">
                            <?php echo $datos_consolidado_planes["promedio_general"]; ?>%
                        </div>
                    </div>
                    <p class="progress-label"> <b>Avance total de Planes de Acción (<?php echo $datos_consolidado_planes["cantidad_planes"]; ?>)</b> </p>
                </div>
            </div>

            <div class="col-md-4 col-12 pt-3 text-center">
                <b>Consolidado Estado Backlog</b>
                <div class="d-flex justify-content-center" style="height:200px;">
                    <canvas id="estadoTareasChart" height="200"></canvas>
                </div>
            </div>

            <div class="col-md-4 col-12 pt-3 text-center">
                <b>Consolidado Prioridad</b>
                <div class="d-flex justify-content-center" style="height:200px;">
                    <canvas id="prioridadChart" height="200"></canvas>
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
            size: 150, // Diámetro del círculo
            lineWidth: 15, // Grosor del anillo
            barColor: '<?php echo $datos_consolidado_planes["color_general"]; ?>', // Color de la parte llena (verde)
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

<!-- GRÁFICAS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script>
    const ctxBacklog = document.getElementById("estadoTareasChart");

    const labelsBacklog = ["Planificado", "En Progreso", "En Revisión", "Completado"];
    const valuesBacklog = [<?= $planificado; ?>, <?= $en_progreso; ?>, <?= $en_revision; ?>, <?= $completado; ?>]; // tus datos reales

    const total = valuesBacklog.reduce((a, b) => a + b, 0);

    new Chart(ctxBacklog, {
        type: "doughnut",
        data: {
            labels: labelsBacklog,
            datasets: [{
                data: valuesBacklog,
                backgroundColor: [
                    "#808080", // planificado
                    "#007bff", // progreso
                    "#ffc107", // revision
                    "#8bc34a" // completado
                ],
                borderWidth: 1
            }]
        },
        options: {
            cutout: "60%",
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let val = context.raw;
                            let pct = total > 0 ? Math.round((val / total) * 100) : 0;
                            return `${context.label}: ${val} (${pct}%)`;
                        }
                    }
                },
                datalabels: {
                    color: "#000",
                    anchor: "end",
                    align: "end",
                    offset: 10,
                    font: {
                        weight: "bold"
                    },
                    formatter: function(value, ctx) {
                        if (value === 0) return ""; // no mostrar valores 0

                        let pct = ((value / total) * 100).toFixed(0);
                        return `${ctx.chart.data.labels[ctxBacklog.dataIndex]} (${pct}%)`;
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
</script>

<script>
    const ctxPrioridad = document.getElementById("prioridadChart");

    const labelsPrioridad = ["Bajo", "Medio", "Alto", "Urgente"];
    const valuesPrioridad = [<?= $bajo; ?>, <?= $medio; ?>, <?= $alto; ?>, <?= $urgente; ?>]; // Tus valores reales

    const totalPrioridad = valuesPrioridad.reduce((a, b) => a + b, 0);

    // Colores por categoría
    const colorsPA = [
        "#808080", // Bajo
        "#27ae60", // Medio
        "#f1c40f", // Alto
        "#e74c3c" // Urgente
    ];

    new Chart(ctxPrioridad, {
        type: 'bar',
        data: {
            labels: labelsPrioridad,
            datasets: [{
                label: "Cantidad por prioridad",
                data: valuesPrioridad,
                backgroundColor: colorsPA,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        generateLabels: function(chart) {
                            const data = chart.data;
                            return data.labels.map((label, i) => {
                                const value = data.datasets[0].data[i];
                                const pct = totalPrioridad > 0 ? Math.round((value / totalPrioridad) * 100) : 0;
                                return {
                                    text: `${label} (${pct}%)`,
                                    fillStyle: colorsPA[i],
                                    strokeStyle: colorsPA[i],
                                    hidden: value === 0,
                                    index: i
                                }
                            });
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                    callbacks: {
                        label: function(context) {
                            const value = context.raw;
                            const pct = totalPrioridad > 0 ? Math.round((value / totalPrioridad) * 100) : 0;
                            return `${context.label}: ${value} (${pct}%)`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: "Cantidad por prioridad"
                    }
                },
                y: {
                    beginAtZero: true,
                    max: Math.max(...valuesPrioridad) + 0.25
                }
            }
        }
    });
</script>