<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_estructura').addClass('active');
});
</script>

<?php
    include("app/models/estructura/EstructuraEmpresa.php");
    $ClassEstructuraEmpresa = new EstructuraEmpresa();
    $estructura_lista = $ClassEstructuraEmpresa->estructura_lista( NULL );
?>

<div class="container">

    <div class="card">

        <div class="card-header">
            <table style="float:right">
                <tr>
                    <td align="right">
                        <?php if($VALIDAR_ROOT["crear"]){ ?>
                            <a href="<?php echo $url; ?>?pg=empresa/estructura/detalle">
                                <button type="button" class="btn btn-warning btn-sm">
                                    Nuevo
                                </button>
                            </a>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <h3>Estructura Organizacional</h3>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                    <table border="1" id="tabla" class="display table" style="width:100%;">
                        <thead class="thead-success">
                            <tr>
                                <th>#</th>
                                <th scope="col">Empresa</th>
                                <th scope="col">Vicepresidencia</th>
                                <th scope="col">Área</th>
                                <th scope="col">Unidad Organizativa</th>
                                <th scope="col">Nivel Jerárquico</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>

                            <tbody id="tabla_lista">
                                <?php
                                $count = 1;
                                foreach($estructura_lista as $estructura){

                                    $bt_editar = '';
                                    if($VALIDAR_ROOT["editar"]){
                                        $bt_editar = '
                                            <a href="' . $url . '?pg=empresa/estructura/detalle&id=' . $estructura["id"] . '">
                                            <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
                                                <i class="bx bx-edit"></i>
                                            </button>
                                            </a>
                                        ';
                                    }

                                    $txt_estado = '';
                                    foreach ($Array_Estado as $nivel) {
                                        if ($nivel[0] == $estructura["estado"]) {
                                            $txt_estado = $nivel[1];
                                        }
                                    }

                                    echo '
                                    <tr>
                                        <td>' . $count . '</td>
                                        <td>' . $estructura["compania"] . '</td>
                                        <td>' . $estructura["nombre_vicepresidencia"] . '</td>
                                        <td>' . $estructura["nombre_area"] . '</td>
                                        <td>' . $estructura["unidad_organizativa"] . '</td>
                                        <td>' . $estructura["nombre_nivel"] . '</td>
                                        <td>' . $txt_estado . '</td>
                                        <td>' . $bt_editar . '</td>
                                    </tr>
                                    ';
                                    $count++;

                                }


                                $sentencia = " 
                                SELECT
                                    Estructura_Empresa.id, 
                                    Estructura_Empresa.compania, 
                                    Estructura_Empresa.unidad_organizativa,   
                                    Estructura_Empresa.estado,   
                                    Vicepresidencia.nombre AS nombre_vicepresidencia,
                                    Areas.nombre AS nombre_area, 
                                    Nivel_Jerarquico.nombre AS nombre_nivel 
                                FROM
                                    Estructura_Empresa 
                                    LEFT JOIN Vicepresidencia ON Vicepresidencia.id = Estructura_Empresa.vicepresidencia 
                                    LEFT JOIN Areas ON Areas.id = Estructura_Empresa.area 
                                    LEFT JOIN Nivel_Jerarquico ON Nivel_Jerarquico.id = Estructura_Empresa.nivel_jerarquico 
                                WHERE
                                    Estructura_Empresa.id_empresa = '".$_SESSION['id_empresa']."' 
                                ORDER BY Vicepresidencia.nombre ASC;
                                ";
                                $query = mysqli_query($connect_admin, $sentencia);
                                while ($data = mysqli_fetch_array($query)) {

                                    

                                    

                                    
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

<script>

    var api = '<?php echo $url; ?>/api/administrar/';
    var activar = false;

    function Eliminar_Estructura(id) {

        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar una estructura de la empresa, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Eliminar_Estructura(' + id + ')"> Confirmar </button>');

        } else {

            jQuery.ajax({
                    url: api + "eliminar_estructura.php",
                    type: 'post',
                    data: {
                        id: id,
                        id_empresa: <?php echo $_SESSION["id_empresa"]; ?>,
                        id_user: <?php echo $_SESSION["id_user"]; ?>,
                        url: "?pg=administrar/estructura_empresa"
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

