<?php 

class Consulta 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function findByCodigo ($codigo) {
        try {
            $this->db->query = "SELECT 
                    c.*,
                    c.UF as uf, 
                    c.Codigo as codigo, 
                    c.TipoConsulta as codTipo, 
                    t.tipoconsulta as tipo, 
                    c.ItemConsultado as item, 
                    c.ValorItem as parametro, 
                    CONCAT(c.Data, ' ', c.Hora) as data, 
                    c.CodCliente as cliente 
                FROM 
                    consultas as c, 
                    ttipoconsulta as t 
                WHERE 
                    c.Codigo = ? 
                    AND t.id = c.TipoConsulta
            ";

            $this->db->content = array($codigo, 'int');
            
            return $this->db->selectOne();
        } catch (\Throwable $th) {
            //var_dump($th);
            throw new Exception("Não foi possivel obter consulta[Codigo:$codigo]");
        }
    }

    function motivos () {
        $motivos = array();
        $motivos[1] = 'Consultas em Branco';
        $motivos[2] = 'Retorno com Erro';
        $motivos[3] = 'Consulta incompleta';
        return $motivos;
    }
}
