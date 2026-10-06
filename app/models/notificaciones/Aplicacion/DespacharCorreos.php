<?php
namespace Notificaciones\Aplicacion;

use DateTimeImmutable;
use mysqli;
use Notificaciones\Dominio\CodigoNotificacion;
use Notificaciones\Dominio\Excepciones\ErrorNotificacion;
use Notificaciones\Dominio\ModoCorreo;
use Notificaciones\Dominio\ModoOperacion;
use Notificaciones\Dominio\Plantilla;
use Notificaciones\Dominio\TipoNotificacion;
use Notificaciones\Infraestructura\BaseDatos\ConfiguracionRepositorio;
use Notificaciones\Infraestructura\BaseDatos\Consulta;
use Notificaciones\Infraestructura\BaseDatos\DestinatariosRepositorio;
use Notificaciones\Infraestructura\BaseDatos\EmpleadosLector;
use Notificaciones\Infraestructura\BaseDatos\EnviosRepositorio;
use Notificaciones\Infraestructura\BaseDatos\EventosRepositorio;
use Notificaciones\Infraestructura\BaseDatos\TiposRepositorio;
use Notificaciones\Infraestructura\Correo\MarcoCorreo;

/**
 * Lo corre el cron cada pocos minutos:
 *  1. vence las tareas cuya fecha límite pasó y quita sus avisos pendientes;
 *  2. envía los correos inmediatos que ya tocan (y programa el siguiente recordatorio);
 *  3. a la hora del resumen, junta los de modo resumen_diario en un correo por persona.
 *
 * El modo de correo se toma del tipo con el que se creó la notificación
 * (Notificaciones_Eventos.id_tipo).
 *
 * Respeta el modo de cada empresa (apagado / solo_plataforma / prueba / activo)
 * y la ventana de horas de envío. Cada aviso se reclama antes de enviarlo, así
 * dos corridas cruzadas no duplican correos.
 */
final class DespacharCorreos
{
    private const LIMITE_POR_CORRIDA = 200;
    /** Más alto: las asignaciones masivas (miles en un día) se resumen en la hora del resumen. */
    private const LIMITE_RESUMEN_POR_CORRIDA = 2000;
    private const HORAS_PARA_CADUCAR = 48;
    private const RUTA_ABRIR = 'api/notificaciones/abrir.php?id=';
    private const ASUNTO_RESUMEN = 'Tienes %d notificaciones nuevas';
    private const VENTANA_POR_DEFECTO = '07-19';
    private const HORA_RESUMEN_POR_DEFECTO = '7';

    /** @var EnviadorCorreo */
    private $enviador;
    /** @var string */
    private $urlBase;
    /** @var DateTimeImmutable */
    private $ahora;
    /** @var EventosRepositorio */
    private $eventos;
    /** @var DestinatariosRepositorio */
    private $bandejas;
    /** @var EnviosRepositorio */
    private $envios;
    /** @var EmpleadosLector */
    private $empleados;
    /** @var TiposRepositorio */
    private $tipos;
    /** @var ConfiguracionRepositorio */
    private $configuraciones;

    /** @var array */
    private $cacheConfiguracion = [];
    /** @var array */
    private $cacheTipos = [];
    /** @var array */
    private $contadores = ['vencidas' => 0, 'enviados' => 0, 'fallidos' => 0, 'omitidos' => 0];

    private function __construct(mysqli $conexion, EnviadorCorreo $enviador, string $urlBase, DateTimeImmutable $ahora)
    {
        $this->enviador = $enviador;
        $this->urlBase = $urlBase;
        $this->ahora = $ahora;

        $consulta = new Consulta($conexion);
        $this->eventos = new EventosRepositorio($consulta);
        $this->bandejas = new DestinatariosRepositorio($consulta);
        $this->envios = new EnviosRepositorio($consulta);
        $this->empleados = new EmpleadosLector($consulta);
        $this->tipos = new TiposRepositorio($consulta);
        $this->configuraciones = new ConfiguracionRepositorio($consulta);
    }

