<?php
//SOLO PARA POPUPS
if (isset($iniciativa['id'])) {
    $okrs_planes_accion = $ClassOkrsServicios->okrs_obtener_plan_accion_iniciativa($iniciativa['id']);
}
?>

<style>
    .kanban {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        padding: 20px;
    }

    .column {
        background: #f4f4f4;
        padding: 10px;
        border-radius: 8px;
        min-height: 300px;
    }

    .column h3 {
        text-align: center;
        margin-bottom: 10px;
    }

    .item {
        background: white;
        padding: 10px;
        margin: 10px 0;
        border-radius: 6px;
        cursor: grab;
        border: 1px solid #ddd;
    }

    .column.drag-over {
        border: 2px dashed #333;
    }
</style>
<div class="content">
    <div class="col-md-12">
        <div class="alert alert-warning" role="alert">
            Para actualizar el estado backlog de cada plan de acción, arrastre cada plan de acción al estado deseado y despúes de realizar esta acción con los planes de acción seleccionados, haga clic en el botón Actualizar Tablero, para confirmar los cambios.
        </div>
    </div>

    <div class="col-md-12">
        <div class="kanban">

            <?php
            //CLASIFICACION DE PLANES DE ACCION SEGUN SU ESTADO DE BACKLOG
            $planificado = [];
            $en_progreso = [];
            $en_revision = [];
            $completado = [];
            foreach ($okrs_planes_accion as $plan_de_accion) {
                //dd($plan_de_accion["estado_backlog"]);
                switch ($plan_de_accion["estado_backlog"]) {
                    case '1':
                        $planificado[] = $plan_de_accion;
                        break;
                    case '2':
                        $en_progreso[] = $plan_de_accion;
                        break;
                    case '3':
                        $en_revision[] = $plan_de_accion;
                        break;
                    case '4':
                        $completado[] = $plan_de_accion;
                        break;
                    default:
                        $planificado[] = $plan_de_accion;
                        break;
                }
            }
            ?>

            <div class="column" id="planificado">
                <h3 class="text-secondary">PLANIFICADO</h3>
                <?php foreach ($planificado as $item_planificado): ?>
                    <div id="<?= $item_planificado["id"]; ?>" class="item" draggable="true">
                        <?php
                        $responsable_foto = !empty($item_planificado["publicado_por"]["foto"]) ? $item_planificado["publicado_por"]["foto"] : "img_default.jpg";
                        $modalId = "modal_responsable_" . $item_planificado["publicado_por"]["id"];
                        ?>
                        <img src="https://goforagile.com/recursos/<?= $responsable_foto; ?>"
                            class="foto_miniaturas mb-1"
                            title="<?= $item_planificado["publicado_por"]["nombre"]; ?>"
                            style="cursor:pointer;"
                            data-bs-toggle="modal"
                            data-bs-target="#<?= $modalId ?>">
                        <?php
                        $responsableData = $item_planificado["publicado_por"];
                        $modales_responsables[$item_planificado["publicado_por"]["id"]] = $responsableData;
                        ?>
                        <b><?= $item_planificado["publicado_por"]["nombre"]; ?></b>
                        <hr>
                        <?= $item_planificado["descripcion"]; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="column" id="progreso">
                <h3>EN PROGRESO</h3>
                <?php foreach ($en_progreso as $item_en_progreso): ?>
                    <div id="<?= $item_en_progreso["id"]; ?>" class="item" draggable="true">
                        <?php
                        $responsable_foto = !empty($item_en_progreso["publicado_por"]["foto"]) ? $item_en_progreso["publicado_por"]["foto"] : "img_default.jpg";
                        $modalId = "modal_responsable_" . $item_en_progreso["publicado_por"]["id"];
                        ?>
                        <img src="https://goforagile.com/recursos/<?= $responsable_foto; ?>"
                            class="foto_miniaturas mb-1"
                            title="<?= $item_en_progreso["publicado_por"]["nombre"]; ?>"
                            style="cursor:pointer;"
                            data-bs-toggle="modal"
                            data-bs-target="#<?= $modalId ?>">
                        <?php
                        $responsableData = $item_en_progreso["publicado_por"];
                        $modales_responsables[$item_en_progreso["publicado_por"]["id"]] = $responsableData;
                        ?>
                        <b><?= $item_en_progreso["publicado_por"]["nombre"]; ?></b>
                        <hr>
                        <?= $item_en_progreso["descripcion"]; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="column" id="revision">
                <h3 class="text-warning">EN REVISIÓN</h3>
                <?php foreach ($en_revision as $item_en_revision): ?>
                    <div id="<?= $item_en_revision["id"]; ?>" class="item" draggable="true">
                        <?php
                        $responsable_foto = !empty($item_en_revision["publicado_por"]["foto"]) ? $item_en_revision["publicado_por"]["foto"] : "img_default.jpg";
                        $modalId = "modal_responsable_" . $item_en_revision["publicado_por"]["id"];
                        ?>
                        <img src="https://goforagile.com/recursos/<?= $responsable_foto; ?>"
                            class="foto_miniaturas mb-1"
                            title="<?= $item_en_revision["publicado_por"]["nombre"]; ?>"
                            style="cursor:pointer;"
                            data-bs-toggle="modal"
                            data-bs-target="#<?= $modalId ?>">
                        <?php
                        $responsableData = $item_en_revision["publicado_por"];
                        $modales_responsables[$item_en_revision["publicado_por"]["id"]] = $responsableData;
                        ?>
                        <b><?= $item_en_revision["publicado_por"]["nombre"]; ?></b>
                        <hr>
                        <?= $item_en_revision["descripcion"]; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="column" id="completado">
                <h3 class="text-success">COMPLETADO</h3>
                <?php foreach ($completado as $item_completado): ?>
                    <div id="<?= $item_completado["id"]; ?>" class="item" draggable="true">
                        <?php
                        $responsable_foto = !empty($item_completado["publicado_por"]["foto"]) ? $item_completado["publicado_por"]["foto"] : "img_default.jpg";
                        $modalId = "modal_responsable_" . $item_completado["publicado_por"]["id"];
                        ?>
                        <img src="https://goforagile.com/recursos/<?= $responsable_foto; ?>"
                            class="foto_miniaturas mb-1"
                            title="<?= $item_completado["publicado_por"]["nombre"]; ?>"
                            style="cursor:pointer;"
                            data-bs-toggle="modal"
                            data-bs-target="#<?= $modalId ?>">
                        <?php
                        $responsableData = $item_completado["publicado_por"];
                        $modales_responsables[$item_completado["publicado_por"]["id"]] = $responsableData;
                        ?>
                        <b><?= $item_completado["publicado_por"]["nombre"]; ?></b>
                        <hr>
                        <?= $item_completado["descripcion"]; ?>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>

    </div>
