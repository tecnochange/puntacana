<script>
	$(document).ready(function() {
		$('#menuEstructura').collapse();
		$("#bt_roles").addClass("active");
	});
</script>

<?php

$id = $_GET["id"];
$hoy = date("Y-m-d H:i:s");

function Validar_Check($REQUEST, $txt_input, $id_ruta, $DATOS, $id)
{

	//SE ENCUENTRA CHECKEADO
	//SE ENCUENTRA CHECKEADO
	//SE ENCUENTRA CHECKEADO
	if ($REQUEST[$txt_input . $id_ruta]) {
		$posicion = 0;
		$exite = false;
		foreach ($DATOS as $key_i => $role) {
			if ($id == $role) {
				$exite = true;
				$posicion = $key_i;
			}
			if (!$role) {
				unset($DATOS[$key_i]);
			}
		}

		if ($exite) {
		} else {
			array_push($DATOS, $id);
		}
	}

	//NO SE ENCUENTRA CHECKEADO
	//NO SE ENCUENTRA CHECKEADO
	//NO SE ENCUENTRA CHECKEADO
	else {
		$posicion = 0;
		$exite = false;
		foreach ($DATOS as $key_i => $role) {
			if ($id == $role) {
				$exite = true;
				$posicion = $key_i;
			}
		}
		if ($exite) {
			unset($DATOS[$posicion]);
		}
	}

	return $DATOS;
}

//PARA GUARDAR DATOS DEL FORAULARIO
//PARA GUARDAR DATOS DEL FORAULARIO
//PARA GUARDAR DATOS DEL FORAULARIO
if ($_POST["guardar_formaulario"] != "") {

	if ($_POST["id_registro"]) {
		$sentencia = "
			UPDATE
				Roles
			SET
				nombre = '" . $_POST["nombre"] . "',
				estado = '" . $_POST["estado"] . "'
			WHERE
				id = '" . $id . "'
			";
		mysqli_query($connect_valentina, $sentencia);
	} else {
		$sentencia = "
			INSERT INTO Roles(
				id_empresa,
				nombre,
				estado,
				created_at
			)
			VALUES(
				1,
				'" . $_POST["nombre"] . "',
				'" . $_POST["estado"] . "',
				'" . $hoy . "'
			)
			";
		mysqli_query($connect_valentina, $sentencia);

		echo '<script> window.location = "?pg=estructura/roles";</script>';
	}
}


