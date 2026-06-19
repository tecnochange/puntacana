<?php
//PARA OCULTAR EL MODULO EN CASO DE NO TENER NINGUN BOTON ACTIVO
$mstrr_6 = false;
?>

<li class="menu_groups" data-bs-toggle="collapse" data-bs-target="#menuEndomarketing" aria-expanded="true" id="mod_endomarketing">
    <table width="100%">
        <tr>
            <td width="25">
                <i class="bx bx-bell menu_icon"></i>
            </td>
            <td class="text_lateral">
                Endomarketing
            </td>
            <td width="30" align="right">
                <i class="bx bx-arrow-to-bottom" style="color: #9f9f9f;"></i>
            </td>
        </tr>
    </table>
</li>

<div class="collapse" id="menuEndomarketing">

    <?php if ($VALIDAR_MENU["endomarketing_muro"]) {  $mstrr_6 = true; ?>
        <a href="<?php echo $url; ?>?pg=endomarketing/muro">
            <li class="menu_sub_items" id="bt_endomarketing_muro">
                Muro de Reconocimientos 
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["endomarketing_publicar"]) { $mstrr_6 = true; ?>
        <a href="<?php echo $url; ?>?pg=endomarketing/dashboard">
            <li class="menu_sub_items" id="bt_endomarketing_publicar">
                Publicar Reconocimientos y Noticias 
            </li>
        </a>
    <?php } ?>


</div>

<?php
if ($mstrr_6 == false) {
	echo '<script> $("#mod_endomarketing").hide(); </script>';
}
?>