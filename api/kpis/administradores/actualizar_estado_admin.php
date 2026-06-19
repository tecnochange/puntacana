<?php
include("../../../app/connect.php");

$id = $_POST["id"];
$estado = $_POST["estado"];

$sentencia = "UPDATE Administradores_Kpi SET estado = {$estado} WHERE id = {$id}";
$query = mysqli_query($connect_kpis, $sentencia);


echo json_encode([
    "success" => true,
    "id" => $id,
    "estado" => $estado
]);
?>