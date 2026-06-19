<script>
    $(".menu_section").addClass("active");
    // $("#nav_empresa").addClass("active");
    jQuery("#menu_empresa").css("display", "none");
    $("#bt_admin_colaboradores").addClass("current-page");
</script>
<?php
include("views/administrar/etiquetas.php");
$hoy = date("Y-m-d H:i:s");
$id = $_GET["id"];

//CONSULTA PARA NUEVO CLIENTE
//CONSULTA PARA NUEVO CLIENTE
if ($_POST["guardar_colaborador"] != "") {
    if ($_POST["nivel_general"] != "") {
        $nivel_general = $_POST["nivel_general"];
    } else {
        $nivel_general = "";
    }
    $queryCargos = mysqli_query($connect_valentina, "SELECT * FROM Cargos WHERE id = '" . $_POST["id_cargo"] . "' ");
    while ($dataCargo = mysqli_fetch_array($queryCargos)) {
        $cargo = $dataCargo['nombre'];
    }

    if ($_POST["id_registro"] != "") {
        $queryVdl = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE documento = '" . $_POST["documento"] . "' AND id_empresa = '" . $_SESSION['id_empresa'] . "' AND id NOT IN (" . $_POST["id_colaborador"] . ")");
        $queryVd2 = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND correo = '" . $_POST['correo'] . "' AND id NOT IN (" . $_POST["id_colaborador"] . ")");
        if ($queryVdl->num_rows == 0) {
            if ($queryVd2->num_rows == 0) {

                $sentencia = "
            UPDATE  Empleados  SET
            documento = '" . $_POST["documento"] . "',
            nombre = '" . $_POST["nombre"] . "',
            genero = '" . $_POST["genero"] . "',
            fecha_ingreso = '" . $_POST["fecha_ingreso"] . "',
            antiguedad_anios = '" . $_POST["antiguedad_anios"] . "',
            antiguedad_meses = '" . $_POST["antiguedad_meses"] . "',
            antiguedad_dias = '" . $_POST["antiguedad_dias"] . "',
            id_cargo = '" . $_POST["id_cargo"] . "',
            cargo = '" . $cargo . "',
            id_posicion = '" . $_POST["id_posicion"] . "',
            correo = '" . $_POST["correo"] . "',
            correo_personal = '" . $_POST["correo_personal"] . "',
            telefono_movil = '" . $_POST["telefono_movil"] . "',
            telefono_fijo = '" . $_POST["telefono_fijo"] . "',
            nivel_jerarquico = '" . $_POST["nivel_jerarquico"] . "',
            nivel_general = '" . $nivel_general . "',
            unidad_organizativa = '" . $_POST["unidad_organizativa"] . "',
            compania = '" . $_POST["compania"] . "',
            unidad_corporativa = '" . $_POST["unidad_corporativa"] . "',
            area = '" . $_POST["area"] . "',
            role = '" . $_POST["role"] . "',
            estado = '" . $_POST["estado"] . "',
            password = '" . $_POST["password"] . "',
            contrasena = '" . hash('sha512', $_POST["password"]) . "',
            verificar = '" . $_POST["verificar"] . "',
            updated_at = '" . $hoy . "'
            WHERE id = '" . $_POST["id_registro"] . "'
            ";
                // echo $sentencia;

                mysqli_query($connect_valentina, $sentencia);

                if ($_FILES["foto"]["name"] != "") {
                    include("app/controllers/subir_documento.php");
                    $archivo = Subir_Documento($_FILES["foto"]);
                    mysqli_query($connect_valentina, "UPDATE Empleados SET foto = '" . $archivo . "' WHERE id = '" . $_POST["id_registro"] . "'  ");
                }

                $accion = 'ACTUALIZAR';
                $descripcion = 'Actualización de usuario ' . $_POST["nombre"];

                $auditoria = "INSERT INTO Auditoria_Admin (id_empresa,id_empleado,accion,descripcion,modulo,created_at)
                VALUES (" . $_SESSION['id_empresa'] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion','Colaboradores','$hoy')";
                // echo $auditoria;
                mysqli_query($connect_valentina, $auditoria);

                $respuesta = '
                <div class="alert alert-success" role="alert">
                  Los datos han sido actualizado
                </div>
                ';
                echo '<script> window.location = "?pg=administrar/colaborador/home&id=' . $id . '&detalle_tab_pane=' . $id . '";</script>';
            } else {
                $respuesta = '
            <div class="alert alert-danger" role="alert">
                Lo sentimos, ya se encuentra un usuario creado con el correo ' . $_POST["correo"] . '
            </div>
            ';
            }
        } else {
            $respuesta = '
        <div class="alert alert-danger" role="alert">
            Lo sentimos, ya se encuentra un usuario creado con el documento ' . $_POST["documento"] . '
        </div>
        ';
        }
    } else {

        $queryVdl = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE documento = '" . $_POST["documento"] . "' AND id_empresa = '" . $_SESSION['id_empresa'] . "'");
        $queryVd2 = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND correo = '" . $_POST['correo'] . "'");
        if ($queryVdl->num_rows == 0) {
            if ($queryVd2->num_rows == 0) {
                if ($queryVdl->num_rows == 0) {
                    if ($_SESSION['id_empresa'] == 1) {
                        $sucursal = 'Punta Cana';
                    } else {
                        $sucursal = 'Colombia';
                    }

                    $sentencia = "
                    INSERT INTO Empleados ( id_empresa , documento , nombre , genero, fecha_ingreso, antiguedad_anios,antiguedad_meses,antiguedad_dias, id_cargo, cargo, id_posicion, correo, correo_personal, telefono_movil, telefono_fijo, nivel_jerarquico, nivel_general, compania, sucursal, unidad_corporativa, area, unidad_organizativa, nombre_jefe, cargo_jefe, role, estado, password, contrasena, foto, verificar, created_at )
                    VALUES
                    ( '" . $_SESSION['id_empresa'] . "', '" . $_POST["documento"] . "', '" . $_POST["nombre"] . "','" . $_POST["genero"] . "', '" . $_POST["fecha_ingreso"] . "','" . $_POST["antiguedad_anios"] . "', '" . $_POST["antiguedad_meses"] . "', '" . $_POST["antiguedad_dias"] . "','" . $_POST["id_cargo"] . "','" . $cargo . "','" . $_POST["id_posicion"] . "','" . $_POST["correo"] . "','" . $_POST["correo_personal"] . "','" . $_POST["telefono_movil"] . "','" . $_POST["telefono_fijo"] . "','" . $_POST["nivel_jerarquico"] . "','$nivel_general','" . $_POST["compania"] . "','$sucursal','" . $_POST["unidad_corporativa"] . "','" . $_POST["area"] . "','" . $_POST["unidad_organizativa"] . "','" . $_POST["nombre_jefe"] . "','" . $_POST["cargo_jefe"] . "' , '" . $_POST["role"] . "','" . $_POST["estado"] . "', '" . $_POST["password"] . "', '" . hash('sha512', $_POST["password"]) . "','" . $_POST["foto"] . "', '3', '" . $hoy . "' )
                    ";

                    mysqli_query($connect_valentina, $sentencia);
                    $id_tmp = mysqli_insert_id($connect_valentina);
                    $_GET["id"] = $id_tmp;

                    $accion = 'CREAR';
                    $descripcion = 'Creación de usuario ' . $_POST["nombre"];

                    $auditoria = "INSERT INTO Auditoria_Admin (id_empresa,id_empleado,accion,descripcion,modulo,created_at)
                VALUES (" . $_SESSION['id_empresa'] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion','Colaboradores','$hoy')";
                    // echo $auditoria;
                    mysqli_query($connect_valentina, $auditoria);

                    echo '<script> window.location = "?pg=administrar/colaborador/home&id=' . $id_tmp . '&detalle_tab_pane=' . $id_tmp . '";</script>';
                } else {
                    $respuesta = '
                    <div class="alert alert-danger" role="alert">
                        Lo sentimos, ya se encuentra un usuario creado con la cédula ' . $_POST["documento"] . '
                    </div>
                    ';
                }
            } else {
                $respuesta = '
                    <div class="alert alert-danger" role="alert">
                        Lo sentimos, ya se encuentra un usuario creado con el correo ' . $_POST["correo"] . '
                    </div>
                    ';
            }
        } else {
            $respuesta = '
                <div class="alert alert-danger" role="alert">
                    Lo sentimos, ya se encuentra un usuario creado con el documento ' . $_POST["documento"] . '
                </div>
                ';
        }
    }
}

if ($_POST["guardar_lider"] != "") {
    $Empleado = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = " . $_POST["id_empleado"] . " AND estado = 1");
    $dataEmpleado = mysqli_fetch_array($Empleado);
    mysqli_query($connect_valentina, "INSERT INTO Lideres (id_empresa, id_empleado, id_jefe, created_at )
        VALUES
        ( '" . $_SESSION['id_empresa'] . "', '" . $_POST["id_empleado"] . "', '" . $_POST["id_jefe"] . "', '" . $hoy . "' ) ");

    // echo '<script> window.location = "?pg=estructura/lideres";</script>'; //para evitar reinsersion
    $accion = 'CREAR';
    $descripcion = 'Asignación de lider al usuario ' . $dataEmpleado["nombre"];

    $auditoria = "INSERT INTO Auditoria_Admin (id_empresa,id_empleado,accion,descripcion,modulo,created_at)
                    VALUES (" . $_SESSION['id_empresa'] . ", " . $_SESSION['id_user'] . ",'$accion','$descripcion','Lideres','$hoy')";
    // echo $auditoria;
    mysqli_query($connect_valentina, $auditoria);
    echo '<script> window.location = "?pg=administrar/colaborador/home&id=' . $id . '&lider_tab_pane=' . $id . '";</script>';
}

if ($_POST["guardar_evaluador"] != "") {

    mysqli_query($connect_valoracion, "INSERT INTO Evaluadores (id_empresa, anio, id_ciclo, id_empleado, id_evaluador, tipo, created_at )
        VALUES
        ( '" . $_SESSION['id_empresa'] . "', '" . $_SESSION["anio_ciclo"] . "', '" . $_SESSION['ciclo'] . "', '" . $_POST["id_empleado"] . "', '" . $_POST["id_evaluador"] . "', '" . $_POST["tipo"] . "', '" . $hoy . "' ) ");

    echo '<script> window.location = "?pg=administrar/colaborador/home&id=' . $id . '&evaluador_tab_pane=' . $id . '";</script>';
}


$query = mysqli_query($connect_valentina, "SELECT * FROM Empleados WHERE id = '" . $_GET["id"] . "' ");
$data = mysqli_fetch_array($query);

$nacimiento = $data["fecha_nace"];
$anioNace = explode("-", $nacimiento);
$anioNace = $anioNace[0];
$edad = (date("Y")) - $anioNace;

$ingreso = $data["fecha_ingreso"];
$anioIngresa = explode("-", $ingreso);
$anioIngresa = $anioIngresa[0];
$ingreso = (date("Y")) - $anioIngresa;

$queryMA1 = mysqli_query($connect_valentina, "SELECT * FROM Menu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 1");
$dataMA1 = mysqli_fetch_array($queryMA1);

include("views/administrar/colaborador/modal_retiro.php");
include("views/administrar/colaborador/modal_movimiento.php");
include("views/okrs/layouts/modal_profile.php");
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    .card,
    .card-header,
    .card-body,
    .card-footer {
        background-color: white !important;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #FFFFFF !important;">
                <div class="row">
                    <div class="col-md-8" style="text-align: start !important;">
                        <h4><i class="fas fa-dice-d20" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;Creación / Edición Colaborador
                        </h4>
                    </div>
                    <div class="col-md-4" align="right">
                        <a href="<?php echo $url; ?>?pg=administrar/colaboradores" class="btn btn-success">Volver</a>
                    </div>
                </div>
            </div>
        </div>
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
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" align="center">
            <div class="card">
                <div class="card-body" align="center">
                    <div class="card">
                        <div class="card-header">
                            <div class="col-md-12">
                                <ul class="nav nav-fill nav-tabs" id="myTab" role="tablist" style="font-family: 'Lato-Bold';">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="detalle-tab" data-bs-toggle="tab" data-bs-target="#detalle-tab-pane-<?php echo $id; ?>" type="button" role="tab" aria-controls="detalle-tab-pane-<?php echo $id; ?>" aria-selected="true" style="color: black;font-weight:700;">Datos Básicos</button>
                                    </li>
                                    <?php
                                    if ($_GET["id"] != "") { ?>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="lider-tab" data-bs-toggle="tab" data-bs-target="#lider-tab-pane-<?php echo $id; ?>" type="button" role="tab" aria-controls="lider-tab-pane-<?php echo $id; ?>" aria-selected="false" style="color: black;font-weight:700;">Lider Plataforma</button>
                                        </li>
                                        <?php
                                        if ($dtEmpresa["mod_valoracion"] == "on") { ?>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="evaluador-tab" data-bs-toggle="tab" data-bs-target="#evaluador-tab-pane-<?php echo $id; ?>" type="button" role="tab" aria-controls="evaluador-tab-pane-<?php echo $id; ?>" aria-selected="false" style="color: black;font-weight:700;">Evaluador Competencias<?php echo $dataMA1["nombre"]; ?></button>
                                            </li>
                                    <?php }
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <div class="tab-content pt-5" id="myTabContent_<?php echo $id; ?>">
                                    <div class="tab-pane fade show active" id="detalle-tab-pane-<?php echo $id; ?>" role="tabpanel" aria-labelledby="detalle-tab" tabindex="0">
                                        <?php if ($_SESSION["id_empresa"] == 1) {
                                            include("views/administrar/colaborador/editar_pc.php");
                                        } else {
                                            include("views/administrar/colaborador/editar.php");
                                        } ?>
                                    </div>
                                    <?php
                                    if ($_GET["id"] != "") { ?>
                                        <div class="tab-pane fade" id="lider-tab-pane-<?php echo $id; ?>" role="tabpanel" aria-labelledby="lider-tab" tabindex="0">
                                            <?php include("views/administrar/colaborador/lider.php"); ?>
                                        </div>
                                        <?php
                                        if ($dtEmpresa["mod_valoracion"] == "on") { ?>
                                            <div class="tab-pane fade" id="evaluador-tab-pane-<?php echo $id; ?>" role="tabpanel" aria-labelledby="evaluador-tab" tabindex="0">
                                                <?php include("views/administrar/colaborador/evaluador.php"); ?>
                                            </div>
                                    <?php }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var divElementEB = document.getElementById("myTabContent_<?php echo $id; ?>");
        var divElementO = document.getElementById("detalle-tab-pane-<?php echo $_GET["detalleo_tab_pane"]; ?>");
        var divElementI = document.getElementById("evaluador-tab-pane-<?php echo $_GET["evaluador_tab_pane"]; ?>");
        var divElementA = document.getElementById("lider-tab-pane-<?php echo $_GET["lider_tab_pane"]; ?>");

        if (divElementO) {
            divElementEB.scrollIntoView();
            $("#lider-tab").removeClass("active");
            $("#lider-tab-pane-<?php echo $id; ?>").removeClass("active show");
            $("#evaluador-tab").removeClass("active");
            $("#evaluador-tab-pane-<?php echo $id; ?>").removeClass("active show");
            $("#detalle-tab").addClass("active");
            $("#detalle-tab-pane-<?php echo $id; ?>").addClass("active show");
        }
        if (divElementI) {
            divElementEB.scrollIntoView();
            $("#detalle-tab").removeClass("active");
            $("#detalle-tab-pane-<?php echo $id; ?>").removeClass("active show");
            $("#lider-tab").removeClass("active");
            $("#lider-tab-pane-<?php echo $id; ?>").removeClass("active show");
            $("#evaluador-tab").addClass("active");
            $("#evaluador-tab-pane-<?php echo $id; ?>").addClass("active show");

        }
        if (divElementA) {
            divElementEB.scrollIntoView();
            $("#detalle-tab").removeClass("active");
            $("#detalle-tab-pane-<?php echo $id; ?>").removeClass("active show");
            $("#evaluador-tab").removeClass("active");
            $("#evaluador-tab-pane-<?php echo $id; ?>").removeClass("active show");
            $("#lider-tab").addClass("active");
            $("#lider-tab-pane-<?php echo $id; ?>").addClass("active show");
        }


    });
    window.location.hash = "";
</script>
<script>
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
</script>