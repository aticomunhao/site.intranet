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
    $MeuGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]); // meu grupo

    $rs1 = pg_query($Conec, "SELECT eft_daf, esc_daf, cpf, enc_escdaf, chefe_escdaf, esc_grupo FROM ".$xProj.".poslog WHERE pessoas_id = $Cod");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($rs1);
        $Grupo = $tbl1[5];
        $Eft = $tbl1[0]; // Efetivo
        if($Eft == 1 && $Grupo != $MeuGrupo ){
            $Eft = 0;
        }
        $Esc = $tbl1[1]; // Escalante
        if($Esc == 1 && $Grupo != $MeuGrupo ){
            $Esc = 0;
        }
        $var = array("coderro"=>$Erro, "eft"=>$Eft, "esc"=>$Esc, "cpf"=>$tbl1[2], "encarreg"=>$tbl1[3], "chefeadm"=>$tbl1[4]);
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

if($Acao == "configMarcaEscala___"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id de poslog
    $Campo = filter_input(INPUT_GET, 'campo');
    $Valor = (int) filter_input(INPUT_GET, 'valor');

    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]); // meu grupo
    $CodGrupo = parEsc("esc_grupo", $Conec, $xProj, $Cod); // grupo do selecionado
    $SiglaGrupo = "";

//    if($Campo == "esc_daf"){ 
//        if($Valor == 0){
//            pg_query($Conec, "UPDATE ".$xProj.".escala_gr SET enc_escdaf = 0 WHERE id = $NumGrupo");
//        }else{
//            pg_query($Conec, "UPDATE ".$xProj.".escala_gr SET enc_escdaf = $Cod WHERE id = $NumGrupo");
//        }
//    }

    if($Campo == "esc_daf" && $Valor == 0){ // não pode ficar sem escalante
        $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE esc_daf = 1 And esc_grupo = $NumGrupo");
        $row = pg_num_rows($rs);
        if($row == 1){
            $Erro = 2;
            $var = array("coderro"=>$Erro);
            $responseText = json_encode($var);
            echo $responseText;
            return false;
        }
    }

    if($CodGrupo == 0){ // está sem grupo - põe no seu grupo e marca
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_grupo = $NumGrupo, daf_turno = 0, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
        $CodGrupo = $NumGrupo;
    }

    if($CodGrupo == $NumGrupo){ // se for do mesmo grupo
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET $Campo = $Valor, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
        if(!$rs1){
            $Erro = 1;
        }
    }else{
        //Está em outro grupo - Verifica está marcado como efetivo naquele grupo
        $rs2 = pg_query($Conec, "SELECT eft_daf FROM ".$xProj.".poslog WHERE id = $Cod;");
        $tbl2 = pg_fetch_row($rs2);
        $MarcaEft = $tbl2[0];
        if($MarcaEft == 0){ // não está marcado como efetivo -> muda o grupo e marca como efetivo no meu grupo
            pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_grupo = $NumGrupo, $Campo = $Valor, daf_turno = 0, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
            $CodGrupo = $NumGrupo;
        }else{ // está em outro grupo e está marcado como efetivo
            $rs = pg_query($Conec, "SELECT siglagrupo FROM ".$xProj.".escalas_gr WHERE id = $CodGrupo;");
            $row = pg_num_rows($rs);
            if($row > 0){
                $tbl = pg_fetch_row($rs);
                $SiglaGrupo = $tbl[0];
            }
            $Erro = 3;
        }
    }
    if($Valor == 0){ // desmarcar se for o caso
        pg_query($Conec, "UPDATE ".$xProj.".poslog SET $Campo = $Valor, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
    }
    $var = array("coderro"=>$Erro, "codgrupo"=>$CodGrupo, "outrogrupo"=>$SiglaGrupo);
    $responseText = json_encode($var);
    echo $responseText;
}


