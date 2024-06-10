<?php 
@session_start();

@include_once('../config.php');

// $consultaVeicularTableRow = ConsultaVeicularTableRow($props = [
//     'parametro' => 'Placa',
//     'tipoParametro' => 'Placa',
//     'numeroCunsulta' => '1234567890',
//     'tipoCunsulta' => 'Simples',
//     'uf' => 'SP',
//     'estado' => 'São Paulo',
//     'logon' => 'usuario123',
// ]);

// echo $consultaVeicularTableRow;

$login = @$_GET['login'] ?: "";
$data = @$_GET['data'] ?: date('Y-m-d');
$parametro = @$_GET['parametro'] ?: "";
$tipoconsulta = @$_GET['tipoconsulta'];
$atividade = @$_GET['atividade'];
$estado = @$_GET['estado'];
$restricao = @$_GET['restricao'];
$operador = @$_GET['operador'];


$orderby_asc = @$_GET['orderby_asc'] ?: '';
$orderby_desc = @$_GET['orderby_desc'] ?: ''; // campos para serem ordenados em 

$orderby_array_asc = [];
if ($orderby_asc) $orderby_array_asc = explode(',', $orderby_asc);

$orderby_array_desc = [];
if ($orderby_desc) $orderby_array_desc = explode(',', $orderby_desc);

$data = @$_GET['data'] ?: date('Y-m-d'); // apartir dessa data
$login = @$_GET['login']; // codigo cliente
$parametro = @$_GET['parametro']; // tipo parametro: 0: parametro da consulta, 1:codigo da consulta

$atividade = @$_GET['atividade']; // qual é o ramo
$tipoconsulta = @$_GET['tipoconsulta']; // se é cred especial....
$estado = @$_GET['estado']; // estado...
$restricao = @$_GET['restricao']; // se tem sinistro score, leilão ....
$operador = @$_GET['operador']; // qual é o atendente

function getParamFromOrderByASC($param) {
    global $orderby_array_asc;
    foreach ($orderby_array_asc as $field) {
        if ($field === $param) return $field;
    }
    return null;
}

function clearParamFromOrderByASC($param, $orderby_array_asc) {
    return array_filter($orderby_array_asc, function($value) use($param) {
        return $value !== $param;
    }, ARRAY_FILTER_USE_BOTH);
}

function getParamFromOrderByDESC($param) {
    global $orderby_array_desc;
    foreach ($orderby_array_desc as $field) {
        if ($field === $param) return $field;
    }
    return null;
}

function clearParamFromOrderByDESC($param, $orderby_array_desc) {
    return array_filter($orderby_array_desc, function($value) use($param) {
        return $value !== $param;
    }, ARRAY_FILTER_USE_BOTH);
}


function useParamFromOrderBy($param) {
    global $orderby_array_asc;
    global $orderby_array_desc;

    $byasc = array_map(function($val) {
        return $val;
    }, $orderby_array_asc);

    $bydesc = array_map(function($val) {
        return $val;
    }, $orderby_array_desc);

    $has_orderby_asc = false;
    foreach ($byasc as $field) {
        if ($field === $param) $has_orderby_asc = true;
    }

    $has_orderby_desc = false;
    foreach ($bydesc as $field) {
        if ($field === $param) $has_orderby_desc = true;
    }

    // limpa antes o campo para poder inserir sem duplicatas.
    $byasc = clearParamFromOrderByASC($param, $byasc);
    $bydesc = clearParamFromOrderByDESC($param, $bydesc);

    if (!$has_orderby_desc && !$has_orderby_asc) {
        // quando não há vai primeiro no ASC ^
        array_push($byasc, $param);
    } elseif ($has_orderby_asc) {
        // quando estiver no ASC vai para o DESC
        array_push($bydesc, $param);
    } elseif ($has_orderby_desc) {
        // quando estiver no DESC ele limpa
    }

    return [ "asc" => $byasc, "desc" => $bydesc ];
}

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

    $orderbys = useParamFromOrderBy($param);

    if (count($orderbys['asc'])) {
        $params['orderby_asc'] = join(',', $orderbys['asc']);
    } else {
        unset($params['orderby_asc']);  
    }

    if (count($orderbys['desc'])) {
        $params['orderby_desc'] = join(',', $orderbys['desc']);
    } else {
        unset($params['orderby_desc']);  
    }

    // Reconstruir a URL com os parâmetros de consulta atualizados
    $parsed_url['query'] = http_build_query($params);
    
    return '?' . $parsed_url['query'];
}

