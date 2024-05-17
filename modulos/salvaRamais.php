<?php
session_start();
require_once("config/abrealas.php");
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    $Tipo = (int) $_REQUEST["tipo"];
}

if($Tipo == 1){

    if($Acao =="buscaNome"){
        $Num = (int) filter_input(INPUT_GET, 'numero'); // id
        $Erro = 0;
        $SiglaSetor = "";
        $CodNome = 0;
        $Ramal = "";
        $JaTem = 0;

        $Sql = pg_query($Conec, "SELECT siglasetor, ".$xProj.".poslog.id 
        FROM ".$xProj.".poslog INNER JOIN ".$xProj.".setores ON ".$xProj.".poslog.codsetor = ".$xProj.".setores.codset
        WHERE id = $Num");
        if(!$Sql){
           $Erro = 1;
        }else{
            $Proc = pg_fetch_row($Sql);
            $SiglaSetor = $Proc[0];
            $CodNome = $Proc[1];
            if($CodNome > 0){
                $JaTem = 1;
                $rs1 = pg_query($Conec, "SELECT nomeusu, ramal FROM ".$xProj.".ramais_int WHERE poslog_id = $CodNome");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    $tbl1 = pg_fetch_row($rs1);
                    $NomeUsual = $tbl1[0];
                    $Ramal = $tbl1[1];
                }
            }
            
        }
        $var = array("coderro"=>$Erro, "jatem"=>$JaTem, "siglasetor"=>$SiglaSetor, "nomeusual"=>$NomeUsual, "ramal"=>$Ramal);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="buscaRamal"){
        $Num = (int) filter_input(INPUT_GET, 'numero'); // id
        $Erro = 0;
        $row = 0;
        $Sql = pg_query($Conec, "SELECT ".$xProj.".poslog.id, nomeusu, ".$xProj.".poslog.codsetor, ramal 
        FROM ".$xProj.".ramais_int INNER JOIN ".$xProj.".poslog ON ".$xProj.".ramais_int.poslog_id = ".$xProj.".poslog.id 
        WHERE poslog.id = $Num");

        if(!$Sql){
           $Erro = 1;
        }else{
            $row = pg_num_rows($Sql);
            $Proc = pg_fetch_row($Sql);
        }
        $var = array("coderro"=>$Erro, "idposlog"=>$Proc[0], "usuario"=>$Proc[1], "setor"=>$Proc[2], "ramal"=>$Proc[3]);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="salvaRamal"){
        $id = (int) filter_input(INPUT_GET, 'numero');
        $Nome = filter_input(INPUT_GET, 'usuario');  //$_REQUEST["nome"];
        $CodNome = filter_input(INPUT_GET, 'nomecompl');
        $Ramal = filter_input(INPUT_GET, 'ramal');
        $UsuLogado = $_SESSION["usuarioID"]; //$_REQUEST["usulogado"];
        $Erro = 0;
        $row = 0;
        if($id != 0){ // edição
            $Sql = pg_query($Conec, "UPDATE ".$xProj.".ramais_int SET poslog_id = $CodNome, nomeusu = '$Nome', ramal = '$Ramal', usumodif = $UsuLogado, datamodif = NOW() WHERE poslog_id = $id");
            if(!$Sql){
                $Erro = 1;
            }
        }else{ // inserção
            $rsCod = pg_query($Conec, "SELECT MAX(codtel) FROM ".$xProj.".ramais_int");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1);

            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".ramais_int (codtel, nomeusu, ramal, datains, usuins, poslog_id) 
            VALUES($CodigoNovo, '$Nome', '$Ramal', NOW(), $UsuLogado, $CodNome)");
            if(!$rs){
                $Erro = 1;
            }
        }
        $var = array("coderro"=>$Erro, "id"=>$id);
        $responseText = json_encode($var);
        echo $responseText;
    }
    if($Acao =="deletaRamal"){
        $id = (int) filter_input(INPUT_GET, 'numero');
        $Erro = 0;
        $Sql = pg_query($Conec, "UPDATE ".$xProj.".ramais_int SET ativo = 0 WHERE codtel = $id");
        if(!$Sql){
            $Erro = 1;
        }    
        $var = array("coderro"=>$Erro, "id"=>$id);
        $responseText = json_encode($var);
        echo $responseText;
    }
}

