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
           $response = $this->request(
                $action = 'sign-up', 
                $body = array( 
                    "nome" => $nome, 
                    "email" => $email, 
                    "password" => $password,
                    "type" => $type,
                    "cpf" => $cpf,
                    "slack_id" => $slack_id,
                )
            );
            return $response->user ?: null;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    function login ($email, $password) {
        try {
           $response = $this->request(
                $action = 'sign-in', 
                $body = array( "email" => $email, "password" => $password )
            );
            return $response->user ?: null;
        } catch (\Throwable $th) {
            //throw $th;
            // echo $th->getMessage();
            return null;
        }
    }

    function logout ($user_id) {
        try {
           $response = $this->request(
                $action = 'logout', 
                $body = array( "user_id" => $user_id )
            );
            return @$response->success ? $user_id : null;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    function findById ($user_id) {
        try {
           $response = $this->request(
                $action = 'user', 
                $body = array( "user_id" => $user_id )
            );
           return @$response->user ?: null;
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
