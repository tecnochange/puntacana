<?php
require_once __DIR__ . '/../../app/models/notificaciones/cargar.php';
require_once __DIR__ . '/componentes/presentacion.php';

use Notificaciones\Aplicacion\ConsultarBandeja;
use Notificaciones\Dominio\EstadoNotificacion;

const NOTIFICACIONES_DIAS_ESTA_SEMANA = 7;
const NOTIFICACIONES_FILTROS_RECIBIDAS = ['' => 'Cualquier fecha', '0' => 'Hoy', '7' => 'Últimos 7 días', '30' => 'Últimos 30 días', '90' => 'Últimos 90 días'];
const NOTIFICACIONES_FILTROS_VENCIMIENTO = ['' => 'Cualquier vencimiento', 'vencidas' => 'Vencidas o vencen hoy', '3' => 'Próximos 3 días', '7' => 'Próximos 7 días', 'sin_fecha' => 'Sin fecha de vencimiento'];
const NOTIFICACIONES_ORDENES = ['recientes' => 'Más recientes', 'antiguas' => 'Más antiguas', 'vencimiento' => 'Vencen primero'];

/** Lo que la vista necesita de cada notificación, ya calculado. */
function notificacionesPresentar(array $fila, DateTimeImmutable $ahora): array
{
    $hoy = $ahora->setTime(0, 0);
    $creada = new DateTimeImmutable($fila['created_at']);
    $fechaVencimiento = $fila['fecha_vencimiento'] ? new DateTimeImmutable($fila['fecha_vencimiento']) : null;
    $estaAbierta = $fila['estado'] === EstadoNotificacion::ABIERTA;
    $diasParaVencer = $fechaVencimiento ? notificacionesDiasEntre($hoy, $fechaVencimiento) : null;

    return [
        'id' => (int) $fila['id'],
        'id_tipo' => (int) $fila['id_tipo'],
        'nombre_tipo' => $fila['nombre_tipo'],
        'titulo' => $fila['titulo'],
        'mensaje' => $fila['mensaje'],
        'icono' => notificacionesIconoSeguro($fila['icono']),
        'color' => notificacionesColorSeguro($fila['color']),
        'leida' => $fila['fecha_lectura'] !== null,
        'grupo' => notificacionesGrupoFecha($creada, $hoy),
        'hace' => notificacionesTiempoRelativo($creada, $ahora),
        'creada' => $creada->format('d/m/Y H:i'),
        'marca_creada' => $creada->getTimestamp(),
        'dias_desde_creada' => notificacionesDiasEntre($creada, $hoy),
        'dias_para_vencer' => $diasParaVencer,
        'vencimiento' => $estaAbierta ? notificacionesEtiquetaVencimiento($diasParaVencer, $fechaVencimiento) : null,
        'vence_pronto' => $estaAbierta && $diasParaVencer !== null && $diasParaVencer <= NOTIFICACIONES_DIAS_VENCE_PRONTO,
        'registro' => notificacionesRegistroVisible($fila['tipo_registro'], $fila['id_registro']),
        'codigo_estado' => $fila['estado'],
        'estado' => $estaAbierta ? null : notificacionesEstadoVisible($fila['estado']),
    ];
}

function notificacionesGrupoFecha(DateTimeImmutable $fecha, DateTimeImmutable $hoy): string
{
    $diasAtras = notificacionesDiasEntre($fecha, $hoy);
    if ($diasAtras <= 0) {
        return 'Hoy';
    }
    if ($diasAtras === 1) {
        return 'Ayer';
    }
    return $diasAtras < NOTIFICACIONES_DIAS_ESTA_SEMANA ? 'Esta semana' : 'Anteriores';
}

/** [id_tipo => nombre] de los tipos que aparecen en la bandeja, por nombre. */
function notificacionesTiposPresentes(array $notificaciones): array
{
    $tipos = array_column($notificaciones, 'nombre_tipo', 'id_tipo');
    asort($tipos, SORT_NATURAL | SORT_FLAG_CASE);
    return $tipos;
}

