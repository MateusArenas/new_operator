<?php 
    function atualizaMensagem($channel_id, $mensagem, $timestamp)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://slack.com/api/chat.update',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode( array(
                    'channel' => $timestam,
                    'ts' => $timestamp,
                    'text' => $mensagem
                )
            ),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json; charset=utf-8',
                'Authorization: Bearer xoxb-7251242262146-7264333439425-HUbQyKF6JYL8EKMkZBKPzMYo'
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        $response = json_decode($response);
        return $response;
    }


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

                atualizaMensagem($payload->message, $message_ts);

            } elseif ($action->action_id === 'chamado-generico-resolvido') {
                // Ação de resolução clicada, faça algo aqui
                // Altere o ícone para "checked" quando "Resolvido" for clicado
                $payload->message[1]->fields[2]->text = "*Status:*\n:white_check_mark: Resolvido";

                atualizaMensagem($payload->message, $message_ts);
            }
        }
    }
  }

?>