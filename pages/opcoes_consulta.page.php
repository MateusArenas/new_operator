<?php 
  @session_start(); 

  @include_once('../config.php');
  require('../classes/Database.class.php');
  require('../classes/Atendente.class.php');
  require('../classes/Consulta.class.php');

  $Atendente = new Atendente();
  $Consulta = new Consulta();

  $response = new stdClass();
  
  $atendente = @$_SESSION["MSId"];
  $consulta = @$_REQUEST['consulta'];

  try {
      if(!isset($_SESSION['MSId'])) throw new Exception('ID do Atendente não localizado');
      if(!isset($_REQUEST['consulta'])) throw new Exception('Código da consulta não localizado');

      // depois tirar. e arrumar em tudo
      $consulta = base64_decode($consulta);
      //
      
      $atendente = $Atendente->findById($atendente);
      
      if (!$atendente) throw new Exception('Atendente não localizado');

      $username = $atendente->LoginAtendente ?: $atendente->NomeAtendente;
    
      if (@$atendente->slack_id) {
        $atendente_link = "https://redecredautogroup.slack.com/team/$atendente->slack_id";
      } else if (@$atendente->email) {
        $atendente_link = "mailto:$atendente->email";
      }

      $consulta = $Consulta->findByCodigo($consulta);

      if (!$consulta) throw new Exception('Consulta não localizada');
      
      $consultaLink = "https://www.credoperador.com.br/rpc/inc_consulta_normalizada.php?Codigo=".$consulta->codigo."&print=1&Tipo=".$consulta->codTipo;

      if (@$_GET['xml'] == '1') $consultaLink = '';

  } catch (\Throwable $th) {
      $response->error = $th->getMessage();
  }

?>

