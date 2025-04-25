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
        <title>Filtros</title>
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
            .modal-content-relacFiltros{
                background: transparent;
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 70%; /* acertar de acordo com a tela */
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
                    $("#container5").load("modulos/filtros/jFiltros.php?acao="+document.getElementById("guardaAcao").value);
                    $("#container6").load("modulos/filtros/kFiltros.php?acao="+document.getElementById("guardaAcao").value);
                    document.getElementById("botimpr").style.visibility = "visible";

                    if(parseInt(document.getElementById("guardaEdit").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){ 
                        document.getElementById("botinserir").style.visibility = "visible";
                        document.getElementById("imgConfig").style.visibility = "visible";
                    }
                }else{
                    document.getElementById("faixaMensagem").style.display = "block";
                }

                if(parseInt(document.getElementById("UsuAdm").value) > 6){
                    $("#configAdmin").load("modulos/filtros/admFiltros.php");
                    document.getElementById("botimprUsu").style.visibility = "visible";
                }

                $('#dataTrocaFiltro').datepicker({ uiLibrary: 'bootstrap5', locale: 'pt-br', format: 'dd/mm/yyyy' });
                $("#dataTrocaFiltro").mask("99/99/9999");
                $("#dataVencim").mask("99/99/9999");

                $('#carregaTema').load('modulos/config/carTema.php?carpag=clavic1');

            }); // fim do ready

            function carregaConfig(){
                $("#configMarcas").load("modulos/filtros/jMarcas.php");
                $("#configTipos").load("modulos/filtros/jTipos.php");
                $("#configEmpr").load("modulos/filtros/jEmpr.php");
                $("#configAdmin").load("modulos/filtros/admFiltros.php");
                document.getElementById("relacmodalConfig").style.display = "block";
            }
            function fechaConfig(){
                document.getElementById("relacmodalConfig").style.display = "none";
            }

            function insereFiltro(){
                document.getElementById("botApagaLanc").style.visibility = "hidden";
                document.getElementById("guardaCodMarca").value = 0;
                document.getElementById("numfiltro").value = "";
                document.getElementById("localinstal").value = "";
                document.getElementById("selecMarca").value = 1;
                document.getElementById("selecTipo").value = 1;
                document.getElementById("modelofiltro").value = "";
                document.getElementById("selecPrazo").value = "12";
                document.getElementById("diasAnteced").value = "30";
                document.getElementById("notifica1").checked = true;
                document.getElementById("pararaviso").checked = false;
//                document.getElementById("dataTrocaFiltro").value = "";
                document.getElementById("dataTrocaFiltro").value = document.getElementById("guardaHoje").value;
                document.getElementById("editaModalFiltros").style.display = "block";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=calcprazo&datatroca="+encodeURIComponent(document.getElementById("dataTrocaFiltro").value)
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
                                    document.getElementById("numfiltro").value = Resp.proxnumero;
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function editaFiltro(Cod){
                document.getElementById("guardaCodMarca").value = Cod;
                if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário 
                    document.getElementById("botApagaLanc").style.visibility = "visible";
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=buscadadosFiltro&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("numfiltro").value = Resp.numapar;
                                    document.getElementById("localinstal").value = Resp.localinst;
                                    document.getElementById("selecMarca").value = Resp.codmarca;
                                    document.getElementById("selecTipo").value = Resp.codtipo;
                                    document.getElementById("dataTrocaFiltro").value = Resp.datatroca;
                                    document.getElementById("modelofiltro").value = Resp.modelo;
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
                                    document.getElementById("editaModalFiltros").style.display = "block";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaEditaFiltro(){
                if(document.getElementById("numfiltro").value == ""){
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
                    document.getElementById("mensagem").innerHTML = "Selecione o tipo de elemento filtrante";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("dataTrocaFiltro").value == ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira a data em que foi trocado o elemento filtrante";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("selecPrazo").value == ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira um prazo para e troca do atual elemento filtrante";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("dataVencim").value == ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira a data de vencimento do atual elemento filtrante";
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
                        document.getElementById("mensagem").innerHTML = "Insira a data para emitir a notificação de troca do elemento filtrante";
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
                    ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=salvaEditFiltro&codigo="+document.getElementById("guardaCodMarca").value
                    +"&numfiltro="+document.getElementById("numfiltro").value
                    +"&localinst="+encodeURIComponent(document.getElementById("localinstal").value)
                    +"&codmarca="+document.getElementById("selecMarca").value
                    +"&codtipo="+document.getElementById("selecTipo").value
                    +"&datatroca="+encodeURIComponent(document.getElementById("dataTrocaFiltro").value)
                    +"&modelo="+encodeURIComponent(document.getElementById("modelofiltro").value)
                    +"&datavenc="+encodeURIComponent(document.getElementById("dataVencim").value)
                    +"&dataaviso="+encodeURIComponent(document.getElementById("dataAviso").value)
                    +"&observ="+encodeURIComponent(document.getElementById("observfiltro").value)
                    +"&notif="+Notif
                    +"&prazotroca="+document.getElementById("selecPrazo").value
                    +"&diasanteced="+document.getElementById("diasAnteced").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    $("#container5").load("modulos/filtros/jFiltros.php?acao=todos");
                                    $("#container6").load("modulos/filtros/kFiltros.php?acao=todos");
                                    document.getElementById("editaModalFiltros").style.display = "none";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function apagarFiltro(){
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma apagar este equipamento?<br>Os lançamentos serão perdidos.<br>Continua?',
                    autoClose: 'Não|20000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=apagaFiltro&codigo="+document.getElementById("guardaCodMarca").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $("#container5").load("modulos/filtros/jFiltros.php?acao=todos");
                                                $("#container6").load("modulos/filtros/kFiltros.php?acao=todos");
                                                document.getElementById("editaModalFiltros").style.display = "none";
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
                document.getElementById("titulomodalMarca").innerHTML = "Nova marca de filtro/purificador";
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
                    ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=buscaMarca&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editNomeMarca").value = Resp.nome;
                                    document.getElementById("titulomodalMarca").innerHTML = "Edita marca de filtro/purificador";
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
                        ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=salvanovaMarca&codigo="+document.getElementById("guardaCodMarca").value 
                        +"&nomemarca="+encodeURIComponent(document.getElementById("editNomeMarca").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("relacEditMarca").style.display = "none";
                                        $("#configMarcas").load("modulos/filtros/jMarcas.php");
                                        $("#container5").load("modulos/filtros/jFiltros.php?acao=todos");
                                        $("#container6").load("modulos/filtros/kFiltros.php?acao=todos");
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
                                ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=apagaMarca&codigo="+document.getElementById("guardaCodMarca").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $("#configMarcas").load("modulos/filtros/jMarcas.php");
//                                                carregaTipos(); // recarrega relação
                                                $("#container5").load("modulos/filtros/jFiltros.php?acao=todos");
                                                $("#container6").load("modulos/filtros/kFiltros.php?acao=todos");
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
                document.getElementById("titulomodalTipo").innerHTML = "Novo tipo de elemento filtrante";
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
                    ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=buscaTipo&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editNomeTipo").value = Resp.nome;
                                    document.getElementById("titulomodalTipo").innerHTML = "Edita tipo de filtro";
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
                        ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=salvanovoTipo&codigo="+document.getElementById("guardaCodMarca").value 
                        +"&nometipo="+encodeURIComponent(document.getElementById("editNomeTipo").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("relacEditTipo").style.display = "none";
                                        $("#configTipos").load("modulos/filtros/jTipos.php");
                                        $("#container5").load("modulos/filtros/jFiltros.php?acao=todos");
                                        $("#container6").load("modulos/filtros/kFiltros.php?acao=todos");
                                        
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
                                ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=apagaTipo&codigo="+document.getElementById("guardaCodMarca").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $("#configTipos").load("modulos/filtros/jTipos.php");
                                                $("#container5").load("modulos/filtros/jFiltros.php?acao=todos");
                                                $("#container6").load("modulos/filtros/kFiltros.php?acao=todos");
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
                    ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=buscaEmpresa&codigo="+Cod, true);
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
                        ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=salvaEmpresa&codigo="+document.getElementById("guardaCodMarca").value 
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
                                        $("#configEmpr").load("modulos/filtros/jEmpr.php");
                                        $("#container5").load("modulos/filtros/jFiltros.php?acao=todos");
                                        $("#container6").load("modulos/filtros/kFiltros.php?acao=todos");
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
                                ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=apagaEmpre&codigo="+document.getElementById("guardaCodMarca").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $("#configEmpr").load("modulos/filtros/jEmpr.php");
//                                                carregaTipos(); // recarrega relação
                                                $("#container5").load("modulos/filtros/jFiltros.php?acao=todos");
                                                $("#container6").load("modulos/filtros/kFiltros.php?acao=todos");
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

            function calcPrazo(){
                if(document.getElementById("selecPrazo").value == ""){
                    return false;
                }
                if(document.getElementById("dataTrocaFiltro").value == ""){
                    return false;
                }
                document.getElementById("guardaPrazo").value = "";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=calcprazo&datatroca="+encodeURIComponent(document.getElementById("dataTrocaFiltro").value)
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
                if(compareDates(document.getElementById("dataTrocaFiltro").value, document.getElementById("dataVencim").value) == true){
                    document.getElementById("dataVencim").focus();
                    $.confirm({
                        title: 'Atenção!',
                        content: 'A data do vencimento é de antes da data de troca do filtro.',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=calcaviso&vencim="+encodeURIComponent(document.getElementById("dataVencim").value)
                    +"&diasanteced="+document.getElementById("diasAnteced").value
                    +"&datatroca="+encodeURIComponent(document.getElementById("dataTrocaFiltro").value)
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")"); 
                                if(parseInt(Resp.coderro) === 0){
                                    if(document.getElementById("notifica1").checked === true){
                                        document.getElementById("dataAviso").value = Resp.dataaviso;
                                        if(compareDates(document.getElementById("dataTrocaFiltro").value, Resp.dataaviso) == true){
                                            document.getElementById("dataAviso").focus();
                                            $.confirm({
                                                title: 'Atenção!',
                                                content: 'A data do aviso é de antes da data de troca do filtro.',
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
                    ajax.open("POST", "modulos/filtros/salvaFiltros.php?acao=mudarAviso&codigo="+document.getElementById("guardaCodMarca").value+"&valor="+Valor, true);
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

            function calcVenc(){
                calcPrazo();
                document.getElementById("mudou").value = "1";
            }
            function imprFiltros(){
                window.open("modulos/filtros/imprFiltros.php?acao=listaFiltros", "listaFiltros");
            }
            function imprUsuFiltros(){
                window.open("modulos/filtros/imprUsuFiltr.php?acao=listaUsuarios", "listaUsuFiltros");
            }
            function fechaInsFiltro(){
                document.getElementById("editaModalFiltros").style.display = "none";
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
//        pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".filtros");
        pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".filtros (
                id SERIAL PRIMARY KEY, 
                numapar integer NOT NULL DEFAULT 0,
                codmarca integer NOT NULL DEFAULT 1,
                modelo character varying(30), 
                tipofiltro integer NOT NULL DEFAULT 1, 
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
                ativo smallint DEFAULT 1 NOT NULL, 
                usuins integer DEFAULT 0 NOT NULL,
                datains timestamp without time zone DEFAULT '3000-12-31',
                usuedit integer DEFAULT 0 NOT NULL,
                dataedit timestamp without time zone DEFAULT '3000-12-31' 
                ) 
            ");
            $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".filtros LIMIT 2");
            $row = pg_num_rows($rs);
            if($row == 0){
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros (id, numapar, codmarca, modelo, tipofiltro, localinst, observ, ativo) VALUES(1, 1, 3, '', 1, 'Harmonização DAO - Sala 008', '', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros (id, numapar, codmarca, modelo, tipofiltro, localinst, observ, ativo) VALUES(2, 2, 2, '', 2, 'Bezerra', '', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros (id, numapar, codmarca, modelo, tipofiltro, localinst, observ, ativo) VALUES(3, 3, 2, '', 3, 'Bezerra', '', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros (id, numapar, codmarca, modelo, tipofiltro, localinst, observ, ativo) VALUES(4, 4, 2, '', 1, '1º andar', '', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros (id, numapar, codmarca, modelo, tipofiltro, localinst, observ, ativo) VALUES(5, 5, 2, '', 4, 'DAF Fin.', '', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros (id, numapar, codmarca, modelo, tipofiltro, localinst, observ, ativo) VALUES(6, 6, 2, 'Pressão', 1, '2º andar A', 'Com a tarja verde no refil - Filtro BAG', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros (id, numapar, codmarca, modelo, tipofiltro, localinst, observ, ativo) VALUES(7, 7, 2, 'Pressão', 1, 'Térreo - Masc', 'Com a tarja verde no refil - Filtro BAG', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros (id, numapar, codmarca, modelo, tipofiltro, localinst, observ, ativo) VALUES(8, 8, 2, 'Refil Purificador', 1, 'Cozinha Eventos Espaço Cultural', '', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros (id, numapar, codmarca, modelo, tipofiltro, localinst, observ, ativo) VALUES(9, 9, 2, 'Avanti', 1, 'André Luiz', '', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros (id, numapar, codmarca, modelo, tipofiltro, localinst, observ, ativo) VALUES(10, 10, 2, 'Avanti', 1, 'Chico Xavier', '', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros (id, numapar, codmarca, modelo, tipofiltro, localinst, observ, ativo) VALUES(11, 11, 2, 'Filtro Industrial', 1, 'Pátio Auta de Souza', '', 1)");
            }

//            pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".filtros_marcas");
            pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".filtros_marcas (
                id SERIAL PRIMARY KEY, 
                descmarca character varying(30), 
                ativo smallint DEFAULT 1 NOT NULL, 
                usuins integer DEFAULT 0 NOT NULL,
                datains timestamp without time zone DEFAULT '3000-12-31',
                usuedit integer DEFAULT 0 NOT NULL,
                dataedit timestamp without time zone DEFAULT '3000-12-31' 
                ) 
            ");
            $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".filtros_marcas LIMIT 2");
            $row = pg_num_rows($rs);
            if($row == 0){
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros_marcas (id, descmarca, ativo) VALUES(1, 'Genérico', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros_marcas (id, descmarca, ativo) VALUES(2, 'IBBL', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros_marcas (id, descmarca, ativo) VALUES(3, 'Colormaq', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros_marcas (id, descmarca, ativo) VALUES(4, 'Esmaltec', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros_marcas (id, descmarca, ativo) VALUES(5, 'Masterfrio', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros_marcas (id, descmarca, ativo) VALUES(6, 'Newmaq', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros_marcas (id, descmarca, ativo) VALUES(7, 'Frisbel', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros_marcas (id, descmarca, ativo) VALUES(8, 'Libell', 1)");
            }
    
//            pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".filtros_tipos");
            pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".filtros_tipos (
                id SERIAL PRIMARY KEY, 
                desctipo character varying(30), 
                ativo smallint DEFAULT 1 NOT NULL, 
                usuins integer DEFAULT 0 NOT NULL,
                datains timestamp without time zone DEFAULT '3000-12-31',
                usuedit integer DEFAULT 0 NOT NULL,
                dataedit timestamp without time zone DEFAULT '3000-12-31' 
                ) 
            ");
            $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".filtros_tipos LIMIT 2");
            $row = pg_num_rows($rs);
            if($row == 0){
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros_tipos (id, desctipo, ativo) VALUES(1, 'Comum', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros_tipos (id, desctipo, ativo) VALUES(2, 'Areia', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros_tipos (id, desctipo, ativo) VALUES(3, 'Carvão Ativado', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros_tipos (id, desctipo, ativo) VALUES(4, 'Quartzo', 1)");
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros_tipos (id, desctipo, ativo) VALUES(5, 'Seixos', 1)");
            }

//            pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".filtros_empr");
            pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".filtros_empr (
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
            $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".filtros_empr LIMIT 2");
            $row = pg_num_rows($rs);
            if($row == 0){
                pg_query($Conec, "INSERT INTO ".$xProj.".filtros_empr (id, descempresa, ender, cep, cidade, uf, ativo) VALUES(1, 'Comércio Local', 'Av. Sobe e Desce e Nunca Aparece, 1001. - Setor de Empresas', '70000-000', 'Brasília', 'DF', 1)");
            }

//-------

        $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'filtros'");
        $rowSis = pg_num_rows($rsSis);
        if($rowSis == 0){
            require_once("../msgErro.php");
            return false;
        }

        $Hoje = date('d/m/Y');
        $Filtro = parEsc("filtros", $Conec, $xProj, $_SESSION["usuarioID"]);
        $FiscFiltro = parEsc("fisc_filtros", $Conec, $xProj, $_SESSION["usuarioID"]);
        $Tema = parEsc("tema", $Conec, $xProj, $_SESSION["usuarioID"]); // Claro(0) Escuro(1)

        $OpMarcas = pg_query($Conec, "SELECT id, descmarca FROM ".$xProj.".filtros_marcas WHERE ativo = 1 ORDER BY descmarca");
        $OpTipos = pg_query($Conec, "SELECT id, desctipo FROM ".$xProj.".filtros_tipos WHERE ativo = 1 ORDER BY desctipo ");

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
        <input type="hidden" id="guardaEdit" value="<?php echo $Filtro; ?>" />
        <input type="hidden" id="guardaFiscal" value="<?php echo $FiscFiltro; ?>" />
        <input type="hidden" id="guardaUsuId" value="<?php echo $_SESSION["usuarioID"]; ?>" />
        <input type="hidden" id="guardaCodMarca" value="0" />
        <input type="hidden" id="guardaPrazo" value = "" />
        <input type="hidden" id="guardaHoje" value = "<?php echo $Hoje; ?>" />
        <input type="hidden" id="guardaAcao" value="<?php echo $Acao; ?>" />

        <!-- div três colunas -->
        <div id="tricoluna0" class="corClara" style="margin: 5px; padding: 10px; border: 2px solid blue; border-radius: 10px; min-height: 50px;">
            <div id="tricoluna1" class="box corClara" style="position: relative; float: left; width: 33%;">
                <img src="imagens/settings.png" height="20px;" id="imgConfig" style="cursor: pointer; padding-right: 20px;" onclick="carregaConfig();" title="Configurar o acesso ao controle de viaturas">
                <input type="button" id="botinserir" class="resetbot fundoAzul2" style="font-size: 80%;" value="Inserir" onclick="insereFiltro();" title="Registrar abasteimento ou manutenção nas viaturas.">
            </div>
            <div id="tricoluna2" class="box corClara" style="position: relative; float: left; width: 33%; text-align: center;">
                <h5>Filtros e Purificadores de Água</h5>
            </div>
            <div id="tricoluna3" class="box corClara" style="position: relative; float: left; width: 33%; text-align: right;">
                <div id="selectTema" style="position: relative; float: left; padding-left: 30px;">
                    <label id="etiqcorFundo" class="etiq" style="color: #6C7AB3; font-size: 80%; padding-left: 10px;">Tema: </label>
                    <input type="radio" name="corFundo" id="corFundo0" value="0" <?php if($Tema == 0){echo 'CHECKED';}; ?> title="Tema claro" onclick="mudaTema(0);" style="cursor: pointer;"><label for="corFundo0" style="cursor: pointer; font-size: 80%;">&nbsp;Claro</label>
                    <input type="radio" name="corFundo" id="corFundo1" value="1" <?php if($Tema == 1){echo 'CHECKED';}; ?> title="Tema escuro" onclick="mudaTema(1);" style="cursor: pointer;"><label for="corFundo1" style="cursor: pointer; font-size: 80%;">&nbsp;Escuro</label>
                </div>
                <label style="padding-left: 10px;"></label>
                <button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="imprFiltros();">PDF</button>
            </div>

            <div id="faixaMensagem" style="display: none; position: relative; margin: 70px; padding: 20px; text-align: center; border: 1px solid; border-radius: 15px;">
                Usuário não cadastrado.
            </div>
        </div>

        <!-- Tabelas -->
        <div id="container5" style="margin-top 10px; width: 70%;"></div>
        <div id="intercolunas" style="width: 1%;"></div> <!-- espaçamento entre colunas  -->
        <div id="container6" style="margin-top 10px; width: 25%;"></div>

        <!-- config para inserir usuários, filtros, empresas... -->
        <div id="relacmodalConfig" class="relacmodal">
            <div class="modal-content-Config">
                <span class="close" onclick="fechaModalConfig();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col" style="margin: 0 auto;"></div>
                        <div class="col"><h6 id="titulomodal" style="text-align: center; color: #666;">Controle dos Filtros e Purificadores</h6></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col" style="margin: 0 auto; text-align: center;"><button id="botimprUsu" class="botpadrred" style="font-size: 70%;" onclick="imprUsuFiltros();">Resumo em PDF</button></div> 
                    </div>
                </div>
                
                <div id="configAdmin"></div> <!-- para superusuários -->

                <table style="margin: 0 auto; width: 86%;">
                    <tr>
                        <td style="vertical-align: top;">
                            <div style="position: relative; float: left; width: 99%; margin-top: 10px; text-align: center; border: 1px solid; border-radius: 10px; background: linear-gradient(180deg, white, #86c1eb);">
                                <div style="margin: 20px; min-width: 200px; padding: 5px; text-align: center; border: 1px solid; border-radius: 15px; background: linear-gradient(180deg, white, #86c1eb);">
                                    <div class='divbot corFundo' onclick='insMarca()' title="Adicionar uma nova marca de filtro/purificador"> Adicionar </div>
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
                <h5 id="titulomodalMarca" style="text-align: center; color: #666;">Nova Marca de filtro/purificador</h5>
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
                <h5 id="titulomodalTipo" style="text-align: center; color: #666;">Novo Tipo de Filtro</h5>
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

        <div id="editaModalFiltros" class="relacmodal">
            <div class="modal-content-relacFiltros corPreta">
                <span class="close" style="font-size: 250%; color: black;" onclick="fechaInsFiltro();">&times;</span>
                <div style="border: 2px solid blue; border-radius: 10px; margin-top: 10px; background: linear-gradient(180deg, white, #87CEEB)">
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 10px;"><label id="titmodaledit">Filtros e Purificadores</label></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td class="etiq">Número: </td>
                            <td colspan="4" style="text-align: left; padding-top: 10px;">
                                <input type="text" id="numfiltro" style="width: 70px; text-align: center; border: 1px solid; border-radius: 5px; font-weight: bold;" placeholder="Número" onkeypress="if(event.keyCode===13){javascript:foco('localinstal');return false;}"/>
                                <label class="etiq">Local: </label>
                                <input type="text" id="localinstal" maxlength="200" style="width: 560px;text-align: left; border: 1px solid; border-radius: 5px;" placeholder="Local de instalação" onkeypress="if(event.keyCode===13){javascript:foco('modelofiltro');return false;}"/>
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
                                <input type="text" id="modelofiltro" maxlength="30" style="width: 250px; text-align: left; border: 1px solid; border-radius: 5px;" placeholder="Modelo" onkeypress="if(event.keyCode===13){javascript:foco('dataTrocaFiltro');return false;}"/>

                                <label class="etiq">Tipo filtro: </label>
                                <select id="selecTipo" style="max-width: 150px;" onchange="modif();" title="Selecione um tipo de filtro.">
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
                                <textarea id="observfiltro" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px; padding: 2px;" rows="2" cols="58" title="Observações" onchange="modif();"></textarea>
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
                            <td class="etiq aCentro"></td>
                            <td class="etiq aCentro"></td>
                            <td class="etiq aCentro"></td>
                            <td colspan="2" class="etiq aCentro" style="border-inline: 1px solid; border-top: 1px solid;">Elemento Filtrante</td>
                        </tr>
                        <tr>
                            <td class="etiq aEsq">Data de troca:</td>
                            <td class="etiq aEsq">Prazo de troca:</td>
                            <td class="etiq aEsq">Data de Vencimento:</td>
                            <td class="etiq aCentro" style="border-left: 1px solid;">Notificação?</td>
                            <td class="etiq aCentro" style="border-right: 1px solid;">Antecedência</td>
                        </tr>

                        <tr>
                            <!-- on change nas datas com o datepicker trava a máquina -->
                            <td style="text-align: center;">
                                <input type="text" id="dataTrocaFiltro" width="150" onclick="$datepicker.open();" onchange="calcVenc();" style="height: 30px; text-align: center; border: 1px solid; border-radius: 5px;" placeholder="Data" onkeypress="if(event.keyCode===13){javascript:foco('dataVencim');return false;}"/>
                            </td>
                            <td style="vertical-align: top;">
                            <select id="selecPrazo" style="min-width: 50px;" onchange="calcPrazo();" title="Selecione um prazo para a troca do elemento filtrante.">
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
                            <td colspan="2" style="text-align: center; border-left: 1px solid; border-bottom: 1px solid; border-right: 1px solid; border-color: #9C9C9C;">
                                <label class="etiq">Dia aviso:</label>
                                <input type="text" id="dataAviso" style="text-align: center; border: 1px solid; border-radius: 5px; width: 110px;" placeholder="Data Aviso" onkeypress="if(event.keyCode===13){javascript:foco('numfiltro');return false;}"/>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td><button class="botpadrred" style="font-size: 60%;" id="botApagaLanc" onclick="apagarFiltro();">Apagar</button></td>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"><button class="botpadrblue" onclick="salvaEditaFiltro();">Salvar</button></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 5px;"><div id="mensagem" style="color: red; font-weight: bold;"></div></td>
                        <tr>
                    </table>
                    <br>
                </div>    
            </div>
        </div> <!-- Fim Modal-->

        <div id="carregaTema"></div> <!-- carrega a pág modulos/config/carTema.php - onde está a função mudaTema() -->

    </body>
</html>