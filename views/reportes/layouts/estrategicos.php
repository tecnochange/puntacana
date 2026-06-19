<?php
$queryEstrategicos = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos 
WHERE id_empresa = " . $user_log["id_empresa"] . " AND anio = " . $_SESSION["anio_fill"] . "");

$count = 0;
while ($dataEstrategicos = mysqli_fetch_array($queryEstrategicos)) {

    $count++;

    $total_obj = 0;
    $count_total_obj = 0;
    $objetivos = $ClassOkrs->okrs_objetivos_estrategicos($user_log["id_empresa"], $dataEstrategicos["id"], $_SESSION["anio_fill"]);
    foreach ($objetivos as $avance) {
        $total_obj  += $avance["avance"];
        $count_total_obj++;
    }

    $total_avance = round($total_obj / $count_total_obj);
    $bg_color = EscalaColor($total_avance);

    //print_r($objetivos);

?>

    <div class="col-md-4">
        <div class="card mb-3">
            <div class="card-header">
                <h3 style="font-size: 1em;"><?php echo $dataEstrategicos["objetivo"]; ?></h3>
            </div>
            <div class="card-body">

                <table style="width: 100%;">
                    <tr>
                        <td style="font-size: 2rem;"><b><?php echo $total_avance; ?>%</b></td>
                        <td style="width: 300px;">
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $total_avance; ?>%; background-color: <?php echo $bg_color; ?>;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </td>
                    </tr>
                </table>

                <canvas id="avancePeriodo_<?=  $count; ?>" height="160"></canvas>

            </div>
        </div>

        <div class="card mb-3">
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Objetivo</th>
                        <th>Avance</th>
                    </tr>
                    <?php
                    $queryOkrs = mysqli_query($connect_okrs, "SELECT * FROM Okrs 
                    WHERE id_empresa = " . $user_log["id_empresa"] . " AND anio = " . $_SESSION["anio_fill"] . " AND objetivos_estrategicos =  '" . $dataEstrategicos["id"] . "' LIMIT 5 ");
                    while ($dataOkrs = mysqli_fetch_array($queryOkrs)) {

                        $avance = $ClassOkrs->resultados_okrs($dataOkrs["id"]);
                        $bg_color_avance = EscalaColor($avance);

                        echo '
                        <tr>
                            <td>' . $dataOkrs["objetivo_okr"] . '</td>
                            <td class="text-center">
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: ' . $avance . '%; background-color: '.$bg_color_avance.';" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                ' . $avance . '%
                            </td>
                        </tr>
                        ';
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>

<?php } ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    let cantidad_graficas = "<?php echo mysqli_num_rows($queryEstrategicos); ?>";

    for (i = 1; i <= cantidad_graficas; i++) {

        let ctx = document.getElementById('avancePeriodo_'+i);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['','Q1', 'Q2', 'Q3', 'Q4', 'Anual', ''],
                datasets: [{
                    label: 'Avance',
                    data: [null,18.62, 12.06, 7.36, 2.82, 0, null],
                    borderColor: '#2d8cff',
                    backgroundColor: '#2d8cff',
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Avance por periodo',
                        font: {
                            size: 18,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
                    legend: {
                        position: 'bottom'
                    },
                    datalabels: {
                        align: 'top',
                        anchor: 'end',
                        font: {
                            weight: 'light'
                        },
                        formatter: value => value
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Progreso'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Periodo'
                        }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });

    }
</script>