<div class="modal-header">
    <h1 class="modal-title fs-5">
      <i class="bi bi-gear-wide-connected me-2"></i>
      Opções da Consulta
    </h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">

  <?php if (@$response->error): ?> 
        
      <div class="alert alert-warning" role="alert">
          <?= $response->error ?>
      </div>

  <?php else: ?>

    <div class="row mb-3">
        <div class="col-6">
            <div class="d-flex flex-column">

                <small class="mb-1">
                    <span class="fw-semibold">Operador:</span>
                    <br> 
                    <?php if(@$atendente_link): ?>
                        <a href="<?= $atendente_link ?>" target="_blank"
                            class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover"
                        >
                            @<?= $atendente->LoginAtendente ?: $atendente->NomeAtendente ?>
                        </a>
                    <?php else: ?>
                        @<?= $atendente->LoginAtendente ?: $atendente->NomeAtendente ?>
                    <?php endif; ?>
                </small>

                <small class="mb-1">
                    <span class="fw-semibold">Código:</span><br> 
                    <?php if (@$consultaLink): ?> 
                        <a href="<?= $consultaLink ?>" target="_blank"
                            class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover"
                        >
                          <?= $consulta->codigo ?> <i class="bi bi-arrow-up-right-circle"></i>
                        </a>
                    <?php else: ?>
                          <?= $consulta->codigo ?>
                    <?php endif; ?> 
                </small>

                <small class="mb-1">
                  <span class="opacity-50 ">( Parâmetro )</span>
                  <span class="fw-semibold text-capitalize"><?= $consulta->item ?><?php if ($consulta->uf) echo ' / UF' ?>:
                  </span><br> <?= $consulta->parametro ?> <?php if ($consulta->uf) echo '/ ' . $consulta->uf ?>
                </small>
            </div>
        </div>
        <div class="col-6">
            <div class="d-flex flex-column">

                <small class="mb-1">
                    <span class="fw-semibold">Data Consulta:</span><br> <?= date('d/m/Y à\s H:i', strtotime($consulta->data)) ?>
                </small>

                <small class="mb-1">
                    <span class="fw-semibold">Tipo Consulta:</span><br> <?= $consulta->tipo ?>
                </small>
                
                <small class="mb-1">
                    <span class="fw-semibold">Cliente:</span><br> 
                    <a href="#" class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover" 
                      data-bs-open="modal"
                      data-bs-template="<?=$baseURL?>/pages/consulta_cliente.page.php?codCliente=<?= $consulta->cliente ?>&consulta=<?= base64_encode($consulta->codigo) ?>"
                      data-bs-jsonb64="<?= $json_base64 ?>"
                      data-bs-modaltype="modal-fullscreen-md-down"
                    >
                      <?= $consulta->cliente ?>
                    </a>
                </small>

            </div>
        </div>
    </div>
    
    <div class="mb-3">
          <div class="list-group">
  
              <!-- <a  href="<?= $consultaLink ?>" target="_blank" role="button" class="list-group-item list-group-item-action d-flex justify-content-between" aria-current="true">
                <span class="fw-semibold">
                  <i class="bi bi-printer-fill me-2"></i> Imprimir consulta
                </span>
  
                <i class="bi bi-arrow-up-right-circle-fill text-primary"></i>
              </a> -->

              <?php $_GET["Pagina"] = "RelatorioDiario"; ?>
  
              <!-- data-bs-modaltype="fullscreen" -->

              <?php if (@$_GET['editar'] !== '0'): ?>
                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between" aria-current="true"
                  data-bs-open="modal" 
                  data-bs-template="<?=$baseURL?>/pages/editar_alterar.page.new.php?TIPO=<?=$consulta->TipoConsulta?>&Consulta=<?=$consulta->Codigo?>&ValorBusca=<?=$consulta->ValorItem?> <?php if($_GET["Pagina"] == "RelatorioDiario"){ echo "&Edita=Sim"; } ?>&Cliente=<?=$consulta->CodCliente?>&Item=<?=$consulta->ItemConsultado?>"
                  data-bs-jsonb64="<?= $json_base64 ?>"
                  data-bs-modaltype="xl"
                >
                  <span class="fw-semibold">
                    <i class="bi bi-file-earmark-text-fill me-2"></i> Gravar ou editar consulta
                  </span>
    
                  <i class="bi bi-arrow-up-circle-fill text-primary"></i>
                </button>
              <?php endif; ?>
  
              <?php if (@$_GET['pedido'] !== '0'): ?>
                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between" aria-current="true"
                  data-bs-open="modal" 
                  data-bs-template="<?=$baseURL?>/pages/acao_pedido_alteracao.page.php?consulta=<?= $_GET['consulta'] ?>&MSId=<?= base64_encode($_SESSION['MSId']) ?>"
                  data-bs-jsonb64="<?= $json_base64 ?>"
                >
                  <span class="fw-semibold">
                    <i class="bi bi-broadcast me-2"></i> Solicitar Alteração
                  </span>
    
                  <i class="bi bi-arrow-up-circle-fill text-primary"></i>
                </button>
              <?php endif; ?>

              <?php if (@$_GET['xml'] !== '0'): ?>
                <a class="list-group-item list-group-item-action d-flex justify-content-between" aria-current="true"
                  target="_blank"
                  href="https://www.credoperador.com.br/rpc/xml_webservice.php?cd=<?php echo base64_encode($consulta->codigo).'&nm='.base64_encode($consulta->tipo).'&cc='.base64_encode($consulta->cliente);?>"
                >
                  <span class="fw-semibold">
                    <i class="bi bi-code-slash me-2"></i> Ver XML
                  </span>
    
                  <i class="bi bi-arrow-up-circle-fill text-primary"></i>
                </a>
              <?php endif; ?>
             
          </div>
    </div>
  
    <!-- <div class="list-group">
        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between" aria-current="true"
            data-bs-open="modal" 
            data-bs-template="<?=$baseURL?>/pages/consulta_cliente.page.php"
            data-bs-jsonb64="<?= $json_base64 ?>"
            data-bs-modaltype="lg"
        >
          <span class="fw-semibold">
            <i class="bi bi-person-fill me-2"></i> Dados do Cliente
          </span>
  
          <i class="bi bi-arrow-up-circle-fill text-primary"></i>
        </button>
    </div> -->

  <?php endif; ?>


</div>

