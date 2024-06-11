<?php @session_start();

@include_once('../../config.php');

@include_once('../../classes/Database.class.php');
@include_once('../../classes/Tickets.class.php');
@include_once('../../classes/Users.class.php');
@include_once('../../classes/Functions.class.php');

$ticketsRepository = new Tickets();
$usersRepository = new Users();
$f = new Functions();

$motivos = $ticketsRepository->motivos();
$tipos = $usersRepository->tipos();

$count = $ticketsRepository->countAll();

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


$chamados = $ticketsRepository->findAll($offset, $itensPorPagina);

?>

<main class="d-flex flex-column flex-fill h-100" id="historico_chamados">
    <form class="d-flex flex-column flex-fill p-3" 
        data-form-type="ajax" 
        data-form-target="#historico_chamados" 
        data-form-replace="true" 
        action="<?= $baseURL ?>/pages/chamados/index.php" 
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
                                    <small>Tipo/Motivo</small>
                                </div>
                            </th>

                            <th class="input-group-sm" scope="col">
                                <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                    <small>Descrição</small>
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

                    <?php foreach ($chamados as $chamado) : ?>

                        <tr class="table-row-fomidable to-hover" 
                            role="button"
                            data-bs-open="modal" 
                            data-bs-useclass="table-active" 
                            data-bs-template="<?=$baseURL?>/pages/atualizar_status_tickect.php?ticket_id=<?= $chamado->id ?>"
                            data-bs-modaltype="modal-fullscreen-md-down"
                        >
                     
                            <!-- NUMERO CHAMADO START -->
                            <td scope="row">
                                <div class="d-flex align-items-center gap-1">

                                    <i class="bi bi-circle-fill text-primary unread" 
                                        aria-hidden="true"
                                        style="font-size: 8px;"
                                    ></i>
            
                                    <span class='badge badge-sm bg-light'>
                                        <?= str_pad($chamado->id, 5, '0', STR_PAD_LEFT) ?>
                                    </span>

                                </div>
                            </td>
                            <!-- NUMERO CHAMADO END -->

                            <!-- OPERADOR START -->
                            <td scope="row">
                                <div class="d-flex align-items-center gap-2">
                                  <?php if (@$chamado->operator_id) : ?>
                                    <img class="bg-light rounded"
                                        alt="" width="28" height="28" 
                                        src="<?= $chamado->operator_image_url ?>" 
                                        data-srcset="<?=$baseURL?>/profile_image.php?fullname=<?= $chamado->operator_name ?>" 
                                        onerror="defaultImage(this)"
                                    />

                                    <small class="fw-semibold text-nowrap">
                                        <?= $chamado->operator_name ?>
                                    </small>
                                  <?php else: ?>
                                  <?php endif; ?>
                                </div>
                            </td>
                            <!-- OPERADOR END -->

                            <!-- SOLICITANTE START -->
                            <td scope="row">
                                <span class="badge badge-sm bg-light">
                                    <?= $tipos[$chamado->user_type] ?>
                                </span>
                            </td>
                            <!-- SOLICITANTE END -->

                            <!-- NOME START -->
                            <td scope="row">
                                <small class="fw-semibold text-nowrap">
                                    <span class="badge badge-sm bg-light">
                                        <?= $chamado->user_id ?>
                                    </span>

                                    <?= $chamado->user_name ?>
                                </small>
                            </td>
                            <!-- NOME END -->

                            <!-- TIPO/MOTIVO START -->
                            <td scope="row">
                                <small class="text-nowrap text-center">
                                    <span class="badge badge-sm bg-light">
                                        <?=$motivos[$chamado->reason] ?>
                                    </span>
                                </small>
                            </td>
                            <!-- TIPO/MOTIVO END -->

                            <!-- DESCRICAO START -->
                            <td scope="row">
                                <span class="badge badge-sm bg-light">
                                    <?= $chamado->description ?>
                                </span>
                            </td>
                            <!-- DESCRICAO END -->

                            
                            <!-- SITUAÇÃO START -->
                            <td scope="row">
                                <small class="fw-semibold text-nowrap text-center">

                                    <?php if ($chamado->status == "0"): ?>
                                        <span class="badge badge-sm text-warning-emphasis bg-warning-subtle border border-warning-subtle">
                                            AGUARDANDO
                                        </span>
                                    <?php elseif ($chamado->status == "1"): ?>
                                        <span class="badge badge-sm text-primary-emphasis bg-primary-subtle border border-primary-subtle">
                                            EM ANDAMENTO
                                        </span>
                                    <?php elseif ($chamado->status == "3"): ?>
                                        <span class="badge badge-sm text-danger-emphasis bg-danger-subtle border border-danger-subtle">
                                            FORA DO PRAZO
                                        </span>
                                    <?php elseif ($chamado->status == "2"): ?>
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
                                    <?= date('d/m/Y - H:i:s', strtotime($chamado->created_at)) ?>
                                </span>

                            </td>
                            <!-- CRIADO END -->

                            <!-- ATUALIZADO START -->
                            <td scope="row">

                                <?php if($chamado->updated_at): ?>
                                    <span class="badge badge-sm <?= $chamado->status == 3 ? 'bg-danger-subtle text-danger' : 'bg-light' ?>" >
                                        <?= date('d/m/Y - H:i:s', strtotime($chamado->updated_at)) ?>
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
                        formaction="<?=$baseURL?>/pages/chamados/index.php?pagina=<?= $paginaAtual-1 ?>"
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
                        formaction="<?=$baseURL?>/pages/chamados/index.php?pagina=<?= $paginaAtual+1 ?>"
                        <?php if ($paginaAtual >= $totalPaginas) echo 'disabled' ?>
                    >
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </span>
            </div>
        </div>
    </form>
</main>

