<script>
    $(document).ready(function () {
        $("#bt_academia_cursos").addClass("active");
        $("#mod_academia").addClass("active_qa");
    });
</script>

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
				mysqli_query($connect_academia,"UPDATE Cursos SET imagen = '".$archivo."' WHERE id = '".$_POST["id_registro"]."'  ");
			}
            
			mysqli_query($connect_academia,"UPDATE Cursos SET 
            id_url = '".$_POST["id_url"]."', 
            id_programa = '".$_POST["id_programa"]."', 
			nombre = '".$_POST["nombre"]."', 
            nombre_corto = '".$_POST["nombre_corto"]."', 
            descripcion_corto = '".$_POST["descripcion_corto"]."', 
            descripcion_corto_ingles = '".$_POST["descripcion_corto_ingles"]."', 
            descripcion = '".$_POST["descripcion"]."', 
            descripcion_ingles = '".$_POST["descripcion_ingles"]."', 
            fecha_inicia = '".$_POST["fecha_inicia"]."', 
            fecha_termina = '".$_POST["fecha_termina"]."', 
            no_modulos = '".$_POST["no_modulos"]."', 
            tiempo = '".$_POST["tiempo"]."', 
            tiempo_ingles = '".$_POST["tiempo_ingles"]."', 
            estado = '".$_POST["estado"]."'
            WHERE id = '".$_POST["id_registro"]."'  ");

		}
		else{
            
            include("app/controllers/subir_documento.php");
			$archivo = Subir_Documento($_FILES['archivo']);	
            
			mysqli_query($connect_academia,"INSERT INTO Cursos (id_empresa, id_url, id_programa, nombre, nombre_corto, descripcion_corto, descripcion_corto_ingles, descripcion, descripcion_ingles, fecha_inicia, fecha_termina, no_modulos, tiempo, tiempo_ingles, imagen, estado, created_at ) 
			VALUES 
			( '".$_SESSION["id_empresa"]."', '".$_POST["id_url"]."', '".$_POST["id_programa"]."', '".$_POST["nombre"]."', '".$_POST["nombre_corto"]."', '".$_POST["descripcion_corto"]."', '".$_POST["descripcion_corto_ingles"]."', '".$_POST["descripcion"]."', '".$_POST["descripcion_ingles"]."', 
            '".$_POST["fecha_inicia"]."', '".$_POST["fecha_termina"]."',  '".$_POST["no_modulos"]."', '".$_POST["tiempo"]."', '".$_POST["tiempo_ingles"]."', '".$archivo."', '".$_POST["estado"]."', '".$hoy."' ) ");
		}
		echo '<script> window.location = "'.$url_gestion.'?pg=goforexpert/admin/cursos";</script>';//para evitar reinsersion
	}
	
	
	//INFORMACION DE LA BATERIA
	$query = mysqli_query($connect_academia,"SELECT * FROM Cursos WHERE id = '".$id."' ");
	$data = mysqli_fetch_array($query);
	
?>





<?php echo $respuesta; ?>

<div class="container-fluid">
	
	<nav aria-label="breadcrumb" style=" margin-top: 10px; margin-bottom: 10px" >
		<ol class="breadcrumb" style="background-color: rgba(0,0,0,0); margin-bottom:0px">
			<li class="breadcrumb-item"><a href="?pg=home">Home</a></li>
			<li class="breadcrumb-item active" aria-current="page"><a href="?pg=goforexpert/admin/cursos">Cursos</a></li>
			<li class="breadcrumb-item active" aria-current="page">Detalle</li>
		</ol>
	</nav>
	
	<div class="card">

        <div class="card-body">
      
            <form action="" method="post" enctype="multipart/form-data">
                <div class="row">
                
                    <input type="hidden" name="id_registro" value="<?php echo $id; ?>">
                    
                    <div class="col-md-4 item_input">
                        <label class="ti_label">Programa</label>
                        <select class="form-control" name="id_programa" required>
                            <option value="">Selecciona...</option>
                            <?php
                            
                            $queryPro = mysqli_query($connect_academia,"SELECT * FROM Programas WHERE id_empresa = '".$_SESSION["id_empresa"]."'  ");
	                        while($dataPro = mysqli_fetch_array($queryPro)){
                               if($dataPro["id"] == $data["id_programa"]){
								    echo '<option value="'.$dataPro["id"].'" selected="selected">'.$dataPro["nombre"].'</option>';
								}
								else{
								    echo '<option value="'.$dataPro["id"].'">'.$dataPro["nombre"].'</option>';
								}
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4 item_input">
                        <label class="ti_label">Nombre completo del curso</label>
						<input type="text" class="form-control" name="nombre" required value="<?php echo $data["nombre"]; ?>"> 
                    </div>
					
					<div class="col-md-4 item_input">
                        <label class="ti_label">Nombre corto del curso</label>
						<input type="text" class="form-control" name="nombre_corto" required value="<?php echo $data["nombre_corto"]; ?>"> 
                    </div>

                    <div class="col-md-4 item_input">
                        <label class="ti_label">Fecha Inicia</label>
						<input type="date" class="form-control" name="fecha_inicia" required value="<?php echo $data["fecha_inicia"]; ?>"> 
                    </div>
                    
                    <div class="col-md-4 item_input">
                        <label class="ti_label">Fecha Termina</label>
						<input type="date" class="form-control" name="fecha_termina" required value="<?php echo $data["fecha_termina"]; ?>"> 
                    </div>
					
					
					
					<div class="col-md-4 item_input">
                        <label class="ti_label">no_modulos</label>
						<input type="text" class="form-control" name="no_modulos" required value="<?php echo $data["no_modulos"]; ?>"> 
                    </div>
                    
                    <div class="col-md-4 item_input">
                        <label class="ti_label">Duración</label>
						<input type="text" class="form-control" name="tiempo" required value="<?php echo $data["tiempo"]; ?>"> 
                    </div>

                    <div class="col-md-4 item_input">
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
                    
                    <div class="col-md-4 item_input">
                                <label class="ti_label">Cargar Imágen </label>
                                <input type="file" name="archivo" class="form-control" accept="image/*" >
                            </div>
                    <?php if($data["imagen"] != ""){ ?>
                    <div class="col-md-4 item_input">
                                <img src="<?php echo $url; ?>/resources/<?php echo $data["imagen"]; ?>" width="100%" />
                    </div>
                    <?php } ?>
                    
                    <div class="col-md-12 item_input">
                        <label class="ti_label">Descripción Corta</label>
                        <textarea class="form-control" rows="2" name="descripcion_corto" required><?php echo $data["descripcion_corto"]; ?></textarea>
                    </div>

                    <div class="col-md-12 item_input">
                        <label class="ti_label">Resumen del curso</label>
                        <textarea class="form-control" rows="5" name="descripcion" required><?php echo $data["descripcion"]; ?></textarea>
                    </div>

                   <div class="col-md-12" style="margin-top: 15px; text-align: left;">
                        <button type="submit" class="btn btn-success btn-block" style="border-radius: 30px;">Guardar</button>
                    </div>
                            
                </div>
            </form>
    
        </div>
    </div>

</div>








<script>
$( document ).ready(function() {
    $("#bt_sala").addClass( "btn-primary" );
});
</script>