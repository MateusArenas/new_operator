<?php 
@session_start(); 

@include_once('../config.php');

$estados = array(
    'AC' => 'Acre',
    'AL' => 'Alagoas',
    'AP' => 'Amapá',
    'AM' => 'Amazonas',
    'BA' => 'Bahia',
    'CE' => 'Ceará',
    'DF' => 'Distrito Federal',
    'ES' => 'Espírito Santo',
    'GO' => 'Goiás',
    'MA' => 'Maranhão',
    'MT' => 'Mato Grosso',
    'MS' => 'Mato Grosso do Sul',
    'MG' => 'Minas Gerais',
    'PA' => 'Pará',
    'PB' => 'Paraíba',
    'PR' => 'Paraná',
    'PE' => 'Pernambuco',
    'PI' => 'Piauí',
    'RJ' => 'Rio de Janeiro',
    'RN' => 'Rio Grande do Norte',
    'RS' => 'Rio Grande do Sul',
    'RO' => 'Rondônia',
    'RR' => 'Roraima',
    'SC' => 'Santa Catarina',
    'SP' => 'São Paulo',
    'SE' => 'Sergipe',
    'TO' => 'Tocantins'
);

$orderby_asc = @$_GET['orderby_asc'];
$orderby_desc = @$_GET['orderby_desc'];

$login = @$_GET['login'];
$data = @$_GET['data'];
$parametro = @$_GET['parametro'];
$tipoconsulta = @$_GET['tipoconsulta'];
$atividade = @$_GET['atividade'];
$estado = @$_GET['estado'];
$restricao = @$_GET['restricao'];
$operador = @$_GET['operador'];


// require_once('../dtos/list-consulta-dto.php');
require_once('../dtos/consulta-diaria-dto.php');

require_once('../classes/Database.class.php');
// require_once('../classes/ConsultaRepository.class.php');
require_once('../classes/ConsultaDiaria.class.php');

$db = new Database();

// $consultaRepository = new ConsultaRepository();
$consultaDiaria = new ConsultaDiaria();

$consultaDiariaDTO = new ConsultaDiariaDTO();

$consultaDiariaDTO->CodCliente = (int)$login;

if ($data) {
    // Converte a string da data para um timestamp
    $timestamp = strtotime($data);
    // Formata a data no formato desejado
    $data_formatada = date('d/m/Y', $timestamp);
} else {
    $data_formatada = '';
}

$consultaDiariaDTO->filtroData = $data_formatada ?: '';
$consultaDiariaDTO->vPlaca = $parametro ?: '';
$consultaDiariaDTO->tipoconsulta = (int)$tipoconsulta;
$consultaDiariaDTO->vramo = (int)$atividade;
$consultaDiariaDTO->vestado = $estado ?: '';
$consultaDiariaDTO->vrestricao = (int)$restricao;
$consultaDiariaDTO->vcodAtendente = (int)$operador;

// $listConsultaData = new ListConsultaDTO();

// $listConsultaData->login = (int)$login;
// $listConsultaData->data = $data ?: '';
// $listConsultaData->parametro = $parametro;
// $listConsultaData->tipoconsulta = (int)$tipoconsulta;
// $listConsultaData->atividade = (int)$atividade;
// $listConsultaData->estado = $estado ?: '';


// switch ((int)$restricao) {
//     case 1:
//         $listConsultaData->leilao = true;
//         break;
//     case 2:
//         $listConsultaData->sinistro = true;
//         break;
//     case 3:
//         $listConsultaData->hist_rf = true;
//         break;
//     case 4:
//         // está faltado esse...
//         break;
//     case 5:
//         $listConsultaData->leilao = true;
//         $listConsultaData->sinistro = true;
//         $listConsultaData->hist_rf = true;
//         $listConsultaData->score = true;
//         break;
//     case 6:
//         $listConsultaData->score = true;
//         break;
//     default:
//         break;
// }

// $listConsultaData->operador = $operador ?: '';

$orderby_asc = $orderby_asc ? explode(',', $orderby_asc) : [];
$orderby_desc = $orderby_desc ? explode(',', $orderby_desc) : [];

$orderbyAscArrayObjects = array();
$orderbyDescArrayObjects = array();

