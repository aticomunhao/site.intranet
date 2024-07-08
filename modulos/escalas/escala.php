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
            .quadrinho {
                font-size: 90%;
                min-width: 33px;
                border: 1px solid;
                border-radius: 3px;
                cursor: pointer;
            }
            .quadroEscolha {
                position: relative; float: left; 
                min-height: 35px;
                border: 1px solid; border-radius: 5px; 
                text-align: center; 
                padding: 5px; width: 40px;
                cursor: pointer;
            }
            .modalEngr{
                display: none; /* Hidden por default */
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
            .modalEngr-content{
                background-color: transparent;
                margin: 20% auto; /* 15% do topo e centrado */
                text-align: center;
                width: 10%; /* acertar de acordo com a tela */
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
                document.getElementById("selecMesAno").value = document.getElementById("guardamesano").value;
                document.getElementById("botLimpaDados").innerHTML = "Limpar Dados "+document.getElementById("selecMesAno").value;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=carregaOpr&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                document.getElementById("selecOpr1").value = Resp.codOpr1;
                                document.getElementById("selecOpr2").value = Resp.codOpr2;
                                document.getElementById("selecOpr3").value = Resp.codOpr3;
                                document.getElementById("selecOpr4").value = Resp.codOpr4;
                                document.getElementById("selecOpr5").value = Resp.codOpr5;
                                document.getElementById("selecOpr6").value = Resp.codOpr6;
                                document.getElementById("selecOpr7").value = Resp.codOpr7;
                                document.getElementById("selecOpr8").value = Resp.codOpr8;
                                document.getElementById("selecOpr9").value = Resp.codOpr9;
                                document.getElementById("selecOpr10").value = Resp.codOpr10;

                                document.getElementById("somaOpr1").innerHTML = Resp.tempo1;
                                document.getElementById("somaOpr2").innerHTML = Resp.tempo2;
                                document.getElementById("somaOpr3").innerHTML = Resp.tempo3;
                                document.getElementById("somaOpr4").innerHTML = Resp.tempo4;
                                document.getElementById("somaOpr5").innerHTML = Resp.tempo5;
                                document.getElementById("somaOpr6").innerHTML = Resp.tempo6;
                                document.getElementById("somaOpr7").innerHTML = Resp.tempo7;
                                document.getElementById("somaOpr8").innerHTML = Resp.tempo8;
                                document.getElementById("somaOpr9").innerHTML = Resp.tempo9;
                                document.getElementById("somaOpr10").innerHTML = Resp.tempo10;

                                document.getElementById("tempoEscala1").innerHTML = Resp.tempo1;
                                document.getElementById("tempoEscala2").innerHTML = Resp.tempo2;
                                document.getElementById("tempoEscala3").innerHTML = Resp.tempo3;
                                document.getElementById("tempoEscala4").innerHTML = Resp.tempo4;
                                document.getElementById("tempoEscala5").innerHTML = Resp.tempo5;
                                document.getElementById("tempoEscala6").innerHTML = Resp.tempo6;
                                document.getElementById("tempoEscala7").innerHTML = Resp.tempo7;
                                document.getElementById("tempoEscala8").innerHTML = Resp.tempo8;
                                document.getElementById("tempoEscala9").innerHTML = Resp.tempo9;
                                document.getElementById("tempoEscala10").innerHTML = Resp.tempo10;

                                document.getElementById("corOpr01").innerHTML = Resp.trigr1;
                                document.getElementById("corOpr01").style.backgroundColor = Resp.cor1;
                                document.getElementById("selecCorOpr1").value = Resp.cor1;
                                document.getElementById("guardacor1").value = Resp.cor1;
                                document.getElementById("trigrOpr1").style.backgroundColor = Resp.cor1;
                                document.getElementById("trigrOpr1").value = Resp.trigr1;

                                document.getElementById("corOpr02").innerHTML = Resp.trigr2;
                                document.getElementById("corOpr02").style.backgroundColor = Resp.cor2;
                                document.getElementById("selecCorOpr2").value = Resp.cor2;
                                document.getElementById("guardacor2").value = Resp.cor2;
                                document.getElementById("trigrOpr2").style.backgroundColor = Resp.cor2;
                                document.getElementById("trigrOpr2").value = Resp.trigr2;

                                document.getElementById("corOpr03").innerHTML = Resp.trigr3;
                                document.getElementById("corOpr03").style.backgroundColor = Resp.cor3;
                                document.getElementById("selecCorOpr3").value = Resp.cor3;
                                document.getElementById("guardacor3").value = Resp.cor3;
                                document.getElementById("trigrOpr3").style.backgroundColor = Resp.cor3;
                                document.getElementById("trigrOpr3").value = Resp.trigr3;
  
                                document.getElementById("corOpr04").innerHTML = Resp.trigr4;
                                document.getElementById("corOpr04").style.backgroundColor = Resp.cor4;
                                document.getElementById("selecCorOpr4").value = Resp.cor4;
                                document.getElementById("guardacor4").value = Resp.cor4;
                                document.getElementById("trigrOpr4").style.backgroundColor = Resp.cor4;
                                document.getElementById("trigrOpr4").value = Resp.trigr4;

                                document.getElementById("corOpr05").innerHTML = Resp.trigr5;
                                document.getElementById("corOpr05").style.backgroundColor = Resp.cor5;
                                document.getElementById("selecCorOpr5").value = Resp.cor5;
                                document.getElementById("guardacor5").value = Resp.cor5;
                                document.getElementById("trigrOpr5").style.backgroundColor = Resp.cor5;
                                document.getElementById("trigrOpr5").value = Resp.trigr5;

                                document.getElementById("corOpr06").innerHTML = Resp.trigr6;
                                document.getElementById("corOpr06").style.backgroundColor = Resp.cor6;
                                document.getElementById("selecCorOpr6").value = Resp.cor6;
                                document.getElementById("guardacor6").value = Resp.cor6;
                                document.getElementById("trigrOpr6").style.backgroundColor = Resp.cor6;
                                document.getElementById("trigrOpr6").value = Resp.trigr6;

                                document.getElementById("corOpr07").innerHTML = Resp.trigr7;
                                document.getElementById("corOpr07").style.backgroundColor = Resp.cor7;
                                document.getElementById("selecCorOpr7").value = Resp.cor7;
                                document.getElementById("guardacor7").value = Resp.cor7;
                                document.getElementById("trigrOpr7").style.backgroundColor = Resp.cor7;
                                document.getElementById("trigrOpr7").value = Resp.trigr7;

                                document.getElementById("corOpr08").innerHTML = Resp.trigr8;
                                document.getElementById("corOpr08").style.backgroundColor = Resp.cor8;
                                document.getElementById("selecCorOpr8").value = Resp.cor8;
                                document.getElementById("guardacor8").value = Resp.cor8;
                                document.getElementById("trigrOpr8").style.backgroundColor = Resp.cor8;
                                document.getElementById("trigrOpr8").value = Resp.trigr8;

                                document.getElementById("corOpr09").innerHTML = Resp.trigr9;
                                document.getElementById("corOpr09").style.backgroundColor = Resp.cor9;
                                document.getElementById("selecCorOpr9").value = Resp.cor9;
                                document.getElementById("guardacor9").value = Resp.cor9;
                                document.getElementById("trigrOpr9").style.backgroundColor = Resp.cor9;
                                document.getElementById("trigrOpr9").value = Resp.trigr9;

                                document.getElementById("corOpr10").innerHTML = Resp.trigr10;
                                document.getElementById("corOpr10").style.backgroundColor = Resp.cor10;
                                document.getElementById("selecCorOpr10").value = Resp.cor10;
                                document.getElementById("guardacor10").value = Resp.cor10;
                                document.getElementById("trigrOpr10").style.backgroundColor = Resp.cor10;
                                document.getElementById("trigrOpr10").value = Resp.trigr10;

                            }
                        }
                    };
                    ajax.send(null);
                }
                $("#escala").load("modulos/escalas/relEsc_adm.php?mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));

                modalMostra = document.getElementById('relacMostraEscala'); //span[0]
                spanMostra = document.getElementsByClassName("close")[0];
                window.onclick = function(event){
                    if(event.target === modalMostra){
                        modalMostra.style.display = "none";
                    }
                };

                $("#selecMesAno").change(function(){
                    document.getElementById("guardaOpr").value = ""; // para não ficar de um mês para outro
                    document.getElementById("amostraCor").style.backgroundColor = "#FFFFFF"; // fundo branco
                    document.getElementById("guardatrigrama").value = "&nbsp;"; // para manter a caixa com altura
                    document.getElementById("guardacor").value = "";
                    document.getElementById("botLimpaDados").innerHTML = "Limpar Dados "+document.getElementById("selecMesAno").value;
                    if(document.getElementById("selecMesAno").value != ""){
                        document.getElementById("engrenagem").style.display = "block";
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=carregaOpr&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText);
                                        Resp = eval("(" + ajax.responseText + ")");
                                        document.getElementById("selecOpr1").value = Resp.codOpr1;
                                document.getElementById("selecOpr2").value = Resp.codOpr2;
                                document.getElementById("selecOpr3").value = Resp.codOpr3;
                                document.getElementById("selecOpr4").value = Resp.codOpr4;
                                document.getElementById("selecOpr5").value = Resp.codOpr5;
                                document.getElementById("selecOpr6").value = Resp.codOpr6;
                                document.getElementById("selecOpr7").value = Resp.codOpr7;
                                document.getElementById("selecOpr8").value = Resp.codOpr8;
                                document.getElementById("selecOpr9").value = Resp.codOpr9;
                                document.getElementById("selecOpr10").value = Resp.codOpr10;

                                document.getElementById("somaOpr1").innerHTML = Resp.tempo1;
                                document.getElementById("somaOpr2").innerHTML = Resp.tempo2;
                                document.getElementById("somaOpr3").innerHTML = Resp.tempo3;
                                document.getElementById("somaOpr4").innerHTML = Resp.tempo4;
                                document.getElementById("somaOpr5").innerHTML = Resp.tempo5;
                                document.getElementById("somaOpr6").innerHTML = Resp.tempo6;
                                document.getElementById("somaOpr7").innerHTML = Resp.tempo7;
                                document.getElementById("somaOpr8").innerHTML = Resp.tempo8;
                                document.getElementById("somaOpr9").innerHTML = Resp.tempo9;
                                document.getElementById("somaOpr10").innerHTML = Resp.tempo10;

                                document.getElementById("tempoEscala1").innerHTML = Resp.tempo1;
                                document.getElementById("tempoEscala2").innerHTML = Resp.tempo2;
                                document.getElementById("tempoEscala3").innerHTML = Resp.tempo3;
                                document.getElementById("tempoEscala4").innerHTML = Resp.tempo4;
                                document.getElementById("tempoEscala5").innerHTML = Resp.tempo5;
                                document.getElementById("tempoEscala6").innerHTML = Resp.tempo6;
                                document.getElementById("tempoEscala7").innerHTML = Resp.tempo7;
                                document.getElementById("tempoEscala8").innerHTML = Resp.tempo8;
                                document.getElementById("tempoEscala9").innerHTML = Resp.tempo9;
                                document.getElementById("tempoEscala10").innerHTML = Resp.tempo10;

                                document.getElementById("corOpr01").innerHTML = Resp.trigr1;
                                document.getElementById("corOpr01").style.backgroundColor = Resp.cor1;
                                document.getElementById("selecCorOpr1").value = Resp.cor1;
                                document.getElementById("guardacor1").value = Resp.cor1;
                                document.getElementById("trigrOpr1").style.backgroundColor = Resp.cor1;
                                document.getElementById("trigrOpr1").value = Resp.trigr1;

                                document.getElementById("corOpr02").innerHTML = Resp.trigr2;
                                document.getElementById("corOpr02").style.backgroundColor = Resp.cor2;
                                document.getElementById("selecCorOpr2").value = Resp.cor2;
                                document.getElementById("guardacor2").value = Resp.cor2;
                                document.getElementById("trigrOpr2").style.backgroundColor = Resp.cor2;
                                document.getElementById("trigrOpr2").value = Resp.trigr2;

                                document.getElementById("corOpr03").innerHTML = Resp.trigr3;
                                document.getElementById("corOpr03").style.backgroundColor = Resp.cor3;
                                document.getElementById("selecCorOpr3").value = Resp.cor3;
                                document.getElementById("guardacor3").value = Resp.cor3;
                                document.getElementById("trigrOpr3").style.backgroundColor = Resp.cor3;
                                document.getElementById("trigrOpr3").value = Resp.trigr3;
  
                                document.getElementById("corOpr04").innerHTML = Resp.trigr4;
                                document.getElementById("corOpr04").style.backgroundColor = Resp.cor4;
                                document.getElementById("selecCorOpr4").value = Resp.cor4;
                                document.getElementById("guardacor4").value = Resp.cor4;
                                document.getElementById("trigrOpr4").style.backgroundColor = Resp.cor4;
                                document.getElementById("trigrOpr4").value = Resp.trigr4;

                                document.getElementById("corOpr05").innerHTML = Resp.trigr5;
                                document.getElementById("corOpr05").style.backgroundColor = Resp.cor5;
                                document.getElementById("selecCorOpr5").value = Resp.cor5;
                                document.getElementById("guardacor5").value = Resp.cor5;
                                document.getElementById("trigrOpr5").style.backgroundColor = Resp.cor5;
                                document.getElementById("trigrOpr5").value = Resp.trigr5;

                                document.getElementById("corOpr06").innerHTML = Resp.trigr6;
                                document.getElementById("corOpr06").style.backgroundColor = Resp.cor6;
                                document.getElementById("selecCorOpr6").value = Resp.cor6;
                                document.getElementById("guardacor6").value = Resp.cor6;
                                document.getElementById("trigrOpr6").style.backgroundColor = Resp.cor6;
                                document.getElementById("trigrOpr6").value = Resp.trigr6;

                                document.getElementById("corOpr07").innerHTML = Resp.trigr7;
                                document.getElementById("corOpr07").style.backgroundColor = Resp.cor7;
                                document.getElementById("selecCorOpr7").value = Resp.cor7;
                                document.getElementById("guardacor7").value = Resp.cor7;
                                document.getElementById("trigrOpr7").style.backgroundColor = Resp.cor7;
                                document.getElementById("trigrOpr7").value = Resp.trigr7;

                                document.getElementById("corOpr08").innerHTML = Resp.trigr8;
                                document.getElementById("corOpr08").style.backgroundColor = Resp.cor8;
                                document.getElementById("selecCorOpr8").value = Resp.cor8;
                                document.getElementById("guardacor8").value = Resp.cor8;
                                document.getElementById("trigrOpr8").style.backgroundColor = Resp.cor8;
                                document.getElementById("trigrOpr8").value = Resp.trigr8;

                                document.getElementById("corOpr09").innerHTML = Resp.trigr9;
                                document.getElementById("corOpr09").style.backgroundColor = Resp.cor9;
                                document.getElementById("selecCorOpr9").value = Resp.cor9;
                                document.getElementById("guardacor9").value = Resp.cor9;
                                document.getElementById("trigrOpr9").style.backgroundColor = Resp.cor9;
                                document.getElementById("trigrOpr9").value = Resp.trigr9;

                                document.getElementById("corOpr10").innerHTML = Resp.trigr10;
                                document.getElementById("corOpr10").style.backgroundColor = Resp.cor10;
                                document.getElementById("selecCorOpr10").value = Resp.cor10;
                                document.getElementById("guardacor10").value = Resp.cor10;
                                document.getElementById("trigrOpr10").style.backgroundColor = Resp.cor10;
                                document.getElementById("trigrOpr10").value = Resp.trigr10;

                                    }
                                }
                            };
                            ajax.send(null);
                        }
                        $("#escala").load("modulos/escalas/relEsc_adm.php?mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                        document.getElementById("engrenagem").style.display = "none";
                    }
                });

                $("#amostraCor").click(function(){ // zerra participante selecionado
                    document.getElementById("guardaOpr").value = "";
                    document.getElementById("guardacor").value = "";
                    document.getElementById("guardatrigrama").value = "";
                    document.getElementById("guardaCodOpr").value = "";
                    document.getElementById("amostraCor").style.backgroundColor= "#FFFFFF";
                });

                $("#selecOpr1").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCodOpr").value = document.getElementById("selecOpr1").value;
                });
                $("#selecOpr1").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecOpr1").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecOpr2").value || Codigo == document.getElementById("selecOpr3").value || Codigo == document.getElementById("selecOpr4").value || Codigo == document.getElementById("selecOpr5").value || Codigo == document.getElementById("selecOpr6").value || Codigo == document.getElementById("selecOpr7").value || Codigo == document.getElementById("selecOpr8").value || Codigo == document.getElementById("selecOpr9").value || Codigo == document.getElementById("selecOpr10").value){
                            document.getElementById("selecOpr1").value = document.getElementById("guardaCodOpr").value; //volta ao que era
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Já está selecionado.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }
                    }else{
                        document.getElementById("trigrOpr1").value = "";
                        document.getElementById("selecCorOpr1").value = "";
                    }
                });
                $("#selecOpr2").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCodOpr").value = document.getElementById("selecOpr2").value;
                });
                $("#selecOpr2").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecOpr2").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecOpr1").value || Codigo == document.getElementById("selecOpr3").value || Codigo == document.getElementById("selecOpr4").value || Codigo == document.getElementById("selecOpr5").value || Codigo == document.getElementById("selecOpr6").value || Codigo == document.getElementById("selecOpr7").value || Codigo == document.getElementById("selecOpr8").value || Codigo == document.getElementById("selecOpr9").value || Codigo == document.getElementById("selecOpr10").value){
                            document.getElementById("selecOpr2").value = document.getElementById("guardaCodOpr").value; //volta ao que era
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Já está selecionado.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }
                    }else{
                        document.getElementById("trigrOpr2").value = "";
                        document.getElementById("selecCorOpr2").value = "";
                    }
                });
                $("#selecOpr3").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCodOpr").value = document.getElementById("selecOpr3").value;
                });
                $("#selecOpr3").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecOpr3").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecOpr1").value || Codigo == document.getElementById("selecOpr2").value || Codigo == document.getElementById("selecOpr4").value || Codigo == document.getElementById("selecOpr5").value || Codigo == document.getElementById("selecOpr6").value || Codigo == document.getElementById("selecOpr7").value || Codigo == document.getElementById("selecOpr8").value || Codigo == document.getElementById("selecOpr9").value || Codigo == document.getElementById("selecOpr10").value){
                            document.getElementById("selecOpr3").value = document.getElementById("guardaCodOpr").value; //volta ao que era
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Já está selecionado.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }
                    }else{
                        document.getElementById("trigrOpr3").value = "";
                        document.getElementById("selecCorOpr3").value = "";
                    }
                });
                $("#selecOpr4").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCodOpr").value = document.getElementById("selecOpr4").value;
                });
                $("#selecOpr4").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecOpr4").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecOpr1").value || Codigo == document.getElementById("selecOpr2").value || Codigo == document.getElementById("selecOpr3").value || Codigo == document.getElementById("selecOpr5").value || Codigo == document.getElementById("selecOpr6").value || Codigo == document.getElementById("selecOpr7").value || Codigo == document.getElementById("selecOpr8").value || Codigo == document.getElementById("selecOpr9").value || Codigo == document.getElementById("selecOpr10").value){
                            document.getElementById("selecOpr4").value = document.getElementById("guardaCodOpr").value; //volta ao que era
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Já está selecionado.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }
                    }else{
                        document.getElementById("trigrOpr4").value = "";
                        document.getElementById("selecCorOpr4").value = "";
                    }
                });
                $("#selecOpr5").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCodOpr").value = document.getElementById("selecOpr5").value;
                });
                $("#selecOpr5").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecOpr5").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecOpr1").value || Codigo == document.getElementById("selecOpr2").value || Codigo == document.getElementById("selecOpr3").value || Codigo == document.getElementById("selecOpr4").value || Codigo == document.getElementById("selecOpr6").value || Codigo == document.getElementById("selecOpr7").value || Codigo == document.getElementById("selecOpr8").value || Codigo == document.getElementById("selecOpr9").value || Codigo == document.getElementById("selecOpr10").value){
                            document.getElementById("selecOpr5").value = document.getElementById("guardaCodOpr").value; //volta ao que era
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Já está selecionado.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }
                    }else{
                        document.getElementById("trigrOpr5").value = "";
                        document.getElementById("selecCorOpr5").value = "";
                    }
                });
                $("#selecOpr6").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCodOpr").value = document.getElementById("selecOpr6").value;
                });
                $("#selecOpr6").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecOpr6").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecOpr1").value || Codigo == document.getElementById("selecOpr2").value || Codigo == document.getElementById("selecOpr3").value || Codigo == document.getElementById("selecOpr4").value || Codigo == document.getElementById("selecOpr5").value || Codigo == document.getElementById("selecOpr7").value || Codigo == document.getElementById("selecOpr8").value || Codigo == document.getElementById("selecOpr9").value || Codigo == document.getElementById("selecOpr10").value){
                            document.getElementById("selecOpr6").value = document.getElementById("guardaCodOpr").value; //volta ao que era
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Já está selecionado.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }
                    }else{
                        document.getElementById("trigrOpr6").value = "";
                        document.getElementById("selecCorOpr6").value = "";
                    }
                });
                $("#selecOpr7").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCodOpr").value = document.getElementById("selecOpr7").value;
                });
                $("#selecOpr7").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecOpr7").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecOpr1").value || Codigo == document.getElementById("selecOpr2").value || Codigo == document.getElementById("selecOpr3").value || Codigo == document.getElementById("selecOpr4").value || Codigo == document.getElementById("selecOpr5").value || Codigo == document.getElementById("selecOpr6").value || Codigo == document.getElementById("selecOpr8").value || Codigo == document.getElementById("selecOpr9").value || Codigo == document.getElementById("selecOpr10").value){
                            document.getElementById("selecOpr7").value = document.getElementById("guardaCodOpr").value; //volta ao que era
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Já está selecionado.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }
                    }else{
                        document.getElementById("trigrOpr7").value = "";
                        document.getElementById("selecCorOpr7").value = "";
                    }
                });
                $("#selecOpr8").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCodOpr").value = document.getElementById("selecOpr8").value;
                });
                $("#selecOpr8").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecOpr8").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecOpr1").value || Codigo == document.getElementById("selecOpr2").value || Codigo == document.getElementById("selecOpr3").value || Codigo == document.getElementById("selecOpr4").value || Codigo == document.getElementById("selecOpr5").value || Codigo == document.getElementById("selecOpr6").value || Codigo == document.getElementById("selecOpr7").value || Codigo == document.getElementById("selecOpr9").value || Codigo == document.getElementById("selecOpr10").value){
                            document.getElementById("selecOpr8").value = document.getElementById("guardaCodOpr").value; //volta ao que era
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Já está selecionado.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }
                    }else{
                        document.getElementById("trigrOpr8").value = "";
                        document.getElementById("selecCorOpr8").value = "";
                    }
                });
                $("#selecOpr9").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCodOpr").value = document.getElementById("selecOpr9").value;
                });
                $("#selecOpr9").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecOpr9").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecOpr1").value || Codigo == document.getElementById("selecOpr2").value || Codigo == document.getElementById("selecOpr3").value || Codigo == document.getElementById("selecOpr4").value || Codigo == document.getElementById("selecOpr5").value || Codigo == document.getElementById("selecOpr6").value || Codigo == document.getElementById("selecOpr7").value || Codigo == document.getElementById("selecOpr8").value || Codigo == document.getElementById("selecOpr10").value){
                            document.getElementById("selecOpr9").value = document.getElementById("guardaCodOpr").value; //volta ao que era
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Já está selecionado.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }
                    }else{
                        document.getElementById("trigrOpr9").value = "";
                        document.getElementById("selecCorOpr9").value = "";
                    }
                });
                $("#selecOpr9").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCodOpr").value = document.getElementById("selecOpr9").value;
                });
                $("#selecOpr10").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecOpr10").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecOpr1").value || Codigo == document.getElementById("selecOpr2").value || Codigo == document.getElementById("selecOpr3").value || Codigo == document.getElementById("selecOpr4").value || Codigo == document.getElementById("selecOpr5").value || Codigo == document.getElementById("selecOpr6").value || Codigo == document.getElementById("selecOpr7").value || Codigo == document.getElementById("selecOpr8").value || Codigo == document.getElementById("selecOpr9").value){
                            document.getElementById("selecOpr10").value = document.getElementById("guardaCodOpr").value; //volta ao que era
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Já está selecionado.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }
                    }else{
                        document.getElementById("trigrOpr10").value = "";
                        document.getElementById("selecCorOpr10").value = "";
                    }
                });

                //Cores
                $("#selecCorOpr1").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCorOpr").value = document.getElementById("selecCorOpr1").value;
                });
                $("#selecCorOpr1").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecCorOpr1").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecCorOpr2").value || Codigo == document.getElementById("selecCorOpr3").value || Codigo == document.getElementById("selecCorOpr4").value || Codigo == document.getElementById("selecCorOpr5").value || Codigo == document.getElementById("selecCorOpr6").value || Codigo == document.getElementById("selecCorOpr7").value || Codigo == document.getElementById("selecCorOpr8").value || Codigo == document.getElementById("selecCorOpr9").value || Codigo == document.getElementById("selecCorOpr10").value){
                            document.getElementById("selecCorOpr1").value = document.getElementById("guardaCorOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Esta cor já foi selecionada.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            if(document.getElementById("selecOpr1").value != ""){
                                document.getElementById("corOpr01").style.backgroundColor = Codigo;
                            }
                        }
                    }
                });
                $("#selecCorOpr2").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCorOpr").value = document.getElementById("selecCorOpr2").value;
                });
                $("#selecCorOpr2").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecCorOpr2").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecCorOpr1").value || Codigo == document.getElementById("selecCorOpr3").value || Codigo == document.getElementById("selecCorOpr4").value || Codigo == document.getElementById("selecCorOpr5").value || Codigo == document.getElementById("selecCorOpr6").value || Codigo == document.getElementById("selecCorOpr7").value || Codigo == document.getElementById("selecCorOpr8").value || Codigo == document.getElementById("selecCorOpr9").value || Codigo == document.getElementById("selecCorOpr10").value){
                            document.getElementById("selecCorOpr2").value = document.getElementById("guardaCorOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Esta cor já foi selecionada.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            if(document.getElementById("selecOpr2").value != ""){
                                document.getElementById("corOpr02").style.backgroundColor = Codigo;
                            }
                        }
                    }
                });
                $("#selecCorOpr3").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCorOpr").value = document.getElementById("selecCorOpr3").value;
                });
                $("#selecCorOpr3").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecCorOpr3").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecCorOpr1").value || Codigo == document.getElementById("selecCorOpr2").value || Codigo == document.getElementById("selecCorOpr4").value || Codigo == document.getElementById("selecCorOpr5").value || Codigo == document.getElementById("selecCorOpr6").value || Codigo == document.getElementById("selecCorOpr7").value || Codigo == document.getElementById("selecCorOpr8").value || Codigo == document.getElementById("selecCorOpr9").value || Codigo == document.getElementById("selecCorOpr10").value){
                            document.getElementById("selecCorOpr3").value = document.getElementById("guardaCorOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Esta cor já foi selecionada.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            if(document.getElementById("selecOpr3").value != ""){
                                document.getElementById("corOpr03").style.backgroundColor = Codigo;
                            }
                        }
                    }
                });
                $("#selecCorOpr4").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCorOpr").value = document.getElementById("selecCorOpr4").value;
                });
                $("#selecCorOpr4").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecCorOpr4").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecCorOpr1").value || Codigo == document.getElementById("selecCorOpr2").value || Codigo == document.getElementById("selecCorOpr3").value || Codigo == document.getElementById("selecCorOpr5").value || Codigo == document.getElementById("selecCorOpr6").value || Codigo == document.getElementById("selecCorOpr7").value || Codigo == document.getElementById("selecCorOpr8").value || Codigo == document.getElementById("selecCorOpr9").value || Codigo == document.getElementById("selecCorOpr10").value){
                            document.getElementById("selecCorOpr4").value = document.getElementById("guardaCorOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Esta cor já foi selecionada.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            if(document.getElementById("selecOpr4").value != ""){
                                document.getElementById("corOpr04").style.backgroundColor = Codigo;
                            }
                        }
                    }
                });
                $("#selecCorOpr5").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCorOpr").value = document.getElementById("selecCorOpr5").value;
                });
                $("#selecCorOpr5").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecCorOpr5").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecCorOpr1").value || Codigo == document.getElementById("selecCorOpr2").value || Codigo == document.getElementById("selecCorOpr3").value || Codigo == document.getElementById("selecCorOpr4").value || Codigo == document.getElementById("selecCorOpr6").value || Codigo == document.getElementById("selecCorOpr7").value || Codigo == document.getElementById("selecCorOpr8").value || Codigo == document.getElementById("selecCorOpr9").value || Codigo == document.getElementById("selecCorOpr10").value){
                            document.getElementById("selecCorOpr5").value = document.getElementById("guardaCorOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Esta cor já foi selecionada.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            if(document.getElementById("selecOpr5").value != ""){
                                document.getElementById("corOpr05").style.backgroundColor = Codigo;
                            }
                        }
                    }
                });
                $("#selecCorOpr6").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCorOpr").value = document.getElementById("selecCorOpr6").value;
                });
                $("#selecCorOpr6").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecCorOpr6").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecCorOpr1").value || Codigo == document.getElementById("selecCorOpr2").value || Codigo == document.getElementById("selecCorOpr3").value || Codigo == document.getElementById("selecCorOpr4").value || Codigo == document.getElementById("selecCorOpr5").value || Codigo == document.getElementById("selecCorOpr7").value || Codigo == document.getElementById("selecCorOpr8").value || Codigo == document.getElementById("selecCorOpr9").value || Codigo == document.getElementById("selecCorOpr10").value){
                            document.getElementById("selecCorOpr6").value = document.getElementById("guardaCorOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Esta cor já foi selecionada.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            if(document.getElementById("selecOpr6").value != ""){
                                document.getElementById("corOpr06").style.backgroundColor = Codigo;
                            }
                        }
                    }
                });
                $("#selecCorOpr7").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCorOpr").value = document.getElementById("selecCorOpr7").value;
                });
                $("#selecCorOpr7").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecCorOpr7").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecCorOpr1").value || Codigo == document.getElementById("selecCorOpr2").value || Codigo == document.getElementById("selecCorOpr3").value || Codigo == document.getElementById("selecCorOpr4").value || Codigo == document.getElementById("selecCorOpr5").value || Codigo == document.getElementById("selecCorOpr6").value || Codigo == document.getElementById("selecCorOpr8").value || Codigo == document.getElementById("selecCorOpr9").value || Codigo == document.getElementById("selecCorOpr10").value){
                            document.getElementById("selecCorOpr7").value = document.getElementById("guardaCorOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Esta cor já foi selecionada.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            if(document.getElementById("selecOpr7").value != ""){
                                document.getElementById("corOpr07").style.backgroundColor = Codigo;
                            }
                        }
                    }
                });
                $("#selecCorOpr8").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCorOpr").value = document.getElementById("selecCorOpr8").value;
                });
                $("#selecCorOpr8").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecCorOpr8").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecCorOpr1").value || Codigo == document.getElementById("selecCorOpr2").value || Codigo == document.getElementById("selecCorOpr3").value || Codigo == document.getElementById("selecCorOpr4").value || Codigo == document.getElementById("selecCorOpr5").value || Codigo == document.getElementById("selecCorOpr6").value || Codigo == document.getElementById("selecCorOpr7").value || Codigo == document.getElementById("selecCorOpr9").value || Codigo == document.getElementById("selecCorOpr10").value){
                            document.getElementById("selecCorOpr8").value = document.getElementById("guardaCorOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Esta cor já foi selecionada.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            if(document.getElementById("selecOpr8").value != ""){
                                document.getElementById("corOpr08").style.backgroundColor = Codigo;
                            }
                        }
                    }
                });
                $("#selecCorOpr9").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCorOpr").value = document.getElementById("selecCorOpr9").value;
                });
                $("#selecCorOpr9").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecCorOpr9").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecCorOpr1").value || Codigo == document.getElementById("selecCorOpr2").value || Codigo == document.getElementById("selecCorOpr3").value || Codigo == document.getElementById("selecCorOpr4").value || Codigo == document.getElementById("selecCorOpr5").value || Codigo == document.getElementById("selecCorOpr6").value || Codigo == document.getElementById("selecCorOpr7").value || Codigo == document.getElementById("selecCorOpr8").value || Codigo == document.getElementById("selecCorOpr10").value){
                            document.getElementById("selecCorOpr9").value = document.getElementById("guardaCorOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Esta cor já foi selecionada.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            if(document.getElementById("selecOpr9").value != ""){
                                document.getElementById("corOpr09").style.backgroundColor = Codigo;
                            }
                        }
                    }
                });
                $("#selecCorOpr10").click(function(){ // para repor se houver erro
                    document.getElementById("guardaCorOpr").value = document.getElementById("selecCorOpr10").value;
                });
                $("#selecCorOpr10").change(function(){
                    document.getElementById("mudou").value = "1";
                    let Codigo = document.getElementById("selecCorOpr10").value;
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("selecCorOpr1").value || Codigo == document.getElementById("selecCorOpr2").value || Codigo == document.getElementById("selecCorOpr3").value || Codigo == document.getElementById("selecCorOpr4").value || Codigo == document.getElementById("selecCorOpr5").value || Codigo == document.getElementById("selecCorOpr6").value || Codigo == document.getElementById("selecCorOpr7").value || Codigo == document.getElementById("selecCorOpr8").value || Codigo == document.getElementById("selecCorOpr9").value){
                            document.getElementById("selecCorOpr10").value = document.getElementById("guardaCorOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Esta cor já foi selecionada.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            if(document.getElementById("selecOpr10").value != ""){
                                document.getElementById("corOpr10").style.backgroundColor = Codigo;
                            }
                        }
                    }
                });

                //Trigramas
                $("#trigrOpr1").click(function(){ // para repor se houver erro
                    document.getElementById("guardatrigOpr").value = document.getElementById("trigrOpr1").value;
                });
                $("#trigrOpr1").change(function(){
                    document.getElementById("mudou").value = "1";
                    let text = document.getElementById("trigrOpr1").value;
                    Codigo = text.toUpperCase();
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("trigrOpr2").value || Codigo == document.getElementById("trigrOpr3").value || Codigo == document.getElementById("trigrOpr4").value || Codigo == document.getElementById("trigrOpr5").value || Codigo == document.getElementById("trigrOpr6").value || Codigo == document.getElementById("trigrOpr7").value || Codigo == document.getElementById("trigrOpr8").value || Codigo == document.getElementById("trigrOpr9").value || Codigo == document.getElementById("trigrOpr10").value){
                            document.getElementById("trigrOpr1").value = document.getElementById("guardatrigOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Este trigrama já foi inserido.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            document.getElementById("corOpr01").innerHTML = Codigo;
                        }
                    }
                });
                $("#trigrOpr2").click(function(){ // para repor se houver erro
                    document.getElementById("guardatrigOpr").value = document.getElementById("trigrOpr2").value;
                });
                $("#trigrOpr2").change(function(){
                    document.getElementById("mudou").value = "1";
                    let text = document.getElementById("trigrOpr2").value;
                    Codigo = text.toUpperCase();
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("trigrOpr1").value || Codigo == document.getElementById("trigrOpr3").value || Codigo == document.getElementById("trigrOpr4").value || Codigo == document.getElementById("trigrOpr5").value || Codigo == document.getElementById("trigrOpr6").value || Codigo == document.getElementById("trigrOpr7").value || Codigo == document.getElementById("trigrOpr8").value || Codigo == document.getElementById("trigrOpr9").value || Codigo == document.getElementById("trigrOpr10").value){
                            document.getElementById("trigrOpr2").value = document.getElementById("guardatrigOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Este trigrama já foi inserido.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            document.getElementById("corOpr02").innerHTML = Codigo;
                        }
                    }
                });
                $("#trigrOpr3").click(function(){ // para repor se houver erro
                    document.getElementById("guardatrigOpr").value = document.getElementById("trigrOpr3").value;
                });
                $("#trigrOpr3").change(function(){
                    document.getElementById("mudou").value = "1";
                    let text = document.getElementById("trigrOpr3").value;
                    Codigo = text.toUpperCase();
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("trigrOpr1").value || Codigo == document.getElementById("trigrOpr2").value || Codigo == document.getElementById("trigrOpr4").value || Codigo == document.getElementById("trigrOpr5").value || Codigo == document.getElementById("trigrOpr6").value || Codigo == document.getElementById("trigrOpr7").value || Codigo == document.getElementById("trigrOpr8").value || Codigo == document.getElementById("trigrOpr9").value || Codigo == document.getElementById("trigrOpr10").value){
                            document.getElementById("trigrOpr3").value = document.getElementById("guardatrigOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Este trigrama já foi inserido.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            document.getElementById("corOpr03").innerHTML = Codigo;
                        }
                    }
                });
                $("#trigrOpr4").click(function(){ // para repor se houver erro
                    document.getElementById("guardatrigOpr").value = document.getElementById("trigrOpr4").value;
                });
                $("#trigrOpr4").change(function(){
                    document.getElementById("mudou").value = "1";
                    let text = document.getElementById("trigrOpr4").value;
                    Codigo = text.toUpperCase();
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("trigrOpr1").value || Codigo == document.getElementById("trigrOpr2").value || Codigo == document.getElementById("trigrOpr3").value || Codigo == document.getElementById("trigrOpr5").value || Codigo == document.getElementById("trigrOpr6").value || Codigo == document.getElementById("trigrOpr7").value || Codigo == document.getElementById("trigrOpr8").value || Codigo == document.getElementById("trigrOpr9").value || Codigo == document.getElementById("trigrOpr10").value){
                            document.getElementById("trigrOpr4").value = document.getElementById("guardatrigOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Este trigrama já foi inserido.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            document.getElementById("corOpr04").innerHTML = Codigo;
                        }
                    }
                });
                $("#trigrOpr5").click(function(){ // para repor se houver erro
                    document.getElementById("guardatrigOpr").value = document.getElementById("trigrOpr5").value;
                });
                $("#trigrOpr5").change(function(){
                    document.getElementById("mudou").value = "1";
                    let text = document.getElementById("trigrOpr5").value;
                    Codigo = text.toUpperCase();
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("trigrOpr1").value || Codigo == document.getElementById("trigrOpr2").value || Codigo == document.getElementById("trigrOpr3").value || Codigo == document.getElementById("trigrOpr4").value || Codigo == document.getElementById("trigrOpr6").value || Codigo == document.getElementById("trigrOpr7").value || Codigo == document.getElementById("trigrOpr8").value || Codigo == document.getElementById("trigrOpr9").value || Codigo == document.getElementById("trigrOpr10").value){
                            document.getElementById("trigrOpr5").value = document.getElementById("guardatrigOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Este trigrama já foi inserido.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            document.getElementById("corOpr05").innerHTML = Codigo;
                        }
                    }
                });
                $("#trigrOpr6").click(function(){ // para repor se houver erro
                    document.getElementById("guardatrigOpr").value = document.getElementById("trigrOpr6").value;
                });
                $("#trigrOpr6").change(function(){
                    document.getElementById("mudou").value = "1";
                    let text = document.getElementById("trigrOpr6").value;
                    Codigo = text.toUpperCase();
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("trigrOpr1").value || Codigo == document.getElementById("trigrOpr2").value || Codigo == document.getElementById("trigrOpr3").value || Codigo == document.getElementById("trigrOpr4").value || Codigo == document.getElementById("trigrOpr5").value || Codigo == document.getElementById("trigrOpr7").value || Codigo == document.getElementById("trigrOpr8").value || Codigo == document.getElementById("trigrOpr9").value || Codigo == document.getElementById("trigrOpr10").value){
                            document.getElementById("trigrOpr6").value = document.getElementById("guardatrigOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Este trigrama já foi inserido.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            document.getElementById("corOpr06").innerHTML = Codigo;
                        }
                    }
                });
                $("#trigrOpr7").click(function(){ // para repor se houver erro
                    document.getElementById("guardatrigOpr").value = document.getElementById("trigrOpr7").value;
                });
                $("#trigrOpr7").change(function(){
                    document.getElementById("mudou").value = "1";
                    let text = document.getElementById("trigrOpr7").value;
                    Codigo = text.toUpperCase();
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("trigrOpr1").value || Codigo == document.getElementById("trigrOpr2").value || Codigo == document.getElementById("trigrOpr3").value || Codigo == document.getElementById("trigrOpr4").value || Codigo == document.getElementById("trigrOpr5").value || Codigo == document.getElementById("trigrOpr6").value || Codigo == document.getElementById("trigrOpr8").value || Codigo == document.getElementById("trigrOpr9").value || Codigo == document.getElementById("trigrOpr10").value){
                            document.getElementById("trigrOpr7").value = document.getElementById("guardatrigOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Este trigrama já foi inserido.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            document.getElementById("corOpr07").innerHTML = Codigo;
                        }
                    }
                });
                $("#trigrOpr8").click(function(){ // para repor se houver erro
                    document.getElementById("guardatrigOpr").value = document.getElementById("trigrOpr8").value;
                });
                $("#trigrOpr8").change(function(){
                    document.getElementById("mudou").value = "1";
                    let text = document.getElementById("trigrOpr8").value;
                    Codigo = text.toUpperCase();
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("trigrOpr1").value || Codigo == document.getElementById("trigrOpr2").value || Codigo == document.getElementById("trigrOpr3").value || Codigo == document.getElementById("trigrOpr4").value || Codigo == document.getElementById("trigrOpr5").value || Codigo == document.getElementById("trigrOpr6").value || Codigo == document.getElementById("trigrOpr7").value || Codigo == document.getElementById("trigrOpr9").value || Codigo == document.getElementById("trigrOpr10").value){
                            document.getElementById("trigrOpr8").value = document.getElementById("guardatrigOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Este trigrama já foi inserido.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }
                        else{
                            document.getElementById("corOpr08").innerHTML = Codigo;
                        }
                    }
                });
                $("#trigrOpr9").click(function(){ // para repor se houver erro
                    document.getElementById("guardatrigOpr").value = document.getElementById("trigrOpr9").value;
                });
                $("#trigrOpr9").change(function(){
                    document.getElementById("mudou").value = "1";
                    let text = document.getElementById("trigrOpr9").value;
                    Codigo = text.toUpperCase();
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("trigrOpr1").value || Codigo == document.getElementById("trigrOpr2").value || Codigo == document.getElementById("trigrOpr3").value || Codigo == document.getElementById("trigrOpr4").value || Codigo == document.getElementById("trigrOpr5").value || Codigo == document.getElementById("trigrOpr6").value || Codigo == document.getElementById("trigrOpr7").value || Codigo == document.getElementById("trigrOpr8").value || Codigo == document.getElementById("trigrOpr10").value){
                            document.getElementById("trigrOpr9").value = document.getElementById("guardatrigOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Este trigrama já foi inserido.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            document.getElementById("corOpr09").innerHTML = Codigo;
                        }
                    }
                });
                $("#trigrOpr10").click(function(){ // para repor se houver erro
                    document.getElementById("guardatrigOpr").value = document.getElementById("trigrOpr10").value;
                });
                $("#trigrOpr10").change(function(){
                    document.getElementById("mudou").value = "1";
                    let text = document.getElementById("trigrOpr10").value;
                    Codigo = text.toUpperCase();
                    if(Codigo != ""){
                        if(Codigo == document.getElementById("trigrOpr1").value || Codigo == document.getElementById("trigrOpr2").value || Codigo == document.getElementById("trigrOpr3").value || Codigo == document.getElementById("trigrOpr4").value || Codigo == document.getElementById("trigrOpr5").value || Codigo == document.getElementById("trigrOpr6").value || Codigo == document.getElementById("trigrOpr7").value || Codigo == document.getElementById("trigrOpr8").value || Codigo == document.getElementById("trigrOpr9").value){
                            document.getElementById("trigrOpr10").value = document.getElementById("guardatrigOpr").value;
                            $.confirm({
                                title: 'Falhou!',
                                content: 'Este trigrama já foi inserido.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }else{
                            document.getElementById("corOpr10").innerHTML = Codigo;
                        }
                    }
                });

                $("#selecCorOpr1").change(function(){
                    document.getElementById("trigrOpr1").style.backgroundColor = document.getElementById("selecCorOpr1").value;
                });
                $("#selecCorOpr2").change(function(){
                    document.getElementById("trigrOpr2").style.backgroundColor = document.getElementById("selecCorOpr2").value;
                });
                $("#selecCorOpr3").change(function(){
                    document.getElementById("trigrOpr3").style.backgroundColor = document.getElementById("selecCorOpr3").value;
                });
                $("#selecCorOpr4").change(function(){
                    document.getElementById("trigrOpr4").style.backgroundColor = document.getElementById("selecCorOpr4").value;
                });
                $("#selecCorOpr5").change(function(){
                    document.getElementById("trigrOpr5").style.backgroundColor = document.getElementById("selecCorOpr5").value;
                });
                $("#selecCorOpr6").change(function(){
                    document.getElementById("trigrOpr6").style.backgroundColor = document.getElementById("selecCorOpr6").value;
                });
                $("#selecCorOpr7").change(function(){
                    document.getElementById("trigrOpr7").style.backgroundColor = document.getElementById("selecCorOpr7").value;
                });
                $("#selecCorOpr8").change(function(){
                    document.getElementById("trigrOpr8").style.backgroundColor = document.getElementById("selecCorOpr8").value;
                });
                $("#selecCorOpr9").change(function(){
                    document.getElementById("trigrOpr9").style.backgroundColor = document.getElementById("selecCorOpr9").value;
                });
                $("#selecCorOpr10").change(function(){
                    document.getElementById("trigrOpr10").style.backgroundColor = document.getElementById("selecCorOpr10").value;
                });


            }); // fim do ready

            function pegaCor(Opr){
                document.getElementById("guardaOpr").value = "";
                document.getElementById("guardacor").value = "";
                document.getElementById("guardacor").value = document.getElementById("guardacor"+Opr).value;

                document.getElementById("amostraCor").style.backgroundColor = document.getElementById("guardacor"+Opr).value;

                document.getElementById("guardaOpr").value = document.getElementById("selecOpr"+Opr).value;
                document.getElementById("guardatrigrama").value = document.getElementById("corOpr0"+Opr).innerHTML;
            }

            function insereCor(id, Dia, Mes, Ano, Hora){
                if(document.getElementById("guardaOpr").value == ""){
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=marcaescala&codigo="+document.getElementById("guardaOpr").value
                    +"&dia="+Dia
                    +"&mes="+Mes
                    +"&ano="+Ano
                    +"&coluna="+Hora
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(document.getElementById(id).innerHTML == document.getElementById("guardatrigrama").value){
                                    document.getElementById(id).style.backgroundColor = "#FFFFFF"; // fundo branco
                                    document.getElementById(id).innerHTML = "&nbsp;"; // espaço
                                }else{
                                    document.getElementById(id).style.backgroundColor = document.getElementById("guardacor").value;
                                    document.getElementById(id).innerHTML = document.getElementById("guardatrigrama").value;
                                }
                                document.getElementById("tempoEscala"+Resp.opr).innerHTML = Resp.tempototal;
                                if(parseInt(Resp.oprlocal) > 0){ // se tinha um participante no local clicado
                                    document.getElementById("tempoEscala"+Resp.oprlocal).innerHTML = Resp.tempototallocal;
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }

            }
            function fechaModal(){
                if(document.getElementById("mudou").value == "1"){
                    $("#escala").load("modulos/escalas/relEsc_adm.php?mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                }
                document.getElementById("relacMostraEscala").style.display = "none";
            }

            function abreModal(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=calculatempos&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                
                                document.getElementById("somaOpr1").innerHTML = Resp.tempo1;
                                document.getElementById("somaOpr2").innerHTML = Resp.tempo2;
                                document.getElementById("somaOpr3").innerHTML = Resp.tempo3;
                                document.getElementById("somaOpr4").innerHTML = Resp.tempo4;
                                document.getElementById("somaOpr5").innerHTML = Resp.tempo5;
                                document.getElementById("somaOpr6").innerHTML = Resp.tempo6;
                                document.getElementById("somaOpr7").innerHTML = Resp.tempo7;
                                document.getElementById("somaOpr8").innerHTML = Resp.tempo8;
                                document.getElementById("somaOpr9").innerHTML = Resp.tempo9;
                                document.getElementById("somaOpr10").innerHTML = Resp.tempo10;
                                document.getElementById("relacMostraEscala").style.display = "block";
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaOpr(Pos){
                if(document.getElementById("selecMesAno").value != ""){
                    if(document.getElementById("selecOpr"+Pos).value == ""){
                        $.confirm({
                             title: 'Atenção!',
                            content: 'Selecione um participante <br>na primeira caixa de escolha à esquerda.',
                            draggable: true,
                            buttons: {
                                OK: function(){}
                            }
                        });
                        return false;
                    }
                    if(document.getElementById("trigrOpr"+Pos).value == "" || document.getElementById("trigrOpr"+Pos).value == "xxx" || document.getElementById("trigrOpr"+Pos).value == "XXX"){
                        document.getElementById("trigrOpr"+Pos).focus();
                        $.confirm({
                             title: 'Atenção!',
                            content: 'Informe um identificador de três letras.',
                            draggable: true,
                            buttons: {
                                OK: function(){}
                            }
                        });
                        return false;
                    }
                    if(document.getElementById("selecCorOpr"+Pos).value == "0" || document.getElementById("selecCorOpr"+Pos).value == ""){
                        document.getElementById("selecCorOpr"+Pos).focus();
                        $.confirm({
                             title: 'Atenção!',
                            content: 'Selecione uma cor <br>para representar o participante.',
                            draggable: true,
                            buttons: {
                                OK: function(){}
                            }
                        });
                        return false;
                    }
                    document.getElementById("guardacor"+Pos).value = document.getElementById("selecCorOpr"+Pos).value;
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=salvaOpr&opr="+Pos+"&codigo="+document.getElementById("selecOpr"+Pos).value
                        +"&trigr="+document.getElementById("trigrOpr"+Pos).value
                        +"&cor="+encodeURIComponent(document.getElementById("selecCorOpr"+Pos).value)
                        +"&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value)
                        , true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText); 
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.")
                                    }else if(parseInt(Resp.coderro) === 2){
                                        $.confirm({
                                            title: 'Erro!',
                                            content: 'Já está na escala neste mês.',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                    }else{
                                        $.confirm({
                                            title: 'Sucesso!',
                                            content: 'Valores salvos com sucesso.',
                                            autoClose: 'OK|7000',
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
                }else{
                    $.confirm({
                         title: 'Atenção!',
                        content: 'Selecione mês e ano.',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
            }

            function limparMes(){  //'+document.getElementById("selecMesAno").value
                let MesAno = 'Não haverá possibilidade de recuperação. <br>Confirma apagar lançamentos de '+document.getElementById('selecMesAno').value+'?';
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Não haverá possibilidade de recuperação. <br>Confirma apagar lançamentos de '+document.getElementById('selecMesAno').value+'?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=apagadatas&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                            if(parseInt(Resp.coderro) === 0){
                                                document.getElementById("relacMostraEscala").style.display = "none";
                                                $("#escala").load("modulos/escalas/relEsc_adm.php?mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));

                $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma limpar também <br>os nomes dos participantes da <br>escala de '+document.getElementById("selecMesAno").value+'?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=apagaparticip&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                            if(parseInt(Resp.coderro) === 0){
                                                document.getElementById("relacMostraEscala").style.display = "none";
                                                $("#escala").load("modulos/escalas/relEsc_adm.php?mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));

                                                document.getElementById("guardaOpr").value = ""; // para não ficar de um mês para outro
                                                document.getElementById("amostraCor").style.backgroundColor = "#FFFFFF"; // fundo branco
                                                document.getElementById("guardatrigrama").value = "&nbsp;"; // para manter a caixa com altura
                                                document.getElementById("guardacor").value = "";

                                                document.getElementById("corOpr01").innerHTML = "";
                                                document.getElementById("corOpr01").style.backgroundColor = "#FFFFFF"; // fundo branco
                                                document.getElementById("corOpr02").innerHTML = "";
                                                document.getElementById("corOpr02").style.backgroundColor = "#FFFFFF"; // fundo branco
                                                document.getElementById("corOpr03").innerHTML = "";
                                                document.getElementById("corOpr03").style.backgroundColor = "#FFFFFF"; // fundo branco
                                                document.getElementById("corOpr04").innerHTML = "";
                                                document.getElementById("corOpr04").style.backgroundColor = "#FFFFFF"; // fundo branco
                                                document.getElementById("corOpr05").innerHTML = "";
                                                document.getElementById("corOpr05").style.backgroundColor = "#FFFFFF"; // fundo branco
                                                document.getElementById("corOpr06").innerHTML = "";
                                                document.getElementById("corOpr06").style.backgroundColor = "#FFFFFF"; // fundo branco
                                                document.getElementById("corOpr07").innerHTML = "";
                                                document.getElementById("corOpr07").style.backgroundColor = "#FFFFFF"; // fundo branco
                                                document.getElementById("corOpr08").innerHTML = "";
                                                document.getElementById("corOpr08").style.backgroundColor = "#FFFFFF"; // fundo branco
                                                document.getElementById("corOpr09").innerHTML = "";
                                                document.getElementById("corOpr09").style.backgroundColor = "#FFFFFF"; // fundo branco
                                                document.getElementById("corOpr10").innerHTML = "";
                                                document.getElementById("corOpr10").style.backgroundColor = "#FFFFFF"; // fundo branco

                                                document.getElementById("selecOpr1").value = "";
                                                document.getElementById("selecOpr2").value = "";
                                                document.getElementById("selecOpr3").value = "";
                                                document.getElementById("selecOpr4").value = "";
                                                document.getElementById("selecOpr5").value = "";
                                                document.getElementById("selecOpr6").value = "";
                                                document.getElementById("selecOpr7").value = "";
                                                document.getElementById("selecOpr8").value = "";
                                                document.getElementById("selecOpr9").value = "";
                                                document.getElementById("selecOpr10").value = "";

                                                document.getElementById("trigrOpr1").value = "";
                                                document.getElementById("trigrOpr2").value = "";
                                                document.getElementById("trigrOpr3").value = "";
                                                document.getElementById("trigrOpr4").value = "";
                                                document.getElementById("trigrOpr5").value = "";
                                                document.getElementById("trigrOpr6").value = "";
                                                document.getElementById("trigrOpr7").value = "";
                                                document.getElementById("trigrOpr8").value = "";
                                                document.getElementById("trigrOpr9").value = "";
                                                document.getElementById("trigrOpr10").value = "";

                                                document.getElementById("trigrOpr1").style.backgroundColor = "#FFFFFF";
                                                document.getElementById("trigrOpr2").style.backgroundColor = "#FFFFFF";
                                                document.getElementById("trigrOpr3").style.backgroundColor = "#FFFFFF";
                                                document.getElementById("trigrOpr4").style.backgroundColor = "#FFFFFF";
                                                document.getElementById("trigrOpr5").style.backgroundColor = "#FFFFFF";
                                                document.getElementById("trigrOpr6").style.backgroundColor = "#FFFFFF";
                                                document.getElementById("trigrOpr7").style.backgroundColor = "#FFFFFF";
                                                document.getElementById("trigrOpr8").style.backgroundColor = "#FFFFFF";
                                                document.getElementById("trigrOpr9").style.backgroundColor = "#FFFFFF";
                                                document.getElementById("trigrOpr10").style.backgroundColor = "#FFFFFF";

                                                document.getElementById("selecCorOpr1").value = "";
                                                document.getElementById("selecCorOpr2").value = "";
                                                document.getElementById("selecCorOpr3").value = "";
                                                document.getElementById("selecCorOpr4").value = "";
                                                document.getElementById("selecCorOpr5").value = "";
                                                document.getElementById("selecCorOpr6").value = "";
                                                document.getElementById("selecCorOpr7").value = "";
                                                document.getElementById("selecCorOpr8").value = "";
                                                document.getElementById("selecCorOpr9").value = "";
                                                document.getElementById("selecCorOpr10").value = "";
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

            function insereDia(Dia, Mes, Ano){ // marca dia 08/12 e 14/18
                if(document.getElementById("guardaOpr").value != ""){
                    $.confirm({
                        title: 'Confirmação!',
                        content: 'Confirma marcar o dia '+Dia+' todo <br>(08/12h e 14/18h) com '+document.getElementById("guardatrigrama").value+'?',
                        autoClose: 'Não|10000',
                        draggable: true,
                        buttons: {
                            Sim: function () {
                                ajaxIni();
                                if(ajax){
                                    ajax.open("POST", "modulos/escalas/salvaEscala.php?acao=marcadia&codigo="+document.getElementById("guardaOpr").value +"&dia="+Dia+"&mes="+Mes+"&ano="+Ano, true);
                                    ajax.onreadystatechange = function(){
                                        if(ajax.readyState === 4 ){
                                            if(ajax.responseText){
//alert(ajax.responseText);
                                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                                if(parseInt(Resp.coderro) === 0){
                                                    $("#escala").load("modulos/escalas/relEsc_adm.php?mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                                                    document.getElementById("tempoEscala"+Resp.opr).innerHTML = Resp.cargaEscala;
                                                    document.getElementById("tempoEscala1").innerHTML = Resp.tempo1;
                                                    document.getElementById("tempoEscala2").innerHTML = Resp.tempo2;
                                                    document.getElementById("tempoEscala3").innerHTML = Resp.tempo3;
                                                    document.getElementById("tempoEscala4").innerHTML = Resp.tempo4;
                                                    document.getElementById("tempoEscala5").innerHTML = Resp.tempo5;
                                                    document.getElementById("tempoEscala6").innerHTML = Resp.tempo6;
                                                    document.getElementById("tempoEscala7").innerHTML = Resp.tempo7;
                                                    document.getElementById("tempoEscala8").innerHTML = Resp.tempo8;
                                                    document.getElementById("tempoEscala9").innerHTML = Resp.tempo9;
                                                    document.getElementById("tempoEscala10").innerHTML = Resp.tempo10;
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
            }

            
        </script>
    </head>
    <body>
        <?php
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

//        pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escala_adm");
        pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escala_adm (
            id SERIAL PRIMARY KEY, 
            hora0000_0030 BIGINT NOT NULL DEFAULT 0, 
            hora0030_0100 BIGINT NOT NULL DEFAULT 0, 
            hora0100_0130 BIGINT NOT NULL DEFAULT 0, 
            hora0130_0200 BIGINT NOT NULL DEFAULT 0, 
            hora0200_0230 BIGINT NOT NULL DEFAULT 0, 
            hora0230_0300 BIGINT NOT NULL DEFAULT 0, 
            hora0300_0330 BIGINT NOT NULL DEFAULT 0, 
            hora0330_0400 BIGINT NOT NULL DEFAULT 0, 
            hora0400_0430 BIGINT NOT NULL DEFAULT 0,   
            hora0430_0500 BIGINT NOT NULL DEFAULT 0,  
            hora0500_0530 BIGINT NOT NULL DEFAULT 0,  
            hora0530_0600 BIGINT NOT NULL DEFAULT 0,  
            hora0600_0630 BIGINT NOT NULL DEFAULT 0,  
            hora0630_0700 BIGINT NOT NULL DEFAULT 0, 
            hora0700_0730 BIGINT NOT NULL DEFAULT 0, 
            hora0730_0800 BIGINT NOT NULL DEFAULT 0, 
            hora0800_0830 BIGINT NOT NULL DEFAULT 0, 
            hora0830_0900 BIGINT NOT NULL DEFAULT 0, 
            hora0900_0930 BIGINT NOT NULL DEFAULT 0, 
            hora0930_1000 BIGINT NOT NULL DEFAULT 0, 
            hora1000_1030 BIGINT NOT NULL DEFAULT 0, 
            hora1030_1100 BIGINT NOT NULL DEFAULT 0, 
            hora1100_1130 BIGINT NOT NULL DEFAULT 0, 
            hora1130_1200 BIGINT NOT NULL DEFAULT 0, 
            hora1200_1230 BIGINT NOT NULL DEFAULT 0, 
            hora1230_1300 BIGINT NOT NULL DEFAULT 0, 
            hora1300_1330 BIGINT NOT NULL DEFAULT 0, 
            hora1330_1400 BIGINT NOT NULL DEFAULT 0, 
            hora1400_1430 BIGINT NOT NULL DEFAULT 0, 
            hora1430_1500 BIGINT NOT NULL DEFAULT 0, 
            hora1500_1530 BIGINT NOT NULL DEFAULT 0, 
            hora1530_1600 BIGINT NOT NULL DEFAULT 0, 
            hora1600_1630 BIGINT NOT NULL DEFAULT 0, 
            hora1630_1700 BIGINT NOT NULL DEFAULT 0, 
            hora1700_1730 BIGINT NOT NULL DEFAULT 0, 
            hora1730_1800 BIGINT NOT NULL DEFAULT 0, 
            hora1800_1830 BIGINT NOT NULL DEFAULT 0, 
            hora1830_1900 BIGINT NOT NULL DEFAULT 0, 
            hora1900_1930 BIGINT NOT NULL DEFAULT 0, 
            hora1930_2000 BIGINT NOT NULL DEFAULT 0, 
            hora2000_2030 BIGINT NOT NULL DEFAULT 0, 
            hora2030_2100 BIGINT NOT NULL DEFAULT 0, 
            hora2100_2130 BIGINT NOT NULL DEFAULT 0, 
            hora2130_2200 BIGINT NOT NULL DEFAULT 0, 
            hora2200_2230 BIGINT NOT NULL DEFAULT 0, 
            hora2230_2300 BIGINT NOT NULL DEFAULT 0, 
            hora2300_2330 BIGINT NOT NULL DEFAULT 0, 
            hora2330_2400 BIGINT NOT NULL DEFAULT 0, 
            pessoas_id BIGINT NOT NULL DEFAULT 0,
            dataescala date DEFAULT '3000-12-31',
            ativo smallint DEFAULT 1 NOT NULL, 
            usuins integer DEFAULT 0 NOT NULL,
            datains timestamp without time zone DEFAULT '3000-12-31',
            usuedit integer DEFAULT 0 NOT NULL,
            dataedit timestamp without time zone DEFAULT '3000-12-31' 
            ) 
         ");

//         pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".escala_eft");
         pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".escala_eft (
            id SERIAL PRIMARY KEY, 
            mes VARCHAR(2),
            ano VARCHAR(4),
            opr smallint NOT NULL DEFAULT 0, 
            oprcor VARCHAR(10) DEFAULT '#FFFFFF',
            poslog_id BIGINT NOT NULL DEFAULT 0,
            trigr VARCHAR(4), 
            tempomensal VARCHAR(100), 
            tempototal VARCHAR(100), 
            ativo smallint DEFAULT 1 NOT NULL, 
            usuins integer DEFAULT 0 NOT NULL,
            datains timestamp without time zone DEFAULT '3000-12-31',
            usuedit integer DEFAULT 0 NOT NULL,
            dataedit timestamp without time zone DEFAULT '3000-12-31' 
            ) 
         ");

         date_default_timezone_set('America/Sao_Paulo'); //Um dia = 86.400 seg
         require_once("../calendario/functions.php");
         $DiaIni = strtotime(date('Y/m/01')); // número - para começar com o dia 1
         $DiaIni = strtotime("-1 day", $DiaIni); // para começar com o dia 1 no loop for
         $ParamIni = date("m/Y");
//         $ParamIni = date("n/Y"); // n - repres o mês sem os zeros iniciais

         //Mantem a tabela meses à frente
         for($i = 0; $i < 180; $i++){
            $Amanha = strtotime("+1 day", $DiaIni);
            $DiaIni = $Amanha;
            $Data = date("Y/m/d", $Amanha); // data legível
            $rs0 = pg_query($Conec, "SELECT id FROM ".$xProj.".escala_adm WHERE dataescala = '$Data' ");
            $row0 = pg_num_rows($rs0);
            if($row0 == 0){
                pg_query($Conec, "INSERT INTO ".$xProj.".escala_adm (dataescala) VALUES ('$Data')");
            }
         }
         echo "<br><br>";

//         $mesAno = date("F Y", $monthTime);
         $Ingl = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
         $Port = array("Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");
//         $Trad = str_replace($Ingl, $Port, $mesAno);
//         $startDate = strtotime("last sunday", $monthTime);
         
//    $admIns = parAdm("insevento", $Conec, $xProj);   // nível para inserir evento no calendário
//    $admEdit = parAdm("editevento", $Conec, $xProj); // nível para editar evento no calendário

//        $OpcoesEscMes = pg_query($Conec, "SELECT EXTRACT(MONTH FROM ".$xProj.".escala_adm.dataescala)::text ||'/'|| EXTRACT(YEAR FROM ".$xProj.".escala_adm.dataescala)::text 
//        FROM ".$xProj.".escala_adm GROUP BY 1 ORDER BY 1 DESC ");

         $rs1 = pg_query($Conec, "SELECT guardaescala FROM ".$xProj.".paramsis WHERE idpar = 1");
         $tbl1 = pg_fetch_row($rs1);
         $EscMes = $tbl1[0];
         if(!is_null($EscMes)){
            $ParamIni = $EscMes;
         }

        $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataescala, 'MM'), '/', TO_CHAR(dataescala, 'YYYY')) 
        FROM ".$xProj.".escala_adm GROUP BY TO_CHAR(dataescala, 'MM'), TO_CHAR(dataescala, 'YYYY') ORDER BY TO_CHAR(dataescala, 'YYYY'), TO_CHAR(dataescala, 'MM') DESC ");

        $OpUsu01 = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl");
        $OpUsu02 = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl");
        $OpUsu03 = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl");
        $OpUsu04 = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl");
        $OpUsu05 = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl");
        $OpUsu06 = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl");
        $OpUsu07 = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl");
        $OpUsu08 = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl");
        $OpUsu09 = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl");
        $OpUsu10 = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl"); 
        ?>
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="guardacor" value="" />
        <input type="hidden" id="guardacor1" value="" />
        <input type="hidden" id="guardacor2" value="" />
        <input type="hidden" id="guardacor3" value="" />
        <input type="hidden" id="guardacor4" value="" />
        <input type="hidden" id="guardacor5" value="" />
        <input type="hidden" id="guardacor6" value="" />
        <input type="hidden" id="guardacor7" value="" />
        <input type="hidden" id="guardacor8" value="" />
        <input type="hidden" id="guardacor9" value="" />
        <input type="hidden" id="guardacor10" value="" />

        <input type="hidden" id="guardamesano" value="<?php echo $ParamIni; ?>" />
        <input type="hidden" id="guardaid" value="" />
        <input type="hidden" id="guardaOpr" value="" />
        <input type="hidden" id="guardatrigrama" value="&nbsp;" />
        <input type="hidden" id="guardaCodOpr" value="" />
        <input type="hidden" id="guardaCorOpr" value="" />
        <input type="hidden" id="guardatrigOpr" value="" />
        <input type="hidden" id="mudou" value = "0" />

        <label>Selecione o mês: </label>
        <select id="selecMesAno" style="font-size: 1rem; width: 90px;" title="Selecione o mês/ano.">
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
        <div style="position: relative; float: right; padding-right: 20px;">
            <button class="botpadrblue" onclick="abreModal();">Participantes</button>
        </div>

        <div style="position: fixed; top: 105px; left: 300px; background-color: white; opacity: .8; margin: 20px; border: 2px solid green; border-radius: 15px; padding-left: 10px; padding-top: 2px; padding-right: 10px; padding-bottom: 2px; min-height: 70px; text-align: center;">
            <label style="color: #036; font-style: italic; font-size: 80%; padding-left: 20px;">Clique para selecionar um participante</label>
            <div id="amostraCor" style="position: absolute; top: 7px; width: 15px; height: 15px; border: 1px solid; border-radius: 3px;" title="Clique para zerar a seleção do participante"></div>
            <table style="margin: 0 auto; width: 60%;">
            </tr>
                <tr>
                    <td><div id="corOpr01" class="box quadroEscolha" onclick="pegaCor('1');" title="Clique aqui e depois na escala.">xxx</div></td>
                    <td><div id="corOpr02" class="box quadroEscolha" onclick="pegaCor('2');" title="Clique aqui e depois na escala.">xxx</div></td>
                    <td><div id="corOpr03" class="box quadroEscolha" onclick="pegaCor('3');" title="Clique aqui e depois na escala.">xxx</div></td>
                    <td><div id="corOpr04" class="box quadroEscolha" onclick="pegaCor('4');" title="Clique aqui e depois na escala.">xxx</div></td>
                    <td><div id="corOpr05" class="box quadroEscolha" onclick="pegaCor('5');" title="Clique aqui e depois na escala.">xxx</div></td>
                    <td><div id="corOpr06" class="box quadroEscolha" onclick="pegaCor('6');" title="Clique aqui e depois na escala.">xxx</div></td>
                    <td><div id="corOpr07" class="box quadroEscolha" onclick="pegaCor('7');" title="Clique aqui e depois na escala.">xxx</div></td>
                    <td><div id="corOpr08" class="box quadroEscolha" onclick="pegaCor('8');" title="Clique aqui e depois na escala.">xxx</div></td>
                    <td><div id="corOpr09" class="box quadroEscolha" onclick="pegaCor('9');" title="Clique aqui e depois na escala.">xxx</div></td>
                    <td><div id="corOpr10" class="box quadroEscolha" onclick="pegaCor('10');" title="Clique aqui e depois na escala.">xxx</div></td>
                </tr>
                <tr>
                    <td class="etiq aCentro" id="tempoEscala1" title="Carga horária neste mês."></td>
                    <td class="etiq aCentro" id="tempoEscala2" title="Carga horária neste mês."></td>
                    <td class="etiq aCentro" id="tempoEscala3" title="Carga horária neste mês."></td>
                    <td class="etiq aCentro" id="tempoEscala4" title="Carga horária neste mês."></td>
                    <td class="etiq aCentro" id="tempoEscala5" title="Carga horária neste mês."></td>
                    <td class="etiq aCentro" id="tempoEscala6" title="Carga horária neste mês."></td>
                    <td class="etiq aCentro" id="tempoEscala7" title="Carga horária neste mês."></td>
                    <td class="etiq aCentro" id="tempoEscala8" title="Carga horária neste mês."></td>
                    <td class="etiq aCentro" id="tempoEscala9" title="Carga horária neste mês."></td>
                    <td class="etiq aCentro" id="tempoEscala10" title="Carga horária neste mês."></td>
                </tr>
            </table>
        </div>

        <div style="margin: 20px; border: 2px solid green; border-radius: 15px; padding: 10px; min-height: 70px; text-align: center;">
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
            <div id="escala"></div>
        </div>

        <!-- div modal   -->
        <div id="relacMostraEscala" class="relacmodal">
            <div class="modal-content-Escala">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h4 id="titulomodal" style="text-align: center; color: #666;">Participantes da Escala</h4>
                <div style="border: 2px solid blue; border-radius: 10px;">
                <table style="margin: 0 auto; width: 95%;">
                    <tr>
                        <td colspan="2" class="etiq aCentro" style="padding-top: 5px;">Participantes</td>
                        <td colspan="2" class="etiq aCentro" style="padding-top: 5px;">Trigrama</td>
                        <td colspan="2" class="etiq aCentro" style="padding-top: 5px;">Cor</td>
                        <td colspan="2"></td>
                        <td colspan="2" class="etiq aCentro" style="padding-top: 5px;">Carga Mensal</td>
                    </tr>

                    <tr>
                        <td class="etiq">01 </td>
                        <td>
                            <select id="selecOpr1" style="font-size: 1rem; width: 100%;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu01){
                                        while ($Opcoes = pg_fetch_row($OpUsu01)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="etiq"> Trigrama: </td>
                        <td><input type="text" id="trigrOpr1" placeholder="xxx" value="" style="border: 1px solid; border-radius: 5px; text-align: center; width: 40px; text-transform: uppercase;" /></td>
                        <td class="etiq">Cor: </td>
                        <td>
                            <select id="selecCorOpr1" style="font-size: 1rem; width: 28px;" title="Selecione uma cor.">
                                <option value="0"></option>
                                <option style="background-color: #FF0000" value="#FF0000"><div>Vermelho</div></option>
                                <option style="background-color: #0090FF" value="#0090FF"><div>Azul</div></option>
                                <option style="background-color: #00FF00" value="#00FF00"><div>Verde</div></option>
                                <option style="background-color: #FFFF00" value="#FFFF00"><div>Amarelo</div></option>
                                <option style="background-color: #ffc4d8" value="#ffc4d8"><div>Rosa</div></option>
                                <option style="background-color: #c8a2c8" value="#c8a2c8"><div>Lilás</div></option>
                                <option style="background-color: #C0C0C0" value="#C0C0C0"><div>Prata</div></option>
                                <option style="background-color: #FF00FF" value="#FF00FF"><div>Fúcsia</div></option>
                                <option style="background-color: #00FFFF" value="#00FFFF"><div>Água</div></option>
                                <option style="background-color: #808080" value="#808080"><div>Cinza</div></option>
                            </select>
                            <!--
                            <input type="color" list="presetColors">
                            <datalist id="presetColors">
                                <option>#ff0000</option>/>
                                <option>#00ff00</option>
                                <option>#0000ff</option>
                            </datalist>
                            -->

                        </td>
                        <td></td>
                        <td><button class="botpadr" onclick="salvaOpr(1);">Salvar</button></td>
                        <td class="etiq"><div id="somaOpr1" title="Carga horária neste mês."></div></td>
                    </tr>
                    <tr>
                        <td class="etiq">02 </td>
                        <td>
                            <select id="selecOpr2" style="font-size: 1rem; width: 100%;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu02){
                                        while ($Opcoes = pg_fetch_row($OpUsu02)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="etiq"> Trigrama: </td>
                        <td><input type="text" id="trigrOpr2" placeholder="xxx" value="" style="border: 1px solid; border-radius: 5px; text-align: center; width: 40px; text-transform: uppercase;" /></td>
                        <td class="etiq">Cor: </td>
                        <td>
                            <select id="selecCorOpr2" style="font-size: 1rem; width: 28px;" title="Selecione uma cor.">
                                <option value="0"></option>
                                <option style="background-color: #FF0000" value="#FF0000"><div>Vermelho</div></option>
                                <option style="background-color: #0090FF" value="#0090FF"><div>Azul</div></option>
                                <option style="background-color: #00FF00" value="#00FF00"><div>Verde</div></option>
                                <option style="background-color: #FFFF00" value="#FFFF00"><div>Amarelo</div></option>
                                <option style="background-color: #ffc4d8" value="#ffc4d8"><div>Rosa</div></option>
                                <option style="background-color: #c8a2c8" value="#c8a2c8"><div>Lilás</div></option>
                                <option style="background-color: #C0C0C0" value="#C0C0C0"><div>Prata</div></option>
                                <option style="background-color: #FF00FF" value="#FF00FF"><div>Fúcsia</div></option>
                                <option style="background-color: #00FFFF" value="#00FFFF"><div>Água</div></option>
                                <option style="background-color: #808080" value="#808080"><div>Cinza</div></option>
                            </select>
                        </td>
                        <td></td>
                        <td><button class="botpadr" onclick="salvaOpr(2);">Salvar</button></td>
                        <td class="etiq"><div id="somaOpr2" title="Carga horária neste mês."></div></td>
                    </tr>

                    <tr>
                        <td class="etiq">03 </td>
                        <td>
                            <select id="selecOpr3" style="font-size: 1rem; width: 100%;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu03){
                                        while ($Opcoes = pg_fetch_row($OpUsu03)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="etiq"> Trigrama: </td>
                        <td><input type="text" id="trigrOpr3" placeholder="xxx" value="" style="border: 1px solid; border-radius: 5px; text-align: center; width: 40px; text-transform: uppercase;" /></td>
                        <td class="etiq">Cor: </td>
                        <td>
                            <select id="selecCorOpr3" style="font-size: 1rem; width: 28px;" title="Selecione uma cor.">
                                <option value="0"></option>
                                <option style="background-color: #FF0000" value="#FF0000"><div>Vermelho</div></option>
                                <option style="background-color: #0090FF" value="#0090FF"><div>Azul</div></option>
                                <option style="background-color: #00FF00" value="#00FF00"><div>Verde</div></option>
                                <option style="background-color: #FFFF00" value="#FFFF00"><div>Amarelo</div></option>
                                <option style="background-color: #ffc4d8" value="#ffc4d8"><div>Rosa</div></option>
                                <option style="background-color: #c8a2c8" value="#c8a2c8"><div>Lilás</div></option>
                                <option style="background-color: #C0C0C0" value="#C0C0C0"><div>Prata</div></option>
                                <option style="background-color: #FF00FF" value="#FF00FF"><div>Fúcsia</div></option>
                                <option style="background-color: #00FFFF" value="#00FFFF"><div>Água</div></option>
                                <option style="background-color: #808080" value="#808080"><div>Cinza</div></option>
                            </select>
                        </td>
                        <td></td>
                        <td><button class="botpadr" onclick="salvaOpr(3);">Salvar</button></td>
                        <td class="etiq"><div id="somaOpr3" title="Carga horária neste mês."></div></td>
                    </tr>

                    <tr>
                        <td class="etiq">04 </td>
                        <td>
                            <select id="selecOpr4" style="font-size: 1rem; width: 100%;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu04){
                                        while ($Opcoes = pg_fetch_row($OpUsu04)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="etiq"> Trigrama: </td>
                        <td><input type="text" id="trigrOpr4" placeholder="xxx" value="" style="border: 1px solid; border-radius: 5px; text-align: center; width: 40px; text-transform: uppercase;" /></td>
                        <td class="etiq">Cor: </td>
                        <td>
                            <select id="selecCorOpr4" style="font-size: 1rem; width: 28px;" title="Selecione uma cor.">
                                <option value="0"></option>
                                <option style="background-color: #FF0000" value="#FF0000"><div>Vermelho</div></option>
                                <option style="background-color: #0090FF" value="#0090FF"><div>Azul</div></option>
                                <option style="background-color: #00FF00" value="#00FF00"><div>Verde</div></option>
                                <option style="background-color: #FFFF00" value="#FFFF00"><div>Amarelo</div></option>
                                <option style="background-color: #ffc4d8" value="#ffc4d8"><div>Rosa</div></option>
                                <option style="background-color: #c8a2c8" value="#c8a2c8"><div>Lilás</div></option>
                                <option style="background-color: #C0C0C0" value="#C0C0C0"><div>Prata</div></option>
                                <option style="background-color: #FF00FF" value="#FF00FF"><div>Fúcsia</div></option>
                                <option style="background-color: #00FFFF" value="#00FFFF"><div>Água</div></option>
                                <option style="background-color: #808080" value="#808080"><div>Cinza</div></option>
                            </select>
                        </td>
                        <td></td>
                        <td><button class="botpadr" onclick="salvaOpr(4);">Salvar</button></td>
                        <td class="etiq"><div id="somaOpr4" title="Carga horária neste mês."></div></td>
                    </tr>

                    <tr>
                        <td class="etiq">05 </td>
                        <td>
                            <select id="selecOpr5" style="font-size: 1rem; width: 100%;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu05){
                                        while ($Opcoes = pg_fetch_row($OpUsu05)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="etiq"> Trigrama: </td>
                        <td><input type="text" id="trigrOpr5" placeholder="xxx" value="" style="border: 1px solid; border-radius: 5px; text-align: center; width: 40px; text-transform: uppercase;" /></td>
                        <td class="etiq">Cor: </td>
                        <td>
                            <select id="selecCorOpr5" style="font-size: 1rem; width: 28px;" title="Selecione uma cor.">
                                <option value="0"></option>
                                <option style="background-color: #FF0000" value="#FF0000"><div>Vermelho</div></option>
                                <option style="background-color: #0090FF" value="#0090FF"><div>Azul</div></option>
                                <option style="background-color: #00FF00" value="#00FF00"><div>Verde</div></option>
                                <option style="background-color: #FFFF00" value="#FFFF00"><div>Amarelo</div></option>
                                <option style="background-color: #ffc4d8" value="#ffc4d8"><div>Rosa</div></option>
                                <option style="background-color: #c8a2c8" value="#c8a2c8"><div>Lilás</div></option>
                                <option style="background-color: #C0C0C0" value="#C0C0C0"><div>Prata</div></option>
                                <option style="background-color: #FF00FF" value="#FF00FF"><div>Fúcsia</div></option>
                                <option style="background-color: #00FFFF" value="#00FFFF"><div>Água</div></option>
                                <option style="background-color: #808080" value="#808080"><div>Cinza</div></option>
                            </select>
                        </td>
                        <td></td>
                        <td><button class="botpadr" onclick="salvaOpr(5);">Salvar</button></td>
                        <td class="etiq"><div id="somaOpr5" title="Carga horária neste mês."></div></td>
                    </tr>

                    <tr>
                        <td class="etiq">06 </td>
                        <td>
                            <select id="selecOpr6" style="font-size: 1rem; width: 100%;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu06){
                                        while ($Opcoes = pg_fetch_row($OpUsu06)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="etiq"> Trigrama: </td>
                        <td><input type="text" id="trigrOpr6" placeholder="xxx" value="" style="border: 1px solid; border-radius: 5px; text-align: center; width: 40px; text-transform: uppercase;" /></td>
                        <td class="etiq">Cor: </td>
                        <td>
                            <select id="selecCorOpr6" style="font-size: 1rem; width: 28px;" title="Selecione uma cor.">
                                <option value="0"></option>
                                <option style="background-color: #FF0000" value="#FF0000"><div>Vermelho</div></option>
                                <option style="background-color: #0090FF" value="#0090FF"><div>Azul</div></option>
                                <option style="background-color: #00FF00" value="#00FF00"><div>Verde</div></option>
                                <option style="background-color: #FFFF00" value="#FFFF00"><div>Amarelo</div></option>
                                <option style="background-color: #ffc4d8" value="#ffc4d8"><div>Rosa</div></option>
                                <option style="background-color: #c8a2c8" value="#c8a2c8"><div>Lilás</div></option>
                                <option style="background-color: #C0C0C0" value="#C0C0C0"><div>Prata</div></option>
                                <option style="background-color: #FF00FF" value="#FF00FF"><div>Fúcsia</div></option>
                                <option style="background-color: #00FFFF" value="#00FFFF"><div>Água</div></option>
                                <option style="background-color: #808080" value="#808080"><div>Cinza</div></option>
                            </select>
                        </td>
                        <td></td>
                        <td><button class="botpadr" onclick="salvaOpr(6);">Salvar</button></td>
                        <td class="etiq"><div id="somaOpr6" title="Carga horária neste mês."></div></td>
                    </tr>

                    <tr>
                        <td class="etiq">07 </td>
                        <td>
                            <select id="selecOpr7" style="font-size: 1rem; width: 100%;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu07){
                                        while ($Opcoes = pg_fetch_row($OpUsu07)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="etiq"> Trigrama: </td>
                        <td><input type="text" id="trigrOpr7" placeholder="xxx" value="" style="border: 1px solid; border-radius: 5px; text-align: center; width: 40px; text-transform: uppercase;" /></td>
                        <td class="etiq">Cor: </td>
                        <td>
                            <select id="selecCorOpr7" style="font-size: 1rem; width: 28px;" title="Selecione uma cor.">
                                <option value="0"></option>
                                <option style="background-color: #FF0000" value="#FF0000"><div>Vermelho</div></option>
                                <option style="background-color: #0090FF" value="#0090FF"><div>Azul</div></option>
                                <option style="background-color: #00FF00" value="#00FF00"><div>Verde</div></option>
                                <option style="background-color: #FFFF00" value="#FFFF00"><div>Amarelo</div></option>
                                <option style="background-color: #ffc4d8" value="#ffc4d8"><div>Rosa</div></option>
                                <option style="background-color: #c8a2c8" value="#c8a2c8"><div>Lilás</div></option>
                                <option style="background-color: #C0C0C0" value="#C0C0C0"><div>Prata</div></option>
                                <option style="background-color: #FF00FF" value="#FF00FF"><div>Fúcsia</div></option>
                                <option style="background-color: #00FFFF" value="#00FFFF"><div>Água</div></option>
                                <option style="background-color: #808080" value="#808080"><div>Cinza</div></option>
                            </select>
                        </td>
                        <td></td>
                        <td><button class="botpadr" onclick="salvaOpr(7);">Salvar</button></td>
                        <td class="etiq"><div id="somaOpr7" title="Carga horária neste mês."></div></td>
                    </tr>

                    <tr>
                        <td class="etiq">08 </td>
                        <td>
                            <select id="selecOpr8" style="font-size: 1rem; width: 100%;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu08){
                                        while ($Opcoes = pg_fetch_row($OpUsu08)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="etiq"> Trigrama: </td>
                        <td><input type="text" id="trigrOpr8" placeholder="xxx" value="" style="border: 1px solid; border-radius: 5px; text-align: center; width: 40px; text-transform: uppercase;" /></td>
                        <td class="etiq">Cor: </td>
                        <td>
                            <select id="selecCorOpr8" style="font-size: 1rem; width: 28px;" title="Selecione uma cor.">
                                <option value="0"></option>
                                <option style="background-color: #FF0000" value="#FF0000"><div>Vermelho</div></option>
                                <option style="background-color: #0090FF" value="#0090FF"><div>Azul</div></option>
                                <option style="background-color: #00FF00" value="#00FF00"><div>Verde</div></option>
                                <option style="background-color: #FFFF00" value="#FFFF00"><div>Amarelo</div></option>
                                <option style="background-color: #ffc4d8" value="#ffc4d8"><div>Rosa</div></option>
                                <option style="background-color: #c8a2c8" value="#c8a2c8"><div>Lilás</div></option>
                                <option style="background-color: #C0C0C0" value="#C0C0C0"><div>Prata</div></option>
                                <option style="background-color: #FF00FF" value="#FF00FF"><div>Fúcsia</div></option>
                                <option style="background-color: #00FFFF" value="#00FFFF"><div>Água</div></option>
                                <option style="background-color: #808080" value="#808080"><div>Cinza</div></option>
                            </select>
                        </td>
                        <td></td>
                        <td><button class="botpadr" onclick="salvaOpr(8);">Salvar</button></td>
                        <td class="etiq" title="Carga horária neste mês."><div id="somaOpr8"></div></td>
                    </tr>

                    <tr>
                        <td class="etiq">09 </td>
                        <td>
                            <select id="selecOpr9" style="font-size: 1rem; width: 100%;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu09){
                                        while ($Opcoes = pg_fetch_row($OpUsu09)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="etiq"> Trigrama: </td>
                        <td><input type="text" id="trigrOpr9" placeholder="xxx" value="" style="border: 1px solid; border-radius: 5px; text-align: center; width: 40px; text-transform: uppercase;" /></td>
                        <td class="etiq">Cor: </td>
                        <td>
                            <select id="selecCorOpr9" style="font-size: 1rem; width: 28px;" title="Selecione uma cor.">
                                <option value="0"></option>
                                <option style="background-color: #FF0000" value="#FF0000"><div>Vermelho</div></option>
                                <option style="background-color: #0090FF" value="#0090FF"><div>Azul</div></option>
                                <option style="background-color: #00FF00" value="#00FF00"><div>Verde</div></option>
                                <option style="background-color: #FFFF00" value="#FFFF00"><div>Amarelo</div></option>
                                <option style="background-color: #ffc4d8" value="#ffc4d8"><div>Rosa</div></option>
                                <option style="background-color: #c8a2c8" value="#c8a2c8"><div>Lilás</div></option>
                                <option style="background-color: #C0C0C0" value="#C0C0C0"><div>Prata</div></option>
                                <option style="background-color: #FF00FF" value="#FF00FF"><div>Fúcsia</div></option>
                                <option style="background-color: #00FFFF" value="#00FFFF"><div>Água</div></option>
                                <option style="background-color: #808080" value="#808080"><div>Cinza</div></option>
                            </select>
                        </td>
                        <td></td>
                        <td><button class="botpadr" onclick="salvaOpr(9);">Salvar</button></td>
                        <td class="etiq" title="Carga horária neste mês."><div id="somaOpr9"></div></td>
                    </tr>

                    <tr>
                        <td class="etiq">10 </td>
                        <td>
                            <select id="selecOpr10" style="font-size: 1rem; width: 100%;" title="Selecione um nome.">
                            <option value=""></option>
                                <?php 
                                    if($OpUsu10){
                                        while ($Opcoes = pg_fetch_row($OpUsu10)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="etiq"> Trigrama: </td>
                        <td><input type="text" id="trigrOpr10" placeholder="xxx" value="" style="border: 1px solid; border-radius: 5px; text-align: center; width: 40px; text-transform: uppercase;" /></td>
                        <td class="etiq">Cor: </td>
                        <td>
                            <select id="selecCorOpr10" style="font-size: 1rem; width: 28px;" title="Selecione uma cor.">
                                <option value="0"></option>
                                <option style="background-color: #FF0000" value="#FF0000"><div>Vermelho</div></option>
                                <option style="background-color: #0090FF" value="#0090FF"><div>Azul</div></option>
                                <option style="background-color: #00FF00" value="#00FF00"><div>Verde</div></option>
                                <option style="background-color: #FFFF00" value="#FFFF00"><div>Amarelo</div></option>
                                <option style="background-color: #ffc4d8" value="#ffc4d8"><div>Rosa</div></option>
                                <option style="background-color: #c8a2c8" value="#c8a2c8"><div>Lilás</div></option>
                                <option style="background-color: #C0C0C0" value="#C0C0C0"><div>Prata</div></option>
                                <option style="background-color: #FF00FF" value="#FF00FF"><div>Fúcsia</div></option>
                                <option style="background-color: #00FFFF" value="#00FFFF"><div>Água</div></option>
                                <option style="background-color: #808080" value="#808080"><div>Cinza</div></option>
                            </select>
                        </td>
                        <td></td>
                        <td><button class="botpadr" onclick="salvaOpr(10);">Salvar</button></td>
                        <td class="etiq" title="Carga horária neste mês."><div id="somaOpr10"></div></td>
                    </tr>
                    <tr>
                        <td colspan="10" style="padding-top: 5px;"></td>
                    </tr>
                    <tr>
                        <td colspan="10"><button class="botpadr" id="botLimpaDados" onclick="limparMes();">Limpar Dados</button></td>
                    </tr>

                </table>
                </div>
           </div>
        </div> <!-- Fim Modal-->

        <div id="engrenagem" class="modalEngr" style="display: none;">
         <div class="modalEngr-content">
            <img src="imagens/Engrenagens.gif" width="70" height="50" draggable="false" style="padding-top: 2px; padding-left: 2px;"/>
         </div>
      </div>

    </body>
</html>