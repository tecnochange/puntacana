<?php
//PARA OCULTAR EL MODULO EN CASO DE NO TENER NINGUN BOTON ACTIVO
$mstrr_9 = false;
?>

<li class="menu_groups" data-bs-toggle="collapse" data-bs-target="#menuAcademia" aria-expanded="true" id="mod_academia">
    <table width="100%">
        <tr>
            <td width="25">
                <i class="bx bx-library menu_icon"></i>
            </td>
            <td class="text_lateral">
                Go For Expert
            </td>
            <td width="30" align="right">
                <i class="bx bx-arrow-to-bottom" style="color: #9f9f9f;"></i>
            </td>
        </tr>
    </table>
</li>

<div class="collapse" id="menuAcademia">

    <?php if ($VALIDAR_MENU["goforexpert_mis_cursos"]) { $mstrr_9 = true; ?>
        <a href="<?php echo $url; ?>?pg=goforexpert/programas">
            <li class="menu_sub_items" id="bt_goforexpert_mis_cursos">
                Mis Cursos
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["goforexpert_programas"]) { $mstrr_9 = true; ?>
        <a href="<?php echo $url; ?>?pg=goforexpert/admin/programas">
            <li class="menu_sub_items" id="bt_goforexpert_programas">
                Programas
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["goforexpert_cursos"]) { $mstrr_9 = true; ?>
        <a href="<?php echo $url; ?>?pg=goforexpert/admin/cursos">
            <li class="menu_sub_items" id="bt_goforexpert_cursos">
                Cursos 
            </li>
        </a>
    <?php } ?>


     <?php if ($VALIDAR_MENU["goforexpert_cohortes"]) { $mstrr_9 = true; ?>
        <a href="<?php echo $url; ?>?pg=goforexpert/admin/cohortes">
            <li class="menu_sub_items" id="bt_goforexpert_cohortes">
                Cohortes
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["goforexpert_reportes"]) { $mstrr_9 = true; ?>
        <a href="<?php echo $url; ?>?pg=goforexpert/admin/final">
            <li class="menu_sub_items" id="bt_goforexpert_reportes">
                Reportes 
            </li>
        </a>
    <?php } ?>

       
</div>


<?php
if ($mstrr_9 == false) {
	echo '<script> $("#mod_academia").hide(); </script>';
}
?>