<script>
	$(".menu_section").addClass("active");
	$("#nav_reportes").addClass("active");
	jQuery("#menu_reportes").css("display", "none");
	$("#bt_okrs_consolidados_areas").addClass("current-page");
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
        $_SESSION['selected_periodo_okr'] = $_POST['periodo'];  // Guardar checkboxes seleccionados en la sesión
    } else {
        // Si no se seleccionan checkboxes, eliminar la sesión para checkboxes
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

$querySM93 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 9 AND id_submenu = 54");
$dataSM93 = mysqli_fetch_array($querySM93);
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
						<h3><i class="fas fa-chart-pie" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo mb_strtoupper($dataSM93["nombre"]); ?> AÑO <?php echo $_SESSION["anio_fill"]; ?></h3>
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
		
		<?php if($_GET["consolidados"] != ""){ ?>removeUrlParameter('consolidados');<?php } ?>	

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
			<?php include("views/okrs_equipos/reportes/filtro_consolidado_areas.php"); ?>
			</div>
		</div>
	</div>
</div>
	

	<br>

	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div id="accordionNivelJerarquico" class="accordion-icons" role="tablist">
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

					

					// $queryVP = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'  AND estado = 1 $filtro_VP ORDER BY nombre ");
					if ($_SESSION["role_plataforma"] == 1) {
						$queryVP = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'  AND estado = 1 $filtro_VP ORDER BY nombre ");
					} else {
						$queryRL = mysqli_query($connect_valentina, "SELECT * FROM Relaciones_Laborales WHERE id_empleado = '" . $_SESSION["id_user"] . "' AND mod_okrs = 'on' AND estado = 1");
						$areasMacro = "";
						if (mysqli_num_rows($queryRL) > 0) {
							$areasMacro = "AND id IN (";
							while ($dataRL = mysqli_fetch_array($queryRL)) {
								$areasMacro .= $dataRL["id_vp"] . ",";
							}
							$areasMacro = substr($areasMacro, 0, -1);
							$areasMacro .= ")";
							$queryVP = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 $areasMacro ORDER BY nombre");
						}
					}

					$contVP = 0;
					while ($dataVP = mysqli_fetch_array($queryVP)) {
						$arrayCol = array();
						include("views/okrs_equipos/consolidados/listado_areas_vp.php");

						$equipo = array_sort($arrayCol, 'promedio', SORT_DESC);
						// $colaboradores = array_slice($equipo, 0, 10);


					?>
						<div class="card mb-0" style="margin-bottom: 10px !important;">
							<div class="card-header" role="tab" id="heading<?php echo $dataVP["id"]; ?>">
								<div class="row" style="align-items: center;">
									<div class="col-md-10">
										<h5><?php echo $dataVP["nombre"]; ?></h5>
									</div>
									<div class="col-md-2" style="text-align:end;">
										<a class="collapsed" data-toggle="collapse" href="#collapseVP_<?php echo $dataVP["id"]; ?>" aria-expanded="false" aria-controls="collapseVP_<?php echo $dataVP["id"]; ?>" id="datosVP_<?php echo $dataVP["id"]; ?>" style="color: black !important;">
										</a>
									</div>
								</div>
							</div>
							<div id="collapseVP_<?php echo $dataVP["id"]; ?>" class="collapse" role="tabpanel" aria-labelledby="heading<?php echo $dataVP["id"]; ?>" data-parent="#accordionNivelJerarquico">
								<?php include("views/okrs_equipos/consolidados/areas_vp.php"); ?>
							</div>
						</div>
					<?php

					}

					?>

				</div>
			</div>
		</div>
	</div>

</div>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		var divElement = document.getElementById("heading<?php echo $_GET["vicepresidencia_"]; ?>");

		if (divElement) {
			divElement.scrollIntoView();
			$("#datosVP_<?php echo $_GET["vicepresidencia_"]; ?>").removeClass("collapsed");
			$("#collapseVP_<?php echo $_GET["vicepresidencia_"]; ?>").addClass("show");

		}

	});
	window.location.hash = "";
</script>
<script>
	<?php if($_GET["vicepresidencia_"] != ""){ ?>removeUrlParameter('vicepresidencia_');<?php } ?>	

	function removeUrlParameter(param) {
		const url = new URL(window.location.href);
		url.searchParams.delete(param);
		window.history.replaceState({}, document.title, url);
	}
	var api = '<?php echo $url; ?>api/okrs/';

	function VerOKRVicepresidencia(id, id_owner, anioFill, empresa, filtro1, filtro2, filtro3, filtro4, filtro5) {
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
		window.open("<?php echo $url; ?>views_okrs/okr_vicepresidencia.php?id=" + id + "&id_owner=" + id_owner + "&anio_fill=" + anioFill + "&id_empresa=" + empresa + "&filtro1=" + filtro1 + "&filtro2=" + filtro2 + "&filtro3=" + filtro3 + "&filtro4=" + filtro4 + "&filtro5=" + filtro5, "GoForAgile", "width=1300, height=900")
	}

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

	function VerOKRArea(id, id_owner, anioFill, empresa, filtro1, filtro2, filtro3, filtro4, filtro5) {
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
		window.open("<?php echo $url; ?>views_okrs/okr_area.php?id=" + id + "&id_owner=" + id_owner + "&anio_fill=" + anioFill + "&id_empresa=" + empresa + "&filtro1=" + filtro1 + "&filtro2=" + filtro2 + "&filtro3=" + filtro3 + "&filtro4=" + filtro4 + "&filtro5=" + filtro5, "GoForAgile", "width=1300, height=900")
	}
</script>