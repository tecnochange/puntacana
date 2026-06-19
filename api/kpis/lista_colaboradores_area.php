<?php
include("../../app/connect.php");
include("../../app/arrays.php");

$hoy = date("Y-m-d H:i:s");
$id = $_POST["id"];
$id_empresa = $_POST["id_empresa"];
$vicepresidencia = $_POST["vicepresidencia"];
$area = $_POST["area"];

$sentencia = "
SELECT 
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
    AND Empleados.unidad_corporativa = '".$vicepresidencia."' AND area = '".$area."'  
    GROUP BY Empleados.id
    ORDER BY Empleados.nombre ASC
";
$query = mysqli_query($connect_admin, $sentencia);
while ($data = mysqli_fetch_array($query)){ 
?>

<tr>
                                <td><img src="<?= 'https://goforagile.com/recursos/' . $integrante_foto; ?>" width="40" height="40" class="foto_miniaturas" title="<?= $integrante["nombre"]; ?>" onclick="FichaEmpleado('<?= $integrante['id_colaborador']; ?>')"></td>
                                <td><?= $data["nombre"]; ?></td>
                                <td><?= $data["cargo"]; ?></td>
                                <td></td>
                                <td>

                                    

                                </td>
</tr>


<?php } ?>
