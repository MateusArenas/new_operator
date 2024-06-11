<?php 
    date_default_timezone_set('America/Sao_Paulo');
    header("Access-Control-Allow-Origin: *");

    session_start(); 

    require_once('./config.php');

    // redireciona para rota inicial automaticamente (url amigável)
    if (!@$_GET['page']) { 
      $_GET['page'] = "realizar_chamado";
    }

    require_once('./classes/Helpers.class.php');
    require_once('./classes/Database.class.php');
    // tem que incluir devido as telas.
    require_once('./classes/Functions.class.php');
    require_once('./classes/Tickets.class.php');
    require_once('./classes/Users.class.php');

    $db = new Database();


    $menus = [
      "painel_controle" => [
        "title" => 'Painel de Controle',
        "submenus" => [
            "realizar_chamado" => [
              "title" => 'Abrir Chamado',
              "link" => $baseURL . '/dashboard/realizar_chamado',
            ]
        ]
      ],

      "atendentes" => [
        "title" => 'Painel Administrativo',
        "submenus" => [
            "lista_atendentes" => [
              "title" => 'Lista de Atendentes',
              "link" => $baseURL . '/dashboard/lista_atendentes',
            ]
        ]
      ],

    ];


    $activeted = '';
    $submenu = [];
    // encontra o titulo da view
    foreach ($menus as $key => $item) {
      foreach ($item['submenus'] as $sub_key => $sub_item) {
        if (@$_GET['page'] === $sub_key) {
          $submenu = $sub_item;
          $activeted = $key;
        }
      }
    }
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    
    <title>Painel Short Link</title>
    
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" integrity="sha384-4LISF5TTJX/fLmGSxO53rV4miRxdg84mZsxmO8Rx5jGtp/LbrixFETvWa5a6sESd" crossorigin="anonymous"> -->

    <script src="<?=$baseURL?>/assets/js/axios/1.4.0/axios.js?v=<?=date('YmdHis')?>"></script>
    <script src="<?=$baseURL?>/assets/js/jquery-3.6.0.js?v=<?=date('YmdHis')?>"></script>
    <script src="<?=$baseURL?>/assets/js/jquery.mask.js?v=<?=date('YmdHis')?>"></script>

    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/default.min.css"> -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

    <!-- <script src='//cdnjs.cloudflare.com/ajax/libs/highlight.js/9.5.0/highlight.min.js'></script> -->
    
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/languages/xml.min.js"></script> -->
    
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.0/styles/atom-one-light.min.css"> -->
    <!-- <link rel='stylesheet' href='https://cdn.jsdelivr.net/foundation/6.2.0/foundation.min.css'> -->

    <script src='https://raw.githubusercontent.com/emmetio/textarea/master/emmet.min.js'></script>
    <script src='https://use.fontawesome.com/b2c0f76220.js'></script>

    <link rel="stylesheet" href="https://unpkg.com/highlightjs-copy/dist/highlightjs-copy.min.css"/>
    <script src="https://unpkg.com/highlightjs-copy/dist/highlightjs-copy.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- <link href="<?=$baseURL?>/assets//js//bootstrap-5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous"> -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link href="<?=$baseURL?>/assets/styles/atom-one-theme.css?v=<?=date('YmdHis')?>" rel="stylesheet" >

    <script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.22.2/dist/bootstrap-table.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.22.2/dist/extensions/sticky-header/bootstrap-table-sticky-header.min.js"></script>


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@100..800&display=swap" rel="stylesheet">
   
    <link href="<?=$baseURL?>/assets/styles/main.css?v=<?=date('YmdHis')?>" rel="stylesheet" >
    <link href="<?=$baseURL?>/assets/styles/sidebars.css?v=<?=date('YmdHis')?>" rel="stylesheet" >


    <script src="<?=$baseURL?>/assets/javascript/preloadimg.js?v=<?=date('YmdHis')?>"></script>
  

</head>

