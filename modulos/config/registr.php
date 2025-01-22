<?php
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
        $rs = pg_query($Conec, "SELECT column_name, data_type FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'poslog'");
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
            $rs1 = pg_query($Conec, "SELECT senha FROM ".$xProj.".poslog WHERE cpf = '$Login' And ativo = 1");
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
                    $_SESSION['sexo'] = $Sexo;
                    $_SESSION['start_login'] = time();
                    $_SESSION["usuarioID"] = $id;
                    $_SESSION["msg"] = ""; //para troca de slides e tráfego de arquivos
                    $_SESSION['msgarq'] = ""; //para upload arquivos diretorias/assessorias
                    $_SESSION['geremsg'] = 0;
                    $_SESSION['gerenum'] = 0;
                    $_SESSION['arquivo'] = "";
                    $_SESSION["CodSetorUsu"] = 0 ;
                    $_SESSION["SiglaSetor"] = "n/d";
                    $_SESSION["CodSubSetorUsu"] = 1; // não tem mais subdiretorias - deixar 1
                    $_SESSION["AdmUsu"] = 2;

                    // Aumenta número de acessos e grava data hora do login  - logfim = now() para mostrar on line
                    pg_query($Conec, "UPDATE ".$xProj.".poslog SET numacessos = (numacessos + 1), logini = NOW(), logfim = NOW() WHERE pessoas_id = $id "); 

                    $_SESSION["AdmBens"] = parEsc("bens", $Conec, $xProj, $_SESSION["usuarioID"]); // ver se está marcado para administrar bens encontrados
                    $_SESSION["FiscBens"] = parEsc("fiscbens", $Conec, $xProj, $_SESSION["usuarioID"]); // ver se está marcado para ver bens encontrados
                    $_SESSION["SoInsBens"] = parEsc("soinsbens", $Conec, $xProj, $_SESSION["usuarioID"]);

                    $rs2 = pg_query($Conec, "SELECT adm, codsetor, nomeusual FROM ".$xProj.".poslog WHERE cpf = '$Login' ");
                    $tbl2 = pg_fetch_row($rs2);
                    if($tbl2[0] == 0){
                        $_SESSION["AdmUsu"] = 2;    
                    }else{
                        $_SESSION["AdmUsu"] = $tbl2[0];
                    }
                    $_SESSION["CodSetorUsu"] = $tbl2[1];
                    if(!is_null($tbl2[2]) && $tbl2[2] != ""){
                        $_SESSION["NomeUsual"] = $tbl2[2];
                    }

                    $rs3 = pg_query($Conec, "SELECT siglasetor, descsetor FROM ".$xProj.".setores WHERE codset = $tbl2[1] "); 
                    $row3 = pg_num_rows($rs3);
                    if($row3 > 0){
                        $tbl3 = pg_fetch_row($rs3);
                        $_SESSION["SiglaSetor"] = $tbl3[0];
                    }

                    $rs4 = pg_query($Conec, "SELECT id FROM ".$xProj.".pessoas WHERE cpf = '$Login' "); 
                    $row4 = pg_num_rows($rs4); 
                    if($row4 == 0){
                        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".pessoas");
                        $tblCod = pg_fetch_row($rsCod);
                        $Codigo = $tblCod[0];
                        $CodigoNovo = ($Codigo+1); // cópia no esquema cesb
                        pg_query($Conec, "INSERT INTO ".$xProj.".pessoas (id, pessoas_id, cpf, nome_completo, dt_nascimento, sexo, status, datains, nome_resumido) VALUES ($CodigoNovo, $id, '$Login', '$NomeCompl', '$DNasc', $Sexo, 1, NOW(), '$NomeUsual') "); 
                    }

                    // o primeiro que logar executa a rotina
                    $rs5 = pg_query($Conec, "SELECT dataelim, prazodel FROM ".$xProj.".paramsis WHERE idpar = 1 ");
                    $row5 = pg_num_rows($rs5);
                    if($row5 > 0){ 
                        $tbl5 = pg_fetch_row($rs5);
                        $DataElim = $tbl5[0];
                        $PrazoDel = $tbl5[1];
                        if(is_null($PrazoDel) || $PrazoDel == 0){
                            $PrazoDel = 5;
                        }
                        $Hoje = date('Y/m/d');
                        if(strtotime($DataElim) < strtotime($Hoje)){ // verifica se alguém já logou e inseriu a data de hoje
                            if($PrazoDel < 1000){
                                pg_query($Conec, "DELETE FROM ".$xProj.".calendev WHERE ativo = 0"); //Elimina dados apagados da tabela calendário
                                pg_query($Conec, "DELETE FROM ".$xProj.".calendev WHERE ((CURRENT_DATE - dataini)/365 > $PrazoDel)"); //Apaga da tabela calendário eventos passados há mais de $PrazoDel anos
                                pg_query($Conec, "DELETE FROM ".$xProj.".leitura_agua WHERE ((CURRENT_DATE - dataleitura)/365 > $PrazoDel)"); //Apaga da tabela lançamentos de leitura do hidrômetro passados há mais de $PrazoDel anos
                                pg_query($Conec, "DELETE FROM ".$xProj.".tarefas WHERE datains < CURRENT_DATE - interval '$PrazoDel years' "); //Apaga da tabela lançamentos de tarefas há mais de $PrazoDel anos
                                pg_query($Conec, "DELETE FROM ".$xProj.".tarefas_msg WHERE datamsg < CURRENT_DATE - interval '$PrazoDel years' "); //Apaga mensagens trocadas nas tarefas há mais de $PrazoDel anos
                                pg_query($Conec, "DELETE FROM ".$xProj.".livroreg WHERE datains < CURRENT_DATE - interval '$PrazoDel years' "); //Apaga registros do livro de ocorrências há mais de $PrazoDel anos
                                pg_query($Conec, "DELETE FROM ".$xProj.".bensachados WHERE datains < CURRENT_DATE - interval '$PrazoDel years' "); //Apaga registros do achados e perdidos há mais de $PrazoDel anos
                                pg_query($Conec, "DELETE FROM ".$xProj.".poslog WHERE logfim < CURRENT_DATE - interval '$PrazoDel years' "); //Apaga registros de usuários com último log há mais de $PrazoDel anos
                                pg_query($Conec, "DELETE FROM ".$xProj.".ramais_int WHERE ativo = 0 And datains < CURRENT_DATE - interval '$PrazoDel years'"); 
                                pg_query($Conec, "DELETE FROM ".$xProj.".ramais_ext WHERE ativo = 0 And datains < CURRENT_DATE - interval '$PrazoDel years'"); 
                                pg_query($Conec, "DELETE FROM ".$xProj.".arqsetor WHERE dataapag < CURRENT_DATE - interval '$PrazoDel years'"); // apaga nome dos arquivos de upload
                                pg_query($Conec, "DELETE FROM ".$xProj.".visitas_ar WHERE datavis < CURRENT_DATE - interval '$PrazoDel years'");
                                pg_query($Conec, "DELETE FROM ".$xProj.".visitas_ar2 WHERE datavis < CURRENT_DATE - interval '$PrazoDel years'");
                                pg_query($Conec, "DELETE FROM ".$xProj.".visitas_ar3 WHERE datavis < CURRENT_DATE - interval '$PrazoDel years'");
                                pg_query($Conec, "DELETE FROM ".$xProj.".visitas_el WHERE datavis < CURRENT_DATE - interval '$PrazoDel years'");
                                pg_query($Conec, "DELETE FROM ".$xProj.".chaves_ctl WHERE datavolta < CURRENT_DATE - interval '$PrazoDel years'");
                                pg_query($Conec, "DELETE FROM ".$xProj.".escaladaf WHERE dataescala < CURRENT_DATE - interval '$PrazoDel years'");
                                pg_query($Conec, "DELETE FROM ".$xProj.".escaladaf WHERE dataescala < CURRENT_DATE - interval '2 months' And ativo = 0;");
                            }
                            $rs6 = pg_query($Conec, "SELECT pessoas_id FROM ".$xProj.".poslog ");
                            $row6 = pg_num_rows($rs6); // atualiza nomes de poslog com pessoas
                            if($row6 > 0){
                                while ($tbl6 = pg_fetch_row($rs6)){
                                    $Cod = $tbl6[0];
                                    $rs7 = pg_query($ConecPes, "SELECT nome_completo, status, dt_nascimento, sexo, nome_resumido FROM ".$xPes.".pessoas WHERE id = $Cod ");
                                    $row7 = pg_num_rows($rs7);
                                    if($row7 == 1){
                                        $tbl7 = pg_fetch_row($rs7);
                                        $NomeC = GUtils::normalizarNome($tbl7[0]);  // Normatizar nomes próprios
                                        $NomeComp = addslashes($NomeC);
                                        $NomeCompl = str_replace('"', "'", $NomeComp); // substitui aspas duplas por simples
//                                        if(!is_null($tbl7[1])){
                                            $Ativo = $tbl7[1];
//                                        }else{
//                                            $Ativo = 0;
//                                        }
                                        if(!is_null($tbl7[2])){
                                            $DNasc = $tbl7[2];
                                        }else{
                                            $DNasc = "1500-01-01";
                                        }
                                        if(!is_null($tbl7[3])){
                                            $Sexo = $tbl7[3];
                                        }else{
                                            $Sexo = 1;
                                        }

                                        if(!is_null($tbl7[4])){
                                            $NomeU = $tbl7[4];
                                            $NomeUs = GUtils::normalizarNome($NomeU);  // Normatizar nomes próprios
                                            $NomeUsu = addslashes($NomeUs);
                                            $NomeUsual = str_replace('"', "'", $NomeUsu); // substitui aspas duplas por simples
                                            pg_query($Conec, "UPDATE ".$xProj.".poslog SET nomeusual = '$NomeUsual' WHERE pessoas_id = $Cod");
                                        }else{
                                            $NomeUsual = "";
                                        }
                                        pg_query($Conec, "UPDATE ".$xProj.".poslog SET nomecompl = '$NomeCompl', nomeusual = '$NomeUsual', ativo = $Ativo, sexo = $Sexo, datanasc = '$DNasc' WHERE pessoas_id = $Cod");
                                        pg_query($Conec, "UPDATE ".$xProj.".pessoas SET nome_completo = '$NomeCompl', nome_resumido = '$NomeUsual', status = $Ativo, dt_nascimento = '$DNasc', sexo = $Sexo WHERE pessoas_id = $Cod");
                                    }else{ // se não estiver mais em pessoas
                                        pg_query($Conec, "UPDATE ".$xProj.".poslog SET ativo = 0 WHERE pessoas_id = $Cod ");
                                    }
                                }
                            }
                            //põe a data de hoje em paramsis para que os próximos a loga não executem esta rotina
                            pg_query($Conec, "UPDATE ".$xProj.".paramsis SET dataelim = NOW() WHERE idpar = 1 ");
                        }
                    }

                    if($Sen == $Login){
                        $Erro = 5; // primeiro login - inserir nova senha
                    }
                    $var = array("coderro"=>$Erro, "msg"=>$Erro_Msg, "usuarioid"=>$id, "usuarioNome"=>$NomeCompl, "usuarioAdm"=>$_SESSION["AdmUsu"], "usuario"=>$NomeUsual); 
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
        //tem Tarefa
        $rowTar = 0;
        $rsTar = pg_query($Conec, "SELECT idtar, sit FROM ".$xProj.".tarefas WHERE usuexec = ".$_SESSION["usuarioID"]." And sit = 1 And ativo = 1");
        $rowTar = pg_num_rows($rsTar);
        if($rowTar > 0){
            if($rowTar == 1){
                $msgTar = "Tarefa expedida para ". $_SESSION["NomeCompl"].".<br> Clique aqui para verificar.";
            }else{
                $msgTar = $rowTar." tarefas expedidas para ". $_SESSION["NomeCompl"].".<br> Clique aqui para verificar.";
            }
        }else{
            $msgTar = "";
        }

        $recadoTar = "";
        $TemRecado = 0;
        $CodTar = 0;
        $Selec = 5;
        $rs4 = pg_query($Conec, "SELECT idtarefa, inslido, execlido FROM ".$xProj.".tarefas_msg WHERE usuexectar = '".$_SESSION["usuarioID"]."' And execlido = 0 Or usuinstar = '".$_SESSION["usuarioID"]."' And inslido = 0"); // procura mensagens não lidas como usuExec 
        $TemRecado = pg_num_rows($rs4); // quantid mensagens não lidas
        if($TemRecado > 0){
            $recadoTar = "Mensagem não lida nas Tarefa.";
            $tbl4 = pg_fetch_row($rs4);
            $CodTar = $tbl4[0];
            if($tbl4[1] == 0){
                $Selec = 6; // Selecionar Meus Pedidos
            }else{
                $Selec = 5; // Selecionar Minhas Tarefas
            }
        }

        $rowBens = 0;
        $rowDest = 0;
        if($_SESSION["AdmBens"] == 1){ 
            $rsBens = pg_query($Conec, "SELECT id FROM ".$xProj.".bensachados WHERE codusuins > 0 And usurestit = 0 And usucsg = 0");
            $rowBens = pg_num_rows($rsBens);

            $rsDest = pg_query($Conec, "SELECT id FROM ".$xProj.".bensachados WHERE (CURRENT_DATE-datareceb) > 90 And usucsg >0 And usurestit = 0 And usudestino = 0");
            $rowDest = pg_num_rows($rsDest);
        }
        // Contratos
        $rowContr1 = 0;
        $rowContr2 = 0;
        $Contr = parEsc("contr", $Conec, $xProj, $_SESSION["usuarioID"]);
        $FiscContr = parEsc("fisc_contr", $Conec, $xProj, $_SESSION["usuarioID"]);
        if($Contr == 1 || $FiscContr == 1){
            $Hoje = date('Y/m/d');
            $rsContr1 = pg_query($Conec, "SELECT ".$xProj.".contratos1.id, empresa 
            FROM ".$xProj.".contratos1 INNER JOIN ".$xProj.".contrato_empr ON ".$xProj.".contratos1.codempresa = ".$xProj.".contrato_empr.id 
            WHERE ".$xProj.".contratos1.ativo = 1 And notific = 1 And dataaviso <= '$Hoje' And datavencim >= '$Hoje' And pararaviso = 0 ");
            $rowContr1 = pg_num_rows($rsContr1);

            $rsContr2 = pg_query($Conec, "SELECT ".$xProj.".contratos2.id, empresa 
            FROM ".$xProj.".contratos2 INNER JOIN ".$xProj.".contrato_empr ON ".$xProj.".contratos2.codempresa = ".$xProj.".contrato_empr.id 
            WHERE ".$xProj.".contratos2.ativo = 1 And notific = 1 And dataaviso <= '$Hoje' And datavencim >= '$Hoje' And pararaviso = 0 ");
            $rowContr2 = pg_num_rows($rsContr2);
        }
    }

    $var = array("coderro"=>$Erro, "marca"=>$Marca, "acessos"=>$NumAcessos, "msg"=>$msg, "temTarefa"=>$rowTar, "msgTar"=>$msgTar, "bens"=>$rowBens, "bensdestinar"=>$rowDest, "contrato1"=>$rowContr1, "contrato2"=>$rowContr2, "temRecado"=>$TemRecado, "recadoTar"=>$recadoTar, "CodTarefa"=>$CodTar, "selecionar"=>$Selec, "usuario"=>$_SESSION["usuarioID"]);
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

//    $rs = pg_query($Conec, "SELECT adm, codsetor, ativo, to_char(logini, 'DD/MM/YYYY HH24:MI'), numacessos, lro, bens, fisclro, agua, eletric, arcond, 
//    arfisc, nomeusual, eletric2, eletric3, fiscbens, soinsbens, arcond2, arcond3, elev, fiscelev, 
//    esc_eft, esc_edit, esc_grupo, esc_fisc, clav, chave, fisc_clav, contr, fisc_contr, fisc_agua, fisc_eletric 
//    FROM ".$xProj.".poslog WHERE cpf = '$GuardaCpf' ");  //pessoas_id = $Usu ");

//  esc_eft -> eft_daf   esc_edit -> esc_daf 
    $rs = pg_query($Conec, "SELECT adm, codsetor, ativo, to_char(logini, 'DD/MM/YYYY HH24:MI'), numacessos, lro, bens, fisclro, agua, eletric, arcond, 
    arfisc, nomeusual, eletric2, eletric3, fiscbens, soinsbens, arcond2, arcond3, elev, fiscelev, 
    eft_daf, esc_daf, esc_grupo, esc_fisc, clav, chave, fisc_clav, contr, fisc_contr, fisc_agua, fisc_eletric 
    FROM ".$xProj.".poslog WHERE cpf = '$GuardaCpf' ");  //pessoas_id = $Usu ");

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
        $var = array("coderro"=>$Erro, "usuario"=>$Proc0[0], "nomecompl"=>$Proc0[1], "usuarioAdm"=>$Proc[0], "setor"=>$Proc[1], "ativo"=>$Proc[2], "ultlog"=>$UltLog, "acessos"=>$Proc[4], "lroPortaria"=>$Proc[5], "bens"=>$Proc[6], "lroFiscaliza"=>$Proc[7], "leituraAgua"=>$Proc[8], "leituraEletric"=>$Proc[9], "regarcond"=>$Proc[10], "regarcond2"=>$Proc[17], "regarcond3"=>$Proc[18], "regelev"=>$Proc[19], "fiscelev"=>$Proc[20], "escala"=>$Proc[21], "editaescala"=>$Proc[22], "grupoescala"=>$Proc[23], "fiscescala"=>$Proc[24], "claviculario"=>$Proc[25], "pegachave"=>$Proc[26], "fiscchaves"=>$Proc[27], "contrato"=>$Proc[28], "fisccontrato"=>$Proc[29], "fisc_agua"=>$Proc[30], "fisc_eletric"=>$Proc[31],
        "fiscarcond"=>$Proc[11], "usuarioNome"=>$Proc[12], "leituraEletric2"=>$Proc[13], "leituraEletric3"=>$Proc[14], "fiscbens"=>$Proc[15], "soinsbens"=>$Proc[16], "diaAniv"=>$Proc0[2], "mesAniv"=>$Proc0[3], "cpf"=>$GuardaCpf);
    }
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

    $NomeU = trim(filter_input(INPUT_GET, 'usuarioNome')); // vem de pessoas mas pode ser modificado aqui
    $NomeUs = GUtils::normalizarNome($NomeU);  // Normatizar nomes próprios
    $NomeUsu = addslashes($NomeUs);
    $NomeUsual = str_replace('"', "'", $NomeUsu); // substitui aspas duplas por simples
