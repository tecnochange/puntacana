<script>
    $(document).ready(function() {
        $(".menu_section").addClass("active");
        // $("#nav_empresa").addClass("active");
        jQuery("#menu_empresa").css("display", "none");
        $("#bt_estructura_lideres").addClass("current-page");
    });
</script>


<?php

$id = $_GET["id"];
$hoy = date("Y-m-d H:i:s");

//CONSULTA PARA NUEVO CLIENTE
//CONSULTA PARA NUEVO CLIENTE
if ($_POST["id_empleado"] != "") {
    $Empleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = " . $_POST["id_empleado"] . " AND estado = 1");
    $dataEmpleado = mysqli_fetch_array($Empleado);
    mysqli_query($connect_valentina, "INSERT INTO Lideres (id_empresa, id_empleado, id_jefe, created_at ) 
        VALUES 
        ( '" . $_SESSION['id_empresa'] . "', '" . $_POST["id_empleado"] . "', '" . $_POST["id_jefe"] . "', '" . $hoy . "' ) ");

    echo '<script> window.location = "?pg=estructura/lideres";</script>'; //para evitar reinsersion  
    $accion = 'CREAR';
    $descripcion = 'Asignación de lider al usuario ' . $dataEmpleado["nombre"];

    $auditoria = "INSERT INTO Auditoria_Admin (id_empresa,id_empleado,accion,descripcion,modulo,created_at)
                    VALUES (" . $_SESSION['id_empresa'] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion','Lideres','$hoy')";
    // echo $auditoria;
    mysqli_query($connect_valentina, $auditoria);
}


//INFORMACION DE LA BATERIA
$query = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '" . $id . "'  AND estado = 1");
$data = mysqli_fetch_array($query);

$queryL = mysqli_query($connect_valentina, "SELECT * FROM Lideres WHERE id_empleado = '" . $id . "' ");
$dataL = mysqli_fetch_array($queryL);

?>



<div class="container">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $url; ?>?pg=empresa/lideres">Líderes</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="">Agregar Jefe</a></li>
        </ol>
    </nav>

    <div class="card">

        <div class="card-body">
            <form action="" method="post">
                <div class="row">

                    <div class="col-md-12">
                        <h3>Detalle Jefe</h3>
                        <input type="hidden" name="id_registro" value="<?php echo $id; ?>">
                    </div>

                    <div class="col-md-6" style="margin-bottom: 10px">
                        <lable>Empleado</lable>
                        <input type="text" class="form-control" value="<?php echo $data["nombre"]; ?>" disabled>
                        <input type="hidden" value="<?php echo $id; ?>" name="id_empleado">
                    </div>

                    <div class="col-md-6" style="margin-bottom: 10px">
                        <lable>Jefe</lable>
                        <select class="form-control multiples_responsables" name="id_jefe">
                            <option value="">Selecciona..</option>
                            <?php
                            $queryJer = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id != '" . $id . "' AND id_empresa = '" . $_SESSION["id_empresa"] . "' AND role <= 2 AND estado = 1 ORDER BY nombre ASC ");
                            while ($dataJer = mysqli_fetch_array($queryJer)) {

                                $queryVal = mysqli_query($connect_valentina, "SELECT * FROM Lideres WHERE id_empleado = '" . $id . "' AND id_jefe = '" . $dataJer["id"] . "' ");

                                if ($queryVal->num_rows == 0) {
                                    if ($dataL["id_jefe"] == $dataJer["id"]) {
                                        echo '<option value="' . $dataJer["id"] . '" selected>' . $dataJer["nombre"] . '</option>';
                                    } else {
                                        echo '<option value="' . $dataJer["id"] . '">' . $dataJer["nombre"] . '</option>';
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <script>
                        $(document).ready(function() {
                            $('.multiples_responsables').select2();
                        });
                    </script>




                    <div class="col-md-12" style="margin-bottom: 10px">
                        <button type="submit" id="sidebarCollapse" class="btn btn-success btn-block btn-sm">
                            <i class="bx bx-check"></i> Guardar
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </div>

</div>