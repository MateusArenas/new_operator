<?php @session_start();

@include_once('../../config.php');

@include_once('../../classes/Database.class.php');
@include_once('../../classes/Chamado.class.php');
@include_once('../../classes/Functions.class.php');

$c = new Chamado();
$f = new Functions();

$requerente[] = array('valor' => 1, 'nome' => 'Cliente');
$requerente[] = array('valor' => 2, 'nome' => 'Representante');
$requerente[] = array('valor' => 3, 'nome' => 'Operador');

$solicitacao[] = array('valor' => 1, 'nome' => 'Contestação Leilão');
$solicitacao[] = array('valor' => 2, 'nome' => 'Contestação Indicio');
$solicitacao[] = array('valor' => 3, 'nome' => 'Contestação Hist. RF');

$motivo[] = array('valor' => 1, 'nome' => 'Não Existe');
$motivo[] = array('valor' => 2, 'nome' => 'Existe');
$motivo[] = array('valor' => 3, 'nome' => 'Comitente');

$provedor[] = array('valor' => 1, 'nome' => 'Infocar');
$provedor[] = array('valor' => 2, 'nome' => 'Auto Risco');
$provedor[] = array('valor' => 3, 'nome' => 'Checkpro');
$provedor[] = array('valor' => 4, 'nome' => 'Motor Consulta');


$count = $c->countChamados();

// Recebendo o offset via GET
$pagina = @$_GET['pagina'] ?? 1;

// Total de itens
$totalItens = @$count->total ?: 0;

// Itens por página
$itensPorPagina = 10;

// Calcula o offset e não deixa passar de menos zero
$offset = max(0, ($pagina - 1) * $itensPorPagina);

// Calcula o total de páginas
$totalPaginas = ceil($totalItens / $itensPorPagina);

// Calcula a página atual
$paginaAtual = ($offset / $itensPorPagina);

// Quando realmente possue resgistros ele adiciona +1 na pagina.
if ($totalPaginas > 0) $paginaAtual++;


// if (isset($_GET['requerente']) && isset($_GET['solicitacao']) && isset($_GET['motivo']) && isset($_GET['provedor']) && isset($_GET['status'])){
//     $chamados = $c->relatorioDetalhe($_GET);
//     $relatorio = 1;
// }else{
//     $chamados = $c->listarChamados();
//     $relatorio = 0;
// }


$chamados = $c->paginarChamados($offset, $itensPorPagina);

?>

