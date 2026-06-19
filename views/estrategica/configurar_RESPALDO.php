<script>
$(document).ready(function() {
    $('#menuEstrategia').collapse();
    $('#bt_estrategia_configuracion').addClass('active');
});
</script>


<?php
date_default_timezone_set('America/Bogota');
$hoy = date("Y-m-d H:i:s");
//CONSULTA PARA NUEVO CLIENTE
//CONSULTA PARA NUEVO CLIENTE
function obtenerListadoDesdeMes($fecha_inicio)
{
    $meses = [
        1 => 'Enero',
        2 => 'Febrero',
        3 => 'Marzo',
        4 => 'Abril',
        5 => 'Mayo',
        6 => 'Junio',
        7 => 'Julio',
        8 => 'Agosto',
        9 => 'Septiembre',
        10 => 'Octubre',
        11 => 'Noviembre',
        12 => 'Diciembre'
    ];

    $inicio = new DateTime($fecha_inicio);

    $mes_inicial = (int)$inicio->format('m');

    $meses_seleccionados = [];

    for ($i = $mes_inicial; $i <= 12; $i++) {
        $meses_seleccionados[$i] = $meses[$i];
    }

    for ($i = 1; $i < $mes_inicial; $i++) {
        $meses_seleccionados[$i] = $meses[$i];
    }

    return $meses_seleccionados;
}

$complemento = " AND M.id IN (";
$complementoM = " AND id_menu IN (";
$complementoSE = " AND id_menu IN (";
if ($_SESSION['id_empresa'] == 1) {
    $complemento .= "3,";
    $complementoM .= "3,";
    $complementoSE .= "3,";
}
if ($dtEmpresa["mod_okrs_equipo"] == "on") {
    $complemento .= "8,9,10,";
    $complementoM .= "8,9,10,";
    $complementoSE .= "8,9,10,";
}

if ($dtEmpresa["mod_valoracion"] == "on") {
    $complemento .= "1,";
    $complementoM .= "1,";
    $complementoSE .= "1,";
}
if ($dtEmpresa["mod_desempenio"] == "on") {
    $complemento .= "6,";
    $complementoM .= "6,";
    $complementoSE .= "6,";
}
if ($dtEmpresa["mod_endomarketing"] == "on") {
    $complemento .= "5,7,";
    $complementoM .= "5,7,";
    $complementoSE .= "5,7,";
}
if ($dtEmpresa["mod_admin"] == "on") {
    $complemento .= "2,4,";
    $complementoM .= "2,4,";
    $complementoSE .= "2,4,";
}

if ($dtEmpresa["mod_academia"] == "on") {
    $complemento .= "11,";
    $complementoM .= "11,";
    $complementoSE .= "11,";
}

$complemento = substr($complemento, 0, -1);
$complemento .= ")";
$complementoM = substr($complementoM, 0, -1);
$complementoM .= ")";
$complementoSE = substr($complementoSE, 0, -1);
$complementoSE .= ")";

if ($_POST["guardar_configurar"] != "") {
    mysqli_query($connect_admin, "UPDATE Empresas SET formato_numerico = '".$_POST["formatoNumerico"]."' , anio_curso = '" . $_POST["anio_curso"] . "', mes_inicio = '" . $_POST["mes_inicio"] . "', mes_fin = '" . $_POST["mes_fin"] . "', update_at = '$hoy'  WHERE id = '" . $_SESSION["id_empresa"] . "'  ");
    $_SESSION["anio_fill"] = $_POST["anio_curso"];
    echo '<script> window.location = "?pg=administrar/configurar";</script>'; //para evitar reinsersion
}

