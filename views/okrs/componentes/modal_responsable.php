<?php
// $responsableData debe estar disponible antes de incluir este archivo.
// Campos esperados: id, nombre, cargo, email, foto, telefono (o los que tengas)

$foto = !empty($responsableData["foto"]) ? $responsableData["foto"] : "img_default.jpg";
$modalId = "modal_responsable_" . $responsableData["id"];
?>

<div class="modal fade" id="<?= $modalId ?>" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" style="color:#007bff;"><?= $responsableData["nombre"] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-center">
                <img src="https://goforagile.com/recursos/<?= $foto ?>" 
                    style="width:100px;height:100px;border-radius:50%;object-fit:cover;">
                
                <div class="mt-3 text-start">
                    <?php if (!empty($responsableData["nombre_cargo"])): ?>
                        <p><strong style="color:#007bff;">Cargo </strong> <br> <?= $responsableData["nombre_cargo"] ?></p>
                    <?php endif; ?>

                    <?php if (!empty($responsableData["nombre_area"])): ?>
                        <p><strong style="color:#007bff;">Area </strong> <br> <?= $responsableData["nombre_area"] ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>