function notificacionesPintarOpciones(array $opciones): void
{
    foreach ($opciones as $valor => $texto) {
        echo '<option value="' . notificacionesEscapar((string) $valor) . '">' . notificacionesEscapar($texto) . '</option>';
    }
}

/** Agrupa por 'Hoy', 'Ayer', ... conservando el orden (más recientes primero). */
function notificacionesAgruparPorFecha(array $notificaciones): array
{
    $grupos = [];
    foreach ($notificaciones as $notificacion) {
        $grupos[$notificacion['grupo']][] = $notificacion;
    }
    return $grupos;
}

function notificacionesPintarItem(array $n): void
{
    $e = 'notificacionesEscapar';
    $claseLeida = $n['leida'] ? '' : ' notif-item--no-leida';
    ?>
    <article class="notif-item<?= $claseLeida ?>" id="notificacion_<?= $n['id'] ?>" style="--notif-color: <?= $n['color'] ?>"
             data-buscar="<?= $e(mb_strtolower($n['titulo'] . ' ' . $n['mensaje'] . ' ' . $n['registro'] . ' ' . $n['nombre_tipo'])) ?>"
             data-leida="<?= $n['leida'] ? 1 : 0 ?>" data-tipo="<?= $n['id_tipo'] ?>" data-estado="<?= $e($n['codigo_estado']) ?>"
             data-grupo="<?= $e($n['grupo']) ?>" data-creada="<?= $n['marca_creada'] ?>" data-dias-creada="<?= $n['dias_desde_creada'] ?>"
             data-dias-vencer="<?= $n['dias_para_vencer'] === null ? '' : $n['dias_para_vencer'] ?>">
        <div class="notif-item__icono"><i class="bx <?= $n['icono'] ?>"></i></div>
        <div class="notif-item__cuerpo">
            <div class="notif-item__titulo">
                <?php if (!$n['leida']): ?><span class="notif-punto" title="Sin leer"></span><?php endif; ?>
                <a href="?pg=notificaciones/detalle&id=<?= $n['id'] ?>" class="notif-item__enlace"><?= $e($n['titulo']) ?></a>
            </div>
            <?php if ($n['mensaje'] !== null && $n['mensaje'] !== ''): ?>
                <p class="notif-item__mensaje"><?= $e($n['mensaje']) ?></p>
            <?php endif; ?>
            <div class="notif-item__meta">
                <span title="<?= $e($n['creada']) ?>"><i class="bx bx-time-five"></i> <?= $e($n['hace']) ?></span>
                <span class="notif-item__tipo"><i class="bx bx-purchase-tag-alt"></i> <?= $e($n['nombre_tipo']) ?></span>
                <?php if ($n['vencimiento']): ?>
                    <span class="notif-chip <?= $n['vencimiento'][1] ?>"><i class="bx bx-calendar-exclamation"></i> <?= $e($n['vencimiento'][0]) ?></span>
                <?php endif; ?>
                <?php if ($n['registro'] !== ''): ?>
                    <span class="notif-chip notif-chip--normal"><i class="bx bx-link-alt"></i> <?= $e($n['registro']) ?></span>
                <?php endif; ?>
                <?php if ($n['estado']): ?>
                    <span class="badge rounded-pill <?= $n['estado'][1] ?>"><?= $e($n['estado'][0]) ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="notif-item__acciones">
            <span class="notif-item__ir" aria-hidden="true"><i class="bx bx-chevron-right"></i></span>
        </div>
    </article>
    <?php
}

