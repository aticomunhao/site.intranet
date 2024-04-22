<?php
require_once("dbcclass.php");

$Conec = conecPost(); // habilitar a extensão: extension=pgsql no phpini
if($Conec != "sConec" && $Conec != "sFunc"){
   $xProj =  "cesb";  //$_SESSION["Projeto"]; 
   $xPes = "public";
}else{
   die("<br>Não foi possível conectar-se ao banco de dados.");
}

$ConecPes = conecPes();
if($ConecPes != "sConec" && $Conec != "sFunc"){
   $xProj =  "cesb";  //$_SESSION["Projeto"]; 
   $xPes = "public";
}else{
   die("<br>Não foi possível conectar-se ao banco de dados de pessoal.");
}

function parAdm($Campo, $Conec, $xProj){
    $rsSis = pg_query($Conec, "SELECT $Campo FROM ".$xProj.".paramsis WHERE idpar = 1");
    $ProcSis = pg_fetch_row($rsSis);
    $admSis = $ProcSis[0]; // nível para inserir 
    return $admSis;
 }