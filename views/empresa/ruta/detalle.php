<?php
$id = $_GET["id"];
$hoy = date("Y-m-d H:i:s");
$respuesta = "";

//CONSULTA PARA NUEVO CLIENTE
//CONSULTA PARA NUEVO CLIENTE
if ($_POST["nombre"] != "") {

    if ($_POST["id_registro"] != "") {
        $sentencia = "
			UPDATE Rutas SET  
			ruta = '" . $_POST["nombre"] . "', 
            modulo = '" . $_POST["modulo"] . "'           
			WHERE id = '" . $_POST["id_registro"] . "'
			";
        mysqli_query($connect_valentina, $sentencia);

        $respuesta = '
                <div class="alert alert-success" role="alert">
                    La ruta ha sido guardada con éxito.
                </div>
            ';

        echo '<script> window.location = "?pg=estructura/rutas";</script>';
    } else {
        $sentencia = "
			INSERT INTO Rutas ( ruta , modulo, id_empresa, roles ) 
			VALUES 
			('" . $_POST["nombre"] . "',
            '" . $_POST["modulo"] . "', 
            '" . 1 . "', 
            1 )
			";
        mysqli_query($connect_valentina, $sentencia);

        echo '<script> window.location = "?pg=estructura/rutas";</script>';
    }
}


//INFORMACION DE LA BATERIA
// $query = mysqli_query($connect_onboarding, "SELECT * FROM Actividades WHERE id = '" . $id . "' ");
// $data = mysqli_fetch_array($query);

$query = mysqli_query($connect_valentina, "SELECT * FROM Rutas WHERE id = '" . $id . "' ");
$data = mysqli_fetch_array($query);


?>

<div class="container">

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $url; ?>?pg=estructura/rutas">Rutas</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="">Detalle</a></li>
        </ol>
    </nav>

    <div class="card">

    <?php echo $respuesta; ?>


        <div class="card-body">
            <form action="" method="post">
                <div class="row">

                    <div class="col-md-12">
                        <h2>Detalle Ruta</h2>
                        <input type="hidden" name="id_registro" value="<?php echo $id; ?>">

                    </div>

                    <div class="col-md-12" style="margin-bottom: 10px">
                        <label>Nombre de la Ruta</label>
                        <input type="text" class="form-control" name="nombre" value="<?php echo $data["ruta"]; ?>" required>
                    </div>


                    <div class="col-md-4" style="margin-bottom: 10px">
                        <label>Módulo *</label>
                        <select class="form-control" name="modulo" required>
                            <option value="0">Selecciona..</option>
                            <?php
                            $queryEmp = mysqli_query($connect_valentina, "SELECT * FROM Modulos");
                            while ($dataEmp = mysqli_fetch_array($queryEmp)) {
                                if ($data["modulo"] == $dataEmp["id"]) {
                                    echo '<option value="' . $dataEmp["id"] . '" selected>' . $dataEmp["nombre"] . '</option>';
                                } else {
                                    echo '<option value="' . $dataEmp["id"] . '">' . $dataEmp["nombre"] . ' </option>';
                                }
                            }
                            ?>
                        </select>
                    </div>


                    <div class="col-md-12" style="margin-bottom: 10px">
                        <button type="submit" id="sidebarCollapse" class="btn btn-success btn-block btn-sm">
                            <i class="fas fa-check"></i> Guardar
                        </button>
                    </div>

                </div>
            </form>
        </div>

    </div>
</div>