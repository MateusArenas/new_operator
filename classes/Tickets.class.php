<?php

class Tickets 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
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

    function status() {
        $status = array();
        $status[0] = 'AGUARDANDO';
        $status[1] = 'EM ANDAMENTO';
        $status[2] = 'CONCLUÍDO';
        $status[3] = 'FORA DO PRAZO';
        return $status;
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
           $response = $this->request(
                $action = 'update-status-ticket', 
                $body = array(
                    "ticket_id" => $ticket_id,
                    "operator_id" => $operator_id,
                    "status" => $status,
                )
            );
            return @$response->success ?: null;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }
    

    function create ($title, $reason, $description, $channel_id, $user_id) {
        try {
           $response = $this->request(
                $action = 'create-ticket', 
                $body = array(
                    "title" => $title,
                    "reason" => $reason,
                    "description" => $description,
                    "channel_id" => $channel_id,
                    "user_id" => $user_id,
                )
            );
            return @$response->ticket ?: null;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }


    function findById ($ticket_id) {
        try {
           $response = $this->request(
                $action = 'ticket', 
                $body = array(
                    "ticket_id" => $ticket_id,
                )
            );
            return @$response->ticket ?: null;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    function findAll ($offset, $limit) {
        try {
            $response = $this->request(
                $action = 'tickets', 
                $body = array(
                    "offset" => $offset,
                    "limit" => $limit,
                )
            );
            return @$response->tickets ?: null;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    function countAll () {
        try {
           $response = $this->request(
                $action = 'tickets-count', 
                $body = array()
            );
            return @$response->count ?: null;
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }
}
