<script>
	$(document).ready(function() {
		$('#menuCompetencias').collapse();
		$('#bt_realizar_valoracion').addClass('active');
	});
</script>

<?php
include("app/models/competencias/Competencias.php");
$ClassCompetencias = new Competencias();
$dataCicloVal = $ClassCompetencias->Ciclo($user_log["id_empresa"], $_SESSION["anio_ciclo"]);
$dataEmpleado = $ClassCompetencias->Empleado($user_log["id"]);
$dataEvaluador = $ClassCompetencias->Evaluador($user_log["id"], $dataCicloVal["anio"], $dataCicloVal["id"]);

$ciclo_cerrado = false;
if ($dataCicloVal["fecha_termina"] < date("Y-m-d")) {
	$ciclo_cerrado = true;
}

if (count($dataEvaluador) == 0) {
	if ($dataEvaluador["tipo"] == 5) {
?>
		<script>
			$(document).ready(function() {
				$('#modal_valoracion_auto').modal('toggle')
			});
		</script>


<?php
	}
}

$queryEval1 = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND
				id_ciclo = '" . $dataCicloVal["id"] . "' AND
				id_evaluado = '" . $user_log["id"] . "' AND
				id_evaluador = '" . $user_log["id"] . "' AND
				anio = '" . $dataCicloVal["anio"] . "' AND
				tipo_evaluacion = '" . $dataEvaluador["tipo"] . "' ");

$update_at1 = "";
if ($queryEval1->num_rows > 0) {
	$dataEval1 = mysqli_fetch_array($queryEval1);

	if ($dataEval1["estado"] == 1 || $dataEvaluador["tipo"] == 1) {
		$txt_estado1 = 'En Proceso';
		$bt_editar1 = '
		<a href="' . $url . '?pg=competencias/evaluacion&id=' . $dataEval1["id"] . '&pos=1">
            <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
                <i class="bx bx-edit"></i>
            </button>
		</a>';
	}

    if ($dataEval1["estado"] == 3 && $dataEvaluador["tipo"] == 1) {
		$txt_estado1 = 'Teminada';
        $update_at1 = $dataEval1["update_at"];
		//$bt_editar1 = 'Teminada';
	}

    if ($dataEval1["estado"] == 3 ) {
		$bt_editar1 = 'Teminada';
	}

	if ($dataEval1["estado"] == 2) {
		$txt_estado1 = 'Terminada';
		$update_at1 = $dataEval1["update_at"];
		$bt_editar1 = '';
	}
} else if ($dataEvaluador["tipo"] == 1) {
	$txt_estado1 = 'Pendiente';
	$bt_editar1 = '
	<a href="' . $url . '?pg=competencias/evaluacion&evaluado=' . $user_log["id"] . '&t=1&pos=1">
        <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
            <i class="bx bx-edit"></i> 
        </button>
	</a>';
}

if ($ciclo_cerrado == true) {
	$bt_editar1 = 'Ciclo Finalizado';
}
include("views/competencias/modal_competencias.php");

$querySM11 = mysqli_query($connect_admin, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $user_log["id_empresa"] . " AND estado = 1 AND id_menu = 1 AND id_submenu = 1");
$dataSM11 = mysqli_fetch_array($querySM11);
?>


<?php 

