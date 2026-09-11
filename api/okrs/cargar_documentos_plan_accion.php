<?php
include("../../app/connect.php");

$id_plan_accion = $_GET["id_plan_accion"];

$sql = "SELECT
            Documentos_Plan_Accion.*,
            Empleados.nombre AS nombre_empleado
        FROM
            Documentos_Plan_Accion
        LEFT JOIN
            puntacana_admin.Empleados AS Empleados ON Empleados.id = Documentos_Plan_Accion.id_empleado  
        WHERE
            Documentos_Plan_Accion.id_plan = '$id_plan_accion'
        ORDER BY
            Documentos_Plan_Accion.created_at DESC";

$res = mysqli_query($connect_okrs, $sql);

$url = $recursos_local;

if (mysqli_num_rows($res) > 0) {

    while ($row = mysqli_fetch_assoc($res)) { ?>
        <div class="mb-2 p-2 border rounded">
            <div class="row">
                <div class="col-md-3">
                    <b><?= $row["nombre_empleado"]; ?></b><br>
                    <small class="text-muted"><?= $row["created_at"]; ?></small>
                </div>
                <div class="col-md-3">
                    <a target="_blank" href="<?= $url.$row["archivo"] ?>"><i class="bi bi-file-text"></i> <?= $row["archivo"]; ?></a>
                </div>
                <div class="col-md-5">
                    <?= $row["comentario"]; ?>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-sm btn-danger disabled"><i class="bi bi-trash3" title="Eliminar Documento"></i></button>    
                </div>
            </div>
        </div>
    <?php }
}else{
    echo '<span class="text-secondary">No hay documentos relacionados.</span>';
}
?>