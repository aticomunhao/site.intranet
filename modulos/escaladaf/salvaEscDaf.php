<?php
session_start(); 
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
$Acao = "";
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    $Hoje = date('d/m/Y');
    $UsuIns = $_SESSION['usuarioID'];
}


if($Acao == "buscausuario"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); //id de poslog
    //bens, fiscbens, soinsbens

    $rs1 = pg_query($Conec, "SELECT eft_daf, esc_daf, cpf, enc_escdaf, chefe_escdaf FROM ".$xProj.".poslog WHERE pessoas_id = $Cod");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($rs1);
        $var = array("coderro"=>$Erro, "eft"=>$tbl1[0], "esc"=>$tbl1[1], "cpf"=>$tbl1[2], "encarreg"=>$tbl1[3], "chefeadm"=>$tbl1[4]);
    }else{
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }        
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "buscacpf"){
    $Erro = 0;
    $Cpf = filter_input(INPUT_GET, 'cpf'); 
    $Cpf1 = addslashes($Cpf);
    $Cpf2 = str_replace(".", "", $Cpf1);
    $GuardaCpf = str_replace("-", "", $Cpf2);

    $rs1 = pg_query($Conec, "SELECT eft_daf, esc_daf, cpf, pessoas_id, enc_escdaf, chefe_escdaf FROM ".$xProj.".poslog WHERE cpf = '$GuardaCpf'");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($rs1);
        $var = array("coderro"=>$Erro, "eft"=>$tbl1[0], "esc"=>$tbl1[1], "cpf"=>$tbl1[2], "PosCod"=>$tbl1[3], "encarreg"=>$tbl1[4], "chefeadm"=>$tbl1[5]);
    }else{
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }        
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "configMarcaEscala"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id de poslog
    $Campo = filter_input(INPUT_GET, 'campo');
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    if($Campo == "esc_daf" && $Valor == 0){
        $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE esc_daf = 1");
        $row = pg_num_rows($rs);
        if($row == 1){
            $Erro = 2;
            $var = array("coderro"=>$Erro);
            $responseText = json_encode($var);
            echo $responseText;
            return false;
        }
    }

    $rs1 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET $Campo = '$Valor', datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
    if(!$rs1){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="marcaPartic"){
    $Erro = 0;
    $CodPartic = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id
    $rs0 = pg_query($Conec, "SELECT daf_marca FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic");
    $tbl0 = pg_fetch_row($rs0);
    $Marca = (int) $tbl0[0];
    if($Marca == 0){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET daf_marca = 1 WHERE pessoas_id = $CodPartic");
    }else{
        $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET daf_marca = 0 WHERE pessoas_id = $CodPartic");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaTurno"){
    $Erro = 0;
    $CodPartic = (int) filter_input(INPUT_GET, 'codpartic'); // pessoas_id
    $CodTurno = (int) filter_input(INPUT_GET, 'codturno');
    $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET daf_turno = $CodTurno WHERE pessoas_id = $CodPartic");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvamesano"){
    $Erro = 0;
    $MesAno = addslashes(filter_input(INPUT_GET, 'mesano'));
    $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET mes_escdaf = '$MesAno' WHERE pessoas_id = $UsuIns");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="insParticipante"){
    $Erro = 0;
    $CodDia = (int) filter_input(INPUT_GET, 'diaIdEscala'); // pessoas_id
    pg_query($Conec, "DELETE FROM ".$xProj.".escaladaf_ins WHERE escaladaf_id = $CodDia;");

    $rs0 = pg_query($Conec, "SELECT dataescala FROM ".$xProj.".escaladaf WHERE id = $CodDia");
    $row0 = pg_num_rows($rs0);
    if($row0 > 0){
        $tbl0 = pg_fetch_row($rs0);
        $DataEscala = $tbl0[0];
    }else{
        $DataEscala = "";
    }
    $rs = pg_query($Conec, "SELECT pessoas_id, daf_turno, letra, horaturno, destaq 
    FROM ".$xProj.".poslog LEFT JOIN ".$xProj.".escaladaf_turnos ON ".$xProj.".poslog.daf_turno = ".$xProj.".escaladaf_turnos.id
    WHERE ".$xProj.".poslog.ativo = 1 And eft_daf = 1 And daf_marca = 1 ");
    $row = pg_num_rows($rs);
    if($row > 0){
        while($tbl = pg_fetch_row($rs)){
            $CodPartic = $tbl[0];
            $Turno = $tbl[1];
            if($Turno == 0){
                $Erro = 2; 
                $var = array("coderro"=>$Erro);
                $responseText = json_encode($var);
                echo $responseText;
                return;       
            }
            $Letra = $tbl[2];
            $DescTurno = $tbl[3];
            $Destaq = $tbl[4];
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_ins");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1);

            pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_ins (id, escaladaf_id, dataescalains, poslog_id, letraturno, turnoturno, destaque, usuins, datains) 
            VALUES($CodigoNovo, $CodDia, '$DataEscala', $CodPartic, '$Letra', '$DescTurno', $Destaq, $UsuIns, NOW() )");
        }
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaordem"){
    $Erro = 0;
    $Cod = filter_input(INPUT_GET, 'codigo');
    $Valor = filter_input(INPUT_GET, 'valor');
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET ordemletra = $Valor WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaletra"){
    $Erro = 0;
    $Cod = filter_input(INPUT_GET, 'codigo');
    $Valor = filter_input(INPUT_GET, 'valor');
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET letra = '$Valor' WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaturno"){
    $Erro = 0;
    $Cod = filter_input(INPUT_GET, 'codigo');
    $Valor = addslashes(filter_input(INPUT_GET, 'valor'));
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET horaturno = '$Valor' WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="buscanota"){
    $Erro = 0;
    $Cod = filter_input(INPUT_GET, 'codigo');
    $rs = pg_query($Conec, "SELECT numnota, textonota FROM ".$xProj.".escaladaf_notas WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $tbl = pg_fetch_row($rs);
        $var = array("coderro"=>$Erro, "numnota"=>$tbl[0], "textonota"=>$tbl[1]);    
    }
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="buscanumnota"){
    $Erro = 0;
    $Cod = filter_input(INPUT_GET, 'codigo');
    $rs = pg_query($Conec, "SELECT MAX(numnota) FROM ".$xProj.".escaladaf_notas");
    if(!$rs){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $tbl = pg_fetch_row($rs);
        $Num = $tbl[0];
        $NumNovo = ($Num+1);
        $var = array("coderro"=>$Erro, "numnota"=>$NumNovo);    
    }
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvanota"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Num = filter_input(INPUT_GET, 'numnota');
    $Texto = addslashes(filter_input(INPUT_GET, 'textonota'));
    if($Cod > 0){ // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_notas SET numnota = $Num, textonota = '$Texto' WHERE id = $Cod");
    }else{ // inserir
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_notas");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_notas (id, numnota, textonota, usuins, datains) 
        VALUES($CodigoNovo, $Num, '$Texto', $UsuIns, NOW() )");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="insereletra"){
    $Erro = 0;
    $Ordem = (int) filter_input(INPUT_GET, 'ordem');
    $Letra = filter_input(INPUT_GET, 'insletra');
    $Turno = addslashes(filter_input(INPUT_GET, 'insturno'));

    $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_turnos");
    $tblCod = pg_fetch_row($rsCod);
    $Codigo = $tblCod[0];
    $CodigoNovo = ($Codigo+1);

    $rs = pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, ordemletra, letra, horaturno, usuins, datains) 
    VALUES($CodigoNovo, $Ordem, '$Letra', '$Turno', $UsuIns, NOW() )");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="apagaletra"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo');

    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET ativo = 0 WHERE id = $Cod ");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}