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
        <script src="class/bootstrap/js/bootstrap.min.js"></script>
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="comp/js/jquery-ui.min.js"></script>
        <script src="comp/js/jquery.mask.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script>   <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <style>
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
                width: 40%;
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
                min-width: 10px;
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
            .etiq{
                text-align: right; font-size: 70%; font-weight: bold; padding-right: 1px; padding-bottom: 1px;
            }
        </style>
        <script>
            $(document).ready(function(){
                document.getElementById("imgEscalaConfig").style.visibility = "hidden"; 
                if(parseInt(document.getElementById("escalante").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){ // // se estiver marcado
                    document.getElementById("imgEscalaConfig").style.visibility = "visible"; 
                }

                document.getElementById("selecMesAnoEsc").value = document.getElementById("guardamesano").value;
                if(parseInt(document.getElementById("liberadoefetivo").value) === 0 && parseInt(document.getElementById("escalante").value) === 0){
                    $("#faixacentral").load("modulos/escaladaf/infoAgd1.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                    $("#faixaquadro").load("modulos/escaladaf/infoAgd2.php");
                    $("#faixanotas").load("modulos/escaladaf/infoAgd3.php");
                     document.getElementById("botImprimir").style.visibility = "hidden";
                }else{
                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                    $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                    $("#faixanotas").load("modulos/escaladaf/notasdaf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                    document.getElementById("botImprimir").style.visibility = "visible";
                }

                $("#selecMesAnoEsc").change(function(){
                    if(parseInt(document.getElementById("selecMesAnoEsc").value) > 0){
//                        $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvamesano&mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value), true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText);
                                        Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                        if(parseInt(Resp.coderro) === 0){
                                            if(parseInt(Resp.mesliberado) === 0 && parseInt(document.getElementById("escalante").value) === 0){
                                                $("#faixacentral").load("modulos/escaladaf/infoAgd1.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                                                $("#faixaquadro").load("modulos/escaladaf/infoAgd2.php");
                                                $("#faixanotas").load("modulos/escaladaf/infoAgd3.php");
                                                document.getElementById("botImprimir").style.visibility = "hidden";
                                            }else{
                                                $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                                                $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                                                $("#faixanotas").load("modulos/escaladaf/notasdaf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
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


                $("#configSelecEscala").change(function(){
                    if(document.getElementById("configSelecEscala").value == ""){
                        document.getElementById("configCpfEscala").value = "";
                        document.getElementById("checkefetivo").checked = false;
                        document.getElementById("checkescalante").checked = false;
                        document.getElementById("checkEncarreg").checked = false;
                        document.getElementById("checkChefeADM").checked = false;
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
                                        if(parseInt(Resp.encarreg) === 1){
                                            document.getElementById("checkEncarreg").checked = true;
                                        }else{
                                            document.getElementById("checkEncarreg").checked = false;
                                        }
                                        if(parseInt(Resp.chefeadm) === 1){
                                            document.getElementById("checkChefeADM").checked = true;
                                        }else{
                                            document.getElementById("checkChefeADM").checked = false;
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
                    document.getElementById("checkefetivo").checked = false;
                    document.getElementById("checkescalante").checked = false;
                    document.getElementById("checkEncarreg").checked = false;
                    document.getElementById("checkChefeADM").checked = false;
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
                                        if(parseInt(Resp.encarreg) === 1){
                                            document.getElementById("checkEncarreg").checked = true;
                                        }else{
                                            document.getElementById("checkEncarreg").checked = false;
                                        }
                                        if(parseInt(Resp.chefeadm) === 1){
                                            document.getElementById("checkChefeADM").checked = true;
                                        }else{
                                            document.getElementById("checkChefeADM").checked = false;
                                        }
                                    }
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.");
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("checkefetivo").checked = false;
                                        document.getElementById("checkescalante").checked = false;
                                        document.getElementById("checkEncarreg").checked = false;
                                        document.getElementById("checkChefeADM").checked = false;
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
                document.getElementById("guardaDiaId").value = DiaId; // id do dia em escaladaf
                document.getElementById("titulomodal").innerHTML = DataDia;
                $("#relacaoParticip").load("modulos/escaladaf/equipe.php");
                document.getElementById("relacParticip").style.display = "block";
            }

            function abreEditHorario(){
                $("#relacaoHorarios").load("modulos/escaladaf/edHorarios.php");
                document.getElementById("relacQuadroHorario").style.display = "block";
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
                                    $("#relacaoHorarios").load("modulos/escaladaf/edHorarios.php");
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
                                    $('#mensagemQuadroHorario').fadeIn("slow");
                                    document.getElementById("mensagemQuadroHorario").innerHTML = "Valor salvo.";
                                    $('#mensagemQuadroHorario').fadeOut(2000);
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function editaTurno(Cod, Valor){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaturno&codigo="+Cod+"&valor="+encodeURIComponent(Valor), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $('#mensagemQuadroHorario').fadeIn("slow");
                                    document.getElementById("mensagemQuadroHorario").innerHTML = "Valor salvo.";
                                    $('#mensagemQuadroHorario').fadeOut(2000);
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
                if(document.getElementById("insturno").value == ""){
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
                                    $("#relacaoHorarios").load("modulos/escaladaf/edHorarios.php");
                                    $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                                    document.getElementById("abreinsletra").style.visibility = "visible";
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
                                            $("#relacaoHorarios").load("modulos/escaladaf/edHorarios.php");
                                            $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
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

            function MarcaDia(Cod){ // vem de destacDia.php
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
            
            function marcaTurno(Cod){ // vem de destacDia.php
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=marcaTurno&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#relacaoHorarios").load("modulos/escaladaf/edHorarios.php");
//                                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                                    $("#faixaquadro").load("modulos/escaladaf/quadrodaf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));

                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function fechaQuadroHorario(){
                document.getElementById("relacQuadroHorario").style.display = "none";
            }

            function fechaQuadroNotas(){
                document.getElementById("relacQuadroNotas").style.display = "none";
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
                                    $("#faixanotas").load("modulos/escaladaf/notasdaf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
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
                                    }else{
                                        $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
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
                    ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaTurno&codpartic="+CodPartic+"&codturno="+CodTurno, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
//                                    $("#relacaoParticip").load("modulos/quadroHorario/jEquipes.php?codigo="+document.getElementById("guardanumgrupo").value);
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
                                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                                    document.getElementById("relacParticip").style.display = "none";
                                }else{
                                    $("#faixacentral").load("modulos/escaladaf/relEsc_daf.php?mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value));
                                    document.getElementById("relacParticip").style.display = "none";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }


            function abreEscalaConfig(){
                document.getElementById("checkefetivo").checked = false;
                document.getElementById("checkescalante").checked = false;
                document.getElementById("checkEncarreg").checked = false;
                document.getElementById("checkChefeADM").checked = false;
                document.getElementById("configCpfEscala").value = "";
                document.getElementById("configSelecEscala").value = "";
                document.getElementById("modalEscalaConfig").style.display = "block";
            }
            function fechaEscalaConfig(){
                document.getElementById("modalEscalaConfig").style.display = "none";
            }
            function fechaRelaPart(){
                document.getElementById("relacParticip").style.display = "none";
            }
            function resumoUsuEscala(){
                window.open("modulos/escaladaf/imprUsuEsc.php?acao=listaUsuarios", "EscalaUsu");
            }
            function imprPlanilha(){
                window.open("modulos/escaladaf/imprEscDaf.php?acao=imprPlan&mesano="+encodeURIComponent(document.getElementById("selecMesAnoEsc").value), document.getElementById("selecMesAnoEsc").value);
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
    <body>
        <?php
            require_once(dirname(dirname(__FILE__))."/config/abrealas.php");

//Provisórios

//    pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf");
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf (
        id SERIAL PRIMARY KEY, 
        dataescala date DEFAULT '3000-12-31',
        ativo smallint DEFAULT 1 NOT NULL, 
        usuins integer DEFAULT 0 NOT NULL,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit integer DEFAULT 0 NOT NULL,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");

    $rs1 = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'escaladaf_ins' AND COLUMN_NAME = 'destaque'");               
    $row1 = pg_num_rows($rs1);
    if($row1 == 0){
        pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf_ins");
    }

    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_ins (
        id SERIAL PRIMARY KEY, 
        escaladaf_id bigint NOT NULL DEFAULT 0,
        dataescalains date DEFAULT '3000-12-31',
        poslog_id INT NOT NULL DEFAULT 0,
        letraturno VARCHAR(3), 
        turnoturno VARCHAR(30), 
        destaque smallint NOT NULL DEFAULT 0,
        marcadaf smallint NOT NULL DEFAULT 0,
        ativo smallint NOT NULL DEFAULT 1, 
        usuins bigint NOT NULL DEFAULT 0, 
        datains timestamp without time zone DEFAULT '3000-12-31', 
        usuedit bigint NOT NULL DEFAULT 0, 
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        )
    ");

    $rs1 = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'escaladaf_turnos' AND COLUMN_NAME = 'destaq'");               
    $row1 = pg_num_rows($rs1);
    if($row1 == 0){
        pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf_turnos");
    }

    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_turnos (
        id SERIAL PRIMARY KEY, 
        letra VARCHAR(3), 
        horaturno VARCHAR(30), 
        ordemletra smallint NOT NULL DEFAULT 0,
        destaq smallint NOT NULL DEFAULT 0,
        ativo smallint NOT NULL DEFAULT 1, 
        usuins bigint NOT NULL DEFAULT 0,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit bigint NOT NULL DEFAULT 0,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");
    $rs2 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_turnos LIMIT 3");
    $row2 = pg_num_rows($rs2);
    if($row2 == 0){
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(1, 'F', 'FÉRIAS', 13, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(2, 'X', 'FOLGA', 14, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(3, 'Y', 'INSS', 15, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(4, 'Q', 'AULA IAQ', 16, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(5, 'A', '08:00 / 17:00', 1, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(6, 'B', '07:00 / 16:00', 2, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(7, 'C', '07:00 / 17:00', 3, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(8, 'E', '09:00 / 18:00', 5, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(9, 'H', '14:00 / 18:00', 7, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(10, 'D', '11:00 / 15:00', 4, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(11, 'K', '08:00 / 14:15', 9, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(12, 'J', '06:50 / 15:50', 8, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(13, 'G', '10:50 / 19:50', 6, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(14, 'L', '07:00 / 13:15', 10, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(15, 'M', '13:35 / 19:50', 11, 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_turnos (id, letra, horaturno, ordemletra, usuins, datains) VALUES(16, 'O', '08:00 / 18:00', 12, 3, NOW() )");
    }

//    pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escaladaf_notas");
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escaladaf_notas (
        id SERIAL PRIMARY KEY, 
        numnota  smallint NOT NULL DEFAULT 0,
        textonota text, 
        ativo smallint NOT NULL DEFAULT 1, 
        usuins bigint NOT NULL DEFAULT 0,
        datains timestamp without time zone DEFAULT '3000-12-31',
        usuedit bigint NOT NULL DEFAULT 0,
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        ) 
    ");
    $rs3 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf_notas LIMIT 2");
    $row3 = pg_num_rows($rs3);
    if($row3 == 0){
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_notas (id, numnota, textonota, usuins, datains) 
        VALUES(1, 1, 'Durante os turnos de 6 horas de duração, o funcionário deverá tirar 15 minutos de descanso, entre a terceira e quinta hora. Em consequência, o horário do turno de serviço deverá ser acrescido de 15 minutos  (Art. 71 - §1º e $2º da CLT). Nesses turnos não será necessário bater ponto quando do inicio e término do descanso. Exemplo: inicio do turno às 07h00 e saída para o descanso às 10h00. Regresso do descanso 10h15 e término do turno às 13h15.', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_notas (id, numnota, textonota, usuins, datains) 
        VALUES(2, 2, 'Durante os turnos de 8 horas de duração, o funcionário deverá tirar 1 h de descanso, entre a quarta e sexta hora. O horário de descanso de cada empregado será definido e obrigatoriamente informado à DAF pelo chefe responsável do setor, por email, até o dia 25 do mês que antecede o início da escala de serviço. É obrigatório bater o ponto quando do início e término do descanso.', 3, NOW() )");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_notas (id, numnota, textonota, usuins, datains) 
        VALUES(3, 3, 'É obrigatório bater o ponto quando do início e término da jornada de trabalho.  Horas extras somente serão realizadas quando expressamente autorizadas pelo diretor da Área ou da Presidência. A utilização do banco de horas somente será possível para os empregados que assinaram o acordo individual - AI - NI-4.18-a DAF.', 3, NOW() ) ");
        pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf_notas (id, numnota, textonota, usuins, datains) 
        VALUES(4, 4, 'As segundas, quartas e sextas feiras, o horário de funcionamento da comunhão será das 07h00 até as 21h30. Os setores funcionarão conforme as escalas de serviço.', 3, NOW() )");
    }
//Provisório
				//0046
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS mes_escdaf VARCHAR(10);"); // para guardar o mês de consulta
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS chefe_escdaf smallint NOT NULL DEFAULT 0;"); // chefe da escala DAF
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".poslog ADD COLUMN IF NOT EXISTS enc_escdaf smallint NOT NULL DEFAULT 0;"); // encarregado da escala DAF
				//0047
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escaladaf_ins ADD COLUMN IF NOT EXISTS destaque smallint NOT NULL DEFAULT 0;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escaladaf_turnos ADD COLUMN IF NOT EXISTS destaq smallint NOT NULL DEFAULT 0;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escaladaf_turnos ADD COLUMN IF NOT EXISTS ordemletra smallint NOT NULL DEFAULT 0;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escaladaf ADD COLUMN IF NOT EXISTS marcadaf smallint NOT NULL DEFAULT 0 ;");
				pg_query($Conec, "ALTER TABLE IF EXISTS ".$xProj.".escaladaf ADD COLUMN IF NOT EXISTS liberames smallint NOT NULL DEFAULT 0 ;");


//------------

 
    $Escalante = parEsc("esc_daf", $Conec, $xProj, $_SESSION["usuarioID"]);
    $MesSalvo = parEsc("mes_escdaf", $Conec, $xProj, $_SESSION["usuarioID"]);
    if(is_null($MesSalvo) || $MesSalvo == ""){
        $MesSalvo = date("m")."/".date("Y");
        pg_query($Conec, "UPDATE ".$xProj."poslog SET mes_escdaf = '$MesSalvo' WHERE pessoas_id = ". $_SESSION["usuarioID"]."" );
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
    $rsLib = pg_query($Conec, "SELECT liberames FROM ".$xProj.".escaladaf WHERE DATE_PART('MONTH', dataescala) = '$Mes' And DATE_PART('YEAR', dataescala) = '$Ano' And liberames != 0 ");
    $rowLib = pg_num_rows($rsLib);
    if($rowLib > 0){
        $MesLiberado = 1;
    }

    $DiaIni = strtotime(date('Y/m/01')); // número - para começar com o dia 1
    $DiaIni = strtotime("-1 day", $DiaIni); // para começar com o dia 1 no loop for

    $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataescala, 'MM'), '/', TO_CHAR(dataescala, 'YYYY')) 
    FROM ".$xProj.".escaladaf GROUP BY TO_CHAR(dataescala, 'MM'), TO_CHAR(dataescala, 'YYYY') ORDER BY TO_CHAR(dataescala, 'YYYY') DESC, TO_CHAR(dataescala, 'MM') DESC ");
    $OpConfig = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");


    //Mantem a tabela meses à frente
    for($i = 0; $i < 180; $i++){
        $Amanha = strtotime("+1 day", $DiaIni);
        $DiaIni = $Amanha;
        $Data = date("Y/m/d", $Amanha); // data legível
        $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".escaladaf WHERE dataescala = '$Data' ");
        $row0 = pg_num_rows($rs0);
        if($row0 == 0){
            pg_query($Conec, "INSERT INTO ".$xProj.".escaladaf (dataescala) VALUES ('$Data')");
        }
    }
            
    ?>

        <input type="hidden" id="guardamesano" value="<?php echo $MesSalvo; ?>" />
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>" />
        <input type="hidden" id="escalante" value="<?php echo $Escalante; ?>" />
        <input type="hidden" id="guardaDiaId" value="" />
        <input type="hidden" id="guardaUsuId" value="" />
        <input type="hidden" id="guardacod" value="" />
        <input type="hidden" id="mudou" value="0" />
        <input type="hidden" id="liberadoefetivo" value="<?php echo $MesLiberado; ?>" />


        <div style="margin: 5px; border: 2px solid green; border-radius: 15px; padding: 5px;">
            <div class="row"> <!-- botões Inserir e Imprimir-->
                <div class="col" style="margin: 0 auto; text-align: left;">
                    <img src="imagens/settings.png" height="20px;" id="imgEscalaConfig" style="cursor: pointer; padding-left: 30px;" onclick="abreEscalaConfig();" title="Configurar o acesso e inserir participantes da escala">
                    <label style="padding-left: 40px;">Escala mês: </label>
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

                    <?php
                    if($Escalante == 1){
                        ?>
                        <label style="padding-left: 10px;"></label>
                        <input type="checkbox" id="evliberames" title="Liberar acesso aos participantes da escala" onClick="liberaMes(this);" <?php if($MesLiberado == 1) {echo "checked";} ?> >
                        <label for="evliberames" title="Acesso aos participantes da escala">liberado</label>
                        <?php
                    }
                    ?>
                </div> <!-- quadro -->

                <div class="col" style="text-align: center;">Escala de Serviço DAF</div> <!-- espaçamento entre colunas  -->
                <div class="col" style="margin: 0 auto; text-align: center;">
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
            <div id="faixacentral"></div>
            <div id="faixaquadro"></div>
            <div id="faixanotas"></div>
        </div>


        <!-- div modal relacionar escalado -->
        <div id="relacParticip" class="relacmodal">
            <div class="modal-content-relacParticip">
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

        <!-- div modal relacionar turnos - edHorarios.php -->
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


         <!-- Modal configuração-->
         <div id="modalEscalaConfig" class="relacmodal">
            <div class="modal-content-escalaControle">
                <span class="close" onclick="fechaEscalaConfig();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"></div>
                        <div class="col quadro"><h5 style="text-align: center; color: #666;">Escala DAF</h5></div> <!-- Central - espaçamento entre colunas  -->
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
                        <td class="etiq80">Encarregado ADM:</td>
                        <td colspan="4">
                            <input type="checkbox" id="checkEncarreg" onchange="marcaConfigEscala(this, 'enc_escdaf');" >
                            <label for="checkEncarreg">Chefe Imediato</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiq80">Chefe DIV ADM:</td>
                        <td colspan="4">
                            <input type="checkbox" id="checkChefeADM" onchange="marcaConfigEscala(this, 'chefe_escdaf');" >
                            <label for="checkChefeADM">Chefe Div Adm</label>
                        </td>
                    </tr>


                </table>
            </div>
        </div> <!-- Fim Modal-->

    </body>
</html>