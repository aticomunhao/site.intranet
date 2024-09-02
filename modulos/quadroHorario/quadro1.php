<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="comp/js/jquery-confirm.min.js"></script> <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="comp/js/jquery.mask.js"></script>
        <style>
            .modal-content-selecParticip{
                background: linear-gradient(180deg, white, #FFF8DC);
                margin: 12% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 40%;
            }
            .modal-content-relacParticip{
                background: linear-gradient(180deg, white, #FFF8DC);
                margin: 12% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 50%;
            }
            .modalMsg-content-Escala{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 7% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%; /* acertar de acordo com a tela */
            }
            .quadrinho {
                font-size: 90%;
                min-width: 33px;
                border: 1px solid #C0C0C0;
                border-radius: 3px;
                padding-left: 5px;
                padding-right: 5px;
            }
            .quadrinhoClick {
                font-size: 90%;
                min-width: 50px;
                border: 1px solid;
                border-radius: 3px;
                padding-left: 5px;
                padding-right: 5px;
                cursor: pointer;
            }
            .quadrgrupo {
                font-size: 90%;
                min-width: 33px;
                border: 1px solid #C0C0C0;
                border-radius: 3px;
                padding-left: 5px;
                padding-right: 5px;
            }

        </style>
        <script>
             function ajaxIni(){
                try{
                ajax = new ActiveXObject("Microsoft.XMLHTTP");}
                catch(e){
                try{
                   ajax = new ActiveXObject("Msxml2.XMLHTTP");}
                   catch(ex) {
                   try{
                       ajax = new XMLHttpRequest();}
                       catch(exc) {
                          alert("Esse browser não tem recursos para uso do Ajax");
                          ajax = null;
                       }
                   }
                }
            }
            $(document).ready(function(){
                document.getElementById("selecMesAno").value = document.getElementById("guardames").value;
                $("#faixacentral").load("modulos/quadroHorario/jQuadro.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                $("#estat").load("modulos/quadroHorario/jCarga.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                document.getElementById("etiqgrupo").style.visibility = "hidden";
                document.getElementById("selecGrupo").style.visibility = "hidden";
                document.getElementById("botimprPlanilha").style.visibility = "hidden";

                $("#selecMesAno").change(function(){
                    if(parseInt(document.getElementById("selecMesAno").value) > 0){
                        $("#faixacentral").load("modulos/quadroHorario/jQuadro.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                        $("#estat").load("modulos/quadroHorario/jCarga.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                    }
                });
                $("#horainiedit").mask("99:99");
                $("#horafimedit").mask("99:99");
            }); // fim do ready

            function abreParticip(Turno, Cod, CodPartic, Data, Nome){
                if(parseInt(document.getElementById("guardaescalante").value) === 0){
                    return false;
                }
                document.getElementById("guardaData").value = Data;
                document.getElementById("guardaTurno").value = Turno;
                document.getElementById("guardaCod").value = Cod;
                document.getElementById("guardaCodParticip").value = CodPartic;
                document.getElementById("titulomodal").innerHTML = Data;
                if(parseInt(document.getElementById("guardaQuantTurnos").value) > 1){
//                    document.getElementById("turnotitmodal").innerHTML = "Turno "+Turno;
                }
                if(parseInt(Nome.length) > 1){
                    document.getElementById("nometitmodal").innerHTML = " - "+Nome;
//                    document.getElementById("retiranomemodal").innerHTML = "Retirar";
                }else{
                    document.getElementById("nometitmodal").innerHTML = " ";
                    document.getElementById("retiranomemodal").innerHTML = " ";                   
                }
                if(parseInt(CodPartic) > 0){
                    $.confirm({
                        title: 'Confirmação!',
                        content: 'Confirma modificar a escala <br>no dia '+Data+' ?',
                        autoClose: 'Não|10000',
                        draggable: true,
                        buttons: {
                            Sim: function () {
                                $("#relacaoParticip").load("modulos/quadroHorario/jEquipes.php?codigo="+document.getElementById("guardanumgrupo").value);
                                document.getElementById("relacParticip").style.display = "block";
                            },
                            Não: function () {}
                        }
                    });
                }else{
                    $("#relacaoParticip").load("modulos/quadroHorario/jEquipes.php?codigo="+document.getElementById("guardanumgrupo").value);
                    document.getElementById("relacParticip").style.display = "block";
                }
            }

            function MarcaPartic(Cod){ // vem de jEquipes
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/quadroHorario/salvaQuadro.php?acao=marcaPartic&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function editaParticip(Cod){
                //Cod = pessoas_id
                document.getElementById("guardaEditCod").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/quadroHorario/salvaQuadro.php?acao=buscaParticip&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{;
                                    document.getElementById("nomecompletoedit").innerHTML = Resp.nomecompl;
                                    if(Resp.nome != "" && Resp.nome != null){
                                        document.getElementById("nomecompletoedit").innerHTML = Resp.nome+" - "+Resp.nomecompl;
                                    }
                                    document.getElementById("horainiedit").value = Resp.horaini;
                                    document.getElementById("horafimedit").value = Resp.horafim;
                                    document.getElementById("editaModalParticip").style.display = "block";
                                    document.getElementById("horainiedit").focus();
                               }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function salvaEditEscalado(){
                TamIni = document.getElementById("horainiedit").value;
                if(TamIni.length < 5){
                    document.getElementById("horainiedit").focus();
                    return false;
                }
                TamFim = document.getElementById("horafimedit").value;
                if(TamFim.length < 5){
                    document.getElementById("horafimedit").focus();
                    return false;
                }

                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/quadroHorario/salvaQuadro.php?acao=salvaParticip&codigo="+document.getElementById("guardaEditCod").value
                    +"&horaini="+encodeURIComponent(document.getElementById("horainiedit").value)
                    +"&horafim="+encodeURIComponent(document.getElementById("horafimedit").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else if(parseInt(Resp.coderro) === 2){
                                    alert("Verifique os horários: "+Resp.horaini+"/"+Resp.horafim);
                                }else{
                                    $("#relacaoParticip").load("modulos/quadroHorario/jEquipes.php?codigo="+document.getElementById("guardanumgrupo").value);
                                    document.getElementById("editaModalParticip").style.display = "none";
                               }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function buscaTurno(CodPartic, CodTurno){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/quadroHorario/salvaQuadro.php?acao=insTurno&codpartic="+CodPartic+"&codturno="+CodTurno, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#relacaoParticip").load("modulos/quadroHorario/jEquipes.php?codigo="+document.getElementById("guardanumgrupo").value);
                               }
                            }
                        }
                    };
                    ajax.send(null);
                }

            }

            function insParticipante(){
                //Cod = pessoas_id de poslog
                //GuardaCod = id de escalas
                //GuardaTurno =  1 a 4 de onde clica
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/quadroHorario/salvaQuadro.php?acao=insParticipante&numgrupo="+document.getElementById("guardanumgrupo").value
                    +"&data="+encodeURIComponent(document.getElementById("guardaData").value)
                    +"&codid="+document.getElementById("guardaCod").value
                    +"&turno="+document.getElementById("guardaTurno").value
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else if(parseInt(Resp.coderro) === 2){
                                    $.confirm({
                                        title: 'Atenção!',
                                        content: 'É necessário inserir os horários dos turnos.<br>Clique em Editar.',
                                        autoClose: 'OK|10000',
                                        draggable: true,
                                        buttons: {
                                            OK: function(){}
                                        }
                                    });
                                    return false;
                                }else if(parseInt(Resp.coderro) === 3){ // escalado em outro grupo
                                    $.confirm({
                                        title: 'Atenção!',
                                        content: 'Parece que este participante já está escalado neste mesmo dia em outro grupo: '+Resp.siglagrupo,
                                        draggable: true,
                                        buttons: {
                                            OK: function(){}
                                        }
                                    });
                                    $("#faixacentral").load("modulos/QuadroHorario/jQuadro.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                                    $("#estat").load("modulos/quadroHorario/jCarga.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                                    document.getElementById("relacParticip").style.display = "none";
                                }else{
                                    $("#faixacentral").load("modulos/quadroHorario/jQuadro.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                                    $("#estat").load("modulos/quadroHorario/jCarga.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                                    document.getElementById("relacParticip").style.display = "none";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function imprQuadro(){
                window.open("modulos/quadroHorario/imprQuadro.php?acao=imprQuadro&numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), document.getElementById("selecMesAno").value);
            }
            function imprPlanilha(){
                window.open("modulos/quadroHorario/imprPlan.php?acao=imprPlan&numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), document.getElementById("selecMesAno").value);
            }
            
            function fechaRelaPart(){
                document.getElementById("relacParticip").style.display = "none";
            }
            function fechaEditaPart(){
                document.getElementById("editaModalParticip").style.display = "none";
                $("#relacaoParticip").load("modulos/quadroHorario/jEquipes.php?codigo="+document.getElementById("guardanumgrupo").value);
            }
            function foco(id){
                document.getElementById(id).focus();
            }

        </script>
    </head>
    <body>
        <?php
        if(!$Conec){
            echo "Sem contato com o PostGresql";
            return false;
        }
        date_default_timezone_set('America/Sao_Paulo'); //Um dia = 86.400 seg

//Provisório
//pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".quadrohor");
pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".quadrohor (
    id SERIAL PRIMARY KEY, 
    dataescala date DEFAULT '3000-12-31',
    grupo_id integer NOT NULL DEFAULT 0,
    ativo smallint NOT NULL DEFAULT 1, 
    usuins bigint NOT NULL DEFAULT 0,
    datains timestamp without time zone DEFAULT '3000-12-31',
    usuedit bigint NOT NULL DEFAULT 0,
    dataedit timestamp without time zone DEFAULT '3000-12-31' 
    ) 
 ");

 //pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".quadroins");
 pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".quadroins (
    id SERIAL PRIMARY KEY, 
    quadrohor_id bigint NOT NULL DEFAULT 0,
    turno1_id BIGINT NOT NULL DEFAULT 0,
    horaini1 timestamp without time zone, 
    horafim1 timestamp without time zone,
    ativo smallint NOT NULL DEFAULT 1, 
    usuins bigint NOT NULL DEFAULT 0,
    datains timestamp without time zone DEFAULT '3000-12-31',
    usuedit bigint NOT NULL DEFAULT 0,
    dataedit timestamp without time zone DEFAULT '3000-12-31' 
    ) 
 ");
 //pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".quadroturnos");
 pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".quadroturnos (
    id SERIAL PRIMARY KEY, 
    horaini VARCHAR(10), 
    horafim VARCHAR(10),
    ativo smallint NOT NULL DEFAULT 1, 
    usuins bigint NOT NULL DEFAULT 0,
    datains timestamp without time zone DEFAULT '3000-12-31',
    usuedit bigint NOT NULL DEFAULT 0,
    dataedit timestamp without time zone DEFAULT '3000-12-31' 
    ) 
 ");
 $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".quadroturnos LIMIT 3");
 $row = pg_num_rows($rs);
 if($row == 0){
    pg_query($Conec, "INSERT INTO ".$xProj.".quadroturnos (id, horaini, horafim, usuins, datains) VALUES(1, '08:00', '17:00', 3, NOW() )");
    pg_query($Conec, "INSERT INTO ".$xProj.".quadroturnos (id, horaini, horafim, usuins, datains) VALUES(2, '07:00', '16:00', 3, NOW() )");
    pg_query($Conec, "INSERT INTO ".$xProj.".quadroturnos (id, horaini, horafim, usuins, datains) VALUES(3, '07:00', '17:00', 3, NOW() )");
    pg_query($Conec, "INSERT INTO ".$xProj.".quadroturnos (id, horaini, horafim, usuins, datains) VALUES(4, '09:00', '18:00', 3, NOW() )");
    pg_query($Conec, "INSERT INTO ".$xProj.".quadroturnos (id, horaini, horafim, usuins, datains) VALUES(5, '14:00', '18:00', 3, NOW() )");
    pg_query($Conec, "INSERT INTO ".$xProj.".quadroturnos (id, horaini, horafim, usuins, datains) VALUES(6, '11:00', '15:00', 3, NOW() )");
    pg_query($Conec, "INSERT INTO ".$xProj.".quadroturnos (id, horaini, horafim, usuins, datains) VALUES(7, '08:00', '14:15', 3, NOW() )");
    pg_query($Conec, "INSERT INTO ".$xProj.".quadroturnos (id, horaini, horafim, usuins, datains) VALUES(8, '06:50', '15:50', 3, NOW() )");
 }

        $Escalante = parEsc("esc_edit", $Conec, $xProj, $_SESSION["usuarioID"]); // escalante do grupo
        $FiscEscala = parEsc("esc_fisc", $Conec, $xProj, $_SESSION["usuarioID"]); // fiscal de escala
        $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);

        if($FiscEscala == 1){
            $NumGrupo = filter_input(INPUT_GET, 'numgrupo'); // = 1
        }

        $rs = pg_query($Conec, "SELECT siglagrupo, guardaescala FROM ".$xProj.".escalas_gr WHERE id = $NumGrupo;");
        $row = pg_num_rows($rs);
        if($row > 0){
            $tbl = pg_fetch_row($rs);
            $Grupo = $tbl[0];
            $MesSalvo = $tbl[1];
        }else{
            $Grupo = "";
            $MesSalvo = "";
        }
        if($MesSalvo == 0){
            $MesSalvo = date("m")."/".date("Y");
        }

        $rsGr = pg_query($Conec, "SELECT qtd_turno FROM ".$xProj.".escalas_gr WHERE id = '$NumGrupo' ");
        $rowGr = pg_num_rows($rsGr);
        if($rowGr > 0){
            $tblGr = pg_fetch_row($rsGr);
            $Turnos = $tblGr[0];
        }else{
            $Turnos = 1;
        }
        
        $Turnos = 1;

        $Ini = strtotime(date('Y/m/01')); // número - para começar com o dia 1
        $DiaIni = strtotime("-1 day", $Ini); // para começar com o dia 1 no loop for


        //Mantem a tabela meses à frente
        for($i = 0; $i < 180; $i++){
            $Amanha = strtotime("+1 day", $DiaIni);
            $DiaIni = $Amanha;
            $Data = date("Y/m/d", $Amanha); // data legível
            $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".quadrohor WHERE dataescala = '$Data' And grupo_id = $NumGrupo ");
            $row0 = pg_num_rows($rs0);
            if($row0 == 0){
                pg_query($Conec, "INSERT INTO ".$xProj.".quadrohor (dataescala, grupo_id, usuins, datains) VALUES ('$Data', $NumGrupo, ".$_SESSION["usuarioID"].", NOW())");
            }
        }

        $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataescala, 'MM'), '/', TO_CHAR(dataescala, 'YYYY')) 
        FROM ".$xProj.".quadrohor GROUP BY TO_CHAR(dataescala, 'MM'), TO_CHAR(dataescala, 'YYYY') ORDER BY TO_CHAR(dataescala, 'YYYY') DESC, TO_CHAR(dataescala, 'MM') DESC ");
        $OpcoesGrupo = pg_query($Conec, "SELECT id, siglagrupo FROM ".$xProj.".escalas_gr ORDER BY siglagrupo");

        ?>
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="mudou" value = "0" />
        <input type="hidden" id="guardanumgrupo" value = "<?php echo $NumGrupo; ?>" />
        <input type="hidden" id="guardames" value = "<?php echo addslashes($MesSalvo); ?>" />
        <input type="hidden" id="guardaescalante" value = "<?php echo $Escalante; ?>" />
        <input type="hidden" id="guardaQuantTurnos" value = "<?php echo $Turnos; ?>" />
        <input type="hidden" id="guardaCod" value = "" />
        <input type="hidden" id="guardaEditCod" value = "" />
        <input type="hidden" id="guardaCodParticip" value = "" />
        <input type="hidden" id="guardaData" value = "" />
        <input type="hidden" id="guardaTurno" value = "" />

        <div style="margin: 20px; padding: 5px;">
            <!-- div três colunas -->
            <div class="container" style="margin: 0 auto;">
                <div class="row" style="text-align: center;">
                    <div class="col" style="text-align: center; margin: 5px; width: 95%; padding: 2px; padding-top: 5px;">
                        <label>Selecione o mês: </label>
                        <select id="selecMesAno" style="font-size: 1rem; width: 90px;" title="Selecione o mês/ano.">
                            <option value="0"></option>
                                <?php 
                                    if($OpcoesEscMes){
                                        while ($Opcoes = pg_fetch_row($OpcoesEscMes)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                        </select>
                    </div>
                    <div class="col" style="text-align: center;"><h4>Escala Grupo <?php echo $Grupo; ?></h4></div> <!-- Central - espaçamento entre colunas  -->

                <div class="col" style="text-align: right; margin: 5px; width: 95%; padding: 2px;">
                    <label id="etiqgrupo">Grupo: </label>
                    <select id="selecGrupo" style="font-size: 1rem; width: 90px;" title="Selecione o grupo.">
                        <option value="0"></option>
                            <?php 
                                if($OpcoesGrupo){
                                    while ($Opcoes = pg_fetch_row($OpcoesGrupo)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                        <?php 
                                    }
                                }
                            ?>
                    </select>

                    <label style="padding-left: 30px;"></label>
                    <button class="botpadrred" id="botimprEsc" style="font-size: 80%;" onclick="imprQuadro();">Gerar PDF</button>
                    <button class="botpadrred" id="botimprPlanilha" style="font-size: 80%;" onclick="imprPlanilha();" title="Planilha">P</button>
                    <label style="padding-left: 10px;"></label>
                    <img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpEscala();" title="Guia rápido">
                </div>
            </div>      
            <br>
        </div>

            <!-- div três colunas -->
            <div style="margin: 0 auto;">
                <div style="position: relative; float: left; text-align: center; width: 70%; border: 1px solid; border-radius: 10px;"><div id="faixacentral"></div></div>
                <div style="position: relative; float: left; text-align: center; width: 1%;">&nbsp;</div>
                <div style="position: relative; float: left; text-align: center; width: 27%; border: 1px solid; border-radius: 10px;"><div id="estat"></div></div>
            </div>

        <!-- div modal relacionar escalado -->
        <div id="relacParticip" class="relacmodal">
            <div class="modal-content-relacParticip">
                <span class="close" onclick="fechaRelaPart();">&times;</span>
                <label style="color: #666;">Escala para o dia: &nbsp; </label><label id="titulomodal" style="color: #666; padding-bottom: 10px;"></label>
                <label id="turnotitmodal" style="color: #666; padding-bottom: 10px;"></label>
                <label id="nometitmodal" style="color: #666; padding-bottom: 10px; font-weight: bold;"></label>
                <label id="retiranomemodal" style="color: blue; text-decoration: underline; padding-bottom: 10px; padding-left: 30px; cursor: pointer;" onclick="apagaEscalado();" title="retira da escala deste dia e turno"></label>

                <!-- lista dos participantes da escala do grupo -->
                <div id="relacaoParticip" style="border: 2px solid #C6E2FF; border-radius: 10px;"></div>

                <button class="botpadrblue" style="font-size: 80%;" onclick="insParticipante();">Inserir Marcados</button>
            </div>
        </div> <!-- Fim Modal-->

        <div id="editaModalParticip" class="relacmodal">
            <div class="modal-content-relacParticip">
                <span class="close" onclick="fechaEditaPart();">&times;</span>
                <label style="color: #666;">Edição de horários preferenciais:</label>
                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td class="etiq" style="padding-bottom: 7px;">Nome: </td>
                        <td colspan="5" style="padding-bottom: 10px; min-width: 200px;"><label id="nomecompletoedit"></label></td>
                    </tr>
                    <tr>
                        <td class="etiq">Hora Início: </td>
                        <td colspan="3" style="width: 100px;"><input type="text" id="horainiedit" style="width: 70px; text-align: center;" onkeypress="if(event.keyCode===13){javascript:foco('horafimedit');return false;}" title="inserir 4 dígitos. Ex 0800"/>
                            <label class="etiq" style="padding-left: 10px;">Hora término: </label>
                            <input type="text" id="horafimedit" style="width: 70px; text-align: center;" onkeypress="if(event.keyCode===13){javascript:foco('horainiedit');return false;}" title="inserir 4 dígitos. Ex 1800" />
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align: center; padding-top: 10px;"><button class="botpadrblue" onclick="salvaEditEscalado();">Salvar</button></td>
                    </tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->


    </body>
</html>