<script>
    $(document).ready(function() {
        $(".menu_section").addClass("active");
        // $("#nav_empresa").addClass("active");
        jQuery("#menu_empresa").css("display", "none");
        $("#bt_admin_auditoria").addClass("current-page");
    });
</script>
<?php
$querySM41 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 4 AND id_submenu = 20");
$dataSM41 = mysqli_fetch_array($querySM41);
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #FFFFFF !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-dice-d20" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM41["nombre"]; ?></h4>
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
            <div class="table-responsive">
                <div class="card">
                    <div class="card-body">
                        <table border="1" id="auditoria" class="display table" style="width:100%">
                            <thead>
                                <th scope="col" width="60">#</th>
                                <th scope="col">Realizado Por</th>
                                <?php if ($_SESSION["id_empresa"] == 1) {
                                ?>
                                    <th scope="col">Vicepresidencia</th>
                                    <th scope="col">Área</th>
                                <?php }
                                ?>
                                <th scope="col">Acción</th>
                                <th scope="col">Descripción de la Acción</th>
                                <th scope="col">Módulo</th>                                
                                <th scope="col">Fecha</th>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                $query = mysqli_query($connect_valentina, "SELECT * FROM Auditoria_Admin WHERE id_empresa = " . $_SESSION["id_empresa"] . "");
                                while ($data = mysqli_fetch_array($query)) {
                                    $empleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = " . $data["id_empleado"] . "");
                                    $dataEmpleado = mysqli_fetch_array($empleado);

                                    if($dataEmpleado["area"] > 0){
                                        $queryArea = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = " . $dataEmpleado["area"] . "");
                                        $dataArea = mysqli_fetch_array($queryArea);
                                        $area = $dataArea["nombre"];
                                    }else{
                                        $area = $dataEmpleado["area"];
                                    }

                                    if($dataEmpleado["unidad_corporativa"] > 0){
                                        $queryVicepresidencia = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id = " . $dataEmpleado["unidad_corporativa"] . "");
                                        $dataVicepresidencia = mysqli_fetch_array($queryVicepresidencia);
                                        $vicepresidencia = $dataVicepresidencia["nombre"];
                                    }else{
                                        $vicepresidencia = $dataEmpleado["unidad_corporativa"];
                                    }

                                    switch ($data["accion"]) {
                                        case 'CREAR':
                                            $color = 'green';
                                            break;
                                        case 'ACTUALIZAR':
                                            $color = 'orange';
                                            break;
                                        case 'ELIMINAR':
                                            $color = 'red';
                                            break;
                                    }

                                    

                                ?>
                                    <tr>
                                        <td>
                                            <?= $count ?>
                                        </td>
                                        <td><?= $dataEmpleado["nombre"] ?></td>
                                        <?php if ($_SESSION["id_empresa"] == 1) {
                                ?>
                                    <td><?= $vicepresidencia ?></td>
                                    <td><?= $area ?></td>
                                <?php }
                                ?>
                                        <td style="color: <?= $color ?> !important;"><?= $data["accion"] ?></td>
                                        <td><?= $data["descripcion"] ?></td>
                                        <td><?= $data["modulo"] ?></td>
                                        <td><?= $data["created_at"] ?></td>
                                    </tr>
                                <?php
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
        $('#auditoria').DataTable({
            columnDefs: [{
                    responsivePriority: 1,
                    targets: 0
                },
                {
                    responsivePriority: 2,
                    targets: -1
                }
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