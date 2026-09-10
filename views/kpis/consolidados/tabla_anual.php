<?php
$julio = $ClassKpisServicios->avance_kpi_mes($kpis["julio"], $kpis["avance_7"], $kpis); 

if($kpis["avance_plano_kpis"]){
    $kpis["avance_plano_kpis"] = round($kpis["avance_plano_kpis"], 2);
}

/**************** ACTIVAR LA ETIQUETA DE ACCIÓN CORRECTIVA - SOLO PARA TABLA MENSUAL ****************/
//VALIDAMOS EL ESTADO DE $kpis["avance_X"]
$accion_correctiva = NULL;
if ((!$kpis["avance_7"] || empty($kpis["avance_7"])) && $kpis["avance_7"] !== "0") {
    $accion_correctiva++;
}


$julio_lectura = '';
if(!$kpis["julio"]){ /*$julio_lectura = ' readonly '; */ }
if($meses_habilitados["julio"] == ""){ $julio_lectura = ' readonly '; }

$read_only_kpis = "";
?>

<table class="table table-bordered">
    <tr>
        <td>MES</td>
        <td>Julio - Junio
            <?php if(!empty($kpis["avance_7"]) || $kpis["avance_7"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Julio - Junio"); ?></div></td>
            <?php endif; ?>
        <td>TOTAL</td>
    </tr>
    <tr>
        <td>META</td>
        <td><?= $kpis["julio"]; ?></td>
        <td><?= $kpis["meta"]; ?></td>
    </tr>
    <?php if($_SESSION["anio_fill"] >= 2027){ ?>
    <tr>
        <td>MÍNIMO ESPERADO</td>
        <td><?= $kpis["julio_min"]; ?></td>
        <td></td>
    </tr>
    <?php } ?>
    <tr>
        <td>SEGUIMIENTO</td>
        <td> 
            <input type="text" class="form-control mes_valor" value="<?= $kpis["avance_7"]; ?>" name="avance_7" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> <?= $julio_lectura; ?> > 
        </td>
        
        <td> 
            <?php //if($kpis["avance_plano_kpis"] != 0){ ?>
                <?= $seguimiento_formato; ?>  
            <?php //} ?>
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
            <div class="progress">
				<div class="progress-bar bg-success" role="progressbar" style="width: <?= $kpis["avance_kpis"]; ?>%; background-color:<?= $kpis["color_avance_kpis"]; ?> !important;" aria-valuenow="<?= round($kpis["avance_kpis"]); ?>" aria-valuemin="0" aria-valuemax="100"></div>
			</div>
            <b><?= $kpis["avance_kpis"]; ?>%</b>
        </td>
    </tr>
</table>