<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
date_default_timezone_set('America/Sao_Paulo');
$Acao = "";
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"]; 
}

if($Acao=="buscaReg"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $DiasDecorridos = 0;
    $rs = pg_query($Conec, "SELECT to_char(".$xProj.".livroreg.dataocor, 'DD/MM/YYYY'), ".$xProj.".poslog.nomecompl, usuant, turno, numrelato, enviado, relato, codusu, ocor, (CURRENT_DATE - ".$xProj.".livroreg.dataocor), relsubstit, usuprox 
    FROM ".$xProj.".livroreg INNER JOIN ".$xProj.".poslog ON ".$xProj.".livroreg.codusu = ".$xProj.".poslog.pessoas_id 
    WHERE ".$xProj.".livroreg.id = $Cod");
    if(!$rs){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $Data = $tbl[0];
            $CodUsu = $tbl[7];
            $NomeIns = "";
            $DiasDecorridos = $tbl[9];

            $rs0 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodUsu");
            $row0 = pg_num_rows($rs0);
            if($row0 > 0){
                $tbl0 = pg_fetch_row($rs0);
                $NomeIns = $tbl0[0];
            }else{
                $NomeIns = "";
            }

            $CodUsuAnt = $tbl[2];
            $CodUsuProx = $tbl[11];
            $NomeAnt = "";
            $NomeProx = "";
            $rs1 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodUsuAnt");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $NomeAnt = $tbl1[0];
            }else{
                $NomeAnt = "";
            }
            if($CodUsuProx > 0){
                $rs4 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodUsuProx");
                $row4 = pg_num_rows($rs4);
                if($row4 > 0){
                    $tbl4 = pg_fetch_row($rs4);
                    $NomeProx = $tbl4[0];
                }else{
                    $NomeProx = "";
                }
            }else{
                $NomeProx = "";
            }


            $rs2 = pg_query($Conec, "SELECT descturno FROM ".$xProj.".livroturnos WHERE codturno = $tbl[3]");
            $row2 = pg_num_rows($rs2);
            if($row2 > 0){
                $tbl2 = pg_fetch_row($rs2);
                $DescTurno = $tbl2[0];
            }else{
                $DescTurno = "";
            }

            //Tem que ser separado da query $rs
            $rs3 = pg_query($Conec, "SELECT lro, fisclro FROM ".$xProj.".poslog WHERE pessoas_id = ". $_SESSION['usuarioID']."");
            $row3 = pg_num_rows($rs3);
            if($row3 > 0){
                $tbl3 = pg_fetch_row($rs3);
                $Lro = $tbl3[0];
                if($_SESSION["AdmUsu"] > 6){
                    $Lro = 1; // superusuário tem acesso ao LRO
                }
                $FiscLro = $tbl3[1];
                if($_SESSION["AdmUsu"] > 6){
                    $FiscLro = 1; // superusuário tem acesso ao LRO
                }
            }else{
                $Lro = 0;
                $FiscLro = 0;
            }

            $var = array("coderro"=>$Erro, "data"=>$Data, "codusuins"=>$tbl[7], "nomeusuins"=>$NomeIns, "usuant"=>$tbl[2], "nomeusuant"=>$NomeAnt, "usuprox"=>$CodUsuProx, "nomeusuprox"=>$NomeProx, "turno"=>$tbl[3], "descturno"=>$DescTurno, "numrelato"=>$tbl[4], "enviado"=>$tbl[5], "relato"=>$tbl[6], "ocor"=>$tbl[8], "substit"=>$tbl[10], "acessoLro"=>$Lro, "fiscalizaLro"=>$FiscLro, "diasdecorridos"=>$DiasDecorridos);
        }else{
            $var = array("coderro"=>$Erro);
        }
    }
     $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvaReg"){
    $Codigo = (int) filter_input(INPUT_GET, 'codigo');
    $Data = addslashes($_REQUEST['datareg']);
    $RevData = implode("-", array_reverse(explode("/", $Data)));
    $Turno = (int) filter_input(INPUT_GET, 'turno');
    $UsuAnt = (int) filter_input(INPUT_GET, 'usuant');
    $UsuProx = (int) filter_input(INPUT_GET, 'usuprox');
    $Relato = trim(addslashes($_REQUEST['relato']));
    $Subst = trim(addslashes($_REQUEST['substit']));

    $Envia = (int) filter_input(INPUT_GET, 'envia');
    $JaTem = (int) filter_input(INPUT_GET, 'jatem');
    $QuantJaTem = (int) filter_input(INPUT_GET, 'quantjatem');
    
    $NumRelAnt = addslashes($_REQUEST['numrelato']);
    $NumRelat = $NumRelAnt; // se estiver em branco será redefinido abaixo
    $Ocor = (int) filter_input(INPUT_GET, 'ocor');

    $Erro = 0;
    $CodigoNovo = 0;

    $ProcAno = explode("/","$Data");
    $d = $ProcAno[0];
    $m = $ProcAno[1];
    $y = $ProcAno[2];

    $rsTurno = pg_query($Conec, "SELECT descturno FROM ".$xProj.".livroturnos WHERE codturno = $Turno");
    $rowTurno = pg_num_rows($rsTurno);
    if($rowTurno > 0){
        $tblTurno = pg_fetch_row($rsTurno);
        $DescTurno = $tblTurno[0];
    }else{
        $DescTurno = "";
    }

    if($Codigo == 0){ // novo registro
        $UsuIns = $_SESSION['usuarioID'];
        $CodSetor = $_SESSION['CodSetorUsu'];
        $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".livroreg WHERE to_char(dataocor, 'YYYY') = '$y'");
        $row0 = pg_num_rows($rs0);
        $Num = str_pad(($row0+1), 4, "0", STR_PAD_LEFT);

        if($JaTem == 0){
            $NumRelat = $Num."/".$y;
        }else{
            $NumRelat = $NumRelAnt."-Compl";
            if($QuantJaTem > 1){
                $NumRelat = $NumRelAnt."-Compl".$QuantJaTem;
            }
        }

        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".livroreg");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = $Codigo+1; 

        $Sql = pg_query($Conec, "INSERT INTO ".$xProj.".livroreg (id, codusu, usuant, usuprox, turno, descturno, dataocor, datains, ativo, numrelato, relato, enviado, ocor, relsubstit) 
        VALUES($CodigoNovo, $UsuIns, $UsuAnt, $UsuProx, $Turno, '$DescTurno', '$RevData', NOW(), 1, '$NumRelat', '$Relato', $Envia, $Ocor, '$Subst')");
        if(!$Sql){
            $Erro = 1;
        }else{
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".livroreg");
            $tblCod = pg_fetch_row($rsCod);
            $CodigoNovo = $tblCod[0];
        }
    }else{
        if($Envia == 1){
            pg_query($Conec, "UPDATE ".$xProj.".livroreg SET usuant = $UsuAnt, usuprox = $UsuProx, turno = $Turno, descturno = '$DescTurno', relato = '$Relato', relsubstit = '$Subst', enviado = $Envia, dataenviado = NOW(), usumodif = ".$_SESSION['usuarioID'].", datamodif = NOW(), ocor = $Ocor WHERE id = $Codigo");
        }else{
            pg_query($Conec, "UPDATE ".$xProj.".livroreg SET usuant = $UsuAnt, usuprox = $UsuProx, turno = $Turno, descturno = '$DescTurno', relato = '$Relato', relsubstit = '$Subst', enviado = $Envia, usumodif = ".$_SESSION['usuarioID'].", datamodif = NOW(), ocor = $Ocor WHERE id = $Codigo");
        }
    }

    $var = array("coderro"=>$Erro, "codigonovo"=>$CodigoNovo, "num"=>$NumRelat, "ocor"=>$Ocor);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvaRegCompl"){ // Salva complemento
    $Codigo = (int) filter_input(INPUT_GET, 'codigo');
    $Data = addslashes($_REQUEST['datareg']);
    $RevData = implode("-", array_reverse(explode("/", $Data)));
    $Turno = (int) filter_input(INPUT_GET, 'turno');
    $UsuAnt = (int) filter_input(INPUT_GET, 'usuant');
    $Relato = trim(addslashes($_REQUEST['relato']));
    $Subst = trim(addslashes($_REQUEST['substit']));
    $Envia = (int) filter_input(INPUT_GET, 'envia');
    $JaTem = 1;
    $Ocor = 1; // tem ocorrência
    $NumRelAnt = "";

    // procura o num relato original
    $rs = pg_query($Conec, "SELECT MIN(id) FROM ".$xProj.".livroreg WHERE dataocor = '$RevData' And turno = $Turno");
    $tbl = pg_fetch_row($rs);
    $CodigoOrig = $tbl[0];

    $rs1 = pg_query($Conec, "SELECT numrelato FROM ".$xProj.".livroreg WHERE id = $CodigoOrig");
    $tbl1 = pg_fetch_row($rs1);
    $NumRelAnt = $tbl1[0];

    $Erro = 0;
    $CodigoNovo = 0;

    $rs1 = pg_query($Conec, "SELECT id, numrelato FROM ".$xProj.".livroreg WHERE to_char(dataocor, 'DD/MM/YYYY') = '$Data' And turno = $Turno");
    $QuantJaTem = pg_num_rows($rs1);

    $NumRelat = $NumRelAnt."-Compl";
    if($QuantJaTem > 1){
        $NumRelat = $NumRelAnt."-Compl".$QuantJaTem;
    }

    $rsTurno = pg_query($Conec, "SELECT descturno FROM ".$xProj.".livroturnos WHERE codturno = $Turno");
    $rowTurno = pg_num_rows($rsTurno);
    if($rowTurno > 0){
        $tblTurno = pg_fetch_row($rsTurno);
        $DescTurno = $tblTurno[0];
    }else{
        $DescTurno = "";
    }

    $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".livroreg");
    $tblCod = pg_fetch_row($rsCod);
    $Codigo = $tblCod[0];
    $CodigoNovo = $Codigo+1; 
    $UsuIns = $_SESSION['usuarioID'];
    $Sql = pg_query($Conec, "INSERT INTO ".$xProj.".livroreg (id, codusu, usuant, turno, descturno, dataocor, datains, ativo, numrelato, relato, enviado, ocor, relsubstit) 
        VALUES($CodigoNovo, $UsuIns, $UsuAnt, $Turno, '$DescTurno', '$RevData', NOW(), 1, '$NumRelat', '$Relato', $Envia, $Ocor, '$Subst')");
    if(!$Sql){
        $Erro = 1;
    }else{
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".livroreg");
        $tblCod = pg_fetch_row($rsCod);
        $CodigoNovo = $tblCod[0];
    }
    
    $var = array("coderro"=>$Erro, "codigonovo"=>$CodigoNovo, "num"=>$NumRelat, "codoriginal"=>$CodigoOrig );
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvaRegEnv"){
    $Codigo = (int) filter_input(INPUT_GET, 'codigo');
    $Envia = (int) filter_input(INPUT_GET, 'envia');
    $Erro = 0;
    if($Envia == 1){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".livroreg SET enviado = $Envia, dataenviado = NOW(), usumodif = ".$_SESSION['usuarioID'].", datamodif = NOW() WHERE id = $Codigo");
    }else{
        $rs = pg_query($Conec, "UPDATE ".$xProj.".livroreg SET enviado = $Envia, usumodif = ".$_SESSION['usuarioID'].", datamodif = NOW() WHERE id = $Codigo");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="AcessoLro"){
    $Erro = 0;
    $rs1 = pg_query($Conec, "SELECT lro, fisclro FROM ".$xProj.".poslog WHERE pessoas_id = ".$_SESSION["usuarioID"]. " ");
    if(!$rs1){
        $Erro = 1;
    }
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
       $tbl1 = pg_fetch_row($rs1);
       $Lro = $tbl1[0];
       $FiscLro = $tbl1[1];
    }else{
        $Lro = 0;
        $FiscLro = 0; 
    }
    $var = array("coderro"=>$Erro, "lro"=>$Lro, "fisclro"=>$FiscLro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="buscaAcessoLro"){
    $Data = addslashes($_REQUEST['geradata']); 
    $Turno = (int) filter_input(INPUT_GET, 'geraturno');
    $Erro = 0;

    $rs2 = pg_query($Conec, "SELECT descturno FROM ".$xProj.".livroturnos WHERE codturno = $Turno");
    $row2 = pg_num_rows($rs2);
    if($row2 > 0){
        $tbl2 = pg_fetch_row($rs2);
        $DescTurno = $tbl2[0];
    }else{
        $DescTurno = "";
    }

    $rs1 = pg_query($Conec, "SELECT id, numrelato FROM ".$xProj.".livroreg WHERE to_char(dataocor, 'DD/MM/YYYY') = '$Data' And turno = $Turno");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
       $tbl1 = pg_fetch_row($rs1);
       $NumRelat = $tbl1[1];
    }else{
        $NumRelat = "";
    }

    $rs3 = pg_query($Conec, "SELECT lro, fisclro FROM ".$xProj.".poslog WHERE pessoas_id = ".$_SESSION["usuarioID"]." ");
    $row3 = pg_num_rows($rs3);
    if($row3 > 0){
       $tbl3 = pg_fetch_row($rs3);
       $Lro = $tbl3[0];
       $FiscLro = $tbl3[1];
    }else{
        $Erro = 1;
        $Lro = 0;
        $FiscLro = 0; 
    }

    $DataLegivel = 0;
    $Ini = strtotime(date('Y/m/d')); // número
    $Ontem = strtotime("-1 day", $Ini);
    $DataLegivel = date("Y/m/d", $Ontem);
    $DataOntem = implode("/", array_reverse(explode("/", $DataLegivel)));

    $InsTurno1 = 0;
    $InsTurno2 = 0;
    $InsTurno3 = 0;

    $Hora = (int) date("H");
    if($Hora >= 0 && $Hora <= 7){ // pernoite depois da meia-noite até 8 horas - prejudica o turno da manhã que começa às 7
        $rs4 = pg_query($Conec, "SELECT id FROM ".$xProj.".livroreg WHERE dataocor = '$DataLegivel' And turno = 1");
        $InsTurno1 = pg_num_rows($rs4);
        $rs5 = pg_query($Conec, "SELECT id FROM ".$xProj.".livroreg WHERE dataocor = '$DataLegivel' And turno = 2");
        $InsTurno2 = pg_num_rows($rs5);
        $rs6 = pg_query($Conec, "SELECT id FROM ".$xProj.".livroreg WHERE dataocor = '$DataLegivel' And turno = 3");
        $InsTurno3 = pg_num_rows($rs6);

        $rs1 = pg_query($Conec, "SELECT id, numrelato FROM ".$xProj.".livroreg WHERE to_char(dataocor, 'DD/MM/YYYY') = '$DataOntem' And turno = $Turno");
        $row1 = pg_num_rows($rs1);

    }

    $var = array("coderro"=>$Erro, "acessoLro"=>$Lro, "fisclro"=>$FiscLro, "jatem"=>$row1, "descturno"=>$DescTurno, "numrelato"=>$NumRelat, "hora"=>$Hora, "dataontem"=>$DataOntem, "turno1"=>$InsTurno1, "turno2"=>$InsTurno2, "turno3"=>$InsTurno3);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="buscaTurno"){
    $Data = addslashes($_REQUEST['datareg']); 
    $Turno = (int) filter_input(INPUT_GET, 'turnoreg');
    $Erro = 0;
    $CodUsu = 0;
    $Enviado = 0;

    $rs1 = pg_query($Conec, "SELECT id, numrelato, codusu, enviado FROM ".$xProj.".livroreg WHERE to_char(dataocor, 'DD/MM/YYYY') = '$Data' And turno = $Turno");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
       $tbl1 = pg_fetch_row($rs1);
       $NumRelat = $tbl1[1];
       $CodUsu = $tbl1[2];
       $Enviado = $tbl1[3];
    }else{
       $NumRelat = "";
    }
    if($Enviado == 1){


    }

    if($CodUsu > 0){
        if($CodUsu != $_SESSION["usuarioID"]){
            $rs2 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodUsu");
            $row2 = pg_num_rows($rs2);
            $tbl2 = pg_fetch_row($rs2);
            $NomeFunc = $tbl2[0];
        }else{
            $NomeFunc = $_SESSION["NomeCompl"];
        }
    }else{
        $NomeFunc = "";
    }

    $var = array("coderro"=>$Erro, "jatem"=>$row1, "numrelato"=>$NumRelat, "codusu"=>$CodUsu, "nomeusu"=>$NomeFunc, "enviado"=>$Enviado);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="incluirnome"){
    $Nome = ucfirst(addslashes(filter_input(INPUT_GET, 'nome')));
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT nomecolet FROM ".$xProj.".coletnomes WHERE nomecolet = '$Nome'");
    $row =  pg_num_rows($rs);
    if($row == 0){
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".coletnomes");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = $Codigo+1; 
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".coletnomes (id, setor, nomecolet, ativo, usuins, datains) 
        VALUES ($CodigoNovo, 1, '$Nome', 1, ". $_SESSION['usuarioID'].", NOW() )");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "nome"=>$Nome);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="buscaitem"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // id de livrocheck
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT itemnum, itemverif FROM ".$xProj.".livrocheck WHERE id = $Cod");
    $row =  pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        if(strLen($tbl[0]) < 2){
            $Item = "0".$tbl[0];
        }else{
            $Item = $tbl[0];
        }
        $Desc = $tbl[1];
    }else{
        $Item = "";
        $Desc = "";
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "item"=>$Item, "descr"=>$Desc);
    $responseText = json_encode($var);
    echo $responseText;
}
