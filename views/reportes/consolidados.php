<script>
    $(document).ready(function() {
        $('#menuReportes').collapse();
        $('#bt_reportes_consolidado').addClass('active');
    });
</script>

<?php
//DATOS DEL USUARIO
include("app/models/okrs/OkrsServicios_2.php");
$ClassOkrs = new OkrsServicios_2();
?>

<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">

    <div class="card mb-3 text-center">
        <div class="card-body">
            <h3>DESEMPEÑO Y AVANCES ESTRATÉGICO</h3>

        </div>
    </div>

    <?php include("views/reportes/layouts/filtros.php"); ?>

    <div class="card mb-3">
        <div class="card-body">
            <?php include("views/reportes/layouts/escala.php"); ?>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header text-center">
            <h3>DESEMPEÑO DE LA COMPAÑIA POR PERIODO</h3>
        </div>
        <div class="card-body">
            <?php $valor = 55; ?>
            <?php include("views/reportes/layouts/objetivos_desempenio.php"); ?>
        </div>
    </div>

    <div class="row">
        <?php include("views/reportes/layouts/estrategicos.php"); ?>
    </div>


    <div class="row" style="display:none" >
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h3>Consolidado Líderes</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>Nombre</th>
                                    <th>Progreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
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

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h3>Consolidado Alta Dirección</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Progreso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h3>Consolidado Áreas</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Progreso</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
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

        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h3>Objetivos</h3>
                </div>
                <div class="card-body">
                    <canvas id="graficaPie" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h3>KRS</h3>
                </div>
                <div class="card-body">
                    <canvas id="graficaBarras" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h3>Iniciativas</h3>
                </div>
                <div class="card-body">
                    <canvas id="graficaBarrasHorizontales" height="300"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card mb-3">
                <div class="card-header">
                    <h3>Planes de Acción</h3>
                </div>
                <div class="card-body">
                    <canvas id="graficaRosca" height="300"></canvas>
                </div>
            </div>
        </div>

    </div>

    <div class="card mb-3">
        <div class="card-body">
            <?php include("views/reportes/layouts/escala.php"); ?>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- GRAFICA OBJETIVOS -->
<script>
    const ctx = document.getElementById('graficaPie');

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Completado', 'En proceso', 'Pendiente'],
            datasets: [{
                data: [55, 30, 15],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Progreso Objetivos',
                    font: {
                        size: 18,
                        weight: 'bold'
                    }
                },
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx.label}: ${ctx.raw}%`
                    }
                }
            }
        }
    });
</script>

<!-- GRAFICA KRS -->
<script>
    const ctx_krs = document.getElementById('graficaBarras');

    new Chart(ctx_krs, {
        type: 'bar',
        data: {
            labels: ['Q1', 'Q2', 'Q3', 'Q4'],
            datasets: [{
                label: 'Ventas',
                data: [120, 90, 150, 70],
                backgroundColor: [
                    '#2d8cff',
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Progreso KRS',
                    font: {
                        size: 18,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx_krs.raw
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: ''
                    }
                }
            }
        }
    });
</script>

<!-- GRAFICA INICIATIVAS -->
<script>
    const ctx_iniciativas = document.getElementById('graficaBarrasHorizontales');

    new Chart(ctx_iniciativas, {
        type: 'bar',
        data: {
            labels: [
                'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
            ],
            datasets: [{
                label: 'Ingresos',
                data: [12, 19, 8, 15, 22, 17, 10, 14, 18, 20, 9, 16],
                backgroundColor: '#2d8cff',
                borderRadius: 6
            }]
        },
        options: {
            indexAxis: 'y', // 👈 Esto las hace horizontales
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Ingresos mensuales',
                    font: {
                        size: 18,
                        weight: 'bold'
                    }
                },
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Cantidad'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Mes'
                    }
                }
            }
        }
    });
</script>

<!-- GRAFICA PLANES DE ACCION -->
<script>
    const ctx_planes_accion = document.getElementById('graficaRosca');

    new Chart(ctx_planes_accion, {
        type: 'doughnut',
        data: {
            labels: ['Planificado Completado', 'En Progreso', 'En Revisión'],
            datasets: [{
                data: [60, 25, 15],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',
            plugins: {
                title: {
                    display: true,
                    text: 'Consolidado Estado Backlog',
                    font: {
                        size: 18,
                        weight: 'bold'
                    }
                },
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: ctx => `${ctx_planes_accion.label}: ${ctx_planes_accion.raw}%`
                    }
                }
            }
        }
    });
</script>