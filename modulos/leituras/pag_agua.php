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
        <title>Leituras</title>
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

        <style type="text/css">
            .quadro{
                position: relative; float: left; margin: 5px; width: 95%; border: 1px solid; border-radius: 10px; padding: 2px; padding-top: 5px;
            }
            .etiq{
                text-align: center; font-size: 70%; font-weight: bold; padding-right: 1px; padding-bottom: 1px;
            }
            .titRelat{
                /*  padding-top, padding-right, padding-bottom, padding-left */
                margin: 5px; padding: 3px 15px 3px 15px; background-color: #87CEFA; border: 1px solid; border-radius:10px;
            }
            .modal-content-AguaControle{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 70%;
            }
            .modal-content-grafico{
                background: linear-gradient(180deg, white, #FFF8DC);
                margin: 12% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 70%;
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
                var nHora = new Date(); 
                var hora = nHora.getHours();
                var Cumpr = "Bom Dia!";
                if(hora >= 12){
                    Cumpr = "Boa Tarde!";
                }
                if(hora >= 18){
                    Cumpr = "Boa Noite!";
                }
                if(parseInt(document.getElementById("guardaerro").value) === 0){
                    document.getElementById("botInserir").style.visibility = "hidden"; 
                    document.getElementById("botImprimir").style.visibility = "hidden"; 
                    document.getElementById("imgAguaConfig").style.visibility = "hidden"; 
                    document.getElementById("etiqselecVisuMesAnoAgua").style.visibility = "hidden"; 
                    document.getElementById("selecVisuMesAnoAgua").style.visibility = "hidden"; 
                    document.getElementById("etiqselecVisuAnoAgua").style.visibility = "hidden"; 
                    document.getElementById("selecVisuAnoAgua").style.visibility = "hidden"; 
                    document.getElementById("botgrafico").style.visibility = "hidden"; 

                    $('#carregaTema').load('modulos/config/carTema.php?carpag=pag_agua');

                    if(parseInt(document.getElementById("InsLeitura").value) === 1 || parseInt(document.getElementById("FiscAgua").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){ // se estiver marcado em cadusu para fazer a leitura
                        if(parseInt(document.getElementById("InsLeitura").value) === 1 || parseInt(document.getElementById("UsuAdm").value) >= 6){
                            document.getElementById("botInserir").style.visibility = "visible"; 
                            document.getElementById("etiqselecVisuMesAnoAgua").style.visibility = "visible"; 
                            document.getElementById("selecVisuMesAnoAgua").style.visibility = "visible";
                            document.getElementById("etiqselecVisuAnoAgua").style.visibility = "visible"; 
                            document.getElementById("selecVisuAnoAgua").style.visibility = "visible"; 
                            document.getElementById("botgrafico").style.visibility = "visible"; 

                            $("#container5").load("modulos/leituras/carAgua.php");
                            $("#container6").load("modulos/leituras/carEstatAgua.php");
                        }
                    }else{
                        $("#container5").load("modulos/leituras/carMsg.php?msgtipo=1&cumpr="+encodeURIComponent(Cumpr));
                        $("#container6").load("modulos/leituras/carMsg.php?msgtipo=1&cumpr="+encodeURIComponent(Cumpr));
                        document.getElementById("botgrafico").style.visibility = "hidden"; 
                        document.getElementById("botImprimir").disabled = true;
                        document.getElementById("imgAguaConfig").style.visibility = "hidden"; 
                    }
                    if(parseInt(document.getElementById("InsLeitura").value) === 0){
                        document.getElementById("botInserir").style.visibility = "hidden"; 
                    }

                    //para editar obedece ao nivel administrativo
                    if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admEdit").value)){
                        document.getElementById("botImprimir").style.visibility = "visible"; 
                    }else{
                        document.getElementById("botImprimir").style.visibility = "hidden"; 
                    }
                    if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                        document.getElementById("botInserir").style.visibility = "visible"; 
                        document.getElementById("botImprimir").style.visibility = "visible"; 
                        document.getElementById("imgAguaConfig").style.visibility = "visible"; 
                        document.getElementById("imgAguaConfig").style.visibility = "visible"; 
                    }
                };

                $("#selecMesAno").change(function(){
                    document.getElementById("selecAno").value = "";
                    if(document.getElementById("selecMesAno").value != ""){
                        window.open("modulos/leituras/imprLista.php?acao=listamesAgua&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), document.getElementById("selecMesAno").value);
                        document.getElementById("selecMesAno").value = "";
                        document.getElementById("relacimprLeitura").style.display = "none";
                    }
                });
                $("#selecAno").change(function(){
                    document.getElementById("selecMesAno").value = "";
                    if(document.getElementById("selecAno").value != ""){
                        window.open("modulos/leituras/imprLista.php?acao=listaanoAgua&ano="+encodeURIComponent(document.getElementById("selecAno").value), document.getElementById("selecAno").value);
                        document.getElementById("selecAno").value = "";
                        document.getElementById("relacimprLeitura").style.display = "none";
                    }
                });

                $("#selecVisuMesAnoAgua").change(function(){
                    document.getElementById("selecVisuAnoAgua").value = "";
                    $("#container5").load("modulos/leituras/carAgua.php?mesano="+encodeURIComponent(document.getElementById("selecVisuMesAnoAgua").value));
                    $("#container6").load("modulos/leituras/carEstatAgua.php?mesano="+encodeURIComponent(document.getElementById("selecVisuMesAnoAgua").value)+"&corTema="+document.getElementById("guardaCor").value);
                });

                $("#selecVisuAnoAgua").change(function(){
                    document.getElementById("selecVisuMesAnoAgua").value = "";
                    $("#container5").load("modulos/leituras/carAgua.php?ano="+encodeURIComponent(document.getElementById("selecVisuAnoAgua").value));
                    $("#container6").load("modulos/leituras/carEstatAgua.php?ano="+encodeURIComponent(document.getElementById("selecVisuAnoAgua").value)+"&corTema="+document.getElementById("guardaCor").value);
                });

                $("#configSelecAgua").change(function(){
                    if(document.getElementById("configSelecAgua").value == ""){
                        document.getElementById("configCpfAgua").value = "";
                        document.getElementById("leituraAgua").checked = false;
                        document.getElementById("leituraFiscAgua").checked = false;
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/leituras/salvaLeitura.php?acao=buscausuarioAgua&codigo="+document.getElementById("configSelecAgua").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configCpfAgua").value = format_CnpjCpf(Resp.cpf);
                                        if(parseInt(Resp.agua) === 1){
                                            document.getElementById("leituraAgua").checked = true;
                                        }else{
                                            document.getElementById("leituraAgua").checked = false;
                                        }
                                        if(parseInt(Resp.fiscagua) === 1){
                                            document.getElementById("leituraFiscAgua").checked = true;
                                        }else{
                                            document.getElementById("leituraFiscAgua").checked = false;
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

                $("#configCpfAgua").click(function(){
                    document.getElementById("configSelecAgua").value = "";
                    document.getElementById("configCpfAgua").value = "";
                    document.getElementById("leituraAgua").checked = false;
                    document.getElementById("leituraFiscAgua").checked = false;
                });
                $("#configCpfAgua").change(function(){
                    document.getElementById("configSelecAgua").value = "";
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/leituras/salvaLeitura.php?acao=buscacpfAgua&cpf="+encodeURIComponent(document.getElementById("configCpfAgua").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configSelecAgua").value = Resp.PosCod;
                                        if(parseInt(Resp.agua) === 1){
                                            document.getElementById("leituraAgua").checked = true;
                                        }else{
                                            document.getElementById("leituraAgua").checked = false;
                                        }
                                        if(parseInt(Resp.fiscagua) === 1){
                                            document.getElementById("leituraFiscAgua").checked = true;
                                        }else{
                                            document.getElementById("leituraFiscAgua").checked = false;
                                        }
                                    }
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.");
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("leituraAgua").checked = false;
                                        document.getElementById("leituraFiscAgua").checked = false;
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

            function carregaModal(Cod){
                if(parseInt(document.getElementById("UsuAdm").value) < 7){
                    if(parseInt(document.getElementById("InsLeitura").value) === 0){
                        return false;
                    }
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/leituras/salvaLeitura.php?acao=buscaData&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("insdata").value = Resp.data;
                                    document.getElementById("insdiasemana").innerHTML = Resp.sem;
                                    document.getElementById("insleitura1").value = Resp.leitura1;
                                    document.getElementById("insleitura2").value = Resp.leitura2;
                                    document.getElementById("insleitura3").value = Resp.leitura3;
                                    document.getElementById("guardacod").value = Cod;
                                    document.getElementById("relacmodalLeitura").style.display = "block";
                                    if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                                        document.getElementById("apagaRegistro").style.visibility = "visible";
                                    }
                                    document.getElementById("guardacod").value = Cod;
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function insereModal(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/leituras/salvaLeitura.php?acao=ultData", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("insdata").value = Resp.data;
                                    document.getElementById("insdiasemana").innerHTML = Resp.sem;
                                    document.getElementById("guardacod").value = 0;
                                    document.getElementById("insdata").disabled = false;
                                    document.getElementById("insdata").value = Resp.proximo;  // document.getElementById("guardahoje").value;
                                    document.getElementById("insleitura1").value = "";
                                    document.getElementById("insleitura2").value = "";
                                    document.getElementById("insleitura3").value = "";
                                    document.getElementById("relacmodalLeitura").style.display = "block";
                                    document.getElementById("apagaRegistro").style.visibility = "hidden";
                                    $('#mensagemLeitura').fadeIn("slow");
                                    document.getElementById("mensagemLeitura").innerHTML = "Próxima data para lançamento.";
                                    $('#mensagemLeitura').fadeOut(2000);
                                    document.getElementById("insleitura1").focus();
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function checaData(){
                document.getElementById("mudou").value = "1";
                Tam = document.getElementById("insdata").value;
                if(Tam.length < 10){
                    document.getElementById("insdata").value = "";
                    document.getElementById("insdata").focus();
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/leituras/salvaLeitura.php?acao=checaData&data="+encodeURIComponent(document.getElementById("insdata").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    if(parseInt(Resp.jatem) === 1){
                                        document.getElementById("insdata").value = Resp.data;
                                        document.getElementById("insdiasemana").innerHTML = Resp.sem;
                                        document.getElementById("insleitura1").value = Resp.leitura1;
                                        document.getElementById("insleitura2").value = Resp.leitura2;
                                        document.getElementById("insleitura3").value = Resp.leitura3;
                                        document.getElementById("guardacod").value = Resp.id;
                                        $('#mensagemLeitura').fadeIn("slow");
                                        document.getElementById("mensagemLeitura").innerHTML = "Essa data já foi lançada.";
                                        $('#mensagemLeitura').fadeOut(3000);
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaModal(){
                if(parseInt(document.getElementById("mudou").value) === 0){
                    document.getElementById("relacmodalLeitura").style.display = "none";
                    return false;
                }
                Tam = document.getElementById("insdata").value;
                if(Tam.length < 10){
                    document.getElementById("insdata").value = "";
                    document.getElementById("insdata").focus();
                    return false;
                }
                if(document.getElementById("insleitura1").value == "" && document.getElementById("insleitura2").value == "" && document.getElementById("insleitura3").value == ""){
                    $('#mensagemLeitura').fadeIn("slow");
                    document.getElementById("mensagemLeitura").innerHTML = "Nenhuma leitura anotada";
                    $('#mensagemLeitura').fadeOut(3000);
                    return false;
                }
                if(parseFloat(document.getElementById("insleitura1").value) >= parseFloat(document.getElementById("insleitura2").value) ){
                    $.confirm({
                        title: 'Ação Suspensa!',
                        content: 'Erro nas leituras',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                if(parseFloat(document.getElementById("insleitura2").value) >= parseFloat(document.getElementById("insleitura3").value) ){
                    $.confirm({
                        title: 'Ação Suspensa!',
                        content: 'Erro nas leituras2',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/leituras/salvaLeitura.php?acao=salvaData&insdata="+encodeURIComponent(document.getElementById("insdata").value)
                    +"&leitura1="+document.getElementById("insleitura1").value
                    +"&leitura2="+document.getElementById("insleitura2").value
                    +"&leitura3="+document.getElementById("insleitura3").value
                    +"&codigo="+document.getElementById("guardacod").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    $('#mensagemLeitura').fadeIn("slow");
                                    document.getElementById("mensagemLeitura").innerHTML = "Lançamento salvo.";
                                    $('#mensagemLeitura').fadeOut(1000);
                                    $("#container5").load("modulos/leituras/carAgua.php");
//                                    $("#container6").load("modulos/leituras/carEstatAgua.php");
                                    $("#container6").load("modulos/leituras/carEstatAgua.php?corTema="+document.getElementById("guardaCor").value);
                                    document.getElementById("relacmodalLeitura").style.display = "none";

                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function apagaModalAgua(){
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma apagar o lançamento?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/leituras/salvaLeitura.php?acao=apagaData&codigo="+document.getElementById("guardacod").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                            if(parseInt(Resp.coderro) === 0){
                                                document.getElementById("relacmodalLeitura").style.display = "none";
                                                $("#container5").load("modulos/leituras/carAgua.php");
//                                                $("#container6").load("modulos/leituras/carEstatAgua.php");
                                                $("#container6").load("modulos/leituras/carEstatAgua.php?corTema="+document.getElementById("guardaCor").value);
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

            function marcaAgua(obj, Campo){
                if(obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                if(document.getElementById("configSelecAgua").value == ""){
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
                    ajax.open("POST", "modulos/leituras/salvaLeitura.php?acao=configMarcaAgua&codigo="+document.getElementById("configSelecAgua").value
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
                                            content: 'Não restaria outro marcado para gerenciar as permissões.',
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

            function abreAguaConfig(){
                document.getElementById("leituraAgua").checked = false;
                document.getElementById("leituraFiscAgua").checked = false;
                document.getElementById("configCpfAgua").value = "";
                document.getElementById("configSelecAgua").value = "";
                document.getElementById("modalAguaConfig").style.display = "block";
            }
            function fechaAguaConfig(){
                document.getElementById("modalAguaConfig").style.display = "none";
            }
            function resumoUsuAgua(){
                window.open("modulos/leituras/imprUsuAgua.php?acao=listaUsuarios", "AguaUsu");
            }
            function imprMesLeitura(){
                if(document.getElementById("selecMesAno").value == ""){
                    return false;
                }
                window.open("modulos/leituras/imprLista.php?acao=listames&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                document.getElementById("relacimprLeitura").style.display = "none";
            }
            function imprAnoLeitura(){
                if(document.getElementById("selecAno").value == ""){
                    return false;
                }
                window.open("modulos/leituras/imprLista.php?acao=listaano&ano="+encodeURIComponent(document.getElementById("selecAno").value));
                document.getElementById("relacimprLeitura").style.display = "none";
            }

            function abreImprLeitura(){
                document.getElementById("relacimprLeitura").style.display = "block";
            }

            function abreGrafico(Mes, Ano){
//                $("#divgrafico").load("modulos/leituras/grafAguaJs.php?mes="+Mes+"&ano="+Ano);
                //Se estiver em branco, verificar valor exagerado nos lançamentos (falta de decimais)  
                $("#divgrafico").load("modulos/leituras/grafAguaJs5A.php"); // gráfico para os últimsos 5 anos
                document.getElementById("relacgrafico").style.display = "block";
            }
            function fechaModalGrafico(){
                document.getElementById("relacgrafico").style.display = "none";
            }
            function fechaModal(){
                document.getElementById("relacmodalLeitura").style.display = "none";
            }
            function fechaModalImpr(){
                document.getElementById("relacimprLeitura").style.display = "none";
            }
            function modifIns(Campo){ // assinala se houve qualquer modificação nos campos do modal
                document.getElementById("mudou").value = "1";

                paragraph1 = document.getElementById(Campo).value;
                document.getElementById(Campo).value = paragraph1.replace(",", '.');

//                if((parseFloat(document.getElementById(Campo).value) % 1) === 0){
                if((document.getElementById(Campo).value % 1) === 0){
                    $.confirm({
                        title: 'Por favor',
                        content: 'Verifique as casas decimais.',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
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
            modalImpr = document.getElementById('relacimprLeitura'); //span[0]
                spanImpr = document.getElementsByClassName("close")[0];
                window.onclick = function(event){
                    if(event.target === modalImpr){
                        modalImpr.style.display = "none";
                    }
                };
//var versaoJquery = $.fn.jquery; 
//alert(versaoJquery);
        </script>
    </head>
    <body class="corClara" onbeforeunload="return mudaTema(0)"> <!-- ao sair retorna os background claros -->
        <?php
            $Hoje = date('d/m/Y');
            $Erro = 0;
            $rs = pg_query($Conec, "SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE table_schema = 'cesb' And table_name = 'leitura_agua'");
            $row = pg_num_rows($rs);
            if($row == 0){
                $Erro = 1;
                echo "Faltam tabelas. Informe à ATI.";
                return false;
            }
            $admIns = parAdm("insleituraagua", $Conec, $xProj);   // nível para inserir 
            $admEdit = parAdm("editleituraagua", $Conec, $xProj); // nível para editar

            $InsAgua = parEsc("agua", $Conec, $xProj, $_SESSION["usuarioID"]); // procura agua em poslog 
            $FiscAgua = parEsc("fisc_agua", $Conec, $xProj, $_SESSION["usuarioID"]); // procura fisc_agua em poslog 
            $Tema = parEsc("tema", $Conec, $xProj, $_SESSION["usuarioID"]); // Claro(0) Escuro(1)

            // Preenche caixa de escolha mes/ano para impressão
            $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataleitura, 'MM'), '/', TO_CHAR(dataleitura, 'YYYY')) 
            FROM ".$xProj.".leitura_agua GROUP BY TO_CHAR(dataleitura, 'MM'), TO_CHAR(dataleitura, 'YYYY') ORDER BY TO_CHAR(dataleitura, 'YYYY') DESC, TO_CHAR(dataleitura, 'MM') DESC ");
            $OpcoesEscAno = pg_query($Conec, "SELECT EXTRACT(YEAR FROM ".$xProj.".leitura_agua.dataleitura)::text 
            FROM ".$xProj.".leitura_agua GROUP BY 1 ORDER BY 1 DESC ");

            $OpcoesVisuMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataleitura, 'MM'), '/', TO_CHAR(dataleitura, 'YYYY')) 
            FROM ".$xProj.".leitura_agua GROUP BY TO_CHAR(dataleitura, 'MM'), TO_CHAR(dataleitura, 'YYYY') ORDER BY TO_CHAR(dataleitura, 'YYYY') DESC, TO_CHAR(dataleitura, 'MM') DESC ");
            $OpcoesVisuAno = pg_query($Conec, "SELECT EXTRACT(YEAR FROM ".$xProj.".leitura_agua.dataleitura)::text 
            FROM ".$xProj.".leitura_agua GROUP BY 1 ORDER BY 1 DESC ");
            
            $OpConfig = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");

        ?>
        <input type="hidden" id="guardahoje" value="<?php echo $Hoje; ?>" />
        <input type="hidden" id="guardaerro" value="<?php echo $Erro; ?>" />
        <input type="hidden" id="guardaUsuId" value="<?php echo $_SESSION["usuarioID"]; ?>" />
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="admIns" value="<?php echo $admIns; ?>" /> <!-- nível mínimo para inserir  -->
        <input type="hidden" id="admEdit" value="<?php echo $admEdit; ?>" />
        <input type="hidden" id="InsLeitura" value="<?php echo $InsAgua; ?>" /> <!-- marca em cadusu para inserir as leituras -->
        <input type="hidden" id="FiscAgua" value="<?php echo $FiscAgua; ?>" />

        <div style="margin: 5px; border: 2px solid green; border-radius: 15px; padding: 3px;">
            <div id="tricoluna0" class="row" style="margin-left: 5px; margin-right: 5px;"> <!-- botões Inserir e Imprimir-->
                <div id="tricoluna1" class="corCinza" style="width: 38%; margin: 0 auto; text-align: left;">
                    <img src="imagens/settings.png" height="20px;" id="imgAguaConfig" style="cursor: pointer; padding-left: 30px;" onclick="abreAguaConfig();" title="Acesso às configuraçõe">
                    <label style="padding-left: 40px;"></label>
                    <button id="botInserir" class="botpadrblue" onclick="insereModal();" title="Inserir leitura do hidrômetro">Inserir</button>
                    <label id="etiqselecVisuMesAnoAgua" style="padding-left: 20px; font-size: .8rem;">Visualisar Mês: </label>
                    <select id="selecVisuMesAnoAgua" style="font-size: .8rem; width: 90px;" title="Selecione o mês/ano a visualisar.">
                        <option value=""></option>
                        <?php 
                        if($OpcoesVisuMes){
                            while ($Opcoes = pg_fetch_row($OpcoesVisuMes)){ ?>
                                <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                            <?php 
                            }
                        }
                        ?>
                    </select>
                </div> <!-- quadro -->
                <div id="tricoluna2" class="corCinza" style="width: 20%; text-align: center;">Controle do Consumo de Água</div> <!-- espaçamento entre colunas  -->
                <div id="tricoluna3" class="corCinza" style="width: 40%; margin: 0 auto; text-align: right; padding-right: 10px;">
                    <div id="selectTema" style="position: relative; float: left;">
                        <label id="etiqcorFundo" class="etiq" style="color: #6C7AB3; font-size: 80%; padding-left: 5px;">Tema: </label>
                        <input type="radio" name="corFundo" id="corFundo0" value="0" <?php if($Tema == 0){echo 'CHECKED';}; ?> title="Tema claro" onclick="mudaTema(0);" style="cursor: pointer;"><label for="corFundo0" class="etiq" style="cursor: pointer;">&nbsp;Claro</label>
                        <input type="radio" name="corFundo" id="corFundo1" value="1" <?php if($Tema == 1){echo 'CHECKED';}; ?> title="Tema escuro" onclick="mudaTema(1);" style="cursor: pointer;"><label for="corFundo1" class="etiq" style="cursor: pointer;">&nbsp;Escuro</label>
                        <label style="padding-right: 5px;"></label>
                    </div>
                    <label id="etiqselecVisuAnoAgua" style="font-size: .8rem;">Visualisar Ano: </label>
                    <select id="selecVisuAnoAgua" style="font-size: .8rem; width: 70px;" title="Selecione o ano a visualisar.">
                        <option value=""></option>
                        <?php 
                        if($OpcoesVisuAno){
                            while ($Opcoes = pg_fetch_row($OpcoesVisuAno)){ ?>
                                <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[0]; ?></option>
                            <?php 
                            }
                        }
                        ?>
                    </select>
                    <label style="padding-right: 15px;"></label>
                    <img src="imagens/iconGraf.png" height="36px;" id="botgrafico" style="cursor: pointer;" onclick="abreGrafico();" title="Gráfico de consumo anual">
                    <label style="padding-right: 15px;"></label>
                    <button id="botImprimir" class="botpadrred" onclick="abreImprLeitura();">PDF</button>
                </div> <!-- quadro -->
            </div>

            <div style="padding: 5px; display: flex; align-items: center; justify-content: center;"> 
                <div class="row" style="width: 97%;">
                    <div id="container5" class="col quadro" style="margin: 0 auto; width: 100%;"></div> <!-- quadro -->

                    <div class="col-1" style="width: 1%;"></div> <!-- espaçamento entre colunas  -->

                    <div id="container6" class="col quadro" style="margin: 0 auto; width: 100%;"></div> <!-- quadro -->
                </div> <!-- row  -->
            </div> <!-- container  -->
        </div>

        <!-- div modal para imprimir em pdf  -->
        <div id="relacimprLeitura" class="relacmodal">
            <div class="modal-content-imprLeitura corPreta">
                <span class="close" onclick="fechaModalImpr();">&times;</span>
                <h5 id="titulomodal" style="text-align: center;color: #666;">Controle do Consumo de Água</h5>
                <h6 id="titulomodal" style="text-align: center; padding-bottom: 18px; color: #666;">Impressão PDF</h6>
                <div style="border: 2px solid #C6E2FF; border-radius: 10px;">
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
           <br><br>
        </div> <!-- Fim Modal-->

         <!-- Modal configuração-->
         <div id="modalAguaConfig" class="relacmodal">
            <div class="modal-content-AguaControle corPreta">
                <span class="close" onclick="fechaAguaConfig();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto; border: 0px;"></div>
                        <div class="col quadro"><h5 style="text-align: center; color: #666; border: 0px;">Configuração Leitura Hidrômetro</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center; border: 0px;">
                            <button class="botpadrred" style="font-size: 70%;" onclick="resumoUsuAgua();">Resumo em PDF</button>
                        </div> 
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
                            <select id="configSelecAgua" style="max-width: 230px;" onchange="modif();" title="Selecione um usuário.">
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
                            <input type="text" id="configCpfAgua" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('configSelecAgua');return false;}" title="Procura por CPF. Digite o CPF."/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                    </tr>
                </table>

                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td class="etiq80" title="Pode registrar as leituras diárias do consumo de água">Consumo Água:</td>
                        <td colspan="4">
                            <input type="checkbox" id="leituraAgua" title="Pode registrar as leituras diárias do consumo água" onchange="marcaAgua(this, 'agua');" >
                            <label for="leituraAgua" title="Pode registrar as leituras diárias do consumo de água">registrar leitura diária do Hidrômetro</label>
                        </td>
                    </tr>

                    <tr>
                        <td class="etiq80" title="Pode registrar as leituras diárias do consumo de água">Consumo Água:</td>
                        <td colspan="4">
                            <input type="checkbox" id="leituraFiscAgua" title="Pode registrar as leituras diárias do consumo de água" onchange="marcaAgua(this, 'fisc_agua');" >
                            <label for="leituraFiscAgua" title="Pode registrar as leituras diárias do consumo de água">acompanhar e fiscalizar as leituras do hidrômetro</label>
                        </td>
                    </tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para mostrar gráfico anual -->
        <div id="relacgrafico" class="relacmodal">
            <div class="modal-content-grafico">
                <span class="close" onclick="fechaModalGrafico();">&times;</span>
                <h5 id="titulomodal" style="text-align: center;color: #666;">Controle do Consumo de Água</h5>
                <div id="divgrafico" style="border: 2px solid #C6E2FF; border-radius: 15px;"></div>
                <div style="padding-bottom: 20px;"></div>
            </div>
            <br><br>
        </div> <!-- Fim Modal-->

        <div id="carregaTema"></div> <!-- carrega a pág modulos/config/carTema.php - onde está a função mudaTema() -->

    </body>
</html>