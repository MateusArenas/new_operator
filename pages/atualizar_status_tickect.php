<?php
  @session_start();

  @include_once('../config.php');
  require_once('../classes/Database.class.php');
  require_once('../classes/Tickets.class.php');
  require_once('../classes/Helpers.class.php');

	$db = new Database();
	$ticketsRepository = new Tickets();

  $user_id = @$_SESSION['MSId'] or die('User id não localizado.');
  $ticket_id = @$_REQUEST['ticket_id'] or die('Ticket id não localizado.');

  $ticket = $ticketsRepository->findById($ticket_id);

  $motivos = $ticketsRepository->motivos();
  $status = $ticketsRepository->status();
?>

<form
  data-form-type="ajax"
  data-form-target="#atualizar_chamado_message"
  action="<?=$baseURL?>/action-update-ticket.php"
  method="post"
>
<div class="modal-header align-items-start">
    <div class="d-flex">
        <div class="d-flex flex-column">
            <h1 class="modal-title fs-5 mb-2" >
              <i class="bi bi-exclamation-triangle-fill me-2"></i>Atualizar Chamado
            </h1>
        </div>
    </div>
    <button type="button" class="btn-close m-1" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <div class="row">
      <input type="hidden" name="ticket_id" value="<?= $ticket_id ?>">

      <div id="atualizar_chamado_message" class="col-12"></div>

      <div class="col-12">

          <div class="form-floating mb-3">
            <textarea class="form-control" placeholder="Leave a comment here" id="floatingTextarea2" style="min-height: 100px" disabled readonly><?= $ticket->description ?></textarea>
            <label for="floatingTextarea2">Descrição</label>
          </div>

          <div class="form-floating mb-3">
              <select class="form-select form-select-sm" 
                  name="status" required
              >
                  <?php foreach($status as $value => $item): ?>
                    <option value="<?=$value?>"  <?php if($ticket->status == $value) echo 'selected'; ?>   ><?="{$item}"?></option>
                  <?php endforeach; ?>
              </select>
              <label class="form-label">Status</label>
          </div>
      </div>
    </div>
</div>

  <div class="modal-footer  justify-content-between">
      
      <div class="d-flex flex-row">
      
          <button class="btn btn-primary" 
              type="submit"
          >
              Atualizar Chamado
          </button>
      </div>
  </div>



</form>
