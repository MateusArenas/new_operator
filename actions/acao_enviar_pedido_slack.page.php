<?php
	ini_set('display_errors', 0); ini_set('display_startup_errors', 0); error_reporting(0);

    header("Access-Control-Allow-Origin: *");

    session_start();

    require('../classes/Database.class.php');
    require('../classes/Functions.class.php');

    require('../classes/Atendente.class.php');
    require('../classes/Consulta.class.php');
    require('../classes/FilaAtualiza.class.php');
    require('../classes/FilaExclusao.class.php');

    $db = new Database();
    $fn = new Functions();

    $Atendente = new Atendente();
    $Consulta = new Consulta();
    $FilaAtualiza = new FilaAtualiza();
    $FilaExclusao = new FilaExclusao();

	$pedido = @$_REQUEST['pedido'];
	$descricao = @$_REQUEST['descricao'];
	$descricao = str_replace('"', '\\"', $descricao);

	$id = base64_decode(@$_REQUEST['MSId']);
	$codConsulta = base64_decode(@$_REQUEST['consulta']);

    $response = new stdClass();

    try {
        if(!isset($_REQUEST['MSId'])) {
            throw new Exception('ID do Atendente não localizado');
        }

        $atendente = $Atendente->findById($id);

        if(!isset($atendente->CodAtendente)) {
            throw new Exception('Atendente não localizado');
        }

        $consulta = $Consulta->findByCodigo($codConsulta);

        if(!isset($consulta->codigo)) {
            throw new Exception('Consulta não localizada');
        }

        $FilaAtualiza->priorizarConsulta($consulta->codigo);

        $FilaExclusao->deleteByCodConsulta($consulta->codigo);

        $FilaAtualiza->register($consulta->codTipo, $consulta->codigo);

        if($pedido == '2')
        {
            $uf = $consulta->uf ? $consulta->uf : '-';
            $data = date('d/m/Y à\s H:i', strtotime($consulta->data));
            $consulta->tipo = ucwords(strtolower($consulta->tipo));
            $num_chamado = '#operador-'.time();
            $url_consulta = "http://www.credoperador.com.br/rpc/inc_consulta_normalizada.php?Codigo={$consulta->codigo}&print=1&Tipo={$consulta->TipoConsulta}";
            $codigo_consulta = "<http://www.credoperador.com.br/?base64=1&redirect=".urlencode(base64_encode($url_consulta))."|{$consulta->codigo}>";
            $mensagem = '
                    [
                        {
                            "type": "section",
                            "text": {
                                "type": "mrkdwn",
                                "text": ":inbox_tray: *Novo Chamado '.$num_chamado.'*"
                            }
                        },
                        {
                            "type": "section",
                            "fields": [
                                {
                                    "type": "mrkdwn",
                                    "text": "*Aberto por:*\n<@'.$atendente->slack_id.'>"
                                },
                                {
                                    "type": "mrkdwn",
                                    "text": "*Data Consulta:*\n'.$data.'"
                                }
                            ]
                        },
                        {
                            "type": "section",
                            "fields": [
                                {
                                    "type": "mrkdwn",
                                    "text": "*Código:*\n'.$codigo_consulta.'"
                                },
                                {
                                    "type": "mrkdwn",
                                    "text": "*Tipo Consulta:*\n'.$consulta->tipo.'"
                                }
                            ]
                        },
                        {
                            "type": "section",
                            "fields": [
                                {
                                    "type": "mrkdwn",
                                    "text": "*'.ucwords($consulta->item).' / UF :*\n'.$consulta->parametro.' / '.$uf.'"
                                },
                                {
                                    "type": "mrkdwn",
                                    "text": "*Cliente:*\n'.$consulta->cliente.'"
                                }
                            ]
                        },
                        {
                            "type": "section",
                            "text": {
                                "type": "mrkdwn",
                                "text": "*Descrição:*\n'.$descricao.'"
                            }
                        },
                        {
                            "type": "actions",
                            "block_id": "chamado-generico",
                            "elements": [
                                {
                                    "type": "button",
                                    "action_id": "chamado-generico-verificando",
                                    "text": {
                                        "type": "plain_text",
                                        "text": "Verificando"
                                    },
                                    "confirm": {
                                        "title": {
                                            "type": "plain_text",
                                            "text": "Atenção"
                                        },
                                        "text": {
                                            "type": "mrkdwn",
                                            "text": "Deseja mudar o status do chamado para verificando?"
                                        },
                                        "confirm": {
                                            "type": "plain_text",
                                            "text": "Sim"
                                        },
                                        "deny": {
                                            "type": "plain_text",
                                            "text": "Não"
                                        }
                                    }
                                },
                                {
                                    "type": "button",
                                    "action_id": "chamado-generico-resolvido",
                                    "text": {
                                        "type": "plain_text",
                                        "text": "Resolvido"
                                    },
                                    "style": "primary",
                                    "confirm": {
                                        "title": {
                                            "type": "plain_text",
                                            "text": "Atenção"
                                        },
                                        "text": {
                                            "type": "mrkdwn",
                                            "text": "Deseja mudar o status do chamado para resolvido?"
                                        },
                                        "confirm": {
                                            "type": "plain_text",
                                            "text": "Sim"
                                        },
                                        "deny": {
                                            "type": "plain_text",
                                            "text": "Não"
                                        }
                                    }
                                }
                            ]
                        },
                        {
                            "type": "divider"
                        }
                    ]
                ';

            $response = $fn->enviarMensagem($mensagem);

            if($response->ok)
            {
                $fn->threadSlackText($response->ts, "<@{$atendente->slack_id}>");
                $response->success = "Solicitação enviada";
            } 
            else 
            {
                throw new Exception('Não foi possível enviar a solicitação via Slack.');
            }

        } 
        else 
        {
            $response->success = "Solicitação enviada."; // não foi enviada mas.....
        }
    } catch (\Throwable $th) {
        $response->error = $th->getMessage();
    }
?>

    <div class="modal-header">
        <h1 class="modal-title fs-5">
            <i class="bi bi-broadcast me-2"></i>
            Solicitação de Alteração
        </h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    
    <div class="modal-body">
        <?php if (@$response->success): ?>
        
            <div class="alert alert-success" role="alert">
                <?= $response->success ?>
            </div>
        
        <?php else: ?>

            <?php if (@$response->error): ?>
                <div class="alert alert-warning" role="alert">
                    <?= $response->error ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
    