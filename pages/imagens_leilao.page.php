<?php
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(0);

@session_start();

$ses_id = session_id ();

@include_once('../config.php');
require_once('../classes/Database.class.php');

#pagina de verificao de login do usuario, se cliente carrega sessao

$p_dados = $_GET["placa"];

// Conversão de placa padrão mercosul

class ConvercaoPlaca
{
	public $placa;
	public $tabela = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');

	public function converterPlaca($placa = '')
	{
		$this->placa = strtoupper($placa);

		$validacaoPlaca = $this->conferirPlaca();
		if ($validacaoPlaca != 'OK')
			return array('erro' => 'ERRO', 'response' => $validacaoPlaca);

		$isPlacaMercosul = $this->isPlacaMercosul();
		if (!$isPlacaMercosul['RESPONSE'])
			return array('erro' => 'OK', 'response' => $this->placa);

		$stringConvertida = array_search($isPlacaMercosul['VAL'], $this->tabela);
		if ($stringConvertida === false)
			return array('erro' => 'ERRO', 'response' => 'LETRA INVALIDA NO PADRAO MERCOSUL: ' . $isPlacaMercosul['VAL']);

		return array('erro' => 'OK', 'response' => substr($this->placa, 0, 4) . $stringConvertida . substr($this->placa, 5, 2));
	}

	private function conferirPlaca()
	{
		$this->placa = str_replace('-', '', $this->placa);

		if ($this->placa == '')
			return 'SEM PLACA';

		if (strlen($this->placa) != 7)
			return 'PLACA INVALIDA';

		return 'OK';
	}

	private function isPlacaMercosul()
	{
		$val = substr($this->placa, 4, 1);
		return array('VAL' => $val, 'RESPONSE' => !is_numeric($val));
	}


}

$obj = new ConvercaoPlaca();
$response = $obj->converterPlaca($p_dados);
if ($response['erro'] != 'OK') {
  header ("content-type: application/xml");
  print "<?xml version='1.0' encoding='ISO-8859-1'?><pesquisa><ERRO>ERRO NA PLACA: {$response['response']}</ERRO></pesquisa>";
  exit;
}

$p_dados = $response['response'];


function ConsultaEspecial($Cod){
    # Conecta com base de dados
    $db = new Database;

    $db->base="leilao";

    #Procura no campo agregado a consulta XML salva
    $sql = ("SELECT * FROM tb_leilao_credauto WHERE Placa ='".$Cod)."'";

    $db->query = $sql;
    $row = $db->selectOne();


    $Envia["leilao_img"] = $row->leilao_img;

    $XML_leilao_img	= simplexml_load_string($row->leilao_img);


    // $XML_leilao   =  $XML_leilao_img->LEILAO2;

    //print_r($XML_leilao);
    //exit;

    //PEGA OS DADOS DO XML QUE PODEM SE REPETIR
    $contador = 0;

    if (@$XML_leilao_img->LEILAO1->imagens) {
        foreach($XML_leilao_img->LEILAO1->imagens as $tudo){
    
            if(@file_get_contents($tudo)){
                $Envia["imagens"][$contador]			=  $tudo;
                $contador++;
            }
        }
    }    

    $Envia["contador"] = $contador;


    #######################################################################################

    $contadorv = 0;

    if (@$XML_leilao_img->LEILAO2->imagens) {
        foreach($XML_leilao_img->LEILAO2->imagens as $tudo){
            if(@file_get_contents($tudo)){
                $Envia["imagens_v"][$contadorv]			=  $tudo;
                $contadorv++;
            }
        }
    }

    $Envia["contador_v"] = $contadorv;
    #######################################################################################
    return $Envia;
}

$Retorno = ConsultaEspecial($p_dados);


