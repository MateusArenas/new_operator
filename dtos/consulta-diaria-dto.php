<?php 

class ConsultaDiariaDTO
{
    public array $orderby_asc;
    public array $orderby_desc;
    
    public ?string $filtroData = null;
    public ?int $tipoconsulta = null;
    public ?string $vPlaca = null;
    public ?int $vcodAtendente  = null;
    public ?int $vrestricao = null;
    public ?int $vramo = null;
    public ?string $vestado = null;
    public ?int $CodCliente	= null;
}