if ($_POST["guardar_roles_okr"] != "") {
    $queryRO = mysqli_query($connect_okrs, "SELECT * FROM Roles_Okrs WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1");
    while ($dataRO = mysqli_fetch_array($queryRO)) {
        $nombreRol = 'nombre_rol_' . $dataRO["id"];
        $descripcion = 'descripcion_' . $dataRO["id"];

        $sentencia = "UPDATE Roles_Okrs SET nombre_rol = '" . $_POST[$nombreRol] . "',
        descripcion = '" . str_replace('<br>', '', $_POST[$descripcion]) . "',
        updated_at = '$hoy'
        WHERE id = " . $dataRO["id"] . "";

        // echo $sentencia;

        mysqli_query($connect_okrs, $sentencia);
    }

    echo '<script> window.location.href = "?pg=administrar/configurar&tab=okrs";</script>';
}

if ($_POST["guardar_roles_kpi"] != "") {
    $queryRO = mysqli_query($connect_kpis, "SELECT * FROM Roles_Kpis WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1");
    while ($dataRO = mysqli_fetch_array($queryRO)) {
        $nombreRol = 'nombre_rol_kpi_' . $dataRO["id"];
        $descripcion = 'descripcion_kpi_' . $dataRO["id"];

        $sentencia = "UPDATE Roles_Kpis SET nombre_rol = '" . $_POST[$nombreRol] . "',
        descripcion = '" . str_replace('<br>', '', $_POST[$descripcion]) . "',
        updated_at = '$hoy'
        WHERE id = " . $dataRO["id"] . "";

        // echo $sentencia;

        mysqli_query($connect_kpis, $sentencia);
    }

    echo '<script> window.location.href = "?pg=administrar/configurar&tab=kpis";</script>';
}

if ($_POST["guardar_resultado_kpi"] != "") {
    $queryRO = mysqli_query($connect_kpis, "SELECT * FROM Tipo_Resultado_Kpi WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1");
    while ($dataRO = mysqli_fetch_array($queryRO)) {
        $nombreRol = 'nombre_resultado_kpi_' . $dataRO["id"];
        $descripcion = 'descripcion_resultado_kpi_' . $dataRO["id"];

        $sentencia = "UPDATE Tipo_Resultado_Kpi SET nombre = '" . $_POST[$nombreRol] . "',
        descripcion = '" . str_replace('<br>', '', $_POST[$descripcion]) . "',
        updated_at = '$hoy'
        WHERE id = " . $dataRO["id"] . "";

        // echo $sentencia;

        mysqli_query($connect_kpis, $sentencia);
    }

    echo '<script> window.location.href = "?pg=administrar/configurar&tab=kpis";</script>';
}

if ($_POST["guardar_calculo_kpi"] != "") {
    $queryRO = mysqli_query($connect_kpis, "SELECT * FROM Tipo_Calculo_Kpi WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1");
    while ($dataRO = mysqli_fetch_array($queryRO)) {
        $nombreRol = 'nombre_calculo_kpi_' . $dataRO["id"];
        $descripcion = 'descripcion_calculo_kpi_' . $dataRO["id"];

        $sentencia = "UPDATE Tipo_Calculo_Kpi SET nombre = '" . $_POST[$nombreRol] . "',
        descripcion = '" . str_replace('<br>', '', $_POST[$descripcion]) . "',
        updated_at = '$hoy'
        WHERE id = " . $dataRO["id"] . "";

        // echo $sentencia;

        mysqli_query($connect_kpis, $sentencia);
    }

    echo '<script> window.location.href = "?pg=administrar/configurar&tab=kpis";</script>';
}

if ($_POST["guardar_mensaje_emergente_kpi"] != "") {
    $queryME = mysqli_query($connect_kpis, "SELECT * FROM Mensajes_Emergentes WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1");
    while ($dataME = mysqli_fetch_array($queryME)) {
        $descripcion = 'descripcion_mensaje_emergente_' . $dataME["id"];

        $sentencia = "UPDATE Mensajes_Emergentes SET descripcion = '" . str_replace('<br>', '', $_POST[$descripcion]) . "',
        updated_at = '$hoy'
        WHERE id = " . $dataME["id"] . "";

        // echo $sentencia;

        mysqli_query($connect_kpis, $sentencia);
    }

    echo '<script> window.location.href = "?pg=administrar/configurar&tab=kpis";</script>';
}

