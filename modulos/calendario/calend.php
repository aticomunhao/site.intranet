<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    header("Location: /cesb/index.php");
    //setInterval("checaCalend()", 3600000) está no indexb.php
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/bootstrap/css/bootstrap.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-ui.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="class/bootstrap/js/bootstrap.min.js"></script>
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="comp/js/jquery-ui.min.js"></script>
        <script src="comp/js/jquery.mask.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script>   <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <style>
            .modalCalend{
                display: none; /* oculto default */
                position: fixed;
                z-index: 200;
                left: 0;
                top: 0;
                width: 100%; /* largura total */
                height: 100%; /* altura total */
                overflow: auto; /* autoriza scroll se necessário */
                background-color: rgb(0,0,0); /* Fallback color */
                background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            }
            .modalCalend-content{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 40%; /* acertar de acordo com a tela */
            }
            .modalMsg-content-Calend{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 40%; /* acertar de acordo com a tela */
            }
            /* Botão fechar */
            .close{
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
                text-align: right;
            }
            .close:hover,
            .close:focus {
                color: black;
                text-decoration: none;
                cursor: pointer;
            }
            header {
                display: flex;
                justify-content: space-around;
                align-items: center;
                height: 50px;
            }
            th {                
                width: 1%;
            }
            td.other-month {
                opacity: .5;
                text-align: center;
            }
            tr td {
                border: 1px solid #c0c0c0;
            }
            .etiq{
                text-align: right; font-size: 70%; font-weight: bold; padding-right: 1px; padding-bottom: 1px;
            }

        </style>
        <script>
            $(document).ready(function(){
                $("#releventos").load("modulos/calendario/listaCalend.php?monthTime="+document.getElementById("monthTime").value+"&dataInicial="+document.getElementById("dataInicial").value);
                $("#calendario").load("modulos/calendario/relCalend.php?monthTime="+document.getElementById("monthTime").value+"&dataInicial="+document.getElementById("dataInicial").value);

                $("#dataini").change(function(){
                    document.getElementById("datafim").value = document.getElementById("dataini").value;
                });
                
                $('#dataini').datepicker();
                $('#datafim').datepicker();
                $("#dataini").mask("99/99/9999");
                $("#datafim").mask("99/99/9999");

                //Fecha caixa ao clicar na página
                modalCalend = document.getElementById('relacmodalCalend'); //span[0]
                helpCalend = document.getElementById('relacHelpCalend'); //span[1]
                spanCalend = document.getElementsByClassName("close")[0];
                spanHelp = document.getElementsByClassName("close")[1];

                window.onclick = function(event){
                    if(event.target === modalCalend){
                        modalCalend.style.display = "none";
                    }
                    if(event.target === helpCalend){
                        helpCalend.style.display = "none";
                    }                    
                };

            });
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
            function carregaMes(Sent){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/calendario/salvaCalend.php?acao=busca&sentido="+Sent+"&monthTime="+document.getElementById("monthTime").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                document.getElementById("numMes").value = Resp.numMes;
                                $("#calendario").load("modulos/calendario/relCalend.php?monthTime="+Resp.monthTime+"&dataInicial="+document.getElementById("dataInicial").value);
                                document.getElementById("monthTime").value = Resp.monthTime;
                                document.getElementById("mesAno").innerHTML = Resp.mesTrad;
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function pegaData(Param){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/calendario/salvaCalend.php?acao=buscadata&dataDia="+Param, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                document.getElementById("guardaDiaClick").value = Resp.diaClick; // está só pegando a data do quadrinho 
                                if(parseInt(Resp.insEv) === 1){ // checou o nível adm do usuário em salvaCalemd.php
                                    document.getElementById("guardaNumEv").value = 0; // evento novo
                                    document.getElementById("dataini").value = Resp.diaClick;
                                    document.getElementById("datafim").value = Resp.diaClick; // para não ficar com a data anterior
                                    document.getElementById("textoev").value = "";
                                    document.getElementById("localev").value = "";
                                    document.getElementById("guardaCor").value = "#FFFFFF";
                                    document.getElementById("relacmodalCalend").style.display = "block";
                                    document.getElementById("apagar").disabled = true;
                                    document.getElementById("evfixo").checked = false;
                                    document.getElementById("avisoObrig").checked = false;
                                    if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                                        document.getElementById("etiqevfixo").style.visibility = "visible";
                                        document.getElementById("evfixo").style.visibility = "visible";
                                        document.getElementById("apagar").disabled = false;

                                    }else{
                                        document.getElementById("etiqevfixo").style.visibility = "hidden";
                                        document.getElementById("evfixo").style.visibility = "hidden";
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function fechaModalCalend(){
                 document.getElementById("relacmodalCalend").style.display = "none";
            }

            function salvaEv(){
                if(document.getElementById("textoev").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha a <u>descrição do evento</u>";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("dataini").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha a <u>data</u> do evento";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("evfixo").checked === true){
                    Fixo = 1;
                }else{
                    Fixo = 0;
                }
                if(document.getElementById("avisoObrig").checked === true){
                    Obrig = 1;
                }else{
                    Obrig = 0;
                }
                if(parseInt(document.getElementById("mudou").value) === 1){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/calendario/salvaCalend.php?acao=salvaev&dataini="+document.getElementById("dataini").value
                        +"&numEv="+document.getElementById("guardaNumEv").value
                        +"&datafim="+document.getElementById("datafim").value
                        +"&textoev="+encodeURIComponent(document.getElementById("textoev").value)
                        +"&localev="+encodeURIComponent(document.getElementById("localev").value)
                        +"&repet="+document.getElementById("guardaRepet").value
                        +"&fixo="+Fixo
                        +"&avobrig="+Obrig
                        +"&corevento="+encodeURIComponent(document.getElementById("guardaCor").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve um erro no servidor.");
                                    }else{
                                    }
                                    document.getElementById("relacmodalCalend").style.display = "none";
                                    $("#releventos").load("modulos/calendario/listaCalend.php?monthTime="+document.getElementById("monthTime").value+"&dataInicial="+document.getElementById("dataInicial").value);
                                    $("#calendario").load("modulos/calendario/relCalend.php?monthTime="+document.getElementById("monthTime").value+"&dataInicial="+document.getElementById("dataInicial").value);
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("mudou").value = "0";
                    document.getElementById("relacmodalCalend").style.display = "none";
                }
            }
            function salvaCor(Cor){
                document.getElementById("guardaCor").value = Cor;
                document.getElementById("mudou").value = "1";
            }
            function salvaRepet(Valor){
                document.getElementById("guardaRepet").value = Valor;
                document.getElementById("mudou").value = "1";
            }
            function modif(){ // assinala se houve qualquer modificação nos campos do modal durante a edição para evitar salvar desnecessariamente
                document.getElementById("mudou").value = "1";
            }

            function pegaEvento(Cod, EvNum){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/calendario/salvaCalend.php?acao=buscaevento&codigo="+Cod+"&evNum="+EvNum, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")"); 
                                if(parseInt(Resp.editEv) === 1){ // nível adm do usuário checado em salvaCalend.php
                                    document.getElementById("guardaNumEv").value = Resp.evNum;
                                    document.getElementById("dataini").value = Resp.dataIni;
                                    document.getElementById("datafim").value = Resp.dataFim;
                                    document.getElementById("textoev").value = Resp.titulo;
                                    document.getElementById("localev").value = Resp.localEv;
                                    document.getElementById("guardaCor").value = Resp.cor;
                                    if(Resp.cor === "#FFFFFF"){
                                        document.getElementById("corevento0").checked = true;
                                    }
                                    if(Resp.cor === "#00FFFF"){
                                        document.getElementById("corevento1").checked = true;
                                    }
                                    if(Resp.cor === "#00FF00"){
                                        document.getElementById("corevento2").checked = true;
                                    }
                                    if(Resp.cor === "#FF7F50"){
                                        document.getElementById("corevento3").checked = true;
                                    }
                                    if(Resp.cor === "#FFFF99"){
                                        document.getElementById("corevento4").checked = true;
                                    }

                                    if(parseInt(Resp.AvObrig) === 1){
                                        document.getElementById("avisoObrig").checked = true;
                                    }else{
                                        document.getElementById("avisoObrig").checked = false;
                                    }

                                    if(parseInt(Resp.Fixo) == 1){
                                        document.getElementById("apagar").style.visibility = "hidden";
                                    }else{
                                        document.getElementById("apagar").style.visibility = "visible";
                                    }
                                    if(parseInt(document.getElementById("UsuAdm").value) < parseInt(Resp.editEv)){
                                        document.getElementById("apagar").style.visibility = "hidden";
                                    }else{
                                        document.getElementById("apagar").style.visibility = "visible";
                                    }

                                    if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                                        document.getElementById("etiqevfixo").style.visibility = "visible";
                                        document.getElementById("evfixo").style.visibility = "visible";
                                        document.getElementById("apagar").disabled = false;
                                        if(parseInt(Resp.Fixo) == 1){
                                            document.getElementById("evfixo").checked = true;
                                        }else{
                                            document.getElementById("evfixo").checked = false;
                                        }
                                    }else{
                                        document.getElementById("etiqevfixo").style.visibility = "hidden";
                                        document.getElementById("evfixo").style.visibility = "hidden";
                                    }
                                    document.getElementById("repet"+Resp.Repet).checked = true; // estrutura repetição 0 = sem, 1 = repet mensal, 2 = repet anual
                                    document.getElementById("mudou").value = "0";
                                    
                                    document.getElementById("relacmodalCalend").style.display = "block";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function apagaEv(){
                $.confirm({
                    title: 'Apagar evento.',
                    content: 'Confirma apagar este lançamento?',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/calendario/salvaCalend.php?acao=apagaev&numEv="+document.getElementById("guardaNumEv").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                            if(parseInt(Resp.coderro) > 0){
                                                alert("Houve um erro no servidor.");
                                            }else{
                                            }
                                            document.getElementById("relacmodalCalend").style.display = "none";
                                            $("#releventos").load("modulos/calendario/listaCalend.php?monthTime="+document.getElementById("monthTime").value+"&dataInicial="+document.getElementById("dataInicial").value);
                                            $("#calendario").load("modulos/calendario/relCalend.php?monthTime="+document.getElementById("monthTime").value+"&dataInicial="+document.getElementById("dataInicial").value);
                                        }
                                    }
                                };
                                ajax.send(null);
                            }
                        },
                        Não: function () {
                        }
                    }
                });
            }
            function AvisosCalend(Obj){
                Valor = 0;
                if(Obj.checked === true){
                    Valor = 1;
                }
                document.getElementById("avisoSusp").innerHTML = ""; // avisa que está marcado mas foi solicitado não apresentar mais avisos
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/calendario/salvaCalend.php?acao=semAviso&param="+Valor, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
//                                    Erro
                                }
                            }
                        }
                    };
                    ajax.send(null);
                } 
            }
            function carregaHelpCalend(){
                document.getElementById("relacHelpCalend").style.display = "block";
            }
            function fechaModalHelp(){
                document.getElementById("relacHelpCalend").style.display = "none";
            }
         /* Brazilian initialisation for the jQuery UI date picker plugin. */
         /* Written by Leonildo Costa Silva (leocsilva@gmail.com). */
         jQuery(function($){
            $.datepicker.regional['pt-BR'] = {
                closeText: 'Fechar',
                prevText: '< Anterior',
                nextText: 'Próximo >',
                currentText: 'Hoje',
                monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado'],
                dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
                dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            $.datepicker.setDefaults($.datepicker.regional['pt-BR']);
         });
        </script>
    </head>
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $avCalend = 0;
            $AvisosSupensos = 0;
            $Hoje = date('Y/m/d');
            pg_query($Conec, "UPDATE ".$xProj.".poslog SET avhoje = (CURRENT_DATE -1) WHERE avhoje IS NULL ");

            $rs1 = pg_query($Conec, "SELECT avcalend, avhoje FROM ".$xProj.".poslog WHERE pessoas_id = ".$_SESSION["usuarioID"]." ");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                $tbl1 = pg_fetch_row($rs1);
                $avCalend = $tbl1[0]; // emitir aviso = 1
                if(!is_null($tbl1[1])){
                    $Data = $tbl1[1];
                }else{
                    $Data = "";
                }
                
                if(strtotime($Data) == strtotime($Hoje)){ // quando não quer mais ver avisos só por hoje
                    $AvisosSupensos = 1;
                }
            }
        ?>
        

