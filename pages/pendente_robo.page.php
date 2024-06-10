<?php
@session_start();

    @include_once('../config.php');
    require_once('../classes/Database.class.php');
    require_once('../classes/Helpers.class.php');

	$db = new Database();

	$db->query = "SELECT * FROM consultas WHERE Codigo = ?";
	$db->content = array(base64_decode($_GET['consulta']), 'int');
	$consulta = $db->selectOne();

	$db->query = "SELECT * FROM resultado_consultas WHERE CodigoResu = ?";
	$db->content = array(base64_decode($_GET['consulta']), 'int');
	$resultado = $db->selectOne();

	$db->query = "SELECT * FROM ttipoconsulta WHERE id = ?";
	$db->content = array($consulta->TipoConsulta, 'int');
	$tipo = $db->selectOne();

	$db->query = "SELECT * FROM tcadcli WHERE ID = ?";
	$db->content = array($consulta->CodCliente, 'int');
	$cliente = $db->selectOne();

	$extra = @json_decode($resultado->extra);

	if(!$extra)
	{
		$extra = array();
	}
	switch($consulta->TipoConsulta)
	{
		case 7:
		case 77:
		case 19:
			if((string)$resultado->Sinistro3 === '' || (int)$resultado->Sinistro3 === 2)
			{
				array_push($extra, 'INDICIO DE SINISTRO');
			}
			break;
	}
?>



<?php 
  // get the raw POST data
  $json_base64 = $_POST['jsonb64'];
    
   /**
   * Returns the JSON encoded POST data, if any, as an object.
   * 
   * @return Object|null
   */
  function retrieveJsonBase64PostData()
  {
    global $json_base64;

    $stringfy = base64_decode($json_base64);

    // this returns null if not valid json
    return json_decode($stringfy);
  }

  $props = retrieveJsonBase64PostData();

?>

<div class="modal-header align-items-start">
    <div class="d-flex">
        <div class="d-flex flex-column">
            <h1 class="modal-title fs-5 mb-2" >
              <i class="bi bi-exclamation-triangle-fill me-2"></i>Pendências de Atualização
            </h1>
            <small class="fw-semibold text-muted">
              Consulta: <span class="fw-bold"><?= $props->consulta ?></span>
            </small>
            <small class="fw-semibold text-muted">
              Código: <span class="fw-bold"><?= $props->codigo ?></span>
            </small>
            <small class="fw-semibold text-muted">
                <?= ucwords($consulta->ItemConsultado) ?>: <span class="fw-bold"><?= $props->parametro ?></span>
            </small>
            <small class="fw-semibold text-muted">
              Realizada em: <span class="fw-bold"><?= $props->data ?> às <?= $props->hora ?></span>
            </small>
        </div>
    </div>
    <button type="button" class="btn-close m-1" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">

    <div class="d-flex flex-column mb-3">
        <small class="fw-semibold text-muted">
            Login: <span class="fw-bold"><?= $cliente->ID ?></span>
        </small>
        <small class="fw-semibold text-muted">
            Cliente: <span class="fw-bold"><?= $cliente->Razao ?></span>
        </small>
        <small class="fw-semibold text-muted">
            Estado: <span class="fw-bold"><?= Helpers::$estados[strtoupper(trim($consulta->UF))] ?> (<?= $consulta->UF ?>)</span>
        </small>
        <small class="fw-semibold text-muted">
            Verificado em: <span class="fw-bold"><?= str_replace(' ', ' às ', date('d/m/Y H:i:s')) ?></span>
        </small>
    </div>

    <?php if(@count($extra) && $extra): ?>
        <table class="table table-bordered table-striped">
            <tr>
                <th colspan="2" class="bg-info text-white">Pendências de Atualização</th>
            </tr>
            <?php foreach($extra as $key => $value): ?>
                <tr>
                    <td><?php echo $key + 1; ?></td>
                    <td><?php echo $value; ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
 
    <?php else: ?>
        <div class="alert alert-warning">
            Nenhuma pendência identificada na consulta
        </div>
    <?php endif; ?>
</div>

<?php if(@count($extra) && $extra): ?>
    <div class="modal-footer  justify-content-between">
        
        <div class="d-flex flex-row">
            <button class="btn btn-info me-3" onclick="window.location.reload();">Verificar</button>
        
            <button class="btn btn-primary" 
                data-bs-open="modal" 
                data-bs-template="<?=$baseURL?>/pages/acao_pedido_alteracao.page.php?consulta=<?= $_GET['consulta'] ?>&MSId=<?= base64_encode($_SESSION['MSId']) ?>"
                data-bs-jsonb64="<?= $json_base64 ?>"
            >
                Abrir Chamado
            </button>
        </div>
    </div>
<?php endif; ?>



<!-- onclick="window.location.href = 'http://credoperador.com.br/acoes/index.php?consulta=<?php echo $_GET['consulta']; ?>&MSId=<?php echo $_GET['MSId']; ?>'" -->
