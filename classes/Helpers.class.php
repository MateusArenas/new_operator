<?php 

class Helpers
{
    static $estados = array(
        'AC' => 'Acre',
        'AL' => 'Alagoas',
        'AP' => 'Amapá',
        'AM' => 'Amazonas',
        'BA' => 'Bahia',
        'CE' => 'Ceará',
        'DF' => 'Distrito Federal',
        'ES' => 'Espirito Santo',
        'GO' => 'Goiás',
        'MA' => 'Maranhão',
        'MS' => 'Mato Grosso do Sul',
        'MT' => 'Mato Grosso',
        'MG' => 'Minas Gerais',
        'PA' => 'Pará',
        'PB' => 'Paraíba',
        'PR' => 'Paraná',
        'PE' => 'Pernambuco',
        'PI' => 'Piauí',
        'RJ' => 'Rio de Janeiro',
        'RN' => 'Rio Grande do Norte',
        'RS' => 'Rio Grande do Sul',
        'RO' => 'Rondônia',
        'RR' => 'Roraima',
        'SC' => 'Santa Catarina',
        'SP' => 'São Paulo',
        'SE' => 'Sergipe',
        'TO' => 'Tocantins',
    );

    public static function validateStatus ($status) {
        try {

            switch ((int)$status) {
                case 0: // aguardando
                    return 0;
                case 1: // conclcuido
                    return 1;
                case 2: // rejeitado
                    return 2;
                default: // status invalido
                    throw new Exception();
                    break;
            }

            return $status;

        } catch (\Throwable $th) {
            throw new Exception("Status inválido");
        }
    }

    public static function validateName ($name) { // João Silva Nome a ser validado
        try {
            // Padrão regex para permitir apenas letras (maiúsculas e minúsculas) e espaços
            $pattern = '/^[a-zA-Z\s]+$/'; 

            if (preg_match($pattern, $name)) {
                return $name;
            } else {
                throw new Exception();
            }

        } catch (\Throwable $th) {
            throw new Exception("O nome é inválido.");
        }
    }

    public static function validateDescription ($description) { // "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam condimentum."
        try {
            if (strlen($description) <= 120) {
                return $description;
            } else {
                throw new Exception();
            }

        } catch (\Throwable $th) {
            throw new Exception("A descrição excede o tamanho máximo permitido de 120 caracteres.");
        }
    }

    public static function validatePlaca ($placa) { 
        try {
            $placa = strtoupper($placa);
            $placa = preg_replace("/[^a-zA-Z0-9]/", "", $placa);
    
            if (strlen($placa) !== 7) 
                throw new Error('Placa inválida.');
    
            if (!preg_match("/^[A-Z]{3}\d{1}[A-Z0-9]\d{2}$/", $placa)) 
                throw new Error('Placa com o fomrato inválido.');

            return $placa;
        } catch (\Throwable $th) {
            // var_dump($th);
            throw $th;
        }
    }

    public static function placaMercosul($placa) {
        $placa = Helpers::validatePlaca($placa);

        if (is_numeric($placa[4])) 
        {   // tranforma em mercosul. // A letra S tem um valor ASCII de 83, enquanto C tem um valor ASCII de 67.
            $val = chr(ord($placa[4]) + 17);
            $placa[4] = $val;
        } 
        else 
        {   // essa placa já é normal.
            return $placa;
        }

        return strtoupper($placa);
    }

    public static function placaNormal($placa) {
        $placa = Helpers::validatePlaca($placa);
        
        if (!is_numeric($placa[4])) 
        {   // tranforma em mercosul.
            $val = chr(ord($placa[4]) - 17);
            $placa[4] = $val;
        } 
        else 
        {   // essa placa já é normal.
            return $placa;
        }
    
        return strtoupper($placa);
    }

    public static function formatarPlaca ($placa) {
        $placa = Helpers::validatePlaca($placa);
        return substr($placa, 0, 3) . '-' . substr($placa, 3);
    }

    public static function formatarDataEscrita ($data) { // "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam condimentum."
        try {
            $formatada = date('d/m à\s H:i', strtotime($data));
            $formatada = str_replace('/01',' de Janeiro', $formatada);
            $formatada = str_replace('/02',' de Fevereiro', $formatada);
            $formatada = str_replace('/03',' de Março', $formatada);
            $formatada = str_replace('/04',' de Abril', $formatada);
            $formatada = str_replace('/05',' de Maio', $formatada);
            $formatada = str_replace('/06',' de Junho', $formatada);
            $formatada = str_replace('/07',' de Julho', $formatada);
            $formatada = str_replace('/08',' de Agosto', $formatada);
            $formatada = str_replace('/09',' de Setembro', $formatada);
            $formatada = str_replace('/10',' de Outubro', $formatada);
            $formatada = str_replace('/11',' de Novembro', $formatada);
            $formatada = str_replace('/12',' de Dezembro', $formatada);
            if(date('Y-m-d', strtotime($data)) == date('Y-m-d'))
                $formatada = date('à\s H:i', strtotime($data));
            if(date('Y-m-d', strtotime($data)) == date('Y-m-d', strtotime('-1 day')))
                $formatada = date('\O\n\t\e\m à\s H:i', strtotime($data));

            return $formatada;
        } catch (\Throwable $th) {
            throw new Exception("Não foi possível formatar a data mencionada.");
        }
    }

    public static function existeImagem ($imageUrl) {
        if (!$imageUrl) return false;

        // Verifica se a URL da imagem é válida
        if(filter_var($imageUrl, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        // Obtém as informações da imagem
        $imageInfo = @getimagesize($imageUrl);

        // Verifica se as informações da imagem foram obtidas com sucesso e se o tipo de mídia é uma imagem
        if($imageInfo !== false && strpos($imageInfo['mime'], 'image') === 0) {
            // A imagem existe.
            return true;
        } else {
            // A imagem não existe ou não é acessível.
            return false;
        }
    }

}


?>