<script>
    $(".menu_section").addClass("active");
    jQuery("#menu_kpi").css("display", "none");
    $("#bt_kpi_objetivos_sg").addClass("current-page");
</script>
<style>
    .card,
    .card-header,
    .card-body {
        background-color: white !important;
    }
</style>
<?php
include("views/kpis_pc/etiquetas.php");
$querySM63 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 6 AND id_submenu = 35");
$dataSM63 = mysqli_fetch_array($querySM63);
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #FFFFFF !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-chart-line" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM63["nombre"]; ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" style="text-align: end;">
            <a href="<?php echo $url; ?>?pg=kpis_pc/detalle/objetivo_sg">
                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Crear <?php echo $etiquetaKpiOS; ?>
                </button>
            </a>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table border="1" id="objetivos_sg" class="display table" style="width:100%">
                            <thead>
                                <th>Año</th>
                                <th><?php echo $etiquetaKpiAM; ?></th>
                                <th><?php echo $etiquetaKpiAP; ?></th>
                                <th>Objetivo</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </thead>
                            <tbody>
                                <?php
                                $query = mysqli_query($connect_kpis, "SELECT * FROM Objetivo_Sg WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'");
                                while ($data = mysqli_fetch_array($query)) {
                                    $queryVP = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id = " . $data['id_vp'] . "");
                                    $dataVP = mysqli_fetch_array($queryVP);

                                    $queryArea = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = " . $data['id_area'] . "");
                                    $dataArea = mysqli_fetch_array($queryArea);

                                    $txt_estado = '';
                                    foreach ($Array_Estado  as $estado) {
                                        if ($estado[0] == $data["estado"]) {
                                            $txt_estado = $estado[1];
                                        }
                                    }

                                    $bt_editar = '<a href="' . $url . '?pg=kpis_pc/detalle/objetivo_sg&id=' . $data["id"] . '">
                                                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </a>
                                        ';
                                ?>
                                    <tr>
                                        <td><?php echo $data["anio"]; ?></td>
                                        <td><?php echo $dataVP["nombre"]; ?></td>
                                        <td><?php echo $dataArea["nombre"]; ?></td>
                                        <td><?php echo $data["objetivo"]; ?></td>
                                        <td><?php echo $txt_estado; ?></td>
                                        <td><?php echo $bt_editar; ?></td>
                                    </tr>
                                <?php
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
        $('#objetivos_sg').DataTable({
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
                [1, 'asc']
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