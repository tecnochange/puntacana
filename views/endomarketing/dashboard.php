<script>
$(document).ready(function() {
    $('#menuEndomarketing').collapse();
    $('#bt_endomarketing_publicar').addClass('active');
});
</script>


<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12" style="text-align: start !important;">
                        <h3>Publicidad Reconocimiento y Noticias</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>

<div class="container">
    <div class="row justify-content-md-center">
        
        <?php if($dtEmpleado["role"] == 1 || $dtEmpleado["role"] == 2 ){ ?>
        <div class="col-md-3" style="margin-bottom: 20px">
            <div class="card">
                <div class="card-body">
                    <a href="<?php echo $url; ?>?pg=endomarketing/publicar/reconocimientos">
                        <div align="center">
                            <i class="fas fa-medal iconos_dash" style="color: #365189;"></i><br>
                            Reconocimientos
                        </div>
                    </a>
                </div>
            </div>
        </div>
		
		<div class="col-md-3" style="margin-bottom: 20px">
            <div class="card">
                <div class="card-body">
                    <a href="<?php echo $url; ?>?pg=endomarketing/publicar/generales&tp=9">
                        <div align="center">
                            <i class="fas fa-newspaper iconos_dash" style="color: #365189;"></i><br>
                            Noticias
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3" style="margin-bottom: 20px">
            <div class="card">
                <div class="card-body">
                    <a href="<?php echo $url; ?>?pg=endomarketing/publicar/lecciones_aprendidas">
                        <div align="center">
                            <i class="fas fa-pencil-ruler iconos_dash" style="color: #365189;"></i><br>
                            Lecciones Aprendidas
                        </div>
                    </a>
                </div>
            </div>
        </div> 
        
        <?php } ?>
        
  
        
        
    </div>

</div>

<style>
    .iconos_dash{
        font-size: 40px;
        margin-bottom: 10px;
        margin-top: 10px;
    }
</style>

