<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Viaturas</title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="class/gijgo/css/gijgo.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="comp/js/jquery.mask.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script> 
        <script src="class/gijgo/js/gijgo.js"></script>
        <script src="class/gijgo/js/messages/messages.pt-br.js"></script>
        <style>
           .modal-content-Insere{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 70%;
                max-width: 850px;
            }
            .modal-content-Config{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 65%;
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
            .modal-content-InsTipo{
                background: linear-gradient(180deg, white, #0099FF);
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 55%;
                max-width: 900px;
            }
           .quadro{
                position: relative; float: left; margin: 5px; width: 95%; border: 1px solid; border-radius: 10px; padding: 2px; padding-top: 5px;
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
                $('#carregaTema').load('modulos/config/carTema.php?carpag=viaturas');
                document.getElementById("botinserir").disabled = true; // botão de inserir compra   || parseInt(document.getElementById("fiscal").value) === 1
                document.getElementById("botimpr").disabled = true;
                document.getElementById("imgCombustConfig").style.visibility = "hidden";
                document.getElementById("botApagaEditTipo").style.visibility = "hidden";
                document.getElementById("botApagaEditEmpr").style.visibility = "hidden";
                if(parseInt(document.getElementById("editor").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){
                    document.getElementById("botinserir").disabled = false;
                    document.getElementById("botimpr").disabled = false;
                    document.getElementById("imgCombustConfig").style.visibility = "visible";
                }
                $("#container5").load("modulos/viaturas/jViatura1.php?acao=todos");
                $("#container6").load("modulos/viaturas/jViatura2.php?acao=todos");
                $('#datacompra').datepicker({ uiLibrary: 'bootstrap3', locale: 'pt-br', format: 'dd/mm/yyyy' });

                if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                    document.getElementById("apagaRegCombust").style.display = "block";
                }

                $("#configselecSolicitante").change(function(){
                    if(document.getElementById("configselecSolicitante").value == ""){
                        document.getElementById("configcpfsolicitante").value = "";
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=buscausuario&codigo="+document.getElementById("configselecSolicitante").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configcpfsolicitante").value = format_CnpjCpf(Resp.cpf);
                                        if(parseInt(Resp.viatura) === 1){
                                            document.getElementById("registroViatura").checked = true;
                                        }else{
                                            document.getElementById("registroViatura").checked = false;
                                        }
                                        if(parseInt(Resp.fiscviatura) === 1){
                                            document.getElementById("fiscalViatura").checked = true;
                                        }else{
                                            document.getElementById("fiscalViatura").checked = false;
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
                });
                $("#configcpfsolicitante").change(function(){
                    document.getElementById("configselecSolicitante").value = "";
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=buscacpfusuario&cpf="+encodeURIComponent(document.getElementById("configcpfsolicitante").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configselecSolicitante").value = Resp.PosCod;
                                        if(parseInt(Resp.viatura) === 1){
                                            document.getElementById("registroViatura").checked = true;
                                        }else{
                                            document.getElementById("registroViatura").checked = false;
                                        }
                                        if(parseInt(Resp.fiscviatura) === 1){
                                            document.getElementById("fiscalViatura").checked = true;
                                        }else{
                                            document.getElementById("fiscalViatura").checked = false;
                                        }
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("registroViatura").checked = false;
                                        document.getElementById("fiscalViatura").checked = false;
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

                $("#odometro").change(function(){
                    if(document.getElementById("relviaturas").value == ""){
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=buscaodometro&valor="+document.getElementById("odometro").value+"&viatura="+document.getElementById("relviaturas").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("odometro").focus();
                                        $('#mensagemLeitura').fadeIn("slow");
                                        document.getElementById("mensagemLeitura").innerHTML = "Valor menor";
                                        $('#mensagemLeitura').fadeOut(5000);
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

                $("#valorcompra").mask("99999999,99");
                $("#valormanut").mask("99999999,99");
                $("#volumecompra").mask("9999999,99");

            }); // Fim ready

            function carregaModal(Cod){
                document.getElementById("guardacod").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=buscaDataCombust&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("reltipocombust").value = Resp.tipocomb;
                                    document.getElementById("datacompra").value = Resp.data;
                                    document.getElementById("relviaturas").value = Resp.codveic;
                                    document.getElementById("volumecompra").value = Resp.volume;
                                    document.getElementById("odometro").value = Resp.odometro;
                                    document.getElementById("precolitro").value = Resp.precolitro;
                                    document.getElementById("obsviatura").value = Resp.obs;
                                    document.getElementById("guardaManut").value = Resp.coddespesa;
                                    document.getElementById("reltipomanut").value = Resp.tipomanut;
                                    if(parseInt(Resp.coddespesa) === 1){ // abastecimento
                                        document.getElementById("manutins1").checked = true;
                                        document.getElementById("valorcompra").value = Resp.custo;
                                        document.getElementById("relacmodalInsManut").style.display = "none";
                                        document.getElementById("relacmodalInsAbast").style.display = "block";
                                    }else{
                                        document.getElementById("manutins2").checked = true;
                                        document.getElementById("valormanut").value = Resp.custo;
                                        document.getElementById("relacmodalInsManut").style.display = "block";
                                        document.getElementById("relacmodalInsAbast").style.display = "none";
                                    }
                                    document.getElementById("relacmodalInsere").style.display = "block";
                                    if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                                        document.getElementById("apagaRegCombust").style.display = "block";
                                    }else{
                                        document.getElementById("apagaRegCombust").style.display = "none";
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function insereCompra(){
                document.getElementById("manutins1").checked = true;
                document.getElementById("relacmodalInsAbast").style.display = "block";
                document.getElementById("relacmodalInsManut").style.display = "none";
                document.getElementById("reltipocombust").value = "";
                document.getElementById("datacompra").value = "";
                document.getElementById("relviaturas").value = "";
                document.getElementById("valorcompra").value = "";
                document.getElementById("valormanut").value = "";
                document.getElementById("volumecompra").value = "";
                document.getElementById("odometro").value = "";
                document.getElementById("precolitro").value = "";
                document.getElementById("obsviatura").value = "";
                document.getElementById("relacmodalInsere").style.display = "block";
                document.getElementById("apagaRegCombust").style.display = "none";
                document.getElementById("odometro").focus();
            }

            function salvaModal(){
                if(parseInt(document.getElementById("mudou").value) === 0){
                    document.getElementById("relacmodalInsere").style.display = "none";
                    return false;
                }
                if(document.getElementById("relviaturas").value == ""){
                    document.getElementById("relviaturas").focus();
                    $('#mensagemLeitura').fadeIn("slow");
                    document.getElementById("mensagemLeitura").innerHTML = "Selecione uma viatura";
                    $('#mensagemLeitura').fadeOut(3000);
                    return false;
                }
                if(parseInt(document.getElementById("guardaManut").value) === 1){ // abastecimento
                    if(document.getElementById("reltipocombust").value == ""){
                        document.getElementById("reltipocombust").focus();
                        $('#mensagemLeitura').fadeIn("slow");
                        document.getElementById("mensagemLeitura").innerHTML = "Selecione um tipo de combustível";
                        $('#mensagemLeitura').fadeOut(3000);
                        return false;
                    }
                }
                if(parseInt(document.getElementById("guardaManut").value) === 2){ // manutenção
                    if(document.getElementById("reltipomanut").value == ""){
                        document.getElementById("reltipomanut").focus();
                        $('#mensagemLeitura').fadeIn("slow");
                        document.getElementById("mensagemLeitura").innerHTML = "Selecione um tipo de manutenção";
                        $('#mensagemLeitura').fadeOut(3000);
                        return false;
                    }
                }
                if(parseInt(document.getElementById("guardaManut").value) === 1){ // abastecimento
                    let Valor = document.getElementById("valorcompra").value;
                    if(Valor.includes(",") == false){
                        document.getElementById("valorcompra").focus();
                        $('#mensagemLeitura').fadeIn("slow");
                        document.getElementById("mensagemLeitura").innerHTML = "Insira o valor da compra, com decimais";
                        $('#mensagemLeitura').fadeOut(3000);
                        return false;
                    }
                    if(document.getElementById("valorcompra").value == ""){
                        document.getElementById("valorcompra").focus();
                        $('#mensagemLeitura').fadeIn("slow");
                        document.getElementById("mensagemLeitura").innerHTML = "Insira o valor da compra, com decimais";
                        $('#mensagemLeitura').fadeOut(3000);
                        return false;
                    }
                }
                if(parseInt(document.getElementById("guardaManut").value) === 2){ // Manutenção
                    let Valor = document.getElementById("valormanut").value;
                    if(Valor.includes(",") == false){
                        document.getElementById("valormanut").focus();
                        $('#mensagemLeitura').fadeIn("slow");
                        document.getElementById("mensagemLeitura").innerHTML = "Insira o valor da nota, com decimais";
                        $('#mensagemLeitura').fadeOut(3000);
                        return false;
                    }
                    if(document.getElementById("valormanut").value == ""){
                        document.getElementById("valormanut").focus();
                        $('#mensagemLeitura').fadeIn("slow");
                        document.getElementById("mensagemLeitura").innerHTML = "Insira o valor da nota, com decimais";
                        $('#mensagemLeitura').fadeOut(3000);
                        return false;
                    }
                }

                if(document.getElementById("datacompra").value == ""){
                    document.getElementById("datacompra").focus();
                    $('#mensagemLeitura').fadeIn("slow");
                    document.getElementById("mensagemLeitura").innerHTML = "Insira a data da compra";
                    $('#mensagemLeitura').fadeOut(3000);
                    return false;
                }
                Tam = document.getElementById("datacompra").value;
                if(Tam.length < 10){
                    document.getElementById("datacompra").value = "";
                    document.getElementById("datacompra").focus();
                    return false;
                }
                if(parseInt(document.getElementById("guardaManut").value) === 1){ // abastecimento
                    let Volume = document.getElementById("volumecompra").value;
                    if(document.getElementById("volumecompra").value == "" || Volume.length <3){
                        document.getElementById("volumecompra").focus();
                        $('#mensagemLeitura').fadeIn("slow");
                        document.getElementById("mensagemLeitura").innerHTML = "Insira o volume em litros, com decimais";
                        $('#mensagemLeitura').fadeOut(3000);
                        return false;
                    }
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=salvaCompraComb&codigo="+document.getElementById("guardacod").value 
                    +"&tipodespesa="+document.getElementById("guardaManut").value
                    +"&tipocombust="+document.getElementById("reltipocombust").value
                    +"&tipoviat="+document.getElementById("relviaturas").value
                    +"&tipomanut="+document.getElementById("reltipomanut").value
                    +"&odometro="+document.getElementById("odometro").value
                    +"&datacompra="+encodeURIComponent(document.getElementById("datacompra").value) 
                    +"&valorcompra="+document.getElementById("valorcompra").value
                    +"&valormanut="+document.getElementById("valormanut").value
                    +"&obs="+encodeURIComponent(document.getElementById("obsviatura").value) 
                    +"&volumecompra="+document.getElementById("volumecompra").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    $("#container5").load("modulos/viaturas/jViatura1.php?acao=todos");
                                    $("#container6").load("modulos/viaturas/jViatura2.php?acao=todos");
                                    document.getElementById("relacmodalInsere").style.display = "none";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function apagaModalViatura(){
                if(document.getElementById("datacompra").value != ""){
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma apagar este lançamento?',
                    autoClose: 'Não|20000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=apagareg&codigo="+document.getElementById("guardacod").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $("#container5").load("modulos/viaturas/jViatura1.php?acao=todos");
                                                $("#container6").load("modulos/viaturas/jViatura2.php?acao=todos");
                                                document.getElementById("relacmodalInsere").style.display = "none";
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
            }

            function marcaCheckBox(obj, Campo){
                if(obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                if(Valor == 0){ // tirando seu próprio acesso
                    if(parseInt(document.getElementById("configselecSolicitante").value) === parseInt(document.getElementById("guardaUsuId").value)){
                        if(parseInt(document.getElementById("UsuAdm").value) < 7){ // superusuário
                            $.confirm({
                                title: 'Alerta!',
                                content: 'Você perderá o acesso a este módulo no próximo login.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                        }
                    }
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
                    ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=configMarcaCheckBox&codigo="+document.getElementById("configselecSolicitante").value
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
                                            content: 'Não restaria outro marcado para gerenciar o combustível.',
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

            function insTipo(){
                document.getElementById("guardaCodTipo").value = "0";
                document.getElementById("editNomeTipo").value = "";
                document.getElementById("botApagaEditTipo").style.visibility = "hidden";
                document.getElementById("titulomodalTipo").innerHTML = "Nome da nova viatura";
                document.getElementById("relacEditTipo").style.display = "block";
                document.getElementById("editNomeTipo").focus();
            }

            function insCombust(){
                document.getElementById("guardaCodTipo").value = "0";
                document.getElementById("editNomeEmpr").value = "";
                document.getElementById("botApagaEditEmpr").style.visibility = "hidden";
                document.getElementById("titulomodalEmpr").innerHTML = "Novo tipo de combustível";
                document.getElementById("relacEditCombust").style.display = "block";
                document.getElementById("editNomeEmpr").focus();
            }

            function insManut(){
                document.getElementById("guardaCodTipo").value = "0";
                document.getElementById("editNomeManut").value = "";
                document.getElementById("botApagaEditManut").style.visibility = "hidden";
                document.getElementById("titulomodalManut").innerHTML = "Novo tipo de manutenção";
                document.getElementById("relacEditManut").style.display = "block";
                document.getElementById("editNomeManut").focus();
            }

            function carregaTipos(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=buscareltipos", true);
                     ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                RespT = eval("(" + ajax.responseText + ")");
                                var optionsT = "";  //Cria array
                                optionsT += "<option value='0'></option>";
                                $.each(RespT, function(key, RespT){
                                    optionsT += '<option value="' + RespT.CodE + '">'+RespT.TipoE + '</option>';
                                });
                                $("#relviaturas").html(optionsT); // recarrega as opções
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function carregaComb(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=buscarelcomb", true);
                     ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                RespT = eval("(" + ajax.responseText + ")");
                                var optionsT = "";  //Cria array
                                optionsT += "<option value='0'></option>";
                                $.each(RespT, function(key, RespT){
                                    optionsT += '<option value="' + RespT.CodE + '">'+RespT.TipoE + '</option>';
                                });
                                $("#reltipocombust").html(optionsT); // recarrega as opções
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function carregaManut(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=buscarelmanut", true);
                     ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                RespT = eval("(" + ajax.responseText + ")");
                                var optionsT = "";  //Cria array
                                optionsT += "<option value='0'></option>";
                                $.each(RespT, function(key, RespT){
                                    optionsT += '<option value="' + RespT.CodE + '">'+RespT.TipoE + '</option>';
                                });
                                $("#reltipomanut").html(optionsT); // recarrega as opções
                            }
                        }
                    };
                    ajax.send(null);
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
                                ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=apagaTipo&codigo="+document.getElementById("guardaCodTipo").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $("#configTipos").load("modulos/viaturas/jTipoViat.php");
                                                carregaTipos(); // recarrega relação
                                                $("#container5").load("modulos/viaturas/jViatura1.php?acao=todos");
                                                $("#container6").load("modulos/viaturas/jViatura2.php?acao=todos");
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

            function apagaCombust(){
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma apagar este tipo?<br>Os lançamentos com este tipo serão perdidos.<br>Continua?',
                    autoClose: 'Não|20000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=apagaComb&codigo="+document.getElementById("guardaCodTipo").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $("#configComb").load("modulos/viaturas/jTipoComb.php");
                                                carregaComb(); // recarrega relação
                                                $("#container5").load("modulos/viaturas/jViatura1.php?acao=todos");
                                                $("#container6").load("modulos/viaturas/jViatura2.php?acao=todos");
                                                document.getElementById("relacEditCombust").style.display = "none";
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

            function apagaManut(){
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma apagar este tipo?<br>Os lançamentos com este tipo serão perdidos.<br>Continua?',
                    autoClose: 'Não|20000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=apagaManut&codigo="+document.getElementById("guardaCodTipo").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $("#configManut").load("modulos/viaturas/jTipoManut.php");
                                                carregaManut(); // recarrega relação
                                                $("#container5").load("modulos/viaturas/jViatura1.php?acao=todos");
                                                $("#container6").load("modulos/viaturas/jViatura2.php?acao=todos");
                                                document.getElementById("relacEditManut").style.display = "none";
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

            function editaTipo(Cod){
                document.getElementById("guardaCodTipo").value = Cod;
                if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                    document.getElementById("botApagaEditTipo").style.visibility = "visible";
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=buscatipo&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editNomeTipo").value = Resp.nome;
                                    document.getElementById("titulomodalTipo").innerHTML = "Edita tipo de viatura";
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

            function editaComb(Cod){
                document.getElementById("guardaCodTipo").value = Cod;
                if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                    document.getElementById("botApagaEditEmpr").style.visibility = "visible";
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=buscacombust&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editNomeEmpr").value = Resp.nome;
                                    document.getElementById("titulomodalEmpr").innerHTML = "Edita tipo de combustível";
                                    document.getElementById("relacEditCombust").style.display = "block";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function editaManut(Cod){
                document.getElementById("guardaCodTipo").value = Cod;
                if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                    document.getElementById("botApagaEditManut").style.visibility = "visible";
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=buscamanut&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editNomeManut").value = Resp.nome;
                                    document.getElementById("titulomodalManut").innerHTML = "Edita tipo de manutenção";
                                    document.getElementById("relacEditManut").style.display = "block";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaEditTipo(){ // viatura
                if(document.getElementById("editNomeTipo").value != ""){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=salvanovotipo&codigo="+document.getElementById("guardaCodTipo").value 
                        +"&nometipo="+encodeURIComponent(document.getElementById("editNomeTipo").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("relacEditTipo").style.display = "none";
                                        $("#configTipos").load("modulos/viaturas/jTipoViat.php");
                                        carregaTipos(); // recarrega relação
                                        $("#container5").load("modulos/viaturas/jViatura1.php?acao=todos");
                                        $("#container6").load("modulos/viaturas/jViatura2.php?acao=todos");
                                        
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

            function salvaEditComb(){
                if(document.getElementById("editNomeEmpr").value != ""){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=salvanovoComb&codigo="+document.getElementById("guardaCodTipo").value 
                        +"&nomeempr="+encodeURIComponent(document.getElementById("editNomeEmpr").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("relacEditCombust").style.display = "none";
                                        $("#configComb").load("modulos/viaturas/jTipoComb.php");
                                        carregaComb(); // recarrega relação combustíveis
//                                        $("#container5").load("modulos/viaturas/jViatura1.php?acao=todos");
                                        $("#container6").load("modulos/viaturas/jViatura2.php?acao=todos");
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("relacEditCombust").style.display = "none";
                }
            }

            function salvaEditManut(){
                if(document.getElementById("editNomeManut").value != ""){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/viaturas/salvaViatura.php?acao=salvanovoManut&codigo="+document.getElementById("guardaCodTipo").value 
                        +"&nomemanut="+encodeURIComponent(document.getElementById("editNomeManut").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("relacEditManut").style.display = "none";
                                        $("#configManut").load("modulos/viaturas/jTipoManut.php");
                                        carregaManut(); // recarrega relação
//                                        $("#container5").load("modulos/viaturas/jViatura1.php?acao=todos");
                                        $("#container6").load("modulos/viaturas/jViatura2.php?acao=todos");
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("relacEditCombust").style.display = "none";
                }
            }

            function salvaManut(Valor){
                document.getElementById("guardaManut").value = Valor;
                document.getElementById("mudou").value = "1";
                if(parseInt(Valor) === 2){
                    document.getElementById("relacmodalInsAbast").style.display = "none";
                    document.getElementById("relacmodalInsManut").style.display = "block";
                }else{
                    document.getElementById("relacmodalInsManut").style.display = "none";
                    document.getElementById("relacmodalInsAbast").style.display = "block";
                }
            }

            function carregaConfig(){
                $("#configTipos").load("modulos/viaturas/jTipoViat.php");
                $("#configComb").load("modulos/viaturas/jTipoComb.php");
                $("#configManut").load("modulos/viaturas/jTipoManut.php");
                document.getElementById("relacmodalConfig").style.display = "block";
            }
            function fechaConfig(){
                document.getElementById("relacmodalConfig").style.display = "none";
            }
            var formatter = new Intl.NumberFormat('pt-BR', {
                    minimumFractionDigits: 2
                }); 

            function modifValor(){
                document.getElementById("mudou").value = "1";
                let text1 = document.getElementById("valorcompra").value;
                let Valor = text1.replace(/,/g, ""); // Tira os decimais
                let text2 = document.getElementById("volumecompra").value;
                let Volume = text2.replace(/,/g, "");
                if(document.getElementById("volumecompra").value != "" && Volume.length < 3){
                    document.getElementById("precolitro").value = formatter.format((parseFloat(Valor)/parseFloat(Volume)));
                }
            }
            function modifVolume(){
                document.getElementById("mudou").value = "1";
                let text1 = document.getElementById("valorcompra").value;
                let Valor = text1.replace(/,/g, ""); // Tira os decimais
                let text2 = document.getElementById("volumecompra").value;
                let Volume = text2.replace(/,/g, "");
                document.getElementById("precolitro").value = formatter.format((parseFloat(Valor)/parseFloat(Volume)));
            }
            function imprUsuComb(){
                window.open("modulos/viaturas/imprUsuViat.php?acao=listaUsuarios", "usuViaturas");
            }
            function fechaModal(){
                document.getElementById("guardacod").value = 0;
                document.getElementById("relacmodalInsere").style.display = "none";
            }
            function fechaModalConfig(){
                document.getElementById("relacmodalConfig").style.display = "none";
            }
            function fechaEditTipo(){
                document.getElementById("relacEditTipo").style.display = "none";
            }
            function fechaEditComb(){
                document.getElementById("relacEditCombust").style.display = "none";
            }
            function fechaEditManut(){
                document.getElementById("relacEditManut").style.display = "none";
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
        </script>
    </head>
    <body class="corClara" onbeforeunload="return mudaTema(0)">
        <?php
            if(!$Conec){
                echo "Sem contato com os arquivos";
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

//Provisório
//            pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".viaturas");
            $rs0 = pg_query($Conec, "SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE table_schema = 'cesb' And table_name = 'viaturas'");
            $row0 = pg_num_rows($rs0);
            if($row0 == 0){
                pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".viaturas (
                    id SERIAL PRIMARY KEY, 
                    datacompra date DEFAULT '3000-12-31', 
                    coddespesa smallint DEFAULT 1 NOT NULL, 
                    codveiculo smallint DEFAULT 0 NOT NULL, 
                    tipocomb int DEFAULT 0 NOT NULL, 
                    volume integer NOT NULL DEFAULT 0,  
                    custo integer NOT NULL DEFAULT 0, 
                    tipomanut integer NOT NULL DEFAULT 0, 
                    odometro integer NOT NULL DEFAULT 0, 
                    observ text, 
                    ativo smallint DEFAULT 1 NOT NULL, 
                    usuins bigint DEFAULT 0 NOT NULL,
                    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                    usuedit bigint DEFAULT 0 NOT NULL,
                    dataedit timestamp without time zone DEFAULT '3000-12-31' ) 
                ");
                pg_query($Conec, "INSERT INTO ".$xProj.".viaturas (id, datacompra, coddespesa, codveiculo, tipocomb, volume, custo, odometro, tipomanut, usuins) VALUES(1, '2025/02/10', 1, 1, 1, 2550, 17030, 5000, 0, 3 )");
                pg_query($Conec, "INSERT INTO ".$xProj.".viaturas (id, datacompra, coddespesa, codveiculo, tipocomb, volume, custo, odometro, tipomanut, usuins) VALUES(2, '2025/02/20', 1, 2, 1, 4550, 30000, 6500, 0, 3 )");
                pg_query($Conec, "INSERT INTO ".$xProj.".viaturas (id, datacompra, coddespesa, codveiculo, tipocomb, volume, custo, odometro, tipomanut, usuins) VALUES(3, '2025/02/20', 1, 2, 3, 9100, 60000, 8600, 0, 3 )");
                pg_query($Conec, "INSERT INTO ".$xProj.".viaturas (id, datacompra, coddespesa, codveiculo, tipocomb, volume, custo, odometro, tipomanut, usuins) VALUES(4, '2025/02/25', 2, 2, 0, 0, 55000, 5500, 1, 3 )");
                pg_query($Conec, "INSERT INTO ".$xProj.".viaturas (id, datacompra, coddespesa, codveiculo, tipocomb, volume, custo, odometro, tipomanut, usuins) VALUES(5, '2025/02/27', 2, 2, 0, 0, 100000, 5700, 2, 3 )");
            }
//                    volume double precision, 
//                    custo double precision, 

//            pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".viaturas_tipo");
            $rs1 = pg_query($Conec, "SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE table_schema = 'cesb' And table_name = 'viaturas_tipo'");
            $row1 = pg_num_rows($rs1);
            if($row1 == 0){
                pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".viaturas_tipo (
                    id SERIAL PRIMARY KEY, 
                    desc_viatura VARCHAR(50),
                    tipo_combust smallint DEFAULT 0 NOT NULL, 
                    media_consumo double precision,
                    ativo smallint DEFAULT 1 NOT NULL, 
                    usuins bigint DEFAULT 0 NOT NULL,
                    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                    usuedit bigint DEFAULT 0 NOT NULL,
                    dataedit timestamp without time zone DEFAULT '3000-12-31' ) 
                ");
                pg_query($Conec, "INSERT INTO ".$xProj.".viaturas_tipo (id, desc_viatura, tipo_combust) VALUES(1, 'Volkswagen Delivery', 3 )");
                pg_query($Conec, "INSERT INTO ".$xProj.".viaturas_tipo (id, desc_viatura, tipo_combust) VALUES(2, 'Honda Civic', 2 )");
            }

//            pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".viaturas_comb");
            $rs2 = pg_query($Conec, "SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE table_schema = 'cesb' And table_name = 'viaturas_comb'");
            $row2 = pg_num_rows($rs2);
            if($row2 == 0){
                pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".viaturas_comb (
                    id SERIAL PRIMARY KEY, 
                    desc_combust VARCHAR(50),
                    ativo smallint DEFAULT 1 NOT NULL, 
                    usuins bigint DEFAULT 0 NOT NULL,
                    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                    usuedit bigint DEFAULT 0 NOT NULL,
                    dataedit timestamp without time zone DEFAULT '3000-12-31' ) 
                ");
                pg_query($Conec, "INSERT INTO ".$xProj.".viaturas_comb (id, desc_combust) VALUES(1, 'Álcool')");
                pg_query($Conec, "INSERT INTO ".$xProj.".viaturas_comb (id, desc_combust) VALUES(2, 'Diesel')");
                pg_query($Conec, "INSERT INTO ".$xProj.".viaturas_comb (id, desc_combust) VALUES(3, 'Gasolina')");
                pg_query($Conec, "INSERT INTO ".$xProj.".viaturas_comb (id, desc_combust) VALUES(4, 'GNV')");
            }

//            pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".viaturas_manut");
            $rs2 = pg_query($Conec, "SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE table_schema = 'cesb' And table_name = 'viaturas_manut'");
            $row2 = pg_num_rows($rs2);
            if($row2 == 0){
                pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".viaturas_manut (
                    id SERIAL PRIMARY KEY, 
                    desc_manut VARCHAR(50),
                    ativo smallint DEFAULT 1 NOT NULL, 
                    usuins bigint DEFAULT 0 NOT NULL,
                    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                    usuedit bigint DEFAULT 0 NOT NULL,
                    dataedit timestamp without time zone DEFAULT '3000-12-31' ) 
                ");
                pg_query($Conec, "INSERT INTO ".$xProj.".viaturas_manut (id, desc_manut) VALUES(1, 'Corretiva')");
                pg_query($Conec, "INSERT INTO ".$xProj.".viaturas_manut (id, desc_manut) VALUES(2, 'Preventiva')");
            }
//--------------

        $Viat = parEsc("viatura", $Conec, $xProj, $_SESSION["usuarioID"]);
        $FiscViat = parEsc("fisc_viat", $Conec, $xProj, $_SESSION["usuarioID"]); // fiscal
        $Tema = parEsc("tema", $Conec, $xProj, $_SESSION["usuarioID"]); // Claro(0) Escuro(1)

        $rsTipos = pg_query($Conec, "SELECT id, desc_combust FROM ".$xProj.".viaturas_comb WHERE ativo = 1 ORDER BY desc_combust");
        $rsViat = pg_query($Conec, "SELECT id, desc_viatura FROM ".$xProj.".viaturas_tipo WHERE ativo = 1 ORDER BY desc_viatura");
        $rsManut = pg_query($Conec, "SELECT id, desc_manut FROM ".$xProj.".viaturas_manut WHERE ativo = 1 ORDER BY desc_manut");
        $OpConfig = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");

        ?>
        <input type="hidden" id="mudou" value="0" />
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>" />
        <input type="hidden" id="guardaUsuId" value="<?php echo $_SESSION["usuarioID"]; ?>" />
        <input type="hidden" id="editor" value="<?php echo $Viat; ?>" />
        <input type="hidden" id="fiscal" value="<?php echo $FiscViat; ?>" />
        <input type="hidden" id="guardaCodTipo" value="0" />
        <input type="hidden" id="guardaCodEmpr" value="0" />
        <input type="hidden" id="guardaManut" value="1" />

        <div id="tricoluna0" class="corClara" style="margin: 5px; padding: 10px; border: 1px solid; border-radius: 10px; min-height: 82px;">
            <div id="tricoluna1" class="box corClara" style="position: relative; float: left; width: 33%;">
            <img src="imagens/settings.png" height="20px;" id="imgCombustConfig" style="cursor: pointer; padding-right: 20px;" onclick="carregaConfig();" title="Configurar o acesso ao controle de viaturas">
                <input type="button" id="botinserir" class="resetbot fundoAzul2" style="font-size: 80%;" value="Inserir" onclick="insereCompra();" title="Registrar abasteimento ou manutenção nas viaturas.">
            </div>
            <div id="tricoluna2" class="box corClara" style="position: relative; float: left; width: 33%; text-align: center;">
                <h5>Controle de Viaturas</h5>
            </div>
            <div id="tricoluna3" class="box corClara" style="position: relative; float: left; width: 33%; text-align: right;">
                <label id="etiqcorFundo" class="etiqAzul" style="font-size: 80%; padding-left: 10px;">Tema: </label>
                <input type="radio" name="corFundo" id="corFundo0" value="0" <?php if($Tema == 0){echo 'CHECKED';}; ?> title="Tema claro" onclick="mudaTema(0);"><label for="corFundo0" style="font-size: 80%;">&nbsp;Claro</label>
                <input type="radio" name="corFundo" id="corFundo1" value="1" <?php if($Tema == 1){echo 'CHECKED';}; ?> title="Tema escuro" onclick="mudaTema(1);"><label for="corFundo1" style="font-size: 80%;">&nbsp;Escuro</label>
                <label style="padding-left: 30px;"></label>
                <button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="imprCombust();">PDF</button>
            </div>
            <div id="faixaMensagem" style="display: none; position: relative; margin: 70px; padding: 20px; text-align: center;">
                <br><br><br>Usuário não cadastrado.
            </div>
            <div class="row" style="margin: 0 auto; width: 99%;">
                <div id="container5" class="col quadro" style="margin: 0 auto; width: 100%;"></div> <!-- quadro -->
                <div id="intercolunas" class="col-1" style="width: 1%;"></div> <!-- espaçamento entre colunas  -->
                <div id="container6" class="col quadro" style="margin: 0 auto; width: 100%;"></div> <!-- quadro -->
            </div> <!-- row  -->
        </div>

        <div id="carregaTema"><
    </div> <!-- carrega a pág modulos/config/carTema.php - onde está a função mudaTema() -->

        <!-- div para edição/inserção compra  -->
        <div id="relacmodalInsere" class="relacmodal">
            <div class="modal-content-Insere">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h5 id="titulomodal" style="text-align: center; color: #666;">Despesas com Viaturas</h5>
                <div id="subtitulomodal" style="text-align: center; color: red;"></div>

                <div id="itensmanut" style="text-align: center;">
                    <label class="etiqAzul" style="font-size: 90%; padding-left: 5px;">Conta: </label>
                    <input type="radio" name="manutins" id="manutins1" value="1" onclick="salvaManut(value);"><label for="manutins1" class="etiqAzul" style="font-size: 90%; padding-left: 3px;"> Abastecimento</label>
                    <input type="radio" name="manutins" id="manutins2" value="2" onclick="salvaManut(value);"><label for="manutins2" class="etiqAzul" style="font-size: 90%; padding-left: 3px;"> Manutenção</label>
                </div>
                <div style="margin: 3px; padding: 3px; border: 1px solid; border-radius: 10px;">
                <table style="margin: 0 auto; width: 95%;">
                    <tr>
                        <td class="etiq aDir">Data: </td>
                        <td><input type="text" id="datacompra" valor="" width="150" onchange="modif();" style="text-align: center; border: 1px solid; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('valorcompra');return false;}"></td>
                        <td class="etiq aDir">Viatura: </td>
                        <td>
                            <select id="relviaturas" onchange="modif();" style="font-size: .9rem; width: 100%;" title="Selecione uma viatura.">
                                <option value=""></option>
                                    <?php 
                                    if($rsViat){
                                        while ($Opcoes = pg_fetch_row($rsViat)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                        <?php 
                                        }
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="etiq aDir">Odômetro: </td>
                        <td><input type="text" id="odometro" valor="" onchange="modif();" style="width: 100px; text-align: center; border: 1px solid; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('datacompra');return false;}"><label class="etiqAzul" style="padding-left: 2px;"> Km</label></td>
                    </tr>
                </table>
                </div>

                <div id="relacmodalInsAbast" style="margin: 3px; padding: 3px; border: 1px solid; border-radius: 10px; background: linear-gradient(180deg, white,rgb(226, 207, 214))">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td class="etiq aDir">Tipo de Combustível: </td>
                            <td>
                                <select id="reltipocombust" onchange="modif();" style="font-size: .9rem; width: 90%;" title="Selecione um tipo de combustível.">
                                    <option value=""></option>
                                    <?php 
                                    if($rsTipos){
                                        while ($Opcoes = pg_fetch_row($rsTipos)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="etiq aDir" style="min-width: 50px;">Valor da Nota R$: </td>
                            <td><input type="text" id="valorcompra" valor="" onchange="modifValor();" style="width: 100px; text-align: center; border: 1px solid; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('volumecompra');return false;}"></td>
                            <td class="etiq aDir" style="min-width: 50px;">Volume: </td>
                            <td><input type="text" id="volumecompra" valor="" onchange="modifVolume();" style="width: 100px; text-align: center; border: 1px solid; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('odometro');return false;}"><label class="etiqAzul" style="padding-left: 2px;"> litros</label></td>
                            <td class="etiq aDir" style="min-width: 50px;">R$: </td>
                            <td><input disabled type="text" id="precolitro" valor="" onchange="modif();" style="width: 100px; text-align: center; border: 1px solid; border-radius: 5px;"><label class="etiqAzul" style="padding-left: 2px;"> por litro</label></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="padding-top: 2px;"></td>
                        <tr>
                    </table>
                </div>

                <div id="relacmodalInsManut" style="margin: 3px; padding: 3px; border: 1px solid; border-radius: 10px; background: linear-gradient(180deg, white,rgb(226, 207, 214))">
                    <table style="margin: 0 auto; width: 95%; text-align: left;">
                        <tr>
                            <td colspan="6" style="padding-top: 2px;"></td>
                        <tr>
                        <tr>
                            <td class="etiq aDir">Tipo de Manutenção: </td>
                            <td>
                                <select id="reltipomanut" onchange="modif();" style="font-size: .9rem; width: 90%;" title="Selecione um tipo de combustível.">
                                    <option value=""></option>
                                    <?php 
                                    if($rsManut){
                                        while ($Opcoes = pg_fetch_row($rsManut)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td style="min-width: 50px;"></td>
                            <td class="etiq aDir">Valor da Nota R$: </td>
                            <td><input type="text" id="valormanut" valor="" onchange="modifValor();" style="width: 100px; text-align: center; border: 1px solid; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('volumecompra');return false;}"></td>
                            <td style="min-width: 100px;"></td>
                        </tr>
                        <tr>
                            <td colspan="6" style="padding-top: 2px;"></td>
                        <tr>
                    </table>
                </div>
                <table style="margin: 0 auto; width: 95%;">
                    <tr>
                        <td class="etiq aDir">Observações: </td>
                        <td><textarea class="form-control" id="obsviatura" style="resize: both; margin-top: 3px; border: 1px solid blue; border-radius: 10px; padding: 4px;" rows="2" cols="70" title="Observações" onchange="modif();"></textarea></td>
                    </tr>
                </table>

                <table style="margin: 0 auto; width: 95%;">
                    <tr>
                        <td><button class="botpadrred" id="apagaRegCombust" style="display: none; font-size: .9rem; font-size: 80%; padding: 2px;" onclick="apagaModalViatura();">Apagar</button></td>
                        <td style="text-align: center;"><div id="mensagemLeitura" style="position: relative; float: right; color: red; font-weight: bold;"></div></td>
                        <td style="text-align: center;"><button id="botsalvar" class="botpadrblue" onclick="salvaModal();">Salvar</button></td>
                    </tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->

        <!-- config para inserir usuários, viaturas e tipo de combustível -->
        <div id="relacmodalConfig" class="relacmodal">
            <div class="modal-content-Config">
                <span class="close" onclick="fechaModalConfig();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col" style="margin: 0 auto;"></div>
                        <div class="col"><h6 id="titulomodal" style="text-align: center; color: #666;">Controle de Viaturas</h6></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col" style="margin: 0 auto; text-align: center;"><button class="botpadrred" style="font-size: 70%;" onclick="imprUsuComb();">Resumo em PDF</button></div> 
                    </div>
                </div>
                <label class="etiqAzul">Selecione um usuário para ver a configuração:</label>
                <div style="position: relative; float: right; color: red; font-weight: bold; padding-right: 200px;" id="mensagemConfig"></div>

                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td colspan="4" style="text-align: center;"></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="etiqAzul" style="text-align: center;">Busca Nome ou CPF do Usuário</td>
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
                            <input type="text" id="configcpfsolicitante" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('configselecSolicitante');return false;}" title="Procura por CPF. Digite o CPF do usuário."/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                    </tr>
                </table>
                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td class="etiq80" title="Registrar despesas com combustíveis e manutenção de viaturas">Viaturas:</td>
                        <td colspan="4">
                            <input type="checkbox" id="registroViatura" title="Registrar as compras de combustíveis e despesas com manutenção" onchange="marcaCheckBox(this, 'viatura');" >
                            <label for="registroViatura" class="etiqNorm" title="Registrar despesas com combustíveis e manutenção de viaturas">Registrar e editar despesas com viaturas</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiq80" title="Fiscalizar as despesas com manutenção de viaturas">Viaturas: </td>
                        <td colspan="4">
                            <input type="checkbox" id="fiscalViatura" title="Fiscalizar as despesas com manutenção de viaturas" onchange="marcaCheckBox(this, 'fisc_viat');" >
                            <label for="fiscalViatura" class="etiqNorm" title="Fiscalizar as despesas com manutenção de viaturas">Fiscalizar as despesas com viaturas</label>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="5" style="text-align: center; padding-top: 5px;"></div></td>
                    <tr>
                </table>

                <hr class="etiqNorm">

                <div class="etiqNorm" style="text-align: center;"><H6>Configuração: Viaturas/Combustíveis</H6></div>
                <table style="margin: 0 auto; width: 86%;">
                    <tr>
                        <td style="vertical-align: top;">
                            <div style="position: relative; float: left; width: 99%; margin-top: 10px; text-align: center; border: 1px solid; border-radius: 10px; background: linear-gradient(180deg, white, #86c1eb);">
                                <div style="margin: 20px; min-width: 200px; padding: 5px; text-align: center; border: 1px solid; border-radius: 15px; background: linear-gradient(180deg, white, #86c1eb);">
                                    <div class='divbot corFundo' onclick='insTipo()' title="Adicionar uma nova viatura"> Adicionar </div>
                                    <div id="configTipos" style="margin-bottom: 15px; text-align: center; width: 90%;"></div>
                                </div>
                            </div>
                        </td>
                        <td></td>
                        <td></td>
                        <td style="vertical-align: top;">
                            <div style="position: relative; float: right; width: 99%; margin-top: 10px; text-align: center; border: 1px solid; border-radius: 10px; background: linear-gradient(180deg, white, #86c1eb);">
                                <div style="margin: 20px; min-width: 200px; padding: 5px; text-align: center; border: 1px solid; border-radius: 15px; background: linear-gradient(180deg, white, #86c1eb);">
                                    <div class='divbot corFundo' onclick='insCombust()' title="Adicionar novo tipo de combustível"> Adicionar </div>
                                    <div id="configComb" style="text-align: center;"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top;"></td>
                        <td></td>
                        <td></td>
                        <td style="vertical-align: top;">
                            <div style="position: relative; float: right; width: 99%; margin-top: 10px; text-align: center; border: 1px solid; border-radius: 10px; background: linear-gradient(180deg, white, #86c1eb);">
                                <div style="margin: 20px; min-width: 200px; padding: 5px; text-align: center; border: 1px solid; border-radius: 15px; background: linear-gradient(180deg, white, #86c1eb);">
                                    <div class='divbot corFundo' onclick='insManut()' title="Adicionar novo tipo de manutenção"> Adicionar </div>
                                    <div id="configManut" style="text-align: center;"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->

        <div id="relacEditTipo" class="relacmodal">
            <div class="modal-content-InsTipo">
                <span class="close" onclick="fechaEditTipo();">&times;</span>
                <h5 id="titulomodalTipo" style="text-align: center; color: #666;">Nova Viatura</h5>
                    <table style="margin: 0 auto; width: 90%">
                        <tr>
                            <td class="etiq aDir">Tipo: </td>
                            <td><input type="text" id="editNomeTipo" valor="" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%;"></td>
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

        <div id="relacEditCombust" class="relacmodal">
            <div class="modal-content-InsTipo">
                <span class="close" onclick="fechaEditComb();">&times;</span>
                <h5 id="titulomodalEmpr" style="text-align: center; color: #666;">Novo Combustível</h5>
                    <table style="margin: 0 auto; width: 90%">
                        <tr>
                            <td class="etiq aDir">Tipo: </td>
                            <td><input type="text" id="editNomeEmpr" valor="" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%;"></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                    <br>
                    <div style="text-align: center;">
                        <button id="botApagaEditEmpr" class="resetbotred" style="font-size: .8rem;" onclick="apagaCombust();">Apagar</button>
                        <label style="padding-left: 50%;"></label>
                        <button id="botSalvarEditEmpr" class="resetbot" style="font-size: .9rem;" onclick="salvaEditComb();">Salvar</button>
                    </div>
                </div>
            </div>
        </div> <!-- Fim Modal-->

        <div id="relacEditManut" class="relacmodal">
            <div class="modal-content-InsTipo">
                <span class="close" onclick="fechaEditManut();">&times;</span>
                <h5 id="titulomodalManut" style="text-align: center; color: #666;">Tipo de Manutenção</h5>
                    <table style="margin: 0 auto; width: 90%">
                        <tr>
                            <td class="etiq aDir">Tipo: </td>
                            <td><input type="text" id="editNomeManut" valor="" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%;"></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                    <br>
                    <div style="text-align: center;">
                        <button id="botApagaEditManut" class="resetbotred" style="font-size: .8rem;" onclick="apagaManut();">Apagar</button>
                        <label style="padding-left: 50%;"></label>
                        <button id="botSalvarEditManut" class="resetbot" style="font-size: .9rem;" onclick="salvaEditManut();">Salvar</button>
                    </div>
                </div>
            </div>
        </div> <!-- Fim Modal-->

    </body>
</html>