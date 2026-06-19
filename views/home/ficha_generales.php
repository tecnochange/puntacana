<?php
    $titulo_ficha = "";
    $descripcion = $dataMuro["descripcion"];
    $fecha_publicacion = FechaAmigable($dataMuro["fecha_publicacion"]);

    $queryPublicadoPor = mysqli_query($connect_admin,"SELECT * FROM Empleados WHERE id = '".$dataMuro["id_user"]."' ");
    $dataPublicadoPor = mysqli_fetch_array($queryPublicadoPor);
    $nombre_publicado_por = $dataPublicadoPor["nombre"]." ".$dataPublicadoPor["apellidos"];


    if($dataMuro["tipo"] == 3){ 

        $queryReconocido = mysqli_query($connect_admin,"SELECT * FROM Empleados WHERE id = '".$dataMuro["titulo"]."' ");
        $dataReconocido = mysqli_fetch_array($queryReconocido);
        $nombre_completo = '<b style="text-transform: uppercase;">'.$dataReconocido["nombre"].' '.$dataReconocido["apellidos"].'</b><br>';
        $titulo_ficha = "Reconocimiento"; 
        $miniatura = 'assets/img/copa_oro_3.jpg'; 

        $descripcion = $nombre_completo.$descripcion;
        //$texto_reconocimiento = 'El presente reconocimiento se otorga a '.$nombre_completo.' por sus logros y aportes el mejoramiento continuo del clima y la cultura de la organización.';
    }

    if($dataMuro["tipo"] == 5){ $titulo_ficha = "Nuevos Colaboradores"; $bg_color = "#81be41";  }
    if($dataMuro["tipo"] == 6){ $titulo_ficha = "SyST"; $bg_color = "#81be41";  }
    if($dataMuro["tipo"] == 7){ $titulo_ficha = "Efemérides"; $bg_color = "#81be41";  }
    if($dataMuro["tipo"] == 8){ $titulo_ficha = "Calidad de vida"; $bg_color = "#81be41";  }
    if($dataMuro["tipo"] == 9){ $titulo_ficha = "Noticias"; $bg_color = "#81be41";  }
    if($dataMuro["tipo"] == 10){ $titulo_ficha = "Beneficios"; $bg_color = "#81be41";  }
    if($dataMuro["tipo"] == 11){ $titulo_ficha = "Lecciones Aprendidas"; $bg_color = "#81be41";  }

/*




$multimedia = '';
//VIDEO DE YOUTUBE
if($dataMuro["tipo_multimedia"] == 2){
    $multimedia = '<iframe width="100%" height="315" src="https://www.youtube.com/embed/'.$dataMuro["script"].'" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
}




//GALERIA DE IMAGENES
if($dataMuro["tipo_multimedia"] == 1){

                            $queryArchivos = mysqli_query($connect_clima,"SELECT * FROM Multimedia WHERE id_publicacion = '".$dataMuro["id"]."' AND tipo = 1 ");
                            if($queryArchivos->num_rows > 1){
                            
                                $multimedia = '<div class="swiper-container">';
                                $multimedia .= '	<div class="swiper-wrapper">';
                                
                                while($dataArchivos = mysqli_fetch_array($queryArchivos)){
                                    $multimedia .= '<div class="swiper-slide"><img loading="lazy" src="'. $recursos_clima.'/'.$dataArchivos["imagen"].'" style=" width:100%" ></div>';
                                }
                                $multimedia .= '	</div>';
                                $multimedia .= '	<div class="swiper-pagination"></div>';
                                $multimedia .= '	<div class="swiper-button-next"></div>';
                                $multimedia .= '	<div class="swiper-button-prev"></div>';
                                $multimedia .= '</div>';
                                
                            }
                            if($queryArchivos->num_rows == 1){
                                $dataArchivos = mysqli_fetch_array($queryArchivos);
                                $multimedia .= '<div class="swiper-slide"><img loading="lazy" src="'. $recursos_clima.'/'.$dataArchivos["imagen"].'" style=" width:100%" ></div>';
                            }
}



*/

?>

<div class="publicaciones">
    <div class="titulo_muro">
        <?php echo $titulo_ficha; ?>
    </div> 
    <div>
        <img class="img_muro" src="<?php echo $miniatura; ?>" alt="">
    </div>
    <div class="fecha_muro">
        Publicado por: <?php echo $nombre_publicado_por; ?> <br>
        Fecha: <?php echo $fecha_publicacion; ?>
    </div>
    <div class="desc_muro">
        <?php echo $descripcion; ?>
    </div>
    

</div>
