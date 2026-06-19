<script>
	$(document).ready(function() {
		$(".menu_section").addClass("active");
		$("#nav_competencias").addClass("active");
		jQuery("#menu_competencias").css("display", "none");
		$("#bt_comp_arbol").addClass("current-page");
	});
</script>

<?php
$queryCicloVal = mysqli_query($connect_valoracion, "SELECT * FROM Ciclos WHERE id = '" . $_SESSION['ciclo'] . "' ");
$dataCicloVal = mysqli_fetch_array($queryCicloVal);

// include("app/models/Collaborators.php");
// $ClassCollaborators = new Collaborators();
// $colaboradores = $ClassCollaborators->collaborators_list($_POST, $connect_valentina);
$filtros = " ";
if ($_POST["nombre"]) {
	$filtros .= " AND Empleados.nombre LIKE '%" . $_POST["nombre"] . "%' ";
}

$sentencia = "SELECT
Empleados.id AS id, Empleados.id_empresa AS id_empresa, Empleados.nombre AS nombre, Empleados.correo AS correo_corporativo, Empleados.correo_personal AS correo_personal, Empleados.telefono_movil AS celular, Empleados.documento AS documento, Empleados.role AS role, Empleados.estado AS id_estado,
Empleados.foto AS foto,
Posiciones.id AS id_posicion, Cargos.id AS id_cargo, Cargos.nombre AS nombre_cargo, Areas.nombre AS nombre_area, Areas.id AS id_area
FROM Empleados
LEFT JOIN Posiciones ON Empleados.id_posicion = Posiciones.id
LEFT JOIN Cargos ON Cargos.id = Posiciones.id_cargo
LEFT JOIN Areas ON Areas.id = Posiciones.id_area
WHERE Empleados.id > 0 " . $filtros . " AND Empleados.id_empresa = '" . $_SESSION["id_empresa"] . "'
AND Empleados.estado = 1
ORDER BY Empleados.nombre ASC
";

