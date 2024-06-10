<?php 
  @session_start();
  // Configuração do local para português do Brasil
  setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
  date_default_timezone_set('America/Sao_Paulo');

  require_once('../config.php');
  require_once('../classes/Database.class.php');



// $Nome                = mb_convert_encoding(@$row->nome_fantasia?:'Não inf.', 'UTF-8', 'ISO-8859-1');
// $codigo              = @$row->ID;
// $Fantasia            = mb_convert_encoding(@$row->nome_fantasia?:'Não inf.', 'UTF-8', 'ISO-8859-1');
// $documento           = @$row->cnpj?:'Não inf.';
// $Cidade              = @$row->CIDADE?:'Não inf.';
// $Bairro              = @$row->Bairro?:'Não inf.';
// $Estado              = @$row->Uf?:'Não inf.';
// $ddd                 = @$row->DDD?:'Não inf.';
// $Telefones           = @$row->Fone?:'Não inf.';
// $Telefones           = $ddd."&nbsp;&nbsp;".$Telefones;

// $ddd1                 = @$row->ddd1?:'Não inf.';
// $Telefones1           = @$row->telefone1?:'Não inf.';
// $Telefones1          = $ddd1."&nbsp;&nbsp;".$Telefones1;

// $celular1           = @$row->celular1?:'Não inf.';
// $celular2           = @$row->celular2?:'Não inf.';
// $Contato            = mb_convert_encoding(@$row->CONTATO?:'Não inf.', 'UTF-8', 'ISO-8859-1');
// $email              = @$row->email?:'Não inf.';
// $email2             = @$row->email2?:'Não inf.';

$operador = @$_REQUEST['operador'];

// buscar no banco.
$db = new Database();

$response = new stdClass();

// Horário atual
$agora = new DateTime();

$agora_formatada = $agora->format('Y-m-d H:i:s');

$agora_formatada = strftime('%e de %B de %Y às %H:%M',  strtotime($agora_formatada));

try {
  if (!isset($_SESSION['MSId'])) throw new Exception('Sessão expirada.');

  if (!$operador) throw new Exception('Operador não informado.');

  $db->query = "SELECT * FROM credauto.atendentes WHERE CodAtendente = ?;";
  $db->content = array([$operador, 'int']);

  $operador = $db->selectOne();

  if (!$operador)  throw new Exception('Operador não encontrado.');

  $operador->nome = @$operador->NomeNovoAtendente ?: @$operador->NomeAtendente;

  // Convertendo a data para o formato correto
  $data_formatada = strftime('%e de %B de %Y',  strtotime($operador->Data));

  // Formatação da hora
  $hora_formatada = date('H:i', strtotime(trim($operador->Hora)));

  $ultima_acesso_formatada = "$data_formatada às $hora_formatada";

  $sessao_contato = [];

  if ( $operador->status =="1" ) { // BLOQUEIO AUTOMATICO
    $v_status = "SIM";
  } else {
    $v_status = "NAO";
  }


  if ( $operador->Situacao !='2' ) {
    // quando 2 não existe. descontinuado ou algo assim.
  }

  
  // ACESSO
  $sessao_contato[] = [ 
    "icon" => 'bi bi-key-fill',
    "label" => 'Situação / Acesso',
    "item" => (
      $operador->acesso == "0" ? 
        '<span class="fw-semibold text-success">Ativo</span>' : '<span class="fw-semibold text-danger">Inativo</span>' 
    )
  ];
 
  
  if ($operador->horaentrada && $operador->horasaida) 
  {
    // Horário de início e término
    $inicio = DateTime::createFromFormat('H:i:s', $operador->horaentrada);
    $fim = DateTime::createFromFormat('H:i:s', $operador->horasaida);
  
    // Formatação para 'H:i'
    $operador->horaentrada = $inicio->format('H:i');
    $operador->horasaida = $fim->format('H:i');
  
    // Horário atual
    $agora = new DateTime();
  
    // Verifica se o horário atual está entre o horário de início e término
    if ($agora >= $inicio && $agora <= $fim) {
        $informacao_horario = '<span class="text-muted">Está dentro do horário.</span>';
    } else {
        $informacao_horario = '<span class="text-muted">Não está dentro do horário.</span>';
    }

    $sessao_contato[] = [ 
      "icon" => 'bi bi-clock-fill',
      "label" => 'Horário de Trabalho',
      "item" => "$operador->horaentrada - $operador->horasaida, $informacao_horario",
    ];
  }

  if ($operador->email) 
  {
    $sessao_contato[] = [ 
      "icon" => 'bi bi-envelope-fill',
      "label" => 'Endereço de e-mail',
      "link" => "mailto:$operador->email",
      "item" => $operador->email,
    ];
  }

  if ($operador->slack_id) 
  {
    $sessao_contato[] = [ 
      "icon" => 'bi bi-slack',
      "label" => 'Perfil do Slack',
      "link" => "https://redecredautogroup.slack.com/team/$operador->slack_id",
      "item" => "$operador->slack_id <i class='bi bi-arrow-right-short before:-rotate-45'></i>",
    ];
  }

  // BLOQUEIO AUTOMATICO
  $sessao_contato[] = [ 
    "icon" => $operador->status == "1" ? 'bi bi-lock-fill' : 'bi bi-unlock-fill',
    "label" => 'Bloqueio Automático',
    "item" => $operador->status == "1" ? 'Sim': 'Não',
  ];


  $sessao_contato[] = [ 
    "icon" => 'bi bi-door-open-fill',
    "label" => 'Último acesso',
    "item" => "Dia $ultima_acesso_formatada",
  ];

} catch (\Throwable $th) {
  $response->error = $th->getMessage();
}

