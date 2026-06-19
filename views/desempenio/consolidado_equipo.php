<script>
$(document).ready(function() {
    $('#menuDesempenio').collapse();
    $('#bt_desempenio_mis_objetivos').addClass('active');
});
</script>

<?php
//OBTENEMOS LA INFORMACIÓN DE LOS COLABORADORES
include("app/models/estructura/Colaboradores.php");
$ClassColaboradores = new Colaboradores();
$array_colaboradores = $ClassColaboradores->colaboradores_lista(NULL, $connect_admin);

//DATOS DE LOS KPIS
include("app/models/kpis/Kpis.php");
$ClassKpis = new Kpis();

include("app/models/competencias/Competencias.php");
$ClassCompetencias = new Competencias();

include("app/models/okrs/Okrs.php");
$ClassOkrs = new Okrs();

$filtros = " AND anio =  '".$_SESSION["anio_fill"]."' ";

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

$queryRL = mysqli_query($connect_admin, "SELECT * FROM Relaciones_Laborales WHERE id_empleado = '" . $user_log["id_user"] . "' AND estado = 1 AND mod_competencias = 'on'");
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







$array_equipo = array();
$sentencia_equipo = "
SELECT
    Empleados.id AS id_empleado,
    Lideres.id,
    Empleados.documento,
    Empleados.nombre AS nombre,
    Cargos.nombre AS nombre_cargo,
    Areas.nombre AS nombre_area
FROM
    Lideres
INNER JOIN Empleados ON Empleados.id = Lideres.id_empleado
LEFT JOIN Posiciones ON Posiciones.id = Empleados.id_posicion
LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
LEFT JOIN Areas ON Areas.id = Empleados.area
WHERE
    Lideres.id_jefe = '".$user_log["id"]."' AND Empleados.estado = 1 AND Lideres.id_empresa =  ".$user_log["id_empresa"]."
";
//EQUIPO DE TRABAJO
$queryJefes = mysqli_query($connect_admin, $sentencia_equipo);
while ($dataJefes = mysqli_fetch_array($queryJefes)) {
    array_push( $array_equipo, $dataJefes );
}
?>


