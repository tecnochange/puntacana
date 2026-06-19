<script>
	// $("#bt_admin_loads").addClass("active_item");
	// $("#nav_empresa").addClass("menu-is-opening menu-open");
	$(".menu_section").addClass("active");
	// $("#nav_empresa").addClass("active");
	jQuery("#menu_empresa").css("display", "none");
	$("#bt_admin_loads").addClass("current-page");
</script>

<?php
$hoy = date("Y:m:d H:i:s");

if ($_POST["base_parse"] != "") {

	$count = 1;
	$filas = json_decode($_POST["base_parse"]);
	$filas = array_slice($filas, 1);

	$total = 0;
	foreach ($filas as $fila) {

		$id_uo = $id_vp = $id_cargo = $id_nj = '';
		$id_area = 'SIN AREA';
		$anios = $meses = $diferencia_dias = $dias = 0;

		if ($count >= 1) {

			if ($fila[0]) {

				$nombre = trim($fila[1]);
				$nombreColaborador = mysqli_real_escape_string($connect_valentina, $nombre);

				if ($fila[2] != "error") {
					$genero = trim($fila[2]);
				} else {
					$genero = "";
				}

				if ($fila[3] != "error") {
					// echo $fila[3] . "<br>";
					$fecha_ingresada = trim($fila[3]);

					// Validar que la fecha esté en el formato correcto
					if (DateTime::createFromFormat('Y-m-d', $fecha_ingresada) !== false) {
						$fecha_formateada = $fecha_ingresada;
					} else {
						$fecha_formateada = date('Y-m-d');
					}

					$fecha_ingreso_dt = DateTime::createFromFormat('Y-m-d', $fecha_formateada);
					$fecha_actual_dt = new DateTime();

					$diferencia = $fecha_actual_dt->diff($fecha_ingreso_dt);

					$anios = $diferencia->y;
					$meses = $diferencia->m;
					$dias = $diferencia->d;
				} else {
					$fecha_formateada = '';
					$anios = $meses = $dias = 0;
				}

				if ($fila[4] != "error") {
					$correo = trim($fila[4]);
				} else if ($fila[5] != "error") {
					$correo = trim($fila[5]);
					$correo_personal = trim($fila[5]);
				} else {
					$correo = $correo_personal = "";
				}

				if ($fila[6] != "error") {
					$telefono_movil = trim($fila[6]);
				} else {
					$telefono_movil = "";
				}

				if ($fila[7] != "error") {
					$telefono_fijo = trim($fila[7]);
				} else {
					$telefono_fijo = "";
				}

				if ($fila[8] != "error") {
					$nombreCompania = trim($fila[8]);
				} else {
					$nombreCompania = "";
				}

				if ($fila[9] != "error") {
					$vp = trim($fila[9]);
					$nombreVp = mysqli_real_escape_string($connect_valentina, $vp);
					$queryVP = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND nombre = '$nombreVp' AND estado = 1");
					if (mysqli_num_rows($queryVP) > 0) {
						$dataVP = mysqli_fetch_array($queryVP);
						$id_vp = $dataVP["id"];
						mysqli_query($connect_valentina, "UPDATE Vicepresidencia SET nombre = '$nombreVp', updated_at = '$hoy' WHERE id = '" . $id_vp . "'");
					} else {
						$queryVP = mysqli_query($connect_valentina, "INSERT INTO Vicepresidencia (id_empresa, nombre, estado, created_at) VALUES ('" . $_SESSION["id_empresa"] . "','$nombreVp',1,'$hoy')");
						$id_vp = mysqli_insert_id($connect_valentina);
					}
				} else {
					$vp = $nombreVp = "";
				}

				if ($fila[10] != "error") {
					$area = trim($fila[10]);
					$nombreArea = mysqli_real_escape_string($connect_valentina, $area);
					$queryArea = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND nombre = '$nombreArea' AND estado = 1");
					if (mysqli_num_rows($queryArea) > 0) {
						$dataArea = mysqli_fetch_array($queryArea);
						$id_area = $dataArea["id"];
						mysqli_query($connect_valentina, "UPDATE Areas SET nombre = '$nombreArea', updated_at = '$hoy' WHERE id = '" . $id_area . "'");
					} else {
						$queryArea = mysqli_query($connect_valentina, "INSERT INTO Areas (id_empresa, nombre, estado, created_at) VALUES ('" . $_SESSION["id_empresa"] . "','$nombreArea',1,'$hoy')");
						$id_area = mysqli_insert_id($connect_valentina);
					}
				} else {
					$area = $nombreArea = "";
				}

				if ($fila[11] != "error") {
					$unidadO = trim($fila[11]);
					$nombreUO = mysqli_real_escape_string($connect_valentina, $unidadO);
					$queryEE = mysqli_query($connect_valentina, "SELECT * FROM Estructura_Empresa WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND vicepresidencia = '$id_vp' AND area = '$id_area' AND unidad_organizativa LIKE '$nombreUO'");
					if (mysqli_num_rows($queryEE) > 0) {
						$dataEE = mysqli_fetch_array($queryEE);
						$id_uo = $dataEE["id"];
					} else {
						$queryEE = mysqli_query($connect_valentina, "INSERT INTO Estructura_Empresa (id_empresa, compania, vicepresidencia, area, unidad_organizativa, estado, created_at) VALUES ('" . $_SESSION["id_empresa"] . "','$nombreCompania','$id_vp' , '$id_area','$nombreUO',1,'$hoy')");
						$id_uo = mysqli_insert_id($connect_valentina);
					}
				} else {
					$unidadO = $nombreUO = "";
				}

				$queryEE1 = mysqli_query($connect_valentina, "SELECT * FROM Estructura_Empresa WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND vicepresidencia = '$id_vp' AND area = '$id_area'  AND estado = 1");
				if (mysqli_num_rows($queryEE1) == 0) {
					$queryEE2 = mysqli_query($connect_valentina, "INSERT INTO Estructura_Empresa (id_empresa, compania, vicepresidencia, area, estado, created_at) VALUES ('" . $_SESSION["id_empresa"] . "','$nombreCompania','$id_vp' , '$id_area',1,'$hoy')");
				}

				if ($fila[12] != "error") {
					$NJ = trim($fila[12]);
					$nombreNJ = mysqli_real_escape_string($connect_valentina, $NJ);
					$queryNJ = mysqli_query($connect_valentina, "SELECT * FROM Nivel_Jerarquico WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND nombre = '$nombreNJ' AND estado = 1");
					if (mysqli_num_rows($queryNJ) > 0) {
						$dataNJ = mysqli_fetch_array($queryNJ);
						$id_nj = $dataNJ["id"];
					} else {
						$queryNJ = mysqli_query($connect_valentina, "INSERT INTO Nivel_Jerarquico (id_empresa, nombre, estado, created_at) VALUES ('" . $_SESSION["id_empresa"] . "','$nombreNJ',1,'$hoy')");
						$id_nj = mysqli_insert_id($connect_valentina);
					}
				} else {
					$NJ = $nombreNJ = "";
				}
				if ($fila[13] != "error") {
					$nivel_general = trim($fila[13]);
				} else {
					$nivel_general = "";
				}

				if ($fila[14] != "error") {
					$cargo = trim($fila[14]);
					$nombreCargo = mysqli_real_escape_string($connect_valentina, $cargo);
					$queryCargo = mysqli_query($connect_valentina, "SELECT * FROM Cargos WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND nombre = '$nombreCargo' AND estado = 1 AND id_area = '$id_area'");
					if (mysqli_num_rows($queryCargo) > 0) {
						$dataCargo = mysqli_fetch_array($queryCargo);
						$id_cargo = $dataCargo["id"];
						mysqli_query($connect_valentina, "UPDATE Cargos SET nivel_jerarquico = '$nivel_general', nombre = '$nombreCargo', updated_at = '$hoy' WHERE id = '" . $id_cargo . "'");
					} else {
						$queryCargo = mysqli_query($connect_valentina, "INSERT INTO Cargos (id_empresa, nivel_jerarquico, nombre, estado, id_area, created_at) VALUES ('" . $_SESSION["id_empresa"] . "','$nivel_general','$nombreCargo',1,'$id_area','$hoy')");
						$id_cargo = mysqli_insert_id($connect_valentina);
					}
				} else {
					$cargo = $nombreCargo = "";
				}

				$rolPlataforma = trim($fila[15]);
				$estado = trim($fila[16]);
				$verificar = trim($fila[17]);
				$actualizar = "";
				// echo "SELECT * FROM Empleados WHERE documento = '" . $fila[0] . "' AND id_empresa = '" . $_SESSION["id_empresa"] . "' <br>";
				// echo "SELECT * FROM Empleados WHERE nombre = '" . $fila[1] . "' AND id_empresa = '" . $_SESSION["id_empresa"] . "' <br> ";
				$qA = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE documento = '" . $fila[0] . "' AND id_empresa = '" . $_SESSION["id_empresa"] . "' ");
				$qA1 = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE nombre = '" . $fila[1] . "' AND id_empresa = '" . $_SESSION["id_empresa"] . "' ");
				if ($qA->num_rows == 0) {
					if ($qA1->num_rows == 0) {
						$sentenciaInsert = "INSERT INTO Empleados ( id_empresa, documento, nombre, genero, fecha_ingreso, antiguedad_anios, antiguedad_meses, antiguedad_dias, correo, correo_personal, telefono_movil, telefono_fijo, compania, unidad_corporativa, area, unidad_organizativa, nivel_jerarquico, nivel_general,id_cargo, cargo, role, estado, verificar, password, contrasena, created_at)
							VALUES ('" . $_SESSION["id_empresa"] . "','" . $fila[0] . "', '$nombreColaborador', '$genero', '$fecha_formateada','$anios','$meses','$diferencia_dias','$correo', '$correo_personal', '$telefono_movil', '$telefono_fijo', '$nombreCompania', '$id_vp', '$id_area', '$id_uo', '$id_nj','$nivel_general', '$id_cargo', '$nombreCargo','$rolPlataforma', '$estado', '$verificar', '" . substr(trim($fila[0]), -4) . "',  '" . hash('sha512', substr(trim($fila[0]), -4)) . "','" . $hoy . "');
							";
						// echo $sentenciaInsert . "<br>";
						mysqli_query($connect_valentina, $sentenciaInsert);
					} else {
						$dataCol = mysqli_fetch_array($qA1);
						if ($dataCol["documento"] == $fila[0]) {
							// echo "1";
							$actualizar = "documento = '" . $dataCol["documento"] . "',";
						} else {
							// echo "2";
							$actualizar = "documento = '" . $fila[0] . "',";
						}
					}
				} else {
					$dataCol = mysqli_fetch_array($qA);
					$actualizar = "documento = '" . $dataCol["documento"] . "',";
				}
				if ($qA->num_rows > 0 || $qA1->num_rows > 0) {


					$sentenciaUpdate = "
						UPDATE Empleados SET
						$actualizar
						nombre = '$nombreColaborador',
						genero = '$genero',
						fecha_ingreso = '$fecha_formateada',
						antiguedad_anios = '$anios',
						antiguedad_meses = '$meses',
						antiguedad_dias = '$dias',
						correo = '" . $correo . "',
						correo_personal = '" . $correo_personal . "',
						telefono_movil = '$telefono_movil',
						telefono_fijo = '$telefono_fijo',
						compania = '$nombreCompania',
						unidad_corporativa = '$id_vp',
						area = '$id_area',
						unidad_organizativa = '$id_uo',
						nivel_jerarquico = '$id_nj',
						nivel_general = '$nivel_general',
						id_cargo = '$id_cargo',
						cargo = '$nombreCargo',
						role = '$rolPlataforma',
						estado = '$estado',
						verificar = '$verificar',
						updated_at = '" . $hoy . "'
						WHERE id = '" . $dataCol["id"] . "';";

					// echo $sentenciaUpdate . "<br>";
					//
					mysqli_query($connect_valentina, $sentenciaUpdate);
				}
			}
		}

		$count++;
	}
	echo '<script> alert("Colaboradores cargados con éxito"); </script>';
	echo '<script> location.href = "?pg=administrar/colaboradores"; </script>';
}

