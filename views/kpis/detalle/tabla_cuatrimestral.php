<div>Ingresa la meta para cada etapa de seguimiento.</div>
<table class="table table-bordered">
    <tr>
        <td>Julio - Octubre</td>
        <td>Noviembre - Febrero </td>
        <td>Marzo - Junio</td>
    </tr>
   
    <tr>

        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["julio"]; ?>" name="julio" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["noviembre"]; ?>" name="noviembre" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["marzo"]; ?>" name="marzo" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>

    </tr>
    
</table>


<?php if($_SESSION["anio_fill"] >= 2027){ ?>
<div class="mt-3 advertencia">Ingresa el mínimo para cada etapa de seguimiento.</div>
<table class="table table-bordered">
    <tr>
        <td>Julio - Octubre</td>
        <td>Noviembre - Febrero </td>
        <td>Marzo - Junio</td>
    </tr>
    <tr>

        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["julio_min"]; ?>" name="julio_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["noviembre_min"]; ?>" name="noviembre_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["marzo_min"]; ?>" name="marzo_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>

    </tr>
</table>
<?php } ?>