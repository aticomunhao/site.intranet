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
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/indlog.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script> <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="comp/js/jquery.mask.js"></script>
        <style>
            .modalMsg-content{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 7% auto; /* 10% do topo e centrado */
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
                var nHora = new Date();   //   function mostraRelogio()  //em relacao.js da SCer
                var hora = nHora.getHours();
                var minuto = nHora.getMinutes();
                var Cumpr = "Bom Dia!";
                if(hora >= 0){
                    document.getElementById("selecturno").value = "3";
                }
                if(hora >= 7){
                    document.getElementById("selecturno").value = "1";
                }
                if(hora >= 12){
                    Cumpr = "Boa Tarde!";
                }
                if(hora >= 13){
                    document.getElementById("selecturno").value = "2";
                }
                if(hora >= 18){
                    Cumpr = "Boa Noite!";
                }
                if(hora >= 19){
                    document.getElementById("selecturno").value = "3";
                }

                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=AcessoLro", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    if(parseInt(Resp.lro) === 0 && parseInt(Resp.fisclro) === 0 && parseInt(document.getElementById("UsuAdm").value) < 7){
                                        $.confirm({
                                            title: Cumpr,
                                            content: 'Usuário não cadastrado para acesso ao LRO. <br>O acesso é proporcionado pela ATI.',
                                            autoClose: 'OK|7000',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                    }else{
                                        $("#carregaReg").load("modulos/lro/relReg.php");
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
                document.getElementById("botRedigirCompl").style.visibility = "hidden"; // para redigir um complemento
                document.getElementById("botimprLRO").style.visibility = "hidden"; // botão de imprimir todo o LRO
                if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admEdit").value) && parseInt(Resp.fiscalizaLro) === 1){
                    document.getElementById("botimprLRO").style.visibility = "visible";
                }
                if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                    document.getElementById("botimprLRO").style.visibility = "visible";
                }

                $("#dataocor").mask("99/99/9999");
                document.getElementById("dataocor").disabled = true; // não deixar mudar a data do registro no LRO

                document.getElementById("botinserir").style.visibility = "hidden"; 
                if(parseInt(document.getElementById("acessoLRO").value) === 1){ // tem que estar autorizado no cadastro de usuários
                    document.getElementById("botinserir").style.visibility = "visible"; 
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
                document.getElementById("quantjatem").value = "0";
                document.getElementById("numrelato").value = "";
                document.getElementById('ocorrencia2').checked = true; // não houve ocorr
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
                                        document.getElementById("dataocor").value = document.getElementById("guardahoje").value;
                                        document.getElementById("nomeusuario").innerHMTL = "";
                                        document.getElementById("selectusuant").value = "";
                                        document.getElementById("relato").value = "";
                                        document.getElementById("relato").disabled = true;
                                        document.getElementById("relacmodalReg").style.display = "block";
                                        document.getElementById("quantjatem").value = Resp.jatem;
                                        if(parseInt(Resp.jatem) > 0){
                                            document.getElementById("jatem").value = "1";
                                            document.getElementById("numrelato").value = Resp.numrelato;
                                            $.confirm({
                                                title: "Atenção!",
                                                content: "Este turno "+document.getElementById("dataocor").value+" - "+Resp.descturno+"  já foi lançado.",
                                                draggable: true,
                                                buttons: {
                                                    OK: function(){
                                                        document.getElementById("selecturno").value = "";
                                                    }
                                                }
                                            });
                                        }
                                        //Condição aqui: período entre 00 e 07 horas, turnos 1 e 2 do dia anterior já foram lançados, 
                                        //já passou da meia noite e faltou o turno 3 -> voltar a data 1 dia
                                        //Situação: o operador deixou para fazer o livro depois da meia noite.
                                        if(parseInt(Resp.turno1) > 0 && parseInt(Resp.turno2) > 0 && parseInt(Resp.turno3) === 0){
                                            document.getElementById("dataocor").value = Resp.dataontem;
                                        }
                                    }else{
                                        $.confirm({
                                            title: 'Informação!',
                                            content: 'Usuário não cadastrado para inserir registros no LRO. <br>O acesso é proporcionado pela ATI.',
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

            function mostraModal(Cod){ // só mostra após o clique. Para editar chama carregaModal()
                document.getElementById("mostramensagem").innerHTML = "";
                document.getElementById("botRedigirCompl").style.visibility = "hidden";
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
                                    if(parseInt(Resp.acessoLro) === 1 || parseInt(Resp.fiscalizaLro) === 1){
                                        document.getElementById("guardacod").value = Cod;
                                        document.getElementById("mostradataocor").value = Resp.data;
                                        document.getElementById("mostraselecturno").value = Resp.descturno;
                                        document.getElementById("mostranomeusuario").innerHTML = Resp.nomeusuins;
                                        document.getElementById("mostraselectusuant").value = Resp.nomeusuant;
                                        document.getElementById("mostrarelato").value = Resp.relato;
                                        document.getElementById("numrelato").value = Resp.numrelato;
                                        document.getElementById("guardausuins").value = Resp.codusuins; // usuário que inseriu o relato
                                        if(parseInt(Resp.ocor) === 0){
                                            document.getElementById('mostraocorrencia2').checked = true;  // não houve ocorr
                                            document.getElementById('relato').disabled = true; 
                                        }else{
                                            document.getElementById('mostraocorrencia1').checked = true; // houve ocorr
                                        }
                                        document.getElementById("guardaenviado").value = Resp.enviado; // encerrou o relato - não edita mais
                                        if(parseInt(Resp.enviado) === 1){
                                            document.getElementById("mostramensagem").innerHTML = "Registro Enviado.";
//Botão inserir complemento - Aguardando determinar tempo 
                                            if(parseInt(Resp.codusuins) === parseInt(document.getElementById("guardaUsuId").value)){ // turno de outro funcionário
                                                if(parseInt(Resp.diasdecorridos) < 1){ //    diasdecorridos < 1 -> no mesmo dia
                                                    document.getElementById("botRedigirCompl").style.visibility = "visible";
                                                }
                                            }

                                        }
                                        document.getElementById("relacMostramodalReg").style.display = "block";
                                    }
                                    //permissões
                                    document.getElementById("botedit").style.visibility = "hidden"; // botão de editar
                                    document.getElementById("botimpr").style.visibility = "hidden"; // botão de imprimir
                                    document.getElementById("mostrabotimpr").style.visibility = "hidden"; // botão de imprimir na visualização

                                    if(parseInt(Resp.enviado) === 0 && parseInt(Resp.codusuins) === parseInt(document.getElementById("guardaUsuId").value)){ // ainda não fechou e foi o usu logado que inseriu
                                        document.getElementById("botedit").style.visibility = "visible"; // botão de editar
                                    }
                                    if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admEdit").value) && parseInt(Resp.fiscalizaLro) === 1){
                                        document.getElementById("mostrabotimpr").style.visibility = "visible";
                                    }
                                    if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                                        document.getElementById("botimpr").style.visibility = "visible";
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
                                    if(parseInt(Resp.ocor) === 0){
                                            document.getElementById('ocorrencia2').checked = true;  // não houve ocorr
                                            document.getElementById('relato').disabled = true; 
                                        }else{
                                            document.getElementById('ocorrencia1').checked = true; // houve ocorr
                                        }
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

            function carregaModalCompl(){
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
                                    document.getElementById("compldataocor").value = Resp.data;
                                    document.getElementById("complselecturno").value = Resp.turno;
                                    document.getElementById("complselectusuant").value = Resp.usuant;
                                    document.getElementById("complnomeusuario").innerHTML = Resp.nomeusuins;
                                    document.getElementById("numrelato").value = Resp.numrelato;
                                    document.getElementById("numrelatoCompl").innerHTML = "Complementando Relato: "+Resp.numrelato;
                                    document.getElementById('complocorrencia1').checked = true; // houve ocorr
                                    document.getElementById("complrelato").value = "";
                                    document.getElementById("relacMostramodalReg").style.display = "none";
                                    document.getElementById("relacmodalCompl").style.display = "block";
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
                    if(document.getElementById("selecturno").value == ""){
                        let element = document.getElementById('selecturno');
                        element.classList.add('destacaBorda');
                    }
                    if(document.getElementById("selectusuant").value == ""){
                        let element = document.getElementById('selectusuant');
                        element.classList.add('destacaBorda');
                    }
                    if(document.getElementById("selecturno").value !== "" && document.getElementById("selectusuant").value !== ""){
                        $.confirm({
                            title: 'Confirmação!',
                            content: "O relato será enviado ao setor competente e não poderá ser modificado.<br> Se quiser editar antes de teminar o turno, clique no botão Salvar e deixe para enviar ao final do turno. <br><br>Confirma enviar agora?",
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
            }

            function enviaModalCompl(Envia){
                if(parseInt(Envia) === 1){
                    if(document.getElementById("complrelato").value !== ""){
                        $.confirm({
                            title: 'Confirmação!',
                            content: "O relato complementar será enviado ao setor competente.<br>Confirma enviar agora?",
                            autoClose: 'Não|15000',
                            draggable: true,
                            buttons: {
                                Sim: function () {
                                    if(parseInt(document.getElementById("mudou").value) === 1){
                                        salvaModalCompl(Envia);
                                    }else{
                                        salvaRegEnv(Envia);
                                    }
                                },
                                Não: function () {
                                }
                            }
                        });
                    }else{
                        $('#mensagemcompl').fadeIn("slow");
                        document.getElementById("mensagemcompl").innerHTML = "Escreva o relato";
                        $('#mensagemcompl').fadeOut(5000);
                    }
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
                    if(document.getElementById('ocorrencia1').checked == true){ // houve ocorrência
                        let element = document.getElementById('relato');
                        element.classList.add('destacaBorda');
                        document.getElementById("relato").focus();
                        $('#mensagem').fadeIn("slow");
                        document.getElementById("mensagem").innerHTML = "Escreva o relato";
                        $('#mensagem').fadeOut(5000);
                        return false;
                    }
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
                if(document.getElementById('ocorrencia1').checked == true){ // houve ocorrência
                    Ocor = 1;
                    if(document.getElementById("relato").value === ""){
                        let element = document.getElementById('relato');
                        element.classList.add('destacaBorda');
                        document.getElementById("relato").focus();
                        $('#mensagem').fadeIn("slow");
                        document.getElementById("mensagem").innerHTML = "Escreva o relato";
                        $('#mensagem').fadeOut(5000);
                        return false;
                    }
                }else{
                    Ocor = 0;
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
                    "&ocor="+Ocor+
                    "&envia="+Envia+
                    "&jatem="+document.getElementById("jatem").value+
                    "&quantjatem="+document.getElementById("quantjatem").value+
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

            function salvaModalCompl(Envia){
                if(parseInt(document.getElementById("mudou").value) === 0){
                    $('#mensagemcompl2').fadeIn("slow");
                    document.getElementById("mensagemcompl2").innerHTML = "Escreva o relato";
                    $('#mensagemcompl2').fadeOut(3000);
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=salvaRegCompl&codigo="+document.getElementById("guardacod").value+
                    "&datareg="+encodeURIComponent(document.getElementById("compldataocor").value)+
                    "&turno="+document.getElementById("complselecturno").value+
                    "&usuant="+document.getElementById("complselectusuant").value+
                    "&ocor=1"+
                    "&envia="+Envia+
                    "&jatem=1"+
                    "&numrelato="+encodeURIComponent(document.getElementById("numrelato").value)+
                    "&relato="+encodeURIComponent(document.getElementById("complrelato").value), true);
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
                                    document.getElementById("relacmodalCompl").style.display = "none";
                                    $("#carregaReg").load("modulos/lro/relReg.php");
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            
            function modifTurno(){
                if(document.getElementById("selecturno").value != ""){
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
                                        if(parseInt(Resp.codusu) !== parseInt(document.getElementById("guardaUsuId").value)){ // turno de outro funcionário
                                            $.confirm({
                                                title: 'Informação!',
                                                content: 'Este turno foi cumprido por '+Resp.nomeusu,
                                                draggable: true,
                                                buttons: {
                                                    OK: function(){
                                                        document.getElementById("relacmodalReg").style.display = "none";
                                                    }
                                                }
                                            });
                                            return false;
                                        }else{
                                            $.confirm({
                                                title: 'Confirmação!',
                                                content: 'Parece que este turno já foi lançado. Confirma redigir outro registro?',
                                                draggable: true,
                                                buttons: {
                                                    Sim: function () {
                                                        //continua
                                                    },
                                                    Não: function () {
                                                        document.getElementById("mudou").value = "0";
                                                        document.getElementById("selecturno").value = "";
                                                        document.getElementById("relacmodalReg").style.display = "none";
                                                    }
                                                }
                                            });
                                        }
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
                }
            }

            function modif(){ // assinala se houve qualquer modificação
                document.getElementById("mudou").value = "1";
            }
            function fechaModal(){
                document.getElementById("guardacod").value = 0;
                document.getElementById("mudou").value = "0";
                document.getElementById("relacmodalReg").style.display = "none";
            }
            function fechaMostraModal(){
                document.getElementById("relacMostramodalReg").style.display = "none";
            }
            function fechaModalCompl(){
                document.getElementById("relacmodalCompl").style.display = "none";
            }
            function carregaHelpLRO(){
                document.getElementById("relacHelpLRO").style.display = "block";
            }
            function fechaHelpLRO(){
                document.getElementById("relacHelpLRO").style.display = "none";
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
            function imprLRO(){
                window.open("modulos/lro/imprLRO.php?acao=impr", "imprLRO");
            }
            function abreOcor(Valor){
                document.getElementById("mudou").value = "1";
                if(parseInt(Valor) === 0){
                    if(document.getElementById("relato").value != ""){
                        $.confirm({
                            title: 'Confirmação!',
                            content: 'O que já foi escrito será perdido. <br>Confirma apagar o relato?',
                            autoClose: 'Não|10000',
                            draggable: true,
                            buttons: {
                                Sim: function () {
                                    document.getElementById("relato").value = "";
                                },
                                Não: function () {
                                    document.getElementById('ocorrencia1').checked = true; // houve ocorr
                                }
                            }
                        });
                    }
                    document.getElementById("relato").disabled = true;
                }else{
                    document.getElementById("relato").disabled = false;
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

        $Lro = parEsc("lro", $Conec, $xProj, $_SESSION["usuarioID"]);
        $FiscLro = parEsc("fisclro", $Conec, $xProj, $_SESSION["usuarioID"]);

        $OpUsuAnt = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE lro = 1 And Ativo = 1 And pessoas_id != ".$_SESSION["usuarioID"]." ORDER BY nomeusual, nomecompl"); // And codsetor = 
        $OpUsuAnt2 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE lro = 1 And Ativo = 1 And pessoas_id != ".$_SESSION["usuarioID"]." ORDER BY nomeusual, nomecompl"); 
        ?>
        <input type="hidden" id="UsuAdm" value = "<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="guardaUsuId" value = "<?php echo $_SESSION["usuarioID"] ?>" />
        <input type="hidden" id="admIns" value = "<?php echo $admIns; ?>" /> <!-- nível mínimo para inserir  -->
        <input type="hidden" id="admEdit" value = "<?php echo $admEdit; ?>" /> <!-- nível mínimo para editar -->
        <input type="hidden" id="guardacod" value = "0" /> <!-- id ocorrência -->
        <input type="hidden" id="guardaenviado" value = "0" /> <!-- relato fechado -->
        <input type="hidden" id="guardahoje" value = "<?php echo $Hoje; ?>" /> <!-- data -->
        <input type="hidden" id="EditIndiv" value = "0" /> <!-- autorização para um só indivíduo conferir o LRO -->
        <input type="hidden" id="mudou" value = "0" />
        <input type="hidden" id="jatem" value = "0" />
        <input type="hidden" id="quantjatem" value = "0" />
        <input type="hidden" id="numrelato" value = "" />
        <input type="hidden" id="guardausuins" value = "0" />
        <input type="hidden" id="acessoLRO" value="<?php echo $Lro; ?>" />
        <input type="hidden" id="fiscalLRO" value="<?php echo $FiscLro; ?>" />

        <div style="margin: 20px; border: 2px solid green; border-radius: 15px; padding: 20px; min-height: 70px;">
            <div class="box" style="position: relative; float: left; width: 33%;">
                <input type="button" id="botinserir" class="botpadr" value="Inserir Registro" onclick="InsRegistro();">
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: center;">
                <h5>Livro de Registro de Ocorrências</h5>
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: right;">
                <button class="botpadrred" style="font-size: 80%;" id="botimprLRO" onclick="imprLRO();">Gerar PDF</button>
                <label style="padding-left: 20px;"></label>
                <img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpLRO();" title="Guia rápido">
            </div>

            <div id="carregaReg"></div>
        </div>


        <!-- div modal para registrar ocorrência  -->
        <div id="relacmodalReg" class="relacmodal">
            <div class="modal-content-RegistroLRO">
                <span class="close" onclick="fechaModal();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"> <button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="imprReg();">Gerar PDF</button></div>
                        <div class="col quadro"><h5 id="titulomodal" style="text-align: center; color: #666;">Livro de Registro de Ocorrências</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><button id="botenviar" class="botpadrred" onclick="enviaModalReg(1);">Enviar</button></div> 
                    </div>
                </div>
                <div style="border: 2px solid blue; border-radius: 10px; padding: 5px; text-align: center;">
                    <div style="paddling-left: 5%;">

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
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php if(!is_null($Opcoes[2]) && $Opcoes[2] != ""){ echo $Opcoes[2]." - ".$Opcoes[1];}else{echo $Opcoes[1];} ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                            </select>
                            <label class="etiqAzul"> ciente das alterações dos turnos anteriores.</label>
                            <br>
                            <label class="etiqAzul" title="Marcar se houve ou não houve ocorrência digna de nota">Ocorrências: </label>
                            <input type="radio" name="ocorrencia" id="ocorrencia1" value="1" title="Houve algo que precisa ser relatado" onclick="abreOcor(value);"><label for="ocorrencia1" class="etiqAzul" style="padding-left: 3px;"> Houve</label>
                            <input type="radio" name="ocorrencia" id="ocorrencia2" value="0" CHECKED title="Nada aconteceu que seja digno de nota" onclick="abreOcor(value);"><label for="ocorrencia2" class="etiqAzul" style="padding-left: 3px;"> Não Houve</label>
                            <br>
                            <textarea style="margin-top: 3px; border: 1px solid blue; border-radius: 10px;" rows="8" cols="90" id="relato" onclick="tiraBorda(id);" onchange="modif();"></textarea>
                        </div>
                        <br>
                    </div>
                    <div id="mensagem" style="color: red; font-weight: bold;"></div>
                    <div style="text-align: center; padding-bottom: 10px;">
                        <button class="botpadrblue" onclick="salvaModalReg(0);">Salvar</button>
                    </div>
                </div>
           </div>
        </div> <!-- Fim Modal-->


        <!-- div modal para mostrar ocorrência  -->
        <div id="relacMostramodalReg" class="relacmodal">
            <div class="modal-content-RegistroLRO">
                <div style="position: absolute;"><button class="botpadrred" style="font-size: 80%;" id="mostrabotimpr" onclick="imprReg();">Gerar PDF</button></div>
                <span class="close" onclick="fechaMostraModal();">&times;</span>
                <h5 id="mostratitulomodal" style="text-align: center; color: #666;">Livro de Registro de Ocorrências</h5>

                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col" style="margin: 0 auto;"> </div>
                        <div class="col"><h6 style="text-align: center; color: blue;">Visualização</h6></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col" style="margin: 0 auto; text-align: right; padding-bottom: 5px;"><button id="botRedigirCompl" class="botpadrblue" style="font-size: 70%;" onclick="carregaModalCompl();" title="Redigir um complemento a esta ocorrência">Complemento</button></div> 
                    </div>
                </div>

                <div style="border: 2px solid blue; border-radius: 10px; padding: 5px; text-align: center;">
                    <div style="paddling-left: 5%;">
                        
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

                            <label class="etiqAzul"> ciente das alterações dos turnos anteriores. </label>
                            <br>
                            <label class="etiqAzul" title="Marcar se houve ou não houve ocorrência digna de nota">Ocorrências: </label>
                            <input disabled type="radio" name="mostraocorrencia" id="mostraocorrencia1" value="1" title="Houve algo que precisa ser relatado"><label for="mostraocorrencia1" class="etiqAzul" style="padding-left: 3px;"> Houve</label>
                            <input disabled type="radio" name="mostraocorrencia" id="mostraocorrencia2" value="0" CHECKED title="Nada aconteceu que seja digno de nota"><label for="mostraocorrencia2" class="etiqAzul" style="padding-left: 3px;"> Não Houve</label>
                            <br>
                            <textarea disabled id="mostrarelato" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px;" rows="10" cols="90"></textarea>
                        </div>
                    </div>
                    <div id="mostramensagem" style="color: red; font-weight: bold;"></div>
                    <div style="text-align: center; padding-bottom: 10px;">
                        <button id="botedit" class="botpadrblue" onclick="carregaModal();">Editar</button>
                    </div>
                </div>
           </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para redigir complemento  -->
        <div id="relacmodalCompl" class="relacmodal">
            <div class="modal-content-RegistroLRO">
                <span class="close" onclick="fechaModalCompl();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col" style="margin: 0 auto;"> <!-- <button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="imprReg();">Gerar PDF</button> --> </div>
                        <div class="col"><h5 style="text-align: center; color: #666;">Complemento ao LRO</h5>
                            <div id="numrelatoCompl" style="color: blue; text-align: center; font-size: 90%;"></div>
                        </div> <!-- Central - espaçamento entre colunas  -->
                        
                        <div class="col" style="margin: 0 auto; text-align: center;"><button id="botenviarcompl" class="botpadrred" onclick="enviaModalCompl(1);">Enviar</button>
                        <div id="mensagemcompl" style="color: red; font-weight: bold;"></div>
                    </div> 
                    </div>
                </div>
                <div style="border: 2px solid blue; border-radius: 10px; padding: 5px; text-align: center;">
                    <div style="paddling-left: 5%;">
                        <label class="etiqAzul">Escritura do Livro de Registro de Ocorrências em: </label>
                        <input disabled type="text" id="compldataocor" onclick="tiraBorda(id);" value="<?php echo $Hoje; ?>" onchange="modif();" placeholder="Data" style="font-size: .9em; width: 100px; text-align: center;">
                        <label class="etiqAzul"> - Turno: </label><label id="complturnosvc" style="font-size: 1.2rem; padding-left: 3px;"></label>
                        <select disabled id="complselecturno" style="font-size: 0.8rem;" title="Selecione o turno.">
                            <option value=""></option>
                            <option value="1">07h00 / 13h15</option>
                            <option value="2">13h15 / 19h00</option>
                            <option value="3">19h00 / 07h00</option>
                        </select>
                        <br>
                        <label class="etiqAzul">Titular em serviço: </label><label id="complnomeusuario" style="font-size: 1.2rem; padding: 5px;"><?php echo $_SESSION["NomeCompl"]; ?></label>
                        <br>
                        <div style="text-align: center;">
                            <label class="etiqAzul"> - Recebi o serviço de: </label>
                            <select disabled id="complselectusuant" style="min-width: 120px;">
                                <option value=""></option>
                                <?php 
                                if($OpUsuAnt2){
                                    while ($Opcoes = pg_fetch_row($OpUsuAnt2)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php if(!is_null($Opcoes[2]) && $Opcoes[2] != ""){ echo $Opcoes[2]." - ".$Opcoes[1];}else{echo $Opcoes[1];} ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                            </select>
                            <label class="etiqAzul"> ciente das alterações dos turnos anteriores.</label>
                            <br>
                            <label class="etiqAzul" title="Marcar se houve ou não houve ocorrência digna de nota">Ocorrências: </label>
                            <input type="radio" name="complocorrencia" id="complocorrencia1" value="1" CHECKED title="Houve algo que precisa ser relatado"><label for="complocorrencia1" class="etiqAzul" style="padding-left: 3px;"> Houve</label>
                            <input type="radio" name="complocorrencia" id="complocorrencia2" value="0" title="Nada aconteceu que seja digno de nota"><label for="complocorrencia2" class="etiqAzul" style="padding-left: 3px;"> Não Houve</label> 
                            <br>
                            <textarea id="complrelato" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px;" rows="8" cols="90" onclick="tiraBorda(id);" onchange="modif();"></textarea>
                        </div>
                        <br>
                    </div>
                    <div id="mensagemcompl2" style="color: red; font-weight: bold;"></div>
                    <div style="text-align: center; padding-bottom: 10px;">
                        <button class="botpadrblue" onclick="salvaModalCompl(0);">Salvar</button>
                    </div>
                </div>
           </div>
        </div> <!-- Fim Modal-->


        <!-- div modal para leitura instruções -->
        <div id="relacHelpLRO" class="relacmodal">
            <div class="modalMsg-content">
                <span class="close" onclick="fechaHelpLRO();">&times;</span>
                <h4 style="text-align: center; color: #666;">Informações</h4>
                <h5 style="text-align: center; color: #666;">Livro de Registro de Ocorrências</h5>
                <div style="border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px;">
                    Regras inseridas:
                    <ul>
                        <li>1 - O Livro de Registro de Ocorrências (LRO) destina-se a registrar os acontecimentos dignos de registro durante os turnos de serviço na portaria.</li>
                        <li>2 - O funcionário designado para o inserir o registro deve anotar ao final do turno (botão Inserir Registro) tudo o que ocorreu durante seu serviço.</li>
                        <li>3 - Se quiser, o funcionário pode iniciar o registro (botão Inserir Registro) já no início do turno, salvar e deixar para enviar o relato ao final do serviço (botão Enviar).</li>
                        <li>4 - Ao salvar o registro, ele aparecerá no topo da relação e pode ser editado até o final do turno. Basta clicar sobre linha do registro e depois em Editar na caixa que aparece.</li>
                        <li>5 - Ao final do turno um clique no botão Enviar encerra o serviço e envia o relato para a administração.</li>
                        <li>6 - Depois de enviado o registro não poderá mais ser alterado.</li>
                        <li>7 - Caso haja necessidade de complementar o registro depois de enviado, é possível inserir outro registro para o mesmo turno pelo mesmo funcionário no mesmo dia.</li>
                        <li>8 - Esse segundo registro terá o mesmo número e será nomeado como complementar. Não pode ser feito em outro dia.</li>
                        <li>9 - Evite comentários e opiniões pessoais, isso pode ser feito pessoalmente na administração.</li>
                    </ul>
                </div>
            </div>
        </div>  <!-- Fim Modal Help-->
    </body>
</html>