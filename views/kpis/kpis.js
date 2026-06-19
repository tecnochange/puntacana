document.addEventListener('DOMContentLoaded', function () {
    const editarFrecuencia = document.getElementById('editar_frecuencia');
    const frecuenciaSeleccionada = document.getElementById('frecuencia');
    const tipoResultadoSeleccionado = document.getElementById('tipo_resultado');
    const unidadMedidaSeleccionada = document.getElementById('unidad_medida');
    const camposMensuales = document.getElementById('lista_mes_padre');
    const camposMensualesTiempo = document.getElementById('lista_mes_padre_horas');
    const camposBimestrales = document.getElementById('list_bimestral');
    const camposTrimestrales = document.getElementById('list_trimestral');
    const camposCuatrimestrales = document.getElementById('list_cuatrimestral');
    const camposCuatrimestrales1 = document.getElementById('metaCuatriNormal1');
    const camposCuatrimestrales2 = document.getElementById('metaCuatriNormal2');
    const camposCuatrimestrales3 = document.getElementById('metaCuatriNormal3');
    const camposCuatrimestralesTiempo1 = document.getElementById('metaCuatriTiempo1');
    const camposCuatrimestralesTiempo2 = document.getElementById('metaCuatriTiempo2');
    const camposCuatrimestralesTiempo3 = document.getElementById('metaCuatriTiempo3');
    const camposSemestrales = document.getElementById('list_semestral');
    const camposSemestrales1 = document.getElementById('metaSemNormal1');
    const camposSemestrales2 = document.getElementById('metaSemNormal2');
    const camposSemestralesTiempo1 = document.getElementById('metaSemTiempo1');
    const camposSemestralesTiempo2 = document.getElementById('metaSemTiempo2');
    const camposAnual = document.getElementById('list_anual');
    const camposAnual1 = document.getElementById('metaANormal1');
    const camposAnualTiempo1 = document.getElementById('metaATiempo1');
    const meta = document.getElementById('meta');
    const metaHora = document.getElementById('meta_h');
    const metaMinuto = document.getElementById('meta_m');
    const metaSegundo = document.getElementById('meta_s');
    const metaNormal = document.getElementById('metaNormal');
    const metaTiempo = document.getElementById('metaTiempo');
    const errorTipoResultado = document.getElementById('error_tipo_resultado');
    errorTipoResultado.style.display = 'none';

    if(editarFrecuencia === 1){
        frecuenciaSeleccionada.disabled = true;  
    }else{
        frecuenciaSeleccionada.disabled = false;  
    }
      

    function validarTipoResultado() {
        if (tipoResultadoSeleccionado.value === '') {
            alert("Es importante seleccionar si las metas son acumulativas o periódicas.");
            errorTipoResultado.style.display = 'block';
            frecuenciaSeleccionada.disabled = true;
            meta.value = '';
            metaHora.value = '';
            metaMinuto.value = '';
            metaSegundo.value = '';
        } else {
            errorTipoResultado.style.display = 'none';
            frecuenciaSeleccionada.disabled = false;

        }
    }

    tipoResultadoSeleccionado.addEventListener('change', function () {
        validarTipoResultado();
        calcularMeta();
    });


    unidadMedidaSeleccionada.addEventListener('change', function () {
        metaNormal.style.display = 'none';
        metaTiempo.style.display = 'none';
        metaHora.style.display = 'none';
        metaMinuto.style.display = 'none';
        metaSegundo.style.display = 'none';

        if (this.value === '4') {
            metaNormal.style.display = 'none';
            metaTiempo.style.display = 'block';
            metaHora.style.display = 'block';
            metaMinuto.style.display = 'block';
            metaSegundo.style.display = 'block';
        } else {
            metaNormal.style.display = 'block';
            metaTiempo.style.display = 'none';
            metaHora.style.display = 'none';
            metaMinuto.style.display = 'none';
            metaSegundo.style.display = 'none';
        }
        manejarCamposVisibilidad();
        calcularMeta();
    });

    frecuenciaSeleccionada.addEventListener('change', function () {
        meta.value = 0;
        manejarCamposVisibilidad();
        calcularMeta();
    });

    function manejarCamposVisibilidad() {
        camposMensuales.style.display = 'none';
        camposMensualesTiempo.style.display = 'none';
        camposBimestrales.style.display = 'none';
        camposTrimestrales.style.display = 'none';
        camposCuatrimestrales.style.display = 'none';
        camposSemestrales.style.display = 'none';
        camposAnual.style.display = 'none';
        camposAnual1.style.display = 'none';
        camposAnualTiempo1.style.display = 'none';
        camposCuatrimestrales1.style.display = 'none';
        camposCuatrimestrales2.style.display = 'none';
        camposCuatrimestrales3.style.display = 'none';
        camposCuatrimestralesTiempo1.style.display = 'none';
        camposCuatrimestralesTiempo2.style.display = 'none';
        camposCuatrimestralesTiempo3.style.display = 'none';
        camposSemestrales1.style.display = 'none';
        camposSemestrales2.style.display = 'none';
        camposSemestralesTiempo1.style.display = 'none';
        camposSemestralesTiempo2.style.display = 'none';        

        if (frecuenciaSeleccionada.value === '1') { // Frecuencia mensual
            if (unidadMedidaSeleccionada.value === '4') {
                camposMensualesTiempo.style.display = 'block';
                camposMensuales.style.display = 'none';
            } else {
                camposMensuales.style.display = 'block';
                camposMensualesTiempo.style.display = 'none';
            }
        } else if (frecuenciaSeleccionada.value === '2') { // Frecuencia bimestral
            camposBimestrales.style.display = 'block';
        } else if (frecuenciaSeleccionada.value === '3') { // Frecuencia trimestral
            camposTrimestrales.style.display = 'block';
        } else if (frecuenciaSeleccionada.value === '4') { // Frecuencia semestral
            camposSemestrales.style.display = 'block';
            if (unidadMedidaSeleccionada.value === '4') {
                camposSemestrales1.style.display = 'none';
                camposSemestrales2.style.display = 'none';
                camposSemestralesTiempo1.style.display = 'block';
                camposSemestralesTiempo2.style.display = 'block';
                
            } else {
                camposSemestrales1.style.display = 'block';
                camposSemestrales2.style.display = 'block';
                camposSemestralesTiempo1.style.display = 'none';
                camposSemestralesTiempo2.style.display = 'none';                
            }
        } else if (frecuenciaSeleccionada.value === '6') { // Frecuencia cuatrimestral            
            camposCuatrimestrales.style.display = 'block';
            if (unidadMedidaSeleccionada.value === '4') {
                camposCuatrimestrales1.style.display = 'none';
                camposCuatrimestrales2.style.display = 'none';
                camposCuatrimestrales3.style.display = 'none';
                camposCuatrimestralesTiempo1.style.display = 'block';
                camposCuatrimestralesTiempo2.style.display = 'block';
                camposCuatrimestralesTiempo3.style.display = 'block';
            } else {
                camposCuatrimestrales1.style.display = 'block';
                camposCuatrimestrales2.style.display = 'block';
                camposCuatrimestrales3.style.display = 'block';
                camposCuatrimestralesTiempo1.style.display = 'none';
                camposCuatrimestralesTiempo2.style.display = 'none';
                camposCuatrimestralesTiempo3.style.display = 'none';
            }
        } else if (frecuenciaSeleccionada.value === '5') { // Frecuencia anual
            camposAnual.style.display = 'block';
            if (unidadMedidaSeleccionada.value === '4') {
                camposAnualTiempo1.style.display = 'block';
                camposAnual1.style.display = 'none';
            } else {
                camposAnual1.style.display = 'block';
                camposAnualTiempo1.style.display = 'none';                
            }
        }
    }

    function calcularMeta() {
        let suma = 0;
        let campos = [];
        let camposH = [];
        let camposM = [];
        let camposS = [];
        let totalHoras = 0, totalMinutos = 0, totalSegundos = 0;

        // Verificamos si se ha seleccionado el tipo de resultado
        if (tipoResultadoSeleccionado.value === '') {
            errorTipoResultado.style.display = 'block'; // Mostramos el mensaje de error
            meta.value = ''; // No calculamos la meta hasta que se seleccione el tipo de resultado
            metaHora.value = '';
            metaMinuto.value = '';
            metaSegundo.value = '';
            return; // Terminamos la función aquí si no hay tipo de resultado seleccionado
        } else {
            errorTipoResultado.style.display = 'none'; // Ocultamos el mensaje de error
        }

        // Si la frecuencia es anual, solo tomamos el valor del campo anual
        if (frecuenciaSeleccionada.value === '5') {
             // Solo un campo de texto
            if (unidadMedidaSeleccionada.value === '4') {
                camposH = document.querySelectorAll('.campoAnualH');
                camposM = document.querySelectorAll('.campoAnualM');
                camposS = document.querySelectorAll('.campoAnualS');
            } else {
                campos = document.querySelectorAll('.campoAnual');
            }
        } else if (frecuenciaSeleccionada.value === '1') {
            if (unidadMedidaSeleccionada.value === '4') {
                camposH = document.querySelectorAll('.campoMensualH');
                camposM = document.querySelectorAll('.campoMensualM');
                camposS = document.querySelectorAll('.campoMensualS');
            } else {
                campos = document.querySelectorAll('.campoMensual');
            }

        } else if (frecuenciaSeleccionada.value === '2') {
            if (unidadMedidaSeleccionada.value === '4') {
                camposH = document.querySelectorAll('.campoBimestralH');
                camposM = document.querySelectorAll('.campoBimestralM');
                camposS = document.querySelectorAll('.campoBimestralS');
            } else {
                campos = document.querySelectorAll('.campoBimestral');
            }
            
        } else if (frecuenciaSeleccionada.value === '3') {
            if (unidadMedidaSeleccionada.value === '4') {
                camposH = document.querySelectorAll('.campoTrimestralH');
                camposM = document.querySelectorAll('.campoTrimestralM');
                camposS = document.querySelectorAll('.campoTrimestralS');
            } else {
                campos = document.querySelectorAll('.campoTrimestral');
            }
        } else if (frecuenciaSeleccionada.value === '4') {
            
            if (unidadMedidaSeleccionada.value === '4') {
                camposH = document.querySelectorAll('.campoSemestralH');
                camposM = document.querySelectorAll('.campoSemestralM');
                camposS = document.querySelectorAll('.campoSemestralS');
            } else {
                campos = document.querySelectorAll('.campoSemestral');
            }
        } else if (frecuenciaSeleccionada.value === '6') {
            if (unidadMedidaSeleccionada.value === '4') {
                camposH = document.querySelectorAll('.campoCuatrimestralH');
                camposM = document.querySelectorAll('.campoCuatrimestralM');
                camposS = document.querySelectorAll('.campoCuatrimestralS');
            } else {
                campos = document.querySelectorAll('.campoCuatrimestral');
            }
            
        }

        if (unidadMedidaSeleccionada.value === '4') {
            // Inicializamos los totales de horas, minutos y segundos
            totalHoras = 0;
            totalMinutos = 0;
            totalSegundos = 0;

            // Calculamos la meta para horas, minutos y segundos, dependiendo del tipo de resultado
            if (tipoResultadoSeleccionado.value === '1') { // Promedio
                let cantidadCamposH = 0, cantidadCamposM = 0, cantidadCamposS = 0;

                camposH.forEach(function (campoH) {
                    if (campoH.value !== '') {
                        totalHoras += parseInt(campoH.value) || 0;
                        cantidadCamposH++;
                    }
                });

                camposM.forEach(function (campoM) {
                    if (campoM.value !== '') {
                        totalMinutos += parseInt(campoM.value) || 0;
                        cantidadCamposM++;
                    }
                });

                camposS.forEach(function (campoS) {
                    if (campoS.value !== '') {
                        totalSegundos += parseInt(campoS.value) || 0;
                        cantidadCamposS++;
                    }
                });

                metaHora.value = (cantidadCamposH > 0) ? Math.round(totalHoras / cantidadCamposH) : '';
                metaMinuto.value = (cantidadCamposM > 0) ? Math.round(totalMinutos / cantidadCamposM) : '';
                metaSegundo.value = (cantidadCamposS > 0) ? Math.round(totalSegundos / cantidadCamposS) : '';
            } else if (tipoResultadoSeleccionado.value === '3') { // Último valor
                let ultimoValorH = null, ultimoValorM = null, ultimoValorS = null;

                camposH.forEach(function (campoH) {
                    if (campoH.value !== '') {
                        ultimoValorH = campoH.value;
                    }
                });

                camposM.forEach(function (campoM) {
                    if (campoM.value !== '') {
                        ultimoValorM = campoM.value;
                    }
                });

                camposS.forEach(function (campoS) {
                    if (campoS.value !== '') {
                        ultimoValorS = campoS.value;
                    }
                });

                metaHora.value = ultimoValorH !== null ? ultimoValorH : '';
                metaMinuto.value = ultimoValorM !== null ? ultimoValorM : '';
                metaSegundo.value = ultimoValorS !== null ? ultimoValorS : '';
            }
        } else {
            if (tipoResultadoSeleccionado.value === '1') {
                let total = 0;
                let cantidadCampos = 0;

                campos.forEach(function (campo) {
                    if (campo.value !== '') {
                        total += parseFloat(campo.value) || 0;
                        cantidadCampos++;
                    }
                });

                if (cantidadCampos > 0) {
                    let resultado = total / cantidadCampos;
                    if (Number.isInteger(resultado)) {
                        meta.value = resultado;
                    } else {
                        meta.value = resultado.toFixed(2);
                    }
                } else {
                    meta.value = '';
                }
            } else if (tipoResultadoSeleccionado.value === '3') {
                let ultimoValor = null;


                campos.forEach(function (campo) {
                    if (campo.value !== '') {
                        ultimoValor = campo.value;
                    }
                });


                if (ultimoValor !== null) {
                    meta.value = ultimoValor;
                } else {
                    meta.value = '';
                }
            }
        }
    }


    document.querySelectorAll('input[type="text"]').forEach(function (input) {
        input.addEventListener('input', function () {
            calcularMeta();
        });
    });
});








