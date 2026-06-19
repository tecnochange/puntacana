<script>
	$(document).ready(function() {
		$('#menuCompetencias').collapse();
		$('#bt_competencias_ciclo').addClass('active');
	});
</script>

<?php
$hoy = date("Y-m-d H:i:s");
//CONSULTA PARA NUEVO CLIENTE
//CONSULTA PARA NUEVO CLIENTE

if ($_POST["guardar_configurar"] != "") {
    $_SESSION["anio_ciclo"] = $_POST["anio_ciclo"];
    $_SESSION["ciclo"] = $_POST["ciclo"];
    echo '<script> window.location = "?pg=competencias/configurar";</script>'; //para evitar reinsersion  
}

?>

<div class="container">
    
    <div class="card">
        <div class="card-header">
            <h3>Seleccionar Ciclo</h3>
        </div>
        <div class="card-body">
            <form action="" method="post">
            <input type="hidden" name="guardar_configurar" value="true" />
            <div class="row">
                <div class="col-md-6 mb-2" >
                    <lable>Año *</lable>
                    <select class="form-control form-control-sm" name="anio_ciclo" id="anio_ciclo" required onchange="ValidarCiclo(this.value)" >
                                    <option value="">Por Año...</option>
                                    <?php
                                    foreach ($Array_Anio as $anio) {
                                        if ($_SESSION["anio_ciclo"] ==  $anio[0]) {
                                            echo '<option value="'.$anio[0].'" selected>'.$anio[1].'</option>';
                                        } else {
                                            echo '<option value="'.$anio[0].'">'.$anio[1].'</option>';
                                        }
                                    }
                                    ?>
                    </select>
                </div>
                <div class="col-md-6 mb-2">
                    <lable>Ciclo *</lable>
                    <select class="form-control form-control-sm" name="ciclo" id="ciclo" >
                                    <option value="">Por Ciclo...</option>
                                    <?php
                                    $query = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '".$_SESSION["anio_ciclo"]."' ");
                                    while ($dataCiclos = mysqli_fetch_array($query)) {
                                        if ($_SESSION["ciclo"] ==  $dataCiclos["id"]) {
                                            echo '<option value="' . $dataCiclos["id"] . '" selected>' . $dataCiclos["nombre"] . '</option>';
                                        } else {
                                            echo '<option value="' . $dataCiclos["id"] . '">' . $dataCiclos["nombre"] . '</option>';
                                        }
                                    }
                                    ?>
                    </select>
                </div>
                <div class="col-md-12 mb-2" >
                    <button type="submit" class="btn btn-success btn-block ">
                        Seleccionar
                    </button>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>

<script>
    var api = '<?php echo $url; ?>api/competencias/';
    function ValidarCiclo(anio){

        $.ajax({
				url: api + 'validar_ciclo.php',
				type: 'post',
				data: {
					anio: anio,
					id_empresa: <?php echo $user_log["id_empresa"] ?>
				},
			}).done(function(resp) {
                $("#ciclo").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});

        
    }
</script>


  
