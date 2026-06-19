<script>
    $(".menu_section").addClass("active");
    $("#nav_estrategia").addClass("active");
    jQuery("#menu_estrategia").css("display", "none");
    $("#bt_admin_relaciones_laborales").addClass("current-page");
</script>
<style>
    .card,
    .card-header,
    .card-body,
    .card-footer {
        background-color: #ffffff !important;
    }

    #iconCabecera {
        font-size: 24px;
        color: #007ae1;
    }
</style>
<?php
$querySM26 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 2 AND id_submenu = 15");
$dataSM26 = mysqli_fetch_array($querySM26);
?>
<div class="row">
    <div class="col-md-12">
        <div class="card" style="padding: unset !important;">
            <div class="card-header" style="background-color: #FFFFFF !important;padding: .5rem 1rem !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-sitemap" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM26["nombre"]; ?></h4>
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
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12" style="text-align: end;">
                            <?php if ($_SESSION["role_plataforma"] == 1) { ?>
                                <a href="<?php echo $url; ?>?pg=administrar/relaciones_laborales/detalle">
                                    <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm">
                                        <i class="fas fa-plus"></i> Crear Relación Laboral
                                    </button>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table border="1" id="responsables" class="display table" style="width:100%">
                                    <thead>
                                        <th>Nombre</th>
                                        <th>Vicepresidencia Encargada</th>
                                        <th>Área Encargada</th>
                                        <th>OKRS</th>
                                        <th>KPIS</th>
                                        <th>Competencias</th>
                                        <th>Acciones</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = mysqli_query($connect_valentina, "SELECT * FROM Relaciones_Laborales WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'");
                                        while ($data = mysqli_fetch_array($query)) {
                                            $queryEmpleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '" . $data['id_empleado'] . "'");
                                            $dataEmpleado = mysqli_fetch_array($queryEmpleado);
                                            $queryCargo = mysqli_query($connect_valentina, "SELECT * FROM Cargos WHERE id = '" . $dataEmpleado["id_cargo"] . "' ");
                                            $dataCargo = mysqli_fetch_array($queryCargo);
                                            $queryArea = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = '" . $data["id_area"] . "' ");
                                            $dataArea = mysqli_fetch_array($queryArea);
                                            $queryVP = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id = '" . $data["id_vp"] . "' ");
                                            $dataVP = mysqli_fetch_array($queryVP);

                                            $mod_OKRS = $mod_KPIS = $mod_Competencias = "";
                                            if ($data["mod_okrs"] == "on") {
                                                $mod_OKRS = "Activado";
                                            }
                                            if ($data["mod_kpis"] == "on") {
                                                $mod_KPIS = "Activado";
                                            }
                                            if ($data["mod_competencias"] == "on") {
                                                $mod_Competencias = "Activado";
                                            }
                                            $bt_editar = '
                                            <a href="' . $url . '?pg=administrar/relaciones_laborales/detalle&id=' . $data["id"] . '">
                                                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </a>
                                        ';
                                        ?>
                                            <tr>
                                                <td><?php echo $dataEmpleado["nombre"]; ?></td>
                                                <td><?php echo $dataVP["nombre"]; ?></td>
                                                <td><?php echo $dataArea["nombre"]; ?></td>
                                                <td><?php echo $mod_OKRS; ?></td>
                                                <td><?php echo $mod_KPIS; ?></td>
                                                <td><?php echo $mod_Competencias; ?></td>
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
    </div>
    <br>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#responsables').DataTable({
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