<?php
//PARA OCULTAR EL MODULO EN CASO DE NO TENER NINGUN BOTON ACTIVO
$mstrr_7 = false;
?>

<li class="menu_groups" data-bs-toggle="collapse" data-bs-target="#menuEmpresa" aria-expanded="true" id="mod_empresa">
    <table width="100%">
        <tr>
            <td width="25">
                <i class="bx bx-buildings menu_icon"></i>
            </td>
            <td class="text_lateral">
                Empresa
            </td>
            <td width="30" align="right">
                <i class="bx bx-arrow-to-bottom" style="color: #9f9f9f;"></i>
            </td>
        </tr>
    </table>
</li>

<div class="collapse" id="menuEmpresa">

    <?php if ($VALIDAR_MENU["empresa_auditoria"]) { $mstrr_7 = true; ?>
        <a href="<?php echo $url; ?>?pg=empresa/auditoria_admin">
            <li class="menu_sub_items" id="bt_empresa_auditoria">
                Auditoría 
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["empresa_primer_nivel"]) { $mstrr_7 = true; ?>
        <a href="<?php echo $url; ?>?pg=empresa/vicepresidencias">
            <li class="menu_sub_items" id="bt_empresa_vicepresidencias">
                Vicepresidencias
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["empresa_segundo_nivel"]) { $mstrr_7 = true; ?>
        <a href="<?php echo $url; ?>?pg=empresa/areas">
            <li class="menu_sub_items" id="bt_empresa_areas">
                Áreas
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["empresa_estructura"]) { $mstrr_7 = true; ?>
        <a href="<?php echo $url; ?>?pg=empresa/estructura_empresa">
            <li class="menu_sub_items" id="bt_empresa_estructura">
                Estructura 
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["empresa_cargos"]) { $mstrr_7 = true; ?>
        <a href="<?php echo $url; ?>?pg=empresa/cargos">
            <li class="menu_sub_items" id="bt_empresa_cargos">
                Cargos 
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["empresa_colaboradores"]) { $mstrr_7 = true; ?>
        <a href="<?php echo $url; ?>?pg=empresa/colaboradores">
            <li class="menu_sub_items" id="bt_empresa_colaboradores">
                Colaboradores 
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["empresa_lideres"]) { $mstrr_7 = true; ?>
        <a href="<?php echo $url; ?>?pg=empresa/lideres">
            <li class="menu_sub_items" id="bt_empresa_lideres">
                Líderes
            </li>
        </a>
    <?php } ?>

     

     <?php if ($VALIDAR_MENU["empresa_nivel_jerarquico"]) { $mstrr_7 = true; ?>
        <a href="<?php echo $url; ?>?pg=empresa/nivel_jerarquico">
            <li class="menu_sub_items" id="bt_empresa_nivel_jerarquico">
                Niveles 
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["empresa_posiciones"]) { $mstrr_7 = true; ?>
        <a href="<?php echo $url; ?>?pg=empresa/posiciones">
            <li class="menu_sub_items" id="bt_empresa_posiciones">
                Posiciones
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["empresa_posiciones"]) { $mstrr_7 = true; ?>
        <a href="<?php echo $url; ?>?pg=empresa/configuracion">
            <li class="menu_sub_items" id="bt_empresa_configurar">
                Apariencia
            </li>
        </a>
    <?php } ?>

    
</div>

<?php
if ($mstrr_7 == false) {
	echo '<script> $("#mod_empresa").hide(); </script>';
}
?>