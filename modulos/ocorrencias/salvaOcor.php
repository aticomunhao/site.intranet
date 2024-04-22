<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    header("Location: /cesb/index.php");
}
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
$Acao = "";
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"]; 
}
if($Acao=="salvaOcor"){
    $Data = addslashes($_REQUEST['dataocor']);  // filter_input(INPUT_GET, 'datains');
    $Texto = addslashes($_REQUEST['textoocorrencia']);
    $Codigo = (int) filter_input(INPUT_GET, 'codigo');

    $ProcAno = explode("/","$Data");
    $d = $ProcAno[0];
    $m = $ProcAno[1];
    $y = $ProcAno[2];

    $Erro = 0;
    $CodigoNovo = 0;
    if($Codigo == 0){
        $UsuIns = $_SESSION['usuarioID'];
        $CodSetor = $_SESSION['CodSetorUsu'];
        $rs0 = pg_query($Conec, "SELECT codocor FROM ".$xProj.".ocorrencias WHERE to_char(dataocor, 'YYYY') = '$y'");
        $row0 = pg_num_rows($rs0);
    
        $Num = str_pad(($row0+1), 4, "0", STR_PAD_LEFT);

        $rsCod = pg_query($Conec, "SELECT MAX(codocor) FROM ".$xProj.".ocorrencias");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = $Codigo+1; 

        $Sql = pg_query($Conec, "INSERT INTO ".$xProj.".ocorrencias (codocor ,usuins, dataocor, datains, codsetor, numocor, ocorrencia) 
        VALUES($CodigoNovo, $UsuIns, TO_DATE('$Data', 'DD/MM/YYYY'), NOW(), $CodSetor, CONCAT('$Num', '/', '$y'), '$Texto')");
        if(!$Sql){
            $Erro = 1;
        }else{
//            $CodigoNovo = mysqli_insert_id($Conec); // obtem o número AUTO_INCREMENT da operação INSERT realizada
            $rsCod = pg_query($Conec, "SELECT MAX(codocor) FROM ".$xProj.".ocorrencias");
            $tblCod = pg_fetch_row($rsCod);
            $CodigoNovo = $tblCod[0];

            //acertar ideogramas
            pg_query($Conec, "UPDATE ".$xProj.".ocorrideogr SET coddaocor = $CodigoNovo WHERE codprov = ".$_SESSION['usuarioID']." And coddaocor = 0");
            pg_query($Conec, "UPDATE ".$xProj.".ocorrideogr SET codprov = 0 WHERE codprov = ".$_SESSION['usuarioID']);
        }
    }else{
        $Sql = pg_query($Conec, "UPDATE ".$xProj.".ocorrencias SET dataocor = TO_DATE('$Data', 'DD/MM/YYYY'), ocorrencia = '$Texto', usumodif = ".$_SESSION['usuarioID'].", datamodif = NOW() WHERE codocor = $Codigo");
        //acertar ideogramas
        pg_query($Conec, "UPDATE ".$xProj.".ocorrideogr SET coddaocor = $Codigo WHERE codprov = ".$_SESSION['usuarioID']." And coddaocor = 0");
        pg_query($Conec, "UPDATE ".$xProj.".ocorrideogr SET codprov = 0 WHERE codprov = ".$_SESSION['usuarioID']);
    }

    $var = array("coderro"=>$Erro, "codigonovo"=>$CodigoNovo);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvaIdeogr"){
    $CodOcor = (int) filter_input(INPUT_GET, 'codOcorr');
    $Src = filter_input(INPUT_GET, 'source');
    $Erro = 0;
    $CodProv = $_SESSION['usuarioID'];
    //lançamento é feito antes de salvar a ocorr para guardar a imagem - codideo é apagado se a ocorrência não for salva
    $rsCod = pg_query($Conec, "SELECT MAX(codideo) FROM ".$xProj.".ocorrideogr");
    $tblCod = pg_fetch_row($rsCod);
    $Codigo = $tblCod[0];
    $CodigoNovo = $Codigo+1; 
    $Sql = pg_query($Conec, "INSERT INTO ".$xProj.".ocorrideogr (codideo, coddaocor, descideo, codprov) VALUES($CodigoNovo, $CodOcor, '$Src', $CodProv)"); // or die ("Faltam Parâmetros" . mysqli_error($Conec));
    if(!$Sql){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "codOcorr"=>$CodOcor, "testeUniq"=>$CodProv);
     $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="sairSemSalvar"){
    $Erro = 0;
    //limpa ideogramas salvos 
    $Sql = pg_query($Conec, "DELETE FROM ".$xProj.".ocorrideogr WHERE codprov = ".$_SESSION['usuarioID']);
    if(!$Sql){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="buscaOcorr"){
    $CodOcor = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $Sql = pg_query($Conec, "SELECT to_char(dataocor, 'DD/MM/YYYY'), ocorrencia, usuins FROM ".$xProj.".ocorrencias WHERE codocor = $CodOcor");
    if(!$Sql){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $Tbl = pg_fetch_row($Sql);
        $Data = $Tbl[0];
        $Texto = $Tbl[1];
        $CodUsuIns = $Tbl[2];
        $NomeIns = "";
        $rs1 = pg_query($ConecPes, "SELECT nome_completo FROM ".$xPes.".pessoas WHERE id = $CodUsuIns");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            $tbl1 = pg_fetch_row($rs1);
            $NomeIns = $tbl1[0];
        }else{
            $NomeIns = "";
        }
        $var = array("coderro"=>$Erro, "data"=>$Data, "texto"=>$Texto, "nomeusuins"=>$NomeIns);
    }
     $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="apagaIdeogr"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $Sql = pg_query($Conec, "DELETE FROM ".$xProj.".ocorrideogr WHERE codideo = $Cod");
    if(!$Sql){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}