<option value="-1">Seleccione objetivo SG...</option>
<?php
include("../../app/connect.php");
$id_empresa = $_POST["id_empresa"];
$id_vicepresidencia = $_POST["id_vicepresidencia"];
$id_area = $_POST["id_area"];

$filtro = "";
if($id_vicepresidencia > 0){
    $filtro .= " AND id_vp = '".$id_vicepresidencia."'  "; 
}
if($id_area > 0){
    //$filtro .= " AND id_area = '".$id_area ."'  "; 
}

$sentencia = "
SELECT
    *
FROM
    Objetivo_Sg 
WHERE
    id_empresa = '".$id_empresa."' 
    ".$filtro." 
    ORDER BY Objetivo_Sg.objetivo ASC  
";

//echo $sentencia;

$query = mysqli_query($connect_kpis, $sentencia);
while ($data = mysqli_fetch_assoc($query)) {

    echo '<option value="' . $data["id"] . '">' . $data["objetivo"] . '</option>';
        
}


?>