<?php
$id = $_POST["id"];
$id_user = $_POST["id_user"];
$id_empresa = $_POST["id_empresa"];

include("../../app/functions.php");
include("../../app/connect.php");

include("../../app/models/okrs/OkrsServicios.php");
$ClassOkrsServicios = new OkrsServicios();
$data = $ClassOkrsServicios->obtener_okrs($id);

$integrantes = $ClassOkrsServicios->obtener_integrantes_asociados($id_empresa, $id);

?>

<div class="text-start">

    <div class="mb-3">
        <h3><?php echo $data["objetivo_okr"]; ?></h3>
    </div>

    <div class="mb-3">
        Avance general <br>
        <div class="progress">
            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $data["avance"]; ?>%; background-color: <?php echo $data["avance_color"]; ?>  !important;" aria-valuenow="<?php echo $data["avance"]; ?>" aria-valuemin="0" aria-valuemax="100">
                    <?php echo $data["avance"]; ?>%
            </div>
        </div>

        
    </div>

    <div class="mb-3">
        Periodo del: <br>
        <b><?php echo $data["fecha_inicia"]; ?> al <?php echo $data["fecha_termina"]; ?></b>
    </div>

    <div class="mb-3">
        Objetivos Estratégicos Relacionados <br>
        <b><?php echo $data["nombre_objetivo_estrategico"]; ?></b>
    </div>

</div>

<div class="card-header">
    <h3>Listado de Integrantes del OKRs</h3>
</div>

<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <td>Foto</td>
            <td>Integrante</td>
            <td>Cargo</td>
            <td>Alta dirección</td>
            <td>Área</td>
            <td>Estado</td>
        </tr>
        <?php
        foreach($integrantes as $integrante){
            echo '
            <tr>
                <td>'.$integrante["empleado"]["foto"].'</td>
                <td>'.$integrante["empleado"]["nombre"].'</td>
                <td>'.$integrante["empleado"]["nombre_cargo"].'</td>
                <td>'.$integrante["empleado"]["nombre_vicepresidencia"].'</td>
                <td>'.$integrante["empleado"]["nombre_area"].'</td>
                <td>Estado</td>
            </tr>
            ';
        }
        ?>
    </table>
</div>