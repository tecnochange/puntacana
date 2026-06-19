<!-- Ficha -->
<div class="card" style="margin-bottom: 15px; font-size: 13px; border-bottom: 3px solid #ea4235;">
	<!-- tipo -->
    <div style="color: #ffffff;text-align: center;font-size: 17px;margin-bottom: 15px;padding: 10px;background-color: #ea4235;">
		Clasificado
	</div>
    
    
    <div style="color: #ea4235; text-align:right; font-size:12px">
		 
	</div>
    
    <!-- cabecera -->
    <div style="padding: 0px 15px 0px 15px;">
    <table width="100%"><tr>
    	<td width="60" align="left">
			<?php
			if($dataUser["foto"] != ""){
				echo '<div class="miniatura_home" style="background-image:url('."'".$url.'/recursos/'.$dataUser["foto"]."'".' );"></div>';
			}
			else{
				echo '<img loading="lazy" src="img/user_reto.png" width="50" class="ico_card" /><br />';
			}
			?>
		</td>
        <td align="left" valign="top" style=" padding-right:15px; font-size:15px">
			<div class="titulo_card"><?php echo $dataUser["nombre"]." ".$dataUser["apellidos"] ?></div>
			<span class="fecha_card">Publicado el <?php echo $data["created_at"] ?></span>
		</td>
        <td align="right">
			<?php
			if($data["id_user"] == $_SESSION['id_user'] || $_SESSION['role_plataforma'] == 1 ){
				echo '
					<img loading="lazy" src="img/ico_delete.png" width="22" class="ico_edit_opt" onclick="Borrar_Reto('.$data["id"].')"/>
					<a href="?pg=endomarketing/publicar/generales&id='.$data["id"].'"><img loading="lazy" src="img/ico_edit.png" width="22" class="ico_edit_opt"/></a>
				';
			}
			?>
        </td>
    </tr></table>
    </div>
    
    <!-- descripcion -->
    <div class="parrafo_card">
		<?php echo nl2br(  utf8_decode($data["descripcion"]) ) ?>
	</div>
  
	<!-- MULTIMEDIA -->
    <!-- MULTIMEDIA -->
    <div>  
    <?php
    	//VIDEO DE YOUTUBE
		if($data["script"] != ""){
			echo '<iframe width="100%" height="315" src="https://www.youtube.com/embed/'.$data["script"].'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
		}
		//GALERIA DE IMAGENES
		else{
			$queryArchivos = mysqli_query($connect_clima,"SELECT * FROM Multimedia WHERE id_publicacion = '".$data["id"]."' AND tipo = 1 ");
			if($queryArchivos->num_rows > 1){
			
				echo '<div class="swiper-container">';
				echo '	<div class="swiper-wrapper">';
				
				while($dataArchivos = mysqli_fetch_array($queryArchivos)){
					echo '<div class="swiper-slide"><img loading="lazy" src="'. $recursos_clima.'/'.$dataArchivos["imagen"].'" style=" width:100%" ></div>';
				}
				echo '	</div>';
				echo '	<div class="swiper-pagination"></div>';
				echo '	<div class="swiper-button-next"></div>';
				echo '	<div class="swiper-button-prev"></div>';
				echo '</div>';
				
			}
			if($queryArchivos->num_rows == 1){
				$dataArchivos = mysqli_fetch_array($queryArchivos);
				echo '<div class="swiper-slide"><img loading="lazy" src="'. $recursos_clima.'/'.$dataArchivos["imagen"].'" style=" width:100%" ></div>';
			}
		}
	?>
    </div>
    
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
			<td align="center" class="bt_option_card" onclick="Comentar(<?php echo $data["id"]; ?>)"> <img loading="lazy" src="img/ico_comment.png" width="40"/> Responder clasificado</td>
		</tr>
	</table>
        
</div>



