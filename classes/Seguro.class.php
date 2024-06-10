<?php

class Seguro 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function motivos () {
        $motivos = array();
        $motivos[1] = 'Veículo é caminhonete';
        $motivos[2] = 'Cotação indisponível';
        return $motivos;
    }
    
}
