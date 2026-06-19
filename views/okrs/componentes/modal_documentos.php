<div class="modal fade" id="modal_documentos" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <div id="modal_documentos_titulo"></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" style="max-height:400px; overflow-y:auto;">
                <div class="card">
                    <div class="card-header">
                        <h5>Nuevo Documento</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="formFile" class="form-label">Sube un archivo</label>
                            <input class="form-control" type="file" id="input_documento" required>
                        </div>
                        <div class="w-100 d-flex">
                            <input type="text" id="input_comentario_documento" class="form-control me-2" placeholder="Escribe un comentario..." required>
                        </div>
                        <div class="w-100 mt-4">
                            <button class="btn btn-primary float-end" id="btn_guardar_documento">Cargar el documento</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="card w-100">
                    <div class="card-header">
                        <h5>Historial de Documentos</h5>
                    </div>
                    <div class="card-body" id="contenedor_documentos">
                        <div class="text-center text-muted">Cargando...</div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>