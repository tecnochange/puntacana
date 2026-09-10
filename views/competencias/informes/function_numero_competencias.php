<?php
                           
$id_evaluado = $_GET["e"];
include("views/competencias/informes/funciones.php");

function ResultadoLiderCompetencias( $id_user, $anio, $ciclo ){
    
    global $connect_valoracion;
    $sentecia_val_jefe = "SELECT obj_evaluacion FROM Competencias_Evaluaciones_New WHERE anio = '".$anio."' AND id_ciclo = '".$ciclo."' AND tipo_evaluacion = 5 AND id_evaluado = '".$id_user."' ";
    $qrValjefe = mysqli_query($connect_valoracion, $sentecia_val_jefe);
    $dtValJefe = mysqli_fetch_array($qrValjefe);

    
    $prom_global = 0;
    $count_global = 0;
    $objet = json_decode( $dtValJefe["obj_evaluacion"], true);

    
    foreach($objet as $comp){

        $datos = $comp["respuestas"];
        $promedio_respuestas = 0;
        $count_resp = 0;
        
        foreach( $datos as $resp ){
            $promedio_respuestas += $resp["respuesta"];
            $count_resp++;
        }
            

        
        $promedio_general_respuestas = $promedio_respuestas/$count_resp;
        $prom_global += $promedio_general_respuestas;

        $count_global++;
        

    }

    $final = 0;
    if($prom_global > 0){
        $final = $prom_global/$count_global;
    }

    $final = ($final*100)/5;

    return $final;
    
    
}

//FUNCTION PARA OBTENER EL CONSOLIDADO POR COMPETENCIAS
//FUNCTION PARA OBTENER EL CONSOLIDADO POR COMPETENCIAS
//FUNCTION PARA OBTENER EL CONSOLIDADO POR COMPETENCIAS
function ObtenerCompetenciasConsolidadasNew($competencia, $COMPETENCIAS, $EVALUACIONES, $lista_array_evaludadores, $array_final_grupos, $datos_ponderar){

    global $connect_valoracion;

    $promedio_total_comp = 0;
    $conteo = 0;

    //PREPARAMOS EL OBJETO CON LAS LISTA DE EVALUADORES SETEANDO POR COMPETENCIA
    foreach ($lista_array_evaludadores as $key => $obj_evaluador) {
        $lista_array_evaludadores[$key]["total_competencia"] = 0;
        $lista_array_evaludadores[$key]["cantidad_competencia"] = 0;
    }

    $array_comportamientos = array();
    $total = 0;
    $cantidad = 0;
    foreach ($EVALUACIONES as $evaluacion) {
        $Array_Objeto = json_decode($evaluacion["obj_evaluacion"], true);
        foreach ($Array_Objeto as $respuestas) {

            if ($respuestas["competencia"] == $competencia) {

                $respuestas_competencia = $respuestas["respuestas"];
                foreach ($respuestas_competencia as $resp) {
                    if ($resp["respuesta"] > 0) {

                        $total += $resp["respuesta"];
                        $cantidad++;

                        //CARGAMOS LOS DATOS POR EVALUADOR
                        foreach ($lista_array_evaludadores as $key => $obj_evaluador) {
                            if ($obj_evaluador["tipo"] == $evaluacion["tipo_evaluacion"] && $obj_evaluador["promedio"] != "") {
                                $lista_array_evaludadores[$key]["total_competencia"] += $resp["respuesta"];
                                $lista_array_evaludadores[$key]["cantidad_competencia"]++;
                                array_push($array_comportamientos, $resp["pregunta"]);
                            }
                        }
                    }
                }
            }
        }
    }

    // print_r($datos_ponderar);

    //SETEAMOS LOS GRUPOS
    foreach ($array_final_grupos as $nodo => $obj_evaluador_d) {
        $array_final_grupos[$nodo]["promedio"] = 0;
        $array_final_grupos[$nodo]["cantidad"] = 0;
        $array_final_grupos[$nodo]["sin_ponderacion"] = 0;
    }
    //CARGAMOS EL TOTAL A LOS GRUPOS
    foreach ($array_final_grupos as $llave => $grupo) {

        foreach ($lista_array_evaludadores as $obj_evaluador) {

            if ($obj_evaluador["tipo"] == $grupo["tipo"] && $obj_evaluador["promedio"] != "") {

                $prom = $obj_evaluador["total_competencia"] / $obj_evaluador["cantidad_competencia"];
                if (is_nan($prom)) {
                    $prom = 0;
                }
                // echo $datos_ponderar["auto"];
                if ($obj_evaluador["tipo"] == 1) {
                    $array_final_grupos[$llave]["promedio"] += ($prom * $datos_ponderar["auto"]) / 100;
                    $array_final_grupos[$llave]["cantidad"]++;
                    $array_final_grupos[$llave]["sin_ponderacion"] += $prom;
                }
                if ($obj_evaluador["tipo"] == 5) {
                    $array_final_grupos[$llave]["promedio"] += ($prom * $datos_ponderar["jefe"]) / 100;
                    $array_final_grupos[$llave]["cantidad"]++;
                    $array_final_grupos[$llave]["sin_ponderacion"] += $prom;
                }
                if ($obj_evaluador["tipo"] == 2) {
                    $array_final_grupos[$llave]["promedio"] += ($prom * $datos_ponderar["par"]) / 100;
                    $array_final_grupos[$llave]["cantidad"]++;
                    $array_final_grupos[$llave]["sin_ponderacion"] += $prom;
                }
                if ($obj_evaluador["tipo"] == 3) {
                    $array_final_grupos[$llave]["promedio"] += ($prom * $datos_ponderar["subalterno"]) / 100;
                    $array_final_grupos[$llave]["cantidad"]++;
                    $array_final_grupos[$llave]["sin_ponderacion"] += $prom;
                }
                if ($obj_evaluador["tipo"] == 4) {
                    $array_final_grupos[$llave]["promedio"] += ($prom * $datos_ponderar["cliente"]) / 100;
                    $array_final_grupos[$llave]["cantidad"]++;
                    $array_final_grupos[$llave]["sin_ponderacion"] += $prom;
                }
            }
        }
    }

    //FINALMENTE SUMAMOS Y PROMEDIAMOS
    foreach ($array_final_grupos as $obj_resultado) {
        $promedio_total_comp += ($obj_resultado["promedio"] / $obj_resultado["cantidad"]);
        $conteo++;
    }

    $array_comportamientos = array_unique($array_comportamientos);

    $nodo = array(
        "general" => $promedio_total_comp,
        "evaluadores" => $array_final_grupos,
        "comportamientos" => $array_comportamientos
    );

    return $nodo;
}

