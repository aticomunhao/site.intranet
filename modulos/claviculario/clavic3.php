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
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-ui.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="comp/js/jquery-ui.min.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script> <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="comp/js/jquery.mask.js"></script>
        <link rel="stylesheet" type="text/css" media="screen" href="class/gijgo/css/gijgo.css" />
        <script src="class/gijgo/js/gijgo.js"></script>
        <script src="class/gijgo/js/messages/messages.pt-br.js"></script>

        <style>
            .modal-content-ChavesControle{
                background: linear-gradient(180deg, white, #CFCFCF);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%; /* acertar de acordo com a tela */
                max-width: 900px;
            }
            .modal-content-Chaves{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 15% auto; 
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%;
                max-width: 900px;
            }
            .modal-content-imprChaves3{
                background: linear-gradient(180deg, white, #CFCFCF);
                margin: 12% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 40%;
            }
            .modal-content-relacChave3{
                background: linear-gradient(180deg, white, #CFCFCF);
                margin: 12% auto;
                padding: 10px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 40%;
            }
            .quadrinho {
                font-size: 90%;
                min-height: 23px;
                border: 1px solid #C0C0C0;
                border-radius: 3px;
                padding-left: 5px;
                padding-right: 5px;
                cursor: default;
            }
            .quadrinhoClick {
                font-size: 90%;
                min-width: 50px;
                border: 1px solid;
                border-radius: 3px;
                padding-left: 5px;
                padding-right: 5px;
                cursor: pointer;
                background: #CFCFCF;
                color: black;
            }
            .quadrgrupo {
                font-size: 90%;
                min-width: 33px;
                border: 1px solid #C0C0C0;
                border-radius: 3px;
                padding-left: 5px;
                padding-right: 5px;
            }
            .quadrlista {
                position: relative;
                float: left;
                margin-left: 2px;
                font-size: 80%;
                min-height: 23px;
                border: 1px solid #C0C0C0;
                border-radius: 3px;
                padding-left: 2px;
                padding-right: 2px;
                text-align: left;
            }
            .quadrnomelista {
                margin-left: 2px;
                font-size: 80%;
                min-height: 23px;
                border: 1px solid #C0C0C0;
                border-radius: 3px;
                padding-left: 2px;
                padding-right: 2px;
                text-align: left;
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
                document.getElementById("botinserir").style.visibility = "hidden";
                document.getElementById("botimpr").style.visibility = "hidden";
                document.getElementById("botagenda1").style.visibility = "hidden";
                document.getElementById("botagenda2").style.visibility = "hidden";
                document.getElementById("imgChavesconfig").style.visibility = "hidden";
                document.getElementById("botaoChaves").style.visibility = "hidden"; // vincular chaves ao usuário

                if(parseInt(document.getElementById("guardaEditaChaves").value) === 1 || parseInt(document.getElementById("guardaEntregaChaves").value) === 1 || parseInt(document.getElementById("guardaFiscChaves").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){
                    $("#faixacentral").load("modulos/claviculario/jChave3.php?acao=todos");
                    $("#faixamostra").load("modulos/claviculario/kChave3.php?acao=todos");
                    $("#faixaagenda").load("modulos/claviculario/agChave3.php?acao=todos");
                    if(parseInt(document.getElementById("guardaEditaChaves").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){ 
                        document.getElementById("botinserir").style.visibility = "visible";
                        document.getElementById("botimpr").style.visibility = "visible";
                        document.getElementById("botagenda1").style.visibility = "visible";
                        document.getElementById("botagenda2").style.visibility = "visible";
                        document.getElementById("imgChavesconfig").style.visibility = "visible";
                    }
                }else{
                    document.getElementById("faixaMensagem").style.display = "block";
                }
                if(parseInt(document.getElementById("guardaFiscChaves").value) === 1){
                    document.getElementById("botimpr").style.visibility = "visible";
                }

                $('#carregaTema').load('modulos/config/carTema.php?carpag=clavic3');

                $("#cpfsolicitante").mask("999.999.999-99");
                $("#cpfentregador").mask("999.999.999-99");
                $("#agendacpfsolicitante").mask("999.999.999-99");
                $("#configcpfsolicitante").mask("999.999.999-99");
                $("#resulttelef").mask("(99) 9 9999-9999");
                $("#voltatelef").mask("(99) 9 9999-9999");
                $("#agendatelef").mask("(99) 9 9999-9999");
                $("#agendadata").mask("99/99/9999");
                $('#agendadata').datepicker({ uiLibrary: 'bootstrap5', locale: 'pt-br', format: 'dd/mm/yyyy' });

                $("#selecSolicitante").change(function(){
                    document.getElementById("cpfsolicitante").value = "";
                    document.getElementById("guardaCPF").value = "";
                    document.getElementById("mensagemErro").style.display = "none";
                    document.getElementById("guardaPosCod").value = document.getElementById("selecSolicitante").value;
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=buscalog&codigo="+document.getElementById("selecSolicitante").value+"&codChave="+document.getElementById("guardaCod").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("resultsolicitante").innerHTML = Resp.nomecompl;
                                        document.getElementById("guardaCPF").value = Resp.cpf;
                                        document.getElementById("resultcpf").innerHTML = format_CnpjCpf(Resp.cpf);
                                        document.getElementById("cpfsolicitante").value = format_CnpjCpf(Resp.cpf);
                                        document.getElementById("resultsetor").innerHTML = Resp.siglasetor;
                                        document.getElementById("resulttelef").value = Resp.telef;
                                        if(parseInt(Resp.chaveautorizada) === 0){
                                            document.getElementById("msgInfo2").innerHTML = "Usuário não vinculado a essa chave";
                                            document.getElementById("msgInfo3").innerHTML = "O vínculo é efetuado pela DAF.";
                                            document.getElementById("modalInfo").style.display = "block";
                                        }
                                    }else{
                                        document.getElementById("selecSolicitante").value = "";
                                        document.getElementById("resultsolicitante").innerHTML = "";
                                        document.getElementById("guardaCPF").value = "";
                                        document.getElementById("resultcpf").innerHTML = "";
                                        document.getElementById("cpfsolicitante").value = "";
                                        document.getElementById("resultsetor").innerHTML = "";
                                        document.getElementById("resulttelef").value = "";
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#cpfsolicitante").click(function(){
                    document.getElementById("selecSolicitante").value = "";
                    document.getElementById("cpfsolicitante").value = "";
                    document.getElementById("resultsolicitante").innerHTML = "";
                    document.getElementById("guardaCPF").value = "";
                    document.getElementById("resultcpf").innerHTML = "";
                    document.getElementById("resultsetor").innerHTML = "";
                    document.getElementById("resulttelef").value = "";
                    document.getElementById("guardaPosCod").value = "";
                    document.getElementById("mensagemErro").style.display = "none";
                });
                $("#cpfsolicitante").change(function(){
                    if(document.getElementById("cpfsolicitante").value == ""){
                        return false;
                    }
                    if(!validaCPF(document.getElementById("cpfsolicitante").value)){
                        document.getElementById("mensagemErro").innerHTML = "Verifique o CPF digitado.";
                        document.getElementById("mensagemErro").style.display = "block";
                        return false;
                    }
                    document.getElementById("selecSolicitante").value = "";
                    document.getElementById("guardaCPF").value = "";
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=buscacpf&cpf="+encodeURIComponent(document.getElementById("cpfsolicitante").value)+"&codChave="+document.getElementById("guardaCod").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("resultsolicitante").innerHTML = Resp.nomecompl;
                                        document.getElementById("guardaCPF").value = Resp.cpf;
                                        document.getElementById("resultcpf").innerHTML = format_CnpjCpf(Resp.cpf);
                                        document.getElementById("resultsetor").innerHTML = Resp.siglasetor;
                                        document.getElementById("guardaPosCod").value = Resp.PosCod;
                                        document.getElementById("selecSolicitante").value = Resp.PosCod;
                                        if(parseInt(Resp.chaveautorizada) === 0){
                                            document.getElementById("msgInfo2").innerHTML = "Usuário não vinculado a essa chave";
                                            document.getElementById("msgInfo3").innerHTML = "O vínculo é efetuado pela DAF.";
                                            document.getElementById("modalInfo").style.display = "block";
                                        }
                                    }
                                    if(parseInt(Resp.coderro) === 3){
                                        document.getElementById("resultsolicitante").innerHTML = "Usuário não autorizado.";
                                        document.getElementById("cpfsolicitante").focus();
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("resultsolicitante").innerHTML = "Nada foi encontrado.";
                                        document.getElementById("cpfsolicitante").focus();
                                    }
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#selecEntregador").change(function(){
                    document.getElementById("cpfentregador").value = "";
                    document.getElementById("guardaCPF").value = "";
                    document.getElementById("guardaPosCod").value = document.getElementById("selecEntregador").value;
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=buscalog&codigo="+document.getElementById("selecEntregador").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("voltasolicitante").value = Resp.nomecompl;
                                        document.getElementById("guardaCPF").value = Resp.cpf;
                                        document.getElementById("voltacpf").innerHTML = format_CnpjCpf(Resp.cpf);
                                        document.getElementById("cpfentregador").value = format_CnpjCpf(Resp.cpf);
                                        document.getElementById("voltasetor").innerHTML = Resp.siglasetor;
                                        document.getElementById("voltatelef").value = Resp.telef;
                                    }else{
                                        document.getElementById("selecEntregador").value = "";
                                        document.getElementById("guardaPosCod").value = "";
                                        document.getElementById("voltasolicitante").value = "";
                                        document.getElementById("guardaCPF").value = "";
                                        document.getElementById("voltacpf").innerHTML = "";
                                        document.getElementById("voltasetor").innerHTML = "";
                                        document.getElementById("voltatelef").value = "";    
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });
                $("#cpfentregador").click(function(){
                    document.getElementById("voltasolicitante").value = "";
                    document.getElementById("selecEntregador").value = "";
                    document.getElementById("cpfentregador").value = "";
                    document.getElementById("guardaCPF").value = "";
                    document.getElementById("voltacpf").innerHTML = "";
                    document.getElementById("voltasetor").innerHTML = "";
                    document.getElementById("voltatelef").value = "";
                    document.getElementById("guardaPosCod").value = "";
                    document.getElementById("voltasolicitante").disabled = false;
                });
                $("#cpfentregador").change(function(){
                    document.getElementById("selecEntregador").value = "";
                    document.getElementById("guardaCPF").value = "";
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=buscacpf&cpf="+encodeURIComponent(document.getElementById("cpfentregador").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("voltasolicitante").value = Resp.nomecompl;
                                        document.getElementById("guardaCPF").value = Resp.cpf;
                                        document.getElementById("voltacpf").innerHTML = format_CnpjCpf(Resp.cpf);
                                        document.getElementById("selecEntregador").value = Resp.PosCod;
                                        document.getElementById("voltasetor").innerHTML = Resp.siglasetor;
                                        document.getElementById("voltatelef").value = Resp.telef;
                                        document.getElementById("guardaPosCod").value = Resp.PosCod;
                                    }
                                    if(parseInt(Resp.coderro) === 3){
                                        document.getElementById("voltasolicitante").value = "Usuário não está autorizado a retirar chaves.";
                                        document.getElementById("guardaCPF").value = "";
                                        document.getElementById("voltasetor").innerHTML = "";
                                        document.getElementById("voltatelef").value = "";
                                        document.getElementById("guardaPosCod").value = ""
                                        document.getElementById("cpfentregador").focus();
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("voltasolicitante").value = "Nada foi encontrado";
                                        document.getElementById("guardaCPF").value = "";
                                        document.getElementById("voltasetor").innerHTML = "";
                                        document.getElementById("voltatelef").value = "";
                                        document.getElementById("guardaPosCod").value = ""
                                        document.getElementById("cpfentregador").focus();
                                    }
                                    if(parseInt(Resp.coderro) === 1){
                                        document.getElementById("selecEntregador").value = "";
                                        document.getElementById("guardaPosCod").value = "";
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#agendaselecSolicitante").change(function(){
                    document.getElementById("agendacpfsolicitante").value = "";
                    document.getElementById("guardaCPF").value = "";
                    document.getElementById("agendamensagemErro").style.display = "none";
                    document.getElementById("guardaPosCod").value = document.getElementById("agendaselecSolicitante").value;
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=buscalog&codigo="+document.getElementById("agendaselecSolicitante").value+"&codChave="+document.getElementById("guardaCod").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("agendasolicitante").innerHTML = Resp.nomecompl;
                                        document.getElementById("guardaCPF").value = Resp.cpf;
                                        document.getElementById("agendacpf").innerHTML = format_CnpjCpf(Resp.cpf);
                                        document.getElementById("agendacpfsolicitante").value = format_CnpjCpf(Resp.cpf);
                                        document.getElementById("agendasetor").innerHTML = Resp.siglasetor;
                                        document.getElementById("agendatelef").value = Resp.telef;
                                        if(parseInt(Resp.chaveautorizada) === 0){
                                            document.getElementById("msgInfo2").innerHTML = "Usuário não vinculado a essa chave";
                                            document.getElementById("msgInfo3").innerHTML = "O vínculo é efetuado pela DAF.";
                                            document.getElementById("modalInfo").style.display = "block";
                                        }
                                    }else{
                                        document.getElementById("agendaselecSolicitante").value = "";
                                        document.getElementById("guardaPosCod").value = "";
                                        document.getElementById("agendasolicitante").innerHTML = "";
                                        document.getElementById("guardaCPF").value = "";
                                        document.getElementById("agendacpf").innerHTML = "";
                                        document.getElementById("agendacpfsolicitante").value = "";
                                        document.getElementById("agendasetor").innerHTML = "";
                                        document.getElementById("agendatelef").value = "";
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#agendacpfsolicitante").click(function(){
                    document.getElementById("agendaselecSolicitante").value = "";
                    document.getElementById("agendacpfsolicitante").value = "";
                    document.getElementById("agendasolicitante").innerHTML = "";
                    document.getElementById("guardaCPF").value = "";
                    document.getElementById("agendacpf").innerHTML = "";
                    document.getElementById("agendasetor").innerHTML = "";
                    document.getElementById("agendatelef").value = "";
                    document.getElementById("guardaPosCod").value = "";
                    document.getElementById("agendamensagemErro").style.display = "none";
                });
                $("#agendacpfsolicitante").change(function(){
                    if(!validaCPF(document.getElementById("agendacpfsolicitante").value)){
                        document.getElementById("agendamensagemErro").innerHTML = "Verifique o CPF digitado.";
                        document.getElementById("agendamensagemErro").style.display = "block";
                        return false;
                    }
                    document.getElementById("agendaselecSolicitante").value = "";
                    document.getElementById("guardaCPF").value = "";
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=buscacpf&cpf="+encodeURIComponent(document.getElementById("agendacpfsolicitante").value)+"&codChave="+document.getElementById("guardaCod").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("agendasolicitante").innerHTML = Resp.nomecompl;
                                        document.getElementById("guardaCPF").value = Resp.cpf;
                                        document.getElementById("agendacpf").innerHTML = format_CnpjCpf(Resp.cpf);
                                        document.getElementById("agendasetor").innerHTML = Resp.siglasetor;
                                        document.getElementById("agendatelef").value = Resp.telef;
                                        document.getElementById("guardaPosCod").value = Resp.PosCod;
                                        document.getElementById("agendaselecSolicitante").value = Resp.PosCod;
                                        if(parseInt(Resp.chaveautorizada) === 0){
                                            document.getElementById("msgInfo2").innerHTML = "Usuário não vinculado a essa chave";
                                            document.getElementById("msgInfo3").innerHTML = "O vínculo é efetuado pela DAF.";
                                            document.getElementById("modalInfo").style.display = "block";
                                        }
                                    }
                                    if(parseInt(Resp.coderro) === 3){
//                                        document.getElementById("agendasolicitante").innerHTML = "Usuário não está autorizado a retirar chaves.";
                                        document.getElementById("agendamensagemErro").innerHTML = "Usuário não autorizado.";
                                        document.getElementById("agendamensagemErro").style.display = "block";
                                        document.getElementById("agendacpfsolicitante").focus();
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("agendasolicitante").innerHTML = "Nada foi encontrado.";
                                        document.getElementById("agendacpfsolicitante").focus();
                                    }
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#configselecSolicitante").change(function(){
                    if(document.getElementById("configselecSolicitante").value == ""){
                        document.getElementById("configcpfsolicitante").value = "";
                        document.getElementById("registroChaves").checked = false;
                        document.getElementById("retiraChave").checked = false;
                        document.getElementById("fiscalChaves").checked = false;
                        document.getElementById("editChaves").checked = false;
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=buscausuario&codigo="+document.getElementById("configselecSolicitante").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configcpfsolicitante").value = format_CnpjCpf(Resp.cpf);
                                        if(parseInt(Resp.claviculario) === 1){
                                            document.getElementById("registroChaves").checked = true;
                                        }else{
                                            document.getElementById("registroChaves").checked = false;
                                        }
                                        if(parseInt(Resp.pegachave) === 1){
                                            document.getElementById("retiraChave").checked = true;
                                            document.getElementById("nomeUsuChaves").innerHTML = "Definir chaves autorizadas para: "+Resp.nomeusual; // para a escolha de chaves
//                                            if(parseInt(document.getElementById("guardaEscolheChaves").value) === 1){ // escolha ativada - Campo esc_chaves1=1 em paramsis
                                                document.getElementById("botaoChaves").style.visibility = "visible";
//                                            }else{
//                                                document.getElementById("botaoChaves").style.visibility = "hidden";
//                                            }
                                        }else{
                                            document.getElementById("retiraChave").checked = false;
                                            document.getElementById("botaoChaves").style.visibility = "hidden";
                                        }
                                        if(parseInt(Resp.fiscchaves) === 1){
                                            document.getElementById("fiscalChaves").checked = true;
                                        }else{
                                            document.getElementById("fiscalChaves").checked = false;
                                        }
                                        if(parseInt(Resp.editachave) === 1){
                                            document.getElementById("editChaves").checked = true;
                                        }else{
                                            document.getElementById("editChaves").checked = false;
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

                $("#configcpfsolicitante").click(function(){
                    document.getElementById("configselecSolicitante").value = "";
                    document.getElementById("configcpfsolicitante").value = "";
                    document.getElementById("registroChaves").checked = false;
                    document.getElementById("retiraChave").checked = false;
                    document.getElementById("fiscalChaves").checked = false;
                    document.getElementById("editChaves").checked = false;
                    document.getElementById("botaoChaves").style.visibility = "hidden";
                });
                $("#configcpfsolicitante").change(function(){
                    document.getElementById("configselecSolicitante").value = "";
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=buscacpfusuario&cpf="+encodeURIComponent(document.getElementById("configcpfsolicitante").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configselecSolicitante").value = Resp.PosCod;
                                        if(parseInt(Resp.claviculario) === 1){
                                            document.getElementById("registroChaves").checked = true;
                                        }else{
                                            document.getElementById("registroChaves").checked = false;
                                        }
                                        if(parseInt(Resp.pegachave) === 1){
                                            document.getElementById("retiraChave").checked = true;
                                            document.getElementById("nomeUsuChaves").innerHTML = "Definir chaves autorizadas para: "+Resp.nomeusual; // para a escolha de chaves
//                                            if(parseInt(document.getElementById("guardaEscolheChaves").value) === 1){ // escolha ativada - Campo esc_chaves1=1 em paramsis
                                                document.getElementById("botaoChaves").style.visibility = "visible";
//                                            }else{
//                                                document.getElementById("botaoChaves").style.visibility = "hidden";
//                                            }
                                        }else{
                                            document.getElementById("retiraChave").checked = false;
                                            document.getElementById("botaoChaves").style.visibility = "hidden";
                                        }
                                        if(parseInt(Resp.fiscchaves) === 1){
                                            document.getElementById("fiscalChaves").checked = true;
                                        }else{
                                            document.getElementById("fiscalChaves").checked = false;
                                        }
                                        if(parseInt(Resp.editachave) === 1){
                                            document.getElementById("editChaves").checked = true;
                                        }else{
                                            document.getElementById("editChaves").checked = false;
                                        }
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("registroChaves").checked = false;
                                        document.getElementById("retiraChave").checked = false;
                                        document.getElementById("fiscalChaves").checked = false;
                                        document.getElementById("editChaves").checked = false;
                                        $('#mensagemConfig').fadeIn("slow");
                                        document.getElementById("mensagemConfig").innerHTML = "Não encontrado";
                                        $('#mensagemConfig').fadeOut(2000);
                                    }
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#imprRelChave").click(function(){
                    document.getElementById("selecAno").value = "";
                    document.getElementById("selecMesAno").value = "";
                    window.open("modulos/claviculario/imprChave3.php?acao=relac", "RelacChavesLacr");
                });
                $("#selecMesAno").change(function(){
                    document.getElementById("selecAno").value = "";
                    if(document.getElementById("selecMesAno").value != ""){
                        window.open("modulos/claviculario/imprChave3.php?acao=listamesChaves&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), "Mes"+document.getElementById("selecMesAno").value);
                        document.getElementById("selecMesAno").value = "";
                        document.getElementById("relacimprChaves").style.display = "none";
                    }
                });
                $("#selecAno").change(function(){
                    document.getElementById("selecMesAno").value = "";
                    if(document.getElementById("selecAno").value != ""){
                        window.open("modulos/claviculario/imprChave3.php?acao=listaanoChaves&ano="+encodeURIComponent(document.getElementById("selecAno").value), "Ano"+document.getElementById("selecAno").value);
                        document.getElementById("selecAno").value = "";
                        document.getElementById("relacimprChaves").style.display = "none";
                    }
                });
                $("#selecMesAnoMov").change(function(){
                    document.getElementById("selecAnoMov").value = "";
                    if(document.getElementById("selecMesAnoMov").value != ""){
                        window.open("modulos/claviculario/imprChave3.php?acao=listamesChavesSoMovimentados&mesano="+encodeURIComponent(document.getElementById("selecMesAnoMov").value), "MesMov"+document.getElementById("selecMesAnoMov").value);
                        document.getElementById("selecMesAnoMov").value = "";
                        document.getElementById("relacimprChaves").style.display = "none";
                    }
                });
                $("#selecAnoMov").change(function(){
                    document.getElementById("selecMesAnoMov").value = "";
                    if(document.getElementById("selecAnoMov").value != ""){
                        window.open("modulos/claviculario/imprChave3.php?acao=listaanoChavesSoMovimentados&ano="+encodeURIComponent(document.getElementById("selecAnoMov").value), "AnoMov"+document.getElementById("selecAnoMov").value);
                        document.getElementById("selecAnoMov").value = "";
                        document.getElementById("relacimprChaves").style.display = "none";
                    }
                });


            }); // fim do ready

            function insChave(){
                document.getElementById("guardaCod").value = 0;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=buscaNumero", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("numchave").value = Resp.chavenum;
                                    document.getElementById("chavecomplem").value = "";
                                    document.getElementById("localchave").value = "";
                                    document.getElementById("salachave").value = "";
                                    document.getElementById("complemchave").value = "";
                                    document.getElementById("obschave").value = "";
                                    document.getElementById("apagarChaves").style.visibility = "hidden";
                                    document.getElementById("editaModalChave").style.display = "block";
                                    document.getElementById("salachave").focus();
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function editaChave(Cod){
                document.getElementById("guardaCod").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=buscaChave&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("numchave").value = Resp.chavenum;
                                    document.getElementById("chavecomplem").value = Resp.chavecompl;
                                    document.getElementById("localchave").value = Resp.chavelocal;
                                    document.getElementById("salachave").value = Resp.chavesala;
                                    document.getElementById("complemchave").value = Resp.chavenumcompl;
                                    document.getElementById("obschave").value = Resp.chaveobs;
                                    document.getElementById("apagarChaves").style.visibility = "visible";
                                    document.getElementById("editaModalChave").style.display = "block";
                                    document.getElementById("salachave").focus();
                               }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaEditChave(){
                if(document.getElementById("mudou").value != "0"){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=salvaChave&codigo="+document.getElementById("guardaCod").value
                        +"&numchave="+document.getElementById("numchave").value
                        +"&chavecompl="+document.getElementById("chavecomplem").value
                        +"&complemchave="+document.getElementById("complemchave").value
                        +"&salachave="+document.getElementById("salachave").value
                        +"&localchave="+document.getElementById("localchave").value
                        +"&obschave="+document.getElementById("obschave").value
                        , true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.");
                                    }else{
                                        document.getElementById("editaModalChave").style.display = "none";
                                        $("#faixacentral").load("modulos/claviculario/jChave3.php?acao=todos");
                                        $("#faixamostra").load("modulos/claviculario/kChave3.php?acao=todos");
                                        $("#faixaagenda").load("modulos/claviculario/agChave3.php?acao=todos");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("editaModalChave").style.display = "none";
                }
            }

            function saidaChave(Cod){ // id de chaves
                if(parseInt(document.getElementById("guardaEditaChaves").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){
                    document.getElementById("botagenda1").style.visibility = "visible";
                }
                document.getElementById("guardaCod").value = Cod;
                document.getElementById("CodidChave").value = Cod;
                document.getElementById("mensagemErro").style.display = "none";
                document.getElementById("agendamensagemErro").style.display = "none";
                document.getElementById("cpfsolicitante").value = "";
                document.getElementById("agendacpfsolicitante").value = "";
                document.getElementById("selecSolicitante").disabled = false;
                document.getElementById("cpfsolicitante").disabled = false;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=buscaChave&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    if(parseInt(Resp.coderro) === 2){
                                        $.confirm({
                                            title: 'Confirmação!',
                                            content: 'Esta chave está agendada para entregar hoje <br>para <b>'+Resp.nomeagendado+'</b><br><br>Se a entrega for para ele(a), clique em <br><b>Registrar Entrega</b> na agenda.<br><br>Continua mesmo assim?',
                                            autoClose: 'Não|30000',
                                            draggable: true,
                                            buttons: {
                                                Sim: function () {
                                                    document.getElementById("sainumchave").value = Resp.chavenum;
                                                    document.getElementById("saichavecomplem").value = Resp.chavecompl;
                                                    document.getElementById("sailocalchave").value = Resp.chavelocal;
                                                    document.getElementById("saisalachave").value = Resp.chavesala;
                                                    document.getElementById("saicomplemchave").value = Resp.chavenumcompl;
                                                    document.getElementById("saiobschave").value = Resp.chaveobs;
                                                    document.getElementById("registroRetiradaChave").style.display = "block";
                                                    document.getElementById("selecSolicitante").value = "";
                                                    document.getElementById("cpfsolicitante").value = "";
                                                    document.getElementById("resultsolicitante").innerHTML = "";
                                                    document.getElementById("resulttelef").value = "";
                                                    document.getElementById("guardaCPF").value = "";
                                                    document.getElementById("resultcpf").innerHTML = "";
                                                    document.getElementById("resultsetor").innerHTML = "";
                                                    document.getElementById("codagenda").value = 0; // também é usado na função entregaChave
                                                },
                                                Não: function () {}
                                            }
                                        });
                                    }else{
                                        document.getElementById("sainumchave").value = Resp.chavenum;
                                        document.getElementById("saichavecomplem").value = Resp.chavecompl;
                                        document.getElementById("sailocalchave").value = Resp.chavelocal;
                                        document.getElementById("saisalachave").value = Resp.chavesala;
                                        document.getElementById("saicomplemchave").value = Resp.chavenumcompl;
                                        document.getElementById("saiobschave").value = Resp.chaveobs;
                                        document.getElementById("registroRetiradaChave").style.display = "block";
                                        document.getElementById("selecSolicitante").value = "";
                                        document.getElementById("cpfsolicitante").value = "";
                                        document.getElementById("resultsolicitante").innerHTML = "";
                                        document.getElementById("resulttelef").value = "";
                                        document.getElementById("guardaCPF").value = "";
                                        document.getElementById("resultcpf").innerHTML = "";
                                        document.getElementById("resultsetor").innerHTML = "";
                                        document.getElementById("codagenda").value = 0; // também é usado na função entregaChave
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function entregaChave(){
                if(document.getElementById("guardaCPF").value == ""){
                    var nHora = new Date(); 
                    var hora = nHora.getHours();
                    var Cumpr = "Bom Dia!";
                    if(hora >= 12){
                        Cumpr = "Boa Tarde!";
                    }
                    if(hora >= 18){
                        Cumpr = "Boa Noite!";
                    }
                    $.confirm({
                        title: Cumpr,
                        content: 'Selecione um usuário ou insira o CPF.',
                        autoClose: 'OK|8000',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=entregaChave&codigo="+document.getElementById("guardaCod").value
                    +"&cpf="+document.getElementById("resultcpf").innerHTML 
                    +"&celular="+document.getElementById("resulttelef").value 
                    +"&poscod="+document.getElementById("guardaPosCod").value 
                    +"&idagenda="+document.getElementById("codagenda").value 
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("registroRetiradaChave").style.display = "none";  
                                    $("#faixacentral").load("modulos/claviculario/jChave3.php?acao=todos");
                                    $("#faixamostra").load("modulos/claviculario/kChave3.php?acao=todos");  
                                    $("#faixaagenda").load("modulos/claviculario/agChave3.php?acao=todos"); 
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function abreAgendaChave1(){
                document.getElementById("guardaPosCod").value = 0; // solicitante
                document.getElementById("agendaselecSolicitante").value = "";
                document.getElementById("agendanumchave").value = document.getElementById("sainumchave").value;
                document.getElementById("agendachavecomplem").value = document.getElementById("saichavecomplem").value;
                document.getElementById("agendacomplemchave").value = document.getElementById("saicomplemchave").value;

                document.getElementById("agendalocalchave").value = document.getElementById("sailocalchave").value;
                document.getElementById("agendasalachave").value = document.getElementById("saisalachave").value;
                document.getElementById("agendasolicitante").innerHTML = "";
                document.getElementById("agendacpf").innerHTML = ""; 
                document.getElementById("agendasetor").innerHTML = ""; 
                document.getElementById("agendatelef").value = ""; 
                document.getElementById("agendadata").value = ""; 
                document.getElementById("registroRetiradaChave").style.display = "none";  
                document.getElementById("registroAgendaChave").style.display = "block";  
            }

            function abreAgendaChave2(){
                document.getElementById("guardaPosCod").value = 0; // solicitante
                document.getElementById("agendaselecSolicitante").value = "";
                document.getElementById("agendanumchave").value = document.getElementById("voltanumchave").value;
                document.getElementById("agendachavecomplem").value = document.getElementById("voltachavecomplem").value;
                document.getElementById("agendacomplemchave").value = document.getElementById("voltacomplemchave").value;

                document.getElementById("agendalocalchave").value = document.getElementById("voltalocalchave").value;
                document.getElementById("agendasalachave").value = document.getElementById("voltasalachave").value;
                document.getElementById("agendasolicitante").innerHTML = "";
                document.getElementById("agendacpf").innerHTML = ""; 
                document.getElementById("agendasetor").innerHTML = ""; 
                document.getElementById("agendatelef").value = ""; 
                document.getElementById("agendadata").value = ""; 
                document.getElementById("registroAgendaChave").style.display = "block";
                document.getElementById("registroRetornoChave").style.display = "none";  
            }

            function salvaAgenda(){
                if(parseInt(document.getElementById("CodidChave").value) === 0){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Erro na seleção da chave.";
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                if(parseInt(document.getElementById("guardaPosCod").value) === 0 || document.getElementById("guardaPosCod").value == ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Selecione o usuário solicitante.";
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                if(document.getElementById("agendadata").value == ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Defina a data da retirada da chave.";
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                if(compareDates(document.getElementById("guardahoje").value, document.getElementById("agendadata").value) == true){
                    $.confirm({
                        title: 'Atenção!',
                        content: 'A data do agendamento está no passado.',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma agendar a entrega desta chave?',
                    autoClose: 'Não|15000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=agendaChave&codigo="+document.getElementById("CodidChave").value
                                +"&poscod="+document.getElementById("guardaPosCod").value
                                +"&dataagenda="+encodeURIComponent(document.getElementById("agendadata").value)
                                +"&cpf="+document.getElementById("agendacpf").innerHTML 
                                +"&celular="+document.getElementById("agendatelef").value 
                                , true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp2 = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                            if(parseInt(Resp.coderro) === 0){
                                                $("#faixaagenda").load("modulos/claviculario/agChave3.php?acao=todos");
                                                document.getElementById("registroAgendaChave").style.display = "none";
                                            }else{
                                                alert("Houve um erro no servidor.")
                                            }
                                        }
                                    }
                                };
                                ajax.send(null);
                            }
                        },
                        Não: function () {}
                    }
                });
            }

            function saidaChaveAgenda(CodAg, CodChaves, CodUsu, DataSai){ // id de chaves
                document.getElementById("botagenda1").style.visibility = "hidden";
                document.getElementById("guardaCod").value = CodChaves;
                document.getElementById("CodidChave").value = CodChaves;
                document.getElementById("guardaPosCod").value = CodUsu;
                document.getElementById("codagenda").value = CodAg;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=buscaChaveAgenda&codigo="+CodChaves
                    +"&codagenda="+CodAg
                    +"&codusu="+CodUsu
                    +"&dataagenda="+encodeURIComponent(DataSai)
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    if(parseInt(Resp.presente) === 0){
                                        document.getElementById("msgAlerta2").innerHTML = "Verifique com: "+Resp.nomeretirou;
                                        document.getElementById("msgAlerta3").innerHTML = "CPF: "+format_CnpjCpf(Resp.cpfretirou);
                                        document.getElementById("msgAlerta4").innerHTML = "Telefone: "+Resp.telefretirou;
                                        document.getElementById("modalAlerta").style.display = "block";
                                    }else{
                                    document.getElementById("sainumchave").value = Resp.chavenum;
                                    document.getElementById("sailocalchave").value = Resp.chavelocal;
                                    document.getElementById("saisalachave").value = Resp.chavesala;
                                    document.getElementById("saicomplemchave").value = Resp.chavenumcompl;
                                    document.getElementById("saiobschave").value = Resp.chaveobs;
                                    document.getElementById("registroRetiradaChave").style.display = "block";
                                    document.getElementById("selecSolicitante").value = CodUsu;
                                    document.getElementById("cpfsolicitante").value = Resp.cpf;
                                    document.getElementById("resultsolicitante").innerHTML = Resp.nomecompl;
                                    document.getElementById("resulttelef").value = Resp.telef;
                                    document.getElementById("guardaCPF").value = Resp.cpf;
                                    document.getElementById("resultcpf").innerHTML = format_CnpjCpf(Resp.cpf);
                                    document.getElementById("resultsetor").innerHTML = Resp.siglasetor;
                                    document.getElementById("selecSolicitante").disabled = true;
                                    document.getElementById("cpfsolicitante").disabled = true;
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function retornoChave1(Cod){  // Cod = id de chaves  
                if(parseInt(document.getElementById("guardaEditaChaves").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){
                    document.getElementById("botagenda1").style.visibility = "visible";
                }
                document.getElementById("CodidChave").value = Cod;
                document.getElementById("botagenda2").style.visibility = "visible";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=retornoChave1&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("guardaCod").value = Resp.codidctl; // id de chaves_ctl
                                    document.getElementById("voltanumchave").value = Resp.chavenum;
                                    document.getElementById("voltachavecomplem").value = Resp.chavecompl;
                                    document.getElementById("voltacomplemchave").value = Resp.chavenumcompl;
                                    document.getElementById("voltalocalchave").value = Resp.chavelocal;
                                    document.getElementById("voltasalachave").value = Resp.chavesala;
                                    document.getElementById("voltatelef").value = Resp.telef;
                                    document.getElementById("voltasolicitante").value = Resp.nomecompl;
                                    document.getElementById("guardaPosCod").value = Resp.codusuretirou;
                                    document.getElementById("voltacpf").innerHTML = format_CnpjCpf(Resp.cpfretirou);
                                    document.getElementById("registroRetornoChave").style.display = "block";
                                    document.getElementById("selecEntregador").value = "";
                                    document.getElementById("cpfentregador").value = "";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function retornoChave(Cod){
                document.getElementById("guardaCod").value = Cod; // id de chaves_ctl
                document.getElementById("botagenda2").style.visibility = "hidden";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=retornoChave&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("voltanumchave").value = Resp.chavenum;
                                    document.getElementById("voltacomplemchave").value = Resp.chavenumcompl;
                                    document.getElementById("voltalocalchave").value = Resp.chavelocal;
                                    document.getElementById("voltasalachave").value = Resp.chavesala;
                                    document.getElementById("voltatelef").value = Resp.telef;
                                    document.getElementById("voltasolicitante").value = Resp.nomecompl;
                                    document.getElementById("guardaPosCod").value = Resp.codusuretirou;
                                    document.getElementById("voltacpf").innerHTML = format_CnpjCpf(Resp.cpfretirou);
                                    document.getElementById("registroRetornoChave").style.display = "block";
                                    document.getElementById("selecEntregador").value = "";
                                    document.getElementById("cpfentregador").value = "";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function devolveChave(){
                if(document.getElementById("voltasolicitante").value == "" || document.getElementById("voltasolicitante").value == "Nada foi encontrado"){
                    return false;
                }
                if(document.getElementById("guardaCod").value == ""){
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=devolveChave&codigo="+document.getElementById("guardaCod").value 
                    +"&cpfdevolve="+encodeURIComponent(document.getElementById("voltacpf").innerHTML)
                    +"&codusudevolve="+document.getElementById("guardaPosCod").value
                    +"&nomedevolve="+encodeURIComponent(document.getElementById("voltasolicitante").value)
                    +"&telefdevolve="+encodeURIComponent(document.getElementById("voltatelef").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp2 = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp2.coderro) === 0){
                                    document.getElementById("registroRetornoChave").style.display = "none";
                                    document.getElementById("msgdevolv").innerHTML = "Chave "+Resp2.numchave+" DEVOLVIDA";
                                    if(Resp2.nome != ""){
                                        document.getElementById("msgUsuDevolv").innerHTML = "por "+Resp2.nome;
                                    }
                                    document.getElementById("modalDevolvida").style.display = "block";
                                    $("#faixacentral").load("modulos/claviculario/jChave3.php?acao=todos");
                                    $("#faixamostra").load("modulos/claviculario/kChave3.php?acao=todos");
                                    $("#faixaagenda").load("modulos/claviculario/agChave3.php?acao=todos");
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function marcaChave(obj, Campo){
                if(obj.checked === true){
                    Valor = 1;
//                    if(parseInt(document.getElementById("guardaEscolheChaves").value) === 1){ // escolha ativada - Campo esc_chaves1=1 em paramsis
                        if(document.getElementById("configselecSolicitante").value != ""){
                            if(Campo == "chave3"){
                                document.getElementById("botaoChaves").style.visibility = "visible";
                            }
                        }
//                    }
                }else{
                    Valor = 0;
//                    if(parseInt(document.getElementById("guardaEscolheChaves").value) === 1){ // escolha ativada - Campo esc_chaves1=1 em paramsis
                        if(document.getElementById("configselecSolicitante").value != ""){
                            if(Campo == "chave3"){
                                document.getElementById("botaoChaves").style.visibility = "hidden";
                            }
                        }
//                    }
                }
                if(document.getElementById("configselecSolicitante").value == ""){
                    if(obj.checked === true){
                        obj.checked = false;
                    }else{
                        obj.checked = true;
                    }
                    $('#mensagemConfig').fadeIn("slow");
                    document.getElementById("mensagemConfig").innerHTML = "Selecione um usuário.";
                    $('#mensagemConfig').fadeOut(2000);
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=configMarcaChave&codigo="+document.getElementById("configselecSolicitante").value
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
                                            content: 'Não restaria outro marcado para gerenciar o claviculário.',
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

            function AbreModalChaves(){
                if(document.getElementById("configselecSolicitante").value == ""){
                    return false;
                }
                document.getElementById("relacmodalChaves").style.display = "block";
                $("#faixachaves").load("modulos/claviculario/escChave3.php?usuario="+document.getElementById("configselecSolicitante").value);
            }
            function fechaModalChaves(){
                document.getElementById("relacmodalChaves").style.display = "none";
            }
            function marcaChaveInd(Obj, Cod){
                if(Obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=marcaChaveUsuario&param="+Valor+"&codigo="+Cod+"&usuario="+document.getElementById("configselecSolicitante").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("chavesmarcadas").innerHTML = "Marcadas: "+Resp.marcadas;
                                    if(parseInt(Resp.todas) === 1){
                                        document.getElementById("checkGeral").checked = true;
                                    }else{
                                        document.getElementById("checkGeral").checked = false;
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                } 
            }

            function marcaChaveTodas(Obj){
                if(Obj.checked === true){
                    Valor = 1;
                    Texto = "Confirma marcar todas as chaves?"
                }else{
                    Valor = 0;
                    Texto = "Confirma DESMARCAR todas as chaves?"
                }
                $.confirm({
                    title: 'Confirmação!',
                    content: Texto,
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=marcaChaveTodas&param="+Valor+"&usuario="+document.getElementById("configselecSolicitante").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }
                                            $("#faixachaves").load("modulos/claviculario/escChave3.php?usuario="+document.getElementById("configselecSolicitante").value);
                                        }
                                    }
                                };
                                ajax.send(null);
                            }
                        },
                        Não: function () {
                            if(Obj.checked === true){
                                Obj.checked = false;
                            }else{
                                Obj.checked = true;
                            }
                        }
                    }
                });
            }

            function apagaAgendaChaves(Cod){ // põe ativo = 2
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma apagar este agendamento?',
                    autoClose: 'Não|15000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=apagaagendaChave&codigo="+Cod, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp2 = eval("(" + ajax.responseText + ")"); 
                                            if(parseInt(Resp.coderro) === 0){
                                                $("#faixaagenda").load("modulos/claviculario/agChave3.php?acao=todos");
                                            }else{
                                                alert("Houve um erro no servidor.")
                                            }
                                        }
                                    }
                                };
                                ajax.send(null);
                            }
                        },
                        Não: function () {}
                    }
                });
            }

            function apagaSaidaChave(Cod, IdChaves){ // só para superusuários
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma apagar esta retirada?',
                    autoClose: 'Não|15000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/claviculario/salvaChave3.php?acao=apagaretiradaChave&codigo="+Cod+"&idchaves3="+IdChaves, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")"); 
                                            if(parseInt(Resp.coderro) === 0){
                                                $("#faixacentral").load("modulos/claviculario/jChave3.php?acao=todos");
                                                $("#faixamostra").load("modulos/claviculario/kChave3.php?acao=todos");
                                                $("#faixaagenda").load("modulos/claviculario/agChave3.php?acao=todos");
                                            }else{
                                                alert("Houve um erro no servidor.")
                                            }
                                        }
                                    }
                                };
                                ajax.send(null);
                            }
                        },
                        Não: function () {}
                    }
                });
            }

            function resumoUsuChaves(){
                window.open("modulos/claviculario/imprUsuCh3.php?acao=listaUsuarios", "ChavesUsu3");
            }
            function abreChavesConfig(){
                document.getElementById("registroChaves").checked = false;
                document.getElementById("retiraChave").checked = false;
                document.getElementById("fiscalChaves").checked = false;
                document.getElementById("editChaves").checked = false;
                document.getElementById("configcpfsolicitante").value = "";
                document.getElementById("configselecSolicitante").value = "";
                document.getElementById("botaoChaves").style.visibility = "hidden";
                document.getElementById("modalChavesConfig").style.display = "block";
            }
            function fechaModalConfig(){
                document.getElementById("modalChavesConfig").style.display = "none";
            }
            function abreImprChaves(){
                document.getElementById("relacimprChaves").style.display = "block";
            }
            function fechaImprChaves(){
                document.getElementById("relacimprChaves").style.display = "none";
            }  
            function fechaEditaChave(){
                document.getElementById("editaModalChave").style.display = "none";
            }
            function fechaRetiradaChave(){
                document.getElementById("registroRetiradaChave").style.display = "none";
            }
            function fechaRetornoChave(){
                document.getElementById("registroRetornoChave").style.display = "none";
            }
            function fechaAgendaChave(){
                document.getElementById("registroAgendaChave").style.display = "none";
            }
            function fechaDevolv(){
                document.getElementById("modalDevolvida").style.display = "none";
            }
            function fechaAlerta(){
                document.getElementById("modalAlerta").style.display = "none";
            }
            function fechaInfo(){
                document.getElementById("modalInfo").style.display = "none";
                document.getElementById("selecSolicitante").value = "";
                document.getElementById("guardaPosCod").value = 0;
                document.getElementById("resultsolicitante").innerHTML = "";
                document.getElementById("guardaCPF").value = "";
                document.getElementById("resultcpf").innerHTML = "";
                document.getElementById("cpfsolicitante").value = "";
                document.getElementById("resultsetor").innerHTML = "";

                document.getElementById("agendacpfsolicitante").value = "";
                document.getElementById("agendasolicitante").innerHTML = "";
                document.getElementById("agendacpf").innerHTML = "";
                document.getElementById("agendasetor").innerHTML = "";
                document.getElementById("agendatelef").value = "";
                document.getElementById("agendaselecSolicitante").value = "";
            }
            function modif(){
                document.getElementById("mudou").value = "1";
            }

            function foco(id){
                document.getElementById(id).focus();
            }
            
            function compareDates (date1, date2) {
                let parts1 = date1.split('/') // separa a data pelo caracter '/'
                date1 = new Date(parts1[2], parts1[1] - 1, parts1[0]) // formata 'date'

                let parts2 = date2.split('/') // separa a data pelo caracter '/'
                date2 = new Date(parts2[2], parts2[1] - 1, parts2[0]) // formata 'date'
                  // compara se a data informada é maior que a data atual e retorna true ou false
                return date1 > date2 ? true : false
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

            function validaCPF(cpf) {
                var Soma = 0
                var Resto
                var strCPF = String(cpf).replace(/[^\d]/g, '')
                if (strCPF.length !== 11)
                    return false
                if ([
                    '00000000000',
                    '11111111111',
                    '22222222222',
                    '33333333333',
                    '44444444444',
                    '55555555555',
                    '66666666666',
                    '77777777777',
                    '88888888888',
                    '99999999999',
                ].indexOf(strCPF) !== -1)
                return false
                for (i=1; i<=9; i++)
                    Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
                    Resto = (Soma * 10) % 11
                    if ((Resto == 10) || (Resto == 11)) 
                        Resto = 0
                    if (Resto != parseInt(strCPF.substring(9, 10)) )
                    return false
                    Soma = 0
                    for (i = 1; i <= 10; i++)
                        Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i)
                        Resto = (Soma * 10) % 11
                        if ((Resto == 10) || (Resto == 11)) 
                            Resto = 0
                        if (Resto != parseInt(strCPF.substring(10, 11) ) )
                            return false
                return true
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
    <body class="corClara" onbeforeunload="return mudaTema(0)"> <!-- ao sair retorna os background claros -->
        <?php
        if(!$Conec){
            echo "Sem contato com o PostGresql";
            return false;
        }
        $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'poslog'");
        $rowSis = pg_num_rows($rsSis);
        if($rowSis == 0){
            echo "Sem contato com os arquivos do sistema. Informe à ATI.";
            return false;
        }
        date_default_timezone_set('America/Sao_Paulo'); //Um dia = 86.400 seg
        $Hoje = date('d/m/Y');


//---------------  Provisório

//        pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".chaves3");
        pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".chaves3 (
            id SERIAL PRIMARY KEY, 
            chavenum integer NOT NULL DEFAULT 0,
            chavenumcompl VARCHAR(5),
            chavelocal VARCHAR(100),
            chavesala VARCHAR(50),
            chaveobs text, 
            presente smallint NOT NULL DEFAULT 1, 
            ativo smallint NOT NULL DEFAULT 1, 
            usuins bigint NOT NULL DEFAULT 0,
            datains timestamp without time zone DEFAULT '3000-12-31',
            usuedit bigint NOT NULL DEFAULT 0,
            dataedit timestamp without time zone DEFAULT '3000-12-31' 
            )
        ");

