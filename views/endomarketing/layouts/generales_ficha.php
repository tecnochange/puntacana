<?php
$titulo_ficha = "";
$bg_color = "";
if($data["tipo"] == 5){ $titulo_ficha = "Nuevos Colaboradores"; $bg_color = "#81be41";  }
if($data["tipo"] == 6){ $titulo_ficha = "SyST"; $bg_color = "#81be41";  }
if($data["tipo"] == 7){ $titulo_ficha = "Efemérides"; $bg_color = "#81be41";  }
if($data["tipo"] == 8){ $titulo_ficha = "Calidad de vida"; $bg_color = "#81be41";  }
if($data["tipo"] == 9){ $titulo_ficha = "Noticias"; $bg_color = "#81be41";  }
if($data["tipo"] == 10){ $titulo_ficha = "Beneficios"; $bg_color = "#81be41";  }
if($data["tipo"] == 11){ $titulo_ficha = "Lecciones Aprendidas"; $bg_color = "#81be41";  }

?>

<!-- Ficha -->
<div class="card" style="margin-bottom: 15px; font-size: 13px; border-bottom: 3px solid #ea4235;" title="<?php echo $data["id"]; ?>">
	<!-- tipo -->
    <div style="color: #ffffff;text-align: center;font-size: 17px;margin-bottom: 15px;padding: 10px;background-color: <?php echo $bg_color; ?>;">
		<?php echo $titulo_ficha; ?> 
	</div>
    
    
    <div style="color: #ea4235; text-align:right; font-size:12px">
		 
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
				echo '<img loading="lazy" src="img/user_reto_new.png" width="50" class="ico_card" /><br />';
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
		<?php 
		$descr = ConvertirLink($data["descripcion"]);
		echo nl2br(  $descr ) 
		?>
	</div>
  
	<!-- MULTIMEDIA -->
    <!-- MULTIMEDIA -->
    <div>  
    <?php
    	//VIDEO DE YOUTUBE
        if($data["tipo_multimedia"] == 2){
            echo '<iframe width="100%" height="315" src="https://www.youtube.com/embed/'.$data["script"].'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
        }
    	
		//GALERIA DE IMAGENES
		if($data["tipo_multimedia"] == 1){
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
        
        echo '<table class="table">';
        //ARCHIVOS ADICIONALES
        $queryArchivos = mysqli_query($connect_clima,"SELECT * FROM Multimedia WHERE id_publicacion = '".$data["id"]."' AND tipo = 2 ");
        while($dataArchivos = mysqli_fetch_array($queryArchivos)){
                echo '
                <tr>
                    <td>
                        <a href="'.$url.'/recursos_clima/'.$dataArchivos["imagen"].'" target="_blank">
                        '.$dataArchivos["imagen"].'
                        </a>
                    </td>
                    <td width="22"> 
                        <a href="'.$url.'/recursos_clima/'.$dataArchivos["imagen"].'" target="_blank">
                        <img loading="lazy" src="img/download.png" width="20"> 
                        </a>
                    </td>
                </tr>
                ';
        }
        echo '</table>';
        
        
	?>
    </div>
    
    <table style="margin:15px; color: #080808;" width="92%">
        	<tr>
            	<td align="left">
                	<a tabindex="<?php echo $tab_index; ?>" role="button" data-toggle="popover" data-trigger="focus" data-content="<?php echo $listMegusta; ?>"><img loading="lazy" src="img/ico_like.png" width="30" /></a>

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
				<div class="comentario_coment">'.$dataComent["comentario"].'</div>
			</div>';
		}
		?>
    </div>
	
    <!-- compartir -->
    <table width="100%" >
            	<tr>
                	<td align="center" width="33%" class="bt_option_card" onclick="Me_Gusta(<?php echo $data["id"]; ?>)">
                    	<img loading="lazy" src="img/ico_like_stroke.png" width="50" /> Me gusta
                    </td>
                    <?php 
					if($data["tipo"] == 1){
						echo '<td align="center" width="33%" class="bt_option_card"><img loading="lazy" src="img/ico_acept_stroke.png" width="50" /> Acepto</td>';					
					}
					?>
                    
                    <td align="center" class="bt_option_card" onclick="Comentar(<?php echo $data["id"]; ?>)"> <img loading="lazy" src="img/ico_comment.png" width="50"/> Comentar</td>
                </tr>
	</table>
        
</div>



