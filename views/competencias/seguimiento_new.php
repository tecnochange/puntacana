<script>
    $(document).ready(function() {
        $(".menu_section").addClass("active");
        $("#nav_competencias").addClass("active");
        jQuery("#menu_competencias").css("display", "none");
        $("#bt_comp_seguimiento").addClass("current-page");
    });
</script>

<?php
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
    Empleados.id_empresa = '".$user_log["id_empresa"]."' AND Empleados.estado = 1
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
    Competencias_Evaluaciones_New.id_evaluado,  Competencias_Evaluaciones_New.id_evaluador, Competencias_Evaluaciones_New.promedio, Competencias_Evaluaciones_New.estado
FROM
    Competencias_Evaluaciones_New
WHERE
    Competencias_Evaluaciones_New.id_ciclo = '". $_SESSION['ciclo']."' AND Competencias_Evaluaciones_New.anio = '". $_SESSION['anio_ciclo']."'
";
$queryEvaluaciones = mysqli_query($connect_valoracion, $sentencia_evaluaciones_realizadas );
while($dataEvaluaciones = mysqli_fetch_array($queryEvaluaciones)){
    array_push($array_evaluaciones, $dataEvaluaciones );
}


$COUNT_SIN_VALORACION = 0;
$AUTOEVALUACION = 0;

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
    Evaluadores.id_ciclo = '". $_SESSION['ciclo']."' AND Evaluadores.anio = '". $_SESSION['anio_ciclo']."'  AND Empleados.estado = 1 
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

    $data["promedio"] = $data_evaluacion["promedio"];
    $data["estado_eval"] = $data_evaluacion["estado"];

    if($sin_valoracion == false){
        $data["promedio"] = -1;
        $COUNT_SIN_VALORACION++;
    }
    


    array_push($array_evaluadores, $data);
}
     
//CONTADORES GENERALES
$TOTAL_GENERAL = $query->num_rows;
$POR_SIN_VALORACION = 0;

if($COUNT_SIN_VALORACION > 0){
    $POR_SIN_VALORACION = $COUNT_SIN_VALORACION*100/$TOTAL_GENERAL;
    $POR_SIN_VALORACION = round($POR_SIN_VALORACION,2);
}
//$SIN_VALORACION = 


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
            <div class="row">
                <div class="col-md-12 text-end">
                    <h5> <?php echo $TOTAL_GENERAL; ?> Valoraciones</h5>
                </div>


                <div class="col-md-3">
                    <div class="card mb-2" style="background-color: #f76d6b ">
                        <div class="card-body interno">
                            <h3><?php echo $POR_SIN_VALORACION; ?>%</h3>
                            <p>Sin Valoración (<?= $COUNT_SIN_VALORACION ?>)</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 ">
                    <div class="card mb-2" style="background-color: #7f38c2 ">
                        <div class="card-body interno">
                            <h3>95.29%</h3>
                            <p>Autoevaluación (1)</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card mb-2"  style="background-color: #748eff ">
                        <div class="card-body interno">
                            <h3>95.29%</h3>
                            <p>Evaluación del Líder (84)</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card mb-2" style="background-color: #fcdb58 ">
                        <div class="card-body interno">
                            <h3 style="color: #000000 !important;">95.29%</h3>
                            <p style="color: #000000 !important;">Entrevista (PDI) (73)</p>
                        </div>
                    </div>
                </div>


                <div class="col-md-3">
                    <div class="card mb-2" style="background-color: #59008e  ">
                        <div class="card-body interno">
                            <h3>95.29%</h3>
                            <p>Envio (PDI) (26)</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card mb-2" style="background-color: #fb924e  ">
                        <div class="card-body interno">
                            <h3>95.29%</h3>
                            <p>Intervención GH (0)</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card mb-2" style="background-color: #64f456  ">
                        <div class="card-body interno">
                            <h3 style="color: #000000 !important;">95.29%</h3>
                            <p style="color: #000000 !important;">PDI Aprobado y Cerrado (3)</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card mb-2" style="background-color: #6B21FF  ">
                        <div class="card-body interno">
                            <h3>95.29%</h3>
                            <p>Proceso Cerrado por GH (0)</p>
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
                                    <select name="tipo_evaluacion" id="tipo_evaluacion" class="form-control multiples_responsables">
                                        <option value="">Seleccione</option>
                                        <option value="1" <?php if (isset($_POST['tipo_evaluacion']) && $_POST['tipo_evaluacion'] == 1) echo 'selected'; ?>>Auto</option>
                                        <option value="5" <?php if (isset($_POST['tipo_evaluacion']) && $_POST['tipo_evaluacion'] == 5) echo 'selected'; ?>>Jefe</option>
                                    </select>
                                </div>
                                <!-- Vicepresidencias -->
                                <div class="form-group col-md-4">
                                    <label for="vicepresidencia">Vicepresidencia</label>
                                    
                                    <select name="vicepresidencia" id="vicepresidencia" class="form-control multiples_responsables">
                                        <option value="">Seleccione...</option>
                                        <?php
                                        $ind_lider = "";
                                        if($_SESSION["role_plataforma"] == 2){
                                            $in_lider = implode(",", $array_relaciones);
                                            $ind_lider = " AND id IN (".$in_lider.") ";
                                        }

                                        //echo "SELECT * FROM Vicepresidencia WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1  ".$ind_lider." ";
                                    
                                        $queryV = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1  ".$ind_lider." ");
                                        while($dataV = mysqli_fetch_array($queryV)){
                                            echo '<option value="'.$dataV["id"].'">'.$dataV["nombre"].'</option>';
                                        }

                                        
                                       
                                        
                                        ?>

                                    </select>



                                    <select name="vicepresidencia" id="vicepresidencia__" class="form-control " style="display:none">
                                        <option value="">Seleccione</option>
                                    </select>
                                </div>
                                <!-- Bóton de filtrar -->
                                <div class="form-group col-md-2 align-self-end d-flex align-items-center">
                                    <button id="btnFiltrar" type="button" class="btn btn-success btn-block">Filtrar</button>
                                    <div id="loaderv2" style="display:none; vertical-align: middle; margin-left: 8px;">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">

        <table class="table table-table-bordered">
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
                <th>Reporte</th>
            </tr>

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
                    <td>'.$empleado["estado_eval"].'</td> 
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
        </table>

        </div>
    </div>
</div>




























<div class="container-fluid">

    <div class="row">

        <div class="col-md-12">
            <table width="100%">
                <tr>
                    <?php if ($user_log["role"] == 10000) { ?>
                        <td width="130">
                            <form action="<?php echo $url; ?>?pg=competencias_pc/send" method="get">
                                <button type="submit" class="btn btn-danger">Comunicados</button>
                            </form>
                        </td>
                    <?php } ?>
                </tr>
            </table>

        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-pills justify-content-center" style="margin-bottom: 10px;">
                        <li class="nav-item">
                            <a class="nav-link active " href="?pg=competencias/seguimiento">Seguimiento Valoración</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link " href="?pg=competencias/formularios">Formularios Valoración</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12" style="text-align: end;">
                            <h3 id="valoracionesCount">0 Valoraciones</h3>
                        </div>
                        <div class="row" id="procesosContainer">
                            <!-- Las demás secciones se generarán dinámicamente -->
                        </div>
                    </div>

                    <br>
                   
                </div>
            
            </div>
        </div>
    </div>
</div>


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