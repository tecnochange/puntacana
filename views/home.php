<style>
    .novedades{
        display: flex;
        overflow: hidden;
        gap: 20px;
    }
    .publicaciones{
        margin-bottom: 10px;
        border: 1px solid #cccccc;
        border-radius: 10px;
        padding: 15px;
        width: 100%;
    }
    .titulo_muro{
        text-align: center;
        font-weight: bold;
    }
    .img_muro{
        width: 100%;
        border-radius: 20px;
        margin-bottom: 10px;
        object-fit: contain;
        aspect-ratio: 4 / 3;
    }
    .desc_muro{
        font-size: 11px;
        
    }
    .fecha_muro{
        font-size: 11px;
        color: #959393;
        margin-bottom: 20px;
    }
    .datos_desempenio{
        display: flex;
        justify-content: space-between;
    }

    @media (max-width: 768px) {
        .novedades{
            display: block;
        }
    }
</style>

<?php 

/* dd($_SESSION); */
/* dd($user_log); */

//DATOS DE LOS KPIS
include("app/models/kpis/Kpis.php");
$ClassKpis = new Kpis();
$kpis = $ClassKpis->ResultadoKpis($dtEmpleado["id"], $_SESSION["id_empresa"], $_SESSION["periodo_desempenio_fill"]);

//DATOS DE LOS COMPETENCIAS
include("app/models/competencias/Competencias.php");
$ClassCompetencias = new Competencias();
$competencias = $ClassCompetencias->ResultadoCompetencias($dtEmpleado["id"], $_SESSION["id_empresa"], $_SESSION["periodo_desempenio_fill"]);

//DATOS DE LOS OKRS
include("app/models/okrs/Okrs.php");
$ClassOkrs = new Okrs();
$okrs = $ClassOkrs->ResultadoOkrs($dtEmpleado["id"], $_SESSION["id_empresa"], $_SESSION["periodo_desempenio_fill"]);

//DATOS DEL EQUIPO DE TRABAJO
include("app/models/estructura/Colaboradores.php");
$ClassColaboradores = new Colaboradores();
$equipo = $ClassColaboradores->equipo( $dtEmpleado["id"] ); 

?>

<div class="container">
    

    <div class="row">

        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body text-center">
                    <h3>Bienvenido a Go for Agile. 👋</h3>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-3">
            <div class="card mb-3">
                <div class="card-header text-center">
                    <h3>Novedades</h3>
                </div>
                <div class="card-body " >
                    <div class="novedades">
                    <?php 
                    //CONSULTAMOS LAS PUBLICACIONES
                    $queryMuro = mysqli_query($connect_clima, "SELECT * FROM Publicaciones 
                    WHERE id_empresa = '" . $_SESSION["id_empresa"] . "' AND estado = 1 AND fecha_publicacion <= '" . $ahora . "' " . $filtro . " ORDER BY id DESC LIMIT 3 ");
                    while($dataMuro = mysqli_fetch_array($queryMuro)){
                        include("views/home/ficha_generales.php");
                    }
                    ?>
                    </div>

                    <a href="?pg=home/muro">
                        <button class="btn btn-success">Ver Muro >></button>
                    </a>
                    
                </div>

                
            </div>

            <div class="card mb-3">
                <div class="card-header text-center">
                    <h3>Equipo</h3>
                </div>
                <div class="card-body">
                    <?php
                    foreach($equipo as $col){
                        echo '
                        <div class="publicaciones">
                            <b> '.$col["nombre"].' </b> <br>
                            '.$col["nombre_cargo"].'
                        </div>
                        ';
                    }
                    ?>
                    
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
           <div class="card">
                <div class="card-header text-center">
                    <h3>Mi Desempeño</h3s>
                </div>
                <div class="card-body">
                    <h5>Periodo <?php echo $_SESSION["periodo_desempenio_fill"]; ?> </h5>
                    <div class="publicaciones">
                        <div class="datos_desempenio">
                            Mis OKRs <br>
                            <b><?php echo $okrs; ?>%</b>
                        </div>
                    </div>

                    <div class="publicaciones">
                        <div class="datos_desempenio">
                            Mis KPIs<br>
                            <b><?php echo $kpis; ?>%</b>
                        </div>
                    </div>

                    <div class="publicaciones">
                        <div class="datos_desempenio">
                            Competencias <br>
                            <b><?php echo $competencias; ?>%</b>
                        </div>
                    </div>
                    <a href="?pg=desempenio/mi_desempenio">
                        <button class="btn btn-success">Ver Mi Desempeño</button>
                    </a>
                </div>
            </div> 
        </div>
    </div>
</div>