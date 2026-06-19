<?php
include("views/administrar/etiquetas.php");
$_SESSION["id_cargo_edit"] = "";
$querySM47 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 4 AND id_submenu = 26");
$dataSM47 = mysqli_fetch_array($querySM47);
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #FFFFFF !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-dice-d20" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM47["nombre"]; ?></h4>
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
                            <a href="<?php echo $url; ?>?pg=administrar/estructura/detalle">
                                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Crear Estructura
                                </button>
                            </a>
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <div class="card">
                    <div class="card-body">
                        <table border="1" id="estructura" class="display table" style="width:100%;">
                            <thead class="thead-success">
                                <tr>
                                    <th>#</th>
                                    <th>Empresa</th>
                                    <th><?php echo $etiquetaAdminEstructura; ?></th>
                                    <th><?php echo $etiquetaAdminArea; ?></th>
                                    <th><?php echo $etiquetaAdminUO; ?></th>
                                    <th><?php echo $etiquetaAdminNJ; ?></th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>

                            <tbody id="tabla_lista">
                                <?php
                                $count = 1;
                                $query = mysqli_query($connect_valentina, "SELECT * FROM Estructura_Empresa WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' ORDER BY vicepresidencia ASC ");
                                while ($data = mysqli_fetch_array($query)) {

                                    $txt_estado = '';
                                    foreach ($Array_Estado as $nivel) {
                                        if ($nivel[0] == $data["estado"]) {
                                            $txt_estado = $nivel[1];
                                        }
                                    }

                                    $queryVicepresidencia = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id = '" . $data['vicepresidencia'] . "'");
                                    while ($dataV = mysqli_fetch_array($queryVicepresidencia)) {
                                        $vicepresidencia = $dataV["nombre"];
                                    }

                                    $queryArea = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = '" . $data['area'] . "'");
                                    while ($dataV = mysqli_fetch_array($queryArea)) {
                                        $area = $dataV["nombre"];
                                    }

                                    $bt_editar = '';
                                    if ($dtEmpleado["role"] == 1) {
                                        $bt_editar = '
                                <a href="' . $url . '?pg=administrar/estructura/detalle&id=' . $data["id"] . '">
                                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                </a>
                                <button type="button" id="sidebarCollapse" class="btn btn-danger btn-sm" title="Quitar Estructura" style="font-size: 12px; padding: 3px 5px;" onclick="Eliminar_Estructura(' . $data["id"] . ')">
                                    <i class="bx bx-trash"></i>
                                </button>
                            ';
                                    }

                                    echo '
                        <tr>
                            <td>' . $count . '</td>
                            <td>' . $data["compania"] . '</td>
                            <td>' . $vicepresidencia . '</td>
                            <td>' . $area . '</td>
                            <td>' . $data["unidad_organizativa"] . '</td>
                            <td>' . $data["nivel_jerarquico"] . '</td>
                            <td>' . $txt_estado . '</td>
                            <td>' . $bt_editar . '</td>
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
        $('#estructura').DataTable({
            columnDefs: [{
                    responsivePriority: 1,
                    targets: 0
                },
                {
                    responsivePriority: 2,
                    targets: -1
                }
            ],
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

<script>
    $(".menu_section").addClass("active");
    // $("#nav_empresa").addClass("active");
    jQuery("#menu_empresa").css("display", "none");
    $("#bt_admin_estructura_empresa").addClass("current-page");
</script>