<?php

    class Chamado {

        private $db;

        function __construct() {
            $this->db = new Database();
        }
		
		public function selecionarCliente($cod_cliente){
			$this->db->query = "SELECT * FROM clientes WHERE Codigo = ?";
			$this->db->content =  array($cod_cliente, 'int');

			return $this->db->selectOne();
		}

		public function selecionarAtendente($cod_atendente){
			$this->db->query = "SELECT * FROM atendentes WHERE CodAtendente = ?";
			$this->db->content =  array($cod_atendente, 'int');

			return $this->db->selectOne();
		}

		public function listarChamados(){
			$this->db->query = "SELECT * FROM t_chamados ORDER BY FIELD(status, 2, 1, 3), abertura LIMIT 10";

			return $this->db->select();
		}

		public function paginarChamados($offset, $limit){
			$this->db->query = "SELECT * FROM t_chamados ORDER BY FIELD(status, 2, 1, 3), abertura LIMIT ?, ?";
			$this->db->content = [];
			$this->db->content[] = [$offset ?: 0, 'int'];
			$this->db->content[] = [$limit ?: 100, 'int'];		
			return $this->db->select();
		}

        public function countChamados(){
			$this->db->query = "SELECT COUNT(*) as total FROM t_chamados";
			return $this->db->selectOne();
		}

		public function selecionarChamados($cod_chamado){
			$this->db->query = "SELECT * FROM t_chamados WHERE cod = ?";
			$this->db->content =  array($cod_chamado, 'int');

			return $this->db->selectOne();
		}

		public function selecionarDescricao($cod_chamado){
			$this->db->query = "SELECT * FROM t_descricao_chamado WHERE cod_chamado = ? ORDER BY criacao ASC";
			$this->db->content =  array($cod_chamado, 'int');

			return $this->db->select();
		}

		public function atualizarChamado($status, $cod_chamado){
            $this->db->query = "UPDATE t_chamados SET status = ?, atualizacao = NOW() WHERE cod = ?";
			$content = array();
			$content[] = array($status);
			$content[] = array($cod_chamado, 'int');
			$this->db->content = $content;

			return $this->db->update();
		}

		public function gravarChamado($dados){
			
			$sql  = "INSERT INTO t_chamados (requerente, codigo, nome, solicitacao, motivo, provedor, provedor_2, provedor_3, cod_atendente, placa, status) ";
			$sql .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
						
			$content = array();
		
			$content[] = array($dados['requerente'], 'int');
			$content[] = array($dados['codigo_req'], 'int');
			$content[] = array($dados['nome']);
			$content[] = array($dados['solicitacao'], 'int');
			$content[] = array($dados['motivo'], 'int');
			$content[] = array($dados['provedor'], 'int');
			$content[] = array($dados['provedor2'], 'int');
			$content[] = array($dados['provedor3'], 'int');
			$content[] = array($dados['cod_atendente'], 'int');
			$content[] = array($dados['placa']);
					
			$this->db->query = $sql;
			$this->db->content = $content;

			return $this->db->insertId();
		}

		public function gravarDescricaoChamado($cod_chamado, $descricao, $cod_atendente){
			
			$sql  = "INSERT INTO t_descricao_chamado (cod_chamado, descricao, cod_atendente) ";
			$sql .= "VALUES (?, ?, ?)";
						
			$content = array();
		
			$content[] = array($cod_chamado, 'int');
			$content[] = array($descricao);
			$content[] = array($cod_atendente, 'int');
			
			$this->db->query = $sql;
			$this->db->content = $content;

			return $this->db->insert();
		}

		public function relatorio($dados){

			$sql  = "SELECT status, COUNT(STATUS) AS total FROM t_chamados WHERE ";

			$sql .= "requerente = ".((@$dados['requerente'])? '?': 'requerente');
			$sql .= " AND ";
			$sql .= "solicitacao = ".((@$dados['solicitacao'])? '?': 'solicitacao');
			$sql .= " AND ";
			$sql .= "motivo = ".((@$dados['motivo'])? '?': 'motivo');
			$sql .= " AND ";
			$sql .= "(";
			$sql .= "provedor = ".((@$dados['provedor'])? '?': 'provedor');
			$sql .= " OR ";
			$sql .= "provedor_2 = ".((@$dados['provedor'])? '?': 'provedor_2');
			$sql .= " OR ";
			$sql .= "provedor_3 = ".((@$dados['provedor'])? '?': 'provedor_3');
			$sql .= ")";
			$sql .= " AND ";
			$sql .= "placa = ".((@$dados['placa'])? '?': 'placa');
			$sql .= " AND ";
			$sql .= "codigo = ".((@$dados['codigo_req'])? '?': 'codigo');

			if (@$dados['status_1'] && @$dados['status_2'] && @$dados['status_3']){
				$sql .= " AND (STATUS = 1 OR STATUS = 2 OR STATUS = 3)";
			}elseif (@$dados['status_1'] && @$dados['status_2']) {
				$sql .= " AND (STATUS = 1 OR STATUS = 2)";
			}elseif (@$dados['status_1'] && @$dados['status_3']) {
				$sql .= " AND (STATUS = 1 OR STATUS = 3)";
			}elseif (@$dados['status_2'] && @$dados['status_3']) {
				$sql .= " AND (STATUS = 2 OR STATUS = 3)";
			}else{
				if (@$dados['status_1']){
					$sql .= " AND STATUS = 1";
				}
				if (@$dados['status_2']){
					$sql .= " AND STATUS = 2";
				}
				if (@$dados['status_3']){
					$sql .= " AND STATUS = 3";
				}
			}
		
			if (@$dados['data_inicio'] && $dados['data_final']){
				$sql .= " AND (abertura BETWEEN '{$dados['data_inicio']} 00:00:00' AND '{$dados['data_final']} 23:59:59')";
			}elseif(@$dados['data_inicio']) {
				$sql .= " AND (abertura BETWEEN '{$dados['data_inicio']} 00:00:00' AND '".date('Y-m-d')." 23:59:59')";
			}elseif (@$dados['data_final']) {
				$sql .= " AND abertura <= '{$dados['data_final']} 23:59:59'";
			}

			$sql .= " GROUP BY status";

			$content = array();

			if (@$dados['requerente'])  $content[] = array($dados['requerente'], 'int');
			if (@$dados['solicitacao']) $content[] = array($dados['solicitacao'], 'int');
			if (@$dados['motivo'])      $content[] = array($dados['motivo'], 'int');
			if (@$dados['provedor']) $content[] = array($dados['provedor'], 'int');
			if (@$dados['provedor']) $content[] = array($dados['provedor'], 'int');
			if (@$dados['provedor']) $content[] = array($dados['provedor'], 'int');
			if (@$dados['placa']) $content[] = array($dados['placa']);
			if (@$dados['codigo']) $content[] = array($dados['codigo'], 'int');
			
			$this->db->query = trim($sql);
			$this->db->content = $content;
			
			return $this->db->select();
		}

		public function relatorioDetalhe($dados){

			$sql  = "SELECT * FROM t_chamados WHERE ";

			$sql .= "requerente = ".((@$dados['requerente'])? '?': 'requerente');
			$sql .= " AND ";
			$sql .= "solicitacao = ".((@$dados['solicitacao'])? '?': 'solicitacao');
			$sql .= " AND ";
			$sql .= "motivo = ".((@$dados['motivo'])? '?': 'motivo');
			$sql .= " AND ";
			$sql .= "(";
			$sql .= "provedor = ".((@$dados['provedor'])? '?': 'provedor');
			$sql .= " OR ";
			$sql .= "provedor_2 = ".((@$dados['provedor'])? '?': 'provedor_2');
			$sql .= " OR ";
			$sql .= "provedor_3 = ".((@$dados['provedor'])? '?': 'provedor_3');
			$sql .= ")";
			$sql .= " AND ";
			$sql .= "status = ".((@$dados['status'])? '?': 'status');
			$sql .= " AND ";
			$sql .= "placa = ".((@$dados['placa'])? '?': 'placa');
			$sql .= " AND ";
			$sql .= "codigo = ".((@$dados['codigo_req'])? '?': 'codigo');
		
			if (@$dados['data_inicio'] && $dados['data_final']){
				$sql .= " AND (abertura BETWEEN '{$dados['data_inicio']} 00:00:00' AND '{$dados['data_final']} 23:59:59')";
			}elseif(@$dados['data_inicio']) {
				$sql .= " AND (abertura BETWEEN '{$dados['data_inicio']} 00:00:00' AND '".date('Y-m-d')." 23:59:59')";
			}elseif (@$dados['data_final']) {
				$sql .= " AND abertura <= '{$dados['data_final']} 23:59:59'";
			}

			$sql .= " ORDER BY status, abertura";

			$content = array();

			if (@$dados['requerente'])  $content[] = array($dados['requerente'], 'int');
			if (@$dados['solicitacao']) $content[] = array($dados['solicitacao'], 'int');
			if (@$dados['motivo'])      $content[] = array($dados['motivo'], 'int');
			if (@$dados['provedor']) $content[] = array($dados['provedor'], 'int');
			if (@$dados['provedor']) $content[] = array($dados['provedor'], 'int');
			if (@$dados['provedor']) $content[] = array($dados['provedor'], 'int');
			if (@$dados['status']) $content[] = array($dados['status'], 'int');
			if (@$dados['placa']) $content[] = array($dados['placa']);
			if (@$dados['codigo']) $content[] = array($dados['codigo'], 'int');
			
			$this->db->query = trim($sql);
			$this->db->content = $content;
			
			return $this->db->select();
		}

		public function relatorioEmail($provedor, $solicitacao){
			$sql  = "SELECT status, COUNT(STATUS) AS total FROM t_chamados WHERE ";
			$sql .= "(IF(atualizacao, atualizacao, abertura) >= CONCAT(CURRENT_DATE - INTERVAL 1 DAY, ' 00:00:00')";
			$sql .= "AND ";
			$sql .= "IF(atualizacao, atualizacao, abertura) <= CONCAT(CURRENT_DATE - INTERVAL 1 DAY, ' 23:59:59'))";
			$sql .= "AND ";
			$sql .= "provedor = ? AND solicitacao = ? GROUP BY STATUS";

			$content = array();

			$content[] = array($provedor, 'int');
			$content[] = array($solicitacao, 'int');
			
			$this->db->query = $sql;
			$this->db->content =  $content;
			
			return $this->db->select();
		}

		public function verificarRelatorioEmailDia(){
			$sql  = "SELECT cod FROM t_relatorio_chamado WHERE data = '".date("Y-m-d")."'";
			
			$this->db->query = $sql;
			
			return $this->db->selectOne();
		}

		public function inserirRelatorioEmailDia(){
			$sql  = "UPDATE t_relatorio_chamado SET data = NOW() WHERE cod = 1";
			
			$this->db->query = $sql;
			
			return $this->db->update();
		}

		public function listarRepresentantes(){
			
			$sql  = "SELECT * FROM trepresentante WHERE status = 0 ORDER BY representante";
			
			$this->db->query = $sql;
			
			return $this->db->select();
		}

		public function selecionarRepresentante($cod_representante){
			
			$sql  = "SELECT * FROM trepresentante WHERE id = ?";
			
			$this->db->query = $sql;
			$this->db->content =  array($cod_representante, 'int');
			
			return $this->db->selectOne();
		}

		public function gravarClienteSemContato($tel_numero, $cod_cliente, $cod_atendente, $status){
			
			$sql  = "INSERT INTO operador.tbl_cliente_sem_contato (tel_numero, cod_cliente, cod_atendente, status) ";
			$sql .= "VALUES (?, ?, ?, ?)";
						
			$content = array();
		
			$content[] = array($tel_numero);
			$content[] = array($cod_cliente, 'int');
			$content[] = array($cod_atendente, 'int');
			$content[] = array($status, 'int');
							
			$this->db->query = $sql;
			$this->db->content = $content;

			return $this->db->insertId();
		}

		public function verificarMensagemJaEnviada($tel_numero, $cod_cliente){
			
			$sql  = "SELECT * FROM operador.tbl_cliente_sem_contato ";
			$sql .= "WHERE tel_numero = ? AND cod_cliente = ? AND DATE(data) = '".date("Y-m-d")."'";

			$content = array();
		
			$content[] = array($tel_numero);
			$content[] = array($cod_cliente, 'int');
						
			$this->db->query = $sql;
			$this->db->content = $content;
			
			return $this->db->selectOne();
		}

		
		public function verificarCliente($cod_cliente){
			
			$sql = "SELECT * FROM credauto.tcadcli WHERE cancelado = 0 AND ID = ?";
			
			$this->db->query = $sql;
			$this->db->content = array($cod_cliente);
			
			return $this->db->selectOne();
		}
		
    }
?>