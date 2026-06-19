<?php
$julio = $ClassKpisServicios->avance_kpi_mes($kpis["julio"], $kpis["avance_7"], $kpis);
$agosto = $ClassKpisServicios->avance_kpi_mes($kpis["agosto"], $kpis["avance_8"], $kpis);
$septiembre = $ClassKpisServicios->avance_kpi_mes($kpis["septiembre"], $kpis["avance_9"], $kpis);
$octubre = $ClassKpisServicios->avance_kpi_mes($kpis["octubre"], $kpis["avance_10"], $kpis);
$noviembre = $ClassKpisServicios->avance_kpi_mes($kpis["noviembre"], $kpis["avance_11"], $kpis);
$diciembre = $ClassKpisServicios->avance_kpi_mes($kpis["diciembre"], $kpis["avance_12"], $kpis);
$enero = $ClassKpisServicios->avance_kpi_mes($kpis["enero"], $kpis["avance_1"], $kpis);
$febrero = $ClassKpisServicios->avance_kpi_mes($kpis["febrero"], $kpis["avance_2"], $kpis);
$marzo = $ClassKpisServicios->avance_kpi_mes($kpis["marzo"], $kpis["avance_3"], $kpis);
$abril = $ClassKpisServicios->avance_kpi_mes($kpis["abril"], $kpis["avance_4"], $kpis);
$mayo = $ClassKpisServicios->avance_kpi_mes($kpis["mayo"], $kpis["avance_5"], $kpis);
$junio = $ClassKpisServicios->avance_kpi_mes($kpis["junio"], $kpis["avance_6"], $kpis);

if($kpis["avance_plano_kpis"]){
    $kpis["avance_plano_kpis"] = round($kpis["avance_plano_kpis"], 2);
}

$read_only_kpis = "";

/**************** ACTIVAR LA ETIQUETA DE ACCIÓN CORRECTIVA - SOLO PARA TABLA MENSUAL ****************/
//VALIDAMOS EL ESTADO DE $kpis["avance_X"]
$accion_correctiva = NULL;

for($i=1; $i<=12; $i++){
    if( (!$kpis["avance_".$i] || empty($kpis["avance_".$i])) && $kpis["avance_".$i] !== "0" ){
        $accion_correctiva++;
    }
}


/**************** OBTENER EL SEGUIMIENTO DEL ULTIMO MES ****************/
/*
$total_seguimiento = 0;
$ultimo_progreso = 0;
if($kpis["avance_6"] || $kpis["avance_6"] == "0"){
    $total_seguimiento = $kpis["avance_6"];
}else if($kpis["avance_5"] || $kpis["avance_5"] == "0"){
    $total_seguimiento = $kpis["avance_5"];
}else if($kpis["avance_4"] || $kpis["avance_4"] == "0"){
    $total_seguimiento = $kpis["avance_4"];
}else if($kpis["avance_3"] || $kpis["avance_3"] == "0"){
    $total_seguimiento = $kpis["avance_3"];
}else if($kpis["avance_2"] || $kpis["avance_2"] == "0"){
    $total_seguimiento = $kpis["avance_2"];
}else if($kpis["avance_1"] || $kpis["avance_1"] == "0"){
    $total_seguimiento = $kpis["avance_1"];
}else if($kpis["avance_12"] || $kpis["avance_12"] == "0"){
    $total_seguimiento = $kpis["avance_12"];
}else if($kpis["avance_11"] || $kpis["avance_11"] == "0"){
    $total_seguimiento = $kpis["avance_11"];
}else if($kpis["avance_10"] || $kpis["avance_10"] == "0"){
    $total_seguimiento = $kpis["avance_10"];
}else if($kpis["avance_9"] || $kpis["avance_9"] == "0"){
    $total_seguimiento = $kpis["avance_9"];
}else if($kpis["avance_8"] || $kpis["avance_8"] == "0"){
    $total_seguimiento = $kpis["avance_8"];
}else if($kpis["avance_7"] || $kpis["avance_7"] == "0"){
    $total_seguimiento = $kpis["avance_7"];
}
  */  
