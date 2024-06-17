<?php
session_start(); 
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
$Acao = "";
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    $Hoje = date('d/m/Y');
}

if($Acao =="buscaData"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); 
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT TO_CHAR(dataleitura, 'DD/MM/YYYY'), date_part('dow', dataleitura), leitura1, leitura2, leitura3 
    FROM ".$xProj.".leitura_agua WHERE id = $Cod And ativo = 1");
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
        $Leitura1 = $tbl[2];
        if($Leitura1 == 0){
            $Leitura1 = "";
        }
        $Leitura2 = $tbl[3];
        if($Leitura2 == 0){
            $Leitura2 = "";
        }
        $Leitura3 = $tbl[4];
        if($Leitura3 == 0){
            $Leitura3 = "";
        }
        $var = array("coderro"=>$Erro, "data"=>$tbl[0], "sem"=>$Sem, "leitura1"=>$Leitura1, "leitura2"=>$Leitura2, "leitura3"=>$Leitura3);
    }
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="buscaDataEletric"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); 
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT TO_CHAR(dataleitura1, 'DD/MM/YYYY'), date_part('dow', dataleitura1), leitura1 
    FROM ".$xProj.".leitura_eletric WHERE id = $Cod And ativo = 1");
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
        $Leitura1 = $tbl[2];
        if($Leitura1 == 0){
            $Leitura1 = "";
        }
        $var = array("coderro"=>$Erro, "data"=>$tbl[0], "sem"=>$Sem, "leitura1"=>$Leitura1);
    }
    $responseText = json_encode($var);
    echo $responseText;
}


