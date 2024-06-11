<?php

class Users 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function tipos() {
        $tipos = array();
        $tipos[1] = 'Cliente';
        $tipos[2] = 'Colaborador';
        return $tipos;
    }

    function register ($nome, $email, $password, $type, $cpf, $slack_id = '') {
        try {
            $this->db->query = "INSERT INTO users (nome, email, password, type, cpf, slack_id) VALUES (?, ?, ?, ?, ?, ?)";

            $hash = password_hash($password, PASSWORD_BCRYPT, array('cost' => 12));

            $this->db->content = array();
            $this->db->content[] = array($nome);
            $this->db->content[] = array($email);
            $this->db->content[] = array($hash);
            $this->db->content[] = array($type);
            $this->db->content[] = array($cpf);
            $this->db->content[] = array($slack_id);

           return  $this->db->insertId();
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    function login ($email, $password) {
        try {
            $this->db->query = "SELECT * FROM users WHERE email = ?";
            $this->db->content = array();
            $this->db->content[] = array($email);

            $user = $this->db->selectOne();

            if (!password_verify($password, $user->password)) {
                return null;
            }

           return  $user;
        } catch (\Throwable $th) {
            //throw $th;
            echo $th->getMessage();
            return null;
        }
    }

    function logout ($user_id) {
        try {
            $this->db->query = "UPDATE users SET session = '' WHERE id = ?";
            $this->db->content = array();
            $this->db->content[] = array($user_id, 'int');

           return $this->db->update();
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    function findById ($user_id) {
        try {
            $this->db->query = "SELECT * FROM users WHERE id = ?";
            $this->db->content = array();
            $this->db->content[] = array($user_id);
           return $this->db->selectOne();
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    function findAll () {
        try {
            $this->db->query = "SELECT * FROM users LIMIT ?";
            $this->db->content = array();
            $this->db->content[] = array(1000, 'int');
           return $this->db->select();
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }
}
