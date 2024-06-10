<?php @session_start();

    @include_once('../config.php');
    @include_once('../../classes/Database.class.php');    
    @include_once('../../classes/HistoricoRF.class.php');

	$db = new Database();
    $historicoRFClass = new HistoricoRF();
    
	$motivos = $historicoRFClass->motivos();
?>

<main id="atualizar_rf" class="d-flex flex-column flex-fill">
    <div class="row flex-grow-1 g-0 m-0" style="height: 100px;"> 
    
        <div class="col-12 col-lg-7 p-3 h-lg-100" >
    
            <form class="d-flex flex-column flex-grow-1 h-100"
                data-form-type="ajax"
                data-form-target="#atualizar_rf_message"
                action="<?=$baseURL?>/actions/acao_remover_leilao.action.php"
                method="post"
            >
                <div class="card h-100">
                    <div class="card-header">
                        <h1 class="fs-6">
                            Atualizar Histórico RF
                        </h1>

                        <small class="card-subtitle mb-2 text-muted">
                            <i class="bi bi-info-circle me-1"></i>Por favor, tenha cuidado ao realizar esta ação. Recomendamos que execute o procedimento de <code>Atualizar Histórico RF</code> com cautela.
                        </small>
                    </div>
                    <div class="card-body overflow-auto">
                        <div class="row">
    
                            <div id="atualizar_rf_message" class="col-12"></div>
    
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input class="form-control form-control-sm text-uppercase" 
                                        name="placa"
                                        type="text"
                                        maxlength="20"
                                        placeholder="**********"
                                        required
                                    >
                                    <label class="form-label">Código da Consulta</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <select class="form-select form-select-sm" 
                                        name="justificativa"
                                        required
                                    >
                                    <option value="0">Selecione</option>
                                    <?php foreach($motivos as $key => $value): ?>
                                        <option value="<?php echo $key; ?>"><?php echo "{$key} - {$value}"; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label class="form-label">Justifique a alteração</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-sm float-end ">
                            Realizar Alteração
                        </button>
                    </div>
    
                </div>
            </form>
        </div>
        <div class="col-12 col-lg-5 p-3 ps-lg-0 h-lg-100" >
            
            <?php require_once('./pages/atualizar_rf/historico.php') ?>
                            
        </div>
    </div>
</main>
