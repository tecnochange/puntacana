<script>
	$(document).ready(function() {
		$('#menuCompetencias').collapse();
		$('#bt_tipo_competencias').addClass('active');
	});
</script>

<?php
include("app/models/competencias/Competencias.php");
$ClassCompetencias = new Competencias();
$dataCicloVal = $ClassCompetencias->Ciclo($user_log["id_empresa"], $_SESSION["anio_ciclo"]);

$hoy = date("Y-m-d H:i:s");

//CONSULTA PARA NUEVO CLIENTE
if ($_POST["nombre"] != "") {
	if ($_POST["id_competencia"] != "") {
		mysqli_query($connect_valoracion, "UPDATE Competencias SET nombre = '" . $_POST["nombre"] . "', id_tipo = '" . $_POST["id_tipo"] . "',  
			definicion = '" . $_POST["definicion"] . "' WHERE id = '" . $_POST["id_competencia"] . "'  ");
	} else {
		mysqli_query($connect_valoracion, "INSERT INTO Competencias (id_empresa, anio, id_ciclo, nombre, definicion, id_tipo, created_at, update_at) 
			VALUES 
			( '" . $user_log['id_empresa'] . "', '" . $_SESSION['anio_ciclo'] . "', '" . $_SESSION['ciclo'] . "', '" . $_POST["nombre"] . "', '" . $_POST["definicion"] . "', '" . $_POST["id_tipo"] . "', '" . $hoy . "', '" . $hoy . "' ) ");


		$id_l = mysqli_insert_id($connect);


		$i = 0;
		foreach ($_POST["id_nivel"] as &$nivel) {
			mysqli_query($connect_valoracion, "INSERT INTO Competencias_Niveles (id_competencia, id_nivel, definicion, created_at, update_at) 
				VALUES 
				('" . $id_l . "', '" . $nivel . "', '" . $_POST["definicion"][$i] . "', '" . $hoy . "', '" . $hoy . "' ) ");
			//print_r($nivel." - ". $_POST["definicion"][$i]."<hr>");
			$i++;
		}
	}
	$respuesta = '
			<div class="alert alert-success" role="alert" style="margin-top:8px">
			  Información Guardada.
			</div>
		';
}

//TIPOS
$arrayTipos = array();
$queryT = mysqli_query($connect_valoracion, "SELECT * FROM Tipos WHERE id_empresa = '" . $user_log['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ORDER BY id DESC ");
while ($dataT = mysqli_fetch_array($queryT)) {
	array_push($arrayTipos, array($dataT["id"], $dataT["nombre"]));
}

//NIVELES
$arrayNiveles = array();
$queryN = mysqli_query($connect_valoracion, "SELECT * FROM Niveles WHERE id_empresa = '" . $user_log['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ORDER BY id DESC ");
while ($dataN = mysqli_fetch_array($queryN)) {
	array_push($arrayNiveles, array($dataN["id"], $dataN["nombre"]));
}
?>

<?php include("views/competencias_pc/layouts/ficha_competencia.php");
$querySM12 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $user_log["id_empresa"] . " AND estado = 1 AND id_menu = 1 AND id_submenu = 2");
$dataSM12 = mysqli_fetch_array($querySM12);
?>

<style>
    table thead th {
        white-space: nowrap;
    }
	.checkbox_list {
		width: 18px;
		height: 18px;
	}
	.card,
	.card-body,
	.card-footer {
		background-color: #FFFFFF !important;
	}
	/* Margen debajo de la barra de herramientas (botones) */
	.dt-buttons {
		margin-bottom: 15px !important;
	}
</style>

<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">

	<?php echo $respuesta; ?>

	<!-- TITULO -->
	<div class="card mb-3">
		<div class="card-header">
			<h3>Tipo de Competencias <?= $dataCicloVal["anio"]; ?> | <small>Ciclo: <?php echo $dataCicloVal["nombre"]; ?></small> </h3>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="card">

				<!-- PESTAÑAS -->
				<div class="card-header">
					<ul class="nav nav-pills justify-content-center">
						<li class="nav-item">
							<a class="nav-link " href="?pg=competencias/competencias/tipos">Tipos</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="?pg=competencias/competencias/niveles">Niveles</a>
						</li>

						<li class="nav-item">
							<a class="nav-link active" href="?pg=competencias/competencias/competencias">Competencias</a>
						</li>

						<li class="nav-item" style="display:none" >
							<a class="nav-link" href="?pg=competencias/competencias/informes">Informes</a>
						</li>

						<li class="nav-item" style="display:none" >
							<a class="nav-link" href="?pg=competencias/competencias/acciones">Acciones de Desarrollo</a>
						</li>
					</ul>
				</div>

				<!-- CONTENIDO -->
				<div class="card-body">
					<?php if ($_SESSION['anio_ciclo'] && $_SESSION['ciclo']) { ?>
						<div class="table-responsive">
							<table class="table">
								<thead class="table-dark" id="competenciasTable">
									<tr>
										<th scope="col" style="width:50px">#</th>
										<th scope="col">Año Lic.</th>
										<th scope="col">Tipo</th>
										<th scope="col">Nivel</th>
										<th scope="col">Indicador</th>
										<th scope="col">Comportamiento / Pregunta</th>
										<th scope="col" style="width: 120px; text-align:center">
											<a href="?pg=competencias/competencias/detalle" type="button" class="btn btn-warning btn-sm" title="Crear nueva competencia">
												Nueva
											</a>
										</th>
									</tr>
								</thead>

								<tbody>
									<?php

									$count = 1;
									$query = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE anio = '" . $_SESSION['anio_ciclo'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "'  ORDER BY id ASC ");

									while ($data = mysqli_fetch_array($query)) {

										$text_tipo = '';
										foreach ($arrayTipos as &$tipo) {
											if ($data["id_tipo"] == $tipo["0"]) {
												$text_tipo = $tipo["1"];
											}
										}

										echo '
										<tr>
											<th scope="row">' . $count . '</th>
											<td>' . $_SESSION['anio_ciclo'] . '</td>
											<td><b>' . eliminar_tildes($text_tipo) . '</b></td>
											<td colspan="3">
												<h4>' . eliminar_tildes(str_replace('LÁDERES', 'LÍDERES', $data["nombre"])) . '</h4>
												' . eliminar_tildes($data["definicion"]) . '
											</td>
											<td align="center">
												<a href="?pg=competencias/competencias/detalle&id=' . $data["id"] . '" type="button" class="btn btn-primary btn-sm">
													<i class="bx bx-pencil" title="Editar"></i>
												</a>
												<a href="?pg=competencias/competencias/preguntas&id=' . $data["id"] . '" type="button" class="btn btn-info btn-sm">
													<i class="bx bx-abacus"></i>
												</a>
											</td>
										</tr>
										';

										$count++;
										// echo "SELECT * FROM Competencias_Niveles WHERE id_competencia = '" . $data["id"] . "' ORDER BY id DESC<br>";
										$queryNiveles = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id_competencia = '" . $data["id"] . "' ORDER BY id DESC ");
										while ($dataNiveles = mysqli_fetch_array($queryNiveles)) {

											$text_nivel = '';
											foreach ($arrayNiveles as &$nivel) {
												if ($dataNiveles["id_nivel"] == $nivel["0"]) {
													$text_nivel = $nivel["1"];
												}
											}
											// echo "SELECT * FROM Competencias_Preguntas WHERE id_nivel_competencia = '" . $dataNiveles["id"] . "' ORDER BY id DESC<br>";
											$queryPreguntas = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Preguntas WHERE id_nivel_competencia = '" . $dataNiveles["id"] . "' ORDER BY id DESC ");
											while ($dataPreguntas = mysqli_fetch_array($queryPreguntas)) {
												echo '
												<tr>
													<td scope="row"></td>
													<td></td>
													<td></td>
													<td><b>' . eliminar_tildes($text_nivel) . '</b></td>
													<td>' . eliminar_tildes($dataPreguntas["indicador"]) . '</td>
													<td>' . eliminar_tildes($dataPreguntas["pregunta"]) . '</td>
												</tr>
												';
											}
										}
									}
									?>
								</tbody>
							</table>
						</div>
					<?php } else { ?>
						<div class="alert alert-warning" role="alert" align="center">
							Primero seleccione un ciclo
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {

		$("#myInput").on("keyup", function() {
			var value = $(this).val().toLowerCase();

			$(".myTable").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
				$(this).next(".insumos").toggle($(this).text().toLowerCase().indexOf(value) > -1);
				//$(this).next(".insumos").toggle();
				//$(this).parent().next().hide();
			});

			$(".name_fil_off").parent().show();

		});

	});
</script>