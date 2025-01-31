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
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-ui.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="comp/js/jquery-ui.min.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script> <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="comp/js/jquery.mask.js"></script>
        <link rel="stylesheet" type="text/css" media="screen" href="class/gijgo/css/gijgo.css" />
        <script src="class/gijgo/js/gijgo.js"></script>
        <script src="class/gijgo/js/messages/messages.pt-br.js"></script>
        <style>
            .modal-content-relacContr{
/*                background: linear-gradient(180deg, white, #86c1eb); */
                background: transparent;
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%; /* acertar de acordo com a tela */
                max-width: 900px;
            }
            .modal-content-ContratosControle{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%; /* acertar de acordo com a tela */
                max-width: 900px;
            }
            .modal-content-Empresa{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 55%;
                max-width: 900px;
            }
            .quadrinho {
                font-size: 90%;
                border: 1px solid #C0C0C0;
                border-radius: 3px;
                padding-left: 2px;
                padding-right: 2px;
            }
            .quadrinhoClick {
                font-size: 90%;
                border: 1px solid;
                border-radius: 3px;
                padding-left: 4px;
                padding-right: 4px;
                cursor: pointer;
            }
            .modal-content-editEmpresas{
                background: linear-gradient(180deg, white, #0099FF);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 55%; /* acertar de acordo com a tela */
                max-width: 900px;
            }
            .divbot{
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
            function format_CnpjCpf(value){
                //https://gist.github.com/davidalves1/3c98ef866bad4aba3987e7671e404c1e
                const CPF_LENGTH = 11;
                const cnpjCpf = value.replace(/\D/g, '');
                if (cnpjCpf.length === CPF_LENGTH) {
                    return cnpjCpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/g, "\$1.\$2.\$3-\$4");
                } 
                  return cnpjCpf.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/g, "\$1.\$2.\$3/\$4-\$5");
            }
            
            $(document).ready(function(){
                if(parseInt(document.getElementById("guardaContr").value) === 1 || parseInt(document.getElementById("guardaFiscContr").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){
                    $("#faixacontatante").load("modulos/contratos/jContratA.php");
                }else{
                    document.getElementById("faixaMensagem").style.display = "block";
                }
                $("#dataAssinat").mask("99/99/9999");
                $('#dataAssinat').datepicker({ uiLibrary: 'bootstrap4', locale: 'pt-br', format: 'dd/mm/yyyy' });
                $("#dataVencim").mask("99/99/9999");
                $("#configCpfUsuario").mask("999.999.999-99");

                carregaEmpresas();

                $("#configselecUsuario").change(function(){
                    if(document.getElementById("configselecUsuario").value == ""){
                        document.getElementById("configCpfUsuario").value = "";
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/contratos/salvaContrato.php?acao=buscausuario&codigo="+document.getElementById("configselecUsuario").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configCpfUsuario").value = format_CnpjCpf(Resp.cpf);
                                        if(parseInt(Resp.registro) === 1){
                                            document.getElementById("registroContratos").checked = true;
                                        }else{
                                            document.getElementById("registroContratos").checked = false;
                                        }
                                        if(parseInt(Resp.fisccontratos) === 1){
                                            document.getElementById("fiscalContratos").checked = true;
                                        }else{
                                            document.getElementById("fiscalContratos").checked = false;
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

                $("#configCpfUsuario").click(function(){
                    document.getElementById("configselecUsuario").value = "";
                });
                $("#configCpfUsuario").change(function(){
                    document.getElementById("configselecUsuario").value = "";
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/contratos/salvaContrato.php?acao=buscacpfusuario&cpf="+encodeURIComponent(document.getElementById("configCpfUsuario").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configselecUsuario").value = Resp.PosCod;
                                        if(parseInt(Resp.registro) === 1){
                                            document.getElementById("registroContratos").checked = true;
                                        }else{
                                            document.getElementById("registroContratos").checked = false;
                                        }
                                        if(parseInt(Resp.fisccontratos) === 1){
                                            document.getElementById("fiscalContratos").checked = true;
                                        }else{
                                            document.getElementById("fiscalContratos").checked = false;
                                        }
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("registroContratos").checked = false;
                                        document.getElementById("fiscalContratos").checked = false;
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

                
            }); // fim do ready

            function insContrato(Tipo){
                document.getElementById("botApagaBem").style.visibility = "hidden"; 
                if(parseInt(Tipo) === 1){
                    document.getElementById("titmodaledit").innerHTML = "Empresas Contratadas";
                }
                if(parseInt(Tipo) === 2){
                    document.getElementById("titmodaledit").innerHTML = "Contratantes";
                }
                document.getElementById("guardaCod").value = 0;
                document.getElementById("guardaTipo").value = Tipo;
                document.getElementById("guardaPrazo").value = "";
                document.getElementById("diasAnteced").disabled = true;
                document.getElementById("notifica2").checked = true;
                document.getElementById("diasAnteced").value = "";
                document.getElementById("dataAviso").disabled = true;
                document.getElementById("numcontrato").value = "";
                document.getElementById("selecSetor").value = "";
                document.getElementById("selecEmpresa").value = "";
                document.getElementById("objetocontrato").value = "";
                document.getElementById("obscontrato").value = "";
                document.getElementById("dataAssinat").value = "";
                document.getElementById("dataVencim").value = "";
                document.getElementById("dataAviso").value = "";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/contratos/salvaContrato.php?acao=buscaNumero&tipo="+Tipo, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("numsequencia").innerHTML = Resp.contratoNum;
                                    botApagaBem

                                    document.getElementById("editaModalContratos").style.display = "block";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function editContrato(Tipo, Cod){
                document.getElementById("botApagaBem").style.visibility = "visible"; 
                document.getElementById("guardaTipo").value = Tipo;
                document.getElementById("guardaCod").value = Cod;
                document.getElementById("guardaPrazo").value = "";
                if(parseInt(Tipo) === 1){
                    document.getElementById("titmodaledit").innerHTML = "Empresas Contratadas";
                }
                if(parseInt(Tipo) === 2){
                    document.getElementById("titmodaledit").innerHTML = "Contratantes";
                }
                if(ajax){
                    ajax.open("POST", "modulos/contratos/salvaContrato.php?acao=buscaContrato&codigo="+Cod+"&tipo="+Tipo, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("numcontrato").value = Resp.numcontrato;
                                    document.getElementById("selecSetor").value = Resp.codsetor;
                                    document.getElementById("selecEmpresa").value = Resp.codempresa;
                                    document.getElementById("objetocontrato").value = Resp.objcontrato;
                                    document.getElementById("obscontrato").value = Resp.obs;
                                    document.getElementById("dataAssinat").value = Resp.dataassinat;
                                    document.getElementById("dataVencim").value = Resp.datavencim;
                                    
                                    document.getElementById("dataAviso").value = Resp.dataaviso;
                                    document.getElementById("selecPrazo").value = Resp.vigencia;

                                    document.getElementById("diasAnteced").value = Resp.diasnotific;
                                    if(parseInt(Resp.diasnotific) === 0){
                                        document.getElementById("diasAnteced").disabled = true;
                                        document.getElementById("dataAviso").value = "";
                                        document.getElementById("dataAviso").disabled = true;
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
                                    if(parseInt(Resp.pararaviso) === 1){
                                        document.getElementById("pararaviso").checked = true;
                                    }else{
                                        document.getElementById("pararaviso").checked = false;
                                    }

                                    document.getElementById("editaModalContratos").style.display = "block";
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaEditContrato(){
                //Tipo 1 = Casa é Contratante   2 = Casa é contratada
                if(document.getElementById("numcontrato").value == ""){
                    document.getElementById("numcontrato").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira o número do contrato.";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("selecSetor").value == ""){
                    let element = document.getElementById('selecSetor');
                    element.classList.add('destacaBorda');
                    document.getElementById("selecSetor").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Selecione o setor correspondente.";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("selecEmpresa").value == ""){
                    let element = document.getElementById('selecEmpresa');
                    element.classList.add('destacaBorda');
                    document.getElementById("selecEmpresa").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Selecione o nome da empresa.";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }

                if(document.getElementById("objetocontrato").value == ""){
                    document.getElementById("objetocontrato").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Descreva a finalidade do contrato.";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("dataAssinat").value == ""){
                    document.getElementById("dataAssinat").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira a data da assinatura do contrato.";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(compareDates(document.getElementById("dataAssinat").value, document.getElementById("dataVencim").value) == true){
                    document.getElementById("dataVencim").focus();
                    $.confirm({
                        title: 'Atenção!',
                        content: 'A data do vencimento é de antes da data de assinatura do contrato.',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                if(compareDates(document.getElementById("dataAssinat").value, document.getElementById("dataAviso").value) == true){
                    document.getElementById("dataAviso").focus();
                    $.confirm({
                        title: 'Atenção!',
                        content: 'A data do aviso é de antes da data de assinatura do contrato.',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }                    
                if(document.getElementById("dataVencim").value == ""){
                    document.getElementById("dataVencim").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira a data de vencimento ou selecione o prazo de duração do contrato.";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }

                Notif = 0;
                if(document.getElementById("notifica1").checked === true){
                    Notif = 1;
                }
                ParaAv = 0;
                if(document.getElementById("pararaviso").checked === true){
                    ParaAv = 1;
                }

                if(document.getElementById("notifica1").checked === true && parseInt(document.getElementById("diasAnteced").value) === 0 || document.getElementById("notifica1").checked === true && document.getElementById("diasAnteced").value == ""){
                    document.getElementById("diasAnteced").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Informe os dias de antecedência para a notificação.";
                    $('#mensagem').fadeOut(3000);
                    return false;
                }

                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/contratos/salvaContrato.php?acao=salvaContrato&codigo="+document.getElementById("guardaCod").value
                    +"&tipo="+document.getElementById("guardaTipo").value
                    +"&numcontrato="+encodeURIComponent(document.getElementById("numcontrato").value)
                    +"&setor="+document.getElementById("selecSetor").value
                    +"&empresanum="+document.getElementById("selecEmpresa").value
                    +"&objeto="+encodeURIComponent(document.getElementById("objetocontrato").value)
                    +"&observ="+encodeURIComponent(document.getElementById("obscontrato").value)
                    +"&vencim="+encodeURIComponent(document.getElementById("dataVencim").value)
                    +"&assinat="+encodeURIComponent(document.getElementById("dataAssinat").value)
                    +"&notific="+Notif
                    +"&pararaviso="+ParaAv
                    +"&anteced="+document.getElementById("diasAnteced").value
                    +"&prazo="+document.getElementById("selecPrazo").value
                    +"&guardaPrazo="+document.getElementById("guardaPrazo").value
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editaModalContratos").style.display = "none";
                                    $("#faixacontatante").load("modulos/contratos/jContratA.php");
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function abreNotific(Valor){
                if(parseInt(Valor) == 0){ 
                    document.getElementById("diasAnteced").disabled = true;
                    document.getElementById("dataAviso").disabled = true;
                    document.getElementById("pararaviso").style.visibility = "hidden";
                    document.getElementById("etiqpararaviso").style.visibility = "hidden";
                }else{
                    document.getElementById("diasAnteced").disabled = false;
                    document.getElementById("dataAviso").disabled = false;
                    document.getElementById("pararaviso").style.visibility = "visible";
                    document.getElementById("etiqpararaviso").style.visibility = "visible";
                }
            }

            function calcAviso(){
                if(document.getElementById("dataVencim").value == ""){
                    return false;
                }
                document.getElementById("guardaPrazo").value = "";
                if(compareDates(document.getElementById("dataAssinat").value, document.getElementById("dataVencim").value) == true){
                    document.getElementById("dataVencim").focus();
                    $.confirm({
                        title: 'Atenção!',
                        content: 'A data do vencimento é de antes da data de assinatura do contrato.',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/contratos/salvaContrato.php?acao=calcaviso&vencim="+encodeURIComponent(document.getElementById("dataVencim").value)
                    +"&diasanteced="+document.getElementById("diasAnteced").value
                    +"&assinat="+encodeURIComponent(document.getElementById("dataAssinat").value)
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")"); 
                                if(parseInt(Resp.coderro) === 0){
                                    if(document.getElementById("notifica1").checked === true){
                                        document.getElementById("dataAviso").value = Resp.dataaviso;
                                        if(compareDates(document.getElementById("dataAssinat").value, Resp.dataaviso) == true){
                                            document.getElementById("dataAviso").focus();
                                            $.confirm({
                                                title: 'Atenção!',
                                                content: 'A data do aviso é de antes da data de assinatura do contrato.',
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

            function calcPrazo(){
                if(document.getElementById("selecPrazo").value == ""){
                    return false;
                }
                if(document.getElementById("dataAssinat").value == ""){
                    return false;
                }
                document.getElementById("guardaPrazo").value = "";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/contratos/salvaContrato.php?acao=calcprazo&assinat="+encodeURIComponent(document.getElementById("dataAssinat").value)
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

            function abreContratosConfig(){
                document.getElementById("registroContratos").checked = false;
                document.getElementById("fiscalContratos").checked = false;
                document.getElementById("configCpfUsuario").value = "";
                document.getElementById("configselecUsuario").value = "";
                document.getElementById("modalContratosConfig").style.display = "block";
            }

            function marcaContrato(obj, Campo){
                if(obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                if(document.getElementById("configselecUsuario").value == ""){
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
                    ajax.open("POST", "modulos/contratos/salvaContrato.php?acao=configMarcaContrato&codigo="+document.getElementById("configselecUsuario").value
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
                                            content: 'Não restaria outro marcado para gerenciar os contratos.',
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

            function apagarContrato(){
                $.confirm({
                    title: 'Confirmação',
                    content: 'Confirma apagar este lançamento?<br>Não haverá possibilidade de recuperação.<br>Continua?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/contratos/salvaContrato.php?acao=apagaContrato&codigo="+document.getElementById("guardaCod").value
                                +"&tipo="+document.getElementById("guardaTipo").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                document.getElementById("editaModalContratos").style.display = "none";
                                                $("#faixacontatante").load("modulos/contratos/jContratA.php");
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
            function Empresas(){
                document.getElementById("relacmodalEmpresas").style.display = "block";
                $("#configEmpr").load("modulos/contratos/jEmpr.php");
            }

            function editaEmpresa(Cod){
                document.getElementById("guardaCodEmpr").value = Cod;
                document.getElementById("titulomodalEdit").innerHTML = "Editar Nome da Empresa";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/contratos/salvaContrato.php?acao=buscaempresa&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("editNomeEmpr").value = Resp.nome;
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
                if(document.getElementById("mudou").value != "0"){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/contratos/salvaContrato.php?acao=salvanomeempresa&codigo="+document.getElementById("guardaCodEmpr").value 
                        +"&nomeempresa="+encodeURIComponent(document.getElementById("editNomeEmpr").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("relacEditEmpresa").style.display = "none";
                                        $("#configEmpr").load("modulos/contratos/jEmpr.php");

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

            function carregaEmpresas(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/contratos/salvaContrato.php?acao=buscarelempresas", true);
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
                                $("#selecEmpresa").html(options);
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function insEmpresa(){
                document.getElementById("guardaCodEmpr").value = "0";
                document.getElementById("editNomeEmpr").value = "";
                document.getElementById("relacEditEmpresa").style.display = "block";
                document.getElementById("titulomodalEdit").innerHTML = "Adicionar Empresa";
            }

            function resumoUsuContratos(){
                window.open("modulos/contratos/imprUsuContr1.php?acao=listaUsuarios", "ContrUsu");
            }
            function imprListaContratos(){
                window.open("modulos/contratos/imprContr1.php?acao=listaContratos", "ListaContratos");
            }
            function imprContratadas(){
                window.open("modulos/contratos/imprContr1.php?acao=listaContratadas", "listaContratadas");
            }
            function imprContratantes(){
                window.open("modulos/contratos/imprContr1.php?acao=listaContratantes", "listaContratantes");
            }

            function fechaContratosConfig(){
                document.getElementById("modalContratosConfig").style.display = "none";
            }
            function fechaInsContrato(){
                document.getElementById("editaModalContratos").style.display = "none";
            }
            function fechaEmpresas(){
                document.getElementById("relacmodalEmpresas").style.display = "none";
            }
            function fechaEditEmpr(){
                document.getElementById("relacEditEmpresa").style.display = "none";
            }
            function modif(){
                document.getElementById("mudou").value = "1";
            }
            function foco(id){
                document.getElementById(id).focus();
            }
            function tiraBorda(id){
                let element = document.getElementById(id);
                element.classList.remove('destacaBorda');
            }

            function compareDates (date1, date2) {
                let parts1 = date1.split('/') // separa a data pelo caracter '/'
                date1 = new Date(parts1[2], parts1[1] - 1, parts1[0]) // formata 'date'

                let parts2 = date2.split('/') // separa a data pelo caracter '/'
                date2 = new Date(parts2[2], parts2[1] - 1, parts2[0]) // formata 'date'
                  // compara se a data informada é maior que a data atual e retorna true ou false
                return date1 > date2 ? true : false
            }
            /* Brazilian initialisation for the jQuery UI date picker plugin. */
            /* Written by Leonildo Costa Silva (leocsilva@gmail.com). */
            jQuery(function($){
                $.datepicker.regional['pt-BR'] = {
                    closeText: 'Fechar',
                    prevText: '< Anterior',
                    nextText: 'Próximo >',
                    currentText: 'Hoje',
                    monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
                    monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
                    dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado'],
                    dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
                    dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
                    weekHeader: 'Sm',
                    dateFormat: 'dd/mm/yy',
                    firstDay: 1,
                    isRTL: false,
                    showMonthAfterYear: false,
                    yearSuffix: ''
                };
                $.datepicker.setDefaults($.datepicker.regional['pt-BR']);
            });

        </script>
    </head>
    <body>
        <?php
        if(!$Conec){
            echo "Sem contato com o PostGresql";
            return false;
        }
        date_default_timezone_set('America/Sao_Paulo'); //Um dia = 86.400 seg
        //Tipo 1 Comunhão é contratante  -  Tipo 2 Comunhão é contratada

//Provisório
//    pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".contratos1");
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".contratos1 (
        id SERIAL PRIMARY KEY, 
        dataassinat date DEFAULT '3000-12-31', 
        datavencim date DEFAULT '3000-12-31', 
        dataaviso date DEFAULT '3000-12-31', 
        numcontrato VARCHAR(100), 
        codsetor smallint NOT NULL DEFAULT 0, 
        codempresa smallint NOT NULL DEFAULT 0, 
        objetocontr text, 
        vigencia VARCHAR(30), 
        notific smallint NOT NULL DEFAULT 0, 
        pararaviso smallint NOT NULL DEFAULT 0, 
        diasnotific smallint NOT NULL DEFAULT 0, 
        observ text, 
        ativo smallint NOT NULL DEFAULT 1, 
        usuins bigint NOT NULL DEFAULT 0, 
        datains timestamp without time zone DEFAULT '3000-12-31', 
        usuedit bigint NOT NULL DEFAULT 0, 
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        )
    ");

//    pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".contratos2");
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".contratos2 (
        id SERIAL PRIMARY KEY, 
        dataassinat date DEFAULT '3000-12-31', 
        datavencim date DEFAULT '3000-12-31', 
        dataaviso date DEFAULT '3000-12-31', 
        numcontrato VARCHAR(100), 
        codsetor smallint NOT NULL DEFAULT 0, 
        codempresa smallint NOT NULL DEFAULT 0, 
        objetocontr text, 
        vigencia VARCHAR(30), 
        notific smallint NOT NULL DEFAULT 0, 
        pararaviso smallint NOT NULL DEFAULT 0, 
        diasnotific smallint NOT NULL DEFAULT 0, 
        observ text, 
        ativo smallint NOT NULL DEFAULT 1, 
        usuins bigint NOT NULL DEFAULT 0, 
        datains timestamp without time zone DEFAULT '3000-12-31', 
        usuedit bigint NOT NULL DEFAULT 0, 
        dataedit timestamp without time zone DEFAULT '3000-12-31' 
        )
    ");

//    pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".contrato_empr");
    pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".contrato_empr (
        id SERIAL PRIMARY KEY, 
        empresa VARCHAR(150),
        tipo smallint NOT NULL DEFAULT 1, 
        ativo smallint DEFAULT 1 NOT NULL,
        obs text 
        ) 
     ");

//------------------------

        $rs = pg_query($Conec, "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'contratos1'");
        $row = pg_num_rows($rs);
        if($row == 0){
            die("<br>Faltam tabelas. Informe à ATI");
            return false;
        }

        $Contr = parEsc("contr", $Conec, $xProj, $_SESSION["usuarioID"]);
        $FiscContr = parEsc("fisc_contr", $Conec, $xProj, $_SESSION["usuarioID"]);

        $OpSetor = pg_query($ConecPes, "SELECT id, sigla FROM ".$xPes.".setor WHERE dt_fim IS NULL ORDER BY sigla");
        $OpDias = pg_query($Conec, "SELECT codesc FROM ".$xProj.".escolhas WHERE codesc <= 120 ORDER BY codesc");
        $OpConfig = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
        ?>
        <input type="hidden" id="UsuAdm" value = "<?php echo $_SESSION["AdmUsu"]; ?>" />
        <input type="hidden" id="guardaContr" value = "<?php echo $Contr; ?>" />
        <input type="hidden" id="guardaFiscContr" value = "<?php echo $FiscContr; ?>" />
        <input type="hidden" id="guardaCod" value = "0" />
        <input type="hidden" id="guardaTipo" value = "0" />
        <input type="hidden" id="guardaPrazo" value = "" />
        <input type="hidden" id="guardaCodEmpr" value="0" />
        <input type="hidden" id="mudou" value="0" />

        <div style="margin: 20px;">
            <div class="box" style="position: relative; float: left; width: 33%;">
                <?php
                if($Contr == 1){
                ?>
                <img src="imagens/settings.png" height="20px;" style="cursor: pointer; padding-left: 30px;" onclick="abreContratosConfig();" title="Configurar o acesso aos contratos">
                <button class="botpadrblue" id="botInsEmpr" onclick="Empresas();" title="Editar/Adicionar empresas">Empresas</button>
                <?php
                }else{
                    echo "&nbsp;";
                }
                ?>
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: center;">
                <h4>Controle de Contratos</h4>
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: center;">
                <label style="padding-left: 20px;"></label>
                <button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="imprListaContratos();" title="Gera um arquivo pdf com as duas relações.">PDF</button>
            </div>

            <br>
            <div id="faixaMensagem" style="display: none; position: relative; margin: 70px; padding: 20px; text-align: center; border: 1px solid; border-radius: 10px;">
                <br>Usuário não cadastrado. <br>O acesso é proporcionado pela ATI.
            </div>
        </div>

        <!-- div três colunas -->
        <div style="margin: 0 auto; justify-content: center; align-items: center;">
            <div style="position: relative; float: left; margin: 5px; text-align: center; width: 98%; border: 1px solid; border-radius: 10px; overflow: auto;"><div id="faixacontatante"></div></div>
<!--            <div style="position: relative; float: left; width: 1%;"></div>
            <div style="position: relative; float: left; margin: 5px; text-align: center; width: 48%; border: 1px solid; border-radius: 10px;"><div id="faixacontratada"></div></div>
 -->
        </div>

        <div id="editaModalContratos" class="relacmodal">
            <div class="modal-content-relacContr">
                <span class="close" style="font-size: 250%; color: black;" onclick="fechaInsContrato();">&times;</span>
                <div style="border: 2px solid blue; border-radius: 10px; margin-top: 10px; background: linear-gradient(180deg, white, #87CEEB)">
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 10px;"><label id="titmodaledit">Contratados e Contratante</label></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td class="etiq" style="padding-bottom: 7px;">Contrato: </td>
                            <td style="padding-bottom: 10px;"><input type="text" id="numcontrato" style="width: 200px; text-align: center; border:1px solid; border-radius: 5px;"/></td>
                            <td class="etiq" style="padding-bottom: 7px;"><label id="numsequencia" style="font-size: 150%; border:1px solid; border-radius: 5px; padding-left: 3px; padding-right: 3px;" title="Mera sugestão para numeração."></label></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="etiq">Setor: </td>
                            <td colspan="3" style="min-width: 150px;">
                                <select id="selecSetor" style="min-width: 50px;" onchange="modif();" onmousedown="tiraBorda(id);" title="Selecione um Setor.">
                                    <option value=""></option>
                                        <?php 
                                        if($OpSetor){
                                            while ($Opcoes = pg_fetch_row($OpSetor)){ ?>
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
                            <td class="etiq">Empresa: </td>
                            <td colspan="3" style="min-width: 200px;">
                                <select id="selecEmpresa" onchange="modif();" style="font-size: 1rem; min-width: 150px;" title="Selecione uma empresa."></select>
                            </td>
                            <td></td>
                        </tr>

                        <tr>
                            <td class="etiq">Objeto: </td>
                            <td colspan="3" style="width: 100px;"><textarea id="objetocontrato" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px; padding: 2px;" rows="2" cols="40" title="Observações" onchange="modif();"></textarea></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="etiq">Observações: </td>
                            <td colspan="3" style="width: 100px;"><textarea id="obscontrato" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px; padding: 2px;" rows="2" cols="40" title="Observações" onchange="modif();"></textarea></td>
                            <td></td>
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
                            <td colspan="2" class="etiq aCentro" style="border-inline: 1px solid; border-top: 1px solid;">Término do Contrato</td>
                        </tr>
                        <tr>
                            <td class="etiq aEsq">Data Assinatura</td>
                            <td class="etiq aCentro">Prazo</td>
                            <td class="etiq aEsq">Data Vencimento</td>
                            <td class="etiq aCentro" style="border-left: 1px solid;">Notificação?</td>
                            <td class="etiq aCentro" style="border-right: 1px solid;">Antecedência</td>
                        </tr>

                        <tr>
                            <!-- on change nas datas com o datepicker trava a máquina -->
                            <td style="text-align: center;"><input type="text" style="text-align: center; border: 1px solid; border-radius: 5px;" id="dataAssinat" width="150" placeholder="Data" onkeypress="if(event.keyCode===13){javascript:foco('dataVencim');return false;}"/></td>
                            <td>
                            <select id="selecPrazo" style="min-width: 50px;" onchange="calcPrazo();" title="Selecione um prazo.">
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
                            <td style="text-align: center;"><input type="text" style="text-align: center; border: 1px solid; border-radius: 5px; width: 100px;" id="dataVencim" placeholder="Data" onkeypress="if(event.keyCode===13){javascript:foco('botsalvadata');return false;}" onchange="calcAviso();" /></td>
                            <td class="aCentro" style="border-left: 1px solid; border-color: #9C9C9C;">
                                <input type="radio" name="notifica" id="notifica1" value="1" title="Há necessidade de notificar o contratado que que o contrato não será prorrogado?" onclick="abreNotific(value);"><label for="notifica1" class="etiqAzul" style="padding-left: 3px;"> Sim</label>
                                <input type="radio" name="notifica" id="notifica2" value="0" CHECKED title="Não há necessidade de notificar o contratado." onclick="abreNotific(value);"><label for="notifica2" class="etiqAzul" style="padding-left: 3px;"> Não</label>
                            </td>
                            <td style="text-align: center; border-right: 1px solid; border-color: #9C9C9C;">
                                <input type="text" style="width: 60px; text-align: center; border: 1px solid; border-radius: 5px;" id="diasAnteced" placeholder="Dias" onchange="calcAviso();" onkeypress="if(event.keyCode===13){javascript:foco('dataAviso');return false;}" title="Aviso emitido na página inicial ao longo desses dias."/>
                                <label class="etiq"> dias</label>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3" id="diascontrato" style="text-align: center;">
                                <input type="checkbox" id="pararaviso" title="Parar a emissão de aviso na página inicial sobre este contrato." onchange="paraAviso(this);" >
                                <label class="etiqAzul" id="etiqpararaviso" for="pararaviso" title="Parar a emissão de aviso na página inicial sobre este contrato.">desativar aviso na página inicial</label>
                            </td>
                            <td colspan="2" style="text-align: center; border-left: 1px solid; border-bottom: 1px solid; border-right: 1px solid; border-color: #9C9C9C;">
                                <label class="etiq">Dia aviso:</label>
                                <input type="text" style="text-align: center; border: 1px solid; border-radius: 5px; width: 110px;" id="dataAviso" placeholder="Data Aviso" onkeypress="if(event.keyCode===13){javascript:foco('dataVencim');return false;}"/>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td><button class="botpadrred" style="font-size: 60%;" id="botApagaBem" onclick="apagarContrato();">Apagar</button></td>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"><button class="botpadrblue" id="botSalvaContrato" onclick="salvaEditContrato();">Salvar</button></td>
                        </tr>
                        <tr>
                            <td colspan="5" style="text-align: center; padding-top: 5px;"><div id="mensagem" style="color: red; font-weight: bold;"></div></td>
                        <tr>
                    </table>
                    <br>
                </div>    
            </div>
        </div> <!-- Fim Modal-->

         <!-- Modal configuração-->
        <div id="modalContratosConfig" class="relacmodal">
            <div class="modal-content-ContratosControle">
                <span class="close" onclick="fechaContratosConfig();">&times;</span>

                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"></div>
                        <div class="col quadro"><h5 id="titulomodal" style="text-align: center; color: #666;">Configuração Contratos</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><button class="botpadrred" style="font-size: 70%;" onclick="resumoUsuContratos();">Resumo em PDF</button></div> 
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
                            <select id="configselecUsuario" style="max-width: 230px;" onchange="modif();" title="Selecione um usuário.">
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
                            <input type="text" id="configCpfUsuario" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('configselecUsuario');return false;}" title="Procura por CPF. Digite o CPF do solicitante."/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                    </tr>
                </table>

                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td class="etiq80" title="Registrar, colecionar e acompanhar os contratos da casa como Contratante ou Contratada.">Contratos: </td>
                        <td colspan="4">
                            <input type="checkbox" id="registroContratos" title="Registrar, colecionar e acompanhar os contratos da casa como Contratante ou Contratada." onchange="marcaContrato(this, 'contr');" >
                            <label for="registroContratos" title="Registrar, colecionar e acompanhar os contratos da casa">registrar, editar e acompanhar os contratos da casa.</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiq80" title="Acompanhar e fiscalizar os contratos da casa como Contratante ou Contratada.">Contratos:</td>
                        <td colspan="4">
                            <input type="checkbox" id="fiscalContratos" title="Acompanhar e fiscalizar os contratos da casa como Contratante ou Contratada." onchange="marcaContrato(this, 'fisc_contr');" >
                            <label for="fiscalContratos" title="Acompanhar e fiscalizar os contratos da casa como Contratante ou Contratada.">acompanhar e fiscalizar os contratos da casa. Não pode editar.</label>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="5" style="text-align: center; padding-top: 5px;"></td>
                    <tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div para editar nome das empresas  -->
        <div id="relacmodalEmpresas" class="relacmodal">
            <div class="modal-content-Empresa">
                <span class="close" onclick="fechaEmpresas();">&times;</span>
                <h5 id="titulomodalEmpr" style="text-align: center; color: #666;">Empresas Contratadas e Contratantes</h5>
                <div class='divbot corFundo' onclick='insEmpresa()' title="Adicionar nova empresa"> Inserir </div>

                <div id="configEmpr" style="text-align: center;"></div>

            </div>
        </div> <!-- Fim Modal-->

        <div id="relacEditEmpresa" class="relacmodal">
            <div class="modal-content-editEmpresas">
                <span class="close" onclick="fechaEditEmpr();">&times;</span>
                <h5 id="titulomodalEdit" style="text-align: center; color: #666;">Editar Nome da Empresa</h5>
                <div id="subtitulomodal" style="text-align: center; color: red;"></div>
                    <table style="margin: 0 auto; width: 90%">
                        <tr>
                            <td class="etiq aDir">Empresa: </td>
                            <td><input type="text" id="editNomeEmpr" valor="" onchange="modif();" style="border: 1px solid; border-radius: 5px; width: 90%;"></td>
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

    </body>
</html>