<!-- Divisão da tela - lista à esquerda e calendário à direita   -->

        <!-- lista -->
        <div style="position: relative; float: left; width: 25%; min-height: 500px;">
            <div style="text-align: center;">
                <div style="position: absolute; left: 10px; top: 8px;">
                    <input type="checkbox" id="admCad" onclick="AvisosCalend(this);" <?php if($avCalend == 1) {echo "checked";} ?>>
                    <label for="admCad" class="etiq">Emitir Avisos <label id='avisoSusp'> <?php if($AvisosSupensos == 1){ echo " (suspensos)";} ?></label></label>
                    <img src="imagens/iinfo.png" height="20px;" style="cursor: pointer; padding-left: 10px;" onclick="carregaHelpCalend();" title="Guia rápido">
                </div>
                <h2>Eventos</h2>
                <div id="releventos" style="min-height: 650px; text-align: left; padding: 5px; border: 2px solid blue; border-radius: 15px;"></div>
            </div>
        </div>

        <!-- calendário -->
        <div style="position: relative; float: right; margin-right: 2%; width: 71%; min-height: 500px;">
            <div>
                <?php
//              setlocale(LC_ALL, "pt-BR");
                date_default_timezone_set('America/Sao_Paulo'); //Um dia = 86.400 seg
                require_once("functions.php");
