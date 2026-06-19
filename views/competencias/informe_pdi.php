<script>
    $(document).ready(function() {
        $(".menu_section").addClass("active");
        $("#nav_competencias").addClass("active");
        jQuery("#menu_competencias").css("display", "none");
        $("#bt_comp_mis_informes").addClass("current-page");
    });

    $(document).ready(function() {
        $('#menuPdi').collapse();
        $("#bt_desarrollo_lista").addClass("active");
    });

    function AgregarId(id, id_empleado) {
        $("#id_desarrollo").val(id);
        $("#id_empleado").val(id_empleado);
    }

    function exportarReporte() {
        // Clonamos la tabla y eliminamos la columna "Ver"
        var clonedTable = $("#TablaExcel").clone();
        clonedTable.find("th:last-child, td:last-child").remove();

        // Pasamos la tabla modificada al formulario
        $("#datos_a_enviar").val($("<div>").append(clonedTable).html());
        $("#FormularioExportacion").submit();
    }
</script>
<style>
    .bg-goforagile {
        background-color: #59008e !important;
    }
</style>

<?php
include("app/models/competencias/Competencias.php");
$ClassCompetencias = new Competencias();
$dataCicloVal = $ClassCompetencias->Ciclo($user_log["id_empresa"], $_SESSION["anio_ciclo"]);

function ValidarEvaluaciones($id_evaluado, $id_jefe, $id_empresa){
    global $dataCicloVal;
    global $connect_valoracion;

    $permitir = false;
    $con_auto = false;
    $sin_evaluacion_terminada = true;
    $no_evaluadores = 0;
    $evaluaciones_terminadas = 0;

    $sentenciaEvals = "
        SELECT * FROM Evaluadores
        WHERE 
        Evaluadores.id_empleado = '".$id_evaluado."' AND 
        Evaluadores.anio = '" . $dataCicloVal["anio"] . "' AND 
        Evaluadores.id_ciclo = '" . $dataCicloVal["id"] . "' AND 
        Evaluadores.tipo != 5
    ";
	$queryEvaluadores = mysqli_query($connect_valoracion, $sentenciaEvals);
	while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) { 

        $no_evaluadores++;

        $queryValidacion = mysqli_query($connect_valoracion, "SELECT * FROM  Competencias_Evaluaciones_New
		WHERE id_empresa = '".$id_empresa."'
		AND id_ciclo = '" . $dataCicloVal["id"] . "'
		AND id_evaluado = '" . $dataEvaluadores["id_empleado"] . "'
		AND id_evaluador = '" . $dataEvaluadores["id_evaluador"] . "'
		AND anio = '" . $dataCicloVal["anio"] . "'");
        $dataValidacion = mysqli_fetch_array($queryValidacion);

        if($queryValidacion->num_rows > 0){
            if($dataValidacion["estado"] >= 2){
                $evaluaciones_terminadas++;

                
            }
            else{
                $sin_evaluacion_terminada = false;
            }
        }
        else{
            $sin_evaluacion_terminada = false;
        }
    }

    /*
    if($sin_evaluacion_terminada == false){
        echo "faltan_evaluadiones";

    }
    */

    return array(
        "validacion" => $sin_evaluacion_terminada,
        "evaluaciones" => $no_evaluadores, 
        "terminadas" => $evaluaciones_terminadas
    );

 

}

