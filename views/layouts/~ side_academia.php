<li class="menu_groups" data-bs-toggle="collapse" data-bs-target="#menuGoForExpert" aria-expanded="true">
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

<div class="collapse" id="menuGoForExpert">

    <?php if ($VALIDAR_MENU["goforexpert_mis_cursos"]) { ?>
        <a href="<?php echo $url; ?>?pg=goforexpert/programas">
            <li class="menu_sub_items" id="bt_goforexpert_mis_cursos">
                Mis Cursos
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["goforexpert_programas"]) { ?>
        <a href="<?php echo $url; ?>?pg=goforexpert/admin/programas">
            <li class="menu_sub_items" id="bt_goforexpert_programas">
                Programas <?php echo $_SESSION['anio']; ?>
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["goforexpert_cursos"]) { ?>
        <a href="<?php echo $url; ?>?pg=goforexpert/admin/cursos">
            <li class="menu_sub_items" id="bt_goforexpert_cursos">
                Cursos
            </li>
        </a>
    <?php } ?>


     <?php if ($VALIDAR_MENU["goforexpert_cohortes"]) { ?>
        <a href="<?php echo $url; ?>?pg=goforexpert/admin/cohortes">
            <li class="menu_sub_items" id="bt_goforexpert_cohortes">
                Cohortes
            </li>
        </a>
    <?php } ?>

     <?php if ($VALIDAR_MENU["goforexpert_reportes"]) { ?>
        <a href="<?php echo $url; ?>?pg=goforexpert/admin/final">
            <li class="menu_sub_items" id="bt_goforexpert_reportes">
                Reportes
            </li>
        </a>
    <?php } ?>

       
</div>