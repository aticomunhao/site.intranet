<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
}

if($Acao == "selectarquivo"){  //vem de relArq.php
    $CodArq = filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT descarq FROM ".$xProj.".trafego WHERE codtraf = $CodArq");
    if(!$rs){
        $Erro = 1;
     }
    $Tbl = pg_fetch_row($rs);
    $DescArq = $Tbl[0];
     //verifica se o arquivo existe no diretório
    if(!file_exists("arquivos/".$DescArq)){
        $Erro = 2;
    }
     $var = array("coderro"=>$Erro, "arquivo"=>$DescArq);
     $responseText = json_encode($var);
     echo $responseText;
}
if($Acao == "apagaarquivo"){  //vem de relArq.php
    $CodArq = filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT descarq FROM ".$xProj.".trafego WHERE codtraf = $CodArq");
    if(!$rs){
        $Erro = 1;
     }
    $Tbl = pg_fetch_row($rs);
    $DescArq = $Tbl[0];
     //deleta o arquivo
    if(file_exists("arquivos/".$DescArq)){
        unlink("arquivos/".$DescArq);
     }else{
        $Erro = 2;
     }
    //modifica a condição na tabela
    $rs = pg_query($Conec, "UPDATE ".$xProj.".trafego SET ativo = 0, usuapag = ".$_SESSION["usuarioID"].", dataapag = NOW() WHERE codtraf = $CodArq");
    //Apaga registros com mais de 5 anos de inativado
    pg_query($Conec, "DELETE FROM ".$xProj.".trafego WHERE ativo = 0 And ((CURRENT_DATE - ".$xProj.".trafego.dataapag) / 365) > 5"); 
     $var = array("coderro"=>$Erro, "arquivo"=>$DescArq);
     $responseText = json_encode($var);
     echo $responseText;
}