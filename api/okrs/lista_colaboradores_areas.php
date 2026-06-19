<?php
include("../../app/connect.php");
$id_empresa = $_POST["id_empresa"];
$id_viceprecidencia = $_POST["id_viceprecidencia"];
$id_area = $_POST["id_area"];
$lista_areas = "";

$sentencia = " 
SELECT 
* FROM Empleados
WHERE area = '".$id_area."' AND id_empresa = '".$id_empresa."' 
ORDER BY nombre ASC
";
$query = mysqli_query($connect_admin, $sentencia);

if(mysqli_num_rows($query) > 0){
    while ($data = mysqli_fetch_assoc($query)) {
        $lista_areas .= '<option value="' . $data["id"] . '">' . $data["nombre"] . '</option>';
        
    }
    echo '<option value="">Seleccione...</option>';
    echo $lista_areas;
}else{
    echo '<option value="">Sin colaboradores relacionados...</option>';
}
?>