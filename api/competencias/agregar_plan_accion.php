<?php
include("../../app/connect.php");
include("../../app/arrays.php");
$hoy = date("Y-m-d H:i:s");

$queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $_POST["id_competencia"] . "' ");
$dataComp = mysqli_fetch_array($queryComp);

function eliminar_tildes($archivo)
{

    $cadena = $archivo;
    $cadena = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Ã¡', 'Á', 'À', 'Â', 'Ä', 'Ã¡', 'Ã'),
        array('á', 'á', 'á', 'á', 'á', 'á', 'Á', 'Á', 'Á', 'Á', 'Á', 'Á'),
        $cadena
    );

    $cadena = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë', 'Ã©'),
        array('e', 'e', 'e', 'e', 'É', 'É', 'É', 'É', 'é'),
        $cadena
    );

    $cadena = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î', 'Ã­'),
        array('i', 'i', 'i', 'i', 'Í', 'Í', 'Í', 'Í', 'Í'),
        $cadena
    );

    $cadena = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô', 'Ã³', 'Ã“'),
        array('ó', 'ó', 'ó', 'ó', 'Ó', 'Ó', 'Ó', 'Ó', 'ó', 'Ó'),
        $cadena
    );

    $cadena = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü', 'Ãº'),
        array('u', 'u', 'u', 'u', 'Ú', 'Ú', 'Ú', 'Ú', 'Ú'),
        $cadena
    );

    $cadena = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç', 'Ã±'),
        array('n', 'Ñ', 'c', 'C', 'ñ'),
        $cadena
    );
    return $cadena;
}

?>
<div class="row">
    <div class="col-md-12">
        <h5>PLAN DE ACCIÓN DEL PDI PARA LA COMPETENCIA <?php echo mb_strtoupper(eliminar_tildes($dataComp["nombre"])); ?></h5>
    </div>
</div>
<br>
<div class="row">
    <div class="col-md-12">
        <form action="" method="post">
            <input type="hidden" name="id_competencia" value="<?php echo $_POST["id_competencia"]; ?>">
            <input type="hidden" name="id_empresa" value="<?php echo $_POST["id_empresa"]; ?>">
            <input type="hidden" name="id_jefe" value="<?php echo $_POST["id_jefe"]; ?>">
            <input type="hidden" name="id_empleado" value="<?php echo $_POST["id_empleado"]; ?>">
            <input type="hidden" name="guardar_plan_accion" value="true">
            <div class="form group">
                <div class="row">
                    <div class="col-md-4">
                        <label for="">Prioridad</label>
                        <select name="prioridad" id="prioridad" class="form-control" required>
                            <option value="">Seleccione...</option>
                            <?php
                            foreach ($Array_Prioridad as $prioridad) {
                                echo '<option value="' . $prioridad[0] . '">' . $prioridad[1] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="">Fecha de Inicio</label>
                        <input type="date" name="fecha_inicia" id="fecha_inicia" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label for="">Fecha de Finalización</label>
                        <input type="date" name="fecha_finaliza" id="fecha_finaliza" class="form-control" required>
                    </div>
                    
                </div>
            </div>
            <br>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <label for="">Plan Acción</label>
                        <textarea name="plan_accion" id="plan_accion_pc" required class="form-control"></textarea>
                    </div>
                </div>
            </div>
            <br>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12" style="text-align: right;margin-top: 15px;">
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
    $(document).ready(function() {

        $('#plan_accion_pc').summernote({
            tabsize: 2,
            height: 150,
            minHeight: null,
            maxHeight: null,
            focus: true,

            toolbar: [
                ['para', ['ul', 'ol']]
            ],
            styleTags: [
                "p",
                "code",
                "blockquote",
                "pre",
                "h1",
                "h2",
                "h3",
                "h4",
                "h5",
                "h6",
            ],
        });
    });
</script>