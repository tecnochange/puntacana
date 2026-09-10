<div class="mt-3 advertencia">Ingresa la meta para cada etapa de seguimiento.</div>
<table class="table table-bordered">
    <tr>
        <td>Julio</td>
        <td>Agosto</td>
        <td>Septiembre</td>
        <td>Octubre</td>
        <td>Noviembre</td>
        <td>Diciembre</td>
        <td>Enero</td>
        <td>Febrero</td>
        <td>Marzo</td>
        <td>Abril</td>
        <td>Mayo</td>
        <td>Junio</td>
    </tr>
   
    <tr>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["julio"]; ?>" name="julio" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this); " > </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["agosto"]; ?>" name="agosto" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)" > </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["septiembre"]; ?>" name="septiembre" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)" > </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["octubre"]; ?>" name="octubre" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)" > </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["noviembre"]; ?>" name="noviembre" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)" > </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["diciembre"]; ?>" name="diciembre" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)" > </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["enero"]; ?>" name="enero" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)" > </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["febrero"]; ?>" name="febrero" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)" > </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["marzo"]; ?>" name="marzo" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)" > </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["abril"]; ?>" name="abril" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)" > </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["mayo"]; ?>" name="mayo" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)" > </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["junio"]; ?>" name="junio" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)" > </td>
    </tr>
    
</table>


<?php if($_SESSION["anio_fill"] >= 2027){ ?>
<div class="mt-3 advertencia">Ingresa el mínimo para cada etapa de seguimiento.</div>
<table class="table table-bordered">
    <tr>
        <td>Julio</td>
        <td>Agosto</td>
        <td>Septiembre</td>
        <td>Octubre</td>
        <td>Noviembre</td>
        <td>Diciembre</td>
        <td>Enero</td>
        <td>Febrero</td>
        <td>Marzo</td>
        <td>Abril</td>
        <td>Mayo</td>
        <td>Junio</td>
    </tr>
    <tr>

        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["julio_min"]; ?>" name="julio_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["agosto_min"]; ?>" name="agosto_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["septiembre_min"]; ?>" name="septiembre_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["octubre_min"]; ?>" name="octubre_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["noviembre_min"]; ?>" name="noviembre_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["diciembre_min"]; ?>" name="diciembre_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["enero_min"]; ?>" name="enero_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["febrero_min"]; ?>" name="febrero_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["marzo_min"]; ?>" name="marzo_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["abril_min"]; ?>" name="abril_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["mayo_min"]; ?>" name="mayo_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["junio_min"]; ?>" name="junio_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
    </tr>
</table>
<?php } ?>