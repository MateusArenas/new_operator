<?php
@session_start();
$ses_id = session_id();

@include_once('../config.php');
require('../classes/Database.class.php');
require('../classes/Atendente.class.php');
require('../classes/Consulta.class.php');

function formatXMLToEditor($xml)
{
  if (!$xml) return "";
  $dom = new \DOMDocument('1.0');
  $dom->preserveWhiteSpace = true;
  $dom->formatOutput = true;
  $dom->loadXML($xml);
  $xml_pretty = $dom->saveXML();
  // $xml_pretty = str_replace('<', '&lt;', $xml_pretty);
  // $xml_pretty = str_replace('/>', '&gt;', $xml_pretty);
  return $xml_pretty;
}

$db = new Database;

$Atendente = new Atendente();
$Consulta = new Consulta();

$response = new stdClass();

//code...
$id = @$_SESSION["MSId"];
$codConsulta = @$_REQUEST['Consulta'];

try {
  if (!isset($_SESSION['MSId'])) throw new Exception('ID do Atendente não localizado');

  $atendente = $Atendente->findById($id);

  if (!$atendente) throw new Exception('Atendente não localizado');

  $username = $atendente->LoginAtendente ?: $atendente->NomeAtendente;

  if (@$atendente->slack_id) {
    $atendente_link = "https://redecredautogroup.slack.com/team/$atendente->slack_id";
  } else if (@$atendente->email) {
    $atendente_link = "mailto:$atendente->email";
  }

  $consulta = $Consulta->findByCodigo($codConsulta);

  if (!$consulta) throw new Exception('Consulta não localizada');

  $consultaLink = "https://www.credoperador.com.br/rpc/inc_consulta_normalizada.php?Codigo=" . $consulta->codigo . "&print=1&Tipo=" . $consulta->codTipo;

  $db->query = "SELECT leilao_1, leilao_2 FROM atendentes WHERE id_acesso = ? AND CodAtendente = ? ";
  $db->content = [[$ses_id], [$id]];
  $r = $db->selectOne();

  // caso não retornar o $r é porque está salvo no banco o session_id do outro sistema

  $id_leilao1 = @$r->leilao_1;
  $id_leilao2 = @$r->leilao_2;

  $db->query = "SELECT * FROM consultas WHERE Codigo = ? ";
  $db->content = [[$codConsulta]];
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

    <!-- <script>
      $("#salvo").css("display", "block");
    </script> -->

<?php }


  $db->query = "SELECT * FROM resultado_consultas WHERE CodigoResu = ? ";
  $db->content = [[$codConsulta]];
  $r = $db->selectOne();

  if (!$r) {
    $db->query = "INSERT INTO resultado_consultas (CodigoResu, TipoConsulta) VALUES (?, ?) ";
    $db->content = [[$codConsulta], [$_GET["TIPO"]]];
    $db->insert();
  }

  $v_consulta = $_GET["Consulta"];

  #se for edicao ele nao executa nada de baixo e ja carrega os dados da consulta

  if ($_GET["Edita"] != "Sim") {

    #verifica se já existe um atendente fazendo esta consulta, se sim abre a próxima consulta
    $db->query = "SELECT * FROM consultas WHERE CodAtendente NOT IN(0, ?) AND Data = ? AND Codigo = ? ";
    $db->content = [[$_SESSION['MSId']], [date("Y-m-d")], [$codConsulta]];
    $verifica = $db->selectOne();

    if ($verifica) {
      //echo "Já existe alguem respondendo esta consulta para o cliente!<BR><BR>";
      $db->query = "SELECT * FROM consultas WHERE CodAtendente IN(0, ?) AND Motivo = 1 AND Codigo = ?  AND Data = ? ORDER BY RAND() ";
      $db->content = [[$_SESSION['MSId']], [$v_consulta], [date("Y-m-d")]];
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
      $$db->query = "UPDATE consultas SET CodAtendente = ? WHERE Codigo = ? ";
      $db->content = [[$_SESSION['MSId']], [$_GET["Consulta"]]];
      $db->update();

      #seleciona no banco de dados os dados
      $db->query = "SELECT * FROM resultado_consultas WHERE CodigoResu = ? ";
      $db->content = [[$_GET["Consulta"]]];
      $r = $db->selectOne();
    }
  } else {

    #seleciona no banco de dados os dados
    $db->query = "SELECT * FROM resultado_consultas WHERE CodigoResu = ? ";
    $db->content = [[$_GET["Consulta"], 'int']];
    $r = $db->selectOne();

    $gambiarra = explode("UF/PLACA", $r->Bin);
    $gambiarraFinal = explode("MUNICIPIO", @$gambiarra[1]);
    $Envia["placa"] = $gambiarraFinal[0];
    $Envia["placa"] = (trim($Envia["placa"]) == "") ? @$MPNC : $Envia["placa"];
    $Envia["placa"] = str_replace(".", "", $Envia["placa"]);
    $Envia["placa"] = str_replace(":", "", $Envia["placa"]);
    $Envia["placa"] = substr($Envia["placa"], 3 - strlen($Envia["placa"]));
    $Envia["placa"] = str_replace(" ", "", $Envia["placa"]);


    $gambiarra = explode("CHASSI/VIN", $r->Bin);
    $gambiarraFinal = explode("UF/PLACA", @$gambiarra[1]);
    $Envia["CHASSI"] = $gambiarraFinal[0];
    $Envia["CHASSI"] = str_replace(".", "", $Envia["CHASSI"]);
    $Envia["CHASSI"] = str_replace(":", "", $Envia["CHASSI"]);
    $Envia["CHASSI"] = trim($Envia["CHASSI"]);


    $gambiarra = explode("RENAVAM", $r->Bin);
    $gambiarraFinal = explode("MARCA", @$gambiarra[1]);
    $Envia["renavam"] = $gambiarraFinal[0];
    $Envia["renavam"] = (trim($Envia["renavam"]) == "") ? @$MPNC : $Envia["renavam"];
    $Envia["renavam"] = str_replace(".", "", $Envia["renavam"]);
    $Envia["renavam"] = str_replace(":", "", $Envia["renavam"]);
    $Envia["renavam"] = str_replace(" ", "", $Envia["renavam"]);

    $gambiarra = explode("UF/PLACA", $r->Bin);
    $gambiarraFinal = explode("MUNICIPIO", @$gambiarra[1]);
    $Envia["vuf"] = $gambiarraFinal[0];
    $iwposvw = substr($Envia["vuf"], @$iwposw + 0, 5);
    $iUF   = substr($iwposvw, 2 - strlen($iwposvw));
    $iUF = str_replace(" ", "", $iUF);
  }


  // ### VAI VERIFICAR QUAIS CAMPOS DEVE CARREGAR, DE ACORDO COM O PERFIL DO CLIENTE
  $db->query = "SELECT * FROM clientes WHERE Codigo = ? ";
  $db->content = [[$_GET["Cliente"]]];
  $sqlPERFIL_Q = $db->selectOne();
} catch (\Throwable $th) {
  $response->error = $th->getMessage();
}