function ConsolidadoColaboradorCompetencias($id_empresa, $anio, $ciclo, $id_evaluado ){

    global $connect_valoracion;
    global $connect_admin;

    //OBTENEMOS EL PROMEDIO GENERAL
    $VALIDACION = PromedioGeneralEvaluado($id_evaluado, $connect_valoracion, $connect_admin);
    $datos_ponderar = $VALIDACION["datos_ponderar"];
    $tipo_ponderacion = $VALIDACION["tipo_ponderacion"];
    
    //OBTENEMOS LOS EVALUADORES
    $datos_generales = ValidarEvaluacionesCompletas($id_evaluado, $connect_valoracion, $connect_admin);
    $lista_array_evaludadores = $datos_generales["dato_evaluadores"];


    //OBTENEMOS LOS TIPOS DE EVALUACION AGRUPADA
    $array_agrupados = array();
    foreach ($lista_array_evaludadores as $key => $obj_evaluador) {
        if ($obj_evaluador["promedio"] != "") {
            array_push($array_agrupados,  $obj_evaluador["tipo"]);
        }
    }
    $array_agrupados = array_unique($array_agrupados);

    $array_final_grupos = array();
    foreach ($array_agrupados as $obj) {
        array_push($array_final_grupos, array("tipo" => $obj["tipo"], "promedio" => 0, "cantidad" => 0));
    }

    //TODAS LAS EVALUACIONES DEL EVALUADO
    $EVALUACIONES = array();
    $COMPETENCIAS = array();

    $sentencia_evaluaciones = "
    SELECT * FROM Competencias_Evaluaciones_New
    WHERE id_empresa = '" . $id_empresa . "' AND id_ciclo = '" . $ciclo . "' AND
    id_evaluado = '" . $id_evaluado . "' AND anio = '" . $anio . "' AND estado >= 2
    ORDER BY created_at DESC 
    ";
    $queryEvaluaciones = mysqli_query($connect_valoracion, $sentencia_evaluaciones);
    while ($dataEvaluacion = mysqli_fetch_array($queryEvaluaciones)) {

        $Array_Objeto = json_decode($dataEvaluacion["obj_evaluacion"], true);
        foreach ($Array_Objeto as $respuestas) {
            array_push($COMPETENCIAS, $respuestas["competencia"]);
        }
        $datos = array(
            "id_evaluador" => $dataEvaluacion["id_evaluador"],
            "obj_evaluacion" => $dataEvaluacion["obj_evaluacion"],
            "tipo_evaluacion" => $dataEvaluacion["tipo_evaluacion"]
        );
        array_push($EVALUACIONES, $datos);
    }

    $COMPETENCIAS = array_unique($COMPETENCIAS);

    $PROMEDIO_GLOBAL_REPORTE = 0;
    //BLOQUE DE FUNCIONES NUEVAS
    foreach ($COMPETENCIAS as $competencia) {
        $nodo_comp =  ObtenerCompetenciasConsolidadasNew($competencia, $COMPETENCIAS, $EVALUACIONES, $lista_array_evaludadores, $array_final_grupos, $datos_ponderar);

        $general = $nodo_comp["general"] * 100 / 5;
        $PROMEDIO_GLOBAL_REPORTE += $general;
    }
    if($PROMEDIO_GLOBAL_REPORTE > 0 ){
        $PROMEDIO_GLOBAL_REPORTE = $PROMEDIO_GLOBAL_REPORTE/count($COMPETENCIAS);
        $PROMEDIO_GLOBAL_REPORTE = round($PROMEDIO_GLOBAL_REPORTE,1);
    }
    if($tipo_ponderacion == '180'){ 
        $competencias = ResultadoLiderCompetencias( $id_evaluado, $_SESSION["anio_fill"], $_SESSION['ciclo'] );
        $competencias = round($competencias,2);
        //$PROMEDIO_GLOBAL_REPORTE = number_format($total_jefe, 1);
    }

    //echo $PROMEDIO_GLOBAL_REPORTE;

    return $PROMEDIO_GLOBAL_REPORTE;
}

//$competencias = ConsolidadoColaboradorCompetencias($_SESSION["id_empresa"], $_SESSION["anio_ciclo"], $_SESSION["ciclo"], $id_evaluado);
//echo $competencias;
?>
