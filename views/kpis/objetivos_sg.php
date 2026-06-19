<script>
    $(document).ready(function() {
        $('#menuKpis').collapse();
        $('#bt_objetivos_sg').addClass('active');
    });
</script>

<?php
include("app/models/kpis/Kpis.php");
$ClassKpis = new Kpis();
$Kpis_objetivos_sg = $ClassKpis->Kpis_Objetivos_SG($user_log["id_empresa"]);
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<style>
    .card,
    .card-header,
    .card-body {
        background-color: white !important;
    }

    /* Margen debajo de la barra de herramientas (botones) */
    .dt-buttons {
        margin-bottom: 15px !important;
    }

    /* Evitar salto de línea en los encabezados */
    #objetivos_sg th {
        white-space: nowrap;
    }
</style>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Objetivos SG</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" style="text-align: end;">
            <a href="<?php echo $url; ?>?pg=kpis/objetivos_sg/detalle">
                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Crear Objetivo SG<?php echo $etiquetaKpiOS; ?>
                </button>
            </a>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="objetivos_sg" class="display table" style="width:100%">
                            <thead>
                                <th>Año</th>
                                <th>Área Macro</th>
                                <th>Área Proceso</th>
                                <th>Objetivo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </thead>

                            <tbody>
                                <?php foreach ($Kpis_objetivos_sg as $objetivo): ?>
                                    <tr>
                                        <td><?= $objetivo["anio"]; ?></td>
                                        <td><?= $objetivo["nombre_vicepresidencia"]; ?></td>
                                        <td><?= $objetivo["nombre_area"]; ?></td>
                                        <td><?= $objetivo["objetivo"]; ?></td>
                                        <td><?= $objetivo["estado"] == "1" ? "Activo" : "Inactivo"; ?></td>
                                        <td class="text-center">
                                            <a href="?pg=kpis/objetivos_sg/detalle&id=<?= $objetivo["id"]; ?>" type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
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
    $(document).ready(function() {
        $('#objetivos_sg').DataTable({
            pageLength: 50,
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