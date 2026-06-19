<script>
    $(document).ready(function() {
        $('#menuGoForExpert').collapse();
        $("#bt_goforexpert_reportes").addClass("active");
    });
</script>


<div class="col-md-12" align="center" style="padding: 10px 0px; margin-bottom: 20px">
    <h3 style="margin-top: 8px;">Reportes Academia</h3>
</div>

<style>
    .card-body {
        background-color: #f8f9fa;
        /* Color de fondo */
        padding: 20px;
        /* Espaciado interno */
        border-radius: 5px;
        /* Bordes redondeados */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* Sombra */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        /* Transiciones */
    }

    .card-body:hover {
        transform: translateY(-5px);
        /* Efecto de elevación */
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        /* Sombra más pronunciada */
    }

    .card-body a {
        text-decoration: none;
        /* Sin subrayado en enlaces */
    }

    .card-body button {
        width: calc(100% - 40px);
        /* Ancho casi completo, ajustando al padding del card-body */
        font-weight: bold;
        /* Texto en negrita */
        font-size: 16px;
        /* Tamaño de fuente */
        margin: 0 auto;
        /* Centrar el botón horizontalmente */
        display: block;
        /* Asegurar que el margen auto funcione */
    }
</style>

<div class="container">

    <div class="row">

            <div class="col-md-6" style="margin-bottom: 15px">
                <div class="card">

                    <div class="card-body">
                        <a href="<?php echo $url; ?>?pg=goforexpert/admin/resumen_evaluaciones">
                            <button type="button" class="btn btn-success btn-block btn-sm">
                                Reporte Evaluaciones
                            </button>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6" style="margin-bottom: 15px">
                <div class="card">

                    <div class="card-body">
                        <a href="<?php echo $url; ?>?pg=goforexpert/admin/reportes">
                            <button type="button" class="btn btn-success btn-block btn-sm">
                                Reporte Cursos
                            </button>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6" style="margin-bottom: 15px">
                <div class="card">

                    <div class="card-body">
                        <a href="<?php echo $url; ?>?pg=goforexpert/admin/tablero">
                            <button type="button" class="btn btn-success btn-block btn-sm">
                                Analitica
                            </button>
                        </a>
                    </div>
                </div>
            </div>
        
            <div class="col-md-6" style="margin-bottom: 15px;">
                <div class="card">

                    <div class="card-body">
                        <a href="<?php echo $url; ?>?pg=goforexpert/admin/resumen_encuestas">
                            <button type="button" class="btn btn-success btn-block btn-sm">
                                Analitica Encuesta
                            </button>
                        </a>
                    </div>
                </div>
            </div>


       



    </div>
</div>