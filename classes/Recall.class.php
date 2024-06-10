<?php

class Recall 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function motivos () {
        $motivos = array();
        $motivos[1] = 'Recall em Branco';
        $motivos[2] = 'Recall Divergente';
        return $motivos;
    }
    
}
