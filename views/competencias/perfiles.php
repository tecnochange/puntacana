<script>
    $(document).ready(function() {
        $('#menuCompetencias').collapse();
        $('#bt_competencias_perfiles').addClass('active');
    });
</script>

<?php
include("app/models/competencias/Competencias.php");
$ClassCompetencias = new Competencias();

$dataCicloVal = $ClassCompetencias->Ciclo($user_log["id_empresa"], $_SESSION["anio_ciclo"]);

$hoy = date("Y-m-d H:i:s");

//CARGAMOS LOS NIVELES
$arrayTipos = array();

$queryT = mysqli_query($connect_valoracion, "SELECT * FROM Tipos 
    WHERE id_empresa = '" . $user_log['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "'  ORDER BY id DESC ");
while ($dataT = mysqli_fetch_array($queryT)) {
    array_push($arrayTipos, array($dataT["id"], $dataT["nombre"]));
}

//CARGAMOS LOS NIVELES
$arrayNiveles = array();
$queryN = mysqli_query($connect_valoracion, "SELECT * FROM Niveles 
    WHERE id_empresa = '" . $user_log['id_empresa'] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ORDER BY id DESC ");
while ($dataN = mysqli_fetch_array($queryN)) {
    array_push($arrayNiveles, array($dataN["id"], $dataN["nombre"]));
}

$querySM13 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $user_log["id_empresa"] . " AND estado = 1 AND id_menu = 1 AND id_submenu = 3");
$dataSM13 = mysqli_fetch_array($querySM13);

include("views/administrar/etiquetas.php");
?>

<style>
    /* Evita saltos de línea en los titulos de las columnas */
    #perfiles thead th {
        white-space: nowrap;
    }

    /* Margen debajo de la barra de herramientas (botones) */
    .dt-buttons {
        margin-bottom: 15px !important;
    }

    /* Margen debajo de la tabla (paginación) */
    .dataTables_paginate,
    .dataTables_info {
        margin-top: 15px !important;
    }
    .checkbox_list {
        width: 18px;
        height: 18px;
    }
</style>

<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <?php echo $respuesta; ?>

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Perfiles <?= $dataCicloVal["anio"]; ?> | <small>Ciclo: <?php echo $dataCicloVal["nombre"]; ?></small> </h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <div class="card">
                    <div class="card-body">
                        <table border="1" id="perfiles" class="display table" style="width:100%;">
                            <thead>
                                <tr>
                                    <th scope="col" style="width:50px">#</th>
                                    <th scope="col">Cargo</th>
                                    <th scope="col">Vicepresidencia</th>
                                    <th scope="col" >Área</th>
                                    <th scope="col">Tipo de Competencias</th>
                                    <th scope="col" style="max-width: 300px">Competencias</th>
                                    <th scope="col">Nivel</th>
                                    <th scope="col">Año Lic.</th>
                                    <th scope="col" style="width: 90px; text-align:center">Acciones</th>
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

    function Seter_Ficha() {
        $('[name="nombre"]').val("");
        $('[name="definicion"]').val("");
        $('[name="id_tipo"]').val("");
        $('[name="id_competencia"]').val("");
    }
</script>

<!-- CSS de DataTables + Botones -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<!-- JS de DataTables + Botones -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script>
    var api = '<?php echo $url; ?>api/competencias/';
	$(document).ready(function() {
		$('#perfiles').DataTable({
        destroy: true,
            ajax: {
                url: api + "perfiles_cargos.php",
                type: "POST",
                data: function(d) {
                    d.id_empresa = '<?php echo $user_log["id_empresa"]; ?>';
                    d.url = '<?php echo $url; ?>';
                    d.ciclo = '<?php echo $_SESSION['ciclo']; ?>';
                    d.anio_ciclo = '<?php echo $_SESSION['anio_ciclo']; ?>';
                    console.log(d);
                },
                //dataSrc: 'data'
                dataSrc: function(json) {
                    console.log(json);
                    if (!json || !json.data) {
                        console.error('Respuesta inválida de la API');
                        return [];
                    }
                    return json.data;
                }
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
                    className: "text-center",
                    render: function(data, type, row) {
                        return data;
                    },
                }
            ],
            createdRow: function(row, data, dataIndex) {
                $('td:eq(4)', row).css({'font-size': '13px', textAlign: 'center'});
            },
			pageLength: 50,
			language: {
				url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
			},
			dom: 'Bfrtip',
			buttons: [{
				extend: 'excelHtml5',
				text: 'Descargar Excel'
			}]
		});
	});
</script>