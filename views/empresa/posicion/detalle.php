<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_posiciones').addClass('active');
});
</script>


<?php
include("views/administrar/etiquetas.php");
$id = $_GET["id"];
$hoy = date("Y-m-d H:i:s");

// include("app/models/Selects.php");
// $ClassSelects = new Selects();

// include("app/models/Positions.php");
// $ClassPositions = new Positions();

if ($_POST["guardar_formulario"]) {
    $cargo = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE id = " . $_POST["id_cargo"] . "");
    $datacargo = mysqli_fetch_array($cargo);
    if ($_POST["id_registro"]) { 

        $sentencia = "
        UPDATE Posiciones 
        SET 
            id_cargo = '" . $_POST["id_cargo"] . "', 
            id_departamento = '" . $_POST["id_departamento"] . "', 
            id_area = '" . $_POST["id_area"] . "', 
            id_centro_costo = '" . $_POST["id_centro_costo"] . "', 
            estado = '".$_POST["estado"]."', 
            updated_at = '".$hoy."' 
        WHERE id =  '".$_POST["id_registro"]."'
        ";

        mysqli_query($connect_admin, $sentencia);

        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        $accion = 'ACTUALIZAR';
        $descripcion = 'Actualización de la posición para el cargo ' . $datacargo["nombre"];
        $modulo = 'Posiciones';
        GuardarAuditoria( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $modulo );

        $respuesta = '
            <div class="alert alert-success" role="alert">
                Los datos ha sido actualizados con éxito.
            </div>
        ';
    } else {
        // $id_tmp = $ClassPositions->create($_POST, $connect_valentina);
        //para evitar reinsersion
        $string_query = "INSERT INTO Posiciones ( id_empresa , id_cargo , id_departamento , id_area , id_centro_costo, estado , created_at , updated_at ) 
            VALUES 
            ( '" . $_SESSION["id_empresa"] . "', '" . $_POST["id_cargo"] . "', '" . $_POST["id_departamento"] . "', '" . $_POST["id_area"] . "', '" . $_POST["id_centro_costo"] . "', '" . $_POST["estado"] . "', '" . $hoy . "', '" . $hoy . "' )";

        mysqli_query($connect_admin, $string_query);

        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        $accion = 'CREAR';
        $descripcion = 'Creación de la posición para el cargo ' . $datacargo["nombre"];
        $modulo = 'Posiciones';
        GuardarAuditoria( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $modulo );

        echo '<script> window.location = "?pg=empresa/posiciones";</script>';
    }
}

// $posicion =  $ClassPositions->position($id, $connect_valentina);
$query = mysqli_query($connect_admin, "SELECT * FROM Posiciones WHERE id = '" . $id . "' ");
$data = mysqli_fetch_array($query);
?>

<div class="container">

    <a href="<?php echo $url; ?>?pg=empresa/posiciones" >
        <button class="btn btn-success mb-3"> << Volver</button>
    </a>

    <div class="card">
        <div class="card-header">
            <h3>Ficha Posición</h3>
        </div>

        <form action="" method="POST">
        <input type="hidden" name="guardar_formulario" value="true">
        <input type="hidden" name="id_registro" value="<?php echo $id; ?>">
        <div class="card-body">

            <div class="row">
                <div class="col-md-4" style="margin-bottom: 10px">
                        <label>Cargo *</label>
                        <select class="form-control" name="id_cargo" required>
                            <option value="">Selecciona...</option>
                            <?php
                            $qry = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' ORDER BY nombre ASC ");
                            while ($dt = mysqli_fetch_array($qry)) {
                                if ($dt["id"] == $data["id_cargo"]) {
                                    echo '<option value="' . $dt["id"] . '" selected>' . $dt["nombre"] . '</option>';
                                } else {
                                    echo '<option value="' . $dt["id"] . '">' . $dt["nombre"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4" style="margin-bottom: 10px">
                        <label>Vicepresidencia *</label>
                        <select class="form-control" name="id_departamento" required>
                            <option value="">Selecciona...</option>
                            <?php //echo $ClassSelects->areas_list($posicion["id_departamento"], $connect_valentina); 
                            $qry = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' ORDER BY nombre ASC ");
                            while ($dt = mysqli_fetch_array($qry)) {
                                if ($dt["id"] == $data["id_departamento"]) {
                                    echo '<option value="' . $dt["id"] . '" selected>' . $dt["nombre"] . '</option>';
                                } else {
                                    echo '<option value="' . $dt["id"] . '">' . $dt["nombre"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4" style="margin-bottom: 10px">
                        <label>Áreas *</label>
                        <select class="form-control" name="id_area" required>
                            <option value="">Selecciona...</option>
                            <?php //echo $ClassSelects->areas_list($posicion["id_area"], $connect_valentina); 
                            $qry = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' ORDER BY nombre ASC ");
                            while ($dt = mysqli_fetch_array($qry)) {
                                if ($dt["id"] == $data["id_area"]) {
                                    echo '<option value="' . $dt["id"] . '" selected>' . $dt["nombre"] . '</option>';
                                } else {
                                    echo '<option value="' . $dt["id"] . '">' . $dt["nombre"] . '</option>';
                                }
                            } ?>
                        </select>
                    </div>

                    <div class="col-md-4" style="margin-bottom: 10px">
                        <label>Estado</label>
                        <select class="form-control" name="estado" required>
                            <option value="">Selecciona...</option>
                            <?php
                            foreach ($Array_Estado as $role) {
                                if ($role[0] == $data["estado"]) {
                                    echo '<option value="' . $role[0] . '" selected>' . $role[1] . '</option>';
                                } else {
                                    echo '<option value="' . $role[0] . '">' . $role[1] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-12" align="right">
                        <button type="submit" class="btn btn-primary ">
                            <i class="bx bx-check"></i> Guardar
                        </button>
                    </div>
            </div>

        </div>
        </form>

    </div>
</div>



