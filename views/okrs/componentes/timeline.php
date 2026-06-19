<?php
//SOLO PARA POPUPS
if (isset($iniciativa['id'])) {
    $okrs_planes_accion = $ClassOkrsServicios->okrs_obtener_plan_accion_iniciativa($iniciativa['id']);
}
?>

<?php
// Construcción del arreglo de tareas
$tareas = [];

foreach ($okrs_planes_accion as $plan_de_accion) {

    $backlog_color = [
        "1" => "#6c757d",
        "2" => "#007bff",
        "3" => "#ffc107",
        "4" => "#198754"
    ];

    $tareas[] = [
        "descripcion_iniciativa" => $plan_de_accion["descripcion_iniciativa"],
        "nombre" => $plan_de_accion["descripcion"],
        "inicio" => $plan_de_accion["fecha_inicia"],
        "fin"    => $plan_de_accion["fecha_entrega"],
        "backlog_color" => $backlog_color[$plan_de_accion["estado_backlog"]],
        "porcentaje_avance" => $plan_de_accion["porcentaje_avance"],
        "bg_color" => $plan_de_accion["bg_color"]
    ];
}

// Arrays para Chart.js
$labels = [];
$starts = [];
$durations = [];
$porcentajes = [];
$colores = [];
$descripciones = [];

foreach ($tareas as $t) {
    $labels[]         = $t["nombre"];
    $starts[]         = $t["inicio"];
    $durations[]      = (strtotime($t["fin"]) - strtotime($t["inicio"])) / 86400;
    $porcentajes[]    = number_format($t["porcentaje_avance"], 0);;
    $colores[]        = $t["backlog_color"];
    $descripciones[]  = $t["descripcion_iniciativa"];
}

// Calcular fecha mínima y máxima
$minFecha = null;
$maxFecha = null;

foreach ($tareas as $t) {
    $inicio = strtotime($t["inicio"]);
    $fin    = strtotime($t["fin"]);
    if ($minFecha === null || $inicio < $minFecha) $minFecha = $inicio;
    if ($maxFecha === null || $fin > $maxFecha) $maxFecha = $fin;
}

// Total de días y ancho dinámico
$dias_totales = ($maxFecha - $minFecha) / 86400;
$ancho_px = max(900, $dias_totales * 3);
$alturaCanvas = 80 * count($tareas);
?>

