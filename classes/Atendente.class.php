<?php 

class Atendente 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function findById ($id) {
        try {
            $this->db->query = "SELECT * FROM atendentes WHERE CodAtendente = ? AND CodAtendente <> ''";
            $this->db->content = array($id, 'int');
            return $this->db->selectOne();
        } catch (\Throwable $th) {
            //var_dump($th);
            throw new Exception("Não foi possivel obter atendente[id:$id]");
        }
    }
}
