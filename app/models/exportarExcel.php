<?php
$fecha = date("d-m-Y");

$nombre_file = 'GoForAgile_'.$fecha.'.xls';

if(isset($_POST["nombre_reporte"]) && $_POST["nombre_reporte"] != ""){
    $nombre_file = $_POST["nombre_reporte"].'_'.$fecha.'.xls';
}

header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=".$nombre_file);
header("Pragma: no-cache");
header("Expires: 0");

// 🔥 CLAVE: BOM para Excel
echo "\xEF\xBB\xBF";

if(isset($_POST['datos_a_enviar'])){
    echo "<table border='1'>";
    echo $_POST['datos_a_enviar'];
    echo "</table>";
}else{
    echo "No hay datos";
}
?>