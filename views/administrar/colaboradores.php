<script>
    $(".menu_section").addClass("active");
    // $("#nav_empresa").addClass("active");
    jQuery("#menu_empresa").css("display", "none");
    $("#bt_admin_colaboradores").addClass("current-page");
</script>
<style>
    .card,
    .card-header,
    .card-body,
    .card-footer {
        background-color: #ffffff !important;
    }

    #iconCabecera {
        font-size: 24px;
        /* color: #007ae1; */
    }

    .formulario-fila {
        display: none;
    }
</style>

<?php

include("views/administrar/etiquetas.php");
include("views/administrar/colaborador/modal_colaborador.php");

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
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î', 'Ã­', 'Ã', 'Ã'),
        array('i', 'i', 'i', 'i', 'Í', 'Í', 'Í', 'Í', 'Í', 'Í', 'Í'),
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
$hoy = date("Y-m-d H:i:s");
if ($_POST["guardar_colaborador"] != "") {

    $queryVdl = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE documento = '" . $_POST["documento"] . "' AND id_empresa = '" . $_SESSION['id_empresa'] . "' AND id NOT IN (" . $_POST["id_colaborador"] . ")");
    $queryVd2 = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND correo = '" . $_POST['correo'] . "' AND id NOT IN (" . $_POST["id_colaborador"] . ")");

    if (mysqli_num_rows($queryVd1) > 0) {
        $respuesta = '
        <div class="alert alert-danger" role="alert">
            Lo sentimos, ya se encuentra un usuario creado con el documento ' . $_POST["documento"] . '
        </div>
        ';
    } else {
        if (mysqli_num_rows($queryVd2) > 0) {
            $respuesta = '
            <div class="alert alert-danger" role="alert">
                Lo sentimos, ya se encuentra un usuario creado con el correo ' . $_POST["correo"] . '
            </div>
            ';
        } else {
            $sentencia = "
            UPDATE  Empleados  SET
            documento = '" . $_POST["documento"] . "',
            nombre = '" . $_POST["nombre"] . "',
            fecha_ingreso = '" . $_POST["fecha_ingreso"] . "',
            antiguedad_anios = '" . $_POST["antiguedad_anios"] . "',
            antiguedad_meses = '" . $_POST["antiguedad_meses"] . "',
            antiguedad_dias = '" . $_POST["antiguedad_dias"] . "',
            id_cargo = '" . $_POST["id_cargo"] . "',
            cargo = '" . $cargo . "',
            correo = '" . $_POST["correo"] . "',
            nivel_jerarquico = '" . $_POST["nivel_jerarquico"] . "',
            unidad_corporativa = '" . $_POST["unidad_corporativa"] . "',
            area = '" . $_POST["area"] . "',
            role = '" . $_POST["role"] . "',
            estado = '" . $_POST["estado"] . "',
            password = '" . $_POST["password"] . "',
            verificar = '" . $_POST["verificar"] . "',
            updated_at = '" . $hoy . "'
            WHERE id = '" . $_POST["id_colaborador"] . "'
            ";

            // echo $sentencia;

            mysqli_query($connect_valentina, $sentencia);
            echo '<script> window.location = "?pg=administrar/colaboradores";</script>';
        }
    }
}

