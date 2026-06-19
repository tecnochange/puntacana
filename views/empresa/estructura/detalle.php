<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_estructura').addClass('active');
});
</script>

<?php
include("views/administrar/etiquetas.php");
$id = $_GET["id"];
$hoy = date("Y-m-d H:i:s");

//CONSULTA PARA NUEVO CLIENTE
//CONSULTA PARA NUEVO CLIENTE
if ($_POST["guardar_formulario"]) {

    $vp = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id = " . $_POST["vicepresidencia"] . "");
    $datavp = mysqli_fetch_array($vp);

    $area = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id = " . $_POST["area"] . "");
    $dataarea = mysqli_fetch_array($area);


    if ($_POST["id_registro"] != "") {
        $sentecia = "
			UPDATE Estructura_Empresa SET
            compania = '" . $_POST["compania"] . "',
            vicepresidencia = '" . $_POST["vicepresidencia"] . "',
            area = '" . $_POST["area"] . "',
            unidad_organizativa = '" . $_POST["unidad_organizativa"] . "',
            nivel_jerarquico = '" . $_POST["nivel_jerarquico"] . "',
            estado = '" . $_POST["estado"] . "',
            updated_at = '" . $hoy . "'
			WHERE id = '" . $_POST["id_registro"] . "'
			";
        // echo $sentecia;
        mysqli_query($connect_admin, $sentecia);

        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        $accion = 'ACTUALIZAR';
        $descripcion = 'Actualización de la estructura ' . $datavp["nombre"]. ' > '.$dataarea["nombre"].' > '.$_POST["unidad_organizativa"];
        $modulo = 'Estructura Organizacional';
        GuardarAuditoria( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $modulo );
    } 
    else {
        $sentencias = "
			INSERT INTO Estructura_Empresa ( id_empresa, compania, vicepresidencia, area, unidad_organizativa, nivel_jerarquico, estado, created_at ) 
			VALUES 
			( '" . $_SESSION['id_empresa'] . "', '" . $_POST["compania"] . "',  '" . $_POST["vicepresidencia"] . "', '" . $_POST["area"] . "', '" . $_POST["unidad_organizativa"] . "', '" . $_POST["nivel_jerarquico"] . "', '" . $_POST["estado"] . "', '" . $hoy . "'   )
		";

        mysqli_query($connect_admin, $sentencias);

        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        $accion = 'CREAR';
        $descripcion = 'Creación de la estructura ' . $datavp["nombre"]. ' > '.$dataarea["nombre"].' > '.$_POST["unidad_organizativa"];
        $modulo = 'Estructura Organizacional';
        GuardarAuditoria( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $modulo );
    }

    echo '<script> window.location = "?pg=empresa/estructura_empresa";</script>'; //para evitar reinsersion
}


//INFORMACION DE LA BATERIA
$query = mysqli_query($connect_admin, "SELECT * FROM Estructura_Empresa WHERE id = '" . $id . "' ");
$data = mysqli_fetch_array($query);

?>

