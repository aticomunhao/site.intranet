<?php
session_start();
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}

require_once(dirname(dirname(__FILE__))."/config/abrealas.php");

date_default_timezone_set('America/Sao_Paulo'); 

if(isset($_REQUEST["acao"])){
    $Acao = $_REQUEST["acao"];

    if($Acao =="salvaParticip"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo');
        $Turno = (int) filter_input(INPUT_GET, 'turno');
        $CodPartic = (int) filter_input(INPUT_GET, 'codparticip');
        $HoraIni = filter_input(INPUT_GET, 'horaini');
        $HoraFim = filter_input(INPUT_GET, 'horafim');

        $rs = pg_query($Conec, "UPDATE ".$xProj.".escalas SET turno".$Turno."_id = $CodPartic, horaini".$Turno." = '$HoraIni', horafim".$Turno." = '$HoraFim', usuedit = ".$_SESSION['usuarioID'].", dataedit = NOW() WHERE id = $Cod ");
        if(!$rs){
            $Erro = 1;
        }

        $var = array("coderro"=>$Erro);
        $responseText = json_encode($var);
        echo $responseText;
    }

    if($Acao =="buscaDados"){
        $Erro = 0;
        $Cod = (int) filter_input(INPUT_GET, 'codigo');
        $Turno = (int) filter_input(INPUT_GET, 'turno');

        $rs = pg_query($Conec, "SELECT turno".$Turno."_id, horaini".$Turno.", horafim".$Turno." FROM ".$xProj.".escalas WHERE id = $Cod ");
        if(!$rs){
            $Erro = 1;
            $var = array("coderro"=>$Erro);
        }else{
            $tbl = pg_fetch_row($rs);
            $Ini = $tbl[1];
            if($Ini < 0){
                $Ini = "0".$Ini;
            }
            $var = array("coderro"=>$Erro, "codigo"=>$tbl[0], "horaini"=>$Ini, "horafim"=>$tbl[2]);
        }
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

}