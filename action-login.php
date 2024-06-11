<?php session_start();

@include_once('./config.php');
require('./classes/Helpers.class.php');
require('./classes/Database.class.php');
require('./classes/Users.class.php');
require('./classes/Slack.class.php');


$db = new Database();
$slackApi = new Slack();
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
    $_SESSION["MSPermission"] = $user->type;

        // caso a imagem esteje fora por algum motivo.
    if (!Helpers::existeImagem($user->image_url) and $user->slack_id) {
        $slack_user = $slackApi->findUser($user->slack_id);

        if ($slack_user) {
            // coloca a imagem nova, no lugar da antiga.
            $user->image_url = $slack_user->profile->image_512;

            // salva no banco essa nova imagem.
            $db->query = "UPDATE users SET image_url = ? WHERE id = ?";
            $db->content = array(array($user->image_url), array($user->id, 'int'));
            $db->update();
        }

    }

    $_SESSION["MSPerfilImagem"] = $user->image_url;

    header("Location: dashboard.php?Pagina=RelatorioDiario");
    exit;
} else {
    die('Email ou Senha invalidos.');
}


?>