//   $flag = (  @$_SESSION["MSId"] !="" );
  $flag = true;

      if ( $flag ) {


  if ( $Retorno["contador"] == "" ){

      $risco_img = file_get_contents("https:/webservice.redecredauto.com.br/pro_auto/pro_leilao_img.php?c=1235972356121190863100012001$p_dados++++++++++++++X");

  }




?>




<script type="text/javascript">
function disableSelection(target){
if (typeof target.onselectstart!="undefined") //IE route
target.onselectstart=function(){return false}
else if (typeof target.style.MozUserSelect!="undefined") //Firefox route
target.style.MozUserSelect="none"
else //All other route (ie: Opera)
target.onmousedown=function(){return false}
target.style.cursor = "default"
}
</script>





<?php 
  // get the raw POST data
  $json_base64 = $_POST['jsonb64'];
    
   /**
   * Returns the JSON encoded POST data, if any, as an object.
   * 
   * @return Object|null
   */
  function retrieveJsonBase64PostData()
  {
    global $json_base64;

    $stringfy = base64_decode($json_base64);

    // this returns null if not valid json
    return json_decode($stringfy);
  }

  $props = retrieveJsonBase64PostData();

?>

<div class="modal-header align-items-start">
    <div class="d-flex">
        <div class="d-flex flex-column">
            <h1 class="modal-title fs-5 mb-2" >
              <i class="bi bi-camera-fill me-2"></i>Fotos Leilão
            </h1>
            <small class="fw-semibold text-muted">
              Consulta: <span class="fw-bold"><?= $props->consulta ?></span>
            </small>
            <small class="fw-semibold text-muted">
              Código: <span class="fw-bold"><?= $props->codigo ?></span>
            </small>
            <small class="fw-semibold text-muted">
               Parâmetro: <span class="fw-bold"><?= $props->parametro ?></span>
            </small>
            <small class="fw-semibold text-muted">
              Realizada em: <span class="fw-bold"><?= $props->data ?> às <?= $props->hora ?></span>
            </small>
        </div>
    </div>
    <button type="button" class="btn-close m-1" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">

    <?php if ( $Retorno["contador"] != "" ){ ?>

    <div class="table-responsive">
        <table border="0" cellpadding="4" width="100%" cellspacing="2" align="center" class="table bordaTabela">
        <tr class="bordatabela">
        <td>

        <?php for ($cont=0; $cont < $Retorno["contador"];$cont++){ ?>

            <!-- href='<?php echo $Retorno["imagens"][$cont]; ?>' 
            target="_blank" -->
            <a id='ft_img15' class='foto_carro' 
                href="#"
                data-bs-open="modal"
                data-bs-template="<?=$baseURL?>/pages/ver_imagem.page.php?imagem=<?= $Retorno["imagens"][$cont] ?>&consulta=<?= base64_encode($props->codigo) ?>"
                data-bs-jsonb64="<?= $json_base64 ?>"
                data-bs-modaltype="fullscreen"
            >
                <img src='<?php echo $Retorno["imagens"][$cont]; ?>'  width="250" height="250" border="0">
            </a>
        
        <?php }  ?>

        <?php if ( $Retorno["contador_v"] != "" ){ ?>
            
            <?php for ($cont=0; $cont < $Retorno["contador_v"];$cont++){ ?>

                <!-- href='<?php echo $Retorno["imagens_v"][$cont]; ?>' 
                target="_blank" -->
            <a id='ft_img15' class='foto_carro' 
                href="#"
                data-bs-open="modal"
                data-bs-template="<?=$baseURL?>/pages/ver_imagem.page.php?imagem=<?= $Retorno["imagens"][$cont] ?>&consulta=<?= base64_encode($consulta->codigo) ?>"
                data-bs-jsonb64="<?= $json_base64 ?>"
                data-bs-modaltype="fullscreen"
            >
                <img src='<?php echo $Retorno["imagens_v"][$cont]; ?>'  width="250" height="250" border="0">
            </a>

        <?php } ?>

            <td>  
        </tr>
        </table>
        <?php } ?>
    </div>


    <?php } 


    }else {

    echo "<script>alert('SESSÃO ENCERRADA !');</script>";
    echo "<script>self.close();</script>";
    exit;


    }


    ?>

</div>



<script type="text/javascript">
    disableSelection(document.body); //disable text selection on entire body of page
</script>

