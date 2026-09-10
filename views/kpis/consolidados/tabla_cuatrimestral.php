<?php
$enero = $ClassKpisServicios->avance_kpi_mes($kpis["enero"], $kpis["avance_1"], $kpis);
$febrero = $ClassKpisServicios->avance_kpi_mes($kpis["febrero"], $kpis["avance_2"], $kpis);
$marzo = $ClassKpisServicios->avance_kpi_mes($kpis["marzo"], $kpis["avance_3"], $kpis);
$abril = $ClassKpisServicios->avance_kpi_mes($kpis["abril"], $kpis["avance_4"], $kpis);
$mayo = $ClassKpisServicios->avance_kpi_mes($kpis["mayo"], $kpis["avance_5"], $kpis);
$junio = $ClassKpisServicios->avance_kpi_mes($kpis["junio"], $kpis["avance_6"], $kpis);
$julio = $ClassKpisServicios->avance_kpi_mes($kpis["julio"], $kpis["avance_7"], $kpis);
$agosto = $ClassKpisServicios->avance_kpi_mes($kpis["agosto"], $kpis["avance_8"], $kpis);
$septiembre = $ClassKpisServicios->avance_kpi_mes($kpis["septiembre"], $kpis["avance_9"], $kpis);
$octubre = $ClassKpisServicios->avance_kpi_mes($kpis["octubre"], $kpis["avance_10"], $kpis);
$noviembre = $ClassKpisServicios->avance_kpi_mes($kpis["noviembre"], $kpis["avance_11"], $kpis);
$diciembre = $ClassKpisServicios->avance_kpi_mes($kpis["diciembre"], $kpis["avance_12"], $kpis);

/**************** ACTIVAR LA ETIQUETA DE ACCIÓN CORRECTIVA - SOLO PARA TABLA MENSUAL ****************/
//VALIDAMOS EL ESTADO DE $kpis["avance_X"]
$accion_correctiva = NULL;
if (!$kpis["avance_7"] || empty($kpis["avance_7"]) || trim($kpis["avance_7"]) == "") {
    $accion_correctiva++;
}
if (!$kpis["avance_11"] || empty($kpis["avance_11"]) || trim($kpis["avance_11"]) == "") {
    $accion_correctiva++;
}
if (!$kpis["avance_3"] || empty($kpis["avance_3"]) || trim($kpis["avance_3"]) == "") {
    $accion_correctiva++;
}
$read_only_kpis = "";


//MESES HABILITADOS
if( $meses_habilitados["julio"] == "" ){ $julio_lectura = ' readonly '; }

if( $meses_habilitados["noviembre"] == ""){ $noviembre_lectura = ' readonly '; }

if( $meses_habilitados["marzo"] == "" ){ $marzo_lectura = ' readonly '; }

?>

<table class="table table-bordered">
    <tr>
        <td>MES</td>
        <td>Julio - Octubre
            <?php if(!empty($kpis["avance_7"]) || $kpis["avance_7"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Julio - Octubre"); ?></div>
            <?php endif; ?>
        </td>
        <td>Noviembre - Febrero
            <?php if(!empty($kpis["avance_11"]) || $kpis["avance_11"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Noviembre - Febrero"); ?></div>
            <?php endif; ?>
        </td>
        <td>Marzo - Junio
            <?php if(!empty($kpis["avance_3"]) || $kpis["avance_3"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Marzo - Junio"); ?></div>
            <?php endif; ?>
        </td>

        <td>TOTAL</td>
    </tr>
    <tr>
        <td>META</td>

        <td><?= $kpis["julio"]; ?></td>
        <td><?= $kpis["noviembre"]; ?></td>
        <td><?= $kpis["marzo"]; ?></td>

        <td><?= $kpis["meta"]; ?></td>

    </tr>
    <?php if($_SESSION["anio_fill"] >= 2027){ ?>
    <tr>
        <td>MÍNIMO ESPERADO</td>
        <td><?= $kpis["julio_min"]; ?></td>
        <td><?= $kpis["noviembre_min"]; ?></td>
        <td><?= $kpis["marzo_min"]; ?></td>

        <td></td>
    </tr>
    <?php } ?>
    <tr>
        <td>SEGUIMIENTO</td>

        <td> <input type="text" class="form-control" value="<?= $kpis["avance_7"]; ?>" name="avance_7" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?>  <?= $julio_lectura; ?> > </td>
        <td> <input type="text" class="form-control" value="<?= $kpis["avance_11"]; ?>" name="avance_11" onkeyup="return NumerosDecimales(this)" <?= $noviembre_lectura; ?>  <?= $julio_lectura; ?>  > </td>
        <td> <input type="text" class="form-control" value="<?= $kpis["avance_3"]; ?>" name="avance_3" onkeyup="return NumerosDecimales(this)" <?= $marzo_lectura; ?>  <?= $julio_lectura; ?> > </td>

        <td> 
            <?php if($kpis["avance_plano_kpis"] != 0){ ?>
                <?= $seguimiento_formato; ?>  
            <?php } ?>
        </td>
    </tr>
    <tr>
        <td>PROGRESO</td> 

        <td>
            <?= $julio["html"]; ?> 
            <b><?= $julio["porcentaje"]; ?>%</b>
            <?php if( $kpis["avance_7"] != "" && (int)$julio["porcentaje"] < 100 ): ?>
                <div>
                    <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                        Acción Correctiva
                    </span>
                </div>
            <?php endif; ?>
        </td>
        <td>
            <?= $noviembre["html"]; ?> 
            <b><?= $noviembre["porcentaje"]; ?>%</b> 
            <?php if( $kpis["avance_11"] != "" && (int)$noviembre["porcentaje"] < 100 ): ?>
                <div>
                    <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                        Acción Correctiva
                    </span>
                </div>
            <?php endif; ?>
        </td>
        <td>
            <?= $marzo["html"]; ?> 
            <b><?= $marzo["porcentaje"]; ?>%</b>
            <?php if( $kpis["avance_3"] != "" && (int)$marzo["porcentaje"] < 100 ): ?>
                <div>
                    <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                        Acción Correctiva
                    </span>
                </div>
            <?php endif; ?>
        </td>

        <td>
            <div class="progress">
				    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $kpis["avance_kpis"] ?>%; background-color: <?= $kpis["color_avance_kpis"]; ?> !important;" aria-valuenow="<?= $kpis["avance_kpis"] ?>" aria-valuemin="0" aria-valuemax="100"></div>
			    </div>
                <b><?= $kpis["avance_kpis"]; ?>%</b>
        </td>

        
    </tr>
</table>