var activar = false;

function AgregarNuevo() {
    html = $("#referencia_nuevo").html();
    $("#registros_sub").append(html);
}

function Eliminar(elemt) {
    $(elemt).parent().parent().next().remove();
    $(elemt).parent().parent().remove();

}

function VerSubObjetivos(accion) {

    $("#meta").val('');
    $("#meta_h").val('');
    $("#meta_m").val('');
    $("#meta_s").val('');

    $(".dinamico").hide();

    if (accion == 1) {
        $("#unidad_medida option:selected").each(function () {
            unidad = $(this).val();
            if (unidad == 4) {
                $("#lista_mes_padre_horas").show();
                $("#metaTiempo").show();
                $("#metaNormal").hide();
                $("#mensual_h").addClass('mes_per_h');
                $("#mensual_m").addClass('mes_per_m');
                $("#mensual_s").addClass('mes_per_s');
                $("#mensual").removeClass('mes_per');
            } else {
                $("#lista_mes_padre").show();
                $("#metaNormal").show();
                $("#metaTiempo").hide();
                $("#mensual").addClass('mes_per');
                $("#mensual_h").removeClass('mes_per_h');
                $("#mensual_m").removeClass('mes_per_m');
                $("#mensual_s").removeClass('mes_per_s');
            }
        });
        $("#list_cuatrimestral").hide();
        $("#list_trimestral").hide();
        $("#list_semestral").hide();
        $("#list_anual").hide();
        $("#list_bimestral").hide();
        $('#ponderado').prop('readonly', false);
        $("#anio1").removeClass('mes_per');
        $("#anio_h").removeClass('mes_per_h');
        $("#anio_m").removeClass('mes_per_m');
        $("#anio_s").removeClass('mes_per_s');
        $("#mesSem").removeClass('mes_per');
        $("#mesSem_h").removeClass('mes_per_h');
        $("#mesSem_m").removeClass('mes_per_m');
        $("#mesSem_s").removeClass('mes_per_s');
        $("#mesCuatri").removeClass('mes_per');
        $("#mesCuatri_h").removeClass('mes_per_h');
        $("#mesCuatri_m").removeClass('mes_per_m');
        $("#mesCuatri_s").removeClass('mes_per_s');
        $("#mesTri").removeClass('mes_per');
        $("#mesTri_h").removeClass('mes_per_h');
        $("#mesTri_m").removeClass('mes_per_m');
        $("#mesTri_s").removeClass('mes_per_s');
        $("#mesbi").removeClass('mes_perB');
        $("#mesbi_h").removeClass('mes_perB_h');
        $("#mesbi_m").removeClass('mes_perB_m');
        $("#mesbi_s").removeClass('mes_perB_s');
        $("#meta").val('');
    }

    if (accion == 2) {
        $("#list_bimestral").show();
        $("#unidad_medida option:selected").each(function () {
            unidad = $(this).val();
            if (unidad == 4) {
                $("#metaBiTiempo1").show();
                $("#metaBiTiempo2").show();
                $("#metaBiTiempo3").show();
                $("#metaBiTiempo4").show();
                $("#metaBiTiempo5").show();
                $("#metaBiTiempo6").show();
                $("#metaBiNormal1").hide();
                $("#metaBiNormal2").hide();
                $("#metaBiNormal3").hide();
                $("#metaBiNormal4").hide();
                $("#metaBiNormal5").hide();
                $("#metaBiNormal6").hide();
                $("#metaTiempo").show();
                $("#metaNormal").hide();
                $("#mesbi_h").addClass('mes_per_h');
                $("#mesbi_m").addClass('mes_per_m');
                $("#mesbi_s").addClass('mes_per_s');
                $("#mesbi").removeClass('mes_per');
            } else {
                $("#metaBiNormal1").show();
                $("#metaBiNormal2").show();
                $("#metaBiNormal3").show();
                $("#metaBiNormal4").show();
                $("#metaBiNormal5").show();
                $("#metaBiNormal6").show();
                $("#metaBiTiempo1").hide();
                $("#metaBiTiempo2").hide();
                $("#metaBiTiempo3").hide();
                $("#metaBiTiempo4").hide();
                $("#metaBiTiempo5").hide();
                $("#metaBiTiempo6").hide();
                $("#metaNormal").show();
                $("#metaTiempo").hide();
                $("#mesbi").addClass('mes_per');
                $("#mesbi_h").removeClass('mes_per_h');
                $("#mesbi_m").removeClass('mes_per_m');
                $("#mesbi_s").removeClass('mes_per_s');
            }
        });
        $("#list_cuatrimestral").hide();
        $("#list_trimestral").hide();
        $("#lista_mes_padre").hide();
        $("#list_semestral").hide();
        $("#list_anual").hide();
        $('#ponderado').prop('readonly', false);
        $("#mesTri").removeClass('mes_per');
        $("#mesTri_h").removeClass('mes_per_h');
        $("#mesTri_m").removeClass('mes_per_m');
        $("#mesTri_s").removeClass('mes_per_s');
        $("#anio1").removeClass('mes_per');
        $("#anio_h").removeClass('mes_per_h');
        $("#anio_m").removeClass('mes_per_m');
        $("#anio_s").removeClass('mes_per_s');
        $("#mesSem").removeClass('mes_per');
        $("#mesSem_h").removeClass('mes_per_h');
        $("#mesSem_m").removeClass('mes_per_m');
        $("#mesSem_s").removeClass('mes_per_s');
        $("#mensual").removeClass('mes_per');
        $("#mensual_h").removeClass('mes_per_h');
        $("#mensual_m").removeClass('mes_per_m');
        $("#mensual_s").removeClass('mes_per_s');
        $("#mesCuatri").removeClass('mes_per');
        $("#mesCuatri_h").removeClass('mes_per_h');
        $("#mesCuatri_m").removeClass('mes_per_m');
        $("#mesCuatri_s").removeClass('mes_per_s');
        $("#meta").val('');
    }

    if (accion == 3) {

        $("#list_trimestral").show();
        $("#unidad_medida option:selected").each(function () {
            unidad = $(this).val();
            if (unidad == 4) {
                $("#metaTriTiempo1").show();
                $("#metaTriTiempo2").show();
                $("#metaTriTiempo3").show();
                $("#metaTriTiempo4").show();
                $("#metaTriNormal1").hide();
                $("#metaTriNormal2").hide();
                $("#metaTriNormal3").hide();
                $("#metaTriNormal4").hide();
                $("#metaTiempo").show();
                $("#metaNormal").hide();
                $("#mesTri_h").addClass('mes_per_h');
                $("#mesTri_m").addClass('mes_per_m');
                $("#mesTri_s").addClass('mes_per_s');
                $("#mesTri").removeClass('mes_per');
            } else {
                $("#metaTriNormal1").show();
                $("#metaTriNormal2").show();
                $("#metaTriNormal3").show();
                $("#metaTriNormal4").show();
                $("#metaTriTiempo1").hide();
                $("#metaTriTiempo2").hide();
                $("#metaTriTiempo3").hide();
                $("#metaTriTiempo4").hide();
                $("#metaNormal").show();
                $("#metaTiempo").hide();
                $("#mesTri").addClass('mes_per');
                $("#mesTri_h").removeClass('mes_per_h');
                $("#mesTri_m").removeClass('mes_per_m');
                $("#mesTri_s").removeClass('mes_per_s');
            }
        });
        $("#list_cuatrimestral").hide();
        $("#lista_mes_padre").hide();
        $("#list_semestral").hide();
        $("#list_anual").hide();
        $("#list_bimestral").hide();
        $('#ponderado').prop('readonly', false);
        $("#anio1").removeClass('mes_per');
        $("#anio_h").removeClass('mes_per_h');
        $("#anio_m").removeClass('mes_per_m');
        $("#anio_s").removeClass('mes_per_s');
        $("#mesSem").removeClass('mes_per');
        $("#mesSem_h").removeClass('mes_per_h');
        $("#mesSem_m").removeClass('mes_per_m');
        $("#mesSem_s").removeClass('mes_per_s');
        $("#mensual").removeClass('mes_per');
        $("#mensual_h").removeClass('mes_per_h');
        $("#mensual_m").removeClass('mes_per_m');
        $("#mensual_s").removeClass('mes_per_s');
        $("#mesbi").removeClass('mes_per');
        $("#mesbi_h").removeClass('mes_per_h');
        $("#mesbi_m").removeClass('mes_per_m');
        $("#mesbi_s").removeClass('mes_per_s');
        $("#mesCuatri").removeClass('mes_per');
        $("#mesCuatri_h").removeClass('mes_per_h');
        $("#mesCuatri_m").removeClass('mes_per_m');
        $("#mesCuatri_s").removeClass('mes_per_s');
        $("#meta").val('');
    }
    if (accion == 4) {
        $("#list_semestral").show();
        $("#unidad_medida option:selected").each(function () {
            unidad = $(this).val();
            if (unidad == 4) {
                $("#metaSemTiempo1").show();
                $("#metaSemTiempo2").show();
                $("#metaSemNormal1").hide();
                $("#metaSemNormal2").hide();
                $("#metaTiempo").show();
                $("#metaNormal").hide();
                $("#mesSem_h").addClass('mes_per_h');
                $("#mesSem_m").addClass('mes_per_m');
                $("#mesSem_s").addClass('mes_per_s');
                $("#mesSem").removeClass('mes_per');
            } else {
                $("#metaSemNormal1").show();
                $("#metaSemNormal2").show();
                $("#metaSemTiempo1").hide();
                $("#metaSemTiempo2").hide();
                $("#metaNormal").show();
                $("#metaTiempo").hide();
                $("#mesSem").addClass('mes_per');
                $("#mesSem_h").removeClass('mes_per_h');
                $("#mesSem_m").removeClass('mes_per_m');
                $("#mesSem_s").removeClass('mes_per_s');
            }
        });
        $("#list_cuatrimestral").hide();
        $("#lista_mes_padre").hide();
        $("#list_trimestral").hide();
        $("#list_anual").hide();
        $("#list_bimestral").hide();
        $('#ponderado').prop('readonly', true);
        $("#anio1").removeClass('mes_per');
        $("#anio_h").removeClass('mes_per_h');
        $("#anio_m").removeClass('mes_per_m');
        $("#anio_s").removeClass('mes_per_s');
        $("#mesTri").removeClass('mes_per');
        $("#mesTri_h").removeClass('mes_per_h');
        $("#mesTri_m").removeClass('mes_per_m');
        $("#mesTri_s").removeClass('mes_per_s');
        $("#mensual").removeClass('mes_per');
        $("#mensual_h").removeClass('mes_per_h');
        $("#mensual_m").removeClass('mes_per_m');
        $("#mensual_s").removeClass('mes_per_s');
        $("#mesbi").removeClass('mes_per');
        $("#mesbi_h").removeClass('mes_per_h');
        $("#mesbi_m").removeClass('mes_per_m');
        $("#mesbi_s").removeClass('mes_per_s');
        $("#mesCuatri").removeClass('mes_per');
        $("#mesCuatri_h").removeClass('mes_per_h');
        $("#mesCuatri_m").removeClass('mes_per_m');
        $("#mesCuatri_s").removeClass('mes_per_s');
        $("#meta").val('');
    }
    if (accion == 5) {
        $("#list_anual").show();
        $("#unidad_medida option:selected").each(function () {
            unidad = $(this).val();
            if (unidad == 4) {
                $("#metaATiempo").show();
                $("#metaANormal").hide();
                $("#metaTiempo").show();
                $("#metaNormal").hide();
                $("#anio_h").addClass('mes_per_h');
                $("#anio_m").addClass('mes_per_m');
                $("#anio_s").addClass('mes_per_s');
                $("#anio1").removeClass('mes_per');
            } else {
                $("#metaANormal").show();
                $("#metaATiempo").hide();
                $("#metaNormal").show();
                $("#metaTiempo").hide();
                $("#anio1").addClass('mes_per');
                $("#anio_h").removeClass('mes_per_h');
                $("#anio_m").removeClass('mes_per_m');
                $("#anio_s").removeClass('mes_per_s');
            }
        });
        $("#list_cuatrimestral").hide();
        $("#list_semestral").hide();
        $("#lista_mes_padre").hide();
        $("#list_trimestral").hide();
        $("#list_bimestral").hide();
        $("#mesSem").removeClass('mes_per');
        $("#mesSem_h").removeClass('mes_per_h');
        $("#mesSem_m").removeClass('mes_per_m');
        $("#mesSem_s").removeClass('mes_per_s');
        $('#ponderado').prop('readonly', true);
        $("#mesTri").removeClass('mes_per');
        $("#mesTri_h").removeClass('mes_per_h');
        $("#mesTri_m").removeClass('mes_per_m');
        $("#mesTri_s").removeClass('mes_per_s');
        $("#mensual").removeClass('mes_per');
        $("#mensual_h").removeClass('mes_per_h');
        $("#mensual_m").removeClass('mes_per_m');
        $("#mensual_s").removeClass('mes_per_s');
        $("#mesbi").removeClass('mes_per');
        $("#mesbi_h").removeClass('mes_per_h');
        $("#mesbi_m").removeClass('mes_per_m');
        $("#mesbi_s").removeClass('mes_per_s');
        $("#mesCuatri").removeClass('mes_per');
        $("#mesCuatri_h").removeClass('mes_per_h');
        $("#mesCuatri_m").removeClass('mes_per_m');
        $("#mesCuatri_s").removeClass('mes_per_s');
        $("#meta").val('');
    }
    if (accion == 6) {

        $("#list_cuatrimestral").show();
        $("#unidad_medida option:selected").each(function () {
            unidad = $(this).val();
            if (unidad == 4) {
                $("#metaCuatriTiempo1").show();
                $("#metaCuatriTiempo2").show();
                $("#metaCuatriTiempo3").show();
                $("#metaCuatriNormal1").hide();
                $("#metaCuatriNormal2").hide();
                $("#metaCuatriNormal3").hide();
                $("#metaTiempo").show();
                $("#metaNormal").hide();
                $("#mesCuatri_h").addClass('mes_per_h');
                $("#mesCuatri_m").addClass('mes_per_m');
                $("#mesCuatri_s").addClass('mes_per_s');
                $("#mesCuatri").removeClass('mes_per');
            } else {
                $("#metaCuatriNormal1").show();
                $("#metaCuatriNormal2").show();
                $("#metaCuatriNormal3").show();
                $("#metaCuatriTiempo1").hide();
                $("#metaCuatriTiempo2").hide();
                $("#metaCuatriTiempo3").hide();
                $("#metaNormal").show();
                $("#metaTiempo").hide();
                $("#mesCuatri").addClass('mes_per');
                $("#mesCuatri_h").removeClass('mes_per_h');
                $("#mesCuatri_m").removeClass('mes_per_m');
                $("#mesCuatri_s").removeClass('mes_per_s');
            }
        });
        $("#list_trimestral").hide();
        $("#lista_mes_padre").hide();
        $("#list_semestral").hide();
        $("#list_anual").hide();
        $("#list_bimestral").hide();
        $('#ponderado').prop('readonly', false);
        $("#anio1").removeClass('mes_per');
        $("#anio_h").removeClass('mes_per_h');
        $("#anio_m").removeClass('mes_per_m');
        $("#anio_s").removeClass('mes_per_s');
        $("#mesSem").removeClass('mes_per');
        $("#mesSem_h").removeClass('mes_per_h');
        $("#mesSem_m").removeClass('mes_per_m');
        $("#mesSem_s").removeClass('mes_per_s');
        $("#mensual").removeClass('mes_per');
        $("#mensual_h").removeClass('mes_per_h');
        $("#mensual_m").removeClass('mes_per_m');
        $("#mensual_s").removeClass('mes_per_s');
        $("#mesbi").removeClass('mes_per');
        $("#mesbi_h").removeClass('mes_per_h');
        $("#mesbi_m").removeClass('mes_per_m');
        $("#mesbi_s").removeClass('mes_per_s');
        $("#mesTri").removeClass('mes_per');
        $("#mesTri_h").removeClass('mes_per_h');
        $("#mesTri_m").removeClass('mes_per_m');
        $("#mesTri_s").removeClass('mes_per_s');
        $("#meta").val('');
    }
}

