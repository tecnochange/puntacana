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
 *
 * Un tipo se puede crear antes en la plataforma ("sin implementar"): su fila
 * existe pero su código todavía no está aquí, y sus plantillas solo pueden usar
 * Plantilla::DATOS_BASE hasta que se agregue la constante.
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

    /** MAYÚSCULAS_CON_GUION_BAJO: empieza con letra, sin guiones bajos dobles ni al final. */
    private const PATRON_CODIGO = '/^[A-Z][A-Z0-9]*(_[A-Z0-9]+)*$/';

    public static function tieneFormatoValido(string $codigo): bool
    {
        return (bool) preg_match(self::PATRON_CODIGO, $codigo);
    }

    /** Si el código ya está implementado en el sistema (tiene constante aquí). */
    public static function existe(string $codigo): bool
    {
        return array_key_exists($codigo, self::DATOS_PERMITIDOS);
    }

    /** Datos de un código implementado; [] si aún no lo está (solo podrá usar los datos base). */
    public static function datosPermitidosSiExiste(string $codigo): array
    {
        return self::existe($codigo) ? self::DATOS_PERMITIDOS[$codigo] : [];
    }

    public static function datosPermitidos(string $codigo): array
    {
        if (!self::existe($codigo)) {
            throw new CodigoDesconocido("El código '$codigo' no está en CodigoNotificacion.");
        }
        return self::DATOS_PERMITIDOS[$codigo];
    }
}
