<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#menuEmpresa').collapse();
        $('#bt_empresa_colaboradores').addClass('active');
    });
</script>

<?php
include("app/models/estructura/Colaboradores.php");
$ClassColaboradores = new Colaboradores();
$lista_colaboradores = $ClassColaboradores->colaboradores_lista($_POST, $connect_admin);

$id = $_GET["id"];
$hoy = date("Y-m-d H:i:s");
$respuesta = "";

//FORMULARIO
if( $_POST["lider_form"] ) {

    $Empleado = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = " . $id . " AND estado = 1");
    $dataEmpleado = mysqli_fetch_array($Empleado);

    $sentencia = "INSERT INTO Lideres (id_empresa, id_empleado, id_jefe, created_at ) 
    VALUES 
    ( '".$user_log['id_empresa']."', '".$id."', '" . $_POST["id_jefe"] . "', '" . $hoy . "' ) ";

    mysqli_query($connect_admin, $sentencia );

    // echo '<script> window.location = "?pg=estructura/lideres";</script>'; //para evitar reinsersion  
    $accion = 'CREAR';
    $descripcion = 'Asignación de lider al usuario ' . $dataEmpleado["nombre"];

    $auditoria = "INSERT INTO Auditoria_Admin (id_empresa,id_empleado,accion,descripcion,modulo,created_at)
                    VALUES (" . $_SESSION['id_empresa'] . ", " .$user_log["id"]. ",'$accion','$descripcion','Lideres','$hoy')";
    // echo $auditoria;
    mysqli_query($connect_admin, $auditoria);
    echo '<script> window.location = "?pg=empresa/colaborador/lider_plataforma&id='.$id.'";</script>';
}

//INFORMACION DE LA BATERIA
$query = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $id . "'  ");
$data = mysqli_fetch_array($query);
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    .select2-selection--single .select2-selection__rendered{
        color: #006dbd !important;
    }
</style>

<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">

    <!-- TITULO -->
    <div class="card mb-3">
        <div class="card-header">
            <h3>Ficha Colaborador</h3>
        </div>
    </div>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" style="margin-top: 15px;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item" aria-current="page"><a href="?pg=empresa/colaboradores">Colaboradores</a></li>
            <li class="breadcrumb-item active" aria-current="page">Lider</li>
        </ol>
    </nav>

    <div class="card mb-3">
        <form method="POST" enctype="multipart/form-data">

            <!-- PESTAÑAS -->
            <div class="card-header">
                <ul class="nav nav-pills justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link" href="?pg=empresa/colaborador/detalle&id=<?= $data["id"]; ?>">Datos Básicos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="?pg=empresa/colaborador/lider_plataforma&id=<?= $data["id"]; ?>">Líder Plataforma</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="?pg=empresa/colaborador/evaluador_competencias&id=<?= $data["id"] ?>">Evaluador Competencias</a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="row">
                    <input type="hidden" name="lider_form" value="true">
                    <div class="row">
                        <!-- DATOS BÁSICOS-->
                        <div class="col-md-12 mt-3">
                            <h5>Seleccione a la persona que será lider del colaborador</h5>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label"><b>Jefe</b></label>
                            <select class="form-control select_2_search" name="id_jefe" id="id_jefe" value="" required>
                                <option value="">Selecciona...</option>
                                <?php foreach($lista_colaboradores as $colaborador): ?>
                                    <option value="<?= $colaborador["id"]; ?>"><?= $colaborador["nombre"]; ?></option>
                                <?php endforeach; ?>    
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="col-md-12 text-start">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                </div>
            </div>

        </form>
    </div>

    <!-- LISTADO DE LIDERES -->
    <div class="card">
        <div class="card-header">
            <h5>Líderes</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">

                    <div class="table-responsive">
                        <table id="lideres" class="table">
                            <thead>
                                <tr>
                                    <th>Documento</th>
                                    <th>Foto</th>
                                    <th>Nombre Lider</th>
                                    <th><?php echo $etiquetaAdminVP; ?></th>
                                    <th><?php echo $etiquetaAdminArea; ?></th>
                                    <th><?php echo $etiquetaAdminCargo; ?></th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $queryLider = mysqli_query($connect_admin, "SELECT * FROM Lideres WHERE id_empleado = " .$id. " AND id_empresa = '".$user_log["id_empresa"]."'  ");
                                while ($dataLider = mysqli_fetch_array($queryLider)) {
                                    
                                    $queryL = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $dataLider["id_jefe"] . "' ");
                                    $dataL = mysqli_fetch_array($queryL);
                                    $listado_lider = '';
                                    if (!$dataL["foto"]) {
                                        $dataL["foto"] = "img_default.jpg";
                                    }
                                    $listado_lider .= '<a data-bs-toggle="tooltip" href="javascript:Profile(' . $dataL["id"] . ',1)" class="dropdown-item" id="profileOkr"><img src="' .$recursos_publico. $dataL["foto"] . '" class="lazyload foto_miniaturas" title="' . $dataL["nombre"] . '" style="width: 35px !important;height: 35px !important;"></a>';

                                    $queryV = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id = '" . $dataL["unidad_corporativa"] . "' ");
                                    $dataV = mysqli_fetch_array($queryV);

                                    $queryA = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id = '" . $dataL["area"] . "' ");
                                    $dataA = mysqli_fetch_array($queryA);

                                    $queryC = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE id = '" . $dataL["id_cargo"] . "' ");
                                    $dataC = mysqli_fetch_array($queryC);


                                    $acciones = '<button type="button" class="btn btn-danger btn-sm" title="Quitar jefe" style="font-size: 12px; padding: 3px 5px;" onclick="Elimimar_Jefe(' . $dataLider["id"] . ')">
                                                <i class="bx bx-trash"></i>
                                            </button>';
                                ?>
                                    <tr>
                                        <td><?php echo $dataL["documento"]; ?></td>
                                        <td><?php echo $listado_lider; ?></td>
                                        <td><?php echo $dataL["nombre"]; ?></td>
                                        <td><?php echo $dataV["nombre"]; ?></td>
                                        <td><?php echo $dataA["nombre"]; ?></td>
                                        <td><?php echo $dataC["nombre"]; ?></td>
                                        <td><?php echo $acciones; ?></td>
                                    </tr>
                                <?php
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

    var api = '<?php echo $url; ?>api/empresa/';
    function Elimimar_Jefe(id_jefe){
        jQuery.ajax({
                url: api + "eliminar_lider.php",
                type: 'post',
                data: {
                    id: id_jefe,
                    id_empresa: <?php echo $user_log["id_empresa"]; ?>, 
                    url: '<?php echo $url; ?>?pg=empresa/colaborador/lider_plataforma&id=<?php echo $id; ?>'
                },
            }).done(function(resp) {
                $("#xscript").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});                                
    }
</script>



<!-- CSS de DataTables + Botones -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<!-- JS de DataTables + Botones -->
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
	$(document).ready(function() {
		$('#lideres').DataTable({
			pageLength: 25,
			language: {
				url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
			},
		});

        //BUSCADORES
        $('.select_2_search').select2({
            // Si usas Bootstrap 5:
            theme: 'bootstrap-5'
        });
	});
</script>