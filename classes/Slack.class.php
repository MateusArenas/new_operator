<?php

// $slack_user->profile->image_24;
// $slack_user->profile->image_32;
// $slack_user->profile->image_48;
// $slack_user->profile->image_72;
// $slack_user->profile->image_192;

class Slack 
{
    public $api_url = "https://slack.com/api";
    public $api_authorization = "Bearer xoxb-7251242262146-7264333439425-HUbQyKF6JYL8EKMkZBKPzMYo";

    function __construct()
    {
    }

    function findUser ($user_id) {
        try {
            $curl = curl_init();
    
            curl_setopt_array($curl, array(
              CURLOPT_URL => "$this->api_url/users.profile.get?user={$user_id}",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                "Authorization: $this->api_authorization"
              ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            
            return json_decode($response, false);
        } catch (\Throwable $th) {
            //throw $th;
            throw new Exception("Erro ao obter informações do usuário no slack.");
        }
    }
    
}
