<?php 
    date_default_timezone_set('America/Sao_Paulo');

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: *');
    header('Content-Type: application/json; charset=utf-8');

    // ini_set('display_errors', 1);
    // ini_set('display_startup_erros', 1);
    // error_reporting(1);

    // require_once('classes/JsonWebToken.class.php');
    require_once('classes/Database.class.php');
    require_once('classes/Users.class.php');
    require_once('classes/Tickets.class.php');

    $db = new Database();

    $db->endpoint = '';

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
                case 'teste':
                        $response->post = $post;
                        $response->get = $get;
                        $response->request = $request;
                        $response->body = $body;
                        
                        $response->estados = [
                            [ "label" => "Acre", "value" => "AC" ],
                            [ "label" => "Alagoas", "value" => "AL" ],
                            [ "label" => "Amapá", "value" => "AP" ],
                            [ "label" => "Amazonas", "value" => "AM" ],
                            [ "label" => "Bahia", "value" => "BA" ],
                            [ "label" => "Ceará", "value" => "CE" ],
                            [ "label" => "Distrito Federal", "value" => "DF" ],
                            [ "label" => "Espírito Santo", "value" => "ES" ],
                            [ "label" => "Goiás", "value" => "GO" ],
                            [ "label" => "Maranhão", "value" => "MA" ],
                            [ "label" => "Mato Grosso", "value" => "MT" ],
                            [ "label" => "Mato Grosso do Sul", "value" => "MS" ],
                            [ "label" => "Minas Gerais", "value" => "MG" ],
                            [ "label" => "Pará", "value" => "PA" ],
                            [ "label" => "Paraíba", "value" => "PB" ],
                            [ "label" => "Paraná", "value" => "PR" ],
                            [ "label" => "Pernambuco", "value" => "PE" ],
                            [ "label" => "Piauí", "value" => "PI" ],
                            [ "label" => "Rio de Janeiro", "value" => "RJ" ],
                            [ "label" => "Rio Grande do Norte", "value" => "RN" ],
                            [ "label" => "Rio Grande do Sul", "value" => "RS" ],
                            [ "label" => "Rondônia", "value" => "RO" ],
                            [ "label" => "Roraima", "value" => "RR" ],
                            [ "label" => "Santa Catarina", "value" => "SC" ],
                            [ "label" => "São Paulo", "value" => "SP" ],
                            [ "label" => "Sergipe", "value" => "SE" ],
                            [ "label" => "Tocantins", "value" => "TO" ],
                        ];       

                        $itemsPerPage = 5;
                        $totalItems = count($response->estados);
                        $currentPage = @$body->page ?: 1;

                        // Calcula o offset e não deixa passar de menos zero
                        $offset = max(0, ($currentPage - 1) * $itemsPerPage);

                        $response->estados = array_slice($response->estados, $offset, $itemsPerPage);

                        // Calcula o total de páginas
                        $totalPages = ceil($totalItems / $itemsPerPage);

                        $response->totalPages = $totalPages;
                        $response->totalItems = $totalItems;

                        if (@$body->email) {
                            $response->message = "Olá {$body->email}, tudo bem? Ótima escolha de senha.";
                        } else {
                            $response->message = "Olá mundo!";
                        }
                    break;
                case 'users':
                    if ($users = $Users->findAll()) {
                        $response->users = $users;
                    } else {
                        throw new Exception("Não foi possível listar usuários.");
                    }

                    break;
                case 'user':
                    if ($user = $Users->findById($body->user_id)) {
                        $response->user = $user;
                    } else {
                        throw new Exception("Não foi possível obter usuário.");
                    }

                    break;
                case 'sign-up':
                    if ($user_id = $Users->register($body->name, $body->email, $body->password, $body->type, $body->cpf)) {
                        $user = $Users->findById($user_id);

                        $response->user = $user;
                    } else {
                        throw new Exception("Não foi criar usuário.");
                    }

                    break;
                case 'sign-in':
                    if ($user = $Users->login($body->email, $body->password)) {

                        $response->user = $user;
                    } else {
                        throw new Exception("Não foi possível logar.");
                    }

                    break;
                case 'logout':
                    if ($Users->logout($body->user_id)) {
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
                    if ($ticket_id = $Tickets->create($body->title, $body->reason, $body->description, $body->channel_id, $body->user_id)) {
                        $ticket = $Tickets->findById($ticket_id);

                        $response->ticket = $ticket;
                    } else {
                        throw new Exception("Não foi criar ticket.");
                    }

                    break;
                case 'update-status-ticket':
                    if ($ticket_id = $Tickets->updateStatus($body->ticket_id, $body->operator_id, $body->status)) {
                        $response->success = true;
                    } else {
                        throw new Exception("Não foi possivel mudar o status do ticket.");
                    }

                    break;
                case 'tickets-count':
                    if (($count = $Tickets->countAll()) !== null) {
                        $response->count = $count;
                    } else {
                        throw new Exception("Não foi possivel obter tickets.");
                    }

                    break;
                case 'tickets':
                    if ($tickets = $Tickets->findAll($body->offset, $body->limit)) {
                        $response->tickets = $tickets;
                    } else {
                        throw new Exception("Não foi possivel obter tickets.");
                    }

                    break;
                case 'ticket':
                    if ($ticket = $Tickets->findById($body->ticket_id)) {
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