function Nuevo_Grupo(elem, nivel) {
    grupo = MatrizGrupo();
    $(elem).after(grupo);
}

function MatrizGrupo(id_nivel) {
    itemg = '';
    itemg += '<div style="margin-bottom:8px">';
    itemg += '<button type="button" class="btn btn-danger btn-sm" onclick="Borrar_Item(this)" style="position: absolute; right: 10px; margin-top: 14px;">	<i class="fa fa-times"></i></button>';

    itemg += '<input type="hidden" name="id_actividad[]" value="">';
    itemg += '<textarea class="form-control" name="actividad[]" required placeholder="Descripción del Indicador..." style=" width:47%; display:inline-table;"></textarea>';
    itemg += '<textarea class="form-control" name="indicador[]" required placeholder="Fuente(s)" style=" width:47%; display:inline-table;"></textarea>';
    itemg += '</div> ';
    return itemg;
}

function Borrar_Item(elem) {
    $(elem).parent().remove();
}

function ValidarPeridicidad() {

    cantidad_meses = 0;
    $(".mes_per").each(function (index) {
        if ($(this).val() > 0) {
            cantidad_meses++;
        }
    });

    meses = 0;
    tipo = $("#tipo").val();
    //periodicidad = $("#periodicidad").val();
    periodicidad = 3;
    meses = 3;
    cantidad_meses = 3;

    // console.log(meses);
    // console.log(cantidad_meses);


    if (cantidad_meses == meses) {
        console.log("meses correctos");
        return true;
    } else {
        $("#cont_modal_general").html('Debe asignar un seguimiento a los meses segun la periodicidad. Total ' + meses + ' meses');
        $("#modal_general").modal('show');
        return false;
    }
}

