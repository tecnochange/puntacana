<?php
session_start();

if ($_POST["user"] != "" &&  $_POST["password"] != "") {
	
	include("../app/connect.php");
	$query = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE correo = '" . $_POST["user"] . "' AND password = '" . $_POST["password"] . "' ");
	if ($query->num_rows > 0) {
		$data = mysqli_fetch_array($query);
		// print_r($data);
		$_SESSION['id_super_user_valentina'] = $data["id"];
		$_SESSION['nombre_super_valentina'] = $data["nombre"];
		$_SESSION['id_empresa_valentina'] = $data["id_empresa"];
		$_SESSION["area_valentina"] = $data["area"];
		if ($data["role"] == '1') {
			$_SESSION["role_plataforma_valentina"] = 2;
		} else {
			$_SESSION["role_plataforma_valentina"] = $data["role"];
		}

		if (!$data["foto"]) {
			$_SESSION['foto_valentina'] = "img_default.jpg";
		} else {
			$_SESSION['foto_valentina'] = $data["foto"];
		}
		// $queryCiclos = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id_empresa = '" . $data["id_empresa"] . "'");
		// if (mysqli_num_rows($queryCiclos) > 0) {
		// 	$dataCiclos = mysqli_fetch_array($queryCiclos);
		// 	$_SESSION["anio_ciclo"] = $dataCiclos["anio"];
		// 	$_SESSION["ciclo"] = $dataCiclos["id"];
		// } else {
		// 	$_SESSION['ciclo'] =  "";
		// 	$_SESSION["anio_ciclo"] = "";
		// }

		$queryEmpresa = mysqli_query($connect_admin, "SELECT * FROM Empresas WHERE id = '" . $data["id_empresa"] . "'");
		if (mysqli_num_rows($queryEmpresa) > 0) {
			$dataEmpresa = mysqli_fetch_array($queryEmpresa);
			$_SESSION["anio_fill"] = $dataEmpresa["anio_curso"];
			$_SESSION["anio_ciclo"] = $dataEmpresa["anio_ciclo"];
			$_SESSION["ciclo"] = $dataEmpresa["id_ciclo"];
		} else {
			$_SESSION['ciclo'] =  "";
			$_SESSION["anio_ciclo"] = $_SESSION["anio_fill"] = "";
		}
		echo '<script> window.location = "' . $url . 'mi_desempenio/"; </script>';
	} else {
		$respuesta = '
			<div class="alert alert-danger">
			  <strong>Algo va mal!</strong> Credenciales Inválidas .
			</div>
			';
	}
}
?>

<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>GO FOR AGILE - OKR Suite By Change Americas</title>
	<link rel="icon" href="../icon.png">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="css/style.min.css">
</head>

<body>
	<video class="video-background" autoplay muted loop>
		<source src="../recursos/Video_Login.mp4" type="video/mp4">
	</video>
	<div class="form-container">
		<?php echo $respuesta; ?>
		<form id="loginForm" action="" method="post">
			<div class="mb-4">
				<label for="user" class="form-label">Usuario</label>
				<input type="text" class="form-control" id="user" name="user" placeholder="Ingrese Usuario">
				<div class="error-message">Este campo es obligatorio</div>
			</div>
			<div class="mb-4">
				<label for="password" class="form-label">Contraseña</label>
				<div class="password-container">
					<input type="password" class="form-control" placeholder="Ingrese Contraseña" aria-label="Password" aria-describedby="Password" name="password" id="password" required>
					<span class="toggle-password" onclick="togglePassword()">👁️</span>
				</div>
				<div class="error-message">Este campo es obligatorio</div>
			</div>
			<button type="submit" class="btn btn-warning" id="btnIngresar">Ingresar</button>
			<div class="forgot-password">
				<a href="#">¿Olvidó su contraseña?</a>
			</div>
			<?php
			$url = "https://goforagile.com/mi_desempenio/log.php";

			$size = 300;

			$qrUrl = "https://quickchart.io/qr?text=" . urlencode($url) . "&size={$size}";

			$qrCode = '<img src="' . $qrUrl . '" alt="QR Code" style="width: 100px; height: 100px;">';

			?>
		</form>
		<br>
		<br>
		<div class="logo"></div>
	</div>
	<!-- <script src="js/validate.js"></script> -->
	<script>
		function togglePassword() {
			var passwordField = document.getElementById("password");
			if (passwordField.type === "password") {
				passwordField.type = "text";
			} else {
				passwordField.type = "password";
			}
		}
	</script>
</body>

</html>