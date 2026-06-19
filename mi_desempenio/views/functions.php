<?php
function OkrsConsolidadoCompetencia($id_empleado, $id_empresa, $connect_okrs)
{

	$sentencia = "SELECT OE.id AS id, OE.id_empresa AS id_empresa,
				OE.id_empleado AS id_empleado, OE.id_okrs AS id_okrs,
				O.objetivo_okr AS objetivo_okr, O.fecha_inicia AS fecha_inicia, O.fecha_termina AS fecha_termina,
				O.tipo AS tipo, O.periodo AS periodo, O.objetivos_estrategicos AS objetivos_estrategicos,
				O.anio AS anio,	OE.tipo AS tipo_role, E.nombre AS nombre_empleado,
				O.id_empleado AS id_owner, EO.nombre AS nombre_owner
				FROM Equipos_Views EV
				LEFT JOIN Okrs_Equipos OE ON OE.id_okrs = EV.id_okrs
				LEFT JOIN Okrs O ON O.id = OE.id_okrs
				LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = OE.id_okrs
				LEFT JOIN Okrs_Areas AS OA ON OA.id_okrs = OE.id_okrs				
				LEFT JOIN goforagile_admin.Empleados AS E ON E.id = OE.id_empleado
				LEFT JOIN goforagile_admin.Empleados AS EO ON EO.id = O.id_empleado
				WHERE EV.id_empresa = $id_empresa
				AND OE.id_empleado = $id_empleado
				AND O.anio = ".$_SESSION["periodo_desempenio"]."
				GROUP BY O.id";

	// echo $sentencia;

	$nodos = array();
	$query1 = mysqli_query($connect_okrs, $sentencia);

	// echo mysqli_num_rows($query);

	while ($data = mysqli_fetch_array($query1)) {

		$permitir = false;
		if ($_SESSION["objestrategico_fill"] > 0) {

			$objetivos_tmp = explode(",", $data["objetivos_estrategicos"]);
			foreach ($objetivos_tmp as $objTmp) {
				if ($objTmp == $_SESSION["objestrategico_fill"]) {
					$permitir = true;
				}
			}
		} else {
			$permitir = true;
		}


		if ($permitir == true) {
			$obj = array(
				"id" => $data["id_okrs"],
				"objetivo" => $data["objetivo_okr"],
				"fecha_inicia" => $data["fecha_inicia"],
				"fecha_termina" => $data["fecha_termina"],
				"tipo" => $data["tipo"],
				"tipo_role" => $data["tipo_role"],
				"periodo" => $data["periodo"],
				"objetivos_estrategicos" => $data["objetivos_estrategicos"],
				"anio" => $data["anio"],
				"nombre_empleado" => $data["nombre_empleado"],
				"id_empleado" => $data["id_empleado"],
				"id_owner" => $data["id_owner"],
				"nombre_owner" => $data["nombre_owner"],
			);
			array_push($nodos, $obj);
		}
	}
	return $nodos;
}

