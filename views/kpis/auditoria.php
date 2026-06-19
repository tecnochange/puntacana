<script>
    $(document).ready(function() {
        $('#menuKpis').collapse();
        $('#bt_auditoria').addClass('active');
    });
</script>

<?php
include("app/models/kpis/Kpis.php");
$ClassKpis = new Kpis();
$Kpis_auditoria = $ClassKpis->Kpis_auditoria($user_log["id_empresa"]);
?>

<style>
    /* Margen debajo de la barra de herramientas (botones) */
    .dt-buttons {
        margin-bottom: 15px !important;
    }
</style>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">
    
    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Reporte Auditoría</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <div class="card">
                    <div class="card-body">
                        <table border="1" id="auditoria" class="display table" style="width:100%">
                            <thead>
                                <th scope="col" width="60">#</th>
                                <th scope="col">Realizado Por</th>
                                <th scope="col">Área Macro</th>
                                <th scope="col">Área Proceso</th>
                                <th scope="col">Acción</th>
                                <th scope="col">Descripción Acción</th>
                                <th scope="col">Indicador</th>
                                <th scope="col">Tipo</th>
                                <th scope="col">Fecha</th>
                            </thead>
                            <tbody>
                                <?php
                                $count = 0;
                                foreach ($Kpis_auditoria as $auditoria): ?>
                                    <?php
                                    $count++;
                                    $tipo = ''; //TIPO
                                    if($auditoria["tipo_kpi"] == 1){
                                       $tipo = 'Táctico';
                                    }
                                    if($auditoria["tipo_kpi"] == 2){
                                       $tipo = 'Estratégico';
                                    }
                                    
                                    ?>

                                    <tr>
                                        <td><?= $count; ?></td>
                                        <td><?= $auditoria["empleado"]["nombre"]; ?></td>
                                        <td><?= $auditoria["empleado"]["nombre_vicepresidencia"]; ?></td>
                                        <td><?= $auditoria["empleado"]["nombre_area"]; ?></td>
                                        <td style="color: <?= $auditoria["color_accion"]; ?> !important;"><?= $auditoria["accion"]; ?></td>
                                        <td><?= $auditoria["descripcion"]; ?></td>
                                        <td><?= $auditoria["indicador"]; ?></td>
                                        <td><?= $tipo; ?></td>
                                        <td><?= $auditoria["created_at"]; ?></td>
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
        $('#auditoria').DataTable({
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