if ($_POST["base_parse_lider"] != "") {

	$count = 1;
	$filas = json_decode($_POST["base_parse_lider"]);

	$filas = array_slice($filas, 1);
	// print_r($filas);
	$total = 0;
	foreach ($filas as $fila1) {
		$qA = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE documento = '" . $fila1[0] . "' AND id_empresa = '" . $_SESSION["id_empresa"] . "' ");
		$dataCol = mysqli_fetch_array($qA);
		mysqli_query($connect_valentina, "DELETE FROM Lideres WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND id_empleado = '" . $dataCol["id"] . "'");
	}
	foreach ($filas as $fila) {
		// print_r($fila[0]);
		// echo "<br>";

		$id_uo = $id_vp = $id_cargo = $id_nj = '';
		$id_area = 'SIN AREA';
		$anios = $meses = $diferencia_dias = $dias = 0;
		if ($count >= 1) {

			if ($fila[0]) {

				if ($fila[2] != "error") {
					$correo = trim($fila[2]);
				} else {
					$correo = "";
				}

				if ($fila[5] != "error") {
					$correo = trim($fila[5]);
				} else {
					$correo = "";
				}
				if ($fila[3] != "error") {
					$qA = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE documento = '" . $fila[0] . "' AND id_empresa = '" . $_SESSION["id_empresa"] . "' ");
					$qAL = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE documento = '" . $fila[3] . "' AND id_empresa = '" . $_SESSION["id_empresa"] . "' ");
					if ($qA->num_rows > 0 && $qAL->num_rows > 0) {

						$dataCol = mysqli_fetch_array($qA);
						$dataLider = mysqli_fetch_array($qAL);
						$qLider = mysqli_query($connect_valentina, "SELECT * FROM Lideres WHERE id_empleado = '" . $dataCol["id"] . "' AND id_jefe = '" . $dataLider["id"] . "' ");
						if (mysqli_num_rows($qLider) === 0) {
							$sentencia = "INSERT INTO Lideres (id_empresa, id_empleado, id_jefe, created_at) VALUES ('" . $_SESSION["id_empresa"] . "', '" . $dataCol["id"] . "', '" . $dataLider["id"] . "','$hoy')";
							mysqli_query($connect_valentina, $sentencia);
						} elseif (mysqli_num_rows($qLider) >= 1) {
							$sentencia = "UPDATE Lideres SET id_jefe = '" . $dataLider["id"] . "' WHERE id_empleado = '" . $dataCol["id"] . "' AND id_empresa = '" . $_SESSION["id_empresa"] . "'";
							mysqli_query($connect_valentina, $sentencia);
						}
						// echo $sentencia.";<br>";
					}
				}
			}
		}

		$count++;
	}
	echo '<script> alert("Lideres cargados con éxito"); </script>';
	echo '<script> location.href = "?pg=estructura/lideres"; </script>';
}

