<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}

require_once(dirname(dirname(__FILE__))."/config/abrealas.php");

date_default_timezone_set('America/Sao_Paulo'); 

function prevMonth($time){
    //return date('Y-m-d', strtotime('-1 month', $time));
    return date('F Y', strtotime('-1 month', $time));
}
    
function nextMonth($time){
    //return date('Y-m-d', strtotime('+1 month', $time));
    return date('F Y', strtotime('+1 month', $time));
}
function prevNumMes($time){
    return date('m', strtotime('-1 month', $time));
}
function nextNumMes($time){
    return date('m', strtotime('+1 month', $time));
}



if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    if($Acao =="buscadata"){
        $time = filter_input(INPUT_GET, 'dataDia');
        $date = new DateTime("@$time");
        $Dia = $date->format('d-m-Y');   //  $var = $date->format('U = Y-m-d H:i:s'); // U é o timestamp unix

        $admIns = parAdm("insevento", $Conec, $xProj);   // nível para inserir 
        $admEdit = parAdm("editevento", $Conec, $xProj); // nível para editar

        $InsEv = 0;
        $EditEv = 0;
        if($_SESSION["AdmUsu"] >= $admIns){
            $InsEv = 1;
        }
        if($_SESSION["AdmUsu"] >= $admEdit){
            $EditEv = 1;
        }
        $var = array("diaClick"=>$Dia, "insEv"=>$InsEv, "editEv"=>$EditEv);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="carregaOpr"){
        $Busca = addslashes(filter_input(INPUT_GET, 'mesano'));
        $Proc = explode("/", $Busca);
        $Mes = $Proc[0];
        if($Mes < 10){
            $Mes = "0".$Mes;
        }
        $Ano = $Proc[1];

        $Erro = 0;
        $rs = pg_query($Conec, "SELECT poslog_id, trigr FROM ".$xProj.".escala_eft WHERE opr = 1 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId1 = $tbl[0];
           $Trig1 = $tbl[1];
        }else{
            $Erro = 1;
            $CodId1 = 0;
            $Trig1 = "XXX";
        }
        
        $rs = pg_query($Conec, "SELECT poslog_id, trigr FROM ".$xProj.".escala_eft WHERE opr = 2 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId2 = $tbl[0];
           $Trig2 = $tbl[1];
        }else{
            $Erro = 1;
            $CodId2 = 0;
            $Trig2 = "XXX";
        }
        $rs = pg_query($Conec, "SELECT poslog_id, trigr FROM ".$xProj.".escala_eft WHERE opr = 3 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId3 = $tbl[0];
           $Trig3 = $tbl[1];
        }else{
            $Erro = 1;
            $CodId3 = 0;
            $Trig3 = "XXX";
        }
        $rs = pg_query($Conec, "SELECT poslog_id, trigr FROM ".$xProj.".escala_eft WHERE opr = 4 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId4 = $tbl[0];
           $Trig4 = $tbl[1];
        }else{
            $Erro = 1;
            $CodId4 = 0;
            $Trig4 = "XXX";
        }
        $rs = pg_query($Conec, "SELECT poslog_id, trigr FROM ".$xProj.".escala_eft WHERE opr = 5 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId5 = $tbl[0];
           $Trig5 = $tbl[1];
        }else{
            $Erro = 1;
            $CodId5 = 0;
            $Trig5 = "XXX";
        }

        $rs = pg_query($Conec, "SELECT poslog_id, trigr FROM ".$xProj.".escala_eft WHERE opr = 6 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId6 = $tbl[0];
           $Trig6 = $tbl[1];
        }else{
            $Erro = 1;
            $CodId6 = 0;
            $Trig6 = "XXX";
        }
        $rs = pg_query($Conec, "SELECT poslog_id, trigr FROM ".$xProj.".escala_eft WHERE opr = 7 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId7 = $tbl[0];
           $Trig7 = $tbl[1];
        }else{
            $Erro = 1;
            $CodId7 = 0;
            $Trig7 = "XXX";
        }
        $rs = pg_query($Conec, "SELECT poslog_id, trigr FROM ".$xProj.".escala_eft WHERE opr = 8 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId8 = $tbl[0];
           $Trig8 = $tbl[1];
        }else{
            $Erro = 1;
            $CodId8 = 0;
            $Trig8 = "XXX";
        }
        $rs = pg_query($Conec, "SELECT poslog_id, trigr FROM ".$xProj.".escala_eft WHERE opr = 9 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId9 = $tbl[0];
           $Trig9 = $tbl[1];
        }else{
            $Erro = 1;
            $CodId9 = 0;
            $Trig9 = "XXX";
        }
        $rs = pg_query($Conec, "SELECT poslog_id, trigr FROM ".$xProj.".escala_eft WHERE opr = 10 And mes = '$Mes' And ano = '$Ano' ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId10 = $tbl[0];
           $Trig10 = $tbl[1];
        }else{
            $Erro = 1;
            $CodId10 = 0;
            $Trig10 = "XXX";
        }

        $var = array("coderro"=>$Erro, "codOpr1"=>$CodId1, "trigr1"=>$Trig1, "codOpr2"=>$CodId2, "trigr2"=>$Trig2, "codOpr3"=>$CodId3, "trigr3"=>$Trig3, "codOpr4"=>$CodId4, "trigr4"=>$Trig4, "codOpr5"=>$CodId5, "trigr5"=>$Trig5, "codOpr6"=>$CodId6, "trigr6"=>$Trig6, "codOpr7"=>$CodId7, "trigr7"=>$Trig7, "codOpr8"=>$CodId8, "trigr8"=>$Trig8, "codOpr9"=>$CodId9, "trigr9"=>$Trig9, "codOpr10"=>$CodId10, "trigr10"=>$Trig10);

        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="buscaOpr"){
        $Cod = (int) filter_input(INPUT_GET, 'codigo');
        $Opr = (int) filter_input(INPUT_GET, 'opr');
        $Erro = 0;
        $rs = pg_query($Conec, "SELECT poslog_id, trigr FROM ".$xProj.".escala_eft WHERE poslog_id = $Cod ");
        $row = pg_num_rows($rs);
        if($row > 0){
           $tbl = pg_fetch_row($rs);
           $CodId = $tbl[0];
           $Trig = $tbl[1];
        }else{
            $Erro = 1;
            $CodId = 0;
            $Trig = "XXX";
        }
        switch ($Opr){
            case 1:
                $Cor = "red";
                break;
            case 2:
                $Cor = "blue";
                break;
            case 3:
                $Cor = "yellow";
                break;
            case 4:
                $Cor = "green";
                break;
            case 5:
                $Cor = "#FF00FF";
                break;
            case 6:
                $Cor = "#FF9933";
                break;
            case 7:
                $Cor = "#CC9966";
                break;
            case 8:
                $Cor = "#99CC66";
                break;
            case 9:
                $Cor = "#6633CC";
                break;
            case 10:
                $Cor = "#009933";
                break;
        }
        pg_query($Conec, "UPDATE ".$xProj.".escala_eft SET oprcor = '$Cor' WHERE poslog_id = $Cod");

        $var = array("coderro"=>$Erro, "codigo"=>$CodId, "trigrama"=>$Trig);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="marcaescala"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo');
        $Dia = filter_input(INPUT_GET, 'dia');
        $Mes = filter_input(INPUT_GET, 'mes');
        $Ano = filter_input(INPUT_GET, 'ano');
        $Coluna = filter_input(INPUT_GET, 'coluna');

        $Data = $Ano."-".$Mes."-".$Dia;
        $rs0 = pg_query($Conec, "SELECT $Coluna FROM ".$xProj.".escala_adm WHERE dataescala = '$Data'");
        $tbl0 = pg_fetch_row($rs0);
        $CodLocal = (int) $tbl0[0];

        if($Cod == $CodLocal){ // já estava marcado
            $rs = pg_query($Conec, "UPDATE ".$xProj.".escala_adm SET $Coluna = 0 WHERE dataescala = '$Data'");
        }else{
            $rs = pg_query($Conec, "UPDATE ".$xProj.".escala_adm SET $Coluna = $Cod WHERE dataescala = '$Data'");
        }
        if(!$rs){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro, "codigo"=>$Data);
        $responseText = json_encode($var);
        echo $responseText;
    }
}


