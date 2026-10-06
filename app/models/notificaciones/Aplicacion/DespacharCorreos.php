<?php
namespace Notificaciones\Aplicacion;

use DateTimeImmutable;
use mysqli;
use Notificaciones\Dominio\CalendarioResumen;
use Notificaciones\Dominio\Excepciones\ErrorNotificacion;
use Notificaciones\Dominio\ModoOperacion;
use Notificaciones\Dominio\Plantilla;
use Notificaciones\Dominio\ResultadoEnvio;
use Notificaciones\Dominio\TipoNotificacion;
use Notificaciones\Infraestructura\BaseDatos\ConfiguracionRepositorio;
use Notificaciones\Infraestructura\BaseDatos\Conexion;
use Notificaciones\Infraestructura\BaseDatos\DestinatariosRepositorio;
use Notificaciones\Infraestructura\BaseDatos\EmpleadosLector;
use Notificaciones\Infraestructura\BaseDatos\EnviosRepositorio;
use Notificaciones\Infraestructura\BaseDatos\NotificacionesRepositorio;
use Notificaciones\Infraestructura\BaseDatos\TiposRepositorio;
use Notificaciones\Infraestructura\Correo\MarcoCorreo;

/**
 * Lo corre el cron cada pocos minutos:
 *  1. marca VENCIDA las TAREA cuya fecha_vencimiento pasó y cancela sus correos pendientes;
 *  2. envía uno por uno los correos de tipos sin intervalo_resumen (y programa el siguiente recordatorio);
 *  3. para tipos con intervalo_resumen, cuando es la hora de su resumen (CalendarioResumen),
 *     junta lo pendiente de cada empleado en un solo correo.
 *
 * Respeta el modo_operacion de cada empresa (APAGADO / SOLO_BANDEJA / PRUEBA /
 * ACTIVO) y su ventana_envio. Cada correo se reserva antes de enviarlo, así dos
 * corridas cruzadas no lo duplican.
 */
final class DespacharCorreos
{
    private const LIMITE_INDIVIDUALES_POR_CORRIDA = 200;
    /** Más alto: las asignaciones masivas (miles en un día) caen en los resúmenes. */
    private const LIMITE_AGRUPADOS_POR_CORRIDA = 2000;
    private const HORAS_PARA_CADUCAR = 48;
    private const RUTA_ABRIR = 'api/notificaciones/abrir.php?id=';
    private const ASUNTO_RESUMEN = 'Tienes %d notificaciones nuevas';
    private const VENTANA_ENVIO_POR_DEFECTO = '07-19';
    private const HORA_RESUMEN_POR_DEFECTO = '7';
    private const DIA_SEMANA_RESUMEN_POR_DEFECTO = '1';

    /** @var ProveedorCorreo */
    private $proveedorCorreo;
    /** @var string */
    private $urlBase;
    /** @var DateTimeImmutable */
    private $ahora;
    /** @var NotificacionesRepositorio */
    private $notificaciones;
    /** @var DestinatariosRepositorio */
    private $destinatarios;
    /** @var EnviosRepositorio */
    private $envios;
    /** @var EmpleadosLector */
    private $empleados;
    /** @var TiposRepositorio */
    private $tipos;
    /** @var ConfiguracionRepositorio */
    private $configuraciones;

    /** @var array */
    private $configuracionPorEmpresa = [];
    /** @var array */
    private $tipoPorId = [];
    /** @var array */
    private $totales = ['vencidas' => 0, 'enviados' => 0, 'fallidos' => 0, 'omitidos' => 0];

    private function __construct(mysqli $mysqli, ProveedorCorreo $proveedorCorreo, string $urlBase, DateTimeImmutable $ahora)
    {
        $this->proveedorCorreo = $proveedorCorreo;
        $this->urlBase = $urlBase;
        $this->ahora = $ahora;

        $conexion = new Conexion($mysqli);
        $this->notificaciones = new NotificacionesRepositorio($conexion);
        $this->destinatarios = new DestinatariosRepositorio($conexion);
        $this->envios = new EnviosRepositorio($conexion);
        $this->empleados = new EmpleadosLector($conexion);
        $this->tipos = new TiposRepositorio($conexion);
        $this->configuraciones = new ConfiguracionRepositorio($conexion);
    }

