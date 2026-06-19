<div id="sidebar">
	<div style="height: 60px"></div>

	<div class="base_lateral">

        <!-- USER --> 
		<div class="menu_usuario">

			<div style="color: #007bff; margin-top: 20px;">
                <b><?php echo $user_log["nombre"]." ".$user_log["apellidos"]; ?></b>
                
            </div>
            <div style="color: #6b21ff; margin-bottom: 10px;"><?php echo $user_log["cargo"]; ?></div>

            <div class="img-circle profile_img foto_menu_lateral" style="background-image: url(<?php echo $user_log["foto"]; ?>); margin-bottom: 10px;">
            </div>

            <div style="font-size: 12px;">
				<?php
				foreach ($roles_usr as $rl) {
					$queryRlUrs = mysqli_query($connect_admin, "SELECT * FROM Roles WHERE id = '" . $rl . "' ");
					$dataRlUrs = mysqli_fetch_array($queryRlUrs);
					echo $dataRlUrs["nombre"] . "<br>";
				}
				?>
			</div>
			
		</div>

        <!-- USER --> 
		<ul class="list-unstyled">

			<a href="<?php echo $url; ?>?pg=home" style="display:none">
				<li class="menu_groups" id="bt_home">
					<table width="100%">
						<tr>
							<td width="25">
								<i class="bx bx-home menu_icon"></i>
							</td>
							<td class="text_lateral">Inicio</td>
						</tr>
					</table>
				</li>
			</a>

			<!-- IMPORTANTE!! VERIFICAR EN LA BASE DE DATOS EL ID DE CADA MODULO -->
			<?php if(ValidarModulosLic('mod_desempenio')){include("views/layouts/side_desempenio.php"); } ?>

            <?php if(ValidarModulosLic('mod_okrs_equipo')){include("views/layouts/side_reportes.php"); } ?>
            <?php if(ValidarModulosLic('mod_okrs_equipo')){include("views/layouts/side_visualizaciones.php"); } ?>
            <?php if(ValidarModulosLic('mod_okrs_equipo')){include("views/layouts/side_okrs.php"); } ?>
            
            <?php //if(ValidarModulosLic('mod_okrs')){include("views/layouts/side_okrs.php"); } ?>
            
            <?php if(ValidarModulosLic('mod_valoracion')){include("views/layouts/side_competencias.php"); } ?>

            <?php if(ValidarModulosLic('mod_desempenio')){include("views/layouts/side_kpis.php"); } ?>
            <?php if(ValidarModulosLic('mod_endomarketing')){include("views/layouts/side_lecciones.php"); } ?>
            <?php if(ValidarModulosLic('mod_endomarketing')){include("views/layouts/side_endomarketing.php"); } ?>
            <?php if(ValidarModulosLic('mod_admin')){include("views/layouts/side_administrativo.php"); } ?>
            <?php if(ValidarModulosLic('mod_estrategica')){include("views/layouts/side_estrategica.php"); } ?>
            
            <?php if(ValidarModulosLic('mod_academia')){include("views/layouts/side_academia.php"); } ?>

			<a href="<?php echo $url; ?>?pg=logout">
				<li class="menu_groups">
					<table width="100%">
						<tr>
							<td width="25">
								<i class="bx bx-log-out menu_icon" style=" margin-right: 7px;"></i>
							</td>
							<td class="text_lateral">Salir</td>
						</tr>
					</table>
				</li>
			</a>

		</ul>

	</div>



</div>