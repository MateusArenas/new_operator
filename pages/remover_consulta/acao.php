<?php @session_start();

    require('../../classes/Helpers.class.php');
    require('../../classes/Api.class.php');

    require('../../classes/Database.class.php');
    require('../../classes/Consulta.class.php');

    $db = new Database();
    $consultaClass = new Consulta();
    
    $api = new Api();

    $response = new stdClass();

    try {
        $operador = @$_SESSION['MSId'];
        $consulta = @$_POST['consulta'];
        $justificativa = @$_POST['justificativa'];
        $descricao = @$_POST['descricao'];
        
        $motivos = $consultaClass->motivos();
        $motivo = @$motivos[$justificativa];

        $response = $api->post('/remover_consulta', [ 
            "consulta" => $consulta,
            "operador" => $operador,
            "justificativa" => $justificativa,
            "descricao" => $descricao,
        ]);

        var_dump($response);

        if (!$response) 
        {
            throw new Exception("Erro de conexão. Verifique sua conexão com a internet");
        } 
        else if (@$response->error) 
        {
            throw new Exception($response->error);
        } 
        else if (@$response->warning) 
        {
            throw new Exception($response->warning);
        } 
        else if (@$response->success) 
        {
            $response->success = "A consulta {$consulta} foi removida com sucesso.";
            $response->success .= "<br>";
            $response->success .= "Justificativa: {$motivo}.";
            $response->success .= "<br>";
            $response->success .= "Descrição: {$descricao}.";
        } 
        else 
        {
            throw new Exception("Erro de conexão. Verifique sua conexão com a internet");
        }

    } catch (\Exception $e) {
        // var_dump($th);
        @$response->error = $e->getMessage();
    }

?>

<?php if (@$response->success): ?>
        
    <div class="alert alert-success" role="alert">
        <?= $response->success ?>
        <div class="mt-2">
            <small class="text-muted">
                Para visualizar a ação de remoção de consulta no histórico, por favor, <a href="#" onclick="location.reload()">recarregue a página</a> 
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