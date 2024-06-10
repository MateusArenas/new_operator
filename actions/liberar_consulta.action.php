<?php
session_start();

require('Database.class.php');

$db = new Database();
$db->base = 'credauto';

$response = new stdClass();

$campos = $_POST;

		$db->query = "
			UPDATE
				consultas
			SET
				Motivo = '3',
				CodAtendente = ?,
				data_altera = ?,
				horaConc = ?,
				restricao1 = ?
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
					Motivo = '3',
					CodAtendente = ?,
					data_altera = ?,
					horaConc = ?,
					restricao1 = ?
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

		if($atualizado && $atualizado2){
			@file_get_contents("https://consulta.redecredauto.com.br/cache.php?codigo={$campos['Codigo']}&token=51e91d75-63e4-4901-9a68-d0ef9f361a23");

            $response->success = 'Consulta liberada com sucesso!';
		} else {

            $response->error = 'Erro ao liberar a consulta.';
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