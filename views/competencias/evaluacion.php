<script>
    $(document).ready(function() {
        $('#menuCompetencias').collapse();
        $('#bt_competencias_reportes').addClass('active');
    });
</script>

<?php
function Escapar_String($string){
    $string = preg_replace("[\r]", "", $string);
    $string = preg_replace("[\n]", "<br>", $string);
    $string = str_replace("'", "", $string);
    $string = str_replace('"', '', $string);
    $string = str_replace('´', '', $string);

    return $string;
}

function QueryEmpleado($id){
    $sentencia = "
    SELECT
		Empleados.id AS id, Empleados.id_empresa AS id_empresa, Empleados.nombre AS nombre, Empleados.correo AS correo_corporativo, Empleados.correo_personal AS correo_personal, Empleados.telefono_movil AS celular, Empleados.documento AS documento, Empleados.role AS role, Empleados.estado AS id_estado,
		Empleados.foto AS foto,
		Posiciones.id AS id_posicion, Cargos.id AS id_cargo, Cargos.nombre AS nombre_cargo, Areas.nombre AS nombre_area, Areas.id AS id_area
	FROM Empleados
		LEFT JOIN Posiciones ON Empleados.id_posicion = Posiciones.id
		LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
		LEFT JOIN Areas ON Areas.id = Empleados.area
	WHERE Empleados.id > 0 AND Empleados.id = " . $id . " AND Empleados.id_empresa = '" . $_SESSION["id_empresa"] . "'
		ORDER BY Empleados.nombre ASC
	";
    return $sentencia;
}

$id_evaluacion = $_GET["id"];
$hoy = date("Y-m-d H:i:s");

//SI NO EXISTE UNA EVALUACION
if ($_GET["evaluado"] != "" && $_GET["t"] != "") { 
    $sentencia = "
    INSERT INTO Competencias_Evaluaciones_New (id_empresa, anio, id_ciclo, id_evaluado, id_evaluador, id_cargo, tipo_evaluacion,
        observaciones, mejoras, promedio, obj_evaluacion, estado, created_at, update_at)
    VALUES
        ('" . $user_log["id_empresa"] . "', '" . $_SESSION["anio_ciclo"] . "', '" . $_SESSION["ciclo"] . "', '" . $_GET["evaluado"] . "', '" . $user_log["id"] . "', '" . $user_log["id_cargo"] . "',
        '" . $_GET["t"] . "', '', '', '', '', 1, '" . $hoy . "', '" . $hoy . "' ) 
    ";
    mysqli_query($connect_valoracion, $sentencia);

    $id_tmp = mysqli_insert_id($connect_valoracion);
    echo '<script> window.location = "?pg=competencias/evaluacion&id=' . $id_tmp . '&pos=1" </script>';
}

//OBTENEMOS LOS DATOS DE LA EVALUCION EN CURSO
$queryEvaluacion = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE id = '" . $id_evaluacion . "'");
$dataEvaluacion = mysqli_fetch_array($queryEvaluacion);

if ($dataEvaluacion["id_evaluado"] == $user_log["id"]) {
    $proceso_valoracion = 1;
    $estado_final = 3;
    // $avance_evaluacion = 3;
    $queryEvaluadores = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores
		WHERE id_empleado = '" . $user_log["id"] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND anio = " . $_SESSION['anio_ciclo'] . " AND tipo != 1");
    if (mysqli_num_rows($queryEvaluadores) > 0) {
        $avance_evaluacion = 2;
    } else {
        $avance_evaluacion = 3;
    }
} else {
    $estado_final = 2;
    $proceso_valoracion = 2;
    $avance_evaluacion = 3;
}

//DATOS DEL CICLO ACTUAL
$queryCicloVal = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos
	WHERE id = '" . $_SESSION['ciclo'] . "' ");
$dataCicloVal = mysqli_fetch_array($queryCicloVal);

