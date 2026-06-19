<script>
	$(document).ready(function() {
		$('#menuAcademia').collapse();
		$("#bt_goforexpert_programas").addClass("active");
	});
</script>
<div class="container-fluid">

	<div align="left" style="padding: 10px 0px;">
		<table width="100%">
			<tr>
				<td>
					<h3 style="margin-top: 8px;"><i class="bx bx-check"></i> PROGRAMAS</h3>
				</td>
				<td align="right">
					<input class="form-control" type="text" placeholder="Búsqueda rápida..." id="buscador" style="width: 200px; display: inline;" />

					

						<a href="<?php echo $url_admin; ?>?pg=goforexpert/admin/programa/detalle">
							<button type="button" class="btn btn-warning" style="margin-bottom: 7px">
								<i class="bx bx-plus btn-left"></i> Crear Programa
							</button>
						</a>

					

				</td>
			</tr>
		</table>
	</div>


	<div class="table-responsive">
        <div class="table-responsive">
		<table class="table table-bordered">
			<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Imagen</th>
					<th scope="col">Programa</th>
					<th scope="col" style="width: 80px"></th>
				</tr>
			</thead>

			<tbody id="tabla_lista">
				<?php
				$count = 1;

				$btn_editar = '';
				if ($inicia > 1) {
					$count = ($posicion - 1) * $limite;
				}
				$query = mysqli_query($connect_academia, "SELECT * FROM Programas WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' ");
				while ($data = mysqli_fetch_array($query)) {

					

						$btn_editar = '<a href="' . $url_admin . '?pg=goforexpert/admin/programa/detalle&id=' . $data["id"] . '">
									<button type="button" class="btn btn-success" style="margin-bottom: 7px" title="editar">
										<i class="bx bx-edit"></i> 
									</button>
									</a>';
					

					echo '
								<tr>
								  <th scope="row">' . $count . '</th>
								  <td><img src="' . $url . '/recursos/' . $data["imagen"] . '" width="70"></td>
								  <td>' . $data["nombre"] . '</td>
								  <td>' . $btn_editar . '</td>
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

<script src="js/functions.js"></script>