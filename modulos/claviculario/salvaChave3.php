<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
date_default_timezone_set('America/Sao_Paulo'); 
$Hoje = date('Y/m/d');
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    $UsuIns = $_SESSION['usuarioID'];

    if($Acao == "buscaChave"){
        $Erro = 0;
        $DataAgenda = "";
        $UsuRetira = "";
        $NomeRetira = "";
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); 
        $rs = pg_query($Conec, "SELECT chavenum, chavenumcompl, chavelocal, chavesala, chaveobs FROM ".$xProj.".chaves3 WHERE id = $Cod");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $ChaveNum = str_pad($tbl[0], 3, 0, STR_PAD_LEFT);
        }
        if(!$rs){
            $Erro = 1;
        }

        // verifica se e para quando está agendada
        $rs1 = pg_query($Conec, "SELECT id, datasaida, usuretira FROM ".$xProj.".chaves3_agd WHERE chaves_id = $Cod And ativo = 1");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            while($tbl1 = pg_fetch_row($rs1)){
                $DataAgenda = $tbl1[1];
                if(strtotime($DataAgenda) == strtotime($Hoje)){
                    $Erro = 2;
                    $UsuRetira = $tbl1[2];
                    $rs2 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $UsuRetira And ativo = 1");
                    $row2 = pg_num_rows($rs2);
                    if($row2 > 0){
                        $tbl2 = pg_fetch_row($rs2);
                        $NomeRetira = $tbl2[0];
                    }
                    
                }
            }
        }
        $var = array("coderro"=>$Erro, "chavenum"=>$ChaveNum, "chavenumcompl"=>$tbl[1], "chavelocal"=>$tbl[2], "chavesala"=>$tbl[3], "chaveobs"=>$tbl[4], "nomeagendado"=>$NomeRetira);
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
            $rs = pg_query($Conec, "UPDATE ".$xProj.".chaves3 SET chavenum = $Num, chavenumcompl = '$Compl', chavelocal = '$Local', chavesala = '$Sala', chaveobs = '$Obs', usuedit = ". $_SESSION['usuarioID'].", dataedit = NOW()  WHERE id = $Cod");
        }else{
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".chaves3");
            $tblCod = pg_fetch_row($rsCod);
            $CodigoNovo = $tblCod[0]+1;

            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".chaves3 (id, chavenum, chavenumcompl, chavelocal, chavesala, chaveobs, presente, usuins, datains) 
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

        $rs = pg_query($Conec, "UPDATE ".$xProj.".chaves3_ctl SET datavolta = NOW(), funcrecebe = ". $_SESSION['usuarioID'].", cpfdevolve = '$GuardaCpf', usudevolve = $CodUsu, usuedit = ". $_SESSION['usuarioID'].", dataedit = NOW() WHERE id = $Cod");

        $rs = pg_query($Conec, "SELECT chaves_id, ".$xProj.".chaves3.chavenum, ".$xProj.".chaves3.chavenumcompl 
        FROM ".$xProj.".chaves3_ctl INNER JOIN ".$xProj.".chaves3 ON ".$xProj.".chaves3_ctl.chaves_id = ".$xProj.".chaves3.id 
        WHERE ".$xProj.".chaves3_ctl.id = $Cod");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $CodChave = $tbl[0];
//            $ChaveNum = str_pad($tbl[1], 3, 0, STR_PAD_LEFT).$tbl[2];
            $ChaveNum = str_pad($tbl[1], 3, 0, STR_PAD_LEFT);
            pg_query($Conec, "UPDATE ".$xProj.".chaves3 SET presente = 1 WHERE id = $CodChave");
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
        $rs = pg_query($Conec, "SELECT MAX(chavenum) FROM ".$xProj.".chaves3");
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
        if(!$rs){
            $Erro = 1;
        }else{
            $row = pg_num_rows($rs);
            if($row > 0){
                $tbl = pg_fetch_row($rs);
            }else{
                $Erro = 1;
                $var = array("coderro"=>$Erro);
                $responseText = json_encode($var);
                echo $responseText;
                return false;
            }
        }

         $rs1 = pg_query($Conec, "SELECT telef FROM ".$xProj.".chaves3_ctl WHERE usuretira = $Cod ORDER BY datasaida DESC");
         $row1 = pg_num_rows($rs1);
         if($row1 > 0){
            $tbl1 = pg_fetch_row($rs1);
            $Telef = $tbl1[0];
         }else{
            $Telef = "";
         }
         if($Telef == ""){
            $rs2 = pg_query($Conec, "SELECT telef FROM ".$xProj.".chaves3_ctl WHERE usudevolve = $Cod ORDER BY datavolta DESC");
            $row2 = pg_num_rows($rs2);
            if($row2 > 0){
                $tbl2 = pg_fetch_row($rs2);
                $Telef = $tbl2[0];
             }else{
                $Telef = "";
             }
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
        $rs1 = pg_query($Conec, "SELECT telef FROM ".$xProj.".chaves3_ctl WHERE cpfretira = '$GuardaCpf' ORDER BY datasaida DESC");
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
        $CodUsu = (int) filter_input(INPUT_GET, 'poscod'); 
        $IdAgenda = (int) filter_input(INPUT_GET, 'idagenda'); 
        $Telef = addslashes(filter_input(INPUT_GET, 'celular')); 
        $DataAgenda = "";

        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".chaves3_ctl");
        $tblCod = pg_fetch_row($rsCod);
        $CodigoNovo = $tblCod[0]+1;

        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".chaves3_ctl (id, chaves_id, cpfretira, datasaida, funcentrega, usuretira, telef, usuins, datains) 
        VALUES ($CodigoNovo, $Cod, '$GuardaCpf', NOW(), ".$_SESSION['usuarioID'].", $CodUsu, '$Telef', ".$_SESSION['usuarioID'].", NOW() )");

        pg_query($Conec, "UPDATE ".$xProj.".chaves3 SET presente = 0 WHERE id = $Cod");// marca como ausente

        //Detectar de onde está vindo
        if($IdAgenda > 0){ // se era uma chave agendada sendo entregue
            pg_query($Conec, "UPDATE ".$xProj.".chaves3_agd SET ativo = 0 WHERE id = $IdAgenda ");
        }else{ // verifica se e para quando está agendada
            $rs1 = pg_query($Conec, "SELECT id, datasaida FROM ".$xProj.".chaves3_agd WHERE chaves_id = $Cod");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $DataAgenda = $tbl1[1];
                if(strtotime($DataAgenda) == strtotime($Hoje)){
                    $Erro = 2;
                }
            }
        }

        $var = array("coderro"=>$Erro, "idagenda"=>$IdAgenda, "dataag"=>strtotime($DataAgenda), "hoje"=>strtotime($Hoje) );
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "agendaChave"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); // código id da chave
        $Cpf = filter_input(INPUT_GET, 'cpf'); 

        $Cpf1 = addslashes($Cpf);
        $Cpf2 = str_replace(".", "", $Cpf1);
        $GuardaCpf = str_replace("-", "", $Cpf2);
        $CodUsu = filter_input(INPUT_GET, 'poscod'); 
        $Telef = addslashes(filter_input(INPUT_GET, 'celular')); 

        $Data = addslashes(filter_input(INPUT_GET, 'dataagenda')); 
        $RevData = implode("/", array_reverse(explode("/", $Data)));

        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".chaves3_agd");
        $tblCod = pg_fetch_row($rsCod);
        $CodigoNovo = $tblCod[0]+1;

        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".chaves3_agd (id, chaves_id, cpfretira, datasaida, usuretira, telef, usuins, datains) 
        VALUES ($CodigoNovo, $Cod, '$GuardaCpf', '$RevData', $CodUsu, '$Telef', ".$_SESSION['usuarioID'].", NOW() )");
    
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "retornoChave1"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); // id de chaves
        $NomeCompl = "";
        $Nome = "";
        $CpfRetirou = "";
        $Telef = "";

        //Pega o número da chave
        $rs0 = pg_query($Conec, "SELECT chavenum, chavenumcompl FROM ".$xProj.".chaves3 WHERE id = $Cod");
        $row0 = pg_num_rows($rs0);
        $tbl0=pg_fetch_row($rs0);
        $Chave = $tbl0[0].$tbl0[1];

        $rs1 = pg_query($Conec, "SELECT ".$xProj.".chaves3_ctl.id 
        FROM ".$xProj.".chaves3 INNER JOIN ".$xProj.".chaves3_ctl ON ".$xProj.".chaves3.id = ".$xProj.".chaves3_ctl.chaves_id 
        WHERE CONCAT(chavenum, chavenumcompl) = '$Chave' And presente = 0 And usudevolve = 0 ");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){ 
            $tbl1=pg_fetch_row($rs1);
            $Ctl_id = $tbl1[0];
        }else{
            $Ctl_id = 0;
        }

        $rs = pg_query($Conec, "SELECT chavenum, chavenumcompl, chavelocal, chavesala, chaveobs, usuretira, telef, cpfretira 
        FROM ".$xProj.".chaves3 INNER JOIN ".$xProj.".chaves3_ctl ON ".$xProj.".chaves3.id = ".$xProj.".chaves3_ctl.chaves_id
        WHERE ".$xProj.".chaves3_ctl.id = $Ctl_id");
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

        $var = array("coderro"=>$Erro, "chavenum"=>$ChaveNum, "chavenumcompl"=>$tbl[1], "chavelocal"=>$tbl[2], "chavesala"=>$tbl[3], "chaveobs"=>$tbl[4], "telef"=>$Telef, "cpfretirou"=>$CpfRetirou, "nomecompl"=>$NomeCompl, "nome"=>$Nome, "codusuretirou"=>$UsuRetirou, "codidctl"=>$Ctl_id);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "retornoChave"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); // id de chaves3_ctl
        $NomeCompl = "";
        $Nome = "";
        $CpfRetirou = "";
        $Telef = "";

        $rs = pg_query($Conec, "SELECT chavenum, chavenumcompl, chavelocal, chavesala, chaveobs, usuretira, telef, cpfretira 
        FROM ".$xProj.".chaves3 INNER JOIN ".$xProj.".chaves3_ctl ON ".$xProj.".chaves3.id = ".$xProj.".chaves3_ctl.chaves_id
        WHERE ".$xProj.".chaves3_ctl.id = $Cod");
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

    if($Acao == "buscaChaveAgenda"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); //id de chaves
        $CodUsu = (int) filter_input(INPUT_GET, 'codusu'); // id de poslog
        $IdAgenda = (int) filter_input(INPUT_GET, 'codagenda'); // id de agenda  
        $Data = addslashes(filter_input(INPUT_GET, 'dataagenda')); 
        $Presente = 1;

        $rs = pg_query($Conec, "SELECT chavenum, chavenumcompl, chavelocal, chavesala, chaveobs, presente FROM ".$xProj.".chaves3 WHERE id = $Cod");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $ChaveNum = str_pad($tbl[0], 3, 0, STR_PAD_LEFT);
            $Presente = (int) $tbl[5]; // 0 = ausente, 1 = no claviculário
        }
        if(!$rs){
            $Erro = 1;
        }

        $rs1 = pg_query($Conec, "SELECT nomecompl, nomeusual, cpf, siglasetor 
        FROM ".$xProj.".poslog INNER JOIN ".$xProj.".setores ON ".$xProj.".poslog.codsetor = ".$xProj.".setores.codset 
        WHERE pessoas_id = $CodUsu");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            $tbl1 = pg_fetch_row($rs1);
            $NomeCompl = $tbl1[0];
            $Nome = $tbl1[1];
            $Cpf = $tbl1[2];
            $SiglaSetor = $tbl1[3];
        }else{
            $NomeCompl = "";
            $Nome = "";
            $Cpf = "";
            $SiglaSetor = "";
        }

        $rs2 = pg_query($Conec, "SELECT telef FROM ".$xProj.".chaves3_agd WHERE id = $IdAgenda ");   //usuretira = $CodUsu And TO_CHAR(datasaida, 'DD/MM/YYYY') = '$Data' ORDER BY datasaida DESC");
        $row2 = pg_num_rows($rs2);
        if($row2 > 0){
            $tbl2 = pg_fetch_row($rs2);
            $Telef = $tbl2[0];
        }else{
            $Telef = "";
        }
        
        if($Presente == 0){
            $rs3 = pg_query($Conec, "SELECT nomecompl, cpf, telef 
            FROM ".$xProj.".poslog INNER JOIN ".$xProj.".chaves3_ctl ON ".$xProj.".poslog.pessoas_id = ".$xProj.".chaves3_ctl.usuretira 
            WHERE chaves_id = $Cod And usudevolve = 0");
            $row3 = pg_num_rows($rs3);
            if($row3 > 0){
                $tbl3 = pg_fetch_row($rs3);
                $NomeRetirou = $tbl3[0];
                $CpfRetirou = $tbl3[1];
                $TelefRetirou = $tbl3[2];
            }else{
                $NomeRetirou = "";
                $CpfRetirou = "";
                $TelefRetirou = "";
            }
        }else{
            $NomeRetirou = "";
            $CpfRetirou = "";
            $TelefRetirou = "";
        }

        $var = array("coderro"=>$Erro, "chavenum"=>$ChaveNum, "chavenumcompl"=>$tbl[1], "chavelocal"=>$tbl[2], "chavesala"=>$tbl[3], "chaveobs"=>$tbl[4], "presente"=>$tbl[5], "nomecompl"=>$NomeCompl, "nome"=>$Nome, "siglasetor"=>$SiglaSetor, "cpf"=>$Cpf, "telef"=>$Telef, "nomeretirou"=>$NomeRetirou, "cpfretirou"=>$CpfRetirou, "telefretirou"=>$TelefRetirou );
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "buscausuario"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); //id de polog

        $rs1 = pg_query($Conec, "SELECT clav3, chave3, fisc_clav3, cpf FROM ".$xProj.".poslog WHERE pessoas_id = $Cod");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            $tbl1 = pg_fetch_row($rs1);
            $var = array("coderro"=>$Erro, "claviculario"=>$tbl1[0], "pegachave"=>$tbl1[1], "fiscchaves"=>$tbl1[2], "cpf"=>$tbl1[3]);
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

        $rs1 = pg_query($Conec, "SELECT clav3, chave3, fisc_clav3, cpf, pessoas_id FROM ".$xProj.".poslog WHERE cpf = '$GuardaCpf'");
        if(!$rs1){
            $Erro = 1;
            $var = array("coderro"=>$Erro);
        }
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            $tbl1 = pg_fetch_row($rs1);
            $var = array("coderro"=>$Erro, "claviculario"=>$tbl1[0], "pegachave"=>$tbl1[1], "fiscchaves"=>$tbl1[2], "cpf"=>$tbl1[3], "PosCod"=>$tbl1[4]);
        }else{
            $Erro = 2;
            $var = array("coderro"=>$Erro);
        }        
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "configMarcaChave"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id de poslog
        $Campo = filter_input(INPUT_GET, 'campo');
        $Valor = (int) filter_input(INPUT_GET, 'valor');

        if($Campo == "fisc_clav3" && $Valor == 0){
            $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE fisc_clav3 = 1");
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

    if($Acao == "apagaagendaChave"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); // id de chaves3_agd

        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".chaves3_agd SET ativo = 2 WHERE id = $Cod");
        if(!$rs1){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }


}
