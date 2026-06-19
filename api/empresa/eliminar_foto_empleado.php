<?php
include("../../app/connect.php");

$hoy = date("Y-m-d H:i:s");
$id = $_POST["id_empleado"];

mysqli_query($connect_admin, "UPDATE Empleados SET foto = NULL WHERE id = '".$id."' ");
?>