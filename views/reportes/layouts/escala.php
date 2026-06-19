<?php
    $queryEscala = mysqli_query($connect_admin, "SELECT * FROM Escala_Medicion WHERE id_empresa = " . $user_log["id_empresa"] . "");
    $dataEscala = mysqli_fetch_array($queryEscala);
?>

<table class="table" width="100%" style="font-weight: bold; font-size: 15px;">
	<tr style="border-style:none !important;font-weight: 700;border-color:transparent !important;">
		<td>
			<h3 style="color:black !important;"><?php echo str_replace(" ", "<br>", $dataEscala["titulo_uno"]); ?></h3>
		</td>
		<td>
			<h3 style="color:black !important;"><?php echo str_replace(" ", "<br>", $dataEscala["titulo_tres"]); ?></h3>
		</td>
		<td>
			<h3 style="color:black !important;"><?php echo str_replace(" ", "<br>", $dataEscala["titulo_cuatro"]); ?></h3>
		</td>
		<td>
			<h3 style="color:black !important;"><?php echo str_replace(" ", "<br>", $dataEscala["titulo_cinco"]); ?></h3>
		</td>
		<td>
			<h3 style="color:black !important;"><?php echo str_replace(" ", "<br>", $dataEscala["titulo_seis"]); ?></h3>
		</td>
	</tr>
	<tr style="font-size: 13px;">
		<td><?php echo $dataEscala["subtitulo_uno"]; ?></td>
		<td><?php echo $dataEscala["subtitulo_tres"]; ?></td>
		<td><?php echo $dataEscala["subtitulo_cuatro"]; ?></td>
		<td><?php echo $dataEscala["subtitulo_cinco"]; ?></td>
		<td><?php echo $dataEscala["subtitulo_seis"]; ?></td>
	</tr>
	<tr>
		<td width="15%" align="center" style="background-color: #FF0000;height: 30px;" bgcolor="#FF0000">
		</td>
		<td width="15%" align="center" style="background-color: #FFF200;height: 30px;" bgcolor="#FFF200">
		</td>
		<td width="15%" align="center" style="background-color: #95FA03;height: 30px;" bgcolor="#95FA03">
		</td>
		<td width="15%" align="center" style="background-color: #0DF205;height: 30px;" bgcolor="#0DF205">
		</td>
		<td width="15%" align="center" style="background-color: #00D30A;height: 30px;" bgcolor="#00D30A">
		</td>
	</tr>
	<tr style="border-style:none !important;border-color:transparent !important;">
		<td width="15%" align="center"><?php echo $dataEscala["porcentaje_uno"]; ?>% al <?php echo $dataEscala["porcentaje_dos"]; ?>%</td>
		<td width="15%" align="center"><?php echo $dataEscala["porcentaje_tres"]; ?>% al <?php echo $dataEscala["porcentaje_cuatro"]; ?>%</td>
		<td width="15%" align="center"><?php echo $dataEscala["porcentaje_cinco"]; ?>% al <?php echo $dataEscala["porcentaje_seis"]; ?>%</td>
		<td width="15%" align="center"><?php echo $dataEscala["porcentaje_siete"]; ?>% al <?php echo $dataEscala["porcentaje_ocho"]; ?>%</td>
		<td width="15%" align="center">> 100%</td>
	</tr>
</table>