<?php @session_start();

@include_once('../config.php');

$login = trim(@$_REQUEST['login'] ?: "");
$data = trim(@$_REQUEST['data'] ?: date('Y-m-d'));
$tipoconsulta = trim(@$_REQUEST['tipoconsulta']);
$parametro = trim(@$_REQUEST['parametro'] ?: "");
$provedor = trim(@$_REQUEST['provedor'] ?: "");
$leilao = trim(@$_REQUEST['leilao'] ?: "");
$estado = trim(@$_REQUEST['estado'] ?: "");

try {
    if (isset($data) && is_string($data)) {
        // Converte a string da data para um timestamp
        $timestamp = strtotime($data);
        if ($timestamp !== false) {
            // Formata a data no formato desejado
            $data_formatada = date('Y-m-d', $timestamp);
        } else {
            // Handle invalid date input
            $data_formatada = '';
        }
    } else {
        // Handle missing or invalid data
        $data_formatada = '';
    }
} catch (\Throwable $th) {
    $data_formatada = '';
}

$data = $data_formatada;

?>

<form class="d-flex flex-column flex-fill p-3"
    data-form-type="ajax"
    data-form-target="#consultasWebserverRows"
    action="<?=$baseURL?>/pages/consultas_webserver_rows.page.php"
    method="get"
    id="form_consultas_webserver"
