<script>
	$(document).ready(function() {
		$('#menuCompetencias').collapse();
		$('#bt_competencias_arbol').addClass('active');
	});
</script>

<?php
include("app/models/competencias/Competencias.php");
$ClassCompetencias = new Competencias();
$dataCicloVal = $ClassCompetencias->Ciclo($user_log["id_empresa"], $_SESSION["anio_ciclo"]);
$evaluadores = $ClassCompetencias->Evaluadores($user_log["id_empresa"], $_SESSION["anio_ciclo"], $_SESSION["ciclo"]);
?>

<style>
	/* Evita saltos de línea en los titulos de las columnas */
	#arbol thead th {
		white-space: nowrap;
	}
	/* Margen debajo de la barra de herramientas (botones) */
	.dt-buttons {
		margin-bottom: 15px !important;
	}
	/* Margen debajo de la tabla (paginación) */
	.dataTables_paginate,
	.dataTables_info {
		margin-top: 15px !important;
	}
</style>

<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">

	<?php echo $respuesta; ?>

	<!-- TITULO -->
	<div class="card mb-3">
		<div class="card-header">
			<h3>Arbol Valoración <?= $dataCicloVal["anio"]; ?> | <small>Ciclo: <?php echo $dataCicloVal["nombre"]; ?></small> </h3>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<div class="table-responsive">
						<table class="table" id="arbol">
							<thead>
								<tr>
									<th scope="col" width="15">Documento</th>
									<th scope="col">Nombres</th>
									<th scope="col">Cargo</th>
									<th scope="col">Área</th>
									<th></th>
									<th scope="col">Evaluadores</th>
									<th scope="col">Cargo Evaluador</th>
									<th scope="col">Area Evaluador</th>
									<th scope="col">Tipo</th>
									<th scope="col">Acción</th>
								</tr>
							</thead>
							<tbody>
								<?php $count = 1; ?>
								<?php foreach ($evaluadores as $colaborador): ?>

									<tr>
										<td><?= $colaborador["empleado"]["documento"]; ?></td>
										<td><?= $colaborador["empleado"]["nombre"]; ?></td>
										<td><?= $colaborador["empleado"]["nombre_cargo"]; ?></td>
										<td><?= $colaborador["empleado"]["nombre_area"]; ?></td>
										<td>
											<a href="?pg=competencias/arbol/agregar&id=<?= $colaborador["empleado"]["id"]; ?>" class="btn btn-primary btn-sm btn-agregar">
												<i class="bx bx-plus" title="Asignar Evaluador"></i>
											</a>
										</td>
										<td><?= $colaborador["evaluador"]["nombre"]; ?></td>
										<td><?= $colaborador["evaluador"]["nombre_cargo"]; ?></td>
										<td><?= $colaborador["evaluador"]["nombre_area"]; ?></td>
										<td><?= $colaborador["txt_tipo"]; ?></td>
										<td>
											<button class="btn btn-danger btn-sm btn-eliminar" data-id="<?= $colaborador['id']; ?>" style="display:none">
												<i class="bx bx-trash" title="Eliminar"></i>
											</button>
										</td>
									</tr>

								<?php $count++;
								endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
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
		$('#arbol').DataTable({
			pageLength: 50,
			order: [
				[0, 'asc']
			],
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

<script>
	$(document).on('click', '.btn-eliminar', function() {

		const id = $(this).data('id');
		const fila = $(this).closest('tr');

		if (!confirm('¿Seguro que deseas eliminar este registro?')) {
			return;
		}

		$.ajax({
			url: 'api/competencias/eliminar_evaluador.php',
			type: 'POST',
			data: {
				id: id
			},
			success: function(response) {

				if (response.trim() === 'ok') {
					// eliminar fila visualmente
					$('#arbol').DataTable().row(fila).remove().draw();
				} else {
					alert('Error al eliminar');
					console.error(response);
				}
			},
			error: function() {
				alert('Error de conexión');
			}
		});
	});
</script>