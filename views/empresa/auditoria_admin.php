<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_auditoria').addClass('active');
});
</script>

<?php
    include("app/models/estructura/Auditoria.php");
    $ClassAuditoria = new Auditoria();
    $auditorias = $ClassAuditoria->auditoria_lista( NULL );
?>

<div class="container">

    <div class="card mb-3">
        <div class="card-header">
            <h3>Auditoría Empresarial</h3>
        </div>
        <div class="card-body">
            
            <div class="table-responsive">
            <table border="1" id="tabla" class="display table" style="width:100%">
                <thead>
                    <th scope="col" width="60">#</th>
                    <th scope="col">Realizado Por</th>
                    <th scope="col">Acción</th>
                    <th scope="col">Descripción de la Acción</th>
                    <th scope="col">Módulo</th>                                
                    <th scope="col">Fecha</th>
                </thead>
                <tbody>
                    
                <?php
                    $count = 1;

                    foreach($auditorias as $auditoria){
                        echo '
                        <tr>
                            <td>'.$count.'</td>
                            <td>'.$auditoria["nombre_empleado"].'</td>
                            <td>'.$auditoria["accion"].'</td>
                            <td>'.$auditoria["descripcion"].'</td>
                            <td>'.$auditoria["modulo"].'</td>
                            <td>'.$auditoria["created_at"].'</td>
                        </tr>
                        ';
                        $count++;
                    }
                ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>

</div>


















<script>
    $(document).ready(function() {
        $('#tabla').DataTable();
    });
</script>