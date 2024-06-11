<?php
    $caminho_index = realpath(__DIR__ . '/../index.php');
    var_dump($caminho_index);
    require_once realpath(__DIR__ . '/../index.php'); 
?>