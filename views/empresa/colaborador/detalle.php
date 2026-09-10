<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#menuEmpresa').collapse();
        $('#bt_empresa_colaboradores').addClass('active');

        $('.multiples_roles').select2();
    });
</script>

<?php
include("app/models/estructura/Colaboradores.php");
$ClassColaboradores = new Colaboradores();
//$lista_cargos = $ClassColaboradores->Lista_Cargos($user_log["id_empresa"]);

$lista_cargos = array();

$sentencia_cargos = "
SELECT Cargos.*, Areas.nombre AS nombre_area 
FROM Cargos 
LEFT JOIN Areas ON Areas.id = Cargos.id_area
WHERE Cargos.id_empresa = '" . $user_log["id_empresa"] . "' AND Cargos.estado = 1 
ORDER BY Cargos.nombre ASC";

$queryCargos = mysqli_query($connect_admin, $sentencia_cargos);
while ($dataCargos = mysqli_fetch_array($queryCargos)) {
    array_push($lista_cargos, $dataCargos);
}


$lista_vicepresidencias = $ClassColaboradores->Lista_Vicepresidencias($user_log["id_empresa"]);
$lista_areas = $ClassColaboradores->Lista_Areas($user_log["id_empresa"]);
$lista_unidades_organizativas = $ClassColaboradores->Lista_Unidades_Organizativas($user_log["id_empresa"]);
$lista_jerarquia = $ClassColaboradores->Lista_Jerarquia($user_log["id_empresa"]);


$id = $_GET["id"];
$hoy = date("Y-m-d H:i:s");
$respuesta = "";

//FORMULARIO DEL COLABORADOR
if (isset($_POST["colaborador_form"]) && $_POST["colaborador_form"] == "true") {
    /* echo "<pre>";
    print_r($_FILES);
    echo "</pre>"; */


    if (isset($_GET["id"]) && !empty($_GET["id"])) {
        $ClassColaboradores->Editar_Colaborador($_POST, $_FILES, $id); //EDITAR
    } else {
        $ClassColaboradores->Guardar_Colaborador($_POST, $_FILES, $user_log["id_empresa"]); //GUARDAR NUEVO
    }
}

if ($_POST["activar_mfa"]) {

    mysqli_query($connect_admin, "UPDATE Empleados SET renovar_codigo = 2 WHERE id = '" . $id . "' ");
    $respuesta = '
        <div class="alert alert-success" role="alert">
            El MFA para este colaborador se ha activado correctamente.
        </div>
        ';
}

//SOLICITAR CAMBIO CONTRASEÑA
if ($_POST["cambio_contrasenia"]) {

    mysqli_query($connect_admin, "UPDATE Empleados SET cambio_pass = 1 WHERE id = '" . $id . "' ");
    $respuesta = '
        <div class="alert alert-success" role="alert">
            La solicitud de cambio de contraseña para este colaborador se ha realizado con exito.
        </div>
        ';
}

//INFORMACION DE LA BATERIA
$query = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $id . "'  ");
$data = mysqli_fetch_array($query);

$antiguedad = ObtenerAntiguedad($data["fecha_ingreso"]);


$vicepresidencia_data = $ClassColaboradores->Obtener_Vicepresidencia_de_Area($user_log["id_empresa"], $data["area"]);
$data["id_vicepresidencia"] = $vicepresidencia_data["vicepresidencia"];

$obj_roles = explode(",", $data["role"]);

$key = 'g0f0rag1l4';
$dato_encript = encrypt_data($data["id"], $key);
$url_unique_access = $url . 'unique_access.php?e=' . $dato_encript;

//$desifrado = decrypt_data($dato_encript, $key);
//print_r($desifrado);

