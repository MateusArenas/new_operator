<?php @session_start();

    require('../classes/Helpers.class.php');

    require('../classes/Database.class.php');
    require('../classes/Consulta.class.php');

    $db = new Database();
    $consultaClass = new Consulta();

    $response = new stdClass();

    try {
        $operador = @$_SESSION['MSId'];
        $consulta = @$_POST['consulta'];
        $justificativa = @$_POST['justificativa'];

        if (!$consulta) throw new Error('Consulta não localizada.');
        
        if (!$operador) throw new Error('ID do Atendente não localizado.');
        
        if (!$justificativa) throw new Error('Justificativa não localizada.');
        
        $motivos = $consultaClass->motivos();
        $motivo = @$motivos[$justificativa];
        if (!$motivo) throw new Error('Justificativa inválida.');

        // var_dump([
        //     "consulta" => $consulta,
        //     "operador" => $operador,
        //     "justificativa" => $justificativa,
        // ]);

        $response->success = "Óbito removido da consulta {$consulta} com sucesso.";
        $response->success .= "<br>";
        $response->success .= "Justificativa: {$motivo}.";

    } catch (\Throwable $th) {
        // var_dump($th);
        $response->error = $th->getMessage();
    }

?>

<?php if (@$response->success): ?>
        
    <div class="alert alert-success" role="alert">
        <?= $response->success ?>
        <div class="mt-2">
            <small class="text-muted">
                Para visualizar a ação de remoção de óbito da consulta no histórico, por favor, <a href="#" onclick="location.reload()">recarregue a página</a> 
                ou clique no botão <code><i class="bi bi-arrow-clockwise"></i></code> em 
                <code><i class="bi bi-clock-history"></i> Histórico de Alterações</code>.
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