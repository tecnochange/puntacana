<script>  
$(document).ready(function(){
    // $("#endomarketing_menu").addClass("active");
    // $("#desplegable_endomarketing").show();
    // $("#bt_wandbook_retos").addClass("active_item");
	$(".menu_section").addClass("active");
	// $("#nav_endomarketing").addClass("active");
	jQuery("#menu_endomarketing").css("display", "none");
	$("#bt_endo_retos").addClass("current-page");
});
</script>

<style>
    .active {
        font-weight: bold;
    }
    
</style>

<?php	
    $tipo_publicacion = $_GET["tp"];
    $txt_titulo = "";
    if($tipo_publicacion == 4){$txt_titulo = "Clasificado"; }
    if($tipo_publicacion == 5){$txt_titulo = "Nuevos Colaboradores"; }
    if($tipo_publicacion == 6){$txt_titulo = "SyST"; }
    if($tipo_publicacion == 7){$txt_titulo = "Celebración"; }
    if($tipo_publicacion == 8){$txt_titulo = "Calidad de Vida"; }
	if($tipo_publicacion == 9){$txt_titulo = "Noticias"; }

    $queryEmplead = mysqli_query($connect_valentina,"SELECT * FROM Empleados WHERE id = '".$_SESSION['id_user_valentina']."' ");
	$dataEmplead = mysqli_fetch_array($queryEmplead);

    $queryCargo = mysqli_query($connect_valentina,"SELECT * FROM Cargos WHERE id = '".$dataEmplead['id_cargo']."' ");
	$dataCargo = mysqli_fetch_array($queryCargo);

    $queryArea = mysqli_query($connect_valentina,"SELECT * FROM Areas WHERE id = '".$dataCargo['id_area']."' ");
	$dataArea = mysqli_fetch_array($queryArea);	

    $hoy = date("Y-m-d H:i:s");
	
	if($_POST["visibilidad"] != "" && $_POST["descripcion"] != ""  ){

		if($_POST["id"] != ""){
			mysqli_query($connect_clima,"UPDATE Publicaciones SET titulo = '".$_POST["titulo"]."', descripcion = '".utf8_encode($_POST["descripcion"])."', 
			visibilidad = '".$_POST["visibilidad"]."' WHERE id = '".$_POST["id"]."' ");
			echo '<script>location.href ="/?pg=home";</script>';
		
		}
		//REGISTRO NUEVO
		//REGISTRO NUEVO
		//REGISTRO NUEVO
		else{
			include("app/controllers/subir_documento.php");
			
			$script = "";
			if( $_POST["youtube"] ){
				
				$partes = explode("=", $_POST["youtube"] );
				
				$partes = explode("&", $partes[1] );
				
				if(count($partes) == 1){
					$script = $_POST["youtube"];
				}
				else{
					$script = $partes[0];
				}
			}
			
			//$archivo = Subir_Documento($_FILES['imagen']);			
			mysqli_query($connect_clima,"INSERT INTO Publicaciones (id_user, id_empresa, id_proceso, 
			descripcion, script, tipo, tipo_multimedia, visibilidad, fecha_publicacion, estado, created_at) VALUES 
			('".$_SESSION['id_user_valentina']."', '".$_SESSION['id_empresa']."', '".$user_log['id_departamento']."',  
			'".utf8_encode($_POST["descripcion"])."', '".$script."', '".$tipo_publicacion."', '".$_POST["tipo_multimedia"]."', '".$_POST["visibilidad"]."', '".$_POST["fecha_publicacion"]."', 1, '".$hoy."'  ) ");

			$id_publicacion = mysqli_insert_id($connect_clima);
			
			if($_FILES['archivo_multiples'] != ""){
				Cargar_Archivos_Multiples_Clima($_FILES['archivo_multiples'],$id_publicacion,1,$hoy,$connect_clima);
			}
            
            if($_FILES['archivo_multiples_docs'] != ""){
				Cargar_Archivos_Multiples_Clima($_FILES['archivo_multiples_docs'],$id_publicacion,2,$hoy,$connect_clima);
			}
			
			echo '<script> location.href ="?pg=endomarketing/muro";</script>';
		}
	}
	
	$text_visibilidad = 'Selecciona la visibilidad...';
	//VALIDAMOS SI ES UNA EDICION
	if($_GET["id"] != ""){
		//CONSULTAMOS LOS DATOS DE LA ASIGNACION
		$query = mysqli_query($connect_clima,"SELECT * FROM Publicaciones WHERE id = '".$_GET["id"]."' ");
		$data = mysqli_fetch_array($query);

		if($data["visibilidad"] == 1){$text_visibilidad = 'Para la organización / institución'; }
		if($data["visibilidad"] == 2){$text_visibilidad = 'Para el área o proceso'; }
		
		if( $data["id_user"] != $_SESSION['id_user_valentina'] ){
			echo '<script>location.href ="?pg=endomarketing/muro";</script>';
		}
	}
?>

<div class="container-fluid">
    

	<!-- Ficha -->
    <div class="card">
        
        <div class="card-body">
        
            <h3>Publicar <?php echo $txt_titulo; ?></h3>

            <div style="padding: 15px; color: #ea4235;"> 
                ¿Quieres publicar ?, solo debes completar el formulario y dar clic en publicar
            </div>
        
            <form action="" method="post" enctype="multipart/form-data">       
                <select name="visibilidad" required="required" class="form-control" style="margin-bottom:15px; width:auto; display: inline-table; color:#4286f5">
                    <option value="<?php echo $data["visibilidad"]; ?>"><?php echo $text_visibilidad; ?></option>
                    <option value="1">Para la organización / institución</option>
                    <option value="2">Para el área o proceso</option>
                </select>
	
                <textarea name="descripcion" required="required" rows="4" class="form-control" rows="5" style="height: auto; margin-bottom:15px" placeholder="Ingresa una descripción detallada y completa de la solicitud, requerimiento o información sobre el clasificado..."><?php echo $data["descripcion"] ?></textarea> 
				
				<label> Fecha Publicación </label>
            	<input type="date"  class="form-control" name="fecha_publicacion" value="<?php echo $data["fecha_publicacion"]; ?>" style="margin-bottom:15px;">

                <?php if($_GET["id"] == ""){ ?>
                <select name="tipo_multimedia" class="form-control multimedia" style="margin-bottom:15px; width:auto; display: inline-table; color:#4286f5" onchange="Tipo_Archivo(this.value)">
                    <option value="">Multimedia...</option>
                    <option value="1">Galeria de Imágenes</option>
                    <option value="2">Video de Youtube</option>
                </select>
                <?php } ?>

                <div id="tipo_archivos">
                </div>
                
                <div>
                    <div>* Para seleccionar varios archivos diferentes de CTRL+click y seleccione los archivos a cargar.</div>
                    <div style="color: #ff0606; font-size: 13px;">* Para seleccionar varios archivos consecutivos de SHIFT+click y seleccionr los archivos a cargar.</div>
                    <input type="file" name="archivo_multiples_docs[]" class="form-control"  multiple="" accept=".docx, application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint, application/pdf " class="entradas_texto">
                </div>


                <div align="right" style="margin-top:20px">
                    <input type="hidden" name="id" value="<?php echo $data["id"] ?>" />
                    <input type="submit" value="Publicar" class="btn btn-primary btn-md bt_blue">
                </div>

            </form>
        </div>
	</div>
 
    <?php echo $respuesta ?> 




    
</div>



<script>
	function Tipo_Archivo(val){
		if(val == 1){
			$("#tipo_archivos").html('<div style="color: #ff0606; font-size: 13px;">* Para seleccionar varios archivos diferentes de CTRL+click y seleccione los archivos a cargar.</div>');
			$("#tipo_archivos").append('<div style="color: #ff0606; font-size: 13px;">* Para seleccionar varios archivos consecutivos de SHIFT+click y seleccionr los archivos a cargar.</div>');
			$("#tipo_archivos").append('<input type="file" name="archivo_multiples[]" class="form-control"  multiple="" accept="image/*" class="entradas_texto">');
		}
		if(val == 2){
			$("#tipo_archivos").html('<textarea required="required" style="width:100%" rows="4" name="youtube" placeholder="Ingrese el link del video de youtube que desea publicar , que aparece en la ventana de su navegador..." class="entradas_texto"/></textarea> ');
		}
		if(val == ""){
			$("#tipo_archivos").html('');
		}

	}
</script>