//PARA GUARDAR UNA RESPUESTA
//PARA GUARDAR UNA RESPUESTA
//PARA GUARDAR UNA RESPUESTA
if ($_POST["preguntas"]) {

    //1. CONSULTA TEMPORAL
    //1. CONSULTA TEMPORAL

    $queryEvaluacionTmp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE id = '" . $id_evaluacion . "'");
    $dataEvaluacionTmp = mysqli_fetch_array($queryEvaluacionTmp);
    if ($dataEvaluacionTmp["obj_evaluacion"]) {
        $objeto_respuestas_tmp = json_decode($dataEvaluacionTmp["obj_evaluacion"], true);
    } else {
        $objeto_respuestas_tmp = array();
    }

    if ($dataEvaluacionTmp["obj_evaluacion"] == "null") {
        $objeto_respuestas_tmp = array();
    }

    //ESTA PARTE DEL CODIDO ELIMINA LA RESPUESTA ANTERIOR // NUEVO CODIGO
    if ($_GET["pos"]) {
        $nuevo_arr_tmp = array();

        //$_POST["id_competencia_nivel"];
        foreach ($objeto_respuestas_tmp as $resp_tmp) {
            $obj_tmp = array(
                "competencia" => $resp_tmp["competencia"],
                "respuestas" => $resp_tmp["respuestas"],
                "observaciones" => $resp_tmp["observaciones"]
            );

            if ($_POST["id_competencia_nivel"] != $resp_tmp["competencia"]) {
                array_push($nuevo_arr_tmp, $obj_tmp);
            }
        }

        $objeto_respuestas_tmp = $nuevo_arr_tmp;
    }

    //2. ARMAMOS EL OBJETO LOCAL
    $objeto = array();
    foreach ($_POST["preguntas"] as $pregunta) {
        $respuesta = array(
            "pregunta" => $pregunta,
            "respuesta" => ($_POST["respuesta_" . $pregunta][0])
        );
        array_push($objeto, $respuesta);
    }

    //3. OBJETO CON LAS RESPUESTAS DE ESTA COMPETENCIA
    $obj_competencia = array(
        "competencia" => $_POST["id_competencia_nivel"],
        "respuestas" => $objeto,
        "observaciones" => Escapar_String($_POST["observaciones"])
    );

    array_push($objeto_respuestas_tmp, $obj_competencia);
    mysqli_query($connect_valoracion, "UPDATE Competencias_Evaluaciones_New SET obj_evaluacion = '" . json_encode($objeto_respuestas_tmp) . "', proceso_valoracion = $proceso_valoracion WHERE id = '" . $id_evaluacion . "'");

    echo '<script> window.location = "' . $url . '/?pg=competencias/evaluacion&id=' . $id_evaluacion . '&pos=' . ($_GET["pos"] + 1) . '"; </script>';
}

