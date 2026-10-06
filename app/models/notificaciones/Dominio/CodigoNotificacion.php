<?php
namespace Notificaciones\Dominio;

use Notificaciones\Dominio\Excepciones\CodigoDesconocido;

/**
 * Catálogo de notificaciones. Cada constante es un código que debe existir
 * también como fila en Notificaciones_Tipos (ahí vive su configuración).
 *
 * Agregar una notificación nueva = una constante aquí, sus campos en CAMPOS y
 * su fila en la BD. El valor de una constante no se cambia nunca: es la llave
 * contra la BD.
 */
final class CodigoNotificacion
{
    const PRUEBA = 'general.prueba';

    /**
     * Datos que acepta cada código en GenerarNotificacion::ejecutar(), y por lo
     * tanto los únicos {{marcadores}} que sus plantillas pueden usar (además de
     * Plantilla::CAMPOS_BASE).
     */
    private const CAMPOS = [
        self::PRUEBA => ['titulo', 'mensaje'],
    ];

    public static function existe(string $codigo): bool
    {
        return array_key_exists($codigo, self::CAMPOS);
    }

    public static function campos(string $codigo): array
    {
        if (!self::existe($codigo)) {
            throw new CodigoDesconocido("El código '$codigo' no está en CodigoNotificacion.");
        }
        return self::CAMPOS[$codigo];
    }
}
