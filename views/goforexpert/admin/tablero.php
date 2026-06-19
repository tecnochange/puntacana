
<script>
    $("#bt_academia_tablero").addClass("active_item");
    $("#academia_menu").addClass("active");
    $('#academia_menu .collapse').collapse();
    
    $('#academia_menu').show();
</script>

<?php
    $queryEmpleado = mysqli_query($connect_admin,"SELECT * FROM Colaboradores WHERE estado = 1 "); 
	$queryInactivos = mysqli_query($connect_admin,"SELECT * FROM Colaboradores WHERE estado = 2 "); 
	
    $queryProgramas = mysqli_query($connect_academia,"SELECT * FROM Programas ");
    $queryCursos = mysqli_query($connect_academia,"SELECT * FROM Cursos ");
    $queryEstudiantes = mysqli_query($connect_academia,"SELECT * FROM Estudiantes_Cursos WHERE estado = 1 ");

    $anio_actual = date("Y");
    $arrayFechas = array();
    $arraycantidad = array();

	//EDADES
	$queryFechasNace = mysqli_query($connect_admin,"SELECT YEAR(fecha_nacimiento) 
	FROM Empleados_Adicionales 
	LEFT JOIN Colaboradores ON Colaboradores.id = Empleados_Adicionales.id_empleado 
	WHERE Colaboradores.estado = 1 
	ORDER BY fecha_nacimiento DESC  ");
    while($dataSol = mysqli_fetch_array($queryFechasNace)){
        array_push($arrayFechas, ($anio_actual-$dataSol["YEAR(fecha_nacimiento)"]) );
        array_push($arraycantidad, $dataSol["COUNT(id)"] );
    }

	$arrayFechasHijos = array();
    $arraycantidadHijos = array();

	//EDADES HIJOS
	$queryFechasNaceHijos = mysqli_query($connect_admin,"SELECT YEAR(fecha_nace) 
	FROM Empleados_Familiares 
	LEFT JOIN Colaboradores ON Colaboradores.id = Empleados_Familiares.id_empleado 
	WHERE Colaboradores.estado = 1 AND Empleados_Familiares.parentezco = 'Hijo'  
	ORDER BY fecha_nace DESC  ");
    while($dataFechasNaceHijos = mysqli_fetch_array($queryFechasNaceHijos)){
        array_push($arrayFechasHijos, ($anio_actual-$dataFechasNaceHijos["YEAR(fecha_nace)"]) );
        //array_push($arraycantidadHijos, $dataFechasNaceHijos["COUNT(id)"] );
    }

	//print_r($arraycantidadHijos);

    //basicos
    $array_basicos = array();
    $array_tipo_contrato = array();


    $queryBasico = mysqli_query($connect_admin,"SELECT * FROM Colaboradores WHERE estado = 1  ");
    while($dataBasico = mysqli_fetch_array($queryBasico)){
        array_push($array_basicos, $dataBasico );
        array_push($array_tipo_contrato, $dataBasico["tipo_contrato"] );
    }
    $array_tipo_contrato = array_unique($array_tipo_contrato);

    //adicionales
    $array_adicionales = array();
    $queryAdicionales = mysqli_query($connect_admin,"SELECT * FROM Empleados_Adicionales 
	LEFT JOIN Colaboradores ON Colaboradores.id = Empleados_Adicionales.id_empleado 
	WHERE Colaboradores.estado = 1 
	");
    while($dataAdicionales = mysqli_fetch_array($queryAdicionales)){
        array_push($array_adicionales, $dataAdicionales );
    }

	$colores = array("#00bcd4", "#673ab7", "#cddc39", "#607d8b", "#e91e63", "#ff9800", "#ffeb3b", "#3f51b5");
	//ESTA FUNCION DEVUELVE LA INFORMACION ORDENADA DE LOS DATOS: LISTO PARA USAR EN CHARTJS
    function Obtener_Datos_Edad($array_lista, $campo){
        global $arrayFechas;
        global $colores;
        
        $label = "[";
        $data = "[";
        $color = "[";
        $n = 0;
        foreach($array_lista as $edades){
            $rangos = explode(",", $edades[2]);
			
            $count = 0;
            foreach($arrayFechas as $edad){
                if($edad >= $rangos[0] && $edad  <= $rangos[1]  ){
                   $count++; 
                }
            }
            $label .= "'".$edades[1]."',";
            $data .= "'".$count."',";
            $color .= "'".$colores[$n]."',";
            $n++;
        }
        $label .= "]";
        $data .= "]";
        $color .= "]";
        
        return array(
            "labels" => $label, 
            "datos" => $data, 
            "color" => $color
        );
    }

	//ESTA FUNCION DEVUELVE LA INFORMACION ORDENADA DE LOS DATOS: LISTO PARA USAR EN CHARTJS
    function Obtener_Datos_Edad_Hijo($array_lista, $campo){
        global $arrayFechasHijos;
        global $colores;
        
        $label = "[";
        $data = "[";
        $color = "[";
        $n = 0;
		
		for ($i = 0; $i <= 34; $i++) {

            $count = 0;
            foreach($arrayFechasHijos as $edad){
                if($edad == $i ){
                   $count++; 
                }
            }
            $label .= "'".$i." años',";
            $data .= "'".$count."',";
            $color .= "'".$colores[$n]."',";
            $n++;
        
		}
		/*
        foreach($array_lista as $edades){
            $rangos = explode(",", $edades[2]);
			
            $count = 0;
            foreach($arrayFechas as $edad){
                if($edad >= $rangos[0] && $edad  <= $rangos[1]  ){
                   $count++; 
                }
            }
            $label .= "'".$edades[1]."',";
            $data .= "'".$count."',";
            $color .= "'".$colores[$n]."',";
            $n++;
        }
		*/
		
		
        $label .= "]";
        $data .= "]";
        $color .= "]";
        
        return array(
            "labels" => $label, 
            "datos" => $data, 
            "color" => $color
        );
    }

    // Obtener datos de la base de datos
    // Inicializar arrays para almacenar los datos de los cursos y la cantidad de estudiantes
    $datos_curso = array();
    $datos_estudiantes = array();

    // Consulta para obtener los cursos y contar la cantidad de estudiantes por curso
    $query = mysqli_query($connect_academia, "SELECT Cursos.nombre, COUNT(Estudiantes_Cursos.id_estudiante) as cantidad_estudiantes 
                                               FROM Cursos 
                                               LEFT JOIN Estudiantes_Cursos 
                                               ON Cursos.id = Estudiantes_Cursos.id_curso 
                                               GROUP BY Cursos.id");

    while ($data = mysqli_fetch_array($query)) {
        $datos_curso[] = $data["nombre"];
        $datos_estudiantes[] = $data["cantidad_estudiantes"];
    }

    // Consulta para obtener el total de estudiantes en todos los cursos
    $query_total = mysqli_query($connect_academia, "SELECT COUNT(*) as total_estudiantes FROM Estudiantes_Cursos");
    if (!$query_total) {
        die('Error en la consulta: ' . mysqli_error($connect_academia));
    }

    $data_total = mysqli_fetch_array($query_total);
    $total_estudiantes = $data_total["total_estudiantes"];

    // Mostrar los datos
    //echo "Cursos y cantidad de estudiantes por curso:\n";
    foreach ($datos_curso as $index => $curso) {
        //echo "Curso: $curso, Estudiantes: " . $datos_estudiantes[$index] . "\n";
    }

    //echo "Total de estudiantes: " . $total_estudiantes;

?>

<style>
    .numeros{
        font-size: 30px;
        font-weight: bold;
        color: #0364ba;
    }
    .titulos{
        font-size: 16px;
        color: #0364ba;
        font-weight: bold;
    }
</style>

<script src="https://cdn.plot.ly/plotly-2.12.1.min.js"></script>

<div align="left" style="padding: 10px 0px;">
	<table width="100%">
    	<tr>
        	<td><h5 style="margin-top: 8px;"><i class="fas fa-check"></i> Analitica</h5></td>
            <td align="right">
            	<input class="form-control" type="text" placeholder="Búsqueda rápida..." id="buscador" style="width: 200px; display: inline-table;"/>
            </td>
        </tr>
    </table>
</div>



<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Inicio</a></li>

            <li class="breadcrumb-item"><a href="<?php echo $url; ?>?pg=academia/admin/final">Reporte Academia</a></li>

            <li class="breadcrumb-item active" aria-current="page"><a href="">Detalle</a></li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-md-4" style="margin-bottom: 15px">
            <div class="card" style="border-left: 0.25rem solid #ff6e40!important;">
                <div class="card-body" align="center" >                    
                    <div class="numeros"><?php echo $queryProgramas->num_rows; ?></div>
                    <div class="titulos">Programas <i style='font-size:24px' class='bx bx-customize'>&#xf0e8;</i></div>
                    
                </div>
            </div>
        </div>
    
        <div class="col-md-4" style="margin-bottom: 15px">
          <div class="card" style="border-left: 0.25rem solid #ff6e40!important;">
              <div class="card-body" align="center">
                    <div class="numeros"><?php echo $queryCursos->num_rows; ?></div>
                    <div class="titulos">Cursos <i style='font-size:24px' class='bx bx-spreadsheet'>&#xf19d;</i></div>
                </div>
            </div>
        </div>
		
		<div class="col-md-4" style="margin-bottom: 15px">
            <div class="card" style="border-left: 0.25rem solid #ff6e40!important;">
                <div class="card-body" align="center">
                    <div class="numeros"><?php echo $queryEstudiantes->num_rows; ?></div>
                    <div class="titulos">Estudiantes <i style='font-size:24px' class='bx bx-user-pin'>&#xf508;</i></div>
                </div>
            </div>
        </div>
        
        <?php
            $datos_ordenados = Obtener_Datos_Edad_Hijo($Array_Rangos_Edad_Hijos, "edad");
			//print_r($datos_ordenados);
        ?>
        
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script>
        <!-- FICHA 3--> 
		<div class="col-md-8" style="text-align:center; margin-top:15px; margin-bottom: 20px">
    
            <div class="row" style="font-size:14px; color: #6f6f6f; background-color: #fff; margin-left: 0; margin-right: 0; border-radius: 5px;">
           
                <div class="col-md-12" style="margin-top:15px">
                    <h4 class="my-0 font-weight-normal" style="color: #000000"><b>Cohortes</b></h4>
                </div>
                
                <div class="col-md-12" align="center" style="color:#8bc34a; margin-top:10px; margin-bottom:10px">
                    <canvas id="myBarChart" width="390" height="300"></canvas>
                </div>
                
            </div>
		</div>
    
		<script>
		var config = {
			type: 'line',
			data: {
				labels: <?php echo json_encode($datos_curso); ?>,
				datasets: [{
					label: 'Cantidad de estudiantes',
					backgroundColor: "#e91e63",
                    hoverBackgroundColor: "#2e59d9",
					borderColor: "#0096db",
					pointRadius:4,
					pointHoverRadius:12,
					data: <?php echo json_encode($datos_estudiantes); ?>,
					fill: false,
					}
				],
			},
			options: {
				responsive: true,
				maintainAspectRatio: false,
				title: {
					display: false,
					text: ''
				},
				tooltips: {
					mode: 'index',
					intersect: false,
				},
				hover: {
					mode: 'nearest',
					intersect: true
				},
				scales: {
					xAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Cursos'
						}
					}],
					yAxes: [{
						display: true,
						scaleLabel: {
							display: true,
							labelString: 'Estudiantes'
						}
					}]
				}
			}
		};

		var ctx = document.getElementById('myBarChart').getContext('2d');
		window.myLine = new Chart(ctx, config);
		</script>
		
		
        
		
        <?php 
        $querynoaprueba = mysqli_query($connect_academia,"SELECT * FROM Estudiantes_Evaluaciones WHERE estado = 0");
        $querysininiciar = mysqli_query($connect_academia,"SELECT * FROM Estudiantes_Evaluaciones WHERE estado = 1");
        $queryaprueba = mysqli_query($connect_academia,"SELECT * FROM Estudiantes_Evaluaciones WHERE estado = 2");
        ?>
        
        
        <div class="col-md-4" style="margin-bottom: 15px">
            <div class="card" style="height: 100%;">
                <div class="card-body" align="center">
                    <div class="titulos" style="margin-bottom: 50px"><h4 style="color: #000000"><b>Reporte de Evaluaciones</b></h4></div>
					<div id='char_genero'></div>
                </div>
            </div>
        </div>
		<script>
		var data = [{
			values: [<?php echo $querynoaprueba->num_rows; ?>, <?php echo $querysininiciar->num_rows; ?>, <?php echo $queryaprueba->num_rows; ?>],
			labels: ['No Aprobado <?php echo $querynoaprueba->num_rows; ?>', 'Iniciada <?php echo $querysininiciar->num_rows; ?>', 'Exámen Aprobado <?php echo $queryaprueba->num_rows; ?>' ], 
        	colors: [ '8bc34a', '03A9F4', '009248' ], 
			domain: {column: 0},
			hoverinfo: 'label+percent+name',
			hole: .4,
			type: 'pie', 
			automargin: true,
        }];
		var layout = {
          margin: {"t": 0, "b": 0, "l": 0, "r": 0},
          showlegend: false,
           height: 200
		};
		var config = {responsive: true}
            
        $( document ).ready(function() {
            Plotly.newPlot('char_genero', data, layout, config ); 
        });  
        </script>
		
		
		
        
        
        <style>
            .barra_avance{
                width: 100%;
                background-color: #f9f9f9;
                border-radius: 3px;
                
            }
            .porcentaje_realizadas{
                background-color: #f6d537;
                height: 20px;
            }
            .porcentaje_enproceso{
                background-color: #eaf5ff;
                height: 20px;
            }
            
            .numeros{
                margin: 3px 8px;
                color: #000000;
                font-weight: bold;
            }
            
        </style>
        
        
        

        <!-- personas por areas -->
        <div class="col-md-12" style="margin-bottom: 15px">
            <div class="card" style="height: 100%;">
                <div class="card-body" align="center">
                    <div class="titulos"><h4 style="color: #000000"><b>Cantidad de estudiantes por curso</b></h4></div>
                </div>
                
                <div style="height: 460px; overflow: auto;">
                        <table class="table table-bordered table-sm">
			<thead class="thead-dark">
			<tr>
				<th scope="col">#</th>
				<th scope="col">Programa</th>
				<th scope="col">Curso</th>
				<th scope="col">Estudiantes</th>
				
			</tr>
			</thead>

			<tbody id="tabla_lista">
			<?php
				$count = 1;
				$query = mysqli_query($connect_academia,"SELECT * FROM Cursos ");
				while($data = mysqli_fetch_array($query)){

					$queryPrograma = mysqli_query($connect_academia,"SELECT * FROM Programas WHERE id = '".$data["id_programa"]."' ");
					$dataPrograma = mysqli_fetch_array($queryPrograma);
					
					$queryEstudiantes = mysqli_query($connect_academia,"SELECT * FROM Estudiantes_Cursos WHERE id_curso = '".$data["id"]."' ");
					while($dataEstudiantes = mysqli_fetch_array($queryEstudiantes)){
						$queryEmpledo = mysqli_query($connect_academia,"SELECT * FROM Programas WHERE id = '".$data["id_programa"]."' ");
					}
					
					echo '
						<tr>
							<th scope="row">'.$count.'</th>
							<td>'.$dataPrograma["nombre"].'</td>
							<td>'.$data["nombre"].'</td>
							<td>'.$queryEstudiantes->num_rows.'</td>
							
						</tr>
					';
					$count++;

				}
			?>
			</tbody>
		</table>
                </div>
                
            </div>
        </div>
        
        
        
        
        
    </div>
    
</div>