?>


<form class="d-flex flex-column flex-fill p-3"
    data-form-type="ajax"
    data-form-target="#consultasVeicularesRows"
    action="<?=$baseURL?>/pages/consultas_veiculares_rows.page.php"
    method="get"
    id="form_consultas"
>

    <div class="d-flex flex-column mb-2">

        <h1 class="fs-6 mb-3">
            Consultas Veiculares
        </h1>

        <!-- <small class="text-muted mb-3" >
            Localize consultas de veículos para obter informações precisas e execute ações como abrir um chamado ou alterar uma consulta.
        </small> -->

        <div class="row gx-0 gy-2">
            
            <input id="orderby_asc" type="hidden" name="orderby_asc"  value="<?= @$_GET['orderby_asc'] ?: '' ?>"  >
            <input id="orderby_desc" type="hidden" name="orderby_desc" value="<?= @$_GET['orderby_desc'] ?: '' ?>" >
            
            <div class="col-12 col-md-6 d-none">
                <div class="d-flex align-items-center justify-content-end">
                    
                    <!-- <small class="me-2 flex-grow-1 flex-md-grow-0 text-start">1-50 de 24.147</small> -->
                    <!-- <span class="me-2"
                        data-bs-toggle="popover" 
                        data-bs-placement="bottom"
                        data-bs-trigger="hover focus" 
                        data-bs-custom-class="dark-popover"
                        data-bs-content="Próximas"
                    >
                        <a href="#" class="btn btn-light btn-sm">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </span>
                    <span 
                        data-bs-toggle="popover" 
                        data-bs-placement="bottom"
                        data-bs-trigger="hover focus" 
                        data-bs-custom-class="dark-popover"
                        data-bs-content="Anteriores"
                    >
                        <a href="#" class="btn btn-light btn-sm">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </span> -->
                </div>
            </div>

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
                        placeholder="Parâmetro da Consulta"
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

                                    <option value="31" <?php if ($tipoconsulta == "31") echo "selected"?>>CRED AGREGADOS</option>
                                    <option value="14" <?php if ($tipoconsulta == "14") echo "selected"?>>CRED BNV HIST-RF</option>
                                    <option value="18" <?php if ($tipoconsulta == "18") echo "selected"?>>CRED BASE ESTADUAL</option>
                                    <option value="7" <?php if ($tipoconsulta == "7") echo "selected"?>>CRED ESPECIAL</option>
                                    <option value="77" <?php if ($tipoconsulta == "77") echo "selected"?>>CRED COMPLETA</option>
                                    <option value="19" <?php if ($tipoconsulta == "19") echo "selected"?>>CRED TOTAL</option>
                                    <option value="12" <?php if ($tipoconsulta == "12") echo "selected"?>>CRED LEILÃO</option>
                                    <option value="78" <?php if ($tipoconsulta == "78") echo "selected"?>>CRED FROTA</option>
                                    <option value="83" <?php if ($tipoconsulta == "83") echo "selected"?>>CRED CSV + MULTAS</option>
                                    <!--<option value="59" <?php if ($tipoconsulta == "59") echo "selected"?>>CRED SEGRURO AUTO</option>-->
                                    <option value="9" <?php if ($tipoconsulta == "9") echo "selected"?>>CRED GRAVAME</option>
                                    <!--<option value="71" <?php if ($tipoconsulta == "71") echo "selected"?>>CRED RENAJUD</option>-->
                                    <option value="256" <?php if ($tipoconsulta == "256") echo "selected"?>>ATPV DIGITAL SP</option>
                                    <option value="258" <?php if ($tipoconsulta == "258") echo "selected"?>>ATPV DIGITAL MG</option>
                                    <option value="251" <?php if ($tipoconsulta == "251") echo "selected"?>>ATPV DIGITAL PR</option>
                                    <option value="259" <?php if ($tipoconsulta == "259") echo "selected"?>>ATPV DIGITAL SC</option>
                                    <option value="217" <?php if ($tipoconsulta == "217") echo "selected"?>>CRLV DIGITAL</option>
                                    <option value="215" <?php if ($tipoconsulta == "215") echo "selected"?>>CRLV DIGITAL RN</option>
                                    <option value="314" <?php if ($tipoconsulta == "314") echo "selected"?>>CRLV DIGITAL RJ</option>
                                    <option value="40" <?php if ($tipoconsulta == "40") echo "selected"?>>LAUDO VIST /CAUTELAR</option>
                                    <option value="38" <?php if ($tipoconsulta == "38") echo "selected"?>>LAUDO GRV / CAUTELAR</option>
