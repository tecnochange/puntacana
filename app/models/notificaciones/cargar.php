<?php
// Autoload del módulo de Notificaciones. Solo atiende el namespace Notificaciones\,
// así no interfiere con las clases globales del proyecto.
// Requiere PHP 8.1 o superior (enums y propiedades readonly).

const NOTIFICACIONES_PREFIJO_NAMESPACE = 'Notificaciones\\';

spl_autoload_register(function (string $clase): void {
    $esDelModulo = strncmp($clase, NOTIFICACIONES_PREFIJO_NAMESPACE, strlen(NOTIFICACIONES_PREFIJO_NAMESPACE)) === 0;
    if (!$esDelModulo) {
        return;
    }

    $relativa = substr($clase, strlen(NOTIFICACIONES_PREFIJO_NAMESPACE));
    $archivo = __DIR__ . '/' . str_replace('\\', '/', $relativa) . '.php';
    if (is_file($archivo)) {
        require $archivo;
    }
});
