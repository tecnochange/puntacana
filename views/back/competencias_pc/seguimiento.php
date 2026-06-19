<script>
    $(document).ready(function() {
        $(".menu_section").addClass("active");
        $("#nav_competencias").addClass("active");
        jQuery("#menu_competencias").css("display", "none");
        $("#bt_comp_seguimiento").addClass("current-page");
    });
</script>

<?php
$queryCicloVal = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id = '" . $_SESSION['ciclo'] . "' ");
$dataCicloVal = mysqli_fetch_array($queryCicloVal);
include("views/competencias_pc/informes/funciones.php");
?>

<?php

$terminadas = 0;
$en_proceso = 0;
$pendientes = 0;
$total = 0;
$TABLA_GENERAL = "";

// include("app/models/Collaborators.php");
// $ClassCollaborators = new Collaborators();
// $colaboradores =  $ClassCollaborators->collaborators_list($_POST, $connect_valentina);

$filtro_eval = " AND tipo IN (1,5)";

if ($_POST["tipo_evaluacion"] != "") {
    $_SESSION["tipo_evaluacion"] = $_POST["tipo_evaluacion"];
    $filtro_eval .= " AND tipo = " . $_POST["tipo_evaluacion"] . " ";
} else {
    $_SESSION["tipo_evaluacion"] = "";
}

if ($_POST["proceso_valoracion"] != "") {
    $_SESSION["proceso_valoracion"] = $_POST["proceso_valoracion"];
    $filtro_eval .= " AND tipo = " . $_POST["proceso_valoracion"] . " ";
} else {
    $_SESSION["proceso_valoracion"] = "";
}

$filtrosVP = $filtrosArea = " ";

$queryRL = mysqli_query($connect_valentina, "SELECT * FROM Relaciones_Laborales WHERE id_empleado = '" . $_SESSION["id_user"] . "' AND estado = 1 AND mod_competencias = 'on'");
if (mysqli_num_rows($queryRL) > 0) {
    $vp = $area = '';
    while ($dataRL = mysqli_fetch_array($queryRL)) {
        if ($dataRL["id_vp"] != '') {
            $vp .= $dataRL["id_vp"] . ',';
        }
        if ($dataRL["id_area"] != '') {
            $area .= $dataRL["id_area"] . ',';
        }
    }
    if ($vp != '') {
        $vpList = rtrim($vp, ",");
        $filtrosVP = "AND Empleados.unidad_corporativa IN ($vpList)";
    }
    if ($area != '') {
        $areaList = rtrim($area, ",");
        $filtrosArea = "AND Empleados.area IN ($areaList)";
    }
}


$querySM19 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 1 AND id_submenu = 9");
$dataSM19 = mysqli_fetch_array($querySM19);
?>

