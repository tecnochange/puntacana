<?php
include("../../app/connect.php");

$id_okr = $_GET["id_okr"];
$id_resultado = $_GET["id_resultado"];
$id_iniciativa = $_GET["id_iniciativa"];

$sql = "SELECT
            Okrs_Comentarios_Iniciativas.*,
            Empleados.nombre AS nombre_empleado
        FROM
            Okrs_Comentarios_Iniciativas
        LEFT JOIN
            puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Comentarios_Iniciativas.id_empleado  
        WHERE
            Okrs_Comentarios_Iniciativas.id_okrs = '$id_okr'
            AND Okrs_Comentarios_Iniciativas.id_resultado = '$id_resultado'
            AND Okrs_Comentarios_Iniciativas.id_iniciativa = '$id_iniciativa'
        ORDER BY
            Okrs_Comentarios_Iniciativas.created_at DESC";

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
?>