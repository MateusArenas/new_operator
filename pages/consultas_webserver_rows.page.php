<?php @session_start();

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

require_once('../classes/Database.class.php');

$db = new Database();

$login = trim(@$_REQUEST['login']);
$data = trim(@$_REQUEST['data'] ?: date('Y-m-d'));
$tipoconsulta = trim(@$_REQUEST['tipoconsulta']);

$parametro = trim(@$_REQUEST['parametro']);
$provedor = trim(@$_REQUEST['provedor']);
$leilao = trim(@$_REQUEST['leilao']);
$estado = trim(@$_REQUEST['estado']);

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

if (($tipoconsulta == "") && ($parametro == "")  && ($login == "")) {
} else {

    $extra = '';
    if ($estado) $extra = " AND Uf = '{$estado}' ";

    if (@$_REQUEST["Filtro"] == "Robo") {

        #aparece consulta que tem que ser feita pelo robô
        $qRobo = "SELECT * FROM consultas  WHERE sistemanovo = 'N'  AND Motivo=1 AND CodAtendente='0' AND origem !='serasa'   AND CodCliente != '2938' {$extra} ORDER BY Codigo DESC";
    } elseif (@$_REQUEST["Filtro"] == "") {


        if ($data  <> "") {

            #aparece consulta que tem que ser feita do dia

            if ($login != "") {

                $qRobo = "SELECT * FROM consultas WHERE sistemanovo = 'N'  AND Motivo=3 AND Data = '" . $data . "' AND CodCliente = '" . $login . "' AND Id_Faturamento <>'' {$extra} ORDER BY Codigo DESC";
            }

            if ($tipoconsulta != "") {

                $qRobo = "SELECT * FROM consultas WHERE  TipoConsulta=$tipoconsulta AND Motivo=3 AND Data = '" . $data . "'  AND CodCliente <>'2938' {$extra} ORDER BY Codigo DESC";
            }


            if ($provedor != "") {

                if ($tipoconsulta != "") {

                    $qRobo = "SELECT TipoConsulta, Codigo, CodCliente, ValorItem, Hora, origem, leilao, UF FROM consultas WHERE  TipoConsulta=$tipoconsulta AND origem='" . $provedor . "' AND leilao='" . $leilao . "'  AND Data = '" . $data . "'  {$extra} ORDER BY Codigo DESC";
                } else {

                    $qRobo = "SELECT TipoConsulta, Codigo, CodCliente, ValorItem, Hora, origem, leilao, UF FROM consultas WHERE  origem='" . $provedor . "' AND Data = '" . $data . "' {$extra} ORDER BY Codigo DESC";
                }
            }

            if ($parametro != "") {

                function duasPlacas($placa)
                {
                    $placa = strtoupper($placa);
                    $placa = preg_replace("/[^a-zA-Z0-9]/", "", $placa);
                    $digito = substr($placa, 4, 1);
                    $alphabet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
                    $mercosul = "";
                    $normal = "";
                    if (preg_match('/[0-9]/', $digito)) {
                        $digito = $alphabet[(int)$digito];
                        $mercosul = substr($placa, 0, 4) . $digito . substr($placa, -2);
                        $normal = $placa;
                    } else if (preg_match('/[a-zA-Z]/', $digito)) {
                        $digito = array_search($digito, $alphabet);
                        $normal = substr($placa, 0, 4) . $digito . substr($placa, -2);
                        $mercosul = $placa;
                    }
                    $resultado = new stdClass();
                    $resultado->mercosul = $mercosul;
                    $resultado->normal = $normal;
                    return $resultado;
                }

                $placas = duasPlacas($parametro);

                $qRobo = "SELECT * FROM consultas WHERE sistemanovo = 'N' AND (ValorItem='" . $parametro . "' OR ValorItem='" . $placas->normal . "' OR ValorItem='" . $placas->mercosul . "') AND Motivo=3 AND Data = '" . $data . "' AND Id_Faturamento <> '' {$extra} ORDER BY Codigo DESC";
            }

            if ($tipoconsulta != "" && $login != "") {

                $qRobo = "SELECT * FROM consultas WHERE sistemanovo = 'N' AND TipoConsulta=$tipoconsulta AND Motivo='3' AND Data = '" . $data . "' AND Id_Faturamento <>'' AND CodCliente = '" . $login . "' {$extra} ORDER BY Codigo DESC";
            }
        } else {

            #aparece consulta que tem que ser feita do dia
            $qRobo = "SELECT * FROM consultas WHERE sistemanovo = 'N' AND Motivo='3' AND Id_Faturamento <>'' AND Data = '" . $data . "' {$extra}  AND CodCliente <> '2938' ORDER BY Codigo DESC LIMIT 0,15";
        }

        $qRobo .= " LIMIT 3000";
    }


    // echo $qRobo;
    // exit;

    $db->query = str_replace('consultas', 'consultas_historico', $qRobo);

    $qExecuta1 = $db->select();

    $db->query = $qRobo;
    $qExecuta2 = $db->select();

    // $qExecuta = array_merge($qExecuta1, $qExecuta2);
    $rows = array_merge($qExecuta1, $qExecuta2);
}
$start = microtime(true);
?>

