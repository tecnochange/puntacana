<?php
//PARA OCULTAR EL MODULO EN CASO DE NO TENER NINGUN BOTON ACTIVO
$mstrr_3 = false;
?>

<li class="menu_groups" data-bs-toggle="collapse" data-bs-target="#menuCompetencias" aria-expanded="true" id="mod_competencias" >
    <table width="100%">
        <tr>
            <td width="25">
                <i class="bx bx-user menu_icon"></i>
            </td>
            <td class="text_lateral">
                Competencias
            </td>
            <td width="30" align="right">
                <i class="bx bx-arrow-to-bottom" style="color: #9f9f9f;"></i>
            </td>
        </tr>
    </table>
</li>

<div class="collapse" id="menuCompetencias">

    <div style="padding: 0px 20px;">
        <form action="" method="POST">
            <input type="hidden" name="guardar_configurar" value="true">
            <lable>Año *</lable>
            <select class="form-control form-control-sm" name="anio_ciclo" id="anio_ciclo" required onchange="ValidarCicloSidebar(this.value)" >
                                    <option value="">Por Año...</option>
                                    <?php
                                    foreach ($Array_Anio as $anio) {
                                        if ($_SESSION["anio_ciclo"] ==  $anio[0]) {
                                            echo '<option value="'.$anio[0].'" selected>'.$anio[1].'</option>';
                                        } else {
                                            echo '<option value="'.$anio[0].'">'.$anio[1].'</option>';
                                        }
                                    }
                                    ?>
            </select>
            <lable>Ciclo *</lable>
            <select class="form-control form-control-sm" name="ciclo" id="ciclo_sidebar" >
                                    <option value="">Por Ciclo...</option>
                                    <?php
                                    $query = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id_empresa = '" . $_SESSION['id_empresa'] . "' AND anio = '".$_SESSION["anio_ciclo"]."' ");
                                    while ($dataCiclos = mysqli_fetch_array($query)) {
                                        if ($_SESSION["ciclo"] ==  $dataCiclos["id"]) {
                                            echo '<option value="' . $dataCiclos["id"] . '" selected>' . $dataCiclos["nombre"] . '</option>';
                                        } else {
                                            echo '<option value="' . $dataCiclos["id"] . '">' . $dataCiclos["nombre"] . '</option>';
                                        }
                                    }
                                    ?>
            </select>
            <button type="submit" class="btn btn-success btn-sm w-100 mt-2 ">
                Seleccionar
            </button>
        </form>
    </div>

    <?php if ($VALIDAR_MENU["competencias_seleccionar_ciclo"]) { $mstrr_3 = true; ?>
        <a href="<?php echo $url; ?>?pg=competencias/configurar" style="display:none">
            <li class="menu_sub_items" id="bt_competencias_ciclo">
                Seleccionar Ciclo 
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["competencias_realizar_valoracion"]) { $mstrr_3 = true; ?>
        <a href="<?php echo $url; ?>?pg=competencias/realizar_valoracion">
            <li class="menu_sub_items" id="bt_realizar_valoracion">
               Realizar Valoración
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["competencias_tipo"]) { $mstrr_3 = true; ?>
        <a href="<?php echo $url; ?>?pg=competencias/competencias/competencias">
            <li class="menu_sub_items" id="bt_tipo_competencias">
                Tipo de Competencias 
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["competencias_perfiles"]) { $mstrr_3 = true; ?>
        <a href="<?php echo $url; ?>?pg=competencias/perfiles">
            <li class="menu_sub_items" id="bt_competencias_perfiles">
                Perfiles 
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["competencias_programacion"]) { $mstrr_3 = true; ?>
        <a href="<?php echo $url; ?>?pg=competencias/programacion">
            <li class="menu_sub_items" id="bt_competencias_programacion">
               Programación
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["competencias_arbol_valoracion"]) { $mstrr_3 = true; ?>
        <a href="<?php echo $url; ?>?pg=competencias/arbol">
            <li class="menu_sub_items" id="bt_competencias_arbol">
                Arbol Valoración
            </li>
        </a>
    <?php } ?>

    <?php 
    //var_dump($VALIDAR_MENU["competencias_reporte"]);
    ?>

     <?php if ($VALIDAR_MENU["competencias_reporte"] || $user_log["permiso_relaciones_laboradores"] == true ) { $mstrr_3 = true; ?>
        <a href="<?php echo $url; ?>?pg=competencias/seguimiento">
            <li class="menu_sub_items" id="bt_competencias_reportes">
                Reporte de Avance 
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["competencias_analitica"]) { $mstrr_3 = true; ?>
        <a href="<?php echo $url; ?>?pg=competencias/analitica">
            <li class="menu_sub_items" id="bt_competencias_analitica">
                Analítica 
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["competencias_mi_informe"]) { $mstrr_3 = true; ?>
        <a href="<?php echo $url; ?>?pg=competencias/mi_informe">
            <li class="menu_sub_items" id="bt_competencias_mi_informe">
                Informes
            </li>
        </a>
    <?php } ?>


</div>

<?php
if ($mstrr_3 == false) {
	echo '<script> $("#mod_competencias").hide(); </script>';
}
?>

<script>
    var api = '<?php echo $url; ?>api/competencias/';
    function ValidarCicloSidebar(anio){

        $.ajax({
				url: api + 'validar_ciclo.php',
				type: 'post',
				data: {
					anio: anio,
					id_empresa: <?php echo $user_log["id_empresa"] ?>
				},
			}).done(function(resp) {
                $("#ciclo_sidebar").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});

        
    }
</script>