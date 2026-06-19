<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_vicepresidencias').addClass('active');
});
</script>

<?php
    include("app/models/estructura/Vicepresidencias.php");
    $ClassVicepresidencias = new Vicepresidencias();
    $vicepresidencias = $ClassVicepresidencias->vicepresidencias_lista( NULL );
?>

<div class="container">

    <div class="card">

        <div class="card-header">
            <table style="float:right">
                <tr>
                    <td align="right">
                        <?php if($VALIDAR_ROOT["crear"]){ ?>
                            <a href="<?php echo $url; ?>?pg=empresa/vicepresidencia/detalle">
                                <button type="button" class="btn btn-warning btn-sm">
                                    Nuevo 
                                </button>
                            </a>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <h3>Vicepresidencias</h3>
        </div>

        <div class="card-body">

            <div class="table-responsive">         

                <table border="1" id="tabla" class="display table" style="width:100%;">
                    <thead class="thead-success">
                        <tr>
                            <th scope="col" width="15">#</th>
                            <th scope="col" width="150">Nombre</th>
                            <th scope="col" width="150">Lider</th>
                            <th scope="col" width="150">Estado</th>
                            <th scope="col" width="120" style="text-align: end;">Acciones</th>
                        </tr>
                    </thead>

                    <tbody id="tabla_lista">
                    <?php
                        $count = 1;

                        foreach($vicepresidencias as $vicepresidencia){

                            $bt_editar = '';

                            if($VALIDAR_ROOT["editar"]){
                                $bt_editar = '
                                    <a href="' . $url . '?pg=empresa/vicepresidencia/detalle&id=' . $vicepresidencia["id"] . '">
                                    <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
                                        <i class="bx bx-edit"></i>
                                    </button>
                                    </a>
                                ';
                            }

                            echo '
                            <tr>
                                <td>' . $count . '</td>
                                <td>' . $vicepresidencia["nombre"] . '</td>
                                <td>' . $vicepresidencia["lideres"] . '</td>
                                <td>'.$vicepresidencia["txt_estado"].'</td>                           
                                <td style="text-align: end;">
                                    ' . $bt_editar . '
                                </td>
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
        $('#tabla').DataTable({
            language: {
                search: "Buscar", 
                lengthMenu: "Mostrar _MENU_ registros.",
            }
        });
    }); 
</script>
