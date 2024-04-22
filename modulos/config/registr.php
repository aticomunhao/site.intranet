<?php
session_start(); // inicia uma sessão

if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    require_once("abrealas.php");
    $Conec = conecPost(); // habilitar a extensão: extension=pgsql no phpini
    $ConecPes = conecPes();
}

if($Acao =="loglog"){
    $Usu = filter_input(INPUT_GET, 'usuario'); 
    $Sen = filter_input(INPUT_GET, 'senha');
    $Erro = 0;
    $Erro_Msg = "";
    $Usuario = removeInj($Usu);
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

        //Tem que estar na tabela funcionarios. cpf -> tabela pessoas, senha -> tabela usuario
        $rs0 = pg_query($ConecPes, "SELECT ".$xPes.".usuario.hash_senha 
        FROM ".$xPes.".pessoas INNER JOIN ".$xPes.".usuario ON ".$xPes.".pessoas.id = ".$xPes.".usuario.id_pessoa 
        WHERE ".$xPes.".pessoas.cpf = '$Usuario' ");
        $row0 = pg_num_rows($rs0); 
        if($row0 > 0){
            $tbl0 = pg_fetch_row($rs0);
            if(password_verify($Sen, $tbl0[0])){
                $rs = pg_query($ConecPes, "SELECT ".$xPes.".pessoas.id, ".$xPes.".pessoas.cpf, ".$xPes.".pessoas.nome_completo, TO_CHAR(".$xPes.".pessoas.dt_nascimento, 'DD/MM/YYYY'), TO_CHAR(".$xPes.".pessoas.dt_nascimento, 'DD'), TO_CHAR(".$xPes.".pessoas.dt_nascimento, 'MM') 
                FROM ".$xPes.".pessoas INNER JOIN ".$xPes.".usuario ON ".$xPes.".pessoas.id = ".$xPes.".usuario.id_pessoa 
                WHERE ".$xPes.".pessoas.cpf = '$Usuario' ");
                $Sql = pg_fetch_row($rs);
                $id = $Sql[0];
                $NomeCompl = $Sql[2];
                $Nome = substr($NomeCompl, 0, 30); // para o campo nome em mysql - ver se tem no postgre
                $DNasc = $Sql[3];
                $DiaAniv = $Sql[4];
                $MesAniv = $Sql[5];

                $_SESSION['start_login'] = time();
//            $_SESSION['Conect'] = $Conec;
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

                $_SESSION["AdmUsu"] = 0;
                //Verifica se já logou uma vez em poslog
                $rs1 = pg_query($Conec, "SELECT pessoas_id FROM ".$xProj.".poslog WHERE pessoas_id = $id "); 
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){ // Já tem - aumenta número de acessos e grava data hora do login 
                    pg_query($Conec, "UPDATE ".$xProj.".poslog SET numacessos = (numacessos + 1), logini = NOW() WHERE pessoas_id = $id "); 
                    $rs2 = pg_query($Conec, "SELECT adm, codsetor FROM ".$xProj.".poslog WHERE pessoas_id = $id "); 
                    $tbl2 = pg_fetch_row($rs2);
                    $_SESSION["AdmUsu"] = $tbl2[0];
                    $_SESSION["CodSetorUsu"] = $tbl2[1];
//                    $rs3 = pg_query($ConecPes, "SELECT sigla, nome FROM ".$xPes.".setor WHERE id = $tbl2[1] "); 
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
                    pg_query($Conec, "INSERT INTO ".$xProj.".poslog (id, pessoas_id, logini, numacessos)  VALUES ($CodigoNovo, $id, NOW(), 1) "); 
                }

                //Parâmetros do sistema
                $rsSis = pg_query($Conec, "SELECT admVisu, admCad, admEdit FROM ".$xProj.".paramsis WHERE idPar = 1");
                $ProcSis = pg_fetch_row($rsSis);
                $_SESSION["AdmVisu"] = $ProcSis[0];  // administrador visualiza usuários
                $_SESSION["AdmCad"] = $ProcSis[1];   // administrador cadastra usuários
                $_SESSION["AdmEdit"] = $ProcSis[2];  // administrador edita usuários

//            $Sexo = 1; // ver se tem no postgre

                if($_SESSION["AdmUsu"] == 0){
                    $Erro = 4;
                    $Erro_Msg = "Usuário ainda não recebeu permissão administrativa.";
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
        }else{
            $Erro = 6;
            $Erro_Msg = "Usuário ou senha não conferem.";
            $var = array("coderro"=>$Erro, "msg"=>$Erro_Msg);
        }
    }else{ // sem contato com o postgre
        $Erro = 1;
        $Erro_Msg = "Usuário ou senha não conferem.";
        if($Conec == "sFunc"){
            $Erro_Msg = "Usuário ou senha não conferem. Conexão BD.";
        }
        $var = array("coderro"=>$Erro, "msg"=>$Erro_Msg);
    }

    $responseText = json_encode($var);
    echo $responseText;
}





if($Acao =="buscaacesso"){
    $Erro = 0;
    $NumAcessos = 0;
    $msg = "";
    $Marca = 0;
    $rsAc = pg_query($Conec, "SELECT NumAcessos FROM ".$xProj.".usuarios WHERE id = ".$_SESSION['usuarioID']);
    if(!$rsAc){
        $Erro = 1;
    }else{
        $ProcAc = pg_fetch_row($rsAc);
        $NumAcessos = $ProcAc[0];
        $msg = "Este é seu acesso nº $NumAcessos";
        if($NumAcessos < 500){ // abaixo de 500
            if($NumAcessos % 100 === 0){ // a cada 100 acessos vai aparecer a caixa comemorativa
                pg_query($Conec, "UPDATE ".$xProj.".usuarios SET NumAcessos = (NumAcessos + 1) WHERE id = ".$_SESSION['usuarioID']); // soma 1 para evitar continuar a comemoração no mesmo login
                $Marca = 1;
            }
        }else{ //se for acima de 500
            if($NumAcessos % 500 === 0){
                pg_query($Conec, "UPDATE ".$xProj.".usuarios SET NumAcessos = (NumAcessos + 1) WHERE id = ".$_SESSION['usuarioID']); // soma 1 para evitar continuar a comemoração no mesmo login
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
    $Erro = 0;
//    $rs = pg_query($Conec, "SELECT ".$xPes.".pessoas.cpf, ".$xPes.".pessoas.nome_completo, ".$xProj.".poslog.adm, ".$xProj.".poslog.codsetor, ".$xProj.".poslog.ativo, ".$xProj.".setores.siglasetor, to_char(".$xProj.".poslog.logini, 'DD/MM/YYYY HH24:MI'), ".$xProj.".poslog.numacessos, ".$xPes.".pessoas.usuario 
//    FROM ".$xProj.".setores INNER JOIN (".$xPes.".pessoas INNER JOIN ".$xProj.".poslog ON ".$xPes.".pessoas.id = ".$xProj.".poslog.pessoas_id) ON ".$xProj.".setores.codset = ".$xProj.".poslog.codsetor 
//    WHERE ".$xProj.".poslog.pessoas_id = $Usu ");

    $rs0 = pg_query($ConecPes, "SELECT ".$xPes.".pessoas.cpf, ".$xPes.".pessoas.nome_completo, to_char(dt_nascimento, 'DD'), TO_CHAR(dt_nascimento, 'MM') FROM ".$xPes.".pessoas WHERE id = $Usu ");
    $rs = pg_query($Conec, "SELECT ".$xProj.".poslog.adm, ".$xProj.".poslog.codsetor, ".$xProj.".poslog.ativo, to_char(".$xProj.".poslog.logini, 'DD/MM/YYYY HH24:MI'), ".$xProj.".poslog.numacessos 
    FROM ".$xProj.".poslog 
    WHERE ".$xProj.".poslog.pessoas_id = $Usu ");
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
        $var = array("coderro"=>$Erro, "usuario"=>$Proc0[0], "usuarioNome"=>$Proc0[1], "nomecompl"=>$Proc0[1], "usuarioAdm"=>$Proc[0], "setor"=>$Proc[1], "ativo"=>$Proc[2], "ultlog"=>$Proc[3], "acessos"=>$Proc[4], "diaAniv"=>$Proc0[2], "mesAniv"=>$UltLog);
    }
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="resetsenha"){
    $Usu = (int) filter_input(INPUT_GET, 'numero'); 
    $Erro = 0;
    $Senha = MD5("123456789");
//    $rs = pg_query($Conec, "UPDATE ".$xProj.".usuarios SET senha = '$Senha' WHERE id = $Usu");
//    if(!$rs){
//        $Erro = 1;
//    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="deletausu"){
    $Usu = (int) filter_input(INPUT_GET, 'numero'); 
    $Erro = 0;
    $rs = pg_query($Conec, "DELETE FROM ".$xProj.".usuarios WHERE id = $Usu");
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
    $Erro = 0;
    $id = 0;
    if($Usu > 0){  // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET codsetor = $Setor, adm = $Adm, usumodif = $UsuLogado, datamodif = NOW() WHERE pessoas_id = $Usu");
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
            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".poslog (id, pessoas_id, codsetor, adm, usuins, datains) 
            VALUES ($CodigoNovo, $GuardaId, $Setor, $Adm, $UsuLogado, NOW() )");
            if(!$rs){
                $Erro = 12;
            }
        }else{
            $Erro = 13;
        }
    }
    $var = array("coderro"=>$Erro, "usuario"=>$GuardaId);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="checaLogin"){
    $Valor = filter_input(INPUT_GET, 'valor');
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
    $rs = pg_query($ConecPes, "SELECT ".$xPes.".pessoas.id, ".$xPes.".pessoas.nome_completo, TO_CHAR(".$xPes.".pessoas.dt_nascimento, 'DD/MM/YYYY'), TO_CHAR(".$xPes.".pessoas.dt_nascimento, 'DD'), TO_CHAR(".$xPes.".pessoas.dt_nascimento, 'MM') 
    FROM ".$xPes.".pessoas 
    WHERE ".$xPes.".pessoas.cpf = '$Valor' ");
    if(!$rs){
        $Erro = 1;
    }else{
        $row = pg_num_rows($rs);
        if($row > 0){
            $Proc= pg_fetch_row($rs);
            $Usu = $Proc[0];
            $NomeCompl = $Proc[1];
            $DiaNasc = $Proc[2];
            $MesNasc = $Proc[3];

            $rs0 = pg_query($ConecPes, "SELECT id_pessoa FROM ".$xPes.".usuario WHERE id_pessoa = $Usu ");
            $row0 = pg_num_rows($rs0);
            if($row0 == 0){
                $Erro = 3; // não encontrado no usuario
            }

            $rs1 = pg_query($Conec, "SELECT to_char(".$xProj.".poslog.logini, 'DD/MM/YYYY HH24:MI'), ".$xProj.".poslog.numacessos, ".$xProj.".poslog.ativo, ".$xProj.".poslog.adm, ".$xProj.".poslog.codsetor 
            FROM ".$xProj.".poslog WHERE pessoas_id = $Usu ");
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
    $var = array("coderro"=>$Erro, "quantiUsu"=>$row, "idpessoa"=>$Usu, "cpf"=>$Valor, "nomecompl"=>$NomeCompl, "dianasc"=>$DiaNasc, "mesnasc"=>$MesNasc, "jatem"=>$JaTem, "ultlog"=>$UltLog, "acessos"=>$Acessos, "ativo"=>$Ativo, "adm"=>$Adm, "setor"=>$Setor);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="confsenhaant"){
    $Usu = $_SESSION["usuarioID"];
    $Valor = filter_input(INPUT_GET, 'valor');
    $Valor = removeInj($Valor);
    $Sen = MD5($Valor);
    $Erro = 0;
    $row = 0;

    $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".usuarios WHERE id = $Usu And senha = '$Sen'");
    if(!$rs){
        $Erro = 1;
    }else{
        $row = pg_num_rows($rs);
    }
    $var = array("coderro"=>$Erro, "row"=>$row);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="salvaAtiv"){
    $Usu = (int) filter_input(INPUT_GET, 'numero');
    $UsuLogado = (int) filter_input(INPUT_GET, 'usulogado');
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".usuarios SET Ativo = $Valor, usuModif = $UsuLogado, dataModif = NOW() WHERE id = $Usu");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="trocasenha"){
    $Usu = $_SESSION["usuarioID"];
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
    if($Sen == "" || is_null($Sen)){
        $Erro = 3;
    }
    if($Erro == 0){
        $Senha = removeInj($Sen);
        $Senha = MD5($Senha);
        $rs = pg_query($Conec, "UPDATE ".$xProj.".usuarios SET senha = '$Senha', dataModif = NOW() WHERE id = $Usu");
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
        $Senha = removeInj($Sen);
        $Senha = MD5($Senha);
        $rs = pg_query($Conec, "UPDATE ".$xProj.".usuarios SET senha = '$Senha', usuModif = $Usu, dataModif = NOW() WHERE id = $Usu");
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