function notificacionesPintarLista(array $notificaciones, string $icono, string $tituloVacio, string $textoVacio): void
{
    if (!$notificaciones) {
        ?>
        <div class="notif-vacio">
            <div class="notif-vacio__icono"><i class="bx <?= $icono ?>"></i></div>
            <h5><?= notificacionesEscapar($tituloVacio) ?></h5>
            <p><?= notificacionesEscapar($textoVacio) ?></p>
        </div>
        <?php
        return;
    }
    echo '<div class="notif-lista">';
    foreach (notificacionesAgruparPorFecha($notificaciones) as $grupo => $items) {
        echo '<div class="notif-grupo"><div class="notif-grupo__titulo">' . notificacionesEscapar($grupo) . '</div>';
        foreach ($items as $notificacion) {
            notificacionesPintarItem($notificacion);
        }
        echo '</div>';
    }
    echo '</div>';
    echo '<div class="notif-sin-resultados d-none"><i class="bx bx-filter-alt"></i><p>Ninguna notificación coincide con los filtros.</p>'
        . '<button type="button" class="btn btn-sm btn-outline-primary notif-limpiar-filtros">Limpiar filtros</button></div>';
}

$bandeja = ConsultarBandeja::ejecutar($connect_admin, (int) $user_log['id_empresa'], (int) $user_log['id']);

$ahora = new DateTimeImmutable();
$presentar = function (array $filas) use ($ahora) {
    return array_map(function (array $fila) use ($ahora) {
        return notificacionesPresentar($fila, $ahora);
    }, $filas);
};
$pendientes = $presentar($bandeja['pendientes']);
$avisos = $presentar($bandeja['avisos']);
$historial = $presentar($bandeja['historial']);
$cantidadVencenPronto = count(array_filter($pendientes, function (array $n) {
    return $n['vence_pronto'];
}));
$tiposPresentes = notificacionesTiposPresentes(array_merge($pendientes, $avisos, $historial));
$estadosCerrados = array_diff_key(NOTIFICACIONES_ESTADOS_VISIBLES, [EstadoNotificacion::ABIERTA => true]);
?>

<?php require __DIR__ . '/componentes/estilos.php'; ?>