//PERMISOS
//PERMISOS
//PERMISOS
if ($_POST["guardar_permisos"] != "") {

	//RECORREMOS LAS RUTAS SELECCIONADAS
	foreach ($_POST["id_ruta"] as $key => $id_ruta) {

		$queryRoles = mysqli_query($connect_valentina, "SELECT * FROM Rutas WHERE id = '" . $id_ruta . "' ");
		$dataRoles = mysqli_fetch_array($queryRoles);

		$ROLES_PARTES = explode(",", $dataRoles["roles"]);
		$ROLES = Validar_Check($_POST, "id_role_", $id_ruta, $ROLES_PARTES, $id);

		$EDITAR_PARTES = explode(",", $dataRoles["editar"]);
		$EDITAR = Validar_Check($_POST, "id_edit_", $id_ruta, $EDITAR_PARTES, $id);

		$CREAR_PARTES = explode(",", $dataRoles["crear"]);
		$CREAR = Validar_Check($_POST, "id_crear_", $id_ruta, $EDITAR_PARTES, $id);

		$ELIMINAR_PARTES = explode(",", $dataRoles["eliminar"]);
		$ELIMINAR = Validar_Check($_POST, "id_eliminar_", $id_ruta, $ELIMINAR_PARTES, $id);

		$EXPORTAR_PARTES = explode(",", $dataRoles["exportar"]);
		$EXPORTAR = Validar_Check($_POST, "id_exportar_", $id_ruta, $EXPORTAR_PARTES, $id);

		mysqli_query($connect_valentina, "UPDATE Rutas SET roles = '" . implode(",", $ROLES) . "', 
		editar = '" . implode(",", $EDITAR) . "', crear = '" . implode(",", $CREAR) . "', 
		eliminar = '" . implode(",", $ELIMINAR) . "' , exportar = '" . implode(",", $EXPORTAR) . "'  
		WHERE id = '" . $id_ruta . "' ");
	}

	//RECORREMOS LOS MENUS SELECCIONADOS
	foreach ($_POST["id_menu"] as $key => $id_menu) {
		$queryMenus = mysqli_query($connect_valentina, "SELECT * FROM Menus WHERE id = '" . $id_menu . "' ");
		$dataMenus = mysqli_fetch_array($queryMenus);

		$ROLES_PARTES = explode(",", $dataMenus["roles"]);
		$ROLES = Validar_Check($_POST, "id_role_menu_", $id_menu, $ROLES_PARTES, $id);

		mysqli_query($connect_valentina, "UPDATE Menus SET roles = '" . implode(",", $ROLES) . "'  	  
		WHERE id = '" . $id_menu . "' ");
	}
}

//INFORMACION DE LA BATERIA
$query = mysqli_query($connect_valentina, "SELECT * FROM Roles WHERE id = '" . $id . "' ");
$data = mysqli_fetch_array($query);
?>

<style>
	.menu_checkbox {
		width: 20px;
		height: 20px;
	}
</style>

<style>
	.menu_checkbox_generales {
		width: 20px;
		height: 20px;
	}
</style>

<div class="container-fluid">
	<nav aria-label="breadcrumb">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?php echo $url; ?>?pg=estructura/roles">Roles</a></li>
			<li class="breadcrumb-item active" aria-current="page"><a href="">Detalle</a></li>
		</ol>
	</nav>

	<div class="card" style="margin-bottom: 20px">

		<div class="card-body">
			<form action="" method="post">
				<div class="row">

					<div class="col-md-12">
						<h2>Detalle de Roles</h2>
						<input type="hidden" name="guardar_formaulario" value="true">
						<input type="hidden" name="id_registro" value="<?php echo $id; ?>">
					</div>

					<div class="col-md-8" style="margin-bottom: 10px">
						<lable>Nombre</lable>
						<input type="text" class="form-control" name="nombre" value="<?php echo $data["nombre"]; ?>">

					</div>

					<div class="col-md-4" style="margin-bottom: 10px">
						<label>Estado *</label>
						<select class="form-control" name="estado" required>
							<option value="">Selecciona...</option>
							<?php
							foreach ($Array_Estado as $role) {
								if ($role[0] == $data["estado"]) {
									echo '<option value="' . $role[0] . '" selected>' . $role[1] . '</option>';
								} else {
									echo '<option value="' . $role[0] . '">' . $role[1] . '</option>';
								}
							}
							?>
						</select>
					</div>

					<div class="col-md-12" style="margin-bottom: 10px">
						<button type="submit" id="sidebarCollapse" class="btn btn-success btn-block btn-sm">
							<i class="fas fa-check"></i> Guardar
						</button>
					</div>

				</div>
			</form>
		</div>
	</div>

	<form action="" method="post">
		<div class="row">

			<div class="col-md-4">
				<div class="card">

					<div class="card-header">
						<h2>
							Menú lateral
							<input class="menu_checkbox_generales" type="checkbox" style="float: right" onclick="SeleccionarTodosModulo(this)" data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="Seleccionar todos los menús" aria-label="Cerrar sesión">
						</h2>
					</div>

					<div class="card-body">

						<div class="table-responsive">
							<table class="table table-sm" style="font-size: 12px;">
								<tr>
									<th>Menú</th>
									<th>Módulo</th>
									<th>

									</th>
								</tr>

								<?php
								$queryModulo = mysqli_query($connect_valentina, "SELECT * FROM Modulos ORDER BY orden ASC ");
								while ($dataModulo = mysqli_fetch_array($queryModulo)) {

									echo '
									<tr>
										<th colspan="2"><h2>' . $dataModulo["nombre"] . '</h2></th>
										<th><input class="menu_checkbox_generales" type="checkbox" title="Todos en este menú" onclick="SeleccionarModulo(' . $dataModulo["id"] . ', this)"></th>
									</tr>
                                	';

									$queryMenu = mysqli_query($connect_valentina, "SELECT * FROM Menus 
                                	WHERE id_modulo = '" . $dataModulo["id"] . "' ");
									while ($dataMenu = mysqli_fetch_array($queryMenu)) {

										$checkbox_menu = "";


										$roles = explode(",", $dataMenu["roles"]);
										foreach ($roles as $r) {
											if ($r == $id) {
												$checkbox_menu = "checked";
											}
										}

										echo '
                                    <tr>	
                                        <td style="max-width:250px;" >' . $dataMenu["menu"] . ' </td>
										<td>' . $dataModulo["nombre"] . '</td>
                                        <td>
                                            <input type="hidden" name="id_menu[]" value="' . $dataMenu["id"] . '">
                                            <input type="checkbox" class="menu_checkbox check_' . $dataModulo["id"] . '" name="id_role_menu_' . $dataMenu["id"] . '" 
                                            value="' . $dataMenu["id"] . '" ' . $checkbox_menu . ' >
                                        </td>
                                    </tr>
                                    ';
									}
								}
								?>
							</table>

						</div>

					</div>

				</div>

			</div>
			<div class="col-md-8">



				<div class="card">

					<div class="card-header">
						<h2>
							Rutas y Permisos
							<input class="menu_checkbox_generales" type="checkbox" id="select_all" onclick="toggleCheckboxes()" style="float: right" data-bs-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title="Seleccionar todos las rutas" aria-label="Cerrar sesión">
						</h2>
						<input class="form-control btn-sm" type="text" placeholder="Búsqueda rápida..." id="buscador" style="margin-bottom: 6px; width:50%;">

					</div>


					<div class="card-body">

						<input type="hidden" name="guardar_permisos" value="true">


						<div class="table-responsive">

							<table class="table table-sm" style="font-size: 12px;">
								<tr>
									<th>Ruta</th>
									<th>Módulo</th>
									<th>Habilitar</th>
									<th>Editar</th>
									<th>Crear</th>
									<th>Eliminar</th>
									<th>Exportar</th>
								</tr>

								<tbody class="tabla_lista">

									<?php
									$queryModulo = mysqli_query($connect_valentina, "SELECT * FROM Modulos ORDER BY orden ASC ");
									while ($dataModulo = mysqli_fetch_array($queryModulo)) {

										echo '
										<tr>
											<th colspan="6">
												<h2 style="margin-left: 5px; margin-right:20px;">' . $dataModulo["nombre"] . '</h2>
											</th>
											<th >
												<input class="menu_checkbox_generales" type="checkbox" style="margin-top: -5px;" class="modulo_checkbox" data-modulo=' . $dataModulo["id"] . ' onclick="toggleCheckboxesModulo(this)">
											</th>
										</tr>
										';

										$queryRuta = mysqli_query($connect_valentina, "SELECT * FROM Rutas 
                                		WHERE modulo = '" . $dataModulo["id"] . "' ");
										while ($dataRuta = mysqli_fetch_array($queryRuta)) {

											$checkbox = "";
											$checkbox_edit = "";
											$checkbox_crear = "";
											$checkbox_eliminar = "";
											$checkbox_exportar = "";

											$roles = explode(",", $dataRuta["roles"]);
											foreach ($roles as $r) {
												if ($r == $id) {
													$checkbox = "checked";
												}
											}

											$editar = explode(",", $dataRuta["editar"]);
											foreach ($editar as $r) {
												if ($r == $id) {
													$checkbox_edit = "checked";
												}
											}

											$crear = explode(",", $dataRuta["crear"]);
											foreach ($crear as $r) {
												if ($r == $id) {
													$checkbox_crear = "checked";
												}
											}

											$eliminar = explode(",", $dataRuta["eliminar"]);
											foreach ($eliminar as $r) {
												if ($r == $id) {
													$checkbox_eliminar = "checked";
												}
											}

											$exportar = explode(",", $dataRuta["exportar"]);
											foreach ($exportar as $r) {
												if ($r == $id) {
													$checkbox_exportar = "checked";
												}
											}

											echo '
											<tr>	
												<td>' . $dataRuta["ruta"] . '</td>
												<td>' . $dataModulo["nombre"] . '</td>
												<td>
													<input type="hidden" name="id_ruta[]" value="' . $dataRuta["id"] . '">
													<input type="checkbox" class="ruta_checkbox modulo_' . $dataModulo["id"] . '" 
													data-modulo="' . $dataModulo["id"] . '" name="id_role_' . $dataRuta["id"] . '" 
													value="' . $dataRuta["id"] . '" ' . $checkbox . ' >
												</td>
												<td>
													<input type="checkbox" class="ruta_checkbox modulo_' . $dataModulo["id"] . '" 
													data-modulo="' . $dataModulo["id"] . '" name="id_edit_' . $dataRuta["id"] . '" 
													value="' . $dataRuta["id"] . '" ' . $checkbox_edit . ' >
												</td>
												<td>
													<input type="checkbox" class="ruta_checkbox modulo_' . $dataModulo["id"] . '" 
													data-modulo="' . $dataModulo["id"] . '" name="id_crear_' . $dataRuta["id"] . '" 
													value="' . $dataRuta["id"] . '" ' . $checkbox_crear . ' >
												</td>
												<td>
													<input type="checkbox" class="ruta_checkbox modulo_' . $dataModulo["id"] . '" 
													data-modulo="' . $dataModulo["id"] . '" name="id_eliminar_' . $dataRuta["id"] . '" 
													value="' . $dataRuta["id"] . '" ' . $checkbox_eliminar . ' >
												</td>
												<td>
													<input type="checkbox" class="ruta_checkbox modulo_' . $dataModulo["id"] . '" 
													data-modulo="' . $dataModulo["id"] . '" name="id_exportar_' . $dataRuta["id"] . '" 
													value="' . $dataRuta["id"] . '" ' . $checkbox_exportar . ' >
												</td>
											</tr>
											';
										}
									}

									?>
								</tbody>

							</table>

						</div>

						<button class="btn btn-success w-100" type="submit">Actualizar Permisos</button>

					</div>
				</div>

			</div>
		</div>
	</form>
</div>

<script>
	function toggleCheckboxes() {
		var checkboxes = document.getElementsByClassName('ruta_checkbox');
		var selectAllCheckbox = document.getElementById('select_all');
		for (var i = 0; i < checkboxes.length; i++) {
			checkboxes[i].checked = selectAllCheckbox.checked;
		}
	}

	function toggleCheckboxesModulo(checkbox) {
		var checkboxes = document.querySelectorAll('[data-modulo="' + checkbox.dataset.modulo + '"].ruta_checkbox');
		for (var i = 0; i < checkboxes.length; i++) {
			checkboxes[i].checked = checkbox.checked;
		}
	}

	function SeleccionarModulo(id, element) {
		if ($(element).prop('checked') == true) {
			$(".check_" + id).prop('checked', true);
		} else {
			$(".check_" + id).prop('checked', false);
		}
	}

	function SeleccionarTodosModulo(element) {
		if ($(element).prop('checked') == true) {
			$(".menu_checkbox ").prop('checked', true);
		} else {
			$(".menu_checkbox").prop('checked', false);
		}
	}


	$(document).ready(function() {
		$("#buscador").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$(".tabla_lista tr").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		});
	});
</script>