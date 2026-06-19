<script>
	$(document).ready(function() {
		$(".menu_section").addClass("active");
		jQuery("#menu_desempenio").css("display", "none");
		$("#bt_desempenio_individual_cierres").addClass("current-page");
	});
</script>

<?php
$permitir = true;
$hoy = date("Y-m-d H:i:s");

include("app/models/kpis/Kpis.php");
$ClassKpis = new Kpis();

include("app/models/competencias/Competencias.php");
$ClassCompetencias = new Competencias();


include("app/models/okrs/OkrsServicios.php");
$ClassOkrsServicios = new OkrsServicios();

include("app/models/desempenio/Desempenio.php");
$ClassDesempenio = new Desempenio();



$ARRAY_COLABORADORES = [];
$sentencia_col = "
    SELECT
        Empleados.id AS id,
        Empleados.nombre AS nombre,
        Empleados.documento AS documento, 
        Empleados.estado AS estado,
        Cargos.id AS id_cargo,
        Cargos.nombre AS nombre_cargo,
        Areas.nombre AS nombre_area,
        Areas.id AS id_area, 
        Vicepresidencia.nombre AS nombre_vicepresidencia, 
        Vicepresidencia.id AS id_vicepresidencia,
        Nivel_Jerarquico.nombre AS nombre_nivel_jerarquico, 
        Nivel_Jerarquico.id AS id_nivel_jerarquico, 
        Estructura_Empresa.unidad_organizativa AS nombre_unidad, 
        Estructura_Empresa.id AS id_unidad  
    FROM
        Empleados
    LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
    LEFT JOIN Areas ON Areas.id = Empleados.area
    LEFT JOIN Vicepresidencia ON Vicepresidencia.id = Empleados.unidad_corporativa
    LEFT JOIN Nivel_Jerarquico ON Nivel_Jerarquico.id = Empleados.nivel_jerarquico 
    LEFT JOIN Estructura_Empresa ON Estructura_Empresa.id = Empleados.unidad_organizativa 
    WHERE
        Empleados.id > 0 AND Empleados.id_empresa = '".$_SESSION["id_empresa"]."'  
    ORDER BY
        Empleados.nombre ASC 
        LIMIT 30
";
$queryColaborador = mysqli_query($connect_admin, $sentencia_col);
while ($dataColaborador = mysqli_fetch_array($queryColaborador)){
    $ARRAY_COLABORADORES[$dataColaborador["id"]] = $dataColaborador;
}

















//include("app/models/Okrs/OkrModelGlobal_V2.php");
//$ClassOkrModelGlobal = new OkrModelGlobal();

