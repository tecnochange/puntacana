<?php
include("../../app/connect.php");

$id_okr = $_GET["id_okr"];
$id_resultado = $_GET["id_resultado"];

$sql = "SELECT
            Documentos_Resultados.*,
            Empleados.nombre AS nombre_empleado
        FROM
            Documentos_Resultados
        LEFT JOIN
            goforagile_admin.Empleados AS Empleados ON Empleados.id = Documentos_Resultados.id_empleado  
        WHERE
            Documentos_Resultados.id_okrs = '$id_okr'
            AND Documentos_Resultados.id_resultado = '$id_resultado'
        ORDER BY
            Documentos_Resultados.created_at DESC";

$res = mysqli_query($connect_okrs, $sql);

$url = "https://goforagile.com/recursos/";

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