<div class="container-fluid">

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
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
                
            </div>
        </div>
    </div>
    </form>

    
    <ul class="nav nav-tabs justify-content-center">
        <li class="nav-item">
            <a class="nav-link " aria-current="page" href="?pg=desempenio/mi_desempenio">Mi Desempeño</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="?pg=desempenio/consolidado_equipo">Desempeño Equipo</a>
        </li>
    </ul>


    <div class="card">

        <div class="card-header">
            <h3><i class="fas fa-tasks" id="iconCabecera"></i> Consolidado Desempeño Equipo  </h3>
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
                    <th>Resultados OKR</th>
                    <th>Resultados KPI</th>
                    <th>Competencias Resultados</th>
                    <th>Número Único Desempeño</th>
                </tr>
                </thead>
				<tbody>

                <?php
                    $count = 1; 
                    //include("app/models/Okrs/OkrModelGlobal_V2.php");
                    //$ClassOkrModelGlobal = new OkrModelGlobal();


                    foreach($array_equipo as $colaborador){ 
                        
                        $colaborador_data = $ClassColaboradores->colaborador($colaborador["id_empleado"], $connect_admin );

                        //KPIS
                        $kpis = $ClassKpis->ResultadoKpis($colaborador["id_empleado"], $user_log["id_empresa"], $_SESSION["anio_fill"]);

                        $competencias = $ClassCompetencias->ResultadoCompetencias($colaborador["id_empleado"], $user_log["id_empresa"], $_SESSION["anio_fill"]); 

                        $okrs = $ClassOkrs->ResultadoOkrs($colaborador["id_empleado"], $user_log["id_empresa"], $_SESSION["anio_fill"]);

                        
             
                        /*
                        //DATOS UNICOS
                        //COMPETENCIAS
                        $competencias =   $ClassOkrModelGlobal->ResultadoCompetencias($colaborador["id_empleado"], $_SESSION["id_empresa"], $_SESSION["periodo_desempenio_fill"]);

                        

                        //OKRS

                        $okrs = 0; 
                                                        
                        $OkrsByEmp = $ClassOkrModelGlobal->OkrsConsolidadoCompetencia($colaborador["id_empleado"], $_SESSION["id_empresa"], $_SESSION["periodo_desempenio_fill"],'okrs');

                        $totalOkrsByEmp = count($OkrsByEmp);
                        $promedio = 0;
                        $cantidad = 0;
                        foreach ($OkrsByEmp as $value) {
                                    $resultados =  $ClassOkrModelGlobal->OkrsReporteUsuarioDesempenio($value["idOkrs"],$_SESSION["id_empresa"],'okrs'); 
                                    $cantidad++;

                                    $promedio += $resultados["promedio"];
                                    //$prueba++;
                                }

                                if( $cantidad == 0 ){
                                    $okrs = 0;
                                }
                                else{
                                    $okrs = round( ($promedio / $cantidad), 2);
                                }




                                //OBTENEMOS LAS PONDERACIONES
                                $queryPonderaciones = mysqli_query( $connect_valentina , "SELECT * FROM Ponderar_Desempenio WHERE id_empresa = '".$_SESSION["id_empresa"]."' AND anio = '".$_SESSION["periodo_desempenio_fill"]."' AND nivel = '".$colaborador_data["id_nivel_jerarquico"]."' " );
                                $dataPonderaciones = mysqli_fetch_array($queryPonderaciones);

                                $okrs_ponderado = 0;
                                if($okrs > 0){ 
                                    $okrs_ponderado = $okrs*($dataPonderaciones["mod_okrs"]/100); 
                                }

                                $competencias_ponderado = 0;
                                if($competencias > 0){ $competencias_ponderado = $competencias*($dataPonderaciones["mod_competencias"]/100); }

                                $kpis_ponderado = 0;
                                if( $kpis > 0 ){ $kpis_ponderado = $kpis*($dataPonderaciones["mod_kpis"]/100); }

                                $total_ponderado = $okrs_ponderado+$competencias_ponderado+$kpis_ponderado;

                                $data["okrs"] = $okrs;
                                $data["kips"] = $kpis;
                                $data["competencias"] = $competencias;




                        */

                        if($colaborador_data){
                            echo '
                                <tr>
                                    <td title="'.$colaborador["id_empleado"].'">'.$count.'</td>
                                    <td>'.$colaborador_data["documento"].'</td>
                                    <td>'.$colaborador_data["nombre"].' '.$colaborador_data["nombre_2"].' '.$colaborador_data["apellidos"].' '.$colaborador_data["apellidos_2"].'</td>
                                    <td>'.$colaborador_data["nombre_vicepresidencia"].'</td>
                                    <td>'.$colaborador_data["nombre_area"].'</td>
                                    <td>'.$colaborador_data["nombre_unidad"].'</td>
                                    <td>'.$colaborador_data["nombre_nivel_jerarquico"].'</td>
                                    <td>'.$colaborador_data["nombre_cargo"].'</td>
                                    <td>'.$okrs.'%</td>
                                    <td>'.$kpis.'%</td>
                                    <td>'.$competencias.'%</td>
                                    <td><b>'.$total_ponderado.'%<b></td>
                                </tr>
                            ';
                            $count++;
                        }
                            
                    }
                ?>






                <?php


                    $count = 1;
                    /*
                    foreach($array_equipo as $equipo){
                        $sentencia = " SELECT * FROM Datos_Sincronizados WHERE id_empresa = '".$user_log["id_empresa"]."' AND id_empleado = '".$equipo["id_empleado"]."' AND anio = '".$_SESSION["anio_fill"]."' ";

                        $query = mysqli_query( $connect_admin, $sentencia );
		                $data = mysqli_fetch_array($query);

                        $colaborador = $array_colaboradores[$data["id_empleado"]];

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
                            <td>'.$data["okrs"].'% - '.$data["okrs_ponderado"].'% | '.$data["okrs_porcentaje"].'</td>
                            <td>'.$data["kips"].'% - '.$data["kips_ponderado"].'% | '.$data["kips_porcentaje"].'</td>
                            <td>'.$data["competencias"].'% '.$data["competencias_ponderado"].'% | '.$data["competencias_porcentaje"].'</td>
                            <td><b>'.$data["numero_unico_ponderado"].'%<b></td>
                        </tr>
                        ';
                        $count++;

                    } 
                    */   
                    
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

</script>