<!-- INTERA SOBRE AS CONSULTAS -->
<?php foreach ($rows as $index => $row) : ?>

<?php 
    #pesquisa o tipo de consulta
    $db->query = "SELECT * FROM ttipoconsulta WHERE id = ? ";
    $db->content = [ [$row->TipoConsulta] ];
    $row_tipoconsulta = $db->selectOne();
?>

<tr role="button" class="table-row-fomidable to-hover px-4"
    data-bs-open="modal" 
    data-bs-useclass="table-active"
    data-bs-template="<?=$baseURL?>/pages/opcoes_consulta.page.php?consulta=<?= base64_encode($row->Codigo) ?>&xml=1&editar=0&pedido=0"
    data-bs-jsonb64="<?= @$json_base64 ?>"
>
    <td scope="row">
        <a href="#" class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover"
            data-bs-open="modal" 
            data-bs-template="<?=$baseURL?>/pages/consulta_cliente.page.php?codCliente=<?= $row->CodCliente ?>&consulta=<?= base64_encode($row->Codigo) ?>"
            data-bs-jsonb64='<?= @$json_base64 ?>'
            data-bs-modaltype="lg"
        >
            <small class="fw-semibold text-nowrap">
                <i class="bi bi-person-fill"></i> 
                <?= $row->CodCliente ?> 
            </small>
        </a>
    </td>

    <!-- PARAMETRO START -->
    <td scope="row">
        <span 
            data-bs-toggle="popover" 
            data-bs-placement="bottom"
            data-bs-trigger="hover" 
            data-bs-custom-class="dark-popover"
            data-bs-content="<?= $row->ItemConsultado ?>"
        >
            <small class="fw-bold text-nowrap">
                <?= ucwords($row->ItemConsultado); ?>: <?= strtoupper($row->ValorItem); ?>
            </small>
        </span>
    </td>
    <!-- PARAMETRO END -->

    <!-- TIPO CONSULTA START -->
    <td scope="row">
        <small class="fw-semibold text-nowrap text-capitalize">
            <?= @$row->NumeroTotal ?><?= $row_tipoconsulta->tipoconsulta ?>
            <?php if(@$_GET["MOSTRACODIGO"]=="SIM"){ echo $row->Codigo; } ?>
        </small>
    </td>
    <!-- TIPO CONSULTA END -->

    <!-- ESTADO START -->
    <td>
        <span class="fw-semibold me-2 text-nowrap">
            <small class="text-capitalize">
                <?= $row->UF ? ($estados[trim(strtoupper($row->UF))] . " (" . trim(strtoupper($row->UF)) .")") : 'Não Inf.' ?>
            </small>
        </span>
    </td>
    <!-- ESTADO END -->

    <!-- PROVEDOR START -->
    <td>
        <span class="fw-semibold me-2 text-nowrap">
            <small class="text-capitalize">
                <?= $row->origem ?>
            </small>
        </span>
    </td>
    <!-- PROVEDOR END -->

    <!-- Leilão START -->
    <td>
        <span class="fw-semibold me-2 text-nowrap">
            <small class="text-capitalize">
                <?php if ( $row->leilao ==1 ) : ?>
                    <span class="text-danger">Sim</span>
                <?php else: ?>
                    Nao
                <?php endif; ?>
            </small>
        </span>
    </td>
    <!-- Leilão END -->

    <!-- DATA START -->
    <td>
        <small class="fw-bold text-nowrap">
            <?= $row->Data ? date("d/m/Y", strtotime($row->Data)) : 'Não Inf.' ?> às <?= $row->Hora ?: 'Não Inf.' ?>
        </small>
    </td>
    <!-- DATA END -->

</tr>
<?php endforeach; ?>

<!-- seta o total das consultas -->
<script>
$('#total-consultas').text("<?=count($rows)?>");
</script>