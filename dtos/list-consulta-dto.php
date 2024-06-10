<?php 

class ListConsultaDTO
{
    public array $orderby_asc;
    public array $orderby_desc;

    public int $login;
    public string $data;
    public string $parametro;

    public int $tipoconsulta;
    public int $atividade;

    public string $estado;

    public ?bool $restricao = null;
    public ?bool $leilao = null;
    public ?bool $sinistro = null;
    public ?bool $hist_rf = null;
    public ?bool $score = null;

    public string $operador;

    public int $offset = 0;
    public int $limit = 20;
}

?>
