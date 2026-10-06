<?php
namespace Notificaciones\Dominio;

use Notificaciones\Dominio\Excepciones\CampoNoPermitido;

/**
 * Segunda defensa contra datos personales en notificaciones (la primera es
 * CodigoNotificacion::campos()). Rechaza cualquier dato cuyo nombre contenga
 * alguna de estas palabras, aunque un código lo declare por error.
 * Ver Politicas_Datos_GoforAgile_V1.pdf.
 */
final class CamposSensibles
{
    private const PALABRAS_PROHIBIDAS = [
        'documento', 'cedula', 'pasaporte', 'password', 'contrasena', 'cod_ingreso',
        'correo', 'email', 'telefono', 'celular', 'genero', 'sexo', 'nacimiento',
        'salario', 'sueldo', 'direccion', 'jefe', 'seguridad_social', 'estado_civil',
    ];

    public static function validar(array $nombresCampos): void
    {
        foreach ($nombresCampos as $nombre) {
            $nombreNormalizado = strtolower((string) $nombre);
            foreach (self::PALABRAS_PROHIBIDAS as $palabra) {
                if (str_contains($nombreNormalizado, $palabra)) {
                    throw new CampoNoPermitido("El campo '$nombre' es sensible y no se puede usar en notificaciones.");
                }
            }
        }
    }
}