//        pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".chaves3_ctl");
        pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".chaves3_ctl (
            id SERIAL PRIMARY KEY, 
            chaves_id integer NOT NULL DEFAULT 0,
            datasaida timestamp without time zone DEFAULT '3000-12-31',
            datavolta timestamp without time zone DEFAULT '3000-12-31',
            funcentrega bigint NOT NULL DEFAULT 0,
            funcrecebe bigint NOT NULL DEFAULT 0,
            usuretira bigint NOT NULL DEFAULT 0,
            usudevolve bigint NOT NULL DEFAULT 0,
            cpfretira VARCHAR(20),
            cpfdevolve VARCHAR(20),
            telef VARCHAR(20),
            nomedevolve VARCHAR(200),
            telefdevolve VARCHAR(20), 
            ativo smallint NOT NULL DEFAULT 1, 
            usuins bigint NOT NULL DEFAULT 0,
            datains timestamp without time zone DEFAULT '3000-12-31',
            usuedit bigint NOT NULL DEFAULT 0,
            dataedit timestamp without time zone DEFAULT '3000-12-31' 
            )
        ");

//        pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".chaves3_agd");
        pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".chaves3_agd (
            id SERIAL PRIMARY KEY, 
            chaves_id integer NOT NULL DEFAULT 0,
            datasaida timestamp without time zone DEFAULT '3000-12-31',
            usuretira bigint NOT NULL DEFAULT 0,
            cpfretira VARCHAR(20),
            telef VARCHAR(20),
            ativo smallint NOT NULL DEFAULT 1, 
            usuins bigint NOT NULL DEFAULT 0,
            datains timestamp without time zone DEFAULT '3000-12-31'
            )
        ");


        $rs = pg_query($Conec, "SELECT chavenum FROM ".$xProj.".chaves3 LIMIT 3 ");
        $row = pg_num_rows($rs);
        if($row == 0){
            //Insere as primeiras 10 chaves
            for($i = 1; $i <= 10; $i++){
                $rs0 = pg_query($Conec, "SELECT chavenum FROM ".$xProj.".chaves3 WHERE chavenum = $i ");
                $row0 = pg_num_rows($rs0);
                if($row0 == 0){
                    pg_query($Conec, "INSERT INTO ".$xProj.".chaves3 (chavenum, usuins, datains) VALUES ($i, 3, NOW())");
                }
            }
        }

