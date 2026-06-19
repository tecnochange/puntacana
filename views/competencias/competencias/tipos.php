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

	if ($_POST["id_tipo"] != "") {
		mysqli_query($connect_valoracion, "UPDATE Tipos SET nombre = '" . $_POST["nombre"] . "' WHERE id = '" . $_POST["id_tipo"] . "'  ");
	} else {
		mysqli_query($connect_valoracion, "INSERT INTO Tipos (id_empresa, anio, nombre, created_at, update_at) 
			VALUES 
			( '" . $_SESSION['id_empresa'] . "', '" . $_SESSION['anio_ciclo'] . "', '" . $_POST["nombre"] . "', '" . $hoy . "', '" . $hoy . "' ) ");
	}

	$respuesta = '
			<div class="alert alert-success" role="alert" style="margin-top:8px">
			  Información Guardada.
			</div>
		';
}
?>

<?php include("views/competencias_pc/layouts/ficha_tipo.php");
$querySM12 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 1 AND id_submenu = 2");
$dataSM12 = mysqli_fetch_array($querySM12);
?>

<style>
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
	/* Margen debajo de la tabla (paginación) */
	.dataTables_paginate, .dataTables_info{
		margin-top: 15px !important;
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

				<!-- PESTAÑAS -->
				<div class="card-header">
					<ul class="nav nav-pills justify-content-center">
						<li class="nav-item">
							<a class="nav-link active" href="?pg=competencias/competencias/tipos">Tipos</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="?pg=competencias/competencias/niveles">Niveles</a>
						</li>

						<li class="nav-item">
							<a class="nav-link " href="?pg=competencias/competencias/competencias">Competencias</a>
						</li>

						<li class="nav-item" style="display:none">
							<a class="nav-link" href="?pg=competencias/competencias/informes">Informes</a>
						</li>

						<li class="nav-item" style="display:none">
							<a class="nav-link" href="?pg=competencias/competencias/acciones">Acciones de Desarrollo</a>
						</li>
					</ul>
				</div>

				<!-- CONTENIDO -->
				<div class="card-body">
					<?php if ($_SESSION['anio_ciclo'] != "") { ?>
						<div class="table-responsive">
							<table border="1" id="competenciasTipos" class="display table" style="width:100%;">
								<thead class="table-dark">
									<tr>
										<th scope="col" style="width:50px">#</th>
										<th scope="col">Tipo</th>
										<th scope="col">Año</th>
										<th scope="col" style="width: 45px;">
											<a href="?pg=competencias/competencias/tipos/detalle" type="button" class="btn btn-warning btn-sm" title="Crear un Tipo de Competencia">
												Nuevo
											</a>
										</th>
									</tr>
								</thead>

								<tbody>
									<?php
									$count = 1;
									$query = mysqli_query($connect_valoracion, "SELECT * FROM Tipos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ORDER BY nombre DESC ");
									while ($data = mysqli_fetch_array($query)) {

										echo '
										<tr>
											<th scope="row">' . $count . '</th>
											<td>' . eliminar_tildes($data["nombre"]) . '</td>
											<td>' . $data["anio"] . '</td>
											<td align="center">
												<a href="?pg=competencias/competencias/tipos/detalle&id=' . $data["id"] . '" type="button" class="btn btn-primary btn-sm">
													<i class="bx bx-pencil" title="Editar"></i>
												</a>
											</td>
										</tr>
										';
										$count++;
									}
									?>
								</tbody>
							</table>
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

	var Cont_Modal = $("#cont_modal").html();
	var api = '<?php echo $url; ?>/api/competencias_pc/';

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
	}
</script>

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
		$('#competenciasTipos').DataTable({
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