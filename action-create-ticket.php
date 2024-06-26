<?php session_start();

@include_once('./config.php');
require('./classes/Database.class.php');
require('./classes/Users.class.php');
require('./classes/Tickets.class.php');
require('./classes/Functions.class.php');

$fn = new Functions();
$usrs = new Users();
$ticketsRepository = new Tickets();

$user_id = @$_SESSION["MSId"];

$user = $usrs->findById($user_id);

$description = @$_REQUEST['description'] ?: '';
$reason = @$_REQUEST['reason'];
$title = @$_REQUEST['title'];


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
        "type": "section",
        "text": {
            "type": "mrkdwn",
            "text": "*Status:*\n'."<http://www.credoperador.com.br|Verificando>".'"
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
                        "text": "Sim",
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
$response = new stdClass();

try {
    if ($ticket = $ticketsRepository->create($title, $reason, $description, "C077SAM4XTK", $user->id)) {
        $response->success = "Chamado aberto.";

        // $titulo = $title;

        // // Destinatário
        // $para = "destinatario@example.com";

        // // Assunto do e-mail
        // $assunto = "Novo Chamado: $titulo";

        // // Cabeçalhos adicionais
        // $headers = "From: operadorchamados@gmail.com\r\n";
        // $headers .= "Reply-To: operadorchamados@gmail.com\r\n";
        // $headers .= "Content-Type: text/html\r\n";

        // // Corpo do e-mail
        // $corpo_email = "<h2>$titulo</h2><p>$description</p>";

        // $this->db->query = "SELECT * FROM users WHERE status = 2 LIMIT 1000";
        // $destinatarios = $this->select();

        // foreach ($destinatarios as $para) {
        //     // Enviar e-mail
        //     if (mail($para, $assunto, $corpo_email, $headers)) {
        //     } else {
        //     }
        // }

    } else {
        throw new Exception('Não foi possivel abrir chamado.    ');
    }
} catch (\Throwable $th) {
    $response->error = $th->getMessage();
}


// if ($m = $fn->enviarMensagem($message)) {
//     echo json_encode($m);
// } else {
//     echo 'Não foi possivel enviar mensagem';
// }

?>

<?php if (@$response->success): ?>
        
    <div class="alert alert-success" role="alert">
        <?= $response->success ?>
        <div class="mt-2">
            <small class="text-muted">
                Para visualizar o chamado que foi aberto no histórico, por favor, <a href="#" onclick="location.reload()">recarregue a página</a> 
                ou clique no botão <code><i class="bi bi-arrow-clockwise"></i></code> em 
                <code><i class="bi bi-clock-history"></i> Histórico de Chamados</code>.
            </small>
        </div>
    </div>


<?php else: ?>

    <?php if (@$response->error): ?>
        <div class="alert alert-warning" role="alert">
            <?= $response->error ?>
        </div>
    <?php endif; ?>

<?php endif; ?>