<body id="app" class="sora navpro">



    <div class="offcanvas navpro-menu offcanvas-start show" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="offcanvasMenu" aria-labelledby="offcanvasMenu">
      <!-- <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasLabel">Offcanvas</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        Content for the offcanvas goes here. You can place just about any Bootstrap component or custom elements here.
      </div> -->
      <!-- https://getbootstrap.com/docs/5.0/examples/sidebars/# -->
      <div class="d-flex justify-content-start flex-column h-100 w-100" >

        <div class="d-flex p-3 bg-dashboard">

            <button type="button" class="btn-close d-lg-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>

            <a href="./" class="text-decoration-none mx-2">
                <span class="fs-5 fw-bold">Operador</span>
            </a>
        </div>

        

        <ul class="nav p-3 navpro-nav nav-pills w-100 flex-column m-0 flex-fill flex-nowrap overflow-y-auto">
          
          <?php foreach ($menus as $key => $item) : ?>

            <li class="mb-2 nav-item w-100 rounded <?php if ($activeted == $key) echo 'bg-body-tertiary'; ?>">
              <button class="btn btn-sm btn-toggle w-100 align-items-center rounded <?php if ($activeted == $key) echo 'active'; ?>" 
                data-bs-toggle="collapse" 
                data-bs-target="#<?=$key?>-collapse" 
                aria-expanded="true"
              >
                <?= $item['title'] ?>
              </button>
              <div class="pb-2 collapse show" id="<?=$key?>-collapse">
                <ul class="btn-toggle-nav list-unstyled fw-normal ps-4 pe-2 py-2 small">

                  <?php foreach ($item['submenus'] as $sub_key => $sub_item) : 
                    if (@$sub_item['hide']) continue;
                  ?>
                    <li class="mb-1 <?php if(@$_GET['page'] == $sub_key) echo 'active' ?> ">
                      <a href="<?= $sub_item['link'] ?>" class="px-2 py-1 rounded"
                        <?php if ($target = @$sub_item['target']) echo "target='$target'" ?>
                      >
                        <?= $sub_item['title'] ?>
                      </a>
                    </li>
                  <?php endforeach; ?>

                </ul>
              </div>
            </li>

          <?php endforeach; ?>
           
          <!-- <li>
            <a href="#" class="nav-link  fw-bold">
              Components
            </a>
            <ul class="nav nav-pills flex-column ms-4">
              <li>
                <a href="#" class="nav-link ">
                  Buttons
                </a>
              </li>
            </ul>
          </li> -->
        </ul>

        <div class="d-flex flex-column align-items-start bg-dashboard p-3">

        </div>
      </div>
    </div>


    <div class="navpro-dashboard d-flex flex-column h-100 w-100">
      

        <div class="gap-3 d-flex flex-row border-bottom align-items-center justify-content-between px-3 py-2">
          
            <div class="d-flex align-items-center d-lg-none">
              <button type="button" class="navpro-link navpro-menu-btn btn btn-light text-primary me-3"
                data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasMenu"
                data-bs-config='{"backdrop":true}'
              >
                <i class="bi bi-list"></i>
              </button>
            
  
            </div>
  
