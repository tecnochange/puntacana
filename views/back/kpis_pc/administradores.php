<script>
    $(document).ready(function() {
        $(".menu_section").addClass("active");
        $("#nav_kpis").addClass("active");
        jQuery("#menu_kpi").css("display", "none");
        $("#bt_kpi_administradores").addClass("current-page");
    });
</script>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h5>Configuración Administradores KPI</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" style="text-align:end;">
            <a href="<?php echo $url; ?>?pg=kpis_pc/detalle/crear_administrador">
                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Crear Administrador
                </button>
            </a>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="table-responsive">
                            <table border="1" id="administradores" class="display table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Cargo</th>
                                        <th>Vicepresidencia</th>
                                        <th>Área</th>
                                        <th>Estado</th>
                                        <!-- <th>Áreas Encargadas</th> -->
                                        <th>Acción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $queryAdministrador = mysqli_query($connect_kpis, "SELECT * FROM Administradores_Kpi WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'");
                                    while ($data = mysqli_fetch_array($queryAdministrador)) {

                                        $queryEmpleado = mysqli_query($connect_valentina, "SELECT E.nombre AS nombre, C.nombre AS nombre_cargo, A.nombre AS nombre_area, VP.nombre AS nombre_vp
                                        FROM Empleados E
                                        INNER JOIN Cargos C ON C.id = E.id_cargo
                                        INNER JOIN Areas A ON A.id = E.area
                                        INNER JOIN Vicepresidencia VP ON VP.id = E.unidad_corporativa 
                                        WHERE E.id = '" . $data["id_empleado"] . "' ");
                                        $dataEmpleado = mysqli_fetch_array($queryEmpleado);

                                        $array_lista_areas = explode(",", $data["areas"]);
                                        $areas = "";
                                        foreach ($array_lista_areas as $id_area) {
                                            $queryArea = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = $id_area");
                                            $dataArea = mysqli_fetch_array($queryArea);
                                            $areas .= $dataArea["nombre"] . "<br>";
                                        }
                                        if($data["estado"] == 1){
                                            $txtEstado = 'Activo';
                                        }else{
                                            $txtEstado = 'Inactivo';
                                        }
                                        $btnEditar = '<a href="'.$url.'?pg=kpis_pc/detalle/editar_administrador&id=' . $data["id"] . '">
                                            <button type="button" id="sidebarCollapse" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </a>';
                                    ?>
                                        <tr>
                                            <td><?php echo $dataEmpleado["nombre"]; ?></td>
                                            <td><?php echo $dataEmpleado["nombre_cargo"]; ?></td>
                                            <td><?php echo $dataEmpleado["nombre_vp"]; ?></td>
                                            <td><?php echo $dataEmpleado["nombre_area"]; ?></td>
                                            <td><?php echo $txtEstado; ?></td>
                                            <!-- <td><?php //echo $areas; ?></td> -->
                                            <td><?php echo $btnEditar; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#administradores').DataTable({
            columnDefs: [{
                    responsivePriority: 1,
                    targets: 0
                },
                {
                    responsivePriority: 2,
                    targets: -1
                }
            ],
            order: [[0, 'asc']],
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
            }]
        });
    });
</script>