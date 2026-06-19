<?php
include("../../app/connect.php");
$id_empresa = $_POST["id_empresa"];
$id_plan = $_POST["id_plan"];
$id_colaborador = $_POST["id_colaborador"];
$urlRedirect  = $_POST["url"];

$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Actividades WHERE id_empresa = '" . $_POST["id_empresa"] . "' AND id = '".$id_plan."' ");
$data = mysqli_fetch_array($query);
$array_responsables = explode(",", $data["id_asignado"]);

print_r($array_responsables);

$insertar = true;
foreach($array_responsables as $responsable){
    if($responsable == $id_colaborador ){
        $insertar = false;
    }
}

if($insertar){
    array_push($array_responsables, $id_colaborador);
}

$lista_txt = implode(",", $array_responsables);



$sentencia = "UPDATE Okrs_Actividades SET id_asignado = '".$lista_txt."' WHERE id = '".$id_plan."' AND id_empresa = '".$id_empresa."' ";
mysqli_query($connect_okrs, $sentencia);
?>

<script>
    window.location = "<?php echo $urlRedirect; ?>";
</script>