<?php
/*
"promedio_general" => 0, 
            "cantidad_okrs" => count($okrs),
            "cantidad_1" => $aplica_1, 
            "cantidad_2" => $aplica_2,
            "cantidad_3" => $aplica_3,
            "cantidad_4" => $aplica_4, 
            "cantidad_5" => $aplica_4, 
            "color_1" => "#FF0000", 
            "color_2" => "#FFF200", 
            "color_3" => "#95FA03", 
            "color_4" => "#14F209", 
            "color_5" => "#00D30A", 
            */
?>

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

    .okr-progress-value{
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

    .small-box p{
        margin-bottom: 0;
    }
</style>

<div class="card mb-3">
    <div class="card-body">
        <div class="row">

            <div class="col-md-3 col-12 pt-3" style="text-align: center;text-align: -webkit-center;">
                <div class="row circular-chart-component">
                    <div class="easy-pie-chart" data-percent="<?php echo $datos_consolidado["promedio_general"]; ?>">
                        <div class="okr-progress-value">
                            <?php echo $datos_consolidado["promedio_general"]; ?>%
                        </div>
                    </div>
                    <p class="progress-label"> <b>Avance total de OKR's</b> </p>
                </div>
            </div>

            <div class="col-md-8 pt-2">
                <h3 class="mt-4 mb-2 text-center">Consolidado de progreso de los <?= count($okrs_objetivos_equipo); ?> OKR's Asignados</h3>
                <div class="row">
                    <div class="flex-container">
                        <div class="small-box bg-danger" style="background-color: <?php echo $datos_consolidado["color_1"]; ?> !important; flex: 50%;">
                            <div class="inner">
                                <h3 class="mb-2" style="color: white !important;"><?php echo round($datos_consolidado["porcentaje_1"],2); ?>%</h3>
                                <p style="color: white !important;">
                                    <?php echo $datos_consolidado["titulo_1"]; ?> <br>
                                    <b><?php echo $datos_consolidado["cantidad_1"]; ?> OKR's</b>
                                </p>
                            </div>
                        </div>
                        <div class="small-box bg-warning" style="background-color: <?php echo $datos_consolidado["color_2"]; ?> !important; flex: 50%;">
                            <div class="inner">
                                <h3 class="mb-2"><?php echo round($datos_consolidado["porcentaje_2"],2); ?>%</h3>
                                <p style="color: #6b21ff !important;">
                                    <?php echo $datos_consolidado["titulo_2"]; ?> <br>
                                    <b><?php echo $datos_consolidado["cantidad_2"]; ?> OKR's</b>
                                </p>
                            </div>
                        </div>
                        <div class="small-box bg-success" style="background-color: <?php echo $datos_consolidado["color_3"]; ?> !important; flex: 50%;">
                            <div class="inner">
                                <h3 class="mb-2"><?php echo round($datos_consolidado["porcentaje_3"],2); ?>%</h3>
                                <p style="color: #6b21ff !important;">
                                    <?php echo $datos_consolidado["titulo_3"]; ?> <br>
                                    <b><?php echo $datos_consolidado["cantidad_3"]; ?> OKR's</b>
                                </p>
                            </div>
                        </div>
                        <div class="small-box bg-success" style="background-color: <?php echo $datos_consolidado["color_4"]; ?> !important; flex: 50%;">
                            <div class="inner">
                                <h3 class="mb-2"><?php echo round($datos_consolidado["porcentaje_4"],2); ?>%</h3>
                                <p style="color: #6b21ff !important;">
                                    <?php echo $datos_consolidado["titulo_4"]; ?> <br>
                                    <b><?php echo $datos_consolidado["cantidad_4"]; ?> OKR's</b>
                                </p>
                            </div>
                        </div>
                        <div class="small-box bg-success" style="background-color: <?php echo $datos_consolidado["color_5"]; ?> !important; flex: 50%;">
                            <div class="inner">
                                <h3 class="mb-2"><?php echo round($datos_consolidado["porcentaje_5"],2); ?>%</h3>
                                <p style="color: #6b21ff !important;">
                                    <?php echo $datos_consolidado["titulo_5"]; ?> <br>
                                    <b><?php echo $datos_consolidado["cantidad_5"]; ?> OKR's</b>
                                </p>
                            </div>
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
            size: 150, // Diámetro del círculo
            lineWidth: 15, // Grosor del anillo
            barColor: '<?php echo $datos_consolidado["color_general"]; ?>', // Color de la parte llena (verde)
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