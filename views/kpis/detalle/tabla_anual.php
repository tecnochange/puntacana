<div class="mt-3 advertencia">Ingresa la meta para cada etapa de seguimiento.</div>
<table class="table table-bordered">
    <tr>
        <td>Julio - Junio</td>
    </tr>
   
    <tr>

        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["julio"]; ?>" name="julio" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>

    </tr>
</table>

<?php if($_SESSION["anio_fill"] >= 2027){ ?>
<div class="mt-3 advertencia">Ingresa el mínimo para cada etapa de seguimiento.</div>
<table class="table table-bordered">
    <tr>
        <td>Julio - Junio</td>
    </tr>
    <tr>

        <td> <input type="text" class="form-control mes_minimo" value="<?= $kpis["julio_min"]; ?>" name="julio_min" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>

    </tr>
</table>
<?php } ?>