function AdicionarAMetas(elemt) {
    periodicidad = $("#periodicidad").val();
    cantidad_meses = 3;

    count_meses = 0;
    sumas_meses_meta = '';
    sumas_meses_meta_sub = '';
    $(".mes_subo").each(function (index) {
        if ($(this).val() > 0) {
            numero = parseFloat($(this).val());
            sumas_meses_meta += numero;
        }
    });

    inputs = $(elemt).parent().parent().find(".mes_subo ");
    inputs.each(function (index) {
        if ($(this).val() > 0) {
            numero = parseFloat($(this).val());
            sumas_meses_meta_sub += numero;
            count_meses++;
        }
    });

    if (count_meses == cantidad_meses) {
        $(elemt).parent().parent().css({
            "background-color": "#ffffff"
        });
    } else {
        $(elemt).parent().parent().css({
            "background-color": "#ffcbcb"
        });
        //alert("debes completar información de por lo menos "+cantidad_meses+" meses.");
    }
    div = $(elemt).parent().parent().parent().parent().parent().parent().prev();
    //div = div.filter(".meta_sub").css("background-color", "yellow");
    div = div.find(".meta_subO");

    $("#meta").val(sumas_meses_meta);
    div.val(sumas_meses_meta_sub);

}

function AdicionarAMetasPor(elemt) {
    acumulativo = $("#tipo_resultado").val();
    frecuencia = $("#frecuencia").val();
    if (!acumulativo) {
        alert("Es importante seleccionar si las metas son acumulativas o periódicas.");
        $(elemt).val(0);
        return;
    }

    let sumas_meses_meta = 0;
    let contador = 0;
    let valor_ultimo = 0;
    let inputs = $(".mes_per");

    if (frecuencia == 2) {
        inputs = $(".mes_perB");
    }

    console.log(inputs);
    inputs.each(function (index) {
        let valor = $(this).val();

        if (parseFloat(valor) > 0) {
            let numero = parseFloat(valor);
            sumas_meses_meta += numero;
            valor_ultimo = numero;
            contador++;
        }
    });

    // console.log("Contador de inputs con valores mayores a cero:", contador);

    if (acumulativo == 1 && contador > 0) {
        sumas_meses_meta = sumas_meses_meta / contador;
    }
    if (acumulativo == 3 && contador > 0) {
        sumas_meses_meta = valor_ultimo;
    }

    let redondeado = Math.round(sumas_meses_meta * 100) / 100;
    console.log(redondeado);
    $("#meta").val(redondeado);

}

