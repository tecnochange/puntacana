<?php
$queryValidarAuto = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores
WHERE anio = '" . $_SESSION['anio_ciclo'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND id_empleado = '" . $user_log['id'] . "' AND tipo = 1 ");

$txt_estado_avance = "Su Valoración está pendiente";
$mensaje_avance = '¡Es hora de iniciar su autovaloración! Complete su valoración para que su líder/supervisor pueda continuar con la valoración de sus competencias.';
$boton1 = '
<a href="?pg=competencias/realizar_valoracion">
    <button class="btn btn-primary">Iniciar Autovaloración</button>
</a>
';

if($queryValidarAuto->num_rows > 0 ){

    $queryValidarAutoEstado = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'
    AND id_ciclo = '" . $_SESSION['ciclo'] . "'
    AND id_evaluado = '" . $_SESSION['id_user'] . "'
    AND id_evaluador = '" . $_SESSION['id_user'] . "'
    AND anio = '" . $_SESSION["anio_ciclo"] . "'");
    $dataValidarAutoEstado = mysqli_fetch_array($queryValidarAutoEstado);


    if($dataValidarAutoEstado["estado"] == 1){
        $txt_estado_avance = 'En Proceso de Autovaloración';
        $mensaje_avance = '¡Es hora de continuar su autovaloración! Complete su valoración para que su líder/supervisor pueda continuar con la valoración de sus competencias.'; 
        $boton1 = '
        <a href="?pg=competencias/realizar_valoracion">
            <button class="btn btn-primary">Continuar Autovaloración</button>
        </a>
        ';
    }
    if($dataValidarAutoEstado["estado"] == 2){
        $txt_estado_avance = 'Autovaloración terminada';
        $mensaje_avance = '¡Autovaloración enviada con éxito! Su líder/supervisor directo ahora está en proceso de realizar la valoración correspondiente.';
        $boton1 = '<a href="' . $url . '?pg=competencias_pc/mi_informe" class="btn btn-primary" style="' . $background . 'color: white !important;border-radius: 30px;">Ver Estado de la Evaluación</a>';

        if ($dataEval2["proceso_valoracion"] == 4) {
            $txt_estado_avance = 'PDI Listo para Firma';
            $mensaje_avance = 'Su Plan de Desarrollo Individual (PDI) está listo para ser revisado y firmado. Puede firmar desde tu celular escaneando el siguiente código QR o ingresando desde su computadora.<hr>
            Si no está de acuerdo con su Plan de Desarrollo Individual (PDI), puede solicitar la intervención del equipo de Relaciones Laborales para revisar el caso.';
            $boton1 = '
            <a href="?pg=competencias/realizar_valoracion">
                <button class="btn btn-primary">Firmar PDI Ahora</button>
            </a>
            ';
        }
    }

    if($dataValidarAutoEstado["estado"] == 3){
        $txt_estado_avance = 'Valoración Finalizada';
        $mensaje_avance = '¡Felicidades! Ha finalizado exitosamente su proceso de valoración de competencias.<br>
        Gracias por su compromiso con el desarrollo profesional y el crecimiento dentro de la organización.<br>
        Recuerde que podrá consultar su PDI y seguimiento a sus acciones de desarrollo en cualquier momento desde la plataforma.'; 
        $boton1 = '
            <a href="?pg=competencias/mi_informe">
                <button class="btn btn-primary">Ver mi PDI</button>
            </a>
        ';
    }     
}
else{
    $txt_estado_avance = "Sin autovaloración";
    $boton1 = '';
    $mensaje_avance = '';
}

$queryJefe = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores
WHERE anio = '" . $_SESSION['anio_ciclo'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND id_empleado = '" . $user_log['id'] . "' AND tipo = 5 ");



$sumProceso1 = $sumProceso2 = $sumProceso3 = $sumProceso4 = $sumProceso5 = $sumProceso6 = $sumProceso7 = 0;

