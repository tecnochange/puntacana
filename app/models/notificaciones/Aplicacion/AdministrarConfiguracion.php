<?php
namespace Notificaciones\Aplicacion;

use DateTimeImmutable;
use mysqli;
use Notificaciones\Dominio\ModoOperacion;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\ConfiguracionRepositorio;
use Notificaciones\Infraestructura\BaseDatos\EmpleadosLector;

/** Configuración del despacho de correos desde la plataforma (valores generales). */
final class AdministrarConfiguracion
{
    const CLAVES = [
        'modo_operacion', 'ids_empleados_prueba', 'correo_desvio_prueba',
        'hora_resumen', 'dia_semana_resumen', 'ventana_envio',
    ];
    private const HORA_MINIMA = 0;
    private const HORA_MAXIMA = 23;
    private const FIN_VENTANA_MAXIMO = 24;
    private const DIA_SEMANA_MINIMO = 1;
    private const DIA_SEMANA_MAXIMO = 7;

    /**
     * [clave => valor] + 'empleados_prueba' => [id => nombre]. $idEmpresa es la
     * del administrador: los empleados de prueba se buscan en ella.
     */
    public static function obtener(mysqli $mysqli, int $idEmpresa): array
    {
        $conexion = new Conexion($mysqli);
        $configuracion = (new ConfiguracionRepositorio($conexion))->valoresGenerales();
        foreach (self::CLAVES as $clave) {
            if (!isset($configuracion[$clave])) {
                $configuracion[$clave] = '';
            }
        }
        $ids = self::idsDesdeCsv($configuracion['ids_empleados_prueba']);
        $configuracion['empleados_prueba'] = (new EmpleadosLector($conexion))->nombresDeEmpresa($ids, $idEmpresa);
        return $configuracion;
    }

    /** Guarda el formulario. Devuelve los errores ([] = guardado). */
    public static function guardar(mysqli $mysqli, int $idEmpresa, array $formulario): array
    {
        $conexion = new Conexion($mysqli);
        $valor = function (string $campo) use ($formulario) {
            return isset($formulario[$campo]) ? trim((string) $formulario[$campo]) : '';
        };

        $errores = [];
        $modo = $valor('modo_operacion');
        if (!in_array($modo, ModoOperacion::valores(), true)) {
            $errores[] = 'Modo de operación inválido.';
        }

        $idsPrueba = self::idsDesdeCsv($valor('ids_empleados_prueba'));
        $existentes = (new EmpleadosLector($conexion))->nombresDeEmpresa($idsPrueba, $idEmpresa);
        $inexistentes = array_diff($idsPrueba, array_map('intval', array_keys($existentes)));
        if ($inexistentes) {
            $errores[] = 'Estos ids no son empleados: ' . implode(', ', $inexistentes) . '.';
        }

        $correoDesvio = $valor('correo_desvio_prueba');
        if ($correoDesvio !== '' && !filter_var($correoDesvio, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo de desvío no es válido.';
        }

        $hora = self::enteroEnRango($valor('hora_resumen'), self::HORA_MINIMA, self::HORA_MAXIMA);
        $diaSemana = self::enteroEnRango($valor('dia_semana_resumen'), self::DIA_SEMANA_MINIMO, self::DIA_SEMANA_MAXIMO);
        $inicioVentana = self::enteroEnRango($valor('inicio_ventana'), self::HORA_MINIMA, self::HORA_MAXIMA);
        $finVentana = self::enteroEnRango($valor('fin_ventana'), self::HORA_MINIMA + 1, self::FIN_VENTANA_MAXIMO);
        if ($hora === null) {
            $errores[] = 'La hora del resumen debe estar entre 0 y 23.';
        }
        if ($diaSemana === null) {
            $errores[] = 'Día de la semana inválido.';
        }
        if ($inicioVentana === null || $finVentana === null || $inicioVentana >= $finVentana) {
            $errores[] = 'La ventana de envío debe empezar antes de terminar.';
        }
        if ($errores) {
            return $errores;
        }

        $nuevos = [
            'modo_operacion' => $modo,
            'ids_empleados_prueba' => implode(',', $idsPrueba),
            'correo_desvio_prueba' => $correoDesvio,
            'hora_resumen' => (string) $hora,
            'dia_semana_resumen' => (string) $diaSemana,
            'ventana_envio' => sprintf('%02d-%02d', $inicioVentana, $finVentana),
        ];
        $configuraciones = new ConfiguracionRepositorio($conexion);
        $ahora = new DateTimeImmutable();
        $conexion->enTransaccion(function () use ($configuraciones, $nuevos, $ahora) {
            foreach ($nuevos as $clave => $valor) {
                $configuraciones->guardarGeneral($clave, $valor, $ahora);
            }
        });
        return [];
    }

    private static function idsDesdeCsv(string $csv): array
    {
        $ids = array_map('intval', array_filter(array_map('trim', explode(',', $csv)), 'strlen'));
        return array_values(array_unique(array_filter($ids, function ($id) {
            return $id > 0;
        })));
    }

    private static function enteroEnRango(string $valor, int $minimo, int $maximo): ?int
    {
        if ($valor === '' || !ctype_digit($valor)) {
            return null;
        }
        $entero = (int) $valor;
        return $entero >= $minimo && $entero <= $maximo ? $entero : null;
    }
}
