<?php
include("../../app/connect.php");

$id_okr = $_POST["id_okr"];
$id_resultado = $_POST["id_resultado"];
$comentario = $_POST["comentario"];
$id_empleado = $_POST["id_empleado"];

$sql = "INSERT INTO Okrs_Comentarios (id_okrs, id_resultado, id_empleado, comentario, created_at)
        VALUES ('$id_okr', '$id_resultado', '$id_empleado','$comentario', NOW())";

mysqli_query($connect_okrs, $sql);
?>