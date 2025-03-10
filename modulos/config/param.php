<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="class/gijgo/css/gijgo.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="comp/js/jquery.mask.js"></script>
        <script src="class/dataTable/datatables.min.js"></script>  <!-- https://datatables.net/examples/basic_init/filter_only.html -->
        <script src="comp/js/jquery-confirm.min.js"></script>   <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="class/gijgo/js/gijgo.js"></script>
        <script src="class/gijgo/js/messages/messages.pt-br.js"></script>
        <style type="text/css">
            .etiq{
                text-align: right; color: #036; font-size: .9em; font-weight: bold; padding: 3px;
            }
            .fundoMenu{
                border: 1px solid; border-radius: 5px; 
                padding-left: 3px; padding-right: 3px; 
                background-color: #BDD2FF;
            }
            .bordaRed{
                border-color: red;
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
                            catch(exc){
                                alert("Esse browser não tem recursos para uso do Ajax");
                                ajax = null;
                            }
                        }
                    }
                }
                $(document).ready(function(){
                    $("#dataIniAgua").mask("99/99/9999");
                    $("#dataIniEletric").mask("99/99/9999");
                    $("#dataIniEletric2").mask("99/99/9999");
                    $("#dataIniEletric3").mask("99/99/9999");
                    $("#cardiretoria").load("modulos/config/carDir.php");
                    $("#relmenu").load("modulos/config/relMenu.php"); // para editar menu
                    $("#carGruposEscala").load("modulos/config/carGrupos.php");
                    $("#carCheckListLRO").load("modulos/config/carckListLRO.php");
                    
                });

                function salvaParam(Valor, Param){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=salvaParam&param="+Param+"&valor="+Valor, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }else{

                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }
                function salvaPrazoDel(Valor, Param){
                    if(parseInt(Valor) === 1000){
                        Texto = "Confirma interromper a rotina de apagar lançamentos antigos?";
                    }else{
                        Texto = "Confirma eliminar dos arquivos os lançamentos <br>com mais de "+Valor+" anos? ";
                    }
                    $.confirm({
                        title: 'Apagar',
                        content: Texto,
                        autoClose: 'Não|15000',
                        draggable: true,
                        buttons: {
                            Sim: function () {
                                ajaxIni();
                                if(ajax){
                                    ajax.open("POST", "modulos/config/registr.php?acao=salvaParam&param="+Param+"&valor="+Valor, true);
                                    ajax.onreadystatechange = function(){
                                        if(ajax.readyState === 4 ){
                                            if(ajax.responseText){
//alert(ajax.responseText);
                                                Resp = eval("(" + ajax.responseText + ")");
                                                if(parseInt(Resp.coderro) > 0){
                                                    alert("Houve erro ao salvar");
                                                }
                                            }
                                        }
                                    };
                                    ajax.send(null);
                                }
                            },
                            Não: function () {
                                document.getElementById("valorprazodel").value = document.getElementById("guardaPrazoDel").value;
                            }
                        }
                    })
                }

                function MarcaAdm(obj){
                    if(obj.checked === true){
                        Valor = 1;
                    }else{
                        Valor = 0;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=salvaAdm&valor="+Valor+"&caixa="+obj.value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }

                function salvaLeitIniAgua(Valor){
                    if(document.getElementById("valoriniagua").value === ""){
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=valorleituraAgua&valor="+document.getElementById("valoriniagua").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }else{
                                        alert("Valor anotado.");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }
                function salvaDataIniAgua(Valor){
                    if(document.getElementById("dataIniAgua").value === ""){
                        return false;
                    }
                    if(!validaData(Valor)){
                        $.confirm({
                            title: 'Atenção!',
                            content: 'A data está incorreta: '+Valor,
                            draggable: true,
                            buttons: {
                                OK: function(){
                                    document.getElementById("dataIniAgua").value = document.getElementById("guardaData").value;
                                }
                            }
                        });
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=dataleituraAgua&valor="+encodeURIComponent(document.getElementById("dataIniAgua").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }else{
                                        alert("Valor anotado.");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }
                function salvaLeitIniEletric(Valor, Num){
                    if(document.getElementById("dataIniAgua").value === ""){
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=valorleituraEletric&numero="+Num+"&valor="+encodeURIComponent(Valor), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }else{
                                        alert("Valor anotado.");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }
                function salvaDataIniEletric(Valor, Num){
                    if(!validaData(Valor)){
                        $.confirm({
                            title: 'Atenção!',
                            content: 'A data está incorreta: '+Valor,
                            draggable: true,
                            buttons: {
                                OK: function(){
                                    if(Num = 1){
                                        document.getElementById("dataIniEletric").value = document.getElementById("guardaData").value;
                                    }
                                    if(Num = 2){
                                        document.getElementById("dataIniEletric2").value = document.getElementById("guardaData").value;
                                    }
                                    if(Num = 3){
                                        document.getElementById("dataIniEletric3").value = document.getElementById("guardaData").value;
                                    }
                                }
                            }
                        });
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=dataleituraEletric&numero="+Num+"&valor="+encodeURIComponent(Valor), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }else{
                                        alert("Valor anotado.");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }

                function zeraAgua(){
                    $.confirm({
                    title: 'Apagar',
                    content: 'Confirma apagar todos os lançamentos do Controle do Consumo de Água?  Não haverá possibilidade de recuperação. Continua?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            zeraAguaDef();
                        },
                        Não: function () {
                        }
                    }
                });
            }
            function zeraAguaDef(){
                $.confirm({
                    title: 'Tem certeza?',
                    content: 'Tem certeza que quer apagar todos os lançamentos?  Não haverá possibilidade de recuperação. Continua?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/config/registr.php?acao=apagaAgua", true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                alert("Arquivos zerados.")
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
            function zeraEletric(Num, Opr){
                $.confirm({
                    title: 'Apagar Coleção - '+ Opr,
                    content: 'Confirma apagar todos os lançamentos do Controle do Consumo de Energia Elétrica?  Não haverá possibilidade de recuperação. Continua?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            zeraEletricDef(Num);
                        },
                        Não: function () {
                        }
                    }
                });
            }
            function zeraEletricDef(Num){
                $.confirm({
                    title: 'Tem certeza?',
                    content: 'Tem certeza que quer apagar todos os lançamentos?  Não haverá possibilidade de recuperação. Continua?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/config/registr.php?acao=apagaEletric&numero="+Num, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                alert("Arquivos zerados.")
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
            function carregaModal(Cod){
                document.getElementById("guardacodsetor").value = Cod;
                document.getElementById("mudou").value = "0";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=buscadir&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) > 0){
                                    alert("Houve erro ao salvar");
                                }else{
                                    document.getElementById("sigladir").value = Resp.sigla;
                                    document.getElementById("descdir").value = Resp.desc;
                                    if(parseInt(Resp.ativo) === 1){
                                        document.getElementById("atividade1").checked = true;
                                    }else{
                                        document.getElementById("atividade2").checked = true;
                                    }
                                    document.getElementById("guardaAtiv").value = Resp.ativo;
                                    $("#relausuarios").load("modulos/config/relDir.php?codigo="+Cod); // está em relDir.php
                                    document.getElementById("relacmodalDir").style.display = "block"; // está em carDir.php
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function insModalDir(){
                document.getElementById("sigladir").value = "";
                document.getElementById("descdir").value = "";
                document.getElementById("atividade1").checked = true;
                document.getElementById("guardaAtiv").value = 1;
                document.getElementById("mudou").value = 1;
                document.getElementById("relausuarios").innerHTML = "";
                document.getElementById("relacmodalDir").style.display = "block"; // está em carDir.php
            }

            function carregaModalGrupos(Cod){
                document.getElementById("mudou").value = "0";
                document.getElementById("guardacodgrupo").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/escala/salvaEsc.php?acao=buscaGrupo&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("siglagrupo").value = Resp.siglagrupo;
                                    document.getElementById("nomegrupo").value = Resp.descgrupo;
                                    document.getElementById("descgrupo").value = Resp.descescala;
//                                    document.getElementById("selecTurnos").value = Resp.turnos;
                                    $("#relusugrupo").load("modulos/config/relGrupo.php?codigo="+Cod); // está em relGrupo.php
                                    document.getElementById("relacEditaGrupos").style.display = "block";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
                
            }
            function inserirGrupo(){
                document.getElementById("guardacodgrupo").value = 0;
                document.getElementById("siglagrupo").value = "";
                document.getElementById("nomegrupo").value = "";
                document.getElementById("descgrupo").value = "";
//                document.getElementById("selecTurnos").value = "1";
                document.getElementById("relusugrupo").innerHTML = "";
                document.getElementById("relacEditaGrupos").style.display = "block";
            }
            function salvaGrupo(){
                if(document.getElementById("mudou").value != "0"){
                    if(document.getElementById("siglagrupo").value == ""){
                        document.getElementById("siglagrupo").focus();
                        return false;
                    }
//                    if(document.getElementById("nomegrupo").value == ""){
//                        document.getElementById("nomegrupo").focus();
//                        return false;
//                    }
//                    if(document.getElementById("selecTurnos").value == ""){
//                        document.getElementById("selecTurnos").focus();
//                        return false;
//                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=salvaGrupo&codigo="+document.getElementById("guardacodgrupo").value
                        +"&siglagrupo="+document.getElementById("siglagrupo").value
                        +"&nomegrupo="+document.getElementById("nomegrupo").value
                        +"&descgrupo="+document.getElementById("descgrupo").value
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
                                            $.confirm({
                                                title: 'Erro!',
                                                content: 'Grupo já existe.',
                                                draggable: true,
                                                buttons: {
                                                    OK: function(){}
                                                }
                                            });
                                        }else{
                                            $("#carGruposEscala").load("modulos/config/carGrupos.php");
                                            document.getElementById("relacEditaGrupos").style.display = "none";
                                        }
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("relacEditaGrupos").style.display = "none";
                }
            }

            function apagaGrupo(Cod){
                $.confirm({
                    title: 'Apagar',
                    content: 'Confirma apagar este grupo e suas escalas?<br>Não haverá possibilidade de recuperação.<br>Continua?',
                    autoClose: 'Não|15000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/escaladaf/salvaEscDaf.php?acao=apagaGrupo&codigo="+Cod, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) > 0){
                                                alert("Houve erro ao salvar");
                                            }else{
                                                $("#carGruposEscala").load("modulos/config/carGrupos.php");
                                            }
                                        }
                                    }
                                };
                                ajax.send(null);
                            }
                        },
                        Não: function () {
                            document.getElementById("valorprazodel").value = document.getElementById("guardaPrazoDel").value;
                        }
                    }
                })               
            }

            function salvaModalDir(){
                if(parseInt(document.getElementById("mudou").value) === 1){
                    if(document.getElementById("sigladir").value === ""){
                        $('#mensagemDir').fadeIn("slow");
                        document.getElementById("mensagemDir").innerHTML = "Preencha o campo <u>Sigla</u> da Diretoria/Assessoria";
                        $('#mensagemDir').fadeOut(3000);
                        return false;
                    }
                    if(document.getElementById("descdir").value === ""){
                        $('#mensagemDir').fadeIn("slow");
                        document.getElementById("mensagemDir").innerHTML = "Preencha o campo <u>Descrição</u> da Diretoria/Assessoria";
                        $('#mensagemDir').fadeOut(3000);
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=salvadir&codigo="+document.getElementById("guardacod").value
                        +"&sigladir="+document.getElementById("sigladir").value
                        +"&descdir="+document.getElementById("descdir").value
                        +"&ativo="+document.getElementById("guardaAtiv").value
                        , true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }else{
                                        $("#cardiretoria").load("modulos/config/carDir.php");
                                        document.getElementById("relacmodalDir").style.display = "none";
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("relacmodalDir").style.display = "none";
                }
            }

            function carregaCheckList(Cod){
                document.getElementById("guardacod").value = Cod;
                document.getElementById("mudou").value = "0";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=buscackList&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) > 0){
                                    alert("Houve erro ao salvar");
                                }else{
                                    document.getElementById("numitem").value = Resp.itemnum;
                                    document.getElementById("descitem").value = Resp.itemcklist;
                                    if(parseInt(Resp.ativo) === 1){
                                        document.getElementById("atividadecklist1").checked = true;
                                    }else{
                                        document.getElementById("atividadecklist2").checked = true;
                                    }
                                    document.getElementById("guardaAtivCkList").value = Resp.ativo;
                                    document.getElementById("relacmodalCkList").style.display = "block"; // está em carckListLRO.php
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function insModalCkList(){
                document.getElementById("guardacod").value = 0;
                document.getElementById("mudou").value = "0";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=buscanumckList", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) > 0){
                                    alert("Houve erro ao salvar");
                                }else{
                                    document.getElementById("numitem").value = Resp.proxitem;
                                    document.getElementById("descitem").value = "";
                                    document.getElementById("atividadecklist1").checked = true;
                                    document.getElementById("guardaAtivCkList").value = 1;
                                    document.getElementById("relacmodalCkList").style.display = "block"; // está em carckListLRO.php
                                    document.getElementById("descitem").focus();
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaModalCkList(){
                if(parseInt(document.getElementById("mudou").value) === 1){
                    if(document.getElementById("descitem").value === ""){
                        $('#mensagemCkList').fadeIn("slow");
                        document.getElementById("mensagemCkList").innerHTML = "Preencha o campo acima";
                        $('#mensagemCkList').fadeOut(3000);
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=salvaCkList&codigo="+document.getElementById("guardacod").value
                        +"&numitem="+document.getElementById("numitem").value
                        +"&descitem="+document.getElementById("descitem").value
                        +"&ativo="+document.getElementById("guardaAtivCkList").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }else{
                                        $("#carCheckListLRO").load("modulos/config/carckListLRO.php");
                                        document.getElementById("relacmodalCkList").style.display = "none";
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("relacmodalCkList").style.display = "none";
                }
            }

            function abreEditMenu(Cod){
                document.getElementById("mudou").value = 0;
                document.getElementById("guardaItemMenu").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=buscaMenuOpr&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                document.getElementById("nomeMenu").innerHTML = Resp.valor;
                                document.getElementById("novoNome").value = Resp.valor;
                                document.getElementById("relacEditMenuOpr").style.display = "block";
                            }
                        }
                    };
                    ajax.send(null);
                }
            } 

            function salvaMenuOpr(){
                if(parseInt(document.getElementById("mudou").value) === 1){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=salvamenuOpr&codigo="+document.getElementById("guardaItemMenu").value
                        +"&valor="+encodeURIComponent(document.getElementById("novoNome").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    $("#relmenu").load("modulos/config/relMenu.php"); 
                                    document.getElementById("relacEditMenuOpr").style.display = "none";
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("relacEditMenuOpr").style.display = "none";
                }
            }

            function guardaData(Valor){
                document.getElementById("guardaData").value = Valor;
            }

            function salvaAtivDir(Valor){
                document.getElementById("guardaAtiv").value = Valor;
                document.getElementById("mudou").value = "1";
            }
            function salvaAtivCkList(Valor){
                document.getElementById("guardaAtivCkList").value = Valor;
                document.getElementById("mudou").value = "1";
            }
            function fechaModalDir(){
                document.getElementById("relacmodalDir").style.display = "none";
            }
            function fechaModalckList(){
                document.getElementById("relacmodalCkList").style.display = "none";
            }
            function fechaEditMenuOpr(){
                document.getElementById("relacEditMenuOpr").style.display = "none";
            }
            function fechaEditaGrupos(){
                document.getElementById("relacEditaGrupos").style.display = "none";
            }

            function modif(){ // assinala se houve qualquer modificação nos campos do modal durante a edição para evitar salvar desnecessariamente
                document.getElementById("mudou").value = "1";
            }
            function foco(id){
                document.getElementById(id).focus();
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
        </script>
    </head>
    <body>
        <?php
            require_once("abrealas.php");
            $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'poslog'");
            $rowSis = pg_num_rows($rsSis);
            if($rowSis == 0){
                echo "Sem contato com os arquivos do sistema. Informe à ATI.";
                return false;
            }
            $Menu1 = escMenu($Conec, $xProj, 1); //abre alas
            $Menu2 = escMenu($Conec, $xProj, 2); //abre alas
            $Menu3 = escMenu($Conec, $xProj, 3); //abre alas

            $rsSis = pg_query($Conec, "SELECT admvisu, admedit, admcad, insevento, editevento, instarefa, edittarefa, insramais, editramais, instelef, edittelef, 
            editpagina, insarq, insaniver, editaniver, instroca, edittroca, insocor, editocor, insleituraagua, editleituraagua, 
            TO_CHAR(datainiagua, 'DD/MM/YYYY'), valoriniagua, insleituraeletric, editleituraeletric, TO_CHAR(datainieletric, 'DD/MM/YYYY'), valorinieletric, inslro, editlro, insbens, editbens, 
            prazodel, vertarefa, verarquivos, TO_CHAR(datainieletric2, 'DD/MM/YYYY'), valorinieletric2, TO_CHAR(datainieletric3, 'DD/MM/YYYY'), valorinieletric3, editpagini 
            FROM ".$xProj.".paramsis WHERE idPar = 1");
            $ProcSis = pg_fetch_row($rsSis);
            $admVisu = $ProcSis[0]; // admVisu - administrador visualiza usuários
            $admEdit = $ProcSis[1]; // admEdit - administrador edita usuários
            $admCad = $ProcSis[2];  // admCad - administrador cadastra usuários
            $DataIniAgua = $ProcSis[21]; // controle de consumo de água - leitura do hidrômetro
            $ValorIniAgua = $ProcSis[22];  // controle de consumo de água - data inicial
            $DataIniEletric = $ProcSis[25]; // controle de consumo de eletricidade
            if($ProcSis[25] == "31/12/3000"){
                $DataIniEletric = "";    
            }
            $ValorIniEletric = $ProcSis[26]; 

            $DataIniEletric2 = $ProcSis[34]; // controle de consumo de eletricidade - Claro
            if($ProcSis[34] == "31/12/3000"){
                $DataIniEletric2 = "";    
            }
            $ValorIniEletric2 = $ProcSis[35]; 
            $DataIniEletric3 = $ProcSis[36]; // controle de consumo de eletricidade - Oi
            if($ProcSis[36] == "31/12/3000"){
                $DataIniEletric3 = "";
            }
            $ValorIniEletric3 = $ProcSis[37]; 

            $insEvento = $ProcSis[3];   // insEvento - inserção de eventos no calendário
            $rs1 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insEvento");
            $Proc1 = pg_fetch_row($rs1);
            $nomeInsEvento = $Proc1[0];

            $editEvento = $ProcSis[4];   // editEvento - edição de eventos no calendário
            $rs2 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editEvento");
            $Proc2 = pg_fetch_row($rs2);
            $nomeEditEvento = $Proc2[0];

            $insLeituraAgua = $ProcSis[19];   // insLeitura - inserção de leitura do hidrômetro
            $rs1 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insLeituraAgua");
            $Proc1 = pg_fetch_row($rs1);
            $nomeInsLeituraAgua = $Proc1[0];

            $editAgua = $ProcSis[20];   // editLeitura - edição de leitura
            $rs2 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editAgua");
            $Proc2 = pg_fetch_row($rs2);
            $nomeEditAgua = $Proc2[0];

            $insLeituraEletric = $ProcSis[23];   // insLeitura - inserção de leitura do medidor
            $rs1 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insLeituraEletric");
            $Proc1 = pg_fetch_row($rs1);
            $nomeInsLeituraEletric = $Proc1[0];

            $editEletric = $ProcSis[24];   // editLeitura - edição de leitura
            $rs2 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editEletric");
            $Proc2 = pg_fetch_row($rs2);
            $nomeEditEletric = $Proc2[0];

            $insTarefa = $ProcSis[5];   // insTarefa - inserção de tarefas
            $rs3 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insTarefa");
            $Proc3 = pg_fetch_row($rs3);
            $nomeInsTarefa = $Proc3[0];

            $editTarefa = $ProcSis[6];   // editTarefa - edição de tarefas
            $rs4 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editTarefa");
            $Proc4 = pg_fetch_row($rs4);
            $nomeEditTarefa = $Proc4[0];
            
            $insRamais = $ProcSis[7];   // insRamais - edição de ramais internos
            $rs5 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insRamais");
            $Proc5 = pg_fetch_row($rs5);
            $nomeInsRamais = $Proc5[0];

            $editRamais = $ProcSis[8];   // editRamais - edição de ramais internos
            $rs6 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editRamais");
            $Proc6 = pg_fetch_row($rs6);
            $nomeEditRamais = $Proc6[0];

            $insTelef = $ProcSis[9];   // insTelef - edição de telefones
            $rs7 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insTelef");
            $Proc7 = pg_fetch_row($rs7);
            $nomeInsTelef = $Proc7[0];

            $editTelef = $ProcSis[10];   // editTelef - edição de telefones
            $rs8 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editTelef");
            $Proc8 = pg_fetch_row($rs8);
            $nomeEditTelef = $Proc8[0];

            $editPagina = $ProcSis[11];   // editPagina - edição das páginas das diretorias/assessorias
            $rs9 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editPagina");
            $Proc9 = pg_fetch_row($rs9);
            $nomeEditPagina = $Proc9[0];

            $insArq = $ProcSis[12];   // insArq - edição das páginas das diretorias/assessorias
            $rs10 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insArq");
            $Proc10 = pg_fetch_row($rs10);
            $nomeInsArq = $Proc10[0];

            $insAniver = $ProcSis[13];   // insAniver - edição das páginas das diretorias/assessorias
            $rs11 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insAniver");
            $Proc11 = pg_fetch_row($rs11);
            $nomeInsAniver = $Proc11[0];

            $editAniver = $ProcSis[14];   // editAniver - edição de telefones
            $rs12 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editAniver");
            $Proc12 = pg_fetch_row($rs12);
            $nomeEditAniver = $Proc12[0];
            
            $insTroca = $ProcSis[15];   // insTroca - edição de trocas
            $rs13 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insTroca");
            $Proc13 = pg_fetch_row($rs13);
            $nomeInsTroca = $Proc13[0];

            $editTroca = $ProcSis[16];   // editTroca - edição de trocas
            $rs14 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editTroca");
            $Proc14 = pg_fetch_row($rs14);
            $nomeEditTroca = $Proc14[0];

            $insOcor = $ProcSis[17];   // insOcor - registro de ocorrências
            $rs15 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insOcor");
            $Proc15 = pg_fetch_row($rs15);
            $nomeInsOcor = $Proc15[0];

            $editOcor = $ProcSis[18];   // editOcor
            $rs16 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editOcor");
            $Proc16 = pg_fetch_row($rs16);
            $nomeEditOcor = $Proc16[0];

            $insLro = $ProcSis[27];   // insLro - registro no LRO
            $rs17 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insLro");
            $Proc17 = pg_fetch_row($rs17);
            $nomeInsLro = $Proc17[0];

            $editLro = $ProcSis[28];   // editro - registro no LRO
            $rs18 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insLro");
            $Proc18 = pg_fetch_row($rs18);
            $nomeEditLro = $Proc18[0];

            $insBens = $ProcSis[29];   // Bens Achados
            $rs19 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insBens");
            $Proc19 = pg_fetch_row($rs19);
            $nomeInsBens = $Proc19[0];

            $editBens = $ProcSis[30];
            $rs20 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editBens");
            $Proc20 = pg_fetch_row($rs20);
            $nomeEditBens = $Proc20[0];

            $editPagIni = $ProcSis[38];
            $rs21 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editPagIni");
            $Proc21 = pg_fetch_row($rs21);
            $nomeEditPagIni = $Proc21[0];


            $PrazoDel = $ProcSis[31];   // prazo para apagar registros antigos
            $VerTarefa = $ProcSis[32];  // parâmetro para liberar tarefas para todos verem
            $VerArquivos = $ProcSis[33];  // parâmetro para liberar a visualização dos arquivos carregados em cada diretoria


            $OpAdmInsEv = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");
            $OpAdmEditEv = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");
            
            $OpAdmInsAgua = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");
            $OpAdmEditAgua = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");

            $OpAdmInsEletric = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");
            $OpAdmEditEletric = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");

            $OpAdmInsTar = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");
            $OpAdmEditTar = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");

            $OpAdmInsRamais = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");
            $OpAdmEditRamais = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");

            $OpAdmInsTelef = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");
            $OpAdmEditTelef = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");

            $OpAdmEditPag = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");
            $OpAdmInsArq = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");

            $OpAdmEditPagIni = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");

            $OpAdmInsTroca = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");
            $OpAdmEditTroca = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");

            $OpAdmInsOcor = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");
            $OpAdmEditOcor = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");

            $OpAdmInsLro = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");
            $OpAdmEditLro = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");

            $OpAdmInsBens = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");
            $OpAdmEditBens = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");

            $TempoInat  = parAdm("tempoinat", $Conec, $xProj); // tempo de ociosidade
            $DescInat = "";
            switch ($TempoInat){
                case 0:
                    $DescInat = "Ilimitado";
                    break;
                case 900:
                    $DescInat = "15 min";
                    break;
                case 1800:
                    $DescInat = "30 min";
                    break;
                case 3600:
                    $DescInat = "01 hora";
                    break;
                case 7200:
                    $DescInat = "02 horas";
                    break;
                case 10800:
                    $DescInat = "03 horas";
                    break;
                case 14400:
                    $DescInat = "04 horas";
                    break;
                case 18000:
                    $DescInat = "05 horas";
                    break;
                case 21600:
                    $DescInat = "06 horas";
                    break;
            }
        ?>
        <input type="hidden" id="guardacod" value="0" /> <!-- id ocorrência -->
        <input type="hidden" id="mudou" value="0" /> <!-- valor 1 quando houver mudança em qualquer campo do modal -->
        <input type="hidden" id="guardaAtiv" value="0" />
        <input type="hidden" id="guardaAtivCkList" value="0" />
        <input type="hidden" id="guardaData" value="0" />
        <input type="hidden" id="guardaItemMenu" value="0" />
        <input type="hidden" id="guardaPrazoDel" value="<?php echo $PrazoDel; ?>" />
        <input type="hidden" id="guardacodgrupo" value="0" />

        <div style="margin: 0 auto; margin-top: 40px; padding: 20px; border: 2px solid blue; border-radius: 15px; width: 70%; min-height: 200px;">
            <div style="text-align: center;">
                <h4>Parâmetros do Sistema</h4>
            </div>
            <div style="margin: 5px; margin-left: 200px; margin-right: 200px; border: 1px solid; border-radius: 10px; text-align: center; padding: 15px;">
                <label style="color: gray; font-size: .8em;">Desconectar após </label>
                <select id="tempoocioso" onchange="salvaParam(value, 'tempoinat');" style="font-size: 1rem;" title="Selecione o tempo apropriado.">
                    <option value="<?php echo $TempoInat; ?>"><?php echo $DescInat; ?></option>
                    <option value="0">Ilimitado</option>
                    <option value="900">15 min</option>
                    <option value="1800">30 min</option>
                    <option value="3600">01 hora</option>
                    <option value="7200">02 horas</option>
                    <option value="10800">03 horas</option>
                    <option value="14400">04 horas</option>
                    <option value="18000">05 horas</option>
                    <option value="21600">06 horas</option>
                </select>
                <label id="labeltempoocioso" style="color: gray; font-size: .8em;"> de tempo ocioso.</label>
            </div>
            
<!-- Achados e Perdidos  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Achados e Perdidos</b>: <label style="color: gray; font-size: .8em;"> Nível mínimo mais a marca no usuário autorizam a inserção.</label><br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR Achados e Perdidos:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insbens');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insBens; ?>"><?php echo $nomeInsBens; ?></option>
                            <?php 
                            if($OpAdmInsBens){
                                while ($Opcoes = pg_fetch_row($OpAdmInsBens)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR Achados e Perdidos:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editBens');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editBens; ?>"><?php echo $nomeEditBens; ?></option>
                            <?php 
                            if($OpAdmEditBens){
                                while ($Opcoes = pg_fetch_row($OpAdmEditBens)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

<!-- Calendário  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Calendário</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR eventos:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insEvento');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insEvento; ?>"><?php echo $nomeInsEvento; ?></option>
                            <?php 
                            if($OpAdmInsEv){
                                while ($Opcoes = pg_fetch_row($OpAdmInsEv)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR eventos:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editEvento');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editEvento; ?>"><?php echo $nomeEditEvento; ?></option>
                            <?php 
                            if($OpAdmEditEv){
                                while ($Opcoes = pg_fetch_row($OpAdmEditEv)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

<!-- Leitura Hidrômetro  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Controle do Consumo de Água - Leitura do Hidrômetro</b>: <label style="color: gray; font-size: .8em;">Nível mínimo mais a marca no usuário autorizam a inserção.</label><br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR leitura:</td>
                        <td style="padding-left: 5px;">
                        <select id="selectLeituraAgua" onchange="salvaParam(value, 'insleituraagua');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insLeituraAgua; ?>"><?php echo $nomeInsLeituraAgua; ?></option>
                            <?php 
                            if($OpAdmInsAgua){
                                while ($Opcoes = pg_fetch_row($OpAdmInsAgua)){ ?>
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
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR leitura:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editleituraagua');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editAgua; ?>"><?php echo $nomeEditAgua; ?></option>
                            <?php 
                            if($OpAdmEditAgua){
                                while ($Opcoes = pg_fetch_row($OpAdmEditAgua)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align: right; font-size: 80%; padding-right: 3px;">Data Inicial:</td>
                        <td style="text-align: left; font-size: 80%; padding-left: 3px;"><input type="text" id="dataIniAgua" value="<?php echo $DataIniAgua; ?>" onchange="salvaDataIniAgua(value);" onclick="guardaData(value);" style="width: 90px; text-align: center;">
                            <label style="color: gray; font-size: .8em;"><- Este é o dia da primeira leitura</label>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right; font-size: 80%; padding-right: 3px;">Leitura Inicial:</td>
                        <td style="text-align: left; font-size: 80%; padding-left: 3px;"><input type="text" id="valoriniagua" value="<?php echo $ValorIniAgua; ?>" onchange="salvaLeitIniAgua(value);" style="width: 90px; text-align: center;"></td>
                    </tr>
                </table>
                <div style="text-align: right;">
                <button class="botpadr" onclick="zeraAgua();">Apagar Tudo</button></div>
            </div>

<!-- Leitura Eletricidade  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Controle do Consumo de Eletricidade - Leitura do Medidor</b>: <label style="color: gray; font-size: .8em;">Nível mínimo mais a marca no usuário autorizam a inserção.</label><br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR leitura:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insleituraeletric');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insLeituraEletric; ?>"><?php echo $nomeInsLeituraEletric; ?></option>
                            <?php 
                            if($OpAdmInsEletric){
                                while ($Opcoes = pg_fetch_row($OpAdmInsEletric)){ ?>
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
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Nível mínimo para EDITAR leitura:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editleituraeletric');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editEletric; ?>"><?php echo $nomeEditEletric; ?></option>
                            <?php 
                            if($OpAdmEditEletric){
                                while ($Opcoes = pg_fetch_row($OpAdmEditEletric)){ ?>
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
                        <td style="text-align: right; font-size: 80%; padding-right: 3px; padding-top: 10px;">Data Inicial<?php echo " - ".$Menu1.":"; ?></td>
                        <td colspan="2" style="text-align: left; font-size: 80%; padding-left: 3px; padding-top: 10px;"><input type="text" id="dataIniEletric" value="<?php echo $DataIniEletric; ?>" onchange="salvaDataIniEletric(value, 1);" onclick="guardaData(value);" style="width: 90px; text-align: center;">
                            <label style="color: gray; font-size: .8em;"><- Este é o dia da primeira leitura</label>
                        </td>
                        
                    </tr>
                    <tr>
                        <td style="text-align: right; font-size: 80%; padding-right: 3px;">Leitura Inicial<?php echo " - ".$Menu1.":"; ?></td>
                        <td style="text-align: left; font-size: 80%; padding-left: 3px;"><input type="text" id="valorIniEletric" value="<?php echo $ValorIniEletric; ?>" onchange="salvaLeitIniEletric(value, 1);" style="width: 90px; text-align: center;"></td>
                        <td><div style="text-align: left; font-size: 80%;"><button class="botpadr" onclick="zeraEletric(1, '<?php echo $Menu1; ?>');">Apagar Tudo<?php echo " - ".$Menu1; ?></button></div></td>
                    </tr>
                    <tr>
                        <td style="text-align: right; font-size: 80%; padding-right: 3px; padding-top: 10px;">Data Inicial<?php echo " - ".$Menu2.":"; ?></td>
                        <td colspan="2" style="text-align: left; font-size: 80%; padding-left: 3px; padding-top: 10px;"><input type="text" id="dataIniEletric2" value="<?php echo $DataIniEletric2; ?>" onchange="salvaDataIniEletric(value, 2);" onclick="guardaData(value);" style="width: 90px; text-align: center;">
                            <label style="color: gray; font-size: .8em;"><- Este é o dia da primeira leitura</label>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right; font-size: 80%; padding-right: 3px;">Leitura Inicial<?php echo " - ".$Menu2.":"; ?></td>
                        <td style="text-align: left; font-size: 80%; padding-left: 3px;"><input type="text" id="valorIniEletric2" value="<?php echo $ValorIniEletric2; ?>" onchange="salvaLeitIniEletric(value, 2);" style="width: 90px; text-align: center;"></td>
                        <td><div style="text-align: left; font-size: 80%;"><button class="botpadr" onclick="zeraEletric(2, '<?php echo $Menu2; ?>');">Apagar Tudo<?php echo " - ".$Menu2; ?></button></div></td>
                    </tr>
                    <tr>
                        <td style="text-align: right; font-size: 80%; padding-right: 3px; padding-top: 10px;">Data Inicial<?php echo " - ".$Menu3.":"; ?></td>
                        <td colspan="2" style="text-align: left; font-size: 80%; padding-left: 3px; padding-top: 10px;"><input type="text" id="dataIniEletric3" value="<?php echo $DataIniEletric3; ?>" onchange="salvaDataIniEletric(value, 3);" onclick="guardaData(value);" style="width: 90px; text-align: center;">
                            <label style="color: gray; font-size: .8em;"><- Este é o dia da primeira leitura</label>
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: right; font-size: 80%; padding-right: 3px;">Leitura Inicial<?php echo " - ".$Menu3.":"; ?></td>
                        <td style="text-align: left; font-size: 80%; padding-left: 3px;"><input type="text" id="valorIniEletric3" value="<?php echo $ValorIniEletric3; ?>" onchange="salvaLeitIniEletric(value, 3);" style="width: 90px; text-align: center;"></td>
                        <td><div style="text-align: left; font-size: 80%;"><button class="botpadr" onclick="zeraEletric(3, '<?php echo $Menu3; ?>');">Apagar Tudo<?php echo " - ".$Menu3; ?></button></div></td>
                    </tr>
                </table>
            </div>

<!-- Páginas Diretorias/Assessorias -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Páginas das Diretorias/Assessorias</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR arquivos:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insarq');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $insArq; ?>"><?php echo $nomeInsArq; ?></option>
                            <?php 
                            if($OpAdmInsArq){
                                while ($Opcoes = pg_fetch_row($OpAdmInsArq)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Nível mínimo para EDITAR página:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editpagina');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editPagina; ?>"><?php echo $nomeEditPagina; ?></option>
                            <?php 
                            if($OpAdmEditPag){
                                while ($Opcoes = pg_fetch_row($OpAdmEditPag)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td style="font-size: 80%; padding-top: 5px;">Quem pode ver os arquivos carregados:</td>
                        <td style="text-align: right; padding-top: 5px;">
                            <input type="radio" name="verarquivos" id="verarquivos1" value="1" <?php if($VerArquivos == 1){echo "CHECKED";} ?> title="Todos podem ver os arquivos" onclick="salvaParam(value, 'verarquivos');"><label for="verarquivos1" style="font-size: 12px; padding-left: 3px;"> Todos</label>
                            <input type="radio" name="verarquivos" id="verarquivos2" value="2" <?php if($VerArquivos == 2){echo "CHECKED";} ?> title="Só os usuários do setor" onclick="salvaParam(value, 'verarquivos');"><label for="verarquivos2" style="font-size: 12px; padding-left: 3px;"> Só usuários da Diretoria/Assessoria</label>
                        </td>
                    </tr>

                </table>
            </div>

<!-- Página Inicial -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Página Inicial</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para EDITAR página:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editpagini');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editPagIni; ?>"><?php echo $nomeEditPagIni; ?></option>
                            <?php 
                            if($OpAdmEditPagIni){
                                while ($Opcoes = pg_fetch_row($OpAdmEditPagIni)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

<!-- Ramais  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Ramais Internos</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR ramais:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insramais');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insRamais; ?>"><?php echo $nomeInsRamais; ?></option>
                            <?php 
                            if($OpAdmInsRamais){
                                while ($Opcoes = pg_fetch_row($OpAdmInsRamais)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR ramais:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editramais');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editRamais; ?>"><?php echo $nomeEditRamais; ?></option>
                            <?php 
                            if($OpAdmEditRamais){
                                while ($Opcoes = pg_fetch_row($OpAdmEditRamais)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

<!-- Ocorrrências  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Registro de Ocorrêncas - LRO</b>: <label style="color: gray; font-size: .8em;">Usuários precisam estar autorizados no cadastro de usuários</label><br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR ocorrência:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insocor');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $insOcor; ?>"><?php echo $nomeInsOcor; ?></option>
                            <?php 
                            if($OpAdmInsOcor){
                                while ($Opcoes = pg_fetch_row($OpAdmInsOcor)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Nível mínimo para VERIFICAR ocorrência:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editocor');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editOcor; ?>"><?php echo $nomeEditOcor; ?></option>
                            <?php 
                            if($OpAdmEditOcor){
                                while ($Opcoes = pg_fetch_row($OpAdmEditOcor)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

<!-- Tarefas  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Tarefas</b>: <label style="color: gray; font-size: .8em;">Cada nível insere tarefa para seu nível administrativo ou nível inferior. Superusuário vê todas.</label><br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR tarefas:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'instarefa');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insTarefa; ?>"><?php echo $nomeInsTarefa; ?></option>
                            <?php 
                            if($OpAdmInsTar){
                                while ($Opcoes = pg_fetch_row($OpAdmInsTar)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR tarefas:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'edittarefa');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editTarefa; ?>"><?php echo $nomeEditTarefa; ?></option>
                            <?php 
                            if($OpAdmEditTar){
                                while ($Opcoes = pg_fetch_row($OpAdmEditTar)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-size: 80%; padding-top: 5px;">Quem pode ver as tarefas designadas:</td>
                        <td style="text-align: right; padding-top: 5px;">
                            <input type="radio" name="vertarefa" id="vertarefa1" value="1" <?php if($VerTarefa == 1){echo "CHECKED";} ?> title="Todos os usuários podem ver as tarefas" onclick="salvaParam(value, 'vertarefa');"><label for="vertarefa1" style="font-size: 12px; padding-left: 3px; padding-right: 5px;" title="Todos os usuários podem ver as tarefas"> Todos</label>

                            <?php
                            $rsProj = pg_query($Conec, "SELECT liberaproj FROM ".$xProj.".escolhas WHERE codesc = 3");
                            $tblProj = pg_fetch_row($rsProj);
                            $LiberaProj = $tblProj[0];
                            if($LiberaProj == 1){ // libera Tarefas por Organograma
                                ?>
                                <input type="radio" name="vertarefa" id="vertarefa4" value="4" <?php if($VerTarefa == 4){echo "CHECKED";} ?> title="Visualização por níveis do organograma" onclick="salvaParam(value, 'vertarefa');"><label for="vertarefa4" style="font-size: 12px; padding-left: 3px; padding-right: 5px;" title="Visualização por níveis do Organograma"> Organograma</label>
                                <?php
                            }
                            ?>

                            <input type="radio" name="vertarefa" id="vertarefa3" value="3" <?php if($VerTarefa == 3){echo "CHECKED";} ?> title="Visualização separada por setor." onclick="salvaParam(value, 'vertarefa');"><label for="vertarefa3" style="font-size: 12px; padding-left: 3px; padding-right: 5px;" title="Visualização separada por setor. Pode inserir tarefa para usuários de outros setores"> Setores</label>
                            <input type="radio" name="vertarefa" id="vertarefa2" value="2" <?php if($VerTarefa == 2){echo "CHECKED";} ?> title="Só o mandante e o executante podem ver as tarefas" onclick="salvaParam(value, 'vertarefa');"><label for="vertarefa2" style="font-size: 12px; padding-left: 3px; padding-right: 5px;" title="Só o mandante e o executante podem ver as tarefas"> Só Mandante e Executante</label>
                        </td>
                    </tr>
                </table>
            </div>

<!-- Telefones  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Telefones Úteis</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR telefones:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'instelef');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insTelef; ?>"><?php echo $nomeInsTelef; ?></option>
                            <?php 
                            if($OpAdmInsTelef){
                                while ($Opcoes = pg_fetch_row($OpAdmInsTelef)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR telefones:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'edittelef');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editTelef; ?>"><?php echo $nomeEditTelef; ?></option>
                            <?php 
                            if($OpAdmEditTelef){
                                while ($Opcoes = pg_fetch_row($OpAdmEditTelef)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

<!-- Trocas  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Trocas de Objetos</b>: <label style="color: gray; font-size: .8em;">É editável pelo setor que inseriu</label><br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR trocas:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'instroca');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insTroca; ?>"><?php echo $nomeInsTroca; ?></option>
                            <?php 
                            if($OpAdmInsTroca){
                                while ($Opcoes = pg_fetch_row($OpAdmInsTroca)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR trocas:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'edittroca');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editTroca; ?>"><?php echo $nomeEditTroca; ?></option>
                            <?php 
                            if($OpAdmEditTroca){
                                while ($Opcoes = pg_fetch_row($OpAdmEditTroca)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <br><hr><br>

    
    <!-- Mostra os ítens modificáveis do menu Controles  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                <b>Itens de Menu</b>: <label style="color: gray; font-size: .8em;">Modificações aqui vão para o menu</label><br>
                <div id="relmenu"></div>
            </div>
            <br><hr><br>


    <!-- Mostra as diretorias e assessorias mais os seus usuários  -->
            <div id="cardiretoria"></div>

    <!-- Mostra os grupos para escalas mais os seus usuários  -->
            <div id="carGruposEscala"></div>

    <!-- Mostra checklist para serviço nas portarias  -->
            <div id="carCheckListLRO"></div>


        </div> <!-- Fim-->
            
            <!-- Modal para editar descrição de alguns itens do menu  -->
            <div id="relacEditMenuOpr" class="relacmodal">
                <div class="modal-content-EditMenu">
                    <span class="close" onclick="fechaEditMenuOpr();">&times;</span>
                    <h5 id="titulomodal" style="text-align: center; color: #666;">Itens do Menu</h5>
                    <table style="margin: 0 auto; width: 90%">
                        <tr>
                            <td class="etiq aDir">Menu: <label id="nomeMenu" style="text-align: center; border: 1px solid; border-radius: 5px; width: 150px;"></label></td>
                            <td class="etiq aDir">Novo Valor: </td>
                            <td><input type="text" id="novoNome" valor="" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 150px; text-align: left;"></td>
                        </tr>
                    </table>
                    <br>
                    <div style="text-align: center;">
                        <button class="resetbot" style="font-size: .9rem;" onclick="salvaMenuOpr();">Salvar</button>
                    </div>
                </div>
            </div>


        <!-- div modal edita grupos para confecção das escalas de serviço nos grupos-->
        <div id="relacEditaGrupos" class="relacmodal">
            <div class="modal-content-editGrupos">
                <span class="close" onclick="fechaEditaGrupos();">&times;</span>
                <label style="font-size: 1.2em; color: #666;">Edita Grupo</label>
                <table style="margin: 0 auto; width: 95%;">
                    <tr>
                        <td class="etiq">Sigla</td>
                        <td><input type="text" id="siglagrupo" style="width: 50%;" onchange="modif();" placeholder="Sigla" onkeypress="if(event.keyCode===13){javascript:foco('nomegrupo');return false;}"/></td>
                        <td class="etiq"></td>
                        <td>
<!--
                            <select id="selecTurnos" style="font-size: 1rem; width: 60px; text-align: centr;" onchange="modif();" title="Selecione o número de turnos para compor a escala deste grupo.">
                                <option value="0"></option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
-->
                        </td>    
                    </tr>
                    <tr>
                        <td class="etiq">Nome</td>
                        <td colspan="3"><input type="text" id="nomegrupo" style="width: 100%;" onchange="modif();" placeholder="Nome Grupo" onkeypress="if(event.keyCode===13){javascript:foco('descgrupo');return false;}"/></td>
                    </tr>
                    <tr>
                        <td class="etiq">Descrição</td>
                        <td colspan="3"><input type="text" id="descgrupo" style="width: 100%;" onchange="modif();" placeholder="Descrição" onkeypress="if(event.keyCode===13){javascript:foco('botsalvar');return false;}"/></td>
                    </tr>
                    <tr>
                        <td colspan="3"></td>
                        <td style="text-align: center; padding-top: 20px;"><div class='bSalvar corFundo' onclick='salvaGrupo()'>Salvar</div></td>
                    </tr>

                </table>
                <div id="relusugrupo" style="padding-left: 20px;"></div> <!-- Apresenta os usuários do grupo -->
            </div>
            <br><br><br>
        </div>

    <!-- Prazo para apagar registros -->
            <div style="margin: 50px; border: 3px solid red; border-radius: 20px; padding: 15px;">
                <div style="margin: 5px; padding: 15px; text-align: center;">
                    <h4>Apagar Registros Antigos</h4>
                </div>
                <table style="margin: 0 auto;">
                    <tr>
                        <td style="padding-right: 5px;"><h5>Prazo para apagar registros antigos no banco de dados:</h5> </td>
                        <td>
                            <select id="valorprazodel" onchange="salvaPrazoDel(value, 'prazodel');" style="font-size: 1rem; text-align: center;" title="Selecione um valor.">
                                <option value="<?php echo $PrazoDel; ?>"><?php echo $PrazoDel; ?></option>
                                <option value="5"> 5</option>
                                <option value="6"> 6</option>
                                <option value="7"> 7</option>
                                <option value="8"> 8</option>
                                <option value="9"> 9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                                <option value="13">13</option>
                                <option value="14">14</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="30">30</option>
                                <option value="1000">1000</option>
                            </select>
                            <label>anos</label>
                        </td>
                    </tr>
                </table>

                <label>Serão eliminados lançamentos dos arquivos de:</label>
                <div style="margin-left: 70px; padding: 5px; text-align: left;">
                    <ul>
                        <li>Registros do Livro de Registros de Ocorrências.</li>
                        <li>Registros de Achados e Perdidos.</li>
                        <li>Registros das leituras do consumo de água.</li>
                        <li>Registros das leituras do consumo de eletricidade.</li>
                        <li>Registros de manutenção dos Condicionadores de Ar.</li>
                        <li>Registros de manutenção dos Elevadores.</li>
                        <li>Evendos do calendário.</li>
                        <li>Tarefas atribuidas.</li>
                        <li>Escalas de Serviço.</li>
                    </ul>
                </div>
                <div style="text-align: center;">Este módulo é acionado uma vez por dia no primeiro login.</div>
        </div>

    </body>
</html>