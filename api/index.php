<?php 
    date_default_timezone_set('America/Sao_Paulo');

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json; charset=utf-8');

    ini_set('display_errors', 1);
    ini_set('display_startup_erros', 1);
    error_reporting(1);

    // require_once('classes/JsonWebToken.class.php');
    require_once('classes/Database.class.php');
    require_once('classes/Users.class.php');
    require_once('classes/Tickets.class.php');

    $db = new Database();
    $usersRepository = new Users();
    $ticketsRepository = new Tickets();

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
                case 'listar-operadores':
                    if ($operadores = $usersRepository->findAll()) {
                        $response->operadores = $operadores;
                    } else {
                        throw new Exception("Não foi possível listar operadores.");
                    }

                    break;
                case 'users':
                    if ($users = $User->findAll()) {
                        $response->users = $users;
                    } else {
                        throw new Exception("Não foi possível listar operadores.");
                    }

                    break;
                case 'sign-up':
                    if ($user_id = $User->register($post->name, $post->email, $post->password, $post->type, $post->cpf)) {
                        $user = $User->findById($user_id);

                        $response->user = $user;
                    } else {
                        throw new Exception("Não foi possível listar operadores.");
                    }

                    break;
                case 'sign-in':
                    if ($user = $User->login($post->email, $post->password)) {

                        $response->user = $user;
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