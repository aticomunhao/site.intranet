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
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT num_ap, localap FROM ".$xProj.".controle_ar WHERE id = $Cod");
    $row = pg_num_rows($rs);
    if(!$rs){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $tbl = pg_fetch_row($rs);
        if(!is_null($tbl[1])){
            $Local = $tbl[1];
        }else{
            $Local = "";
        }
        $var = array("coderro"=>$Erro, "apar"=>str_pad($tbl[0], 3, 0, STR_PAD_LEFT), "local"=>$Local );
    }
    
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvadados"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Local = filter_input(INPUT_GET, 'localap');
    $Empresa = (int) filter_input(INPUT_GET, 'empresa');

    $Erro = 0;
    $rs0 = pg_query($Conec, "SELECT MAX(num_ap) FROM ".$xProj.".controle_ar");
    $tbl0 = pg_fetch_row($rs0);
    $Prox = ($tbl0[0]+1);
    $rs = pg_query($Conec, "INSERT INTO ".$xProj.".controle_ar (num_ap, localap, empresa_id) VALUES ($Prox, '$Local', $Empresa)");
    
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

if($Acao=="buscadata"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT num_ap, localap, to_char(datavis, 'DD/MM/YYYY'), nometec, ".$xProj.".visitas_ar.empresa_id, tipovis 
    FROM ".$xProj.".controle_ar INNER JOIN ".$xProj.".visitas_ar ON ".$xProj.".controle_ar.id = ".$xProj.".visitas_ar.controle_id 
    WHERE ".$xProj.".visitas_ar.id = $Cod");
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
        $var = array("coderro"=>$Erro, "cod"=>$Cod, "apar"=>str_pad($tbl[0], 3, 0, STR_PAD_LEFT), "local"=>$tbl[1], "data"=>$Data, "nome"=>$tbl[3], "empresa"=>$tbl[4], "tipomanut"=>$tbl[5] );
    }
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvadatains"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Dat = addslashes(filter_input(INPUT_GET, 'datavis'));
    $Nome = filter_input(INPUT_GET, 'nometec');
    $Empresa = (int) filter_input(INPUT_GET, 'empresa');
    $Tipo = (int) filter_input(INPUT_GET, 'tipomanut');

    if($Dat == ""){
        $Data = "1500-01-01";
    }else{
        $Data = implode("-", array_reverse(explode("/", $Dat))); // inverte o formato da data para y/m/d
    }
    $Erro = 0;
    $rs = pg_query($Conec, "INSERT INTO ".$xProj.".visitas_ar (controle_id, datavis, nometec, usuins, datains, empresa_id, ativo, tipovis) 
    VALUES ($Cod, '$Data', '$Nome', ".$_SESSION["usuarioID"].", NOW(), $Empresa, 1, $Tipo)");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="salvadataedit"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Dat = addslashes(filter_input(INPUT_GET, 'datavis'));
    $Nome = filter_input(INPUT_GET, 'nometec');
    $Empresa = (int) filter_input(INPUT_GET, 'empresa');
    $Tipo = (int) filter_input(INPUT_GET, 'tipomanut');
    if($Dat == ""){
        $Data = "1500-01-01";
    }else{
        $Data = implode("-", array_reverse(explode("/", $Dat))); // inverte o formato da data para y/m/d
    }
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".visitas_ar SET datavis = '$Data', nometec = '$Nome', empresa_id = $Empresa, tipovis = $Tipo, ativo = 1, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod");
    
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
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
if($Acao=="salvalocal"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Local = filter_input(INPUT_GET, 'local');
    $Empresa = (int) filter_input(INPUT_GET, 'empresa');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".controle_ar SET localap = '$Local', empresa_id = $Empresa, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="apagadata"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".visitas_ar SET ativo = 0, usudel = ".$_SESSION["usuarioID"].", datadel = NOW() WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
