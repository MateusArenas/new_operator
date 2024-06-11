<?php

class Tickets 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function status() {
        $status = array();
        $motivos[0] = 'AGUARDANDO';
        $motivos[1] = 'EM ANDAMENTO';
        $motivos[2] = 'CONCLUÍDO';
        $motivos[3] = 'FORA DO PRAZO';
        return $motivos;
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

    function updateStatus ($ticket_id, $operator_id, $status) {
        try {

            $this->db->query = "UPDATE tickets SET status = ?, operator_id = ? WHERE id = ?";

            $this->db->content = array();
            $this->db->content[] = array($status, 'int');
            $this->db->content[] = array($operator_id, 'int');
            $this->db->content[] = array($ticket_id, 'int');

           return  $this->db->update();
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }
    

    function create ($title, $reason, $description, $channel_id, $user_id) {
        try {
            $this->db->query = "INSERT INTO tickets (title, reason, description, channel_id, user_id) 
            VALUES (?, ?, ?, ?, ?)
            ";

            $this->db->content = array();
            $this->db->content[] = array($title);
            $this->db->content[] = array($reason);
            $this->db->content[] = array($description);
            $this->db->content[] = array($channel_id);
            $this->db->content[] = array($user_id, 'int');

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
                SELECT 
                    tickets.*, 

                    users.id AS user_id, 
                    users.image_url AS user_image_url, 
                    users.nome AS user_name, 
                    users.type AS user_type, 
                    users.email AS user_email, 

                    operators.id AS operator_id, 
                    operators.image_url AS operator_image_url, 
                    operators.nome AS operator_name, 
                    operators.type AS operator_type, 
                    operators.email AS operator_email

                FROM tickets
                INNER JOIN users ON tickets.user_id = users.id
                LEFT JOIN users AS operators ON tickets.operator_id = operators.id
                ORDER BY id DESC
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
            $this->db->query = "SELECT * FROM tickets LIMIT ?";
            $this->db->content = array();
            $this->db->content[] = array(1000, 'int');

           return $this->db->countRows();
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }
}
