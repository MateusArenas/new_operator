<?php @session_start();

    // essa maneira de incluir, inclui de qualquer forma.
    @include_once('../../config.php');
    @include_once('../../classes/Helpers.class.php');
    @include_once('../../classes/Database.class.php');
    @include_once('../../classes/Farol.class.php');

	$db = new Database();
	$farolClass = new Farol();
    
    $db->query = "SELECT COUNT(*) as total 
        FROM operador.tbl_altera_farol as f, credauto.consultas AS c
        WHERE f.justificar <> '' AND c.Codigo = f.consulta
    ";

    $count = $db->selectOne();

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

    $db->query = "SELECT f.id, f.consulta as cod_consulta, f.farol, f.date_remocao, f.date_remocao AS data, c.ValorItem AS parametro, t.tipoconsulta AS tipo, t.id AS tipo_consulta, c.ItemConsultado as tipo_parametro , f.justificar, 
        a.NomeAtendente AS nome, a.slack_id, a.CodAtendente AS atendente, a.acesso AS atendente_acesso, a.ImagemAtendente AS ImagemAtendente
        FROM operador.tbl_altera_farol AS f, consultas AS c, atendentes AS a, ttipoconsulta AS t 
        WHERE a.CodAtendente = f.usuario AND c.Codigo = f.consulta AND t.codigo = c.TipoConsulta 
        ORDER BY id DESC LIMIT ?, ?
    ";

    $db->content = [];
    $db->content[] = [$offset, 'int'];
    $db->content[] = [$itensPorPagina, 'int'];
	$rows = $db->select();

	$motivos = $farolClass->motivos();

	$farois = $farolClass->farois();
?>

<main class="d-flex flex-column flex-fill h-100"
    id="historico_leilao" 
>
    <form class="card d-flex flex-column h-100"
        data-form-type="ajax"
        data-form-target="#historico_leilao"
        data-form-replace="true"
        action="<?=$baseURL?>/pages/alterar_farol/historico.php"
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
                    formaction="<?=$baseURL?>/pages/alterar_farol/historico.php?pagina=<?= $paginaAtual ?>"
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
                        Ainda não há nenhum farol que foi atualizado
                    </p>
                </div>
            <?php endif; ?>

            <?php
                foreach ($rows as $key => $row): 
                    $data = Helpers::formatarDataEscrita($row->date_remocao);
            ?>
                <div class="list-group-item ">
    
                    <div class="d-flex flex-row w-100">
    
                        <a class="mt-1 me-3"
                            data-bs-open="modal"
                            href="?user=<?= $row->atendente ?>"
                            data-bs-modaltype="modal-fullscreen-md-down"
                        >
                            <img class="bg-light rounded"
                                alt="" width="32" height="32" 
                                src="<?= @$row->ImagemAtendente ?>" 
                                data-srcset="<?=$baseURL?>/profile_image.php?fullname=<?= ucwords($row->nome) ?>" 
                                onerror="defaultImage(this)"
                                <?php if(@$row->atendente_acesso != "0") echo 'disabled' ?>
                            />
                        </a> 
    
                        <div class="d-flex flex-column flex-grow-1">
                            <div class="d-flex w-100 justify-content-between mb-1">
    
                                <div class="d-flex flex-column pe-1 flex-grow-1 gap-1">
                                    <span class="fw-semibold">
                                        <a class="fw-semibold link-underline-secondary link-underline-opacity-0 link-underline-opacity-100-hover"
                                            data-bs-open="modal"
                                            href="?user=<?= $row->atendente ?>"
                                            data-bs-modaltype="modal-fullscreen-md-down"
                                            style="color: unset;"
                                        >
                                            <?= ucwords($row->nome) ?>
                                        </a> atualizou o farol para <strong><?=$farois[$row->farol]?></strong>
                                        <strong><?=strtoupper($row->tipo_parametro)?> <?=strtoupper($row->parametro)?></strong>
                                    </span>

                                    <small>
                                        <a type="button" class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover" 
                                            data-bs-open="modal" 
                                            data-bs-useclass="table-active"
                                            href="?consulta=<?= base64_encode($row->cod_consulta) ?>&xml=0"
                                        >
                                            <i class="bi bi-file-earmark-fill"></i> <?=$row->tipo?>: <?=$row->cod_consulta?>
                                        </a>                            
                                    </small>

                                    <div class="d-flex flex-column">
                                        <small class="fw-semibold text-muted">
                                            • <?= $motivos[$row->justificar]?>
                                        </small> 
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
                        formaction="<?=$baseURL?>/pages/alterar_farol/historico.php?pagina=<?= $paginaAtual-1 ?>"
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
                        formaction="<?=$baseURL?>/pages/alterar_farol/historico.php?pagina=<?= $paginaAtual+1 ?>"
                        <?php if ($paginaAtual >= $totalPaginas) echo 'disabled' ?>
                    >
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </span>
            </div>

        </div>
    </form>
</main>
    
    
