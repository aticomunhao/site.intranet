<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
}
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
$Acao = "";
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"]; 
}

if($Acao=="salvaRegBem"){
    $Codigo = (int) filter_input(INPUT_GET, 'codigo');
    $DataR = addslashes($_REQUEST['dataregistro']);
    $DataAc = addslashes($_REQUEST['dataachado']);

    $DataReg = implode("-", array_reverse(explode("/", $DataR)));
    $DataAchou = implode("-", array_reverse(explode("/", $DataAc)));
    $DescBem = str_replace("'","\"",$_REQUEST["descdobem"]); // substituir aspas simples por duplas

    $LocalAchou = addslashes($_REQUEST['localachado']);
    $NomeAchou = addslashes($_REQUEST['nomeachou']);
    $TelefAchou = addslashes($_REQUEST['telefachou']);
    $Observ = str_replace("'","\"",$_REQUEST["observ"]); // substituir aspas simples por duplas
    $JaTem = 0;   //(int) filter_input(INPUT_GET, 'jatem');
    $NumRelat = addslashes($_REQUEST['numrelato']);
    $UsuIns = $_SESSION['usuarioID'];
    $Erro = 0;
    $CodigoNovo = 0;

    $ProcAno = explode("-","$DataReg");
    $d = $ProcAno[2];
    $m = $ProcAno[1];
    $y = $ProcAno[0];

    if($Codigo == 0){ // novo registro
        $CodSetor = $_SESSION['CodSetorUsu'];
        //Número do relato - reinicia a cada ano
        $rs0 = pg_query($Conec, "SELECT MAX(LEFT(numprocesso, 4)) FROM ".$xProj.".bensachados WHERE TO_CHAR(datareceb, 'YYYY') = '$y' ");
        $tbl0 = pg_fetch_row($rs0);
        if(!$rs0){
            $Erro = 1;
        }
        $Num = $tbl0[0];
        $Processo = str_pad(($Num+1), 4, "0", STR_PAD_LEFT);
        $NumRelat = $Processo."/".$y;

        //Cod novo para o BD
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".bensachados");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = $Codigo+1; 

        $rs1 = pg_query($Conec, "INSERT INTO ".$xProj.".bensachados (id, datareceb, dataachou, descdobem, localachou, nomeachou, telefachou, codusuins, datains, ativo, numprocesso, observ) 
        VALUES($CodigoNovo, '$DataReg', '$DataAchou', '$DescBem', '$LocalAchou', '$NomeAchou', '$TelefAchou', $UsuIns, NOW(), 1, '$NumRelat', '$Observ')");
        if(!$rs1){
            $Erro = 1;
        }
    }else{
        $rs = pg_query($Conec, "SELECT descdobem FROM ".$xProj.".bensachados WHERE id = $Codigo ");
        $tbl = pg_fetch_row($rs);
        $DescBemAnt = $tbl[0];

        if($DescBem == $DescBemAnt){ // anotar quem modificou a descrião do bem
            $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensachados SET datareceb = '$DataReg', dataachou = '$DataAchou', descdobem = '$DescBem', localachou = '$LocalAchou', nomeachou = '$NomeAchou', telefachou = '$TelefAchou', numprocesso = '$NumRelat', observ = '$Observ', usumodif = $UsuIns, datamodif = NOW() WHERE id = $Codigo ");
        }else{
            $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensachados SET datareceb = '$DataReg', dataachou = '$DataAchou', descdobem = '$DescBem', localachou = '$LocalAchou', nomeachou = '$NomeAchou', telefachou = '$TelefAchou', numprocesso = '$NumRelat', observ = '$Observ' , usumodif = $UsuIns, datamodif = NOW(), usumodifdescbem = $UsuIns, datamodifdescbem = NOW(), descdobemant = '$DescBemAnt' WHERE id = $Codigo ");
        }
    }
    $var = array("coderro"=>$Erro, "codigonovo"=>$CodigoNovo, "numrelat"=>$NumRelat);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="buscaBem"){
    $Codigo = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $NomeUsuRest = "";

    $rs1 = pg_query($Conec, "SELECT to_char(datareceb, 'DD/MM/YYYY'), TO_CHAR(dataachou, 'DD/MM/YYYY'), descdobem, localachou, nomeachou, telefachou, numprocesso, codusuins, TO_CHAR(AGE(CURRENT_DATE, datareceb), 'MM') AS intervalo, destinonodestino, setordestino, 
    nomerecebeudestino, nomepropriet, cpfpropriet, telefpropriet, usurestit, descencdestino, descencprocesso, codencdestino, codencprocesso, usudestino, 
    usuencdestino, observ FROM ".$xProj.".bensachados WHERE id = $Codigo ");
    //TO_CHAR(AGE(CURRENT_DATE, datareceb), 'MM') AS intervalo  - procura o intervalo de 3 meses entre o recebimento e hoje
    if(!$rs1){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $tbl1 = pg_fetch_row($rs1);
        $CodUsuIns = $tbl1[7];
        $CodUsuRestit = $tbl1[15];
        $CodDest = $tbl1[9];

        $CodDestino = $tbl1[18];
        if(!is_null($tbl1[16])){
            $EncDest = $tbl1[16];
        }else{
            $EncDest = "";
        }
        if(!is_null($tbl1[22])){
            $Observ = $tbl1[22];
        }else{
            $Observ = "";
        }

        $UsuDestino = $tbl1[20];
        $rs6 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $UsuDestino"); // usuário que inseriu no sistema
        $tbl6 = pg_fetch_row($rs6);
        if($tbl6 > 0){
            $NomeUsuDestino = $tbl6[0];
        }else{
            $NomeUsuDestino = "";
        }

        $CodProcesso = $tbl1[19];
        if(!is_null($tbl1[17])){
            $EncProcesso = $tbl1[17];
        }else{
            $EncProcesso = "";
        }

        $UsuEncProcesso = $tbl1[21];
        $rs5 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $UsuEncProcesso"); // usuário que inseriu no sistema
        $tbl5 = pg_fetch_row($rs5);
        if($tbl5 > 0){
            $NomeEncProcesso = $tbl5[0];
        }else{
            $NomeEncProcesso = "";
        }

        $rs2 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodUsuIns"); // usuário que inseriu no sistema
        $tbl2 = pg_fetch_row($rs2);
        $NomeUsuIns = $tbl2[0];

        $NomeUsuRest = $_SESSION["NomeCompl"];
        if($CodUsuRestit > 0){ // restituição já feita
            $rs3 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodUsuRestit"); // usuário que inseriu no sistema
            $tbl3 = pg_fetch_row($rs3);
            $NomeUsuRest = $tbl3[0];
        }

        $rs4 = pg_query($Conec, "SELECT descdest FROM ".$xProj.".bensdestinos WHERE numdest = $CodDest"); // usuário que inseriu no sistema
        $tbl4 = pg_fetch_row($rs4);
        $DescDest = $tbl4[0];

        $var = array("coderro"=>$Erro, "datareg"=>$tbl1[0], "dataachou"=>$tbl1[1], "descdobem"=>nl2br($tbl1[2]), "localachou"=>$tbl1[3], "nomeachou"=>$tbl1[4], "telefachou"=>$tbl1[5], "numprocesso"=>$tbl1[6], "codusuins"=>$CodUsuIns, "nomeusuins"=>$NomeUsuIns, "intervalo"=>$tbl1[8], "destino"=>$tbl1[9], "setordestino"=>$tbl1[10], "nomerecebeu"=>$tbl1[11], "nomepropriet"=>$tbl1[12], "cpfpropriet"=>$tbl1[13], "telefpropriet"=>$tbl1[14], "nomeusurestit"=>$NomeUsuRest, "codusurestit"=>$CodUsuRestit, "setorrecebeu"=>$DescDest, "codSetorDestino"=>$CodDestino, "DescDest"=>$EncDest, "codProcesso"=>$CodProcesso, "DescProcesso"=>$EncProcesso, "UsuEncProcesso"=>$UsuEncProcesso, "NomeEncProcesso"=>$NomeEncProcesso, "UsuDestino"=>$UsuDestino, "NomeUsuDestino"=>$NomeUsuDestino, "observ"=>nl2br($Observ));
    }
    $responseText = json_encode($var);
    echo $responseText;
}

// usuentrega - usuário que entrega objeto para guarda 
// usuguarda - usuário que recebe para guarda 

if($Acao=="RcbGuardaBem"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;

    $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensachados SET usuguarda = ".$_SESSION["usuarioID"].", dataguarda = NOW() WHERE id = $Cod");
    if(!$rs1){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="restituiBem"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Nome = filter_input(INPUT_GET, 'nomeproprietario');
    $Cpf = filter_input(INPUT_GET, 'cpfproprietario');
    $Telef = filter_input(INPUT_GET, 'telefproprietario');
    $Erro = 0;
    $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensachados SET nomepropriet = '$Nome', cpfpropriet = '$Cpf', telefpropriet = '$Telef', usurestit = ".$_SESSION["usuarioID"].", datarestit = NOW(), usuarquivou = ".$_SESSION["usuarioID"].", dataarquivou = NOW() WHERE id = $Cod");
//    $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensachados SET nomepropriet = '$Nome', cpfpropriet = '$Cpf', telefpropriet = '$Telef', usurestit = ".$_SESSION["usuarioID"].", datarestit = NOW() WHERE id = $Cod");
    if(!$rs1){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="encamBemCsg"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensachados SET usucsg = ".$_SESSION["usuarioID"].", datarcbcsg = NOW() WHERE id = $Cod");
   if(!$rs1){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="destinaBem__"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Setor = filter_input(INPUT_GET, 'setordestino');
    $NomeFunc = filter_input(INPUT_GET, 'nomefuncionario');
    $Destino = filter_input(INPUT_GET, 'selecdestino');
    $Erro = 0;
    $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensachados SET setordestino = '$Setor', nomerecebeudestino = '$NomeFunc', destinonodestino = '$Destino', datadestino = NOW(), dataarquivou = NOW(), usudestino = ".$_SESSION["usuarioID"].", usuarquivou = ".$_SESSION["usuarioID"]." WHERE id = $Cod");
    if(!$rs1){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="encdestinaBem"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Setor = (int) filter_input(INPUT_GET, 'selecdestino');
    $UsuEdita = (int) filter_input(INPUT_GET, 'codusudest');
//    $Processo = (int) filter_input(INPUT_GET, 'selecprocesso');
    $Erro = 0;

    $rs = pg_query($Conec, "SELECT descdest FROM ".$xProj.".bensdestinos WHERE numdest = $Setor");
    $tbl = pg_fetch_row($rs);
    $SetorDest = $tbl[0];

//    $rs0 = pg_query($Conec, "SELECT processo FROM ".$xProj.".bensprocessos WHERE id = $Processo");
//    $tbl0 = pg_fetch_row($rs0);
//    $DescProcesso = $tbl0[0];

    if($UsuEdita == 0){
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensachados SET codencdestino = $Setor, descencdestino = '$SetorDest', dataencdestino = NOW(), usudestino = ".$_SESSION["usuarioID"]." WHERE id = $Cod");
    }else{ // superusuário modificando
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensachados SET codencdestinoant = codencdestino, usumodifdestino = ".$_SESSION["usuarioID"].", datamodifdestino = NOW(), codencdestino = $Setor, descencdestino = '$SetorDest', dataencdestino = NOW(), usudestino = $UsuEdita WHERE id = $Cod");    
    }
    if(!$rs1){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="apagaBem"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensachados SET ativo = 0, dataapagou = NOW(), usuapagou = ".$_SESSION["usuarioID"]." WHERE id = $Cod");
    if(!$rs1){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "buscausuario"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); //id de poslog
    //bens, fiscbens, soinsbens

    $rs1 = pg_query($Conec, "SELECT bens, fiscbens, soinsbens, encbens, cpf FROM ".$xProj.".poslog WHERE pessoas_id = $Cod");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($rs1);
        $var = array("coderro"=>$Erro, "bens"=>$tbl1[0], "fiscbens"=>$tbl1[1], "soinsbens"=>$tbl1[2], "encbens"=>$tbl1[3], "cpf"=>$tbl1[4]);
    }else{
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }        
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "configMarcafBem"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id de poslog
    $Campo = filter_input(INPUT_GET, 'campo');
    $Valor = (int) filter_input(INPUT_GET, 'valor');

    if($Campo == "bens" && $Valor == 0){
        $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE bens = 1");
        $row = pg_num_rows($rs);
        if($row == 1){
            $Erro = 2;
            $var = array("coderro"=>$Erro);
            $responseText = json_encode($var);
            echo $responseText;
            return false;
        }else{
            //retirar a marca de encaminha bens se bens estiver zero
            pg_query($Conec, "UPDATE ".$xProj.".poslog SET encbens = 0 WHERE pessoas_id = $Cod");
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
if($Acao == "buscacpf"){
    $Erro = 0;
    $Cpf = filter_input(INPUT_GET, 'cpf'); 
    $Cpf1 = addslashes($Cpf);
    $Cpf2 = str_replace(".", "", $Cpf1);
    $GuardaCpf = str_replace("-", "", $Cpf2);
    //bens, fiscbens, soinsbens

    $rs1 = pg_query($Conec, "SELECT bens, fiscbens, soinsbens, cpf, pessoas_id FROM ".$xProj.".poslog WHERE cpf = '$GuardaCpf'");
    if(!$rs1){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }
    $row1 = pg_num_rows($rs1);
    if($row1 == 0){
        $Erro = 2;
        $var = array("coderro"=>$Erro);
    }else{
        $tbl1 = pg_fetch_row($rs1);
        $var = array("coderro"=>$Erro, "bens"=>$tbl1[0], "fiscbens"=>$tbl1[1], "soinsbens"=>$tbl1[2], "cpf"=>$tbl1[3], "PosCod"=>$tbl1[4], "row1"=>$row1);
    } 
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "buscanum"){
    $Erro = 0;
    $Num = 0;
    $DataR = addslashes($_REQUEST['dataregistro']);
    $DataReg = implode("/", array_reverse(explode("/", $DataR)));

    $ProcAno = explode("/","$DataReg");
    $d = $ProcAno[2];
    $m = $ProcAno[1];
    $y = $ProcAno[0];

    $rs = pg_query($Conec, "SELECT MAX(LEFT(numprocesso, 4)) FROM ".$xProj.".bensachados WHERE TO_CHAR(datareceb, 'YYYY') = '$y' ");
    $tbl = pg_fetch_row($rs);
    $Processo = $tbl[0];
    $Num = $Processo+1; 
    if(!$rs){
        $Erro = 1;
    }
    $Num = str_pad($Num, 4, "0", STR_PAD_LEFT);
    $NumRelat = $Num."/".$y;

    $var = array("coderro"=>$Erro, "numprocesso"=>$NumRelat);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "checaNumero"){
    $Erro = 0;
    $Num = 0;
    $NumReg = addslashes($_REQUEST['numero']);
    $Cod = (int) filter_input(INPUT_GET, 'codigo');

    $row = 0;
    $Data = "";
    $rs = pg_query($Conec, "SELECT id, TO_CHAR(datareceb, 'DD/MM/YYYY') FROM ".$xProj.".bensachados WHERE numprocesso = '$NumReg' And id != $Cod");
    if(!$rs){
        $Erro = 1;
    }else{
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $Data = $tbl[1];
        }
    }

    $var = array("coderro"=>$Erro, "achou"=>$row, "data"=>$Data);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="recebeBemDest"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Processo = (int) filter_input(INPUT_GET, 'selecprocesso');
    $UsuReceb = (int) filter_input(INPUT_GET, 'codusureceb');
    $Erro = 0;

    $rs0 = pg_query($Conec, "SELECT processo FROM ".$xProj.".bensprocessos WHERE id = $Processo");
    $tbl0 = pg_fetch_row($rs0);
    $DescProcesso = $tbl0[0];

    if($UsuReceb == 0){
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensachados SET usuencdestino = ".$_SESSION["usuarioID"].", datadestino = NOW(), codencprocesso = $Processo, descencprocesso = '$DescProcesso' WHERE id = $Cod");
    }else{ // superusuário corrindo finalidade do objeto destinado
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensachados SET codencprocessoant = codencprocesso, usumodifprocesso = ".$_SESSION["usuarioID"].", datamodifprocesso = NOW(), usuencdestino = $UsuReceb, datadestino = NOW(), codencprocesso = $Processo, descencprocesso = '$DescProcesso' WHERE id = $Cod");
    }
    if(!$rs1){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="encerraProcesso"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;

    $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensachados SET usuarquivou = ".$_SESSION["usuarioID"].", dataarquivou = NOW() WHERE id = $Cod");
    if(!$rs1){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="buscaReivind"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs1 = pg_query($Conec, "SELECT to_char(datareiv, 'DD/MM/YYYY'), TO_CHAR(dataperdeu, 'DD/MM/YYYY'), nome, email, telef, localperdeu, descdobemperdeu, observ, processoreiv, encontrado, entregue, 
    processobens 
    FROM ".$xProj.".bensreivind WHERE id = $Cod ");
    if(!$rs1){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $tbl1 = pg_fetch_row($rs1);
        if(is_null($tbl1[5])){
            $Local = "";
        }else{
            $Local = $tbl1[5];
        }
        if(is_null($tbl1[6])){
            $Desc = "";
        }else{
            $Desc = $tbl1[6];
        }
        if(is_null($tbl1[7])){
            $Obs = "";
        }else{
            $Obs = $tbl1[7];
        }
        $var = array("coderro"=>$Erro, "datareiv"=>$tbl1[0], "dataperdeu"=>$tbl1[1], "nome"=>$tbl1[2], "email"=>$tbl1[3], "telef"=>$tbl1[4], "localperdeu"=>nl2br($Local), "descdobem"=>nl2br($Desc), "observperdeu"=>nl2br($Obs), "processo"=>$tbl1[8], "encontrado"=>$tbl1[9], "entregue"=>$tbl1[10], "processobens"=>$tbl1[11]);
    }
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="buscaProcReivind"){
    $Erro = 0;
    $Ano = date('Y');
    //Número do relato - reinicia a cada ano
    $rs0 = pg_query($Conec, "SELECT MAX(LEFT(processoreiv, 3)) FROM ".$xProj.".bensreivind WHERE ativo = 1 And TO_CHAR(datareiv, 'YYYY') = '$Ano' ");
    $tbl0 = pg_fetch_row($rs0);
    if(!$rs0){
        $Erro = 1;
    }
    $Num = $tbl0[0];
    $Processo = str_pad(($Num+1), 3, "0", STR_PAD_LEFT);
    $NumRelat = $Processo."/".$Ano;

    $var = array("coderro"=>$Erro, "numprocesso"=>$NumRelat);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="salvaReivind"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Registro = addslashes(filter_input(INPUT_GET, 'numregistro'));
    $Processo = addslashes(filter_input(INPUT_GET, 'numprocesso'));

    $DataR = addslashes($_REQUEST['dataReivind']);
    $DataP = addslashes($_REQUEST['dataPerdido']);

    $DataReg = implode("-", array_reverse(explode("/", $DataR)));
    $DataPerdeu = implode("-", array_reverse(explode("/", $DataP)));
    $DescBem = str_replace("'","\"",$_REQUEST["descdobemPerdeu"]); // substituir aspas simples por duplas
    $Nome = addslashes($_REQUEST['nomereclamante']);
    $Email = addslashes($_REQUEST['emailreclamante']);
    $Telef = addslashes($_REQUEST['telefreclamante']);
    $Local = str_replace("'","\"",$_REQUEST["localperdeu"]);
    $Obs = str_replace("'","\"",$_REQUEST["obsperdeu"]);

    $Encontr = (int) filter_input(INPUT_GET, 'encontrado');
    $Entreg = (int) filter_input(INPUT_GET, 'entregue');

    if($Cod > 0){ // salvar
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensreivind SET processoreiv = '$Registro', datareiv = '$DataReg', dataperdeu = '$DataPerdeu', nome = '$Nome', email = '$Email', telef = '$Telef', localperdeu = '$Local', descdobemperdeu = '$DescBem', observ = '$Obs', encontrado = $Encontr, entregue = $Entreg, processobens = '$Processo', ativo = 1, usuins = ".$_SESSION["usuarioID"].", datains = NOW() WHERE id = $Cod");
    }else{ // inserir novo
        //Cod novo para o BD
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".bensreivind");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = $Codigo+1; 
        $rs1 = pg_query($Conec, "INSERT INTO ".$xProj.".bensreivind (id, processoreiv, datareiv, dataperdeu, nome, email, telef, localperdeu, descdobemperdeu, observ, encontrado, entregue, processobens, ativo, usuins, datains) 
        VALUES($CodigoNovo, '$Registro', '$DataReg', '$DataPerdeu', '$Nome', '$Email', '$Telef', '$Local', '$DescBem', '$Obs', $Encontr, $Entreg, '$Processo', 1, ".$_SESSION["usuarioID"].", NOW() )");
        $Cod = $CodigoNovo; // mandar de volta para o botão salvar sem fechar
    }
    if(!$rs1){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "codigo"=>$Cod);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="apagaReivind"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo');

    if($Cod > 0){ // salvar
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensreivind SET ativo = 0, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod");
    }
    if(!$rs1){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}