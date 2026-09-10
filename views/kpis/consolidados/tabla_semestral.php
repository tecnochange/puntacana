<?php
$enero = $ClassKpisServicios->avance_kpi_mes($kpis["enero"], $kpis["avance_1"], $kpis);
$julio = $ClassKpisServicios->avance_kpi_mes($kpis["julio"], $kpis["avance_7"], $kpis);

/**************** ACTIVAR LA ETIQUETA DE ACCIÓN CORRECTIVA - SOLO PARA TABLA MENSUAL ****************/
//VALIDAMOS EL ESTADO DE $kpis["avance_X"]
$accion_correctiva = NULL;
if ((!$kpis["avance_1"] || empty($kpis["avance_1"])) && $kpis["avance_1"] !== "0") {
    $accion_correctiva++;
}
if ((!$kpis["avance_7"] || empty($kpis["avance_7"])) && $kpis["avance_7"] !== "0") {
    $accion_correctiva++;
}



//PARA VALIDAR SI EXISTE UN SEGUIMIENTO ESE MES
$julio_lectura = '';
$enero_lectura = '';

if(!$kpis["julio"]){ $julio_lectura = ' readonly '; }
if(!$kpis["enero"]){ $enero_lectura = ' readonly '; }

//MESES HABILITADOS
if( $meses_habilitados["julio"] == "" ){ $julio_lectura = ' readonly '; }
if( $meses_habilitados["enero"] == ""){ $enero_lectura = ' readonly '; }
?>

<table class="table table-bordered">
    <tr>
        <td>MES</td>
        <td>Julio - Diciembre
            <?php if (!empty($kpis["avance_7"]) || $kpis["avance_7"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Julio - Diciembre"); ?></div>
            <?php endif; ?>
        </td>
        <td>Enero - Junio
            <?php if (!empty($kpis["avance_1"]) || $kpis["avance_1"] == "0"): ?>
                <div><?= $ClassKpisServicios->obtener_comentarios($kpis["id_kpi"], "Enero - Junio"); ?></div>
            <?php endif; ?>
        </td>
        <td>TOTAL</td>
    </tr>
    <tr>
        <td>META</td>
        <td><?= $kpis["julio"]; ?></td>
        <td><?= $kpis["enero"]; ?></td>
        <td><?= $kpis["meta"]; ?></td>
    </tr>
    <?php if($_SESSION["anio_fill"] >= 2027){ ?>
    <tr>
        <td>MÍNIMO ESPERADO</td>
        <td><?= $kpis["julio_min"]; ?></td>
        <td><?= $kpis["enero_min"]; ?></td>
        <td></td>
    </tr>
    <?php } ?>
    <tr>
        <td>SEGUIMIENTO</td>
        <td> 
            <input type="text" class="form-control" value="<?= $kpis["avance_7"]; ?>" name="avance_7" onkeyup="return NumerosDecimales(this)" <?= $julio_lectura; ?>  > 
        </td>
        <td> 
            <input type="text" class="form-control" value="<?= $kpis["avance_1"]; ?>" name="avance_1" onkeyup="return NumerosDecimales(this)" <?= $enero_lectura; ?>> 
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
        <td> <b><?= $kpis["avance_kpis"]; ?>%</b> </td>
    </tr>
</table>