    /** $urlBase: raíz del portal, p. ej. https://puntacana.goforagile.com/ */
    public static function ejecutar(mysqli $conexion, EnviadorCorreo $enviador, string $urlBase, ?DateTimeImmutable $ahora = null): array
    {
        $despacho = new self($conexion, $enviador, rtrim($urlBase, '/') . '/', $ahora ?: new DateTimeImmutable());
        return $despacho->correr();
    }

    private function correr(): array
    {
        $this->contadores['vencidas'] = $this->eventos->marcarTareasVencidas($this->ahora);
        $this->bandejas->limpiarAvisosDeCerradas();

        $empresasHabilitadas = [];
        foreach ($this->bandejas->empresasConAvisoPendiente($this->ahora) as $idEmpresa) {
            if ($this->puedeEnviarAhora($this->configuracion($idEmpresa))) {
                $empresasHabilitadas[] = $idEmpresa;
            }
        }

        $inmediatos = $this->bandejas->conAvisoPendiente($this->ahora, ModoCorreo::INMEDIATO, $empresasHabilitadas, self::LIMITE_POR_CORRIDA);
        foreach ($inmediatos as $fila) {
            $tipo = $this->tipo($fila['codigo'], (int) $fila['id_empresa']);
            if ($tipo === null) {
                $this->omitirSinTipo($fila);
                continue;
            }
            $this->enviarInmediato($fila, $tipo, $this->configuracion((int) $fila['id_empresa']));
        }

        $empresasEnHoraDeResumen = [];
        foreach ($empresasHabilitadas as $idEmpresa) {
            if ($this->esHoraDeResumen($this->configuracion($idEmpresa))) {
                $empresasEnHoraDeResumen[] = $idEmpresa;
            }
        }
        $paraResumen = [];
        $resumenes = $this->bandejas->conAvisoPendiente($this->ahora, ModoCorreo::RESUMEN_DIARIO, $empresasEnHoraDeResumen, self::LIMITE_RESUMEN_POR_CORRIDA);
        foreach ($resumenes as $fila) {
            $tipo = $this->tipo($fila['codigo'], (int) $fila['id_empresa']);
            if ($tipo === null) {
                $this->omitirSinTipo($fila);
                continue;
            }
            if ($this->entraEnResumenDeHoy($this->configuracion((int) $fila['id_empresa']), $fila)) {
                $paraResumen[$fila['id_empresa']][$fila['id_empleado']][] = ['fila' => $fila, 'tipo' => $tipo];
            }
        }

        foreach ($paraResumen as $idEmpresa => $porEmpleado) {
            foreach ($porEmpleado as $idEmpleado => $items) {
                $this->enviarResumen((int) $idEmpresa, (int) $idEmpleado, $items, $this->configuracion((int) $idEmpresa));
            }
        }
        return $this->contadores;
    }

    private function enviarInmediato(array $fila, TipoNotificacion $tipo, array $configuracion): void
    {
        if (!$this->reclamar($fila, $tipo)) {
            return;
        }
        $idEmpresa = (int) $fila['id_empresa'];
        $idEmpleado = (int) $fila['id_empleado'];
        $empleado = $this->empleados->paraEnvio($idEmpleado, $idEmpresa);

        $motivo = $this->motivoCaducado($fila);
        if ($motivo === null) {
            $motivo = $this->motivoOmisionPersona($configuracion, $idEmpleado, $empleado);
        }
        if ($motivo !== null) {
            $this->omitir($fila, $motivo);
            return;
        }

        $valores = $this->valoresPlantilla($fila, $empleado['nombre']);
        $asunto = Plantilla::renderizarTexto($tipo->correoAsunto, $valores);
        $html = MarcoCorreo::envolver(Plantilla::renderizarHtml($tipo->correoCuerpo, $valores));

        $resultado = $this->enviador->enviar($empleado['nombre'], $empleado['correo'], $asunto, $html);
        $this->registrarResultado($fila, $asunto, $resultado);
    }

