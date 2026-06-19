<?php
include("../../app/connect.php");
include("../../app/arrays.php");
$hoy = date("Y-m-d H:i:s");

$anio = $_POST["anio"];
$idEmpresa = $_POST["id_empresa"];
$url = $_POST["url"];


$query = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id_empresa = '" . $idEmpresa . "' AND anio = '".$anio."' ");
while ($dataCiclos = mysqli_fetch_array($query)) {

    echo '<option value="' . $dataCiclos["id"] . '">' . $dataCiclos["nombre"] . '</option>';
                                        
}
