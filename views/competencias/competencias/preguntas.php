<script>
	$(document).ready(function() {
		$('#menuCompetencias').collapse();
		$('#bt_tipo_competencias').addClass('active');
	});
</script>

<?php
$hoy = date("Y-m-d H:i:s");
$id = $_GET["id"];

//PARA GUARDAR LOS NIVELES Y PREGUNTAS

if ($_POST["nivel"] != "") {

	$id_nivel_competencia = 0;
	//SI ES UN NIVEL EXISTENTE
	if ($_POST["id_nivel_competencia"] != "") {
		mysqli_query($connect_valoracion, "UPDATE Competencias_Niveles SET id_nivel = '" . $_POST["nivel"] . "' WHERE id = '" . $_POST["id_nivel_competencia"] . "' ");
		$id_nivel_competencia = $_POST["id_nivel_competencia"];
	} else {
		$queryValidar = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles 
            WHERE id_competencia = '" . $id . "' AND id_nivel = '" . $_POST["nivel"] . "' ");
		if ($queryValidar->num_rows == 0) {
			mysqli_query($connect_valoracion, "INSERT INTO Competencias_Niveles (id_empresa, anio, id_competencia, id_nivel, created_at, update_at) 
                VALUES 
                ('" . $_SESSION['id_empresa'] . "', '" . $_SESSION['anio_ciclo'] . "', '" . $id . "', '" . $_POST["nivel"] . "', '" . $hoy . "', '" . $hoy . "' ) ");
			$id_nivel_competencia = mysqli_insert_id($connect_valoracion);
		}
	}
	//RECORREMOS LA PREGUNTAS SI EXISTEN		
	$i = 0;
	if ($id_nivel_competencia != 0) {
		foreach ($_POST["indicador"] as &$indicador) {

			if ($_POST["id_pregunta"][$i] != "") {
				mysqli_query($connect_valoracion, "UPDATE Competencias_Preguntas SET indicador = '" . $indicador . "', pregunta = '" . $_POST["pregunta"][$i] . "', 
                    contra_pregunta = '" . $_POST["contrapregunta"][$i] . "' WHERE id = '" . $_POST["id_pregunta"][$i] . "' ");
			} else {
				mysqli_query($connect_valoracion, "INSERT INTO Competencias_Preguntas (id_empresa, anio, id_competencia , id_nivel_competencia, indicador, pregunta, contra_pregunta, created_at, update_at) 
                    VALUES 
                    ('" . $_SESSION['id_empresa'] . "', '" . $_SESSION['anio_ciclo'] . "', '" . $id . "', '" . $id_nivel_competencia . "', '" . $indicador . "', '" . $_POST["pregunta"][$i] . "', '" . $_POST["contrapregunta"][$i] . "', '" . $hoy . "', '" . $hoy . "' ) ");
			}
			$i++;
		}
	}
} 

//CARGAMOS LOS TIPOS
$arrayTipos = array();
$queryT = mysqli_query($connect_valoracion, "SELECT * FROM Tipos WHERE id_empresa = '" . $user_log['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ORDER BY id DESC ");
while ($dataT = mysqli_fetch_array($queryT)) {
	array_push($arrayTipos, array($dataT["id"], $dataT["nombre"]));
}

//CARGAMOS LOS NIVELES
$arrayNiveles = array();
$queryN = mysqli_query($connect_valoracion, "SELECT * FROM Niveles WHERE id_empresa = '" . $user_log['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ORDER BY id DESC ");
while ($dataN = mysqli_fetch_array($queryN)) {
	array_push($arrayNiveles, array($dataN["id"], $dataN["nombre"]));
}

$query = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $id . "'  ");
$data = mysqli_fetch_array($query);

$text_tipo = "";
foreach ($arrayTipos as &$valor) {
	if ($valor[0] == $data["id_tipo"]) {
		$text_tipo = $valor[1];
	}
}
?>

<style>
	.checkbox_list {
		width: 18px;
		height: 18px;
	}

	.form-control {
		display: inline-table;
		font-size: 14px;
		overflow: hidden;
	}

	.card,
	.card-body,
	.card-header,
	.card-footer {
		background-color: #FFFFFF !important;
	}
</style>

<div class="container pb-4" >

	<?php echo $respuesta; ?>

	<!-- TITULO -->
	<div class="card mb-3">
		<div class="card-header">
			<h3>Preguntas Competencias</h3>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb">
					<li class="breadcrumb-item" aria-current="page"><a href="?pg=competencias/competencias/competencias" style="font-weight: 700;">Competencias</a></li>
					<li class="breadcrumb-item active" aria-current="page">Preguntas Competencias</li>
				</ol>
			</nav>
		</div>
	</div>

	<div class="row">
		<div class="col-md-12">
			<div class="card">

				<div class="card-header">
					<h3 class="card-title"><?php echo $data["nombre"]; ?> - <?php echo $text_tipo; ?></h3>
					<p class="card-text">
						<b>Definición</b> <br />
						<?php echo $data["definicion"]; ?>
					</p>
				</div>

				<div class="card-body">
					<?php
					$queryNiveles = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id_competencia = '" . $id . "'  ");
					while ($dataNiveles = mysqli_fetch_array($queryNiveles)) {
					?>

						<form action="" method="post">
							<div class="row">
								<div class="col-md-12">
									<h3 style="background-color: #2d323e; color: #fff; padding: 10px;">Nivel Competencia</h3>
									<input type="hidden" name="id_nivel_competencia" value="<?php echo $dataNiveles["id"]; ?>" />
									<select class="form-control" name="nivel">
										<?php
										foreach ($arrayNiveles as &$valor) {
											if ($valor[0] == $dataNiveles["id_nivel"]) {
												echo '<option value="' . $valor[0] . '" selected="selected">' . $valor[1] . '</option>';
											} else {
												echo '<option value="' . $valor[0] . '">' . $valor[1] . '</option>';
											}
										}
										?>
									</select>
								</div>

								<div class="col-md-12">
									<button type="button" class="btn btn-secondary btn-sm" onclick="Nuevo_Grupo(this)" style="margin:10px 0px">
										<i class="fa fa-plus"></i> Agregar indicadores / Pregunta
									</button>
									<?php
									$queryPreguntas = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Preguntas WHERE id_competencia  = '" . $id . "' AND id_nivel_competencia = '" . $dataNiveles["id"] . "'   ");
									while ($dataPregunta = mysqli_fetch_array($queryPreguntas)) {
										echo '
											<div style="margin-bottom:8px">
												<input type="hidden" name="id_pregunta[]" value="' . $dataPregunta["id"] . '"> 
												<button type="button" class="btn btn-danger btn-sm" onclick="Borrar_Pregunta(' . $dataPregunta["id"] . ')" style="float: right;">	
													<i class="bx bx-trash" title="Eliminar"></i>
												</button>
												<textarea class="form-control" name="indicador[]" required="" placeholder="Ingrese Indicador..." style=" width:45%">' . $dataPregunta["indicador"] . '</textarea>
												<textarea class="form-control" name="pregunta[]" required="" placeholder="Ingrese Pregunta..." style=" width:45%">' . $dataPregunta["pregunta"] . '</textarea>
											</div>
										';
									}
									?>
								</div>

								<div class="col-md-12" style="margin-top:10px; margin-bottom:20px; text-align: right;">
									<button type="submit" class="btn btn-primary w-100">Guardar Datos</button>
								</div>
							</div>
						</form>

					<?php } ?>
				</div>

				<div class="card-footer">
					<form action="" method="post">
						<div class="row">
							<div class="col-md-12">
								<h3 style="background-color: #2d323e; color: #FFFFFF !important; padding: 10px;">Nuevo Nivel de Competencia</h3>
								<input type="hidden" name="id_nivel_competencia" value="" />
								<select class="form-control" name="nivel">
									<option value="">Selecciona...</option>
									<?php
									foreach ($arrayNiveles as &$valor) {
										echo '<option value="' . $valor[0] . '">' . $valor[1] . '</option>';
									}
									?>
								</select>
							</div>

							<div class="col-md-12" style="margin-top:15px; margin-bottom:20px; text-align: right;">
								<button type="submit" class="btn btn-danger">Agregar nuevo nivel</button>
							</div>

						</div>
					</form>
				</div>

			</div>
		</div>
	</div>
</div>

<script>
	function Nuevo_Grupo(elem, nivel) {
		grupo = MatrizGrupo();
		$(elem).after(grupo);
	}

	function MatrizGrupo(id_nivel) {
		itemg = '';
		itemg += '<div style="margin-bottom:8px">';
		itemg += '<input type="hidden" name="id_pregunta[]" value="">';
		itemg += '<textarea class="form-control" name="indicador[]" required placeholder="Ingrese Indicador..." style=" width:45%"></textarea>';
		itemg += '<textarea class="form-control" name="pregunta[]" required placeholder="Ingrese Pregunta..." style=" width:45%"></textarea>';
		//itemg +=	'<textarea class="form-control" name="contrapregunta[]" placeholder="Ingrese contra pregunta..." style=" width:33%"></textarea>';
		itemg += '</div> ';
		return itemg;
	}
</script>

<script>
	var api = '<?php echo $url; ?>api/competencias_pc/';

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
				$("#lista_niveles").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});
	}

	function Seter_Ficha() {
		$('#lista_niveles').html(div);

		$('[name="nombre"]').val("");
		$('[name="id_competencia"]').val("");
		$('[name="id_tipo"]').val("");

		$('#new_items').html("");

	}

	var activar = false;

	function Borrar_Pregunta(val) {
		console.log("Borrar Pregunta");
		
		/* if (activar == false) {
			$("#cont_modal_general").html('Estas a punto de eliminar esta pregunta, esta acción es irreversible ¿Estás seguro?<br><br>');
			$("#botones_modal_general").append('<button type="button" class="btn btn-danger" style="margin-right: 10px;" onclick="activar = true; Borrar_Pregunta(' + val + ')">Borrar</button>');
			$("#botones_modal_general").append('<button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Cancelar</button>');
			$("#modal_general").modal('show');
		} else {
			jQuery.ajax({
					url: api + "borrar_pregunta.php",
					type: 'post',
					data: {
						id: val,
						url: "?pg=competencias_pc/competencias/preguntas_competencia&id=<?php echo $id; ?>"
					},
				}).done(function(resp) {
					$("#xscript").html(resp);
				})
				.fail(function(resp) {
					console.log(resp);
				})
				.always(function(resp) {});
		} */
	}
</script>