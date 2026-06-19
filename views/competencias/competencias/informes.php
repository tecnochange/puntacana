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
//CONSULTA PARA NUEVO CLIENTE
if ($_POST["id_informe_indicador"] != "") {

	mysqli_query($connect_valoracion, "UPDATE Competencias_Preguntas SET fortaleza = '" . $_POST["fortaleza"] . "', oportunidad = '" . $_POST["oportunidad"] . "' 
        WHERE id = '" . $_POST["id_informe_indicador"] . "'  ");

	$respuesta = '
			<div class="alert alert-success" role="alert" style="margin-top:8px">
			  Información Guardada.
			</div>
		';
}

//TIPOS
$arrayTipos = array();
$queryT = mysqli_query($connect_valoracion, "SELECT * FROM Tipos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ORDER BY id DESC ");
while ($dataT = mysqli_fetch_array($queryT)) {
	array_push($arrayTipos, array($dataT["id"], $dataT["nombre"]));
}

//NIVELES
$arrayNiveles = array();
$queryN = mysqli_query($connect_valoracion, "SELECT * FROM Niveles WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ORDER BY id DESC ");
while ($dataN = mysqli_fetch_array($queryN)) {
	array_push($arrayNiveles, array($dataN["id"], $dataN["nombre"]));
}
?>

<?php include("views/competencias_pc/layouts/ficha_informe_competencia.php");
$querySM12 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 1 AND id_submenu = 2");
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
					<ul class="nav nav-pills justify-content-center" style="margin-bottom: 10px;">
						<li class="nav-item">
							<a class="nav-link " href="?pg=competencias/competencias/tipos">Tipos</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="?pg=competencias/competencias/niveles">Niveles</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="?pg=competencias/competencias/competencias">Competencia</a>
						</li>

						<li class="nav-item">
							<a class="nav-link active" href="?pg=competencias/competencias/informes">Informes</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="?pg=competencias/competencias/acciones">Acciones de Desarrollo</a>
						</li>
					</ul>
				</div>

				<!-- CONTENIDO -->
				<div class="card-body">
					<div class="table-responsive">
						<table border="1" id="competenciasInformes" class="display table" style="width:100%;">
							<thead class="table-dark">
								<tr>
									<th scope="col" style="width:50px">#</th>
									<th scope="col">Año Lic.</th>
									<th scope="col">Tipo</th>
									<th scope="col">Competencia</th>
									<th scope="col">Nivel</th>
									<th scope="col">Indicador</th>
									<th scope="col">Fortaleza</th>
									<th scope="col">Oportunidad de Mejora</th>
									<th scope="col" style="width: 90px; text-align:center">Acciones</th>
								</tr>
							</thead>

							<tbody>
								<?php
								$count = 1;
								$query = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Preguntas WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ORDER BY id ASC ");

								while ($data = mysqli_fetch_array($query)) {

									$queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $data["id_competencia"] . "' ");
									$dataComp = mysqli_fetch_array($queryComp);

									$text_tipo = '';
									foreach ($arrayTipos as &$tipo) {
										if ($dataComp["id_tipo"] == $tipo[0]) {
											$text_tipo = $tipo[1];
										}
									}

									$queryNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $data["id_nivel_competencia"] . "' ");
									$dataNivel = mysqli_fetch_array($queryNivel);

									$text_nivel = '';
									foreach ($arrayNiveles as &$tipo) {
										if ($dataNivel["id_nivel"] == $tipo["0"]) {
											$text_nivel = $tipo["1"];
										}
									}

									echo '
									<tr>
										<th scope="row">' . $count . '</th>
										<td>' . $_SESSION['anio_ciclo'] . '</td>
										<td>' . eliminar_tildes($text_tipo) . '</td>
										<td>' . eliminar_tildes($dataComp["nombre"]) . '</td>
										<td>' . eliminar_tildes($text_nivel) . '</td>
										<td>' . eliminar_tildes($data["indicador"]) . '</td>
										<td>' . eliminar_tildes($data["fortaleza"]) . '</td>
										<td>' . eliminar_tildes($data["oportunidad"]) . '</td>
										<td align="center">
											<a href="?pg=competencias/competencias/informes/detalle&id='.$data["id"].'" type="button" class="btn btn-primary btn-sm">
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
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	var api = '<?php echo $url; ?>/api/competencias_pc/';

	function Ficha_Informe(id) {
		$('#lista_niveles').html('');
		jQuery.ajax({
				url: api + "ficha_informe_indicador.php",
				type: 'post',
				data: {
					id: id,
					url: "?pg=competencias"
				},
			}).done(function(resp) {
				$("#cont_modal_competencia").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});
	}

	function Seter_Ficha() {

		$('[name="nombre"]').val("");
		$('[name="definicion"]').val("");
		$('[name="id_tipo"]').val("");

		$('[name="id_competencia"]').val("");

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
		$('#competenciasInformes').DataTable({
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