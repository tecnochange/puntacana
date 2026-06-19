<script>
    $(document).ready(function() {
        $(".menu_section").addClass("active");
        // $("#nav_empresa").addClass("active");
        jQuery("#menu_kpi").css("display", "none");
        $("#bt_kpi_auditoria").addClass("current-page");
    });
</script>
<?php
$queryMA6 = mysqli_query($connect_valentina, "SELECT * FROM Menu_Empresa WHERE id_empresa = " . $_POST["id_empresa"] . " AND estado = 1 AND id_menu = 6");
$dataMA6 = mysqli_fetch_array($queryMA6);
include("views/kpis_pc/etiquetas.php");
?>

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <h1>Reporte Auditoria <?php echo $dataMA6["nombre"]; ?></h1>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <div class="card">
                    <div class="card-body">
                        <table border="1" id="auditoria" class="display table" style="width:100%">
                            <thead>
                                <th scope="col" width="60">#</th>
                                <th scope="col">Realizado Por</th>
                                <th scope="col"><?php echo $etiquetaKpiAM; ?></th>
                                <th scope="col"><?php echo $etiquetaKpiAP; ?></th>
                                <th scope="col">Acción</th>
                                <th scope="col">Descripción Acción</th>
                                <th scope="col"><?php echo $dataMA6["nombre"]; ?></th>
                                <th scope="col">Tipo <?php echo $dataMA6["nombre"]; ?></th>
                                <th scope="col">Fecha</th>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                $query = mysqli_query($connect_kpis, "SELECT * FROM Auditoria_Kpi WHERE id_empresa = " . $_SESSION["id_empresa"] . "");
                                while ($data = mysqli_fetch_array($query)) {
                                    $empleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = " . $data["id_empleado"] . "");
                                    $dataEmpleado = mysqli_fetch_array($empleado);

                                    if ($dataEmpleado["area"] > 0) {
                                        $queryArea = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = " . $dataEmpleado["area"] . "");
                                        $dataArea = mysqli_fetch_array($queryArea);
                                        $area = $dataArea["nombre"];
                                    } else {
                                        $area = $dataEmpleado["area"];
                                    }

                                    if ($dataEmpleado["unidad_corporativa"] > 0) {
                                        $queryVicepresidencia = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id = " . $dataEmpleado["unidad_corporativa"] . "");
                                        $dataVicepresidencia = mysqli_fetch_array($queryVicepresidencia);
                                        $vicepresidencia = $dataVicepresidencia["nombre"];
                                    } else {
                                        $vicepresidencia = $dataEmpleado["unidad_corporativa"];
                                    }

                                    $okr = mysqli_query($connect_okrs, "SELECT * FROM Okrs WHERE id = " . $data["id_okr"] . "");
                                    $dataOKR = mysqli_fetch_array($okr);

                                    if ($dataOKR["tipo"] == 1) {
                                        $tipo = 'Organizacional';
                                    } else {
                                        $tipo = 'Equipo';
                                    }

                                    switch ($data["accion"]) {
                                        case 'CREACIÓN':
                                            $color = 'green';
                                            break;
                                        case 'ACTUALIZAR':
                                            $color = 'orange';
                                            break;
                                        case 'ELIMINAR':
                                            $color = 'red';
                                            break;
                                    }

                                    if ($data["id_kpi"] > 0) {
                                        $resultado = mysqli_query($connect_kpis, "SELECT * FROM Kpis WHERE id = " . $data["id_kpi"] . "");
                                        $dataResultado = mysqli_fetch_array($resultado);
                                        $infoResultado = $dataResultado["indicador"];
                                    } else {
                                        $infoResultado = '';
                                    }

                                    $Array_tipo_kpi_PC1 = array(
                                        array("1",$etiquetaKpiT ), 
                                        array("2",$etiquetaKpiE ),	
                                    );
                                    
                                    foreach ($Array_tipo_kpi_PC1 as $tipoKpi) {
                                        if ($data["tipo_kpi"] ==  $tipoKpi[0]) {
                                            $tipo =  $tipoKpi[1];
                                        }
                                    }
                                    

                                ?>
                                    <tr>
                                        <td><?php echo $count; ?></td>
                                        <td><?php echo $dataEmpleado["nombre"]; ?></td>
                                        <td><?php echo $vicepresidencia; ?></td>
                                        <td><?php echo $area; ?></td>
                                        <td style="color: <?php echo $color; ?> !important;"><?php echo $data["accion"]; ?></td>
                                        <td><?php echo $data["descripcion"]; ?></td>
                                        <td><?php echo $infoResultado; ?></td>
                                        <td><?php echo $tipo; ?></td>
                                        <td><?php echo $data["created_at"]; ?></td>
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