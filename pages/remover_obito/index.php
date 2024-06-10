<?php @session_start();

    @include_once('../../config.php');
    @include_once('../../classes/Database.class.php');
    @include_once('../../classes/Obito.class.php');
    
	$db = new Database();
    $obitoClass = new Obito();

	$motivos = $obitoClass->motivos();
?>

<main id="remover_obito" class="d-flex flex-column flex-fill">
    <div class="row flex-grow-1 g-0 m-0" style="height: 100px;"> 
    
        <div class="col-12 col-lg-7 p-3 h-lg-100" >
    
            <form class="d-flex flex-column flex-grow-1 h-100"
                data-form-type="ajax"
                data-form-cleanup="true"
                data-form-target="#remover_obito_message"
                action="<?=$baseURL?>/actions/acao_remover_obito.action.php"
                method="post"
            >
                <div class="card h-100">
                    <div class="card-header">
                        <h1 class="fs-6">
                            Remover Óbito
                        </h1>

                        <small class="card-subtitle mb-2 text-muted">
                            <i class="bi bi-info-circle me-1"></i>Por favor, tenha cuidado ao realizar esta ação. Recomendamos que execute o procedimento de <code>Remoção de Óbito</code> com cautela.
                        </small>
                    </div>
                    <div class="card-body overflow-auto">
                        <div class="row">
    
                            <div id="remover_obito_message" class="col-12"></div>
    
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input class="form-control form-control-sm text-uppercase" 
                                        name="consulta"
                                        maxlength="20" type="text"
                                        placeholder="*******"
                                        data-input-mask="codigo"
                                        required
                                    >
                                    <label class="form-label">Código da consulta</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <select class="form-select form-select-sm" 
                                        name="justificativa"
                                        required
                                    >
                                    <option value="">Selecione</option>
                                    <?php foreach($motivos as $key => $value): ?>
                                        <option value="<?php echo $key; ?>"><?php echo "{$key} - {$value}"; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label class="form-label">Justifique a remoção</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <button type="submit" class="btn btn-danger btn-sm float-end ">
                            Realizar Remoção
                        </button>
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
            
            <?php require_once('./pages/remover_obito/historico.php') ?>
                            
        </div>
    </div>
</main>