<!-- 
                <h5 class="mb-0 fw-semibold ">
                  <?= @$submenu['title'] ?>
                </h5> -->

                <div class="d-flex align-items-center justify-content-center flex-grow-1">
                  <button type="button" class="btn btn-sm btn-outline-secondary text-start flex-grow-1"
                    style="max-width: 22em;"
                    data-bs-toggle="modal" 
                    data-bs-target="#docSearchModal"
                  >
                    <i class="bi bi-search me-1"></i>Buscar
                  </button>
                </div>
  



              <!-- Modal -->
              <div class="modal fade" id="docSearchModal" data-bs-keyboard="false" tabindex="-1" aria-labelledby="docSearchModal" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable pt-5">
                  <div class="modal-content">
                    <div class="modal-header">


                      <div class="d-flex align-items-center position-relative w-100">
                        <input class="form-control" 
                          type="search" 
                          placeholder="Buscar"
                          name="search"
                          style="padding-left: 40px;"
                        >
                        <span class="position-absolute start-0 m-3">
                          <i class="bi bi-search me-3"></i>
                        </span>
                      </div>


                    </div>
                    <div id="all-results" class="modal-body">

                      <div class="list-group" 
                        id="results"
                      >
                        <a class="list-group-item list-group-item-action d-flex align-items-center gap-3" 
                          href="#" 
                          id="docSearchListItem"
                          style="display: none !important;"
                        >
                          <span id="avatar">
                            <i class="bi bi-list fs-5"></i>
                          </span>
                          <div class="d-flex flex-column flex-grow-1 ">
                            <p class="mb-0" id="title">
                              Lista de Atendentes
                            </p>
                            <small id="description" class="text-muted number-of-lines-1"
                            >
                              <!-- The current button -->
                            </small>
                          </div>
                          <i class="bi bi-arrow-return-left fs-6"></i>
                        </a>
                      </div>

                    </div>
                  </div>
                </div>
              </div>

              <script>
                $(document).ready(function () {

                  var listarOperadores = function () {
                    return new Promise((resolve, reject) => {

                      var settings = {
                        "url": "<?= $baseURL ?>/api/listar-operadores",
                        "method": "GET",
                        "timeout": 0,
                        "headers": {
                          "token": "26d7c43e-504f-4bab-6777-8392fd4839ee",
                          // "Content-Type": "application/json"
                        },
                        // "data": JSON.stringify({
                        //   "login": "12296"
                        // }),
                      };

                      $.ajax(settings).done(function( data ) {
                        // alert( "success: " + JSON.stringify(data) );
                        // console.log({ data });
                        resolve(data);
                      }).fail(function(error) {
                        // alert( "error" );
                        reject(error);
                      });
                    });
                  }

                  function debounce(func, delay) {
                      let timeoutId;
                      return function(...args) {
                          clearTimeout(timeoutId);
                          timeoutId = setTimeout(() => {
                              func.apply(this, args);
                          }, delay);
                      };
                  }

                  var docSearchListItemEl = $('#docSearchListItem'); 

                  var docSearchModalEl = $('#docSearchModal');
                  var docSearchInputEl = docSearchModalEl.find('[name="search"]');

                  var resultsList = docSearchModalEl.find('#results');

                  docSearchModalEl.on('shown.bs.modal', () => {
                    docSearchInputEl.focus();
                  });

                  const data = [
                    { 
                      name: 'Lista de Atendentes', 
                      section: 'Painel Administrativo',
                      description: 'Todos os atendentes e suas informações como: Horários de trabalho, Último Acesso,Bloqueio Automático e status e situação.', 
                      keywords: ['lista de atendentes', 'atendentes', 'operadores', 'usuarios', 'usuários', 'todos os usuarios', 'Horários de trabalho'],
                      link: '<?= $baseURL?>/dashboard/lista_atendentes'
                    },
                    { 
                      name: 'Abrir de Chamado', 
                      section: 'Painel de Controle',
                      description: 'Abrir Chamado e Histórico de abertura de chamados', 
                      keywords: ['abrir de chamado', 'abertura de chamdo', 'histórico de chamado', 'histórico', 'chamados'],
                      link: '<?= $baseURL?>/dashboard/remover_leilao'
                    },
                  ];

                  listarOperadores().then(response => {
                    if (response.operadores) {
                      response.operadores.map(operador => {
                        console.log({operador});

                        var atendente_nome = operador?.nome;

                        data.push({
                          avatar: `
                            <img class="bg-light rounded me-1"
                                alt="" width="32" height="32" 
                                src="${operador?.image_url ?? ''}" 
                                data-srcset="<?=$baseURL?>/profile_image.php?fullname=${atendente_nome}" 
                                onerror="defaultImage(this)"
                            />
                          `,
                          section: 'Operadores',
                          name: atendente_nome, 
                          description: operador?.email ?? '', 
                          keywords: ['operador', 'usuario', 'atendente'],
                          link: `?user=${operador?.id}`
                        });
                      });
                    }
                  });

                  function removerAcentos(str) {
                      return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                  }

                  function handleInput(event) {
                    var query = event.target.value.toLowerCase();

                    var filteredData = [];

                    if (query.length > 0) {
                      
                      filteredData = data.filter(item => {
                        var _query = removerAcentos(query);
                        var item_name = removerAcentos(item.name);
                        var item_description = removerAcentos(item?.description ?? '');

                        if (item_name.toLowerCase().includes(_query)) {
                          return true;
                        }

                        if (item_description && item_description.toLowerCase().includes(_query)) {
                          return true;
                        }

                        if (item.keywords.some(keyword => removerAcentos(keyword).includes(_query))) {
                          return true;
                        }

                        return false;
                      });
                    }


                    var result = {};

                    filteredData.forEach((item) => {
                      if (result[item.section]) {
                        result[item.section].push(item);
                      } else {
                        result[item.section] = [item];
                      }
                    });

                    filteredData = Object.entries(result).map(([section, items]) => {
                      return ({ section, items })
                    })

                    displayResults(filteredData);
                  }

                  const debounceHandleInput = debounce(handleInput, 200);

                  docSearchInputEl.on('input', debounceHandleInput);

                  function displayResults(results) {
                    console.log({ results });

                    var parentEl = $('#all-results');

                    // limpa tudo.
                    parentEl.html('');
                    
                    results.forEach(section => {
                      
                      var sectionEl = document.createElement('p');
                      $(sectionEl).addClass('text-primary mb-1');
                      sectionEl.innerHTML = section.section;
                      parentEl.append(sectionEl);
                      
                      var resultsListCloneEl = resultsList.clone();
                      resultsListCloneEl.html('');
                      resultsListCloneEl.addClass('mb-3');
                      parentEl.append(resultsListCloneEl);

                      section.items.forEach((item) => {
                        var docSearchListItemCloneEl = docSearchListItemEl.clone();
                        docSearchListItemCloneEl.show();
  
                        if (item?.link) {
                          docSearchListItemCloneEl.attr('href', item.link);
                        }
  
                        docSearchListItemCloneEl.find('#title').html(item.name);
  
                        if (item?.description) {
                          docSearchListItemCloneEl.find('#description').html(item.description);
                        }
  
                        if (item?.avatar) {
                          docSearchListItemCloneEl.find('#avatar').html(item.avatar);
                        }
                        
                        resultsListCloneEl.append(docSearchListItemCloneEl);
                      });

                    });

                  }

                });
              </script>


          <div class="d-flex align-items-center gap-3 ">
            <div class="dropdown">
              <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-moon-stars-fill"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-sm"
                  style="z-index: 9999;"
              >
                <li>
                  <button type="button" id="theme-light" class="dropdown-item d-flex justify-content-between">
                    <span>
                      <i class="bi bi-sun-fill"></i> Light
                    </span>
                    <i class="bi bi-check-lg theme-check"></i>
                  </button>
                </li>
                <li>
                  <button type="button" id="theme-dark" class="dropdown-item d-flex justify-content-between">
                    <span>
                      <i class="bi bi-moon-stars-fill"></i> Dark 
                    </span>
                    <i class="bi bi-check-lg theme-check"></i>
                  </button> 
                </li>
                <li>
                  <button type="button" id="theme-auto" class="dropdown-item d-flex justify-content-between">
                    <span>
                      <i class="bi bi-circle-half"></i> Auto
                    </span>
                    <i class="bi bi-check-lg theme-check"></i>
                  </button>
                </li>
              </ul>
            </div>
  
            <div class="dropdown dropstart">
              <a href="#" class="text-decoration-none" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false" >
                <div class="position-relative" style="width: 30px;">
                    <img class="rounded"
                      alt="" width="30" height="30" 
                      src="<?= @$_SESSION["MSPerfilImagem"] ?>" 
                      data-srcset="<?=$baseURL?>/profile_image.php?fullname=<?= @$_SESSION["MSLogin"] ?>" 
                      onerror="defaultImage(this)"
                    >
                    
                    <span class="position-absolute translate-middle p-1 bg-success border border-2 border-themed rounded-circle"
                        style="bottom: -8px; right: -8px;"
                    >
                        <span class="visually-hidden">New alerts</span>
                    </span>
                </div>
              </a>
  
              <ul class="dropdown-menu bg-dashboard text-small shadow ms-2" 
                aria-labelledby="dropdownUser1"
                style="min-width: 16em; z-index: 9999;"
              >
  
                <li class="d-flex p-3">
                    <img class="rounded me-3" 
                      width="38" height="38"
                      src="<?=$baseURL?>/profile_image.php?user=<?= @$_SESSION["MSSlackId"] ?>" 
                      data-srcset="<?=$baseURL?>/profile_image.php?fullname=<?= @$_SESSION["MSLogin"] ?>" 
                      onerror="defaultImage(this)"
                    >
                    <div class="d-flex flex-column flex-grow-1">
                        <small class="fs-6 fw-semibold"><?= @$_SESSION["MSNome"] ?></small>
                        <small class="text-muted"><?= @$_SESSION["MSLogin"] ?></small>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
  
                <!-- <li><a class="dropdown-item" href="#">New project...</a></li> -->
                <!-- <li><a class="dropdown-item" href="#">Settings</a></li> -->
                <li>
                  <a class="dropdown-item" 
                    data-bs-open="modal"
                    href="?user=<?= @$_SESSION["MSId"] ?>"
                    data-bs-modaltype="modal-fullscreen-md-down"
                  >
                    Perfil
                  </a>
                </li>
  
                
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="<?=$baseURL?>/sair.php">
                      <small>Sair da conta</small>
                    </a>
                </li>
              </ul>
            </div>
          </div>
 

        </div>

        <div class="d-flex flex-column flex-fill" >

          <?php 


              switch (@$_GET['page']) {
                  case 'realizar_chamado':
                      require_once('./pages/realizar_chamado/index.php');
                      break;
                  // case 'consultas_veiculares':
                  //     require_once('./pages/consultas_veiculares.page.php');
                  //     break;
                  case 'lista_atendentes':
                      require_once('./pages/lista_atendentes.page.php');
                    break;
                  default:
                      break;
              }
          ?>
        </div>
    </div>

    <!-- <div class="container" style="padding-left: 280px;">


      <pre data-theme="w-100 atom-one-dark" style="display: flex; margin: 0;" >
        <code class="w-100 rounded language-php">
          &lt;body&gt;
              hello = "hello";
              if(hello == "hello") {
                  &lt;p&gt;The condition is true!&lt;/p&gt;
              } else {
                  &lt;p&gt; The condition is false...&lt;/p&gt;
              }
          &lt;/body&gt;
        </code>
      </pre>

    </div> -->


