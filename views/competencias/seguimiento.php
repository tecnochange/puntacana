<script>
	$(document).ready(function() {
		$('#menuCompetencias').collapse();
		$('#bt_competencias_reportes').addClass('active');
	});
</script>

<?php
$filtros = "";
if($_POST["fill_tipo_evaluacion"]){
    $filtros .= " AND Evaluadores.tipo = '".$_POST["fill_tipo_evaluacion"]."' ";
}

if($_POST["fill_vicepresidencia"]){
    $filtros .= " AND Empleados.unidad_corporativa = '".$_POST["fill_vicepresidencia"]."' ";
}

$queryCicloVal = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id = '" . $_SESSION['ciclo'] . "' ");
$dataCicloVal = mysqli_fetch_array($queryCicloVal);
include("views/competencias_pc/informes/funciones.php");

//PLANTA DE PERSONAL
$sentencia_empleados = "
SELECT
	Empleados.id, Empleados.nombre, Empleados.documento,
    Cargos.nombre AS nombre_cargo, 
    Vicepresidencia.nombre AS nombre_vicepresidencia, 
    Areas.nombre AS nombre_area 
FROM
    Empleados
    LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo 
    LEFT JOIN Vicepresidencia ON Vicepresidencia.id = Empleados.unidad_corporativa 
    LEFT JOIN Areas ON Areas.id = Empleados.area
WHERE
    Empleados.id_empresa = '".$user_log["id_empresa"]."' 
ORDER BY
    Empleados.nombre ASC;
";
$queryEmp = mysqli_query($connect_admin, $sentencia_empleados );
while($dataEmp = mysqli_fetch_array($queryEmp)){
    $array_empleados[$dataEmp["id"]] = $dataEmp;
}

//TODAS LAS EVALUACIONES DE ESTE CICLO
$array_evaluaciones = array();
$sentencia_evaluaciones_realizadas = "
SELECT
    Competencias_Evaluaciones_New.id_evaluado,  Competencias_Evaluaciones_New.id_evaluador, 
    Competencias_Evaluaciones_New.promedio, Competencias_Evaluaciones_New.estado, 
    Competencias_Evaluaciones_New.proceso_valoracion
FROM
    Competencias_Evaluaciones_New
WHERE
    Competencias_Evaluaciones_New.id_ciclo = '". $_SESSION['ciclo']."' AND Competencias_Evaluaciones_New.anio = '". $_SESSION['anio_ciclo']."'
";
$queryEvaluaciones = mysqli_query($connect_valoracion, $sentencia_evaluaciones_realizadas );
while($dataEvaluaciones = mysqli_fetch_array($queryEvaluaciones)){
    array_push($array_evaluaciones, $dataEvaluaciones );
}

$ARRAY_MATRIZ = array(
    "auto" => array( "programadas" => 0, "realizadas" => 0, "en_progreso" => 0, "sin_iniciar" => 0 , "avance" => 0 ),
    "lider" => array( "programadas" => 0, "realizadas" => 0, "en_progreso" => 0, "sin_iniciar" => 0, "avance" => 0  ),
    "par" => array( "programadas" => 0, "realizadas" => 0, "en_progreso" => 0, "sin_iniciar" => 0, "avance" => 0  ),
    "colaborador" => array( "programadas" => 0, "realizadas" => 0, "en_progreso" => 0, "sin_iniciar" => 0, "avance" => 0  ),
    "total" => array( "programadas" => 0, "realizadas" => 0, "en_progreso" => 0, "sin_iniciar" => 0 , "avance" => 0 ),
);


$COUNT_SIN_VALORACION = 0;
$COUNT_EN_PROCESO = 0;
$COUNT_PRESENTADAS = 0;


$ENTREVISTA_PDI = 0;
$ENVIO_PDI = 0;
$FIRMA_APROBACION = 0;
$INTERVESION_GH = 0;
$PDI_APROBADO_CERRADO = 0;
$CERRADO_GH = 0;

//TODAS LAS EVALUACIONES DE ESTE CICLO
$array_evaluadores = array();
$sentencia_evaluadores = "
SELECT 
    Evaluadores.id_empleado, Evaluadores.id_evaluador, Evaluadores.tipo, 
    Empleados.nombre, Empleados.documento, Empleados.correo 
