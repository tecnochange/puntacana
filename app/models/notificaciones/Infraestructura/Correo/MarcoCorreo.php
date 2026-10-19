<?php
namespace Notificaciones\Infraestructura\Correo;

use DateTimeImmutable;

/**
 * Encabezado, pie y estilos comunes de todos los correos. El contenido ya viene
 * escapado (Plantilla::renderizarHtml).
 *
 * Los clientes de correo ignoran casi todo el CSS de <style>, así que los estilos
 * van en línea y en px, y el diseño se arma con tablas. Los colores son los de la
 * web (azul #007bff de la cabecera y el menú). Las plantillas de los tipos son
 * HTML simple (<p>, <a>, <strong>, <ul>): aquí se les da estilo, y un párrafo que
 * solo tiene un enlace (la acción principal) se convierte en botón.
 */
final class MarcoCorreo
{
    private const URL_PORTAL = 'https://puntacana.goforagile.com/';
    /** El logo de Go For Agile solo existe en blanco: va sobre el encabezado azul. */
    private const LOGO = 'assets/img/logo_white.png';
    private const RUTA_BANDEJA = '?pg=notificaciones/bandeja';

    private const AZUL = '#007bff';
    private const AZUL_OSCURO = '#0062cc';
    private const FONDO = '#e6eaef';
    private const TINTA = '#1f2937';
    private const TEXTO = '#374151';
    private const GRIS = '#6b7280';
    private const BORDE = '#e5e9f0';
    private const FUENTE = "Poppins,'Segoe UI',Roboto,Helvetica,Arial,sans-serif";
    /** [fondo, texto] de cada tono de etiqueta. */
    private const TONOS = [
        'azul' => ['#e7f1ff', '#0062cc'],
        'naranja' => ['#fff4e5', '#c2410c'],
        'rojo' => ['#fee2e2', '#b91c1c'],
        'gris' => ['#f1f3f5', '#495057'],
    ];
    private const DIAS_URGENTE = 1;
    private const DIAS_PRONTO = 3;

    /**
     * $cabecera (todo opcional):
     *  - 'etiquetas' => [['texto' => 'Tarea', 'tono' => 'azul'], ...]
     *  - 'titulo'    => título grande sobre el contenido (texto plano)
     *  - 'resumen'   => texto que el cliente de correo muestra junto al asunto (texto plano)
     */
    public static function envolver(string $contenidoHtml, array $cabecera = []): string
    {
        $portal = self::URL_PORTAL;
        $enlacePie = 'color:' . self::AZUL . ';font-weight:600;text-decoration:none;';
        $resumen = isset($cabecera['resumen']) ? self::escapar($cabecera['resumen']) : '';
        return '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8">'
            . '<meta name="viewport" content="width=device-width,initial-scale=1">'
            // Los clientes que entienden <style> achican los márgenes en celular; los demás usan los de escritorio.
            . '<style>@media only screen and (max-width:620px){.notif-cuerpo{padding:28px 22px 26px !important;}.notif-marco{padding:16px 8px !important;}.notif-titulo{font-size:20px !important;}}</style>'
            . '</head><body style="margin:0;padding:0;background:' . self::FONDO . ';">'
            . ($resumen !== '' ? '<div style="display:none;max-height:0;overflow:hidden;mso-hide:all;">' . $resumen . '</div>' : '')
            . '<table role="presentation" class="notif-marco" width="100%" cellpadding="0" cellspacing="0" style="background:' . self::FONDO . ';padding:32px 12px;font-family:' . self::FUENTE . ';">'
            . '<tr><td align="center">'
            . '<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;">'
            // Encabezado azul con el logo; si el cliente bloquea imágenes se ve el texto alternativo en blanco.
            . '<tr><td align="center" bgcolor="' . self::AZUL . '" style="background:' . self::AZUL . ';background-image:linear-gradient(135deg,' . self::AZUL . ' 0%,' . self::AZUL_OSCURO . ' 100%);border-radius:16px 16px 0 0;padding:26px 24px 22px;">'
            . '<img src="' . $portal . self::LOGO . '" width="116" alt="Go For Agile" style="display:block;width:116px;max-width:116px;height:auto;border:0;color:#ffffff;font-size:20px;font-weight:700;">'
            . '</td></tr>'
            // Tarjeta con el contenido.
            . '<tr><td class="notif-cuerpo" bgcolor="#ffffff" style="background:#ffffff;border-radius:0 0 16px 16px;padding:34px 40px 36px;box-shadow:0 8px 24px rgba(16,24,40,0.08);color:' . self::TEXTO . ';font-size:15px;line-height:1.7;">'
            . self::cabeceraDelContenido($cabecera)
            . self::darEstilo($contenidoHtml)
            . '</td></tr>'
            // Pie fuera de la tarjeta.
            . '<tr><td align="center" style="padding:22px 24px 4px;font-size:12px;line-height:1.7;color:' . self::GRIS . ';">'
            . '<a href="' . $portal . self::RUTA_BANDEJA . '" style="' . $enlacePie . '">Ver mis notificaciones</a>'
            . '<span style="color:#c4cad4;">&nbsp;&nbsp;&bull;&nbsp;&nbsp;</span>'
            . '<a href="' . $portal . '" style="' . $enlacePie . '">Ir a Go For Agile</a><br>'
            . 'Es un mensaje automático de Go For Agile, por favor no respondas a esta dirección.'
            . '</td></tr>'
            . '</table></td></tr></table></body></html>';
    }

