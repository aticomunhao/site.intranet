<?php
session_start(); 
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
$Acao = "";
if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    $Hoje = date('d/m/Y');
    $HojeIng = date('Y/m/d');
    $UsuIns = $_SESSION['usuarioID'];
}

if($Acao == "buscausuario"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); //id de poslog
    if(isset($_REQUEST["numgrupo"])){ //grupo escolhido
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal que pode editar escala
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    if($NumGrupo == 0 || $NumGrupo == ""){
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }

    $rs1 = pg_query($Conec, "SELECT eft_daf, cpf, enc_escdaf, chefe_escdaf, esc_grupo, cargo_daf, esc_fisc FROM ".$xProj.".poslog WHERE pessoas_id = $Cod");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($rs1);
        $Grupo = $tbl1[4];  // Grupo em poslog
        $Eft = $tbl1[0]; // Efetivo
        if($Grupo == 0){
            $Grupo = $NumGrupo;
            pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_grupo = $NumGrupo WHERE pessoas_id = $Cod");
        }
        if($Eft == 1 && $Grupo != $NumGrupo ){
            $Eft = 0;
        }
//        $Esc = $tbl1[1]; // Escalante
//        if($Esc == 1 && $Grupo != $MeuGrupo ){
//            $Esc = 0;
//        }

        // Escalante
        $rs2 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_esc WHERE ativo = 1 And usu_id = $Cod And grupo_id = $NumGrupo");
        $row2 = pg_num_rows($rs2);
        if($row2 > 0){
            $Esc = 1;
        }else{
            $Esc = 0;
        }

        $var = array("coderro"=>$Erro, "eft"=>$Eft, "esc"=>$Esc, "cpf"=>$tbl1[1], "encarreg"=>$tbl1[2], "chefeadm"=>$tbl1[3], "cargo"=>$tbl1[5], "escfiscal"=>$tbl1[6], "grupo"=>$Grupo, "cod"=>$Cod, "row2"=>$row2);
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

    $rs1 = pg_query($Conec, "SELECT eft_daf, esc_daf, cpf, pessoas_id, enc_escdaf, chefe_escdaf, cargo_daf FROM ".$xProj.".poslog WHERE cpf = '$GuardaCpf'");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($rs1);
        $var = array("coderro"=>$Erro, "eft"=>$tbl1[0], "esc"=>$tbl1[1], "cpf"=>$tbl1[2], "PosCod"=>$tbl1[3], "encarreg"=>$tbl1[4], "chefeadm"=>$tbl1[5], "cargo"=>$tbl1[6]);
    }else{
        $Erro = 1;
        $var = array("coderro"=>$Erro);
    }        
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "configMarcaEscala__"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id de poslog
    $Campo = filter_input(INPUT_GET, 'campo');
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    $Fiscal = parEsc("esc_fisc", $Conec, $xProj, $_SESSION["usuarioID"]);

    if(isset($_REQUEST["numgrupo"])){ //grupo escolhido
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal que pode editar escala
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    if($NumGrupo == 0 || $NumGrupo == ""){
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }

    //Verif se esse grupo já tem datas em escaladaf
    $rsGrupo = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf WHERE grupo_id = $NumGrupo ");
    $rowGrupo = pg_num_rows($rsGrupo);
    $DiaIni = strtotime(date('Y/m/01'));
    $Amanha = $DiaIni;
    if($rowGrupo < 120){
        for($i = 0; $i < 90; $i++){
            $Data = date("Y/m/d", $Amanha); // data legível
            $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf WHERE dataescala = '$Data' And grupo_id = $NumGrupo ");
            $row0 = pg_num_rows($rs0);
            if($row0 == 0){
                pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf (dataescala, grupo_id) VALUES ('$Data', $NumGrupo)");
            }
            $Amanha = strtotime("+1 day", $DiaIni);
            $DiaIni = $Amanha;
        }
    }

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
        if($Campo == "eft_daf"){
            //Verifica se está marcado como efetivo naquele grupo
            $rs2 = pg_query($Conec, "SELECT eft_daf, esc_daf FROM ".$xProj.".poslog WHERE pessoas_id = $Cod;");
            $tbl2 = pg_fetch_row($rs2);
            $MarcaEft = $tbl2[0];
            $MarcaEsc = $tbl2[1];
            if($MarcaEft == 0 && $MarcaEsc == 0){ // não está marcado como efetivo nem como escalange -> muda o grupo e marca como efetivo no meu grupo
                pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_grupo = $NumGrupo, $Campo = $Valor, daf_turno = 0, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
                $CodGrupo = $NumGrupo;
            }
//          if($MarcaEft == 1 || $MarcaEsc == 1){ // está em outro grupo e está marcado como efetivo ou escalante
            if($MarcaEft == 1){ // está em outro grupo e está marcado como efetivo ou escalante
                $rs = pg_query($Conec, "SELECT siglagrupo FROM ".$xProj.".escalas_gr WHERE id = $CodGrupo;");
                $row = pg_num_rows($rs);
                if($row > 0){
                    $tbl = pg_fetch_row($rs);
                    $SiglaGrupo = $tbl[0];
                }
                $Erro = 3;
            }
        }
    }
    if($Campo == "esc_daf"){ // escalante pode gerenciar outros grupos
        pg_query($Conec, "UPDATE ".$xProj.".poslog SET $Campo = $Valor, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
    }

    $var = array("coderro"=>$Erro, "codgrupo"=>$CodGrupo, "meugrupo"=>$NumGrupo, "outrogrupo"=>$SiglaGrupo);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "configMarcaEscala"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id de poslog
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    $Fiscal = parEsc("esc_fisc", $Conec, $xProj, $_SESSION["usuarioID"]);
    $SiglaGrupo = "";
    $MarcaEft = 0;

    if(isset($_REQUEST["numgrupo"])){ //grupo escolhido vem de Ver Grupo: selecGrupo
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal que pode editar escala - vem de selecGrupo
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $Cod);
    }
    if($NumGrupo == 0 || $NumGrupo == ""){
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $Cod);
    }

    $CodGrupo = parEsc("esc_grupo", $Conec, $xProj, $Cod); // grupo do poslog
    if($CodGrupo == 0){ // está sem grupo - põe no $NumGrupo
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_grupo = $NumGrupo, daf_turno = 0, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
        $CodGrupo = $NumGrupo;
    }

    if($CodGrupo == $NumGrupo){ // já está no grupo que vem do post
        $rs1 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET eft_daf = $Valor, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
    }else{ // está em outro grupo
        //Verifica se está marcado como efetivo em algum grupo
        $rs2 = pg_query($Conec, "SELECT eft_daf, esc_grupo FROM ".$xProj.".poslog WHERE pessoas_id = $Cod;");
        $tbl2 = pg_fetch_row($rs2);
        $MarcaEft = $tbl2[0];
        $MarcaGrupo = $tbl2[1];
        if($MarcaEft == 1){ // está marcado como efetivo - procura nome do grupo em que está
            $rs = pg_query($Conec, "SELECT siglagrupo FROM ".$xProj.".escalas_gr WHERE id = $MarcaGrupo;");
            $row = pg_num_rows($rs);
            if($row > 0){
                $tbl = pg_fetch_row($rs);
                $SiglaGrupo = $tbl[0];
            }
        }else{ // não é efetivo daquele grupo - insere no grupo que vem de post
            pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_grupo = $NumGrupo, daf_turno = 0, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
            pg_query($Conec, "UPDATE ".$xProj.".poslog SET eft_daf = $Valor, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
        }
    }

    $var = array("coderro"=>$Erro, "codgrupo"=>$CodGrupo, "meugrupo"=>$NumGrupo, "jaesta"=>$MarcaEft, "outrogrupo"=>$SiglaGrupo);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "configMarcaEscalaEsc"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id de poslog
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    if(isset($_REQUEST["numgrupo"])){ //grupo escolhido
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal que pode editar escala
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    if($NumGrupo == 0 || $NumGrupo == ""){
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    if($Valor == 1){
        $rs0 = pg_query($Conec, "SELECT id, ativo FROM ".$xProj.".escaladaf_esc WHERE usu_id = $Cod And grupo_id = $NumGrupo");
        $row0 = pg_num_rows($rs0);
        if($row0 == 0){
            $CodigoNovo = 0;
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_esc");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1);
            $rs1 = pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_esc (id, usu_id, grupo_id, ativo, usuins, datains) 
                VALUES($CodigoNovo, $Cod, $NumGrupo, 1, ".$_SESSION["usuarioID"].", NOW() )");
                if(!$rs1){
                    $Erro = 1;
                }
        }else{ // já tem
            $tbl0 = pg_fetch_row($rs0);
            $CodId = $tbl0[0];
            $Ativo = $tbl0[1];
            if($Ativo == 0){ // recupera
                $rs2 = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_esc SET ativo = 1 WHERE id = $CodId");
                if(!$rs2){
                    $Erro = 1;
                }
            }
        }
    }
    if($Valor == 0){
        //Verifica se restará algum escalante no grupo
        $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_esc WHERE ativo = 1 And usu_id != $Cod And grupo_id = $NumGrupo");
        $row3 = pg_num_rows($rs3);
        if($row3 == 0){
            $Erro = 2; // não restará nenhum escalante no grupo
        }else{
            $rs4 = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_esc SET ativo = 0 WHERE usu_id = $Cod And grupo_id = $NumGrupo");
            if(!$rs4){
                $Erro = 1;
            }   
        }
    }

    $var = array("coderro"=>$Erro, "numgrupo"=>$NumGrupo);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "configMarcaEscalaFisc"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id de poslog
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_fisc = $Valor, datamodif = NOW(), usumodif = $UsuIns WHERE pessoas_id = $Cod");
    if(!$rs){
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

if($Acao =="marcaDia"){
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

if($Acao =="marcaTurno"){ 
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
    if(isset($_REQUEST["numgrupo"])){ //grupo escolhido
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal que pode editar escala
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    if($NumGrupo == 0 || $NumGrupo == ""){
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }

    $MesAno = addslashes(filter_input(INPUT_GET, 'mesano'));
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

    $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf WHERE grupo_id = $NumGrupo And DATE_PART('MONTH', dataescala) = '$Mes' And DATE_PART('YEAR', dataescala) = '$Ano' ");
    $row1 = pg_num_rows($rs1);

    $MesAtual = date('m');
    $AnoAtual = date('Y');
    $TanoMes = 0;
    if($Mes <= $MesAtual && $Ano <= $AnoAtual){
        $TanoMes = 1;
    }
    pg_query($Conec, "UPDATE ".$xProj.".escalas_gr SET editaesc = $TanoMes WHERE id = $NumGrupo ");

    $var = array("coderro"=>$Erro, "mesliberado"=>$MesLiberado, "temMes"=>$row1, "anoselec"=>$Ano, "TaNoMes"=>$TanoMes);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="insParticipante"){
    $Erro = 0;
    $CodDia = (int) filter_input(INPUT_GET, 'diaIdEscala'); // id de escaladaf
    if(isset($_REQUEST["numgrupo"])){
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);   
    }

    $rs0 = pg_query($Conec, "SELECT dataescala, TO_CHAR(dataescala, 'MM'), TO_CHAR(dataescala, 'YYYY') FROM ".$xProj.".escaladaf WHERE id = $CodDia And grupo_id = $NumGrupo");
    $row0 = pg_num_rows($rs0);
    if($row0 > 0){
        $tbl0 = pg_fetch_row($rs0);
        $DataEscala = $tbl0[0];
        $MesEscala = $tbl0[1];
        $AnoEscala = $tbl0[2];
    }else{
        $DataEscala = "";
        $MesEscala = "";
        $AnoEscala = "";
    }

    $rs = pg_query($Conec, "SELECT editaesc FROM ".$xProj.".escalas_gr WHERE id = $NumGrupo;");
    $row = pg_num_rows($rs);
    if($row > 0){
        $tbl = pg_fetch_row($rs);
        $EscalaFechada = $tbl[0];
    }else{
        $EscalaFechada = 0;
    }

    $MesAtual = date('m');
    $AnoAtual = date('Y');
    // guarda os turnos originais desse dia: $CodDia (id de escaladaf)
//    if($MesEscala == $MesAtual && $AnoEscala == $AnoAtual){ // troca durante o mês vigente - Salvar antes de apagar
    if($EscalaFechada == 1){ // 1 -> considerar como troca
        pg_query($Conec, "DELETE FROM ".$xProj.".escaladaf_trocas WHERE escaladaf_id = $CodDia And marca = 0");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_trocas (escaladaf_id, dataescala_orig, poslog_id, letra_orig, turno_orig, codturno_orig, horafolga_orig, grupo_id) 
        SELECT escaladaf_id, dataescalains, poslog_id, letraturno, turnoturno, turnos_id, horafolga, grupo_ins 
        FROM ".$xProj.".escaladaf_ins
        WHERE escaladaf_id = $CodDia ;");
    }else{
        pg_query($Conec, "DELETE FROM ".$xProj.".escaladaf_trocas WHERE escaladaf_id = $CodDia And grupo_id = $NumGrupo"); 
    }

    //Apaga o dia
    pg_query($Conec, "DELETE FROM ".$xProj.".escaladaf_ins WHERE escaladaf_id = $CodDia;");

    $rs = pg_query($Conec, "SELECT pessoas_id, ".$xProj.".poslog.daf_turno, ".$xProj.".escaladaf_turnos.letra, ".$xProj.".escaladaf_turnos.horaturno, ".$xProj.".escaladaf_turnos.destaq, ".$xProj.".escaladaf_turnos.cargacont, ".$xProj.".escaladaf_turnos.id, ".$xProj.".escaladaf_turnos.valeref 
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
            $ValeRef = $tbl[7];

            $CodigoNovo = 0;
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_ins");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1);

            pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_ins (id, grupo_ins, escaladaf_id, dataescalains, poslog_id, letraturno, turnoturno, destaque, cargatime, usuins, datains, turnos_id, valepag) 
            VALUES($CodigoNovo, $NumGrupo, $CodDia, '$DataEscala', $CodPartic, '$Letra', '$DescTurno', $Destaq, '$CargaHor', $UsuIns, NOW(), $IdTurno, $ValeRef )");
        }
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "row"=>$row, "dataescala"=>$DataEscala);
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
        VALUES($CodigoNovo, $NumGrupo, $Num, '$Texto', $UsuIns, NOW() )");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvainsLetra"){
    $Erro = 0;
    $Ordem = (int) filter_input(INPUT_GET, 'ordem');
    $Letra = strtoupper(filter_input(INPUT_GET, 'insletra'));

    if(isset($_REQUEST["numgrupo"])){ //grupo escolhido
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal que pode editar escala
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    if($NumGrupo == 0 || $NumGrupo == ""){
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }

    $row1 = 0;
    $rs1 = pg_query($Conec, "SELECT letra, infotexto FROM ".$xProj.".escaladaf_turnos WHERE letra = '$Letra' And grupo_turnos = $NumGrupo and ativo = 1");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $Erro = 2;
    }

    if($row1 == 0){
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_turnos");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);

        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, ordemletra, letra, horaturno, usuins, datains) 
        VALUES($CodigoNovo, $NumGrupo, $Ordem, UPPER('$Letra'), '00:00 / 00:00', $UsuIns, NOW() )");
        if(!$rs){
            $Erro = 1;
        }
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

if($Acao =="editaEscala"){
    $Erro = 0;
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    $CodEscala = addslashes(filter_input(INPUT_GET, 'codescala')); 
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escalas_gr SET editaesc = $Valor WHERE id = $CodEscala ");
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
    $Ano = filter_input(INPUT_GET, 'ano');

    $Proc = explode("/", $Data);
    $Dia = $Proc[0];
    if(strLen($Dia) < 2){
        $Dia = "0".$Dia;
    }
    $Mes = $Proc[1];

    //$AnoHoje = date('Y');
    //$Feriado = $AnoHoje."/".$Mes."/".$Dia;
    $Feriado = $Ano."/".$Mes."/".$Dia;

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
    if(isset($_REQUEST["numgrupo"])){
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal que pode editar escala
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    if($NumGrupo == 0 || $NumGrupo == ""){
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }

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
    if($tblIni[0] == 6){
        $DiaSemanaIni = 6;
    }else{
        $DiaSemanaIni = ($tblIni[0]+1);  // 0, 1, 2...  Soma um dia na semana para iniciar a sequência no mês seguinte
    }
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
    pg_query($Conec, "DELETE FROM ".$xProj.".escaladaf_ins WHERE TO_CHAR(dataescalains, 'MM') = '$Mes' And TO_CHAR(dataescalains, 'YYYY') = '$Ano' And grupo_ins = $NumGrupo ");

    //Inicia selecionando 
    $rs = pg_query($Conec, "SELECT id, TO_CHAR(dataescalains, 'DD'), poslog_id, letraturno, turnoturno, destaque, cargatime, turnos_id, horafolga 
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
                $HoraFolga = $tbl[8];

                $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf WHERE dataescala = '$NovaData' And grupo_id = $NumGrupo ");
                $row1 = pg_num_rows($rs1);
                if($row1 > 0){
                    $tbl1 = pg_fetch_row($rs1);
                    $CodIdEscala = $tbl1[0];
                    $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_ins");
                    $tblCod = pg_fetch_row($rsCod);
                    $Codigo = $tblCod[0];
                    $CodigoNovo = ($Codigo+1);

                    pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_ins (id, escaladaf_id, dataescalains, poslog_id, letraturno, turnoturno, destaque, cargatime, usuins, datains, grupo_ins, turnos_id, horafolga) 
                    VALUES ($CodigoNovo, $CodIdEscala, '$NovaData', $PoslogId, '$Letra', '$Turno', $Dest, '$Carga', $UsuIns, NOW(), $NumGrupo, $TurnoId, '$HoraFolga' ) ");
                }
            }
        }
    }
    //Salva nas preferências a escala do mês
    pg_query($Conec, "UPDATE ".$xProj.".poslog SET mes_escdaf = '$MesAno' WHERE pessoas_id = $UsuIns");
    if(!$rs || !$rs1){
        $Erro = 1;
    }


    $MesAtual = date('m');
    $AnoAtual = date('Y');
    $TanoMes = 0;
    if($Mes <= $MesAtual && $Ano <= $AnoAtual){
        $TanoMes = 1;
    }
    pg_query($Conec, "UPDATE ".$xProj.".escalas_gr SET editaesc = $TanoMes WHERE id = $NumGrupo ");

    $var = array("coderro"=>$Erro, "novadata"=>$NovaData, "NumDia"=>$DiaSemDia);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="procChefeDiv"){
    $Erro = 0;
    if(isset($_REQUEST["numgrupo"])){
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal que pode editar escala
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    if($NumGrupo == 0 || $NumGrupo == ""){
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    //Quando fiscal pode editar escala 
    $rs2 = pg_query($Conec, "SELECT siglagrupo FROM ".$xProj.".escalas_gr WHERE id = $NumGrupo;");
    $row2 = pg_num_rows($rs2);
    if($row2 > 0){
        $tbl2 = pg_fetch_row($rs2);
        $SiglaGrupo = $tbl2[0];
    }else{
        $SiglaGrupo = "";
    }

//    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    $rs = pg_query($Conec, "SELECT chefe_escdaf, enc_escdaf FROM ".$xProj.".escalas_gr WHERE id = $NumGrupo");
    $tbl = pg_fetch_row($rs);
    if(!$rs){
        $Erro = 1;
    }
    $rs1 = pg_query($Conec, "SELECT visucargo_daf, primcargo_daF, seminifim_daf, corlistas_daf FROM ".$xProj.".paramsis WHERE idpar = 1");
    if($rs1){
        $tbl1 = pg_fetch_row($rs1);
        $VisuCargo = $tbl1[0];
        $PrimCargo = $tbl1[1];
        $SemaIniFim = $tbl1[2];
        $CorListas = $tbl1[3];
    }else{
        $VisuCargo = 0;
        $PrimCargo = 0;
        $SemaIniFim = 0;
        $CorListas = 0;
    }

    $var = array("coderro"=>$Erro, "chefe"=>$tbl[0], "encarreg"=>$tbl[1], "visucargo"=>$VisuCargo, "primcargo"=>$PrimCargo, "siglagrupo"=>$SiglaGrupo, "semanaIniFim"=>$SemaIniFim, "escolhaCorListas"=>$CorListas);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvachefediv"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    if(isset($_REQUEST["numgrupo"])){
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal que pode editar escala
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    if($NumGrupo == 0 || $NumGrupo == ""){
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
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
//    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    if(isset($_REQUEST["numgrupo"])){
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal que pode editar escala
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    if($NumGrupo == 0 || $NumGrupo == ""){
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
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

    //Procura mes de consulta salvo pelo usuário
    if(isset($_REQUEST["selecmes"])){
        $MesSalvo = $_REQUEST["selecmes"]; // quando vem do fiscal
    }else{
        $MesSalvo = parEsc("mes_escdaf", $Conec, $xProj, $_SESSION["usuarioID"]); 
    }
    //Ver se o que está guardado em poslog corresponde a algum mes salvo em escaladaf
    $rsMes = pg_query($Conec, "SELECT id 
    FROM ".$xProj.".escaladaf WHERE grupo_id = $NumGrupo And CONCAT(TO_CHAR(dataescala, 'MM'), '/', TO_CHAR(dataescala, 'YYYY')) = '$MesSalvo' ");
    $rowMes = pg_num_rows($rsMes);

    if(is_null($MesSalvo) || $MesSalvo == "" || $rowMes == 0){
        $MesSalvo = date("m")."/".date("Y");
        pg_query($Conec, "UPDATE ".$xProj.".poslog SET mes_escdaf = '$MesSalvo' WHERE pessoas_id = ". $_SESSION["usuarioID"]."" );
    }


    $Proc = explode("/", $MesSalvo);
    $Mes = $Proc[0];
    if(strLen($Mes) < 2){
        $Mes = "0".$Mes;
    }
    $Ano = $Proc[1];
    
    $MesAtual = date('m');
    $AnoAtual = date('Y');
    $TanoMes = 0;
    if($Mes <= $MesAtual && $Ano <= $AnoAtual){
        $TanoMes = 1;
    }
    pg_query($Conec, "UPDATE ".$xProj.".escalas_gr SET editaesc = $TanoMes WHERE id = $NumGrupo ");

    $var = array("coderro"=>$Erro, "siglagrupo"=>$SiglaGrupo, "mesSalvo"=>$MesSalvo, "temMes"=>$rowMes);
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
        $Turno24 = 0;
        if(strtotime($HoraI) == strtotime($HoraF)){
//            $RevData = implode("/", array_reverse(explode("/", $Hoje)));
//            $Ini = strtotime(date($RevData)); // número
//            $Amanha = strtotime("+1 day", $Ini);
//            $TurnoF = date("d/m/Y", $Amanha); // data legível
//            $TurnoFim = $TurnoF." ".$HoraF;
            $Turno24 = 1;
        }

        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET calcdataini = '$TurnoIni', calcdatafim = '$TurnoFim' WHERE id = $Cod");
        if($Turno24 == 0){
            pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargahora = (calcdatafim - calcdataini) WHERE id = $Cod");
        }else{
            pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargahora = '24:00' WHERE id = $Cod");
        }

        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = (cargahora - time '01:00'), interv = '01:00' WHERE cargahora >= '08:00' And id = $Cod ");
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = (cargahora - time '00:15'), interv = '00:15' WHERE cargahora >= '06:00' And cargahora < '08:00' And id = $Cod ");
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = cargahora, interv = '00:00' WHERE cargahora <= '06:00' And id = $Cod ");
    }else{
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargahora = '00:00:00' WHERE id = $Cod");
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET interv = '00:00:00' WHERE id = $Cod");
        pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = '00:00:00' WHERE id = $Cod");
    }

    $var = array("coderro"=>$Erro, "horaini"=>$TurnoIni, "horafim"=>$TurnoFim, "codigo"=>$Cod);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="buscaOrdem"){
//    $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    if(isset($_REQUEST["numgrupo"])){ //grupo escolhido
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal que pode editar escala
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    if($NumGrupo == 0 || $NumGrupo == ""){
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }

    $Erro = 0;
    $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_turnos WHERE grupo_turnos = $NumGrupo And ativo = 1");
    $row0 = pg_num_rows($rs0);

    $rs = pg_query($Conec, "SELECT MAX(ordemletra) FROM ".$xProj.".escaladaf_turnos WHERE grupo_turnos = $NumGrupo And ativo = 1");
    if(!$rs){
        $Erro = 1;
        $Ordem = 0;
    }else{
        $tbl = pg_fetch_row($rs);
        $Num = $tbl[0];
        $Ordem = ($Num+1);
    }
//    if($Ordem >= 25){
//        $Ordem = 25;
//    }
    $var = array("coderro"=>$Erro, "ordem"=>$Ordem, "quantTurno"=>$row0);
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

if($Acao =="marcaVale"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Valor = (int) filter_input(INPUT_GET, 'valor');
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET valeref = $Valor WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="cargousudaf"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Valor = filter_input(INPUT_GET, 'valor');
    $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET cargo_daf = '$Valor' WHERE pessoas_id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="marcaVisuCargo"){
    $Erro = 0;
    $Valor = filter_input(INPUT_GET, 'valor');
    $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET visucargo_daf = $Valor WHERE idpar = 1");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="marcaPrimCargo"){
    $Erro = 0;
    $Valor = filter_input(INPUT_GET, 'valor');
    $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET primcargo_daf = $Valor WHERE idpar = 1");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="marcaSemanaIniFim"){
    $Erro = 0;
    $Valor = filter_input(INPUT_GET, 'valor');
    $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET seminifim_daf = $Valor WHERE idpar = 1");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaFeriado"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Data = addslashes(filter_input(INPUT_GET, 'novadata'));
    $Ano = filter_input(INPUT_GET, 'ano');
    $Erro = 0;
    
    $Proc = explode("/", $Data);
    $Dia = $Proc[0];
    if(strLen($Dia) < 2){
        $Dia = "0".$Dia;
    }
    $Mes = $Proc[1];

    $Feriado = $Ano."/".$Mes."/".$Dia;

    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_fer SET dataescalafer = '$Feriado' WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "data"=>$Feriado);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaDescFeriado"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Desc = addslashes(filter_input(INPUT_GET, 'descfer'));
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_fer SET descr = '$Desc' WHERE id = $Cod");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "data"=>$Feriado);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "carregames"){ // sem uso
    if(isset($_REQUEST["numgrupo"])){ //grupo escolhido
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal que pode editar escala
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    if($NumGrupo == 0 || $NumGrupo == ""){
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    $rs = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataescala, 'MM'), '/', TO_CHAR(dataescala, 'YYYY')) 
    FROM ".$xProj.".escaladaf WHERE grupo_id = $NumGrupo GROUP BY TO_CHAR(dataescala, 'MM'), TO_CHAR(dataescala, 'YYYY') ORDER BY TO_CHAR(dataescala, 'YYYY') DESC, TO_CHAR(dataescala, 'MM') DESC ");
    while ($tbl = pg_fetch_row($rs)){
       $Meses[] = array('Mes' => $tbl[0]);
    }
    $responseText = json_encode($Meses);
    echo $responseText;
 }

 if($Acao =="renumeraletras"){
    if(isset($_REQUEST["numgrupo"])){ //grupo escolhido
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal que pode editar escala
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    if($NumGrupo == 0 || $NumGrupo == ""){
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }

    $Erro = 0;
    // seleciona os turnos 
    $rs1 = pg_query($Conec, "SELECT id, ordemletra FROM ".$xProj.".escaladaf_turnos WHERE grupo_turnos = $NumGrupo And ativo = 1 And infotexto = 0 ORDER BY letra");
    $row1 = pg_num_rows($rs1);

    //Seleciona os textos informativos
    $rs2 = pg_query($Conec, "SELECT id, ordemletra FROM ".$xProj.".escaladaf_turnos WHERE grupo_turnos = $NumGrupo And ativo = 1 And infotexto = 1 ORDER BY letra");
    $row2 = pg_num_rows($rs2);

    // Numera primeiro os que são turnos
    if($row1 > 0){
        $Num1 = 1;
        while($tbl1 = pg_fetch_row($rs1)){
            $Cod = $tbl1[0];
            pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET ordemletra = $Num1 WHERE id = $Cod ");
            $Num1++;
        }
    }
    if($row2 > 0){
        $rsCod = pg_query($Conec, "SELECT MAX(ordemletra) FROM ".$xProj.".escaladaf_turnos WHERE grupo_turnos = $NumGrupo And ativo = 1 And infotexto = 0");
        $tblCod = pg_fetch_row($rsCod);
        $Tot = $tblCod[0];
        $Num2 = ($Tot+1);

        if($Num1 <= 21){
            $Num2 = 21;
        }
        if($Num1 <= 16){
            $Num2 = 16;
        }
        if($Num1 <= 10){
            $Num2 = 11;
        }
        if($Num1 > 21){
            $Num2 = 25;
        }
        while($tbl2 = pg_fetch_row($rs2)){
            $Cod = $tbl2[0];
            pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET ordemletra = $Num2 WHERE id = $Cod ");
            $Num2++;
        }
    }
    if(!$rs1){
        $Erro = 1;
    }

    $var = array("coderro"=>$Erro, "Num1"=>$Num1);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaDragEquipe"){
    if(isset($_REQUEST["numgrupo"])){ //grupo escolhido
        $NumGrupo = $_REQUEST["numgrupo"]; // quando vem do fiscal que pode editar escala
    }else{
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    if($NumGrupo == 0 || $NumGrupo == ""){
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
    }
    $Erro = 0;
    if(isset($_POST['posicao']) && is_array( $_POST['posicao'])){
        $Pos = $_POST['posicao'];
        for($i = 0; $i < count($Pos); $i++) {
           pg_query($Conec, "UPDATE ".$xProj.".poslog SET ordem_daf = ".($i+1)." WHERE pessoas_id =". intval($Pos[$i])."");
        }
    }else{
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro, "grupo"=>$NumGrupo);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="salvaCorListas"){
    $Cor = (int) filter_input(INPUT_GET, 'valor');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET corlistas_daf = $Cor WHERE pessoas_id = ".$_SESSION["usuarioID"]." And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "buscaNome"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); //id de poslog
    $Data = filter_input(INPUT_GET, 'data');
    $RevData = implode("/", array_reverse(explode("/", $Data)));

    $rs1 = pg_query($Conec, "SELECT nomecompl, esc_grupo FROM ".$xProj.".poslog WHERE pessoas_id = $Cod");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $tbl1 = pg_fetch_row($rs1);
        $Nome = $tbl1[0];
        $Grupo = $tbl1[1];
    }else{
        $Nome = "";
        $Grupo = "";
    }

    $rs2 = pg_query($Conec, "SELECT letra, turno, observ, escaladafins_id, id_ocor, id_mot, id_stat, id_adm FROM ".$xProj.".escaladaf_func WHERE poslog_id = $Cod And dataescala = '$RevData' And ativo = 1");
    $row2 = pg_num_rows($rs2);
    if($row2 > 0){
        $tbl2 = pg_fetch_row($rs2);
        $Letra = $tbl2[0];
        $Turno = $tbl2[1];
        $Observ = $tbl2[2];
        $Ins_id = $tbl2[3];
        $idOcor = $tbl2[4];
        $idMot = $tbl2[5];
        $idStat = $tbl2[6];
        $idAdm = $tbl2[7];            
    }else{
        $Observ = "";
        $rs3 = pg_query($Conec, "SELECT letraturno, turnoturno, id FROM ".$xProj.".escaladaf_ins WHERE poslog_id = $Cod And dataescalains = '$RevData'");
        $row3 = pg_num_rows($rs3);
        if($row3 > 0){
            $tbl3 = pg_fetch_row($rs3);
            $Letra = $tbl3[0];
            $Turno = $tbl3[1];
            $Ins_id = $tbl3[2];
        }else{
            $Letra = "";
            $Turno = "";
            $Ins_id = 0;
        }
        $idOcor = 1;
        $idMot = 1;
        $idStat = 1;
        $idAdm = 1;
    }
    $var = array("coderro"=>$Erro, "nomecompl"=>$Nome, "letra"=>$Letra, "turno"=>$Turno, "observ"=>$Observ, "grupo"=>$Grupo, "idescalains"=>$Ins_id, "idOcor"=>$idOcor, "idMot"=>$idMot, "idStat"=>$idStat, "idAdm"=>$idAdm);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao == "salvaNotaFunc"){
    $Erro = 0;
    $Cod = (int) filter_input(INPUT_GET, 'codigo'); //id de poslog
    $Data = filter_input(INPUT_GET, 'data');
    $RevData = implode("/", array_reverse(explode("/", $Data)));
    $Observ = addslashes(filter_input(INPUT_GET, 'observ'));
    $Letra = addslashes(filter_input(INPUT_GET, 'letra'));
    $Turno = addslashes(filter_input(INPUT_GET, 'turno'));
    $Grupo = (int) filter_input(INPUT_GET, 'grupo');
    $IdEscalaIns = (int) filter_input(INPUT_GET, 'idEscalaIns');

    $selecOcor = (int) filter_input(INPUT_GET, 'selecOcor');
    $selecMotivo = (int) filter_input(INPUT_GET, 'selecMotivo');
    $selecStatus = (int) filter_input(INPUT_GET, 'selecStatus');
    $selecAcaoAdm = (int) filter_input(INPUT_GET, 'selecAcaoAdm');

    $rs2 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_func WHERE poslog_id = $Cod And dataescala = '$RevData' And ativo = 1");
    $row2 = pg_num_rows($rs2);
    if($row2 > 0){ // atualizar
        $rs3 = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_func SET observ = '$Observ', grupo_id = $Grupo, escaladafins_id = $IdEscalaIns, id_ocor = $selecOcor, id_mot = $selecMotivo, id_stat = $selecStatus, id_adm = $selecAcaoAdm, usuedit = $UsuIns, dataedit = NOW() WHERE poslog_id = $Cod And dataescala = '$RevData' And ativo = 1");
    }else{ // adicionar
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_func");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_func (id, poslog_id, dataescala, observ, letra, turno, grupo_id, escaladafins_id, id_ocor, id_mot, id_stat, id_adm, usuins, datains) 
        VALUES($CodigoNovo, $Cod, '$RevData', '$Observ', '$Letra', '$Turno', $Grupo, $IdEscalaIns, $selecOcor, $selecMotivo, $selecStatus, $selecAcaoAdm, $UsuIns, NOW() )");
        if(!$rs){
            $Erro = 1;
        }
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}

if($Acao =="apagaNotaFunc"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Data = filter_input(INPUT_GET, 'data');
    $RevData = implode("/", array_reverse(explode("/", $Data)));
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_func SET ativo = 0 WHERE poslog_id = $Cod And dataescala = '$RevData' And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}


if($Acao =="editOcor"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT descocor FROM ".$xProj.".escaladaf_funcoc WHERE id = $Cod And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $tbl = pg_fetch_row($rs);
    
    $var = array("coderro"=>$Erro, "desc"=>$tbl[0]);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="editMotivo"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT descmot FROM ".$xProj.".escaladaf_funcmot WHERE id = $Cod And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $tbl = pg_fetch_row($rs);
    
    $var = array("coderro"=>$Erro, "desc"=>$tbl[0]);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="editStat"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT descstat FROM ".$xProj.".escaladaf_funcstat WHERE id = $Cod And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $tbl = pg_fetch_row($rs);
    
    $var = array("coderro"=>$Erro, "desc"=>$tbl[0]);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="editAdm"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "SELECT descadm FROM ".$xProj.".escaladaf_funcadm WHERE id = $Cod And ativo = 1");
    if(!$rs){
        $Erro = 1;
    }
    $tbl = pg_fetch_row($rs);
    
    $var = array("coderro"=>$Erro, "desc"=>$tbl[0]);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="salvaOcor"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Texto = filter_input(INPUT_GET, 'texto');
    $Erro = 0;
    if($Cod > 0){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_funcoc SET descocor = '$Texto' WHERE id = $Cod ");
    }else{
        $CodigoNovo = 0;
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_funcoc");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcoc (id, descocor, usuins, datains) 
        VALUES($CodigoNovo, '$Texto', $UsuIns, NOW() )");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="salvaMotivo"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Texto = filter_input(INPUT_GET, 'texto');
    $Erro = 0;
    if($Cod > 0){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_funcmot SET descmot = '$Texto' WHERE id = $Cod ");
    }else{
        $CodigoNovo = 0;
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_funcmot");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcmot (id, descmot, usuins, datains) 
        VALUES($CodigoNovo, '$Texto', $UsuIns, NOW() )");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="salvaStat"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Texto = filter_input(INPUT_GET, 'texto');
    $Erro = 0;
    if($Cod > 0){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_funcstat SET descstat = '$Texto' WHERE id = $Cod ");
    }else{
        $CodigoNovo = 0;
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_funcstat");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcstat (id, descstat, usuins, datains) 
        VALUES($CodigoNovo, '$Texto', $UsuIns, NOW() )");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="salvaAdm"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Texto = filter_input(INPUT_GET, 'texto');
    $Erro = 0;
    if($Cod > 0){
        $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_funcadm SET descadm = '$Texto' WHERE id = $Cod ");
    }else{
        $CodigoNovo = 0;
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_funcadm");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];
        $CodigoNovo = ($Codigo+1);
        $rs = pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcadm (id, descadm, usuins, datains) 
        VALUES($CodigoNovo, '$Texto', $UsuIns, NOW() )");
    }
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="apagaOcor"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_funcoc SET ativo = 0 WHERE id = $Cod ");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="apagaMotivo"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_funcmot SET ativo = 0 WHERE id = $Cod ");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="apagaStat"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_funcstat SET ativo = 0 WHERE id = $Cod ");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="apagaAdm"){
    $Cod = (int) filter_input(INPUT_GET, 'codigo');
    $Erro = 0;
    $rs = pg_query($Conec, "UPDATE ".$xProj.".escaladaf_funcadm SET ativo = 0 WHERE id = $Cod ");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}
if($Acao =="marcaEscolhaCorListas"){
    $Erro = 0;
    $Valor = filter_input(INPUT_GET, 'valor');
    $rs = pg_query($Conec, "UPDATE ".$xProj.".paramsis SET corlistas_Daf = $Valor WHERE idpar = 1");
    if(!$rs){
        $Erro = 1;
    }
    $var = array("coderro"=>$Erro);
    $responseText = json_encode($var);
    echo $responseText;
}