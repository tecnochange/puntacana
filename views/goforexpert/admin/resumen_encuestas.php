<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
    $("#bt_academia_tablero").addClass("active_item");
    $("#academia_menu").addClass("active");
    $('#academia_menu .collapse').collapse();
    
    $('#academia_menu').show();
</script>

<?php 

    $year = date("Y");

    // Construir el filtro SQL
    $filtro = '';
    if (!empty($_POST["fill_proceso"])) {
        $id_proceso = mysqli_real_escape_string($connect_academia, $_POST["fill_proceso"]);
        $filtro = "WHERE id_curso = '$id_proceso' AND YEAR(created_at) = '$year'";
    } else {
        $filtro = "WHERE YEAR(created_at) = '$year'";
    }

    // Debugging: Mostrar la consulta SQL generada
    //echo "Filtro SQL: $filtro<br>";

    // Consulta con el filtro
    $queryEncuesta = mysqli_query($connect_academia, "SELECT * FROM Encuesta_Curso $filtro");
    $dataEncuesta = mysqli_fetch_all($queryEncuesta, MYSQLI_ASSOC);

    // Inicialización de arreglos para las encuestas
    $metodologia = array(0, 0, 0, 0, 0);
    $aprendizaje = array(0, 0, 0, 0, 0);
    $tiempo = array(0, 0, 0, 0, 0);
    $contenidos = array(0, 0, 0, 0, 0);
    $concentrado = array(0, 0, 0, 0, 0);
    $interes = array(0, 0, 0, 0, 0);
    $uso_tiempo = array(0, 0, 0, 0, 0);

    // Contar respuestas
    foreach ($dataEncuesta as $encuesta) {
        $metodologia[$encuesta['metodologia']-1]++;
        $aprendizaje[$encuesta['aprendizaje']-1]++;
        $tiempo[$encuesta['tiempo']-1]++;
        $contenidos[$encuesta['contenidos']-1]++;
        $concentrado[$encuesta['concentrado']-1]++;
        $interes[$encuesta['interes']-1]++;
        $uso_tiempo[$encuesta['uso_tiempo']-1]++;
    }

    // Función para calificación
    function calificacion($calificacion) {
        switch ($calificacion) {
            case 1:
                return array("Muy Malo", "#fc0400");
            case 2:
                return array("Malo", "yellow");
            case 3:
                return array("Regular", "#92E922");
            case 4:
                return array("Bueno", "#00AAEF");
            case 5:
                return array("Excelente", "#012060");
        }
    }

    // Sumar totales
    $metodologiaTotal = array_sum($metodologia);
    $aprendizajeTotal = array_sum($aprendizaje);
    $tiempoTotal = array_sum($tiempo);
    $contenidosTotal = array_sum($contenidos);
    $concentradoTotal = array_sum($concentrado);
    $interesTotal = array_sum($interes);
    $uso_tiempoTotal = array_sum($uso_tiempo);
?>
<div class="container">
	<div align="left" class="cabecera_interna">
		<table width="100%">
			<tr>
				<td><h2>Encuesta de Satisfacción</h2></td>
			</tr>
		</table>
	</div>
</div>

