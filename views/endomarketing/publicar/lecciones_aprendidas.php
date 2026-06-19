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
    $queryEmplead = mysqli_query($connect_valentina,"SELECT * FROM Empleados WHERE id = '".$_SESSION['id_user_valentina']."' ");
	$dataEmplead = mysqli_fetch_array($queryEmplead);

    $queryCargo = mysqli_query($connect_valentina,"SELECT * FROM Cargos WHERE id = '".$dataEmplead['id_cargo']."' ");
	$dataCargo = mysqli_fetch_array($queryCargo);

    $queryArea = mysqli_query($connect_valentina,"SELECT * FROM Areas WHERE id = '".$dataCargo['id_area']."' ");
	$dataArea = mysqli_fetch_array($queryArea);	

    $hoy = date("Y-m-d H:i:s");
	
	if($_POST["area"] != "" && $_POST["descripcion"] != ""  ){
		
		include("app/controllers/subir_documento.php");
		//registramos los reconocimientos a los areas
		foreach ($_POST["area"] as &$valor) {
			
			mysqli_query($connect_clima,"INSERT INTO Publicaciones (id_user, id_empresa, id_proceso, 
			titulo, descripcion, tipo, visibilidad, copa, fecha_publicacion, estado, created_at) 
			VALUES 
			('".$_SESSION['id_user_valentina']."', '".$_SESSION['id_empresa']."', '".$dataArea['id']."', 
			'".$valor."', '".$_POST["descripcion"]."', '11', '".$_POST["visibilidad"]."', '".$_POST["leccion"]."', '".$_POST["fecha_publicacion"]."', '1', '".$hoy."'  ) ");
            
            $id_publicacion = mysqli_insert_id($connect_clima);
            
            if($_FILES['archivo_multiples'] != ""){
				Cargar_Archivos_Multiples_Clima($_FILES['archivo_multiples'],$id_publicacion, 3 ,$hoy,$connect_clima);
			}
            
            
            $queryEmpTmp = mysqli_query($connect_valentina,"SELECT * FROM Empleados WHERE id = '".$valor."' ");
            $dataEmpTmp = mysqli_fetch_array($queryEmpTmp);

            $nombre_mail = $dataEmpTmp["nombre"]." ".$dataEmpTmp["nombre_2"]." ".$dataEmpTmp["apellidos"]." ".$dataEmpTmp["apellidos_2"];
            $correo_mail = $dataEmpTmp["correo"];
            if($correo_mail != ""){
                //include("mail/nuevo_reconocimiento.php");
            }
            
            
			
		}

		
		echo '<script>location.href ="?pg=endomarketing/muro";</script>';	
			
		
	}
	
	
	
	
?>

<div class="container-fluid">
    
	<!-- Ficha -->
    <form action="" method="post" enctype="multipart/form-data">   
    <div class="card">
		
        <div class="card-body">
            
            <h3>Lecciones Aprendidas</h3>
      		<p>¿Quieres publicar un reconocimiento?, solo debes completar el formulario y dar clic en publicar</p>
   
        	<select name="visibilidad" required="required" class="form-control" style="margin-bottom:15px; width:auto; display: inline-table; color:#4286f5">
            	<option value="">Selecciona la visibilidad...</option>
                <option value="1">Para la organización / institución</option>
                <option value="2">Para el área o proceso</option>
                <option value="3">Privado</option>
            </select>
            
            <div style="padding: 15px; color: #ea4235;">Selecciona 1 o varias áreas <b><span></span></b></div>
            <input type="text" placeholder="Busca y selecciona el área que deseas reconocer.." class="form-control" id="myInput" />
            
            <div style=" margin-top:15px; max-height:200px; overflow:auto">
            <table width="100%" style="color: #4286f5; font-size: 13px;">
            	<tbody id="myTable">
                 <?php
					$queryAreas = mysqli_query($connect_valentina,"SELECT * FROM Areas WHERE id_empresa = '".$_SESSION["id_empresa"]."' ORDER BY nombre ASC ");
					while($dataAreas = mysqli_fetch_array($queryAreas)){
						echo '
						<tr>
						<td>
							&nbsp;&nbsp;&nbsp;&nbsp;'.$dataAreas["nombre"].'
							<input type="checkbox" name="area[]" value="'.$dataAreas["id"].'" class="checkbox_list" onchange="Select_This(this)" />
						</td>
						</tr>
						';
					}
				?>
                </tbody> 
            </table>
            </div>
<br>
        	<select name="leccion" required="required" class="form-control" style="margin-bottom:15px;">
            	<option value="">Selecciona la lección...</option>
                <option value="1">Que hicimos bien</option>
                <option value="2">Que debemos mejorar</option>
                <option value="3">Que debemos empezar a hacer</option>
            </select>
            
            <input type="file" name="archivo_multiples[]" class="form-control"  multiple="" accept="image/*" class="entradas_texto" style="margin-bottom: 15px; display: none">
			
			
            <label> Fecha Publicación </label>
            <input type="date"  class="form-control" name="fecha_publicacion" value="<?php echo $data["fecha_publicacion"]; ?>" style="margin-bottom:15px;">
            
           
            
            <textarea name="descripcion" required="required" rows="4" class="form-control" rows="5" style="height: auto; margin-bottom:15px" placeholder="Ingresa una descripción detallada para la lección aprendida que deseas enviar..."><?php echo $data["descripcion"] ?></textarea> 


            <div align="right" style="margin-top:20px">
                <input type="submit" value="Publicar" class="btn btn-primary btn-md bt_blue">
            </div>
        
        </div>
        
        
    </div>
    </form>
    <?php echo $respuesta ?> 

 
   
</div>



<script>

function Select_This(elemt){
	if( $(elemt).prop('checked') == false ){
		$(elemt).parent().addClass( "name_fil" );
	}
	else{
		$(elemt).parent().addClass( "name_fil_off" );
	}
}




$(document).ready(function(){
	  $("#myInput").on("keyup", function() {
		var value = $(this).val().toLowerCase();
		
		$("#myTable tr").filter(function() {
		  $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		});
		
		$(".name_fil_off").parent().show();
		
	  });
});
</script>

<style>
.checkbox_list{
	float: left;
    width: 18px;
    height: 18px;
}
</style>

