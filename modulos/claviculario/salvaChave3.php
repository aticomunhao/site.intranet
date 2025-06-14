<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
date_default_timezone_set('America/Sao_Paulo'); 
$Hoje = date('Y/m/d');
require_once(dirname(dirname(__FILE__))."/config/gUtils.php"); // Normatizar nomes próprios
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    $UsuIns = $_SESSION['usuarioID'];

    if($Acao == "buscaChave"){
        $Erro = 0;
        $DataAgenda = "";
        $UsuRetira = "";
        $NomeRetira = "";
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); 
        $rs = pg_query($Conec, "SELECT chavenum, chavenumcompl, chavelocal, chavesala, chaveobs, chavecompl FROM ".$xProj.".chaves3 WHERE id = $Cod");
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
        $var = array("coderro"=>$Erro, "chavenum"=>$ChaveNum, "chavecompl"=>$tbl[5], "chavenumcompl"=>$tbl[1], "chavelocal"=>$tbl[2], "chavesala"=>$tbl[3], "chaveobs"=>$tbl[4], "nomeagendado"=>$NomeRetira);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "salvaChave"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); 
        $Num = (int) filter_input(INPUT_GET, 'numchave');
        $ComplNum =  trim(strtoupper(filter_input(INPUT_GET, 'chavecompl'))); 
        $Compl =  filter_input(INPUT_GET, 'complemchave'); 
        $Sala =  filter_input(INPUT_GET, 'salachave'); 
        $Local =  filter_input(INPUT_GET, 'localchave'); 
        $Obs =  filter_input(INPUT_GET, 'obschave'); 

        if($Cod > 0){
            $rs = pg_query($Conec, "UPDATE ".$xProj.".chaves3 SET chavenum = $Num, chavecompl = '$ComplNum', chavenumcompl = '$Compl', chavelocal = '$Local', chavesala = '$Sala', chaveobs = '$Obs', usuedit = ". $_SESSION['usuarioID'].", dataedit = NOW()  WHERE id = $Cod");
        }else{
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".chaves3");
            $tblCod = pg_fetch_row($rsCod);
            $CodigoNovo = $tblCod[0]+1;

            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".chaves3 (id, chavenum, chavecompl, chavenumcompl, chavelocal, chavesala, chaveobs, presente, usuins, datains, ativo) 
            VALUES ($CodigoNovo, $Num, '$ComplNum', '$Compl', '$Local', '$Sala', '$Obs', 1, ". $_SESSION['usuarioID'].", NOW(), 1) ");
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
        $NomeDevolve = addslashes(filter_input(INPUT_GET, 'nomedevolve'));
        $TelefDevolve = addslashes(filter_input(INPUT_GET, 'telefdevolve'));

        $Cpf = filter_input(INPUT_GET, 'cpfdevolve'); 
        $Cpf1 = addslashes($Cpf);
        $Cpf2 = str_replace(".", "", $Cpf1);
        $GuardaCpf = str_replace("-", "", $Cpf2);

        $rs2 = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE pessoas_id = $CodUsu");
        $row2 = pg_num_rows($rs2);
        if($row2 > 0){
           $rs = pg_query($Conec, "UPDATE ".$xProj.".chaves3_ctl SET datavolta = NOW(), funcrecebe = ". $_SESSION['usuarioID'].", cpfdevolve = '$GuardaCpf', usudevolve = $CodUsu, usuedit = ". $_SESSION['usuarioID'].", dataedit = NOW() WHERE id = $Cod");
        }else{
           $rs = pg_query($Conec, "UPDATE ".$xProj.".chaves3_ctl SET nomedevolve = '$NomeDevolve', telefdevolve = '$TelefDevolve', datavolta = NOW(), funcrecebe = ". $_SESSION['usuarioID'].", cpfdevolve = '$GuardaCpf', usudevolve = $CodUsu, usuedit = ". $_SESSION['usuarioID'].", dataedit = NOW() WHERE id = $Cod");
        } 

        $rs = pg_query($Conec, "SELECT chaves_id, ".$xProj.".chaves3.chavenum, ".$xProj.".chaves3.chavenumcompl 
        FROM ".$xProj.".chaves3_ctl INNER JOIN ".$xProj.".chaves3 ON ".$xProj.".chaves3_ctl.chaves_id = ".$xProj.".chaves3.id 
        WHERE ".$xProj.".chaves3_ctl.id = $Cod");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $CodChave = $tbl[0];
            $ChaveNum = str_pad($tbl[1], 3, 0, STR_PAD_LEFT);
            pg_query($Conec, "UPDATE ".$xProj.".chaves3 SET presente = 1 WHERE id = $CodChave");
        }
        if(!$rs){
            $Erro = 1;
        }
        //retorna o nome de quem devolveu a chave
        $rs1 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $CodUsu");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            $tbl1 = pg_fetch_row($rs1);
            $Nome = $tbl1[0];
        }else{
            $Nome = "";
        }

        $var = array("coderro"=>$Erro, "numchave"=>$ChaveNum, "nome"=>$Nome);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "buscaNumero"){
        $Erro = 0;
        $Prox = 1;
        $rs = pg_query($Conec, "SELECT MAX(chavenum) FROM ".$xProj.".chaves3 WHERE ativo = 1");
        $tbl = pg_fetch_row($rs);
        $Prox = ($tbl[0]+1);
    
        $var = array("coderro"=>$Erro, "chavenum"=>str_pad($Prox, 3, 0, STR_PAD_LEFT));
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "buscalog"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); 
        $CodChave = (int) filter_input(INPUT_GET, 'codChave'); 

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
        if($Telef == ""){
            $rs2 = pg_query($Conec, "SELECT telef FROM ".$xProj.".chaves3_agd WHERE usuretira = $Cod ORDER BY datasaida DESC");
            $row2 = pg_num_rows($rs2);
            if($row2 > 0){
                $tbl2 = pg_fetch_row($rs2);
                $Telef = $tbl2[0];
             }else{
                $Telef = "";
             }
        }
        //Verifica se usuário $Cod pode pegar a chave $CodChave
        $EscChave = parAdm("esc_chaves3", $Conec, $xProj); // marca para ligar/desligar chaves autorizadas a retirar por usuário - arq paramsis
        if($EscChave == 1){ // ligado
            $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".chaves3_aut WHERE pessoas_id = $Cod And chaves_id = $CodChave And ativo = 1");
            $Autoriz = pg_num_rows($rs3);
        }else{ // desligado
            $Autoriz = 1; // todos autorizados
        }

        $var = array("coderro"=>$Erro, "nomecompl"=>$tbl[0], "nome"=>$tbl[1], "cpf"=>$tbl[2], "siglasetor"=>$tbl[3], "telef"=>$Telef, "chaveautorizada"=>$Autoriz );
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "buscacpf"){
        $Erro = 0;
        $Cpf = filter_input(INPUT_GET, 'cpf'); 
        $Cpf1 = addslashes($Cpf);
        $Cpf2 = str_replace(".", "", $Cpf1);
        $GuardaCpf = str_replace("-", "", $Cpf2);
        $CodChave = (int) filter_input(INPUT_GET, 'codChave'); 

        $EscChave = parAdm("esc_chaves3", $Conec, $xProj); // marca para ligar/desligar chaves autorizadas a retirar por usuário - arq paramsis

        //pega o último número de telefone informado
        $rs1 = pg_query($Conec, "SELECT telef FROM ".$xProj.".chaves3_ctl WHERE cpfretira = '$GuardaCpf' ORDER BY datasaida DESC");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
           $tbl1 = pg_fetch_row($rs1);
           $Telef = $tbl1[0];
        }else{
           $Telef = "";
        }

        $rs = pg_query($Conec, "SELECT nomecompl, nomeusual, cpf, siglasetor, pessoas_id, chave3 
        FROM ".$xProj.".poslog INNER JOIN ".$xProj.".setores ON ".$xProj.".poslog.codsetor = ".$xProj.".setores.codset 
        WHERE cpf = '$GuardaCpf' "); //And chave = 1
        $row = pg_num_rows($rs);
        if($row > 0){ // está no site em poslog
            $tbl = pg_fetch_row($rs);
            $Chave = $tbl[5]; // 1 = está autorizado a retirar chaves
            $Cod = $tbl[4]; // cod do usuário para procurar o vínculo com a chave
            if($Chave == 0){
                $Erro = 3;    
            }

            //Verifica se usuário $Cod pode pegar a chave $CodChave
            if($EscChave == 1){ // ligado
                $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".chaves_aut WHERE pessoas_id = $Cod And chaves_id = $CodChave And ativo = 1");
                $Autoriz = pg_num_rows($rs3);
            }else{ // desligado
                $Autoriz = 1; // todos autorizados
            }

            $var = array("coderro"=>$Erro, "nomecompl"=>$tbl[0], "nome"=>$tbl[1], "cpf"=>$tbl[2], "siglasetor"=>$tbl[3], "PosCod"=>$tbl[4], "telef"=>$Telef, "chave"=>$Chave, "chaveautorizada"=>$Autoriz);
        }else{ // vai procurar no arquivo pessoas
            if($EscChave == 0){ // desligado     
                $rs2 = pg_query($ConecPes, "SELECT nome_completo, nome_resumido, cpf, id, TO_CHAR(dt_nascimento, 'DD/MM/YYYY'), TO_CHAR(dt_nascimento, 'DD'), TO_CHAR(dt_nascimento, 'MM') 
                FROM ".$xPes.".pessoas 
                WHERE ".$xPes.".pessoas.cpf = '$GuardaCpf' ");
                $row2 = pg_num_rows($rs2);
                if($row2 > 0){
                    $tbl2 = pg_fetch_row($rs2);

                    $NomeC = GUtils::normalizarNome($tbl2[0]);  // Normatizar nomes próprios
                    $NomeComp = addslashes($NomeC);
                    $NomeCompl = str_replace('"', "'", $NomeComp); // substitui aspas duplas por simples

                    if(!is_null($tbl2[1])){ // nome_resumido
                        $NomeU = $tbl2[1];
                        $NomeUs = GUtils::normalizarNome($NomeU);  // Normatizar nomes próprios
                        $NomeUsu = addslashes($NomeUs);
                        $NomeUsual = str_replace('"', "'", $NomeUsu); // substitui aspas duplas por simples
                    }else{
                        $NomeUsual = "";
                    }
                    $var = array("coderro"=>$Erro, "nomecompl"=>$NomeCompl, "nome"=>$NomeUsual, "cpf"=>$tbl2[2], "siglasetor"=>'', "PosCod"=>$tbl2[3], "telef"=>'', "chave"=>'1', "chaveautorizada"=>'1');
                }else{
                    $Erro = 2;
                    $var = array("coderro"=>$Erro );
                }
            }else{
                $Erro = 2;
                $var = array("coderro"=>$Erro );
            }
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


        $m = strtotime("-1 Hour");
        $HoraAnt = date("Y-m-d H:i:s", $m); // para o recem cadastrado não aparecer on line

        //Inserir usuário em poslog, se seu cpf não estiver lá
        $rs2 = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE cpf = '$GuardaCpf'");
        $row2 = pg_num_rows($rs2);
        if($row2 == 1){
            pg_query($Conec, "UPDATE ".$xProj.".poslog SET ativo = 1, chave3 = 1, logfim = '$HoraAnt' WHERE cpf = '$GuardaCpf'");
        }
        if($row2 == 0){ // inserir
            $rs3 = pg_query($ConecPes, "SELECT id, nome_completo, nome_resumido, dt_nascimento, sexo FROM ".$xPes.".pessoas WHERE cpf = '$GuardaCpf'");
            $tbl3 = pg_fetch_row($rs3);
            $GuardaId = $tbl3[0];
            $CodUsu = $tbl3[0];

            $NomeC = GUtils::normalizarNome($tbl3[1]);  // Normatizar nomes próprios
            $NomeComp = addslashes($NomeC);
            $NomeCompl = str_replace('"', "'", $NomeComp); // substitui aspas duplas por simples

            if(!is_null($tbl3[2])){ // nome_resumido
                $NomeU = $tbl3[2];
                $NomeUs = GUtils::normalizarNome($NomeU);  // Normatizar nomes próprios
                $NomeUsu = addslashes($NomeUs);
                $NomeUsual = str_replace('"', "'", $NomeUsu); // substitui aspas duplas por simples
            }else{
                $NomeUsual = "";
            }

            $DNasc = $tbl3[3];
            if(is_null($DNasc)){
                $DNasc = "1500-01-01";
            }
            $Sexo = $tbl3[4];
            if(is_null($Sexo)){
                $Sexo = 1;
            }

            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".poslog");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodIns = ($Codigo+1); // para inserir em chaves_ctl
            $Senha = password_hash($GuardaCpf, PASSWORD_DEFAULT);
            pg_query($Conec, "INSERT INTO ".$xProj.".poslog (id, pessoas_id, codsetor, adm, usuins, datains, cpf, nomecompl, nomeusual, datanasc, senha, ativo, chave3, logini, logfim) 
            VALUES ($CodIns, $GuardaId, 1, 2, ".$_SESSION['usuarioID'].", NOW(), '$GuardaCpf', '$NomeCompl', '$NomeUsual', '$DNasc', '$Senha', 1,  1, '3000-12-31', '$HoraAnt' )");
        }

        //Verifica se já está vinculado a esta chave - Vincula se não estiver
        $rs4 = pg_query($Conec, "SELECT id FROM ".$xProj.".chaves3_aut WHERE chaves_id = $Cod And pessoas_id = $CodUsu");
        $row4 = pg_num_rows($rs4);
        if($row4 > 0){
            pg_query($Conec, "UPDATE ".$xProj.".chaves3_aut SET ativo = 1 WHERE chaves_id = $Cod And pessoas_id = $CodUsu ");
        }else{
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".chaves3_aut");
            $tblCod = pg_fetch_row($rsCod);
            $CodigoNovo = $tblCod[0]+1;
            pg_query($Conec, "INSERT INTO ".$xProj.".chaves3_aut (id, chaves_id, pessoas_id, ativo, usuins, datains) VALUES($CodigoNovo, $Cod, $CodUsu, 1, ".$_SESSION['usuarioID'].", NOW())");
        }

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


        $m = strtotime("-1 Hour");
        $HoraAnt = date("Y-m-d H:i:s", $m); // para o recem cadastrado não aparecer on line

        //Inserir usuário em poslog, se seu cpf não estiver lá
        $rs2 = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE cpf = '$GuardaCpf'");
        $row2 = pg_num_rows($rs2);
        if($row2 == 1){
            pg_query($Conec, "UPDATE ".$xProj.".poslog SET ativo = 1, chave3 = 1, logfim = '$HoraAnt' WHERE cpf = '$GuardaCpf'");
        }
        if($row2 == 0){ // inserir
            $rs3 = pg_query($ConecPes, "SELECT id, nome_completo, nome_resumido, dt_nascimento, sexo FROM ".$xPes.".pessoas WHERE cpf = '$GuardaCpf'");
            $tbl3 = pg_fetch_row($rs3);
            $GuardaId = $tbl3[0];

            $NomeC = GUtils::normalizarNome($tbl3[1]);  // Normatizar nomes próprios
            $NomeComp = addslashes($NomeC);
            $NomeCompl = str_replace('"', "'", $NomeComp); // substitui aspas duplas por simples
        
            if(!is_null($tbl3[2])){ // nome_resumido
                $NomeU = $tbl3[2];
                $NomeUs = GUtils::normalizarNome($NomeU);  // Normatizar nomes próprios
                $NomeUsu = addslashes($NomeUs);
                $NomeUsual = str_replace('"', "'", $NomeUsu); // substitui aspas duplas por simples
            }else{
                $NomeUsual = "";
            }
        
            $DNasc = $tbl3[3];
            if(is_null($DNasc)){
                $DNasc = "1500-01-01";
            }
            $Sexo = $tbl3[4];
            if(is_null($Sexo)){
                $Sexo = 1;
            }
        
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".poslog");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodIns = ($Codigo+1); // para inserir em chaves_ctl
            $Senha = password_hash($GuardaCpf, PASSWORD_DEFAULT);
            pg_query($Conec, "INSERT INTO ".$xProj.".poslog (id, pessoas_id, codsetor, adm, usuins, datains, cpf, nomecompl, nomeusual, datanasc, senha, ativo, chave3, logini, logfim) 
            VALUES ($CodIns, $GuardaId, 1, 2, ".$_SESSION['usuarioID'].", NOW(), '$GuardaCpf', '$NomeCompl', '$NomeUsual', '$DNasc', '$Senha', 1,  1, '3000-12-31', '$HoraAnt' )");
        }

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
        $rs0 = pg_query($Conec, "SELECT chavenum, chavecompl FROM ".$xProj.".chaves3 WHERE id = $Cod");
        $row0 = pg_num_rows($rs0);
        $tbl0=pg_fetch_row($rs0);
        $Chave = $tbl0[0].$tbl0[1];

        $rs1 = pg_query($Conec, "SELECT ".$xProj.".chaves3_ctl.id 
        FROM ".$xProj.".chaves3 INNER JOIN ".$xProj.".chaves3_ctl ON ".$xProj.".chaves3.id = ".$xProj.".chaves3_ctl.chaves_id 
        WHERE CONCAT(chavenum, chavecompl) = '$Chave' And ".$xProj.".chaves3_ctl.ativo = 1 And presente = 0 And usudevolve = 0 ");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){ 
            $tbl1=pg_fetch_row($rs1);
            $Ctl_id = $tbl1[0];
        }else{
            $Ctl_id = 0;
        }

        $rs = pg_query($Conec, "SELECT chavenum, chavenumcompl, chavelocal, chavesala, chaveobs, usuretira, telef, cpfretira, chavecompl 
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

        $var = array("coderro"=>$Erro, "chavenum"=>$ChaveNum, "chavenumcompl"=>$tbl[1], "chavecompl"=>$tbl[8], "chavelocal"=>$tbl[2], "chavesala"=>$tbl[3], "chaveobs"=>$tbl[4], "telef"=>$Telef, "cpfretirou"=>$CpfRetirou, "nomecompl"=>$NomeCompl, "nome"=>$Nome, "codusuretirou"=>$UsuRetirou, "codidctl"=>$Ctl_id);
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

        $rs = pg_query($Conec, "SELECT chavenum, chavenumcompl, chavelocal, chavesala, chaveobs, usuretira, telef, cpfretira, chavecompl 
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

        $var = array("coderro"=>$Erro, "chavenum"=>$ChaveNum, "chavenumcompl"=>$tbl[1], "chavecompl"=>$tbl[8], "chavelocal"=>$tbl[2], "chavesala"=>$tbl[3], "chaveobs"=>$tbl[4], "telef"=>$Telef, "cpfretirou"=>$CpfRetirou, "nomecompl"=>$NomeCompl, "nome"=>$Nome, "codusuretirou"=>$UsuRetirou);
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

        $rs = pg_query($Conec, "SELECT chavenum, chavenumcompl, chavelocal, chavesala, chaveobs, presente, chavecompl FROM ".$xProj.".chaves3 WHERE id = $Cod");
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

        $var = array("coderro"=>$Erro, "chavenum"=>$ChaveNum, "chavenumcompl"=>$tbl[1], "chavecompl"=>$tbl[6], "chavelocal"=>$tbl[2], "chavesala"=>$tbl[3], "chaveobs"=>$tbl[4], "presente"=>$tbl[5], "nomecompl"=>$NomeCompl, "nome"=>$Nome, "siglasetor"=>$SiglaSetor, "cpf"=>$Cpf, "telef"=>$Telef, "nomeretirou"=>$NomeRetirou, "cpfretirou"=>$CpfRetirou, "telefretirou"=>$TelefRetirou );
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "buscausuario"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); //id de polog

        $rs1 = pg_query($Conec, "SELECT clav3, chave3, fisc_clav3, cpf, clav_edit3, nomeusual FROM ".$xProj.".poslog WHERE pessoas_id = $Cod");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            $tbl1 = pg_fetch_row($rs1);
            $var = array("coderro"=>$Erro, "claviculario"=>$tbl1[0], "pegachave"=>$tbl1[1], "fiscchaves"=>$tbl1[2], "cpf"=>$tbl1[3], "editachave"=>$tbl1[4], "nomeusual"=>$tbl1[5]);
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

        $rs1 = pg_query($Conec, "SELECT clav3, chave3, fisc_clav3, cpf, pessoas_id, clav_edit3, nomeusual FROM ".$xProj.".poslog WHERE cpf = '$GuardaCpf'");
        if(!$rs1){
            $Erro = 1;
            $var = array("coderro"=>$Erro);
        }
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            $tbl1 = pg_fetch_row($rs1);
            $var = array("coderro"=>$Erro, "claviculario"=>$tbl1[0], "pegachave"=>$tbl1[1], "fiscchaves"=>$tbl1[2], "cpf"=>$tbl1[3], "PosCod"=>$tbl1[4], "editachave"=>$tbl1[5], "nomeusual"=>$tbl1[6]);
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

        if($Campo == "clav_edit3" && $Valor == 0){
            $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE clav_edit3 = 1");
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

    if($Acao == "apagaretiradaChave"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); // id de chaves2_ctl
        $CodIdChaves = (int) filter_input(INPUT_GET, 'idchaves3'); // id de chaves2
        pg_query($Conec, "UPDATE ".$xProj.".chaves3 SET presente = 1 WHERE id = $CodIdChaves");
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".chaves3_ctl SET ativo = 0 WHERE id = $Cod");
        if(!$rs1){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "apagaChave"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); // id de chaves

        $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".chaves3_ctl WHERE id = $Cod And usudevolve = 0");
        $row = pg_num_rows($rs);
        if($row == 1){
            $Erro = 2; // chave ainda não voltou
            $var = array("coderro"=>$Erro);
            $responseText = json_encode($var);
            echo $responseText;
            return false;
        }

        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".chaves3 SET ativo = 0 WHERE id = $Cod");
        if(!$rs1){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "apagaChave_ctl"){
        $Erro = 0; // apagar chave que ainda não voltou
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); 
        $rs = pg_query($Conec, "UPDATE ".$xProj.".chaves3_ctl SET ativo = 0 WHERE chaves_id = $Cod And usudevolve = 0 ");
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".chaves3 SET ativo = 0 WHERE id = $Cod");
        if(!$rs1){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="marcaChaveUsuario"){
        $CodChave = (int) filter_input(INPUT_GET, 'codigo'); // id de Chaves
        $Param = (int) filter_input(INPUT_GET, 'param');
        $Usu = (int) filter_input(INPUT_GET, 'usuario');
        $Erro = 0;
        $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".chaves3_aut WHERE chaves_id = $CodChave And pessoas_id = $Usu ");
        $row = pg_num_rows($rs);
        if($row > 0){
            if($Param == 1 ){
                $rs = pg_query($Conec, "UPDATE ".$xProj.".chaves3_aut SET ativo = 1, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE chaves_id = $CodChave And pessoas_id = $Usu ");
            }else{
                $rs = pg_query($Conec, "UPDATE ".$xProj.".chaves3_aut SET ativo = 0, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE chaves_id = $CodChave And pessoas_id = $Usu ");
            }
        }else{ // inserir
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".chaves3_aut");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1); 
            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".chaves3_aut (id, chaves_id, pessoas_id, ativo, usuins, datains) 
            VALUES ( $CodigoNovo, $CodChave, $Usu, 1, ".$_SESSION["usuarioID"].", NOW())");
        }
        if(!$rs){
            $Erro = 1;
        }
        //Conta as chaves marcadas
        $rsCont = pg_query($Conec, "SELECT id FROM ".$xProj.".chaves3_aut WHERE pessoas_id = $Usu And ativo = 1");
        $rowCont = pg_num_rows($rsCont);
        //Conta o total de chaves
        $rsT = pg_query($Conec, "SELECT id FROM ".$xProj.".chaves WHERE ativo = 1 ");
        $rowT = pg_num_rows($rsT);
    
        $Todos = 0;
        if($rowCont == $rowT){
            $Todos = 1;
        }
    
        $var = array("coderro"=>$Erro, "marcadas"=>$rowCont, "todas"=>$Todos);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="marcaChaveTodas"){
        $Param = (int) filter_input(INPUT_GET, 'param');
        $Usu = (int) filter_input(INPUT_GET, 'usuario');
        $Erro = 0;
    
        if($Param == 0){ // desmarcar todas
            $rs = pg_query($Conec, "UPDATE ".$xProj.".chaves3_aut SET ativo = 0, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE pessoas_id = $Usu "); 
        }else{ // marcar todas
            pg_query($Conec, "UPDATE ".$xProj.".chaves3_aut SET ativo = 1 WHERE pessoas_id = $Usu"); // se tiver marca
            $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".chaves WHERE ativo = 1 ORDER BY chavenum, chavenumcompl ");
            $row = pg_num_rows($rs);
            if($row > 0){
                while($tbl = pg_fetch_row($rs)){
                    $CodChave = $tbl[0];
                    $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".chaves3_aut WHERE chaves_id = $CodChave And pessoas_id = $Usu");
                    $row1 = pg_num_rows($rs1);
                    if($row1 == 0){
                        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".chaves3_aut");
                        $tblCod = pg_fetch_row($rsCod);
                        $Codigo = $tblCod[0];
                        $CodigoNovo = ($Codigo+1); 
                        pg_query($Conec, "INSERT INTO ".$xProj.".chaves3_aut (id, chaves_id, pessoas_id, ativo, usuins, datains) 
                        VALUES ( $CodigoNovo, $CodChave, $Usu, 1, ".$_SESSION["usuarioID"].", NOW())");
                    }
                }
            }
        }
        if(!$rs){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }


}
