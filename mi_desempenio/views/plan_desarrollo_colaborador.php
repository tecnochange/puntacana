<div class="table-responsive">
<table class="table table-bordered" style="width:100%; ">
								<thead>
									<tr>
										<th>Año</th>
										<th>Competencia</th>
										<th>Plan de Acción</th>
										<th>Prioridad</th>
										<th>Jefe</th>
										<th>Fecha Inicio</th>
										<th>Fecha Entrega</th>
										<th>Estado</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$queryPDIS = mysqli_query($connect_valoracion, "SELECT *, YEAR(created_at) AS anio FROM Pdi_Competencias WHERE id_empleado = '" . $_SESSION["id_super_user_valentina"] . "' AND YEAR(created_at) = '".$_SESSION['periodo_desempenio']."'  ");
									while ($dataPDIS = mysqli_fetch_array($queryPDIS)) {

										$queryComp = mysqli_query($connect_valoracion, "SELECT * FROM Competencias WHERE id = '" . $dataPDIS["id_competencia"] . "' ");
										$dataComp = mysqli_fetch_array($queryComp);
										$QRYJefes = mysqli_query($connect_admin, "SELECT nombre AS NOMBRES FROM Empleados WHERE id = '" . $dataPDIS['id_jefe'] . "'");
										$datosJefes = mysqli_fetch_array($QRYJefes);
										$nombreJefe = $datosJefes['NOMBRES'];
										switch ($dataPDIS['estado']) {
											case 1:
												$txtEstado = 'Sin Iniciar';
												$colorEstado = 'badge bg-light';
												$colorBadge = 'black';
												break;
											case 2:
												$txtEstado = 'En Proceso';
												$colorEstado = 'badge bg-primary';
												$colorBadge = 'white';
												break;
											case 3:
												$txtEstado = 'En Revisión';
												$colorEstado = 'badge bg-warning';
												$colorBadge = 'black';
												break;
											case 4:
												$txtEstado = 'Completado';
												$colorEstado = 'badge bg-success';
												$colorBadge = 'white';
												break;
										}

										switch ($dataPDIS['prioridad']) {
											case 1:
												$txtPrioridad = 'Bajo';
												$colorPrioridad = 'badge bg-light';
												$colorBadgeP = 'black';
												break;
											case 2:
												$txtPrioridad = 'Medio';
												$colorPrioridad = 'badge bg-success';
												$colorBadgeP = 'white';
												break;
											case 3:
												$txtPrioridad = 'Alto';
												$colorPrioridad = 'badge bg-warning';
												$colorBadgeP = 'black';
												break;
											case 4:
												$txtPrioridad = 'Urgente';
												$colorPrioridad = 'badge bg-danger';
												$colorBadgeP = 'white';
												break;
										}

									?>
										<tr>
											<td><?php echo $dataPDIS['anio']; ?></td>
											<td><?php echo $dataComp["nombre"]; ?></td>
											<td><?php echo $dataPDIS["plan_accion"]; ?></td>
											<td><span class="<?php echo $colorPrioridad; ?>" style="color:<?php echo $colorBadgeP; ?>  !important;font-size: 13px !important;"><b><?php echo $txtPrioridad; ?></b></span></td>
											<td><?php echo $nombreJefe; ?></td>
											<td><?php echo $dataPDIS["fecha_inicia"]; ?></td>
											<td><?php echo $dataPDIS["fecha_finaliza"]; ?></td>
											<td><span class="<?php echo $colorEstado; ?>" style="color:<?php echo $colorBadge; ?>  !important;font-size: 13px !important;"><b><?php echo $txtEstado; ?></b></span></td>
											
										</tr>

									<?php
									}
									?>
								</tbody>
							</table>
</div>