function PorOkrsReporteUsuarioDesempenio($connect_okrs, $okr, $id_empleado, $filtro_claves)
{

	$filtro_kr = " ";
	if ($filtro_claves) {
		$filtro_kr .= $filtro_claves;
	}

	$suma_resultado = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;
	$porcentaje = 0;
	$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr $filtro_kr ");
	
	while ($dataResultados = mysqli_fetch_array($queryResultados)) {
		$porcentaje = ($dataResultados["avance"] * 100) / $dataResultados["meta"];

		if ($dataResultados["tendencia"] == 2) {
			$porcentaje = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
		}

		if (is_infinite($porcentaje) || is_nan($porcentaje)) {
			$porcentaje = 0;
		}

		if ($porcentaje > 100) {
			$porcentaje = 100;
		}

		$suma_resultado = $suma_resultado + $porcentaje;
		// echo $suma_resultado;
		$conteo_resultado++;
	}

	$resultado_prom_okr = ($suma_resultado / mysqli_num_rows($queryResultados));

	if (is_infinite($resultado_prom_okr) || is_nan($resultado_prom_okr)) {
		$resultado_prom_okr = 0;
	}

	$nodo = array(
		"promedio" => $resultado_prom_okr,
		"no_resultados" => $queryResultados->num_rows
	);
	// print_r($nodo);
	return $nodo;
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
    WHERE id_empresa = '" . $_SESSION["id_empresa_valentina"] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "'  " . $fill . " ");
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

function EscalaColor($porcentaje, $id_empresa, $connect_admin)
{
	$queryEscala = mysqli_query($connect_admin, "SELECT * FROM Escala_Medicion WHERE id_empresa = $id_empresa");
	$dataEscala = mysqli_fetch_array($queryEscala);

	$color_bg = "#FF0000";
	$color_text = "#000000";
	$escala = array();

	if ($porcentaje >= $dataEscala['porcentaje_uno'] && $porcentaje < $dataEscala['porcentaje_tres']) {
		$color_bg = "#FF0000";
		$txt_subtitulo = $dataEscala['subtitulo_uno'];
		$color_text = "#F7F7F7";
	}
	if ($porcentaje >= $dataEscala['porcentaje_tres'] && $porcentaje < $dataEscala['porcentaje_cinco']) {
		$color_bg = "#FFF200";
		$txt_subtitulo = $dataEscala['subtitulo_dos'];
	}
	if ($porcentaje >= $dataEscala['porcentaje_cinco'] && $porcentaje < $dataEscala['porcentaje_siete']) {
		$color_bg = "#95FA03";
		$txt_subtitulo = $dataEscala['subtitulo_tres'];
	}
	if ($porcentaje >= $dataEscala['porcentaje_siete'] && $porcentaje <= 100) {
		$color_bg = "#14F209";
		$txt_subtitulo = $dataEscala['subtitulo_cuatro'];
	}
	// if( $porcentaje == 100 ){
	// 	$color_bg = "#0DF205";
	// 	$txt_subtitulo = $dataEscala['subtitulo_cinco'];
	// }
	if ($porcentaje > 100) {
		$color_bg = "#00D30A";
		$txt_subtitulo = $dataEscala['subtitulo_cinco'];
	}

	$escala['color_bg'] = $color_bg;
	$escala['color_text'] = $color_text;
	$escala['txt_subtitulo'] = $txt_subtitulo;

	return $escala;
}

function metaMes($connect_kpis, $id, $mes)
{
    $valor = 0;

    $queryFrecuencia = mysqli_query($connect_kpis, "SELECT * FROM Frecuencia_Kpis 
    WHERE id_kpi = '" . $id . "' AND tipo = 1 ");
    $datatrim = mysqli_fetch_array($queryFrecuencia);
    if (mysqli_num_rows($queryFrecuencia) > 0) {
        if ($mes == 1) {
            $valor =  $datatrim["enero"];
        }
        if ($mes == 2) {
            $valor =  $datatrim["febrero"];
        }
        if ($mes == 3) {
            $valor =  $datatrim["marzo"];
        }
        if ($mes == 4) {
            $valor =  $datatrim["abril"];
        }
        if ($mes == 5) {
            $valor =  $datatrim["mayo"];
        }
        if ($mes == 6) {
            $valor =  $datatrim["junio"];
        }
        if ($mes == 7) {
            $valor =  $datatrim["julio"];
        }
        if ($mes == 8) {
            $valor =  $datatrim["agosto"];
        }
        if ($mes == 9) {
            $valor =  $datatrim["septiembre"];
        }
        if ($mes == 10) {
            $valor =  $datatrim["octubre"];
        }
        if ($mes == 11) {
            $valor =  $datatrim["noviembre"];
        }
        if ($mes == 12) {
            $valor =  $datatrim["diciembre"];
        }
    }
    return $valor;
}

function metaMesTiempo($connect_kpis, $id, $mes)
{
    $valor = 0;

    $queryFrecuencia = mysqli_query($connect_kpis, "SELECT * FROM Frecuencia_Kpis FKP
    INNER JOIN Kpis K ON K.id = FKP.id_kpi
    WHERE FKP.id_kpi = '" . $id . "' AND FKP.tipo = 1 AND K.unidad_medida = 4");
    $datatrim = mysqli_fetch_array($queryFrecuencia);
    if (mysqli_num_rows($queryFrecuencia) > 0) {
        if ($mes == 1) {
            $array =  explode(":", $datatrim["enero"]);
            $hora = $array[0];
            $minuto = $array[1];
            $segundo = $array[2];
        }
        if ($mes == 2) {
            $array =  explode(":", $datatrim["febrero"]);
            $hora = $array[0];
            $minuto = $array[1];
            $segundo = $array[2];
        }
        if ($mes == 3) {
            $array =  explode(":", $datatrim["marzo"]);
            $hora = $array[0];
            $minuto = $array[1];
            $segundo = $array[2];
        }
        if ($mes == 4) {
            $array =  explode(":", $datatrim["abril"]);
            $hora = $array[0];
            $minuto = $array[1];
            $segundo = $array[2];
        }
        if ($mes == 5) {
            $array = explode(":", $datatrim["mayo"]);
            $hora = $array[0];
            $minuto = $array[1];
            $segundo = $array[2];
        }
        if ($mes == 6) {
            $array = explode(":", $datatrim["junio"]);
            $hora = $array[0];
            $minuto = $array[1];
            $segundo = $array[2];
        }
        if ($mes == 7) {
            $array = explode(":", $datatrim["julio"]);
            $hora = $array[0];
            $minuto = $array[1];
            $segundo = $array[2];
        }
        if ($mes == 8) {
            $array = explode(":", $datatrim["agosto"]);
            $hora = $array[0];
            $minuto = $array[1];
            $segundo = $array[2];
        }
        if ($mes == 9) {
            $array = explode(":", $datatrim["septiembre"]);
            $hora = $array[0];
            $minuto = $array[1];
            $segundo = $array[2];
        }
        if ($mes == 10) {
            $array = explode(":", $datatrim["octubre"]);
            $hora = $array[0];
            $minuto = $array[1];
            $segundo = $array[2];
        }
        if ($mes == 11) {
            $array = explode(":", $datatrim["noviembre"]);
            $hora = $array[0];
            $minuto = $array[1];
            $segundo = $array[2];
        }
        if ($mes == 12) {
            $array = explode(":", $datatrim["diciembre"]);
            $hora = $array[0];
            $minuto = $array[1];
            $segundo = $array[2];
        }
    }

    $nodo = array(
        "hora" => $hora,
        "minuto" => $minuto,
        "segundo" => $segundo
    );
    return $nodo;
}

function buscarMes($connect_kpis, $id, $tipo)
{
    $periodo = array();
    $trims = 0;
    $queryFrecuencia = mysqli_query($connect_kpis, "SELECT * FROM Frecuencia_Kpis 
							WHERE id_kpi = '" . $id . "' AND tipo = $tipo ");
    while ($datatrim = mysqli_fetch_array($queryFrecuencia)) {

        if ($datatrim["julio"] != "") {
            array_push($periodo, 7);
        }
        if ($datatrim["agosto"] != "") {
            array_push($periodo, 8);
        }
        if ($datatrim["septiembre"] != "") {
            array_push($periodo, 9);
        }
        if ($datatrim["octubre"] != "") {
            array_push($periodo, 10);
        }
        if ($datatrim["noviembre"] != "") {
            array_push($periodo, 11);
        }
        if ($datatrim["diciembre"] != "") {
            array_push($periodo, 12);
        }
        if ($datatrim["enero"] != "") {
            array_push($periodo, 1);
        }
        if ($datatrim["febrero"] != "") {
            array_push($periodo, 2);
        }
        if ($datatrim["marzo"] != "") {
            array_push($periodo, 3);
        }
        if ($datatrim["abril"] != "") {
            array_push($periodo, 4);
        }
        if ($datatrim["mayo"] != "") {
            array_push($periodo, 5);
        }
        if ($datatrim["junio"] != "") {
            array_push($periodo, 6);
        }
        $trims++;
    }

    return $periodo;
}

function buscarMeta($connect_kpis, $id, $tipo, $connect_admin, $unidad_medida)
{
    // $meta = array();
    $trims = 0;

$queryFrecuencia = mysqli_query($connect_kpis, "SELECT * FROM Frecuencia_Kpis FKP
INNER JOIN Kpis K ON K.id = FKP.id_kpi
WHERE FKP.id_kpi = '" . $id . "' AND FKP.tipo = $tipo AND K.unidad_medida = $unidad_medida ORDER BY FKP.id ASC ");
    $datatrim = mysqli_fetch_array($queryFrecuencia);
    $meta = [
        1 => $datatrim["enero"],
        2 => $datatrim["febrero"],
        3 => $datatrim["marzo"],
        4 => $datatrim["abril"],
        5 => $datatrim["mayo"],
        6 => $datatrim["junio"],
        7 => $datatrim["julio"],
        8 => $datatrim["agosto"],
        9 => $datatrim["septiembre"],
        10 => $datatrim["octubre"],
        11 => $datatrim["noviembre"],
        12 => $datatrim["diciembre"]
    ];

    $queryEmpresa = mysqli_query($connect_admin, "SELECT * FROM Empresas WHERE id = '" . $_SESSION["id_empresa_valentina"] . "'");
    $dataEmpresa = mysqli_fetch_array($queryEmpresa);
    $inicio = new DateTime($dataEmpresa["mes_inicio"]);
    $mesInicio = (int)$inicio->format('m');
    // $mesInicio = (int)$dataEmpresa["mes_inicio"];
    // $mes_actual = date("n");
    // $anio_actual = date("Y");
    $mes_actual = $mesInicio - 1;
    if ($mesInicio == 12) {
        $mes_actual = 1;
    }
    $meses_permitidos = obtenerRangosDeMeses($mesInicio, $mes_actual, $tipo);

    $resultado_final = [];

    foreach ($meses_permitidos as $mes) {
        if (isset($meta[$mes])) {
            $resultado_final[] = $meta[$mes];
        }
    }
    // echo "<pre>";
    // print_r($meta);
    // echo "<pre>";
    return $resultado_final;
}

function obtenerRangosDeMeses($mes_inicio, $mes_actual, $periodo)
{
    $meses = [];

    for ($mes = $mes_inicio; $mes <= 12; $mes++) {
        $meses[] = $mes;
    }
    for ($mes = 1; $mes <= $mes_actual; $mes++) {
        $meses[] = $mes;
    }

    $meses_bloqueados = [];

    switch ($periodo) {
        case 1: // Mensual
            $meses_bloqueados = $meses;
            break;
        case 2: // Bimestral
            for ($i = 0; $i < count($meses); $i++) {
                $meses_bloqueados[] = $meses[$i];
                $i++;
            }
            break;
        case 3: // Trimestral
            for ($i = 0; $i < count($meses); $i++) {
                if ($i % 3 == 2) {
                    $meses_bloqueados[] = $meses[$i];
                }
            }
            break;
        case 6: // Cuatrimestral
            for ($i = 0; $i < count($meses); $i++) {
                if ($i % 4 == 3) {
                    $meses_bloqueados[] = $meses[$i];
                }
            }
            break;
        case 4: // Semestral
            for ($i = 0; $i < count($meses); $i++) {
                if ($i % 6 == 5) {
                    $meses_bloqueados[] = $meses[$i];
                }
            }
            break;
        case 5: // Anual
            $meses_bloqueados[] = $mes_inicio;
            break;
    }

    return $meses_bloqueados;
}

function Seguimiento_Tiempo($avance_1, $avance_2, $avance_3, $avance_4, $avance_5, $avance_6, $avance_7, $avance_8, $avance_9, $avance_10, $avance_11, $avance_12, $id_kpi,
$connect_kpis)
{
    $queryFrecuencia = mysqli_query($connect_kpis, "SELECT * FROM Frecuencia_Kpis WHERE id = $id_kpi");
    $dataFrecuencia = mysqli_fetch_array($queryFrecuencia);
    $sumH = $sumM = $sumS = "";
    if ($avance_1 != null || $avance_1 != "" && ($dataFrecuencia["enero"] != null || $dataFrecuencia["enero"] != "")) {
        $array1 = explode(":", $avance_1);
        $sumH = $sumH + $array1[0];
        $sumM = $sumM + $array1[1];
        $sumS = $sumS + $array1[2];
    }
    if ($avance_2 != null || $avance_2 != "" && ($dataFrecuencia["febrero"] != null || $dataFrecuencia["febrero"] != "")) {
        $array2 = explode(":", $avance_2);
        $sumH = $sumH + $array2[0];
        $sumM = $sumM + $array2[1];
        $sumS = $sumS + $array2[2];
    }
    if ($avance_3 != null || $avance_3 != "" && ($dataFrecuencia["marzo"] != null || $dataFrecuencia["marzo"] != "")) {
        $array3 = explode(":", $avance_3);
        $sumH = $sumH + $array3[0];
        $sumM = $sumM + $array3[1];
        $sumS = $sumS + $array3[2];
    }
    if ($avance_4 != null || $avance_4 != ""  && ($dataFrecuencia["abril"] != null || $dataFrecuencia["abril"] != "")) {
        $array4 = explode(":", $avance_4);
        $sumH = $sumH + $array4[0];
        $sumM = $sumM + $array4[1];
        $sumS = $sumS + $array4[2];
    }
    if ($avance_5 != null || $avance_5 != ""  && ($dataFrecuencia["mayo"] != null || $dataFrecuencia["mayo"] != "")) {
        $array5 = explode(":", $avance_5);
        $sumH = $sumH + $array5[0];
        $sumM = $sumM + $array5[1];
        $sumS = $sumS + $array5[2];
    }
    if ($avance_6 != null || $avance_6 != ""  && ($dataFrecuencia["junio"] != null || $dataFrecuencia["junio"] != "")) {
        $array6 = explode(":", $avance_6);
        $sumH = $sumH + $array6[0];
        $sumM = $sumM + $array6[1];
        $sumS = $sumS + $array6[2];
    }
    if ($avance_7 != null || $avance_7 != ""  && ($dataFrecuencia["julio"] != null || $dataFrecuencia["julio"] != "")) {
        $array7 = explode(":", $avance_7);
        $sumH = $sumH + $array7[0];
        $sumM = $sumM + $array7[1];
        $sumS = $sumS + $array7[2];
    }
    if ($avance_8 != null || $avance_8 != ""  && ($dataFrecuencia["agosto"] != null || $dataFrecuencia["agosto"] != "")) {
        $array8 = explode(":", $avance_8);
        $sumH = $sumH + $array8[0];
        $sumM = $sumM + $array8[1];
        $sumS = $sumS + $array8[2];
    }
    if ($avance_9 != null || $avance_9 != ""  && ($dataFrecuencia["septiembre"] != null || $dataFrecuencia["septiembre"] != "")) {
        $array9 = explode(":", $avance_9);
        $sumH = $sumH + $array9[0];
        $sumM = $sumM + $array9[1];
        $sumS = $sumS + $array9[2];
    }
    if ($avance_10 != null || $avance_10 != ""  && ($dataFrecuencia["octubre"] != null || $dataFrecuencia["octubre"] != "")) {
        $array10 = explode(":", $avance_10);
        $sumH = $sumH + $array10[0];
        $sumM = $sumM + $array10[1];
        $sumS = $sumS + $array10[2];
    }
    if ($avance_11 != null || $avance_11 != ""  && ($dataFrecuencia["noviembre"] != null || $dataFrecuencia["noviembre"] != "")) {
        $array11 = explode(":", $avance_11);
        $sumH = $sumH + $array11[0];
        $sumM = $sumM + $array11[1];
        $sumS = $sumS + $array11[2];
    }
    if ($avance_12 != null || $avance_12 != ""  && ($dataFrecuencia["diciembre"] != null || $dataFrecuencia["diciembre"] != "")) {
        $array12 = explode(":", $avance_12);
        $sumH = $sumH + $array12[0];
        $sumM = $sumM + $array12[1];
        $sumS = $sumS + $array12[2];
    }

    $sumSeguimiento = $sumH . ":" . $sumM . ":" . $sumS;

    return $sumSeguimiento;
}

function ProgresoHoras($frecuencia1, $frecuencia2, $tipo)
{
    if ($frecuencia1[0] != "" && $frecuencia2[0] != "") {
        $hora = $frecuencia1[0];
        $minuto = $frecuencia1[1];
        $segundo = $frecuencia1[2];

        $hora2 = $frecuencia2[0];
        $minuto2 = $frecuencia2[1];
        $segundo2 = $frecuencia2[2];

        $suma1 = $segundo + ($minuto * 60) + ($hora * 3600);
        $suma2 = $segundo2 + ($minuto2 * 60) + ($hora2 * 3600);

        if ($tipo == 1) {
            $progreso = round(($suma1 * 100) / $suma2, 2);
        }

        if ($tipo == 2) {
            $progreso = round(($suma2 / $suma1) * 100, 2);
            if (is_infinite($progreso) || is_nan($progreso)) {

                if ($suma2 == 0 && $suma1 == 0) {
                    $progreso = 100;
                }

                if ($suma1 == 0 && ($suma2 > 0 || $suma2 < 0)) {
                    $progreso = 100;
                }

                if ($suma2 == 0 && $suma1 < 0) {
                    $progreso = 100;
                }

                if ($suma2 > 0 && $suma1 == 0) {
                    $progreso = 100;
                }

                if ($suma2 == 0 && $suma1 > 0) {
                    $progreso = 100 - ($suma1 * 10);
                }
            }
        }
    } else {
        $progreso = "";
    }



    return $progreso;
}

function CalculoProgresoDesMes($meta, $avance)
{
    $progreso = 0;
    if ($meta == 0 && $avance == 0) {
        $progreso = 100;
    }

    if ($avance == 0 && ($meta > 0 || $meta < 0)) {
        $progreso = 100;
    }

       

    if ($meta > 0 && $avance == 0) {
        $progreso = 100;
    }

    if ($meta == 0 && $avance > 0) {
        $progreso = 100;
    }

    if ($progreso != 100) {
        if ($meta > 0 && $avance < 0) {
            $progreso = (round(($meta / $avance) * 100, 2)) * (-1);
        } else if ($meta == 0 && $avance > 0) {
            $progreso = 100 - ($avance * 10);
        } else {
            // $progreso = ($avance*100)/$meta; /// NUEVO CODIGO
            // $progreso = round($progreso, 2); /// NUEVO CODIGO
            $progreso = round(($meta / $avance) * 100, 2);
        }
    }

    return $progreso;
}

function MesLabel($mesInicio, $cantidad_meses)
{
    $meses = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    ];
    $listado = [];

    if ($mesInicio > 12) {
        switch ($mesInicio) {
            case 13:
                $mesInicio = 1;
                break;
            case 14:
                $mesInicio = 2;
                break;
            case 15:
                $mesInicio = 3;
                break;
            case 16:
                $mesInicio = 4;
                break;
            case 17:
                $mesInicio = 5;
                break;
            case 18:
                $mesInicio = 6;
                break;
            case 19:
                $mesInicio = 7;
                break;
            case 20:
                $mesInicio = 8;
                break;
            case 21:
                $mesInicio = 9;
                break;
            case 22:
                $mesInicio = 10;
                break;
            case 23:
                $mesInicio = 11;
                break;
            case 24:
                $mesInicio = 12;
                break;
        }
    }

    if ($cantidad_meses == 6 || $cantidad_meses == 12) {
        $mesFin = $mesInicio + ($cantidad_meses - 1);
        $listado = "";
        $listado .= $meses[$mesInicio];
        if ($mesFin > 12) {
            switch ($mesFin) {
                case 13:
                    $mesFin = 1;
                    break;
                case 14:
                    $mesFin = 2;
                    break;
                case 15:
                    $mesFin = 3;
                    break;
                case 16:
                    $mesFin = 4;
                    break;
                case 17:
                    $mesFin = 5;
                    break;
                case 18:
                    $mesFin = 6;
                    break;
                case 19:
                    $mesFin = 7;
                    break;
                case 20:
                    $mesFin = 8;
                    break;
                case 21:
                    $mesFin = 9;
                    break;
                case 22:
                    $mesFin = 10;
                    break;
                case 23:
                    $mesFin = 11;
                    break;
                case 24:
                    $mesFin = 12;
                    break;
            }
            $listado .= " - " . $meses[$mesFin];
        } else {
            $listado .= " - " . $meses[$mesFin];
        }
        $ListadoFrecuencia = $listado;
    } else {
        for ($i = 0; $i < $cantidad_meses; $i++) {
            $listado[] = $meses[$mesInicio];

            $mesInicio++;
            if ($mesInicio > 12) {
                $mesInicio = 1;
            }
        }

        $ListadoFrecuencia = implode(' - ', $listado);
    }

    return $ListadoFrecuencia;
}

function obtenerListadoDesdeMes($fecha_inicio)
{
    $meses = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    ];

    $inicio = new DateTime($fecha_inicio);

    $mes_inicial = (int)$inicio->format('m');

    $meses_seleccionados = [];

    for ($i = $mes_inicial; $i <= 12; $i++) {
        $meses_seleccionados[$i] = $meses[$i];
    }

    for ($i = 1; $i < $mes_inicial; $i++) {
        $meses_seleccionados[$i] = $meses[$i];
    }

    return $meses_seleccionados;
}

function AgregarMetaFrecuencia($mes_inicial, $cantidad_meses, $valor)
{
    $meses = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    ];

    $meses_resultado = [];
    for ($i = 0; $i < $cantidad_meses; $i++) {
        $mes_num = ($mes_inicial + $i - 1) % 12 + 1;

        if ($cantidad_meses == 12 && ($i == 0 || $i == $cantidad_meses - 1)) {
            $meses_resultado[$mes_num] = [
                'nombre' => $meses[$mes_num],
                'valor' => $valor
            ];
        } else {
            if (!isset($meses_resultado[$mes_num])) {
                $meses_resultado[$mes_num] = [
                    'nombre' => $meses[$mes_num],
                    'valor' => $valor
                ];
            }
        }
    }

    return $meses_resultado;
}

function obtenerNombreMes($mes)
{
    $meses = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    ];

    return isset($meses[$mes]) ? $meses[$mes] : '';
}

function obtenerMesesPermitidos($fecha_inicio, $fecha_fin, $frecuencia)
{
    $inicio = new DateTime($fecha_inicio);
    $fin = new DateTime($fecha_fin);

    $meses_permitidos = [];
    $mes = $inicio->format('m');
    $anio = $inicio->format('Y');

    while ($inicio <= $fin) {
        $meses_permitidos[] = ['mes' => $mes, 'anio' => $anio, 'nombre' => obtenerNombreMes($mes)];
        $inicio->modify('+1 month');
        $mes = $inicio->format('m');
        $anio = $inicio->format('Y');
    }

    $meses_grouped = [];
    $group_size = 0;
    switch ($frecuencia) {
        case 1: // Mensual
            $group_size = 1;
            break;
        case 2: // Bimestral
            $group_size = 2;
            break;
        case 3: // Trimestral
            $group_size = 3;
            break;
        case 6: // Cuatrimestral
            $group_size = 4;
            break;
        case 4: // Semestral
            $group_size = 6;
            break;
        case 5: // Anual
            $group_size = 12;
            break;
        default:
            return [];
    }

    $i = 0;
    while ($i < count($meses_permitidos)) {
        $group = array_slice($meses_permitidos, $i, $group_size);
        $meses_grouped[] = $group;
        $i += $group_size;
    }

    return $meses_grouped;
}

function agruparMesesPorFrecuencia($mes_inicial, $frecuencia)
{
    $meses = obtenerListadoDesdeMes($mes_inicial);
    $meses_grouped = [];

    $mes_count = count($meses);
    $group_size = 0;

    switch ($frecuencia) {
        case 2:
            $group_size = 2;
            break;
        case 3:
            $group_size = 3;
            break;
        case 6:
            $group_size = 4;
            break;
        case 4:
            $group_size = 6;
            break;
        case 5:
            $group_size = 12;
            break;
        default:
            return [];
    }

    $i = 0;
    while ($i < $mes_count) {
        $group = [];

        if ($frecuencia == 4) {
            $group = array_merge(array_slice($meses, $i, 6));
            $i += 6;
        } else {
            $group = array_merge(array_slice($meses, $i, $group_size));
            $i += $group_size;
        }

        $end_month = end($group);
        $end_month_num = array_search($end_month, $meses);

        $start_month = $group[0];
        $meses_grouped[$end_month_num] = "$start_month - $end_month";
    }

    return $meses_grouped;
}

function obtenerRangoMeses($fecha_inicio, $fecha_fin)
{
    $meses = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    ];

    $inicio = new DateTime($fecha_inicio . '-01');
    $fin = new DateTime($fecha_fin . '-01');

    $meses_en_rango = [];

    while ($inicio <= $fin) {
        $mes = (int) $inicio->format('m');
        $meses_en_rango[$mes] = $meses[$mes];

        $inicio->modify('+1 month');
    }

    return $meses_en_rango;
}

