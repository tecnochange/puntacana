<script>
$(document).ready(function() {
    $('#menuVisual').collapse();
    $('#bt_visualizaciones_auditorias').addClass('active');
});
</script>

<style>
	.card, .card-header, .card-body, .card-footer{
		background-color: white !important;
	}
</style>

<div class="container">
    <div class="card mb-3">
        <div class="card-header">
            <h3>AUDITORÍA DE OKRS</h3>
        </div>
    </div>

   

    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
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
                                <th scope="col">OKR</th>
                                <th scope="col">Tipo OKR</th>
                                <th scope="col">Resultado</th>
                                <th scope="col">Iniciativa</th>
                                <th scope="col">id registro</th>
                                <th scope="col">Fecha</th>
                    </thead>
                    <tbody>
                        <?php
                        $count = 1;
                        $query = mysqli_query($connect_okrs, "SELECT * FROM Auditoria_Okrs WHERE id_empresa = " . $user_log["id_empresa"] . " ORDER BY created_at DESC LIMIT 3000 ");
                        while ($data = mysqli_fetch_array($query)) {
                                    $empleado = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = " . $data["id_empleado"] . "");
                                    $dataEmpleado = mysqli_fetch_array($empleado);

                                    if($dataEmpleado["area"] > 0){
                                        $queryArea = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id = " . $dataEmpleado["area"] . "");
                                        $dataArea = mysqli_fetch_array($queryArea);
                                        $area = $dataArea["nombre"];
                                    }else{
                                        $area = $dataEmpleado["area"];
                                    }

                                    if($dataEmpleado["unidad_corporativa"] > 0){
                                        $queryVicepresidencia = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id = " . $dataEmpleado["unidad_corporativa"] . "");
                                        $dataVicepresidencia = mysqli_fetch_array($queryVicepresidencia);
                                        $vicepresidencia = $dataVicepresidencia["nombre"];
                                    }else{
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

                                    if ($data["id_kr"] > 0) {
                                        $resultado = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Resultados WHERE id = " . $data["id_kr"] . "");
                                        $dataResultado = mysqli_fetch_array($resultado);
                                        $infoResultado = $dataResultado["descripcion"];
                                    } else {
                                        $infoResultado = '';
                                    }

                                    if ($data["id_iniciativa"] > 0) {
                                        $iniciativa = mysqli_query($connect_okrs, "SELECT * FROM Okrs_Iniciativas WHERE id = " . $data["id_iniciativa"] . "");
                                        $dataIniciativa = mysqli_fetch_array($iniciativa);
                                        $infoIniciativa = $dataIniciativa["descripcion"];
                                    } else {
                                        $infoIniciativa = '';
                                    }

                        ?>
                                    <tr>
                                        <td>
                                            <?= $count ?>
                                        </td>
                                        <td><?= $dataEmpleado["nombre"] ?></td>
                        <?php if ($_SESSION["id_empresa"] == 1) { ?>
                                    <td><?= $vicepresidencia ?></td>
                                    <td><?= $area ?></td>
                        <?php } ?>
                                        <td style="color: <?= $color ?> !important;"><?= $data["accion"] ?></td>
                                        <td><?= $data["descripcion"] ?></td>
                                        <td><?= $dataOKR["objetivo_okr"] ?></td>
                                        <td><?= $tipo ?></td>
                                        <td><?= $infoResultado ?></td>
                                        <td><?= $infoIniciativa ?></td>
                                        <td><?= $data["id"] ?></td>
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