<?php
include("../../app/connect.php");

$id_okr = $_POST["id_okr"];
$id_resultado = $_POST["id_resultado"];
$id_iniciativa = $_POST["id_iniciativa"];
$comentario = $_POST["comentario"];
$id_empleado = $_POST["id_empleado"];

$sql = "INSERT INTO Okrs_Comentarios_Iniciativas (id_empresa, id_okrs, id_resultado, id_iniciativa, id_empleado, comentario, created_at)
        VALUES (1, '$id_okr', '$id_resultado', '$id_iniciativa', '$id_empleado','$comentario', NOW())";

mysqli_query($connect_okrs, $sql);
?>