function ValidarEvaluaciones($id_evaluado, $id_jefe, $id_empresa){
    global $dataCicloVal;
    global $connect_valoracion;

    $permitir = false;
    $con_auto = false;
    $sin_evaluacion_terminada = true;
    $no_evaluadores = 0;
    $evaluaciones_terminadas = 0;

    $sentenciaEvals = "
        SELECT * FROM Evaluadores
        WHERE 
        Evaluadores.id_empleado = '".$id_evaluado."' AND 
        Evaluadores.anio = '" . $dataCicloVal["anio"] . "' AND 
        Evaluadores.id_ciclo = '" . $dataCicloVal["id"] . "' AND 
        Evaluadores.tipo != 5
    ";
	$queryEvaluadores = mysqli_query($connect_valoracion, $sentenciaEvals);
	while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) { 

        $no_evaluadores++;

        $queryValidacion = mysqli_query($connect_valoracion, "SELECT * FROM  Competencias_Evaluaciones_New
		WHERE id_empresa = '".$id_empresa."'
		AND id_ciclo = '" . $dataCicloVal["id"] . "'
		AND id_evaluado = '" . $dataEvaluadores["id_empleado"] . "'
		AND id_evaluador = '" . $dataEvaluadores["id_evaluador"] . "'
		AND anio = '" . $dataCicloVal["anio"] . "'");
        $dataValidacion = mysqli_fetch_array($queryValidacion);

        if($queryValidacion->num_rows > 0){
            if($dataValidacion["estado"] >= 2){
                $evaluaciones_terminadas++;

                
            }
            else{
                $sin_evaluacion_terminada = false;
            }
        }
        else{
            $sin_evaluacion_terminada = false;
        }
    }

    /*
    if($sin_evaluacion_terminada == false){
        echo "faltan_evaluadiones";

    }
    */

    return array(
        "validacion" => $sin_evaluacion_terminada,
        "evaluaciones" => $no_evaluadores, 
        "terminadas" => $evaluaciones_terminadas
    );

 

}

?>

<style>
    /* Margen debajo de la barra de herramientas (botones) */
    .dt-buttons {
        margin-bottom: 15px !important;
    }
	/* Margen debajo de la tabla (paginación) */
	.dataTables_paginate, .dataTables_info{
		margin-top: 15px !important;
	}
</style>

