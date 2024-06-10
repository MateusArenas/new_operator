<?php
session_start();

require('Database.class.php');

$db = new Database();
$db->base = 'credauto';

$response = new stdClass();

$campos = $_POST;


		$campos["gravame"] = str_replace("'", "", $campos["gravame"]);
		$campos["gravame"] = str_replace("ISO88591", "UTF-8", $campos["gravame"]);

		$campos["XML"] = str_replace("'", "", $campos["XML"]);
		$campos["XML"] = str_replace("ISO88591", "UTF-8", $campos["XML"]);

		$campos["recall"] = str_replace("'", "", $campos["recall"]);

		$campos["bin"] = str_replace("-", "", $campos["bin"]);
		$campos["bin"] = str_replace("ISO88591", "UTF-8", $campos["bin"]);

		$campos["gravame"] = str_replace("-", "", $campos["gravame"]);

		$campos["decodificador"] = str_replace("-", "", $campos["decodificador"]);
		$campos["decodificador"] = str_replace("'", "", $campos["decodificador"]);
		$campos["decodificador"] = str_replace("ISO88591", "UTF-8", $campos["decodificador"]);

		$campos["renajud"] = str_replace("-", "", $campos["renajud"]);

		$campos["proprietarios"] = str_replace("'", " ", $campos["proprietarios"]);

		$campos["leilao"] = str_replace("-", "", @$campos["leilao"]);

		$campos["leilao2"] = str_replace("-", "", @$campos["leilao2"]);

		$campos["pendencia"] = str_replace("-", "", @$campos["pendencia"]);
		$campos["pendencia"] = str_replace("'", "", $campos["pendencia"]);
		$campos["pendencia"] = str_replace("ISO88591", "UTF-8", $campos["pendencia"]);

		$campos["info"] = str_replace("-", "", @$campos["info"]);
		$campos["info"] = str_replace("'", "", $campos["info"]);
		$campos["info"] = str_replace("ISO88591", "UTF-8", $campos["info"]);
		$campos["info"] = str_replace("iso88591", "UTF-8", $campos["info"]);
		$campos["ssp"]  = str_replace("'", "", $campos["ssp"]);

		foreach ($campos as $key => $campo)
		{
			$campo = preg_replace("/\r|\n/", "", $campo);
			$campo = preg_replace('/\s+/', ' ', $campo);
			$campos[$key] = $campo;
		}

		print_r($campos['decodificador']);

		if ($campos["ssp"] != "") 
		{
			$db->query = "
				UPDATE 
					resultado_consultas 
				SET 
					Bin = ?, Proprietarios = ?, Estadual = ?, renajud = ?, decodificador = ?, SSP = ?, Gravame = ?, Leilao2 = ?, Leilao3 = ?, Sinistro = ?, Sinistro3 = ?, Sinistro2 = ?
				WHERE
					CodigoResu = ?
			";
			$content = array();
			$content[] = array(@$campos['bin']);
			$content[] = array(@$campos['proprietarios']);
			$content[] = array(@$campos['Estadual']);
			$content[] = array(@$campos['renajud']);
			$content[] = array(@$campos['decodificador']);
			$content[] = array(@$campos['ssp']);
			$content[] = array(@$campos['gravame']);
			$content[] = array(@$campos['leilao2']);
			$content[] = array(@$campos['leilao3']);
			$content[] = array(@$campos['sinistro']);
			$content[] = array(@$campos['sinistro3']);
			$content[] = array(@$campos['sinistro2']);
			$content[] = array(@$campos['Codigo'], 'int');
			$db->content = $content;
			$atualizado = $db->update();
			if(!$atualizado){
				$db->query = "
					UPDATE 
						resultado_consultas_historico
					SET 
						Bin = ?, Proprietarios = ?, Estadual = ?, renajud = ?, decodificador = ?, SSP = ?, Gravame = ?, Leilao2 = ?, Leilao3 = ?, Sinistro = ?, Sinistro3 = ?, Sinistro2 = ?
					WHERE
						CodigoResu = ?
				";
				$content = array();
				$content[] = array(@$campos['bin']);
				$content[] = array(@$campos['proprietarios']);
				$content[] = array(@$campos['Estadual']);
				$content[] = array(@$campos['renajud']);
				$content[] = array(@$campos['decodificador']);
				$content[] = array(@$campos['ssp']);
				$content[] = array(@$campos['gravame']);
				$content[] = array(@$campos['leilao2']);
				$content[] = array(@$campos['leilao3']);
				$content[] = array(@$campos['sinistro']);
				$content[] = array(@$campos['sinistro3']);
				$content[] = array(@$campos['sinistro2']);
				$content[] = array(@$campos['Codigo'], 'int');
				$db->content = $content;
				$atualizado = $db->update();
			}
		} 
		else 
		{
			$db->query = "
				UPDATE 
					resultado_consultas 
				SET 
					Bin = ?, Proprietarios = ?, Estadual = ?, renajud = ?, decodificador = ?, Gravame = ?, Leilao2 = ?, Leilao3 = ?, Sinistro = ?, Sinistro3 = ?, Sinistro2 = ?
				WHERE
					CodigoResu = ?
			";
			$content = array();
			$content[] = array(@$campos['bin']);
			$content[] = array(@$campos['proprietarios']);
			$content[] = array(@$campos['Estadual']);
			$content[] = array(@$campos['renajud']);
			$content[] = array(@$campos['decodificador']);
			$content[] = array(@$campos['gravame']);
			$content[] = array(@$campos['leilao2']);
			$content[] = array(@$campos['leilao3']);
			$content[] = array(@$campos['sinistro']);
			$content[] = array(@$campos['sinistro3']);
			$content[] = array(@$campos['sinistro2']);
			$content[] = array(@$campos['Codigo'], 'int');
			$db->content = $content;
			$atualizado = $db->update();
			if(!$atualizado){
				$db->query = "
					UPDATE 
						resultado_consultas_historico 
					SET 
						Bin = ?, Proprietarios = ?, Estadual = ?, renajud = ?, decodificador = ?, Gravame = ?, Leilao2 = ?, Leilao3 = ?, Sinistro = ?, Sinistro3 = ?, Sinistro2 = ?
					WHERE
						CodigoResu = ?
				";
				$content = array();
				$content[] = array(@$campos['bin']);
				$content[] = array(@$campos['proprietarios']);
				$content[] = array(@$campos['Estadual']);
				$content[] = array(@$campos['renajud']);
				$content[] = array(@$campos['decodificador']);
				$content[] = array(@$campos['gravame']);
				$content[] = array(@$campos['leilao2']);
				$content[] = array(@$campos['leilao3']);
				$content[] = array(@$campos['sinistro']);
				$content[] = array(@$campos['sinistro3']);
				$content[] = array(@$campos['sinistro2']);
				$content[] = array(@$campos['Codigo'], 'int');
				$db->content = $content;
				$atualizado = $db->update();
			}
		}
		if ($campos["bin"] == "")
		{
			$db->query = "
				UPDATE 
					resultado_consultas 
				SET 
					XML = ?, Proprietarios = ?, Estadual = ?, renajud = ?, decodificador = ?, Gravame = ?, Leilao2 = ?, Leilao3 = ?, Sinistro = ?, Sinistro3 = ?, Sinistro2 = ?
				WHERE
					CodigoResu = ?
			";
			$content = array();
			$content[] = array(@$campos['XML']);
			$content[] = array(@$campos['proprietarios']);
			$content[] = array(@$campos['Estadual']);
			$content[] = array(@$campos['renajud']);
			$content[] = array(@$campos['decodificador']);
			$content[] = array(@$campos['gravame']);
			$content[] = array(@$campos['leilao2']);
			$content[] = array(@$campos['leilao3']);
			$content[] = array(@$campos['sinistro']);
			$content[] = array(@$campos['sinistro3']);
			$content[] = array(@$campos['sinistro2']);
			$content[] = array(@$campos['Codigo'], 'int');
			$db->content = $content;
			$atualizado = $db->update();
			if(!$atualizado){
				$db->query = "
					UPDATE 
						resultado_consultas_historico 
					SET 
						XML = ?, Proprietarios = ?, Estadual = ?, renajud = ?, decodificador = ?, Gravame = ?, Leilao2 = ?, Leilao3 = ?, Sinistro = ?, Sinistro3 = ?, Sinistro2 = ?
					WHERE
						CodigoResu = ?
				";
				$content = array();
				$content[] = array(@$campos['XML']);
				$content[] = array(@$campos['proprietarios']);
				$content[] = array(@$campos['Estadual']);
				$content[] = array(@$campos['renajud']);
				$content[] = array(@$campos['decodificador']);
				$content[] = array(@$campos['gravame']);
				$content[] = array(@$campos['leilao2']);
				$content[] = array(@$campos['leilao3']);
				$content[] = array(@$campos['sinistro']);
				$content[] = array(@$campos['sinistro3']);
				$content[] = array(@$campos['sinistro2']);
				$content[] = array(@$campos['Codigo'], 'int');
				$db->content = $content;
				$atualizado = $db->update();
			}
		}

		$db->query = "
			UPDATE
				consultas
			SET
				CodAtendente = ?, data_altera = ?, horaConc = ?, restricao1 = ?
			WHERE
				Codigo = ?
		";
		$content = array();
		$content[] = array(@$_SESSION["MSId"]);
		$content[] = array(date("Ymd"));
		$content[] = array(date("H:i:s"));
		$content[] = array(@$campos["sinistro"]);
		$content[] = array(@$campos['Codigo'], 'int');
		$db->content = $content;
		$atualizado2 = $db->update();
		if(!$atualizado2){
			$db->query = "
				UPDATE
					consultas_historico
				SET
					CodAtendente = ?, data_altera = ?, horaConc = ?, restricao1 = ?
				WHERE
					Codigo = ?
			";
			$content = array();
			$content[] = array(@$_SESSION["MSId"]);
			$content[] = array(date("Ymd"));
			$content[] = array(date("H:i:s"));
			$content[] = array(@$campos["sinistro"]);
			$content[] = array(@$campos['Codigo'], 'int');
			$db->content = $content;
			$atualizado2 = $db->update();
		}

		$insert_leilao = @file_get_contents("https://webservice.redecredauto.com.br/base_binrf/sendbin_pretorno.php?c=22359723561211908631000120{$campos['Codigo']}+++++++++++++++X");
		if($atualizado && $atualizado2){
			@file_get_contents("https://consulta.redecredauto.com.br/cache.php?codigo={$campos['Codigo']}&token=51e91d75-63e4-4901-9a68-d0ef9f361a23");

            $response->success = 'Consulta salva com sucesso!';
		} else {
            $response->error = 'Erro ao salvar a consulta.';
		}
	
?>


<div id="alert-info" class="modal-content p-3 border-0 rounded-0 border-bottom">

	<?php if(@$response->success) : ?>

		<div class="alert alert-primary d-flex align-items-center p-2 mb-0" role="alert">
			<div class="d-flex flex-column flex-grow-1">
				<?=@$response->success?><br/><small><?=date('d/m/Y \à\s H:i:s')?></small>
			</div>
			<button type="button" class="btn-close me-2" aria-label="Close" onclick="$('#alert-info').slideUp()" ></button>
		</div>

	<?php endif; ?>

	<?php if(@$response->error) : ?>

		<div class="alert alert-danger d-flex align-items-center p-2 mb-0" role="alert">
			<div class="d-flex flex-column flex-grow-1">
                <?=@$response->error?><br/><small><?=date('d/m/Y \à\s H:i:s')?></small>
			</div>
			<button type="button" class="btn-close me-2" aria-label="Close" onclick="$('#alert-info').slideUp()" ></button>
		</div>

	<?php endif; ?>

</div>