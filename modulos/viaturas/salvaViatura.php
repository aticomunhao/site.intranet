<?php
session_start(); 
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
$Acao = "";
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    $Hoje = date('d/m/Y');
    $HojeIng = date('Y/m/d');
    $UsuIns = $_SESSION['usuarioID'];
}
//numeração do dia da semana da função extract() (DOW) é diferente da função to_char() (D)
//Função para Extract no postgres
$Semana_Extract = array(
    '0' => 'Dom',
    '1' => 'Seg',
    '2' => 'Ter',
    '3' => 'Qua',
    '4' => 'Qui',
    '5' => 'Sex',
    '6' => 'Sab',
    'xª'=> ''
);

if($Acao =="salvaTema"){
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET tema = $Valor WHERE pessoas_id = ".$_SESSION["usuarioID"]." And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="buscaDataCombust"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); 
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT TO_CHAR(datacompra, 'DD/MM/YYYY'), date_part('dow', datacompra), codveiculo, tipocomb, volume, custo, odometro, coddespesa, observ, tipomanut 
    FROM ".$xProj.".viaturas WHERE id = $Cod And ativo = 1");
    $row = pg_num_rows($rs);
    if($row == 0){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $tbl = pg_fetch_row($rs);
        $Dow = $Semana_Extract[$tbl[1]];
        if($tbl[4] > 0){
            $PrecLitro = ($tbl[5]/$tbl[4]);
        }else{
            $PrecLitro = 0;
        }
        $var = array("coderro"=>$Erro, "data"=>$tbl[0], "sem"=>$Dow, "codveic"=>$tbl[2], "tipocomb"=>$tbl[3], "volume"=>number_format(($tbl[4]/100), 2, ",","."), "custo"=>number_format(($tbl[5]/100), 2, ",","."), "odometro"=>$tbl[6], "coddespesa"=>$tbl[7], "obs"=>$tbl[8], "tipomanut"=>$tbl[9], "precolitro"=>number_format($PrecLitro, 3, ",","."));
    }
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="apagareg"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); 
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".viaturas SET ativo = 0 WHERE id = $Cod And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="salvaCompraComb"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); 
    $Despesa = filter_input(INPUT_GET, 'tipodespesa'); // 1=abstec  2=manutenção 
    $TipoViat = filter_input(INPUT_GET, 'tipoviat'); 
    $TipoManut = filter_input(INPUT_GET, 'tipomanut'); 
    $Ob = trim(filter_input(INPUT_GET, 'obs')); 
    $Obs = str_replace("'", '"', $Ob);
    $Odomet = filter_input(INPUT_GET, 'odometro'); 
    $Odometr = str_replace(".", "", $Odomet);
    $Odometro = str_replace(",", "", $Odometr);
    if($Odometro == ""){
        $Odometro = 0;
    }
    $Data = addslashes(filter_input(INPUT_GET, 'datacompra')); 
    $DataVal = implode("/", array_reverse(explode("/", $Data)));
    $Vol = filter_input(INPUT_GET, 'volumecompra'); 
    $Volum = str_replace(",", ".", $Vol);
    $Volume = str_replace(".", "", $Volum);

    if($Despesa == 1){ // abastecimento
        $Combust = filter_input(INPUT_GET, 'tipocombust');
        $TipoManut = 0;
        $Val = filter_input(INPUT_GET, 'valorcompra');
        $Valo = str_replace(",", ".", $Val);
        $Valor = str_replace(".", "", $Valo);
    }
    if($Despesa == 2){ // manutenção
        $Combust = 0;
        $Volume = 0;
        $Val = filter_input(INPUT_GET, 'valormanut');
        $Valo = str_replace(",", ".", $Val);
        $Valor = str_replace(".", "", $Valo);
    }


