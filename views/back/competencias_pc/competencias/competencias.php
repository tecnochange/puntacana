<script>
	$(document).ready(function() {
		$(".menu_section").addClass("active");
		// $("#nav_competencias").addClass("active");
		jQuery("#menu_competencias").css("display", "none");
		$("#bt_comp_competencias").addClass("current-page");
	});
</script>

<?php
$hoy = date("Y-m-d H:i:s");

function eliminar_tildes($archivo){

    $cadena = $archivo;
    $cadena = str_replace(
        array('á', 'à', 'ä', 'â', 'ª','Ã¡', 'Á', 'À', 'Â', 'Ä', 'Ã¡','Ã','Ã','ÃƒÁ'),
        array('á', 'á', 'á', 'á', 'á', 'á','Á', 'Á', 'Á', 'Á', 'Á','Á','Á','Á'),
        $cadena
    );

    $cadena = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë', 'Ã©','Ã‰'),
        array('e', 'e', 'e', 'e', 'É', 'É', 'É', 'É', 'é','É'),
        $cadena
    );

    $cadena = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î', 'Ã­','Ã'),
        array('i', 'i', 'i', 'i', 'Í', 'Í', 'Í', 'Í', 'Í', 'Í'),
        $cadena
    );

    $cadena = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô', 'Ã³','Ã“','Ã“','oÍ','ÃƒÁ“'),
        array('ó', 'ó', 'ó', 'ó', 'Ó', 'Ó', 'Ó', 'Ó', 'ó','Ó','Ó','ó','Ó'),
        $cadena
    );

    $cadena = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü', 'Ãº','Ãš'),
        array('u', 'u', 'u', 'u', 'Ú', 'Ú', 'Ú', 'Ú', 'ú','Ú'),
        $cadena
    );

    $cadena = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç','Ã±','ÃƒÁ±','Ã‘'),
        array('n', 'Ñ', 'c', 'C','ñ','ñ','Ñ'),
        $cadena
    );
    return $cadena;
}

