<?php
namespace Notificaciones\Dominio;

use Notificaciones\Dominio\Excepciones\CodigoDesconocido;

/**
 * Catálogo de notificaciones. Cada constante es un código que debe existir
 * también como fila en Notificaciones_Tipos (ahí vive su configuración).
 *
 * El valor es igual al nombre, en MAYÚSCULAS_CON_GUION_BAJO, con el módulo como
 * prefijo. Agregar una notificación nueva:
 *
 *   const OKRS_KR_ASIGNACION = 'OKRS_KR_ASIGNACION';
 *   // en CAMPOS: self::OKRS_KR_ASIGNACION => ['kr_titulo', 'okr_titulo'],
 *   // y su fila en Notificaciones_Tipos con codigo = 'OKRS_KR_ASIGNACION'
 *
 * El valor de una constante no se cambia nunca: es la llave contra la BD.
 * Hoy el catálogo está vacío: la base del módulo no trae implementaciones.
 */
final class CodigoNotificacion
{
    /**
     * Datos que acepta cada código en GenerarNotificacion::ejecutar(), y por lo
     * tanto los únicos {{marcadores}} que sus plantillas pueden usar (además de
     * Plantilla::CAMPOS_BASE).
     */
    private const CAMPOS = [];

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
