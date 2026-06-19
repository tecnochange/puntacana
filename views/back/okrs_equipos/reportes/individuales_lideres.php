<script>
	// $("#bt_okrs_lideres").addClass("active_item");
	// $("#nav_reportes").addClass("menu-is-opening menu-open");
	$(".menu_section").addClass("active");
	$("#nav_reportes").addClass("active");
	jQuery("#menu_reportes").css("display", "none");
	$("#bt_okrs_lideres").addClass("current-page");
</script>
<style>
	#foto_colaboradores {
		width: 50px !important;
		height: 50px !important;
	}
</style>
<?php
include("views/okrs_equipos/functions.php");

$avance_general_equipo = 0;
$count_avance_general_equipo = 0;

if ($_POST["anio_fill"] != "") {
	$_SESSION["anio_fill"] = $_POST["anio_fill"];
}
if ($_POST["anio_fill"] == -1) {
	$_SESSION["anio_fill"] = "";
}

// print_r($_SESSION);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	if (isset($_POST['periodo'])) {
		$_SESSION['selected_periodo_okr'] = $_POST['periodo'];  // Guardar checkboxes seleccionados en la sesión
	} else {
		// Si no se seleccionan checkboxes, eliminar la sesión para checkboxes
		unset($_SESSION['selected_periodo_okr']);
	}
}

$filtro_periodo = "";

$contFiltro = 0;
$contQ = 0;
$filtroPonderado = "";

if (isset($_SESSION['selected_periodo_okr'])) {

	$quotedOptions = array_map(function ($value) {
		return "'$value'";
	}, $_SESSION['selected_periodo_okr']);

	$contFiltro = count($_SESSION['selected_periodo_okr']);
	$filtro_periodo = implode(', ', $quotedOptions);
	$filtroPonderado = implode(', ', $quotedOptions);
}

$_SESSION["periodo_fill"] = $filtro_periodo != "" ? $filtro_periodo : "";

$filtros = $filtro != "" ? $filtro : "Q";

$filtro_vr = null;
$filtro_kr = $filtro_claves = "";
if ($filtro) {
	$filtro_kr = "AND Okrs.anio = " . $_SESSION["anio_fill"] . " ";
	$filtro_claves = "AND periodo IN (" . $filtro . ") ";
	$filtro_claves1 = "AND Okrs.periodo IN (" . $filtro . ") ";
} else {
	$filtro_kr = "AND Okrs.anio = " . $_SESSION["anio_fill"] . "";
}
if ($filtro_claves) {
	$filtro_vr = "," . $filtro;
}

$filtro_area = "";

if ($_SESSION['role_plataforma'] == 2) {
	if ($_SESSION['area'] > 0) {
		$filtro_area = " AND area = " . $_SESSION['area'] . "";
	} else {
		$queryAreaF = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND nombre = '" . $_SESSION['area'] . "' ");
		$dataAreaF = mysqli_fetch_array($queryAreaF);
		$filtro_area = " AND area = " . $dataAreaF["id"] . "";
	}
}


// $query = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id_empresa = '" . $dtEmpleado['id_empresa'] . "' $filtro_area");
// $count = 1;
// $avance_general_equipo = $cont_array = 0;
// $array_equipo = array();
// while ($data = mysqli_fetch_array($query)) {

// 	$queryCargo = mysqli_query($connect_valentina, "SELECT * FROM Cargos WHERE id = '" . $data["id_cargo"] . "' ");
// 	$dataCargo = mysqli_fetch_array($queryCargo);

// 	$queryArea = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = '" . $dataCargo["id_area"] . "' ");
// 	$dataArea = mysqli_fetch_array($queryArea);

// 	$queryDireccion = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = '" . $dataCargo["id_direccion"] . "' ");
// 	$dataDireccion = mysqli_fetch_array($queryDireccion);

// 	$txt_role = '';
// 	foreach ($Array_Role as $role) {
// 		if ($role[0] == $data["role"]) {
// 			$txt_role = $role[1];
// 		}
// 	}

// 	$txt_estado = '';
// 	foreach ($Array_Estado  as $estado) {
// 		if ($estado[0] == $data["estado"]) {
// 			$txt_estado = $estado[1];
// 		}
// 	}

// 	if (!$data["foto"]) {
// 		$data["foto"] = "img_default.jpg";
// 	}

// 	$photo = $data["foto"];
// 	$nombreEmpleado = $data["nombre"];
// 	$idColaborador = $data["id"];

// 	include("views/avatars/avatar.php");

// 	$array_equipo[$cont_array]["foto"] = $avatarOkrs;
// 	// $array_equipo[$cont_array]["foto"] = $data["foto"];

// 	if ($data["area"] > 0) {
// 		$queryArea = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = '" . $data["area"] . "' ");
// 		$dataArea = mysqli_fetch_array($queryArea);
// 		$area = $dataArea["nombre"];
// 	} else {
// 		$area = $data["area"];
// 	}

