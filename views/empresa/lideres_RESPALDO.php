<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_lideres').addClass('active');
});
</script>

<?php
include("app/models/estructura/Lideres.php");
$ClassLideres = new Lideres();

$array_lideres = $ClassLideres->lideres_lista($_POST);

?>




<?php
$array_colaboradores = [];
$sentencia_colaboradores = "
SELECT
    Empleados.id,
    Empleados.documento,
    Empleados.nombre, 
    Cargos.nombre AS nombre_cargo,
    Areas.nombre AS nombre_area
FROM
    Empleados
LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
LEFT JOIN Areas ON Areas.id = Empleados.area
WHERE
    Empleados.id_empresa = '" . $_SESSION["id_empresa"] . "'
ORDER BY
    Empleados.nombre ASC
";
$queryCol = mysqli_query($connect_admin, $sentencia_colaboradores);
while ($dataCol = mysqli_fetch_array($queryCol)) { 
    $array_colaboradores[$dataCol["id"]] = $dataCol;
}

$array_lideres = array();
$queryLideres = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empresa = '".$_SESSION["id_empresa"]."' ");
while ($dataLideres = mysqli_fetch_array($queryLideres)) { 
    array_push($array_lideres, $dataLideres);
}

?>

<div class="container">

    <div class="card">
        
        <div class="card-header">
            <h3>Gestión de Líderess</h3>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                        <table border="1" id="lideres" class="display table" style="width:100%;">
                            <thead class="">
                                <tr>
                                    <th scope="col" colspan="5">Colaborador</th>
                                    <th scope="col" colspan="6">Jefes Asignados</th>
                                </tr>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Documento</th>
                                    <th scope="col">Nombre y Apellidos</th>
                                    <th scope="col">Cargo</th>
                                    <th scope="col">Área</th>
                                    <th scope="col">Documento</th>
                                    <th scope="col">Nombre y Apellidos</th>
                                    <th scope="col">Cargo</th>
                                    <th scope="col">Áreas</th>
                                    <th scope="col">Acciones</th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $hoy = date("Y-m-d H:i:s");
                                $count = 1;


                                foreach($array_lideres as $lider){
                                    echo '
                                            <tr>
                                                <td>' . $count . '</td>
                                                <td>' . $lider["documento"] . '</td>
                                                <td>' . $lider["nombre"] . '</td>
                                                <td>' . $lider["cargo"] . '</td>
                                                <td>' . $lider["area"] . '</td>
                                                <td>' . $lider["documento_lider"] . '</td>
                                                <td>' . $lider["nombre_lider"] . '</td>
                                                <td>' . $lider["cargo_lider"] . '</td>
                                                <td>' . $lider["area_lider"] . '</td>
                                                <td>' . $lider["acciones"] . '</td>
                                                <td>
                                                    <a href="' . $url . '?pg=empresa/lider/agregar&id=' . $data["id"] . '">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" title="Asignar jefe">
                                                        <i class="bx bx-edit"></i>
                                                    </button>
                                                    </a>
                                                </td>
                                            </tr>
                                            ';
                                    $count++;

                                }

                                $sentencia = "
                                SELECT
                                    Empleados.id,
                                    Empleados.documento,
                                    Empleados.nombre, 
                                    Cargos.nombre AS nombre_cargo,
                                    Areas.nombre AS nombre_area
                                FROM
                                    Empleados
                                LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
                                LEFT JOIN Areas ON Areas.id = Empleados.area
                                WHERE
                                    Empleados.id_empresa = '" . $_SESSION["id_empresa"] . "' AND Empleados.estado = 1
                                ORDER BY
                                    Empleados.nombre ASC
                                ";

                                $query = mysqli_query($connect_admin, $sentencia);
                                while ($data = mysqli_fetch_array($query)) {

                                    $lista_lideres = "";

                                    foreach($array_lideres as $lider){

                                        $lista_jefes_acciones = '';

                                        if($lider["id_empleado"] == $data["id"] ){
                                            $data_lider = $array_colaboradores[$lider["id_jefe"]];

                                            $lista_jefes_acciones = '
                                                <div style="display: flex;gap: 8px;' . ($tiene_margen ? $estilos : '') . '">
                                                    <button type="button" id="sidebarCollapse" class="btn btn-danger btn-sm" title="Quitar jefe" style="font-size: 12px; padding: 3px 5px;" onclick="Elimimar_Jefe(' . $lider["id"] . ')">
                                                        <i class="bx bx-trash"></i>
                                                    </button>
                                                </div>
                                            ';

                                            $lista_lideres .= '
                                            <tr>
                                                <td>' . $count . '</td>
                                                <td>' . $data["documento"] . '</td>
                                                <td>' . $data["nombre"] . '</td>
                                                <td>' . $data["nombre_cargo"] . '</td>
                                                <td>' . $data["nombre_area"] . '</td>
                                                <td>' . $data_lider["documento"] . '</td>
                                                <td>' . $data_lider["nombre"] . '</td>
                                                <td>' . $data_lider["nombre_cargo"] . '</td>
                                                <td>' . $data_lider["nombre_area"] . '</td>
                                                <td>' . $lista_jefes_acciones . '</td>
                                                <td>
                                                    <a href="' . $url . '?pg=empresa/lider/agregar&id=' . $data["id"] . '">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" title="Asignar jefe">
                                                        <i class="bx bx-edit"></i>
                                                    </button>
                                                    </a>
                                                </td>
                                            </tr>
                                            ';
                                            $count++;
                                        }
                                    }

                                    if($lista_lideres == ""){
                                        $lista_lideres .= '
                                            <tr>
                                                <td>' . $count . '</td>
                                                <td>' . $data["documento"] . '</td>
                                                <td>' . $data["nombre"] . '</td>
                                                <td>' . $data["nombre_cargo"] . '</td>
                                                <td>' . $data["nombre_area"] . '</td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>

                                                <td></td>
                                                <td>
                                                    <a href="' . $url . '?pg=empresa/lider/agregar&id=' . $data["id"] . '">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" title="Asignar jefe">
                                                        <i class="bx bx-edit"></i>
                                                    </button>
                                                    </a>
                                                </td>
                                            </tr>
                                            ';
                                            $count++;
                                    }

                                    echo $lista_lideres;

                                    /*
                                    $lista_jefes_documentos = !empty($dataJefe) ? ('<div>' . $dataJefe["documento"] . '</div>') : '';
                                    $lista_jefes_nombres = !empty($dataJefe) ? ('<div>' . $dataJefe["nombre"] . ' ' . $dataCargo["apellidos"] . '</div>') : '';
                                    $lista_jefes_posiciones = !empty($dataJefe) ? '<div>' . $dataJefe["cargo"] . '</div>' : '';
                                    $lista_jefes_acciones = !empty($dataJefe) ? '<div style="height: 26px;"></div>' : '';
                                    $lista_jefes_areas = "";
                                    $counter_jefes = 0;
                                    




                                    $queryJefes = mysqli_query($connect_admin, "
                                    SELECT j.id, e.documento, e.nombre AS nombre, c.nombre as nombre_cargo, a.nombre as nombre_area
                                    FROM Lideres j
                                        INNER JOIN Empleados e ON e.id = j.id_jefe
                                        LEFT JOIN Posiciones p ON p.id = e.id_posicion
                                        LEFT JOIN Cargos c ON c.id = e.id_cargo
                                        LEFT JOIN Areas a ON a.id = e.area
                                    WHERE j.id_empleado = '" . $data["id"] . "' AND e.estado = 1 and j.id_empresa = ".$_SESSION["id_empresa"]."
                                    ");

                                    while ($dataJefes = mysqli_fetch_array($queryJefes)) {
                                        $tiene_margen = !empty($dataJefe) || $counter_jefes > 0;
                                        $estilos = 'margin-top: 10px;';

                                        $div_clases = $tiene_margen ? "style='$estilos'" : '';

                                        $lista_jefes_documentos .= '<div ' . $div_clases . '>' . $dataJefes["documento"] . '</div>';
                                        $lista_jefes_nombres .= '<div ' . $div_clases . '>' . $dataJefes["nombre"] . '</div>';
                                        $lista_jefes_posiciones .= '<div ' . $div_clases . '>' . $dataJefes["nombre_cargo"] . '</div>';

                                        $lista_jefes_areas .= '<div ' . $div_clases . '>' . $dataJefes["nombre_area"] . '</div>';


                                        $lista_jefes_acciones .= '
                            <div style="display: flex;gap: 8px;' . ($tiene_margen ? $estilos : '') . '">
                                <button type="button" id="sidebarCollapse" class="btn btn-danger btn-sm" title="Quitar jefe" style="font-size: 12px; padding: 3px 5px;" onclick="Elimimar_Jefe(' . $dataJefes["id"] . ')">
                                    <i class="bx bx-trash"></i>
                                </button>
                            </div>
                        ';

                                        $counter_jefes++;
                                    }

                                    echo '
                        <tr>
                            <td>' . $count . '</td>
                            <td>' . $data["documento"] . '</td>
                            <td>' . $data["nombre"] . '</td>
                            <td>' . $data["nombre_cargo"] . '</td>
							<td>' . $data["nombre_area"] . '</td>
                            <td>' . $lista_jefes_documentos . '</td>
                            <td>' . $lista_jefes_nombres . '</td>
                            <td>' . $lista_jefes_posiciones . '</td>
							<td>' . $lista_jefes_areas . '</td>
							<td></td>
                            <td>' . $lista_jefes_acciones . '</td>
                            <td>
                                <a href="' . $url . '?pg=empresa/lider/agregar&id=' . $data["id"] . '">
                                <button type="button" class="btn btn-outline-secondary btn-sm" title="Asignar jefe">
                                    <i class="bx bx-edit"></i>
                                </button>
                                </a>
                            </td>
                        </tr>
                    ';

                                    $count++;
                                    */
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
        $('#lideres').DataTable({
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
                        },
                        {
                            extend: 'csv',
                            text: 'Exportar a CSV',
                            title: 'Lideres',
                            exportOptions: {
                                columns: [],
                                modifier: {
                                    page: 'all'
                                }
                            },
                            customize: function(csv) {
                                var BOM = "\uFEFF";
                                <?php
                                    $queryLider = mysqli_query($connect_valentina, "SELECT * FROM Lideres WHERE id_empresa = '" . $_SESSION['id_empresa'] . "'");
                                    $rows = [];
                                    while ($dataLider = mysqli_fetch_array($queryLider)) {
                                        $qC = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '" . $dataLider["id_empleado"] . "' AND id_empresa = '" . $_SESSION["id_empresa"] . "' ");
                                        $dataCol = mysqli_fetch_array($qC);
                                        $documentoColaborador = $dataCol["documento"];
                                        $nombreColaborador = $dataCol["nombre"];
                                        $correoColaborador = $dataCol["correo"];

                                        $qL = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '" . $dataLider["id_jefe"] . "' AND id_empresa = '" . $_SESSION["id_empresa"] . "' ");
                                        $dataLider = mysqli_fetch_array($qL);
                                        $documentoLider = $dataLider["documento"];
                                        $nombreLider = $dataLider["nombre"];
                                        $correoLider = $dataLider["correo"];
                                        $rows[] = [
                                            $documentoColaborador,
                                            $nombreColaborador,
                                            $correoColaborador,
                                            $documentoLider,
                                            $nombreLider,
                                            $correoLider
                                        ];

                                    }
                                    ?>
                                var dataExport = [
                                    ['Codigo / Documento (obligatorio)', 'Nombre completo (obligatorio)', 'Correo empresarial (obligatorio)', 'Codigo / Documento Lider(obligatorio)', 'Nombre completo Lider(obligatorio)', 'Correo empresarial Lider (obligatorio)'],
                                    <?php foreach ($rows as $index => $row) { ?>[
                                            '<?php echo $row[0]; ?>',
                                            '<?php echo $row[1]; ?>',
                                            '<?php echo $row[2]; ?>',
                                            '<?php echo $row[3]; ?>',
                                            '<?php echo $row[4]; ?>',
                                            '<?php echo $row[5]; ?>'
                                        ] <?php if ($index < count($rows) - 1) { ?>, <?php } ?>
                                    <?php } ?>
                                ];

                                dataExport = dataExport.map(function(row) {
                                    return row.map(function(cell) {
                                        return cell;
                                    });
                                });
                                var newCSV = BOM;
                                for (var i = 0; i < dataExport.length; i++) {
                                    newCSV += dataExport[i].join(';');

                                    if (i < dataExport.length - 1) {
                                        newCSV += '\n';
                                    }
                                }

                                return newCSV + csv;
                            }
                        }
                    ]
                }

            ]
        });
        $('.div_content').width($('#lideres').width());
    });
</script>

<script>
    $(document).ready(function() {
        $("#buscador").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".tabla_lista tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    var api = '<?php echo $url; ?>/api/administrar/';

    var activar = false;

    function Elimimar_Jefe(id) {

        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar un jefe, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Elimimar_Jefe(' + id + ')"> Confirmar </button>');

        } else {

            jQuery.ajax({
                    url: api + "eliminar_lideres.php",
                    type: 'post',
                    data: {
                        id: id,
                        url: "?pg=estructura/lideres"
                    },
                }).done(function(resp) {
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp) {});

        }
    }
</script>