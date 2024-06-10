<?php

class Obito 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function listarObitosRemovidos ($offset = 0, $limit = 100) {
        try {
            $this->db->query = "SELECT f.id, f.consulta as cod_consulta, f.date_remocao, f.date_remocao AS data, c.ValorItem AS parametro, t.tipoconsulta AS tipo, t.id AS tipo_consulta, c.ItemConsultado as tipo_parametro , f.justificar, a.NomeAtendente AS nome, a.slack_id
                FROM operador.tbl_remove_obito AS f, credauto.consultas AS c, credauto.atendentes AS a, credauto.ttipoconsulta AS t 
                WHERE a.CodAtendente = f.usuario AND c.Codigo = f.consulta AND t.codigo = c.TipoConsulta 
                ORDER BY id DESC LIMIT ?, ?
            ";

            $this->db->content = [];
            $this->db->content[] = [$offset, 'int'];
            // aqui está fazendo com que não passe de 1000.
            $this->db->content[] = [min(1000, $limit), 'int'];

            return $this->db->select();
        } catch (\Throwable $th) {
            //var_dump($th);
            throw new Exception("Não foi obter a lista de óbitos removidos.");
        }
    }

    function totalObitosRemovidos () {
        try {
            $this->db->query = "SELECT COUNT(*) as total 
                FROM operador.tbl_remove_obito as f, credauto.consultas AS c
                WHERE c.Codigo = f.consulta
            ";
        
            return @$this->db->selectOne()->total ?: 0;
        } catch (\Throwable $th) {
            //var_dump($th);
            throw new Exception("Não foi obter total da lista de óbitos removidos.");
        }
    }

    function motivos () {
        $motivos = array();
        $motivos[1] = 'Data de óbito incorreta';
        $motivos[2] = 'Informação de óbito não procede';
        return $motivos;
    }
    
}