<!-- <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#firstModal" data-bs-whatever="@mdo">
    Open modal for @mdo
</button> -->

<div id="spawn-modalize" class="d-none"></div>

<div class="modal modalize fade" id="firstModal" tabindex="-1" aria-labelledby="firstModalLabel" aria-hidden="true"
    data-bs-backdrop="static"
>
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">

      <!-- <div class="modal-header">
        <i class="bi bi-broadcast me-2"></i>
        <h1 class="modal-title fs-5" id="firstModalLabel"></h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div> -->

      <div class="modal-body">
        <div class="d-flex flex-column align-items-center justify-content-center h-100 w-100 p-4">
          <div class="spinner-border text-primary mb-2" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <small class="fw-semibold">Carregando...</small>
        </div>
      </div>
      
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Send message</button>
      </div> -->
    </div>
  </div>
</div>



<div class="modal modalize fade" id="secondModal" tabindex="-1" aria-labelledby="secondModal" aria-hidden="true"
    data-bs-backdrop="static"
>
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">


      <div class="modal-body">
        <div class="d-flex flex-column align-items-center justify-content-center h-100 w-100 p-4">
          <div class="spinner-border text-primary mb-2" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <small class="fw-semibold">Carregando...</small>
        </div>
      </div>
      
    </div>
  </div>
