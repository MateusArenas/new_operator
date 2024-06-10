<?php @session_start();

    // essa maneira de incluir, inclui de qualquer forma.
    @include_once('../../config.php');
    @include_once('../../classes/Helpers.class.php');
    @include_once('../../classes/Database.class.php');
    @include_once('../../classes/Leilao.class.php');

	$db = new Database();
    
	$leilaoClass = new Leilao();

    // Recebendo o offset via GET
    $pagina = @$_GET['pagina'] ?? 1;

    // Total de itens
    $totalItens = $leilaoClass->totalLeiloesRemovidos();
    
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

    $rows = $leilaoClass->listarLeiloesRemovidos($offset, $itensPorPagina);

	$motivos = $leilaoClass->motivos();
?>

<main class="d-flex flex-column flex-fill h-100"
    id="historico_leilao" 
>
    <form class="card d-flex flex-column h-100"
        data-form-type="ajax"
        data-form-target="#historico_leilao"
        data-form-replace="true"
        action="<?=$baseURL?>/pages/remover_leilao/historico.php"
        method="get"
    >
    
        <div class="card-header d-flex flex-row align-items-center justify-content-between p-3 border-bottom">
    
            <h6 class="card-subtitle mb-0 text-muted" style="max-width: 18em;">
                <i class="bi bi-clock-history me-1"></i> Histórico de Alterações
            </h6>

            <span 
                data-bs-toggle="popover" 
                data-bs-placement="bottom"
                data-bs-trigger="hover focus" 
                data-bs-custom-class="dark-popover"
                data-bs-content="Recarregar"
            >
                <button type="submit" class="btn btn-light btn-sm"
                    formaction="<?=$baseURL?>/pages/remover_leilao/historico.php?pagina=<?= $paginaAtual ?>"
                >
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </span>
    
        </div>
    
        <div class="card-body p-0 list-group list-group-flush flex-grow-1 overflow-auto" >
            <?php if (!count($rows)): ?>
                <div class="d-flex flex-column align-items-center justify-content-center flex-fill p-5">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="text-center text-muted" style="max-width: 18em;">
                        Ainda não há nenhum leilão que foi removido
                    </p>
                </div>
            <?php endif; ?>

            <?php
                foreach ($rows as $key => $row): 
                    $data = Helpers::formatarDataEscrita($row->date_remocao);
            ?>
                <div class="list-group-item ">
    
                    <div class="d-flex flex-row w-100">
    
                        <img 
                            alt="" width="32" height="32" 
                            class="rounded me-3 mt-1"
                            data-srcset="<?= $row->ImagemAtendente ?>" 
                            src="<?=$baseURL?>/profile_image.php?user=<?= $row->slack_id?>" 
                            onerror="defaultImage(this)"
                        />
    
                        <div class="d-flex flex-column flex-grow-1">
                            <div class="d-flex w-100 justify-content-between">
    
                                <div class="d-flex flex-column pe-1 flex-grow-1 gap-1">
                                    <span class="fw-semibold">
                                        <?= ucwords($row->nome) ?> removeu leilão da
                                        placa <strong><?=strtoupper($row->placa)?></strong>
                                    </span>

                                    <div class="d-flex flex-column">
                                        <small class="fw-semibold text-muted">
                                            • <?= $motivos[$row->justificar]?>
                                        </small> 
                                        <span class="">
                                            <?= utf8_encode(@$row->descricao); ?>
                                        </span>
                                    </div>
                                </div>
    
                                <div class="d-flex flex-column align-items-end text-end"
                                    style="min-width: 3.6em;"
                                >
                                    <small class="text-muted"><?= $data; ?></small>
                                </div>
                            </div>
                   
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
        </div>
        <div class="card-footer">

            <div class="d-flex align-items-center justify-content-end">
                    
                <small class="me-2 flex-grow-1 flex-md-grow-0 text-start"><?= $paginaAtual ?>-<?= $totalPaginas ?> de <?= $totalItens ?></small>
                <span class="me-2"
                    data-bs-toggle="popover" 
                    data-bs-placement="bottom"
                    data-bs-trigger="hover focus" 
                    data-bs-custom-class="dark-popover"
                    data-bs-content="Próximas"
                >
                    <button type="submit" class="btn btn-light btn-sm"
                        formaction="<?=$baseURL?>/pages/remover_leilao/historico.php?pagina=<?= $paginaAtual-1 ?>"
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
                        formaction="<?=$baseURL?>/pages/remover_leilao/historico.php?pagina=<?= $paginaAtual+1 ?>"
                        <?php if ($paginaAtual >= $totalPaginas) echo 'disabled' ?>
                    >
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </span>
            </div>

        </div>
    </form>
</main>
    
    