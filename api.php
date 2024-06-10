<?php 
    date_default_timezone_set('America/Sao_Paulo');

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=utf-8');

    ini_set('display_errors', 1);
    ini_set('display_startup_erros', 1);
    error_reporting(1);

    require_once('./classes/JsonWebToken.class.php');
    require_once('./classes/Database.class.php');

    $db = new Database();

    $response = new stdClass();

    $get = (object)$_GET;
    $post = (object)$_POST;
    $request = (object)$_REQUEST;
    $server = (object)$_SERVER;

    $input = file_get_contents('php://input');
    $body = json_decode($input);

    $http_token = @$server->HTTP_TOKEN;
    $access_token = @$server->HTTP_AUTHORIZATION;

    $action = @$request->action;

    try {
        if ($http_token == '26d7c43e-504f-4bab-6777-8392fd4839ee') {

            // midleware de auth
            if (in_array($action, ['remover_consulta'])) {
                $response->auth = true;
            }


            switch ($action) {
                case 'remover_consulta':

                    // $payload = JsonWebToken::decode($access_token);

                    $consulta = @$body->consulta;
                    $operador = @$body->operador;
                    $justificativa = @$body->justificativa;
                    $descricao = @$body->descricao;

                    if (!$consulta) throw new Exception('Consulta não localizada.');
                    if (!$operador) throw new Exception('ID do Atendente não localizado.');
                    if (!$justificativa) throw new Exception('Justificativa não localizada.');
                    if (!$descricao) throw new Exception('Descrição não localizada.');

                    if (strlen($descricao) > 120) {
                        throw new Exception("A descrição excede o tamanho máximo permitido de 120 caracteres.");
                    }

                    $response->session_id = session_id();
    
                    $response->success = true;
                    break;
                case 'listar-operadores':
                    $db->query = "SELECT * FROM credauto.atendentes WHERE CodAtendente != 0 AND Situacao != 2 ORDER BY LoginAtendente";
                    $db->content = array();

                    if ($operadores = $db->select()) {
                        $response->operadores = $operadores;
                    } else {
                        throw new Exception("Não foi possível listar operadores.");
                    }

                    break;
                default:
                    $response->warning = "Ação não configurada [{$action}]";
                    break;
            }
        } else {
            $response->warning = "Autorização de transação negada.";
        }
    } catch (\Exception $e) {
        $response->error = $e->getMessage();
    }

    echo json_encode($response, JSON_PRETTY_PRINT);
?>