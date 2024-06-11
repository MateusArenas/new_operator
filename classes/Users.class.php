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

    function request ($action, $body) {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "http://new-operator.vercel.app?action={$action}",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($body),
                CURLOPT_HTTPHEADER => array(
                    'token: 26d7c43e-504f-4bab-6777-8392fd4839ee',
                    'Content-Type: application/json'
                ),
            ));
    
            $response = curl_exec($curl);

            // Verifica se ocorreu algum erro
            if(curl_errno($curl)){
                // Se ocorreu um erro, exibe a mensagem de erro
                $message = curl_error($curl);
                throw new Exception("Erro ao fazer a requisição: $message");
            }
    
            curl_close($curl);

            return json_decode($response, false);
        } catch (\Throwable $th) {
            // var_dump($th);
            throw $th;
        }
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

           return  $this->db->insert();
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

        //    $response = $this->request(
        //         $action = 'users', 
        //         $body = array( "user_id" => $user_id )
        //     );
        //    return @$response->user ?: null;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    function findAll () {
        try {
            $response = $this->request($action = 'users', $body = array());
            return @$response->users ?: null;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }
}
