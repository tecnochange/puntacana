<?php
include("../../app/connect.php");
$id_empresa = $_POST["id_empresa"];
$anio = $_POST["anio"];

$lista_okrs = "";

$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id_empresa = '".$id_empresa."' AND anio = '".$anio."' ORDER BY objetivo_okr ASC ");
while ($data = mysqli_fetch_array($query)) {
    $lista_okrs .= '<option value="' . $data["id"] . '" >' . $data["objetivo_okr"] . '</option>';
}

if($query->num_rows > 0){
    echo '<option value="">Seleccione...</option>';
    echo $lista_okrs;
}
else{
    echo '<option value="">Sin Okrs...</option>';
}

?>