FROM
    Evaluadores 
    LEFT JOIN goforagile_admin.Empleados AS Empleados ON Empleados.id = Evaluadores.id_empleado
WHERE
    Evaluadores.id_ciclo = '". $_SESSION['ciclo']."' AND Evaluadores.anio = '". $_SESSION['anio_ciclo']."'  
    ".$filtros."
    ORDER BY Empleados.nombre ASC;
";
$query = mysqli_query($connect_valoracion, $sentencia_evaluadores );
while($data = mysqli_fetch_array($query)){

    $data_evaluado = $array_empleados[$data["id_empleado"]];
    $data_evaluador = $array_empleados[$data["id_evaluador"]];

    //DATOS EVALUADO
    $data["cargo_evaluado"] = $data_evaluado["nombre_cargo"];
    $data["vice_evaluado"] = $data_evaluado["nombre_vicepresidencia"]; 
    $data["area_evaluado"] = $data_evaluado["nombre_area"];

    //DATOS EVALUADOR
    $data["doc_evaluador"] = $data_evaluador["documento"];
    $data["nombre_evaluador"] = $data_evaluador["nombre"];
    
    $txt_tipo = "";
    $color_tipo = "";
    foreach($array_Tipo_Colaborador as $tipo){
        if($tipo[0] == $data["tipo"] ){
            $txt_tipo = $tipo[1];
            $color_tipo = $tipo[2];
        }
    }

    $data["txt_tipo"] = $txt_tipo ;
    $data["color_tipo"] = $color_tipo ;

    //BUSCAMOS LA EVALUACION
    $data_evaluacion = [];
    $sin_valoracion = false;
    foreach ($array_evaluaciones as $evaluacion) {
        if($evaluacion["id_evaluado"] == $data["id_empleado"] && $evaluacion["id_evaluador"] == $data["id_evaluador"] ){
            $data_evaluacion = $evaluacion;
            $sin_valoracion = true;
            break;
        }
    }


    //TODOS LOS ESTADOS
    //TODOS LOS ESTADOS
    //TODOS LOS ESTADOS
    //TODOS LOS ESTADOS
    //TODOS LOS ESTADOS
    $estado_proceso = $data_evaluacion["proceso_valoracion"];

    if($estado_proceso == 3){ $ENTREVISTA_PDI++; }
    if($estado_proceso == 4){ $ENVIO_PDI++; }
    if($estado_proceso == 5){ $FIRMA_APROBACION++; }
    if($estado_proceso == 6){ $INTERVESION_GH++; }
    if($estado_proceso == 7){ $PDI_APROBADO_CERRADO++; }
    if($estado_proceso == 8){ $CERRADO_GH++; }

    $txt_proceso_valoracion = "";
    $color_proceso_valoracion = '';
    foreach( $Array_Proceso_Valoracion as $provalora){
        if($estado_proceso == $provalora[0] ){
            $txt_proceso_valoracion = $provalora[1];
            $color_proceso_valoracion = $provalora[2];
        }
    }

    $data["txt_proceso_valoracion"] = $txt_proceso_valoracion;
    $data["color_proceso_valoracion"] = $color_proceso_valoracion;

    //TODOS LOS ESTADOS
    //TODOS LOS ESTADOS
    //TODOS LOS ESTADOS
    //TODOS LOS ESTADOS


    $data["promedio"] = $data_evaluacion["promedio"];
    $data["estado_eval"] = $data_evaluacion["estado"];
    $data["txt_estado_eval"] = "Pendiente";

    if($sin_valoracion == false){
        $COUNT_SIN_VALORACION++;
        //CARGAMOS DATOS A LA MATRIZ
        if($data["tipo"] == 1 ){
            $ARRAY_MATRIZ["auto"]["sin_iniciar"] ++;
        }
        if($data["tipo"] == 5 ){
            $ARRAY_MATRIZ["lider"]["sin_iniciar"] ++;
        }
        if($data["tipo"] == 2 ){
            $ARRAY_MATRIZ["par"]["sin_iniciar"] ++;
        }
        if($data["tipo"] == 3 ){
            $ARRAY_MATRIZ["colaborador"]["sin_iniciar"] ++;
        }
        $ARRAY_MATRIZ["total"]["sin_iniciar"] ++;
    }

    if($data_evaluacion["estado"] == 1 ||  $data_evaluacion["estado"] == 1 ){
        $data["txt_estado_eval"] = "En Proceso";
        $COUNT_EN_PROCESO++;

        //CARGAMOS DATOS A LA MATRIZ
        if($data["tipo"] == 1 ){
            $ARRAY_MATRIZ["auto"]["en_progreso"] ++;
        }
        if($data["tipo"] == 5 ){
            $ARRAY_MATRIZ["lider"]["en_progreso"] ++;
        }
        if($data["tipo"] == 2 ){
            $ARRAY_MATRIZ["par"]["en_progreso"] ++;
        }
        if($data["tipo"] == 3 ){
            $ARRAY_MATRIZ["colaborador"]["en_progreso"] ++;
        }
        $ARRAY_MATRIZ["total"]["en_progreso"] ++;
    }
    if($data_evaluacion["estado"] >= 2){
        $data["txt_estado_eval"] = "Presentanda";
        $COUNT_PRESENTADAS++;
        //CARGAMOS DATOS A LA MATRIZ
        if($data["tipo"] == 1 ){
            $ARRAY_MATRIZ["auto"]["realizadas"] ++;
        }
        if($data["tipo"] == 5 ){
            $ARRAY_MATRIZ["lider"]["realizadas"] ++;
        }
        if($data["tipo"] == 2 ){
            $ARRAY_MATRIZ["par"]["realizadas"] ++;
        }
        if($data["tipo"] == 3 ){
            $ARRAY_MATRIZ["colaborador"]["realizadas"] ++;
        }

        $ARRAY_MATRIZ["total"]["realizadas"] ++;
    }

    //CARGAMOS DATOS A LA MATRIZ
    if($data["tipo"] == 1 ){
        $ARRAY_MATRIZ["auto"]["programadas"] ++;
    }
    if($data["tipo"] == 5 ){
        $ARRAY_MATRIZ["lider"]["programadas"] ++;
    }
    if($data["tipo"] == 2 ){
        $ARRAY_MATRIZ["par"]["programadas"] ++;
    }
    if($data["tipo"] == 3 ){
        $ARRAY_MATRIZ["colaborador"]["programadas"] ++;
    }

    //TOTALES
    $ARRAY_MATRIZ["total"]["programadas"] ++;
    


    array_push($array_evaluadores, $data);
}

