<script>
$(document).ready(function() {
    $('#menuEstrategia').collapse();
    $('#bt_estrategia_configuracion').addClass('active');
});
</script>


<?php
date_default_timezone_set('America/Bogota');
$hoy = date("Y-m-d H:i:s");
//CONSULTA PARA NUEVO CLIENTE
//CONSULTA PARA NUEVO CLIENTE



if ($_POST["guardar_formulario"] != "") {
    mysqli_query($connect_admin, "UPDATE Empresas SET formato_numerico = '".$_POST["formatoNumerico"]."' , anio_curso = '" . $_POST["anio_curso"] . "', mes_inicio = '" . $_POST["mes_inicio"] . "', mes_fin = '" . $_POST["mes_fin"] . "', update_at = '$hoy'  WHERE id = '" . $user_log["id_empresa"] . "'  ");
    $_SESSION["anio_fill"] = $_POST["anio_curso"];
    echo '<script> window.location = "?pg=estrategica/configurar";</script>'; //para evitar reinsersion
}

$query = mysqli_query($connect_admin, "SELECT * FROM Empresas WHERE id = '" . $user_log["id_empresa"] . "'  ");
$data = mysqli_fetch_array($query);

?>

<div class="container">

    <ul class="nav nav-tabs justify-content-center">
        <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="?pg=estrategica/configurar">General</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?pg=estrategica/desempenio">Desempeño</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?pg=estrategica/competencias">Competencias</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?pg=estrategica/kpis">Kpis</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="?pg=estrategica/okrs">Okrs</a>
        </li>
        
    </ul>

    <div class="card mb-3">
        <div class="card-header">
            <h3>Configuración General</h3>
        </div>
        <form action="" method="POST">
        <input type="hidden" name="guardar_formulario" value="true">
        <input type="hidden" name="id_registro" value="<?php echo $id; ?>">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <h3>Configurar año en curso</h3>
                </div>
                <div class="col-md-3" style="margin-bottom: 10px">
                                <lable>Año en Curso</lable>
                                <select class="form-control form-control-sm" name="anio_curso">
                                    <option value="">Por Año...</option>
                                    <?php
                                    foreach ($Array_Anio as $anio) {
                                        if ($data["anio_curso"] ==  $anio[1]) {
                                            echo '<option value="' . $anio[1] . '" selected>' . $anio[1] . '</option>';
                                        } else {
                                            echo '<option value="' . $anio[1] . '">' . $anio[1] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                </div>

                <div class="col-md-2">
                                <lable>Mes inicio</lable>
                                <input type="month" name="mes_inicio" id="mes_inicio" class="form-control form-control-sm" value="<?php echo $data["mes_inicio"]; ?>">
                </div>
                <div class="col-md-2">
                                <lable>Mes fin</lable>
                                <input type="month" name="mes_fin" id="mes_fin" class="form-control form-control-sm" value="<?php echo $data["mes_fin"]; ?>">
                </div>

                <div class="col-md-12">
                                <label for="formatoNumerico" class="col-sm-3 col-form-label">Formato numérico</label>
                                <div class="col-sm-4">
                                    <select name="formatoNumerico" id="formatoNumerico" class="form-control">
                                        <option value="americano" <?php if($data["formato_numerico"] == 'americano') echo 'selected'; ?>>Americano (1,000.25)</option>
                                        <option value="europeo" <?php if($data["formato_numerico"] == 'europeo') echo 'selected'; ?>>Europeo (1.000,25)</option>
                                    </select>
                                </div>
                                <div class="col-sm-5 text-muted">
                                    <small>Selecciona cómo deseas interpretar los números con separadores de miles y decimales.</small>
                                </div>
                </div>

                <div class="col-md-12" style="margin-bottom: 10px">
                                <button type="submit" class="btn btn-success btn-block btn-sm">
                                    <i class="fas fa-check"></i> Guardar
                                </button>
                </div>

            </div>
        </div>
        </form>
    </div>
</div>





