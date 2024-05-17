<?php
session_start(); 
require_once(dirname(dirname(__FILE__))."/dbclass.php");
$Acao = "";
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
}

if($Acao =="buscaTexto"){
    $CodTroca = (int) filter_input(INPUT_GET, 'numero'); 
    $Erro = 0;
    $Texto = "";
    $rs = mysqli_query($xVai, "SELECT textoTroca FROM cesb_trocas WHERE idTr = $CodTroca");
    if(!$rs){
        $Erro = 1;
    }else{
        $tbl = mysqli_fetch_array($rs);
        $Texto = $tbl["textoTroca"];
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
        $rs = mysqli_query($xVai, "UPDATE cesb_trocas SET textoTroca = '$Texto' WHERE idTr = $CodTroca");
        if(!$rs){
            $Erro = 1;
        }
        if($_SESSION["itrArq"] != ""){ // nome do arquivo que foi incorporado ao anúncio - só um
            $rs = mysqli_query($xVai, "INSERT INTO cesb_arqitr (idTroca, idUser, idSetor, dataIns, nomeArq) VALUES ($CodTroca, ".$_SESSION["usuarioID"].", ".$_SESSION["CodSetorUsu"].", NOW(), '".$_SESSION["itrArq"]."')");
            $_SESSION["itrArq"] = "";
        }
    }else{ // inserção
        $rs = mysqli_query($xVai, "INSERT INTO cesb_trocas (textoTroca, idUser, idSetor, trocaAtiva) VALUES ('$Texto', ".$_SESSION['usuarioID'].", ".$_SESSION['CodSetorUsu'].", 1)");
        if(!$rs){
            $Erro = 1;
        }
        $CodigoNovo = mysqli_insert_id($xVai); // obtem o número AUTO_INCREMENT da operação INSERT
        if($_SESSION["itrArq"] != ""){ // nome do arquivo que foi incorporado ao anúncio - só um
            $rs = mysqli_query($xVai, "INSERT INTO cesb_arqitr (idTroca, idUser, idSetor, dataIns, nomeArq) VALUES ($CodigoNovo, ".$_SESSION["usuarioID"].", ".$_SESSION["CodSetorUsu"].", NOW(), '".$_SESSION["itrArq"]."')");
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
    $rs = mysqli_query($xVai, "DELETE FROM cesb_trocas WHERE idTr = $CodTroca") or die ("Deleção: " . mysqli_error($xVai));
    if(!$rs){
        $Erro = 1;
    }
    $rs1 = mysqli_query($xVai, "SELECT nomeArq FROM cesb_arqitr WHERE idTroca = $CodTroca");
    $row1 = mysqli_num_rows($rs1);
    if($row1 > 0){
        While ($tbl1 = mysqli_fetch_array($rs1)){
            $Arq = $tbl1["nomeArq"];
            if(file_exists(dirname(dirname(dirname(__FILE__)))."/itr/".$Arq)){
                unlink(dirname(dirname(dirname(__FILE__)))."/itr/".$Arq);
            }
        }
        $rs = mysqli_query($xVai, "DELETE FROM cesb_arqitr WHERE idTroca = $CodTroca");
    }

    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}