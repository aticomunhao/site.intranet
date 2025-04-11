<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Extintores</title>
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
            .modal-content-Ins{
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
            .modal-content-InsImpr{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 65%;
                max-width: 500px;
            }
            .alinhaCentro{
                text-align: center;
            }
        </style>

        <script type="text/javascript">
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
                document.getElementById("imagconfig").style.visibility = "hidden";
                document.getElementById("botsalvarextint").style.visibility = "hidden";
                document.getElementById("botimprUsu").style.visibility = "hidden";

                if(parseInt(document.getElementById("guardaInsExtint").value) === 1 || parseInt(document.getElementById("guardaFiscExtint").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){
                    document.getElementById("botinserir").style.visibility = "visible";
                    document.getElementById("imagconfig").style.visibility = "visible";
                    document.getElementById("botsalvarextint").style.visibility = "visible";
                }
                if(parseInt(document.getElementById("UsuAdm").value) > 6){
                    $("#configAdmin").load("modulos/extintores/admExtint.php");
                    document.getElementById("botimprUsu").style.visibility = "visible";
                }

                $('#carregaTema').load('modulos/config/carTema.php?carpag=clavic1');

                if(parseInt(document.getElementById("guardaInsExtint").value) === 1){ // editar
                    $('#datarevis').datepicker({ uiLibrary: 'bootstrap3', locale: 'pt-br', format: 'dd/mm/yyyy' });
                    $('#datavalid').datepicker({ uiLibrary: 'bootstrap3', locale: 'pt-br', format: 'dd/mm/yyyy' });
                    $('#datavalcasco').datepicker({ uiLibrary: 'bootstrap3', locale: 'pt-br', format: 'dd/mm/yyyy' });
                }
                $("#faixacentral").load("modulos/extintores/jExtint.php?acao="+document.getElementById("guardaAcao").value);


            }); // fimm do ready

            function insExtintor(){
                document.getElementById("guardaid").value = 0;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/extintores/salvaExtint.php?acao=buscanumero", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("numextintor").innerHTML = Resp.extint;
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("subtitulomodal").innerHTML = "Inserindo novo extintor";
                                    document.getElementById("registroextint").value = "";
                                    document.getElementById("serieextint").value = "";
                                    document.getElementById("localextint").value = "";
                                    document.getElementById("reltipoextint").value = "";
                                    document.getElementById("capacidextint").value = "";
                                    document.getElementById("datarevis").value = "";
                                    document.getElementById("datavalid").value = "";
                                    document.getElementById("datavalcasco").value = "";
                                    document.getElementById("relempresas").value = "";
                                    document.getElementById("relacmodalIns").style.display = "block";
                                    document.getElementById("botapagaextint").style.display = "none";
                                    document.getElementById("localextint").focus();
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function carregaExtintor(Cod){
                document.getElementById("guardaid").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/extintores/salvaExtint.php?acao=buscaextintor&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("numextintor").innerHTML = Resp.extint;
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("subtitulomodal").innerHTML = "";
                                    document.getElementById("registroextint").value = Resp.registro;
                                    document.getElementById("serieextint").value = Resp.numserie;
                                    document.getElementById("reltipoextint").value = Resp.tipo;
                                    document.getElementById("capacidextint").value = Resp.capacid;
                                    if(Resp.revis !== "31/12/3000"){
                                        document.getElementById("datarevis").value = Resp.revis;
                                    }
                                    if(Resp.valid !== "31/12/3000"){
                                        document.getElementById("datavalid").value = Resp.valid;
                                    }
                                    if(Resp.casco !== "31/12/3000"){
                                        document.getElementById("datavalcasco").value = Resp.casco;
                                    }
                                    document.getElementById("localextint").value = Resp.local;
                                    document.getElementById("relempresas").value = Resp.empresa;
                                    document.getElementById("relacmodalIns").style.display = "block";

                                    if(parseInt(document.getElementById("UsuAdm").value) > 6){
                                        document.getElementById("botapagaextint").style.display = "block";
                                    }
                                    if(parseInt(document.getElementById("guardaInsExtint").value) === 0){
                                        document.getElementById("registroextint").disabled = true;
                                        document.getElementById("serieextint").disabled = true;
                                        document.getElementById("reltipoextint").disabled = true;
                                        document.getElementById("capacidextint").disabled = true;
                                        document.getElementById("datarevis").disabled = true;
                                        document.getElementById("datavalid").disabled = true;
                                        document.getElementById("datavalcasco").disabled = true;
                                        document.getElementById("localextint").disabled = true;
                                        document.getElementById("relempresas").disabled = true;
                                    }else{
                                        document.getElementById("localextint").focus();
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

            function carregaEmpresas(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/extintores/salvaExtint.php?acao=buscarelempresas", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                var options = "";  //Cria array
                                options += "<option value='0'></option>";
                                $.each(Resp, function(key, Resp){
                                    options += '<option value="' + Resp.Cod + '">'+Resp.Nome + '</option>';
                                });
                                $("#relempresas").html(options);
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function carregaTipos(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/extintores/salvaExtint.php?acao=buscareltipos", true);
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
                                $("#reltipoextint").html(optionsT);
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function apagaExtintor(){
                $.confirm({
                    title: 'Apagar.',
                    content: 'Confirma apagar este lançamento?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/extintores/salvaExtint.php?acao=apagaextint&codigo="+document.getElementById("guardaid").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro desconhecido.");
                                            }else{
                                                document.getElementById("relacmodalIns").style.display = "none";
                                                $("#faixacentral").load("modulos/extintores/jExtint.php?acao="+document.getElementById("guardaAcao").value);
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

            function fechaModal(){
                document.getElementById("guardaid").value = 0;
                document.getElementById("relacmodalIns").style.display = "none";
                document.getElementById("relacmodalConfig").style.display = "none";
            }

            function salvaInsExtintor(){
                if(document.getElementById("datavalid").value == ""){
                    $.confirm({
                        title: 'Informação!',
                        content: 'Infome a data de validade da carga do extintor.',
                        autoClose: 'OK|5000',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                if(document.getElementById("reltipoextint").value == ""){
                    $.confirm({
                        title: 'Informação!',
                        content: 'Infome o tipo do extintor.',
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
                        ajax.open("POST", "modulos/extintores/salvaExtint.php?acao=salvadados&codigo="+document.getElementById("guardaid").value
                        +"&numero="+encodeURIComponent(document.getElementById("numextintor").innerHTML)
                        +"&registroextint="+encodeURIComponent(document.getElementById("registroextint").value)
                        +"&serieextint="+encodeURIComponent(document.getElementById("serieextint").value)
                        +"&localextint="+encodeURIComponent(document.getElementById("localextint").value)
                        +"&tipoextint="+document.getElementById("reltipoextint").value
                        +"&capacidextint="+encodeURIComponent(document.getElementById("capacidextint").value)
                        +"&datarevis="+encodeURIComponent(document.getElementById("datarevis").value)
                        +"&datavalid="+encodeURIComponent(document.getElementById("datavalid").value)
                        +"&datavalcasco="+encodeURIComponent(document.getElementById("datavalcasco").value)
                        +"&empresa="+document.getElementById("relempresas").value
                        , true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.")
                                    }else{
                                        document.getElementById("relacmodalIns").style.display = "none";
                                        $("#faixacentral").load("modulos/extintores/jExtint.php?acao=ext_todos");
                                        var element = document.getElementById("ext_vencer");
                                        element.classList.remove("fundoAzul");
                                        var element = document.getElementById("ext_vencidos");
                                        element.classList.remove("fundoAzul");
                                        var element = document.getElementById("ext_todos");
                                        element.classList.add("fundoAzul");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("relacmodalIns").style.display = "none";
                }
            }

            function carregaConfig(){
                $("#configEmpr").load("modulos/extintores/extEmpr.php");
                $("#configTipos").load("modulos/extintores/extTipos.php");
                $("#configAdmin").load("modulos/extintores/admExtint.php");
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/extintores/salvaExtint.php?acao=buscaConfig", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                document.getElementById("diasanteced").value = Resp.aviso;
                                if(parseInt(Resp.coderro) === 0){
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
                document.getElementById("relacmodalConfig").style.display = "block";
            }

            function insEmpresa(){
                document.getElementById("guardaCodEmpr").value = "0";
                document.getElementById("editNomeEmpr").value = "";
                document.getElementById("editEnder").value = "";
                document.getElementById("editCEP").value = "";
                document.getElementById("editCidade").value = "";
                document.getElementById("editUF").value = "DF";
                document.getElementById("editCNPJ").value = "";
                document.getElementById("editInscr").value = "";
                document.getElementById("editTelef").value = "";
                document.getElementById("editContato").value = "";
                document.getElementById("editObs").value = "";
                document.getElementById("titulomodalEmpr").innerHTML = "Nova Empresa";
                document.getElementById("relacEditEmpresa").style.display = "block";
            }
            function insTipo(){
                document.getElementById("guardaCodTipo").value = "0";
                document.getElementById("editNomeTipo").value = "";
                document.getElementById("titulomodalTipo").innerHTML = "Nome do novo tipo";
                document.getElementById("relacEditTipo").style.display = "block";
            }

            function editaEmpresa(Cod){
                document.getElementById("guardaCodEmpr").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/extintores/salvaExtint.php?acao=buscaempresa&codigo="+Cod, true);
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
                                    document.getElementById("titulomodalEmpr").innerHTML = "Edita Empresa";
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

            function editaTipo(Cod){
                document.getElementById("guardaCodTipo").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/extintores/salvaExtint.php?acao=buscatipo&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editNomeTipo").value = Resp.nome;
                                    document.getElementById("titulomodalTipo").innerHTML = "Edita tipo de extintor";
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
                        ajax.open("POST", "modulos/extintores/salvaExtint.php?acao=salvanomeempresa&codigo="+document.getElementById("guardaCodEmpr").value 
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
                                        $("#configEmpr").load("modulos/extintores/extEmpr.php");
                                        carregaEmpresas(); // recarrega relação
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

            function salvaEditTipo(){
                if(document.getElementById("mudou").value != "0"){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/extintores/salvaExtint.php?acao=salvanometipo&codigo="+document.getElementById("guardaCodTipo").value 
                        +"&nometipo="+encodeURIComponent(document.getElementById("editNomeTipo").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("relacEditTipo").style.display = "none";
                                        $("#configTipos").load("modulos/extintores/extTipos.php");
                                        carregaTipos(); // recarrega relação
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

            function salvaAviso(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/extintores/salvaExtint.php?acao=salvaaviso&valor="+document.getElementById("diasanteced").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    $('#mensagemConfig').fadeIn("slow");
                                    document.getElementById("mensagemConfig").innerHTML = "Valor Salvo";
                                    $('#mensagemConfig').fadeOut(2000);
                                    $("#faixacentral").load("modulos/extintores/jExtint.php?acao=ext_todos");
                                    var element = document.getElementById("ext_vencer");
                                    element.classList.remove("fundoAzul");
                                    var element = document.getElementById("ext_vencidos");
                                    element.classList.remove("fundoAzul");
                                    var element = document.getElementById("ext_todos");
                                    element.classList.add("fundoAzul");
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            //põe fundo azul no botão Todos
            var element = document.getElementById("ext_todos");
            element.classList.add("fundoAzul");

            function mostraExtint(Acao){
                var element = document.getElementById("ext_todos");
                element.classList.remove("fundoAzul");
                var element = document.getElementById("ext_vencer");
                element.classList.remove("fundoAzul");
                var element = document.getElementById("ext_vencidos");
                element.classList.remove("fundoAzul");
                $("#faixacentral").load("modulos/extintores/jExtint.php?acao="+Acao);
                var element = document.getElementById(Acao);
                element.classList.add("fundoAzul");
            }

            function imprUsuExtint(){
                window.open("modulos/extintores/imprUsuExtint.php?acao=listaUsuarios", "usuExtint");
            }
            function imprExtintModal(){
                document.getElementById("relacimprExtint").style.display = "block";
            }
            function ImprExtint(Valor){
                window.open("modulos/extintores/imprExtint.php?acao=imprExtint&valor="+Valor, Valor);
            }
            function fechaModalImpr(){
                document.getElementById("relacimprExtint").style.display = "none";
            }
            function fechaEditEmpr(){
                document.getElementById("relacEditEmpresa").style.display = "none";
            }
            function fechaEditTipo(){
                document.getElementById("relacEditTipo").style.display = "none";
            }
            function modif(){ // assinala se houve qualquer modificação nos campos do modal durante a edição para evitar salvar desnecessariamente
                document.getElementById("mudou").value = "1";
            }
            function foco(id){
                document.getElementById(id).focus();
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

            $("#editCEP").mask("99999-999");
            $("#editTelef").mask("(99) 9999-9999");
            $("#editCNPJ").mask("99.999.999/9999-99");
 
        </script>
    </head>
    <body class="corClara" onbeforeunload="return mudaTema(0)"> <!-- ao sair retorna os background claros -->
        <?php
            date_default_timezone_set('America/Sao_Paulo');
            $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'poslog'");
            $rowSis = pg_num_rows($rsSis);
            if($rowSis == 0){
                echo "Sem contato com os arquivos do sistema. Informe à ATI.";
                return false;
            }

//Provisórios
//            pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".extintores");
            $rs = pg_query($Conec, "SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE table_schema = 'cesb' And table_name = 'extintores'");
            $row = pg_num_rows($rs);
            if($row == 0){
//                echo "Faltam tabelas. Informe à ATI.";
//                return false;

                pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".extintores (
                    id SERIAL PRIMARY KEY, 
                    ext_num integer NOT NULL DEFAULT 0, 
                    ext_local VARCHAR(50), 
                    ext_empresa smallint DEFAULT 0 NOT NULL, 
                    ext_tipo smallint DEFAULT 0 NOT NULL, 
                    ext_capac VARCHAR (50), 
                    ext_reg VARCHAR (50), 
                    ext_serie VARCHAR (50), 
                    datacarga timestamp without time zone DEFAULT '3000-12-31',
                    datavalid timestamp without time zone DEFAULT '3000-12-31',
                    datacasco timestamp without time zone DEFAULT '3000-12-31',
                    ativo smallint DEFAULT 1 NOT NULL, 
                    usuins integer DEFAULT 0 NOT NULL,
                    datains timestamp without time zone DEFAULT '3000-12-31',
                    usuedit integer DEFAULT 0 NOT NULL,
                    dataedit timestamp without time zone DEFAULT '3000-12-31' ) 
                ");
                pg_query($Conec, "INSERT INTO ".$xProj.".extintores (id, ext_num, ext_local, ext_empresa, ext_tipo, ext_capac, ext_reg, ext_serie, datacarga, datavalid, ativo, usuins, datains) 
                VALUES(1, 1, 'Corredor Principal', 1, 1, '10 litros', '005525/2015', '307.259.747', '2024-06-03', '2025-06-03', 1, 3, NOW() )");
                pg_query($Conec, "INSERT INTO ".$xProj.".extintores (id, ext_num, ext_local, ext_empresa, ext_tipo, ext_capac, ext_reg, ext_serie, datacarga, datavalid, ativo, usuins, datains) 
                VALUES(2, 2, 'Elevador Principal', 1, 2, '10 quilos', '000000/2025', '000.000.001', '2024-08-25', '2025-02-25', 1, 3, NOW() )");
            }
            $rs = pg_query($Conec, "SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE table_schema = 'cesb' And table_name = 'extintores_tipo'");
            $row = pg_num_rows($rs);
            if($row == 0){
                pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".extintores_tipo (
                    id SERIAL PRIMARY KEY, 
                    desc_tipo VARCHAR(50),
                    ativo smallint DEFAULT 1 NOT NULL,
                    usuins integer DEFAULT 0 NOT NULL,
                    datains timestamp without time zone DEFAULT '3000-12-31',
                    usuedit integer DEFAULT 0 NOT NULL,
                    dataedit timestamp without time zone DEFAULT '3000-12-31',
                    usudel integer DEFAULT 0 NOT NULL,
                    datadel timestamp without time zone DEFAULT '3000-12-31'
                    ) 
                 ");

                 pg_query($Conec, "INSERT INTO ".$xProj.".extintores_tipo (id, desc_tipo, ativo, usuins, datains) VALUES(1, 'CO2', 1, 3, NOW() )");
                 pg_query($Conec, "INSERT INTO ".$xProj.".extintores_tipo (id, desc_tipo, ativo, usuins, datains) VALUES(2, 'Espuma', 1, 3, NOW() )");
                 pg_query($Conec, "INSERT INTO ".$xProj.".extintores_tipo (id, desc_tipo, ativo, usuins, datains) VALUES(3, 'Pó Químico', 1, 3, NOW() )");
            }
            
            $rs = pg_query($Conec, "SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE table_schema = 'cesb' And table_name = 'extintores_empr'");
            $row = pg_num_rows($rs);
            if($row == 0){
                pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".extintores_empr (
                    id SERIAL PRIMARY KEY, 
                    empresa VARCHAR(150),
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
                pg_query($Conec, "INSERT INTO ".$xProj.".extintores_empr (id, empresa, ender, cep, cidade, uf, telefone, ativo, usuins, datains) 
                VALUES(1, 'Combate Comércio de Extintores Ltda.', 'QS122 - Conj 11-02 - Samambaia Sul', '72304531', 'Brasília', 'DF', '61999915504', 1, 3, NOW() )");
            }
            if(isset($_REQUEST["acao"])){
                $Acao = $_REQUEST["acao"];
            }else{
                $Acao = "ext_todos";
            }

            $rsAno = pg_query($Conec, "SELECT DISTINCT to_char(datacarga, 'YYYY') FROM ".$xProj.".extintores WHERE ativo = 1");
            $AnoIni = date("Y");
            $Hoje = date("d/m/Y");
            $Data = date("d/m/Y H:i");
            $rsTipos = pg_query($Conec, "SELECT id, desc_tipo FROM ".$xProj.".extintores_tipo WHERE ativo = 1 ORDER BY desc_tipo");
            $rsEmpr = pg_query($Conec, "SELECT id, empresa FROM ".$xProj.".extintores_empr WHERE ativo = 1 ORDER BY empresa");

            $InsExtint = parEsc("extint", $Conec, $xProj, $_SESSION["usuarioID"]); // procura marca em poslog
            $FiscExtint = parEsc("fisc_extint", $Conec, $xProj, $_SESSION["usuarioID"]);
            $TempoAviso  = parAdm("aviso_extint", $Conec, $xProj);
            $Tema = parEsc("tema", $Conec, $xProj, $_SESSION["usuarioID"]); // Claro(0) Escuro(1)
    
        ?>
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="guardaid" value="0" />
        <input type="hidden" id="mudou" value="0" />
        <input type="hidden" id="guardaInsExtint" value="<?php echo $InsExtint; ?>" />
        <input type="hidden" id="guardaFiscExtint" value="<?php echo $FiscExtint; ?>" />
        <input type="hidden" id="guardaInsEdit" value="0" />
        <input type="hidden" id="guardaHoje" value="<?php echo $Hoje; ?>" />
        <input type="hidden" id="guardaCodEmpr" value="0" />
        <input type="hidden" id="guardaCodTipo" value="0" />
        <input type="hidden" id="guardaAcao" value="<?php echo $Acao; ?>" />

        <div id="tricoluna0" style="margin: 20px; border: 2px solid blue; border-radius: 15px; padding: 20px; min-height: 200px;">
            <div id="tricoluna1" class="box" style="position: relative; float: left; width: 33%;">
                <img src="imagens/settings.png" height="20px;" id="imagconfig" style="cursor: pointer; padding-right: 30px;" onclick="carregaConfig();" title="Configurar tipos de extintor e empresas de manutenção">
                <input type="button" id="botinserir" class="resetbot fundoAzul2" style="font-size: 80%;" value="Inserir Novo Extintor" onclick="insExtintor();">
            </div>
            <div id="tricoluna2" class="box" style="position: relative; float: left; width: 33%; text-align: center;">
                <h5>Controle de Extintores</h5>
                <button id="ext_todos" class="resetbot fundoAmareloCl" style="font-size: .9rem;" onclick="mostraExtint(id);">Todos</button>
                <button id="ext_vencer" class="resetbot fundoAmareloCl" style="font-size: .9rem;" onclick="mostraExtint(id);" title="Dentro do prazo para aviso <?php echo $TempoAviso.' dias'; ?>">a Vencer</button>
                <button id="ext_vencidos" class="resetbot fundoAmareloCl" style="font-size: .9rem;" onclick="mostraExtint(id);" title="Extintores com prazo de validade vencido.">Vencidos</button>
            </div>
            <div id="tricoluna3" class="box" style="position: relative; float: right; width: 33%; text-align: right;">
                <div id="selectTema" style="position: relative; float: left; padding-left: 30px;">
                    <label id="etiqcorFundo" class="etiq" style="color: #6C7AB3; font-size: 80%; padding-left: 10px;">Tema: </label>
                    <input type="radio" name="corFundo" id="corFundo0" value="0" <?php if($Tema == 0){echo 'CHECKED';}; ?> title="Tema claro" onclick="mudaTema(0);"><label for="corFundo0" style="font-size: 80%;">&nbsp;Claro</label>
                    <input type="radio" name="corFundo" id="corFundo1" value="1" <?php if($Tema == 1){echo 'CHECKED';}; ?> title="Tema escuro" onclick="mudaTema(1);"><label for="corFundo1" style="font-size: 80%;">&nbsp;Escuro</label>
                </div>
                <label style="padding-left: 20px;"></label>
                <button class="botpadrred" style="font-size: 80%;" onclick="imprExtintModal();">PDF</button>
            </div>

            <div id="faixacentral"></div>

            <div id="faixaMensagem" style="display: none; position: relative; margin: 70px; padding: 20px; text-align: center;">
                Usuário não cadastrado. <br>O acesso é proporcionado pela ATI.
            </div>
        </div>

        <!-- div para inserção novo aparelho  -->
        <div id="relacmodalIns" class="relacmodal">
            <div class="modal-content-Ins corPreta">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h5 id="titulomodal" style="text-align: center; color: #666;">Extintor</h5>
                <div id="subtitulomodal" style="text-align: center; color: red;"></div>
                <div style="margin: 3px; padding: 3px; border: 1px solid; border-radius: 10px; background: linear-gradient(180deg, white,rgb(99, 167, 215));">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td class="etiq aDir">Extintor nº: </td>
                            <td colspan="2"><label class="aCentro" style="padding-left: 5px; font-weight: bold;" id="numextintor"></label></td>
                            <td class="etiq aDir">nº de Registro:</td>
                            <td colspan="2"><input type="text" id="registroextint" valor="" onchange="modif();" style="width: 150px; border: 1px solid; border-radius: 5px; padding-left: 3px;"></td>
                            <td class="etiq aDir">nº de Série:</td>
                            <td><input type="text" id="serieextint" valor="" onchange="modif();" style="width: 150px; border: 1px solid; border-radius: 5px; padding-left: 3px;"></td>
                            <td></td>
                        </tr>
                    </table>
                </div>
                <div style="margin: 3px; padding: 3px; border: 1px solid; border-radius: 10px;">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td class="etiq aDir">Tipo de Extintor: </td>
                            <td colspan="26">
                                <select id="reltipoextint" onchange="modif();" style="font-size: .9rem; width: 90%;" title="Selecione um tipo de extintor.">
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
                            <td class="etiq aDir">Capacidade: </td>
                            <td colspan="2"><input type="text" id="capacidextint" valor="" onchange="modif();" style="padding-left: 3px; width: 150px; border: 1px solid; border-radius: 5px;"></td>
                        </tr>
                        <tr>
                            <td class="etiq aDir">Revisado em: </td>
                            <td colspan="12"><input type="text" id="datarevis" valor="" width="150" onchange="modif();" style="text-align: center; border: 1px solid; border-radius: 5px;"></td>
                            <td colspan="4" class="etiq aDir">Validade até: </td>
                            <td colspan="10"><input type="text" id="datavalid" valor="" width="150" onchange="modif();" style="text-align: center; border: 1px solid; border-radius: 5px;"></td>
                            <td class="etiq aDir">Validade Casco: </td>
                            <td><input type="text" id="datavalcasco" valor="" width="150" onchange="modif();" style="text-align: center; border: 1px solid; border-radius: 5px;"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="etiq aDir">Local de instalação: </td>
                            <td colspan="28"><input type="text" id="localextint" valor="" onchange="modif();" style="width: 100%; padding-left: 3px; border: 1px solid; border-radius: 5px;"></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="etiq aDir">Empresa de Manutenção: </td>
                            <td colspan="27">
                                <select id="relempresas" onchange="modif();" style="font-size: .9rem; width: 100%;" title="Selecione uma empresa.">
                                    <option value=""></option>
                                    <?php 
                                    if($rsEmpr){
                                        while ($Opcoes = pg_fetch_row($rsEmpr)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="30" style="padding-bottom: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="15" style="text-align: center;">
                                <button class="botpadrred" id="botapagaextint" style="font-size: .9rem; font-size: 80%; padding: 2px;" onclick="apagaExtintor();">Apagar</button>
                            </td>
                            <td colspan="15" style="text-align: center;">
                                <button class="botpadrblue" id="botsalvarextint" style="font-size: .9rem;" onclick="salvaInsExtintor();">Salvar</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div para editar nome das empresas e Tipos de Extintores -->
        <div id="relacmodalConfig" class="relacmodal">
            <div class="modal-content-Ins corPreta">
                <span class="close" onclick="fechaModal();">&times;</span>
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col" style="margin: 0 auto;"></div>
                        <div class="col"><h6 id="titulomodal" style="text-align: center; color: #666;">Controle de Extintores</h6></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col" style="margin: 0 auto; text-align: center;"><button id="botimprUsu" class="botpadrred" style="font-size: 70%;" onclick="imprUsuExtint();">Resumo em PDF</button></div> 
                    </div>
                </div>

                <div id="configAdmin"></div> <!-- para superusuários -->

                <div class="box" style="position: relative; float: left; width: 43%; margin-top: 10px; text-align: center; border: 1px solid; border-radius: 10px; background: linear-gradient(180deg, white, #86c1eb);">
                    <div class='divbot corFundo' style='margin-top: 10px; margin-left: 5px; margin-bottom: 5px;' onclick='insTipo()' title="Adicionar um novo tipo de extintor"> Adicionar </div>
                    <div id="configTipos" style="margin-bottom: 15px; text-align: center; width: 90%;"></div>
                </div>
                <div class="box" style="position: relative; float: left; width: 10%; margin-top: 10px; text-align: center;"></div>
                <div class="box" style="position: relative; float: right; width: 40%; margin-top: 10px; margin-right: 20px; padding-top: 5px; text-align: left; border: 2px solid green; border-radius: 10px; min-height: 100px;">
                    <label class="etiqAzul" style="padding-left: 20px;">Aviso de vencimento:</label>
                    <br>
                    <table style="margin: 0 auto; padding-top: 5px;">
                        <tr>
                            <td class="etiqAzul"> Avisar com </td>
                            <td>
                                <input type="text" id="diasanteced" valor="" onchange="salvaAviso();" style="border: 1px solid; border-radius: 5px; width: 40px; text-align: center;">
                            </td>
                            <td class="etiqAzul"> dias de antecedência</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: center;">
                            <label id="mensagemConfig" style="color: red; font-weight: bold; padding-left: 30px;"></label>
                            </td>
                        </tr>
                    </table>
                </div>

                <table>
                    <tr>
                        <td>
                            <div style="margin: 20px; min-width: 500px; padding: 5px; text-align: center; border: 1px solid; border-radius: 15px; background: linear-gradient(180deg, white, #86c1eb);">
                                <div class='divbot corFundo' onclick='insEmpresa()' title="Adicionar nova empresa"> Adicionar </div>
                                <div id="configEmpr" style="text-align: center;"></div>
                            </div>
                        </td>
                    </tr>
                </table>
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
                        <button id="botSalvarEditEmpr" class="resetbot" style="font-size: .9rem;" onclick="salvaEditEmpr();">Salvar</button>
                    </div>
                </div>
            </div>
        </div> <!-- Fim Modal-->

        <div id="relacEditTipo" class="relacmodal">
            <div class="modal-content-InsTipo corPreta">
                <span class="close" onclick="fechaEditTipo();">&times;</span>
                <h5 id="titulomodalTipo" style="text-align: center; color: #666;">Novo tipo de extintor</h5>
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
                        <button id="botSalvarEditTipo" class="resetbot" style="font-size: .9rem;" onclick="salvaEditTipo();">Salvar</button>
                    </div>
                </div>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para imprimir em pdf  -->
        <div id="relacimprExtint" class="relacmodal">
            <div class="modal-content-InsImpr corPreta">
                <span class="close" onclick="fechaModalImpr();">&times;</span>
                <h5 id="titulomodal" style="text-align: center;color: #666;">Controle de Manutenção de Extintores</h5>
                <h6 id="titulomodal" style="text-align: center; padding-bottom: 18px; color: #666;">Impressão PDF</h6>
                <div>
                    <table style="margin: 0 auto;">
                        <tr>
                            <td><div style="margin: 5px; padding: 5px; border: 2px solid #C6E2FF; border-radius: 10px;"><button class="resetbot fundoAmareloCl" style="font-size: .9rem;" onclick="ImprExtint('todos');">Todos</button></div></td>
                            <td><div style="margin: 5px; padding: 5px; border: 2px solid #C6E2FF; border-radius: 10px;"><button class="resetbot fundoAmareloCl" style="font-size: .9rem;" onclick="ImprExtint('vencer');" title="Dentro do prazo para aviso <?php echo $TempoAviso.' dias'; ?>">a Vencer</button></div></td>
                            <td><div style="margin: 5px; padding: 5px; border: 2px solid #C6E2FF; border-radius: 10px;"><button class="resetbot fundoAmareloCl" style="font-size: .9rem;" onclick="ImprExtint('vencidos');" title="Extintores com prazo de validade vencido.">Vencidos</button></div></td>
                        </tr>
                    </table>

                </div>
                <div style="padding-bottom: 20px;"></div>
           </div>
           <br><br>
        </div> <!-- Fim Modal-->

        <div id="carregaTema"></div> <!-- carrega a pág modulos/config/carTema.php - onde está a função mudaTema() -->

    </body>
</html>