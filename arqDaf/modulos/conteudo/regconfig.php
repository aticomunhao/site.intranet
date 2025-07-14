<?php
session_name("arqAdm"); // sessão diferente da CEsB
session_start();
require_once("../config/abrealas.php");
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
}

if($Acao == "selectarquivo"){  //vem de relArq.php
    $CodArq = filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT descarq FROM ".$xProj.".daf_arqsetor WHERE codarq = $CodArq");
    if(!$rs){
        $Erro = 1;
     }
    $Tbl = pg_fetch_row($rs);
    $DescArq = $Tbl[0];
     //verifica se o arquivo existe no diretório
//    if(!file_exists("arquivos/".$DescArq)){
    if(!file_exists(dirname(dirname(dirname(__FILE__)))."/arquivos/".$DescArq)){
        $Erro = 2;
    }
     $var = array("coderro"=>$Erro, "arquivo"=>$DescArq);
     $responseText = json_encode($var);
     echo $responseText;
}
if($Acao == "apagaarquivo"){  //vem de relArq.php
    $CodArq = filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT descArq FROM ".$xProj.".daf_arqsetor WHERE CodArq = $CodArq");
    if(!$rs){
        $Erro = 1;
     }
    $Tbl = pg_fetch_row($rs);
    $DescArq = $Tbl[0];
     //deleta o arquivo
    if(file_exists(dirname(dirname(dirname(__FILE__)))."/arquivos/".$DescArq)){
        unlink(dirname(dirname(dirname(__FILE__)))."/arquivos/".$DescArq);
     }else{
        $Erro = 2;
     }
     $Caminho = dirname(dirname(dirname(__FILE__)))."/arquivos/".$DescArq;
     $usuarioID = arqDafAdm("pessoas_id", $Conec, $xProj, $_SESSION["usuarioCPF"]);
    //modifica a condição na tabela
    $rs = pg_query($Conec, "UPDATE ".$xProj.".daf_arqsetor SET ativo = 0, usuapag = $usuarioID, dataapag = NOW() WHERE codarq = $CodArq");

    //Apaga registros com mais de 5 anos de inativado
    pg_query($Conec, "DELETE FROM ".$xProj.".daf_arqsetor WHERE ativo = 0 And ((CURRENT_DATE - ".$xProj.".daf_arqsetor.dataapag) / 365) > 5 ");

//     $var = array("coderro"=>$Erro, "arquivo"=>$DescArq, "caminho"=>$Caminho);
     $var = array("coderro"=>$Erro);
     $responseText = json_encode($var);
     echo $responseText;
}

if($Acao == "salvaTexto"){  //vem de relPag.php
    $CodSetor = filter_input(INPUT_GET, 'setorid');
    $Text = str_replace("'","\"",$_REQUEST["textopagina"]); // substituir aspas simples por duplas
    $Texto = htmlentities($Text);
//    $Texto = htmlentities(addslashes($_REQUEST["textopagina"])); // addslashes não dá
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".setores SET textopag = '$Texto' WHERE codset = $CodSetor");
    if(!$rs){
        $Erro = 1;
    }
     $var = array("coderro"=>$Erro, "texto"=>$Texto);
     $responseText = json_encode($var);
     echo $responseText;
}
if($Acao == "buscaTextoPag"){  //vem de relPag.php
    $CodSetor = filter_input(INPUT_GET, 'setorid');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT textopag FROM ".$xProj.".setores WHERE codset = $CodSetor");
    if(!$rs){
        $Erro = 1;
    }else{
        $Tbl = pg_fetch_row($rs);
        $Texto = $Tbl["textoPag"];
    }
     $var = array("coderro"=>$Erro, "texto"=>$Texto);
     $responseText = json_encode($var);
     echo $responseText;
}
if($Acao == "salvaTextoIni"){  //vem de indexb.php
    $Text = str_replace("'","\"",$_REQUEST["textopaginaini"]); // substituir aspas simples por duplas
    $Texto = htmlentities($Text);
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".setores SET textopag = '$Texto' WHERE codset = 1");
    if(!$rs){
        $Erro = 1;
    }
     $var = array("coderro"=>$Erro);
     $responseText = json_encode($var);
     echo $responseText;
}