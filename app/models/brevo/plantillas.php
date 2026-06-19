
<?php
function PlantillaMiembro($nombre, $tipo_miembro){
	$mensaje_encuesta = '
	<div align="center">
		<table cellpadding="0" cellspacing="0" width="500" style="font-family:Arial, Helvetica, sans-serif" bgcolor="#ffffff">
			
			<tr>
				<td align="center">
					<h2>Nuevo OKRs relacionado - Go For Agile</h2>
				</td>
			</tr>
			
			<tr>
				<td align="left" style="padding: 10px 30px;" colspan="2">
				
					Apreciado(a) <b>'.$nombre.'</b><br><br>
				
					Ha sido relacionado en un nuevo OKRs como mienbro de equipo.<br><br>
					
					Su rol es: <b>'.$tipo_miembro.'</b><br><br>
					
					Puede realizar el seguimiento en el siguiente link:<br><br>
					
					
					https://goforagile.com/ <br><br>
					
					
					
					Att: Go For Agile<br><br>
					

				</td>
			</tr>

			<tr >
				<td align="center" colspan="2" style="padding:10px; background-color: #00efbe;">
					Este correo es informativo, por favor no responder.
				</td>
			</tr>
		</table>
	</div>
	';

	return $mensaje_encuesta;
}

function PlantillaComentario($nombre, $comentario){
	$mensaje_encuesta = '
	<div align="center">
		<table cellpadding="0" cellspacing="0" width="500" style="font-family:Arial, Helvetica, sans-serif" bgcolor="#ffffff">
			
			<tr>
				<td align="center">
					<h2>Nuevo Comentario en OKRs - Go For Agile</h2>
				</td>
			</tr>
			
			<tr>
				<td align="left" style="padding: 10px 30px;" colspan="2">
				
					Apreciado(a) <b>'.$nombre.'</b><br><br>
				
					Se ha registrado el siguiente comentario en un OKRs.<br><br>
					
					<b>'.$comentario.'</b>
					
					<br><br>
					
					Puede realizar el seguimiento en el siguiente link:<br><br>
					
					
					https://goforagile.com/ <br><br>
					
					
					
					Att: Go For Agile<br><br>
					

				</td>
			</tr>

			<tr >
				<td align="center" colspan="2" style="padding:10px; background-color: #00efbe;">
					Este correo es informativo, por favor no responder.
				</td>
			</tr>
		</table>
	</div>
	';

	return $mensaje_encuesta;
}

function PlantillaDocumentos($nombre, $documento){
	$mensaje_encuesta = '
	<div align="center">
		<table cellpadding="0" cellspacing="0" width="500" style="font-family:Arial, Helvetica, sans-serif" bgcolor="#ffffff">
			
			<tr>
				<td align="center">
					<h2>Nuevo Documento Cargado en OKRs - Go For Agile</h2>
				</td>
			</tr>
			
			<tr>
				<td align="left" style="padding: 10px 30px;" colspan="2">
				
					Apreciado(a) <b>'.$nombre.'</b><br><br>
				
					Se ha cargado un nuevo documento en un OKRs.<br><br>
					
					<b>'.$documento.'</b>
					
					<br><br>
					
					Puede realizar el seguimiento en el siguiente link:<br><br>
					
					
					https://goforagile.com/ <br><br>
					
					
					
					Att: Go For Agile<br><br>
					

				</td>
			</tr>

			<tr >
				<td align="center" colspan="2" style="padding:10px; background-color: #00efbe;">
					Este correo es informativo, por favor no responder.
				</td>
			</tr>
		</table>
	</div>
	';

	return $mensaje_encuesta;
}

function PlantillaAvances( $nombre, $nombre_realiza, $iniciativa, $avance_por ){
	$mensaje_encuesta = '
	<div align="center">
		<table cellpadding="0" cellspacing="0" width="500" style="font-family:Arial, Helvetica, sans-serif" bgcolor="#ffffff">
			
			<tr>
				<td align="center">
					<h2>Nuevo Avance en OKRs - Go For Agile</h2>
				</td>
			</tr>
			
			<tr>
				<td align="left" style="padding: 10px 30px;" colspan="2">
				
					Apreciado(a) <b>'.$nombre.'</b><br><br>
				
					Se ha realizado un avance a una iniciativa.<br><br>
					
					Colaborador: <b>'.$nombre_realiza.'</b><br>
					Iniciativa: <b>'.$iniciativa.'</b><br>
					Porcentaje de avance: <b>'.$avance_por.'%</b><br>
					
					<br><br>
					
					Puede realizar el seguimiento en el siguiente link:<br><br>
					
					https://goforagile.com/ <br><br>
					
					Att: Go For Agile<br><br>
					

				</td>
			</tr>

			<tr >
				<td align="center" colspan="2" style="padding:10px; background-color: #00efbe;">
					Este correo es informativo, por favor no responder.
				</td>
			</tr>
		</table>
	</div>
	';

	return $mensaje_encuesta;
}