//    $NomeCompl = trim(filter_input(INPUT_GET, 'nomecompl')); // vem de pessoas
    $Lro  = (int) filter_input(INPUT_GET, 'lro');
    $FiscLro  = (int) filter_input(INPUT_GET, 'fisclro');
    $Bens  = (int) filter_input(INPUT_GET, 'bens');
    $FiscBens  = (int) filter_input(INPUT_GET, 'fiscbens');
    $SoInsBens  = (int) filter_input(INPUT_GET, 'soinsbens');
    
    $Agua  = (int) filter_input(INPUT_GET, 'agua');
    $FiscAgua  = (int) filter_input(INPUT_GET, 'fiscagua');
    
    $Eletric = (int) filter_input(INPUT_GET, 'eletric');
    $Eletric2 = (int) filter_input(INPUT_GET, 'eletric2');
    $Eletric3 = (int) filter_input(INPUT_GET, 'eletric3');
    $FiscEletric  = (int) filter_input(INPUT_GET, 'fisceletric');
    $ArCond = (int) filter_input(INPUT_GET, 'arcond');
    $ArCond2 = (int) filter_input(INPUT_GET, 'arcond2');
    $ArCond3 = (int) filter_input(INPUT_GET, 'arcond3');
    $FiscAr = (int) filter_input(INPUT_GET, 'fiscar');

    $Elev = (int) filter_input(INPUT_GET, 'elev');
    $FiscElev = (int) filter_input(INPUT_GET, 'fiscelev');
