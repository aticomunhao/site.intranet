<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
date_default_timezone_set('America/Sao_Paulo'); 

if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    $UsuIns = $_SESSION['usuarioID'];

    if($Acao == "buscaChave"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); 
        $rs = pg_query($Conec, "SELECT chavenum, chavenumcompl, chavelocal, chavesala, chaveobs FROM ".$xProj.".chaves WHERE id = $Cod");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $ChaveNum = str_pad($tbl[0], 3, 0, STR_PAD_LEFT);
        }
        if(!$rs){
            $Erro = 1;
        }
        
        $var = array("coderro"=>$Erro, "chavenum"=>$ChaveNum, "chavenumcompl"=>$tbl[1], "chavelocal"=>$tbl[2], "chavesala"=>$tbl[3], "chaveobs"=>$tbl[4]);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "salvaChave"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); 
        $Num = (int) filter_input(INPUT_GET, 'numchave');
        $Compl =  filter_input(INPUT_GET, 'complemchave'); 
        $Sala =  filter_input(INPUT_GET, 'salachave'); 
        $Local =  filter_input(INPUT_GET, 'localchave'); 
        $Obs =  filter_input(INPUT_GET, 'obschave'); 

        if($Cod > 0){
            $rs = pg_query($Conec, "UPDATE ".$xProj.".chaves SET chavenum = $Num, chavenumcompl = '$Compl', chavelocal = '$Local', chavesala = '$Sala', chaveobs = '$Obs', usuedit = ". $_SESSION['usuarioID'].", dataedit = NOW()  WHERE id = $Cod");
        }else{
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".chaves");
            $tblCod = pg_fetch_row($rsCod);
            $CodigoNovo = $tblCod[0]+1;

            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".chaves (id, chavenum, chavenumcompl, chavelocal, chavesala, chaveobs, presente, usuins, datains) 
            VALUES ($CodigoNovo, $Num, '$Compl', '$Local', '$Sala', '$Obs', 1, ". $_SESSION['usuarioID'].", NOW()) ");
        }
        
        if(!$rs){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "devolveChave"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo');  // id de chaves_tll
        $CodUsu = (int) filter_input(INPUT_GET, 'codusudevolve');

        $Cpf = filter_input(INPUT_GET, 'cpfdevolve'); 
        $Cpf1 = addslashes($Cpf);
        $Cpf2 = str_replace(".", "", $Cpf1);
        $GuardaCpf = str_replace("-", "", $Cpf2);

        $rs = pg_query($Conec, "UPDATE ".$xProj.".chaves_ctl SET datavolta = NOW(), funcrecebe = ". $_SESSION['usuarioID'].", cpfdevolve = '$GuardaCpf', usudevolve = $CodUsu, usuedit = ". $_SESSION['usuarioID'].", dataedit = NOW() WHERE id = $Cod");

        $rs = pg_query($Conec, "SELECT chaves_id, ".$xProj.".chaves.chavenum, ".$xProj.".chaves.chavenumcompl 
        FROM ".$xProj.".chaves_ctl INNER JOIN ".$xProj.".chaves ON ".$xProj.".chaves_ctl.chaves_id = ".$xProj.".chaves.id 
        WHERE ".$xProj.".chaves_ctl.id = $Cod");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $CodChave = $tbl[0];
            $ChaveNum = str_pad($tbl[1], 3, 0, STR_PAD_LEFT).$tbl[2];

            pg_query($Conec, "UPDATE ".$xProj.".chaves SET presente = 1 WHERE id = $CodChave");
//            $rs = pg_query($Conec, "UPDATE ".$xProj.".chaves_ctl SET datavolta = NOW(), funcrecebe = ". $_SESSION['usuarioID'].", cpfdevolve = '$GuardaCpf', usudevolve = $CodUsu, usuedit = ". $_SESSION['usuarioID'].", dataedit = NOW() WHERE id = $Cod");
        }
        if(!$rs){
            $Erro = 1;
        }

        $var = array("coderro"=>$Erro, "numchave"=>$ChaveNum);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "buscaNumero"){
        $Erro = 0;
        $Prox = 1;
        $rs = pg_query($Conec, "SELECT MAX(chavenum) FROM ".$xProj.".chaves");
        $tbl = pg_fetch_row($rs);
        $Prox = ($tbl[0]+1);
    
        $var = array("coderro"=>$Erro, "chavenum"=>str_pad($Prox, 3, 0, STR_PAD_LEFT));
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "buscalog"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); 

        $rs = pg_query($Conec, "SELECT nomecompl, nomeusual, cpf, siglasetor 
        FROM ".$xProj.".poslog INNER JOIN ".$xProj.".setores ON ".$xProj.".poslog.codsetor = ".$xProj.".setores.codset 
        WHERE pessoas_id = $Cod");
        $tbl = pg_fetch_row($rs);

         $rs1 = pg_query($Conec, "SELECT telef FROM ".$xProj.".chaves_ctl WHERE usuretira = $Cod ORDER BY datasaida DESC");
         $row1 = pg_num_rows($rs1);
         if($row1 > 0){
            $tbl1 = pg_fetch_row($rs1);
            $Telef = $tbl1[0];
         }else{
            $Telef = "";
         }

        $var = array("coderro"=>$Erro, "nomecompl"=>$tbl[0], "nome"=>$tbl[1], "cpf"=>$tbl[2], "siglasetor"=>$tbl[3], "telef"=>$Telef );
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "buscacpf"){
        $Erro = 0;
        $Cpf = filter_input(INPUT_GET, 'cpf'); 
        $Cpf1 = addslashes($Cpf);
        $Cpf2 = str_replace(".", "", $Cpf1);
        $GuardaCpf = str_replace("-", "", $Cpf2);

        //pega o último número de telefone informado
        $rs1 = pg_query($Conec, "SELECT telef FROM ".$xProj.".chaves_ctl WHERE cpfretira = '$GuardaCpf' ORDER BY datasaida DESC");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
           $tbl1 = pg_fetch_row($rs1);
           $Telef = $tbl1[0];
        }else{
           $Telef = "";
        }

        $rs = pg_query($Conec, "SELECT nomecompl, nomeusual, cpf, siglasetor, pessoas_id, chave 
        FROM ".$xProj.".poslog INNER JOIN ".$xProj.".setores ON ".$xProj.".poslog.codsetor = ".$xProj.".setores.codset 
        WHERE cpf = '$GuardaCpf' "); //And chave = 1
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $Chave = $tbl[5]; // 1 = está autorizado a retirar chaves
            if($Chave == 0){
                $Erro = 3;    
            }
            $var = array("coderro"=>$Erro, "nomecompl"=>$tbl[0], "nome"=>$tbl[1], "cpf"=>$tbl[2], "siglasetor"=>$tbl[3], "PosCod"=>$tbl[4], "telef"=>$Telef, "chave"=>$Chave);
        }else{
            $Erro = 2;
            $var = array("coderro"=>$Erro );
        }

        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "entregaChave"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); // código id da chave
        $Cpf = filter_input(INPUT_GET, 'cpf'); 
        $Cpf1 = addslashes($Cpf);
        $Cpf2 = str_replace(".", "", $Cpf1);
        $GuardaCpf = str_replace("-", "", $Cpf2);
        $CodUsu = filter_input(INPUT_GET, 'poscod'); 
        $Telef = addslashes(filter_input(INPUT_GET, 'celular')); 