    /** Etiquetas de una notificación: su clase y, si es una tarea con fecha, cuánto falta. */
    public static function etiquetas(string $clase, ?DateTimeImmutable $vencimiento, DateTimeImmutable $ahora): array
    {
        $esTarea = $clase === 'TAREA';
        $etiquetas = [['texto' => $esTarea ? 'Tarea' : 'Aviso', 'tono' => $esTarea ? 'azul' : 'gris']];
        if ($esTarea && $vencimiento !== null) {
            $dias = (int) $ahora->setTime(0, 0)->diff($vencimiento->setTime(0, 0))->format('%r%a');
            if ($dias < 0) {
                $etiquetas[] = ['texto' => 'Venció', 'tono' => 'rojo'];
            } elseif ($dias === 0) {
                $etiquetas[] = ['texto' => 'Vence hoy', 'tono' => 'rojo'];
            } elseif ($dias <= self::DIAS_URGENTE) {
                $etiquetas[] = ['texto' => 'Vence mañana', 'tono' => 'rojo'];
            } else {
                $etiquetas[] = ['texto' => 'Vence en ' . $dias . ' días', 'tono' => $dias <= self::DIAS_PRONTO ? 'naranja' : 'azul'];
            }
        }
        return $etiquetas;
    }

    /**
     * Lista del resumen: una tarjeta por notificación.
     * @param array $items [['titulo' => texto plano, 'enlace' => url, 'detalle' => texto plano o ''], ...]
     */
    public static function tarjetas(array $items): string
    {
        $html = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:6px 0 4px;">';
        foreach ($items as $item) {
            $detalle = $item['detalle'] !== ''
                ? '<div style="font-size:12px;color:' . self::GRIS . ';margin-top:3px;">' . self::escapar($item['detalle']) . '</div>'
                : '';
            $enlace = self::escapar($item['enlace']);
            $html .= '<tr><td style="padding:0 0 10px;">'
                . '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid ' . self::BORDE . ';border-radius:12px;">'
                . '<tr><td style="padding:14px 18px;">'
                . '<a style="font-size:14px;font-weight:600;color:' . self::TINTA . ';text-decoration:none;line-height:1.45;" href="' . $enlace . '">' . self::escapar($item['titulo']) . '</a>'
                . $detalle . '</td>'
                . '<td align="right" width="40" style="padding:14px 18px 14px 0;vertical-align:middle;">'
                . '<a style="font-size:18px;font-weight:700;color:' . self::AZUL . ';text-decoration:none;" href="' . $enlace . '">&rarr;</a>'
                . '</td></tr></table></td></tr>';
        }
        return $html . '</table>';
    }

    private static function cabeceraDelContenido(array $cabecera): string
    {
        $html = '';
        foreach (isset($cabecera['etiquetas']) ? $cabecera['etiquetas'] : [] as $etiqueta) {
            [$fondo, $color] = self::TONOS[$etiqueta['tono']] ?? self::TONOS['gris'];
            $html .= '<span style="display:inline-block;background:' . $fondo . ';color:' . $color . ';font-size:11px;font-weight:700;letter-spacing:0.06em;'
                . 'text-transform:uppercase;padding:5px 12px;border-radius:999px;margin:0 6px 6px 0;">' . self::escapar($etiqueta['texto']) . '</span>';
        }
        if (isset($cabecera['titulo']) && $cabecera['titulo'] !== '') {
            $html .= '<h1 class="notif-titulo" style="margin:' . ($html !== '' ? '14px' : '0') . ' 0 18px;font-size:23px;line-height:1.3;font-weight:700;color:#000000;">'
                . self::escapar($cabecera['titulo']) . '</h1>';
        }
        return $html;
    }

    /** Estilos en línea para las etiquetas simples de las plantillas. */
    private static function darEstilo(string $html): string
    {
        // Un párrafo que solo contiene un enlace es la acción principal: botón.
        $html = preg_replace_callback('#<p>\s*<a href="([^"]*)">(.*?)</a>\s*</p>#s', function (array $m) {
            return '<table role="presentation" cellpadding="0" cellspacing="0" style="margin:26px 0 4px;"><tr>'
                . '<td bgcolor="' . self::AZUL . '" style="background:' . self::AZUL . ';border-radius:10px;box-shadow:0 4px 12px rgba(0,123,255,0.25);">'
                // style antes de href: así el reemplazo de los enlaces normales (abajo) no lo toca.
                . '<a style="display:inline-block;padding:14px 30px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:10px;" href="' . $m[1] . '">'
                . $m[2] . '&nbsp;&nbsp;&rarr;</a>'
                . '</td></tr></table>';
        }, $html);
        $html = str_replace('<p>', '<p style="margin:0 0 14px;">', $html);
        $html = str_replace('<ul>', '<ul style="margin:4px 0 14px;padding-left:20px;">', $html);
        $html = str_replace('<li>', '<li style="margin:0 0 10px;">', $html);
        $html = str_replace('<strong>', '<strong style="color:' . self::TINTA . ';">', $html);
        return str_replace('<a href=', '<a style="color:' . self::AZUL . ';font-weight:600;text-decoration:none;" href=', $html);
    }

    private static function escapar(string $texto): string
    {
        return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
    }
}
