<?php
	$hoy = date("Y-m-d H:i:s");
	$id = $_POST["id"];

	
	include("../../app/connect.php");
    include("../../app/arrays.php");

    $id = $_POST["id_empleado"];
    $id_empresa = $_POST["id_empresa"];

    $sentencia = "
        SELECT
            Empleados.id,
            Empleados.documento,
            Empleados.nombre,
            Empleados.role,
            Empleados.foto,
            Empleados.id_cargo,
            Cargos.nombre AS nombre_cargo,
            Areas.id AS id_area,
            Areas.nombre AS nombre_area,
            goforagile_admin.Vicepresidencia.nombre AS nombre_vicepresidencia,
            goforagile_okrs.Roles_Okrs.nombre_rol AS rol
        FROM
            Empleados
        LEFT JOIN
            Cargos ON Cargos.id = Empleados.id_cargo
        LEFT JOIN
            Areas ON Areas.id = Empleados.area
        LEFT JOIN
            goforagile_admin.Estructura_Empresa ON Empleados.area = goforagile_admin.Estructura_Empresa.area
        LEFT JOIN
            goforagile_admin.Vicepresidencia ON goforagile_admin.Vicepresidencia.id = goforagile_admin.Estructura_Empresa.vicepresidencia
        LEFT JOIN
            goforagile_okrs.Roles_Okrs ON goforagile_okrs.Roles_Okrs.id = Empleados.role
        WHERE
            Empleados.id_empresa = '".$id_empresa."' AND Empleados.id = '".$id."'
        
    ";

    $query = mysqli_query($connect_admin, $sentencia ); 
    $data = mysqli_fetch_array($query);

    $foto = 'img_default.jpg';
    if($data["foto"]){
        $foto = $data["foto"];
    }


?>

<div style="text-align: center;" >

    <img src="https://goforagile.com/recursos/<?= $foto ?>" style="width:150px;height:150px;border-radius:50%;object-fit:cover;">

    <div class="mt-5 mb-4">
        <h3><?php echo $data["nombre"]; ?></h3>
    </div>

    <div>
        <h5><?php echo $data["nombre_cargo"]; ?></h5>
    </div>

    <div>
        <h6><?php echo $data["nombre_area"]; ?></h6>
    </div>

</div>
