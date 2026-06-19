<script>
    $(document).ready(function() {

    const $objetivo = $("#select_objetivo");
    const $resultado = $("#select_resultado");
    const $iniciativa = $("#select_iniciativa");

    // 1️⃣ FILTRA RESULTADOS CLAVE POR OBJETIVO
    $objetivo.on("change", function () {
        let idObjetivo = $(this).val();

        $resultado.prop("disabled", true).val("");
        $iniciativa.prop("disabled", true).val("");

        $resultado.find("option:not(:first)").hide();
        $iniciativa.find("option:not(:first)").hide();

        if (idObjetivo !== "") {
            $resultado.find(`option[data-idokr='${idObjetivo}']`).show();
            $resultado.prop("disabled", false);
        }
    });

    // 2️⃣ FILTRA INICIATIVAS POR RESULTADO CLAVE
    $resultado.on("change", function () {
        let idResultado = $(this).val();

        $iniciativa.prop("disabled", true).val("");
        $iniciativa.find("option:not(:first)").hide();

        if (idResultado !== "") {
            $iniciativa.find(`option[data-idresultado='${idResultado}']`).show();
            $iniciativa.prop("disabled", false);
        }
    });

});
</script>