//        pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".chaves3_aut");
        pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".chaves3_aut (
            id SERIAL PRIMARY KEY, 
            chaves_id integer NOT NULL DEFAULT 0,
            pessoas_id bigint NOT NULL DEFAULT 0,
            ativo smallint NOT NULL DEFAULT 1, 
            usuins bigint NOT NULL DEFAULT 0,
            datains timestamp without time zone DEFAULT '3000-12-31',
            usuedit bigint NOT NULL DEFAULT 0,
            dataedit timestamp without time zone DEFAULT '3000-12-31'
            )"
        );

//______________________


        $Clav = parEsc("clav3", $Conec, $xProj, $_SESSION["usuarioID"]); // entrega e devolução
        $ClavEdit = parEsc("clav_edit", $Conec, $xProj, $_SESSION["usuarioID"]); // edita, modifica
        $Chave = parEsc("chave3", $Conec, $xProj, $_SESSION["usuarioID"]); // pode pegar chaves
        $FiscClav = parEsc("fisc_clav3", $Conec, $xProj, $_SESSION["usuarioID"]); // fiscal de chaves
        $Tema = parEsc("tema", $Conec, $xProj, $_SESSION["usuarioID"]); // Claro(0) Escuro(1)
        $EscChave = parAdm("esc_chaves3", $Conec, $xProj); // marca para aparecer/ocultar escolha de chaves a retirar por usuário 

        $OpUsuSolic = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE chave3 = 1 And ativo = 1 ORDER BY nomecompl, nomeusual");
        $OpUsuAgenda = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE chave3 = 1 And ativo = 1 ORDER BY nomecompl, nomeusual");
        $OpUsuEntreg = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE chave3 = 1 And ativo = 1 ORDER BY nomecompl, nomeusual");
        $OpConfig = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
        $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(datasaida, 'MM'), '/', TO_CHAR(datasaida, 'YYYY')) 
        FROM ".$xProj.".chaves3_ctl GROUP BY TO_CHAR(datasaida, 'MM'), TO_CHAR(datasaida, 'YYYY') ORDER BY TO_CHAR(datasaida, 'YYYY') DESC, TO_CHAR(datasaida, 'MM') DESC ");
        $OpcoesEscAno = pg_query($Conec, "SELECT EXTRACT(YEAR FROM ".$xProj.".chaves3_ctl.datasaida)::text 
        FROM ".$xProj.".chaves3_ctl GROUP BY 1 ORDER BY 1 DESC ");
        $OpcoesEscMesMov = pg_query($Conec, "SELECT CONCAT(TO_CHAR(datasaida, 'MM'), '/', TO_CHAR(datasaida, 'YYYY')) 
        FROM ".$xProj.".chaves3_ctl GROUP BY TO_CHAR(datasaida, 'MM'), TO_CHAR(datasaida, 'YYYY') ORDER BY TO_CHAR(datasaida, 'YYYY') DESC, TO_CHAR(datasaida, 'MM') DESC ");
        $OpcoesEscAnoMov = pg_query($Conec, "SELECT EXTRACT(YEAR FROM ".$xProj.".chaves3_ctl.datasaida)::text 
        FROM ".$xProj.".chaves3_ctl GROUP BY 1 ORDER BY 1 DESC ");
        ?>

        <div id="tricoluna0" style="margin: 20px; padding: 10px; border: 2px solid; border-radius: 10px; min-height: 52px;">
            <div id="tricoluna1" class="box" style="position: relative; float: left; width: 17%;">
                <img src="imagens/settings.png" height="20px;" id="imgChavesconfig" style="cursor: pointer; padding-right: 30px;" onclick="abreChavesConfig();" title="Configurar o acesso às chaves no claviculário das Chafes Lacradas">
                <input type="button" id="botinserir" class="resetbot fundoAzul2" style="font-size: 80%;" value="Inserir Nova Chave" onclick="insChave();">
            </div>
            <div id="tricoluna2" class="box" style="position: relative; float: left; width: 55%; text-align: center;">
                <h5>Controle de Chaves Lacradas</h5>
            </div>
            <div id="tricoluna3" class="box" style="position: relative; float: left; width: 25%; text-align: right;">
                <div id="selectTema" style="position: relative; float: left; padding-left: 8px;">
                    <label id="etiqcorFundo" class="etiq" style="color: #6C7AB3; font-size: 80%;">Tema: </label>
                    <input type="radio" name="corFundo" id="corFundo0" value="0" <?php if($Tema == 0){echo 'CHECKED';}; ?> title="Tema claro" onclick="mudaTema(0);" style="cursor: pointer;"><label for="corFundo0" class="etiq" style="cursor: pointer;">&nbsp;Claro</label>
                    <input type="radio" name="corFundo" id="corFundo1" value="1" <?php if($Tema == 1){echo 'CHECKED';}; ?> title="Tema escuro" onclick="mudaTema(1);" style="cursor: pointer;"><label for="corFundo1" class="etiq" style="cursor: pointer;">&nbsp;Escuro</label>
                    <label style="padding-right: 5px;"></label>
                </div>
                <label style="padding-left: 20px;"></label>
                <button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="abreImprChaves();">PDF</button>
            </div>

            <div id="faixaMensagem" style="display: none; position: relative; margin: 70px; padding: 20px; text-align: center;">
                <br><br><br>Usuário não cadastrado.
            </div>
        </div>


        <!-- div três colunas -->
        <div style="margin: 0 auto; text-align: center;">
            <div style="position: relative; float: left; margin: 5px; text-align: center; width: 16%; border: 1px solid; border-radius: 10px;"><div id="faixaagenda"></div></div>
            <div style="position: relative; float: left; margin: 5px; text-align: center; width: 55%; border: 1px solid; border-radius: 10px;"><div id="faixacentral"></div></div>
            <div style="position: relative; float: left; margin: 5px; text-align: center; width: 25%; border: 1px solid; border-radius: 10px;"><div id="faixamostra"></div></div>
        </div>

        <input type="hidden" id="guardaCod" value="0" />
        <input type="hidden" id="guardaCPF" value="" />
        <input type="hidden" id="guardaPosCod" value="" />
        <input type="hidden" id="guardahoje" value="<?php echo $Hoje; ?>" />
        <input type="hidden" id="CodidChave" value="0" />
        <input type="hidden" id="mudou" value="0" />
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>" />
        <input type="hidden" id="guardaFiscChaves" value="<?php echo $FiscClav; ?>" />
        <input type="hidden" id="guardaEditaChaves" value="<?php echo $ClavEdit; ?>" />
        <input type="hidden" id="guardaEntregaChaves" value="<?php echo $Clav; ?>" />
        <input type="hidden" id="codagenda" value="0" />
        <input type="hidden" id="guardaEscolheChaves" value="<?php echo $EscChave; ?>" />
        
        <div id="editaModalChave" class="relacmodal">
            <div class="modal-content-relacChave3 corPreta">
                <span class="close" onclick="fechaEditaChave();">&times;</span>
                <label style="color: #666;">Edição:</label>
                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td class="etiqAzul" style="padding-bottom: 7px;">Chave: </td>
                        <td style="padding-bottom: 10px;"><input type="text" id="numchave" style="width: 70px; text-align: center;" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('chavecomplem');return false;}" title="Número da chave. Preferencialmente único."/>
                            <input type="text" id="chavecomplem" style="width: 60px; text-align: center;" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('salachave');return false;}" title="Complemento do número da Chave"/><label class="etiqAzul"><- complemento</label>
                        </td>
                        <td style="padding-bottom: 10px;"></td>
                    </tr>
                    <tr>
                        <td class="etiqAzul">Sala nº: </td>
                        <td colspan="3" style="width: 200px;"><input type="text" id="salachave" style="width: 180px; text-align: center;" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('complemchave');return false;}" title="Número da sala"/></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiqAzul">Sala Nome: </td>
                        <td colspan="3" style="width: 100px;"><input type="text" id="complemchave" style="width: 380px; text-align: left;" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('localchave');return false;}" title="Nome da sala"/></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiqAzul">Local: </td>
                        <td colspan="3" style="width: 100px;"><input type="text" id="localchave" maxlength="70" style="width: 380px; text-align: left;" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('numchave');return false;}" title=""/></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiqAzul">Observações: </td>
                        <td colspan="3" style="width: 100px;"><textarea id="obschave" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px; padding: 2px;" rows="2" cols="43" title="Observações" onchange="modif();"></textarea></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="text-align: center; padding-top: 10px;"><button class="botpadrred" id="apagarChaves" style="font-size: 60%;" onclick="apagaChave();">Apagar</button></td>
                        <td colspan="2" style="text-align: center; padding-top: 10px;"><button class="botpadrblue" onclick="salvaEditChave();">Salvar</button></td>
                    </tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->

        <!-- Retirada Chave-->
        <div id="registroRetiradaChave" class="relacmodal">
            <div class="modal-content-registroChave corPreta"> <!-- background transparent-->
                <span class="close" style="font-size: 250%; color: black;" onclick="fechaRetiradaChave();">&times;</span>
                <div style="border: 2px solid red; border-radius: 10px; background: linear-gradient(180deg, white, #fce8e7)">
                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td colspan="4" style="text-align: center; padding-top: 5px; font-weight: bold;">Registro de Retirada de Chave Lacrada</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding-top: 10px;"></td>
                    </tr>
                    <tr>
                        <td class="etiq" style="padding-bottom: 7px;">Chave: </td>
                        <td><input disabled type="text" id="sainumchave" style="width: 70px; text-align: center; border: 1px solid #666; border-radius: 5px;" />
                            <input disabled type="text" id="saichavecomplem" style="width: 60px; text-align: center; border: 1px solid #666; border-radius: 5px;" />
                        </td>
                        <td colspan="2" style="padding-bottom: 10px;"></td>
                    </tr>
                    <tr>
                        <td class="etiq">Sala nº: </td>
                        <td colspan="3"><input disabled type="text" id="saisalachave" style="width: 180px; text-align: center; border: 1px solid #666; border-radius: 5px;"/></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiq">Sala Nome: </td>
                        <td colspan="3"><input disabled type="text" id="saicomplemchave" style="width: 380px; text-align: LEFT; border: 1px solid #666; border-radius: 5px;"/></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiq">Local: </td>
                        <td colspan="3"><input disabled type="text" id="sailocalchave" style="width: 380px; text-align: left; border: 1px solid #666; border-radius: 5px;" /></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="etiq">Observações: </td>
                        <td colspan="3" style="width: 100px;"><textarea disabled id="saiobschave" style="border: 1px solid blue; border-radius: 10px; padding: 2px;" rows="2" cols="43"></textarea></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                    </tr>
                </table>
                </div>

                <div style="border: 2px solid red; border-radius: 10px; margin-top: 10px; background: linear-gradient(180deg, white, #fce8e7)">
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="4" style="text-align: center;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center;">Busca Nome ou CPF do Solicitante</td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Procura nome: </td>
                            <td style="width: 100px;">
                                <select id="selecSolicitante" style="max-width: 230px;" onchange="modif();" title="Selecione um usuário.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpUsuSolic){
                                        while ($Opcoes = pg_fetch_row($OpUsuSolic)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td class="etiqAzul"><label class="etiqAzul">ou CPF:</label></td>
                            <td>
                                <input type="text" id="cpfsolicitante" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('resulttelef');return false;}" title="Procura por CPF. Digite o CPF do solicitante."/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                    </table>
                </div>

                <div style="border: 2px solid red; border-radius: 10px; margin-top: 10px; text-align: left; background: linear-gradient(180deg, white, #fce8e7)">
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="2" style="text-align: center; padding-top: 10px; min-width: 150px;"></td>
                            <td colspan="2" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; font-weight: bold;">Solicitante</td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; font-weight: bold;">
                                <label id="mensagemErro" style="display: none; min-width: 20px; padding-left: 3px; font-size: 120%; color: red;"></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiqAzul" style="width: 150px;">Nome:</td>
                            <td colspan="3" style="min-width: 200px;"><label id="resultsolicitante" style="min-width: 200px; padding-left: 3px; font-size: 120%;"></label></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">CPF:</td>
                            <td><label id="resultcpf" style="padding-left: 3px;"></label></td>
                            <td class="etiqAzul" style="width: 100px;">Setor:</td>
                            <td><label id="resultsetor" style="padding-left: 3px;"></label></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Telefone Celular:</td>
                            <td colspan="3"><input type="text" id="resulttelef" style="width: 200px; text-align: center; border: 1px solid #666; border-radius: 5px;" onchange="modif();" /></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: left; padding-top: 10px;"><button class="botpadramarelo" id="botagenda1" style="font-size: 80%;" onclick="abreAgendaChave1();">Agendar retirada desta Chave</button></td>
                            <td colspan="2" style="text-align: left; padding-top: 10px;"><button class="botpadrred" style="font-size: 80%;" onclick="entregaChave();">Registrar Saída</button></td>
                        </tr>

                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 5px;"></td>
                        </tr>
                    </table>
                </div>

            </div>
        </div> <!-- Fim Modal-->