?>


<?php
$hoy = date("Y:m:d H:i:s");
$querySM43 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 4 AND id_submenu = 22");
$dataSM43 = mysqli_fetch_array($querySM43);
include("views/administrar/etiquetas.php");
?>
<script src="<?php echo $url; ?>js/papaparse.js"></script>
<script>
	//FUNCTION PARA CARGAR EL CSV Y VALIDAR, NO CARGA A BASE DE DATOS
	function Cargar_Base_Datos() {

		$("#btn_subir").show();
		$("#tabla_previo").show();


		if ($('#file_base_datos').val() == "") {
			alert("Debes seleccionar un archivo...");
			$("#btn_subir").hide();
			$("#tabla_previo").hide();
		} else {

			$('#file_base_datos').parse({
				config: {
					delimiter: ";",
					complete: pintar_tabla,
					encoding: "ISO-8859-1"
				},
				before: function(file, inputElem) {

				},
				error: function(err, file) {
					console.log("ERROR:", err, file);
				},
				complete: function(result) {
					$("#archivo_cargado_tmp").html("");
				}
			});
		}
	}

	// function pintar_tabla(results) {

	// 	$("#table_validar").html("");
	// 	let data = results.data;
	// 	let cont_item = 1;

	// 	for (let i = 1; i < data.length; i++) {

	// 		let filaValida = false;

	// 		for (let j = 0; j < data[i].length; j++) {
	// 			if (data[i][j] && data[i][j].trim() !== "") {
	// 				filaValida = true;
	// 				break;
	// 			}
	// 		}

	// 		if (filaValida) {
	// 			$("#table_validar").append('<tr>');
	// 			$("#table_validar").append('<td>' + cont_item + '</td>');

	// 			for (let j = 0; j < data[i].length; j++) {
	// 				if (data[i][j] && data[i][j].trim() !== "") {
	// 					$("#table_validar").append('<td>' + data[i][j] + '</td>');
	// 				} else {
	// 					$("#table_validar").append('<td></td>');
	// 				}
	// 			}

	// 			$("#table_validar").append('</tr>');
	// 			cont_item++;
	// 		}
	// 	}

	// 	let modifiedData = data.map(row => row.map(cell => cell && cell.trim() !== "" ? cell : "error"));
	// 	if (modifiedData[modifiedData.length - 1].every(cell => cell === "error")) {
	// 		modifiedData.pop(); // Elimina la última fila si todos los valores son "error"
	// 	}
	// 	// console.log(modifiedData);
	// 	$("#base_parse").val(JSON.stringify(modifiedData));
	// }

	function pintar_tabla(results) {
		console.log(results.data);

		$("#table_validar").html("");
		let data = results.data;
		let cont_item = 1;

		let rows = "";
		let tableColumns = 18;

		let columnasExtras = false;
		data.forEach(row => {
			if (row.length > tableColumns) {
				columnasExtras = true;
			}
		});

		if (columnasExtras) {
			alert("Algunas filas contienen más columnas de las que se pueden mostrar, por favor validar en la siguiente tabla de validación. Si existen mas columnas, los datos no se cargarán");
		}

		for (let i = 1; i < data.length; i++) {
			let row = data[i];

			if (row.every(cell => !cell || cell.trim() === "")) {
				continue;
			}

			// Validar y formatear la fecha en la columna correspondiente (por ejemplo, columna 4)
			if (row[3] && row[3].trim() !== "") {
				let fechaPartes = row[3].split('/');
				if (fechaPartes.length === 3) {
					let dia = fechaPartes[0].padStart(2, '0');
					let mes = fechaPartes[1].padStart(2, '0');
					let anio = fechaPartes[2];
					if (anio.length === 2) {
						anio = parseInt(anio) < 50 ? '20' + anio : '19' + anio;
					}
					row[3] = `${anio}-${mes}-${dia}`;
				} else {
					row[3] = "error";
				}
			} else {
				row[3] = "error";
			}

			let rowHtml = "<tr>";
			rowHtml += "<td>" + cont_item + "</td>";

			row.forEach(cell => {
				rowHtml += "<td>" + (cell || "") + "</td>";
			});

			rowHtml += "</tr>";
			rows += rowHtml;
			cont_item++;
		}

		$("#table_validar").append(rows);

		$("#tabla_previo").show();

		if ($.fn.dataTable.isDataTable('#tabla_previo')) {
			$('#tabla_previo').DataTable().clear().destroy();
		}

		$('#tabla_previo').DataTable({
			columnDefs: [{
					responsivePriority: 1,
					targets: 0
				},
				{
					responsivePriority: 2,
					targets: -1
				}
			],
			order: [
				[2, 'asc']
			],
			responsive: false,
			pageLength: 50,
			language: {
				processing: "Procesando...",
				search: "Buscar:",
				lengthMenu: "Mostrar _MENU_ registros.",
				info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
				infoEmpty: "Mostrando registros del 0 al 0 de 0 registros",
				infoFiltered: "(filtrado de un total de _MAX_ registros)",
				loadingRecords: "Cargando...",
				zeroRecords: "No se encontraron resultados",
				emptyTable: "Ningún dato disponible en esta tabla",
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
			}
		});

		let modifiedData = data.map(row => row.map(cell => cell && cell.trim() !== "" ? cell : "error"));
		if (modifiedData[modifiedData.length - 1].every(cell => cell === "error")) {
			modifiedData.pop();
		}

		$("#base_parse").val(JSON.stringify(modifiedData));
	}

	function Reset() {
		$("#table_validar").html("");
		$("#btn_subir").hide();
		$("#tabla_previo").hide();
	}
