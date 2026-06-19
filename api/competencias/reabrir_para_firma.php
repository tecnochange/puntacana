<?php
include("/var/www/html/puntacana.goforagile.com/app/connect.php");
include("/var/www/html/puntacana.goforagile.com/app/arrays.php");
$hoy = date("Y-m-d H:i:s");


$ciclo = $_POST["ciclo"];
$id_empresa = $_POST["id_empresa"];
$id_evaluacion = $_POST["id_evaluacion"];
$id_evaluado = $_POST["id_evaluado"];
$id_responsable = $_POST["id_responsable"];
$urlRedirect = $_POST["url"];

$queryCiclo = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id = '" .$ciclo. "' ");
$dataCiclo = mysqli_fetch_array($queryCiclo);

//SE DEBE ELIMINAR?
/*
$sentencia = "UPDATE Intervencion_Gh SET comentario = '".$descripcion_html."' WHERE id_empresa = '".$id_empresa."' AND id_responsable = '".$id_responsable."' AND id_evaluado = '".$id_evaluado."' AND ciclo = '".$dataCiclo["anio"]."' ";
mysqli_query($connect_valoracion, $sentencia);
*/

$sentencia_intervecion = "UPDATE Competencias_Evaluaciones_New SET proceso_valoracion = 4 WHERE id_empresa = '".$id_empresa."' AND id_evaluado = '".$id_evaluado."' AND id_ciclo = '".$ciclo."' ";
mysqli_query($connect_valoracion, $sentencia_intervecion);

?>

<script>
    window.location = "<?php echo $urlRedirect; ?>";
</script>