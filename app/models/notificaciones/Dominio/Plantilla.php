<?php
namespace Notificaciones\Dominio;

/** Reemplaza {{campo}} por su valor. Un marcador sin valor queda vacío. */
final class Plantilla
{
    /** Campos que el módulo pone siempre, además de los datos del código. */
    public const CAMPOS_BASE = ['destinatario_nombre', 'fecha_limite', 'enlace'];

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
    public static function renderizarTexto(?string $texto, array $valores): string
    {
        return self::renderizar($texto, $valores, false);
    }

    /** Para cuerpos de correo en HTML: cada valor se escapa antes de insertarlo. */
    public static function renderizarHtml(?string $texto, array $valores): string
    {
        return self::renderizar($texto, $valores, true);
    }

    private static function renderizar(?string $texto, array $valores, bool $escaparHtml): string
    {
        if ($texto === null || $texto === '') {
            return '';
        }
        return preg_replace_callback(self::PATRON_MARCADOR, function (array $m) use ($valores, $escaparHtml) {
            $valor = (string) ($valores[$m[1]] ?? '');
            return $escaparHtml ? htmlspecialchars($valor, ENT_QUOTES, 'UTF-8') : $valor;
        }, $texto);
    }
}
