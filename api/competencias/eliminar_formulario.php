<?php
include("../../app/connect.php");

$hoy = date("Y-m-d H:i:s");
$id = $_POST["id"];

$id_evaluado = $_POST["id_evaluado"];
$id_evaluador = $_POST["id_evaluador"];
$id_tipo = $_POST["id_tipo"];

$query = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE id = '" . $id . "'  ");
$row = mysqli_fetch_assoc($query);
$jsonData = $row['obj_evaluacion'];
$data = json_decode($jsonData, true);

if ($data) {
	foreach ($data as $item) {
		//mysqli_query($connect_valoracion, "DELETE FROM Pdi_Competencias WHERE id_empleado = '" . $id_evaluado . "' AND id_competencia = " . $item['competencia'] . " AND id_jefe = $id_evaluador");
	}
}

mysqli_query($connect_valoracion, "DELETE FROM Competencias_Evaluaciones_New WHERE id = '" . $id . "'  ");
?>