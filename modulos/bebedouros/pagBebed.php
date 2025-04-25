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
        <title>Bebedouros</title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="class/gijgo/css/gijgo.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="comp/js/jquery.mask.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script> 
        <script src="class/gijgo/js/gijgo.min.js"></script> <!-- versão 1.9.14 -->
        <script src="class/gijgo/js/messages/messages.pt-br.min.js"></script>
        <style>
            .modal-content-Config{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 65%;
                max-width: 900px;
            }
            .modal-content-InsMarca{
                background: linear-gradient(180deg, white, #0099FF);
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 35%;
                max-width: 900px;
            }
            .modal-content-InsTipo{
                background: linear-gradient(180deg, white, #0099FF);
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 35%;
                max-width: 900px;
            }
            .modal-content-relacEquip{
                background: transparent;
                margin: 10% auto; 
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 70%; 
                max-width: 900px;
            }
            .modal-content-InsEmpresa{
                background: linear-gradient(180deg, white, #0099FF);
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 55%;
                max-width: 900px;
            }
            .modal-content-relacAbastec{
                background: transparent;
                margin: 10% auto; 
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 980px; 
 /*               max-width: 900px; */
            }
            .modal-content-relacEditaAbastec{
                background: transparent;
                margin: 10% auto; 
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 50%;
                max-width: 900px;
            }
            .modal-content-escImprBebed{
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
                document.getElementById("faixaMensagem").innerHTML = "Bom Dia!<br>Usuário não cadastrado. <br>O acesso é proporcionado pela DAF/ATI.";
                if(hora >= 12){
                    document.getElementById("faixaMensagem").innerHTML = "Boa Tarde!<br>Usuário não cadastrado. <br>O acesso é proporcionado pela DAF/ATI.";
                }
                if(hora >= 18){
                    document.getElementById("faixaMensagem").innerHTML = "Boa Noite!<br>Usuário não cadastrado. <br>O acesso é proporcionado pela DAF/ATI.";
                }
                document.getElementById("imgConfig").style.visibility = "hidden";
                document.getElementById("botinserir").style.visibility = "hidden";
                document.getElementById("botimpr").style.visibility = "hidden";
                document.getElementById("botApagaLanc").style.visibility = "hidden";
                document.getElementById("botimprUsu").style.visibility = "hidden";

                if(parseInt(document.getElementById("guardaEdit").value) === 1 || parseInt(document.getElementById("guardaFiscal").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){
                    $("#container5").load("modulos/bebedouros/jBebed.php?acao="+document.getElementById("guardaAcao").value);
                    $("#container6").load("modulos/bebedouros/kBebed.php?acao="+document.getElementById("guardaAcao").value);
                    document.getElementById("botimpr").style.visibility = "visible";

                    if(parseInt(document.getElementById("guardaEdit").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){ 
                        document.getElementById("botinserir").style.visibility = "visible";
                        document.getElementById("imgConfig").style.visibility = "visible";
                    }
                }else{
                    document.getElementById("faixaMensagem").style.display = "block";
                }

                if(parseInt(document.getElementById("UsuAdm").value) > 6){
                    $("#configAdmin").load("modulos/bebedouros/admBebed.php");
                    document.getElementById("botimprUsu").style.visibility = "visible";
                }

                $("#imprRelBebed").click(function(){
                    document.getElementById("selecAno").value = "";
                    document.getElementById("selecMesAno").value = "";
                    window.open("modulos/bebedouros/imprBebed.php?acao=listaBebed", "listaBebed");
                });

                $("#selecMesAno").change(function(){
                    document.getElementById("selecAno").value = "";
                    if(document.getElementById("selecMesAno").value != ""){
                        window.open("modulos/bebedouros/imprCons.php?acao=listaMes&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), "Mes"+document.getElementById("selecMesAno").value);
                        document.getElementById("selecMesAno").value = "";
                        document.getElementById("relacImprBebed").style.display = "none";
                    }
                });
                $("#selecAno").change(function(){
                    document.getElementById("selecMesAno").value = "";
                    if(document.getElementById("selecAno").value != ""){
                        window.open("modulos/bebedouros/imprCons.php?acao=listaAno&ano="+encodeURIComponent(document.getElementById("selecAno").value), "Ano"+document.getElementById("selecAno").value);
                        document.getElementById("selecAno").value = "";
                        document.getElementById("relacImprBebed").style.display = "none";
                    }
                });
                $("#imprBebedAbast").click(function(){
                    window.open("modulos/bebedouros/imprCons.php?acao=listaIndiv&bebedouro="+document.getElementById("guardaCodMarca").value, "Bebed"+document.getElementById("guardaCodMarca").value);
                });

                $('#dataManut').datepicker({ uiLibrary: 'bootstrap5', locale: 'pt-br', format: 'dd/mm/yyyy' });
                $('#dataAbastec').datepicker({ uiLibrary: 'bootstrap5', locale: 'pt-br', format: 'dd/mm/yyyy' });
                $("#dataManut").mask("99/99/9999");
                $("#dataVencim").mask("99/99/9999");
                $("#dataAbastec").mask("99/99/9999");

                $('#carregaTema').load('modulos/config/carTema.php?carpag=clavic1');

            }); // fim do ready

            function carregaConfig(){
                $("#configMarcas").load("modulos/bebedouros/jMarcas.php");
                $("#configTipos").load("modulos/bebedouros/jTipos.php");
                $("#configEmpr").load("modulos/bebedouros/jEmpr.php");
                if(parseInt(document.getElementById("UsuAdm").value) > 6){
                    $("#configAdmin").load("modulos/bebedouros/admBebed.php");
                }
                document.getElementById("relacmodalConfig").style.display = "block";
            }
            function fechaConfig(){
                document.getElementById("relacmodalConfig").style.display = "none";
            }

            function insereEquip(){
                document.getElementById("botApagaLanc").style.visibility = "hidden";
                document.getElementById("botinsAbastecer").style.visibility = "hidden";
                document.getElementById("guardaCodMarca").value = 0;
                document.getElementById("localinstal").value = "";
                document.getElementById("selecMarca").value = 1;
                document.getElementById("selecTipo").value = 1;
                document.getElementById("modeloBebedouro").value = "";
                document.getElementById("selecPrazo").value = "3";
                document.getElementById("diasAnteced").value = "30";
                document.getElementById("notifica1").checked = true;
                document.getElementById("pararaviso").checked = false;
                document.getElementById("dataManut").value = document.getElementById("guardaHoje").value;
                document.getElementById("editaModalEquipam").style.display = "block";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=calcprazo&datatroca="+encodeURIComponent(document.getElementById("dataManut").value)
                    +"&prazoselec="+document.getElementById("selecPrazo").value
                    +"&diasanteced="+document.getElementById("diasAnteced").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("dataVencim").value = Resp.datafinal;
                                    document.getElementById("dataAviso").value = Resp.dataaviso;
                                    document.getElementById("numequip").value = Resp.proxnumero;
                                    document.getElementById("numEquipam").innerHTML = Resp.proxnumero;
                                    document.getElementById("numEquipamAbast").innerHTML = Resp.proxnumero;
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function editaEquip(Cod){
                document.getElementById("guardaCodMarca").value = Cod;
                document.getElementById("botinsAbastecer").style.visibility = "visible";
                if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário 
                    document.getElementById("botApagaLanc").style.visibility = "visible";
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=buscadadosEquip&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("numequip").value = Resp.numapar;
                                    document.getElementById("numEquipam").innerHTML = Resp.numapar;
                                    document.getElementById("numEquipamAbast").innerHTML = Resp.numapar;
                                    document.getElementById("botInsEditaAbastec").innerHTML = "Abastecer Bebedouro "+Resp.numapar;
                                    document.getElementById("guardaNum").value = Resp.numapar;
                                    document.getElementById("localinstal").value = Resp.localinst;
                                    document.getElementById("selecMarca").value = Resp.codmarca;
                                    document.getElementById("selecTipo").value = Resp.codtipo;
                                    document.getElementById("dataManut").value = Resp.datatroca;
                                    document.getElementById("modeloBebedouro").value = Resp.modelo;
                                    document.getElementById("dataVencim").value = Resp.datavenc;
                                    document.getElementById("dataAviso").value = Resp.dataaviso;
                                    document.getElementById("selecPrazo").value = Resp.prazotroca;
                                    document.getElementById("diasAnteced").value = Resp.diasanteced;
                                    if(parseInt(Resp.pararAviso) === 1){
                                        document.getElementById("pararaviso").checked = true;
                                    }else{
                                        document.getElementById("pararaviso").checked = false;
                                    }
                                    if(parseInt(Resp.notific) === 1){
                                        document.getElementById("notifica1").checked = true;
                                        document.getElementById("diasAnteced").disabled = false;
                                        document.getElementById("dataAviso").disabled = false;
                                        document.getElementById("pararaviso").style.visibility = "visible";
                                        document.getElementById("etiqpararaviso").style.visibility = "visible";
                                    }else{
                                        document.getElementById("notifica2").checked = true
                                        document.getElementById("diasAnteced").value = "";
                                        document.getElementById("dataAviso").value = "";
                                        document.getElementById("dataAviso").disabled = true;
                                        document.getElementById("pararaviso").style.visibility = "hidden";
                                        document.getElementById("etiqpararaviso").style.visibility = "hidden";
                                    }
                                    document.getElementById("editaModalEquipam").style.display = "block";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaEditaEquip(){
                if(document.getElementById("numequip").value == ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira o número dado ao equipamento";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("localinstal").value == ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira o local de instalação do equipamento";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("selecMarca").value == ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Selecione a marca do equipamento";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("selecTipo").value == ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Selecione o tipo de bebedouro";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("dataManut").value == ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira a data da ÚLTIMA LIMPEZA da base do bebedouro";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("selecPrazo").value == ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira um prazo para e efetuar NOVA limpeza do bebedouro";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("dataVencim").value == ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira a DATA para limpeza da base do bebedouro";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("notifica1").checked == true){
                    if(document.getElementById("diasAnteced").value == ""){
                        $('#mensagem').fadeIn("slow");
                        document.getElementById("mensagem").innerHTML = "Insira quantos dias de antecedência quer para a notificação";
                        $('#mensagem').fadeOut(2000);
                        return false;
                    }
                    if(document.getElementById("dataAviso").value == ""){
                        $('#mensagem').fadeIn("slow");
                        document.getElementById("mensagem").innerHTML = "Insira a data para emitir a notificação pra limpeza do bebedouro";
                        $('#mensagem').fadeOut(2000);
                        return false;
                    }
                }
                Notif = 1;
                if(document.getElementById("notifica1").checked == false){
                    Notif = 0;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=salvaEditBebed&codigo="+document.getElementById("guardaCodMarca").value
                    +"&numequip="+document.getElementById("numequip").value
                    +"&localinst="+encodeURIComponent(document.getElementById("localinstal").value)
                    +"&codmarca="+document.getElementById("selecMarca").value
                    +"&codtipo="+document.getElementById("selecTipo").value
                    +"&datatroca="+encodeURIComponent(document.getElementById("dataManut").value)
                    +"&modelo="+encodeURIComponent(document.getElementById("modeloBebedouro").value)
                    +"&datavenc="+encodeURIComponent(document.getElementById("dataVencim").value)
                    +"&dataaviso="+encodeURIComponent(document.getElementById("dataAviso").value)
                    +"&observ="+encodeURIComponent(document.getElementById("observBebed").value)
                    +"&notif="+Notif
                    +"&prazotroca="+document.getElementById("selecPrazo").value
                    +"&diasanteced="+document.getElementById("diasAnteced").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    $("#container5").load("modulos/bebedouros/jBebed.php?acao=todos");
                                    $("#container6").load("modulos/bebedouros/kBebed.php?acao=todos");
                                    document.getElementById("editaModalEquipam").style.display = "none";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function apagarEquip(){
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma apagar este equipamento?<br>Os lançamentos serão perdidos.<br>Continua?',
                    autoClose: 'Não|20000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=apagaEquip&codigo="+document.getElementById("guardaCodMarca").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $("#container5").load("modulos/bebedouros/jBebed.php?acao=todos");
                                                $("#container6").load("modulos/bebedouros/kBebed.php?acao=todos");
                                                document.getElementById("editaModalEquipam").style.display = "none";
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

            function insMarca(){
                document.getElementById("guardaCodMarca").value = "0";
                document.getElementById("editNomeMarca").value = "";
                document.getElementById("botApagaEditMarca").style.visibility = "hidden";
                document.getElementById("titulomodalMarca").innerHTML = "Nova marca de Bebedouro";
                document.getElementById("relacEditMarca").style.display = "block";
                document.getElementById("editNomeMarca").focus();
            }

            function editaMarca(Cod){
                document.getElementById("guardaCodMarca").value = Cod;
                if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                    document.getElementById("botApagaEditMarca").style.visibility = "visible";
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=buscaMarca&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editNomeMarca").value = Resp.nome;
                                    document.getElementById("titulomodalMarca").innerHTML = "Edita marca de bebedouro";
                                    document.getElementById("relacEditMarca").style.display = "block";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function salvaEditMarca(){ 
                if(document.getElementById("editNomeMarca").value != ""){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=salvanovaMarca&codigo="+document.getElementById("guardaCodMarca").value 
                        +"&nomemarca="+encodeURIComponent(document.getElementById("editNomeMarca").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("relacEditMarca").style.display = "none";
                                        $("#configMarcas").load("modulos/bebedouros/jMarcas.php");
                                        $("#container5").load("modulos/bebedouros/jBebed.php?acao=todos");
                                        $("#container6").load("modulos/bebedouros/kBebed.php?acao=todos");
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("relacEditMarca").style.display = "none";
                }
            }
            function apagaMarca(){
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma apagar esta Marca?<br>Os lançamentos serão perdidos.<br>Continua?',
                    autoClose: 'Não|20000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=apagaMarca&codigo="+document.getElementById("guardaCodMarca").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $("#configMarcas").load("modulos/bebedouros/jMarcas.php");
                                                $("#container5").load("modulos/bebedouros/jBebed.php?acao=todos");
                                                $("#container6").load("modulos/bebedouros/kBebed.php?acao=todos");
                                                document.getElementById("relacEditMarca").style.display = "none";
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

            function insTipo(){
                document.getElementById("guardaCodMarca").value = "0";
                document.getElementById("editNomeTipo").value = "";
                document.getElementById("botApagaEditTipo").style.visibility = "hidden";
                document.getElementById("titulomodalTipo").innerHTML = "Novo tipo de Bebedouro";
                document.getElementById("relacEditTipo").style.display = "block";
                document.getElementById("editNomeTipo").focus();
            }
            function editaTipo(Cod){
                document.getElementById("guardaCodMarca").value = Cod;
                if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                    document.getElementById("botApagaEditTipo").style.visibility = "visible";
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=buscaTipo&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editNomeTipo").value = Resp.nome;
                                    document.getElementById("titulomodalTipo").innerHTML = "Edita tipo de bebedouro";
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
            function salvaEditTipo(){
                if(document.getElementById("editNomeTipo").value != ""){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=salvanovoTipo&codigo="+document.getElementById("guardaCodMarca").value 
                        +"&nometipo="+encodeURIComponent(document.getElementById("editNomeTipo").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("relacEditTipo").style.display = "none";
                                        $("#configTipos").load("modulos/bebedouros/jTipos.php");
                                        $("#container5").load("modulos/bebedouros/jBebed.php?acao=todos");
                                        $("#container6").load("modulos/bebedouros/kBebed.php?acao=todos");
                                        
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("relacEditTipo").style.display = "none";
                }
            }
            function apagaTipo(){
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma apagar este tipo?<br>Os lançamentos serão perdidos.<br>Continua?',
                    autoClose: 'Não|20000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=apagaTipo&codigo="+document.getElementById("guardaCodMarca").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $("#configTipos").load("modulos/bebedouros/jTipos.php");
                                                $("#container5").load("modulos/bebedouros/jBebed.php?acao=todos");
                                                $("#container6").load("modulos/bebedouros/kBebed.php?acao=todos");
                                                document.getElementById("relacEditTipo").style.display = "none";
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

            function insEmpr(){
                document.getElementById("guardaCodMarca").value = "0";
                document.getElementById("editNomeEmpr").value = "";
                document.getElementById("botApagaEditEmpr").style.visibility = "hidden";
                document.getElementById("titulomodalEmpr").innerHTML = "Nome da nova empresa";
                document.getElementById("editEnder").value = "";
                document.getElementById("editCEP").value = "";
                document.getElementById("editCidade").value = "";
                document.getElementById("editUF").value = "";
                document.getElementById("editCNPJ").value = "";
                document.getElementById("editInscr").value = "";
                document.getElementById("editTelef").value = "";
                document.getElementById("editContato").value = "";
                document.getElementById("editObs").value = "";
                document.getElementById("relacEditEmpresa").style.display = "block";
                document.getElementById("editNomeEmpr").focus();
            }
            function editaEmpr(Cod){
                document.getElementById("guardaCodMarca").value = Cod;
                if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                    document.getElementById("botApagaEditEmpr").style.visibility = "visible";
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=buscaEmpresa&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editNomeEmpr").value = Resp.nome;
                                    document.getElementById("editEnder").value = Resp.ender;
                                    document.getElementById("editCEP").value = Resp.cep;
                                    document.getElementById("editCidade").value = Resp.cidade;
                                    document.getElementById("editUF").value = Resp.uf;
                                    document.getElementById("editCNPJ").value = format_CnpjCpf(Resp.cnpjempr);
                                    document.getElementById("editInscr").value = Resp.inscrempr;
                                    document.getElementById("editTelef").value = Resp.telefone;
                                    document.getElementById("editContato").value = Resp.contato;
                                    document.getElementById("editObs").value = Resp.obsempr;
                                    document.getElementById("titulomodalEmpr").innerHTML = "Edita Empresa de Manutenção";
                                    document.getElementById("relacEditEmpresa").style.display = "block";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaEditEmpr(){
                if(document.getElementById("editNomeEmpr").value == ""){
                    $.confirm({
                        title: 'Informação!',
                        content: 'O nome da empresa é obrigatório.',
                        autoClose: 'OK|5000',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                if(document.getElementById("mudou").value != "0"){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=salvaEmpresa&codigo="+document.getElementById("guardaCodMarca").value 
                        +"&nomeempresa="+encodeURIComponent(document.getElementById("editNomeEmpr").value)
                        +"&editEnder="+encodeURIComponent(document.getElementById("editEnder").value)
                        +"&editCEP="+encodeURIComponent(document.getElementById("editCEP").value)
                        +"&editCidade="+encodeURIComponent(document.getElementById("editCidade").value)
                        +"&editUF="+encodeURIComponent(document.getElementById("editUF").value)
                        +"&editCNPJ="+encodeURIComponent(document.getElementById("editCNPJ").value)
                        +"&editInscr="+encodeURIComponent(document.getElementById("editInscr").value)
                        +"&editTelef="+encodeURIComponent(document.getElementById("editTelef").value)
                        +"&editContato="+encodeURIComponent(document.getElementById("editContato").value)
                        +"&editObs="+encodeURIComponent(document.getElementById("editObs").value)
                        , true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("relacEditEmpresa").style.display = "none";
                                        $("#configEmpr").load("modulos/bebedouros/jEmpr.php");
                                        $("#container5").load("modulos/bebedouros/jBebed.php?acao=todos");
                                        $("#container6").load("modulos/bebedouros/kBebed.php?acao=todos");
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("relacEditEmpresa").style.display = "none";
                }
            }
            function apagaEmpr(){
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma apagar esta Empresa?<br>Os lançamentos serão perdidos.<br>Continua?',
                    autoClose: 'Não|20000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=apagaEmpre&codigo="+document.getElementById("guardaCodMarca").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $("#configEmpr").load("modulos/bebedouros/jEmpr.php");
                                                $("#container5").load("modulos/bebedouross/jBebed.php?acao=todos");
                                                $("#container6").load("modulos/bebedouross/kBebed.php?acao=todos");
                                                document.getElementById("relacEditEmpresa").style.display = "none";
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

            function InsEditaAbastec(){
                document.getElementById("botApagaAbast").style.visibility = "hidden";
                document.getElementById("botSalvaInsAbastec").style.visibility = "visible"; // inserir volume
                document.getElementById("botSalvaEditaAbastec").style.visibility = "hidden";
                document.getElementById("titEditInsEquipamAbast").innerHTML = "Abastecer Bebedouro";
                document.getElementById("guardaIdCtl").value = 0;
                document.getElementById("dataAbastec").value = document.getElementById("guardaHoje").value; //"";
                document.getElementById("volumeAbast").value = "";
                document.getElementById("editaAbastec").style.display = "block";
            }
            function carEditaAbastec(Cod, idCtl, Data, Vol){
                document.getElementById("botApagaAbast").style.visibility = "visible";
                document.getElementById("botSalvaInsAbastec").style.visibility = "hidden";
                document.getElementById("botSalvaEditaAbastec").style.visibility = "visible"; // editar data/volume
                document.getElementById("titEditInsEquipamAbast").innerHTML = "Editando Abastecimento Bebedouro"; 
                document.getElementById("guardaCodMarca").value = Cod;
                document.getElementById("guardaIdCtl").value = idCtl;  // id do arquivo bebed.ctl para editar/deletar
                document.getElementById("dataAbastec").value = Data;
                document.getElementById("volumeAbast").value = Vol;
                document.getElementById("editaAbastec").style.display = "block";
            }
            function insAbastec(Cod, Num){
                document.getElementById("guardaCodMarca").value = Cod;
                document.getElementById("numEquipam").innerHTML = Num;
                document.getElementById("botInsEditaAbastec").innerHTML = "Abastecer Bebedouro "+Num;
                document.getElementById("numEquipamAbast").innerHTML = Num;
                document.getElementById("botSalvaInsAbastec").style.visibility = "visible";
                document.getElementById("botSalvaEditaAbastec").style.visibility = "hidden";
                document.getElementById("editaModalAbastec").style.display = "block";
                $("#relaBebed").load("modulos/bebedouros/iBebed.php?acao=todos&codigo="+Cod);
            }
            function insAbastecer(){
                insAbastec(document.getElementById("guardaCodMarca").value, document.getElementById("guardaNum").value);
            }

            function salvarEditaAbastec(){
                if(document.getElementById("dataAbastec").value == ""){
                    $('#mensagemAbastec').fadeIn("slow");
                    document.getElementById("mensagemAbastec").innerHTML = "Insira a data";
                    $('#mensagemAbastec').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("volumeAbast").value == ""){
                    $('#mensagemAbastec').fadeIn("slow");
                    document.getElementById("mensagemAbastec").innerHTML = "Insira o volume em litros";
                    $('#mensagemAbastec').fadeOut(2000);
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=salvaAbastec&codigo="+document.getElementById("guardaIdCtl").value
                    +"&data="+encodeURIComponent(document.getElementById("dataAbastec").value)
                    +"&volume="+document.getElementById("volumeAbast").value
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editaAbastec").style.display = "none";
                                    document.getElementById("editaModalAbastec").style.display = "block";
                                    $("#relaBebed").load("modulos/bebedouros/iBebed.php?acao=todos&codigo="+document.getElementById("guardaCodMarca").value);
                                    $("#container6").load("modulos/bebedouros/kBebed.php?acao=todos");
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function salvarAbastec(){
                if(document.getElementById("dataAbastec").value == ""){
                    $('#mensagemAbastec').fadeIn("slow");
                    document.getElementById("mensagemAbastec").innerHTML = "Insira a data";
                    $('#mensagemAbastec').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("volumeAbast").value == ""){
                    $('#mensagemAbastec').fadeIn("slow");
                    document.getElementById("mensagemAbastec").innerHTML = "Insira o volume em litros";
                    $('#mensagemAbastec').fadeOut(2000);
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=insAbastec&codigo="+document.getElementById("guardaCodMarca").value
                    +"&data="+encodeURIComponent(document.getElementById("dataAbastec").value)
                    +"&volume="+document.getElementById("volumeAbast").value
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editaAbastec").style.display = "none";
                                    document.getElementById("editaModalAbastec").style.display = "block";
                                    $("#relaBebed").load("modulos/bebedouros/iBebed.php?acao=todos&codigo="+document.getElementById("guardaCodMarca").value);
                                    $("#container6").load("modulos/bebedouros/kBebed.php?acao=todos");
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            
            function apagarAbastec(){
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma apagar este lançamento?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=apagaAbastec&codigo="+document.getElementById("guardaIdCtl").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                document.getElementById("editaAbastec").style.display = "none";
                                                document.getElementById("editaModalAbastec").style.display = "block";
                                                $("#relaBebed").load("modulos/bebedouros/iBebed.php?acao=todos&codigo="+document.getElementById("guardaCodMarca").value);
                                                $("#container6").load("modulos/bebedouros/kBebed.php?acao=todos");
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

            function calcPrazo(){
                if(document.getElementById("selecPrazo").value == ""){
                    return false;
                }
                if(document.getElementById("dataManut").value == ""){
                    return false;
                }
                document.getElementById("guardaPrazo").value = "";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=calcprazo&datatroca="+encodeURIComponent(document.getElementById("dataManut").value)
                    +"&prazoselec="+document.getElementById("selecPrazo").value
                    +"&diasanteced="+document.getElementById("diasAnteced").value
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("dataVencim").value = Resp.datafinal;
                                    document.getElementById("dataAviso").value = Resp.dataaviso;
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function calcAviso(){
                if(document.getElementById("dataVencim").value == ""){
                    return false;
                }
                document.getElementById("guardaPrazo").value = "";
                if(compareDates(document.getElementById("dataManut").value, document.getElementById("dataVencim").value) == true){
                    document.getElementById("dataVencim").focus();
                    $.confirm({
                        title: 'Atenção!',
                        content: 'A data do vencimento é de antes da data da manutenção.',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=calcaviso&vencim="+encodeURIComponent(document.getElementById("dataVencim").value)
                    +"&diasanteced="+document.getElementById("diasAnteced").value
                    +"&datatroca="+encodeURIComponent(document.getElementById("dataManut").value)
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")"); 
                                if(parseInt(Resp.coderro) === 0){
                                    if(document.getElementById("notifica1").checked === true){
                                        document.getElementById("dataAviso").value = Resp.dataaviso;
                                        if(compareDates(document.getElementById("dataManut").value, Resp.dataaviso) == true){
                                            document.getElementById("dataAviso").focus();
                                            $.confirm({
                                                title: 'Atenção!',
                                                content: 'A data do aviso é de antes da data da manutenção.',
                                                draggable: true,
                                                buttons: {
                                                    OK: function(){}
                                                }
                                            });
                                            return false;
                                        }
                                    }
                                    if(parseInt(Resp.prazod) > 0){
                                        document.getElementById("selecPrazo").value = "";
                                        document.getElementById("guardaPrazo").value = Resp.prazom+" meses "+Resp.prazod+" dias";
                                    }else{
                                        document.getElementById("selecPrazo").value = Resp.prazom;
                                    }
                                    if(parseInt(Resp.limpa) === 1){
                                        document.getElementById("diasAnteced").value = "";
                                        document.getElementById("diasAnteced").disabled = true;
                                        document.getElementById("notifica2").checked = true;
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

            function paraAviso(Obj){
                if(Obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bebedouros/salvaBebed.php?acao=mudarAviso&codigo="+document.getElementById("guardaCodMarca").value+"&valor="+Valor, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){

                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
                
            }

            function abreEscImprBebed(){
                document.getElementById("relacImprBebed").style.display = "block";
            }
            function fechaImprChaves(){
                document.getElementById("relacImprBebed").style.display = "none";
            }
            function calcVenc(){
                calcPrazo();
                document.getElementById("mudou").value = "1";
            }

            function imprUsuBebed(){
                window.open("modulos/bebedouros/imprUsuBebed.php?acao=listaUsuarios", "listaUsuBebed");
            }
            function fechaInsBebed(){
                document.getElementById("editaModalEquipam").style.display = "none";
            }
            function fechaModalConfig(){
                document.getElementById("relacmodalConfig").style.display = "none";
            }
            function fechaEditMarca(){
                document.getElementById("relacEditMarca").style.display = "none";
            }
            function fechaEditTipo(){
                document.getElementById("relacEditTipo").style.display = "none";
            }
            function fechaEditEmpr(){
                document.getElementById("relacEditEmpresa").style.display = "none";
            }
            function fechaInsAbastec(){
                document.getElementById("editaModalAbastec").style.display = "none";
            }
            function fechaEditaAbastec(){
                document.getElementById("editaAbastec").style.display = "none";
            }

            function foco(id){
                document.getElementById(id).focus();
            }
            function modif(){ // assinala se houve qualquer modificação nos campos do modal durante a edição para evitar salvar desnecessariamente
                document.getElementById("mudou").value = "1";
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
            function compareDates (date1, date2) {
                let parts1 = date1.split('/') // separa a data pelo caracter '/'
                date1 = new Date(parts1[2], parts1[1] - 1, parts1[0]) // formata 'date'

                let parts2 = date2.split('/') // separa a data pelo caracter '/'
                date2 = new Date(parts2[2], parts2[1] - 1, parts2[0]) // formata 'date'
                  // compara se a data informada é maior que a data atual e retorna true ou false
                return date1 > date2 ? true : false
            }

        </script>
    </head>
    <body class="corClara" onbeforeunload="return mudaTema(0)"> <!-- ao sair retorna os background claros -->
        <?php
        date_default_timezone_set('America/Sao_Paulo');

//------- Provisório
//        pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".bebed");
        pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".bebed (
                id SERIAL PRIMARY KEY, 
                numapar integer NOT NULL DEFAULT 0,
                codmarca integer NOT NULL DEFAULT 1, 
                modelo character varying(30), 
                codtipo integer NOT NULL DEFAULT 1, 
                localinst character varying(200), 
                datatroca date DEFAULT '3000-12-31', 
                datavencim date DEFAULT '3000-12-31', 
                dataaviso date DEFAULT '3000-12-31',
                notific smallint NOT NULL DEFAULT 1, 
                pararaviso smallint NOT NULL DEFAULT 0,
                prazotroca character varying(10), 
                diasanteced character varying(10),  
                observ text, 
                codempr integer NOT NULL DEFAULT 1, 
                consumo integer NOT NULL DEFAULT 0, 
                ativo smallint DEFAULT 1 NOT NULL, 
                usuins integer DEFAULT 0 NOT NULL,
                datains timestamp without time zone DEFAULT '3000-12-31',
                usuedit integer DEFAULT 0 NOT NULL,
                dataedit timestamp without time zone DEFAULT '3000-12-31' 
                ) 
            ");
            $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".bebed LIMIT 2");
            $row = pg_num_rows($rs);
            if($row == 0){
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed (id, numapar, codmarca, modelo, codtipo, localinst, observ, ativo) VALUES(1, 1, 3, 'Verona', 1, 'Recepção 1º andar', '', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed (id, numapar, codmarca, modelo, codtipo, localinst, observ, ativo) VALUES(2, 2, 2, 'PBE-200', 2, 'Auditório', '', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed (id, numapar, codmarca, modelo, codtipo, localinst, observ, ativo) VALUES(3, 3, 2, 'Gen-1A', 1, 'Sala 1', '', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed (id, numapar, codmarca, modelo, codtipo, localinst, observ, ativo) VALUES(4, 4, 2, '', 1, 'Térreo 1', '', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed (id, numapar, codmarca, modelo, codtipo, localinst, observ, ativo) VALUES(5, 5, 1, '', 2, 'Atendimento', '', 1)");
            }

//            pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".bebed_ctl");
            pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".bebed_ctl (
                id SERIAL PRIMARY KEY, 
                bebed_id integer DEFAULT 0 NOT NULL,
                datatroca date DEFAULT '3000-12-31', 
                volume integer DEFAULT 0 NOT NULL,
                ativo smallint DEFAULT 1 NOT NULL, 
                usuins integer DEFAULT 0 NOT NULL,
                datains timestamp without time zone DEFAULT '3000-12-31',
                usuedit integer DEFAULT 0 NOT NULL,
                dataedit timestamp without time zone DEFAULT '3000-12-31' 
                ) 
            ");
//            $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".bebed_ctl LIMIT 2");
//            $row = pg_num_rows($rs);
//            if($row == 0){
//                $DiaIni = strtotime(date('Y/m/01')); // número - para começar com o dia 1
//                $DiaIni = strtotime("-1 day", $DiaIni); // para começar com o dia 1 no loop for
//                for($i = 0; $i < 1280; $i++){
//                    $Amanha = strtotime("+1 day", $DiaIni);
//                    $DiaIni = $Amanha;
//                    $Data = date("Y/m/d", $Amanha); // data legível
//                    pg_query($Conec, "INSERT INTO ".$xProj.".bebed_ctl (bebed_id, datatroca, volume, usuins) VALUES (1, '$Data', 20, 3)");
//                }                
//            }

//            pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".bebed_marcas");
            pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".bebed_marcas (
                id SERIAL PRIMARY KEY, 
                descmarca character varying(30), 
                ativo smallint DEFAULT 1 NOT NULL, 
                usuins integer DEFAULT 0 NOT NULL,
                datains timestamp without time zone DEFAULT '3000-12-31',
                usuedit integer DEFAULT 0 NOT NULL,
                dataedit timestamp without time zone DEFAULT '3000-12-31' 
                ) 
            ");
            $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".bebed_marcas LIMIT 2");
            $row = pg_num_rows($rs);
            if($row == 0){
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed_marcas (id, descmarca, ativo) VALUES(1, 'Genérico', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed_marcas (id, descmarca, ativo) VALUES(2, 'IBBL', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed_marcas (id, descmarca, ativo) VALUES(3, 'Colormaq', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed_marcas (id, descmarca, ativo) VALUES(4, 'Esmaltec', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed_marcas (id, descmarca, ativo) VALUES(5, 'Masterfrio', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed_marcas (id, descmarca, ativo) VALUES(6, 'Newmaq', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed_marcas (id, descmarca, ativo) VALUES(7, 'Frisbel', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed_marcas (id, descmarca, ativo) VALUES(8, 'Libell', 1)");
            }
    
//            pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".bebed_tipos");
            pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".bebed_tipos (
                id SERIAL PRIMARY KEY, 
                desctipo character varying(30), 
                ativo smallint DEFAULT 1 NOT NULL, 
                usuins integer DEFAULT 0 NOT NULL,
                datains timestamp without time zone DEFAULT '3000-12-31',
                usuedit integer DEFAULT 0 NOT NULL,
                dataedit timestamp without time zone DEFAULT '3000-12-31' 
                ) 
            ");
            $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".bebed_tipos LIMIT 2");
            $row = pg_num_rows($rs);
            if($row == 0){
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed_tipos (id, desctipo, ativo) VALUES(1, 'Bancada', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed_tipos (id, desctipo, ativo) VALUES(2, 'Pedestal', 1)");
            }

//            pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".bebed_empr");
            pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".bebed_empr (
                id SERIAL PRIMARY KEY, 
                descempresa character varying(30), 
                ender VARCHAR(250), 
                cep VARCHAR(15), 
                cidade VARCHAR(50), 
                uf VARCHAR(3), 
                telefone VARCHAR(20), 
                contato VARCHAR(50), 
                cnpjempr VARCHAR(20), 
                inscrempr VARCHAR(20), 
                obsempr text, 
                ativo smallint DEFAULT 1 NOT NULL, 
                usuins integer DEFAULT 0 NOT NULL, 
                datains timestamp without time zone DEFAULT '3000-12-31', 
                usuedit integer DEFAULT 0 NOT NULL, 
                dataedit timestamp without time zone DEFAULT '3000-12-31', 
                usudel integer DEFAULT 0 NOT NULL, 
                datadel timestamp without time zone DEFAULT '3000-12-31' 
                ) 
            ");
            $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".bebed_empr LIMIT 2");
            $row = pg_num_rows($rs);
            if($row == 0){
                pg_query($Conec, "INSERT INTO ".$xProj.".bebed_empr (id, descempresa, ender, cep, cidade, uf, ativo) VALUES(1, 'Comércio Local', 'Av. Sobe e Desce e Nunca Aparece, 1001. - Setor de Empresas', '70000-000', 'Brasília', 'DF', 1)");
            }

//-------

        $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'bebed'");
        $rowSis = pg_num_rows($rsSis);
        if($rowSis == 0){
            require_once("../msgErro.php");
            return false;
        }

        $Hoje = date('d/m/Y');
        $Bebed = parEsc("bebed", $Conec, $xProj, $_SESSION["usuarioID"]);
        $FiscBebed = parEsc("bebed_fisc", $Conec, $xProj, $_SESSION["usuarioID"]);
        $Tema = parEsc("tema", $Conec, $xProj, $_SESSION["usuarioID"]); // Claro(0) Escuro(1)

        $OpMarcas = pg_query($Conec, "SELECT id, descmarca FROM ".$xProj.".bebed_marcas WHERE ativo = 1 ORDER BY descmarca");
        $OpTipos = pg_query($Conec, "SELECT id, desctipo FROM ".$xProj.".bebed_tipos WHERE ativo = 1 ORDER BY desctipo ");

        $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(datatroca, 'MM'), '/', TO_CHAR(datatroca, 'YYYY')) 
        FROM ".$xProj.".bebed_ctl GROUP BY TO_CHAR(datatroca, 'MM'), TO_CHAR(datatroca, 'YYYY') ORDER BY TO_CHAR(datatroca, 'YYYY') DESC, TO_CHAR(datatroca, 'MM') DESC ");
        $OpcoesEscAno = pg_query($Conec, "SELECT EXTRACT(YEAR FROM ".$xProj.".bebed_ctl.datatroca)::text 
        FROM ".$xProj.".bebed_ctl GROUP BY 1 ORDER BY 1 DESC ");

        if(isset($_REQUEST["acao"])){
            $Acao = $_REQUEST["acao"];
        }else{
            $Acao = "todos";
        }
        //filtrado - precisa botão para mostrar todos
        $Acao = "todos";
        ?>

        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>" />
        <input type="hidden" id="mudou" value="0" />
        <input type="hidden" id="guardaEdit" value="<?php echo $Bebed; ?>" />
        <input type="hidden" id="guardaFiscal" value="<?php echo $FiscBebed; ?>" />
        <input type="hidden" id="guardaUsuId" value="<?php echo $_SESSION["usuarioID"]; ?>" />
        <input type="hidden" id="guardaCodMarca" value="0" />
        <input type="hidden" id="guardaIdCtl" value="0" />
        <input type="hidden" id="guardaPrazo" value = "" />
        <input type="hidden" id="guardaHoje" value = "<?php echo $Hoje; ?>" />
        <input type="hidden" id="guardaAcao" value="<?php echo $Acao; ?>" />
        <input type="hidden" id="guardaNum" value = "" />

        <!-- div três colunas -->
        <div id="tricoluna0" class="corClara" style="margin: 5px; padding: 10px; border: 2px solid blue; border-radius: 10px; min-height: 50px;">
            <div id="tricoluna1" class="box corClara" style="position: relative; float: left; width: 33%;">
                <img src="imagens/settings.png" height="20px;" id="imgConfig" style="cursor: pointer; padding-right: 20px;" onclick="carregaConfig();" title="Configurar parâmetros.">
                <input type="button" id="botinserir" class="resetbot fundoAzul2" style="font-size: 80%;" value="Inserir" onclick="insereEquip();" title="Inserir um novo bebedouro.">
            </div>
            <div id="tricoluna2" class="box corClara" style="position: relative; float: left; width: 33%; text-align: center;">
                <h5>Bebedouros Instalados</h5>
            </div>
            <div id="tricoluna3" class="box corClara" style="position: relative; float: left; width: 33%; text-align: right;">
                <div id="selectTema" style="position: relative; float: left; padding-left: 30px;">
                    <label id="etiqcorFundo" class="etiq" style="color: #6C7AB3; font-size: 80%; padding-left: 10px;">Tema: </label>
                    <input type="radio" name="corFundo" id="corFundo0" value="0" <?php if($Tema == 0){echo 'CHECKED';}; ?> title="Tema claro" onclick="mudaTema(0);" style="cursor: pointer;"><label for="corFundo0" style="cursor: pointer; font-size: 80%;">&nbsp;Claro</label>
                    <input type="radio" name="corFundo" id="corFundo1" value="1" <?php if($Tema == 1){echo 'CHECKED';}; ?> title="Tema escuro" onclick="mudaTema(1);" style="cursor: pointer;"><label for="corFundo1" style="cursor: pointer; font-size: 80%;">&nbsp;Escuro</label>
                </div>
                <label style="padding-left: 10px;"></label>
                <button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="abreEscImprBebed();">PDF</button>
            </div>

            <div id="faixaMensagem" style="display: none; position: relative; margin: 70px; padding: 20px; text-align: center; border: 1px solid; border-radius: 15px;">
                Usuário não cadastrado.
            </div>
        </div>

        <!-- Tabelas -->
        <div id="container5" style="margin-top 10px; width: 70%;"></div>
        <div id="intercolunas" style="width: 1%;"></div>  <!-- espaçamento entre colunas -->
        <div id="container6" style="margin-top 10px; width: 25%;"></div> 

        <!-- config para inserir usuários, bebedouros, empresas... -->
        <div id="relacmodalConfig" class="relacmodal">
            <div class="modal-content-Config">
                <span class="close" onclick="fechaModalConfig();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col" style="margin: 0 auto;"></div>
                        <div class="col"><h6 id="titulomodal" style="text-align: center; color: #666;">Controle dos Bebedouros</h6></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col" style="margin: 0 auto; text-align: center;"><button id="botimprUsu" class="botpadrred" style="font-size: 70%;" onclick="imprUsuBebed();">Resumo em PDF</button></div> 
                    </div>
                </div>
                
                <div id="configAdmin"></div> <!-- para superusuários -->

                <table style="margin: 0 auto; width: 86%;">
                    <tr>
                        <td style="vertical-align: top;">
                            <div style="position: relative; float: left; width: 99%; margin-top: 10px; text-align: center; border: 1px solid; border-radius: 10px; background: linear-gradient(180deg, white, #86c1eb);">
                                <div style="margin: 20px; min-width: 200px; padding: 5px; text-align: center; border: 1px solid; border-radius: 15px; background: linear-gradient(180deg, white, #86c1eb);">
                                    <div class='divbot corFundo' onclick='insMarca()' title="Adicionar uma nova marca de bebedouro"> Adicionar </div>
                                    <div id="configMarcas" style="margin-bottom: 15px; text-align: center; width: 90%;"></div>
                                </div>
                            </div>
                        </td>
                        <td></td>
                        <td></td>
                        <td style="vertical-align: top;">
                            <div style="position: relative; float: right; width: 99%; margin-top: 10px; text-align: center; border: 1px solid; border-radius: 10px; background: linear-gradient(180deg, white, #86c1eb);">
                                <div style="margin: 20px; min-width: 200px; padding: 5px; text-align: center; border: 1px solid; border-radius: 15px; background: linear-gradient(180deg, white, #86c1eb);">
                                    <div class='divbot corFundo' onclick='insTipo()' title="Adicionar novo tipo de elementro filtrante"> Adicionar </div>
                                    <div id="configTipos" style="text-align: center;"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;">
                            <div style="position: relative; float: right; width: 99%; margin-top: 10px; text-align: center; border: 1px solid; border-radius: 10px; background: linear-gradient(180deg, white, #86c1eb);">
                                <div style="margin: 20px; min-width: 200px; padding: 5px; text-align: center; border: 1px solid; border-radius: 15px; background: linear-gradient(180deg, white, #86c1eb);">
                                    <div class='divbot corFundo' onclick='insEmpr()' title="Adicionar nova empresa de manutenção"> Adicionar </div>
                                    <div id="configEmpr" style="text-align: center;"></div>
                                </div>
                            </div>
                        </td>
                        <td></td>
                        <td></td>
                        <td style="vertical-align: top;"></td>
                    </tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->

        <div id="relacEditMarca" class="relacmodal">
            <div class="modal-content-InsMarca">
                <span class="close" onclick="fechaEditMarca();">&times;</span>
                <h5 id="titulomodalMarca" style="text-align: center; color: #666;">Nova Marca de bebedouro</h5>
                    <table style="margin: 0 auto; width: 90%">
                        <tr>
                            <td class="etiq aDir">Marca: </td>
                            <td><input type="text" id="editNomeMarca" maxlength="30" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%;"></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                    <br>
                    <div style="text-align: center;">
                        <button id="botApagaEditMarca" class="resetbotred" style="font-size: .8rem;" onclick="apagaMarca();">Apagar</button>
                        <label style="padding-left: 50%;"></label>
                        <button id="botSalvarEditTipo" class="resetbot" style="font-size: .9rem;" onclick="salvaEditMarca();">Salvar</button>
                    </div>
                </div>
            </div>
        </div> <!-- Fim Modal-->

        <div id="relacEditTipo" class="relacmodal">
            <div class="modal-content-InsTipo">
                <span class="close" onclick="fechaEditTipo();">&times;</span>
                <h5 id="titulomodalTipo" style="text-align: center; color: #666;">Novo Tipo de Bebedouro</h5>
                    <table style="margin: 0 auto; width: 90%">
                        <tr>
                            <td class="etiq aDir">Tipo: </td>
                            <td><input type="text" id="editNomeTipo" maxlength="30" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%;"></td>
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

        <div id="relacEditEmpresa" class="relacmodal">
            <div class="modal-content-InsEmpresa corPreta">
                <span class="close" onclick="fechaEditEmpr();">&times;</span>
                <h5 id="titulomodalEmpr" style="text-align: center; color: #666;">Nome da nova Empresa</h5>
                    <table style="margin: 0 auto; width: 90%">
                        <tr>
                            <td class="etiq aDir">Empresa: </td>
                            <td colspan="3"><input type="text" id="editNomeEmpr" valor="" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%;" onkeypress="if(event.keyCode===13){javascript:foco('editCEP');return false;}"></td>
                        </tr>
                        <tr>
                            <td class="etiq aDir">Ender: </td>
                            <td colspan="3">
                                <textarea id="editEnder" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px; padding: 2px;" rows="2" cols="40" title="Endereço da Empresa" onchange="modif();"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiq aDir">CEP: </td>
                            <td colspan="3">
                                <input type="text" id="editCEP" valor="" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90px; font-size: 80%; text-align: center;" onkeypress="if(event.keyCode===13){javascript:foco('editCidade');return false;}">
                                <label class="etiq aDir">Cidade: </label>
                                <input type="text" id="editCidade" valor="" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 250px; font-size: 80%;" onkeypress="if(event.keyCode===13){javascript:foco('editUF');return false;}">
                                <label class="etiq aDir">UF: </label>
                                <input type="text" id="editUF" valor="" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 60px; font-size: 80%; text-align: center;" onkeypress="if(event.keyCode===13){javascript:foco('editCNPJ');return false;}">
                            </td>
                        </tr>
                        <tr>
                            <td class="etiq aDir">CNPJ: </td>
                            <td><input type="text" id="editCNPJ" valor="" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%; text-align: center;" onkeypress="if(event.keyCode===13){javascript:foco('editInscr');return false;}"></td>
                            <td class="etiq aDir">Inscrição: </td>
                            <td><input type="text" id="editInscr" valor="" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%; text-align: center;" onkeypress="if(event.keyCode===13){javascript:foco('editTelef');return false;}"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding-top: 5px;"></td>
                        </tr>
                        <tr>
                            <td class="etiq aDir">Telef: </td>
                            <td><input type="text" id="editTelef" valor="" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%;" onkeypress="if(event.keyCode===13){javascript:foco('editContato');return false;}"></td>
                            <td class="etiq aDir">Contato: </td>
                            <td><input type="text" id="editContato" valor="" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%;" onkeypress="if(event.keyCode===13){javascript:foco('editNomeEmpr');return false;}"></td>
                        </tr>
                        <tr>
                            <td class="etiq aDir">Observ: </td>
                            <td colspan="3">
                                <textarea id="editObs" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px; padding: 2px;" rows="2" cols="58" title="Observações sobre a Empresa" onchange="modif();"></textarea>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <div style="text-align: center;">
                        <button id="botApagaEditEmpr" class="resetbotred" style="font-size: .8rem;" onclick="apagaEmpr();">Apagar</button>
                        <label style="padding-left: 50%;"></label>
                        <button id="botSalvarEditEmpr" class="resetbot" style="font-size: .9rem;" onclick="salvaEditEmpr();">Salvar</button>
                    </div>
                </div>
            </div>
        </div> <!-- Fim Modal-->

        <!-- edita Equipamento -->
        <div id="editaModalEquipam" class="relacmodal">
            <div class="modal-content-relacEquip corPreta">
                <span class="close" style="font-size: 250%; color: black;" onclick="fechaInsBebed();">&times;</span>
                <div style="border: 2px solid blue; border-radius: 10px; margin-top: 10px; background: linear-gradient(180deg, white, #87CEEB)">
                    <div style="position: relative; float: left; padding: 10px;">
                        <button id="botinsAbastecer" class="botpadrblue" style="font-size: 70%; padding-left: 2px; padding-right: 2px;" onclick="insAbastecer();">Abastecer</button>
                    </div>
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 10px;"><label id="titmodaledit">Bebedouro</label></td>
                        </tr>
                        <tr>
                            <td class="etiq">Número: </td>
                            <td colspan="4" style="text-align: left; padding-top: 10px;">
                                <input type="text" id="numequip" style="width: 70px; text-align: center; border: 1px solid; border-radius: 5px; font-weight: bold;" placeholder="Número" onkeypress="if(event.keyCode===13){javascript:foco('localinstal');return false;}"/>
                                <label class="etiq">Local: </label>
                                <input type="text" id="localinstal" maxlength="200" style="width: 560px;text-align: left; border: 1px solid; border-radius: 5px;" placeholder="Local de instalação" onkeypress="if(event.keyCode===13){javascript:foco('modeloBebedouro');return false;}"/>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiq">Marca: </td>
                            <td colspan="4" style="min-width: 120px;">
                                <select id="selecMarca" style="max-width: 150px;" onchange="modif();" title="Selecione uma marca.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpMarcas){
                                        while ($Opcoes = pg_fetch_row($OpMarcas)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                        <?php 
                                        }
                                    } 
                                    ?>
                                </select>

                                <label class="etiq">Modelo: </label>
                                <input type="text" id="modeloBebedouro" maxlength="30" style="width: 250px; text-align: left; border: 1px solid; border-radius: 5px;" placeholder="Modelo" onkeypress="if(event.keyCode===13){javascript:foco('dataManut');return false;}"/>

                                <label class="etiq">Tipo Bebedouro: </label>
                                <select id="selecTipo" style="max-width: 150px;" onchange="modif();" title="Selecione um tipo de Bebedouro.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpTipos){
                                        while ($Opcoes = pg_fetch_row($OpTipos)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiq">Observ: </td>
                            <td colspan="4" style="text-align: left;">
                                <textarea id="observBebed" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px; padding: 2px;" rows="2" cols="58" title="Observações" onchange="modif();"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                    </table>
                </div>

                <div style="border: 2px solid blue; border-radius: 10px; margin-top: 10px; background: linear-gradient(180deg, white, #87CEEB)">
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="3" class="etiq aCentro">Limpeza da Base do Bebedouro</td>
                            <td colspan="2" class="etiq aCentro" style="border-inline: 1px solid; border-top: 1px solid;">Notificação</td>
                        </tr>
                        <tr>
                            <td class="etiq aEsq">Data da última limpeza:</td>
                            <td class="etiq aEsq">Intervalo:</td>
                            <td class="etiq aEsq">Próxima Limpeza:</td>
                            <td class="etiq aCentro" style="border-left: 1px solid;">Avisar?</td>
                            <td class="etiq aCentro" style="border-right: 1px solid;">Antecedência</td>
                        </tr>

                        <tr>
                            <!-- on change nas datas com o datepicker trava a máquina -->
                            <td style="text-align: center;">
                                <input type="text" id="dataManut" width="150" onclick="$datepicker.open();" onchange="calcVenc();" style="height: 30px; text-align: center; border: 1px solid; border-radius: 5px;" placeholder="Data" onkeypress="if(event.keyCode===13){javascript:foco('dataVencim');return false;}"/>
                            </td>
                            <td style="vertical-align: top;">
                                <select id="selecPrazo" style="min-width: 50px;" onchange="calcPrazo();" title="Selecione um prazo para a limpeza do bebedouro.">
                                    <option value=""></option>
                                    <option value="1"> 1 mês</option>
                                    <option value="2"> 2 meses</option>
                                    <option value="3"> 3 meses</option>
                                    <option value="4"> 4 meses</option>
                                    <option value="5"> 5 meses</option>
                                    <option value="6"> 6 meses</option>
                                    <option value="7"> 7 meses</option>
                                    <option value="8"> 8 meses</option>
                                    <option value="9"> 9 meses</option>
                                    <option value="10">10 meses</option>
                                    <option value="11">11 meses</option>
                                    <option value="12">12 meses</option>
                                    <option value="13">13 meses</option>
                                    <option value="14">14 meses</option>
                                    <option value="15">15 meses</option>
                                    <option value="16">16 meses</option>
                                    <option value="17">17 meses</option>
                                    <option value="18">18 meses</option>
                                    <option value="24"> 2 anos</option>
                                    <option value="36"> 3 anos</option>
                                    <option value="48"> 4 anos</option>
                                    <option value="60"> 5 anos</option>
                                    <option value="72"> 6 anos</option>
                                    <option value="84"> 7 anos</option>
                                    <option value="96"> 8 anos</option>
                                    <option value="108"> 9 anos</option>
                                    <option value="120">10 anos</option>
                                </select>
                            </td>
                            <!-- on change nas datas com o datepicker trava a máquina -->
                            <td style="text-align: center; vertical-align: top;"><input type="text" id="dataVencim" style="text-align: center; border: 1px solid; border-radius: 5px; width: 100px;" onchange="calcAviso();" placeholder="Data" onkeypress="if(event.keyCode===13){javascript:foco('diasAnteced');return false;}" /></td>
                            <td class="aCentro" style="border-left: 1px solid; border-color: #9C9C9C; vertical-align: top;">
                                <input type="radio" name="notifica" id="notifica1" value="1" CHECKED title="Emite notificação?" onclick="abreNotific(value);"><label for="notifica1" class="etiqAzul" style="padding-left: 3px;"> Sim</label>
                                <input type="radio" name="notifica" id="notifica2" value="0" title="Emite notificação?" onclick="abreNotific(value);"><label for="notifica2" class="etiqAzul" style="padding-left: 3px;"> Não</label>
                            </td>
                            <td style="text-align: center; border-right: 1px solid; border-color: #9C9C9C; vertical-align: top;">
                                <input type="text" id="diasAnteced" style="width: 60px; text-align: center; border: 1px solid; border-radius: 5px;" placeholder="Dias" onchange="calcAviso();" onkeypress="if(event.keyCode===13){javascript:foco('dataAviso');return false;}" title="Aviso emitido na página inicial ao longo desses dias."/>
                                <label class="etiq"> dias</label>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3" style="text-align: center;">
                                <input type="checkbox" id="pararaviso" title="Parar a emissão de aviso na página inicial sobre este aparelho." onclick="paraAviso(this);" >
                                <label class="etiqAzul" id="etiqpararaviso" for="pararaviso" title="Parar a emissão de aviso na página inicial sobre este aparelho.">desativar aviso na página inicial</label>
                            </td>
                            <td colspan="2" style="text-align: center; border-left: 1px solid; border-bottom: 1px solid; border-right: 1px solid; border-color: #9C9C9C; vertical-align: top;">
                                <label class="etiq">Dia aviso:</label>
                                <input type="text" id="dataAviso" style="text-align: center; border: 1px solid; border-radius: 5px; width: 110px;" placeholder="Data Aviso" onkeypress="if(event.keyCode===13){javascript:foco('numequip');return false;}"/>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 5px;"><div id="mensagem" style="color: red; font-weight: bold;"></div></td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid gray;"><button class="botpadrred" style="font-size: 60%;" id="botApagaLanc" onclick="apagarEquip();">Apagar</button></td>
                            <td colspan="4" style="border-bottom: 1px solid gray; text-align: center; padding: 5px;"><button class="botpadrblue" onclick="salvaEditaEquip();">Salvar</button></td>
                        </tr>
                    </table>
                    <br>
                </div>

                </div>
            </div>
        </div> <!-- Fim Modal-->


        <!-- Abastecimento -->
        <div id="editaModalAbastec" class="relacmodal">
            <div class="modal-content-relacAbastec corPreta">
                <span class="close" style="font-size: 250%; color: black;" onclick="fechaInsAbastec();">&times;</span>
                <div style="margin: 0 auto; border: 2px solid blue; border-radius: 10px; margin-top: 10px; background: linear-gradient(180deg, white, #87CEEB)">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td colspan="3" style="text-align: center;">
                                <label>Abastecimento</label> <br> <label></label>Bebedouro: <label id="numEquipam"></label>
                            </td>
                        </tr>
                        <tr>
                            <td><button id="botInsEditaAbastec" class="botpadrblue" onclick="InsEditaAbastec();">Abastecer Bebedouro</button></td>
                            <td></td>
                            <td class="aDir"><button id="imprBebedAbast" class="botpadrred" style="font-size: 80%;" id="botimpr">PDF</button></td>
                        </tr>
                    </table>
                    
                    <div id="relaBebed" style="overflow: auto; width: 900px; margin: 10px; border: 2px solid blue; border-radius: 10px; background: linear-gradient(180deg, white, #87CEEB)"></div>

                </div>
            </div>
        </div> <!-- Fim Modal-->


        <!-- edita Equipamento -->
        <div id="editaAbastec" class="relacmodal">
            <div class="modal-content-relacEditaAbastec corPreta">
                <span class="close" style="font-size: 250%; color: black;" onclick="fechaEditaAbastec();">&times;</span>
                <div style="border: 2px solid red; border-radius: 10px; margin-top: 10px; padding: 10px; background: linear-gradient(180deg, white, #87CEEB)">
                    <table style="margin: 0 auto; width: 65%;">
                        <tr>
                            <td colspan="3" style="text-align: center;"><label id="titEditInsEquipamAbast">Abastecer Bebedouro</label> <label id="numEquipamAbast"></label></td>
                        </tr>
                        <tr>
                            <td class="etiq aCentro"></td>
                            <td class="etiq aCentro"></td>
                            <td class="etiq aCentro"></td>
                        </tr>
                        <tr>
                            <td class="etiq aEsq">Data abastecimento:</td>
                            <td class="etiq aEsq"></td>
                            <td class="etiq aEsq"></td>
                        </tr>

                        <tr>
                            <!-- on change nas datas com o datepicker trava a máquina -->
                            <td style="text-align: right; padding-top 5px;">
                                <input type="text" id="dataAbastec" width="150" onclick="$datepicker.open();" onchange="insGalao();" style="height: 30px; text-align: center; border: 1px solid; border-radius: 5px;" placeholder="Data" onkeypress="if(event.keyCode===13){javascript:foco('volumeAbast');return false;}"/>
                            </td>
                            <td class="aEsq" style="vertical-align: top;">
                                <label class="etiqAzul">Volume:</label>
                                <input type="text" id="volumeAbast" maxlength="10" title="Insira o volume em litros do galão de água." onchange="modif();" placeholder="Volume" style="height: 30px; width: 60px; text-align: center; border: 1px solid; border-radius: 5px;">
                                <label class="etiqAzul">litros</label>
                            </td>
                            <td class="etiq aEsq"></td>
                        </tr>
                        <tr>
                            <td colspan="3" id="mensagemAbastec" style="color: red; font-weight: bold; text-align: center;"></td>
                        </tr>
                        <tr>
                            <td style="border-bottom: 1px solid gray;"><button id="botApagaAbast" class="botpadrred" style="font-size: 60%; padding-left: 2px; padding-right: 2px;" onclick="apagarAbastec();">Apagar</button></td>
                            <td colspan="2" style="border-bottom: 1px solid gray; text-align: center; padding: 5px;">
                                <button id="botSalvaInsAbastec" class="botpadrblue" onclick="salvarAbastec();">Salvar</button>
                                <button id="botSalvaEditaAbastec" class="botpadrblue" onclick="salvarEditaAbastec();">Salvar</button>
                            </td>
                        </tr>

                    </table>
                    <br>
                    </div>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para escolher imprimir em pdf  -->
        <div id="relacImprBebed" class="relacmodal">
            <div class="modal-content-escImprBebed corPreta">
                <span class="close" onclick="fechaImprChaves();">&times;</span>
                <h5 style="text-align: center;color: #666;">Controle de Bebedouros</h5>
                <h6 style="text-align: center; padding-bottom: 18px; color: #666;">Gerar PDF</h6>
                <div style="border: 2px solid; border-radius: 10px; padding: 10px; text-align: center;">
                    <input type="button" id="imprRelBebed" class="resetbot fundoAzul2" style="font-size: 80%;" value="Relação dos Bebedouros">
                </div>
                <div style="margin-top: 5px; border: 2px solid; border-radius: 10px; padding: 10px;">
                    <div style="text-align: center; color: #666;">Relação Consumo</div>
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
        </div> <!-- Fim Modal Impr -->


        <div id="carregaTema"></div> <!-- carrega a pág modulos/config/carTema.php - onde está a função mudaTema() -->

    </body>
</html>