function generarPeriodos($mesInicio, $mesFin, $codigoPeriodo) {
    $fechaInicioObj = new DateTime($mesInicio . '-01');
    $fechaFinObj = new DateTime($mesFin . '-01');
    
    $mesInicioInt = (int) $fechaInicioObj->format('m');
    $mesFinInt = (int) $fechaFinObj->format('m');
    $anioInicio = (int) $fechaInicioObj->format('Y');
    $anioFin = (int) $fechaFinObj->format('Y');

    $periodos = [];
    $meses = [];
    $mes = $mesInicioInt;
    $anio = $anioInicio;

    // Generar los meses del periodo
    while ($anio < $anioFin || ($anio == $anioFin && $mes <= $mesFinInt)) {
        $meses[] = $mes;

        // Incrementar mes
        $mes++;
        if ($mes > 12) {
            $mes = 1; // Resetear a enero
            $anio++; // Pasar al siguiente año
        }
    }

    // Crear periodos por tipo
    switch ($codigoPeriodo) {
        case 2: // Bimestral
            $bimestres = [];
            for ($i = 0; $i < count($meses); $i += 2) {
                if (isset($meses[$i + 1])) {
                    $bimestres[$meses[$i]] = [$meses[$i], $meses[$i + 1]];
                }
            }
            $periodos[2] = $bimestres;
            break;

        case 3: // Trimestral
            $trimestres = [];
            for ($i = 0; $i < count($meses); $i += 3) {
                if (isset($meses[$i + 2])) {
                    $trimestres[$meses[$i]] = [$meses[$i], $meses[$i + 1], $meses[$i + 2]];
                }
            }
            $periodos[3] = $trimestres;
            break;

        case 6: // Cuatrimestral
            $cuatrimestres = [];
            for ($i = 0; $i < count($meses); $i += 4) {
                if (isset($meses[$i + 3])) {
                    $cuatrimestres[$meses[$i]] = [$meses[$i], $meses[$i + 1], $meses[$i + 2], $meses[$i + 3]];
                }
            }
            $periodos[6] = $cuatrimestres;
            break;

        case 4: // Semestral
            $semestres = [];
            for ($i = 0; $i < count($meses); $i += 6) {
                if (isset($meses[$i + 5])) {
                    $semestres[$meses[$i]] = [$meses[$i], $meses[$i + 1], $meses[$i + 2], $meses[$i + 3], $meses[$i + 4], $meses[$i + 5]];
                }
            }
            $periodos[4] = $semestres;
            break;

        default:
            echo "Código de periodo no válido.";
            return [];
    }

    return $periodos;
}

