<!-- Modal -->
<div class="modal fade" id="infoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="infoModalTitle">ACCIONES CORRECTIVAS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body text-justify" id="infoModalBody">
                <p class="mb-4">
                    Favor completar el formulario de <strong>Riesgos, Acciones Correctivas y Mejoras (GPC-CA-F3)</strong> <br><br>
                    Ubicado en la siguiente ruta: <br>
                    <strong>Q:\\CALIDAD\\FORMATOS</strong>
                </p>

                <p style="color:#397b21;">
                    <i>Una vez diligenciado, por favor cárguelo en la opción de <strong>Documentos</strong> dentro del sistema y seleccione la frecuencia correspondiente para garantizar que la documentación quede correctamente registrada.</i>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    const modal = new bootstrap.Modal(document.getElementById('infoModal'));

    document.querySelectorAll('.open-info-modal').forEach(button => {
        button.addEventListener('click', function() {
            modal.show();
        });
    });
</script>