$queryValidarTodasEvaluacionesUser = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores
WHERE anio = '" . $_SESSION['anio_ciclo'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND id_evaluador = '" . $user_log['id'] . "' AND tipo IN (5) ");
while ($dataValidarTodasEvaluacionesUser = mysqli_fetch_array($queryValidarTodasEvaluacionesUser)) {


    $queryValidarEval = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'
    AND id_ciclo = '" . $_SESSION['ciclo'] . "'
    AND id_evaluado = '" . $dataValidarTodasEvaluacionesUser['id_empleado'] . "'
    AND id_evaluador = '" . $dataValidarTodasEvaluacionesUser['id_evaluador'] . "'
    AND anio = '" . $_SESSION["anio_ciclo"] . "'");
    $dataValidarValidar = mysqli_fetch_array($queryValidarEval);

    $dataValidarTodasEvaluacionesUser["proceso_valoracion"] = $dataValidarValidar["proceso_valoracion"];

    //EN CASO DE NO TENER LA EVALUACION DEL JEFE
    if($queryValidarEval->num_rows == 0){
        //VALIDAR AUTOEVALUACION
        //VALIDAR AUTOEVALUACION
        $queryValidarAuto = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New
        WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'
        AND id_ciclo = '" . $_SESSION['ciclo'] . "'
        AND id_evaluado = '" . $dataValidarTodasEvaluacionesUser['id_empleado'] . "'
        AND anio = '" . $_SESSION["anio_ciclo"] . "' 
        AND tipo_evaluacion = 1 
        ");
        if($queryValidarAuto->num_rows > 0  ){
            $dataValidarTodasEvaluacionesUser["proceso_valoracion"] = 1;
        }
    }

    



    switch ($dataValidarTodasEvaluacionesUser["proceso_valoracion"]) {
                                case 1:
                                    $sumProceso1++;
                                    break;
                                case 2:
                                    $sumProceso2++;
                                    break;
                                case 3:
                                    $sumProceso3++;
                                    break;
                                case 4:
                                    $sumProceso4++;
                                    break;
                                case 5:
                                    $sumProceso5++;
                                    break;
                                case 6:
                                    $sumProceso6++;
                                    break;
                                case 7:
                                    $sumProceso7++;
                                    break;
                                default:
                                    $sumProceso++;
                                    break;
    }
}





$arrayTotalProceso = array();
$arrayTotalProceso[1] = $sumProceso1;
$arrayTotalProceso[2] = $sumProceso2;
$arrayTotalProceso[3] = $sumProceso3;
$arrayTotalProceso[4] = $sumProceso4;
$arrayTotalProceso[5] = $sumProceso5;
$arrayTotalProceso[6] = $sumProceso6;
$arrayTotalProceso[7] = $sumProceso7;

?>

<style>
    .titulo_modal_avance{
        background-color: #FFC107;
        padding: 10px;
    }
</style>