<div class="container">

    <a href="<?php echo $url; ?>?pg=empresa/estructura_empresa" >
        <button class="btn btn-success mb-3"> << Volver</button>
    </a>

    <div class="card">
        <div class="card-header">
            <h3>Detalle de la Estructura</h3>
        </div>

        <form action="" method="POST">
        <input type="hidden" name="guardar_formulario" value="true">
        <input type="hidden" name="id_registro" value="<?php echo $id; ?>">
        <div class="card-body">
            <div class="row">

                <div class="col-md-3 mb-2">
                    <lable>Nombre de la Empresa</lable>
                    <input type="text" class="form-control" name="compania" value="<?php echo $data["compania"]; ?>">
                </div>

                <div class="col-md-3" style="margin-bottom: 10px">
                    <lable>Vicepresidencia</lable>
                    <select class="form-control" name="vicepresidencia" required>
                        <option value="">Seleccione...</option>
                        <?php
                        $queryVicepresidencias = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia 
                        WHERE id_empresa = '".$user_log["id_empresa"]."' ORDER BY nombre ASC ");
                        while ($dataVicepresidencia = mysqli_fetch_array($queryVicepresidencias)) {
                                if ($dataVicepresidencia["id"] == $data["vicepresidencia"]) {
                                    echo '
                                  <option value="' . $dataVicepresidencia["id"] . '" selected>' . $dataVicepresidencia["nombre"] . '</option>
                                  ';
                                } else {
                                    echo '
                                  <option value="' . $dataVicepresidencia["id"] . '">' . $dataVicepresidencia["nombre"] . '</option>
                                  ';
                                }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <lable>Áreas</lable>
                    <select class="form-control" name="area" required>
                        <option value="">Seleccione...</option>
                        <?php
                            $queryAreas = mysqli_query($connect_admin, "SELECT * FROM Areas 
                            WHERE id_empresa = '".$user_log["id_empresa"]."' AND estado = 1 ORDER BY nombre ASC ");
                            while ($dataArea = mysqli_fetch_array($queryAreas)) {
                                if ($dataArea["id"] == $data["area"]) {
                                    echo '
                                  <option value="' . $dataArea["id"] . '" selected>' . $dataArea["nombre"] . '</option>
                                  ';
                                } else {
                                    echo '
                                  <option value="' . $dataArea["id"] . '">' . $dataArea["nombre"] . '</option>
                                  ';
                                }
                            }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 mb-2" >
                    <lable>Unidad Organizativa</lable>
                    <input type="text" class="form-control" name="unidad_organizativa" value="<?php echo $data["unidad_organizativa"]; ?>">
                </div>

                <div class="col-md-3 mb-3">
                    <lable>Nivel Jerarquico</lable>
                    <select class="form-control" style="width: 100%" name="nivel_jerarquico">
                        <option value="">Seleccione...</option>
                        <?php
                        $queryNivel = mysqli_query($connect_admin, "SELECT * FROM Nivel_Jerarquico WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND estado = 1 ORDER BY nombre ASC ");
                        while ($dataNivel = mysqli_fetch_array($queryNivel)) {
                                if ($dataNivel["id"] == $data["nivel_jerarquico"]) {
                                    echo '
                                  <option value="' . $dataNivel["id"] . '" selected>' . $dataNivel["nombre"] . '</option>
                                  ';
                                } else {
                                    echo '
                                  <option value="' . $dataNivel["id"] . '">' . $dataNivel["nombre"] . '</option>
                                  ';
                                }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <lable>Estado</lable>
                        <select class="form-control" name="estado">
                        <option value="">Selecciona...</option>
                        <?php
                            foreach ($Array_Estado as $nivel) {
                                if ($data["estado"] == $nivel[0]) {
                                    echo '<option value="' . $nivel[0] . '" selected>' . $nivel[1] . '</option>';
                                } else {
                                    echo '<option value="' . $nivel[0] . '">' . $nivel[1] . '</option>';
                                }
                        }
                        ?>
                        </select>
                </div>

                <div class="col-md-12 mb-2" >
                    <button type="submit" id="sidebarCollapse" class="btn btn-success">
                         Guardar
                    </button>
                </div>

            </div>
        </div>
        </form>
    </div>
</div>
















<script>
    var api = '<?php echo $url; ?>api/administrar/';

    function CargarNivelJerarquia(id) {
        $('#jerarquia').html('');
        jQuery.ajax({
                url: api + "cargar_niveles_jerarquia.php",
                type: 'post',
                data: {
                    id: id
                },
            }).done(function(resp) {
                $("#jerarquia").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }

    var activar = false;

    function Eliminar_Estructura(id) {

        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar una estructura, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Eliminar_Estructura(' + id + ')"> Confirmar </button>');
        } else {

            jQuery.ajax({
                    url: api + "eliminar_area.php",
                    type: 'post',
                    data: {
                        id: id,
                        url: "?pg=administrar/estructura"
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
    $("#bt_admin_estructura_empresa").addClass("current-page");
</script>