<?php
session_start();
//ini_set('memory_limit', '1024M');
//$Arq = preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $Arq ));
$Arq = $_FILES['arquivo'];  //recebe o arquivo do formulário
$_SESSION['arquivo'] = "";
$Arquivo = basename($_FILES['arquivo']['name']);

if($Arq['type'] == 'image/jpeg'){
    $temp = $_FILES["arquivo"]["tmp_name"];
    if(move_uploaded_file($temp, "imagens/".$Arquivo)){
        //atualizar o BD - foi para salvaSlide.php
        $_SESSION['geremsg'] = 1;
        $_SESSION["arquivo"] = $Arquivo;
        $_SESSION['msg'] = "Arquivo carregado com sucesso";
    }else{
        $_SESSION['geremsg'] = 0;
        $_SESSION['arquivo'] = "";
        $_SESSION['msg'] = "O arquivo NÃO foi carregado";
    }
}else{
    $_SESSION['geremsg'] = 2;
    $_SESSION["msg"] = "A extensão do arquivo deve ser <b> .jpg </b> - Updload suspenso.";
}