if($Acao == "configMarcaEscala__"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id de poslog
    $Campo = filter_input(INPUT_GET, 'campo');
    $Valor = (int) filter_input(INPUT_GET, 'valor');

    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]); // meu grupo no poslog
    $CodGrupo = parEsc("esc_grupo", $Conec, $xProj, $Cod); // grupo do selecionado no poslog
    $SiglaGrupo = "";

    if($CodGrupo == 0){ // está sem grupo - põe no seu grupo
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_grupo = $NumGrupo, daf_turno = 0, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
        $CodGrupo = $NumGrupo;
    }

    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]); // meu grupo no poslog
    $CodGrupo = parEsc("esc_grupo", $Conec, $xProj, $Cod); // grupo do selecionado no poslog

    if($CodGrupo == $NumGrupo){
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET $Campo = $Valor, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");

        if($Campo == "esc_daf"){ // verifica se vai ficar algum escalante - não pode ficar sem escalante
            $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE esc_daf = 1 And esc_grupo = $NumGrupo");
            $row = pg_num_rows($rs);
            if($row == 0){
                pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_daf = 1 WHERE pessoas_id = $Cod");
                $Erro = 2;
                $var = array("coderro"=>$Erro);
                $responseText = json_encode($var);
                echo $responseText;
                return false;
            }
        }
    }else{ // está em outro grupo
        //Verifica se está marcado como efetivo naquele grupo
        $rs2 = pg_query($Conec, "SELECT eft_daf, esc_daf FROM ".$xProj.".poslog WHERE pessoas_id = $Cod;");
        $tbl2 = pg_fetch_row($rs2);
        $MarcaEft = $tbl2[0];
        $MarcaEsc = $tbl2[1];
        if($MarcaEft == 0 && $MarcaEsc == 0){ // não está marcado como efetivo nem como escalange -> muda o grupo e marca como efetivo no meu grupo
            pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_grupo = $NumGrupo, $Campo = $Valor, daf_turno = 0, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
            $CodGrupo = $NumGrupo;
        }
        if($MarcaEft == 1 || $MarcaEsc == 1){ // está em outro grupo e está marcado como efetivo ou escalante
            $rs = pg_query($Conec, "SELECT siglagrupo FROM ".$xProj.".escalas_gr WHERE id = $CodGrupo;");
            $row = pg_num_rows($rs);
            if($row > 0){
                $tbl = pg_fetch_row($rs);
                $SiglaGrupo = $tbl[0];
            }
            $Erro = 3;
        }
    }

    $var = array("coderro"=>$Erro, "codgrupo"=>$CodGrupo, "outrogrupo"=>$SiglaGrupo);
    $responseText = json_encode($var);
    echo $responseText;
}


if($Acao == "configMarcaEscala"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id de poslog
    $Campo = filter_input(INPUT_GET, 'campo');
    $Valor = (int) filter_input(INPUT_GET, 'valor');

    $Fiscal = parEsc("esc_fisc", $Conec, $xProj, $_SESSION["usuarioID"]);

    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]); // meu grupo no poslog
    $CodGrupo = parEsc("esc_grupo", $Conec, $xProj, $Cod); // grupo do selecionado no poslog
    $SiglaGrupo = "";

    if($CodGrupo == 0){ // está sem grupo - põe no seu grupo
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_grupo = $NumGrupo, daf_turno = 0, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
        $CodGrupo = $NumGrupo;
    }

    $Marca = parEsc("eft_daf", $Conec, $xProj, $Cod);

    if($CodGrupo == $NumGrupo){
        if($Fiscal == 1 && $Valor == 1){
           //Verifica se está marcado como efetivo em algum grupo
            $rs2 = pg_query($Conec, "SELECT eft_daf, esc_grupo FROM ".$xProj.".poslog WHERE pessoas_id = $Cod;");
            $tbl2 = pg_fetch_row($rs2);
            $MarcaEft = $tbl2[0];
            $GrupoFisc = $tbl2[1];
            if($MarcaEft == 1){ // está marcado como efetivo
                $rs = pg_query($Conec, "SELECT siglagrupo FROM ".$xProj.".escalas_gr WHERE id = $GrupoFisc;");
                $row = pg_num_rows($rs);
                if($row > 0){
                    $tbl = pg_fetch_row($rs);
                    $SiglaGrupo = $tbl[0];
                }
                $Erro = 3;
            }else{
                $rs1 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET $Campo = $Valor, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
            }
        }else{
            $rs1 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET $Campo = $Valor, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
        }        

        if($Campo == "esc_daf"){ // verifica se vai ficar algum escalante - não pode ficar sem escalante
            $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE esc_daf = 1 And esc_grupo = $NumGrupo");
            $row = pg_num_rows($rs);
            if($row == 0){
                pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_daf = 1 WHERE pessoas_id = $Cod");
                $Erro = 2;
                $var = array("coderro"=>$Erro);
                $responseText = json_encode($var);
                echo $responseText;
                return false;
            }
        }
    }else{ // está em outro grupo
        //Verifica se está marcado como efetivo naquele grupo
        $rs2 = pg_query($Conec, "SELECT eft_daf, esc_daf FROM ".$xProj.".poslog WHERE pessoas_id = $Cod;");
        $tbl2 = pg_fetch_row($rs2);
        $MarcaEft = $tbl2[0];
        $MarcaEsc = $tbl2[1];
        if($MarcaEft == 0 && $MarcaEsc == 0){ // não está marcado como efetivo nem como escalange -> muda o grupo e marca como efetivo no meu grupo
            pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_grupo = $NumGrupo, $Campo = $Valor, daf_turno = 0, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
            $CodGrupo = $NumGrupo;
        }
        if($MarcaEft == 1 || $MarcaEsc == 1){ // está em outro grupo e está marcado como efetivo ou escalante
            $rs = pg_query($Conec, "SELECT siglagrupo FROM ".$xProj.".escalas_gr WHERE id = $CodGrupo;");
            $row = pg_num_rows($rs);
            if($row > 0){
                $tbl = pg_fetch_row($rs);
                $SiglaGrupo = $tbl[0];
            }
            $Erro = 3;
        }
    }

    $var = array("coderro"=>$Erro, "codgrupo"=>$CodGrupo, "meugrupo"=>$NumGrupo, "outrogrupo"=>$SiglaGrupo);
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

