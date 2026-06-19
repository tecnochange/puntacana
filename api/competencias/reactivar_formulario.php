<?php
include("../../app/connect.php");

$hoy = date("Y-m-d H:i:s");
$id = $_POST["id"];

$query = mysqli_query($connect_valoracion, "UPDATE Competencias_Evaluaciones_New SET observaciones = '', promedio = '',  
    estado = 1, update_at = '" . $hoy . "' WHERE id = '" . $id . "'  ");
$data = mysqli_fetch_array($query); 

$data = [];
?>