if ($_POST["guardar_ponderacion_kpi"] != "") {
    $queryP = mysqli_query($connect_kpis, "SELECT * FROM Ponderaciones WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1");
    if (mysqli_num_rows($queryP) > 0) {
        $sentencia = "UPDATE Ponderaciones SET estrategico = '" . $_POST["estrategicos"] . "',tactico = '" . $_POST["tacticos"] . "',
        updated_at = '$hoy'
        WHERE id_empresa = " . $_SESSION["id_empresa"] . "";
    } else {
        $sentencia = "INSERT INTO Ponderaciones (id_empresa, estrategico, tactico, estado, created_at)
        VALUES (" . $_SESSION["id_empresa"] . ",'" . $_POST["estrategicos"] . "','" . $_POST["tacticos"] . "',1,'$hoy')";
    }


    mysqli_query($connect_kpis, $sentencia);

    echo '<script> window.location.href = "?pg=administrar/configurar&tab=kpis";</script>';
}

if ($_POST["id_registro_la"] != "") {

    $queryEscala = mysqli_query($connect_clima, "SELECT * FROM Escala_Lecciones WHERE id_empresa = " . $_SESSION["id_empresa"] . "");
    while ($dataEscala = mysqli_fetch_array($queryEscala)) {
        $acciont = 'acciont_' . $dataEscala["id"];
        $fondoAccion = 'colorPickerbg_' . $dataEscala["id"];
        $colorAccion = 'colorPickerl_' . $dataEscala["id"];
        $titulo = 'titulo_' . $dataEscala["id"];
        $estado = 'estado_' . $dataEscala["id"];

        if ($_POST[$estado] == 'on') {
            $estado = 1;
        } else {
            $estado = 2;
        }

        $sentencia = "UPDATE Escala_Lecciones SET accion = '" . $_POST[$acciont] . "',
        color = '" . $_POST[$fondoAccion] . "',
        color_letra= '" . $_POST[$colorAccion] . "',
        titulo = '" . $_POST[$titulo] . "',
        estado = $estado,
        updated_at = '$hoy'
        WHERE id = " . $dataEscala["id"] . "";

        mysqli_query($connect_clima, $sentencia);
    }
    $respuesta = '
			<div class="alert alert-success" role="alert">
			 	Actualización de semáforo realizada con exito
			</div>
			';
    echo '<script> window.location.href = "?pg=administrar/configurar&tab=lecciones";</script>';
}