if($Acao =="marcaDia"){ // sem uso
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id
    $rs0 = pg_query($Conec, "SELECT marcadaf FROM ".$xProj.".escaladaf WHERE id = $Cod");
    $tbl0 = pg_fetch_row($rs0);
    $Marca = (int) $tbl0[0];
    if($Marca == 0){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf SET marcadaf = 1 WHERE id = $Cod");
    }else{
        $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf SET marcadaf = 0 WHERE id = $Cod");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="marcaTurno"){ // sem uso
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id
    $Cor = (int) filter_input(INPUT_GET, 'cor');
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET destaq = $Cor WHERE id = $Cod");
//    $rs0 = pg_query($Conec, "SELECT destaq FROM ".$xProj.".escaladaf_turnos WHERE id = $Cod");
//    $tbl0 = pg_fetch_row($rs0);
//    $Marca = (int) $tbl0[0];
//    if($Marca == 0){
//        $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET destaq = 1 WHERE id = $Cod");
//    }else{
//        $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET destaq = 0 WHERE id = $Cod");
//    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaTurnoParticip"){
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
    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    $Proc = explode("/", $MesAno);
    $Mes = $Proc[0];
    if(strLen($Mes) < 2){
        $Mes = "0".$Mes;
    }
    $Ano = $Proc[1];

    //Guarda a consulta
    $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET mes_escdaf = '$MesAno' WHERE pessoas_id = $UsuIns");
    //ver se a escala do mes está liberada para os participantes da escala
    $MesLiberado = 0;
    $rsLib = pg_query($Conec, "SELECT liberames FROM ".$xProj.".escaladaf WHERE DATE_PART('MONTH', dataescala) = '$Mes' And DATE_PART('YEAR', dataescala) = '$Ano' And liberames != 0 And grupo_id = $NumGrupo ");
    $rowLib = pg_num_rows($rsLib);
    if($rowLib > 0){
        $MesLiberado = 1;
    }

    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "mesliberado"=>$MesLiberado);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="insParticipante"){
    $Erro = 0;
    $CodDia = (int) filter_input(INPUT_GET, 'diaIdEscala'); // pessoas_id
//    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    if(isset($_REQUEST["numgrupo"])){
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);   
    }
    //Apaga o dia
    pg_query($Conec, "DELETE FROM ".$xProj.".escaladaf_ins WHERE escaladaf_id = $CodDia;");

    $rs0 = pg_query($Conec, "SELECT dataescala FROM ".$xProj.".escaladaf WHERE id = $CodDia And grupo_id = $NumGrupo");
    $row0 = pg_num_rows($rs0);
    if($row0 > 0){
        $tbl0 = pg_fetch_row($rs0);
        $DataEscala = $tbl0[0];
    }else{
        $DataEscala = "";
    }
    $rs = pg_query($Conec, "SELECT pessoas_id, ".$xProj.".poslog.daf_turno, ".$xProj.".escaladaf_turnos.letra, ".$xProj.".escaladaf_turnos.horaturno, ".$xProj.".escaladaf_turnos.destaq, ".$xProj.".escaladaf_turnos.cargacont, ".$xProj.".escaladaf_turnos.id 
    FROM ".$xProj.".poslog LEFT JOIN ".$xProj.".escaladaf_turnos ON ".$xProj.".poslog.daf_turno = ".$xProj.".escaladaf_turnos.id
    WHERE ".$xProj.".poslog.ativo = 1 And ".$xProj.".poslog.eft_daf = 1 And ".$xProj.".poslog.daf_marca = 1 And ".$xProj.".escaladaf_turnos.grupo_turnos = $NumGrupo");
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
            $IdTurno = $tbl[6];
            $Letra = $tbl[2];
            $DescTurno = $tbl[3];
            $Destaq = $tbl[4];
            $CargaHor = $tbl[5];

            $CodigoNovo = 0;
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_ins");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1);

            pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_ins (id, grupo_ins, escaladaf_id, dataescalains, poslog_id, letraturno, turnoturno, destaque, cargatime, usuins, datains, turnos_id) 
            VALUES($CodigoNovo, $NumGrupo, $CodDia, '$DataEscala', $CodPartic, '$Letra', '$DescTurno', $Destaq, '$CargaHor', $UsuIns, NOW(), $IdTurno )");
        }
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "row"=>$row);
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


