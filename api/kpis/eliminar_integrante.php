<?php
include("../../app/connect.php");
include("../../app/arrays.php");

$hoy = date("Y-m-d H:i:s");
$id = $_POST["id"];
$id_empresa = $_POST["id_empresa"];
$id_user = $_POST["id_user"];
$urlRedirect = $_POST["url"];

$tipo_kpi = $_POST["tipo_kpi"];
$id_kpi = $_POST["id_kpi"];
$descripcion = $_POST["descripcion"];


mysqli_query($connect_kpis, "DELETE FROM Kpis_Colaborador WHERE id = '" . $id . "' ");

$sentencia_auditoria = "
INSERT INTO Auditoria_Kpi(
    id_empresa,
    id_empleado,
    accion,
    descripcion,
    id_kpi,
    tipo_kpi,
    created_at
)
VALUES(
    '".$id_empresa."',
    '".$id_user."',
    'ELIMINAR',
    '".$descripcion."', 
    '".$id_kpi."',
    '".$tipo_kpi."',
    '".$hoy."'
)
";

mysqli_query($connect_kpis, $sentencia_auditoria);

?>

<script>
    window.location = "<?php echo $urlRedirect; ?>";
</script>