<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_erros', 1);
	error_reporting(0);

	session_start();

	#pagina de verificao de login do usuario, se cliente carrega sessao
	require_once('./classes/Database.class.php');

	$db = new Database();

	if(isset($_SESSION['MSId']))
	{
		// $sql = "UPDATE atendentes SET Data = ?, id_acesso ='' WHERE CodAtendente = ?";
		// $db->query = $sql;
		// $db->content = array(array(date("Ymd")), array($_SESSION["MSId"], 'int'));
		// $db->update();
	}

	session_destroy();

	echo "<script>location.href='login.php';</script>";
?>