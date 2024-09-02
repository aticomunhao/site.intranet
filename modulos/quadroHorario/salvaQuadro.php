<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
date_default_timezone_set('America/Sao_Paulo'); 

if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];
    $UsuIns = $_SESSION['usuarioID'];

    if($Acao =="marcaPartic"){
        $Erro = 0;
        $CodPartic = (int) filter_input(INPUT_GET, 'codigo'); // pessoas_id
        $rs0 = pg_query($Conec, "SELECT esc_marca FROM ".$xProj.".poslog WHERE pessoas_id = $CodPartic");
        $tbl0 = pg_fetch_row($rs0);
        $Marca = (int) $tbl0[0];
        if($Marca == 0){
            $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_marca = 1 WHERE pessoas_id = $CodPartic");
        }else{
            $rs = pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_marca = 0 WHERE pessoas_id = $CodPartic");
        }
        if(!$rs){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro);
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

        $HoraI = addslashes(filter_input(INPUT_GET, 'horaini')); 
        $Proc = explode(":", $HoraI);
        $Hora = $Proc[0];
        $Min = $Proc[1];
        if($Hora == 24 && $Min != "00"){
            $Erro = 2;
            $var = array("coderro"=>$Erro, "horaini"=>$HoraIni, "horafim"=>$HoraFim);
            $responseText = json_encode($var);
            echo $responseText;
            return;
        }
        $HoraF = addslashes(filter_input(INPUT_GET, 'horafim')); 
        $Proc = explode(":", $HoraF);
        $Hora = $Proc[0];
        $Min = $Proc[1];
        if($Hora == 24 && $Min != "00"){
            $Erro = 2;
            $var = array("coderro"=>$Erro, "horaini"=>$HoraIni, "horafim"=>$HoraFim);
            $responseText = json_encode($var);
            echo $responseText;
            return;
        }

        $rs0 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_horaini = '$HoraIni', esc_horafim = '$HoraFim' WHERE pessoas_id = $CodPartic");
        if(!$rs0){
            $Erro = 1;
        }
        //Salva o turno para futuro inserção facilitada
        $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos WHERE horaini = '$HoraIni' And horafim = '$HoraFim' ");
        $row = pg_num_rows($rs);
        if($row == 0){
            $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".quadroturnos");
            $tblCod = pg_fetch_row($rsCod);
            $Codigo = $tblCod[0];
            $CodigoNovo = ($Codigo+1);
            pg_query($Conec, "INSERT INTO ".$xProj.".quadroturnos (id, horaini, horafim, usuins, datains) VALUES($CodigoNovo, '$HoraIni', '$HoraFim', $UsuIns, NOW() )");
        }

        $var = array("coderro"=>$Erro, "horaini"=>$HoraIni, "horafim"=>$HoraFim);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="insTurno"){
        $Erro = 0;
        $CodPartic = (int) filter_input(INPUT_GET, 'codpartic'); // pessoas_id
        $CodTurno = (int) filter_input(INPUT_GET, 'codturno'); // id de quadroturnos

        $rs = pg_query($Conec, "SELECT horaini, horafim FROM ".$xProj.".quadroturnos WHERE id = $CodTurno");
        $tbl = pg_fetch_row($rs);
        $HoraIni = $tbl[0];
        $HoraFim = $tbl[1];

        $rs0 = pg_query($Conec, "UPDATE ".$xProj.".poslog SET esc_horaini = '$HoraIni', esc_horafim = '$HoraFim' WHERE pessoas_id = $CodPartic");
        if(!$rs0){
            $Erro = 1;
        }
        $var = array("coderro"=>$Erro, "horaini"=>$HoraIni, "horafim"=>$HoraFim);
        $responseText = json_encode($var);
        echo $responseText;
    }
    
    if($Acao =="insParticipante"){
        $Erro = 0;
        $CodId = (int) filter_input(INPUT_GET, 'codid'); // id de quadrohor
        $Data = addslashes(filter_input(INPUT_GET, 'data'));
        $RevData = implode("/", array_reverse(explode("/", $Data)));
        $Turno = (int) filter_input(INPUT_GET, 'turno');
        $NumGrupo = (int) filter_input(INPUT_GET, 'numgrupo');

        pg_query($Conec, "DELETE FROM ".$xProj.".quadroins WHERE quadrohor_id = $CodId;");

        $rs = pg_query($Conec, "SELECT pessoas_id, esc_horaini, esc_horafim FROM ".$xProj.".poslog WHERE ativo = 1 And esc_eft = 1 And esc_grupo = $NumGrupo And esc_marca = 1 ");
        $row = pg_num_rows($rs);
        if($row > 0){
            while($tbl = pg_fetch_row($rs)){
                $CodPartic = $tbl[0];
                $HoraIni = $RevData." ".$tbl[1];
                $HoraFim = $RevData." ".$tbl[2];

                //Caso do pernoite
                $HoraI = (int) $tbl[1];
                $HoraF = (int) $tbl[2];
                if($HoraF < $HoraI){ // se a hora fim for menor que hora ini
                    $Ini = strtotime(date($RevData)); // número
                    $Amanha = strtotime("+1 day", $Ini);
                    $RevDataMais = date("Y/m/d", $Amanha); // data legível
                    $HoraFim = $RevDataMais." ".$tbl[2];
                }

                pg_query($Conec, "INSERT INTO ".$xProj.".quadroins (quadrohor_id, turno".$Turno."_id, horaini".$Turno.", horafim".$Turno.", usuins, datains) 
                VALUES($CodId, $CodPartic, '$HoraIni', '$HoraFim', $UsuIns, NOW() )");
            }
        }

        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }



}