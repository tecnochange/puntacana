<?php
include("../../app/connect.php");

$id_plan_accion = $_POST["id_plan_accion"];
$comentario = $_POST["comentario"];
$id_empleado = $_POST["id_empleado"];
$id_empresa = $_POST["id_empresa"];
$hoy = date("Y-m-d H:i:s");

$sql = "INSERT INTO Comentarios_Plan_Accion (id_empresa, id_empleado, id_plan, comentario, created_at)
VALUES ('$id_empresa', '$id_empleado', '$id_plan_accion', '$comentario', '$hoy')";

mysqli_query($connect_okrs, $sql);

/* echo json_encode([
        "id_plan_accion" => $id_plan_accion,
        "comentario" => $comentario,
        "id_empleado" => $id_empleado,
        "id_empresa" => $id_empresa,
        "hoy" => $hoy
]); */
?>