if($ARRAY_MATRIZ["auto"]["realizadas"] > 0){
    $ARRAY_MATRIZ["auto"]["avance"] = round(( $ARRAY_MATRIZ["auto"]["realizadas"]*100/$ARRAY_MATRIZ["auto"]["programadas"]),1);
}
if($ARRAY_MATRIZ["lider"]["realizadas"] > 0){
    $ARRAY_MATRIZ["lider"]["avance"] = round(( $ARRAY_MATRIZ["lider"]["realizadas"]*100/$ARRAY_MATRIZ["lider"]["programadas"]),1);
}
if($ARRAY_MATRIZ["par"]["realizadas"] > 0){
    $ARRAY_MATRIZ["par"]["avance"] = round(( $ARRAY_MATRIZ["par"]["realizadas"]*100/$ARRAY_MATRIZ["par"]["programadas"]),1);
}
if($ARRAY_MATRIZ["colaborador"]["realizadas"] > 0){
    $ARRAY_MATRIZ["colaborador"]["avance"] = round(( $ARRAY_MATRIZ["colaborador"]["realizadas"]*100/$ARRAY_MATRIZ["colaborador"]["programadas"]),1);
}
if($ARRAY_MATRIZ["total"]["realizadas"] > 0){
    $ARRAY_MATRIZ["total"]["avance"] = round(( $ARRAY_MATRIZ["total"]["realizadas"]*100/$ARRAY_MATRIZ["total"]["programadas"]),1);
}
     
//CONTADORES GENERALES
$TOTAL_GENERAL = $query->num_rows;
$POR_SIN_VALORACION = 0;
$POR_EN_PROCESO = 0;
$POR_PRESENTADAS = 0;

if($COUNT_SIN_VALORACION > 0){
    $POR_SIN_VALORACION = $COUNT_SIN_VALORACION*100/$TOTAL_GENERAL;
    $POR_SIN_VALORACION = round($POR_SIN_VALORACION,2);
}

