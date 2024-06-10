<?php

class Api 
{
    private $baseURL = "";

    function __construct()
    {
        if(strpos("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", 'localhost'))
        {
            $this->baseURL = 'http://localhost/new_operator/api';
        }
        else
        {
            $this->baseURL = 'http://painel.credoperador.com.br/api';
        }
    }

    function post ($action, $body) {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->baseURL . $action,
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
    
}
