<?php
session_start(); // inicia uma sessão
if(!isset($_SESSION["usuarioID"])){
    header("Location: /cesb/index.php");
}
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
date_default_timezone_set('America/Sao_Paulo'); 
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
}
if($Acao =="busca"){
    $Sent = (int) filter_input(INPUT_GET, 'sentido');
    $time = (int) filter_input(INPUT_GET, 'monthTime');
    $Erro = 0;
    if($Sent == 1){
        $mesAno = prevMonth($time);
        $numMes = (int) prevNumMes($time);
        $data = strtotime('-1 month', $time);
    }
    if($Sent == 2){
        $mesAno = nextMonth($time);
        $numMes = (int) nextNumMes($time);
        $data = strtotime('+1 month', $time);
    }
    $Ingl = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    $Port = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
    $Trad = str_replace($Ingl, $Port, $mesAno);
    $var = array("result"=>$mesAno, "numMes"=>$numMes, "monthTime"=>$data, "mesTrad"=> $Trad);
    $responseText = json_encode($var);
    echo $responseText;
}
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
if($Acao =="salvaev"){
    $numEv = (int) filter_input(INPUT_GET, 'numEv'); // numEv = 0 novo lançamento
    if($numEv > 0){ // pega a data da inserção e cancela o lanç anterior pq pode haver mudança de duração
        $DataIns = "3000-12-31";
        $rsIns = pg_query($Conec, "SELECT to_char(datains, 'YYYY/MM/DD HH24:MI') FROM ".$xProj.".calendev WHERE ativo = 1 And evnum = $numEv");
        $tblIns = pg_fetch_row($rsIns);
        $DataIns = $tblIns[0]; // guarda a data de inserção para o novo lançamento
        $rsApag = pg_query($Conec, "UPDATE ".$xProj.".calendev SET ativo = 0 WHERE evnum = $numEv"); // desativa o lançamento anterior
    }

    $DataI = addslashes($_REQUEST['dataini']);  //  filter_input(INPUT_GET, 'dataini');
    if(!isset($_REQUEST['datafim'])){
        $DataF = $DataI;
    }else{
        $DataF = addslashes($_REQUEST['datafim']);  // filter_input(INPUT_GET, 'datafim');
    }
    $Texto = filter_input(INPUT_GET, 'textoev');
    $Local = filter_input(INPUT_GET, 'localev');
    $Cor = filter_input(INPUT_GET, 'corevento');
    $Repet = (int) filter_input(INPUT_GET, 'repet');
    $Fixo = (int) filter_input(INPUT_GET, 'fixo');
    if($Fixo == 1){
        $Cor = "#F5DEB3";
    }
    $Obrig = (int) filter_input(INPUT_GET, 'avobrig'); // insere nas colunas: avobrig e avok
    
    $Erro = 0;

    // inverter o formato da data para y/m/d - lançar direto no BD
    $diaIni = implode("-", array_reverse(explode("/", $DataI))); // date('d/m/Y', strtotime("+ 1 days", strtotime($DataI)));
    $diaFim = implode("-", array_reverse(explode("/", $DataF))); 

    // transforma a data em número para poder somar
    $diaIniUnix = strtotime($diaIni);
    $diaFimUnix = strtotime($diaFim);
    $diasDif = (($diaFimUnix - $diaIniUnix) / 86400); // verifica quanto dias dá

    //Calcula o número do próximo evento
    $rs0 = pg_query($Conec, "SELECT MAX(evnum) As UltEv FROM ".$xProj.".calendev");
    if($rs0){
        $tbl0 = pg_fetch_row($rs0);
        $UltEv = $tbl0[0];
        $ProxEv = ($UltEv + 1);
    }

    if($diasDif == 0){ // só um dia
        $Dia = date('Y/m/d', $diaIniUnix);
        $rsCod = pg_query($Conec, "SELECT MAX(idev) FROM ".$xProj.".calendev");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        if($numEv > 0){ // modificação
            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".calendev (idev, evnum, titulo, cor, dataini, localev, repet, fixo, avobrig, avok, datains, datamodif) VALUES ($CodigoNovo, $ProxEv, '$Texto', '$Cor', '$Dia', '$Local', $Repet, $Fixo, $Obrig, $Obrig, '$DataIns', NOW() )");
        }else{ // novo evento
            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".calendev (idev, evnum, titulo, cor, dataini, localev, repet, fixo, avobrig, avok, datains) VALUES ($CodigoNovo, $ProxEv, '$Texto', '$Cor', '$Dia', '$Local', $Repet, $Fixo, $Obrig, $Obrig, NOW() )");
        }
    }else{
        $Dia = date('Y/m/d', $diaIniUnix);
        for($n = 0; $n <= $diasDif; $n++){
            if($numEv > 0){ // modificação
                $rsCod = pg_query($Conec, "SELECT MAX(idev) FROM ".$xProj.".calendev");
                $tblCod = pg_fetch_row($rsCod);
                $Codigo = $tblCod[0];
                $rs = pg_query($Conec, "INSERT INTO ".$xProj.".calendev (idev, evnum, titulo, cor, dataini, localev, repet, fixo, avobrig, avok, datains, datamodif) VALUES (($Codigo+1), $ProxEv, '$Texto', '$Cor', '$Dia', '$Local', $Repet, $Fixo, $Obrig, $Obrig, '$DataIns', NOW() )");
                $Soma = strtotime($Dia . ' + 1 day'); // soma um dia no formato unix
                $Dia = date('Y/m/d', $Soma); // transforma para poder lançar no BD.
            }else{
                $rsCod = pg_query($Conec, "SELECT MAX(idev) FROM ".$xProj.".calendev");
                $tblCod = pg_fetch_row($rsCod);
                $Codigo = $tblCod[0];
                $rs = pg_query($Conec, "INSERT INTO ".$xProj.".calendev (idev, evnum, titulo, cor, dataini, localev, repet, fixo, avobrig, avok, datains) VALUES (($Codigo+1), $ProxEv, '$Texto', '$Cor', '$Dia', '$Local', $Repet, $Fixo, $Obrig, $Obrig, NOW() )");
                $Soma = strtotime($Dia . ' + 1 day'); // soma um dia no formato unix
                $Dia = date('Y/m/d', $Soma); // transforma para poder lançar no BD.
            }
        }
    }
    $var = array("coderro"=>$Erro, "diaIni"=>$diaIni, "obrig"=>$Obrig);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="buscaevento"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $evNum = (int) filter_input(INPUT_GET, 'evNum');
    $Erro = 0;

    $rs0 = pg_query($Conec, "SELECT evnum, titulo, cor, localev, repet, fixo, avobrig FROM ".$xProj.".calendev WHERE idev = $Cod");
    if($rs0){
        $tbl0 = pg_fetch_row($rs0);
        $evNum = $tbl0[0];
        $Tit = $tbl0[1];
        $Cor = $tbl0[2];
        $Local = $tbl0[3];
        $Repet = $tbl0[4];
        $Fixo = $tbl0[5];
        $AvObrig = $tbl0[6]; // aviso obrigatório
        //pegar a data de início do evento
        //ver se há mais de um dia
        $rs1 = pg_query($Conec, "SELECT to_char(MIN(dataini), 'DD/MM/YYYY') as DataIni FROM ".$xProj.".calendev WHERE evnum = $evNum And ativo = 1");
        if($rs1){
            $tbl1 = pg_fetch_row($rs1);
            $DataIni = $tbl1[0];
            $DataFim = $DataIni;
        }
        //ver se há mais de um dia
//        $rs2 = pg_query($Conec, "SELECT date_format(MAX(dataIni), '%d/%m/%Y') as DataFim FROM ".$xProj.".calendev WHERE evNum = $evNum And Ativo = 1");
        $rs2 = pg_query($Conec, "SELECT to_char(MAX(dataini), 'DD/MM/YYYY') as DataFim FROM ".$xProj.".calendev WHERE evnum = $evNum And ativo = 1");
        if($rs2){
            $tbl2 = pg_fetch_row($rs2);
            $DataFim = $tbl2[0];
        }
    }else{
        $Erro = 1;
    }

    $admIns = parAdm("insEvento", $Conec, $xProj);   // nível para inserir
    $admEdit = parAdm("editEvento", $Conec, $xProj); // nível para editar

    $InsEv = 0;
    $EditEv = 0;
    if($_SESSION["AdmUsu"] >= $admIns){
        $InsEv = 1;
    }
    if($_SESSION["AdmUsu"] >= $admEdit){
        $EditEv = 1;
    }

    $var = array("coderro"=>$Erro, "evNum"=>$evNum, "dataIni"=>$DataIni, "titulo"=>$Tit, "cor"=>$Cor, "localEv"=>$Local, "dataFim"=>$DataFim, "Repet"=>$Repet, "Fixo"=>$Fixo, "AvObrig"=>$AvObrig, "insEv"=>$InsEv, "editEv"=>$EditEv);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="apagaev"){
    $numEv = (int) filter_input(INPUT_GET, 'numEv'); // numEv = 0 novo lançamento
    $Erro = 0;
    if($numEv > 0){ // cancela o lanç anterior pq pode haver mudança de duração
        $rsApag = pg_query($Conec, "UPDATE ".$xProj.".calendev SET Ativo = 0, UsuApag = ".$_SESSION["usuarioID"].", DataApag = NOW()  WHERE evNum = $numEv");
        if(!$rsApag){
            $Erro = 1;
        }
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="msgAviso"){
    $Erro = 0;
    $Msg = "";
    $CodMsg = 0;
    $Aviso = 0;
    $AvisoObrig = 0;
    $Hoje = date('Y/m/d');
    $rs0 = pg_query($Conec, "SELECT evnum, titulo, cor, avobrig, avok, repet, fixo FROM ".$xProj.".calendev WHERE dataini = '$Hoje' And ativo = 1 ");
    $row0 = pg_num_rows($rs0);
    if($row0 > 0){
        $tbl0 = pg_fetch_row($rs0);
        $CodMsg = $tbl0[0];
        $Msg = $tbl0[1];
        $Cor = $tbl0[2];
        $AvisoObrig = $tbl0[3]; // sinal de aviso obrigatório
        $AvisoObrigCfm = $tbl0[4]; // se for zero foi clicado em não apresentar mais
    }else{
        $Erro = 1;
    }
    $rs1 = pg_query($Conec, "SELECT avcalend, avhoje FROM ".$xProj.".poslog WHERE pessoas_id = ".$_SESSION["usuarioID"]." ");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($rs1);
        $Aviso = $tbl1[0]; // emitir aviso = 1
        $Data = $tbl1[1];
        if(strtotime($Data) == strtotime($Hoje)){ // quando não quer mais ver avisos só por hoje
            $Aviso = 0;
        }
    }
    if($row0 > 0 && $AvisoObrig == 1 && $AvisoObrigCfm == 1){
        $Aviso = 2;
    }
    $var = array("coderro"=>$Erro, "Quant"=>$row0, "msg"=>$Msg, "avisocalend"=>$Aviso, "codMsg"=>$CodMsg);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="semAvisoHoje"){ // pára os avisos da agenda só por hoje - é reativado a cada logon
    $CodMsg = (int) filter_input(INPUT_GET, 'codigomsg');
    $Erro = 0;
    $rs0 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET avhoje = NOW() WHERE pessoas_id = ".$_SESSION["usuarioID"]." ");
    $row0 = pg_num_rows($rs0);
    if($row0 == 0){
        $Erro = 1;
    }
    if($CodMsg > 0){ // era uma mensagem obrigatória e foi mandado parar
        pg_query($Conec, "UPDATE ".$xProj.".calendev SET avok = 0 WHERE evnum = $CodMsg ");
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="semAviso"){ // pára os avisos da agenda 
    $Valor = (int) filter_input(INPUT_GET, 'param');
    $Erro = 0;
    $rs0 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET avcalend = $Valor WHERE pessoas_id = ".$_SESSION["usuarioID"]." ");
    if(!$rs0){
        $Erro = 1;
    }
    if($Valor == 1){ // reativa os avisos que foram bloqueados só por hoje
        pg_query($Conec, "UPDATE ".$xProj.".poslog SET avhoje = (CURRENT_DATE -1) WHERE pessoas_id = ".$_SESSION["usuarioID"]." ");
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

function prevMonth($time){
//    return date('Y-m-d', strtotime('-1 month', $time));
    return date('F Y', strtotime('-1 month', $time));
}

function nextMonth($time){
//    return date('Y-m-d', strtotime('+1 month', $time));
    return date('F Y', strtotime('+1 month', $time));
}

function prevNumMes($time){
    return date('m', strtotime('-1 month', $time));
}
function nextNumMes($time){
    return date('m', strtotime('+1 month', $time));
}

