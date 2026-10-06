<?php
// Piezas compartidas por las pantallas de views/notificaciones/administrar.

use Notificaciones\Aplicacion\VerificarAdministrador;

const NOTIFICACIONES_SECCIONES_ADMINISTRACION = [
    'tipos' => ['Tipos', 'bx-category', '?pg=notificaciones/administrar/tipos'],
    'configuracion' => ['Configuración', 'bx-cog', '?pg=notificaciones/administrar/configuracion'],
    'envios' => ['Envíos', 'bx-envelope', '?pg=notificaciones/administrar/envios'],
];

/** Además de Rutas, cada pantalla confirma el rol Administrador contra la BD. */
function notificacionesEsAdministrador(mysqli $mysqli, array $usuario): bool
{
    return VerificarAdministrador::ejecutar($mysqli, (int) $usuario['id_empresa'], (int) $usuario['id']);
}

function notificacionesPintarSinPermiso(): void
{
    echo '<div class="notif"><div class="notif-panel notif-vacio"><div class="notif-vacio__icono"><i class="bx bx-lock-alt"></i></div>'
        . '<h5>Sin permiso</h5><p>Solo un administrador puede configurar las notificaciones.</p></div></div>';
}

/** Encabezado con título y la navegación entre secciones de administración. */
function notificacionesPintarEncabezadoAdministracion(string $seccionActual, string $titulo, string $subtitulo): void
{
    ?>
    <div class="notif-encabezado">
        <div>
            <h3><?= notificacionesEscapar($titulo) ?></h3>
            <p><?= notificacionesEscapar($subtitulo) ?></p>
        </div>
        <a href="?pg=notificaciones/bandeja" class="btn btn-light"><i class="bx bx-bell"></i> Mi bandeja</a>
    </div>
    <ul class="nav notif-pestanas mb-3">
        <?php foreach (NOTIFICACIONES_SECCIONES_ADMINISTRACION as $clave => $seccion): ?>
            <li class="nav-item">
                <a class="nav-link<?= $clave === $seccionActual ? ' active' : '' ?>" href="<?= $seccion[2] ?>">
                    <i class="bx <?= $seccion[1] ?>"></i> <?= notificacionesEscapar($seccion[0]) ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php
}

/** Mensajes de resultado de un guardado: éxito o lista de errores. */
function notificacionesPintarResultado(?array $errores, string $mensajeExito): void
{
    if ($errores === null) {
        return;
    }
    if (!$errores) {
        echo '<div class="alert alert-success"><i class="bx bx-check-circle"></i> ' . notificacionesEscapar($mensajeExito) . '</div>';
        return;
    }
    echo '<div class="alert alert-danger"><strong>No se guardó:</strong><ul class="mb-0">';
    foreach ($errores as $error) {
        echo '<li>' . notificacionesEscapar($error) . '</li>';
    }
    echo '</ul></div>';
}
