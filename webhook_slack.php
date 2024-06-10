<?php 
  @include_once('./config.php');
  require('./classes/Functions.class.php');

  $fn = new Functions();

  // Verifique se há uma solicitação POST
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decodifique o corpo da solicitação JSON
    $payload = json_decode($_POST['payload']);

    // Verifique se a ação é do tipo button e corresponde ao seu action_id
    if ($payload->type === 'block_actions') {
        foreach ($payload->actions as $action) {
            if ($action->action_id === 'chamado-generico-verificando') {
                // Ação de verificação clicada, faça algo aqui
                // Por exemplo, atualize o status do chamado no seu sistema
                $channel_id = $payload->channel->id; // ID do canal onde a mensagem foi postada
                $user_id = $payload->user->id; // ID do usuário que clicou no botão
                $message_ts = $payload->message->ts; // Timestamp da mensagem
                // Faça algo com essas informações, como atualizar o status do chamado no seu sistema

                // Altere o ícone para "olhinho" quando "Verificando" for clicado
                $payload->message[1]->fields[2]->text = "*Status:*\n:eyes: Verificando";

                $fn->atualizaMensagem($payload->message, $message_ts);

            } elseif ($action->action_id === 'chamado-generico-resolvido') {
                // Ação de resolução clicada, faça algo aqui
                // Altere o ícone para "checked" quando "Resolvido" for clicado
                $payload->message[1]->fields[2]->text = "*Status:*\n:white_check_mark: Resolvido";

                $fn->atualizaMensagem($payload->message, $message_ts);
            }
        }
    }
  }

?>