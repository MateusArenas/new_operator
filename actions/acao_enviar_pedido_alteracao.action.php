<?php 

  function escapeHtml($unsafe) {
    return htmlspecialchars($unsafe, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
  }

//   header("Content-type: application/xhtml+xml");

  if ($xml = @$_REQUEST['bin_online']) {
    echo $xml;

    // $xml = escapeHtml($xml);

    // echo $xml;

    // $xmlObject = simplexml_load_string($xml);
    
    // echo $xmlObject->asXML();
  } else {
    // Caso haja algum erro no upload do arquivo
    echo "Erro ao fazer o upload do arquivo.";
  }
?>