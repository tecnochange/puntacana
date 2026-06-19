<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_cargos').addClass('active');
});
</script>

<?php
include("app/models/estructura/Cargos.php");
$ClassCargos = new Cargos();

$array_cargos = $ClassCargos->cargos_lista($_POST); 
?>


<div class="container">

    <div class="card">
        
        <div class="card-header">
            <table style="float:right">
                <tr>
                    <td align="right">
                        <?php if($VALIDAR_ROOT["crear"]){ ?>
                            <a href="<?php echo $url; ?>?pg=empresa/cargos/detalle">
                                <button type="button" class="btn btn-warning btn-sm">
                                    Nuevo
                                </button>
                            </a>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <h3>Cargos</h3>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                        
                <table border="1" id="cargos" class="display table" style="width:100%;">
                    <thead class="thead-success">
                                <tr>
                                    <th scope="col" width="15">#</th>
                                    <th scope="col" width="150">Cargo</th>
                                    <th scope="col" width="150">Nivel</th>
                                    <th scope="col" width="150">Área</th>
                                    <th scope="col" width="150">Colaboradores Asignados</th>
                                    <th scope="col" width="150">Estado</th>
                                    <th scope="col" width="120">Acciones</th>
                                </tr>
                            </thead>

                            <tbody id="tabla_lista">
                                <?php
                                $count = 1;
                                foreach($array_cargos as $cargo){

                                    $bt_editar = '';
                                    if($VALIDAR_ROOT["editar"]){
                                        $bt_editar = '
                                            <a href="' . $url . '?pg=empresa/cargos/detalle&id=' . $cargo["id"] . '">
                                            <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
                                                <i class="bx bx-edit"></i>
                                            </button>
                                            </a>
                                        ';
                                    }

                                    echo '
                                    <tr>
                                        <td>' . $count . '</td>
                                        <td>' . $cargo["cargo"] . '</td>
                                        <td>' . $cargo["nivel"] . '</td>
                                        <td>' . $cargo["area"] . '</td>
                                        <td>' . $cargo["asignados"] . '</td>
                                        <td>' . $cargo["estado"] . '</td>
                                        <td>
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
        $('#cargos').DataTable();
    });
</script>
