<?php session_start();

@include_once('./config.php');
require('./classes/Database.class.php');
require('./classes/Users.class.php');
require('./classes/Tickets.class.php');
require('./classes/Functions.class.php');

$fn = new Functions();
$usrs = new Users();
$ticketsRepository = new Tickets();

$user_id = @$_SESSION["MSId"];

$user = $usrs->findById($user_id);

$db = new Database();
$ticketsRepository = new Tickets();

$ticket_id = @$_REQUEST['ticket_id'] or die('Ticket id não localizado.');
$status = @$_REQUEST['status'] or die('status não localizado.');

$response = new stdClass();

try {
    if ($ticket = $ticketsRepository->updateStatus($ticket_id, $user->id, $status)) {
        $response->success = "Status Alterado.";
    } else {
        throw new Exception('Não foi possivel atualizar chamado.');
    }
} catch (\Throwable $th) {
    $response->error = $th->getMessage();
}
?>

<?php if (@$response->success): ?>
        
    <div class="alert alert-success" role="alert">
        <?= $response->success ?>
        <div class="mt-2">
            <small class="text-muted">
                Para visualizar o chamado que foi aberto, por favor, <a href="#" onclick="location.reload()">recarregue a página</a> 
                ou clique no botão <code><i class="bi bi-arrow-clockwise"></i></code>.
            </small>
        </div>
    </div>


<?php else: ?>

    <?php if (@$response->error): ?>
        <div class="alert alert-warning" role="alert">
            <?= $response->error ?>
        </div>
    <?php endif; ?>

<?php endif; ?>