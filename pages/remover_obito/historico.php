<?php @session_start();

    // essa maneira de incluir, inclui de qualquer forma.
    @include_once('../../config.php');
    @include_once('../../classes/Helpers.class.php');
    @include_once('../../classes/Database.class.php');
    @include_once('../../classes/Obito.class.php');

	$db = new Database();
	$obitoClass = new Obito();

    // Recebendo o offset via GET
    $pagina = @$_GET['pagina'] ?? 1;
    
    // Total de itens
    $totalItens = $obitoClass->totalObitosRemovidos();
    
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

	$rows = $obitoClass->listarObitosRemovidos($offset, $itensPorPagina);

	$motivos = $obitoClass->motivos();
?>

<main class="d-flex flex-column flex-fill h-100"
    id="historico_obito" 
>
    <form class="card d-flex flex-column h-100"
        data-form-type="ajax"
        data-form-target="#historico_obito"
        data-form-replace="true"
        action="<?=$baseURL?>/pages/remover_obito/historico.php"
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
                    formaction="<?=$baseURL?>/pages/remover_obito/historico.php?pagina=<?= $paginaAtual ?>"
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
                        Ainda não há nenhum óbito que foi removido
                    </p>
                </div>
            <?php endif; ?>

            <?php foreach ($rows as $key => $row): 
                $data = Helpers::formatarDataEscrita($row->date_remocao);
            ?>
                <div class="list-group-item ">
    
                    <div class="d-flex flex-row w-100">
    
                        <img src="<?=$baseURL?>/profile_image.php?fullname=<?= ucwords($row->nome) ?>" 
                            alt="" width="32" height="32" 
                            class="rounded me-3 mt-1"
                        />
    
                        <div class="d-flex flex-column flex-grow-1">
                            <div class="d-flex w-100 justify-content-between mb-1">
                            
                                <div class="d-flex flex-column pe-1 flex-grow-1 ">
                                    <p class="mb-1">
                                        <?= ucwords($row->nome) ?> removeu o óbito da
                                        <strong><?=strtoupper($row->tipo_parametro)?> <?=strtoupper($row->parametro)?></strong>
                                    </p>
                                </div>

                                <div class="d-flex flex-column align-items-end text-end"
                                    style="min-width: 3.6em;"
                                >
                                    <small class="text-muted"><?= $data; ?></small>
                                </div>
                            </div>

                            <small class="mb-1">
                                <a href="#" type="button" class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover" 
                                    data-bs-open="modal" 
                                    data-bs-useclass="table-active"
                                    data-bs-template="<?=$baseURL?>/pages/opcoes_consulta.page.php?consulta=<?= base64_encode($row->cod_consulta) ?>&xml=0"
                                    data-bs-jsonb64="<?= $json_base64 ?>"
                                >
                                    <i class="bi bi-file-earmark-fill"></i> <?=$row->tipo?>: <?=$row->cod_consulta?>
                                </a>                            
                            </small>
    
                            <!-- <small class="mb-1">
                                <i class="bi bi-person-fill"></i> <?=$row->cliente_nome?> (<?=$row->cliente_codigo?>)
                            </small> -->

                            <div class="d-flex flex-column bg-light rounded px-2 py-2">
                                <small class="mb-1">
                                    <i class="bi bi-pin-fill"></i> <?= $motivos[$row->justificar]?>
                                </small> 
                                <p class="text-muted mb-0">
                                    <?= utf8_encode(@$row->descricao); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
        </div>
        <div class="card-footer">

            <div class="d-flex align-items-center justify-content-end">
                    
                <small class="me-2 flex-grow-1 flex-md-grow-0 text-start"><?= $paginaAtual+1 ?>-<?= $totalPaginas ?> de <?= $totalItens ?></small>
                <span class="me-2"
                    data-bs-toggle="popover" 
                    data-bs-placement="bottom"
                    data-bs-trigger="hover focus" 
                    data-bs-custom-class="dark-popover"
                    data-bs-content="Próximas"
                >
                    <button type="submit" class="btn btn-light btn-sm"
                        formaction="<?=$baseURL?>/pages/remover_obito/historico.php?pagina=<?= $paginaAtual-1 ?>"
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
                        formaction="<?=$baseURL?>/pages/remover_obito/historico.php?pagina=<?= $paginaAtual+1 ?>"
                        <?php if ($paginaAtual >= $totalPaginas) echo 'disabled' ?>
                    >
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </span>
            </div>

        </div>
    </form>
</main>
    
    
