<?php
include("../../app/connect.php");

$entidad = $_POST["entidad"];
$id_registro = $_POST["id_registro"];
$valor = $_POST["valor"];

$tablas_permitidas = [
    "Okrs_Resultados",
    "Okrs_Iniciativas",
    "Okrs_Actividades"
];

// Validamos la entidad
if (!in_array($entidad, $tablas_permitidas)) {
    echo json_encode([
        "status" => "error",
        "message" => "Entidad no permitida"
    ]);
    exit;
}

if($entidad == "Okrs_Actividades"){
    $sentencia = "UPDATE {$entidad} SET progreso = {$valor} WHERE id = {$id_registro}";
}else{
    $sentencia = "UPDATE {$entidad} SET avance = {$valor} WHERE id = {$id_registro}";
}
$query = mysqli_query($connect_okrs, $sentencia);

echo json_encode([
    "status" => "success"
]);
?>