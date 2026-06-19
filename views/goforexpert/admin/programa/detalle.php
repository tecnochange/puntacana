<?php

	$id = $_GET["id"];
	$hoy = date("Y-m-d H:i:s");

	//CONSULTA PARA NUEVO CLIENTE
	//CONSULTA PARA NUEVO CLIENTE
	if($_POST["nombre"] != ""){

		if($_POST["id_registro"] != ""){
			if($_FILES['archivo']["name"] != ""){
				include("app/controllers/subir_documento.php");
				$archivo = Subir_Documento($_FILES['archivo']);	
				mysqli_query($connect_academia,"UPDATE Programas SET imagen = '".$archivo."' WHERE id = '".$_POST["id_registro"]."'  ");
			}
			
			mysqli_query($connect_academia,"UPDATE Programas SET id_url = '".$_POST["id_url"]."', nombre = '".$_POST["nombre"]."', 
            nombre_ingles = '".$_POST["nombre_ingles"]."', descripcion = '".$_POST["descripcion"]."', 
            descripcion_ingles = '".$_POST["descripcion_ingles"]."', estado = '".$_POST["estado"]."' WHERE id = '".$_POST["id_registro"]."'  ");
		}
		else{
			include("app/controllers/subir_documento.php");
			$archivo = Subir_Documento($_FILES['archivo']);		
			
			mysqli_query($connect_academia,"INSERT INTO Programas (id_empresa, id_url, nombre, nombre_ingles, descripcion, descripcion_ingles, imagen, estado, created_at ) 
			VALUES 
			( '".$dtEmpleado["id_empresa"]."', '".$_POST["id_url"]."', '".$_POST["nombre"]."', '".$_POST["nombre_ingles"]."',  '".$_POST["descripcion"]."', '".$_POST["descripcion_ingles"]."', '".$archivo."', '".$_POST["estado"]."', '".$hoy."' ) ");
			
			
		}
		echo '<script> window.location = "?pg=goforexpert/admin/programas";</script>';//para evitar reinsersion
	}

	//INFORMACION DE LA BATERIA
	$query = mysqli_query($connect_academia,"SELECT * FROM Programas WHERE id = '".$id."' ");
	$data = mysqli_fetch_array($query);	
?>





<?php echo $respuesta; ?>

<div class="container-fluid">
	
	<nav aria-label="breadcrumb" style=" margin-top: 10px; margin-bottom: 10px" >
		<ol class="breadcrumb" style="background-color: rgba(0,0,0,0); margin-bottom:0px">
			<li class="breadcrumb-item"><a href="?pg=home">Home</a></li>
			<li class="breadcrumb-item active" aria-current="page"><a href="?pg=goforexpert/admin/programas">Programas</a></li>
			<li class="breadcrumb-item active" aria-current="page">Programa</li>
		</ol>
	</nav>
	
	<div class="card" style="margin-bottom: 15px">

        <div class="card-body">
      
            <form action="" method="post" enctype="multipart/form-data">
                <div class="row">
                
                    <input type="hidden" name="id_registro" value="<?php echo $id; ?>">

                    <div class="col-md-6 item_input">
                        <label class="ti_label">Nombre</label>
						<input type="text" class="form-control" name="nombre" required value="<?php echo $data["nombre"]; ?>"> 
                    </div>
 
                    

					<div class="col-md-6 item_input">
                        <label class="ti_label">Estado</label>
                        <select class="form-control" name="estado" required>
                            <option value="">Selecciona...</option>
                            <?php
                                foreach($Array_Estado as $estado){
									if($estado[0] == $data["estado"]){
										echo '<option value="'.$estado[0].'" selected="selected">'.$estado[1].'</option>';
									}
									else{
										echo '<option value="'.$estado[0].'">'.$estado[1].'</option>';
									}
								}
                            ?>
                        </select>
                    </div>

                    <div class="col-md-6 item_input">
                        <label class="ti_label">Cargar Imágen </label>
                        <input type="file" name="archivo" class="form-control" accept="image/*" >
                    </div>
                    <?php if($data["imagen"] != ""){ ?>
                    <div class="col-md-4 item_input">
                    	<img src="<?php echo $url; ?>/recursos/<?php echo $data["imagen"]; ?>" width="100%" />
                    </div>
					<?php } ?>
                    
                    
                    <div class="col-md-12 item_input">
                        <label class="ti_label">Descricpcion </label>
                        <textarea name="descripcion" class="form-control"><?php echo $data["descripcion"]; ?></textarea>
                    </div>
                    
                    <div class="col-md-12 item_input">
                        <label class="ti_label">Descricpción Ingles </label>
                        <textarea name="descripcion_ingles" class="form-control"><?php echo $data["descripcion_ingles"]; ?></textarea>
                    </div>

                   <div class="col-md-12" style="margin-top: 15px; text-align: left;">
                        <button type="submit" class="btn btn-success btn-block" style="border-radius: 30px;">Guardar</button>
                    </div>
                            
                </div>
            </form>
    
        </div>
    </div>

	

</div>





<style>
.item_input{
	margin-bottom:15px;
}
</style>



<script>
$( document ).ready(function() {
    $("#bt_sala").addClass( "btn-primary" );
});
	
var api = '<?php echo $api; ?>';

var activar = false;
function Eliminar(id){
	if(activar == false){
		$("#modal_body").html('Estas a punto de eliminar un expositor de esta actividad, esta acción es irreversible ¿Estás seguro?<br><br>');
		$("#modal_body").append('<button type="button" class="btn btn-danger" style="margin-right: 10px;" onclick="activar = true; Eliminar('+id+')">Eliminar</button>');
		$("#modal_general").modal('show');
	}
	else{
	
		jQuery.ajax({
			url: api+"quitar_speaker_lista.php",
			type:'post',
			data: {id: <?php echo $id; ?>, conferencista:id, url:"?pg=agenda/detalle&id=<?php echo $id; ?>"},
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
</script>