<div class="container">
	<div class="card">
        <form action="" method="post">
            <div class="row" style="margin: 20px 10px">
                <div class="col-md-3" style="margin-bottom: 10px">
                    <select class="form-control" name="fill_proceso">
                        <option value="">Capacitación...</option>
                        <?php
                            $queryDep = mysqli_query($connect_academia, "SELECT * FROM Cursos ORDER BY nombre ASC");  
                            while ($dataDep = mysqli_fetch_array($queryDep)) {
                                $selected = ($_POST["fill_proceso"] == $dataDep["id"]) ? 'selected' : '';
                                echo '<option value="' . $dataDep["id"] . '" ' . $selected . '>' . $dataDep["nombre"] . '</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="col-md-1" style="margin-bottom: 10px">
                    
                        <button type="submit" class="btn btn-success btn-sm btn-block">Filtrar</button>
                   
                </div>
            </div>
        </form>
        
        <?php if ($queryEncuesta->num_rows == 0) { ?>
            <div class="card-body">  
                <div class="col-md-12 text-center">
                    <h2>No se encontraron datos</h2>
                </div>
            </div>
        <?php } else { ?>
            <div class="card-body"> 
                <div class="row">
                    <div class="col-md-4">
                        <figure class="highcharts-figure">
                            <div id="metodologia"></div>
                        </figure>
                    </div>
                    <div class="col-md-4">
                        <figure class="highcharts-figure">
                            <div id="aprendizaje"></div>
                        </figure>
                    </div>
                    <div class="col-md-4">
                        <figure class="highcharts-figure">
                            <div id="tiempo"></div>
                        </figure>
                    </div>
                    <div class="col-md-4">
                        <figure class="highcharts-figure">
                            <div id="contenidos"></div>
                        </figure>
                    </div>
                    <div class="col-md-4">
                        <figure class="highcharts-figure">
                            <div id="concentrado"></div>
                        </figure>
                    </div>
                    <div class="col-md-4">
                        <figure class="highcharts-figure">
                            <div id="interes"></div>
                        </figure>
                    </div>
                    <div class="col-md-4">
                        <figure class="highcharts-figure">
                            <div id="uso_tiempo"></div>
                        </figure>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <div class="card">
                    <div class="card-body">
                        <table class="table" style="font-size: 13px" id="tabla_maestra">
                            <thead class="thead-success">
                                <tr>
                                    <th scope="col" width="15">#</th>
                                    <th scope="col">Colaborador</th>
                                    <th scope="col">Cargo</th>
                                    <th scope="col">Retroalimentación</th>
                                </tr>
                            </thead>
                            <tbody class="tabla_lista">
                                <?php 
                                    $count = 1;
                                    $query = mysqli_query($connect_academia, "SELECT * FROM Encuesta_Curso $filtro");  
                                    while ($data = mysqli_fetch_array($query)) { 
                                        $queryColaborador = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '".$data["id_empleado"]."' "); 
                                        $dataColaborador = mysqli_fetch_array($queryColaborador);
                                        
                                        $queryCargo = mysqli_query($connect_valentina, "SELECT car.nombre FROM Posiciones as pos
                                        LEFT JOIN Cargos as car
                                        ON car.id = pos.id_cargo
                                        WHERE pos.id = '".$dataColaborador["id_posicion"]."' "); 

                                        $dataCargo = mysqli_fetch_array($queryCargo);

                                        echo '<tr>';
                                        echo '<td>'.$count.'</td>';
                                        echo '<td>'.$dataColaborador["nombre"].' </td>';
                                        echo '<td>'.$dataColaborador["cargo"].'</td>';
                                        echo '<td>'.$data["feedback"].'</td>';
                                        echo '</tr>';
                                        $count++;
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?>
	</div>
</div>

<?php

//print_r($tiempo);
?>

<script>
    $(document).ready(function () {
		<?php  if($queryEncuesta->num_rows != 0){ ?>
			$('#tabla_maestra').DataTable({
				"language": {
					"url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
				},
				"pageLength": 50 // Mostrar 50 elementos por página
			});
		<?php } ?>
    });
    Highcharts.chart('metodologia', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'La metodología utilizada facilito el aprendizaje',
            align: 'left'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            name: 'Brands',
            colorByPoint: true,
            data: [
                <?php 
                    foreach($metodologia as $key => $meto){
                        $calificacion = calificacion($key+1)[0];
                        $color = calificacion($key+1)[1];
                        echo '{name: "'.$calificacion.'",';
                        echo "y: ".($meto/$metodologiaTotal)*100 . ",";
                        echo "color: '".$color."'},";
                    }
                ?>]
        }]
    });

    Highcharts.chart('aprendizaje', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Los aprendizajes se pueden llevar fácilmente a la practica',
            align: 'left'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            name: 'Brands',
            colorByPoint: true,
            data: [
                <?php 
                    foreach($aprendizaje as $key => $meto){
                        $calificacion = calificacion($key+1)[0];
                        $color = calificacion($key+1)[1];
                        echo '{name: "'.$calificacion.'",';
                        echo "y: ".($meto/$aprendizajeTotal)*100 . ",";
                        echo "color: '".$color."'},";
                    }
                ?>]
        }]
    });

    Highcharts.chart('tiempo', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'El tiempo programado fue suficiente para el desarrollo de la capacitación',
            align: 'left'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            name: 'Brands',
            colorByPoint: true,
            data: [
                <?php 
                    foreach($tiempo as $key => $meto){
                        $calificacion = calificacion($key+1)[0];
                        $color = calificacion($key+1)[1];
                        echo '{name: "'.$calificacion.'",';
                        echo "y: ".($meto/$tiempoTotal)*100 . ",";
                        echo "color: '".$color."'},";
                    }
                ?>]
        }]
    });

    Highcharts.chart('contenidos', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Los contenidos fueron adecuados para el tema',
            align: 'left'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            name: 'Brands',
            colorByPoint: true,
            data: [
                <?php 
                    foreach($contenidos as $key => $meto){
                        $calificacion = calificacion($key+1)[0];
                        $color = calificacion($key+1)[1];
                        echo '{name: "'.$calificacion.'",';
                        echo "y: ".($meto/$contenidosTotal)*100 . ",";
                        echo "color: '".$color."'},";
                    }
                ?>]
        }]
    });

    Highcharts.chart('concentrado', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Me mantuve concentrado',
            align: 'left'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            name: 'Brands',
            colorByPoint: true,
            data: [
                <?php 
                    foreach($concentrado as $key => $meto){
                        $calificacion = calificacion($key+1)[0];
                        $color = calificacion($key+1)[1];
                        echo '{name: "'.$calificacion.'",';
                        echo "y: ".($meto/$concentradoTotal)*100 . ",";
                        echo "color: '".$color."'},";
                    }
                ?>]
        }]
    });

    Highcharts.chart('interes', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'La temática si fue de mi interés',
            align: 'left'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            name: 'Brands',
            colorByPoint: true,
            data: [
                <?php 
                    foreach($interes as $key => $meto){
                        $calificacion = calificacion($key+1)[0];
                        $color = calificacion($key+1)[1];
                        echo '{name: "'.$calificacion.'",';
                        echo "y: ".($meto/$interesTotal)*100 . ",";
                        echo "color: '".$color."'},";
                    }
                ?>]
        }]
    });

    Highcharts.chart('uso_tiempo', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Hice buen eso del tiempo',
            align: 'left'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: false
                },
                showInLegend: true
            }
        },
        series: [{
            name: 'Brands',
            colorByPoint: true,
            data: [
                <?php 
                    foreach($uso_tiempo as $key => $meto){
                        $calificacion = calificacion($key+1)[0];
                        $color = calificacion($key+1)[1];
                        echo '{name: "'.$calificacion.'",';
                        echo "y: ".($meto/$uso_tiempoTotal)*100 . ",";
                        echo "color: '".$color."'},";
                    }
                ?>]
        }]
    });




</script>