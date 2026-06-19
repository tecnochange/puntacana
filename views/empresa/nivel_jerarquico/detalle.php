<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_nivel_jerarquico').addClass('active');
});
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
        color: #007ae1;
    }
</style>
<?php
include("views/administrar/etiquetas.php");
$id = $_GET["id"];
$hoy = date("Y-m-d H:i:s");

//CONSULTA PARA NUEVO CLIENTE
//CONSULTA PARA NUEVO CLIENTE
if ($_POST["nombre"] != "") {

    if ($_POST["id_registro"] != "") {
        $sentecia = "
			UPDATE Nivel_Jerarquico  SET nivel = '" . $_POST["nivel"] . "', nombre = '" . $_POST["nombre"] . "', 
			estado =  '" . $_POST["estado"] . "'  
			WHERE id = '" . $_POST["id_registro"] . "'
			";
        mysqli_query($connect_admin, $sentecia);

        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        $accion = 'ACTUALIZAR';
        $descripcion = 'Actualización de nivel jerárquico ' . $_POST["nombre"];
        $modulo = 'Nivel Organizacional';
        GuardarAuditoria( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $modulo );

    } 
    else {
        $sentencias = "
			INSERT INTO Nivel_Jerarquico ( id_empresa , nivel , nombre ,estado, created_at ) 
			VALUES 
			( '" . $_SESSION['id_empresa'] . "', '" . $_POST["nivel"] . "', '" . $_POST["nombre"] . "', '" . $_POST["estado"] . "', '" . $hoy . "'   )
			";
        mysqli_query($connect_admin, $sentencias);

        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        //BLOQUE PARA GUARDAR LA BITACORA
        $accion = 'CREAR';
        $descripcion = 'Creación de Nivel Jerárquico ' . $_POST["nombre"];
        $modulo = 'Nivel Organizacional';
        GuardarAuditoria( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $modulo );

    }

    echo '<script> window.location = "?pg=empresa/nivel_jerarquico";</script>'; //para evitar reinsersion  
}


//INFORMACION DE LA BATERIA
$query = mysqli_query($connect_admin, "SELECT * FROM Nivel_Jerarquico WHERE id = '" . $id . "' ");
$data = mysqli_fetch_array($query);

?>

<div class="container">

    <a href="<?php echo $url; ?>?pg=empresa/nivel_jerarquico" >
        <button class="btn btn-success mb-3"> << Volver</button>
    </a>
    <div class="card mb-3">
        
        <div class="card-header">
            <h3>Ficha Nivel Jerárquico</h3>
        </div>
        <form action="" method="POST">
        <input type="hidden" name="guardar_formulario" value="true">
        <input type="hidden" name="id_registro" value="<?php echo $id; ?>">
        <div class="card-body">
            <div class="row">
                        <div class="col-md-4" style="margin-bottom: 10px">
                            <lable>Nivel Jerárquico</lable>
                            <select class="form-control" name="nivel">
                                <option value="">Seleccione..</option>
                                <?php
                                foreach ($Array_Nivel as $nivel) {
                                    if ($data["nivel"] == $nivel[0]) {
                                        echo '<option value="' . $nivel[0] . '" selected>' . $nivel[1] . '</option>';
                                    } else {
                                        echo '<option value="' . $nivel[0] . '">' . $nivel[1] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4" style="margin-bottom: 10px">
                            <lable>Nombre de nivel</lable>
                            <input type="text" class="form-control" name="nombre" value="<?php echo $data["nombre"]; ?>" required>
                        </div>
                        
                        <div class="col-md-4" style="margin-bottom: 10px">
                            <lable>Estado *</lable>
                            <select class="form-control" name="estado">
                                <option value="">Seleccione..</option>
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

                        <div class="col-md-12">
                            <button class="btn btn-success" type="submit">Guardar</button>
                        </div>
            </div>
        </div>
        </form>

    </div>
</div>



<script>
    var api = '<?php echo $url; ?>api/administrar/';
    var activar = false;

    function Eliminar_Nivel(id, id_empresa, id_user) {

        if (activar == false) {
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar un nivel jerárquico, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Eliminar_Nivel(' + id + ',' + id_empresa + ',' + id_user + ')"> Confirmar </button>');
        } else {

            jQuery.ajax({
                    url: api + "eliminar_nivelj.php",
                    type: 'post',
                    data: {
                        id: id,
                        id_empresa: id_empresa,
                        id_user: id_user,
                        url: "?pg=administrar/nivel_jerarquico"
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
    $("#bt_nivel_jerarquico").addClass("current-page");
</script>