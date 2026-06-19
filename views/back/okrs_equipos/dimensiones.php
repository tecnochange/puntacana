<script>
    $(".menu_section").addClass("active");
    $("#nav_estrategia").addClass("active");
    jQuery("#menu_estrategia").css("display", "none");
    $("#bt_admin_bsc").addClass("current-page");
</script>
<?php include("views/okrs/layouts/modal_okr.php"); 
$querySM22 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 2 AND id_submenu = 11");
$dataSM22 = mysqli_fetch_array($querySM22);
?>
<div class="row">
    <div class="col-md-12">
        <div class="card" style="padding: unset !important;">
            <div class="card-header" style="background-color: #FFFFFF !important;padding: .5rem 1rem !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-sitemap" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM22["nombre"]; ?></h4>
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

                        <a href="<?php echo $url; ?>?pg=okrs/dimension/detalle">
                            <button type="button" id="sidebarCollapse" class="btn btn-primary btn-sm">
                                Crear Dimensión
                            </button>
                        </a>
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
                                    if ($_SESSION['role'] == 1) {
                                        $bt_editar = '
								<a href="' . $url . '?pg=okrs/dimension/detalle&id=' . $data["id"] . '">
									<button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
										<i class="fas fa-edit"></i>
									</button>
								</a>
							';
                                        $bt_editar .= '<button type="button" class="btn btn-danger btn-sm bt_editar" title="Eliminar Plan" onclick="EliminarDimension(' . $data["id"] . ',' . $_SESSION["id_empresa"] . ',' . $_SESSION["id_user"] . ')" >
                            <i class="fas fa-times"></i>
                        </button>';
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
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        $('#dimensiones').DataTable({

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
                        url: "?pg=okrs_equipos/dimensiones"
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