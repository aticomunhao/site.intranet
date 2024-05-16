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

if($Acao=="salvaRegBem"){
    $Codigo = (int) filter_input(INPUT_GET, 'codigo');
    $DataReg = addslashes($_REQUEST['dataregistro']);
    $DataAchou = addslashes($_REQUEST['dataachado']);
    $DescBem = addslashes($_REQUEST['descdobem']);
    $LocalAchou = addslashes($_REQUEST['localachado']);
    $NomeAchou = addslashes($_REQUEST['nomeachou']);
    $TelefAchou = addslashes($_REQUEST['telefachou']);
    $JaTem = 0;   //(int) filter_input(INPUT_GET, 'jatem');
    $NumRelat = addslashes($_REQUEST['numrelato']);
    $UsuIns = $_SESSION['usuarioID'];
    $Erro = 0;
    $CodigoNovo = 0;

    $ProcAno = explode("/","$DataReg");
    $d = $ProcAno[0];
    $m = $ProcAno[1];
    $y = $ProcAno[2];

    if($Codigo == 0){ // novo registro
        $CodSetor = $_SESSION['CodSetorUsu'];
        $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".bensachados WHERE to_char(datareceb, 'YYYY') = '$y'");
        $row0 = pg_num_rows($rs0);
        $Num = str_pad(($row0+1), 4, "0", STR_PAD_LEFT);
        $NumRelat = $Num."/".$y;
//        if($JaTem == 0){
//            $NumRelat = $Num."/".$y;
//        }else{
//            $NumRelat = $NumRelAnt."-Compl";
//        }
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".bensachados");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = $Codigo+1; 

        $rs1 = pg_query($Conec, "INSERT INTO ".$xProj.".bensachados (id, datareceb, dataachou, descdobem, localachou, nomeachou, telefachou, codusuins, codsetorusu, datains, ativo, numprocesso) 
        VALUES($CodigoNovo, '$DataReg', '$DataAchou', '$DescBem', '$LocalAchou', '$NomeAchou', '$TelefAchou', $UsuIns, $CodSetor, NOW(), 1, '$NumRelat')");
        if(!$rs1){
            $Erro = 1;
        }

    }else{
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".bensachados SET datareceb = '$DataReg', dataachou = '$DataAchou', descdobem = '$DescBem', localachou = '$LocalAchou', nomeachou = '$NomeAchou', telefachou = '$TelefAchou', usumodif = $UsuIns, datamodif =  NOW() WHERE id = $Codigo ");

    }

//        testemunha1 VARCHAR(200),
//        testemunha2 VARCHAR(200),
//        nomerestituido VARCHAR(200),
//        enderrestituido VARCHAR(200),
//        telefarestituido VARCHAR(50),
//        nomedafrestituiu VARCHAR(200),
//        datarestituiu date, 
//        dataencaminhoucsg date, 
//        nomerecebeucsg VARCHAR(200), 
//        datadestino date, 
//        setordestino VARCHAR(200), 
//        nomerecebeudestino VARCHAR(200), 
//        destinonodestino VARCHAR(50),
//        dataarquivou date,
//        usuarquivou bigint NOT NULL DEFAULT 0,

//        usumodif bigint NOT NULL DEFAULT 0,
//        datamodif timestamp without time zone DEFAULT '3000-12-31 00:00:00',
  

    $var = array("coderro"=>$Erro, "codigonovo"=>$CodigoNovo, "numrelat"=>$NumRelat);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="buscaBem"){
    $Codigo = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    
    $rs1 = pg_query($Conec, "SELECT to_char(datareceb, 'DD/MM/YYYY'), TO_CHAR(dataachou, 'DD/MM/YYYY'), descdobem, localachou, nomeachou, telefachou, numprocesso, codusuins, 
    TO_CHAR(AGE(CURRENT_DATE, datareceb), 'MM') AS intervalo, destinonodestino, setordestino, nomerecebeudestino, nomepropriet, cpfpropriet, telefpropriet FROM ".$xProj.".bensachados WHERE id = $Codigo ");
    //TO_CHAR(AGE(CURRENT_DATE, datareceb), 'MM') AS intervalo  - procura o intervalo de 3 meses entre o recebimento e hoje
    if(!$rs1){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $tbl1 = pg_fetch_row($rs1);
        $CodUsuIns = $tbl1[7];

        $rs2 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodUsuIns"); // usuário que inseriu no sistema
        $tbl2 = pg_fetch_row($rs2);
        $NomeUsuIns = $tbl2[0];
        $var = array("coderro"=>$Erro, "datareg"=>$tbl1[0], "dataachou"=>$tbl1[1], "descdobem"=>nl2br($tbl1[2]), "localachou"=>$tbl1[3], "nomeachou"=>$tbl1[4], "telefachou"=>$tbl1[5], "numprocesso"=>$tbl1[6], "codusuins"=>$CodUsuIns, "nomeusuins"=>$NomeUsuIns, "intervalo"=>$tbl1[8], "destino"=>$tbl1[9], "setordestino"=>$tbl1[10], "nomerecebeu"=>$tbl1[11], "nomepropriet"=>$tbl1[12], "cpfpropriet"=>$tbl1[13], "telefpropriet"=>$tbl1[14]);
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

if($Acao=="destinaBem"){
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