//        $rs0 = pg_query($Conec, "SELECT pessoas_id FROM ".$xProj.".poslog where cpf = '$Cpf'");
//        $row0 = pg_num_rows($rs0);
//        if($row0 > 0){
//            $tbl0 = pg_fetch_row($rs0);
//            $CodUsu = $tbl0[0];
//        }else{
//            $CodUsu = 0;
///        }

        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".chaves_ctl");
        $tblCod = pg_fetch_row($rsCod);
        $CodigoNovo = $tblCod[0]+1;

        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".chaves_ctl (id, chaves_id, cpfretira, datasaida, funcentrega, usuretira, telef, usuins, datains) 
        VALUES ($CodigoNovo, $Cod, '$GuardaCpf', NOW(), ".$_SESSION['usuarioID'].", $CodUsu, '$Telef', ".$_SESSION['usuarioID'].", NOW() )");

        pg_query($Conec, "UPDATE ".$xProj.".chaves SET presente = 0 WHERE id = $Cod");// marca como ausente
        
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "retornoChave"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); // id de chaves_ctl
        $NomeCompl = "";
        $Nome = "";
        $CpfRetirou = "";
        $Telef = "";

        $rs = pg_query($Conec, "SELECT chavenum, chavenumcompl, chavelocal, chavesala, chaveobs, usuretira, telef, cpfretira 
        FROM ".$xProj.".chaves INNER JOIN ".$xProj.".chaves_ctl ON ".$xProj.".chaves.id = ".$xProj.".chaves_ctl.chaves_id
        WHERE ".$xProj.".chaves_ctl.id = $Cod");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $ChaveNum = str_pad($tbl[0], 3, 0, STR_PAD_LEFT);
            $UsuRetirou = $tbl[5];
            $Telef = $tbl[6];
            $CpfRetirou = $tbl[7];
        }
        if(!$rs){
            $Erro = 1;
        }

        $rs2 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $UsuRetirou");
        $row2 = pg_num_rows($rs2);
        if($row2 > 0){
            $tbl2 = pg_fetch_row($rs2);
            $NomeCompl = $tbl2[0];
            $Nome = $tbl2[1];
        }

        $var = array("coderro"=>$Erro, "chavenum"=>$ChaveNum, "chavenumcompl"=>$tbl[1], "chavelocal"=>$tbl[2], "chavesala"=>$tbl[3], "chaveobs"=>$tbl[4], "telef"=>$Telef, "cpfretirou"=>$CpfRetirou, "nomecompl"=>$NomeCompl, "nome"=>$Nome, "codusuretirou"=>$UsuRetirou);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "voltaChave"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); // id de chaves
        $NomeCompl = "";
        $Nome = "";
        $CpfRetirou = "";
        $Telef = "";

        $rs = pg_query($Conec, "SELECT chavenum, chavenumcompl, chavelocal, chavesala, chaveobs, usuretira, telef, cpfretira, chaves_id 
        FROM ".$xProj.".chaves INNER JOIN ".$xProj.".chaves_ctl ON ".$xProj.".chaves.id = ".$xProj.".chaves_ctl.chaves_id
        WHERE ".$xProj.".chaves.id = $Cod And usudevolve = 0 ");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $ChaveNum = str_pad($tbl[0], 3, 0, STR_PAD_LEFT);
            $ChaveCompl = $tbl[1];
            $ChaveLocal = $tbl[2];
            $ChaveSala = $tbl[3];
            $UsuRetirou = $tbl[5];
            $Telef = $tbl[6];
            $CpfRetirou = $tbl[7];
            $IdChaves_Ctl = $tbl[8];
        }else{
            $ChaveNum = 0;
            $ChaveCompl = "";
            $ChaveLocal = "";
            $ChaveSala = "";
            $UsuRetirou = 0;
            $Telef = "";
            $CpfRetirou = "";
        }
        if(!$rs){
            $Erro = 1;
        }

        $rs2 = pg_query($Conec, "SELECT nomecompl, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $UsuRetirou");
        
        $row2 = pg_num_rows($rs2);
        if($row2 > 0){
            $tbl2 = pg_fetch_row($rs2);
            $NomeCompl = $tbl2[0];
            $Nome = $tbl2[1];
        }

        $var = array("coderro"=>$Erro, "chavenum"=>$ChaveNum, "chavenumcompl"=>$ChaveCompl, "chavelocal"=>$ChaveLocal, "chavesala"=>$ChaveSala, "telef"=>$Telef, "cpfretirou"=>$CpfRetirou, "nomecompl"=>$NomeCompl, "nome"=>$Nome, "codusuretirou"=>$UsuRetirou, "guardaCod"=>$IdChaves_Ctl);
        $responseText = json_encode($var);
        echo $responseText;
    }

}
