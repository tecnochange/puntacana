<?php
include("../../app/connect.php");

$id = $_POST["id"];
$id_user = $_POST["id_user"];
$valor = $_POST["valor"];
$urlRedirect = $_POST["url"];

$sentencia = "UPDATE Okrs_Resultados SET avance = '".$valor."' WHERE id = '".$id."' ";
$query = mysqli_query($connect_okrs, $sentencia);

?>

<script>
    window.location.reload();
</script>