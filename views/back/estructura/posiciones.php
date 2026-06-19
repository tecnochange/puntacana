<script>
    $(document).ready(function() {
        $(".menu_section").addClass("active");
        // $("#nav_empresa").addClass("active");
        jQuery("#menu_empresa").css("display", "none");
        $("#bt_estructura_posiciones").addClass("current-page");
    });
</script>
<?php
include("views/administrar/etiquetas.php");
$querySM410 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 4 AND id_submenu = 29");
$dataSM410 = mysqli_fetch_array($querySM410);
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #FFFFFF !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-dice-d20" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM410["nombre"]; ?></h4>
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
                    <td align="right" width="200">
                        <?php if ($dtEmpleado["role"] == 1) { ?>
                            <a href="<?php echo $url; ?>?pg=estructura/posicion/detalle">
                                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Crear Posición
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
                        <table border="1" id="posiciones" class="display table" style="width:100%">
                            <thead>
                                <tr>
                                    <th scope="col" width="60">#</th>
                                    <th scope="col"><?php echo $etiquetaCargo; ?></th>
                                    <th scope="col">Cod. Posición</th>
                                    <th scope="col"><?php echo $etiquetaArea; ?></th>
                                    <th scope="col"><?php echo $etiquetaVP; ?></th>
                                    <th scope="col">Ocupante</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                $query = mysqli_query($connect_valentina, "SELECT Posiciones.id AS id, Cargos.nombre AS nombre_cargo, Areas.nombre AS nombre_area, 
                    Posiciones.estado AS estado, are.nombre as gerencia 
                    FROM Posiciones 
                    LEFT JOIN Cargos on Cargos.id = Posiciones.id_cargo 
                    LEFT JOIN Areas on Areas.id = Posiciones.id_area 
                    LEFT JOIN Areas as are on are.id = Posiciones.id_departamento 
                    WHERE Posiciones.id_empresa = '" . $_SESSION["id_empresa"] . "'
                    ORDER BY Cargos.nombre ASC");
                                while ($data = mysqli_fetch_array($query)) {
                                    $txt_name = "";
                                    $qryCollaborator = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id_posicion = '" . $data["id"] . "' AND estado = 1 ");
                                    $dtCollaborator = mysqli_fetch_array($qryCollaborator);
                                    if ($qryCollaborator->num_rows == 0) {
                                        $txt_name = "Vacante";
                                    } else {
                                        $txt_name = $dtCollaborator["nombre"] . " " . $dtCollaborator["apellidos"];
                                    }
                                ?>
                                    <tr>
                                        <td>
                                            <?= $count ?>
                                        </td>
                                        <td><?= $data["nombre_cargo"] ?></td>
                                        <td><?= $data["id"] ?></td>
                                        <td><?= $data["nombre_area"] ?></td>
                                        <td><?= $data["gerencia"] ?></td>
                                        <td><?= $txt_name ?></td>
                                        <td><?= $data["estado"] ?></td>
                                        <td>
                                            <a href="<?php echo $url ?>?pg=estructura/posicion/detalle&id=<?= $data["id"] ?>">
                                                <button type="button" class="btn btn-success btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </a>
                                        </td>
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
        $('#posiciones').DataTable({
            pageLength: 25,
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