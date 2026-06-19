<?php
include("../../app/connect.php");
include("../../app/arrays.php");

$hoy = date("Y-m-d H:i:s");
$id = $_POST["id"];
$id_empresa = $_POST["id_empresa"];
$url_rediret = $_POST["url"];

//echo "DELETE FROM Lideres WHERE id = '" . $id . "' AND id_empresa = '".$id_empresa."' ";

mysqli_query($connect_admin, "DELETE FROM Lideres WHERE id = '" . $id . "' AND id_empresa = '".$id_empresa."' ");

?>

<script>
    window.location = "<?php echo $url_rediret; ?>";
</script>