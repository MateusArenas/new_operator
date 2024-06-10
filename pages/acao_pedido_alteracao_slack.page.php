<?php 
header("Access-Control-Allow-Origin: *");

@session_start();

@include_once('../config.php');
require('../classes/Database.class.php');
require('../classes/Atendente.class.php');
require('../classes/Consulta.class.php');
require('../classes/FilaAtualiza.class.php');

$Atendente = new Atendente();
$Consulta = new Consulta();
$FilaAtualiza = new FilaAtualiza();

$response = new stdClass();

//code...
$id = @base64_decode($_REQUEST["MSId"]);
$codConsulta = base64_decode(@$_REQUEST['consulta']);


try {
    if(!isset($_REQUEST['MSId'])) throw new Exception('Id do Atendente não localizado');
    
    $atendente = $Atendente->findById($id);
    
    if (!$atendente) throw new Exception('Atendente não localizado');

    $username = $atendente->LoginAtendente ?: $atendente->NomeAtendente;
    
    if (@$atendente->slack_id) {
        $atendente_link = "https://redecredautogroup.slack.com/team/$atendente->slack_id";
    }

    $consulta = $Consulta->findByCodigo($codConsulta);

    if (!$consulta) throw new Exception('Consulta não localizada');
    
    $robo = $FilaAtualiza->findByCodConsulta($consulta->codigo);

    $consultaLink = "https://www.credoperador.com.br/rpc/inc_consulta_normalizada.php?Codigo=".$consulta->codigo."&print=1&Tipo=".$consulta->codTipo;

} catch (\Throwable $th) {
    $response->error = $th->getMessage();
}

?>


<form  class="modal-content border-0"
    id="pedido_alteracao"
    data-form-type="ajax"
    data-form-target="#pedido_alteracao"
    data-form-cleanup="true"
    action="<?=$baseURL?>/actions/acao_enviar_pedido_slack.page.php"
    method="get"
>

    <div class="modal-header">
        <h1 class="modal-title fs-5">
        <i class="bi bi-broadcast me-2"></i>
            Solicitar Alteração
        </h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    
    <div class="modal-body">

        <!-- AQUI ALERTA CASO JÁ ESTIVER ESPERANDO POR ATUALIZAÇÂO -->
        <?php if(@$robo): ?>
            <div class="alert alert-warning" role="alert">
                Consulta em processamento no robô, aguarde a atualização automática.
            </div>
        <?php endif; ?>

        <!-- AQUI É MOSTRADO O ERRO CASO TENHA -->
        <?php if (@$response->error): ?> 
        
            <div class="alert alert-warning" role="alert">
                <?= $response->error ?>
            </div>

        <?php else: ?>
            <div class="container" style="width: 100%;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="well" id="solicitacao">

                            <input type="hidden" name="MSId" value="<?php echo base64_encode($id); ?>">
                            <input type="hidden" name="consulta" value="<?php echo base64_encode($codConsulta); ?>">
    
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="d-flex flex-column">
                                        <small class="mb-1">
                                            <span class="fw-semibold">Aberto por:</span>
                                            <br> 
                                            <?php if(@$atendente_link): ?>
                                                <a href="<?= $atendente_link ?>" target="_blank"
                                                    class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover"
                                                >
                                                    @<?= $atendente->LoginAtendente ?: $atendente->NomeAtendente ?>
                                                </a>
                                            <?php else: ?>
                                                @<?= $atendente->LoginAtendente ?: $atendente->NomeAtendente ?>
                                            <?php endif; ?>
                                        </small>
                                        
                                        <small class="mb-1">
                                            <span class="fw-semibold">Código:</span><br> 
                                            <?php if (@$consultaLink): ?> 
                                                <a href="<?= $consultaLink ?>" target="_blank"
                                                    class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover"
                                                >
                                                    <?= $consulta->codigo ?>
                                                </a>
                                            <?php else: ?>
                                                <?= $consulta->codigo ?>
                                            <?php endif; ?> 
                                        </small>

                                        <small class="mb-1">
                                            <span class="opacity-50 ">( Parâmetro )</span>
                                            <span class="fw-semibold text-capitalize"><?= $consulta->item ?><?php if ($consulta->uf) echo ' / UF' ?>:
                                            </span><br> <?= $consulta->parametro ?> <?php if ($consulta->uf) echo '/ ' . $consulta->uf ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex flex-column">

                                        <small class="mb-1">
                                            <span class="fw-semibold">Data Consulta:</span><br> <?= date('d/m/Y à\s H:i', strtotime($consulta->data)) ?>
                                        </small>

                                        <small class="mb-1">
                                            <span class="fw-semibold">Tipo Consulta:</span><br> <?= $consulta->tipo ?>
                                        </small>

                                        <small class="mb-1">
                                            <span class="fw-semibold">Cliente:</span><br> 
                                            <a href="#" class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover" 
                                                data-bs-open="modal" 
                                                data-bs-template="<?=$baseURL?>/pages/consulta_cliente.page.php?codCliente=<?= $consulta->cliente ?>&consulta=<?= base64_encode($consulta->codigo) ?>"
                                                data-bs-jsonb64="<?= $json_base64 ?>"
                                                data-bs-modaltype="lg"
                                            >
                                            <?= $consulta->cliente ?>
                                            </a>
                                        </small>

                                    </div>
                                </div>
                            </div>

                            <?php if($consulta->codTipo == 7 || $consulta->codTipo == 77 || $consulta->codTipo == 19 || $consulta->codTipo == 12 || $consulta->codTipo == 14 || $consulta->codTipo == 71 || $consulta->codTipo == 18 || $consulta->codTipo == 83 || $consulta->codTipo == 9): ?>
                                <div class="form-floating mb-3">
                                    <select class="selectable form-select form-select-sm" id="floatingSelect" name="pedido" required >
                                        <option value="">Selecione</option>
                                        <option value="1" data-toggle="collapse" data-target=".solicitarAtualizacao">Atualização Automática</option>
                                        <option value="2" data-toggle="collapse" data-target=".abrirChamado">Outras Solicitações</option>
                                    </select>
                                    <label for="floatingSelect">Selecione o Pedido de Alteração*</label>
                                </div>

                                <div class="abrirChamado collapse">
                                    <div class="form-floating mb-3">
                                        <textarea required class="form-control input-sm" placeholder="Motivo da Solicitação" name="descricao" style="min-height: 120px;"></textarea>
                                        <label>Descrição da Solicitação*</label>
                                    </div>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="pedido" value="2" >

                                <div class="form-floating mb-3">
                                    <textarea required class="form-control input-sm" placeholder="Motivo da Solicitação" name="descricao" style="min-height: 120px;"></textarea>
                                    <label>Descrição da Solicitação*</label>
                                </div>
                            <?php endif; ?>
    
                        </div>
                        
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="modal-footer">
    
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>

        <?php if($consulta->codTipo == 7 || $consulta->codTipo == 77 || $consulta->codTipo == 19 || $consulta->codTipo == 12 || $consulta->codTipo == 14 || $consulta->codTipo == 71 || $consulta->codTipo == 18 || $consulta->codTipo == 83 || $consulta->codTipo == 9): ?>
            <div class="solicitarAtualizacao collapse">
                <button type="submit" class="btn btn-primary">Solicitar Atualização</button>
            </div>
    
            <div class="abrirChamado collapse">
                <button type="submit" class="btn btn-primary">Abrir Chamado</button>
            </div>
        <?php else: ?>
            <button type="submit" class="btn btn-primary">Abrir Chamado</button>
        <?php endif; ?>

    </div>
</form>
