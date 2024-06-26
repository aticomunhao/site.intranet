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

if($Acao=="buscadados"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT num_ap, localap FROM ".$xProj.".controle_ar3 WHERE id = $Cod");
    $row = pg_num_rows($rs);
    if(!$rs){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $tbl = pg_fetch_row($rs);
        if(!is_null($tbl[1])){
            $Local = $tbl[1];
        }else{
            $Local = "";
        }
        $var = array("coderro"=>$Erro, "apar"=>str_pad($tbl[0], 3, 0, STR_PAD_LEFT), "local"=>$Local );
    }

    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvadados"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Local = filter_input(INPUT_GET, 'localap');
    $Empresa = (int) filter_input(INPUT_GET, 'empresa');

    $Erro = 0;
    $rs0 = pg_query($Conec, "SELECT MAX(num_ap) FROM ".$xProj.".controle_ar3");
    $tbl0 = pg_fetch_row($rs0);
    $Prox = ($tbl0[0]+1);
    $rs = pg_query($Conec, "INSERT INTO ".$xProj.".controle_ar3 (num_ap, localap, empresa_id) VALUES ($Prox, '$Local', $Empresa)");
    
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, 'empresa'=>$Empresa);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="buscanumero"){
    $Erro = 0;
    $Prox = 1;
    $rs = pg_query($Conec, "SELECT MAX(num_ap) FROM ".$xProj.".controle_ar3");
    $tbl = pg_fetch_row($rs);
    $Prox = ($tbl[0]+1);

    $var = array("coderro"=>$Erro, "apar"=>str_pad($Prox, 3, 0, STR_PAD_LEFT));
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="buscadata"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // id de visitas_ar3
    $Erro = 0;
    //controle_id, datavis, , , , tipovis, empresa_id,  , nometec, , , usuins, datains, ativo
    $rs = pg_query($Conec, "SELECT num_ap, localap, to_char(datavis, 'DD/MM/YYYY'), nometec, ".$xProj.".visitas_ar3.empresa_id, tipovis, 
    to_char(acionam, 'DD/MM/YYYY  HH24:MI'), contato, defeito, to_char(atendim, 'DD/MM/YYYY  HH24:MI'), acompanh, diagtec, svcrealizado, to_char(conclus, 'DD/MM/YYYY') 
    FROM ".$xProj.".controle_ar3 INNER JOIN ".$xProj.".visitas_ar3 ON ".$xProj.".controle_ar3.id = ".$xProj.".visitas_ar3.controle_id 
    WHERE ".$xProj.".visitas_ar3.id = $Cod");
    $row = pg_num_rows($rs);
    if(!$rs){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $tbl = pg_fetch_row($rs);
        if($tbl[2] == "01/01/1500"){
            $Data = "";
        }else{
            $Data = $tbl[2];
        }
        //  6 acionam  9 atendim  13 conclus
        if(substr($tbl[6], 0, 10) == "01/01/1500" || substr($tbl[6], 0, 10) == "31/12/3000"){
            $DataAc = "";
        }else{
            $DataAc = $tbl[6];
        }
        if(substr($tbl[9], 0, 10) == "01/01/1500" || substr($tbl[9], 0, 10) == "31/12/3000"){
            $DataAt = "";
        }else{
            $DataAt = $tbl[9];
        }
        if(substr($tbl[13], 0, 10) == "01/01/1500" || substr($tbl[13], 0, 10) == "31/12/3000"){
            $DataConc = "";
        }else{
            $DataConc = $tbl[13];
        }
        $var = array("coderro"=>$Erro, "cod"=>$Cod, "apar"=>str_pad($tbl[0], 3, 0, STR_PAD_LEFT), "local"=>$tbl[1], "data"=>$Data, "nome"=>$tbl[3], "empresa"=>$tbl[4], "tipomanut"=>$tbl[5], "acionam"=>$DataAc, "nomecontactado"=>$tbl[7], "defeito"=>$tbl[8], "atendim"=>$DataAt, "acompanh"=>$tbl[10], "diagtec"=>$tbl[11], "svcrealizado"=>$tbl[12], "dataConclus"=>$DataConc );
    }
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvadatainsprevent"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Dat = addslashes(filter_input(INPUT_GET, 'datavis'));
    $Nome = filter_input(INPUT_GET, 'nometec');
    $Empresa = (int) filter_input(INPUT_GET, 'empresa');
    $Tipo = (int) filter_input(INPUT_GET, 'tipomanut');
    $InsEdit = (int) filter_input(INPUT_GET, 'insedit');
    $AcompPrev = filter_input(INPUT_GET, 'acompPrevent');

    if($Dat == ""){
        $Data = "1500-01-01";
    }else{
        $Data = implode("-", array_reverse(explode("/", $Dat))); // inverte o formato da data para y/m/d
    }
    $Erro = 0;
    if($InsEdit == 0){ // inserindo: é o id de controle_ar3
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".visitas_ar3");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".visitas_ar3 (id, controle_id, datavis, nometec, usuins, datains, empresa_id, ativo, tipovis, acompanh) 
        VALUES ($CodigoNovo, $Cod, '$Data', '$Nome', ".$_SESSION["usuarioID"].", NOW(), $Empresa, 1, $Tipo, '$AcompPrev')");
    }else{ // salvando: é o id de visitar_ar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".visitas_ar3 SET datavis = '$Data', nometec = '$Nome', dataedit = NOW(), usuedit = ".$_SESSION["usuarioID"].", acompanh = '$AcompPrev' WHERE id = $Cod ");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "codigo"=>$Cod, "insedit"=>$InsEdit);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvamanutcorret"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // id de visitas_ar3
    $InsEdit = (int) filter_input(INPUT_GET, 'insedit');
    $Tipo = (int) filter_input(INPUT_GET, 'tipomanut');
    $Empresa = (int) filter_input(INPUT_GET, 'empresa');

    $DataAc = addslashes(filter_input(INPUT_GET, 'dataAcionam'));
    if($DataAc == ""){
        $DataAc = "1500/01/01 00:00";
    }
    $DataAt = addslashes(filter_input(INPUT_GET, 'dataAtendim'));
    if($DataAt == ""){
        $DataAt = "1500/01/01 00:00";
    }
    $DataConc = addslashes(filter_input(INPUT_GET, 'dataConclus'));
    if($DataConc == ""){
        $DataConc = "1500/01/01 00:00";
    }

    $DataAcio = substr($DataAc, 0, 10);
    $DataAcionam = implode("-", array_reverse(explode("/", $DataAcio)));
    $HoraAcionam = substr($DataAc, 11, 6);
    $Acionam = $DataAcionam." ".$HoraAcionam;

    $DataAtend = substr($DataAt, 0, 10);
    $DataAtendim = implode("-", array_reverse(explode("/", $DataAtend)));
    $HoraAtendim = substr($DataAt, 11, 6);
    $Atendim = $DataAtendim." ".$HoraAtendim;

    $Concl = substr($DataConc, 0, 10);
    $Conclus = implode("-", array_reverse(explode("/", $Concl)));

    $DataVis = $Acionam;

    $NomeContactado = filter_input(INPUT_GET, 'nomecontactado');
    $NomeAcompanhante = filter_input(INPUT_GET, 'nomeAcompanhante');
    $NomeTecnico = filter_input(INPUT_GET, 'nomeTecnicoEmpresa');

    $Defeito = filter_input(INPUT_GET, 'defeito');
    $Diagnostico = filter_input(INPUT_GET, 'diagnostico');
    $SvcRealiz = filter_input(INPUT_GET, 'svcRealizado');

    $Erro = 0;
    if($InsEdit == 0){ // inserindo: é o id de controle_ar3
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".visitas_ar3");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".visitas_ar3 (id, controle_id, datavis, acionam, atendim, conclus, tipovis, empresa_id, contato, acompanh, nometec, defeito, diagtec, svcrealizado, usuins, datains, ativo) 
       VALUES ($CodigoNovo, $Cod, '$DataVis', '$Acionam', '$Atendim', '$Conclus', $Tipo, $Empresa, '$NomeContactado', '$NomeAcompanhante', '$NomeTecnico', '$Defeito', '$Diagnostico', '$SvcRealiz', ".$_SESSION["usuarioID"].", NOW(), 1)");
    }else{ // salvando: é o id de visitar_ar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".visitas_ar3 SET datavis = '$DataVis', acionam = '$Acionam', atendim = '$Atendim', conclus = '$Conclus', 
        tipovis = $Tipo, empresa_id = $Empresa, contato =  '$NomeContactado', acompanh = '$NomeAcompanhante', 
        nometec = '$NomeTecnico', defeito = '$Defeito', diagtec = '$Diagnostico', svcrealizado = '$SvcRealiz', usuins = ".$_SESSION["usuarioID"].", datains = NOW(), ativo = 1 
        WHERE id = $Cod");
    }
    if(!$rs){
        $Erro = 1;
    }
    //TO_DATE('$Data', 'DD/MM/YYYY')   HH24:MI      $Usu = str_replace("-", "", $Cpf2); TO_DATE('$DataAcionam', 'DD-MM-YYYY')
    $var = array("coderro"=>$Erro, "data"=>$Acionam, "hora"=>$HoraAcionam, "svcrealiz"=>$SvcRealiz);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvadataedit"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Dat = addslashes(filter_input(INPUT_GET, 'datavis'));
    $Nome = filter_input(INPUT_GET, 'nometec');
    $Empresa = (int) filter_input(INPUT_GET, 'empresa');
    $Tipo = (int) filter_input(INPUT_GET, 'tipomanut');
    if($Dat == ""){
        $Data = "1500-01-01";
    }else{
        $Data = implode("-", array_reverse(explode("/", $Dat))); // inverte o formato da data para y/m/d
    }
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".visitas_ar3 SET datavis = '$Data', nometec = '$Nome', empresa_id = $Empresa, tipovis = $Tipo, ativo = 1, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod");
    
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="buscalocal"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT num_ap, localap, empresa_id FROM ".$xProj.".controle_ar3 WHERE id = $Cod");
    $row = pg_num_rows($rs);
    if(!$rs){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $tbl = pg_fetch_row($rs);
        $var = array("coderro"=>$Erro, "apar"=>str_pad($tbl[0], 3, 0, STR_PAD_LEFT), "local"=>$tbl[1], "empresa"=>$tbl[2] );
    }
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="salvalocal"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Local = filter_input(INPUT_GET, 'local');
    $Empresa = (int) filter_input(INPUT_GET, 'empresa');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".controle_ar3 SET localap = '$Local', empresa_id = $Empresa, usuedit = ".$_SESSION["usuarioID"].", dataedit = NOW() WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao=="apagadata"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".visitas_ar3 SET ativo = 0, usudel = ".$_SESSION["usuarioID"].", datadel = NOW() WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="buscaempresa"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT empresa, valorvisita FROM ".$xProj.".empresas_ar WHERE id = $Cod And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $tbl = pg_fetch_row($rs);
    $var = array("coderro"=>$Erro, "nome"=>$tbl[0], "valor"=>number_format($tbl[1], 2, ",",".")  );
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao=="salvanomeempresa"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Nome = filter_input(INPUT_GET, 'nomeempresa');
    $Valor = str_replace(",", ".", filter_input(INPUT_GET, 'valorvisita'));
    $ValorVis = number_format($Valor, 2, ".",",");

    $Erro = 0;
    if($Cod > 0){ // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".empresas_ar SET empresa = '$Nome', valorvisita = $ValorVis  WHERE id = $Cod ");
    }else{ // inserir
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".empresas_ar");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".empresas_ar (id, empresa) VALUES ($CodigoNovo, '$Nome') ");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "buscarelempresas"){  // vem de controleAr.php
    $rsEmpr = pg_query($Conec, "SELECT id, empresa FROM ".$xProj.".empresas_ar WHERE ativo = 1");

    while ($tbl = pg_fetch_row($rsEmpr)){
       $Empr[] = array(
       'Cod' => $tbl[0],
       'Nome' => $tbl[1]);
    }
    $responseText = json_encode($Empr);
    echo $responseText;
 }