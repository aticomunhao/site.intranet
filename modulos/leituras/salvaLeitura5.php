<?php
session_start(); 
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
$Acao = "";
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    $Hoje = date('d/m/Y');
}

if($Acao =="buscaDataEletric"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); 
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT TO_CHAR(dataleitura5, 'DD/MM/YYYY'), date_part('dow', dataleitura5), leitura5 
    FROM ".$xProj.".leitura_eletric WHERE id = $Cod And colec = 5 And ativo = 1");
    $row = pg_num_rows($rs);
    if($row == 0){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $tbl = pg_fetch_row($rs);
        $Dow = $tbl[1];
        switch ($Dow){
            case 0:
                $Sem = "DOM";
                break;
            case 1:
                $Sem = "SEG";
                break;
            case 2:
                $Sem = "TER";
                break;
            case 3:
                $Sem = "QUA";
                break;
            case 4:
                $Sem = "QUI";
                break;
            case 5:
                $Sem = "SEX";
                break;
            case 6:
                $Sem = "SAB";
                break;
        }
        $leitura5 = $tbl[2];
        if($leitura5 == 0){
            $leitura5 = "";
        }
        $var = array("coderro"=>$Erro, "data"=>$tbl[0], "sem"=>$Sem, "leitura5"=>$leitura5);
    }
    $responseText = json_encode($var);
    echo $responseText;
}


