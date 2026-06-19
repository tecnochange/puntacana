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

if($kpis["avance_plano_kpis"]){
    $kpis["avance_plano_kpis"] = round($kpis["avance_plano_kpis"], 2);
}

/**************** ACTIVAR LA ETIQUETA DE ACCIÓN CORRECTIVA - SOLO PARA TABLA MENSUAL ****************/
//VALIDAMOS EL ESTADO DE $kpis["avance_X"]
$accion_correctiva = NULL;
if ((!$kpis["avance_7"] || empty($kpis["avance_7"])) && $kpis["avance_7"] !== "0") {
    $accion_correctiva++;
}
if ((!$kpis["avance_10"] || empty($kpis["avance_10"])) && $kpis["avance_10"] !== "0") {
    $accion_correctiva++;
}
if ((!$kpis["avance_1"] || empty($kpis["avance_1"])) && $kpis["avance_1"] !== "0") {
    $accion_correctiva++;
}
if ((!$kpis["avance_4"] || empty($kpis["avance_4"])) && $kpis["avance_4"] !== "0") {
    $accion_correctiva++;
}

/**************** OBTENER EL SEGUIMIENTO DEL ULTIMO MES ****************/
$total_seguimiento = 0;
$ultimo_progreso = 0;
if($kpis["avance_4"] || $kpis["avance_4"] == "0"){
    $total_seguimiento = $kpis["avance_4"];
}else if($kpis["avance_1"] || $kpis["avance_1"] == "0"){
    $total_seguimiento = $kpis["avance_1"];
}else if($kpis["avance_10"] || $kpis["avance_10"] == "0"){
    $total_seguimiento = $kpis["avance_10"];
}else if($kpis["avance_7"] || $kpis["avance_7"] == "0"){
    $total_seguimiento = $kpis["avance_7"];
}

if ($total_seguimiento > 0 && $kpis["meta"] > 0) {
    $total_progreso = ($total_seguimiento / $kpis["meta"]) * 100;
    $total_progreso_bg = EscalaColor($total_progreso);
} else {
    if($total_seguimiento > $kpis["meta"]){
        $total_progreso = 100;
        $total_progreso_bg = EscalaColor($total_progreso);
    }else{
        $total_progreso = 0;
    }
}


//PARA VALIDAR SI EXISTE UN SEGUIMIENTO ESE MES
$julio_lectura = '';
$octubre_lectura = '';
$enero_lectura = '';
$abril_lectura = '';
if(!$kpis["julio"]){ $julio_lectura = ' readonly '; }
if(!$kpis["octubre"]){ $octubre_lectura = ' readonly '; }
if(!$kpis["enero"]){ $enero_lectura = ' readonly '; }
if(!$kpis["abril"]){ $abril_lectura = ' readonly '; }

echo "Avance Plano: ".$kpis["avance_plano_kpis"];
?>

<table class="table table-bordered">
    <tr>
        <td>MES</td>
        <td>Julio - Septiembre
            <?php if(!empty($kpis["avance_7"]) || $kpis["avance_7"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Julio - Septiembre"); ?></div>
            <?php endif; ?>
        </td>
        <td>Octubre - Diciembre
            <?php if(!empty($kpis["avance_10"]) || $kpis["avance_10"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Octubre - Diciembre"); ?></div>
            <?php endif; ?>
        </td>
        <td>Enero - Marzo
            <?php if(!empty($kpis["avance_1"]) || $kpis["avance_1"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Enero - Marzo"); ?></div>
            <?php endif; ?>
        </td>
        <td>Abril - Junio
            <?php if(!empty($kpis["avance_4"]) || $kpis["avance_4"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Abril - Junio"); ?></div>
            <?php endif; ?>
        </td>

        <td>TOTAL</td>
    </tr>
    <tr>
        <td>META</td>

        <td><?= $kpis["julio"]; ?> </td>
        <td><?= $kpis["octubre"]; ?></td>
        <td><?= $kpis["enero"]; ?></td>
        <td><?= $kpis["abril"]; ?></td>
        <td><?= $kpis["meta"]; ?></td>

    </tr>
    <tr>
        <td>SEGUIMIENTO</td>

        <td> 
            <input type="text" class="form-control" value="<?= $kpis["avance_7"]; ?>" name="avance_7" onkeyup="return NumerosDecimales(this)" <?= $julio_lectura; ?> > 
        </td>
        <td> 
            <input type="text" class="form-control" value="<?= $kpis["avance_10"]; ?>" name="avance_10" onkeyup="return NumerosDecimales(this)" <?= $octubre_lectura; ?> > 
        </td>
        <td> 
            <input type="text" class="form-control" value="<?= $kpis["avance_1"]; ?>" name="avance_1" onkeyup="return NumerosDecimales(this)" <?= $enero_lectura; ?> > 
        </td>
        <td> 
            <input type="text" class="form-control" value="<?= $kpis["avance_4"]; ?>" name="avance_4" onkeyup="return NumerosDecimales(this)" <?= $abril_lectura; ?> > 
        </td>

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
            <?= $octubre["html"]; ?>
            <b><?= $octubre["porcentaje"]; ?>%</b>
            <?php if( $kpis["avance_10"] != "" && (int)$octubre["porcentaje"] < 100 ): ?>
                <div>
                    <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                        Acción Correctiva
                    </span>
                </div>
            <?php endif; ?>
        </td>
        <td>
            <?= $enero["html"]; ?>
            <b><?= $enero["porcentaje"]; ?>%</b>
            <?php if( $kpis["avance_1"] != "" && (int)$enero["porcentaje"] < 100 ): ?>
                <div>
                    <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                        Acción Correctiva
                    </span>
                </div>
            <?php endif; ?>
        </td>
        <td>
            <?= $abril["html"]; ?>
            <b><?= $abril["porcentaje"]; ?>%</b>
            <?php if( $kpis["avance_4"] != "" && (int)$abril["porcentaje"] < 100 ): ?>
                <div>
                    <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                        Acción Correctiva
                    </span>
                </div>
            <?php endif; ?>
        </td>

        <td>
            <div class="progress">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $kpis["avance_kpis"]; ?>%; background-color: <?= $kpis["color_avance_kpis"]; ?> !important;" aria-valuenow="<?= $total_progreso ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <b><?= $kpis["avance_kpis"]; ?>%</b>
        </td>
    </tr>
</table>