if($COUNT_EN_PROCESO > 0){
    $POR_EN_PROCESO = $COUNT_EN_PROCESO*100/$TOTAL_GENERAL;
    $POR_EN_PROCESO = round($POR_EN_PROCESO,2);
}

if($COUNT_PRESENTADAS > 0){
    $POR_PRESENTADAS = $COUNT_PRESENTADAS*100/$TOTAL_GENERAL;
    $POR_PRESENTADAS = round($POR_PRESENTADAS,2);
}



//$COUNT_EN_PROCESO = 0;
//$COUNT_PRESENTADAS = 0;

//PROCENTAJES DE LOS ESTADOS DEL PROCESO DE VALORACION
$POR_ENTREVISTA_PDI = 0;
$POR_ENVIO_PDI = 0;
$POR_FIRMA_APROBACION = 0;
$POR_INTERVESION_GH = 0;
$POR_PDI_APROBADO_CERRADO = 0;
$POR_CERRADO_GH = 0;

if($ENTREVISTA_PDI > 0){
    $POR_ENTREVISTA_PDI = $ENTREVISTA_PDI*100/$COUNT_PRESENTADAS;
    $POR_ENTREVISTA_PDI = round($POR_ENTREVISTA_PDI,2);
}
if($ENVIO_PDI > 0){
    $POR_ENVIO_PDI = $ENVIO_PDI*100/$COUNT_PRESENTADAS;
    $POR_ENVIO_PDI = round($POR_ENVIO_PDI,2);
}
if($FIRMA_APROBACION > 0){
    $POR_FIRMA_APROBACION = $FIRMA_APROBACION*100/$COUNT_PRESENTADAS;
    $POR_FIRMA_APROBACION = round($POR_FIRMA_APROBACION,2);
}
if($INTERVESION_GH > 0){
    $POR_INTERVESION_GH = $INTERVESION_GH*100/$COUNT_PRESENTADAS;
    $POR_INTERVESION_GH = round($POR_INTERVESION_GH,2);
}
if($PDI_APROBADO_CERRADO > 0){
    $POR_PDI_APROBADO_CERRADO = $PDI_APROBADO_CERRADO*100/$COUNT_PRESENTADAS;
    $POR_PDI_APROBADO_CERRADO = round($POR_PDI_APROBADO_CERRADO,2);
}
if($CERRADO_GH > 0){
    $POR_CERRADO_GH = $CERRADO_GH*100/$COUNT_PRESENTADAS;
    $POR_CERRADO_GH = round($POR_CERRADO_GH,2);
}

?>



<style>
    .bg-goforagile {
        background-color: #59008e !important;
    }

    .bg-proceso {
        background-color: #f76d6b !important;
    }

    .bg-proceso1 {
        background-color: #7f38c2 !important;
    }

    .bg-proceso2 {
        background-color: #748eff !important;
    }

    .bg-proceso3 {
        background-color: #fcdb58 !important;
    }

    .bg-proceso4 {
        background-color: #59008e !important;
    }

    .bg-proceso5 {
        background-color: #5cdc53 !important;
    }

    .bg-proceso6 {
        background-color: #fb924e !important;
    }

    .bg-proceso7 {
        background-color: #64f456 !important;
    }

    .small-box {
        border-radius: .25rem;
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        display: block;
        margin-bottom: 20px;
        position: relative;
        color: white !important;
    }

    .small-box>.inner {
        padding: 10px;
    }

    .small-box .icon {
        color: rgba(0, 0, 0, .15);
        z-index: 0;
    }

    .col-lg-2 .small-box h3,
    .col-md-2 .small-box h3,
    .col-xl-2 .small-box h3 {
        font-size: 2.2rem;
        color: black;
    }

    .small-box p {
        font-size: 1rem;
        color: black;
    }

    .small-box>.small-box-footer {
        background-color: rgba(0, 0, 0, .1);
        color: rgba(255, 255, 255, .8);
        display: block;
        padding: 3px 0;
        position: relative;
        text-align: center;
        text-decoration: none;
        z-index: 10;
    }
</style>