    /** $urlBase: raíz del portal, p. ej. https://puntacana.goforagile.com/ */
    public static function ejecutar(mysqli $mysqli, ProveedorCorreo $proveedorCorreo, string $urlBase, ?DateTimeImmutable $ahora = null): array
    {
        $despacho = new self($mysqli, $proveedorCorreo, rtrim($urlBase, '/') . '/', $ahora ?: new DateTimeImmutable());
        return $despacho->despachar();
    }

    private function despachar(): array
    {
        $this->totales['vencidas'] = $this->notificaciones->marcarTareasVencidas($this->ahora);
        $this->destinatarios->cancelarCorreosDeCerradas();

        $empresasQuePuedenEnviar = [];
        foreach ($this->destinatarios->empresasConCorreoPendiente($this->ahora) as $idEmpresa) {
            if ($this->puedeEnviarAhora($this->configuracion($idEmpresa))) {
                $empresasQuePuedenEnviar[] = $idEmpresa;
            }
        }

        $individuales = $this->destinatarios->pendientesDeCorreo($this->ahora, false, $empresasQuePuedenEnviar, self::LIMITE_INDIVIDUALES_POR_CORRIDA);
        foreach ($individuales as $pendiente) {
            $tipo = $this->tipo((int) $pendiente['id_tipo']);
            if ($tipo === null) {
                $this->omitirPorTipoInactivo($pendiente);
                continue;
            }
            $this->enviarCorreoIndividual($pendiente, $tipo);
        }

        $resumenesPorEmpleado = [];
        $agrupados = $this->destinatarios->pendientesDeCorreo($this->ahora, true, $empresasQuePuedenEnviar, self::LIMITE_AGRUPADOS_POR_CORRIDA);
        foreach ($agrupados as $pendiente) {
            $tipo = $this->tipo((int) $pendiente['id_tipo']);
            if ($tipo === null) {
                $this->omitirPorTipoInactivo($pendiente);
                continue;
            }
            if ($this->entraEnResumenActual($pendiente, $tipo)) {
                $resumenesPorEmpleado[$pendiente['id_empresa']][$pendiente['id_empleado']][] = ['pendiente' => $pendiente, 'tipo' => $tipo];
            }
        }
        foreach ($resumenesPorEmpleado as $idEmpresa => $porEmpleado) {
            foreach ($porEmpleado as $idEmpleado => $items) {
                $this->enviarResumen((int) $idEmpresa, (int) $idEmpleado, $items);
            }
        }
        return $this->totales;
    }

    private function enviarCorreoIndividual(array $pendiente, TipoNotificacion $tipo): void
    {
        if (!$this->reservar($pendiente, $tipo)) {
            return;
        }
        $idEmpresa = (int) $pendiente['id_empresa'];
        $idEmpleado = (int) $pendiente['id_empleado'];
        $empleado = $this->empleados->paraEnvio($idEmpleado, $idEmpresa);

        $motivoOmision = $this->motivoCaducado($pendiente);
        if ($motivoOmision === null) {
            $motivoOmision = $this->motivoOmisionEmpleado($this->configuracion($idEmpresa), $idEmpleado, $empleado);
        }
        if ($motivoOmision !== null) {
            $this->registrarCorreoOmitido($pendiente, $motivoOmision);
            return;
        }

        $datos = $this->datosPlantilla($pendiente, $empleado['nombre']);
        $asunto = Plantilla::renderizarTexto($tipo->plantillaAsuntoCorreo, $datos);
        $html = MarcoCorreo::envolver(Plantilla::renderizarHtml($tipo->plantillaCuerpoCorreo, $datos));

        $respuesta = $this->proveedorCorreo->enviar($empleado['nombre'], $empleado['correo'], $asunto, $html);
        $this->registrarRespuesta($pendiente, $asunto, $respuesta);
    }

