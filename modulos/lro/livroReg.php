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
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/indlog.css" />
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script> <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="comp/js/jquery.mask.js"></script>
        <style>
            .modalMsg-content{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%; /* acertar de acordo com a tela */
            }
            .checkList-content{
                background: linear-gradient(180deg, white, #00FF99);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%; /* acertar de acordo com a tela */
            }
            .config-content{
                background: linear-gradient(180deg, white, #00BFFF);
                margin: 10% auto;
                padding: 10px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 75%;
                max-width: 800px;
                overflow: auto;
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
                var nHora = new Date(); 
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
                                        $('#carregaTema').load('modulos/config/carTema.php?carpag=livroReg');
                                        $("#faixaCentral").load("modulos/lro/relReg.php");
                                        document.getElementById("selectTema").style.visibility = "visible";
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }

                document.getElementById("botRedigirCompl").style.visibility = "hidden"; // para redigir um complemento
                document.getElementById("botimprLRO").style.visibility = "hidden"; // botão de imprimir todo o LRO
                document.getElementById("imgLROConfig").style.visibility = "hidden";
                document.getElementById("etiqrubrica").style.visibility = "hidden";
                document.getElementById("etiqcheckrubrica").style.visibility = "hidden";
                document.getElementById("checkrubrica").style.visibility = "hidden";
                document.getElementById("selectTema").style.visibility = "hidden";
                if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admEdit").value) && parseInt(Resp.fiscalizaLro) === 1){
                    document.getElementById("botimprLRO").style.visibility = "visible";
                }

                if(parseInt(document.getElementById("fiscalLRO").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                    document.getElementById("botimprLRO").style.visibility = "visible";
                }

                if(parseInt(document.getElementById("revisorLRO").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){ // rubricar LRO ou superusuário
                    document.getElementById("imgLROConfig").style.visibility = "visible";
                }

                if(parseInt(document.getElementById("revisorLRO").value) === 1){ // rubricar LRO
                    document.getElementById("etiqrubrica").style.visibility = "visible";
                    document.getElementById("etiqcheckrubrica").style.visibility = "visible";
                    document.getElementById("checkrubrica").style.visibility = "visible";
                }

                $("#dataocor").mask("99/99/9999");
                document.getElementById("dataocor").disabled = true; // não deixar mudar a data do registro no LRO

                document.getElementById("botinserir").style.visibility = "hidden"; 
                if(parseInt(document.getElementById("acessoLRO").value) === 1){ // tem que estar autorizado no cadastro de usuários
                    document.getElementById("botinserir").style.visibility = "visible"; 
                }
                $("#listaCheckList").load("modulos/lro/checkListLRO.php");
                $("#listaCheckReg").load("modulos/lro/checkListReg.php");

                modalMostra = document.getElementById('relacMostramodalReg'); //span[0]
                spanMostra = document.getElementsByClassName("close")[0];
                window.onclick = function(event){
                    if(event.target === modalMostra){
                        modalMostra.style.display = "none";
                    }
                };

                $("#selecMesAno").change(function(){
                    document.getElementById("selecAno").value = "";
                    if(document.getElementById("selecMesAno").value != ""){
                        window.open("modulos/lro/imprLRO.php?acao=listamesLRO&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), document.getElementById("selecMesAno").value);
                        document.getElementById("selecMesAno").value = "";
                        document.getElementById("relacimprBens").style.display = "none";
                    }
                });
                $("#selecAno").change(function(){
                    document.getElementById("selecMesAno").value = "";
                    if(document.getElementById("selecAno").value != ""){
                        window.open("modulos/lro/imprLRO.php?acao=listaanoLRO&ano="+encodeURIComponent(document.getElementById("selecAno").value), document.getElementById("selecAno").value);
                        document.getElementById("selecAno").value = "";
                        document.getElementById("relacimprBens").style.display = "none";
                    }
                });

                $("#configSelecUsu").change(function(){
                    if(document.getElementById("configSelecUsu").value == ""){
                        document.getElementById("configCpfUsu").value = "";
                        document.getElementById("checkefetivo").checked = false;
                        document.getElementById("checkfiscal").checked = false;
                        document.getElementById("checkrubrica").checked = false;
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/lro/salvaReg.php?acao=buscaCodUsu&codigo="+document.getElementById("configSelecUsu").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configCpfUsu").value = format_CnpjCpf(Resp.cpf);
                                        if(parseInt(Resp.lro) === 1){
                                            document.getElementById("checkefetivo").checked = true;
                                        }else{
                                            document.getElementById("checkefetivo").checked = false;
                                        }
                                        if(parseInt(Resp.fisclro) === 1){
                                            document.getElementById("checkfiscal").checked = true;
                                        }else{
                                            document.getElementById("checkfiscal").checked = false;
                                        }
                                        if(parseInt(Resp.revlro) === 1){
                                            document.getElementById("checkrubrica").checked = true;
                                        }else{
                                            document.getElementById("checkrubrica").checked = false;
                                        }
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#configCpfUsu").click(function(){
                    document.getElementById("configSelecUsu").value = "";
                    document.getElementById("configCpfUsu").value = "";
                    document.getElementById("checkefetivo").checked = false;
                    document.getElementById("checkfiscal").checked = false;
                    document.getElementById("checkrubrica").checked = false;
                });
                $("#configCpfUsu").change(function(){
                    document.getElementById("configSelecUsu").value = "";
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/lro/salvaReg.php?acao=buscaCpfUsu&cpf="+encodeURIComponent(document.getElementById("configCpfUsu").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configSelecUsu").value = Resp.PosCod;
                                        if(parseInt(Resp.lro) === 1){
                                            document.getElementById("checkefetivo").checked = true;
                                        }else{
                                            document.getElementById("checkefetivo").checked = false;
                                        }
                                        if(parseInt(Resp.fisclro) === 1){
                                            document.getElementById("checkfiscal").checked = true;
                                        }else{
                                            document.getElementById("checkfiscal").checked = false;
                                        }
                                        if(parseInt(Resp.revlro) === 1){
                                            document.getElementById("checkrubrica").checked = true;
                                        }else{
                                            document.getElementById("checkrubrica").checked = false;
                                        }
                                    }
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.");
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("checkefetivo").checked = false;
                                        document.getElementById("checkfiscal").checked = false;
                                        document.getElementById("checkrubrica").checked = false;
                                          $('#mensagemConfig').fadeIn("slow");
                                        document.getElementById("mensagemConfig").innerHTML = "Não encontrado";
                                        $('#mensagemConfig').fadeOut(2000);
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

            }); // fim ready

            function InsRegistro(){ // inserir novo registro
                document.getElementById("jatem").value = "0";
                document.getElementById("quantjatem").value = "0";
                document.getElementById("numrelato").value = "";
                document.getElementById('ocorrencia2').checked = true; // não houve ocorr

                //Situação: o operador deixou para fazer o livro depois da meia noite.
                if(parseInt(Resp.hora) >= 0 && parseInt(Resp.hora) <= 7){
//                    document.getElementById("dataocor").value = Resp.dataontem;
                    document.getElementById("selecturno").value = "3";
                }
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
                                        document.getElementById("selectusuprox").value = "";
                                        document.getElementById("relato").value = "";
                                        document.getElementById("relato").disabled = true;
                                        document.getElementById("relatosubstit").value = "";
                                        document.getElementById("selectsubstit").value = "";
                                        document.getElementById("relacmodalReg").style.display = "block";
                                        document.getElementById("quantjatem").value = Resp.jatem;
                                        if(parseInt(Resp.jatem) > 0){
                                            document.getElementById("jatem").value = "1";
                                            document.getElementById("numrelato").value = Resp.numrelato;
                                            $.confirm({
                                                title: "Atenção!",
                                                content: "Este turno "+Resp.descturno+"  já foi lançado.",
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
                                        if(parseInt(Resp.hora) >= 0 && parseInt(Resp.hora) <= 7){
                                            document.getElementById("dataocor").value = Resp.dataontem;
//                                            document.getElementById("selecturno").value = "3";
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
                                        document.getElementById("mostraselectusuprox").value = Resp.nomeusuprox;
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
                                            document.getElementById("mostrarelatofiscal").value = Resp.relatofisc;
//Botão inserir complemento - Aguardando determinar tempo 
                                            if(parseInt(Resp.codusuins) === parseInt(document.getElementById("guardaUsuId").value)){ // turno de outro funcionário
                                                if(parseInt(Resp.diasdecorridos) < 1){ //    diasdecorridos < 1 -> no mesmo dia
                                                    document.getElementById("botRedigirCompl").style.visibility = "visible";
                                                }
                                            }
                                            document.getElementById('etiqmostrarelatofiscal').style.visibility = "visible";
                                            document.getElementById('mostrarelatofiscal').style.visibility = "visible";
                                            document.getElementById('botsalvaRelFisc').style.visibility = "hidden";
                                            document.getElementById('botmarcaLido').style.visibility = "hidden";
                                            document.getElementById('imgmarcaLido').style.visibility = "hidden";
                                            document.getElementById('NumRegistro').innerHTML = "Registro nº "+Resp.numrelato;
//                                            if(parseInt(document.getElementById("fiscalLRO").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                                            if(parseInt(document.getElementById("revisorLRO").value) === 1){ // revisor (lro_rev) 
                                                if(parseInt(Resp.lidofisc) === 0){
                                                    document.getElementById('botmarcaLido').style.visibility = "visible";
                                                }else{
                                                    document.getElementById('imgmarcaLido').style.visibility = "visible";
                                                }
                                                document.getElementById('mostrarelatofiscal').disabled = false;
                                            }
                                        }else{
                                            document.getElementById('mostrarelatofiscal').disabled = true;
                                            document.getElementById('etiqmostrarelatofiscal').style.visibility = "hidden";
                                            document.getElementById('mostrarelatofiscal').style.visibility = "hidden";
                                            document.getElementById('botsalvaRelFisc').style.visibility = "hidden";
                                            document.getElementById('botmarcaLido').style.visibility = "hidden";
                                            document.getElementById('imgmarcaLido').style.visibility = "hidden";
                                        }
                                        document.getElementById("mostrarelatosubstit").value = Resp.substit;
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
                                    document.getElementById("selectusuprox").value = Resp.usuprox;
                                    if(parseInt(Resp.ocor) === 0){
                                        document.getElementById('ocorrencia2').checked = true;  // não houve ocorr
                                        document.getElementById('relato').disabled = true; 
                                    }else{
                                        document.getElementById('ocorrencia1').checked = true; // houve ocorr
                                        document.getElementById('relato').disabled = false;
                                    }
                                    document.getElementById("relato").value = Resp.relato;
                                    document.getElementById("relatosubstit").value = Resp.substit;
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
                                    document.getElementById("complselectusuprox").value = Resp.nomeusuprox;
                                    document.getElementById("complnomeusuario").innerHTML = Resp.nomeusuins;
                                    document.getElementById("numrelato").value = Resp.numrelato;
                                    document.getElementById("numrelatoCompl").innerHTML = "Complementando Relato: "+Resp.numrelato;
                                    document.getElementById('complocorrencia1').checked = true; // houve ocorr
                                    document.getElementById("complrelato").value = "";
//                                    document.getElementById("complrelatosubstit").value = "";
                                    document.getElementById("complrelatosubstit").value = Resp.substit;
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
                    if(document.getElementById("selectusuprox").value == ""){
                        let element = document.getElementById('selectusuprox');
                        element.classList.add('destacaBorda');
                        return false;
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
//                if(parseInt(document.getElementById("mudou").value) === 0){
//                    document.getElementById("relacmodalReg").style.display = "none";
//                    return false;
//                }
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
//                if(document.getElementById("selectusuprox").value === ""){
//                    let element = document.getElementById('selectusuprox');
//                    element.classList.add('destacaBorda');
//                    document.getElementById("selectusuprox").focus();
//                    $('#mensagem').fadeIn("slow");
//                    document.getElementById("mensagem").innerHTML = "Selecione o funcionário que vai assumir o serviços";
//                    $('#mensagem').fadeOut(5000);
//                    return false;
//                }

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
                    "&usuprox="+document.getElementById("selectusuprox").value+
                    "&ocor="+Ocor+
                    "&envia="+Envia+
                    "&jatem="+document.getElementById("jatem").value+
                    "&quantjatem="+document.getElementById("quantjatem").value+
                    "&numrelato="+encodeURIComponent(document.getElementById("numrelato").value)+
                    "&substit="+encodeURIComponent(document.getElementById("relatosubstit").value)+
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
                                    $("#faixaCentral").load("modulos/lro/relReg.php");
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
                    "&substit="+encodeURIComponent(document.getElementById("complrelatosubstit").value)+
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
                                    $("#faixaCentral").load("modulos/lro/relReg.php");
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
                                                title: 'Informação!',
                                                content: 'Este turno já foi lançado.',
                                                draggable: true,
                                                buttons: {
                                                    OK: function(){
//                                                        document.getElementById("relacmodalReg").style.display = "none";
                                                        document.getElementById("selecturno").value = "";
                                                    }
                                                }
                                            });
                                            return false;
//                                            $.confirm({
//                                                title: 'Confirmação!',
//                                                content: 'Parece que este turno já foi lançado. Confirma redigir outro registro?',
//                                                draggable: true,
//                                                buttons: {
//                                                    Sim: function () {
//                                                        //continua
//                                                    },
//                                                    Não: function () {
//                                                        document.getElementById("mudou").value = "0";
//                                                        document.getElementById("selecturno").value = "";
//                                                        document.getElementById("relacmodalReg").style.display = "none";
//                                                    }
//                                                }
//                                            });
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

            function mostraBotSalvar(){
                document.getElementById('botsalvaRelFisc').style.visibility = "visible";
                document.getElementById('botmarcaLido').style.visibility = "hidden";
//                if(document.getElementById("mostrarelatofiscal").value == ""){ // se apagar tudo
//                    document.getElementById('botsalvaRelFisc').style.visibility = "hidden";
//                    document.getElementById('botmarcaLido').style.visibility = "visible";
//                }
            }

            function salvaRelFisc(){
//                if(document.getElementById("mostrarelatofiscal").value == ""){
//                    $('#mostramensagemfisc').fadeIn("slow");
//                    document.getElementById("mostramensagemfisc").innerHTML = "Escreva o relato";
//                    $('#mostramensagemfisc').fadeOut(3000);
//                    return false;
//                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=salvaRegFiscal&codigo="+document.getElementById("guardacod").value
                    +"&relatofisc="+encodeURIComponent(document.getElementById("mostrarelatofiscal").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("relacMostramodalReg").style.display = "none";
                                    $("#faixaCentral").load("modulos/lro/relReg.php");
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function marcaLidoFisc(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=marcaLidoFiscal&codigo="+document.getElementById("guardacod").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("relacMostramodalReg").style.display = "none";
                                    $("#faixaCentral").load("modulos/lro/relReg.php");
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function insSubstit(){
                if(document.getElementById("selectsubstit").value != ""){
                    if(document.getElementById("relatosubstit").value == ""){
                        document.getElementById("relatosubstit").value = "Fui substituído por: "+document.getElementById("selectsubstit").value+" das      "+"às     ";
                    }else{
                        document.getElementById("relatosubstit").value = document.getElementById("relatosubstit").value+"\nFui substituído por: "+document.getElementById("selectsubstit").value+" das      "+"às     ";
                    }
                    document.getElementById("selectsubstit").value = ""; // para forçar o onchange
                }
            }
            function insComplSubstit(){
                if(document.getElementById("complselectsubstit").value != ""){
                    if(document.getElementById("complrelatosubstit").value == ""){
                        document.getElementById("complrelatosubstit").value = "Fui substituído por: "+document.getElementById("complselectsubstit").value+" das      "+"às     ";
                    }else{
                        document.getElementById("complrelatosubstit").value = document.getElementById("complrelatosubstit").value+"\nFui substituído por: "+document.getElementById("complselectsubstit").value+" das      "+"às     ";
                    }
                    document.getElementById("complselectsubstit").value = ""; // para forçar o onchange
                }
            }

            function incluiNome(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=incluirnome&nome="+encodeURIComponent(document.getElementById("formanomes").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    if(document.getElementById("relatosubstit").value == ""){
                                        document.getElementById("relatosubstit").value = "Fui substituído por: "+Resp.nome+" das      "+"às     ";
                                    }else{
                                        document.getElementById("relatosubstit").value = document.getElementById("relatosubstit").value+"\nFui substituído por: "+Resp.nome+" das      "+"às     ";
                                      }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function MarcaLista(obj, Cod){
                if(obj.checked === false){
                    return false;
                }
               $.confirm({
                    title: 'Confirmação!',
                    content: 'Quer inserir este item nas ocorrências para comentar?',
                    autoClose: 'Não|20000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/lro/salvaReg.php?acao=buscaitem&codigo="+Cod, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                document.getElementById('ocorrencia1').checked = true; // houve ocorr
                                                document.getElementById('relato').disabled = false;
                                                document.getElementById("mudou").value = "1";
                                                if(document.getElementById('relato').value == ""){ 
                                                    document.getElementById('relato').value = "item "+Resp.item+" - "+Resp.descr;
                                                }else{
                                                    document.getElementById('relato').value = document.getElementById('relato').value+"\n"+"item "+Resp.item+" - "+Resp.descr;
                                                }
                                                document.getElementById("relacCheckListReg").style.display = "none";
                                            }
                                        }
                                    }
                                };
                                ajax.send(null);
                            }
                        },
                        Não: function () {
                            obj.checked = false;
                        }
                    }
                });
            }

            function marcaConfig(obj, Campo){
                if(obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                if(document.getElementById("configSelecUsu").value == ""){
                    if(obj.checked === true){
                        obj.checked = false;
                    }else{
                        obj.checked = true;
                    }
                    $('#mensagemConfig').fadeIn("slow");
                    document.getElementById("mensagemConfig").innerHTML = "Selecione um usuário.";
                    $('#mensagemConfig').fadeOut(1000);
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=MarcaConfig&codigo="+document.getElementById("configSelecUsu").value
                    +"&campo="+Campo
                    +"&valor="+Valor
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    if(parseInt(Resp.coderro) === 2){
                                        obj.checked = true;
                                        $.confirm({
                                            title: 'Ação Suspensa!',
                                            content: 'Não restaria outro marcado para gerenciar as permissões.',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                        return false;
                                    }else{
                                        $('#mensagemConfig').fadeIn("slow");
                                        document.getElementById("mensagemConfig").innerHTML = "Valor salvo.";
                                        $('#mensagemConfig').fadeOut(1000);
                                    }
                                }

                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function abreLROconfig(){
                document.getElementById("checkefetivo").checked = false;
                document.getElementById("checkfiscal").checked = false;
                document.getElementById("checkrubrica").checked = false;
                document.getElementById("configCpfUsu").value = "";
                document.getElementById("configSelecUsu").value = "";
                document.getElementById("modalLROconfig").style.display = "block";
            }
            function fechaLROconfig(){
                document.getElementById("modalLROconfig").style.display = "none";
            }
            function resumoUsu(){
                window.open("modulos/lro/imprUsuLro.php?acao=listaUsuarios", "ImpraUsuLro");
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
            function abreCheckList(){
                document.getElementById("relacCheckList").style.display = "block";
            }
            function fechaCheckList(){
                document.getElementById("relacCheckList").style.display = "none";
            }
            
            function abreCheckReg(){
                document.getElementById("relacCheckListReg").style.display = "block";
            }
            function fechaCheckListReg(){
                document.getElementById("relacCheckListReg").style.display = "none";
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

            function abreimprLRO(){
                document.getElementById("relacimprLRO").style.display = "block";
            }
            function fechaImprLRO(){
                document.getElementById("relacimprLRO").style.display = "none";
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

            function format_CnpjCpf(value){
                //https://gist.github.com/davidalves1/3c98ef866bad4aba3987e7671e404c1e
                const CPF_LENGTH = 11;
                const cnpjCpf = value.replace(/\D/g, '');
                if (cnpjCpf.length === CPF_LENGTH) {
                    return cnpjCpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/g, "\$1.\$2.\$3-\$4");
                } 
                  return cnpjCpf.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/g, "\$1.\$2.\$3/\$4-\$5");
            }

        </script>
    </head>
    <body class="corClara" onbeforeunload="return mudaTema(0)"> <!-- ao sair retorna os background claros -->
        <?php
        date_default_timezone_set('America/Sao_Paulo');
        $Hoje = date('d/m/Y');
        if(!$Conec){
            echo "Sem contato com o PostGresql";
            return false;
        }
        $rs = pg_query($Conec, "SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE table_schema = 'cesb' And table_name = 'livroreg'");
        $row = pg_num_rows($rs);
        if($row == 0){
            $Erro = 1;
            echo "Faltam tabelas. Informe à ATI.";
            return false;
        }

        //Marca enviado após 13 horas de inserido
        $rs = pg_query($Conec, "UPDATE ".$xProj.".livroreg SET enviado = 1 WHERE datains < (NOW() - interval '13 hour') And enviado = 0"); // marca enviado após 13 horas de inserido - o turno 3 tem 12 horas

        $admIns = parAdm("insocor", $Conec, $xProj);   // nível para inserir 
        $admEdit = parAdm("editocor", $Conec, $xProj); // nível para editar

        $Lro = parEsc("lro", $Conec, $xProj, $_SESSION["usuarioID"]);
        $FiscLro = parEsc("fisclro", $Conec, $xProj, $_SESSION["usuarioID"]);
        $RevLro = parEsc("lro_rev", $Conec, $xProj, $_SESSION["usuarioID"]); // revisor do LRO
        $Tema = parEsc("tema", $Conec, $xProj, $_SESSION["usuarioID"]); // Claro(0) Escuro(1)

        $OpUsuAnt = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE lro = 1 And ativo = 1 And pessoas_id != ".$_SESSION["usuarioID"]." ORDER BY nomeusual, nomecompl"); // And codsetor = 
        $OpUsuAnt2 = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE lro = 1 And ativo = 1 And pessoas_id != ".$_SESSION["usuarioID"]." ORDER BY nomeusual, nomecompl"); 
        $OpUsuAnt3 = pg_query($Conec, "SELECT id, nomecolet FROM ".$xProj.".coletnomes WHERE ativo = 1 ORDER BY nomecolet");
        $OpUsuAnt4 = pg_query($Conec, "SELECT id, nomecolet FROM ".$xProj.".coletnomes WHERE ativo = 1 ORDER BY nomecolet");
        $OpTurnos = pg_query($Conec, "SELECT codturno, descturno FROM ".$xProj.".livroturnos WHERE descturno != '' ORDER BY codturno;"); 
        $OpTurnos2 = pg_query($Conec, "SELECT codturno, descturno FROM ".$xProj.".livroturnos WHERE descturno != '' ORDER BY codturno;"); 
        $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataocor, 'MM'), '/', TO_CHAR(dataocor, 'YYYY')) 
        FROM ".$xProj.".livroreg GROUP BY TO_CHAR(dataocor, 'MM'), TO_CHAR(dataocor, 'YYYY') ORDER BY TO_CHAR(dataocor, 'YYYY') DESC, TO_CHAR(dataocor, 'MM') DESC ");
        $OpcoesEscAno = pg_query($Conec, "SELECT EXTRACT(YEAR FROM ".$xProj.".livroreg.dataocor)::text 
        FROM ".$xProj.".livroreg GROUP BY 1 ORDER BY 1 DESC ");
        $OpUsuProx = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE lro = 1 And ativo = 1 And pessoas_id != ".$_SESSION["usuarioID"]." ORDER BY nomeusual, nomecompl");
        $OpConfig = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");

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
        <input type="hidden" id="revisorLRO" value="<?php echo $RevLro; ?>" /><!-- João -> dá o visto e anota as providências tomadas nas ocorrências -->

        <div id="tricoluna0" style="margin: 10px; border: 2px solid green; border-radius: 15px; padding: 20px; min-height: 70px;">
            <div id="tricoluna1" class="box" style="position: relative; float: left; width: 35%;">
                <img src="imagens/settings.png" height="20px;" id="imgLROConfig" style="cursor: pointer; padding-left: 20px;" onclick="abreLROconfig();" title="Configurar o acesso ao Livro de Registro de Ocorrêcias">
                <label style="padding-left: 20px;"></label>
                <input type="button" id="botinserir" class="botpadr" value="Inserir Registro" onclick="InsRegistro();">
            </div>
            <div id="tricoluna2" class="box" style="position: relative; float: left; width: 28%; text-align: center;">
                <h6>Livro de Registro de Ocorrências</h6>
            </div>
            <div id="tricoluna3" class="box" style="position: relative; float: left; width: 35%; text-align: right;">
                <div id="selectTema" style="position: relative; float: left; padding-left: 8px;">
                    <label id="etiqcorFundo" class="etiq" style="color: #6C7AB3; font-size: 80%; padding-left: 5px;">Tema: </label>
                    <input type="radio" name="corFundo" id="corFundo0" value="0" <?php if($Tema == 0){echo 'CHECKED';}; ?> title="Tema claro" onclick="mudaTema(0);" style="cursor: pointer;"><label for="corFundo0" class="etiq" style="cursor: pointer;">&nbsp;Claro</label>
                    <input type="radio" name="corFundo" id="corFundo1" value="1" <?php if($Tema == 1){echo 'CHECKED';}; ?> title="Tema escuro" onclick="mudaTema(1);" style="cursor: pointer;"><label for="corFundo1" class="etiq" style="cursor: pointer;">&nbsp;Escuro</label>
                    <label style="padding-left: 20px;"></label>
                </div>
                <img src="imagens/checkVerde.png" height="20px;" style="cursor: pointer;" onclick="abreCheckList();" title="Lista de Verificação (checklist)">
                <label style="padding-left: 20px;"></label>
                <button class="botpadrred" style="font-size: 80%;" id="botimprLRO" onclick="abreimprLRO();">Gerar PDF</button>
                <label style="padding-left: 20px;"></label>
                <img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpLRO();" title="Guia rápido">
            </div>
            <div id="faixaCentral"></div>
        </div>
        <div id="carregaTema"></div> <!-- carrega a pág modulos/config/carTema.php - onde está a função mudaTema() -->

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
                                <?php 
                                if($OpTurnos){
                                    while ($Opcoes = pg_fetch_row($OpTurnos)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                    <?php 
                                    }
                                }
                            ?>
                        </select>
                        <br>
                        <label class="etiqAzul">Titular em serviço: </label><label id="nomeusuario" style="color: black; font-size: 1.2rem; padding: 5px;"><?php echo $_SESSION["NomeCompl"]; ?></label>
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
                            
                            <label style="padding-left: 10px;"></label>
                            <img src="imagens/checkVerde.png" height="15px;" style="cursor: pointer;" onclick="abreCheckReg();" title="Lista de Verificação (checklist)">

                            <br>
                            <label class="etiqAzul" title="Marcar se houve ou não houve ocorrência digna de nota">Ocorrências: </label>
                            <input type="radio" name="ocorrencia" id="ocorrencia1" value="1" title="Houve algo que precisa ser relatado" onclick="abreOcor(value);"><label for="ocorrencia1" class="etiqAzul" style="padding-left: 3px;"> Houve</label>
                            <input type="radio" name="ocorrencia" id="ocorrencia2" value="0" CHECKED title="Nada aconteceu que seja digno de nota" onclick="abreOcor(value);"><label for="ocorrencia2" class="etiqAzul" style="padding-left: 3px;"> Não Houve</label>

                            <br>
                            <div class="col-xs-6">
                                <textarea class="form-control" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px;" rows="6" cols="90" id="relato" onclick="tiraBorda(id);" onchange="modif();"></textarea>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 10px; margin-bottom: 10px;">
                        <label class="etiqAzul"> - Passagem do serviço para: </label>
                        <select id="selectusuprox" style="min-width: 120px;" onclick="tiraBorda(id);" onchange="modif();" title="Selecione um usuário.">
                            <option value=""></option>
                            <?php 
                            if($OpUsuProx){
                                while ($Opcoes = pg_fetch_row($OpUsuProx)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php if(!is_null($Opcoes[2]) && $Opcoes[2] != ""){ echo $Opcoes[2]." - ".$Opcoes[1];}else{echo $Opcoes[1];} ?></option>
                                <?php 
                                }
                            }
                            ?>
                        </select>
                        <label class="etiqAzul" style="padding-right: 280px;"></label>
                    </div>
                    <hr>

                    <div style="text-align: left;">
                        <label class="etiqAzul"> - Substituições temporárias: </label>
                        <select id="selectsubstit" style="width: 25px;" onchange="insSubstit();" title="Selecione um nome.">
                            <option value=""></option>
                            <?php 
                            if($OpUsuAnt3){
                                while ($Opcoes = pg_fetch_row($OpUsuAnt3)){ ?>
                                    <option value="<?php echo $Opcoes[1]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                        </select>
                        <input type="text" id="formanomes" value="" style="font-size: 80%;" onchange="incluiNome();"/> 
                        <label class="etiqAzul"> <- Insira o nome do substituto aqui para formar um arquivo de nomes</label>
                    </div>
                    <div class="col-xs-6" style="text-align: center; padding-bottom: 10px;">
                        <textarea class="form-control" id="relatosubstit" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px;" rows="4" cols="80" onchange="modif();"></textarea>
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
                        <label class="etiqAzul">Registrado por: </label><label id="mostranomeusuario" style="color: black; font-size: 1.2rem; padding-left: 3px;"></label>
                        <br>
                        <div style="text-align: center;">
                            <label class="etiqAzul"> - Recebi o serviço de: </label>
                            <input disabled type="text" id="mostraselectusuant" value="" style="width: 300px;">

                            <label class="etiqAzul"> ciente das alterações dos turnos anteriores. </label>
                            <br>
                            <label class="etiqAzul" title="Marcar se houve ou não houve ocorrência digna de nota">Ocorrências: </label>
                            <input disabled type="radio" name="mostraocorrencia" id="mostraocorrencia1" value="1" title="Houve algo que precisa ser relatado"><label for="mostraocorrencia1" class="etiqAzul" style="padding-left: 3px;"> Houve</label>
                            <input disabled type="radio" name="mostraocorrencia" id="mostraocorrencia2" value="0" CHECKED title="Nada aconteceu que seja digno de nota"><label for="mostraocorrencia2" class="etiqAzul" style="padding-left: 3px;"> Não Houve</label>
                            <br>
                            <div class="col-xs-6">
                                <textarea class="form-control" disabled id="mostrarelato" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px;" rows="7" cols="90"></textarea>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top: 10px; margin-bottom: 10px;">
                        <label class="etiqAzul"> - Passei o serviço para: </label>
                        <input disabled type="text" id="mostraselectusuprox" value="" style="width: 300px;">
                        <label class="etiqAzul" style="padding-right: 270px;"></label>
                    </div>
                    <hr>
                    <div class="aEsq"><label class="etiqAzul"> - Substituições temporárias: </label></div>
                    <div class="col-xs-6" style="text-align: center; padding-bottom: 10px;">
                        <textarea class="form-control" disabled id="mostrarelatosubstit" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px;" rows="4" cols="80" ></textarea>
                    </div>
                    <div id="mostramensagem" style="color: red; font-weight: bold;"></div>
                    <div style="text-align: center; padding-top: 10px;">
                        <button id="botedit" class="botpadrblue" onclick="carregaModal();">Editar</button>
                    </div>

                    <div id="etiqmostrarelatofiscal" class="aEsq">
                        <label id="NumRegistro" class="etiqAzul"></label>
                        <label class="etiqAzul" style="padding-right: 20px;"> - Considerações da Administração: </label>
                        
                        <img id="imgmarcaLido" src="imagens/ok.png" height="15px;" title="Visto OK">
                        <button id="botsalvaRelFisc" class="botpadrblue" style="font-size: 70%; padding: 2px;" onclick="salvaRelFisc();" title="Salva e marca como lido.">Salvar</button>
                        <label class="etiqAzul" style="padding-left: 20px;">
                        <button id="botmarcaLido" class="botpadrblue" style="font-size: 70%; padding: 2px;" onclick="marcaLidoFisc();" title="Só marca como lido.">Marcar Lido</button>
                    </div>
                    <div class="col-xs-6" style="text-align: center; padding-bottom: 10px;">
                        <textarea id="mostrarelatofiscal" class="form-control" disabled onkeydown="mostraBotSalvar();" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px;" rows="4" cols="60" ></textarea>
                    </div>
                    <div id="mostramensagemfisc" style="color: red; font-weight: bold;"></div>
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
                                <?php 
                                if($OpTurnos2){
                                    while ($Opcoes = pg_fetch_row($OpTurnos2)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                    <?php 
                                    }
                                }
                            ?>
                        </select>
                        <br>
                        <label class="etiqAzul">Titular em serviço: </label><label id="complnomeusuario" style="color: black; font-size: 1.2rem; padding: 5px;"><?php echo $_SESSION["NomeCompl"]; ?></label>
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
                            <div class="col-xs-6">
                                <textarea class="form-control" id="complrelato" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px;" rows="7" cols="90" onclick="tiraBorda(id);" onchange="modif();"></textarea>
                            </div>
                        </div>
                        <br>
                    </div>
                    <div style="margin-top: 10px; margin-bottom: 10px;">
                        <label class="etiqAzul"> - Passei o serviço para: </label>
                        <input disabled type="text" id="complselectusuprox" value="" style="width: 300px;">
                        <label class="etiqAzul" style="padding-right: 270px;"></label>
                    </div>
                    <hr>
                    <div style="text-align: left;">
                        <label class="etiqAzul"> - Substituições temporárias: </label>
                        <select id="complselectsubstit" style="width: 25px;" onchange="insComplSubstit();" title="Selecione um nome.">
                            <option value=""></option>
                            <?php 
                            if($OpUsuAnt4){
                                while ($Opcoes = pg_fetch_row($OpUsuAnt4)){ ?>
                                    <option value="<?php echo $Opcoes[1]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                        </select> 
                    </div>
                    <div class="col-xs-6" style="text-align: center; padding-bottom: 10px;">
                        <textarea class="form-control" id="complrelatosubstit" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px;" rows="4" cols="80" onchange="modif();"></textarea>
                    </div>

                    <div id="mensagemcompl2" style="color: red; font-weight: bold;"></div>
                    <div style="text-align: center; padding-bottom: 10px;">
                        <button class="botpadrblue" onclick="salvaModalCompl(0);">Salvar</button>
                    </div>
                </div>
           </div>
        </div> <!-- Fim Modal-->


        <!-- div modal para checklist -->
        <div id="relacCheckList" class="relacmodal">
            <div class="checkList-content">
                <span class="close" onclick="fechaCheckList();">&times;</span>
                <h4 style="text-align: center; color: #666;">Lista de Verificação</h4>
                <h5 style="text-align: center; color: #666;">Passagem de Serviço</h5>
                <div style="color: #000000; border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px;">
                    <div id="listaCheckList"></div>  <!-- checkListLRO.php -->
                </div>
            </div>
        </div>  <!-- Fim Modal checklist-->

        <!-- div modal para checklist no registro -->
        <div id="relacCheckListReg" class="relacmodal">
            <div class="checkList-content">
                <span class="close" onclick="fechaCheckListReg();">&times;</span>
                <h4 style="text-align: center; color: #666;">Lista de Verificação</h4>
                <div style="text-align: center; color: #666;">Auxiliar na composição do relato</div>
                <div style="color: #000000; border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px;">
                    <div id="listaCheckReg"></div>  <!-- checkListLRO.php -->
                </div>
            </div>
        </div>  <!-- Fim Modal checklist-->


        <!-- Modal configuração-->
        <div id="modalLROconfig" class="relacmodal">
            <div class="config-content">
                <span class="close" onclick="fechaLROconfig();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div style="width: 25%;"></div>
                        <div style="width: 48%;"><h5 style="text-align: center; color: #666;">Livro de Registro de Ocorrências</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div style="width: 25%; text-align: center;"><button class="botpadrred" style="font-size: 70%;" onclick="resumoUsu();" title="Mostra uma relação dos usuários com acesso autorizado">Resumo em PDF</button></div> 
                    </div>
                </div>
                <label class="etiqAzul">Selecione um usuário para ver a configuração:</label>
                <table style="margin: 0 auto; width: 95%;">
                    <tr>
                        <td colspan="4" style="text-align: center;"></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="etiqNorm eItalic" style="text-align: center;">Busca Nome ou CPF do Usuário</td>
                    </tr>
                    <tr>
                        <td class="etiqAzul">Procura nome: </td>
                        <td style="width: 100px;">
                            <select id="configSelecUsu" style="max-width: 270px;" onchange="modif();" title="Selecione um usuário.">
                                <option value=""></option>
                                <?php 
                                if($OpConfig){
                                    while ($Opcoes = pg_fetch_row($OpConfig)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td class="etiqAzul"><label class="etiqAzul">ou CPF:</label></td>
                        <td>
                            <input type="text" id="configCpfUsu" placeholder="CPF" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('configSelecUsu');return false;}" title="Procura por CPF. Digite o CPF."/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                    </tr>
                </table>

                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td class="etiqAzul eItalic">Preenchar o LRO:</td>
                        <td colspan="4">
                            <input type="checkbox" id="checkefetivo" onchange="marcaConfig(this, 'lro');" >
                            <label for="checkefetivo" class="etiqNorm eItalic">preencher o Livro de Registro de Ocorrências</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiqAzul eItalic">Administrar o LRO:</td>
                        <td colspan="4">
                            <input type="checkbox" id="checkfiscal" onchange="marcaConfig(this, 'fisclro');" >
                            <label for="checkfiscal" class="etiqNorm eItalic"> fiscalizar o Livro de Registro de Ocorrências</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiqAzul eItalic"><Label id="etiqrubrica" style="color: #006400;" title="Esta opção só aparece aqui. É restrita aos encarregados de rubricar o LRO.">Rubricar o LRO:</label></td>
                        <td colspan="4">
                            <input type="checkbox" id="checkrubrica" onchange="marcaConfig(this, 'lro_rev');" title="Esta opção só aparece aqui. É restrita aos encarregados de rubricar o LRO." >
                            <label id="etiqcheckrubrica" for="checkrubrica" class="etiqNorm eItalic" style="color: #006400;" title="Esta opção só aparece aqui. É restrita aos encarregados de rubricar o LRO."> rubricar diariamente o Livro de Registro de Ocorrências</label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"><hr style="margin: 0; padding: 2px;"></td>
                    </tr>

                    <tr>
                        <td colspan="5" style="text-align: center;"><label id="mensagemConfig" style="color: red; font-weight: bold;"></label></td>
                    </tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para imprimir em pdf  -->
        <div id="relacimprLRO" class="relacmodal">
            <div class="modal-content-imprLRO">
                <span class="close" onclick="fechaImprLRO();">&times;</span>
                <h5 id="titulomodal" style="text-align: center;color: #666;">Livro de Registro de Ocorrências</h5>
                <h6 id="titulomodal" style="text-align: center; padding-bottom: 18px; color: #666;">Impressão PDF</h6>
                <div style="color: #000000; border: 2px solid #C6E2FF; border-radius: 10px;">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td style="text-align: right;"><label style="font-size: 80%;">Mensal - Selecione o Mês/Ano: </label></td>
                            <td>
                                <select id="selecMesAno" style="font-size: 1rem; width: 90px;" title="Selecione o período.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpcoesEscMes){
                                        while ($Opcoes = pg_fetch_row($OpcoesEscMes)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: right;"><label style="font-size: 80%;">Anual - Selecione o Ano: </label></td>
                            <td>
                                <select id="selecAno" style="font-size: 1rem; width: 90px;" title="Selecione o Ano.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpcoesEscAno){
                                        while ($Opcoes = pg_fetch_row($OpcoesEscAno)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <div style="padding-bottom: 20px;"></div>
           </div>
        </div>


        <!-- div modal para instruções -->
        <div id="relacHelpLRO" class="relacmodal">
            <div class="modalMsg-content">
                <span class="close" onclick="fechaHelpLRO();">&times;</span>
                <h4 style="text-align: center; color: #666;">Informações</h4>
                <h5 style="text-align: center; color: #666;">Livro de Registro de Ocorrências</h5>
                <div style="color: #000000; border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px;">
                    Regras inseridas:
                    <ul>
                        <li>1 - O Livro de Registro de Ocorrências (LRO) destina-se a registrar os acontecimentos dignos de registro durante os turnos de serviço na portaria.</li>
                        <li>2 - O funcionário designado para inserir o registro deve anotar ao final do turno (botão Inserir Registro) tudo o que ocorreu durante seu serviço.</li>
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