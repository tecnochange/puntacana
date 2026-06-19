<?php
include("../../app/connect.php");

$id = $_POST["id"];
$id_empresa = $_POST["id_empresa"];
$urlRedirect = $_POST["url"];
$id_user = $_POST["id_user"];
$descripcion = $_POST["descripcion"];
$tipo_okr = $_POST["tipo_okr"];
$id_okr = $_POST["id_okr"];
$hoy = date("Y-m-d H:i:s");

//echo "DELETE FROM Okrs_Equipos WHERE id = '".$id."' AND id_empresa = '".$id_empresa."' ";

mysqli_query($connect_okrs, "DELETE FROM Okrs_Equipos WHERE id = '".$id."' AND id_empresa = '".$id_empresa."'  ");

$sentencia_auditoria = "
INSERT INTO Auditoria_Okrs(
    id_empresa,
    id_empleado,
    accion,
    descripcion,
    tipo_okr,
    id_okr,
    id_kr,
    id_iniciativa,
    created_at
)
VALUES(
    '".$id_empresa."',
    '".$id_user."',
    'ELIMINAR',
    '".$descripcion."',
    '".$tipo_okr."',
    '".$id_okr."',
    '0',
    '0',
    '".$hoy."'
)
";

//echo $sentencia_auditoria;
mysqli_query($connect_okrs, $sentencia_auditoria);



?>

<script>
    window.location = "<?php echo $urlRedirect; ?>";
</script>