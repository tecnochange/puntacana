<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_lideres').addClass('active');
});
</script>

<?php

include("app/models/estructura/Lideres.php");
$ClassLideres = new Lideres();

$array_lideres = $ClassLideres->lideres_lista($_POST);

?>


<div class="container">

    <div class="card">
        
        <div class="card-header">
            <h3>Líderes</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                        <table border="1" id="lideres" class="display table" style="width:100%;">
                            <thead class="">
                                <tr>
                                    <th scope="col" colspan="5">Colaborador</th>
                                    <th scope="col" colspan="5">Jefes Asignados</th>
                                </tr>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Documento</th>
                                    <th scope="col">Nombre y Apellidos</th>
                                    <th scope="col">Cargo</th>
                                    <th scope="col">Área</th>
                                    <th scope="col">Documento</th>
                                    <th scope="col">Nombre y Apellidos</th>
                                    <th scope="col">Cargo</th>
                                    <th scope="col">Áreas</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $hoy = date("Y-m-d H:i:s");
                                $count = 1;


                                foreach($array_lideres as $lider){
                                    echo'
                                            <tr>
                                                <td>' . $count . '</td>
                                                <td>' . $lider["documento"] . '</td>
                                                <td>' . $lider["nombre"] . '</td>
                                                <td>' . $lider["cargo"] . '</td>
                                                <td>' . $lider["area"] . '</td>
                                                <td>' . $lider["documento_lider"] . '</td>
                                                <td>' . $lider["nombre_lider"] . '</td>
                                                <td>' . $lider["cargo_lider"] . '</td>
                                                <td>' . $lider["area_lider"] . '</td>
                                                <td> </td>
                                                
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
        $('#lideres').DataTable();
    });
</script>

<script>
    $(document).ready(function() {
        $("#buscador").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".tabla_lista tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    var api = '<?php echo $url; ?>/api/administrar/';

    var activar = false;

    function Elimimar_Jefe(id) {

        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar un jefe, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Elimimar_Jefe(' + id + ')"> Confirmar </button>');

        } else {

            jQuery.ajax({
                    url: api + "eliminar_lideres.php",
                    type: 'post',
                    data: {
                        id: id,
                        url: "?pg=estructura/lideres"
                    },
                }).done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {});

        }
    }
</script>