<!-- Retorno Chave -->
        <div id="registroRetornoChave" class="relacmodal">
            <div class="modal-content-registroChave corPreta">
                <span class="close" style="font-size: 250%; color: black;" onclick="fechaRetornoChave();">&times;</span>
                <div style="border: 2px solid blue; border-radius: 10px; background: linear-gradient(180deg, white, #89e9eb)">
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 5px; font-weight: bold;">Registro de Retorno de Chave Lacrada</td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 5px;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td class="etiq" style="padding-bottom: 7px;">Chave: </td>
                            <td><input disabled type="text" id="voltanumchave" style="width: 70px; text-align: center; border: 1px solid #666; border-radius: 5px;" />
                                <input disabled type="text" id="voltachavecomplem" style="width: 60px; text-align: center; border: 1px solid #666; border-radius: 5px;" />
                            </td>
                            <td colspan="2" style="padding-bottom: 10px;"></td>
                        </tr>
                        <tr>
                            <td class="etiq">Sala nº: </td>
                            <td colspan="3"><input disabled type="text" id="voltasalachave" style="width: 180px; text-align: center; border: 1px solid #666; border-radius: 5px;"/></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="etiq">Sala Nome: </td>
                            <td colspan="3"><input disabled type="text" id="voltacomplemchave" style="width: 380px; text-align: left; border: 1px solid #666; border-radius: 5px;" /></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="etiq">Local: </td>
                            <td colspan="3"><input disabled type="text" id="voltalocalchave" style="width: 380px; text-align: left; border: 1px solid #666; border-radius: 5px;" /></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                    </table>
                </div>

               <div style="border: 2px solid blue; border-radius: 10px; margin-top: 10px; text-align: left; background: linear-gradient(180deg, white, #89e9eb)">
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; font-weight: bold;">Devolvido por</td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Nome:</td>
                            <td colspan="3"><input disabled type="text" id="voltasolicitante" style="min-width: 350px; padding-left: 3px; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('voltatelef');return false;}" /></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">CPF:</td>
                            <td><label id="voltacpf" style="padding-left: 3px; min-width: 120px;"></label></td>
                            <td class="etiqAzul">Setor:</td>
                            <td><label id="voltasetor" style="padding-left: 3px;"></label></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Telefone Celular:</td>
                            <td colspan="3"><input type="text" id="voltatelef" style="width: 200px; text-align: center; border: 1px solid #666; border-radius: 5px;" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('voltasolicitante');return false;}" /></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: left; padding-top: 10px;"><button class="botpadramarelo" id="botagenda2" style="font-size: 80%;" onclick="abreAgendaChave2();">Agendar retirada desta Chave</button></td>
                            <td colspan="2" style="text-align: center; padding-top: 10px;"><button class="botpadrred" style="font-size: 80%;" onclick="devolveChave();">Registrar Retorno</button></td>
                        </tr>

                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 5px;"></td>
                        </tr>
                    </table>
                </div>

                <div style="border: 2px solid blue; border-radius: 10px; margin-top: 10px; background: linear-gradient(180deg, white, #89e9eb)">
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="4" style="text-align: center;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center;">Busca Nome ou CPF de <u>outro Entregador</u></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Procura nome: </td>
                            <td style="width: 100px;">
                                <select id="selecEntregador" style="max-width: 230px;" onchange="modif();" title="Selecione um usuário.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpUsuEntreg){
                                        while ($Opcoes = pg_fetch_row($OpUsuEntreg)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td class="etiqAzul"><label class="etiqAzul">ou CPF:</label></td>
                            <td>
                                <input type="text" id="cpfentregador" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('voltasolicitante');return false;}" title="Procura por CPF. Digite o CPF do entregador. Enter em branco para digitar um nome."/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div> <!-- Fim Modal-->


