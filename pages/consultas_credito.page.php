<?php 
@session_start();

@include_once('../config.php');

$login = @$_GET['login'] ?: "";
$data = @$_GET['data'] ?: date('Y-m-d');
$parametro = @$_GET['parametro'] ?: "";
$tipoconsulta = @$_GET['tipoconsulta'];

$data = @$_GET['data'] ?: date('Y-m-d'); // apartir dessa data
$login = @$_GET['login']; // codigo cliente
$parametro = @$_GET['parametro']; // tipo parametro: 0: parametro da consulta, 1:codigo da consulta
$tipoconsulta = @$_GET['tipoconsulta']; // se é cred especial....
$totalconsultas = @$_GET['totalconsultas'];

function getFullUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];

    return $protocol . $host . $uri;
}


function navigateToOrderByLink($param) {
    $url = getFullUrl();
    
    $parsed_url = parse_url($url);
    
    // Adicionar ou modificar um parâmetro de consulta
    $params = array();

    if (@$parsed_url['query']) {
        parse_str($parsed_url['query'], $params);
    }

    // Reconstruir a URL com os parâmetros de consulta atualizados
    $parsed_url['query'] = http_build_query($params);
    
    return '?' . $parsed_url['query'];
}

?>


<form class="d-flex flex-column flex-fill p-3"
    data-form-type="ajax"
    data-form-target="#consultasCreditoRows"
    action="<?=$baseURL?>/pages/consultas_credito_rows.page.php"
    method="get"
    id="form_consultas_credito"
>

    <div class="d-flex flex-column mb-2">

        <h1 class="fs-6 mb-3">
            Consultas Crédito
        </h1>

        <div class="row gx-0 gy-2">
            
            <div class="col-12 col-md-12 col-lg-3 order-1">
                <div class="input-group input-group-sm" >
                    <span class="input-group-text rounded-0" id="basic-addon1">Data</span>
                    <input name="data" class="form-control rounded-0 form-control-sm" 
                        type="date" 
                        placeholder="Data" aria-label="Data"
                        value="<?= $data ?>"
                        max="<?= date('Y-m-d') ?>"
                    >
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 order-2">
                <div class="input-group rounded-0 input-group-sm" >
                    <span class="input-group-text rounded-0">Login</span>
                    <input name="login" class="form-control rounded-0"
                        type="text" 
                        placeholder="Código do Cliente" 
                        aria-label="Server"
                        value="<?= $login ?>"
                    >
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 order-3">
                <div class="input-group input-group-sm">
                    <span class="input-group-text rounded-0">Parâmetro</span>
                    <input class="form-control rounded-0"
                        name="parametro"
                        placeholder="Buscar..."
                        value="<?= $parametro ?>"
                    >
                </div>
            </div>
              

                    
            <div class="col-12 col-lg text-truncate order-4 order-md-3">
                <div class="input-group input-group-sm">
                    <button type="submit" class="btn btn-sm btn-primary text-truncate w-100 rounded-0 px-4">
                        Buscar Consultas
                    </button>
                </div>
            </div>
                    


           <div class="col-12 order-3 order-md-4">
               <div class="d-flex align-items-center justify-content-between">
    
                   <div class="d-flex w-100 flex-wrap">
                       <!-- <div class="me-2">
    
                           <div class="input-group input-group-sm mb-3">
                               <span class="input-group-text bg-white" id="basic-addon1">Data:</span>
                               <input type="date" class="form-control form-control-sm" 
                                   placeholder="Data" aria-label="Data"
                               >
                           </div>
    
                       </div> -->
               
                       <div class="d-flex flex-grow-1 flex-md-grow-0">

                            <div class="input-group input-group-sm flex-nowrap input-group-sm me-1 mb-2 ms-md-0 me-md-2 w-md-auto">
                                <span class="input-group-text" ><small>Tipo</small></span>
                                <select class="form-select w-md-auto" aria-label="Default select example"
                                    name="tipoconsulta"
                                    style="width: inherit; min-width: 112px;"
                                >
                                    <option selected value="">Selecionar</option>
                                    <?php
                                        $vestigio = @$vestigio;

                                        $db->query = 'SELECT * FROM ttipoconsulta WHERE id <> 0 AND status = 0 order by id';
                                        $r = $db->select();
                                        $rcount = count($r);

                                        if ($rcount != 0) {
                                        $corline = "FFF1E3";
                                        foreach ($r as $key => $table ) {
                                            $t = "";
                                            if ("[".$table->id."]" == "[$vestigio]") $t = "selected";
                                            print "<option value='".$table->id."' $t >".$table->tipoconsulta;
                                        }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="input-group input-group-sm flex-nowrap input-group-sm ms-1 mb-2 ms-md-0 me-md-2 w-md-auto">
                                <span class="input-group-text" ><small>Total Consultas</small></span>
                                <select class="form-select w-md-auto" aria-label="Default select example"
                                    name="totalconsultas"
                                    style="width: inherit; min-width: 112px;"
                                >
                                    <option selected value="">Selecionar</option>
                                    <option value="1" <?php if ($totalconsultas == "1") echo "selected"?>>POR CÓDIGO
                                </select>
                            </div>
                       </div>
                   </div>
    
    
               </div>
           </div>
            

        </div>
    </div>

    <!-- passando width: 10px; foi a solução -->
    <div class="dashboard-table-overflow rounded overflow-auto position-relative d-flex flex-column flex-fill">

        <div class="table-responsive bg-dashboard h-100 p-2 pt-0 border rounded">
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
                                <small>Cliente</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Parâmetro</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Consulta</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Provedor</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Data</small>
                            </div>
                        </th>

                    </tr>
                </thead>
                <tbody id="consultasCreditoRows"  >

                    <?php 
                    // require_once('./pages/consultas_credito_rows.page.php'); 
                    ?>

                </tbody>
                </div>
            </table>
        </div>
    </div>
</form>



<script>
    $(document).ready(function() {
        setTimeout(() => {
            var formEl = $('#form_consultas_credito');

            var bodyArray = formEl.serializeArray();

            var body =  bodyArray.reduce((acc, val) => {
                return { ...acc, [val.name]: val.value };
            }, {});

            console.log({ body });

            // verifica se além do campo data há algo selecionado
            if (Object.values(body).filter(val => val).length > 1) {
                formEl.submit();
            }

        }, 100);
    });
</script>

<!-- seta o total das consultas -->
<script>
    $('#total-consultas').text("<?=count($rows)?>");
</script>