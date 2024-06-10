<?php

class Farol 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function motivos () {
        $motivos = array();
        $motivos[1] = 'Sistema fez a filtragem de forma incorreta.';
        $motivos[2] = 'Farol Vermelho por informações erradas.';
        $motivos[3] = 'Alterada informação de Óbito/Renajud.';
        return $motivos;
    }

    function farois () {
        $farois = array();
        $farois[1] = '🔴 Vermelho';
        $farois[2] = '🟡 Amarelo';
        $farois[3] = '🟢 Verde';
        return $farois;
    }
    
}
