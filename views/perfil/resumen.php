<script>
$( document ).ready(function() {
    $("#bt_perfil_cuenta").addClass("active");
	$("#mod_home").addClass("active_qa");
});
</script>

<?php 
	$id = $_GET["id"];
	$hoy = date("Y-m-d H:i:s");

    if($_GET["id"]){
        $_SESSION["id_colaborador_edit"] = $_GET["id"];
    }

    if($_POST["guardar_formulario"]){

		if($_FILES["archivo"]["name"]){
            include("app/controllers/subir_documento.php");
			$archivo = Subir_Documento($_FILES["archivo"]);
            mysqli_query($connect_admin, "UPDATE Empleados SET foto = '".$archivo."' WHERE id = '".$user_log["id"]."' ");
		}
			
		$respuesta = '
			<div class="alert alert-success" role="alert">
				Los datos ha sido actualizados con éxito.
			</div>
		';
	}

	include("app/models/estructura/Colaboradores.php");
	$ClassColaboradores = new Colaboradores();

	$colaborador =  $ClassColaboradores->colaborador( $user_log["id"], $connect_admin);
    $foto = "https://goforagile.com/recursos/".$colaborador["foto"];
    if(!$colaborador["foto"]){
        $colaborador["foto"] = 'img_default.jpg';
    }

?>

<div class="container" style="max-width: 800px">

	<?php echo $respuesta; ?>
	
	<div class="card" >
		
		<div class="card-header">
			<h3>Mi Cuenta</h3>
		</div>
        
    	<div class="card-body">   
    
        	
	
            <div class="row">
				
				<div class="col-md-4">
					<img src="https://goforagile.com/recursos/<?php echo $colaborador["foto"]; ?>" style="width: 100%; max-width: 300px;" >
                </div>
				
				<div class="col-md-8">
					<h3><?php echo $colaborador["nombre"]." ".$colaborador["apellidos"]; ?></h3>
					<b><?php echo $colaborador["nombre_cargo"]; ?></b><br><br>
					
					Área: <?php echo $colaborador["nombre_area"]; ?><br>
					Documento: <?php echo $colaborador["documento"]; ?><br>
					Correo: <?php echo $colaborador["correo"]; ?><br>

                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="guardar_formulario" value="true">
                        <div class="col-md-12" style="margin-bottom: 10px">
                        <label>Fotografía (El peso max. 2 megas)</label>
                        <input type="file" id="archivo" name="archivo" class="form-control"
                            accept="image/png, image/jpeg">
                            <p style="color: #F44336; font-size: 16px;">Antes de cargar la imágen, verifique que la misma pese menos de 2 megas, ya que si pesa más. el sistema no la podrá cargar.</p>
                        
                    

                        <button type="submit" class="btn btn-primary w100 ">
                            <i class="fas fa-check"></i> Guardar
                        </button>

                    </form>
					
					
					<div align="center" style="margin-top: 20px">
						<a href="<?php echo $url; ?>?pg=perfil/cambiar_pass">
							<button type="button" class="btn btn-danger w-100 mb-1">Cambiar Contraseña</button>
						</a>
                        <a href="https://puntacana.goforagile.com/Politicas_Datos_GoforAgile_V1.pdf" target="_blank">
							<button type="button" class="btn w-100 mb-1" style="font-size: 13px;">Politica de Tratamiento y Protección de Datos Personales</button>
						</a>
					</div>
				</div>
				
                
				
				
				
				
				
				
				
				
				
				
				
            </div>
    	</div>
			
	</div>

</div>