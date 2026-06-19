<?php
	session_start();
	include("../app/connect.php");	
    include("../app/arrays.php");	

    include("../app/models/brevo/Brevo.php");

    function TablaPlantilla($id_empleado, $anio, $ciclo){

        global $connect_valoracion;
        global $Array_Proceso_Valoracion;
        global $connect_admin;

        //colaborador
        $queryCol = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '".$id_empleado."' ");
        $dataCol = mysqli_fetch_array($queryCol);

        //DATOS EVALUACION PROPIA
        $queryValidarAuto = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores
        WHERE anio = '" . $anio . "' AND id_ciclo = '" . $ciclo . "' AND id_empleado = '" .$id_empleado. "' AND tipo = 1 ");

        $txt_estado_avance = "Su Valoración está pendiente";
        $mensaje_avance = '¡Es hora de iniciar su autovaloración! Complete su valoración para que su líder/supervisor pueda continuar con la valoración de sus competencias.';
        $boton1 = '
        <a href="https://puntacana.goforagile.com/?pg=competencias/realizar_valoracion">
            <button class="btn btn-primary">Iniciar Autovaloración</button>
        </a>
        ';

        if($queryValidarAuto->num_rows > 0 ){

            $queryValidarAutoEstado = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New
            WHERE id_empresa = 1
            AND id_ciclo = '" . $ciclo . "'
            AND id_evaluado = '" . $id_empleado . "'
            AND id_evaluador = '" . $id_empleado . "'
            AND anio = '" . $anio . "'");
            $dataValidarAutoEstado = mysqli_fetch_array($queryValidarAutoEstado);


            if($dataValidarAutoEstado["estado"] == 1){
                $txt_estado_avance = 'En Proceso de Autovaloración';
                $mensaje_avance = '¡Es hora de continuar su autovaloración! Complete su valoración para que su líder/supervisor pueda continuar con la valoración de sus competencias.'; 
                $boton1 = '
                <a href="https://puntacana.goforagile.com/?pg=competencias/realizar_valoracion">
                    <button class="btn btn-primary">Continuar Autovaloración</button>
                </a>
                ';
            }
            if($dataValidarAutoEstado["estado"] == 2){
                $txt_estado_avance = 'Autovaloración terminada';
                $mensaje_avance = '¡Autovaloración enviada con éxito! Su líder/supervisor directo ahora está en proceso de realizar la valoración correspondiente.';
                $boton1 = '<a href="https://puntacana.goforagile.com/?pg=competencias_pc/mi_informe" class="btn btn-primary" style="' . $background . 'color: white !important;border-radius: 30px;">Ver Estado de la Evaluación</a>';

                if ($dataEval2["proceso_valoracion"] == 4) {
                    $txt_estado_avance = 'PDI Listo para Firma';
                    $mensaje_avance = 'Su Plan de Desarrollo Individual (PDI) está listo para ser revisado y firmado. Puede firmar desde tu celular escaneando el siguiente código QR o ingresando desde su computadora.<hr>
                    Si no está de acuerdo con su Plan de Desarrollo Individual (PDI), puede solicitar la intervención del equipo de Relaciones Laborales para revisar el caso.';
                    $boton1 = '
                    <a href="https://puntacana.goforagile.com/?pg=competencias/realizar_valoracion">
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
                    <a href="https://puntacana.goforagile.com/?pg=competencias/mi_informe">
                        <button class="btn btn-primary">Ver mi PDI</button>
                    </a>
                ';
            }     
        }




































        $sumProceso1 = $sumProceso2 = $sumProceso3 = $sumProceso4 = $sumProceso5 = $sumProceso6 = $sumProceso7 = 0;

        $queryValidarTodasEvaluacionesUser = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores
        WHERE anio = '" .$anio. "' AND id_ciclo = '" .$ciclo. "' AND id_evaluador = '" .$id_empleado. "' AND tipo IN (5) ");
        while ($dataValidarTodasEvaluacionesUser = mysqli_fetch_array($queryValidarTodasEvaluacionesUser)) { 

            $queryValidarEval = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New
            WHERE id_empresa = '1'
            AND id_ciclo = '" . $ciclo . "'
            AND id_evaluado = '" . $dataValidarTodasEvaluacionesUser['id_empleado'] . "'
            AND id_evaluador = '" . $dataValidarTodasEvaluacionesUser['id_evaluador'] . "'
            AND anio = '" . $anio . "'");
            $dataValidarValidar = mysqli_fetch_array($queryValidarEval);

            $dataValidarTodasEvaluacionesUser["proceso_valoracion"] = $dataValidarValidar["proceso_valoracion"];


            //EN CASO DE NO TENER LA EVALUACION DEL JEFE
            if($queryValidarEval->num_rows == 0){
                //VALIDAR AUTOEVALUACION
                //VALIDAR AUTOEVALUACION
                $queryValidarAuto = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New
                WHERE id_empresa = '1'
                AND id_ciclo = '" . $ciclo . "'
                AND id_evaluado = '" . $dataValidarTodasEvaluacionesUser['id_empleado'] . "'
                AND anio = '" . $anio . "' 
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

        $lista_filas = '';
        foreach ($Array_Proceso_Valoracion as $proceso) {
            $lista_filas .= '
            <tr>
                <td>'.$proceso[1].'</td>
                <td style="text-align: center;">'.$arrayTotalProceso[$proceso[0]].'</td>
            </tr>
            ';
                                                
        }

        $string = '
        <div style="margin: 0 auto; max-width: 700px; font-family: sans-serif; font-size: 14px;">

            <style>
            .btn{
                padding: 10px 30px;
                border-radius: 10px;
                background-color: #03a9f4;
                border: 0;
                margin: 10px;
                color: #ffffff;
                font-weight: bold;
            }
            </style>

            <div>
                <br><br>
                Estimado(a) '.$dataCol["nombre"].'<br><br>

                A continuación presentamos el avance del proceso de evaluación de competencias de su equipo.<br><br>
            </div>

            <div style="background-color: #FFC107; padding: 10px; color: #ffffff; text-align: center; font-size: 20px; font-weight: bold;">
                Estado Actual de su Proceso de Valoración de Competencias
            </div>

            <div style=" text-align: center; ">
                <h5>'.$txt_estado_avance.'</h5>
                <p>'.$mensaje_avance.'</p>
                <p>'.$boton1.'</p>
            </div>
            
           


                









            <div style="background-color: #03A9F4; padding: 10px; color: #ffffff; text-align: center; font-size: 20px; font-weight: bold;">
                Resumen General del Estado de Valoración de sus Colaboradores
            </div>

            <div style=" padding: 20px; text-align: center; ">
                Resumen del estado de valoración para los colaboradores asignados. Consulte el progreso actual y continúe con las acciones necesarias para finalizar el proceso.
            </div>

            
        

            <table border="0" cellpadding="3" style="width:100%; border: 1px solid #cccccc; ">
                <thead style="background-color: #000000;color: #ffffff;">
                    <tr style="text-align: center;">
                        <th>Estado</th>
                        <th>N° de Colaboradores</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Sin Valoración</td>
                        <td style="text-align: center;">'.$sumProceso.'</td>
                    </tr>
                    '.$lista_filas.'
                </tbody>
            </table>



            <div style="text-align: center;">
                <a href="https://puntacana.goforagile.com/?pg=competencias/realizar_valoracion" target="_blank">
                    <button class="btn btn-primary" style="padding: 10px 30px; border-radius: 10px; background-color: #03a9f4; border: 0; margin: 10px; color:#ffffff; font-weight: bold;">Gestionar Valoraciones</button>
                </a>
            </div>

        </div>
        ';

        return $string;

       
    }

    $asunto = "GoFor Agile - Avance Competencias ";
    //$plantilla = PlantillaAvance( $nombre, $correo, $password );
    //$correo = "ialvarado@changeamericas.com";
    //$correo = "alozano@puntacana.com";
    $ClassBrevo = new Brevo();
    
    $respuesta = '
        <div class="alert alert-success" role="alert">
            Hemos enviado un correo con tus datos de acceso.
        </div>
    ';

    $queryJefes = mysqli_query($connect_valoracion, " SELECT * FROM Evaluadores WHERE id_ciclo = 21 AND tipo = 5  GROUP BY id_evaluador  ");
    while($dataJefe = mysqli_fetch_array($queryJefes)){

        $queryCol = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '".$dataJefe["id_evaluador"]."' ");
        $dataCol = mysqli_fetch_array($queryCol);

        $correo = $dataCol["correo"]; 
        //$correo = "eniac321@gmail.com";
        $plantilla = TablaPlantilla( $dataJefe["id_evaluador"] , 2026, 21);
        //echo $correo; 
        //echo $plantilla;

        //$ClassBrevo->individual( "GoFor Agile", $asunto, $correo, $plantilla );

        echo $dataCol["correo"]; ;
        echo "<hr>";

    }



?>















