</div>

<script>
(function() {

    var api_okrs = '<?php echo $url; ?>api/okrs/';

    const items = document.querySelectorAll('.item');
    const columns = document.querySelectorAll('.column');
    let draggedItem = null;

    items.forEach(item => {
        item.addEventListener('dragstart', () => {
            draggedItem = item;
            item.classList.add('dragging');
        });

        item.addEventListener('dragend', () => {
            draggedItem = null;
            item.classList.remove('dragging');
        });
    });

    columns.forEach(column => {
        column.addEventListener('dragover', e => {
            e.preventDefault();
            column.classList.add('drag-over');
        });

        column.addEventListener('dragleave', () => {
            column.classList.remove('drag-over');
        });

        column.addEventListener('drop', () => {
            column.appendChild(draggedItem);
            column.classList.remove('drag-over');

            if(column.id == "planificado"){
                cambiarEstadoBacklog(draggedItem.id, 1);
            }else if(column.id == "progreso"){
                cambiarEstadoBacklog(draggedItem.id, 2);
            }else if(column.id == "revision"){
                cambiarEstadoBacklog(draggedItem.id, 3);
            }else if(column.id == "completado"){
                cambiarEstadoBacklog(draggedItem.id, 4);
            }
        });
    });

    const editarBacklogRow = (id, backlog) => {
        const select = $("#select_backlog_" + id);
        const container = select.closest("div");
        const texto = container.find(".backlog-texto");
        const backlogs = {
            1: '<span class="btn btn-sm btn-info bg-white">Planificado</span>',
            2: '<span class="btn btn-sm btn-success">En Progreso</span>',
            3: '<span class="btn btn-sm btn-warning">En Revisión</span>',
            4: '<span class="btn btn-sm btn-success bg-success">Completado</span>'
        };
        texto.html(backlogs[backlog]);
    };

    function cambiarEstadoBacklog(id_plan_accion, estado){
        jQuery.ajax({
            url: api_okrs + "editar_estado_backlog.php",
            type: 'post',
            data: {
                id_plan_accion: id_plan_accion,
                estado: estado
            },
            success: function(data){
                editarBacklogRow(id_plan_accion, estado);
            }
        });
    }

})();
</script>