function PlantillaResponsable( $nombre, $descripcion ){
	$mensaje = '
	<div align="center">
		<table cellpadding="0" cellspacing="0" width="500" style="font-family:Arial, Helvetica, sans-serif" bgcolor="#ffffff">
			
			<tr>
				<td align="center">
					<h2>Responsable de Iniciativa - Go For Agile</h2>
				</td>
			</tr>
			
			<tr>
				<td align="left" style="padding: 10px 30px;" colspan="2">
				
					Apreciado(a) <b>'.$nombre.'</b><br><br>
				
					Se ha relacionado como responsable en una iniciativa.<br><br>
					
					Descripción de iniciativa: <b>'.$descripcion.'</b><br>
					
					
					<br><br>
					
					Puede realizar el seguimiento en el siguiente link.<br><br>
					
					https://goforagile.com/ <br><br>
					
					Att: Go For Agile<br><br>
					

				</td>
			</tr>

			<tr >
				<td align="center" colspan="2" style="padding:10px; background-color: #00efbe;">
					Este correo es informativo, por favor no responder.
				</td>
			</tr>
		</table>
	</div>
	';

	return $mensaje;
}


function PlantillaBienvenida( $nombre, $descripcion ){
	$mensaje = '
	<div align="center">
		<table cellpadding="0" cellspacing="0" width="500" style="font-family:Arial, Helvetica, sans-serif" bgcolor="#ffffff">
			
			<tr>
				<td align="center">
					<h2>Bienvenido - Go For Agile</h2>
				</td>
			</tr>
			
			<tr>
				<td align="left" style="padding: 10px 30px;" colspan="2">
				
					Apreciado(a) <b>'.$nombre.'</b><br><br>
					
					Usted ha sido vinculado a la plataforma para gestión de los OKRs de la organización. 
					Por favor ingrese en el siguiente link con correo y número de identificación.<br><br>
					
					
					https://goforagile.com/ <br><br>
					
					Att: Go For Agile<br><br>
					

				</td>
			</tr>

			<tr >
				<td align="center" colspan="2" style="padding:10px; background-color: #00efbe;">
					Este correo es informativo, por favor no responder.
				</td>
			</tr>
		</table>
	</div>
	';

	return $mensaje;
}


function PlantillaRecordarPass( $nombre, $correo, $pass ){
	$mensaje = '
	<div align="center">
		<table cellpadding="0" cellspacing="0" width="500" style="font-family:Arial, Helvetica, sans-serif" bgcolor="#ffffff">
			
			<tr>
				<td align="center">
					<h2>Bienvenido - Go For Agile</h2>
				</td>
			</tr>
			
			<tr>
				<td align="left" style="padding: 10px 30px;" colspan="2">
				
					Apreciado(a) <b>'.$nombre.'</b><br><br>
					
					Usted ha sido vinculado a la plataforma para gestión de los OKRs de la organización. 
					Por favor ingrese en el siguiente link con correo y número de identificación.<br><br>
					
					Correo: <b>'.$correo.'</b><br>
					Contraseña: <b>'.$pass.'</b><br><br>
					
					
					https://goforagile.com/ <br><br>
					
					Att: Go For Agile<br><br>
					

				</td>
			</tr>

			<tr >
				<td align="center" colspan="2" style="padding:10px; background-color: #00efbe;">
					Este correo es informativo, por favor no responder.
				</td>
			</tr>
		</table>
	</div>
	';

	return $mensaje;
}

//PRUEBA UNTARIA
function PlantillaMFA( $codigo ){

	$mensaje = '
	<div align="center">
		<table cellpadding="0" cellspacing="0" width="500" style="font-family:Arial, Helvetica, sans-serif" bgcolor="#ffffff">
			<tr>
				<td align="left">
					<br><br>

                    Apreciado Colaborador(a)<br /><br />
						
					A continuación relacionamos su código temporal de acceso.<br><br>
					Codigo: <b>'.$codigo.'</b><br><br>

                    Cordialmente<br />
					<b>Gofor Agile</b><br /><br />

				</td>
			</tr>
		</table>
	</div>
	';

	return $mensaje;
}






?>

