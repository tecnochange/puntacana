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

<div class="card mb-3">
    <div class="card-body">
        <div class="row">

            <div class="col-md-4 col-12 d-flex align-items-center pt-3" style="text-align: center;text-align: -webkit-center;">
                <div class="row circular-chart-component">
                    <div class="easy-pie-chart" data-percent="<?php echo $datos_consolidado_kpis["promedio_general"]; ?>">
                        <div class="okr-progress-value"></div>
                    </div>
                    <p class="progress-label"> <b>Avance total de los Kpis </b> </p>
                </div>
            </div>

            <div class="col-md-8 col-12 pt-3 text-center">
                <b>Consolidado de progreso por Kpis (<?php echo $datos_consolidado_kpis["cantidad_kpis"]; ?>)</b>
                <?php 
                // Ejemplo de datos desde PHP - reemplaza por tus consultas
                $mediciones = [
                    [$datos_consolidado_kpis["titulo_1"] , $datos_consolidado_kpis["cantidad_1"], $datos_consolidado_kpis["color_1"] ],
                    [$datos_consolidado_kpis["titulo_2"] , $datos_consolidado_kpis["cantidad_2"], $datos_consolidado_kpis["color_2"] ],
                    [$datos_consolidado_kpis["titulo_3"] , $datos_consolidado_kpis["cantidad_3"], $datos_consolidado_kpis["color_3"] ],
                    [$datos_consolidado_kpis["titulo_4"] , $datos_consolidado_kpis["cantidad_4"], $datos_consolidado_kpis["color_4"] ],
                    [$datos_consolidado_kpis["titulo_5"] , $datos_consolidado_kpis["cantidad_5"], $datos_consolidado_kpis["color_5"]]
                ];

                // Extraer arrays para JS
                $labels = array_column($mediciones, 0);
                $values = array_column($mediciones, 1);
                $colors = array_column($mediciones, 2);
                ?>
                <div style="width:100%; margin:0 auto;">
                    <canvas id="graficaHorizontal"></canvas>
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
            barColor: '<?php echo $datos_consolidado_kpis["color_general"]; ?>', // Color de la parte llena (verde)
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Plugin para mostrar valores sobre las barras (opcional pero recomendado) -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>
const labels = <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>;
const values = <?= json_encode($values) ?>;
const colors = <?= json_encode($colors) ?>;

const ctx = document.getElementById('graficaHorizontal').getContext('2d');

const chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels, // nombres (se colocan técnicamente en eje Y con indexAxis:'y')
        datasets: [{
            label: 'Cantidad',
            data: values,
            backgroundColor: colors,
            borderRadius: 12,      // bordes redondeados (Chart.js 3+)
            barThickness: 24
        }]
    },
    options: {
        indexAxis: 'y', // <-- hace la gráfica HORIZONTAL
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            datalabels: { // plugin para mostrar valor al final de la barra
                anchor: 'end',
                align: 'end',
                formatter: function(value) { return value; },
                font: { weight: 'bold' },
                color: '#000'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.x;
                    }
                }
            }
        },
        scales: {
            // Eje X (técnicamente valores numéricos para barras horizontales)
            /* x: {
                beginAtZero: true,
                // Aquí ponemos el título: "Nombres"
                title: {
                    display: true,
                    text: 'Nombres',
                    font: { size: 12, weight: 'bold' }
                },
                ticks: {
                    precision: 0
                }
            }, */

            // Eje Y (técnicamente categorías)
            y: {
                title: {
                    display: true,
                    text: 'Cantidad Iniciativas',
                    font: { size: 12, weight: 'bold' }
                },
                ticks: {
                    autoSkip: false
                }
            }
        }
    },
    plugins: [ChartDataLabels]
});
</script>

<!-- Ajuste de altura para que se vea bien -->
<style>
#graficaHorizontal {
    height: 250px !important;
}
</style>