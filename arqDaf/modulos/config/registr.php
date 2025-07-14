<?php
session_name("arqAdm"); // sessão diferente da CEsB
session_start(); // inicia uma sessão
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    require_once("abrealas.php");
    $Conec = conecPost(); // habilitar a extensão: extension=pgsql no phpini
    $ConecPes = conecPes();
    date_default_timezone_set('America/Sao_Paulo');

    if($ConecPes == "sConec" || $ConecPes == "sFunc"){
        $ConecPes = $Conec;
        $xPes = $xProj;
    }
}

require_once("gUtils.php"); // Classe para Normatizar nomes próprios

if($Acao =="loglog"){
    $Cpf = filter_input(INPUT_GET, 'usuario'); 
    $Cpf1 = addslashes($Cpf);
    $Cpf2 = str_replace(".", "", $Cpf1);
    $Usu = str_replace("-", "", $Cpf2);
    $Login = removeInj($Usu);

    $Sen = filter_input(INPUT_GET, 'senha');
    $Sen = removeInj($Sen);

    $Erro = 0;
    $Erro_Msg = "";
    $id = 0; 

    if($Conec != "sConec" && $Conec != "sFunc"){
        $rs = pg_query($Conec, "SELECT * FROM information_schema.tables WHERE table_schema = 'cesb';");
        $row = pg_num_rows($rs);
        if($row == 0){
            $Erro = 6;
            $Erro_Msg = "Erro de sistema. Faltam tabelas. Informe à ATI.";
            $var = array("coderro"=>$Erro, "msg"=>$Erro_Msg);
            $responseText = json_encode($var);
            echo $responseText;
            return;
        }
        $rs = pg_query($Conec, "SELECT column_name, data_type FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'daf_poslog'");
        $row = pg_num_rows($rs);
        if($row == 0){
            $Erro = 6;
            $Erro_Msg = "Faltam tabelas. Informe à ATI.";
            $var = array("coderro"=>$Erro, "msg"=>$Erro_Msg);
            $responseText = json_encode($var);
            echo $responseText;
            return false;
        }

        $rs0 = pg_query($ConecPes, "SELECT nome_completo, sexo, cpf, dt_nascimento FROM ".$xPes.".pessoas WHERE cpf = '$Login' And status = 1");
        $row0 = pg_num_rows($rs0); 
        if($row0 == 1){ // está no arquivo pessoas
            $rs1 = pg_query($Conec, "SELECT senha FROM ".$xProj.".daf_poslog WHERE cpf = '$Login' And ativo = 1");
            $row1 = pg_num_rows($rs1);
            if($row1 == 1){ // está no arquivo poslog
                $tbl1 = pg_fetch_row($rs1);

                if(password_verify($Sen, $tbl1[0])){
                    $tbl0 = pg_fetch_row($rs0);
                    $rs = pg_query($ConecPes, "SELECT id, cpf, nome_completo, TO_CHAR(dt_nascimento, 'DD/MM/YYYY'), TO_CHAR(dt_nascimento, 'DD'), TO_CHAR(dt_nascimento, 'MM'), sexo, nome_resumido 
                    FROM ".$xPes.".pessoas WHERE cpf = '$Login' ");  //And status = 1");
                    $Sql = pg_fetch_row($rs);
                    $id = $Sql[0];
                    $NomeC = GUtils::normalizarNome($Sql[2]);  // Normatizar nomes próprios
                    $NomeComp = addslashes($NomeC);
                    $NomeCompl = str_replace('"', "'", $NomeComp); // substitui aspas duplas por simples
                    $_SESSION["NomeCompl"] = $NomeCompl;

                    if(!is_null($Sql[7])){ // nome_resumido
                        $NomeU = $Sql[7];
                        $NomeUs = GUtils::normalizarNome($NomeU);  // Normatizar nomes próprios
                        $NomeUsu = addslashes($NomeUs);
                        $NomeUsual = str_replace('"', "'", $NomeUsu); // substitui aspas duplas por simples
                    }else{
                        $NomeUsual = "";
                    }
                    $_SESSION["NomeUsual"] = $NomeUsual;
                    if($NomeUsual == ""){
                        $_SESSION["NomeUsual"] = $_SESSION["NomeCompl"];
                    }

                    $DiaAniv = $Sql[4];
                    $MesAniv = $Sql[5];
                    $_SESSION['usuarioCPF'] = $Sql[1];
                    if(!is_null($Sql[6])){
                        $Sexo = $Sql[6];    
                    }else{
                        $Sexo = 1;
                    }
                    if(!is_null($Sql[3])){
                        $DNasc = $Sql[3];    
                    }else{
                        $DNasc = "1500-01-01";
                    }
                    $_SESSION['msgarqDaf'] = ""; //para upload arquivos diretorias/assessorias
                    $TamSen = strlen($Sen);
                    // Aumenta número de acessos e grava data hora do login  - logfim = now() para mostrar on line  - extsen tamanho da senha para o login sem enter
                    pg_query($Conec, "UPDATE ".$xProj.".daf_poslog SET numacessos = (numacessos + 1), logini = NOW(), logfim = NOW(), extsen = $TamSen WHERE pessoas_id = $id "); 

                    $_SESSION["CodSetorUsuDaf"] = 0 ;
                    $rs2 = pg_query($Conec, "SELECT adm, codsetor, nomeusual FROM ".$xProj.".daf_poslog WHERE cpf = '$Login' ");
                    $tbl2 = pg_fetch_row($rs2);
                    $_SESSION["CodSetorUsuDaf"] = $tbl2[1];
                    if(!is_null($tbl2[2]) && $tbl2[2] != ""){
                        $_SESSION["NomeUsual"] = $tbl2[2];
                    }
                    $_SESSION["SiglaSetor"] = "";
                    $rs3 = pg_query($Conec, "SELECT siglasetor, descsetor FROM ".$xProj.".setores WHERE codset = $tbl2[1] "); 
                    $row3 = pg_num_rows($rs3);
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $_SESSION["SiglaSetor"] = $tbl3[0];
                    }

                    if($Sen == $Login){
                        $Erro = 5; // primeiro login - inserir nova senha
                    }

                    $var = array("coderro"=>$Erro, "msg"=>$Erro_Msg, "usuarioid"=>$id, "usuarioNome"=>$NomeCompl, "usuario"=>$NomeUsual); 
                    $responseText = json_encode($var);
                    echo $responseText;
                    return;
                }else{ // usuário está no poslog mas a senha não confere 
                    $Erro = 6;
                    $Erro_Msg = "Usuário ou senha não conferem. Erro 2244";
                    $var = array("coderro"=>$Erro, "msg"=>$Erro_Msg);
                    $responseText = json_encode($var);
                    echo $responseText;
                    return;
                }
            }else{ // não está no poslog
                $Erro = 6;
                $Erro_Msg = "Usuário e/ou senha não encontrados. Erro 1673";
                $rs5 = pg_query($Conec, "SELECT ativo FROM ".$xProj.".poslog WHERE cpf = '$Login'");
                $row5 = pg_num_rows($rs5);
                if($row5 > 0){ // para saber se está e é bloqueado
                    $tbl5 = pg_fetch_row($rs5);
                    if($tbl5[0] == 0){
                        $Erro = 4;
                        $Erro_Msg = "Usuário bloqueado. Informe à ATI.";
                    }
                }
                $var = array("coderro"=>$Erro, "msg"=>$Erro_Msg);
            }
        }else{ // não está em pessoas ou está com status 0 
            $Erro = 6;
            $Erro_Msg = "Usuário ou senha não cadastrados. Erro 1244";
            $var = array("coderro"=>$Erro, "msg"=>$Erro_Msg);
        }
    }else{ // sem contato com o postgre
        $Erro = 1;
        $Erro_Msg = "Sem contato com o servidor. Informe à ATI.";
        if($Conec == "sFunc"){
            $Erro_Msg = "Sem conexão com BD.";
        }
        $var = array("coderro"=>$Erro, "msg"=>$Erro_Msg);
    }
    $responseText = json_encode($var);
    echo $responseText;
}


