<?php

class HistoricoRF 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function motivos () {
        $motivos = array();
        $motivos[1] = 'Chamado de Cliente';
        $motivos[2] = 'Histórico Indisponível';
        return $motivos;
    }
    
}
