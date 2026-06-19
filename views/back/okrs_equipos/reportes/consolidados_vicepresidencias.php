<script>
	$(".menu_section").addClass("active");
	$("#nav_reportes").addClass("active");
	jQuery("#menu_reportes").css("display", "none");
	$("#bt_okrs_consolidados_vicepresidencias").addClass("current-page");
</script>
<link rel="stylesheet" href="<?php echo $url; ?>views/okrs_equipos/reportes/styles.css">
<style>
	.progreso-bar {
		width: 130px !important;
		height: 130px !important;
	}

	.objetivo-okr {
		font-size: 1.8rem !important;
	}

	#vistaAreas:before,#profileOkr:before {
		content: none !important;
	}
</style>
<?php
global $connect_valentina;
global $connect_okrs;

include("views/okrs_equipos/functions.php");
include("views/okrs/layouts/modal_profile.php");
if ($_POST["anio_fill"] != "") {
	$_SESSION["anio_fill"] = $_POST["anio_fill"];
}
if ($_POST["anio_fill"] == -1) {
	$_SESSION["anio_fill"] = "";
}

if ($_POST["vp_fill"] != "") {
	$_SESSION["vp_fill"] = $_POST["vp_fill"];
}
if ($_POST["vp_fill"] == -1) {
	$_SESSION["vp_fill"] = "";
}

if ($_SESSION["vp_fill"] != "") {
	$filtro_VP = "AND id = " . $_POST["vp_fill"] . "";
} else {
	$filtro_VP = "";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['periodo'])) {
        $_SESSION['selected_periodo_okr'] = $_POST['periodo'];
    } else {
        unset($_SESSION['selected_periodo_okr']);
    }
}

$filtro = "";

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

$filtros = $filtro != "" ? $filtro : "Q";
$_SESSION["periodo_fill"] = $filtro != "" ? $filtro : "";

//POR OKRS
//POR OKRS
//POR OKRS
//POR OKRS


$resultado_total = 0;
$conteo_total = 0;
$conteo_okr = 0;

$queryRL = mysqli_query($connect_valentina, "SELECT * FROM Relaciones_Laborales WHERE id_empleado = '" . $_SESSION["id_user"] . "' AND mod_okrs = 'on' AND id_area = '' AND estado = 1");
$areasMacro = "";
if(mysqli_num_rows($queryRL) > 0){
    $areasMacro = "AND id IN (";
    while ($dataRL = mysqli_fetch_array($queryRL)) {
        $areasMacro .= $dataRL["id_vp"].",";
    }
    $areasMacro = substr($areasMacro, 0, -1);
    $areasMacro .= ")";
}
$querySM92 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 9 AND id_submenu = 53");
$dataSM92 = mysqli_fetch_array($querySM92);
?>

<link rel="stylesheet" href="<?php echo $url; ?>css/okrs.css">
<!-- OKRS EQUIPOS -->
<!-- OKRS EQUIPOS -->
<!-- OKRS EQUIPOS -->
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col-md-12">
						<h3><i class="fas fa-chart-pie" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo mb_strtoupper($dataSM92["nombre"]); ?> AÑO <?php echo $_SESSION["anio_fill"]; ?></h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
if ($_GET["consolidados"] == 1) {
?>
<br>
	<div class="row">
		<div class="col-md-12">
			<a href="<?php echo $url ?>?pg=okrs_equipos/reportes/consolidados" class="btn btn-primary" id="btnAccion">Volver a consolidado general</a>
		</div>
	</div>
	<script>
		removeUrlParameter('consolidados');	

		function removeUrlParameter(param) {
			const url = new URL(window.location.href);

			url.searchParams.delete(param);

			window.history.replaceState({}, document.title, url);

		}
	</script>
<?php
}
?>
<br>
<div class="container-fluid">

<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-body">
			<?php include("views/okrs_equipos/reportes/filtro_consolidado_vicepresidencias.php"); ?>
			</div>
		</div>
	</div>