?>
<!-- 
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
</script> -->


<!-- action="<?=$baseURL?>/actions/acao_enviar_pedido_alteracao.action.php" -->
<!-- action="<?=$baseURL?>/actions/editar_alterar.action.php"  -->

    <form class="modal-content border-0" 
      id="editar_alteracao" 
      data-form-type="ajax"
      data-form-target="#editar_alteracao_message"
      data-form-cleanup="true"
      action="<?=$baseURL?>/actions/gravar_consulta.action.php" 
      method="post"
    >


      <div class="modal-header">
        <h1 class="modal-title fs-5">
          <i class="bi bi-file-earmark-text-fill me-2"></i>Gravar ou editar consulta
        </h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

     

      <div class="modal-body position-relative p-0">

        <div id="editar_alteracao_message" class="d-flex flex-column sticky-top"></div>

        <div class="d-flex flex-column flex-grow-1 p-3">

          <?php if (@$response->error) : ?>

            <div class="alert alert-warning" role="alert">
              <?= $response->error ?>
            </div>

          <?php else : ?>

            <?php if (@$SemConsulta == "Sim") { ?>

              <div class="alert alert-warning" role="alert">
                A consulta a qual você escolheu, já esta sendo respondida.<br />
                Não existe outra consulta para ser realizada!
              </div>

            <?php } else { ?>

              <?php if (@$ProxConsulta == "Sim") : ?>
                <div class="alert alert-warning" role="alert">
                  A consulta a qual você escolheu, já esta sendo respondida.<br />
                  O sistema redirecionou para outra consulta!
                </div>
              <?php endif; ?>

              <div class="d-flex flex-column mb-3">
                  <small class="fw-semibold text-muted">
                    Tipo Consulta: <span class="fw-bold"><?= $consulta->tipo ?></span>
                  </small>
                  <small class="fw-semibold text-muted">
                    Código: <span class="fw-bold"><?= $consulta->codigo ?></span>
                  </small>
                  <small class="fw-semibold text-muted">
                    <span class="fw-semibold text-capitalize"><?= $consulta->item ?> / UF:</span> <?= $consulta->parametro ?> / <?= $consulta->uf ?>
                  </small>
                  <small class="fw-semibold text-muted">
                    Realizada em: <span class="fw-bold"><?= date('d/m/Y à\s H:i', strtotime($consulta->data)) ?></span>
                  </small>
              </div>


              <input name="Codigo" type="hidden" id="Codigo" value="<?= $_GET["Consulta"] ?>" />

              <?php if ($_GET["TIPO"] == 10) : ?>

                <div class="codedit col-12 mb-5">
                  <div class="d-flex flex-column py-2">
                    <h5 class="fs-5">DPVAT - PAGO</h5>
                    <div class="d-flex flex-row">
                      <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                        <a href="http://www.dpvatseguro.com.br/consulta-pagamento/default.aspx?TabContainer1_tab_renavam_customizado_renavam" target="_blank" style="font-size: 12px;">
                          [url] dpvat seguro
                        </a>
                      </div>
                    </div>
                  </div>
                  <div class="editor-holder border rounded">
                    <div class="scroller position-relative h-100">
                      <textarea name="proprietarios" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->Proprietarios) ?></textarea>
                      <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                          <code class="xml syntax-highight language-xml w-auto"></code>
                      </pre>
                    </div>
                  </div>
                </div>

              <?php endif; ?>


              <?php if ($_GET["TIPO"] == 9) : ?>

                <div class="codedit col-12 mb-5">
                  <div class="d-flex flex-column py-2">
                    <h5 class="fs-5">GRAVAME</h5>
                    <div class="d-flex flex-row">
                      <!-- <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="op_gravame_acsp.php?cliente=<?php echo $_GET["Cliente"]; ?>&status=chassi&campo=<?php echo $Envia["CHASSI"] ?>&consulta=<?php echo $_GET["Consulta"]; ?>&tipo=<?php echo $_GET["TIPO"]; ?>" target="_blank" style="font-size: 12px;">
                          </a>
                        </div> -->
                      <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                        <a href="#" style="font-size: 12px;">
                          [xml] GRAVAME AUTO
                        </a>
                      </div>
                    </div>
                  </div>
                  <div class="editor-holder border rounded">
                    <div class="scroller position-relative h-100">
                      <textarea name="gravame" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->Gravame) ?></textarea>
                      <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                          <code class="xml syntax-highight language-xml w-auto"></code>
                      </pre>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <?php if ($_GET["TIPO"] == 7 || $_GET["TIPO"] == 76 || $_GET["TIPO"] == 77 || $_GET["TIPO"] == 14 || $_GET["TIPO"] == 15 || $_GET["TIPO"] == 19 ||  $_GET["TIPO"] == 12 || $_GET["TIPO"] == 29) { ?>
                <?php

                $Doc = explode("<documento>", $r->Bin);
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

                $chassi = explode("<chassi>", $r->Bin);
                $chassi = explode("</chassi>", $chassi[1]);
                $chassi = $chassi[0];
                $chassi = str_replace("-", "", $chassi);
                $chassi = str_replace(".", "", $chassi);
                $chassi = str_replace(" ", "", $chassi);

                ?>

                <div class="codedit col-12 mb-5">
                  <div class="d-flex flex-column py-2">
                    <h5 class="fs-5">BIN ONLINE</h5>
                    <?php if ($rC_ItemConsultado != "motor") : ?>
                      <div class="d-flex flex-row">
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="cred_estadual.php?c=1535972356121190863100012001<?= $_GET["ValorBusca"] ?>++++++++++++++X" target="_blank" style="font-size: 12px;">
                            [xml] AGREGADOS
                          </a>
                        </div>
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="bin_operador_auto.php?cliente=<?= $_GET["Cliente"] ?>&status=<?= $_GET["Item"] ?>&campo=<?= $_GET["ValorBusca"] ?>&consulta=<?= $_GET["Consulta"] ?>&tipo=<?= $_GET["TIPO"] ?>" target="_blank" style="font-size: 12px;">
                            [xml] BIN AUTO
                          </a>
                        </div>
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="bin_operador.php?cliente=<?= $_GET["Cliente"] ?>&status=<?= $_GET["Item"] ?>&campo=<?= $_GET["ValorBusca"] ?>&consulta=<?= $_GET["Consulta"] ?>&tipo=<?= $_GET["TIPO"] ?>" target="_blank" style="font-size: 12px;">
                            [xml] BIN TDI BACKP
                          </a>
                        </div>
                      </div>
                    <?php endif; ?>

                  </div>
                  <div class="editor-holder border rounded">
                    <div class="scroller position-relative h-100">
                      <textarea name="bin" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->Bin) ?></textarea>
                      <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                          <code class="xml syntax-highight language-xml w-auto"></code>
                      </pre>
                    </div>
                  </div>
                </div>

                <?php if ($_GET["TIPO"] == 7 || $_GET["TIPO"] == 77 || $_GET["TIPO"] == 19 || $_GET["TIPO"] == 76) : ?>

                  <div class="codedit col-12 mb-5">
                    <div class="d-flex flex-column py-2">
                      <h5 class="fs-5">BASE ESTADUAL</h5>
                      <div class="d-flex flex-row">
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="op_estadual.php?cliente=<?= $_GET["Cliente"] ?>&status=<?= $_GET["Item"] ?>&campo=<?= trim($_GET["ValorBusca"]) ?>&consulta=<?= $_GET["Consulta"] ?>&tipo=<?= $_GET["TIPO"] ?>" target="_blank" style="font-size: 12px;">
                            [xml] Atualizar Estadual
                          </a>
                        </div>
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="motor_estadual.php?cliente=<?= $_GET["Cliente"] ?>&status=<?= $_GET["Item"] ?>&campo=<?= trim($_GET["ValorBusca"]) ?>&consulta=<?= $_GET["Consulta"] ?>&tipo=<?= $_GET["TIPO"] ?>" target="_blank" style="font-size: 12px;">
                            [xml] VERIFICAR MOTOR
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="editor-holder border rounded">
                      <div class="scroller position-relative h-100">
                        <textarea name="Estadual" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->Estadual) ?></textarea>
                        <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                              <code class="xml syntax-highight language-xml w-auto"></code>
                          </pre>
                      </div>
                    </div>
                  </div>

                <?php endif; ?>

                <?php if ($_GET["TIPO"] == 7 || $_GET["TIPO"] == 77) : ?>

                  <div class="codedit col-12 mb-5">
                    <div class="d-flex flex-column py-2">
                      <h5 class="fs-5">DECODIFICAR + FIPE</h5>
                    </div>
                    <div class="editor-holder border rounded">
                      <div class="scroller position-relative h-100">
                        <textarea name="decodificador" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->decodificador) ?></textarea>
                        <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                              <code class="xml syntax-highight language-xml w-auto"></code>
                          </pre>
                      </div>
                    </div>
                  </div>

                <?php endif; ?>


                <?php if ($_GET["TIPO"] == 7 || $_GET["TIPO"] == 77) : ?>

                  <div class="codedit col-12 mb-5">
                    <div class="d-flex flex-column py-2">
                      <h5 class="fs-5">RENAJUD - DETALHADO</h5>
                    </div>
                    <div class="editor-holder border rounded">
                      <div class="scroller position-relative h-100">
                        <textarea name="renajud" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->renajud) ?></textarea>
                        <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                            <code class="xml syntax-highight language-xml w-auto"></code>
                        </pre>
                      </div>
                    </div>
                  </div>

                <?php endif; ?>


                <div class="row" id="salvo" name="salvo" >  

                  <?php if (@$sqlPERFIL_Q->proprietarios == "S" && $_GET["TIPO"] == "22") :  ?>

                    <div class="codedit col-12 mb-5">
                      <div class="d-flex flex-column py-2">
                        <h5 class="fs-5">PROPRIETARIO DO VEICULO</h5>
                        <div class="d-flex flex-row">
                          <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                            <a href="http://www.receita.fazenda.gov.br/Aplicacoes/ATCTA/cpf/ConsultaPublica.asp" target="_blank" style="font-size: 12px;">
                              [X] Receita Federal
                            </a>
                          </div>
                        </div>
                      </div>
                      <div class="editor-holder border rounded">
                        <div class="scroller position-relative h-100">
                          <textarea name="proprietarios" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->Proprietarios) ?></textarea>
                          <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                                <code class="xml syntax-highight language-xml w-auto"></code>
                            </pre>
                        </div>
                      </div>
                    </div>

                  <?php endif; ?>

                  <?php if ($_GET["TIPO"] != "12") : ?>

                    <!--<br />
                      <font color="#0000FF"><b>ROUBOS E FURTOS</b></font><br />
                      <a href="http://celepar7.pr.gov.br/policiacivil/furto.asp" target="_blank" class="linkR">[url] polícia civil</a><br />

                      <?php if ($_GET["TIPO"] == "7" || $_GET["TIPO"] == "76" || $_GET["TIPO"] == "14" || $_GET["TIPO"] == "19") { ?>
                        <textarea name="HistoricoRF" id="HistoricoRF" style="width: 90%;height: 30px" ><?php echo $r->HistoricoRF; ?></textarea><BR>
                      <?php } ?>

                      <select name="rf" id="rf" style="width: 90%;">
                        <option value="0" <?php if ($r->RF == 0) { ?>selected="selected"<?php } ?>>NADA CONSTA NOS ARQUIVOS DA DFRV</option>
                        <option value="1" <?php if ($r->RF == 1) { ?>selected="selected"<?php } ?>>CONSTA NOS ARQUIVOS DA DFRV</option>
                      </select>
                      <BR />

                      <font color="#0000FF"><b>RENAVAM<b></font><br />
                      <a href="http://www.dpvatseguro.com.br/consulta-pagamento/default.aspx?TabContainer1_tab_renavam_customizado_renavam" target="_blank" class="linkR">[url] dpvat seguro</a><br />
                      <input name="renavam" type="text" id="renavam" style="width: 90%;" value="<?php echo $r->renavam; ?>" maxlength="19" />
                      <br />-->

                  <?php endif; ?>


                <?php } elseif (@$sqlPERFIL_Q->especial_Bin_Est == "S" && $_GET["TIPO"] == 7 || $_GET["TIPO"] == 77 || $_GET["TIPO"] == 76 || $_GET["TIPO"] == 15 && @$sqlPERFIL_Q->basebin == "S"  || $_GET["TIPO"] == 19) { ?>

                  <div class="codedit col-12 mb-5">
                    <div class="d-flex flex-column py-2">
                      <h5 class="fs-5">BIN ONLINE</h5>
                      <div class="d-flex flex-row">
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="cred_estadual.php?c=1535972356121190863100012001<?= $_GET["ValorBusca"] ?>++++++++++++++X" target="_blank" style="font-size: 12px;">
                            [xml] Agregados
                          </a>
                        </div>
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="bin_operador_auto.php?cliente=<?= $_GET["Cliente"] ?>&status=<?= $_GET["Item"] ?>&campo=<?= $_GET["ValorBusca"] ?>&consulta=<?= $_GET["Consulta"] ?>&tipo=<?= $_GET["TIPO"] ?>" target="_blank" style="font-size: 12px;">
                            [xml] bin AUTO
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="editor-holder border rounded">
                      <div class="scroller position-relative h-100">
                        <textarea name="bin" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->Bin) ?></textarea>
                        <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                            <code class="xml syntax-highight language-xml w-auto"></code>
                        </pre>
                      </div>
                    </div>
                  </div>

                <?php } ?>

                <?php
                # SE CONSULTA FOR 7, OU SEJA ESPECIAL, ABRE TODOS CAMPOS, CASO NAO SEJA ABRE SO BIN

                $BINNN = "SELECT TipoConsulta FROM consultas WHERE Codigo=" . $_GET["Consulta"];
                $db->query = $BINNN;
                $B_Q = $db->selectOne();

                ?>

                <?php if (@$sqlPERFIL_Q->Venda == "S"  && $_GET["TIPO"] == 7 || $_GET["TIPO"] == 76) : ?>

                  <div class="codedit col-12 mb-5">
                    <div class="d-flex flex-column py-2">
                      <h5 class="fs-5">COMUNICADO / VENDA</h5>
                      <div class="d-flex flex-row">
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="https://denatran.serpro.gov.br/certificado/veiculo.asp" target="_blank" style="font-size: 12px;">
                            [url] Denatran
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="editor-holder border rounded">
                      <div class="scroller position-relative h-100">
                        <textarea name="XML" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->XML) ?></textarea>
                        <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                            <code class="xml syntax-highight language-xml w-auto"></code>
                        </pre>
                      </div>
                    </div>
                  </div>

                  <div class="codedit col-12 mb-5">
                    <div class="d-flex flex-column py-2">
                      <h5 class="fs-5">RECALL / DETALHADO</h5>
                      <div class="d-flex flex-row">
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="https://recall.serpro.gov.br/" target="_blank" style="font-size: 12px;">
                            [url] RECALL
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="editor-holder border rounded">
                      <div class="scroller position-relative h-100">
                        <textarea name="recall" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->recall) ?></textarea>
                        <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                            <code class="xml syntax-highight language-xml w-auto"></code>
                        </pre>
                      </div>
                    </div>
                  </div>

                <?php endif; ?>

                <?php if ($_GET["TIPO"] == "18") : ?>

                  <div class="codedit col-12 mb-5">
                    <div class="editor-holder border rounded">
                      <div class="scroller position-relative h-100">
                        <textarea name="XML" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->XML) ?></textarea>
                        <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                            <code class="xml syntax-highight language-xml w-auto"></code>
                        </pre>
                      </div>
                    </div>
                  </div>

                <?php endif; ?>


                <?php if ($B_Q->TipoConsulta == "78") : ?>

                  <div class="codedit col-12 mb-5">
                    <div class="editor-holder border rounded">
                      <div class="scroller position-relative h-100">
                        <textarea name="XML" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->XML) ?></textarea>
                        <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                            <code class="xml syntax-highight language-xml w-auto"></code>
                        </pre>
                      </div>
                    </div>
                  </div>

                <?php endif; ?>


                <?php if ($B_Q->TipoConsulta == "77") : ?>

                  <div class="codedit col-12 mb-5">
                    <div class="d-flex flex-column py-2">
                      <h5 class="fs-5">RECALL / DETALHADO</h5>
                      <div class="d-flex flex-row">
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="https://recall.serpro.gov.br/" target="_blank" style="font-size: 12px;">
                            [url] RECALL
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="editor-holder border rounded">
                      <div class="scroller position-relative h-100">
                        <textarea name="recall" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->recall) ?></textarea>
                        <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                            <code class="xml syntax-highight language-xml w-auto"></code>
                        </pre>
                      </div>
                    </div>
                  </div>

                  <div class="codedit col-12 mb-5">
                    <div class="d-flex flex-column py-2">
                      <h5 class="fs-5">LOCALIZA</h5>
                    </div>
                    <div class="editor-holder border rounded">
                      <div class="scroller position-relative h-100">
                        <textarea name="XML" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->XML) ?></textarea>
                        <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                            <code class="xml syntax-highight language-xml w-auto"></code>
                        </pre>
                      </div>
                    </div>
                  </div>

                <?php endif; ?>


                <?php if ($B_Q->TipoConsulta == "7" || $B_Q->TipoConsulta == "77" || $B_Q->TipoConsulta == "76" || $_GET["TIPO"] == 19 || $_GET["TIPO"] == 29) { ?>

                  <div class="codedit col-12 mb-5">
                    <div class="d-flex flex-column py-2">
                      <h5 class="fs-5">SSP / DETRAN</h5>
                      <div class="d-flex flex-row">
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="op_detran.php?Codigo=<?= $_GET["Consulta"] ?>" target="_blank" style="font-size: 12px;">
                            SSP - SP
                          </a>
                        </div>
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="http://www.extratodebito.detran.pr.gov.br/detranextratos/geraExtrato.do?action=emiteRelatorio" target="_blank" style="font-size: 12px;">
                            SSP - PR
                          </a>
                        </div>
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="http://www.detran.rj.gov.br/_monta_aplicacoes.asp?cod=16&tipo=consulta_multa" target="_blank" style="font-size: 12px;">
                            SSP - RJ
                          </a>
                        </div>
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="https://www.detran.mg.gov.br/veiculos/situacao-do-veiculo/consulta-a-situacao-do-veiculo" target="_blank" style="font-size: 12px;">
                            SSP - MG
                          </a>
                        </div>
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="http://www2.detran.goias.gov.br/pagina/ver/9104/consulta-de-multas" target="_blank" style="font-size: 12px;">
                            SSP - GO
                          </a>
                        </div>
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="http://www.detran.ba.gov.br/web/guest/consultar-situacao-do-veiculo" target="_blank" style="font-size: 12px;">
                            SSP - BA
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="editor-holder border rounded">
                      <div class="scroller position-relative h-100">
                        <textarea name="ssp" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->SSP) ?></textarea>
                        <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                            <code class="xml syntax-highight language-xml w-auto"></code>
                        </pre>
                      </div>
                    </div>
                  </div>


                  <?php if (@$sqlPERFIL_Q->especial_restricao == "S" && @$sqlPERFIL_Q->total_restricao == "N") : ?>

                    <div class="codedit col-12 mb-5">
                      <div class="d-flex flex-column py-2">
                        <h5 class="fs-5">RESTRICÕES</h5>
                        <div class="d-flex flex-row">
                          <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                            <a href="https://webservice.redecredauto.com.br/sys_basebin/ssp_send.php?cliente=<?= $_GET["Cliente"] ?>&status=renavam&campo=<?php echo $renavam = explode("<renavam>", $r->Bin);
                                                                                                                                                        $renavamFinal = explode("</renavam>", $renavam[1]);
                                                                                                                                                        echo $renavamFinal[0]; ?>" target="_blank" style="font-size: 12px;">
                              BASE - ESTADUAL
                            </a>
                          </div>
                        </div>
                      </div>
                      <div class="editor-holder border rounded">
                        <div class="scroller position-relative h-100">
                          <textarea name="serpro" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->serpro) ?></textarea>
                          <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                              <code class="xml syntax-highight language-xml w-auto"></code>
                          </pre>
                        </div>
                      </div>
                    </div>

                  <?php endif; ?>

                  <?php if (@$sqlPERFIL_Q->especial_restricao == "N" && @$sqlPERFIL_Q->total_restricao == "S") : ?>

                    <div class="codedit col-12 mb-5">
                      <div class="d-flex flex-column py-2">
                        <h5 class="fs-5">RESTRICÕES</h5>
                        <div class="d-flex flex-row">
                          <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                            <a href="https://webservice.redecredauto.com.br/sys_basebin/ssp_send.php?cliente=<?php echo $_GET["Cliente"]; ?>&status=renavam&campo=<?php $renavam = explode("<renavam>", $r->Bin);
                                                                                                                                                                  $renavamFinal = explode("</renavam>", $renavam[1]);
                                                                                                                                                                  echo $renavamFinal[0]; ?>" target="_blank" style="font-size: 12px;">
                              BASE - ESTADUAL
                            </a>
                          </div>
                        </div>
                      </div>
                      <div class="editor-holder border rounded">
                        <div class="scroller position-relative h-100">
                          <textarea name="serpro" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->serpro) ?></textarea>
                          <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                              <code class="xml syntax-highight language-xml w-auto"></code>
                          </pre>
                        </div>
                      </div>
                    </div>

                  <?php endif; ?>


                  <?php if (@$sqlPERFIL_Q->especial_restricao == "S" && @$sqlPERFIL_Q->total_restricao == "S") : ?>

                    <div class="codedit col-12 mb-5">
                      <div class="d-flex flex-column py-2">
                        <h5 class="fs-5">RESTRICÕES</h5>
                        <div class="d-flex flex-row">
                          <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                            <a href="https://webservice.redecredauto.com.br/sys_basebin/ssp_send.php?cliente=<?php echo $_GET["Cliente"]; ?>&status=renavam&campo=<?php $renavam = explode("<renavam>", $r->Bin);
                                                                                                                                                                  $renavamFinal = explode("</renavam>", $renavam[1]);
                                                                                                                                                                  echo $renavamFinal[0]; ?>" target="_blank" style="font-size: 12px;">
                              BASE - ESTADUAL
                            </a>
                          </div>
                        </div>
                      </div>
                      <div class="editor-holder border rounded">
                        <div class="scroller position-relative h-100">
                          <textarea name="serpro" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->serpro) ?></textarea>
                          <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                              <code class="xml syntax-highight language-xml w-auto"></code>
                          </pre>
                        </div>
                      </div>
                    </div>

                  <?php endif; ?>


                  <?php if ((@$sqlPERFIL_Q->especial_Gravame == "S"  && $_GET["TIPO"] == 7 && $id_UF != "SP") || ($_GET["TIPO"] == 77) || ($_GET["TIPO"] == 19 && $id_UF != "SP")) { ?>


                    <div class="codedit col-12 mb-5">
                      <div class="d-flex flex-column py-2">
                        <h5 class="fs-5">GRAVAME</h5>
                        <div class="d-flex flex-row">
                          <!-- <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                              <a href="op_gravame_acsp.php?cliente=<?php echo $_GET["Cliente"]; ?>&status=chassi&campo=<?php echo $Envia["CHASSI"] ?>&consulta=<?php echo $_GET["Consulta"]; ?>&tipo=<?php echo $_GET["TIPO"]; ?>" 
                                target="_blank" 
                                style="font-size: 12px;"
                              >
                              </a>
                            </div>   -->
                          <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                            <a href="#" style="font-size: 12px;">
                              [xml] GRAVAME AUTO
                            </a>
                          </div>
                        </div>
                      </div>
                      <div class="editor-holder border rounded">
                        <div class="scroller position-relative h-100">
                          <textarea name="gravame" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->Gravame) ?></textarea>
                          <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                              <code class="xml syntax-highight language-xml w-auto"></code>
                          </pre>
                        </div>
                      </div>
                    </div>

                  <?php } else if ($_GET["TIPO"] != 9) { ?>

                    <div class="codedit col-12 mb-5">
                      <div class="editor-holder border rounded">
                        <div class="scroller position-relative h-100">
                          <textarea name="gravame" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->Gravame) ?></textarea>
                          <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                              <code class="xml syntax-highight language-xml w-auto"></code>
                          </pre>
                        </div>
                      </div>
                    </div>

                  <?php } ?>


                  <div class="codedit col-12 mb-5">
                    <div class="d-flex flex-column py-2">
                      <h5 class="fs-5">RESTRICÕES</h5>
                      <div class="d-flex flex-row">
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="operador_sendnort_leilao.php?c=1235972356121190863100012001<?php echo trim($_GET["ValorBusca"]); ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" style="font-size: 12px;">
                            LEILAO AUTO RISCO
                          </a>
                        </div>
                        <?php if ($id_leilao2 == 1) : ?>
                          <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                            <a href="operador_abs_leilao.php?c=1235972356121190863100012001<?php echo trim($_GET["ValorBusca"]); ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" style="font-size: 12px;">
                              LEILAO NORTIX - DIRETO
                            </a>
                          </div>
                        <?php endif; ?>
                        <?php if ($id_leilao2 == 1) : ?>
                          <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                            <a href="operador_abs_varejo.php?c=1235972356121190863100012001<?php echo trim($_GET["ValorBusca"]); ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" style="font-size: 12px;">
                              LEILAO ABS - VAREJO
                            </a>
                          </div>
                        <?php endif; ?>
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="operador_info_leilao.php?c=1235972356121190863100012001<?php echo trim($_GET["ValorBusca"]); ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" style="font-size: 12px;">
                            LEILAO INFOCAR - DIRETO
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="editor-holder border rounded">
                      <div class="scroller position-relative h-100">
                        <textarea name="leilao3" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->Leilao3) ?></textarea>
                        <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                            <code class="xml syntax-highight language-xml w-auto"></code>
                        </pre>
                      </div>
                    </div>
                  </div>


                  <div class="codedit col-12 mb-5">
                    <div class="d-flex flex-column py-2">
                      <div class="d-flex flex-row">
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="credauto_leilao_base.php?c=1235972356121190863100012001<?php echo trim($_GET["ValorBusca"]); ?>++++++++++++++X" target="_blank" style="font-size: 12px;">
                            LEILAO BASE CRED AUTO
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="editor-holder border rounded">
                      <div class="scroller position-relative h-100">
                        <textarea name="leilao2" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->Leilao2) ?></textarea>
                        <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                            <code class="xml syntax-highight language-xml w-auto"></code>
                        </pre>
                      </div>
                    </div>
                  </div>


                  <?php if ($_GET["TIPO"] == 7 || $_GET["TIPO"] == 77 || $_GET["TIPO"] == 76 || $_GET["TIPO"] == 15 || $_GET["TIPO"] == 19) : ?>

                    <div class="codedit col-12 mb-5">
                      <div class="d-flex flex-column py-2">
                        <h5 class="fs-5">PROPRIETÁRIOS ANTERIORES</h5>
                      </div>
                      <div class="editor-holder border rounded">
                        <div class="scroller position-relative h-100">
                          <textarea name="proprietarios" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->Proprietarios) ?></textarea>
                          <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                              <code class="xml syntax-highight language-xml w-auto"></code>
                          </pre>
                        </div>
                      </div>
                    </div>

                  <?php endif; ?>

                  <?php if ($_GET["TIPO"] == 7 || $_GET["TIPO"] == 77 || $_GET["TIPO"] == 76 || $_GET["TIPO"] == 19 || $_GET["TIPO"] == 29) { ?>


                    <div class="codedit col-12 mb-5">
                      <div class="d-flex flex-column py-2">
                        <h5 class="fs-5">SINISTRO</h5>
                        <div class="d-flex flex-row">
                          <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                            <a href="operador_sinistro.php?c=11xjx5i0fx3w1190863100012001<?php echo $_GET["ValorBusca"]; ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" style="font-size: 12px;">
                              [xml] Sinistro ACSP
                            </a>
                          </div>
                        </div>
                      </div>
                      <select class="form-select" name="sinistro" id="sinistro" >
                        <option value="" <?php if ($r->Sinistro == "") { ?>selected="selected" <?php } ?>>Selecionar</option>
                        <option value="0" <?php if ($r->Sinistro == "0") { ?>selected="selected" <?php } ?>>Não existe sinistro de Indenização Integral.</option>
                        <option value="1" <?php if ($r->Sinistro == "1") { ?>selected="selected" <?php } ?>>Encontrado sinistro de Indenização Integral.</option>
                        <option value="2" <?php if ($r->Sinistro == "2") { ?>selected="selected" <?php } ?>>SISTEMA INDISPONIVEL NO MOMENTO</option>
                      </select>
                    </div>


                    <?php if ($r->Sinistro2 == "") : ?>

                      <div class="col-12 mb-5">
                        <div class="d-flex flex-column py-2">
                          <h5 class="fs-5">DATA DO SINISTRO</h5>
                        </div>

                        <input class="form-control" type="text" name="sinistro2" >

                      </div>

                    <?php endif; ?>

                    <?php if ($r->Sinistro == "0") : ?>

                      &nbsp;&nbsp;&nbsp;&nbsp;<b>Sinistro Automatico Realizado</b><BR />

                    <?php endif; ?>


                    <?php if ($r->Sinistro2 != "") : ?>
                      <a>
                        <font color=red size=2> Em <?php echo $r->Sinistro2; ?></font>
                      </a>
                    <?php endif; ?>

                    <?php if ($_GET["TIPO"] == 7 || $_GET["TIPO"] == 29 || $_GET["TIPO"] == 77 || $_GET["TIPO"] == 76 || $_GET["TIPO"] == 19) : ?>

                      <BR />
                      <div class="col-12 mb-5">
                        <div class="d-flex flex-column py-2">
                          <h5 class="fs-5 text-danger">INDICIO DE SINISTRO</h5>
                        </div>
                        <select class="form-select" name="sinistro3" id="sinistro3" >
                          <option value="" <?php if ($r->Sinistro == "") { ?>selected="selected" <?php } ?>>Selecionar</option>
                          <option value="0" <?php if ($r->Sinistro3 == "0") { ?>selected="selected" <?php } ?>>Não existe indicio de sinistro.</option>
                          <option value="1" <?php if ($r->Sinistro3 == "1") { ?>selected="selected" <?php } ?>>Encontrado indicio de sinistro.</option>
                          <option value="2" <?php if ($r->Sinistro3 == "2") { ?>selected="selected" <?php } ?>>SISTEMA INDISPONIVEL NO MOMENTO</option>
                        </select>
                      </div>

                    <?php endif; ?>


                  <?php } ?>

                <?php } ?>

              <?php } ?>
              <BR />
              <?php if ($B_Q->TipoConsulta == "12") { ?>

                <?php if (@$sqlPERFIL_Q->Leilao == "S") { ?>

                  <div class="codedit col-12 mb-5">
                    <div class="d-flex flex-column py-2">
                      <!-- <h5 class="fs-5"></h5> -->
                      <div class="d-flex flex-row">
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="operador_sendnort_leilao.php?c=1235972356121190863100012001<?php echo $_GET["ValorBusca"]; ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" style="font-size: 12px;">
                            LEILAO AUTO RISCO
                          </a>
                        </div>
                        <?php if ($id_leilao2 == 1) : ?>
                          <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                            <a href="operador_abs_leilao.php?c=1235972356121190863100012001<?php echo $_GET["ValorBusca"]; ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" style="font-size: 12px;">
                              LEILAO NORTIX - DIRETO
                            </a>
                          </div>
                        <?php endif; ?>
                        <?php if ($id_leilao2 == 1) : ?>
                          <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                            <a href="operador_abs_varejo.php?c=1235972356121190863100012001<?php echo $_GET["ValorBusca"]; ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" style="font-size: 12px;">
                              LEILAO ABS - VAREJO
                            </a>
                          </div>
                        <?php endif; ?>
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="operador_info_leilao.php?c=1235972356121190863100012001<?php echo $_GET["ValorBusca"]; ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" style="font-size: 12px;">
                            LEILAO INFOCAR - DIRETO
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="editor-holder border rounded">
                      <div class="scroller position-relative h-100">
                        <textarea name="leilao3" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->Leilao3) ?></textarea>
                        <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                            <code class="xml syntax-highight language-xml w-auto"></code>
                        </pre>
                      </div>
                    </div>
                  </div>




                  <div class="codedit col-12 mb-5">
                    <div class="d-flex flex-column py-2">
                      <!-- <h5 class="fs-5"></h5> -->
                      <div class="d-flex flex-row">
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="credauto_leilao_base.php?c=1235972356121190863100012001<?php echo $_GET["ValorBusca"]; ?>++++++++++++++X" target="_blank" style="font-size: 12px;">
                            LEILAO BASE CRED AUTO
                          </a>
                        </div>
                      </div>
                    </div>
                    <div class="editor-holder border rounded">
                      <div class="scroller position-relative h-100">
                        <textarea name="leilao2" class="editor allow-tabs" spellcheck="false"><?= formatXMLToEditor($r->Leilao2) ?></textarea>
                        <pre lang=xml class="atom-one-light language-xml shadow-3xl text-sm h-100">
                            <code class="xml syntax-highight language-xml w-auto"></code>
                        </pre>
                      </div>
                    </div>
                  </div>

                  <div class="codedit col-12 mb-5">
                    <div class="d-flex flex-column py-2">
                      <h5 class="fs-5">SINISTRO</h5>
                      <div class="d-flex flex-row">
                        <div class="badge bg-light text-primary border p-2 fw-semibold me-2">
                          <a href="operador_sinistro.php?c=11xjx5i0fx3w1190863100012001<?php echo $_GET["ValorBusca"]; ?><?php echo $_GET["Consulta"]; ?>+++++++X" target="_blank" style="font-size: 12px;">
                            [xml] Sinistro ACSP
                          </a>
                        </div>
                      </div>
                    </div>
                    <select name="sinistro" id="sinistro" style="width: 90%;">
                      <option value="0" <?php if ($r->Sinistro == "0") { ?>selected="selected" <?php } ?>>Não existe sinistro de Indenização Integral.</option>
                      <option value="1" <?php if ($r->Sinistro == "1") { ?>selected="selected" <?php } ?>>Encontrado sinistro de Indenização Integral.</option>
                      <option value="2" <?php if ($r->Sinistro == "2") { ?>selected="selected" <?php } ?>>SISTEMA INDISPONIVEL NO MOMENTO</option>
                    </select>
                  </div>


                  <!--<br /><font color=red ><b>BACKP - ( somente deve ser utilizado se o NORTIX estiver fora do ar )</b></font><br>
  <a href="operador_leilao1.php?cliente=<?php echo $_GET["Cliente"]; ?>&placa=<?php echo $_GET["ValorBusca"]; ?>&consulta=<?php echo $_GET["Consulta"]; ?>&tipo=<?php echo $_GET["TIPO"]; ?>" target="_blank" class="linkR">[xml] leilao  TDI</a><br>
  <textarea name="leilao" id="leilao" style="width: 90%;height: 30px" ><?php echo $r->Leilao; ?></textarea><BR />-->


                <?php  } ?>


              <?php } ?>

              <?php

              //- ATUALIZA CONSULTA -----------------------------------------------

              if ($valor_dia < 6) { ?>


                <!-- <br />
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
                <br /> -->
              <?php } ?>


            <?php endif; ?>

                </div>

        </div>
      </div>

      <div class="modal-footer  justify-content-between">

        <?php if ($valor_dia < 6) : ?>

          <button type="submit" class="btn btn-danger"
            formaction="<?=$baseURL?>/actions/liberar_consulta.action.php"
          >
            Fechar e Liberar para o Cliente
          </button>

          <div class="d-flex flex-row">
            <a class="btn btn-secondary me-2" href="rpc/inc_consulta_normalizada.php?Codigo=<?php echo $_GET["Consulta"]; ?>&print=1&Tipo=7" target="_blank">
              Pré-Visualizar
            </a>

            <button type="submit" class="btn btn-primary" 
            >
              Salvar Alterações
            </button>

          </div>
        <?php endif; ?>

      </div>
    </form>
