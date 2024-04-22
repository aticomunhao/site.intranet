<?php
session_start(); 
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
$Acao = "";
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
}

if($Acao =="buscaTexto"){
    $CodTroca = (int) filter_input(INPUT_GET, 'numero'); 
    $Erro = 0;
    $Texto = "";
    $rs = pg_query($Conec, "SELECT textotroca FROM ".$xProj.".trocas WHERE idtr = $CodTroca");
    if(!$rs){
        $Erro = 1;
    }else{
        $tbl = pg_fetch_row($rs);
        $Texto = $tbl[0];
    }
    $var = array("coderro"=>$Erro, "textotroca"=>$Texto);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaTroca"){
    $CodTroca = (int) filter_input(INPUT_GET, 'numero'); 
    $Texto = filter_input(INPUT_GET, 'texto'); 
    $Erro = 0;
    if($CodTroca > 0){ // edição
        $rs = pg_query($Conec, "UPDATE ".$xProj.".trocas SET textotroca = '$Texto' WHERE idtr = $CodTroca");
        if(!$rs){
            $Erro = 1;
        }
        if($_SESSION["itrArq"] != ""){ // nome do arquivo que foi incorporado ao anúncio - só um
            $rsCod = pg_query($Conec, "SELECT MAX(iditr) FROM ".$xProj.".arqitr");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1);
            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".arqitr (iditr, idtroca, iduser, idsetor, datains, nomearq) VALUES ($CodigoNovo, $CodTroca, ".$_SESSION["usuarioID"].", ".$_SESSION["CodSetorUsu"].", NOW(), '".$_SESSION["itrArq"]."')");
            $_SESSION["itrArq"] = "";
        }
    }else{ // inserção
        $rsCod = pg_query($Conec, "SELECT MAX(idtr) FROM ".$xProj.".trocas");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".trocas (idtr, textotroca, iduser, idsetor, trocaativa) VALUES ($CodigoNovo, '$Texto', ".$_SESSION['usuarioID'].", ".$_SESSION['CodSetorUsu'].", 1)");
        if(!$rs){
            $Erro = 1;
        }
        $rsCod = pg_query($Conec, "SELECT MAX(idtr) FROM ".$xProj.".trocas");
        $tblCod = pg_fetch_row($rsCod);
        $UltCodigo = $tblCod[0]; // inserção no arquivo trocas
//        $CodigoNovo = mysqli_insert_id($xVai); // obtem o número AUTO_INCREMENT da operação INSERT

        if($_SESSION["itrArq"] != ""){ // nome do arquivo que foi incorporado ao anúncio - só um
            $rsCod = pg_query($Conec, "SELECT MAX(iditr) FROM ".$xProj.".arqitr");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1);
                $rs = pg_query($Conec, "INSERT INTO ".$xProj.".arqitr (iditr, idTroca, idUser, idSetor, dataIns, nomeArq) VALUES ($CodigoNovo, $UltCodigo, ".$_SESSION["usuarioID"].", ".$_SESSION["CodSetorUsu"].", NOW(), '".$_SESSION["itrArq"]."')");
            $_SESSION["itrArq"] = "";
        }
    }
    $var = array("coderro"=>$Erro, "textotroca"=>$Texto);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="apagaTroca"){
    $CodTroca = (int) filter_input(INPUT_GET, 'numero'); 
    $Erro = 0;
    $Texto = "";
    $rs = pg_query($Conec, "DELETE FROM ".$xProj.".trocas WHERE idtr = $CodTroca");
    if(!$rs){
        $Erro = 1;
    }
    $rs1 = pg_query($Conec, "SELECT nomearq FROM ".$xProj.".arqitr WHERE idtroca = $CodTroca");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        While ($tbl1 = pg_fetch_row($rs1)){
            $Arq = $tbl1[0];
            if(file_exists(dirname(dirname(dirname(__FILE__)))."/itr/".$Arq)){
                unlink(dirname(dirname(dirname(__FILE__)))."/itr/".$Arq);
            }
        }
        $rs = pg_query($Conec, "DELETE FROM ".$xProj.".arqitr WHERE idtroca = $CodTroca");
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}