function obtenerValorDelUltimoRango($fecha_inicio, $fecha_fin, $frecuencia, $valores)
{
    // Obtener los rangos de meses agrupados según la frecuencia
    $meses_grouped = obtenerMesesPermitidos($fecha_inicio, $fecha_fin, $frecuencia);

    // Obtener el mes y año actuales
    $mes_actual = date('m'); // Este es el mes actual
    $anio_actual = date('Y'); // Este es el año actual

    // Búsqueda de valor en el rango actual y en rangos anteriores si es necesario
    $sumSeguimiento = null;

    // Si el mes actual es enero (mes 1), buscamos el valor de diciembre (mes 12)
    if ($mes_actual == 1) {
        // Comprobamos el valor de diciembre del año anterior
        if (isset($valores[12])) {
            return $valores[12];
        }
    }

    foreach ($meses_grouped as $index => $rango) {
        $en_rango_actual = false;
        foreach ($rango as $mes) {
            // Comprobar si el mes actual está dentro del rango
            if ((int)$mes['mes'] === (int)$mes_actual && (int)$mes['anio'] === (int)$anio_actual) {
                $en_rango_actual = true;
                break;
            }
        }

        if ($en_rango_actual) {
            // Intentar obtener el último valor en el rango actual
            foreach (array_reverse($rango) as $mes) {
                $mes_index = (int)$mes['mes'];
                if (isset($valores[$mes_index])) {
                    // Si el valor es 0 o cualquier otro valor no nulo, se acepta
                    if ($valores[$mes_index] !== null) {
                        return $valores[$mes_index];  // Valor válido encontrado
                    }
                }
            }

            // Si no se encuentra en el rango actual, buscar en el rango anterior
            if ($index > 0) {
                $rango_anterior = $meses_grouped[$index - 1];
                foreach (array_reverse($rango_anterior) as $mes) {
                    $mes_index = (int)$mes['mes'];
                    if (isset($valores[$mes_index])) {
                        // Si el valor no es null (aceptar 0)
                        if ($valores[$mes_index] !== null) {
                            return $valores[$mes_index];  // Valor válido encontrado
                        }
                    }
                }
            }
        }
    }

    // Si no se encuentra en los rangos anteriores ni el actual, buscar en cualquier rango válido
    foreach (array_reverse($meses_grouped) as $rango) {
        foreach (array_reverse($rango) as $mes) {
            $mes_index = (int)$mes['mes'];
            if (isset($valores[$mes_index])) {
                // Verifica si hay un valor válido (aceptando 0 como valor válido)
                if ($valores[$mes_index] !== null) {
                    return $valores[$mes_index];
                }
            }
        }
    }

    return null;  // Si no se encuentra ningún valor válido
}

