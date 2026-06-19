<?php
include("../../app/connect.php");
$id_empresa = $_POST["id_empresa"];
$id_viceprecidencia = $_POST["id_viceprecidencia"];
$anio = $_POST["anio"];
$lista_objetivos = "";

$queryObjEstrategicos = mysqli_query($connect_okrs, "SELECT id, objetivo 
FROM Objetivos_estrategicos WHERE id_empresa = '".$id_empresa."' AND anio = '".$anio."'");

while ($dataObjEstra = mysqli_fetch_array($queryObjEstrategicos)) {

    if ($id_estrategico == $dataObjEstra["id"]) {
        $lista_objetivos .= '<option value="' . $dataObjEstra["id"] . '" selected>' . $dataObjEstra["objetivo"] . '</option>';
    } else {
        $lista_objetivos .= '<option value="' . $dataObjEstra["id"] . '">' . $dataObjEstra["objetivo"] . '</option>';
    }
}

if($queryObjEstrategicos->num_rows > 0){
    echo '<option value="">Seleccione...</option>';
    echo $lista_objetivos;
}
else{
    echo '<option value="">Sin objetivos relacionados...</option>';
}

?>

