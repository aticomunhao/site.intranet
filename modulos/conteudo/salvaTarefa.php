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

if($Acao=="mudaStatus"){
    $Erro = 0;
    if(isset($_REQUEST["numero"])){
        $Num = (int) $_REQUEST["numero"];
        $Ativo = (int) $_REQUEST["guardaativ"]; // Se for 4 vai tornar inativo
    }else{
        $Num = 0;
        $Erro = 1;
    }
    if(isset($_REQUEST["novoStatus"])){
        $Sit = (int) $_REQUEST["novoStatus"];
    }else{
        $Sit = 1;
    }
    if(isset($_REQUEST["usumodif"])){
        $UsuModif = $_REQUEST["usumodif"];
    }else{
        $UsuModif = 0;
    }

    //procura a situação Sit no bd
    $rs0 = pg_query($Conec, "SELECT sit, to_char(datasit2, 'YYYY/MM/DD'), to_char(datasit3, 'YYYY/MM/DD') FROM ".$xProj.".tarefas WHERE idtar = $Num");
    $tbl = pg_fetch_row($rs0);
    $SitOrig = $tbl[0];
    $DataSit2 = $tbl[1];
    $DataSit3 = $tbl[2];

    $Erro = 0;
    if($Sit > $SitOrig){ // Não deixa voltar a tarefa
        if($Num > 0){
            if($Sit == 4){
                $Sql = pg_query($Conec, "UPDATE ".$xProj.".tarefas SET sit = $Sit, datasit".$Sit." = NOW(), usumodifsit = $UsuModif, ativo = 2 WHERE idtar = $Num");
                if($DataSit2 == '3000/12/31'){ // passou direto para Terminada
                    $Sql = pg_query($Conec, "UPDATE ".$xProj.".tarefas SET datasit2 = NOW() WHERE idtar = $Num");
                }
                if($DataSit3 == '3000/12/31'){ // passou direto para Terminada
                    $Sql = pg_query($Conec, "UPDATE ".$xProj.".tarefas SET datasit3 = NOW() WHERE idtar = $Num");
                }
            }else{
                $Sql = pg_query($Conec, "UPDATE ".$xProj.".tarefas SET sit = $Sit, datasit".$Sit." = NOW(), usumodifsit = $UsuModif, ativo = $Ativo WHERE idtar = $Num");
                if($Sit == 3 && $DataSit2 == '3000/12/31'){ // passou direto para Em andamento
                    $Sql = pg_query($Conec, "UPDATE ".$xProj.".tarefas SET datasit2 = NOW() WHERE idtar = $Num");
                }
            }
            if(!$Sql){
                $Erro = 1;
            }
        }
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="buscaTarefa"){
    $Erro = 0;
    if(isset($_REQUEST["numero"])){
        $Num = (int) $_REQUEST["numero"];
    }else{
        $Num = 0;
        $Erro = 1;
    }
    if($Num > 0){
        $Sql = pg_query($Conec, "SELECT ".$xProj.".poslog.pessoas_id, ".$xProj.".poslog.nomecompl, ".$xProj.".tarefas.usuins, usuexec, tittarefa, textotarefa, sit, prio 
        FROM ".$xProj.".tarefas INNER JOIN ".$xProj.".poslog ON ".$xProj.".tarefas.usuins = ".$xProj.".poslog.pessoas_id WHERE ".$xProj.".tarefas.idtar = $Num");
        if(!$Sql){
            $Erro = 1;
            $var = array("coderro"=>$Erro);
        }
        $row = pg_num_rows($Sql);
        $tbl = pg_fetch_row($Sql);
        $usuIns = $tbl[2];
        $var = array("coderro"=>$Erro, "usuExec"=>$tbl[3], "usuIns"=>$usuIns, "NomeUsuIns"=>$usuIns, "TitTarefa"=>$tbl[4], "TextoTarefa"=>$tbl[5], "Usuario"=>$tbl[1], "sit"=>$tbl[6], "priorid"=>$tbl[7]);
    }
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="salvaTarefa"){
    if(isset($_REQUEST["numero"])){
        $idTarefa = (int) filter_input(INPUT_GET, 'numero');
    }else{
        $idTarefa = 0;
        $Erro = 1;
    }
    $usuLogado = (int) filter_input(INPUT_GET, 'usuLogado');
    $usuExec = (int) filter_input(INPUT_GET, 'idExecSelect');
    $textoEvid = filter_input(INPUT_GET, 'textoEvid'); 
    $textoExt = filter_input(INPUT_GET, 'textoExt');
    $Status = filter_input(INPUT_GET, 'selectStatus'); // adminstr pode mudar
    $Priorid = filter_input(INPUT_GET, 'priorid');
    $Erro = 0;
    $row = 0;

    if($idTarefa != 0){
        if($Status == 4){
            $Sql = pg_query($Conec, "UPDATE ".$xProj.".tarefas SET usuexec = $usuExec, tittarefa = '$textoEvid', textotarefa = '$textoExt', sit = $Status, prio = $Priorid, ativo = 2, usumodif = $usuLogado, datamodif = NOW() WHERE idtar = $idTarefa"); 
        }else{
            $Sql = pg_query($Conec, "UPDATE ".$xProj.".tarefas SET usuexec = $usuExec, tittarefa = '$textoEvid', textotarefa = '$textoExt', sit = $Status, prio = $Priorid, ativo = 1, usumodif = $usuLogado, datamodif = NOW() WHERE idtar = $idTarefa"); 
            if($Status == 1){ // póde ser modificação para voltar a designada
                $Sql = pg_query($Conec, "UPDATE ".$xProj.".tarefas SET datasit2 = '3000-12-31', datasit3 = '3000-12-31', datasit4 = '3000-12-31' WHERE idtar = $idTarefa"); 
            }
        }
        if(!$Sql){
            $Erro = 1;
        }
    }else{  //inserçao de nova tarefa
        $rs0 = pg_query($Conec, "SELECT usuexec FROM ".$xProj.".tarefas WHERE usuexec = $usuExec And tittarefa = '$textoEvid' And sit != 4"); 
        $row0 = pg_num_rows($rs0);
        if($row0 > 0){
            $Erro = 2; // tarefa já foi dada para o mesmo usuário
        }else{
            $rsCod = pg_query($Conec, "SELECT MAX(idtar) FROM ".$xProj.".tarefas");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1); //usuinsorig para possib transferir tarefa para outro mandante
            $Sql = pg_query($Conec, "INSERT INTO ".$xProj.".tarefas (idtar, usuins, usuexec, tittarefa, textotarefa, datains, sit, prio) VALUES($CodigoNovo, $usuLogado, $usuExec, '$textoEvid', '$textoExt', NOW(), 1, $Priorid)"); 
            if(!$Sql){
                $Erro = 1;
            }
        }
    }
    $var = array("coderro"=>$Erro, "idtarefa"=>$idTarefa);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="deletaTarefa"){
    $Erro = 0;
    if(isset($_REQUEST["numero"])){
        $idTarefa = (int) filter_input(INPUT_GET, 'numero');
        $usuLogado = (int) filter_input(INPUT_GET, 'usuLogado');
    }else{
        $idTarefa = 0;
        $Erro = 1;
    }
    if($idTarefa > 0){
        $Sql = pg_query($Conec, "UPDATE ".$xProj.".tarefas SET ativo = 0, datacancel = NOW(), usucancel = $usuLogado WHERE idtar = $idTarefa");
        if(!$Sql){
            $Erro = 1;
        }
    }
    $var = array("coderro"=>$Erro, "idtarefa"=>$idTarefa);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="buscaMsg"){
    $Erro = 0;
    if(isset($_REQUEST["numero"])){
        $idTarefa = (int) filter_input(INPUT_GET, 'numero');
    }else{
        $idTarefa = 0;
        $Erro = 1;
    }
    $Sql0 = pg_query($Conec, "SELECT tittarefa, textotarefa FROM ".$xProj.".tarefas WHERE idtar = $idTarefa");
    $tbl0 = pg_fetch_row($Sql0);

    $Sql1 = pg_query($Conec, "SELECT iduser, idtarefa, textomsg, datamsg FROM ".$xProj.".tarefas LEFT JOIN ".$xProj.".tarefas_msg ON ".$xProj.".tarefas.idtar = ".$xProj.".tarefas_msg.idtarefa WHERE idtarefa = $idTarefa");
    $row1 = pg_num_rows($Sql1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($Sql1);
        $var = array("coderro"=>$Erro, "TitTarefa"=>$tbl0[0], "TextoTarefa"=>$tbl0[1], "dataMsg"=>$tbl1[3], "textoMsg"=>$tbl1[2]);
    }else{
        $Erro = 2; // nenhuma mensagem
        $var = array("coderro"=>$Erro, "TitTarefa"=>$tbl0[0], "TextoTarefa"=>$tbl0[1]);
    }
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="salvaMensagem"){
    $Erro = 0;
    if(isset($_REQUEST["numtarefa"])){
        $idTarefa = (int) filter_input(INPUT_GET, 'numtarefa');
        $idLogado = (int) filter_input(INPUT_GET, 'numusuario');
        $NomeLogado = filter_input(INPUT_GET, 'nomeusuario'); // para salvar em arquivo
        $textoExt = filter_input(INPUT_GET, 'textoExt');
    }else{
        $idTarefa = 0;
        $Erro = 1;
    }
    if($idTarefa > 0){
        $rs0 = pg_query($Conec, "SELECT usuins, usuexec FROM ".$xProj.".tarefas WHERE idtar = $idTarefa");
        $tbl0 = pg_fetch_row($rs0);
        $UsuIns = $tbl0[0];
        $UsuExec = $tbl0[1];
        if($UsuIns == $idLogado){
            $Campo = "insLido";
        }else{
            $Campo = "execLido";
        }

//        $Sql = pg_query($Conec, "INSERT INTO ".$xProj.".tarefas_msg (idUser, idTarefa, TextoMsg, DataMsg, Tarefa_Ativ, Tarefa_Lida, Leitura) VALUES($idLogado, $idTarefa, '$textoExt', NOW(), 1, 0, CONCAT(NOW(), ' - ', '$NomeLogado', ' - Inserção', '\n'))");
        $rsCod = pg_query($Conec, "SELECT MAX(idmsg) FROM ".$xProj.".tarefas_msg");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);

        $Sql = pg_query($Conec, "INSERT INTO ".$xProj.".tarefas_msg (idmsg, iduser, idtarefa, usuinstar, usuexectar, textomsg, datamsg, $Campo) 
        VALUES($CodigoNovo, $idLogado, $idTarefa, $UsuIns, $UsuExec, '$textoExt', NOW(), 1)");
        if(!$Sql){
            $Erro = 1;
        }
    }
    $var = array("coderro"=>$Erro, "idtarefa"=>$idTarefa);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="marcalidas"){
    $Erro = 0;
    if(isset($_REQUEST["numtarefa"])){
        $idTarefa = (int) filter_input(INPUT_GET, 'numtarefa');
        $NomeLogado = filter_input(INPUT_GET, 'nomeusuario'); // para salvar em arquivo
    }else{
        $idTarefa = 0;
        $Erro = 1;
    }
    if($idTarefa > 0){
        $Sql = pg_query($Conec, "UPDATE ".$xProj.".tarefas_msg SET tarefa_lida = 1 WHERE idtarefa = $idTarefa");
        // And idUser = $idLogado
        if(!$Sql){
            $Erro = 1;
        }
    }
    $var = array("coderro"=>$Erro, "idtarefa"=>$idTarefa);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="apagaMensagem"){
    $Cod = (int) filter_input(INPUT_GET, 'numMsg');
    $Erro = 0;
    $Sql = pg_query($Conec, "UPDATE ".$xProj.".tarefas_msg SET elim = 1, dataelim = NOW() WHERE idmsg = $Cod");
    if(!$Sql){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="marcaTransf"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $Marca = 0;
    $rs = pg_query($Conec, "SELECT marca FROM ".$xProj.".tarefas WHERE idtar = $Cod ");
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        $Marca = $tbl[0];
    }
    if($Marca == 0){ //estava desmarcado
        pg_query($Conec, "UPDATE ".$xProj.".tarefas SET marca = 1 WHERE idtar = $Cod");
    }else{ // estava marcado
        pg_query($Conec, "UPDATE ".$xProj.".tarefas SET marca = 0 WHERE idtar = $Cod");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="procuramarcas"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');  // usuário que vai receber
    $Erro = 0;
    $NomeCompleto = "";
    $rs0 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $Cod ");
    $tbl0 = pg_fetch_row($rs0);
    $NomeCompleto = $tbl0[0];

    $rs0 = pg_query($Conec, "SELECT marca FROM ".$xProj.".tarefas WHERE usuins = ".$_SESSION["usuarioID"]." And sit != 4 ");
    $row0 = pg_num_rows($rs0);

    $rs = pg_query($Conec, "SELECT marca FROM ".$xProj.".tarefas WHERE usuins = ".$_SESSION["usuarioID"]." And marca = 1 ");
    $row = pg_num_rows($rs);
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "contagem"=>$row0, "marcas"=>$row, "nomecompleto"=>$NomeCompleto);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="transferemarcas"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');    
    $Erro = 0;
    $rs0 = pg_query($Conec, "UPDATE ".$xProj.".tarefas SET usuinsorig = usuins WHERE usuins = ".$_SESSION["usuarioID"]." And marca = 1 ");
    $rs1 = pg_query($Conec, "UPDATE ".$xProj.".tarefas SET usuins = $Cod, usutransf = ".$_SESSION["usuarioID"].", datatransf = NOW() WHERE usuinsorig = ".$_SESSION["usuarioID"]." And marca = 1 ");
    if(!$rs1){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "codigo"=>$Cod);
    $responseText = json_encode($var);
    echo $responseText;
}

