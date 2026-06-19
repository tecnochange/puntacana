<script>
$(document).ready(function() {
    $('#menuEstrategia').collapse();
    $('#bt_estrategia_objetivos').addClass('active');
});
</script>

<?php
$hoy = date("Y-m-d H:i:s");
$id = $_GET["id"];

if ($_POST["objetivo"] != "") {

    if ($_POST["id_registro"] != "") {
        $sentencia = "
			UPDATE  Objetivos_estrategicos  SET  dimensiones = '" . implode(",", $_POST["dimensiones"]) . "', 
			id_responsable = '" . $_POST["id_responsable"] . "', objetivo = '" . $_POST["objetivo"] . "', descripcion = '" . $_POST["descripcion"] . "', pais =  '" . $_POST["pais"] . "', sucursal =  '" . $_POST["sucursal"] . "', anio = '" . $_POST["anio"] . "',ponderacion = '" . $_POST["ponderacion"] . "',updated_at = '$hoy'
			WHERE id = '" . $_POST["id_registro"] . "'
			";

        mysqli_query($connect_okrs, $sentencia);
        $accion = 'ACTUALIZAR';
        $descripcion = 'Actualización Objetivo Estratégico ' . $_POST["objetivo"];
        
        $auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	    VALUES (" . $_SESSION['id_empresa'] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
        // echo $auditoria;
        mysqli_query($connect_okrs, $auditoria);
    } else {

        $objetivo = EscaparInyeccion($_POST["objetivo"]);

        $sentencia = "
			INSERT INTO  Objetivos_estrategicos ( id_empresa ,  dimensiones ,  id_responsable ,  objetivo , descripcion,pais, sucursal, estado , anio, ponderacion, created_at ) 
			VALUES 
			( '" . $_SESSION['id_empresa'] . "', '" . implode(",", $_POST["dimensiones"]) . "', '" . $_POST["id_responsable"] . "', '" . $objetivo . "', '" . $_POST["descripcion"] . "','" . $_POST["pais"] . "', '" . $_POST["sucursal"] . "',  1, '" . $_POST["anio"] . "','" . $_POST["ponderacion"] . "','" . $hoy . "'  )
			";
        mysqli_query($connect_okrs, $sentencia);

        $accion = 'CREAR';
        $descripcion = 'Creación Objetivo Estratégico ' . $objetivo;
        
        $auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	    VALUES (" . $_SESSION['id_empresa'] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
        // echo $auditoria;
        mysqli_query($connect_okrs, $auditoria);
    }

    echo '<script> window.location.href = "?pg=estrategica/objetivos";</script>';
}

$query = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id = '" . $id . "' ");
$data = mysqli_fetch_array($query);
$array_dimensiones = explode(",", $data["dimensiones"]);
$cont = 0;
$count_anios = array();
foreach ($Array_Anio as $value) {
    $queryAnio = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = " . $value[1] . "");
    $count_anios[$cont]["anio"] = $value[1];
    $count_anios[$cont]["porcentaje"] = round(100 / (mysqli_num_rows($queryAnio)), 2);
    $cont++;
}
if (!isset($data["ponderacion"])) {
    foreach ($count_anios as $periodo) {
        if ($data["anio"] == $periodo["anio"]) {
            mysqli_query($connect_okrs, "UPDATE Objetivos_estrategicos SET ponderacion = ".$periodo["porcentaje"]." WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = " . $data["anio"] . "");
            $ponderacion = $periodo["porcentaje"];
        }
    }
} else {
    if($data["ponderacion"] == ""){
        foreach ($count_anios as $periodo) {
            if ($data["anio"] == $periodo["anio"]) {
                mysqli_query($connect_okrs, "UPDATE Objetivos_estrategicos SET ponderacion = ".$periodo["porcentaje"]." WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = " . $data["anio"] . "");
                $ponderacion = $periodo["porcentaje"];
            }
        }
    }else{
    $ponderacion = $data["ponderacion"];
    }
}
?>
<?php echo $respuesta; ?>


<style>
    .check_dimensiones {
        width: 150px;
        display: inline-table;
        text-align: center;
    }

    .check_box {
        width: 25px;
        height: 25px;
    }

    label {
        color: #353535;
        margin-top: 20px;
    }
</style>

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3>Detalle Objetivo Estratégico</h3>
                </div>
                <div class="card-body">
                    <form action="" method="post">

                        <div class="form-group">
                            <div class="row">
                                <input type="hidden" name="id_registro" value="<?php echo $data["id"]; ?>">
                                <div class="col-md-12">
                                    <label style="margin-bottom: 15px">Seleccione las dimensiones para este objetivo</label><br>
                                    <?php
                                    $queryDim = mysqli_query($connect_okrs, "SELECT * FROM Dimensiones WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' ");
                                    while ($dataDim = mysqli_fetch_array($queryDim)) {

                                        $checked = "";
                                        foreach ($array_dimensiones as $dimension) {
                                            if ($dimension == $dataDim["id"]) {
                                                $checked = "checked";
                                            }
                                        }

                                        echo '
						<div class="check_dimensiones">
							<input type="checkbox" name="dimensiones[]" value="' . $dataDim["id"] . '" class="check_box" ' . $checked . '><br>
							' . $dataDim["nombre"] . '
							
						</div>
						';
                                    }
                                    ?>
                                </div>


                            </div>
                        </div>
                        <br>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-3">
                                    <h5>Responsable</h5>
                                    <select class="form-control" name="id_responsable" required>
                                        <option value="">Selecciona...</option>
                                        <?php
                                        $queryReponsable = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id_empresa = '" . $user_log["id_empresa"] . "' ORDER BY nombre ASC ");
                                        while ($dataReponsable = mysqli_fetch_array($queryReponsable)) {
                                            if ($data["id_responsable"] == $dataReponsable["id"]) {
                                                echo '
                                  <option value="' . $dataReponsable["id"] . '" selected>' . $dataReponsable["nombre"] . ' ' . $dataReponsable["apellidos"] . '</option>
                                  ';
                                            } else {
                                                echo '
                                  <option value="' . $dataReponsable["id"] . '">' . $dataReponsable["nombre"] . ' ' . $dataReponsable["apellidos"] . '</option>
                                  ';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <h5>País</h5>
                                    <input type="text" class="form-control" name="pais" value="<?php echo $data["pais"]; ?>">
                                </div>
                                <div class="col-md-3">
                                    <h5>Sucursal</h5>
                                    <input type="text" class="form-control" name="sucursal" value="<?php echo $data["sucursal"]; ?>">
                                </div>
                                <div class="col-md-2">
                                    <h5>Año</h5>
                                    <select class="form-control" name="anio" id="anio" required>
                                        <option value="">Selecciona...</option>
                                        <?php
                                        foreach ($Array_Anio as $periodo) {
                                            if ($data["anio"] ==  $periodo[0]) {
                                                echo '<option value="' . $periodo[0] . '" selected>' . $periodo[1] . '</option>';
                                            } else {
                                                echo '<option value="' . $periodo[0] . '">' . $periodo[1] . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <h5>Ponderación</h5>
                                    <input type="number" class="form-control" name="ponderacion" id="ponderacion" value="<?php echo $ponderacion; ?>" onkeypress="return filterFloat(event,this);">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Objetivo</h5>
                                    <textarea rows="3" class="form-control" name="objetivo"><?php echo $data["objetivo"]; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5>Descripción del objetivo</h5>
                                    <textarea rows="3" class="form-control" name="descripcion" id="descripcion_obj"><?php echo $data["descripcion"]; ?></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-12" style="margin-top: 15px">
                                    <button type="submit" class="btn btn-success ">Guardar</button>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

</div>



<script>
    var api = '<?php echo $url; ?>api/desempenio/';

    var activar = false;

    function Elimimar(id) {

        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar un objetivo, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Elimimar(' + id + ')"> Confirmar </button>');
        } else {

            jQuery.ajax({
                    url: api + "eliminar_objetivo.php",
                    type: 'post',
                    data: {
                        id: id,
                        url: "?pg=desempenio/objetivos"
                    },
                }).done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {});

        }
    }

    function filterFloat(evt, input) {
        // Backspace = 8, Enter = 13, ‘0′ = 48, ‘9′ = 57, ‘.’ = 46, ‘-’ = 43
        var key = window.Event ? evt.which : evt.keyCode;
        var chark = String.fromCharCode(key);
        var tempValue = input.value + chark;
        var isNumber = (key >= 48 && key <= 57);
        var isSpecial = (key == 8 || key == 13 || key == 0 || key == 46);
        if (isNumber || isSpecial) {
            return filter(tempValue);
        }

        return false;

    }

    function filter(__val__) {
        var preg = /^([0-9]+\.?[0-9]{0,2})$/;
        return (preg.test(__val__) === true);
    }
</script>
<script>
    $(document).ready(function() {

        $('#descripcion_obj').summernote({
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