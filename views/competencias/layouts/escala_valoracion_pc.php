<!-- Escala -->
<div class="card" style="margin-bottom: 15px">
    <div class="card-body" align="center">

        <div><h4 style="color: black !important;"><b>ESCALA DE VALORACIÓN A UTILIZAR</b></h4></div>
        <div style="margin-bottom: 15px">Para valorar cada uno de los comportamientos de las competencias utilice la siguiente escala; léala detenidamente antes de comenzar la valoración.</div>

        <?php
        $queryEscala = mysqli_query($connect_valoracion, "SELECT * FROM Escalas WHERE id_empresa = '" . $user_log["id_empresa"] . "' AND anio = '" . $_SESSION['anio_ciclo'] . "' ");
        $dataEscala = mysqli_fetch_array($queryEscala);
        ?>

        <div class="table-responsive">
        <table width="100%">

            <tr>
                <td width="16.6%" align="center" style="font-size: 20px; padding: 5px; font-weight: bold">
                    1
                </td>
                <td width="16.6%" align="center" style="font-size: 20px; padding: 5px; font-weight: bold">
                    2
                </td>
                <td width="16.6%" align="center" style="font-size: 20px; padding: 5px; font-weight: bold">
                    3
                </td>
                <td width="16.6%" align="center" style="font-size: 20px; padding: 5px; font-weight: bold">
                    4
                </td>
                <td width="16%" align="center" style="font-size: 20px; padding: 5px; font-weight: bold">
                    5
                </td>
            </tr>
            <tr>
                <td align="center" valign="top" style="background-color: #ffd965; color: black;">
                    <b><?php echo $dataEscala["nombre_n_1"]; ?></b><br>
                </td>
                <td align="center" valign="top" style="background-color: #ffd965; color: black;">
                    <b><?php echo $dataEscala["nombre_n_2"]; ?></b><br>
                </td>
                <td align="center" valign="top" style="background-color: #ffd965; color: black;">
                    <b><?php echo $dataEscala["nombre_n_3"]; ?></b><br>
                </td>
                <td align="center" valign="top" style="background-color: #ffd965; color: black;">
                    <b><?php echo $dataEscala["nombre_n_4"]; ?></b><br>
                </td>
                <td align="center" valign="top" style="background-color: #ffd965; color: black;">
                    <b><?php echo $dataEscala["nombre_n_5"]; ?></b><br>
                </td>
            </tr>
            <tr>
                <td align="center" valign="top">
                    <?php echo $dataEscala["descripcion_n_1"]; ?>
                </td>
                <td align="center" valign="top">
                    <?php echo $dataEscala["descripcion_n_2"]; ?>
                </td>
                <td align="center" valign="top">
                    <?php echo $dataEscala["descripcion_n_3"]; ?>
                </td>
                <td align="center" valign="top">
                    <?php echo $dataEscala["descripcion_n_4"]; ?>
                </td>
                <td align="center" valign="top">
                    <?php echo $dataEscala["descripcion_n_5"]; ?>
                </td>
            </tr>
            <tr>
                <td align="center">
                    <!-- Primera columna: 1 estrella pintada -->
                    <i class="bx bx-star" style="color: #3aae2a; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #9e9e9e; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #9e9e9e; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #9e9e9e; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #9e9e9e; font-size: 25px;"></i>
                </td>
                <td align="center">
                    <!-- Segunda columna: 2 estrellas pintadas -->
                    <i class="bx bx-star" style="color: #3aae2a; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #3aae2a; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #9e9e9e; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #9e9e9e; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #9e9e9e; font-size: 25px;"></i>
                </td>
                <td align="center">
                    <!-- Tercera columna: 3 estrellas pintadas -->
                    <i class="bx bx-star" style="color: #3aae2a; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #3aae2a; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #3aae2a; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #9e9e9e; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #9e9e9e; font-size: 25px;"></i>
                </td>
                <td align="center">
                    <!-- Cuarta columna: 4 estrellas pintadas -->
                    <i class="bx bx-star" style="color: #3aae2a; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #3aae2a; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #3aae2a; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #3aae2a; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #9e9e9e; font-size: 25px;"></i>
                </td>
                <td align="center">
                    <!-- Quinta columna: 5 estrellas pintadas -->
                    <i class="bx bx-star" style="color: #3aae2a; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #3aae2a; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #3aae2a; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #3aae2a; font-size: 25px;"></i>
                    <i class="bx bx-star" style="color: #3aae2a; font-size: 25px;"></i>
                </td>
            </tr>
        </table>
        </div>

    </div>

</div>