    /** @param array $items [['fila' => ..., 'tipo' => TipoNotificacion], ...] de una misma persona */
    private function enviarResumen(int $idEmpresa, int $idEmpleado, array $items, array $configuracion): void
    {
        $vigentes = [];
        foreach ($items as $item) {
            if (!$this->reclamar($item['fila'], $item['tipo'])) {
                continue;
            }
            $caducado = $this->motivoCaducado($item['fila']);
            if ($caducado !== null) {
                $this->omitir($item['fila'], $caducado);
                continue;
            }
            $vigentes[] = $item['fila'];
        }
        if (!$vigentes) {
            return;
        }

        $empleado = $this->empleados->paraEnvio($idEmpleado, $idEmpresa);
        $motivo = $this->motivoOmisionPersona($configuracion, $idEmpleado, $empleado);
        if ($motivo !== null) {
            foreach ($vigentes as $fila) {
                $this->omitir($fila, $motivo);
            }
            return;
        }

        $asunto = sprintf(self::ASUNTO_RESUMEN, count($vigentes));
        $html = MarcoCorreo::envolver($this->contenidoResumen($empleado['nombre'], $vigentes));
        $resultado = $this->enviador->enviar($empleado['nombre'], $empleado['correo'], $asunto, $html);
        foreach ($vigentes as $fila) {
            $this->registrarResultado($fila, $asunto, $resultado);
        }
    }

    private function contenidoResumen(string $nombre, array $filas): string
    {
        $items = '';
        foreach ($filas as $fila) {
            $enlace = htmlspecialchars($this->enlace($fila), ENT_QUOTES, 'UTF-8');
            $titulo = htmlspecialchars($fila['titulo'], ENT_QUOTES, 'UTF-8');
            $fechaLimite = Plantilla::formatearFecha($this->fechaLimite($fila));
            $vence = $fechaLimite === '' ? '' : " (vence $fechaLimite)";
            $items .= "<li><a href=\"$enlace\">$titulo</a>$vence</li>";
        }
        $nombreSeguro = htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8');
        return "<p>Hola $nombreSeguro,</p><p>Tienes estas notificaciones nuevas:</p><ul>$items</ul>";
    }

    /** Toma el aviso y programa el siguiente recordatorio. false si otra corrida lo tomó. */
    private function reclamar(array $fila, ?TipoNotificacion $tipo): bool
    {
        $avisosDados = (int) $fila['cantidad_avisos'] + 1;
        $siguiente = $tipo === null ? null : $tipo->siguienteAviso($this->ahora, $avisosDados, $this->fechaLimite($fila));
        return $this->bandejas->reclamarAviso((int) $fila['id'], $fila['proximo_aviso'], $siguiente, $this->ahora);
    }

    /** Un aviso que lleva días esperando (p. ej. el módulo estuvo apagado) ya no se manda. */
    private function motivoCaducado(array $fila): ?string
    {
        $limiteCaducidad = $this->ahora->modify('-' . self::HORAS_PARA_CADUCAR . ' hours');
        $estaCaducado = new DateTimeImmutable($fila['proximo_aviso']) < $limiteCaducidad;
        return $estaCaducado ? EnviosRepositorio::OMITIDO_CADUCADO : null;
    }

    private function motivoOmisionPersona(array $configuracion, int $idEmpleado, ?array $empleado): ?string
    {
        $modo = $configuracion['modo'];
        $fueraDeLaPrueba = $modo === ModoOperacion::PRUEBA && !in_array($idEmpleado, $configuracion['lista_prueba'], true);
        if ($modo === ModoOperacion::SOLO_PLATAFORMA || $fueraDeLaPrueba) {
            return EnviosRepositorio::OMITIDO_MODO;
        }
        if ($empleado === null || !$empleado['activo']) {
            return EnviosRepositorio::OMITIDO_INACTIVO;
        }
        if (!filter_var($empleado['correo'], FILTER_VALIDATE_EMAIL)) {
            return EnviosRepositorio::OMITIDO_SIN_CORREO;
        }
        return null;
    }

    private function omitirSinTipo(array $fila): void
    {
        if ($this->reclamar($fila, null)) {
            $this->omitir($fila, EnviosRepositorio::OMITIDO_TIPO);
        }
    }