$_SESSION["id_colaborador_edit"] = "";
$querySM45 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 4 AND id_submenu = 24");
$dataSM45 = mysqli_fetch_array($querySM45);
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #FFFFFF !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-dice-d20" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM45["nombre"]; ?></h4>
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
                        <?php if ($_SESSION["role_plataforma"] == 1) { ?>

                            <a href="<?php echo $url; ?>?pg=administrar/colaborador/home">
                                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Crear Colaborador
                                </button>
                            </a>
                        <?php } ?>

                        <button type="button" id="sidebarCollapse" class="btn btn-info btn-sm" title="Descargar Excel" style="display: none">
                            <i class="fas fa-download"></i>
                        </button>


                    </td>
                </tr>
            </table>

        </div>
    </div>
    <br>
    <?php if ($respuesta != "") { ?>
        <div class="row">
            <div class="col-md-12">
                <?php echo $respuesta; ?>
            </div>
        </div>
        <br>
    <?php } ?>
    <div class="row">
        <div class="col-md-12">

            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" style="display: none">
                        <li class="nav-item">
                            <a href="<?php echo $url; ?>?pg=administrar/colaboradores" class="nav-link active"><?php echo $IDIOMA["estructura_colaboradores"]; ?> </a>
                        </li>

                        <li class="nav-item">
                            <a href="<?php echo $url; ?>?pg=administrar/hojas_vida" class="nav-link"><?php echo $IDIOMA["estructura_hojas_de_vida"]; ?></a>
                        </li>
                    </ul>
                    <div class="table-responsive">
                        <input type="hidden" name="csrf" value="<?php echo $_SESSION['token']; ?>">
                        <table border="1" id="colaboradores_list" class="display table" style="width:100%">
                            <thead class="thead-success">
                                <tr>
                                    <th scope="col" width="15">Documento</th>
                                    <th scope="col" width="100">Foto</th>
                                    <th scope="col">Nombres y Apellidos</th>
                                    <th scope="col">Correo</th>
                                    <th scope="col"><?php echo $etiquetaAdminCargo; ?></th>
                                    <th scope="col"><?php echo $etiquetaAdminArea; ?></th>
                                    <th scope="col"><?php echo $etiquetaAdminNJ; ?></th>
                                    <th scope="col">Compañia</th>
                                    <th scope="col">Rol</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Verificación</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tabla_lista">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


    </div>
</div>

