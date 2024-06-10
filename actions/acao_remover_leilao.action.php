<?php @session_start();

    require('../classes/Helpers.class.php');

    require('../classes/Database.class.php');
    require('../classes/Leilao.class.php');

    $db = new Database();
    $leilaoClass = new Leilao();


    $response = new stdClass();

    try {
        $operador = @$_SESSION['MSId'];
        $placa = @$_POST['placa'];
        $justificativa = @$_POST['justificativa'];
        $descricao = @$_POST['descricao'];

        if (!$placa) throw new Error('Placa não localizada.');
        $placa = Helpers::validatePlaca($placa);
        
        if (!$operador) throw new Error('ID do Atendente não localizado.');
        
        if (!$justificativa) throw new Error('Justificativa não localizada.');
        
        $motivos = $leilaoClass->motivos();
        if (!@$motivos[$justificativa]) throw new Error('Justificativa inválida.');

        if (!$descricao) throw new Error('Descrição não localizada.');
        $descricao = Helpers::validateDescription($descricao);

        $placa_normal = Helpers::placaNormal($placa);
        $placa_mercosul = Helpers::placaMercosul($placa);

        // $placa_normal_formatada = Helpers::formatarPlaca($placa_normal);
        // $placa_mercosul_formatada = Helpers::formatarPlaca($placa_mercosul);
        
        // var_dump([
        //     "placa" => $placa,
        //     "placa_normal" => $placa_normal,
        //     "placa_mercosul" => $placa_mercosul,
        //     "operador" => $operador,
        //     "justificativa" => $justificativa,
        //     "descricao" => $descricao,
        // ]);

        $placa_formatada = Helpers::formatarPlaca($placa);

        $response->success = "O leilão foi removido com sucesso para a placa {$placa_formatada}.";
        $response->success .= "<br>";
        $response->success .= "Justificativa: {$motivo}.";
        $response->success .= "<br>";
        $response->success .= "Descrição: {$descricao}.";
    
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
                Para visualizar a ação de remoção de leilão no histórico, por favor, <a href="#" onclick="location.reload()">recarregue a página</a> 
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