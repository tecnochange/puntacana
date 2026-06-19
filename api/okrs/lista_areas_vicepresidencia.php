<?php
include("../../app/connect.php");
$id_empresa = $_POST["id_empresa"];
$id_viceprecidencia = $_POST["id_viceprecidencia"];
$id_area = $_POST["id_area"];
$lista_areas = "";

$sentencia =
"SELECT
    Estructura_Empresa.area as id_area,
    Areas.nombre as nombre_area
FROM
    Estructura_Empresa
LEFT JOIN
    Areas ON Areas.id = Estructura_Empresa.area
WHERE
    Estructura_Empresa.vicepresidencia = '$id_viceprecidencia'
    AND Estructura_Empresa.id_empresa = '$id_empresa'
GROUP BY
    Estructura_Empresa.area
";
$query = mysqli_query($connect_admin, $sentencia);

if(mysqli_num_rows($query) > 0){
    while ($data = mysqli_fetch_assoc($query)) {
        if( $id_area == $data["id_area"] ){
            $lista_areas .= '<option value="' . $data["id_area"] . '" selected >' . $data["nombre_area"] . '</option>';
        }
        else{
            $lista_areas .= '<option value="' . $data["id_area"] . '">' . $data["nombre_area"] . '</option>';
        }
        
    }
    echo '<option value="">Seleccione...</option>';
    echo $lista_areas;
}else{
    echo '<option value="">Sin áreas relacionados...</option>';
}
?>