if ($_POST["guardar_nombre_menu"] != "") {
    $queryM = mysqli_query($connect_admin, "SELECT * FROM Menu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " $complementoM");
    while ($dataM = mysqli_fetch_array($queryM)) {
        $nombreMenu = 'menu_' . $dataM["id"];

        $sentencia = "UPDATE Menu_Empresa SET nombre = '" . $_POST[$nombreMenu] . "',
        updated_at = '$hoy'
        WHERE id = " . $dataM["id"] . "";

        mysqli_query($connect_admin, $sentencia);
    }

    echo '<script> window.location.href = "?pg=administrar/configurar&tab=plataforma";</script>';
}

if ($_POST["guardar_desempenio"] != "") {

    $queryNJ = mysqli_query($connect_admin, "SELECT * FROM Nivel_Jerarquico WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 AND nivel NOT IN (1,6)");
    while ($dataNJ1 = mysqli_fetch_array($queryNJ)) {

        $okr = 'mod_okr_' . $dataNJ1["id"];
        $competencias = 'mod_competencias_' . $dataNJ1["id"];
        $kpi = 'mod_kpi_' . $dataNJ1["id"];

        $sentencia = "INSERT INTO Ponderar_Desempenio (id_empresa, anio, nivel, mod_competencias, mod_kpis, mod_okrs, estado, created_at) VALUES
        ('" . $_SESSION["id_empresa"] . "','" . $_POST["periodo"] . "','" . $dataNJ1["id"] . "','" . $_POST[$competencias] . "','" . $_POST[$kpi] . "','" . $_POST[$okr] . "',1,'$hoy')";

        // echo $sentencia."<br>";

        mysqli_query($connect_admin, $sentencia);
    }

    echo '<script> window.location.href = "?pg=administrar/configurar&tab=desempenio";</script>';
}

if ($_POST["guardar_configruar_ciclo"] != "") {
    mysqli_query($connect_admin, "UPDATE Empresas SET anio_ciclo = '" . $_POST["anio_ciclo"] . "', id_ciclo = '" . $_POST["ciclo"] . "', update_at = '$hoy'  WHERE id = '" . $_SESSION["id_empresa"] . "'  ");
    $_SESSION["anio_ciclo"] = $_POST["anio_ciclo"];
    $_SESSION["ciclo"] = $_POST["ciclo"];
    echo '<script> window.location.href = "?pg=administrar/configurar&tab=competencias";</script>';
}

include("views/lecciones_aprendidas/modal_aspecto_clave.php");
include("views/lecciones_aprendidas/modal_general.php");
include("views/administrar/etiquetas.php");

//INFORMACION DE LA BATERIA
$query = mysqli_query($connect_admin, "SELECT * FROM Empresas WHERE id = '" . $_SESSION["id_empresa"] . "'  ");
$data = mysqli_fetch_array($query);

$querySM21 = mysqli_query($connect_admin, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 2 AND id_submenu = 10");
$dataSM21 = mysqli_fetch_array($querySM21);
?>



<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3>Configurar año en curso</h3>
                </div>

                <div class="card-body">

                    <form action="" method="post">
                        <div class="row">
                            <div class="col-md-12">
                                
                                <input type="hidden" name="guardar_configurar" value="true">
                            </div>
                            <div class="col-md-3" style="margin-bottom: 10px">
                                <lable>Año en Curso</lable>
                                <select class="form-control form-control-sm" name="anio_curso">
                                    <option value="">Por Año...</option>
                                    <?php
                                    foreach ($Array_Anio as $anio) {
                                        if ($data["anio_curso"] ==  $anio[1]) {
                                            echo '<option value="' . $anio[1] . '" selected>' . $anio[1] . '</option>';
                                        } else {
                                            echo '<option value="' . $anio[1] . '">' . $anio[1] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-2">
                                <lable>Mes inicio</lable>
                                <input type="month" name="mes_inicio" id="mes_inicio" class="form-control form-control-sm" value="<?php echo $data["mes_inicio"]; ?>">
                            </div>
                            <div class="col-md-2">
                                <lable>Mes fin</lable>
                                <input type="text" name="mes_fin" id="mes_fin" class="form-control form-control-sm" value="<?php echo $data["mes_fin"]; ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <label for="formatoNumerico" class="col-sm-3 col-form-label">Formato numérico</label>
                                <div class="col-sm-4">
                                    <select name="formatoNumerico" id="formatoNumerico" class="form-control">
                                        <option value="americano" <?php if($data["formato_numerico"] == 'americano') echo 'selected'; ?>>Americano (1,000.25)</option>
                                        <option value="europeo" <?php if($data["formato_numerico"] == 'europeo') echo 'selected'; ?>>Europeo (1.000,25)</option>
                                    </select>
                                </div>
                                <div class="col-sm-5 text-muted">
                                    <small>Selecciona cómo deseas interpretar los números con separadores de miles y decimales.</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12" style="margin-bottom: 10px">
                                <button type="submit" class="btn btn-success btn-block btn-sm">
                                    <i class="fas fa-check"></i> Guardar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-md-12">
                            <h3>Configuración Módulos Plataforma</h3>
                        </div>
                    </div>

                </div>
                <div class="card-body">

                    <div class="col-md-12">
                        <ul class="nav nav-fill nav-tabs" id="myTab" role="tablist" >
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="desempenio-tab" data-bs-toggle="tab" data-bs-target="#desempenio-tab-pane" type="button" role="tab" aria-controls="desempenio-tab-pane" aria-selected="true" style="color: black;">Desempeño</button>
                            </li>

                            <?php if ($dtEmpresa["mod_valoracion"] == "on") { ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="competencias-tab" data-bs-toggle="tab" data-bs-target="#competencias-tab-pane" type="button" role="tab" aria-controls="competencias-tab-pane" aria-selected="true" style="color: black;">Competencias</button>
                                </li>
                            <?php } ?>
                            <?php $activeOkr = 'active';
                            if ($dtEmpresa["mod_desempenio"] == "on") {
                                $activeOkr = ''; ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="kpis-tab" data-bs-toggle="tab" data-bs-target="#kpis-tab-pane" type="button" role="tab" aria-controls="kpis-tab-pane" aria-selected="true" style="color: black;">KPIS</button>
                                </li>
                            <?php } ?>
                            <?php if ($dtEmpresa["mod_endomarketing"] == "on") { ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="lecciones-tab" data-bs-toggle="tab" data-bs-target="#lecciones-tab-pane" type="button" role="tab" aria-controls="lecciones-tab-pane" aria-selected="false" style="color: black;">Lecciones Aprendidas</button>
                                </li>
                            <?php } ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo $activeOkr; ?>" id="okrs-tab" data-bs-toggle="tab" data-bs-target="#okrs-tab-pane" type="button" role="tab" aria-controls="okrs-tab-pane" aria-selected="false" style="color: black;">OKRS</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="plataforma-tab" data-bs-toggle="tab" data-bs-target="#plataforma-tab-pane" type="button" role="tab" aria-controls="plataforma-tab-pane" aria-selected="false" style="color: black;">Plataforma</button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="col-md-12">
                        <div class="tab-content pt-5" id="myTabContent_<?php echo $_SESSION["id_user"]; ?>">
                            <div class="tab-pane fade" id="desempenio-tab-pane" role="tabpanel" aria-labelledby="desempenio-tab" tabindex="0">
                                <?php include("views/administrar/permisos/desempenio.php"); ?>
                            </div>
                            <?php if ($dtEmpresa["mod_valoracion"] == "on") { ?>
                                <div class="tab-pane fade" id="competencias-tab-pane" role="tabpanel" aria-labelledby="competencias-tab" tabindex="0">
                                    <?php include("views/administrar/permisos/competencias.php"); ?>
                                </div>
                            <?php } ?>
                            <?php $modOkr = " active show";
                            if ($dtEmpresa["mod_desempenio"] == "on") {
                                $modOkr = "";  ?>
                                <div class="tab-pane fade active show" id="kpis-tab-pane" role="tabpanel" aria-labelledby="kpis-tab" tabindex="0">
                                    <?php include("views/administrar/permisos/kpis.php"); ?>
                                </div>
                            <?php } ?>
                            <?php
                            if ($dtEmpresa["mod_endomarketing"] == "on") {
                            ?>
                                <div class="tab-pane fade" id="lecciones-tab-pane" role="tabpanel" aria-labelledby="lecciones-tab" tabindex="0">
                                    <?php include("views/administrar/permisos/lecciones.php"); ?>
                                </div>
                            <?php } ?>
                            <div class="tab-pane fade<?php echo $modOkr; ?>" id="okrs-tab-pane" role="tabpanel" aria-labelledby="okrs-tab" tabindex="0">
                                <?php include("views/administrar/permisos/okrs.php"); ?>
                            </div>
                            <div class="tab-pane fade" id="plataforma-tab-pane" role="tabpanel" aria-labelledby="plataforma-tab" tabindex="0">
                                <?php include("views/administrar/permisos/aplicativo.php"); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<script>
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');

        if (activeTab) {
            const tabs = ['competencias', 'kpis', 'lecciones', 'okrs', 'plataforma', 'desempenio'];

            tabs.forEach(function(tab) {
                const tabButton = document.getElementById(tab + '-tab');
                const tabContent = document.getElementById(tab + '-tab-pane');

                if (tabButton) {
                    tabButton.classList.remove('active');
                    tabButton.setAttribute('aria-selected', 'false');
                }

                if (tabContent) {
                    tabContent.classList.remove('show', 'active');
                }
            });

            if (tabs.includes(activeTab)) {
                const tabButton = document.getElementById(activeTab + '-tab');
                const tabContent = document.getElementById(activeTab + '-tab-pane');

                tabButton.classList.add('active');
                tabButton.setAttribute('aria-selected', 'true');
                tabContent.classList.add('show', 'active');
            }
        }
    }

    var api = '<?php echo $url; ?>api/administrar/';

    var activar = false;

    function Elimimar_Cargo(id) {

        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar un cargo, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Elimimar_Cargo(' + id + ')"> Confirmar </button>');
        } else {

            jQuery.ajax({
                    url: api + "eliminar_cargo.php",
                    type: 'post',
                    data: {
                        id: id,
                        url: "?pg=administrar/cargos"
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


    function Ver_Info(tipo) {
        if (tipo == 1) {
            $("#modal_tooltips").modal("show");
            $("#body_tooltips").html("<h2>Nivel y Cargos Relacionados</h2>");
            $("#body_tooltips").append("1. Estratégicos: Presidente, Director, Gerente, Vicepresidente.<br> ");
            $("#body_tooltips").append("2. Táctico Administrativo: Subdirector, Subgerente, Jefe, Coordinador, Supervisor.<br> ");
            $("#body_tooltips").append("3. Táctico Comercial: Subdirector Comercial, Subgerente Comercial, Jefe Comercial, Coordinador Comercial, Supervisor Comercial, K.A.M. <br> ");
            $("#body_tooltips").append("4. Comercial: Vendedor, Representante, Visitador, Promotor. <br> ");
            $("#body_tooltips").append("5. Profesional sin personal a cargo: Profesionales sin colaboradores a cargo. <br> ");
            $("#body_tooltips").append("6. Operativo: Operarios, Auxiliares, Técnicos, Técnologos. <br> ");
            $("#body_tooltips").append("7. Apoyo Administrativo: Asistente administrativo, Auxiliar adminsitrativo, Soporte Administrativo. <br> ");
            $("#body_tooltips").append("8. Soporte Comercial: Auxiliar comercial, Asistente comercial, Call Center, Telemercadeo, Impulso. <br> ");
        }
    }

    function actualizarTab(tab) {
        const url = new URL(window.location);

        url.searchParams.set('tab', tab);

        window.history.pushState({}, '', url);

        location.reload();
    }

    function Guardar_Nombre_Etiqueta(nombre, id, id_empresa, id_user, tipo) {
        data = {
            id: id,
            id_empresa: id_empresa,
            id_user: id_user,
            nombre: nombre,
            tipo: tipo
        };
        jQuery.ajax({
                url: api + "guardar_nombre_etiqueta.php",
                type: 'post',
                data: data,
            }).done(function(resp) {
                toastr.success('Actualización de nombre de etiqueta realizado con éxito', '¡Éxito!');
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>
<script>
    const fechaActual = new Date();
    const mesActual = fechaActual.getMonth() + 1;
    const anioActual = fechaActual.getFullYear();
    const fechaFormateada = `${anioActual}-${mesActual < 10 ? '0' + mesActual : mesActual}`;
    flatpickr("#mes_fin", {
        dateFormat: "Y-m",
        mode: "single",
        monthSelector: true,
        disableMobile: true,
        altInput: true,
        altFormat: "F Y",
        locale: "es"
    });
    flatpickr("#mes_inicio", {
        dateFormat: "Y-m",
        mode: "single",
        monthSelector: true,
        disableMobile: true,
        altInput: true,
        altFormat: "F Y",
        locale: "es"
    });
</script>