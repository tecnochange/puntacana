
<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_configurar').addClass('active');
});
</script>

<?php 

if( $_POST["guardar_formulario"] ){

    $skin = json_encode($_POST["conf"], JSON_UNESCAPED_UNICODE);
    $sentencia = "
    UPDATE Empresas SET skin_empresa = '".$skin."'  WHERE id = '".$user_log["id_empresa"]."'
    ";
    $queryColaborador = mysqli_query($connect_admin, $sentencia);
    
}

$queryConfig = mysqli_query($connect_admin, "SELECT * FROM Empresas WHERE id = '".$user_log["id_empresa"]."' ");
$dataConfig = mysqli_fetch_array($queryConfig);

$config = json_decode($dataConfig["skin_empresa"], true);
//print_r($config);
?>


<div class="container">
    <form action="" method="POST">
    <input type="hidden" name="guardar_formulario" value="true">
    <div class="card">
        <div class="card-header">
            <h3>Personalizar Apariencia</h3>
        </div>
        <div class="card-body">

        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="">Cabecera</label>
                <input type="color" class="form-control" name="conf[apariencia][cabecera]" value="<?php echo $config["apariencia"]["cabecera"] ?>">
            </div>

            <div class="col-md-3 mb-3">
                <label for="">Menú Lateral</label>
                <input type="color" class="form-control" name="conf[apariencia][lateral]" value="<?php echo $config["apariencia"]["lateral"] ?>">
            </div>

            <div class="col-md-3 mb-3">
                <label for="">Background</label>
                <input type="color" class="form-control" name="conf[apariencia][background]" value="<?php echo $config["apariencia"]["background"] ?>">
            </div>

            <div class="col-md-3 mb-3">
                <label for="">Fondo Modales</label>
                <input type="color" class="form-control" name="conf[apariencia][modal]" value="<?php echo $config["apariencia"]["modal"] ?>">
            </div>

            <div class="col-md-12 mb-3">
                <hr>
                <h5>Componentes</h5>
            </div>

            <div class="col-md-3 mb-3">
                <label for="">Fondo Cabeceras Tarjetas</label>
                <input type="color" class="form-control" name="conf[componentes][cabecera_card]" value="<?php echo $config["componentes"]["cabecera_card"] ?>">
            </div>

            <div class="col-md-3 mb-3">
                <label for="">Fondo Tarjetas</label>
                <input type="color" class="form-control" name="conf[componentes][bg_card]" value="<?php echo $config["componentes"]["bg_card"] ?>">
            </div>

            <div class="col-md-3 mb-3">
                <label for="">Fuente Principal</label>
                <input type="color" class="form-control" name="conf[componentes][fuente_principal]" value="<?php echo $config["componentes"]["fuente_principal"] ?>">
            </div>

            <div class="col-md-3 mb-3">
                <label for="">Titulos</label>
                <input type="color" class="form-control" name="conf[componentes][titulo]" value="<?php echo $config["componentes"]["titulo"] ?>" >
            </div>

            <div class="col-md-3 mb-3">
                <label for="">Subtitulos</label>
                <input type="color" class="form-control" name="conf[componentes][subtitulos]" value="<?php echo $config["componentes"]["subtitulos"] ?>" >
            </div>

            <div class="col-md-12 mb-3">
                <hr>
                <h5>Botones</h5>
            </div>

            <div class="col-md-3 mb-3">
                <label for="">Botón Primario</label>
                <input type="color" class="form-control" name="conf[botones][primario]" value="<?php echo $config["botones"]["primario"] ?>">
            </div>

            <div class="col-md-3 mb-3">
                <label for="">Botón Secundario</label>
                <input type="color" class="form-control" name="conf[botones][secundario]" value="<?php echo $config["botones"]["secundario"] ?>">
            </div>

            <div class="col-md-3 mb-3">
                <label for="">Botón Oscuro</label>
                <input type="color" class="form-control" name="conf[botones][oscura]" value="<?php echo $config["botones"]["oscura"] ?>">
            </div>

           



            <div class="col-md-12 mb-3">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </div>

        </div>
    </div>
    </form>
</div>