<main class="d-flex flex-column flex-fill h-100" id="historico_chamados">
    <form class="d-flex flex-column flex-fill p-3" 
        data-form-type="ajax" 
        data-form-target="#historico_chamados" 
        data-form-replace="true" 
        action="<?= $baseURL ?>/pages/chamados/chamados.page.php" 
        method="get"
    >

        <div class="d-flex flex-column mb-2">

            <h1 class="fs-6 mb-3">
                Chamados
            </h1>

        </div>

        <!-- passando width: 10px; foi a solução -->
        <div class="dashboard-table-overflow rounded overflow-auto position-relative d-flex flex-column flex-fill">

            <div class="table-responsive h-100 rounded border bg-dashboard p-2 pt-0">
                <table class="table dashboard-table table-hover table-sm caption-top">
                    <caption class="pb-0 pt-2 px-2">
                        <small class="text-start text-muted " style="font-size: 12px;">
                            Total / Realizadas: <span id="total-consultas"></span>
                        </small>
                    </caption>
                    <thead class="sticky-top table-header bg-dashboard">
                        <tr class="align-middle" style="height: 42px;">

                            <th class="input-group-sm" scope="col">
                                <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                    <small>N°</small>
                                </div>
                            </th>

                            


                            <th class="input-group-sm" scope="col">
                                <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                    <small>Operador</small>
                                </div>
                            </th>

                            <th class="input-group-sm" scope="col">
                                <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                    <small>Solicitante</small>
                                </div>
                            </th>

                            <th class="input-group-sm" scope="col">
                                <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                    <small>Código / Nome</small>
                                </div>
                            </th>

                            <th class="input-group-sm" scope="col">
                                <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                    <small>Placa</small>
                                </div>
                            </th>

                            <th class="input-group-sm" scope="col">
                                <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                    <small>Tipo/Motivo</small>
                                </div>
                            </th>

                            <th class="input-group-sm" scope="col">
                                <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                    <small>Provedor</small>
                                </div>
                            </th>

                            <th class="input-group-sm" scope="col">
                                <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                    <small>Status</small>
                                </div>
                            </th>

                            <th class="input-group-sm" scope="col">
                                <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                    <small>Aberto</small>
                                </div>
                            </th>

                            <th class="input-group-sm" scope="col">
                                <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                    <small>Atualizado</small>
                                </div>
                            </th>

                        </tr>
                    </thead>
                    <tbody>

                    <?php foreach ($chamados as $chamado) : 
                        
                        $atendente = $c->selecionarAtendente($chamado->cod_atendente);
                    
                        $atendente_imagem = $atendente->ImagemAtendente;
                        $atendente_nome = ucwords($atendente->NomeNovoAtendente ?: $atendente->NomeAtendente);
                    ?>

                        <!-- role="button" 
                        data-bs-open="modal" 
                        data-bs-useclass="table-active" 
                        data-bs-template="<?= $baseURL ?>/pages/opcoes_consulta.page.php?consulta=<?= base64_encode($row->Codigo) ?>&xml=0" data-bs-jsonb64="<?= $json_base64 ?>" -->
                        <tr class="table-row-fomidable to-hover" 
                        role="button"
                        >
                     
                            <!-- NUMERO CHAMADO START -->
                            <td scope="row">
                                <div class="d-flex align-items-center gap-1">

                                    <i class="bi bi-circle-fill text-primary unread" 
                                        data-info="<?= $chamado->cod ?>-<?= $chamado->atualizacao ?>" 
                                        aria-hidden="true"
                                        style="font-size: 8px;"
                                    ></i>
            
                                    <span class='badge badge-sm bg-light'>
                                        <?= str_pad($chamado->cod, 5, '0', STR_PAD_LEFT) ?>
                                    </span>

                                </div>
                            </td>
                            <!-- NUMERO CHAMADO END -->

                            <!-- OPERADOR START -->
                            <td scope="row">
                                <div class="d-flex align-items-center gap-2">

                                    <img class="bg-light rounded"
                                        alt="" width="28" height="28" 
                                        src="<?= $atendente_imagem ?>" 
                                        data-srcset="<?=$baseURL?>/profile_image.php?fullname=<?= $atendente_nome ?>" 
                                        onerror="defaultImage(this)"
                                        <?php if(@$atendente->acesso != "0") echo 'disabled' ?>
                                    />

                                    <small class="fw-semibold text-nowrap">
                                        <?= $atendente_nome ?>
                                    </small>

                                </div>
                            </td>
                            <!-- OPERADOR END -->

                            <!-- SOLICITANTE START -->
                            <td scope="row">
                                <span class="badge badge-sm bg-light">
                                    <?= $requerente[$chamado->requerente - 1]['nome'] ?>
                                </span>
                            </td>
                            <!-- SOLICITANTE END -->

                            <!-- NOME START -->
                            <td scope="row">
                                <small class="fw-semibold text-nowrap">
                                    <?php if ($chamado->requerente == "1") : ?>
                                        <span class="badge badge-sm bg-light">
                                            <?= $chamado->codigo ?>
                                        </span>
                                    <?php endif; ?>

                                    <?= ($chamado->nome ? str_replace('REPRESENTANTE ', '', strtoupper($chamado->nome)) : '...') ?>
                                </small>
                            </td>
                            <!-- NOME END -->

                            <!-- PLACA START -->
                            <td scope="row">
                                <span class="badge badge-sm bg-light">
                                    <?= ($chamado->placa ? $chamado->placa : '...') ?>
                                </span>
                            </td>
                            <!-- PLACA END -->

                            <!-- TIPO/MOTIVO START -->
                            <td scope="row">
                                <small class="text-nowrap text-center">
                                    <span class="fw-semibold text-muted">
                                        <?= $solicitacao[$chamado->solicitacao - 1]['nome'] ?>
                                    </span>
                                    <span class="badge badge-sm bg-light">
                                        <?=$motivo[$chamado->motivo - 1]['nome'] ?>
                                    </span>
                                </small>
                            </td>
                            <!-- TIPO/MOTIVO END -->

                            <!-- PROVEDOR START -->
                            <td scope="row">
                                <div class="d-flex gap-1">
                                    <span class="badge badge-sm bg-light">
                                        <?= $provedor[$chamado->provedor - 1]['nome'] ?>
                                    </span>

                                    <?php if ($chamado->provedor_2): ?>
                                        <span class="badge badge-sm bg-light">
                                            <?= $provedor[$chamado->provedor_2 - 1]['nome'] ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($chamado->provedor_3): ?>
                                        <span class="badge badge-sm bg-light">
                                            <?= $provedor[$chamado->provedor_3 - 1]['nome'] ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                            </td>
                            <!-- PROVEDOR END -->

                            
                            <!-- SITUAÇÃO START -->
                            <td scope="row">
                                <small class="fw-semibold text-nowrap text-center">

                                    <?php if ($chamado->status == "1"): ?>
                                        <span class="badge badge-sm text-warning-emphasis bg-warning-subtle border border-warning-subtle">
                                            AGUARDANDO
                                        </span>
                                    <?php elseif ($chamado->status == "2"): ?>
                                        <span class="badge badge-sm text-danger-emphasis bg-danger-subtle border border-danger-subtle">
                                            FORA DO PRAZO
                                        </span>
                                    <?php elseif ($chamado->status == "3"): ?>
                                        <span class="badge badge-sm text-success-emphasis bg-success-subtle border border-success-subtle">
                                            CONCLUÍDO
                                        </span>
                                    <?php else: ?>
                                        ...
                                    <?php endif; ?>

                                </small>
                            </td>
                            <!-- SITUAÇÃO END -->

                            <!-- CRIADO START -->
                            <td scope="row">

                                <span class="badge badge-sm bg-light">
                                    <?= date('d/m/Y - H:i:s', strtotime($chamado->abertura)) ?>
                                </span>

                            </td>
                            <!-- CRIADO END -->

                            <!-- ATUALIZADO START -->
                            <td scope="row">

                                <?php if($chamado->atualizacao): ?>
                                    <span class="badge badge-sm <?= $chamado->status == 2 ? 'bg-danger-subtle text-danger' : 'bg-light' ?>" >
                                        <?= date('d/m/Y - H:i:s', strtotime($chamado->atualizacao)) ?>
                                    </span>
                                <?php endif; ?>

                            </td>
                            <!-- ATUALIZADO END -->


                        </tr>

                        <?php endforeach; ?>

                    </tbody>
                </table>

            </div>
            <div class="d-flex align-items-center justify-content-end mt-3">
                
                <small class="me-2 flex-grow-1 flex-md-grow-0 text-start"><?= $paginaAtual ?>-<?= $totalPaginas ?> de <?= $totalItens ?></small>
                
                <span class="me-2"
                    data-bs-toggle="popover" 
                    data-bs-placement="bottom"
                    data-bs-trigger="hover focus" 
                    data-bs-custom-class="dark-popover"
                    data-bs-content="Próximas"
                >
                    <button type="submit" class="btn btn-light btn-sm"
                        formaction="<?=$baseURL?>/pages/chamados/chamados.page.php?pagina=<?= $paginaAtual-1 ?>"
                        <?php if ($paginaAtual <= 1) echo 'disabled' ?>
                    >
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </span>
                <span 
                    data-bs-toggle="popover" 
                    data-bs-placement="bottom"
                    data-bs-trigger="hover focus" 
                    data-bs-custom-class="dark-popover"
                    data-bs-content="Anteriores"
                >
                    <button type="submit" class="btn btn-light btn-sm"
                        formaction="<?=$baseURL?>/pages/chamados/chamados.page.php?pagina=<?= $paginaAtual+1 ?>"
                        <?php if ($paginaAtual >= $totalPaginas) echo 'disabled' ?>
                    >
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </span>
            </div>
        </div>
    </form>
</main>

