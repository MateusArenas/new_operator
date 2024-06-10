<?php session_start();

@include_once('./config.php');
require('./classes/Database.class.php');
require('./classes/Users.class.php');
require('./classes/Functions.class.php');

$fn = new Functions();
$usrs = new Users();

$user_id = @$_SESSION["MSId"];

$user = $usrs->findById($user_id);

$description = @$_REQUEST['description'] ?: '';
$reason = @$_REQUEST['reason'];


$data = date('d/m/Y à\s H:i');
$num_chamado = '#operador-'.time();

$message = '
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
                "text": "*Aberto por:*\n<@'.$user->slack_id.'>"
            },
            {
                "type": "mrkdwn",
                "text": "*Data Consulta:*\n'.$data.'"
            }
        ]
    },
    {
        "type": "section",
        "text": {
            "type": "mrkdwn",
            "text": "*Descrição:*\n'.$description.'"
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

if ($m = $fn->enviarMensagem($message)) {
    echo json_encode($m);
} else {
    echo 'Não foi possivel enviar mensagem';
}

?>