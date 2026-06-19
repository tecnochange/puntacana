<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_nivel_jerarquico').addClass('active');
});
</script>



<?php 
include("views/administrar/etiquetas.php");
$_SESSION["id_colaborador_edit"] = ""; 
$querySM49 = mysqli_query($connect_admin, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 4 AND id_submenu = 28");
$dataSM49 = mysqli_fetch_array($querySM49);
?>

<div class="container">

    <div class="card">

        <div class="card-header">
            <table style="float:right">
                <tr>
                    <td align="right">
                        <?php if($VALIDAR_ROOT["crear"]){ ?>
                        <a href="<?php echo $url; ?>?pg=empresa/nivel_jerarquico/detalle">
                            <button type="button" class="btn btn-warning btn-sm">
                                <i class="bx bx-plus"></i> Crear 
                            </button>
                        </a>
                        <?php } ?>
                    </td>
                </tr>
            </table>
            <h3>Nivel Jerárquico</h3>
        </div>
        
        <div class="card-body">

                    <div class="table-responsive">
                        <table border="1" id="nivel_jerarquico" class="display table" style="width:100%">
                            <thead class="thead-success">
                                <tr>
                                    
                                    <th scope="col">Nivel</th>
                                    <th scope="col">Nombre <?php echo $etiquetaAdminNJ; ?></th>                                    
                                    <th scope="col">Estado</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>

                            <tbody id="tabla_lista">
                                <?php

                                $count = 1;
                                $query = mysqli_query($connect_admin, "SELECT * FROM Nivel_Jerarquico WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' ORDER BY nombre ASC  ");
                                while ($data = mysqli_fetch_array($query)) {
                                    $txt_estado = '';
                                    foreach ($Array_Estado  as $estado) {
                                        if ($estado[0] == $data["estado"]) {
                                            $txt_estado = $estado[1];
                                        }
                                    }

                                    if($VALIDAR_ROOT["editar"]){
                                        $bt_editar = '
                                            <a href="' . $url . '?pg=empresa/nivel_jerarquico/detalle&id=' . $data["id"] . '">
                                                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                            </a>
                                        ';
                                    }

                                    echo '
                                    <tr>
                                        
                                        <td>' . $data["nivel"] . '</td>
                                        <td>' . $data["nombre"] . '</td>
                                        
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




<script type="text/javascript">
    $(document).ready(function() {
        $('#nivel_jerarquico').DataTable({
            columnDefs: [{
                    responsivePriority: 1,
                    targets: 0
                },
                {
                    responsivePriority: 2,
                    targets: -1
                }
            ],
            order: [
                [2, 'asc']
            ],
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
</script>


<script>
    $(document).ready(function() {

        $("#buscador").on("keyup", function() {
            var value = $(this).val().toLowerCase();

            $("#tabla_lista tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

    });
</script>