<!-- 
                                    <option value="31">CRED AGREGADOS</option>
                                    <option value="14">CRED BNV HIST-RF</option>
                                    <option value="18">CRED BASE ESTADUAL</option>
                                    <option value="7">CRED ESPECIAL</option>
                                    <option value="77">CRED COMPLETA</option>
                                    <option value="19">CRED TOTAL</option>
                                    <option value="12">CRED LEILÃO</option>
                                    <option value="78">CRED FROTA</option>
                                    <option value="83">CRED CSV + MULTAS</option>

                                    <option value="9">CRED GRAVAME</option>

                                    <option value="256">ATPV DIGITAL SP</option>
                                    <option value="258">ATPV DIGITAL MG</option>
                                    <option value="251">ATPV DIGITAL PR</option>
                                    <option value="259">ATPV DIGITAL SC</option>
                                    <option value="217">CRLV DIGITAL</option>
                                    <option value="215">CRLV DIGITAL RN</option>
                                    <option value="314">CRLV DIGITAL RJ</option>
                                    <option value="40">LAUDO VIST /CAUTELAR</option>
                                    <option value="38">LAUDO GRV / CAUTELAR</option> -->
                                </select>
                            </div>
                            <div class="input-group input-group-sm flex-nowrap input-group-sm ms-1 mb-2 ms-md-0 me-md-2 w-md-auto">
                                <span class="input-group-text" ><small>Ramo</small></span>
                                <select class="form-select w-md-auto" aria-label="Default select example"
                                    name="atividade"
                                    style="width: inherit; min-width: 112px;"
                                >
                                    <option selected value="">Selecionar</option>
                                    <option value="1" <?php if ($atividade == "1") echo "selected"?>>VISTORIA
                                    <option value="278" <?php if ($atividade == "278") echo "selected"?>>LOJA
                                    <option value="4" <?php if ($atividade == "4") echo "selected"?>>CLIENTE
                                </select>
                            </div>
                       </div>
                       <div class="d-flex flex-grow-1 flex-md-grow-0">
                            <div class="input-group input-group-sm flex-nowrap input-group-sm me-1 mb-2 ms-md-0 me-md-2 w-md-auto">
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
                            
                           <div class="input-group flex-nowrap input-group-sm ms-1 mb-2 ms-md-0 me-md-2 w-md-auto">
                                <span class="input-group-text" ><small>Restrição</small></span>
                                <select class="form-select pe-4" aria-label="Default select example"
                                    name="restricao"
                                    style="width: inherit; min-width: 112px;"
                                >
                                    <option selected value="">Selecionar</option>
                                     <!-- <option value="5">Todas</option>
                                     <option value="1">Leilão</option>
                                     <option value="2">Sinistro</option>
                                     <option value="4">Vec/Roubo</option>
                                     <option value="3">Hist /RF</option>
                                     <option value="6">Score</option> -->

                                    <!-- <option value="5" <?php if ($vrestricao4 == "1") echo "selected"?>>TODAS
                                    <option value="1" <?php if ($vrestricao == "1") echo "selected"?>>LEILÃO
                                    <option value="2" <?php if ($vrestricao1 == "1") echo "selected"?>>SINISTRO
                                    <option value="4" <?php if ($vrestricao3 == "1") echo "selected"?>>VEC/ROUBO
                                    <option value="3" <?php if ($vrestricao2 == "1") echo "selected"?>>HIST /RF
                                    <option value="6" <?php if ($vrestricao6 == "2") echo "selected"?>>SCORE -->

                                    <option value="5" <?php if ($restricao == "5") echo "selected"?>>TODAS
                                    <option value="1" <?php if ($restricao == "1") echo "selected"?>>LEILÃO
                                    <option value="2" <?php if ($restricao == "2") echo "selected"?>>SINISTRO
                                    <option value="4" <?php if ($restricao == "4") echo "selected"?>>VEC/ROUBO
                                    <option value="3" <?php if ($restricao == "3") echo "selected"?>>HIST /RF
                                    <option value="6" <?php if ($restricao == "6") echo "selected"?>>SCORE
                                </select>
                            </div>
                       </div>
                       <div class="d-flex flex-grow-1 flex-md-grow-0">
                            <div class="input-group input-group-sm flex-nowrap input-group-sm mb-2 me-md-2 w-md-auto">
                                <span class="input-group-text" ><small>Operador</small></span>
                                <select class="form-select w-md-auto " aria-label="Default select example"
                                     name="operador"
                                     style="width: inherit; min-width: 112px;"
                                >
                                    <option selected value="">Selecionar</option>
                                    <?php
                                        $sql = '';
                                        $sql .= " SELECT *";
                                        $sql .= " FROM atendentes";
                                        $sql .= " WHERE Permissoes2 = 0";
                                        $sql .= " AND Situacao <> 2";
                                        $sql .= " ORDER BY NomeAtendente";

                                        $db->query = $sql;
                                        $r = $db->select();
                                        $rcount = count($r);

                                        if ($rcount != 0)
                                        {
                                            $corline = "FFF1E3";
                                            foreach ($r as $key => $table )
                                            {
                                                $t = "";
                                                if ("[".$table->CodAtendente."]" == "[".@$vestigio."]") $t = "selected";
                                                echo "<option value='".$table->CodAtendente."' $t >".$table->NomeAtendente;
                                            }
                                        }
                                    ?>
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

        <div class="table-responsive h-100 border rounded bg-dashboard p-2 pt-0">
            <table class="table dashboard-table table-hover table-sm caption-top">
                <caption class="pb-0 pt-2 px-2">
                    <small class="text-start text-muted " style="font-size: 12px;">
                        Total / Realizadas: <span id="total-consultas"></span>
                    </small>
                </caption>
                <thead class="sticky-top table-header bg-dashboard">
                    <tr class="align-middle" style="height: 42px;">
                        <!-- <th scope="col">
                            <button type="button" disabled class="btn btn-sm btn-light pe-4 text-start w-100">
                                <small class="fw-semibold">Opções</small>
                            </button>
                        </th> -->

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
                                <small>Tipo</small>
                            </div>
                        </th>
                        
                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Estado</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Atualizar</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Operador</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>RT</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Alterado</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>H.Atualizada</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Data</small>
                            </div>
                        </th>

                    </tr>
                </thead>
                <tbody id="consultasVeicularesRows" >

                    <?php 
                    // require_once('./pages/consultas_veiculares_rows.page.php'); 
                    ?>

                </tbody>
               
            </table>
        </div>
    </div>
</form>



<script>
    $(document).ready(function() {
        setTimeout(() => {
            var formEl = $('#form_consultas');

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