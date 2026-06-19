<script>
    $(document).ready(function() {
        $(".menu_section").addClass("active");
        // $("#nav_competencias").addClass("active");
        jQuery("#menu_competencias").css("display", "none");
        $("#bt_comp_configurar").addClass("current-page");
    });
</script>

<?php
$hoy = date("Y-m-d H:i:s");
//CONSULTA PARA NUEVO CLIENTE
//CONSULTA PARA NUEVO CLIENTE
if ($_POST["guardar_configruar"] != "") {
    $_SESSION["anio_ciclo"] = $_POST["anio_ciclo"];
    $_SESSION["ciclo"] = $_POST["ciclo"];
    echo '<script> window.location = "?pg=competencias_pc/configurar";</script>'; //para evitar reinsersion  
}

//INFORMACION DE LA BATERIA
$query = mysqli_query($connect_valentina, "SELECT * FROM Empresas WHERE id = '" . $_SESSION["id_empresa"] . "'  ");
$data = mysqli_fetch_array($query);
?>

<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Inicio</a></li>
            <li class="breadcrumb-item active" aria-current="page"><a href="">Configurar</a></li>
        </ol>
    </nav>
    <div class="card">
        <div class="card-body">
            <form action="" method="post">
                <div class="row">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <h2>Seleccionar Ciclo</h2>
                                <input type="hidden" name="guardar_configruar" value="true">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-2">
                                <lable>Año:</lable>
                                <select class="form-control form-control-sm" name="anio_ciclo" id="anio_ciclo">
                                    <option value="">Por Año...</option>
                                    <?php
                                    $query = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' ");
                                    while ($dataCiclos = mysqli_fetch_array($query)) {
                                        if ($_SESSION["ciclo"] ==  $dataCiclos["id"]) {
                                            echo '<option value="' . $dataCiclos["anio"] . '" selected>' . $dataCiclos["anio"] . '</option>';
                                        } else {
                                            echo '<option value="' . $dataCiclos["anio"] . '">' . $dataCiclos["anio"] . '</option>';
                                        }
                                    }

                                    ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                            <lable>Ciclo:</lable>
                                <select class="form-control form-control-sm" name="ciclo" id="ciclo">
                                    <option value="">Por Ciclo...</option>
                                    <?php
                                    $query = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' ");
                                    while ($dataCiclos = mysqli_fetch_array($query)) {
                                        if ($_SESSION["ciclo"] ==  $dataCiclos["id"]) {
                                            echo '<option value="' . $dataCiclos["id"] . '" selected>' . $dataCiclos["nombre"] . '</option>';
                                        } else {
                                            echo '<option value="' . $dataCiclos["id"] . '">' . $dataCiclos["nombre"] . '</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
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
<script>
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
</script>