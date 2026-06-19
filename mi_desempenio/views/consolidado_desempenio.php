<style>
    .bg-goforagile {
        background-color: #59008e !important;
    }

    .bg-proceso {
        background-color: #f76d6b !important;
    }

    .bg-proceso1 {
        background-color: #7f38c2 !important;
    }

    .bg-proceso2 {
        background-color: #748eff !important;
    }

    .bg-proceso3 {
        background-color: #fcdb58 !important;
    }

    .bg-proceso4 {
        background-color: #59008e !important;
    }

    .bg-proceso5 {
        background-color: #5cdc53 !important;
    }

    .bg-proceso6 {
        background-color: #fb924e !important;
    }

    .bg-proceso7 {
        background-color: #64f456 !important;
    }

    .card,
    .card-header,
    .card-body {
        background-color: white !important;
    }
</style>
<?php
include("functions.php");
include("etiquetas.php");
//include("modal_firma.php");
// include("modals.php");
$hoy = date("Y-m-d H:i:s");
if ($_POST["guardar_firma"] != "") {

    $queryFirma = "UPDATE Competencias_Evaluaciones_New SET firma_aprobacion = '" . $_POST["firma"] . "', proceso_valoracion = 7, estado = 3, fecha_firma = '$hoy',update_at = '$hoy' WHERE id_Evaluado = " . $_SESSION["id_super_user_valentina"] . "";

    $accion = 'ACTUALIZAR';
    $descripcion = 'Actualización de firma de pdi por parte del colaborador ' . $_SESSION['nombre_super_valentina'];
    mysqli_query($connect_valoracion, $queryFirma);


    $auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
VALUES (" . $_SESSION["id_empresa_valentina"] . ", " . $_SESSION["id_super_user_valentina"] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
    // echo $auditoria;
    mysqli_query($connect_okrs, $auditoria);
    echo '<script> window.location.href = "' . $_SERVER['PHP_SELF'] . '";</script>';
}

if ($_POST["enviar_intervencion"] != "") {

    $queryRelacion = mysqli_query($connect_valoracion, "SELECT * FROM Relaciones_Laborales WHERE areas LIKE '%" . $_SESSION["area_valentina"] . "%' ");
    $dataRelacion = mysqli_fetch_array($queryRelacion);
    if (mysqli_num_rows($queryRelacion) == 0) {
        echo '<script> alert("No tiene asignado el responsable para Intervención de GH");</script>';
        echo '<script> window.location.href = "' . $_SERVER['PHP_SELF'] . '";</script>';
    } else {
        $evaluaciones = "";

        $queryEvaluaciones = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New WHERE id_evaluado = " . $_SESSION["id_super_user_valentina"] . " AND estado = 2 AND proceso_Valoracion = 4 ");
        while ($dataEvaluaciones = mysqli_fetch_array($queryEvaluaciones)) {
            $evaluaciones .= $dataEvaluaciones["id"] . ",";
        }

        if ($evaluaciones != "") {
            $evaluaciones = substr($evaluaciones, 0, -1);
        }

        $queryIntervencion = "INSERT INTO Intervencion_Gh (id_empresa,id_responsable,id_evaluado,evaluaciones,comentario,ciclo,estado,created_at) VALUES
(" . $_SESSION["id_empresa_valentina"] . "," . $dataRelacion["id_empleado"] . "," . $_SESSION["id_super_user_valentina"] . ",'$evaluaciones','" . $_POST["comentario"] . "'," . $_SESSION["anio_ciclo"] . ",1,'$hoy')";

        mysqli_query($connect_valoracion, $queryIntervencion);

        $queryEnvio = "UPDATE Competencias_Evaluaciones_New SET proceso_valoracion = 6, update_at = '$hoy' WHERE id_evaluado = " . $_SESSION["id_super_user_valentina"] . " AND estado = 2";
        // echo $queryEnvio;
        $accion = 'ACTUALIZAR';
        $descripcion = 'Actualización envio de Pdi a intervención GH para el colaborador ' . $colaborador["nombre"];
        mysqli_query($connect_valoracion, $queryEnvio);

        $auditoria = "INSERT INTO Auditoria_Okrs (id_empresa,id_empleado,accion,descripcion,tipo_okr,id_okr,id_kr,id_iniciativa,created_at)
VALUES (" . $_SESSION["id_empresa_valentina"] . ", " . $_SESSION["id_super_user_valentina"] . ",'$accion','$descripcion',0,0,0,0,'$hoy')";
        // echo $auditoria;
        mysqli_query($connect_okrs, $auditoria);
        // 

    }
    echo '<script> window.location.href = "' . $_SERVER['PHP_SELF'] . '";</script>';
}


if ($_POST["periodo_desempenio"] != "") {
    $_SESSION["periodo_desempenio"] = $_POST["periodo_desempenio"];
}
if ($_POST["periodo_desempenio"] == -1) {
    $_SESSION["periodo_desempenio"] = "";
}
$querySM34 = mysqli_query($connect_admin, "SELECT * FROM Submenu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa_valentina"] . " AND estado = 1 AND id_menu = 3 AND id_submenu = 19");
$dataSM34 = mysqli_fetch_array($querySM34);

$periodo = ((int)$_SESSION["periodo_desempenio"] - 1) . " - " . $_SESSION["periodo_desempenio"];

$cont = 1;