/*
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
*/

//PARA VALIDAR SI EXISTE UN SEGUIMIENTO ESE MES
$julio_lectura = '';
$agosto_lectura = '';
$septiembre_lectura = '';
$octubre_lectura = '';

$noviembre_lectura = '';
$diciembre_lectura = '';
$enero_lectura = '';
$febrero_lectura = '';

$marzo_lectura = '';
$abril_lectura = '';
$mayo_lectura = '';
$junio_lectura = '';


if( $kpis["julio"] == "" ){ $julio_lectura = ' readonly '; }
if( $kpis["agosto"] == ""){ $agosto_lectura = ' readonly '; }
if( $kpis["septiembre"] == ""){ $septiembre_lectura = ' readonly '; }
if( $kpis["octubre"] == ""){ $octubre_lectura = ' readonly '; }


if( $kpis["noviembre"] == ""){ $noviembre_lectura = ' readonly '; }
if( $kpis["diciembre"] == "" ){ $diciembre_lectura = ' readonly '; }
if( $kpis["enero"] == "" ){ $enero_lectura = ' readonly '; }
if( $kpis["febrero"] == "" ){ $febrero_lectura = ' readonly '; }


if( $kpis["marzo"] == "" ){ $marzo_lectura = ' readonly '; }
if( $kpis["abril"] == "" ){ $abril_lectura = ' readonly '; }
if( $kpis["mayo"] == "" ){ $mayo_lectura = ' readonly '; }
if( $kpis["junio"] == "" ){ $junio_lectura = ' readonly '; }

?>

