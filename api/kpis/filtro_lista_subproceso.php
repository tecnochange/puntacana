<option value="-1">Área SubProceso...</option>
<?php
include("../../app/connect.php");
$id_empresa = $_POST["id_empresa"];
$id_vicepresidencia = $_POST["id_vicepresidencia"];
$id_area = $_POST["id_area"];

$filtro = "";
if($id_vicepresidencia > 0){
    $filtro .= " AND Estructura_Empresa.vicepresidencia = '".$id_vicepresidencia."'  "; 
}
if($id_area){
    $filtro .= " AND Estructura_Empresa.area = '".$id_area ."'  "; 
}

$sentencia = "
SELECT
    Estructura_Empresa.id, Estructura_Empresa.unidad_organizativa 
FROM
    Estructura_Empresa 
    LEFT JOIN Areas ON Areas.id = Estructura_Empresa.area
WHERE
    Estructura_Empresa.id_empresa = '".$id_empresa."' AND
    Estructura_Empresa.unidad_organizativa != ''
    ".$filtro." 
    ORDER BY Estructura_Empresa.unidad_organizativa ASC  
";

echo  $sentencia;
$query = mysqli_query($connect_admin, $sentencia);
while ($data = mysqli_fetch_assoc($query)) {

    echo '<option value="' . $data["id"] . '">' . $data["unidad_organizativa"] . '</option>';
        
}


?>