<?php
	require_once('./classes/Helpers.class.php');

	if (@$_POST['Login'] && @$_POST['Senha'])
	{
		session_start();

		$ses_id = session_id ();
		$redirect = @$_POST['redirect'];
		$login = addslashes($_POST["Login"]);
		$senha = addslashes($_POST["Senha"]);

		require_once('./classes/Slack.class.php');
        require_once('./classes/Database.class.php');

		$slackApi = new Slack();
		$db = new Database();
		$db->base = 'credauto';

		if (date('w') != 0)
		{
			$sql = "SELECT * FROM atendentes WHERE LoginAtendente = ? AND horaentrada <= CURRENT_TIME AND horasaida >= CURRENT_TIME AND situacao = 1";
			$db->query = $sql;
			$db->content = array($login);
			$r = $db->selectOne();
			if($r)
			{
				$sql = "UPDATE atendentes SET acesso = 0 WHERE CodAtendente = ?";
				$db->query = $sql;
				$db->content = array($r->CodAtendente, 'int');
				$db->update();
			}
			$sql = "SELECT * FROM atendentes WHERE LoginAtendente = ? AND SenhaAtendente = ? AND acesso = 0 AND situacao = 1";
		}
		else
		{
			$sql = "SELECT * FROM atendentes WHERE LoginAtendente = ? AND SenhaAtendente = ? AND situacao = 1";
		}

		$db->query = $sql;
		$db->content = array(
			array($login),
			array($senha)
		);
		$result = $db->selectOne();
		if($result)
		{
			$_SESSION["MSLogin"] = $login;
			$_SESSION["MSNome"] = @$result->NomeNovoAtendente ?: @$result->NomeAtendente;
			$_SESSION["MSId"] = $result->CodAtendente;
			$_SESSION["MSSlackId"] = $result->slack_id;
			$_SESSION["MSPermissoes"] = $result->Permissoes;
			$_SESSION["MSPermissoes2"] = $result->Permissoes2;
			$_SESSION["MSPermissoes3"] = $result->Permissoes3;

			// caso a imagem esteje fora por algum motivo.
			if (!Helpers::existeImagem($result->ImagemAtendente)) {
				$slack_user = $slackApi->findUser($result->slack_id);

				if ($slack_user) {
					// coloca a imagem nova, no lugar da antiga.
					$result->ImagemAtendente = $slack_user->profile->image_512;
	
					// salva no banco essa nova imagem.
					$db->query = "UPDATE credauto.atendentes SET ImagemAtendente = ? WHERE CodAtendente = ?";
					$db->content = array(array($result->ImagemAtendente), array($result->CodAtendente, 'int'));
					$db->update();
				}

			}

			$_SESSION["MSPerfilImagem"] = $result->ImagemAtendente;


			$sql = "UPDATE atendentes SET Data = REPLACE(CURRENT_DATE, '-', ''), Hora = CURRENT_TIME, id_acesso = ? WHERE CodAtendente = ?";
			$db->query = $sql;
			$db->content = array(
				array($ses_id), 
				array($result->CodAtendente, 'int')
			);
			$db->update();

			if($redirect)
			{
				if(@$_REQUEST['base64'])
				{
					$redirect = base64_decode($redirect);
				}
				header("Location: {$redirect}");
				echo "<script>location.href='{$redirect}';</script>";
				exit;
			}
			else 
			{
				header("Location: dashboard.php?Pagina=RelatorioDiario");
				echo "<script>location.href='dashboard.php?Pagina=RelatorioDiario';</script>";
				exit;
			}

		}
		else
		{
			echo "<script>alert('Usuario ou Senha Invalido !');</script>";
			header("Location: login.php");
			echo "<script>location.href='login.php';</script>";
			exit;
		}
	}
?>
