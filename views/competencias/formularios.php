<script>
    $(document).ready(function() {
        $('#menuCompetencias').collapse();
        $('#bt_competencias_reportes').addClass('active');
    });
</script>

<?php
include("app/models/competencias/Competencias.php");
$ClassCompetencias = new Competencias();
$dataCicloVal = $ClassCompetencias->Ciclo($user_log["id_empresa"], $_SESSION["anio_ciclo"]);

include("app/models/estructura/Colaboradores.php");
$ClassColaboradores = new Colaboradores();
$ARRAY_COLABORADORES = $ClassColaboradores->colaboradores_lista($_POST, $connect_admin);

//EVALUADORES
$ARRAY_EVALUADORES = [];
$sentencia_evaluadores =  "
SELECT * FROM Evaluadores 
WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ORDER BY tipo ASC ";
$queryEvaluadores = mysqli_query($connect_valoracion, $sentencia_evaluadores);
while ($dataEvaluadores = mysqli_fetch_array($queryEvaluadores)) {
    array_push($ARRAY_EVALUADORES, $dataEvaluadores);
}

//EVALUACIONES
$ARRAY_EVALUACIONES = [];
$sentencia_evaluaciones =  "
SELECT id, id_evaluado, id_evaluador, estado FROM Competencias_Evaluaciones_New 
WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ";
$queryEvaluaciones = mysqli_query($connect_valoracion, $sentencia_evaluaciones);
while ($dataEvaluaciones = mysqli_fetch_array($queryEvaluaciones)) {
    array_push($ARRAY_EVALUACIONES, $dataEvaluaciones);
}
?>

<style>
    .card,
    .card-body,
    .card-footer {
        background-color: #FFFFFF !important;
    }

    /* Margen debajo de la barra de herramientas (botones) */
    .dt-buttons {
        margin-bottom: 15px !important;
    }

    /* Margen debajo de la tabla (paginación) */
    .dataTables_paginate,
    .dataTables_info {
        margin-top: 15px !important;
    }
