<script>
$(document).ready(function() {
    $('#menuEmpresa').collapse();
    $('#bt_empresa_cargos').addClass('active');
});
</script>


<?php
include("views/administrar/etiquetas.php");
	$id = $_GET["id"];
	$hoy = date("Y-m-d H:i:s");

    if($_GET["id"]){
        $_SESSION["id_cargo_edit"] = $_GET["id"];
    }

	//CONSULTA PARA NUEVO CLIENTE
	//CONSULTA PARA NUEVO CLIENTE
	if($_POST["nombre"] != ""){

		if($_POST["id_registro"] != ""){
			mysqli_query($connect_admin,"UPDATE Cargos SET nivel_cargo = '".$_POST["nivel_cargo"]."', nombre = '".$_POST["nombre"]."', 
			padre = '".$_POST["padre"]."', id_area = '".$_POST["id_area"]."', id_direccion = '".$_POST["id_direccion"]."', estado = '".$_POST["estado"]."' WHERE id = '".$_POST["id_registro"]."'  ");

            //BLOQUE PARA GUARDAR LA BITACORA
            //BLOQUE PARA GUARDAR LA BITACORA
            //BLOQUE PARA GUARDAR LA BITACORA
            $accion = 'ACTUALIZAR';
            $descripcion = 'Actualización de cargo ' . $_POST["nombre"];
            $modulo = 'Cargos';
            GuardarAuditoria( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $modulo );

		}
		else{

            echo "INSERT INTO Cargos (id_empresa, nivel_cargo, nombre, padre, id_area, id_direccion, estado, created_at ) 
			VALUES 
			( '".$_SESSION['id_empresa']."', '".$_POST["nivel_cargo"]."', '".$_POST["nombre"]."', '".$_POST["padre"]."', '".$_POST["id_area"]."',  '".$_POST["id_direccion"]."',  '".$_POST["estado"]."', '".$hoy."' ) ";

            
			mysqli_query($connect_admin,"INSERT INTO Cargos (id_empresa, nivel_cargo, nombre, padre, id_area, id_direccion, estado, created_at ) 
			VALUES 
			( '".$_SESSION['id_empresa']."', '".$_POST["nivel_cargo"]."', '".$_POST["nombre"]."', '".$_POST["padre"]."', '".$_POST["id_area"]."',  '".$_POST["id_direccion"]."',  '".$_POST["estado"]."', '".$hoy."' ) "); 

            //BLOQUE PARA GUARDAR LA BITACORA
            //BLOQUE PARA GUARDAR LA BITACORA
            //BLOQUE PARA GUARDAR LA BITACORA
            $accion = 'CREAR';
            $descripcion = 'Creación de cargo ' . $_POST["nombre"];
            $modulo = 'Cargos';
            GuardarAuditoria( $user_log["id_empresa"], $user_log["id"], $accion, $descripcion, $modulo );
		}
        
        echo '<script> window.location = "?pg=empresa/cargos";</script>';//para evitar reinsersion  
	}
	
	
	//INFORMACION DE LA BATERIA
	$query = mysqli_query($connect_admin,"SELECT * FROM Cargos WHERE id = '".$_SESSION["id_cargo_edit"]."'  ");
	$data = mysqli_fetch_array($query);
	
?>

<div class="container">

    <a href="<?php echo $url; ?>?pg=empresa/cargos" >
        <button class="btn btn-success mb-3"> << Volver</button>
    </a>

    <div class="card mb-3">
        <div class="card-header">
            <h3>Ficha Cargo</h3>
        </div>

        <form action="" method="POST">
        <input type="hidden" name="guardar_formulario" value="true">
        <input type="hidden" name="id_registro" value="<?php echo $id; ?>">
        <div class="card-body">
            <div class="row">

            
                <div class="col-md-4" style="margin-bottom: 10px">
                    <lable>Nivel</lable>
                    <select class="form-control" name="nivel_cargo">
                        <option value="">Selecciona...</option>
                        <?php
                        foreach($Array_Nivel_Cargo as $nivel){
                            if($data["nivel_cargo"] == $nivel[0] ){
                                echo '<option value="'.$nivel[0].'" selected>'.$nivel[1].'</option>';
                            }
                            else{
                                echo '<option value="'.$nivel[0].'">'.$nivel[1].'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <div class="col-md-4" style="margin-bottom: 10px">
                    <lable>Nombre</lable>
                    <input type="text" class="form-control" name="nombre" value="<?php echo $data["nombre"]; ?>">
                </div>

                <div class="col-md-4" style="margin-bottom: 10px">
                    <lable>Reporte</lable>
                    <select class="form-control" name="padre">
                        <option value="0">Selecciona...</option>
                        <?php
                        $queryJer = mysqli_query($connect_admin,"SELECT * FROM Cargos WHERE id_empresa = '".$_SESSION['id_empresa']."' ORDER BY nombre ASC ");  
                        while($dataJer = mysqli_fetch_array($queryJer)){
                            if($data["padre"] == $dataJer["id"] ){
                                echo '<option value="'.$dataJer["id"].'" selected>'.$dataJer["nombre"].'</option>';
                            }
                            else{
                                echo '<option value="'.$dataJer["id"].'">'.$dataJer["nombre"].' </option>';
                            }
                            
                        }
                        ?>
                    </select>
                </div>
                
                <div class="col-md-4" style="margin-bottom: 10px">
                    <lable>Área</lable>
                    <select class="form-control" name="id_area">
                        <option value="">Selecciona...</option>
                        <?php
                        $queryJer = mysqli_query($connect_admin,"SELECT * FROM Areas WHERE id_empresa = '".$_SESSION['id_empresa']."'  ORDER BY nombre ASC ");  
                        while($dataJer = mysqli_fetch_array($queryJer)){
                            if($data["id_area"] == $dataJer["id"] ){
                                echo '<option value="'.$dataJer["id"].'" selected>'.$dataJer["nombre"].' - '.$dataJer["jerarquia"].'</option>';
                            }
                            else{
                                echo '<option value="'.$dataJer["id"].'">'.$dataJer["nombre"].' - '.$dataJer["jerarquia"].'</option>';
                            }
                            
                        }
                        ?>
                    </select>
                </div>
                
                <div class="col-md-4" style="margin-bottom: 10px">
                    <lable>Dirección</lable>
                    <select class="form-control" name="id_direccion">
                        <option value="">Selecciona...</option>
                        <?php
                        $queryJer = mysqli_query($connect_admin,"SELECT * FROM Areas WHERE id_empresa = '".$user_log['id_empresa']."' AND jerarquia = 2  ORDER BY nombre ASC ");  
                        while($dataJer = mysqli_fetch_array($queryJer)){
                            if($data["id_direccion"] == $dataJer["id"] ){
                                echo '<option value="'.$dataJer["id"].'" selected>'.$dataJer["nombre"].' - '.$dataJer["jerarquia"].'</option>';
                            }
                            else{
                                echo '<option value="'.$dataJer["id"].'">'.$dataJer["nombre"].' - '.$dataJer["jerarquia"].'</option>';
                            }
                            
                        }
                        ?>
                    </select>
                </div>
                
                <div class="col-md-4" style="margin-bottom: 10px">
                    <lable>Estado</lable>
                    <select class="form-control" name="estado">
                        <option value="">Selecciona...</option>
                        <?php
                        foreach($Array_Estado as $nivel){
                            if($data["estado"] == $nivel[0] ){
                                echo '<option value="'.$nivel[0].'" selected>'.$nivel[1].'</option>';
                            }
                            else{
                                echo '<option value="'.$nivel[0].'">'.$nivel[1].'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                

                <div class="col-md-12">
                    <button type="submit" class="btn btn-success " >
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
    
    var activar = false;
    function Elimimar_Cargo(id,id_empresa,id_user){
        
        if(activar == false){
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar un cargo, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Elimimar_Cargo(' + id + ',' + id_empresa + ',' + id_user + ')"> Confirmar </button>');  
        }
        else{
            
            jQuery.ajax({
                url: api+"eliminar_cargo.php",
                type:'post',
                data: {id: id, id_empresa: id_empresa,
                    id_user: id_user, url:"?pg=administrar/cargos"},
                }).done(function (resp){
                    $("#xscript").html(resp);
                })
                .fail(function(resp) {
                    console.log(resp);
                })
                .always(function(resp){
                }
            );
            
        }
    }
    

    function Ver_Info(tipo){
        if(tipo == 1){
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

<script>
    $(".menu_section").addClass("active");
	// $("#nav_empresa").addClass("active");
	jQuery("#menu_empresa").css("display", "none");
	$("#bt_admin_cargos").addClass("current-page");
</script>