    /** @param array $items [['pendiente' => fila, 'tipo' => TipoNotificacion], ...] de un mismo empleado */
    private function enviarResumen(int $idEmpresa, int $idEmpleado, array $items): void
    {
        $vigentes = [];
        foreach ($items as $item) {
            if (!$this->reservar($item['pendiente'], $item['tipo'])) {
                continue;
            }
            $caducado = $this->motivoCaducado($item['pendiente']);
            if ($caducado !== null) {
                $this->registrarCorreoOmitido($item['pendiente'], $caducado);
                continue;
            }
            $vigentes[] = $item['pendiente'];
        }
        if (!$vigentes) {
            return;
        }

        $empleado = $this->empleados->paraEnvio($idEmpleado, $idEmpresa);
        $motivoOmision = $this->motivoOmisionEmpleado($this->configuracion($idEmpresa), $idEmpleado, $empleado);
        if ($motivoOmision !== null) {
            foreach ($vigentes as $pendiente) {
                $this->registrarCorreoOmitido($pendiente, $motivoOmision);
            }
            return;
        }

        $asunto = sprintf(self::ASUNTO_RESUMEN, count($vigentes));
        $html = MarcoCorreo::envolver($this->contenidoResumen($empleado['nombre'], $vigentes));
        $respuesta = $this->proveedorCorreo->enviar($empleado['nombre'], $empleado['correo'], $asunto, $html);
        foreach ($vigentes as $pendiente) {
            $this->registrarRespuesta($pendiente, $asunto, $respuesta);
        }
    }

    private function contenidoResumen(string $nombreEmpleado, array $pendientes): string
    {
        $items = '';
        foreach ($pendientes as $pendiente) {
            $enlace = htmlspecialchars($this->enlace($pendiente), ENT_QUOTES, 'UTF-8');
            $titulo = htmlspecialchars($pendiente['titulo'], ENT_QUOTES, 'UTF-8');
            $fechaVencimiento = Plantilla::formatearFecha($this->fechaVencimiento($pendiente));
            $vence = $fechaVencimiento === '' ? '' : " (vence $fechaVencimiento)";
            $items .= "<li><a href=\"$enlace\">$titulo</a>$vence</li>";
        }
        $nombreSeguro = htmlspecialchars($nombreEmpleado, ENT_QUOTES, 'UTF-8');
        return "<p>Hola $nombreSeguro,</p><p>Tienes estas notificaciones nuevas:</p><ul>$items</ul>";
    }

    /** Reserva el correo y programa el siguiente recordatorio. false si otra corrida lo reservó. */
    private function reservar(array $pendiente, ?TipoNotificacion $tipo): bool
    {
        $intentosRealizados = (int) $pendiente['cantidad_intentos_correo'] + 1;
        $fechaSiguienteCorreo = $tipo === null
            ? null
            : $tipo->fechaSiguienteCorreo($this->ahora, $intentosRealizados, $this->fechaVencimiento($pendiente));
        return $this->destinatarios->reservarCorreo((int) $pendiente['id'], $pendiente['fecha_proximo_correo'], $fechaSiguienteCorreo, $this->ahora);
    }

    /** Un correo que lleva días esperando (p. ej. el módulo estuvo APAGADO) ya no se manda. */
    private function motivoCaducado(array $pendiente): ?string
    {
        $limiteCaducidad = $this->ahora->modify('-' . self::HORAS_PARA_CADUCAR . ' hours');
        $estaCaducado = new DateTimeImmutable($pendiente['fecha_proximo_correo']) < $limiteCaducidad;
        return $estaCaducado ? ResultadoEnvio::OMITIDO_CADUCADO : null;
    }

    private function motivoOmisionEmpleado(array $configuracion, int $idEmpleado, ?array $empleado): ?string
    {
        $modo = $configuracion['modo_operacion'];
        $noEstaEnLaPrueba = $modo === ModoOperacion::PRUEBA && !in_array($idEmpleado, $configuracion['ids_empleados_prueba'], true);
        if ($modo === ModoOperacion::SOLO_BANDEJA || $noEstaEnLaPrueba) {
            return ResultadoEnvio::OMITIDO_MODO;
        }
        if ($empleado === null || !$empleado['activo']) {
            return ResultadoEnvio::OMITIDO_EMPLEADO_INACTIVO;
        }
        if (!filter_var($empleado['correo'], FILTER_VALIDATE_EMAIL)) {
            return ResultadoEnvio::OMITIDO_SIN_CORREO;
        }
        return null;
    }

    private function omitirPorTipoInactivo(array $pendiente): void
    {
        if ($this->reservar($pendiente, null)) {
            $this->registrarCorreoOmitido($pendiente, ResultadoEnvio::OMITIDO_TIPO_INACTIVO);
        }
    }

    private function registrarCorreoOmitido(array $pendiente, string $resultado): void
    {
        $this->envios->registrar((int) $pendiente['id_empresa'], (int) $pendiente['id'], (int) $pendiente['id_empleado'], null, $resultado, $this->ahora);
        $this->totales['omitidos']++;
    }

