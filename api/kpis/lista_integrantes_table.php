<?php
include("../../app/connect.php");
$id_empresa = $_POST["id_empresa"];
$id_viceprecidencia = $_POST["id_vicepresidencia"];
$id_area = $_POST["id_area"];

$filtros = '';
if($id_area){ $filtros .= ' AND Empleados.area = '".$id_area."'  '; }

$sentencia = "
SELECT 
    Empleados.id as id,
    Empleados.area as id_area,
    Estructura_Empresa.vicepresidencia as id_vicepresidencia,
    Empleados.foto,
    Empleados.nombre,
    Cargos.nombre as nombre_cargo
FROM
    Empleados
LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
WHERE 
    Empleados.id_empresa = '$id_empresa'
    AND Empleados.estado = 1
    AND Empleados.unidad_corporativa = '".$id_viceprecidencia."' 
    ".$filtros."
    GROUP BY Empleados.id
    ORDER BY Empleados.nombre ASC
";
$query = mysqli_query($connect_admin, $sentencia);
while ($data = mysqli_fetch_array($query)) { 

    $photo = $recursos_publico.$dt["foto"];
    if($data["foto"] == ""){$photo = $recursos_publico."img_default.jpg"; }

    $img_foto = '<img src="'.$photo.'" width="40" height="40" class="foto_miniaturas" onclick="FichaEmpleado('.$data["id"].')">',

    echo '
    <tr>
        <td>'.$img_foto.'</td>
        <td>'.$data["nombre"].'</td>
        <td>'.$data["nombre_cargo"].'</td>
        <td>
            <input type="checkbox" class="chk-rol" name="empleados['.$data["id"].']" value="1">
        </td>
    </tr>
    ';
}

if( $query->num_rows == 0 ){
     echo '
    <tr>
        <td></td>
        <td>Sin Datos</td>
        <td></td>
        <td></td>
    </tr>
    ';
}

?>
