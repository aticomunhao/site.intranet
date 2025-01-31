<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
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
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="class/bootstrap/js/bootstrap.min.js"></script>
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="comp/js/jquery-ui.min.js"></script>
        <script src="comp/js/jquery.mask.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script>   <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <style>
             .relacmodalMovel{
                display: none; /* oculto default */
                position: fixed;
                min-width: 800px;
                z-index: 200;
                left: 20%;
                top: 120px;
                border-radius: 15px;
                overflow: auto; /* autoriza scroll se necessário */
                background-color: rgb(0,0,0); /* Fallback color */
                background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
            }

            .modal-content-relacHorarioMovel{
                background: linear-gradient(180deg, white, #00BFFF);
                margin: 2% auto;
                padding: 5px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 97%;
                overflow: auto;
            }
            .modal-content-relacParticipMovel{
                background: linear-gradient(180deg, white, #00BFFF);
                margin: 5% auto;
                padding: 10px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 95%;
                overflow: auto;
            }

            .modal-content-relacParticip{
                background: linear-gradient(180deg, white, #00BFFF);
                margin: 12% auto;
                padding: 10px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%;
            }
            .modal-content-escalaControle{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 70%;
            }
            .modal-content-relacHorario{
                background: linear-gradient(180deg, white, #00BFFF);
                margin: 12% auto;
                padding: 10px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%;
            }
            .modal-content-relacDescanso{
                background: linear-gradient(180deg, white,rgb(221, 243, 203));
                margin: 12% auto;
                padding: 10px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 50%;
            }
            .modal-content-destacaDia{
                background: linear-gradient(180deg, white, #00BFFF);
                margin: 12% auto;
                padding: 10px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 30%;
            }
            .modal-content-relacNotas{
                background: linear-gradient(180deg, white, #00BFFF);
                margin: 12% auto;
                padding: 10px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%;
            }
            .modal-content-relacFeriados{
                background: linear-gradient(180deg, white, #00BFFF);
                margin: 12% auto;
                padding: 10px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 40%;
            }
             .quadrodia {
                font-size: 90%;
                min-width: 30px;
                border: 1px solid;
                border-radius: 3px;
            }
            .quadrodiaCinza {
                font-size: 90%;
                min-width: 30px;
                border: 1px solid;
                border-radius: 3px;
                background-color: #E8E8E8;
            }
            .quadrodiaClick {
                font-size: 90%;
                min-width: 30px;
                border: 1px solid;
                border-radius: 3px;
                cursor: pointer;
            }
            .quadrodiaClickCinza {
                font-size: 90%;
                min-width: 30px;
                border: 1px solid;
                border-radius: 3px;
                cursor: pointer;
                background-color: #E8E8E8;
            }
            .quadroletra {
                text-align: center;
                font-size: 90%;
                min-width: 20px;
                border: 1px solid;
                border-radius: 3px;
            }
            .quadroletraYellow {
                text-align: center;
                font-size: 90%;
                min-width: 10px;
                border: 1px solid;
                border-radius: 3px;
                background-color: yellow;
            }
            .quadroletraBlue {
                text-align: center;
                font-size: 90%;
                min-width: 10px;
                border: 1px solid;
                border-radius: 3px;
                background-color: #00BFFF;
            }
            .quadroletraGreen {
                text-align: center;
                font-size: 90%;
                min-width: 10px;
                border: 1px solid;
                border-radius: 3px;
                background-color: #00FF7F;
            }
            .quadroCinza {
                font-size: 80%;
                border: 1px solid #838B8B;
                color: #838B8B;
            }
            .bContainer{ /* encapsula uma frase no topo de uma div */
                position: absolute; 
                right: 50px;
                margin-top: -10px; 
                border: 1px solid blue;
                background-color: blue;
                color: white;
                cursor: pointer;
                border-radius: 10px; 
                padding-left: 10px; 
                padding-right: 10px; 
            }
            .modal-content-relacTurnos{
                background: linear-gradient(180deg, white,rgb(203, 229, 236));
                margin: 12% auto;
                padding: 10px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 40%;
            }
        </style>
        <script>
            $(document).ready(function(){
                //Não vai liberar para os escalados - só o pdf impresso
                document.getElementById("evliberames").style.visibility = "hidden"; 
                document.getElementById("etiqevliberames").style.visibility = "hidden"; 
                document.getElementById("selecGrupo").style.visibility = "hidden"; 
                document.getElementById("etiqGrupo").style.visibility = "hidden"; 
                document.getElementById("imgEscalaConfig").style.visibility = "hidden";

//                if(parseInt(document.getElementById("escalante").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){ // Escalante e Superusuário
                if(parseInt(document.getElementById("escalante").value) === 1){ // Escalante
                    document.getElementById("imgEscalaConfig").style.visibility = "visible"; 
                }
                if(parseInt(document.getElementById("fiscal").value) === 1){ // Fiscal das escalas
                    document.getElementById("selecGrupo").style.visibility = "visible"; 
                    document.getElementById("etiqGrupo").style.visibility = "visible"; 
                }
                document.getElementById("selecMesAnoEsc").value = document.getElementById("guardamesano").value;

                if(parseInt(document.getElementById("liberadoefetivo").value) === 0 && parseInt(document.getElementById("escalante").value) === 0 && parseInt(document.getElementById("fiscal").value) === 0){
                    $("#faixacentral").load("modulos/escaladaf/infoAgd1.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                    $("#faixaquadro").load("modulos/escaladaf/infoAgd2.php");
                    $("#faixacarga").load("modulos/escaladaf/infoAgd2.php");
                    $("#faixanotas").load("modulos/escaladaf/infoAgd3.php");
                    $("#faixaferiados").load("modulos/escaladaf/infoAgd2.php");
                     document.getElementById("botImprimir").style.visibility = "hidden";
                     document.getElementById("transfMesAnoEsc").style.visibility = "hidden";
                }else{
                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                    $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                    $("#faixacarga").load("modulos/escaladaf/jCargaDaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                    $("#faixanotas").load("modulos/escaladaf/notasdaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                    $("#faixaferiados").load("modulos/escaladaf/relFeriados.php");
                    document.getElementById("botImprimir").style.visibility = "visible";
                    if(parseInt(document.getElementById("escalante").value) === 1){
                        document.getElementById("transfMesAnoEsc").style.visibility = "visible";
                    }
                }

                $("#edinterv").mask("99:99");
//                $("#insdata").mask("99/99");

                $("#selecMesAnoEsc").change(function(){
                    if(parseInt(document.getElementById("selecMesAnoEsc").value) > 0){
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvamesano&mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value), true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText);
                                        Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                        if(parseInt(Resp.coderro) === 0){
                                            if(parseInt(Resp.mesliberado) === 0 && parseInt(document.getElementById("escalante").value) === 0 && parseInt(document.getElementById("fiscal").value) === 0){
                                                $("#faixacentral").load("modulos/escaladaf/infoAgd1.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                                                $("#faixaquadro").load("modulos/escaladaf/infoAgd2.php");
                                                $("#faixanotas").load("modulos/escaladaf/infoAgd3.php");
                                                $("#faixaferiados").load("modulos/escaladaf/infoAgd2.php");
                                                document.getElementById("botImprimir").style.visibility = "hidden";
                                            }else{
                                                $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                $("#faixacarga").load("modulos/escaladaf/jCargaDaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                $("#faixanotas").load("modulos/escaladaf/notasdaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                $("#faixaferiados").load("modulos/escaladaf/relFeriados.php");
                                                document.getElementById("botImprimir").style.visibility = "visible";
                                            }
                                            if(parseInt(Resp.mesliberado) === 0){
                                                document.getElementById("evliberames").checked = false;
                                            }else{
                                                document.getElementById("evliberames").checked = true;
                                            }
                                        }else{
                                            alert("Houve um erro no servidor.")
                                        }
                                    }
                                }
                            };
                            ajax.send(null);
                        }
                    }
                });

                //Transfere de um mês para outro
                $("#transfMesAnoEsc").change(function(){
                    if(document.getElementById("transfMesAnoEsc").value == document.getElementById("selecMesAnoEsc").value){
                        $.confirm({
                            title: 'Ação Suspensa!',
                            content: 'Selecione outro mês.',
                            draggable: true,
                            buttons: {
                                OK: function(){}
                            }
                        });
                        document.getElementById("transfMesAnoEsc").value = "";
                        return false;
                    }

                    if(parseInt(document.getElementById("transfMesAnoEsc").value) > parseInt(document.getElementById("selecMesAnoEsc").value)+1){
                        $.confirm({
                            title: 'Ação Suspensa!',
                            content: 'Pulando mês.',
                            draggable: true,
                            buttons: {
                                OK: function(){}
                            }
                        });
                        document.getElementById("transfMesAnoEsc").value = "";
                        return false;
                    }
                    if(parseInt(document.getElementById("selecMesAnoEsc").value) === 12 && parseInt(document.getElementById("transfMesAnoEsc").value) > 1){
                        $.confirm({
                            title: 'Ação Suspensa!',
                            content: 'Pulando mês.',
                            draggable: true,
                            buttons: {
                                OK: function(){}
                            }
                        });
                        document.getElementById("transfMesAnoEsc").value = "";
                        return false;
                    }

                    if(parseInt(document.getElementById("transfMesAnoEsc").value) > 0){
                        $.confirm({
                            title: 'Transferir escala.',
                            content: 'Se houver lançamentos no mês de destino ('+document.getElementById("transfMesAnoEsc").value+') eles serão perdidos.<br>Confirma transferir?',
                            autoClose: 'Não|10000',
                            draggable: true,
                            buttons: {
                                Sim: function () {
                                    ajaxIni();
                                    if(ajax){
                                        ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=transfmesano&mesano="+encodeURIComponent(document.getElementById("transfMesAnoEsc").value)
                                        +"&transfde="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value), true);
                                        ajax.onreadystatechange = function(){
                                            if(ajax.readyState === 4 ){
                                                if(ajax.responseText){
//alert(ajax.responseText);
                                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                                    if(parseInt(Resp.coderro) === 2){
                                                            $.confirm({
                                                                title: 'Ação Suspensa!',
                                                                content: 'O preenchimento não está completo.',
                                                                draggable: true,
                                                                buttons: {
                                                                    OK: function(){}
                                                                }
                                                            });
                                                            document.getElementById("transfMesAnoEsc").value = "";
                                                            return false;
                                                    }
                                                    if(parseInt(Resp.coderro) === 0){
                                                        if(parseInt(Resp.mesliberado) === 0 && parseInt(document.getElementById("escalante").value) === 0){
                                                            $("#faixacentral").load("modulos/escaladaf/infoAgd1.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                            $("#faixaquadro").load("modulos/escaladaf/infoAgd2.php");
                                                            $("#faixanotas").load("modulos/escaladaf/infoAgd3.php");
                                                            $("#faixaferiados").load("modulos/escaladaf/infoAgd2.php");
                                                            document.getElementById("botImprimir").style.visibility = "hidden";
                                                        }else{
                                                            $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                            $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                            $("#faixacarga").load("modulos/escaladaf/jCargaDaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                            $("#faixanotas").load("modulos/escaladaf/notasdaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                            $("#faixaferiados").load("modulos/escaladaf/relFeriados.php");
                                                            document.getElementById("selecMesAnoEsc").value = document.getElementById("transfMesAnoEsc").value;
                                                            document.getElementById("transfMesAnoEsc").value = "";
                                                            document.getElementById("botImprimir").style.visibility = "visible";
                                                        }
                                                        if(parseInt(Resp.mesliberado) === 0){
                                                            document.getElementById("evliberames").checked = false;
                                                        }else{
                                                            document.getElementById("evliberames").checked = true;
                                                        }
                                                    }else{
                                                        alert("Houve um erro no servidor.")
                                                    }
                                                }
                                            }
                                        };
                                        ajax.send(null);
                                    }
                                },
                                Não: function () {
                                    document.getElementById("transfMesAnoEsc").value = "";
                                }
                            }
                        });
                    }
                });

                $("#configSelecEscala").change(function(){
                    if(document.getElementById("configSelecEscala").value == ""){
                        document.getElementById("configCpfEscala").value = "";
                        document.getElementById("checkefetivo").checked = false;
                        document.getElementById("checkescalante").checked = false;
//                        document.getElementById("checkEncarreg").checked = false;
//                        document.getElementById("checkChefeADM").checked = false;
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=buscausuario&codigo="+document.getElementById("configSelecEscala").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configCpfEscala").value = format_CnpjCpf(Resp.cpf);
                                        if(parseInt(Resp.eft) === 1){
                                            document.getElementById("checkefetivo").checked = true;
                                        }else{
                                            document.getElementById("checkefetivo").checked = false;
                                        }
                                        if(parseInt(Resp.esc) === 1){
                                            document.getElementById("checkescalante").checked = true;
                                        }else{
                                            document.getElementById("checkescalante").checked = false;
                                        }
//                                        if(parseInt(Resp.encarreg) === 1){
//                                            document.getElementById("checkEncarreg").checked = true;
//                                        }else{
//                                            document.getElementById("checkEncarreg").checked = false;
//                                        }
//                                        if(parseInt(Resp.chefeadm) === 1){
//                                            document.getElementById("checkChefeADM").checked = true;
//                                        }else{
//                                            document.getElementById("checkChefeADM").checked = false;
//                                        }
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#configCpfEscala").click(function(){
                    document.getElementById("configSelecEscala").value = "";
                    document.getElementById("configCpfEscala").value = "";
                    document.getElementById("checkefetivo").checked = false;
                    document.getElementById("checkescalante").checked = false;
//                    document.getElementById("checkEncarreg").checked = false;
//                    document.getElementById("checkChefeADM").checked = false;
                });

                $("#configCpfEscala").change(function(){
                    document.getElementById("configSelecEscala").value = "";
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=buscacpf&cpf="+encodeURIComponent(document.getElementById("configCpfEscala").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configSelecEscala").value = Resp.PosCod;
                                        document.getElementById("configCpfEscala").value = format_CnpjCpf(Resp.cpf);
                                        if(parseInt(Resp.eft) === 1){
                                            document.getElementById("checkefetivo").checked = true;
                                        }else{
                                            document.getElementById("checkefetivo").checked = false;
                                        }
                                        if(parseInt(Resp.esc) === 1){
                                            document.getElementById("checkescalante").checked = true;
                                        }else{
                                            document.getElementById("checkescalante").checked = false;
                                        }
//                                        if(parseInt(Resp.encarreg) === 1){
//                                            document.getElementById("checkEncarreg").checked = true;
//                                        }else{
//                                            document.getElementById("checkEncarreg").checked = false;
//                                        }
//                                        if(parseInt(Resp.chefeadm) === 1){
//                                            document.getElementById("checkChefeADM").checked = true;
//                                        }else{
//                                            document.getElementById("checkChefeADM").checked = false;
//                                        }
                                    }
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.");
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("checkefetivo").checked = false;
                                        document.getElementById("checkescalante").checked = false;
//                                        document.getElementById("checkEncarreg").checked = false;
//                                        document.getElementById("checkChefeADM").checked = false;
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

                $("#configSelecChefeDiv").change(function(){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvachefediv&codigo="+document.getElementById("configSelecChefeDiv").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });
                $("#configSelecEncarreg").change(function(){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaencarreg&codigo="+document.getElementById("configSelecEncarreg").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#selecGrupo").change(function(){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=trocagrupo&grupo="+document.getElementById("selecGrupo").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.");
                                    }else{
                                        document.getElementById("guardanumgrupo").value = document.getElementById("selecGrupo").value;
                                        document.getElementById("etiqSiglaGrupo").innerHTML = "Escala "+Resp.siglagrupo;
                                        $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                        $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                        $("#faixacarga").load("modulos/escaladaf/jCargaDaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                        $("#faixanotas").load("modulos/escaladaf/notasdaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                        $("#faixaferiados").load("modulos/escaladaf/relFeriados.php");
                                        document.getElementById("botImprimir").style.visibility = "visible";
                                        document.getElementById("transfMesAnoEsc").style.visibility = "visible";
                                        //Verificar se é igual Meugrupo e Numgrupo
                                        if(parseInt(document.getElementById("escalante").value) === 1 && parseInt(document.getElementById("guardanumgrupo").value) === parseInt(document.getElementById("guardameugrupo").value)){ // Se for Escalante e está no próprio grupo
                                            document.getElementById("imgEscalaConfig").style.visibility = "visible";
                                            document.getElementById("etiqtransfMesAnoEsc").style.visibility = "visible";
                                            document.getElementById("transfMesAnoEsc").style.visibility = "visible";
                                        }else{
                                            document.getElementById("imgEscalaConfig").style.visibility = "hidden";
                                            document.getElementById("etiqtransfMesAnoEsc").style.visibility = "hidden";
                                            document.getElementById("transfMesAnoEsc").style.visibility = "hidden";
                                        }
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                modalParticip = document.getElementById('relacParticip'); //span[0]
                window.onclick = function(event){
                    if(event.target === modalParticip){
                        modalParticip.style.display = "none";
                    }
                };

            }); // fim do ready

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

            function abreEdit(DiaId, DataDia){
                // Com a caixa flutuante - fechar se já estiver aberto e o usuário clicar em outro dia
                if(document.getElementById("relacParticip").style.display == "block"){
                    document.getElementById("relacParticip").style.display = "none";
                    return false;
                }
                document.getElementById("guardaDiaId").value = DiaId; // id do dia em escaladaf
                document.getElementById("titulomodal").innerHTML = DataDia;
                $("#relacaoParticip").load("modulos/escaladaf/equipe.php?diaid="+DiaId+"&numgrupo="+document.getElementById("guardanumgrupo").value);
                document.getElementById("relacParticip").style.display = "block";
            }

            function abreEditHorario(){
                $("#relacaoHorarios").load("modulos/escaladaf/edHorarios.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                document.getElementById("relacQuadroHorario").style.display = "block";
            }

            function abreEditDescanso(){
                $("#relacaoDescanso").load("modulos/escaladaf/edDescanso.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                document.getElementById("relacQuadroDescanso").style.display = "block";
            }

            function abreEditFeriados(){
                $("#relacaoFeriados").load("modulos/escaladaf/edFeriados.php");
                document.getElementById("relacQuadroFeriados").style.display = "block";
            }

            function editaOrdem(Cod, Valor){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaordem&codigo="+Cod+"&valor="+Valor, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#relacaoHorarios").load("modulos/escaladaf/edHorarios.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                    $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function editaFolga(Cod, Valor){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvafolga&codigo="+Cod+"&valor="+Valor, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    //recarregar para salvar horários onde for null
                                    $("#relacaoDescanso").load("modulos/escaladaf/edDescanso.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function editaLetra(Cod, Valor){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaletra&codigo="+Cod+"&valor="+Valor, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $.confirm({
                                        title: 'Valor Salvo!',
                                        content: '',
                                        autoClose: 'OK|1000',
                                        draggable: true,
                                        buttons: {
                                            OK: function(){}
                                        }
                                    });
                                    $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function editaInterv(Cod, Valor){
                let INTERV_LENGTH = 5;
                if (Valor.length !== INTERV_LENGTH) {
                    $.confirm({
                        title: 'Ação Suspensa!',
                        content: 'Observe o formato 00:00',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaInterv&codigo="+Cod+"&valor="+encodeURIComponent(Valor), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#relacaoHorarios").load("modulos/escaladaf/edHorarios.php?numgrupo="+document.getElementById("guardanumgrupo").value);

                                    $.confirm({
                                        title: 'Valor Salvo!',
                                        content: '',
                                        autoClose: 'OK|1000',
                                        draggable: true,
                                        buttons: {
                                            OK: function(){}
                                        }
                                    });
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaLetra__(){
                if(document.getElementById("insordem").value == ""){
                    return false;
                }
                if(document.getElementById("insletra").value == ""){
                    return false;
                }
                if(document.getElementById("insturno").value == ""){
                    return false;
                }

                let Turno = document.getElementById("insturno").value;
                let Valor_Length = 13;
                if (Turno.length !== Valor_Length) {
                    $.confirm({
                        title: 'Ação Suspensa!',
                        content: 'Observe o formato: 00:00 / 00:00',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }

                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=insereletra&ordem="+document.getElementById("insordem").value
                    +"&insletra="+encodeURIComponent(document.getElementById("insletra").value)
                    +"&insturno="+encodeURIComponent(document.getElementById("insturno").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#relacaoHorarios").load("modulos/escaladaf/edHorarios.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                    $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                    document.getElementById("abreinsletra").style.visibility = "visible";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaLetra(){
                if(document.getElementById("insordem").value == ""){
                    return false;
                }
                if(document.getElementById("insletra").value == ""){
                    return false;
                }
//                if(document.getElementById("insturno").value == ""){
//                    return false;
//                }
//                let Turno = document.getElementById("insturno").value;
//                let Valor_Length = 13;
//                if (Turno.length !== Valor_Length) {
//                    $.confirm({
//                        title: 'Ação Suspensa!',
//                        content: 'Observe o formato: 00:00 / 00:00',
//                        draggable: true,
//                        buttons: {
//                            OK: function(){}
//                        }
//                    });
//                    return false;
//                }

                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=insereletra&ordem="+document.getElementById("insordem").value
                    +"&insletra="+encodeURIComponent(document.getElementById("insletra").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#relacaoHorarios").load("modulos/escaladaf/edHorarios.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                    $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                    document.getElementById("abreinsletra").style.visibility = "visible";
                                    abreQuadroTurnos(Resp.codigonovo);
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function apagaLetra(Cod){
                $.confirm({
                    title: 'Apagar turno.',
                    content: 'Confirma apagar este turno?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=apagaletra&codigo="+Cod, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                            if(parseInt(Resp.coderro) > 0){
                                                alert("Houve um erro no servidor.");
                                            }else{
                                            }
                                            $("#relacaoHorarios").load("modulos/escaladaf/edHorarios.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                            $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
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


//Sem uso no momento
//            function abreDestacaDia(){ // está em relEsc_daf.php
//                $("#relacaoDias").load("modulos/escaladaf/destacDia.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value) );
//                document.getElementById("relacDestacaDia").style.display = "block";
//            }
//            function fechaDestacaDia(){
//                document.getElementById("relacDestacaDia").style.display = "none";
//            }

            function MarcaDia(Cod){ 
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=marcaDia&codigo="+Cod, true);
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
            
            function marcaTurno(Cod, Cor){ 
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=marcaTurno&codigo="+Cod+"&cor="+Cor, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#relacaoHorarios").load("modulos/escaladaf/edHorarios.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                    $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function marcaVale(obj, Cod){
                if(obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=marcaVale&codigo="+Cod+"&valor="+Valor, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#relacaoHorarios").load("modulos/escaladaf/edHorarios.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function editaNota(Cod){
                document.getElementById("guardacod").value = Cod;
                document.getElementById("mudou").value = "0";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=buscanota&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("numnota").value = Resp.numnota;
                                    document.getElementById("textonota").value = Resp.textonota;
                                    document.getElementById("relacQuadroNotas").style.display = "block";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function InsereNota(){
                document.getElementById("guardacod").value = 0;
                document.getElementById("mudou").value = "0";
                document.getElementById("numnota").value = "";
                document.getElementById("textonota").value = "";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=buscanumnota", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro ao calcular o novo número.");
                                }else{
                                    document.getElementById("numnota").value = Resp.numnota;
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
                document.getElementById("relacQuadroNotas").style.display = "block";
            }

            function salvaNota(){
                if(document.getElementById("mudou").value == "0"){
                    document.getElementById("relacQuadroNotas").style.display = "none";
                    return false;
                }
                if(document.getElementById("numnota").value == ""){
                    return false;
                }
                if(document.getElementById("textonota").value == ""){
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvanota&codigo="+document.getElementById("guardacod").value
                    +"&numnota="+document.getElementById("numnota").value
                    +"&textonota="+encodeURIComponent(document.getElementById("textonota").value)
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("relacQuadroNotas").style.display = "none";
                                    $("#faixanotas").load("modulos/escaladaf/notasdaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }

            }
            
            function MarcaPartic(Cod){ // vem de equipe.php
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=marcaPartic&codigo="+Cod, true);
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

            function salvaDataFer(){
                if(document.getElementById("insdata").value == ""){
                    return false;
                }
                if(document.getElementById("insdescr").value == ""){
                    return false;
                }
                let Turno = document.getElementById("insdata").value;
                let Valor_Length = 5;
                if (Turno.length !== Valor_Length) {
                    $.confirm({
                        title: 'Ação Suspensa!',
                        content: 'Observe o formato: 00/00',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }

                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=insereFeriado&insdata="+document.getElementById("insdata").value
                    +"&insdescr="+encodeURIComponent(document.getElementById("insdescr").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#relacaoFeriados").load("modulos/escaladaf/edFeriados.php");
                                    $("#faixaferiados").load("modulos/escaladaf/relFeriados.php");
                                    document.getElementById("inserirData").style.display = "none";
                                    document.getElementById("abreinsData").style.visibility = "visible";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function apagaDataFer(Cod){
                $.confirm({
                    title: 'Apagar Data.',
                    content: 'Confirma apagar este feriado?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=apagadatafer&codigo="+Cod, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                            if(parseInt(Resp.coderro) > 0){
                                                alert("Houve um erro no servidor.");
                                            }else{
                                                $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                $("#relacaoFeriados").load("modulos/escaladaf/edFeriados.php");
                                                $("#faixaferiados").load("modulos/escaladaf/relFeriados.php");
                                            }
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

            function marcaConfigEscala(obj, Campo){
                if(obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                if(document.getElementById("configSelecEscala").value == ""){
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
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=configMarcaEscala&codigo="+document.getElementById("configSelecEscala").value
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
                                            content: 'Não restaria outro marcado para gerenciar a escala.',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                        return false;
                                    }else if(parseInt(Resp.coderro) === 3){
                                        obj.checked = false;
                                        $.confirm({
                                            title: 'Atenção!',
                                            content: 'Usuário participa de outra escala:<br>'+Resp.outrogrupo+".<br>Solicite à ATI modificar o grupo para fins de escala, se for o caso.",
                                            autoClose: 'OK|15000',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                        return false;
                                    }else{
                                        $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                        $("#faixacarga").load("modulos/escaladaf/jCargaDaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
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

            function mudaTurno(CodPartic, CodTurno){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaTurnoParticip&codpartic="+CodPartic+"&codturno="+CodTurno, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
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
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=insParticipante"
                    +"&diaIdEscala="+document.getElementById("guardaDiaId").value
                    +"&numgrupo="+document.getElementById("guardanumgrupo").value, true);
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
                                        content: 'É necessário inserir os horários dos turnos.',
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
                                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                    $("#faixacarga").load("modulos/escaladaf/jCargaDaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                    document.getElementById("relacParticip").style.display = "none";
                                }else{
                                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                    $("#faixacarga").load("modulos/escaladaf/jCargaDaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                    document.getElementById("relacParticip").style.display = "none";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function abreEscalaConfig(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=procChefeDiv", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("checkefetivo").checked = false;
                                    document.getElementById("checkescalante").checked = false;
                                    document.getElementById("configSelecChefeDiv").value = Resp.chefe;
                                    document.getElementById("configSelecEncarreg").value = Resp.encarreg;
                                    document.getElementById("configCpfEscala").value = "";
                                    document.getElementById("configSelecEscala").value = "";
                                    document.getElementById("modalEscalaConfig").style.display = "block";
                               }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function fechaEscalaConfig(){
                document.getElementById("modalEscalaConfig").style.display = "none";
            }
            function fechaRelaPart(){
                document.getElementById("relacParticip").style.display = "none";
            }
            function fechaQuadroHorario(){
                document.getElementById("relacQuadroHorario").style.display = "none";
            }
            function fechaQuadrodescanso(){
                document.getElementById("relacQuadroDescanso").style.display = "none";
            }
            function fechaQuadroNotas(){
                document.getElementById("relacQuadroNotas").style.display = "none";
            }
            function fechaQuadroFeriados(){
                document.getElementById("relacQuadroFeriados").style.display = "none";
            }

            function resumoUsuEscala(){
                window.open("modulos/escaladaf/imprUsuEsc.php?acao=listaUsuarios&numgrupo="+document.getElementById("guardanumgrupo").value, "EscalaUsu");
            }
            function imprPlanilha(){
                window.open("modulos/escaladaf/imprEscDaf.php?acao=imprPlan&mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value)+"&numgrupo="+document.getElementById("guardanumgrupo").value, document.getElementById("selecMesAnoEsc").value);
            }
            function imprDescanso(){
                window.open("modulos/escaladaf/imprDescanso.php?acao=imprDescanso&mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value)+"&numgrupo="+document.getElementById("guardanumgrupo").value, "Folga");
            }
            function modif(){ // assinala se houve qualquer modificação nos campos do modal durante a edição para evitar salvar desnecessariamente
                document.getElementById("mudou").value = "1";
            }
            function foco(id){
                document.getElementById(id).focus();
            }

            function liberaMes(obj){
                if(obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=liberaMes&valor="+Valor+"&mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value), true);
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

            function abreQuadroTurnos(Cod){
                document.getElementById("guardaCodTurno").value = Cod;
                document.getElementById("textoTurno").style.display = "none";
                document.getElementById("selecTurno").style.display = "block";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=buscaTurno&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    if(parseInt(Resp.infotexto) === 0){
                                        document.getElementById("boxtextoTurno").checked = false;
                                        document.getElementById("etiqletra").innerHTML = Resp.letra;
                                        document.getElementById("selecHor1").value = Resp.turno1Hor;
                                        document.getElementById("selecMin1").value = Resp.turno1Min;
                                        document.getElementById("selecHor2").value = Resp.turno2Hor;
                                        document.getElementById("selecMin2").value = Resp.turno2Min;
                                    }else{
                                        document.getElementById("boxtextoTurno").checked = true;
                                        document.getElementById("etiqletra").innerHTML = Resp.letra;
                                        document.getElementById("textoTurno").value = Resp.turno;
                                        document.getElementById("selecTurno").style.display = "none";
                                        document.getElementById("textoTurno").style.display = "block";
                                        document.getElementById("textoTurno").focus();
                                    }
                                }
                                document.getElementById("relacQuadroTurnos").style.display = "block";
                                document.getElementById("botsalvaturno").focus();
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function abreTexto(obj){
                if(obj.checked === true){
                    document.getElementById("selecTurno").style.display = "none";
                    document.getElementById("textoTurno").style.display = "block";
                }else{
                    document.getElementById("selecTurno").style.display = "block";
                    document.getElementById("textoTurno").style.display = "none";
                }
            }

            function salvaEditTurno(){
                if(document.getElementById("mudou").value == "0"){
                    document.getElementById("relacQuadroTurnos").style.display = "none";
                    return false;
                }
                if(document.getElementById("boxtextoTurno").checked == false){
                    InfoTexto = 0;
                    if(parseInt(document.getElementById("selecHor2").value) < parseInt(document.getElementById("selecHor1").value)){
                        $.confirm({
                            title: 'Ação Suspensa!',
                            content: 'Verifique a hora do final do turno.',
                            draggable: true,
                            buttons: {
                                OK: function(){}
                            }
                        });
                        return false;
                    }
                    Turno = document.getElementById("selecHor1").value+":"+document.getElementById("selecMin1").value+" / "+document.getElementById("selecHor2").value+":"+document.getElementById("selecMin2").value
                }else{ // só texto
                    InfoTexto = 1;
                    Turno = document.getElementById("textoTurno").value;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaEditaTurno&codigo="+document.getElementById("guardaCodTurno").value
                    +"&numgrupo="+document.getElementById("guardanumgrupo").value
                    +"&turno="+encodeURIComponent(Turno)+"&infotexto="+InfoTexto
                    +"&mesano="+document.getElementById("guardamesano").value
                    +"&letra="+document.getElementById("etiqletra").innerHTML, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("relacQuadroTurnos").style.display = "none";
                                    $("#relacaoHorarios").load("modulos/escaladaf/edHorarios.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                    $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                    $("#faixacarga").load("modulos/escaladaf/jCargaDaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                               }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function abreExcel(){
                $.confirm({
                    title: 'Exportar',
                    content: 'Confirma criar arquivo Excel?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/criaExcel_daf.php?acao=listaturnos&numgrupo="+document.getElementById("guardanumgrupo").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    //Salva arquivo xlsx
                                    $(location).attr("href", "modulos/escaladaf/ListaTurnos.xlsx");
                                    $.confirm({
                                        title: 'Sucesso',
                                        content: 'Arquivo baixado para o diretório de downloads.',
                                        draggable: true,
                                        buttons: {
                                            OK: function(){}
                                        }
                                    });
                                }
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

            function fechaQuadroTurnos(){
                document.getElementById("relacQuadroTurnos").style.display = "none";
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

            jQuery(function($){
                $("#relacParticip").draggable();
//                $("#relacQuadroHorario").draggable();
            });

        </script>
    </head>
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            //Meu grupo não varia, embora tenha poderes para ver outros grupos
            $MeuGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
            if($MeuGrupo == 0){ // está sem grupo
                $rs0 = pg_query($Conec, "SELECT MIN(id) FROM ".$xProj.".escalas_gr;");
                $row0 = pg_num_rows($rs0);
                if($row0 > 0){
                    $tbl0 = pg_fetch_row($rs0);
                    $MeuGrupo = $tbl0[0];
                }    
            }

            //NumGrupo pode variar se é Fiscal de grupos
            $NumGrupo = parEsc("esc_grupo", $Conec, $xProj, $_SESSION["usuarioID"]);
            if($NumGrupo == 0){ // está sem grupo
                $rs0 = pg_query($Conec, "SELECT MIN(id) FROM ".$xProj.".escalas_gr;");
                $row0 = pg_num_rows($rs0);
                if($row0 > 0){
                    $tbl0 = pg_fetch_row($rs0);
                    $NumGrupo = $tbl0[0];
                }    
            }
            $rs = pg_query($Conec, "SELECT siglagrupo FROM ".$xProj.".escalas_gr WHERE id = $NumGrupo;");
            $row = pg_num_rows($rs);
            if($row > 0){
                $tbl = pg_fetch_row($rs);
                $SiglaGrupo = $tbl[0];
            }else{
                $SiglaGrupo = "";
            }

//Provisórios
//    pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf");
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf (
        id SERIAL PRIMARY KEY, 
        dataescala date DEFAULT '3000-12-31',
        grupo_id integer NOT NULL DEFAULT 0, 
        feriado smallint NOT NULL DEFAULT 0, 
        ativo smallint DEFAULT 1 NOT NULL, 
        liberames smallint NOT NULL DEFAULT 0, 
        usuins integer DEFAULT 0 NOT NULL,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit integer DEFAULT 0 NOT NULL,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");

//    $rs1 = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'escaladaf_ins' AND COLUMN_NAME = 'destaque'");
//    $row1 = pg_num_rows($rs1);
//    if($row1 == 0){
//        pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf_ins");
//    }

    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_ins (
        id SERIAL PRIMARY KEY, 
        escaladaf_id bigint NOT NULL DEFAULT 0,
        grupo_ins integer NOT NULL DEFAULT 0, 
        dataescalains date DEFAULT '3000-12-31',
        poslog_id INT NOT NULL DEFAULT 0,
        letraturno VARCHAR(3), 
        turnoturno VARCHAR(30), 
        destaque smallint NOT NULL DEFAULT 0,
        marcadaf smallint NOT NULL DEFAULT 0,
        ativo smallint NOT NULL DEFAULT 1, 
        cargatime time without time zone NOT NULL DEFAULT '00:00', 
        usuins bigint NOT NULL DEFAULT 0, 
        datains timestamp without time zone DEFAULT '3000-12-31', 
        usuedit bigint NOT NULL DEFAULT 0, 
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        )
    ");


//    pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf_turnos");
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_turnos (
        id SERIAL PRIMARY KEY, 
        grupo_turnos integer NOT NULL DEFAULT 0, 
        letra VARCHAR(3), 
        horaturno VARCHAR(30), 
        calcdataini timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
        calcdatafim timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
        interv time without time zone NOT NULL DEFAULT '00:00',
        cargacont time without time zone NOT NULL DEFAULT '00:00',
        cargahora time without time zone NOT NULL DEFAULT '00:00',
        ordemletra smallint NOT NULL DEFAULT 0,
        destaq smallint NOT NULL DEFAULT 0,
        ativo smallint NOT NULL DEFAULT 1, 
        usuins bigint NOT NULL DEFAULT 0,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit bigint NOT NULL DEFAULT 0,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");
    $rs2 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_turnos WHERE grupo_turnos = $NumGrupo LIMIT 2");
    $row2 = pg_num_rows($rs2);
    if($row2 == 0){
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_turnos");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];

        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+1), $NumGrupo, 'F', 'FÉRIAS', 13, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+2), $NumGrupo, 'X', 'FOLGA', 14, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+3), $NumGrupo, 'Y', 'INSS', 15, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+4), $NumGrupo, 'Q', 'AULA IAQ', 16, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+5), $NumGrupo, 'A', '08:00 / 17:00', 1, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+6), $NumGrupo, 'B', '07:00 / 16:00', 2, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+7), $NumGrupo, 'C', '07:00 / 17:00', 3, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+8), $NumGrupo, 'E', '09:00 / 18:00', 5, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+9), $NumGrupo, 'H', '14:00 / 18:00', 7, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+10), $NumGrupo, 'D', '11:00 / 15:00', 4, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+11), $NumGrupo, 'K', '08:00 / 14:15', 9, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+12), $NumGrupo, 'J', '06:50 / 15:50', 8, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+13), $NumGrupo, 'G', '10:50 / 19:50', 6, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+14), $NumGrupo, 'L', '07:00 / 13:15', 10, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+15), $NumGrupo, 'M', '13:35 / 19:50', 11, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, grupo_turnos, letra, horaturno, ordemletra, usuins, datains) VALUES(($Codigo+16), $NumGrupo, 'O', '08:00 / 18:00', 12, 3, NOW() )");
    }

    //Acerta as colunas auxiliares para os turnos
    $rsT = pg_query($Conec, "SELECT id, horaturno, letra, infotexto FROM ".$xProj.".escaladaf_turnos WHERE infotexto = 0 And ativo = 1 And cargahora < '00:01' And cargahora IS NOT NULL And grupo_turnos = $NumGrupo ORDER BY letra");
    $rowT = pg_num_rows($rsT);
    if($rowT > 0){
        $Hoje = date('d/m/Y');
        while($tblT = pg_fetch_row($rsT)){  //Calcular carga horaria
            $Cod = $tblT[0];
            $Hora = $tblT[1];
            if(is_null($Hora)){
                $Hora = "00:00 / 00:00";
            }
            if($tblT[3] == 1){ // infotexto = 0  => férias, inss, folga, etc
                $Proc = explode("/", $Hora);
                $HoraI = $Proc[0];
                $HoraF = $Proc[1];
                $TurnoIni = $Hoje." ".$HoraI;
                $TurnoFim = $Hoje." ".$HoraF;

                pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET calcdataini = '$TurnoIni', calcdatafim = '$TurnoFim' WHERE id = $Cod");
                pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargahora = (calcdatafim - calcdataini) WHERE id = $Cod");
                pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = (cargahora - time '01:00'), interv = '01:00' WHERE cargahora >= '08:00' And id = $Cod ");
                pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = (cargahora - time '00:15'), interv = '00:15' WHERE cargahora >= '06:00' And cargahora < '08:00' And id = $Cod ");
                pg_query($Conec, "UPDATE ".$xProj.".escaladaf_turnos SET cargacont = cargahora, interv = '00:00' WHERE cargahora <= '06:00' And id = $Cod ");
            }
        }
    }

//    pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf_notas");
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_notas (
        id SERIAL PRIMARY KEY, 
        numnota smallint NOT NULL DEFAULT 0,
        grupo_notas integer NOT NULL DEFAULT 0, 
        textonota text, 
        ativo smallint NOT NULL DEFAULT 1, 
        usuins bigint NOT NULL DEFAULT 0,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit bigint NOT NULL DEFAULT 0,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");
    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_notas WHERE grupo_notas = $NumGrupo LIMIT 2");
    $row3 = pg_num_rows($rs3);
    if($row3 == 0){
        $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_notas");
        $tblCod = pg_fetch_row($rsCod);
        $Codigo = $tblCod[0];

        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_notas (id, grupo_notas, numnota, textonota, usuins, datains) 
        VALUES(($Codigo+1), $NumGrupo, 1, 'Durante os turnos de 6 horas de duração, o funcionário deverá tirar 15 minutos de descanso, entre a terceira e quinta hora. Em consequência, o horário do turno de serviço deverá ser acrescido de 15 minutos  (Art. 71 - §1º e $2º da CLT). Nesses turnos não será necessário bater ponto quando do inicio e término do descanso. Exemplo: inicio do turno às 07h00 e saída para o descanso às 10h00. Regresso do descanso 10h15 e término do turno às 13h15.', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_notas (id, grupo_notas, numnota, textonota, usuins, datains) 
        VALUES(($Codigo+2), $NumGrupo, 2, 'Durante os turnos de 8 horas de duração, o funcionário deverá tirar 1 h de descanso, entre a quarta e sexta hora. O horário de descanso de cada empregado será definido e obrigatoriamente informado à DAF pelo chefe responsável do setor, por email, até o dia 25 do mês que antecede o início da escala de serviço. É obrigatório bater o ponto quando do início e término do descanso.', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_notas (id, grupo_notas, numnota, textonota, usuins, datains) 
        VALUES(($Codigo+3), $NumGrupo, 3, 'É obrigatório bater o ponto quando do início e término da jornada de trabalho.  Horas extras somente serão realizadas quando expressamente autorizadas pelo diretor da Área ou da Presidência. A utilização do banco de horas somente será possível para os empregados que assinaram o acordo individual - AI - NI-4.18-a DAF.', 3, NOW() ) ");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_notas (id, grupo_notas, numnota, textonota, usuins, datains) 
        VALUES(($Codigo+4), $NumGrupo, 4, 'As segundas, quartas e sextas feiras, o horário de funcionamento da comunhão será das 07h00 até as 21h30. Os setores funcionarão conforme as escalas de serviço.', 3, NOW() )");
    }

//    pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf_fer");
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_fer (
        id SERIAL PRIMARY KEY, 
        dataescalafer date DEFAULT '3000-12-31',
        descr VARCHAR(200), 
        ativo smallint NOT NULL DEFAULT 1, 
        usuins bigint NOT NULL DEFAULT 0,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit bigint NOT NULL DEFAULT 0,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");
    $rs5 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_fer LIMIT 2");
    $row5 = pg_num_rows($rs5);
    if($row5 == 0){
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(1, '2024/01/01', 'Confraternização Universal', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(2, '2024/04/21', 'Tiradentes', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(3, '2024/05/01', 'Dia do Trabalhador', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(4, '2024/09/07', 'Proclamação da Independência', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(5, '2024/10/12', 'Padroeira do Brasil', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(6, '2024/11/02', 'Dia de Finados', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(7, '2024/11/15', 'Proclamação da República', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, usuins, datains) VALUES(8, '2024/12/25', 'Natal', 3, NOW() )");
    }
    //pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escalas_gr");
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escalas_gr (
        id SERIAL PRIMARY KEY, 
        siglagrupo VARCHAR(20),
        descgrupo VARCHAR(100),
        descescala VARCHAR(200),
        guardaescala VARCHAR(20),
        qtd_turno smallint NOT NULL DEFAULT 1,
        ativo smallint NOT NULL DEFAULT 1, 
        chefe_escdaf bigint NOT NULL DEFAULT 0, 
        enc_escdaf bigint NOT NULL DEFAULT 0, 
        usuins bigint NOT NULL DEFAULT 0,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit bigint NOT NULL DEFAULT 0,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");
//------------

    $DiaIni = strtotime(date('Y/m/01')); // número - para começar com o dia 1
    $DiaIni = strtotime("-1 day", $DiaIni); // para começar com o dia 1 no loop for

    $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataescala, 'MM'), '/', TO_CHAR(dataescala, 'YYYY')) 
    FROM ".$xProj.".escaladaf WHERE grupo_id = $NumGrupo GROUP BY TO_CHAR(dataescala, 'MM'), TO_CHAR(dataescala, 'YYYY') ORDER BY TO_CHAR(dataescala, 'YYYY') DESC, TO_CHAR(dataescala, 'MM') DESC ");
    $OpcoesGrupo = pg_query($Conec, "SELECT id, siglagrupo FROM ".$xProj.".escalas_gr WHERE ativo = 1 ORDER BY siglagrupo");

    $OpcoesTransfMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataescala, 'MM'), '/', TO_CHAR(dataescala, 'YYYY')) 
    FROM ".$xProj.".escaladaf WHERE grupo_id = $NumGrupo  
    GROUP BY TO_CHAR(dataescala, 'MM'), TO_CHAR(dataescala, 'YYYY') ORDER BY TO_CHAR(dataescala, 'YYYY') DESC, TO_CHAR(dataescala, 'MM') DESC ");

    $OpConfig = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");

    //Mantem a tabela meses à frente
    for($i = 0; $i < 180; $i++){
        $Amanha = strtotime("+1 day", $DiaIni);
        $DiaIni = $Amanha;
        $Data = date("Y/m/d", $Amanha); // data legível
        $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf WHERE dataescala = '$Data' And grupo_id = $NumGrupo ");
        $row0 = pg_num_rows($rs0);
        if($row0 == 0){
            pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf (dataescala, grupo_id) VALUES ('$Data', $NumGrupo)");
        }
    }

    // Marcar Feriados em escaladaf
    $rsFer = pg_query($Conec, "SELECT TO_CHAR(dataescalafer, 'DD'), TO_CHAR(dataescalafer, 'MM') FROM ".$xProj.".escaladaf_fer WHERE ativo = 1 ORDER BY dataescalafer ");
    $rowFer = pg_num_rows($rsFer);
    if($rowFer > 0){
        while($tblFer = pg_fetch_row($rsFer)){
            $DiaFer = $tblFer[0];
            $MesFer = $tblFer[1];
            pg_query($Conec, "UPDATE ".$xProj.".escaladaf SET feriado = 1 WHERE TO_CHAR(dataescala, 'DD') = '$DiaFer' And TO_CHAR(dataescala, 'MM') = '$MesFer' And grupo_id = $NumGrupo ");
        }
    }

    $Escalante = parEsc("esc_daf", $Conec, $xProj, $_SESSION["usuarioID"]);
    $Fiscal = parEsc("esc_fisc", $Conec, $xProj, $_SESSION["usuarioID"]);
    $MesSalvo = parEsc("mes_escdaf", $Conec, $xProj, $_SESSION["usuarioID"]);

    //Ver se o que está guardado em poslog corresponde a algum mes salvo em escaladaf
    $rsMes = pg_query($Conec, "SELECT id 
    FROM ".$xProj.".escaladaf WHERE grupo_id = $NumGrupo And CONCAT(TO_CHAR(dataescala, 'MM'), '/', TO_CHAR(dataescala, 'YYYY')) = '$MesSalvo' ");
    $rowMes = pg_num_rows($rsMes);

    if(is_null($MesSalvo) || $MesSalvo == "" || $rowMes == 0){
        $MesSalvo = date("m")."/".date("Y");
        pg_query($Conec, "UPDATE ".$xProj.".poslog SET mes_escdaf = '$MesSalvo' WHERE pessoas_id = ". $_SESSION["usuarioID"]."" );
    }

    $Busca = addslashes($MesSalvo); 
    $Proc = explode("/", $Busca);
    $Mes = $Proc[0];
    if(strLen($Mes) < 2){
        $Mes = "0".$Mes;
    }
    $Ano = $Proc[1];
    //ver se a escala do mes está liberada para os participantes da escala
    $MesLiberado = 0;
    $rsLib = pg_query($Conec, "SELECT liberames FROM ".$xProj.".escaladaf WHERE DATE_PART('MONTH', dataescala) = '$Mes' And DATE_PART('YEAR', dataescala) = '$Ano' And liberames != 0 And grupo_id = $NumGrupo");
    $rowLib = pg_num_rows($rsLib);
    if($rowLib > 0){
        $MesLiberado = 1;
    }

    $MesLiberado = 0; // só o escalante verá a página

    ?>
        <input type="hidden" id="guardamesano" value="<?php echo addslashes($MesSalvo); ?>" />
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>" />
        <input type="hidden" id="escalante" value="<?php echo $Escalante; ?>" />
        <input type="hidden" id="fiscal" value="<?php echo $Fiscal; ?>" />
        <input type="hidden" id="guardameugrupo" value="<?php echo $MeuGrupo; ?>" />
        <input type="hidden" id="guardanumgrupo" value="<?php echo $NumGrupo; ?>" />
        <input type="hidden" id="guardaDiaId" value="" />
        <input type="hidden" id="guardaUsuId" value="" />
        <input type="hidden" id="guardacod" value="" />
        <input type="hidden" id="mudou" value="0" />
        <input type="hidden" id="liberadoefetivo" value="<?php echo $MesLiberado; ?>" />
        <input type="hidden" id="guardaCodTurno" value="0" />

        <div style="margin: 5px; border: 2px solid green; border-radius: 15px; padding: 5px;">
            <div class="row"> <!-- botões Inserir e Imprimir-->
                <div class="col" style="margin: 0 auto; text-align: left;">
                    <img src="imagens/settings.png" height="20px;" id="imgEscalaConfig" style="cursor: pointer; padding-left: 30px;" onclick="abreEscalaConfig();" title="Configurar o acesso e inserir participantes da escala">
                    <label style="padding-left: 20px; font-size: .8rem;">Escala mês: </label>
                    <select id="selecMesAnoEsc" style="font-size: 1rem; width: 90px;" title="Selecione o mês/ano.">
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

                    <label id="etiqGrupo" style="font-size: .8rem;">Ver Grupo: </label>
                    <select id="selecGrupo" style="font-size: .8rem; width: 90px;" title="Selecione o grupo.">
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

                    <?php
                    if($Escalante == 1 || $Fiscal == 1){ // suspenso
                        ?>
                        <label style="padding-left: 10px;"></label>
                        <input type="checkbox" id="evliberames" title="Liberar acesso aos participantes da escala" onClick="liberaMes(this);" <?php if($MesLiberado == 1) {echo "checked";} ?> >
                        <label id="etiqevliberames" for="evliberames" title="Acesso aos participantes da escala">liberado</label>
                        <?php
                    }
                    ?>
                </div> <!-- quadro -->

                <div class="col" id="etiqSiglaGrupo" style="text-align: center;">Escala <?php echo $SiglaGrupo; ?></div> <!-- espaçamento entre colunas  -->
                <div class="col" style="margin: 0 auto; text-align: center;">
                    <label id="etiqtransfMesAnoEsc" style="padding-left: 40px; font-size: .8rem;">Transferir para o mês: </label>
                    <select id="transfMesAnoEsc" style="font-size: .8rem; width: 90px;" title="Transferir esta escala para o mês/ano escolhido">
                        <option value=""></option>
                            <?php 
                                if($OpcoesTransfMes){
                                    while ($Opcoes = pg_fetch_row($OpcoesTransfMes)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                                        <?php 
                                    }
                                }
                            ?>
                    </select>
                    <label style="padding-left: 20px;"></label>
                    <button id="botImprimir" class="botpadrred" onclick="imprPlanilha();">PDF</button>
                </div> <!-- quadro -->
            </div>
        </div>

        <div style="margin: 10px; border: 2px solid green; border-radius: 15px; padding: 10px; min-height: 70px; text-align: center;">
            <table style="margin: 0 auto; width: 90%;">
                <tr>
                    <td>
                        <div class="container" style="margin: 0 auto;">
                            <div class="row">
                                <div class="col quadro"></div>
                                <div class="col quadro" style="text-align: center;"></div> <!-- Central - espaçamento entre colunas  -->
                                <div class="col quadro" style="position: relative; float: rigth; text-align: right;"></div> 
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
<!-- Faixas da página -->
            <div id="faixacentral"></div>
            <div id="faixaquadro"></div>
            <div id="faixacarga"></div>
            <div id="faixanotas"></div>

            <div class="row">
                <div class="col quadro"></div>
                <div class="col quadro" style="text-align: center;">
                    <div id="faixaferiados"></div> 
                </div> <!-- Central -->
                <div class="col quadro" style="position: relative; float: rigth; text-align: right;"></div> 
            </div>
        </div>

        <!-- div modal relacionar escalado -->
        <div id="relacParticip" class="relacmodalMovel">
            <div class="modal-content-relacParticipMovel">
                <span class="close" onclick="fechaRelaPart();">&times;</span>
                <label style="color: #666;">Escala para o dia: &nbsp; </label><label id="titulomodal" style="color: #666; padding-bottom: 10px; font-weight: bold;"></label>
                <!-- lista dos participantes da escala do grupo -->
                <div id="relacaoParticip" style="margin-bottom: 5px; border: 2px solid #C6E2FF; border-radius: 10px;"></div>
                <button class="botpadrblue" style="font-size: 80%;" onclick="insParticipante();">Inserir Marcados</button>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal relacionar turnos - edHorarios.php -->
        <div id="relacQuadroHorario" class="relacmodal">
            <div class="modal-content-relacHorario">
                <span class="close" onclick="fechaQuadroHorario();">&times;</span>
                <div id="relacaoHorarios" style="border: 2px solid #C6E2FF; border-radius: 10px;"></div>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal relacionar descanso - edDescanso.php -->
        <div id="relacQuadroDescanso" class="relacmodal">
            <div class="modal-content-relacDescanso">
                <span class="close" onclick="fechaQuadrodescanso();">&times;</span>
                <div id="relacaoDescanso" style="border: 2px solid #C6E2FF; border-radius: 10px;"></div>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para destacar um dia - Sem uso -->
        <div id="relacDestacaDia" class="relacmodal">
            <div class="modal-content-destacaDia">
                <span class="close" onclick="fechaDestacaDia();">&times;</span>
                <div id="relacaoDias" style="border: 2px solid #C6E2FF; border-radius: 10px;"></div>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal editar notas -->
        <div id="relacQuadroNotas" class="relacmodal">
            <div class="modal-content-relacNotas">
                <span class="close" onclick="fechaQuadroNotas();">&times;</span>
                <h5 id="titulomodal" style="text-align: center;color: #666;">Edição de Nota ao Horário de Trabalho</h5>
                <div style="border: 2px solid #C6E2FF; border-radius: 10px;">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td style="text-align: right;"><label style="font-size: 80%;">Nota nº: </label></td>
                            <td><input type="text" id="numnota" style="width: 40px; text-align: center; border: 1px solid; border-radius: 5px;" valor="" onchange="modif();" title="Número da nota"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Texto: </td>
                            <td colspan="2" >
                                <div class="col-xs-6"> <!-- textarea com bootstrap  -->
                                    <textarea class="form-control" id="textonota" style="resize: both; margin-top: 3px; border: 1px solid blue; border-radius: 10px; padding: 4px;" rows="6" cols="70" title="Texto da nota" onchange="modif();"></textarea>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiqAzul"><button id="botInsNota" class="botpadrred" style="font-size: .7rem;" onclick="InsereNota();" title="Insere uma nova nota">Inserir</button></td>
                            <td colspan="2" class="aDir">
                                <button id="botSalvarNota" class="botpadrblue" style="font-size: .9rem;" onclick="salvaNota();">Salvar</button>
                            <label style="padding-left: 80px;"></label>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div> <!-- Fim Modal-->


        <!-- div modal relacionar feriados de interesse - edFeriados.php -->
        <div id="relacQuadroFeriados" class="relacmodal">
            <div class="modal-content-relacFeriados">
                <span class="close" onclick="fechaQuadroFeriados();">&times;</span>
                <div id="relacaoFeriados" style="border: 2px solid #C6E2FF; border-radius: 10px;"></div>
            </div>
        </div> <!-- Fim Modal-->


         <!-- Modal configuração-->
         <div id="modalEscalaConfig" class="relacmodal">
            <div class="modal-content-escalaControle">
                <span class="close" onclick="fechaEscalaConfig();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"></div>
                        <div class="col quadro"><h5 style="text-align: center; color: #666;">Escala <?php echo $SiglaGrupo; ?></h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><button class="botpadrred" style="font-size: 70%;" onclick="resumoUsuEscala();">Resumo em PDF</button></div> 
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
                            <select id="configSelecEscala" style="max-width: 230px;" onchange="modif();" title="Selecione um usuário.">
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
                            <input type="text" id="configCpfEscala" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('configSelecEscala');return false;}" title="Procura por CPF. Digite o CPF."/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                    </tr>
                </table>

                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td class="etiq80">Escala DAF:</td>
                        <td colspan="4">
                            <input type="checkbox" id="checkefetivo" onchange="marcaConfigEscala(this, 'eft_daf');" >
                            <label for="checkefetivo">efetivo da escala</label>
                        </td>
                    </tr>

                    <tr>
                        <td class="etiq80">Escala DAF:</td>
                        <td colspan="4">
                            <input type="checkbox" id="checkescalante" onchange="marcaConfigEscala(this, 'esc_daf');" >
                            <label for="checkescalante">escalante</label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"><hr style="margin: 0; padding: 2px;"></td>
                    </tr>
                    <tr>
                        <td class="etiq80">Encarregado ADM:</td>
                        <td colspan="4">
<!--                            <input type="checkbox" id="checkEncarreg" onchange="marcaConfigEscala(this, 'enc_escdaf');" >
                            <label for="checkEncarreg">Chefe Imediato</label>
-->
                            <select id="configSelecEncarreg" style="max-width: 230px;" onchange="modif();" title="Selecione um usuário.">
                                <option value=""></option>
                                <?php 
                                $OpEncarreg = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
                                if($OpEncarreg){
                                    while ($Opcoes = pg_fetch_row($OpEncarreg)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiq80">Chefe DIV ADM:</td>
                        <td colspan="4">
<!--                            <input type="checkbox" id="checkChefeADM" onchange="marcaConfigEscala(this, 'chefe_escdaf');" >
                            <label for="checkChefeADM">Chefe Div Adm</label>
-->
                            <select id="configSelecChefeDiv" style="max-width: 230px;" onchange="modif();" title="Selecione um usuário.">
                                <option value=""></option>
                                <?php 
                                $OpChefeDiv = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
                                if($OpEncarreg){
                                    while ($Opcoes = pg_fetch_row($OpChefeDiv)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                            </select>

                        </td>
                    </tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal formatar turno -->
        <div id="relacQuadroTurnos" class="relacmodal">
            <div class="modal-content-relacTurnos">
                <span class="close" onclick="fechaQuadroTurnos();">&times;</span>
                <div>
                    <table style="margin: 0 auto;">
                        <tr>
                            <td><label class="etiqAzul">Letra</label></td>
                            <td colspan="7" style="text-align: center;"><label class="etiqAzul">Formação do Turno</label></td>
                            <td><label class="etiqAzul"></label></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul" style="text-align: center; font-size: 110%; font-weight: bold;" id="etiqletra">Letra</td>
                            <td colspan="7" style="text-align: center;">
                                <div id="selecTurno" style="margin-left: 10px; padding: 4px; border: 1px solid; border-radius: 4px;">
                                <select id="selecHor1" style="font-size: .9rem; width: 50px;" title="Selecione o mês/ano." onchange="modif();">
                                    <option value="00">00</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                </select>
                                :
                                <select id="selecMin1" style="font-size: .9rem; width: 50px;" onchange="modif();">
                                    <option value="00">00</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                    <option value="32">32</option>
                                    <option value="33">33</option>
                                    <option value="34">34</option>
                                    <option value="35">35</option>
                                    <option value="36">36</option>
                                    <option value="37">37</option>
                                    <option value="38">38</option>
                                    <option value="39">39</option>
                                    <option value="40">40</option>
                                    <option value="41">41</option>
                                    <option value="42">42</option>
                                    <option value="43">43</option>
                                    <option value="44">44</option>
                                    <option value="45">45</option>
                                    <option value="46">46</option>
                                    <option value="47">47</option>
                                    <option value="48">48</option>
                                    <option value="49">49</option>
                                    <option value="50">50</option>
                                    <option value="51">51</option>
                                    <option value="52">52</option>
                                    <option value="53">53</option>
                                    <option value="54">54</option>
                                    <option value="55">55</option>
                                    <option value="56">56</option>
                                    <option value="57">57</option>
                                    <option value="58">58</option>
                                    <option value="59">59</option>
                                </select>
                                /
                                <select id="selecHor2" style="font-size: .9rem; width: 50px;" onchange="modif();">
                                <option value="00">00</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                </select>
                                :
                                <select id="selecMin2" style="font-size: .9rem; width: 50px;" onchange="modif();">
                                <option value="00">00</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                    <option value="32">32</option>
                                    <option value="33">33</option>
                                    <option value="34">34</option>
                                    <option value="35">35</option>
                                    <option value="36">36</option>
                                    <option value="37">37</option>
                                    <option value="38">38</option>
                                    <option value="39">39</option>
                                    <option value="40">40</option>
                                    <option value="41">41</option>
                                    <option value="42">42</option>
                                    <option value="43">43</option>
                                    <option value="44">44</option>
                                    <option value="45">45</option>
                                    <option value="46">46</option>
                                    <option value="47">47</option>
                                    <option value="48">48</option>
                                    <option value="49">49</option>
                                    <option value="50">50</option>
                                    <option value="51">51</option>
                                    <option value="52">52</option>
                                    <option value="53">53</option>
                                    <option value="54">54</option>
                                    <option value="55">55</option>
                                    <option value="56">56</option>
                                    <option value="57">57</option>
                                    <option value="58">58</option>
                                    <option value="59">59</option>
                                </select>
                                </div>
                                <div style="min-width: 250px; align-content: center; margin-left: 10px; padding: 4px;">
                                    <input type="text" id="textoTurno" style="width: 100%; text-align: center;" title="Digite o texto para o turno" onchange="modif();"/>
                                </div>
                            </td>
                            <td style="padding-left: 10px;">
                                <input type="checkbox" id="boxtextoTurno" title="Texto livre" onClick="abreTexto(this);"><label for="boxtextoTurno" style="padding-left: 3px;" title="Escrever texto"> Texto</label>
                            </td>
                        </tr>
                        <tr>
                            <td><label class="etiqAzul"></label></td>
                            <td colspan="7" style="text-align: center;">
                            <button id="botsalvaturno" class="botpadrblue" style="font-size: .8rem;" onclick="salvaEditTurno();">Salvar</button>
                            </td>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div> <!-- Fim Modal-->

    </body>
</html>