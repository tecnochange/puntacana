<?php
//PARA OCULTAR EL MODULO EN CASO DE NO TENER NINGUN BOTON ACTIVO
$mstrr_4 = false;

$queryValidarAdministrador = mysqli_query($connect_kpis, "SELECT * FROM Administradores_Kpi WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND id_empleado = '".$user_log["id"]."' AND estado = 1 ");

$queryRelacionesValidarKpis = mysqli_query($connect_admin, "SELECT * FROM Relaciones_Laborales WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND id_empleado = '".$user_log["id"]."' AND mod_kpis = 'on' ");

?>

<?php //if($user_log["id"] == 374 || $user_log["id"] == 85 || $user_log["id"] == 261 || $user_log["id"] == 4412 || $user_log["id"] == 4397 || $user_log["id"] == 153 || $user_log["id"] == 2701 ){ ?>


<li class="menu_groups" data-bs-toggle="collapse" data-bs-target="#menuKpis" aria-expanded="true" id="mod_kpis">
    <table width="100%">
        <tr>
            <td width="25">
                <i class="bx bx-chart menu_icon"></i>
            </td>
            <td class="text_lateral">
                KPIs
            </td>
            <td width="30" align="right">
                <i class="bx bx-arrow-to-bottom" style="color: #9f9f9f;"></i>
            </td>
        </tr>
    </table>
</li>

<div class="collapse" id="menuKpis">

    <?php if ( $VALIDAR_MENU["kpis_auditoria"] || $queryRelacionesValidarKpis->num_rows > 0 ) { $mstrr_4 = true; ?>
        <a href="<?php echo $url; ?>?pg=kpis/auditoria">
            <li class="menu_sub_items" id="bt_auditoria">
                Auditoria KPI  
            </li>
        </a>
    <?php } ?>

    <?php 
        //ESTA VISTA SOLO SE HABILITA PARA EL ADMINISTRADOR CON PERMISO O PARA LAS PERSONAS RELACIONADAS EN Delegar Administradores KPIs
        if ($VALIDAR_MENU["kpis_reporte_general"] || $queryValidarAdministrador->num_rows > 0  ) { $mstrr_4 = true; 
    ?>
        <a href="<?php echo $url; ?>?pg=kpis/generales">
            <li class="menu_sub_items" id="bt_reporte_general">
                Reporte General KPis
            </li>
        </a>
    <?php } ?>

    <?php 
        if ($queryRelacionesValidarKpis->num_rows > 0) { $mstrr_4 = true; 
    ?>
        <a href="<?php echo $url; ?>?pg=kpis/generales_relaciones">
            <li class="menu_sub_items" id="bt_reporte_relaciones">
                Reporte Relaciones
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["kpis_administradores"]) { $mstrr_4 = true; ?>
        <a href="<?php echo $url; ?>?pg=kpis/administradores">
            <li class="menu_sub_items" id="bt_administradores">
               Delegar Administradores de KPIs 
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["kpis_objetivos_sg"]) { $mstrr_4 = true; ?>
        <a href="<?php echo $url; ?>?pg=kpis/objetivos_sg">
            <li class="menu_sub_items" id="bt_objetivos_sg">
               Objetivos SG  
            </li>
        </a>
    <?php } ?>


    <?php  if ( $VALIDAR_MENU["kpis_crear"] || $queryValidarAdministrador->num_rows > 0) { $mstrr_4 = true; ?>
         <a href="<?php echo $url; ?>?pg=kpis/detalle/crear_kpi">
            <li class="menu_sub_items" id="bt_crear">
                Crear KPI 
            </li>
        </a>
    <?php } ?>






    <?php if ($VALIDAR_MENU["kpis_crear"]) {  $mstrr_4 = true; ?>
    <?php } ?>






    <?php if ($VALIDAR_MENU["kpis_mis_kpis"]) { $mstrr_4 = true; ?>
        <a href="<?php echo $url; ?>?pg=kpis/mis_kpis">
            <li class="menu_sub_items" id="bt_mis_kpis">
                Ver Mis KPIs 
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["kpis_mi_equipo"]) { $mstrr_4 = true; ?>
        <a href="<?php echo $url; ?>?pg=kpis/kpis_equipo&page=1" style="display:none">
            <li class="menu_sub_items" id="bt_kpis_equipo">
                Ver KPIs de Mi Equipo
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["kpis_compañia"] || $queryValidarAdministrador->num_rows > 0 ) { $mstrr_4 = true; ?>
        <a href="<?php echo $url; ?>?pg=kpis/kpis_empresa">
            <li class="menu_sub_items" id="bt_kpis_compania">
                Todos los KPIs de la Compañía 
            </li>
        </a>
    <?php } ?>

    <?php if ($VALIDAR_MENU["kpis_seguimiento"]) { $mstrr_4 = true; ?>
        <a href="<?php echo $url; ?>?pg=kpis/seguimiento_equipo">
            <li class="menu_sub_items" id="bt_seguimiento">
                Seguimiento de Equipo
            </li>
        </a>
    <?php } ?>


</div>

<?php
if ($mstrr_4 == false) {
	echo '<script> $("#mod_kpis").hide(); </script>';
}
?>


<?php //} ?>