if (isset($_POST["id_desarrollo"]) && isset($_POST["id_empleado"])) {
    $queryValoracion = mysqli_query($connect_pdi, "UPDATE Desarrollo SET
    estado = 1 WHERE id_empleado = '" . $_POST["id_empleado"] . "' AND id = '" . $_POST["id_desarrollo"] . "' ");
}
?>

<?php include("views/pdi_pc/layouts/mod_reabrir_pdi.php");
include("views/competencias/informes/funciones.php"); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <table width="100%">
                <tr>
                    <td>
                        <h2>PDI Empleados</h2>
                    </td>

                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <form action="app/models/exportarExcel.php" method="post" target="_blank" id="FormularioExportacion">
                <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link" href="?pg=competencias/mi_informe">Mi Informe</a>
                        </li>
                        <?php if ($VALIDAR_MENU["competencias_informe_equipo"]) { ?>
                            <li class="nav-item">
                                <a class="nav-link active" href="?pg=competencias/informe_pdi">PDI Colaboradores</a>
                            </li>
                        <?php } ?>

                    </ul>
                </div>
                <div class="card-body">
                    <div class="table-responsive" id="tabla_contenido">
                        <table border="1" id="mi_informe" class="display table" style="width:100%;">
                            <thead>
                                <tr>
                                    <th scope="col" width="15">#</th>
                                    <th scope="col">Documento</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Cargo</th>
                                    <th scope="col">Vicepresidencia</th>
                                    <th scope="col">Area</th>
                                    <th scope="col">Unidad Organizativa</th>
                                    <th scope="col">Jefe</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Proceso Valoración</th>
                                    <th scope="col" width="150">Ver</th>
                                    <!-- <th scope="col">Puntuación Competencia</th> -->
                                </tr>
                            </thead>
                            <tbody id="tabla_lista" class="tabla_lista">
                                <?php

                                $count = 1;
                                $sentencia =  "
                                SELECT E.*
                                FROM Empleados E
                                    INNER JOIN Lideres ON Lideres.id_empleado = E.id
                                WHERE E.id_empresa = '" . $user_log['id_empresa'] . "' AND E.estado = 1 AND Lideres.id_jefe = " . $user_log['id'] . " 
                                ORDER BY E.nombre ASC";

                               
                                $queryEmp = mysqli_query($connect_admin, $sentencia);
                                while ($dataEmp = mysqli_fetch_array($queryEmp)) { 

                                /*    
                                //VALIDAR AUTOEVALUACION
                                    $sentencia_validar_auto = "
                                        SELECT * FROM Competencias_Evaluaciones_New WHERE 
                                        id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' AND
                                        id_evaluado = '" . $id_empleado . "' AND  id_evaluador = '" . $dataEvaluadores['id_evaluador'] . "' AND 
                                        id_ciclo = '" . $_SESSION['ciclo'] . "' AND tipo_evaluacion = '" . $dataEvaluadores["tipo"] . "' 
                                    ";
                                    $queryValidarAuto = mysqli_query($connect_valoracion, $sentencia_validar_auto);
                                    */

                                    $queryCargo = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE id = '" . $dataEmp["id_cargo"] . "' ");
                                    $dataCargo = mysqli_fetch_array($queryCargo);

                                    $queryArea = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id = '" . $dataEmp["area"] . "' ");
                                    $dataArea = mysqli_fetch_array($queryArea);

                                    $queryValoracion = mysqli_query($connect_pdi, "SELECT * FROM Desarrollo
                                    WHERE id_empleado = '" . $dataEmp["id"] . "' AND anio = '" . $_SESSION["anio_ciclo"] . "' ");
                                    $dataValoracion = mysqli_fetch_array($queryValoracion);

                                    $lista_jefes = '';
                                    $id_jefe = 0;
                                    $queryJefes = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empleado = '" . $dataEmp["id"] . "' 
                                    AND id_empresa = '".$user_log["id_empresa"]."' ");
                                    while ($dataJefes = mysqli_fetch_array($queryJefes)) {

                                        $id_jefe = $dataJefes["id_jefe"];

                                        $queryEm = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $dataJefes["id_jefe"] . "' ");
                                        $dataEm = mysqli_fetch_array($queryEm);

                                        $queryCro = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE id = '" . $dataEm["id_cargo"] . "' ");
                                        $dataCro = mysqli_fetch_array($queryCro);

                                        $lista_jefes .= '
                                            <div style="margin-bottom: 10px;">
                                                ' . $dataEm["nombre"] . '
                                            </div>
                                        ';
                                    }

                                    //             $btn_edit = '
                                    //     <a href="?pg=pdi_pc/desarrollo/resumen_pdi&e=' . $dataEmp["id"] . '">
                                    //         <button type="button" class="btn btn-danger btn-sm">Ver</button>
                                    //     </a>
                                    // ';

                                    $btn_edit = '';

                                    $btn_reabrir = '';
                                    if ($queryValoracion->num_rows > 0) {
                                        if ($dataValoracion["estado"] >= 3) {
                                            $btn_reabrir = '
                                    <button type="button" class="btn btn-warning btn-sm" title="Aplicar" data-bs-toggle="modal" data-bs-target=".modal_reabrir_pdi" onclick="AgregarId(' . $dataValoracion["id"] . ', ' . $dataEmp["id"] . ' )">Reabrir</button>
                                    ';
                                        }
                                    }

                                    // $queryAsignado = mysqli_query($connect_valoracion, "SELECT * FROM Asignacion_Pdi WHERE id_jefe = '" . $_SESSION["id_user"] . "' AND id_empleado = " . $dataEmp["id"] . " AND ciclo = " . $_SESSION["anio_ciclo"] . "");
                                    // $asignado = mysqli_fetch_array($queryAsignado);

                                    $adicional = "";
                                    // if ($asignado["id"] > 0) {
                                    //     $adicional = "&id_asignado=" . $asignado["id"] . "";
                                    // } else {
                                    //     $adicional = "&id_asignado=0";
                                    // }


                                    $VALIDACION = PromedioGeneralEvaluado($dataEmp["id"], $connect_valoracion, $connect_admin);

                                    //print_r($VALIDACION["proceso_valoracion"]);
                                    //echo "<br>";


                                    if ($VALIDACION["no_evaluadores_evaluacion"]  == 0) {
                                        $btn_reporte = '';
                                    } 
                                    else{
                                        if ($VALIDACION["estado"] == 2 || $VALIDACION["estado"] == 3 || $VALIDACION["proceso_valoracion"] >= 2) {
                                            $btn_reporte = '<a href="?pg=competencias/informes/reporte_individual&e=' . $dataEmp["id"] . '' . $adicional . '" target="_blank" >
                                                <button type="button" class="btn btn-primary" style="border-radius: 30px; margin: 3px; ">
                                                    Reporte
                                                </button>
                                            </a>';
                                        }
                                    }

                                    $datos_generales = ValidarEvaluacionesCompletas($dataEmp["id"], $connect_valoracion, $connect_admin);

                                    $promedio_general = $datos_generales["promedio_general"];
                                    $porcentaje_general = ($promedio_general * 100) / 5;

                                    if($porcentaje_general > 100){
                                        $porcentaje_general = 100;
                                    }


                                    $txt_estado = "Pendiente";
                                    if ($VALIDACION["estado"] == 1) {
                                        $txt_estado = "En Proceso";
                                    }
                                    if ($VALIDACION["estado"] == 2) {
                                        $txt_estado = "En Proceso";
                                    }
                                    if ($VALIDACION["estado"] == 3) {
                                        $txt_estado = "Finalizado";
                                    }
                                    // foreach ($Array_Estado_Planes_Desarrollo as $estado) {
                                    //     if ($dataValoracion["estado"] == $estado[0]) {
                                    //         $txt_estado = $estado[1];
                                    //     }
                                    // }

                                    // echo $VALIDACION["proceso_valoracion"];

                                    $txt_proceso = "Sin Valoración";
                                    foreach ($Array_Proceso_Valoracion as $proceso) {
                                        if ($VALIDACION["proceso_valoracion"] == $proceso[0]) {
                                            $txt_proceso = $proceso[1];
                                        }
                                    }

                                    switch ($VALIDACION["proceso_valoracion"]) {
                                        case 1:
                                            $colorProceso = 'badge bg-goforagile';
                                            $colorBadgeP = 'white';
                                            break;
                                        case 2:
                                            $colorProceso = 'badge bg-primary';
                                            $colorBadgeP = 'white';
                                            break;
                                        case 3:
                                            $colorProceso = 'badge bg-warning';
                                            $colorBadgeP = 'black';
                                            break;
                                        case 4:
                                            $colorProceso = 'badge bg-goforagile';
                                            $colorBadgeP = 'white';
                                            break;
                                        case 5:
                                            $colorProceso = 'badge bg-goforagile';
                                            $colorBadgeP = 'white';
                                            break;
                                        case 6:
                                            $colorProceso = 'badge bg-goforagile';
                                            $colorBadgeP = 'white';
                                            break;
                                        case 7:
                                            $colorProceso = 'badge bg-success';
                                            $colorBadgeP = 'white';
                                            break;
                                        default:
                                            $colorProceso = 'badge bg-light';
                                            $colorBadgeP = 'black';
                                            break;
                                    }









                                    $sentenciaEvalsJefe = "
                                    SELECT * FROM
                                        Evaluadores
                                    WHERE
                                        id_empleado  = '" .$dataEmp["id"]. "' AND Evaluadores.anio = '" . $_SESSION["anio_ciclo"] . "' AND Evaluadores.id_ciclo = '" . $_SESSION["ciclo"] . "' AND Evaluadores.tipo = 5 
                                    ";
                                    $queryValidarJefe = mysqli_query($connect_valoracion, $sentenciaEvalsJefe);
                                    //while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) { 


                                    
                                    if($queryValidarJefe->num_rows > 0 ){
                                        $permitir_jefe = ValidarEvaluaciones($dataEmp["id"], $user_log["id"], 1);

                                        $sentvalidarTmpJefe = " SELECT * FROM Competencias_Evaluaciones_New
                                            WHERE
                                                id_evaluado  = '" .$dataEmp["id"]. "' AND anio = '" . $_SESSION["anio_ciclo"] . "' AND id_ciclo = '" . $_SESSION["ciclo"] . "' AND tipo_evaluacion = 5 
                                            ";
                                        $queryValidarTmpJefe = mysqli_query($connect_valoracion, $sentvalidarTmpJefe);
                                        if($queryValidarTmpJefe->num_rows > 0){

                                        }
                                        else{
                                            if(!$permitir_jefe["validacion"]){
                                                $txt_proceso = "Otros evaluadores en curso ".$permitir_jefe["terminadas"]."/".$permitir_jefe["evaluaciones"]; 
                                                $colorProceso = ' badge bg-info ';
                                            }
                                        }


                                        
                                        
                                    }
                                        
                                        

                                    echo '
                                    <tr>
                                        <td>' . $count . '</td>
                                        <td>' . $dataEmp["documento"] . '</td>
                                        <td>' . $dataEmp["nombre"] . '</td>
                                        <td>' . $dataCargo["nombre"] . '</td>
                                        <td></td>
                                        <td>' . $dataArea["nombre"] . '</td>
                                        <td></td>
                                        <td>' . $lista_jefes . '</td>
                                        <td>' . $txt_estado . '</td>
                                        <td>
                                            <span class="' . $colorProceso . '" style="color:' . $colorBadgeP . ' !important;font-size: 13px !important;"><b>' . $txt_proceso . '</b></span>
                                        </td>
                                        <td align="center">' . $btn_edit . ' ' . $btn_reporte . '</td>
                                    </tr>
                                    ';

                                    //<td>' . round($porcentaje_general) . '%</td>
                                    $count++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


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

<script type="text/javascript">
    $(document).ready(function() {
        $('#mi_informe').DataTable({
            pageLength: 15,
            language: {
                processing: "Procesando...",
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros.",
                info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                infoEmpty: "Mostrando registros del 0 al 0 de 0 registros",
                infoFiltered: "(filtrado de un total de _MAX_ registros)",
                infoPostFix: "",
                loadingRecords: "Cargando...",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                row: "Registro",
                export: "Exportar",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Ultimo"
                },
                aria: {
                    sortAscending: ": Activar para ordenar la columna de manera ascendente",
                    sortDescending: ": Activar para ordenar la columna de manera descendente"
                },
                select: {
                    row: "registro",
                    selected: "seleccionado"
                }
            },
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'collection',
                    text: 'Exportar',
                    buttons: [
                        'copy',
                        'excel',
                        'csv',
                        {
                            extend: 'pdfHtml5',
                            text: 'PDF',
                            orientation: 'landscape',
                            pageSize: 'LEGAL'
                        },
                        {
                            extend: 'print',
                            customize: function(win) {
                                $(win.document.body)
                                    .css('font-size', '10pt');

                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            }
                        }
                    ]
                }

            ]
        });

    });
</script>