<script type="text/javascript">
    var api = '<?php echo $url; ?>/api/administrar/';
    $(document).ready(function() {
        $('#colaboradores_list').DataTable({
            destroy: true,
            ajax: {
                url: api + "listar_colaboradores.php",
                type: "POST",
                data: function(d) {
                    d.id_empresa = '<?php echo $_SESSION["id_empresa"]; ?>';
                    d.url = '<?php echo $url; ?>';
                    //console.log(d); // Revisa los parámetros que se están enviando
                },
                dataSrc: 'data' // Corregido para usar 'data' como la clave de los datos
            },
            columns: [{
                    data: "documento"
                },
                {
                    data: "foto"
                },
                {
                    data: "nombre"
                },
                {
                    data: "correo"
                },
                {
                    data: "cargo"
                },
                {
                    data: "area"
                },
                {
                    data: "nivel_jerarquico"
                },
                {
                    data: "compania"
                },
                {
                    data: "role"
                },
                {
                    data: "estado"
                },
                {
                    data: "verificar"
                },
                {
                    data: "acciones"
                }
            ],
            columnDefs: [{
                    responsivePriority: 1,
                    targets: 0
                },
                {
                    responsivePriority: 2,
                    targets: -1
                }
            ],
            order: [
                [2, 'asc']
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
                loadingRecords: "Cargando listado de colaboradores...",
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
                            title: 'Colaboradores',
                            exportOptions: {
                                columns: [],
                                modifier: {
                                    page: 'all'
                                }
                            },
                            customize: function(csv) {
                                var BOM = "\uFEFF";
                                <?php
                                $query = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' ORDER BY nombre ASC  ");
                                $rows = [];
                                while ($data = mysqli_fetch_array($query)) {
                                    $queryCargo = mysqli_query($connect_valentina, "SELECT * FROM Cargos WHERE id = '" . $data["id_cargo"] . "'  AND id_empresa = " . $_SESSION["id_empresa"] . "");
                                    $dataCargo = mysqli_fetch_array($queryCargo);

                                    if ($data["unidad_corporativa"] > 0) {
                                        $queryArea = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id = '" . $data["unidad_corporativa"] . "' AND id_empresa = " . $_SESSION["id_empresa"] . " ");
                                        $dataArea = mysqli_fetch_array($queryArea);
                                        $unidad_corporativa = $dataArea["nombre"];
                                    } else {
                                        $unidad_corporativa = $data["unidad_corporativa"];
                                    }

                                    if ($data["area"] > 0) {
                                        $queryArea = mysqli_query($connect_valentina, "SELECT * FROM Areas WHERE id = '" . $data["area"] . "' AND id_empresa = " . $_SESSION["id_empresa"] . " ");
                                        $dataArea = mysqli_fetch_array($queryArea);
                                        $area = $dataArea["nombre"];
                                    } else {
                                        $area = $data["area"];
                                    }

                                    if ($data["unidad_organizativa"] > 0) {
                                        $queryEE = mysqli_query($connect_valentina, "SELECT * FROM Estructura_Empresa WHERE id = '" . $data["unidad_organizativa"] . "'  AND id_empresa = " . $_SESSION["id_empresa"] . "");
                                        $dataEE = mysqli_fetch_array($queryEE);
                                        $unidad_organizativa = $dataEE["unidad_organizativa"];
                                    } else {
                                        $unidad_organizativa = $data["unidad_organizativa"];
                                    }

                                    if ($data["nivel_jerarquico"] > 0) {
                                        $querynj = mysqli_query($connect_valentina, "SELECT * FROM Nivel_Jerarquico WHERE id = '" . $data["nivel_jerarquico"] . "' AND id_empresa = " . $_SESSION["id_empresa"] . "");
                                        $datanj = mysqli_fetch_array($querynj);
                                        $nj = $datanj["nombre"];
                                    } else {
                                        $nj = $data["nivel_jerarquico"];
                                    }

                                    if ($data["verificar"] === "" || $data["verificar"] === null) {
                                        $verificar = 1;
                                    } else {
                                        $verificar = $data["verificar"];
                                    }

                                    $rows[] = [
                                        $data["documento"],
                                        $data["nombre"],
                                        $data["genero"],
                                        $data["fecha_ingreso"],
                                        $data["correo"],
                                        $data["correo_personal"],
                                        $data["telefono_movil"],
                                        $data["telefono_fijo"],
                                        $data["compania"],
                                        $unidad_corporativa,
                                        $area,
                                        $unidad_organizativa,
                                        $nj,
                                        $dataCargo["nombre"],
                                        $data["role"],
                                        $data["estado"],
                                        $verificar
                                    ];
                                }
                                ?>
                                var dataExport = [
                                    ['Codigo / Documento (obligatorio)', 'Nombre completo (obligatorio)', 'Genero (Masculino/Femenino)', 'Fecha Ingreso', 'Correo empresarial (obligatorio)', 'Correo personal', 'Telefono movil', 'Telefono fijo', 'Sociedad - EMPRESA', 'Vicepresidencia / Gerencia / Primer Nivel Organizacional (obligatorio)', 'Area / Segundo Nivel Organizacional (obligatorio)', 'Unidad organizativa / Tercer Nivel organizacional', 'Nivel Jerarquico', 'Cargo (obligatorio)', 'Rol en plataforma(1= administrador, 2=jefe, 3=Usuario) (obligatorio)', 'Estado (1. Activo, 2. Inactivo)', 'Verificado (1. No verificado, 2. Pendiente, 3. Verificado)'],
                                    <?php foreach ($rows as $index => $row) { ?>[
                                            '<?php echo $row[0]; ?>',
                                            '<?php echo $row[1]; ?>',
                                            '<?php echo $row[2]; ?>',
                                            '<?php echo $row[3]; ?>',
                                            '<?php echo $row[4]; ?>',
                                            '<?php echo $row[5]; ?>',
                                            '<?php echo $row[6]; ?>',
                                            '<?php echo $row[7]; ?>',
                                            '<?php echo $row[8]; ?>',
                                            '<?php echo $row[9]; ?>',
                                            '<?php echo $row[10]; ?>',
                                            '<?php echo $row[11]; ?>',
                                            '<?php echo $row[12]; ?>',
                                            '<?php echo $row[13]; ?>',
                                            '<?php echo $row[14]; ?>',
                                            '<?php echo $row[15]; ?>',
                                            '<?php echo $row[16]; ?>'
                                        ] <?php if ($index < count($rows) - 1) { ?>, <?php } ?>
                                    <?php } ?>
                                ];

                                dataExport = dataExport.filter(function(row) {
                                    return row.some(function(cell) {
                                        return cell && cell.trim() !== "";
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

    });

    $(document).ready(function() {

        $("#buscador").on("keyup", function() {
            var value = $(this).val().toLowerCase();

            $("#tabla_lista tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });

    });
</script>

<script>
    var api = '<?php echo $url; ?>/api/administrar/';

    function validarImagen() {
        var fileSize = $('#foto_perfil')[0].files[0].size;
        console.log(fileSize);

        var siezekiloByte = parseInt(fileSize / 1024);
        if (siezekiloByte > 1000) {
            alert("La fotografía es demasiado grande. por favor reduzca su tamaño a menos de 1 mega.");
            $('#foto_perfil').val("");
            return false;
        }
    }

    function CargarDepartamento(id_pais) {
        jQuery.ajax({
                url: api + "departamentos_admin.php",
                type: 'post',
                data: {
                    id_pais: id_pais
                },
            }).done(function(resp) {
                $("#departamento_expide").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }


    function CargarCiudades(id_dep) {
        jQuery.ajax({
                url: api + "ciudades_admin.php",
                type: 'post',
                data: {
                    id_dep: id_dep
                },
            }).done(function(resp) {
                $("#ciudad_expide").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function ValidarEstado(estado) {
        if (estado == 3) {
            $(".modal_retiro").modal("show");
        }
    }

    function Ver_Ficha_Movimientos() {
        $(".modal_movimiento").modal("show");
    }


    function AgregarObservacion() {
        $("#fecha_retiro").val($("#ficha_fecha_retiro").val());
        $("#motivo_retiro").val($("#ficha_motivo_retiro").val());
        $("#observacion").val($("#ficha_observacion").val());
    }

    function AgregarMovimiento() {
        $("#tipo_movimiento").val($("#ficha_tipo_movimiento").val());
        $("#fecha_movimiento").val($("#ficha_fecha_movimiento").val());
        $("#observacion_movimiento").val($("#ficha_observacion_movimiento").val());
    }

    function Quitar_elemento() {
        $("#foto").html('<input type="file" class="form-control" name="foto" accept="image/*" />');
    }

    var activar = false;

    function EliminarColaborador(id, id_empresa, id_user) {
        var confirmado = $("#modal_body").data("confirmado") || false;

        if (!confirmado) {
            // Mostrar el modal de confirmación
            $("#modal_general").modal("show");
            $("#modal_body").html(
                'Está a punto de eliminar un colaborador. ESTA ACCIÓN ES IRREVERSIBLE. Se perderán los datos. ¿Está seguro?<br><br>'
            );
            $("#modal_body").append(
                '<button type="button" class="btn btn-danger btn-sm" id="confirmar_eliminar_colaborador">Eliminar</button>'
            );

            // Marcar como no confirmado
            $("#modal_body").data("confirmado", false);

            // Agregar evento al botón de confirmación
            $("#confirmar_eliminar_colaborador").off("click").on("click", function() {
                $("#modal_body").data("confirmado", true); // Marcar como confirmado
                EliminarColaborador(id, id_empresa, id_user); // Llamar de nuevo a la función
            });
        } else {
            // Realizar la solicitud AJAX para eliminar
            jQuery.ajax({
                url: api + "eliminar_colaborador.php",
                type: "post",
                data: {
                    id: id,
                    id_empresa: id_empresa,
                    id_user: id_user,
                    url: "?pg=administrar/colaboradores"
                },
                success: function(resp) {
                    toastr.success("Colaborador eliminado correctamente", "¡Éxito!");
                    $('#colaboradores_list').DataTable().ajax.reload();
                    $("#modal_general").modal("hide");
                },
                error: function(xhr, status, error) {
                    toastr.error("Hubo un problema al eliminar el colaborador. Intente nuevamente.");
                }
            });

            // Reiniciar el estado de confirmación
            $("#modal_body").data("confirmado", false);
        }
    }

    function select_vicepresidencia() {

        $("#unidad_corporativa option:selected").each(function() {
            var empresa = $("#id_empresa").val();
            var area = $("#id_area").val();
            id = $(this).val();
            // console.log(id);
            $.post(api + "cargar_vicepresidencias.php", {
                id: id,
                id_empresa: empresa,
                area: area
            }, function(data) {
                $("#area").html(data);
                var foption = $('#area option:first');
                var soptions = $('#area option:not(:first)').sort(function(a, b) {
                    return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
                });
                $('#area').html(soptions).prepend(foption);
            });
        });

    }

    function select_area() {
        $("#area option:selected").each(function() {
            var empresa = $("#id_empresa").val();
            var vicepresidencia = $("#unidad_corporativa").val();
            var area = $("#id_area").val();
            var idVicepresidencia = $("#id_vicepresidencia").val();
            var idUnidadOrg = $("#id_UnidadOrg").val();
            id = $(this).val();
            // console.log(id);
            $.post(api + "cargar_unidad_organizativa.php", {
                id: id,
                id_empresa: empresa,
                area: area,
                vicepresidencia: vicepresidencia,
                idVicepresidencia: idVicepresidencia,
                idUnidadOrg: idUnidadOrg
            }, function(data) {

                $("#unidad_organizativa").html(data);
                var foption = $('#unidad_organizativa option:first');
                var soptions = $('#unidad_organizativa option:not(:first)').sort(function(a, b) {
                    return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
                });
                $('#unidad_organizativa').html(soptions).prepend(foption);
            });
        });
    }

    function calcularAntiguedad() {
        const fechaInput = document.getElementById('fecha_ingreso').value;
        const fechaSeleccionada = new Date(fechaInput + 'T00:00:00');
        const fechaActual = new Date();
        const fechaBaseUTC = new Date(fechaActual.toISOString());

        // Asegurarse de que la fecha seleccionada no esté en el futuro
        if (fechaSeleccionada > fechaBaseUTC) {
            alert("La fecha seleccionada no puede ser futura.");
            return;
        }

        let anios = fechaBaseUTC.getFullYear() - fechaSeleccionada.getFullYear();
        let meses = fechaBaseUTC.getMonth() - fechaSeleccionada.getMonth();
        let dias = fechaBaseUTC.getDate() - fechaSeleccionada.getDate();

        if (meses < 0) {
            anios--;
            meses += 12;
        }

        if (dias < 0) {
            meses--;
            const ultimoDiaDelMes = new Date(fechaBaseUTC.getFullYear(), fechaBaseUTC.getMonth(), 0).getDate();
            dias += ultimoDiaDelMes;
        } else {
            if (dias == 0) {
                dias = 0;
            }
        }



        document.getElementById('antiguedad').value = `${anios} años / ${meses} meses / ${dias} días`;

        document.getElementById('antiguedad_anios').value = anios;
        document.getElementById('antiguedad_meses').value = meses;
        document.getElementById('antiguedad_dias').value = dias;
    }

    function calcularFecha() {
        const anios = parseInt(document.getElementById('antiguedad_anios').value) || 0;
        const meses = parseInt(document.getElementById('antiguedad_meses').value) || 0;
        const dias = parseInt(document.getElementById('antiguedad_dias').value) || 0;

        if (anios === 0 && meses === 0 && dias === 0) {
            document.getElementById('fecha_ingreso').value = '';
            return;
        }

        const fechaBase = new Date();
        const fechaBaseUTC = new Date(fechaBase.toISOString());

        fechaBaseUTC.setFullYear(fechaBaseUTC.getFullYear() - anios);
        fechaBaseUTC.setMonth(fechaBaseUTC.getMonth() - meses);

        const diaBase = fechaBaseUTC.getDate();

        let diasRestados = dias;
        if (dias > diaBase) {
            diasRestados = dias - 1; // Restamos un día adicional
        }

        // Restar los días
        fechaBaseUTC.setDate(fechaBaseUTC.getDate() - diasRestados);

        const diaCalculado = fechaBaseUTC.getDate();
        console.log(fechaBaseUTC);

        if (fechaBaseUTC.getDate() !== diaCalculado) {
            const ultimoDiaDelMes = new Date(fechaBaseUTC.getFullYear(), fechaBaseUTC.getMonth(), 0).getDate();
            fechaBaseUTC.setDate(ultimoDiaDelMes);
        }

        const dia = fechaBaseUTC.getDate().toString().padStart(2, '0');
        const mes = (fechaBaseUTC.getMonth() + 1).toString().padStart(2, '0');
        const anio = fechaBaseUTC.getFullYear();

        const fechaCalculada = `${anio}-${mes}-${dia}`;

        document.getElementById('fecha_ingreso').value = fechaCalculada;
    }

    window.onload = calcularFecha;

    function EditarColaborador(id, id_empresa) {
        jQuery.ajax({
                url: api + "editar_colaborador.php",
                type: 'post',
                data: {
                    id: id,
                    id_empresa: id_empresa,
                },
            }).done(function(resp) {
                $("#modal_colaborador").modal("show");
                $("#modal_contenido").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }
</script>