//CONSULTA PARA NUEVO CLIENTE
//CONSULTA PARA NUEVO CLIENTE
if ($_POST["nombre"] != "") {
	if ($_POST["id_competencia"] != "") {
		mysqli_query($connect_valoracion, "UPDATE Competencias SET nombre = '" . $_POST["nombre"] . "', id_tipo = '" . $_POST["id_tipo"] . "',  
			definicion = '" . $_POST["definicion"] . "' WHERE id = '" . $_POST["id_competencia"] . "'  ");
	} else {
		mysqli_query($connect_valoracion, "INSERT INTO Competencias (id_empresa, anio, id_ciclo, nombre, definicion, id_tipo, created_at, update_at) 
			VALUES 
			( '" . $_SESSION['id_empresa'] . "', '" . $_SESSION['anio_ciclo'] . "', '" . $_SESSION['ciclo'] . "', '" . $_POST["nombre"] . "', '" . $_POST["definicion"] . "', '" . $_POST["id_tipo"] . "', '" . $hoy . "', '" . $hoy . "' ) ");


		$id_l = mysqli_insert_id($connect);


		$i = 0;
		foreach ($_POST["id_nivel"] as &$nivel) {
			mysqli_query($connect_valoracion, "INSERT INTO Competencias_Niveles (id_competencia, id_nivel, definicion, created_at, update_at) 
				VALUES 
				('" . $id_l . "', '" . $nivel . "', '" . $_POST["definicion"][$i] . "', '" . $hoy . "', '" . $hoy . "' ) ");
			//print_r($nivel." - ". $_POST["definicion"][$i]."<hr>");
			$i++;
		}
	}
	$respuesta = '
			<div class="alert alert-success" role="alert" style="margin-top:8px">
			  Información Guardada.
			</div>
		';
}

//TIPOS
$arrayTipos = array();
$queryT = mysqli_query($connect_valoracion, "SELECT * FROM Tipos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ORDER BY id DESC ");
while ($dataT = mysqli_fetch_array($queryT)) {
	array_push($arrayTipos, array($dataT["id"], $dataT["nombre"]));
}

//NIVELES
$arrayNiveles = array();
$queryN = mysqli_query($connect_valoracion, "SELECT * FROM Niveles WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ORDER BY id DESC ");
while ($dataN = mysqli_fetch_array($queryN)) {
	array_push($arrayNiveles, array($dataN["id"], $dataN["nombre"]));
}
?>

<?php include("views/competencias_pc/layouts/ficha_competencia.php"); 
$querySM12 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 1 AND id_submenu = 2");
$dataSM12 = mysqli_fetch_array($querySM12);
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
                        <h4><i class="fas fa-users" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM12["nombre"]; ?> <?php echo $_SESSION["anio_ciclo"]; ?></b> Ciclo: <b><?php echo $dataCicloVal["nombre"]; ?></b></h4>
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
			<div class="card">
				<div class="card-header">
					<ul class="nav nav-pills justify-content-center" style="margin-bottom: 10px;">
						<li class="nav-item">
							<a class="nav-link " href="?pg=competencias_pc/competencias/competencias_tipos">Tipos</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="?pg=competencias_pc/competencias/competencias_niveles">Niveles</a>
						</li>

						<li class="nav-item">
							<a class="nav-link active" href="?pg=competencias_pc/competencias/competencias">Competencias</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="?pg=competencias_pc/competencias/competencias_informes">Informes</a>
						</li>

						<li class="nav-item">
							<a class="nav-link" href="?pg=competencias_pc/competencias/competencias_acciones">Acciones de Desarrollo</a>
						</li>
					</ul>
				</div>
				<div class="card-body">
					<?php if ($_SESSION['anio_ciclo'] && $_SESSION['ciclo']) { ?>
						<div class="table-responsive">
							<table class="table">
								<thead class="table-dark" id="competenciasTable">
									<tr>
										<th scope="col" style="width:50px">#</th>
										<th scope="col">Año Lic.</th>
										<th scope="col">Tipo</th>
										<th scope="col">Nivel</th>
										<th scope="col">Indicador</th>
										<th scope="col">Comportamiento / Pregunta</th>
										<th scope="col" style="width: 120px; text-align:center">
											<button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target=".modal_competencia" onclick="Seter_Ficha()" title="Crear nueva competencia">
												Nueva
											</button>
										</th>
									</tr>
								</thead>

								<tbody>
									<?php

									$count = 1;
									$query = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE anio = '" . $_SESSION['anio_ciclo'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "'  ORDER BY id ASC ");

									while ($data = mysqli_fetch_array($query)) {

										$text_tipo = '';
										foreach ($arrayTipos as &$tipo) {
											if ($data["id_tipo"] == $tipo["0"]) {
												$text_tipo = $tipo["1"];
											}
										}

										echo '
				<tr>
					<th scope="row">' . $count . '</th>
					<td>' . $_SESSION['anio_ciclo'] . '</td>
					<td><b>' . eliminar_tildes($text_tipo) . '</b></td>
					<td colspan="3">
						<h4>' . eliminar_tildes(str_replace('LÁDERES','LÍDERES',$data["nombre"])) . '</h4>
						' . eliminar_tildes($data["definicion"]) . '
					</td>
					<td align="center">
						<button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target=".modal_competencia" onclick="Ficha_Competencia(' . $data["id"] . ')">
							<i class="bx bxs-edit"></i>
						</button>
						<a href="?pg=competencias_pc/competencias/preguntas_competencia&id=' . $data["id"] . '">
						<button type="button" class="btn btn-info btn-sm">
							<i class="bx bx-abacus"></i>
						</button>
						</a>
					</td>
				</tr>
				';
										$count++;
										// echo "SELECT * FROM Competencias_Niveles WHERE id_competencia = '" . $data["id"] . "' ORDER BY id DESC<br>";
										$queryNiveles = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id_competencia = '" . $data["id"] . "' ORDER BY id DESC ");
										while ($dataNiveles = mysqli_fetch_array($queryNiveles)) {

											$text_nivel = '';
											foreach ($arrayNiveles as &$nivel) {
												if ($dataNiveles["id_nivel"] == $nivel["0"]) {
													$text_nivel = $nivel["1"];
												}
											}
// echo "SELECT * FROM Competencias_Preguntas WHERE id_nivel_competencia = '" . $dataNiveles["id"] . "' ORDER BY id DESC<br>";
											$queryPreguntas = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Preguntas WHERE id_nivel_competencia = '" . $dataNiveles["id"] . "' ORDER BY id DESC ");
											while ($dataPreguntas = mysqli_fetch_array($queryPreguntas)) {

												echo '
						<tr>
							<td scope="row"></td>
							<td></td>
							<td></td>
							<td><b>' . eliminar_tildes($text_nivel) . '</b></td>
							<td>' . eliminar_tildes($dataPreguntas["indicador"]) . '</td>
							<td>' . eliminar_tildes($dataPreguntas["pregunta"]) . '</td>
						</tr>
						';
											}
										}
									}
									?>
								</tbody>
							</table>
						</div>
					<?php } else { ?>
						<div class="alert alert-warning" role="alert" align="center">
							Primero seleccione un ciclo
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
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

	var Cont_Modal = "";
	$(document).ready(function() {
		Cont_Modal = $("#cont_modal_comp").html();
	});


	var api = '<?php echo $url; ?>api/competencias_pc/';

	function Ficha_Competencia(id) {
		$('#lista_niveles').html('');
		jQuery.ajax({
				url: api + "ficha_competencia.php",
				type: 'post',
				data: {
					id: id
				},
			}).done(function(resp) {
				$("#cont_modal_comp").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});
	}

	function Seter_Ficha() {
		$("#cont_modal_comp").html(Cont_Modal);
	}
</script>


<style>
	.checkbox_list {
		width: 18px;
		height: 18px;
	}

	.card,
	.card-body,
	.card-header,
	.card-footer {
		background-color: #FFFFFF !important;
	}
</style>
