<?php
session_start();
require_once("config/abrealas.php");
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    $Tipo = (int) $_REQUEST["tipo"];
}
require_once("config/gUtils.php"); 

if($Tipo == 1){ // ramais internos

    if($Acao =="buscaNome"){
        $Num = (int) filter_input(INPUT_GET, 'numero'); // id
        $Arq = (int) filter_input(INPUT_GET, 'arquivo'); // arquivo 0 = pessoas   1 - poslog
        $Erro = 0;
        $CodTel = 0;
        $CodSetor = "";
        $SiglaSetor = "";
        $NomeUsual = "";
        $NomeCompl = "";
        $Ramal = "";
        $JaTem = 0;

        if($Num > 0){ // veio um número do select usuários de poslog
            if($Arq == 0){
                $rs = pg_query($ConecPes, "SELECT nome_completo, sexo, nome_resumido FROM ".$xPes.".pessoas WHERE id = $Num");
            }else{
                $rs = pg_query($Conec, "SELECT nomecompl, codsetor, nomeusual FROM ".$xProj.".poslog WHERE id = $Num");
            }
            $row = pg_num_rows($rs);
            if($row > 0){
                $tbl = pg_fetch_row($rs);
                $NomeCompl = $tbl[0];
                if($Arq == 0){
                    $CodSetor = 0;
                }else{
                    $CodSetor = $tbl[1]; 
                }
                $NomeUsual = $tbl[2];
                if($CodSetor > 0){
                    $rs1 = pg_query($Conec, "SELECT siglasetor FROM ".$xProj.".setores WHERE codset = $CodSetor");
                    $row1 = pg_num_rows($rs1);
                    if($row1 > 0){
                        $tbl1 = pg_fetch_row($rs1);
                        $SiglaSetor = $tbl1[0];
                    }
                }
            }
            //Verifica se já foi inserido em ramais_int
            $rs2 = pg_query($Conec, "SELECT codtel, nomeusu, nomecompl, ramal FROM ".$xProj.".ramais_int WHERE poslog_id = $Num");
            $row2 = pg_num_rows($rs2);
            if($row2 > 0){ //pega o codtel de ramais_int
                $JaTem = 1;
                $tbl2 = pg_fetch_row($rs2);
                $CodTel = $tbl2[0];
                $NomeUsual = $tbl2[1];
                $NomeCompl = $tbl2[2];
                $Ramal = $tbl2[3];
            }
        }

        $var = array("coderro"=>$Erro, 'jatem'=>$JaTem, "codtel"=>$CodTel, "codsetor"=>$CodSetor, "siglasetor"=>$SiglaSetor, "nomeusual"=>$NomeUsual, "nomecompleto"=>$NomeCompl, "ramal"=>$Ramal);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="buscaRamal"){
        $Num = (int) filter_input(INPUT_GET, 'numero'); // id
        $Erro = 0;
        $row = 0;

        $Sql = pg_query($Conec, "SELECT nomeusu, nomecompl, codsetor, ramal, poslog_id, setor 
        FROM ".$xProj.".ramais_int 
        WHERE codtel = $Num");
        if(!$Sql){
           $Erro = 1;
        }else{
            $row = pg_num_rows($Sql);
            $Proc = pg_fetch_row($Sql);
        }
        $var = array("coderro"=>$Erro, "usuario"=>$Proc[0], "nomecompleto"=>$Proc[1], "codsetor"=>$Proc[2], "setor"=>$Proc[5], "ramal"=>$Proc[3], "idposlog"=>$Proc[4] );
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="buscaDescSetor"){
        $Num = (int) filter_input(INPUT_GET, 'numero'); // id
        $Erro = 0;
        $row = 0;
        $Sql = pg_query($ConecPes, "SELECT sigla FROM ".$xPes.".setor WHERE id = $Num");
        if(!$Sql){
           $Erro = 1;
        }else{
            $row = pg_num_rows($Sql);
            $Proc = pg_fetch_row($Sql);
        }
        $var = array("coderro"=>$Erro, "descsetor"=>$Proc[0] );
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="salvaRamal"){
        $id = (int) filter_input(INPUT_GET, 'numero');
        $Nom = trim(filter_input(INPUT_GET, 'usuario'));  // nome usual
        if(!is_null($Nom)){
            $NomeU = $Nom;
            $NomeUs = GUtils::normalizarNome($NomeU);  // Normatizar nomes próprios
            $NomeUsu = addslashes($NomeUs);
            $Nome = str_replace('"', "'", $NomeUsu); // substitui aspas duplas por simples
        }else{
            $Nome = "";
        }

        $CodNome = filter_input(INPUT_GET, 'codnomecompl'); // id de poslog
        if($CodNome == ""){
            $CodNome = 0;
        }
        if(is_null($CodNome)){
            $CodNome = 0;
        }
        $NomeC = trim(filter_input(INPUT_GET, 'nomecompleto'));
        $NomeCo = GUtils::normalizarNome($NomeC);  // Normatizar nomes próprios
        $NomeComp = addslashes($NomeCo);
        $NomeCompl = str_replace('"', "'", $NomeComp); // substitui aspas duplas por simples

        $Ramal = filter_input(INPUT_GET, 'ramal');
        $CodSetor = (int) filter_input(INPUT_GET, 'codsetor');
        $DescSetor = filter_input(INPUT_GET, 'setor');


        $UsuLogado = $_SESSION["usuarioID"]; //$_REQUEST["usulogado"];
        $Erro = 0;
        $row = 0;
        if($id != 0){ // já tem em ramais_int - edição
            $Sql = pg_query($Conec, "UPDATE ".$xProj.".ramais_int SET poslog_id = $CodNome, nomeusu = '$Nome', nomecompl = '$NomeCompl', ramal = '$Ramal', setor = '$DescSetor', usumodif = $UsuLogado, datamodif = NOW() WHERE codtel = $id");
            if(!$Sql){
                $Erro = 1;
            }
        }else{ // inserção
            $rsCod = pg_query($Conec, "SELECT MAX(codtel) FROM ".$xProj.".ramais_int");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1);

            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".ramais_int (codtel, nomeusu, nomecompl, ramal, setor, datains, usuins, poslog_id) 
            VALUES($CodigoNovo, '$Nome', '$NomeCompl', '$Ramal', '$DescSetor', NOW(), $UsuLogado, $CodNome) ");
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


if($Tipo == 2){ // ramais externos
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
        $SiglaEmpresa = trim(filter_input(INPUT_GET, 'SiglaEmpresa'));  //$_REQUEST["nome"];
        $NomeEmpresa = trim(filter_input(INPUT_GET, 'NomeEmpresa'));
        $Setor = filter_input(INPUT_GET, 'Setor');
        $TelefoneFixo = filter_input(INPUT_GET, 'TelefoneFixo');
        if(strlen($TelefoneFixo) <= 8){ // telefones de três números, ex 193, 195, etc 
            $TelefoneFixo = str_replace("(", "", $TelefoneFixo);
            $TelefoneFixo = str_replace(")", "", $TelefoneFixo);
            $TelefoneFixo = str_replace(" ", "", $TelefoneFixo);
        }
        $TelefoneCel = filter_input(INPUT_GET, 'TelefoneCel');
        if(strlen($TelefoneCel) <= 8){ // telefones de três números, ex 193, 195, etc 
            $TelefoneCel = str_replace("(", "", $TelefoneCel);
            $TelefoneCel = str_replace(")", "", $TelefoneCel);
            $TelefoneCel = str_replace(" ", "", $TelefoneCel);
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
        $var = array("coderro"=>$Erro, "id"=>$id, "row"=>$row, "telcelleng"=>$TelefoneCel);
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