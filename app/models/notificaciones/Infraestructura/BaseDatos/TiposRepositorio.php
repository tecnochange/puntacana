<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use DateTimeImmutable;
use Notificaciones\Dominio\EstadoTipo;
use Notificaciones\Dominio\Excepciones\TipoNoEncontrado;
use Notificaciones\Dominio\TipoNotificacion;

/**
 * Tabla Notificaciones_Tipos. La plataforma tiene una sola empresa en uso, así
 * que la administración trabaja con la fila general de cada tipo
 * (id_empresa = 0): una fila por código, con id estable. La lectura en tiempo
 * de ejecución sigue aceptando una fila propia de empresa si algún día existe.
 */
final class TiposRepositorio
{
    private const EMPRESA_GENERAL = 0;

    /** Columnas que la administración puede cambiar (el código no) => tipo de mysqli_stmt_bind_param. */
    const COLUMNAS_EDITABLES = [
        'nombre' => 's',
        'descripcion' => 's',
        'clase' => 's',
        'dias_antes_de_vencer' => 'i',
        'dias_entre_recordatorios' => 'i',
        'maximo_recordatorios' => 'i',
        'incluye_autor' => 'i',
        'muestra_en_bandeja' => 'i',
        'envia_correo' => 'i',
        'intervalo_resumen' => 'i',
        'unidad_intervalo_resumen' => 's',
        'plantilla_titulo' => 's',
        'plantilla_mensaje' => 's',
        'plantilla_asunto_correo' => 's',
        'plantilla_cuerpo_correo' => 's',
        'icono' => 's',
        'color' => 's',
        'estado' => 's',
    ];

    /** @var Conexion */
    private $conexion;

    public function __construct(Conexion $conexion)
    {
        $this->conexion = $conexion;
    }

    /**
     * El tipo que aplica a la empresa: su propia fila si la tuviera, si no la
     * general. NULL si está INACTIVO (apagarlo es configuración, no un error).
     * Lanza TipoNoEncontrado si el código no tiene fila: eso sí es un error de
     * despliegue (falta crear el tipo).
     */
    public function buscarActivo(string $codigo, int $idEmpresa): ?TipoNotificacion
    {
        $fila = $this->conexion->consultarUno(
            'SELECT * FROM Notificaciones_Tipos
             WHERE codigo = ? AND id_empresa IN (?, ?)
             ORDER BY id_empresa DESC
             LIMIT 1',
            'sii',
            [$codigo, self::EMPRESA_GENERAL, $idEmpresa]
        );
        if ($fila === null) {
            throw new TipoNoEncontrado("El código '$codigo' no tiene fila en Notificaciones_Tipos.");
        }
        return $fila['estado'] === EstadoTipo::ACTIVO ? TipoNotificacion::desdeFila($fila) : null;
    }

    /** El tipo con el que se generó una notificación. NULL si ya no existe o está INACTIVO. */
    public function buscarActivoPorId(int $idTipo): ?TipoNotificacion
    {
        $fila = $this->filaPorId($idTipo);
        $estaActivo = $fila !== null && $fila['estado'] === EstadoTipo::ACTIVO;
        return $estaActivo ? TipoNotificacion::desdeFila($fila) : null;
    }

    /** Un tipo por id en cualquier estado (para las notificaciones de prueba). NULL si no existe. */
    public function buscarPorId(int $idTipo): ?TipoNotificacion
    {
        $fila = $this->filaPorId($idTipo);
        return $fila === null ? null : TipoNotificacion::desdeFila($fila);
    }

    public function filaPorId(int $idTipo): ?array
    {
        return $this->conexion->consultarUno('SELECT * FROM Notificaciones_Tipos WHERE id = ?', 'i', [$idTipo]);
    }

    /** Todos los tipos generales, por nombre. */
    public function filasGenerales(): array
    {
        return $this->conexion->consultar(
            'SELECT * FROM Notificaciones_Tipos WHERE id_empresa = ? ORDER BY nombre',
            'i',
            [self::EMPRESA_GENERAL]
        );
    }

    public function existeCodigo(string $codigo): bool
    {
        return $this->conexion->consultarUno('SELECT id FROM Notificaciones_Tipos WHERE codigo = ? LIMIT 1', 's', [$codigo]) !== null;
    }

    /** Crea el tipo general y devuelve su id. $valores: COLUMNAS_EDITABLES ya validadas. */
    public function crear(string $codigo, array $valores, DateTimeImmutable $ahora): int
    {
        $nombresColumnas = array_keys(self::COLUMNAS_EDITABLES);
        $columnas = implode(', ', $nombresColumnas);
        $marcadores = implode(', ', array_fill(0, count($nombresColumnas), '?'));
        $this->conexion->modificar(
            "INSERT INTO Notificaciones_Tipos (id_empresa, codigo, $columnas, created_at)
             VALUES (?, ?, $marcadores, ?)",
            'is' . implode('', self::COLUMNAS_EDITABLES) . 's',
            array_merge([self::EMPRESA_GENERAL, $codigo], self::valoresEnOrden($valores), [Conexion::fecha($ahora)])
        );
        return $this->conexion->ultimoIdInsertado();
    }

    /** Actualiza el tipo. $valores: COLUMNAS_EDITABLES ya validadas. */
    public function actualizar(int $idTipo, array $valores, DateTimeImmutable $ahora): void
    {
        $asignaciones = implode(', ', array_map(function ($columna) {
            return "$columna = ?";
        }, array_keys(self::COLUMNAS_EDITABLES)));
        $this->conexion->modificar(
            "UPDATE Notificaciones_Tipos SET $asignaciones, updated_at = ? WHERE id = ?",
            implode('', self::COLUMNAS_EDITABLES) . 'si',
            array_merge(self::valoresEnOrden($valores), [Conexion::fecha($ahora), $idTipo])
        );
    }

    private static function valoresEnOrden(array $valores): array
    {
        $ordenados = [];
        foreach (array_keys(self::COLUMNAS_EDITABLES) as $columna) {
            $ordenados[] = $valores[$columna];
        }
        return $ordenados;
    }
}