function AdicionarAMetasPorHora(elemt) {
    let acumulativo = $("#tipo_resultado").val();
    if (!acumulativo) {
        alert("Es importante seleccionar si las metas son acumulativas o periódicas.");
        $(elemt).val(0);
        return;
    }

    let sumas_meses_meta = 0;
    let contador = 0;
    let valor_ultimo = 0;

    let inputs = $(".mes_per_h");

    inputs.each(function () {
        let valor = $(this).val().trim();
        if (valor === "00") {
            valor = 0;
        }

        let numero = parseFloat(valor);
        if (!isNaN(numero) && numero > 0) {
            sumas_meses_meta += numero;
            valor_ultimo = numero;
            contador++;
        }
    });

    if (contador === 0) {
        $("#meta_h").val(0);
        return;
    }

    if (acumulativo == 1 && contador > 0) {
        sumas_meses_meta = sumas_meses_meta / contador;
    } else if (acumulativo == 3 && contador > 0) {
        sumas_meses_meta = valor_ultimo;
    }


    let redondeado = Math.round(sumas_meses_meta);
    $("#meta_h").val(redondeado);
}

function AdicionarAMetasPorMinuto(elemt) {
    let acumulativo = $("#tipo_resultado").val();
    if (!acumulativo) {
        alert("Es importante seleccionar si las metas son acumulativas o periódicas.");
        $(elemt).val(0);
        return;
    }

    let sumas_meses_meta = 0;
    let contador = 0;
    let valor_ultimo = 0;

    let inputs = $(".mes_per_m");

    inputs.each(function () {
        let valor = $(this).val().trim();
        if (valor === "00") {
            valor = 0;
        }

        let numero = parseFloat(valor);
        if (!isNaN(numero) && numero > 0) {
            sumas_meses_meta += numero;
            valor_ultimo = numero;
            contador++;
        }
    });

    if (contador === 0) {
        $("#meta_m").val(0);
        return;
    }

    if (acumulativo == 1 && contador > 0) {
        sumas_meses_meta = sumas_meses_meta / contador;
    } else if (acumulativo == 3 && contador > 0) {
        sumas_meses_meta = valor_ultimo;
    }


    let redondeado = Math.round(sumas_meses_meta);

    $("#meta_m").val(redondeado);
}

