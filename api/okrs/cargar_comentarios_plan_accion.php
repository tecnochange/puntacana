<?php
include("../../app/connect.php");

$id_plan_accion = $_GET["id_plan_accion"];

$sql = "SELECT
            Comentarios_Plan_Accion.*,
            Empleados.nombre AS nombre_empleado
        FROM
            Comentarios_Plan_Accion
        LEFT JOIN
            puntacana_admin.Empleados AS Empleados ON Empleados.id = Comentarios_Plan_Accion.id_empleado  
        WHERE
            Comentarios_Plan_Accion.id_plan = '$id_plan_accion'
        ORDER BY
            Comentarios_Plan_Accion.created_at DESC";

$res = mysqli_query($connect_okrs, $sql);

if (mysqli_num_rows($res) > 0) {

    while ($row = mysqli_fetch_assoc($res)) { ?>
        <div class="mb-2 p-2 border rounded">
            <b><?= $row["nombre_empleado"]; ?></b><br>
            <small class="text-muted"><?= $row["created_at"]; ?></small>
            <div><?= $row["comentario"]; ?></div>
        </div>
    <?php }
}else{
    echo '<span class="text-secondary">No hay comentarios relacionados</span>';
}

/* echo json_encode([
    "id_plan_accion" => $id_plan_accion,
    "resultadis" => mysqli_num_rows($res)
]); */
?>