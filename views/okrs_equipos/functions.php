<?php
include '../../app/helpers/helpers.php';
//POR OKRS
function PorOkrs($connect_valentina, $connect_okrs, $okr, $id_empresa)
{

	$filtro_kr = " ";
	if ($_SESSION["periodo_fill"]) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr .= " AND periodo IN (" . $_SESSION["periodo_fill"] . ") ";
	}

	if ($_SESSION["colaborador_fill_okr"]) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr .= " AND (responsables LIKE '%," . $_SESSION["colaborador_fill_okr"] . "%' OR responsables LIKE '%" . $_SESSION["colaborador_fill_okr"] . ",%' OR responsables IN ('" . $_SESSION["colaborador_fill_okr"] . "')) ";
	}

	$suma_resultado = 0;
	$porcentaje = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;
	$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = '" . $okr . "'  " . $filtro_kr . " ");
	// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs = '".$okr."'  ".$filtro_kr."<br> ";
	while ($dataResultados = mysqli_fetch_array($queryResultados)) {
		$porcentaje = ($dataResultados["avance"] * 100) / $dataResultados["meta"];

        if($dataResultados["meta"] < 0){
            if( $dataResultados["avance"] > $dataResultados["meta"] ){
                $porcentaje = 100;
            }
        }

		if ($dataResultados["tendencia"] == 2) {
			$porcentaje = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
		}

        //SE AGREGA EL 16 DE OCTIBRE PARA SOLUCIONAR EL OKR DE UN COLABORADOR
        if ($dataResultados["meta"] === '0' && $dataResultados["avance"] === '0' ) {
			$porcentaje = 100;
		}

		if (is_infinite($porcentaje) || is_nan($porcentaje)) {
			$porcentaje = 0;
		}

		if ($porcentaje > 100) {
			$porcentaje = 100;
		}

		$suma_resultado = $suma_resultado + $porcentaje;
		$conteo_resultado++;
	}

	// if ($suma_resultado > 0) {
	// 	$resultado_prom_okr = ($suma_resultado / $conteo_resultado);
	// } else {
	// 	$resultado_prom_okr += 0;
	// }

	$resultado_prom_okr = ($suma_resultado / mysqli_num_rows($queryResultados));
	// echo $resultado_prom_okr."<br>";
	$nodo = array(
		"promedio" => $resultado_prom_okr,
		"no_resultados" => $queryResultados->num_rows
	);
	return $nodo;
}

