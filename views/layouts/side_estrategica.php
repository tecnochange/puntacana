<?php
//PARA OCULTAR EL MODULO EN CASO DE NO TENER NINGUN BOTON ACTIVO
$mstrr_8 = false;
?>

<li class="menu_groups" data-bs-toggle="collapse" data-bs-target="#menuEstrategia" aria-expanded="true" id="mod_estrategia">
    <table width="100%">
        <tr>
            <td width="25">
                <i class="bx bx-vector menu_icon"></i>
            </td>
            <td class="text_lateral">
                Configuración Estratégica
            </td>
            <td width="30" align="right">
                <i class="bx bx-arrow-to-bottom" style="color: #9f9f9f;"></i>
            </td>
        </tr>
    </table>
</li>

<div class="collapse" id="menuEstrategia">

    <?php if ($VALIDAR_MENU["estrategia_configuracion"]) { $mstrr_8 = true; ?>
        <a href="<?php echo $url; ?>?pg=estrategica/configurar">
            <li class="menu_sub_items" id="bt_estrategia_configuracion">
                Configuración General
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["estrategia_balanced"]) { $mstrr_8 = true; ?>
        <a href="<?php echo $url; ?>?pg=estrategica/dimensiones">
            <li class="menu_sub_items" id="bt_estrategia_balanced">
                Balanced Scorecard (BSC)
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["estrategia_escala"]) { $mstrr_8 = true; ?>
        <a href="<?php echo $url; ?>?pg=estrategica/escala_medicion">
            <li class="menu_sub_items" id="bt_estrategia_escala">
                Escala de Medición 
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["estrategia_objetivos"]) { $mstrr_8 = true; ?>
        <a href="<?php echo $url; ?>?pg=estrategica/objetivos">
            <li class="menu_sub_items" id="bt_estrategia_objetivos">
                Definir Objetivos Estratégicos 
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["estrategia_ponderacion"]) { $mstrr_8 = true; ?>
        <a href="<?php echo $url; ?>?pg=estrategica/ponderacion_okrs">
            <li class="menu_sub_items" id="bt_estrategia_ponderacion">
                Ponderación de OKRs 
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["estrategia_relaciones_laborales"]) { $mstrr_8 = true; ?>
        <a href="<?php echo $url; ?>?pg=estrategica/relaciones_laborales">
            <li class="menu_sub_items" id="bt_estrategia_relaciones_laborales">
                Gestión de Relaciones Laborales
            </li>
        </a>
    <?php } ?>



</div>

<?php
if ($mstrr_8 == false) {
	echo '<script> $("#mod_estrategia").hide(); </script>';
}
?>