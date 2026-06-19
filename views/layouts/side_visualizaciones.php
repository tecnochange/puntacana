<?php
//PARA OCULTAR EL MODULO EN CASO DE NO TENER NINGUN BOTON ACTIVO
$mstrr_3 = false;
?>

<li class="menu_groups" data-bs-toggle="collapse" data-bs-target="#menuVisual" aria-expanded="true" id="mod_visualizaciones">
    <table width="100%">
        <tr>
            <td width="25">
                <i class="bx bx-bar-chart menu_icon"></i>
            </td>
            <td class="text_lateral">
                Visualizaciones
            </td>
            <td width="30" align="right">
                <i class="bx bx-arrow-to-bottom" style="color: #9f9f9f;"></i>
            </td>
        </tr>
    </table>
</li>

<div class="collapse" id="menuVisual">

    <?php if ($VALIDAR_MENU["visualizaciones_auditoria"]) { $mstrr_3 = true; ?>
        <a href="<?php echo $url; ?>?pg=visualizaciones/auditoria_okrs">
            <li class="menu_sub_items" id="bt_visualizaciones_auditorias">
                Auditoría de OKRs
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["visualizaciones_derivacion"]) { $mstrr_3 = true; ?>
        <a href="<?php echo $url; ?>?pg=visualizaciones/okrs_cascada">
            <li class="menu_sub_items" id="bt_visualizaciones_derivaciones">
                Derivación de OKRs
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["visualizaciones_timeline"]) { $mstrr_3 = true; ?>
        <a href="<?php echo $url; ?>?pg=visualizaciones/timeline">
            <li class="menu_sub_items" id="bt_visualizaciones_timeline">
                Timeline 
            </li>
        </a>
    <?php } ?>

</div>

<?php
if ($mstrr_3 == false) {
	echo '<script> $("#mod_visualizaciones").hide(); </script>';
}
?>