<div class="container">
    <div style="width: 100%; overflow-x: auto;">
        <div style="width: <?php echo $ancho_px; ?>px; padding-bottom: 40px;">
            <canvas id="timelineChart<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>" width="<?php echo $ancho_px; ?>" height="<?php echo $alturaCanvas + 100; ?>" style="min-width:100%;"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const labels<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?> = <?php echo json_encode($labels); ?>;
    const startDates<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?> = <?php echo json_encode($starts); ?>;
    const durations<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?> = <?php echo json_encode($durations); ?>;
    const descripciones<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?> = <?php echo json_encode($descripciones); ?>;
    const porcentajes<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?> = <?php echo json_encode($porcentajes); ?>;
    const colores<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?> = <?php echo json_encode($colores); ?>;
    const minFecha<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?> = <?php echo $minFecha * 1000; ?>;

    // Convertir fechas a offset en días
    const startTimestamps<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?> = startDates<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>.map(d => new Date(d).getTime());
    const offsets<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?> = startTimestamps<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>.map(ts => (ts - minFecha<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>) / 86400000);
    const tooltipEl<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?> = document.getElementById('tooltip-container');
    const ctx<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?> = document.getElementById('timelineChart<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>').getContext('2d');

    //HOY
    const hoy = new Date().getTime();
    const hoyOffset = (hoy - minFecha<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>) / 86400000;
    const lineaHoyPlugin = {
        id: 'lineaHoy',
        afterDraw: (chart) => {
            const {
                ctx,
                chartArea,
                scales
            } = chart;
            if (!chartArea) return;

            const xScale = scales.x;
            const x = xScale.getPixelForValue(hoyOffset);

            // 🔒 Ajustar dentro del área visible
            let xClamped = x;
            let label = "HOY";

            if (x < chartArea.left) {
                xClamped = chartArea.left;
                label = "HOY ←";
            }

            if (x > chartArea.right) {
                xClamped = chartArea.right;
                label = "HOY →";
            }

            ctx.save();

            // 🔴 Línea vertical
            ctx.beginPath();
            ctx.moveTo(xClamped, chartArea.top);
            ctx.lineTo(xClamped, chartArea.bottom);
            ctx.lineWidth = 2;
            ctx.strokeStyle = 'red';
            ctx.setLineDash([6, 4]);
            ctx.stroke();

            // 🏷️ Texto
            ctx.fillStyle = 'red';
            ctx.font = '12px Arial';
            ctx.fillText(label, xClamped + 5, chartArea.top + 12);

            ctx.restore();
        }
    };

    new Chart(ctx<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>, {

        type: 'bar',
        data: {
            labels: labels<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>,
            datasets: [{
                    label: '',
                    data: offsets<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>,
                    backgroundColor: 'rgba(0,0,0,0)',
                    stack: 'stack0',
                    // 👇 tooltip solo activo aquí
                    tooltip: {
                        enabled: true
                    }
                },
                {
                    label: 'Duración',
                    data: durations<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>,
                    backgroundColor: function(context) {
                        return colores<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>[context.dataIndex] || '#f1c40f';
                    },
                    stack: 'stack0',
                    barThickness: 20,
                    maxBarThickness: 20,
                    categoryPercentage: 0.5,
                    barPercentage: 0.5,
                    // 👇 desactivar tooltip en esta barra
                    tooltip: {
                        enabled: false
                    }
                }
            ]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    position: 'nearest', // 🟢 importante: evita que se vaya al final
                    callbacks: {
                        title: function(ctx) {
                            return labels<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>[ctx[0].dataIndex];
                        },
                        label: function(ctx) {
                            const index = ctx.dataIndex;
                            const inicio = startDates<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>[index];
                            const dias = durations<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>[index];
                            const avance = porcentajes<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>[index];
                            const descripcion = descripciones<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>[index];
                            const finDate = new Date(new Date(inicio).getTime() + dias * 86400000).toISOString().slice(0, 10);
                            return [
                                "📌 " + descripcion.slice(0, 100) + '...',
                                "📅 Inicio: " + inicio,
                                "🏁 Fin: " + finDate,
                                "⏳ Duración: " + dias + " días",
                                "📊 Avance: " + avance + "%"
                            ];
                        }
                    },
                    yAlign: 'center', // centra verticalmente el tooltip sobre la barra
                    xAlign: 'center', // centra horizontalmente
                    caretPadding: 10,
                    displayColors: false // opcional: oculta los cuadritos de color
                }
            },
            scales: {
                x: {
                    stacked: true,
                    type: 'linear',
                    ticks: {
                        stepSize: 30, // cada tick ~1 mes
                        callback: function(value) {
                            const fecha = new Date(minFecha<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?> + value * 86400000);
                            return fecha.toISOString().slice(0, 7); // YYYY-MM
                        }
                    }
                },
                y: {
                    stacked: true,
                    ticks: {
                        callback: function(value, index) {
                            const label = this.getLabelForValue(value);
                            const maxLength = 50;
                            const texto = label.length > maxLength ? label.slice(0, maxLength) + "..." : label;
                            const avance = porcentajes<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>[index] !== undefined ? porcentajes<?= isset($iniciativa['id']) ? '_' . $iniciativa['id'] : '' ?>[index] : "";
                            return `${texto} (${avance}%)`;
                        }
                    }
                }
            }
        },
        plugins: [lineaHoyPlugin]
    });
</script>