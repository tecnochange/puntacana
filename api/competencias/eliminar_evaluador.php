<?php
include("../../app/connect.php");

if (!isset($_POST['id'])) {
	echo 'error';
	exit;
}

$id = intval($_POST['id']);
$urlRedirect = $_POST['url'];



$sentencia_eliminar = " DELETE FROM Evaluadores WHERE id = '$id' ";
mysqli_query($connect_valoracion, $sentencia_eliminar);


/*
//DATOS DEL EVALUADOR
$queryValidar = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores WHERE id = '" . $id . "' ");
$dataValidar = mysqli_fetch_array($queryValidar);

$queryValidarEvaluaciones = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE anio = '".$dataValidar["anio"]."' AND id_ciclo = '".$dataValidar["ciclo"]."' AND id_evaluado = '".$dataValidar["id_empleado"]."' AND id_evaluador = '".$dataValidar["id_evaluador"]."'  ");
$dataValidarEvaluaciones = mysqli_fetch_array($queryValidarEvaluaciones);

print_r($dataValidarEvaluaciones);
*/


//mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores WHERE id_empleado = '".$id."' AND id_ciclo = '".$ciclo."' AND anio = '".$anio."' ");

/*
$query = true;
mysqli_query(
	$conexion,
	" "
); 

if ($query) {
	echo 'ok';
} else {
	echo 'error';
}
    */
?>

<script>
    window.location = "<?php echo $urlRedirect; ?>";
</script>