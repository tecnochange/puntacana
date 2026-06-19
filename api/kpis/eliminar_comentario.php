<?php
include("../../app/connect.php");
include("../../app/arrays.php");

$hoy = date("Y-m-d H:i:s");
$id = $_POST["id"];
$id_empresa = $_POST["id_empresa"];
$id_empleado = $_POST["id_empleado"];
$url = $_POST["url"];

mysqli_query($connect_kpis, "DELETE FROM Comentarios_Kpis WHERE id = '" . $id . "' ");