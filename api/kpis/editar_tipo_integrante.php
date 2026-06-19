<?php
include("../../app/connect.php");

$id_Kpi_Colaborador = $_POST["id_Kpi_Colaborador"];
$tipo = $_POST["tipo"];

$sentencia = "UPDATE Kpis_Colaborador SET tipo = {$tipo} WHERE id = {$id_Kpi_Colaborador}";
$query = mysqli_query($connect_kpis, $sentencia);

echo json_encode([
    "status" => "success",
    "sentencia" => $sentencia
]);
?>