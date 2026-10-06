<?php
require_once __DIR__ . '/../../app/models/notificaciones/cargar.php';
require_once __DIR__ . '/componentes/presentacion.php';

use Notificaciones\Aplicacion\GenerarNotificacion;
use Notificaciones\Dominio\CodigoNotificacion;
use Notificaciones\Dominio\Excepciones\ErrorNotificacion;

// Valida el módulo de punta a punta: el administrador se genera una notificación
// a sí mismo con el tipo general.prueba. Nunca genera para otra persona.
const NOTIFICACIONES_ROL_ADMINISTRADOR = '1';

$esAdministrador = in_array(NOTIFICACIONES_ROL_ADMINISTRADOR, $roles_usr, true);
$mensaje = null;
$exito = false;

$seEnvioFormulario = $esAdministrador && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generar_prueba']);
if ($seEnvioFormulario) {
    try {
        $idEvento = GenerarNotificacion::ejecutar(
            $connect_admin,
            codigo: CodigoNotificacion::PRUEBA,
            idEmpresa: (int) $user_log['id_empresa'],
            destinatarios: [(int) $user_log['id']],
            datos: [
                'titulo' => trim((string) ($_POST['titulo'] ?? '')),
                'mensaje' => trim((string) ($_POST['mensaje'] ?? '')),
            ],
            url: '?pg=notificaciones/bandeja',
        );
        $exito = $idEvento !== null;
        $mensaje = $exito
            ? "Notificación #$idEvento generada. Revísala en tu bandeja."
            : 'No se generó: tu usuario no quedó como destinatario válido (¿inactivo o de otra empresa?).';
    } catch (ErrorNotificacion $error) {
        $mensaje = 'No se generó: ' . $error->getMessage();
    }
}

$e = 'notificacionesEscapar';
?>

<div class="container-fluid" style="max-width: 700px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h3 class="mb-0">Prueba de notificaciones</h3>
        </div>
        <div class="card-body">
            <?php if (!$esAdministrador): ?>
                <p class="text-muted mb-0">Solo un administrador puede usar esta página.</p>
            <?php else: ?>
                <?php if ($mensaje !== null): ?>
                    <div class="alert <?= $exito ? 'alert-success' : 'alert-danger' ?>"><?= $e($mensaje) ?></div>
                <?php endif; ?>

                <p class="small text-muted">
                    Genera una notificación de tipo <code>general.prueba</code> dirigida solo a ti.
                    El correo depende del modo configurado en <code>Notificaciones_Configuracion</code>.
                </p>
                <form method="post" action="?pg=notificaciones/prueba">
                    <input type="hidden" name="generar_prueba" value="1">
                    <div class="mb-3">
                        <label class="form-label" for="prueba_titulo">Título</label>
                        <input type="text" class="form-control" id="prueba_titulo" name="titulo" maxlength="150" required value="Notificación de prueba">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="prueba_mensaje">Mensaje</label>
                        <textarea class="form-control" id="prueba_mensaje" name="mensaje" rows="3" maxlength="500">Si ves esto, el módulo de notificaciones funciona.</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Generar</button>
                    <a href="?pg=notificaciones/bandeja" class="btn btn-outline-secondary">Ir a mi bandeja</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
