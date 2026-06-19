<?php

function Subir_Documento($file) {
    $permitidos = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($extension, $permitidos)) {
        return false; // Archivo no permitido
    }
    
    // Limpiar nombre de archivo, pero conservar el punto antes de la extensión
    $nombreLimpio = preg_replace('/[^A-Za-z0-9_.-]/', '', pathinfo($file['name'], PATHINFO_FILENAME));
    $sku = time();
    $nombreFinal = $sku . $nombreLimpio . '.' . $extension;
    
    $dir_subida = '/var/www/html/goforagile.com/recursos';
    $fichero_subido = $dir_subida . $nombreFinal;

    if (move_uploaded_file($file['tmp_name'], $fichero_subido)) {
        return $nombreFinal;
    } else {
        return false; // Error al subir
    }
}

?>