<script>
    $(document).ready(function() {
        $(".menu_section").addClass("active");
        // $("#nav_competencias").addClass("active");
        jQuery("#menu_competencias").css("display", "none");
        $("#bt_comp_perfiles").addClass("current-page");
    });
</script>

<?php
$hoy = date("Y-m-d H:i:s");

function eliminar_tildes($archivo)
{

    $cadena = $archivo;
    $cadena = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Ã¡', 'Á', 'À', 'Â', 'Ä', 'Ã¡', 'Ã', 'Ã', 'ÃƒÁ'),
        array('á', 'á', 'á', 'á', 'á', 'á', 'Á', 'Á', 'Á', 'Á', 'Á', 'Á', 'Á', 'Á'),
        $cadena
    );

    $cadena = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë', 'Ã©', 'Ã‰'),
        array('e', 'e', 'e', 'e', 'É', 'É', 'É', 'É', 'é', 'É'),
        $cadena
    );

    $cadena = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î', 'Ã­', 'Ã'),
        array('i', 'i', 'i', 'i', 'Í', 'Í', 'Í', 'Í', 'Í', 'Í'),
        $cadena
    );

    $cadena = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô', 'Ã³', 'Ã“', 'Ã“', 'oÍ', 'ÃƒÁ“'),
        array('ó', 'ó', 'ó', 'ó', 'Ó', 'Ó', 'Ó', 'Ó', 'ó', 'Ó', 'Ó', 'ó', 'Ó'),
        $cadena
    );

    $cadena = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü', 'Ãº', 'Ãš'),
        array('u', 'u', 'u', 'u', 'Ú', 'Ú', 'Ú', 'Ú', 'ú', 'Ú'),
        $cadena
    );

    $cadena = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç', 'Ã±', 'ÃƒÁ±', 'Ã‘'),
        array('n', 'Ñ', 'c', 'C', 'ñ', 'ñ', 'Ñ'),
        $cadena
    );
    return $cadena;
}

//CARGAMOS LOS NIVELES
$arrayTipos = array();

$queryT = mysqli_query($connect_valoracion, "SELECT * FROM Tipos 
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "'  ORDER BY id DESC ");
while ($dataT = mysqli_fetch_array($queryT)) {
    array_push($arrayTipos, array($dataT["id"], $dataT["nombre"]));
}

//CARGAMOS LOS NIVELES
$arrayNiveles = array();
$queryN = mysqli_query($connect_valoracion, "SELECT * FROM Niveles 
    WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ORDER BY id DESC ");
while ($dataN = mysqli_fetch_array($queryN)) {
    array_push($arrayNiveles, array($dataN["id"], $dataN["nombre"]));
}
?>

<?php
$querySM13 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 1 AND id_submenu = 3");
$dataSM13 = mysqli_fetch_array($querySM13);
$queryCicloVal = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id = '" . $_SESSION['ciclo'] . "' ");
$dataCicloVal = mysqli_fetch_array($queryCicloVal);
include("views/administrar/etiquetas.php");
?>
<?php echo $respuesta; ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #FFFFFF !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-users" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM13["nombre"]; ?> <?php echo $_SESSION["anio_ciclo"]; ?></b> Ciclo: <b><?php echo $dataCicloVal["nombre"]; ?></b></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="container-fluid">
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <div class="card">
                    <div class="card-body">
                        <table border="1" id="perfiles" class="display table" style="width:100%;">
                            <thead>
                                <tr>
                                    <th scope="col" style="width:50px">#</th>
                                    <th scope="col" style="max-width: 180px"><?php echo $etiquetaAdminCargo; ?></th>
                                    <th scope="col" style="max-width: 180px"><?php echo $etiquetaAdminVP; ?></th>
                                    <th scope="col" style="max-width: 180px"><?php echo $etiquetaAdminArea; ?></th>
                                    <th scope="col">Tipo de Competencias</th>
                                    <th scope="col">Competencias</th>
                                    <th scope="col">Nivel</th>
                                    <th scope="col">Año Lic.</th>
                                    <th scope="col" style="width: 90px; text-align:center">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script type="text/javascript">

    var api = '<?php echo $url; ?>api/competencias_pc/';
    $(document).ready(function() {
        $('#perfiles').DataTable({
            destroy: true,
            ajax: {
                url: api + "perfiles_cargos.php",
                type: "POST",
                data: function(d) {
                    d.id_empresa = '<?php echo $_SESSION["id_empresa"]; ?>';
                    d.url = '<?php echo $url; ?>';
                    d.ciclo = '<?php echo $_SESSION['ciclo']; ?>';
                    d.anio_ciclo = '<?php echo $_SESSION['anio_ciclo']; ?>';
                    console.log(d);
                },
                dataSrc: 'data'
            },
            columns: [{
                    data: "contador"
                },
                {
                    data: "cargo",
                    "width": "180px",
                },
                {
                    data: "vicepresidencia"
                },
                {
                    data: "area"
                },
                {
                    data: "tipo_competencia",
                    render: function(data, type, row) {
                        return data;
                    },
                },
                {
                    data: "competencia",
                    render: function(data, type, row) {
                        return data;
                    },
                },
                {
                    data: "nivel",
                    render: function(data, type, row) {
                        return data;
                    },
                },
                {
                    data: "anio",
                    render: function(data, type, row) {
                        return data;
                    },
                },
                {
                    data: "acciones",
                    render: function(data, type, row) {
                        return data;
                    },
                }
            ],
            createdRow: function(row, data, dataIndex) {
                $('td:eq(4)', row).css('font-size', '13px');
            },
            pageLength: 100,
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
            dom: '<"top"Blfp>rt<"bottom"lip><"clear">',
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
    $(document).ready(function() {

        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();

            $(".myTable").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
                $(this).next(".insumos").toggle($(this).text().toLowerCase().indexOf(value) > -1);
                //$(this).next(".insumos").toggle();
                //$(this).parent().next().hide();
            });

            $(".name_fil_off").parent().show();

        });

    });


    // var api = 'https://wandtalent.com/seleccion/superadmin/api/';

    // function Ficha_Competencia(id) {
    //     $('#lista_niveles').html('');
    //     jQuery.ajax({
    //             url: api + "ficha_competencia.php",
    //             type: 'post',
    //             data: {
    //                 id: id,
    //                 url: "?pg=competencias"
    //             },
    //         }).done(function(resp) {
    //             $("#xscript").html(resp);
    //         })
    //         .fail(function(resp) {
    //             console.log(resp);
    //         })
    //         .always(function(resp) {});
    // }

    function Seter_Ficha() {

        $('[name="nombre"]').val("");
        $('[name="definicion"]').val("");
        $('[name="id_tipo"]').val("");

        $('[name="id_competencia"]').val("");

    }
</script>

<style>
    .checkbox_list {
        width: 18px;
        height: 18px;
    }
</style>