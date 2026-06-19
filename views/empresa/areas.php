<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_areas').addClass('active');
});
</script>

<?php
    include("app/models/estructura/Areas.php");
    $ClassAreas = new Areas();
    $areas = $ClassAreas->areas_lista( NULL );
?>

<div class="container">

    <div class="card">

        <div class="card-header">
            <table style="float:right">
                <tr>
                    <td align="right">
                        <?php if($VALIDAR_ROOT["crear"]){ ?>
                            <a href="<?php echo $url; ?>?pg=empresa/area/detalle">
                                <button type="button" class="btn btn-warning btn-sm">
                                    Nuevo
                                </button>
                            </a>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <h3>Áreas</h3>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table border="1" id="tabla" class="display table" style="width:100%;">
                    <thead class="thead-success">
                        <tr>
                            <th scope="col" width="15">#</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Líder</th>
                            <th scope="col">Colaboradores Asignados</th>
                            <th scope="col">Estado</th>
                            <th scope="col" width="100">Acciones</th>
                        </tr>
                    </thead>

                    <tbody id="tabla_lista">
                        <?php
                        $count = 1;

                        foreach($areas as $area){

                            $bt_editar = '';
                            if($VALIDAR_ROOT["editar"]){
                                $bt_editar = '
                                    <a href="' . $url . '?pg=empresa/area/detalle&id=' . $area["id"] . '">
                                        <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                    </a>
                                ';
                            }

                            echo '
                                <tr>
                                    <td>'.$count.'</td>
                                    <td>'.$area["nombre"].'</td>
                                    <td>'.$area["lideres"].'</td>
                                    <td>'.$area["colaboradores_asignados"].'</td>
                                    <td>'.$area["txt_estado"].'</td>
                                     <td>'.$bt_editar.'</td>
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