</div>

<div class="modal modalize fade" id="thirdModal" tabindex="-1" aria-hidden="true"
    data-bs-backdrop="static"
>
  <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-body">
        <div class="d-flex flex-column align-items-center justify-content-center h-100 w-100 p-4">
          <div class="spinner-border text-primary mb-2" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <small class="fw-semibold">Carregando...</small>
        </div>
      </div>
      
    </div>
  </div>
</div>


<div class="modal fade" id="loadingModal" tabindex="-1" 
  data-bs-backdrop="static"
>
  <div class="modal-dialog modal-sm modal-dialog-scrollable modal-dialog-centered">
    
    
    <div class="modal-content">
      <div id="loading-modal-header" class="modal-header" style="display: none;">
        <h1 class="modal-title fs-5">Alerta</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">

        <div id="loading-modal-spinner" class="h-100 w-100">
          <div class="d-flex flex-column align-items-center justify-content-center h-100 w-100 p-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <small class="fw-semibold">Carregando...</small>
          </div>
        </div>

        <div id="loading-modal-error" class="alert alert-danger text-break" role="alert" style="display: none;">
          Erro Genérico (500).
        </div>
      </div>
      
    </div>
  </div>
</div>


  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>


  <script src="<?=$baseURL?>/assets/javascript/navigation.js"></script>
  <script src="<?=$baseURL?>/assets/javascript/popover_config.js"></script>

<script>
  $(document).ready(function() {
    
    var loadingModalEl = $('#loadingModal');
    var loadingModal = new bootstrap.Modal(loadingModalEl);

    var headerEl = loadingModalEl.find('#loading-modal-header');
    var spinnerEl = loadingModalEl.find('#loading-modal-spinner');
    var errorEl = loadingModalEl.find('#loading-modal-error');

    loadingModal.init = () => {
      loadingModal

      headerEl.hide();
      errorEl.html('Erro Genérico (500).');
      errorEl.hide();
      spinnerEl.show();
      loadingModal.show();
    }

    loadingModal.error = (message) => {
      headerEl.show();
      errorEl.html(message);
      errorEl.show();
      spinnerEl.hide();
    }

    // está dando bug o modal hide do bootstrap

    $(document).on('submit', '[data-form-type="ajax"]', function(event) {
      event.preventDefault();

      loadingModal.init();

      var form = $(this);

      // aqui permite que os parametros sejem mostrados na url
      var showParams = form.attr('data-show-params');
      var formCleanup = form.attr('data-form-cleanup');

      var submitterEl = $(event.originalEvent.submitter);

      var target = form.attr('data-form-target');
      var targetEl = $(target);

      var render = (html) => targetEl.html(html);

      // caso seja para substituir
      if (form.attr('data-form-replace') == 'true') {
        render = (html) => {
          // Encontre o pai desse elemento
          var parentEl = targetEl.parent();

          // Determine a posição do elemento a ser substituído em relação aos seus irmãos
          var targetElIndex = parentEl.children().index(targetEl);

          // Crie um novo elemento
          var newEl = $(html);
          
          // Insira o novo elemento na posição exata onde o anterior estava
          parentEl.children().eq(targetElIndex).after(newEl);

          // Remova o elemento que você deseja substituir
          targetEl.remove();

          targetEl = newEl;

          return targetEl;
        };
      }

      var actionUrl = form.attr('action');

      var submitterFormaction = submitterEl.attr('formaction');

      if (submitterFormaction) {
        actionUrl = submitterFormaction;
      }

      var url = actionUrl;
      var method = form.attr('method');

      console.log({ data: form.serialize(), array: form.serializeArray(), url });
      
      // aqui permite que os parametros sejem mostrados na url
      if (showParams || showParams == 'true') {
        form.serializeArray().forEach(el => {
          if (el.value) {
            insertParam(el.name, el.value);
          } else {
            removeParam(el.name);
          }
        });
      }
      
      $.ajax({
        url,
        type: method,
        data: form.serialize(), // serializes the form's elements.
        cache: false, // Evitar cache
        success: (html) => {
          if (html) {
            setTimeout(() => {
              console.log({ targetEl, html});            
              render(html);
  
              // serve para limpar o formulario
              if (formCleanup || formCleanup == "true") {
                form.trigger('reset');
              }

              // targetEl.change();
              loadingModal.hide();
            }, 900);

          } else {
            // console.log('not: ', html);
            setTimeout(() => {
              loadingModal.error("Erro de conexão. Verifique sua conexão com a internet");
              render('');
            }, 900);
          }
          
        },
        error: function(result) {
          setTimeout(() => {
            // console.log('e: ', result);
            var message = '';
            message += `(${result?.status ?? '500'}) ${result?.statusText ?? 'Error'}`;
            message += '<br>';
            message += '<br>';
            message += `<a href="${url}" target="_blank" >${url}</a>`;
            loadingModal.error(message);
            render('');
          }, 900);
        }
      });

    });


  });
