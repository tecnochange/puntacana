<?php

//FUNCTION PARA OBTENER EL CONSOLIDADO POR COMPETENCIAS
//FUNCTION PARA OBTENER EL CONSOLIDADO POR COMPETENCIAS
//FUNCTION PARA OBTENER EL CONSOLIDADO POR COMPETENCIAS
function ObtenerCompetenciasConsolidadas($competencia, $COMPETENCIAS, $EVALUACIONES){
    global $lista_array_evaludadores;
    global $array_final_grupos;
    global $datos_ponderar;
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

//FUNCTION PARA OBTENER EL CONSOLIDADO POR COMPORTAMIENTO
//FUNCTION PARA OBTENER EL CONSOLIDADO POR COMPORTAMIENTO
//FUNCTION PARA OBTENER EL CONSOLIDADO POR COMPORTAMIENTO
function ObtenerComportamientosConsolidadas($competencia, $comportamiento, $COMPETENCIAS, $EVALUACIONES){
    global $lista_array_evaludadores;
    global $array_final_grupos;
    global $datos_ponderar;
    global $connect_valoracion;

    // print_r($datos_ponderar);

    $promedio_total_comp = 0;
    $conteo = 0;

    //PREPARAMOS EL OBJETO CON LAS LISTA DE EVALUADORES SETEANDO POR COMPETENCIA

    foreach ($lista_array_evaludadores as $key => $obj_evaluador) {
        $lista_array_evaludadores[$key]["total_comportamiento"] = 0;
        $lista_array_evaludadores[$key]["cantidad_comportamiento"] = 0;
    }

    // print_r($lista_array_evaludadores);

    $total = 0;
    $cantidad = 0;
    foreach ($EVALUACIONES as $evaluacion) {
        $Array_Objeto = json_decode($evaluacion["obj_evaluacion"], true);
        foreach ($Array_Objeto as $respuestas) {

            if ($respuestas["competencia"] == $competencia) {

                $respuestas_competencia = $respuestas["respuestas"];
                foreach ($respuestas_competencia as $resp) {
                    if ($resp["respuesta"] > 0) {

                        if ($comportamiento == $resp["pregunta"]) {

                            $total += $resp["respuesta"];
                            $cantidad++;

                            //CARGAMOS LOS DATOS POR EVALUADOR
                            foreach ($lista_array_evaludadores as $key => $obj_evaluador) {
                                if ($obj_evaluador["tipo"] == $evaluacion["tipo_evaluacion"] && $obj_evaluador["promedio"] != "") {
                                    $lista_array_evaludadores[$key]["total_comportamiento"] += $resp["respuesta"];
                                    $lista_array_evaludadores[$key]["cantidad_comportamiento"]++;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    // print_r($lista_array_evaludadores);

    //SETEAMOS LOS GRUPOS
    foreach ($array_final_grupos as $nodo => $obj_evaluador_d) {
        $array_final_grupos[$nodo]["promedio"] = 0;
        $array_final_grupos[$nodo]["cantidad"] = 0;
        $array_final_grupos[$nodo]["sin_ponderacion"] = 0;
    }

    //CARGAMOS EL TOTAL A LOS GRUPOS
    foreach ($array_final_grupos as $llave => $grupo) {

        foreach ($lista_array_evaludadores as $obj_evaluador) {
            // print_r($obj_evaluador);
            if ($obj_evaluador["tipo"] == $grupo["tipo"] && $obj_evaluador["promedio"] != "") {

                $prom = $obj_evaluador["total_comportamiento"] / $obj_evaluador["cantidad_comportamiento"];

                if (is_nan($prom)) {
                    $prom = 0;
                }

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
    // print_r($array_final_grupos);
    //FINALMENTE SUMAMOS Y PROMEDIAMOS
    foreach ($array_final_grupos as $obj_resultado) {
        $promedio_total_comp += ($obj_resultado["promedio"] / $obj_resultado["cantidad"]);
        $conteo++;
    }

    $nodo = array(
        "general" => $promedio_total_comp,
        "evaluadores" => $array_final_grupos
    );

    return $nodo;
}

function ObtenerComportamientosConsolidadasPreguntas($competencia, $comportamiento, $COMPETENCIAS, $EVALUACIONES){ 
    global $lista_array_evaludadores;
    global $array_final_grupos;
    global $datos_ponderar;
    global $connect_valoracion;

    //print_r($datos_ponderar);

    $promedio_total_comp = 0;
    $conteo = 0;

    //PREPARAMOS EL OBJETO CON LAS LISTA DE EVALUADORES SETEANDO POR COMPETENCIA

    foreach ($lista_array_evaludadores as $key => $obj_evaluador) {
        $lista_array_evaludadores[$key]["total_comportamiento"] = 0;
        $lista_array_evaludadores[$key]["cantidad_comportamiento"] = 0;
    }

    // print_r($lista_array_evaludadores);

    $total = 0;
    $cantidad = 0;
    foreach ($EVALUACIONES as $evaluacion) {
        $Array_Objeto = json_decode($evaluacion["obj_evaluacion"], true);
        foreach ($Array_Objeto as $respuestas) {

            if ($respuestas["competencia"] == $competencia) {

                $respuestas_competencia = $respuestas["respuestas"];
                foreach ($respuestas_competencia as $resp) {
                    if ($resp["respuesta"] > 0) {

                        if ($comportamiento == $resp["pregunta"]) {

                            

                            $total += $resp["respuesta"];
                            $cantidad++;

                            //CARGAMOS LOS DATOS POR EVALUADOR
                            foreach ($lista_array_evaludadores as $key => $obj_evaluador) {
                                if ($obj_evaluador["tipo"] == $evaluacion["tipo_evaluacion"] && $obj_evaluador["promedio"] != "") {
                                    $lista_array_evaludadores[$key]["total_comportamiento"] += $resp["respuesta"];
                                    $lista_array_evaludadores[$key]["cantidad_comportamiento"]++;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    //SETEAMOS LOS GRUPOS
    foreach ($array_final_grupos as $nodo => $obj_evaluador_d) {
        $array_final_grupos[$nodo]["promedio"] = 0;
        $array_final_grupos[$nodo]["cantidad"] = 0;
        $array_final_grupos[$nodo]["sin_ponderacion"] = 0;
    }

    
    //CARGAMOS EL TOTAL A LOS GRUPOS
    foreach ($array_final_grupos as $llave => $grupo) {

        foreach ($lista_array_evaludadores as $obj_evaluador) {
            // print_r($obj_evaluador);
            if ($obj_evaluador["tipo"] == $grupo["tipo"] && $obj_evaluador["promedio"] != "") {

                $prom = $obj_evaluador["total_comportamiento"] / $obj_evaluador["cantidad_comportamiento"];

                //$datos_ponderar["auto"] = 50;

                if (is_nan($prom)) {
                    $prom = 0;
                }

                if ($obj_evaluador["tipo"] == 1) {                    
                    if($datos_ponderar["auto"]){
                        $array_final_grupos[$llave]["promedio"] += ($prom * $datos_ponderar["auto"]) / 100;
                    }else{
                        //$array_final_grupos[$llave]["promedio"] += $prom;
                    }
                    
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
    // print_r($array_final_grupos);
    //FINALMENTE SUMAMOS Y PROMEDIAMOS
    foreach ($array_final_grupos as $obj_resultado) {
        $promedio_total_comp += ($obj_resultado["promedio"] / $obj_resultado["cantidad"]);
        $conteo++;
    }

    $nodo = array(
        "general" => $promedio_total_comp,
        "evaluadores" => $array_final_grupos
    );

    return $nodo;
}


function ObtenerComportamientosSinConsolidarPreguntas($competencia, $comportamiento, $COMPETENCIAS, $EVALUACIONES){ 
    global $lista_array_evaludadores;
    global $array_final_grupos;
    global $connect_valoracion;

    global $datos_ponderar;

    $ponderacion_plana = [];
    $ponderacion_plana["auto"] = 100;
    $ponderacion_plana["jefe"] = 100;
    $ponderacion_plana["par"] = 100;
    $ponderacion_plana["subalterno"] = 100;
    $ponderacion_plana["cliente"] = 100;

    //print_r($datos_ponderar);

    $promedio_total_comp = 0;
    $conteo = 0;

    //PREPARAMOS EL OBJETO CON LAS LISTA DE EVALUADORES SETEANDO POR COMPETENCIA

    foreach ($lista_array_evaludadores as $key => $obj_evaluador) {
        $lista_array_evaludadores[$key]["total_comportamiento"] = 0;
        $lista_array_evaludadores[$key]["cantidad_comportamiento"] = 0;
    }

    // print_r($lista_array_evaludadores);

    $total = 0;
    $cantidad = 0;
    foreach ($EVALUACIONES as $evaluacion) {
        $Array_Objeto = json_decode($evaluacion["obj_evaluacion"], true);
        foreach ($Array_Objeto as $respuestas) {

            if ($respuestas["competencia"] == $competencia) {

                $respuestas_competencia = $respuestas["respuestas"];
                foreach ($respuestas_competencia as $resp) {
                    if ($resp["respuesta"] > 0) {

                        if ($comportamiento == $resp["pregunta"]) {

                            

                            $total += $resp["respuesta"];
                            $cantidad++;

                            //CARGAMOS LOS DATOS POR EVALUADOR
                            foreach ($lista_array_evaludadores as $key => $obj_evaluador) {
                                if ($obj_evaluador["tipo"] == $evaluacion["tipo_evaluacion"] && $obj_evaluador["promedio"] != "") {
                                    $lista_array_evaludadores[$key]["total_comportamiento"] += $resp["respuesta"];
                                    $lista_array_evaludadores[$key]["cantidad_comportamiento"]++;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    //SETEAMOS LOS GRUPOS
    foreach ($array_final_grupos as $nodo => $obj_evaluador_d) {
        $array_final_grupos[$nodo]["promedio"] = 0;
        $array_final_grupos[$nodo]["cantidad"] = 0;
        $array_final_grupos[$nodo]["sin_ponderacion"] = 0;
    }

    
    //CARGAMOS EL TOTAL A LOS GRUPOS
    foreach ($array_final_grupos as $llave => $grupo) {

        foreach ($lista_array_evaludadores as $obj_evaluador) {
            // print_r($obj_evaluador);
            if ($obj_evaluador["tipo"] == $grupo["tipo"] && $obj_evaluador["promedio"] != "") {

                $prom = $obj_evaluador["total_comportamiento"] / $obj_evaluador["cantidad_comportamiento"];

                //$datos_ponderar["auto"] = 50;

                if (is_nan($prom)) {
                    $prom = 0;
                }

                if ($obj_evaluador["tipo"] == 1) {                    
                    if($ponderacion_plana["auto"]){
                        $array_final_grupos[$llave]["promedio"] += ($prom * $ponderacion_plana["auto"]) / 100;
                    }else{
                        //$array_final_grupos[$llave]["promedio"] += $prom;
                    }
                    
                    $array_final_grupos[$llave]["cantidad"]++;
                    $array_final_grupos[$llave]["sin_ponderacion"] += $prom;
                }
                if ($obj_evaluador["tipo"] == 5) {       
                    
                    $array_final_grupos[$llave]["promedio"] += ($prom * $ponderacion_plana["jefe"]) / 100;
                    $array_final_grupos[$llave]["cantidad"]++;
                    $array_final_grupos[$llave]["sin_ponderacion"] += $prom;
                }
                if ($obj_evaluador["tipo"] == 2) {
                    $array_final_grupos[$llave]["promedio"] += ($prom * $ponderacion_plana["par"]) / 100;
                    $array_final_grupos[$llave]["cantidad"]++;
                    $array_final_grupos[$llave]["sin_ponderacion"] += $prom;
                }
                if ($obj_evaluador["tipo"] == 3) {
                    $array_final_grupos[$llave]["promedio"] += ($prom * $ponderacion_plana["subalterno"]) / 100;
                    $array_final_grupos[$llave]["cantidad"]++;
                    $array_final_grupos[$llave]["sin_ponderacion"] += $prom;
                }
                if ($obj_evaluador["tipo"] == 4) {
                    $array_final_grupos[$llave]["promedio"] += ($prom * $ponderacion_plana["cliente"]) / 100;
                    $array_final_grupos[$llave]["cantidad"]++;
                    $array_final_grupos[$llave]["sin_ponderacion"] += $prom;
                }
            }
        }
    }
    // print_r($array_final_grupos);
    //FINALMENTE SUMAMOS Y PROMEDIAMOS
    foreach ($array_final_grupos as $obj_resultado) {
        $promedio_total_comp += ($obj_resultado["promedio"] / $obj_resultado["cantidad"]);
        $conteo++;
    }

    $nodo = array(
        "general" => $promedio_total_comp,
        "evaluadores" => $array_final_grupos
    );

    return $nodo;
}





















//FUNCTION OBTENER CREENCIAS VALORES
//FUNCTION OBTENER CREENCIAS VALORES
//FUNCTION OBTENER CREENCIAS VALORES
function ObtenerCreenciasValores($COMPETENCIAS, $id_creencia, $rangos){
    global $EVALUACIONES;
    global $connect_valoracion;

    $total_cree = 0;
    $cantidad_cree = 0;
    foreach ($COMPETENCIAS as $competencia) {
        $sentecia_string = "
			SELECT Competencias.id_tipo AS id_tipo FROM Competencias_Niveles 
			LEFT JOIN Competencias ON Competencias.id = Competencias_Niveles.id_competencia
			WHERE Competencias_Niveles.id = '" . $competencia . "'
			";
        $queryCompTemp = mysqli_query($connect_valoracion, $sentecia_string);
        $dataCompTemp = mysqli_fetch_array($queryCompTemp);
        if ($dataCompTemp["id_tipo"] == $id_creencia) {
            $nodo_comp = ObtenerCompetenciasConsolidadas($competencia, $COMPETENCIAS, $EVALUACIONES);
            $total_cree += $nodo_comp["general"];
            $cantidad_cree++;
        }
    }

    $promedio_compt = $total_cree / $cantidad_cree;
    $porcentaje_comt = ($promedio_compt * 100) / 4;

    $rng_1 = explode("-", $rangos["rango_1"]);
    $rng_2 = explode("-", $rangos["rango_2"]);
    $rng_3 = explode("-", $rangos["rango_3"]);
    $rng_4 = explode("-", $rangos["rango_4"]);

    $color_compt = '';
    if ($porcentaje_comt >= $rng_1[0] && $porcentaje_comt < $rng_2[1]) {
        $color_compt = "#FF0000";
    }
    if ($porcentaje_comt >= $rng_2[0] && $porcentaje_comt < $rng_3[1]) {
        $color_compt = "#FFF200";
    }
    if ($porcentaje_comt >= $rng_3[0] && $porcentaje_comt < $rng_4[1]) {
        $color_compt = "#95FA03";
    }
    if ($porcentaje_comt >= $rng_4[0]) {
        $color_compt = "#14F209";
    }

    $nodo = array(
        "promedio" => $promedio_compt,
        "color" => $color_compt,
        "porcentaje" => $porcentaje_comt
    );
    return $nodo;
}

//PARA RETORNAR EL COLOR
function RetornarColor($valor, $rangos){

    $rng_1 = explode("-", $rangos["rango_1"]);
    $rng_2 = explode("-", $rangos["rango_2"]);
    $rng_3 = explode("-", $rangos["rango_3"]);
    $rng_4 = explode("-", $rangos["rango_4"]);

    $color_competencia = '';
    if ($valor >= $rng_1[0] && $valor < $rng_2[1]) {
        $color_competencia = "#FF0000";
    }
    if ($valor >= $rng_2[0] && $valor < $rng_3[1]) {
        $color_competencia = "#FFF200";
    }
    if ($valor >= $rng_3[0] && $valor < $rng_4[1]) {
        $color_competencia = "#95FA03";
    }
    if ($valor >= $rng_4[0]) {
        $color_competencia = "#14F209";
    }
    return $color_competencia;
}


//PARA OBTENER EL PROMEDIO GENERAL DEL EVALUADO PROMEDIANDO LOS EVALUADORES Y LA PONDERACIÓN.
function PromedioGeneralEvaluado($id_empleado, $connect_valoracion, $connect_admin){

    $permitir = true;
    $no_evaluaciones = 0;
    $array_tipos = array();
    $id_cargo = 0;


    //CONSULTAMOS LOS EVALUADORES DE ESTE EMPLEADO
    $queryEvaluadores = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores 
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND id_empleado = '" . $id_empleado . "' ");
    while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) {

        $queryEval = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE 
        id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND
        id_evaluado = '" . $id_empleado . "' AND  id_evaluador = '" . $dataEvaluadores['id_evaluador'] . "' AND 
        id_ciclo = '" . $_SESSION['ciclo'] . "' AND tipo_evaluacion = '" . $dataEvaluadores["tipo"] . "' AND estado > 1 AND promedio > 0 ");
        $dataEval = mysqli_fetch_array($queryEval);
        if ($queryEval->num_rows > 0) {
            $id_cargo = $dataEval["id_cargo"];
            $proceso_valoracion = $dataEval["proceso_valoracion"];
            $estado = $dataEval["estado"];
        }

        array_push(
            $array_tipos,
            array(
                "tipo" => $dataEval["tipo_evaluacion"],
                "promedio" => $dataEval["promedio"]
            )
        );

        if ($queryEval->num_rows == 0) {
            $permitir = false;
        }
        if ($queryEval->num_rows > 0) {
            $no_evaluaciones++;
        }
    }

    //VARIABLES PARA AGRUPAR A LOS EVALUADORES
    $auto = 0;
    $auto_cant = 0;
    $par = 0;
    $par_cant = 0;
    $colaborador = 0;
    $colaborador_cant = 0;
    $cliente = 0;
    $cliente_cant = 0;
    $jefe = 0;
    $jefe_cant = 0;

    //RECORREMOS LOS PROMEDIOS DE LOS EVALUADORES
    foreach ($array_tipos as $tipo) {
        if ($tipo["tipo"] == 1) { //AUTO
            $auto += $tipo["promedio"];
            $auto_cant++;
        }
         if ($tipo["tipo"] == 2) { //PAR
             $par += $tipo["promedio"];
             $par_cant++;
        }
        if ($tipo["tipo"] == 3) { //COLABORADOR
             $colaborador += $tipo["promedio"];
             $colaborador_cant++;
        }
        if ($tipo["tipo"] == 4) { //CLIENTE
            $cliente += $tipo["promedio"];
             $cliente_cant++;
        }
        if ($tipo["tipo"] == 5) { //JEFE
            $jefe += $tipo["promedio"];
            $jefe_cant++;
        }
    }

    //PROMEDIO POR TIPO EVALUADOR
    $promedio_auto = $auto / $auto_cant;
    $promedio_par = $par / $par_cant;
    $promedio_colaborador = $colaborador / $colaborador_cant;
    $promedio_cliente = $cliente / $cliente_cant;
    $promedio_jefe = $jefe / $jefe_cant;

    if (is_nan($promedio_auto)) {
        $promedio_auto = 0;
    }
    if (is_nan($promedio_par)) {
        $promedio_par = 0;
    }
    if (is_nan($promedio_colaborador)) {
        $promedio_colaborador = 0;
    }
    if (is_nan($promedio_cliente)) {
        $promedio_cliente = 0;
    }
    if (is_nan($promedio_jefe)) {
        $promedio_jefe = 0;
    }

    //AQUI PREPARAMOS LA SENTENCIA PARA VALIDAR EL TIPO DE EVALUACION
    //AQUI PREPARAMOS LA SENTENCIA PARA VALIDAR EL TIPO DE EVALUACION
    //AQUI PREPARAMOS LA SENTENCIA PARA VALIDAR EL TIPO DE EVALUACION
    $fill = "";
    // echo "promedio_auto = ".$promedio_auto;
    if ($promedio_auto >= 0) {
        $fill .= " AND auto != '' ";
        // $fill .= " AND auto = '' ";
    } else {
        $fill .= " AND auto = '' ";
    }

    if ($promedio_par > 0) {
        $fill .= " AND par != '' ";
    } else {
        $fill .= " AND par = '' ";
    }

    if ($promedio_colaborador > 0) {
        $fill .= " AND subalterno != '' ";
    } else {
        $fill .= " AND subalterno = '' ";
    }

    if ($promedio_cliente > 0) {
        $fill .= " AND cliente != '' ";
    } else {
        $fill .= " AND cliente = '' ";
    }

    if ($promedio_jefe > 0) {
        $fill .= " AND jefe != '' ";
    } else {
        $fill .= " AND jefe = '' ";
    }

    $queryValidate = mysqli_query($connect_valoracion, "SELECT * FROM Ponderar_Evaluaciones 
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "'  " . $fill . " ");
    $dataValidate = mysqli_fetch_array($queryValidate);

    $queryTipoPonderacion = mysqli_query($connect_valoracion, "SELECT * FROM Ponderar_Lista WHERE id = '" . $dataValidate["id_tipo_competencia"] . "' ");
    $dataTipoPonderacion = mysqli_fetch_array($queryTipoPonderacion);

    $autoP = ($promedio_auto * $dataValidate["auto"]) / 100;
    $jefeP = ($promedio_jefe * $dataValidate["jefe"]) / 100;
    $parP = ($promedio_par * $dataValidate["par"]) / 100;
    $colaP = ($promedio_colaborador * $dataValidate["subalterno"]) / 100;
    $clienteP = ($promedio_jefe * $dataValidate["cliente"]) / 100;

    $total_promedio = $autoP + $jefeP + $parP + $colaP + $clienteP;

    if($dataTipoPonderacion["nombre"] == '180'){
        $total_promedio = $promedio_jefe;
    }
    return array(
        "permitir" => $permitir,
        "tipo_ponderacion" => $dataTipoPonderacion["nombre"],
        "promedio" => $total_promedio,
        "datos_ponderar" => $dataValidate,
        "no_evaluadores" => $queryEvaluadores->num_rows,
        "no_evaluadores_evaluacion" => $no_evaluaciones,
        "id_cargo" => $id_cargo,
        "arreglos" => $array_tipos,
        "proceso_valoracion" => $proceso_valoracion,
        "estado" => $estado
    );
    
}


function PromedioGeneralEvaluadoPreguntas($id_empleado, $connect_valoracion, $connect_admin){

    $permitir = true;
    $no_evaluaciones = 0;
    $array_tipos = array();
    $id_cargo = 0;

    //CONSULTAMOS LOS EVALUADORES DE ESTE EMPLEADO
    $queryEvaluadores = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores 
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND id_empleado = '" . $id_empleado . "' ");
    while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) {

        $queryEval = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE 
        id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND
        id_evaluado = '" . $id_empleado . "' AND  id_evaluador = '" . $dataEvaluadores['id_evaluador'] . "' AND 
        id_ciclo = '" . $_SESSION['ciclo'] . "' AND tipo_evaluacion = '" . $dataEvaluadores["tipo"] . "'");
        $dataEval = mysqli_fetch_array($queryEval);
        if ($queryEval->num_rows > 0) {
            $id_cargo = $dataEval["id_cargo"];
            $proceso_valoracion = $dataEval["proceso_valoracion"];
            $estado = $dataEval["estado"];
        }

        array_push(
            $array_tipos,
            array(
                "tipo" => $dataEval["tipo_evaluacion"],
                "promedio" => $dataEval["promedio"]
            )
        );

        if ($queryEval->num_rows == 0) {
            $permitir = false;
        }
        if ($queryEval->num_rows > 0) {
            $no_evaluaciones++;
        }
    }

    //VARIABLES PARA AGRUPAR A LOS EVALUADORES
    $auto = 0;
    $auto_cant = 0;
    $par = 0;
    $par_cant = 0;
    $colaborador = 0;
    $colaborador_cant = 0;
    $cliente = 0;
    $cliente_cant = 0;
    $jefe = 0;
    $jefe_cant = 0;

    //RECORREMOS LOS PROMEDIOS DE LOS EVALUADORES
    foreach ($array_tipos as $tipo) {
        if ($tipo["tipo"] == 1) { //AUTO
            $auto += $tipo["promedio"];
            $auto_cant++;
        }
        // if ($tipo["tipo"] == 2) { //PAR
        //     $par += $tipo["promedio"];
        //     $par_cant++;
        // }
        // if ($tipo["tipo"] == 3) { //COLABORADOR
        //     $colaborador += $tipo["promedio"];
        //     $colaborador_cant++;
        // }
        // if ($tipo["tipo"] == 4) { //CLIENTE
        //     $cliente += $tipo["promedio"];
        //     $cliente_cant++;
        // }
        if ($tipo["tipo"] == 5) { //JEFE
            $jefe += $tipo["promedio"];
            $jefe_cant++;
        }
    }

    //PROMEDIO POR TIPO EVALUADOR
    $promedio_auto = $auto / $auto_cant;
    $promedio_par = $par / $par_cant;
    $promedio_colaborador = $colaborador / $colaborador_cant;
    $promedio_cliente = $cliente / $cliente_cant;
    $promedio_jefe = $jefe / $jefe_cant;

    if (is_nan($promedio_auto)) {
        $promedio_auto = 0;
    }
    if (is_nan($promedio_par)) {
        $promedio_par = 0;
    }
    if (is_nan($promedio_colaborador)) {
        $promedio_colaborador = 0;
    }
    if (is_nan($promedio_cliente)) {
        $promedio_cliente = 0;
    }
    if (is_nan($promedio_jefe)) {
        $promedio_jefe = 0;
    }

    //AQUI PREPARAMOS LA SENTENCIA PARA VALIDAR EL TIPO DE EVALUACION
    //AQUI PREPARAMOS LA SENTENCIA PARA VALIDAR EL TIPO DE EVALUACION
    //AQUI PREPARAMOS LA SENTENCIA PARA VALIDAR EL TIPO DE EVALUACION
    $fill = "";
    // echo "promedio_auto = ".$promedio_auto;
    if ($promedio_auto >= 0) {
        
        $fill .= " AND auto = '' ";
    } else {
        $fill .= " AND auto = '' ";
    }

    if ($promedio_par > 0) {
        $fill .= " AND par != '' ";
    } else {
        $fill .= " AND par = '' ";
    }

    if ($promedio_colaborador > 0) {
        $fill .= " AND subalterno != '' ";
    } else {
        $fill .= " AND subalterno = '' ";
    }

    if ($promedio_cliente > 0) {
        $fill .= " AND cliente != '' ";
    } else {
        $fill .= " AND cliente = '' ";
    }

    if ($promedio_jefe > 0) {
        $fill .= " AND jefe != '' ";
    } else {
        $fill .= " AND jefe = '' ";
    }

    $queryValidate = mysqli_query($connect_valoracion, "SELECT * FROM Ponderar_Evaluaciones 
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "'  " . $fill . " ");
    $dataValidate = mysqli_fetch_array($queryValidate);

    $queryTipoPonderacion = mysqli_query($connect_valoracion, "SELECT * FROM Ponderar_Lista WHERE id = '" . $dataValidate["id_tipo_competencia"] . "' ");
    $dataTipoPonderacion = mysqli_fetch_array($queryTipoPonderacion);

    $autoP = ($promedio_auto * $dataValidate["auto"]) / 100;
    $jefeP = ($promedio_jefe * $dataValidate["jefe"]) / 100;
    $parP = ($promedio_par * $dataValidate["par"]) / 100;
    $colaP = ($promedio_colaborador * $dataValidate["subalterno"]) / 100;
    $clienteP = ($promedio_jefe * $dataValidate["cliente"]) / 100;

    $total_promedio = $autoP + $jefeP + $parP + $colaP + $clienteP;
    // print_r($dataValidate);
    return array(
        "permitir" => $permitir,
        "tipo_ponderacion" => $dataTipoPonderacion["nombre"],
        "promedio" => $total_promedio,
        "datos_ponderar" => $dataValidate,
        "no_evaluadores" => $queryEvaluadores->num_rows,
        "no_evaluadores_evaluacion" => $no_evaluaciones,
        "id_cargo" => $id_cargo,
        "arreglos" => $array_tipos,
        "proceso_valoracion" => $proceso_valoracion,
        "estado" => $estado
    );
}



//////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////
//PARA OBTENER EL PROMEDIO GENERAL DEL EVALUADO PROMEDIANDO LOS EVALUADORES Y LA PONDERACIÓN.
function PromedioGeneralEvaluadoMixto($id_empleado, $id_empleado2, $connect_valoracion, $connect_admin){

    $permitir = true;
    $no_evaluaciones = 0;
    $array_tipos = array();
    $id_cargo = 0;

    //CONSULTAMOS LOS EVALUADORES DE ESTE EMPLEADO
    $queryEvaluadores = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores 
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND ( id_empleado = '" . $id_empleado . "' OR id_empleado = '" . $id_empleado2 . "' ) ");
    while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) {

        $queryEval = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE 
        id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND
        ( id_evaluado = '" . $id_empleado . "' OR id_evaluado = '" . $id_empleado2 . "'  ) AND  id_evaluador = '" . $dataEvaluadores['id_evaluador'] . "' AND 
        id_ciclo = '" . $_SESSION['ciclo'] . "' AND tipo_evaluacion = '" . $dataEvaluadores["tipo"] . "' AND 
        estado = 2 ");
        $dataEval = mysqli_fetch_array($queryEval);
        if ($queryEval->num_rows > 0) {
            $id_cargo = $dataEval["id_cargo"];
        }

        array_push(
            $array_tipos,
            array(
                "tipo" => $dataEval["tipo_evaluacion"],
                "promedio" => $dataEval["promedio"]
            )
        );

        if ($queryEval->num_rows == 0) {
            $permitir = false;
        }
        if ($queryEval->num_rows > 0) {
            $no_evaluaciones++;
        }
    }

    //VARIABLES PARA AGRUPAR A LOS EVALUADORES
    $auto = 0;
    $auto_cant = 0;
    $par = 0;
    $par_cant = 0;
    $colaborador = 0;
    $colaborador_cant = 0;
    $cliente = 0;
    $cliente_cant = 0;
    $jefe = 0;
    $jefe_cant = 0;

    //RECORREMOS LOS PROMEDIOS DE LOS EVALUADORES
    foreach ($array_tipos as $tipo) {
        if ($tipo["tipo"] == 1) { //AUTO
            $auto += $tipo["promedio"];
            $auto_cant++;
        }
        if ($tipo["tipo"] == 2) { //PAR
            $par += $tipo["promedio"];
            $par_cant++;
        }
        if ($tipo["tipo"] == 3) { //COLABORADOR
            $colaborador += $tipo["promedio"];
            $colaborador_cant++;
        }
        if ($tipo["tipo"] == 4) { //CLIENTE
            $cliente += $tipo["promedio"];
            $cliente_cant++;
        }
        if ($tipo["tipo"] == 5) { //JEFE
            $jefe += $tipo["promedio"];
            $jefe_cant++;
        }
    }

    //PROMEDIO POR TIPO EVALUADOR
    $promedio_auto = $auto / $auto_cant;
    $promedio_par = $par / $par_cant;
    $promedio_colaborador = $colaborador / $colaborador_cant;
    $promedio_cliente = $cliente / $cliente_cant;
    $promedio_jefe = $jefe / $jefe_cant;

    if (is_nan($promedio_auto)) {
        $promedio_auto = 0;
    }
    if (is_nan($promedio_par)) {
        $promedio_par = 0;
    }
    if (is_nan($promedio_colaborador)) {
        $promedio_colaborador = 0;
    }
    if (is_nan($promedio_cliente)) {
        $promedio_cliente = 0;
    }
    if (is_nan($promedio_jefe)) {
        $promedio_jefe = 0;
    }

    //AQUI PREPARAMOS LA SENTENCIA PARA VALIDAR EL TIPO DE EVALUACION
    //AQUI PREPARAMOS LA SENTENCIA PARA VALIDAR EL TIPO DE EVALUACION
    //AQUI PREPARAMOS LA SENTENCIA PARA VALIDAR EL TIPO DE EVALUACION
    $fill = "";

    if ($promedio_auto > 0) {
        $fill .= " AND auto != '' ";
    } else {
        $fill .= " AND auto = '' ";
    }

    if ($promedio_par > 0) {
        $fill .= " AND par != '' ";
    } else {
        $fill .= " AND par = '' ";
    }

    if ($promedio_colaborador > 0) {
        $fill .= " AND subalterno != '' ";
    } else {
        $fill .= " AND subalterno = '' ";
    }

    if ($promedio_cliente > 0) {
        $fill .= " AND cliente != '' ";
    } else {
        $fill .= " AND cliente = '' ";
    }

    if ($promedio_jefe > 0) {
        $fill .= " AND jefe != '' ";
    } else {
        $fill .= " AND jefe = '' ";
    }

    $queryValidate = mysqli_query($connect_valoracion, "SELECT * FROM Ponderar_Evaluaciones 
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "'  " . $fill . " ");
    $dataValidate = mysqli_fetch_array($queryValidate);

    $queryTipoPonderacion = mysqli_query($connect_valoracion, "SELECT * FROM Ponderar_Lista WHERE id = '" . $dataValidate["id_tipo_competencia"] . "' ");
    $dataTipoPonderacion = mysqli_fetch_array($queryTipoPonderacion);

    $autoP = ($promedio_auto * $dataValidate["auto"]) / 100;
    $jefeP = ($promedio_jefe * $dataValidate["jefe"]) / 100;
    $parP = ($promedio_par * $dataValidate["par"]) / 100;
    $colaP = ($promedio_colaborador * $dataValidate["subalterno"]) / 100;
    $clienteP = ($promedio_jefe * $dataValidate["cliente"]) / 100;

    $total_promedio = $autoP + $jefeP + $parP + $colaP + $clienteP;

    return array(
        "permitir" => $permitir,
        "tipo_ponderacion" => $dataTipoPonderacion["nombre"],
        "promedio" => $total_promedio,
        "datos_ponderar" => $dataValidate,
        "no_evaluadores" => $queryEvaluadores->num_rows,
        "no_evaluadores_evaluacion" => $no_evaluaciones,
        "id_cargo" => $id_cargo,
        "arreglos" => $array_tipos
    );
}

function ValidarEvaluacionesCompletas($id_empleado, $connect_valoracion, $connect_admin)
{

    //1. CARGAMOS LO PROMEDIOS DE LA EVALUACIONES DE LOS EVALUADORES
    //1. CARGAMOS LO PROMEDIOS DE LA EVALUACIONES DE LOS EVALUADORES
    $promedios_evaluaciones = array();
    $no_evaluadores = 0;
    $permitir = true;

    $queryEvaluadores = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores 
    WHERE id_ciclo = '" . $_SESSION['ciclo'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND id_empleado = '" . $id_empleado . "' ");
    while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) {

        $queryEvalDor = mysqli_query($connect_admin, "SELECT nombre, apellidos FROM Empleados 
        WHERE id = '" . $dataEvaluadores['id_evaluador'] . "' ");
        $dataEvalDor = mysqli_fetch_array($queryEvalDor);

        $no_evaluadores++;

        $queryEval = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE 
        anio = '" . $_SESSION['anio_ciclo'] . "' AND
        id_evaluado = '" . $id_empleado . "' AND  
        id_evaluador = '" . $dataEvaluadores['id_evaluador'] . "' AND 
        id_ciclo = '" . $_SESSION['ciclo'] . "' AND 
        tipo_evaluacion = '" . $dataEvaluadores["tipo"] . "' AND 
        estado >= 2  ");
        $dataEval = mysqli_fetch_array($queryEval);

        array_push(
            $promedios_evaluaciones,
            array(
                "id" => $dataEval["id"],
                "tipo" => $dataEval["tipo_evaluacion"],
                "nombre" => ($dataEvalDor["nombre"] . " " . $dataEvalDor["apellidos"]),
                "promedio" => $dataEval["promedio"]
            )
        );
    }

    //2. SIMPLIFICAMOS LAS EVALUACIONES EN UN NUEVO ARRAY
    //2. SIMPLIFICAMOS LAS EVALUACIONES EN UN NUEVO ARRAY

    //VARIABLES PARA AGRUPAR A LOS EVALUADORES
    $auto = 0;
    $auto_cant = 0;
    $par = 0;
    $par_cant = 0;
    $colaborador = 0;
    $colaborador_cant = 0;
    $cliente = 0;
    $cliente_cant = 0;
    $jefe = 0;
    $jefe_cant = 0;

    //RECORREMOS LOS PROMEDIOS DE LOS EVALUADORES
    foreach ($promedios_evaluaciones as $grupo) {
        if ($grupo["tipo"] == 1) { //AUTO
            $auto += $grupo["promedio"];
            $auto_cant++;
        }
        if ($grupo["tipo"] == 2) { //PAR
            $par += $grupo["promedio"];
            $par_cant++;
        }
        if ($grupo["tipo"] == 3) { //COLABORADOR
            $colaborador += $grupo["promedio"];
            $colaborador_cant++;
        }
        if ($grupo["tipo"] == 4) { //CLIENTE
            $cliente += $grupo["promedio"];
            $cliente_cant++;
        }
        if ($grupo["tipo"] == 5) { //JEFE
            $jefe += $grupo["promedio"];
            $jefe_cant++;
        }
    }

    //PROMEDIO POR TIPO EVALUADOR
    $promedio_auto = $auto / $auto_cant;
    $promedio_par = $par / $par_cant;
    $promedio_colaborador = $colaborador / $colaborador_cant;
    $promedio_cliente = $cliente / $cliente_cant;
    $promedio_jefe = $jefe / $jefe_cant;

    if (is_nan($promedio_auto)) {
        $promedio_auto = 0;
    }
    if (is_nan($promedio_par)) {
        $promedio_par = 0;
    }
    if (is_nan($promedio_colaborador)) {
        $promedio_colaborador = 0;
    }
    if (is_nan($promedio_cliente)) {
        $promedio_cliente = 0;
    }
    if (is_nan($promedio_jefe)) {
        $promedio_jefe = 0;
    }

    //PROMEDIOS FINALES
    $promedios_evaluador_general = array(
        "auto" => $promedio_auto,
        "par" => $promedio_par,
        "colaborador" => $promedio_colaborador,
        "cliente" => $promedio_cliente,
        "jefe" => $promedio_jefe,
        "comentarios_competencias" => "",
        "comentarios_evaluaciones" => ""
    );

    $general_sin_ponderar = $promedio_auto + $promedio_par + $promedio_colaborador + $promedio_cliente + $promedio_jefe;

    // EN CASO DE NO TENER EVALUADORES
    if ($no_evaluadores == 0) {
        $permitir = false;
    }

    $PROMEDIO_GENERAL = PonderarEvaluaciones($connect_valoracion, $promedios_evaluador_general);

    return array(
        "permitir" => $permitir,
        "no_evaluadores" => $no_evaluadores,
        "promedio_general" => $PROMEDIO_GENERAL,
        "dato_evaluadores" => $promedios_evaluaciones
    );
}

function ValidarEvaluacionesCompletasReporte($id_empleado, $anio_ciclo, $ciclo, $connect_valoracion, $connect_admin)
{

    //1. CARGAMOS LO PROMEDIOS DE LA EVALUACIONES DE LOS EVALUADORES
    //1. CARGAMOS LO PROMEDIOS DE LA EVALUACIONES DE LOS EVALUADORES
    $promedios_evaluaciones = array();
    $no_evaluadores = 0;
    $permitir = true;

    $queryEvaluadores = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores 
    WHERE id_ciclo = '" . $ciclo . "' AND anio = '" . $anio_ciclo . "' AND id_empleado = '" . $id_empleado . "' ");
    while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) {

        $queryEvalDor = mysqli_query($connect_admin, "SELECT nombre, apellidos FROM Empleados 
        WHERE id = '" . $dataEvaluadores['id_evaluador'] . "' ");
        $dataEvalDor = mysqli_fetch_array($queryEvalDor);

        $no_evaluadores++;

        $queryEval = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE 
        anio = '" . $anio_ciclo . "' AND
        id_evaluado = '" . $id_empleado . "' AND  
        id_evaluador = '" . $dataEvaluadores['id_evaluador'] . "' AND 
        id_ciclo = '" . $ciclo . "' AND 
        tipo_evaluacion = '" . $dataEvaluadores["tipo"] . "' AND 
        estado >= 2  ");
        $dataEval = mysqli_fetch_array($queryEval);

        array_push(
            $promedios_evaluaciones,
            array(
                "id" => $dataEval["id"],
                "tipo" => $dataEval["tipo_evaluacion"],
                "nombre" => ($dataEvalDor["nombre"] . " " . $dataEvalDor["apellidos"]),
                "promedio" => $dataEval["promedio"]
            )
        );
    }

    //2. SIMPLIFICAMOS LAS EVALUACIONES EN UN NUEVO ARRAY
    //2. SIMPLIFICAMOS LAS EVALUACIONES EN UN NUEVO ARRAY

    //VARIABLES PARA AGRUPAR A LOS EVALUADORES
    $auto = 0;
    $auto_cant = 0;
    $par = 0;
    $par_cant = 0;
    $colaborador = 0;
    $colaborador_cant = 0;
    $cliente = 0;
    $cliente_cant = 0;
    $jefe = 0;
    $jefe_cant = 0;

    //RECORREMOS LOS PROMEDIOS DE LOS EVALUADORES
    foreach ($promedios_evaluaciones as $grupo) {
        if ($grupo["tipo"] == 1) { //AUTO
            $auto += $grupo["promedio"];
            $auto_cant++;
        }
        if ($grupo["tipo"] == 2) { //PAR
            $par += $grupo["promedio"];
            $par_cant++;
        }
        if ($grupo["tipo"] == 3) { //COLABORADOR
            $colaborador += $grupo["promedio"];
            $colaborador_cant++;
        }
        if ($grupo["tipo"] == 4) { //CLIENTE
            $cliente += $grupo["promedio"];
            $cliente_cant++;
        }
        if ($grupo["tipo"] == 5) { //JEFE
            $jefe += $grupo["promedio"];
            $jefe_cant++;
        }
    }

    //PROMEDIO POR TIPO EVALUADOR
    $promedio_auto = $auto / $auto_cant;
    $promedio_par = $par / $par_cant;
    $promedio_colaborador = $colaborador / $colaborador_cant;
    $promedio_cliente = $cliente / $cliente_cant;
    $promedio_jefe = $jefe / $jefe_cant;

    if (is_nan($promedio_auto)) {
        $promedio_auto = 0;
    }
    if (is_nan($promedio_par)) {
        $promedio_par = 0;
    }
    if (is_nan($promedio_colaborador)) {
        $promedio_colaborador = 0;
    }
    if (is_nan($promedio_cliente)) {
        $promedio_cliente = 0;
    }
    if (is_nan($promedio_jefe)) {
        $promedio_jefe = 0;
    }

    //PROMEDIOS FINALES
    $promedios_evaluador_general = array(
        "auto" => $promedio_auto,
        "par" => $promedio_par,
        "colaborador" => $promedio_colaborador,
        "cliente" => $promedio_cliente,
        "jefe" => $promedio_jefe,
        "comentarios_competencias" => "",
        "comentarios_evaluaciones" => ""
    );

    $general_sin_ponderar = $promedio_auto + $promedio_par + $promedio_colaborador + $promedio_cliente + $promedio_jefe;

    // EN CASO DE NO TENER EVALUADORES
    if ($no_evaluadores == 0) {
        $permitir = false;
    }

    $PROMEDIO_GENERAL = PonderarEvaluaciones($connect_valoracion, $promedios_evaluador_general);

    return array(
        "permitir" => $permitir,
        "no_evaluadores" => $no_evaluadores,
        "promedio_general" => $PROMEDIO_GENERAL,
        "dato_evaluadores" => $promedios_evaluaciones
    );
}

///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
function ValidarEvaluacionesCompletasMixtas($id_empleado, $id_empleado2, $connect_valoracion, $connect_admin)
{

    //1. CARGAMOS LO PROMEDIOS DE LA EVALUACIONES DE LOS EVALUADORES
    //1. CARGAMOS LO PROMEDIOS DE LA EVALUACIONES DE LOS EVALUADORES
    $promedios_evaluaciones = array();
    $no_evaluadores = 0;
    $permitir = true;

    $queryEvaluadores = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores 
    WHERE id_ciclo = '" . $_SESSION['ciclo'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND (id_empleado = '" . $id_empleado . "' OR id_empleado = '" . $id_empleado2 . "'  ) ");
    while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) {

        $queryEvalDor = mysqli_query($connect_admin, "SELECT nombre, apellidos FROM Empleados 
        WHERE id = '" . $dataEvaluadores['id_evaluador'] . "' ");
        $dataEvalDor = mysqli_fetch_array($queryEvalDor);

        $no_evaluadores++;

        $queryEval = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE 
        anio = '" . $_SESSION['anio_ciclo'] . "' AND
        ( id_evaluado = '" . $id_empleado . "' OR id_evaluado = '" . $id_empleado2 . "' ) 
		
		AND  
        id_evaluador = '" . $dataEvaluadores['id_evaluador'] . "' AND 
        id_ciclo = '" . $_SESSION['ciclo'] . "' AND 
        tipo_evaluacion = '" . $dataEvaluadores["tipo"] . "' AND 
        estado = 2  ");
        $dataEval = mysqli_fetch_array($queryEval);

        array_push(
            $promedios_evaluaciones,
            array(
                "id" => $dataEval["id"],
                "tipo" => $dataEval["tipo_evaluacion"],
                "nombre" => ($dataEvalDor["nombre"] . " " . $dataEvalDor["apellidos"]),
                "promedio" => $dataEval["promedio"]
            )
        );
    }

    //2. SIMPLIFICAMOS LAS EVALUACIONES EN UN NUEVO ARRAY
    //2. SIMPLIFICAMOS LAS EVALUACIONES EN UN NUEVO ARRAY

    //VARIABLES PARA AGRUPAR A LOS EVALUADORES
    $auto = 0;
    $auto_cant = 0;
    $par = 0;
    $par_cant = 0;
    $colaborador = 0;
    $colaborador_cant = 0;
    $cliente = 0;
    $cliente_cant = 0;
    $jefe = 0;
    $jefe_cant = 0;

    //RECORREMOS LOS PROMEDIOS DE LOS EVALUADORES
    foreach ($promedios_evaluaciones as $grupo) {
        if ($grupo["tipo"] == 1) { //AUTO
            $auto += $grupo["promedio"];
            $auto_cant++;
        }
        if ($grupo["tipo"] == 2) { //PAR
            $par += $grupo["promedio"];
            $par_cant++;
        }
        if ($grupo["tipo"] == 3) { //COLABORADOR
            $colaborador += $grupo["promedio"];
            $colaborador_cant++;
        }
        if ($grupo["tipo"] == 4) { //CLIENTE
            $cliente += $grupo["promedio"];
            $cliente_cant++;
        }
        if ($grupo["tipo"] == 5) { //JEFE
            $jefe += $grupo["promedio"];
            $jefe_cant++;
        }
    }

    //PROMEDIO POR TIPO EVALUADOR
    $promedio_auto = $auto / $auto_cant;
    $promedio_par = $par / $par_cant;
    $promedio_colaborador = $colaborador / $colaborador_cant;
    $promedio_cliente = $cliente / $cliente_cant;
    $promedio_jefe = $jefe / $jefe_cant;

    if (is_nan($promedio_auto)) {
        $promedio_auto = 0;
    }
    if (is_nan($promedio_par)) {
        $promedio_par = 0;
    }
    if (is_nan($promedio_colaborador)) {
        $promedio_colaborador = 0;
    }
    if (is_nan($promedio_cliente)) {
        $promedio_cliente = 0;
    }
    if (is_nan($promedio_jefe)) {
        $promedio_jefe = 0;
    }

    //PROMEDIOS FINALES
    $promedios_evaluador_general = array(
        "auto" => $promedio_auto,
        "par" => $promedio_par,
        "colaborador" => $promedio_colaborador,
        "cliente" => $promedio_cliente,
        "jefe" => $promedio_jefe,
        "comentarios_competencias" => "",
        "comentarios_evaluaciones" => ""
    );

    $general_sin_ponderar = $promedio_auto + $promedio_par + $promedio_colaborador + $promedio_cliente + $promedio_jefe;

    // EN CASO DE NO TENER EVALUADORES
    if ($no_evaluadores == 0) {
        $permitir = false;
    }

    $PROMEDIO_GENERAL = PonderarEvaluaciones($connect_valoracion, $promedios_evaluador_general);


    return array(
        "permitir" => $permitir,
        "no_evaluadores" => $no_evaluadores,
        "promedio_general" => $PROMEDIO_GENERAL,
        "dato_evaluadores" => $promedios_evaluaciones
    );
}

function PonderarEvaluaciones($connect_valoracion, $promedios)
{

    //CONSULTAMOS EL MODELO DE PONDERACION DE LA EMPRESA
    $fill = " AND id > 0 ";

    if ($promedios["auto"] >= 0) {
        $fill .= " AND auto != '' ";
    } else {
        $fill .= " AND auto = '' ";
    }

    if ($promedios["par"] > 0) {
        $fill .= " AND par != '' ";
    } else {
        $fill .= " AND par = '' ";
    }

    if ($promedios["colaborador"] > 0) {
        $fill .= " AND subalterno != '' ";
    } else {
        $fill .= " AND subalterno = '' ";
    }

    if ($promedios["cliente"] > 0) {
        $fill .= " AND cliente != '' ";
    } else {
        $fill .= " AND cliente = '' ";
    }

    if ($promedios["jefe"] >= 0) {
        $fill .= " AND jefe != '' ";
    } else {
        $fill .= " AND jefe = '' ";
    }

    $queryValidate = mysqli_query($connect_valoracion, "SELECT * FROM Ponderar_Evaluaciones 
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "'  " . $fill . " ");
    $dataValidate = mysqli_fetch_array($queryValidate);

    $queryTipo = mysqli_query($connect_valoracion, "SELECT * FROM Ponderar_Lista WHERE id = '" . $dataValidate["id_tipo_competencia"] . "' ");
    $dataTipo = mysqli_fetch_array($queryTipo);

    $autoP = ($promedios["auto"] * $dataValidate["auto"]) / 100;    
    $jefeP = ($promedios["jefe"] * $dataValidate["jefe"]) / 100;
    $parP = ($promedios["par"] * $dataValidate["par"]) / 100;
    $colaP = ($promedios["colaborador"] * $dataValidate["subalterno"]) / 100;
    $clienteP = ($promedios["cliente"] * $dataValidate["cliente"]) / 100;

    $total_promedio = $autoP + $jefeP + $parP + $colaP + $clienteP;

    return $total_promedio;
}

///
//PROMEDIO COMPETENCIAS
function PromedioCompetencias($id_empleado, $connect_valoracion, $connect_admin)
{

    $permitir = true;
    $no_evaluaciones = 0;
    $array_tipos = array();
    $id_cargo = 0;


    //NUEVO CODIGO PARA OBTENER TODOS LOS EVALUADORES
    //NUEVO CODIGO PARA OBTENER TODOS LOS EVALUADORES
    //NUEVO CODIGO PARA OBTENER TODOS LOS EVALUADORES
    $queryEval = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE 
    id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND
    id_evaluado = '" . $id_empleado . "' AND estado = 2 ");
    while ($dataEval = mysqli_fetch_array($queryEval)) {
        array_push(
            $array_tipos,
            array(
                "tipo" => $dataEval["tipo_evaluacion"],
                "promedio" => $dataEval["promedio"]
            )
        );

        if ($queryEval->num_rows == 0) {
            $permitir = false;
        }
        if ($queryEval->num_rows > 0) {
            $no_evaluaciones++;
        }
    }



    //VARIABLES PARA AGRUPAR A LOS EVALUADORES
    //VARIABLES PARA AGRUPAR A LOS EVALUADORES
    //VARIABLES PARA AGRUPAR A LOS EVALUADORES
    $auto = 0;
    $auto_cant = 0;
    $par = 0;
    $par_cant = 0;
    $colaborador = 0;
    $colaborador_cant = 0;
    $cliente = 0;
    $cliente_cant = 0;
    $jefe = 0;
    $jefe_cant = 0;

    //RECORREMOS LOS PROMEDIOS DE LOS EVALUADORES
    foreach ($array_tipos as $tipo) {
        if ($tipo["tipo"] == 1) { //AUTO
            $auto += $tipo["promedio"];
            $auto_cant++;
        }
        if ($tipo["tipo"] == 2) { //PAR
            $par += $tipo["promedio"];
            $par_cant++;
        }
        if ($tipo["tipo"] == 3) { //COLABORADOR
            $colaborador += $tipo["promedio"];
            $colaborador_cant++;
        }
        if ($tipo["tipo"] == 4) { //CLIENTE
            $cliente += $tipo["promedio"];
            $cliente_cant++;
        }
        if ($tipo["tipo"] == 5) { //JEFE
            $jefe += $tipo["promedio"];
            $jefe_cant++;
        }
    }

    //PROMEDIO POR TIPO EVALUADOR
    $promedio_auto = $auto / $auto_cant;
    $promedio_par = $par / $par_cant;
    $promedio_colaborador = $colaborador / $colaborador_cant;
    $promedio_cliente = $cliente / $cliente_cant;
    $promedio_jefe = $jefe / $jefe_cant;

    if (is_nan($promedio_auto)) {
        $promedio_auto = 0;
    }
    if (is_nan($promedio_par)) {
        $promedio_par = 0;
    }
    if (is_nan($promedio_colaborador)) {
        $promedio_colaborador = 0;
    }
    if (is_nan($promedio_cliente)) {
        $promedio_cliente = 0;
    }
    if (is_nan($promedio_jefe)) {
        $promedio_jefe = 0;
    }

    //AQUI PREPARAMOS LA SENTENCIA PARA VALIDAR EL TIPO DE EVALUACION
    //AQUI PREPARAMOS LA SENTENCIA PARA VALIDAR EL TIPO DE EVALUACION
    //AQUI PREPARAMOS LA SENTENCIA PARA VALIDAR EL TIPO DE EVALUACION
    $fill = "";

    if ($promedio_auto > 0) {
        $fill .= " AND auto != '' ";
    } else {
        $fill .= " AND auto = '' ";
    }

    if ($promedio_par > 0) {
        $fill .= " AND par != '' ";
    } else {
        $fill .= " AND par = '' ";
    }

    if ($promedio_colaborador > 0) {
        $fill .= " AND subalterno != '' ";
    } else {
        $fill .= " AND subalterno = '' ";
    }

    if ($promedio_cliente > 0) {
        $fill .= " AND cliente != '' ";
    } else {
        $fill .= " AND cliente = '' ";
    }

    if ($promedio_jefe > 0) {
        $fill .= " AND jefe != '' ";
    } else {
        $fill .= " AND jefe = '' ";
    }

    $queryValidate = mysqli_query($connect_valoracion, "SELECT * FROM Ponderar_Evaluaciones 
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "'  " . $fill . " ");
    $dataValidate = mysqli_fetch_array($queryValidate);

    $queryTipoPonderacion = mysqli_query($connect_valoracion, "SELECT * FROM Ponderar_Lista WHERE id = '" . $dataValidate["id_tipo_competencia"] . "' ");
    $dataTipoPonderacion = mysqli_fetch_array($queryTipoPonderacion);

    $autoP = ($promedio_auto * $dataValidate["auto"]) / 100;
    $jefeP = ($promedio_jefe * $dataValidate["jefe"]) / 100;
    $parP = ($promedio_par * $dataValidate["par"]) / 100;
    $colaP = ($promedio_colaborador * $dataValidate["subalterno"]) / 100;
    $clienteP = ($promedio_jefe * $dataValidate["cliente"]) / 100;

    $total_promedio = $autoP + $jefeP + $parP + $colaP + $clienteP;

    return array(
        "permitir" => $permitir,
        "tipo_ponderacion" => $dataTipoPonderacion["nombre"],
        "promedio" => (($total_promedio * 100) / 4),
        "no_evaluadores" => count($array_evaluadores_modelo),
        "no_evaluadores_evaluacion" => $no_evaluaciones,
        "id_cargo" => $id_cargo
    );
}

/*
function eliminar_tildes($archivo)
{

    $cadena = $archivo;
    $cadena = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Ã¡', 'Á', 'À', 'Â', 'Ä', 'Ã¡', 'Ã', 'Ã', 'ÃƒÁ'),
        array('á', 'á', 'á', 'á', 'á', 'á', 'Á', 'Á', 'Á', 'Á', 'Á', 'Á', 'Á', 'Á'),
        $cadena
    );

    $cadena = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë', 'Ã©', 'Ã‰'),
        array('e', 'e', 'e', 'e', 'É', 'É', 'É', 'É', 'é', 'É'),
        $cadena
    );

    $cadena = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î', 'Ã­', 'Ã'),
        array('i', 'i', 'i', 'i', 'Í', 'Í', 'Í', 'Í', 'í', 'Í'),
        $cadena
    );

    $cadena = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô', 'Ã³', 'Ã“', 'Ã“', 'oÍ', 'ÃƒÁ“'),
        array('ó', 'ó', 'ó', 'ó', 'Ó', 'Ó', 'Ó', 'Ó', 'ó', 'Ó', 'Ó', 'ó', 'Ó'),
        $cadena
    );

    $cadena = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü', 'Ãº', 'Ãš'),
        array('u', 'u', 'u', 'u', 'Ú', 'Ú', 'Ú', 'Ú', 'ú', 'Ú'),
        $cadena
    );

    $cadena = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç', 'Ã±', 'ÃƒÁ±', 'Ã‘'),
        array('n', 'Ñ', 'c', 'C', 'ñ', 'ñ', 'Ñ'),
        $cadena
    );
    return $cadena;
}

*/
?>