<div class="notif">
    <div class="notif-encabezado">
        <div>
            <h3>Mis notificaciones</h3>
            <p>Tus tareas pendientes y avisos en un solo lugar.</p>
        </div>
        <?php if ($bandeja['cantidad_no_leidas'] > 0): ?>
            <button type="button" class="btn btn-outline-primary" id="bt_marcar_todas">
                <i class="bx bx-check-double"></i> Marcar todas como leídas
            </button>
        <?php endif; ?>
    </div>

    <div class="notif-resumen">
        <button type="button" class="notif-tarjeta notif-tarjeta--pendientes notif-atajo" data-atajo="pendientes" title="Ver tus pendientes">
            <div class="notif-tarjeta__icono"><i class="bx bx-task"></i></div>
            <div><div class="notif-tarjeta__valor"><?= count($pendientes) ?></div><div class="notif-tarjeta__etiqueta">Pendientes</div></div>
        </button>
        <button type="button" class="notif-tarjeta notif-tarjeta--pronto notif-atajo" data-atajo="pronto" title="Ver los pendientes que vencen pronto">
            <div class="notif-tarjeta__icono"><i class="bx bx-alarm-exclamation"></i></div>
            <div><div class="notif-tarjeta__valor"><?= $cantidadVencenPronto ?></div><div class="notif-tarjeta__etiqueta">Vencen en <?= NOTIFICACIONES_DIAS_VENCE_PRONTO ?> días o menos</div></div>
        </button>
        <button type="button" class="notif-tarjeta notif-tarjeta--sin-leer notif-atajo" data-atajo="sin_leer" title="Ver solo las que no has leído">
            <div class="notif-tarjeta__icono"><i class="bx bx-bell"></i></div>
            <div><div class="notif-tarjeta__valor" id="notif_total_sin_leer"><?= (int) $bandeja['cantidad_no_leidas'] ?></div><div class="notif-tarjeta__etiqueta">Sin leer</div></div>
        </button>
    </div>

    <div class="notif-panel">
        <div class="notif-barra">
            <ul class="nav notif-pestanas" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab_pendientes" type="button" role="tab">
                        <i class="bx bx-task"></i> Pendientes <span class="notif-contador"><?= count($pendientes) ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_avisos" type="button" role="tab">
                        <i class="bx bx-bell"></i> Notificaciones <span class="notif-contador"><?= count($avisos) ?></span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab_historial" type="button" role="tab">
                        <i class="bx bx-history"></i> Historial <span class="notif-contador"><?= count($historial) ?></span>
                    </button>
                </li>
            </ul>
            <div class="notif-barra__derecha">
                <div class="notif-buscador">
                    <i class="bx bx-search"></i>
                    <input type="search" class="form-control" id="notif_buscar" placeholder="Buscar notificación..." title="Busca en el título, el mensaje y el tipo">
                </div>
                <button type="button" class="btn btn-light notif-boton-filtros" id="bt_filtros" aria-expanded="true" aria-controls="notif_filtros">
                    <i class="bx bx-slider-alt"></i> Filtros <span class="notif-contador d-none" id="notif_cantidad_filtros">0</span>
                </button>
            </div>
        </div>

        <div class="notif-filtros" id="notif_filtros">
            <div class="notif-filtro">
                <span class="notif-filtro__etiqueta">Lectura</span>
                <div class="notif-segmentos" role="group" aria-label="Filtrar por lectura">
                    <button type="button" class="active" data-lectura="">Todas</button>
                    <button type="button" data-lectura="0">Sin leer</button>
                    <button type="button" data-lectura="1">Leídas</button>
                </div>
            </div>
            <div class="notif-filtro">
                <label class="notif-filtro__etiqueta" for="notif_filtro_tipo">Tipo</label>
                <select class="form-select form-select-sm" id="notif_filtro_tipo" data-filtro="tipo">
                    <option value="">Todos los tipos</option>
                    <?php notificacionesPintarOpciones($tiposPresentes); ?>
                </select>
            </div>
            <div class="notif-filtro">
                <label class="notif-filtro__etiqueta" for="notif_filtro_recibidas">Recibidas</label>
                <select class="form-select form-select-sm" id="notif_filtro_recibidas" data-filtro="recibidas">
                    <?php notificacionesPintarOpciones(NOTIFICACIONES_FILTROS_RECIBIDAS); ?>
                </select>
            </div>
            <div class="notif-filtro">
                <label class="notif-filtro__etiqueta" for="notif_filtro_vencimiento">Vencimiento</label>
                <select class="form-select form-select-sm" id="notif_filtro_vencimiento" data-filtro="vencimiento">
                    <?php notificacionesPintarOpciones(NOTIFICACIONES_FILTROS_VENCIMIENTO); ?>
                </select>
            </div>
            <div class="notif-filtro d-none" id="notif_filtro_estado_contenedor">
                <label class="notif-filtro__etiqueta" for="notif_filtro_estado">Estado</label>
                <select class="form-select form-select-sm" id="notif_filtro_estado" data-filtro="estado">
                    <option value="">Todos los estados</option>
                    <?php notificacionesPintarOpciones(array_map(function (array $estado) {
                        return $estado[0];
                    }, $estadosCerrados)); ?>
                </select>
            </div>
            <div class="notif-filtro">
                <label class="notif-filtro__etiqueta" for="notif_orden">Ordenar</label>
                <select class="form-select form-select-sm" id="notif_orden" data-filtro="orden">
                    <?php notificacionesPintarOpciones(NOTIFICACIONES_ORDENES); ?>
                </select>
            </div>
        </div>

        <div class="notif-activos d-none" id="notif_activos">
            <span class="notif-activos__resultado" id="notif_resultado"></span>
            <div class="notif-activos__chips" id="notif_chips"></div>
            <button type="button" class="btn btn-link btn-sm notif-limpiar-filtros">Limpiar filtros</button>
        </div>

        <div class="tab-content notif-contenido">
            <div class="tab-pane fade show active" id="tab_pendientes" role="tabpanel">
                <?php notificacionesPintarLista($pendientes, 'bx-check-circle', 'Todo al día', 'No tienes tareas pendientes.'); ?>
            </div>
            <div class="tab-pane fade" id="tab_avisos" role="tabpanel">
                <?php notificacionesPintarLista($avisos, 'bx-bell-off', 'Sin notificaciones', 'Aquí verás los avisos que te lleguen.'); ?>
            </div>
            <div class="tab-pane fade" id="tab_historial" role="tabpanel">
                <?php notificacionesPintarLista($historial, 'bx-history', 'Sin historial', 'Las tareas completadas, vencidas o canceladas aparecerán aquí.'); ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        var CLAVE_GUARDADO = 'notificaciones_bandeja_filtros';
        var DIAS_VENCE_PRONTO = <?= NOTIFICACIONES_DIAS_VENCE_PRONTO ?>;
        var FILTROS_VACIOS = { texto: '', lectura: '', tipo: '', recibidas: '', vencimiento: '', estado: '', orden: 'recientes', pestana: 'tab_pendientes' };
        var filtros = $.extend({}, FILTROS_VACIOS, leerGuardado());

        // Cada panel guarda sus notificaciones una vez; filtrar y ordenar vuelve a armar la lista a partir de ellas.
        var paneles = {};
        $('.notif-contenido .tab-pane').each(function () {
            paneles[this.id] = { panel: $(this), items: $(this).find('.notif-item').toArray() };
        });

        function leerGuardado() {
            try {
                return JSON.parse(sessionStorage.getItem(CLAVE_GUARDADO)) || {};
            } catch (error) {
                return {};
            }
        }

        function guardar() {
            try {
                sessionStorage.setItem(CLAVE_GUARDADO, JSON.stringify(filtros));
            } catch (error) {
                // Sin almacenamiento (navegación privada): los filtros solo duran mientras la página esté abierta.
            }
        }

        function numero(item, atributo) {
            var valor = item.getAttribute(atributo);
            return valor === '' || valor === null ? null : parseInt(valor, 10);
        }

        // El estado solo filtra el historial: lo abierto siempre está ABIERTA.
        function cumple(item, idPanel) {
            var diasVencer = numero(item, 'data-dias-vencer');
            if (filtros.texto !== '' && item.getAttribute('data-buscar').indexOf(filtros.texto) === -1) return false;
            if (filtros.lectura !== '' && item.getAttribute('data-leida') !== filtros.lectura) return false;
            if (filtros.tipo !== '' && item.getAttribute('data-tipo') !== filtros.tipo) return false;
            if (filtros.estado !== '' && idPanel === 'tab_historial' && item.getAttribute('data-estado') !== filtros.estado) return false;
            if (filtros.recibidas !== '' && numero(item, 'data-dias-creada') > parseInt(filtros.recibidas, 10)) return false;
            if (filtros.vencimiento === 'sin_fecha') return diasVencer === null;
            if (filtros.vencimiento === 'vencidas') return diasVencer !== null && diasVencer <= 0;
            if (filtros.vencimiento !== '') return diasVencer !== null && diasVencer >= 0 && diasVencer <= parseInt(filtros.vencimiento, 10);
            return true;
        }

        function grupoVencimiento(item) {
            var dias = numero(item, 'data-dias-vencer');
            if (dias === null) return 'Sin fecha de vencimiento';
            if (dias < 0) return 'Vencidas';
            if (dias === 0) return 'Vencen hoy';
            if (dias <= DIAS_VENCE_PRONTO) return 'Próximos ' + DIAS_VENCE_PRONTO + ' días';
            if (dias <= 7) return 'Esta semana';
            return 'Más adelante';
        }

        function comparar(a, b) {
            var creadaA = numero(a, 'data-creada'), creadaB = numero(b, 'data-creada');
            if (filtros.orden === 'antiguas') return creadaA - creadaB;
            if (filtros.orden === 'vencimiento') {
                var venceA = numero(a, 'data-dias-vencer'), venceB = numero(b, 'data-dias-vencer');
                if (venceA !== venceB) {
                    if (venceA === null) return 1;
                    if (venceB === null) return -1;
                    return venceA - venceB;
                }
            }
            return creadaB - creadaA;
        }

        function armarLista(datos, visibles) {
            var lista = datos.panel.find('.notif-lista');
            if (!lista.length) return;
            $(datos.items).detach();
            lista.empty();
            var grupoActual = null, contenedor = null;
            visibles.forEach(function (item) {
                var grupo = filtros.orden === 'vencimiento' ? grupoVencimiento(item) : item.getAttribute('data-grupo');
                if (grupo !== grupoActual) {
                    grupoActual = grupo;
                    contenedor = $('<div class="notif-grupo"></div>').append($('<div class="notif-grupo__titulo"></div>').text(grupo));
                    lista.append(contenedor);
                }
                contenedor.append(item);
            });
            datos.panel.find('.notif-sin-resultados').toggleClass('d-none', datos.items.length === 0 || visibles.length > 0);
        }

        function cantidadFiltrosActivos() {
            var cantidad = 0;
            ['lectura', 'tipo', 'recibidas', 'vencimiento'].forEach(function (clave) {
                if (filtros[clave] !== '') cantidad++;
            });
            if (filtros.estado !== '' && filtros.pestana === 'tab_historial') cantidad++;
            return cantidad;
        }

        function textoDeSelect(id) {
            return $('#' + id + ' option:selected').text();
        }

        function pintarChips(totalPestana, visiblesPestana) {
            var chips = [];
            if (filtros.texto !== '') chips.push(['texto', '“' + filtros.texto + '”']);
            if (filtros.lectura !== '') chips.push(['lectura', filtros.lectura === '0' ? 'Sin leer' : 'Leídas']);
            if (filtros.tipo !== '') chips.push(['tipo', textoDeSelect('notif_filtro_tipo')]);
            if (filtros.recibidas !== '') chips.push(['recibidas', 'Recibidas: ' + textoDeSelect('notif_filtro_recibidas').toLowerCase()]);
            if (filtros.vencimiento !== '') chips.push(['vencimiento', textoDeSelect('notif_filtro_vencimiento')]);
            if (filtros.estado !== '' && filtros.pestana === 'tab_historial') chips.push(['estado', textoDeSelect('notif_filtro_estado')]);

            var contenedor = $('#notif_chips').empty();
            chips.forEach(function (chip) {
                var boton = $('<button type="button" class="notif-chip notif-chip--filtro"></button>').attr('data-quitar', chip[0]).attr('title', 'Quitar filtro');
                boton.text(chip[1]).append(' <i class="bx bx-x"></i>');
                contenedor.append(boton);
            });
            $('#notif_resultado').text('Mostrando ' + visiblesPestana + ' de ' + totalPestana);
            $('#notif_activos').toggleClass('d-none', chips.length === 0);

            var cantidad = cantidadFiltrosActivos();
            $('#notif_cantidad_filtros').text(cantidad).toggleClass('d-none', cantidad === 0);
        }

        function aplicar() {
            var totalPestana = 0, visiblesPestana = 0;
            $.each(paneles, function (id, datos) {
                var visibles = datos.items.filter(function (item) {
                    return cumple(item, id);
                }).sort(comparar);
                armarLista(datos, visibles);
                $('[data-bs-target="#' + id + '"] .notif-contador').text(visibles.length);
                if (id === filtros.pestana) {
                    totalPestana = datos.items.length;
                    visiblesPestana = visibles.length;
                }
            });
            $('#notif_filtro_estado_contenedor').toggleClass('d-none', filtros.pestana !== 'tab_historial');
            pintarChips(totalPestana, visiblesPestana);
            guardar();
        }

        function sincronizarControles() {
            $('#notif_buscar').val(filtros.texto);
            $('[data-lectura]').removeClass('active').filter('[data-lectura="' + filtros.lectura + '"]').addClass('active');
            $('[data-filtro]').each(function () {
                var clave = $(this).data('filtro');
                $(this).val(filtros[clave]);
                // Un valor guardado que ya no existe (p. ej. un tipo sin notificaciones) vuelve a "todos".
                if ($(this).val() === null) {
                    filtros[clave] = FILTROS_VACIOS[clave];
                    $(this).val(filtros[clave]);
                }
            });
        }

        function mostrarPestana(id) {
            filtros.pestana = id;
            // El clic lo atiende el data-api de Bootstrap, igual que si lo hiciera la persona.
            $('[data-bs-target="#' + id + '"]').get(0).click();
        }

        function limpiar() {
            filtros = $.extend({}, FILTROS_VACIOS, { pestana: filtros.pestana });
            sincronizarControles();
            aplicar();
        }

        $('#notif_buscar').on('input', function () {
            filtros.texto = $(this).val().toLowerCase().trim();
            aplicar();
        });
        $('[data-lectura]').on('click', function () {
            filtros.lectura = String($(this).data('lectura'));
            $('[data-lectura]').removeClass('active');
            $(this).addClass('active');
            aplicar();
        });
        $('[data-filtro]').on('change', function () {
            filtros[$(this).data('filtro')] = $(this).val();
            aplicar();
        });
        $('[data-bs-toggle="tab"]').on('shown.bs.tab', function () {
            filtros.pestana = $(this).data('bs-target').substring(1);
            aplicar();
        });
        $(document).on('click', '.notif-limpiar-filtros', limpiar);
        $(document).on('click', '[data-quitar]', function () {
            var clave = $(this).data('quitar');
            filtros[clave] = FILTROS_VACIOS[clave];
            sincronizarControles();
            aplicar();
        });
        $('#bt_filtros').on('click', function () {
            var oculto = $('#notif_filtros').toggleClass('d-none').hasClass('d-none');
            $(this).attr('aria-expanded', oculto ? 'false' : 'true');
        });

        // Atajos de las tarjetas del resumen.
        $('.notif-atajo').on('click', function () {
            var atajo = $(this).data('atajo');
            var pestanaActual = filtros.pestana;
            filtros = $.extend({}, FILTROS_VACIOS);
            if (atajo === 'pronto') {
                filtros.vencimiento = String(DIAS_VENCE_PRONTO);
                filtros.orden = 'vencimiento';
            }
            if (atajo === 'sin_leer') {
                filtros.lectura = '0';
                filtros.pestana = pestanaActual;
            }
            sincronizarControles();
            mostrarPestana(filtros.pestana);
            aplicar();
        });

        $('#bt_marcar_todas').on('click', function () {
            $.post('api/notificaciones/marcar_todas_leidas.php', {}, function (respuesta) {
                if (respuesta.status === 'success') {
                    location.reload();
                }
            }, 'json');
        });

        // En pantallas pequeñas los filtros empiezan plegados para que la lista se vea primero.
        if (window.matchMedia('(max-width: 48rem)').matches) {
            $('#bt_filtros').trigger('click');
        }
        sincronizarControles();
        if (!paneles[filtros.pestana]) {
            filtros.pestana = 'tab_pendientes';
        }
        if (filtros.pestana !== 'tab_pendientes') {
            // Al cargar se activa sin animación: si alguien hace clic mientras dura la transición, Bootstrap lo ignora
            // y la pestaña y su contenido quedan desfasados.
            $('.notif-pestanas .nav-link').removeClass('active').attr('aria-selected', 'false');
            $('[data-bs-target="#' + filtros.pestana + '"]').addClass('active').attr('aria-selected', 'true');
            $('.notif-contenido .tab-pane').removeClass('active show');
            $('#' + filtros.pestana).addClass('active show');
        }
        aplicar();
    });
</script>
