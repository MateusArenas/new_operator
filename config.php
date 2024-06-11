<?php 

if(strpos("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", 'localhost') != false)
{
  $baseURL = 'http://localhost/new_operator';
} 
else 
{
  $baseURL = 'https://storedine.com';
}