function AdicionarAMetasPorSegundo(elemt) {
    let acumulativo = $("#tipo_resultado").val();
    if (!acumulativo) {
        alert("Es importante seleccionar si las metas son acumulativas o periódicas.");
        $(elemt).val(0);
        return;
    }

    let sumas_meses_meta = 0;
    let contador = 0;
    let valor_ultimo = 0;

    let inputs = $(".mes_per_s");

    inputs.each(function () {
        let valor = $(this).val().trim();
        if (valor === "00") {
            valor = 0;
        }

        let numero = parseFloat(valor);
        if (!isNaN(numero) && numero > 0) {
            sumas_meses_meta += numero;
            valor_ultimo = numero;
            contador++;
        }
    });

    if (contador === 0) {
        $("#meta_s").val(0);
        return;
    }

    if (acumulativo == 1 && contador > 0) {
        sumas_meses_meta = sumas_meses_meta / contador;
    } else if (acumulativo == 3 && contador > 0) {
        sumas_meses_meta = valor_ultimo;
    }


    let redondeado = Math.round(sumas_meses_meta);

    $("#meta_s").val(redondeado);
}

function AdicionarAPonderado() {
    sumas_meses_meta = 0;
    sumas_meses_pond = 0;
    $(".mes_ponderado").each(function (index) {
        if ($(this).val() > 0) {
            numero = parseFloat($(this).val());
            sumas_meses_pond += numero;
        }
    });
    $("#ponderado").val(sumas_meses_pond);

}

