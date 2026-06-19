<?php
include("../../app/connect.php");

date_default_timezone_set("America/Bogota");

// DATOS
$id_plan_accion = $_POST["id_plan_accion"];
$id_empresa = $_POST["id_empresa"];
$id_empleado = $_POST["id_empleado"];
$comentario = trim($_POST["comentario"]);
$hoy = date("Y-m-d H:i:s");

// ARCHIVO
$documento = $_FILES["documento"];

// Generar nombre único (evita sobreescritura)
// Carpeta destino
$carpeta = "/var/www/html/goforagile.com/recursos/";

$nombre_archivo = uniqid() . "_" . basename($documento["name"]);
$tmp = $documento["tmp_name"];

$ruta = $carpeta . $nombre_archivo;

// SEGURIDAD SQL
$id_empresa = mysqli_real_escape_string($connect_okrs, $id_empresa);
$id_empleado = mysqli_real_escape_string($connect_okrs, $id_empleado);
$comentario = mysqli_real_escape_string($connect_okrs, $comentario);

try {

    // Guardar archivo primero
    if (!is_writable("/var/www/html/goforagile.com/recursos/")) {
        die("No hay permisos de escritura");
    }

    if (!move_uploaded_file($tmp, $ruta)) {
        echo json_encode(["ok" => false, "error" => "No se pudo guardar el archivo"]);
        exit;
    }

    $sql = "INSERT INTO Documentos_Plan_Accion 
    (id_empresa, id_empleado, id_plan, archivo, comentario, created_at)
    VALUES 
    ('$id_empresa', '$id_empleado', '$id_plan_accion', '$nombre_archivo', '$comentario', '$hoy')";

    if (!mysqli_query($connect_okrs, $sql)) {
        echo json_encode(["ok" => false, "error" => "Error en la base de datos"]);
        exit;
    }

    // RESPUESTA OK
    echo json_encode([
        "ok" => true,
        "id_empresa" => $id_empresa,
        "id_empleado" => $id_empleado,
        "id_plan_accion" => $id_plan_accion,
        "archivo" => $nombre_archivo,
        "comentario" => $comentario
    ]);

} catch (\Throwable $th) {
    echo json_encode([
        "ok" => false,
        "error" => "Excepción del servidor"
    ]);
}