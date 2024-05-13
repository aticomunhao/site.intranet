<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    header("Location: /cesb/index.php");
}
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
$Acao = "";
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"]; 
}

if($Acao=="buscaReg"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT to_char(".$xProj.".livroreg.dataocor, 'DD/MM/YYYY'), ".$xProj.".poslog.nomecompl, usuant, turno, numrelato, enviado, relato, codusu 
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
            $rs0 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodUsu");
            $row0 = pg_num_rows($rs0);
            if($row0 > 0){
                $tbl0 = pg_fetch_row($rs0);
                $NomeIns = $tbl0[0];
            }else{
                $NomeIns = "";
            }

            $CodUsuAnt = $tbl[2];
            $NomeAnt = "";
            $rs1 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodUsuAnt");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $NomeAnt = $tbl1[0];
            }else{
                $NomeAnt = "";
            }
            $rs2 = pg_query($Conec, "SELECT descturno FROM ".$xProj.".livroturnos WHERE codturno = $tbl[3]");
            $row2 = pg_num_rows($rs2);
            if($row2 > 0){
                $tbl2 = pg_fetch_row($rs2);
                $DescTurno = $tbl2[0];
            }else{
                $DescTurno = "";
            }

            $Lro = $_SESSION["acessoLRO"];
            if($_SESSION["AdmUsu"] > 6){
                $Lro = 1; // superusuário tem acesso ao LRO
            }
            $var = array("coderro"=>$Erro, "data"=>$Data, "codusuins"=>$tbl[7], "nomeusuins"=>$NomeIns, "usuant"=>$tbl[2], "nomeusuant"=>$NomeAnt, "turno"=>$tbl[3], "descturno"=>$DescTurno, "numrelato"=>$tbl[4], "enviado"=>$tbl[5], "relato"=>$tbl[6], "acessoLro"=>$Lro);
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
    $Relato = addslashes($_REQUEST['relato']);
    $Envia = (int) filter_input(INPUT_GET, 'envia');
    $JaTem = (int) filter_input(INPUT_GET, 'jatem');
    $NumRelAnt = addslashes($_REQUEST['numrelato']);
    $NumRelat = $NumRelAnt; // se estiver em branco será redefinido abaixo

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
        }

        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".livroreg");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = $Codigo+1; 

        $Sql = pg_query($Conec, "INSERT INTO ".$xProj.".livroreg (id, codusu, usuant, turno, descturno, dataocor, datains, ativo, numrelato, relato, enviado) 
        VALUES($CodigoNovo, $UsuIns, $UsuAnt, $Turno, '$DescTurno', '$RevData', NOW(), 1, '$NumRelat', '$Relato', $Envia)");
//        VALUES($CodigoNovo, $UsuIns, $UsuAnt, $Turno, '$DescTurno', '$RevData', NOW(), 1, CONCAT('$Num', '/', '$y'), '$Relato', $Envia)");
        if(!$Sql){
            $Erro = 1;
        }else{
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".livroreg");
            $tblCod = pg_fetch_row($rsCod);
            $CodigoNovo = $tblCod[0];
        }
    }else{
        pg_query($Conec, "UPDATE ".$xProj.".livroreg SET usuant = $UsuAnt, turno = $Turno, descturno = '$DescTurno', relato = '$Relato', enviado = $Envia, usumodif = ".$_SESSION['usuarioID'].", datamodif = NOW() WHERE id = $Codigo");
    }

    $var = array("coderro"=>$Erro, "codigonovo"=>$CodigoNovo, "num"=>$NumRelat);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="salvaRegEnv"){
    $Codigo = (int) filter_input(INPUT_GET, 'codigo');
    $Envia = (int) filter_input(INPUT_GET, 'envia');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".livroreg SET enviado = $Envia, usumodif = ".$_SESSION['usuarioID'].", datamodif = NOW() WHERE id = $Codigo");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}


if($Acao=="buscaAcessoLro"){
    $Data = addslashes($_REQUEST['geradata']); 
    $Turno = (int) filter_input(INPUT_GET, 'geraturno');
    $Erro = 0;
    $Lro = $_SESSION["acessoLRO"];

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
       $NumRelat = $tbl1 [1];
    }else{
        $NumRelat = "";
    }
      $var = array("coderro"=>$Erro, "acessoLro"=>$Lro, "jatem"=>$row1, "descturno"=>$DescTurno, "numrelato"=>$NumRelat);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="buscaTurno"){
    $Data = addslashes($_REQUEST['datareg']); 
    $Turno = (int) filter_input(INPUT_GET, 'turnoreg');
    $Erro = 0;

    $rs1 = pg_query($Conec, "SELECT id, numrelato FROM ".$xProj.".livroreg WHERE to_char(dataocor, 'DD/MM/YYYY') = '$Data' And turno = $Turno");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
       $tbl1 = pg_fetch_row($rs1);
       $NumRelat = $tbl1 [1];
    }else{
        $NumRelat = "";
    }

    $var = array("coderro"=>$Erro, "jatem"=>$row1, "numrelato"=>$NumRelat);
    $responseText = json_encode($var);
    echo $responseText;
}