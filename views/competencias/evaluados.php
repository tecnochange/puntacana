<script>
	$(document).ready(function() {
		$('#menuCompetencias').collapse();
		$('#bt_competencias_programacion').addClass('active');
	});
</script>

<?php
include("app/models/competencias/Competencias.php");
$ClassCompetencias = new Competencias();

$dataCicloVal = $ClassCompetencias->Ciclo($user_log["id_empresa"], $_SESSION["anio_ciclo"]);

$hoy = date("Y-m-d H:i:s");

if ($_POST["anio"] != "") {
	$_SESSION['anio_ciclo'] = $_POST["anio"];
}

if ($_POST["id_tipo"] != "") {

	foreach ($_POST["id_tipo"] as $tipo) {
		$auto = $_POST["auto_" . $tipo];
		$jefe = $_POST["jefe_" . $tipo];
		$par = $_POST["par_" . $tipo];
		$subalterno = $_POST["subalterno_" . $tipo];
		$cliente = $_POST["cliente_" . $tipo];

		$queryValidate = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Tipo_Evaluador WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND id_tipo_competencia = '" . $tipo . "' ");
		if ($queryValidate->num_rows > 0) {

			$dataValidate = mysqli_fetch_array($queryValidate);

			mysqli_query($connect_valoracion, "UPDATE Competencias_Tipo_Evaluador SET  
                auto = '" . $auto . "', jefe = '" . $jefe . "', par = '" . $par . "', subalterno = '" . $subalterno . "', cliente = '" . $cliente . "' 
                WHERE id = '" . $dataValidate["id"] . "' ");
		} else {
			mysqli_query($connect_valoracion, "INSERT INTO Competencias_Tipo_Evaluador (id_empresa, anio, id_tipo_competencia, 
                auto, 	jefe, par, subalterno, 	cliente, estado, created_at) 
                VALUES 
                ( '" . $_SESSION['id_empresa'] . "', '" . $_SESSION['anio_ciclo'] . "', '" . $tipo . "', 
                '" . $auto . "',  '" . $jefe . "', '" . $par . "', '" . $subalterno . "', '" . $cliente . "', 1, '" . $hoy . "' ) ");
		}
	}
}


if ($_POST["terminar_conf"] != "") {
	mysqli_query($connect_valoracion, "UPDATE Competencias_Tipo_Evaluador SET  estado = 3 
        WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ");
}
?>

<?php include("views/competencias_pc/layouts/ficha_tipo.php"); ?>

<style>
	.checkbox {
		width: 22px;
		height: 22px;
	}
	.card,
	.card-body,
	.card-footer {
		background-color: #FFFFFF !important;
	}
	.checkbox_list {
		width: 18px;
		height: 18px;
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
							<a class="nav-link" href="?pg=competencias/escala">Administrar Escala</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="?pg=competencias/escala_interpretacion">Interpretación</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" href="?pg=competencias/evaluados">Competencias Tipo Evaluador</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="?pg=competencias/ponderar">Ponderar Evaluación</a>
						</li>
					</ul>
				</div>
				<div class="card-body">
					<form action="" method="post">
						<table class="table table-bordered table-sm">
							<thead class="thead-dark">
								<tr>
									<th scope="col" style="width:50px">#</th>
									<th scope="col">Tipo</th>
									<th scope="col">Auto</th>
									<th scope="col">Jefe</th>
									<th scope="col">Par</th>
									<th scope="col">Colaborador</th>
									<th scope="col">Cliente</th>
								</tr>
							</thead>

							<tbody>
								<?php
								$ver_editar = true;
								$count = 1;
								$query = mysqli_query($connect_valoracion, "SELECT * FROM Tipos WHERE id_empresa = '" . $user_log['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ORDER BY nombre DESC ");
								while ($data = mysqli_fetch_array($query)) {

									$queryValidate = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Tipo_Evaluador WHERE id_empresa = '" . $user_log['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND id_tipo_competencia = '" . $data["id"] . "' ");
									$dataValidate = mysqli_fetch_array($queryValidate);

									$ckeck_auto = '';
									$ckeck_jefe = '';
									$ckeck_par = '';
									$ckeck_subalterno = '';
									$ckeck_cliente = '';

									if ($dataValidate["auto"] == "on") {
										$ckeck_auto = 'checked';
									}
									if ($dataValidate["jefe"] == "on") {
										$ckeck_jefe = 'checked';
									}
									if ($dataValidate["par"] == "on") {
										$ckeck_par = 'checked';
									}
									if ($dataValidate["subalterno"] == "on") {
										$ckeck_subalterno = 'checked';
									}
									if ($dataValidate["cliente"] == "on") {
										$ckeck_cliente = 'checked';
									}

									if ($dataValidate["estado"] == 3) {
										$ver_editar = false;
									}

									echo '
									<tr>
										<th scope="row">' . $count . '</th>
										<td>
											' . $data["nombre"] . ' <input type="hidden" name="id_tipo[]"  value="' . $data["id"] . '">
										</td>
										<td><input type="checkbox" class="checkbox" name="auto_' . $data["id"] . '" ' . $ckeck_auto . '></td>
										<td><input type="checkbox" class="checkbox" name="jefe_' . $data["id"] . '" ' . $ckeck_jefe . '></td>
										<td><input type="checkbox" class="checkbox" name="par_' . $data["id"] . '" ' . $ckeck_par . '></td>
										<td><input type="checkbox" class="checkbox" name="subalterno_' . $data["id"] . '" ' . $ckeck_subalterno . '></td>
										<td><input type="checkbox" class="checkbox" name="cliente_' . $data["id"] . '" ' . $ckeck_cliente . '></td>
									</tr>
									';
									$count++;
								}
								?>
							</tbody>
						</table>

						<?php if ($ver_editar == true) { ?>
							<button type="submit" class="btn btn-primary w-100 ">
								Guardar
							</button>
					</form>

					<form action="" method="post" style="display: none">
						<input type="hidden" name="terminar_conf" value="true">
						<button type="submit" class="btn btn-danger btn-sm" style="margin-top: 15px">
							Terminar Programación
						</button>
					</form>
				<?php } else { ?>

					<script>
						$(".checkbox").attr("disabled", true);
					</script>

				<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	/* $(document).ready(function() {

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

	var Cont_Modal = $("#cont_modal").html();
	var api = '<?php echo $url; ?>api/competencias_pc/';

	function Ficha_Tipo(id) {
		jQuery.ajax({
				url: api + "ficha_tipo.php",
				type: 'post',
				data: {
					id: id,
					url: "?pg=competencias_tipo"
				},
			}).done(function(resp) {
				$("#cont_modal").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});
	}

	function Seter_Ficha() {
		$("#cont_modal").html(Cont_Modal);
	} */
</script>