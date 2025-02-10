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

if($Acao == "buscarelempresas"){
    $rsEmpr = pg_query($Conec, "SELECT id, empresa FROM ".$xProj.".extintores_empr WHERE ativo = 1 ORDER BY empresa");
    while ($tbl = pg_fetch_row($rsEmpr)){
       $Empr[] = array(
       'Cod' => $tbl[0],
       'Nome' => $tbl[1]);
    }
    $responseText = json_encode($Empr);
    echo $responseText;
 }
 if($Acao == "buscareltipos"){
    $rsTipos = pg_query($Conec, "SELECT id, desc_tipo FROM ".$xProj.".extintores_tipo WHERE ativo = 1 ORDER BY desc_tipo");
    while ($tbl = pg_fetch_row($rsTipos)){
       $TipoExt[] = array(
       'CodE' => $tbl[0],
       'TipoE' => $tbl[1]);
    }
    $responseText = json_encode($TipoExt);
    echo $responseText;
 }
 if($Acao=="buscaempresa"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT empresa FROM ".$xProj.".extintores_empr WHERE id = $Cod And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $tbl = pg_fetch_row($rs);
    $var = array("coderro"=>$Erro, "nome"=>$tbl[0] );
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="buscatipo"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT desc_tipo FROM ".$xProj.".extintores_tipo WHERE id = $Cod And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $tbl = pg_fetch_row($rs);
    $var = array("coderro"=>$Erro, "nome"=>$tbl[0] );
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="buscanumero"){
    $Erro = 0;
    $Prox = 1;
    $rs = pg_query($Conec, "SELECT MAX(ext_num) FROM ".$xProj.".extintores");
    $tbl = pg_fetch_row($rs);
    $Prox = ($tbl[0]+1);

    $var = array("coderro"=>$Erro, "extint"=>str_pad($Prox, 3, 0, STR_PAD_LEFT));
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvanomeempresa"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Nome = filter_input(INPUT_GET, 'nomeempresa');
     $Erro = 0;
    if($Cod > 0){ // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".extintores_empr SET empresa = '$Nome' WHERE id = $Cod ");
    }else{ // inserir
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".extintores_empr");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".extintores_empr (id, empresa) VALUES ($CodigoNovo, '$Nome') ");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvanometipo"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Nome = filter_input(INPUT_GET, 'nometipo');
     $Erro = 0;
    if($Cod > 0){ // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".extintores_tipo SET desc_tipo = '$Nome' WHERE id = $Cod ");
    }else{ // inserir
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".extintores_tipo");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".extintores_tipo (id, desc_tipo) VALUES ($CodigoNovo, '$Nome') ");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvadados"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Num = filter_input(INPUT_GET, 'numero');
    $Registro = filter_input(INPUT_GET, 'registroextint');
    $NumSerie = filter_input(INPUT_GET, 'serieextint');
    $Local = filter_input(INPUT_GET, 'localextint');
    $Capac = filter_input(INPUT_GET, 'capacidextint');
    $Dat = addslashes(filter_input(INPUT_GET, 'datarevis'));
    if($Dat == ""){
        $DataRev = "3000-12-31";
    }else{
        $DataRev = implode("-", array_reverse(explode("/", $Dat))); // inverte o formato da data para y/m/d
    }
    $DataV = addslashes(filter_input(INPUT_GET, 'datavalid'));
    if($DataV == ""){
        $DataVal = "3000-12-31";
    }else{
        $DataVal = implode("-", array_reverse(explode("/", $DataV))); // inverte o formato da data para y/m/d
    }
    $DataC = addslashes(filter_input(INPUT_GET, 'datavalcasco'));
    if($DataC == ""){
        $DataCasco = "3000-12-31";
    }else{
        $DataCasco = implode("-", array_reverse(explode("/", $DataC))); // inverte o formato da data para y/m/d
    }
    $Tipo = filter_input(INPUT_GET, 'tipoextint');
    if($Tipo == ""){
        $Tipo = 0;
    }
    $Empresa = filter_input(INPUT_GET, 'empresa');
    if($Empresa == ""){
        $Empresa = 0;
    }
       
     $Erro = 0;
    if($Cod > 0){ // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".extintores SET ext_num = $Num, ext_local = '$Local', ext_empresa = $Empresa, ext_tipo = $Tipo, ext_capac = '$Capac', ext_reg = '$Registro', ext_serie = '$NumSerie', datacarga = '$DataRev', datavalid = '$DataVal', datacasco = '$DataCasco', ativo = 1, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod ");
    }else{ // inserir
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".extintores");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".extintores (id, ext_num, ext_local, ext_empresa, ext_tipo, ext_capac, ext_reg, ext_serie, datacarga, datavalid, datacasco, ativo, usuins, datains) 
        VALUES ($CodigoNovo, $Num, '$Local', $Empresa, $Tipo, '$Capac', '$Registro', '$NumSerie', '$DataRev', '$DataVal', '$DataCasco', 1, ".$_SESSION["usuarioID"].", NOW() ) ");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="buscaextintor"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT id, ext_num, ext_local, ext_empresa, ext_tipo, ext_capac, ext_reg, ext_serie, TO_CHAR(datacarga, 'DD/MM/YYYY'), TO_CHAR(datavalid, 'DD/MM/YYYY'), TO_CHAR(datacasco, 'DD/MM/YYYY'), ativo, usuins, datains FROM ".$xProj.".extintores WHERE id = $Cod");
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
    }else{
        $Erro = 1;
        $var = array("coderro"=>$Erro, "row"=>$row );
        $responseText = json_encode($var);
        echo $responseText;
        return false;
    }

    $var = array("coderro"=>$Erro, "extint"=>str_pad($tbl[1], 3, 0, STR_PAD_LEFT), "local"=>$tbl[2], "empresa"=>$tbl[3], "tipo"=>$tbl[4], "capacid"=>$tbl[5], "registro"=>$tbl[6], "numserie"=>$tbl[7], "revis"=>$tbl[8], "valid"=>$tbl[9], "casco"=>$tbl[10] );
    $responseText = json_encode($var);
    echo $responseText;
}