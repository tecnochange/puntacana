<?php
include("../../app/connect.php");

$id_Okrs_Equipos = $_POST["id_Okrs_Equipos"];
$tipo = $_POST["tipo"];

$sentencia = "UPDATE Okrs_Equipos SET tipo = {$tipo} WHERE id = {$id_Okrs_Equipos}";
$query = mysqli_query($connect_okrs, $sentencia);

echo json_encode([
    "status" => "success",
    "sentencia" => $sentencia
]);
?>