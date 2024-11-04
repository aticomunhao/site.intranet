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
            .quadrodia {
                font-size: 90%;
                min-width: 33px;
                border: 1px solid;
                border-radius: 3px;
                cursor: pointer;
            }
            .bEdit{
                position: absolute; 
                left: 30px;
                margin-top: -12px; 
                border: 1px solid blue;
                background-color: blue;
                font-size: 80%;
                color: white;
                cursor: pointer;
                border-radius: 10px; 
                padding-left: 10px; 
                padding-right: 10px; 
            }
            .bSalvar{
                position: relative; 
                float: left;
                border: 1px solid blue;
                background-color: blue;
                font-size: 80%;
                color: white;
                cursor: pointer;
                border-radius: 10px; 
                padding-left: 10px; 
                padding-right: 10px; 
            }
            .modal-content-relacGrupos{
                background: linear-gradient(180deg, white, #FFF8DC);
                margin: 12% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 50%;
            }
            .modal-content-editGrupos{
                background: linear-gradient(180deg, white, #edd882);
                margin: 12% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 50%;
            }
            .modalMsg-content{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%; /* acertar de acordo com a tela */
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
                $("#selecGrupo").change(function(){
                    if(parseInt(document.getElementById("selecGrupo").value) > 0){
                        $('#container3').load('modulos/escala/escalas.php?numgrupo='+document.getElementById("selecGrupo").value);
                    }
                });
            }); // fim do ready
            function abreGrupos(){
                $("#relacao").load("modulos/escala/jGrupos.php");
                document.getElementById("relacGrupos").style.display = "block";
            }
            function fechaModalGrupos(){
                document.getElementById("relacGrupos").style.display = "none";
            }
            function fechaEditaGrupos(){
                document.getElementById("relacEditaGrupos").style.display = "none";
            }
            function editaGrupo(Cod){
                document.getElementById("mudou").value = "0";
                document.getElementById("guardacodgrupo").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escala/salvaEsc.php?acao=buscaGrupo&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("siglagrupo").value = Resp.siglagrupo;
                                    document.getElementById("nomegrupo").value = Resp.descgrupo;
                                    document.getElementById("descgrupo").value = Resp.descescala;
                                    document.getElementById("selecTurnos").value = Resp.turnos;
                                    document.getElementById("relacEditaGrupos").style.display = "block";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function inserirGrupo(){
                document.getElementById("guardacodgrupo").value = 0;
                document.getElementById("siglagrupo").value = "";
                document.getElementById("nomegrupo").value = "";
                document.getElementById("descgrupo").value = "";
                document.getElementById("selecTurnos").value = "1";
                document.getElementById("relacEditaGrupos").style.display = "block";
            }
            function salvaGrupo(){
                if(document.getElementById("mudou").value != "0"){
                    if(document.getElementById("siglagrupo").value == ""){
                        document.getElementById("siglagrupo").focus();
                        return false;
                    }
                    if(document.getElementById("nomegrupo").value == ""){
                        document.getElementById("nomegrupo").focus();
                        return false;
                    }
                    if(document.getElementById("selecTurnos").value == ""){
                        document.getElementById("selecTurnos").focus();
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/escala/salvaEsc.php?acao=salvaGrupo&codigo="+document.getElementById("guardacodgrupo").value
                        +"&siglagrupo="+document.getElementById("siglagrupo").value
                        +"&selecTurnos="+document.getElementById("selecTurnos").value
                        +"&nomegrupo="+document.getElementById("nomegrupo").value
                        +"&descgrupo="+document.getElementById("descgrupo").value
                        , true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.");
                                    }else{
                                        $("#relacao").load("modulos/escala/jGrupos.php");
                                        document.getElementById("relacEditaGrupos").style.display = "none";
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("relacEditaGrupos").style.display = "none";
                }
            }
            function modif(){ // assinala se houve qualquer modificação nos campos do modal
                document.getElementById("mudou").value = "1";
            }
            function carregaHelp(){
                document.getElementById("relacHelpEsc").style.display = "block";
            }
            function fechaHelp(){
                document.getElementById("relacHelpEsc").style.display = "none";
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
//pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escalas");
pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escalas (
    id SERIAL PRIMARY KEY, 
    grupo_id integer NOT NULL DEFAULT 0,
    dataescala date DEFAULT '3000-12-31',
    turno1_id BIGINT NOT NULL DEFAULT 0,
    horaini1 smallint NOT NULL DEFAULT 0, 
    horafim1 smallint NOT NULL DEFAULT 0,
    turno2_id BIGINT NOT NULL DEFAULT 0,
    horaini2 smallint NOT NULL DEFAULT 0, 
    horafim2 smallint NOT NULL DEFAULT 0,
    turno3_id BIGINT NOT NULL DEFAULT 0,
    horaini3 smallint NOT NULL DEFAULT 0, 
    horafim3 smallint NOT NULL DEFAULT 0,
    turno4_id BIGINT NOT NULL DEFAULT 0,
    horaini4 smallint NOT NULL DEFAULT 0, 
    horafim4 smallint NOT NULL DEFAULT 0,
    turno5_id BIGINT NOT NULL DEFAULT 0,
    horaini5 smallint NOT NULL DEFAULT 0, 
    horafim5 smallint NOT NULL DEFAULT 0,
    turno6_id BIGINT NOT NULL DEFAULT 0,
    horaini6 smallint NOT NULL DEFAULT 0, 
    horafim6 smallint NOT NULL DEFAULT 0,
    ativo smallint NOT NULL DEFAULT 1, 
    usuins bigint NOT NULL DEFAULT 0,
    datains timestamp without time zone DEFAULT '3000-12-31',
    usuedit bigint NOT NULL DEFAULT 0,
    dataedit timestamp without time zone DEFAULT '3000-12-31' 
    ) 
 ");

pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escala_ptc");
//pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escala_ptc (
//    id SERIAL PRIMARY KEY, 
//    poslog_id BIGINT NOT NULL DEFAULT 0,
//    trigr VARCHAR(4), 
//    tempomensal VARCHAR(100), 
//    tempototal VARCHAR(100),
//    ativo smallint DEFAULT 1 NOT NULL, 
//    usuins integer DEFAULT 0 NOT NULL,
//    datains timestamp without time zone DEFAULT '3000-12-31',
//    usuedit integer DEFAULT 0 NOT NULL,
//    dataedit timestamp without time zone DEFAULT '3000-12-31'
//    ) 
//");

//pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".calcEstat");
//pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escalas_calc (
//    id SERIAL PRIMARY KEY, 
//    codigo_id BIGINT NOT NULL DEFAULT 0,
//    horaini smallint NOT NULL DEFAULT 0, 
//    horafim smallint NOT NULL DEFAULT 0
//    )
//");

//pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escalas_gr");
pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escalas_gr (
    id SERIAL PRIMARY KEY, 
    siglagrupo VARCHAR(20),
    descgrupo VARCHAR(100),
    descescala VARCHAR(200),
    guardaescala VARCHAR(20),
    qtd_turno smallint NOT NULL DEFAULT 1,
    ativo smallint NOT NULL DEFAULT 1, 
    usuins bigint NOT NULL DEFAULT 0,
    datains timestamp without time zone DEFAULT '3000-12-31',
    usuedit bigint NOT NULL DEFAULT 0,
    dataedit timestamp without time zone DEFAULT '3000-12-31' 
    ) 
 ");
 $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".escalas_gr LIMIT 3 ");
 $row0 = pg_num_rows($rs0);
 if($row0 == 0){
     pg_query($Conec, "INSERT INTO ".$xProj.".escalas_gr (id, siglagrupo, descgrupo, descescala, guardaescala) VALUES (1, 'DAF', 'Diretoria Administrativa', 'Escala de Permanência', '07/2024')");
     pg_query($Conec, "INSERT INTO ".$xProj.".escalas_gr (id, siglagrupo, descgrupo, descescala, guardaescala) VALUES (2, 'DEF', 'Assessoria das Cucuias', 'Escala de Permanência', '08/2024')");
 }


 //-----------


        $rs = pg_query($Conec, "SELECT column_name, data_type, character_maximum_length FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'escalas'");
        $row = pg_num_rows($rs);
        if($row == 0){
            $Erro = 1;
            echo "Faltam as tabelas das escalas. Informe à ATI.";
            return false;
        }

        $OpcoesGrupo = pg_query($Conec, "SELECT id, siglagrupo FROM ".$xProj.".escalas_gr ORDER BY siglagrupo");
        $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataescala, 'MM'), '/', TO_CHAR(dataescala, 'YYYY')) 
        FROM ".$xProj.".escalas GROUP BY TO_CHAR(dataescala, 'MM'), TO_CHAR(dataescala, 'YYYY') ORDER BY TO_CHAR(dataescala, 'YYYY'), TO_CHAR(dataescala, 'MM') DESC ");
        ?>
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="mudou" value = "0" />
        <input type="hidden" id="guardacodgrupo" value = "0" />

        <div style="margin: 20px; border: 2px solid blue; border-radius: 15px; padding: 20px; min-height: 200px;">

        <!-- div três colunas -->
        <div class="container" style="margin: 0 auto;">
        <div class='bEdit corFundo' onclick='abreGrupos()'>Editar Grupos</div>

            <div class="row" style="text-align: center;">
                <div class="col" style="text-align: center; margin: 5px; width: 95%; padding: 2px; padding-top: 5px;">
                    <label>Selecione o grupo: </label>
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
                </div>
                <div class="col" style="text-align: center;"><h2>Escalas</h2></div> <!-- Central - espaçamento entre colunas  -->
                <div class="col" style="text-align: right; margin: 5px; width: 95%; padding: 2px;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelp();" title="Guia rápido"></div>
            </div>
            <br>
        </div>

        <!-- div modal mostra Grupos-->
        <div id="relacGrupos" class="relacmodal">
            <div class="modal-content-relacGrupos">
                <span class="close" onclick="fechaModalGrupos();">&times;</span>
                <label style="font-size: 1.2em; color: #666;">Grupos</label>
                <label style="color: #666; padding-left: 40px;"><sub>Clique para editar</sub></label>
                <div id="relacao" style="margin-top: 5px; border: 2px solid #C6E2FF; border-radius: 10px;"></div>
                <div style="padding: 20px;">
                <div class='bSalvar corFundo' onclick='inserirGrupo()'>Inserir Grupo</div>
                </div>
           </div>
           <br><br>
        </div> <!-- Fim Modal-->

        <!-- div modal edita grupos-->
        <div id="relacEditaGrupos" class="relacmodal">
            <div class="modal-content-editGrupos">
                <span class="close" onclick="fechaEditaGrupos();">&times;</span>
                <label style="font-size: 1.2em; color: #666;">Edita Grupo</label>
                <table style="margin: 0 auto; width: 95%;">
                    <tr>
                        <td class="etiq">Sigla</td>
                        <td><input type="text" id="siglagrupo" style="width: 50%;" onchange="modif();" placeholder="Sigla" onkeypress="if(event.keyCode===13){javascript:foco('nomegrupo');return false;}"/></td>
                        <td class="etiq">Turnos</td>
                        <td>
                            <select id="selecTurnos" style="font-size: 1rem; width: 60px; text-align: centr;" onchange="modif();" title="Selecione o número de turnos para a escala.">
                                <option value="0"></option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </td>    
                    </tr>
                    <tr>
                        <td class="etiq">Nome</td>
                        <td colspan="3"><input type="text" id="nomegrupo" style="width: 100%;" onchange="modif();" placeholder="Nome Grupo" onkeypress="if(event.keyCode===13){javascript:foco('descgrupo');return false;}"/></td>
                    </tr>
                    <tr>
                        <td class="etiq">Descrição</td>
                        <td colspan="3"><input type="text" id="descgrupo" style="width: 100%;" onchange="modif();" placeholder="Descrição" onkeypress="if(event.keyCode===13){javascript:foco('botsalvar');return false;}"/></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td style="text-align: center; padding-top: 20px;"><div class='bSalvar corFundo' onclick='salvaGrupo()'>Salvar</div></td>
                    </tr>

                </table>
                <div style="padding 20px; text-align: center;"></div>
           </div>
           <br><br>
        </div> <!-- Fim Modal-->

        <!-- div modal para leitura instruções -->
        <div id="relacHelpEsc" class="relacmodal">
            <div class="modalMsg-content">
                <span class="close" onclick="fechaHelp();">&times;</span>
                <h4 style="text-align: center; color: #666;">Informações</h4>
                <h5 style="text-align: center; color: #666;">Montagem das Escalas dos Grupos</h5>
                <div style="border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px;">
                    Regras inseridas:
                    <ul>
                        <li>1 - A Escala pode conter de um a quatro turnos.</li>
                        <li>2 - O número de turnos de cada grupo é escolhido ao inserir ou editar o grupo e pode ser modificado a qualquer momento.</li>
                    </ul>
                </div>
            </div>
        </div>  <!-- Fim Modal Help-->
    </body>
</html>