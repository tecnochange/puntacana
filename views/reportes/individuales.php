<script>
    $(document).ready(function() {
        $('#menuReportes').collapse();
        $('#bt_reportes_lideres').addClass('active');
    });
</script>

<?php
//DATOS DEL USUARIO
include("app/models/estructura/Colaboradores.php");
$ClassColaboradores = new Colaboradores();

$array_empleados = $ClassColaboradores->colaboradores_lista(null, $connect_admin);
?>

<div class="container-fluid pb-4" style="max-width: 90%; margin: 0 auto;">

    <div class="card mb-3 text-center">
        <div class="card-body">
            <h3>Reporte Individual</h3>

        </div>
    </div>

     <?php include("views/reportes/layouts/filtros.php"); ?>

    <div class="card mb-3">
        <div class="card-body">
            <table class="table table-bordered" id="tabla">
                <thead>
                <tr>
                    <th>Documento</th>
                    <th>Nombres y Apellidos</th>
                    <th>Correo</th>
                    <th>Compañia</th>
                    <th>Alta dirección</th>
                    <th>Área</th>
                    <th>Cargo</th>
                    <th>Nivel Jerárquico</th>
                    <th>Estado</th>
                    <th>Avance OKRs</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $query = mysqli_query( $connect_admin , "SELECT * FROM Datos_Sincronizados WHERE anio = '".$_SESSION["anio_ciclo"]."' " );
                while($data = mysqli_fetch_array($query)){

                    $dataEmpleado = $array_empleados[$data["id_empleado"]];

                    echo '
                    <tr>
                        <td>'.$dataEmpleado["documento"].' - '.$data["id_empleado"].'</td>
                        <td>'.$dataEmpleado["nombre"].'</td>
                        <td>'.$dataEmpleado["correo"].'</td>
                        <td>'.$dataEmpleado["compania"].'</td>
                        <td>'.$dataEmpleado["nombre_vicepresidencia"].'</td>
                        <td>'.$dataEmpleado["nombre_area"].'</td>
                        <td>'.$dataEmpleado["nombre_cargo"].'</td>
                        <td>'.$dataEmpleado["nombre_nivel_jerarquico"].'</td>
                        <td>'.$data["nombre"].'</td>
                        <td> <b> '.$data["okrs"].'%<b> </td>
                    </tr>
                    ';
                }

                ?>
                </tbody>
            </table>
        </div>
    </div>

   

    
   




</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#tabla').DataTable();
    });

</script>