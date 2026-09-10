<script>
$(document).ready(function() {
    $('#menuDesempenio').collapse();
    $('#bt_desempenio_consolidado').addClass('active');
});
</script>

<?php
//OBTENEMOS LA INFORMACIÓN DE LOS COLABORADORES
include("app/models/estructura/Colaboradores.php");
$ClassColaboradores = new Colaboradores();
$array_colaboradores = $ClassColaboradores->colaboradores_lista(NULL, $connect_admin);

$filtros = " AND anio =  '".$_SESSION["anio_fill"]."' ";

if($_POST["anio_fill"]){
    $filtros = " AND anio = '".$_POST["anio_fill"]."' "; ;
}

if($_POST["vicepresidencia_fill"]){
    $filtros .= " AND id_vicepresidencia = '".$_POST["vicepresidencia_fill"]."' ";
}

if($_POST["area_fill"]){
    $filtros .= " AND id_area = '".$_POST["area_fill"]."' ";
}


if($_POST["unidad_fill"]){
    $filtros .= " AND id_unidad = '".$_POST["unidad_fill"]."' ";
}

if($_POST["nivel_fill"]){
    $filtros .= " AND id_nivel = '".$_POST["nivel_fill"]."' ";
}

if($_POST["cargo_fill"]){
    $filtros .= " AND id_cargo = '".$_POST["cargo_fill"]."' ";
}

//FILTRO PARA RELACIONES LABORALES
//FILTRO PARA RELACIONES LABORALES
//FILTRO PARA RELACIONES LABORALES
//FILTRO PARA RELACIONES LABORALES
$filtrosVP = $filtrosArea = " ";

$queryRL = mysqli_query($connect_admin, "SELECT * FROM Relaciones_Laborales WHERE id_empleado = '" . $_SESSION["id_user"] . "' AND estado = 1 AND mod_desempenio = 'on'");
if (mysqli_num_rows($queryRL) > 0) {
    $vp = $area = '';
    while ($dataRL = mysqli_fetch_array($queryRL)) {

        if ($dataRL["id_vp"] != '') {
            $vp .= $dataRL["id_vp"] . ',';
        }
        if ($dataRL["id_area"] != '') {
            $area .= $dataRL["id_area"] . ',';
        }
    }
    if ($vp != '') {
        $vpList = rtrim($vp, ",");
        $filtrosVP = " AND id_vicepresidencia IN ($vpList) ";
    }
    if ($area != '') {
        $areaList = rtrim($area, ",");
        $filtrosArea = " AND id_area IN ($areaList) ";
    }
}


$filtros .= $filtrosVP;
?>


