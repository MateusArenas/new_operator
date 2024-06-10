<?php @session_start();

    @include_once('../../config.php');
    @include_once('../../classes/Database.class.php');
    @include_once('../../classes/Farol.class.php');
    
	$db = new Database();
	$farolClass = new Farol();

	$motivos = $farolClass->motivos();

	$farois = $farolClass->farois();
?>

<main id="remover_leilao" class="d-flex flex-column flex-fill">
    <div class="row flex-grow-1 g-0 m-0" style="height: 100px;"> 
    
        <div class="col-12 col-lg-7 p-3 h-lg-100" >
    
            <form class="d-flex flex-column flex-grow-1 h-100"
                data-form-type="ajax"
                data-form-target="#remover_leilao_message"
                action="<?=$baseURL?>/actions/acao_remover_leilao.action.php"
                method="post"
            >
                <div class="card h-100">

                    <div class="card-header">
                        <h1 class="fs-6">
                            Cliente sem contato via telefone
                        </h1>

                        <small class="card-subtitle mb-2 text-muted">
                            <i class="bi bi-info-circle me-1"></i>É importante destacar que este sistema de envio de mensagem no Whatsapp do cliente deve ser utilizado somente quando não for possível entrar em contato com o cliente por telefone..
                        </small>
                    </div>

                    <div class="card-body overflow-auto">
                        <div class="row">
    
                            <div id="remover_leilao_message" class="col-12"></div>
    
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input class="form-control form-control-sm text-uppercase" 
                                        name="placa"
                                        maxlength="20" 
                                        type="text"
                                        placeholder="**********"
                                        required
                                    >
                                    <label class="form-label">Código Cliente</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input class="form-control form-control-sm text-uppercase" 
                                        name="placa"
                                        maxlength="20" 
                                        type="text"
                                        placeholder="**********"
                                        required
                                    >
                                    <label class="form-label">Telefone</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <p class="text-muted mb-2">
                                    Exemplo de mensagem que vai para o cliente:
                                </p>

                                <div class="form-group text-center">
                                    <img class="w-100 h-auto rounded"
                                        src="<?= $baseURL ?>/assets/images/layout_msg.png" 
                                        alt="Mensagem Whatsapp"
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <div class="row m-0 g-0">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-sm float-end ">
                                    Enviar Mensagem
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
    </div>
</main>
