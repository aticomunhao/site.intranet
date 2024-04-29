<?php
session_start(); 
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
$Acao = "";
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
}

if($Acao =="buscaData"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); 
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT TO_CHAR(dataleitura, 'DD/MM/YYYY'), date_part('dow', dataleitura), leitura1, leitura2, leitura3 
    FROM ".$xProj.".leituras WHERE id = $Cod And controle = 1 And ativo = 1");
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


if($Acao =="checaData"){
    $BuscaData = addslashes(filter_input(INPUT_GET, 'data')); 
    // inverter o formato da data para y/m/d - procurar direto no BD
    $BuscaDia = implode("-", array_reverse(explode("/", $BuscaData))); // date('d/m/Y', strtotime("+ 1 days", strtotime($DataI)));
    $Erro = 0;
    $JaTem = 0;
    $rs = pg_query($Conec, "SELECT id, TO_CHAR(dataleitura, 'DD/MM/YYYY'), date_part('dow', dataleitura), leitura1, leitura2, leitura3 
    FROM ".$xProj.".leituras WHERE dataleitura = '$BuscaDia' And controle = 1 And ativo = 1");
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
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".leituras");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1); 
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".leituras (id, dataleitura, leitura1, leitura2, leitura3, controle, ativo, usuins, datains) 
        VALUES($CodigoNovo, '$PegaDia', $Leitura1, $Leitura2, $Leitura3, 1, 1, ".$_SESSION["usuarioID"].", NOW() )");
        if(!$rs){
            $Erro = 1;
        }
    }else{
        $rs = pg_query($Conec, "UPDATE ".$xProj.".leituras SET dataleitura = '$PegaDia', leitura1 = $Leitura1, leitura2 = $Leitura2, leitura3 = $Leitura3, usumodif = ".$_SESSION["usuarioID"].", datamodif = NOW() 
        WHERE id = $Cod ");
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
    FROM ".$xProj.".leituras WHERE controle = 1 And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        $ProxDay = date('d-m-Y', strtotime($tbl[0]. ' + 1 day'));
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
