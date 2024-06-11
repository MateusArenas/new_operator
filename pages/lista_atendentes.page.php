<?php @session_start();

// Configuração do local para português do Brasil
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');

@include_once('../config.php');
@include_once('../classes/Database.class.php');
@include_once('../classes/Users.class.php');

$db = new Database();
$usersRepository = new Users();

$atendentes = $usersRepository->findAll();
$tipos = $usersRepository->tipos();

?>

<div class="d-flex flex-column flex-fill p-3">

    <h1 class="fs-5 mb-3">
        Lista de Operadores
    </h1>

    <!-- passando width: 10px; foi a solução -->
    <div class="dashboard-table-overflow rounded overflow-auto position-relative d-flex flex-column flex-fill">

        <div class="table-responsive h-100 rounded border bg-dashboard p-2 pt-0">
            <table class="table dashboard-table table-hover table-sm caption-top">
                <!-- <caption class="p-2">
                    <small class="text-start text-muted " style="font-size: 12px;">
                        Total / Atendentes: <span id="total-atendentes"></span>
                    </small>
                </caption> -->
                <thead class="sticky-top table-header bg-dashboard">
                    <tr class="align-middle" style="height: 42px;">

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Nome</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Tipo</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Status</small>
                            </div>
                        </th>
                        
                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Bloqueio Automático</small>
                            </div>
                        </th>

                        <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Último Acesso</small>
                            </div>
                        </th>

                        <!-- <th class="input-group-sm" scope="col">
                            <div class="input-group-text text-start w-100 d-flex align-items-center position-relative">
                                <small>Criado em</small>
                            </div>
                        </th> -->

                    </tr>
                </thead>
                <tbody id="--" >

                    <?php foreach ($atendentes as $atendente) : 

                        $atendente_imagem = $atendente->image_url;
                        $atendente_nome = ucwords($atendente->nome);

                        $bloqueio_auto = @$atendente->status == "1" ? 'Sim': 'Não';

                        $atendente_status = @$atendente->acesso != "0" ? 
                        '<span class="fw-semibold text-success">Ativo</span>' 
                        : '<span class="fw-semibold text-danger">Inativo</span>'; 
                        
                        // Último acesso
                        $data_formatada = strftime('%e de %B de %Y',  strtotime($atendente->created_at));
                        // Formatação da hora
                        $hora_formatada = date('H:i', strtotime(trim($atendente->created_at)));

                        $ultima_acesso_formatada = "$data_formatada às $hora_formatada";
                        // fim

                        if (@$atendente->horaentrada && @$atendente->horasaida) 
                        {
                          // Horário de início e término
                          $inicio = DateTime::createFromFormat('H:i:s', $atendente->horaentrada);
                          $fim = DateTime::createFromFormat('H:i:s', $atendente->horasaida);
                        
                          // Formatação para 'H:i'
                          $atendente->horaentrada = $inicio->format('H:i');
                          $atendente->horasaida = $fim->format('H:i');
                        
                          // Horário atual
                          $agora = new DateTime();
                        
                          // Verifica se o horário atual está entre o horário de início e término
                          if ($agora >= $inicio && $agora <= $fim) {
                              $informacao_horario = '<span class="text-muted">Está dentro do horário.</span>';
                          } else {
                              $informacao_horario = '<span class="text-muted">Não está dentro do horário.</span>';
                          }

                          $horario_trabalho = "$atendente->horaentrada - $atendente->horasaida, $informacao_horario";
                        }
                        
                        
                    ?>
                        <tr role="button" class="table-row-fomidable to-hover px-4"
                            data-bs-useclass="table-active"
                            data-bs-open="modal"
                            href="?user=<?= $atendente->id ?>"
                            data-bs-modaltype="modal-fullscreen-md-down"
                        >
                            <!-- Nome -->
                            <td scope="row">
                                <img class="bg-light rounded me-1"
                                    alt="" width="32" height="32" 
                                    src="<?= $atendente_imagem ?>" 
                                    data-srcset="<?=$baseURL?>/profile_image.php?fullname=<?= $atendente_nome ?>" 
                                    onerror="defaultImage(this)"
                                />
                                <small class="fw-semibold text-nowrap">
                                    <?= $atendente_nome ?>
                                </small>
                            </td>
                            
                            <!-- Horáio de Trabalho -->
                            <td scope="row">
                                <small class="fw-semibold text-nowrap">
                                    <?= $tipos[$atendente->type] ?>
                                </small>
                            </td>

                            <!-- Status -->
                            <td scope="row">
                                <small class="fw-semibold text-nowrap">
                                    <?= $atendente_status ?>
                                </small>
                            </td>

                            <!-- Bloqueio Automático -->
                            <td scope="row">
                                <small class="fw-semibold text-nowrap">
                                    <?= $bloqueio_auto ?>
                                </small>
                            </td>

                            <!-- Último Acesso -->
                            <td scope="row">
                                <small class="fw-semibold text-nowrap">
                                    <?= $ultima_acesso_formatada ?>
                                </small>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>
</div>
