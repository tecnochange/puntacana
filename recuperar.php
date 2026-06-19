<?php
	session_start();
	include("app/connect.php");	


    //EDITAR O CREAR REGISTROS
    if( $_POST["correo"] != "" ){

        $queryVal = mysqli_query($connect_admin,"SELECT * FROM Empleados WHERE correo = '".$_POST["correo"]."' ");
        if($queryVal->num_rows > 0){
            
			$dataVal = mysqli_fetch_array($queryVal);
			
			$nombre = $dataVal["nombre"]." ".$dataVal["apellidos"];
            $correo = $_POST["correo"];
            //$correo = "eniac321@gmail.com";
			$password = $dataVal["password"];

            include("app/models/brevo/Brevo.php");
            include("app/models/brevo/plantillas.php"); 

            $asunto = "Gofor Agile - Recuperación de Contraseña";

            $plantilla = PlantillaRecordarPass( $nombre, $correo, $password );
            $ClassBrevo = new Brevo();
            $resp = $ClassBrevo->individual( "GoFor Agile", $asunto, $correo, $plantilla );

            $respuesta = '
                <div class="alert alert-success" role="alert">
                 	Hemos enviado un correo con tus datos de acceso.
                </div>
            ';
            /*
            echo '
                <script> window.location = "'.$url.'recuperar.php?msm=true"; </script>
            ';
            */
        }
        else{
            $respuesta = '
                <div class="alert alert-danger" role="alert">
                 	Lo sentimos, este correo electrónico no se encuentra registrado en nuestro sistema.
                </div>
            ';
        }
        
    }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>GO FOR AGILE - OKR Suite By Change Americas</title>
	<link rel="icon" href="<?php echo $url; ?>icon.png">
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="<?php echo $url; ?>assets/css/styles.min.css">
</head>

<style>
.login-logo { 
    width: 250px; height: 250px; background: url('assets/img/BLANCO_LOGO_GFA.png') center center/contain no-repeat; margin-bottom: 10px; margin-top: -20px;
}

.btn-volver{
    padding: 15px;
    width: 70%;
    font-weight: 800;
    display: block;
    margin: 0 auto;
    color: #4a4a4a !important;
    border: none;
    border-radius: 8px;
    color: #ffffff !important;
}
</style>

<body>
	<div class="login-container">
		<video autoplay muted loop class="video-background">
			<source src="recursos/Video_Login.mp4" type="video/mp4">
		</video>
		<div class="login-form">
			<div class="login-logo"></div>
			<?php echo isset($respuesta) ? $respuesta : ''; ?>

            <form action="" method="post">

                <div class="mb-3 text-start position-relative">
                    <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">

					<label class="form-label">Recuperar Contraseña</label>

                    <p>Al dar click en solicitar se enviarán las instrucciones para recuperar su contraseña al correo corporativo registrado en la plataforma.</p>

                    <input name="correo" type="text" id="formMail" class="form-control" placeholder="Ingrese correo..." required/>
				</div>

                <button class="mb-3" type="submit" id="btnIngresar">Solicitar Contraseña</button> 


                <a href="log.php">
                    <button type="button" class="btn btn-primary btn-volver"  >Volver</button>
                </a>

            </form>	


		
		</div>
	</div>
</body>

<script src="<?php echo $url; ?>assets/js/jquery.js"></script>
<script src="<?php echo $url; ?>assets/js/bootstrap.min.js"></script>
<script src="<?php echo $url; ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo $url; ?>assets/js/login.js"></script> 


</html>



