//    $Escala = (int) filter_input(INPUT_GET, 'escala');
    $GrupoEsc = (int) filter_input(INPUT_GET, 'grupoesc');
    $Escalante = (int) filter_input(INPUT_GET, 'escalante');
    $FiscEscala = (int) filter_input(INPUT_GET, 'fiscalescala');


    $Clavic = (int) filter_input(INPUT_GET, 'clavic');
    $PegaChave = (int) filter_input(INPUT_GET, 'pegachave');
    $FiscChaves = (int) filter_input(INPUT_GET, 'fiscchaves');

    $Contr = (int) filter_input(INPUT_GET, 'contrato');
    $FiscContr = (int) filter_input(INPUT_GET, 'fisccontrato');

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

    if($Usu > 0){  // salvar não atualiza o campo logfim - logfim conta tempo para apagar (5 anos)
        $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET codsetor = $Setor, adm = $Adm, ativo = $Ativo, usumodif = $UsuLogado, datamodif = NOW(), nomeusual = '$NomeUsual', nomecompl = '$NomeCompl', lro = $Lro, fisclro = $FiscLro, bens = $Bens, fiscbens =  $FiscBens, soinsbens = $SoInsBens, agua = $Agua, fisc_agua = $FiscAgua, eletric = $Eletric, eletric2 = $Eletric2, eletric3 = $Eletric3, fisc_eletric = $FiscEletric, arcond = $ArCond, arcond2 = $ArCond2, arcond3 = $ArCond3, arfisc = $FiscAr, elev = $Elev, fiscelev = $FiscElev, esc_grupo = $GrupoEsc, esc_daf = $Escalante, esc_fisc = $FiscEscala, clav = $Clavic, chave = $PegaChave, fisc_clav = $FiscChaves, contr = $Contr, fisc_contr = $FiscContr WHERE cpf = '$Cpf'"); 
        pg_query($Conec, "UPDATE ".$xProj.".pessoas SET pessoas_id = $Usu, nome_completo = '$NomeCompl', sexo = $Sexo, status = $Ativo WHERE cpf = '$Cpf' "); //coleção
// eft_daf = $Escala - só é marcado pelo escalante na página escala DAF
        if(!is_null($DNasc)){
            pg_query($Conec, "UPDATE ".$xProj.".pessoas SET dt_nascimento = '$DNasc' WHERE cpf = '$Cpf' "); 
        }
        if($Ativo == 0){ // bloqueado
            pg_query($Conec, "UPDATE ".$xProj.".poslog SET datainat = NOW() WHERE cpf = '$Cpf'"); // só marca a data da inatividade
        }else{
            pg_query($Conec, "UPDATE ".$xProj.".poslog SET datainat = '3000-12-31' WHERE cpf = '$Cpf'");
        }
        if(!$rs){
            $Erro = 1;
        }
    }
    if($Usu == 0){ // cadastrar
        if($GuardaId > 0){
            $m = strtotime("-1 Hour");
            $HoraAnt = date("Y-m-d H:i:s", $m); // para o recem cadastrado não aparecer on line

            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".poslog");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1);
            $Senha = password_hash($Cpf, PASSWORD_DEFAULT);
            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".poslog (id, pessoas_id, codsetor, adm, usuins, datains, cpf, nomecompl, senha, ativo, lro, fisclro, bens, fiscbens, soinsbens, agua, fisc_agua, eletric, eletric2, eletric3, fisc_eletric, arcond, arcond2, arcond3, arfisc, elev, fiscelev, esc_grupo, esc_daf, esc_fisc, clav, chave, fisc_clav, contr, fisc_contr, logini, logfim, datamodif, datainat, nomeusual, avhoje) 
            VALUES ($CodigoNovo, $GuardaId, $Setor, $Adm, $UsuLogado, NOW(), '$Cpf', '$NomeCompl', '$Senha', 1, $Lro, $FiscLro, $Bens, $FiscBens, $SoInsBens, $Agua, $FiscAgua, $Eletric, $Eletric2, $Eletric3, $FiscEletric, $ArCond, $ArCond2, $ArCond3, $FiscAr, $Elev, $FiscElev, $GrupoEsc, $Escalante, $FiscEscala, $Clavic, $PegaChave, $FiscChaves, $Contr, $FiscContr, '3000-12-31', '$HoraAnt', '3000-12-31', '3000-12-31', '$NomeUsual', (CURRENT_DATE - 1) )"); // logfim conta tempo para apagar usuário (5 anos)
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
    $var = array("coderro"=>$Erro, "usuario"=>$Usu, "guardausu"=>$GuardaId, "fiscar"=>$FiscAr);
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
    $Ativo = 0;
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
//                $NomeUsual = $Proc1[6];
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
if($Acao =="checaBoxes"){ // por mudança de setor
    $Param = (int) filter_input(INPUT_GET, 'param'); 
    $Cpf = filter_input(INPUT_GET, 'numero'); 
    $Cpf1 = addslashes($Cpf);
    $Cpf2 = str_replace(".", "", $Cpf1);
    $Cpf = str_replace("-", "", $Cpf2);
    $Erro = 0;
    $rs0 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET lro = 0, bens = 0 WHERE cpf = '$Cpf' ");
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

if($Acao =="confsenhaant"){
    $Cpf = $_SESSION["usuarioCPF"];
    $Valor = filter_input(INPUT_GET, 'valor');
    $Valor = removeInj($Valor);
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
        $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET senha = '$Sen', datamodif = NOW() WHERE cpf = '$Cpf'");
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
        $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET senha = '$Senha', usumodif = $Usu, datamodif = NOW() WHERE cpf = '$Cpf'");
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

if($Acao =="apagaAgua"){
    $Erro = 0;
    $rs = pg_query($Conec, "TRUNCATE TABLE ".$xProj.".leitura_agua");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="apagaEletric"){
    $Num = (int) filter_input(INPUT_GET, 'numero'); 
    $Erro = 0;
    $rs = pg_query($Conec, "DELETE FROM ".$xProj.".leitura_eletric WHERE colec = $Num");
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
    $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET logfim = NOW() WHERE pessoas_id = ".$_SESSION["usuarioID"]."");
    if(!$rs){
        $Erro = 1;
    }
    $rs1 = pg_query($Conec, "SELECT EXTRACT(HOURS FROM (NOW()-logini)) as horas FROM  ".$xProj.".poslog WHERE pessoas_id = ".$_SESSION["usuarioID"]."");
    $tbl1 = pg_fetch_row($rs1);
    if($tbl1[0] > 15){ // 15 horas logado - reiniciar
        session_start();
        $_SESSION = array();
        session_destroy();
        header("Location: ../../index.php");
    }

    $rs2 = pg_query($Conec, "SELECT pico_online, pico_dia FROM ".$xProj.".paramsis WHERE idpar = 1 ");
    $tbl2 = pg_fetch_row($rs2);
    $QuantOn = $tbl2[0];
    $QuantDia = $tbl2[1];
    //Usuários on line
    $rs3 = pg_query($Conec, "SELECT COUNT(id) FROM ".$xProj.".poslog WHERE ativo = 1 And EXTRACT(EPOCH FROM (NOW() - logfim)) <= 60");
    if($rs3){
        $tbl3 = pg_fetch_row($rs3);
        $Quant = $tbl3[0];
    }else{
        $Quant = 0;
    }
    //registra pico de usuários on line
    if($Quant > $QuantOn){
        pg_query($Conec, "UPDATE ".$xProj.".paramsis SET pico_online = $Quant, data_pico_online = NOW() WHERE idpar = 1 ");
    }
    //registra pico de usuários no dia
    $rs4 = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE ativo = 1 And TO_CHAR(logini, 'YYYY/MM/DD') = TO_CHAR(CURRENT_DATE, 'YYYY/MM/DD')");
    $row4 = pg_num_rows($rs4);
    if($row4 > $QuantDia){
        pg_query($Conec, "UPDATE ".$xProj.".paramsis SET pico_dia = $row4, data_pico_dia = NOW() WHERE idpar = 1 ");
    }
    $var = array("coderro"=>$Erro, "interv"=>$tbl1[0]);
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

if($Acao =="insexectarefa"){
    $UsuIndiv = (int) filter_input(INPUT_GET, 'usuindiv');
    $UsuGrupo = (int) filter_input(INPUT_GET, 'usugrupo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".tarefas_gr WHERE usuindiv = $UsuIndiv And usugrupo = $UsuGrupo");
    $row = pg_num_rows($rs);
    if($row == 0){
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".tarefas_gr");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".tarefas_gr (id, usuindiv, usugrupo) VALUES ($CodigoNovo, $UsuIndiv, $UsuGrupo); ");
    }
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