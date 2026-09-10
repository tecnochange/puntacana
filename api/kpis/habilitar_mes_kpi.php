<?php
$hoy = date("Y-m-d H:i:s");
$id = $_POST["id"];
$urlRedirect = $_POST["url"];

include("../../app/connect.php");

$mes = $_POST["mes"];

if ($_POST["estado"] == 1) {
    $estado = 'on';
}

$query = mysqli_query($connect_kpis, "UPDATE Habilitar_Mes SET $mes = '$estado', updated_at = '$hoy' WHERE id = " . $_POST["id"] . " ");

if ($query) {
    echo 'Acción actualizada con exito';
}
