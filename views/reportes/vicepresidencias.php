<script>
    $(document).ready(function() {
        $('#menuReportes').collapse();
        $('#bt_reportes_vicepresidencia').addClass('active');
    });
</script>

<?php
include("app/models/okrs/OkrsServicios.php");
$ClassOkrServicios = new OkrsServicios();
$vicepresidencias_listas = $ClassOkrServicios->reporte_lista_vicepresidencias($user_log["id_empresa"]);
?>

<style>
    /* Margen debajo de la barra de herramientas (botones) */
	.dt-buttons {
		margin-bottom: 15px !important;
	}
	/* Margen debajo de la tabla (paginación) */
	.dataTables_paginate, .dataTables_info{
		margin-top: 15px !important;
	}
</style>

<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">
    <div class="card mb-3">
        <div class="card-body">
            <h3>CONSOLIDADO PRIMER NIVEL ORGANIZACIONAL (ALTA DIRECCIÓN) AÑO <?php echo $_SESSION["anio_fill"]; ?></h3>
        </div>
    </div>

    <?php include("views/reportes/layouts/filtros.php"); ?>

    <div class="card">
        <div class="card-body">

            <table id="vicepresidencias_table" class="table">

                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Líder</th>
                        <th>OKRs Asignados</th>
                        <th>Progreso</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($vicepresidencias_listas as $vicepresidencia): ?>
                        <tr>
                            <td><?php echo $vicepresidencia["nombre"]; ?></td>
                            <td>
                                <?php
                                $min_lider = '';
                                foreach ($vicepresidencia["array_lideres"] as $lider) {
                                    if ($lider["id"]) {
                                        $foto = isset($lider["foto"]) && !empty($lider["foto"]) ? $lider["foto"] : 'img_default.jpg';
                                        $min_lider .= '
                                                <img src="https://goforagile.com/recursos/' . $foto . '"
                                                    width="32" height="32"
                                                    class="foto_miniaturas mb-1"
                                                    title="' . $lider["nombre"] . '"
                                                    style="cursor:pointer;" 
                                                    onclick="FichaEmpleado(' . $lider["id"] . ')">
                                                ';
                                    }
                                }

                                if ($min_lider == '') {
                                    $min_lider = 'Sin asignar';
                                }

                                echo $min_lider;
                                ?>
                            </td>
                            <td></td>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="?pg=reportes/vicepresidencia/detalle&id=<?= $vicepresidencia["id"]; ?>" type="button" class="btn btn-success btn-sm" title="Vista rápida de los OKRs">
                                    <i class="bx bx-show"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

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
    $(document).ready(function() {
        $('#vicepresidencias_table').DataTable({
            pageLength: 25,
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
</script>

<script>
    var api = '<?php echo $url; ?>api/kpis/';
    function FichaEmpleado(id_empleado){

        var id_empresa = <?php echo $user_log["id_empresa"]; ?>

        $("#modal_empleado").modal("show");
        $("#body_empleado").html("Cargando...");

        jQuery.ajax({
            url: api + "ficha_empleado.php",
            type: 'post',
            data: {
                id_empresa: id_empresa,
                id_empleado: id_empleado,
            },
            })
            .done(function(resp) {
                $("#body_empleado").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {}
        );
    }
</script>