<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/indlog.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script> <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="comp/js/jquery.mask.js"></script>
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
                $("#carregaReg").load("modulos/lro/relReg.php");
                $("#dataocor").mask("99/99/9999");
                document.getElementById("dataocor").disabled = true; // não deixar mudar a data do registro no LRO

                var nHora = new Date();   //   function mostraRelogio()  //em relacao.js da SCer
                var hora = nHora.getHours();
                var minuto = nHora.getMinutes();
                if(hora >= 0){
                    document.getElementById("selecturno").value = "3";
                }
                if(hora >= 7){
                    document.getElementById("selecturno").value = "1";
                }
                if(hora > 13){
                    document.getElementById("selecturno").value = "2";
                }
                if(hora > 19){
                    document.getElementById("selecturno").value = "3";
                }

                modalMostra = document.getElementById('relacMostramodalReg'); //span[0]
                spanMostra = document.getElementsByClassName("close")[0];
                window.onclick = function(event){
                    if(event.target === modalMostra){
                        modalMostra.style.display = "none";
                    }
                };

            });
            
            function InsRegistro(){ // inserir novo registro 
                document.getElementById("jatem").value = "0";
                document.getElementById("numrelato").value = "";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=buscaAcessoLro&geradata="+document.getElementById("guardahoje").value+"&geraturno="+document.getElementById("selecturno").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    if(parseInt(Resp.acessoLro) === 1){
                                        document.getElementById("guardacod").value = 0;
                                        document.getElementById("mudou").value = "1";
                                        document.getElementById("dataocor").value = document.getElementById("guardahoje").value;
                                        document.getElementById("nomeusuario").innerHMTL = "";
                                        document.getElementById("selectusuant").value = "";
                                        document.getElementById("relato").value = "";
                                        document.getElementById("relacmodalReg").style.display = "block";

                                        if(parseInt(Resp.jatem) > 0){
                                            document.getElementById("jatem").value = "1";
                                            document.getElementById("numrelato").value = Resp.numrelato;
                                            $.confirm({
                                                title: "Atenção!",
                                                content: "Este turno "+document.getElementById("dataocor").value+" - "+Resp.descturno+"  já foi lançado.",
                                                draggable: true,
                                                buttons: {
                                                    OK: function(){}
                                                }
                                            });
                                            document.getElementById("selecturno").value = "";
                                        }
                                    }else{
                                        $.confirm({
                                            title: 'Informação!',
                                            content: 'Usuário não cadastrado para acesso ao LRO. <br>O acesso é proporcionado pela ATI.',
                                            autoClose: 'OK|7000',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function mostraModal(Cod){ // só mostra após o clique, para editar chama carregaModal()
                document.getElementById("mostramensagem").innerHTML = "";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=buscaReg&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    if(parseInt(Resp.acessoLro) === 1){
                                        document.getElementById("guardacod").value = Cod;
                                        document.getElementById("mostradataocor").value = Resp.data;
                                        document.getElementById("mostraselecturno").value = Resp.descturno;
                                        document.getElementById("mostranomeusuario").innerHTML = Resp.nomeusuins;
                                        document.getElementById("mostraselectusuant").value = Resp.nomeusuant;
                                        document.getElementById("mostrarelato").value = Resp.relato;
                                        document.getElementById("numrelato").value = Resp.numrelato;
                                        document.getElementById("guardausuins").value = Resp.codusuins; // usuário que inseriu o relato
                                        document.getElementById("guardaenviado").value = Resp.enviado; // encerrou o relato - não edita mais
                                        if(parseInt(Resp.enviado) === 1){
                                            document.getElementById("mostramensagem").innerHTML = "Registro Enviado.";
                                        }
                                        document.getElementById("relacMostramodalReg").style.display = "block";
                                    }
                                    document.getElementById("botedit").style.visibility = "hidden"; // botão de editar
                                    document.getElementById("botimpr").style.visibility = "hidden"; // botão de imprimir
                                    document.getElementById("mostrabotimpr").style.visibility = "hidden"; // botão de imprimir na visualização
                                    if(parseInt(Resp.enviado) === 0 && parseInt(Resp.codusuins) === parseInt(document.getElementById("guardaUsuId").value)){ // ainda não fechou e foi o usu logado que inseriu
                                        document.getElementById("botedit").style.visibility = "visible"; // botão de editar
                                    }
                                    if(parseInt(document.getElementById("EditIndiv").value) > 0){ // se houver alguém designado para fazer a conferência
                                        if(parseInt(document.getElementById("EditIndiv").value) === parseInt(document.getElementById("guardaUsuId").value)){ //checa se é o designado
                                            document.getElementById("mostrabotimpr").style.visibility = "visible"; // botão de imprimir na visualização
                                        }else{
                                            document.getElementById("mostrabotimpr").style.visibility = "hidden"; // botão de imprimir na visualização
                                        }
                                    }else{
                                        if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admEdit").value)){
                                            document.getElementById("mostrabotimpr").style.visibility = "visible";
                                        }
                                    }
                                    if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                                        document.getElementById("mostrabotimpr").style.visibility = "visible"; // botão de imprimir na visualização
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function carregaModal(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=buscaReg&codigo="+document.getElementById("guardacod").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("dataocor").value = Resp.data;
                                    document.getElementById("selecturno").value = Resp.turno;
                                    document.getElementById("selectusuant").value = Resp.usuant;
                                    document.getElementById("relato").value = Resp.relato;
                                    document.getElementById("relacMostramodalReg").style.display = "none";
                                    document.getElementById("relacmodalReg").style.display = "block";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function tiraBorda(id){
                let element = document.getElementById(id);
                element.classList.remove('destacaBorda');
            }

            function enviaModalReg(Envia){
                if(parseInt(Envia) === 1){
                    $.confirm({
                        title: 'Confirmação!',
                        content: "O relato será enviado ao setor competente e não poderá ser modificado.<br> Se quiser apenas editar antes de teminar o turno, clique no botão Salvar e deixe para enviar ao final do turno. <br><br>Confirma enviar agora?",
                        autoClose: 'Não|15000',
                        draggable: true,
                        buttons: {
                            Sim: function () {
                                if(parseInt(document.getElementById("mudou").value) === 1){
                                    salvaModalReg(Envia);
                                }else{
                                    salvaRegEnv(Envia);
                                }
                            },
                            Não: function () {
                            }
                        }
                    });
                }
            }
            function salvaRegEnv(Envia){
                if(document.getElementById("dataocor").value === ""){
                    let element = document.getElementById('dataocor');
                    element.classList.add('destacaBorda');

                    document.getElementById("dataocor").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira a data do registro";
                    $('#mensagem').fadeOut(5000);
                    return false;
                }
                if(document.getElementById("selecturno").value === ""){
                    let element = document.getElementById('selecturno');
                    element.classList.add('destacaBorda');

                    document.getElementById("selecturno").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Selecione o turno do serviço";
                    $('#mensagem').fadeOut(5000);
                    return false;
                }
                if(document.getElementById("selectusuant").value === ""){
                    let element = document.getElementById('selectusuant');
                    element.classList.add('destacaBorda');
                    document.getElementById("selectusuant").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Selecione o funcionário anterior";
                    $('#mensagem').fadeOut(5000);
                    return false;
                }
                if(document.getElementById("relato").value === ""){
                    let element = document.getElementById('relato');
                    element.classList.add('destacaBorda');
                    document.getElementById("relato").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Escreva o relato";
                    $('#mensagem').fadeOut(5000);
                    return false;
                }
                 if(!validaData(document.getElementById("dataocor").value)){
                    let element = document.getElementById('dataocor');
                    element.classList.add('destacaBorda');
                    $.confirm({
                        title: 'Informação!',
                        content: 'A data está incorreta.',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=salvaRegEnv&codigo="+document.getElementById("guardacod").value+"&envia="+Envia, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("relacmodalReg").style.display = "none";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaModalReg(Envia){
                if(parseInt(document.getElementById("mudou").value) === 0){
                    document.getElementById("relacmodalReg").style.display = "none";
                    return false;
                }
                if(document.getElementById("dataocor").value === ""){
                    let element = document.getElementById('dataocor');
                    element.classList.add('destacaBorda');

                    document.getElementById("dataocor").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira a data do registro";
                    $('#mensagem').fadeOut(5000);
                    return false;
                }
                if(document.getElementById("selecturno").value === ""){
                    let element = document.getElementById('selecturno');
                    element.classList.add('destacaBorda');

                    document.getElementById("selecturno").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Selecione o turno do serviço";
                    $('#mensagem').fadeOut(5000);
                    return false;
                }
                if(document.getElementById("selectusuant").value === ""){
                    let element = document.getElementById('selectusuant');
                    element.classList.add('destacaBorda');
                    document.getElementById("selectusuant").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Selecione o funcionário anterior";
                    $('#mensagem').fadeOut(5000);
                    return false;
                }
                if(document.getElementById("relato").value === ""){
                    let element = document.getElementById('relato');
                    element.classList.add('destacaBorda');
                    document.getElementById("relato").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Escreva o relato";
                    $('#mensagem').fadeOut(5000);
                    return false;
                }
                if(!validaData(document.getElementById("dataocor").value)){
                    let element = document.getElementById('dataocor');
                    element.classList.add('destacaBorda');
                    $.confirm({
                        title: 'Informação!',
                        content: 'A data está incorreta.',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=salvaReg&codigo="+document.getElementById("guardacod").value+
                    "&datareg="+encodeURIComponent(document.getElementById("dataocor").value)+
                    "&turno="+document.getElementById("selecturno").value+
                    "&usuant="+document.getElementById("selectusuant").value+
                    "&envia="+Envia+
                    "&jatem="+document.getElementById("jatem").value+
                    "&numrelato="+encodeURIComponent(document.getElementById("numrelato").value)+
                    "&relato="+encodeURIComponent(document.getElementById("relato").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("guardacod").value = Resp.codigonovo;
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("relacmodalReg").style.display = "none";
                                    $("#carregaReg").load("modulos/lro/relReg.php");
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            
            function modifTurno(){
                document.getElementById("mudou").value = "1";
                document.getElementById("jatem").value = "0";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=buscaTurno&datareg="+document.getElementById("dataocor").value+"&turnoreg="+document.getElementById("selecturno").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    if(parseInt(Resp.jatem) > 0){
                                        document.getElementById("jatem").value = "1";
                                        document.getElementById("numrelato").value = Resp.numrelato;
                                        $.confirm({
                                            title: 'Confirmação!',
                                            content: 'Parece que este turno já foi lançado. Confirma redigir outro registro?',
                                            draggable: true,
                                            buttons: {
                                                Sim: function () {
                                                    //continua
                                                },
                                                Não: function () {
                                                    document.getElementById("relacmodalReg").style.display = "none";
                                                }
                                            }
                                        });
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function modif(){ // assinala se houve qualquer modificação
                document.getElementById("mudou").value = "1";
            }
            function fechaModal(){
                document.getElementById("guardacod").value = 0;
                document.getElementById("relacmodalReg").style.display = "none";
            }
            function fechaMostraModal(){
                document.getElementById("relacMostramodalReg").style.display = "none";
            }
            function imprReg(){
                if(parseInt(document.getElementById("guardacod").value) != 0){
                    if(parseInt(document.getElementById("mudou").value) != 0){
                        $.confirm({
                            title: 'Informação!',
                                content: 'Houve modificação. É necessario salvar antes de imprimir.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }
                    window.open("modulos/lro/imprReg.php?acao=impr&codigo="+document.getElementById("guardacod").value, document.getElementById("guardacod").value);
                }
            }

            function validaData (valor) { // tks ao Arthur Ronconi  - https://devarthur.com/blog/funcao-para-validar-data-em-javascript
                // Verifica se a entrada é uma string
                if (typeof valor !== 'string') {
                    return false;
                }
                // Verifica formado da data
                if (!/^\d{2}\/\d{2}\/\d{4}$/.test(valor)) {
                    return false;
                }
                // Divide a data para o objeto "data"
                const partesData = valor.split('/')
                const data = { 
                    dia: partesData[0], 
                    mes: partesData[1], 
                    ano: partesData[2] 
                }
                // Converte strings em número
                const dia = parseInt(data.dia);
                const mes = parseInt(data.mes);
                const ano = parseInt(data.ano);
                // Dias de cada mês, incluindo ajuste para ano bissexto
                const diasNoMes = [ 0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];
                // Atualiza os dias do mês de fevereiro para ano bisexto
                if (ano % 400 === 0 || ano % 4 === 0 && ano % 100 !== 0) {
                    diasNoMes[2] = 29
                }
                // Regras de validação:
                // Mês deve estar entre 1 e 12, e o dia deve ser maior que zero
                if (mes < 1 || mes > 12 || dia < 1) {
                    return false;
                }else if (dia > diasNoMes[mes]) { // Valida número de dias do mês
                    return false;
                }
                return true // Passou nas validações
            }

        </script>
    </head>
    <body>
        <?php
        date_default_timezone_set('America/Sao_Paulo');
        require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
        $Hoje = date('d/m/Y');
        if(!$Conec){
            echo "Sem contato com o PostGresql";
            return false;
        }
        $rs = pg_query($Conec, "SELECT column_name, data_type, character_maximum_length FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'livroreg'");
        $row = pg_num_rows($rs);
        if($row == 0){
            $Erro = 1;
            echo "Faltam tabelas. Informe à ATI.";
            return false;
        }
        $rs = pg_query($Conec, "UPDATE ".$xProj.".livroreg SET enviado = 1 WHERE datains < (NOW() - interval '13 hour') "); // marca enviado após 13 horas de inserido - o turno 3 tem 12 horas

        $admIns = parAdm("insocor", $Conec, $xProj);   // nível para inserir 
        $admEdit = parAdm("editocor", $Conec, $xProj); // nível para editar
        $editIndiv = parAdm("editlroindiv", $Conec, $xProj);   // autorização para um só indivíduo inserir
        if($editIndiv > 0){
            $rs0 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $editIndiv");
            $row0 = pg_num_rows($rs0);
            if($row0 > 0){
                $tbl0 = pg_fetch_row($rs0);
                $nomeEditLroIndiv = $tbl0[0];
            }else{
                $nomeEditLroIndiv = "";
            }
        }else{
            $nomeEditLroIndiv = "";
        }

        $OpUsuAnt = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE lro = 1 And Ativo = 1 And pessoas_id != ".$_SESSION["usuarioID"]." ORDER BY nomecompl"); // And codsetor = 
        ?>
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="guardaUsuId" value="<?php echo $_SESSION["usuarioID"] ?>" />
        <input type="hidden" id="admIns" value="<?php echo $admIns; ?>" /> <!-- nível mínimo para inserir  -->
        <input type="hidden" id="admEdit" value="<?php echo $admEdit; ?>" /> <!-- nível mínimo para editar -->
        <input type="hidden" id="guardacod" value="0" /> <!-- id ocorrência -->
        <input type="hidden" id="guardaenviado" value="0" /> <!-- relato fechado -->
        <input type="hidden" id="guardahoje" value="<?php echo $Hoje; ?>" /> <!-- data -->
        <input type="hidden" id="EditIndiv" value="<?php echo $editIndiv; ?>" /> <!-- autorização para um só indivíduo conferir o LRO -->
        <input type="hidden" id="NomeEditIndiv" value="<?php echo $nomeEditLroIndiv; ?>" />
        <input type="hidden" id="mudou" value="0" />
        <input type="hidden" id="jatem" value="0" />
        <input type="hidden" id="numrelato" value="" />
        <input type="hidden" id="guardausuins" value="0" />

        <div style="margin: 20px; border: 2px solid green; border-radius: 15px; padding: 20px;">
            <div class="box" style="position: relative; float: left; width: 33%;">
                <input type="button" id="botinserir" class="botpadr" value="Inserir Registro" onclick="InsRegistro();">
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: center;">
                <h3>Livro de Registro de Ocorrências</h3>
            </div>
            <div id="carregaReg"></div>
        </div>

        <!-- div modal para mostrar ocorrência  -->
        <div id="relacMostramodalReg" class="relacmodal">
            <div class="modal-content-RegistroLRO">
                <div style="position: absolute;"><button class="botpadrred" style="font-size: 80%;" id="mostrabotimpr" onclick="imprReg();">Gerar PDF</button></div>
                <span class="close" onclick="fechaMostraModal();">&times;</span>
                <h3 id="mostratitulomodal" style="text-align: center; color: #666;" title="<?php if($nomeEditLroIndiv != ''){echo "Conferência LRO atribuida a ".$nomeEditLroIndiv;} ?>">Livro de Registro de Ocorrências</h3>

                <div style="border: 2px solid blue; border-radius: 10px; padding: 10px; text-align: center;">
                    <div style="paddling-left: 5%;">
                        <br>
                        <label class="etiqAzul">Escritura do Livro de Registro de Ocorrências em: </label>
                        
                        <input disabled type="text" id="mostradataocor" value="<?php echo $Hoje; ?>" placeholder="Data" style="font-size: .9em; width: 100px; text-align: center;">

                        <label class="etiqAzul"> - Turno: </label><label id="turnosvc" style="font-size: 1.2rem; padding-left: 3px;"></label>
                        <input disabled type="text" id="mostraselecturno" value="" style="font-size: .9em; width: 100px; text-align: center;">
                        <br>
                        <label class="etiqAzul">Registrado por: </label><label id="mostranomeusuario" style="font-size: 1.2rem; padding-left: 3px;"></label>
                        <br>
                        <div style="text-align: center;">
                            <label class="etiqAzul"> - Recebi o serviço de: </label>
                            <input disabled type="text" id="mostraselectusuant" value="" >

                            <label class="etiqAzul"> com as seguintes alterações: </label>
                            <br><br>
                            <textarea disabled id="mostrarelato" style="border: 1px solid blue; border-radius: 10px;" rows="10" cols="85"></textarea>
                        </div>
                        <br>
                    </div>
                    <div id="mostramensagem" style="color: red; font-weight: bold;"></div>
                    <br>
                    <div style="text-align: center; padding-bottom: 20px;">
                        <button id="botedit" class="botpadrblue" onclick="carregaModal();">Editar</button>
                    </div>
                </div>
           </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para registrar ocorrência  -->
        <div id="relacmodalReg" class="relacmodal">
            <div class="modal-content-RegistroLRO">
                <span class="close" onclick="fechaModal();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"> <button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="imprReg();">Gerar PDF</button></div>
                        <div class="col quadro"><h4 id="titulomodal" style="text-align: center; color: #666;">Livro de Registro de Ocorrências</h4></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><button class="botpadrred" onclick="enviaModalReg(1);">Enviar</button></div> 
                    </div>
                </div>
                <div style="border: 2px solid blue; border-radius: 10px; padding: 10px; text-align: center;">
                    <div style="paddling-left: 5%;">
                        <br>
                        <label class="etiqAzul">Escritura do Livro de Registro de Ocorrências em: </label>

                        <input type="text" id="dataocor" onclick="tiraBorda(id);" value="<?php echo $Hoje; ?>" onchange="modif();" placeholder="Data" style="font-size: .9em; width: 100px; text-align: center;">

                        <label class="etiqAzul"> - Turno: </label><label id="turnosvc" style="font-size: 1.2rem; padding-left: 3px;"></label>
                        <select id="selecturno" onclick="tiraBorda(id);" onchange="modifTurno();" style="font-size: 0.8rem;" title="Selecione o turno.">
                            <option value=""></option>
                            <option value="1">07h00 / 13h15</option>
                            <option value="2">13h15 / 19h00</option>
                            <option value="3">19h00 / 07h00</option>
                        </select>
                        <br>
                        <label class="etiqAzul">Titular em serviço: </label><label id="nomeusuario" style="font-size: 1.2rem; padding: 5px;"><?php echo $_SESSION["NomeCompl"]; ?></label>
                        <br>
                        <div style="text-align: center;">
                            <label class="etiqAzul"> - Recebi o serviço de: </label>
                            <select id="selectusuant" style="min-width: 120px;" onclick="tiraBorda(id);" onchange="modif();" title="Selecione um usuário.">
                                <option value=""></option>
                                <?php 
                                if($OpUsuAnt){
                                    while ($Opcoes = pg_fetch_row($OpUsuAnt)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                            </select>
                            <label class="etiqAzul"> com as seguintes alterações: </label>
                            <br><br>
                            <textarea style="border: 1px solid blue; border-radius: 10px;" rows="10" cols="85" id="relato" onclick="tiraBorda(id);" onchange="modif();"></textarea>
                        </div>
                        <br>
                    </div>
                    <div id="mensagem" style="color: red; font-weight: bold;"></div>
                    <br>
                    <div style="text-align: center; padding-bottom: 20px;">
                        <button class="botpadrblue" onclick="salvaModalReg(0);">Salvar</button>
                    </div>
                </div>
           </div>
        </div> <!-- Fim Modal-->
    </body>
</html>