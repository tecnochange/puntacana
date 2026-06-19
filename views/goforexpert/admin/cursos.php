<script>
	$(document).ready(function() {
		$('#menuAcademia').collapse();
		$("#bt_goforexpert_cursos").addClass("active");
	});
</script>

<div class="container-fluid">
	<div align="left" style="padding: 10px 0px;">
		<table width="100%">
			<tr>
				<td>
					<h3 style="margin-top: 8px;">Cursos</h3>
				</td>
				<td align="right">
					<input class="form-control" type="text" placeholder="Búsqueda rápida..." id="buscador" style="width: 200px; display: inline;" />

					

						<a href="<?php echo $url_admin; ?>?pg=goforexpert/admin/curso/detalle">
							<button type="button" class="btn btn-warning" style="margin-bottom: 7px">
								Crear Curso
							</button>
						</a>

					

				</td>
			</tr>
		</table>
	</div>


	<div class="table-responsive">
		<table class="table ">
			<thead>
				<tr>
					<th scope="col">#</th>
					<th scope="col">Curso</th>
					<th scope="col">Módulos</th>
					<th scope="col">Capítulos</th>
					<th scope="col">Inicia</th>
					<th scope="col">Termina</th>
					<th scope="col">Programa</th>
					<th scope="col" style="width: 110px">Contenidos</th>
					<th scope="col" style="width: 60px">Editar</th>
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

					$queryModulos = mysqli_query($connect_academia, "SELECT * FROM Cursos_Modulos WHERE id_curso = '" . $data["id"] . "' ");
					$queryCapitulos = mysqli_query($connect_academia, "SELECT * FROM Cursos_Capitulos WHERE id_curso = '" . $data["id"] . "' ");


					

						$btn_edit = '<a href="' . $url_admin . '?pg=goforexpert/admin/curso/detalle&id=' . $data["id"] . '">
								<button type="button" class="btn btn-success btn-sm" style="margin-bottom: 7px" title="editar">
									<i class="bx bx-edit"></i> 
								</button>
							</a>';
					


					echo '
					<tr>
						<th scope="row">' . $count . '</th>
						<td>' . $data["nombre"] . '</td>
						<td>' . $queryModulos->num_rows . '</td>
						<td>' . $queryCapitulos->num_rows . '</td>
						<td>' . $data["fecha_inicia"] . '</td>
						<td>' . $data["fecha_termina"] . '</td>
						<td>' . $dataPrograma["nombre"] . '</td>
						<td>
							<a href="' . $url_admin . '?pg=goforexpert/admin/curso/contenidos&id=' . $data["id"] . '">
								<button type="button" class="btn btn-warning btn-sm" style="margin-bottom: 7px" title="editar">
									Contenidos
								</button>
							</a>
						</td>
						<td>
						' . $btn_edit . '
							
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