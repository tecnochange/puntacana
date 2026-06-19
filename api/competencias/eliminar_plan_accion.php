<?php
include("../../app/connect.php");

$hoy = date("Y-m-d H:i:s");
$id_plan = $_POST["id_plan"];

$urlRedirect = $_POST["url"];

mysqli_query($connect_valoracion, "DELETE FROM Pdi_Competencias WHERE id = '" . $id_plan . "'  ");
?>


<script>
    window.location = "<?php echo $urlRedirect; ?>";
</script>