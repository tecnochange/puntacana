<?php
include("../../app/connect.php");
require_once __DIR__ . '/../../app/models/notificaciones/cargar.php';
$id_empresa = $_POST["id_empresa"];
$id_plan = $_POST["id_plan"];
$id_colaborador = $_POST["id_colaborador"];
$urlRedirect  = $_POST["url"];

$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Actividades WHERE id_empresa = '" . $_POST["id_empresa"] . "' AND id = '".$id_plan."' ");
$data = mysqli_fetch_array($query);
$array_responsables = explode(",", $data["id_asignado"]);

print_r($array_responsables);

$insertar = true;
foreach($array_responsables as $responsable){
    if($responsable == $id_colaborador ){
        $insertar = false;
    }
}

if($insertar){
    array_push($array_responsables, $id_colaborador);
}

$lista_txt = implode(",", $array_responsables);



$sentencia = "UPDATE Okrs_Actividades SET id_asignado = '".$lista_txt."' WHERE id = '".$id_plan."' AND id_empresa = '".$id_empresa."' ";
if (mysqli_query($connect_okrs, $sentencia) && $insertar) {
    // Avisa al nuevo asignado (el módulo de notificaciones nunca interrumpe el guardado).
    \Notificaciones\Integracion\AvisosOkrs::planAccionGuardado($connect_admin, (int) $id_plan, explode(",", $data["id_asignado"]), $_SESSION['id_user'] ?? null);
}
?>

<script>
    window.location = "<?php echo $urlRedirect; ?>";
</script>