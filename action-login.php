<?php session_start();

@include_once('./config.php');
require('./classes/Database.class.php');
require('./classes/Users.class.php');

$db = new Database();

$User = new Users();

$post = (object)@$_POST;

if (!$post->email || !$post->password) {
    die('Email\Senha não localizados.');
}

if ($user = $User->login($post->email, $post->password)) {
    $_SESSION["MSLogin"] = $user->email;
    $_SESSION["MSNome"] = $user->nome;
    $_SESSION["MSId"] = $user->id;
    $_SESSION["MSSlackId"] = $user->slack_id;

    header("Location: dashboard2.php?Pagina=RelatorioDiario");
    exit;
} else {
    die('Email ou Senha invalidos.');
}


?>