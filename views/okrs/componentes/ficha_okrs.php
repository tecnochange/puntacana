<style>
    .accordion-button .foto_miniaturas {
        width: 60px !important;
        height: 60px !important;
        object-fit: cover;
        border-radius: 50%;
    }

    .foto_miniaturas_owner {
        width: 60px !important;
        height: 60px !important;
        object-fit: cover;
        border-radius: 50%;
    }

    .foto_miniaturas {
        width: 35px !important;
        height: 35px !important;
        object-fit: cover;
        border-radius: 50%;
    }

    /* Estilo del texto dentro del círculo */
    .easy-pie-chart {
        /* Centra el texto vertical y horizontalmente */
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 12px;
        font-weight: bold;
        color: #333;
        /* Asegúrate de que el contenedor del texto pueda tener margen */
        line-height: normal;
    }

    .okr-progress-value {
        position: absolute;
        margin: auto;
        font-size: 0.9em;
    }
</style>

<!-- MODALES PARA RESPONSABLES -->
<?php $modales_responsables = []; ?>

<div class="card mb-3 pb-4">

    <div class="card-body">
        <div class="row w-100">

            <!-- Barra de progreso -->
            <div class="col-md-1 pt-4" style="text-align: center;text-align: -webkit-center;">

                <div class="easy-pie-chart chat_graf_<?php echo $okrs["id"]; ?>" data-percent="<?php echo $okrs["porcentaje_avance"]; ?>">
                    <div class="okr-progress-value">
                        <?php echo $okrs["porcentaje_avance"]; ?>%
                    </div>
                </div>
            </div>

            <!-- Foto del accordion-item -->
            <div class="col-md-1 text-center d-flex justify-content-center align-items-center">
                <?php
                $foto = !empty($okrs["owner"]["foto"]) ? $okrs["owner"]["foto"] : "img_default.jpg";
                $modalId = "modal_responsable_" . $okrs["owner"]["id"];
                ?>
                <img src="https://goforagile.com/recursos/<?= $foto ?>"
                    class="foto_miniaturas_owner"
                    title="<?= $okrs["owner"]["nombre"]; ?>"
                    style="cursor:pointer;"
                    data-bs-toggle="modal"
                    data-bs-target="#<?= $modalId ?>"
                >
                <?php $modales_responsables[$okrs["owner"]["id"]] = $okrs["owner"]; ?>
            </div>

            <div class="col-md-7 ">
                <div style="color:#007bff;">
                    <h5><?php echo $okrs["tipo_okrs"] ?></h5>
                </div>
                <div>Owner: <b><?= $okrs["owner"]["nombre"]; ?></b></div>
                <div class="mt-1"><b><?php echo $okrs["objetivo"]; ?></b></div>
                <div class="text-secondary" style="font-size:0.8em;"><b><?= $okrs["fecha_inicia"]; ?> a <?= $okrs["fecha_termina"]; ?></b></div>
            </div>

            <div class="col-md-3 pt-3">
                <div class="text-end mb-3">
                    
                    <button type="button" class="btn btn-success btn-sm " onclick="FichaOkrs(<?= $okrs["id_okrs"]; ?>)">
                        <i class="bx bx-check" title="Resumen"></i>
                    </button>

                    <?php if( $VALIDAR_ROOT["editar"] || $okrs["owner"]["id"] == $user_log["id"] ){ ?>
                    <a href="?pg=okrs/okr/detalle&id=<?= $okrs["id_okrs"]; ?>" class="btn btn-outline-dark btn-sm ">
                        <i class="bx bx-pencil" title="Editar Okrs"></i>
                    </a>
                    
                    <a href="?pg=okrs/okr/integrantes&id=<?= $okrs["id_okrs"]; ?>" class="btn btn-outline-dark btn-sm">
                        <i class="bx bx-user-check" title="Editar Integrantes"></i>
                    </a>
                    <?php } ?>
                    <a href="?pg=okrs/okr/crear_resultados&id=<?= $okrs["id_okrs"]; ?>" class="btn btn-outline-dark btn-sm">
                        <i class="bx bx-plus" title="Agregar Resultados Claves"></i>
                    </a>
                </div>
                <?php if($user_log["id"] == $okrs["id_empleado"]): ?>
                    <div>
                        <button class="btn btn-warning btn-sm" style=" border-radius: 50px; ">
                            <i class="bx bx-user"></i>
                        </button>
                        Tu Rol: <?= $okrs["owner"]["rol"]; ?>
                    </div>
                <?php endif; ?>

            </div>

        </div>
        <hr>                
        <div class="accordion mt-2" id="desplegable_<?php echo $okrs["id_okrs"]; ?>">
            <?php
            foreach ($okrs["resultados"] as $resultado) {
                include("views/okrs/componentes/resultado_clave.php");
            }
            ?>
        </div>

    </div>
</div>

<!-- MODALES PARA CADA RESPONSABLE -->
<?php foreach ($modales_responsables as $responsableData): ?>
    <?php include "modal_responsable.php"; ?>
<?php endforeach; ?>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/easy-pie-chart@2.1.7/dist/jquery.easypiechart.min.js"></script>
<script>
    $(document).ready(function() {
        $('.chat_graf_<?php echo $okrs["id"]; ?>').easyPieChart({
            // Opciones principales
            size: 60, // Diámetro del círculo
            lineWidth: 10, // Grosor del anillo
            barColor: '<?= isset($okrs["color_avance"]) ? $okrs["color_avance"] : (isset($okrs["bg_color"]) ? $okrs["bg_color"] : ""); ?>', // Color de la parte llena (verde)
            trackColor: '#E6E6E6', // Color de la parte vacía (gris claro)
            scaleColor: false, // Oculta las marcas de escala
            lineCap: 'butt', // Estilo del final del progreso ('round' para redondeado)
            animate: 1000, // Duración de la animación en ms al cargar

            // Función para actualizar el texto si el valor es dinámico
            onStep: function(from, to, percent) {
                // Este código mantiene el número centrado y lo actualiza durante la animación
                $(this.el).find('.okr-progress-value').text(Math.round(percent) + '%');
            }
        });
    });
</script>