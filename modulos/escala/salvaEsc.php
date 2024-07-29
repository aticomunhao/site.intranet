<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
date_default_timezone_set('America/Sao_Paulo'); 

if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];

    if($Acao =="insParticip"){
        $Erro = 0;
        $CodPartic = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id
        $CodId = (int) filter_input(INPUT_GET, 'codid'); // id de escalas
        $Data = addslashes(filter_input(INPUT_GET, 'data'));
        $RevData = implode("/", array_reverse(explode("/", $Data)));
        $Turno = (int) filter_input(INPUT_GET, 'turno');

        $rs0 = pg_query($Conec, "SELECT esc_horaini, esc_horafim, esc_grupo FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic");
        $tbl0 = pg_fetch_row($rs0);
        if(is_null($tbl0[0]) || is_null($tbl0[1])){
            $Erro = 2;
            $var = array("coderro"=>$Erro);
            $responseText = json_encode($var);
            echo $responseText;
            return false;
        }
        $HoraIni = $RevData." ".$tbl0[0];
        $HoraFim = $RevData." ".$tbl0[1];
        $NumGrupo = $tbl0[2];

        //Caso do pernoite
        $HoraI = (int) $tbl0[0];
        $HoraF = (int) $tbl0[1];
        if($HoraF < $HoraI){ // se a hora fim for menor que hora ini
            $Ini = strtotime(date($RevData)); // número
            $Amanha = strtotime("+1 day", $Ini);
            $RevDataMais = date("Y/m/d", $Amanha); // data legível
            $HoraFim = $RevDataMais." ".$tbl0[1];
        }

        $rs = pg_query($Conec, "UPDATE ".$xProj.".escalas SET turno".$Turno."_id = $CodPartic, horaini".$Turno." = '$HoraIni', horafim".$Turno." = '$HoraFim', usuedit = ".$_SESSION['usuarioID'].", dataedit = NOW() WHERE id = $CodId ");
        if(!$rs){
            $Erro = 1;
        }

        //Procura se está em outra escala no mesmo dia
        $SiglaGrupo = "";
        $rs1 = pg_query($Conec, "SELECT id, grupo_id FROM ".$xProj.".escalas 
        WHERE turno".$Turno."_id = $CodPartic And horaini".$Turno." = '$HoraIni' And grupo_id != $NumGrupo");
        $row1 = pg_num_rows($rs1);
        if($row1 > 0){
            $tbl1 = pg_fetch_row($rs1);
            $NumGrupo = $tbl1[1];
            
            $rs2 = pg_query($Conec, "SELECT siglagrupo FROM ".$xProj.".escalas_gr WHERE id = $NumGrupo");
            $row2 = pg_num_rows($rs2);
            if($row2 > 0){
                $tbl2 = pg_fetch_row($rs2);
                $SiglaGrupo = $tbl2[0];
            }
            $Erro = 3;

        }
        $var = array("coderro"=>$Erro, "horaini"=>$HoraIni, "horafim"=>$HoraFim, "temOutro"=>$row1, "siglagrupo"=>$SiglaGrupo);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="buscaParticip"){
        $Erro = 0;
        $CodPartic = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id
        $Nome = "";
        $NomeCompl = "";
        $HoraIni = "";
        $HoraFim = "";

        $rs0 = pg_query($Conec, "SELECT nomeusual, nomecompl, esc_horaini, esc_horafim FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic");
        if(!$rs0){
            $Erro = 1;
        }else{
            $tbl0 = pg_fetch_row($rs0);
            $Nome = $tbl0[0];
            $NomeCompl = $tbl0[1];
            $HoraIni = $tbl0[2];
            $HoraFim = $tbl0[3];
        }
        $var = array("coderro"=>$Erro, "nome"=>$Nome, "nomecompl"=>$NomeCompl, "horaini"=>$HoraIni, "horafim"=>$HoraFim);
        $responseText = json_encode($var);
        echo $responseText;
    }
    
    if($Acao =="salvaParticip"){
        $Erro = 0;
        $CodPartic = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id
        $HoraIni = addslashes(filter_input(INPUT_GET, 'horaini'));
        $HoraFim = addslashes(filter_input(INPUT_GET, 'horafim'));

        $rs0 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_horaini = '$HoraIni', esc_horafim = '$HoraFim' WHERE pessoas_id = $CodPartic");
        if(!$rs0){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro, "horaini"=>$HoraIni, "horafim"=>$HoraFim);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="buscaGrupo"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo');
        $Turno = (int) filter_input(INPUT_GET, 'turno');

        $rs = pg_query($Conec, "SELECT siglagrupo, descgrupo, descescala, qtd_turno FROM ".$xProj.".escalas_gr WHERE id = $Cod ");
        if(!$rs){
            $Erro = 1;
            $var = array("coderro"=>$Erro);
        }else{
            $tbl = pg_fetch_row($rs);
            $var = array("coderro"=>$Erro, "siglagrupo"=>$tbl[0], "descgrupo"=>$tbl[1], "descescala"=>$tbl[2], "turnos"=>$tbl[3]);
        }
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
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escalas_gr");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1);
            $rs = pg_query($Conec, "INSERT INTO ".$xProj.".escalas_gr (id, siglagrupo, descgrupo, descescala, qtd_turno) 
            VALUES ($CodigoNovo, '$Sigla', '$Nome', '$Descr', $Turnos) ");
        }
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }
    
    if($Acao =="apagaEscala"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo');
        $Turno = (int) filter_input(INPUT_GET, 'turno');
        $rs = pg_query($Conec, "UPDATE ".$xProj.".escalas SET turno".$Turno."_id = 0, horaini".$Turno." = null, horafim".$Turno." = null, usuedit = ".$_SESSION['usuarioID'].", dataedit = NOW() WHERE id = $Cod ");
        if(!$rs){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }


}