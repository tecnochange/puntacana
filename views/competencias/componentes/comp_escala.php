<?php 
$queryEscalaInter = mysqli_query($connect_admin, "SELECT * FROM Escala_Medicion WHERE id_empresa = " . $_SESSION["id_empresa"] . "");
$dataEscalaInter = mysqli_fetch_array($queryEscalaInter);

?>


<div style="background-color: #e0e0e0; border: 1px solid #d4d4d4; margin-bottom: 20px; font-size: 12px">
    <table width="100%">
        <tr>
            <td width="20%" bgcolor="#FF0000" align="center" style="font-size: 12px; padding: 5px; font-weight: bold">
                <?php echo $dataEscalaInter["titulo_uno"]; ?>
            </td>
            <td width="20%" bgcolor="#FFF200" align="center" style="font-size: 12px; padding: 5px; font-weight: bold">
                <?php echo $dataEscalaInter["titulo_tres"]; ?>
            </td>
            <td width="20%" bgcolor="#95FA03" align="center" style="font-size: 12px; padding: 5px; font-weight: bold">
                <?php echo $dataEscalaInter["titulo_cuatro"]; ?>
            </td>
            <td width="20%" bgcolor="#14F209" align="center" style="font-size: 12px; padding: 5px; font-weight: bold">
                <?php echo $dataEscalaInter["titulo_cinco"]; ?>
            </td>
            <td width="20%" bgcolor="#00D30A" align="center" style="font-size: 12px; padding: 5px; font-weight: bold">
                <?php echo $dataEscalaInter["titulo_seis"]; ?>
            </td>
            
        </tr>
        <tr>
            <td align="center" valign="top" style=" padding: 10px; padding-top: 15px; border-right: 1px solid #bbbbbb;">
                <b><?php echo $dataEscalaInter["subtitulo_uno"]; ?></b><br>
                <?php echo $dataEscalaInter["porcentaje_uno"]; ?>% al <?php echo $dataEscalaInter["porcentaje_dos"]; ?>%
            </td>
            <td align="center" valign="top" style=" padding: 10px; padding-top: 15px; border-right: 1px solid #bbbbbb;">
                <b><?php echo $dataEscalaInter["subtitulo_tres"]; ?></b><br>
                <?php echo $dataEscalaInter["porcentaje_tres"]; ?>% al <?php echo $dataEscalaInter["porcentaje_cuatro"]; ?>%
            </td>
            <td align="center" valign="top" style=" padding: 10px; padding-top: 15px; border-right: 1px solid #bbbbbb;">
                <b><?php echo $dataEscalaInter["subtitulo_cuatro"]; ?></b><br>
                <?php echo $dataEscalaInter["porcentaje_cinco"]; ?>% al <?php echo $dataEscalaInter["porcentaje_seis"]; ?>%
            </td>
            <td align="center" valign="top" style=" padding: 10px; padding-top: 15px; border-right: 1px solid #bbbbbb;">
                <b><?php echo $dataEscalaInter["subtitulo_cinco"]; ?></b><br>
                <?php echo $dataEscalaInter["porcentaje_siete"]; ?>% al <?php echo $dataEscalaInter["porcentaje_ocho"]; ?>%
            </td>
            <td align="center" valign="top" style=" padding: 10px; padding-top: 15px; border-right: 1px solid #bbbbbb;">
                <b><?php echo $dataEscalaInter["subtitulo_seis"]; ?></b><br>
                <?php echo $dataEscalaInter["porcentaje_ocho"]; ?>% o más
            </td>
            
        </tr>
    </table>
</div>