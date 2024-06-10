<?php
	ini_set('display_errors', 0); ini_set('display_startup_errors', 0); error_reporting(0);

    header("Access-Control-Allow-Origin: *");

    session_start();

    $id = @base64_decode($_REQUEST["MSId"]);
    $codConsulta = base64_decode(@$_REQUEST['consulta']);

    if(!isset($_REQUEST['MSId'])) die('Atendente não localizado');

    require('../classes/Database.class.php');
    require('../classes/Mailer.class.php');
        
    $db = new Database;
    $db->query = "SELECT * FROM atendentes WHERE CodAtendente = ? AND CodAtendente <> ''";
    $db->content = array($id, 'int');
    $atendente = $db->selectOne();

    if(!isset($atendente->CodAtendente)) die('Atendente não localizado');

    $db->query = "SELECT c.Codigo as codigo, c.TipoConsulta as codTipo, t.tipoconsulta as tipo, c.ItemConsultado as item, c.ValorItem as parametro, CONCAT(c.Data, ' ', c.Hora) as data FROM consultas as c, ttipoconsulta as t WHERE c.Codigo = ? AND t.id = c.TipoConsulta";
    $db->content = array($codConsulta, 'int');
    $consulta = $db->selectOne();

    if(!isset($consulta->codigo)) die('Consulta não localizada');

	$pedido = @$_REQUEST['pedido'];
	switch ($pedido) {
		case 1:	$pedido = "Exclusão de Consulta"; break;
		case 2:	$pedido = "Exclusão de Laudo"; break;
		case 3:	$pedido = "Remover Alerta de Obito"; break;
		case 4:	$pedido = "Remover Kart"; break;
		case 5:	$pedido = "Remover Farol"; break;
		case 6:	$pedido = "Atualizar Renajud"; break;
		case 7:	$pedido = "Remover Alerta de Motor"; break;
		case 8:	$pedido = "Atualizar Base Estadual"; break;
		case 9:	$pedido = "Atualizar Sinistro"; break;
		case 10: $pedido = "Atualizar Gravame"; break;
		case 11: $pedido = "Remover Ação Judicial"; break;	
		case 12: $pedido = "Atualizar Detran"; break;		
		case 13: $pedido = "Atualizar Tabela Fipe"; break;		
		case 999: $pedido = "Outros"; break;		
	}
	$motivo = @$_REQUEST['motivo'];
	$departamento = @$_REQUEST['departamento'];
	$url = "<a href='http://credoperador.com.br/acoes/acao.php?consulta={$consulta->codigo}&pedido={$_REQUEST['pedido']}&token=58490685-880496850543054-65478594739543'>Abrir Consulta</a>";
	$nome = ucwords(strtolower($atendente->NomeAtendente));
	$data = date('d/m/Y à\s H:i', strtotime($consulta->data));
	$parametro = strtoupper($consulta->parametro);
	$html = '';
	$html .= "<strong style='font-size: 18px;'>Nova Solicitação</strong><br /><br />";
	$html .= "<strong>Solicitante:</strong> {$nome}<br />";	
	$html .= "<strong>Pedido:</strong> {$pedido}<br />";	
	$html .= "<strong>Parametro:</strong> {$parametro}<br />";	
	$html .= "<strong>Data da Consulta:</strong> {$data}<br />";	
	$html .= "<strong>Código da Consulta:</strong> {$codConsulta}<br />";	
	$html .= "<strong>URL:</strong> {$url}<br />";		
	$html .= "<strong>Motivo:</strong> {$motivo}";		
	$mailer = new Mailer;
    $mailer->subject = $pedido . ' - Cred Operador'; 
    $mailer->body =  $html;
    $mailer->name = 'Cred Operador';
    $mailer->sender = "no-reply@redecredauto.com.br";
    switch ($departamento) {
    	case 1: $reply = "suporte@redecredauto.com.br"; break;    	
    	case 2: $reply = "financeiro@redecredauto.com.br"; break;    	
    	case 3: $reply = "vendas@redecredauto.com.br"; break;    	
    	case 4: $reply = "ti@redecredauto.com.br"; break;    	
    	default: $reply = "suporte@redecredauto.com.br"; break;
    }
    $mailer->reply = $reply;
    $mailer->email = "ti@redecredauto.com.br";

    // if(@$_REQUEST['pedido'] == 12 || @$_REQUEST['pedido'] == 6) $enviado = true;
    // else $enviado = $mailer->send();

    if($enviado = false){
    	if(@$consulta->codTipo == 7 || @$consulta->codTipo == 77 || @$consulta->codTipo == 19 || @$consulta->codTipo == 12 || @$consulta->codTipo == 14 || @$consulta->codTipo == 83 || @$consulta->codTipo == 9){
    		$db->query = "INSERT IGNORE INTO robo.tfila_atualiza (tipo, consulta, prioridade) VALUES (?, ?, 1)";
    		$content = array();
    		$content[] = array($consulta->codTipo, 'int');
    		$content[] = array($consulta->codigo, 'int');
    		$db->content = $content;
    		$db->insert();
    	} 

		$robo = false;
		switch((int)@$_REQUEST['pedido']){
			case 12:
			case 6:
			case 8:
			case 10:
			case 12:
			case 14:
				$robo = true;
			break;
		}

    	if($robo){
    		$html = '';
	    	$html .= "<strong>Sua solicitação está sendo processada pelo robô</strong><br /><br />";
	    	$html .= "<i style='font-size: 16px;'>Resumo:</i><br /><br />";
	    	$html .= "<strong>Solicitante:</strong> {$nome}<br />";		
	    	$html .= "<strong>Pedido:</strong> {$pedido}<br />";
			$html .= "<strong>Parametro:</strong> {$consulta['codTipo']}<br />";	
			$html .= "<strong>Tipo Consulta:</strong> {$parametro}<br />";	
			$html .= "<strong>Data da Consulta:</strong> {$data}<br />";	
			$html .= "<strong>Motivo:</strong> {$motivo}";

		    $mailer->subject = 'Solicitação enviada - ' . $pedido . ' - Cred Operador'; 
		    $mailer->body =  $html;
		    $mailer->name = 'Cred Operador';
		    $mailer->sender = "no-reply@redecredauto.com.br";
		    $mailer->reply = "no-reply@redecredauto.com.br";
		    $mailer->email = $reply;

		    $mailer->send();
    	} else {
	    	$html = '';
	    	$html .= "<strong>Sua solicitação foi enviada com sucesso</strong><br /><br />";
	    	$html .= "<i style='font-size: 16px;'>Resumo:</i><br /><br />";
	    	$html .= "<strong>Solicitante:</strong> {$nome}<br />";		
	    	$html .= "<strong>Pedido:</strong> {$pedido}<br />";
			$html .= "<strong>Parametro:</strong> {$consulta['codTipo']}<br />";	
			$html .= "<strong>Tipo Consulta:</strong> {$parametro}<br />";	
			$html .= "<strong>Data da Consulta:</strong> {$data}<br />";	
			$html .= "<strong>Motivo:</strong> {$motivo}";

		    $mailer->subject = 'Solicitação enviada - ' . $pedido . ' - Cred Operador'; 
		    $mailer->body =  $html;
		    $mailer->name = 'Cred Operador';
		    $mailer->sender = "no-reply@redecredauto.com.br";
		    $mailer->reply = "no-reply@redecredauto.com.br";
		    $mailer->email = $reply;

		    $mailer->send();
		}

    	echo 1;
    } else echo 0;

?>