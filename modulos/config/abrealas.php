<?php
require_once("dbcclass.php");
$url = $_SERVER["PHP_SELF"];
if(strtolower($url) == $urlIni."modulos/config/abrealas.php"){
   $_SESSION["usuarioID"] = 0;
   header("Location: $urlIni");
}

$ConecPes = conecPes();
if($ConecPes != "sConec" && $Conec != "sFunc"){
   $xProj =  "cesb";
   $xPes = "public";
}else{
   die("<br>Não foi possível conectar-se ao banco de dados Pessoal.");
}

$Conec = conecPost(); // habilitar a extensão: extension = pgsql no phpini
if($Conec != "sConec" && $Conec != "sFunc"){
   $xProj =  "cesb"; 
   $xPes = "public";
}else{
   die("<br>Não foi possível conectar-se ao banco de dados Cesb.");
}

function parAdm($Campo, $Conec, $xProj){
    $rsSis = pg_query($Conec, "SELECT $Campo FROM ".$xProj.".paramsis WHERE idpar = 1");
    $row = pg_num_rows($rsSis);
    if($row > 0){
      $ProcSis = pg_fetch_row($rsSis);  
      $admSis = $ProcSis[0]; // nível para inserir 
    }else{
      $admSis = 0;
    }
    return $admSis;
 }

 function parEsc($Campo, $Conec, $xProj, $Cod){
   $rsSis = pg_query($Conec, "SELECT $Campo FROM ".$xProj.".poslog WHERE pessoas_id = $Cod");
   $row = pg_num_rows($rsSis);
   if($row > 0){
      $ProcSis = pg_fetch_row($rsSis);
      $escSis = $ProcSis[0]; // nível para inserir 
   }else{
      $escSis = 0;
   }
   return $escSis;
}