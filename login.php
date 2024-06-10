<?php
    session_start();

    include("acesso_login.php");

    if(strpos("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", 'localhost') != false)
    {
        $url = 'http://localhost/new_operator/dashboard.php';

        if (!@$_REQUEST['base64']) $_GET['redirect'] = $url;
        else $_GET['redirect'] = base64_encode($url);
    } else {
        $url = 'https://painel.credoperador.com.br/dashboard.php';

        if (!@$_REQUEST['base64']) $_GET['redirect'] = $url;
        else $_GET['redirect'] = base64_encode($url);
    }

    if(@$_REQUEST['base64'])
    {
        $redirect = urldecode(@$_GET['redirect']);
        $redirect = base64_decode($redirect);
    }
    else
    {
        $redirect = urldecode(@$_GET['redirect']);
    }

    if(isset($_SESSION['MSLogin'])){ 
        if($redirect) header("location:{$redirect}"); 
        else header('location:dashboard.php?Pagina=RelatorioDiario'); 
        exit; 
    }

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <script src="./assets/js/jquery-3.6.0.js?v=<?=date('YmdHis')?>"></script>

    <script src="./assets/modules/auto_theme.js?v=time()?>"></script>

    <link href="./assets/styles/main.css?v=<?=date('YmdHis')?>" rel="stylesheet" >

    <style>
        html, body {
            height: 100%;
            width: 100%;
        }

        [data-bs-theme="dark"] .dark-img {
            filter: brightness(0) invert(1);
        }
    </style>
</head>
<body style="background-color: #f0f4f91a;">

    <div class="container h-100">
        <div class="row h-100">
            <div class="col-12 d-flex flex-column justify-content-center align-items-center">

                
                <form class="card w-100 mb-4" 
                    style="max-width: 24rem;"
                    name="flogin" 
                    method="post" 
                    action="action-login.php" 
                >
                    <div class="card-header text-center">
                        <h3 class="fw-bold my-2"><i class="bi bi-gear-wide-connected"></i> Painel de Controle</h3>
                        <!-- ACESSO -->
                    </div>
                    <div class="card-body d-flex flex-column p-5">
                        <!-- <img src="assets/images/rca.svg" class="mb-5 dark-img" alt=""
                            style="width: 146px;"
                        > -->
                        <div class="d-flex align-items-center align-self-center mb-3">
                            <small class="fw-semibold me-2">Tema:</small>
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-moon-stars-fill"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-sm">
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
                        </div>
                        <div class="form-floating mb-3">
                            <input name="email" type="text" class="form-control" id="floatingInput" placeholder="name@example.com"
                                value="mateusarenas97@gmail.com"
                            >
                            <label for="floatingInput">Usuario:</label>
                        </div>
                        <div class="form-floating mb-3">
                            <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password"
                                value="operador12345"
                            >
                            <label for="floatingPassword">Senha:</label>
                        </div>

                        
                        <button href="index_oauth.php?autostart=1&redirect=<?php echo urlencode(base64_encode($redirect)); ?>&base64=1" 
                            class="btn btn-primary w-100"
                            type="submit"
                        >
                            Entrar
                        </button>
                    </div>
                </form>

                <small class="fw-semibold">© Todos os Direitos Reservados <?= date("Y") ?></small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
    
    <script src="./assets/modules/switch_theme.js?v=time()?>"></script>

</body>
</html>