//FINALIZAR LA EVALUACION
//FINALIZAR LA EVALUACION
if ($_POST["terminar_evaluacion_final"] != "") {
    mysqli_query($connect_valoracion, "UPDATE Competencias_Evaluaciones_New SET observaciones = '" . Escapar_String($_POST["observaciones"]) . "', mejoras = '" . Escapar_String($_POST["mejoras"]) . "', promedio = '" . $_POST["promedio"] . "',
        estado = $estado_final, proceso_valoracion = $avance_evaluacion, update_at = '" . $hoy . "'
        WHERE id = '" . $id_evaluacion . "' ");
    $queryEvaluadores = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores
		WHERE id_empleado = '" . $dataEvaluacion["id_evaluado"] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND anio = " . $_SESSION['anio_ciclo'] . " AND id_evaluador = '" . $user_log["id"] . "' AND tipo = 5");
    if (mysqli_num_rows($queryEvaluadores) > 0) {
        mysqli_query($connect_valoracion, "UPDATE Competencias_Evaluaciones_New SET proceso_valoracion = $avance_evaluacion, update_at = '" . $hoy . "'
        WHERE id_evaluado = '" . $dataEvaluacion["id_evaluado"] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND anio = " . $_SESSION['anio_ciclo'] . " AND tipo_evaluacion = 1");
    }
    // echo '<script> window.location = "?pg=competencias/realizar_valoracion" </script>';
    echo '<script> window.location = "?pg=competencias/informes/reporte_individual&e=' . $dataEvaluacion["id_evaluado"] . '" </script>';
}

//VOLVER A PRESENTAR LA EVALUACION
//VOLVER A PRESENTAR LA EVALUACION
if ($_POST["volver_a_presentar"] != "") {
    mysqli_query($connect_valoracion, "UPDATE Competencias_Evaluaciones_New SET observaciones = '', mejoras = '', promedio = '',  obj_evaluacion = '',
        estado = 1, update_at = '" . $hoy . "'
        WHERE id = '" . $id_evaluacion . "' ");

    echo '<script> window.location = "?pg=competencias/evaluacion&id=' . $id_evaluacion . '" </script>';
}

//DATOS DE LA EVALUACION
// $queryEvaluacion = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE id = '" . $id_evaluacion . "'");
// $dataEvaluacion = mysqli_fetch_array($queryEvaluacion);
$objeto_respuestas = json_decode($dataEvaluacion["obj_evaluacion"], true);
$tipo_evaluador = $dataEvaluacion["tipo_evaluacion"];

//CODIGO PARA OBTENER EL ACUMULARO GENERAL
//CODIGO PARA OBTENER EL ACUMULARO GENERAL
//CODIGO PARA OBTENER EL ACUMULARO GENERAL
$promedio_general = 0;
foreach($objeto_respuestas as $respuesta){

    $acumulado_res_nodo = 0;
    $total_resp_nodo = 0;

    foreach($respuesta["respuestas"] as $resp){
        $acumulado_res_nodo += $resp["respuesta"];
        $total_resp_nodo ++;
    }

    $acum_nodo =  $acumulado_res_nodo/$total_resp_nodo; 

    $promedio_general += $acum_nodo;
    //echo "<br>";
}

if( $promedio_general > 0 ){
    $promedio_general = $promedio_general / count($objeto_respuestas);
    $promedio_general = round(($promedio_general *20),1);
}




//DATOS DEL EVALUADO
$queryEvaluado = mysqli_query($connect_admin, QueryEmpleado($dataEvaluacion["id_evaluado"]));
$dataEvaluado = mysqli_fetch_array($queryEvaluado);

//DATOS DEL EVALUADOR
$queryEvaluador = mysqli_query($connect_admin, QueryEmpleado($dataEvaluacion["id_evaluador"]));
// $queryEvaluador = mysqli_query($connect_admin, $sentencia);
$dataEvaluador = mysqli_fetch_array($queryEvaluador);

//TIPOS PERMITIDOS
//TIPOS PERMITIDOS
$arrayTiposPermitidos = array();
$queryN = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Tipo_Evaluador WHERE anio = '" . $_SESSION['anio_ciclo'] . "' ORDER BY id DESC ");
while ($dataN = mysqli_fetch_array($queryN)) {

    $permitir = false;
    if ($tipo_evaluador == 1) {
        if ($dataN["auto"] == 'on') {
            $permitir = true;
        }
    }
    if ($tipo_evaluador == 2) {
        if ($dataN["par"] == 'on') {
            $permitir = true;
        }
    }
    if ($tipo_evaluador == 3) {
        if ($dataN["subalterno"] == 'on') {
            $permitir = true;
        }
    }
    if ($tipo_evaluador == 4) {
        if ($dataN["cliente"] == 'on') {
            $permitir = true;
        }
    }
    if ($tipo_evaluador == 5) {
        if ($dataN["jefe"] == 'on') {
            $permitir = true;
        }
    }

    if ($permitir == true) {
        array_push($arrayTiposPermitidos, array(
            "id" => $dataN["id"],
            "tipo" => $dataN["id_tipo_competencia"],
            "auto" => $dataN["auto"],
            "jefe" => $dataN["jefe"],
            "par" => $dataN["par"],
            "subalterno" => $dataN["subalterno"],
            "cliente" => $dataN["cliente"]
        ));
    }
}
?>

<div class="container-fluid">

    <!-- RESUMEN -->
    <div class="card" style="margin-bottom: 15px">
        <div class="card-body" align="center">

            <div class="row">
                <div class="col-md-3" align="left">
                    Nombre del Evaluado: <b><?php echo $dataEvaluado["nombre"]; ?></b> <br>
                    Nombre del Evaluador: <b><?php echo $dataEvaluador["nombre"]; ?></b> <br>
                </div>
                <div class="col-md-3" align="left">
                    Cargo Evaluado: <b><?php echo $dataEvaluado["nombre_cargo"]; ?></b> <br>
                    Cargo Evaluador: <b><?php echo $dataEvaluador["nombre_cargo"]; ?></b> <br>
                </div>
                <div class="col-md-3" align="left">
                    Área Evaluado: <b><?php echo $dataEvaluado["nombre_area"]; ?></b> <br>
                    Área Evaluador: <b><?php echo $dataEvaluador["nombre_area"]; ?></b> <br>
                </div>
                <div class="col-md-3" align="left">
                    Ciclo: <b><?php echo $dataCicloVal["nombre"]; ?></b><br>
                    Fecha:<b><?php echo $dataEvaluacion["created_at"]; ?></b>
                </div>

                <div class="col-md-12" align="right">
                    <h4>
                        <div style="    background-color: #9bfffa; width: fit-content; padding: 4px 20px; border-radius: 10px; font-weight: bold;">
                        Promedio Acumulado Evaluación: <?php echo $promedio_general; ?>%
                        </div>
                    </h4>
                </div>

            </div>


        </div>
    </div>

    <?php
    $observaciones = "";
    $respuestas_obj = array();

    //1. OBTENEMOS EL PERFIL DE CARGO DEL EVALUADO CON SUS COMPETENCIAS
    //$queryPerfilCargo = mysqli_query($connect_admin,"SELECT * FROM Cargos
    //WHERE id_empresa = '".$dataEvaluacion["id_empresa"]."' AND id = '".$dataEvaluado["id_cargo"]."' ");
    $queryPerfilCargo = mysqli_query($connect_valoracion, "SELECT * FROM Perfiles_Cargos
    WHERE  id_cargo = '" . $dataEvaluado["id_cargo"] . "' AND anio = '" . $dataEvaluacion["anio"] . "' ");
    $dataPerfilCargo = mysqli_fetch_array($queryPerfilCargo);
    $PERFILES = explode(",", $dataPerfilCargo["perfiles"]);

    $competencias_count = 0;
    $count_total = 0;
    foreach ($PERFILES as $perfil) {

        if ($perfil) {
            $competencias_count++;
        }

        $queryNivelTmp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $perfil . "' ");
        $dataNivelTmp = mysqli_fetch_array($queryNivelTmp);

        $queryCompTemp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataNivelTmp["id_competencia"] . "' ");
        $dataCompTemp = mysqli_fetch_array($queryCompTemp);

        foreach ($arrayTiposPermitidos as &$tipoPermitidos) {
            if ($dataCompTemp["id_tipo"] == $tipoPermitidos["tipo"]) {
                $count_total++;
            }
        }
    }

    $PERFILES = array_reverse($PERFILES);

    $cadenalimpia_edit = "";
    $count = 1;
    $contador_preguntas = 0;
    //2. RECORREMOS LOS NIVELES DE PERFIL DE COMPETENCIAS
    //2. RECORREMOS LOS NIVELES DE PERFIL DE COMPETENCIAS
    foreach ($PERFILES as $perfil) {

        $sin_respuestas = true;
        //$nodo_respuestas;
        foreach ($objeto_respuestas as $resp_eval) {
            if ($resp_eval["competencia"] == $perfil) {
                //$nodo_respuestas = $resp_eval;
                $sin_respuestas = false;
                $cadenalimpia_edit = preg_replace("[<br>]", "\r", $resp_eval["observaciones"]);

                $cadenalimpia_edit = str_replace("u00e1", "á",  $cadenalimpia_edit);
                $cadenalimpia_edit = str_replace("u00f3", "ó",  $cadenalimpia_edit);
                $cadenalimpia_edit = str_replace("u00e9", "é",  $cadenalimpia_edit);
                $cadenalimpia_edit = str_replace("u00f1", "ñ",  $cadenalimpia_edit);
                $cadenalimpia_edit = str_replace("u00ed", "í",  $cadenalimpia_edit);
                $cadenalimpia_edit = str_replace("u00fa", "ú",  $cadenalimpia_edit);

                $observaciones = $cadenalimpia_edit;
                $respuestas_obj = $resp_eval["respuestas"];
            }
        }

        //3.OBTENEMOS LOS DATOS DEL NIVEL
        //3.OBTENEMOS LOS DATOS DEL NIVEL
        $qNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $perfil . "' ");
        $dataNivel = mysqli_fetch_array($qNivel);

        //4.OBTENEMOS LA COMPETENCIA
        //4.OBTENEMOS LA COMPETENCIA
        $qCompetencia = mysqli_query($connect_valoracion, "SELECT Competencias.id AS id, Competencias.nombre AS nombre, Competencias.definicion AS definicion,
        Competencias.pregunta_abierta AS pregunta_abierta,
        Tipos.nombre AS nombre_tipo, Competencias.id_tipo AS id_tipo
        FROM Competencias
        LEFT JOIN Tipos ON Tipos.id = Competencias.id_tipo
        WHERE Competencias.id = '" . $dataNivel["id_competencia"] . "' ");
        $dCompetencia = mysqli_fetch_array($qCompetencia);

        $mostrar = false;
        foreach ($arrayTiposPermitidos as &$tipoPermitidos) {
            if ($dCompetencia["id_tipo"] == $tipoPermitidos["tipo"]) {
                $mostrar = true;
            }
        }

        if ($mostrar == true) {

            //5. RECORREMOS LAS PREGUNTAS DE ESTE NIVEL DE COMPETENCIA
            //5. RECORREMOS LAS PREGUNTAS DE ESTE NIVEL DE COMPETENCIA
            $lista_preguntas = "";
            $count_int = 1;
            $contador_preguntas = 0;
            $queryPreguntas = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Preguntas
            WHERE id_competencia = '" . $dCompetencia["id"] . "' AND id_nivel_competencia = '" . $perfil . "' AND anio = '" . $dataEvaluacion["anio"] . "' ORDER BY id DESC ");
            while ($dataPreguntas = mysqli_fetch_array($queryPreguntas)) {

                $arrayRequired = ["required ", "required ", "required ", "required", "required", "required", "required"];
                $arrayChecked = [" ", "", "", "", "", "", ""];


                foreach ($respuestas_obj as $res_o) {
                    if ($res_o["pregunta"] == $dataPreguntas["id"]) {
                        if ($res_o["respuesta"] == 0) {
                            $arrayChecked = ["checked ", " ", " ", " ", " ", " ", " "];
                        }
                        if ($res_o["respuesta"] == 1) {
                            $arrayChecked = [" ", "checked ", " ", " ", " ", " ", " "];
                        }
                        if ($res_o["respuesta"] == 2) {
                            $arrayChecked = [" ", " ", "checked ", " ", " ", " ", " "];
                        }
                        if ($res_o["respuesta"] == 3) {
                            $arrayChecked = [" ", " ", " ", "checked ", " ", " ", " "];
                        }
                        if ($res_o["respuesta"] == 4) {
                            $arrayChecked = [" ", " ", " ", " ", "checked ", " ", " "];
                        }
                        if ($res_o["respuesta"] == 5) {
                            $arrayChecked = [" ", " ", " ", " ", " ", "checked ", " "];
                        }
                        if ($res_o["respuesta"] == 6) {
                            $arrayChecked = [" ", " ", " ", " ", " ", " ", "checked "];
                        }
                    }
                }

                $lista_preguntas .= '
                <tr title="' . $dataNivel["id"] . '">
                    <td style="padding-right: 15px;"><b>' . $count_int . '.</b> </td>
                    <td>' . eliminar_tildes($dataPreguntas["pregunta"]) . '</td>
                    <td>
                    <input type="radio" onchange="Mostrar_Porcentaje(this)" class="checkbox_list valor_por" name="respuesta_' . $dataPreguntas["id"] . '[]" value="1" ' . $arrayRequired[1] . ' ' . $arrayChecked[1] . ' >
                    </td>
                    <td>
                        <input type="radio" onchange="Mostrar_Porcentaje(this)" class="checkbox_list valor_por" name="respuesta_' . $dataPreguntas["id"] . '[]" value="2" ' . $arrayRequired[2] . ' ' . $arrayChecked[2] . ' >
                    </td>
                    <td>
                        <input type="radio" onchange="Mostrar_Porcentaje(this)" class="checkbox_list valor_por" name="respuesta_' . $dataPreguntas["id"] . '[]" value="3" ' . $arrayRequired[3] . ' ' . $arrayChecked[3] . ' >
                    </td>
                    <td>
                        <input type="radio" onchange="Mostrar_Porcentaje(this)" class="checkbox_list valor_por" name="respuesta_' . $dataPreguntas["id"] . '[]" value="4" ' . $arrayRequired[4] . ' ' . $arrayChecked[4] . ' >
                    </td>
                    <td>
                        <input type="radio" onchange="Mostrar_Porcentaje(this)" class="checkbox_list valor_por" name="respuesta_' . $dataPreguntas["id"] . '[]" value="5" ' . $arrayRequired[5] . ' ' . $arrayChecked[5] . ' >
                    </td>
                </tr>
                <input type="hidden" name="preguntas[]" value="' . $dataPreguntas["id"] . '">

                ';
                $count_int++;
                $contador_preguntas++;
            }

            if ($sin_respuestas == true) {
                $observaciones = "";
                break;
            }


            if ($_GET["pos"] == $count) {
                break;
            }

            if ($_GET["pos"] == "") {
                $observaciones = "";
            }

            $count++;
        }
    }
    ?>

    <?php
    if ($competencias_count == 0) {
        echo '
		<div class="alert alert-danger" role="alert" align="center"	>
		  <h4>No tiene competencias asignadas a su perfil</h4>
		</div>
		';
    }

    /*
    echo $count;
    echo "<br>";

    echo $count_total;
    echo "<br>";

    echo $dataEvaluacion["estado"];
    echo "<br>";

    echo $competencias_count;
    echo "<br>";

    echo $contador_preguntas;
    */
    
    
    

    ?>

    <!-- EVALAUCION SIN INICIAR -->
    <!-- RESUMEN DE LA EVALUACION -->
    <?php if ($count <= $count_total && $dataEvaluacion["estado"] == 1 && $competencias_count > 0) { ?>
        <?php include("views/competencias/layouts/escala_valoracion_pc.php"); ?>
        <div class="card" style="margin-bottom: 15px">
            <div class="card-body" align="left">
                <form action="" method="post">

                    <h4>
                        <?php echo $count . "/" . $count_total . " : " . eliminar_tildes($dCompetencia["nombre"]); ?>

                        <div style="float: right;">
                            <b><span id="promedio_esta_competencia" ></span></b>
                        </div>
                        
                    </h4>

                    <h4 style="display: none"><?php echo eliminar_tildes($dCompetencia["nombre"]); ?></h4>
                    <p><?php echo eliminar_tildes($dCompetencia["definicion"]); ?></p>
                    <div style="margin-bottom: 20px">
                        <input type="hidden" name="id_competencia_nivel" value="<?php echo $perfil; ?>">
                    </div>

                    <table width="100%">
                        <tr>
                            <td></td>
                            <td></td>
                            <td align="center" width="35"><b>1</b></td>
                            <td align="center" width="35"><b>2</b></td>
                            <td align="center" width="35"><b>3</b></td>
                            <td align="center" width="35"><b>4</b></td>
                            <td align="center" width="35"><b>5</b></td>
                        </tr>
                        <?php echo eliminar_tildes($lista_preguntas); ?>
                    </table>

                    <div style="margin: 10px"><?php echo eliminar_tildes($dCompetencia["pregunta_abierta"]); ?></div>
                    <textarea class="form-control" name="observaciones" placeholder="En esta área si lo deseas puedes ampliar la respuesta de alguna de las afirmaciones realizadas arriba, coloca el número de la pregunta y detalla tu comentario..."><?php echo $observaciones; ?></textarea>


                    <table width="100%">
                        <tr>
                            <td width="50%">
                                <?php if ($count > 1) { ?>
                                    <div align="center" style="margin: 10px">

                                        <a href="<?php echo $url; ?>/?pg=competencias/evaluacion&id=<?php echo $id_evaluacion; ?>&pos=<?php echo ($count - 1); ?>">
                                            <button type="button" class="btn btn-success btn-block w-100">
                                                Anterior
                                            </button>
                                    </div>
                                <?php } ?>
                            </td>
                            <td width="50%">
                                <div align="center" style="margin: 10px">
                                    <button type="submit" class="btn btn-primary w-100">
                                        Siguiente
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </table>


                </form>

            </div>
        </div>

        <script>
            //ANTERIOR
            // 
            /*
            var total_modulos = <?php echo $count_modulo; ?>;

            
            function Anterior() {

                id = $("#modulo_visible").val();
                if (id == 0) {
                    id = total_modulos;
                }
                id = id - 1;

                if (id >= 1) {
                    $(".modulo").hide();
                    $("#modulo_" + id).show();
                    $("#modulo_visible").val(id);
                }
            }
            */

            $(document).ready(function() {
                id = $("#modulo_visible").val();
                $("#modulo_" + id).show();
            });
        </script>
    <?php } ?>

    <!-- EVALUACION EN PROCESO -->
    <!-- EVALUACION EN PROCESO -->
    <!-- EVALUACION EN PROCESO -->
    <?php if ($count > $count_total && $dataEvaluacion["estado"] == 1 && $competencias_count > 0) { ?>
        <div class="card" style="margin-bottom: 15px">
            <div class="card-body" align="left">
                <form action="" method="post">

                    <?php
                    $promedio = 0;
                    $cant_respuestas = 0;
                    foreach ($objeto_respuestas as $resp_eval) {
                        foreach ($resp_eval["respuestas"] as $nodo_resp) {
                            if ($nodo_resp["respuesta"] > 0) {
                                $promedio += $nodo_resp["respuesta"];
                                $cant_respuestas++;
                            }
                        }
                    }
                    $promedio = round(($promedio / $cant_respuestas), 2);
                    ?>
                    <input type="hidden" name="terminar_evaluacion_final" value="true">
                    <input type="hidden" name="promedio" value="<?php echo $promedio; ?>">

                    <h2>
                        ¡Muchas gracias por tu tiempo!, con la realización de este ejercicio estás contribuyendo de manera importante con el desarrollo del talento propio y de otros.
                    </h2><br><br>

                    <p>
                        Recuerda que después de enviadas estas calificaciones, no se podrán modificar ni borrar, por lo tanto, es importante que hayas realizado esta valoración de manera consciente y objetiva. Marca la casilla que está a continuación si estás de acuerdo con las calificaciones y observaciones que asignaste. Sin embargo, si tienes dudas o ves necesario revisar algo, puedes devolverte para ajustar la evaluación.<br>
                        <br>

                        Una vez finalices, puedes volver al menú principal para revisar el avance y continuar si es necesario, con otra evaluación.

                    </p>


                    <!-- <div style="margin: 20px 0px;">
                            Detalla a continuación áreas de Fortalezas y áreas de Mejora que te gustaría compartir con tu evaluado.
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <label>Áreas de Fortalezas *</label>
                                <textarea class="form-control" name="observaciones" required><?php //echo $dataEvaluacion["observaciones"];
                                                                                                ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label>Áreas de Mejora *</label>
                                <textarea class="form-control" name="mejoras" required><?php //echo $dataEvaluacion["mejoras"];
                                                                                        ?></textarea>
                            </div>
                        </div> -->






                    <div style="margin: 20px 0px; display: none">
                        Recuerde que después de enviadas estas calificaciones no se podrán modificar ni borrar, por lo tanto es importante que usted haya realizado esta valoración de manera consciente y objetiva. Marque la casilla a continuación si está de acuerdo con la valoracion realizada y con las calificaciones que asigno a cada uno de los comportamientos de las competencias.<br><br>
                    </div>

                    <!-- <div style="margin: 20px 0px;">
                Selecciona la siguiente casilla para poder terminar la evaluación:  <input type="checkbox" class="checkbox_list" required>
            </div> -->

                    <table width="100%">
                        <tr>
                            <td width="50%">
                                <div align="center" style="margin: 10px">
                                    <a href="<?php echo $url; ?>/?pg=competencias/evaluacion&id=<?php echo $id_evaluacion; ?>&pos=<?php echo ($count - 1); ?>">
                                        <button type="button" class="btn btn-success w-100">
                                            Anterior
                                        </button>
                                </div>
                            </td>
                            <td width="50%">
                                <div align="center" style="margin: 10px">
                                    <button type="submit" class="btn btn-primary w-100">
                                        Terminar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </table>




                </form>
            </div>
        </div>
    <?php } ?>

    <!-- RESUMEN DE LA EVALUACION -->
    <!-- RESUMEN DE LA EVALUACION -->
    <!-- RESUMEN DE LA EVALUACION -->
    <?php if ($dataEvaluacion["estado"] >=2) { ?>
        <div class="card" style="margin-bottom: 15px">
            <div class="card-body" align="left">


                <h3><?php echo $IDIOMA["evaluacion_resumen_evaluacion"]; ?></h3>

                <table class="table table-bordered">
                    <?php
                    foreach ($objeto_respuestas as $resp_eval) {
                        $qNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $resp_eval["competencia"] . "' ");
                        $dataNivel = mysqli_fetch_array($qNivel);

                        //4.OBTENEMOS LA COMPETENCIA
                        //4.OBTENEMOS LA COMPETENCIA
                        $qCompetencia = mysqli_query($connect_valoracion, "SELECT Competencias.id AS id, Competencias.nombre AS nombre, Competencias.definicion AS definicion, Tipos.nombre AS nombre_tipo
            FROM Competencias
            LEFT JOIN Tipos ON Tipos.id = Competencias.id_tipo
            WHERE Competencias.id = '" . $dataNivel["id_competencia"] . "' ");
                        $dCompetencia = mysqli_fetch_array($qCompetencia);

                        $lista_preg = '';
                        foreach ($resp_eval["respuestas"] as $respuesta) {
                            $queryPregunta = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Preguntas
                WHERE id = '" . $respuesta["pregunta"] . "' ");
                            $dataPregunta = mysqli_fetch_array($queryPregunta);

                            $respuesta_txt = $respuesta["respuesta"];
                            if ($respuesta_txt == 0) {
                                $respuesta_txt = "n/a";
                            }

                            $lista_preg .= '
                <tr>
                    <td>' . $dataPregunta["pregunta"] . '</td>
                    <td>' . $respuesta_txt . '</td>
                </tr>
                ';
                        }

                        $cadenalimpia_edit = preg_replace("[<br>]", "\r", $resp_eval["observaciones"]);

                        $cadenalimpia_edit = str_replace("u00e1", "á",  $cadenalimpia_edit);
                        $cadenalimpia_edit = str_replace("u00f3", "ó",  $cadenalimpia_edit);
                        $cadenalimpia_edit = str_replace("u00e9", "é",  $cadenalimpia_edit);
                        $cadenalimpia_edit = str_replace("u00f1", "ñ",  $cadenalimpia_edit);
                        $cadenalimpia_edit = str_replace("u00ed", "í",  $cadenalimpia_edit); 
                        $cadenalimpia_edit = str_replace("u00fa", "ú",  $cadenalimpia_edit);

                        $observaciones = $cadenalimpia_edit;

                        echo '
                <tr bgcolor="#efefef">
                    <td colspan="2"><h4>' . $dCompetencia["nombre"] . '</h4></td>
                </tr>
                ' . $lista_preg . '
                <tr>
                    <td colspan="2">Observaciones: <b>' . $observaciones . '</b></td>
                </tr>
            ';
                    }
                    ?>
                </table>

                <div>
                    <b>
                        <h4>Áreas de Fortalezas:</h4>
                    </b>
                    <?php echo $dataEvaluacion["observaciones"]; ?><br><br>

                    <b>
                        <h4>Áreas de Mejoras:</h4>
                    </b>
                    <?php echo $dataEvaluacion["mejoras"]; ?><br><br>
                </div>

                <div align="right" style="display: none">
                    <form action="" method="post">
                        <input type="hidden" value="true" name="volver_a_presentar">
                        <button type="submit" class="btn btn-danger">
                            Volver a realizar esta evaluación
                        </button>
                    </form>
                </div>

            </div>
        </div>
    <?php } ?>

</div>

<style>
    .checkbox_list {
        width: 18px;
        height: 18px;
        margin-left: 8px;
    }
</style>

<script>

    function ConvertirProcentaje(){

    }
    function Mostrar_Porcentaje(element){

        cantidad_pregunta = <?php echo $contador_preguntas; ?>;
        total_resp = 0;
        $('.valor_por:checked').each(function() {

            valor_select = $(this).val()*20;
            total_resp += valor_select;
            //console.log(valor_select);
        });

        porcentaje_final = 0;
        if(total_resp > 0){
            porcentaje_final = Math.round(total_resp / cantidad_pregunta);
        }

        $("#promedio_esta_competencia").html("Promedio competencia: "+porcentaje_final+"%");

        //console.log(porcentaje_final);

        //
        //valor = $(element).val()*20;



        //console.log(valor);




    }   

$( document ).ready(function() {
    Mostrar_Porcentaje(null)
});
</script>