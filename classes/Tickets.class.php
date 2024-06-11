<?php

class Tickets 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    
    function motivos() {
        $motivos = array();
        $motivos[0] = 'Outros';
        $motivos[1] = 'Erro de página não encontrada (404)';
        $motivos[2] = 'Problema de autenticação ou login';
        $motivos[3] = 'Erro de validação de formulário';
        $motivos[4] = 'Problema de conexão com o banco de dados';
        $motivos[5] = 'Erro de sintaxe ou semântica no código';
        $motivos[6] = 'Funcionalidade não está respondendo conforme esperado';
        $motivos[7] = 'Problema de exibição ou layout quebrado';
        $motivos[8] = 'Erro de manipulação de dados (inserção, atualização ou exclusão)';
        $motivos[9] = 'Desempenho lento ou tempo de resposta excessivo';
        $motivos[10] = 'Erro de integração com sistemas externos';
        return $motivos;
    }
    

    function create ($reason, $description, $channel_id, $user_id) {
        try {
            $this->db->query = "INSERT INTO tickets (reason, description, channel_id, user_id) VALUES (?, ?, ?, ?)";

            $this->db->content = array();
            $this->db->content[] = array($reason);
            $this->db->content[] = array($description);
            $this->db->content[] = array($channel_id);
            $this->db->content[] = array($user_id);

           return  $this->db->insert();
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }


    function findById ($ticket_id) {
        try {
            $this->db->query = "SELECT * FROM tickets WHERE id = ?";
            $this->db->content = array();
            $this->db->content[] = array($ticket_id);
           return $this->db->selectOne();
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    function findAll ($offset, $limit) {
        try {
            $this->db->query = "
                SELECT tickets.*, users.*
                FROM tickets
                INNER JOIN users ON tickets.user_id = users.id
                LIMIT ? OFFSET ?
            ";
            $this->db->content = array();
            $this->db->content[] = array($limit ?: 1000, 'int');
            $this->db->content[] = array($offset ?: 0, 'int');
           return $this->db->select();
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    function countAll () {
        try {
            $this->db->query = "SELECT * FROM tickets";
            $this->db->content = array();
           return $this->db->countAll();
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }
}
