<?php
namespace Notificaciones\Dominio;

use Notificaciones\Dominio\Excepciones\CodigoDesconocido;

/**
 * Catálogo de notificaciones. Cada constante es un código que debe existir
 * también como fila en Notificaciones_Tipos (ahí vive su configuración).
 *
 * El valor es igual al nombre, en MAYÚSCULAS_CON_GUION_BAJO, con el módulo como
 * prefijo. Agregar una notificación nueva = una constante aquí, sus datos en
 * DATOS_PERMITIDOS y su fila en Notificaciones_Tipos con el mismo codigo.
 *
 * El valor de una constante no se cambia nunca: es la llave contra la BD.
 */
final class CodigoNotificacion
{
    /** Al owner y a los responsables cuando se les asigna un resultado clave. */
    const OKRS_KR_ASIGNACION = 'OKRS_KR_ASIGNACION';

    /**
     * Nombres de datos que acepta cada código en datos_plantilla, y por lo tanto
     * los únicos {{marcadores}} que sus plantillas pueden usar (además de
     * Plantilla::DATOS_BASE).
     */
    private const DATOS_PERMITIDOS = [
        self::OKRS_KR_ASIGNACION => ['kr_titulo', 'okr_titulo'],
    ];

    public static function existe(string $codigo): bool
    {
        return array_key_exists($codigo, self::DATOS_PERMITIDOS);
    }

    public static function datosPermitidos(string $codigo): array
    {
        if (!self::existe($codigo)) {
            throw new CodigoDesconocido("El código '$codigo' no está en CodigoNotificacion.");
        }
        return self::DATOS_PERMITIDOS[$codigo];
    }
}
