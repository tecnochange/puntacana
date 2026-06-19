<?php
class IntervencionGh {
    private $db;
    public function __construct($db)
    {
        $this->db = $db;
    }

    public function guardarYCerrarIntervencionGH($data){
        /**Verificar si ya existe una intervención con esos paramétros.*/
        $sqlCheck = $this->db->prepare("select id, comentario FROM Intervencion_Gh WHERE id_empresa = ? AND id_responsable = ? AND id_evaluado = ? AND evaluaciones = ? AND ciclo = ?");
        $sqlCheck->execute([$data['empresa'],$data['interventor'],$data['evaluado'],$data['evaluacion'],$data['ciclo']]);
        $comentarioExistente = $sqlCheck->fetch(PDO::FETCH_ASSOC);
        if($comentarioExistente){
            if($data['descripcion']!== $comentarioExistente['comentario']){
                $upCheck = $this->db->prepare("UPDATE Intervencion_Gh SET comentario = ? WHERE id_empresa = ? AND id_responsable = ? AND id_evaluado = ? AND evaluaciones = ? AND ciclo = ?");
                $upCheck->execute([$data['descripcion'], $data['empresa'],$data['interventor'],$data['evaluado'],$data['evaluacion'],$data['ciclo']]);
            }
            //Ya existe: El comentario del interventor
            $this->actualizarEstadoEvaluacionByGH($data['evaluacion'],8);
            return [
                'id' => $comentarioExistente['id'],
                'comentario' => $comentarioExistente['comentario']
            ];
        }

        /*Registrar comentario del interventor*/
        $sql = $this->db->prepare("Insert into `Intervencion_Gh` (id_empresa, id_responsable, id_evaluado, evaluaciones, comentario, ciclo, estado, created_at, updated_at) VALUE (?,?,?,?,?,?,?,?,?)");

        /**Actualizar evaluacion al estado de Proceso: Proceso Cerrado por GH*/
        $success = $sql->execute([$data['empresa'],$data['interventor'],$data['evaluado'],$data['evaluacion'],$data['descripcion'],$data['ciclo'],1,$data['fecha'],$data['fecha']]);

        if($success){
            //Obtener ID registrado
            $idInsertado = $this->db->lastInsertId();
            //Actualiza la evalucion
            $this->actualizarEstadoEvaluacionByGH($data['evaluacion'],8);

            //Obtener los datos del comentario recién registrado
            $sqlGet = $this->db->prepare("SELECT comentario FROM `Intervencion_Gh` WHERE id = ?");
            $sqlGet->execute([$idInsertado]);
            $comentario = $sqlGet->fetchColumn();

            return [
                'id' => $idInsertado,
                'comentario' => $comentario
            ];
        }
        return false;
    }

    public function actualizarEstadoEvaluacionByGH($idEvaluacion, $proceso_valoracion)
    {
        $sql = $this->db->prepare("UPDATE `Competencias_Evaluaciones_New` SET proceso_valoracion = ? WHERE id = ?");
        return $sql->execute([$proceso_valoracion, $idEvaluacion]);
    }
}