<div class="modal fade" id="modal_avance_valoracion" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- cerrar -->
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body" align="center">

        <div class="row">
            <div class="col-md-12">
                <div class="titulo_modal_avance mb-2">
                    <h5>Estado Actual de su Proceso de Valoración de Competencias</h5>
                </div>


                <h5><?php echo $txt_estado_avance; ?></h5>
                <p><?php echo $mensaje_avance; ?></p>
                <p><?php echo $boton1; ?></p>
                

                
                <?php if($queryValidarTodasEvaluacionesUser->num_rows > 0){ ?>
                <div class="mb-2" style="background-color: #03A9F4; padding: 10px;">
                    <h5 style="color: #ffffff;">Resumen General del Estado de Valoración de sus Colaboradores</h5>
                </div>

                <div >
                    <h6>
                        Resumen del estado de valoración para los colaboradores asignados. Consulte el progreso actual y continúe con las acciones necesarias para finalizar el proceso.
                    </h6>
                    
                </div>

                <table border="1" class="table" style="width:100%;">
                                        <thead class="table-dark">
                                            <tr style="text-align: center;">
                                                <th>Estado</th>
                                                <th>N° de Colaboradores</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Sin Valoración</td>
                                                <td style="text-align: center;"><?php echo $sumProceso; ?></td>
                                            </tr>
                                            <?php foreach ($Array_Proceso_Valoracion as $proceso) { ?>
                                                <tr>
                                                    <td><?php echo $proceso[1]; ?></td>
                                                    <td style="text-align: center;"><?php echo $arrayTotalProceso[$proceso[0]]; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                </table>



                <div>
                    <a href="">
                        <button class="btn btn-primary">Gestionar Valoraciones</button>
                    </a>
                </div>
                <?php } ?>

            </div>
        </div>

      </div>


    </div>
  </div>
</div>


























<div class="modal fade" id="modal_avance_valoracion_2" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- cerrar -->
      <div class="modal-header">
        <h5 class="modal-title"></h5>
        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body" align="center">

        <a href="?pg=competencias/realizar_valoracion">
            <img src="assets/img/img_pop_3.jpeg" alt="" width="100%">
        </a>

      </div>


    </div>
  </div>
</div>





















<?php
/*
include("views/competencias_pc/modal_competencias.php");
include("views/competencias_pc/modal_firma.php");
include("views/competencias_pc/informes/funciones.php");
$hoy = date("Y-m-d H:i:s");
if ($_POST["guardar_firma"] != "") {

    $queryFirma = "UPDATE Competencias_Evaluaciones_New SET firma_aprobacion = '" . $_POST["firma"] . "', proceso_valoracion = 7, estado = 3, fecha_firma = '$hoy',update_at = '$hoy' WHERE id_evaluado = " . $_SESSION['id_user'] . "";

    $accion = 'ACTUALIZAR';
    $descripcion = 'Actualización de firma de pdi por parte del colaborador ' . $colaborador["nombre"];
    mysqli_query($connect_competencias_pc, $queryFirma);


    $auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
VALUES (" . $_SESSION["id_empresa"] . ", " . $_SESSION["id_user"] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
    mysqli_query($connect_okrs, $auditoria);

    echo '<script> window.location.href = "?pg=competencias_pc/informes/reporte_individual&e=' . $_SESSION['id_user'] . '";</script>';
}

if ($_POST["enviar_intervencion"] != "") {

    $queryRelacion = mysqli_query($connect_competencias_pc, "SELECT * FROM Relaciones_Laborales WHERE areas LIKE '%" . $_SESSION["area"] . "%' ");
    $dataRelacion = mysqli_fetch_array($queryRelacion);
    if (mysqli_num_rows($queryRelacion) == 0) {
        echo '<script> alert("No tiene asignado el responsable para Intervención de GH");</script>';
        echo '<script> window.location.href = "?pg=competencias_pc/informes/reporte_individual&e=' . $_SESSION["id_user"] . '";</script>';
    } else {
        $evaluaciones = "";

        $queryEvaluaciones = mysqli_query($connect_competencias_pc, "SELECT * FROM Competencias_Evaluaciones_New WHERE id_evaluado = " . $_SESSION["id_user"] . " AND estado = 2 AND proceso_Valoracion = 4 ");
        while ($dataEvaluaciones = mysqli_fetch_array($queryEvaluaciones)) {
            $evaluaciones .= $dataEvaluaciones["id"] . ",";
        }

        if ($evaluaciones != "") {
            $evaluaciones = substr($evaluaciones, 0, -1);
        }

        $queryIntervencion = "INSERT INTO Intervencion_Gh (id_empresa,id_responsable,id_evaluado,evaluaciones,comentario,ciclo,estado,created_at) VALUES
(" . $_SESSION["id_empresa"] . "," . $dataRelacion["id_empleado"] . "," . $_SESSION["id_user"] . ",'$evaluaciones','" . $_POST["comentario"] . "'," . $_SESSION["anio_ciclo"] . ",1,'$hoy')";

        mysqli_query($connect_competencias_pc, $queryIntervencion);

        $queryEnvio = "UPDATE Competencias_Evaluaciones_New SET proceso_valoracion = 6, update_at = '$hoy' WHERE id_evaluado = " . $_SESSION["id_user"] . " AND estado = 2";
        // echo $queryEnvio;
        $accion = 'ACTUALIZAR';
        $descripcion = 'Actualización envio de Pdi a intervención GH para el colaborador ' . $colaborador["nombre"];
        mysqli_query($connect_competencias_pc, $queryEnvio);


        $auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
VALUES (" . $_SESSION["id_empresa"] . ", " . $_SESSION["id_user"] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
        // echo $auditoria;
        mysqli_query($connect_okrs, $auditoria);
        //
        echo '<script> window.location.href = "?pg=competencias_pc/informes/reporte_individual&e=' . $_SESSION["id_user"] . '";</script>';
    }
}

$boton1 = $boton2 = '';
$modal = 0;











$queryAuto = mysqli_query($connect_competencias_pc, "SELECT * FROM Evaluadores
WHERE anio = '" . $_SESSION['anio_ciclo'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND id_empleado = '" . $_SESSION['id_user'] . "' AND tipo = 1 ");
$queryJefe = mysqli_query($connect_competencias_pc, "SELECT * FROM Evaluadores
WHERE anio = '" . $_SESSION['anio_ciclo'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND id_empleado = '" . $_SESSION['id_user'] . "' AND tipo != 1 ");
$dataJefe = mysqli_fetch_array($queryJefe);
if (mysqli_num_rows($queryAuto) > 0) {
    $modal = 1;
    $queryEval1 = mysqli_query($connect_competencias_pc, "SELECT * FROM Competencias_Evaluaciones_New
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'
    AND id_ciclo = '" . $_SESSION['ciclo'] . "'
    AND id_evaluado = '" . $_SESSION['id_user'] . "'
    AND id_evaluador = '" . $_SESSION['id_user'] . "'
    AND anio = '" . $_SESSION["anio_ciclo"] . "'");
    if (mysqli_num_rows($queryEval1) > 0) {
        $dataEval1 = mysqli_fetch_array($queryEval1);
        if ($dataEval1["estado"] == 1) {
            // $modal = 2;
            $txt_estado = 'En Proceso de Autovaloración';
            $background = 'background-color: #6B21FF !important;';
            $mensaje = '¡Es hora de continuar su autovaloración! Complete su valoración para que su líder/supervisor pueda continuar con la valoración de sus competencias.';
            $boton1 = '<a href="' . $url . '?pg=competencias_pc/evaluacion&id=' . $dataEval1["id"] . '&pos=1" class="btn btn-agile" style="' . $background . 'color: white !important;border-radius: 30px;">Continuar Autovaloración</a>';
        }

        if ($dataEval1["estado"] == 2) {
            // $modal = 2;
            $txt_estado = 'Autovaloración terminada';
            $mensaje = '¡Autovaloración enviada con éxito! Su líder/supervisor directo ahora está en proceso de realizar la valoración correspondiente.';
            $background = 'background-color: #007BFF !important;';
            $boton1 = '<a href="' . $url . '?pg=competencias_pc/mi_informe" class="btn btn-primary" style="' . $background . 'color: white !important;border-radius: 30px;">Ver Estado de la Evaluación</a>';
            $queryEval2 = mysqli_query($connect_competencias_pc, "SELECT * FROM Competencias_Evaluaciones_New
            WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'
            AND id_ciclo = '" . $_SESSION['ciclo'] . "'
            AND id_evaluado = '" . $_SESSION['id_user'] . "'
            AND id_evaluador = '" . $dataJefe['id_evaluador'] . "'
            AND anio = '" . $_SESSION["anio_ciclo"] . "'");
            if (mysqli_num_rows($queryEval2) > 0) {
                $dataEval2 = mysqli_fetch_array($queryEval2);
                if ($dataEval2["estado"] == 2 && $dataEval2["proceso_valoracion"] == 4) {
                    // $modal = 2;
                    $txt_estado = 'PDI Listo para Firma';
                    $mensaje = 'Su Plan de Desarrollo Individual (PDI) está listo para ser revisado y firmado. Puede firmar desde tu celular escaneando el siguiente código QR o ingresando desde su computadora.<hr>
                    Si no está de acuerdo con su Plan de Desarrollo Individual (PDI), puede solicitar la intervención del equipo de Relaciones Laborales para revisar el caso.';
                    $background1 = 'background-color: #28a745 !important;';
                    $background2 = 'background-color: #f4815e !important;';
                    $boton1 = '<button class="btn btn-success" style="' . $background1 . 'color: white !important;border-radius: 30px;" onClick="FirmaAprobacion(' . $_SESSION["id_user"] . ')">Firmar PDI Ahora</button>';
                    $boton2 = '<button class="btn btn-warning" style="' . $background2 . 'color: white !important;border-radius: 30px;" onClick="IntervencionGH(' . $_SESSION["id_user"] . ')">Solicitar Intervención</button>';
                }
            }
        }

        if ($dataEval1["estado"] == 3) {
            $txt_estado = 'Valoración Finalizada';
            $background = 'background-color: #007BFF !important;';
            $mensaje = '¡Felicidades! Ha finalizado exitosamente su proceso de valoración de competencias.<br>
            Gracias por su compromiso con el desarrollo profesional y el crecimiento dentro de la organización.<br>
            Recuerde que podrá consultar su PDI y seguimiento a sus acciones de desarrollo en cualquier momento desde la plataforma.';
            $boton1 = '<a href="' . $url . '?pg=competencias_pc/mi_informe" class="btn btn-agile" style="' . $background . 'color: white !important;border-radius: 30px;">Ver mi PDI</a>';
        }
    } else {
        // $modal = 2;
        $txt_estado = 'Su valoración está pendiente';
        $background = 'background-color: #6B21FF !important;';
        $mensaje = '¡Es hora de iniciar su autovaloración! Complete su valoración para que su líder/supervisor pueda continuar con la valoración de sus competencias.';
        $boton1 = '<a href="' . $url . '?pg=competencias_pc/evaluacion&evaluado=' . $_SESSION['id_user'] . '&t=1&pos=1" class="btn btn-agile" style="' . $background . 'color: white !important;border-radius: 30px;">Iniciar Autovaloración</a>';
    }
} elseif (mysqli_num_rows($queryJefe) > 0) {
    $modal = 1;

    $queryEval2 = mysqli_query($connect_competencias_pc, "SELECT * FROM Competencias_Evaluaciones_New
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'
    AND id_ciclo = '" . $_SESSION['ciclo'] . "'
    AND id_evaluado = '" . $_SESSION['id_user'] . "'
    AND id_evaluador = '" . $dataJefe['id_evaluador'] . "'
    AND anio = '" . $_SESSION["anio_ciclo"] . "'");
    if (mysqli_num_rows($queryEval2) > 0) {
        $dataEval2 = mysqli_fetch_array($queryEval2);
        if ($dataEval2["estado"] == 2 && $dataEval2["proceso_valoracion"] == 4) {
            // $modal = 2;
            $txt_estado = 'PDI Listo para Firma';
            $mensaje = 'Su Plan de Desarrollo Individual (PDI) está listo para ser revisado y firmado. Puede firmar desde tu celular escaneando el siguiente código QR o ingresando desde su computadora.<hr>
                    Si no está de acuerdo con su Plan de Desarrollo Individual (PDI), puede solicitar la intervención del equipo de Relaciones Laborales para revisar el caso.';
            $background1 = 'background-color: #28a745 !important;';
            $background2 = 'background-color: #f4815e !important;';
            $boton1 = '<button class="btn btn-success" style="' . $background1 . 'color: white !important;border-radius: 30px;" onClick="FirmaAprobacion(' . $_SESSION["id_user"] . ')">Firmar PDI Ahora</button>';
            $boton2 = '<button class="btn btn-warning" style="' . $background2 . 'color: white !important;border-radius: 30px;" onClick="IntervencionGH(' . $_SESSION["id_user"] . ')">Solicitar Intervención</button>';
        } else {
            $txt_estado = 'Valoración en proceso';
            $mensaje = 'Estamos a la espera de que su jefe directo complete el proceso de valoración de competencias. Le notificaremos cuando su valoración haya finalizado.';
            $background = 'background-color: #007BFF !important;';
            $boton1 = '<a href="' . $url . '?pg=competencias_pc/mi_informe" class="btn btn-primary" style="' . $background . 'color: white !important;border-radius: 30px;">Ver Estado de la Evaluación</a>';
        }
    } else {
        $txt_estado = 'Valoración sin iniciar';
        $mensaje = 'Estamos a la espera de que su jefe directo complete el proceso de valoración de competencias. Le notificaremos cuando su valoración haya finalizado.';
        $background = 'background-color: #007BFF !important;';
    }
}

if ($_SESSION["role_plataforma"] == 1 || $_SESSION["role_plataforma"] == 2) {
    // $colorHeader = 'background-color: #ffc107 !important;';
    $colorHeader = 'background-color: #ffc107 !important;';
    $colorFooter = 'background-color: #007BFF !important;';
    $colortext = 'color: black !important;';
    $colorFooterText = 'color: white !important;';
} else {
    $colorHeader = 'background-color: #3aae2a !important;';
    $colortext = 'color: white !important;';
}
if ($ciclo_cerrado == true) {
    $modal = 0;
    $bt_editar1 = 'Ciclo Finalizado';
}
    */

if ($modal == 1) {
/**Se oculta Modal Proceso de Valoración */
?>

    <!-- <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="<?php echo $colorHeader; ?>">
                    <div class="row">
                        <div class="col-md-12" style="text-align: center;">
                            <h4 style="<?php echo $colortext; ?>">Estado Actual de su Proceso de Valoración de Competencias</h4>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12" style="text-align: center;">
                            <h4 style="color: black !important;"><?php echo $txt_estado; ?></h4>
                            <p style="color: black !important;font-size: 1.2rem !important;"><?php echo $mensaje; ?></p>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12" style="text-align: center;">
                            <?php echo $boton1; ?>&nbsp;&nbsp;<?php echo $boton2; ?>
                        </div>
                    </div>
                </div>
                <?php if ($_SESSION["role_plataforma"] == 1 || $_SESSION["role_plataforma"] == 2) {
                    $count = 1;
                    $countValoracion = $sumEscalaRes1 = 0;
                    $sumProceso = $sumProceso1 = $sumProceso2 = $sumProceso3 = $sumProceso4 = $sumProceso5 = $sumProceso6 = $sumProceso7 = 0;
                    $queryEvaluadores1 = mysqli_query($connect_competencias_pc, "SELECT EV.* FROM Evaluadores EV
								INNER JOIN goforagile_admin.Empleados E ON E.id = EV.id_empleado
								WHERE EV.id_evaluador = '" . $_SESSION['id_user'] . "' AND EV.anio = '" . $_SESSION['anio_ciclo'] . "' AND EV.id_ciclo = '" . $_SESSION['ciclo'] . "' AND EV.id_empleado != '" . $_SESSION['id_user'] . "' ORDER BY E.nombre ASC");
                    while ($colaborador = mysqli_fetch_array($queryEvaluadores1)) {
                        $lista_reportes = '';
                        $queryEvaluadores = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores
		                WHERE id_empleado = '" . $colaborador["id_empleado"] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND anio = " . $_SESSION['anio_ciclo'] . " AND tipo != 1");
                        while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) {
                            $id_colaborador = $colaborador["id_empleado"];
                            $VALIDACION = PromedioGeneralEvaluado($id_colaborador, $connect_valoracion, $connect_valentina);

                            $queryEvaluaciones = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND
			                anio = '" . $dataCicloVal["anio"] . "' AND id_evaluado = '" . $dataEvaluadores["id_empleado"] . "' AND id_evaluador = '" . $dataEvaluadores["id_evaluador"] . "' AND
			                tipo_evaluacion = '" . $dataEvaluadores["tipo"] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' ");
                            $dataEvaluacion = mysqli_fetch_array($queryEvaluaciones);

                            $queryEvaluador = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '" . $dataEvaluadores["id_evaluador"] . "' ");
                            $dataEvaluador = mysqli_fetch_array($queryEvaluador);

                            $color_estado = '';
                            $tipo_txt = '';
                            foreach ($array_Tipo_Colaborador as $tipo) {
                                if ($tipo[0] == $dataEvaluadores["tipo"]) {
                                    $tipo_txt =  $tipo[1];
                                }
                            }

                            if ($queryEvaluaciones->num_rows > 0) {
                                $countValoracion++;
                                if ($dataEvaluacion["estado"] == 1) {
                                    $en_proceso++;
                                }

                                if ($dataEvaluacion["estado"] == 3) {
                                    $en_proceso++;
                                }

                                if ($dataEvaluacion["estado"] == 2) {
                                    $terminadas++;
                                }
                            } else {
                                $pendientes++;
                            }

                            switch ($VALIDACION["proceso_valoracion"]) {
                                case 1:
                                    $sumProceso1++;
                                    break;
                                case 2:
                                    $sumProceso2++;
                                    break;
                                case 3:
                                    $sumProceso3++;
                                    break;
                                case 4:
                                    $sumProceso4++;
                                    break;
                                case 5:
                                    $sumProceso5++;
                                    break;
                                case 6:
                                    $sumProceso6++;
                                    break;
                                case 7:
                                    $sumProceso7++;
                                    break;
                                default:
                                    $sumProceso++;
                                    break;
                            }
                        }
                    }

                    $arrayTotalProceso = array();
                    $arrayTotalProceso[1] = $sumProceso1;
                    $arrayTotalProceso[2] = $sumProceso2;
                    $arrayTotalProceso[3] = $sumProceso3;
                    $arrayTotalProceso[4] = $sumProceso4;
                    $arrayTotalProceso[5] = $sumProceso5;
                    $arrayTotalProceso[6] = $sumProceso6;
                    $arrayTotalProceso[7] = $sumProceso7;

                    $valoracion0 = round((($sumProceso / $countValoracion) * 100), 2);

                    if (is_nan($valoracion0) || is_infinite($valoracion0)) {
                        $sinValoracion = 0;
                    } else {
                        if ($valoracion0 > 100) {
                            $sinValoracion = 100;
                        } else {
                            $sinValoracion = $valoracion0;
                        }
                    }
                    // print_r($arrayTotalProceso);
                ?>
                    <div class="card-footer">
                        <div class="row" style="<?php echo $colorFooter; ?>">
                            <div class="col-md-12" style="text-align: center;">
                                <h4 style="<?php echo $colorFooterText; ?>">Resumen General del Estado de Valoración de sus Colaboradores</h4>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12" style="text-align: center;">
                                <h5 style="color: black !important;">Resumen del estado de valoración para los colaboradores asignados. Consulte el progreso actual y continúe con las acciones necesarias para finalizar el proceso.</h5>
                            </div>
                        </div>
                        <br><br>
                        <div class="row" style="align-items: center;">

                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table border="1" id="notificacion_competencias_pc" class="display table" style="width:100%;">
                                        <thead class="table-dark">
                                            <tr style="text-align: center;">
                                                <th>Estado</th>
                                                <th>N° de Colaboradores</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Sin Valoración</td>
                                                <td style="text-align: center;"><?php echo $sumProceso; ?></td>
                                            </tr>
                                            <?php foreach ($Array_Proceso_Valoracion as $proceso) { ?>
                                                <tr>
                                                    <td><?php echo $proceso[1]; ?></td>
                                                    <td style="text-align: center;"><?php echo $arrayTotalProceso[$proceso[0]]; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>



                        </div>
                        <br><br>
                        <div class="row">
                            <div class="col-md-12" style="text-align: center;">
                                <a href="<?php echo $url ?>?pg=competencias_pc/realizar_valoracion" class="btn btn-primary" style="background-color: #007BFF !important;color: white !important;border-radius: 30px;">Gestionar Valoraciones</a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div> -->
<?php } ?>
    
