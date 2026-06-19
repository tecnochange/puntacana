<script>
    $(document).ready(function() {
        $('#menuAcademia').collapse();
        $("#bt_academia_historicos_reportes").addClass("active");
    });
</script>

<?php
// Inicializa la variable de filtros
$filtros = " AND Estudiantes_Evaluaciones.promedio >= 0 ";  // Excluir resultados con promedio 0 y NULL

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Agrega condiciones a los filtros si se han enviado valores en el formulario
    if (!empty($_POST["programa_fill"])) {
        $filtros .= " AND Programas.id = '" . mysqli_real_escape_string($connect_academia, $_POST["programa_fill"]) . "' ";
    }

    if (!empty($_POST["curso_fill"])) {
        $filtros .= " AND Cursos.id = '" . mysqli_real_escape_string($connect_academia, $_POST["curso_fill"]) . "' ";
    }

    if (!empty($_POST["evaluacion_fill"])) {
        $filtros .= " AND Estudiantes_Evaluaciones.id_contenido = '" . mysqli_real_escape_string($connect_academia, $_POST["evaluacion_fill"]) . "' ";
    }

    if (!empty($_POST["estudiante_fill"])) {
        $filtros .= " AND Estudiantes_Evaluaciones.id_estudiante = '" . mysqli_real_escape_string($connect_academia, $_POST["estudiante_fill"]) . "' ";
    }
}
?>

