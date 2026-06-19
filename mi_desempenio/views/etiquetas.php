<?php
$arrayEtiquetas = array();

$queryEA = mysqli_query($connect_admin, "SELECT * FROM Etiquetas_Empresa WHERE id_empresa = " . $_SESSION["id_empresa_valentina"] . " AND estado = 1 AND tipo_etiqueta = 1");
while ($dataEA = mysqli_fetch_array($queryEA)) {
    $arrayEtiquetas[$dataEA["id_etiqueta"]]["etiqueta"] = $dataEA["nombre"];
}

$etiquetaAdminDE = $arrayEtiquetas[1]["etiqueta"];
$etiquetaAdminVP = $arrayEtiquetas[2]["etiqueta"];
$etiquetaAdminArea = $arrayEtiquetas[3]["etiqueta"];
$etiquetaAdminUO = $arrayEtiquetas[4]["etiqueta"];
$etiquetaAdminNJ = $arrayEtiquetas[5]["etiqueta"];
$etiquetaAdminCargo = $arrayEtiquetas[6]["etiqueta"];