$okr = $competencia = $kpis = $puntuacionCompetencias = $numero_unico = 0;
$desempenioOKr = $desempenioKpi = $desempenioCompetencias = 0;
if ($_SESSION["periodo_desempenio"] == 2024 && $_SESSION["id_empresa_valentina"] == 1) {
    $query = mysqli_query($connect_valoracion, "SELECT * FROM Consolidado_Competencias WHERE id_empresa = 1 AND estado = 1 AND id_empleado = " . $_SESSION["id_super_user_valentina"] . "");
    $data = mysqli_fetch_array($query);
    $id_empleado = $data["id_empleado"];
    $nombrecolaborador = $data["nombre_empleado"];
    $vicepresidencia = $data["vicepresidencia"];
    $area = $data["area"];
    $unidadOrganizativa = $data["unidad_corporativa"];
    $nivelJerarquico = $data["nivel_jerarquico"];
    $cargo = $data["cargo"];
    $puntuacion = round($data["puntuacion_competencia"], 1);
    if (is_nan($puntuacion)) {
        $puntuacion = 0;
    }
    if ($puntuacion > 100) {
        $puntuacion = 100;
    }
} else {
    $query = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id_empresa = " . $_SESSION["id_empresa_valentina"] . " AND id = " . $_SESSION["id_super_user_valentina"] . " ");
    $data = mysqli_fetch_array($query);
    $id_empleado = $data["id"];
    $nombrecolaborador = $data["nombre"];
    $queryNJ = mysqli_query($connect_admin, "SELECT * FROM Nivel_Jerarquico WHERE id_empresa = '" . $_SESSION["id_empresa_valentina"] . "' AND id = '" . $data["nivel_jerarquico"] . "'");
    $dataNJ = mysqli_fetch_array($queryNJ);
    $nivelJerarquico = $dataNJ["nombre"];
    $queryCargo = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE id_empresa = '" . $_SESSION["id_empresa_valentina"] . "' AND id = '" . $data["id_cargo"] . "'");
    $dataCargo = mysqli_fetch_array($queryCargo);
    $cargo = $dataCargo["nombre"];
    $queryVP = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa_valentina"] . "' AND id = '" . $data["unidad_corporativa"] . "'");
    $dataVP = mysqli_fetch_array($queryVP);
    $vicepresidencia = $dataVP["nombre"];
    $queryArea = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id_empresa = '" . $_SESSION["id_empresa_valentina"] . "' AND id = '" . $data["area"] . "'");
    $dataArea = mysqli_fetch_array($queryArea);
    $area = $dataArea["nombre"];
    $queryEE = mysqli_query($connect_admin, "SELECT * FROM Estructura_Empresa WHERE id_empresa = '" . $_SESSION["id_empresa_valentina"] . "' AND id = '" . $data["unidad_organizativa"] . "'");
    $dataEE = mysqli_fetch_array($queryEE);
    $unidadOrganizativa = $dataEE["unidad_organizativa"];
}

$OKRS = OkrsConsolidadoCompetencia($_SESSION["id_super_user_valentina"], $_SESSION["id_empresa_valentina"], $connect_okrs);

$contador = $prueba = 0;
foreach ($OKRS as $value) {
    $resultados = PorOkrsReporteUsuarioDesempenio($connect_okrs, $value["id"], $_SESSION["id_super_user_valentina"], $filtro_claves);
    $contador = $contador + $resultados["promedio"];
    $prueba++;
}

$a_por = round(($contador / $prueba), 1);


