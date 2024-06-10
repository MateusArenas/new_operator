<?php @session_start();

    @include_once('../../config.php');
    @include_once('../../classes/Database.class.php');
    @include_once('../../classes/Farol.class.php');
    
	$db = new Database();
	$farolClass = new Farol();

	$motivos = $farolClass->motivos();

	$farois = $farolClass->farois();
?>

<main id="realizar_chamado" class="d-flex flex-column flex-fill">
    <div class="row flex-grow-1 g-0 m-0" style="height: 100px;"> 
    
        <div class="col-12 col-lg-7 p-3 h-lg-100" >
    
            <form class="d-flex flex-column flex-grow-1 h-100"
                data-form-type="ajax"
                data-form-target="#realizar_chamado_message"
                action="<?=$baseURL?>/action-create-ticket.php"
                method="post"
            >
                <div class="card h-100">

                    <div class="card-header">
                        <h1 class="fs-6">
                            Abrir Chamado
                        </h1>

                        <small class="card-subtitle mb-2 text-muted">
                            <i class="bi bi-info-circle me-1"></i>Por favor, tenha cuidado ao realizar esta ação. Recomendamos que execute o procedimento de <code>Realizar Chamado</code> com cautela.
                        </small>
                    </div>

                    <div class="card-body overflow-auto">
                        <div class="row">
    
                            <div id="realizar_chamado_message" class="col-12"></div>
    
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <select class="form-select form-select-sm" 
                                        name="reason"
                                        required
                                    >
                                    <option value="">Selecione</option>
                                    <?php foreach($motivos as $value => $item): ?>
                                        <option value="<?=$value?>"><?="{$value} - {$item}"?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label class="form-label">Motivo</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control form-control-sm" 
                                        style="min-height: 160px;"
                                        name="description"
                                        placeholder="Descreva o motivo..."
                                        rows="4" cols="55"
                                        required
                                    ></textarea>
                                    <label class="form-label">Descrição</label>
                                </div>
                            </div>
                      
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="row m-0 g-0">
                            <div class="col-12 pt-2 pb-3 border-bottom">
                                <small class="mb-2" role="alert">
                                    <i class="bi bi-exclamation-circle me-1"></i>Após a abertura do chamado, ele estará disponível tanto no <strong>histórico</strong> quanto no <strong>canal do Slack</strong>.
                                </small>
                            </div>
                            <div class="col-12 pt-2">
                                <button type="submit" class="btn btn-primary btn-sm float-end ">
                                    Abrir Chamado
                                </button>
                            </div>
                        </div>
                    </div>
    
                </div>
            </form>
    
    
            <!-- <div class="form-group" >
                <div style="width: 410px">
                    <label center for="focusedInput"> <h3><b> Digite a Placa </b></h3> </label> 
                    <input maxlength="7" id="placa" class="form-control text-center text-upper" placeholder="*******" />
                    <label center for="focusedInput"> <h3><b> Justifique a remoção </b></h3> </label> 					
                    <select class="form-control" id="justificativa">
                        <option value="0">Selecione</option>
                        <?php foreach($motivos as $key => $value): ?>
                            <option value="<?php echo $key; ?>"><?php echo "{$key} - {$value}"; ?></option>
                        <?php endforeach; ?>
                    </select>	
                    <label center for="focusedInput"> <h3><b>Descrição</b></h3> </label>
                    <textarea id="descricao" class="form-control" name="descricao" rows="4" cols="55" style="border: 1px solid #cccccc"></textarea>
                    <br><br>
                    <input id="remover" type="button" class="btn btn-default remover" value="Remover" />	
                </div>					
            </div> -->
        </div>
        <div class="col-12 col-lg-5 p-3 ps-lg-0 h-lg-100" >
            
            <?php require_once('./pages/alterar_farol/historico.php') ?>
                            
        </div>
    </div>
</main>
