<?php @session_start();

    @include_once('../../config.php');
    @include_once('../../classes/Database.class.php');
    @include_once('../../classes/Consulta.class.php');
    
	$db = new Database();
	$consultaClass = new Consulta();

	$motivos = $consultaClass->motivos();
?>

<main id="remover_consulta" class="d-flex flex-column flex-fill">
    <div class="row flex-grow-1 g-0 m-0" style="height: 100px;"> 
    
        <div class="col-12 col-lg-7 p-3 h-lg-100" >
    
            <form class="d-flex flex-column flex-grow-1 h-100"
                data-form-type="ajax"
                data-form-target="#remover_consulta_message"
                data-form-cleanup="true"
                action="<?=$baseURL?>/pages/remover_consulta/acao.php"
                method="post"
            >
                <div class="card h-100">
                    <div class="card-header">

                        <h1 class="fs-6">
                            Remover Consulta
                        </h1>

                        <small class="card-subtitle mb-2 text-muted">
                            <i class="bi bi-info-circle me-1"></i>Por favor, tenha cuidado ao realizar esta ação. Recomendamos que execute o procedimento de <code>Remoção de Consulta</code> com cautela.
                        </small>
                    </div>
                    <div class="card-body overflow-auto">
                        <div class="row">
    
                            <div id="remover_consulta_message" class="col-12"></div>
    
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input class="form-control form-control-sm" 
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
                                    <option value="0">Selecione</option>
                                    <?php foreach($motivos as $key => $value): ?>
                                        <option value="<?php echo $key; ?>"><?php echo "{$key} - {$value}"; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label class="form-label">Justifique a remoção</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control form-control-sm" 
                                        style="min-height: 160px;"
                                        name="descricao"
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
            
            <?php require_once('./pages/remover_consulta/historico.php') ?>
                            
        </div>
    </div>
</main>


<!-- https://igorescobar.github.io/jQuery-Mask-Plugin/docs.html -->
<script>
    $(document).ready(function(){
      $('[data-input-mask="codigo"]').mask('0'.repeat(100), {
            translation: {
                '0': {pattern: /[0-9]/},
            }
        });
    });
</script>
