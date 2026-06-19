<?php
//PARA OCULTAR EL MODULO EN CASO DE NO TENER NINGUN BOTON ACTIVO
$mstrr_2 = false;
?>

<li class="menu_groups" data-bs-toggle="collapse" data-bs-target="#menuReportes" aria-expanded="true" id="mod_reportes">
    <table width="100%">
        <tr>
            <td width="25">
                <i class="bx bx-pie-chart-alt-2 menu_icon"></i>
            </td>
            <td class="text_lateral">
                Reportes
            </td>
            <td width="30" align="right">
                <i class="bx bx-arrow-to-bottom" style="color: #9f9f9f;"></i>
            </td>
        </tr>
    </table>
</li>

<div class="collapse" id="menuReportes">

    <?php if ($VALIDAR_MENU["reportes_general"]) { $mstrr_2 = true; ?>
        <a href="<?php echo $url; ?>?pg=reportes/consolidados">
            <li class="menu_sub_items" id="bt_reportes_consolidado">
                General
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["reportes_primer_nivel"]) { $mstrr_2 = true; ?>
        <a href="<?php echo $url; ?>?pg=reportes/vicepresidencias">
            <li class="menu_sub_items" id="bt_reportes_vicepresidencia">
                Vicepresidencia
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["reportes_segundo_nivel"]) { $mstrr_2 = true; ?>
        <a href="<?php echo $url; ?>?pg=reportes/areas">
            <li class="menu_sub_items" id="bt_reportes_areas">
                Áreas
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["reportes_individuales_lideres"]) { $mstrr_2 = true; ?>
        <a href="<?php echo $url; ?>?pg=reportes/lideres">
            <li class="menu_sub_items" id="bt_reportes_lideres">
                Reporte Individual
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["reportes_relacion"]) { $mstrr_2 = true; ?>
        <a href="<?php echo $url; ?>?pg=reportes/reporteGeneral" style="display:none">
            <li class="menu_sub_items" id="bt_reportes_relacion">
                Relación de OKRs y KPIs por Colaborador
            </li>
        </a>
    <?php } ?>


</div>

<?php
if ($mstrr_2 == false) {
	echo '<script> $("#mod_reportes").hide(); </script>';
}
?>