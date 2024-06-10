<?php
@session_start();

#faz conexão com banco de dados
require('../classes/Database.class.php');
require('../classes/Atendente.class.php');
require('../classes/Consulta.class.php');

$db = new Database;

$ses_id = session_id();


$sql = "";
$sql .= " SELECT leilao_1, leilao_2";
$sql .= " FROM atendentes";
$sql .= " WHERE id_acesso ='" . $ses_id . "'";
$sql .= " AND CodAtendente =" . $_SESSION["MSId"];
//print "<br>".$sql; exit;
$db->query = $sql;
$r = $db->selectOne();

$id_leilao1 = $r->leilao_1;

$id_leilao2 = $r->leilao_2;


$sql = "";
$sql .= " SELECT *";
$sql .= " FROM consultas";
$sql .= " WHERE Codigo =" . $_GET["Consulta"];
$db->query = $sql;
$wr = $db->selectOne();

$id_motivo = $wr->Motivo;

$id_UF = $wr->UF;
$rC_Data = $wr->Data;
$rC_ItemConsultado = $wr->ItemConsultado;

$hoje = time();
$data = strtotime($rC_Data);
$diferenca = $hoje - $data;
$valor_dia = floor($diferenca / (60 * 60 * 24));



if ($id_motivo == 3) { ?>

    <script>
        $("#salvo").css("display", "block");
    </script>

<?php }


$sql = "";
$sql .= " SELECT *";
$sql .= " FROM resultado_consultas";
$sql .= " WHERE CodigoResu=" . $_GET["Consulta"];
$db->query = $sql;
$r = $db->selectOne();

if (!$r) {

    $s = "";
    $s .= " INSERT INTO resultado_consultas (";
    $s .= " CodigoResu";
    $s .= ",TipoConsulta";
    $s .= ") VALUES (";
    $s .= "" . $_GET["Consulta"];
    $s .= "," . $_GET["TIPO"];
    $s .= ")";
    $db->query = $s;
    $db->insert();
}

$v_consulta = $_GET["Consulta"];

#se for edicao ele nao executa nada de baixo e ja carrega os dados da consulta

if ($_GET["Edita"] != "Sim") {


    #verifica se já existe um atendente fazendo esta consulta, se sim abre a próxima consulta
    $verifica = "SELECT * FROM consultas WHERE CodAtendente NOT IN(0, {$_SESSION['MSId']}) AND Data='" . date("Y-m-d") . "' AND Codigo=" . $_GET["Consulta"];
    $db->query = $verifica;
    $verifica = $db->selectOne();

    if ($verifica) {

        //echo "Já existe alguem respondendo esta consulta para o cliente!<BR><BR>";

        $proxConsultaRAND = "SELECT * FROM consultas WHERE CodAtendente IN(0, {$_SESSION['MSId']}) AND Motivo=1 AND Codigo= $v_consulta  AND Data='" . date("Y-m-d") . "' ORDER BY RAND()";
        $db->query = $proxConsultaRAND;
        $pCr = $db->selectOne();

        if (!$pCr) {

            $SemConsulta = "Sim";
        } else {

            $ProxConsulta = "Sim";
            $_GET["Consulta"] = $pCr->Codigo;
        }
    }

    if ($SemConsulta <> "Sim") {

        #muda status para processando consulta
        $up = "UPDATE consultas SET CodAtendente =  '" . $_SESSION["MSId"] . "' WHERE Codigo=" . $_GET["Consulta"];
        $db->query = $up;
        $db->update();


        #seleciona no banco de dados os dados
        $q = "SELECT * FROM resultado_consultas WHERE CodigoResu=" . $_GET["Consulta"];
        $db->query = $q;
        $r = $db->selectOne();
    }
} else {

    if(!isset($Envia)) $Envia = array();

    #seleciona no banco de dados os dados
    $q = "SELECT * FROM resultado_consultas WHERE CodigoResu=" . $_GET["Consulta"];
    $db->query = $q;
    $r = $db->selectOne();

    $gambiarra = explode("UF/PLACA", $r->Bin);
    $gambiarraFinal = explode("MUNICIPIO", $gambiarra[1]);
    $Envia["placa"] = $gambiarraFinal[0];
    $Envia["placa"] = (trim($Envia["placa"]) == "") ? $MPNC : $Envia["placa"];
    $Envia["placa"] = str_replace(".", "", $Envia["placa"]);
    $Envia["placa"] = str_replace(":", "", $Envia["placa"]);
    $Envia["placa"] = substr($Envia["placa"], 3 - strlen($Envia["placa"]));
    $Envia["placa"] = str_replace(" ", "", $Envia["placa"]);


    $gambiarra = explode("CHASSI/VIN", $r["Bin"]);
    $gambiarraFinal = explode("UF/PLACA", $gambiarra[1]);
    $Envia["CHASSI"] = $gambiarraFinal[0];
    $Envia["CHASSI"] = str_replace(".", "", $Envia["CHASSI"]);
    $Envia["CHASSI"] = str_replace(":", "", $Envia["CHASSI"]);
    $Envia["CHASSI"] = trim($Envia["CHASSI"]);


    $gambiarra = explode("RENAVAM", $r["Bin"]);
    $gambiarraFinal = explode("MARCA", $gambiarra[1]);
    $Envia["renavam"] = $gambiarraFinal[0];
    $Envia["renavam"] = (trim($Envia["renavam"]) == "") ? $MPNC : $Envia["renavam"];
    $Envia["renavam"] = str_replace(".", "", $Envia["renavam"]);
    $Envia["renavam"] = str_replace(":", "", $Envia["renavam"]);
    $Envia["renavam"] = str_replace(" ", "", $Envia["renavam"]);
    $gambiarra = explode("UF/PLACA", $r["Bin"]);
    $gambiarraFinal = explode("MUNICIPIO", $gambiarra[1]);
    $Envia["vuf"] = $gambiarraFinal[0];
    $iwposvw = substr($Envia["vuf"], $iwposw + 0, 5);
    $iUF     = substr($iwposvw, 2 - strlen($iwposvw));
    $iUF = str_replace(" ", "", $iUF);
}


