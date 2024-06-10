<?php 
header("Access-Control-Allow-Origin: *");

@session_start();

@include_once('../config.php');
require('../classes/Database.class.php');
require('../classes/Atendente.class.php');
require('../classes/Consulta.class.php');

$Atendente = new Atendente();
$Consulta = new Consulta();

$response = new stdClass();

//code...
$id = @base64_decode($_REQUEST["MSId"]);
$codConsulta = base64_decode(@$_REQUEST['consulta']);

try {
    if(!isset($_REQUEST['MSId'])) throw new Exception('Id do Atendente não localizado');
    
    $atendente = $Atendente->findById($id);

    if (!$atendente) throw new Exception('Atendente não localizado');

    $username = $atendente->LoginAtendente ?: $atendente->NomeAtendente ?: $atendente->email;
    
    if (@$atendente->email) {
        $atendente_link = "mailto:$atendente->email";
    }
    
    $consulta = $Consulta->findByCodigo($codConsulta);

    if (!$consulta) throw new Exception('Consulta não localizada');
    
    // Redireciona caso esse atendente não tenha o slack_id em seu registro.
    if(@$atendente->slack_id) {
        header("location:acao_pedido_alteracao_slack.page.php?consulta={$_REQUEST['consulta']}&MSId={$_REQUEST['MSId']}"); 
    }
} catch (\Throwable $th) {
    $response->error = $th->getMessage();
}

?>


<form class="modal-content border-0"
    id="pedido_alteracao"
    data-form-type="ajax"
    data-form-target="#pedido_alteracao"
    data-form-cleanup="true"
    action="<?=$baseURL?>/actions/acao_enviar_pedido.page.php"
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
    
                            <div class="form-floating mb-3">
                                <select required class="form-select form-select-sm" name="pedido" >
                                    <option value="">Selecione</option>
                                    <option value="1">1) Exclusão de Consulta</option>
                                    <option value="2">2) Exclusão de Laudo</option>
                                    <option value="3">3) Remover Alerta de Obito</option>
                                    <option value="4">4) Remover Kart</option>
                                    <option value="5">5) Remover Farol</option>
                                    <option value="6">6) Atualizar Renajud</option>
                                    <option value="7">7) Remover Alerta de Motor</option>
                                    <option value="8">8) Atualizar Base Estadual</option>
                                    <option value="9">9) Atualizar Sinistro</option>
                                    <option value="10">10) Atualizar Gravame</option>
                                    <option value="11">11) Remover Ação Judicial</option>
                                    <option value="12">12) Atualizar Detran</option>
                                    <option value="13">13) Atualizar Tabela Fipe</option>
                                    <option value="14">14) Atualizar Ação Trabalhista</option>
                                    <option value="999">Outros</option>
                                </select>
                                <label for="floatingSelect">Pedido de Alteração*</label>
                            </div>
    
                            <div class="form-floating mb-3">
                                <select required class="form-select form-select-sm" name="departamento" >
                                    <option value="">Selecione</option>
                                    <option value="1">Suporte</option>
                                    <option value="2">Financeiro</option>
                                    <option value="3">Vendas</option>
                                    <option value="4">T.I.</option>						
                                </select>
                                <label for="floatingSelect">Seu Departamento*</label>
                            </div>
    
                            <div class="form-floating mb-3">
                                <textarea required class="form-control input-sm" placeholder="Motivo da Solicitação" name="motivo" rows="5" style="min-height: 120px;"></textarea>
                                <label for="floatingTextarea2">Motivo da Solicitação*</label>
                            </div>
    
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="modal-footer">
    
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
        <button type="submit" class="btn btn-primary">Enviar Solicitação</button>
      
    </div>
</form>