//              $formatter = new IntlDateFormatter('pt_BR', IntlDateFormatter::FULL, IntlDateFormatter::NONE);  //FULL no primeiro parâmetro dá o dia da semana também
//              echo $formatter->format(time());  // não funcionou na comunhão
                $monthTime = getMonthTime();
                echo "<header>";
                    echo "<div style='border: 1px solid; border-radius: 7px; background-color: blue; color: white; font-weight: bold; padding-left: 15px; padding-right: 15px; cursor: pointer;' onclick='carregaMes(1);'>Anterior</div>";
                    $mesAno = date("F Y", $monthTime);
                    $Ingl = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
                    $Port = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
                    $Trad = str_replace($Ingl, $Port, $mesAno);
                    echo "<h2 id='mesAno'>".$Trad."</h2>";
                    echo "<div style='border: 1px solid; border-radius: 7px; background-color: blue; color: white; font-weight: bold; padding-left: 15px; padding-right: 15px; cursor: pointer;' onclick='carregaMes(2);'>Próximo</div>";
                echo "</header>";
                $startDate = strtotime("last sunday", $monthTime);
                ?>
            </div>

            <div id="calendario" style="padding: 15px; min-height: 500px; border: 2px solid blue; border-radius: 15px;"></div>

        </div>

        <input type="hidden" id="monthTime" value="<?php echo $monthTime; ?>" />
        <input type="hidden" id="dataInicial" value="<?php echo $startDate; ?>" />
        <input type="hidden" id="guardaDiaClick" value="0" />
        <input type="hidden" id="guardaNumEv" value="0" />
        <input type="hidden" id="mudou" value="0" /> <!-- valor 1 quando houver mudança em qualquer campo do modal -->
        <input type="hidden" id="guardaRepet" value="0" />
        <input type="hidden" id="guardaCor" value="#FFFFFF" /> <!-- cor branca default -->
        <input type="hidden" id="numMes" value="<?php echo date("m", $monthTime); ?>" />
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>" />

        <!-- div modal para inserção de eventos no calendário -->
        <div id="relacmodalCalend" class="modalCalend">
            <div class="modalCalend-content">
                <span class="close" onclick="fechaModalCalend();">&times;</span>
                <h4 id="titulomodalCalend" style="text-align: center; color: #666;">Inserção de Eventos no Calendário</h4>
                <div style="border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px; text-align: left;">
                    <div>
                        <label class="etiq" style="padding: 5px;">Data do Início: </label>
                        <input type="text" id="dataini" value="" onchange="modif();" placeholder="Data início" style="font-size: .9em; width: 100px; text-align: center;">
                        <label class="etiq" style="padding: 5px;">Data do Término: </label>
                        <input type="text" id="datafim" value="" onchange="modif();" placeholder="Data término" style="font-size: .9em; width: 100px; text-align: center;">
                    </div>
                    <hr style="margin: 0; padding: 2px;">
                    <div style="text-align: left;">
                        <label class="etiq" style="padding-right: 25px;">Evento: </label>
                        <!-- Para superusuários - evento fixo tipo natal -->
                        <input type="checkbox" id="evfixo" value="0" onchange="modif();" title="Evento fixo é exclusivo para superusuários">
                        <label for="evfixo" id="etiqevfixo" class="etiq" title="Evento fixo é exclusivo para superusuários">Evento fixo</label>

                        <label class="etiq" style="padding-left 30px;"></label>
                        <input type="checkbox" id="avisoObrig" value="0" onchange="modif();" title="Aviso será emitido independente das configurações">
                        <label for="avisoObrig" id="etiqavisoObrig" class="etiq" title="Aviso será emitido independente das configurações">Aviso Obrigatório</label>


                    </div>
                    <div style="text-align: center;">
                        <input type="text" id="textoev" value="" placeholder="Descrição do evento" style="font-size: .9em; width: 98%;" onchange='modif();'>
                    </div>
                    <hr style="margin: 2px; padding: 2px;">

                    <div style="text-align: left;">
                        <label class="etiq" style="padding: 5px; padding-right: 30px;">Local: </label>
                        <div style="position: relative; float: right; border: 1px solid; border-radius: 5px; margin-right: 10px; padding: 0 5px 0 5px;">
                            <input type="radio" name="repet" id="repet0" value="0" CHECKED title="Sem repetição" onclick="salvaRepet(0);"><label for="repet0" class="etiq" style="padding-left: 2px;">Sem repetição</label>
                            <label style="padding-right: 10px;"></label>
                            <input type="radio" name="repet" id="repet1" value="1" title="Repetição Mensal" onclick="salvaRepet(1);"><label for="repet1" class="etiq" style="padding-left: 2px;">Repetição Mensal</label>
                            <label style="padding-right: 10px;"></label>
                            <input type="radio" name="repet" id="repet2" value="2" title="Repetição Anual" onclick="salvaRepet(2);"><label for="repet2" class="etiq" style="padding-left: 2px;">Repetição Anual</label>
                        </div>
                    </div>

                    <div style="text-align: center;">
                        <textarea id="localev" rows="3" value="" placeholder="Local do evento" style="font-size: .9em; width: 98%;" onchange='modif();'></textarea>
                    </div>
                    <hr style="margin: 2px; padding: 2px;">

                    <div class="etiq" style="text-align: center;">Cor: 
                        <input type="radio" name="corevento" id="corevento0" value="1" CHECKED title="branco" onclick="salvaCor('#FFFFFF');"><label for="corevento0"><div style="width: 45px; height: 15px; border-radius: 5px; background: #FFFFFF;"></div></label>
                        <label style="padding-left: 20px;"></label>
                        <input type="radio" name="corevento" id="corevento1" value="1" title="azul" onclick="salvaCor('#00FFFF');"><label for="corevento1"><div style="width: 45px; height: 15px; border-radius: 5px; background: #00FFFF;"></div></label>
                        <label style="padding-left: 20px;"></label>
                        <input type="radio" name="corevento" id="corevento2" value="2" title="verde" onclick="salvaCor('#00FF00');"><label for="corevento2"><div style="width: 45px; height: 15px; border-radius: 5px; background: #00FF00;"></div></label>
                        <label style="padding-left: 20px;"></label>
                        <input type="radio" name="corevento" id="corevento3" value="3" title="vermelho" onclick="salvaCor('#FF7F50');"><label for="corevento3"><div style="width: 45px; height: 15px; border-radius: 5px; background: #FF7F50;"></div></label>
                        <label style="padding-left: 20px;"></label>
                        <input type="radio" name="corevento" id="corevento4" value="4" title="amarelo" onclick="salvaCor('#FFFF99');"><label for="corevento4"><div style="width: 45px; height: 15px; border-radius: 5px; background: #FFFF99;"></div></label>
                    </div>
                    <hr style="margin: 2px; padding: 2px;">
                    <div id="mensagem" style="text-align: center; color: red; font-weight: bold;"></div>

                    <div style="margin: 7px;">
                        <table style="margin: 0 auto; width: 90%;">
                            <tr>
                                <td class="etiq" style="text-align: left;">
                                    <input type="button" class="resetbotred" id="apagar" value="Apagar" onclick="apagaEv();">
                                </td>
                                <td style="text-align: right;">
                                    <input type="button" class="resetbotazul" id="salvar" value="Salvar" onclick="salvaEv();">
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>  <!-- Fim Modal Mensagens-->

        <!-- div modal para leitura instruções -->
        <div id="relacHelpCalend" class="modalCalend">
            <div class="modalMsg-content-Calend">
                <span class="close" onclick="fechaModalHelp();">&times;</span>
                <h3 style="text-align: center; color: #666;">Informações</h3>
                <h4 style="text-align: center; color: #666;">Calendário</h4>
                <div style="border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px;">
                    Regras inseridas:
                    <ul>
                        <li>1 - O calendário emite avisos dos eventos para os usuários com a opção Emitir Avisos ligada.</li>
                        <li>2 - Os avisos se repetem em intervalos de UMA HORA durante o dia do evento.</li>
                        <li>3 - Os avisos podem ser dispensados para o dia em curso. Esta opção está no próprio aviso e é uma opção individual do usuário.</li>
                        <li>4 - Os avisos podem ser dispensados definitivamente, desligando a opção Emitir Avisos. Esta é uma opção individual do usuário.</li>
                        <li>5 - Existe uma opção, no ato de inserir um evento no calendário, que torna aquele evento de aviso obrigatório.</li>
                        <li>6 - Os eventos de aviso obrigatório aparecem naquele dia mesmo para os usuários com avisos desligados.</li>
                    </ul>
                </div>
            </div>
        </div>  <!-- Fim Modal Help-->
    </body>
</html>