<?php
// if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
//     http_response_code(403);
//     exit('Acceso no autorizado');
// }
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"),true);

include("/var/www/html/puntacana.goforagile.com/app/connect.php");
include("/var/www/html/puntacana.goforagile.com/app/arrays.php");
$hoy = date("Y-m-d H:i:s");


$ciclo = $data["ciclo"];
$descripcion_html = $data["descripcion_html"];
$id_empresa = $data["id_empresa"];
$id_evaluacion = $data["id_evaluacion"];
$id_evaluado = $data["id_evaluado"];
$id_responsable = $data["id_responsable"];

$queryCiclo = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id = '" .$ciclo. "' ");
$dataCiclo = mysqli_fetch_array($queryCiclo);


$sentencia = "UPDATE Intervencion_Gh SET comentario = '".$descripcion_html."' WHERE id_empresa = '".$id_empresa."' AND id_responsable = '".$id_responsable."' AND id_evaluado = '".$id_evaluado."' AND ciclo = '".$dataCiclo["anio"]."' ";
mysqli_query($connect_valoracion, $sentencia);

$sentencia_intervecion = "UPDATE Competencias_Evaluaciones_New SET proceso_valoracion = 8 WHERE id_empresa = '".$id_empresa."' AND id_evaluado = '".$id_evaluado."' AND id_ciclo = '".$ciclo."' ";
mysqli_query($connect_valoracion, $sentencia_intervecion);

echo json_encode([
                'success' => true,
                'comentario' => $descripcion_html
            ]);


//$queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $_POST["id_competencia"] . "' ");
//$dataComp = mysqli_fetch_array($queryComp);






















/*
require_once '/var/www/html/puntacana.goforagile.com/core/Database.php';
require_once '/var/www/html/puntacana.goforagile.com/models/competencias/IntervencionGh.php';

$data = json_decode(file_get_contents("php://input"),true);
$html = isset($data['descripcion_html'])? $data['descripcion_html'] : '';
$idEmpresa = $data['id_empresa'] ?? null;
$idInterventorGH = $data['id_responsable'] ?? null;
$idEvaluado = $data['id_evaluado'] ?? null;
$idEvaluacion = $data['id_evaluacion'] ?? null;
$ciclo = $data['ciclo'] ?? null;
if($html){
    //Sanear html
    $allowed = '<p><b><strong><i><u><em><br><ul><ol><li><span><div><a><img><h1><h2><h3><h4><h5><h6>';
    $html_clean = strip_tags($html,$allowed);
    try {
        $db = Database::connect('competencias');
        $model = new IntervencionGh($db);

        $result = $model->guardarYCerrarIntervencionGH([
            'descripcion' => $html_clean,
            'empresa' => $idEmpresa,
            'interventor' => $idInterventorGH,
            'evaluado' => $idEvaluado,
            'evaluacion' => $idEvaluacion,
            'ciclo' => $ciclo,
            'fecha' => date('Y-m-d H:i:s'),
        ]);
        if ($result) {
            echo json_encode([
                'success' => true,
                'comentario' => $result['comentario']
            ]);
        }else{
            echo json_encode([
                'success' => false,
                'message' => 'Error al guardar la intervención'
            ]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Excepción: ' . $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Faltan datos']);
}

*/