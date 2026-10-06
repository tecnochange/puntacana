<?php
namespace Notificaciones\Dominio;

use DateTimeImmutable;
use Notificaciones\Dominio\Excepciones\TipoMalConfigurado;

/**
 * Configuración de un tipo, ya validada contra su código. Si una fila de
 * Notificaciones_Tipos no cuadra con CodigoNotificacion, no se construye.
 * Las propiedades son de solo lectura por convención: nada las modifica
 * después de desdeFila().
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
    public $diasAnticipacion;
    /** @var int|null */
    public $recordarCadaDias;
    /** @var int|null */
    public $maxRecordatorios;
    /** @var bool */
    public $notificarAutor;
    /** @var bool */
    public $canalPlataforma;
    /** @var bool */
    public $canalCorreo;
    /** @var string ModoCorreo::* */
    public $modoCorreo;
    /** @var string|null */
    public $correoAsunto;
    /** @var string|null */
    public $correoCuerpo;
    /** @var string */
    public $plataformaTitulo;
    /** @var string|null */
    public $plataformaCuerpo;

    private function __construct()
    {
    }

    public static function desdeFila(array $fila, string $codigo): self
    {
        $clase = (string) $fila['clase'];
        $modoCorreo = (string) $fila['modo_correo'];
        if (!ClaseNotificacion::esValida($clase) || !ModoCorreo::esValido($modoCorreo)) {
            throw new TipoMalConfigurado("Tipo '$codigo': clase o modo_correo inválidos.");
        }

        $tipo = new self();
        $tipo->id = (int) $fila['id'];
        $tipo->codigo = $codigo;
        $tipo->nombre = (string) $fila['nombre'];
        $tipo->clase = $clase;
        $tipo->diasAnticipacion = self::enteroNoNegativo($fila['dias_anticipacion'], 'dias_anticipacion', $codigo);
        $tipo->recordarCadaDias = self::enteroNoNegativo($fila['recordar_cada_dias'], 'recordar_cada_dias', $codigo);
        $tipo->maxRecordatorios = self::enteroNoNegativo($fila['max_recordatorios'], 'max_recordatorios', $codigo);
        $tipo->notificarAutor = SiNo::esSi($fila['notificar_autor']);
        $tipo->canalPlataforma = SiNo::esSi($fila['canal_plataforma']);
        $tipo->canalCorreo = SiNo::esSi($fila['canal_correo']);
        $tipo->modoCorreo = $modoCorreo;
        $tipo->correoAsunto = $fila['correo_asunto'];
        $tipo->correoCuerpo = $fila['correo_cuerpo'];
        $tipo->plataformaTitulo = (string) $fila['plataforma_titulo'];
        $tipo->plataformaCuerpo = $fila['plataforma_cuerpo'];
        $tipo->validarPlantillas();
        return $tipo;
    }

    /** Cuándo va el primer correo. NULL si este tipo no envía correo. */
    public function primerAviso(?DateTimeImmutable $fechaLimite, DateTimeImmutable $ahora): ?DateTimeImmutable
    {
        if (!$this->canalCorreo) {
            return null;
        }
        $avisaConAnticipacion = $this->diasAnticipacion !== null && $fechaLimite !== null;
        if (!$avisaConAnticipacion) {
            return $ahora;
        }
        $aviso = $fechaLimite->setTime(0, 0)->modify("-{$this->diasAnticipacion} days");
        return max($aviso, $ahora);
    }

    /** Cuándo va el siguiente recordatorio tras $avisosDados avisos. NULL = ya no hay más. */
    public function siguienteAviso(DateTimeImmutable $ultimoAviso, int $avisosDados, ?DateTimeImmutable $fechaLimite): ?DateTimeImmutable
    {
        if ($this->recordarCadaDias === null || $this->recordarCadaDias === 0) {
            return null;
        }
        $agotoRecordatorios = $this->maxRecordatorios !== null && $avisosDados > $this->maxRecordatorios;
        if ($agotoRecordatorios) {
            return null;
        }
        $siguiente = $ultimoAviso->modify("+{$this->recordarCadaDias} days");
        $pasaDeLaFecha = $fechaLimite !== null && $siguiente > $fechaLimite;
        return $pasaDeLaFecha ? null : $siguiente;
    }

    /** Las plantillas solo pueden usar los campos del código y los base. */
    private function validarPlantillas(): void
    {
        $permitidos = array_merge(CodigoNotificacion::campos($this->codigo), Plantilla::CAMPOS_BASE);
        $usados = array_merge(
            Plantilla::marcadores($this->correoAsunto),
            Plantilla::marcadores($this->correoCuerpo),
            Plantilla::marcadores($this->plataformaTitulo),
            Plantilla::marcadores($this->plataformaCuerpo)
        );
        $noPermitidos = array_diff($usados, $permitidos);
        if ($noPermitidos) {
            throw new TipoMalConfigurado("Tipo '{$this->codigo}': marcadores no permitidos: " . implode(', ', $noPermitidos));
        }
        if ($this->canalCorreo && ($this->correoAsunto === null || $this->correoCuerpo === null)) {
            throw new TipoMalConfigurado("Tipo '{$this->codigo}': tiene correo activo pero sin asunto o cuerpo.");
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