function PorOkrsIndividual($connect_valentina, $connect_okrs, $okr, $id_empresa)
{

	$filtro_kr = " ";
	if ($_SESSION["periodo_fill"]) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr .= " AND periodo IN (" . $_SESSION["periodo_fill"] . ") ";
	}

	$suma_resultado = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;
	$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = '" . $okr . "'  " . $filtro_kr . " AND CONCAT(',',responsables,',') LIKE '%," . $_SESSION["id_user"]. ",%' ");

	while ($dataResultados = mysqli_fetch_array($queryResultados)) {
		$porcentaje = ($dataResultados["avance"] * 100) / $dataResultados["meta"];

        if($dataResultados["meta"] < 0){
            if( $dataResultados["avance"] > $dataResultados["meta"] ){
                $porcentaje = 100;
            }
        }

		if ($dataResultados["tendencia"] == 2) {
			$porcentaje = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
		}

        //SE AGREGA EL 16 DE OCTIBRE PARA SOLUCIONAR EL OKR DE UN COLABORADOR
        if ($dataResultados["meta"] === '0' && $dataResultados["avance"] === '0' ) {
			$porcentaje = 100;
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

function PorOkrsIndividualIniciativa($connect_valentina, $connect_okrs, $okr, $id_empresa)
{

	$filtro_kr = " ";
	if ($_SESSION["periodo_fill"]) {
		$filtro_kr .= " AND periodo IN (" . $_SESSION["periodo_fill"] . ") ";
	}

	$suma_resultado = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;
	$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr $filtro_kr");
	// $queryResultados = mysqli_query($connect_okrs, "SELECT ORE.* FROM Okrs_Resultados ORE LEFT JOIN Okrs_Iniciativas OI ON ORE.id = OI.id_resultado LEFT JOIN Okrs O ON O.id = ORE.id_okrs WHERE OI.responsables LIKE '%".$_SESSION["id_user"]."%' AND ORE.id_okrs = $id_okrs $filtro_kr");
	// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr $filtro_kr";
	while ($dataResultados = mysqli_fetch_array($queryResultados)) {
		$queryIniciativas1 = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Iniciativas WHERE id_resultado = '" . $dataResultados["id"] . "' AND (responsables LIKE '%" . $_SESSION["id_user"] . ",%' OR responsables LIKE '%," . $_SESSION["id_user"] . ",%' OR responsables IN ('" . $_SESSION["id_user"] . "'))");
		if (mysqli_num_rows($queryIniciativas1) > 0) {

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
	}
	// if ($suma_resultado > 0) {
	// 	$resultado_prom_okr = ($suma_resultado / $conteo_resultado);
	// } else {
	// 	$resultado_prom_okr += 0;
	// }
	$resultado_prom_okr = ($suma_resultado / $conteo_resultado);
	$nodo = array(
		"promedio" => $resultado_prom_okr,
		"no_resultados" => $queryResultados->num_rows
	);
	return $nodo;
}

function GuardarResultado($post, $connect_okrs, $role, $id_empresa, $id_empleado)
{
	$metaFormateo = normalizarNumero($post["meta"], $_SESSION['formatoNumerico']);
	$hoy = date("Y-m-d H:i:s");
	if ($post["id_registro"] != "") {
		$sentencia = "
			UPDATE Okrs_Resultados  SET descripcion = '" . $post["descripcion"] . "', fecha_inicia = '" . $post["fecha_inicia"] . "',
			fecha_entrega = '" . $post["fecha_entrega"] . "', responsables = '" . implode(",", $post["responsables"]) . "',
			tendencia = '" . $post["tendencia"] . "', medicion = '" . $post["medicion"] . "', periodo = '" . $post["periodo"] . "',
			meta = '" . $metaFormateo . "', meta_minimo = '0',
			meta_maximo = '0'
			WHERE id = '" . $post["id_registro"] . "'
			";
		// echo $sentencia;
		mysqli_query($connect_okrs, $sentencia);

		$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $post["id_okrs"] . "");
		$data2 = mysqli_fetch_array($query);

		$accion = 'ACTUALIZAR';
		$descripcion = 'Actualización de KR ' . $post["descripcion"] . ' para el OKR ' . $data2["objetivo_okr"];

		$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $id_empresa . ", " . $id_empleado . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $post["id_okrs"] . "," . $post["id_registro"] . ",0,'$hoy')";
		// echo $auditoria;
		mysqli_query($connect_okrs, $auditoria);
	}
}

function GuardarInciativa($post, $connect_okrs, $id_empleado, $id_empresa, $id_user)
{
	$hoy = date("Y-m-d H:i:s");

	$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $post["id_okr"] . "");
	$data2 = mysqli_fetch_array($query);

	$query1 = mysqli_query($connect_okrs, "SELECT * FROM Okrs_resultado WHERE id = " . $post["id_resultado"] . "");
	$data3 = mysqli_fetch_array($query1);

	$id = "";

	if ($post["id_registro"] != "") {

		if ($id_empresa == 1) {
			$sentencia = "
	UPDATE Okrs_Iniciativas SET descripcion = '" . $post["descripcion"] . "',
	responsables = '" . implode(",", $post["responsables"]) . "', fecha_entrega = '" . $post["fecha_entrega"] . "', meta = '" . $post["meta"] . "',
	tendencia = '" . $post["tendencia"] . "', updated_at = '" . $hoy . "', mes = '" . $post["mes"] . "'
	WHERE id = '" . $post["id_registro"] . "'
	";
		} else {
			$responsables = "";
			$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Iniciativas WHERE id = '" . $_POST["id_registro"] . "' ");
			$data = mysqli_fetch_array($query);

			$baseDeDatos = $data["responsables"];

			$arrayBaseDatos = explode(',', $baseDeDatos);

			$arrayFormulario = $post["responsables"];

			if (in_array($id_empleado, $arrayFormulario)) {
				$arrayBaseDatos[] = $id_empleado;
			}

			$eliminar = array_diff($arrayBaseDatos, $arrayFormulario);

			$agregar = array_diff($arrayFormulario, $arrayBaseDatos);

			foreach ($agregar as $numAgregar) {
				if ($numAgregar == $id_empleado) {
					$estado = 1;
				} else {
					$estado = 4;
				}
				$queryNI = mysqli_query($connect_okrs, "SELECT * FROM Notificacion_Iniciativas WHERE id_iniciativa = '" . $id . "' AND id_asignado = $numAgregar");
				if (mysqli_num_rows($queryNI) == 0) {
					mysqli_query($connect_okrs, "INSERT INTO Notificacion_Iniciativas (id_empresa, id_iniciativa,id_empleado,id_asignado,estado,created_at)
			VALUES(" . $_SESSION["id_empresa"] . "," . $_POST["id_registro"] . "," . $id_empleado . "," . $numAgregar . ",$estado,'$hoy')");
				}
			}

			$baseDeDatosFinal = array_intersect($arrayBaseDatos, $arrayFormulario);
			$baseDeDatosFinal = array_unique($baseDeDatosFinal);
			$responsables = implode(',', $baseDeDatosFinal);
			$sentencia = "
			UPDATE Okrs_Iniciativas SET descripcion = '" . $post["descripcion"] . "',
			responsables = '$responsables',
			fecha_entrega = '" . $post["fecha_entrega"] . "', meta = '" . $post["meta"] . "',
			tendencia = '" . $post["tendencia"] . "', updated_at = '" . $hoy . "', mes = '" . $post["mes"] . "'
			WHERE id = '" . $post["id_registro"] . "'
			";
		}

		mysqli_query($connect_okrs, $sentencia);
		// $array_lista = explode(",", $post["responsables"]);
		// foreach ($post["responsables"] as $id_resp) {
		// 	$queryNI = mysqli_query($connect_okrs, "SELECT * FROM Notificacion_Iniciativas WHERE id_iniciativa = " . $post["id_registro"] . " AND id_asignado = $id_resp");
		// 	if (mysqli_num_rows($queryNI) == 0) {
		// 		mysqli_query($connect_okrs, "INSERT INTO Notificacion_Iniciativas (id_empresa, id_iniciativa,id_empleado,id_asignado,estado,created_at)
		// 		VALUES(" . $id_empresa . "," . $post["id_registro"] . "," . $id_user . "," . $id_resp . ",4,'$hoy')");
		// 	}
		// }

		// echo $sentencia;

		$id = $post["id_registro"];
		$accion = 'ACTUALIZAR';
		$descripcion = 'Actualización de iniciativa ' . $post["descripcion"] . ' para el KR ' . $data3["descripcion"];
	} else {
		if ($id_empresa == 1) {
			$sentencia = "
			INSERT INTO Okrs_Iniciativas ( id_okrs , id_resultado, id_empleado, responsables, descripcion, mes, fecha_entrega, meta,  tendencia, created_at, aprobacion )
			VALUES
			( '" . $post["id_okr"] . "',  '" . $post["id_resultado"] . "', $id_empleado, '" . implode(",", $post["responsables"]) . "', '" . $post["descripcion"] . "', '" . $post["mes"] . "','" . $post["fecha_entrega"] . "', '" . $post["meta"] . "', '" . $post["tendencia"] . "','" . $hoy . "',1)";
		} else {
			$sentencia = "
			INSERT INTO Okrs_Iniciativas ( id_okrs , id_resultado, id_empleado, responsables, descripcion, mes, fecha_entrega, meta,  tendencia, created_at )
			VALUES
			( '" . $post["id_okr"] . "',  '" . $post["id_resultado"] . "', $id_empleado, '', '" . $post["descripcion"] . "', '" . $post["mes"] . "','" . $post["fecha_entrega"] . "', '" . $post["meta"] . "', '" . $post["tendencia"] . "','" . $hoy . "'  )
			";
		}
		// $sentencia = "
		// 	INSERT INTO Okrs_Iniciativas ( id_okrs , id_resultado, id_empleado, responsables, descripcion, mes, fecha_entrega, meta,  tendencia, created_at )
		// 	VALUES
		// 	( '" . $post["id_okr"] . "',  '" . $post["id_resultado"] . "', $id_empleado, '', '" . $post["descripcion"] . "', '" . $post["mes"] . "','" . $post["fecha_entrega"] . "', '" . $post["meta"] . "', '" . $post["tendencia"] . "','" . $hoy . "'  )
		// 	";

		// echo $sentencia;

		mysqli_query($connect_okrs, $sentencia);
		$id = mysqli_insert_id($connect_okrs);
		if ($id_empresa != 1) {
			if (count($post["responsables"]) > 1) {
				foreach ($post["responsables"] as $id_resp) {
					if ($id_user == $id_resp) {

						$sentencia = "UPDATE Okrs_Iniciativas SET responsables = '" . $id_user . "' WHERE id = '" . $id . "'";
						mysqli_query($connect_okrs, $sentencia);
					} else {
						$aprobacion = 4;
						$queryNI = mysqli_query($connect_okrs, "SELECT * FROM Notificacion_Iniciativas WHERE id_iniciativa = '" . $id . "' AND id_asignado = $id_resp");
						if (mysqli_num_rows($queryNI) == 0) {
							mysqli_query($connect_okrs, "INSERT INTO Notificacion_Iniciativas (id_empresa, id_iniciativa,id_empleado,id_asignado,estado,created_at)
			VALUES(" . $id_empresa . ",$id," . $id_user . "," . $id_resp . ",$aprobacion,'$hoy')");
						}
					}
					// 	ECHO "INSERT INTO Notificacion_Iniciativas (id_empresa, id_iniciativa,id_empleado,id_asignado,estado,created_at)
					// VALUES(" . $id_empresa . ",$id," . $id_user . "," . $id_resp . ",4,'$hoy')";

				}
			} else {
				foreach ($post["responsables"] as $id_resp) {
					if ($id_user == $id_resp) {
						$sentencia = "UPDATE Okrs_Iniciativas SET responsables = '" . $id_user . "' WHERE id = '" . $id . "'";
						mysqli_query($connect_okrs, $sentencia);
					} else {
						$queryNI = mysqli_query($connect_okrs, "SELECT * FROM Notificacion_Iniciativas WHERE id_iniciativa = '" . $id . "' AND id_asignado = $id_resp");
						if (mysqli_num_rows($queryNI) == 0) {
							mysqli_query($connect_okrs, "INSERT INTO Notificacion_Iniciativas (id_empresa, id_iniciativa,id_empleado,id_asignado,estado,created_at)
			VALUES(" . $id_empresa . ",$id," . $id_user . "," . $id_resp . ",4,'$hoy')");
						}
					}
				}
				// ECHO "INSERT INTO Notificacion_Iniciativas (id_empresa, id_iniciativa,id_empleado,id_asignado,estado,created_at)
				// VALUES(" . $id_empresa . ",$id," . $id_user . "," . $post["responsables"][0] . ",4,'$hoy')";

			}
		}

		$accion = 'CREAR';
		$descripcion = 'Creación de iniciativa ' . $post["descripcion"] . ' para el KR ' . $data3["descripcion"];
	}

	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $id_empresa . ", " . $id_user . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $post["id_okr"] . "," . $post["id_resultado"] . ",$id,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);

	return $id;
}

function GuardarComentario($post, $connect_okrs, $id_empleado, $id_empresa, $id_user)
{
	$hoy = date("Y-m-d H:i:s");
	$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $post["id_okr"] . "");
	$data2 = mysqli_fetch_array($query);
	$sentencia = "
		INSERT INTO Okrs_Comentarios ( id_okrs , id_resultado, id_empleado, comentario,  created_at )
		VALUES
		( '" . $post["id_okr"] . "',  '" . $post["id_resultado"] . "', '" . $id_empleado . "', '" . $post["comentario"] . "', '" . $hoy . "'  )
			";
	mysqli_query($connect_okrs, $sentencia);
	$id = mysqli_insert_id($connect_okrs);
	$accion = 'CREAR';
	$descripcion = 'Creación de comentario de resultado clave: ' . $post["comentario"] . ' para el OKR ' . $data2["objetivo_okr"];
	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $id_empresa . ", " . $id_user . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $post["id_okr"] . "," . $post["id_resultado"] . ",0,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);
}

function EditarComentario($post, $connect_okrs, $id_empresa, $id_user)
{
	$hoy = date("Y-m-d H:i:s");
	$sentencia = "UPDATE Okrs_Comentarios SET comentario = '" . $post["comentario"] . "', updated_at = '$hoy' WHERE id = " . $post["id_registro"] . "";
	// echo $sentencia;
	mysqli_query($connect_okrs, $sentencia);
	$sentencia1 = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Comentarios WHERE id = " . $post["id_registro"] . "");
	$dataSentencia = mysqli_fetch_array($sentencia1);
	$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $dataSentencia["id_okrs"] . "");
	$data2 = mysqli_fetch_array($query);
	$accion = 'ACTUALIZAR';
	$descripcion = 'Actualización de comentario de resultado clave: ' . $post["comentario"] . ' para el OKR ' . $data2["objetivo_okr"];
	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $id_empresa . ", " . $id_user . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $data2["id"] . "," . $dataSentencia["id_resultado"] . ",0,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);
}

function OkrsFiltro($id_empleado, $id_empresa, $connect_okrs, $inicio, $registros_por_pagina)
{

	$filtros = "";
	if ($id_empleado > 0) {
		$filtros = " AND Okrs_Equipos.id_empleado = '" . $id_empleado . "' ";
	}
	if ($id_empleado == 0) {
		$filtros = " AND Okrs_Equipos.id_empleado > 0 ";
	}
	$anio = "AND Okrs.anio = " . $_SESSION["anio_fill"];

	$sentencia = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_okrs AS id_okrs , Okrs.objetivo_okr AS objetivo,
		Okrs.fecha_inicia AS fecha_inicia, Okrs.fecha_termina AS fecha_termina ,
		Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
		" . $filtros . "
		$anio
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		LIMIT $inicio, $registros_por_pagina
		";

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	while ($data = mysqli_fetch_array($query)) {
		if ($data["objetivo"]) {
			$obj = array(
				"id" => $data["id_okrs"],
				"objetivo" => $data["objetivo"],
				"fecha_inicia" => $data["fecha_inicia"],
				"fecha_termina" => $data["fecha_termina"],
				"tipo" => $data["tipo"],
				"periodo" => $data["periodo"],
				"objetivos_estrategicos" => $data["objetivos_estrategicos"],
				"anio" => $data["anio"],
			);
			array_push($nodos, $obj);
		}
	}
	return $nodos;
}

//OKRS DEL USUARIO FILTRADOS
function OkrsUsuario($id_empleado, $id_empresa, $connect_okrs, $filtro_periodo)
{

	$filtro_usuario = "";
	if ($id_empleado > 0) {
		$filtro_usuario = " AND Okrs_Equipos.id_empleado = '" . $id_empleado . "'  ";
	}

	$filtro = "";
	if ($_SESSION["equipo_fill"] > 0) {
		$filtro .= " AND Okrs_Equipos.id_okrs = '" . $_SESSION["equipo_fill"] . "' ";
	}

	// if($_SESSION["periodo_fill"] != ""){
	// 	$filtro .= " AND Okrs.periodo IN (".$_SESSION["periodo_fill"].") ";
	// }

	if ($_SESSION["anio_fill"] > 0) {
		$filtro .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
	}

	if ($_SESSION["colaborador_fill_okr"] > 0) {
		$filtro_usuario = " AND Okrs_Equipos.id_empleado = '" . $_SESSION["colaborador_fill_okr"] . "'  ";
		$filtro_usuario .= " AND (Okrs_Resultados.responsables LIKE '%" . $_SESSION["colaborador_fill_okr"] . ",%' OR Okrs_Resultados.responsables LIKE '%," . $_SESSION["colaborador_fill_okr"] . "%' OR Okrs_Resultados.responsables IN ('" . $_SESSION["colaborador_fill_okr"] . "'))  ";
		//$filtro .= " AND Okrs.id_empleado = '".$_SESSION["colaborador_fill_okr"]."' ";
		//$filtro .= " AND Okrs_Equipos.id_empleado = '".$_SESSION["colaborador_fill_okr"]."'  ";
	}

	if ($_SESSION["areas_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Areas.id_area = '" . $_SESSION["areas_fill_okr"] . "'  ";
		//$filtro .= " AND Okrs.id_empleado = '".$_SESSION["colaborador_fill_okr"]."' ";
		//$filtro .= " AND Okrs_Equipos.id_empleado = '".$_SESSION["colaborador_fill_okr"]."'  ";
	}

	if ($_SESSION["tipo_fill_okr"] > 0) {
		$filtro .= " AND Okrs.tipo = '" . $_SESSION["tipo_fill_okr"] . "'  ";
	}

	if ($_SESSION["objestrategico_fill"] > 0) {
		$filtro .= " AND Okrs.objetivos_estrategicos IN ('" . $_SESSION["objestrategico_fill"] . "')  ";
	}

	if ($_SESSION["vicepresidencia_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Vicepresidencia.id_vicepresidencia = " . $_SESSION["vicepresidencia_fill_okr"] . "  ";
	}

	if ($filtro_periodo) {
		$filtro .= " AND Okrs_Resultados.periodo IN ($filtro_periodo)";
	}

	$resultado = "";
	// if ($_SESSION["buscador_resultado"] != "") {
	// 	$resultado = "LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs";
	// 	$filtro .= " AND Okrs_Resultados.descripcion LIKE '%" . $_SESSION["buscador_resultado"] . "%'  ";
	// }
	// echo '<pre>';
	// print_r($_SESSION);
	$sentencia = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		" . $resultado . "
		WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
		" . $filtro_usuario . "
		" . $filtro . "
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC";

	// echo $sentencia;

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);

	while ($data = mysqli_fetch_array($query)) {

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
				"nombre_owner" => $data["nombre_owner"]
			);
			array_push($nodos, $obj);
		}
	}

	return $nodos;
}

function OkrsUsuarioPaginacion($id_empleado, $id_empresa, $connect_okrs, $inicio, $registros_por_pagina, $filtro_periodo)
{

	$filtro_usuario = "";
	if ($id_empleado > 0) {
		$filtro_usuario = " AND Okrs_Equipos.id_empleado = '" . $id_empleado . "'  ";
	}

	$filtro = "";
	if ($_SESSION["equipo_fill"] > 0) {
		$filtro .= " AND Okrs_Equipos.id_okrs = '" . $_SESSION["equipo_fill"] . "' ";
	}

	// if($_SESSION["periodo_fill"] != ""){
	// 	$filtro .= " AND Okrs.periodo IN (".$_SESSION["periodo_fill"].") ";
	// }

	if ($_SESSION["anio_fill"] > 0) {
		$filtro .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
	}

	if ($_SESSION["colaborador_fill_okr"] > 0) {
		$filtro_usuario = " AND Okrs_Equipos.id_empleado = '" . $_SESSION["colaborador_fill_okr"] . "'  ";
		$filtro_usuario .= " AND (Okrs_Resultados.responsables LIKE '%" . $_SESSION["colaborador_fill_okr"] . ",%' OR Okrs_Resultados.responsables LIKE '%," . $_SESSION["colaborador_fill_okr"] . "%' OR Okrs_Resultados.responsables IN ('" . $_SESSION["colaborador_fill_okr"] . "'))  ";
		//$filtro .= " AND Okrs.id_empleado = '".$_SESSION["colaborador_fill_okr"]."' ";
		//$filtro .= " AND Okrs_Equipos.id_empleado = '".$_SESSION["colaborador_fill_okr"]."'  ";
	}

	if ($_SESSION["areas_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Areas.id_area = '" . $_SESSION["areas_fill_okr"] . "'  ";
		//$filtro .= " AND Okrs.id_empleado = '".$_SESSION["colaborador_fill_okr"]."' ";
		//$filtro .= " AND Okrs_Equipos.id_empleado = '".$_SESSION["colaborador_fill_okr"]."'  ";
	}

	if ($_SESSION["tipo_fill_okr"] > 0) {
		$filtro .= " AND Okrs.tipo = '" . $_SESSION["tipo_fill_okr"] . "'  ";
	}

	if ($_SESSION["vicepresidencia_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Vicepresidencia.id_vicepresidencia = " . $_SESSION["vicepresidencia_fill_okr"] . "  ";
	}

	if ($_SESSION["objestrategico_fill"] > 0) {
		$filtro .= " AND Okrs.objetivos_estrategicos IN ('" . $_SESSION["objestrategico_fill"] . "')  ";
	}

	if ($filtro_periodo) {
		$filtro .= " AND Okrs_Resultados.periodo IN ($filtro_periodo)";
	}

	$resultado = "";
	// if ($_SESSION["buscador_resultado"] != "") {
	// 	$resultado = "LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs";
	// 	$filtro .= " AND Okrs_Resultados.descripcion LIKE '%" . $_SESSION["buscador_resultado"] . "%'  ";
	// }
	// echo '<pre>';
	// print_r($_SESSION);
	$sentencia = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		" . $resultado . "
		WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
		" . $filtro_usuario . "
		" . $filtro . "
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		LIMIT $inicio, $registros_por_pagina
		";


	$sentencia1 = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		" . $resultado . "
		WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
		" . $filtro_usuario . "
		" . $filtro . "
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		";


	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	$query1 = mysqli_query($connect_okrs, $sentencia1);
	$registros = mysqli_num_rows($query1);
	while ($data = mysqli_fetch_array($query)) {


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
				"nombre_owner" => $data["nombre_owner"]
			);
			array_push($nodos, $obj);
		}
	}
	$totalRegistros = array("registros" => $registros);
	array_push($nodos, $totalRegistros);
	return $nodos;
}

function OkrsUsuarioIndividual($id_empleado, $id_empresa, $connect_okrs, $filtro_periodo)
{

	$filtro = "";
	if ($_SESSION["equipo_fill"] > 0) {
		$filtro .= " AND Okrs_Equipos.id_okrs = '" . $_SESSION["equipo_fill"] . "' ";
	}

	if ($_SESSION["anio_fill"] > 0) {
		$filtro .= " AND Okrs.anio = " . $_SESSION["anio_fill"] . " ";
	}

	if ($_SESSION["areas_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Areas.id_area = '" . $_SESSION["areas_fill_okr"] . "'  ";
	}

	if ($_SESSION["tipo_fill_okr"] > 0) {
		$filtro .= " AND Okrs.tipo = '" . $_SESSION["tipo_fill_okr"] . "'  ";
	}

	if ($_SESSION["objestrategico_fill"] > 0) {
		$filtro .= " AND Okrs.objetivos_estrategicos IN ('" . $_SESSION["objestrategico_fill"] . "')  ";
	}

	if ($filtro_periodo) {
		$filtro .= " AND Okrs_Resultados.periodo IN ($filtro_periodo)";
	}

	if ($_SESSION["vicepresidencia_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Vicepresidencia.id_vicepresidencia = " . $_SESSION["vicepresidencia_fill_okr"] . "  ";
	}

	$resultado = "";
	$sentencia = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		" . $resultado . "
		WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
 		AND CONCAT(',', Okrs_Resultados.responsables, ',' ) LIKE '%,".$_SESSION['id_user'].",%'
		" . $filtro . "
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		";

	$nodos = array();
	if (!$connect_okrs) {
    die("Error de conexión: " . mysqli_connect_error());
}
	$query = mysqli_query($connect_okrs, $sentencia);
		if (!$query) {
			die("Error en la consulta: " . mysqli_error($connect_okrs));
		}
	while ($data = mysqli_fetch_array($query)) {

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

function OkrsUsuarioIndividualPaginacion($id_empleado, $id_empresa, $connect_okrs, $inicio, $registros_por_pagina, $filtro_periodo)
{

	$filtro = "";
	if ($_SESSION["equipo_fill"] > 0) {
		$filtro .= " AND Okrs_Equipos.id_okrs = '" . $_SESSION["equipo_fill"] . "' ";
	}

	if ($_SESSION["anio_fill"] > 0) {
		$filtro .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
	}

	if ($_SESSION["areas_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Areas.id_area = '" . $_SESSION["areas_fill_okr"] . "'  ";
	}

	if ($_SESSION["tipo_fill_okr"] > 0) {
		$filtro .= " AND Okrs.tipo = '" . $_SESSION["tipo_fill_okr"] . "'  ";
	}

	if ($filtro_periodo) {
		$filtro .= " AND Okrs_Resultados.periodo IN ($filtro_periodo)";
	}

	if ($_SESSION["vicepresidencia_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Vicepresidencia.id_vicepresidencia = " . $_SESSION["vicepresidencia_fill_okr"] . "  ";
	}

	$resultado = "";

	$sentencia = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		" . $resultado . "
		WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
 		AND CONCAT(',',Okrs_Resultados.responsables,',') LIKE '%," . $_SESSION["id_user"]. ",%'
		" . $filtro . "
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		LIMIT $inicio, $registros_por_pagina
		";

	$sentencia1 = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		" . $resultado . "
		WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
		AND CONCAT(',',Okrs_Resultados.responsables,',') LIKE '%," . $_SESSION["id_user"]. ",%'
		" . $filtro . "
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		";

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	$query1 = mysqli_query($connect_okrs, $sentencia1);
	$registros = mysqli_num_rows($query1);
	while ($data = mysqli_fetch_array($query)) {

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
	$totalRegistros = array("registros" => $registros);
	array_push($nodos, $totalRegistros);
	return $nodos;
}

function OkrsUsuarioIndividualP($id_empleado, $id_empresa, $limit, $connect_okrs)
{

	$filtro = "";
	if ($_SESSION["equipo_fill"] > 0) {
		$filtro .= " AND Okrs_Equipos.id_okrs = '" . $_SESSION["equipo_fill"] . "' ";
	}

	if ($_SESSION["anio_fill"] > 0) {
		$filtro .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
	}

	if ($_SESSION["areas_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Areas.id_area = '" . $_SESSION["areas_fill_okr"] . "'  ";
	}

	if ($_SESSION["tipo_fill_okr"] > 0) {
		$filtro .= " AND Okrs.tipo = '" . $_SESSION["tipo_fill_okr"] . "'  ";
	}

	if ($_SESSION["vicepresidencia_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Vicepresidencia.id_vicepresidencia = " . $_SESSION["vicepresidencia_fill_okr"] . "  ";
	}

	$resultado = "";
	// if ($_SESSION["buscador_resultado"] != "") {
	// 	$resultado = "LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs";
	// 	$filtro .= " AND Okrs_Resultados.descripcion LIKE '%" . $_SESSION["buscador_resultado"] . "%'  ";
	// }
	// echo '<pre>';
	// print_r($_SESSION);
	$sentencia = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		" . $resultado . "
		WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
		AND Okrs_Equipos.id_empleado = '" . $id_empleado . "'
		AND (Okrs_Resultados.responsables LIKE '%," . $id_empleado . "%' OR Okrs_Resultados.responsables LIKE '%" . $id_empleado . ",%' OR Okrs_Resultados.responsables IN ('" . $id_empleado . "'))
		" . $filtro . "
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		$limit
		";

	// echo $sentencia;

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	while ($data = mysqli_fetch_array($query)) {

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

function OkrsUsuarioCelula($id_empleado, $id_empresa, $connect_okrs, $filtro_periodo)
{

	$filtro = "";
	if ($_SESSION["equipo_fill"] > 0) {
		$filtro .= " AND OE.id_okrs = '" . $_SESSION["equipo_fill"] . "' ";
	}

	if ($_SESSION["anio_fill"] > 0) {
		$filtro .= " AND O.anio = '" . $_SESSION["anio_fill"] . "' ";
	}

	if ($_SESSION["areas_fill_okr"] > 0) {
		$filtro .= " AND OA.id_area = '" . $_SESSION["areas_fill_okr"] . "'  ";
	}

	if ($_SESSION["tipo_fill_okr"] > 0) {
		$filtro .= " AND O.tipo = '" . $_SESSION["tipo_fill_okr"] . "'  ";
	}

	if ($_SESSION["vicepresidencia_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Vicepresidencia.id_vicepresidencia = " . $_SESSION["vicepresidencia_fill_okr"] . "  ";
	}

	if ($_SESSION["objestrategico_fill"] > 0) {
		$filtro .= " AND O.objetivos_estrategicos IN ('" . $_SESSION["objestrategico_fill"] . "')  ";
	}

	if ($filtro_periodo) {
		$filtro .= " AND Okrs_Resultados.periodo IN ($filtro_periodo)";
	}


	// if ($_SESSION["colaborador_fill_okr"] > 0) {
	$filtro_usuario = " AND OE.id_empleado = '" . $_SESSION["id_user"] . "'  ";
	// $filtro_usuario .= " AND (Okrs_Resultados.responsables LIKE '%" . $_SESSION["id_user"] . ",%' OR responsables LIKE '%," . $_SESSION["id_user"] . ",%' OR Okrs_Resultados.responsables IN ('" . $_SESSION["id_user"] . "'))  ";
	//$filtro .= " AND Okrs.id_empleado = '".$_SESSION["colaborador_fill_okr"]."' ";
	//$filtro .= " AND Okrs_Equipos.id_empleado = '".$_SESSION["colaborador_fill_okr"]."'  ";
	// }else{
	// $filtro_usuario = 'AND OE.id_empleado = '.$id_empleado;
	// }

	// echo '<pre>';
	// print_r($_SESSION);
	$sentencia = "
				SELECT OE.id AS id, OE.id_empresa AS id_empresa,
				OE.id_empleado AS id_empleado,
				OE.id_okrs AS id_okrs, O.objetivo_okr AS objetivo_okr,
				O.fecha_inicia AS fecha_inicia, O.fecha_termina AS fecha_termina,
				O.tipo AS tipo, O.periodo AS periodo,
				O.objetivos_estrategicos AS objetivos_estrategicos,
				O.anio AS anio,
				OE.tipo AS tipo_role,
				E.nombre AS nombre_empleado,
				O.id_empleado AS id_owner, EO.nombre AS nombre_owner
				FROM Equipos_Views EV
				LEFT JOIN Okrs_Equipos OE ON OE.id_okrs = EV.id_okrs
				LEFT JOIN Okrs O ON O.id = OE.id_okrs
				LEFT JOIN Okrs_Areas AS OA ON OA.id_okrs = OE.id_okrs
				LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = O.id
				LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = OE.id_okrs
				LEFT JOIN puntacana_admin.Empleados AS E ON E.id = OE.id_empleado
				LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = O.id_empleado
				WHERE EV.id_empresa = $id_empresa
				$filtro
				$filtro_usuario
				GROUP BY O.id
				ORDER BY O.objetivo_okr ASC;
		";
	// echo $sentencia;

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	while ($data = mysqli_fetch_array($query)) {

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

function OkrsUsuarioCelulaPaginacion($id_empleado, $id_empresa, $connect_okrs, $inicio, $registros_por_pagina, $filtro_periodo)
{

	$filtro = "";
	if ($_SESSION["equipo_fill"] > 0) {
		$filtro .= " AND OE.id_okrs = '" . $_SESSION["equipo_fill"] . "' ";
	}

	if ($_SESSION["anio_fill"] > 0) {
		$filtro .= " AND O.anio = '" . $_SESSION["anio_fill"] . "' ";
	}

	if ($_SESSION["areas_fill_okr"] > 0) {
		$filtro .= " AND OA.id_area = '" . $_SESSION["areas_fill_okr"] . "'  ";
	}

	if ($_SESSION["tipo_fill_okr"] > 0) {
		$filtro .= " AND O.tipo = '" . $_SESSION["tipo_fill_okr"] . "'  ";
	}

	if ($_SESSION["vicepresidencia_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Vicepresidencia.id_vicepresidencia = " . $_SESSION["vicepresidencia_fill_okr"] . "  ";
	}

	if ($_SESSION["objestrategico_fill"] > 0) {
		$filtro .= " AND O.objetivos_estrategicos IN ('" . $_SESSION["objestrategico_fill"] . "')  ";
	}

	if ($filtro_periodo) {
		$filtro .= " AND Okrs_Resultados.periodo IN ($filtro_periodo)";
	}

	// if ($_SESSION["colaborador_fill_okr"] > 0) {
	$filtro_usuario = " AND OE.id_empleado = '" . $_SESSION["id_user"] . "'  ";
	// $filtro_usuario .= " AND (Okrs_Resultados.responsables LIKE '%" . $_SESSION["id_user"] . ",%' OR responsables LIKE '%," . $_SESSION["id_user"] . ",%' OR Okrs_Resultados.responsables IN ('" . $_SESSION["id_user"] . "'))  ";
	//$filtro .= " AND Okrs.id_empleado = '".$_SESSION["colaborador_fill_okr"]."' ";
	//$filtro .= " AND Okrs_Equipos.id_empleado = '".$_SESSION["colaborador_fill_okr"]."'  ";
	// }else{
	// $filtro_usuario = 'AND OE.id_empleado = '.$id_empleado;
	// }


	// echo '<pre>';
	// print_r($_SESSION);
	$sentencia = "
				SELECT OE.id AS id, OE.id_empresa AS id_empresa,
				OE.id_empleado AS id_empleado,
				OE.id_okrs AS id_okrs, O.objetivo_okr AS objetivo_okr,
				O.fecha_inicia AS fecha_inicia, O.fecha_termina AS fecha_termina,
				O.tipo AS tipo, O.periodo AS periodo,
				O.objetivos_estrategicos AS objetivos_estrategicos,
				O.anio AS anio,
				OE.tipo AS tipo_role,
				E.nombre AS nombre_empleado,
				O.id_empleado AS id_owner, EO.nombre AS nombre_owner
				FROM Equipos_Views EV
				LEFT JOIN Okrs_Equipos OE ON OE.id_okrs = EV.id_okrs
				LEFT JOIN Okrs O ON O.id = OE.id_okrs
				LEFT JOIN Okrs_Areas AS OA ON OA.id_okrs = OE.id_okrs
				LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = O.id
				LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = OE.id_okrs
				LEFT JOIN puntacana_admin.Empleados AS E ON E.id = OE.id_empleado
				LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = O.id_empleado
				WHERE EV.id_empresa = $id_empresa

				" . $filtro . "
				$filtro_usuario
				GROUP BY O.id
				ORDER BY O.objetivo_okr ASC
				LIMIT $inicio, $registros_por_pagina;
		";

	// echo $sentencia;

	$sentencia1 = "
				SELECT OE.id AS id, OE.id_empresa AS id_empresa,
				OE.id_empleado AS id_empleado,
				OE.id_okrs AS id_okrs, O.objetivo_okr AS objetivo_okr,
				O.fecha_inicia AS fecha_inicia, O.fecha_termina AS fecha_termina,
				O.tipo AS tipo, O.periodo AS periodo,
				O.objetivos_estrategicos AS objetivos_estrategicos,
				O.anio AS anio,
				OE.tipo AS tipo_role,
				E.nombre AS nombre_empleado,
				O.id_empleado AS id_owner, EO.nombre AS nombre_owner
				FROM Equipos_Views EV
				LEFT JOIN Okrs_Equipos OE ON OE.id_okrs = EV.id_okrs
				LEFT JOIN Okrs O ON O.id = OE.id_okrs
				LEFT JOIN Okrs_Areas AS OA ON OA.id_okrs = OE.id_okrs
				LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = O.id
				LEFT JOIN puntacana_admin.Empleados AS E ON E.id = OE.id_empleado
				LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = O.id_empleado
				WHERE EV.id_empresa = $id_empresa

				" . $filtro . "
				$filtro_usuario
				GROUP BY O.id
				ORDER BY O.objetivo_okr ASC;
		";

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	$query1 = mysqli_query($connect_okrs, $sentencia1);
	$registros = mysqli_num_rows($query1);
	while ($data = mysqli_fetch_array($query)) {

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
	$totalRegistros = array("registros" => $registros);
	array_push($nodos, $totalRegistros);
	return $nodos;
}

function OkrsUsuarioIniciativas($id_empleado, $id_empresa, $connect_okrs, $filtro_periodo)
{

	$filtro = "";
	if ($_SESSION["equipo_fill"] > 0) {
		$filtro .= " AND Okrs_Equipos.id_okrs = '" . $_SESSION["equipo_fill"] . "' ";
	}

	if ($_SESSION["anio_fill"] > 0) {
		$filtro .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
	}

	if ($_SESSION["areas_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Areas.id_area = '" . $_SESSION["areas_fill_okr"] . "'  ";
	}

	if ($_SESSION["tipo_fill_okr"] > 0) {
		$filtro .= " AND Okrs.tipo = '" . $_SESSION["tipo_fill_okr"] . "'  ";
	}

	if ($_SESSION["objestrategico_fill"] > 0) {
		$filtro .= " AND Okrs.objetivos_estrategicos IN ('" . $_SESSION["objestrategico_fill"] . "')  ";
	}

	if ($filtro_periodo) {
		$filtro .= " AND Okrs_Resultados.periodo IN ($filtro_periodo)";
	}

	if ($_SESSION["vicepresidencia_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Vicepresidencia.id_vicepresidencia = " . $_SESSION["vicepresidencia_fill_okr"] . "  ";
	}

	$resultado = "";
	if ($_SESSION["buscador_resultado"] != "") {
		$resultado = "LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs";
		$filtro .= " AND Okrs_Resultados.descripcion LIKE '%" . $_SESSION["buscador_resultado"] . "%'  ";
	}
	// echo '<pre>';
	// print_r($_SESSION);
	$sentencia = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Iniciativas ON Okrs_Iniciativas.id_resultado = Okrs_Resultados.id
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		$resultado
		WHERE Okrs_Equipos.id_empresa = $id_empresa
		AND Okrs_Equipos.id_empleado = $id_empleado
		AND (Okrs_Iniciativas.responsables LIKE '%," . $id_empleado . "%' OR Okrs_Iniciativas.responsables LIKE '%" . $id_empleado . ",%' OR Okrs_Iniciativas.responsables IN (" . $id_empleado . "))
		$filtro
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		";

	// echo $sentencia;

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	while ($data = mysqli_fetch_array($query)) {

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

function OkrsUsuarioIniciativasPagina($id_empleado, $id_empresa, $connect_okrs, $inicio, $registros_por_pagina, $filtro_periodo)
{

	$filtro = "";
	if ($_SESSION["equipo_fill"] > 0) {
		$filtro .= " AND Okrs_Equipos.id_okrs = '" . $_SESSION["equipo_fill"] . "' ";
	}

	if ($_SESSION["anio_fill"] > 0) {
		$filtro .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
	}

	if ($_SESSION["areas_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Areas.id_area = '" . $_SESSION["areas_fill_okr"] . "'  ";
	}

	if ($_SESSION["tipo_fill_okr"] > 0) {
		$filtro .= " AND Okrs.tipo = '" . $_SESSION["tipo_fill_okr"] . "'  ";
	}

	if ($_SESSION["objestrategico_fill"] > 0) {
		$filtro .= " AND Okrs.objetivos_estrategicos IN ('" . $_SESSION["objestrategico_fill"] . "')  ";
	}

	if ($filtro_periodo) {
		$filtro .= " AND Okrs_Resultados.periodo IN ($filtro_periodo)";
	}

	if ($_SESSION["vicepresidencia_fill_okr"] > 0) {
		$filtro .= " AND Okrs_Vicepresidencia.id_vicepresidencia = " . $_SESSION["vicepresidencia_fill_okr"] . "  ";
	}

	$resultado = "";
	if ($_SESSION["buscador_resultado"] != "") {
		$resultado = "LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs";
		$filtro .= " AND Okrs_Resultados.descripcion LIKE '%" . $_SESSION["buscador_resultado"] . "%'  ";
	}
	// echo '<pre>';
	// print_r($_SESSION);
	$sentencia = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Iniciativas ON Okrs_Iniciativas.id_resultado = Okrs_Resultados.id
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		$resultado
		WHERE Okrs_Equipos.id_empresa = $id_empresa
		AND Okrs_Equipos.id_empleado = $id_empleado
		AND (Okrs_Iniciativas.responsables LIKE '%," . $id_empleado . "%' Okrs_Iniciativas.responsables LIKE '%" . $id_empleado . ",%' OR Okrs_Iniciativas.responsables IN (" . $id_empleado . "))
		$filtro
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		LIMIT $inicio, $registros_por_pagina
		";

	$sentencia1 = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Iniciativas ON Okrs_Iniciativas.id_resultado = Okrs_Resultados.id
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		$resultado
		WHERE Okrs_Equipos.id_empresa = $id_empresa
		AND Okrs_Equipos.id_empleado = $id_empleado
		AND (Okrs_Iniciativas.responsables LIKE '%," . $id_empleado . "%' Okrs_Iniciativas.responsables LIKE '%" . $id_empleado . ",%' OR Okrs_Iniciativas.responsables IN (" . $id_empleado . "))
		$filtro
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		";

	// echo $sentencia;

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	$query1 = mysqli_query($connect_okrs, $sentencia1);
	$registros = mysqli_num_rows($query1);
	while ($data = mysqli_fetch_array($query)) {

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
	$totalRegistros = array("registros" => $registros);
	array_push($nodos, $totalRegistros);
	return $nodos;
}

function array_sort($array, $on, $order = SORT_ASC)
{
	$new_array = array();
	$sortable_array = array();

	if (count($array) > 0) {
		foreach ($array as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $k2 => $v2) {
					if ($k2 == $on) {
						$sortable_array[$k] = $v2;
					}
				}
			} else {
				$sortable_array[$k] = $v;
			}
		}

		switch ($order) {
			case SORT_ASC:
				asort($sortable_array);
				break;
			case SORT_DESC:
				arsort($sortable_array);
				break;
		}

		foreach ($sortable_array as $k => $v) {
			array_push($new_array, $array[$k]);
		}
	}

	return $new_array;
}

function EscalaColor($porcentaje, $id_empresa, $connect_valentina)
{

	$queryEscala = mysqli_query($connect_valentina, "SELECT * FROM Escala_Medicion WHERE id_empresa = $id_empresa");
	$dataEscala = mysqli_fetch_array($queryEscala);

	$color_bg = "#FF0000";
	$color_text = "#000000";
	$escala = array();
	// echo $porcentaje;
	if ($porcentaje >= $dataEscala['porcentaje_uno'] && $porcentaje < $dataEscala['porcentaje_tres']) {
		$color_bg = "#FF0000";
		$txt_subtitulo = $dataEscala['subtitulo_uno'];
		$color_text = "#F7F7F7";
		$medicion = 1;
	}
	if ($porcentaje >= $dataEscala['porcentaje_tres'] && $porcentaje < $dataEscala['porcentaje_cinco']) {
		$color_bg = "#FFF200";
		$txt_subtitulo = $dataEscala['subtitulo_dos'];
		$medicion = 2;
	}
	if ($porcentaje >= $dataEscala['porcentaje_cinco'] && $porcentaje < $dataEscala['porcentaje_siete']) {
		$color_bg = "#95FA03";
		$txt_subtitulo = $dataEscala['subtitulo_tres'];
		$medicion = 3;
	}
	if ($porcentaje >= $dataEscala['porcentaje_siete'] && $porcentaje <= 100) {
		$color_bg = "#14F209";
		$txt_subtitulo = $dataEscala['subtitulo_cuatro'];
		$medicion = 4;
	}
	// if( $porcentaje == 100 ){
	// 	$color_bg = "#0DF205";
	// 	$txt_subtitulo = $dataEscala['subtitulo_cinco'];
	// }
	if ($porcentaje > 100) {
		$color_bg = "#00D30A";
		$txt_subtitulo = $dataEscala['subtitulo_cinco'];
		$medicion = 5;
	}

	$escala['color_bg'] = $color_bg;
	$escala['color_text'] = $color_text;
	$escala['txt_subtitulo'] = $txt_subtitulo;
	$escala['medicion'] = $medicion;

	return $escala;
}

function PorOkrsAreas($id_okr, $connect_okrs, $filtro)
{
	$suma_resultado = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;
	$queryOKRs = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = '" . $id_okr . "' $filtro");
	// echo "SELECT * FROM Okrs WHERE id = '".$id_okr."' $filtro <br>";
	while ($dataOkr = mysqli_fetch_array($queryOKRs)) {
		$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs =  '" . $id_okr . "'");
		// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs =  '".$id_okr."' <br>";
		while ($dataResultados = mysqli_fetch_array($queryResultados)) {

			//TENDENCIA ASCENTENTE :: POR DEFECTO
			$porcentaje = ($dataResultados["avance"] * 100) / $dataResultados["meta"];
			if ($dataResultados["tendencia"] == 2) {
				//TENDENCIA DESCENDENTE
				$porcentaje = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
			}
			if (is_infinite($porcentaje)) {
				$porcentaje = 0;
			}

			$suma_resultado += $porcentaje;
			$conteo_resultado++;
		}
	}



	if ($suma_resultado > 0) {
		$resultado_prom_okr = ($suma_resultado / $conteo_resultado);
	} else {
		$resultado_prom_okr += 0;
	}

	$nodo = array(
		"promedio" => round($resultado_prom_okr),
		"no_resultados" => $queryResultados->num_rows
	);
	return $nodo;
}

function OkrsPorArea($id_area, $request, $connect_okrs, $filtro)
{
	$suma = 0;
	$conteo = 0;
	$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Areas
		WHERE id_area = '" . $id_area . "' GROUP BY id_okrs ");
	while ($dataOkrsNum = mysqli_fetch_array($query)) {
		$respuesta = PorOkrsAreas($dataOkrsNum["id_okrs"], $connect_okrs, $filtro);
		$suma += $respuesta["promedio"];
		$conteo++;
	}
	$promedio = $suma / $conteo;

	$nodo = array(
		"promedio" => $promedio,
		"no_okrs" => $conteo
	);
	return $nodo;
}

function PorOkrsConsolidados($id_okr, $request, $connect_okrs)
{
	$suma_resultado = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;
	$responsables = "";
	$filtro = $request ? $request : "";

	$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = '" . $id_okr . "' $filtro  ");
	// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs = '".$id_okr."' $filtro  <br>";
	while ($dataResultados = mysqli_fetch_array($queryResultados)) {

		//TENDENCIA ASCENTENTE :: POR DEFECTO
		$porcentaje = ($dataResultados["avance"] * 100) / $dataResultados["meta"]; 

        if($dataResultados["meta"] < 0){
            if( $dataResultados["avance"] > $dataResultados["meta"] ){
                $porcentaje = 100;
            }
        }

		$nombreOkrs = $dataResultados['descripcion'];
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

		$responsables .= $dataResultados["responsables"] . ",";
	}

	// if ($suma_resultado > 0) {
	// 	$resultado_prom_okr = ($suma_resultado / $conteo_resultado);
	// } else {
	// 	$resultado_prom_okr += 0;
	// }

	$resultado_prom_okr = ($suma_resultado / $conteo_resultado);

	if (is_nan($resultado_prom_okr) || is_infinite($resultado_prom_okr)) {
		$resultado_prom_okr = 0;
	}

	$nodo = array(
		"promedio" => round($resultado_prom_okr),
		"no_resultados" => $queryResultados->num_rows,
		"team"  => $responsables,
		'nombreOkrs' => $nombreOkrs
	);

	return $nodo;
}

function PorOkrsConsolidadosEquipo($id_okr, $request, $filtro_usuario_kr, $connect_okrs)
{

	$suma_resultado = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;
	$responsables = "";
	$filtro = $request ? $request : "";

	$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = '" . $id_okr . "' $filtro  ");
	// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs = '" . $id_okr . "' $filtro  ";
	while ($dataResultados = mysqli_fetch_array($queryResultados)) {

		//TENDENCIA ASCENTENTE :: POR DEFECTO
		$porcentaje = ($dataResultados["avance"] * 100) / $dataResultados["meta"];
		$nombreOkrs = $dataResultados['descripcion'];
		if ($dataResultados["tendencia"] == 2) {
			$porcentaje = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
		}


		if (is_infinite($porcentaje)) {
			$porcentaje = 0;
		}

		$suma_resultado += $porcentaje;
		$conteo_resultado++;

		$responsables .= $dataResultados["responsables"] . ",";
	}

	if ($suma_resultado > 0) {
		$resultado_prom_okr = ($suma_resultado / $conteo_resultado);
	} else {
		$resultado_prom_okr += 0;
	}

	$nodo = array(
		"promedio" => round($resultado_prom_okr),
		"no_resultados" => $queryResultados->num_rows,
		"team"  => $responsables,
		'nombreOkrs' => $nombreOkrs
	);


	return $nodo;
}

//POR AREAS
function OkrsPorAreaConsolidados($id_area, $request, $connect_okrs)
{

	$promedio = 0;
	$suma = 0;
	$conteo = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = "";
	$filtro_rol = "";

	// if ($_SESSION['role_plataforma'] == 2) {
	// 	$filtro_rol .= " AND Okrs_Equipos.id_empleado = " . $_SESSION['id_user'] . "";
	// }

	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr = $request;
	}
	$okr = "";
	// $query = mysqli_query($connect_okrs,"SELECT OA.id_okrs AS id_okrs FROM Okrs_Areas OA INNER JOIN Okrs O ON O.id = OA.id_okrs
	// WHERE OA.id_area = '".$id_area."' AND O.anio = '".$_SESSION["anio_fill"]."' $filtro_rol GROUP BY OA.id_okrs ");

	$query = mysqli_query($connect_okrs, "SELECT Okrs.*
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		WHERE Okrs_Equipos.id_empresa = '" . $_SESSION["id_empresa"] . "' AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' AND Okrs_Areas.id_area = $id_area AND Okrs.tipo = 2
		GROUP BY Okrs.id ORDER BY Okrs.objetivo_okr ASC");

	// echo $filtro_kr."<br>";

	if (mysqli_num_rows($query) > 0) {
		while ($dataOkrsNum = mysqli_fetch_array($query)) {
			$owner = $dataOkrsNum['id_empleado'];
			$okr .= $dataOkrsNum['id'] . ",";
			$respuesta = PorOkrsConsolidados($dataOkrsNum["id"], $filtro_kr, $connect_okrs);
			$suma += $respuesta["promedio"];
			$conteo++;
			$responsables .= $respuesta["team"] . ",";
		}

		$promedio = $suma / $conteo;
	}

	$okr = trim($okr, ',');

	$nodo = array(
		"promedio" => $promedio,
		"no_okrs" => $conteo,
		"team" => $responsables,
		"owner" => $owner,
		"okr" => $okr
	);

	return $nodo;
}

function OkrsPorAreaConsolidadosEquipo($id_area, $request, $connect_okrs, $connect_valentina, $filtro_periodo, $id_user)
{

	$promedio = 0;
	$suma = 0;
	$conteo = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = $filtro_usuario = "";
	$filtro_rol = "";



	$queryEE = mysqli_query($connect_valentina, "SELECT DISTINCT(vicepresidencia) AS id_vp FROM Estructura_Empresa WHERE area = $id_area");
	$dataEE = mysqli_fetch_array($queryEE);
	$id_vp = $dataEE["id_vp"];

	// if ($_SESSION['role_plataforma'] == 2) {
	// 	$filtro_rol .= " AND Okrs_Equipos.id_empleado = " . $_SESSION['id_user'] . "";
	// }

	if ($_SESSION["colaborador_fill_equipo"] > 0) {
		$filtro_usuario = " AND Okrs_Equipos.id_empleado = '" . $_SESSION["colaborador_fill_equipo"] . "'  ";
		$filtro_usuario .= " AND (Okrs_Resultados.responsables LIKE '%," . $_SESSION["colaborador_fill_equipo"] . "%' OR Okrs_Resultados.responsables LIKE '%" . $_SESSION["colaborador_fill_equipo"] . ",%' OR Okrs_Resultados.responsables IN ('" . $_SESSION["colaborador_fill_equipo"] . "'))  ";
		//$filtro .= " AND Okrs.id_empleado = '".$_SESSION["colaborador_fill_okr"]."' ";
		//$filtro .= " AND Okrs_Equipos.id_empleado = '".$_SESSION["colaborador_fill_okr"]."'  ";
	}

	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr = $request;
	}
	$okr = "";
	// $query = mysqli_query($connect_okrs,"SELECT OA.id_okrs AS id_okrs FROM Okrs_Areas OA INNER JOIN Okrs O ON O.id = OA.id_okrs
	// WHERE OA.id_area = '".$id_area."' AND O.anio = '".$_SESSION["anio_fill"]."' $filtro_rol GROUP BY OA.id_okrs ");

	// $query = mysqli_query($connect_okrs, "SELECT Okrs.*
	// 	FROM Okrs_Equipos
	// 	LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
	// 	LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
	// 	LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
	// 	LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
	// 	WHERE Okrs_Equipos.id_empresa = '" . $_SESSION["id_empresa"] . "' AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' AND (Okrs_Areas.id_area = '$id_area' OR Okrs_Vicepresidencia.id_vicepresidencia = '$id_vp') $filtro_usuario
	// 	GROUP BY Okrs.id ORDER BY Okrs.objetivo_okr ASC");

	// $query = mysqli_query($connect_okrs, "SELECT Okrs.*
	// 	FROM Okrs_Equipos
	// 	LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
	// 	LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
	// 	LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
	// 	LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
	// 	WHERE Okrs_Equipos.id_empresa = '" . $_SESSION["id_empresa"] . "' AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' AND Okrs_Areas.id_area = '$id_area' $filtro_usuario
	// 	GROUP BY Okrs.id ORDER BY Okrs.objetivo_okr ASC");

	// $queryLV = mysqli_query($connect_valentina, "SELECT * FROM Lideres_Vicepresidencia WHERE id_lider = '$id_user' AND estado = 1");
	// if (mysqli_num_rows($queryLV) > 0) {
	// 	$dataLV = mysqli_fetch_array($queryLV);
	// 	$query = mysqli_query($connect_okrs, "SELECT Okrs.*
	// 	FROM Okrs_Equipos
	// 	LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
	// 	LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
	// 	LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
	// 	LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
	// 	WHERE Okrs_Equipos.id_empresa = '" . $_SESSION["id_empresa"] . "' AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' AND Okrs_Vicepresidencia.id_vicepresidencia = '" . $dataLV["id_vicepresidencia"] . "' $filtro_usuario
	// 	GROUP BY Okrs.id ORDER BY Okrs.objetivo_okr ASC");
	// } else {
	$queryLA = mysqli_query($connect_valentina, "SELECT * FROM Lideres_Area WHERE id_lider = '$id_user' AND estado = 1");
	$dataLA = mysqli_fetch_array($queryLA);
	$query = mysqli_query($connect_okrs, "SELECT Okrs.*
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
		WHERE Okrs_Equipos.id_empresa = '" . $_SESSION["id_empresa"] . "' AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' AND Okrs_Areas.id_area = '$id_area' $filtro_usuario
		GROUP BY Okrs.id ORDER BY Okrs.objetivo_okr ASC");
	// }


	// echo $filtro_kr."<br>";

	if (mysqli_num_rows($query) > 0) {
		while ($dataOkrsNum = mysqli_fetch_array($query)) {
			$owner = $dataOkrsNum['id_empleado'];
			$okr .= $dataOkrsNum['id'] . ",";
			$respuesta = PorOkrsConsolidados($dataOkrsNum["id"], $filtro_kr, $connect_okrs);
			$suma += $respuesta["promedio"];
			$conteo++;
			$responsables .= $respuesta["team"] . ",";
		}

		$promedio = $suma / $conteo;
	}

	$okr = trim($okr, ',');

	$nodo = array(
		"promedio" => $promedio,
		"no_okrs" => $conteo,
		"team" => $responsables,
		"owner" => $owner,
		"okr" => $okr
	);

	return $nodo;
}

function OkrsPorNJConsolidados($id_nj, $request, $connect_okrs, $filtro_kr, $filtro)
{

	$promedio = 0;
	$suma = 0;
	$conteo = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = "";
	$filtro_rol = "";
	if ($request) {
		$filtro_kr = $request;
	}
	$okr = "";


	$query = mysqli_query($connect_okrs, "SELECT Okrs.*
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Resultados AS ORE ON ORE.id_okrs = Okrs.id
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		WHERE Okrs_Equipos.id_empresa = '" . $_SESSION["id_empresa"] . "' $filtro AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' AND Empleados.nivel_jerarquico = $id_nj $filtro
		GROUP BY Okrs.id ORDER BY Okrs.objetivo_okr ASC");

	// $query = mysqli_query($connect_okrs,"SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
	// Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
	// Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
	// Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
	// Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
	// Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner, Empleados.nivel_jerarquico AS nivel_jerarquico
	// FROM Okrs_Equipos
	// LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
	// LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
	// LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
	// LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
	// LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
	// WHERE Okrs_Equipos.id_empresa = " . $_SESSION["id_empresa"] . "
	// AND Okrs.anio = ".$_SESSION["anio_fill"]."
	// AND Empleados.nivel_jerarquico = $id_nj
	// $filtro
	// GROUP BY Okrs.id
	// ORDER BY Okrs.objetivo_okr ASC");

	$total = mysqli_num_rows($query);
	if (mysqli_num_rows($query) > 0) {
		while ($dataOkrsNum = mysqli_fetch_array($query)) {
			$owner = $dataOkrsNum['id_empleado'];
			$okr .= $dataOkrsNum['id'] . ",";
			$respuesta = PorOkrsConsolidados($dataOkrsNum["id"], $filtro_kr, $connect_okrs);
			if (is_nan($respuesta["promedio"])) {
				$promedioOkr = 0;
			} else {
				$promedioOkr = $respuesta["promedio"];
			}
			$suma += $promedioOkr;
			$conteo++;
			$responsables .= $respuesta["team"] . ",";
		}

		$promedio = $suma / $conteo;
	}

	$okr = trim($okr, ',');

	$nodo = array(
		"promedio" => $promedio,
		"no_okrs" => $conteo,
		"team" => $responsables,
		"owner" => $owner,
		"okr" => $okr,
		"total" => $total
	);

	return $nodo;
}


//POR AREAS
function OkrsPorObjetivosEstrategicosConsolidados($id_objetivo, $request, $connect_okrs)
{

	$suma = 0;
	$conteo = 0;
	$promedio = 0;

	$filtro_kr = " ";
	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr .= " AND periodo IN (" . $request . ") ";
	}
	// echo $filtro_kr;
	$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs
		WHERE objetivos_estrategicos LIKE '%" . $id_objetivo . "%'");

	if (mysqli_num_rows($query) > 0) {
		while ($dataOkrsNum = mysqli_fetch_array($query)) {
			$respuesta = PorOkrsConsolidados($dataOkrsNum["id"], $filtro_kr, $connect_okrs);

			$suma += $respuesta["promedio"];
			$conteo++;
		}
		$promedio = $suma / $conteo;
	}

	$nodo = array(
		"promedio" => $promedio,
		"no_okrs" => $conteo
	);
	return $nodo;
}

function PorOkrsReporteConsolidados($connect_valentina, $connect_okrs, $okr, $id_empresa, $filtro)
{

	$filtro_kr = "";
	if ($filtro) {
		$filtro_kr .= " AND periodo IN (" . $filtro . ") ";
	}

	$suma_resultado = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;
	$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = '" . $okr . "'  " . $filtro_kr . " ");
	// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs = '".$okr."'  ".$filtro_kr."<br> ";
	while ($dataResultados = mysqli_fetch_array($queryResultados)) {
		$porcentaje = ($dataResultados["avance"] * 100) / $dataResultados["meta"];

		if ($dataResultados["tendencia"] == 2) {
			$porcentaje = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
		}

		if (is_infinite($porcentaje)) {
			$porcentaje = 0;
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

function OkrsEmpresaReporteConsolidado($id_empresa, $connect_okrs)
{

	$filtro = "";
	if ($_SESSION["anio_fill"] > 0) {
		$filtro .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
	}

	$sentencia = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
		" . $filtro . "
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		";

	// echo $sentencia;

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	while ($data = mysqli_fetch_array($query)) {
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
		);
		array_push($nodos, $obj);
	}

	return $nodos;
}

function responsePhoto($id_empleado, $connect_valentina)
{
	$queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id  = $id_empleado ");
	while ($data = mysqli_fetch_array($queryEmpleado)) {
		$photo = $data["foto"];
		$nombre = $data["nombre"];
		$id = $data["id"];
	}
	$nodo = array(
		"nombre" => $nombre,
		"foto" => $photo,
		"id" => $id
	);
	return $nodo;
}

function PorOkrsMapa($connect_valentina, $connect_okrs, $okr, $id_empresa, $filtro)
{

	$filtro_kr = "";
	if ($filtro) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr .= " AND periodo IN (" . $filtro . ") ";
	}

	$suma_resultado = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;
	$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = '" . $okr . "'  " . $filtro . " ");

	while ($dataResultados = mysqli_fetch_array($queryResultados)) {
		$porcentaje = ($dataResultados["avance"] * 100) / $dataResultados["meta"];

		if ($dataResultados["tendencia"] == 2) {
			$porcentaje = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
		}

		if (is_infinite($porcentaje)) {
			$porcentaje = 0;
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

function OkrOrganizacional($id_okr, $id_empresa, $connect_okrs)
{

	$sentencia = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
		AND Okrs_Equipos.id_okrs = '" . $id_okr . "'
		AND Okrs.tipo = 1
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		";

	// echo $sentencia;


	$query = mysqli_query($connect_okrs, $sentencia);
	$data = mysqli_fetch_array($query);

	$nodos = array(
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

	return $nodos;
}

function PorOkrsOrganizacional($connect_okrs, $okr, $filtro)
{

	$filtro_kr = " ";
	if ($filtro) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr .= " AND periodo IN (" . $filtro . ") ";
	}

	$suma_resultado = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;
	$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = '" . $okr . "'  " . $filtro_kr . " ");
	// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs = '".$okr."'  ".$filtro_kr."<br> ";
	while ($dataResultados = mysqli_fetch_array($queryResultados)) {
		$porcentaje = ($dataResultados["avance"] * 100) / $dataResultados["meta"];

		if ($dataResultados["tendencia"] == 2) {
			$porcentaje = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
		}

		if (is_infinite($porcentaje)) {
			$porcentaje = 0;
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

function unique_multidim_array($array, $key)
{

	$temp_array = array();

	$i = 0;

	$key_array = array();

	foreach ($array as $val) {

		if (!in_array($val[$key], $key_array)) {

			$key_array[$i] = $val[$key];

			$temp_array[$i] = $val;
		}

		$i++;
	}

	return $temp_array;
}

function CargaLecciones($connect_clima, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $id_user)
{
	$array_celula = $array_empleado = $array_area = array();
	$cont_celula = $cont_empleado = $cont_area = 0;

	$query1 = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas WHERE celula LIKE '%" . $id_user . "%' $filtro_kr $filtro_claves $filtro_area AND estado = 1 ORDER BY fecha_inicia DESC");
	while ($dataquery1 = mysqli_fetch_array($query1)) {
		$array_celula[$cont_celula]["id"] = $dataquery1["id"];
		$array_celula[$cont_celula]["id_empresa"] = $dataquery1["id_empresa"];
		$array_celula[$cont_celula]["id_empleado"] = $dataquery1["id_empleado"];
		$array_celula[$cont_celula]["descripcion"] = $dataquery1["descripcion"];
		$array_celula[$cont_celula]["celula"] = $dataquery1["celula"];
		$array_celula[$cont_celula]["anio"] = $dataquery1["anio"];
		$array_celula[$cont_celula]["fecha_inicia"] = $dataquery1["fecha_inicia"];
		$array_celula[$cont_celula]["fecha_termina"] = $dataquery1["fecha_termina"];
		$array_celula[$cont_celula]["periodo"] = $dataquery1["periodo"];
		$array_celula[$cont_celula]["area"] = $dataquery1["area"];
		$array_celula[$cont_celula]["estado"] = $dataquery1["estado"];
		$array_celula[$cont_celula]["fecha_publicacion"] = $dataquery1["fecha_publicacion"];
		$array_celula[$cont_celula]["created_at"] = $dataquery1["created_at"];
		$cont_celula++;
	}



	$query2 = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas WHERE id_empleado = " . $id_user . " $filtro_kr $filtro_claves $filtro_area AND estado = 1 ORDER BY fecha_inicia DESC");
	while ($dataquery2 = mysqli_fetch_array($query2)) {
		$array_empleado[$cont_empleado]["id"] = $dataquery2["id"];
		$array_empleado[$cont_empleado]["id_empresa"] = $dataquery2["id_empresa"];
		$array_empleado[$cont_empleado]["id_empleado"] = $dataquery2["id_empleado"];
		$array_empleado[$cont_empleado]["descripcion"] = $dataquery2["descripcion"];
		$array_empleado[$cont_empleado]["celula"] = $dataquery2["celula"];
		$array_empleado[$cont_empleado]["anio"] = $dataquery2["anio"];
		$array_empleado[$cont_empleado]["fecha_inicia"] = $dataquery2["fecha_inicia"];
		$array_empleado[$cont_empleado]["fecha_termina"] = $dataquery2["fecha_termina"];
		$array_empleado[$cont_empleado]["periodo"] = $dataquery2["periodo"];
		$array_empleado[$cont_empleado]["area"] = $dataquery2["area"];
		$array_empleado[$cont_empleado]["estado"] = $dataquery2["estado"];
		$array_empleado[$cont_empleado]["fecha_publicacion"] = $dataquery2["fecha_publicacion"];
		$array_empleado[$cont_empleado]["created_at"] = $dataquery2["created_at"];
		$cont_empleado++;
	}

	$query3 = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas WHERE $filtro_la $filtro_kr $filtro_claves $filtro_area AND estado = 1 ORDER BY fecha_inicia DESC");
	while ($dataquery3 = mysqli_fetch_array($query3)) {
		$array_area[$cont_area]["id"] = $dataquery3["id"];
		$array_area[$cont_area]["id_empresa"] = $dataquery3["id_empresa"];
		$array_area[$cont_area]["id_empleado"] = $dataquery3["id_empleado"];
		$array_area[$cont_area]["descripcion"] = $dataquery3["descripcion"];
		$array_area[$cont_area]["celula"] = $dataquery3["celula"];
		$array_area[$cont_area]["anio"] = $dataquery3["anio"];
		$array_area[$cont_area]["fecha_inicia"] = $dataquery3["fecha_inicia"];
		$array_area[$cont_area]["fecha_termina"] = $dataquery3["fecha_termina"];
		$array_area[$cont_area]["periodo"] = $dataquery3["periodo"];
		$array_area[$cont_area]["area"] = $dataquery3["area"];
		$array_area[$cont_area]["estado"] = $dataquery3["estado"];
		$array_area[$cont_area]["fecha_publicacion"] = $dataquery3["fecha_publicacion"];
		$array_area[$cont_area]["created_at"] = $dataquery3["created_at"];
		$cont_area++;
	}

	$resultado = array_merge($array_celula, $array_empleado, $array_area);

	return $resultado;
}

function CargaLeccionesAdmin($connect_clima, $connect_valentina, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp)
{
	$array_celula = $array_empleado = $array_area = array();
	$cont_celula = $cont_empleado = $cont_area = 0;

	$query1 = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas WHERE estado = 1 $filtro_kr $filtro_claves $filtro_area $filtro_vp AND (id_okr_estrategico IS NULL OR id_okr_estrategico = '')
AND (id_okr_organizacional IS NULL OR id_okr_organizacional = '') AND (id_okr_equipo IS NULL OR id_okr_equipo = '')  AND (id_vp IS NULL OR id_vp = '') ORDER BY fecha_inicia DESC");
	while ($dataquery1 = mysqli_fetch_array($query1)) {
		$queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = " . $dataquery1["id_empleado"] . "");
		$dataEmpleado = mysqli_fetch_array($queryEmpleado);
		$array_celula[$cont_celula]["id"] = $dataquery1["id"];
		$array_celula[$cont_celula]["id_empresa"] = $dataquery1["id_empresa"];
		$array_celula[$cont_celula]["id_empleado"] = $dataquery1["id_empleado"];
		$array_celula[$cont_celula]["descripcion"] = $dataquery1["descripcion"];
		$array_celula[$cont_celula]["celula"] = $dataquery1["celula"];
		$array_celula[$cont_celula]["anio"] = $dataquery1["anio"];
		$array_celula[$cont_celula]["fecha_inicia"] = $dataquery1["fecha_inicia"];
		$array_celula[$cont_celula]["fecha_termina"] = $dataquery1["fecha_termina"];
		$array_celula[$cont_celula]["periodo"] = $dataquery1["periodo"];
		if ($dataquery1["area"] != "") {
			$array_celula[$cont_celula]["area"] = $dataquery1["area"];
		} else {
			$array_celula[$cont_celula]["area"] = $dataEmpleado["area"];
		}
		$array_celula[$cont_celula]["estado"] = $dataquery1["estado"];
		$array_celula[$cont_celula]["fecha_publicacion"] = $dataquery1["fecha_publicacion"];
		$array_celula[$cont_celula]["created_at"] = $dataquery1["created_at"];
		$cont_celula++;
	}

	return $array_celula;
}

function CargaLeccionesLider($connect_clima, $connect_valentina, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp, $id_empleado)
{
	$array_celula = $array_empleado = $array_area = array();
	$cont_celula = $cont_empleado = $cont_area = 0;

	$queryLA = mysqli_query($connect_valentina, "SELECT * FROM Lideres_Area WHERE id_area = '" . $_GET['id'] . "' AND estado = 1");
	$lider_area = "";
	$validar = false;
	if (mysqli_num_rows($queryLA) > 0) {
		while ($dataLA = mysqli_fetch_array($queryLA)) {
			if ($id_empleado == $dataLA["id_lider"]) {
				$validar = true;
			}
		}
	}

	if ($validar == true) {
		$lider_area = "(id_empleado = $id_empleado OR celula LIKE '%," . $id_empleado . "%' OR celula LIKE '%" . $id_empleado . ",%')";
	} else {
		$lider_area = "(celula LIKE '%," . $id_empleado . "%' OR celula LIKE '%" . $id_empleado . ",%')";
	}

	// echo "SELECT * FROM Lecciones_Aprendidas WHERE estado = 1 AND $lider_area $filtro_kr $filtro_claves $filtro_area $filtro_vp ORDER BY fecha_inicia DESC";

	$query1 = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas WHERE estado = 1 AND $lider_area $filtro_kr $filtro_claves $filtro_area $filtro_vp AND (id_okr_estrategico IS NULL OR id_okr_estrategico = '')
AND (id_okr_organizacional IS NULL OR id_okr_organizacional = '') AND (id_okr_equipo IS NULL OR id_okr_equipo = '') AND (id_vp IS NULL OR id_vp = '') ORDER BY fecha_inicia DESC");
	while ($dataquery1 = mysqli_fetch_array($query1)) {
		$queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = " . $dataquery1["id_empleado"] . "");
		$dataEmpleado = mysqli_fetch_array($queryEmpleado);
		$array_celula[$cont_celula]["id"] = $dataquery1["id"];
		$array_celula[$cont_celula]["id_empresa"] = $dataquery1["id_empresa"];
		$array_celula[$cont_celula]["id_empleado"] = $dataquery1["id_empleado"];
		$array_celula[$cont_celula]["descripcion"] = $dataquery1["descripcion"];
		$array_celula[$cont_celula]["celula"] = $dataquery1["celula"];
		$array_celula[$cont_celula]["anio"] = $dataquery1["anio"];
		$array_celula[$cont_celula]["fecha_inicia"] = $dataquery1["fecha_inicia"];
		$array_celula[$cont_celula]["fecha_termina"] = $dataquery1["fecha_termina"];
		$array_celula[$cont_celula]["periodo"] = $dataquery1["periodo"];
		if ($dataquery1["area"] == "") {
			$array_celula[$cont_celula]["area"] = $dataEmpleado["area"];
		} else {
			$array_celula[$cont_celula]["area"] = $dataquery1["area"];
		}
		if ($dataquery1["id_vp"] == "") {
			$array_celula[$cont_celula]["id_vp"] = $dataEmpleado["unidad_corporativa"];
		} else {
			$array_celula[$cont_celula]["id_vp"] = $dataquery1["id_vp"];
		}
		$array_celula[$cont_celula]["estado"] = $dataquery1["estado"];
		$array_celula[$cont_celula]["fecha_publicacion"] = $dataquery1["fecha_publicacion"];
		$array_celula[$cont_celula]["created_at"] = $dataquery1["created_at"];
		$cont_celula++;
	}

	return $array_celula;
}

function CargaLeccionesLiderInfo($connect_clima, $connect_valentina, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp, $id_empleado)
{
	$array_celula = $array_empleado = $array_area = array();
	$cont_celula = $cont_empleado = $cont_area = 0;
	// echo "SELECT * FROM Lecciones_Aprendidas WHERE estado = 1  $filtro_kr $filtro_claves $filtro_area $filtro_vp ORDER BY fecha_inicia DESC";
	$query1 = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas WHERE estado = 1  $filtro_kr $filtro_claves $filtro_area $filtro_vp ORDER BY fecha_inicia DESC");
	while ($dataquery1 = mysqli_fetch_array($query1)) {
		$queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = " . $dataquery1["id_empleado"] . "");
		$dataEmpleado = mysqli_fetch_array($queryEmpleado);
		$array_celula[$cont_celula]["id"] = $dataquery1["id"];
		$array_celula[$cont_celula]["id_empresa"] = $dataquery1["id_empresa"];
		$array_celula[$cont_celula]["id_empleado"] = $dataquery1["id_empleado"];
		$array_celula[$cont_celula]["descripcion"] = $dataquery1["descripcion"];
		$array_celula[$cont_celula]["celula"] = $dataquery1["celula"];
		$array_celula[$cont_celula]["anio"] = $dataquery1["anio"];
		$array_celula[$cont_celula]["fecha_inicia"] = $dataquery1["fecha_inicia"];
		$array_celula[$cont_celula]["fecha_termina"] = $dataquery1["fecha_termina"];
		$array_celula[$cont_celula]["periodo"] = $dataquery1["periodo"];
		if ($dataquery1["area"] == "") {
			$array_celula[$cont_celula]["area"] = $dataEmpleado["area"];
		} else {
			$array_celula[$cont_celula]["area"] = $dataquery1["area"];
		}
		if ($dataquery1["id_vp"] == "") {
			$array_celula[$cont_celula]["id_vp"] = $dataEmpleado["unidad_corporativa"];
		} else {
			$array_celula[$cont_celula]["id_vp"] = $dataquery1["id_vp"];
		}
		$array_celula[$cont_celula]["estado"] = $dataquery1["estado"];
		$array_celula[$cont_celula]["fecha_publicacion"] = $dataquery1["fecha_publicacion"];
		$array_celula[$cont_celula]["created_at"] = $dataquery1["created_at"];
		$cont_celula++;
	}

	return $array_celula;
}

function CargaLeccionesColaborador($connect_clima, $connect_valentina, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp, $id_empleado)
{
	$array_celula = $array_empleado = $array_area = array();
	$cont_celula = $cont_empleado = $cont_area = 0;

	$query1 = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas WHERE estado = 1 AND (celula LIKE '%," . $id_empleado . "%' OR celula LIKE '%" . $id_empleado . ",%') $filtro_kr $filtro_claves $filtro_area $filtro_vp  AND (id_okr_estrategico IS NULL OR id_okr_estrategico = '')
AND (id_okr_organizacional IS NULL OR id_okr_organizacional = '') AND (id_okr_equipo IS NULL OR id_okr_equipo = '')  AND (id_vp IS NULL OR id_vp = '') ORDER BY fecha_inicia DESC");
	while ($dataquery1 = mysqli_fetch_array($query1)) {
		$queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = " . $dataquery1["id_empleado"] . "");
		$dataEmpleado = mysqli_fetch_array($queryEmpleado);
		$array_celula[$cont_celula]["id"] = $dataquery1["id"];
		$array_celula[$cont_celula]["id_empresa"] = $dataquery1["id_empresa"];
		$array_celula[$cont_celula]["id_empleado"] = $dataquery1["id_empleado"];
		$array_celula[$cont_celula]["descripcion"] = $dataquery1["descripcion"];
		$array_celula[$cont_celula]["celula"] = $dataquery1["celula"];
		$array_celula[$cont_celula]["anio"] = $dataquery1["anio"];
		$array_celula[$cont_celula]["fecha_inicia"] = $dataquery1["fecha_inicia"];
		$array_celula[$cont_celula]["fecha_termina"] = $dataquery1["fecha_termina"];
		$array_celula[$cont_celula]["periodo"] = $dataquery1["periodo"];
		if ($dataquery1["area"] == "") {
			$array_celula[$cont_celula]["area"] = $dataEmpleado["area"];
		} else {
			$array_celula[$cont_celula]["area"] = $dataquery1["area"];
		}
		if ($dataquery1["id_vp"] == "") {
			$array_celula[$cont_celula]["id_vp"] = $dataEmpleado["unidad_corporativa"];
		} else {
			$array_celula[$cont_celula]["id_vp"] = $dataquery1["id_vp"];
		}
		$array_celula[$cont_celula]["estado"] = $dataquery1["estado"];
		$array_celula[$cont_celula]["fecha_publicacion"] = $dataquery1["fecha_publicacion"];
		$array_celula[$cont_celula]["created_at"] = $dataquery1["created_at"];
		$cont_celula++;
	}

	return $array_celula;
}

function CargaLeccionesColaboradorInfo($connect_clima, $connect_valentina, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp, $id_empleado)
{
	$array_celula = $array_empleado = $array_area = array();
	$cont_celula = $cont_empleado = $cont_area = 0;

	$query1 = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas WHERE estado = 1 AND (celula LIKE '%," . $id_empleado . "%' OR celula LIKE '%" . $id_empleado . ",%') $filtro_kr $filtro_claves $filtro_area $filtro_vp ORDER BY fecha_inicia DESC");
	while ($dataquery1 = mysqli_fetch_array($query1)) {
		$queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = " . $dataquery1["id_empleado"] . "");
		$dataEmpleado = mysqli_fetch_array($queryEmpleado);
		$array_celula[$cont_celula]["id"] = $dataquery1["id"];
		$array_celula[$cont_celula]["id_empresa"] = $dataquery1["id_empresa"];
		$array_celula[$cont_celula]["id_empleado"] = $dataquery1["id_empleado"];
		$array_celula[$cont_celula]["descripcion"] = $dataquery1["descripcion"];
		$array_celula[$cont_celula]["celula"] = $dataquery1["celula"];
		$array_celula[$cont_celula]["anio"] = $dataquery1["anio"];
		$array_celula[$cont_celula]["fecha_inicia"] = $dataquery1["fecha_inicia"];
		$array_celula[$cont_celula]["fecha_termina"] = $dataquery1["fecha_termina"];
		$array_celula[$cont_celula]["periodo"] = $dataquery1["periodo"];
		if ($dataquery1["area"] == "") {
			$array_celula[$cont_celula]["area"] = $dataEmpleado["area"];
		} else {
			$array_celula[$cont_celula]["area"] = $dataquery1["area"];
		}
		if ($dataquery1["id_vp"] == "") {
			$array_celula[$cont_celula]["id_vp"] = $dataEmpleado["unidad_corporativa"];
		} else {
			$array_celula[$cont_celula]["id_vp"] = $dataquery1["id_vp"];
		}
		$array_celula[$cont_celula]["estado"] = $dataquery1["estado"];
		$array_celula[$cont_celula]["fecha_publicacion"] = $dataquery1["fecha_publicacion"];
		$array_celula[$cont_celula]["created_at"] = $dataquery1["created_at"];
		$cont_celula++;
	}

	return $array_celula;
}

function OkrsReporteUsuario($id_empleado, $id_empresa, $connect_okrs, $filtro)
{

	$sentencia = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		WHERE Okrs_Equipos.id_empresa = $id_empresa
		AND Okrs_Equipos.id_empleado = $id_empleado
		AND (Okrs_Resultados.responsables LIKE '%," . $id_empleado . "%' OR Okrs_Resultados.responsables LIKE '%" . $id_empleado . ",%' OR Okrs_Resultados.responsables IN ('" . $id_empleado . "'))
		AND Okrs.anio = " . $_SESSION["anio_fill"] . "
		$filtro
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		";

	// echo $sentencia."<br>";

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	while ($data = mysqli_fetch_array($query)) {

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

function OkrsReporteUsuarioReporte($id_empleado, $id_empresa, $connect_okrs, $filtro, $anio)
{

	$sentencia = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		WHERE Okrs_Equipos.id_empresa = $id_empresa
		AND Okrs_Equipos.id_empleado = $id_empleado
		AND (Okrs_Resultados.responsables LIKE '%," . $id_empleado . "%' OR Okrs_Resultados.responsables LIKE '%" . $id_empleado . ",%' OR Okrs_Resultados.responsables IN ('" . $id_empleado . "'))
		AND Okrs.anio = " . $anio . "
		$filtro
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		";

	// echo $sentencia."<br>";

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	while ($data = mysqli_fetch_array($query)) {

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

function PorOkrsReporteUsuario($connect_okrs, $okr, $id_empleado, $filtro_claves)
{

	$filtro_kr = " ";
	if ($filtro_claves) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr .= $filtro_claves;
	}

	$suma_resultado = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;
	$porcentaje = 0;
	// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr  $filtro_kr ";
	// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr AND responsables LIKE '%$id_empleado%' $filtro_kr ";

	$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr AND (responsables LIKE '%,$id_empleado%' OR responsables LIKE '%$id_empleado,%' OR responsables IN ('$id_empleado')) $filtro_kr ");
	// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr AND (responsables LIKE '%,$id_empleado%' OR responsables LIKE '%$id_empleado,%' OR responsables IN ($id_empleado)) $filtro_kr ";

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

function PorOkrsReporteUsuarioDesempenio($connect_okrs, $okr, $id_empleado, $filtro_claves)
{

	$filtro_kr = " ";
	if ($filtro_claves) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr .= $filtro_claves;
	}

	$suma_resultado = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;
	$porcentaje = 0;
	// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr  $filtro_kr ";
	// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr AND responsables LIKE '%$id_empleado%' $filtro_kr ";

	$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr $filtro_kr ");
	// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr AND (responsables LIKE '%,$id_empleado%' OR responsables LIKE '%$id_empleado,%' OR responsables IN ($id_empleado)) $filtro_kr ";

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

function ComprobarOrden($connect_okrs, $id_okrs)
{
	$queryOrder = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = '" . $id_okrs . "' AND orden IS NULL");

	$registros = mysqli_num_rows($queryOrder);
	$contRes = 0;
	if ($registros > 0) {
		while ($dataRes = mysqli_fetch_array($queryOrder)) {
			$array_resultados[$contRes]['id'] = $dataRes['id'];
			$array_resultados[$contRes]['orden'] = $dataRes['orden'];
			$contRes++;
		}

		for ($i = 0; $i < count($array_resultados); $i++) {
			$j = $i + 1;
			mysqli_query($connect_okrs, "UPDATE Okrs_Resultados SET orden = $j WHERE id = '" . $array_resultados[$i]['id'] . "' ");
		}
	}
}

function MoverResultado($connect_okrs, $okr_fill, $id_registro, $id_empresa, $id_user)
{
	$hoy = date("Y-m-d H:i:s");
	$query1 = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id = '" . $id_registro . "' ");
	$data1 = mysqli_fetch_array($query1);

	$query2 = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $data1["id_okrs"] . "");
	$data2 = mysqli_fetch_array($query2);

	$query3 = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $okr_fill . "");
	$data3 = mysqli_fetch_array($query3);

	$accion = 'ACTUALIZAR';
	$descripcion = 'Movimiento de resultado clave: ' . $data1["descripcion"] . ' desde el OKR ' . $data2["objetivo_okr"] . ' al OKR ' . $data3["objetivo_okr"];
	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $id_empresa . ", " . $id_user . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $data1["id_okrs"] . "," . $id_registro . ",0,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);

	$query1 = mysqli_query($connect_okrs, "SELECT MAX(orden) AS orden FROM Okrs_Resultados WHERE id_okrs = '" . $okr_fill . "' ORDER BY orden");
	$data1 = mysqli_fetch_array($query1);

	$orden = $data1["orden"] + 1;

	$sentencia1 = "
			UPDATE Okrs_Resultados SET id_okrs = '" . $okr_fill . "', orden = $orden
			WHERE id = '" . $id_registro . "'
			";

	mysqli_query($connect_okrs, $sentencia1);

	$sentencia2 = "
			UPDATE Okrs_Iniciativas SET id_okrs = '" . $okr_fill . "'
			WHERE id_resultado = '" . $id_registro . "'
			";

	mysqli_query($connect_okrs, $sentencia2);

	$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id = '" . $id_registro . "' ");
	$data = mysqli_fetch_array($query);

	return $data["id_okrs"];
}

function DuplicarResultado($connect_okrs, $post, $id_okr, $hoy, $id_empresa, $id_user)
{
	ComprobarOrden($connect_okrs, $id_okr);

	$query1 = mysqli_query($connect_okrs, "SELECT MAX(orden) AS orden FROM Okrs_Resultados WHERE id_okrs = '" . $id_okr . "' ORDER BY orden");
	$data1 = mysqli_fetch_array($query1);

	$orden = $data1["orden"] + 1;

	$sentencia1 = "
			INSERT INTO Okrs_Resultados (id_empresa, id_okrs, id_empleado, responsables, descripcion, avance, fecha_inicia, fecha_entrega, tendencia, medicion, meta, meta_minimo, meta_maximo, periodo, estado, orden, created_at)
			VALUES ('" . $post["id_empresa"] . "', $id_okr, " . $post["id_owner"] . ", '" . implode(",", $post["responsables"]) . "', '" . $post["descripcion"] . "', '" . $post["avance"] . "', '" . $post["fecha_inicia"] . "', '" . $post["fecha_entrega"] . "', '" . $post["tendencia"] . "', '" . $post["medicion"] . "', '" . $post["meta"] . "', '" . $post["meta_minimo"] . "', '" . $post["meta_maximo"] . "', '" . $post["periodo"] . "', 1, $orden, '" . $hoy . "')
			";

	mysqli_query($connect_okrs, $sentencia1);
	$id_tmp = mysqli_insert_id($connect_okrs);

	$query3 = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id = '" . $id_tmp . "' ");
	$data3 = mysqli_fetch_array($query3);

	$query2 = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $id_okr . "");
	$data2 = mysqli_fetch_array($query2);

	$accion = 'CREAR';
	$descripcion = 'Creación de resultado clave: ' . $data3["descripcion"] . ' en el OKR ' . $data2["objetivo_okr"] . ' por la opción duplicar resultado';
	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $id_empresa . ", " . $id_user . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $id_okr . "," . $id_tmp . ",0,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);

	if ($post["decision"] == "1") {
		$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Iniciativas WHERE id_resultado = '" . $post["id_registro"] . "' ");
		if (mysqli_num_rows($query) > 0) {
			while ($data = mysqli_fetch_array($query)) {
				$sentencia2 = "
						INSERT INTO Okrs_Iniciativas (id_okrs, id_resultado, id_empleado, responsables, descripcion, fecha_entrega, meta, avance, tendencia, created_at)
						VALUES ('" . $data["id_okrs"] . "', $id_tmp, " . $post["id_owner"] . ", '" . $data["responsables"] . "', '" . $data["descripcion"] . "', '" . $data["fecha_entrega"] . "', '" . $data["meta"] . "', '" . $data["avance"] . "', '" . $data["tendencia"] . "', '" . $hoy . "')
						";

				mysqli_query($connect_okrs, $sentencia2);
			}
		}
	}
	return $id_tmp;
}

function ReubicarResultado($connect_okrs, $post, $id, $hoy, $id_empresa, $id_user)
{
	ComprobarOrden($connect_okrs, $post['id_okrs']);

	$sentencia1 = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id = '" . $post["id_registro"] . "'");
	$data1 = mysqli_fetch_array($sentencia1);

	$orden1 = $data1["orden"];

	$sentencia2 = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id = '" . $post["okr_fill"] . "'");
	$data2 = mysqli_fetch_array($sentencia2);

	$orden2 = $data2["orden"];

	if ($post["posicion"] == '3') {

		mysqli_query($connect_okrs, "UPDATE Okrs_Resultados SET orden = $orden2 WHERE id = '" . $post["id_registro"] . "'");
		mysqli_query($connect_okrs, "UPDATE Okrs_Resultados SET orden = $orden1 WHERE id = '" . $post["okr_fill"] . "'");
	} else {
		ModificarOrden($connect_okrs, $post, $id);
	}

	$query3 = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $data1["id_okrs"] . "");
	$data3 = mysqli_fetch_array($query3);

	$accion = 'ACTUALIZAR';
	$descripcion = 'Cambio de ubicación de KR : ' . $data1["descripcion"] . ' con el KR ' . $data2["descripcion"] . ' en el OKR ' . $data3["objetivo_okr"] . ' por la opción reubicar resultado';
	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $id_empresa . ", " . $id_user . ",'$accion','$descripcion'," . $data3["tipo"] . "," . $data1["id_okrs"] . "," . $post["id_registro"] . ",0,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);
}


function ModificarOrden($connect_okrs, $post, $id_okrs)
{
	$id_okrs = $post["id_okrs"];
	$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id = '" . $post["id_registro"] . "' ");
	$data = mysqli_fetch_array($query);

	$orden1 = $data["orden"];
	// print_r($post);
	if ($post["posicion"] == '1') {
		$orden2 = $orden1 - 1;

		if ($orden2 > 0) {
			$query1 = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = $id_okrs AND orden = $orden2");
			$data1 = mysqli_fetch_array($query1);

			$registro = $data1["id"];
			$orden3 = $data1["orden"];
			mysqli_query($connect_okrs, "UPDATE Okrs_Resultados SET orden = $orden3 WHERE id = " . $post["id_registro"] . "");
			mysqli_query($connect_okrs, "UPDATE Okrs_Resultados SET orden = $orden1 WHERE id = $registro");
		}
	} else {
		$orden2 = $orden1 + 1;

		$query1 = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = $id_okrs AND orden = $orden2");

		$registros = mysqli_num_rows($query1);
		$data1 = mysqli_fetch_array($query1);
		if ($registros > 0) {
			$registro = $data1["id"];
			$orden3 = $data1["orden"];
			mysqli_query($connect_okrs, "UPDATE Okrs_Resultados SET orden = $orden3 WHERE id = " . $post["id_registro"] . "");
			mysqli_query($connect_okrs, "UPDATE Okrs_Resultados SET orden = $orden1 WHERE id = $registro");
		}
	}
}

function CrearResultado($connect_okrs, $post, $id_okr, $hoy, $id_empresa, $id_empleado)
{
	ComprobarOrden($connect_okrs, $id_okr);

	$query1 = mysqli_query($connect_okrs, "SELECT MAX(orden) AS orden FROM Okrs_Resultados WHERE id_okrs = '" . $id_okr . "' ORDER BY orden");
	$data1 = mysqli_fetch_array($query1);

	$orden = $data1["orden"] + 1;

	$query2 = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $id_okr . "");
	$data2 = mysqli_fetch_array($query2);

	$sentencia1 = "
			INSERT INTO Okrs_Resultados (id_empresa, id_okrs, id_empleado, responsables, descripcion, avance, fecha_inicia, fecha_entrega, tendencia, medicion, meta, meta_minimo, meta_maximo, periodo, estado, orden, created_at)
			VALUES ('" . $post["id_empresa"] . "', $id_okr, " . $post["id_owner"] . ", '" . implode(",", $post["responsables"]) . "', '" . $post["descripcion"] . "', '" . $post["avance"] . "', '" . $post["fecha_inicia"] . "', '" . $post["fecha_entrega"] . "', '" . $post["tendencia"] . "', '" . $post["medicion"] . "', '" . $post["meta"] . "', '" . $post["meta_minimo"] . "', '" . $post["meta_maximo"] . "', '" . $post["periodo"] . "', 1, $orden, '" . $hoy . "')
			";

	// echo $sentencia1;

	mysqli_query($connect_okrs, $sentencia1);

	$id_tmp = mysqli_insert_id($connect_okrs);

	$accion = 'CREAR';
	$descripcion = 'Creación KR ' . $post["descripcion"] . ' para el OKR ' . $data2["objetivo_okr"];

	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $id_empresa . ", " . $id_empleado . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $id_okr . "," . $id_tmp . ",0,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);

	return $id_tmp;
}

function BuscarVicepresidenciaOKR($id_empresa, $id_okrs, $connect_okrs, $connect_valentina)
{
	$vicepresidencia = 0;
	$query1 = mysqli_query($connect_okrs, "SELECT DISTINCT(id_vicepresidencia) AS id_vicepresidencia FROM Okrs_Areas WHERE id_okrs = '" . $id_okrs . "'");
	$data1 = mysqli_fetch_array($query1);
	if (mysqli_num_rows($query1) > 0) {
		$query2 = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id = " . $data1["id_vicepresidencia"] . " AND id_empresa = $id_empresa");
		$data2 = mysqli_fetch_array($query2);
		if (mysqli_num_rows($query2) > 0) {
			$vicepresidencia = $data2["nombre"];
		}
	}

	return $vicepresidencia;
}

function PorOkrsPonderacion($connect_okrs, $okr, $id_empleado, $filtro_claves)
{

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

function OkrsPorVicepresidenciaConsolidados($id_vicepresidencia, $request, $connect_okrs)
{

	$promedio = 0;
	$suma = 0;
	$conteo = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = "";
	$filtro_rol = "";

	// if ($_SESSION['role_plataforma'] == 2) {
	// 	$filtro_rol .= " AND Okrs_Equipos.id_empleado = " . $_SESSION['id_user'] . "";
	// }

	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr = $request;
	}
	$okr = "";
	// $query = mysqli_query($connect_okrs,"SELECT OA.id_okrs AS id_okrs FROM Okrs_Areas OA INNER JOIN Okrs O ON O.id = OA.id_okrs
	// WHERE OA.id_vicepresidencia = '".$id_vicepresidencia."' AND O.anio = '".$_SESSION["anio_fill"]."' $filtro_rol GROUP BY OA.id_okrs ");

	$query = mysqli_query($connect_okrs, "SELECT Okrs.*
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		WHERE Okrs_Equipos.id_empresa = '" . $_SESSION["id_empresa"] . "' $filtro_rol AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' AND Okrs_Vicepresidencia.id_vicepresidencia = $id_vicepresidencia AND Okrs.tipo = 1
		GROUP BY Okrs.id ORDER BY Okrs.objetivo_okr ASC");

	// echo "SELECT Okrs.*
	// 	FROM Okrs_Equipos
	// 	LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
	// 	LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
	// 	LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
	// 	WHERE Okrs_Equipos.id_empresa = '" . $_SESSION["id_empresa"] . "' $filtro_rol AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' AND Okrs_Areas.id_vicepresidencia = $id_vicepresidencia
	// 	GROUP BY Okrs.id ORDER BY Okrs.objetivo_okr ASC";
	$total = mysqli_num_rows($query);
	if (mysqli_num_rows($query) > 0) {
		while ($dataOkrsNum = mysqli_fetch_array($query)) {
			$owner = $dataOkrsNum['id_empleado'];
			$okr .= $dataOkrsNum['id'] . ",";
			$respuesta = PorOkrsConsolidados($dataOkrsNum["id"], $filtro_kr, $connect_okrs);
			$suma += $respuesta["promedio"];
			$conteo++;
			$responsables .= $respuesta["team"] . ",";
		}
		$promedio = $suma / $conteo;
	}

	$okr = trim($okr, ',');

	$nodo = array(
		"promedio" => $promedio,
		"no_okrs" => $conteo,
		"team" => $responsables,
		"owner" => $owner,
		"okr" => $okr,
		"total" => $total
	);

	return $nodo;
}

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
				LEFT JOIN puntacana_admin.Empleados AS E ON E.id = OE.id_empleado
				LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = O.id_empleado
				WHERE EV.id_empresa = $id_empresa
				AND OE.id_empleado = $id_empleado
				AND O.anio = " . $_SESSION["periodo_desempenio_fill"] . "
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

function OkrsConsolidadoCompetenciaReporte($id_empleado, $id_empresa, $periodo_desempenio_fill, $connect_okrs)
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
				LEFT JOIN puntacana_admin.Empleados AS E ON E.id = OE.id_empleado
				LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = O.id_empleado
				WHERE EV.id_empresa = '$id_empresa'
				AND OE.id_empleado = $id_empleado
				AND O.anio = '" . $periodo_desempenio_fill . "'
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

function OkrsMiDesempenio($id_empleado, $id_empresa, $connect_okrs)
{

	$sentencia = "SELECT OE.id AS id, OE.id_empresa AS id_empresa,
				OE.id_empleado AS id_empleado,
				OE.id_okrs AS id_okrs, O.objetivo_okr AS objetivo_okr,
				O.fecha_inicia AS fecha_inicia, O.fecha_termina AS fecha_termina,
				O.tipo AS tipo, O.periodo AS periodo,
				O.objetivos_estrategicos AS objetivos_estrategicos,
				O.anio AS anio,
				OE.tipo AS tipo_role,
				E.nombre AS nombre_empleado,
				O.id_empleado AS id_owner, EO.nombre AS nombre_owner
				FROM Equipos_Views EV
				LEFT JOIN Okrs_Equipos OE ON OE.id_okrs = EV.id_okrs
				LEFT JOIN Okrs O ON O.id = OE.id_okrs
				LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = OE.id_okrs
				LEFT JOIN Okrs_Areas AS OA ON OA.id_okrs = OE.id_okrs
				LEFT JOIN puntacana_admin.Empleados AS E ON E.id = OE.id_empleado
				LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = O.id_empleado
				WHERE EV.id_empresa = $id_empresa
				AND EV.id_empleado = $id_empleado
				AND OE.id_empleado = $id_empleado
				AND O.anio = " . $_SESSION["periodo_desempenio_fill"] . "
				AND (Okrs_Resultados.responsables LIKE '%," . $id_empleado . "%' OR Okrs_Resultados.responsables LIKE '%" . $id_empleado . ",%' OR Okrs_Resultados.responsables IN ('" . $id_empleado . "'))
				GROUP BY O.id";



	// echo $sentencia;

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	while ($data = mysqli_fetch_array($query)) {

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

function OkrsReporteUsuarioConsolidado($id_empleado, $id_empresa, $connect_okrs, $filtro)
{

	$filtro = "";
	if ($filtro) {
		$filtro .= " AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' ";
	}

	// print_r($_SESSION);
	$sentencia = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
		AND Okrs_Equipos.id_empleado = '" . $id_empleado . "'
		AND (Okrs_Resultados.responsables LIKE '%," . $id_empleado . "%' OR Okrs_Resultados.responsables LIKE '%" . $id_empleado . ",%' OR Okrs_Resultados.responsables IN ('" . $id_empleado . "'))
		AND Okrs.anio = " . $_SESSION["anio_fill"] . "
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		";

	// echo $sentencia."<br>";

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	while ($data = mysqli_fetch_array($query)) {

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

function AgregarAuditoria($connect_okrs, $accion, $descripcion, $tipo_okr, $id_okr, $id_kr, $id_iniciativa)
{
	$hoy = date("Y-m-d H:i:s");
	$sentencia = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	    VALUES (" . $_SESSION['id_empresa'] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion',$tipo_okr,$id_okr,$id_kr,$id_iniciativa,'$hoy')";

	// echo $sentencia;
	mysqli_query($connect_okrs, $sentencia);
}

function GuardarComentarioIniciativa($post, $connect_okrs, $id_empleado, $id_empresa, $id_user)
{
	$hoy = date("Y-m-d H:i:s");
	$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $post["id_okr"] . "");
	$data2 = mysqli_fetch_array($query);
	$sentencia = "
		INSERT INTO Okrs_Comentarios_Iniciativas ( id_empresa, id_okrs , id_resultado, id_iniciativa, id_empleado, comentario,  created_at )
		VALUES
		(" . $id_empresa . ", '" . $post["id_okr"] . "',  '" . $post["id_resultado"] . "', '" . $post["id_iniciativa"] . "','" . $id_empleado . "', '" . $post["comentario"] . "', '" . $hoy . "'  )
			";
	// echo $sentencia;
	mysqli_query($connect_okrs, $sentencia);
	$id = mysqli_insert_id($connect_okrs);
	$accion = 'CREAR';
	$descripcion = 'Creación de comentario de iniciativa: ' . $post["comentario"] . ' para el OKR ' . $data2["objetivo_okr"];
	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $id_empresa . ", " . $id_user . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $post["id_okr"] . "," . $post["id_resultado"] . "," . $post["id_iniciativa"] . ",'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);
}

function EditarComentarioIniciativa($post, $connect_okrs, $id_empresa, $id_user)
{
	$hoy = date("Y-m-d H:i:s");
	$sentencia = "UPDATE Okrs_Comentarios_Iniciativas SET comentario = '" . $post["comentario"] . "', updated_at = '$hoy' WHERE id = " . $post["id_registro"] . "";
	// echo $sentencia;
	mysqli_query($connect_okrs, $sentencia);
	$sentencia1 = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Comentarios WHERE id = " . $post["id_registro"] . "");
	$dataSentencia = mysqli_fetch_array($sentencia1);
	$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $dataSentencia["id_okrs"] . "");
	$data2 = mysqli_fetch_array($query);
	$accion = 'ACTUALIZAR';
	$descripcion = 'Actualización de comentario de resultado clave: ' . $post["comentario"] . ' para el OKR ' . $data2["objetivo_okr"];
	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $id_empresa . ", " . $id_user . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $data2["id"] . "," . $dataSentencia["id_resultado"] . ",0,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);
}

function OkrsReporteUsuarioHomeLider($id_empleado, $id_empresa, $connect_okrs, $filtro)
{

	$sentencia = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
		AND Okrs_Equipos.id_empleado = '" . $id_empleado . "'
		AND (Okrs_Resultados.responsables LIKE '%," . $id_empleado . "%' OR Okrs_Resultados.responsables LIKE '%" . $id_empleado . ",%' OR Okrs_Resultados.responsables IN ('" . $id_empleado . "'))
		AND Okrs.anio = " . $_SESSION["anio_fill"] . "
		$filtro
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		";

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	while ($data = mysqli_fetch_array($query)) {

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

function ReporteIniciativasUsuario($connect_okrs, $okr, $id_empleado, $filtro_claves)
{

	$filtro_kr = " ";
	if ($filtro_claves) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr .= $filtro_claves;
	}

	$suma_resultado = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;

	$queryResultados = mysqli_query($connect_okrs, "SELECT OI.* FROM Okrs_Iniciativas OI INNER JOIN Okrs_Resultados ON Okrs_Resultados.id = OI.id_resultado ON WHERE OI.id_okrs = $okr  $filtro_kr ");

	while ($dataResultados = mysqli_fetch_array($queryResultados)) {
		$porcentaje = ($dataResultados["avance"] * 100) / $dataResultados["meta"];

		if ($dataResultados["tendencia"] == 2) {
			$porcentaje = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
		}

		if (is_infinite($porcentaje)) {
			$porcentaje = 0;
		}

		$suma_resultado += $porcentaje;
		// echo $suma_resultado;
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
	// print_r($nodo);
	return $nodo;
}

function IniciativasReporteUsuarioHomeLider($id_empleado, $id_empresa, $connect_okrs, $filtro)
{

	$sentencia = "
		SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
		Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
		Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
		Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
		Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
		Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Iniciativas ON Okrs_Iniciativas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
		AND Okrs_Equipos.id_empleado = '" . $id_empleado . "'
		AND (Okrs_Iniciativas.responsables LIKE '%," . $id_empleado . "%' OR Okrs_Iniciativas.responsables LIKE '%" . $id_empleado . ",%' OR Okrs_Iniciativas.responsables IN (" . $id_empleado . "))
		AND Okrs.anio = " . $_SESSION["anio_fill"] . "
		$filtro
		GROUP BY Okrs.id
		ORDER BY Okrs.objetivo_okr ASC
		";
	// echo $sentencia;
	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	while ($data = mysqli_fetch_array($query)) {

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

function OkrsPorUsuarioConsolidado($id_user, $connect_okrs, $filtro_kr, $filtro)
{

	$promedio = 0;
	$suma = 0;
	$conteo = 0;
	$responsables = $owner = $okr = "";
	$okr = "";

	$query = mysqli_query($connect_okrs, "SELECT Okrs.*
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Resultados AS ORE ON ORE.id_okrs = Okrs.id
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		WHERE Okrs_Equipos.id_empresa = '" . $_SESSION["id_empresa"] . "' AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' AND Empleados.id = $id_user $filtro_kr
		GROUP BY Okrs.id ORDER BY Okrs.objetivo_okr ASC");

	if (mysqli_num_rows($query) > 0) {
		while ($dataOkrsNum = mysqli_fetch_array($query)) {
			$owner = $dataOkrsNum['id_empleado'];
			$okr .= $dataOkrsNum['id'] . ",";
			$respuesta = PorOkrsConsolidados($dataOkrsNum["id"], $filtro_kr, $connect_okrs);
			if (is_nan($respuesta["promedio"])) {
				$promedioOkr = 0;
			} else {
				$promedioOkr = $respuesta["promedio"];
			}
			$suma += $promedioOkr;
			$conteo++;
			$responsables .= $respuesta["team"] . ",";
		}

		$promedio = $suma / $conteo;
	}

	$okr = trim($okr, ',');

	$nodo = array(
		"promedio" => $promedio,
		"no_okrs" => $conteo,
		"team" => $responsables,
		"owner" => $owner,
		"okr" => $okr
	);

	return $nodo;
}

function OkrArea($id, $id_empresa, $connect_okrs, $filtro_periodo)
{
	$filtro = '';
	if ($filtro_periodo) {
		$filtro .= " AND Okrs_Resultados.periodo IN ($filtro_periodo)";
	}
	$sentencia = "
	SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
	Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
	Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
	Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
	Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
	Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
	FROM Okrs_Equipos
	LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
	LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
	LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
	LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
	LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
	WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
	AND Okrs_Areas.id_area = '" . $id . "'
	AND Okrs.anio = '" . $_SESSION["anio_fill"] . "'
	AND Okrs.tipo = 2
	$filtro
	GROUP BY Okrs.id
	ORDER BY Okrs.objetivo_okr ASC
	";

	// echo $sentencia;
	$nodos = array();
	$cont = 0;
	$query = mysqli_query($connect_okrs, $sentencia);
	// $data = mysqli_fetch_array($query);
	while ($data = mysqli_fetch_array($query)) {

		$nodos[$cont]["id"] = $data["id_okrs"];
		$nodos[$cont]["objetivo"] = $data["objetivo_okr"];
		$nodos[$cont]["fecha_inicia"] = $data["fecha_inicia"];
		$nodos[$cont]["fecha_termina"] = $data["fecha_termina"];
		$nodos[$cont]["tipo"] = $data["tipo"];
		$nodos[$cont]["tipo_role"] = $data["tipo_role"];
		$nodos[$cont]["periodo"] = $data["periodo"];
		$nodos[$cont]["objetivos_estrategicos"] = $data["objetivos_estrategicos"];
		$nodos[$cont]["anio"] = $data["anio"];
		$nodos[$cont]["nombre_empleado"] = $data["nombre_empleado"];
		$nodos[$cont]["id_empleado"] = $data["id_empleado"];
		$nodos[$cont]["id_owner"] = $data["id_owner"];
		$nodos[$cont]["nombre_owner"] = $data["nombre_owner"];

		$cont++;
	}
	return $nodos;
}

function OkrAreaGestion($id, $id_empresa, $connect_okrs, $connect_valentina, $filtro_periodo, $id_user)
{
	$queryEE = mysqli_query($connect_valentina, "SELECT DISTINCT(vicepresidencia) AS id_vp FROM Estructura_Empresa WHERE area = $id");
	$dataEE = mysqli_fetch_array($queryEE);
	$id_vp = $dataEE["id_vp"];
	$filtro = '';
	$filtro = $filtro_usuario = "";
	if ($_SESSION["equipo_fill_equipo"] > 0) {
		$filtro .= " AND Okrs_Equipos.id_okrs = '" . $_SESSION["equipo_fill_equipo"] . "' ";
	}

	if ($_SESSION["areas_fill_equipo"] > 0) {
		$filtro .= " AND Okrs_Areas.id_area = '" . $_SESSION["areas_fill_equipo"] . "'  ";
	}

	if ($_SESSION["tipo_fill_equipo"] > 0) {
		$filtro .= " AND Okrs.tipo = '" . $_SESSION["tipo_fill_equipo"] . "'  ";
	}

	if ($_SESSION["objestrategico_fill_equipo"] > 0) {
		$filtro .= " AND Okrs.objetivos_estrategicos IN ('" . $_SESSION["objestrategico_fill_equipo"] . "')  ";
	}

	if ($filtro_periodo) {
		$filtro .= " AND Okrs_Resultados.periodo IN ($filtro_periodo)";
	}

	if ($_SESSION["vicepresidencia_fill_equipo"] > 0) {
		$filtro .= " AND Okrs_Vicepresidencia.id_vicepresidencia = " . $_SESSION["vicepresidencia_fill_equipo"] . "  ";
	}

	if ($_SESSION["colaborador_fill_equipo"] > 0) {
		$filtro_usuario = " AND Okrs_Equipos.id_empleado = '" . $_SESSION["colaborador_fill_equipo"] . "'  ";
		$filtro_usuario .= " AND (Okrs_Resultados.responsables LIKE '%," . $_SESSION["colaborador_fill_equipo"] . "%' OR Okrs_Resultados.responsables LIKE '%" . $_SESSION["colaborador_fill_equipo"] . ",%' OR Okrs_Resultados.responsables IN ('" . $_SESSION["colaborador_fill_equipo"] . "'))  ";
		//$filtro .= " AND Okrs.id_empleado = '".$_SESSION["colaborador_fill_okr"]."' ";
		//$filtro .= " AND Okrs_Equipos.id_empleado = '".$_SESSION["colaborador_fill_okr"]."'  ";
	}

	if ($filtro_periodo) {
		$filtro .= " AND Okrs_Resultados.periodo IN ($filtro_periodo)";
	}

	// 	$sentencia = "
	// 	SELECT Okrs_Equipos.id AS id,
	// Okrs_Equipos.id_empresa AS id_empresa,
	// Okrs_Equipos.id_empleado AS id_empleado,
	// Okrs_Equipos.id_okrs AS id_okrs,
	// Okrs.objetivo_okr AS objetivo_okr,
	// Okrs.fecha_inicia AS fecha_inicia,
	// Okrs.fecha_termina AS fecha_termina,
	// Okrs.tipo AS tipo,
	// Okrs.periodo AS periodo,
	// Okrs.objetivos_estrategicos AS objetivos_estrategicos,
	// Okrs.anio AS anio,
	// Okrs_Equipos.tipo AS tipo_role,
	// E.nombre AS nombre_empleado,
	// Okrs.id_empleado AS id_owner,
	// EO.nombre AS nombre_owner
	// 	FROM Equipos_Views EV
	// LEFT JOIN Okrs_Equipos ON Okrs_Equipos.id_okrs = EV.id_okrs
	// LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
	// LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
	// LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
	// LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
	// LEFT JOIN puntacana_admin.Empleados AS E ON E.id = Okrs_Equipos.id_empleado
	// LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
	// WHERE EV.id_empresa = '" . $id_empresa . "'
	// 	AND (Okrs_Areas.id_area = '" . $id . "' OR Okrs_Vicepresidencia.id_vicepresidencia = " . $id_vp . ")
	// 	AND Okrs.anio = '" . $_SESSION["anio_fill"] . "'
	// 	$filtro
	// 	$filtro_usuario
	// 	GROUP BY Okrs.id
	// 	ORDER BY Okrs.objetivo_okr ASC
	// 	";
	// echo "SELECT * FROM Lideres_Vicepresidencia WHERE id_lider = '$id_user' AND estado = 1";
	// $queryLV = mysqli_query($connect_valentina, "SELECT * FROM Lideres_Vicepresidencia WHERE id_lider = '$id_user' AND estado = 1");
	// if (mysqli_num_rows($queryLV) > 0) {
	// 	$dataLV = mysqli_fetch_array($queryLV);
	// 	$complemento = "AND Okrs_Vicepresidencia.id_vicepresidencia = '" . $dataLV["id_vicepresidencia"] . "'";
	// } else {
	$complemento = "AND Okrs_Areas.id_area = '$id'";
	// }

	$sentencia = "SELECT Okrs_Equipos.id AS id,
Okrs_Equipos.id_empresa AS id_empresa,
Okrs_Equipos.id_empleado AS id_empleado,
Okrs_Equipos.id_okrs AS id_okrs,
Okrs.objetivo_okr AS objetivo_okr,
Okrs.fecha_inicia AS fecha_inicia,
Okrs.fecha_termina AS fecha_termina,
Okrs.tipo AS tipo,
Okrs.periodo AS periodo,
Okrs.objetivos_estrategicos AS objetivos_estrategicos,
Okrs.anio AS anio,
(
        SELECT OE.tipo
        FROM Okrs_Equipos OE
        WHERE OE.id_okrs = Okrs.id
          AND OE.id_empleado =  $id_user
        LIMIT 1
    ) AS tipo_role,
E.nombre AS nombre_empleado,
Okrs.id_empleado AS id_owner,
EO.nombre AS nombre_owner
	FROM Equipos_Views EV
LEFT JOIN Okrs_Equipos ON Okrs_Equipos.id_okrs = EV.id_okrs
LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
LEFT JOIN puntacana_admin.Empleados AS E ON E.id = Okrs_Equipos.id_empleado
LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
WHERE EV.id_empresa = '" . $id_empresa . "'
	$complemento
	AND Okrs.anio = '" . $_SESSION["anio_fill"] . "'
	$filtro
	$filtro_usuario
	GROUP BY Okrs.id
	ORDER BY Okrs.objetivo_okr ASC
	";
	// echo "<br>" . $sentencia;
	$nodos = array();
	$cont = 0;
	$query = mysqli_query($connect_okrs, $sentencia);
	// $data = mysqli_fetch_array($query);
	while ($data = mysqli_fetch_array($query)) {

		$nodos[$cont]["id"] = $data["id_okrs"];
		$nodos[$cont]["objetivo"] = $data["objetivo_okr"];
		$nodos[$cont]["fecha_inicia"] = $data["fecha_inicia"];
		$nodos[$cont]["fecha_termina"] = $data["fecha_termina"];
		$nodos[$cont]["tipo"] = $data["tipo"];
		$nodos[$cont]["tipo_role"] = $data["tipo_role"];
		$nodos[$cont]["periodo"] = $data["periodo"];
		$nodos[$cont]["objetivos_estrategicos"] = $data["objetivos_estrategicos"];
		$nodos[$cont]["anio"] = $data["anio"];
		$nodos[$cont]["nombre_empleado"] = $data["nombre_empleado"];
		$nodos[$cont]["id_empleado"] = $data["id_empleado"];
		$nodos[$cont]["id_owner"] = $data["id_owner"];
		$nodos[$cont]["nombre_owner"] = $data["nombre_owner"];

		$cont++;
	}
	return $nodos;
}

function OkrAreaGestionFiltro($id, $id_empresa, $connect_okrs, $filtro_periodo)
{
	$filtro = '';
	$filtro = $filtro_usuario = "";
	if ($_SESSION["equipo_fill_equipo"] > 0) {
		$filtro .= " AND Okrs_Equipos.id_okrs = '" . $_SESSION["equipo_fill_equipo"] . "' ";
	}

	if ($_SESSION["areas_fill_equipo"] > 0) {
		$filtro .= " AND Okrs_Areas.id_area = '" . $_SESSION["areas_fill_equipo"] . "'  ";
	}

	if ($_SESSION["tipo_fill_equipo"] > 0) {
		$filtro .= " AND Okrs.tipo = '" . $_SESSION["tipo_fill_equipo"] . "'  ";
	}

	if ($_SESSION["objestrategico_fill_equipo"] > 0) {
		$filtro .= " AND Okrs.objetivos_estrategicos IN ('" . $_SESSION["objestrategico_fill_equipo"] . "')  ";
	}

	if ($filtro_periodo) {
		$filtro .= " AND Okrs_Resultados.periodo IN ($filtro_periodo)";
	}

	if ($_SESSION["vicepresidencia_fill_equipo"] > 0) {
		$filtro .= " AND Okrs_Vicepresidencia.id_vicepresidencia = " . $_SESSION["vicepresidencia_fill_equipo"] . "  ";
	}

	if ($_SESSION["colaborador_fill_equipo"] > 0) {
		$filtro_usuario = " AND Okrs_Equipos.id_empleado = '" . $_SESSION["colaborador_fill_equipo"] . "'  ";
		$filtro_usuario .= " AND (Okrs_Resultados.responsables LIKE '%," . $_SESSION["colaborador_fill_equipo"] . "%' OR Okrs_Resultados.responsables LIKE '%" . $_SESSION["colaborador_fill_equipo"] . ",%' OR Okrs_Resultados.responsables IN ('" . $_SESSION["colaborador_fill_equipo"] . "'))  ";
		//$filtro .= " AND Okrs.id_empleado = '".$_SESSION["colaborador_fill_okr"]."' ";
		//$filtro .= " AND Okrs_Equipos.id_empleado = '".$_SESSION["colaborador_fill_okr"]."'  ";
	}

	$sentencia = "
	SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
	Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
	Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
	Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
	Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
	Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
	FROM Okrs_Equipos
	LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
	LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
	LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
	LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
	LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
	WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
	AND Okrs_Areas.id_area = '" . $id . "'
	AND Okrs.anio = '" . $_SESSION["anio_fill"] . "'

	GROUP BY Okrs.id
	ORDER BY Okrs.objetivo_okr ASC
	";

	// echo "<br>" . $sentencia;
	$nodos = array();
	$cont = 0;
	$query = mysqli_query($connect_okrs, $sentencia);
	// $data = mysqli_fetch_array($query);
	while ($data = mysqli_fetch_array($query)) {

		$nodos[$cont]["id"] = $data["id_okrs"];
		$nodos[$cont]["objetivo"] = $data["objetivo_okr"];
		$nodos[$cont]["fecha_inicia"] = $data["fecha_inicia"];
		$nodos[$cont]["fecha_termina"] = $data["fecha_termina"];
		$nodos[$cont]["tipo"] = $data["tipo"];
		$nodos[$cont]["tipo_role"] = $data["tipo_role"];
		$nodos[$cont]["periodo"] = $data["periodo"];
		$nodos[$cont]["objetivos_estrategicos"] = $data["objetivos_estrategicos"];
		$nodos[$cont]["anio"] = $data["anio"];
		$nodos[$cont]["nombre_empleado"] = $data["nombre_empleado"];
		$nodos[$cont]["id_empleado"] = $data["id_empleado"];
		$nodos[$cont]["id_owner"] = $data["id_owner"];
		$nodos[$cont]["nombre_owner"] = $data["nombre_owner"];

		$cont++;
	}
	return $nodos;
}

function OkrsPorVPConsolidados($id_vp, $request, $connect_okrs)
{
	$promedio = 0;
	$suma = 0;
	$conteo = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = "";
	$filtro_rol = "";

	// if ($_SESSION['role_plataforma'] == 2) {
	// 	$filtro_rol .= " AND Okrs_Equipos.id_empleado = " . $_SESSION['id_user'] . "";
	// }

	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr = $request;
	}
	$okr = "";
	// $query = mysqli_query($connect_okrs,"SELECT OA.id_okrs AS id_okrs FROM Okrs_Areas OA INNER JOIN Okrs O ON O.id = OA.id_okrs
	// WHERE OA.id_vp = '".$id_vp."' AND O.anio = '".$_SESSION["anio_fill"]."' $filtro_rol GROUP BY OA.id_okrs ");

	$query = mysqli_query($connect_okrs, "SELECT Okrs.*
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		WHERE Okrs_Equipos.id_empresa = '" . $_SESSION["id_empresa"] . "' AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' AND Okrs_Vicepresidencia.id_vicepresidencia = $id_vp AND Okrs.tipo = 1
		GROUP BY Okrs.id ORDER BY Okrs.objetivo_okr ASC");

	if (mysqli_num_rows($query) > 0) {
		while ($dataOkrsNum = mysqli_fetch_array($query)) {
			$owner = $dataOkrsNum['id_empleado'];
			$okr .= $dataOkrsNum['id'] . ",";
			$respuesta = PorOkrsConsolidados($dataOkrsNum["id"], $filtro_kr, $connect_okrs);
			if (is_nan($respuesta["promedio"])) {
				$promedioOkr = 0;
			} else {
				$promedioOkr = $respuesta["promedio"];
			}
			if ($respuesta["promedio"] > 100) {
				$promedioOkr = 100;
			}
			$suma += $promedioOkr;
			$conteo++;
			$responsables .= $respuesta["team"] . ",";
		}

		$promedio = $suma / $conteo;
	}

	$okr = trim($okr, ',');

	$nodo = array(
		"promedio" => $promedio,
		"no_okrs" => $conteo,
		"team" => $responsables,
		"owner" => $owner,
		"okr" => $okr
	);

	return $nodo;
}

function OkrVP($id, $id_empresa, $connect_okrs, $filtro_periodo)
{
	$filtro = '';
	if ($filtro_periodo) {
		$filtro .= " AND Okrs_Resultados.periodo IN ($filtro_periodo)";
	}
	$sentencia = "
	SELECT Okrs_Equipos.id AS id , Okrs_Equipos.id_empresa AS id_empresa ,
	Okrs_Equipos.id_empleado AS id_empleado , Okrs_Equipos.id_okrs AS id_okrs ,
	Okrs.objetivo_okr AS objetivo_okr , Okrs.fecha_inicia AS fecha_inicia,
	Okrs.fecha_termina AS fecha_termina , Okrs.tipo AS tipo, Okrs.periodo AS periodo,
	Okrs.objetivos_estrategicos AS objetivos_estrategicos, Okrs.anio AS anio, Okrs_Equipos.tipo AS tipo_role,
	Empleados.nombre AS nombre_empleado, Okrs.id_empleado AS id_owner, EO.nombre AS nombre_owner
	FROM Okrs_Equipos
	LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
	LEFT JOIN Okrs_Vicepresidencia ON Okrs_Vicepresidencia.id_okrs = Okrs.id
	LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
	LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
	LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
	WHERE Okrs_Equipos.id_empresa = '" . $id_empresa . "'
	AND Okrs_Vicepresidencia.id_vicepresidencia = '" . $id . "'
	AND Okrs.tipo = 1
	AND Okrs.anio = '" . $_SESSION["anio_fill"] . "'
	$filtro
	GROUP BY Okrs.id
	ORDER BY Okrs.objetivo_okr ASC
	";

	// echo $sentencia;
	$nodos = array();
	$cont = 0;
	$query = mysqli_query($connect_okrs, $sentencia);
	// $data = mysqli_fetch_array($query);
	while ($data = mysqli_fetch_array($query)) {

		$nodos[$cont]["id"] = $data["id_okrs"];
		$nodos[$cont]["objetivo"] = $data["objetivo_okr"];
		$nodos[$cont]["fecha_inicia"] = $data["fecha_inicia"];
		$nodos[$cont]["fecha_termina"] = $data["fecha_termina"];
		$nodos[$cont]["tipo"] = $data["tipo"];
		$nodos[$cont]["tipo_role"] = $data["tipo_role"];
		$nodos[$cont]["periodo"] = $data["periodo"];
		$nodos[$cont]["objetivos_estrategicos"] = $data["objetivos_estrategicos"];
		$nodos[$cont]["anio"] = $data["anio"];
		$nodos[$cont]["nombre_empleado"] = $data["nombre_empleado"];
		$nodos[$cont]["id_empleado"] = $data["id_empleado"];
		$nodos[$cont]["id_owner"] = $data["id_owner"];
		$nodos[$cont]["nombre_owner"] = $data["nombre_owner"];

		$cont++;
	}
	return $nodos;
}

function IniciativasPorVPConsolidados($id_vp, $request, $connect_okrs)
{
	$promedio = $suma = $conteo = $conteoIni = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = "";
	$filtro_rol = "";

	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr = $request;
	}
	$okr = "";

	$query = mysqli_query($connect_okrs, "SELECT OI.*
		FROM Okrs_Iniciativas OI
		INNER JOIN Okrs_Resultados ORE ON ORE.id = OI.id_resultado
		INNER JOIN Okrs O ON O.id = ORE.id_okrs
		INNER JOIN Okrs_Equipos OE ON OE.id_okrs = ORE.id_okrs
		INNER JOIN Okrs_Vicepresidencia OA ON OA.id_okrs = OE.id_okrs
		WHERE OE.id_empresa = '" . $_SESSION["id_empresa"] . "' AND O.anio = '" . $_SESSION["anio_fill"] . "' AND OA.id_vicepresidencia = $id_vp $filtro_kr
		GROUP BY OI.id ORDER BY OI.descripcion ASC");

	return $query;
}

function PlanesAccionPorVPConsolidados($id_vp, $request, $connect_okrs)
{
	$promedio = $suma = $conteo = $conteoIni = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = "";
	$filtro_rol = "";

	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr = $request;
	}
	$okr = "";

	$query = mysqli_query($connect_okrs, "SELECT OA.*
		FROM Okrs_Actividades OA
		INNER JOIN Okrs_Resultados ORE ON ORE.id = OA.id_resultado
		INNER JOIN Okrs O ON O.id = ORE.id_okrs
		INNER JOIN Okrs_Equipos OE ON OE.id_okrs = ORE.id_okrs
		INNER JOIN Okrs_Areas OAR ON OAR.id_okrs = OE.id_okrs
		WHERE OE.id_empresa = '" . $_SESSION["id_empresa"] . "' AND O.anio = '" . $_SESSION["anio_fill"] . "' AND OAR.id_vicepresidencia = $id_vp $filtro_kr
		GROUP BY OA.id ORDER BY OA.descripcion ASC");

	return $query;
}

function PlanesAccionPorVPConsolidadosEstado($id_vp, $request, $estado, $connect_okrs)
{
	$promedio = $suma = $conteo = $conteoIni = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = "";
	$filtro_rol = $filtro_estado = "";

	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr = $request;
	}
	$okr = "";

	if ($estado == 1) {
		$filtro_estado = "AND (OA.estado_backlog = 1 OR OA.estado_backlog IS NULL)";
	} else {
		$filtro_estado = "AND OA.estado_backlog = $estado";
	}

	$query = mysqli_query($connect_okrs, "SELECT OA.*
		FROM Okrs_Actividades OA
		INNER JOIN Okrs_Resultados ORE ON ORE.id = OA.id_resultado
		INNER JOIN Okrs O ON O.id = ORE.id_okrs
		INNER JOIN Okrs_Equipos OE ON OE.id_okrs = ORE.id_okrs
		INNER JOIN Okrs_Areas OAR ON OAR.id_okrs = OE.id_okrs
		WHERE OE.id_empresa = '" . $_SESSION["id_empresa"] . "' AND O.anio = '" . $_SESSION["anio_fill"] . "' AND OAR.id_vicepresidencia = $id_vp $filtro_kr $filtro_estado
		GROUP BY OA.id ORDER BY OA.descripcion ASC");

	return $query;
}

function PlanesAccionUsuarioConsolidadosEstado($id_user, $request, $estado, $connect_okrs)
{
	$promedio = $suma = $conteo = $conteoIni = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = "";
	$filtro_rol = $filtro_estado = "";

	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr = $request;
	}
	$okr = "";


	if ($estado == 1) {
		$filtro_estado = "AND (OA.estado_backlog = 1 OR OA.estado_backlog IS NULL)";
	} else {
		$filtro_estado = "AND OA.estado_backlog = $estado";
	}

	$query = mysqli_query($connect_okrs, "SELECT OA.*
		FROM Okrs_Actividades OA
		INNER JOIN Okrs_Resultados ORE ON ORE.id = OA.id_resultado
		INNER JOIN Okrs O ON O.id = ORE.id_okrs
		INNER JOIN Okrs_Equipos OE ON OE.id_okrs = ORE.id_okrs
		INNER JOIN Okrs_Areas OAR ON OAR.id_okrs = OE.id_okrs
		WHERE OA.id_asignado = $id_user AND O.anio = '" . $_SESSION["anio_fill"] . "' $filtro_kr $filtro_estado
		GROUP BY OA.id ORDER BY OA.descripcion ASC");

	return $query;
}

function PlanesAccionUsuarioConsolidadosEstadoGestion($id_user, $request, $estado, $connect_okrs, $connect_valentina, $filtro_periodo)
{
	$promedio = $suma = $conteo = $conteoIni = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = $filtro_usuario = "";
	$filtro_rol = $filtro_estado = "";

	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr = $request;
	}
	$okr = "";

	if ($_SESSION["equipo_fill_equipo"] > 0) {
		$filtro_kr .= " AND OE.id_okrs = '" . $_SESSION["equipo_fill_equipo"] . "' ";
	}

	if ($_SESSION["tipo_fill_equipo"] > 0) {
		$filtro_kr .= " AND O.tipo = '" . $_SESSION["tipo_fill_equipo"] . "'  ";
	}

	if ($_SESSION["objestrategico_fill_equipo"] > 0) {
		$filtro_kr .= " AND O.objetivos_estrategicos IN ('" . $_SESSION["objestrategico_fill_equipo"] . "')  ";
	}

	if ($_SESSION["colaborador_fill_equipo"] > 0) {
		$filtro_usuario = " AND OA.id_asignado =" . $_SESSION["colaborador_fill_equipo"] . "";
		//$filtro .= " AND Okrs.id_empleado = '".$_SESSION["colaborador_fill_okr"]."' ";
		//$filtro .= " AND Okrs_Equipos.id_empleado = '".$_SESSION["colaborador_fill_okr"]."'  ";
	} else {
		$queryPub = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE area = $id_user AND estado = 1 ");
		while ($dataPub = mysqli_fetch_array($queryPub)) {
			$filtro_usuario .= $dataPub["id"] . ",";
		}
		$filtro_user = substr($filtro_usuario, 0, -1);
		$filtro_usuario = " AND OA.id_asignado IN ($filtro_user)";
	}

	if ($estado == 1) {
		$filtro_estado = "AND (OA.estado_backlog = 1 OR OA.estado_backlog IS NULL)";
	} else {
		$filtro_estado = "AND OA.estado_backlog = $estado";
	}

	if ($filtro_periodo != "") {
		$filtro_kr .= " AND ORE.periodo IN ($filtro_periodo)";
	}

	$query = mysqli_query($connect_okrs, "SELECT OA.*
		FROM Okrs_Actividades OA
		INNER JOIN Okrs_Resultados ORE ON ORE.id = OA.id_resultado
		INNER JOIN Okrs O ON O.id = ORE.id_okrs
		INNER JOIN Okrs_Equipos OE ON OE.id_okrs = ORE.id_okrs
		INNER JOIN Okrs_Areas OAR ON OAR.id_okrs = OE.id_okrs
		WHERE OAR.id_area = $id_user AND O.anio = '" . $_SESSION["anio_fill"] . "' $filtro_kr $filtro_estado $filtro_usuario
		GROUP BY OA.id ORDER BY OA.descripcion ASC");

	return $query;
}

function PlanesAccionUsuarioConsolidadosTimeline($id_user, $request, $estado, $connect_okrs, $connect_valentina, $filtro_periodo)
{
	$promedio = $suma = $conteo = $conteoIni = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = $filtro_usuario = "";
	$filtro_rol = $filtro_estado = "";

	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr = $request;
	}
	$okr = "";

	if ($_SESSION["equipo_fill_equipo"] > 0) {
		$filtro_kr .= " AND OE.id_okrs = '" . $_SESSION["equipo_fill_equipo"] . "' ";
	}

	if ($_SESSION["tipo_fill_equipo"] > 0) {
		$filtro_kr .= " AND O.tipo = '" . $_SESSION["tipo_fill_equipo"] . "'  ";
	}

	if ($_SESSION["objestrategico_fill_equipo"] > 0) {
		$filtro_kr .= " AND O.objetivos_estrategicos IN ('" . $_SESSION["objestrategico_fill_equipo"] . "')  ";
	}

	if ($_SESSION["colaborador_fill_equipo"] > 0) {
		$filtro_usuario = " AND OA.id_asignado =" . $_SESSION["colaborador_fill_equipo"] . "";
		//$filtro .= " AND Okrs.id_empleado = '".$_SESSION["colaborador_fill_okr"]."' ";
		//$filtro .= " AND Okrs_Equipos.id_empleado = '".$_SESSION["colaborador_fill_okr"]."'  ";
	}
	// else {
	// 	$queryPub = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE area = $id_user AND estado = 1 ");
	// 	while ($dataPub = mysqli_fetch_array($queryPub)) {
	// 		$filtro_usuario .= $dataPub["id"] . ",";
	// 	}
	// 	$filtro_user = substr($filtro_usuario, 0, -1);
	// 	$filtro_usuario = " AND OA.id_asignado IN ($filtro_user)";
	// }

	if ($estado == 1) {
		$filtro_estado = "AND (OA.estado_backlog = 1 OR OA.estado_backlog IS NULL)";
	} else {
		$filtro_estado = "AND OA.estado_backlog = $estado";
	}

	if ($filtro_periodo != "") {
		$filtro_kr .= " AND ORE.periodo IN ($filtro_periodo)";
	}

	$query = mysqli_query($connect_okrs, "SELECT OA.*
		FROM Okrs_Actividades OA
		INNER JOIN Okrs_Resultados ORE ON ORE.id = OA.id_resultado
		INNER JOIN Okrs O ON O.id = ORE.id_okrs
		INNER JOIN Okrs_Equipos OE ON OE.id_okrs = ORE.id_okrs
		INNER JOIN Okrs_Areas OAR ON OAR.id_okrs = OE.id_okrs
		WHERE OAR.id_area = $id_user AND O.anio = '" . $_SESSION["anio_fill"] . "' $filtro_kr $filtro_usuario
		GROUP BY OA.id ORDER BY OA.fecha_inicia ASC");

	return $query;
}

function PlanesAccionUsuarioConsolidadosTimelineIni($id_user, $request, $estado, $connect_okrs, $connect_valentina, $filtro_periodo)
{
	$promedio = $suma = $conteo = $conteoIni = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = $filtro_usuario = "";
	$filtro_rol = $filtro_estado = "";

	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr = $request;
	}
	$okr = "";

	if ($_SESSION["equipo_fill_equipo"] > 0) {
		$filtro_kr .= " AND OE.id_okrs = '" . $_SESSION["equipo_fill_equipo"] . "' ";
	}

	if ($_SESSION["tipo_fill_equipo"] > 0) {
		$filtro_kr .= " AND O.tipo = '" . $_SESSION["tipo_fill_equipo"] . "'  ";
	}

	if ($_SESSION["objestrategico_fill_equipo"] > 0) {
		$filtro_kr .= " AND O.objetivos_estrategicos IN ('" . $_SESSION["objestrategico_fill_equipo"] . "')  ";
	}

	if ($_SESSION["colaborador_fill_equipo"] > 0) {
		$filtro_usuario = " AND OA.id_asignado =" . $_SESSION["colaborador_fill_equipo"] . "";
		//$filtro .= " AND Okrs.id_empleado = '".$_SESSION["colaborador_fill_okr"]."' ";
		//$filtro .= " AND Okrs_Equipos.id_empleado = '".$_SESSION["colaborador_fill_okr"]."'  ";
	} else {
		$queryPub = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE area = $id_user AND estado = 1 ");
		while ($dataPub = mysqli_fetch_array($queryPub)) {
			$filtro_usuario .= $dataPub["id"] . ",";
		}
		$filtro_user = substr($filtro_usuario, 0, -1);
		$filtro_usuario = " AND OA.id_asignado IN ($filtro_user)";
	}

	if ($estado == 1) {
		$filtro_estado = "AND (OA.estado_backlog = 1 OR OA.estado_backlog IS NULL)";
	} else {
		$filtro_estado = "AND OA.estado_backlog = $estado";
	}

	if ($filtro_periodo != "") {
		$filtro_kr .= " AND ORE.periodo IN ($filtro_periodo)";
	}
	$query = mysqli_query($connect_okrs, "SELECT DISTINCT(OI.id) AS id, OI.descripcion AS descripcion
		FROM Okrs_Actividades OA
		LEFT JOIN Okrs_Iniciativas OI ON OI.id = OA.id_iniciativa
		INNER JOIN Okrs_Resultados ORE ON ORE.id = OA.id_resultado
		INNER JOIN Okrs O ON O.id = ORE.id_okrs
		INNER JOIN Okrs_Equipos OE ON OE.id_okrs = ORE.id_okrs
		INNER JOIN Okrs_Areas OAR ON OAR.id_okrs = OE.id_okrs
		WHERE OAR.id_area = $id_user AND O.anio = '" . $_SESSION["anio_fill"] . "' $filtro_kr $filtro_usuario
		GROUP BY OA.id ORDER BY OA.fecha_inicia ASC");

	return $query;
}

function IniciativasConsolidados($id_okr, $request, $connect_okrs)
{
	$suma_resultado = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;
	$responsables = "";
	$filtro = $request ? $request : "";

	$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Iniciativas INNER JOIN Okrs_Resultados ORE ON ORE.id = Okrs_Iniciativas.id_resultado WHERE Okrs_Resultados.id_okrs = '" . $id_okr . "' $filtro  ");
	// echo "SELECT * FROM Okrs_Iniciativas INNER JOIN Okrs_Resultados ORE ON ORE.id = Okrs_Iniciativas.id_resultado WHERE Okrs_Resultados.id_okrs = '" . $id_okr . "' $filtro <br> ";
	while ($dataResultados = mysqli_fetch_array($queryResultados)) {

		//TENDENCIA ASCENTENTE :: POR DEFECTO
		$porcentaje = ($dataResultados["avance"] * 100) / $dataResultados["meta"];
		$nombreOkrs = $dataResultados['descripcion'];
		if ($dataResultados["tendencia"] == 2) {
			$porcentaje = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
		}


		if (is_infinite($porcentaje) || is_nan($porcentaje)) {
			$porcentaje = 0;
		}

		$suma_resultado += $porcentaje;
		$conteo_resultado++;

		$responsables .= $dataResultados["responsables"] . ",";
	}

	// if ($suma_resultado > 0) {
	// 	$resultado_prom_okr = ($suma_resultado / $conteo_resultado);
	// } else {
	// 	$resultado_prom_okr += 0;
	// }

	$resultado_prom_okr = ($suma_resultado / $conteo_resultado);

	if (is_nan($resultado_prom_okr)) {
		$resultado_prom_okr = 0;
	}

	$nodo = array(
		"promedio" => round($resultado_prom_okr),
		"no_iniciativas" => $queryResultados->num_rows,
		"team"  => $responsables,
		'nombreOkrs' => $nombreOkrs
	);

	return $nodo;
}

function IniciativasPorAreaConsolidados($id, $request, $connect_okrs, $connect_valentina)
{
	$promedio = $suma = $conteo = $conteoIni = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = "";
	$filtro_rol = "";

	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr = $request;
	}
	$okr = "";

	$query = mysqli_query($connect_okrs, "SELECT OI.*
		FROM Okrs_Iniciativas OI
		INNER JOIN Okrs_Resultados ORE ON ORE.id = OI.id_resultado
		INNER JOIN Okrs O ON O.id = ORE.id_okrs
		INNER JOIN Okrs_Equipos OE ON OE.id_okrs = ORE.id_okrs
		INNER JOIN Okrs_Areas OA ON OA.id_okrs = OE.id_okrs
		WHERE OE.id_empresa = '" . $_SESSION["id_empresa"] . "' AND O.anio = '" . $_SESSION["anio_fill"] . "' AND OA.id_area = $id $filtro_kr
		GROUP BY OI.id ORDER BY OI.descripcion ASC");

	return $query;
}

function IniciativasPorAreaConsolidadosGestion($id, $request, $connect_okrs, $filtro_periodo)
{
	$promedio = $suma = $conteo = $conteoIni = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = $filtro_usuario = "";
	$filtro_rol = "";

	// if ($request) {
	// 	// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
	// 	$filtro_kr = $request;
	// }

	if ($_SESSION["equipo_fill_equipo"] > 0) {
		$filtro_kr .= " AND OE.id_okrs = '" . $_SESSION["equipo_fill_equipo"] . "' ";
	}

	if ($_SESSION["tipo_fill_equipo"] > 0) {
		$filtro_kr .= " AND O.tipo = '" . $_SESSION["tipo_fill_equipo"] . "'  ";
	}

	if ($_SESSION["objestrategico_fill_equipo"] > 0) {
		$filtro_kr .= " AND O.objetivos_estrategicos IN ('" . $_SESSION["objestrategico_fill_equipo"] . "')  ";
	}

	if ($_SESSION["colaborador_fill_equipo"] > 0) {
		$filtro_usuario = " AND (OI.responsables LIKE '%," . $_SESSION["colaborador_fill_equipo"] . "%' OR OI.responsables LIKE '%" . $_SESSION["colaborador_fill_equipo"] . ",%' OR OI.responsables IN (" . $_SESSION["colaborador_fill_equipo"] . "))  ";
		//$filtro .= " AND Okrs.id_empleado = '".$_SESSION["colaborador_fill_okr"]."' ";
		//$filtro .= " AND Okrs_Equipos.id_empleado = '".$_SESSION["colaborador_fill_okr"]."'  ";
	}

	if ($filtro_periodo != "") {
		$filtro_kr .= " AND ORE.periodo IN ($filtro_periodo)";
	}

	// if ($filtro_periodo) {
	// 	$filtro_kr .= " AND ORE.periodo IN ($filtro_periodo)";
	// }

	$okr = "";

	$query = mysqli_query($connect_okrs, "SELECT OI.*
		FROM Okrs_Iniciativas OI
		INNER JOIN Okrs_Resultados ORE ON ORE.id = OI.id_resultado
		INNER JOIN Okrs O ON O.id = ORE.id_okrs
		INNER JOIN Okrs_Equipos OE ON OE.id_okrs = ORE.id_okrs
		INNER JOIN Okrs_Areas OA ON OA.id_okrs = OE.id_okrs
		WHERE OE.id_empresa = '" . $_SESSION["id_empresa"] . "' AND O.anio = '" . $_SESSION["anio_fill"] . "' AND OA.id_area = $id $filtro_kr $filtro_usuario
		GROUP BY OI.id ORDER BY OI.descripcion ASC");

	return $query;
}

function PlanesAccionPorAreaConsolidados($id, $request, $connect_okrs, $connect_valentina)
{
	$promedio = $suma = $conteo = $conteoIni = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = $filtro_usuario = "";
	$filtro_rol = "";

	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr = $request;
	}

	$queryPub = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE area = $id AND estado = 1 ");
	while ($dataPub = mysqli_fetch_array($queryPub)) {
		$filtro_usuario .= $dataPub["id"] . ",";
	}
	$filtro_user = substr($filtro_usuario, 0, -1);
	$filtro_usuario = " AND OA.id_asignado IN ($filtro_user)";
	$okr = "";

	$query = mysqli_query($connect_okrs, "SELECT OA.*
		FROM Okrs_Actividades OA
		INNER JOIN Okrs_Resultados ORE ON ORE.id = OA.id_resultado
		INNER JOIN Okrs O ON O.id = ORE.id_okrs
		INNER JOIN Okrs_Equipos OE ON OE.id_okrs = ORE.id_okrs
		INNER JOIN Okrs_Areas OAR ON OAR.id_okrs = OE.id_okrs
		WHERE OE.id_empresa = '" . $_SESSION["id_empresa"] . "' AND O.anio = '" . $_SESSION["anio_fill"] . "' AND OAR.id_area = $id $filtro_kr $filtro_usuario
		GROUP BY OA.id ORDER BY OA.descripcion ASC");

	return $query;
}

function PlanesAccionPorAreaConsolidadosGestion($id, $request, $connect_okrs, $connect_valentina, $filtro_periodo)
{
	$promedio = $suma = $conteo = $conteoIni = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = $filtro_usuario = "";
	$filtro_rol = "";

	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr = $request;
	}

	if ($_SESSION["equipo_fill_equipo"] > 0) {
		$filtro_kr .= " AND OE.id_okrs = '" . $_SESSION["equipo_fill_equipo"] . "' ";
	}

	if ($_SESSION["tipo_fill_equipo"] > 0) {
		$filtro_kr .= " AND O.tipo = '" . $_SESSION["tipo_fill_equipo"] . "'  ";
	}

	if ($_SESSION["objestrategico_fill_equipo"] > 0) {
		$filtro_kr .= " AND O.objetivos_estrategicos IN ('" . $_SESSION["objestrategico_fill_equipo"] . "')  ";
	}

	if ($_SESSION["colaborador_fill_equipo"] > 0) {
		$filtro_usuario = " AND OA.id_asignado =" . $_SESSION["colaborador_fill_equipo"] . "";
		//$filtro .= " AND Okrs.id_empleado = '".$_SESSION["colaborador_fill_okr"]."' ";
		//$filtro .= " AND Okrs_Equipos.id_empleado = '".$_SESSION["colaborador_fill_okr"]."'  ";
	} else {

		$queryPub = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE area = $id AND estado = 1 ");
		while ($dataPub = mysqli_fetch_array($queryPub)) {
			$filtro_usuario .= $dataPub["id"] . ",";
		}
		$filtro_user = substr($filtro_usuario, 0, -1);
		$filtro_usuario = " AND OA.id_asignado IN ($filtro_user)";
	}
	$okr = "";

	if ($filtro_periodo != "") {
		$filtro_kr .= " AND ORE.periodo IN ($filtro_periodo)";
	}

	$query = mysqli_query($connect_okrs, "SELECT OA.*
		FROM Okrs_Actividades OA
		INNER JOIN Okrs_Resultados ORE ON ORE.id = OA.id_resultado
		INNER JOIN Okrs O ON O.id = ORE.id_okrs
		INNER JOIN Okrs_Equipos OE ON OE.id_okrs = ORE.id_okrs
		INNER JOIN Okrs_Areas OAR ON OAR.id_okrs = OE.id_okrs
		WHERE OE.id_empresa = " . $_SESSION["id_empresa"] . " AND O.anio = " . $_SESSION["anio_fill"] . " AND OAR.id_area = $id $filtro_kr $filtro_usuario
		GROUP BY OA.id ORDER BY OA.descripcion ASC");

	return $query;
}

function PlanesAccionPorAreaConsolidadosEstado($id, $request, $estado, $connect_okrs, $connect_valentina)
{
	$promedio = $suma = $conteo = $conteoIni = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = $filtro_usuario = "";
	$filtro_rol = $filtro_estado = "";

	$queryPub = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE area = $id AND estado = 1 ");
	while ($dataPub = mysqli_fetch_array($queryPub)) {
		$filtro_usuario .= $dataPub["id"] . ",";
	}
	$filtro_user = substr($filtro_usuario, 0, -1);
	$filtro_usuario = " AND OA.id_asignado IN ($filtro_user)";

	if ($request) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr = $request;
	}
	$okr = "";

	if ($estado == 1) {
		$filtro_estado = "AND (OA.estado_backlog = 1 OR OA.estado_backlog IS NULL)";
	} else {
		$filtro_estado = "AND OA.estado_backlog = $estado";
	}

	$query = mysqli_query($connect_okrs, "SELECT OA.*
		FROM Okrs_Actividades OA
		INNER JOIN Okrs_Resultados ORE ON ORE.id = OA.id_resultado
		INNER JOIN Okrs O ON O.id = ORE.id_okrs
		INNER JOIN Okrs_Equipos OE ON OE.id_okrs = ORE.id_okrs
		INNER JOIN Okrs_Areas OAR ON OAR.id_okrs = OE.id_okrs
		WHERE OE.id_empresa = '" . $_SESSION["id_empresa"] . "' AND O.anio = '" . $_SESSION["anio_fill"] . "' AND OAR.id_area = $id $filtro_kr $filtro_estado $filtro_usuario
		GROUP BY OA.id ORDER BY OA.descripcion ASC");

	return $query;
}

function OkrsPorAreaConsolidadosLider($id_area, $request, $filtro, $connect_okrs)
{

	$promedio = 0;
	$suma = 0;
	$conteo = 0;
	$responsables = $owner = $okr = "";

	$filtro_kr = "";
	$filtro_periodo = "";

	// if ($_SESSION['role_plataforma'] == 2) {
	// 	$filtro_rol .= " AND Okrs_Equipos.id_empleado = " . $_SESSION['id_user'] . "";
	// }

	if ($request) {
		$filtro_periodo .= " AND Okrs_Resutlados.periodo IN ($filtro) ";
		$filtro_kr = $request;
	}
	$okr = "";
	// $query = mysqli_query($connect_okrs,"SELECT OA.id_okrs AS id_okrs FROM Okrs_Areas OA INNER JOIN Okrs O ON O.id = OA.id_okrs
	// WHERE OA.id_area = '".$id_area."' AND O.anio = '".$_SESSION["anio_fill"]."' $filtro_rol GROUP BY OA.id_okrs ");

	$query = mysqli_query($connect_okrs, "SELECT Okrs.*
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		WHERE Okrs_Equipos.id_empresa = '" . $_SESSION["id_empresa"] . "' AND Okrs.anio = '" . $_SESSION["anio_fill"] . "' AND Okrs_Areas.id_area = $id_area
		$filtro_periodo
		GROUP BY Okrs.id ORDER BY Okrs.objetivo_okr ASC");

	// echo $filtro_kr."<br>";

	if (mysqli_num_rows($query) > 0) {
		while ($dataOkrsNum = mysqli_fetch_array($query)) {
			$owner = $dataOkrsNum['id_empleado'];
			$okr .= $dataOkrsNum['id'] . ",";
			$respuesta = PorOkrsConsolidados($dataOkrsNum["id"], $filtro_kr, $connect_okrs);
			$suma += $respuesta["promedio"];
			$conteo++;
			$responsables .= $respuesta["team"] . ",";
		}

		$promedio = $suma / $conteo;
	}

	$okr = trim($okr, ',');

	$nodo = array(
		"promedio" => $promedio,
		"no_okrs" => $conteo,
		"team" => $responsables,
		"owner" => $owner,
		"okr" => $okr
	);

	return $nodo;
}

function OkrsReporteUsuarioConsolidados($id_empleado, $id_empresa, $connect_okrs, $filtro)
{

	$sentencia = "
		SELECT Okrs.id as id
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		WHERE Okrs_Equipos.id_empresa = $id_empresa
		AND Okrs_Equipos.id_empleado = $id_empleado
		AND Okrs.anio = " . $_SESSION["anio_fill"] . "
		AND (Okrs_Resultados.responsables LIKE '%," . $id_empleado . "%' OR Okrs_Resultados.responsables LIKE '%" . $id_empleado . ",%' OR Okrs_Resultados.responsables IN ('" . $id_empleado . "'))
		$filtro
		GROUP BY Okrs.id
		ORDER BY Okrs.id ASC
		";

	// echo $sentencia."<br>";

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	if ($query) {
		while ($data = mysqli_fetch_array($query)) {
			$obj = array(
				"id" => $data["id"]
			);
			array_push($nodos, $obj);
		}
	}

	// Verificar si el array $nodos está vacío
	if (empty($nodos)) {
		// Puedes agregar un mensaje de log o un valor que indique que no hay datos.
		$nodos[] = ["id" => "Sin OKRS para el colaborador"];
	}

	return $nodos;
}

function OkrsReporteUsuarioConsolidadosJson($id_empleado, $id_empresa, $anioFill, $connect_okrs, $filtro)
{

	$sentencia = "
		SELECT Okrs.id as id
		FROM Okrs_Equipos
		LEFT JOIN Okrs ON Okrs.id = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Areas ON Okrs_Areas.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN Okrs_Resultados ON Okrs_Resultados.id_okrs = Okrs_Equipos.id_okrs
		LEFT JOIN puntacana_admin.Empleados AS Empleados ON Empleados.id = Okrs_Equipos.id_empleado
		LEFT JOIN puntacana_admin.Empleados AS EO ON EO.id = Okrs.id_empleado
		WHERE Okrs_Equipos.id_empresa = $id_empresa
		AND Okrs_Equipos.id_empleado = $id_empleado
		AND Okrs.anio = " . $anioFill . "
		AND (Okrs_Resultados.responsables LIKE '%," . $id_empleado . "%' OR Okrs_Resultados.responsables LIKE '%" . $id_empleado . ",%' OR Okrs_Resultados.responsables IN ('" . $id_empleado . "'))
		$filtro
		GROUP BY Okrs.id
		ORDER BY Okrs.id ASC
		";

	// echo $sentencia."<br>";

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	if ($query) {
		while ($data = mysqli_fetch_array($query)) {
			$obj = array(
				"id" => $data["id"]
			);
			array_push($nodos, $obj);
		}
	}

	// Verificar si el array $nodos está vacío
	if (empty($nodos)) {
		// Puedes agregar un mensaje de log o un valor que indique que no hay datos.
		$nodos[] = ["id" => "Sin OKRS para el colaborador"];
	}

	return $nodos;
}

function KRsReporteUsuarioConsolidados($id_empleado, $id_empresa, $connect_okrs, $filtro)
{

	$sentencia = "SELECT ORE.*
		FROM Okrs_Resultados ORE
		LEFT JOIN Okrs O ON O.id = ORE.id_okrs
		WHERE (ORE.responsables LIKE '%," . $id_empleado . "%' OR ORE.responsables LIKE '%" . $id_empleado . ",%' OR ORE.responsables IN ('" . $id_empleado . "'))	AND O.anio = " . $_SESSION["anio_fill"] . "
		$filtro ";

	// echo $sentencia."<br>";

	$nodos = array();
	$query = mysqli_query($connect_okrs, $sentencia);
	while ($data = mysqli_fetch_array($query)) {
		$obj = array(
			"id" => $data["id"]
		);
		array_push($nodos, $obj);
	}
	// $obj1 = array(
	// 	"contador" => mysqli_num_rows($query)
	// );
	// array_push($nodos, $obj1);
	return $nodos;
}

function PorOkrsReporteUsuarioConsolidado($connect_okrs, $okr, $id_empleado, $filtro_claves)
{

	$filtro_kr = " ";
	if ($filtro_claves) {
		// $filtro_kr .= " AND periodo = '".$_SESSION["periodo_fill"]."' ";
		$filtro_kr .= $filtro_claves;
	}

	$suma_resultado = 0;
	$conteo_resultado = 0;
	$resultado_prom_okr = 0;
	// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr  $filtro_kr ";
	// echo "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr AND responsables LIKE '%$id_empleado%' $filtro_kr ";
	$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = $okr AND (responsables LIKE '%,$id_empleado%' OR responsables LIKE '%$id_empleado,%' OR responsables IN ('$id_empleado')) $filtro_kr ");


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
	if ($suma_resultado > 0) {
		$resultado_prom_okr = ($suma_resultado / mysqli_num_rows($queryResultados));
	} else {
		$resultado_prom_okr += 0;
	}
	$nodo = array(
		"promedio" => $resultado_prom_okr,
		"no_resultados" => $queryResultados->num_rows
	);
	// print_r($nodo);
	return $nodo;
}

function AvanceObjEstrategico($connect_okrs, $objetivo, $id_empresa, $filtro_claves)
{
	$filtro_kr = " ";
	if ($filtro_claves) {
		$filtro_kr .= $filtro_claves;
	}

	$queryEstrategico = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND tipo = 1 AND anio = " . $_SESSION["anio_fill"] . " AND (objetivos_estrategicos LIKE '%,$objetivo' OR objetivos_estrategicos IN ('$objetivo'))");
	$promedioQ1 = $promedioQ2 = $promedioQ3 = $promedioQ4 = $promedioAnual = $suma_resultado = $resultado_prom_okr = $conteo_resultado = 0;
	$sumQ1 = $sumQ2 = $sumQ3 = $sumQ4 = $sumAnual = $contQ1 = $contQ2 = $contQ3 = $contQ4 = $contAnual = 0;
	while ($dataEstrategico = mysqli_fetch_array($queryEstrategico)) {
		$queryResultados = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id_okrs = " . $dataEstrategico["id"] . " $filtro_kr ");

		while ($dataResultados = mysqli_fetch_array($queryResultados)) {
			if ($dataResultados["periodo"] == 'Q1') {
				$porcentajeQ1 = ($dataResultados["avance"] * 100) / $dataResultados["meta"];

				if ($dataResultados["tendencia"] == 2) {
					$porcentajeQ1 = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
				}

				if (is_infinite($porcentajeQ1) || is_nan($porcentajeQ1)) {
					$porcentajeQ1 = 0;
				}

				if ($porcentajeQ1 > 100) {
					$porcentajeQ1 = 100;
				}
				$sumQ1 = $sumQ1 + $porcentajeQ1;
				$contQ1++;
			}
			if ($dataResultados["periodo"] == 'Q2') {
				$porcentajeQ2 = ($dataResultados["avance"] * 100) / $dataResultados["meta"];

				if ($dataResultados["tendencia"] == 2) {
					$porcentajeQ2 = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
				}

				if (is_infinite($porcentajeQ2) || is_nan($porcentajeQ2)) {
					$porcentajeQ2 = 0;
				}

				if ($porcentajeQ2 > 100) {
					$porcentajeQ2 = 100;
				}
				$sumQ2 = $sumQ2 + $porcentajeQ2;
				$contQ2++;
			}
			if ($dataResultados["periodo"] == 'Q3') {
				$porcentajeQ3 = ($dataResultados["avance"] * 100) / $dataResultados["meta"];

				if ($dataResultados["tendencia"] == 2) {
					$porcentajeQ3 = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
				}

				if (is_infinite($porcentajeQ3) || is_nan($porcentajeQ3)) {
					$porcentajeQ3 = 0;
				}

				if ($porcentajeQ3 > 100) {
					$porcentajeQ3 = 100;
				}
				$sumQ3 = $sumQ3 + $porcentajeQ3;
				$contQ3++;
			}
			if ($dataResultados["periodo"] == 'Q4') {
				$porcentajeQ4 = ($dataResultados["avance"] * 100) / $dataResultados["meta"];

				if ($dataResultados["tendencia"] == 2) {
					$porcentajeQ4 = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
				}

				if (is_infinite($porcentajeQ4) || is_nan($porcentajeQ4)) {
					$porcentajeQ4 = 0;
				}

				if ($porcentajeQ4 > 100) {
					$porcentajeQ4 = 100;
				}
				$sumQ4 = $sumQ4 + $porcentajeQ4;
				$contQ4++;
			}
			if ($dataResultados["periodo"] == 'Anual') {
				$porcentajeAnual = ($dataResultados["avance"] * 100) / $dataResultados["meta"];

				if ($dataResultados["tendencia"] == 2) {
					$porcentajeAnual = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
				}

				if (is_infinite($porcentajeAnual) || is_nan($porcentajeAnual)) {
					$porcentajeAnual = 0;
				}

				if ($porcentajeAnual > 100) {
					$porcentajeAnual = 100;
				}
				$sumAnual = $sumAnual + $porcentajeAnual;
				$contAnual++;
			}
		}
		$promedioQ1 = $sumQ1 / $contQ1;
		$promedioQ2 = $sumQ2 / $contQ2;
		$promedioQ3 = $sumQ3 / $contQ3;
		$promedioQ4 = $sumQ4 / $contQ4;
		$promedioAnual = $sumAnual / $contAnual;
	}
	$nodo = array(
		"promedioQ1" => $promedioQ1,
		"promedioQ2" => $promedioQ2,
		"promedioQ3" => $promedioQ3,
		"promedioQ4" => $promedioQ4,
		"promedioAnual" => $promedioAnual,
	);
	// print_r($nodo);
	return $nodo;
}

function CantidadKRPeriodo($connect_okrs, $periodo, $connect_valentina)
{
	$porcentaje = $promedioQ1 = $promedioQ2 = $promedioQ3 = $promedioQ4 = $promedioQ5 = 0;
	$queryEscala = mysqli_query($connect_valentina, "SELECT * FROM Escala_Medicion WHERE id_empresa = " . $_SESSION["id_empresa"] . "");
	$dataEscala = mysqli_fetch_array($queryEscala);
	$queryResultados = mysqli_query($connect_okrs, "SELECT Okrs_Resultados.* FROM Okrs_Resultados
INNER JOIN Okrs ON Okrs.id = Okrs_Resultados.id_okrs
WHERE Okrs_Resultados.id_empresa = '" . $_SESSION['id_empresa'] . "'
AND Okrs.anio = " . $_SESSION['anio_fill'] . "
AND Okrs_Resultados.periodo = '$periodo'");
	while ($dataResultados = mysqli_fetch_array($queryResultados)) {
		$porcentaje = ($dataResultados["avance"] * 100) / $dataResultados["meta"];

		if ($dataResultados["tendencia"] == 2) {
			$porcentaje = ($dataResultados["meta"] / $dataResultados["avance"] * 100);
		}
		$a_por = round($porcentaje);
		if (is_nan($a_por) || is_infinite($a_por)) {
			$a_por = 0;
		}
		if ($a_por >= $dataEscala['porcentaje_uno'] && $a_por < $dataEscala['porcentaje_tres']) {
			$promedioQ1++;
		}
		if ($a_por >= $dataEscala['porcentaje_tres'] && $a_por < $dataEscala['porcentaje_cinco']) {
			$promedioQ2++;
		}
		if ($a_por >= $dataEscala['porcentaje_cinco'] && $a_por < $dataEscala['porcentaje_siete']) {
			$promedioQ3++;
		}
		if ($a_por >= $dataEscala['porcentaje_siete'] && $a_por <= 100) {
			$promedioQ4++;
		}
		if ($a_por > 100) {
			$promedioQ5++;
		}
	}
	$nodo = array(
		"avance1" => $promedioQ1,
		"avance2" => $promedioQ2,
		"avance3" => $promedioQ3,
		"avance4" => $promedioQ4,
		"avance5" => $promedioQ5,
	);
	// print_r($nodo);
	return $nodo;
}

function CantidadIniRPeriodo($connect_okrs, $mes, $connect_valentina)
{
	if ($mes < 13) {
		$filtroMes = "AND Okrs_Iniciativas.mes = $mes";
	} else {
		$filtroMes = "AND Okrs_Iniciativas.mes IS NULL";
	}
	$porcentaje = $promedioQ1 = $promedioQ2 = $promedioQ3 = $promedioQ4 = $promedioQ5 = 0;
	$queryEscala = mysqli_query($connect_valentina, "SELECT * FROM Escala_Medicion WHERE id_empresa = " . $_SESSION["id_empresa"] . "");
	$dataEscala = mysqli_fetch_array($queryEscala);
	$queryIniciativas = mysqli_query($connect_okrs, "SELECT Okrs_Iniciativas.* FROM Okrs_Iniciativas
	INNER JOIN Okrs_Resultados ON Okrs_Resultados.id = Okrs_Iniciativas.id_resultado
INNER JOIN Okrs ON Okrs.id = Okrs_Resultados.id_okrs
WHERE Okrs_Resultados.id_empresa = '" . $_SESSION['id_empresa'] . "'
AND Okrs.anio = " . $_SESSION['anio_fill'] . "
$filtroMes");
	while ($dataIniciativas = mysqli_fetch_array($queryIniciativas)) {
		$porcentaje = ($dataIniciativas["avance"] * 100) / $dataIniciativas["meta"];

		if ($dataIniciativas["tendencia"] == 2) {
			$porcentaje = ($dataIniciativas["meta"] / $dataIniciativas["avance"] * 100);
		}
		$a_por = round($porcentaje);
		if (is_nan($a_por) || is_infinite($a_por)) {
			$a_por = 0;
		}
		if ($a_por >= $dataEscala['porcentaje_uno'] && $a_por < $dataEscala['porcentaje_tres']) {
			$promedioQ1++;
		}
		if ($a_por >= $dataEscala['porcentaje_tres'] && $a_por < $dataEscala['porcentaje_cinco']) {
			$promedioQ2++;
		}
		if ($a_por >= $dataEscala['porcentaje_cinco'] && $a_por < $dataEscala['porcentaje_siete']) {
			$promedioQ3++;
		}
		if ($a_por >= $dataEscala['porcentaje_siete'] && $a_por <= 100) {
			$promedioQ4++;
		}
		if ($a_por > 100) {
			$promedioQ5++;
		}
	}
	$nodo = array(
		"avance1" => $promedioQ1,
		"avance2" => $promedioQ2,
		"avance3" => $promedioQ3,
		"avance4" => $promedioQ4,
		"avance5" => $promedioQ5,
	);
	// print_r($nodo);
	return $nodo;
}


function CargaLeccionesAdminVP($connect_clima, $connect_valentina, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp)
{
	$array_celula = $array_empleado = $array_area = array();
	$cont_celula = $cont_empleado = $cont_area = 0;

	$query1 = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas WHERE estado = 1 $filtro_kr $filtro_claves $filtro_area $filtro_vp AND (id_okr_estrategico IS NULL OR id_okr_estrategico = '')
AND (id_okr_organizacional IS NULL OR id_okr_organizacional = '') AND (id_okr_equipo IS NULL OR id_okr_equipo = '') AND (area IS NULL OR area = '') ORDER BY fecha_inicia DESC");
	while ($dataquery1 = mysqli_fetch_array($query1)) {
		$queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = " . $dataquery1["id_empleado"] . "");
		$dataEmpleado = mysqli_fetch_array($queryEmpleado);
		$array_celula[$cont_celula]["id"] = $dataquery1["id"];
		$array_celula[$cont_celula]["id_empresa"] = $dataquery1["id_empresa"];
		$array_celula[$cont_celula]["id_empleado"] = $dataquery1["id_empleado"];
		$array_celula[$cont_celula]["descripcion"] = $dataquery1["descripcion"];
		$array_celula[$cont_celula]["celula"] = $dataquery1["celula"];
		$array_celula[$cont_celula]["anio"] = $dataquery1["anio"];
		$array_celula[$cont_celula]["fecha_inicia"] = $dataquery1["fecha_inicia"];
		$array_celula[$cont_celula]["fecha_termina"] = $dataquery1["fecha_termina"];
		$array_celula[$cont_celula]["periodo"] = $dataquery1["periodo"];
		if ($dataquery1["area"] != "") {
			$array_celula[$cont_celula]["area"] = $dataquery1["area"];
		} else {
			$array_celula[$cont_celula]["area"] = $dataEmpleado["area"];
		}
		if ($dataquery1["id_vp"] == "") {
			$array_celula[$cont_celula]["id_vp"] = $dataEmpleado["unidad_corporativa"];
		} else {
			$array_celula[$cont_celula]["id_vp"] = $dataquery1["id_vp"];
		}
		$array_celula[$cont_celula]["estado"] = $dataquery1["estado"];
		$array_celula[$cont_celula]["fecha_publicacion"] = $dataquery1["fecha_publicacion"];
		$array_celula[$cont_celula]["created_at"] = $dataquery1["created_at"];
		$cont_celula++;
	}

	return $array_celula;
}

function CargaLeccionesLiderVP($connect_clima, $connect_valentina, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp, $id_empleado)
{
	$array_celula = $array_empleado = $array_area = array();
	$cont_celula = $cont_empleado = $cont_area = 0;

	$queryLA = mysqli_query($connect_valentina, "SELECT * FROM Lideres_Area WHERE id_area = '" . $_GET['id'] . "'  AND estado = 1");
	$lider_area = "";
	$validar = false;
	if (mysqli_num_rows($queryLA) > 0) {
		while ($dataLA = mysqli_fetch_array($queryLA)) {
			if ($id_empleado == $dataLA["id_lider"]) {
				$validar = true;
			}
		}
	}

	if ($validar == true) {
		$lider_area = "(id_empleado = $id_empleado OR celula LIKE '%," . $id_empleado . "%' OR celula LIKE '%" . $id_empleado . ",%')";
	} else {
		$lider_area = "(celula LIKE '%," . $id_empleado . "%' OR celula LIKE '%" . $id_empleado . ",%')";
	}

	$query1 = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas WHERE estado = 1 AND $lider_area $filtro_kr $filtro_claves $filtro_area $filtro_vp AND (id_okr_estrategico IS NULL OR id_okr_estrategico = '')
AND (id_okr_organizacional IS NULL OR id_okr_organizacional = '') AND (id_okr_equipo IS NULL OR id_okr_equipo = '') AND (area IS NULL OR area = '') ORDER BY fecha_inicia DESC");
	while ($dataquery1 = mysqli_fetch_array($query1)) {
		$queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = " . $dataquery1["id_empleado"] . "");
		$dataEmpleado = mysqli_fetch_array($queryEmpleado);
		$array_celula[$cont_celula]["id"] = $dataquery1["id"];
		$array_celula[$cont_celula]["id_empresa"] = $dataquery1["id_empresa"];
		$array_celula[$cont_celula]["id_empleado"] = $dataquery1["id_empleado"];
		$array_celula[$cont_celula]["descripcion"] = $dataquery1["descripcion"];
		$array_celula[$cont_celula]["celula"] = $dataquery1["celula"];
		$array_celula[$cont_celula]["anio"] = $dataquery1["anio"];
		$array_celula[$cont_celula]["fecha_inicia"] = $dataquery1["fecha_inicia"];
		$array_celula[$cont_celula]["fecha_termina"] = $dataquery1["fecha_termina"];
		$array_celula[$cont_celula]["periodo"] = $dataquery1["periodo"];
		if ($dataquery1["area"] == "") {
			$array_celula[$cont_celula]["area"] = $dataEmpleado["area"];
		} else {
			$array_celula[$cont_celula]["area"] = $dataquery1["area"];
		}
		if ($dataquery1["id_vp"] == "") {
			$array_celula[$cont_celula]["id_vp"] = $dataEmpleado["unidad_corporativa"];
		} else {
			$array_celula[$cont_celula]["id_vp"] = $dataquery1["id_vp"];
		}
		$array_celula[$cont_celula]["estado"] = $dataquery1["estado"];
		$array_celula[$cont_celula]["fecha_publicacion"] = $dataquery1["fecha_publicacion"];
		$array_celula[$cont_celula]["created_at"] = $dataquery1["created_at"];
		$cont_celula++;
	}

	return $array_celula;
}

function CargaLeccionesColaboradorVP($connect_clima, $connect_valentina, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp, $id_empleado)
{
	$array_celula = $array_empleado = $array_area = array();
	$cont_celula = $cont_empleado = $cont_area = 0;

	$query1 = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas WHERE estado = 1 AND (celula LIKE '%," . $id_empleado . "%' OR celula LIKE '%" . $id_empleado . ",%') $filtro_kr $filtro_claves $filtro_area $filtro_vp AND (id_okr_estrategico IS NULL OR id_okr_estrategico = '')
AND (id_okr_organizacional IS NULL OR id_okr_organizacional = '') AND (id_okr_equipo IS NULL OR id_okr_equipo = '') ORDER BY fecha_inicia DESC");
	while ($dataquery1 = mysqli_fetch_array($query1)) {
		$queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = " . $dataquery1["id_empleado"] . "");
		$dataEmpleado = mysqli_fetch_array($queryEmpleado);
		$array_celula[$cont_celula]["id"] = $dataquery1["id"];
		$array_celula[$cont_celula]["id_empresa"] = $dataquery1["id_empresa"];
		$array_celula[$cont_celula]["id_empleado"] = $dataquery1["id_empleado"];
		$array_celula[$cont_celula]["descripcion"] = $dataquery1["descripcion"];
		$array_celula[$cont_celula]["celula"] = $dataquery1["celula"];
		$array_celula[$cont_celula]["anio"] = $dataquery1["anio"];
		$array_celula[$cont_celula]["fecha_inicia"] = $dataquery1["fecha_inicia"];
		$array_celula[$cont_celula]["fecha_termina"] = $dataquery1["fecha_termina"];
		$array_celula[$cont_celula]["periodo"] = $dataquery1["periodo"];
		if ($dataquery1["area"] == "") {
			$array_celula[$cont_celula]["area"] = $dataEmpleado["area"];
		} else {
			$array_celula[$cont_celula]["area"] = $dataquery1["area"];
		}
		if ($dataquery1["id_vp"] == "") {
			$array_celula[$cont_celula]["id_vp"] = $dataEmpleado["unidad_corporativa"];
		} else {
			$array_celula[$cont_celula]["id_vp"] = $dataquery1["id_vp"];
		}
		$array_celula[$cont_celula]["estado"] = $dataquery1["estado"];
		$array_celula[$cont_celula]["fecha_publicacion"] = $dataquery1["fecha_publicacion"];
		$array_celula[$cont_celula]["created_at"] = $dataquery1["created_at"];
		$cont_celula++;
	}

	return $array_celula;
}

function CargaLeccionesAdminObjEst($connect_clima, $connect_valentina, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp)
{
    $id_empresa = $_SESSION["id_empresa"];
    

	$array_celula = $array_empleado = $array_area = array();
	$cont_celula = $cont_empleado = $cont_area = 0;
	$query1 = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas WHERE id_empresa = $id_empresa AND estado = 1 $filtro_kr $filtro_claves $filtro_area $filtro_vp AND (id_vp IS NULL OR id_vp = '')
AND (id_okr_organizacional IS NULL OR id_okr_organizacional = '') AND (id_okr_equipo IS NULL OR id_okr_equipo = '') AND (area IS NULL OR area = '') ORDER BY fecha_inicia DESC");
	while ($dataquery1 = mysqli_fetch_array($query1)) {
		$queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = " . $dataquery1["id_empleado"] . "");
		$dataEmpleado = mysqli_fetch_array($queryEmpleado);
		$array_celula[$cont_celula]["id"] = $dataquery1["id"];
		$array_celula[$cont_celula]["id_empresa"] = $dataquery1["id_empresa"];
		$array_celula[$cont_celula]["id_empleado"] = $dataquery1["id_empleado"];
		$array_celula[$cont_celula]["descripcion"] = $dataquery1["descripcion"];
		$array_celula[$cont_celula]["celula"] = $dataquery1["celula"];
		$array_celula[$cont_celula]["anio"] = $dataquery1["anio"];
		$array_celula[$cont_celula]["fecha_inicia"] = $dataquery1["fecha_inicia"];
		$array_celula[$cont_celula]["fecha_termina"] = $dataquery1["fecha_termina"];
		$array_celula[$cont_celula]["id_okr_estrategico"] = $dataquery1["id_okr_estrategico"];
		$array_celula[$cont_celula]["periodo"] = $dataquery1["periodo"];
		if ($dataquery1["area"] != "") {
			$array_celula[$cont_celula]["area"] = $dataquery1["area"];
		} else {
			$array_celula[$cont_celula]["area"] = $dataEmpleado["area"];
		}
		if ($dataquery1["id_vp"] == "") {
			$array_celula[$cont_celula]["id_vp"] = $dataEmpleado["unidad_corporativa"];
		} else {
			$array_celula[$cont_celula]["id_vp"] = $dataquery1["id_vp"];
		}
		$array_celula[$cont_celula]["estado"] = $dataquery1["estado"];
		$array_celula[$cont_celula]["fecha_publicacion"] = $dataquery1["fecha_publicacion"];
		$array_celula[$cont_celula]["created_at"] = $dataquery1["created_at"];
		$cont_celula++;
	}

	return $array_celula;
}

function CargaLeccionesLiderObjEst($connect_clima, $connect_valentina, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp, $id_empleado){

	$array_celula = $array_empleado = $array_area = array();
	$cont_celula = $cont_empleado = $cont_area = 0;


	$queryLA = mysqli_query($connect_valentina, "SELECT * FROM Lideres_Area WHERE id_area = '" . $_GET['id'] . "' AND estado = 1");
	$lider_area = "";
	$validar = false;
	if (mysqli_num_rows($queryLA) > 0) {
		while ($dataLA = mysqli_fetch_array($queryLA)) {
			if ($id_empleado == $dataLA["id_lider"]) {
				$validar = true;
			}
		}
	}

	if ($validar == true) {
		$lider_area = "(id_empleado = $id_empleado OR celula LIKE '%," . $id_empleado . "%' OR celula LIKE '%" . $id_empleado . ",%')";
	} else {
		$lider_area = "(celula LIKE '%," . $id_empleado . "%' OR celula LIKE '%" . $id_empleado . ",%')";
	}

	$query1 = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas WHERE estado = 1 AND $lider_area $filtro_kr $filtro_claves $filtro_area $filtro_vp  AND (area IS NULL OR area = '')
AND (id_okr_organizacional IS NULL OR id_okr_organizacional = '') AND (id_okr_equipo IS NULL OR id_okr_equipo = '') AND (id_vp IS NULL OR id_vp = '') ORDER BY fecha_inicia DESC");
	while ($dataquery1 = mysqli_fetch_array($query1)) {
		$queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = " . $dataquery1["id_empleado"] . "");
		$dataEmpleado = mysqli_fetch_array($queryEmpleado);
		$array_celula[$cont_celula]["id"] = $dataquery1["id"];
		$array_celula[$cont_celula]["id_empresa"] = $dataquery1["id_empresa"];
		$array_celula[$cont_celula]["id_empleado"] = $dataquery1["id_empleado"];
		$array_celula[$cont_celula]["descripcion"] = $dataquery1["descripcion"];
		$array_celula[$cont_celula]["celula"] = $dataquery1["celula"];
		$array_celula[$cont_celula]["anio"] = $dataquery1["anio"];
		$array_celula[$cont_celula]["fecha_inicia"] = $dataquery1["fecha_inicia"];
		$array_celula[$cont_celula]["fecha_termina"] = $dataquery1["fecha_termina"];
		$array_celula[$cont_celula]["id_okr_estrategico"] = $dataquery1["id_okr_estrategico"];
		$array_celula[$cont_celula]["periodo"] = $dataquery1["periodo"];
		if ($dataquery1["area"] == "") {
			$array_celula[$cont_celula]["area"] = $dataEmpleado["area"];
		} else {
			$array_celula[$cont_celula]["area"] = $dataquery1["area"];
		}
		if ($dataquery1["id_vp"] == "") {
			$array_celula[$cont_celula]["id_vp"] = $dataEmpleado["unidad_corporativa"];
		} else {
			$array_celula[$cont_celula]["id_vp"] = $dataquery1["id_vp"];
		}
		$array_celula[$cont_celula]["estado"] = $dataquery1["estado"];
		$array_celula[$cont_celula]["fecha_publicacion"] = $dataquery1["fecha_publicacion"];
		$array_celula[$cont_celula]["created_at"] = $dataquery1["created_at"];
		$cont_celula++;
	}

	return $array_celula;
}

function CargaLeccionesColaboradorObjEst($connect_clima, $connect_valentina, $filtro_la, $filtro_kr, $filtro_claves, $filtro_area, $filtro_vp, $id_empleado)
{
	$array_celula = $array_empleado = $array_area = array();
	$cont_celula = $cont_empleado = $cont_area = 0;

	$query1 = mysqli_query($connect_clima, "SELECT * FROM Lecciones_Aprendidas WHERE estado = 1 AND (celula LIKE '%," . $id_empleado . "%' OR celula LIKE '%" . $id_empleado . ",%') $filtro_kr $filtro_claves $filtro_area $filtro_vp AND (area IS NULL OR area = '')
AND (id_okr_organizacional IS NULL OR id_okr_organizacional = '') AND (id_okr_equipo IS NULL OR id_okr_equipo = '') AND (id_vp IS NULL OR id_vp = '') ORDER BY fecha_inicia DESC");
	while ($dataquery1 = mysqli_fetch_array($query1)) {
		$queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = " . $dataquery1["id_empleado"] . "");
		$dataEmpleado = mysqli_fetch_array($queryEmpleado);
		$array_celula[$cont_celula]["id"] = $dataquery1["id"];
		$array_celula[$cont_celula]["id_empresa"] = $dataquery1["id_empresa"];
		$array_celula[$cont_celula]["id_empleado"] = $dataquery1["id_empleado"];
		$array_celula[$cont_celula]["descripcion"] = $dataquery1["descripcion"];
		$array_celula[$cont_celula]["celula"] = $dataquery1["celula"];
		$array_celula[$cont_celula]["anio"] = $dataquery1["anio"];
		$array_celula[$cont_celula]["fecha_inicia"] = $dataquery1["fecha_inicia"];
		$array_celula[$cont_celula]["fecha_termina"] = $dataquery1["fecha_termina"];
		$array_celula[$cont_celula]["id_okr_estrategico"] = $dataquery1["id_okr_estrategico"];
		$array_celula[$cont_celula]["periodo"] = $dataquery1["periodo"];
		if ($dataquery1["area"] == "") {
			$array_celula[$cont_celula]["area"] = $dataEmpleado["area"];
		} else {
			$array_celula[$cont_celula]["area"] = $dataquery1["area"];
		}
		if ($dataquery1["id_vp"] == "") {
			$array_celula[$cont_celula]["id_vp"] = $dataEmpleado["unidad_corporativa"];
		} else {
			$array_celula[$cont_celula]["id_vp"] = $dataquery1["id_vp"];
		}
		$array_celula[$cont_celula]["estado"] = $dataquery1["estado"];
		$array_celula[$cont_celula]["fecha_publicacion"] = $dataquery1["fecha_publicacion"];
		$array_celula[$cont_celula]["created_at"] = $dataquery1["created_at"];
		$cont_celula++;
	}

	return $array_celula;
}

function DuplicarIniciativa($connect_okrs, $post, $hoy, $id_empresa, $id_user)
{
	$query1 = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Iniciativas WHERE id = '" . $post["id_registro"] . "'");
	$data1 = mysqli_fetch_array($query1);

	$sentencia1 = "
			INSERT INTO Okrs_Iniciativas (id_okrs, id_resultado, id_empleado, responsables, descripcion, mes, fecha_entrega, meta, avance, tendencia, aprobacion, created_at)
			VALUES (" . $data1["id_okrs"] . "," . $data1["id_resultado"] . "," . $post["id_empleado"] . ",'" . implode(",", $post["responsables"]) . "','" . $post["descripcion"] . "'," . $post["mes"] . ",'" . $post["fecha_entrega"] . "','" . $post["meta"] . "','" . $data1["avance"] . "','" . $post["tendencia"] . "','" . $data1["aprobacion"] . "','$hoy' )
			";

	mysqli_query($connect_okrs, $sentencia1);
	$id_tmp = mysqli_insert_id($connect_okrs);

	$queryComentarios = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Comentarios_Iniciativas WHERE id_iniciativa = '" . $post["id_registro"] . "' ");
	if (mysqli_num_rows($queryComentarios) > 0) {
		while ($dataComentarios = mysqli_fetch_array($queryComentarios)) {
			$sentenciaC = "
						INSERT INTO Okrs_Comentarios_Iniciativas (id_empresa, id_okrs, id_resultado, id_iniciativa, id_empleado, comentario, created_at)
						VALUES (" . $post["id_empresa"] . ",'" . $data1["id_okrs"] . "','" . $data1["id_resultado"] . "','" . $id_tmp . "','" . $dataComentarios["id_empleado"] . "','" . $dataComentarios["comentario"] . "','$hoy')";
			// echo $sentenciaC;
			mysqli_query($connect_okrs, $sentenciaC);
		}
	}

	$queryDocumentos = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Documentos WHERE id_iniciativa = '" . $post["id_registro"] . "' ");
	if (mysqli_num_rows($queryDocumentos) > 0) {
		while ($dataDocumentos = mysqli_fetch_array($queryDocumentos)) {
			$sentenciaD = "
						INSERT INTO Okrs_Documentos (id_empresa, id_okrs, id_resultado, id_iniciativa, id_empleado, comentario, archivo,created_at)
						VALUES (" . $post["id_empresa"] . ",'" . $data1["id_okrs"] . "','" . $data1["id_resultado"] . "','" . $id_tmp . "','" . $dataDocumentos["id_empleado"] . "','" . $dataDocumentos["comentario"] . "','" . $dataDocumentos["archivo"] . "','$hoy')";
			// echo $sentenciaC;
			mysqli_query($connect_okrs, $sentenciaD);
		}
	}

	if ($id_empresa != 1) {
		$queryNotificacion = mysqli_query($connect_okrs, "SELECT * FROM Notificacion_Iniciativas WHERE id_iniciativa = '" . $post["id_registro"] . "' ");
		if (mysqli_num_rows($queryNotificacion) > 0) {
			while ($dataNotificacion = mysqli_fetch_array($queryNotificacion)) {

				if (count($post["responsables"]) > 1) {
					foreach ($post["responsables"] as $id_resp) {
						if ($id_resp == $id_user) {
							mysqli_query($connect_okrs, "INSERT INTO Notificacion_Iniciativas (id_empresa, id_iniciativa,id_empleado,id_asignado,estado,created_at)
			VALUES(" . $id_empresa . ",$id_tmp," . $id_user . "," . $dataNotificacion["id_asignado"] . ",1,'$hoy')");
						} elseif ($id_resp == $dataNotificacion["id_asignado"]) {
							mysqli_query($connect_okrs, "INSERT INTO Notificacion_Iniciativas (id_empresa, id_iniciativa,id_empleado,id_asignado,estado,created_at)
			VALUES(" . $id_empresa . ",$id_tmp," . $id_user . "," . $dataNotificacion["id_asignado"] . ",4,'$hoy')");
						} else {
							mysqli_query($connect_okrs, "INSERT INTO Notificacion_Iniciativas (id_empresa, id_iniciativa,id_empleado,id_asignado,estado,created_at)
			VALUES(" . $id_empresa . ",$id_tmp," . $id_user . "," . $id_resp . ",4,'$hoy')");
						}
					}
				} else {
					mysqli_query($connect_okrs, "INSERT INTO Notificacion_Iniciativas (id_empresa, id_iniciativa,id_empleado,id_asignado,estado,created_at)
			VALUES(" . $id_empresa . ",$id_tmp," . $id_user . "," . $post["responsables"][0] . ",4,'$hoy')");
				}
			}
		}

		$queryAuditoria = mysqli_query($connect_okrs, "SELECT * FROM Auditoria_Iniciativas WHERE id_iniciativa = '" . $post["id_registro"] . "' ");
		if (mysqli_num_rows($queryAuditoria) > 0) {
			while ($dataAuditoria = mysqli_fetch_array($queryAuditoria)) {
				$sentenciaD = "
						INSERT INTO Auditoria_Iniciativas (id_empresa, id_empleado, id_iniciativa, accion, id_asignado, comentario, created_at)
						VALUES (" . $id_empresa . ",'" . $id_user . "','" . $id_tmp . "','" . $dataAuditoria["accion"] . "','" . $dataAuditoria["id_asignado"] . "','" . $dataAuditoria["comentario"] . "','$hoy')";
				// echo $sentenciaC;
				mysqli_query($connect_okrs, $sentenciaD);
			}
		}
	}

	$query3 = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Iniciativas WHERE id = '" . $id_tmp . "' ");
	$data3 = mysqli_fetch_array($query3);

	$query2 = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $data1["id_okrs"] . "");
	$data2 = mysqli_fetch_array($query2);

	$accion = 'CREAR';
	$descripcion = 'Creación de iniciativa: ' . $data3["descripcion"] . ' por la opción duplicar iniciativa';
	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $id_empresa . ", " . $id_user . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $data1["id_okrs"] . "," . $data1["id_resultado"] . ",$id_tmp,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);

	if ($post["decision"] == "1") {
		$query = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Actividades WHERE id_iniciativa = '" . $post["id_registro"] . "' ");
		if (mysqli_num_rows($query) > 0) {
			while ($data = mysqli_fetch_array($query)) {
				$sentencia2 = "
						INSERT INTO Okrs_Actividades (id_empresa, id_okrs, id_resultado, id_iniciativa, id_empleado, id_asignado, ciclo, descripcion, prioridad, meta, progreso, estado_backlog, fecha_inicia, fecha_entrega, checked, aprobacion, created_at)
						VALUES (" . $post["id_empresa"] . ",'" . $data1["id_okrs"] . "','" . $data1["id_resultado"] . "','" . $data3["id"] . "','" . $post["id_empleado"] . "','" . $data["id_asignado"] . "','" . $data["ciclo"] . "','" . $data["descripcion"] . "','" . $data["prioridad"] . "','" . $data["meta"] . "','" . $data["progreso"] . "','" . $data["estado_backlog"] . "','" . $data["fecha_inicia"] . "','" . $data["fecha_entrega"] . "','" . $data["checked"] . "','" . $data["aprobacion"] . "','$hoy')";
				// echo $sentencia2;
				mysqli_query($connect_okrs, $sentencia2);
			}
		}
	}
	return $id_tmp;
}

function DuplicarPlanAccion($connect_okrs, $post, $hoy, $id_empresa, $id_user)
{
	$query1 = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Actividades WHERE id = '" . $post["id_registro"] . "'");
	$data1 = mysqli_fetch_array($query1);

	$empleado = "";
	if ($post["empleado_asignado_pa_edit"] == 1) {
		$empleado = $post["emp_interno_pa_edit"];
	} else if ($post["empleado_asignado_pa_edit"] == 2) {
		$empleado = $post["emp_ext_pa_edit"];
	} else {
		$empleado = $post["id_asignado_pa_edit"];
	}

	// echo "<br>INSERT INTO Okrs_Actividades (id_empresa, id_okrs, id_resultado, id_iniciativa, id_empleado, id_asignado, ciclo, descripcion, prioridad, meta, progreso, estado_backlog, fecha_inicia, fecha_entrega, checked, aprobacion, created_at)
	// 						VALUES (" . $id_empresa . ",'" . $data1["id_okrs"] . "','" . $data1["id_resultado"] . "','" . $data1["id_iniciativa"] . "','" . $id_user . "','" . $data1["id_asignado"] . "','" . $data1["ciclo"] . "','" . $data1["descripcion"] . "','" . $data1["prioridad"] . "','" . $data1["meta"] . "','" . $data1["progreso"] . "','" . $data1["estado_backlog"] . "','" . $data1["fecha_inicia"] . "','" . $data1["fecha_entrega"] . "','" . $data1["checked"] . "','" . $data1["aprobacion"] . "','$hoy')";
	if ($empleado == $id_user) {
		$aprobacion = 1;
	} elseif ($empleado != $data1["id_asignado"]) {
		$aprobacion = '';
	} else {
		$aprobacion = $data1["aprobacion"];
	}

	$sentencia1 = "INSERT INTO Okrs_Actividades (id_empresa, id_okrs, id_resultado, id_iniciativa, id_empleado, id_asignado, ciclo, descripcion, prioridad, meta, progreso, estado_backlog, fecha_inicia, fecha_entrega, checked, aprobacion, created_at)
						VALUES (" . $id_empresa . ",'" . $data1["id_okrs"] . "','" . $data1["id_resultado"] . "','" . $data1["id_iniciativa"] . "','" . $id_user . "','" . $empleado . "','" . $post["ciclo_pa"] . "','" . $post["descripcion_pa"] . "','" . $post["prioridad_pa"] . "','" . $post["meta_pa"] . "','" . $data1["progreso"] . "','" . $data1["estado_backlog"] . "','" . $post["fecha_inicia_pa"] . "','" . $post["fecha_fin_pa"] . "','" . $data1["checked"] . "','$aprobacion','$hoy')";
	// echo $sentencia1."<br>";
	mysqli_query($connect_okrs, $sentencia1);
	$id_tmp = mysqli_insert_id($connect_okrs);

	include("app/controllers/subir_documento.php");
	if (count($_FILES['archivos']['name']) > 0) {
		$uploadSuccess = true;
		$maxFileSize = 10 * 1024 * 1024;
		foreach ($_FILES['archivos']['name'] as $key => $fileName) {
			$fileTmpName = $_FILES['archivos']['tmp_name'][$key];
			$fileSize = $_FILES['archivos']['size'][$key];

			if ($fileSize > $maxFileSize) {
				$uploadSuccess = false;
				break;
			}

			if ($uploadSuccess) {
				// print_r($_FILES['archivos'][$key]);
				$file = array(
					'name' => $_FILES['archivos']['name'][$key],
					'type' => $_FILES['archivos']['type'][$key],
					'tmp_name' => $_FILES['archivos']['tmp_name'][$key],
					'error' => $_FILES['archivos']['error'][$key],
					'size' => $_FILES['archivos']['size'][$key]
				);
				$archivo = Subir_Documento($file);

				$sentencia_doc = "
		INSERT INTO Documentos_Plan_Accion ( id_empresa , id_empleado, id_plan, archivo , comentario,  created_at )
		VALUES
		( '" . $id_empresa . "', '" . $id_user . "', '" . $id_tmp . "', '" . $archivo . "', '" . $post["comentario_plan"] . "', '" . $hoy . "' )
		";

				// echo "<br>" . $sentencia_doc;
				mysqli_query($connect_okrs, $sentencia_doc);
			}
		}
	}

	$queryComentarios = mysqli_query($connect_okrs, "SELECT * FROM Comentarios_Plan_Accion WHERE id_plan = '" . $post["id_registro"] . "' ");
	if (mysqli_num_rows($queryComentarios) > 0) {
		while ($dataComentarios = mysqli_fetch_array($queryComentarios)) {
			$sentenciaC = "
						INSERT INTO Comentarios_Plan_Accion (id_empresa, id_plan, id_empleado, comentario, created_at)
						VALUES (" . $id_empresa . ",'" . $id_tmp . "','" . $dataComentarios["id_empleado"] . "','" . $dataComentarios["comentario"] . "','$hoy')";
			// echo $sentenciaC;
			// echo $sentencia1C."<br>";
			mysqli_query($connect_okrs, $sentenciaC);
		}
	}


	if ($id_empresa != 1) {
		$queryNotificacion = mysqli_query($connect_okrs, "SELECT * FROM Notificacion_Plan_Accion WHERE id_plan = '" . $post["id_registro"] . "' ");
		if (mysqli_num_rows($queryNotificacion) > 0) {
			while ($dataNotificacion = mysqli_fetch_array($queryNotificacion)) {
				mysqli_query($connect_okrs, "INSERT INTO Notificacion_Plan_Accion (id_empresa, id_plan,id_empleado,id_asignado,estado,created_at)
			VALUES(" . $id_empresa . ",$id_tmp," . $id_user . "," . $dataNotificacion["id_asignado"] . "," . $dataNotificacion["estado"] . ",'$hoy')");
			}
		}


		if ($empleado == $id_user) {
			$id_asignado = $id_user;
			mysqli_query($connect_okrs, "INSERT INTO Notificacion_Plan_Accion (id_empresa, id_plan,id_empleado,id_asignado,estado,created_at)
			VALUES(" . $id_empresa . ",$id_tmp," . $id_user . "," . $id_asignado . ",1,'$hoy')");
		} elseif ($empleado != $data1["id_asignado"]) {
			$id_asignado = $empleado;
			mysqli_query($connect_okrs, "INSERT INTO Notificacion_Plan_Accion (id_empresa, id_plan,id_empleado,id_asignado,estado,created_at)
			VALUES(" . $id_empresa . ",$id_tmp," . $id_user . "," . $id_asignado . ",4,'$hoy')");
		} else {
			$id_asignado = $data1["id_asignado"];
			mysqli_query($connect_okrs, "INSERT INTO Notificacion_Plan_Accion (id_empresa, id_plan,id_empleado,id_asignado,estado,created_at)
			VALUES(" . $id_empresa . ",$id_tmp," . $id_user . "," . $id_asignado . ",4,'$hoy')");
		}
		// echo "INSERT INTO Notificacion_Plan_Accion (id_empresa, id_plan,id_empleado,id_asignado,estado,created_at)
		// 		// 	VALUES(" . $id_empresa . ",$id_tmp," . $id_user . "," . $id_asignado . ",4,'$hoy')<br>";


		$queryAuditoria = mysqli_query($connect_okrs, "SELECT * FROM Auditoria_Plan_Accion WHERE id_plan = '" . $post["id_registro"] . "' ");
		if (mysqli_num_rows($queryAuditoria) > 0) {
			while ($dataAuditoria = mysqli_fetch_array($queryAuditoria)) {
				$sentenciaA = "
						INSERT INTO Auditoria_Plan_Accion (id_empresa, id_empleado, id_plan, accion, id_asignado, comentario, created_at)
						VALUES (" . $id_empresa . ",'" . $id_user . "','" . $id_tmp . "','" . $dataAuditoria["accion"] . "','" . $dataAuditoria["id_asignado"] . "','" . $dataAuditoria["comentario"] . "','$hoy')";
				// echo $sentenciaA;
				mysqli_query($connect_okrs, $sentenciaA);
			}
		}

		if ($post["decision"] == 1) {
			$queryDocumentos = mysqli_query($connect_okrs, "SELECT * FROM Documentos_Plan_Accion WHERE id_plan = '" . $post["id_registro"] . "' ");
			if (mysqli_num_rows($queryDocumentos) > 0) {
				while ($dataDocumentos = mysqli_fetch_array($queryDocumentos)) {
					$sentenciaD = "
							INSERT INTO Documentos_Plan_Accion (id_empresa, id_plan, id_empleado, comentario, archivo,created_at)
							VALUES (" . $post["id_empresa"] . ",'" . $id_tmp . "','" . $post["id_empleado"] . "','" . $dataDocumentos["comentario"] . "','" . $dataDocumentos["archivo"] . "','$hoy')";
					// echo $sentenciaD;
					mysqli_query($connect_okrs, $sentenciaD);
				}
			}
		}
	}

	$query3 = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Actividades WHERE id = '" . $id_tmp . "' ");
	$data3 = mysqli_fetch_array($query3);

	$query2 = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $data1["id_okrs"] . "");
	$data2 = mysqli_fetch_array($query2);

	$accion = 'CREAR';
	$descripcion = 'Creación de plan de acción: ' . $data3["descripcion"] . ' por la opción duplicar plan de acción';
	$auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
	VALUES (" . $id_empresa . ", " . $id_user . ",'$accion','$descripcion'," . $data2["tipo"] . "," . $data1["id_okrs"] . "," . $data1["id_resultado"] . ",$id_tmp,'$hoy')";
	// echo $auditoria;
	mysqli_query($connect_okrs, $auditoria);


	return $id_tmp;
}

function convertirSeparadorDecimal($numero, $pais = null)
{
	$pais = 'CO';
	$numero = trim($numero);
    $numero = str_replace('%', '', $numero); // quitar símbolo de porcentaje

    $numero = trim($numero);

    // Si tiene punto y coma (ambos)
    if (strpos($numero, '.') !== false && strpos($numero, ',') !== false) {
        // Si ',' está después de '.' => formato europeo (1.000,25)
        if (strpos($numero, ',') > strpos($numero, '.')) {
            $numero = str_replace('.', '', $numero);     // quitar separador de miles
            $numero = str_replace(',', '.', $numero);    // usar punto como decimal
        } else {
            // Formato americano (1,000.25)
            $numero = str_replace(',', '', $numero);
        }
    }

    // Solo coma
    elseif (strpos($numero, ',') !== false) {
        // Si parece decimal (ej: 0,3 o 77,5)
        if (preg_match('/,\d{1,2}$/', $numero)) {
            $numero = str_replace(',', '.', $numero); // decimal europeo
        } else {
            // Coma como separador de miles (100,000)
            $numero = str_replace(',', '', $numero);
        }
    }

    // Solo punto
    elseif (strpos($numero, '.') !== false) {
        // Si parece miles (ej: 100.000)
        if (preg_match('/\.\d{3}$/', $numero)) {
            if ($pais === 'CO' || $pais === 'DO') {
                $numero = str_replace('.', '', $numero); // quitar miles
            } // En EE.UU. lo dejamos como está
        }
        // Si es decimal (0.3 o 77.5) lo dejamos
    }
	return $numero;
}


function calcularProgresoAscendente($meta, $avance)
{
	$meta = floatval($meta);
	$avance = floatval($avance);

	// echo "FUNCIÓN CALCULAR PROGRESO ASCENDENTE <br>";
	// echo "Meta: ".$meta."<br>";
	// echo "Avance: ".$avance."<br>";
	// echo "(".$avance."* 100 )/".$meta;
    //SE AGREGAR EL 16 DE OCTUBRE PARA AJUSTAR UN OKR DE UN COLABORADOR
    if ($meta === 0.0 && $avance === 0.0) {
		return 100;
	}
 

	if ($avance === '' || $avance === null) {
		return 0;
	}

	if ($meta == 0) {
		return 0;
	}

	$progreso = ($avance * 100) / $meta;

    if($meta < 0){
        if($avance > $meta ){
            $progreso = 100;
        }
    }
    

	return round($progreso, 2);
}


function calcularProgresoDescendente($meta, $avance)
{
	$meta = floatval($meta);
	$avance = floatval($avance);
	// echo "FUNCIÓN CALCULAR PROGRESO DESCENDETE <br>";
	// echo "Meta: ".$meta."<br>";
	// echo "Avance: ".$avance."<br>";
	// echo "(".$meta."/ ".$avance." )* 100";

    if ($meta === 0.0 && $avance === 0.0) {
		return 100;
	}

	if ($avance === '' || $avance === null) {
		return 0;
	}

	if ($avance == 0) {
		return 0;
	}

	$progreso = ($meta / $avance) * 100;

	return round($progreso, 2);
}