<div class="container" >

	<!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Realizar Valoración <?= $dataCicloVal["anio"]; ?> |  <small>Ciclo: <?php echo $dataCicloVal["nombre"]; ?></small> </h3>
        </div>
    </div>

	<?php echo $respuesta; ?>

    <!-- AUTOEVALUACION --> 
	<div class="row mb-3">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header" style="background-color: #3aae2a !important;">
					<div class="row">
						<div class="col-md-12" style="text-align: center !important;">
							<h3 style="color: #FFFFFF !important;">Mi Valoración</h3>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col" style="min-width: 250px !important;">
							<b>Nombre:</b><br>
							<?php echo $dataEmpleado["nombre"]; ?>
						</div>
						<div class="col">
							<b>Cargo:</b><br>
							<?php echo $dataEmpleado["nombre_cargo"]; ?>
						</div>
						<div class="col">
							<b>Área:</b><br>
							<?php echo $dataEmpleado["nombre_area"]; ?>
						</div>
						<div class="col">
							<b>Rol Valoración:</b><br>
							<?php
							foreach ($array_Tipo_Colaborador as $tipo) {
								if ($tipo[0] == $dataEvaluador["tipo"]) {
									echo $tipo[1];
								}
							}
							?>
						</div>
						<div class="col">
							<b>Fecha Realización:</b><br>
							<?php echo $update_at1; ?>
						</div>
						<div class="col">
							<b>Estado:</b><br>
							<?php echo $txt_estado1; ?>
						</div>
						<div class="col">
							<b>Acción:</b><br>
							<?php echo $bt_editar1; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

    <!-- ASIGNADAS -->
    <div class="card">
        <div class="card-header" style="background-color: #007bff !important;">
            <h3 style="color: #FFFFFF !important; text-align: center !important; ">Valoraciones Asignadas</h3> 
        </div>
        <div class="card-body">
            <div class="table-responsive">
               <table border="1" id="competencias_pc" class="display table" style="width:100%;">
					<thead class="table-dark">
									<tr>
										<th scope="col">Persona Valorada</th>
										<th scope="col">Cargo</th>
										<th scope="col">Area</th>
										<th scope="col">Autovaloración</th>
										<th scope="col">Estado Autovaloración</th>
										<th scope="col">Fecha Autovaloración</th>
										<th scope="col">Rol Valoración</th>
										<th scope="col">Fecha Realización Valoración</th>
										<th scope="col">Estado</th>
										<th scope="col">Acciones</th>
									</tr>
					</thead>

					<tbody>
					<?php 

                        $sentenciaEvals = "
                        SELECT
                            Evaluadores.*
                        FROM
                            Evaluadores
                        INNER JOIN puntacana_admin.Empleados E ON E.id = Evaluadores.id_empleado
                        WHERE
                            Evaluadores.id_evaluador = '" . $user_log["id"] . "' AND Evaluadores.anio = '" . $dataCicloVal["anio"] . "' AND Evaluadores.id_ciclo = '" . $dataCicloVal["id"] . "' AND Evaluadores.tipo != 1
                        ORDER BY
                            E.nombre ASC;
                        ";
						$queryEvaluadores = mysqli_query($connect_valoracion, $sentenciaEvals);
						while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) { 

                        

                            //DATOS COLABORADOR
							$sentencia = "SELECT
										Empleados.id AS id, Empleados.id_empresa AS id_empresa, Empleados.nombre AS nombre, Empleados.correo AS correo_corporativo, Empleados.correo_personal AS correo_personal, Empleados.telefono_movil AS celular, Empleados.documento AS documento, Empleados.role AS role, Empleados.estado AS id_estado,
										Empleados.foto AS foto,Cargos.id AS id_cargo, Cargos.nombre AS nombre_cargo, Areas.nombre AS nombre_area, Areas.id AS id_area
										FROM Empleados
										LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
										LEFT JOIN Areas ON Areas.id = Empleados.area
										WHERE Empleados.id = " . $dataEvaluadores["id_empleado"] . " AND Empleados.id_empresa = '" . $user_log["id_empresa"] . "'
										ORDER BY Empleados.nombre ASC
							";
							$queryColaborador = mysqli_query($connect_admin, $sentencia);
							$colaborador = mysqli_fetch_array($queryColaborador);

                            //STRING TIPO
							$tipo_txt = '';
							foreach ($array_Tipo_Colaborador as $tipo) {
								if ($tipo[0] == $dataEvaluadores["tipo"]) {
									$tipo_txt =  $tipo[1];
								}
							}

                            //VARIABLES EVALUACION
						    $txt_auto = 'No';
							$txt_estado_Auto = ''; 
                            $update_at_auto =  '';

                            //PARA VALIDAR SI EXISTE UN AUTO EVALUADOR
                            //PARA VALIDAR SI EXISTE UN AUTO EVALUADOR
                            //PARA VALIDAR SI EXISTE UN AUTO EVALUADOR
							$queryAutoValidar = mysqli_query($connect_valoracion ," SELECT * FROM Evaluadores
							WHERE anio = '" . $dataCicloVal["anio"] . "' AND id_ciclo = '" . $dataCicloVal["id"] . "' AND id_empleado = '" . $dataEvaluadores["id_empleado"] . "' AND tipo = 1 ");
                            $dataAutoValidar = mysqli_fetch_array($queryAutoValidar);

                            if($queryAutoValidar->num_rows > 0){
                                $txt_auto = 'No';
                                $txt_estado_Auto = 'No'; 
                                $update_at_auto = 'N/A';
                            }
                            else{
                                $txt_auto = 'No Programada';
							    $txt_estado_Auto = 'No Programada'; 
                                $update_at_auto = 'No Programada';
                            }











                            //VARIABLES EVALUACIÓN ASIGNADA
							$txt_estado = 'Pendiente';
							$procesar = 0;
							$update_at = 'N/A';
                            $bt_editar = '';

                            //VALIDAR SI EXISTE EVALUACION PARA ESTE EVALUADOR
                            $queryValidacion = mysqli_query($connect_valoracion, "SELECT * FROM  Competencias_Evaluaciones_New
							WHERE id_empresa = '" . $user_log["id_empresa"] . "'
							AND id_ciclo = '" . $dataCicloVal["id"] . "'
							AND id_evaluado = '" . $dataEvaluadores["id_empleado"] . "'
							AND id_evaluador = '" . $dataEvaluadores["id_evaluador"] . "'
							AND anio = '" . $dataCicloVal["anio"] . "'");
                            $dataValidacion = mysqli_fetch_array($queryValidacion);

                            if($queryValidacion->num_rows > 0){
                                if($dataValidacion["estado"] >= 2){
                                    $txt_estado = "Finalizada"; 
                                    $bt_editar = '✅';
                                    $update_at = $dataValidacion["created_at"];
                                }
                                else{
                                    $txt_estado = "En Proceso"; 
                                    $bt_editar = '
                                        <a href="' . $url . '?pg=competencias/evaluacion&id='.$dataValidacion["id"].'&pos=1">
                                            <button type="button" class="btn btn-primary btn-sm" title="Continuar Evaluación">
                                                <i class="bx bx-edit"></i>
                                            </button>
                                        </a>
                                    ';
                                }
                            }
                            else{
                                $bt_editar = '
                                    <a href="' . $url . '?pg=competencias/evaluacion&evaluado=' . $dataEvaluadores["id_empleado"] . '&t=' . $dataEvaluadores["tipo"] . '&cargo=' . $dataEm["id_cargo"] . '">
                                        <button type="button" class="btn btn-primary btn-sm" title="Iniciar Evaluación">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                    </a>
                                ';
                            }





                            //PARA VALIDAR SI EXISTE UNA EVALUACION AUTO
							$queryAuto = mysqli_query($connect_valoracion ,"
                            SELECT * FROM Competencias_Evaluaciones_New
							WHERE anio = '" . $dataCicloVal["anio"] . "' AND id_ciclo = '" . $dataCicloVal["id"] . "' AND id_evaluado = '" . $dataEvaluadores["id_empleado"] . "' AND tipo_evaluacion = 1 AND estado >= 2 ");

                            //VALIDACION SI EXISTE UN AUTO
							if ($queryAuto->num_rows > 0) { 
                                $dataAuto = mysqli_fetch_array($queryAuto);
								$txt_auto = 'Si';
							    $txt_estado_Auto = 'Finalizada'; 
                                $update_at_auto = $dataAuto["created_at"];
                            }


                            



                            

                            //REGLAS PARA EL JEFE
                            
                            if($dataEvaluadores["tipo"] == 5 && $txt_auto != 'No Programada' ){
                                $permitir_jefe = ValidarEvaluaciones($dataEvaluadores["id_empleado"], $dataEvaluadores["id_evaluador"], 1);

                                if(!$permitir_jefe["validacion"]){
                                    $txt_estado = "Otros evaluadores en curso ".$permitir_jefe["terminadas"]."/".$permitir_jefe["evaluaciones"]; 
                                    //$bt_editar = '';
                                }
                                else{
                                    //$txt_estado = "Pendiente"; 
                                }
                            }
                            

                            













                            if($txt_auto == 'No'){
                                //$txt_estado = 'Sin Autoevaluación';
                                $bt_editar = 'Sin Autoevaluación';
                            }
                                        

                            if ($ciclo_cerrado == true) {
                                $bt_editar = 'Ciclo Finalizado';
                            }


							echo '
											<tr>
												<td>' . $colaborador["nombre"] . '</td>
												<td>' . $colaborador["nombre_cargo"] . '</td>
												<td>' . $colaborador["nombre_area"] . '</td>
												<td>' . $txt_auto . '</td>
												<td>' . $txt_estado_Auto . '</td>
												<td>' . $update_at_auto . '</td>
												<td>' . $tipo_txt . '</td>
												<td>' . $update_at . '</td>
												<td align="center">' . $txt_estado . '</td>
												<td align="center">' . $bt_editar . '</td>
											</tr>
							';
						}

					?>
					</tbody>
				</table> 
            </div>
        </div>
    </div>





	
	
</div>

<!-- CSS de DataTables + Botones -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- JS de DataTables + Botones -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
    $(document).ready(function() {
        $('#competencias_pc').DataTable({
            pageLength: 50,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: 'Descargar Excel'
            }]
        });
    });
</script>