//    $Valor = ($Valor*100);
//    $Volume = ($Volume*100);

    $Erro = 0;
    if($Cod == 0){
        $CodigoNovo = 0;
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".viaturas");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".viaturas (id, datacompra, coddespesa, codveiculo, tipocomb, volume, custo, odometro, observ, tipomanut, ativo, usuins, datains) 
        VALUES($CodigoNovo, '$DataVal', $Despesa, $TipoViat, $Combust, $Volume, $Valor, $Odometro, '$Obs', $TipoManut, 1, ".$_SESSION["usuarioID"].", NOW() )");
        if(!$rs){
            $Erro = 1;
        }
    }else{
        $rs = pg_query($Conec, "UPDATE ".$xProj.".viaturas SET datacompra = '$DataVal', coddespesa = $Despesa, codveiculo = $TipoViat, tipocomb = $Combust, volume = $Volume, custo = $Valor, odometro = $Odometro, observ = '$Obs', tipomanut = $TipoManut, ativo = 1, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod");
        if(!$rs){
            $Erro = 1;
        }
    }
 
    $var = array("coderro"=>$Erro, "data"=>$DataVal, "valor"=>$Valor);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "configMarcaCheckBox"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id de poslog
    $Campo = filter_input(INPUT_GET, 'campo');
    $Valor = (int) filter_input(INPUT_GET, 'valor');

    if($Campo == "viatura" && $Valor == 0){
        $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE combust = 1");
        $row = pg_num_rows($rs);
        if($row == 1){
            $Erro = 2;
            $var = array("coderro"=>$Erro);
            $responseText = json_encode($var);
            echo $responseText;
            return false;
        }
    }
    $rs1 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET $Campo = '$Valor' WHERE pessoas_id = $Cod");
    if(!$rs1){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao == "buscausuario"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); //id de polog
    $rs1 = pg_query($Conec, "SELECT viatura, fisc_viat, cpf FROM ".$xProj.".poslog WHERE pessoas_id = $Cod");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($rs1);
        $var = array("coderro"=>$Erro, "viatura"=>$tbl1[0], "fiscviatura"=>$tbl1[1], "cpf"=>$tbl1[2]);
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

    $rs1 = pg_query($Conec, "SELECT viatura, fisc_viat, cpf, pessoas_id FROM ".$xProj.".poslog WHERE cpf = '$GuardaCpf'");
    if(!$rs1){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($rs1);
        $var = array("coderro"=>$Erro, "viatura"=>$tbl1[0], "fiscviatura"=>$tbl1[1], "cpf"=>$tbl1[2], "PosCod"=>$tbl1[3]);
    }else{
        $Erro = 2;
        $var = array("coderro"=>$Erro);
    }        
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="buscacombust"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT desc_combust FROM ".$xProj.".viaturas_comb WHERE id = $Cod And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $tbl = pg_fetch_row($rs);
    $var = array("coderro"=>$Erro, "nome"=>$tbl[0]);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="buscatipo"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT desc_viatura FROM ".$xProj.".viaturas_tipo WHERE id = $Cod And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $tbl = pg_fetch_row($rs);
    $var = array("coderro"=>$Erro, "nome"=>$tbl[0] );
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="buscamanut"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT desc_manut FROM ".$xProj.".viaturas_manut WHERE id = $Cod And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $tbl = pg_fetch_row($rs);
    $var = array("coderro"=>$Erro, "nome"=>$tbl[0]);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="salvanovotipo"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Nome = filter_input(INPUT_GET, 'nometipo');
    $Erro = 0;
    if($Cod > 0){ // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".viaturas_tipo SET desc_viatura = '$Nome', usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod ");
    }else{ // inserir
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".viaturas_tipo");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".viaturas_tipo (id, desc_viatura, usuins, datains) VALUES ($CodigoNovo, '$Nome', ".$_SESSION["usuarioID"].", NOW()) ");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="salvanovoComb"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Nome = filter_input(INPUT_GET, 'nomeempr');
    $Erro = 0;
    if($Cod > 0){ // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".viaturas_comb SET desc_combust = '$Nome', usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod ");
    }else{ // inserir
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".viaturas_comb");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".viaturas_comb (id, desc_combust, usuins, datains) VALUES ($CodigoNovo, '$Nome', ".$_SESSION["usuarioID"].", NOW() )");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="salvanovoManut"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Nome = filter_input(INPUT_GET, 'nomemanut');
    $Erro = 0;
    if($Cod > 0){ // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".viaturas_manut SET desc_manut = '$Nome', usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod ");
    }else{ // inserir
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".viaturas_manut");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".viaturas_manut (id, desc_manut, usuins, datains) VALUES ($CodigoNovo, '$Nome', ".$_SESSION["usuarioID"].", NOW() )");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao == "buscareltipos"){
    $rsTipos = pg_query($Conec, "SELECT id, desc_viatura FROM ".$xProj.".viaturas_tipo WHERE ativo = 1 ORDER BY desc_viatura");
    while ($tbl = pg_fetch_row($rsTipos)){
       $TipoExt[] = array(
       'CodE' => $tbl[0],
       'TipoE' => $tbl[1]);
    }
    $responseText = json_encode($TipoExt);
    echo $responseText;
 }
 if($Acao == "buscarelcomb"){
    $rsTipos = pg_query($Conec, "SELECT id, desc_combust FROM ".$xProj.".viaturas_tipo WHERE ativo = 1 ORDER BY desc_combust");
    while ($tbl = pg_fetch_row($rsTipos)){
       $TipoExt[] = array(
       'CodE' => $tbl[0],
       'TipoE' => $tbl[1]);
    }
    $responseText = json_encode($TipoExt);
    echo $responseText;
 }
 if($Acao == "buscarelmanut"){
    $rsTipos = pg_query($Conec, "SELECT id, desc_manut FROM ".$xProj.".viaturas_manut WHERE ativo = 1 ORDER BY desc_manut");
    while ($tbl = pg_fetch_row($rsTipos)){
       $TipoExt[] = array(
       'CodE' => $tbl[0],
       'TipoE' => $tbl[1]);
    }
    $responseText = json_encode($TipoExt);
    echo $responseText;
 }
 if($Acao == "buscaodometro"){
    $Viatura = (int) filter_input(INPUT_GET, 'viatura');
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT MAX(odometro) FROM ".$xProj.".viaturas WHERE ativo = 1 And codveiculo = $Viatura");
    if(!$rs){
        $Erro = 1;
        $Odometro = 0;
    }else{
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $Odometro = $tbl[0];
            if($Odometro > $Valor){
                $Erro = 2;
            }
        } else{
            $Odometro = 0;
        }
    }
    $var = array("coderro"=>$Erro, "ultodometro"=>$Odometro);
    $responseText = json_encode($var);
    echo $responseText;
 }
 if($Acao=="apagaTipo"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    //tira do arq principal
    pg_query($Conec, "UPDATE ".$xProj.".viaturas SET ativo = 0, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE codveiculo = $Cod ");
    $rs = pg_query($Conec, "UPDATE ".$xProj.".viaturas_tipo SET ativo = 0, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod ");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="apagaComb"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    //tira do arq principal
    pg_query($Conec, "UPDATE ".$xProj.".viaturas SET ativo = 0, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE tipocomb = $Cod ");
    $rs = pg_query($Conec, "UPDATE ".$xProj.".viaturas_comb SET ativo = 0, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod ");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="apagaManut"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    //tira do arq principal
    pg_query($Conec, "UPDATE ".$xProj.".viaturas SET ativo = 0, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE tipomanut = $Cod ");
    $rs = pg_query($Conec, "UPDATE ".$xProj.".viaturas_manut SET ativo = 0, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod ");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}