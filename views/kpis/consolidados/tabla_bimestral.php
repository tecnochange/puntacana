<?php
$enero = $ClassKpisServicios->avance_kpi_mes($kpis["enero"], $kpis["avance_1"], $kpis);
$marzo = $ClassKpisServicios->avance_kpi_mes($kpis["marzo"], $kpis["avance_3"], $kpis);
$mayo = $ClassKpisServicios->avance_kpi_mes($kpis["mayo"], $kpis["avance_5"], $kpis);
$julio = $ClassKpisServicios->avance_kpi_mes($kpis["julio"], $kpis["avance_7"], $kpis);
$septiembre = $ClassKpisServicios->avance_kpi_mes($kpis["septiembre"], $kpis["avance_9"], $kpis);
$noviembre = $ClassKpisServicios->avance_kpi_mes($kpis["noviembre"], $kpis["avance_11"], $kpis);

/**************** ACTIVAR LA ETIQUETA DE ACCIÓN CORRECTIVA - SOLO PARA TABLA MENSUAL ****************/
//VALIDAMOS EL ESTADO DE $kpis["avance_X"]
$accion_correctiva = NULL;
if ((!$kpis["avance_7"] || empty($kpis["avance_7"])) && $kpis["avance_7"] !== "0") {
    $accion_correctiva++;
}
if ((!$kpis["avance_9"] || empty($kpis["avance_9"])) && $kpis["avance_9"] !== "0") {
    $accion_correctiva++;
}
if ((!$kpis["avance_11"] || empty($kpis["avance_11"])) && $kpis["avance_11"] !== "0") {
    $accion_correctiva++;
}
if ((!$kpis["avance_1"] || empty($kpis["avance_1"])) && $kpis["avance_1"] !== "0") {
    $accion_correctiva++;
}
if ((!$kpis["avance_3"] || empty($kpis["avance_3"])) && $kpis["avance_3"] !== "0") {
    $accion_correctiva++;
}
if ((!$kpis["avance_5"] || empty($kpis["avance_5"])) && $kpis["avance_5"] !== "0") {
    $accion_correctiva++;
}
$read_only_kpis = "";
?>

<table class="table table-bordered">
    <tr>
        <td>MES</td>
        <td>Julio - Agosto
            <?php if(!empty($kpis["avance_7"]) || $kpis["avance_7"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Julio - Agosto"); ?></div>
            <?php endif; ?>
        </td>
        <td>Septiembre - Octubre
            <?php if(!empty($kpis["avance_9"]) || $kpis["avance_9"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Septiembre - Octubre"); ?></div>
            <?php endif; ?>
        </td>
        <td>Noviembre - Diciembre
            <?php if(!empty($kpis["avance_11"]) || $kpis["avance_11"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Noviembre - Diciembre"); ?></div>
            <?php endif; ?>
        </td>
        <td>Enero - Febrero
            <?php if(!empty($kpis["avance_1"]) || $kpis["avance_1"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Enero - Febrero"); ?></div>
            <?php endif; ?>
        </td>
        <td>Marzo - Abril
            <?php if(!empty($kpis["avance_3"]) || $kpis["avance_3"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Marzo - Abril"); ?></div>
            <?php endif; ?>
        </td>
        <td>Mayo - Junio
            <?php if(!empty($kpis["avance_5"]) || $kpis["avance_5"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Mayo - Junio"); ?></div>
            <?php endif; ?>
        </td>
        <td>TOTAL</td>
    </tr>
    <tr>
        <td>META</td>

        <td><?= $kpis["julio"]; ?></td>
        <td><?= $kpis["septiembre"]; ?></td>
        <td><?= $kpis["noviembre"]; ?></td>

        <td><?= $kpis["enero"]; ?></td>
        <td><?= $kpis["marzo"]; ?></td>
        <td><?= $kpis["mayo"]; ?></td>

        <td><?= $kpis["meta"]; ?></td>

    </tr>
    <?php if($_SESSION["anio_fill"] >= 2027){ ?>
    <tr>
        <td>MÍNIMO ESPERADO</td>
        <td><?= $kpis["julio_min"]; ?></td>
        <td><?= $kpis["septiembre_min"]; ?></td>
        <td><?= $kpis["noviembre_min"]; ?></td>
        <td><?= $kpis["enero_min"]; ?></td>
        <td><?= $kpis["mayo_min"]; ?></td>

        <td></td>
    </tr>
    <?php } ?>
    <tr>
        <td>SEGUIMIENTO</td>

        <td> <input type="text" class="form-control" value="<?= $kpis["avance_7"]; ?>" name="avance_7" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?>> </td>
        <td> <input type="text" class="form-control" value="<?= $kpis["avance_9"]; ?>" name="avance_9" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> > </td>
        <td> <input type="text" class="form-control" value="<?= $kpis["avance_11"]; ?>" name="avance_11" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> > </td>

        <td> <input type="text" class="form-control" value="<?= $kpis["avance_1"]; ?>" name="avance_1" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> > </td>
        <td> <input type="text" class="form-control" value="<?= $kpis["avance_3"]; ?>" name="avance_3" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> > </td>
        <td> <input type="text" class="form-control" value="<?= $kpis["avance_5"]; ?>" name="avance_5" onkeyup="return NumerosDecimales(this)" <?= $read_only_kpis; ?> > </td>

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
            <?php if(!$accion_correctiva && (int)$julio["porcentaje"] < 100): ?>
                <div>
                    <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                        Acción Correctiva
                    </span>
                </div>
            <?php endif; ?>
        </td>
        <td>
            <?= $septiembre["html"]; ?>
            <b><?= $septiembre["porcentaje"]; ?>%</b>
            <?php if( $kpis["avance_9"] != "" && (int)$septiembre["porcentaje"] < 100 ): ?>
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
            <?= $mayo["html"]; ?>
            <b><?= $mayo["porcentaje"]; ?>%</b>
            <?php if( $kpis["avance_5"] != "" && (int)$mayo["porcentaje"] < 100 ): ?>
                <div>
                    <span class="btn btn-sm btn-warning open-info-modal" style="font-size:0.6rem; min-width:110px;">
                        Acción Correctiva
                    </span>
                </div>
            <?php endif; ?>
        </td>

        <td>
            <div class="progress">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?= $kpis["avance_kpis"]; ?>%; background-color: <?= $kpis["color_avance_kpis"]; ?> !important;" aria-valuenow="<?= $kpis["avance_kpis"]; ?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <b><?= $kpis["avance_kpis"]; ?>%</b>
        </td>

        
    </tr>
</table>