function ValidarPorcentajes() {
    permirit_enviar = true;
    form = $("#form_objetivos_lista :input");
    form.each(function () {

        if ($(this)[0].type == 'select-one') {
            if ($(this)[0].value == "") {
                permirit_enviar = false;
                console.log($(this)[0].value);
                console.log("Sin datos");
            }
        }

    });
    if (permirit_enviar == true) {
        $("#form_emviar_aprobacion").submit();
    } else {
        alert("Recuerde que debe seleccionar todos los desplegables para poder enviar la aprobación.");
    }
}

$(function () {
    $(".numeros").keydown(function (event) {
        //alert(event.keyCode);
        if ((event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105) && event.keyCode !== 190 && event.keyCode !== 110 && event.keyCode !== 8 && event.keyCode !== 9 && event.keyCode !== 109 && event.keyCode !== 189 && event.keyCode !== 46) {
            return false;
        }
    });
});

function SoloNumeros(evt) {
    // code is the decimal ASCII representation of the pressed key.
    var code = (evt.which) ? evt.which : evt.keyCode;

    if (code == 8) { // backspace.
        return true;
    } else if (code >= 48 && code <= 57) { // is a number.
        return true;
    } else { // other keys.
        return false;
    }
}

function PrevenirDefault(evt) {
    return false;
}

