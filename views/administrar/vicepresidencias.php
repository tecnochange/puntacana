<?php
include("views/administrar/etiquetas.php");
include("views/okrs/layouts/modal_profile.php");
$_SESSION["id_cargo_edit"] = "";
$querySM411 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 4 AND id_submenu = 30");
$dataSM411 = mysqli_fetch_array($querySM411);
?>
<style>
    #profileOkr {
    display: contents !important;
}
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #FFFFFF !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h4><i class="fas fa-dice-d20" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM411["nombre"]; ?></h4>
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
                        <?php if ($dtEmpleado["role"] == 1) { ?>
                            <a href="<?php echo $url; ?>?pg=administrar/vicepresidencia/detalle">
                                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm">
                                    <i class="fas fa-plus"></i> Crear <?php echo $etiquetaAdminVP; ?>
                                </button>
                            </a>

                            <button type="button" id="sidebarCollapse" class="btn btn-warning btn-sm" style="display: none">
                                <i class="fas fa-plus"></i> <?php echo $IDIOMA["estructura_vicepresidencias_cargar_csv"]; ?>
                            </button>
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
    <div class="row">
        <div class="col-md-12">


            <ul class="nav nav-tabs" style="display: none">
                <li class="nav-item">
                    <a href="<?php echo $url; ?>?pg=administrar/vicepresidencias" class="nav-link active"><?php echo $IDIOMA["estructura_vicepresidencias_inventario_vicepresidencias"]; ?></a>
                </li>

                <li class="nav-item">
                    <a href="<?php echo $url; ?>?pg=administrar/vicepresidencias_estructura" class="nav-link"><?php echo $IDIOMA["estructura_vicepresidencias_estructura_vicepresidencias"]; ?></a>
                </li>
            </ul>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <div class="card">
                    <div class="card-body">

                        <table border="1" id="vicepresidencias" class="display table" style="width:100%;">
                            <thead class="thead-success">
                                <tr>
                                    <th scope="col" width="15">#</th>
                                    <th scope="col" width="150">Nombre</th>
                                    <th scope="col" width="150">Lider</th>
                                    <th scope="col" width="150">Estado</th>
                                    <th scope="col" width="120" style="text-align: end;">Acciones</th>
                                </tr>
                            </thead>

                            <tbody id="tabla_lista">
                                <?php
                                $count = 1;
                                $query = mysqli_query($connect_valentina, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' ORDER BY nombre ASC ");
                                while ($data = mysqli_fetch_array($query)) {

                                    $bt_editar = '';
                                    if ($dtEmpleado["role"] == 1) {
                                        $bt_editar = '
                                <a href="' . $url . '?pg=administrar/vicepresidencia/detalle&id=' . $data["id"] . '">
                                <button type="button" id="sidebarCollapse" class="btn btn-success btn-sm" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                </a>
                                <button type="button" id="sidebarCollapse" class="btn btn-danger btn-sm" title="Quitar Vicepresidencia" style="font-size: 12px; padding: 3px 5px;" onclick="Eliminar_VP(' . $data["id"] . ')">
                                    <i class="bx bx-trash"></i>
                                </button>
                            ';
                                    }
                                    $listado_lideres = '';

                                    $queryLideres = mysqli_query($connect_valentina, "SELECT * FROM Lideres_Vicepresidencia WHERE id_vicepresidencia = '" . $data["id"] . "'");

                                    if(mysqli_num_rows($queryLideres) > 0){
                                        while ($dataLideres = mysqli_fetch_array($queryLideres)) {
                                            $queryEmple = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '" . $dataLideres["id_lider"] . "' ");
                                            $dataEmple = mysqli_fetch_array($queryEmple);
    
                                            if (!$dataEmple["foto"]) {
                                                $dataEmple["foto"] = "img_default.jpg";
                                            }
                                            $listado_lideres .= '<a data-bs-toggle="tooltip" href="javascript:Profile(' . $dataLideres["id_lider"] . ',1)" class="dropdown-item" id="profileOkr"><img data-src="' . $url . '/recursos/' . $dataEmple["foto"] . '" class="lazyload foto_min" title="' . $dataEmple["nombre"] . '" style="width: 35px !important;height: 35px !important;"></a>';
                                        }
                                    }else{
                                        $listado_lideres = 'Sin Asignar';
                                    }
                                    
                                    if ($data["estado"] == 1) {
                                        $estado = 'ACTIVO';
                                    } else {
                                        $estado = 'INACTIVO';
                                    }

                                    /*
						<a href="'.$url.'?pg=administrar/vicepresidencias/descriptivos&id='.$data["id"].'">
                                <button type="button" id="sidebarCollapse" class="btn btn-info btn-sm" title="Descriptivo">
                                    <i class="fas fa-plus"></i>
                                </button>
                                </a>
                                
                                <a href="'.$url.'?pg=administrar/vicepresidencias/onboarding&id='.$data["id"].'">
                                <button type="button" id="sidebarCollapse" class="btn btn-primary btn-sm" title="Onboarding">
                                    <i class="fas fa-users"></i>
                                </button>
                                </a>
						*/


                                    echo '
                        <tr style="vertical-align: middle;">
                            <td>' . $count . '</td>
                            <td>' . $data["nombre"] . '</td>
                            <td>' . $listado_lideres . '</td>
                            <td>' . $estado . '</td>                           
                            
                            <td style="text-align: end;">
                                ' . $bt_editar . '
                                
                                
                            </td>
                        </tr>
                        
                        ';
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
        $('#vicepresidencias').DataTable({
            autoWidth: false,
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
    var api_okrs = '<?php echo $url; ?>api/okrs/';
    function Profile(id, val) {
		jQuery.ajax({
				url: api_okrs + "profile_empleado.php",
				type: 'post',
				data: {
					id: id,
					val: val
				},
			}).done(function(resp) {
				$("#modal_profile").modal("show");
				$("#modal_contenidos").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});
	}
    var api = '<?php echo $url; ?>/api/administrar/';
    var activar = false;

    function Eliminar_VP(id) {

        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar un <?php echo $etiquetaAdminVP; ?>, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Eliminar_VP(' + id + ')"> Confirmar </button>');

        } else {

            jQuery.ajax({
                    url: api + "eliminar_vicepresidencia.php",
                    type: 'post',
                    data: {
                        id: id,
                        id_empresa: <?php echo $_SESSION["id_empresa"]; ?>,
                        id_user: <?php echo $_SESSION["id_user"]; ?>,
                        url: "?pg=administrar/vicepresidencias"
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
<script>
    $(".menu_section").addClass("active");
    // $("#nav_empresa").addClass("active");
    jQuery("#menu_empresa").css("display", "none");
    $("#bt_admin_vicepresidencia").addClass("current-page");
</script>