</style>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Seguimiento Evaluadores <?= $dataCicloVal["anio"]; ?> | <small>Ciclo: <?php echo $dataCicloVal["nombre"]; ?></small> </h3>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <ul class="nav nav-pills justify-content-center">
                <li class="nav-item">
                    <a class="nav-link " href="?pg=competencias/seguimiento">Seguimiento Valoración</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" href="?pg=competencias/formularios">Formularios Valoración</a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="formularios" class="display table" style="width:100%;">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col" width="15">#</th>
                            <th scope="col">Nombre Evaluador</th>
                            <th scope="col">Cargo</th>
                            <th scope="col">Persona a la que realiza la valoración</th>
                            <th scope="col">Cargo</th>
                            <th scope="col">Tipo</th>
                            <th scope="col">Estado</th>
                            <th scope="col" class="text-center" style="min-width: 180px !important;">Acciones</th>
                        </tr>
                    </thead>

                    <tbody class="tabla_lista">
                        <?php
                        $count = 0;
                        foreach ($ARRAY_COLABORADORES as $colaborador) {

                            $filas_evaluadores = '';
                            foreach ($ARRAY_EVALUADORES  as $evaluador) {
                                if ($evaluador["id_evaluador"] == $colaborador["id"]) {

                                    $dataEvaluado = $ARRAY_COLABORADORES[$evaluador["id_empleado"]];

                                    $tipo_txt = '';
                                    foreach ($array_Tipo_Colaborador as $tipo) {
                                        if ($tipo[0] == $evaluador["tipo"]) {
                                            $tipo_txt =  $tipo[1];
                                        }
                                    }

                                    $dataEvaluacion = [];
                                    //VALIDAMOS SI TIENE EVALUACIONES
                                    foreach ($ARRAY_EVALUACIONES as $evaluacion) {
                                        if ($evaluacion["id_evaluado"] == $evaluador["id_empleado"] && $evaluacion["id_evaluador"] == $evaluador["id_evaluador"]) {
                                            $dataEvaluacion = $evaluacion;
                                            break;
                                        }
                                    }

                                    $botones_generales = 'Sin Evaluación';
                                    if (count($dataEvaluacion) > 0) {
                                        $botones_generales = '
                                            <a href="' . $url . '?pg=competencias/evaluacion&id=' . $dataEvaluacion["id"] . '">
                                                <button type="button" class="btn btn-success btn-sm" title="Detalle">
                                                    <i class="bx bx-pencil" title="Editar"></i> 
                                                </button>
                                            </a>

                                            <button
                                                type="button"
                                                class="btn btn-info btn-sm btn-reactivar"
                                                title="Reactivar Evaluación"
                                                data-id="' . $dataEvaluacion["id"] . '"
                                            >
                                                <i class="bx bx-refresh"></i>
                                            </button>

                                            <button
                                                type="button"
                                                class="btn btn-danger btn-sm btn-eliminar"
                                                title="Eliminar Evaluación"
                                                data-id="' . $dataEvaluacion["id"] . '"
                                                data-id-evaluado="' . $dataEvaluacion["id_evaluado"] . '"
                                                data-id-evaluador="' . $dataEvaluacion["id_evaluador"] . '"
                                                data-tipo="' . $dataEvaluacion["tipo"] . '"
                                            >
                                                <i class="bx bx-trash"></i>
                                            </button>

                                        ';
                                    }
                                    $txt_estado = "Sin iniciar";
                                    if ($dataEvaluacion["estado"] == 1) {
                                        $txt_estado = 'En Proceso ';
                                    }

                                    if ($dataEvaluacion["estado"] >= 2) {
                                        $txt_estado = 'Terminada';
                                    }

                                    $count++;
                                    $filas_evaluadores .= '
                                        <tr>   
                                            <td>' . $count . '</td>
                                            <td>' . $colaborador["nombre"] . ' </td>
                                            <td>' . $colaborador["nombre_cargo"] . '</td>
                                            <td>' . $dataEvaluado["nombre"] . ' </td>
                                            <td>' . $dataEvaluado["nombre_cargo"] . '</td>
                                            <td>' . $tipo_txt . '</td>
                                            <td>' . $txt_estado . '</td>
                                            <td class="text-center">' . $botones_generales . '</td>
                                        </tr>
                                    ';
                                }
                            }
                            echo $filas_evaluadores;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- CSS de DataTables + Botones -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- JS de DataTables + Botones -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
    //CARGA DE LA TABLA
    $(document).ready(function() {
        $('#formularios').DataTable({
            pageLength: 10,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
            },
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: 'Descargar Excel'
            }]
        });
    });

    //ELIMINAR EVALUACION
    $(document).on('click', '.btn-eliminar', function() {

        const api = "api/competencias/";
        const btn = $(this);
        const fila = btn.closest('tr');

        const id = btn.data('id');
        const idEvaluado = btn.data('id-evaluado');
        const idEvaluador = btn.data('id-evaluador');
        const tipo = btn.data('tipo');

        if (!confirm('¿Seguro que deseas eliminar esta evaluación? Esta acción es irreversible.')) {
            return;
        }

        $.ajax({
            url: api + 'eliminar_formulario.php',
            type: 'POST',
            data: {
                id: id,
                id_evaluado: idEvaluado,
                id_evaluador: idEvaluador,
                id_tipo: tipo
            },
            success: function(resp) {

                alert('Eliminado correctamente');

                // Eliminar fila visualmente
                $('#formularios').DataTable()
                    .row(fila)
                    .remove()
                    .draw(false);
            },
            error: function(err) {
                toastr.error('Error al eliminar');
                console.error(err);
            }
        });

    });

    //REACTIVAR EVALUACION
    $(document).on('click', '.btn-reactivar', function() {

        const api = "api/competencias/";

        const btn = $(this);
        const fila = btn.closest('tr');
        const id = btn.data('id');

        if (!confirm('¿Seguro que deseas reactivar esta evaluación? Esta acción es irreversible.')) {
            return;
        }

        $.ajax({
            url: api + 'reactivar_formulario.php',
            type: 'POST',
            data: {
                id: id
            },
            success: function(resp) {

                alert('Reactivado correctamente');

                // Eliminar fila visualmente
                // Opción 1: recargar tabla completa (seguro)
                //$('#formularios').DataTable().ajax.reload(null, false);

                // Opción 2 (si la fila cambia de estado):
                // $('#formularios').DataTable().row(fila).remove().draw(false);
            },
            error: function(err) {
                toastr.error('Error al reactivar');
                console.error(err);
            }
        });

    });
</script>