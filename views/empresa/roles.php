<script>
	$(document).ready(function() {
		$('#menuEstructura').collapse();
		$("#bt_roles").addClass("active");
	});
</script>


<div class="container">

	<div class="d-flex justify-content-between ">
		<div class="mb-1">
			<h2>Gestionar Roles</h2>
		</div>

		<!-- <div class="mb-1" align="right">
			<input class="form-control" type="text" placeholder="Búsqueda rápida..." id="buscador" />
		</div> -->

		<div class="mb-1" align="right">
			<?php if ($VALIDAR_ROOT["crear"]) { ?>
				<a href="<?php echo $url; ?>?pg=empresa/role/detalle">
					<button type="button" class="btn btn-primary btn-sm" style="margin-bottom: 6px">Nuevo</button>
				</a>
			<?php } ?>



			<!-- <a href="https://talentoicesi.hr-suite.app/informes/reporte_areas.php" target="_blank">
				<button type="button" class="btn btn-warning btn-sm" style="margin-bottom: 6px">Exportar</button>
			</a> -->
		</div>
	</div>

	<div class="table-responsive">
        <div class="table-responsive">
		<table class="table">
			<thead class="table-dark">
				<tr>
					<th scope="col">Código</th>
					<th scope="col">Nombre</th>
					<th scope="col">Fecha</th>
					<th scope="col">Estado</th>
					<th scope="col">Acciones</th>
				</tr>
			</thead>
			<tbody id="tabla_lista">
				<?php
				$count = 1;
				$roles = mysqli_query($connect_admin, "SELECT * FROM Roles");

				foreach ($roles as $rol) {

					$txt_estado = "";
					foreach ($Array_Estado as $estado) {
						if ($rol["estado"] == $estado[0]) {
							$txt_estado = $estado[1];
							break;
						}
					}

					if ($VALIDAR_ROOT["editar"]) {

						$bt_edit = '
						<button type="button" class="btn btn-success btn-sm">
							Editar
						</button>
					';
					}

					echo '
					<tr>
					<td>' . $rol["id"] . '</td>
					<td>' . $rol["nombre"] . '</td>
					<td>' . $rol["created_at"] . '</td>
					<td>' . $txt_estado . '</td>
						
						<td>
							<a href="' . $url . '?pg=empresa/role/detalle&id=' . $rol["id"] . '">
							' . $bt_edit . '
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


<script>
	$(document).ready(function() {
		$("#buscador").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$("#tabla_lista tr").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		});
	});
</script>