if (is_nan($a_por) || is_infinite($a_por)) {
    $a_por = 0;
}
if ($a_por > 100) {
    $a_por = 100;
}
if ($_SESSION["periodo_desempenio"] == 2024 && $_SESSION["id_empresa_valentina"] == 1) {
    switch ($data["nivel_jerarquico"]) {
        case "Gerentes y Directores":
            $okr = ($a_por * 60) / 100;
            $competencia = ($data["puntuacion_competencia"] * 40) / 100;
            break;
        case "Vicepresidentes y Directores Ejecutivos":
            $okr = ($a_por * 70) / 100;
            $competencia = ($data["puntuacion_competencia"] * 30) / 100;
            break;
        case "Operativos y Técnicos":
            $okr = $a_por * 0;
            $competencia = $data["puntuacion_competencia"];
            break;
        case "Mandos Medios y Administrativos":
            $okr = ($a_por * 40) / 100;
            $competencia = ($data["puntuacion_competencia"] * 60) / 100;
            break;
        case "Presidencia":
            $okr = $a_por;
            $competencia = $data["puntuacion_competencia"];
            break;
    }


    if (is_nan($okr)) {
        $okr = 0;
    }

    if (is_nan($competencia)) {
        $competencia = 0;
    }

    $numero_unico = round(($okr + $competencia), 2);

    // $numero_unico = round($total);
} else {
    $puntuacion = 0;

    //     // COMPETENCIAS

    $datos_generales = ValidarEvaluacionesCompletas($id_empleado, $connect_valoracion, $connect_admin);
    $promedio_general = $datos_generales["promedio_general"];
    $puntuacion = ($promedio_general * 100) / 5;


    if (is_nan($puntuacion) || is_infinite($puntuacion)) {
        $puntuacion = 0;
    }

    if ($puntuacion > 100) {
        $puntuacion = 100;
    }

    //     // KPIS
    $queryKpis = mysqli_query($connect_kpis, "SELECT DISTINCT(K.Id) AS id, K.tipo_kpi, K.anio, K.area_proceso, K.subproceso, K.objetivo_sg, K.indicador, K.objetivo_indicador,
                        K.formula, K.resultado_anterior, K.unidad_medida, K.tipo_calculo, K.meta, K.frecuencia, K.tipo_resultado, K.obj_meses $mesConsultaI $mesConsultaF $periodoFill
                        FROM Kpis K 
                        INNER JOIN Kpis_Colaborador KC ON KC.id_kpi = K.id
                        INNER JOIN Frecuencia_Kpis FKP ON FKP.id_kpi = K.id
                        WHERE K.id_empresa = " . $_SESSION["id_empresa_valentina"] . " AND K.anio = " . $_SESSION['periodo_desempenio'] . " $complemento $filtro  AND KC.id_colaborador = '$id_empleado' ORDER BY K.indicador ASC");
    $contKpis = mysqli_num_rows($queryKpis);

    include("kpis.php");

    $avanceGeneral = $sumAvanceE / $contKpiE;

    if ($_SESSION["tipo_kpi_fill"] == "") {
        if ($contKpiE > 0 && $contKpiT > 0) {
            $queryPonderacion = mysqli_query($connect_kpis, "SELECT * FROM Ponderaciones WHERE id_empresa = " . $_SESSION["id_empresa_valentina"] . " AND estado = 1");
            $dataPonderacion = mysqli_fetch_array($queryPonderacion);
            $estrategicos = round(($sumAvanceE / $contKpiE) * ($dataPonderacion["estrategico"] / 100), 2);

            $tacticos = round(($sumAvanceT / $contKpiT) * ($dataPonderacion["tactico"] / 100), 2);

            if (is_nan($estrategicos) || is_infinite($estrategicos)) {
                $estrategicos = 0;
            }
            if (is_nan($tacticos) || is_infinite($tacticos)) {
                $tacticos = 0;
            }
            $avanceGeneral = $estrategicos + $tacticos;
        }
    }

    $kpis = $avanceGeneral;

    if (is_nan($kpis) || is_infinite($kpis)) {
        $kpis = 0;
    }

    if ($kpis > 100) {
        $kpis = 100;
    }

    $queryDesempenio1 = mysqli_query($connect_admin, "SELECT * FROM Ponderar_Desempenio WHERE id_empresa = '" . $_SESSION["id_empresa_valentina"] . "' AND anio = '" . $_SESSION["periodo_desempenio"] . "' AND nivel = '" . $data["nivel_jerarquico"] . "' AND estado = 1 ");

    if (mysqli_num_rows($queryDesempenio1) > 0) {
        $dataDesempenio1 = mysqli_fetch_array($queryDesempenio1);
        $desempenioOKr = ($a_por * $dataDesempenio1["mod_okrs"]) / 100;
        $desempenioKpi = ($kpis * $dataDesempenio1["mod_kpis"]) / 100;
        $desempenioCompetencias = ($puntuacion * $dataDesempenio1["mod_competencias"]) / 100;
    } else {
        $desempenioOKr = $a_por;
        $desempenioKpi = $kpis;
        $desempenioCompetencias = $puntuacion;
    }

    $numero_unico = round(($desempenioOKr + $desempenioKpi + $desempenioCompetencias), 2);
}


if (is_nan($numero_unico)) {
    $numero_unico = 0;
}
if ($numero_unico > 100) {
    $numero_unico_total = 100;
} else {
    $numero_unico_total = $numero_unico;
}

$escala = EscalaColor($a_por, 1, $connect_admin);
$escala1 = EscalaColor($puntuacion, 1, $connect_admin);
$escala2 = EscalaColor($numero_unico, 1, $connect_admin);
$escala3 = EscalaColor($kpis, 1, $connect_admin);
$color_bg = $escala['color_bg'];
$color_bg1 = $escala1['color_bg'];
$color_bg2 = $escala2['color_bg'];
$color_bg3 = $escala3['color_bg'];
$queryMA8 = mysqli_query($connect_admin, "SELECT * FROM Menu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa_valentina"] . " AND estado = 1 AND id_menu = 8");
$dataMA8 = mysqli_fetch_array($queryMA8);
$queryMA6 = mysqli_query($connect_admin, "SELECT * FROM Menu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa_valentina"] . " AND estado = 1 AND id_menu = 6");
$dataMA6 = mysqli_fetch_array($queryMA6);
$queryMA3 = mysqli_query($connect_admin, "SELECT * FROM Menu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa_valentina"] . " AND estado = 1 AND id_menu = 3");
$dataMA3 = mysqli_fetch_array($queryMA3);
$queryMA1 = mysqli_query($connect_admin, "SELECT * FROM Menu_Empresa WHERE id_empresa = " . $_SESSION["id_empresa_valentina"] . " AND estado = 1 AND id_menu = 1");
$dataMA1 = mysqli_fetch_array($queryMA1);
$estadoEvaluado = $estadoAuto = '';
$Array_Proceso_Valoracion = array(
    array("1", "Autoevaluación", "#7f38c2", "white"),
    array("2", "Evaluación del Lider", "#748eff", "white"),
    array("3", "Entrevista (PDI)", "#fcdb58", "black"),
    array("4", "Envio (PDI)", "#59008e", "white"),
    array("5", "Firmar Aprobación", "#5cdc53", "black"),
    array("6", "Intervención GH", "#fb924e", "white"),
    array("7", "PDI Aprobado y Cerrado", "#64f456", "black"),
);

$mostarModal = 0;

