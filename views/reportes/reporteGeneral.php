<style>
    .card,
    .card-body,
    .card-header,
    .card-footer {
        background-color: #FFFFFF !important;
    }

    #reporteGeneral tbody tr {
        background-color: white !important;
        color: black !important;
    }
</style>
<div class="container-fluid">
    <!-- Título -->
    <div class="card">
        <div class="card-header">
            <h4 class="mb-0"><i class="fas fa-chart-pie" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;Relación de OKRs y KPIs por Colaborador</h4>
        </div>
    </div>
    <div class="card-body">
        <!-- Formulario de filtros para tabla -->
        <div class="row mb-4">
            <form action="" method="post">
                <div class="col-md-4">
                    <label for="vicepresidencia" class="form-label">Vicepresidencia</label>
                    <select name="vicepresidencia" id="vicepresidencia" class="form-control multiples_responsables">
                        <option value="" selected="selected" disabled="disabled">Seleccione</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="nivelJerarquico" class="form-label">Nivel jerárquico</label>
                    <select class="form-control multiples_responsables" id="nivelJerarquico" name="nivelJerarquico">
                        <option selected disabled>Seleccione</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <!-- Bóton de filtrar -->
                    <div class="d-flex align-items-center w-100">
                        <button id="btnFiltrar" type="button" class="btn btn-success btn-block" style="margin-top: 1.7rem;">Filtrar</button>
                        <div id="loaderv2" style="display:none; vertical-align: middle; margin-left: 8px;margin-top: 1.7rem;">
                            <div class="spinner-border spinner-border-sm text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="card-footer">
        <div class="table-responsive">
            <table border="1" id="reporteGeneral" class="table table-bordered table-striped table-hover" style="width:100%;">
                <thead class="table-dark text-center">
                    <tr>
                        <th>#</th>
                        <th>Doc. Evaluado</th>
                        <th>Nombre</th>
                        <th>Vicepresidencia</th>
                        <th>Área</th>
                        <th>Unidad organizativa</th>
                        <th>Nivel jerárquico</th>
                        <th>Nivel general</th>
                        <th>Cargo</th>
                        <th>Número total OKRs asignados</th>
                        <th>Proceso OKRs individual</th>
                        <!-- <th>Ver Detalle</th> -->
                        <th>Número total KPIs asignados</th>
                        <!-- <th>Proceso KPIs individual</th> -->
                        <!-- <th>Ver Detalle</th> -->
                    </tr>
                </thead>
                <tbody class="tabla_lista"></tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.multiples_responsables').select2();
        $("#buscador").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $(".tabla_lista tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
        /** Cargar Vicepresidencias */
        let controller = '<?php echo $url; ?>app/controllers/Empresa/';
        //Cargar datos de Vicepresidencias
        $.ajax({
            url: controller + "VicepresidenciasController.php",
            method: 'post',
            success: function(data) {
                let select = $('#vicepresidencia');
                select.html('<option value="">Seleccione</option>');

                data.forEach(function(vp) {
                    let selected = '<?php echo isset($_POST["vicepresidencia"]) ? $_POST["vicepresidencia"] : ""; ?>';
                    let isSelected = selected == vp.id ? 'selected' : '';
                    select.append(`<option value="${vp.id}" ${isSelected}>${vp.nombre}</option>`);
                });

                // Reestablecer el valor seleccionado
                let savedVP = localStorage.getItem('vicepresidencia');
                if (savedVP) {
                    select.val(savedVP).trigger('change');
                }
            },
            error: function() {
                console.error('No se puedieron cargar las vicepresidencias');
            }
        });

        $('#vicepresidencia').on('change', function() {
            localStorage.setItem('vicepresidencia', $(this).val());
        });

        /**Cargar nivel Jerárquico */
        let rutaNiveles = '<?php echo $url; ?>app/controllers/Empresa/';

        $.ajax({
            url: rutaNiveles + "NivelJerarquicoController.php",
            method: 'post',
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // 👈 Necesario
            },
            data: {
                action:'getNiveles',
                id_empresa:'<?php echo $_SESSION["id_empresa"]; ?>',
            },
            success: function(data) {
                let select = $('#nivelJerarquico');
                select.html('<option value="">Seleccione</option>');

                data.forEach(function(nivel) {
                    let selected = '<?php echo isset($_POST["nivelJerarquico"]) ? $_POST["nivelJerarquico"] : ""; ?>';
                    let isSelected = selected == nivel.indNivel ? 'selected' : '';
                    select.append(`<option value="${nivel.indNivel}" ${isSelected}>${nivel.NivelJerarquico}</option>`);
                });

                // Reestablecer el valor seleccionado
                let savedVP = localStorage.getItem('nivelJerarquico');
                if (savedVP) {
                    select.val(savedVP).trigger('change');
                }
            },
            error: function() {
                console.error('No se puedieron cargar los nivel Jerárquicos');
            }
        });

        let ruta = '<?php echo $url; ?>app/controllers/Empleados/';
        /**Cargar información tabla */
        let escalasGlobal = [];
        let tabla = $("#reporteGeneral").DataTable({
            destroy: true,
            ajax: {
                url: ruta + "PanelController.php",
                type: "POST",
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // 👈 Necesario
                },
                data: function(d) {
                    d.action = 'getReporteGeneral';
                    d.id_empresa = '<?php echo $_SESSION["id_empresa"]; ?>';
                    d.url = '<?php echo $url; ?>';
                    d.ciclo = '<?php echo $_SESSION['ciclo']; ?>';
                    d.anio_ciclo = '<?php echo $_SESSION['anio_ciclo']; ?>';
                    d.identidad = '<?php echo $_SESSION["id_user"]; ?>';
                    d.vicepresidencia = $('#vicepresidencia').val();
                    d.nivelJerarquico = $('#nivelJerarquico').val();
                    d.rol = '<?php echo $_SESSION['role_plataforma']; ?>';
                },
                beforeSend: function() {
                    $('#loader').show();
                },
                complete: function() {
                    $('#loader').hide();
                },
                error: function() {
                    $('#loader').hide();
                    alert("Ocurrió un error al cargar los datos.");
                },
                dataSrc: function(json) {
                    escalasGlobal = json.escalas; //Guardamos la escala de colores
                    return json.data;
                }
            },
            stripeClasses: [], // Desactiva filas alternadas
            serverSide: false,
            paging: true,
            searching: true,
            ordering: true,
            responsive: true,
            pageLength: 10,
            columns: [{
                    data: 'contador'
                },
                {
                    data: 'documento'
                },
                {
                    data: 'nombre'
                },
                {
                    data: 'vicepresidencia'
                },
                {
                    data: 'area'
                },
                {
                    data: 'unidadOrganizativa'
                },
                {
                    data: 'nivelJerarquico'
                },
                {
                    data: 'nivelGeneral'
                },
                {
                    data: 'cargo'
                },
                {
                    data: 'totalOKR'
                },
                {
                    data: 'progresoPorcent',
                    render: function(data, type, row) {
                        let porcentaje = parseFloat(data).toFixed(2);
                        let bgColor = '';
                        let textColor = 'text-dark';
                        /**Escalas dinámicas por medio del dato escalas que recibe del controlador. */
                        //Validar escalas
                        if (!escalasGlobal || escalasGlobal.length === 0) {
                            return `${porcentaje}%`;
                        }

                        let escala = escalasGlobal[0];

                        /**Convertir a float para evitar errores por strings */
                        let unoMin = parseFloat(escala.minEscalaUno);
                        let unoMax = parseFloat(escala.maxEscalaUno);
                        let dosMin = parseFloat(escala.minEscalaDos);
                        let dosMax = parseFloat(escala.maxEscalaDos);
                        let tresMin = parseFloat(escala.minEscalaTres);
                        let tresMax = parseFloat(escala.maxEscalaTres);
                        let cuatroMin = parseFloat(escala.minEscalaCuatro);
                        let cuatroMax = parseFloat(escala.maxEscalaCuatro);
                        if (porcentaje === 0) {
                            bgColor = '';
                        } else if (porcentaje >= unoMin && porcentaje < dosMin) {
                            bgColor = '#FF0000';
                            // textColor = 'text-white';
                        } else if (porcentaje >= dosMin && porcentaje < tresMin) {
                            bgColor = '#FFF200';
                        } else if (porcentaje >= tresMin && porcentaje < cuatroMin) {
                            bgColor = '#95FA03';
                        } else if (porcentaje >= cuatroMin && porcentaje <= 100) {
                            bgColor = '#0DF205';
                        } else if (porcentaje > 100) {
                            bgColor = '#00D30A';
                            textColor = 'text-white';
                        }
                        return `
                        <div class="position-relative" style="height:22px;">
                            <div class="progress" style="height:100%;">
                                <div class="progress-bar" role="progressbar"
                                    style="width:${porcentaje}%; background-color:${bgColor}!important; z-index:1;"
                                    aria-valuenow="${porcentaje}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <span class="position-absolute w-100 text-center ${textColor}"
                                style="top:0; left:0; height:22px; line-height:22px; font-size:0.9rem; z-index:2;">
                                ${porcentaje}%
                            </span>
                        </div>`;
                    },
                    createdCell: function(td, cellData, rowData, row, col) {
                        $(td).css({
                            'vertical-align': 'middle'
                        });
                    }
                },
                {
                    data: 'totalKPI'
                },
                // {
                //     data: 'progresoPorcentKPI',
                //     render: function(data, type, row) {
                //         let porcentaje = parseFloat(data).toFixed(2);
                //         let bgColor = '';
                //         let textColor = 'text-dark';
                //         /**Escalas dinámicas por medio del dato escalas que recibe del controlador. */
                //         //Validar escalas
                //         if (!escalasGlobal || escalasGlobal.length === 0) {
                //             return `${porcentaje}%`;
                //         }

                //         let escala = escalasGlobal[0];

                //         /**Convertir a float para evitar errores por strings */
                //         let unoMin = parseFloat(escala.minEscalaUno);
                //         let unoMax = parseFloat(escala.maxEscalaUno);
                //         let dosMin = parseFloat(escala.minEscalaDos);
                //         let dosMax = parseFloat(escala.maxEscalaDos);
                //         let tresMin = parseFloat(escala.minEscalaTres);
                //         let tresMax = parseFloat(escala.maxEscalaTres);
                //         let cuatroMin = parseFloat(escala.minEscalaCuatro);
                //         let cuatroMax = parseFloat(escala.maxEscalaCuatro);
                //         if (porcentaje === 0) {
                //             bgColor = '';
                //         } else if (porcentaje >= unoMin && porcentaje < dosMin) {
                //             bgColor = '#FF0000';
                //             // textColor = 'text-white';
                //         } else if (porcentaje >= dosMin && porcentaje < tresMin) {
                //             bgColor = '#FFF200';
                //         } else if (porcentaje >= tresMin && porcentaje < cuatroMin) {
                //             bgColor = '#95FA03';
                //         } else if (porcentaje >= cuatroMin && porcentaje <= 100) {
                //             bgColor = '#0DF205';
                //         } else if (porcentaje > 100) {
                //             bgColor = '#00D30A';
                //             textColor = 'text-white';
                //         }
                //         return `
                //         <div class="position-relative" style="height:22px;">
                //             <div class="progress" style="height:100%;">
                //                 <div class="progress-bar" role="progressbar"
                //                     style="width:${porcentaje}%; background-color:${bgColor}!important; z-index:1;"
                //                     aria-valuenow="${porcentaje}" aria-valuemin="0" aria-valuemax="100">
                //                 </div>
                //             </div>
                //             <span class="position-absolute w-100 text-center ${textColor}"
                //                 style="top:0; left:0; height:22px; line-height:22px; font-size:0.9rem; z-index:2;">
                //                 ${porcentaje}%
                //             </span>
                //         </div>`;
                //     },
                //     createdCell: function(td, cellData, rowData, row, col) {
                //         $(td).css({
                //             'vertical-align': 'middle'
                //         });
                //     }
                // }
            ],
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros.",
                info: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                infoEmpty: "Mostrando registros del 0 al 0 de 0 registros",
                infoFiltered: "(filtrado de un total de _MAX_ registros)",
                loadingRecords: '<div id="loader" style="display: block; text-align: center; margin-top: 20px;">' +
                    '<div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>' +
                    '<p style="margin-top: 10px;">Cargando datos, por favor espera...</p>' +
                    '</div>',
                zeroRecords: "No se encontraron resultados",
                emptyTable: "Ningún dato disponible en esta tabla",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último"
                }
            },
            buttons: [{
                extend: 'collection',
                text: 'Exportar',
                buttons: [
                    'copy',
                    'excel',
                    'csv',
                    {
                        extend: 'pdfHtml5',
                        text: 'PDF',
                        orientation: 'landscape',
                        pageSize: 'LEGAL'
                    },
                    {
                        extend: 'print',
                        customize: function(win) {
                            $(win.document.body)
                                .css('font-size', '10pt');

                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size', 'inherit');
                        }
                    }
                ]
            }],
            dom: '<"top"Bfl>rt<"bottom"ip><"clear">'
        });

        // Evento de Filtrar datos por selects
        $('#btnFiltrar').on('click', function() {
            $('#loaderv2').show(); // mostrar loader al iniciar
            tabla.ajax.reload(null, false); // recarga tabla

            // Ocultar loader cuando termine la petición ajax
            tabla.on('xhr', function() {
                $('#loaderv2').hide();
            });
        });
    });
</script>