</div>

	<br>

	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table border="1" id="vicepresidencias" class="display table" style="width:100%">
									<thead>
										<th>Nombre</th>
										<th>Líder</th>
										<th>OKRs asignados</th>
										<th>Progreso</th>
										<th>Acciones</th>
									</thead>
									<tbody>
										<?php
										$filtro_vr = null;
										$filtro_kr = $filtro_claves = $filtro_claves1 = $filtro_empleado = "";
										if ($filtro) {
											$filtro_kr .= "AND anio = '" . $_SESSION["anio_fill"] . "' ";
											$filtro_claves .= "AND periodo IN (" . $filtro . ") ";
											$filtro_claves1 .= "AND ORE.periodo IN (" . $filtro . ") ";
										} else {
											$filtro_kr .= "AND anio = '" . $_SESSION["anio_fill"] . "'";
										}
										if ($_SESSION['role_plataforma'] == 2) {
											$filtro_kr .= " AND id_empleado = " . $dtEmpleado['id'] . "";
											// $filtro_empleado .= " AND Okrs_Equipos.id_empleado = " . $dtEmpleado['id'] . "";
										}

										if($filtro_periodo){
											$filtro_claves .= "AND periodo IN (" . $filtro_periodo . ") ";
										}

										if ($filtro_claves) {
											$filtro_vr = "," . $filtro;
										}

										

										$queryVP = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'  AND estado = 1 $filtro_VP $areasMacro ORDER BY nombre");

										$contVP = 0;
										while ($dataVP = mysqli_fetch_array($queryVP)) {
											$arrayCol = array();
											$resultado1 = OkrsPorVicepresidenciaConsolidados($dataVP["id"], $filtro_claves, $connect_okrs);
											$cantidadOkrs = $resultado1["total"];

											$promedio = round($resultado1["promedio"],2);
											if (is_nan($promedio) || is_infinite($promedio)) {
												$promedio = 0;
											}										

											$nombre_lider = '';

											$queryLideres = mysqli_query($connect_valentina, "SELECT * FROM Lideres_Vicepresidencia WHERE id_vicepresidencia = '" . $dataVP["id"] . "'");

											if (mysqli_num_rows($queryLideres) > 0) {
												while ($dataLideres = mysqli_fetch_array($queryLideres)) {
													$queryEmple = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '" . $dataLideres["id_lider"] . "' ");
													$dataEmple = mysqli_fetch_array($queryEmple);

													if (!$dataEmple["foto"]) {
														$dataEmple["foto"] = "img_default.jpg";
													}
													$nombre_lider .= '<a data-bs-toggle="tooltip" href="javascript:Profile(' . $dataLideres["id_lider"] . ',1)" class="dropdown-item" id="profileOkr"><img data-src="' . $url . '/recursos/' . $dataEmple["foto"] . '" class="lazyload foto_min" title="' . $dataEmple["nombre"] . '" style="width: 35px !important;height: 35px !important;"></a>';
												}
											} else {
												$nombre_lider = 'Sin Asignar';
											}
											if ($promedio > 100) {
												$porcentaje_barra = 100;
												$promedio = 100;
											} else {
												$porcentaje_barra = round($promedio);
											}
											$escala = EscalaColor($promedio, $_SESSION['id_empresa'], $connect_valentina);
											$color_bg = $escala['color_bg'];

											// include("views/okrs_equipos/consolidados/listado_areas_vp.php");

											// $equipo = array_sort($arrayCol, 'promedio', SORT_DESC);
											// $colaboradores = array_slice($equipo, 0, 10);


										?>
											<tr>
												<td><?php echo $dataVP["nombre"]; ?></td>
												<td><?php echo $nombre_lider; ?></td>
												<td><?php echo $cantidadOkrs; ?></td>
												<td>
													<div class="progress" data-bs-toggle="tooltip" title="" style="height: 15px;">
														<div class="progress-bar bg-success progress-bar-striped active" role="progressbar" style=" color: black; width: <?php echo $porcentaje_barra; ?>%;background-color: <?php echo $color_bg; ?> !important;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
													</div><?php echo $promedio; ?>%
												</td>
												<td>
													<a class="btn btn-black btn-sm bt_editar" href="<?php echo $url ?>?pg=okrs_equipos/consolidados/info_vicepresidencia&id=<?php echo $dataVP["id"] ?>" data-bs-toggle="tooltip" title="Vista rápida de los OKRs" style="float: right;">
														<i class="fa fa-eye" style="font-size: 1.3rem;"></i>
													</a>
												</td>
											</tr>
										<?php

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
	</div>

</div>

<script>
	$(document).ready(function() {
		$(' #vicepresidencias').DataTable({
			columnDefs: [{
					responsivePriority: 1,
					targets: 0
				},
				{
					responsivePriority: 2,
					targets: -1
				}
			],

			responsive: true,
			pageLength: 30,
			info: true,
			ordering: true,
			paging: true,
			searching: true,
			autoWidth: true,
			language: {
				processing: "Procesando...",
				search: "Buscar:",
				lengthMenu: "Mostrar _MENU_ registros.",
				info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
				infoEmpty: "Mostrando registros del 0 al 0 de 0 registros",
				infoFiltered: "(filtrado de un total de _MAX_ registros)",
				infoPostFix: "",
				loadingRecords: "Cargando...",
				zeroRecords: "No se encontraron vicepresidencias",
				emptyTable: "Ninguna iniciativa para este KR",
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
					buttons: ['copy', 'excel', 'csv',
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
	var api = '<?php echo $url; ?>api/okrs/';



	function Filtrar() {
		$("#formulario_filtro").submit();
	}

	function Profile(id, val) {
		jQuery.ajax({
				url: api + "profile_empleado.php",
				type: 'post',
				data: {
					id: id,
					val: val
				},
			}).done(function(resp) {
				$("#modal_profile").modal("show");
				$("#modal_contenidos").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});
	}
</script>