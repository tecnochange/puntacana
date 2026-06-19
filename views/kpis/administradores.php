<script>
    $(document).ready(function() {
        $('#menuKpis').collapse();
        $('#bt_administradores').addClass('active');
    });
</script>

<?php
include("app/models/kpis/Kpis.php");
$ClassKpis = new Kpis();
$Administradores = $ClassKpis->Kpis_Administradores($user_log["id_empresa"]);
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

<style>
    /* Margen debajo de la barra de herramientas (botones) */
    .dt-buttons {
        margin-bottom: 15px !important;
    }
</style>

<!-- CSS de DataTables + Botones -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Configuración Administradores KPI</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" style="text-align:end;">
            <a href="<?php echo $url; ?>?pg=kpis/detalle/crear_administrador">
                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Crear Administrador
                </button>
            </a>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table border="1" id="administradores" class="display table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Cargo</th>
                                        <th>Vicepresidencia</th>
                                        <th>Área</th>
                                        <th>Estado</th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php foreach ($Administradores as $administrador): ?>
                                        <tr>
                                            <td><?= $administrador["empleado"]["nombre"]; ?></td>
                                            <td><?= $administrador["empleado"]["nombre_cargo"]; ?></td>
                                            <td><?= $administrador["empleado"]["nombre_vicepresidencia"]; ?></td>
                                            <td><?= $administrador["empleado"]["nombre_area"]; ?></td>
                                            <td class="text-estado" data-id="<?= $administrador["id"]; ?>"><?= $administrador["estado"] == "1" ? "Activo" : "Inactivo"; ?></td>
                                            <td class="text-center d-flex justify-content-center">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input btn-cambiar-estado" type="checkbox" role="switch" data-id="<?= $administrador["id"]; ?>" <?= $administrador["estado"] == "1" ? "checked" : ""; ?> onclick="ActualizarEstado(this)" >
                                                </div>
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
</div>

<!-- JS de DataTables + Botones -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
    $(document).ready(function() {
        /* Tabla de administradores*/
        $('#administradores').DataTable({
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

        /*
        // Evento para el switch
        $(document).on('change', '.btn-cambiar-estado', function() {
            const checkbox = $(this);
            const adminId = checkbox.data('id');
            const nuevoEstado = checkbox.is(':checked') ? 1 : 0;
            const filaTexto = $(`.text-estado[data-id="${adminId}"]`);

            // Deshabilitar temporalmente para evitar múltiples clics
            checkbox.prop('disabled', true);

            $.ajax({
                url: 'api/kpis/administradores/actualizar_estado_admin.php', // Ajusta la ruta a tu controlador
                type: 'POST',
                data: {
                    id: adminId,
                    estado: nuevoEstado
                }
                success: function(response) {
                    if (response.success) {
                        console.log(response);
                        // Cambiar el texto de la columna Estado
                        filaTexto.text(nuevoEstado === 1 ? 'Activo' : 'Inactivo');
                        // Opcional: Notificación visual (puedes usar SweetAlert2)
                        console.log('Estado actualizado correctamente');
                    } else {
                        alert('Error al actualizar: ' + response.message);
                        checkbox.prop('checked', !checkbox.is(':checked')); // Revertir si falló
                    }
                },
                error: function() {
                    alert('Error de conexión con el servidor');
                    checkbox.prop('checked', !checkbox.is(':checked')); // Revertir
                },
                complete: function() {
                    checkbox.prop('disabled', false);
                }
            });
        });
        */
    });
</script>

<script>
    var api = '<?php echo $url; ?>api/kpis/';
    function ActualizarEstado(elemet){

        check = $(elemet).is(":checked");
        nuevoEstado = 0;
        if(check){ nuevoEstado = 1;}
        id = $(elemet).data('id');                                  

        var id_empresa = <?php echo $user_log["id_empresa"]; ?>

        jQuery.ajax({
            url: api + "administradores/actualizar_estado_admin.php",
            type: 'post',
            data: {
                id_empresa: id_empresa,
                id: id,
                estado: nuevoEstado
            },
            })
            .done(function(resp) {
                //$("#body_empleado").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {}
        );

        

        
    }
</script>