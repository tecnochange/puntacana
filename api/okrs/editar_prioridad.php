<?php
include("../../app/connect.php");

$id_plan_accion = $_POST["id"];
$prioridad = $_POST["prioridad"];

$sentencia = "UPDATE Okrs_Actividades SET prioridad = '$prioridad' WHERE id = '$id_plan_accion'";
$query = mysqli_query($connect_okrs, $sentencia);

echo json_encode([
    "status" => "success",
    "data" => [
        "prioridad" => $prioridad,
        "id_plan_accion" => $id_plan_accion
    ]
]);
?>