<style>
    .interno{
        color: #ffffff;
    }
    .interno h3{
        color: #ffffff !important;
    }

    /*
    NUEVOS ESTILOS
    */
    .titulos_contador{
        font-size: 20px;
        font-weight: bold;
        line-height: 21px;
        margin-bottom: 10px;
    }
    .contador_grande{
        font-size: 40px;
        font-weight: bold;
        font-family:Poppins-ExtraBold;
    }
    .total_contador{
        font-weight: bold;
        color: #807a7a;
    }

    .item_verde{
        color: #8bc34a;
        font-weight: bold;
    }

    .item_naranja{
        color: #ffc107;
        font-weight: bold;
    }

    .item_rojo{
        color: #dc3545;
        font-weight: bold;
    }
    .baged_contador{
        color: #ffffff;
        background-color: #8BC34A;
        text-align: center;
        border-radius: 20px;
    }

</style>


<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-body">
            <h5>Ciclo: <b><?php echo $dataCicloVal["nombre"]; ?></b></h5>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <ul class="nav nav-pills justify-content-center" style="margin-bottom: 10px;">
                <li class="nav-item">
                    <a class="nav-link active " href="?pg=competencias/seguimiento_new">Seguimiento Valoración</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link " href="?pg=competencias/formularios">Formularios Valoración</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="mb-3">Este reporte muestra el total de evaluadores programados para esta evaluación. incluye personal activo e inactivo</div>
            <div class="row">
                


                <div class="col-md-3">
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="titulos_contador">TOTAL VALORACIONES PROGRAMADAS</div>
                            <div class="contador_grande" style="color: #0351ad;"><?php echo $TOTAL_GENERAL; ?></div>
                            <div>100% del total</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="titulos_contador">PRESENTADAS</div>
                            <div class="contador_grande" style="color: #8bc34a;"><?= $COUNT_PRESENTADAS ?></div>
                            <div class="total_contador"><?php echo $POR_PRESENTADAS; ?>% del total</div>
                            <div>
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $POR_PRESENTADAS; ?>%" aria-valuenow="<?php echo $POR_PRESENTADAS; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="titulos_contador">EN PROCESO</div>
                            <div class="contador_grande" style="color: #ffc107;"><?= $COUNT_EN_PROCESO ?></div>
                            <div class="total_contador"><?php echo $POR_EN_PROCESO; ?>% del total</div>
                             <div>
                                <div class="progress">
                                    <div class="progress-bar bg-warning" role="progressbar" style="width: <?php echo $POR_EN_PROCESO; ?>%" aria-valuenow="<?php echo $POR_EN_PROCESO; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="titulos_contador">SIN INICIAR</div>
                            <div class="contador_grande" style="color: #dc3545;"><?= $COUNT_SIN_VALORACION ?></div>
                            <div class="total_contador"><?php echo $POR_SIN_VALORACION; ?>% del total</div>
                             <div>
                                <div class="progress">
                                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $POR_SIN_VALORACION; ?>%" aria-valuenow="<?php echo $POR_SIN_VALORACION; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-8">
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="titulos_contador">
                                DETALLE POR TIPO DE VALORACIÓN
                            </div>
                            <table class="table">
                                <tr>
                                    <th>Tipo de Valoración</th>
                                    <th>Programadas</th>
                                    <th>Presentadas</th>
                                    <th>En Proceso</th>
                                    <th>Sin iniciar</th>
                                    <th>Avance</th>
                                </tr>

                                <tr>
                                    <td>Autoevaluación</td>
                                    <td><?= $ARRAY_MATRIZ["auto"]["programadas"] ?></td>
                                    <td class="item_verde"><?= $ARRAY_MATRIZ["auto"]["realizadas"] ?></td>
                                    <td class="item_naranja"><?= $ARRAY_MATRIZ["auto"]["en_progreso"] ?></td>
                                    <td class="item_rojo"><?= $ARRAY_MATRIZ["auto"]["sin_iniciar"] ?></td> 
                                    <td><div class="baged_contador"><?= $ARRAY_MATRIZ["auto"]["avance"] ?>%</div></td> 
                                </tr>
                                <tr>
                                    <td>Lider</td>
                                    <td><?= $ARRAY_MATRIZ["lider"]["programadas"] ?></td>
                                    <td class="item_verde"><?= $ARRAY_MATRIZ["lider"]["realizadas"] ?></td>
                                    <td class="item_naranja"><?= $ARRAY_MATRIZ["lider"]["en_progreso"] ?></td>
                                    <td class="item_rojo"><?= $ARRAY_MATRIZ["lider"]["sin_iniciar"] ?></td> 
                                    <td><div class="baged_contador"><?= $ARRAY_MATRIZ["lider"]["avance"] ?>%</div></td> 
                                </tr>
                                <tr>
                                    <td>Par</td>
                                    <td><?= $ARRAY_MATRIZ["par"]["programadas"] ?></td>
                                    <td class="item_verde"><?= $ARRAY_MATRIZ["par"]["realizadas"] ?></td>
                                    <td class="item_naranja"><?= $ARRAY_MATRIZ["par"]["en_progreso"] ?></td>
                                    <td class="item_rojo"><?= $ARRAY_MATRIZ["par"]["sin_iniciar"] ?></td> 
                                    <td><div class="baged_contador"><?= $ARRAY_MATRIZ["par"]["avance"] ?>%</div></td> 
                                </tr>
                                <tr>
                                    <td>Colaborador</td>
                                    <td><?= $ARRAY_MATRIZ["colaborador"]["programadas"] ?></td>
                                    <td class="item_verde"><?= $ARRAY_MATRIZ["colaborador"]["realizadas"] ?></td>
                                    <td class="item_naranja"><?= $ARRAY_MATRIZ["colaborador"]["en_progreso"] ?></td>
                                    <td class="item_rojo"><?= $ARRAY_MATRIZ["colaborador"]["sin_iniciar"] ?></td> 
                                    <td><div class="baged_contador"><?= $ARRAY_MATRIZ["colaborador"]["avance"] ?>%</div></td> 
                                </tr>

                                <tr>
                                    <td>Total</td>
                                    <td><?= $ARRAY_MATRIZ["total"]["programadas"] ?></td>
                                    <td class="item_verde"><?= $ARRAY_MATRIZ["total"]["realizadas"] ?></td>
                                    <td class="item_naranja"><?= $ARRAY_MATRIZ["total"]["en_progreso"] ?></td>
                                    <td class="item_rojo"><?= $ARRAY_MATRIZ["total"]["sin_iniciar"] ?></td> 
                                    <td><div class="baged_contador"><?= $ARRAY_MATRIZ["total"]["avance"] ?>%</div></td> 
                                </tr>

                                
                            </table>
                        </div>
                    </div>
                </div>


                <div class="col-md-4 mb-4">
                    <div class="card" style="max-height: 300px;">
                        <div class="card-body">
                            <div class="titulos_contador">
                                AVANCE GENERAL DEL CICLO
                            </div>
                            <div id="dona_avance" style="width: 80%; margin: 0 auto; max-height: 230px; height: 230px;">
                                <canvas id="graficaDona"></canvas>
                            </div>
                        </div>
                    </div>
                    
                </div>  


                <div class="col-md-2">
                    <div class="card mb-2" style="background-color: #fcdb58 ">
                        <div class="card-body interno">
                            <h3 style="color: #000000 !important;"><?php echo $POR_ENTREVISTA_PDI; ?>%</h3>
                            <p style="color: #000000 !important;">Entrevista (PDI) (<?php echo $ENTREVISTA_PDI; ?>)</p>
                        </div>
                    </div>
                </div>




                <div class="col-md-2" >
                    <div class="card mb-2" style="background-color: #59008e  ">
                        <div class="card-body interno">
                            <h3><?php echo $POR_ENVIO_PDI; ?>%</h3>
                            <p>Envio (PDI) (<?php echo $ENVIO_PDI; ?>)</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-2" >
                    <div class="card mb-2" style="background-color: #fb924e  ">
                        <div class="card-body interno">
                            <h3><?php echo $POR_INTERVESION_GH; ?>%</h3>
                            <p>Intervención GH (<?php echo $INTERVESION_GH; ?>)</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card mb-2" style="background-color: #64f456  ">
                        <div class="card-body interno">
                            <h3 style="color: #000000 !important;"><?php echo $POR_PDI_APROBADO_CERRADO; ?>%</h3>
                            <p style="color: #000000 !important;">PDI Aprobado y Cerrado (<?php echo $PDI_APROBADO_CERRADO; ?>)</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card mb-2" style="background-color: #6B21FF  ">
                        <div class="card-body interno">
                            <h3><?php echo $POR_CERRADO_GH; ?>%</h3>
                            <p>Proceso Cerrado por GH (<?php echo $CERRADO_GH; ?>)</p>
                        </div>
                    </div>
                </div>




            </div>
        </div>

        <div class="card-body">
            <div class="row">
                        <div class="col-12">
                            <form action="" method="post" class="row">
                                <!-- Tipo Evaluación -->
                                <div class="form-group col-md-4">
                                    <label for="tipo_evaluacion">Tipo de Evaluación</label>
                                    <select name="fill_tipo_evaluacion" class="form-control ">
                                        <option value="">Seleccione</option>
                                        <?php
                                        foreach($array_Tipo_Colaborador as $tipo){
                                            if($tipo[0] == $_POST["fill_tipo_evaluacion"] ){
                                                echo '<option value="'.$tipo[0].'" selected >'.$tipo[1].'</option>';
                                            }
                                            else{
                                                echo '<option value="'.$tipo[0].'">'.$tipo[1].'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <!-- Vicepresidencias -->
                                <div class="form-group col-md-4">
                                    <label for="vicepresidencia">Vicepresidencia</label>
                                    <select name="fill_vicepresidencia" id="vicepresidencia" class="form-control ">
                                        <option value="">Seleccione...</option>
                                        <?php
                                        
                                        $queryV = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1  ".$ind_lider." ");
                                        while($dataV = mysqli_fetch_array($queryV)){
                                            echo '<option value="'.$dataV["id"].'">'.$dataV["nombre"].'</option>';
                                        }

                                        
                                       
                                        
                                        ?>

                                    </select>



                                   
                                </div>
                                <!-- Bóton de filtrar -->
                                <div class="form-group col-md-2 align-self-end d-flex align-items-center">
                                    <button type="submit" class="btn btn-success btn-block">Filtrar</button>

                                    <button type="button" class="btn btn-danger" onclick="ExportarExcel()" style="margin-left: 10px;">
                                        Descargar
                                    </button>
                                    
                                </div>
                            </form>
                        </div>
                    </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">

        <table class="table table-table-bordered" id="tabla_general">
            <thead>
            <tr>
                <th>#</th>
                <th>Doc. Evaluado</th>
                <th>Evaluado</th>
                <th>Cargo</th>
                <th>Vicepresidencia</th>
                <th>Área</th>
                <th>Doc. Evaluador</th>
                <th>Evaluador</th>
                <th>Tipo evaluación</th>
                <th>Estado</th>
                <th>Proceso valoración</th>
                <th>Reporte</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $count = 1;
            foreach($array_evaluadores as $empleado){

                $porcentaje = 0;
                if($empleado["promedio"] > 0 ){
                    $porcentaje = $empleado["promedio"]*20;
                    $porcentaje = round($porcentaje);
                }

                $progreso = '
                <div class="progress" style="height: 22px; position: relative;">
                    <div class="progress-bar bg-success text-white" role="progressbar" style="width: '.$porcentaje.'%; display: flex; align-items: center; justify-content: center;" aria-valuenow="'.$porcentaje.'" aria-valuemin="0" aria-valuemax="100">
                        '.$porcentaje.'%
                    </div>
                                
                </div>
                ';

                echo '
                <tr>
                    <td>'.$count.'</td>
                    <td>'.$empleado["documento"].'</td>
                    <td>'.$empleado["nombre"].'</td>
                    <td>'.$empleado["cargo_evaluado"].'</td>
                    <td>'.$empleado["vice_evaluado"].'</td> 
                    <td>'.$empleado["area_evaluado"].'</td>  
                    <td>'.$empleado["doc_evaluador"].'</td> 
                    <td>'.$empleado["nombre_evaluador"].'</td> 
                    <td style="color: '.$empleado["color_tipo"].'">'.$empleado["txt_tipo"].'</td> 
                    <td>'.$empleado["txt_estado_eval"].'</td> 
                    <td style="color:'.$empleado["color_proceso_valoracion"].'"><b>'.$empleado["txt_proceso_valoracion"].'</b></td> 
                    
                    <th>
                        <a href="?pg=competencias/informes/reporte_individual&e='.$empleado["id_empleado"].'" target="_blank">
                            <button type="button" class="btn btn-primary" style="border-radius: 8px; margin: 1px; font-size: 0.8rem !important">
                                Reporte
                            </button>
                        </a>
                    </td>
                </tr>
                ';
                $count++;
            }
            ?>
            </tbody>
        </table>

        </div>
    </div>
</div>


<script>
    $(document).ready(function() {

        $('#tabla_general').DataTable(
            {
                pageLength: 50
            }
        );
    });

    function ExportarExcel() {

        var table = $('#tabla_general').DataTable();

        // Mostrar todos los registros
        table.page.len(-1).draw();

        setTimeout(function(){

            var tabla = document.getElementById("tabla_general").outerHTML;

            var archivo = new Blob(
                ['\ufeff' + tabla],
                { type: 'application/vnd.ms-excel' }
            );

            var url = URL.createObjectURL(archivo);

            var link = document.createElement("a");
            link.href = url;
            link.download = "ReporteSeguimiento.xls";

            document.body.appendChild(link);

            link.click();

            document.body.removeChild(link);

            // Volver a 50 registros
            table.page.len(50).draw();

        }, 500);
    }
</script>


























<script type="text/javascript">
    var api = '<?php echo $url; ?>api/competencias/';

    var activar = false;

    function Reactivar_Evaluacion(id) {
        if (activar == false) {
            $("#modal_general").modal("show");
            $("#cont_modal_general").html('Está a punto de reactivar este formulario, esta acción es irreversible ¿está seguro?<br><br>');
            $("#botones_modal_general").html('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Reactivar_Evaluacion(' + id + ')"> Reactivar Formulario </button>');
        } else {

            jQuery.ajax({
                    url: api + "reactivar_formulario_new.php",
                    type: 'post',
                    data: {
                        id: id,
                        url: "?pg=competencias/seguimiento"
                    },
                }).done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {});

        }
    }


    function Elinar_Evaluacion(id, id_evaluado, id_evaluador, tipo) {
        if (activar == false) {
            $("#modal_general").modal("show");
            $("#cont_modal_general").html('Está a punto de borrar este formulario, esta acción es irreversible ¿está seguro?<br><br>');
            $("#botones_modal_general").html('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Elinar_Evaluacion(' + id + ', ' + id_evaluado + ', ' + id_evaluador + ', ' + tipo + ' )"> Eliminar Formulario </button>');
        } else {

            jQuery.ajax({
                    url: api + "eliminar_formulario_new.php",
                    type: 'post',
                    data: {
                        id: id,
                        id_evaluado: id_evaluado,
                        id_evaluador: id_evaluador,
                        id_tipo: tipo,
                        url: "?pg=competencias/seguimiento"
                    },
                }).done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {});

        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

    const data = {
    labels: [
        'Presentadas (<?= $ARRAY_MATRIZ["total"]["realizadas"] ?>)',
        'En proceso (<?= $ARRAY_MATRIZ["total"]["en_progreso"] ?>)',
        'Sin iniciar (<?= $ARRAY_MATRIZ["total"]["sin_iniciar"] ?>)'
    ],
    datasets: [{
        label: 'Evaluadores',
        data: [<?= $ARRAY_MATRIZ["total"]["realizadas"] ?>, <?= $ARRAY_MATRIZ["total"]["en_progreso"] ?>, <?= $ARRAY_MATRIZ["total"]["sin_iniciar"] ?>],
        backgroundColor: [
            'rgb(139 195 74)',
            'rgb(255 193 7)',
            'rgb(220 53 69)'
        ],
        hoverOffset: 4
    }]
    };

    /*
    const config = {
    type: 'doughnut',
    data: data,
    };
    */

    const config = {
        type: 'doughnut',
        data: data,
        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                /* LABELS A LA DERECHA */
                legend: {
                    display: true,
                    position: 'right',

                    labels: {
                        boxWidth: 15,
                        padding: 20,
                        font: {
                            size: 14
                        }
                    }
                }
            }
        }
    };

    
    // Renderizar dentro del div dona_avance
    const ctx = document.getElementById('graficaDona').getContext('2d');
    new Chart(ctx, config);


    
</script>