<div>Ingresa la meta para cada etapa de seguimiento.</div>
<table class="table table-bordered">
    <tr>
        <td>Julio - Septiembre</td>
        <td>Octubre - Diciembre</td>
        <td>Enero - Marzo</td>
        <td>Abril - Junio</td>
    </tr>
   
    <tr>

        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["julio"]; ?>" name="julio" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["octubre"]; ?>" name="octubre" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["enero"]; ?>" name="enero" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["abril"]; ?>" name="abril" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>

    </tr>
    
</table>


<?php if($_SESSION["anio_fill"] >= 2027){ ?>
<div class="mt-3 advertencia">Ingresa el mínimo para cada etapa de seguimiento.</div>
<table class="table table-bordered">
    <tr>
        <td>Julio - Septiembre</td>
        <td>Octubre - Diciembre</td>
        <td>Enero - Marzo</td>
        <td>Abril - Junio</td>
    </tr>
    <tr>

        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["julio_min"]; ?>" name="julio_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["octubre_min"]; ?>" name="octubre_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["enero_min"]; ?>" name="enero_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["abril_min"]; ?>" name="abril_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>

    </tr>
</table>
<?php } ?>