<?php
	$queryEmple = mysqli_query($connect_valentina,"SELECT * FROM Empleados WHERE id = '".$data["titulo"]."' ");
	$dataEmple = mysqli_fetch_array($queryEmple);
?>

<!-- Ficha -->
<div class="card" style="margin-bottom: 15px; font-size: 15px; border-bottom: 3px solid #34a853;">
	<!-- tipo -->
    <div style="color: #ffffff;text-align: center;font-size: 17px;margin-bottom: 15px;padding: 10px;background-color: #ff6839;">
		Reconocimiento  para: <b><?php echo $dataEmple["nombre"]." ".$dataEmple["apellidos"]; ?></b> 
	</div>
    
    <!-- cabecera -->
    <div style="padding: 0px 15px 0px 15px;">
    <table width="100%"><tr>
    	<td width="70" align="left">
			<?php
			if($dataUser["foto"] != ""){
				echo '<div class="miniatura_home" style="background-image:url('."'".$url.'/recursos/'.$dataUser["foto"]."'".' );"></div>';
			}
			else{
				echo '<img loading="lazy" src="assets/img/user_reto.png" width="50" class="ico_card" /><br />';
			}
			?>
		</td>
    	<td align="left" valign="top" style=" padding-right:15px; font-size:15px">
			<div class="titulo_card"><?php echo $dataUser["nombre"]." ".$dataUser["apellidos"] ?></div>
			<span class="fecha_card">Publicado el <?php echo $data["created_at"] ?></span>
		</td>
        <td align="right">
			<?php
			if($data["id_user"] == $_SESSION['id_user_valentina'] || $_SESSION['role_plataforma'] == 1){
				echo '
					<img loading="lazy" src="assets/img/ico_delete.png" width="22" class="ico_edit_opt" onclick="Borrar_Reto('.$data["id"].')"/>
				';
			}
			?>
        </td>
    </tr></table>
    </div>
    
	<!-- contenido -->   
	<div style="padding: 15px;"> 
		<table width="100%">
        	<tr>
            	<td>
                	<?php
						if($data["copa"] == 1){ echo '<img loading="lazy" src="assets/img/copa_oro.jpg" width="100%">';}
						if($data["copa"] == 2){ echo '<img loading="lazy" src="assets/img/copa_plata.jpg" width="100%">';}
					?>
                	
                </td>
                <td>
                	<?php
						if($data["copa"] == 1){ echo '<div align="center" style="color: #dd9308; font-weight: bold; padding-bottom: 15px;">RECONOCIMIENTO DORADO</div>';}
						if($data["copa"] == 2){ echo '<div align="center" style="color: #7a7a7a; font-weight: bold; padding-bottom: 15px;">RECONOCIMIENTO PLATEADO</div>';}
					?>
                	
                    <div style="text-align:justify">
                    	El presente reconocimiento se otorga a <b style="color: #dd9308;">
						<?php
							echo $dataEmple["nombre"]." ".$dataEmple["apellidos"] ; 
						?>
                        </b> 
                        
                        <?php if($data["copa"] == 1){ echo 'por sus logros y aportes el mejoramiento continuo del clima y la cultura de la organización.<br><br>'; } ?>
                        <?php if($data["copa"] == 2){ echo 'por su esfuerzo permanente en realizar aportes que permitan el mejoramiento continuo del clima y la cultura de la Organización.<br><br>'; } ?>
                        
                        
                        Atentamente<br>
                        <b>
                        <?php 
							echo $dataUser["nombre"]." ".$dataUser["apellidos"];
							
						?>
                        </b>
                    </div>
                </td>
            </tr>
        </table>
        
        <div style="border: 1px solid #eceaea; padding: 10px;">
			<?php 
				echo  utf8_decode($data["descripcion"]);		
			?>
        </div>
	</div>
    
    <table style="margin:15px; color: #080808;" width="92%">
        	<tr>
            	<td align="left">
                	<a tabindex="<?php echo $tab_index; ?>" role="button" data-toggle="popover" data-trigger="focus" data-content="<?php echo $listMegusta; ?>"><img loading="lazy" src="assets/img/ico_like_2.png" width="30" /></a>

					<b><?php echo $queryMegusta->num_rows; ?></b>
                </td>
                <td align="right"><b><?php echo $queryComentarios->num_rows; ?></b> Comentarios</td>
            </tr>
	</table>
    
	<!-- COMENTARIOS -->
    <div style="max-height:150px; overflow:auto">
    	<?php
		$queryComent = mysqli_query($connect_clima,"SELECT * FROM Comentarios WHERE id_publicacion = '".$data["id"]."' ORDER BY id DESC ");
		while($dataComent = mysqli_fetch_array($queryComent)){
			echo '
			<div class="comentarios_item">
				<div class="comentario_fecha"><b>'.$dataComent["nombre_full"].'</b> - '.$dataComent["created_at"].'</div>
				<div class="comentario_coment">'. utf8_decode($dataComent["comentario"]).'</div>
			</div>';
		}
		?>
    </div>
	
    <!-- compartir -->
    <table width="100%" >
            	<tr>
                	<td align="center" width="33%" class="bt_option_card" onclick="Me_Gusta(<?php echo $data["id"]; ?>)">
                    	<img loading="lazy" src="assets/img/ico_like_stroke.png" width="50" /> Me gusta
                    </td>
                    <?php 
					if($data["tipo"] == 1){
						echo '<td align="center" width="33%" class="bt_option_card"><img loading="lazy" src="assets/img/ico_acept_stroke.png" width="50" /> Acepto</td>';					
					}
					?>
                    
                    <td align="center" class="bt_option_card" onclick="Comentar(<?php echo $data["id"]; ?>)"> <img loading="lazy" src="img/ico_comment.png" width="50"/> Comentar</td>
                </tr>
	</table>
        
</div>



