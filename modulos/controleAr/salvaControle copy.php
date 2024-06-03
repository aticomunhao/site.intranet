<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
$Acao = "";
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"]; 
}

if($Acao=="buscadados"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Cel = filter_input(INPUT_GET, 'celula');
    $DataAr = "data".$Cel;
    $NomeAr = "nome".$Cel;

    $Erro = 0;
    $rs = pg_query($Conec, "SELECT num_ap, localap, to_char($DataAr, 'DD/MM/YYYY'), $NomeAr, empresa_id FROM ".$xProj.".controle_ar WHERE id = $Cod");
    $row = pg_num_rows($rs);
    if(!$rs){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $tbl = pg_fetch_row($rs);
        if($tbl[2] == "01/01/1500"){
            $Data = "";
        }else{
            $Data = $tbl[2];
        }

        $var = array("coderro"=>$Erro, "apar"=>str_pad($tbl[0], 3, 0, STR_PAD_LEFT), "local"=>$tbl[1], "data"=>$Data, "nome"=>$tbl[3], "empresa"=>$tbl[4] );
    }
    
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="buscalocal"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT num_ap, localap, empresa_id FROM ".$xProj.".controle_ar WHERE id = $Cod");
    $row = pg_num_rows($rs);
    if(!$rs){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $tbl = pg_fetch_row($rs);
        $var = array("coderro"=>$Erro, "apar"=>str_pad($tbl[0], 3, 0, STR_PAD_LEFT), "local"=>$tbl[1], "empresa"=>$tbl[2] );
    }
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvadados"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Cel = filter_input(INPUT_GET, 'celula');
    $Local = filter_input(INPUT_GET, 'localap');
    $Dat = addslashes(filter_input(INPUT_GET, 'datavis'));
    $Nome = filter_input(INPUT_GET, 'nometec');
    $Empresa = (int) filter_input(INPUT_GET, 'empresa');

    if($Dat == ""){
        $Data = "1500-01-01";
    }else{
        $Data = implode("-", array_reverse(explode("/", $Dat))); // inverte o formato da data para y/m/d
    }
    $DataAr = "data".$Cel;
    $NomeAr = "nome".$Cel;

    $Erro = 0;
    
    if($Cod > 0){
        if($Cel == 0){ // editando só o local do aparelho
            $rs = pg_query($Conec, "UPDATE ".$xProj.".controle_ar SET localap = '$Local', empresa_id = $Empresa WHERE id = $Cod");
        }else{ // editando visita
            $rs = pg_query($Conec, "UPDATE ".$xProj.".controle_ar SET $DataAr = '$Data', $NomeAr = '$Nome', localap = '$Local', empresa_id = $Empresa WHERE id = $Cod");
        }
    }else{
        $rs0 = pg_query($Conec, "SELECT MAX(num_ap) FROM ".$xProj.".controle_ar");
        $tbl0 = pg_fetch_row($rs0);
        $Prox = ($tbl0[0]+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".controle_ar (num_ap) VALUES ($Prox)");

        $rs = pg_query($Conec, "UPDATE ".$xProj.".controle_ar SET $DataAr = '$Data', $NomeAr = '$Nome', localap = '$Local', empresa_id = $Empresa WHERE num_ap = $Prox");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, 'empresa'=>$Empresa);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="buscanumero"){
    $Erro = 0;
    $Prox = 1;
    $rs = pg_query($Conec, "SELECT MAX(num_ap) FROM ".$xProj.".controle_ar");
    $tbl = pg_fetch_row($rs);
    $Prox = ($tbl[0]+1);

    $var = array("coderro"=>$Erro, "apar"=>str_pad($Prox, 3, 0, STR_PAD_LEFT));
    $responseText = json_encode($var);
    echo $responseText;
}
