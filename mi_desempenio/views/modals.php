<?php
if ($_SESSION["role_plataforma_valentina"] == 1 || $_SESSION["role_plataforma_valentina"] == 2) {
	$colorHeader = 'background-color: #ffc107 !important;';
	$colorFooter = 'background-color: #007BFF !important;';
	$colortext = 'color: black !important;';
	$colorFooterText = 'color: white !important;';
} else {
	$colorHeader = 'background-color: #3aae2a !important;';
	$colortext = 'color: white !important;';
}

$background1 = 'background-color: #28a745 !important;';
$background2 = 'background-color: #f4815e !important;';
// $boton1 = '<button class="btn btn-success" style="' . $background1 . 'color: white !important;border-radius: 30px;" onClick="FirmaAprobacion(' . $_SESSION["id_super_user_valentina"] . ')">CTA: Firmar PDI Ahora</button>';
// $boton2 = '<button class="btn btn-warning" style="' . $background2 . 'color: white !important;border-radius: 30px;" onClick="IntervencionGH(' . $_SESSION["id_super_user_valentina"] . ')">CTA: Solicitar Intervención</button>';
?>
<div class="modal fade" id="modalAviso" tabindex="-1" aria-labelledby="modalAvisoLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="contenidoAviso">
				<div class="row">
					<div class="col-md-12">
						<div class="card">
							<div class="card-header" style="<?php echo $colorHeader; ?>">
								<div class="row">
									<div class="col-md-12" style="text-align: center;">
										<h4 style="<?php echo $colortext; ?>">Estado Actual de su Proceso de Valoración de Competencias</h4>
									</div>
								</div>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-md-12" style="text-align: center;">
										<h4 style="color: black !important;"><?php echo $txt_estado; ?></h4>
										<p style="color: black !important;font-size: 1.2rem !important;"><?php echo $mensaje; ?></p>
									</div>
								</div>

                                <div>
                                    <?php include("views/plan_desarrollo_colaborador.php"); ?>
                                </div>
								<br>
								<div class="row">
									<div class="col-md-12" style="text-align: center;">
										<?php echo $boton1; ?>&nbsp;&nbsp;<?php echo $boton2; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>