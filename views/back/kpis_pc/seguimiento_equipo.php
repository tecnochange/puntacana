<script>
	$(document).ready(function() {
		$('#menuDesempenio').collapse();
		$("#bt_desempenio_seguimiento_equipo").addClass("active");
	});
</script>

<?php
include("views/okrs/layouts/modal_profile.php");
include("views/okrs_equipos/functions.php");
include("views/kpis_pc/detalle/functions.php");
$hoy = date("Y-m-d H:i:s");
$queryEscala = mysqli_query($connect_valentina, "SELECT * FROM Escala_Medicion WHERE id_empresa = " . $_SESSION["id_empresa"] . "");
$dataEscala = mysqli_fetch_array($queryEscala);

$tituloRes1 = $tituloIni1 = $dataEscala['titulo_uno'];
$tituloRes2 = $tituloIni2 = $dataEscala['titulo_tres'];
$tituloRes3 = $tituloIni3 = $dataEscala['titulo_cuatro'];
$tituloRes4 = $tituloIni4 = $dataEscala['titulo_cinco'];
$tituloRes5 = $tituloIni5 = $dataEscala['titulo_seis'];

$colorRes1 = $colorIni1 = "#FF0000";
$colorRes2 = $colorIni2 = "#FFF200";
$colorRes3 = $colorIni3 = "#95FA03";
$colorRes4 = $colorIni4 = "#14F209";
$colorRes5 = $colorIni5 = "#00D30A";

$querySM68 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 6 AND id_submenu = 40");
$dataSM68 = mysqli_fetch_array($querySM68);
?>

<style>
	.tabla_anios {
		margin-top: 6px;
		margin-bottom: 20px;
		width: 100%;
	}

	.meses {
		margin: 3px;
		border-radius: 10px;
		padding: 3px 0px;
		color: #ffffff;
		border: 1px solid #c3c3c3;
	}

	.card,
	.card-header,
	.card-body,
	.card-footer {
		background-color: #ffffff !important;
	}

	#iconCabecera {
		font-size: 24px;
		/* color: #007ae1; */
	}

	.card-header,
	.card-body {
		border-bottom: none;
	}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header" style="background-color: #FFFFFF !important;">
				<div class="row">
					<div class="col-md-12" style="text-align: start !important;">
						<h4><i class="fas fa-chart-line" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM68["nombre"]; ?></h4>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br>
<div class="container-fluid">
	<div class="row">
		<h4 style="color: black !important;">AVANCE GENERAL EQUIPO</h4>
		<div class="col-md-12" style="text-align: center;" id="progreso_desempenio">
			<div class="card">
				<div class="card-header">

					<?php

					$colaborador = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '" . $_SESSION['id_user'] . "' ");
					$dataEmpleado = mysqli_fetch_array($colaborador);

					$complemento = "";
					$queryLV = mysqli_query($connect_valentina, "SELECT * FROM Lideres_Vicepresidencia WHERE id_lider = " . $_SESSION['id_user'] . "");
					if (mysqli_num_rows($queryLV) > 0) {
						$listadoLV = "";
						while ($dataLV = mysqli_fetch_array($queryLV)) {
							$listadoLV .= $dataLV["id_vicepresidencia"] . ",";
						}
						$listadoLV = substr($listadoLV, 0, -1);
						$complemento = "AND K.area_macro IN ($listadoLV)";
					} else {
						$queryLA = mysqli_query($connect_valentina, "SELECT * FROM Lideres_Area WHERE id_lider = " . $_SESSION['id_user'] . "");
						if (mysqli_num_rows($queryLA) > 0) {
							$listadoLA = "";
							while ($dataLA = mysqli_fetch_array($queryLA)) {
								$listadoLA .= $dataLA["id_area"] . ",";
							}
							$listadoLA = substr($listadoLA, 0, -1);
							$complemento = "AND K.area_proceso IN ($listadoLA)";
						}
					}

					$queryKpis = mysqli_query($connect_kpis, "SELECT DISTINCT(K.Id) AS id, K.tipo_kpi, K.anio, K.area_proceso, K.subproceso, K.objetivo_sg, K.indicador, K.objetivo_indicador,
                    K.formula, K.resultado_anterior, K.unidad_medida, K.tipo_calculo, K.meta, K.frecuencia, K.tipo_resultado, K.obj_meses $mesConsultaI $mesConsultaF $periodoFill
                    FROM Kpis K
                    INNER JOIN Kpis_Colaborador KC ON KC.id_kpi = K.id
                    INNER JOIN Frecuencia_Kpis FKP ON FKP.id_kpi = K.id
                    WHERE K.id_empresa = " . $_SESSION['id_empresa'] . " AND K.anio = " . $_SESSION['anio_fill'] . " $complemento $filtro ORDER BY K.indicador ASC");
					$contKpis = mysqli_num_rows($queryKpis);
					if (mysqli_num_rows($queryKpis) == 0) {
						echo '
		<div class="alert alert-primary" role="alert">
			Su célula no tiene KPIS asignados.
		</div>
		';
					}

					include("views/kpis_pc/seguimiento_equipo/desempenio_equipo.php");

                    $avanceGeneral = $sumAvanceE / $contKpiE;

					if ($_SESSION["tipo_kpi_fill"] == "") {
						if ($contKpiE > 0 && $contKpiT > 0) {
							$queryPonderacion = mysqli_query($connect_kpis, "SELECT * FROM Ponderaciones WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1");
							$dataPonderacion = mysqli_fetch_array($queryPonderacion);
							$estrategicos = round(($sumAvanceE / $contKpiE) * ($dataPonderacion["estrategico"] / 100), 2);

							$tacticos = round(($sumAvanceT / $contKpiT) * ($dataPonderacion["tactico"] / 100), 2);

							if (is_nan($estrategicos) || is_infinite($estrategicos)) {
								$estrategicos = 0;
							}
							if (is_nan($tacticos) || is_infinite($tacticos)) {
								$tacticos = 0;
							}
							$avanceGeneral = $estrategicos + $tacticos;
						}
					}

					$porciento = $avanceGeneral;
                    if (is_nan($porciento) || is_infinite($porciento)) {
						$porciento = 0;
					}

					if ($porciento > 100) {
						$porcentaje_barra = 100;
						// $porciento = 100;
					} else {
						$porcentaje_barra = $porciento;
					}

					$escalaBarra = EscalaColor(round($porciento), $_SESSION['id_empresa'], $connect_valentina);
					$back_colorBarra = "background-color:" . $escalaBarra['color_bg'] . " !important";
					// echo 'porcentaje' . $porciento;
					?>
					<!-- <script>
						$(document).ready(function() {
							$("#progreso_desempenio").html('<div class="progresos" data-bs-toggle="tooltip" align="center">' +
								'<h1 style="font-size: 3.5rem;color: black !important;"><?php //echo round($porciento, 2); ?> %</h1></div>' +
								'<div class="progress-bar bg-success" role="progressbar" style=" width: <?php //echo round($porcentaje_barra, 2); ?>%; <?php //echo $back_colorBarra; ?>;opacity: 0.3;z-index: 2;margin-top: -76px;height: 70px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">' +
								'</div><br>'
							);
						});
					</script> -->
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header">
					<?php include("views/kpis_pc/seguimiento_equipo/kpis.php"); ?>
				</div>
			</div>
		</div>
	</div>

</div>
<script type="text/javascript">
	$(document).ready(function() {
		$('#kpis_equipo').DataTable({
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
</script>