<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <td>MES</td>
            <td>Julio
                <?php if(!empty($kpis["avance_7"]) || $kpis["avance_7"] == "0"): ?>
                    <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Julio"); ?></div>
                <?php endif; ?>
            </td>
            <td>Agosto
                <?php if(!empty($kpis["avance_8"]) || $kpis["avance_8"] == "0"): ?>
                    <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Agosto"); ?></div>
                <?php endif; ?>
            </td>
            <td>Septiembre 
                <?php if(!empty($kpis["avance_9"]) || $kpis["avance_9"] == "0"): ?>
                    <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Septiembre"); ?></div>
                <?php endif; ?>
            </td>
            <td>Octubre 
                <?php if(!empty($kpis["avance_10"]) || $kpis["avance_10"] == "0"): ?>
                    <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Octubre"); ?></div>
                <?php endif; ?>
            </td>
            <td>Noviembre 
                <?php if(!empty($kpis["avance_11"]) || $kpis["avance_11"] == "0"): ?>
                    <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Noviembre"); ?></div>
                <?php endif; ?>
            </td>
            <td>Diciembre 
                <?php if(!empty($kpis["avance_12"]) || $kpis["avance_12"] == "0"): ?>
                    <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Diciembre"); ?></div>
                <?php endif; ?>
            </td>
            <td>Enero 
                <?php if(!empty($kpis["avance_1"]) || $kpis["avance_1"] == "0"): ?>
                    <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Enero"); ?></div>
                <?php endif; ?>
            </td>
            <td>Febrero 
                <?php if(!empty($kpis["avance_2"]) || $kpis["avance_2"] == "0"): ?>
                    <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Febrero"); ?></div>
                <?php endif; ?>
            </td>
            <td>Marzo 
                <?php if(!empty($kpis["avance_3"]) || $kpis["avance_3"] == "0"): ?>
                    <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Marzo"); ?></div>
                <?php endif; ?>
            </td>
            <td>Abril 
                <?php if(!empty($kpis["avance_4"]) || $kpis["avance_4"] == "0"): ?>
                    <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Abril"); ?></div>
                <?php endif; ?>
            </td>
            <td>Mayo 
                <?php if(!empty($kpis["avance_5"]) || $kpis["avance_5"] == "0"): ?>
                    <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Mayo"); ?></div>
                <?php endif; ?>
            </td>
            <td>Junio 
                <?php if(!empty($kpis["avance_6"]) || $kpis["avance_6"] == "0"): ?>
                    <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Junio"); ?></div>
                <?php endif; ?>
            </td>

            <td>TOTAL</td>
        </tr>
        <tr>
            <td>META</td>

            <td><?= $kpis["julio"]; ?></td>
            <td><?= $kpis["agosto"]; ?></td>
            <td><?= $kpis["septiembre"]; ?></td>
            <td><?= $kpis["octubre"]; ?></td>
            <td><?= $kpis["noviembre"]; ?></td>
            <td><?= $kpis["diciembre"]; ?></td>

            <td><?= $kpis["enero"]; ?></td>
            <td><?= $kpis["febrero"]; ?></td>
            <td><?= $kpis["marzo"]; ?></td>
            <td><?= $kpis["abril"]; ?></td>
            <td><?= $kpis["mayo"]; ?></td>
            <td><?= $kpis["junio"]; ?></td>

            <td><?= $meta_formato; ?></td>
        </tr>

        <tr>
            <td>SEGUIMIENTO</td>        
            <td> 
                <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0" value="<?= $kpis["avance_7"]; ?>" name="avance_7" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> <?= $julio_lectura; ?> > 
            </td>
            <td> 
                <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0" value="<?= $kpis["avance_8"]; ?>" name="avance_8" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> <?= $agosto_lectura; ?> > 
            </td>
            <td> 
                <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0" value="<?= $kpis["avance_9"]; ?>" name="avance_9" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> <?= $septiembre_lectura; ?> > 
            </td>
            <td> 
                <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0" value="<?= $kpis["avance_10"]; ?>" name="avance_10" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> <?= $octubre_lectura; ?> > 
            </td>
            <td> 
                <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0" value="<?= $kpis["avance_11"]; ?>" name="avance_11" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> <?= $noviembre_lectura; ?> > 
            </td>
            <td> 
                <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0" value="<?= $kpis["avance_12"]; ?>" name="avance_12" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> <?= $diciembre_lectura; ?> > 
            </td>

            <td> 
                <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0" value="<?= $kpis["avance_1"]; ?>" name="avance_1" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> <?= $enero_lectura; ?> > 
            </td>
            <td> 
                <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0" value="<?= $kpis["avance_2"]; ?>" name="avance_2" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> <?= $febrero_lectura; ?> > 
            </td>
            <td> 
                <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0" value="<?= $kpis["avance_3"]; ?>" name="avance_3" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> <?= $marzo_lectura; ?> > 
            </td>
            <td> 
                <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0" value="<?= $kpis["avance_4"]; ?>" name="avance_4" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> <?= $abril_lectura; ?> > 
            </td>
            <td> 
                <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0" value="<?= $kpis["avance_5"]; ?>" name="avance_5" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> <?= $mayo_lectura; ?> > 
            </td>
            <td> 
                <input type="text" class="form-control form-control-sm border-0 border-bottom rounded-0" value="<?= $kpis["avance_6"]; ?>" name="avance_6" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> <?= $junio_lectura; ?> > 
            </td>

            <td> 
                <?php //if($kpis["avance_plano_kpis"] != 0){ ?>
                    <?= $seguimiento_formato; ?> 
                <?php // } ?>
            </td>
        </tr>
        <tr>
            <td>PROGRESO</td>

            <td>
                <?= $julio["html"]; ?>
                <b><?= $julio["porcentaje"]; ?>% </b>
                <?php if( $kpis["avance_7"] != "" && (int)$julio["porcentaje"] < 100 ): ?>
                    <div>
                        <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                            Acción Correctiva
                        </span>
                    </div>
                <?php endif; ?>
            </td>
            <td>
                <?= $agosto["html"]; ?>
                <b><?= $agosto["porcentaje"]; ?>% </b>
                <?php if( $kpis["avance_8"] != "" && (int)$agosto["porcentaje"] < 100 ): ?>
                    <div>
                        <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                            Acción Correctiva
                        </span>
                    </div>
                <?php endif; ?>
            </td>
            <td>
                <?= $septiembre["html"]; ?>
                <b><?= $septiembre["porcentaje"]; ?>% </b>
                <?php if( $kpis["avance_9"] != "" && (int)$septiembre["porcentaje"] < 100 ): ?>
                    <div>
                        <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                            Acción Correctiva
                        </span>
                    </div>
                <?php endif; ?>
            </td>
            <td>
                <?= $octubre["html"]; ?>
                <b><?= $octubre["porcentaje"]; ?>% </b>
                <?php if( $kpis["avance_10"] != "" && (int)$octubre["porcentaje"] < 100 ): ?>
                    <div>
                        <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                            Acción Correctiva
                        </span>
                    </div>
                <?php endif; ?>
            </td>
            <td>
                <?= $noviembre["html"]; ?>
                <b><?= $noviembre["porcentaje"]; ?>% </b>
                <?php if( $kpis["avance_11"] != "" && (int)$noviembre["porcentaje"] < 100 ): ?>
                    <div>
                        <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                            Acción Correctiva
                        </span>
                    </div>
                <?php endif; ?>
            </td>
            <td>
                <?= $diciembre["html"]; ?>
                <b><?= $diciembre["porcentaje"]; ?>% </b>
                <?php if( $kpis["avance_12"] != "" && (int)$diciembre["porcentaje"] < 100 ): ?>
                    <div>
                        <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                            Acción Correctiva
                        </span>
                    </div>
                <?php endif; ?>
            </td>
            <td>
                <?= $enero["html"]; ?>
                <b><?= $enero["porcentaje"]; ?>% </b>
                <?php if( $kpis["avance_1"] != "" && (int)$enero["porcentaje"] < 100 ): ?>
                    <div>
                        <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                            Acción Correctiva
                        </span>
                    </div>
                <?php endif; ?>
            </td>
            <td>
                <?= $febrero["html"]; ?>
                <b><?= $febrero["porcentaje"]; ?>% </b>
                <?php if( $kpis["avance_2"] != "" && (int)$febrero["porcentaje"] < 100 ): ?>
                    <div>
                        <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                            Acción Correctiva
                        </span>
                    </div>
                <?php endif; ?>
            </td>
            <td>
                <?= $marzo["html"]; ?>
                <b><?= $marzo["porcentaje"]; ?>% </b>
                <?php if( $kpis["avance_3"] != "" && (int)$marzo["porcentaje"] < 100 ): ?>
                    <div>
                        <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                            Acción Correctiva
                        </span>
                    </div>
                <?php endif; ?>
            </td>
            <td>
                <?= $abril["html"]; ?>
                <b><?= $abril["porcentaje"]; ?>% </b>
                <?php if( $kpis["avance_4"] != "" && (int)$abril["porcentaje"] < 100 ): ?>
                    <div>
                        <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                            Acción Correctiva
                        </span>
                    </div>
                <?php endif; ?>
            </td>
            <td>
                <?= $mayo["html"]; ?>
                <b><?= $mayo["porcentaje"]; ?>% </b>
                <?php if( $kpis["avance_5"] != "" && (int)$mayo["porcentaje"] < 100 ): ?>
                    <div>
                        <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                            Acción Correctiva
                        </span>
                    </div>
                <?php endif; ?>
            </td>
            <td>
                <?= $junio["html"]; ?>
                <b><?= $junio["porcentaje"]; ?>%</b>
                <?php if( $kpis["avance_6"] != "" && (int)$junio["porcentaje"] < 100 ): ?>
                    <div>
                        <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                            Acción Correctiva
                        </span>
                    </div>
                <?php endif; ?>
            </td>

            <td>
                <div class="progress">
				    <div class="progress-bar bg-success" role="progressbar" style="width: <?= $kpis["avance_kpis"]; ?>%; background-color: <?= $kpis["color_avance_kpis"]; ?> !important;" aria-valuenow="<?= $kpis["avance_kpis"] ?>" aria-valuemin="0" aria-valuemax="100"></div>
			    </div>
                <b><?= $kpis["avance_kpis"]; ?>%</b>
            </td>
        </tr>
    </table>
</div>