<?php
namespace Notificaciones\Infraestructura\BaseDatos;

use DateTimeImmutable;
use Notificaciones\Dominio\EstadoTipo;
use Notificaciones\Dominio\Excepciones\TipoNoEncontrado;
use Notificaciones\Dominio\TipoNotificacion;

/** Tabla Notificaciones_Tipos. */
final class TiposRepositorio
{
    private const EMPRESA_POR_DEFECTO = 0;

    /** Columnas que la administración puede cambiar (codigo e id_empresa no) => tipo de mysqli_stmt_bind_param. */
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
     * El tipo que aplica a la empresa: su propia fila gana sobre la por defecto
     * (id_empresa = 0), también cuando está INACTIVO, así una empresa puede
     * apagar un tipo solo para ella.
     *
     * NULL si está INACTIVO (apagarlo es configuración, no un error). Lanza
     * TipoNoEncontrado si el código no tiene fila: eso sí es un error de
     * despliegue (falta el INSERT del tipo).
     */
    public function buscarActivo(string $codigo, int $idEmpresa): ?TipoNotificacion
    {
        $fila = $this->filaDeEmpresa($codigo, $idEmpresa);
        if ($fila === null) {
            throw new TipoNoEncontrado("El código '$codigo' no tiene fila en Notificaciones_Tipos (empresa $idEmpresa ni por defecto).");
        }
        return $fila['estado'] === EstadoTipo::ACTIVO ? TipoNotificacion::desdeFila($fila) : null;
    }

    /** El tipo con el que se generó una notificación. NULL si ya no existe o está INACTIVO. */
    public function buscarActivoPorId(int $idTipo): ?TipoNotificacion
    {
        $fila = $this->conexion->consultarUno('SELECT * FROM Notificaciones_Tipos WHERE id = ?', 'i', [$idTipo]);
        $estaActivo = $fila !== null && $fila['estado'] === EstadoTipo::ACTIVO;
        return $estaActivo ? TipoNotificacion::desdeFila($fila) : null;
    }

    /** Un tipo por id en cualquier estado (para las notificaciones de prueba). NULL si no existe. */
    public function buscarPorId(int $idTipo): ?TipoNotificacion
    {
        $fila = $this->conexion->consultarUno('SELECT * FROM Notificaciones_Tipos WHERE id = ?', 'i', [$idTipo]);
        return $fila === null ? null : TipoNotificacion::desdeFila($fila);
    }

    /** La fila que aplica a la empresa (la suya o la por defecto), en cualquier estado. NULL si no hay. */
    public function filaDeEmpresa(string $codigo, int $idEmpresa): ?array
    {
        return $this->conexion->consultarUno(
            'SELECT * FROM Notificaciones_Tipos
             WHERE codigo = ? AND id_empresa IN (?, ?)
             ORDER BY id_empresa DESC
             LIMIT 1',
            'sii',
            [$codigo, self::EMPRESA_POR_DEFECTO, $idEmpresa]
        );
    }

    /** Una fila por código: la de la empresa si la tiene, si no la por defecto. */
    public function filasDeEmpresa(int $idEmpresa): array
    {
        return $this->conexion->consultar(
            'SELECT t.* FROM Notificaciones_Tipos t
             WHERE t.id_empresa = ?
                OR (t.id_empresa = ? AND NOT EXISTS (
                       SELECT 1 FROM Notificaciones_Tipos propia
                       WHERE propia.codigo = t.codigo AND propia.id_empresa = ?))
             ORDER BY t.codigo',
            'iii',
            [$idEmpresa, self::EMPRESA_POR_DEFECTO, $idEmpresa]
        );
    }

    /**
     * Guarda la configuración del tipo como propia de la empresa: crea su fila
     * la primera vez y la actualiza después. La fila por defecto no se toca.
     * $valores: COLUMNAS_EDITABLES ya validadas.
     */
    public function guardarDeEmpresa(int $idEmpresa, string $codigo, array $valores, DateTimeImmutable $ahora): void
    {
        $nombresColumnas = array_keys(self::COLUMNAS_EDITABLES);
        $columnas = implode(', ', $nombresColumnas);
        $marcadores = implode(', ', array_fill(0, count($nombresColumnas), '?'));
        $actualizaciones = implode(', ', array_map(function ($columna) {
            return "$columna = VALUES($columna)";
        }, $nombresColumnas));

        $parametros = [];
        foreach ($nombresColumnas as $columna) {
            $parametros[] = $valores[$columna];
        }
        $fecha = Conexion::fecha($ahora);
        $this->conexion->modificar(
            "INSERT INTO Notificaciones_Tipos (id_empresa, codigo, $columnas, created_at)
             VALUES (?, ?, $marcadores, ?)
             ON DUPLICATE KEY UPDATE $actualizaciones, updated_at = ?",
            'is' . implode('', self::COLUMNAS_EDITABLES) . 'ss',
            array_merge([$idEmpresa, $codigo], $parametros, [$fecha, $fecha])
        );
    }
}