function transformOrderBy ($orderby_array) {
    $new_array = $orderby_array;
    foreach ($orderby_array as $index => $param) {
        // aqui serve para tranformar para o banco de dados.
        foreach ($orderby_array as $index => $param) {
            // aqui serve para tranformar para o banco de dados.
            switch ($param) {
                case 'login':
                    $new_array[$index] = 'CodCliente';
                    break;
                case 'parametro':
                    $new_array[$index] = 'ValorItem';
                    break;
                case 'tipo':
                    $new_array[$index] = 'TipoConsulta';
                    break;
                case 'uf':
                    $new_array[$index] = 'UF';
                    break;
                case 'atualizar':
                    $new_array[$index] = 'atualiza';
                    break;
                case 'operador':
                    $new_array[$index] = 'CodAtendente';
                    break;
                case 'rt':
                    break;
                case 'h_atualizada':
                    $new_array[$index] = 'HoraConc';
                    break;
                case 'data':
                    $new_array[$index] = 'Data';
                    break;
                case 'h_data':
                    $new_array[$index] = 'Hora';
                    break;
                default:
                    # code...
                    break;
            }
        }
    }

    return $new_array;
}


$orderbyAscArrayObjects  = transformOrderBy($orderby_asc);
$orderbyDescArrayObjects = transformOrderBy($orderby_desc);

// $listConsultaData->orderby_asc = $orderbyAscArrayObjects;
// $listConsultaData->orderby_desc = $orderbyDescArrayObjects;

// $rows = $consultaRepository->findAll($listConsultaData) ?: [];

$consultaDiariaDTO->orderby_asc = $orderbyAscArrayObjects;
$consultaDiariaDTO->orderby_desc = $orderbyDescArrayObjects;

$rows = $consultaDiaria->execute($consultaDiariaDTO) ?: [];

$qConsulta = "SELECT * FROM painel_controle";
$db->query = $qConsulta;
$r_consulta = $db->select();
$consultas = array();
foreach($r_consulta as $value)
{
    $consultas[(int)$value->ID] = $value->NomeConsulta;
}

$qCodAtendente = "SELECT * FROM atendentes";
$db->query = $qCodAtendente;
$r_atendente = $db->select();
$atendentes = array();
foreach($r_atendente as $value)
{
    $atendentes[(int)$value->CodAtendente] = $value->NomeAtendente;
}


// throw new Exception(json_encode($rows[0]));