function NumerosDecimales(elemet) {
    elemet.value = elemet.value.replace(/[^0-9,.]/g, '').replace(/,/g, '.');
}

$('.decimales').on('input', function () {
    console.log("ingreso");
    this.value = this.value.replace(/[^0-9,.]/g, '').replace(/,/g, '.');
});

function select_meta() {
    $("#unidad_medida option:selected").each(function () {
        unidad = $(this).val();
        switch (unidad) {
            case '1':
                $('#meta').attr('placeholder', 'Solo numero entero');
                document.getElementById('meta').removeAttribute('onkeypress');
                document.getElementById('meta').setAttribute('type', 'text');
                break;
            case '2':
                $('#meta').attr('placeholder', 'Porcentaje con el punto como separador de decimales');
                document.getElementById('meta').setAttribute('onkeypress', 'return filterFloat(event,this);');
                document.getElementById('meta').setAttribute('type', 'text');
                break;
            case '3':
                $('#meta').attr('placeholder', 'Numero entero sin separador');
                document.getElementById('meta').setAttribute('onkeypress', 'return SoloNumeros(event,this)');
                document.getElementById('meta').setAttribute('type', 'text');
                break;
            case '4':
                $('#meta').attr('placeholder', 'Cantidad de horas en hh:mm:ss');
                document.getElementById('meta').removeAttribute('onkeypress');
                document.getElementById('meta').setAttribute('type', 'text');
                break;
            default:
                $('#meta').attr('placeholder', '');
                break;

        }

    });

}

function filterFloat(evt, input) {
    // Backspace = 8, Enter = 13, ‘0′ = 48, ‘9′ = 57, ‘.’ = 46, ‘-’ = 43
    var key = window.Event ? evt.which : evt.keyCode;
    var chark = String.fromCharCode(key);
    var tempValue = input.value + chark;
    var isNumber = (key >= 48 && key <= 57);
    var isSpecial = (key == 8 || key == 13 || key == 0 || key == 46);
    if (isNumber || isSpecial) {
        return filter(tempValue);
    }

    return false;

}

function filter(__val__) {
    var preg = /^([0-9]+\.?[0-9]{0,2})$/;
    return (preg.test(__val__) === true);
}