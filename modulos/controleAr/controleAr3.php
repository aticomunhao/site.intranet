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
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Controle Condicionadores</title>
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
            .modal-content-Controle{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 55%; /* acertar de acordo com a tela */
                max-width: 900px;
            }
            .modal-content-InsControle{
                background: linear-gradient(180deg, white, #0099FF);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 55%; /* acertar de acordo com a tela */
                max-width: 900px;
            }
            .divbot{ /* botão upload */
                position: relative; 
                float: left;
                margin-top: -20px; 
                border: 1px solid blue;
                background-color: blue;
                color: white;
                cursor: pointer;
                border-radius: 10px; 
                padding-left: 10px; 
                padding-right: 10px;
                font-size: 80%;
            }
            .etiqCel{
                text-align: center; 
                border: 1px solid;
                border-radius: 8px;
            }
            .destacaRed{
                color: red;
            }
            .destacaPreto{
                color: black;
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
                document.getElementById("selectAno").disabled = true;
                document.getElementById("botinserir").disabled = true;
                document.getElementById("botimpr").disabled = true;
                document.getElementById("editNomeEmpr").disabled = true;
                document.getElementById("valorvisita").disabled = true;
                document.getElementById("botSalvarEditEmpr").disabled = true;


                if(parseInt(document.getElementById("guardaInsArCond").value) === 1 || parseInt(document.getElementById("guardaFiscArCond").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){
                    $("#faixacentral").load("modulos/controleAr/relAr3.php?acao=todos&ano="+document.getElementById("selectAno").value);
                    if(parseInt(document.getElementById("guardaInsArCond").value) === 0 && parseInt(document.getElementById("UsuAdm").value) < 7){ //Só fiscaliza
                        document.getElementById("botinserir").disabled = true;
                    }
                }else{
                    document.getElementById("faixaMensagem").style.display = "block";
                }

                $('#carregaTema').load('modulos/config/carTema.php?carpag=controleAr');

                //Autorizado a editar ou superusuário - se bloquear só o campo, o datepicker continua tentando carregar
                if(parseInt(document.getElementById("guardaInsArCond").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){
                    document.getElementById("selectAno").disabled = false;
                    document.getElementById("botinserir").disabled = false;
                    document.getElementById("botimpr").disabled = false;
                    document.getElementById("editNomeEmpr").disabled = false;
                    document.getElementById("valorvisita").disabled = false;
                    document.getElementById("botSalvarEditEmpr").disabled = false;

                    $('#datavisins').datepicker({ uiLibrary: 'bootstrap3', locale: 'pt-br', format: 'dd/mm/yyyy' });
                    $('#dataAcionam').datetimepicker({ footer: true, modal: true , uiLibrary: 'bootstrap3', locale: 'pt-br', format: 'dd/mm/yyyy HH:MM'});
                    $('#dataAtendim').datetimepicker({ footer: true, modal: true , uiLibrary: 'bootstrap3', locale: 'pt-br', format: 'dd/mm/yyyy HH:MM'});
                    $('#dataConclus').datepicker({ uiLibrary: 'bootstrap3', locale: 'pt-br', format: 'dd/mm/yyyy' });                
                    $("#datavisins").mask("99/99/9999");
                    carregaEmpresas();
                }

                //Fiscal
                if(parseInt(document.getElementById("guardaFiscArCond").value) === 1){
                    document.getElementById("selectAno").disabled = false;
                    document.getElementById("botimpr").disabled = false;
                }

            });

            function carregaEmpresas(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/controleAr/salvaControle3.php?acao=buscarelempresas", true);
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
                                $("#empresa").html(options);
                                $("#empresaCorret").html(options);
                                $("#empresalocal").html(options);
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function insAparelho(){
                document.getElementById("guardaid").value = 0;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/controleAr/salvaControle3.php?acao=buscanumero", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("apar").innerHTML = Resp.apar;
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("subtitulomodal").innerHTML = "Inserindo novo aparelho";
                                    document.getElementById("etiqmes").innerHTML = "";
                                    document.getElementById("relacmodalControle").style.display = "block";
                                    document.getElementById("localap").focus();
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaModal(){
                if(document.getElementById("mudou").value != "0"){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/controleAr/salvaControle3.php?acao=salvadados&codigo="+document.getElementById("guardaid").value
                        +"&localap="+encodeURIComponent(document.getElementById("localap").value)
                        +"&empresa="+document.getElementById("empresa").value
                        , true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.")
                                    }else{
                                        document.getElementById("relacmodalControle").style.display = "none";
                                        $("#faixacentral").load("modulos/controleAr/relAr3.php?acao=todos&ano="+document.getElementById("selectAno").value);
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("relacmodalControle").style.display = "none";
                }
            }

            function buscaData(Cod, InsEdit){ // Cod é o id de visitas_ar2 - O guardaid fica com o id de controle_ar3 pq vem do click na linha DataTable
                if(parseInt(document.getElementById("guardaInsArCond").value) === 0 && parseInt(document.getElementById("UsuAdm").value) < 7){
                    document.getElementById("empresaCorret").disabled = true;
                    document.getElementById("datavisins").disabled = true;
                    document.getElementById("nometecins").disabled = true;
                    document.getElementById("nomeTecnicoEmpresa").disabled = true;
                    document.getElementById("dataAcionam").disabled = true;
                    document.getElementById("dataAtendim").disabled = true;
                    document.getElementById("dataConclus").disabled = true;
                    document.getElementById("nomecontactado").disabled = true;
                    document.getElementById("defeito").disabled = true;
                    document.getElementById("nomeAcompanhante").disabled = true;
                    document.getElementById("nomeAcompPrevent").disabled = true;
                    document.getElementById("diagnostico").disabled = true;
                    document.getElementById("svcRealizado").disabled = true;
                    document.getElementById("dataConclus").disabled = true;
                    document.getElementById("nometecins").disabled = true;
                    document.getElementById("guardaManut").disabled = true;
                    document.getElementById("apagaregistro").disabled = true;
                    document.getElementById("botSalvaDataCorret").disabled = true;
                    document.getElementById("salvaDataPrev").disabled = true;
                }
                document.getElementById("guardaInsEdit").value = InsEdit;
                document.getElementById("apagaregistro").style.visibility = "visible";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/controleAr/salvaControle3.php?acao=buscadata&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("guardaid").value = Resp.cod;// id de visitas_ar2 para editar
                                    document.getElementById("aparins").innerHTML = Resp.apar;
                                    document.getElementById("localapins").innerHTML = Resp.local;
                                    document.getElementById("datavisins").value = Resp.data;
                                    document.getElementById("empresaCorret").value = Resp.empresa;
                                    document.getElementById("nometecins").value = Resp.nome;
                                    document.getElementById("nomeTecnicoEmpresa").value = Resp.nome;
                                    document.getElementById("dataAcionam").value = Resp.acionam;
                                    document.getElementById("nomecontactado").value = Resp.nomecontactado;
                                    document.getElementById("defeito").value = Resp.defeito;
                                    document.getElementById("dataAtendim").value = Resp.atendim;
                                    document.getElementById("nomeAcompanhante").value = Resp.acompanh;
                                    document.getElementById("nomeAcompPrevent").value = Resp.acompanh;
                                    document.getElementById("diagnostico").value = Resp.diagtec;
                                    document.getElementById("svcRealizado").value = Resp.svcrealizado;
                                    document.getElementById("dataConclus").value = Resp.dataConclus;
                                    document.getElementById("nometecins").value = Resp.nome;
                                    document.getElementById("guardaManut").value = Resp.tipomanut;
                                    if(parseInt(Resp.tipomanut) === 1){ // preventiva
                                        document.getElementById("manutins1").checked = true;
                                        document.getElementById("relacmodalInsCorret").style.display = "none";
                                        document.getElementById("relacmodalInsPrevent").style.display = "block";
                                        document.getElementById("subtitulomodalins").innerHTML = "Manutenção Preventiva";
                                        let element = document.getElementById('subtitulomodalins');
                                        element.classList.remove("destacaRed");
                                        element.classList.add('destacaPreto');
                                    }
                                    if(parseInt(Resp.tipomanut) === 2){ // corretiva
                                        document.getElementById("manutins2").checked = true;                                       
                                        document.getElementById("relacmodalInsCorret").style.display = "block";
                                        document.getElementById("relacmodalInsPrevent").style.display = "none";
                                        document.getElementById("subtitulomodalins").innerHTML = "Manutenção Corretiva";
                                        let element = document.getElementById('subtitulomodalins');
                                        element.classList.remove("destacaPreto");
                                        element.classList.add('destacaRed');
                                    }
                                    //Não mudar de tipo de manutenção depois de inserido
                                    document.getElementById("itensmanut").style.visibility = "hidden";
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("relacmodalIns").style.display = "block";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            //Vem de relAr3.php - insere visita preventiva ou corretiva
            function insereData(Cod, InsEdit){ // Cod é o id de controle_ar3 - busca dados do aparelho
                if(parseInt(document.getElementById("guardaInsArCond").value) === 0){
                    $.confirm({
                        title: 'Informação!',
                        content: 'Credenciais para acompanhar e fiscalizar.',
                        autoClose: 'OK|5000',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                document.getElementById("guardaManut").value = 1;
                document.getElementById("subtitulomodalins").innerHTML = "";
                document.getElementById("itensmanut").style.visibility = "visible";
                document.getElementById("apagaregistro").style.visibility = "hidden";
                document.getElementById("guardaid").value = Cod;
                document.getElementById("guardaInsEdit").value = InsEdit;
                document.getElementById("relacmodalInsPrevent").style.display = "block"; // abre manut preventiva
                document.getElementById("relacmodalInsCorret").style.display = "none";
                document.getElementById("empresaCorret").value = "0";
                document.getElementById("dataAcionam").value = document.getElementById("guardaData").value;
                document.getElementById("datavisins").value = document.getElementById("guardaHoje").value;
                document.getElementById("dataAtendim").value = "";
                document.getElementById("dataConclus").value = "";        
                document.getElementById("nomecontactado").value = "";
                document.getElementById("defeito").value = "";
                document.getElementById("nomeAcompanhante").value = "";
                document.getElementById("nomeAcompPrevent").value = "";
                document.getElementById("diagnostico").value = "";
                document.getElementById("svcRealizado").value = "";
                document.getElementById("nomeTecnicoEmpresa").value = "";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/controleAr/salvaControle3.php?acao=buscadados&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("aparins").innerHTML = Resp.apar;
                                    document.getElementById("localapins").innerHTML = Resp.local;
                                    document.getElementById("localapins").disabled = true;
                                    document.getElementById("manutins1").checked = true;
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("relacmodalIns").style.display = "block";
                                    document.getElementById("subtitulomodal").innerHTML = "Inserindo visita técnica";
                                    if(document.getElementById("datavisins").value == ""){
                                        document.getElementById("datavisins").focus();
                                    }else{
                                        document.getElementById("nometecins").focus();
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

            function salvaDataInsPrevent(){
                if(document.getElementById("mudou").value != "0"){
                    if(!validaData(document.getElementById("datavisins").value)){
                        $.confirm({
                            title: 'Informação!',
                            content: 'Confira a data.',
                            draggable: true,
                            buttons: {
                                OK: function(){}
                            }
                        });
                        return false;
                        document.getElementById("datavisins").focus();
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/controleAr/salvaControle3.php?acao=salvadatainsprevent&codigo="+document.getElementById("guardaid").value
                        +"&datavis="+encodeURIComponent(document.getElementById("datavisins").value)
                        +"&nometec="+encodeURIComponent(document.getElementById("nometecins").value)
                        +"&insedit="+document.getElementById("guardaInsEdit").value
                        +"&tipomanut="+document.getElementById("guardaManut").value
                        +"&acompPrevent="+encodeURIComponent(document.getElementById("nomeAcompPrevent").value)
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
                                        $("#faixacentral").load("modulos/controleAr/relAr3.php?acao=todos&ano="+document.getElementById("selectAno").value);
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

            function salvaDataInsCorret(){ // OK
                if(document.getElementById("mudou").value != "0"){
                    if(document.getElementById("empresaCorret").value == "0"){
                        $.confirm({
                            title: 'Informação!',
                            content: 'Selecione a empresa contratada.',
                            draggable: true,
                            buttons: {
                                OK: function(){}
                            }
                        });
                        return false;
                        document.getElementById("empresaCorret").focus();
                    }
                    if(document.getElementById("dataAcionam").value == ""){
                        $.confirm({
                            title: 'Informação!',
                            content: 'Confira a data do acionamento.',
                            draggable: true,
                            buttons: {
                                OK: function(){}
                            }
                        });
                        return false;
                        document.getElementById("dataAcionam").focus();
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/controleAr/salvaControle3.php?acao=salvamanutcorret&codigo="+document.getElementById("guardaid").value
                        +"&empresa="+encodeURIComponent(document.getElementById("empresaCorret").value)
                        +"&dataAcionam="+encodeURIComponent(document.getElementById("dataAcionam").value)
                        +"&dataAtendim="+encodeURIComponent(document.getElementById("dataAtendim").value)
                        +"&dataConclus="+encodeURIComponent(document.getElementById("dataConclus").value)
                        +"&nomecontactado="+encodeURIComponent(document.getElementById("nomecontactado").value)
                        +"&defeito="+encodeURIComponent(document.getElementById("defeito").value)
                        +"&nomeAcompanhante="+encodeURIComponent(document.getElementById("nomeAcompanhante").value)
                        +"&diagnostico="+encodeURIComponent(document.getElementById("diagnostico").value)
                        +"&svcRealizado="+encodeURIComponent(document.getElementById("svcRealizado").value)
                        +"&nomeTecnicoEmpresa="+encodeURIComponent(document.getElementById("nomeTecnicoEmpresa").value)
                        +"&tipomanut="+document.getElementById("guardaManut").value
                        +"&insedit="+document.getElementById("guardaInsEdit").value
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
                                        $("#faixacentral").load("modulos/controleAr/relAr3.php?acao=todos&ano="+document.getElementById("selectAno").value);
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

            function editaLocal(Cod){
                if(parseInt(document.getElementById("guardaInsArCond").value) === 0){
                    $.confirm({
                        title: 'Informação!',
                        content: 'Credenciais para acompanhar e fiscalizar.',
                        autoClose: 'OK|5000',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                document.getElementById("guardaid").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/controleAr/salvaControle3.php?acao=buscalocal&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("aparlocal").innerHTML = Resp.apar;
                                    document.getElementById("localaplocal").value = Resp.local;
                                    document.getElementById("empresalocal").value = Resp.empresa;
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("relacmodalLocal").style.display = "block";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }   
            }

            function salvaLocal(){
                if(document.getElementById("mudou").value != "0"){
                        ajaxIni();
                        if(ajax){
                            ajax.open("POST", "modulos/controleAr/salvaControle3.php?acao=salvalocal&codigo="+document.getElementById("guardaid").value
                            +"&local="+encodeURIComponent(document.getElementById("localaplocal").value)
                            +"&empresa="+encodeURIComponent(document.getElementById("empresalocal").value)
                            , true);
                            ajax.onreadystatechange = function(){
                                if(ajax.readyState === 4 ){
                                    if(ajax.responseText){
//alert(ajax.responseText);
                                        Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                        if(parseInt(Resp.coderro) === 1){
                                            alert("Houve um erro no servidor.")
                                        }else{
                                            document.getElementById("relacmodalLocal").style.display = "none";
                                            $("#faixacentral").load("modulos/controleAr/relAr3.php?acao=todos&ano="+document.getElementById("selectAno").value);
                                        }
                                    }
                                }
                            };
                            ajax.send(null);
                        }
                }else{
                    document.getElementById("relacmodalLocal").style.display = "none";
                }
            }
            function apagaData(){
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Não haverá possibilidade de recuperação. <br>Confirma apagar?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/controleAr/salvaControle3.php?acao=apagadata&codigo="+document.getElementById("guardaid").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                            if(parseInt(Resp.coderro) === 0){
                                                document.getElementById("relacmodalIns").style.display = "none";
                                                $("#faixacentral").load("modulos/controleAr/relAr3.php?acao=todos&ano="+document.getElementById("selectAno").value);
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

            function carregaConfig(){
                if(parseInt(document.getElementById("guardaInsArCond").value) === 1 || parseInt(document.getElementById("guardaFiscArCond").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){
                    $("#configAr").load("modulos/controleAr/relEmpr.php");
                    document.getElementById("relacmodalConfig").style.display = "block";
                }else{
                    $.confirm({
                        title: 'Informação!',
                        content: 'Credenciais para acompanhar e fiscalizar.',
                        autoClose: 'OK|5000',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
            }

            function editaEmpresa(Cod){
                document.getElementById("guardaCodEmpr").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/controleAr/salvaControle3.php?acao=buscaempresa&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editNomeEmpr").value = Resp.nome;
                                    document.getElementById("valorvisita").value = Resp.valor;
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

            function insEmpresa(){
                if(parseInt(document.getElementById("guardaInsArCond").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){
                    document.getElementById("guardaCodEmpr").value = "0";
                    document.getElementById("editNomeEmpr").value = "";
                    document.getElementById("relacEditEmpresa").style.display = "block";
                }
            }

            function salvaEditEmpr(){
                if(document.getElementById("mudou").value != "0"){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/controleAr/salvaControle3.php?acao=salvanomeempresa&codigo="+document.getElementById("guardaCodEmpr").value 
                        +"&nomeempresa="+encodeURIComponent(document.getElementById("editNomeEmpr").value)
                        +"&valorvisita="+encodeURIComponent(document.getElementById("valorvisita").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("relacEditEmpresa").style.display = "none";
                                        $("#configAr").load("modulos/controleAr/relEmpr.php");

                                        carregaEmpresas();

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

            function fechaModal(){
                document.getElementById("guardaid").value = 0;
                document.getElementById("guardaCodVis").value = 0;
                document.getElementById("relacmodalControle").style.display = "none";
                document.getElementById("relacmodalIns").style.display = "none";
                document.getElementById("relacmodalLocal").style.display = "none";
                document.getElementById("relacmodalInsCorret").style.display = "none";
                document.getElementById("relacmodalConfig").style.display = "none";
                document.getElementById("relacEditEmpresa").style.display = "none";
            }
            function fechaEditEmpr(){
                document.getElementById("relacEditEmpresa").style.display = "none";
            }

            function salvaManut(Valor){
                document.getElementById("guardaManut").value = Valor;
                document.getElementById("mudou").value = "1";
                if(parseInt(Valor) === 2){
                    document.getElementById("relacmodalInsPrevent").style.display = "none";
                    document.getElementById("relacmodalInsCorret").style.display = "block";
                }else{
                    document.getElementById("relacmodalInsCorret").style.display = "none";
                    document.getElementById("relacmodalInsPrevent").style.display = "block";
                }
            }
            function modif(){ // assinala se houve qualquer modificação nos campos do modal durante a edição para evitar salvar desnecessariamente
                document.getElementById("mudou").value = "1";
            }
            function modifAno(){
                $("#faixacentral").load("modulos/controleAr/relAr3.php?acao=todos&ano="+document.getElementById("selectAno").value);
            }
            function imprAr(){
                if(parseInt(document.getElementById("selectAno").value) != ""){
                    window.open("modulos/controleAr/imprListaAr3.php?acao=listamesManut&colec=3&ano="+document.getElementById("selectAno").value, "colec3");
                }
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
            function foco(id){
                document.getElementById(id).focus();
            }

        </script>
    </head>
    <body class="corClara" onbeforeunload="return mudaTema(0)"> <!-- ao sair retorna os background claros -->
        <?php
        $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'poslog'");
        $rowSis = pg_num_rows($rsSis);
        if($rowSis == 0){
            echo "Sem contato com os arquivos do sistema. Informe à ATI.";
            return false;
        }
        date_default_timezone_set('America/Sao_Paulo');
        $rsEmpr = pg_query($Conec, "SELECT id, empresa FROM ".$xProj.".empresas_ar WHERE ativo = 1");
        $rsEmprLocal = pg_query($Conec, "SELECT id, empresa FROM ".$xProj.".empresas_ar WHERE ativo = 1");
        $rsEmprCorret = pg_query($Conec, "SELECT id, empresa FROM ".$xProj.".empresas_ar WHERE ativo = 1");

        $rsAno = pg_query($Conec, "SELECT DISTINCT to_char(datavis, 'YYYY') FROM ".$xProj.".visitas_ar2 WHERE ativo = 1");
        $AnoIni = date("Y");
        $Hoje = date("d/m/Y");
        $Data = date("d/m/Y H:i");

        $InsArCond = parEsc("arcond", $Conec, $xProj, $_SESSION["usuarioID"]); // procura marca arcond em poslog
        $FiscArCond = parEsc("arfisc", $Conec, $xProj, $_SESSION["usuarioID"]); // procura marca arfisc em poslog
        $Tema = parEsc("tema", $Conec, $xProj, $_SESSION["usuarioID"]); // Claro(0) Escuro(1)
        $Menu6 = escMenu($Conec, $xProj, 6);

        ?>
        <div id="tricoluna0" style="margin: 20px; border: 2px solid #330066; border-radius: 15px; padding: 20px; min-height: 200px;">
            <div id="tricoluna1" class="box" style="position: relative; float: left; width: 33%;">
                <img src="imagens/settings.png" height="20px;" style="cursor: pointer; padding-right: 30px;" onclick="carregaConfig();" title="Configurar empresas de manutenção">
                <input type="button" id="botinserir" class="resetbot" style="background-color: #F8F4E1; font-size: 80%;" value="Inserir Novo Aparelho" onclick="insAparelho();">
            </div>
            <div id="tricoluna2" class="box" style="position: relative; float: left; width: 33%; text-align: center; border: 2px solid #C0C0C0; border-radius: 10px;">
                <h6>Controle da Manutenção dos Condicionadores de Ar</h6>
                <div style="text-align: center;"><?php echo $Menu6; ?></div>
            </div>
            <div id="tricoluna3" class="box" style="position: relative; float: left; width: 33%; text-align: left;">
                <div id="selectTema" style="position: relative; float: left; padding-left: 8px;">
                    <label id="etiqcorFundo" class="etiq" style="color: #6C7AB3; font-size: 80%;">Tema: </label>
                    <input type="radio" name="corFundo" id="corFundo0" value="0" <?php if($Tema == 0){echo 'CHECKED';}; ?> title="Tema claro" onclick="mudaTema(0);" style="cursor: pointer;"><label for="corFundo0" class="etiq" style="cursor: pointer;">&nbsp;Claro</label>
                    <input type="radio" name="corFundo" id="corFundo1" value="1" <?php if($Tema == 1){echo 'CHECKED';}; ?> title="Tema escuro" onclick="mudaTema(1);" style="cursor: pointer;"><label for="corFundo1" class="etiq" style="cursor: pointer;">&nbsp;Escuro</label>
                    <label style="padding-right: 5px;"></label>
                </div>
                <label style="font-size: 80%;">Selecione o Ano: </label>
                <select id="selectAno" onchange="modifAno();" style="font-size: 80%; width: 70px;" title="Selecione o ano de trabalho.">
                    <option value="<?php echo $AnoIni; ?>"><?php echo $AnoIni; ?></option>
                        <?php 
                            if($rsAno){
                                while ($Opcoes = pg_fetch_row($rsAno)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                                <?php 
                                }
                            }
                        ?>
                </select>
                <label style="padding-left: 10px;"></label>
                <button id="botimpr" class="botpadrred" style="padding-left: 5px; padding-right: 5px; font-size: 80%;" onclick="imprAr();">PDF</button>
            </div>

            <div id="faixacentral"></div>
            <div id="faixaMensagem" style="display: none; position: relative; margin: 120px; padding: 20px; text-align: center; border: 1px solid; border-radius: 15px;">
                Usuário não cadastrado. <br>O acesso é proporcionado pela ATI.
            </div>
        </div>
        <input type="hidden" id="guardaid" value="0" />
        <input type="hidden" id="guardaInsEdit" value="0" />
        <input type="hidden" id="guardaCodVis" value="0" />
        <input type="hidden" id="guardaManut" value="1" />
        <input type="hidden" id="mudou" value="0" /> <!-- valor 1 quando houver mudança em qualquer campo do modal -->
        <input type="hidden" id="guardaData" value="<?php echo $Data; ?>" />
        <input type="hidden" id="guardaHoje" value="<?php echo $Hoje; ?>" />
        <input type="hidden" id="guardaInsArCond" value="<?php echo $InsArCond; ?>" />
        <input type="hidden" id="guardaFiscArCond" value="<?php echo $FiscArCond; ?>" />
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="guardaCodEmpr" value="0" />

        <!-- div para inserção novo aparelho  -->
        <div id="relacmodalControle" class="relacmodal">
            <div class="modal-content-Controle corPreta">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h5 id="titulomodal" style="text-align: center; color: #666;">Controle de Manutenção</h5>
                <div id="subtitulomodal" style="text-align: center; color: red;"></div>
                <table style="margin: 0 auto; width: 90%">
                    <tr>
                        <td class="etiq aDir">Aparelho: </td>
                        <td><label class="aCentro" style="padding-left: 5px; font-weight: bold;" id="apar"></label><label id="etiqmes" class="etiq" style="padding-left: 50px; font-size: 90%; font-weight: bold;"></label></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiq aDir">Local de instalação: </td>
                        <td><input type="text" id="localap" valor="" onchange="modif();" style="width: 50%;"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiq aDir">Empresa: </td>
                        <td>
                            <select id="empresa" onchange="modif();" style="font-size: 1rem; width: 100%;" title="Selecione uma empresa.">
                                <option value=""></option>
                                <?php 
                                    $OpEmpr1 = pg_query($Conec, "SELECT id, empresa FROM ".$xProj.".empresas_ar WHERE ativo = 1 ORDER BY empresa");
                                    if($OpEmpr1){
                                        while ($Opcoes = pg_fetch_row($OpEmpr1)){ ?>
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
                        <td colspan="4" style="padding-bottom: 10px;"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center;"><button class="resetbot" style="font-size: .9rem;" onclick="salvaModal();">Salvar</button></td>
                    </tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div para inserção de nova data de visita preventiva ou corretiva -->
        <div id="relacmodalIns" class="relacmodal">
            <div class="modal-content-Controle corPreta">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h5 id="titulomodal" style="text-align: center; color: #666;">Controle de Manutenção</h5>
                <div id="subtitulomodalins" style="text-align: center;"></div>

                <div id="itensmanut" style="text-align: center;">
                    <label style="font-size: 90%; padding-left: 5px;">Tipo de manutenção: </label>
                    <input type="radio" name="manutins" id="manutins1" value="1" title="Manutenção preventiva" onclick="salvaManut(value);"><label for="manutins1" style="font-size: 90%; padding-left: 3px;"> Preventiva</label>
                    <input type="radio" name="manutins" id="manutins2" value="2" title="Manutenção corretiva" onclick="salvaManut(value);"><label for="manutins2" style="font-size: 90%; padding-left: 3px;"> Corretiva</label>
                </div>

                <table style="margin: 0 auto; width: 90%;">
                    <tr>
                        <td class="etiq aDir">Aparelho: </td>
                        <td><label class="aCentro" style="padding-left: 5px; font-weight: bold;" id="aparins"></label><label id="etiqmes" class="etiq" style="padding-left: 50px; font-size: 90%; font-weight: bold;"></label></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiq aDir">Local de instalação: </td>
                        <td style="min-width: 400px;"><label id="localapins" style="width: 90%; font-weight: bold;"></label></td>
                        <td></td>
                        <td></td>
                    </tr>
                </table>

                <!-- div o acionamento de manut preventiva -->
                <div id="relacmodalInsPrevent" style="display: none;">
                    <table style="margin: 0 auto; width: 90%;">
                        <tr>
                            <td class="etiq aDir">Data da Visita: </td>
                            <td style="text-align: left;"><input type="text" id="datavisins" valor="" width="150" style="text-align: center; border: 1px solid; border-radius: 5px;" onchange="modif();"></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="etiq aDir">Nome do Técnico: </td>
                            <td><input type="text" id="nometecins" style="width: 100%; min-width: 400px;" valor="" onchange="modif();"></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="etiq aDir" title="Nome do funcionário que acompanhou a manutenção">Acompanhante: </td>
                            <td><input type="text" id="nomeAcompPrevent" style="width: 100%;" valor="" onchange="modif();" title="Nome do funcionário que acompanhou a manutenção"></td>
                            <td colspan="2" style="padding-bottom: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center;"><button id="salvaDataPrev" class="resetbot" style="font-size: .9rem;" onclick="salvaDataInsPrevent();">Salvar</button></td>
                        </tr>
                    </table>
                </div>

                <!-- div o acionamento de manut corretiva -->
                <div id="relacmodalInsCorret" style="display: none;">
                    <table style="margin: 0 auto; width: 90%;">
                        <tr>
                            <td class="etiq aDir" title="Nome da empresa contratada">Empresa: </td>
                            <td colspan="3">
                                <select id="empresaCorret" onchange="modif();" style="font-size: 1rem; width: 100%;" title="Selecione uma empresa.">
                                    <option value=""></option>
                                    <?php 
                                        $OpEmpr2 = pg_query($Conec, "SELECT id, empresa FROM ".$xProj.".empresas_ar WHERE ativo = 1 ORDER BY empresa");
                                        if($OpEmpr2){
                                            while ($Opcoes = pg_fetch_row($OpEmpr2)){ ?>
                                                <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                            <?php 
                                            }
                                        }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiq aDir" title="Data e hora do acionamento">Acionamento: </td>
                            <td><input type="text" id="dataAcionam" width="200" style="text-align: center; border: 1px solid; border-radius: 5px;" valor="" onchange="modif();" title="Data do acionamento"></td>
                            <td class="etiq aDir" title="Nome do responsável contactado da empresa contratada">Nome do Atendente: </td>
                            <td><input type="text" id="nomecontactado" style="width: 100%;" valor="" onchange="modif();" title="Nome do responsável contactado da empresa contratada"></td>
                        </tr>
                        <tr>
                            <td class="etiq aDir" title="Defeito observado no aparelho">Defeito observado: </td>
                            <td colspan="3" style="padding-bottom: 10px;">
                                <textarea id="defeito" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px; padding: 2px;" rows="4" cols="60" title="Defeito observado no aparelho" onchange="modif();"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiq aDir" title="Data e hora do atendimento">Atendimento: </td>
                            <td><input type="text" id="dataAtendim" width="200" style="text-align: center; border: 1px solid; border-radius: 5px;" valor="" onchange="modif();" title="Data e hora do atendimento"></td>
                            <td class="etiq aDir" title="Nome do funcionário que acompanhou a manutenção">Acompanhante: </td>
                            <td><input type="text" id="nomeAcompanhante" style="width: 100%;" valor="" onchange="modif();" title="Nome do funcionário que acompanhou a manutenção"></td>
                        </tr>

                        <td class="etiq aDir" title="Diagnóstico do técnico da empresa contratada">Diagnóstico do técnico: </td>
                            <td colspan="3" style="padding-bottom: 10px; width: 100%;">
                                <textarea id="diagnostico" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px; padding: 2px;" rows="4" cols="60" title="Diagnóstico do técnico da empresa contratada" onchange="modif();"></textarea>
                            </td>
                        </tr>
                        <td class="etiq aDir" title="Descrição do serviço realizado">Serviço realizado: </td>
                            <td colspan="3" style="padding-bottom: 10px; width: 100%;">
                                <textarea id="svcRealizado" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px; padding: 2px;" rows="4" cols="60" title="Descrição do serviço realizado" onchange="modif();"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiq aDir" title="Data da conclusão do serviço">Data conclusão: </td>
                            <td><input type="text" id="dataConclus" width="160" style="text-align: center; border: 1px solid; border-radius: 5px;" valor="" onchange="modif();" title="Data e hora da conclusão do reparo"></td>
                            <td class="etiq aDir" title="Nome do técnico da empresa contratada que efetuou o reparo">Técnico da Empresa: </td>
                            <td><input type="text" id="nomeTecnicoEmpresa" style="width: 100%;" valor="" onchange="modif();" title="Nome do técnico da empresa contratada que efetuou o reparo"></td>
                        </tr>
                        <tr>
                        <tr>
                            <td colspan="4" style="padding-bottom: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center;"><button id="botSalvaDataCorret" class="resetbot" style="font-size: .9rem;" onclick="salvaDataInsCorret();">Salvar</button></td>
                        </tr>
                    </table>
                </div>
                <div style="padding-bottom: 10px;"><button id="apagaregistro" class="resetbot" style="font-size: .7rem;" onclick="apagaData();">Apagar</button></div>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div para o nome do Local do aparelho  -->
        <div id="relacmodalLocal" class="relacmodal">
            <div class="modal-content-Controle corPreta">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h5 id="titulomodal" style="text-align: center; color: #666;">Controle de Manutenção</h5>
                <div id="subtitulomodal" style="text-align: center; color: red;"></div>
                <table style="margin: 0 auto; width: 90%">
                    <tr>
                        <td class="etiq aDir">Aparelho: </td>
                        <td><label class="aCentro" style="padding-left: 5px; font-weight: bold;" id="aparlocal"></label><label id="etiqmes" class="etiq" style="padding-left: 50px; font-size: 90%; font-weight: bold;"></label></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiq aDir">Local de instalação: </td>
                        <td><input type="text" id="localaplocal" valor="" onchange="modif();" style="width: 90%;"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiq aDir">Empresa: </td>
                        <td>
                            <select id="empresalocal" onchange="modif();" style="font-size: 1rem; width: 100%;" title="Selecione uma empresa.">
                                <option value=""></option>
                                <?php 
                                    $OpEmpr3 = pg_query($Conec, "SELECT id, empresa FROM ".$xProj.".empresas_ar WHERE ativo = 1 ORDER BY empresa");
                                    if($OpEmpr3){
                                        while ($Opcoes = pg_fetch_row($OpEmpr3)){ ?>
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
                        <td colspan="4" style="padding-bottom: 10px;"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center;"><button class="resetbot" style="font-size: .9rem;" onclick="salvaLocal();">Salvar</button></td>
                    </tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div para editar nome das empresas  -->
        <div id="relacmodalConfig" class="relacmodal">
            <div class="modal-content-Controle corPreta">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h5 id="titulomodal" style="text-align: center; color: #666;">Empresas de Manutenção de Ar Condicionado</h5>
                <div class='divbot corFundo' onclick='insEmpresa()' title="Adicionar nova empresa de manutenção"> Inserir </div>

                <div id="configAr" style="text-align: center;"></div>

            </div>
        </div> <!-- Fim Modal-->
        
        <div id="relacEditEmpresa" class="relacmodal">
            <div class="modal-content-InsControle">
                <span class="close" onclick="fechaEditEmpr();">&times;</span>
                <h5 id="titulomodal" style="text-align: center; color: #666;">Nome da Empresa de Ar Condicionado</h5>
                <div id="subtitulomodal" style="text-align: center; color: red;"></div>
                    <table style="margin: 0 auto; width: 90%">
                        <tr>
                            <td class="etiq aDir">Empresa: </td>
                            <td><input type="text" id="editNomeEmpr" valor="" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%;"></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="etiq aDir">Valor da visita: </td>
                            <td><input type="text" id="valorvisita" valor="" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 120px; text-align: right;"></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </table>
                    <br>
                    <div style="text-align: center;">
                        <button id="botSalvarEditEmpr" class="resetbot" style="font-size: .9rem;" onclick="salvaEditEmpr();">Salvar</button>
                    </div>
                </div>
            </div>
        </div> <!-- Fim Modal-->

        <div id="carregaTema"></div> <!-- carrega a pág modulos/config/carTema.php - onde está a função mudaTema() -->

    </body>
</html>