    private function registrarRespuesta(array $pendiente, string $asunto, RespuestaProveedor $respuesta): void
    {
        $this->envios->registrar(
            (int) $pendiente['id_empresa'],
            (int) $pendiente['id'],
            (int) $pendiente['id_empleado'],
            $asunto,
            $respuesta->exitosa ? ResultadoEnvio::ENVIADO : ResultadoEnvio::FALLIDO,
            $this->ahora,
            $respuesta->idMensaje,
            $respuesta->mensajeError
        );
        $this->totales[$respuesta->exitosa ? 'enviados' : 'fallidos']++;
    }

    private function datosPlantilla(array $pendiente, string $nombreDestinatario): array
    {
        $datos = json_decode((string) $pendiente['datos_plantilla'], true) ?: [];
        return $datos + [
            'destinatario_nombre' => $nombreDestinatario,
            'fecha_vencimiento' => Plantilla::formatearFecha($this->fechaVencimiento($pendiente)),
            'enlace' => $this->enlace($pendiente),
        ];
    }

    private function enlace(array $pendiente): string
    {
        return $this->urlBase . self::RUTA_ABRIR . (int) $pendiente['id_notificacion'];
    }

    private function fechaVencimiento(array $pendiente): ?DateTimeImmutable
    {
        return $pendiente['fecha_vencimiento'] === null ? null : new DateTimeImmutable($pendiente['fecha_vencimiento']);
    }

    private function puedeEnviarAhora(array $configuracion): bool
    {
        $hora = (int) $this->ahora->format('G');
        $dentroDeVentana = $hora >= $configuracion['hora_inicio_ventana'] && $hora < $configuracion['hora_fin_ventana'];
        return $configuracion['modo_operacion'] !== ModoOperacion::APAGADO && $dentroDeVentana;
    }

    /** Es la hora del resumen de su tipo y el correo ya estaba pendiente cuando ese resumen empezó. */
    private function entraEnResumenActual(array $pendiente, TipoNotificacion $tipo): bool
    {
        $configuracion = $this->configuracion((int) $pendiente['id_empresa']);
        $inicioResumen = CalendarioResumen::inicioResumenActual(
            $this->ahora,
            $tipo->intervaloResumen,
            $tipo->unidadIntervaloResumen,
            $configuracion['hora_resumen'],
            $configuracion['dia_semana_resumen']
        );
        return $inicioResumen !== null && new DateTimeImmutable($pendiente['fecha_proximo_correo']) <= $inicioResumen;
    }

    private function configuracion(int $idEmpresa): array
    {
        if (!isset($this->configuracionPorEmpresa[$idEmpresa])) {
            $ventana = explode('-', $this->configuraciones->valor($idEmpresa, 'ventana_envio', self::VENTANA_ENVIO_POR_DEFECTO));
            $idsPrueba = array_filter(array_map('trim', explode(',', $this->configuraciones->valor($idEmpresa, 'ids_empleados_prueba'))), 'strlen');

            $this->configuracionPorEmpresa[$idEmpresa] = [
                'modo_operacion' => ModoOperacion::desdeValor($this->configuraciones->valor($idEmpresa, 'modo_operacion')),
                'ids_empleados_prueba' => array_map('intval', $idsPrueba),
                'hora_resumen' => (int) $this->configuraciones->valor($idEmpresa, 'hora_resumen', self::HORA_RESUMEN_POR_DEFECTO),
                'dia_semana_resumen' => (int) $this->configuraciones->valor($idEmpresa, 'dia_semana_resumen', self::DIA_SEMANA_RESUMEN_POR_DEFECTO),
                'hora_inicio_ventana' => (int) $ventana[0],
                'hora_fin_ventana' => isset($ventana[1]) ? (int) $ventana[1] : 0,
            ];
        }
        return $this->configuracionPorEmpresa[$idEmpresa];
    }

    /** NULL si el tipo está INACTIVO, ya no existe, ya no está en CodigoNotificacion o está mal configurado. */
    private function tipo(int $idTipo): ?TipoNotificacion
    {
        if (!array_key_exists($idTipo, $this->tipoPorId)) {
            try {
                $this->tipoPorId[$idTipo] = $this->tipos->buscarActivoPorId($idTipo);
            } catch (ErrorNotificacion $error) {
                $this->tipoPorId[$idTipo] = null;
            }
        }
        return $this->tipoPorId[$idTipo];
    }
}