if( $_POST["periodo_desempenio_fill"] ){
    $_SESSION["periodo_desempenio_fill"] = $_POST["periodo_desempenio_fill"];
}
/*
//CONSULTA DE COLABORADORES
$ARRAY_COLABORADORES = [];
$sentencia_col = "
    SELECT
        Empleados.id AS id,
        Empleados.nombre AS nombre,
        Empleados.documento AS documento, 
        Empleados.estado AS estado,
        Cargos.id AS id_cargo,
        Cargos.nombre AS nombre_cargo,
        Areas.nombre AS nombre_area,
        Areas.id AS id_area, 
        Vicepresidencia.nombre AS nombre_vicepresidencia, 
        Vicepresidencia.id AS id_vicepresidencia,
        Nivel_Jerarquico.nombre AS nombre_nivel_jerarquico, 
        Nivel_Jerarquico.id AS id_nivel_jerarquico, 
        Estructura_Empresa.unidad_organizativa AS nombre_unidad, 
        Estructura_Empresa.id AS id_unidad  
    FROM
        Empleados
    LEFT JOIN Cargos ON Cargos.id = Empleados.id_cargo
    LEFT JOIN Areas ON Areas.id = Empleados.area
    LEFT JOIN Vicepresidencia ON Vicepresidencia.id = Empleados.unidad_corporativa
    LEFT JOIN Nivel_Jerarquico ON Nivel_Jerarquico.id = Empleados.nivel_jerarquico 
    LEFT JOIN Estructura_Empresa ON Estructura_Empresa.id = Empleados.unidad_organizativa 
    WHERE
        Empleados.id > 0 AND Empleados.id_empresa = '".$_SESSION["id_empresa"]."' 
    ORDER BY
        Empleados.nombre ASC
";
if($_POST["filtrar_sincronizar"]){

    $queryColaborador = mysqli_query($connect_valentina, $sentencia_col);
    while ($dataColaborador = mysqli_fetch_array($queryColaborador)){
        $ARRAY_COLABORADORES[$dataColaborador["id"]] = $dataColaborador;
    }

}

*/
?>
<div class="row">
	<div class="col-md-12">
		<div class="card">
			<div class="card-header">
				<div class="row">
					<div class="col-md-12">
						<h3><i class="fas fa-tasks" id="iconCabecera"></i> Sincronizar número único Desempeño </h3>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<br>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<form action="" method="POST">
                <input type="hidden" name="filtrar_sincronizar" value="true">
				<div class="form-group">
					<div class="row">
						<div class="col-md-3">
							<select class="form-control form-control-sm" name="periodo_desempenio_fill">
								<option value="-1">Filtrar por Periodo...</option>
								<?php
								foreach ($Array_Anio_Desempenio as $periodo) {
                                    if($periodo[0] == 2026){

                                    
                                        if ($_SESSION["periodo_desempenio_fill"] ==  $periodo[0]) {
                                            echo '<option value="' . $periodo[0] . '" selected>' . ((int)$periodo[1] - 1) . ' - ' . $periodo[1] . '</option>';
                                        } else {
                                            echo '<option value="' . $periodo[0] . '">' . ((int)$periodo[1] - 1) . ' - ' . $periodo[1] . '</option>';
                                        }
                                    }
								}
								?>
							</select>
						</div>
						<div class="col-md-2" style="text-align: right;">
							<button type="submit" class="btn btn-primary">Sincronizar</button>
						</div>
					</div>
				</div>
			</form>
		</div>


	</div>
	<br>
    <?php if( $_POST["periodo_desempenio_fill"] ){ ?>
	<div class="row">

		<div class="card">
			<div class="card-body">

				<div class="table-responsive">
				<table border="1" id="formularios" class="display table" style="width:100%">
					<thead style="font-weight: 700;font-size: 16px;text-align:center;">
                        <tr>
                            <th>No</th>
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
                    include("views/competencias/informes/funciones.php");
                    //if($_POST["guardar_formulario"]){
                        $count = 1;
                        foreach($ARRAY_COLABORADORES as $colaborador){

                            //KPIS
                            $kpis = $ClassKpis->ResultadoKpis($colaborador["id"], $user_log["id_empresa"], $_SESSION["periodo_desempenio_fill"]);
                            
                            //OKRS
                            $okrs_objetivos_asociados = $ClassOkrsServicios->objetivos_asociados($user_log["id_empresa"], $colaborador["id"], $colaborador["id_area"], $_SESSION["anio_fill"]);
                            $datos_consolidado = $ClassOkrsServicios->datos_consolidado_okrs($user_log["id_empresa"], $okrs_objetivos_asociados);
                            $okrs = $datos_consolidado["promedio_general"];

                            //COMPETENCIAS
                            $VALIDACION = PromedioGeneralEvaluado($colaborador["id"], $connect_valoracion, $connect_admin);
                            $promedio = $VALIDACION["promedio"];
                            $competencias = 0;
                            if($promedio > 0){
                                $competencias = $promedio*100/5;
                                $competencias = round($competencias,1);
                            }

                            //DESEMPEÑO
                            $numero_desempenio = $ClassDesempenio->ResultadoDesempenio($okrs, $kpis, $competencias, $colaborador["id_nivel_jerarquico"]);


                            //OBTENEMOS LAS PONDERACIONES
                            $queryPonderaciones = mysqli_query( $connect_admin , "SELECT * FROM Ponderar_Desempenio WHERE id_empresa = '".$user_log["id_empresa"]."' AND anio = '".$_SESSION["periodo_desempenio_fill"]."' AND nivel = '".$colaborador["id_nivel_jerarquico"]."' " );
                            $dataPonderaciones = mysqli_fetch_array($queryPonderaciones);

                            
                            $okrs_ponderado = 0;
                            if($okrs > 0){ 
                                $okrs_ponderado = $okrs*($dataPonderaciones["mod_okrs"]/100); 
                            }

                            $competencias_ponderado = 0;
                            if($competencias > 0){ 
                                $competencias_ponderado = $competencias*($dataPonderaciones["mod_competencias"]/100); 
                            }

                            $kpis_ponderado = 0;
                            if( $kpis > 0 ){ 
                                $kpis_ponderado = $kpis*($dataPonderaciones["mod_kpis"]/100); 
                            }

                            //$total_ponderado = $okrs_ponderado+$competencias_ponderado+$kpis_ponderado;
                            $total = ($okrs+$competencias+$kpis);
                            if($total != 0){
                                $total = round(($total/3),1);
                            }





                            $queryVal = mysqli_query( $connect_admin , "SELECT * FROM Datos_Sincronizados WHERE id_empleado = '".$colaborador["id"]."' AND anio = '".$_SESSION["periodo_desempenio_fill"]."' " );
                            $dataVal = mysqli_fetch_array($queryVal);

                            if($permitir == true){

                                if($queryVal->num_rows == 0 ){

                                    $sentencia_sincro = "
                                    INSERT INTO Datos_Sincronizados(
                                        id_empresa,
                                        anio,
                                        id_empleado,
                                        id_vicepresidencia,
                                        id_area,
                                        id_unidad,
                                        id_nivel,
                                        id_cargo,

                                        okrs, 
                                        okrs_ponderado, 
                                        okrs_porcentaje,
                                        kips, 
                                        kips_ponderado, 
                                        kips_porcentaje, 
                                        competencias, 
                                        competencias_ponderado, 
                                        competencias_porcentaje,
                                        numero_unico, 
                                        numero_unico_ponderado, 

                                        created_at,
                                        updated_at
                                    )
                                    VALUES(
                                        '".$user_log["id_empresa"]."',
                                        '".$_SESSION["periodo_desempenio_fill"]."',
                                        '".$colaborador["id"]."',
                                        '".$colaborador["id_vicepresidencia"]."',
                                        '".$colaborador["id_area"]."',
                                        '".$colaborador["id_unidad"]."',
                                        '".$colaborador["id_nivel_jerarquico"]."',
                                        '".$colaborador["id_cargo"]."',
                                        '".$okrs."', 
                                        '".$okrs_ponderado."', 
                                        '".$dataPonderaciones["mod_okrs"]."', 
                                        '".$kpis."', 
                                        '".$kpis_ponderado."', 
                                        '".$dataPonderaciones["mod_kpis"]."',
                                        '".$competencias."', 
                                        '".$competencias_ponderado."', 
                                        '".$dataPonderaciones["mod_competencias"]."',
                                        '".$total."', 
                                        '".$numero_desempenio."', 
                                        '".$hoy."', 
                                        '".$hoy."'
                                    )
                                    ";
                                    //mysqli_query( $connect_valentina , $sentencia_sincro);
                                }

                                if($queryVal->num_rows > 0 ){ 
                                    $sentencia_upd = "
                                    UPDATE Datos_Sincronizados SET 
                                        okrs = '".$okrs."', 
                                        okrs_ponderado = '".$okrs_ponderado."', 
                                        okrs_porcentaje = '".$dataPonderaciones["mod_okrs"]."',
                                        kips = '".$kpis."',
                                        kips_ponderado = '".$kpis_ponderado."', 
                                        kips_porcentaje = '".$dataPonderaciones["mod_kpis"]."',
                                        competencias = '".$competencias."', 
                                        competencias_ponderado = '".$competencias_ponderado."', 
                                        competencias_porcentaje = '".$dataPonderaciones["mod_competencias"]."',
                                        numero_unico = '".$total."', 
                                        numero_unico_ponderado = '".$numero_desempenio."', 
                                        updated_at =  '".$hoy."'
                                    WHERE id = '".$dataVal["id"]."'
                                    ";
                                    echo $sentencia_upd;
                                    //mysqli_query( $connect_admin , $sentencia_upd);
                                }
                            }

                            
                                
                            echo '
                                <tr>
                                    <td>'.$count.'</td>
                                    <td>'.$colaborador["documento"].'</td>
                                    <td>'.$colaborador["nombre"].'</td>
                                    <td>'.$colaborador["nombre_vicepresidencia"].'</td>
                                    <td>'.$colaborador["nombre_area"].'</td>
                                    <td>'.$colaborador["nombre_unidad"].'</td>
                                    <td>'.$colaborador["nombre_nivel_jerarquico"].'</td>
                                    <td>'.$colaborador["nombre_cargo"].'</td>
                                    <td>'.$okrs.'% - '.$okrs_ponderado.'% - '.$dataPonderaciones["mod_okrs"].'</td>
                                    <td>'.$kpis.'% - '.$kpis_ponderado.'% - '.$dataPonderaciones["mod_kpis"].'</td>
                                    <td>'.$competencias.'% - '.$competencias_ponderado.'% - '.$dataPonderaciones["mod_competencias"].'</td>
                                    <td><b>'.$numero_desempenio.'%<b></td>
                                </tr>
                            ';


                            $count++;
                        }

                    ?>

                    
                    



                    <?php
                    if(!$_POST["filtrar_sincronizar"]){
                        $count = 1;

                        $sentencia = " SELECT * FROM Datos_Sincronizados WHERE id_empresa = '".$_SESSION["id_empresa"]."' ".$filtros."  ";

                        $query = mysqli_query( $connect_valentina, $sentencia );
                        while($data = mysqli_fetch_array($query)){ 

                            $colaborador = $ClassColaboradores->colaborador($data["id_empleado"], $connect_valentina );
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
                                <td>'.$data["okrs"].'% - '.$data["okrs_ponderado"].'% - '.$data["okrs_porcentaje"].' </td>
                                <td>'.$data["kips"].'% -'.$data["kips_ponderado"].'%</td>
                                <td>'.$data["competencias"].'% - '.$data["competencias_ponderado"].'%</td>
                                <td><b>'.$data["numero_unico_ponderado"].'%<b></td>
                            </tr>
                            ';
                            $count++;
                        }
                    }
                    ?>








					</tbody>
				</table>
				</div>

			</div>
		</div>


	</div>
    <?php } ?>

</div>


<script type="text/javascript">
    $(document).ready(function() {
        $('#formularios').DataTable();
    });

    function VerLoader(){
        $("#loader").show();
    }
</script>