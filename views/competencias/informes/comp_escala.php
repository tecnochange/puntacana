<?php
$anio = $_SESSION["anio_fill"] ?? $_SESSION["anio_ciclo"];
$queryEscalaInter = mysqli_query($connect_valoracion, "SELECT * FROM Escalas_Interpretacion WHERE id_empresa = ".$user_log["id_empresa"]." AND anio = ".$anio." ");
$dataEscalaInter = mysqli_fetch_array($queryEscalaInter);
?>


<div style="background-color: #e0e0e0; border: 1px solid #d4d4d4; margin-bottom: 20px; font-size: 12px">
    <table width="100%">
        <tr>
            <td width="20%" bgcolor="#FF0000" align="center" style="font-size: 12px; padding: 5px; font-weight: bold">
                <?php echo $dataEscalaInter["nombre_n_1"]; ?>
            </td>
            <td width="20%" bgcolor="#FFF200" align="center" style="font-size: 12px; padding: 5px; font-weight: bold">
                <?php echo $dataEscalaInter["nombre_n_2"]; ?>
            </td>
            <td width="20%" bgcolor="#95FA03" align="center" style="font-size: 12px; padding: 5px; font-weight: bold">
                <?php echo $dataEscalaInter["nombre_n_3"]; ?>
            </td>
            <td width="20%" bgcolor="#14F209" align="center" style="font-size: 12px; padding: 5px; font-weight: bold">
                <?php echo $dataEscalaInter["nombre_n_4"]; ?>
            </td>
            <td width="20%" bgcolor="#00D30A" align="center" style="font-size: 12px; padding: 5px; font-weight: bold">
                <?php echo $dataEscalaInter["nombre_n_5"]; ?>
            </td>
            
        </tr>
        <tr>
            <td align="center" valign="top" style=" padding: 10px; padding-top: 15px; border-right: 1px solid #bbbbbb;">
                <b><?php echo $dataEscalaInter["descripcion_n_1"]; ?></b><br>
                <?php echo $dataEscalaInter["rango_1"]; ?>
            </td>
            <td align="center" valign="top" style=" padding: 10px; padding-top: 15px; border-right: 1px solid #bbbbbb;">
                <b><?php echo $dataEscalaInter["descripcion_n_2"]; ?></b><br>
                <?php echo $dataEscalaInter["rango_2"]; ?>
            </td>
            <td align="center" valign="top" style=" padding: 10px; padding-top: 15px; border-right: 1px solid #bbbbbb;">
                <b><?php echo $dataEscalaInter["descripcion_n_3"]; ?></b><br>
                <?php echo $dataEscalaInter["rango_3"]; ?>
            </td>
            <td align="center" valign="top" style=" padding: 10px; padding-top: 15px; border-right: 1px solid #bbbbbb;">
                <b><?php echo $dataEscalaInter["descripcion_n_4"]; ?></b><br>
                <?php echo $dataEscalaInter["rango_4"]; ?>
            </td>
            <td align="center" valign="top" style=" padding: 10px; padding-top: 15px; border-right: 1px solid #bbbbbb;">
                <b><?php echo $dataEscalaInter["descripcion_n_5"]; ?></b><br>
                <?php echo $dataEscalaInter["rango_5"]; ?>
            </td>
            
        </tr>
    </table>
</div>