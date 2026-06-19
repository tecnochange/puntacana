<div class="accordion-item mb-2" style="border:none;">

    <?php
    //dd($kpi);
     //NOMBRE DEL TIPO DE KPI
    $tipo_kpi_nombre = null;
    foreach($Array_tipo_kpi_PC1 as $tipo_kpi_text){
        if($tipo_kpi_text[0] == $kpi["tipo_kpi"])
            $tipo_kpi_nombre = $tipo_kpi_text[1];
    }
    ?>

    <!-- HEADER -->
    <div class="px-3 py-2 border" style="border-radius: 0.50rem;">
        <div class="row d-flex align-items-center ">
            <div class="col-md-5">
                <div class="fw-bold">
                    <?php echo $kpi["indicador"]; ?>
                </div>
                <small class="text-muted">
                    Tipo KPI: <?php echo $tipo_kpi_nombre; ?> <br>
                    Área Macro: <?php echo $kpi["vicepresidencia_nombre"]; ?> <br>
                    Área Proceso: <?php echo $kpi["area_nombre"]; ?>
                </small>
                <div>
                    <i class="bi bi-person-circle"></i> Rol <?= $kpi["role_nombre"]; ?>
                </div>
            </div>
            <div class="col-md-1">
                <?php echo $kpi["meta"]; ?>
            </div>
            <div class="col-md-1">
                Seguimiento
            </div>
            <div class="col-md-3">
                <div class="text-center" style="width: 100%;">
                    Progreso
                    <div class="progress">
                        <!-- AVANCE DEL KPI -->
                        <?php $Kpi_avance = $ClassOkrs->AvanceKPI($kpi["id"]); ?>
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $Kpi_avance; ?>%; background-color:<?= EscalaColor($Kpi_avance); ?> !important;" aria-valuenow="<?= $Kpi_avance; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <b><?= number_format($Kpi_avance, 0); ?>%</b>
                </div>
            </div>
            <div class="col-md-1 text-center">
                <a href="?pg=kpis/crear_kpi&id_kpi=<?= $kpi["id"]; ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-pencil-square"></i>
                </a>
                <a href="?pg=kpis/crear_kpi&id_kpi=<?= $kpi["id"]; ?>" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-person-plus"></i>
                </a>
            </div>
            <div class="col-md-1">
                <!-- FLECHA (ÚNICO TRIGGER) -->
                <button
                    class="btn btn-sm btn-outline-secondary float-end"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#kpi_<?php echo $kpi["id"]; ?>"
                    aria-expanded="false"
                    aria-controls="kpi_<?php echo $kpi["id"]; ?>">
                    <i class="bi bi-caret-down"></i>
                </button>
            </div>

        </div>
    </div>

    <!-- CONTENIDO COLAPSABLE -->
    <div id="kpi_<?php echo $kpi["id"]; ?>" class="accordion-collapse collapse" data-bs-parent="#accordionKpis">
        <div class="accordion-body">

            <!-- FICHAS -->
            <div class="row mb-2">
                <div class="col-md-2 text-center">
                    <b>Responsables</b>
                </div>
                <div class="col-md-2 text-center">
                    <b>Objetivo del Indicador</b>
                    <p><?= $kpi["objetivo_indicador"]; ?></p>
                </div>
                <div class="col-md-2 text-center">
                    <b>Fórmula de Cálculo</b>
                    <p><?= $kpi["formula"]; ?></p>
                </div>
                <div class="col-md-2 text-center">
                    <b>Tipo de Resultado</b>
                    <p><?= $kpi["tipo_resultado"] == "1" ? "Estratégico": "Táctico"; ?></p>
                </div>
                <div class="col-md-2 text-center">
                    <b>Tipo de Cálculo</b>
                    <?php
                    foreach($Array_Tendencia_KPIS as $tendencia){
                        if($tendencia[0] == $kpi["tipo_calculo"])
                            $tipo_calculo = $tendencia[1];
                        else if($tendencia[0] == $kpi["tipo_calculo"])
                            $tipo_calculo = $tendencia[1];
                        else if($tendencia[0] == $kpi["tipo_calculo"])
                            $tipo_calculo = $tendencia[1];
                    }
                    ?>
                    <p><?= $tipo_calculo; ?></p>
                </div>
                <div class="col-md-2 text-center">
                    <b>Unidad de Medida</b>
                    <?php
                    foreach($Array_Medicion_PC as $medicion){
                        if($medicion[0] == $kpi["unidad_medida"])
                            $unidad_medida = $tendencia[1];
                        else if($medicion[0] == $kpi["unidad_medida"])
                            $unidad_medida = $tendencia[1];
                        else if($medicion[0] == $kpi["unidad_medida"])
                            $unidad_medida = $tendencia[1];
                    }
                    ?>
                    <p><?= $unidad_medida; ?></p>
                </div>
            </div>

            <!-- TABLA -->
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-responsive">
                        <thead>
                            <th>Mes</th>
                            <th>Periodos</th>
                            <th>Total</th>
                        </thead>
                        <tbody>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ACCIONES -->
            <div class="row mb-3">
                <div class="col-md-12 d-flex justify-content-center">
                    <a href="#" class="btn btn-sm btn-outline-primary mx-1">
                        <i class="bi bi-floppy"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-secondary mx-1">
                        <i class="bi bi-chat-right-text"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-secondary mx-1">
                        <i class="bi bi-file-earmark"></i>
                    </a>
                    <a href="#" class="btn btn-sm btn-outline-warning mx-1">
                        Gestionar KPI
                    </a>
                </div>
            </div>

        </div>
    </div>

</div>