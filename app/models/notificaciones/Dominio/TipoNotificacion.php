<?php
namespace Notificaciones\Dominio;

use DateTimeImmutable;
use Notificaciones\Dominio\Excepciones\TipoMalConfigurado;

/**
 * Configuración de un tipo, ya validada contra su código. Si una fila de
 * Notificaciones_Tipos no cuadra con el enum, no se construye.
 */
final class TipoNotificacion
{
    private function __construct(
        public readonly int $id,
        public readonly CodigoNotificacion $codigo,
        public readonly string $nombre,
        public readonly ClaseNotificacion $clase,
        public readonly ?int $diasAnticipacion,
        public readonly ?int $recordarCadaDias,
        public readonly ?int $maxRecordatorios,
        public readonly bool $notificarAutor,
        public readonly bool $canalPlataforma,
        public readonly bool $canalCorreo,
        public readonly ModoCorreo $modoCorreo,
        public readonly ?string $correoAsunto,
        public readonly ?string $correoCuerpo,
        public readonly string $plataformaTitulo,
        public readonly ?string $plataformaCuerpo,
    ) {
    }

    public static function desdeFila(array $fila, CodigoNotificacion $codigo): self
    {
        $clase = ClaseNotificacion::tryFrom((string) $fila['clase']);
        $modoCorreo = ModoCorreo::tryFrom((string) $fila['modo_correo']);
        if ($clase === null || $modoCorreo === null) {
            throw new TipoMalConfigurado("Tipo '{$codigo->value}': clase o modo_correo inválidos.");
        }

        $tipo = new self(
            (int) $fila['id'],
            $codigo,
            (string) $fila['nombre'],
            $clase,
            self::enteroNoNegativo($fila['dias_anticipacion'], 'dias_anticipacion', $codigo),
            self::enteroNoNegativo($fila['recordar_cada_dias'], 'recordar_cada_dias', $codigo),
            self::enteroNoNegativo($fila['max_recordatorios'], 'max_recordatorios', $codigo),
            (bool) $fila['notificar_autor'],
            (bool) $fila['canal_plataforma'],
            (bool) $fila['canal_correo'],
            $modoCorreo,
            $fila['correo_asunto'],
            $fila['correo_cuerpo'],
            (string) $fila['plataforma_titulo'],
            $fila['plataforma_cuerpo'],
        );
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
        $permitidos = array_merge($this->codigo->campos(), Plantilla::CAMPOS_BASE);
        $usados = array_merge(
            Plantilla::marcadores($this->correoAsunto),
            Plantilla::marcadores($this->correoCuerpo),
            Plantilla::marcadores($this->plataformaTitulo),
            Plantilla::marcadores($this->plataformaCuerpo),
        );
        $noPermitidos = array_diff($usados, $permitidos);
        if ($noPermitidos) {
            throw new TipoMalConfigurado("Tipo '{$this->codigo->value}': marcadores no permitidos: " . implode(', ', $noPermitidos));
        }
        if ($this->canalCorreo && ($this->correoAsunto === null || $this->correoCuerpo === null)) {
            throw new TipoMalConfigurado("Tipo '{$this->codigo->value}': tiene correo activo pero sin asunto o cuerpo.");
        }
    }

    private static function enteroNoNegativo(mixed $valor, string $columna, CodigoNotificacion $codigo): ?int
    {
        if ($valor === null || $valor === '') {
            return null;
        }
        $entero = (int) $valor;
        if ($entero < 0) {
            throw new TipoMalConfigurado("Tipo '{$codigo->value}': $columna no puede ser negativo.");
        }
        return $entero;
    }
}
