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
        <title>Escala</title>
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
            /* Tamanho do checkbox e radiobox */
            input[type=checkbox]{
                transform: scale(1.2);
            }
            input[type=radio]{
                transform: scale(1.2);
            }
            .relacmodalMovel{
                display: none; 
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
            .modal-content-escalaControleMovel{
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
                padding: 10px;
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
                padding-top: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 40%;
            }
            .modal-content-AnotFunc{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 15% auto; 
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 70%;
                max-width: 900px;
            }
            .quadrodia {
                font-size: 90%;
                min-width: 30px;
                border: 1px solid;
                border-radius: 3px;
                background-color: transparent;
            }
            .quadrodiaCinza {
                font-size: 90%;
                min-width: 30px;
                border: 1px solid;
                border-radius: 3px;
                background-color: #E8E8E8;
                color: black;
            }
            .quadrodiaClick {
                font-size: 90%;
                min-width: 30px;
                border: 1px solid;
                border-radius: 3px;
                cursor: pointer;
                background-color: transparent;
            }
            .quadrodiaClickCinza {
                font-size: 90%;
                min-width: 30px;
                border: 1px solid;
                border-radius: 3px;
                cursor: pointer;
                background-color: #E8E8E8;
                color: black;
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
                color: black;
            }
            .quadroletraBlue {
                text-align: center;
                font-size: 90%;
                min-width: 10px;
                border: 1px solid;
                border-radius: 3px;
                background-color: #00BFFF;
                color: black;
            }
            .quadroletraGreen {
                text-align: center;
                font-size: 90%;
                min-width: 10px;
                border: 1px solid;
                border-radius: 3px;
                background-color: #00FF7F;
                color: black;
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
            .modal-content-escImprNotas{
                background: linear-gradient(180deg, white, #0099FF);
                margin: 10% auto; 
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 40%;
                max-width: 500px;
            }
            .modal-content-InsTipo{
                background: linear-gradient(180deg, white, #0099FF);
                margin: 10% auto; 
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 40%;
                max-width: 500px;
            }
        </style>
        <script>
            LargTela = $(window).width(); // largura da tela ao abrir o módulo
            $(document).ready(function(){
//                $('#carregaTema').load('modulos/config/carTema.php?carpag=escala_daf');
                //Não vai liberar para os escalados - só o pdf impresso
                document.getElementById("evliberames").style.visibility = "hidden"; 
                document.getElementById("etiqevliberames").style.visibility = "hidden";  // liberar para todos no site - suspenso
                document.getElementById("selecGrupo").style.visibility = "hidden"; 
                document.getElementById("etiqGrupo").style.visibility = "hidden"; 
                document.getElementById("imgEscalaConfig").style.visibility = "hidden";
                document.getElementById("imprGrupos").style.visibility = "hidden"; 
                document.getElementById("imgEspera").style.visibility = "hidden"; 
                document.getElementById("etiqtransfMesAnoEsc").style.visibility = "hidden";
                document.getElementById("transfMesAnoEsc").style.visibility = "hidden";
                document.getElementById("escolhaTema").style.visibility = "hidden";

                if(parseInt(document.getElementById("escalante").value) === 0 && parseInt(document.getElementById("fiscal").value) === 0){
                    $("#faixacentral").load("modulos/escaladaf/infoAgd1.php");
                }else{
                    $('#carregaTema').load('modulos/config/carTema.php?carpag=escala_daf');
                    document.getElementById("escolhaTema").style.visibility = "visible";
                }

                if(parseInt(document.getElementById("UsuAdm").value) > 6){ // Superusuário
                    document.getElementById("imprGrupos").style.visibility = "visible"; 
                }

                if(parseInt(document.getElementById("guardaUsuId").value) != 3){ // Programador
                    document.getElementById("etiqcheckvisucargo").style.visibility = "hidden";
                    document.getElementById("checkvisucargo").style.visibility = "hidden";
                    document.getElementById("etiqcheckprimcargo").style.visibility = "hidden";
                    document.getElementById("checkprimcargo").style.visibility = "hidden";
                }
                if(parseInt(document.getElementById("guardaUsuId").value) != 3 && parseInt(document.getElementById("guardaUsuId").value) != 83){ // Programador
                    document.getElementById("etiqchecksemanaIniFim").style.visibility = "hidden";
                    document.getElementById("checksemanaIniFim").style.visibility = "hidden";
                }
//alert($(window).width());

                if(parseInt(document.getElementById("escalante").value) === 1 || parseInt(document.getElementById("quantGruposEsc").value) > 1){ // Escalante ou é escalante em mais de um grupo
                    document.getElementById("imgEscalaConfig").style.visibility = "visible"; 
                }
                if(parseInt(document.getElementById("fiscal").value) === 1 || parseInt(document.getElementById("quantGruposEsc").value) > 1){ // Fiscal das escalas ou é escalante em mais de um grupo
                    document.getElementById("selecGrupo").style.visibility = "visible"; 
                    document.getElementById("etiqGrupo").style.visibility = "visible"; 
                }

                document.getElementById("selecMesAnoEsc").value = document.getElementById("guardamesano").value;
                document.getElementById("selecGrupo").value = document.getElementById("guardanumgrupo").value;

                if(parseInt(document.getElementById("liberadoefetivo").value) === 0 && parseInt(document.getElementById("escalante").value) === 0 && parseInt(document.getElementById("fiscal").value) === 0){
                    $("#faixacentral").load("modulos/escaladaf/infoAgd1.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                    $("#faixaquadro").load("modulos/escaladaf/infoAgd2.php");
                    $("#faixacarga").load("modulos/escaladaf/infoAgd2.php");
                    $("#faixanotas").load("modulos/escaladaf/infoAgd2.php");
                    $("#faixaferiados").load("modulos/escaladaf/infoAgd2.php");
                     document.getElementById("botImprimir").style.visibility = "hidden";
                     document.getElementById("transfMesAnoEsc").style.visibility = "hidden";
                }else{
                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                    $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                    $("#faixacarga").load("modulos/escaladaf/jCargaDaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                    $("#faixanotas").load("modulos/escaladaf/notasdaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                    $("#faixaferiados").load("modulos/escaladaf/relFeriados.php?ano="+document.getElementById("guardaAno").value);
                    document.getElementById("botImprimir").style.visibility = "visible";
                    if(parseInt(document.getElementById("escalante").value) === 1 || parseInt(document.getElementById("guardaUsuId").value) === 83){
                        document.getElementById("etiqtransfMesAnoEsc").style.visibility = "visible";
                        document.getElementById("transfMesAnoEsc").style.visibility = "visible";
                    }
                }

                $("#edinterv").mask("99:99");

                $("#selecMesAnoEsc").change(function(){
                    if(parseInt(document.getElementById("selecMesAnoEsc").value) > 0){
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvamesano&mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value)
                            +"&numgrupo="+document.getElementById("selecGrupo").value, true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText);
                                        Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                        if(parseInt(Resp.coderro) === 0){
                                            if(parseInt(Resp.mesliberado) === 0 && parseInt(document.getElementById("escalante").value) === 0 && parseInt(document.getElementById("fiscal").value) === 0){
                                                $("#faixacentral").load("modulos/escaladaf/infoAgd1.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                                                $("#faixaquadro").load("modulos/escaladaf/infoAgd2.php");
                                                $("#faixanotas").load("modulos/escaladaf/infoAgd2.php");
                                                $("#faixaferiados").load("modulos/escaladaf/infoAgd2.php");
                                                document.getElementById("botImprimir").style.visibility = "hidden";
                                            }else{
                                                if(parseInt(Resp.temMes) > 27){
                                                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                                                    $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                    $("#faixacarga").load("modulos/escaladaf/jCargaDaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                    $("#faixanotas").load("modulos/escaladaf/notasdaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                    $("#faixaferiados").load("modulos/escaladaf/relFeriados.php?ano="+Resp.anoselec);  //document.getElementById("guardaAno").value);
                                                    document.getElementById("botImprimir").style.visibility = "visible";
                                                }else{
                                                    $("#faixacentral").load("modulos/escaladaf/infoAgd2.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                                                }
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
                    if(parseInt(document.getElementById("selecMesAnoEsc").value) !== 12){
                        if(parseInt(document.getElementById("transfMesAnoEsc").value) < parseInt(document.getElementById("selecMesAnoEsc").value)){
                            $.confirm({
                            title: 'Ação Suspensa!',
                            content: 'Mês escolhido é anterior a '+document.getElementById("selecMesAnoEsc").value,
                            draggable: true,
                            buttons: {
                                OK: function(){}
                            }
                        });
                        document.getElementById("transfMesAnoEsc").value = "";
                        return false;

                        }
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
                                        +"&transfde="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value)
                                        +"&numgrupo="+document.getElementById("selecGrupo").value
                                        , true);
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
                                                            $("#faixanotas").load("modulos/escaladaf/infoAgd2.php");
                                                            $("#faixaferiados").load("modulos/escaladaf/infoAgd2.php");
                                                            document.getElementById("botImprimir").style.visibility = "hidden";
                                                        }else{
                                                            $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                                                            $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                            $("#faixacarga").load("modulos/escaladaf/jCargaDaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                            $("#faixanotas").load("modulos/escaladaf/notasdaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                                            $("#faixaferiados").load("modulos/escaladaf/relFeriados.php?ano="+document.getElementById("guardaAno").value);
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
                        document.getElementById("configCargoEscala").value = "";
                        document.getElementById("checkefetivo").checked = false;
                        document.getElementById("checkescalante").checked = false;
                        document.getElementById("checkfiscal").checked = false;
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=buscausuario&codigo="+document.getElementById("configSelecEscala").value
                        +"&numgrupo="+document.getElementById("selecGrupo").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configCpfEscala").value = format_CnpjCpf(Resp.cpf);
                                        document.getElementById("configCargoEscala").value = Resp.cargo;
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
                                        if(parseInt(Resp.escfiscal) === 1){
                                            document.getElementById("checkfiscal").checked = true;
                                        }else{
                                            document.getElementById("checkfiscalescalante").checked = false;
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

                $("#configCpfEscala").click(function(){
                    document.getElementById("configSelecEscala").value = "";
                    document.getElementById("configCpfEscala").value = "";
                    document.getElementById("configCargoEscala").value = "";
                    document.getElementById("checkefetivo").checked = false;
                    document.getElementById("checkescalante").checked = false;
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
                                        document.getElementById("configCargoEscala").value = Resp.cargo;
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
                                    }
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.");
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("checkefetivo").checked = false;
                                        document.getElementById("checkescalante").checked = false;
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

                $("#configCargoEscala").change(function(){
                    if(document.getElementById("configSelecEscala").value == ""){
                        document.getElementById("configCargoEscala").value = "";
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=cargousudaf&codigo="+document.getElementById("configSelecEscala").value+"&valor="+document.getElementById("configCargoEscala").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#configSelecChefeDiv").change(function(){
                    ajaxIni();
                    if(ajax){ // envia o selecGrupo, se estiver vazio o salvaEscDaf procura o original
                        ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvachefediv&codigo="+document.getElementById("configSelecChefeDiv").value
                        +"&numgrupo="+document.getElementById("selecGrupo").value, true);
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
                        ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaencarreg&codigo="+document.getElementById("configSelecEncarreg").value
                        +"&numgrupo="+document.getElementById("selecGrupo").value, true);
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
                    if(parseInt(document.getElementById("selecGrupo").value) === 0){
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=trocagrupo&grupo="+document.getElementById("selecGrupo").value+"&selecmes="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.");
                                    }else{
                                        document.getElementById("guardanumgrupo").value = document.getElementById("selecGrupo").value;
                                        document.getElementById("tricoluna2").innerHTML = "Escala "+Resp.siglagrupo;
                                        if(parseInt(Resp.temMes) > 27){ // encontrados lançamentos do mês e ano
                                            $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela+"&selecmes="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                                            $("#faixacarga").load("modulos/escaladaf/jCargaDaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                        }else{
                                            $("#faixacentral").load("modulos/escaladaf/infoAgd2.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                                            $("#faixacarga").load("modulos/escaladaf/infoAgd2.php");
                                        }
                                        $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);                                        
                                        $("#faixanotas").load("modulos/escaladaf/notasdaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                        $("#faixaferiados").load("modulos/escaladaf/relFeriados.php?ano="+document.getElementById("guardaAno").value);
                                        document.getElementById("botImprimir").style.visibility = "visible";
                                        document.getElementById("transfMesAnoEsc").style.visibility = "visible";
                                        //Verificar se é igual Meugrupo e Numgrupo
                                        if(parseInt(document.getElementById("escalante").value) === 1 || parseInt(document.getElementById("quantGruposEsc").value) > 1){ // Se for Escalante ou é escalante de vários grupos
                                            document.getElementById("imgEscalaConfig").style.visibility = "visible";
                                            document.getElementById("etiqtransfMesAnoEsc").style.visibility = "visible";
                                            document.getElementById("transfMesAnoEsc").style.visibility = "visible";
                                        }else{
                                            if(parseInt(document.getElementById("guardaUsuId").value) != 83){ // provisório Will
                                            document.getElementById("imgEscalaConfig").style.visibility = "hidden";
                                            document.getElementById("etiqtransfMesAnoEsc").style.visibility = "hidden";
                                            document.getElementById("transfMesAnoEsc").style.visibility = "hidden";
                                            }
                                        }
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#selecAnoFer").change(function(){
                    if(document.getElementById("selecAnoFer").value == ""){
                        return false;
                    }
                    $("#relacaoFeriados").load("modulos/escaladaf/edFeriados.php?ano="+document.getElementById("selecAnoFer").value);
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
                $("#relacaoParticip").load("modulos/escaladaf/equipe.php?diaid="+DiaId+"&numgrupo="+document.getElementById("selecGrupo").value);
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
                $("#relacaoFeriados").load("modulos/escaladaf/edFeriados.php?ano="+document.getElementById("guardaAno").value);
                document.getElementById("selecAnoFer").value = document.getElementById("guardaAno").value;
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

            function insereLetra(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=buscaOrdem&numgrupo="+document.getElementById("guardanumgrupo").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    document.getElementById("insordem").value = 0;
                                }else{
                                    if(parseInt(Resp.quantTurno) > 25){
                                        $.confirm({
                                            title: 'Ação Suspensa!',
                                            content: 'Número máximo de turnos (25) atingido',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                        return false;
                                    }else{
                                        document.getElementById("insordem").value = Resp.ordem;
                                        document.getElementById("inserirletra").style.display = "block";
                                        document.getElementById("insletra").focus();
                                        document.getElementById("abreinsletra").style.visibility = "hidden";
                                    }
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
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvainsLetra&ordem="+document.getElementById("insordem").value
                    +"&insletra="+encodeURIComponent(document.getElementById("insletra").value)
                    +"&numgrupo="+document.getElementById("guardanumgrupo").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("insletra").value = "";
                                        document.getElementById("insletra").focus();
                                        $.confirm({
                                            title: 'Ação Suspensa!',
                                            content: 'Letra já existe',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                        return false;
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

            function apagaLetra(Cod, Letra){
                $.confirm({
                    title: 'Apagar turno.',
                    content: 'Confirma apagar turno: Letra '+Letra+' ?',
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

            function fechaInsLetra(){
                document.getElementById("inserirletra").style.display = "none";
                document.getElementById("abreinsletra").style.visibility = "visible";
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
                    +"&insdescr="+encodeURIComponent(document.getElementById("insdescr").value)
                    +"&ano="+document.getElementById("selecAnoFer").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#relacaoFeriados").load("modulos/escaladaf/edFeriados.php?ano="+document.getElementById("selecAnoFer").value);
                                    $("#faixaferiados").load("modulos/escaladaf/relFeriados.php?ano="+document.getElementById("guardaAno").value);
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
                                                $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                                                $("#relacaoFeriados").load("modulos/escaladaf/edFeriados.php?ano="+document.getElementById("guardaAno").value);
                                                $("#faixaferiados").load("modulos/escaladaf/relFeriados.php?ano="+document.getElementById("guardaAno").value);
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

            function editaDataFer(Cod, Valor){
                if(Valor == ""){
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaFeriado&codigo="+Cod+"&novadata="+encodeURIComponent(Valor)+"&ano="+document.getElementById("selecAnoFer").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                                    $("#relacaoFeriados").load("modulos/escaladaf/edFeriados.php?ano="+document.getElementById("selecAnoFer").value);
                                    $("#faixaferiados").load("modulos/escaladaf/relFeriados.php?ano="+document.getElementById("guardaAno").value);
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function editaDescrFer(Cod, Valor){
                if(Valor == ""){
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaDescFeriado&codigo="+Cod+"&descfer="+encodeURIComponent(Valor), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                                    $("#relacaoFeriados").load("modulos/escaladaf/edFeriados.php?ano="+document.getElementById("selecAnoFer").value);
                                    $("#faixaferiados").load("modulos/escaladaf/relFeriados.php?ano="+document.getElementById("guardaAno").value);
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function marcaConfigEscalaEsc(obj){
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
                if(parseInt(document.getElementById("UsuAdm").value) < 7){
                    $.confirm({
                        title: '<img src="imagens/Logo1.png" height="20px;">',
                        content: 'Requer nível administrativo mais alto.',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    if(obj.checked === true){
                        obj.checked = false;
                    }else{
                        obj.checked = true;
                    }
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=configMarcaEscalaEsc&codigo="+document.getElementById("configSelecEscala").value
                    +"&numgrupo="+document.getElementById("selecGrupo").value
                    +"&valor="+Valor, true);
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
                                            content: 'Não restaria outro escalante para gerenciar a escala.',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                        return false;
                                    }else{
                                        $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
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

            function marcaConfigEscalaEft(obj){
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
                    +"&numgrupo="+document.getElementById("selecGrupo").value
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
                                    if(parseInt(Resp.jaesta) === 1){
                                        obj.checked = false;
                                        $.confirm({
                                            title: '<img src="imagens/Logo1.png" height="20px;">',
                                            content: 'Usuário participa de outra escala:<br>'+Resp.outrogrupo+".<br>Solicite à ATI modificar o grupo para fins de escala, se for o caso.",
                                            autoClose: 'OK|15000',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                        return false;
                                    }else{
                                        $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
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

            function marcaConfigEscalaFisc(obj){
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
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=configMarcaEscalaFisc&codigo="+document.getElementById("configSelecEscala").value
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
                                    if(parseInt(Resp.jaesta) === 1){
                                        obj.checked = false;
                                        $.confirm({
                                            title: '<img src="imagens/Logo1.png" height="20px;">',
                                            content: 'Usuário participa de outra escala:<br>'+Resp.outrogrupo+".<br>Solicite à ATI modificar o grupo para fins de escala, se for o caso.",
                                            autoClose: 'OK|15000',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                        return false;
                                    }else{
                                        $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
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
//                document.getElementById("guardaCodUsuTrocaTurno").value = CodPartic; // para caso de trocar letra escala em andamento
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
                    +"&numgrupo="+document.getElementById("selecGrupo").value, true);
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
                                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                                    $("#faixacarga").load("modulos/escaladaf/jCargaDaf.php?numgrupo="+document.getElementById("guardanumgrupo").value);
                                    document.getElementById("relacParticip").style.display = "none";
                                }else{
                                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
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
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=procChefeDiv&numgrupo="+document.getElementById("selecGrupo").value, true);
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
                                    document.getElementById("checkfiscal").checked = false;
                                    document.getElementById("configSelecChefeDiv").value = Resp.chefe;
                                    document.getElementById("configSelecEncarreg").value = Resp.encarreg;
                                    document.getElementById("etiqNomeGrupo").innerHTML = Resp.siglagrupo; // fiscal editando 
                                    document.getElementById("configCpfEscala").value = "";
                                    document.getElementById("configSelecEscala").value = "";
                                    if(parseInt(Resp.visucargo) === 1){
                                        document.getElementById("checkvisucargo").checked = true;
                                    }else{
                                        document.getElementById("checkvisucargo").checked = false;
                                    }
                                    if(parseInt(Resp.primcargo) === 1){
                                        document.getElementById("checkprimcargo").checked = true;
                                    }else{
                                        document.getElementById("checkprimcargo").checked = false;
                                    }
                                    if(parseInt(Resp.semanaIniFim) === 1){
                                        document.getElementById("checksemanaIniFim").checked = true;
                                    }else{
                                        document.getElementById("checksemanaIniFim").checked = false;
                                    }

                                    document.getElementById("modalEscalaConfig").style.display = "block";
                                    $("#configOcorrencias").load("modulos/escaladaf/edNotaOcor.php");
                                    $("#configMotivos").load("modulos/escaladaf/edNotaMot.php");
                                    $("#configStat").load("modulos/escaladaf/edNotaStat.php");
                                    $("#configAdm").load("modulos/escaladaf/edNotaAdm.php");

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
            function geralEscala(){
                window.open("modulos/escaladaf/imprEscGrupos.php?acao=listaGrupos", "EscalaGrupos");
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

            function editaEscala(obj){
                if(obj.checked === true){
                    Valor = 1;
                    document.getElementById("etiqeditaEscala").style.color = "red";
                }else{
                    Valor = 0;
                    document.getElementById("etiqeditaEscala").style.color = "#9C9C9C";
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=editaEscala&valor="+Valor+"&codescala="+document.getElementById("guardanumgrupo").value, true);
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
                    Turno = document.getElementById("selecHor1").value+":"+document.getElementById("selecMin1").value+" / "+document.getElementById("selecHor2").value+":"+document.getElementById("selecMin2").value
                }else{ // só texto
                    InfoTexto = 1;
                    Turno = document.getElementById("textoTurno").value;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaEditaTurno&codigo="+document.getElementById("guardaCodTurno").value
                    +"&numgrupo="+document.getElementById("guardanumgrupo").value
                    +"&turno="+encodeURIComponent(Turno)
                    +"&infotexto="+InfoTexto
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
                            document.getElementById("imgEspera").style.visibility = "visible"; 
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/escaladaf/criaExcel_daf.php?acao=listaturnos&numgrupo="+document.getElementById("guardanumgrupo").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            document.getElementById("imgEspera").style.visibility = "hidden"; 
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.");
                                            }else if(parseInt(Resp.criaobjphp) === 0){
                                                alert("Plugin do Office não encontrado.");
                                            }else if(parseInt(Resp.arquivo) === 0){
                                                alert("O arquivo Excel não pode ser criado.");
                                            }else{
                                                window.open("modulos/conteudo/arquivos/ListaTurnos.xlsx", '_blank');
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

            function marcaVisuCargo(obj, Cod){
                if(obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=marcaVisuCargo&valor="+Valor, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function marcaPrimCargo(obj, Cod){
                if(obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=marcaPrimCargo&valor="+Valor, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function marcaSemanaIniFim(obj, Cod){
                if(obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=marcaSemanaIniFim&valor="+Valor, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#faixacarga").load("modulos/escaladaf/jCargaDaf.php?numgrupo="+document.getElementById("selecGrupo").value);
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function carregaMes(){ // sem uso
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=carregames&numgrupo="+document.getElementById("selecGrupo").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                var options = "";  //Cria array
                                options += "<option value='0'></option>";
                                $.each(Resp, function(key, Resp){
                                    options += '<option value="' + Resp.Mes + '">'+Resp.Mes + '</option>';
                                });
                                $("#selecMesAnoEsc").html(options);
                                document.getElementById("selecMesAnoEsc").value = document.getElementById("guardamesano").value;
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function renumeraLetras(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=renumeraletras&numgrupo="+document.getElementById("selecGrupo").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#relacaoHorarios").load("modulos/escaladaf/edHorarios.php?numgrupo="+document.getElementById("selecGrupo").value);
                                    $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?numgrupo="+document.getElementById("selecGrupo").value);
                                }                                
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaCor(Valor){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaCorListas&valor="+Valor, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&guardatema="+document.getElementById("guardaTema").value+"&largTela="+LargTela);
                                }                                
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function abreAnot(Cod){
                document.getElementById("guardaCodFunc").value = Cod;
                document.getElementById("dataFuncEscala").innerHTML = document.getElementById("titulomodal").innerHTML;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=buscaNome&codigo="+Cod+"&data="+document.getElementById("titulomodal").innerHTML, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("nomeEscalado").innerHTML = Resp.nomecompl;
                                    document.getElementById("letraFuncEscala").innerHTML = Resp.letra;
                                    document.getElementById("turnoFuncEscala").innerHTML = Resp.turno;
                                    document.getElementById("letraFuncEscala").innerHTML = Resp.letra;
                                    document.getElementById("guardaGrupo").value = Resp.grupo;
                                    document.getElementById("selecOcor").value = Resp.idOcor;
                                    document.getElementById("selecMotivo").value = Resp.idMot;
                                    document.getElementById("selecStatus").value = Resp.idStat;
                                    document.getElementById("selecAcaoAdm").value = Resp.idAdm;
                                    document.getElementById("guardaIdEscalaIns").value = Resp.idescalains;
                                    document.getElementById("observEscalado").value = Resp.observ;
                                    if(Resp.observ == ""){
                                        document.getElementById("apagarNotaFunc").style.visibility = "hidden";
                                        }else{
                                            document.getElementById("apagarNotaFunc").style.visibility = "visible";
                                    }
                                    document.getElementById("relacmodalAnotFunc").style.display = "block";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaNotaFunc(){
                if(document.getElementById("mudou").value == "0"){
                    document.getElementById("relacmodalAnotFunc").style.display = "none";
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaNotaFunc&codigo="+document.getElementById("guardaCodFunc").value
                    +"&data="+document.getElementById("titulomodal").innerHTML
                    +"&letra="+document.getElementById("letraFuncEscala").innerHTML
                    +"&turno="+document.getElementById("turnoFuncEscala").innerHTML
                    +"&grupo="+document.getElementById("guardaGrupo").value
                    +"&idEscalaIns="+document.getElementById("guardaIdEscalaIns").value
                    +"&selecOcor="+document.getElementById("selecOcor").value
                    +"&selecMotivo="+document.getElementById("selecMotivo").value
                    +"&selecStatus="+document.getElementById("selecStatus").value
                    +"&selecAcaoAdm="+document.getElementById("selecAcaoAdm").value
                    +"&observ="+encodeURIComponent(document.getElementById("observEscalado").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("relacmodalAnotFunc").style.display = "none";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function apagaNotaFunc(){
                $.confirm({
                    title: 'Apagar nota.',
                    content: 'Confirma apagar essa nota?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=apagaNotaFunc&codigo="+document.getElementById("guardaCodFunc").value
                                +"&data="+document.getElementById("titulomodal").innerHTML, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.");
                                            }else{
                                                document.getElementById("relacmodalAnotFunc").style.display = "none";
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

            function abreImprNotasFunc(){
                $("#notasCentrais").load("modulos/escaladaf/quadroImpr.php?numgrupo="+document.getElementById("guardanumgrupo").value+"&mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                document.getElementById("relacImprNotas").style.display = "block";
            }
            function imprNotasFunc(){
                window.open("modulos/escaladaf/imprNotas.php?acao=imprNotasGrupo&mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value)+"&numgrupo="+document.getElementById("selecGrupo").value+"&codigo=0", "NotasFunc");
                document.getElementById("selectNotasIndiv").value = "";
            }
            function imprNotasIndiv(Cod){
                if(Cod != ""){
                    window.open("modulos/escaladaf/imprNotas.php?acao=imprNotasIndiv&mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value)+"&numgrupo="+document.getElementById("selecGrupo").value+"&codigo="+Cod, "NotasFuncIndiv");
                    document.getElementById("selectNotasIndiv").value = "";
                }
            }
            function fechaImprNotas(){
                document.getElementById("relacImprNotas").style.display = "none";
            }
            function insOcor(){
                document.getElementById("guardaCodEdit").value = 0;
                document.getElementById("editNomeTipo").value = "";
                document.getElementById("relacEditTipo").style.display = "block";
            }
            function insMotivo(){
                document.getElementById("guardaCodEdit").value = 0;
                document.getElementById("editNomeMot").value = "";
                document.getElementById("relacEditMotivo").style.display = "block";
            }
            function insStat(){
                document.getElementById("guardaCodEdit").value = 0;
                document.getElementById("editNomeStat").value = "";
                document.getElementById("relacEditStat").style.display = "block";
            }
            function insAdm(){
                document.getElementById("guardaCodEdit").value = 0;
                document.getElementById("editNomeAdm").value = "";
                document.getElementById("relacEditAdm").style.display = "block";
            }

            function editaOcor(Cod){
                document.getElementById("guardaCodEdit").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=editOcor&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editNomeTipo").value = Resp.desc;
                                    document.getElementById("relacEditTipo").style.display = "block";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function editaMotivo(Cod){
                document.getElementById("guardaCodEdit").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=editMotivo&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editNomeMot").value = Resp.desc;
                                    document.getElementById("relacEditMotivo").style.display = "block";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function editaStat(Cod){
                document.getElementById("guardaCodEdit").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=editStat&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editNomeStat").value = Resp.desc;
                                    document.getElementById("relacEditStat").style.display = "block";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function editaAdm(Cod){
                document.getElementById("guardaCodEdit").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=editAdm&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editNomeAdm").value = Resp.desc;
                                    document.getElementById("relacEditAdm").style.display = "block";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }


            function salvaEditTipo(){
                if(document.getElementById("editNomeTipo").value == ""){
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaOcor&codigo="+document.getElementById("guardaCodEdit").value
                    +"&texto="+document.getElementById("editNomeTipo").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    $("#configOcorrencias").load("modulos/escaladaf/edNotaOcor.php");
                                    document.getElementById("relacEditTipo").style.display = "none";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function salvaEditMot(){
                if(document.getElementById("editNomeMot").value == ""){
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaMotivo&codigo="+document.getElementById("guardaCodEdit").value
                    +"&texto="+document.getElementById("editNomeMot").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    $("#configMotivos").load("modulos/escaladaf/edNotaMot.php");
                                    document.getElementById("relacEditMotivo").style.display = "none";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function salvaEditStat(){
                if(document.getElementById("editNomeStat").value == ""){
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaStat&codigo="+document.getElementById("guardaCodEdit").value
                    +"&texto="+document.getElementById("editNomeStat").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    $("#configStat").load("modulos/escaladaf/edNotaStat.php");
                                    document.getElementById("relacEditStat").style.display = "none";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function salvaEditAdm(){
                if(document.getElementById("editNomeAdm").value == ""){
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaAdm&codigo="+document.getElementById("guardaCodEdit").value
                    +"&texto="+document.getElementById("editNomeAdm").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    $("#configAdm").load("modulos/escaladaf/edNotaAdm.php");
                                    document.getElementById("relacEditAdm").style.display = "none";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function apagaTipo(){
                if(document.getElementById("editNomeTipo").value == ""){
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=apagaOcor&codigo="+document.getElementById("guardaCodEdit").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    $("#configOcorrencias").load("modulos/escaladaf/edNotaOcor.php");
                                    document.getElementById("relacEditTipo").style.display = "none";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function apagaMot(){
                if(document.getElementById("editNomeMot").value == ""){
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=apagaMotivo&codigo="+document.getElementById("guardaCodEdit").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    $("#configMotivo").load("modulos/escaladaf/edNotaMot.php");
                                    document.getElementById("relacEditMotivo").style.display = "none";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function apagaStat(){
                if(document.getElementById("editNomeStat").value == ""){
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=apagaStat&codigo="+document.getElementById("guardaCodEdit").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    $("#configStat").load("modulos/escaladaf/edNotaStat.php");
                                    document.getElementById("relacEditStat").style.display = "none";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function apagaAdm(){
                if(document.getElementById("editNomeAdm").value == ""){
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=apagaAdm&codigo="+document.getElementById("guardaCodEdit").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    $("#configAdm").load("modulos/escaladaf/edNotaAdm.php");
                                    document.getElementById("relacEditAdm").style.display = "none";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function fechaEditTipo(){
                document.getElementById("relacEditTipo").style.display = "none";
                document.getElementById("relacEditMotivo").style.display = "none";
                document.getElementById("relacEditStat").style.display = "none";
                document.getElementById("relacEditAdm").style.display = "none";
            }
            function fechaModalAnot(){
                document.getElementById("relacmodalAnotFunc").style.display = "none";
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
//                $("#modalEscalaConfig").draggable();
            });

        </script>
    </head>
    <body class="corClara" onbeforeunload="return mudaTema(0)"> <!-- ao sair retorna os background claros -->
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
            $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'poslog'");
            $rowSis = pg_num_rows($rsSis);
            if($rowSis == 0){
                echo "Sem contato com os arquivos do sistema. Informe à ATI.";
				return false;
            }
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

//-------------
//echo dirname(dirname(__FILE__));

//   pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf_trocas");
   pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_trocas (
      id SERIAL PRIMARY KEY, 
      poslog_id integer NOT NULL DEFAULT 0, 
      escaladaf_id integer NOT NULL DEFAULT 0, 

      dataescala_orig date DEFAULT '3000-12-31',
      letra_orig character varying(3),
      turno_orig character varying(30),
      codturno_orig integer NOT NULL DEFAULT 0,
      horafolga_orig character varying(30),  
      grupo_id integer NOT NULL DEFAULT 0,

      marca smallint DEFAULT 0 NOT NULL, 
      ativo smallint DEFAULT 1 NOT NULL, 
      usuins integer DEFAULT 0 NOT NULL,
      datains timestamp without time zone DEFAULT '3000-12-31',
      usuedit integer DEFAULT 0 NOT NULL,
      dataedit timestamp without time zone DEFAULT '3000-12-31' 
      ) 
  ");

//   pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf_func");
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_func (
        id SERIAL PRIMARY KEY, 
        poslog_id integer NOT NULL DEFAULT 0, 
        dataescala date DEFAULT '3000-12-31',
        letra character varying(3),
        turno character varying(30),
        observ text, 
        escaladafins_id integer NOT NULL DEFAULT 0, 
        grupo_id integer NOT NULL DEFAULT 0,
        ativo smallint DEFAULT 1 NOT NULL, 
        usuins integer DEFAULT 0 NOT NULL,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit integer DEFAULT 0 NOT NULL,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");


    //   pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf_funcoc"); // ocorrência
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_funcoc (
        id SERIAL PRIMARY KEY, 
        descocor character varying(100),
        ativo smallint DEFAULT 1 NOT NULL, 
        usuins integer DEFAULT 0 NOT NULL,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit integer DEFAULT 0 NOT NULL,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");
    $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_funcoc LIMIT 2");
    $row = pg_num_rows($rs);
    if($row == 0){
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcoc (id, descocor)  VALUES (1, '')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcoc (id, descocor)  VALUES (2, 'Falta')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcoc (id, descocor)  VALUES (3, 'Atraso')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcoc (id, descocor)  VALUES (4, 'Hora Extra')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcoc (id, descocor)  VALUES (5, 'Saida Antecipada')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcoc (id, descocor)  VALUES (6, 'Suspensão')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcoc (id, descocor)  VALUES (7, 'Registro Ponto Entrada')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcoc (id, descocor)  VALUES (8, 'Registro Ponto Intervalo')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcoc (id, descocor)  VALUES (9, 'Registro Ponto Saída')");
    }

    //   pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf_funcmot"); // motivo
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_funcmot (
        id SERIAL PRIMARY KEY, 
        descmot character varying(100),
        ativo smallint DEFAULT 1 NOT NULL, 
        usuins integer DEFAULT 0 NOT NULL,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit integer DEFAULT 0 NOT NULL,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");
    $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_funcmot LIMIT 2");
    $row = pg_num_rows($rs);
    if($row == 0){
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcmot (id, descmot)  VALUES (1, '')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcmot (id, descmot)  VALUES (2, 'Atestado Médico')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcmot (id, descmot)  VALUES (3, 'Atestado Comparec')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcmot (id, descmot)  VALUES (4, 'Transporte')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcmot (id, descmot)  VALUES (5, 'Trabalho Externo')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcmot (id, descmot)  VALUES (6, 'Trabalho Interno')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcmot (id, descmot)  VALUES (7, 'Esquecimento')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcmot (id, descmot)  VALUES (8, 'Falta Energia')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcmot (id, descmot)  VALUES (9, 'RPE Inoperante')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcmot (id, descmot)  VALUES (10, 'Particular')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcmot (id, descmot)  VALUES (11, 'Outros')");
    }

    //   pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf_funcstat"); // status
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_funcstat (
        id SERIAL PRIMARY KEY, 
        descstat character varying(100),
        ativo smallint DEFAULT 1 NOT NULL, 
        usuins integer DEFAULT 0 NOT NULL,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit integer DEFAULT 0 NOT NULL,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");
    $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_funcstat LIMIT 2");
    $row = pg_num_rows($rs);
    if($row == 0){
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcstat (id, descstat)  VALUES (1, '')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcstat (id, descstat)  VALUES (2, 'Autorizado')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcstat (id, descstat)  VALUES (3, 'Não Autorizado')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcstat (id, descstat)  VALUES (4, 'Justificado')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcstat (id, descstat)  VALUES (5, 'Não Justificado')");
    }

    //   pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf_funcadm"); // status
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_funcadm (
        id SERIAL PRIMARY KEY, 
        descadm character varying(100),
        ativo smallint DEFAULT 1 NOT NULL, 
        usuins integer DEFAULT 0 NOT NULL,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit integer DEFAULT 0 NOT NULL,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");
    $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_funcadm LIMIT 2");
    $row = pg_num_rows($rs);
    if($row == 0){
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcadm (id, descadm)  VALUES (1, '')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcadm (id, descadm)  VALUES (2, 'Banco de Horas')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcadm (id, descadm)  VALUES (3, 'Desconto Salário')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcadm (id, descadm)  VALUES (4, 'Abonar')");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_funcadm (id, descadm)  VALUES (5, 'Pagar Hora Extra')");
    }


//  dataescala_troca date DEFAULT '3000-12-31',
//  letra_troca character varying(3),
//  turno_troca character varying(30),


  //Provisório
if(strtotime('2025/03/10') > strtotime(date('Y/m/d'))){
    $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".poslog WHERE ativo = 1 And ordem_daf = 0 ");
    $row1 = pg_num_rows($rs1);
    if($row1 > 0){
        $rs2 = pg_query($Conec, "SELECT id, esc_grupo, ordem_daf FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY esc_grupo, nomeusual ");
        $row2 = pg_num_rows($rs2);
        if($row2 > 0){
            while($tbl2 = pg_fetch_row($rs2)){
                $CodGrupo = $tbl2[1];
                $Num = 1;
                $rs3 = pg_query($Conec, "SELECT id, esc_grupo, ordem_daf FROM ".$xProj.".poslog WHERE ativo = 1 And esc_grupo = $CodGrupo ORDER BY nomeusual ");
                $row3 = pg_num_rows($rs3);
                if($row3 > 0){
                    while($tbl3 = pg_fetch_row($rs3)){
                        $Cod = $tbl3[0];
                        pg_query($Conec, "UPDATE ".$xProj.".poslog SET ordem_daf = $Num WHERE id = $Cod And ativo = 1");
                        $Num++;
                    }
                }
            }
        }
    }
}
//------------

    $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataescala, 'MM'), '/', TO_CHAR(dataescala, 'YYYY')) 
    FROM ".$xProj.".escaladaf WHERE grupo_id = $NumGrupo GROUP BY TO_CHAR(dataescala, 'MM'), TO_CHAR(dataescala, 'YYYY') ORDER BY TO_CHAR(dataescala, 'YYYY') DESC, TO_CHAR(dataescala, 'MM') DESC ");

    $rsGr = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_esc WHERE usu_id = ".$_SESSION["usuarioID"]." And ativo = 1");
    $rowGr = pg_num_rows($rsGr); // quantidade de grupos em que é escalante
    
    if($_SESSION["usuarioID"] == 83){ // provisório
        $OpcoesGrupo = pg_query($Conec, "SELECT id, siglagrupo FROM ".$xProj.".escalas_gr WHERE ativo = 1 ORDER BY siglagrupo");
    }else{
        if($rowGr > 1){ // seleciona só os grupos em que é escalante
            $OpcoesGrupo = pg_query($Conec, "SELECT ".$xProj.".escalas_gr.id, siglagrupo 
            FROM ".$xProj.".escalas_gr INNER JOIN ".$xProj.".escaladaf_esc ON ".$xProj.".escalas_gr.id = ".$xProj.".escaladaf_esc.grupo_id 
            WHERE ".$xProj.".escalas_gr.ativo = 1 And ".$xProj.".escaladaf_esc.ativo = 1 And usu_id = ".$_SESSION["usuarioID"]." ORDER BY siglagrupo"); 
        }else{
            $OpcoesGrupo = pg_query($Conec, "SELECT id, siglagrupo FROM ".$xProj.".escalas_gr WHERE ativo = 1 ORDER BY siglagrupo");
        }
    }

    $OpcoesTransfMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataescala, 'MM'), '/', TO_CHAR(dataescala, 'YYYY')) 
    FROM ".$xProj.".escaladaf WHERE grupo_id = $NumGrupo  
    GROUP BY TO_CHAR(dataescala, 'MM'), TO_CHAR(dataescala, 'YYYY') ORDER BY TO_CHAR(dataescala, 'YYYY') DESC, TO_CHAR(dataescala, 'MM') DESC ");

    $OpConfig = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
    
    $DiaIni = strtotime(date('Y/m/01')); // número - para começar com o dia 1
    $DiaIni = strtotime("-1 day", $DiaIni); // para começar com o dia 1 no loop for

    //Mantem a tabela escaladaf meses à frente
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

    $Escalante = parEsc("esc_daf", $Conec, $xProj, $_SESSION["usuarioID"]);
    $Fiscal = parEsc("esc_fisc", $Conec, $xProj, $_SESSION["usuarioID"]);
    $MesSalvo = parEsc("mes_escdaf", $Conec, $xProj, $_SESSION["usuarioID"]);
    $CorListas = parEsc("corlistas_daf", $Conec, $xProj, $_SESSION["usuarioID"]);
    $Tema = parEsc("tema", $Conec, $xProj, $_SESSION["usuarioID"]); // Claro(0) Escuro(1)

    //Ver se o que está guardado em poslog corresponde a algum mes salvo em escaladaf
    $rsMes = pg_query($Conec, "SELECT id 
    FROM ".$xProj.".escaladaf WHERE grupo_id = $NumGrupo And CONCAT(TO_CHAR(dataescala, 'MM'), '/', TO_CHAR(dataescala, 'YYYY')) = '$MesSalvo' ");
    $rowMes = pg_num_rows($rsMes);

    if(is_null($MesSalvo) || $MesSalvo == "" || $rowMes == 0){
        $MesSalvo = date("m")."/".date("Y");
        pg_query($Conec, "UPDATE ".$xProj.".poslog SET mes_escdaf = '$MesSalvo' WHERE pessoas_id = ". $_SESSION["usuarioID"]."" );
    }

    $Proc = explode("/", $MesSalvo);
    $MesEscala = $Proc[0];
    if(strLen($MesEscala) < 2){
        $MesEscala = "0".$Mes;
    }
    $AnoEscala = $Proc[1];

    $MesAtual = date('m');
    $AnoAtual = date('Y');

    if($MesEscala == $MesAtual && $AnoEscala == $AnoAtual){
        pg_query($Conec, "UPDATE ".$xProj.".escalas_gr SET editaesc = 1 WHERE id = $NumGrupo ");    
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

    $MesLiberado = 0;

    //Mantém feriados fixos dois anos à frente atualizando com o arquivo escaladaf_fer_padr
    $rsFer = pg_query($Conec, "SELECT id, TO_CHAR(dataescalafer, 'DD'), TO_CHAR(dataescalafer, 'MM'), descr FROM ".$xProj.".escaladaf_fer_padr WHERE ativo = 1");
	$rowFer = pg_num_rows($rsFer);
	if($rowFer > 0){
        while($tblFer = pg_fetch_row($rsFer)){
            $Cod = $tblFer[0];
            $Descr = $tblFer[3];
            $DiaFer = $tblFer[1];
            $MesFer = $tblFer[2];
            $Feriado = ($Ano+1)."/".$MesFer."/".$DiaFer;
            $rsProc = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_fer WHERE ativo = 1 And dataescalafer = '$Feriado'");
            $rowProc = pg_num_rows($rsProc);
            if($rowProc == 0){
                $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_fer");
                $tblCod = pg_fetch_row($rsCod);
                $Codigo = $tblCod[0];
                $CodigoNovo = ($Codigo+1);
                pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, ativo) 
                VALUES($CodigoNovo, '$Feriado', '$Descr', 1) ");
            }
            $Feriado = ($Ano+2)."/".$MesFer."/".$DiaFer;
            $rsProc = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_fer WHERE ativo = 1 And dataescalafer = '$Feriado'");
            $rowProc = pg_num_rows($rsProc);
            if($rowProc == 0){
                $rsCod = pg_query($Conec, "SELECT MAX(id) FROM ".$xProj.".escaladaf_fer");
                $tblCod = pg_fetch_row($rsCod);
                $Codigo = $tblCod[0];
                $CodigoNovo = ($Codigo+1);
                pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_fer (id, dataescalafer, descr, ativo) 
                VALUES($CodigoNovo, '$Feriado', '$Descr', 1) ");
            }

        }
    }

    ?>
        <input type="hidden" id="guardamesano" value="<?php echo addslashes($MesSalvo); ?>" />
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>" />
        <input type="hidden" id="guardaUsuId" value="<?php echo $_SESSION["usuarioID"]; ?>" />
        <input type="hidden" id="escalante" value="<?php echo $Escalante; ?>" />
        <input type="hidden" id="fiscal" value="<?php echo $Fiscal; ?>" />
        <input type="hidden" id="guardameugrupo" value="<?php echo $MeuGrupo; ?>" />
        <input type="hidden" id="guardanumgrupo" value="<?php echo $NumGrupo; ?>" />
        <input type="hidden" id="guardaDiaId" value="" />
        <input type="hidden" id="guardacod" value="" />
        <input type="hidden" id="mudou" value="0" />
        <input type="hidden" id="liberadoefetivo" value="<?php echo $MesLiberado; ?>" />
        <input type="hidden" id="guardaCodTurno" value="0" />
        <input type="hidden" id="quantGruposEsc" value="<?php echo $rowGr; ?>" />
        <input type="hidden" id="guardaAno" value="<?php echo $Ano; ?>" />
        <input type="hidden" id="guardaCorListas" value="<?php echo $CorListas; ?>" />
        <input type="hidden" id="guardaCodFunc" value = "0" />
        <input type="hidden" id="guardaGrupo" value = "0" />
        <input type="hidden" id="guardaIdEscalaIns" value = "0" />
        <input type="hidden" id="guardaTema" value = "<?php echo $Tema; ?>" />
        <input type="hidden" id="guardaCodEdit" value = "0" />
        

        <div style="margin: 5px; border: 2px solid green; border-radius: 15px; padding: 5px;">
            <div id="tricoluna0" class="row" style="margin: 0 auto;"> <!-- botões Inserir e Imprimir-->

                <div id="tricoluna1" class="col" style="margin: 0 auto; text-align: left;">
                    <img src="imagens/settings.png" height="20px;" id="imgEscalaConfig" style="cursor: pointer; padding-left: 20px;" onclick="abreEscalaConfig();" title="Configurar o acesso e inserir participantes da escala">
                    <label class="etiq eItalic" style="padding-left: 20px;">Escala mês: </label>
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
                    <label id="etiqGrupo" class="etiq eItalic" style="padding-left: 5px;">Ver Grupo: </label>
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
                    <!-- Suspenso -->
                        <label style="padding-left: 10px;"></label>
                        <input type="checkbox" id="evliberames" title="Liberar acesso aos participantes da escala" onClick="liberaMes(this);" <?php if($MesLiberado == 1) {echo "checked";} ?> >
                        <label id="etiqevliberames" for="evliberames" title="Acesso aos participantes da escala">liberado</label>
                </div>

                <div id="tricoluna2" class="col corCinza" style="text-align: center; font-size: 80%;">Escala <?php echo $SiglaGrupo; ?> </div> <!-- espaçamento entre colunas  -->

                <div id="tricoluna3" class="col" style="margin: 0 auto; text-align: center;">
                    <label id="etiqtransfMesAnoEsc" class="etiq eItalic" style="padding-left: 40px;">Transferir para o mês: </label>
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
                    <label style="padding-left: 10px;"></label>
                    <button id="botImprimir" class="botpadrred" onclick="imprPlanilha();">PDF</button>
                    <div id="escolhaTema" style="position: relative; float: right;">
                        <label style="padding-left: 5px;"></label>
                        <img id="imgEspera" src="imagens/gears-512.gif" height="20px;">
                        <label id="etiqcorFundo" class="etiq" style="color: #6C7AB3; font-size: 80%; padding-left: 65px;">Tema: </label>
                        <input type="radio" name="corFundo" id="corFundo0" value="0" <?php if($Tema == 0){echo 'CHECKED';}; ?> title="Tema claro" onclick="mudaTema(0);" style="cursor: pointer;"><label for="corFundo0" class="etiq" style="cursor: pointer;">&nbsp;Claro</label>
                        <input type="radio" name="corFundo" id="corFundo1" value="1" <?php if($Tema == 1){echo 'CHECKED';}; ?> title="Tema escuro" onclick="mudaTema(1);" style="cursor: pointer;"><label for="corFundo1" class="etiq" style="cursor: pointer;">&nbsp;Escuro</label>
                        <label style="padding-right: 10px;"></label>
                    </div>
                </div> <!-- quadro -->
            </div>
        </div>

        <div style="margin: 5px; border: 2px solid green; border-radius: 15px; padding: 5px; min-height: 70px; text-align: center;">
            <table style="margin: 0 auto; width: 90%;">
                <tr>
                    <td>
                        <div class="container" style="margin: 0 auto;">
                            <div class="row">
                                <div class="col"></div>
                                <div class="col" style="text-align: center;"></div> <!-- Central - espaçamento entre colunas  -->
                                <div class="col" style="position: relative; float: rigth; text-align: right;"></div> 
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
                <div class="col"></div>
                <div class="col" style="text-align: center;">
                    <div id="faixaferiados"></div> 
                </div> <!-- Central -->
                <div class="col" style="position: relative; float: rigth; text-align: right;"></div> 
            </div>
        </div>

        <div id="carregaTema"></div> <!-- carrega a pág modulos/config/carTema.php - onde está a função mudaTema() -->

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
                <h5 style="text-align: center;color: #666;">Edição de Nota ao Horário de Trabalho</h5>
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
                <label class="etiqAzul" style="padding-left: 15px;">Selecione o Ano: </label>
                <select id="selecAnoFer" style="font-size: .8rem; width: 70px;" title="Selecione o ano.">
                    <?php 
                        $OpcoesAno = pg_query($Conec, "SELECT TO_CHAR(dataescalafer, 'YYYY') FROM ".$xProj.".escaladaf_fer WHERE ativo = 1 GROUP BY TO_CHAR(dataescalafer, 'YYYY') ORDER BY TO_CHAR(dataescalafer, 'YYYY')");
                        if($OpcoesAno){
                            while ($Opcoes = pg_fetch_row($OpcoesAno)){ ?>
                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                            <?php 
                        }
                    }
                    ?>
                </select>

                <div id="relacaoFeriados"></div>
            </div>
        </div> <!-- Fim Modal-->


         <!-- Modal configuração-->
         <div id="modalEscalaConfig" class="relacmodal">
            <div class="modal-content-escalaControle">
                <span class="close" onclick="fechaEscalaConfig();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col" style="margin: 0 auto;"><button class="botpadrred" id="imprGrupos" style="font-size: 70%;" onclick="geralEscala();" title="Mostra a relação dos chefes, escalantes e efetivo das escalas">Geral Grupos</button></div>
                        <div class="col"><h6 id="etiqNomeGrupo" style="text-align: center; color: #666; font-size: 80%;">Escala <?php echo $SiglaGrupo; ?></h6></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col" style="margin: 0 auto; text-align: center;"><button class="botpadrred" style="font-size: 70%;" onclick="resumoUsuEscala();" title="Mostra uma relação dos chefes, escalante e efetivo deste grupo">Resumo do Grupo</button></div> 
                    </div>
                </div>
                <label class="etiqAzul">Selecione um usuário para ver a configuração:</label>

                <label for='checkvisucargo' id='etiqcheckvisucargo' class='etiqAzul' style='padding-left: 15px'>Mostrar Cargo</label>
                <input type='checkbox' id='checkvisucargo' onchange='marcaVisuCargo(this);' title='Visualisar cargo no formulário' >
                <label for='checkprimcargo' id='etiqcheckprimcargo' class='etiqAzul' style='padding-left: 15px'>Primeiro o Cargo</label> 
                <input type='checkbox' id='checkprimcargo' onchange='marcaPrimCargo(this);' title='Primeiro o cargo depois o nome' >
                <label for='checksemanaIniFim' id='etiqchecksemanaIniFim' class='etiqAzul' style='padding-left: 15px' title='Mostar as semanas inicial e final na contagem da carga horária'>Semanas Inicial e Final</label> 
                <input type='checkbox' id='checksemanaIniFim' onchange='marcaSemanaIniFim(this);' title='Mostar as semanas inicial e final na contagem da carga horária' >
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
                            <input type="text" id="configCpfEscala" placeholder="CPF" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('configSelecEscala');return false;}" title="Procura por CPF. Digite o CPF."/>
                            <label class="etiqAzul" style="padding-left: 10px">Função: </label>
                            <input type="text" id="configCargoEscala" maxlength="15" placeholder="Cargo/FG" style="width: 150px; border: 1px solid #666; border-radius: 5px;" title="Digite o cargo/FG"/>

                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                    </tr>
                </table>

                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td class="etiqAzul eItalic">Escala DAF:</td>
                        <td colspan="2">
                            <input type="checkbox" id="checkefetivo" onchange="marcaConfigEscalaEft(this);" >
                            <label for="checkefetivo" class="etiqNorm eItalic">efetivo desta escala</label>
                        </td>
                        <td colspan="2">
                            <label id="mensagemConfig" style="color: red; font-weight: bold;"></label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiqAzul eItalic">Escala DAF:</td>
                        <td colspan="4">
                            <input type="checkbox" id="checkescalante" onchange="marcaConfigEscalaEsc(this);" >
                            <label for="checkescalante" class="etiqNorm eItalic">escalante desta escala</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiqAzul eItalic">Escala DAF:</td>
                        <td colspan="4">
                            <input type="checkbox" id="checkfiscal" onchange="marcaConfigEscalaFisc(this);" title="Para acompanhar e fiscalizar todas as escalas." >
                            <label for="checkfiscal" class="etiqNorm eItalic" title="Para acompanhar e fiscalizar todas as escalas.">fiscal de escalas</label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5"><hr style="margin: 0; padding: 2px;"></td>
                    </tr>
                    <tr>
                        <td class="etiqAzul eItalic">Encarregado ADM:</td>
                        <td colspan="4">
                            <select id="configSelecEncarreg" style="max-width: 300px;" onchange="modif();" title="Selecione um usuário.">
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
                        <td class="etiqAzul eItalic">Chefe DIV ADM:</td>
                        <td colspan="4">
                            <select id="configSelecChefeDiv" style="max-width: 300px;" onchange="modif();" title="Selecione um usuário.">
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
                    <tr>
                        <td colspan="5" style="padding-top: 3px;"></td>
                    </tr>
                    <tr>
                        <td colspan="5"><hr style="margin: 0; padding: 2px;"></td>
                    </tr>
                    <tr>
                        <td class="etiqAzul eItalic" title="Cor das listas na tela (Tema: Claro) e na impressão do PDF">Cor das Listas:</td>
                        <td colspan="4" style="text-align: left;">
                            <input type="radio" name="corlistas" id="corlista0" value="0" <?php if($CorListas == 0){echo "CHECKED";} ?> title="Branco" onclick="salvaCor(0);"><label for="corlista0"><div style="padding-left: 3px; width: 60px; border-radius: 5px; color: black; background: #FFFFFF; font-size: 70%;">Branco</div></label>
                            <input type="radio" name="corlistas" id="corlista1" value="1" <?php if($CorListas == 1){echo "CHECKED";} ?> title="Cornsilk" onclick="salvaCor(1);"><label for="corlista1"><div style="padding-left: 3px; width: 60px; border-radius: 5px; color: black; background: #FFF8DC; font-size: 70%;">Cornsilk</div></label>
                            <input type="radio" name="corlistas" id="corlista2" value="1" <?php if($CorListas == 2){echo "CHECKED";} ?> title="Azure" onclick="salvaCor(2);"><label for="corlista2"><div style="padding-left: 3px; width: 60px; border-radius: 5px; color: black; background: #F0FFFF; font-size: 70%;">Azure</div></label>
                            <input type="radio" name="corlistas" id="corlista3" value="1" <?php if($CorListas == 3){echo "CHECKED";} ?> title="Lavanda" onclick="salvaCor(3);"><label for="corlista3"><div style="padding-left: 3px; width: 60px; border-radius: 5px; color: black; background: #E6E6FA; font-size: 70%;">Lavanda</div></label>
                            <input type="radio" name="corlistas" id="corlista4" value="1" <?php if($CorListas == 4){echo "CHECKED";} ?> title="Marfim" onclick="salvaCor(4);"><label for="corlista4"><div style="padding-left: 3px; width: 60px; border-radius: 5px; color: black; background: #EEEEE0; font-size: 70%;">Marfim</div></label>
                            <br>
                            <input type="radio" name="corlistas" id="corlista5" value="1" <?php if($CorListas == 5){echo "CHECKED";} ?> title="Cinza" onclick="salvaCor(5);"><label for="corlista5"><div style="padding-left: 3px; width: 60px; border-radius: 5px; color: black; background: #BEBEBE; font-size: 70%;">Cinza</div></label>
                            <input type="radio" name="corlistas" id="corlista6" value="1" <?php if($CorListas == 6){echo "CHECKED";} ?> title="Magenta" onclick="salvaCor(6);"><label for="corlista6"><div style="padding-left: 3px; width: 60px; border-radius: 5px; color: black; background: #FF00FF; font-size: 70%;">Magenta</div></label>
                            <input type="radio" name="corlistas" id="corlista7" value="1" <?php if($CorListas == 7){echo "CHECKED";} ?> title="Violeta" onclick="salvaCor(7);"><label for="corlista7"><div style="padding-left: 3px; width: 60px; border-radius: 5px; color: black; background: #EE82EE; font-size: 70%;">Violeta</div></label>
                            <input type="radio" name="corlistas" id="corlista8" value="1" <?php if($CorListas == 8){echo "CHECKED";} ?> title="Laranja" onclick="salvaCor(8);"><label for="corlista8"><div style="padding-left: 3px; width: 60px; border-radius: 5px; color: black; background: #FFA500; font-size: 70%;">Laranja</div></label>
                            <input type="radio" name="corlistas" id="corlista9" value="1" <?php if($CorListas == 9){echo "CHECKED";} ?> title="Ciano" onclick="salvaCor(9);"><label for="corlista9"><div style="padding-left: 3px; width: 60px; border-radius: 5px; color: black; background: #00EEEE; font-size: 70%;">Ciano</div></label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="padding-top: 3px; padding-bottom: 3px;"></td>
                    </tr>
                    <tr>
                        <td colspan="5"><hr style="margin: 0; padding: 2px;"></td>
                    </tr>
                </table>

                <?php
                if($_SESSION["AdmUsu"] > 6){
                ?>
                <hr>
                <div style="margin-top: 5px; padding-top: 10px; border-top: 2px solid;">
                    <label class="corPreta" style="padding-bottom: 10px;">Configurações: parâmetros para anotações na escala</label>
                    <table>
                        <tr style="vertical-align: top;">
                            <td>
                                <div style="margin: 10px; min-width: 200px; padding: 5px; text-align: center; border: 1px solid; border-radius: 15px; background: linear-gradient(180deg, white, #86c1eb);">
                                    <div class='divbot corFundo' onclick='insOcor()' title="Adicionar tipo de ocorrência"> Adicionar </div>
                                    <div id="configOcorrencias" style="text-align: center; color: black;"></div>
                                </div>
                            </td>

                            <td>
                                <div style="margin: 10px; min-width: 200px; padding: 5px; text-align: center; border: 1px solid; border-radius: 15px; background: linear-gradient(180deg, white, #86c1eb);">
                                    <div class='divbot corFundo' onclick='insMotivo()' title="Adicionar motivo"> Adicionar </div>
                                    <div id="configMotivos" style="text-align: center; color: black;"></div>
                                </div>
                            </td>

                            <td>
                                <div style="margin: 10px; min-width: 180px; padding: 5px; text-align: center; border: 1px solid; border-radius: 15px; background: linear-gradient(180deg, white, #86c1eb);">
                                    <div class='divbot corFundo' onclick='insStat()' title="Adicionar status"> Adicionar </div>
                                    <div id="configStat" style="text-align: center; color: black;"></div>
                                </div>
                            </td>

                            <td>
                                <div style="margin: 10px; min-width: 180px; padding: 5px; text-align: center; border: 1px solid; border-radius: 15px; background: linear-gradient(180deg, white, #86c1eb);">
                                    <div class='divbot corFundo' onclick='insAdm()' title="Adicionar ação administrativa"> Adicionar </div>
                                    <div id="configAdm" style="text-align: center; color: black;"></div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <?php
                    
                }
                ?>
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

        <!-- div modal para anotações individuais   -->
        <div id="relacmodalAnotFunc" class="relacmodal"> 
            <div class="modal-content-AnotFunc corPreta">
                <span class="close" onclick="fechaModalAnot();">&times;</span>
                <div style="text-align: center;"><h6>Anotações da Escala</h6></div>
                <table style="margin: 0 auto;">
                    <tr>
                        <td class="etiqAzul">Data:</td>
                        <td><label id="dataFuncEscala" style="border: 1px solid #666; border-radius: 5px; padding-left: 5px; padding-right: 5px;"></label>
                            <label style="padding-left: 5px; padding-right: 5px;">Letra:</label>
                            <label id="letraFuncEscala" style="border: 1px solid #666; border-radius: 5px; padding-left: 5px; padding-right: 5px;"></label>
                            <label style="padding-left: 5px; padding-right: 5px;">Turno:</label>
                            <label id="turnoFuncEscala" style="border: 1px solid #666; border-radius: 5px; padding-left: 5px; padding-right: 5px;"></label>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiqAzul">Nome: </td>
                        <td><label id="nomeEscalado" style="border: 1px solid #666; border-radius: 5px; padding-left: 5px; padding-right: 5px;"></label></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3" style="text-align: center;">
                            <label class="etiqAzul">Ocorrência: </label>
                            <select id="selecOcor" style="font-size: .8rem; width: 130px;" onchange="modif();" title="Selecione a ocorrência.">
                            <?php 
                                $OpcoesOcor = pg_query($Conec, "SELECT id, descocor FROM ".$xProj.".escaladaf_funcoc WHERE ativo = 1 ORDER BY descocor");
                                if($OpcoesOcor){
                                    while ($Opcoes = pg_fetch_row($OpcoesOcor)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                    <?php 
                                }
                            }
                            ?>
                            </select>
                            <label class="etiqAzul">Motivo: </label>
                            <select id="selecMotivo" style="font-size: .8rem; width: 120px;" onchange="modif();" title="Selecione o motivo.">
                            <?php 
                                $OpcoesMot = pg_query($Conec, "SELECT id, descmot FROM ".$xProj.".escaladaf_funcmot WHERE ativo = 1 ORDER BY descmot");
                                if($OpcoesMot){
                                    while ($Opcoes = pg_fetch_row($OpcoesMot)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                    <?php 
                                }
                            }
                            ?>
                            </select>

                            <label class="etiqAzul">Status: </label>
                            <select id="selecStatus" style="font-size: .8rem; width: 120px;" onchange="modif();" title="Selecione o status.">
                            <?php 
                                $OpcoesStat = pg_query($Conec, "SELECT id, descstat FROM ".$xProj.".escaladaf_funcstat WHERE ativo = 1 ORDER BY descstat");
                                if($OpcoesStat){
                                    while ($Opcoes = pg_fetch_row($OpcoesStat)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                    <?php 
                                }
                            }
                            ?>
                            </select>

                            <label class="etiqAzul">Ação Adm: </label>
                            <select id="selecAcaoAdm" style="font-size: .8rem; width: 120px;" onchange="modif();" title="Selecione a ação da administração.">
                            <?php 
                                $OpcoesAdm = pg_query($Conec, "SELECT id, descadm FROM ".$xProj.".escaladaf_funcadm WHERE ativo = 1 ORDER BY descadm");
                                if($OpcoesAdm){
                                    while ($Opcoes = pg_fetch_row($OpcoesAdm)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                    <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiqAzul">Observações: </td>
                        <td><textarea class="form-control" id="observEscalado" style="resize: both; margin-top: 3px; border: 1px solid blue; border-radius: 10px; padding: 4px;" rows="6" cols="70" title="Texto da nota" onchange="modif();"></textarea></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td style="text-align: center; padding-top: 10px;"><button class="botpadrred" id="apagarNotaFunc" style="font-size: 60%; padding-left: 3px; padding-right: 3px;" onclick="apagaNotaFunc();">Apagar</button></td>
                        <td style="text-align: center; padding-top: 10px;"><button class="botpadrblue" onclick="salvaNotaFunc();">Salvar</button></td>
                        <td></td>
                    </tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para escolher imprimir em pdf  -->
        <div id="relacImprNotas" class="relacmodal">
            <div class="modal-content-escImprNotas corPreta">
                <span class="close" onclick="fechaImprNotas();">&times;</span>
                <h5 style="text-align: center;color: #666;">Anotações da Escala</h5>
                <h6 style="text-align: center; padding-bottom: 8px; color: #666;">Gerar PDF</h6>
                <div id="notasCentrais" style="border: 2px solid; border-radius: 10px; padding: 10px; text-align: center;"></div>
                <div style="padding-bottom: 20px;"></div>
           </div>
        </div> <!-- Fim Modal Impr -->


        <!-- div modal para editar Ocorr em Notas na escala  -->
        <div id="relacEditTipo" class="relacmodal">
            <div class="modal-content-InsTipo">
                <span class="close" onclick="fechaEditTipo();">&times;</span>
                <h5 style="text-align: center; color: #666;">Ocorrência</h5>
                    <table style="margin: 0 auto; width: 90%">
                        <tr>
                            <td class="etiq aDir">Texto: </td>
                            <td><input type="text" id="editNomeTipo" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%;"></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                    <br>
                    <div style="text-align: center;">
                        <button id="botApagaEditTipo" class="resetbotred" style="font-size: .8rem;" onclick="apagaTipo();">Apagar</button>
                        <label style="padding-left: 50%;"></label>
                        <button id="botSalvarEditTipo" class="resetbot" style="font-size: .9rem;" onclick="salvaEditTipo();">Salvar</button>
                    </div>
                </div>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para editar Ocorr em Notas na escala  -->
        <div id="relacEditMotivo" class="relacmodal">
            <div class="modal-content-InsTipo">
                <span class="close" onclick="fechaEditTipo();">&times;</span>
                <h5 style="text-align: center; color: #666;">Motivo</h5>
                    <table style="margin: 0 auto; width: 90%">
                        <tr>
                            <td class="etiq aDir">Texto: </td>
                            <td><input type="text" id="editNomeMot" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%;"></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                    <br>
                    <div style="text-align: center;">
                        <button id="botApagaEditMot" class="resetbotred" style="font-size: .8rem;" onclick="apagaMot();">Apagar</button>
                        <label style="padding-left: 50%;"></label>
                        <button id="botSalvarEditMot" class="resetbot" style="font-size: .9rem;" onclick="salvaEditMot();">Salvar</button>
                    </div>
                </div>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para editar Ocorr em Notas na escala  -->
        <div id="relacEditStat" class="relacmodal">
            <div class="modal-content-InsTipo">
                <span class="close" onclick="fechaEditTipo();">&times;</span>
                <h5 style="text-align: center; color: #666;">Status</h5>
                    <table style="margin: 0 auto; width: 90%">
                        <tr>
                            <td class="etiq aDir">Texto: </td>
                            <td><input type="text" id="editNomeStat" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%;"></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                    <br>
                    <div style="text-align: center;">
                        <button id="botApagaEditStat" class="resetbotred" style="font-size: .8rem;" onclick="apagaStat();">Apagar</button>
                        <label style="padding-left: 50%;"></label>
                        <button id="botSalvarEditStat" class="resetbot" style="font-size: .9rem;" onclick="salvaEditStat();">Salvar</button>
                    </div>
                </div>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para editar Ocorr em Notas na escala  -->
        <div id="relacEditAdm" class="relacmodal">
            <div class="modal-content-InsTipo">
                <span class="close" onclick="fechaEditTipo();">&times;</span>
                <h5 style="text-align: center; color: #666;">Ação</h5>
                    <table style="margin: 0 auto; width: 90%">
                        <tr>
                            <td class="etiq aDir">Texto: </td>
                            <td><input type="text" id="editNomeAdm" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%;"></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                    <br>
                    <div style="text-align: center;">
                        <button id="botApagaEditAdm" class="resetbotred" style="font-size: .8rem;" onclick="apagaAdm();">Apagar</button>
                        <label style="padding-left: 50%;"></label>
                        <button id="botSalvarEditAdm" class="resetbot" style="font-size: .9rem;" onclick="salvaEditAdm();">Salvar</button>
                    </div>
                </div>
            </div>
        </div> <!-- Fim Modal-->

    </body>
</html>