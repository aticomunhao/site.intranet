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
                min-width: 33px;
                border: 1px solid;
                border-radius: 3px;
                padding-left: 5px;
                padding-right: 5px;
                cursor: pointer;
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
                $("#escala").load("modulos/escala/jEscala.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                $("#estat").load("modulos/escala/jEstat.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                $("#selecMesAno").change(function(){
                    if(parseInt(document.getElementById("selecMesAno").value) > 0){
                        $("#escala").load("modulos/escala/jEscala.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                        $("#estat").load("modulos/escala/jEstat.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                    }
                });

            }); // fim do ready


            function abreParticip(Turno, Cod, CodPartic, Data){
                document.getElementById("guardaTurno").value = Turno;
                document.getElementById("guardaCod").value = Cod;
                document.getElementById("selecNomeParticip").value = CodPartic;
                document.getElementById("titulomodal").innerHTML = Data;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escala/salvaEsc.php?acao=buscaDados&codigo="+Cod+"&turno="+Turno, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("selecNomeParticip").value = Resp.codigo;
                                    if(parseInt(Resp.horaini) === 0){
                                        document.getElementById("selecHoraIni").value = 8;
                                    }else{
                                        document.getElementById("selecHoraIni").value = Resp.horaini;
                                    }
                                    if(parseInt(Resp.horafim) === 0){
                                        document.getElementById("selecHoraFim").value = 18;
                                    }else{
                                        document.getElementById("selecHoraFim").value = Resp.horafim;
                                    }
                                    document.getElementById("relacSelectParticip").style.display = "block";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function fechaModalPart(){
                document.getElementById("relacSelectParticip").style.display = "none";
            }

            function salvaEscalado(){
                if(document.getElementById("selecNomeParticip").value == ""){
                    return false;
                }
                if(document.getElementById("selecHoraIni").value == ""){
                    return false;
                }
                if(document.getElementById("selecHoraFim").value == ""){
                    return false;
                }
                if(parseInt(document.getElementById("selecHoraFim").value) <= parseInt(document.getElementById("selecHoraIni").value)){
                    document.getElementById("selecHoraFim").value = "";
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escala/salvaEsc.php?acao=salvaParticip&codigo="+document.getElementById("guardaCod").value
                    +"&turno="+document.getElementById("guardaTurno").value
                    +"&codparticip="+document.getElementById("selecNomeParticip").value
                    +"&horaini="+encodeURIComponent(document.getElementById("selecHoraIni").value)
                    +"&horafim="+encodeURIComponent(document.getElementById("selecHoraFim").value)
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("relacSelectParticip").style.display = "none";
                                    $("#escala").load("modulos/escala/jEscala.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                                    $("#estat").load("modulos/escala/jEstat.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function imprEscala(){
                window.open("modulos/escala/imprEsc.php?acao=imprEscala&numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), document.getElementById("selecMesAno").value);
            }
            function carregaHelpEscala(){
                document.getElementById("relacHelpEscala").style.display = "block";
            }
            function fechaHelpEscala(){
                document.getElementById("relacHelpEscala").style.display = "none";
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
        $NumGrupo = filter_input(INPUT_GET, 'numgrupo');

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

        $Ini = strtotime(date('Y/m/01')); // número - para começar com o dia 1
        $DiaIni = strtotime("-1 day", $Ini); // para começar com o dia 1 no loop for

        //Mantem a tabela meses à frente
        for($i = 0; $i < 180; $i++){
            $Amanha = strtotime("+1 day", $DiaIni);
            $DiaIni = $Amanha;
            $Data = date("Y/m/d", $Amanha); // data legível
            $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".escalas WHERE dataescala = '$Data' And grupo_id = $NumGrupo ");
            $row0 = pg_num_rows($rs0);
            if($row0 == 0){
                pg_query($Conec, "INSERT INTO ".$xProj.".escalas (dataescala, grupo_id, usuins, datains) VALUES ('$Data', $NumGrupo, ".$_SESSION["usuarioID"].", NOW())");
            }
        }

        $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataescala, 'MM'), '/', TO_CHAR(dataescala, 'YYYY')) 
        FROM ".$xProj.".escalas GROUP BY TO_CHAR(dataescala, 'MM'), TO_CHAR(dataescala, 'YYYY') ORDER BY TO_CHAR(dataescala, 'YYYY'), TO_CHAR(dataescala, 'MM') DESC ");
        $OpNomes = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl");

        ?>
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="mudou" value = "0" />
        <input type="hidden" id="guardanumgrupo" value = "<?php echo $NumGrupo; ?>" />
        <input type="hidden" id="guardames" value = "<?php echo $MesSalvo; ?>" />
        <input type="hidden" id="guardaCod" value = "" />
        <input type="hidden" id="guardaTurno" value = "" />

        <div style="margin: 20px; border: 2px solid blue; border-radius: 15px; padding: 5px; min-height: 200px;">
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
                    <button class="botpadrred" id="botimprEsc" style="font-size: 80%;" onclick="imprEscala();">Gerar PDF</button>
                    <label style="padding-left: 30px;"></label>
                    <img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpEscala();" title="Guia rápido">
                </div>
            </div>      
            <br>
        </div>

        <div id="escala"></div>
        <div id="estat"></div>

        <!-- div modal -->
        <div id="relacSelectParticip" class="relacmodal">
            <div class="modal-content-selecParticip">
                <span class="close" onclick="fechaModalPart();">&times;</span>
                <label style="font-size: 1.2em; color: #666;">Escalado para o dia: &nbsp; </label><label id="titulomodal" style="font-size: 1.2em; color: #666;"></label>
                <div style="border: 2px solid #C6E2FF; border-radius: 10px;">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td style="text-align: right;"><label style="font-size: 80%;">Selecione um nome: </label></td>
                            <td colspan="3">
                                <select id="selecNomeParticip" style="font-size: 1rem; width: 100%;" title="Selecione um nome.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpNomes){
                                        while ($Opcoes = pg_fetch_row($OpNomes)){
                                            ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><label style="font-size: 80%;">Início </label></td>
                            <td>
                                <select id="selecHoraIni" style="font-size: 1rem; width: 100%;" title="Selecione a hora de início.">
                                    <option value=""></option>
                                    <option value="0">00:00</option>
                                    <option value="1">01:00</option>
                                    <option value="2">02:00</option>
                                    <option value="3">03:00</option>
                                    <option value="4">04:00</option>
                                    <option value="5">05:00</option>
                                    <option value="6">06:00</option>
                                    <option value="7">07:00</option>
                                    <option value="8">08:00</option>
                                    <option value="9">09:00</option>
                                    <option value="10">10:00</option>
                                    <option value="11">11:00</option>
                                    <option value="12">12:00</option>
                                    <option value="13">13:00</option>
                                    <option value="14">14:00</option>
                                    <option value="15">15:00</option>
                                    <option value="16">16:00</option>
                                    <option value="17">17:00</option>
                                    <option value="18">18:00</option>
                                    <option value="19">19:00</option>
                                    <option value="20">20:00</option>
                                    <option value="21">21:00</option>
                                    <option value="22">22:00</option>
                                    <option value="23">23:00</option>
                                    <option value="24">24:00</option>
                                </select>
                            </td>
                            <td style="text-align: right;"><label style="font-size: 80%;">Término </label></td>
                            <td>
                                <select id="selecHoraFim" style="font-size: 1rem; width: 100%;" title="Selecione a hora de término.">
                                    <option value=""></option>
                                    <option value="0">00:00</option>
                                    <option value="1">01:00</option>
                                    <option value="2">02:00</option>
                                    <option value="3">03:00</option>
                                    <option value="4">04:00</option>
                                    <option value="5">05:00</option>
                                    <option value="6">06:00</option>
                                    <option value="7">07:00</option>
                                    <option value="8">08:00</option>
                                    <option value="9">09:00</option>
                                    <option value="10">10:00</option>
                                    <option value="11">11:00</option>
                                    <option value="12">12:00</option>
                                    <option value="13">13:00</option>
                                    <option value="14">14:00</option>
                                    <option value="15">15:00</option>
                                    <option value="16">16:00</option>
                                    <option value="17">17:00</option>
                                    <option value="18">18:00</option>
                                    <option value="19">19:00</option>
                                    <option value="20">20:00</option>
                                    <option value="21">21:00</option>
                                    <option value="22">22:00</option>
                                    <option value="23">23:00</option>
                                    <option value="24">24:00</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="3" style="text-align: center;"><button class="botpadrblue" onclick="salvaEscalado();">Salvar</button></td>
                        </tr>
                    </table>
                </div>
                <div style="padding-bottom: 20px;"></div>
           </div>
           <br><br>
        </div> <!-- Fim Modal-->

        <!-- div modal para leitura instruções -->
        <div id="relacHelpEscala" class="relacmodal">
            <div class="modalMsg-content-Escala">
                <span class="close" onclick="fechaHelpEscala();">&times;</span>
                <h4 style="text-align: center; color: #666;">Informações</h4>
                <h5 style="text-align: center; color: #666;">Montagem das Escalas dos Grupos</h5>
                <div style="border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px;">
                    Regras inseridas:
                    <ul>
                        <li>1 - A Escala pode conter de um a quatro turnos.</li>
                        <li>2 - O número de turnos de cada grupo é escolhido na página anterior ao inserir ou editar o grupo e pode ser modificado a qualquer momento.</li>
                        <li>3 - Quando houver mais de um turno, preencha primeiro os turnos mais à esquerda, mantendo a sequência nos horários.</li>
                    </ul>
                </div>
            </div>
        </div>  <!-- Fim Modal Help-->
    </body>
</html>