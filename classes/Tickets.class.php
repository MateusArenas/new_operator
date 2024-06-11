<?php

class Tickets 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    
    function motivos () {
        $motivos = array();
        $motivos[1] = 'Sistema fez a filtragem de forma incorreta.';
        $motivos[2] = 'Farol Vermelho por informações erradas.';
        $motivos[3] = 'Alterada informação de Óbito/Renajud.';
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
