<?php
//PARA OCULTAR EL MODULO EN CASO DE NO TENER NINGUN BOTON ACTIVO
$mstrr_2 = false;
?>

<li class="menu_groups" data-bs-toggle="collapse" data-bs-target="#menuOkrs" aria-expanded="true" id="mod_okrs">
    <table width="100%">
        <tr>
            <td width="25">
                <i class="bx bx-bullseye menu_icon"></i>
            </td>
            <td class="text_lateral">
                Okrs
            </td>
            <td width="30" align="right">
                <i class="bx bx-arrow-to-bottom" style="color: #9f9f9f;"></i>
            </td>
        </tr>
    </table>
</li>

<div class="collapse" id="menuOkrs">

    <?php if ($VALIDAR_MENU["okrs_crear"]) { $mstrr_2 = true; ?>
        <a href="<?php echo $url; ?>?pg=okrs/okr/detalle&new=true">
            <li class="menu_sub_items" id="bt_okrs_crear">
                Crear OKR  
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["okrs_resultados"] || $user_log["permiso_relaciones_laboradores_okrs"] ) { $mstrr_2 = true; ?>
        <a href="<?php echo $url; ?>?pg=okrs/equipos">
            <li class="menu_sub_items" id="bt_okrs_equipo">
                Mi (Área/Equipo)
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["okrs_mis_okrs"]) { $mstrr_2 = true; ?>
        <a href="<?php echo $url; ?>?pg=okrs/objetivos_asociados">
            <li class="menu_sub_items" id="bt_okrs_objetivos_asociados">
                Objetivos Asociados
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["okrs_mis_resultados"]) { $mstrr_2 = true; ?>
        <a href="<?php echo $url; ?>?pg=okrs/mis_resultados">
            <li class="menu_sub_items" id="bt_okrs_mis_resultados">
                Mis Resultados Claves 
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["okrs_iniciativas"]) { $mstrr_2 = true; ?>
        <a href="<?php echo $url; ?>?pg=okrs/mis_iniciativas">
            <li class="menu_sub_items" id="bt_okrs_mis_iniciativas">
                Mis Iniciativas 
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["okrs_planes_accion"]) { $mstrr_2 = true; ?>
        <a href="<?php echo $url; ?>?pg=okrs/planes_accion">
            <li class="menu_sub_items" id="bt_okrs_planes_accion">
                Mis Planes de Acción 
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["okrs_todos"]) { $mstrr_2 = true; ?>
        <a href="<?php echo $url; ?>?pg=okrs/reportes">
            <li class="menu_sub_items" id="bt_okrs_reportes">
                Ver Todos los OKRs
            </li>
        </a>
    <?php } ?>

       
</div>

<?php
if ($mstrr_2 == false) {
	echo '<script> $("#mod_okrs").hide(); </script>';
}
?>