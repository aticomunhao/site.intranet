<?php
require_once("dbcclass.php");
$url = $_SERVER["PHP_SELF"];
if(strtolower($url) == $urlIni."modulos/config/abrealas.php"){
   $_SESSION["usuarioID"] = 0;
   header("Location: $urlIni");
}

$ConecPes = conecPes();
if($ConecPes != "sConec" && $ConecPes != "sFunc"){
   $xProj =  "cesb";
   $xPes = "public";
}else{
   if($ConecPes == "sFunc"){
      die("<br>Não foi possível conectar-se ao banco de dados Pessoal. <br>Habilite a extensão pgsql no PHP.");
   }else{
      die("<br>Não foi possível conectar-se ao banco de dados Pessoal. <br>Verifique os parâmetros da conexão.");
   }
}

$Conec = conecPost(); // habilitar a extensão: extension = pgsql no phpini
if($Conec != "sConec" && $Conec != "sFunc"){
   $xProj =  "cesb"; 
   $xPes = "public";
}else{
   die("<br>Não foi possível conectar-se ao banco de dados Cesb.");
}

function parAdm__($Campo, $Conec, $xProj){
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
function parAdm($Campo, $Conec, $xProj){
   $rs = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'paramsis' AND COLUMN_NAME = '$Campo' ");
//   $rs = pg_query($Conec, "SELECT t.column_name FROM information_schema.columns AS t WHERE t.table_schema = 'cesb' And t.table_name = '$Campo' ");
   $row = pg_num_rows($rs);
   if($row > 0){
      $rsSis = pg_query($Conec, "SELECT $Campo FROM ".$xProj.".paramsis WHERE idpar = 1");
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
function escMenu($Conec, $xProj, $Cod){
   $rsSis = pg_query($Conec, "SELECT descr FROM ".$xProj.".cesbmenu WHERE id = $Cod");
   $row = pg_num_rows($rsSis);
   if($row > 0){
      $ProcSis = pg_fetch_row($rsSis);
      $escSis = $ProcSis[0]; // nível para inserir 
   }else{
      $escSis = "Menu".$Cod;
   }
   return $escSis;
}

function calcRecuaPrazoDias($DataFut, $Dias){
   list($dia, $mes, $ano) = explode('/', $DataFut);
   $DataUnix = mktime( 0, 0, 0, $mes, $dia, $ano);
   $DiasUnix = ($Dias*86400); // valor de 1 dia
   $Data = date('d/m/Y', $DataUnix-$DiasUnix);
   return $Data;
}

function calculaDifDias($DataAnt, $DataFut){
   list($dia, $mes, $ano) = explode('/', $DataAnt);  // Separa em dia, mês e ano
   $DataUnix1 = mktime( 0, 0, 0, $mes, $dia, $ano);
   list($dia, $mes, $ano) = explode('/', $DataFut);  // Separa em dia, mês e ano
   $DataUnix2 = mktime( 0, 0, 0, $mes, $dia, $ano);
   $Dias = floor((((($DataUnix1 - $DataUnix2) / 60) / 60) / 24)); // Dias decorridos
   $Dias = $Dias*-1;
   return $Dias;
}

function calculaDiasDecorridos($Data1){
   list($dia, $mes, $ano) = explode('/', $Data1);  // Separa em dia, mês e ano
   $DataUnix = mktime( 0, 0, 0, $mes, $dia, $ano);
   $HojeUnix = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
   $Dias = floor((((($HojeUnix - $DataUnix) / 60) / 60) / 24)); // Dias decorridos
   return $Dias;
}
