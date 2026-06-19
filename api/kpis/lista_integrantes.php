<?php
include("../../app/connect.php");
$id_empresa = $_POST["id_empresa"];
$id_viceprecidencia = $_POST["id_vicepresidencia"];
$id_area = $_POST["id_area"];

$filtro = "";
if($id_area){
    $filtro .= " AND Empleados.area = '$id_area'  ";
}

$sentencia =
"SELECT 
    Empleados.id as id,
    Empleados.area as id_area,
    Empleados.unidad_corporativa as id_vicepresidencia,
    Empleados.foto,
    Empleados.nombre,
    Cargos.nombre as nombre_cargo
FROM
    Empleados
LEFT JOIN
    Cargos ON Cargos.id = Empleados.id_cargo
WHERE 
    Empleados.id_empresa = ".$id_empresa."
    AND Empleados.estado = 1
    AND Empleados.unidad_corporativa = ".$id_viceprecidencia."
    ".$filtro."
    GROUP BY Empleados.id
    ORDER BY Empleados.nombre ASC
";
$query = mysqli_query($connect_admin, $sentencia);

$data = [];
while ($row = mysqli_fetch_assoc($query)) {
    $data[] = $row;
}

echo json_encode($data);
?>