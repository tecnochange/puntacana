<?php
include("../../app/connect.php");
include("../../app/arrays.php");
$hoy = date("Y-m-d H:i:s");

$idEmpresa = $_POST["id_empresa"];
$url = $_POST["url"];
$ciclo = $_POST['ciclo'];
$anio_ciclo = $_POST['anio_ciclo'];
$tipoEvaluacion = $_POST['tipo_evaluacion'] ?? '';
$vicepresidencia = $_POST['vicepresidencia'] ?? '';
$idEmpleadoSession = $_POST['identidad'];
$rol = $_POST['rol'];
//filtro_vp

// Filtro por Relaciones laborales
$filtroInRol ='';
if(isset($rol) && $rol == 2){
    $directiva = [];
    $smtpRelacionesLaborales =  "SELECT id_vp as directiva FROM  `Relaciones_Laborales` WHERE id_empresa = $idEmpresa and id_empleado = $idEmpleadoSession AND estado = 1 AND mod_competencias = 'on' ";
    $resultadoRelLabRole = mysqli_query($connect_valentina, $smtpRelacionesLaborales);
    while ($rowRelLabRol = mysqli_fetch_assoc($resultadoRelLabRole)){
        $directiva [] = $rowRelLabRol['directiva'];
    }
    if(!empty($directiva)){
        $filtroInRol = ' AND id_vicepresidencia IN ('.implode(',',$directiva).')';
    }
}

//Filtro por vicepresicendia
$filtroVice = '';
if(isset($vicepresidencia) && $vicepresidencia != ''){
   //limpia las vicepresidencias anteriores para buscar ESPECIFICAMENTE la seleccionada.
    $filtroInRol = '';
    $filtroVice = " AND id_vicepresidencia IN ($vicepresidencia)";
}

//Procesos de valoración
$ListaProcesoValoracion = [];

$queryProcesosValoracion = "SELECT id, etiqueta, color_fondo as fondo, color_fuente as colorTexto FROM Proceso_Valoracion";
$resultProcesosValoracion = mysqli_query($connect_valoracion, $queryProcesosValoracion);

while ($row = mysqli_fetch_assoc($resultProcesosValoracion)) {
    $ListaProcesoValoracion[] = [
        (string)$row['id'],  // ID como string para que coincida con el JSON
        $row['etiqueta'] ?: "Sin Valoración",
        $row['fondo'] ?: "#f76d6b",
        $row['colorTexto'] ?: "white",
    ];
}

if (!empty($tipoEvaluacion)) {
    $where = " AND tipo_evaluacion = '".intval($tipoEvaluacion)."' ";
}






//Vista optmizada con todos los datos necesarios
mysqli_query($connect_valoracion, "SET SQL_BIG_SELECTS=1");
$queryGeneral = "SELECT SQL_CALC_FOUND_ROWS id_empleado, doc_empleado, nombre_empleado, nombre_cargo, nombre_area, nombre_vp, doc_jefe, nombre_jefe, tipo_evaluador, proceso_valoracion, etiqueta, fondo, colorTexto FROM vista_general_evaluaciones WHERE ciclo = '".$ciclo."' AND anio = '".$anio_ciclo."' AND empresa = $idEmpresa   $filtroInRol $filtroVice $where ORDER BY nombre_empleado ASC";
$resultEmpleados = mysqli_query($connect_valoracion, $queryGeneral);

// id_empleado IN (96, 1320, 143, 38) AND


