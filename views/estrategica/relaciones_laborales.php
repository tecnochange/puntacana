<script>
$(document).ready(function() {
    $('#menuEstrategia').collapse();
    $('#bt_estrategia_relaciones_laborales').addClass('active');
});
</script>

<div class="container">

    <div class="card">

        <div class="card-header">
            <?php if($VALIDAR_ROOT["crear"]){ ?>
                            <a href="<?php echo $url; ?>?pg=estrategica/relaciones_laborales/detalle">
                                <button type="button" class="btn btn-warning btn-sm" style="float:right">
                                    Nuevo
                                </button>
                            </a>
                    <?php } ?>
            <h3>Relaciones Laborales</h3>
        </div>
        <div class="card-body">
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
                                        $query = mysqli_query($connect_admin, "SELECT * FROM Relaciones_Laborales WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'");
                                        while ($data = mysqli_fetch_array($query)) {
                                            $queryEmpleado = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $data['id_empleado'] . "'");
                                            $dataEmpleado = mysqli_fetch_array($queryEmpleado);
                                            $queryCargo = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE id = '" . $dataEmpleado["id_cargo"] . "' ");
                                            $dataCargo = mysqli_fetch_array($queryCargo);
                                            $queryArea = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id = '" . $data["id_area"] . "' ");
                                            $dataArea = mysqli_fetch_array($queryArea);
                                            $queryVP = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id = '" . $data["id_vp"] . "' ");
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
                                            <a href="' . $url . '?pg=estrategica/relaciones_laborales/detalle&id=' . $data["id"] . '">
                                                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
                                                    <i class="bx bx-edit"></i>
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