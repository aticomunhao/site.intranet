<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
date_default_timezone_set('America/Sao_Paulo'); 
$Hoje = date('Y/m/d');
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    $UsuIns = $_SESSION['usuarioID'];

    if($Acao == "buscaNumero"){
        $Erro = 0;
        $Prox = 1;
        $Tipo = (int) filter_input(INPUT_GET, 'tipo');
        $rs = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".contratos".$Tipo."");
        $tbl = pg_fetch_row($rs);
        $Prox = ($tbl[0]+1);
        $var = array("coderro"=>$Erro, "contratoNum"=>str_pad($Prox, 3, 0, STR_PAD_LEFT));
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "buscaContrato"){
        $Cod = (int) filter_input(INPUT_GET, 'codigo');
        $Tipo = (int) filter_input(INPUT_GET, 'tipo');
        $Erro = 0;
        $rs = pg_query($Conec, "SELECT TO_CHAR(dataassinat, 'DD/MM/YYYY'), TO_CHAR(datavencim, 'DD/MM/YYYY'), TO_CHAR(dataaviso, 'DD/MM/YYYY'), numcontrato, codsetor, codempresa, objetocontr, observ, notific, diasnotific, vigencia, pararaviso, emvigor FROM ".$xProj.".contratos".$Tipo." WHERE id = $Cod");
        if(!$rs){
            $Erro = 1;
            $var = array("coderro"=>$Erro);
        }else{
            $row = pg_num_rows($rs);
            if($row > 0){
                $tbl = pg_fetch_row($rs);
                $Vig = str_replace("meses", "", $tbl[10]);
                $Vigencia = str_replace("mês", "", $Vig);
                $var = array("coderro"=>$Erro, "dataassinat"=>$tbl[0], "datavencim"=>$tbl[1], "dataaviso"=>$tbl[2], "numcontrato"=>$tbl[3], "codsetor"=>$tbl[4], "codempresa"=>$tbl[5], "objcontrato"=>$tbl[6], "obs"=>$tbl[7], "notific"=>$tbl[8], "diasnotific"=>$tbl[9], "vigencia"=>trim($Vigencia), "pararaviso"=>$tbl[11], "emvigor"=>$tbl[12]);
            }
        }
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "calcaviso"){
        $Erro = 0;
        $DataVenc = addslashes(filter_input(INPUT_GET, 'vencim')); 
        $DiasAntec = (int) filter_input(INPUT_GET, 'diasanteced'); 
        
        if($DiasAntec > 0){
            $DataAviso = calcRecuaPrazoDias($DataVenc, $DiasAntec); // (vencimento - dias de antecedência - abrealas.php)
        }else{
            $DataAviso = "";
        }

        //Procura o prazo
        $PrazoM = "";
        $PrazoD = "";
        $DataA = addslashes(filter_input(INPUT_GET, 'assinat')); 
        $DataAss = implode("/", array_reverse(explode("/", $DataA)));
        $DataV = implode("/", array_reverse(explode("/", $DataVenc)));
        pg_query($Conec, "UPDATE ".$xProj.".poslog SET calcdata1 = '$DataV', calcdata2 = '$DataAss' WHERE pessoas_id = $UsuIns");

        $rs = pg_query($Conec, "SELECT extract('Year' FROM AGE(calcdata1, calcdata2)), extract('Month' FROM AGE(calcdata1, calcdata2)), extract('Day' FROM AGE(calcdata1, calcdata2)) 
        FROM ".$xProj.".poslog WHERE pessoas_id = $UsuIns ");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $PrazoM = ($tbl[0]*12)+$tbl[1]; // prazo em meses - $tbl[0] * 12 + $tbl[1]
            $PrazoD = $tbl[2];
        }

        $var = array("coderro"=>$Erro, "dataaviso"=>$DataAviso, "dataassinat"=>$DataA, "antecip"=>$DiasAntec, "prazom"=>$PrazoM, "prazod"=>$PrazoD);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "calcprazo"){
        $Erro = 0;
        $DataAss = addslashes(filter_input(INPUT_GET, 'assinat')); 
        $Data = implode("/", array_reverse(explode("/", $DataAss)));
        $Prazo = filter_input(INPUT_GET, 'prazoselec'); 
        $DiasAntec = filter_input(INPUT_GET, 'diasanteced'); 
        $DataFim = "31/12/3000";
        $PrazoDias = 0;

        pg_query($Conec, "UPDATE ".$xProj.".poslog SET calcdata1 = '$Data' WHERE pessoas_id = $UsuIns");

        $rs = pg_query($Conec, "SELECT to_char(calcdata1 + interval '".$Prazo." months', 'dd/mm/yyyy') FROM ".$xProj.".poslog WHERE pessoas_id = $UsuIns");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $DataFim = $tbl[0];
            $PrazoDias = calculaDifDias($DataAss, $DataFim);
        }

        $DataAviso = "";
        if($DiasAntec != "" && $DiasAntec > 0){
            $DataAviso = calcRecuaPrazoDias($DataFim, $DiasAntec); // (vencimento - dias de antecedência - abrealas.php)
        }

        $var = array("coderro"=>$Erro, "datafinal"=>$DataFim, "prazodias"=>$PrazoDias, "dataaviso"=>$DataAviso);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "salvaContrato"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo');
        $Tipo = (int) filter_input(INPUT_GET, 'tipo');
        $NumContrato = addslashes(filter_input(INPUT_GET, 'numcontrato')); 
        $Setor = (int) filter_input(INPUT_GET, 'setor');
        $NumEmpresa = (int) filter_input(INPUT_GET, 'empresanum');
        $Objeto = addslashes(filter_input(INPUT_GET, 'objeto')); 
        $Obs = addslashes(filter_input(INPUT_GET, 'observ')); 
        $Prazo = (int) filter_input(INPUT_GET, 'prazo');
        $guardaPrazo = filter_input(INPUT_GET, 'guardaPrazo');

        if($Prazo == 0){
            $Prazo = "";
        }else{
            if($Prazo > 1){
                $Prazo = $Prazo." meses";
            }else{
                $Prazo = $Prazo." mês";
            }
        }
        if($guardaPrazo != ""){
            $Prazo = $guardaPrazo;
        }

        $DataA = addslashes(filter_input(INPUT_GET, 'assinat')); 
        $DataAssinat = implode("/", array_reverse(explode("/", $DataA))); // inverte o formato da data para y/m/d

        $DataV = addslashes(filter_input(INPUT_GET, 'vencim')); 
        $DataVenc = implode("/", array_reverse(explode("/", $DataV))); // inverte o formato da data para y/m/d

        $Notif = (int) filter_input(INPUT_GET, 'notific');
        $ParaAv = (int) filter_input(INPUT_GET, 'pararaviso');

        $DiasAntec = (int) filter_input(INPUT_GET, 'anteced');

        $DataAviso = "31/12/3000";
        if($DiasAntec > 0){
            $DataAv = calcRecuaPrazoDias($DataV, $DiasAntec); // (vencimento - dias de antecedência - abrealas.php)
            $DataAviso = implode("/", array_reverse(explode("/", $DataAv)));
        }

        $PrazoDias = 0;
        if($Cod == 0){ // inserir novo
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".contratos".$Tipo."");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = $Codigo+1; 

            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".contratos".$Tipo." (id, dataassinat, datavencim, dataaviso, numcontrato, codsetor, codempresa, objetocontr, observ, notific, pararaviso, diasnotific, ativo, usuins, datains, vigencia ) 
            VALUES ($CodigoNovo, '$DataAssinat', '$DataVenc', '$DataAviso', '$NumContrato', $Setor, $NumEmpresa, '$Objeto', '$Obs', $Notif, $ParaAv, $DiasAntec, 1, $UsuIns, NOW(), '$Prazo' )");
            if($Prazo == "" || $Prazo == 0){
//                    $rs1 = pg_query($Conec, "UPDATE ".$xProj.".contratos1 SET vigencia = (extract('Year' from AGE(datavencim, dataassinat))*12) + extract('Month' from AGE(datavencim, dataassinat)) WHERE id = $CodigoNovo");
                $PrazoDias = calculaDifDias($DataA, $DataV);
                if($PrazoDias < 60){
                    pg_query($Conec, "UPDATE ".$xProj.".contratos".$Tipo." SET vigencia = CONCAT('$PrazoDias', ' dias') WHERE id = $Cod"); 
                }
            }
        }else{  // atualizar
            $rs = pg_query($Conec, "UPDATE ".$xProj.".contratos".$Tipo." SET dataassinat = '$DataAssinat', datavencim = '$DataVenc', vigencia = '$Prazo', 
            dataaviso = '$DataAviso', numcontrato = '$NumContrato', codsetor = $Setor, codempresa = $NumEmpresa, 
            objetocontr = '$Objeto', observ = '$Obs', notific = $Notif, pararaviso = $ParaAv, diasnotific = $DiasAntec, ativo = 1, usuedit =  $UsuIns, dataedit = NOW() WHERE id = $Cod");
            if($Prazo == "" || $Prazo == 0){
                $PrazoDias = calculaDifDias($DataA, $DataV);
                if($PrazoDias < 60){
                    pg_query($Conec, "UPDATE ".$xProj.".contratos".$Tipo." SET vigencia = CONCAT('$PrazoDias', ' dias') WHERE id = $Cod"); 
                }
            }
        }
        $var = array("coderro"=>$Erro, "prazodias"=>$PrazoDias);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "buscausuario"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); //id de poslog

        $rs1 = pg_query($Conec, "SELECT contr, fisc_contr, cpf FROM ".$xProj.".poslog WHERE pessoas_id = $Cod");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            $tbl1 = pg_fetch_row($rs1);
            $var = array("coderro"=>$Erro, "registro"=>$tbl1[0], "fisccontratos"=>$tbl1[1], "cpf"=>$tbl1[2]);
        }else{
            $Erro = 1;
            $var = array("coderro"=>$Erro);
        }        
        $responseText = json_encode($var);
        echo $responseText;
    }
    
    if($Acao == "configMarcaContrato"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id de poslog
        $Campo = filter_input(INPUT_GET, 'campo');
        $Valor = (int) filter_input(INPUT_GET, 'valor');

        if($Campo == "contr" && $Valor == 0){
            $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE contr = 1");
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

    if($Acao == "buscacpfusuario"){
        $Erro = 0;
        $Cpf = filter_input(INPUT_GET, 'cpf'); 
        $Cpf1 = addslashes($Cpf);
        $Cpf2 = str_replace(".", "", $Cpf1);
        $GuardaCpf = str_replace("-", "", $Cpf2);

        $rs1 = pg_query($Conec, "SELECT contr, fisc_contr, cpf, pessoas_id FROM ".$xProj.".poslog WHERE cpf = '$GuardaCpf'");
        if(!$rs1){
            $Erro = 1;
            $var = array("coderro"=>$Erro);
        }
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            $tbl1 = pg_fetch_row($rs1);
            $var = array("coderro"=>$Erro, "registro"=>$tbl1[0], "fisccontratos"=>$tbl1[1], "cpf"=>$tbl1[2], "PosCod"=>$tbl1[3]);
        }else{
            $Erro = 2;
            $var = array("coderro"=>$Erro);
        }        
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "apagaContrato"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo');
        $Tipo = (int) filter_input(INPUT_GET, 'tipo');
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".contratos".$Tipo." SET ativo = 0 WHERE id = $Cod");
        if(!$rs1){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "buscaempresa"){
        $Cod = (int) filter_input(INPUT_GET, 'codigo');
        $Erro = 0;
        $rs = pg_query($Conec, "SELECT empresa FROM ".$xProj.".contrato_empr WHERE id = $Cod And ativo = 1");
        if(!$rs){
            $Erro = 1;
        }
        $tbl = pg_fetch_row($rs);
        $var = array("coderro"=>$Erro, "nome"=>$tbl[0] );
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao=="salvanomeempresa"){
        $Cod = (int) filter_input(INPUT_GET, 'codigo');
        $Nome = filter_input(INPUT_GET, 'nomeempresa');
        $Erro = 0;
        if($Cod > 0){ // salvar
            $rs = pg_query($Conec, "UPDATE ".$xProj.".contrato_empr SET empresa = '$Nome', usuedit = ".$_SESSION['usuarioID'].", dataedit = NOW() WHERE id = $Cod ");
        }else{ // inserir
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".contrato_empr");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1);
            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".contrato_empr (id, empresa, ativo, usuins, datains) VALUES ($CodigoNovo, '$Nome', 1, ".$_SESSION['usuarioID'].", NOW() )");
        }
        if(!$rs){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao == "buscarelempresas"){  // vem de controleAr.php
        $rsEmpr = pg_query($Conec, "SELECT id, empresa FROM ".$xProj.".contrato_empr WHERE ativo = 1 ORDER BY empresa");
    
        while ($tbl = pg_fetch_row($rsEmpr)){
           $Empr[] = array(
           'Cod' => $tbl[0],
           'Nome' => $tbl[1]);
        }
        $responseText = json_encode($Empr);
        echo $responseText;
     }

     if($Acao == "statuscontrato"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo');
        $Tipo = (int) filter_input(INPUT_GET, 'tipo');
        $Valor = (int) filter_input(INPUT_GET, 'valor');
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".contratos".$Tipo." SET emvigor = $Valor WHERE id = $Cod");
        if(!$rs1){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }
}