<?php
$url = $_SERVER["PHP_SELF"];
if(strtolower($url) == "/cesb/modulos/dbclass.php"){
   header("Location: ../index.php");
}else{
   $Dbase = include(dirname(__FILE__).'/config/dbiclass.php');
   $link = mysqli_connect($Dbase['servidor'], $Dbase['usuario'], $Dbase['senha']) or die("Não conectado");
   mysqli_select_db($link, $Dbase['banco']) or die("Não foi possível conectar-se ao banco de dados.");
   $xVai = mysqli_connect($Dbase['servidor'], $Dbase['usuario'], $Dbase['senha'], $Dbase['banco']);
   $xVai->set_charset("utf8");
   $_SESSION['Conec'] = $xVai;
   $_SESSION['ConecAtiv'] = 1;
}

function parAdm($Campo){
   $xVai = $_SESSION['Conec'];
//   $rsSis = mysqli_query($xVai, "SELECT insAniver, editAniver FROM cesb_paramsis WHERE idPar = 1");
   $rsSis = mysqli_query($xVai, "SELECT $Campo FROM cesb_paramsis WHERE idPar = 1");
   $ProcSis = mysqli_fetch_array($rsSis);
   $admSis = $ProcSis[$Campo]; // nível para inserir 
   return $admSis;
}