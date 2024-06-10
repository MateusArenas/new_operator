<?php 

class FilaExclusao 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function deleteByCodConsulta ($codConsulta) {
        try {
            $this->db->query = "DELETE FROM robo.tfila_exclusao WHERE consulta = ?";
            $this->db->content = array($codConsulta, 'int');
            return $this->db->delete();
        } catch (\Throwable $th) {
            //var_dump($th);
            throw new Exception("Não foi possivel deletar consulta em fila_exclusao[codConsulta:$codConsulta]");
        }
    }
}
