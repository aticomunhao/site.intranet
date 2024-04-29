<?php
session_start(); // inicia uma sessão
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    require_once("abrealas.php");
    $Conec = conecPost(); // habilitar a extensão: extension=pgsql no phpini
    $ConecPes = conecPes();

//    $ConecPes = "sConec";
    if($ConecPes == "sConec" || $ConecPes == "sFunc"){
        $ConecPes = $Conec;
        $xPes = $xProj;
    }
}

if($Acao =="loglog"){
    $Usu = filter_input(INPUT_GET, 'usuario'); 
    $Sen = filter_input(INPUT_GET, 'senha');
    $Erro = 0;
    $Erro_Msg = "";
    $Login = removeInj($Usu);
    $Sen = removeInj($Sen);
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

        $rs0 = pg_query($ConecPes, "SELECT nome_completo, sexo, cpf, dt_nascimento FROM ".$xPes.".pessoas WHERE cpf = '$Login' ");
        $row0 = pg_num_rows($rs0); 
        if($row0 > 0){ // está no arquivo pessoas
            $rs1 = pg_query($Conec, "SELECT senha FROM ".$xProj.".poslog WHERE cpf = '$Login' And ativo = 1");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){ // está no arquivo poslog
                $tbl1 = pg_fetch_row($rs1);
            
                if(password_verify($Sen, $tbl1[0])){
                    $tbl0 = pg_fetch_row($rs0);
                    $rs = pg_query($ConecPes, "SELECT ".$xPes.".pessoas.id, ".$xPes.".pessoas.cpf, ".$xPes.".pessoas.nome_completo, TO_CHAR(".$xPes.".pessoas.dt_nascimento, 'DD/MM/YYYY'), TO_CHAR(".$xPes.".pessoas.dt_nascimento, 'DD'), TO_CHAR(".$xPes.".pessoas.dt_nascimento, 'MM'), sexo 
                    FROM ".$xPes.".pessoas  
                    WHERE ".$xPes.".pessoas.cpf = '$Login' ");  //And status = 1");
                    $Sql = pg_fetch_row($rs);
                    $id = $Sql[0];
                    $NomeCompl = $Sql[2];
                    $Nome = substr($NomeCompl, 0, 30); // para o campo nome em mysql - ver se tem no postgre
                    $DNasc = $Sql[3];
                    $DiaAniv = $Sql[4];
                    $MesAniv = $Sql[5];
                    $_SESSION['usuarioCPF'] = $Sql[1];
                    $_SESSION['sexo'] = $Sql[6];
                    $_SESSION['start_login'] = time();
                    $_SESSION["UsuLogado"] = "";
                    $_SESSION["usuarioID"] = $id;
                    $_SESSION["UsuLogado"] =  $NomeCompl;
                    $_SESSION["NomeCompl"] =  $NomeCompl;
                    $_SESSION["msg"] = ""; //para troca de slides e tráfego de arquivos
                    $_SESSION['msgarq'] = ""; //para upload arquivos diretorias/assessorias
                    $_SESSION['geremsg'] = 0;
                    $_SESSION['gerenum'] = 0;
                    $_SESSION['arquivo'] = "";
                    $_SESSION["CodSetorUsu"] = 0 ;
                    $_SESSION["SiglaSetor"] = "n/d";
                    $_SESSION["CodSubSetorUsu"] = 1; // não tem subdiretorias - deixar 1
                    $_SESSION["AdmUsu"] = 2;
                    //Parâmetros do sistema
                    $rsSis = pg_query($Conec, "SELECT admVisu, admCad, admEdit FROM ".$xProj.".paramsis WHERE idPar = 1");
                    $ProcSis = pg_fetch_row($rsSis);
                    $_SESSION["AdmVisu"] = $ProcSis[0];  // administrador visualiza usuários
                    $_SESSION["AdmCad"] = $ProcSis[1];   // administrador cadastra usuários
                    $_SESSION["AdmEdit"] = $ProcSis[2];  // administrador edita usuários

                    //Verifica se já logou uma vez em poslog
                    $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE cpf = '$Login' "); 
                    $row1 = pg_num_rows($rs1);
                    if($row1 > 0){ // Já tem - aumenta número de acessos e grava data hora do login 
                        pg_query($Conec, "UPDATE ".$xProj.".poslog SET numacessos = (numacessos + 1), logini = NOW() WHERE pessoas_id = $id "); 
                        $rs2 = pg_query($Conec, "SELECT adm, codsetor FROM ".$xProj.".poslog WHERE cpf = '$Login' "); 
                        $tbl2 = pg_fetch_row($rs2);
                        $_SESSION["AdmUsu"] = $tbl2[0];
                        $_SESSION["CodSetorUsu"] = $tbl2[1];
                        $rs3 = pg_query($Conec, "SELECT siglasetor, descsetor FROM ".$xProj.".setores WHERE codset = $tbl2[1] "); 
                        $row3 = pg_num_rows($rs3);
                        if($row3 > 0){
                            $tbl3 = pg_fetch_row($rs3);
                            $_SESSION["SiglaSetor"] = $tbl3[0];
                        }
                    }else{ // se não houver, acrescenta
                        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".poslog");
                        $tblCod = pg_fetch_row($rsCod);
                        $Codigo = $tblCod[0];
                        $CodigoNovo = ($Codigo+1); 
                        pg_query($Conec, "INSERT INTO ".$xProj.".poslog (id, pessoas_id, logini, numacessos, cpf, nomecompl)  VALUES ($CodigoNovo, $id, NOW(), 1, '$Login', '$NomeCompl') "); 
                    }
//                    $rs4 = pg_query($ConecPes, "SELECT id FROM ".$xPes.".pessoas WHERE cpf = '$Login' "); 
                    $rs4 = pg_query($Conec, "SELECT id FROM ".$xProj.".pessoas WHERE cpf = '$Login' "); 
                    $row4 = pg_num_rows($rs4);
                    if($row4 == 0){
                        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".pessoas");
                        $tblCod = pg_fetch_row($rsCod);
                        $Codigo = $tblCod[0];
                        $CodigoNovo = ($Codigo+1); 
                        pg_query($Conec, "INSERT INTO ".$xProj.".pessoas (id, pessoas_id, cpf, nome_completo, dt_nascimento, sexo, status, datains) VALUES ($CodigoNovo, $id, '$Login', '$NomeCompl', '$DNasc', ".$_SESSION['sexo'].", 1, NOW() ) "); 
                    }

                    if($_SESSION["AdmUsu"] == 0){
                        $Erro = 4;
                        $Erro_Msg = "Usuário ainda não recebeu permissão administrativa.";
                    }

                    if($Sen == $Login){
                        $Erro = 5; // inserir nova senha
                    }
                    $var = array("coderro"=>$Erro, "msg"=>$Erro_Msg, "usuarioid"=>$id, "usuarioNome"=>$_SESSION["NomeCompl"], "usuarioAdm"=>$_SESSION["AdmUsu"], "usuario"=>$_SESSION["UsuLogado"]); 
                }else{ // usuário não encontrado 
                    $Erro = 6;
                    $Erro_Msg = "Usuário ou senha não conferem.";
                    $var = array("coderro"=>$Erro, "msg"=>$Erro_Msg);
                    $responseText = json_encode($var);
                    echo $responseText;
                    return;
                }
            }else{ // não está no poslog
                $Erro = 6;
                $Erro_Msg = "Usuário e/ou senha não encontrados.";
                $rs5 = pg_query($Conec, "SELECT ativo FROM ".$xProj.".poslog WHERE cpf = '$Login'");
                $row5 = pg_num_rows($rs5);
                if($row5 > 0){
                    $tbl5 = pg_fetch_row($rs5);
                    if($tbl5[0] == 0){
                        $Erro = 4;
                        $Erro_Msg = "Usuário bloqueado. Informe à ATI.";
                    }
                }
                $var = array("coderro"=>$Erro, "msg"=>$Erro_Msg);
            }
        }else{ // não está em pessoas
            $Erro = 6;
            $Erro_Msg = "Usuário ou senha não cadastrados.";
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

if($Acao =="buscaacesso"){
    $Cpf = $_SESSION["usuarioCPF"];
    $Erro = 0;
    $NumAcessos = 0;
    $msg = "";
    $Marca = 0;
    $rsAc = pg_query($Conec, "SELECT numacessos FROM ".$xProj.".poslog WHERE cpf = '$Cpf'");
    if(!$rsAc){
        $Erro = 1;
    }else{
        $ProcAc = pg_fetch_row($rsAc);
        $NumAcessos = $ProcAc[0];
        $msg = "Este é seu acesso nº $NumAcessos";
        if($NumAcessos < 500){ // abaixo de 500
            if($NumAcessos % 100 === 0){ // a cada 100 acessos vai aparecer a caixa comemorativa
                pg_query($Conec, "UPDATE ".$xProj.".poslog SET NumAcessos = (NumAcessos + 1) WHERE cpf = '$Cpf'"); // soma 1 para evitar continuar a comemoração no mesmo login
                $Marca = 1;
            }
        }else{ //se for acima de 500
            if($NumAcessos % 500 === 0){
                pg_query($Conec, "UPDATE ".$xProj.".poslog SET NumAcessos = (NumAcessos + 1) WHERE cpf = '$Cpf'"); // soma 1 para evitar continuar a comemoração no mesmo login
                $Marca = 1;
            }
        }
        $rsTar = pg_query($Conec, "SELECT idtar, sit FROM ".$xProj.".tarefas WHERE usuexec = '".$_SESSION["usuarioID"]."' And sit = 1");
        $rowTar = pg_num_rows($rsTar);
        if($rowTar > 0){
            if($rowTar == 1){
                $msgTar = "Tarefa expedida para ". $_SESSION["NomeCompl"].".<br> Clique em <u style='cursor: pointer;'>Tarefas</u> para verificar.";
            }else{
                $msgTar= $rowTar." tarefas expedidas para ". $_SESSION["NomeCompl"].".<br> Clique em <u style='cursor: pointer;'>Tarefas</u> para verificar.";
            }
        }else{
            $msgTar = "";
        }
    }

    $var = array("coderro"=>$Erro, "marca"=>$Marca, "acessos"=>$NumAcessos, "msg"=>$msg, "temTarefa"=>$rowTar, "msgTar"=>$msgTar);
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

    $rs0 = pg_query($ConecPes, "SELECT ".$xPes.".pessoas.cpf,".$xPes.".pessoas.nome_completo, to_char(dt_nascimento, 'DD'), TO_CHAR(dt_nascimento, 'MM') FROM ".$xPes.".pessoas WHERE cpf = '$GuardaCpf' ");
    $rs = pg_query($Conec, "SELECT adm, codsetor, ativo, to_char(logini, 'DD/MM/YYYY HH24:MI'), numacessos FROM ".$xProj.".poslog WHERE cpf = '$GuardaCpf' ");  //pessoas_id = $Usu ");
    $row = pg_num_rows($rs);
    if($row == 0){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $Proc = pg_fetch_row($rs);
        if(!is_null($Proc[3])){
            $UltLog = $Proc[3];
        }else{
            $UltLog = "31/12/3000";
        }
        $Proc0 = pg_fetch_row($rs0);    
        $var = array("coderro"=>$Erro, "usuario"=>$Proc0[0], "usuarioNome"=>$Proc0[1], "nomecompl"=>$Proc0[1], "usuarioAdm"=>$Proc[0], "setor"=>$Proc[1], "ativo"=>$Proc[2], "ultlog"=>$Proc[3], "acessos"=>$Proc[4], "diaAniv"=>$Proc0[2], "mesAniv"=>$Proc0[3], "cpf"=>$GuardaCpf);
    }
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

    $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET senha = '$Senha' WHERE cpf = '$Cpf'");
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

if($Acao =="salvaUsu"){
    $Usu = (int) filter_input(INPUT_GET, 'numero');
    $GuardaId = (int) filter_input(INPUT_GET, 'guardaidpessoa');
    $UsuLogado = (int) filter_input(INPUT_GET, 'usulogado');
    $Setor = (int) filter_input(INPUT_GET, 'setor');
    $Adm = (int) filter_input(INPUT_GET, 'flAdm');
    $Cpf = filter_input(INPUT_GET, 'cpf');
    $Ativo = (int) filter_input(INPUT_GET, 'ativo');
    $NomeCompl = filter_input(INPUT_GET, 'nomecompl');

    $Cpf1 = addslashes($Cpf);
    $Cpf2 = str_replace(".", "", $Cpf1);
    $Cpf = str_replace("-", "", $Cpf2);

    $Erro = 0;
    $id = 0;
    $rs0 = pg_query($ConecPes, "SELECT nome_completo, dt_nascimento, sexo FROM ".$xPes.".pessoas WHERE cpf = '$Cpf'");
    $tbl0 = pg_fetch_row($rs0);
    $NomeCompl = $tbl0[0];
    $DNasc = $tbl0[1];
    $Sexo = $tbl0[2];
    if(is_null($Sexo)){
        $Sexo = 1;
    }

    if($Usu > 0){  // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET codsetor = $Setor, adm = $Adm, ativo = $Ativo, usumodif = $UsuLogado, datamodif = NOW(), nomecompl = '$NomeCompl' WHERE cpf = '$Cpf'");  // pessoas_id = $Usu");
        pg_query($Conec, "UPDATE ".$xProj.".pessoas SET pessoas_id = $Usu, nome_completo = '$NomeCompl', sexo = $Sexo, status = $Ativo WHERE cpf = '$Cpf' "); //coleção

        if(!is_null($DNasc)){
            pg_query($Conec, "UPDATE ".$xProj.".pessoas SET dt_nascimento = '$DNasc' WHERE cpf = '$Cpf' "); 
        }
        if(!$rs){
            $Erro = 1;
        }
    }
    if($Usu == 0){ // cadastrar
        if($GuardaId > 0){
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".poslog");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1);
            $Senha = password_hash($Cpf, PASSWORD_DEFAULT);

            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".poslog (id, pessoas_id, codsetor, adm, usuins, datains, cpf, nomecompl, senha, ativo) 
            VALUES ($CodigoNovo, $GuardaId, $Setor, $Adm, $UsuLogado, NOW(), '$Cpf', '$NomeCompl', '$Senha', 1 )");
            if(!$rs){
                $Erro = 12;
            }
        }else{
            $Erro = 13;
        }
        $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".pessoas WHERE cpf = '$Cpf'");
        $row1 = pg_num_rows($rs1);
        if($row1 == 0){
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".pessoas");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1); 
            pg_query($Conec, "INSERT INTO ".$xProj.".pessoas (id, pessoas_id, cpf, nome_completo, dt_nascimento, sexo, status, datains) VALUES ($CodigoNovo, $GuardaId, '$Cpf', '$NomeCompl', '$DNasc', $Sexo, $Ativo, NOW() ) "); 
        }
    }
    $var = array("coderro"=>$Erro, "usuario"=>$Usu, "guardausu"=>$GuardaId);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="checaLogin"){
    $Cpf = filter_input(INPUT_GET, 'valor');
    $Erro = 0;
    $row = 0;
    $NomeCompl = "";
    $NomeCompl = "";
    $DiaNasc = 0;
    $MesNasc = 0;
    $UltLog = "";
    $Acessos = 0;
    $Ativo = 0;
    $Adm = 1;
    $Setor = 0;
    $JaTem = 0;
    $Usu = 0;

    $rs = pg_query($ConecPes, "SELECT ".$xPes.".pessoas.id, ".$xPes.".pessoas.nome_completo, TO_CHAR(".$xPes.".pessoas.dt_nascimento, 'DD/MM/YYYY'), TO_CHAR(".$xPes.".pessoas.dt_nascimento, 'DD'), TO_CHAR(".$xPes.".pessoas.dt_nascimento, 'MM') 
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

            $rs1 = pg_query($Conec, "SELECT to_char(".$xProj.".poslog.logini, 'DD/MM/YYYY HH24:MI'), ".$xProj.".poslog.numacessos, ".$xProj.".poslog.ativo, ".$xProj.".poslog.adm, ".$xProj.".poslog.codsetor, ".$xProj.".poslog.pessoas_id 
            FROM ".$xProj.".poslog WHERE cpf = '$Cpf' ");  //pessoas_id = $Usu ");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                $JaTem = 1;
                $Proc1= pg_fetch_row($rs1);
                $UltLog = $Proc1[0];
                $Acessos = $Proc1[1];
                $Ativo = $Proc1[2];
                $Adm = $Proc1[3];
                $Setor = $Proc1[4];
//                $Usu = $Proc1[5];
            }
        }else{
            $Erro = 2; // não encontrado no pessoal
        }
    }
    $var = array("coderro"=>$Erro, "quantiUsu"=>$row, "idpessoa"=>$Usu, "cpf"=>$Cpf, "nomecompl"=>$NomeCompl, "dianasc"=>$DiaNasc, "mesnasc"=>$MesNasc, "jatem"=>$JaTem, "ultlog"=>$UltLog, "acessos"=>$Acessos, "ativo"=>$Ativo, "adm"=>$Adm, "setor"=>$Setor);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="confsenhaant"){
    $Cpf = $_SESSION["usuarioCPF"];
    $Valor = filter_input(INPUT_GET, 'valor');
    $Valor = removeInj($Valor);
//    $Sen = password_hash($Valor, PASSWORD_DEFAULT);
    $Erro = 0;
    $Achou = 0;

    $rs = pg_query($Conec, "SELECT senha FROM ".$xProj.".poslog WHERE cpf = '$Cpf'");
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
//    $Usu = $_SESSION["usuarioID"];
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
        $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET senha = '$Sen', dataModif = NOW() WHERE cpf = '$Cpf'");
        if(!$rs){
            $Erro = 4;
        }
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="mudasenha"){
    $Usu = $_SESSION["usuarioID"];
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
        $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET senha = '$Senha', usuModif = $Usu, dataModif = NOW() WHERE cpf = '$Cpf'");
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
if($Acao =="valorleitura"){
    $Val = filter_input(INPUT_GET, 'valor');
    $Valor = str_replace(",", ".", $Val);
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET valorinileitura = $Valor WHERE idPar = 1");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="dataleitura"){
    $PegaData = addslashes(filter_input(INPUT_GET, 'valor')); 
    $PegaDia = implode("-", array_reverse(explode("/", $PegaData))); // date('d/m/Y', strtotime("+ 1 days", strtotime($DataI)));
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET datainileitura = '$PegaDia' WHERE idPar = 1");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
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