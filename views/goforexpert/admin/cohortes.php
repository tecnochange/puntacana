<script>
	$(document).ready(function() {
		$('#menuAcademia').collapse();
		$("#bt_goforexpert_cohortes").addClass("active");
	});
</script>

<div class="container">
	<div align="left" style="padding: 10px 0px;">
		<table width="100%">
			<tr>
				<td>
					<h3 style="margin-top: 8px;">Cohortes (estudiantes)</h3>
				</td>
				<td align="right">
					<input class="form-control" type="text" placeholder="Búsqueda rápida..." id="buscador" style="width: 200px; display: inline-table;" />
				</td>
			</tr>
		</table>
	</div>


	<div class="table-responsive">
		<table class="table table-sm">
			<thead class="thead-dark">
				<tr>
					<th scope="col">#</th>
					<th scope="col">Programa</th>
					<th scope="col">Curso</th>
					<th scope="col">Estudiantes</th>
					<th scope="col" style="width: 200px"></th>
				</tr>
			</thead>

			<tbody id="tabla_lista">
				<?php
				$count = 1;

				$btn_edit = '';
				$query = mysqli_query($connect_academia, "SELECT * FROM Cursos ");
				while ($data = mysqli_fetch_array($query)) {

					$queryPrograma = mysqli_query($connect_academia, "SELECT * FROM Programas WHERE id = '" . $data["id_programa"] . "' ");
					$dataPrograma = mysqli_fetch_array($queryPrograma);

					$queryEstudiantes = mysqli_query($connect_academia, "SELECT * FROM Estudiantes_Cursos WHERE id_curso = '" . $data["id"] . "' ");
					while ($dataEstudiantes = mysqli_fetch_array($queryEstudiantes)) {
						$queryEmpledo = mysqli_query($connect_academia, "SELECT * FROM Programas WHERE id = '" . $data["id_programa"] . "' ");
					}

					
						$btn_edit = '<a href="' . $url_admin . '?pg=goforexpert/admin/cohortes/detalle&id=' . $data["id"] . '">
									<button type="button" class="btn btn-success" style="margin-bottom: 7px" title="editar">
										Asignar estudiantes
									</button>
								</a>';
				

					echo '
						<tr>
							<th scope="row">' . $count . '</th>
							<td>' . $dataPrograma["nombre"] . '</td>
							<td>' . $data["nombre"] . '</td>
							<td>' . $queryEstudiantes->num_rows . '</td>
							<td> ' . $btn_edit . '
								
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