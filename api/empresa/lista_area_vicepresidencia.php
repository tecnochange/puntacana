<option value="-1">Seleccione...</option>
<?php
include("../../app/connect.php");
$id_empresa = $_POST["id_empresa"];
$id_vicepresidencia = $_POST["id_vicepresidencia"];
$lista_areas = "";

$sentencia = "
SELECT
    Areas.id AS id_area, Areas.nombre AS nombre_area 
FROM
    Estructura_Empresa 
    LEFT JOIN Areas ON Areas.id = Estructura_Empresa.area
WHERE
    Estructura_Empresa.id_empresa = '".$id_empresa."' AND
    Estructura_Empresa.vicepresidencia = '".$id_vicepresidencia."' AND 
    Areas.nombre != ''
GROUP BY
    Areas.id 
    ORDER BY Areas.nombre ASC  
";
$query = mysqli_query($connect_admin, $sentencia);
while ($data = mysqli_fetch_assoc($query)) {

    echo '<option value="' . $data["id_area"] . '">' . $data["nombre_area"] . '</option>';
        
}


?>