?>


    <?php foreach ($rows as $index => $row) : 
        
        $ramoatividade = "";

        if ($row->atividade == 367){ 
            $ramoatividade = 'Teste';
        } else {  		
            if ($row->atividade == 278) { 
                $ramoatividade = 'Loja';
            } else {
                if ($row->atividade == 345){
                    $ramoatividade = 'Concess.';
                } else {
                    if ($row->atividade == 1){
                        $ramoatividade = 'Vistoria';
                    } else {
                       if ($row->atividade == 323){
                            $ramoatividade = 'Reprent.';
                        } else {
                            $ramoatividade = 'Cliente';
                         }
                    }
                }
            }
        }

        $rt = "";

        if (($row->restricao == 1) || ($row->restricao1 == 1) || ($row->restricao2 == 1) || ($row->restricao3 == 1)){ 
            $rt = "Sim";
            if ($row->restricao == 1) { 
                // <a href="javascript:void(0)" style="text-decoration:none;color:000000" onclick="localiza_img(event, {id:'echo strtoupper($r->ValorItem); ',titulo:'<b>FOTOS</b>', wd:'1070',hg:'700'})">
                //     <img src="_imgs/camera.png?v=2" height="9" border="0" align="absmiddle" style="height: 12px; margin-top: -2px;"  />
                // </a>
            }
        } else { 
            if ($row->restricao1 == 2) { 
                $rt = "Score";
            } else { 
                $rt = "Não inf.";
            } 
        }

        $consultaLink = "https://www.credoperador.com.br/rpc/inc_consulta_normalizada.php?Codigo=".$row->Codigo."&print=1&Tipo=".$row->TipoConsulta;
        
        $props = [
            // 0°
            'codigo' => $row->Codigo,
            'consultaLink' => $consultaLink,
            // 1° sessão:
            'parametro' => $row->ValorItem ?: 'Não Inf.',
            'tipoParametro' => 'Placa',
            // 2° sessão:
            'tipoCunsulta' => $consultas[(int)$row->TipoConsulta] ?: 'Não Inf.',
            'numeroCunsulta' => $row->TipoConsulta ?: 'Não Inf.',
            // 3° sessão:
            'uf' => $row->UF ? trim(strtoupper($row->UF)) : 'Não Inf.',
            'estado' => $row->UF ? ($estados[trim(strtoupper($row->UF))] . " (" . trim(strtoupper($row->UF)) .")") : 'Não Inf.',
            // 4° sessão:
            'logon' => $row->CodCliente ?: 'Não Inf.',
            'codCliente' => $row->CodCliente,
            'tipoConta' => $row->STATUS,
            'atividade' => $ramoatividade ?: 'Não Inf.',
            // 5° sessão:
            'atualizarConsulta' => $row->atualiza == 0,
            // 6° sessão:
            'operador' =>  ucwords($atendentes[(int)$row->CodAtendente] ?: ''),
            // 7° sessão:
            'rt' => $rt?:'Não inf.',
            // 8° sessão:
            'dataAlteracao' => '-',
            // 9° sessão:
            'data' =>  $row->Data ? date("d/m/Y", strtotime($row->Data)) : 'Não Inf.',
            // 10° sessão:
            'horaRealizada' => $row->Hora ?: 'Não Inf.',
            // 11° sessão:
            'horaAtualizada' => '13:24:54',
        ];

        
        $json_base64 = base64_encode(json_encode([
            "codigo" => $props['codigo'],
            "consultaLink" => $props['consultaLink'],
            "consulta" => $props['tipoCunsulta'],
            "parametro" => $props['parametro'],
            "data" => $props['data'],
            "hora" => $props['horaRealizada'],
            "codCliente" => $props['codCliente'],
        ]));
    ?>

        <tr role="button" class="table-row-fomidable to-hover"
                data-bs-open="modal" 
                data-bs-useclass="table-active"
                data-bs-template="<?=$baseURL?>/pages/opcoes_consulta.page.php?consulta=<?= base64_encode($row->Codigo) ?>&xml=0"
                data-bs-jsonb64="<?= $json_base64 ?>"
            >
                <!-- <td scope="row">
                    <div class="d-flex align-items-center">
                        <a href="$consultaLink" target="_blank" role="button" class="btn btn-light text-primary me-2 py-0 px-1 btn-sm"
                            data-bs-toggle="popover" 
                            data-bs-placement="bottom"
                            data-bs-trigger="hover" 
                            data-bs-custom-class="dark-popover"
                            data-bs-content="Imprimir consulta"
                        >
                            <small>
                                <i class="bi bi-printer-fill"></i>
                            </small>
                        </a>
                        <span class="me-2"
                            data-bs-toggle="popover" 
                            data-bs-placement="bottom"
                            data-bs-trigger="hover" 
                            data-bs-custom-class="dark-popover"
                            data-bs-content="Gravar ou editar consulta"
                        >
                            <button type="button" class="btn btn-light text-primary py-0 px-1 btn-sm"
                                data-bs-open="modal" 
                                data-bs-template="<?=$baseURL?>/pages/editar_alterar.page.php"
                                data-bs-jsonb64='<?= $json_base64 ?>'
                                data-bs-modaltype="lg"
                            >
                                <small>
                                    <i class="bi bi-file-earmark-text-fill"></i>
                                </small>
                            </button>
                        </span>
                        <span
                            data-bs-toggle="popover" 
                            data-bs-placement="bottom"
                            data-bs-trigger="hover" 
                            data-bs-custom-class="dark-popover"
                            data-bs-content="Solicitar Alteração"
                        >
                            <button type="button" class="btn btn-light text-primary py-0 px-1 btn-sm"
                                data-bs-open="modal" 
                                data-bs-template="<?=$baseURL?>/pages/pedir_atualizacao.page.php"
                                data-bs-jsonb64='<?= $json_base64 ?>'
                            >
                                <small>
                                    <i class="bi bi-broadcast"></i>
                                </small>
                            </button>
                        </span>
                    </div>
                </td> -->
                <td scope="row">
                    <a href="#" class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover"
                        data-bs-open="modal" 
                        data-bs-template="<?=$baseURL?>/pages/consulta_cliente.page.php?codCliente=<?= $row->CodCliente ?>&consulta=<?= base64_encode($row->Codigo) ?>"
                        data-bs-jsonb64='<?= $json_base64 ?>'
                        data-bs-modaltype="lg"
                    >
                        <small class="fw-semibold text-nowrap">
                            <i class="bi bi-person-fill"></i> <?= $props['logon'] ?> | <?= $props['tipoConta'] ?> | <?= $props['atividade'] ?>
                        </small>
                    </a>
                </td>

                <!-- PARAMETRO START -->
                <td scope="row">
                    <span 
                        data-bs-toggle="popover" 
                        data-bs-placement="top"
                        data-bs-trigger="hover" 
                        data-bs-custom-class="dark-popover"
                        data-bs-content="<?= $row->ItemConsultado ?>"
                    >
                        <span class="badge badge-sm bg-light">
                            <?= $props['parametro'] ?>
                        </span>
                    </span>
                </td>
                <!-- PARAMETRO END -->

                <!-- TIPO CONSULTA START -->
                <td scope="row">
                    <span class="badge badge-sm bg-light">
                        <?= $props['tipoCunsulta'] ?>
                    </span>
                </td>
                <!-- TIPO CONSULTA END -->

                <!-- ESTADO START -->
                <td>
                    <span class="badge badge-sm bg-light">
                        <?= $props['estado'] ?>
                    </span>
                </td>
                <!-- ESTADO END -->

                <!-- ATUALIZAR START -->
                <td>
                    <?php if($props['atualizarConsulta']): ?>
                        <a href="#" class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover" 
                            data-bs-open="modal" 
                            data-bs-template="<?=$baseURL?>/pages/pendente_robo.page.php?consulta=<?= base64_encode($row->Codigo) ?>"
                            data-bs-jsonb64='<?= $json_base64 ?>'
                            data-bs-modaltype="modal-fullscreen-md-down"
                        >
                            <small class="text-nowrap fw-semibold">
                                Sim
                            </small>
                        </a>
                    <?php else: ?>
                        <small class="text-nowrap text-muted">
                            Não
                        </small>
                    <?php endif; ?>
                </td>
                <!-- ATUALIZAR END -->

                <!-- OPERADOR START -->
                <td>
                    <span class="badge badge-sm bg-light">
                        <?= $props['operador'] ?>
                    </span>
                </td>
                <!-- OPERADOR END -->

                <!-- RESTRIÇÃO START -->
                <td>
                    <?php if (($row->restricao == 1) || ($row->restricao1 == 1) || ($row->restricao2 == 1) || ($row->restricao3 == 1)): ?>
                        
                        <?php if ($row->restricao == 1): ?>
                            <a href="#" class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover"
                                data-bs-open="modal" 
                                data-bs-template="<?=$baseURL?>/pages/imagens_leilao.page.php?placa=<?= $row->ValorItem ?>"
                                data-bs-jsonb64='<?= $json_base64 ?>'
                                data-bs-modaltype="lg"
                            >
                                <small class="fw-semibold">Sim <i class="bi bi-camera-fill"></i></small>
                            </a>
                        <?php else: ?>
                            <small class="fw-semibold text-nowrap">
                                <span class="text-warning">Sim</span>
                            </small>
                        <?php endif; ?>

                    <?php else: ?>

                        <?php if ($row->restricao1 == 2): ?>
                            <small class="fw-semibold text-nowrap">
                                <span class="text-danger">Score</span>
                            </small>
                        <?php else: ?>
                            <small class="fw-semibold text-nowrap text-muted">
                                <span>Não inf.</span>
                            </small>
                        <?php endif; ?>

                    <?php endif; ?>
                </td>
                <!-- RESTRIÇÃO END -->

                <!-- DATA DE ALTERAÇÃO START -->
                <td>
                    <small class="fw-semibold text-nowrap">
                        <?= $props['dataAlteracao'] ?>
                    </small>
                </td>
                <!-- DATA DE ALTERAÇÃO END -->

                <!-- HORA DA ALTERAÇÃO START -->
                <td>
                    <span class="badge badge-sm bg-light">
                        <?= $props['horaAtualizada'] ?>
                    </span>
                </td>
                <!-- HORA DA ALTERAÇÃO END -->

                <!-- DATA START -->
                <td>
                    <span class="badge badge-sm bg-light">
                        <?= $props['data'] ?> às <?= $props['horaRealizada'] ?>
                    </span>
                </td>
                <!-- DATA END -->

            </tr>
    <?php endforeach; ?>


<script>
    function localiza_img(e, args) {
		var url = ""
		url += "rpc/inc_leilao_img.php";
		url += "?placa=" + args.id;

		//alert(url);

		System.CreateWindow( e, url, 'frmopen', {
			dragdrop: '1',
			position: 'absolute',
			width: args.wd,
			height: args.hg,
			title: args.titulo,
			closeico:		'_imgs/icons_close.gif',
			backgroundImage:'_imgs/bkground.gif',
			fontCaptionColor: '#000',
			fontCaptionSize: '12',
			borderStyle: 'dotted',
			borderWidth: '1',
			borderColor: '#5050D3'
		});
	}
</script>


<!-- seta o total das consultas -->
<script>
    $('#total-consultas').text("<?=count($rows)?>");
</script>