if($Acao =="checaData"){
    $BuscaData = addslashes(filter_input(INPUT_GET, 'data')); 
    // inverter o formato da data para y/m/d - procurar direto no BD
    $BuscaDia = implode("-", array_reverse(explode("/", $BuscaData))); // date('d/m/Y', strtotime("+ 1 days", strtotime($DataI)));
    $Erro = 0;
    $JaTem = 0;
    $rs = pg_query($Conec, "SELECT id, TO_CHAR(dataleitura, 'DD/MM/YYYY'), date_part('dow', dataleitura), leitura1, leitura2, leitura3 
    FROM ".$xProj.".leitura_agua WHERE dataleitura = '$BuscaDia' And ativo = 1");
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
        $var = array("coderro"=>$Erro,"id"=>$tbl[0], "jatem"=>$JaTem, "data"=>$tbl[1], "sem"=>$Sem, "leitura1"=>$tbl[3], "leitura2"=>$tbl[4], "leitura3"=>$tbl[5]);

    }else{
        $var = array("coderro"=>$Erro, "jatem"=>$JaTem);
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
    $rs = pg_query($Conec, "SELECT id, TO_CHAR(dataleitura1, 'DD/MM/YYYY'), date_part('dow', dataleitura1), leitura1 
    FROM ".$xProj.".leitura_eletric WHERE dataleitura1 = '$BuscaDia' And ativo = 1");
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
        $var = array("coderro"=>$Erro,"id"=>$tbl[0], "jatem"=>$JaTem, "data"=>$tbl[1], "sem"=>$Sem, "leitura1"=>$tbl[3]);
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

if($Acao =="salvaData"){
    $Cod = (float) filter_input(INPUT_GET, 'codigo'); 
    $PegaData = addslashes(filter_input(INPUT_GET, 'insdata')); 
    $PegaDia = implode("-", array_reverse(explode("/", $PegaData))); // date('d/m/Y', strtotime("+ 1 days", strtotime($DataI)));

    $Leit1 = filter_input(INPUT_GET, 'leitura1'); 
    if($Leit1 == ""){
        $Leitura1 = 0;
    }else{
        $Leitura1 = str_replace(",", ".", $Leit1);
    }
    
    $Leit2 = filter_input(INPUT_GET, 'leitura2'); 
    if($Leit2 == ""){
        $Leitura2 = 0;
    }else{
        $Leitura2 = str_replace(",", ".", $Leit2);
    }

    $Leit3 = filter_input(INPUT_GET, 'leitura3'); 
    if($Leit3 == ""){
        $Leitura3 = 0;
    }else{
        $Leitura3 = str_replace(",", ".", $Leit3);
    }
    $Erro = 0;

    if($Cod == 0){
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".leitura_agua");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1); 
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".leitura_agua (id, dataleitura, leitura1, leitura2, leitura3, ativo, usuins, datains) 
        VALUES($CodigoNovo, '$PegaDia', $Leitura1, $Leitura2, $Leitura3, 1, ".$_SESSION["usuarioID"].", NOW() )");
        if(!$rs){
            $Erro = 1;
        }
    }else{
        $rs = pg_query($Conec, "UPDATE ".$xProj.".leitura_agua SET dataleitura = '$PegaDia', leitura1 = $Leitura1, leitura2 = $Leitura2, leitura3 = $Leitura3, usumodif = ".$_SESSION["usuarioID"].", datamodif = NOW() WHERE id = $Cod ");
        if(!$rs){
            $Erro = 1;
        }
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaDataEletric"){
    $Cod = (float) filter_input(INPUT_GET, 'codigo'); 
    $Colec = (int) filter_input(INPUT_GET, 'colec'); // 1 comunhão - 2 Claro - 3 Oi
    $PegaData = addslashes(filter_input(INPUT_GET, 'insdata')); 
    $PegaDia = implode("-", array_reverse(explode("/", $PegaData))); // date('d/m/Y', strtotime("+ 1 days", strtotime($DataI)));

    $Leit1 = filter_input(INPUT_GET, 'leitura1'); 
    if($Leit1 == ""){
        $Leitura1 = 0;
    }else{
        $Leitura1 = str_replace(",", ".", $Leit1);
    }
    $Erro = 0;

    if($Cod == 0){
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".leitura_eletric");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1); 
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".leitura_eletric (id, colec, dataleitura1, leitura1, ativo, usuins, datains) 
        VALUES($CodigoNovo, $Colec, '$PegaDia', $Leitura1, 1, ".$_SESSION["usuarioID"].", NOW() )");
        if(!$rs){
            $Erro = 1;
        }
        if($CodigoNovo == 1){ // primeiro lançamento - verif dataini em paramsis
            $rs1 = pg_query($Conec, "SELECT datainieletric FROM ".$xProj.".paramsis WHERE idpar = 1");
            $tbl1 = pg_fetch_row($rs1);
            $DataIni = $tbl1[0];
            if(strtotime($DataIni) != strtotime($PegaData)){
                $Erro = 2;
            }
        }
    }else{
        $rs = pg_query($Conec, "UPDATE ".$xProj.".leitura_eletric SET colec = 1, dataleitura1 = '$PegaDia', leitura1 = $Leitura1, usumodif = ".$_SESSION["usuarioID"].", datamodif = NOW() WHERE id = $Cod ");
        if(!$rs){
            $Erro = 1;
        }
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}


if($Acao =="ultData"){
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT MAX(dataleitura) 
    FROM ".$xProj.".leitura_agua WHERE ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        if(is_null($tbl[0])){
            $ProxDay = $Hoje;
        }else{
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
        $var = array("coderro"=>$Erro, "data"=>$tbl[0], "sem"=>$semana["$Sem"], "proximo"=>$ProxDay);
    }else{
        $var = array("coderro"=>$Erro);
    }
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="ultDataEletric"){
    $Erro = 0;
    $ValorIni = 0;
    $rs = pg_query($Conec, "SELECT MAX(dataleitura1) FROM ".$xProj.".leitura_eletric WHERE ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        if(is_null($tbl[0])){
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".leitura_eletric WHERE colec = 1");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1); 
            if($CodigoNovo == 1){ // primeiro lançamento
                $rs1 = pg_query($Conec, "SELECT datainieletric, valorinieletric FROM ".$xProj.".paramsis WHERE idpar = 1");
                $tbl1 = pg_fetch_row($rs1);
                $ProxDay = date('d/m/Y', strtotime($tbl1[0]));
                $Erro = 2;
                $ValorIni = $tbl1[1];
            }else{
                $ProxDay = $Hoje;
            }
        }else{
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
        $var = array("coderro"=>$Erro, "data"=>$tbl[0], "sem"=>$semana["$Sem"], "proximo"=>$ProxDay, "valorini"=>$ValorIni);
    }else{
        $var = array("coderro"=>$Erro);
    }
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="imprmes"){
    $Busca = addslashes(filter_input(INPUT_GET, 'dataimpr')); 
    $Erro = 0;

    $Proc = explode("/", $Busca);
    $Mes = $Proc[0];
    $Ano = $Proc[1];

    $rs = pg_query($Conec, "SELECT dataleitura FROM ".$xProj.".leitura_agua WHERE DATE_PART('MONTH', dataleitura) = '$Mes' ");
    $row = pg_num_rows($rs);
    $var = array("coderro"=>$Erro, "mes"=>$Mes, "ano"=>$Ano, "quantid"=>$row);

    $responseText = json_encode($var);
    echo $responseText;
}