if ($_POST["enviar_bienvenida"]) {
    include("app/models/brevo/Brevo.php");
    $ClassBrevo = new Brevo();

    $nombre = $data["nombre"];
    $asunto = "Gofor Agile - Bienvenido!";
    $correo = $data["correo"];

    include("app/models/brevo/plantillas.php");
    $plantilla = PlantillaBienvenida("William", "");
    $ClassBrevo->individual($nombre, $asunto, $correo, $plantilla);
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    .select2-selection--single .select2-selection__rendered {
        color: #006dbd !important;
    }

    .foto_miniaturas {
        width: 60px !important;
        height: 60px !important;
        object-fit: cover;
        border-radius: 50%;
    }
</style>

<div class="container pb-4">

    <a href="<?php echo $url; ?>?pg=empresa/colaboradores">
        <button class="btn btn-success mb-3">
            << Volver</button>
    </a>

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
            <li class="breadcrumb-item active" aria-current="page">Detalle</li>
        </ol>
    </nav>

    <div class="card mb-3">
        <form method="POST" enctype="multipart/form-data">

            <!-- PESTAÑAS -->
            <div class="card-header">
                <ul class="nav nav-pills justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="?pg=empresa/colaborador/detalle&id=<?= $data["id"] ?>">Datos Básicos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?pg=empresa/colaborador/lider_plataforma&id=<?= $data["id"] ?>">Líder Plataforma</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " href="?pg=empresa/colaborador/evaluador_competencias&id=<?= $data["id"] ?>">Evaluador Competencias</a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="row">
                    <input type="hidden" name="colaborador_form" value="true">
                    <div class="row">
                        <!-- DATOS BÁSICOS-->
                        <div class="col-md-12 mt-3">
                            <h5>Datos Básicos</h5>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><b>Código Colaborador / Documento</b></label>
                            <input type="text" class="form-control" name="documento" value="<?= $data["documento"]; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><b>Nombre</b></label>
                            <input type="text" class="form-control" name="nombre" value="<?= $data["nombre"]; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><b>Género</b></label>
                            <select class="form-control" name="genero" id="genero" value="<?= $data["genero"]; ?>" required>
                                <option value="">Selecciona...</option>
                                <option value="Masculino" <?= isset($data["genero"]) && $data["genero"] == "Masculino" ? 'selected' : ''; ?>>Masculino</option>
                                <option value="Femenino" <?= isset($data["genero"]) && $data["genero"] == "Femenino" ? 'selected' : ''; ?>>Femenino</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="fecha_ingreso" class="form-label">Fecha Ingreso Empresa</label>
                            <input type="date" class="form-control" name="fecha_ingreso" value="<?php echo $data["fecha_ingreso"]; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label for="antiguedad_empresa" class="form-label">Antiguedad en Empresa</label>
                            <input type="text" class="form-control" name="antiguedad_empresa" disabled value="<?= $antiguedad; ?>">
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Correo</label>
                            <input type="text" class="form-control" name="correo" value="<?php echo $data["correo"]; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Teléfono Móvil</label>
                            <input type="text" class="form-control" name="telefono_movil" value="<?php echo $data["telefono_movil"]; ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Teléfono Fijo</label>
                            <input type="text" class="form-control" name="telefono_fijo" value="<?php echo $data["telefono_fijo"]; ?>">
                        </div>

                        <!-- FOTO -->
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-6">
                                    <label for="foto" class="form-label">Cargar Foto ".jpg"</label>
                                    <input type="file" class="form-control" id="foto" name="foto" accept=".jpg,.png">
                                </div>
                                <?php if (!empty($data["foto"])): ?>
                                    <div id="foto_cargada" class="col-1">
                                        <?php $foto = !empty($data["foto"]) ? $data["foto"] : "img_default.jpg"; ?>
                                        <img src="<?= $recursos_local . $foto ?>" class="mt-4 foto_miniaturas" onclick="FichaEmpleado(<?= $data['id'] ?>)">

                                        <!-- BOTÓN ELIMINAR FOTO -->
                                        <span class="text-danger" style="cursor: pointer;" onclick="eliminarFoto(<?= $data['id'] ?>)"><i class="bi bi-trash3-fill" style="position:relative; top:10%; right:0;" title="Eliminar Foto"></i></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- POSICION EN LA ESTRUCTURA -->
                        <div class="col-md-12 mt-4">
                            <h5>Posición en la Estructura</h5>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Compañia</label>
                            <input type="text" class="form-control" name="compania" value="<?php echo $data["compania"]; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">División / Unidad Estratégica</label>
                            <input type="text" class="form-control" name="unidad_estrategica" value="<?php echo $data["unidad_estrategica"]; ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="id_cargo" class="form-label">Cargo</label>
                            <select class="form-control" name="id_cargo" id="id_cargo" value="<?php echo $data["id_cargo"]; ?>">
                                <option value="">Selecciona...</option>
                                <?php foreach ($lista_cargos as $cargo): ?>
                                    <option value="<?= $cargo["id"]; ?>" <?= isset($data["id_cargo"]) && $cargo["id"] == $data["id_cargo"] ? 'selected' : ''; ?>><?= $cargo["nombre"]; ?> - <?= $cargo["nombre_area"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="id_posicion" class="form-label">Posición</label>
                            <select class="form-control" name="id_posicion" id="id_posicion" value="">
                                <option value="">Selecciona...</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="unidad_corporativa" class="form-label">Vicepresidencia</label>
                            <select class="select_2_search form-control" name="unidad_corporativa" id="unidad_corporativa" value="" onchange="ListaAreasVicepresidencia(this.value)">
                                <option value="">Selecciona...</option>
                                <?php foreach ($lista_vicepresidencias as $vicepresidencia): ?>
                                    <option value="<?= $vicepresidencia["id"]; ?>" <?= isset($vicepresidencia["id"]) && $vicepresidencia["id"] == $data["unidad_corporativa"] ? 'selected' : ''; ?>><?= $vicepresidencia["nombre"]; ?></option>
                                <?php endforeach; ?>

                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="id_area" class="form-label">Área</label>
                            <select class="select_2_search form-control" name="area" id="area" onchange="ListaUnidadOrganizativa(this.value)">
                                <option value="">Selecciona...</option>
                                <?php foreach ($lista_areas as $area): ?>
                                    <option value="<?= $area["id"]; ?>" <?= isset($area["id"]) && $area["id"] == $data["area"] ? 'selected' : ''; ?>><?= $area["nombre"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="unidad_organizativa" class="form-label">División / Unidad Organizativa</label>
                            <select class="select_2_search form-control" name="unidad_organizativa" id="unidad_organizativa">
                                <option value="">Selecciona...</option>
                                <?php foreach ($lista_unidades_organizativas as $unidad): ?>
                                    <option value="<?= $unidad["id"]; ?>" <?= isset($unidad["id"]) && $unidad["id"] == $data["unidad_organizativa"] ? 'selected' : ''; ?>><?= $unidad["unidad_organizativa"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="nivel_jerarquico" class="form-label">Nivel Jerárquico</label>
                            <select class="form-control" name="nivel_jerarquico" id="nivel_jerarquico" required>
                                <option value="">Selecciona...</option>
                                <?php foreach ($lista_jerarquia as $jerarquia): ?>
                                    <option value="<?= $jerarquia["id"]; ?>" <?= isset($data["nivel_jerarquico"]) && $jerarquia["id"] == $data["nivel_jerarquico"] ? 'selected' : ''; ?>><?= $jerarquia["nombre"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Nivel General</label>
                            <select class="form-control" name="nivel_general" id="nivel_general" required>
                                <option value="">Selecciona...</option>
                                <?php for ($i = 1; $i <= 16; $i++): ?>
                                    <option value="<?= $i; ?>" <?= isset($data["nivel_general"]) && $i == $data["nivel_general"] ? 'selected' : ''; ?>><?= $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <!-- ACCESOS -->
                        <div class="col-md-12 mt-4">
                            <h5>Accesos</h5>
                        </div>
                        <div class="col-md-4" style="margin-bottom: 10px; margin-top:3px">
                            <label class="form-label">Roles Múltiples Plataforma Prueba *</label>

                            <select class="form-control multiples_roles" name="role[]" id="role_usuario" multiple="multiple" required style="width: 100%" required>
                                <option value="">Selecciona...</option>
                                <?php
                                $queryRoles = mysqli_query($connect_admin, " SELECT * FROM Roles WHERE estado = 1  ");
                                while ($dataRoles = mysqli_fetch_array($queryRoles)) {

                                    $permitir = false;
                                    foreach ($obj_roles as $id_role) {
                                        if ($id_role == $dataRoles["id"]) {
                                            $permitir = true;
                                        }
                                    }

                                    if ($permitir == true) {
                                        echo '<option value="' . $dataRoles["id"] . '" selected >' . $dataRoles["nombre"] . '</option>';
                                    } else {
                                        echo '<option value="' . $dataRoles["id"] . '">' . $dataRoles["nombre"] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Correo</label>
                            <input type="text" class="form-control" value="<?php echo $data["correo"]; ?>" readonly>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Contraseña *</label>
                            <input type="password" class="form-control" name="password" value="<?php echo $data["password"]; ?>">
                        </div>

                        <div class="col-md-2">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-control" name="estado" id="estado" required>
                                <option value="">Selecciona...</option>
                                <option value="1" <?= isset($data["estado"]) && $data["estado"] == "1" ? 'selected' : ''; ?>>Activo</option>
                                <option value="2" <?= isset($data["estado"]) && $data["estado"] == "2" ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="verificar" class="form-label">Validación Usuario</label>
                            <input type="text" class="form-control" value="<?= isset($data["verificar"]) && !empty($data["verificar"]) ? 'Verificado' : 'Sin verificar'; ?>" readonly>
                        </div>


                    </div>
                </div>
            </div>

            <div class="card-footer">
                <div class="col-md-12 text-center">
                    <button class="btn btn-primary w-100" type="submit">Guardar</button>
                </div>
            </div>
        </form>
    </div>

    <?php if ($id) { ?>
        <div class="card mb-3">
            <div class="card-body">
                <button class="btn btn-danger" onclick="SolicitaMFA()">Activar MFA</button>
                <button class="btn btn-primary" onclick="SolicitarCambioContrasenia()">Solicitar Cambio Contraseña</button>
                <button class="btn btn-warning" onclick="IngresarComoUsuario()">Ingresar como usuario</button>
                <button class="btn btn-success" onclick="EnviarCorreoBinvenida()">Enviar Correo de Bienvenida</button>
            </div>
        </div>
    <?php } ?>

</div>

<form action="" method="POST" id="formulario_activar_mfa">
    <input type="hidden" name="activar_mfa" value="true">
    <input type="hidden" name="id_colaborador" value="<?php echo $id; ?>">
</form>

<form action="" method="POST" id="formulario_cambio_contrasenia">
    <input type="hidden" name="cambio_contrasenia" value="true">
    <input type="hidden" name="id_colaborador" value="<?php echo $id; ?>">
</form>

<form action="" method="POST" id="formulario_enviar_bienvenida">
    <input type="hidden" name="enviar_bienvenida" value="true">
    <input type="hidden" name="id_colaborador" value="<?php echo $id; ?>">
</form>

<script>
    var permitir = false;

    function SolicitaMFA() {
        if (permitir == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de activar la solicitud de MFA para este colaborador. <br><br> Un MFA (Autenticación Multifactor) sirve para añadir una capa extra de seguridad al proceso de inicio de sesión.  Al momento de iniciar sesión se enviará un codigo de 6 dígitos al correo electrónico registrado en la cuenta del colaborador.<br><br>');
            $("#modal_body").append('<button class="btn btn-danger" onclick="permitir = true; SolicitaMFA()">Activar MFA</button>');
        } else {
            $("#formulario_activar_mfa").submit();
        }
    }

    function SolicitarCambioContrasenia() {
        if (permitir == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de cambiar el estado de este usuario para solicitar cambio de contraseña. ¿Está seguro?<br><br>');
            $("#modal_body").append('<button class="btn btn-danger" onclick="permitir = true; SolicitarCambioContrasenia()">Solicitar cambio de contraseña</button>');
        } else {
            $("#formulario_cambio_contrasenia").submit();
        }
    }

    function IngresarComoUsuario() {
        if (permitir == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de ingresar como Usuario. Por seguridad esta acción cerrará su sessión actual. ¿Está seguro?<br><br>');
            $("#modal_body").append('<button class="btn btn-danger" onclick="permitir = true; IngresarComoUsuario()">Ingresar como usuario</button>');
        } else {
            window.location = "<?php echo $url_unique_access; ?>";
        }
    }

    function EnviarCorreoBinvenida() {
        if (permitir == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de enviar un correo de bienvenida a este usuario, esta acción es irreversible. ¿Está seguro?<br><br>');
            $("#modal_body").append('<button class="btn btn-danger" onclick="permitir = true; EnviarCorreoBinvenida()">Enviar Correo</button>');
        } else {
            $("#formulario_enviar_bienvenida").submit();
        }

    }
</script>

<script>
    var api = '<?php echo $url; ?>api/empresa/';

    function ListaAreasVicepresidencia() {

        data = {
            id_empresa: <?php echo $user_log["id_empresa"]; ?>,
            id_vicepresidencia: $("#unidad_corporativa").val(),
        };
        jQuery.ajax({
                url: api + "lista_area_vicepresidencia.php",
                type: 'post',
                data: data,
            })
            .done(function(resp) {
                $("#area").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    function ListaUnidadOrganizativa() {

        data = {
            id_empresa: <?php echo $user_log["id_empresa"]; ?>,
            id_vicepresidencia: $("#unidad_corporativa").val(),
            id_area: $("#area").val()
        };

        jQuery.ajax({
                url: api + "lista_unidad_area.php",
                type: 'post',
                data: data,
            })
            .done(function(resp) {
                console.log(resp);

                $("#unidad_organizativa").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }
</script>

<script>
    $(document).ready(function() {
        //BUSCADORES
        $('.select_2_search').select2({
            // Si usas Bootstrap 5:
            theme: 'bootstrap-5'
        });
    });

    function FichaEmpleado(id_empleado) {

        var api = 'api/kpis/';
        var id_empresa = <?php echo $user_log["id_empresa"]; ?>

        $("#modal_empleado").modal("show");
        $("#body_empleado").html("Cargando...");

        jQuery.ajax({
                url: api + "ficha_empleado.php",
                type: 'post',
                data: {
                    id_empresa: id_empresa,
                    id_empleado: id_empleado,
                },
            })
            .done(function(resp) {
                $("#body_empleado").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});


    }

    function eliminarFoto(id_empleado) {
        if (confirm("¿Está seguro de que desea eliminar la foto de este empleado?")) {

            $("#foto_cargada").remove();

            var api = 'api/empresa/';
            jQuery.ajax({
                    url: api + "eliminar_foto_empleado.php",
                    type: 'post',
                    data: {
                        id_empleado
                    },
                })
                .done(function(resp) {
                    console.log(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
        }
    }
</script>