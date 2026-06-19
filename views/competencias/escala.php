<script>
	$(document).ready(function() {
		$('#menuCompetencias').collapse();
		$('#bt_competencias_programacion').addClass('active');
	});
</script>

<?php
include("app/models/competencias/Competencias.php");
include("app/models/competencias/CompetenciasCrud.php");
$ClassCompetencias = new Competencias();
$ClassCompetenciasCrud = new CompetenciasCrud();
$dataCicloVal = $ClassCompetencias->Ciclo($user_log["id_empresa"], $_SESSION["anio_ciclo"]);

$hoy = date("Y-m-d H:i:s");
$id = $_GET["id"];

//CONSULTA PARA NUEVO CLIENTE
if ($_POST["nombre_n_1"] != "") {

	if ($_POST["id_registro"] != "") {
		$ClassCompetenciasCrud->Editar_Escala($_POST);
	} else {
		$ClassCompetenciasCrud->Guardar_Escala($user_log["id_empresa"], $_SESSION["anio_ciclo"], $_POST);
	}
	
	/* echo '
            <script> window.location = "' . $url . '/?pg=competencias/escala"; </script>
        '; */
}

$data = $ClassCompetencias->Obtener_Escala($user_log["id_empresa"], $_SESSION["anio_ciclo"]); //DATOS DE LA ESCALA
?>

<style>
	.titu {
		font-weight: bold;
		text-align: center;
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
</style>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

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
				<div class="card-header">
					<ul class="nav nav-pills justify-content-center">
						<li class="nav-item">
							<a class="nav-link " href="?pg=competencias/programacion">Ciclo Evaluación</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" href="?pg=competencias/escala">Administrar Escala</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="?pg=competencias/escala_interpretacion">Interpretación</a>
						</li>
						<li class="nav-item">
							<a class="nav-link " href="?pg=competencias/evaluados">Competencias Tipo Evaluador</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="?pg=competencias/ponderar">Ponderar Evaluación</a>
						</li>
					</ul>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<h3>Administrar Escala</h3>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<form action="" method="post">
								<input type="hidden" name="id_registro" value="<?php echo $data["id"];  ?>">
								<table width="100%">

									<tr>
										<td width="16%" align="center" style="font-size: 25px; padding: 20px; font-weight: bold">
											1
										</td>
										<td width="16%" align="center" style="font-size: 25px; padding: 20px; font-weight: bold">
											2
										</td>
										<td width="16%" align="center" style="font-size: 25px; padding: 20px; font-weight: bold">
											3
										</td>
										<td width="16%" align="center" style="font-size: 25px; padding: 20px; font-weight: bold">
											4
										</td>
										<td width="16%" align="center" style="font-size: 25px; padding: 20px; font-weight: bold">
											5
										</td>
									</tr>
									<tr>
										<td>
											<input type="text" class="form-control titu" placeholder="Ingresar Nombre..." name="nombre_n_1" value="<?php echo $data["nombre_n_1"]; ?>">
											<textarea class="form-control" placeholder="Ingresar descripción..." rows="7" name="descripcion_n_1"><?php echo $data["descripcion_n_1"]; ?></textarea>
										</td>
										<td>
											<input type="text" class="form-control titu" placeholder="Ingresar Nombre..." name="nombre_n_2" value="<?php echo $data["nombre_n_2"]; ?>">
											<textarea class="form-control" placeholder="Ingresar descripción" rows="7" name="descripcion_n_2"><?php echo $data["descripcion_n_2"]; ?></textarea>
										</td>
										<td>
											<input type="text" class="form-control titu" placeholder="Ingresar Nombre..." name="nombre_n_3" value="<?php echo $data["nombre_n_3"]; ?>">
											<textarea class="form-control" placeholder="Ingresar descripción" rows="7" name="descripcion_n_3"><?php echo $data["descripcion_n_3"]; ?></textarea>
										</td>
										<td>
											<input type="text" class="form-control titu" placeholder="Ingresar Nombre..." name="nombre_n_4" value="<?php echo $data["nombre_n_4"]; ?>">
											<textarea class="form-control" placeholder="Ingresar descripción" rows="7" name="descripcion_n_4"><?php echo $data["descripcion_n_4"]; ?></textarea>
										</td>
										<td>
											<input type="text" class="form-control titu" placeholder="Ingresar Nombre..." name="nombre_n_5" value="<?php echo $data["nombre_n_5"]; ?>">
											<textarea class="form-control" placeholder="Ingresar descripción" rows="7" name="descripcion_n_5"><?php echo $data["descripcion_n_5"]; ?></textarea>
										</td>
									</tr>
								</table>

								<button type="submit" class="btn btn-primary w-100" style="margin-top: 20px">
									Guardar
								</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>