function calcularDiferenciaMeses($mesInicio, $mesFin) {
    if ($mesFin < $mesInicio) {
        $mesFin += 12;
    }

    return $mesFin - $mesInicio + 1;
}

function PromedioGeneralEvaluado($id_empleado, $connect_valoracion, $connect_admin)
{

    $permitir = true;
    $no_evaluaciones = 0;
    $array_tipos = array();
    $id_cargo = 0;

    //CONSULTAMOS LOS EVALUADORES DE ESTE EMPLEADO
    $queryEvaluadores = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores 
    WHERE id_empresa = '" . $_SESSION['id_empresa_valentina'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND id_empleado = '" . $id_empleado . "' ");
    while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) {

        $queryEval = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE 
        id_empresa = '" . $_SESSION['id_empresa_valentina'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND
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

    if ($promedio_auto >= 0) {
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
    WHERE id_empresa = '" . $_SESSION['id_empresa_valentina'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "'  " . $fill . " ");
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
        "arreglos" => $array_tipos,
        "proceso_valoracion" => $proceso_valoracion,
        "estado" => $estado
    );
}

function PromedioCompetencias($id_empleado, $connect_valoracion, $connect_admin)
{

    $permitir = true;
    $no_evaluaciones = 0;
    $array_tipos = array();
    $id_cargo = 0;

    $queryEval = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE 
    id_empresa = '" . $_SESSION['id_empresa_valentina'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND
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
    WHERE id_empresa = '" . $_SESSION['id_empresa_valentina'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "'  " . $fill . " ");
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

function RetornarColor($valor, $rangos)
{

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