// 	if ($data["unidad_corporativa"] > 0) {
// 		$queryvicepresidencia = mysqli_query($connect_valentina, "SELECT * FROM Vicenpresidencia WHERE id = '" . $data["unidad_corporativa"] . "' ");
// 		$datavicepresidencia = mysqli_fetch_array($queryvicepresidencia);
// 		$vicepresidencia = $datavicepresidencia["nombre"];
// 	} else {
// 		$vicepresidencia = $data["unidad_corporativa"];
// 	}

// 	if ($data["nivel_jerarquico"] > 0) {
// 		$querynj = mysqli_query($connect_valentina, "SELECT * FROM Nivel_Jerarquico WHERE id = '" . $data["nivel_jerarquico"] . "' ");
// 		$datanj = mysqli_fetch_array($querynj);
// 		$nj = $datanj["nombre"];
// 	} else {
// 		$nj = $data["nivel_jerarquico"];
// 	}


// 	$OKRS = OkrsReporteUsuario($data["id"], $data["id_empresa"], $connect_okrs, $filtro_kr);

// 	$contador = $prueba = 0;
// 	foreach ($OKRS as $value) {
// 		$resultados = PorOkrsReporteUsuario($connect_okrs, $value["id"], $data['id'], $filtro_claves);
// 		$contador = $contador + $resultados["promedio"];
// 		$prueba++;
// 	}

// 	$a_por = round(($contador / $prueba), 1);

// 	$escala = EscalaColor($a_por, $dtEmpleado['id_empresa'], $connect_valentina);

// 	$color_bg = $escala['color_bg'];
// 	if (is_nan($a_por)) {
// 		$a_por = 0;
// 	}

// 	$escala_home = EscalaColor(round($a_por), $dtEmpleado['id_empresa'], $connect_valentina);
// 	$back_color = "background-color:" . $escala_home['color_bg'] . " !important";
// 	$txt_rango_meta = $escala_kr['txt_subtitulo'];

// 	if ($a_por > 100) {
// 		$a_por = 100;
// 	}

// 	$avance = '<div class="progress" title="' . $escala_home["txt_subtitulo"] . '">
// 	<div class="progress-bar bg-success" role="progressbar" style=" min-width: 15px; width: ' . $a_por . '%;' . $back_color . ';color:' . $escala_home["color_text"] . ' !important;opacity: 0.7 !important;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">' . $a_por . '%</div>
// </div>';
// 	$array_equipo[$cont_array]["id"] = $data["id"];
// 	$array_equipo[$cont_array]["id_empresa"] = $data["id_empresa"];
// 	$array_equipo[$cont_array]["documento"] = $data["documento"];
// 	// $array_equipo[$cont_array]["foto"] = $data["foto"];
// 	$array_equipo[$cont_array]["nombre"] = $data["nombre"];
// 	$array_equipo[$cont_array]["correo"] = $data["correo"];
// 	$array_equipo[$cont_array]["compania"] = $data["compania"];
// 	$array_equipo[$cont_array]["vicepresidencia"] = $vicepresidencia;
// 	$array_equipo[$cont_array]["area"] = $area;
// 	$array_equipo[$cont_array]["cargo"] = $data["cargo"];
// 	$array_equipo[$cont_array]["nivel_jerarquico"] = $nj;
// 	$array_equipo[$cont_array]["estado"] = $txt_estado;
// 	$array_equipo[$cont_array]["promedio"] = $a_por;
// 	$array_equipo[$cont_array]["avance"] = $avance;
// 	$array_equipo[$cont_array]["promedio"] = $a_por;

// 	$cont_array++;
// 	$count++;
// }

// $equipo = array_sort($array_equipo, 'promedio', SORT_DESC);

?>

<?php include("views/okrs/layouts/modal_okr.php"); ?>

