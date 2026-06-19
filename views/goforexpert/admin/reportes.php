<?php
//$filtro = "";
$filtro_contenidos = "";
$filtro_modulos = "";

$id_modulo = isset($_POST["id_modulo"]) ? $_POST["id_modulo"] : "";

if (!empty($id_modulo)) {

    //$filtro .= " AND id_modulo = '" . $id_modulo . "' ";
    $filtro_contenidos .= " AND id_curso_modulo = '" . $id_modulo . "' ";
    $filtro_modulos = " WHERE id = '" . $id_modulo . "' ";
}
?>

<script>
    
    function exportarReporte(){
            $("#datos_a_enviar").val( $("<div>").append( $("#TablaExcel").eq(0).clone()).html());
            $("#FormularioExportacion").submit();
        }
    
   
</script>

<div align="left" style="padding: 10px 0px;">
    <table width="100%">
        <tr>
            <td>
                <h5 style="margin-top: 8px;"><i class="fas fa-check"></i> REPORTES</h5>
            </td>
            <td align="right">
                <input class="form-control" type="text" placeholder="Búsqueda rápida..." id="buscador" style="width: 200px; display: inline-table;" />
                
            </td>
        </tr>
    </table>
</div>


<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Inicio</a></li>

            <li class="breadcrumb-item"><a href="<?php echo $url; ?>?pg=academia/admin/final">Reporte Academia</a></li>

            <li class="breadcrumb-item active" aria-current="page"><a href="">Detalle</a></li>
        </ol>
    </nav>

    <div class="card">

        <div class="card-body">

            <form action="" method="post" enctype="multipart/form-data">
                <div class="row">

                    <input type="hidden" name="id_registro" value="<?php echo $id; ?>">
                    <div class="col-md-12 item_input">
                        <select class="form-control" name="id_modulo" required style=" width: 200px; display: inline-table;">
                            <option value="">Selecciona Cursos...</option>
                            <?php
                            $queryEval = mysqli_query($connect_academia, " SELECT * FROM  Cursos  
                                WHERE estado  = 1 ORDER BY nombre ASC ");
                            while ($dataEval = mysqli_fetch_array($queryEval)) {
                                if ($dataEval["id"] == $_POST["curso_fill"]) {
                                    echo '<option value="' . $dataEval["id"] . '" selected>' . $dataEval["nombre"] . '</option>';
                                } else {
                                    echo '<option value="' . $dataEval["id"] . '">' . $dataEval["nombre"] . '</option>';
                                }
                            }
                            ?>
                        </select>
                        <button type="submit" class="btn btn-success " style="border-radius: 30px;">Filtrar</button>
                        
                        <button type="button" class="btn btn-danger" onclick="exportarReporte()">
                            <i class="fas fa-download">Exportar a Excel</i>
                        </button>
                    </div>


                </div>
            </form>

        </div>
    </div>




<!-- export-->
    <form action="app/models/exportarExcel.php" method="post" target="_blank" id="FormularioExportacion">
        <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
    </form>

    <table class="table table-bordered" style="margin-top: 20px" id="TablaExcel" border="1">
        <tr>
            <th>#</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Documento</th>
            <th>Avance Módulos</th>
            <th>Eval. Totales</th>
        </tr>

        <tbody id="tabla_lista">

            <?php
            $count = 1;
            $count_total = 0;

            //GLOBALES
            $global_evaluaciones = 0;
            $global_evaluaciones_estudiante = 0;
            $realizados = 0;
            $pendientes = 0;

            if (!empty($id_modulo)) {

                $queryCaps = mysqli_query($connect_academia, "SELECT * FROM Cursos_Capitulos 
                WHERE id_curso = '".$id_modulo."' ");

                $queryEstudianteCursos = mysqli_query($connect_academia, "SELECT * FROM Estudiantes_Cursos WHERE id_curso = '" . $id_modulo . "'");
                $estudiantesCursos = array();
                $estudiantesTotal = array();
                while ($row = mysqli_fetch_array($queryEstudianteCursos)) {
                    $estudiantesCursos[] = $row;
                }


                foreach ($estudiantesCursos as $estudianteCurso) {
                    $id_estudiante = $estudianteCurso['id_estudiante'];

                    $queryEstudiante = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE estado = 1 AND id = '" . $id_estudiante . "'");
                    while ($dataEstudiante = mysqli_fetch_array($queryEstudiante)) {

                        //TOTAL EVALUACIONES
                        $queryEval = mysqli_query($connect_academia, "SELECT * FROM Estudiantes_Evaluaciones WHERE id_estudiante = '" . $id_estudiante . "' AND id_curso = '".$id_modulo."'  ");
                        $total_evaluaciones = $queryEval->num_rows;

                        //TOTAL COMPLETADAS
                        $queryEvaluaciones = mysqli_query($connect_academia, "SELECT * FROM Estudiantes_Evaluaciones WHERE id_estudiante = '" . $id_estudiante . "' AND id_curso = '".$id_modulo."' AND estado = 2  ");
                        $total_evaluaciones_estudiante = $queryEvaluaciones->num_rows;

                        $obj_avances; 
                        if($estudianteCurso["obj_avance"]){
                            $obj_avances = json_decode($estudianteCurso["obj_avance"], true);
                        }

                        $porcentaje = 0;
                        if( count($obj_avances) > 0){
                            $porcentaje = (count($obj_avances) * 100) / $queryCaps->num_rows;
                            $porcentaje = round($porcentaje,2);
                        }

                        $global_evaluaciones += $total_evaluaciones;
            
                        $barra = '
                        <div class="base_base">
                            <div class="barra_porcentaje" style="width: ' . $porcentaje . '%">
                            ' . round($porcentaje, 2) . '%
                            </div>
                        </div>
                        ';
                        
                        echo '
                            <tr>
                                <td>' . $count . '</td>
                                <td>' . $dataEstudiante["nombre"] . ' </td>
                                <td>' . $dataEstudiante["correo"] . '</td>
                                <td>' . $dataEstudiante["documento"] . '</td>
                                <td>' . $barra . '</td>
                                <td><b># ' . $total_evaluaciones . ' </b></td>

                            </tr>
                            ';

                        $count++;
                    }
                }
            } else {

                /*

                $queryEstudiante = mysqli_query($connect_admin, "SELECT * FROM Colaboradores WHERE estado = 1  ORDER BY nombre ASC ");
                while($dataEstudiante = mysqli_fetch_array($queryEstudiante)) {

                    $total_evaluaciones = 0;
                    $queryVali = mysqli_query($connect_academia, "SELECT * FROM Estudiantes_Cursos 
                    WHERE id_estudiante = '" . $dataEstudiante["id"] . "' AND id_curso =  '" . $id . "' ");
                    $total_evaluaciones = $queryVali->num_rows;

                    

                    $global_evaluaciones += $total_evaluaciones;

                    $queryEvaluaciones = mysqli_query($connect_academia, "SELECT * FROM Estudiantes_Evaluaciones WHERE id_estudiante = '" . $dataEstudiante["id"] . "' AND (estado = 2) " . $filtro . "  ");
                    $total_evaluaciones_estudiante = $queryEvaluaciones->num_rows;

                    
                    $porcentaje = 0;
                    if ($total_evaluaciones_estudiante > 0) {
                        $porcentaje = ($total_evaluaciones_estudiante * 100) / $total_evaluaciones;
                        $realizados++;
                        $global_evaluaciones_estudiante += $total_evaluaciones_estudiante;
                    } else {
                        $pendientes++;
                    }

                    if ($porcentaje > 100) {
                        $porcentaje = 100;
                    }
                        

                    $barra = '
                    <div class="base_base">
                        <div class="barra_porcentaje" style="width: ' . $porcentaje . '%">
                           ' . round($porcentaje, 2) . '%
                        </div>
                    </div>
                    ';

                    

                    echo '
                    <tr>
                        <td>' . $count . '</td>
                        <td>' . $dataEstudiante["nombre"] . ' ' . $dataEstudiante["apellidos"] . '</td>
                        <td>' . $dataEstudiante["correo"] . '</td>
                        <td>' . $dataEstudiante["documento"] . '</td>
                        <td>' . $barra . '</td>
                        <td># ' . $total_evaluaciones . ' de ' . $total_evaluaciones_estudiante . '</td>

                    </tr>
                    ';

                    $count++;
                }
                */
            }

            //$porcentaje_global = ($global_evaluaciones_estudiante * 100) / $global_evaluaciones;
            //$porcentaje_global = round($porcentaje_global, 2);

            ?>


        </tbody>

    </table>
</div>

<div>
    <h2>
        Promedio general avance módulo: <?php echo $porcentaje_global; ?>%<br>
        Total Estudiantes: <?php echo $queryEstudiante->num_rows; ?><br>
    </h2>
</div>




<style>
    .base_base {
        background-color: #cccccc;
        width: 150px;

    }

    .barra_porcentaje {
        background-color: #71FF00;
        border-radius: 0px 10px 10px 0px;
    }

    .col-md-4 {
        margin-bottom: 15px
    }

    .col-md-6 {
        margin-bottom: 15px
    }

    .btn-primary_blue {
        color: #fff;
        background-color: #2196f3;
        border-color: #2196f3;
    }
</style>



<script>

    $(document).ready(function() {
        $("#buscador").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#tabla_lista tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    
</script>