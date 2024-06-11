<?php
header('content-type: application/json');
// Configuração do cabeçalho Content-Disposition
header('Content-Disposition: inline');

session_start(); 
echo json_encode(['time' => time(), 'date' => date('d.m.Y'), 'tech' => 'Vercel']);
    
    // if(@$_REQUEST['base64'])
    // {
    //     $redirect = urldecode(@$_GET['redirect']);
    //     $redirect = base64_decode($redirect);
    // }
    // else
    // {
    //     $redirect = urldecode(@$_GET['redirect']);
    // }

    // if(isset($_SESSION['MSLogin'])){ 
    //     if($redirect) header("location:{$redirect}"); 
    //     else header('location:dashboard2.php?Pagina=RelatorioDiario'); 
    //     exit; 
    // }

    // header('location: login.php'); 
?>