    private function omitir(array $fila, string $motivo): void
    {
        $this->envios->registrar((int) $fila['id_empresa'], (int) $fila['id'], (int) $fila['id_empleado'], null, $motivo, $this->ahora);
        $this->contadores['omitidos']++;
    }

    private function registrarResultado(array $fila, string $asunto, ResultadoEnvio $resultado): void
    {
        $estado = $resultado->exitoso ? EnviosRepositorio::ENVIADO : EnviosRepositorio::FALLIDO;
        $this->envios->registrar(
            (int) $fila['id_empresa'],
            (int) $fila['id'],
            (int) $fila['id_empleado'],
            $asunto,
            $estado,
            $this->ahora,
            $resultado->idMensaje,
            $resultado->error
        );
        $this->contadores[$resultado->exitoso ? 'enviados' : 'fallidos']++;
    }

    private function valoresPlantilla(array $fila, string $nombreDestinatario): array
    {
        $datos = json_decode((string) $fila['datos'], true) ?: [];
        return $datos + [
            'destinatario_nombre' => $nombreDestinatario,
            'fecha_limite' => Plantilla::formatearFecha($this->fechaLimite($fila)),
            'enlace' => $this->enlace($fila),
        ];
    }

    private function enlace(array $fila): string
    {
        return $this->urlBase . self::RUTA_ABRIR . (int) $fila['id_evento'];
    }

    private function fechaLimite(array $fila): ?DateTimeImmutable
    {
        return $fila['fecha_limite'] === null ? null : new DateTimeImmutable($fila['fecha_limite']);
    }

    private function puedeEnviarAhora(array $configuracion): bool
    {
        $hora = (int) $this->ahora->format('G');
        $dentroDeVentana = $hora >= $configuracion['ventana_inicio'] && $hora < $configuracion['ventana_fin'];
        return $configuracion['modo'] !== ModoOperacion::APAGADO && $dentroDeVentana;
    }

    private function esHoraDeResumen(array $configuracion): bool
    {
        return (int) $this->ahora->format('G') === $configuracion['hora_resumen'];
    }

    /** El resumen solo incluye lo acumulado hasta la hora del resumen; lo posterior va mañana. */
    private function entraEnResumenDeHoy(array $configuracion, array $fila): bool
    {
        $corteDelDia = $this->ahora->setTime($configuracion['hora_resumen'], 0);
        return new DateTimeImmutable($fila['proximo_aviso']) <= $corteDelDia;
    }

    private function configuracion(int $idEmpresa): array
    {
        if (!isset($this->cacheConfiguracion[$idEmpresa])) {
            $ventana = explode('-', $this->configuraciones->valor($idEmpresa, 'ventana_envio', self::VENTANA_POR_DEFECTO));
            $listaPrueba = array_filter(array_map('trim', explode(',', $this->configuraciones->valor($idEmpresa, 'lista_prueba'))), 'strlen');

            $this->cacheConfiguracion[$idEmpresa] = [
                'modo' => ModoOperacion::desdeValor($this->configuraciones->valor($idEmpresa, 'modo')),
                'lista_prueba' => array_map('intval', $listaPrueba),
                'hora_resumen' => (int) $this->configuraciones->valor($idEmpresa, 'hora_resumen', self::HORA_RESUMEN_POR_DEFECTO),
                'ventana_inicio' => (int) $ventana[0],
                'ventana_fin' => isset($ventana[1]) ? (int) $ventana[1] : 0,
            ];
        }
        return $this->cacheConfiguracion[$idEmpresa];
    }

    /** NULL si el código ya no está en CodigoNotificacion o su tipo está inactivo o mal configurado. */
    private function tipo(string $codigo, int $idEmpresa): ?TipoNotificacion
    {
        $llave = "$idEmpresa|$codigo";
        if (!array_key_exists($llave, $this->cacheTipos)) {
            try {
                $this->cacheTipos[$llave] = CodigoNotificacion::existe($codigo) ? $this->tipos->buscarActivo($codigo, $idEmpresa) : null;
            } catch (ErrorNotificacion $error) {
                $this->cacheTipos[$llave] = null;
            }
        }
        return $this->cacheTipos[$llave];
    }
}
