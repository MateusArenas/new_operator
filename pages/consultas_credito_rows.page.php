<?php @session_start(); 

$login = @$_GET['login'];
$data = @$_GET['data'];
$parametro = @$_GET['parametro'];
$tipoconsulta = @$_GET['tipoconsulta'];
$totalconsultas = @$_GET['totalconsultas'];

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

@include_once('../config.php');
require_once('../classes/Database.class.php');

$db = new Database();

$qRobo = '';
$cRobo = [];

if ( ( $tipoconsulta == "" ) && ( $parametro == "" )  && ( $login == "" ) )
{

} 
else
{

    if(@$_REQUEST["Filtro"] == "Robo")
    {
        #aparece consulta que tem que ser feita pelo robô
        //$qRobo = "SELECT * FROM consultas INNER JOIN resultado_consultas ON consultas.Codigo = resultado_consultas.CodigoResu WHERE sistemanovo = 'N' AND Motivo=1 AND CodAtendente='0' AND origem='serasa' ORDER BY Hora DESC";
    } 
    elseif(@$_REQUEST["Filtro"] == "")
    {

        if($totalconsultas) {
            $totalconsultas1 = " CodCliente, count(CodCliente) as NumeroTotal ";
            $totalconsultas2 = " group by CodCliente ";
            $totalconsultas3 = " NumeroTotal ";
        } else {
            $totalconsultas1 = " * ";
            $totalconsultas2 = " ";
            $totalconsultas3 = " Hora ";
        }

        if($data <> "") {

            #aparece consulta que tem que ser feita do dia
            if($_GET["parametro"] <> ""){
                $parametro = trim($parametro);
                $parametro = str_replace('.', '', $parametro);
                $parametro = str_replace('/', '', $parametro);
                $parametro = str_replace('-', '', $parametro);
                $qRobo = "SELECT *  FROM consultas  WHERE sistemanovo = 'N' AND Motivo='3' AND Data = ? AND ValorItem = ? ORDER BY Hora DESC";
                $cRobo[] = [$data];
                $cRobo[] = [$parametro];
            } else {

                if($login <> "") {

                    if ( $tipoconsulta != "" ) {

                        $qRobo = "SELECT *  FROM consultas  WHERE sistemanovo = 'N'  AND CodCliente = ?  AND TipoConsulta = ? AND Motivo = '3' AND Data = ? AND CodCliente <> 2938 AND Id_Faturamento = '' ORDER BY Hora DESC";
                        $cRobo[] = [$login, 'int'];
                        $cRobo[] = [$tipoconsulta];
                        $cRobo[] = [$data];
                    } else {

                        $qRobo = "SELECT *  FROM consultas
                            WHERE sistemanovo = 'N' 
                            AND Motivo='3' 
                            AND Data = ? 
                            AND CodCliente = ? 
                            AND Id_Faturamento = '' 
                            AND ( origem = 'checkcinco' 
                                OR origem = 'serasa'
                                OR origem = 'Procob'
                                OR origem = 'ACSP'
                                OR origem = 'allcheck'
                                OR origem = 'credauto'
                                OR origem = 'spc'
                                OR origem = 'grupovc'
                            )
                            ORDER BY Hora DESC
                        ";

                        $cRobo[] = [$data];
                        $cRobo[] = [$login, 'int'];
                    }

                } else {


                if ( $tipoconsulta !="" ) {

                    $qRobo = "SELECT * FROM consultas  WHERE sistemanovo = 'N' AND TipoConsulta = ? AND Motivo = '3' AND Data = ? AND CodCliente <> 2938 AND Id_Faturamento = '' ORDER BY Hora DESC";
                    $cRobo[] = [$tipoconsulta];
                    $cRobo[] = [$data];

                } else {

                    $qRobo = "SELECT ".$totalconsultas1." FROM consultas
                                WHERE ( sistemanovo = 'N' 
                                    AND Motivo = '3' 
                                    AND Data = ?
                                    AND Id_Faturamento = '' 
                                    AND ( origem='checkcinco' OR origem='serasa' OR origem='Procob' OR origem='ACSP' OR origem='spc' )
                                )
                                OR sistemanovo = 'N' AND TipoConsulta='' AND Motivo='3' AND Data = ?
                                ".$totalconsultas2."
                                ORDER BY ".$totalconsultas3." DESC";

                    $cRobo[] = [$data];
                    $cRobo[] = [$data];
                }

            }
        }

        } else {

            $qRobo = "SELECT * FROM consultas WHERE sistemanovo = 'N' AND Motivo='3' AND Id_Faturamento <> '' AND Data = ?  AND CodCliente <> '2938' ORDER BY Hora DESC LIMIT 0,15";
            $cRobo[] = [$data];
        }


        //$qRobo = "SELECT * FROM consultas WHERE Motivo=1 AND CodAtendente=''";

    }

    // echo $qRobo;

    // echo '<br />';

    // var_dump($cRobo);
    // exit;

    $db->query = str_replace('consultas', 'consultas_historico', $qRobo);
    $db->content = $cRobo;
    $qExecuta1 = $db->select();

    $db->query = $qRobo;
    $db->content = $cRobo;
    $qExecuta2 = $db->select();

    $rows = array_merge($qExecuta1, $qExecuta2);

}

?>

<!-- INTERA SOBRE AS CONSULTAS -->
<?php foreach ($rows as $index => $row) : ?>

    <?php 
        #pesquisa o nome do cliente
        $db->query = "SELECT * FROM clientes WHERE Codigo = ? ";
        $db->content = [ [$row->CodCliente, 'int'] ];
        $row_cliente = $db->selectOne();

        #pesquisa o tipo de consulta
        $db->query = "SELECT * FROM ttipoconsulta WHERE id = ? ";
        $db->content = [ [$row->TipoConsulta] ];
        $row_tipoconsulta = $db->selectOne();
    ?>

    <tr role="button" class="table-row-fomidable to-hover px-4"
        data-bs-open="modal" 
        data-bs-useclass="table-active"
        data-bs-template="<?=$baseURL?>/pages/opcoes_consulta.page.php?consulta=<?= base64_encode($row->Codigo) ?>&xml=0&editar=0&pedido=<?=!isset($row->cache) ? '1' : '0'?>"
        data-bs-jsonb64="<?= $json_base64 ?>"
    >
        <td scope="row">
            <a href="#" class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover"
                data-bs-open="modal" 
                data-bs-template="<?=$baseURL?>/pages/consulta_cliente.page.php?codCliente=<?= $row->CodCliente ?>&consulta=<?= base64_encode($row->Codigo) ?>"
                data-bs-jsonb64='<?= $json_base64 ?>'
                data-bs-modaltype="lg"
            >
                <small class="fw-semibold text-nowrap">
                    <i class="bi bi-person-fill"></i> 
                    <?= $row->CodCliente ?> 
                    <?php if( $row_cliente->STATUS == "G" ) { ?>

                        <span class="text-danger">/ So Consult</span>

                    <?php } else { ?>

                        <?php if( $row_cliente->STATUS == "L" ) { ?>

                            <span class="text-danger">/ <?= $row_cliente->STATUS ?></span>

                        <?php } else { ?>

                            <span>/ Cred Auto</span>

                        <?php } 
                    } ?>
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
                    <?= ucwords($row->ItemConsultado); ?>:  <?= strtoupper($row->ValorItem); ?>
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

        <!-- OPERADOR START -->
        <td>
            <small class="text-nowrap <?= $row->origem == "Automatico" ? "fw-bold": "fw-semibold" ?>">
                <?= $row->origem ?>
            </small>
        </td>
        <!-- OPERADOR END -->

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