<!-- Agenda Chave -->
        <div id="registroAgendaChave" class="relacmodal">
            <div class="modal-content-registroChave corPreta">
                <span class="close" style="font-size: 250%; color: black;" onclick="fechaAgendaChave();">&times;</span>
                <div style="border: 2px solid blue; border-radius: 10px; background: linear-gradient(180deg, white, #FFFF00)">
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 5px; font-weight: bold;">Agendamento de Retirada de Chave Lacrada</td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 5px;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td class="etiq" style="padding-bottom: 7px;">Chave: </td>
                            <td><input disabled type="text" id="agendanumchave" style="width: 70px; text-align: center; border: 1px solid #666; border-radius: 5px;" />
                                <input disabled type="text" id="agendachavecomplem" style="width: 60px; text-align: center; border: 1px solid #666; border-radius: 5px;" />
                            </td>
                            <td colspan="2" style="padding-bottom: 10px;"></td>
                        </tr>
                        <tr>
                            <td class="etiq">Sala n°: </td>
                            <td colspan="3"><input disabled type="text" id="agendasalachave" style="width: 180px; text-align: center; border: 1px solid #666; border-radius: 5px;"/></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="etiq">Sala Nome: </td>
                            <td colspan="3"><input disabled type="text" id="agendacomplemchave" style="width: 380px; text-align: left; border: 1px solid #666; border-radius: 5px;"/></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="etiq">Local: </td>
                            <td colspan="3"><input disabled type="text" id="agendalocalchave" style="width: 380px; text-align: left; border: 1px solid #666; border-radius: 5px;" /></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                    </table>
                </div>

                <div style="border: 2px solid red; border-radius: 10px; margin-top: 10px; background: linear-gradient(180deg, white, #FFFF00)">
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="4" style="text-align: center;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center;">Busca Nome ou CPF do Solicitante</td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Procura nome: </td>
                            <td style="width: 100px;">
                                <select id="agendaselecSolicitante" style="max-width: 230px;" onchange="modif();" title="Selecione um usuário.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpUsuAgenda){
                                        while ($Opcoes = pg_fetch_row($OpUsuAgenda)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td class="etiqAzul"><label class="etiqAzul">ou CPF:</label></td>
                            <td>
                                <input type="text" id="agendacpfsolicitante" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('agendatelef');return false;}" title="Procura por CPF. Digite o CPF do solicitante."/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                    </table>
                </div>

                <div style="border: 2px solid red; border-radius: 10px; margin-top: 10px; background: linear-gradient(180deg, white, #FFFF00)">
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="6"></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="text-align: center; font-weight: bold;">Solicitante</td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; font-weight: bold;">
                                <label id="agendamensagemErro" style="display: none; min-width: 20px; padding-left: 3px; font-size: 120%; color: red;"></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Nome:</td>
                            <td colspan="5"><label id="agendasolicitante" style="min-width: 200px; padding-left: 3px; font-size: 120%;"></label></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">CPF:</td>
                            <td colspan="2"><label id="agendacpf" style="padding-left: 5px;"></label></td>
                            <td class="etiqAzul">Setor:</td>
                            <td colspan="2"><label id="agendasetor" style="padding-left: 3px;"></label></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Telefone Celular:</td>
                            <td colspan="5"><input type="text" id="agendatelef" style="width: 200px; text-align: center; border: 1px solid #666; border-radius: 5px;" onchange="modif();" /></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                    </table>
                    <br>
                </div>

                <div style="border: 2px solid red; border-radius: 10px; margin-top: 10px; background: linear-gradient(180deg, white, #FFFF00)">
                    <br>
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="6" style="text-align: center; font-weight: bold;">Retirada</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: right; vertical-align: top"><label class="etiqAzul">Autorizar entrega da chave em: </label></td>
                            <td colspan="3" style="text-align: left;"><input type="text" id="agendadata" width="150" style="height: 30px; text-align: center; border: 1px solid; border-radius: 5px;" placeholder="Data" onkeypress="if(event.keyCode===13){javascript:foco('botsalvadata');return false;}"/></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="text-align: center; padding-top: 10px;"><button class="botpadrred" id="botsalvadata" onclick="salvaAgenda();">Salvar</button></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="text-align: center; padding-top: 5px;"><div id="mensagem" style="color: red; font-weight: bold;"></div></td>
                        <tr>
                    </table>
                    <br>
                </div>
            </div>
        </div> <!-- Fim Modal-->

         <!-- Modal configuração-->
        <div id="modalChavesConfig" class="relacmodal">
            <div class="modal-content-ChavesControle corPreta">
                <span class="close" onclick="fechaModalConfig();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"></div>
                        <div class="col quadro"><h6 id="titulomodal" style="text-align: center; color: #666;">Configuração Claviculário Chaves Lacradas</h6></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><button class="botpadrred" style="font-size: 70%;" onclick="resumoUsuChaves();">Resumo em PDF</button></div> 
                    </div>
                </div>
                <label class="etiqAzul">Selecione um usuário para ver a configuração:</label>
                <div style="position: relative; float: right; color: red; font-weight: bold; padding-right: 200px;" id="mensagemConfig"></div>
                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td colspan="4" style="text-align: center;"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center;">Busca Nome ou CPF do Usuário</td>
                    </tr>
                    <tr>
                        <td class="etiqAzul">Procura nome: </td>
                        <td style="width: 100px;">
                            <select id="configselecSolicitante" style="max-width: 230px;" onchange="modif();" title="Selecione um usuário.">
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
                            <input type="text" id="configcpfsolicitante" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('configselecSolicitante');return false;}" title="Procura por CPF. Digite o CPF do solicitante."/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                    </tr>
                </table>

                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td class="etiqAzul" title="Registrar a entrega e devolução das chaves do claviculário de Chaves Lacradas">Lacradas: </td>
                        <td colspan="4">
                            <input type="checkbox" id="registroChaves" title="Registrar a entrega e devolução das chaves do claviculário de Chaves Lacradas" onchange="marcaChave(this, 'clav3');" >
                            <label for="registroChaves" title="Registrar a entrega e devolução das chaves do claviculário de Chaves Lacradas">registrar a entrega e devolução das Chaves Lacradas</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiqAzul" title="Inserir, modificar, editar a descrição das chaves do claviculário das Chaves Lacradas">Lacradas:</td>
                        <td colspan="4">
                            <input type="checkbox" id="editChaves" title="Gerenciar, inserir, modificar a descrição das chaves do claviculário das Chaves Lacradas" onchange="marcaChave(this, 'clav_edit3');" >
                            <label for="editChaves" title="Inserir, modificar, editar a descrição das chaves do claviculário das Chaves Lacradas">inserir, editar e apagar Chaves Lacradas</label>
                        </td>
                    </tr>

                    <tr>
                        <td class="etiqAzul" title="Apenas fiscalizar o funcionamento do claviculário das Chaves Lacradas">Lacradas: </td>
                        <td colspan="4">
                            <input type="checkbox" id="fiscalChaves" title="Apenas fiscalizar o funcionamento do claviculário das Chaves Lacradas" onchange="marcaChave(this, 'fisc_clav3');" >
                            <label for="fiscalChaves" title="Apenas fiscalizar o funcionamento do claviculário das Chaves Lacradas">fiscalizar o funcionamento do claviculário das Chaves Lacradas</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiq" style="border-bottom: 1px solid black;" title="Autorizado a retirar chaves do claviculário de Chaves Lacradas"></td>
                        <td colspan="4" style="border-bottom: 1px solid; padding-left: 20px;">
                            <input type="checkbox" id="retiraChave" title="Autorizado a retirar chaves do claviculário de Chaves Lacradas" onchange="marcaChave(this, 'chave3');" >
                            <label for="retiraChave" title="Autorizado a retirar chaves do claviculário de Chaves Lacradas">usuário autorizado a retirar Chaves Lacradas</label>
                            <label style="padding-left: 5px;"></label>
                            <!-- Há um gatilho em paramsis para requerer ou interromper a exigência de determinar qual chave um usuário pode pegar -->
                            <button id="botaoChaves" <?php if($EscChave == 1){echo "class='botpadrTijolo'";}else{echo "class='botpadrblue'";} ?> style="font-size: 70%;" onclick="AbreModalChaves();" <?php if($EscChave == 1){echo "title='Vínculo ligado - Definir quais chaves este usuário pode pegar.'";}else{echo "title='Vínculo desligado - Definir quais chaves este usuário pode pegar.'";} ?> >Chaves</button>
                        </td>
                    </tr>
                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 5px;"><div id="mensagemConfig__" style="color: red; font-weight: bold;"></div></td>
                        <tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para imprimir em pdf  -->
        <div id="relacimprChaves" class="relacmodal">
            <div class="modal-content-imprChaves corPreta">
                <span class="close" onclick="fechaImprChaves();">&times;</span>
                <h5 style="text-align: center;color: #666;">Controle das Chaves Lacradas</h5>
                <h6 style="text-align: center; padding-bottom: 18px; color: #666;">Gerar PDF</h6>
                <div style="border: 2px solid; border-radius: 10px; padding: 10px; text-align: center;">
                    <input type="button" id="imprRelChave" class="resetbot fundoAzul2" style="font-size: 80%;" value="Relação completa do claviculário das Chaves Lacradas">
                </div>
                <div style="margin-top: 5px; border: 2px solid; border-radius: 10px; padding: 10px;">
                    <div style="text-align: center; color: #666;">Relação completa com movimentação</div>
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
                
                <div style="margin-top: 5px; border: 2px solid; border-radius: 10px; padding: 10px;">
                    <div style="text-align: center; color: #666;">Relação das Chaves que tiveram movimentação</div>
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td style="text-align: right;"><label style="font-size: 80%;">Mensal - Selecione o Mês/Ano: </label></td>
                            <td>
                                <select id="selecMesAnoMov" style="font-size: 1rem; width: 90px;" title="Selecione o período.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpcoesEscMesMov){
                                        while ($Opcoes = pg_fetch_row($OpcoesEscMesMov)){ ?>
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
                                <select id="selecAnoMov" style="font-size: 1rem; width: 90px;" title="Selecione o Ano.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpcoesEscAnoMov){
                                        while ($Opcoes = pg_fetch_row($OpcoesEscAnoMov)){ ?>
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

        <div id="modalDevolvida" class="relacmodal">
            <div class="modal-content-tarjaAzul corPreta">
                <span class="close" onclick="fechaDevolv();">&times;</span>
                <div id="msgdevolv" style="font-size: 300%; text-align: center;">Chave DEVOLVIDA</div>
                <div id="msgUsuDevolv" style="color: white; font-size: 110%; text-align: center;"></div>
            </div>
        </div> <!-- Fim Modal-->

        <div id="modalAlerta" class="relacmodal">
            <div class="modal-content-tarjaVerm">
                <span class="close" onclick="fechaAlerta();">&times;</span>
                <div id="msgAlerta1" style="color: white; font-size: 300%; text-align: center;">Chave AUSENTE</div>
                <div id="msgAlerta2" style="color: white; font-size: 150%; text-align: center;"></div>
                <div id="msgAlerta3" style="color: white; font-size: 150%; text-align: center;"></div>
                <div id="msgAlerta4" style="color: white; font-size: 150%; text-align: center;"></div>
            </div>
        </div> <!-- Fim Modal-->

        <div id="modalInfo" class="relacmodal">
            <div class="modal-content-tarjaAzul corPreta">
                <span class="close" onclick="fechaInfo();">&times;</span>
                <div id="msgInfo1" style="font-size: 300%; text-align: center;">Sem Vínculo</div>
                <div id="msgInfo2" style="font-size: 150%; text-align: center;"></div>
                <div id="msgInfo3" style="text-align: center;"></div>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para escolher chaves por usuário  -->
        <div id="relacmodalChaves" class="relacmodal"> 
            <div class="modal-content-Chaves corPreta">
                <span class="close" onclick="fechaModalChaves();">&times;</span>
                <div style="text-align: center;"><h6>Claviculário Chaves Lacradas</h6></div>
                <div id="nomeUsuChaves" style="text-align: center;"></div>
                <div id="faixachaves"></div>
            </div>
        </div> <!-- Fim Modal-->

        <div id="carregaTema"></div> <!-- carrega a pág modulos/config/carTema.php - onde está a função mudaTema() -->

    </body>
</html>