<div class="container-fluid" style="max-width: 90%; margin: 0 auto;">

    <form action="" method="post">
    <div class="card mb-2">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <input type="hidden" name="formulario_filtrar" value="true">
					<select class="form-control form-control-sm" name="anio_fill">
								<option value="">Filtrar por Periodo...</option>
								<?php
								foreach ($Array_Anio_Desempenio as $periodo) {
									if ($_SESSION["anio_fill"] ==  $periodo[0]) {
										echo '<option value="' . $periodo[0] . '" selected>' . ((int)$periodo[1] - 1) . ' - ' . $periodo[1] . '</option>';
									} else {
										echo '<option value="' . $periodo[0] . '">' . ((int)$periodo[1] - 1) . ' - ' . $periodo[1] . '</option>';
									}
								}
								?>
					</select>
                </div>

                <div class="col-md-3 mb-3">
                    <select class="form-control form-control-sm" name="vicepresidencia_fill">
								<option value="">Vicepresidencia...</option>
								<?php
                                $queryVice = mysqli_query( $connect_admin, "SELECT id, nombre FROM Vicepresidencia WHERE estado = 1 AND id_empresa = '".$_SESSION["id_empresa"]."' ORDER BY nombre ASC");
		                        while($dataVice = mysqli_fetch_array($queryVice)){

                                    if ($_POST["vicepresidencia_fill"] ==  $dataVice["id"]) {
										echo '<option value="'.$dataVice["id"].'" selected >'.$dataVice["nombre"].'</option>';
									} else {
										echo '<option value="'.$dataVice["id"].'" >'.$dataVice["nombre"].'</option>';
									}
                                }
								?>
					</select>
                </div>

                <div class="col-md-3 mb-3">
                    <select class="form-control form-control-sm" name="area_fill">
								<option value="">Área...</option>
								<?php
                                $queryArea = mysqli_query( $connect_admin, "SELECT id, nombre FROM Areas WHERE estado = 1 AND id_empresa = '".$_SESSION["id_empresa"]."' ORDER BY nombre ASC");
		                        while($dataArea = mysqli_fetch_array($queryArea)){

                                    if ($_POST["area_fill"] ==  $dataArea["id"]) {
										echo '<option value="'.$dataArea["id"].'" selected >'.$dataArea["nombre"].'</option>';
									} else {
										echo '<option value="'.$dataArea["id"].'" >'.$dataArea["nombre"].'</option>';
									}
                                }
								?>
					</select>
                </div>
                <div class="col-md-3 mb-3">
                    <select class="form-control form-control-sm" name="unidad_fill">
								<option value="">Unidad Organizativa...</option>
								<?php
                                $queryArea = mysqli_query( $connect_admin, "SELECT id, unidad_organizativa FROM Estructura_Empresa WHERE estado = 1 AND id_empresa = '".$_SESSION["id_empresa"]."' AND unidad_organizativa != '' ORDER BY unidad_organizativa ASC");
		                        while($dataArea = mysqli_fetch_array($queryArea)){

                                    if ($_POST["unidad_fill"] ==  $dataArea["id"]) {
										echo '<option value="'.$dataArea["id"].'" selected >'.$dataArea["unidad_organizativa"].'</option>';
									} else {
										echo '<option value="'.$dataArea["id"].'" >'.$dataArea["unidad_organizativa"].'</option>';
									}
                                }
								?>
					</select>
                </div>
                <div class="col-md-3 mb-3">
                    <select class="form-control form-control-sm" name="nivel_fill">
								<option value="">Nivel Jerárquico...</option>
								<?php
                                $queryArea = mysqli_query( $connect_admin, "SELECT id, nombre FROM Nivel_Jerarquico WHERE estado = 1 AND id_empresa = '".$_SESSION["id_empresa"]."' ORDER BY nombre ASC");
		                        while($dataArea = mysqli_fetch_array($queryArea)){

                                    if ($_POST["nivel_fill"] ==  $dataArea["id"]) {
										echo '<option value="'.$dataArea["id"].'" selected >'.$dataArea["nombre"].'</option>';
									} else {
										echo '<option value="'.$dataArea["id"].'" >'.$dataArea["nombre"].'</option>';
									}
                                }
								?>
							</select>
                </div>
                <div class="col-md-3 mb-3">
                    <select class="form-control form-control-sm" name="cargo_fill">
								<option value="">Cargo...</option>
								<?php
                                $queryCargos = mysqli_query( $connect_admin, "SELECT id, nombre FROM Cargos WHERE estado = 1 AND id_empresa = '".$_SESSION["id_empresa"]."' ORDER BY nombre ASC");
		                        while($dataCargo = mysqli_fetch_array($queryCargos)){

                                    if ($_POST["cargo_fill"] ==  $dataCargo["id"]) {
										echo '<option value="'.$dataCargo["id"].'" selected >'.$dataCargo["nombre"].'</option>';
									} else {
										echo '<option value="'.$dataCargo["id"].'" >'.$dataCargo["nombre"].'</option>';
									}
                                }
								?>
					</select>
                </div>
                <div class="col-md-3 mb-3">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="?pg=desempenio/sincronizar" >
                        <button type="button" class="btn btn-danger">Sincronizar</button>
                    </a>
                </div>
                
            </div>
        </div>
    </div>
    </form>

    


    <div class="card">

        <div class="card-header">

            <table class="w-100">
                <tr>
                    <td><h3><i class="fas fa-tasks" id="iconCabecera"></i> Consolidado Desempeño  </h3></td>
                    <td class="text-end">
                        <button type="button" class="btn btn-success btn-sm" onclick="ExportarExcel()">
                            <i class="bx bx-download"></i> Excel
                        </button>               
                    </td>
                </tr>
            </table>
            


            
        </div>

        <div class="card-body">

            <div class="table-responsive">
            <table class="table" id="formularios">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Documento</th>
                    <th>Nombre</th>
                    <th>Vicepresidencia</th>
                    <th>Área</th>
                    <th>Unidad Organizativa</th>
                    <th>Nivel Jerárquico</th>
                    <th>Cargo</th>
                    <th>Estado</th>
                    <th>Resultados OKR</th>
                    <th>Resultados KPI</th>
                    <th>Competencias Resultados</th>
                    <th>Número Único Desempeño</th>
                </tr>
                </thead>
				<tbody>

                <?php
                    $count = 1;

                    $sentencia = " SELECT * FROM Datos_Sincronizados WHERE id_empresa = '".$user_log["id_empresa"]."' ".$filtros." ";

                    $query = mysqli_query( $connect_admin, $sentencia );
		            while($data = mysqli_fetch_array($query)){

                        $colaborador = $array_colaboradores[$data["id_empleado"]];

                        $txt_estado = 'Inactivo';
                        if($colaborador["estado"] == 1){
                            $txt_estado = 'Activo';
                        }
                        
                        echo '
                        <tr>
                            <td>'.$count.'</td>
                            <td>'.$colaborador["documento"].'</td>
                            <td>'.$colaborador["nombre"].' '.$colaborador["nombre_2"].' '.$colaborador["apellidos"].' '.$colaborador["apellidos_2"].'</td>
                            <td>'.$colaborador["nombre_vicepresidencia"].'</td>
                            <td>'.$colaborador["nombre_area"].'</td>
                            <td>'.$colaborador["nombre_unidad"].'</td>
                            <td>'.$colaborador["nombre_nivel_jerarquico"].'</td>
                            <td>'.$colaborador["nombre_cargo"].' '.$data["id_cargo"].'</td>
                            <td>'.$txt_estado.' </td>
                            <td>'.$data["okrs"].'% - '.$data["okrs_ponderado"].'% | '.$data["okrs_porcentaje"].'</td>
                            <td>'.$data["kips"].'% - '.$data["kips_ponderado"].'% | '.$data["kips_porcentaje"].'</td>
                            <td>'.$data["competencias"].'% '.$data["competencias_ponderado"].'% | '.$data["competencias_porcentaje"].'</td>
                            <td><b>'.round($data["numero_unico_ponderado"],2).'%<b></td>
                        </tr>
                        ';
                        $count++;
                    }
                ?>
                </tbody>
            </table>
            </div>

        </div>

    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        $('#formularios').DataTable();
    });

    /*
    $(document).ready(function() {
        $('#formularios').DataTable(
            {
                paging: true,
                lengthMenu: [ [10, 25, 50, 250], [10, 25, 50, 250] ],
                pageLength: 10,
                dom: '<"top"Bf>rt<"bottom"lip><"clear">',
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
                }]


            }
        );
    });
    */


    function ExportarExcel() {

        var table = $('#formularios').DataTable();

        // Mostrar todos los registros
        table.page.len(-1).draw();

        setTimeout(function(){

            var tabla = document.getElementById("formularios").outerHTML;

            var archivo = new Blob(
                ['\ufeff' + tabla],
                { type: 'application/vnd.ms-excel' }
            );

            var url = URL.createObjectURL(archivo);

            var link = document.createElement("a");
            link.href = url;
            link.download = "Consolidado_Desempenio.xls";

            document.body.appendChild(link);

            link.click();

            document.body.removeChild(link);

            // Volver a 50 registros
            table.page.len(50).draw();

        }, 500);
    }

</script>