<?php @session_start(); ?>

<div class="row h-100 p-3">

        
    <div class="col-12 h-100">
        <div class="d-flex flex-column h-100 rounded">
            
            <div class="card h-100 overflow-hidden ">
                <div class="card-header">
                    <h1 class="fs-5">
                        2ª Via
                    </h1>
                </div>
                <div class="card-body p-0">
                    <iframe class="w-100 h-100 border-0"
                        src="https://painel-segvia.credoperador.com.br/?token=d976fa59-47e8-470e-a841-d58c511c7376&aux=<?=urlencode(base64_encode($_SESSION["MSId"]));?>" 
                    ></iframe>
                </div>
            </div>

        </div>
    </div>
</div>