>

    <div class="d-flex flex-column mb-2">

        <h1 class="fs-6 mb-3">
            Consultas WebServer
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

                                        $db->query = 'SELECT * FROM painel_controle WHERE ID <> 0 AND status = 0 order by NomeConsulta';
                                        $options = $db->select();
                                        if (count($options) != 0) {
                                            $corline = "FFF1E3";
                                            foreach ($options as $key => $table )
                                            {
                                                $t = "";
                                                if ("[".$table->ID."]" == "[$vestigio]") $t = "selected";
                                                echo "<option value='".$table->ID."' $t >".$table->NomeConsulta;
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="input-group input-group-sm flex-nowrap input-group-sm ms-1 mb-2 ms-md-0 me-md-2 w-md-auto">
                                <span class="input-group-text" ><small>Provedor</small></span>
                                <select class="form-select w-md-auto" aria-label="Default select example"
                                    name="atividade"
                                    style="width: inherit; min-width: 112px;"
                                >
                                    <option value="">Selecionar
                                    <OPTION value="allcheck" <?php if ($provedor == "allcheck") echo "selected"?>>ALCHECK
                                    <OPTION value="autorisco" <?php if ($provedor == "autorisco") echo "selected"?>>AUTO RISCO
                                    <OPTION value="avisovenda" <?php if ($provedor == "avisovenda") echo "selected"?>>AVISO DE VENDA
                                    <OPTION value="credleilao" <?php if ($provedor == "credleilao") echo "selected"?>>BASE LEILÃO
                                    <OPTION value="auto" <?php if ($provedor == "auto") echo "selected"?>>C.AUTO
                                    <OPTION value="checklog" <?php if ($provedor == "checklog") echo "selected"?>>CHECKLOG 
                                    <OPTION value="credauto" <?php if ($provedor == "credauto") echo "selected"?>>CREDAUTO 
                                    <OPTION value="checkpro" <?php if ($provedor == "checkpro") echo "selected"?>>CHECKPRO
                                    <OPTION value="cobremais" <?php if ($provedor == "cobremais") echo "selected"?>>COBRE MAIS
                                    <OPTION value="credloc" <?php if ($provedor == "credloc") echo "selected"?>>CRED LOCALIZA 
                                    <OPTION value="correct" <?php if ($provedor == "correct") echo "selected"?>>CORRECT DATA	
                                    <OPTION value="future" <?php if ($provedor == "future") echo "selected"?>>FUTURE DATA	    
                                    <OPTION value="infocar" <?php if ($provedor == "infocar") echo "selected"?>>INFOCAR
                                    <OPTION value="qualy" <?php if ($provedor == "qualy") echo "selected"?>>INF QUALY
                                    <OPTION value="procob" <?php if ($provedor == "procob") echo "selected"?>>PROCOB
                                    <OPTION value="jwm" <?php if ($provedor == "jwm") echo "selected"?>>JWM
                                    <OPTION value="corretagem" <?php if ($provedor == "corretagem") echo "selected"?>>MULTI-CALCULO	
                                    <OPTION value="motor" <?php if ($provedor == "motor") echo "selected"?>>MOTOR CONSULTA
                                    <OPTION value="mtix" <?php if ($provedor == "mtix") echo "selected"?>>MTIX
                                    <OPTION value="SPC" <?php if ($provedor == "SPC") echo "selected"?>>SPC
                                    <OPTION value="serasa" <?php if ($provedor == "serasa") echo "selected"?>>SERASA
                                    <OPTION value="SERP" <?php if ($provedor == "SERP") echo "selected"?>>SERP
                                    <OPTION value="siwork" <?php if ($provedor == "siwork") echo "selected"?>>SIWORK	
                                    <OPTION value="TDI" <?php if ($provedor == "TDI") echo "selected"?>>TDI	
                                    <OPTION value="union" <?php if ($provedor == "union") echo "selected"?>>UNION SOLUTIONS
                                    <OPTION value="vec" <?php if ($provedor == "vec") echo "selected"?>>VC SISTEMAS
                                    <OPTION value="datasmart" <?php if ($provedor == "datasmart") echo "selected"?>>DATA SMART
                                    <OPTION value="xpertia" <?php if ($provedor == "xpertia") echo "selected"?>>XPERTIA	
                                    <OPTION value="ksi" <?php if ($provedor == "ksi") echo "selected"?>>KSI	
                                </select>
                            </div>
                       </div>
                       <div class="d-flex flex-grow-1 flex-md-grow-0">

                            <div class="input-group flex-nowrap input-group-sm me-1 mb-2 ms-md-0 me-md-2 w-md-auto">
                                <span class="input-group-text" ><small>Leilão</small></span>
                                <select class="form-select pe-4" aria-label="Default select example"
                                    name="leilao"
                                    style="width: inherit; min-width: 112px;"
                                >
                                    <option selected value="">Selecionar</option>
	                                <option value="1" <?php if ($leilao == "1") echo "selected"?>>SIM</option>
                                </select>
                            </div>

                            <div class="input-group input-group-sm flex-nowrap input-group-sm ms-1 mb-2 ms-md-0 me-md-2 w-md-auto">
                                <span class="input-group-text" ><small>Estado</small></span>
                                <select class="form-select w-md-auto" aria-label="Default select example"
                                    name="estado"
                                    style="width: inherit; min-width: 112px;"
                               >
                                    <option selected value="">Selecionar</option>

                                    <option value='AC' <?php if($estado == 'AC') echo 'selected'; ?>>Acre (AC)</option>
                                    <option value='AL' <?php if($estado == 'AL') echo 'selected'; ?>>Alagoas (AL)</option>
                                    <option value='AM' <?php if($estado == 'AM') echo 'selected'; ?>>Amazonas (AM)</option>
                                    <option value='AP' <?php if($estado == 'AP') echo 'selected'; ?>>Amapá (AP)</option>
                                    <option value='BA' <?php if($estado == 'BA') echo 'selected'; ?>>Bahia (BA)</option>
                                    <option value='CE' <?php if($estado == 'CE') echo 'selected'; ?>>Ceará (CE)</option>
                                    <option value='DF' <?php if($estado == 'DF') echo 'selected'; ?>>Distrito Federal (DF)</option>
                                    <option value='ES' <?php if($estado == 'ES') echo 'selected'; ?>>Espírito Santo (ES)</option>
                                    <option value='GO' <?php if($estado == 'GO') echo 'selected'; ?>>Goiás (GO)</option>
                                    <option value='MA' <?php if($estado == 'MA') echo 'selected'; ?>>Maranhão (MA)</option>
                                    <option value='MG' <?php if($estado == 'MG') echo 'selected'; ?>>Minas Gerais (MG)</option>
                                    <option value='MS' <?php if($estado == 'MS') echo 'selected'; ?>>Mato Grosso do Sul (MS)</option>
                                    <option value='MT' <?php if($estado == 'MT') echo 'selected'; ?>>Mato Grosso (MT)</option>
                                    <option value='PA' <?php if($estado == 'PA') echo 'selected'; ?>>Pará (PA)</option>
                                    <option value='PE' <?php if($estado == 'PE') echo 'selected'; ?>>Pernambuco (PE)</option>
                                    <option value='PB' <?php if($estado == 'PB') echo 'selected'; ?>>Paraíba (PB)</option>
                                    <option value='PI' <?php if($estado == 'PI') echo 'selected'; ?>>Piauí (PI)</option>
                                    <option value='PR' <?php if($estado == 'PR') echo 'selected'; ?>>Paraná (PR)</option>
                                    <option value='RJ' <?php if($estado == 'RJ') echo 'selected'; ?>>Rio de Janeiro (RJ)</option>
                                    <option value='RN' <?php if($estado == 'RN') echo 'selected'; ?>>Rio Grande do Norte (RN)</option>
                                    <option value='RO' <?php if($estado == 'RO') echo 'selected'; ?>>Rondônia (RO)</option>
                                    <option value='RR' <?php if($estado == 'RR') echo 'selected'; ?>>Roraima (RR)</option>
                                    <option value='RS' <?php if($estado == 'RS') echo 'selected'; ?>>Rio Grande do Sul (RS)</option>
                                    <option value='SC' <?php if($estado == 'SC') echo 'selected'; ?>>Santa Catarina (SC)</option>
                                    <option value='SE' <?php if($estado == 'SE') echo 'selected'; ?>>Sergipe (SE)</option>
                                    <option value='SP' <?php if($estado == 'SP') echo 'selected'; ?>>São Paulo (SP)</option>
                                    <option value='TO' <?php if($estado == 'TO') echo 'selected'; ?>>Tocantins (TO)</option>
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

        <div class="table-responsive h-100 rounded border bg-dashboard p-2 pt-0">
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
                                <small>Tipo de Consulta</small>
                            </div>
                        </th>
                        
                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Estado</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Provedor</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Leilão</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Data</small>
                            </div>
                        </th>

                    </tr>
                </thead>
                <tbody id="consultasWebserverRows" >

                    <?php 
                    // require_once('./pages/consultas_webserver_rows.page.php'); 
                    ?>

                </tbody>
            </table>
        </div>
    </div>
</form>



<script>
    $(document).ready(function() {
        setTimeout(() => {
            var formEl = $('#form_consultas_webserver');

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