// ### VAI VERIFICAR QUAIS CAMPOS DEVE CARREGAR, DE ACORDO COM O PERFIL DO CLIENTE
$sqlPERFIL = "SELECT * FROM clientes WHERE Codigo=" . $_GET["Cliente"];
$db->query = $sqlPERFIL;
$sqlPERFIL_Q = $db->selectOne();

?>

<script>
    $(document).ready(function() {

        $("#gravaconsulta").click(function(evento) {

            $("#gravaconsulta").css("display", "none");
            $("#salvando").css("display", "block");

            var bin = $('#bin').val();
            var Estadual = $('#Estadual').val();
            var renavam = $('#renavam').val();
            var decodificador = $('#decodificador').val();
            var ssp = $('#ssp').val();
            var serpro = $('#serpro').val();
            var HistoricoRF = $('#HistoricoRF').val();
            var rf = $('#rf').val();
            var gravame = $('#gravame').val();
            var XML = $('#XML').val();
            var recall = $('#recall').val();
            var lt = $('#lt').val();
            var leilao = $('#leilao').val();
            var leilao2 = $('#leilao2').val();
            var leilao3 = $('#leilao3').val();
            var sinistro = $('#sinistro').val();
            var sinistro2 = $('#sinistro2').val();
            var sinistro3 = $('#sinistro3').val();
            var pendencia = $('#pendencia').val();
            var info = $('#info').val();
            var proprietarios = $('#proprietarios').val();
            var renajud = $('#renajud').val();
            var Codigo = $('#Codigo').val();



            // passo por parametro as variaveis por post para a segunda pagina e retorno na
            //função (data)
            $.post("sql.php", {
                Exec: 'GravaConsulta',
                bin: bin,
                Estadual: Estadual,
                renavam: renavam,
                decodificador: decodificador,
                ssp: ssp,
                serpro: serpro,
                HistoricoRF: HistoricoRF,
                rf: rf,
                gravame: gravame,
                recall: recall,
                XML: XML,
                lt: lt,
                leilao: leilao,
                leilao2: leilao2,
                leilao3: leilao3,
                sinistro: sinistro,
                sinistro2: sinistro2,
                sinistro3: sinistro3,
                pendencia: pendencia,
                info: info,
                proprietarios: proprietarios,
                renajud: renajud,
                Codigo: Codigo
            }, function(data) {

                $("#salvando").css("display", "none");
                $("#gravaconsulta").css("display", "block");
                $("#salvo").css("display", "block");


            });

        });

        $("#liberaconsulta").click(function(evento) {

            var Codigo = $('#Codigo').val();

            $.post("sql.php", {
                Exec: 'LiberaConsulta',
                Codigo: Codigo
            }, function(data) {
                $("#TB_window").find("a").click();

                window.location.href = "home.php?Pagina=RelatorioDiario";
            });
        });
    });
</script>

<link href="_css/estilo_relatorio_diario.css" rel="stylesheet" type="text/css" />
<style>
    hmtl {
        margin: 0 auto;
    }


    #Dados {

        font-family: Arial;
        font-size: 11px;
        color: #646464;
        margin-top: -10px;

    }

    .info,
    .success,
    .warning,
    .error,
    .validation {
        border: 1px solid;
        margin: 10px 0px;
        padding: 15px 10px 15px 10px;
        background-repeat: no-repeat;
        background-position: 10px center;
        font-weight: bold;
        width: 90%
    }

    .info {
        color: #00529B;
        background-color: #BDE5F8;
    }

    .success {
        color: #4F8A10;
        background-color: #DFF2BF;
    }

    .warning {
        color: #9F6000;
        background-color: #FEEFB3;
    }

    .error {
        color: #D8000C;
        background-color: #FFBABA;
    }

    .linkR {
        font-family: TAhoma;
        font-size: 12px;
        color: #069;
        font-weight: bold;
    }
