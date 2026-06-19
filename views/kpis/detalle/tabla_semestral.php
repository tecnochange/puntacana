<div>Ingresa la meta para cada etapa de seguimiento.</div>
<table class="table table-bordered">
    <tr>
        <td>Julio - Diciembre</td>
        <td>Enero - Junio </td>
    </tr>
   
    <tr>

        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["julio"]; ?>" name="julio" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>
        <td> <input type="text" class="form-control mes_valor" value="<?= $kpis["enero"]; ?>" name="enero" onkeyup="ValidarUnidadMedida(this); return NumerosDecimales(this)"> </td>

    </tr>
    
</table>