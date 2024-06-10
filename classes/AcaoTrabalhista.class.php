<?php

class AcaoTrabalhista 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function motivos () {
        $motivos = array();
        $motivos[1] = 'Trabalhista em branco';
        $motivos[2] = 'Ação trabalhista divergente';
        return $motivos;
    }

}