?>

<div class="modal-header">
  <h1 class="modal-title fs-5">
    Perfil
  </h1>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">

  <?php if (@$response->error) : ?>

    <div class="alert alert-warning" role="alert">
      <?= $response->error ?>
    </div>

  <?php else : ?>
    <div class="row g-3">

      <div class="col-12">

        <div class="d-flex flex-row justify-content-between">

          <img class="bg-light rounded align-self-center me-3" 
            src="<?= $operador->ImagemAtendente ?>" 
            data-srcset="<?= $baseURL ?>/profile_image.php?fullname=<?= ucwords($operador->nome) ?>" 
            onerror="defaultImage(this)" 
            alt="" width="60" height="60" 
            <?php if(@$operador->acesso != "0") echo 'disabled' ?>
          />
          
          <div class="d-flex flex-column justify-content-center flex-grow-1">
            <h5 class="mb-0"><?= ucwords($operador->nome) ?></h5>
            <small class="text-muted"><?= $operador->LoginAtendente ?></small>
          </div>

        </div>
        
      </div>

      <div class="col-12">
        <div class="d-flex flex-row gap-2">
          
          <button type="button" class="btn btn-sm btn-outline-secondary text-truncate flex-grow-1"
            data-bs-action="copy"
            data-bs-duration="900"
            value="<?= "$baseURL/dashboard?user=$operador->CodAtendente" ?>"
            placeholder="Link Copiado!"
          >
            Copiar link para o perfil
          </button>

          <button type="button" class="btn btn-sm btn-outline-secondary text-truncate flex-grow-1"
            data-bs-action="copy"
            data-bs-duration="900"
            value="<?= $operador->CodAtendente ?>"           
            placeholder="ID Copiado!"
          >
            Copiar ID do operador
          </button>

          <button type="button" class="btn btn-sm btn-outline-secondary"
            disabled
          >
            <i class="bi bi-three-dots-vertical"></i>
          </button>
        </div>
      </div>

      <div class="col-12">

        <div class="d-flex flex-column gap-2">

            <div class="d-flex flex-row align-items-center justify-content-between">
              <h6 class="mb-0">Dados do Operador</h6>

              <button type="button" class="btn btn-sm btn-link link-underline link-underline-opacity-0 link-underline-opacity-100-hover mb-0">
                Editar
              </button>
            </div>

            <div class="d-flex flex-column gap-2">

              <?php foreach ($sessao_contato as $item): ?>

                <div class="d-flex flex-row align-items-center mb-2">

                  <div class="badge bg-light text-secondary p-2 me-2" >
                    <i class="<?= $item['icon'] ?> fs-6"></i>
                  </div>

                  <div class="d-flex flex-column">

                    <small class="text-muted"><?= $item['label'] ?></small>

                    <?php if (@$item['link']) : ?>
                      <a class="link-underline link-underline-opacity-0 link-underline-opacity-100-hover" 
                        href="<?= $item['link'] ?>"
                        target="_blank"
                      >
                        <?= $item['item'] ?>
                      </a> 
                    <?php else: ?>
                      <span>
                        <?= $item['item'] ?>
                      </span>
                    <?php endif; ?>

                  </div>
                </div>

              <?php endforeach; ?>

            </div>

            <!-- <br>

            <small class="text-muted">
              ID do perfil: <?= $operador->CodAtendente ?>
            </small> -->


        </div>

      </div>



        
    </div>
  <?php endif; ?>
  
</div>

<div class="modal-footer justify-content-center">
  <small class="text-muted">
    Dados referentes ao dia <?=$agora_formatada?>
  </small>
</div>