function limpar_texto($str){ 
//    return preg_replace("/[^0-9]/", "", $str); 
//    $a = array('a', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
    $a = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'Ü', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z');
    $b = array('0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');
    return str_replace($a, $b, $str);
  }

if($Acao =="salvaturno__"){ // sem uso
    $Erro = 0;
    $Cod = filter_input(INPUT_GET, 'codigo');
    $Val = addslashes(filter_input(INPUT_GET, 'valor'));
    $Valor = limpar_texto($Val);// letra O no lugar de 0

    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET horaturno = '$Valor' WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }

    if($Cod > 4){ // além de férias, folga, etc
        //Calcular carga horaria
        $Ho = addslashes(filter_input(INPUT_GET, 'valor')); 
        $Hor = str_replace("O", "0", $Ho); // letra O no lugar de 0
        $Hora = str_replace("o", "0", $Hor); // letra o no lugar de 0

        $Proc = explode("/", $Hora);
        $HoraI = $Proc[0];
        $HoraF = $Proc[1];
        $TurnoI = $Hoje." ".$HoraI;
        $TurnoF = $Hoje." ".$HoraF;

        $TurnoIni = limpar_texto($TurnoI);
        $TurnoFim = limpar_texto($TurnoF);

        if(strLen($TurnoIni) < 17 || strLen($TurnoFim) < 17){
            $Erro = 2;
            $var = array("coderro"=>$Erro, "hora1"=>$TurnoFim);
            $responseText = json_encode($var);
            echo $responseText;
            return false;
        }
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET calcdataini = '$TurnoIni', calcdatafim = '$TurnoFim' WHERE id = $Cod");
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargahora = (calcdatafim - calcdataini) WHERE id = $Cod");

        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = (cargahora - time '01:00'), interv = '01:00' WHERE cargahora >= '08:00' And id = $Cod ");
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = (cargahora - time '00:15'), interv = '00:15' WHERE cargahora >= '06:00' And cargahora < '08:00' And id = $Cod ");
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = cargahora, interv = '00:00' WHERE cargahora <= '06:00' And id = $Cod ");

    }

    $var = array("coderro"=>$Erro, "cod"=>$Cod, "hora1"=>$TurnoFim);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaInterv"){
    $Erro = 0;
    $Cod = filter_input(INPUT_GET, 'codigo');
    $Va = filter_input(INPUT_GET, 'valor');
    $Val = str_replace("O", "0", $Va); // letra O no lugar de 0
    $Valor = str_replace("o", "0", $Val); // letra o no lugar de 0

    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET interv = '$Valor' WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = (cargahora - time '$Valor') WHERE id = $Cod ");

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
    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    $rs = pg_query($Conec, "SELECT MAX(numnota) FROM ".$xProj.".escaladaf_notas WHERE grupo_notas = $NumGrupo");
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
    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    $Texto = addslashes(filter_input(INPUT_GET, 'textonota'));
    if($Cod > 0){ // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_notas SET numnota = $Num, grupo_notas = $NumGrupo, textonota = '$Texto' WHERE id = $Cod");
    }else{ // inserir
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_notas");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_notas (id, grupo_notas, numnota, textonota, usuins, datains) 
        VALUES($CodigoNovo, , $NumGrupo, $Num, '$Texto', $UsuIns, NOW() )");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="insereletra__"){
    $Erro = 0;
    $Ordem = (int) filter_input(INPUT_GET, 'ordem');
    $Letra = filter_input(INPUT_GET, 'insletra');
    $Tur = addslashes(filter_input(INPUT_GET, 'insturno'));
     $Turno = limpar_texto($Tur);

    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);

    $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_turnos");
    $tblCod = pg_fetch_row($rsCod);
    $Codigo = $tblCod[0];
    $CodigoNovo = ($Codigo+1);

    $rs = pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, ordemletra, letra, horaturno, usuins, datains) 
    VALUES($CodigoNovo, $NumGrupo, $Ordem, UPPER('$Letra'), '$Turno', $UsuIns, NOW() )");
    if(!$rs){
        $Erro = 1;
    }
        //Calcular carga horaria
        $Proc = explode("/", $Turno);
        $HoraI = $Proc[0];
        $HoraF = $Proc[1];
        $TurnoIni = $Hoje." ".$HoraI;
        $TurnoFim = $Hoje." ".$HoraF;
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET calcdataini = '$TurnoIni', calcdatafim = '$TurnoFim' WHERE id = $CodigoNovo");
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargahora = (calcdatafim - calcdataini) WHERE id = $CodigoNovo");

        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = (cargahora - time '01:00'), interv = '01:00' WHERE cargahora >= '08:00' And id = $CodigoNovo ");
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = (cargahora - time '00:15'), interv = '00:15' WHERE cargahora >= '06:00' And cargahora < '08:00' And id = $CodigoNovo ");
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = cargahora, interv = '00:00' WHERE cargahora <= '06:00' And id = $CodigoNovo ");

    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="insereletra"){
    $Erro = 0;
    $Ordem = (int) filter_input(INPUT_GET, 'ordem');
    $Letra = filter_input(INPUT_GET, 'insletra');
//    $Tur = addslashes(filter_input(INPUT_GET, 'insturno'));
//     $Turno = limpar_texto($Tur);

    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);

    $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_turnos");
    $tblCod = pg_fetch_row($rsCod);
    $Codigo = $tblCod[0];
    $CodigoNovo = ($Codigo+1);

    $rs = pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, ordemletra, letra, horaturno, usuins, datains) 
    VALUES($CodigoNovo, $NumGrupo, $Ordem, UPPER('$Letra'), '00:00 / 00:00', $UsuIns, NOW() )");
    if(!$rs){
        $Erro = 1;
    }
        //Calcular carga horaria
