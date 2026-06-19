<?php
include("../../app/connect.php");
include("../../app/arrays.php");

$hoy = date("Y-m-d H:i:s");

$queryColaborador = mysqli_query($connect_admin, "SELECT E.nombre AS nombre, E.documento AS documento, C.nombre as cargo, A.nombre AS area 
FROM Empleados E
INNER JOIN Cargos C ON C.id = E.id_cargo
INNER JOIN Areas A ON A.id = E.area
WHERE E.id = '" . $_POST["id_empleado"] . "' ");
$dataColaborador = mysqli_fetch_array($queryColaborador);

?>
<div class="row">
    <div class="col-md-12">
        <h5>Envio de evaluación a intervención de GH</h5>
    </div>
</div>
<form action="" method="post" id="intervencionPdi" autocomplete="off">
    <input type="hidden" name="id_empleado" value="<?php echo $_POST["id_empleado"]; ?>">

    <input type="hidden" name="enviar_intervencion" value="true">

    <div class="form-group">
        <div class="row">
            <div class="col-md-12">
                <label for="">Agregar Comentario</label>                
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <textarea name="comentario" id="comentario" rows="4" class="form-control"></textarea>
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-primary" id="sig-submitBtn" type="submit">Enviar</button>
            </div>
        </div>
    </div>
</form>
