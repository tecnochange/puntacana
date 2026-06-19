<?php
include("../../app/connect.php");
$id_empresa = $_POST["id_empresa"];
$id_kpi = $_POST["id_kpi"];

?>

<table class="table table-bordered" >
    <tr>
        <th>Nombre</th>
        <th>Cargo</th>
        <th>Vicepresidencia</th>
        <th>Área</th>
        <th>Rol KPI</th>
    </tr>
    <?php
    $sentencia = "
    SELECT id_colaborador, id_kpi, tipo  
    FROM Kpis_Colaborador 
    LEF
    WHERE id_empresa = '".$id_empresa."' AND id_kpi = '".$id_kpi."' 
    ";
    $query = mysqli_query($connect_kpis, $sentencia);
    while ($data = mysqli_fetch_array($query)){ 

        $sentencia_col = "
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
            Vicepresidencia.nombre AS nombre_vicepresidencia
        FROM
            Empleados
        LEFT JOIN
            Cargos ON Cargos.id = Empleados.id_cargo
        LEFT JOIN
            Areas ON Areas.id = Empleados.area
        LEFT JOIN
            Estructura_Empresa ON Empleados.area = Estructura_Empresa.area
        LEFT JOIN
            Vicepresidencia ON Vicepresidencia.id = Estructura_Empresa.vicepresidencia
        WHERE
            Empleados.id_empresa = '".$id_empresa."' AND Empleados.id = '".$data["id_colaborador"]."' 
        ";
        
        $queryCol = mysqli_query($connect_admin, $sentencia_col);
        $dataCol = mysqli_fetch_array($queryCol); 

        $txt_role = "";
        if($data["tipo"] == 2 ){ $txt_role = "Lider KPI"; }
        if($data["tipo"] == 3 ){ $txt_role = "Colaborador KPI"; }


        echo '
        <tr>
            <td>'.$dataCol["nombre"].'</td>
            <td>'.$dataCol["nombre_cargo"].'</td>
            <td>'.$dataCol["nombre_vicepresidencia"].'</td>
            <td>'.$dataCol["nombre_area"].'</td>
            <td>'.$txt_role.'</td>
        </tr>
        ';
        array_push($array, $data);
    }
    ?>
</table>