<?php 

class ConsultaRepository
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    public function countAll ($document = NULL, $status = NULL, $from_date = NULL) {
        try {
            $query = "SELECT COUNT(*) as count FROM manager.tbl_cliente WHERE cod IS NOT NULL";
            $content = array();

            if ($document) {
                $only_numbers = preg_replace("/[^0-9]/", "", $document);

                $field = (strlen($only_numbers) == 14) ? "$.cnpj" : "$.cpf_desp";

                $query .= " AND (JSON_EXTRACT(dados_cliente, '{$field}') LIKE ? OR JSON_EXTRACT(dados_cliente, '{$field}') LIKE ?)";
                $content[] = array("%$document%");
                $content[] = array("%$only_numbers%");

                // $query .= " AND (dados_cliente LIKE ? OR dados_cliente LIKE ?)";
                // $content[] = array("%$document%");
                // $content[] = array("%$only_numbers%");
            }

            if (is_numeric($status)) {
                $query .= " AND situacao = ? ";
                $content[] = array($status, 'int');
            }

            if ($from_date) {
                $query .= " AND SUBSTR(criacao, 1, 10) <= ? ";
                $content[] = array($from_date);
            }

            $this->db->query = $query;
            $this->db->content = $content;

            return (int)$this->db->selectOne()->count;
        } catch (\Throwable $th) {
            throw new Exception("Não foi possivel contar contratos");
        }
    }

    // retorna todos os contratos atravez dos campos (CNPJ, STATUS, DATA) e possue paginação
    public function findAll (ListConsultaDTO $payload) {
        try {
            if (
                !$payload->login && 
                !$payload->data && 
                !$payload->parametro && 
                !$payload->tipoconsulta &&
                !$payload->atividade &&
                !$payload->estado && 
                !$payload->leilao &&
                !$payload->sinistro &&
                !$payload->operador
            ) {
                return [ "query" => $payload->login, "content" => [] ];
            } else {
    
                $query = "SELECT c.*, ttc.tipoconsulta as NomeConsulta
                    FROM credauto.consultas as c 
                    LEFT JOIN credauto.tcadcli as tc ON c.CodCliente = tc.ID
                    LEFT JOIN credauto.ttipoconsulta AS ttc ON c.TipoConsulta = ttc.id
                    WHERE c.CodCliente IS NOT NULL
                ";

                // -- AND CodCliente = ?
                // -- AND TipoConsulta = ?
                // -- AND atualiza = ?
                // -- AND UF = ?
                // -- AND ValorItem = ?
                // -- AND Codigo = ?
                // -- AND CodAtendente = ?
                // -- AND Data = ?
                // -- AND Hora = ?
    
                $content = array();

                if ($payload->data) {
                    $query .= " AND c.Data >= ? ";
                    $content[] = array($payload->data);
                }

                if ($payload->login) {
                    $query .= " AND c.CodCliente = ? ";
                    $content[] = array($payload->login, 'int');
                }

                if ($payload->parametro) 
                {
                    $query .= " AND c.ValorItem = ? ";
                    $content[] = array($payload->parametro);
                } 

                if ($payload->tipoconsulta) {
                    $query .= " AND c.TipoConsulta = ? ";
                    $content[] = array($payload->tipoconsulta);
                }

                if ($payload->atividade) {
                    $query .= " AND tc.atividade = ? ";
                    $content[] = array($payload->atividade);
                }
                
                if ($payload->estado) {
                    $query .= " AND c.UF = ? ";
                    $content[] = array($payload->estado);
                }

                // restrições inicio

                if ($payload->leilao) {
                    $query .= " AND c.leilao = 1 ";
                }

                if ($payload->sinistro) {
                    $query .= " AND c.sinistro = 1 ";
                }

                //hist/rf = restrição2
                //score = (restrição1 = 2)
                //falta: vec/roubo

                if ($payload->hist_rf) {
                    $query .= " AND c.restricao2 = 1 ";
                }

                if ($payload->score) {
                    $query .= " AND c.restricao1 = 2 ";
                }

                // restrições fim

                if ($payload->operador) {
                    $query .= " AND c.CodAtendente = ? ";
                    $content[] = array($payload->operador, 'int');
                }


                // if ($payload->tipoconsulta) {
                //     $query .= " AND TipoConsulta = ? ";
                //     $content[] = array($payload->tipoconsulta, 'int');
                // }
                
                if (count($payload->orderby_asc) or count($payload->orderby_desc)) {
                    $query .= " ORDER BY ";
                    // $query .= "ORDER BY `estado` DESC, `nome` ASC";

                    $orderby = array();

                    foreach ($payload->orderby_asc as $field) {
                        $orderby[] = " c.$field ASC ";
                        // $content[] = array($field);
                    }

                    foreach ($payload->orderby_desc as $field) {
                        $orderby[] = " c.$field DESC ";
                        // $content[] = array($field);
                    }

                    $query .= join(',', $orderby);
                }
    
                $query .= " LIMIT ?, ?;";
    
                $content[] = array($payload->offset ?: 0, 'int');
                $content[] = array($payload->limit ?: 20, 'int');

                // var_dump($query);
                // var_dump($content);

                // throw new Exception('\n\n\n\n\  ' . $query . "    \n:   " . json_encode($content) . " \n aqui \n" . json_encode($payload->orderby_asc));
            
                $this->db->query = $query;
                $this->db->content = $content;
    
                return $this->db->select();
            }
        } catch (\Throwable $th) {
            // throw new Exception("Não foi possivel buscar contratos");
            throw $th;
        }
    }

    // busca um contrato através do cod
    public function findByCod ($cod_client) {
        try {

            $query = "SELECT * FROM manager.tbl_cliente WHERE cod = ? ";
            
            $content = array();
            $content[] = array($cod_client, 'int');

            $this->db->query = $query;
            $this->db->content = $content;

            return $this->db->selectOne();
        } catch (\Throwable $th) {
            throw new Exception("Não foi possivel obter contrato");
        }
    }

}


?>