if($Acao =="buscausu"){
    $Usu = (int) filter_input(INPUT_GET, 'numero'); 
    $Cpf = filter_input(INPUT_GET, 'cpf'); 
    $Cpf1 = addslashes($Cpf);
    $Cpf2 = str_replace(".", "", $Cpf1);
    $GuardaCpf = str_replace("-", "", $Cpf2);
    $Erro = 0;

    $rs0 = pg_query($ConecPes, "SELECT cpf, nome_completo, to_char(dt_nascimento, 'DD'), TO_CHAR(dt_nascimento, 'MM'), nome_resumido FROM ".$xPes.".pessoas WHERE cpf = '$GuardaCpf' ");
    if(!$rs0){
        $Erro = 1;
    }
    $row0 = pg_num_rows($rs0);
    if($row0 == 0){
        $Erro = 2;
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
        return false;
    }else{
        $Proc0 = pg_fetch_row($rs0);
    }

    $rs = pg_query($Conec, "SELECT adm, codsetor, ativo, to_char(logini, 'DD/MM/YYYY HH24:MI'), numacessos, nomeusual 
    FROM ".$xProj.".daf_poslog WHERE cpf = '$GuardaCpf' ");  //pessoas_id = $Usu ");

    $row = pg_num_rows($rs);
    if($row == 0){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $Proc = pg_fetch_row($rs);
        $UltLog = $Proc[3];
        if(is_null($Proc[3])){
            $UltLog = "";
        }
        if($Proc[3] == "31/12/3000 00:00"){
            $UltLog = "";
        }
        if($Proc[3] == "01/01/1500 00:00"){
            $UltLog = "";
        }
        $var = array("coderro"=>$Erro, "usuario"=>$Proc0[0], "nomecompl"=>$Proc0[1], "usuarioAdm"=>$Proc[0], "setor"=>$Proc[1], "ativo"=>$Proc[2], "ultlog"=>$UltLog, "acessos"=>$Proc[4], "usuarioNome"=>$Proc[5], "cpf"=>$GuardaCpf);
    }
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaUsu"){
    $Usu = (int) filter_input(INPUT_GET, 'numero');
    $GuardaId = (int) filter_input(INPUT_GET, 'guardaidpessoa');
    $UsuLogado = (int) filter_input(INPUT_GET, 'usulogado');  // usuarioID
    $Setor = (int) filter_input(INPUT_GET, 'setor');
    $Adm = (int) filter_input(INPUT_GET, 'flAdm');
    $Cpf = filter_input(INPUT_GET, 'cpf');
    $Ativo = (int) filter_input(INPUT_GET, 'ativo');

    $NomeU = trim(filter_input(INPUT_GET, 'usuarioNome')); // vem de pessoas mas pode ser modificado aqui
    $NomeUs = GUtils::normalizarNome($NomeU);  // Normatizar nomes próprios
    $NomeUsu = addslashes($NomeUs);
    $NomeUsual = str_replace('"', "'", $NomeUsu); // substitui aspas duplas por simples

    $Cpf1 = addslashes($Cpf);
    $Cpf2 = str_replace(".", "", $Cpf1);
    $Cpf = str_replace("-", "", $Cpf2);

    $Erro = 0;
    $id = 0;
    $rs0 = pg_query($ConecPes, "SELECT nome_completo, dt_nascimento, sexo FROM ".$xPes.".pessoas WHERE cpf = '$Cpf'");
    $tbl0 = pg_fetch_row($rs0);

    $NomeC = GUtils::normalizarNome($tbl0[0]);  // Normatizar nomes próprios
    $NomeComp = addslashes($NomeC);
    $NomeCompl = str_replace('"', "'", $NomeComp); // substitui aspas duplas por simples

    $DNasc = $tbl0[1];
    $Sexo = $tbl0[2];
    if(is_null($Sexo)){
        $Sexo = 1;
    }

    if($Usu > 0){ 
        $rs = pg_query($Conec, "UPDATE ".$xProj.".daf_poslog SET codsetor = $Setor, adm = $Adm, ativo = $Ativo, usumodif = $UsuLogado, datamodif = NOW(), nomeusual = '$NomeUsual', nomecompl = '$NomeCompl' WHERE cpf = '$Cpf'"); 
        if($Ativo == 0){ // bloqueado
            pg_query($Conec, "UPDATE ".$xProj.".daf_poslog SET datainat = NOW() WHERE cpf = '$Cpf'"); // só marca a data da inatividade
        }else{
            pg_query($Conec, "UPDATE ".$xProj.".daf_poslog SET datainat = '3000-12-31' WHERE cpf = '$Cpf'");
        }
        if(!$rs){
            $Erro = 1;
        }
    }
    if($Usu == 0){ // cadastrar
        if($GuardaId > 0){
            $m = strtotime("-1 Hour");
            $HoraAnt = date("Y-m-d H:i:s", $m); // para o recem cadastrado não aparecer on line

            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".daf_poslog");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1);
            $Senha = password_hash($Cpf, PASSWORD_DEFAULT);
            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".daf_poslog (id, pessoas_id, codsetor, adm, usuins, datains, cpf, nomecompl, senha, ativo, logini, logfim, datamodif, datainat, nomeusual) 
            VALUES ($CodigoNovo, $GuardaId, $Setor, $Adm, $UsuLogado, NOW(), '$Cpf', '$NomeCompl', '$Senha', 1, '3000-12-31', '$HoraAnt', '3000-12-31', '3000-12-31', '$NomeUsual' )"); // logfim conta tempo para apagar usuário (5 anos)

            //Para que o usu possa ver o atalho para este programa em Ferramentas 
            $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET verarqdaf = 1 WHERE pessoas_id = $GuardaId");

            if(!$rs){
                $Erro = 12;
            }
        }else{
            $Erro = 13;
        }
    }
    $var = array("coderro"=>$Erro, "usuario"=>$Usu, "guardausu"=>$GuardaId);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="checaLogin"){
    $Cpf0 = filter_input(INPUT_GET, 'valor');
    $Cpf1 = addslashes($Cpf0);
    $Cpf2 = str_replace(".", "", $Cpf1);
    $Cpf = str_replace("-", "", $Cpf2);
    
    $Erro = 0;
    $row = 0;
    $NomeUsual = "";
    $NomeCompl = "";
    $DiaNasc = 0;
    $MesNasc = 0;
    $UltLog = "";
    $Acessos = 0;
    $Ativo = 1;
    $Adm = 1;
    $Setor = 0;
    $JaTem = 0;
    $Usu = 0;

    $rs = pg_query($ConecPes, "SELECT id, nome_completo, TO_CHAR(dt_nascimento, 'DD/MM/YYYY'), TO_CHAR(dt_nascimento, 'DD'), TO_CHAR(dt_nascimento, 'MM'), nome_resumido 
    FROM ".$xPes.".pessoas 
    WHERE ".$xPes.".pessoas.cpf = '$Cpf' ");
    if(!$rs){
        $Erro = 1;
    }else{
        $row = pg_num_rows($rs);
        if($row > 0){
            $Proc= pg_fetch_row($rs);
            $Usu = $Proc[0];
            $NomeCompl = $Proc[1];
            $DiaNasc = $Proc[3];
            $MesNasc = $Proc[4];
            $NomeCompl = $Proc[1];
            $NomeUsual = $Proc[5];

            $rs1 = pg_query($Conec, "SELECT to_char(logini, 'DD/MM/YYYY HH24:MI'), numacessos, ativo, adm, codsetor, pessoas_id, nomeusual 
            FROM ".$xProj.".daf_poslog WHERE cpf = '$Cpf' ");  //pessoas_id = $Usu ");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                $JaTem = 1;
                $Proc1= pg_fetch_row($rs1);
                $UltLog = $Proc1[0];
                $Acessos = $Proc1[1];
                $Ativo = $Proc1[2];
                $Adm = $Proc1[3];
                $Setor = $Proc1[4];
            }
        }else{
            $Erro = 2; // não encontrado no pessoal
        }
    }
    $var = array("coderro"=>$Erro, "quantiUsu"=>$row, "idpessoa"=>$Usu, "cpf"=>$Cpf, "nomeusual"=>$NomeUsual, "nomecompl"=>$NomeCompl, "dianasc"=>$DiaNasc, "mesnasc"=>$MesNasc, "jatem"=>$JaTem, "ultlog"=>$UltLog, "acessos"=>$Acessos, "ativo"=>$Ativo, "adm"=>$Adm, "setor"=>$Setor);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="checaLro"){  // Sem uso
    $Param = (int) filter_input(INPUT_GET, 'param'); 
    $Cpf = filter_input(INPUT_GET, 'numero'); 
    $Cpf1 = addslashes($Cpf);
    $Cpf2 = str_replace(".", "", $Cpf1);
    $Cpf = str_replace("-", "", $Cpf2);
    $Erro = 0;
    $rs0 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET lro = $Param WHERE cpf = '$Cpf' ");
    if(!$rs0){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="checaBens"){  // Sem uso
    $Param = (int) filter_input(INPUT_GET, 'param'); 
    $Cpf = filter_input(INPUT_GET, 'numero'); 
    $Cpf1 = addslashes($Cpf);
    $Cpf2 = str_replace(".", "", $Cpf1);
    $Cpf = str_replace("-", "", $Cpf2);
    $Erro = 0;
    $rs0 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET bens = $Param WHERE cpf = '$Cpf' ");
    if(!$rs0){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="resetsenha"){
    $Cpf = filter_input(INPUT_GET, 'numero'); 
    $Cpf1 = addslashes($Cpf);
    $Cpf2 = str_replace(".", "", $Cpf1);
    $Cpf = str_replace("-", "", $Cpf2);
    $Erro = 0;
    $Senha = password_hash($Cpf, PASSWORD_DEFAULT);

    $rs = pg_query($Conec, "UPDATE ".$xProj.".daf_poslog SET senha = '$Senha', extsen = 0 WHERE cpf = '$Cpf'");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="deletausu"){
    $Usu = (int) filter_input(INPUT_GET, 'numero'); 
    $Erro = 0;
//    $rs = pg_query($Conec, "DELETE FROM ".$xProj.".poslog WHERE id = $Usu");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="confsenhaant"){
    $Cpf = $_SESSION["usuarioCPF"];
    $Valor = filter_input(INPUT_GET, 'valor');
    $Valor = removeInj($Valor);
    $Erro = 0;
    $Achou = 0;

    $rs = pg_query($Conec, "SELECT senha FROM ".$xProj.".daf_poslog WHERE cpf = '$Cpf'");
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        if(password_verify($Valor, $tbl[0])){
            $Achou = 1;
        }
    }else{
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "row"=>$row, "confere"=> $Achou, "CPF"=>$_SESSION["usuarioCPF"]);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="trocasenha"){
    $Cpf = $_SESSION["usuarioCPF"];
    $Sen = filter_input(INPUT_GET, 'novasenha');
    $Repet = filter_input(INPUT_GET, 'repetsenha');
  
    $Erro = 0;
    if($Sen != $Repet){
        $Erro = 1;
    }
    if($Sen == "1234567890" || $Sen == "123456789" || $Sen == "12345678" || $Sen == "1234567" || $Sen == "123456" || $Sen == "0987654321" || $Sen == "987654321" || $Sen == "87654321" || $Sen == "7654321" || $Sen == "654321"){
        $Erro = 2;
    }
    if(strlen($Sen) < 6){
        $Erro = 5;
    }
    if($Sen == $Cpf){
        $Erro = 3;
    }
    if($Erro == 0){
        $Senha = removeInj($Sen);
        $Sen = password_hash($Senha, PASSWORD_DEFAULT);
        $rs = pg_query($Conec, "UPDATE ".$xProj.".daf_poslog SET senha = '$Sen', extsen = 0, datamodif = NOW() WHERE cpf = '$Cpf'");
        if(!$rs){
            $Erro = 4;
        }
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="mudasenha"){
    $Usu = arqDafAdm("pessoas_id", $Conec, $xProj, $_SESSION["usuarioCPF"]);
    $Cpf = $_SESSION["usuarioCPF"];
    $SenAnt = filter_input(INPUT_GET, 'senhaant'); // já foi checada no preenchimento
    $Sen = filter_input(INPUT_GET, 'novasenha');
    $Repet = filter_input(INPUT_GET, 'repetsenha');
    $Erro = 0;
    $Busca = str_split($Sen, 1);
    $Seq = $Busca[0];

    if($Sen != $Repet){
        $Erro = 1;
    }
    if($Sen == "1234567890" || $Sen == "123456789" || $Sen == "12345678" || $Sen == "1234567" || $Sen == "123456" || $Sen == "0987654321" || $Sen == "987654321" || $Sen == "87654321" || $Sen == "7654321" || $Sen == "654321"){
        $Erro = 2;
    }
    if(strlen($Sen) < 6){
        $Erro = 5;
    }
    if($Sen == "" || is_null($Sen)){
        $Erro = 3;
    }
    if($Erro == 0){
        $SenhaR = removeInj($Sen);
        $Senha = password_hash($SenhaR, PASSWORD_DEFAULT);
        $rs = pg_query($Conec, "UPDATE ".$xProj.".daf_poslog SET senha = '$Senha', usumodif = $Usu, datamodif = NOW() WHERE cpf = '$Cpf'");
        if(!$rs){
            $Erro = 4;
        }
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="salvaAdm"){
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    $Caixa = filter_input(INPUT_GET, 'caixa');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET $Caixa = $Valor WHERE idPar = 1"); // nome da coluna é o nome da variável
    if(!$rs){
        $Erro = 1;
    }
    $_SESSION[$Caixa] = $Valor;

    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaParam"){
    $Campo = filter_input(INPUT_GET, 'param');
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET $Campo = $Valor WHERE idPar = 1");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="valorleituraAgua"){
    $Val = filter_input(INPUT_GET, 'valor');
    $Valor = str_replace(",", ".", $Val);
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET valoriniagua = $Valor WHERE idPar = 1");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="dataleituraAgua"){
    $PegaData = addslashes(filter_input(INPUT_GET, 'valor')); 
    $PegaDia = implode("-", array_reverse(explode("/", $PegaData))); // date('d/m/Y', strtotime("+ 1 days", strtotime($DataI)));
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET datainiagua = '$PegaDia' WHERE idPar = 1");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="valorleituraEletric"){
    $Num = (int) filter_input(INPUT_GET, 'numero'); 
    $Val = filter_input(INPUT_GET, 'valor');
    $Valor = str_replace(",", ".", $Val);
    $Erro = 0;
    if($Num == 1){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET valorinieletric = $Valor WHERE idPar = 1");
    }
    if($Num == 2){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET valorinieletric2 = $Valor WHERE idPar = 1");
    }
    if($Num == 3){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET valorinieletric3 = $Valor WHERE idPar = 1");
    }

    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "valor"=>$Valor);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="dataleituraEletric"){
    $Num = (int) filter_input(INPUT_GET, 'numero'); 
    $PegaData = addslashes(filter_input(INPUT_GET, 'valor')); 
    $PegaDia = implode("-", array_reverse(explode("/", $PegaData))); // date('d/m/Y', strtotime("+ 1 days", strtotime($DataI)));
    $Erro = 0;
    if($Num == 1){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET datainieletric = '$PegaDia' WHERE idPar = 1");
    }
    if($Num == 2){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET datainieletric2 = '$PegaDia' WHERE idPar = 1");
    }
    if($Num == 3){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET datainieletric3 = '$PegaDia' WHERE idPar = 1");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="buscadir"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT siglasetor, descsetor, ativo FROM ".$xProj.".setores WHERE codset = $Cod");
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        
    }else{
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "sigla"=>$tbl[0], "desc"=> $tbl[1], "ativo"=> $tbl[2]);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvadir"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Sigla = filter_input(INPUT_GET, 'sigladir');
    $Desc = filter_input(INPUT_GET, 'descdir');
    $Ativo = (int) filter_input(INPUT_GET, 'ativo');
    $Erro = 0;
    if($Cod == 0){
        $AdDir = substr($Desc, 0, 3);
        if($AdDir == "Dir"){
            $Menu = 1;
        }else{
            $Menu = 2;
        }
        $rsCod = pg_query($Conec, "SELECT MAX(codset) FROM ".$xProj.".setores");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1); 
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".setores (codset, siglasetor, descsetor, menu, ativo, cabec1, cabec2, textopag) VALUES ($CodigoNovo, '$Sigla', '$Desc', $Menu, 1, 'COMUNHÃO ESPÍRITA DE BRASÍLIA', '$Desc', '&lt;p&gt;Página Exclusiva&lt;/p&gt;' )");
    }else{
        $rs = pg_query($Conec, "UPDATE ".$xProj.".setores SET siglasetor = '$Sigla', descsetor = '$Desc', ativo = $Ativo WHERE codset = $Cod");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaAtivDir"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Valor = filter_input(INPUT_GET, 'valor');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".setores SET ativo = $Valor WHERE codset = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="buscackList"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT itemnum, itemverif, ativo FROM ".$xProj.".livrocheck WHERE id = $Cod");
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        
    }else{
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "itemnum"=>$tbl[0], "itemcklist"=>$tbl[1], "ativo"=> $tbl[2]);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="buscanumckList"){
    $Erro = 0;
    $ProxItem = 0;
    $rs = pg_query($Conec, "SELECT MAX(itemnum) FROM ".$xProj.".livrocheck WHERE ativo = 1");
    if(!$rs){
        $Erro = 1;
    }else{
        $tbl = pg_fetch_row($rs);
        $Item = $tbl[0];
        $ProxItem = $Item+1;
    }
    $var = array("coderro"=>$Erro, "proxitem"=>$ProxItem);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaCkList"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $NumItem = filter_input(INPUT_GET, 'numitem');
    $DescItem = filter_input(INPUT_GET, 'descitem');
    $Ativo = (int) filter_input(INPUT_GET, 'ativo');
    $UsuIns = $_SESSION['usuarioID'];
    $Erro = 0;
    if($Cod == 0){
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".livrocheck");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1); 
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".livrocheck (id, itemnum, itemverif, ativo, usuins, datains) 
        VALUES ($CodigoNovo, $NumItem, '$DescItem', $Ativo, $UsuIns, NOW() )");
    }else{
        $rs = pg_query($Conec, "UPDATE ".$xProj.".livrocheck SET itemnum = $NumItem, itemverif = '$DescItem', ativo = $Ativo WHERE id = $Cod");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="checaLogFim"){
    $Erro = 0;
    //atualiza a cada minuto para verificar usuário on line
    $rs = pg_query($Conec, "UPDATE ".$xProj.".daf_poslog SET logfim = NOW() WHERE pessoas_id = ".$_SESSION["usuarioID"]."");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="buscaMenuOpr"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $Valor = "";
    $rs = pg_query($Conec, "SELECT descr FROM ".$xProj.".cesbmenu WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }else{
        $tbl=pg_fetch_row($rs);
        $Valor = $tbl[0];
    }
    $var = array("coderro"=>$Erro, "valor"=>$Valor);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvamenuOpr"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Valor = filter_input(INPUT_GET, 'valor');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".cesbmenu SET descr = '$Valor' WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="logbuscaTamsen"){ // pega o tamanho da senha para entrar ao acabar de digitá-la
    $Cpf = filter_input(INPUT_GET, 'usuario'); 
    $Cpf1 = addslashes($Cpf);
    $Cpf2 = str_replace(".", "", $Cpf1);
    $Usu = str_replace("-", "", $Cpf2);
    $Erro = 0;
    $Tam = 0;
    $rs = pg_query($Conec, "SELECT extsen FROM ".$xProj.".poslog WHERE cpf = '$Usu' And ativo = 1");
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        $Tam = $tbl[0];
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "tamanho"=>$Tam);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="salvaTema"){
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET tema = $Valor WHERE pessoas_id = ".$_SESSION["usuarioID"]." And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "valor"=>$Valor);
    $responseText = json_encode($var);
    echo $responseText;
}


