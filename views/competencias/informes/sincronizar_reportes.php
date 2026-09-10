<script>
    $(document).ready(function() {
        $('#menuCompetencias').collapse();
        $("#bt_competencias_analitica").addClass("active");
    });
</script>

<?php
include("app/models/competencias/Competencias.php");
$ClassCompetencias = new Competencias();
$dataCicloVal = $ClassCompetencias->Ciclo($user_log["id_empresa"], $_SESSION["anio_ciclo"]);

$hoy = date("Y-m-d H:i:s");

$activar_ciclo = 2;
$txt_activar = "Activar Informes";
$txt_activar_popup = "activar";

if ($dataCicloVal["activar"] == 1) {
    $activar_ciclo = $dataCicloVal["activar"];
    $txt_activar = "Desactivar Informes";
    $txt_activar_popup = "Desactivar";
}
if ($dataCicloVal["activar"] == 2) {
    $txt_activar = "Activar Informes";
    $txt_activar_popup = "activar";
}

$arrayEvaluadores = array();
$queryEvaluadores = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores 
WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' ");
while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) {
    array_push($arrayEvaluadores, $dataEvaluadores);
}

//CARGAMOS COMPETENCIAS EVALUACIONES
$arrayCompetencias_Evaluaciones = array();
$queryCompetencias_Evaluaciones = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' ");
while ($dataCompetencias_Evaluaciones = mysqli_fetch_array($queryCompetencias_Evaluaciones)) {
    array_push($arrayCompetencias_Evaluaciones, $dataCompetencias_Evaluaciones);
}

//CARGAMOS CARGOS
$arrayCargos = array();
$queryCargos = mysqli_query($connect_valentina, "SELECT * FROM Cargos ");
while ($dataCargos = mysqli_fetch_array($queryCargos)) {
    array_push($arrayCargos, $dataCargos);
}

