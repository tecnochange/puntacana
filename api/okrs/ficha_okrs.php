

<?php
include("../../app/connect.php");
include("../../app/arrays.php");
include("../../app/functions.php");


include("../../app/models/okrs/OkrsCrud.php");
include("../../app/models/okrs/OkrsServicios.php");
$OkrsCRUD = new OkrsCrud();
$OkrsServicios = new OkrsServicios();

$hoy = date("Y-m-d H:i:s");

$data = $OkrsServicios->obtener_okrs($_POST["id_okr"]); //OBTENER OKRS
//print_r($data);

if(!$data["objetivos_estrategicos"]){
    $data["objetivos_estrategicos"] = 'Sin objetivos estratégicos';
}



$integrantes_asociados = $OkrsServicios->obtener_integrantes_asociados($_POST["id_empresa"], $_POST["id_okr"]); 


?>


    <h5><?php echo $data["objetivo_okr"] ?></h5>



    Avance General
    <div class="progress mb-3">
        <div class="progress-bar" role="progressbar" style="width: 25%; background-color: <?php echo $data["avance_color"] ?>; color: #000000;" aria-valuenow="<?php echo $data["avance"] ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $data["avance"] ?>%</div>
    </div>

    Periodo del: <br>
    <?php echo $data["fecha_inicia"] ?> al <?php echo $data["fecha_termina"] ?><br><br>
    
    Objetivos Estratégicos Relacionados: <br>
    <?php echo $data["objetivos_estrategicos"] ?><br><br>



   

    <h5 style="background-color: #007BFF !important; color: #ffffff; padding: 10px; text-align: center;">Listado de Integrantes del OKRs</h5>


    <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Integrante</th>
                            <th>Cargo</th>
                            <th>Alta Dirección</th>
                            <th>Área</th>
                            <th>Tipo</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($integrantes_asociados as $integrante): ?>

                            <?php
                            $integrante_foto = !empty($integrante["empleado"]["foto"]) ? $integrante["empleado"]["foto"] : "img_default.jpg"; //FOTO DEL INTEGRANTE
                            $tipo = $integrante["tipo"] == "1" ? "Administrador de Resultados Clave (KR)" : ($integrante["tipo"] == "2" ? "Contribuyente Directo" : ($integrante["tipo"] == "3" ? "Contribuyente de Apoyo" : "Sin Tipo Asignado"));
                            ?>

                            <tr>
                                <td><img src="<?= 'https://goforagile.com/recursos/' . $integrante_foto; ?>" width="40" height="40" class="rounded-circle" title="<?= $integrante["empleado"]["nombre"]; ?>"></td>
                                <td><?= $integrante["empleado"]["nombre"]; ?></td>
                                <td><?= $integrante["empleado"]["nombre_cargo"]; ?></td>
                                <td><?= $integrante["empleado"]["nombre_vicepresidencia"]; ?></td>
                                <td><?= $integrante["empleado"]["nombre_area"]; ?></td>
                                <td><?= $tipo; ?></td>
                                <td>
                                    
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
    </div>
        
</div>