<!-- OKRS EQUIPOS -->
<!-- OKRS EQUIPOS -->
<!-- OKRS EQUIPOS -->
<div class="container-fluid">

	<h3>Reporte Individual Líderes Equipo</h3>
	<div class="row">
		<div class="col-md-12">
			<?php include("views/okrs_equipos/reportes/filtro_individual_lider.php"); ?>
		</div>
	</div>

	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="table-responsive">
				<div class="card">
					<div class="card-body">
						<ul class="nav nav-tabs" style="display: none">
							<li class="nav-item">
								<a href="<?php echo $url; ?>?pg=administrar/colaboradores" class="nav-link active"><?php echo $IDIOMA["estructura_colaboradores"]; ?> </a>
							</li>

							<li class="nav-item">
								<a href="<?php echo $url; ?>?pg=administrar/hojas_vida" class="nav-link"><?php echo $IDIOMA["estructura_hojas_de_vida"]; ?></a>
							</li>
						</ul>

						<table border="1" id="individuales" class="display table" style="width:100%">
							<thead class="thead-success">
								<tr>
									<th scope="col" width="15">Documento</th>
									<th scope="col" width="100">Foto</th>
									<th scope="col">Nombres y Apellidos</th>
									<th scope="col">Correo</th>
									<th scope="col">Compañia</th>
									<th scope="col">Vicepresidencia</th>
									<th scope="col">Área</th>
									<th scope="col">Cargo</th>
									<th scope="col">Nivel Jerárquico</th>
									<th scope="col">Estado</th>
									<th scope="col">Avance OKR</th>
									<th scope="col">Acciones</th>
								</tr>
							</thead>
							<tbody>
								<?php

								$number = 1;
								foreach ($equipo as $value) {

									// 			echo '
									// <tr style="vertical-align: middle;">

									//     <td>' . $value["documento"] . '</td>
									//     <td align="center"> ' . $value["foto"] . ' </td>
									//     <td>' . $value["nombre"] . '</td>
									//     <td>' . $value["correo"] . '</td>
									// 	<td>' . $value["compania"] . '</td>	
									// 	<td>' . $value["vicepresidencia"] . '</td>							
									// 	<td>' . $value["area"] . '</td>
									//     <td>' . $value["cargo"] . '</td>
									// 	<td>' . $value["nivel_jerarquico"] . '</td>
									//     <td>' . $value["estado"] . '</td>
									// 	<td>' . $value["avance"] . '</td>
									//     <td><button type="button" class="btn btn-black btn-sm bt_editar" onClick="VerOKRIndividual(' . $value['id'] . ',' . $value['id_empresa'] . ',' . $_SESSION["anio_fill"] . ',' . $value["promedio"] . '' . $filtro_vr . ')" data-bs-toggle="tooltip" title="Vista rápida del OKRs" style="float: right;">
									// 	<i class="fa fa-eye" style="font-size: 1.3rem;"></i>
									// </button></td>
									// </tr>';
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
	var api = '<?php echo $url; ?>api/okrs/';

	function VerOKRs(id) {
		jQuery.ajax({
				url: api + "ver_okr.php",
				type: 'post',
				data: {
					id: id
				},
			}).done(function(resp) {
				$("#modal_okr").modal("show");
				$("#modal_contenido").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});


	}

	function Filtrar() {
		$("#formulario_filtro").submit();
	}

	function VerOKRIndividual(id_owner, id_empresa, anio, promedio, filtro1, filtro2, filtro3, filtro4, filtro5) {
		if (!filtro1) {
			filtro1 = 0;
		}
		if (!filtro2) {
			filtro2 = 0;
		}
		if (!filtro3) {
			filtro3 = 0;
		}
		if (!filtro4) {
			filtro4 = 0;
		}
		if (!filtro5) {
			filtro5 = 0;
		}
		window.open("<?php echo $url; ?>views_okrs/okrs_individual.php?id_owner=" + id_owner + "&id_empresa=" + id_empresa + "&anio=" + anio + "&promedio=" + promedio + "&filtro1=" + filtro1 + "&filtro2=" + filtro2 + "&filtro3=" + filtro3 + "&filtro4=" + filtro4 + "&filtro5=" + filtro5, "GoForAgile", "width=1300, height=900")
	}
</script>
<script type="text/javascript">
	$(document).ready(function() {
		var api = '<?php echo $url; ?>api/okrs/';
		$('#individuales').DataTable({
			destroy: true,
			ajax: {
				url: api + "reporte_individual.php",
				type: "GET",
				data: function(d) {
					d.id_empresa = '<?php echo $_SESSION["id_empresa"]; ?>';
					d.url = '<?php echo $url; ?>';
					d.filtro_area = '<?php echo $filtro_area; ?>';
					d.filtro_kr = '<?php echo $filtro_kr; ?>';
					d.filtro_claves = '<?php echo $filtro_claves; ?>';
					d.filtro_Vr = '<?php echo $filtro_Vr; ?>';
					d.anio_fill = '<?php echo $_SESSION["anio_fill"]; ?>';
					console.log(d); // Revisa los parámetros que se están enviando
				},
				dataSrc: 'data' // Corregido para usar 'data' como la clave de los datos
			},
			columns: [{
					data: "documento"
				},
				{
					data: "foto"
				},
				{
					data: "nombre"
				},
				{
					data: "correo"
				},
				{
					data: "compania"
				},
				{
					data: "vicepresidencia"
				},
				{
					data: "area"
				},
				{
					data: "cargo"
				},				
				{
					data: "nivel_jerarquico"
				},
				{
					data: "estado"
				},
				{
					data: "avance"
				},
				{
					data: "acciones"
				}
			],
			pageLength: 50,
			order: [
				[10, 'desc']
			],
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