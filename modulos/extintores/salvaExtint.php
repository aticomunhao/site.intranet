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
    $rs = pg_query($Conec, "SELECT empresa, ender, cep, cidade, uf, cnpjempr, inscrempr, telefone, contato, obsempr FROM ".$xProj.".extintores_empr WHERE id = $Cod And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $tbl = pg_fetch_row($rs);
    if(is_null($tbl[5])){
        $Cnpj = "";    
    }else{
        $Cnpj = $tbl[5];
    }

    $var = array("coderro"=>$Erro, "nome"=>$tbl[0], "ender"=>$tbl[1], "cep"=>$tbl[2], "cidade"=>$tbl[3], "uf"=>$tbl[4], "cnpjempr"=>$Cnpj, "inscrempr"=>$tbl[6], "telefone"=>$tbl[7], "contato"=>$tbl[8], "obsempr"=>$tbl[9]);
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
    $editEnder = filter_input(INPUT_GET, 'editEnder');
    $editCEP = filter_input(INPUT_GET, 'editCEP');
    $editCidade = filter_input(INPUT_GET, 'editCidade');
    $editUF = filter_input(INPUT_GET, 'editUF');
    if(strlen($editUF) == 2 ){
        $editUF = strtoupper($editUF);
    }
    $editC = addslashes(filter_input(INPUT_GET, 'editCNPJ'));
    $editCN = str_replace(".", "", $editC);
    $editCNP = str_replace("/", "", $editCN);
    $editCNPJ = str_replace("-", "", $editCNP);
    $editInscr = filter_input(INPUT_GET, 'editInscr');
    $editTelef = filter_input(INPUT_GET, 'editTelef');
    $editContato = filter_input(INPUT_GET, 'editContato');
    $editObs = filter_input(INPUT_GET, 'editObs');
    $Erro = 0;
    if($Cod > 0){ // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".extintores_empr SET empresa = '$Nome', ender = '$editEnder', cep = '$editCEP', cidade = '$editCidade', uf = '$editUF', cnpjempr = '$editCNPJ', inscrempr = '$editInscr', telefone = '$editTelef', contato = '$editContato', obsempr = '$editObs', usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod ");
    }else{ // inserir
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".extintores_empr");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".extintores_empr (id, empresa, ender, cep, cidade, uf, cnpjempr, inscrempr, telefone, contato, obsempr, usuins, datains) 
        VALUES ($CodigoNovo, '$Nome', '$editEnder', '$editCEP', '$editCidade', '$editUF', '$editCNPJ', '$editInscr', '$editTelef', '$editContato', '$editObs', ".$_SESSION["usuarioID"].", NOW() )");
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
    $Compl = trim(strtoupper(filter_input(INPUT_GET, 'complem')));
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
        $rs = pg_query($Conec, "UPDATE ".$xProj.".extintores SET ext_num = $Num, ext_compl = '$Compl', ext_local = '$Local', ext_empresa = $Empresa, ext_tipo = $Tipo, ext_capac = '$Capac', ext_reg = '$Registro', ext_serie = '$NumSerie', datacarga = '$DataRev', datavalid = '$DataVal', datacasco = '$DataCasco', ativo = 1, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod ");
    }else{ // inserir
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".extintores");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".extintores (id, ext_num, ext_compl, ext_local, ext_empresa, ext_tipo, ext_capac, ext_reg, ext_serie, datacarga, datavalid, datacasco, ativo, usuins, datains) 
        VALUES ($CodigoNovo, $Num, '$Compl', '$Local', $Empresa, $Tipo, '$Capac', '$Registro', '$NumSerie', '$DataRev', '$DataVal', '$DataCasco', 1, ".$_SESSION["usuarioID"].", NOW() ) ");
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
    $rs = pg_query($Conec, "SELECT id, ext_num, ext_local, ext_empresa, ext_tipo, ext_capac, ext_reg, ext_serie, TO_CHAR(datacarga, 'DD/MM/YYYY'), TO_CHAR(datavalid, 'DD/MM/YYYY'), TO_CHAR(datacasco, 'DD/MM/YYYY'), 
    ext_compl, ativo, usuins, datains FROM ".$xProj.".extintores WHERE id = $Cod");
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

    $var = array("coderro"=>$Erro, "extint"=>str_pad($tbl[1], 3, 0, STR_PAD_LEFT), "local"=>$tbl[2], "empresa"=>$tbl[3], "tipo"=>$tbl[4], "capacid"=>$tbl[5], "registro"=>$tbl[6], "numserie"=>$tbl[7], "revis"=>$tbl[8], "valid"=>$tbl[9], "casco"=>$tbl[10], "complem"=>$tbl[11] );
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvaaviso"){
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET aviso_extint = $Valor WHERE idpar = 1");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="buscaConfig"){
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT aviso_extint FROM ".$xProj.".paramsis WHERE idpar = 1");
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        $DiasAviso = $tbl[0];
    }else{
        $DiasAviso = 30;
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "aviso"=>$DiasAviso);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="apagaextint"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".extintores SET ativo = 0 WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao == "buscausuario"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); //id de polog
    $rs1 = pg_query($Conec, "SELECT extint, fisc_extint, cpf FROM ".$xProj.".poslog WHERE pessoas_id = $Cod");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($rs1);
        $var = array("coderro"=>$Erro, "extint"=>$tbl1[0], "fiscextint"=>$tbl1[1], "cpf"=>$tbl1[2]);
    }else{
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }        
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao == "buscacpfusuario"){
    $Erro = 0;
    $Cpf = filter_input(INPUT_GET, 'cpf'); 
    $Cpf1 = addslashes($Cpf);
    $Cpf2 = str_replace(".", "", $Cpf1);
    $GuardaCpf = str_replace("-", "", $Cpf2);

    $rs1 = pg_query($Conec, "SELECT extint, fisc_extint, cpf, pessoas_id FROM ".$xProj.".poslog WHERE cpf = '$GuardaCpf'");
    if(!$rs1){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($rs1);
        $var = array("coderro"=>$Erro, "extint"=>$tbl1[0], "fiscextint"=>$tbl1[1], "cpf"=>$tbl1[2], "PosCod"=>$tbl1[3]);
    }else{
        $Erro = 2;
        $var = array("coderro"=>$Erro);
    }        
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao == "configMarcaCheckBox"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id de poslog
    $Campo = filter_input(INPUT_GET, 'campo');
    $Valor = (int) filter_input(INPUT_GET, 'valor');

    if($Campo == "extint" && $Valor == 0){
        $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE extint = 1");
        $row = pg_num_rows($rs);
        if($row == 1){
            $Erro = 2;
            $var = array("coderro"=>$Erro);
            $responseText = json_encode($var);
            echo $responseText;
            return false;
        }
    }

    $rs1 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET $Campo = $Valor WHERE pessoas_id = $Cod");
    if(!$rs1){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="transfDatas"){
    $Campo = filter_input(INPUT_GET, 'campo');
    $DataR = addslashes(filter_input(INPUT_GET, 'datarevis'));
    $DataV = addslashes(filter_input(INPUT_GET, 'datavalid'));
    $DataC = addslashes(filter_input(INPUT_GET, 'datavalcasco'));

    $DataRev = implode("-", array_reverse(explode("/", $DataR))); // inverte o formato da data para y/m/d
    $DataVal = implode("-", array_reverse(explode("/", $DataV))); // inverte o formato da data para y/m/d
    $DataCas = implode("-", array_reverse(explode("/", $DataC))); // inverte o formato da data para y/m/d

    $Erro = 0;
    if($Campo == "Revisão"){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".extintores SET datacarga = '$DataRev' WHERE ativo = 1");
    }
    if($Campo == "Vencimento"){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".extintores SET datavalid = '$DataVal' WHERE ativo = 1");
    }
    if($Campo == "Validade"){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".extintores SET datacasco = '$DataCas' WHERE ativo = 1");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}