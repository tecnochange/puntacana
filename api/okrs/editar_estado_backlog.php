<?php
include("../../app/connect.php");

$id_plan_accion = $_POST["id_plan_accion"];
$estado = $_POST["estado"];

$sentencia = "UPDATE Okrs_Actividades SET estado_backlog = '$estado' WHERE id = '$id_plan_accion'";
$query = mysqli_query($connect_okrs, $sentencia);

echo json_encode([
    "status" => "success",
    "data" => [
        "estado" => $estado,
        "id_plan_accion" => $id_plan_accion
    ]
]);
?>