$query = mysqli_query($connect_valentina, $sentencia);
$querySM15 = mysqli_query($connect_valentina, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa"] . " AND estado = 1 AND id_menu = 1 AND id_submenu = 5");
$dataSM15 = mysqli_fetch_array($querySM15);

include("views/administrar/etiquetas.php");
?>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header" style="background-color: #FFFFFF !important;">
				<div class="row">
					<div class="col-md-12" style="text-align: start !important;">
						<h4><i class="fas fa-users" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo $dataSM15["nombre"]; ?> <b><?php echo $_SESSION["anio_ciclo"]; ?></b> Ciclo: <b><?php echo $dataCicloVal["nombre"]; ?></b></h4>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br>
<div class="container-fluid">


	<div class="row">
		<?php if ($_SESSION['ciclo'] != "") { ?>
			<div class="col-md-12">
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table border="1" id="arbol" class="display table" style="width:100%;">
								<thead class="thead-success">
									<tr>
										<th scope="col" width="15">Documento</th>
										<th scope="col">Nombres</th>
										<th scope="col"><?php echo $etiquetaAdminCargo; ?></th>
										<th scope="col"><?php echo $etiquetaAdminArea; ?></th>
										<th scope="col"></th>
										<th scope="col">Evaluadores</th>
										<th scope="col"><?php echo $etiquetaAdminCargo; ?> Evaluador</th>
										<th scope="col"><?php echo $etiquetaAdminArea; ?> Evaluador</th>
										<th scope="col">Tipo</th>
										<th scope="col">Acción</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		<?php } else { ?>
			<div class="col-md-12">
				<div class="alert alert-success" role="alert">
					Realizar
				</div>
			</div>
		<?php } ?>

	</div>
</div>


<script>
	var api = '<?php echo $url; ?>api/competencias_pc/';

	function Eliminar_Evaluador(id) {
		var confirmado = $("#modal_body").data("confirmado") || false;

		if (!confirmado) {
			$("#modal_body").html(
				'Está a punto de eliminar un evaluador. ESTO ELIMINARÁ LAS EVALUACIONES QUE REALIZÓ EL EVALUADOR. Esta acción es irreversible. ¿Está seguro?<br><br>'
			);
			$("#modal_body").append(
				'<button type="button" class="btn btn-danger btn-sm" id="confirmar_eliminar_evaluador">Confirmar</button>'
			);

			$("#modal_body").data("confirmado", false);

			$("#confirmar_eliminar_evaluador").off("click").on("click", function() {
				$("#modal_body").data("confirmado", true); // Marcar como confirmado
				Eliminar_Evaluador(id); // Llamar de nuevo a la función
			});

			$("#modal_general").modal("show");
		} else {
			jQuery.ajax({
				url: api + "eliminar_evaluador.php",
				type: "post",
				data: {
					id: id,
					url: "?pg=competencias_pc/arbol"
				},
				success: function(resp) {
					toastr.success("Evaluador eliminado correctamente", "¡Éxito!");
					$('#arbol').DataTable().ajax.reload();
					$("#modal_general").modal("hide");
				},
				error: function(xhr, status, error) {
					toastr.error("Hubo un problema al eliminar el evaluador. Intente nuevamente.");
				}
			});

			$("#modal_body").data("confirmado", false);
		}
	}

	$(document).ready(function() {
		$("#buscador").on("keyup", function() {
			var value = $(this).val().toLowerCase();
			$("#tabla_lista tr").filter(function() {
				$(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
			});
		});
	});
</script>

<script type="text/javascript">
	var api = '<?php echo $url; ?>api/competencias_pc/';
	$(document).ready(function() {
		$('#arbol').DataTable({
			destroy: true,
			ajax: {
				url: api + "arbol.php",
				type: "POST",
				data: function(d) {
					d.id_empresa = '<?php echo $_SESSION["id_empresa"]; ?>';
					d.url = '<?php echo $url; ?>';
					d.ciclo = '<?php echo $_SESSION['ciclo']; ?>';
					d.anio_ciclo = '<?php echo $_SESSION['anio_ciclo']; ?>';
					console.log(d);
				},
				dataSrc: 'data'
			},
			columns: [{
					data: "documento"
				},
				{
					data: "nombre_colaborador"
				},
				{
					data: "cargo_colaborador"
				},
				{
					data: "area_colaborador"
				},
				{
					data: "agregar"
				},
				{
					data: "nombre_evaluador",
					render: function(data, type, row) {
						return data;
					},
				},
				{
					data: "cargo_evaluador",
					render: function(data, type, row) {
						return data;
					},
				},
				{
					data: "area_evaluador",
					render: function(data, type, row) {
						return data;
					},
				},
				{
					data: "tipo",
					render: function(data, type, row) {
						return data;
					},
				},
				{
					data: "accion",
					render: function(data, type, row) {
						return data;
					},
				}
			],
			pageLength: 100,
			lengthMenu: [
				[10, 25, 50, -1],
				[10, 25, 50, "Todos"]
			],
			language: {
				processing: "Procesando...",
				search: "Buscar:",
				lengthMenu: "Mostrar _MENU_ registros.",
				info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
				infoEmpty: "Mostrando registros del 0 al 0 de 0 registros",
				infoFiltered: "(filtrado de un total de _MAX_ registros)",
				infoPostFix: "",
				loadingRecords: "Cargando...",
				zeroRecords: "No se encontraron resultados",
				emptyTable: "Ningún dato disponible en esta tabla",
				row: "Registro",
				export: "Exportar",
				paginate: {
					first: "Primero",
					previous: "Anterior",
					next: "Siguiente",
					last: "Ultimo"
				},
				aria: {
					sortAscending: ": Activar para ordenar la columna de manera ascendente",
					sortDescending: ": Activar para ordenar la columna de manera descendente"
				},
				select: {
					row: "registro",
					selected: "seleccionado"
				}
			},
			dom: '<"top"Blfp>rt<"bottom"lip><"clear">',
			buttons: [{
					extend: 'collection',
					text: 'Exportar',
					buttons: [
						'copy',
						'excel',
						'csv',
						{
							extend: 'pdfHtml5',
							text: 'PDF',
							orientation: 'landscape',
							pageSize: 'LEGAL'
						},
						{
							extend: 'print',
							customize: function(win) {
								$(win.document.body)
									.css('font-size', '10pt');

								$(win.document.body).find('table')
									.addClass('compact')
									.css('font-size', 'inherit');
							}
						}
					]
				}

			]
		});

	});
</script>