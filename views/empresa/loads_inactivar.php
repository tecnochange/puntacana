<script>
	$(".menu_section").addClass("active");
	// $("#nav_empresa").addClass("active");
	jQuery("#menu_empresa").css("display", "none");
	$("#bt_admin_loads_eliminar").addClass("current-page");
</script>

<?php
$hoy = date("Y:m:d H:i:s");
//CONSULTA PARA NUEVO CLIENTE
//CONSULTA PARA NUEVO CLIENTE

//CONSULTA PARA NUEVO CLIENTE
//CONSULTA PARA NUEVO CLIENTE
if ($_POST["base_parse"] != "") {
	$count = 1;
	$filas = json_decode($_POST["base_parse"]);

	$total = 0;
	foreach ($filas as &$fila) {

		if ($count >= 2) {

			if ($fila[0]) {

				$qA = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE documento = '" . $fila[0] . "' ");
				if ($qA->num_rows > 0) {
					$dataCol = mysqli_fetch_array($qA);

					$sentencia = "
						UPDATE Empleados SET estado = '2' WHERE id = '" . $dataCol["id"] . "'
						";
					mysqli_query($connect_valentina, $sentencia);
				}
			}
		}

		$count++;
	}

	echo '<script> location.href = "?pg=administrar/colaboradores"; </script>';
}

?>


<?php
$hoy = date("Y:m:d H:i:s");
$querySM46 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 4 AND id_submenu = 25");
$dataSM46 = mysqli_fetch_array($querySM46);
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
					delimiter: ",",
					complete: pintar_tabla,
					encoding: "ISO-8859-1"
				},
				before: function(file, inputElem) {
					//data_csv(file, inputElem);
					//console.log("Parsing file...", file);
					//console.log(inputElem);
				},
				error: function(err, file) {
					console.log("ERROR:", err, file);
				},
				complete: function(result) {
					//console.log("Done with all files");
					//console.log(result);
					$("#archivo_cargado_tmp").html("");
				}
			});
		}
	}



	//RONDAS 2
	function pintar_tabla(results) {

		$("#table_validar").html("");
		data = results.data;
		cont = 1;
		cont_item = 1;
		for (i = 0; i < data.length; i++) {

			if (cont >= 2) {

				id_colaborador = 0;
				color = "";
				checked = '';

				console.log(JSON.stringify(data[i][10]));

				$("#table_validar").append('<tr>');
				$("#table_validar").append('<td>' + cont_item + '</td>');
				$("#table_validar").append('<td>' + data[i][0] + '</td>');
				$("#table_validar").append('<td>' + data[i][1] + '</td>');
				$("#table_validar").append('</tr>');
				cont_item++;
			}

			cont++;

		}

		$("#base_parse").val(JSON.stringify(data));
	}

	function Reset() {
		$("#table_validar").html("");
		$("#btn_subir").hide();
		$("#tabla_previo").hide();
	}
</script>

<style>
	.card-footer {
		background-color: #FFFFFF !important;
	}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #FFFFFF !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-dice-d20" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM46["nombre"]; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
	<?php echo $respuesta; ?>
	<section style="display:flex; flex-direction:column; align-items:center; justify-content:center; height:80vh; text-align:center; background:#fff; color:#333; padding:2rem; box-shadow:0 0 10px rgba(0,0,0,0.1); max-width:600px; margin:auto; border-radius:8px;">
		<h1 style="font-size:2rem; margin-bottom:1rem;">🔧 En Mantenimiento</h1>
		<p style="font-size:1.2rem; margin:0;">Este módulo se encuentra temporalmente fuera de servicio por tareas de mantenimiento. Por favor, vuelve a intentarlo más tarde.</p>
	</section>
	<!-- <div class="row">
		<div class="col-md-12" align="left" style="padding-left: 20px; padding-right: 20px;">

			<div class="card" style=" margin-top:15px; margin-bottom:15px">
				<div class="card-body">


					<div class="row">
						<div class="col-md-12">
							<div align="right">
								<a href="<?php echo $url; ?>/Archivo_de_ejemplo_inactivar.csv" target="_blank">
									<input type="button" class="btn btn-warning btn-sm" value="Descargar archivo de ejemplo aquí" />
								</a>


							</div>

							<h5 class="card-title">
								Inactivar masiva de colaboradores
							</h5>
							<p class="card-text">
								Aquí podrá cargar un archivo .csv con la lista de colaboradores.<br /><br />
								Seleccione el archivo <b>.csv</b> y revise que los datos se encuentren correctamente configurados,
								luego de clic en el botón <b>Relacionar colaboradores</b> para iniciar con el proceso.<br />
								<b style="color: #fc0758;">* Puede realizar este proceso tantas veces sea nesesario antes de guardar los colaboradores. *</b> <br />
								<b style="color: #fc0758;">* Recuerde que los documento duplicados no serán relacionado en la base de datos. *</b> <br />
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


				</div>


				<div class="card-footer">
					<input name="guardar_colaboradores" type="hidden" value="true" />
					<table class="table" style="margin-top:15px; display:none" id="tabla_previo">
						<thead class="thead-dark">
							<tr>
								<th>#</th>
								<th>Documento</th>
								<th>Nombre</th>
							</tr>
						</thead>

						<tbody id="table_validar">
						</tbody>
					</table>

					<!-- FILTRO EMPRESA -->
					<form action="" method="post">
						<div class="row" style="margin-bottom:20px; display:none" id="btn_subir">
							<div class="col-md-12">
								<input name="id_proyecto" type="hidden" value="<?php echo $_GET["id"]; ?>" />
								<input id="base_parse" name="base_parse" type="hidden" />
								<button type="submit" class="btn btn-primary btn-md btn-block">
									<i class="fa fa-upload"></i> Inactivar Masivo
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div> -->
</div>