</script>

  <!-- Configura para as linhas da tabela sejem clicaveis -->
  <!-- <script>
    $(function(){
        $('.table tr[data-href]').each(function(){
            $(this).css('cursor','pointer').hover(
                function(){ 
                    $(this).addClass('active'); 
                },  
                function(){ 
                    $(this).removeClass('active'); 
                }).click( function(){ 
                    document.location = $(this).attr('data-href'); 
                }
            );
        });
    });
  </script> -->

  <script>
    $(document).ready(function() {
      // hljs.addPlugin(
      //   new CopyButtonPlugin({
      //     callback: (text, el) => console.log("Copied to clipboard", text),
      //   })
      // );
    });
  </script>

  <script>
    $(document).ready(function() {
        // hljs.highlightAll();
    });
  </script>

  <script>
    function update() {
        // hljs.highlightAll();
    }
  </script>

  <script>
      // document.addEventListener('DOMContentLoaded', (event) => {
      //   document.querySelectorAll('pre code').forEach((block) => {
      //     hljs.highlightElement(block);
      //   });
      // });

  </script>

  <script>
    function copyTextToClipboard(id) {
      // Get the text field
      var copyText = document.getElementById(id);

      const inputEl = document.createElement('input');
      inputEl.setAttribute('value', copyText.innerText);
      inputEl.setAttribute('type', 'hidden');

      copyText.appendChild(inputEl);

      // Select the text field
      inputEl.select();
      inputEl.setSelectionRange(0, 99999); // For mobile devices

      // Copy the text inside the text field
      navigator.clipboard.writeText(inputEl.value);

      // Alert the copied text
      alert("Copied the text: " + inputEl.value);
    }
  </script>


  <script>
    
    $(document).ready(function() {
      $(document).on('click', '[data-bs-toggle="form"]', function(event) {
        const target = $(this).attr('data-bs-target');
        if (target) {
          const targetEl = $(target);
          if (targetEl) targetEl.submit();
        }
      });
    });

  </script>

  <script>



  </script>

  <script>
      // -------------------------------
    // Modal
    // -------------------------------
    // Modal 'Varying modal content' example in docs and StackBlitz
    // js-docs-start varying-modal-content

    // $( "#myModal" ).modal( "show", $( "#buttonBeingClicked" ) );

    function guidGenerator() {
      var S4 = function() {
        return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
      };
      return (S4()+S4()+"-"+S4()+"-"+S4()+"-"+S4()+"-"+S4()+S4()+S4());
    }

    
    // esse código se refere a linha da tabela selecionada.
    
    $(document).on('click', '[data-bs-open="modal"]', function(event) {
      var currentEl = $(this);
      
      var parent = currentEl.parent();
      
      var table = parent.parents('table');
      
      if (table) {
        table.find('.table-active').removeClass('table-active');
        
        if (currentEl.is('tr')) {
          currentEl.addClass('table-active');
        } else {
          currentEl.parents('tr').addClass('table-active');
        }
        
      }
    });

    var initialModalize = $('.modalize');

    function instanciateModalize ({ relatedTargetEl }) {
      var modalCloneEl = initialModalize.first().clone();

      var spawn = $('.modalize').last();

      spawn.after(modalCloneEl);

      var modalizeId = 'modalize' + guidGenerator();
      
      modalCloneEl.attr('id', modalizeId);

      $(relatedTargetEl).attr('data-bs-target', ("#" + modalizeId));

      modalCloneEl.modal("show" , relatedTargetEl);

      return modalCloneEl;
    }
    

    $(document).on('click', '[data-bs-open="modal"]', function(event) {
      event.stopPropagation();

      var relatedTargetEl = $(this);

      const template = relatedTargetEl.attr('data-bs-template');

      if (template) { // somente se for do tipo template, caso contrario ele roda lá embaixo.
        instanciateModalize({ relatedTargetEl });
      }

    });

    $(document).ready(function() {
      
      $(document).on('show.bs.modal', '.modalize', async function (event) {
        var modalize = $(this);

        const button = $(event.relatedTarget);

        const template = button.attr('data-bs-template');

        const modaltype = button.attr('data-bs-modaltype');

        var modalizeStyles = {
          fullscreen: 'modal-fullscreen',
          xl: 'modal-xl',
          lg: 'modal-lg',
          sm: 'modal-sm',
          ['modal-fullscreen-md-down']: 'modal-fullscreen-md-down',
        };

        Object.values(modalizeStyles).forEach(className => {
          modalize.find('.modal-dialog')
          .removeClass(className);
        })

        if (modaltype) {
          var className = modalizeStyles[modaltype];

          console.log({ className });

          if (className) {
            modalize.find('.modal-dialog')
            .addClass(className);
          }
        }

          const modalContent = modalize.find('.modal-content');
          
          const jsonb64 = button.attr('data-bs-jsonb64');

          // var req = `<?= $baseURL?>/minify.php?f=${template}`;
          var req = template;

          $.post(req, { jsonb64: jsonb64 })
          .done(function (html) {
            modalContent.html(html);

            modalContent.find('*').change();
          })
          .fail(function (error) {
            console.log({ error });
          });
              
      });
      
      $(document).on('hide.bs.modal', '.modalize', async function (event) {
        setTimeout(() => $(this).remove(), 300);
      });
     
    });

    
    function createModalize ({ template, modaltype }) {
      var buttonEl = document.createElement('button');
      var relatedTargetEl = $(buttonEl);
      
      relatedTargetEl.attr('data-bs-open', "modal");
      relatedTargetEl.attr('data-bs-template', template);
      relatedTargetEl.attr('data-bs-modaltype', modaltype);

      setTimeout(() => relatedTargetEl.remove(), 900);

      return instanciateModalize({ relatedTargetEl });
    }




    // $(document).ready(function() {
    //   $(document).on('change', function () {
    //     document.querySelectorAll('.selectable')
    //     .forEach(select => {
    //       select.addEventListener('change', async event => {
    
    //         console.log({ target: event.target });
    
    //         // const target = $(select).getAttribute('data-target');
    
    //       });
    //     });
    //   });
    // });


    $(document).ready(function() {
      $(document).on('change', '.selectable', function(event) {
        const selectedEl = event.target;

        [...selectedEl.options].forEach((optionEl, index) => {
          const selectedIndex = selectedEl.selectedIndex;

          const target = optionEl.getAttribute('data-target');
          const toggle = optionEl.getAttribute('data-toggle');

          if (target) {
            const targetEl = $(target);

            if (toggle === 'collapse') {
              if (selectedIndex === index) { 
                targetEl.addClass('show');
              } else {
                targetEl.removeClass('show');
              }
            }
          }
        
        });
      });

    });

  </script>


  <script>
    
    $(document).ready(function() {

      var copyToClipboard = async (text) => {
        try {
          await navigator.clipboard.writeText(text);
        } catch (error) {
          console.error("Failed to copy to clipboard:", error);
        }
      };

      $(document).on('click', '[data-toggle="copy"]', function(event) {
        const buttonEl = $(this);

        console.log({ buttonEl });

        const target = buttonEl.attr('data-target');
        
        if (target) {
          const targetEl = $(target);
          const targetText = targetEl.text();
          console.log({ target });
          copyToClipboard(targetText);
        }
      });

    });
  </script>


