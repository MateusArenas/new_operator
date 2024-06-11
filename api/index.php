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
    $Users = new Users();
    $Tickets = new Tickets();

    $response = new stdClass();

    $get = (object)$_GET;
    $post = (object)$_POST;
    $request = (object)$_REQUEST;
    $server = (object)$_SERVER;

    $input = file_get_contents('php://input');
    $body = json_decode($input);

    $http_token = @$server->HTTP_TOKEN ?: @$request->token;
    $access_token = @$server->HTTP_AUTHORIZATION;

    $action = @$request->action;

    try {
        if ($http_token == '26d7c43e-504f-4bab-6777-8392fd4839ee') {

            // midleware de auth
            if (in_array($action, ['remover_consulta'])) {
                $response->auth = true;
            }


            switch ($action) {
                case 'users':
                    if ($users = $Users->findAll()) {
                        $response->users = $users;
                    } else {
                        throw new Exception("Não foi possível listar usuários.");
                    }

                    break;
                case 'user':
                    if ($user = $Users->findById($request->user_id)) {
                        $response->user = $user;
                    } else {
                        throw new Exception("Não foi possível obter usuário.");
                    }

                    break;
                case 'sign-up':
                    if ($user_id = $Users->register($post->name, $post->email, $post->password, $post->type, $post->cpf)) {
                        $user = $Users->findById($user_id);

                        $response->user = $user;
                    } else {
                        throw new Exception("Não foi criar usuário.");
                    }

                    break;
                case 'sign-in':
                    if ($user = $Users->login($post->email, $post->password)) {

                        $response->user = $user;
                    } else {
                        throw new Exception("Não foi possível logar.");
                    }

                    break;
                case 'logout':
                    if ($Users->logout($request->user_id)) {
                        $response->success = true;
                    } else {
                        throw new Exception("Não foi possível logar.");
                    }

                    break;
                case 'listar-operadores':
                    if ($operadores = $Users->findAll()) {
                        $response->operadores = $operadores;
                    } else {
                        throw new Exception("Não foi possível listar operadores.");
                    }

                    break;
                case 'create-ticket':
                    if ($ticket_id = $Tickets->create($post->title, $post->reason, $post->description, $post->channel_id, $post->user_id)) {
                        $ticket = $Tickets->findById($ticket_id);

                        $response->ticket = $ticket;
                    } else {
                        throw new Exception("Não foi criar ticket.");
                    }

                    break;
                case 'update-status-ticket':
                    if ($ticket_id = $Tickets->updateStatus($post->ticket_id, $post->operator_id, $post->status)) {
                        $response->success = true;
                    } else {
                        throw new Exception("Não foi possivel mudar o status do ticket.");
                    }

                    break;
                case 'tickets-count':
                    if (($count = $Tickets->countAll() !== null)) {
                        $response->count = $count;
                    } else {
                        throw new Exception("Não foi possivel obter tickets.");
                    }

                    break;
                case 'tickets':
                    if ($tickets = $Tickets->findAll($post->offset, $post->limit)) {
                        $response->tickets = $tickets;
                    } else {
                        throw new Exception("Não foi possivel obter tickets.");
                    }

                    break;
                case 'ticket':
                    if ($ticket = $Tickets->findById($post->ticket_id)) {
                        $response->ticket = $ticket;
                    } else {
                        throw new Exception("Não foi possivel obter ticket.");
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