<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
            <li class="breadcrumb-item"><a href="<?php echo $url; ?>?pg=academia/admin/final">Reporte Academia</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="">Detalle</a></li>
        </ol>
    </nav>

    <div align="left" style="padding: 10px 0px;">
        <table width="100%">
            <tr>
                <td>
                    <h2 style="margin-top: 8px;">Reporte de Evaluaciones</h2>
                </td>
                <td align="right">
                    <input class="form-control" type="text" placeholder="Búsqueda rápida..." id="buscador" style="width: 200px; display: inline-table;" />
                </td>
            </tr>
        </table>
    </div>

    <form action="" method="post">
        <div class="row">
            <div class="col-md-3" style="margin-bottom: 10px; display: none">
                <select class="form-control" name="programa_fill">
                    <option value="">Seleccione Programas...</option>
                    <?php
                    $queryEval = mysqli_query($connect_academia, "SELECT * FROM Programas WHERE estado = 1 ORDER BY nombre ASC");
                    while ($dataEval = mysqli_fetch_array($queryEval)) {
                        echo '<option value="' . $dataEval["id"] . '"' . 
                            ($dataEval["id"] == $_POST["programa_fill"] ? ' selected' : '') . '>' . 
                            $dataEval["nombre"] . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-3" style="margin-bottom: 10px; display: none">
                <select class="form-control" name="curso_fill">
                    <option value="">Seleccione Cursos...</option>
                    <?php
                    $queryEval = mysqli_query($connect_academia, "SELECT * FROM Cursos WHERE estado = 1 ORDER BY nombre ASC");
                    while ($dataEval = mysqli_fetch_array($queryEval)) {
                        echo '<option value="' . $dataEval["id"] . '"' . 
                            ($dataEval["id"] == $_POST["curso_fill"] ? ' selected' : '') . '>' . 
                            $dataEval["nombre"] . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-3" style="margin-bottom: 10px">
                <select class="form-control" name="evaluacion_fill">
                    <option value="">Seleccione Evaluación...</option>
                    <?php
                    $queryEval = mysqli_query($connect_academia, "SELECT * FROM Cursos_Contenidos WHERE tipo = 6 ORDER BY nombre ASC");
                    while ($dataEval = mysqli_fetch_array($queryEval)) {
                        echo '<option value="' . $dataEval["id"] . '"' . 
                            ($dataEval["id"] == $_POST["evaluacion_fill"] ? ' selected' : '') . '>' . 
                            $dataEval["nombre"] . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-3" style="margin-bottom: 10px">
                <select class="form-control" name="estudiante_fill">
                    <option value="">Seleccione Estudiante...</option>
                    <?php
                    $queryEval = mysqli_query($connect_academia, "SELECT DISTINCT id_estudiante FROM Estudiantes_Cursos");
                    while ($dataEval = mysqli_fetch_array($queryEval)) {
                        $queryCol = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '" . $dataEval["id_estudiante"] . "'");
                        $dataCol = mysqli_fetch_array($queryCol);

                        if ($queryCol->num_rows > 0) {
                            echo '<option value="' . $dataCol["id"] . '"' . 
                                ($dataCol["id"] == $_POST["estudiante_fill"] ? ' selected' : '') . '>' . 
                                $dataCol["nombre"] . ' </option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="col-md-3" style="margin-bottom: 10px">
                <button type="submit" class="btn btn-success btn-sm">Filtrar</button>
                
                <button type="button" class="btn btn-danger" onclick="exportarReporte()">
                    <i class="fas fa-download">Exportar a Excel</i>
                </button>
            </div>
        </div>
    </form>

    <form action="app/models/exportarExcel.php" method="post" target="_blank" id="FormularioExportacion">
        <input type="hidden" id="datos_a_enviar" name="datos_a_enviar" />
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($filtros)): ?>
        <!-- Mostrar la tabla solo si se han aplicado filtros -->
        <div class="table-responsive">
            <table class="table table-bordered" id="TablaExcel" border="1">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Cod.</th>
                        <th scope="col">Exámen</th>
                        <th scope="col">Nombre</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Inicia</th>
                        <th scope="col">Calificación</th>
                        <th scope="col">Detalle</th>
                    </tr>
                </thead>
                <tbody id="tabla_lista">
                    <?php
                    $count = 1;
                    $queryGrupo = mysqli_query($connect_academia, "
                        SELECT * 
                        FROM Estudiantes_Evaluaciones 
                        WHERE id >= 0 AND estado != 1 ".$filtros." 
                        GROUP BY id_estudiante
                    ");

                    while ($dataGrupo = mysqli_fetch_array($queryGrupo)) {

                        $queryEstudiante = mysqli_query($connect_valentina, "
                            SELECT * 
                            FROM Empleados 
                            WHERE id = '" . $dataGrupo["id_estudiante"] . "'
                        ");
                        $dataEstudiante = mysqli_fetch_array($queryEstudiante);

                        $bt_editar = '';

                        $query = mysqli_query($connect_academia, "
                            SELECT * 
                            FROM Estudiantes_Evaluaciones 
                            WHERE id_estudiante = '" . $dataGrupo["id_estudiante"] . "' 
                            AND promedio >= 0 AND estado != 1
                        ");
                        while ($data = mysqli_fetch_array($query)) {
                            $queryExamen = mysqli_query($connect_academia, "
                                SELECT * 
                                FROM Cursos_Contenidos 
                                WHERE id = '" . $data["id_contenido"] . "' 
                                AND estado = 1
                            ");
                            $dataExamen = mysqli_fetch_array($queryExamen);

                            $txt_estado = "n/a";
                            if ($data["estado"] == 0) {
                                $txt_estado = "Iniciada";
                            } elseif ($data["estado"] == 0 && $data["promedio"] > 0) {
                                $txt_estado = "No Aprobado";
                            } elseif ($data["estado"] == 2) {
                                $txt_estado = "Exámen Aprobado";
                            }

                            
                                $bt_editar = '<a href="' . $url_admin . '?pg=academia/admin/historico/detalle_evaluacion&id=' . $data["id"] . '" target="_blank">
                                                <button type="button" class="btn btn-success btn-sm" title="Calificaciones">
                                                    <i class="bx bx-edit-alt"></i>
                                                </button>
                                            </a>';
                           

                            echo '
                            <tr>
                                <th scope="row">' . $count . '</th>
                                <td>' . $dataExamen["id"] . '</td>
                                <td>' . $dataExamen["nombre"] . '</td>
                                <td>' . $dataEstudiante["nombre"] . ' </td>
                                <td>' . $dataEstudiante["correo"] . '</td>
                                <td>' . $txt_estado . '</td>
                                <td>' . $data["created_at"] . '</td>
                                <td>' . round($data["promedio"], 2) . '</td>
                                <td>' . $bt_editar . '</td>
                            </tr>';
                            $count++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<script>
    
    function exportarReporte(){
            $("#datos_a_enviar").val( $("<div>").append( $("#TablaExcel").eq(0).clone()).html());
            $("#FormularioExportacion").submit();
        }
    
    /*
    function exportarReporte() {
        var datos = [];
        var headers = [];

        $('#TablaExcel thead th').each(function (i, elem) {
            headers[i] = $(elem).text();
        });

        $('#TablaExcel tbody tr').each(function (i, elem) {
            var row = [];
            $(elem).find('td').each(function (j, td) {
                row[j] = $(td).text();
            });
            datos[i] = row;
        });

        $("#datos_a_enviar").val(JSON.stringify({ headers: headers, data: datos }));
        $("#FormularioExportacion").submit();
    }*/
</script>
