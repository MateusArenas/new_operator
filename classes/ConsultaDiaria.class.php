<?php 

class ConsultaDiaria 
{
    private $db;

    function __construct()
    {
        $this->db = new Database();
    }

    function execute (ConsultaDiariaDTO $params) {
        try {

            // $filtroData = $_GET["FiltroDT"];
            // $tipoconsulta = $_REQUEST["tipoconsulta"];
            // $vPlaca  = trim($_REQUEST["vPlaca"]);
            // $vcodAtendente  = $_REQUEST["vcodAtendente"];
            // $vrestricao = $_REQUEST["vrestricao"];
            // $vramo = $_REQUEST["vramo"];
            // $vestado = $_REQUEST["vestado"];
            // $CodCliente	= $_REQUEST["CodCliente"];
                    
            $filtroData = $params->filtroData;
            $tipoconsulta = $params->tipoconsulta;
            $vPlaca  = $params->vPlaca;
            $vcodAtendente  = $params->vcodAtendente;
            $vrestricao = $params->vrestricao;
            $vramo = $params->vramo;
            $vestado = $params->vestado;
            $CodCliente	= $params->CodCliente;
            
            // $encodeAtendente = base64_encode($_SESSION['MSId']);
            $filtro_placa = false;

            $qRobo = "";

            if ( ( $tipoconsulta == "" ) && ( $vPlaca == "" )  && ( $CodCliente == "" ) && ( $vestado == "" ) && ( $vrestricao == "" )  && ( $vramo == "" ))
            {
                //print $filtro_placa;
            }
            else
            {
                    if($filtroData<>"")
                    {
                            $Filtro = explode("/", $filtroData);
                            $Filtrando = $Filtro[2]."-".$Filtro[1]."-".$Filtro[0];
                            #aparece consulta que tem que ser feita do dia
                            
                            if($CodCliente<>"")
                            {
                                $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'  AND a.Motivo = 3 AND a.Data = '".$Filtrando."' AND a.CodCliente = '".$CodCliente."' ORDER BY a.Codigo DESC";
                            }
                            else
                            {
                                if ($vestado != "")
                                {

                                    if ($tipoconsulta != "")
                                    {

                                        $qRobo = "SELECT a.ItemConsultado, a.restricao, a.restricao1, a.restricao2, a.restricao3, a.TipoConsulta, a.CodAtendente, a.atualiza, a.Data, a.data_altera,  a.Codigo, a.CodCliente, a.ValorItem, a.Hora, a.horaConc,  a.UF, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND  a.sistemanovo = 'S'  AND a.UF='".$vestado."' AND a.TipoConsulta=$tipoconsulta  AND a.Motivo2 = 1 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";

                                    }else {		

                                        $qRobo = "SELECT a.ItemConsultado, a.restricao, a.restricao1, a.restricao2, a.restricao3, a.TipoConsulta, a.CodAtendente, a.atualiza, a.Data, a.data_altera, a.Codigo, a.CodCliente, a.ValorItem, a.Hora, a.horaConc,  a.UF, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'  AND a.UF='".$vestado."' AND a.Motivo2 = 1 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                    
                                    }

                                }
                                else
                                {
                                    if ($tipoconsulta != "")
                                    {
                                        if (($tipoconsulta == 40) || ($tipoconsulta == 38))
                                        { 
                                            $tipo_sist ="N";
                                        }
                                        else
                                        {
                                            $tipo_sist = "S";
                                        }
                                        if ($vramo == "")
                                        {
                                            $qRobo = "SELECT a.ItemConsultado, a.restricao, a.restricao1, a.restricao2, a.restricao3, a.TipoConsulta, a.CodAtendente, a.atualiza, a.Data, a.data_altera, a.Codigo, a.CodCliente, a.ValorItem, a.Hora, a.horaConc,  a.UF, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = '".$tipo_sist."'   AND a.TipoConsulta=$tipoconsulta AND a.Motivo2 = 1 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                        }
                                        else
                                        {
                                            if ($vramo == 278)
                                            {
                                                if ($tipoconsulta != "")
                                                {
                                                    $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'  AND TipoConsulta=$tipoconsulta AND b.atividade=$vramo AND a.Motivo = 3 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                                }
                                                else
                                                {
                                                    $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'   AND b.atividade=$vramo AND a.Motivo = 3 AND a.Data = '".$Filtrando."'  ORDER BY a.Codigo DESC";
                                                }
                                            }
                                            else
                                            {
                                                if ($vramo == 1)
                                                {
                                                    if ($tipoconsulta != "")
                                                    {
                                                        $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'  AND TipoConsulta=$tipoconsulta AND b.atividade=$vramo AND a.Motivo = 3 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                                    }
                                                    else
                                                    {
                                                        $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'   AND b.atividade=$vramo AND a.Motivo = 3 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if ($vPlaca != "")
                                        {
                                            function duasPlacas($placa)
                                            {
                                                $placa = strtoupper($placa);
                                                $placa = preg_replace("/[^a-zA-Z0-9]/", "", $placa);
                                                $digito = substr($placa, 4, 1);
                                                $alphabet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
                                                $mercosul = "";
                                                $normal = "";
                                                if (preg_match('/[0-9]/', $digito))
                                                {
                                                    $digito = $alphabet[(int)$digito];
                                                    $mercosul = substr($placa, 0, 4).$digito.substr($placa, -2);
                                                    $normal = $placa;
                                                } 
                                                else if (preg_match('/[a-zA-Z]/', $digito)) 
                                                {
                                                    $digito = array_search($digito, $alphabet);
                                                    $normal = substr($placa, 0, 4).$digito.substr($placa, -2);
                                                    $mercosul = $placa;
                                                }
                                                $resultado = new stdClass();
                                                $resultado->mercosul = $mercosul;
                                                $resultado->normal = $normal;
                                                return $resultado;
                                            }
                                            $placas = duasPlacas($vPlaca);

                                            $filtro_placa = true;
                                            $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c   WHERE   b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S' AND (a.ValorItem='".$vPlaca."' OR a.ValorItem='".$placas->normal."' OR a.ValorItem='".$placas->mercosul."') AND a.Motivo = 3 ORDER BY a.Data ASC, a.Codigo ASC";
                                        } 
                                        else
                                        {
                                            if ($vramo == 278)
                                            {
                                                if ($tipoconsulta != "")
                                                {
                                                    $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'  AND a.TipoConsulta=$tipoconsulta AND b.atividade=$vramo AND a.Motivo = 3 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                                }
                                                else 
                                                {
                                                    $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'   AND b.atividade=$vramo AND a.Motivo = 3 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                                }
                                            }
                                            else
                                            {
                                                if ($vramo == 1)
                                                {
                                                    $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'   AND b.atividade=$vramo AND a.Motivo = 3 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                                }
                                                else
                                                {
                                                    if ($vramo == 4)
                                                    {
                                                            $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c WHERE b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'   AND b.atividade<>278 AND b.atividade<>'1' AND a.Motivo = 3 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                                    }
                                                    else
                                                    {
                                                        if ($vcodAtendente != "")
                                                        {
                                                            $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'   AND a.CodAtendente=$vcodAtendente AND a.Motivo = 3 AND a.Data = '".$Filtrando."' ORDER BY Codigo DESC";
                                                        } 
                                                        else
                                                        {
                                                            if ($vrestricao == "1")
                                                            {
                                                                $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'   AND a.restricao=1 AND a.Motivo = 3 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                                            } 
                                                            else
                                                            {
                                                                if ($vrestricao == 2)
                                                                {
                                                                    $vrestricao1 = "1";
                                                                    $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'   AND a.restricao1=1 AND a.Motivo = 3 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                                                }
                                                                else
                                                                {
                                                                    if ($vrestricao == 3)
                                                                    {
                                                                        $vrestricao2 ="1";
                                                                        $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'   AND a.restricao2=1 AND a.Motivo = 3 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                                                    }
                                                                    else
                                                                    {
                                                                        if ($vrestricao == 4)
                                                                        {
                                                                            $vrestricao3 ="1";
                                                                            $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'   AND a.restricao3 = 1 AND a.Motivo = 3 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                                                        }
                                                                        else
                                                                        {
                                                                            if ($vrestricao == "5")
                                                                            {
                                                                                $vrestricao4 = "1";
                                                                                $qRobo = "SELECT a.*, b.atividade, c.STATUS FROM consultas as a, tcadcli as b, clientes as c WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S' AND (a.restricao = 1 OR a.restricao1 = 1 OR a.restricao2 = 1 OR a.restricao3 = 1 OR a.restricao4 = 1) AND a.Motivo = 3 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                                                            } 
                                                                            else 
                                                                            {

                                                                                if ($vrestricao == "6")
                                                                                {
                                                                                    $vrestricao6 = "2";
                                                                                    $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'   AND a.restricao1 = 2 AND a.restricao2 <>1  AND a.restricao <>1 AND a.Motivo = 3 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                                                                }
                                                                                else
                                                                                {
                                                                                    $qRobo = "SELECT a.*, b.atividade, c.STATUS  FROM consultas as a, tcadcli as b, clientes as c  WHERE  b.ID = a.CodCliente AND c.Codigo = a.CodCliente AND a.sistemanovo = 'S'  AND a.Motivo = 3 AND a.Data = '".$Filtrando."' ORDER BY a.Codigo DESC";
                                                                                }

                                                                            }
                                                                        }

                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }	
                                } 
                            }
                            $qRobo .= " LIMIT 3000";
                } 

                    if (strtotime($Filtrando) < strtotime("-30 days") || $filtro_placa)
                    {
                        $this->db->query = str_replace('consultas', 'consultas_historico', $qRobo);
                        $qExecuta1 = $this->db->select();

                        $this->db->query = $qRobo;
                        $qExecuta2 = $this->db->select();

                       return array_merge($qExecuta1, $qExecuta2);
                    }
                    else
                    {
                        $this->db->query = $qRobo;
                        return $this->db->select();
                    }
                }
        } catch (\Throwable $th) {
            // throw new Exception("Não foi possivel buscar contratos");
            throw $th;
        }
    }
}
