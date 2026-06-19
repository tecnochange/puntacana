<style>
    
#foto-lateral{
    width: 70px !important;
    height: 70px !important;
    background-size: cover;
    background-position: center;
    border-radius: 100px;
    border: 2px solid #365189;
}
</style>
<div class="profile clearfix"><br>
                        <div class="profile_img" style="text-align: center;">
                            <?php
                            if ($dtEmpresa["logo"] != "") {
                                echo '<img loading="lazy" src="' . $url . 'recursos/' . $dtEmpresa["logo"] . '" style="width: auto; max-height: 60px;" class="profile_img">';
                            } else {
                                echo '<img loading="lazy" src="' . $url . 'img/logo_agile_marker.png" style="width: auto; max-height: 50px;" class="profile_img">';
                            }
                            ?>
                        </div>

                        <div class="profile_pic" style="text-align: center;width: auto !important;float: none !important;">
                        <div class="img-circle profile_img" style="background-image: url(<?php echo $url . "recursos/" . $dtEmpleado["foto"]; ?>);width: 70px !important;height: 70px !important;background-size: cover;
    background-position: center;border-radius: 100px;border: 2px solid #365189;margin-left: 34% !important;" id="foto_lateral"></div>
    <div style="font-size: 15px;color: black;text-transform: uppercase;">
                            <?php echo $dtEmpleado["nombre"] . " " . $dtEmpleado["apellidos"]; ?>
					        </div>
                            
                        </div>
                        
                       
                        <!-- <div class="" style="text-align: center;"> -->
                        

                        <!-- </div> -->
                    </div>
                    <div class="profile clearfix">
                        <br>
                        <div class="row" style="text-align: center;">
                            <div class="col-md-12">
                                <?php if ($dtEmpleado['role'] == 1) { ?>
                                    <div>
                                    <span>¿Cual perfil desea usar?</span>
                                        <a href="<?php echo $url; ?>/?pg=seleccionar_perfil&t=1">
                                            <button type="button" class="btn btn-success btn-sm " style="margin-bottom: 10px">
                                                Administrador
                                            </button>
                                        </a>

                                        <a href="<?php echo $url; ?>?pg=seleccionar_perfil&t=2">
                                            <button type="button" class="btn btn-primary btn-sm" style="margin-bottom: 10px">
                                                Líder
                                            </button>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>

                        </div>
                    </div>