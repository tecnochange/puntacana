<?php
namespace Notificaciones\Dominio;

/** Reemplaza {{marcador}} por su dato. Un marcador sin dato queda vacío. */
final class Plantilla
{
    /**
     * Datos que el módulo pone siempre, además de los del código:
     * enlace es la URL absoluta para abrir la notificación (distinta de `url`,
     * que es la ruta relativa al registro).
     */
    const DATOS_BASE = ['destinatario_nombre', 'fecha_vencimiento', 'enlace'];

    private const PATRON_MARCADOR = '/\{\{\s*([a-z0-9_]+)\s*\}\}/';
    private const FORMATO_FECHA_VISIBLE = 'd/m/Y';

    /** Cómo se muestra una fecha en plantillas y correos. */
    public static function formatearFecha(?\DateTimeImmutable $fecha): string
    {
        return $fecha === null ? '' : $fecha->format(self::FORMATO_FECHA_VISIBLE);
    }

    /** Nombres de los marcadores que usa un texto. */
    public static function marcadores(?string $texto): array
    {
        if ($texto === null || $texto === '') {
            return [];
        }
        preg_match_all(self::PATRON_MARCADOR, $texto, $coincidencias);
        return array_values(array_unique($coincidencias[1]));
    }

    /** Para texto plano (títulos, asuntos): se escapa al pintarlo, no aquí. */
    public static function renderizarTexto(?string $texto, array $datos): string
    {
        return self::renderizar($texto, $datos, false);
    }

    /** Para cuerpos de correo en HTML: cada dato se escapa antes de insertarlo. */
    public static function renderizarHtml(?string $texto, array $datos): string
    {
        return self::renderizar($texto, $datos, true);
    }

    private static function renderizar(?string $texto, array $datos, bool $escaparHtml): string
    {
        if ($texto === null || $texto === '') {
            return '';
        }
        return preg_replace_callback(self::PATRON_MARCADOR, function (array $coincidencia) use ($datos, $escaparHtml) {
            $dato = isset($datos[$coincidencia[1]]) ? (string) $datos[$coincidencia[1]] : '';
            return $escaparHtml ? htmlspecialchars($dato, ENT_QUOTES, 'UTF-8') : $dato;
        }, $texto);
    }
}