$queryAuto = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores
WHERE anio = '" . $_SESSION['anio_ciclo'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND id_empleado = '" . $_SESSION['id_super_user_valentina'] . "' AND tipo = 1 ");
$queryJefe = mysqli_query($connect_valoracion, "SELECT * FROM Evaluadores
WHERE anio = '" . $_SESSION['anio_ciclo'] . "' AND id_ciclo = '" . $_SESSION['ciclo'] . "' AND id_empleado = '" . $_SESSION['id_super_user_valentina'] . "' AND tipo != 1 ");
$dataJefe = mysqli_fetch_array($queryJefe);
if (mysqli_num_rows($queryAuto) > 0) {
    $modal = 1;
    $queryEval1 = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New
    WHERE id_empresa = '" . $_SESSION['id_empresa_valentina'] . "'
    AND id_ciclo = '" . $_SESSION['ciclo'] . "'
    AND id_evaluado = '" . $_SESSION['id_super_user_valentina'] . "'
    AND id_evaluador = '" . $_SESSION['id_super_user_valentina'] . "'
    AND anio = '" . $_SESSION["anio_ciclo"] . "'");
    if (mysqli_num_rows($queryEval1) > 0) {
        $dataEval1 = mysqli_fetch_array($queryEval1);
        if ($dataEval1["estado"] == 1) {
            $mostarModal = 1;
            $txt_estado = 'En Proceso de Autovaloración';
            $background = 'background-color: #6B21FF !important;';
            $mensaje = '¡Es hora de continuar su autovaloración! Complete su valoración para que su líder/supervisor pueda continuar con la valoración de sus competencias.';
            // $boton1 = '<a href="' . $url . '?pg=competencias_pc/evaluacion&id=' . $dataEval1["id"] . '&pos=1" class="btn btn-agile" style="' . $background . 'color: white !important;border-radius: 30px;">Continuar Autovaloración</a>';
        }

        if ($dataEval1["estado"] == 2) {
            $mostarModal = 1;
            $txt_estado = 'Autovaloración terminada';
            $mensaje = '¡Autovaloración enviada con éxito! Su líder/supervisor directo ahora está en proceso de realizar la valoración correspondiente.';
            $background = 'background-color: #007BFF !important;';
            // $boton1 = '<a href="' . $url . '?pg=competencias_pc/mi_informe" class="btn btn-primary" style="' . $background . 'color: white !important;border-radius: 30px;">Ver Estado de la Evaluación</a>';
            $queryEval2 = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New
            WHERE id_empresa = '" . $_SESSION['id_empresa_valentina'] . "'
            AND id_ciclo = '" . $_SESSION['ciclo'] . "'
            AND id_evaluado = '" . $_SESSION['id_super_user_valentina'] . "'
            AND id_evaluador = '" . $dataJefe['id_evaluador'] . "'
            AND anio = '" . $_SESSION["anio_ciclo"] . "'");
            if (mysqli_num_rows($queryEval2) > 0) {
                $dataEval2 = mysqli_fetch_array($queryEval2);
                if ($dataEval2["estado"] == 2 && $dataEval2["proceso_valoracion"] == 4) {
                    $mostarModal = 1;
                    $txt_estado = 'PDI Listo para Firma';
                    $mensaje = 'Su Plan de Desarrollo Individual (PDI) está listo para ser revisado y firmado. Puede firmar desde tu celular escaneando el siguiente código QR o ingresando desde su computadora.<hr>
                    Si no está de acuerdo con su Plan de Desarrollo Individual (PDI), puede solicitar la intervención del equipo de Relaciones Laborales para revisar el caso.';
                    $background1 = 'background-color: #28a745 !important;';
                    $background2 = 'background-color: #f4815e !important;';
                    $boton1 = '<button class="btn btn-success" style="' . $background1 . 'color: white !important;border-radius: 30px;" onClick="FirmaAprobacion(' . $_SESSION["id_super_user_valentina"] . ')">Firmar PDI Ahora</button>';
                    $boton2 = '<button class="btn btn-warning" style="' . $background2 . 'color: white !important;border-radius: 30px;" onClick="IntervencionGH(' . $_SESSION["id_super_user_valentina"] . ')">Solicitar Intervención</button>';
                }
            }
        }
    } else {
        $mostarModal = 1;
        $txt_estado = 'Su valoración está pendiente';
        $background = 'background-color: #6B21FF !important;';
        $mensaje = '¡Es hora de iniciar su autovaloración! Complete su valoración para que su líder/supervisor pueda continuar con la valoración de sus competencias.';
        // $boton1 = '<a href="' . $url . '?pg=competencias_pc/evaluacion&evaluado=' . $_SESSION['id_super_user_valentina'] . '&t=1&pos=1" class="btn btn-agile" style="' . $background . 'color: white !important;border-radius: 30px;">Iniciar Autovaloración</a>';
    }
} elseif (mysqli_num_rows($queryJefe) > 0) {
    $modal = 1;

    $queryEval2 = mysqli_query($connect_valoracion, "SELECT * FROM Competencias_Evaluaciones_New
    WHERE id_empresa = '" . $_SESSION['id_empresa_valentina'] . "'
    AND id_ciclo = '" . $_SESSION['ciclo'] . "'
    AND id_evaluado = '" . $_SESSION['id_super_user_valentina'] . "'
    AND id_evaluador = '" . $dataJefe['id_evaluador'] . "'
    AND anio = '" . $_SESSION["anio_ciclo"] . "'");
    if (mysqli_num_rows($queryEval2) > 0) {
        $dataEval2 = mysqli_fetch_array($queryEval2);
        if ($dataEval2["estado"] == 2 && $dataEval2["proceso_valoracion"] == 4) {
            $mostarModal = 1;
            $txt_estado = 'PDI Listo para Firma';
            $mensaje = 'Su Plan de Desarrollo Individual (PDI) está listo para ser revisado y firmado. Puede firmar desde tu celular escaneando el siguiente código QR o ingresando desde su computadora.<hr>
                    Si no está de acuerdo con su Plan de Desarrollo Individual (PDI), puede solicitar la intervención del equipo de Relaciones Laborales para revisar el caso.';
            $background1 = 'background-color: #28a745 !important;';
            $background2 = 'background-color: #f4815e !important;';
            $boton1 = '<button class="btn btn-success" style="' . $background1 . 'color: white !important;border-radius: 30px;" onClick="FirmaAprobacion(' . $_SESSION["id_user"] . ')">Firmar PDI Ahora</button>';
            $boton2 = '<button class="btn btn-warning" style="' . $background2 . 'color: white !important;border-radius: 30px;" onClick="IntervencionGH(' . $_SESSION["id_user"] . ')">Solicitar Intervención</button>';
        } else {
            $mostarModal = 1;
            $txt_estado = 'Valoración en proceso';
            $mensaje = 'Estamos a la espera de que su jefe directo complete el proceso de valoración de competencias. Le notificaremos cuando su valoración haya finalizado.';
            $background = 'background-color: #007BFF !important;';
            // $boton1 = '<a href="' . $url . '?pg=competencias_pc/mi_informe" class="btn btn-primary" style="' . $background . 'color: white !important;border-radius: 30px;">Ver Estado de la Evaluación</a>';
        }
    } else {
        $mostarModal = 1;
        $txt_estado = 'Valoración sin iniciar';
        $mensaje = 'Estamos a la espera de que su jefe directo complete el proceso de valoración de competencias. Le notificaremos cuando su valoración haya finalizado.';
        $background = 'background-color: #007BFF !important;';
    }
}

if ($mostarModal == 1) {
    include("modals.php");
}

if ($puntuacion === 0) {

?>


    <div class="modal fade" id="AlertaModalLabel" tabindex="-1" aria-labelledby="AlertaModalLabelLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h3 class="modal-title text-danger" id="AlertaModalLabel">NOTIFICACIÓN IMPORTANTE</h3>

                </div>
                <div class="modal-body" style="text-align:center;">
                    <h2>Proceso de Obtención del Número Único de Desempeño</h2><br>
                    <h4 style="color: black !important;">Les informamos que en estos momentos estamos trabajando en la obtención de su Número Único de Desempeño</h4>
                    <br>
                    <h4 style="color: black !important;">Les mantendremos informados sobre cualquier actualización y los pasos a seguir, si tienen alguna pregunta o requieren asistencia adicional, no duden en ponerse en contacto con nuestro equipo de Desarrollo Organizacional.</h4><br>
                    <h5 style="color: black !important;">Para validar esta información, por favor ingresar a la página de <a href="https://puntacana.goforagile.com/">GoForAgile</a> y en el modulo de desempeño, ingresar a Mi Desempeño.</h5><br>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            
            var AlertaModalLabel = new bootstrap.Modal(document.getElementById('AlertaModalLabel'), {

            });
            AlertaModalLabel.show();

            if (<?php echo $mostarModal; ?> == 1) {
                var modalAviso = new bootstrap.Modal(document.getElementById('modalAviso'), {
                    // backdrop: false // Deshabilita el backdrop
                });
                
                setTimeout(function() {
                    AlertaModalLabel.hide();
                    modalAviso.show();
                }, 500);
            }
        }
    </script>
<?php
} else { ?>
    <div class="modal fade" id="AlertaModalLabel_2" tabindex="-1" aria-labelledby="AlertaModalLabel_2Label" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h3 class="modal-title text-danger" id="AlertaModalLabel_2">NOTIFICACIÓN IMPORTANTE</h3>

                </div>
                <div class="modal-body" style="text-align:center;">
                    <h2 style="color: black !important;">Para validar esta información, por favor ingresar a la página de <a href="https://puntacana.goforagile.com/">GoForAgile</a> y en el modulo de desempeño, ingresar a Mi Desempeño.</h2>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.onload = function() {
            
            var AlertaModalLabel_2 = new bootstrap.Modal(document.getElementById('AlertaModalLabel_2'), {
                // backdrop: false // Deshabilita el backdrop
            });
            AlertaModalLabel_2.show();

            if (<?php echo $mostarModal; ?> == 1) {
                
                var modalAviso = new bootstrap.Modal(document.getElementById('modalAviso'), {
                    // backdrop: false // Deshabilita el backdrop
                });
                
                setTimeout(function() {
                    AlertaModalLabel_2.hide();
                    modalAviso.show();
                }, 1000);
            }

        }
    </script>
<?php } ?>
<div class="row">
    <div class="col-xl-12" style="color: #365189 !important;">
        <div class="card shadow h-100 py-2">
            <div class="card-header" style="border-bottom: none !important;">
                <form action="" method="post">
                    <div class="row">
                        <div class="col-md-9">
                            <h3><i class="fas fa-tasks" id="iconCabecera"></i>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo mb_strtoupper($dataSM34["nombre"]); ?> <?php echo $periodo; ?></h3>
                        </div>
                        <div class="col-md-2" style="text-align: right;">
                            <select class="form-control form-control-sm" name="periodo_desempenio">
                                <option value="-1">Filtrar por Periodo...</option>
                                <?php
                                foreach ($Array_Anio_Desempenio as $periodo) {
                                    if ($_SESSION["periodo_desempenio"] ==  $periodo[0]) {
                                        echo '<option value="' . $periodo[0] . '" selected>' . ((int)$periodo[1] - 1) . ' - ' . $periodo[1] . '</option>';
                                    } else {
                                        echo '<option value="' . $periodo[0] . '">' . ((int)$periodo[1] - 1) . ' - ' . $periodo[1] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-1" style="text-align: right;">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<br>
<div class="row">

    <div class="col-xl-2 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $_SESSION['nombre_super_valentina']; ?></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            <?php echo $etiquetaAdminVP; ?></div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $vicepresidencia; ?></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            <?php echo $etiquetaAdminArea; ?></div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $area; ?></div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            <?php echo $etiquetaAdminUO; ?></div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $unidadOrganizativa; ?></div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            <?php echo $etiquetaAdminNJ; ?></div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $nivelJerarquico; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            <?php echo $etiquetaAdminCargo; ?></div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $cargo; ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- <div class="row">
    <div class="col-md-12">
        <p>
            <button class="btn btn-primary" type="button" style="background-color: #6B21FF!important;" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                Ver Informe de valoración
            </button>
        </p>
        <div class="collapse" id="collapseExample">
            <div class="card card-body shadow h-100 py-2" >
                <?php //include("estado_pdi.php"); 
                ?>
            </div>
        </div>
    </div>

</div> -->
<hr>
<div class="row">
    <div class="col-md-12">
        <div class="card shadow h-100 py-2">
            <div class="card-header" style="border-bottom: none !important;">
                <div class="row">
                    <div class="col-md-12" style="text-align: center;">
                        <h1># Único de desempeño</h1>
                    </div>
                </div>
                <div class="progresos" data-bs-toggle="tooltip" align="center" style="background-color: #e9ecef;">
                    <h1 style="font-size: 3.5rem;color: black !important;font-weight: 700;"><?php echo $numero_unico; ?>%</h1>
                </div>
                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $numero_unico_total; ?>%;background-color: <?php echo $color_bg2; ?> !important;opacity: 0.3;z-index: 2;margin-top: -77px;height: 67px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                </div>
            </div>
            <br>
            <div class="card-body">
                <div class="row">
                    <?php if ($_SESSION["periodo_desempenio"] == 2024 && $_SESSION["id_empresa_valentina"] == 1) { ?>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body" style="align-self: center;">
                                    <div class="progreso-bar-container" style="--i:<?php echo round($a_por); ?>;--clr:<?php echo $color_bg; ?>">
                                        <div class="progreso-bar objetivo-okr">
                                            <progreso id="objetivo-okr" min="0" value="<?php echo round($a_por); ?>"></progreso>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer" style="text-align: center;background-color: white !important;">
                                    <h5>Resultado de mis <?php echo $dataMA8["nombre"]; ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body" style="align-self: center;">
                                    <div class="progreso-bar-container" style="--i:<?php echo $puntuacion; ?>;--clr:<?php echo $color_bg1; ?>">
                                        <div class="progreso-bar objetivo-okr">
                                            <progreso id="objetivo-okr" min="0" value="<?php echo $puntuacion; ?>"></progreso>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer" style="text-align: center;background-color: white !important;">
                                    <h5>Resultado de mis <?php echo $dataMA1["nombre"]; ?></h5>
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body" style="align-self: center;">
                                    <div class="progreso-bar-container" style="--i:<?php echo round($a_por); ?>;--clr:<?php echo $color_bg; ?>">
                                        <div class="progreso-bar objetivo-okr">
                                            <progreso id="objetivo-okr" min="0" value="<?php echo round($a_por); ?>"></progreso>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer" style="text-align: center;background-color: white !important;">
                                    <h5>Resultado de mis <?php echo $dataMA8["nombre"]; ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body" style="align-self: center;">
                                    <div class="progreso-bar-container" style="--i:<?php echo round($kpis); ?>;--clr:<?php echo $color_bg3; ?>">
                                        <div class="progreso-bar objetivo-okr">
                                            <progreso id="objetivo-okr" min="0" value="<?php echo round($kpis); ?>"></progreso>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer" style="text-align: center;background-color: white !important;">
                                    <h5>Resultado de mis <?php echo $dataMA6["nombre"]; ?></h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body" style="align-self: center;">
                                    <div class="progreso-bar-container" style="--i:<?php echo round($puntuacion); ?>;--clr:<?php echo $color_bg1; ?>">
                                        <div class="progreso-bar objetivo-okr">
                                            <progreso id="objetivo-okr" min="0" value="<?php echo round($puntuacion,2); ?>"></progreso>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer" style="text-align: center;background-color: white !important;">
                                    <h5>Resultado de mis <?php echo $dataMA1["nombre"]; ?></h5>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div>
</div>
<br>
<br>
<?php if ($_SESSION["role_plataforma_valentina"] == 2) {    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="background-color: white !important;border-bottom: none !important;">
                    <h4>Desempeño de mis evaluados</h4>
                </div>
                <div class="card-footer" style="background-color: white !important;">
                    <div class="table-responsive">
                        <table border="1" id="competencias" class="display table" style="width:100%">
                            <thead style="font-weight: 700;font-size: 16px;text-align:center;">
                                <th>No.</th>
                                <th>Documento</th>
                                <th>Nombre</th>
                                <th>Vicepresidencia</th>
                                <th>Área</th>
                                <th>Unidad Organizativa</th>
                                <th>Nivel Jerárquico</th>
                                <th>Cargo</th>
                                <th>Resultado <?php echo $dataMA8["nombre"]; ?></th>
                                <?php if ($_SESSION["periodo_desempenio"] > 2024) { ?>
                                    <th>Resultado <?php echo $dataMA6["nombre"]; ?></th>
                                <?php } ?>
                                <th>Resultado <?php echo $dataMA1["nombre"]; ?></th>
                                <th style="background-color: #007BFF;color: white !important;">Numero Único Desempeño</th>
                            </thead>
                            <tbody>
                                <?php
                                $filtro_kr = $filtro_claves = "";
                                $okr = $competencia = 0;
                                if ($_SESSION["periodo_desempenio"] == 2024 && $_SESSION["id_empresa_valentina"] == 1) {
                                    $query1 = mysqli_query($connect_valoracion, "SELECT * FROM Consolidado_Competencias WHERE id_empresa = 1 AND nombre_evaluador LIKE '%" . $_SESSION["nombre_super_valentina"] . "%'");
                                } else {
                                    $query1 = mysqli_query($connect_admin, "SELECT * FROM Lideres L INNER JOIN Empleados E ON E.id = L.id_empleado WHERE E.id_empresa = '" . $_SESSION["id_empresa_valentina"] . "' AND L.id_jefe = '" . $_SESSION['id_super_user_valentina'] . "';");
                                }
                                $okr = $competencia = $kpis = $puntuacionCompetencias = $numero_unico = 0;
                                $desempenioOKr = $desempenioKpi = $desempenioCompetencias = 0;
                                while ($data = mysqli_fetch_array($query1)) {
                                    if ($_SESSION["periodo_desempenio"] == 2024 && $_SESSION["id_empresa_valentina"] == 1) {
                                        $id_empleado = $data["id_empleado"];
                                    } else {
                                        $id_empleado = $data["id"];
                                    }
                                    $OKRS = OkrsConsolidadoCompetencia($id_empleado, 1, $connect_okrs, $filtro_kr);
                                    // $OKRS = OkrsReporteUsuarioConsolidado($data["id_empleado"], $data["id_empresa_valentina"], $connect_okrs, $filtro_kr);
                                    // print_r($OKRS);
                                    $contador = $prueba = 0;
                                    foreach ($OKRS as $value) {
                                        $resultados = PorOkrsReporteUsuarioDesempenio($connect_okrs, $value["id"], $id_empleado, $filtro_claves);
                                        $contador = $contador + $resultados["promedio"];
                                        $prueba++;
                                    }

                                    $a_por = round(($contador / $prueba), 1);
                                    if (is_nan($a_por)) {
                                        $a_por = 0;
                                    }

                                    if ($_SESSION["periodo_desempenio"] == 2024 && $_SESSION["id_empresa_valentina"] == 1) {
                                        $nombreColaborador = $data["nombre_empleado"];
                                        $nivelJerarquico = $data["nivel_jerarquico"];
                                        $cargo = $data["cargo"];
                                        $vicepresidencia = $data["vicepresidencia"];
                                        $area = $data["area"];
                                        $unidadCorporativa = $data["unidad_corporativa"];
                                        $puntuacionCompetencias = $data["puntuacion_competencia"];
                                        switch (utf8_encode($data["nivel_jerarquico"])) {
                                            case "Gerentes y Directores":
                                                $okr = ($a_por * 60) / 100;
                                                $competencia = ($data["puntuacion_competencia"] * 40) / 100;
                                                break;
                                            case "Vicepresidentes y Directores Ejecutivos":
                                                $okr = ($a_por * 70) / 100;
                                                $competencia = ($data["puntuacion_competencia"] * 30) / 100;
                                                break;
                                            case "Operativos y Técnicos":
                                                $okr = $a_por * 0;
                                                $competencia = $data["puntuacion_competencia"];
                                                break;
                                            case "Mandos Medios y Administrativos":
                                                $okr = ($a_por * 40) / 100;
                                                $competencia = ($data["puntuacion_competencia"] * 60) / 100;
                                                break;
                                            case "Presidencia":
                                                $okr = $a_por;
                                                $competencia = $data["puntuacion_competencia"];
                                                break;
                                        }
                                        $numero_unico = round(($okr + $competencia), 2);
                                    } else {
                                        $nombreColaborador = $data["nombre"];
                                        $queryNJ = mysqli_query($connect_admin, "SELECT * FROM Nivel_Jerarquico WHERE id_empresa = '" . $_SESSION["id_empresa_valentina"] . "' AND id = '" . $data["nivel_jerarquico"] . "'");
                                        $dataNJ = mysqli_fetch_array($queryNJ);
                                        $nivelJerarquico = $dataNJ["nombre"];
                                        $queryCargo = mysqli_query($connect_admin, "SELECT * FROM Cargos WHERE id_empresa = '" . $_SESSION["id_empresa_valentina"] . "' AND id = '" . $data["id_cargo"] . "'");
                                        $dataCargo = mysqli_fetch_array($queryCargo);
                                        $cargo = $dataCargo["nombre"];
                                        $queryVP = mysqli_query($connect_admin, "SELECT * FROM Vicepresidencia WHERE id_empresa = '" . $_SESSION["id_empresa_valentina"] . "' AND id = '" . $data["unidad_corporativa"] . "'");
                                        $dataVP = mysqli_fetch_array($queryVP);
                                        $vicepresidencia = $dataVP["nombre"];
                                        $queryArea = mysqli_query($connect_admin, "SELECT * FROM Areas WHERE id_empresa = '" . $_SESSION["id_empresa_valentina"] . "' AND id = '" . $data["area"] . "'");
                                        $dataArea = mysqli_fetch_array($queryArea);
                                        $area = $dataArea["nombre"];
                                        $queryEE = mysqli_query($connect_admin, "SELECT * FROM Estructura_Empresa WHERE id_empresa = '" . $_SESSION["id_empresa_valentina"] . "' AND id = '" . $data["unidad_organizativa"] . "'");
                                        $dataEE = mysqli_fetch_array($queryEE);
                                        $unidadCorporativa = $dataEE["unidad_organizativa"];
                                        $puntuacionCompetencias = 0;

                                        //COMPETENCIAS

                                        $datos_generales = ValidarEvaluacionesCompletas($id_empleado, $connect_valoracion, $connect_admin);
                                        $promedio_general = $datos_generales["promedio_general"];
                                        $puntuacionCompetencias = ($promedio_general * 100) / 4;

                                        if (is_nan($puntuacionCompetencias) || is_infinite($puntuacionCompetencias)) {
                                            $puntuacionCompetencias = 0;
                                        }

                                        if ($puntuacionCompetencias > 100) {
                                            $puntuacionCompetencias = 100;
                                        }

                                        // KPIS
                                        $queryKpis = mysqli_query($connect_kpis, "SELECT DISTINCT(K.Id) AS id, K.tipo_kpi, K.anio, K.area_proceso, K.subproceso, K.objetivo_sg, K.indicador, K.objetivo_indicador,
						K.formula, K.resultado_anterior, K.unidad_medida, K.tipo_calculo, K.meta, K.frecuencia, K.tipo_resultado, K.obj_meses $mesConsultaI $mesConsultaF $periodoFill
						FROM Kpis K 
						INNER JOIN Kpis_Colaborador KC ON KC.id_kpi = K.id
						INNER JOIN Frecuencia_Kpis FKP ON FKP.id_kpi = K.id
						WHERE K.id_empresa = " . $_SESSION['id_empresa_valentina'] . " AND K.anio = " . $_SESSION['periodo_desempenio'] . " $complemento $filtro  AND KC.id_colaborador = '$id_empleado' ORDER BY K.indicador ASC");
                                        $contKpis = mysqli_num_rows($queryKpis);

                                        include("views/desempenio_individual/kpis/kpis.php");

                                        $avanceGeneral = $sumAvanceE / $contKpiE;

                                        if ($_SESSION["tipo_kpi_fill"] == "") {
                                            if ($contKpiE > 0 && $contKpiT > 0) {
                                                $queryPonderacion = mysqli_query($connect_kpis, "SELECT * FROM Ponderaciones WHERE id_empresa = " . $_SESSION["id_empresa_valentina"] . " AND estado = 1");
                                                $dataPonderacion = mysqli_fetch_array($queryPonderacion);
                                                $estrategicos = round(($sumAvanceE / $contKpiE) * ($dataPonderacion["estrategico"] / 100), 2);

                                                $tacticos = round(($sumAvanceT / $contKpiT) * ($dataPonderacion["tactico"] / 100), 2);

                                                if (is_nan($estrategicos) || is_infinite($estrategicos)) {
                                                    $estrategicos = 0;
                                                }
                                                if (is_nan($tacticos) || is_infinite($tacticos)) {
                                                    $tacticos = 0;
                                                }
                                                $avanceGeneral = $estrategicos + $tacticos;
                                            }
                                        }

                                        $kpis = $avanceGeneral;

                                        if (is_nan($kpis) || is_infinite($kpis)) {
                                            $kpis = 0;
                                        }

                                        if ($kpis > 100) {
                                            $kpis = 100;
                                        }

                                        $queryDesempenio1 = mysqli_query($connect_admin, "SELECT * FROM Ponderar_Desempenio WHERE id_empresa = '" . $_SESSION["id_empresa_valentina"] . "' AND anio = '" . $_SESSION["periodo_desempenio"] . "' AND nivel = '" . $data["nivel_jerarquico"] . "' AND estado = 1 ");

                                        if (mysqli_num_rows($queryDesempenio1) > 0) {
                                            $dataDesempenio1 = mysqli_fetch_array($queryDesempenio1);
                                            $desempenioOKr = ($a_por * $dataDesempenio1["mod_okrs"]) / 100;
                                            $desempenioKpi = ($kpis * $dataDesempenio1["mod_kpis"]) / 100;
                                            $desempenioCompetencias = ($puntuacionCompetencias * $dataDesempenio1["mod_competencias"]) / 100;
                                        } else {
                                            $desempenioOKr = $a_por;
                                            $desempenioKpi = $kpis;
                                            $desempenioCompetencias = $puntuacionCompetencias;
                                        }

                                        $numero_unico = round(($desempenioOKr + $desempenioKpi + $desempenioCompetencias), 2);
                                    }

                                    $color_bg = "#FF0000";
                                    $color_text = "#000000";

                                    $escala_home = EscalaColor($numero_unico, $data['id_empresa'], $connect_admin);
                                    $color_text = $escala_home["color_text"];
                                    $color_bg = $escala_home["color_bg"];

                                    $escala = array();

                                    if ($numero_unico >= 0 && $numero_unico < 80) {
                                        $txt_subtitulo = "NO CUMPLE";
                                    }
                                    if ($numero_unico >= 80 && $numero_unico < 86) {
                                        $txt_subtitulo = "CUMPLE EXPECTATIVAS";
                                    }
                                    if ($numero_unico >= 86 && $numero_unico < 90) {
                                        $txt_subtitulo = "EXCEDE EXPECTATIVAS";
                                    }
                                    if ($numero_unico >= 90) {
                                        $txt_subtitulo = "ALTO DESEMPEÑO";
                                    }



                                ?>
                                    <tr>
                                        <td><?php echo $cont; ?></td>
                                        <td><?php echo $data["documento"]; ?></td>
                                        <td><?php echo $nombreColaborador; ?></td>
                                        <td><?php echo $vicepresidencia; ?></td>
                                        <td><?php echo $area; ?></td>
                                        <td><?php echo $unidadCorporativa; ?></td>
                                        <td><?php echo $nivelJerarquico; ?></td>
                                        <td><?php echo $cargo; ?></td>
                                        <td style="text-align:center;"><?php echo $a_por; ?></td>
                                        <?php if ($_SESSION["periodo_desempenio"] > 2024) { ?>
                                            <td style="text-align:center;"><?php echo $kpis; ?></td>
                                        <?php } ?>
                                        <td style="text-align:center;"><?php echo $puntuacionCompetencias; ?></td>
                                        <td style="font-weight: 700;font-size: 16px;text-align:center;background-color: <?php echo $color_bg; ?> !important;color:<?php echo $color_text; ?>;" title="<?php echo $txt_subtitulo; ?>"><?php echo $numero_unico; ?></td>
                                    </tr>
                                <?php $cont++;
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
<?php } ?>

<script>
    var api_competencias = '<?php echo $url; ?>mi_desempenio/api/';

    function FirmaAprobacion(id_empleado) {

        // Llamada AJAX para cargar el contenido del modal_firma
        jQuery.ajax({
            url: api_competencias + "firma_pdi.php",
            type: 'post',
            data: {
                id_empleado: id_empleado
            },
        }).done(function(resp) {
            $('#modalAviso .modal-dialog').removeClass('modal-sm modal-lg modal-xl').addClass('modal-md');
            // $('#modalAviso').modal({
            //     backdrop: 'static',
            //     keyboard: false
            // });

            $("#contenidoAviso").html(resp);

        }).fail(function(resp) {
            console.log(resp);
        });

    }

    function IntervencionGH(id_empleado) {
        jQuery.ajax({
                url: api_competencias + "intervencion_pdi.php",
                type: 'post',
                data: {
                    id_empleado: id_empleado
                },
            }).done(function(resp) {
                $('#modalAviso .modal-dialog').removeClass('modal-sm modal-lg modal-xl').addClass('modal-md');
                // $('#modalAviso').modal({
                //     backdrop: 'static',
                //     keyboard: false
                // });

                $("#contenidoAviso").html(resp);
            })
            .fail(function(resp) {
                console.log(resp);
            })
            .always(function(resp) {});
    }
</script>