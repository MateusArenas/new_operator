<?php 
  @session_start();

  require('../classes/Database.class.php');
  require('../classes/Atendente.class.php');
  require('../classes/Consulta.class.php');

  $Atendente = new Atendente();
  $Consulta = new Consulta();

  $response = new stdClass();

  $url_imagem = @$_REQUEST['imagem'];
  
  //code...
  $id = @$_SESSION["MSId"];

  try {
      if(!isset($_SESSION['MSId'])) throw new Exception('ID do Atendente não localizado');
      
      $atendente = $Atendente->findById($id);
      
      if (!$atendente) throw new Exception('Atendente não localizado');

  } catch (\Throwable $th) {
      $response->error = $th->getMessage();
  }

?>

<div class="modal-header">
    <h1 class="modal-title fs-5">
      <i class="bi bi-image me-2"></i>
      Vizualização de Imagem
    </h1>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body position-relative">

  <?php if (@$response->error): ?> 
        
      <div class="alert alert-warning" role="alert">
          <?= $response->error ?>
      </div>

  <?php else: ?>

    <center>
        <img src="<?= $url_imagem ?>" class="img-thumbnail" >
    </center>


  <?php endif; ?>


</div>

