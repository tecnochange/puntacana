<?php
header('X-Powered-By: Goforagile 2025');
header("Set-Cookie: key=value; path=/; domain=goforagile.com; HttpOnly; SameSite=Strict");
define('DURACION_SESION', '7200'); //4 horas
ini_set("session.cookie_lifetime", DURACION_SESION);
ini_set("session.gc_maxlifetime", DURACION_SESION);
session_cache_expire(DURACION_SESION);

session_start();
ini_set('session.cookie_httponly', 1);
include("app/connect.php");

$respuesta = "";

function generateCode(){
    $limit = 6;
    return random_int(10 ** ($limit - 1), (10 ** $limit) - 1);
}

// echo $_SESSION['id_user'];
$monstrar_cod_temporal = false;

if ($_POST["user"] != "" &&  $_POST["password"] != "") {

    //ESCAPAS LOS CARATERES DEL FORMULARIO
	$user = addslashes($_POST["user"]);
	$pass = addslashes($_POST["password"]);

    //CARGAMOS LAS VARIABLES DE VALIDACION
    $permitir_ingreso = false; //POR DEFECTO ES FALSO

    //VALIDAR QUE EL USUARIO EXISTA Y SE ENCUENTRE ACTIVO
	$query = mysqli_query($connect_admin, "SELECT * FROM Empleados 
    WHERE correo = '".$user."' AND password = '".$pass."' AND estado = 1  ");
    $data = mysqli_fetch_array($query);

    //SI EL COLABORADOR EXISTE
	if ($query->num_rows > 0) {

        //SOLO CUANDO VIENE CODIGO TEMPORAL DE ACCESO - DOBLE FACTOR DE AUTENTICACION
        //SOLO CUANDO VIENE CODIGO TEMPORAL DE ACCESO - DOBLE FACTOR DE AUTENTICACION
        if($_POST["cod_temporal"]){

            $queryVal = mysqli_query($connect_admin,"SELECT * FROM Empleados 
            WHERE cod_ingreso = '".$_POST["cod_temporal"]."' ");
			if($queryVal->num_rows > 0){
                $permitir_ingreso = true;
                mysqli_query($connect_admin,"UPDATE Empleados SET renovar_codigo = 1 WHERE id = '".$data["id"]."' ");
                $data["renovar_codigo"] = 1;
            }
            else{
                $respuesta = '
                    <div class="alert alert-danger">
                        <strong>El código de acceso no es válido.</strong> Intenta nuevamente.
                    </div>
                ';
            }
        }
        
        //SI NO ES REQUERIDO EL DOBLE FACTOR DE AUTENTICACION
        if($data["renovar_codigo"] != 2){
            $permitir_ingreso = true;
        }
        //EN CASO DE REQUIRIR RENOVACION DE CODIGO. SE GENERA Y SE ACTIVA EL CAMPO EN EL FORMULARIO
        else{

            $cod_temporal = generateCode();
            mysqli_query($connect_admin,"UPDATE Empleados SET cod_ingreso = '".$cod_temporal."' 
            WHERE id = '".$data["id"]."' ");
            $monstrar_cod_temporal = true;

            //$correo = $data["correo"];
            $correo = "eniac321@gmail.com";

            include("app/models/brevo/Brevo.php");
            include("app/models/brevo/plantillas.php"); 

            $asunto = "Gofor Agile - Codigo Temporal de Acceso";

            $plantilla = PlantillaMFA( $cod_temporal );
            $ClassBrevo = new Brevo();
            $resp = $ClassBrevo->individual( "GoFor Agile", $asunto, $correo, $plantilla );
        }


        //LUEGO DE PASAR LAS VALIDACIONES Y PERMITIR EL ACCESO
        //LUEGO DE PASAR LAS VALIDACIONES Y PERMITIR EL ACCESO
        //LUEGO DE PASAR LAS VALIDACIONES Y PERMITIR EL ACCESO
        if($permitir_ingreso == true){

            $_SESSION['id_user'] = $data["id"];
            $_SESSION["id_empresa"] = $data["id_empresa"];

            //INICIALIZACION DEL CICLO Y EL AÑO
            $query_ciclo = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id_empresa = '" . $data["id_empresa"] . "' ORDER BY anio DESC LIMIT 1 ");
            $data_ciclo = mysqli_fetch_array($query_ciclo);
            $_SESSION["anio_ciclo"] = $data_ciclo["anio"];
            $_SESSION["ciclo"] = $data_ciclo["id"];

            echo '<script>  window.location = "' . $url . '"; </script>';
        }
	} 
    //CUANDO NO EXISTE O LAS CREDENCIALES SON INVÁLIDAS
    else {
		$respuesta = '
			<div class="alert alert-danger">
			  <strong>Algo va mal!</strong> Credenciales Inválidas .
			</div>
		';
		$_POST['user'] = $_POST['password'] = "";
		session_destroy();
	}
}


$_SESSION['token'] = md5(uniqid(mt_rand(), true));

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
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-QR4WZ86XSL"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-QR4WZ86XSL');
    </script>
</head>

<style>

body{
    background: #e6eaef; 
}
.login-logo { 
    width: 250px; height: 250px; background: url('assets/img/BLANCO_LOGO_GFA.png') center center/contain no-repeat; margin-bottom: 10px; margin-top: -20px;
}
.base_azul{
    background: #007bff; 
    color: #ffffff;
}
.video-loop{
    background-color: #ffffff; 
    display: flex;
}
@media (max-width: 768px) {
    .video-loop{
        display:none; 
    }
    body{
        background: #007bff; 
    }
}
</style>

<body>

    <div class="container ">
        <div class="row" style="border-radius: 10px; overflow: hidden; box-shadow: 0px 10px 25px rgba(0,0,0,0.2);">
            <div class="col-md-7 video-loop">
                <video autoplay muted loop style="width: 100%;">
                    <source src="assets/video_login.mp4" type="video/mp4">
                </video>
            </div>
            <div class="col-md-5 text-center base_azul" >

            <div class="p-4">       

                <div class="mt-3">
                    <img src="assets/img/logo_white.png" style="width: 100%; max-width: 240px;">
                </div>

                <?php echo $respuesta; ?>

                <form id="form-login" class="w-100" action="" method="post" autocomplete="off">
                    <input type="hidden" name="csrf" value="<?php echo $_SESSION['token']; ?>">

                    <div class="mb-3 text-start position-relative">
                        <label class="form-label">Usuario</label>
                        <input type="text" class="form-control" placeholder="Ingrese Usuario" name="user" id="user" required value="<?php echo isset($user) ? htmlspecialchars($user) : ''; ?>">
                        <div class="invalid-feedback">Campo obligatorio</div>
                    </div>

                    <div class="mb-3 text-start position-relative">
                        <label class="form-label">Contraseña</label>
                        <div class="password-container">
                            <input type="password" class="form-control" placeholder="Ingrese Contraseña" name="password" id="password" required value="<?php echo isset($pass) ? htmlspecialchars($pass) : ''; ?>">
                            <span class="toggle-password" onclick="togglePassword()">👁️</span>
                        </div>
                        <div class="invalid-feedback">Campo obligatorio</div>
                    </div>

                    <?php if( $monstrar_cod_temporal == true){ ?>
                    <div class="mb-3 text-start position-relative">
                        <label >Ingrese Código Temporal</label>
                        <input type="text" class="form-control" placeholder="Código Temporal.." name="cod_temporal" id="cod_temporal"  autocomplete="off" onkeyup="return SoloNumeros(this)">
                        <small id="emailHelp" class="form-text text-muted">Ingrese el código temporal enviado al correo que aparece en su registro.</small>
                    </div>
                    <?php } ?>



                    <button type="submit" id="btnIngresar">Ingresar</button>
                    <p class="forgot-password">¿Olvidó su contraseña? <a href="<?php echo $url; ?>/recuperar.php" style="color: white !important;">Recuperar</a></p>

                    <div class="mt-3 mb-3" style="font-size: 0.8rem; margin-top: 10px;">
                        <a href="https://puntacana.goforagile.com/Politicas_Datos_GoforAgile_V1.pdf" target="_blank" style="color: #ffffff " >Políticas de tratamiento de datos</a>
                    </div>
                </form>

            </div>

            </div>
        </div>
    </div>

</body>
<script src="<?php echo $url; ?>assets/js/jquery.js"></script>
<script src="<?php echo $url; ?>assets/js/bootstrap.min.js"></script>
<script src="<?php echo $url; ?>assets/js/jquery.validate.js"></script>
<script src="<?php echo $url; ?>assets/js/login.js"></script> 

<script>
    function SoloNumeros(elemet){
		elemet.value = elemet.value.replace(/[^0-9]/g, '').replace(/,/g, '.');
	}
</script>
</html>




