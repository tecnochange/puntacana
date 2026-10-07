<?php
namespace Notificaciones\Dominio;

use DateTimeImmutable;
use Notificaciones\Dominio\Excepciones\TipoMalConfigurado;

/**
 * Configuración de un tipo (fila de Notificaciones_Tipos), ya validada contra su
 * código. Si la fila no cuadra con CodigoNotificacion, no se construye.
 * Las propiedades son de solo lectura por convención: nada las modifica después
 * de desdeFila().
 */
final class TipoNotificacion
{
    /** @var int */
    public $id;
    /** @var string */
    public $codigo;
    /** @var string */
    public $nombre;
    /** @var string ClaseNotificacion::* */
    public $clase;
    /** @var int|null */
    public $diasAntesDeVencer;
    /** @var int|null */
    public $diasEntreRecordatorios;
    /** @var int|null */
    public $maximoRecordatorios;
    /** @var bool */
    public $incluyeAutor;
    /** @var bool */
    public $muestraEnBandeja;
    /** @var bool */
    public $enviaCorreo;
    /** @var int|null */
    public $intervaloResumen;
    /** @var string|null UnidadIntervalo::* */
    public $unidadIntervaloResumen;
    /** @var string */
    public $plantillaTitulo;
    /** @var string|null */
    public $plantillaMensaje;
    /** @var string|null */
    public $plantillaAsuntoCorreo;
    /** @var string|null */
    public $plantillaCuerpoCorreo;

    private function __construct()
    {
    }

    public static function desdeFila(array $fila): self
    {
        $tipo = new self();
        $tipo->id = (int) $fila['id'];
        $tipo->codigo = (string) $fila['codigo'];
        $tipo->nombre = (string) $fila['nombre'];
        $tipo->clase = (string) $fila['clase'];
        $tipo->diasAntesDeVencer = self::enteroNoNegativo($fila['dias_antes_de_vencer'], 'dias_antes_de_vencer', $tipo->codigo);
        $tipo->diasEntreRecordatorios = self::enteroNoNegativo($fila['dias_entre_recordatorios'], 'dias_entre_recordatorios', $tipo->codigo);
        $tipo->maximoRecordatorios = self::enteroNoNegativo($fila['maximo_recordatorios'], 'maximo_recordatorios', $tipo->codigo);
        $tipo->incluyeAutor = (bool) $fila['incluye_autor'];
        $tipo->muestraEnBandeja = (bool) $fila['muestra_en_bandeja'];
        $tipo->enviaCorreo = (bool) $fila['envia_correo'];
        $tipo->intervaloResumen = self::enteroNoNegativo($fila['intervalo_resumen'], 'intervalo_resumen', $tipo->codigo);
        $tipo->unidadIntervaloResumen = $fila['unidad_intervalo_resumen'];
        $tipo->plantillaTitulo = (string) $fila['plantilla_titulo'];
        $tipo->plantillaMensaje = $fila['plantilla_mensaje'];
        $tipo->plantillaAsuntoCorreo = $fila['plantilla_asunto_correo'];
        $tipo->plantillaCuerpoCorreo = $fila['plantilla_cuerpo_correo'];
        $tipo->validar();
        return $tipo;
    }

    /** Sus correos se agrupan en un resumen periódico en vez de salir uno por uno. */
    public function agrupaEnResumen(): bool
    {
        return $this->intervaloResumen !== null;
    }

    /** Cuándo va el primer correo. NULL si este tipo no envía correo. */
    public function fechaPrimerCorreo(?DateTimeImmutable $fechaVencimiento, DateTimeImmutable $ahora): ?DateTimeImmutable
    {
        if (!$this->enviaCorreo) {
            return null;
        }
        $avisaAntesDeVencer = $this->diasAntesDeVencer !== null && $fechaVencimiento !== null;
        if (!$avisaAntesDeVencer) {
            return $ahora;
        }
        $fechaCorreo = $fechaVencimiento->setTime(0, 0)->modify("-{$this->diasAntesDeVencer} days");
        return max($fechaCorreo, $ahora);
    }

    /** Cuándo va el siguiente recordatorio tras $intentosRealizados correos. NULL = ya no hay más. */
    public function fechaSiguienteCorreo(DateTimeImmutable $ultimoIntento, int $intentosRealizados, ?DateTimeImmutable $fechaVencimiento): ?DateTimeImmutable
    {
        if ($this->diasEntreRecordatorios === null || $this->diasEntreRecordatorios === 0) {
            return null;
        }
        $agotoRecordatorios = $this->maximoRecordatorios !== null && $intentosRealizados > $this->maximoRecordatorios;
        if ($agotoRecordatorios) {
            return null;
        }
        $siguiente = $ultimoIntento->modify("+{$this->diasEntreRecordatorios} days");
        $pasaDelVencimiento = $fechaVencimiento !== null && $siguiente > $fechaVencimiento;
        return $pasaDelVencimiento ? null : $siguiente;
    }

    private function validar(): void
    {
        if (!ClaseNotificacion::esValida($this->clase)) {
            throw new TipoMalConfigurado("Tipo '{$this->codigo}': clase inválida.");
        }

        $intervaloCompleto = $this->intervaloResumen !== null && $this->intervaloResumen > 0
            && $this->unidadIntervaloResumen !== null && UnidadIntervalo::esValida($this->unidadIntervaloResumen);
        $sinIntervalo = $this->intervaloResumen === null && $this->unidadIntervaloResumen === null;
        if (!$intervaloCompleto && !$sinIntervalo) {
            throw new TipoMalConfigurado("Tipo '{$this->codigo}': intervalo_resumen y unidad_intervalo_resumen van juntos (ambos NULL o ambos con valor).");
        }

        // Las plantillas solo pueden usar los datos del código (si ya está implementado) y los base.
        $permitidos = array_merge(CodigoNotificacion::datosPermitidosSiExiste($this->codigo), Plantilla::DATOS_BASE);
        $usados = array_merge(
            Plantilla::marcadores($this->plantillaTitulo),
            Plantilla::marcadores($this->plantillaMensaje),
            Plantilla::marcadores($this->plantillaAsuntoCorreo),
            Plantilla::marcadores($this->plantillaCuerpoCorreo)
        );
        $noPermitidos = array_diff($usados, $permitidos);
        if ($noPermitidos) {
            throw new TipoMalConfigurado("Tipo '{$this->codigo}': marcadores no permitidos: " . implode(', ', $noPermitidos));
        }

        $faltaPlantillaDeCorreo = $this->plantillaAsuntoCorreo === null || $this->plantillaCuerpoCorreo === null;
        if ($this->enviaCorreo && $faltaPlantillaDeCorreo) {
            throw new TipoMalConfigurado("Tipo '{$this->codigo}': envia_correo sin plantilla de asunto o de cuerpo.");
        }
    }

    private static function enteroNoNegativo($valor, string $columna, string $codigo): ?int
    {
        if ($valor === null || $valor === '') {
            return null;
        }
        $entero = (int) $valor;
        if ($entero < 0) {
            throw new TipoMalConfigurado("Tipo '$codigo': $columna no puede ser negativo.");
        }
        return $entero;
    }
}