//CARGAMOS AREAS
$arrayAreas = array();
$queryAreas = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' ");
while ($dataAreas = mysqli_fetch_array($queryAreas)) {
    array_push($arrayAreas, $dataAreas);
}
?>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h3>
                        Sincronizar informes <?= $dataCicloVal["anio"]; ?> | <small>Ciclo: <?php echo $dataCicloVal["nombre"]; ?></small>
                        <input type="text" class="form-control form-control-sm float-end" id="buscar" placeholder="Buscar..." style="width:300px;">
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-pills justify-content-center">
                        <li class="nav-item">
                            <a class="nav-link " href="?pg=competencias/analitica">Vicepresidencias</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link " href="?pg=competencias/informes/areas">Áreas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?pg=competencias/informes/niveles_cargo">Niveles Cargos </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="?pg=competencias/informes/sincronizar_reportes">Sincronizar</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <?php if (!$_POST["generar_sincronizacion"]) { ?>
                        <form action="" method="post">
                            <input type="hidden" name="generar_sincronizacion" value="true">
                            Esta apunto de generar una sincronización de los reportes para este año y ciclo. Recuerde que esta sincroniación tomará los ultimos datos registrados en la plataforma. esta acción permite generar reportes de manera más rápida. ¿Está seguro? <br><br>
                            <button type="submit" class="btn btn-danger w-100" onClick="Ver_Activar_Informes()" >
                                Sincronizar
                            </button>
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- VALIDAMOS SI EXISTE UN CICLO SELECCIONADO -->
<?php if ($_SESSION['ciclo'] != "" && $_POST["generar_sincronizacion"] && $_SESSION['ciclo'] >= 21 ) { ?>

    <div class="container-fluid" style="max-width: 90%; margin: 0 auto;">
        <div class="row">

            <div class="col-md-12">

                <div class="alert alert-success text-center" role="alert" id="alert_carga" style="margin-top: 15px">
                    Estamos cargado la información. este proceso puede tardes unos segundos
                    <img src="<?php echo $url; ?>/img/spinner.gif" width="100">
                </div>

                <table class="table table-sm" style="font-size: 12px; display: none" id="tabla_contenido">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Evaluado</th>
                            <th scope="col">Cédula</th>
                            <th scope="col" style="width: 80px;">Cargo</th>
                            <th scope="col">Area</th>
                            <th scope="col"># Evaluadores</th>
                            <th scope="col"># Evaluaciones</th>
                            <th scope="col">Promedio Ponderado</th>
                            <th scope="col">Tipo Ponderación</th>
                        </tr>
                    </thead>
                    <tbody id="listado">
                        <?php
                        //mysqli_query($connect_valoracion, "DELETE FROM aa_sincronizacion WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION["anio_ciclo"] . "' AND ciclo = '" . $_SESSION["ciclo"] . "' ");
                        include("views/competencias/informes/funciones.php");

                        $count = 1;
                        //CONSULTAMOS A TODOS LOS EMPLEADOS DE ESTA EMPRESA
                        $sentencia = "
                        SELECT
                            Empleados.id AS id,
                            Empleados.nombre AS nombre,
                            Empleados.documento AS documento,
                            Cargos.nombre AS nombre_cargo,
                            Cargos.id AS id_cargo, 
                            Vicepresidencia.id AS id_viceprecidiencia,
                            Vicepresidencia.nombre AS nombre_viceprecidencia, 
                            
                            Areas.id AS id_area,
                            Areas.nombre AS nombre_area, 
                            
                            Unidad_Organizativa.id AS id_unidad_organizativa,
                            Unidad_Organizativa.unidad_organizativa AS nombre_unidad_organizativa,
                            
                            Nivel_Jerarquico.id AS id_nivel_jerarquico,
                            Nivel_Jerarquico.nombre AS nombre_nivel_jerarquico 
                            
                        FROM
                            Empleados
                        LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
                        LEFT JOIN Vicepresidencia ON Vicepresidencia.id = Empleados.unidad_corporativa 
                        LEFT JOIN Areas ON Areas.id = Empleados.area
                        LEFT JOIN Estructura_Empresa AS Unidad_Organizativa ON Unidad_Organizativa.id = Empleados.unidad_organizativa
                        LEFT JOIN Nivel_Jerarquico ON Nivel_Jerarquico.id = Empleados.nivel_jerarquico

                        WHERE
                            Empleados.estado = 1 AND Empleados.id_empresa = '" . $user_log["id_empresa"] . "'  
                        ORDER BY
                            Empleados.nombre ASC 
                        ";

                        $query = mysqli_query($connect_admin, $sentencia);
                        while ($data = mysqli_fetch_array($query)) {

                            //VALIDAMOS SI TIENE POR LO MENOS 1 EVALUACION
                            $sentencia_validar = "
                            SELECT *
                                FROM Competencias_Evaluaciones_New
                                WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND id_evaluado = '" . $data["id"] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND estado >= 2 
                            ";
                            $queryValidar = mysqli_query($connect_valoracion, $sentencia_validar);
                            if ($queryValidar->num_rows > 0) {

                                //$DATOS_GENERALES_EVALUADO = ResultadoGeneral($data["id"]);

                                //VALIDAMOS SI TIENE TODAS LAS EVALUACIONES Y EVALUADORES
                                $VALIDACION = PromedioGeneralEvaluado($data["id"], $connect_valoracion, $connect_admin); 

                                $datos_ponderar = $VALIDACION["datos_ponderar"];
                                //print_r($datos_ponderar);
                                //echo "<br>";

                                //$id_cargo = $VALIDACION["id_cargo"];

                                $datos_generales = ValidarEvaluacionesCompletas($data["id"], $connect_valoracion, $connect_admin);
                                //$datos_generales = [];

                                //print_r($datos_generales);
                                //echo "<br>";

                                //LOS EVALUADORES DE ESTE EVALUADO
                                $lista_array_evaludadores = $datos_generales["dato_evaluadores"];

                                //OBTENEMOS LOS TIPOS DE EVALUACION AGRUPADA
                                $array_agrupados = array();
                                foreach ($lista_array_evaludadores as $key => $obj_evaluador) {
                                    if ($obj_evaluador["promedio"]) {
                                        array_push($array_agrupados,  $obj_evaluador["tipo"]);
                                    }
                                }
                                $array_agrupados = array_unique($array_agrupados);

                                $array_final_grupos = array();
                                foreach ($array_agrupados as $obj) {
                                    array_push($array_final_grupos, array("tipo" => $obj, "promedio" => 0, "cantidad" => 0));
                                }

                                $id_cargo = 0;
                                $id_area = 0;
                                $id_gerencia = 0;
                                $id_nivel = 0;

                                //EVALUACIONES Y COMPETENCIAS
                                //EVALUACIONES Y COMPETENCIAS
                                $EVALUACIONES = array();
                                $COMPETENCIAS = array();

                                $id_cargo = 0;
                                $sentencia_evaluaciones = "
                                SELECT * FROM Competencias_Evaluaciones_New 
                                    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND 
                                    id_evaluado = '" . $data["id"] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND estado >= 2 ORDER BY created_at DESC 
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

                                    $id_cargo = $dataEvaluacion["id_cargo"];
                                    $id_area = $dataEvaluacion["id_area"];
                                    $id_gerencia = $dataEvaluacion["id_gerencia"];
                                    $id_nivel = $dataEvaluacion["id_nivel"];
                                }
                                $COMPETENCIAS = array_unique($COMPETENCIAS);

                                //PREPARAMOS EL OBJETO CON LAS LISTA DE EVALUADORES SETEANDO POR COMPETENCIA
                                foreach ($lista_array_evaludadores as $key => $obj_evaluador) {
                                    $lista_array_evaludadores[$key]["total_competencia"] = 0;
                                    $lista_array_evaludadores[$key]["cantidad_competencia"] = 0;
                                }


                                $CONSOLIDADO_EVALUACION = array();
                                foreach ($COMPETENCIAS as $competencia) {

                                    //$nodo_comp =  ObtenerCompetenciasConsolidadas($competencia, $COMPETENCIAS, $EVALUACIONES);

                                    $total = 0;
                                    $cantidad = 0;
                                    foreach ($EVALUACIONES as $evaluacion) {
                                        $Array_Objeto = json_decode($evaluacion["obj_evaluacion"], true);
                                        foreach ($Array_Objeto as $respuestas) {

                                            if ($respuestas["competencia"] == $competencia) {
                                                $respuestas_competencia = $respuestas["respuestas"];
                                                foreach ($respuestas_competencia as $resp) {


                                                    $total += $resp["respuesta"];
                                                    $cantidad++;

                                                    //CARGAMOS LOS DATOS POR EVALUADOR
                                                    foreach ($lista_array_evaludadores as $key => $obj_evaluador) {
                                                        if ($obj_evaluador["tipo"] == $evaluacion["tipo_evaluacion"] && $obj_evaluador["promedio"] > 0) {
                                                            $lista_array_evaludadores[$key]["total_competencia"] += $resp["respuesta"];
                                                            $lista_array_evaludadores[$key]["cantidad_competencia"]++;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }


                                    //SETEAMOS LOS GRUPOS
                                    foreach ($array_final_grupos as $nodo => $obj_evaluador) {
                                        $array_final_grupos[$nodo]["promedio"] = 0;
                                        $array_final_grupos[$nodo]["cantidad"] = 0;
                                    }

                                    //CARGAMOS EL TOTAL A LOS GRUPOS
                                    foreach ($array_final_grupos as $llave => $grupo) {

                                        foreach ($lista_array_evaludadores as $obj_evaluador) {

                                            if ($obj_evaluador["tipo"] == $grupo["tipo"] && $obj_evaluador["promedio"] != "") {

                                                $prom = $obj_evaluador["total_competencia"] / $obj_evaluador["cantidad_competencia"];

                                                if ($obj_evaluador["tipo"] == 1) {
                                                    $array_final_grupos[$llave]["promedio"] += ($prom * $datos_ponderar["auto"]) / 100;
                                                    $array_final_grupos[$llave]["cantidad"]++;
                                                }
                                                if ($obj_evaluador["tipo"] == 5) {
                                                    $array_final_grupos[$llave]["promedio"] += ($prom * $datos_ponderar["jefe"]) / 100;
                                                    $array_final_grupos[$llave]["cantidad"]++;
                                                }
                                                if ($obj_evaluador["tipo"] == 2) {
                                                    $array_final_grupos[$llave]["promedio"] += ($prom * $datos_ponderar["par"]) / 100;
                                                    $array_final_grupos[$llave]["cantidad"]++;
                                                }
                                                if ($obj_evaluador["tipo"] == 3) {
                                                    $array_final_grupos[$llave]["promedio"] += ($prom * $datos_ponderar["subalterno"]) / 100;
                                                    $array_final_grupos[$llave]["cantidad"]++;
                                                }
                                                if ($obj_evaluador["tipo"] == 4) {
                                                    $array_final_grupos[$llave]["promedio"] += ($prom * $datos_ponderar["cliente"]) / 100;
                                                    $array_final_grupos[$llave]["cantidad"]++;
                                                }
                                            }
                                        }
                                    }

                                    $promedio_total_comp = 0;
                                    //FINALMENTE SUMAMOS Y PROMEDIAMOS
                                    foreach ($array_final_grupos as $obj_resultado) {
                                        if ($obj_resultado["promedio"] > 0) {
                                            $promedio_total_comp += ($obj_resultado["promedio"] / $obj_resultado["cantidad"]);
                                        }
                                    }

                                    $promedio_compt = $promedio_total_comp;

                                    $queryNivel = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Niveles WHERE id = '" . $competencia . "' ");
                                    $dataNivel = mysqli_fetch_array($queryNivel);

                                    $queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataNivel["id_competencia"] . "' ");
                                    $dataComp = mysqli_fetch_array($queryComp);

                                    $porcentaje_comt = ($promedio_general * 100) / 5;

                                    $color_compt = '';
                                    if ($promedio_compt >= 0 && $promedio_compt <= 2.0) {
                                        $color_compt = "#FF7173";
                                    }
                                    if ($promedio_compt > 2.0 && $promedio_compt <= 3.0) {
                                        $color_compt = "#FFC03A";
                                    }
                                    if ($promedio_compt > 3.0 && $promedio_compt <= 3.75) {
                                        $color_compt = "#FFEB3B";
                                    }
                                    if ($promedio_compt > 3.75 && $promedio_compt <= 4) {
                                        $color_compt = "#8BC34A";
                                    }
                                    if ($promedio_compt > 4.5 && $promedio_compt <= 5) {
                                        $color_compt = "#8BC34A";
                                    }

                                    $porcentaje_comt = ($promedio_compt * 100) / 5;

                                    $fila = array(
                                        "promedio_g_comp" => (round($promedio_compt, 2)),
                                        "porcentaje_g_comp" => $porcentaje_comt,
                                        "nombre_g_comp" => $dataComp["nombre"],
                                        "id_g_comp" => $dataComp["id"]
                                    );

                                    array_push($CONSOLIDADO_EVALUACION, $fila);
                                }

                                //print_r($CONSOLIDADO_EVALUACION);



                                $json_comptencias = json_encode($COMPETENCIAS);
                                $json_consolidado = json_encode($CONSOLIDADO_EVALUACION);

                                

                                $nombre_completo = $data["nombre"] . " " . $data["nombre_2"] . " " . $data["apellidos"] . " " . $data["apellidos_2"];

                                $sentencia = "
                                INSERT INTO aa_sincronizacion(
                                    anio,
                                    ciclo,
                                    id_empleado,
                                    id_empresa,
                                    nombre,
                                    id_cargo,
                                    id_area,
                                    id_viceprecidiencia,
                                    id_unidad_organizativa, 
                                    id_nivel_jerarquico, 
                                    documento,
                                    evaluadores,
                                    evaluaciones,
                                    promedio,
                                    tipo,
                                    competencias,
                                    objeto_competencias,
                                    created_at
                                )
                                VALUES(
                                    '" . $_SESSION["anio_ciclo"] . "',
                                    '" . $_SESSION["ciclo"] . "',
                                    '" . $data["id"] . "',
                                    '" . $user_log["id_empresa"] . "',
                                    '" . $nombre_completo . "',
                                    '" . $data["id_cargo"] . "',
                                    '" . $data["id_area"] . "',
                                    '" . $data["id_viceprecidiencia"] . "',
                                    '" . $data["id_unidad_organizativa"] . "',
                                    '" . $data["id_nivel_jerarquico"] . "',
                                    '" . $data["documento"] . "',
                                    '" . $VALIDACION["no_evaluadores"] . "',
                                    '" . $VALIDACION["no_evaluadores_evaluacion"] . "',
                                    '" . $VALIDACION["promedio"] . "',
                                    '" . $VALIDACION["tipo_ponderacion"] . "',
                                    '" . $json_comptencias . "',
                                    '" . $json_consolidado . "',
                                    '" . $hoy . "'
                                )
                                ";

                                //echo $sentencia;

                                mysqli_query($connect_valoracion, $sentencia);

                                

                                echo '
                                <tr>
                                    <td>' . $count . '</td>
                                    <td>' . $data["nombre"] . ' ' . $data["apellidos"] . '</td>
                                    <td>' . $data["ci"] . '</td>
                                    <td>' . $data["nombre_cargo"] . '</td>
                                    <td>' . $data["nombre_area"] . '</td>   
                                    <td align="center">' . $VALIDACION["no_evaluadores"] . '</td>
                                    <td align="center">' . $VALIDACION["no_evaluadores_evaluacion"] . '</td>
                                    <td align="center">' . round($VALIDACION["promedio"], 2) . '</td>
                                    <td>' . $VALIDACION["tipo_ponderacion"] . '</td>
                                </tr>
                                ';
                                $count++;
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

<?php } else { ?>
    <div class="container-fluid mt-4" style="max-width: 90%; margin: 0 auto;">
        <div class="alert alert-success text-center" role="alert">
            Para realizar este proceso primero debe seleccionar un Ciclo. y dar click en sincronizar
        </div>
    </div>
<?php } ?>

<script>
    $(window).on('load', function() {
        $("#alert_carga").fadeOut();
        $("#tabla_contenido").fadeIn();
    });

    $("#buscar").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#listado tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
</script>