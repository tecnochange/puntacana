<?php
//PARA OCULTAR EL MODULO EN CASO DE NO TENER NINGUN BOTON ACTIVO
$mstrr_1 = false;
?>

<li class="menu_groups" data-bs-toggle="collapse" data-bs-target="#menuDesempenio" aria-expanded="true" id="mod_desempenio">
    <table width="100%">
        <tr>
            <td width="25">
                <i class="bx bx-check-square menu_icon"></i>
            </td>
            <td class="text_lateral">
                Desempeño
            </td>
            <td width="30" align="right">
                <i class="bx bx-arrow-to-bottom" style="color: #9f9f9f;"></i>
            </td>
        </tr>
    </table>
</li>

<div class="collapse" id="menuDesempenio">

    <?php if ($VALIDAR_MENU["desempenio_consolidado"] || $user_log["permiso_relaciones_laboradores_competencias"]) { $mstrr_1 = true; ?>
        <a href="<?php echo $url; ?>?pg=desempenio/consolidado">
            <li class="menu_sub_items" id="bt_desempenio_consolidado">
                Consolidado Desempeño <?php echo $_SESSION['anio']; ?>
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["desempenio_mi_desempenio"]) { $mstrr_1 = true; ?>
        <a href="<?php echo $url; ?>?pg=desempenio/mi_desempenio">
            <li class="menu_sub_items" id="bt_desempenio_mis_objetivos">
                Mi Desempeño <?php echo $_SESSION['anio']; ?>
            </li>
        </a>
    <?php } ?>

</div>

<?php
if ($mstrr_1 == false) {
	echo '<script> $("#mod_desempenio").hide(); </script>';
}
?>