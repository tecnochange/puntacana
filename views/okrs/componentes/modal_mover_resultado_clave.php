<!-- MODAL MOVER OKRS -->
<div class="modal fade" id="modal_mover_okrs" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- cerrar -->
            <div class="modal-header">
                <div><h5>Mover Resultados Clave (KR)</h5></div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" align="left">
                <form action="" method="POST">
                    <input type="hidden" name="mover_okrs" value="true">
                    <input type="hidden" name="id_resultado_mover" id="id_resultado_mover">
                    <label>Seleccione el OKR al cual quiere mover el Resultados Clave (KR)</label>
                    <select name="id_okrs_nuevo" id="id_okrs_nuevo" class="form-control">
                    </select>
                    <button class="btn btn-primary mt-3" type="submit">
                        Mover
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>