if($Tipo == 2){
    if($Acao=="buscaRamal"){
        $Num = (int) filter_input(INPUT_GET, 'numero'); // id
        $Erro = 0;
        $row = 0;
        $Sql = pg_query($Conec, "SELECT siglaempresa, nomeempresa, setor, contatonome, telefonefixo, telefonecel FROM ".$xProj.".ramais_ext WHERE codtel = $Num");
        if(!$Sql){
           $Erro = 1;
        }else{
            $row = pg_num_rows($Sql);
            $Proc = pg_fetch_row($Sql);
        }
         $var = array("coderro"=>$Erro, "SiglaEmpresa"=>$Proc[0], "NomeEmpresa"=>$Proc[1], "Setor"=>$Proc[2], "ContatoNome"=>$Proc[3], "TelefoneFixo"=>$Proc[4], "TelefoneCel"=>$Proc[5]);
        $responseText = json_encode($var);
        echo $responseText;
     }
    
    if($Acao=="salvaRamal"){
        $id = (int) filter_input(INPUT_GET, 'numero');
        $SiglaEmpresa = filter_input(INPUT_GET, 'SiglaEmpresa');  //$_REQUEST["nome"];
        $NomeEmpresa = filter_input(INPUT_GET, 'NomeEmpresa');
        $Setor = filter_input(INPUT_GET, 'Setor');
        $TelefoneFixo = filter_input(INPUT_GET, 'TelefoneFixo');
        $TelefoneCel = filter_input(INPUT_GET, 'TelefoneCel');
        if(strlen($TelefoneFixo) == 6){
            $TelefoneFixo = str_replace("(", "", $TelefoneFixo);
            $TelefoneFixo = str_replace(")", "", $TelefoneFixo);
            $TelefoneFixo = str_replace(" ", "", $TelefoneFixo);
        }
        $ContatoNome = filter_input(INPUT_GET, 'ContatoNome');
        $UsuLogado = $_SESSION["usuarioID"]; //$_REQUEST["usulogado"];
        $Erro = 0;
        $row = 0;
        if($id !== 0){ // edição
            $Sql = pg_query($Conec, "UPDATE ".$xProj.".ramais_ext SET siglaempresa = '$SiglaEmpresa', nomeempresa = '$NomeEmpresa', setor = '$Setor', telefonefixo = '$TelefoneFixo', telefonecel = '$TelefoneCel', contatonome = '$ContatoNome', usumodif = '$UsuLogado', datamodif = NOW() WHERE codtel = $id");
            if(!$Sql){
                $Erro = 1;
            }
        }else{ // inserção
            $rs = pg_query($Conec, "SELECT siglaempresa, nomeempresa, setor, telefonefixo FROM ".$xProj.".ramais_ext WHERE siglaempresa = '$SiglaEmpresa' And nomeempresa = '$NomeEmpresa'");
            $row = pg_num_rows($rs);
            if($row > 0){
                $Erro = 2; // nome já existe
            }else{
                $rsCod = pg_query($Conec, "SELECT MAX(codtel) FROM ".$xProj.".ramais_ext");
                $tblCod = pg_fetch_row($rsCod);
                $Codigo = $tblCod[0];
                $CodigoNovo = ($Codigo+1);

                $Sql = pg_query($Conec, "INSERT INTO ".$xProj.".ramais_ext (codtel, siglaempresa, nomeempresa, setor, telefonefixo, telefonecel, contatonome, datains, usuins) 
                VALUES($CodigoNovo, '$SiglaEmpresa', '$NomeEmpresa', '$Setor', '$TelefoneFixo', '$TelefoneCel', '$ContatoNome', NOW(), '$UsuLogado')");
                if(!$Sql){
                    $Erro = 1;
                }
            }
        }
        $var = array("coderro"=>$Erro, "id"=>$id, "row"=>$row);
        $responseText = json_encode($var);
        echo $responseText;
    }
    if($Acao=="deletaRamal"){
        $id = (int) filter_input(INPUT_GET, 'numero');
        $Erro = 0;
        $Sql = pg_query($Conec, "UPDATE ".$xProj.".ramais_ext SET ativo = 0 WHERE codtel = $id");
        if(!$Sql){
            $Erro = 1;
        }    
        $var = array("coderro"=>$Erro, "id"=>$id);
        $responseText = json_encode($var);
        echo $responseText;
    }

}