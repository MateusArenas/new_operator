<?php

class JsonWebToken
{
    private static $salt = "operador";

    /**
     * @param string $string
     * @return string|false 
     */
    private static function base64url_encode($data)
    {
        return str_replace(['+','/','='], ['-','_',''], base64_encode($data));
    }
 
    /**
     * @param string $string
     * @return string|false 
     */
    private static function base64_decode_url($string) 
    {
        return base64_decode(str_replace(['-','_'], ['+','/'], $string));
    }
 
        /**
     * @param array $payload
     * @param string|null $secret
     * @return string|false 
     */
    public static function encode(array $payload, string $exptime="+60 seconds")
    {
        try {
            $key = static::$salt;
            
            $header = json_encode(array(
                "alg" => "HS256",
                "typ" => "JWT"
            ));
            
            $now = time(); // Obter a data/hora atual como um valor Unix timestamp

            $extra = array(
                // "iss" => "https://seusite.com", // Emissor do JWT
                // "aud" => "https://seusite.com", // Destinatário do JWT
                "iat" => $now, // Data/hora de emissão
                "exp" => strtotime($exptime) // Data/hora de expiração
            );

            $payload = json_encode(array_merge($payload, $extra), JSON_UNESCAPED_SLASHES);
        
            $header_payload = static::base64url_encode($header) . '.'. 
                                static::base64url_encode($payload);
    
            $signature = hash_hmac('sha256', $header_payload, $key, true);
            
            return 
                static::base64url_encode($header) . '.' .
                static::base64url_encode($payload) . '.' .
                static::base64url_encode($signature);
        } 
        catch(Error $e)
        {
            $message = $e->getMessage() ?? "Erro ao gerar Token";
            throw new \Error($message, $e->getCode());
        }
    }
 
    /**
     * @param string|null $token
     * @param string|null $secret
     * @return object|false 
     */
    public static function decode(string $token = NULL)
    {
        try {
            if ($token == false) throw new \Error('Empty Token', 401);

            $key = static::$salt;
    
            $token = explode('.', $token);

            if (count($token) < 3) return false;

            $header = static::base64_decode_url($token[0]);
            $payload = static::base64_decode_url($token[1]);
            $signature = static::base64_decode_url($token[2]);
     
            $header_payload = $token[0] . '.' . $token[1];

            if (hash_hmac('sha256', $header_payload, $key, true) !== $signature) {
                throw new \Error('Invalid signature', 401);
            }
            $payload = (object)json_decode($payload, true);

            // Verificar se o token expirou
            if (isset($payload->exp) && $payload->exp < time()) {
                throw new \Error('Token expired', 401);
            }

            return $payload;
        } 
        catch(Error $e)
        {
            $message = $e->getMessage() ?? "Erro ao decodificar Token";
            throw new \Error($message, $e->getCode());
        }
    }
 
}

?>