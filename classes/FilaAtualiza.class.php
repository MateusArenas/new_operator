<?php 

class FilaAtualiza 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function findByCodConsulta ($codConsulta) {
        try {
            $this->db->query = "SELECT * FROM robo.tfila_atualiza WHERE consulta = ?";
            $this->db->content = array($codConsulta, 'int');
            return  $this->db->selectOne();
        } catch (\Throwable $th) {
            //var_dump($th);
            throw new Exception("Não foi possivel obter fila_atualiza[codConsulta:$codConsulta]");
        }
    }

    function priorizarConsulta ($codConsulta) {
        try {
            $this->db->query = "UPDATE robo.tfila_atualiza SET tentativa = 0, prioridade = 1 WHERE consulta = ?";
            $this->db->content = array($codConsulta, 'int');
            return $this->db->update();
        } catch (\Throwable $th) {
            //var_dump($th);
            throw new Exception("Não foi possivel priorizar fila_atualiza[codConsulta:$codConsulta]");
        }
    }

    function register ($codTipoConsulta, $codConsulta) {
        try {
            $this->db->query = "INSERT IGNORE INTO robo.tfila_atualiza (tipo, consulta, prioridade) VALUES (?, ?, 1)";
            $content = array();
            $content[] = array($codTipoConsulta, 'int');
            $content[] = array($codConsulta, 'int');
            $this->db->content = $content;
            return $this->db->insert();
        } catch (\Throwable $th) {
            //var_dump($th);
            throw new Exception("Não foi possivel inserir fila_atualiza[codConsulta:$codConsulta, tipoCod:$codTipoConsulta]");
        }
    }
}
