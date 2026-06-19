<script>
$(document).ready(function() {
    $('#menuEstrategia').collapse();
    $('#bt_estrategia_balanced').addClass('active');
});
</script>


<?php 
include("views/okrs/layouts/modal_okr.php"); 
include("views/administrar/etiquetas.php");
$querySM22 = mysqli_query($connect_admin, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 2 AND id_submenu = 11");
$dataSM22 = mysqli_fetch_array($querySM22);
?>



<div class="container">

    <div class="card">

        <div class="card-header">
            <?php if($VALIDAR_ROOT["crear"]){ ?>
             <table style="float:right">
                <tr>
                    <td align="right">

                        <a href="<?php echo $url; ?>?pg=estrategica/dimension/detalle">
                            <button type="button" class="btn btn-warning btn-sm">
                                Nuevo
                            </button>
                        </a>
                    </td>
                </tr>
            </table>
            <?php } ?>
            <h3>Balanced Scorecard (BSC)</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                        <table border="1" id="dimensiones" class="display table" style="width:100%;">
                            <thead>
                                <tr>
                                    <th scope="col" width="15">#</th>
                                    <th scope="col">Dimensión</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col" width="90">Acciones</th>
                                </tr>
                            </thead>



                            <tbody id="tabla_lista">

                                <?php

                                $count = 1;
                                $query = mysqli_query($connect_okrs, "SELECT * FROM Dimensiones WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' 
					            ORDER BY nombre ASC  ");
                                while ($data = mysqli_fetch_array($query)) {

                                    $txt_estado = '';
                                    foreach ($Array_Estado  as $estado) {
                                        if ($estado[0] == $data["estado"]) {
                                            $txt_estado = $estado[1];
                                        }
                                    }

                                    $bt_editar = '';
                                    if($VALIDAR_ROOT["editar"]){
                                        $bt_editar = '
                                            <a href="' . $url . '?pg=estrategica/dimension/detalle&id=' . $data["id"] . '">
                                                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                            </a>
                                        ';
                                    }



                                    echo '
                                    <tr>
                                        <td>' . $count . '</td>
                                        <td>' . $data["nombre"] . '</td>
                                        <td>' . $txt_estado . '</td>
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




<script type="text/javascript">
    $(document).ready(function() {

        $('#tabla_general').DataTable(
            {
                pageLength: 50
            }
        );
    });

    var api = '<?php echo $url; ?>api/okrs/';
    var activar = false;

    function EliminarDimension(id, id_empresa, id_empleado) {
        if (activar == false) {
            $("#modal_general").modal("show");
			$("#modal_body").html('Está a punto de eliminar esta dimension. ESTA ACCIÓN ES IRREVERSIBLE. se perderán los datos, los planes de acción, comentarios y documentos asociados a la iniciativa. ¿está seguro?<br><br>');
			$("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; EliminarDimension(' + id + ',' + id_empresa + ',' + id_empleado + ')"> Eliminar </button>');

			$("#modal_okr").modal("hide");
        } else {
            jQuery.ajax({
                    url: api + "eliminar_dimension.php",
                    type: 'post',
                    data: {
                        id: id,
                        id_empresa: id_empresa,
                        id_empleado: id_empleado,
                        url: "?pg=estrategica/dimensiones"
                    },
                }).done(function(resp) {
                    window.location.reload();
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {

                });
        }
    }
</script>

</div>
</div>