<script>
	$(document).ready(function() {
		$('#menuKpis').collapse();
		$('#bt_reporte_general').addClass('active');
	});
</script>

<?php
include("app/models/kpis/KpisServicios.php");
$ClassKpisServicios = new KpisServicios($user_log["id_empresa"]);
$vicepresidencias_listas = $ClassKpisServicios->vicepresidencias_lista($user_log["id_empresa"]);
?>

<style>
	/* Margen debajo de la barra de herramientas (botones) */
	.dt-buttons {
		margin-bottom: 15px !important;
	}
</style>

<div class="container-fluid pb-2">

	<!-- TITULO -->
	<div class="card mb-3">
		<div class="card-header">
			<h3>Reporte General de KPIs</h3>
		</div>
	</div>

	<!-- FILTROS -->
	<div class="row">
		<div class="col-md-12">
			<?php include("views/kpis/componentes/filtros.php"); ?>
		</div>
	</div>

	<div class="card mb-3">

		<div class="accordion accordion-flush" id="accordionVicepresidencias">

			<?php foreach ($vicepresidencias_listas as $vicepresidencia) { ?>

				<div class="accordion-item card">
					<div class="card-body" style="padding: 5px 20px;">
						<div class="row">
							<div class="col-md-4 d-flex align-items-center">
								<h5><?= $vicepresidencia["nombre"]; ?></h5>
							</div>

							<div class="col-md-2 d-flex align-items-center">
								<div class="text-left" style="width: 100%;">
									Lider:
									<?php
									$min_lider = '';
									foreach ($vicepresidencia["array_lideres"] as $lider) {
										if ($lider["id"]) {
											$min_lider .= '
											<img src="https://goforagile.com/recursos/' . $lider["foto"] . '"
												width="32" height="32"
												class="foto_miniaturas"
												title="' . $lider["nombre"] . '"
												style="cursor:pointer;" 
												onclick="FichaEmpleado(' . $lider["id"] . ')">
											';
										}
									}

									if ($min_lider == '') {
										$min_lider = 'Sin asignar';
									}

									echo $min_lider;
									?>
								</div>
							</div>

							<div class="col-md-2 d-flex align-items-center justify-content-center">
								Kpis Asignados <br>
								<span class="px-1"><b><?= $vicepresidencia["kpis_asignados"]; ?></b></span>
							</div>

							<div class="col-md-2 d-flex align-items-center">
								<div class="text-left" style="width: 100%;">
									Progreso
									<div class="progress">
										<div class="progress-bar bg-success" role="progressbar" style="width: <?= $vicepresidencia["avance_vicepresidencia"]; ?>%; background-color:<?= EscalaColor($vicepresidencia["avance_vicepresidencia"]); ?> !important;" aria-valuenow="<?= round($vicepresidencia["avance_general"]); ?>" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<b><?= round($vicepresidencia["avance_vicepresidencia"]); ?>%</b>
								</div>
							</div>

							<div class="col-md-1 d-flex align-items-center justify-content-center">
								<a href="<?php echo $url; ?>?pg=kpis/consolidados/vicepresidencias&id=<?= $vicepresidencia["id"]; ?>">
									<i class="bi bi-eye-fill fs-4"></i>
								</a>
							</div>

							<div class="col-md-1">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#item_<?= $vicepresidencia["id"]; ?>" aria-expanded="false" aria-controls="item_<?= $vicepresidencia["id"]; ?>" style="margin-top: 14px;"></button>
							</div>

							<!-- AQUI SE PINTA LA TABLA CON LAS AREAS -->
							<div class="col-md-12">
								<div id="item_<?= $vicepresidencia["id"]; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionVicepresidencias">

									<table id="table_kpis_asignados_<?= $vicepresidencia["id"]; ?>" class="table table-bordered">
										<thead>
											<tr>
												<th>Nombre Área Proceso</th>
												<th class="text-center">Líder</th>
												<th class="text-center">KPIS Asignados</th>
												<th class="text-center">Progreso</th>
												<th class="text-center">Subprocesos</th>
												<th class="text-center">Acciones</th>
											</tr>
										</thead>
										<tbody>
											<?php
											foreach ($vicepresidencia["areas_lista"] as $area) {

												$lider = $area["lider_area"];
												$min_lider_area = 'Sin asignar';

												if ($lider["id"]) {
													$min_lider_area = '
													<img src="https://goforagile.com/recursos/' . $lider["foto"] . '"
														width="32" height="32"
														class="foto_miniaturas mb-1"
														title="' . $lider["nombre"] . '" 
														style="cursor:pointer;" 
														onclick="FichaEmpleado(' . $lider["id"] . ')">
													';
												}
											?>
												<tr>
													<td><?php echo $area["nombre_area"]; ?></td>
													<td><?php echo $min_lider_area; ?></td>
													<td class="text-center"><?php echo $area["kpis_cantidad"]; ?></td>
													<td>
														<div class="progress">
															<div class="progress-bar bg-success"
																role="progressbar"
																style="width: <?php echo $area["avance_area"]; ?>%; background-color: <?php echo $area["avance_color"]; ?> !important;"
																aria-valuenow="<?php echo $area["avance_area"]; ?>"
																aria-valuemin="0"
																aria-valuemax="100">
															</div>
														</div>
														<b><?php echo $area["avance_area"]; ?>%</b>
													</td>

													<!-- boton subproceso -->
													<?php $collapseId = 'subprocesos_' . $vicepresidencia["id"] . '_' . $area["area_proceso"]; ?>
													<td class="text-center">
														<button class="btn btn-sm btn-outline-primary"
															data-bs-toggle="collapse"
															data-bs-target="#<?= $collapseId; ?>">
															<i class="bi bi-diagram-3"></i>
														</button>
													</td>

													<td>
														<a href="<?php echo $url; ?>?pg=kpis/consolidados/areas&id=<?php echo $area["area_proceso"]; ?>">
															<i class="bi bi-eye-fill fs-4"></i>
														</a>
													</td>
												</tr>

												<!-- CONTENIDO SUBPROCESOS -->
												<tr>
													<td colspan="6" class="bg-light p-0">
														<div id="<?= $collapseId; ?>" class="collapse">
															<div class="p-3">

																<!-- TABLA DE SUBPROCESOS -->
																<table class="table table-sm table-bordered mb-0">
																	<thead class="table-secondary">
																		<tr>
																			<th>Subproceso</th>
																			<th class="text-center">KPI Asignados</th>
																			<th class="text-center">Progreso</th>
																			<!-- <th class="text-center">Acciones</th> -->
																		</tr>
																	</thead>
																	<tbody>
																		<?php
																		//SE AGRUPAN POR UNIDAD ORGANIZATIVA
																		$agrupados = [];
																		foreach ($area["kpis_lista"] as $kpi) {
																			$subproceso = $kpi["subproceso"];
																			$agrupados[$subproceso][] = $kpi;
																		}
																		?>

																		<!-- SE RECORRE CADA GRUPO DE SUBPROCESOS -->
																		<?php foreach ($agrupados as $subproceso => $kpis): ?>
																			<?php
																			$nombre_unidad = null;
																			$total_avance = 0;
																			$cantidad = count($kpis);

																			foreach ($kpis as $kpi) {
																				//dd($kpi);
																				if (!$nombre_unidad && !empty($kpi["nombre_unidad"])) {
																					$id_unidad = $kpi["subproceso"];
																					$nombre_unidad = $kpi["nombre_unidad"];
																				}

																				// Sumar avance
																				$total_avance += $kpi["avance_kpis"] ?? 0;
																			}

																			// Calcular promedio
																			$promedio_avance = $cantidad > 0 ? $total_avance / $cantidad : 0;
																			$color = EscalaColor($promedio_avance);
																			?>
																			<tr>
																				<td><?//= $id_unidad." - "; ?><?= $nombre_unidad ?? 'Sin definir'; ?></td>
																				<td class="text-center"><?php echo count($kpis); ?></td>
																				<td>
																					<div class="progress">
																						<div class="progress-bar"
																							role="progressbar"
																							style="width: <?= $promedio_avance; ?>%; background-color: <?= $color; ?>;"
																							aria-valuenow="<?= $promedio_avance; ?>"
																							aria-valuemin="0" aria-valuemax="100">
																							<?= round($promedio_avance); ?>%
																						</div>
																					</div>
																				</td>
																				<!-- <td class="text-center">
																					<i class="bi bi-eye-fill"></i>
																				</td> -->
																			</tr>
																		<?php endforeach; ?>

																	</tbody>
																</table>

															</div>
														</div>
													</td>
												</tr>

											<?php } ?>
										</tbody>
									</table>

								</div>
							</div>
						</div>
					</div>
				</div>

			<?php } ?>






























			<?php foreach ($vicepresidencias as $vicepresidencia): ?>
				<div class="accordion-item card">
					<div class="card-body">
						<div class="row">
							<div class="col-md-4 d-flex align-items-center">
								<h3><?= $vicepresidencia["nombre"]; ?></h3>
							</div>

							<div class="col-md-2 d-flex align-items-center">
								<div class="text-center" style="width: 100%;">
									<?php
									$lideres = explode(',', $vicepresidencia["id_lideres"]);
									$totalLideres = count($lideres);

									for ($i = 0; $i < min(2, $totalLideres); $i++):
										$responsableData = $ClassKpis->Empleado($lideres[$i]);
										$fotoLider = !empty($responsableData["foto"]) ? $responsableData["foto"] : "img_default.jpg";
										$modalId = "modal_responsable_" . $responsableData["id"];
									?>
										<img src="https://goforagile.com/recursos/<?= $fotoLider; ?>"
											width="32" height="32"
											class="foto_miniaturas mb-1"
											title="<?= $responsableData["nombre"]; ?>"
											style="cursor:pointer;"
											data-bs-toggle="modal"
											data-bs-target="#<?= $modalId ?>">
										<!-- Incluye el modal reusable -->
										<?php include "views/okrs/componentes/modal_responsable.php"; ?>
									<?php endfor; ?>

									<?php if ($totalLideres > 2): ?>
										<span class="badge rounded-circle bg-dark d-inline-flex justify-content-center align-items-center"
											style="cursor:pointer; width:32px; height:32px; font-size:13px;"
											data-bs-toggle="modal" data-bs-target="#modal_lideres_<?= $vicepresidencia["id"]; ?>">
											+<?= $totalLideres - 2 ?>
										</span>
									<?php endif; ?>
								</div>

								<!-- ========== MODAL RESPONSABLES CUANDO HAY MAS DE 2 ========= -->
								<?php if ($totalLideres > 2): ?>
									<div class="modal fade" id="modal_lideres_<?= $vicepresidencia["id"] ?>" tabindex="-1">
										<div class="modal-dialog modal-dialog-centered">
											<div class="modal-content">

												<div class="modal-header">
													<h5 class="modal-title">Responsables de la Iniciativa</h5>
													<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
												</div>

												<div class="modal-body">
													<?php for ($j = 0; $j < $totalLideres; $j++):
														$liderData = $ClassOkrsServicios->Empleado($totalLideres[$j]);
														$fotoIniFull = !empty($liderData["foto"]) ? $liderData["foto"] : "img_default.jpg";
													?>
														<div class="d-flex align-items-center mb-2">
															<img src="https://goforagile.com/recursos/<?= $fotoIniFull ?>"
																class="rounded-circle me-2"
																width="35" height="35"
																title="<?= $liderData["nombre"] ?>">
															<span><?= $liderData["nombre"] ?></span>
														</div>
													<?php endfor; ?>
												</div>

												<div class="modal-footer">
													<button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
												</div>

											</div>
										</div>
									</div>
								<?php endif; ?>
							</div>

							<div class="col-md-2 d-flex align-items-center justify-content-center">
								<!-- KPIS ASIGNADOS -->
								<?php
								$kpis_asignados = $ClassKpis->Kpis_Asignados($user_log["id_empresa"], $vicepresidencia["id"], $_SESSION["anio_fill"]);
								$Kpis_Asignados_area_proceso = $ClassKpis->Kpis_Asignados_area_proceso($user_log["id_empresa"], $vicepresidencia["id"], $_SESSION["anio_fill"]);
								?>
								Kpis Asignados <span class="px-1"><b><?= $vicepresidencia["cantidad"]; ?></b></span>
							</div>

							<div class="col-md-2 d-flex align-items-center">
								<div class="text-center" style="width: 100%;">
									Progreso
									<div class="progress">
										<div class="progress-bar bg-success" role="progressbar" style="width: <?= $vicepresidencia["avance_general"]; ?>%; background-color:<?= EscalaColor($vicepresidencia["avance_general"]); ?> !important;" aria-valuenow="<?= round($vicepresidencia["avance_general"]); ?>" aria-valuemin="0" aria-valuemax="100"></div>
									</div>
									<b><?= round($vicepresidencia["avance_general"]); ?>%</b>
								</div>
							</div>

							<div class="col-md-1 d-flex align-items-center justify-content-center">
								<a href="#&id_vicepresidencia=<?= $vicepresidencia["id"]; ?>">
									<i class="bi bi-eye-fill fs-4"></i>
								</a>
							</div>

							<div class="col-md-1">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#item_<?= $vicepresidencia["id"]; ?>" aria-expanded="false" aria-controls="item_<?= $vicepresidencia["id"]; ?>"></button>
							</div>

							<div class="col-md-12">
								<div id="item_<?= $vicepresidencia["id"]; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionVicepresidencias">
									<div class="accordion-body">
										<table id="table_kpis_asignados_<?= $vicepresidencia["id"]; ?>" class="table table-bordered">
											<thead>
												<tr>
													<th>Nombre Área Proceso</th>
													<th class="text-center">Líder</th>
													<th class="text-center">KPIS Asignados</th>
													<th class="text-center">Progreso</th>
													<th class="text-center">Subproceso</th>
													<th class="text-center">Acciones</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach ($Kpis_Asignados_area_proceso as $kpi_area_proceso): ?>

													<?php
													//print_r($kpi_area_proceso);
													?>

													<!-- KPIS ASIGNADOS POR AREA-->
													<?php
													$kpisAgrupados = [];
													foreach ($kpis_asignados as $kpi_asignado) {
														if ($kpi_asignado["area_proceso"] == $kpi_area_proceso["area_proceso"]) {
															if (!empty($kpi_asignado["subproceso"])) {
																$sub = $kpi_asignado["subproceso"] ?? 'Sin subproceso';
																$kpisAgrupados[$sub][] = $kpi_asignado;
															}
														}
													}
													$kpis_por_area = count(array_merge(...array_values($kpisAgrupados)));
													?>

													<tr>
														<td><?= $kpi_area_proceso["area_nombre"]; ?></td>
														<td class="text-center">
															<?php
															$responsableData = $kpi_area_proceso["lider"];
															$fotoLider = !empty($responsableData["foto"]) ? $responsableData["foto"] : "img_default.jpg";
															$modalId = "modal_responsable_" . $responsableData["id"];
															?>
															<img src="https://goforagile.com/recursos/<?= $fotoLider; ?>"
																width="32" height="32"
																class="foto_miniaturas mb-1"
																title="<?= $responsableData["nombre"]; ?>"
																style="cursor:pointer;"
																data-bs-toggle="modal"
																data-bs-target="#<?= $modalId ?>">
															<!-- Incluye el modal reusable -->
															<?php include "views/okrs/componentes/modal_responsable.php"; ?>
														</td>
														<td class="text-center"><?= $kpis_por_area; ?></td>
														<td class="text-center">
															<div style="width: 100%;">
																Progreso
																<div class="progress">
																	<div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $kpi_area_proceso["porcentaje_avance"] ?>%;" aria-valuenow="<?php echo $kpi_area_proceso["porcentaje_avance"] ?>" aria-valuemin="0" aria-valuemax="100">
																		<b><?php echo $kpi_area_proceso["porcentaje_avance"] ?>%</b>
																	</div>
																</div>
															</div>
														</td>

														<?php $collapseId = 'subprocesos_' . $vicepresidencia["id"] . '_' . $kpi_area_proceso["area_proceso"]; ?>
														<td class="text-center">
															<?php if (!empty($kpisAgrupados)): ?>
																<button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" data-bs-target="#<?= $collapseId; ?>">
																	<i class="bi bi-diagram-3"></i>
																</button>
															<?php endif; ?>
														</td>

														<td class="text-center">
															<a href="#&id_area_proceso=<?= $kpi_area_proceso["area_proceso"]; ?>">
																<i class="bi bi-eye-fill fs-4"></i>
															</a>
														</td>
													</tr>

													<!-- SUBPROCESOS -->
													<tr>
														<td colspan="6" class="bg-light p-0">
															<div id="<?= $collapseId; ?>" class="collapse">
																<div class="collapse-body-wrapper p-3">
																	<?php if (!empty($kpisAgrupados)): ?>
																		<table class="table table-sm table-bordered mb-0">
																			<thead class="table-secondary">
																				<tr>
																					<th>Subproceso</th>
																					<th>KPI Asignados</th>
																					<th>Progreso</th>
																					<th>Acciones</th>
																				</tr>
																			</thead>
																			<tbody>
																				<?php foreach ($kpisAgrupados as $subproceso => $kpis): ?>

																					<?php if ($subproceso): ?>
																						<?php $unidad_organizativa = $ClassKpis->Unidad_Organizativa($subproceso); ?>
																						<tr>
																							<td><?= $unidad_organizativa; ?></td>
																							<td class="text-center"><?= count($kpis); ?></td>
																							<td class="text-center">
																								<div style="width: 100%;">
																									Progreso
																									<div class="progress">
																										<div class="progress-bar bg-success" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
																											<b>25%</b>
																										</div>
																									</div>
																								</div>
																							</td>
																							<td class="text-center">
																								<a href="#&id_subproceso=<?= $subproceso; ?>">
																									<i class="bi bi-eye-fill fs-4"></i>
																								</a>
																							</td>
																						</tr>
																					<?php endif; ?>

																				<?php endforeach; ?>
																			</tbody>
																		</table>

																	<?php else: ?>
																		<div class="text-muted text-center">
																			No hay KPIs asociados a subprocesos
																		</div>
																	<?php endif; ?>
																</div>
															</div>
														</td>
													</tr>

												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			<?php endforeach; ?>

		</div>

	</div>

</div>

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">

<script>
	var api = '<?php echo $url; ?>api/kpis/';

	function FichaEmpleado(id_empleado) {

		var id_empresa = <?php echo $user_log["id_empresa"]; ?>

		$("#modal_empleado").modal("show");
		$("#body_empleado").html("Cargando...");

		jQuery.ajax({
				url: api + "ficha_empleado.php",
				type: 'post',
				data: {
					id_empresa: id_empresa,
					id_empleado: id_empleado,
				},
			})
			.done(function(resp) {
				$("#body_empleado").html(resp);
			})
			.fail(function(resp) {
				console.log(resp);
			})
			.always(function(resp) {});


	}
</script>