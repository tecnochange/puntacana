<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use DateTimeImmutable;
use mysqli;
use mysqli_sql_exception;
use mysqli_stmt;

/**
 * Sentencias preparadas sobre mysqli. Todo el SQL del módulo pasa por aquí:
 * ningún valor se concatena en una consulta.
 */
final class Consulta
{
    /** Formato de DATETIME en la BD. */
    const FORMATO_FECHA = 'Y-m-d H:i:s';

    /** @var mysqli */
    private $conexion;

    public function __construct(mysqli $conexion)
    {
        $this->conexion = $conexion;
    }

    /** Fecha lista para un parámetro DATETIME (NULL se queda NULL). */
    public static function fecha(?DateTimeImmutable $fecha): ?string
    {
        return $fecha === null ? null : $fecha->format(self::FORMATO_FECHA);
    }

    public function filas(string $sql, string $tipos = '', array $parametros = []): array
    {
        $sentencia = $this->ejecutarSentencia($sql, $tipos, $parametros);
        $resultado = mysqli_stmt_get_result($sentencia);
        $filas = $resultado ? mysqli_fetch_all($resultado, MYSQLI_ASSOC) : [];
        mysqli_stmt_close($sentencia);
        return $filas;
    }

    public function fila(string $sql, string $tipos = '', array $parametros = []): ?array
    {
        $filas = $this->filas($sql, $tipos, $parametros);
        return $filas ? $filas[0] : null;
    }

    /** Ejecuta un INSERT/UPDATE/DELETE y devuelve las filas afectadas. */
    public function ejecutar(string $sql, string $tipos = '', array $parametros = []): int
    {
        $sentencia = $this->ejecutarSentencia($sql, $tipos, $parametros);
        $afectadas = mysqli_stmt_affected_rows($sentencia);
        mysqli_stmt_close($sentencia);
        return $afectadas;
    }

    public function ultimoId(): int
    {
        return (int) mysqli_insert_id($this->conexion);
    }

    /** Ejecuta $operacion dentro de una transacción; si lanza, deshace todo. */
    public function transaccion(callable $operacion)
    {
        mysqli_begin_transaction($this->conexion);
        try {
            $resultado = $operacion();
            mysqli_commit($this->conexion);
            return $resultado;
        } catch (\Throwable $error) {
            mysqli_rollback($this->conexion);
            throw $error;
        }
    }

    private function ejecutarSentencia(string $sql, string $tipos, array $parametros): mysqli_stmt
    {
        try {
            $sentencia = mysqli_prepare($this->conexion, $sql);
            if ($sentencia === false) {
                throw new ErrorBaseDatos(mysqli_error($this->conexion), mysqli_errno($this->conexion));
            }
            if ($tipos !== '') {
                mysqli_stmt_bind_param($sentencia, $tipos, ...$parametros);
            }
            if (!mysqli_stmt_execute($sentencia)) {
                throw new ErrorBaseDatos(mysqli_stmt_error($sentencia), mysqli_stmt_errno($sentencia));
            }
            return $sentencia;
        } catch (mysqli_sql_exception $error) {
            throw new ErrorBaseDatos($error->getMessage(), $error->getCode(), $error);
        }
    }
}