</style>
<table width="90%" border="0" cellpadding="2" cellspacing="0" style="margin-left: 42px; margin-top:-25px; font-family:verdana;color:white;font-size:11px;font-weight: bold">
    <tr>
        <td>
            <?php if ($SemConsulta == "Sim") { ?>
                <div id="SemConsulta" class="error" style="display:<?php if ($SemConsulta == "Sim") { ?>block<?php } else { ?>none<?php } ?>">
                    A consulta a qual você escolheu, já esta sendo respondida.<br />
                    Não existe outra consulta para ser realizada!
                </div>
            <?php } else { ?>

                <div id="ProxConsulta" class="warning" style="display:<?php if ($ProxConsulta == "Sim") { ?>block<?php } else { ?>none<?php } ?>">
                    A consulta a qual você escolheu, já esta sendo respondida.<br />
                    O sistema redirecionou para outra consulta!
                </div>

                <div id="Dados" style="font-family:arial;font-size:18px;"><input name="Codigo" type="hidden" id="Codigo" value="<?php echo $_GET["Consulta"]; ?>" />
                    <b>PLACA : </b>
                    <?php echo $_GET["ValorBusca"]; ?>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b> / CLIENTE : </b> <?php echo $_GET["Cliente"]; ?><br />
                    <hr style=" width:100%" />
                </div>

                <form action="" method="post">

                    <?php if ($_GET["TIPO"] == 10) { ?>
                        DPVAT - PAGO<br />
                        <a href="http://www.dpvatseguro.com.br/consulta-pagamento/default.aspx?TabContainer1_tab_renavam_customizado_renavam" target="_blank" class="linkR">[url] dpvat seguro</a><br />
                        <textarea name="proprietarios" id="proprietarios" style="width: 90%;height: 50px"><?php echo $r["Proprietarios"]; ?></textarea>
                    <?php } ?>


                    <?php if ($_GET["TIPO"] == 9) { ?>
                        GRAVAME<br />
                        <!--<a href="op_gravame_acsp.php?cliente=<?php echo $_GET["Cliente"]; ?>&status=chassi&campo=<?php echo $Envia["CHASSI"] ?>&consulta=<?php echo $_GET["Consulta"]; ?>&tipo=<?php echo $_GET["TIPO"]; ?>" target="_blank" class="linkR"></a>-->
                        <font color=red>[xml] GRAVAME AUTO</font><br>
                        <textarea name="gravame" id="gravame" style="width: 90%;height: 120px"><?php echo $r["Gravame"]; ?></textarea>
                    <?php } ?>
                    <?php

                    //print_r($sqlPERFIL_Q);

                    ?>


                    <?php if ($_GET["TIPO"] == 7 || $_GET["TIPO"] == 76 || $_GET["TIPO"] == 77 || $_GET["TIPO"] == 14 || $_GET["TIPO"] == 15 || $_GET["TIPO"] == 19 ||  $_GET["TIPO"] == 12 || $_GET["TIPO"] == 29) { ?>
                        <?php


                        $Doc = explode("<documento>", $r["Bin"]);
                        $Doc = explode("</documento>", $Doc[1]);
                        $Doc = $Doc[0];
                        $Doc = str_replace("-", "", $Doc);
                        $Doc = str_replace(".", "", $Doc);

                        if (strlen($Doc) == 11) {
                            $tipo = "F";
                        } else {

                            $tipo = "J";
                        }

                        if (strlen($Doc) == 11) {
                            $Doc = "000" . $Doc;
                        }

                        $chassi = explode("<chassi>", $r["Bin"]);
                        $chassi = explode("</chassi>", $chassi[1]);
                        $chassi = $chassi[0];
                        $chassi = str_replace("-", "", $chassi);
                        $chassi = str_replace(".", "", $chassi);
                        $chassi = str_replace(" ", "", $chassi);

                        ?>
                        <font color="#0000FF"><b>BIN ONLINE<b></font>$rC_ItemConsultado
                        <p>
                            <?php if ($rC_ItemConsultado != "motor") { ?>

                                <a href="cred_estadual.php?c=1535972356121190863100012001<?php echo $_GET["ValorBusca"]; ?>++++++++++++++X" target="_blank" class="linkR">[xml] AGREGADOS</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <a href="bin_operador_auto.php?cliente=<?php echo $_GET["Cliente"]; ?>&status=<?php echo $_GET["Item"]; ?>&campo=<?php echo $_GET["ValorBusca"]; ?>&consulta=<?php echo $_GET["Consulta"]; ?>&tipo=<?php echo $_GET["TIPO"]; ?>" target="_blank" class="linkR">[xml] BIN AUTO</a>&nbsp;&nbsp;&nbsp;
                                <a href="bin_operador.php?cliente=<?php echo $_GET["Cliente"]; ?>&status=<?php echo $_GET["Item"]; ?>&campo=<?php echo $_GET["ValorBusca"]; ?>&consulta=<?php echo $_GET["Consulta"]; ?>&tipo=<?php echo $_GET["TIPO"]; ?>" target="_blank" class="linkR">
                                    <font color=red>[xml] BIN TDI BACKP</font>
                                </a>&nbsp;&nbsp;&nbsp;

                            <?php } ?>
                            <textarea name="bin" id="bin" style="width: 100%;height: 200px"><?php echo $r["Bin"]; ?></textarea><BR /><BR />


                            <?php if ($_GET["TIPO"] == 7 || $_GET["TIPO"] == 77 || $_GET["TIPO"] == 19 || $_GET["TIPO"] == 76) { ?>

                                <font color="#0000FF"><b>BASE ESTADUAL<b></font><br />
                                <a href="op_estadual.php?cliente=<?php echo $_GET["Cliente"]; ?>&status=<?php echo $_GET["Item"]; ?>&campo=<?php echo trim($_GET["ValorBusca"]); ?>&consulta=<?php echo $_GET["Consulta"]; ?>&tipo=<?php echo $_GET["TIPO"]; ?>" target="_blank" class="linkR">
                                    <font color=red>[xml] Atualizar Estadual</font>
                                </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <a href="motor_estadual.php?cliente=<?php echo $_GET["Cliente"]; ?>&status=<?php echo $_GET["Item"]; ?>&campo=<?php echo trim($_GET["ValorBusca"]); ?>&consulta=<?php echo $_GET["Consulta"]; ?>&tipo=<?php echo $_GET["TIPO"]; ?>" target="_blank" class="linkR">
                                    <font color=red>[xml] VERIFICAR MOTOR </font>
                                </a>
                                <textarea name="Estadual" id="Estadual" style="width: 100%;height: 130px"><?php echo $r["Estadual"]; ?></textarea><BR />

                            <?php } ?>

                            <?php if ($_GET["TIPO"] == 7 || $_GET["TIPO"] == 77) { ?>
                        <p>
                            <font color="#0000FF"><b>DECODIFICAR + FIPE <b></font><br />
                            <textarea name="decodificador" id="decodificador" style="width: 100%;height: 130px"><?php echo $r["decodificador"]; ?></textarea><BR />

                        <?php } ?>


                        <?php if ($_GET["TIPO"] == 7 || $_GET["TIPO"] == 77) { ?>
                        <p>
                            <font color="#0000FF"><b>RENAJUD - DETALHADO <b></font><br />
                            <textarea name="renajud" id="renajud" style="width: 100%;height: 130px"><?php echo $r["renajud"]; ?></textarea><BR />

                        <?php } ?>


                        <div id="salvo" name="salvo" style="display: block;">

                            <?php if ($sqlPERFIL_Q["proprietarios"] == "S") {

                                if ($_GET["TIPO"] == "22") {

                            ?>

                                    <BR />
                                    <BR />
                                    <font color=red><b>PROPRIETARIO DO VEICULO<b></font>  <br />
                                    <a href="http://www.receita.fazenda.gov.br/Aplicacoes/ATCTA/cpf/ConsultaPublica.asp" target="_blank" class="linkR">[X] Receita Federal</a>
                                    <textarea name="proprietarios" id="proprietarios" style="width: 90%;height: 50px"><?php echo $r["Proprietarios"]; ?></textarea>
                            <?php
                                }
                            } ?>

                            <?php if ($_GET["TIPO"] != "12") { ?>

                                <!--<br />
<font color="#0000FF"><b>ROUBOS E FURTOS</b></font><br />
<a href="http://celepar7.pr.gov.br/policiacivil/furto.asp" target="_blank" class="linkR">[url] polícia civil</a><br />

<?php if ($_GET["TIPO"] == "7" || $_GET["TIPO"] == "76" || $_GET["TIPO"] == "14" || $_GET["TIPO"] == "19") { ?>
  <textarea name="HistoricoRF" id="HistoricoRF" style="width: 90%;height: 30px" ><?php echo $r["HistoricoRF"]; ?></textarea><BR>
<?php } ?>

<select name="rf" id="rf" style="width: 90%;">
  <option value="0" <?php if ($r["RF"] == 0) { ?>selected="selected"<?php } ?>>NADA CONSTA NOS ARQUIVOS DA DFRV</option>
  <option value="1" <?php if ($r["RF"] == 1) { ?>selected="selected"<?php } ?>>CONSTA NOS ARQUIVOS DA DFRV</option>
</select>
<BR />

<font color="#0000FF"><b>RENAVAM<b></font><br />
<a href="http://www.dpvatseguro.com.br/consulta-pagamento/default.aspx?TabContainer1_tab_renavam_customizado_renavam" target="_blank" class="linkR">[url] dpvat seguro</a><br />
<input name="renavam" type="text" id="renavam" style="width: 90%;" value="<?php echo $r["renavam"]; ?>" maxlength="19" />
<br />-->

                            <?php } ?>


                        <?php } elseif ($sqlPERFIL_Q["especial_Bin_Est"] == "S" && $_GET["TIPO"] == 7 || $_GET["TIPO"] == 77 || $_GET["TIPO"] == 76 || $_GET["TIPO"] == 15 && $sqlPERFIL_Q["basebin"] == "S"  || $_GET["TIPO"] == 19) { ?>
                            BIN ONLINE<br />
                            <a href="cred_estadual.php?c=1535972356121190863100012001<?php echo $_GET["ValorBusca"]; ?>++++++++++++++X" target="_blank" class="linkR">[xml] Agregados</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <a href="bin_operador_auto.php?cliente=<?php echo $_GET["Cliente"]; ?>&status=<?php echo $_GET["Item"]; ?>&campo=<?php echo $_GET["ValorBusca"]; ?>&consulta=<?php echo $_GET["Consulta"]; ?>&tipo=<?php echo $_GET["TIPO"]; ?>" target="_blank" class="linkR">[xml] bin AUTO</a>&nbsp;&nbsp;&nbsp;
                            <textarea name="bin" id="bin" style="width: 90%;height: 130px"><?php echo $r["Bin"]; ?></textarea>





                        <?php } ?>

                        <BR>

                        <?php
                        # SE CONSULTA FOR 7, OU SEJA ESPECIAL, ABRE TODOS CAMPOS, CASO NAO SEJA ABRE SO BIN

                        $BINNN = "SELECT TipoConsulta FROM consultas WHERE Codigo=" . $_GET["Consulta"];
                        $db->query = $BINNN;
                        $B_Q = $db->selectOne();

                        ?>

                        <?php if ($sqlPERFIL_Q["Venda"] == "S"  && $_GET["TIPO"] == 7 || $_GET["TIPO"] == 76) { ?>
                            <BR />
                            <font color="#228B22"><b>COMUNICADO / VENDA</b></font>
                            <br />
                            <a href="https://denatran.serpro.gov.br/certificado/veiculo.asp" target="_blank" class="linkR">[url] Denatran</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <textarea name="XML" id="XML" style="width: 90%;height: 50px"><?php echo $r["XML"]; ?></textarea><BR />



                            <BR />
                            <font color="#228B22"><b>RECALL / DETALHADO</b></font>
                            <br />
                            <a href="https://recall.serpro.gov.br/" target="_blank" class="linkR">[url] RECALL</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <textarea name="recall" id="recall" style="width: 100%;height: 70px"><?php echo $r["recall"]; ?></textarea><BR />


                        <?php  } ?>

                        <?php if ($_GET["TIPO"] == "18") { ?>

                            <textarea name="XML" id="XML" style="width: 100%;height: 100px"><?php echo $r["XML"]; ?></textarea><BR><BR>

                        <?php  } ?>


                        <?php if ($B_Q["TipoConsulta"] == "78") { ?>

                            <textarea name="XML" id="XML" style="width: 100%;height: 100px"><?php echo $r["XML"]; ?></textarea><BR><BR>

                        <?php  } ?>


                        <?php if ($B_Q["TipoConsulta"] == "77") { ?>

                            <BR />
                            <font color="#228B22"><b>RECALL / DETALHADO</b></font>
                            <br />
                            <a href="https://recall.serpro.gov.br/" target="_blank" class="linkR">[url] RECALL</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <textarea name="recall" id="recall" style="width: 100%;height: 70px"><?php echo $r["recall"]; ?></textarea><BR />
                            <BR />
                            <font color="#228B22"><b>LOCALIZA</b></font>
                            <br />
                            <textarea name="XML" id="XML" style="width: 100%;height: 100px"><?php echo $r["XML"]; ?></textarea><BR><BR>

                        <?php  } ?>



                        <?php if ($B_Q["TipoConsulta"] == "7" || $B_Q["TipoConsulta"] == "77" || $B_Q["TipoConsulta"] == "76" || $_GET["TIPO"] == 19 || $_GET["TIPO"] == 29) { ?>


                            <BR />
                            SSP / DETRAN<br />
                            <a href='op_detran.php?Codigo=<?php echo $_GET["Consulta"]; ?>' target="_blank" class="linkR">SSP - SP
                            </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.extratodebito.detran.pr.gov.br/detranextratos/geraExtrato.do?action=emiteRelatorio" target="_blank" class="linkR">SSP - PR</a>
                            </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www.detran.rj.gov.br/_monta_aplicacoes.asp?cod=16&tipo=consulta_multa" target="_blank" class="linkR">SSP - RJ</a>
                            </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://www.detran.mg.gov.br/veiculos/situacao-do-veiculo/consulta-a-situacao-do-veiculo" target="_blank" class="linkR">SSP - MG</a>
                            </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="http://www2.detran.goias.gov.br/pagina/ver/9104/consulta-de-multas" target="_blank" class="linkR">SSP - GO</a>
                            </a>&nbsp;&nbsp;&nbsp;<a href="http://www.detran.ba.gov.br/web/guest/consultar-situacao-do-veiculo" target="_blank" class="linkR">SSP - BA</a><br>
                            <textarea name="ssp" id="ssp" style="width: 100%;height: 100px"><?php echo $r["SSP"]; ?></textarea><BR><BR>

                            <?php if ($sqlPERFIL_Q["especial_restricao"] == "S") { ?>

                                <?php if ($sqlPERFIL_Q["total_restricao"] == "N") { ?>

                                    RESTRICÕES<br />
                                    <a href="https://www.redecredauto.com.br/webservice/homologa/Prod/sys_basebin/ssp_send.php?cliente=<?php echo $_GET["Cliente"]; ?>&status=renavam&campo=<?php $renavam = explode("<renavam>", $r["Bin"]);
                                                                                                                                                                                            $renavamFinal = explode("</renavam>", $renavam[1]);
                                                                                                                                                                                            echo $renavamFinal[0]; ?>" target="_blank" class="linkR">BASE - ESTADUAL
                                    </a>
                                    <textarea name="serpro" id="serpro" style="width: 90%;height: 50px"><?php echo $r["serpro"]; ?></textarea><BR>

                                <?php  } ?>

                            <?php  } ?>

                            <?php if ($sqlPERFIL_Q["especial_restricao"] == "N") { ?>

                                <?php if ($sqlPERFIL_Q["total_restricao"] == "S") { ?>

                                    RESTRICÕES<br />
                                    <a href="https://www.redecredauto.com.br/webservice/homologa/Prod/sys_basebin/ssp_send.php?cliente=<?php echo $_GET["Cliente"]; ?>&status=renavam&campo=<?php $renavam = explode("<renavam>", $r["Bin"]);
                                                                                                                                                                                            $renavamFinal = explode("</renavam>", $renavam[1]);
                                                                                                                                                                                            echo $renavamFinal[0]; ?>" target="_blank" class="linkR">BASE - ESTADUAL
                                    </a>
                                    <textarea name="serpro" id="serpro" style="width: 90%;height: 50px"><?php echo $r["serpro"]; ?></textarea><BR>

                                <?php  } ?>

                            <?php  } ?>


                            <?php if ($sqlPERFIL_Q["especial_restricao"] == "S") { ?>

                                <?php if ($sqlPERFIL_Q["total_restricao"] == "S") { ?>

                                    RESTRICÕES<br />
                                    <a href="https://www.redecredauto.com.br/webservice/homologa/Prod/sys_basebin/ssp_send.php?cliente=<?php echo $_GET["Cliente"]; ?>&status=renavam&campo=<?php $renavam = explode("<renavam>", $r["Bin"]);
                                                                                                                                                                                            $renavamFinal = explode("</renavam>", $renavam[1]);
                                                                                                                                                                                            echo $renavamFinal[0]; ?>" target="_blank" class="linkR">BASE - ESTADUAL
                                    </a>
                                    <textarea name="serpro" id="serpro" style="width: 90%;height: 50px"><?php echo $r["serpro"]; ?></textarea><BR>

                                <?php  } ?>

                            <?php  } ?>


                            <?php if (($sqlPERFIL_Q["especial_Gravame"] == "S"  && $_GET["TIPO"] == 7 && $id_UF != "SP") || ($_GET["TIPO"] == 77) || ($_GET["TIPO"] == 19 && $id_UF != "SP")) { ?>
                                <BR />
                                GRAVAME<br />
                                <!--<a href="op_gravame_acsp.php?cliente=<?php echo $_GET["Cliente"]; ?>&status=chassi&campo=<?php echo $Envia["CHASSI"] ?>&consulta=<?php echo $_GET["Consulta"]; ?>&tipo=<?php echo $_GET["TIPO"]; ?>" target="_blank" class="linkR"></a>-->
                                <font color=red>[xml] GRAVAME AUTO</font><br>
                                <textarea name="gravame" id="gravame" style="width: 100%;height: 120px"><?php echo $r["Gravame"]; ?></textarea><BR />
                            <?php  } else if ($_GET["TIPO"] != 9) { ?>
                                <textarea name="gravame" id="gravame" style="display: none;"><?php echo $r["Gravame"]; ?></textarea>
                            <?php } ?>

                            <BR />
                            <a href="operador_sendnort_leilao.php?c=1235972356121190863100012001<?php echo trim($_GET["ValorBusca"]); ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" class="linkR">
                                <font color="#228B22"><b>LEILAO AUTO RISCO</b></font>
                            </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                            <?php if ($id_leilao2 == 1) { ?>
                                <a href="operador_abs_leilao.php?c=1235972356121190863100012001<?php echo trim($_GET["ValorBusca"]); ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" class="linkR">
                                    <font color="#e60000"><b>LEILAO NORTIX - DIRETO</b></font>
                                </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php } ?>

                            <?php if ($id_leilao2 == 1) { ?>
                                <a href="operador_abs_varejo.php?c=1235972356121190863100012001<?php echo trim($_GET["ValorBusca"]); ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" class="linkR">
                                    <font color="#e60000"><b>LEILAO ABS - VAREJO</b></font>
                                </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php } ?>

                            <a href="operador_info_leilao.php?c=1235972356121190863100012001<?php echo trim($_GET["ValorBusca"]); ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" class="linkR">
                                <font color="#228B22"><b>LEILAO INFOCAR - DIRETO</b></font>
                            </a>

                            <textarea name="leilao3" id="leilao3" style="width: 100%;height: 160px"><?php echo $r["Leilao3"]; ?></textarea><BR />

                            <BR />
                            <a href="credauto_leilao_base.php?c=1235972356121190863100012001<?php echo trim($_GET["ValorBusca"]); ?>++++++++++++++X" target="_blank" class="linkR">LEILAO BASE CRED AUTO</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <textarea name="leilao2" id="leilao2" style="width: 100%;height: 160px"><?php echo $r["Leilao2"]; ?></textarea><BR />

                            <?php if ($_GET["TIPO"] == 7 || $_GET["TIPO"] == 77 || $_GET["TIPO"] == 76 || $_GET["TIPO"] == 15 || $_GET["TIPO"] == 19) { ?>
                                <BR />
                                <font color=navy><b>PROPRIETÁRIOS ANTERIORES</b></font> <br />
                                <textarea name="proprietarios" id="proprietarios" style="width: 100%;height: 130px"><?php echo $r["Proprietarios"]; ?></textarea>
                                <br />


                            <?php } ?>

                            <?php if ($_GET["TIPO"] == 7 || $_GET["TIPO"] == 77 || $_GET["TIPO"] == 76 || $_GET["TIPO"] == 19 || $_GET["TIPO"] == 29) { ?>

                                <BR />
                                <font color=navy><b>SINISTRO</b></font> <br />
                                <a href="operador_sinistro.php?c=11xjx5i0fx3w1190863100012001<?php echo $_GET["ValorBusca"]; ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" class="linkR">[xml] Sinistro ACSP</a>
                                <br>
                                <select name="sinistro" id="sinistro" style="width: 60%;">
                                    <option value="" <?php if ($r["Sinistro"] == "") { ?>selected="selected" <?php } ?>>Selecionar</option>
                                    <option value="0" <?php if ($r["Sinistro"] == "0") { ?>selected="selected" <?php } ?>>Não existe sinistro de Indenização Integral.</option>
                                    <option value="1" <?php if ($r["Sinistro"] == "1") { ?>selected="selected" <?php } ?>>Encontrado sinistro de Indenização Integral.</option>
                                    <option value="2" <?php if ($r["Sinistro"] == "2") { ?>selected="selected" <?php } ?>>SISTEMA INDISPONIVEL NO MOMENTO</option>
                                </select>
                                <?php
                                if ($r["Sinistro2"] == "") { ?>
                                    &nbsp;&nbsp;<font color="#363636"><b>DATA</b></font> &nbsp;&nbsp;<textarea name="sinistro2" id="sinistro2" style="width: 23%;height: 18px"></textarea><BR />
                                <?php  } ?>

                                <?php
                                if ($r["Sinistro"] == "0") { ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;<b>Sinistro Automatico Realizado</b><BR />
                                <?php  } ?>


                                <?php
                                if ($r["Sinistro2"] != "") { ?>
                                    <a>
                                        <font color=red size=2> Em <?php echo $r["Sinistro2"]; ?></font>
                                    </a>
                                <?php } ?>

                                <?php if ($_GET["TIPO"] == 7 || $_GET["TIPO"] == 29 || $_GET["TIPO"] == 77 || $_GET["TIPO"] == 76 || $_GET["TIPO"] == 19) { ?>

                                    <BR />
                                    <font color=red><b>INDICIO DE SINISTRO</b></font>
                                    <br>
                                    <select name="sinistro3" id="sinistro3" style="width: 60%;">
                                        <option value="" <?php if ($r["Sinistro"] == "") { ?>selected="selected" <?php } ?>>Selecionar</option>
                                        <option value="0" <?php if ($r["Sinistro3"] == "0") { ?>selected="selected" <?php } ?>>Não existe indicio de sinistro.</option>
                                        <option value="1" <?php if ($r["Sinistro3"] == "1") { ?>selected="selected" <?php } ?>>Encontrado indicio de sinistro.</option>
                                        <option value="2" <?php if ($r["Sinistro3"] == "2") { ?>selected="selected" <?php } ?>>SISTEMA INDISPONIVEL NO MOMENTO</option>
                                    </select>
                                    <BR />

                                <?php } ?>


                            <?php } ?>

                        <?php } ?>

                    <?php } ?>
                    <BR />
                    <?php if ($B_Q["TipoConsulta"] == "12") { ?>


                        <?php if ($sqlPERFIL_Q["Leilao"] == "S") { ?>
                            <BR />
                            <a href="operador_sendnort_leilao.php?c=1235972356121190863100012001<?php echo $_GET["ValorBusca"]; ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" class="linkR">
                                <font color="#228B22"><b>LEILAO AUTO RISCO</b></font>
                            </a>nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

                            <?php if ($id_leilao2 == 1) { ?>
                                <a href="operador_abs_leilao.php?c=1235972356121190863100012001<?php echo $_GET["ValorBusca"]; ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" class="linkR">
                                    <font color="#e60000"><b>LEILAO NORTIX - DIRETO</b></font>
                                </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php } ?>

                            <?php if ($id_leilao2 == 1) { ?>
                                <a href="operador_abs_varejo.php?c=1235972356121190863100012001<?php echo $_GET["ValorBusca"]; ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" class="linkR">
                                    <font color="#e60000"><b>LEILAO ABS - VAREJO</b></font>
                                </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <?php } ?>

                            <a href="operador_info_leilao.php?c=1235972356121190863100012001<?php echo $_GET["ValorBusca"]; ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" class="linkR">
                                <font color="#228B22"><b>LEILAO INFOCAR - DIRETO</b></font>
                            </a>


                            <textarea name="leilao3" id="leilao3" style="width: 100%;height: 160px"><?php echo $r["Leilao3"]; ?></textarea><BR />
                            <BR />
                            <a href="credauto_leilao_base.php?c=1235972356121190863100012001<?php echo $_GET["ValorBusca"]; ?>++++++++++++++X" target="_blank" class="linkR">LEILAO BASE CRED AUTO</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <textarea name="leilao2" id="leilao2" style="width: 100%;height: 160px"><?php echo $r["Leilao2"]; ?></textarea><BR />

                            <BR />SINISTRO <br />
                            <a href="operador_sinistro.php?c=11xjx5i0fx3w1190863100012001<?php echo $_GET["ValorBusca"]; ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" class="linkR">[xml] Sinistro ACSP</a>
                            <br>
                            <select name="sinistro" id="sinistro" style="width: 90%;">
                                <option value="0" <?php if ($r["Sinistro"] == "0") { ?>selected="selected" <?php } ?>>Não existe sinistro de Indenização Integral.</option>
                                <option value="1" <?php if ($r["Sinistro"] == "1") { ?>selected="selected" <?php } ?>>Encontrado sinistro de Indenização Integral.</option>
                                <option value="2" <?php if ($r["Sinistro"] == "2") { ?>selected="selected" <?php } ?>>SISTEMA INDISPONIVEL NO MOMENTO</option>
                            </select><BR />

                            <!--<br /><font color=red ><b>BACKP - ( somente deve ser utilizado se o NORTIX estiver fora do ar )</b></font><br>
  <a href="operador_leilao1.php?cliente=<?php echo $_GET["Cliente"]; ?>&placa=<?php echo $_GET["ValorBusca"]; ?>&consulta=<?php echo $_GET["Consulta"]; ?>&tipo=<?php echo $_GET["TIPO"]; ?>" target="_blank" class="linkR">[xml] leilao  TDI</a><br>
  <textarea name="leilao" id="leilao" style="width: 90%;height: 30px" ><?php echo $r["Leilao"]; ?></textarea><BR />-->


                        <?php  } ?>


                    <?php } ?>

                    <?php

                    //- ATUALIZA CONSULTA -----------------------------------------------

                    if ($valor_dia < 6) { ?>


                        <br />
    <tr>
        <td align="center"><input name="gravaconsulta" type="button" class="botao" id="gravaconsulta" value="Salvar Dados da Consulta" style="cursor: pointer; width:50%; padding: 5px; background-color: #4DAA0A; color: #fff;" />
            <div id="salvando" name="salvando" style="display: none; width:50%; text-align: center; color: #000000;">Salvando Consulta...</div>
            <br /><br />
    </tr>
    </td>
    <tr>
        <td align="center"><a href="rpc/inc_consulta_normalizada.php?Codigo=<?php echo $_GET["Consulta"]; ?>&print=1&Tipo=7" target="_blank">
                <div style=" text-align: center; padding: 5px; width:49%; background-color: #FF8C00; color: #fff;">Pré-Visualizar Consulta</div>
            </a><br />
    </tr>
    </td>
    <tr>
        <td align="center"><input name="liberaconsulta" type="button" class="botao" id="liberaconsulta" value="Fechar e Liberar para o Cliente" style="cursor: pointer; width:50%; padding: 5px; background-color: #CD0000; color: #fff;" /><br /><br />
    </tr>
    </td>
    <br />
    <br />
    <br />
    </form>
    </div>
    </td>
    </tr>
</table>

<?php } ?>