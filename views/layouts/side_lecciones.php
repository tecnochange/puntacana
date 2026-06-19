<?php
//PARA OCULTAR EL MODULO EN CASO DE NO TENER NINGUN BOTON ACTIVO
$mstrr_10 = false;
?>

<li class="menu_groups" data-bs-toggle="collapse" data-bs-target="#menuLecciones" aria-expanded="true" id="mod_lecciones" >
    <table width="100%">
        <tr>
            <td width="25">
                <i class="bx bx-book menu_icon"></i>
            </td>
            <td class="text_lateral">
                Lecciones
            </td>
            <td width="30" align="right">
                <i class="bx bx-arrow-to-bottom" style="color: #9f9f9f;"></i>
            </td>
        </tr>
    </table>
</li>

<div class="collapse" id="menuLecciones">

    <?php if ($VALIDAR_MENU["lecciones_crear"]) { $mstrr_10 = true; ?>
        <a href="<?php echo $url; ?>?pg=lecciones/detalle/crear">
            <li class="menu_sub_items" id="bt_lecciones_crear">
                Crear Lección Aprendida 
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["lecciones_objetivos_estrategicos"]) { $mstrr_10 = true; ?>
        <a href="<?php echo $url; ?>?pg=lecciones/lecciones_aprendidas_objetivos">
            <li class="menu_sub_items" id="bt_lecciones_objetivos_estrategicos">
                Lecciones Obj. Estratégicos 
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["lecciones_direccion"]) { $mstrr_10 = true; ?>
        <a href="<?php echo $url; ?>?pg=lecciones/lecciones_aprendidas_vp">
            <li class="menu_sub_items" id="bt_lecciones_direccion">
                Lecciones Alta Dirección 
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["lecciones_area"]) { $mstrr_10 = true; ?>
        <a href="<?php echo $url; ?>?pg=lecciones/lecciones_aprendidas">
            <li class="menu_sub_items" id="bt_lecciones_area">
                Lecciones Área / Equipo 
            </li>
        </a>
    <?php } ?>


</div>

<?php
if ($mstrr_10 == false) {
	echo '<script> $("#mod_lecciones").hide(); </script>';
}
?>