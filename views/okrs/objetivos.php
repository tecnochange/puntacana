<script>
    $(".menu_section").addClass("active");
    $("#nav_estrategia").addClass("active");
    jQuery("#menu_estrategia").css("display", "none");
    $("#bt_admin_objetivos").addClass("current-page");
</script>


<?php
$hoy = date("Y-m-d H:i:s");
$id = $_GET["id"];

if ($_POST["objetivo"] != "") {

    if ($_POST["id_registro"] != "") {
        $sentencia = "
			UPDATE  Objetivos_estrategicos  SET  dimensiones = '" . implode(",", $_POST["dimensiones"]) . "', 
			id_responsable = '" . $_POST["id_responsable"] . "', objetivo = '" . $_POST["objetivo"] . "' 
			WHERE id = '" . $_POST["id_registro"] . "'
			";

        mysqli_query($connect_okrs, $sentencia);
    } else {
        $sentencia = "
			INSERT INTO  Objetivos_estrategicos ( id_empresa ,  dimensiones ,  id_responsable ,  objetivo ,  estado ,  created_at ) 
			VALUES 
			( '" . $_SESSION['id_empresa'] . "', '" . implode(",", $_POST["dimensiones"]) . "', '" . $_POST["id_responsable"] . "', '" . $_POST["objetivo"] . "', 1, '" . $hoy . "'  )
			";
        mysqli_query($connect_okrs, $sentencia);
    }

    echo '<script> window.location.href = "?pg=okrs/objetivos";</script>';
}

$query = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id = '" . $id . "' ");
$data = mysqli_fetch_array($query);
$array_dimensiones = explode(",", $data["dimensiones"]);

$querySM24 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 2 AND id_submenu = 13");
$dataSM24 = mysqli_fetch_array($querySM24);
?>
<?php echo $respuesta; ?>
<div class="row">
    <div class="col-md-12">
        <div class="card" style="padding: unset !important;">
            <div class="card-header" style="background-color: #FFFFFF !important;padding: .5rem 1rem !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-sitemap" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM24["nombre"]; ?></h4>
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
                    <td align="right">

                        <a href="<?php echo $url; ?>?pg=okrs/objetivo/detalle">
                            <button type="button" id="sidebarCollapse" class="btn btn-primary btn-sm">
                                Crear Objetivo
                            </button>
                        </a>
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
                        <table border="1" id="objetivos" class="display table" style="width:100%;">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Objetivo</th>
                                    <th scope="col">Responsable</th>
                                    <th scope="col">País</th>
                                    <th scope="col">Sucursal</th>
                                    <th scope="col">Dimensiones</th>
                                    <th scope="col">Ponderación</th>
                                    <th scope="col">Año</th>
                                    <th scope="col" style="width: 120px">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>

                            <?php
                            $count = 1;
                            $queryObjetivos = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id_empresa = '" . $dtEmpleado['id_empresa'] . "' ");
                            while ($dataObjetivos = mysqli_fetch_array($queryObjetivos)) {
                                $query1 = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id = '" . $dataObjetivos["id"] . "' ");
                                $data1 = mysqli_fetch_array($query1);
                                $array_dimensiones = explode(",", $data1["dimensiones"]);
                                $cont = 0;
                                $count_anios = array();
                                foreach ($Array_Anio as $value) {
                                    $queryAnio = mysqli_query($connect_okrs, "SELECT * FROM Objetivos_estrategicos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = " . $value[1] . "");
                                    $count_anios[$cont]["anio"] = $value[1];
                                    $count_anios[$cont]["porcentaje"] = round(100 / (mysqli_num_rows($queryAnio)), 2);
                                    $cont++;
                                }
                                if (!isset($data1["ponderacion"])) {
                                    foreach ($count_anios as $periodo) {
                                        if ($data1["anio"] == $periodo["anio"]) {
                                            mysqli_query($connect_okrs, "UPDATE Objetivos_estrategicos SET ponderacion = ".$periodo["porcentaje"]." WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = " . $data1["anio"] . "");
                                            $ponderacion = $periodo["porcentaje"];
                                        }
                                    }
                                } else {
                                    if($data1["ponderacion"] == ""){
                                        foreach ($count_anios as $periodo) {
                                            if ($data1["anio"] == $periodo["anio"]) {
                                                mysqli_query($connect_okrs, "UPDATE Objetivos_estrategicos SET ponderacion = ".$periodo["porcentaje"]." WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = " . $data1["anio"] . "");
                                                $ponderacion = $periodo["porcentaje"];
                                            }
                                        }
                                    }else{
                                    $ponderacion = $data1["ponderacion"];
                                    }
                                }
                                $lista_dimensiones = "";
                                $array_dimensiones = explode(",", $dataObjetivos["dimensiones"]);
                                foreach ($array_dimensiones as $dimension) {
                                    $queryDim = mysqli_query($connect_okrs, "SELECT * FROM Dimensiones WHERE id = '" . $dimension . "' ");
                                    $dataDim = mysqli_fetch_array($queryDim);
                                    $lista_dimensiones .= $dataDim["nombre"] . "<br>";
                                }

                                $queryResponsable = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '" . $dataObjetivos["id_responsable"] . "' ");
                                $dataResponsable = mysqli_fetch_array($queryResponsable);

                                echo '
            <tr>
                <td><b>' . $count . '</b></td>
				<td>' . $dataObjetivos["objetivo"] . '</td>
				<td>' . $dataResponsable["nombre"] . ' ' . $dataResponsable["apellidos"] . '</td>
				<td>' . $dataObjetivos["pais"] . '</td>
				<td>' . $dataObjetivos["sucursal"] . '</td>
                <td>' . $lista_dimensiones . '</td>
                <td>' . $dataObjetivos["ponderacion"] . '%</td>	
                <td>' . $dataObjetivos["anio"] . '</td>
								
                <td>
                <a href="' . $url . '?pg=okrs/objetivo/detalle&id=' . $dataObjetivos["id"] . '" >
                <button type="button" class="btn btn-success btn-sm bt_editar" title="editar">
                    <i class="fa fa-eye"></i>
                </button>
                </a>
				
                <button type="button" class="btn btn-danger btn-sm bt_editar" title="editar" onclick="Elimimar(' . $dataObjetivos["id"] . ')">
                    <i class="fa fa-times"></i>
                </button>

                </td>
            </tr>

            ';
                                $count++;
                            }
                            ?>


                        </table>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('#objetivos').DataTable({

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
    var api = '<?php echo $url; ?>api/okrs/';

    var activar = false;

    function Elimimar(id) {

        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar un objetivo, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Elimimar(' + id + ')"> Confirmar </button>');
        } else {

            jQuery.ajax({
                    url: api + "eliminar_objetivo.php",
                    type: 'post',
                    data: {
                        id: id,
                        url: "?pg=okrs/objetivos"
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