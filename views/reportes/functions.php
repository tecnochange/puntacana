<?php
function PorOkrsPonderacion($connect_okrs, $okr, $id_empleado, $filtro_claves){

	$filtro_kr = " ";
	if ($filtro_claves) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr .= $filtro_claves;
	}

	$suma_resultado = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;
	$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr  $filtro_kr ");
	// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr  $filtro_kr ";
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

		$suma_resultado += $porcentaje;
		$conteo_resultado++;
	}
	if ($suma_resultado > 0) {
		$resultado_prom_okr = ($suma_resultado / $conteo_resultado);
	} else {
		$resultado_prom_okr += 0;
	}
	$nodo = array(
		"promedio" => $resultado_prom_okr,
		"no_resultados" => $queryResultados->num_rows
	);

	return $nodo;
}
?>