//        $Proc = explode("/", $Turno);
//        $HoraI = $Proc[0];
//        $HoraF = $Proc[1];
//        $TurnoIni = $Hoje." ".$HoraI;
//        $TurnoFim = $Hoje." ".$HoraF;
//        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET calcdataini = '$TurnoIni', calcdatafim = '$TurnoFim' WHERE id = $CodigoNovo");
//        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargahora = (calcdatafim - calcdataini) WHERE id = $CodigoNovo");
//
//        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = (cargahora - time '01:00'), interv = '01:00' WHERE cargahora >= '08:00' And id = $CodigoNovo ");
//        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = (cargahora - time '00:15'), interv = '00:15' WHERE cargahora >= '06:00' And cargahora < '08:00' And id = $CodigoNovo ");
//        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = cargahora, interv = '00:00' WHERE cargahora <= '06:00' And id = $CodigoNovo ");

    $var = array("coderro"=>$Erro, "codigonovo"=>$CodigoNovo);
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

if($Acao =="liberaMes"){
    $Erro = 0;
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    $Busca = addslashes(filter_input(INPUT_GET, 'mesano')); 
    $Proc = explode("/", $Busca);
    $Mes = $Proc[0];
    if(strLen($Mes) < 2){
        $Mes = "0".$Mes;
    }
    $Ano = $Proc[1];

    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf SET liberames = $Valor WHERE TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");    

    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="insereFeriado"){
    $Erro = 0;
    $Data = addslashes(filter_input(INPUT_GET, 'insdata'));
    $Descr = filter_input(INPUT_GET, 'insdescr');
    
    $Proc = explode("/", $Data);
    $Dia = $Proc[0];
    if(strLen($Dia) < 2){
        $Dia = "0".$Dia;
    }
    $Mes = $Proc[1];

    $AnoHoje = date('Y');
    $Feriado = $AnoHoje."/".$Mes."/".$Dia;

    $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_fer");
    $tblCod = pg_fetch_row($rsCod);
    $Codigo = $tblCod[0];
    $CodigoNovo = ($Codigo+1);

    $rs = pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) 
    VALUES($CodigoNovo, '$Feriado', '$Descr', $UsuIns, NOW() )");
    if(!$rs){
        $Erro = 1;
    }

    $var = array("coderro"=>$Erro, "data"=>$Feriado);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="apagadatafer"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    // retirar marca de feriado em escaladaf
    $rsFer = pg_query($Conec, "SELECT TO_CHAR(dataescalafer, 'DD'), TO_CHAR(dataescalafer, 'MM') FROM ".$xProj.".escaladaf_fer WHERE id = $Cod");
    $tblFer = pg_fetch_row($rsFer);
    $DiaFer = $tblFer[0];
    $MesFer = $tblFer[1];
    pg_query($Conec, "UPDATE ".$xProj.".escaladaf SET feriado = 0 WHERE TO_CHAR(dataescala, 'DD') = '$DiaFer' And TO_CHAR(dataescala, 'MM') = '$MesFer' ");

    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_fer SET ativo = 0 WHERE id = $Cod ");

    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="transfmesano"){
    $Erro = 0;

    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    $MesAno = addslashes(filter_input(INPUT_GET, 'mesano')); // mes a transferir
    $Proc = explode("/", $MesAno);
    $Mes = $Proc[0];
    if(strLen($Mes) < 2){
        $Mes = "0".$Mes;
    }
    $Ano = $Proc[1];

    $MesFrom = addslashes(filter_input(INPUT_GET, 'transfde')); // mes de onde transferir
    $Proc = explode("/", $MesFrom);
    $MesFrom = $Proc[0];
    if(strLen($MesFrom) < 2){
        $MesFrom = "0".$Mes;
    }
    $AnoFrom = $Proc[1];

    //Quantos dias tem o próximo mês
    $rsIni = pg_query($Conec, "SELECT MAX(TO_CHAR(dataescala, 'DD')) 
    FROM ".$xProj.".escaladaf 
    WHERE TO_CHAR(dataescala, 'MM') = '$Mes' And TO_CHAR(dataescala, 'YYYY') = '$Ano' ");
    $tblIni = pg_fetch_row($rsIni);
    $UltDiaProxMes = $tblIni[0]; // 31

    $rsIni = pg_query($Conec, "SELECT MAX(TO_CHAR(dataescala, 'DD')) 
    FROM ".$xProj.".escaladaf 
    WHERE TO_CHAR(dataescala, 'MM') = '$MesFrom' And TO_CHAR(dataescala, 'YYYY') = '$AnoFrom' ");
    $tblIni = pg_fetch_row($rsIni);
    $UltDia = $tblIni[0]; // 31

    //Dia da semana onde iniciar 
    $rsIni = pg_query($Conec, "SELECT date_part('dow', dataescala) 
    FROM ".$xProj.".escaladaf 
    WHERE TO_CHAR(dataescala, 'MM') = '$MesFrom' And TO_CHAR(dataescala, 'YYYY') = '$AnoFrom' And TO_CHAR(dataescala, 'DD') = '$UltDia' And grupo_id = $NumGrupo ");
    $tblIni = pg_fetch_row($rsIni);
    $DiaSemanaIni = ($tblIni[0]+1);  // 0, 1, 2...  Soma um dia na semana para iniciar a sequência no mês seguinte

    //Procura o dia do mês para iniciar
    $rsIni = pg_query($Conec, "SELECT TO_CHAR(dataescala, 'DD') FROM ".$xProj.".escaladaf 
    WHERE TO_CHAR(dataescala, 'MM') = '$MesFrom' And TO_CHAR(dataescala, 'YYYY') = '$AnoFrom' And date_part('dow', dataescala) = '$DiaSemanaIni' And grupo_id = $NumGrupo ORDER BY dataescala ");
    $rowIni = pg_num_rows($rsIni);
    if($rowIni > 0){
        $tblIni = pg_fetch_row($rsIni);
        $DiaSemDia = $tblIni[0];
    }else{
        $DiaSemDia = "01";
        $Erro = 2;
        $var = array("coderro"=>$Erro, "ulDiaSemana"=>$DiaSemanaIni, "NumDia"=>$DiaSemDia);
        $responseText = json_encode($var);
        echo $responseText;
        return false;
    }
    
    // apaga o que houver no mês seguinte
    pg_query($Conec, "DELETE FROM ".$xProj.".escaladaf_ins WHERE TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' ;");

    //Inicia selecionando 
    $rs = pg_query($Conec, "SELECT id, TO_CHAR(dataescalains, 'DD'), poslog_id, letraturno, turnoturno, destaque, cargatime, turnos_id 
    FROM ".$xProj.".escaladaf_ins 
    WHERE TO_CHAR(dataescalains, 'MM') = '$MesFrom' And TO_CHAR(dataescalains, 'YYYY') = '$AnoFrom' And grupo_ins = $NumGrupo And TO_CHAR(dataescalains, 'DD') >= '$DiaSemDia' And TO_CHAR(dataescalains, 'DD') <= '$UltDiaProxMes' ORDER BY dataescalains ");
    $row = pg_num_rows($rs);
    if($row > 0){
        while($tbl = pg_fetch_row($rs)){
            $CodId = $tbl[0];
            $Dia = $tbl[1];
            $Dia = ($Dia-($DiaSemDia-1)); // retroceder até o dia que é o próximo dia da semana do mes seguinte
            if($Dia <= $UltDiaProxMes){
                $NovaData = $Ano."/".$Mes."/".$Dia;
                $PoslogId = $tbl[2];
                $Letra = $tbl[3];
                $Turno = $tbl[4];
                $Dest = $tbl[5];
                $Carga = $tbl[6];
                $TurnoId = $tbl[7]; // usado para carregar o turno do dia clicado

                $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf WHERE dataescala = '$NovaData' And grupo_id = $NumGrupo ");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    $tbl1 = pg_fetch_row($rs1);
                    $CodIdEscala = $tbl1[0];
                    $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_ins");
                    $tblCod = pg_fetch_row($rsCod);
                    $Codigo = $tblCod[0];
                    $CodigoNovo = ($Codigo+1);

                    pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_ins (id, escaladaf_id, dataescalains, poslog_id, letraturno, turnoturno, destaque, cargatime, usuins, datains, grupo_ins, turnos_id) 
                    VALUES ($CodigoNovo, $CodIdEscala, '$NovaData', $PoslogId, '$Letra', '$Turno', $Dest, '$Carga', $UsuIns, NOW(), $NumGrupo, $TurnoId ) ");
                }
            }
        }
    }
    //Salva nas preferências a escala do mês
    pg_query($Conec, "UPDATE ".$xProj.".poslog SET mes_escdaf = '$MesAno' WHERE pessoas_id = $UsuIns");
    if(!$rs || !$rs1){
        $Erro = 1;
    }

    $var = array("coderro"=>$Erro, "novadata"=>$NovaData, "NumDia"=>$DiaSemDia);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="procChefeDiv"){
    $Erro = 0;
    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    $rs = pg_query($Conec, "SELECT chefe_escdaf, enc_escdaf FROM ".$xProj.".escalas_gr WHERE id = $NumGrupo");
    $tbl = pg_fetch_row($rs);
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "chefe"=>$tbl[0], "encarreg"=>$tbl[1]);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvachefediv"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escalas_gr SET chefe_escdaf = $Cod WHERE id = $NumGrupo");
    $tbl = pg_fetch_row($rs);
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="salvaencarreg"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escalas_gr SET enc_escdaf = $Cod WHERE id = $NumGrupo");
    $tbl = pg_fetch_row($rs);
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="salvaGrupo"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Sigla = filter_input(INPUT_GET, 'siglagrupo');
    $Nome = filter_input(INPUT_GET, 'nomegrupo');
    $Descr = filter_input(INPUT_GET, 'descgrupo');
    $Turnos = (int) filter_input(INPUT_GET, 'selecTurnos');

    if($Cod > 0){ // salvar
        $rs = pg_query($Conec, "UPDATE ".$xProj.".escalas_gr SET siglagrupo = '$Sigla', descgrupo = '$Nome', descescala = '$Descr', qtd_turno = $Turnos WHERE id = $Cod ");
        if(!$rs){
            $Erro = 1;
        }
    }else{ // inserir
        $rs1 = pg_query($Conec, "SELECT siglagrupo FROM ".$xProj.".escalas_gr WHERE siglagrupo = '$Sigla' ");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            $Erro = 2;
            $var = array("coderro"=>$Erro);
            $responseText = json_encode($var);
            echo $responseText;
            return false;
        }

        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escalas_gr");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".escalas_gr (id, siglagrupo, descgrupo, descescala, qtd_turno) 
        VALUES ($CodigoNovo, '$Sigla', '$Nome', '$Descr', $Turnos) ");

        //Inserir um para a primeira abertura
        $DiaIni = strtotime(date('Y/m/01'));
        $Amanha = strtotime("+1 day", $DiaIni);
        $DiaIni = $Amanha;
        $Data = date("Y/m/d", $Amanha); // data legível
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf (dataescala, grupo_id) VALUES ('$Data', $CodigoNovo)");

    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="trocagrupo"){
    $NumGrupo = (int) filter_input(INPUT_GET, 'grupo');
    $Erro = 0;
//    $rs0 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_grupo = $NumGrupo WHERE pessoas_id = ".$_SESSION["usuarioID"]." And ativo = 1");
//    if(!$rs0){
//        $Erro = 1;
//    }else{
        $rs = pg_query($Conec, "SELECT siglagrupo FROM ".$xProj.".escalas_gr WHERE id = $NumGrupo;");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $SiglaGrupo = $tbl[0];
        }else{
            $SiglaGrupo = "";
        } 
//    }
    $var = array("coderro"=>$Erro, "siglagrupo"=>$SiglaGrupo);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="apagaGrupo"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escalas_gr SET ativo = 0 WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }else{
        $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf SET ativo = 0 WHERE grupo_id = $Cod");
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="buscaTurno"){
    $Erro = 0;
    $Cod = filter_input(INPUT_GET, 'codigo');
    $rs = pg_query($Conec, "SELECT horaturno, ordemletra, letra, infotexto FROM ".$xProj.".escaladaf_turnos WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }else{
        $tbl = pg_fetch_row($rs);
        $InfoTexto = (int) $tbl[3];
        if($InfoTexto == 0){ // turno númerico
            $HoraTurno = $tbl[0];
            if(is_null($HoraTurno)){
                $HoraTurno = "00:00 / 00:00";
            }
            $Proc = explode("/", $HoraTurno);
            $Turno1 = $Proc[0];
            $Turno2 = $Proc[1];

            $Proc1 = explode(":", $Turno1);
            $Turno1Hor = trim($Proc1[0]);
            $Turno1Min = trim($Proc1[1]);

            $Proc2 = explode(":", $Turno2);
            $Turno2Hor = trim($Proc2[0]);
            $Turno2Min = trim($Proc2[1]);
    
            $var = array("coderro"=>$Erro, "infotexto"=>$InfoTexto, "turno1Hor"=>$Turno1Hor, "turno1Min"=>$Turno1Min, "turno2Hor"=>$Turno2Hor, "turno2Min"=>$Turno2Min, "ordemletra"=>$tbl[1], "letra"=>$tbl[2]);
        }else{ // férias, folga, etc
            $var = array("coderro"=>$Erro, "infotexto"=>$InfoTexto, "turno"=>$tbl[0], "letra"=>$tbl[2]);
        }
    }
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="salvaEditaTurno"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Turno = addslashes(filter_input(INPUT_GET, 'turno'));
    $InfoTexto = (int) filter_input(INPUT_GET, 'infotexto');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET horaturno = '$Turno', infotexto = $InfoTexto WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }

    if($InfoTexto == 0){
        $Proc = explode("/", $Turno);
        $HoraI = $Proc[0];
        $HoraF = $Proc[1];
        $TurnoIni = $Hoje." ".$HoraI;
        $TurnoFim = $Hoje." ".$HoraF;

        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET calcdataini = '$TurnoIni', calcdatafim = '$TurnoFim' WHERE id = $Cod");
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargahora = (calcdatafim - calcdataini) WHERE id = $Cod");

        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = (cargahora - time '01:00'), interv = '01:00' WHERE cargahora >= '08:00' And id = $Cod ");
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = (cargahora - time '00:15'), interv = '00:15' WHERE cargahora >= '06:00' And cargahora < '08:00' And id = $Cod ");
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = cargahora, interv = '00:00' WHERE cargahora <= '06:00' And id = $Cod ");
    }else{
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargahora = '00:00:00' WHERE id = $Cod");
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET interv = '00:00:00' WHERE id = $Cod");
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = '00:00:00' WHERE id = $Cod");
    }

//Se for para acertar a carga horária do mês - não afeta meses passados
// Pode haver confusão
//    $MesAno = addslashes(filter_input(INPUT_GET, 'mesano'));
//    $Letra = addslashes(filter_input(INPUT_GET, 'letra'));
//    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
//    $Proc = explode("/", $MesAno);
//    $Mes = $Proc[0];
//    if(strLen($Mes) < 2){
//        $Mes = "0".$Mes;
//    }
//    $Ano = $Proc[1];
//
//    $rs1 = pg_query($Conec, "SELECT cargacont FROM ".$xProj.".escaladaf_turnos WHERE id = $Cod");
//    $row1 = pg_num_rows($rs1);
//    if($row1 > 0){
//        $tbl1 = pg_fetch_row($rs1);
//        $Carga = $tbl1[0];
//        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_ins 
//        SET cargatime = '$Carga' 
//        WHERE letraturno = '$Letra' And TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' And grupo_ins = $NumGrupo ");
//     }
//

    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="buscaOrdem"){
    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT MAX(ordemletra) FROM ".$xProj.".escaladaf_turnos WHERE grupo_turnos = $NumGrupo");
    if(!$rs){
        $Erro = 1;
        $Ordem = 0;
    }else{
        $tbl = pg_fetch_row($rs);
        $Num = $tbl[0];
        $Ordem = ($Num+1);
    }
    $var = array("coderro"=>$Erro, "ordem"=>$Ordem);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="salvafolga"){
    $Erro = 0;
    $Cod = filter_input(INPUT_GET, 'codigo');
    $Valor = filter_input(INPUT_GET, 'valor');
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_ins SET horafolga = '$Valor' WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}