if($Acao =="checaDataEletric"){
    $BuscaData = addslashes(filter_input(INPUT_GET, 'data')); 
    // inverter o formato da data para y/m/d - procurar direto no BD
    $BuscaDia = implode("-", array_reverse(explode("/", $BuscaData))); // date('d/m/Y', strtotime("+ 1 days", strtotime($DataI)));
    $Erro = 0;
    $JaTem = 0;
    $rs = pg_query($Conec, "SELECT id, TO_CHAR(dataleitura5, 'DD/MM/YYYY'), date_part('dow', dataleitura5), leitura5 
    FROM ".$xProj.".leitura_eletric WHERE dataleitura5 = '$BuscaDia' And colec = 5 And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $row = pg_num_rows($rs);
    if($row > 0){
        $JaTem = 1;
        $tbl = pg_fetch_row($rs);
        $Dow = $tbl[2];
        switch ($Dow){
            case 0:
                $Sem = "DOM";
                break;
            case 1:
                $Sem = "SEG";
                break;
            case 2:
                $Sem = "TER";
                break;
            case 3:
                $Sem = "QUA";
                break;
            case 4:
                $Sem = "QUI";
                break;
            case 5:
                $Sem = "SEX";
                break;
            case 6:
                $Sem = "SAB";
                break;
        }
        $var = array("coderro"=>$Erro,"id"=>$tbl[0], "jatem"=>$JaTem, "data"=>$tbl[1], "sem"=>$Sem, "leitura5"=>$tbl[3]);
    }else{
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".leitura_eletric");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1); 
        $var = array("coderro"=>$Erro, "jatem"=>$JaTem, "codigo"=>$CodigoNovo);
    }
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaDataEletric"){
    $Cod = (float) filter_input(INPUT_GET, 'codigo'); 
    $Colec = (int) filter_input(INPUT_GET, 'colec'); // 1 comunhão - 2 Claro - 3 Oi - 5 viaturas
    $PegaData = addslashes(filter_input(INPUT_GET, 'insdata')); 
    $PegaDia = implode("-", array_reverse(explode("/", $PegaData))); // date('d/m/Y', strtotime("+ 1 days", strtotime($DataI)));
    $Leit5 = filter_input(INPUT_GET, 'leitura5'); 
    if($Leit5 == ""){
        $leitura5 = 0;
    }else{
        $leitura5 = str_replace(",", ".", $Leit5);
    }

    $rsIni = pg_query($Conec, "SELECT TO_CHAR(datainieletric5, 'YYYY') FROM ".$xProj.".paramsis WHERE idpar = 1");
    $tblIni = pg_fetch_row($rsIni);
    if($tblIni[0] == "3000"){ // não tem data inicial em paramsis - salva a primeira data - valor fica 0
        pg_query($Conec, "UPDATE ".$xProj.".paramsis SET datainieletric5 = '$PegaDia' WHERE idpar = 1 ");
    }

    $ValorKwh = parAdm("valorkwh", $Conec, $xProj);
    $FatorCor = parAdm("fatorcor_eletr", $Conec, $xProj);
    $Erro = 0;

    if($Cod == 0){
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".leitura_eletric");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1); 
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".leitura_eletric (id, colec, dataleitura5, leitura5, fator, valorkwh, ativo, usuins, datains) 
        VALUES($CodigoNovo, $Colec, '$PegaDia', $leitura5, $FatorCor, $ValorKwh, 1, ".$_SESSION["usuarioID"].", NOW() )");
        if(!$rs){
            $Erro = 1;
        }
        if($CodigoNovo == 1){ // primeiro lançamento - verif dataini em paramsis
            $rs1 = pg_query($Conec, "SELECT datainieletric5 FROM ".$xProj.".paramsis WHERE idpar = 1");
            $tbl1 = pg_fetch_row($rs1);
            $DataIni = $tbl1[0];
            if(strtotime($DataIni) != strtotime($PegaData)){
                $Erro = 2;
            }
        }
    }else{
        $rs = pg_query($Conec, "UPDATE ".$xProj.".leitura_eletric SET colec = $Colec, dataleitura5 = '$PegaDia', leitura5 = $leitura5, fator =  $FatorCor, valorkwh = $ValorKwh, usumodif = ".$_SESSION["usuarioID"].", datamodif = NOW() WHERE id = $Cod ");
        if(!$rs){
            $Erro = 1;
        }
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="ultDataEletric"){
    $Erro = 0;
    $ValorIni = 0;
    $rs = pg_query($Conec, "SELECT MAX(dataleitura5) FROM ".$xProj.".leitura_eletric WHERE colec = 5 And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        if(is_null($tbl[0])){
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".leitura_eletric WHERE colec = 5");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1); 
            if($CodigoNovo == 1){ // primeiro lançamento
                $rs1 = pg_query($Conec, "SELECT datainieletric5, valorinieletric5 FROM ".$xProj.".paramsis WHERE idpar = 1");
                $tbl1 = pg_fetch_row($rs1);
                $ProxDay = date('d/m/Y', strtotime($tbl1[0]));
                $Erro = 2;
                $ValorIni = $tbl1[1];
                if($ValorIni == 0){
                    $UltDay = date('d/m/Y', strtotime($Hoje. ' - 1 day'));
                    $ProxDay = $Hoje;
                }
            }else{
                $ProxDay = $Hoje;
            }
        }else{
            $UltDay = date('Y/m/d', strtotime($tbl[0]));
            $ProxDay = date('d/m/Y', strtotime($tbl[0]. ' + 1 day'));
        }
        $Sem = date("D", strtotime("$ProxDay"));
        $semana = array(
            'Sun' => 'DOM', 
            'Mon' => 'SEG',
            'Tue' => 'TER',
            'Wed' => 'QUA',
            'Thu' => 'QUI',
            'Fri' => 'SEX',
            'Sat' => 'SAB'
        );
        //última leitura para conferir
        $rs2 = pg_query($Conec, "SELECT leitura5 FROM ".$xProj.".leitura_eletric WHERE dataleitura5 = '$UltDay' And colec = 5 And ativo = 1");
        $row2 = pg_num_rows($rs2);
        if($row2 > 0){
            $tbl2 = pg_fetch_row($rs2);
            $UltLeit = $tbl2[0];
        }else{
            $UltLeit = 0;
        }
        $var = array("coderro"=>$Erro, "data"=>$tbl[0], "sem"=>$semana["$Sem"], "proximo"=>$ProxDay, "valorini"=>$ValorIni, "ultleitura"=>$UltLeit);
    }else{
        $var = array("coderro"=>$Erro);
    }
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "apagareg"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $rs1 = pg_query($Conec, "UPDATE ".$xProj.".leitura_eletric SET ativo = 0 WHERE id = $Cod");
    if(!$rs1){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