<!-- navpro -->
<script>
  $(document).ready(function() {
    $(document).on('click', '[data-toggle="touch"]', function(event) {
      const buttonEl = $(this);

      const target = buttonEl.attr('data-target');
      const className = buttonEl.attr('data-class');
      
      if (target && className) {
        const targetEl = $(target);

        if (targetEl) {
          if (targetEl.hasClass(className)) {
            targetEl.removeClass(className);
          } else {
            targetEl.addClass(className);
          }
        }
      }
    });
  });
</script>

<!-- CODE EDITOR -->
<script src="<?=$baseURL?>/assets/javascript/codeeditor.js?v=<?=time()?>"></script>
<!-- THEMED -->
<script src="<?=$baseURL?>/assets/modules/theme.js?v=<?=time()?>"></script>


<script>
    $(document).ready(function() {
      // Verifica se a tela está no breakpoint 'sm' ou menor
      var offcanvasEl = $('#offcanvasMenu');

      var appEl = $('#app');

      var matche_mobile = false;
      var matche_descktop = false;

      var bsOffcanvas = new bootstrap.Offcanvas(offcanvasEl);

      function checkScreenSize() {
        if (window.matchMedia('(max-width: 992px)').matches) {
          if (!matche_mobile) {
            offcanvasEl.removeClass('show');
            // Se estiver no breakpoint 'sm' ou menor, esconde o offcanvas
  
            offcanvasEl.attr('data-bs-backdrop', true);
            offcanvasEl.change();
  
            bsOffcanvas.hide();

            matche_mobile = true;
            matche_descktop = false;
          }
        } else {
          if (!matche_descktop) {
            offcanvasEl.attr('data-bs-backdrop', false);
            offcanvasEl.change();

            bsOffcanvas.show();

            matche_descktop = true;
            matche_mobile = false;
          }
        }
      }
    
      checkScreenSize();
      // Executa a verificação ao carregar a página e redimensionar a tela
      window.addEventListener('load', checkScreenSize);
      window.addEventListener('resize', checkScreenSize);
    });

</script>

