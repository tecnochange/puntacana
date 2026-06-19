<script>
	$(document).ready(function() {
		$('#menuCompetencias').collapse();
		$('#bt_competencias_perfiles').addClass('active');
	});
</script>

<?php
$hoy = date("Y-m-d H:i:s");
$id = $_GET["id"];

//CONSULTA PARA NUEVO CLIENTE
//CONSULTA PARA NUEVO CLIENTE
if ($_POST["actualizar"] != "") {

	$queryTmp = mysqli_query($connect_valoracion, "SELECT * FROM Perfiles_Cargos WHERE id_cargo = '" . $id . "' AND anio = '" . $_SESSION["anio_ciclo"] . "' ");
	if ($queryTmp->num_rows == 0) {
		mysqli_query($connect_valoracion, "INSERT INTO Perfiles_Cargos (anio, id_ciclo, id_empresa, id_cargo, perfiles, created_at) 
            VALUES ('" . $_SESSION["anio_ciclo"] . "', '" . $_SESSION["ciclo"] . "', '" . $user_log["id_empresa"] . "', '" . $id . "', '', '" . $hoy . "' ) ");
	}

	$peril = '';
	foreach ($_POST["niveles"] as $perfiles) {
		if ($peril == "") {
			$peril = $perfiles;
		} else {
			$peril .= "," . $perfiles;
		}
	}

	mysqli_query($connect_valoracion, "UPDATE Perfiles_Cargos SET perfiles = '" . $peril . "' WHERE id_cargo = '" . $id . "' AND anio = '" . $_SESSION["anio_ciclo"] . "'  ");

	$respuesta = '
			<div class="alert alert-success" role="alert" style="margin-top:8px">
			  Información actualizada.
			</div>
		';
}


//CARGAMOS LOS NIVELES
$arrayTipos = array();
$queryT = mysqli_query($connect_valoracion, "SELECT * FROM Tipos WHERE id_empresa = '" . $user_log["id_empresa"] . "' ORDER BY id DESC ");
while ($dataT = mysqli_fetch_array($queryT)) {
	array_push($arrayTipos, array($dataT["id"], $dataT["nombre"]));
}

//CARGAMOS LOS NIVELES
$arrayNiveles = array();
$queryN = mysqli_query($connect_valoracion, "SELECT * FROM Niveles WHERE id_empresa = '" . $user_log["id_empresa"] . "' ORDER BY id DESC ");
while ($dataN = mysqli_fetch_array($queryN)) {
	array_push($arrayNiveles, array($dataN["id"], $dataN["nombre"]));
}

$queryCargo = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE id = '" . $id . "' ");
$dataCargo = mysqli_fetch_array($queryCargo);

$queryC = mysqli_query($connect_valoracion, "SELECT * FROM Perfiles_Cargos WHERE id_cargo = '" . $id . "' AND anio = '" . $_SESSION["anio_ciclo"] . "' ");
$dataC = mysqli_fetch_array($queryC);
$perfiles = explode(",", $dataC["perfiles"]);

?>

<style>
	.checkbox {
		width: 22px;
		height: 22px;
		margin-right: 20px;
	}

	.card,
	.card-header,
	.card-body {
		background-color: #FFFFFF !important;
	}
</style>

<?php include("views/layouts/ficha_competencia.php"); ?>

<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">

	<?php echo $respuesta; ?>

	<div class="row">
		<div class="col-md-12">
			<div class="card">
				<div class="card-header" style="background-color: #FFFFFF !important;">
					<div class="row">
						<div class="col-md-8" style="text-align: start !important;">
							<h3>Edición Perfil Cargo</h3>
						</div>
						<div class="col-md-4" align="right">
							<a href="<?php echo $url; ?>?pg=competencias/perfiles" class="btn btn-success">Volver</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Breadcrumb -->
	<nav aria-label="breadcrumb" style="margin-top: 15px;">
		<ol class="breadcrumb">
			<li class="breadcrumb-item" aria-current="page"><a href="<?php echo $url; ?>?pg=competencias/perfiles">Perfiles</a></li>
			<li class="breadcrumb-item active" aria-current="page">Detalle</li>
		</ol>
	</nav>

	<div class="card">
		<div class="card-header">
			<h1><?php echo $dataCargo["nombre"]; ?></h1>
		</div>
		<div class="card-body">
			<form action="" method="post">
				<input type="hidden" name="actualizar" value="true">

				<div class="table-responsive">
					<table class="table">
						<thead class="thead-dark">
							<tr>
								<th scope="col" style="width:50px">#</th>
								<th scope="col">Tipo</th>
								<th scope="col">Competencia</th>
								<th scope="col">Nivel</th>
								<th scope="col">Año Lic.</th>
							</tr>
						</thead>

						<tbody>
							<?php
							$count = 1;
							$query = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id_empresa = '" . $user_log["id_empresa"] . "' ORDER BY id DESC ");
							while ($data = mysqli_fetch_array($query)) {

								$text_tipo = '';
								foreach ($arrayTipos as &$tipo) {
									if ($data["id_tipo"] == $tipo["0"]) {
										$text_tipo = $tipo["1"];
									}
								}

								$lista_niveles = '';
								$queryNiveles = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id_competencia = '" . $data["id"] . "' ORDER BY id_nivel DESC ");
								while ($dataNiveles = mysqli_fetch_array($queryNiveles)) {

									$text_nivel = '';
									foreach ($arrayNiveles as &$nivel) {
										if ($dataNiveles["id_nivel"] == $nivel["0"]) {
											$text_nivel = $nivel["1"];
										}
									}

									$checkbox = '<input type="checkbox" class=" comp_' . $data["id"] . ' nivl_' . $dataNiveles["id_nivel"] . ' checkbox " name="niveles[]" value="' . $dataNiveles["id"] . '" onClick="SelectCheck(this, ' . $data["id"] . ')" >';

									foreach ($perfiles as $prf) {
										if ($prf == $dataNiveles["id"]) {
											$checkbox = '<input type="checkbox" class=" comp_' . $data["id"] . ' nivl_' . $dataNiveles["id_nivel"] . ' checkbox " name="niveles[]" value="' . $dataNiveles["id"] . '" checked onClick="SelectCheck(this, ' . $data["id"] . ')" >';
										}
									}

									$lista_niveles .= '<div class="item_padre">' . $text_nivel . " " . $checkbox . '</div>';
								}

								if ($data["anio"] == $_SESSION['anio_ciclo']) {
									echo '
										<tr>
											<td scope="row">' . $count . '</td>
											<td>' . eliminar_tildes($text_tipo) . '</td>
											<td>' . eliminar_tildes($data["nombre"]) . '</td>
											<td>' . eliminar_tildes($lista_niveles) . '</td>
											<td>' . $_SESSION['anio_ciclo'] . '</td>
										</tr>
									';
								} else {
									echo '
										<tr style="visibility: collapse;" >
											<td scope="row">' . $count . '</td>
											<td>' . eliminar_tildes($text_tipo) . '</td>
											<td>' . eliminar_tildes($data["nombre"]) . '</td>
											<td>' . eliminar_tildes($lista_niveles) . '</td>
											<td>' . $_SESSION['anio_ciclo'] . '</td>
										</tr>
									';
								}
								$count++;
							}
							?>
						</tbody>
					</table>
				</div>

				<button type="submit" class="btn btn-success btn-block" style="margin-bottom: 30px">
					Actualizar
				</button>
			</form>
		</div>
	</div>
</div>

<script>
	function SelectCheck(elem, id) {

		estado = $(elem).prop('checked');
		if (estado == true) {
			//$(".comp_"+id).removeAttr('checked');
			//$(".comp_"+id).prop('disabled', !this.checked);
			//event.preventDefault();
			$(".comp_" + id).prop('checked', false);
			$(elem).prop('checked', true);
		} else {
			$(".comp_" + id).prop('checked', false);
		}
	}
</script>

<script>
	$(document).ready(function() {

		$("#myInput").on("keyup", function() {
			var value = $(this).val().toLowerCase();

			$(".myTable").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
				$(this).next(".insumos").toggle($(this).text().toLowerCase().indexOf(value) > -1);
				//$(this).next(".insumos").toggle();
				//$(this).parent().next().hide();
			});

			$(".name_fil_off").parent().show();

		});

	});


	var api = 'https://wandtalent.com/seleccion/superadmin/api/';

	function Ficha_Competencia(id) {
		$('#lista_niveles').html('');
		jQuery.ajax({
				url: api + "ficha_competencia.php",
				type: 'post',
				data: {
					id: id,
					url: "?pg=competencias"
				},
			}).done(function(resp) {
				$("#xscript").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});
	}

	function Seter_Ficha() {

		$('[name="nombre"]').val("");
		$('[name="definicion"]').val("");
		$('[name="id_tipo"]').val("");

		$('[name="id_competencia"]').val("");

	}


	function Marcar_Paquete(classe) {

		$(".checkbox").attr('checked', false);

		$(".item_padre").hide();
		$(".nivl_" + classe).prop('checked', true);
		$(".nivl_" + classe).parent().show();

		if (!classe) {
			$(".item_padre").show();
		}
	}
</script>


<style>
	.checkbox_list {
		width: 18px;
		height: 18px;
	}
</style>