function removeInj($VemDePost){  // função para remover injeções SQL
    $VemDePost = addslashes($VemDePost);
    $VemDePost = htmlspecialchars($VemDePost);
    $VemDePost = strip_tags($VemDePost);
    $VemDePost = str_replace("SELECT","",$VemDePost);
    $VemDePost = str_replace("FROM","",$VemDePost);
    $VemDePost = str_replace("WHERE","",$VemDePost);
    $VemDePost = str_replace("INSERT","",$VemDePost);
    $VemDePost = str_replace("UPDATE","",$VemDePost);
    $VemDePost = str_replace("DELETE","",$VemDePost);
    $VemDePost = str_replace("DROP","",$VemDePost);
    $VemDePost = str_replace("DATABASE","",$VemDePost);
    return $VemDePost; 
 }

 function Navegador(){
    $MSIE = strpos($_SERVER['HTTP_USER_AGENT'],"MSIE");
    $Firefox = strpos($_SERVER['HTTP_USER_AGENT'],"Firefox");
    $Mozilla = strpos($_SERVER['HTTP_USER_AGENT'],"Mozilla");
    $Chrome = strpos($_SERVER['HTTP_USER_AGENT'],"Chrome");
    $Chromium = strpos($_SERVER['HTTP_USER_AGENT'],"Chromium");
    $Safari = strpos($_SERVER['HTTP_USER_AGENT'],"Safari");
    $Opera = strpos($_SERVER['HTTP_USER_AGENT'],"Opera");

    if($MSIE == true){
        $navegador = "IE"; 
    }else if($Firefox == true){
        $navegador = "Firefox"; 
    }else if($Mozilla == true){
        $navegador = "Firefox"; 
    }else if($Chrome == true){
        $navegador = "Chrome"; 
    }else if($Chromium == true){
        $navegador = "Chromium"; 
    }else if($Safari == true){ 
        $navegador = "Safari"; 
    }else if($Opera == true){
        $navegador = "Opera"; 
    }else{
        $navegador = $_SERVER['HTTP_USER_AGENT']; 
    }
    return $navegador;
}
