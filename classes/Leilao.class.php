<?php

class Leilao 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    // function deleteByCodConsulta ($codConsulta) {
    //     try {
    //         $this->db->query = "DELETE FROM robo.tfila_exclusao WHERE consulta = ?";
    //         $this->db->content = array($codConsulta, 'int');
    //         return $this->db->delete();
    //     } catch (\Throwable $th) {
    //         //var_dump($th);
    //         throw new Exception("Não foi possivel deletar consulta em fila_exclusao[codConsulta:$codConsulta]");
    //     }
    // }

    function listarLeiloesRemovidos ($offset = 0, $limit = 100) {
        try {
            $this->db->query = "SELECT r.*, a.NomeAtendente as nome , a.ImagemAtendente as ImagemAtendente
                FROM operador.tbl_remove_leilao as r, atendentes as a 
                WHERE r.placa <> '' AND a.CodAtendente = r.usuario 
                ORDER BY r.id DESC LIMIT ?, ?
            ";

            $this->db->content = [];
            $this->db->content[] = [$offset, 'int'];
            // aqui está fazendo com que não passe de 1000.
            $this->db->content[] = [min(1000, $limit), 'int'];

            return $this->db->select();
        } catch (\Throwable $th) {
            //var_dump($th);
            throw new Exception("Não foi obter a lista de leilões removidos.");
        }
    }

    function totalLeiloesRemovidos () {
        try {
            $this->db->query = "SELECT COUNT(*) as total 
                FROM operador.tbl_remove_leilao as f
                WHERE f.placa <> ''
            ";
        
            return @$this->db->selectOne()->total ?: 0;
        } catch (\Throwable $th) {
            //var_dump($th);
            throw new Exception("Não foi obter total da lista de leilões removidos.");
        }
    }

    function removerLeilao ($placa, $operador, $motivo, $descricao) {
        try {

            $removidos = 0;
            $this->db->content = array($placa);
            $this->db->query = "DELETE FROM leilao.base_leilao WHERE PLACA = ?";
            if($this->db->delete()) $removidos++;
            $this->db->query = "DELETE FROM leilao.leilao_import WHERE CD_PLACA = ?";
            if($this->db->delete()) $removidos++;
            $this->db->query = "DELETE FROM leilao.tb_leilao WHERE CD_PLACA = ?";
            if($this->db->delete()) $removidos++;
            $this->db->query = "DELETE FROM leilao.tb_leilao_credauto WHERE Placa = ?";
            if($this->db->delete()) $removidos++;
            $this->db->query = "DELETE FROM leilao.tbleilao WHERE CD_PLACA = ?";
            if($this->db->delete()) $removidos++;

            if ($removidos) {
                $this->db->query = "INSERT INTO operador.tbl_remove_leilao (placa, usuario, justificar, descricao) 
                    VALUES (?, ?, ?, ?)
                ";
                $content = array();
                $content[] = array($placa);
                $content[] = array($operador, 'int');
                $content[] = array($motivo, 'int');
                $content[] = array($descricao);
                $this->db->content = $content;
                $this->db->insert();
            }
        
            return $removidos;
        } catch (\Throwable $th) {
            //var_dump($th);
            throw new Exception("Não foi remover leilão para placa: {$placa}.");
        }
    }

    function motivos () {
        $motivos = array();
        $motivos[1] = 'Localizar mais informações';
        $motivos[2] = 'Fornecedor corrigiu a base';
        $motivos[3] = 'Fornecedor removeu da base';
        $motivos[4] = 'Confirmado leilão, cliente afirma não ter leilão';
        $motivos[5] = 'Confirmado leilão, veículo divergente na base';
        $motivos[6] = 'Remover da base com carta do leiloeiro';
        $motivos[7] = 'Confirmar leilão cliente webserver';
        $motivos[8] = 'Cliente com nota fiscal do veículo desde zero, leilão removido';
        $motivos[9] = 'Juiz decretou baixa de leilão judicial';
        $motivos[10] = 'Divulgação por chassi ou placa parecido, removido da base';
        return $motivos;
    }
    
}
