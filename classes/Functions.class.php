<?php 

class Functions 
{
    public $channel;
    public $api_authorization = "Bearer xoxb-7251242262146-7264333439425-HUbQyKF6JYL8EKMkZBKPzMYo";

    public function __construct()
    {
        // $this->channel = 'C077FN00YHJ'; # wasit-ti
        $this->channel = 'C077SAM4XTK'; # sandbox
    }

    public function atualizaMensagem($mensagem, $timestamp)
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
                    'channel' => 'C077SAM4XTK',
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

    public function enviarMensagem($mensagem)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://slack.com/api/chat.postMessage?channel=C077SAM4XTK',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode( array(
                    'channel' => 'C077SAM4XTK',
                    'link_names' => '1',
                    'blocks' => $mensagem,
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

    public function reactSlack($thread, $name)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://slack.com/api/reactions.add?channel={$this->channel}&timestamp={$thread}&name={$name}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode(
                array(
                    "channel" => $this->channel, 
                    "name" => $name, 
                    "timestamp" => $thread
                )
            ),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer $this->api_authorization"
            ),
        ));
        $data = curl_exec($curl);
        curl_close($curl);
        $data = @json_decode($data);
        if (@$data->ok) return true;
        else return false;
    }

    public function removeReactSlack($thread, $name)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://slack.com/api/reactions.remove?channel={$this->channel}&timestamp={$thread}&name={$name}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode(
                array(
                    "channel" => $this->channel, 
                    "name" => $name, 
                    "timestamp" => $thread
                )
            ),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer $this->api_authorization"
            ),
        ));
        $data = curl_exec($curl);
        curl_close($curl);
        $data = @json_decode($data);
        if (@$data->ok) return true;
        else return false;
    }

    public function threadSlackText($thread, $text)
    {
        $curl = curl_init(); 
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://slack.com/api/chat.postMessage?channel={$this->channel}&thread_ts={$thread}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query(
                array(
                    "channel" => $this->channel,
                    "link_names" => "1",
                    "text" => $text
                )
            ),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/x-www-form-urlencoded",
                "Authorization: Bearer $this->api_authorization"
            ),
        ));
        $data = curl_exec($curl);
        curl_close($curl);
        $data = @json_decode($data);
        if (@$data->ok) return $data->ts;
        else return false;
    }
}