</script>

<style>
	.card-footer,
	.card,
	.card-body,
	.card-header {
		background-color: #FFFFFF !important;
	}

	.card-title,
	.card-text {
		text-align: justify !important;
	}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header" style="background-color: #FFFFFF !important;">
				<div class="row">
					<div class="col-md-12" style="text-align: start !important;">
						<h4><i class="fas fa-dice-d20" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM43["nombre"]; ?></h4>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br>
<section style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:80vh; text-align:center; background:#fff; color:#333; padding:2rem; box-shadow:0 0 10px rgba(0,0,0,0.1); max-width:600px; margin:auto; border-radius:8px;">
		<h1 style="font-size:2rem; margin-bottom:1rem;">🔧 En Mantenimiento</h1>
		<p style="font-size:1.2rem; margin:0;">Este módulo se encuentra temporalmente fuera de servicio por tareas de mantenimiento. Por favor, vuelve a intentarlo más tarde.</p>
	</section>
<!-- <div class="container-fluid">

	<div class="row">
		<div class="col-md-12" align="center">
			<div class="card">
				<div class="card-body" align="center">
					<div class="card">
						<div class="card-header">
							<div class="col-md-12">
								<ul class="nav nav-fill nav-tabs" id="myTab" role="tablist" style="font-family: 'Lato-Bold';">
									<li class="nav-item" role="presentation">
										<button class="nav-link active" id="detalle-tab" data-bs-toggle="tab" data-bs-target="#detalle-tab-pane-<?php echo $id; ?>" type="button" role="tab" aria-controls="detalle-tab-pane-<?php echo $id; ?>" aria-selected="true" style="color: black;font-weight:700;">Colaboradores</button>
									</li>
									<li class="nav-item" role="presentation">
										<button class="nav-link" id="lider-tab" data-bs-toggle="tab" data-bs-target="#lider-tab-pane-<?php echo $id; ?>" type="button" role="tab" aria-controls="lider-tab-pane-<?php echo $id; ?>" aria-selected="false" style="color: black;font-weight:700;">Lideres Plataforma</button>
									</li>
								</ul>
							</div>
						</div>
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									<div class="tab-content pt-5" id="myTabContent_<?php echo $id; ?>">
										<div class="tab-pane fade show active" id="detalle-tab-pane-<?php echo $id; ?>" role="tabpanel" aria-labelledby="detalle-tab" tabindex="0">

											<div class="row">
												<div class="col-md-12">
													<div align="right">
														<a href="<?php echo $url; ?>/Archivo_de_ejemplo_new_PC.csv" target="_blank">
															<input type="button" class="btn btn-warning btn-sm" value="Descargar archivo de ejemplo aquí" />
														</a>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<p class="card-text" style="text-align: justify !important;">
														Aquí podrá cargar un archivo .csv con la lista de colaboradores.<br /><br />
														Seleccione el archivo <b>.csv</b> y revise que los datos se encuentren correctamente configurados,
														luego de clic en el botón <b>Relacionar colaboradores</b> para iniciar con el proceso.<br />
														<b style="color: #fc0758;">* Puede realizar este proceso tantas veces sea nesesario antes de guardar los colaboradores. *</b> <br />
														<b style="color: #fc0758;">* Recuerde que los documento duplicados no serán relacionado en la base de datos. *</b> <br />
														<b style="color: #fc0758;">* Recuerde validar el archivo csv que NO TENGA UNA FILA EN BLANCO despues del ultimo registro, ya que esto genera un error y no se cargaran los colaboradores. *</b> <br />
														<br>
														Tener en cuenta lo siguiente:
													<ul style="font-size: 1rem;text-align: justify;">
														<li>Validar el número de documento de cada colaborador, ya que no se guardara colaboradores con el mismo número de documento, si el cargue es para actualización de colaboradores, validar documento ya que si en el archivo el documento tiene un digito diferente al relacionado en base de datos, este se creará como nuevo colaborador.</li>
														<li>Omitir en los nombres de colaborador, primer, segundo y tercer nivel organizacional las comillas simples (') ya que genera error en el cargue, y el colaborador no se cargará.</li>
														<li>De igual forma validar todos los nombres que no tengan espacios tanto al comienzo como al final, verificar que los nombres estén bien, ya que si un cargo, primer, segundo o tercer nivel organizacional se diferencia del otro por un carácter o una letra de más, este se considera como nuevo registro en el sistema.</li>
														<li>Tener en cuenta la descripción o título de cada columna, ya que algunas columnas se manejan por códigos y no por nombre.</li>
														<li>La contraseña del colaborador por defecto son los 4 ultimos numeros o dígitos del documento</li>
														<li>Los listados de primer, segundo y tercer nivel organizacional, se encuentra en el menú empresa y en el submenú correspondiente a cada uno, igual que el listado de cargos, áreas y nivel jerárquico.</li>
														<li>La contraseña de acceso para usuarios nuevos, son los 4 ultimos dígitos del documento.</li>
													</ul>
													</p>
													<p class="card-text" style="text-align: justify !important;">
														Los campos obligatorios son: <br>
													<ul style="text-align: justify;">
														<li><b style="color: #fc0758;">Documento / Código</b></li>
														<li><b style="color: #fc0758;">Nombre Colaborador</b></li>
														<li><b style="color: #fc0758;">Correo Empresarial</b></li>
														<li><b style="color: #fc0758;"><?php echo $etiquetaAdminVP; ?></b></li>
														<li><b style="color: #fc0758;"><?php echo $etiquetaAdminArea; ?></b></li>
														<li><b style="color: #fc0758;"><?php echo $etiquetaAdminCargo; ?></b></li>
														<li><b style="color: #fc0758;">Rol Plataforma</b></li>
														<li><b style="color: #fc0758;">Estado</b></li>
													</ul>
													</p>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">

													<input class="form-control" id="file_base_datos" type="file" accept=".csv" style="margin-bottom:10px; border: 0;">
													<div align="right">
														<input type="button" class="btn btn-primary btn-md" value="Validar" onclick="Cargar_Base_Datos()" />
														<input type="button" class="btn btn-success btn-md" value="Borrar" onclick="Reset()" />
													</div>
												</div>
											</div>
											<hr>
											<div class="row">
												<div class="col-md-12" style="text-align: justify !important;">
													<div class="table-responsive">
														<input name="guardar_colaboradores" type="hidden" value="true" />
														<table border="1" id="tabla_previo" class="display table" style="width:100%;margin-top:15px; display:none">
															<thead class="thead-dark">
																<tr>
																	<th>#</th>
																	<th>Documento</th>
																	<th>Nombre</th>
																	<th>Género</th>
																	<th>Fecha Ingreso</th>
																	<th>Correo empresarial</th>
																	<th>Correo personal</th>
																	<th>Teléfono móvil</th>
																	<th>Teléfono fijo</th>
																	<th>Compañía - Empresa</th>
																	<th><?php echo $etiquetaAdminVP; ?></th>
																	<th><?php echo $etiquetaAdminArea; ?></th>
																	<th><?php echo $etiquetaAdminUO; ?></th>
																	<th><?php echo $etiquetaAdminNJ; ?></th>
																	<th>Nivel General</th>
																	<th><?php echo $etiquetaAdminCargo; ?></th>
																	<th>Rol Plataforma</th>
																	<th>Estado</th>
																	<th>Verificado</th>
																</tr>
															</thead>

															<tbody id="table_validar">
															</tbody>
														</table>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<form action="" method="post">
														<div class="row" style="margin-bottom:20px; display:none" id="btn_subir">
															<div class="col-md-12">
																<input name="id_proyecto" type="hidden" value="<?php echo $_GET["id"]; ?>" />
																<input id="base_parse" name="base_parse" type="hidden" />
																<button type="submit" class="btn btn-primary btn-md btn-block">
																	<i class="fa fa-upload"></i> Cargar Colaboradores
																</button>
															</div>
														</div>
													</form>
												</div>
											</div>
										</div>
										<div class="tab-pane fade" id="lider-tab-pane-<?php echo $id; ?>" role="tabpanel" aria-labelledby="lider-tab" tabindex="0">
											<?php include("views/administrar/loads_lideres.php"); ?>
										</div>
									</div>
								</div>
							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php echo $respuesta; ?>

</div> -->
<!-- <script type="text/javascript">
	$(document).ready(function() {
		$('#tabla_previo').DataTable({
			columnDefs: [{
					responsivePriority: 1,
					targets: 0
				},
				{
					responsivePriority: 2,
					targets: -1
				}
			],
			order: [
				[2, 'asc']
			],
			responsive: true,
			pageLength: 50,
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
				emptyTable: "Ningún dato disponible en esta tabla",
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
			}

		});

	});
</script> -->