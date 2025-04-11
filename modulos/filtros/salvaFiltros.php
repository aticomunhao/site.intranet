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

if($Acao == "buscausuario"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); //id de polog
    $rs1 = pg_query($Conec, "SELECT filtros, fisc_filtros, cpf FROM ".$xProj.".poslog WHERE pessoas_id = $Cod");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($rs1);
        $var = array("coderro"=>$Erro, "filtros"=>$tbl1[0], "fiscfiltros"=>$tbl1[1], "cpf"=>$tbl1[2]);
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

    $rs1 = pg_query($Conec, "SELECT filtros, fisc_filtros, cpf, pessoas_id FROM ".$xProj.".poslog WHERE cpf = '$GuardaCpf'");
    if(!$rs1){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($rs1);
        $var = array("coderro"=>$Erro, "filtros"=>$tbl1[0], "fiscfiltros"=>$tbl1[1], "cpf"=>$tbl1[2], "PosCod"=>$tbl1[3]);
    }else{
        $Erro = 2;
        $var = array("coderro"=>$Erro);
    }        
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "buscadadosFiltro"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo');

    $rs0 = pg_query($Conec, "SELECT id, numapar, codmarca, tipofiltro, codempr, modelo, localinst, observ, TO_CHAR(datatroca, 'DD/MM/YYYY'), TO_CHAR(datavencim, 'DD/MM/YYYY'), TO_CHAR(dataaviso, 'DD/MM/YYYY'), 
    TO_CHAR(datatroca, 'YYYY'), TO_CHAR(datavencim, 'YYYY'), TO_CHAR(dataaviso, 'YYYY'), prazotroca, diasanteced, notific, pararaviso 
    FROM ".$xProj.".filtros 
    WHERE id = $Cod And ativo = 1 ");
    if(!$rs0){
        $Erro = 1;
    }else{
        $tbl0 = pg_fetch_row($rs0);
        if($tbl0[11] == "3000"){
            $DataTroca = "";
        }else{
            $DataTroca = $tbl0[8];
        }
        if($tbl0[12] == "3000"){
            $DataVenc = "";
        }else{
            $DataVenc = $tbl0[9];
        }
        if($tbl0[13] == "3000"){
            $DataAvis = "";
        }else{
            $DataAvis = $tbl0[10];
        }
        if(is_null($tbl0[14]) || $tbl0[14] == ""){
            $Prazo = "12";
        }else{
            $Prazo = $tbl0[14];
        }
        if(is_null($tbl0[15]) || $tbl0[15] == ""){
            $Anteced = "30";
        }else{
            $Anteced = $tbl0[15];
        }
    }
    $var = array("coderro"=>$Erro, "numapar"=>str_pad($tbl0[1], 3, 0, STR_PAD_LEFT), "codmarca"=>$tbl0[2], "codtipo"=>$tbl0[3], "codempr"=>$tbl0[4], "modelo"=>$tbl0[5], "localinst"=>$tbl0[6], "observ"=>$tbl0[7], "datatroca"=>$DataTroca, "datavenc"=>$DataVenc, "dataaviso"=>$DataAvis, "prazotroca"=>$Prazo, "diasanteced"=>$Anteced, "notific"=>$tbl0[16], "pararAviso"=>$tbl0[17]);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "configMarcaCheckBox"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id de poslog
    $Campo = filter_input(INPUT_GET, 'campo');
    $Valor = (int) filter_input(INPUT_GET, 'valor');

    if($Campo == "filtros" && $Valor == 0){
        $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE filtros = 1");
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
if($Acao=="buscaMarca"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT descmarca FROM ".$xProj.".filtros_marcas WHERE id = $Cod And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $tbl = pg_fetch_row($rs);
    $var = array("coderro"=>$Erro, "nome"=>$tbl[0] );
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="salvanovaMarca"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Nome = filter_input(INPUT_GET, 'nomemarca');
    $Erro = 0;
    if($Cod > 0){ // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".filtros_marcas SET descmarca = '$Nome', usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod ");
    }else{ // inserir
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".filtros_marcas");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".filtros_marcas (id, descmarca, usuins, datains) VALUES ($CodigoNovo, '$Nome', ".$_SESSION["usuarioID"].", NOW()) ");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "nome"=>$Nome);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="apagaMarca"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    //tira do arq principal
    pg_query($Conec, "UPDATE ".$xProj.".filtros SET codmarca = 1, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE codmarca = $Cod ");
    $rs = pg_query($Conec, "UPDATE ".$xProj.".filtros_marcas SET ativo = 0, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod ");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="buscaTipo"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT desctipo FROM ".$xProj.".filtros_tipos WHERE id = $Cod And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $tbl = pg_fetch_row($rs);
    $var = array("coderro"=>$Erro, "nome"=>$tbl[0] );
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="salvanovoTipo"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Nome = filter_input(INPUT_GET, 'nometipo');
    $Erro = 0;
    if($Cod > 0){ // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".filtros_tipos SET desctipo = '$Nome', usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod ");
    }else{ // inserir
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".filtros_tipos");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".filtros_tipos (id, desctipo, usuins, datains) VALUES ($CodigoNovo, '$Nome', ".$_SESSION["usuarioID"].", NOW()) ");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="apagaTipo"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    //tira do arq principal
    pg_query($Conec, "UPDATE ".$xProj.".filtros SET tipofiltro = 1, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE tipofiltro = $Cod ");
    $rs = pg_query($Conec, "UPDATE ".$xProj.".filtros_tipos SET ativo = 0, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod ");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="buscaEmpresa"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT descempresa, ender, cep, cidade, uf, cnpjempr, inscrempr, telefone, contato, obsempr FROM ".$xProj.".filtros_empr WHERE id = $Cod And ativo = 1");
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
if($Acao=="salvaEmpresa"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
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
        $rs = pg_query($Conec, "UPDATE ".$xProj.".filtros_empr SET descempresa = '$Nome', ender = '$editEnder', cep = '$editCEP', cidade = '$editCidade', uf = '$editUF', cnpjempr = '$editCNPJ', inscrempr = '$editInscr', telefone = '$editTelef', contato = '$editContato', obsempr = '$editObs', usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod ");
    }else{ // inserir
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".filtros_empr");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".filtros_empr (id, descempresa, ender, cep, cidade, uf, cnpjempr, inscrempr, telefone, contato, obsempr, usuins, datains) 
        VALUES ($CodigoNovo, '$Nome', '$editEnder', '$editCEP', '$editCidade', '$editUF', '$editCNPJ', '$editInscr', '$editTelef', '$editContato', '$editObs', ".$_SESSION["usuarioID"].", NOW() )");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "calcprazo"){
    $Erro = 0;
    $DataAss = addslashes(filter_input(INPUT_GET, 'datatroca')); 
    $Data = implode("/", array_reverse(explode("/", $DataAss)));
    $Prazo = filter_input(INPUT_GET, 'prazoselec'); 
    $DiasAntec = filter_input(INPUT_GET, 'diasanteced'); 
    $DataFim = "31/12/3000";
    $PrazoDias = 0;

    $rsCod = pg_query($Conec, "SELECT MAX(numapar) FROM ".$xProj.".filtros");
    $tblCod = pg_fetch_row($rsCod);
    $Codigo = $tblCod[0];
    $NovoFiltro = ($Codigo+1);

    pg_query($Conec, "UPDATE ".$xProj.".poslog SET calcdata1 = '$Data' WHERE pessoas_id = $UsuIns");

    $rs = pg_query($Conec, "SELECT to_char(calcdata1 + interval '".$Prazo." months', 'dd/mm/yyyy') FROM ".$xProj.".poslog WHERE pessoas_id = $UsuIns");
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        $DataFim = $tbl[0];
        $PrazoDias = calculaDifDias($DataAss, $DataFim);
    }

    $DataAviso = "";
    if($DiasAntec != "" && $DiasAntec > 0){
        $DataAviso = calcRecuaPrazoDias($DataFim, $DiasAntec); // (vencimento - dias de antecedência - abrealas.php)
    }

    $var = array("coderro"=>$Erro, "datafinal"=>$DataFim, "prazodias"=>$PrazoDias, "dataaviso"=>$DataAviso, "proxnumero"=>str_pad($NovoFiltro, 3, 0, STR_PAD_LEFT));
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "calcaviso"){
    $Erro = 0;
    $DataVenc = addslashes(filter_input(INPUT_GET, 'vencim')); 
    $DiasAntec = (int) filter_input(INPUT_GET, 'diasanteced'); 
    
    if($DiasAntec > 0){
        $DataAviso = calcRecuaPrazoDias($DataVenc, $DiasAntec); // (vencimento - dias de antecedência - abrealas.php)
    }else{
        $DataAviso = "";
    }

    //Procura o prazo
    $PrazoM = "";
    $PrazoD = "";
    $DataA = addslashes(filter_input(INPUT_GET, 'datatroca')); 
    $DataAss = implode("/", array_reverse(explode("/", $DataA)));
    $DataV = implode("/", array_reverse(explode("/", $DataVenc)));
    pg_query($Conec, "UPDATE ".$xProj.".poslog SET calcdata1 = '$DataV', calcdata2 = '$DataAss' WHERE pessoas_id = $UsuIns");

    $rs = pg_query($Conec, "SELECT extract('Year' FROM AGE(calcdata1, calcdata2)), extract('Month' FROM AGE(calcdata1, calcdata2)), extract('Day' FROM AGE(calcdata1, calcdata2)) 
    FROM ".$xProj.".poslog WHERE pessoas_id = $UsuIns ");
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        $PrazoM = ($tbl[0]*12)+$tbl[1]; // prazo em meses - $tbl[0] * 12 + $tbl[1]
        $PrazoD = $tbl[2];
    }

    $var = array("coderro"=>$Erro, "dataaviso"=>$DataAviso, "dataassinat"=>$DataA, "antecip"=>$DiasAntec, "prazom"=>$PrazoM, "prazod"=>$PrazoD);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "salvaEditFiltro"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); 
    $NumFiltro = (int) filter_input(INPUT_GET, 'numfiltro'); 
    $Local = filter_input(INPUT_GET, 'localinst'); 
    $CodMarca = (int) filter_input(INPUT_GET, 'codmarca'); 
    $CodTipo = (int) filter_input(INPUT_GET, 'codtipo'); 

    $DataT = addslashes(filter_input(INPUT_GET, 'datatroca')); 
    $DataTroca = implode("/", array_reverse(explode("/", $DataT)));
    $DataV = addslashes(filter_input(INPUT_GET, 'datavenc')); 
    $DataVenc = implode("/", array_reverse(explode("/", $DataV)));
    $DataA = addslashes(filter_input(INPUT_GET, 'dataaviso')); 
    $DataAviso = implode("/", array_reverse(explode("/", $DataA)));
    $Observ = filter_input(INPUT_GET, 'observ'); 
    $Modelo = filter_input(INPUT_GET, 'modelo'); 
    $Prazo = filter_input(INPUT_GET, 'prazotroca'); 
    $DiasAnt = filter_input(INPUT_GET, 'diasanteced'); 
    $Notif = (int) filter_input(INPUT_GET, 'notif'); 
    if($Notif == 0){
        $DataAviso = "3000-12-31";
        $DiasAnt = 0;
    }

    if($Cod > 0){ // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".filtros SET numapar = $NumFiltro, codmarca = $CodMarca, modelo = '$Modelo', tipofiltro = $CodTipo, localinst = '$Local', datatroca = '$DataTroca', datavencim = '$DataVenc',
        dataaviso = '$DataAviso', prazotroca = '$Prazo', diasanteced = '$DiasAnt', observ = '$Observ', notific = $Notif, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW()
        WHERE id = $Cod");
    }else{
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".filtros");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".filtros (id, numapar, codmarca, modelo, tipofiltro, localinst, datatroca, datavencim, dataaviso, prazotroca, diasanteced, observ, notific, usuins, datains) 
        VALUES ($CodigoNovo, $NumFiltro, $CodMarca, '$Modelo', $CodTipo, '$Local', '$DataTroca', '$DataVenc', '$DataAviso', '$Prazo', '$DiasAnt', '$Observ', $Notif, ".$_SESSION["usuarioID"].", NOW() ) ");
    }
    if(!$rs){
        $Erro = 1;
    }
 
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="apagaFiltro"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".filtros SET ativo = 0 WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $tbl = pg_fetch_row($rs);
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="mudarAviso"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".filtros SET pararaviso = $Valor WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $tbl = pg_fetch_row($rs);
    $var = array("coderro"=>$Erro, "valor"=>$Valor);
    $responseText = json_encode($var);
    echo $responseText;
}