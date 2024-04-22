<?php
session_start();
require_once("config/abrealas.php");
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    $Tipo = (int) $_REQUEST["tipo"];
}

if($Tipo == 1){
    if($Acao =="buscaRamal"){
        $Num = (int) filter_input(INPUT_GET, 'numero'); // id
        $Erro = 0;
        $row = 0;
        $Sql = pg_query($Conec, "SELECT nomeusu, nomecompl, setor, ramal FROM ".$xProj.".ramais_int WHERE codtel = $Num");
        if(!$Sql){
           $Erro = 1;
        }else{
            $row = pg_num_rows($Sql);
            $Proc = pg_fetch_row($Sql);
        }
        $var = array("coderro"=>$Erro, "usuario"=>$Proc[0], "nomecompl"=>$Proc[1], "setor"=>$Proc[2], "ramal"=>$Proc[3]);
        $responseText = json_encode($var);
        echo $responseText;
    }
    if($Acao =="salvaRamal"){
        $id = (int) filter_input(INPUT_GET, 'numero');
        $Nome = filter_input(INPUT_GET, 'usuario');  //$_REQUEST["nome"];
        $NomeCompl = filter_input(INPUT_GET, 'nomecompl');
        $Setor = filter_input(INPUT_GET, 'setor');
        $Ramal = filter_input(INPUT_GET, 'ramal');
        $UsuLogado = $_SESSION["usuarioID"]; //$_REQUEST["usulogado"];
        $Erro = 0;
        $row = 0;
        if($id !== 0){ // edição
            $Sql = pg_query($Conec, "UPDATE ".$xProj.".ramais_int SET nomeusu = '$Nome', nomecompl = '$NomeCompl', setor = '$Setor', ramal = '$Ramal', usumodif = $UsuLogado, datamodif = NOW() WHERE codtel = $id");
            if(!$Sql){
                $Erro = 1;
            }
        }else{ // inserção
            $rs = pg_query($Conec, "SELECT nomeusu, nomecompl, setor, ramal FROM ".$xProj.".ramais_int WHERE nomeusu = '$Nome' And nomecompl = '$NomeCompl'");
            $row = pg_num_rows($rs);
            if($row > 0){
                $Erro = 2; // nome já existe
            }else{
                $rsCod = pg_query($Conec, "SELECT MAX(codtel) FROM ".$xProj.".ramais_int");
                $tblCod = pg_fetch_row($rsCod);
                $Codigo = $tblCod[0];
                $CodigoNovo = ($Codigo+1);

                $Sql = pg_query($Conec, "INSERT INTO ".$xProj.".ramais_int (codtel, nomeusu, nomecompl, setor, ramal, datains, usuins) 
                VALUES($CodigoNovo, '$Nome', '$NomeCompl', '$Setor', '$Ramal', NOW(), $UsuLogado)");
                if(!$Sql){
                    $Erro = 1;
                }
            }
        }
        $var = array("coderro"=>$Erro, "id"=>$id, "row"=>$row);
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