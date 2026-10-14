<?php
include("../../app/connect.php");
include("../../app/arrays.php");
require_once __DIR__ . '/../../app/models/notificaciones/cargar.php';

$hoy = date("Y-m-d H:i:s");
$id = $_POST["id"];
$id_empresa = $_POST["id_empresa"];
$id_user = $_POST["id_user"];
$urlRedirect = $_POST["url"];

$tipo_kpi = $_POST["tipo_kpi"];
$id_kpi = $_POST["id_kpi"];
$descripcion = $_POST["descripcion"];


$fila_retirada = \Notificaciones\Integracion\AvisosKpis::filaDeIntegrante($connect_admin, (int) $id);
if (mysqli_query($connect_kpis, "DELETE FROM Kpis_Colaborador WHERE id = '" . $id . "' ")) {
    // Avisa a los Líderes del KPI (el módulo de notificaciones nunca interrumpe el guardado).
    \Notificaciones\Integracion\AvisosKpis::integranteRetirado($connect_admin, $fila_retirada, $_SESSION['id_user'] ?? $id_user);
}

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