<style>
    .bg-goforagile {
        background-color: #59008e !important;
    }

    .bg-proceso {
        background-color: #f76d6b !important;
    }

    .bg-proceso1 {
        background-color: #7f38c2 !important;
    }

    .bg-proceso2 {
        background-color: #748eff !important;
    }

    .bg-proceso3 {
        background-color: #fcdb58 !important;
    }

    .bg-proceso4 {
        background-color: #59008e !important;
    }

    .bg-proceso5 {
        background-color: #5cdc53 !important;
    }

    .bg-proceso6 {
        background-color: #fb924e !important;
    }

    .bg-proceso7 {
        background-color: #64f456 !important;
    }

    .small-box {
        border-radius: .25rem;
        box-shadow: 0 0 1px rgba(0, 0, 0, .125), 0 1px 3px rgba(0, 0, 0, .2);
        display: block;
        margin-bottom: 20px;
        position: relative;
        color: white !important;
    }

    .small-box>.inner {
        padding: 10px;
    }

    .small-box .icon {
        color: rgba(0, 0, 0, .15);
        z-index: 0;
    }

    .col-lg-2 .small-box h3,
    .col-md-2 .small-box h3,
    .col-xl-2 .small-box h3 {
        font-size: 2.2rem;
        color: black;
    }

    .small-box p {
        font-size: 1rem;
        color: black;
    }

    .small-box>.small-box-footer {
        background-color: rgba(0, 0, 0, .1);
        color: rgba(255, 255, 255, .8);
        display: block;
        padding: 3px 0;
        position: relative;
        text-align: center;
        text-decoration: none;
        z-index: 10;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #FFFFFF !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-users" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM19["nombre"]; ?> <?php echo $_SESSION["anio_ciclo"]; ?></b> Ciclo: <b><?php echo $dataCicloVal["nombre"]; ?></b></h4>
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
                    <?php if ($user_log["role"] == 1) { ?>
                        <td width="130">
                            <form action="<?php echo $url; ?>?pg=competencias_pc/send" method="get">
                                <button type="submit" class="btn btn-danger">Comunicados</button>
                            </form>
                        </td>
                    <?php } ?>
                </tr>
            </table>

        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-pills justify-content-center" style="margin-bottom: 10px;">
                        <li class="nav-item">
                            <a class="nav-link active " href="?pg=competencias_pc/seguimiento">Seguimiento Valoración</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link " href="?pg=competencias_pc/formularios">Formularios Valoración</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12" style="text-align: end;">
                            <h3 id="valoracionesCount">0 Valoraciones</h3>
                        </div>
                        <div class="row" id="procesosContainer">
                            <!-- Las demás secciones se generarán dinámicamente -->
                        </div>
                    </div>

                    <br>
                    <div class="row">
                        <div class="col-12">
                            <form action="" method="post" class="row">
                                <!-- Tipo Evaluación -->
                                <div class="form-group col-md-4">
                                    <label for="tipo_evaluacion">Tipo de Evaluación</label>
                                    <select name="tipo_evaluacion" id="tipo_evaluacion" class="form-control multiples_responsables">
                                        <option value="">Seleccione</option>
                                        <option value="1" <?php if (isset($_POST['tipo_evaluacion']) && $_POST['tipo_evaluacion'] == 1) echo 'selected'; ?>>Auto</option>
                                        <option value="5" <?php if (isset($_POST['tipo_evaluacion']) && $_POST['tipo_evaluacion'] == 5) echo 'selected'; ?>>Jefe</option>
                                    </select>
                                </div>
                                <!-- Vicepresidencias -->
                                <div class="form-group col-md-4">
                                    <label for="vicepresidencia">Vicepresidencia</label>
                                    <select name="vicepresidencia" id="vicepresidencia" class="form-control multiples_responsables">
                                        <option value="">Seleccione</option>
                                    </select>
                                </div>
                                <!-- Bóton de filtrar -->
                                <div class="form-group col-md-2 align-self-end d-flex align-items-center">
                                    <button id="btnFiltrar" type="button" class="btn btn-success btn-block">Filtrar</button>
                                    <div id="loaderv2" style="display:none; vertical-align: middle; margin-left: 8px;">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Cargando...</span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="table-responsive">
                        <table border="1" id="seguimiento" class="display table" style="width:100%;">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Doc. Evaluado</th>
                                    <th>Evaluado</th>
                                    <th>Cargo</th>
                                    <th>Vicepresidencia</th>
                                    <th>Área</th>
                                    <th>Doc. Evaluador</th>
                                    <th>Evaluador</th>
                                    <th>Tipo evaluación</th>
                                    <th>Proceso valoración</th>
                                    <th>Resultado</th>
                                    <th>Reporte</th>
                                </tr>
                            </thead>
                            <tbody class="tabla_lista">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .fichas {
        font-size: 20px;
        padding: 10px;
        border-radius: 15px;
    }

    .card,
    .card-body,
    .card-header,
    .card-footer {
        background-color: #FFFFFF !important;
    }

    table#seguimiento span.proceso-valoracion {
        background-color: var(--bg-color, #eee);
        color: var(--text-color, #000);
        padding: 6px 12px;
        font-weight: 600;
        font-size: 1rem;
        border-radius: 999px;
    }
</style>

<script type="text/javascript">
    $(document).ready(function() {
        $('.multiples_responsables').select2();
        $("#buscador").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".tabla_lista tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

        /** Cargar Vicepresidencias */
        let controller = '<?php echo $url; ?>app/controllers/Empresa/';
        //Cargar datos de Vicepresidencias
        $.ajax({
            url: controller + "VicepresidenciasController.php",
            method: 'post',
            success: function(data) {
                let select = $('#vicepresidencia');
                select.html('<option value="">Seleccione</option>');

                data.forEach(function(vp) {
                    let selected = '<?php echo isset($_POST["vicepresidencia"]) ? $_POST["vicepresidencia"] : ""; ?>';
                    let isSelected = selected == vp.id ? 'selected' : '';
                    select.append(`<option value="${vp.id}" ${isSelected}>${vp.nombre}</option>`);
                });

                // Reestablecer el valor seleccionado
                let savedVP = localStorage.getItem('vicepresidencia');
                if (savedVP) {
                    select.val(savedVP).trigger('change');
                }
            },
            error: function() {
                console.error('No se puedieron cargar las vicepresidencias');
            }
        });

        $('#vicepresidencia').on('change', function() {
            localStorage.setItem('vicepresidencia', $(this).val());
        });

        let tabla = $('#seguimiento').DataTable({
            destroy: true,
            ajax: {
                url: api + "seguimiento.php",
                type: "POST",
                data: function(d) {
                    d.id_empresa = '<?php echo $_SESSION["id_empresa"]; ?>';
                    d.url = '<?php echo $url; ?>';
                    d.ciclo = '<?php echo $_SESSION['ciclo']; ?>';
                    d.anio_ciclo = '<?php echo $_SESSION['anio_ciclo']; ?>';
                    d.filtro_eval = '<?php echo $filtro_eval; ?>';
                    d.filtro_vp = '<?php echo $filtrosVP ?>';
                    d.filtro_area = '<?php echo $filtrosArea; ?>';
                    d.tipo_evaluacion = $('#tipo_evaluacion').val();
                    d.vicepresidencia = $('#vicepresidencia').val();
                    d.identidad = '<?php echo $_SESSION["id_user"]; ?>';
                    d.rol = '<?php echo $_SESSION['role_plataforma']; ?>';
                },
                beforeSend: function() {
                    $('#loader').show(); // Mostrar loader
                },
                complete: function() {
                    $('#loader').hide(); // Ocultar loader cuando termine
                },
                error: function() {
                    $('#loader').hide();
                    alert("Ocurrió un error al cargar los datos.");
                },
                dataSrc: 'data'
            },
            serverSide: false, // ¡Esto es lo importante!
            paging: true,
            searching: true,
            ordering: true,
            columns: [{
                    data: "contador"
                },
                {
                    data: "documento"
                },
                {
                    data: "evaluado"
                },
                {
                    data: "cargo"
                },
                {
                    data: "vicepresidencia"
                },
                {
                    data: "area"
                },
                {
                    data: "documento_evaluador"
                },
                {
                    data: "evaluador"
                },
                {
                    data: "tipo",
                    render: function(data, type, row) {
                        let color = "#000000"; // color por defecto (negro)

                        if (data?.toLowerCase() === "auto") {
                            color = "#6f42c1"; // morado
                        } else if (data?.toLowerCase() === "jefe") {
                            color = "#28a745"; // verde
                        }

                        return `<p style="color: ${color}!important; margin: 0;">${data}</p>`;
                    }
                },
                {
                    data: "proceso_valoracion",
                    render: function(data, type, row) {
                        if (!data || !data.estado) return '';

                        return `<span
                        class="badge rounded-pill"
                            style="
                                background-color: ${data.background};
                                color: ${data.color} !important;
                                padding: 6px 12px;
                                font-weight: 500;
                                font-size: 0.8rem;
                                border-radius: 0.3rem !important;
                                display: inline-block;
                                text-align: center;
                                min-width: 90px;
                                ">
                                ${data.estado}
                                </span>`;
                    },
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).css({
                            'vertical-align': 'middle'
                        });
                    }
                },
                {
                    data: "promedio",
                   render: function (data, type, row) {
                        let porcentaje = parseFloat(data).toFixed(2); // Redondear
                        let color = 'bg-success';
                        let textColor = 'text-white';

                        if (porcentaje == 0) {
                            color = '';
                            textColor = 'text-dark';
                        } else if (porcentaje < 60) {
                            color = 'bg-danger';
                        } else if (porcentaje < 80) {
                            color = 'bg-warning text-dark';
                            textColor = 'text-dark';
                        }

                        return `
                            <div class="progress" style="height: 22px; position: relative;">
                                <div class="progress-bar ${color} ${textColor}" role="progressbar"
                                    style="width: ${porcentaje}%; display: flex; align-items: center; justify-content: center;"
                                    aria-valuenow="${porcentaje}" aria-valuemin="0" aria-valuemax="100">
                                    ${porcentaje}%
                                </div>
                                ${porcentaje == 0 ? `<span class="position-absolute w-100 text-center text-dark" style="font-size: 0.9rem;">${porcentaje}%</span>` : ''}
                            </div>`;
                    },
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).css({
                            'vertical-align': 'middle'
                        });
                    }
                },
                {
                    data: "reporte",
                    render: function(data, type, row) {
                        return data;
                    },
                }
            ],
            responsive: true,
            pageLength: 10,
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros.",
                info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                infoEmpty: "Mostrando registros del 0 al 0 de 0 registros",
                infoFiltered: "(filtrado de un total de _MAX_ registros)",
                loadingRecords: '<div id="loader" style="display: block; text-align: center; margin-top: 20px;">' +
                    '<div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>' +
                    '<p style="margin-top: 10px;">Cargando datos, por favor espera...</p>' +
                    '</div>',
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último"
                }
            },
            dom: '<"top"Bf>rt<"bottom"lip><"clear">',
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
        }).on('xhr', function(e, settings, json, xhr) {
            if (json && json.data) {
                const totalValoraciones = json.countValoracion ?? json.data.length;
                $('#valoracionesCount').text(`${totalValoraciones} Valoraciones`);

                let procesosHtml = '';

                // Convertimos arrayTotalProceso en mapa con strings como claves
                const totalPorProceso = json.arrayTotalProceso ?? {};

                // Si no existe explícitamente el proceso "0", lo agregamos con lo que viene en sumProceso o con la key vacía ("")
                const sinValoracionCantidad = totalPorProceso["0"] ?? totalPorProceso[""] ?? json.sumProceso ?? 0;

                // Recorrer los procesos desde el array definido
                const procesosDefinidos = json.Array_Proceso_Valoracion ?? [];

                // Bandera para verificar si "Sin Valoración" está definido en el array
                let sinValoracionDefinido = false;

                // Si "Sin Valoración" no está definido en el array, lo agregamos manualmente
                if (!sinValoracionDefinido) {
                    const porcentaje = totalValoraciones > 0 ? ((sinValoracionCantidad / totalValoraciones) * 100).toFixed(2) : '0.00';
                    procesosHtml += `
                    <div class="col-md-3 col-6 proceso-dinamico">
                    <div class="small-box" style="background-color: #f76d6b !important;">
                    <div class="inner">
                    <h3 style="color: white !important;">${porcentaje}%</h3>
                    <p style="color: white !important;">Sin Valoración (${sinValoracionCantidad})</p>
                    </div>
                    </div>
                    </div>
                    `;
                }
                procesosDefinidos.forEach(proceso => {
                    const id = proceso[0];
                    const nombre = proceso[1];
                    const bgColor = proceso[2];
                    const textColor = proceso[3];

                    const cantidad = totalPorProceso[id] || 0;
                    const porcentaje = totalValoraciones > 0 ? ((cantidad / totalValoraciones) * 100).toFixed(2) : '0.00';

                    if (id === "0") sinValoracionDefinido = true;
                    if(id !== "5"){ //Omitir la tarjeta de Firma Aprobación
                        procesosHtml += `
                        <div class="col-md-3 col-6 proceso-dinamico">
                        <div class="small-box" style="background-color: ${bgColor} !important;">
                        <div class="inner">
                        <h3 style="color: ${textColor} !important;">${porcentaje}%</h3>
                        <p style="color: ${textColor} !important;">${nombre} (${cantidad})</p>
                        </div>
                        </div>
                        </div>
                        `;
                    }
                });


                // Elimina solo los procesos dinámicos anteriores y agrega los nuevos
                $('#procesosContainer .proceso-dinamico').remove();
                $('#procesosContainer').append(procesosHtml);

                // También puedes mantener el elemento adicional si deseas mostrarlo aparte
                $('#sinValoracionPorcentaje').text(`${Math.round((sinValoracionCantidad / totalValoraciones) * 100)}%`);
                $('#sinValoracionTexto').text(`Sin Valoración (${sinValoracionCantidad})`);
            }
        });

        // Evento de Filtrar datos por selects
        $('#btnFiltrar').on('click', function() {
            $('#loaderv2').show(); // mostrar loader al iniciar
            tabla.ajax.reload(null, false); // recarga tabla

            // Ocultar loader cuando termine la petición ajax
            tabla.on('xhr', function() {
                $('#loaderv2').hide();
            });
        });
    });

    var api = '<?php echo $url; ?>api/competencias_pc/';

    var activar = false;

    function Reactivar_Evaluacion(id) {
        if (activar == false) {
            $("#modal_general").modal("show");
            $("#cont_modal_general").html('Está a punto de reactivar este formulario, esta acción es irreversible ¿está seguro?<br><br>');
            $("#botones_modal_general").html('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Reactivar_Evaluacion(' + id + ')"> Reactivar Formulario </button>');
        } else {

            jQuery.ajax({
                    url: api + "reactivar_formulario_new.php",
                    type: 'post',
                    data: {
                        id: id,
                        url: "?pg=competencias_pc/seguimiento"
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


    function Elinar_Evaluacion(id, id_evaluado, id_evaluador, tipo) {
        if (activar == false) {
            $("#modal_general").modal("show");
            $("#cont_modal_general").html('Está a punto de borrar este formulario, esta acción es irreversible ¿está seguro?<br><br>');
            $("#botones_modal_general").html('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Elinar_Evaluacion(' + id + ', ' + id_evaluado + ', ' + id_evaluador + ', ' + tipo + ' )"> Eliminar Formulario </button>');
        } else {

            jQuery.ajax({
                    url: api + "eliminar_formulario_new.php",
                    type: 'post',
                    data: {
                        id: id,
                        id_evaluado: id_evaluado,
                        id_evaluador: id_evaluador,
                        id_tipo: tipo,
                        url: "?pg=competencias_pc/seguimiento"
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