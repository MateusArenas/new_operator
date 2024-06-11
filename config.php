<?php 
    @date_default_timezone_set('America/Sao_Paulo');
    @header("Access-Control-Allow-Origin: *");
    @setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');

if(strpos("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", 'localhost') != false)
{
  $baseURL = 'http://localhost/new_operator';
} 
else 
{
  $baseURL = 'https://storedine.com';
}