<?php

include("app/connect.php");

$fecha = date("d_m_Y");
$nombre_file = 'Colaboradores_'.$fecha.'.xls';

header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=$nombre_file");
header("Pragma: no-cache");
header("Expires: 0");

$query = mysqli_query($connect_admin, "
    SELECT 
        documento,
        nombre,
        correo,
        nombre_cargo,
        nombre_area,
        nombre_nivel_jerarquico,
        compania,
        nombre_rol,
        txt_estado,
        txt_verificado
    FROM vista_colaboradores
");

echo '
<table border="1">
    <tr>
        <th>Documento</th>
        <th>Nombres y Apellidos</th>
        <th>Correo</th>
        <th>Cargo</th>
        <th>Área</th>
        <th>Nivel Jerárquico</th>
        <th>Compañía</th>
        <th>Rol</th>
        <th>Estado</th>
        <th>Verificación</th>
    </tr>
';

while($row = mysqli_fetch_assoc($query)){

    echo '
    <tr>
        <td>'.$row["documento"].'</td>
        <td>'.$row["nombre"].'</td>
        <td>'.$row["correo"].'</td>
        <td>'.$row["nombre_cargo"].'</td>
        <td>'.$row["nombre_area"].'</td>
        <td>'.$row["nombre_nivel_jerarquico"].'</td>
        <td>'.$row["compania"].'</td>
        <td>'.$row["nombre_rol"].'</td>
        <td>'.$row["txt_estado"].'</td>
        <td>'.$row["txt_verificado"].'</td>
    </tr>
    ';
}

echo '</table>';
?>