<?php
include("views/administrar/etiquetas.php");
$_SESSION["id_cargo_edit"] = "";
$querySM44 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 4 AND id_submenu = 23");
$dataSM44 = mysqli_fetch_array($querySM44);

?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #FFFFFF !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-dice-d20" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM44["nombre"]; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <table width="100%">
                <tr>

                    <td align="right">
                        <?php if ($dtEmpleado["role"] == 1) { ?>
                            <a href="<?php echo $url; ?>?pg=administrar/cargos/detalle">
                                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Crear <?php echo $etiquetaAdminCargo; ?>
                                </button>
                            </a>

                            <button type="button" id="sidebarCollapse" class="btn btn-warning btn-sm" style="display: none">
                                <i class="fas fa-plus"></i> <?php echo $IDIOMA["estructura_cargos_cargar_csv"]; ?>
                            </button>
                        <?php } ?>

                        <button type="button" id="sidebarCollapse" class="btn btn-info btn-sm" title="Descargar Excel" style="display: none">
                            <i class="fas fa-download"></i>
                        </button>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">


            <ul class="nav nav-tabs" style="display: none">
                <li class="nav-item">
                    <a href="<?php echo $url; ?>?pg=administrar/cargos" class="nav-link active"><?php echo $IDIOMA["estructura_cargos_inventario_cargos"]; ?></a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $url; ?>?pg=administrar/cargos_estructura" class="nav-link"><?php echo $IDIOMA["estructura_cargos_estructura_cargos"]; ?></a>
                </li>
            </ul>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <div class="card">
                    <div class="card-body">

                        <table border="1" id="cargos" class="display table" style="width:100%;">
                            <thead class="thead-success">
                                <tr>
                                    <th scope="col" width="15">#</th>
                                    <th scope="col" width="150"><?php echo $etiquetaAdminCargo; ?></th>
                                    <th scope="col" width="150">Nivel</th>
                                    <th scope="col" width="150"><?php echo $etiquetaAdminArea; ?></th>
                                    <th scope="col" width="150">Colaboradores Asignados</th>
                                    <th scope="col" width="150">Estado</th>
                                    <th scope="col" width="120">Acciones</th>
                                </tr>
                            </thead>

                            <tbody id="tabla_lista">
                                <?php
                                $count = 1;
                                $query = mysqli_query($connect_valentina, "SELECT * FROM Cargos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' ORDER BY nombre ASC ");
                                while ($data = mysqli_fetch_array($query)) {

                                    $queryArea = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = '" . $data["id_area"] . "' ");
                                    $dataArea = mysqli_fetch_array($queryArea);

                                    $queryReporte = mysqli_query($connect_valentina, "SELECT * FROM Cargos WHERE id = '" . $data["padre"] . "' ");
                                    $dataReporte = mysqli_fetch_array($queryReporte);

                                    $queryDireccion = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = '" . $data["id_direccion"] . "' ");
                                    $dataDireccion = mysqli_fetch_array($queryDireccion);

                                    $queryCargos = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id_cargo = '" . $data["id"] . "' ");

                                    $txt_nivel = '';
                                    foreach ($Array_Nivel_Cargo as $nivel) {
                                        if ($nivel[0] == $data["nivel_cargo"]) {
                                            $txt_nivel = $nivel[1];
                                        }
                                    }

                                    $txt_estado = '';
                                    foreach ($Array_Estado as $nivel) {
                                        if ($nivel[0] == $data["estado"]) {
                                            $txt_estado = $nivel[1];
                                        }
                                    }

                                    if(mysqli_num_rows($queryCargos) == 0){
                                        // echo "DELETE Cargos WHERE id = '".$data["id"]."' ";
                                        //mysqli_query($connect_valentina, "DELETE FROM Cargos WHERE id = '".$data["id"]."' ");
                                    }

                                    $bt_editar = '';
                                    if ($dtEmpleado["role"] == 1) {
                                        $bt_editar = '
                                <a href="' . $url . '?pg=administrar/cargos/detalle&id=' . $data["id"] . '">
                                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                </a>
                            ';
                                    }
                                // <button type="button" id="sidebarCollapse" class="btn btn-danger btn-sm" title="Quitar cargo" style="font-size: 12px; padding: 3px 5px;" onclick="Eliminar_Cargo(' . $data["id"] . ')">
                                //     <i class="bx bx-trash"></i>
                                // </button>
                                    /*
						<a href="'.$url.'?pg=administrar/cargos/descriptivos&id='.$data["id"].'">
                                <button type="button" id="sidebarCollapse" class="btn btn-info btn-sm" title="Descriptivo">
                                    <i class="fas fa-plus"></i>
                                </button>
                                </a>

                                <a href="'.$url.'?pg=administrar/cargos/onboarding&id='.$data["id"].'">
                                <button type="button" id="sidebarCollapse" class="btn btn-primary btn-sm" title="Onboarding">
                                    <i class="fas fa-users"></i>
                                </button>
                                </a>
						*/


                                    echo '
                        <tr>
                            <td>' . $count . '</td>
                            <td>' . $data["nombre"] . '</td>
                            <td>' . $data["nivel_jerarquico"] . '</td>
                            <td>' . $dataArea["nombre"] . '</td>
                            <td>' . mysqli_num_rows($queryCargos) . '</td>
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
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#cargos').DataTable({
            autoWidth: false,
            responsive: true,
            pageLength: 50,
            language: {
                processing: "Procesando...",
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros.",
                info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                infoEmpty: "Mostrando registros del 0 al 0 de 0 registros",
                infoFiltered: "(filtrado de un total de _MAX_ registros)",
                infoPostFix: "",
                loadingRecords: "Cargando...",
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                row: "Registro",
                export: "Exportar",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Ultimo"
                },
                aria: {
                    sortAscending: ": Activar para ordenar la columna de manera ascendente",
                    sortDescending: ": Activar para ordenar la columna de manera descendente"
                },
                select: {
                    row: "registro",
                    selected: "seleccionado"
                }
            },
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'collection',
                    text: 'Exportar',
                    buttons: [
                        'copy',
                        'excel',
                        'csv',
                        {
                            extend: 'pdfHtml5',
                            text: 'PDF',
                            orientation: 'landscape',
                            pageSize: 'LEGAL'
                        },
                        {
                            extend: 'print',
                            customize: function(win) {
                                $(win.document.body)
                                    .css('font-size', '10pt');

                                $(win.document.body).find('table')
                                    .addClass('compact')
                                    .css('font-size', 'inherit');
                            }
                        }
                    ]
                }

            ]
        });
    });
    var api = '<?php echo $url; ?>/api/administrar/';
    var activar = false;

    function Eliminar_Cargo(id) {

        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar un <?php echo $etiquetaAdminCargo; ?>, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Eliminar_Cargo(' + id + ')"> Confirmar </button>');

        } else {

            jQuery.ajax({
                    url: api + "eliminar_cargo.php",
                    type: 'post',
                    data: {
                        id: id,
                        id_empresa: <?php echo $_SESSION["id_empresa"]; ?>,
                        id_user: <?php echo $_SESSION["id_user"]; ?>,
                        url: "?pg=administrar/cargos"
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
<script>
    $(".menu_section").addClass("active");
    // $("#nav_empresa").addClass("active");
    jQuery("#menu_empresa").css("display", "none");
    $("#bt_admin_cargos").addClass("current-page");
</script>