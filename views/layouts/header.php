<div id="logo_header">
	<a href="<?php echo $url; ?>?pg=desempenio/mi_desempenio">
		<img src="https://goforagile.com/recursos/<?php echo $user_log["logo"]; ?>" style="margin: 5px; height: 50px; padding: 2px;">
	</a>

	<a href="<?php echo $url; ?>?pg=perfil/colaborador/detalle" class="solo_movil">
		<i class="bx bx-user icons_menu_h" title="Mi Perfil"></i>
	</a>
	<a href="<?php echo $url; ?>?pg=logout" class="solo_movil">
		<i class="bx bx-log-out icons_menu_h" data-bs-toggle="tooltip" data-bs-placement="bottom" ></i>
	</a>
</div>

<div id="navbar_header" align="right">
			
	<i class="bx bx-align-middle btn_menu_lat" id="sidebarCollapse" ></i>

	
	
	<table class="e_web" style=" width: 130px; margin-top: 8px;">
		<tr>
			<td>
				<b>
                    <?php
                    foreach ($roles_usr as $rl) {
                        $queryRlUrs = mysqli_query($connect_valentina, "SELECT * FROM Roles WHERE id = '" . $rl . "' ");
                        $dataRlUrs = mysqli_fetch_array($queryRlUrs);
                        echo $dataRlUrs["nombre"] . "<br>";
                    }
                    ?>
                </b>
			</td>
			<td style="padding: 8px 20px;">
                <a href="<?php echo $url; ?>?pg=perfil/resumen">
					<i class="bx bx-user icons_menu_h" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Mi Perfil"></i>
				</a>
				<a href="<?php echo $url; ?>?pg=logout">
					<i class="bx bx-log-out icons_menu_h" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Cerrar sesión"></i>
				</a>
				
			</td>
			
		</tr>
	</table>
			
</div>