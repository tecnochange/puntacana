<script>
	$(document).ready(function() {
		$(".menu_section").addClass("active");
		$("#nav_competencias").addClass("active");
		jQuery("#menu_competencias").css("display", "none");
		$("#bt_comp_realizar_competencias_pc").addClass("current-page");
	});
</script>

<?php
// print_r($_SESSION);
$queryCicloVal = mysqli_query($connect_competencias_pc, "SELECT * FROM Ciclos WHERE id = '" . $_SESSION['ciclo'] . "' ");
$dataCicloVal = mysqli_fetch_array($queryCicloVal);

$ciclo_cerrado = false;
if ($dataCicloVal["fecha_termina"] < date("Y-m-d")) {
	$ciclo_cerrado = true;
}

$queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '" . $_SESSION['id_user'] . "'");
$dataEmpleado = mysqli_fetch_array($queryEmpleado);

$queryCargo = mysqli_query($connect_valentina, "SELECT * FROM Cargos WHERE id = '" . $dataEmpleado['id_cargo'] . "'");
$dataCargo = mysqli_fetch_array($queryCargo);

$queryArea = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = '" . $dataEmpleado['area'] . "'");
$dataArea = mysqli_fetch_array($queryArea);

$queryEvaluador = mysqli_query($connect_competencias_pc, "SELECT * FROM Evaluadores
			WHERE id_evaluador = '" . $_SESSION['id_user'] . "' AND id_empleado = '" . $_SESSION['id_user'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' ");
$dataEvaluador = mysqli_fetch_array($queryEvaluador);

if (mysqli_num_rows($queryEvaluador) == 0) {
	$queryEvaluadorJ = mysqli_query($connect_competencias_pc, "SELECT * FROM Evaluadores
	WHERE id_evaluador != '" . $_SESSION['id_user'] . "' AND id_empleado = '" . $_SESSION['id_user'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' ");
	$dataEvaluadorJ = mysqli_fetch_array($queryEvaluadorJ);

	if ($dataEvaluadorJ["tipo"] == 5) { ?>
		<script>
			$(document).ready(function() {
				$('#modal_valoracion_auto').modal('toggle')
			});
		</script>

<?php }
}


$queryEval1 = mysqli_query($connect_competencias_pc, "SELECT * FROM Competencias_Evaluaciones_New WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND
				id_ciclo = '" . $_SESSION['ciclo'] . "' AND
				id_evaluado = '" . $_SESSION['id_user'] . "' AND
				id_evaluador = '" . $_SESSION['id_user'] . "' AND
				anio = '" . $_SESSION["anio_ciclo"] . "' AND
				tipo_evaluacion = '" . $dataEvaluador["tipo"] . "' ");
// $dataEval1 = mysqli_fetch_array($queryEval1);
$update_at1 = "";
if ($queryEval1->num_rows > 0) {
	$dataEval1 = mysqli_fetch_array($queryEval1);

	if ($dataEval1["estado"] == 1 || $dataEvaluador["tipo"] == 1) {
		$txt_estado1 = 'En Proceso';
		$bt_editar1 = '
<a href="' . $url . '?pg=competencias_pc/evaluacion&id=' . $dataEval1["id"] . '&pos=1">
<button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
<i class="fas fa-edit"></i>
</button>
</a>';
	}

	if ($dataEval1["estado"] == 2) {
		$txt_estado1 = 'Terminada';
		$update_at1 = $dataEval1["update_at"];
		$bt_editar1 = '';
	}
} else if ($dataEvaluador["tipo"] == 1) {
	$txt_estado1 = 'Pendiente';
	$bt_editar1 = '
<a href="' . $url . '?pg=competencias_pc/evaluacion&evaluado=' . $_SESSION['id_user'] . '&t=1&pos=1">
<button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
<i class="fas fa-edit"></i>
</button>
</a>';
}

if ($ciclo_cerrado == true) {
	$bt_editar1 = 'Ciclo Finalizado';
}
include("views/competencias_pc/modal_competencias.php");
$querySM11 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 1 AND id_submenu = 1");
$dataSM11 = mysqli_fetch_array($querySM11);

$queryCicloVal = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id = '" . $_SESSION['ciclo'] . "' ");
$dataCicloVal = mysqli_fetch_array($queryCicloVal);
?>

<?php echo $respuesta; ?>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header" style="background-color: #FFFFFF !important;">
				<div class="row">
					<div class="col-md-12" style="text-align: start !important;">
						<h4><i class="fas fa-users" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM11["nombre"]; ?> <?php echo $_SESSION["anio_ciclo"]; ?></b> Ciclo: <b><?php echo $dataCicloVal["nombre"]; ?></b></h4>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br>
<div class="container-fluid">

	<div class="row">
		<div class="col-md-12">
			<div align="left" style="padding: 5px 0px;">
				<h2 style="margin-top: 8px;"></i><?php echo $IDIOMA["valoracion_valoraciones_para"]; ?> <b><?php echo $dataCicloVal["nombre"]; ?></b> <?php echo $IDIOMA["valoracion_del_anio"]; ?> <b><?php echo $dataCicloVal["anio"]; ?></b></h2>
				<?php if ($ciclo_cerrado == true) { ?>
					<div class="alert alert-success" role="alert" align="center">
						<?php echo $IDIOMA["valoracion_este_ciclo_ha_finalizado"]; ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
	<br>



	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header" style="background-color: #3aae2a !important;">
					<div class="row">
						<div class="col-md-12" style="text-align: center !important;">
							<h3 style="color: #FFFFFF !important;">Mi Valoración</h3>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-3">
							Nombre:<br>
							<?php echo $dataEmpleado["nombre"]; ?>
						</div>
						<div class="col-md-2">
							Cargo:<br>
							<?php echo $dataCargo["nombre"]; ?>
						</div>
						<div class="col-md-2">
							Área:<br>
							<?php echo $dataArea["nombre"]; ?>
						</div>
						<div class="col-md-1">
							Rol Valoración:<br>
							<?php
							foreach ($array_Tipo_Colaborador as $tipo) {
								if ($tipo[0] == $dataEvaluador["tipo"]) {
									echo $tipo[1];
								}
							}
							?>
						</div>
						<div class="col-md-2">
							Fecha Realización:<br>
							<?php echo $update_at1; ?>
						</div>
						<div class="col-md-1">
							Estado:<br>
							<?php echo $txt_estado1; ?>
						</div>
						<div class="col-md-1">
							Acción:<br>
							<?php echo $bt_editar1; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php if ($_SESSION["role_plataforma"] == 2 || $_SESSION["role_plataforma"] == 1) { ?>

		<div class="row">
			<div class="col-md-12">
				<div class="table-responsive">
					<div class="card">
						<div class="card-header" style="background-color: #007bff !important;">
							<div class="row">
								<div class="col-md-12" style="text-align: center !important;">
									<h3 style="color: #FFFFFF !important;">Valoración de mis colaboradores</h3>
								</div>
							</div>
						</div>
						<div class="card-body">
							<table border="1" id="competencias_pc" class="display table" style="width:100%;">
								<thead class="table-dark">
									<tr>
										<th scope="col" style="width:50px">#</th>
										<th scope="col">Persona Valorada</th>
										<th scope="col">Cargo</th>
										<th scope="col">Area</th>
										<th scope="col">Autovaloración</th>
										<th scope="col">Estado Autovaloración</th>
										<th scope="col">Fecha Autovaloración</th>
										<th scope="col">Rol Valoración</th>
										<th scope="col">Fecha Realización Valoración</th>
										<th scope="col">Estado</th>
										<th scope="col">Acciones</th>
									</tr>
								</thead>

								<tbody>
									<?php
									$count = 1;

									$lista_evaluadores = '';

									$queryEvaluadores = mysqli_query($connect_competencias_pc, "SELECT EV.* FROM Evaluadores EV
								INNER JOIN goforagile_admin.Empleados E ON E.id = EV.id_empleado
								WHERE EV.id_evaluador = '" . $_SESSION['id_user'] . "' AND EV.anio = '" . $_SESSION['anio_ciclo'] . "' AND EV.id_ciclo = '" . $_SESSION['ciclo'] . "' AND EV.id_empleado != '" . $_SESSION['id_user'] . "' ORDER BY E.nombre ASC");
									while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) {

										$sentencia = "SELECT
				Empleados.id AS id, Empleados.id_empresa AS id_empresa, Empleados.nombre AS nombre, Empleados.correo AS correo_corporativo, Empleados.correo_personal AS correo_personal, Empleados.telefono_movil AS celular, Empleados.documento AS documento, Empleados.role AS role, Empleados.estado AS id_estado,
				Empleados.foto AS foto,Cargos.id AS id_cargo, Cargos.nombre AS nombre_cargo, Areas.nombre AS nombre_area, Areas.id AS id_area
				FROM Empleados
				LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
				LEFT JOIN Areas ON Areas.id = Empleados.area
				WHERE Empleados.id = " . $dataEvaluadores["id_empleado"] . " AND Empleados.id_empresa = '" . $_SESSION["id_empresa"] . "'
				ORDER BY Empleados.nombre ASC
				";

										$queryColaborador = mysqli_query($connect_valentina, $sentencia);
										$colaborador = mysqli_fetch_array($queryColaborador);

										$tipo_txt = '';
										foreach ($array_Tipo_Colaborador as $tipo) {
											if ($tipo[0] == $dataEvaluadores["tipo"]) {
												$tipo_txt =  $tipo[1];
											}
										}
										$txt_auto = 'No';
										$txt_estado_Auto = '';
										$txt_estado = 'Pendiente';
										$procesar = 0;
										$update_at = $update_at_auto = 'N/A';

										$queryAuto = mysqli_query($connect_competencias_pc, "SELECT * FROM Evaluadores
									WHERE anio = '" . $_SESSION['anio_ciclo'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "'
									AND id_empleado = '" . $dataEvaluadores["id_empleado"] . "' AND tipo = 1 ");

										if (mysqli_num_rows($queryAuto) > 0) {

											$txt_auto = 'Si';
											$queryEval1 = mysqli_query($connect_competencias_pc, "SELECT * FROM Competencias_Evaluaciones_New
											WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'
											AND id_ciclo = '" . $_SESSION['ciclo'] . "'
											AND id_evaluado = '" . $dataEvaluadores["id_empleado"] . "'
											AND id_evaluador = '" . $dataEvaluadores["id_empleado"] . "'
											AND anio = '" . $_SESSION["anio_ciclo"] . "'");
											if (mysqli_num_rows($queryEval1) > 0) {
												$dataEval1 = mysqli_fetch_array($queryEval1);
												if ($dataEval1["estado"] == 1) {
													$txt_estado_Auto = 'En Proceso de Autovaloración';

													$bt_editar = '';
												}
												if ($dataEval1["estado"] == 2 || $dataEval1["estado"] == 3) {
													$txt_estado_Auto = 'Autovaloración terminada';
													$update_at_auto = $dataEval1["update_at"];
													$bt_editar = '
				<a href="' . $url . '?pg=competencias_pc/evaluacion&evaluado=' . $dataEvaluadores["id_empleado"] . '&t=' . $dataEvaluadores["tipo"] . '&cargo=' . $dataEm["id_cargo"] . '">
					<button type="button" class="btn btn-primary btn-sm" title="Editar">
						<i class="fas fa-edit"></i>
					</button>
				</a>';
													$procesar = 1;
												}
											} else {
												$txt_estado_Auto = 'Pendiente de Autovaloración';
												$bt_editar = '';
											}
										} else {
											$procesar = 1;
											$bt_editar = '
				<a href="' . $url . '?pg=competencias_pc/evaluacion&evaluado=' . $dataEvaluadores["id_empleado"] . '&t=' . $dataEvaluadores["tipo"] . '&cargo=' . $dataEm["id_cargo"] . '">
					<button type="button" class="btn btn-primary btn-sm" title="Editar">
						<i class="fas fa-edit"></i>
					</button>
				</a>';
										}

										$queryEval = mysqli_query($connect_competencias_pc, "SELECT * FROM Competencias_Evaluaciones_New WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND
										id_ciclo = '" . $_SESSION['ciclo'] . "' AND
										id_evaluado = '" . $dataEvaluadores["id_empleado"] . "' AND
										id_evaluador = '" . $dataEvaluadores["id_evaluador"] . "' AND
										anio = '" . $_SESSION["anio_ciclo"] . "' AND
										tipo_evaluacion = '" . $dataEvaluadores["tipo"] . "' ");

										if ($procesar == 1) {
											if ($queryEval->num_rows > 0) {
												$dataEval = mysqli_fetch_array($queryEval);

												if ($dataEval["estado"] == 1) {
													$txt_estado = 'En Proceso';
													$bt_editar = '
							<a href="' . $url . '?pg=competencias_pc/evaluacion&id=' . $dataEval["id"] . '&pos=1">
								<button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
									<i class="fas fa-edit"></i>
								</button>
							</a>';
												}

												if ($dataEval["estado"] == 2 || $dataEval["estado"] == 3) {
													$txt_estado = 'Terminada';
													$update_at = $dataEval["update_at"];
													$bt_editar = '';
													$ciclo_cerrado == true;
												}
											}
										}

										if ($ciclo_cerrado == true) {
											$bt_editar = 'Ciclo Finalizado';
										}

										echo '
					<tr>
						<td title="' . $dataEval["id"] . '">' . $count . '</td>
						<td>' . $colaborador["nombre"] . '</td>
						<td>' . $colaborador["nombre_cargo"] . '</td>
						<td>' . $colaborador["nombre_area"] . '</td>
						<td>' . $txt_auto . '</td>
						<td>' . $txt_estado_Auto . '</td>
						<td>' . $update_at_auto . '</td>
						<td>' . $tipo_txt . '</td>
						<td>' . $update_at . '</td>
						<td align="center">' . $txt_estado . '</td>
						<td align="center">' . $bt_editar . '</td>



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
	<?php } ?>
</div>

<style>
	.checkbox_list {
		width: 18px;
		height: 18px;
	}
</style>

<script type="text/javascript">
	$(document).ready(function() {
		$('#competencias_pc').DataTable({
			pageLength: 100,
			language: {
				processing: "Procesando...",
				search: "Buscar:",
				lengthMenu: "Mostrar _MENU_ registros.",
				info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
				infoEmpty: "Mostrando registros del 0 al 0 de 0 registros",
				infoFiltered: "(filtrado de un total de _MAX_ registros)",
				infoPostFix: "",
				loadingRecords: "Cargando...",
				zeroRecords: "No se encontraron resultados",
				emptyTable: "No se tienen programadas evaluaciones",
				row: "Registro",
				export: "Exportar",
				paginate: {
					first: "Primero",
					previous: "Anterior",
					next: "Siguiente",
					last: "Ultimo"
				},
				aria: {
					sortAscending: ": Activar para ordenar la columna de manera ascendente",
					sortDescending: ": Activar para ordenar la columna de manera descendente"
				},
				select: {
					row: "registro",
					selected: "seleccionado"
				}
			},
			dom: 'Bfrtip',
			buttons: [{
					extend: 'collection',
					text: 'Exportar',
					buttons: [
						'copy',
						'excel',
						'csv',
						{
							extend: 'pdfHtml5',
							text: 'PDF',
							orientation: 'landscape',
							pageSize: 'LEGAL'
						},
						{
							extend: 'print',
							customize: function(win) {
								$(win.document.body)
									.css('font-size', '10pt');

								$(win.document.body).find('table')
									.addClass('compact')
									.css('font-size', 'inherit');
							}
						}
					]
				}

			]
		});
	});
</script>