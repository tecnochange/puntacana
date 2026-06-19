<?php if ($_SESSION["anio_fill"]) {
	$queryMA3 = mysqli_query($connect_valentina, "SELECT * FROM Menu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 3");
	$dataMA3 = mysqli_fetch_array($queryMA3);
	$querySM31 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 3 AND id_submenu = 16");
	$dataSM31 = mysqli_fetch_array($querySM31);
	$querySM32 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 3 AND id_submenu = 17");
	$dataSM32 = mysqli_fetch_array($querySM32);
	$querySM33 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 3 AND id_submenu = 18");
	$dataSM33 = mysqli_fetch_array($querySM33);
	$querySM34 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 3 AND id_submenu = 19");
	$dataSM34 = mysqli_fetch_array($querySM34);
	$querySM35 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 3 AND id_submenu = 64");
	$dataSM35 = mysqli_fetch_array($querySM35);
?>
	<li id="nav_desempenio"><a><i class="fas fa-tasks"></i> <?php echo $dataMA3["nombre"]; ?><span class="fas fa-chevron-left"></span></a>
		<ul class="nav child_menu" id="menu_desempenio">
			<?php if ($_SESSION["role_plataforma"] == 1 || $permisoDCD == true) { ?>
				<li id="bt_desempenio_individual_consolidado_desempenio">
					<a href="<?php echo $url; ?>?pg=desempenio_individual/consolidado_desempenio">Consolidado Desempeño..</a>
				</li>
			<?php }
			if ($_SESSION["role_plataforma"] == 1 || $permisoDCC == true) { ?>
				<li id="bt_desempenio_individual_competencias" style="display:none">
					<a href="<?php echo $url; ?>?pg=desempenio_individual/competencias"><?php echo $dataSM32["nombre"]; ?></a>
				</li>
			<?php }
			if ($_SESSION["role_plataforma"] == 1 || $permisoDCK == true) { ?>
				<li id="bt_desempenio_individual_consolidado_individual_kpi" style="display:none">
					<a href="<?php echo $url; ?>?pg=desempenio_individual/consolidado_individual_kpi"><?php echo $dataSM31["nombre"]; ?></a>
				</li>
			<?php }
			if ($_SESSION["role_plataforma"] == 1 || $permisoDCO == true) { ?>
				<li id="bt_desempenio_individual_consolidado_individual_okr" style="display:none">
					<a href="<?php echo $url; ?>?pg=desempenio_individual/consolidado_individual_okr"><?php echo $dataSM33["nombre"]; ?></a>
				</li>
			<?php }
			if ($_SESSION["role_plataforma"] == 1 || $permisoDMD == true) { ?>
				<li id="bt_desempenio_individual_consolidado_individual_desempenio">
					<a href="<?php echo $url; ?>?pg=desempenio_individual/consolidado_individual_desempenio"><?php echo $dataSM34["nombre"]; ?></a>
				</li>
			<?php } ?>
		</ul>
	</li>
<?php } ?>