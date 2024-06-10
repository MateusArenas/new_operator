<?php 
  @session_start(); 

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

  require_once('../classes/Database.class.php');

  $codCliente = @$props->codCliente ?: @$_REQUEST['codCliente'];

  if (!$codCliente) exit('código do cliente não informado.');
  
  // buscar no banco.
  $db = new Database();

  $db->query = "SELECT * FROM tcadcli WHERE ID = ?;";
  $db->content = array([$codCliente]);

  $row = $db->selectOne();

  if (!$row) exit('cliente não encontrado.');

  $Nome                = mb_convert_encoding(@$row->nome_fantasia?:'Não inf.', 'UTF-8', 'ISO-8859-1');
  $codigo              = @$row->ID;
  $Fantasia            = mb_convert_encoding(@$row->nome_fantasia?:'Não inf.', 'UTF-8', 'ISO-8859-1');
  $documento           = @$row->cnpj?:'Não inf.';
  $Cidade              = @$row->CIDADE?:'Não inf.';
  $Bairro              = @$row->Bairro?:'Não inf.';
  $Estado              = @$row->Uf?:'Não inf.';
  $ddd                 = @$row->DDD?:'Não inf.';
  $Telefones           = @$row->Fone?:'Não inf.';
  $Telefones           = $ddd."&nbsp;&nbsp;".$Telefones;

  $ddd1                 = @$row->ddd1?:'Não inf.';
  $Telefones1           = @$row->telefone1?:'Não inf.';
  $Telefones1          = $ddd1."&nbsp;&nbsp;".$Telefones1;

  $celular1           = @$row->celular1?:'Não inf.';
  $celular2           = @$row->celular2?:'Não inf.';
  $Contato            = mb_convert_encoding(@$row->CONTATO?:'Não inf.', 'UTF-8', 'ISO-8859-1');
  $email              = @$row->email?:'Não inf.';
  $email2             = @$row->email2?:'Não inf.';

  require('../classes/Atendente.class.php');

  $Atendente = new Atendente();

  $response = new stdClass();
  
  //code...
  $id = @$_SESSION["MSId"];
  $codConsulta = base64_decode(@$_REQUEST['consulta']);

  try {
      if(!isset($_SESSION['MSId'])) throw new Exception('ID do Atendente não localizado');
      
      $atendente = $Atendente->findById($id);
      
      if (!$atendente) throw new Exception('Atendente não localizado');

      $username = $atendente->LoginAtendente ?: $atendente->NomeAtendente;
    
      if (@$atendente->slack_id) {
        $atendente_link = "https://redecredautogroup.slack.com/team/$atendente->slack_id";
      } else if (@$atendente->email) {
        $atendente_link = "mailto:$atendente->email";
      }
      
  } catch (\Throwable $th) {
      $response->error = $th->getMessage();
  }

?>

<div class="modal-header">
    <h1 class="modal-title fs-5">
    <i class="bi bi-person-fill me-2"></i>
      Cadastro do Cliente
    </h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">

<?php if (@$response->error): ?> 
        
    <div class="alert alert-warning" role="alert">
        <?= $response->error ?>
    </div>

<?php else: ?>
  <div class="row g-2">
    <div class="col-12">
  
    <?php if(@$Situacao == 'E'): ?>
      <img src=imagens/delete.gif>&nbsp;<font size=2 color=red><b>CODIGO AGUARDANDO ATIVAÇÃO</b></font>
    <?php else: ?>
  
      <?php if(@$Situacao == 'A'): ?>
        <img src=imagens/smallSuccess.png width='20' height='20'>&nbsp;<font size=2 color=rede><b>CODIGO ATIVO</b></font>
      <?php endif; ?>
  
      <?php if(@$Situacao == 'B'): ?>
        <img src=imagens/delete.gif>&nbsp;<font size=2 color=red><b>CODIGO BLOQUEADO</b></font>
      <?php endif; ?>
    <?php endif; ?>
  
      <table class="table m-0 table-sm table-bordered">
        <thead>
          <tr>
            <th scope="col"><small>Código</small></th>
            <th scope="col"><small>Cliente</small></th>
          </tr>
        </thead>
        <tbody >
          <tr>
            <td>
              <small class="fw-semibold" >
                <?= $codigo ?: 'Não Inf.' ?>
              </small>
            </td>
            <td>
              <small>
                <?= $Nome ?: 'Não Inf.' ?>
              </small>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  
    <div class="col-12">
      <table class="table m-0 table-sm table-bordered">
        <thead>
          <tr>
            <th scope="col"><small>CNPJ</small></th>
          </tr>
        </thead>
        <tbody >
          <tr>
            <td>
              <small class="fw-semibold">
                <?= $documento ?: 'Não Inf.' ?>
              </small>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  
    <div class="col-12">
      <table class="table m-0 table-sm table-bordered">
        <thead>
          <tr>
            <th scope="col"><small>Cidade</small></th>
            <th scope="col"><small>Estado</small></th>
          </tr>
        </thead>
        <tbody >
          <tr>
            <td>
              <small >
                <?= $Cidade ?: 'Não Inf.' ?>
              </small>
            </td>
            <td>
              <small>
                <?= $Estado ?: 'Não Inf.' ?>
              </small>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  
    <div class="col-12">
      <table class="table m-0 table-sm table-bordered">
        <thead>
          <tr>
            <th scope="col"><small>Contato</small></th>
          </tr>
        </thead>
        <tbody >
          <tr>
            <td>
              <small>
                <?= $Contato ?: 'Não Inf.' ?>
              </small>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  
    <div class="col-6">
      <table class="table m-0 table-sm table-bordered">
        <thead>
          <tr>
            <th scope="col"><small>Telefone 1</small></th>
          </tr>
        </thead>
        <tbody >
          <tr>
            <td>
              <small >
                <?= $Telefones ?: 'Não Inf.' ?>
              </small>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  
    <div class="col-6">
      <table class="table m-0 table-sm table-bordered">
        <thead>
          <tr>
            <th scope="col"><small>Telefone 2</small></th>
          </tr>
        </thead>
        <tbody >
          <tr>
            <td>
              <small>
                <?= $Telefones1 ?: 'Não Inf.' ?>
              </small>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  
    <div class="col-6">
      <table class="table m-0 table-sm table-bordered">
        <thead>
          <tr>
            <th scope="col"><small>Celular 1</small></th>
          </tr>
        </thead>
        <tbody >
          <tr>
            <td>
              <small>
                <?= $celular1 ?: 'Não Inf.' ?>
              </small>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  
    <div class="col-6">
      <table class="table m-0 table-sm table-bordered">
        <thead>
          <tr>
            <th scope="col"><small>Celular 2</small></th>
          </tr>
        </thead>
        <tbody >
          <tr>
            <td>
              <small>
                <?= $celular2 ?: 'Não Inf.' ?>
              </small>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  
    <div class="col-12">
      <table class="table m-0 table-sm  table-bordered">
        <thead>
          <tr>
            <th scope="col"><small>Email 1</small></th>
          </tr>
        </thead>
        <tbody >
          <tr>
            <td>
              <small >
                <?= $email ?: 'Não Inf.' ?>
              </small>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  
    <div class="col-12">
      <table class="table m-0 table-sm  table-bordered">
        <thead>
          <tr>
            <th scope="col"><small>Email 2</small></th>
          </tr>
        </thead>
        <tbody >
          <tr>
            <td>
              <small>
                <?= $email2 ?: 'Não Inf.' ?>
              </small>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>

</div>

