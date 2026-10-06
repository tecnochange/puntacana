<?php
namespace Notificaciones\Dominio;

/**
 * Catálogo de notificaciones. Cada caso es un código que debe existir también
 * como fila en Notificaciones_Tipos (ahí vive su configuración).
 *
 * Agregar una notificación nueva = un caso aquí, sus campos() y su fila en la BD.
 * El valor del caso no se cambia nunca: es la llave contra la BD.
 */
enum CodigoNotificacion: string
{
    case PRUEBA = 'general.prueba';

    /**
     * Datos que acepta este código en GenerarNotificacion::ejecutar(), y por lo
     * tanto los únicos {{marcadores}} que sus plantillas pueden usar (además de
     * Plantilla::CAMPOS_BASE).
     */
    public function campos(): array
    {
        return match ($this) {
            self::PRUEBA => ['titulo', 'mensaje'],
        };
    }
}