$data = [];
$contador = 1;
while ($row = mysqli_fetch_assoc($resultEmpleados)) {

//print_r($row);



    $idEmpleado = $row['id_empleado'];
    $proceso_valoracion = $row['proceso_valoracion'];

    /**Botón de reporte */
    $btn_reporte = "";
    //if($row['proceso_valoracion']>= 2){
        $btn_reporte = '<a href="?pg=competencias/informes/reporte_individual&e=' . $idEmpleado . '" target="_blank" >
                    <button type="button" class="btn btn-primary" style="border-radius: 8px; margin: 1px; font-size: 0.8rem !important">
                        Reporte
                    </button>
                </a>';
    //}
    $estadoEvaluacion = $row['proceso_valoracion'] ? [
        "estado" => $row['etiqueta'],
        "background" => $row['fondo'],
        "color" => $row['colorTexto'],
    ] : [
        "estado" => "Sin Valoración",
        "background" => "#f76d6b",
        "color" => "white",
    ];
    /** Contadores para las tarjetas */
    $arrayTotalProceso[$proceso_valoracion] = ($arrayTotalProceso[$proceso_valoracion]??0)+1;
    if($proceso_valoracion==1) $sumProceso++;

    /**Confirmar % de valoración */
    $qyPorcentajeEvaluacion = "SELECT SQL_CALC_FOUND_ROWS `id`, `obj_evaluacion`, `tipo_evaluacion` FROM `Competencias_Evaluaciones_New` WHERE `id_ciclo` = '".$ciclo."' AND `anio` = '".$anio_ciclo."' AND `id_empresa` = $idEmpresa AND `id_evaluado` = $idEmpleado";

    $resultPorcentEval = mysqli_query($connect_valoracion, $qyPorcentajeEvaluacion);
    $promedio = 0;
    while ($rowPorcnt = mysqli_fetch_assoc($resultPorcentEval)) {
        if (!empty($rowPorcnt['obj_evaluacion'])) {
            $promedio = obtenerPromedioEvaluacion( $rowPorcnt['obj_evaluacion'] );
        } else {
            $promedio = 0;
        }

        //echo $promedio;

        if( $rowPorcnt['tipo_evaluacion'] == 1 ){
            $queryValJefe = mysqli_query($connect_valoracion, "SELECT id FROM `Competencias_Evaluaciones_New` WHERE `tipo_evaluacion` = 5 AND `id_evaluado` = '".$idEmpleado."' AND `anio` = '".$anio_ciclo."' AND `id_ciclo` = '".$ciclo."' ");

            if($queryValJefe->num_rows == 0){
                $promedio = 0;
                $estadoEvaluacion["estado"] = "Autoevaluación";

            }
        }

        if( $rowPorcnt['tipo_evaluacion'] == 5 ){


            $estadoEvaluacion["estado"] = "Autoevaluación";
        }
    }

    if($estadoEvaluacion["estado"] == "Sin Valoración"){
        //$promedio = 0;
    }


//"proceso_valoracion" => $estadoEvaluacion,
    $data[] = [
            "contador" => $contador++,
            "documento" => $row['doc_empleado'],
            "evaluado" => $row['nombre_empleado'],
            "cargo" => $row['nombre_cargo'],
            "vicepresidencia" =>$row['nombre_vp'],
            "area" => $row['nombre_area'],
            "documento_evaluador" => $row['doc_jefe'],
            "evaluador" => $row['nombre_jefe'],
            "tipo"=>$row['tipo_evaluador'],
            
            "reporte" => $btn_reporte,
            "promedio" =>$promedio
        ];
}

/**Función para traer el % total de su evaluación*/
function obtenerPromedioEvaluacion($obj)
{
    // Decodificamos el JSON
    $data = json_decode($obj, true);
    if (!is_array($data) || empty($data)) {
        return 0;
    }
    $totalPorcentajes = 0;
    $totalCompetencias = 0;

    foreach ($data as $competencia) {
        if (!isset($competencia['respuestas']) || !is_array($competencia['respuestas']) || count($competencia['respuestas']) === 0) {
            continue;
        }
        $respuestas = $competencia['respuestas'];
        $suma = 0;
        $cantidad = count($respuestas);

        foreach ($respuestas as $respuesta) {
            $suma += intval($respuesta['respuesta']);
        }

        // Validar que cantidad > 0
        if ($cantidad > 0) {
            $porcentaje = ($suma / ($cantidad * 5)) * 100;
            $totalPorcentajes += $porcentaje;
            $totalCompetencias++;
        }
        // Validar que hay competencias válidas
        if ($totalCompetencias === 0) {
            return 0;
        }
    }

    // Promedio final de todas las competencias
    $promedioFinal = round($totalPorcentajes / $totalCompetencias, 2);

    return $promedioFinal;
}

// Preparar respuesta para DataTable
header('Content-Type: application/json');
// Preparar respuesta para DataTable
$response = [
    "data" => $data,
    "sumProceso" => $sumProceso,
    "countValoracion" => count($data),
    "arrayTotalProceso" => $arrayTotalProceso,
    "Array_Proceso_Valoracion" => $ListaProcesoValoracion,
    "datos" => $queryGeneral,
    "rol" => $rol,
    "keyEmpleado" => $idEmpleadoSession,
    "vice" =>$vicepresidencia
];


echo json_encode($response);
exit;