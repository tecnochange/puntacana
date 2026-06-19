<script>
$(document).ready(function() {
    $('#menuEstrategia').collapse();
    $('#bt_estrategia_balanced').addClass('active');
});
</script>

<?php
	$id = $_GET["id"];
	$hoy = date("Y-m-d H:i:s");

	//CONSULTA PARA NUEVO CLIENTE
	//CONSULTA PARA NUEVO CLIENTE
	if($_POST["nombre"] != ""){

		if($_POST["id_registro"] != ""){
			mysqli_query($connect_okrs,"UPDATE Dimensiones SET nombre = '".$_POST["nombre"]."', 
			estado = '".$_POST["estado"]."' WHERE id = '".$_POST["id_registro"]."'  ");
		}
		else{
			mysqli_query($connect_okrs,"INSERT INTO Dimensiones (id_empresa, nombre, estado, created_at ) 
			VALUES 
			( '".$user_log['id_empresa']."', '".$_POST["nombre"]."', '".$_POST["estado"]."', '".$hoy."' ) ");  
		}

        echo '<script> window.location = "?pg=estrategica/dimensiones";</script>';//para evitar reinsersion  
	}
	
	
	//INFORMACION DE LA BATERIA
	$query = mysqli_query($connect_okrs,"SELECT * FROM Dimensiones WHERE id = '".$id."'  ");
	$data = mysqli_fetch_array($query);
	
?>

<div class="container"> 
    
    <a href="<?php echo $url; ?>?pg=estrategica/dimensiones" >
        <button class="btn btn-success mb-3"> << Volver</button>
    </a>

    <div class="card">

        <div class="card-header">
            <h3>Ficha Dimensión</h3>
        </div>
        
        <div class="card-body">   
    
            <form action="" method="post">
            <div class="row">

                <div class="col-md-12">
                    <input type="hidden" name="id_registro" value="<?php echo $id; ?>">
                </div>

                

                <div class="col-md-8" style="margin-bottom: 10px">
                    <lable>Nombre Dimensión</lable>
                    <input type="text" class="form-control" name="nombre" value="<?php echo $data["nombre"]; ?>">
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
                

                <div class="col-md-12" style="margin-bottom: 10px">
                    <button type="submit" id="sidebarCollapse" class="btn btn-success btn-block btn-sm" >
                        Guardar
                    </button>
                </div>
                
                <?php if($id){ ?>
                <div class="col-md-12" style="margin-tp: 30px; display: none">
                    <button type="button"  class="btn btn-danger btn-sm" onClick="Elimimar_Cargo(<?php echo $id; ?>)" >
                        <i class="bx bx-trash"></i> Eliminar
                    </button>
                </div>
                <?php } ?>

            </div>
            </form>
        </div> 
        
    </div>
    
</div>


<script>
    var api = '<?php echo $url; ?>api/administrar/';
    
    var activar = false;
    function Elimimar_Cargo(id){
        
        if(activar == false){
            $("#modal_general").modal("show");
            $("#modal_body").html('Está a punto de eliminar un cargo, esta acción es irreversible ¿está seguro?<br><br>');
            $("#modal_body").append('<button type="button" class="btn btn-danger btn-sm" onclick="activar= true; Elimimar_Cargo('+id+')"> Confirmar </button>');  
        }
        else{
            
            jQuery.ajax({
                url: api+"eliminar_cargo.php",
                type:'post',
                data: {id: id, url:"?pg=administrar/cargos"},
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
