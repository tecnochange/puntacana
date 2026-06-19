<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<div class="row">
    <div class="col-md-12">
        <table class="table display" style="width:100%;font-size: 14px !important;" id="documentos">
            <thead>
                <th>Documento</th>
                <th>Comentario</th>
                <th class="text-center">Publicado por</th>
                <th>Frecuencia</th>
                <th>Fecha</th>
                <th class="text-center">Acciones</th>
            </thead>
            <tbody>
                <?php
                $queryDocumentos = mysqli_query($connect_kpis, "SELECT * FROM Documentos_Kpis WHERE id_kpi = $idKpi ");
                $queryKPIS = mysqli_query($connect_kpis, "SELECT * FROM Kpis WHERE id = $idKpi");
                $dataKpis = mysqli_fetch_array($queryKPIS);
                while ($dataDocumentos = mysqli_fetch_array($queryDocumentos)) {

                    //dd($dataDocumentos);

                    $extension = pathinfo($dataDocumentos["archivo"], PATHINFO_EXTENSION);

                    switch ($extension) {
                        case 'doc':
                            $icono = '<i class="fas fa-file-word" style="color: #007bff; font-size: 1.5rem;"></i>';
                            break;
                        case 'docx':
                            $icono = '<i class="fas fa-file-word" style="color: #007bff; font-size: 1.5rem;"></i>';
                            break;
                        case 'pdf':
                            $icono = '<i class="fas fa-file-pdf" style="color: red; font-size: 1.5rem;"></i>';
                            break;
                        case 'xls':
                            $icono = '<i class="fas fa-file-excel" style="color: green; font-size: 1.5rem;"></i>';
                            break;
                        case 'xlsx':
                            $icono = '<i class="fas fa-file-excel" style="color: green; font-size: 1.5rem;"></i>';
                            break;
                        case 'ppt':
                            $icono = '<i class="fas fa-file-powerpoint" style="color: orange; font-size: 1.5rem;"></i>';
                            break;
                        case 'pptx':
                            $icono = '<i class="fas fa-file-powerpoint" style="color: orange; font-size: 1.5rem;"></i>';
                            break;
                    }

                    $archivo = '<a href="' . $recursos_publico . '/' . $dataDocumentos["archivo"] . '" target="_blank">' . $icono . '</a>';

                    $queryPub = mysqli_query($connect_admin, "SELECT * FROM Empleados WHERE id = '" . $dataDocumentos["id_empleado"] . "' ");
                    $dataPub = mysqli_fetch_array($queryPub);

                    if (!$dataPub["foto"]) {
                        $dataPub["foto"] = "img_default.jpg";
                    }

                    $publicadoPor = '<a data-bs-toggle="tooltip" href="javascript:Profile(' . $dataPub["id"] . ',1)" class="" id="profileOkr"><img loading="lazy" src="' . $recursos_publico . '/' .  $dataPub["foto"] . '" class="foto_min" title="' . $dataPub["nombre"] . '" style="width: 40px !important;height: 40px !important;"></a>&nbsp;&nbsp;';

                    if ($dataDocumentos["created_at"]) {
                        $fechaInicio = date("d-m-Y", strtotime($dataDocumentos["created_at"]));
                    } else {
                        $fechaInicio = "";
                    }

                    echo '<tr style="vertical-align: middle;">
                    <td class="text-start">' . $archivo . '</td>
                    <td>' . $dataDocumentos["comentario"] . '</td>
                    <td class="text-center">' . $publicadoPor . '</td>
                    <td>' . $dataDocumentos["frecuencia"] . '</td>
                    <td>' . $fechaInicio . '</td>';
                    if ($VALIDAR_ROOT["crear"] || $permisoEditDoc == true || $dataRolKPI['nombre_rol'] == 'Lider KPI') {
                        echo '<td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm bt_editar" title="Eliminar Documento" onclick="EliminarDocumento(this, ' . $dataDocumentos["id"] . ',' . $dataEmpleado["id_empresa"] . ',' . $dataEmpleado["id"] . ')" >
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>';
                    } else {
                        echo '<td class="text-end"></td>';
                    }
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
</div>