<script>
  // Função para observar mudanças no DOM
  function observeDOM(callback) {
      // Crie uma nova instância de MutationObserver
      var observer = new MutationObserver(function(mutations) {
          // Iterar sobre as mutações
          mutations.forEach(function(mutation) {
              // Verificar se a mutação é uma inserção de nó
              if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                  // Iterar sobre os nós adicionados
                  mutation.addedNodes.forEach(function(node) {
                      // Verificar se o nó é um elemento HTML
                      if (node.nodeType === Node.ELEMENT_NODE) {
                        callback?.(node);
                      }
                  });
              }
          });
      });

      // Defina as opções de observação
      var config = {
          childList: true, // Observar mudanças na lista de filhos do nó alvo
          subtree: true // Observar todos os nós descendentes do nó alvo
      };

      // Alvo para observação (todo o documento neste caso)
      var target = document.documentElement;

      // Inicie a observação do DOM
      observer.observe(target, config);
  }

  $(document).ready(function() {
    // observeDOM(function (node) {
    //     // Verificar se o elemento possui o atributo data-srcset
    //     if ($(node).is('[data-srcset]')) {

    //         console.log({ node });
    //         // Capturar o elemento com o atributo data-srcset
    //         console.log('Elemento com data-srcset inserido:', node);
    //         // Use AJAX para carregar a imagem mais pesada em segundo plano
    //         var srcset = $(node).attr('[data-srcset]');
    //         $.ajax({
    //             url: srcset,
    //             success: function() {
    //                 // Quando a imagem for carregada com sucesso
    //                 // Atualize o atributo src da tag img para exibir a imagem pré-carregada
    //                 $(node).attr('src', srcset);
    //             }
    //         });
    //     }
    // });


    // Quando a página estiver completamente carregada
    // $(window).on('load', function() {
        
    //     // Use AJAX para carregar a imagem mais pesada em segundo plano
    //     $.ajax({
    //         url: 'caminho/para/imagem/pesada.jpg',
    //         success: function() {
    //             // Quando a imagem for carregada com sucesso
    //             // Atualize o atributo src da tag img para exibir a imagem pré-carregada
    //             $('#imagem').attr('src', 'caminho/para/imagem/pesada.jpg');
    //         }
    //     });
    // });
    // Capturar o evento de carregamento de uma imagem por meio do atributo src

    $('img').on('load', function() {
        // console.log('Imagem carregada:', this);
        // Execute qualquer ação necessária após o carregamento da imagem
    });

    $(document).on('load', 'img', function(event) {
      // console.log({ img: this });
    });
});

</script>


<script>
  function replaceContext (text, params) {
    return text.replace(/{{(.*?)}}/g, match => params[match.slice(2, -2)]);
  }

  function handleModalScreens (linking) {

    var searchUrl = window.location.search;

    var searchParams = new URLSearchParams(searchUrl);

    searchParams.forEach(function(value, key) {
      console.log({ value, key });

      if (key in linking) {
        var route = linking[key];

        var params = route.params.reduce((acc, key) => {
          return { ...acc, [key]: searchParams.get(key) }
        }, {});
        
        var template = replaceContext(route.url, params);
  
        var modalUser = createModalize({ template });
  
        // modalUser.modal('toggle'); // para fechar o modal
        modalUser.on('hide.bs.modal', function () {
          route.params.forEach(param => removeParam(param));
        });
      }

    });

  }

  function initialize (linking) {
    handleModalScreens(linking);
  
    $(document).on('click', '[data-bs-open="modal"]', function(event) {
      var href = $(this).attr('href');
  
      if (href && href !== '#') {
        event.preventDefault();
  
        var searchParams = new URLSearchParams(href);
  
        // Itera sobre os parâmetros da string de consulta
        searchParams.forEach(function(value, key) {
          insertParam(key, value);
        });
  
        handleModalScreens(linking);
      }
    });
  }


  var linking = {
    "user": {
      params: ['user'],
      url: `<?= $baseURL ?>/pages/perfil_operador.page.php?operador={{user}}`,
    },
  };


  $(document).ready(function() {
    initialize (linking)
  });


</script>


<script>

    $(document).on('click', '[data-bs-action="copy"]', function(event) {
      event.preventDefault();

      var targetEl = $(this);

      if (!targetEl.attr('disabled')) {

        var value = targetEl.attr('value');
  
        var inputEl = document.createElement('input');
  
        inputEl.setAttribute('value', value);
        inputEl.setAttribute('type', 'text');
  
        $(inputEl).hide();
  
        $('body').append(inputEl);
  
        // Select the text field
        inputEl.select();
        inputEl.setSelectionRange(0, 99999); // For mobile devices
  
        // Copy the text inside the text field
        navigator.clipboard.writeText(inputEl.value);
  
        var duration = targetEl.attr('data-bs-duration');
  
        var content = targetEl.html();
  
        var placeholder = targetEl.attr('placeholder');
  
        var width = targetEl.width();
        
        targetEl.css("width", width);
        targetEl.html(placeholder);
        
        targetEl.attr('disabled', true);
  
        setTimeout(() => {
          targetEl.html(content);
  
          targetEl.removeAttr('disabled');
          targetEl.css("width", "");
  
        }, Number(duration) ?? 600);
      }

    });
</script>

<script>


</script>

</body>
</html>