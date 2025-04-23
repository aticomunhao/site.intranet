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
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="class/gijgo/css/gijgo.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="comp/js/jquery-confirm.min.js"></script> <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="class/gijgo/js/gijgo.js"></script>
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="comp/js/jquery.mask.js"></script>
        <script src="class/gijgo/js/messages/messages.pt-br.js"></script>
        <style>
           /* Tamanho do checkbox */
            input[type=checkbox]{
                transform: scale(1.2);
            }
            .modal-content-BensControle{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 70%;
            }
            .modal-content-Reivindic{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 75%;
                min-height: 500px;
            }
            .modal-content-EditaReinvindic{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 12% auto;
                padding: 10px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 70%;
            }
            .modal-content-ImprReiv{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 65%;
                max-width: 500px;
            }
            .quadro{
                position: relative; float: left; text-align: center; margin: 5px; width: 95%; padding: 2px; padding-top: 5px;
            }
            .modalMsg-content{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 50%; /* acertar de acordo com a tela */
            }
            .etiqResult{
                position: relative;
                float: left;
                margin: 1px;
                border: 1px solid; border-radius: 5px; padding-left: 3px; padding-right: 3px; font-size: 80%;
            }
            .botpadrinat{
                text-align: center;
                padding: 2px; 
                padding-left: 15px;
                padding-right: 15px; 
                border-radius: 7px;
                font-size: 80%;
                font-weight: bold;
                background-color: #D3D3D3;
                color: #FFE4E1;
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
                var nHora = new Date(); 
                var hora = nHora.getHours();
                var Cumpr = "Bom Dia!";
                if(hora >= 12){
                    Cumpr = "Boa Tarde!";
                }
                if(hora >= 18){
                    Cumpr = "Boa Noite!";
                }
                document.getElementById("botimpr").style.visibility = "hidden"; 
                document.getElementById("botimprReg").style.visibility = "hidden"; 
                document.getElementById("botInsReg").style.visibility = "hidden"; 
                document.getElementById("botApagaBem").style.visibility = "hidden";
                document.getElementById("imgBensconfig").style.visibility = "hidden";
                document.getElementById("numregistro").disabled = true;
                document.getElementById("selectBens").style.visibility = "hidden"; 
                document.getElementById("imgHelpBens").style.visibility = "hidden"; 
                document.getElementById("botabreReiv").style.visibility = "hidden";
                document.getElementById("botApagaReiv").style.visibility = "hidden";
                document.getElementById("imprReciboReiv").style.visibility = "hidden";
                document.getElementById("imprReinv").style.visibility = "hidden";
                document.getElementById("insReinv").style.visibility = "hidden";
                document.getElementById("salvaReivindc0").style.visibility = "hidden";
                document.getElementById("salvaReivindc1").style.visibility = "hidden";

                if(parseInt(document.getElementById("guardaEditBens").value) === 0 && parseInt(document.getElementById("guardaFiscBens").value) === 0 && parseInt(document.getElementById("guardaSoInsBens").value) === 0){
                    $("#carregaBens").load("modulos/leituras/carMsg.php?msgtipo=1&cumpr="+encodeURIComponent(Cumpr));
                }else{
                    $("#carregaBens").load("modulos/bensEncont/relBens.php?acao="+document.getElementById("guardaIndex").value);
                    document.getElementById("selectBens").style.visibility = "visible"; 
                    document.getElementById("imgHelpBens").style.visibility = "visible"; 
                    document.getElementById("botabreReiv").style.visibility = "visible";
                }
                
                $('#carregaTema').load('modulos/config/carTema.php?carpag=pagBens');

                //Impedir a mudança de data do registro de bem encontrado -> liberado a pedido em 16/08/2024
//                DataPr = compareDates ("30/06/2024", dataAtualFormatada()); // se o prazo for maior que a data atual
//                if(DataPr == true){ // se for maior que a data atual
                    $('#dataregistro').datepicker({ uiLibrary: 'bootstrap5', locale: 'pt-br', format: 'dd/mm/yyyy' });
//                }else{
//                    document.getElementById("dataregistro").disabled = true;
//                }

                $('#dataachado').datepicker({ uiLibrary: 'bootstrap5', locale: 'pt-br', format: 'dd/mm/yyyy' });
                $('#dataReivind').datepicker({ uiLibrary: 'bootstrap5', locale: 'pt-br', format: 'dd/mm/yyyy' });
                $('#dataPerdido').datepicker({ uiLibrary: 'bootstrap5', locale: 'pt-br', format: 'dd/mm/yyyy' });

                $("#cpfproprietario").mask("999.999.999-99");
                $("#configCpfBens").mask("999.999.999-99");
                $("#dataregistro").mask("99/99/9999");
                $("#dataachado").mask("99/99/9999");
                $("#dataReivind").mask("99/99/9999");
                $("#dataPerdido").mask("99/99/9999");

                if(parseInt(document.getElementById("guardaEditBens").value) === 1){ // tem que estar autorizado no cadastro de usuários
                    document.getElementById("imprReinv").style.visibility = "visible";
                    document.getElementById("insReinv").style.visibility = "visible";
                    document.getElementById("salvaReivindc0").style.visibility = "visible";
                    document.getElementById("salvaReivindc1").style.visibility = "visible";
                    if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admIns").value)){
                        document.getElementById("botInsReg").style.visibility = "visible"; 
                        document.getElementById("botimpr").style.visibility = "visible"; 
                    }
                    if(parseInt(document.getElementById("UsuAdm").value) > 6){
                        document.getElementById("botInsReg").style.visibility = "visible";
                        document.getElementById("botApagaBem").style.visibility = "visible";
                        document.getElementById("imgBensconfig").style.visibility = "visible";
                        document.getElementById("botimpr").style.visibility = "visible"; 
                    }
                }
                if(parseInt(document.getElementById("guardaSoInsBens").value) === 1){ // Para pessoal da portaria registrar nos fins de semana
                    document.getElementById("botInsReg").style.visibility = "visible";
                    document.getElementById("insReinv").style.visibility = "visible";
                    document.getElementById("salvaReivindc0").style.visibility = "visible";
                    document.getElementById("salvaReivindc1").style.visibility = "visible";
                }

                $("#configSelecBens").change(function(){
                    if(document.getElementById("configSelecBens").value == ""){
                        document.getElementById("configCpfBens").value = "";
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=buscausuario&codigo="+document.getElementById("configSelecBens").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configCpfBens").value = format_CnpjCpf(Resp.cpf);
                                        if(parseInt(Resp.bens) === 1){
                                            document.getElementById("preencheBens").checked = true;
                                        }else{
                                            document.getElementById("preencheBens").checked = false;
                                            document.getElementById("encaminhaBens").checked = false;
                                            document.getElementById("encaminhaBens").disabled = true;
                                        }
                                        if(parseInt(Resp.fiscbens) === 1){
                                            document.getElementById("fiscBens").checked = true;
                                        }else{
                                            document.getElementById("fiscBens").checked = false;
                                        }
                                        if(parseInt(Resp.soinsbens) === 1){
                                            document.getElementById("soPreencheBens").checked = true;
                                        }else{
                                            document.getElementById("soPreencheBens").checked = false;
                                        }
                                        if(parseInt(Resp.encbens) === 1){
                                            document.getElementById("encaminhaBens").checked = true;
                                            document.getElementById("encaminhaBens").disabled = false;
                                        }else{
                                            document.getElementById("encaminhaBens").checked = false;
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

                $("#configCpfBens").click(function(){
                    document.getElementById("configSelecBens").value = "";
                    document.getElementById("configCpfBens").value = "";
                    document.getElementById("preencheBens").checked = false;
                    document.getElementById("fiscBens").checked = false;
                    document.getElementById("soPreencheBens").checked = false;
                });
                $("#configCpfBens").change(function(){
                    document.getElementById("configSelecBens").value = "";
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=buscacpf&cpf="+encodeURIComponent(document.getElementById("configCpfBens").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configSelecBens").value = Resp.PosCod;
                                        if(parseInt(Resp.bens) === 1){
                                            document.getElementById("preencheBens").checked = true;
                                        }else{
                                            document.getElementById("preencheBens").checked = false;
                                        }
                                        if(parseInt(Resp.fiscbens) === 1){
                                            document.getElementById("fiscBens").checked = true;
                                        }else{
                                            document.getElementById("fiscBens").checked = false;
                                        }
                                        if(parseInt(Resp.soinsbens) === 1){
                                            document.getElementById("soPreencheBens").checked = true;
                                        }else{
                                            document.getElementById("soPreencheBens").checked = false;
                                        }
                                    }
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.");
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("preencheBens").checked = false;
                                        document.getElementById("fiscBens").checked = false;
                                        document.getElementById("soPreencheBens").checked = false;
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

                $("#selecMesAno").change(function(){
                    document.getElementById("selecAno").value = "";
                    if(document.getElementById("selecMesAno").value != ""){
                        window.open("modulos/bensEncont/imprListaBens.php?acao=listamesBens&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), document.getElementById("selecMesAno").value);
                        document.getElementById("selecMesAno").value = "";
                        document.getElementById("relacimprBens").style.display = "none";
                    }
                });
                $("#selecAno").change(function(){
                    document.getElementById("selecMesAno").value = "";
                    if(document.getElementById("selecAno").value != ""){
                        window.open("modulos/bensEncont/imprListaBens.php?acao=listaanoBens&ano="+encodeURIComponent(document.getElementById("selecAno").value), document.getElementById("selecAno").value);
                        document.getElementById("selecAno").value = "";
                        document.getElementById("relacimprBens").style.display = "none";
                    }
                });
                $("#resumoBens").click(function(){
                    window.open("modulos/bensEncont/imprResumo.php?acao=resumo", "Resumo");
                    document.getElementById("relacimprBens").style.display = "none";
                });
                $("#imprReciboReiv").click(function(){
                    if(parseInt(document.getElementById("mudou").value) === 1){
                        $.confirm({
                            title: 'Alerta',
                            content: 'Há alterações não salvas. Confirma gerar PDF sem as alterações?',
                            autoClose: 'Não|10000',
                            draggable: true,
                            buttons: {
                                Sim: function () {
                                    window.open("modulos/bensEncont/imprRecReiv.php?acao=imprReciboReiv&codigo="+document.getElementById("guardaid").value, "Recibo");
                                },
                                Não: function () {
                                }
                            }
                        });
                    }else{
                        window.open("modulos/bensEncont/imprRecReiv.php?acao=imprReciboReiv&codigo="+document.getElementById("guardaid").value, "Recibo");
                    }
                });

                $("#selecprocesso").change(function(){
                    document.getElementById("descprocesso").innerHTML = "";
                    document.getElementById("botsalvaregrec").disabled = false;
                    let element = document.getElementById('botsalvaregrec');
                    element.classList.remove('botpadrinat');
                });

                $("#selecdestino").change(function(){
                    document.getElementById("botsalvaregdest").disabled = false;
                    let element = document.getElementById('botsalvaregdest');
                    element.classList.remove('botpadrinat');
                });

                $("#imprReinv").click(function(){
                    document.getElementById("relacImprReiv").style.display = "block";
                });


            }); // fim do ready

            function abreRegistro(){
                document.getElementById("guardacod").value = 0;
                document.getElementById("botsalvareg").style.visibility = "visible"; 
                document.getElementById("dataregistro").value = document.getElementById("guardahoje").value;
                document.getElementById("dataachado").value = document.getElementById("guardahoje").value;
//                document.getElementById("numprocesso").innerHTML = "";
                document.getElementById("descdobem").value = "";
                document.getElementById("localachado").value = "";
                document.getElementById("nomeachou").value = "";
                document.getElementById("telefachou").value = "";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=buscanum&dataregistro="+encodeURIComponent(document.getElementById("dataregistro").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("numregistro").value = Resp.numprocesso;
                                }
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
                document.getElementById("botApagaBem").style.visibility = "hidden";
                document.getElementById("relacmodalRegistro").style.display = "block";
            }

            function checaNumRegistro(){
                document.getElementById("mudou").value = "1";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=checaNumero&numero="+encodeURIComponent(document.getElementById("numregistro").value)+"&codigo="+document.getElementById("guardacod").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    if(parseInt(Resp.achou) > 0){
                                        $.confirm({
                                            title: 'Atenção!',
                                            content: 'Este número de processo já existe. <br>Foi registrado em '+Resp.data+'.',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                        return false;
                                    }
//                                    document.getElementById("numregistro").value = Resp.numprocesso;
                                }
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function dataAtualFormatada(){
                var data = new Date(),
                dia  = data.getDate().toString(),
                diaF = (dia.length == 1) ? '0'+dia : dia,
                mes  = (data.getMonth()+1).toString(), //+1 pois no getMonth Janeiro começa com zero.
                mesF = (mes.length == 1) ? '0'+mes : mes,
                anoF = data.getFullYear();
                return diaF+"/"+mesF+"/"+anoF;
            }

            function compareDates (date1, date2) {
                let parts1 = date1.split('/') // separa a data pelo caracter '/'
                date1 = new Date(parts1[2], parts1[1] - 1, parts1[0]) // formata 'date'

                let parts2 = date2.split('/') // separa a data pelo caracter '/'
                date2 = new Date(parts2[2], parts2[1] - 1, parts2[0]) // formata 'date'
                  // compara se a data informada é maior que a data atual e retorna true ou false
                return date1 > date2 ? true : false
            }

            function salvaModalRegistro(){
                if(parseInt(document.getElementById("mudou").value) === 0){
                    document.getElementById("relacmodalRegistro").style.display = "none";
                    return false;
                }
                if(compareDates(document.getElementById("dataachado").value, document.getElementById("dataregistro").value) == true){
                    let element = document.getElementById('dataachado');
                    element.classList.add('destacaBorda');
                    $.confirm({
                        title: 'Atenção!',
                        content: 'A data do registro é de antes do objeto ser encontrado.',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }

                if(document.getElementById("dataregistro").value === ""){
                    let element = document.getElementById('dataregistro');
                    element.classList.add('destacaBorda');
                    document.getElementById("dataregistro").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira a data do registro";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("dataachado").value === ""){
                    let element = document.getElementById('dataachado');
                    element.classList.add('destacaBorda');
                    document.getElementById("dataachado").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira a data em que foi encontradp";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("descdobem").value === ""){
                    let element = document.getElementById('descdobem');
                    element.classList.add('destacaBorda');
                    document.getElementById("descdobem").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Escreva uma breve descrição do bem encontrado";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("localachado").value === ""){
                    let element = document.getElementById('localachado');
                    element.classList.add('localachado');
                    document.getElementById("localachado").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Escreva uma breve descrição do local onde foi encontrado";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("nomeachou").value === ""){
                    let element = document.getElementById('nomeachou');
                    element.classList.add('nomeachou');
                    document.getElementById("nomeachou").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Anote o nome do colaborador que encontrou";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("telefachou").value === ""){
                    let element = document.getElementById('telefachou');
                    element.classList.add('telefachou');
                    document.getElementById("telefachou").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Anote o telefone do colaborador que encontrou";
                    $('#mensagem').fadeOut(2000);
                    return false;
                }
                 if(!validaData(document.getElementById("dataachado").value)){
                    let element = document.getElementById('dataachado');
                    element.classList.add('destacaBorda');
                    $.confirm({
                        title: 'Atenção!',
                        content: 'A data está incorreta.',
                        draggable: true,
                        buttons: {
                            OK: function(){}
                        }
                    });
                    return false;
                }
                document.getElementById("botsalvareg").disabled = true; // para evitar de salvar duas vezes em sistemas lentos
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=salvaRegBem&codigo="+document.getElementById("guardacod").value+
                    "&dataregistro="+encodeURIComponent(document.getElementById("dataregistro").value)+
                    "&dataachado="+encodeURIComponent(document.getElementById("dataachado").value)+
                    "&descdobem="+encodeURIComponent(document.getElementById("descdobem").value)+
                    "&localachado="+encodeURIComponent(document.getElementById("localachado").value)+
                    "&nomeachou="+encodeURIComponent(document.getElementById("nomeachou").value)+
                    "&numrelato="+encodeURIComponent(document.getElementById("numregistro").value)+
                    "&telefachou="+encodeURIComponent(document.getElementById("telefachou").value)
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("guardacod").value = Resp.codigonovo;
                                    document.getElementById("guardaNumRelat").value = Resp.numrelat;
                                    document.getElementById("mudou").value = "0";
                                    $("#carregaBens").load("modulos/bensEncont/relBens.php");
                                    document.getElementById("relacmodalRegistro").style.display = "none";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
                document.getElementById("botsalvareg").disabled = false;
            }

            function verRegistroRcb(Cod){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=buscaBem&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("guardacod").value = Cod;
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("dataregistro").value = Resp.datareg;
                                    document.getElementById("dataachado").value = Resp.dataachou;
                                    document.getElementById("descdobem").value = Resp.descdobem;
                                    document.getElementById("localachado").value = Resp.localachou;
                                    document.getElementById("nomeachou").value = Resp.nomeachou;
                                    document.getElementById("telefachou").value = Resp.telefachou;
                                    document.getElementById("numregistro").value = Resp.numprocesso;
                                    document.getElementById("guardaNumRelat").value = Resp.numprocesso;
//                                    document.getElementById("numprocesso").innerHTML = "Registrado sob nº "+Resp.numprocesso;
                                    document.getElementById("botsalvareg").innerHTML = "Salvar";
                                    document.getElementById("botApagaBem").style.visibility = "visible";
                                    document.getElementById("relacmodalRegistro").style.display = "block";
                                    document.getElementById("numregistro").disabled = false;
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function mostraBem(Cod, modal, Restit){
                document.getElementById("guardamodal").value = modal;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=buscaBem&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("guardacod").value = Cod;
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("codusuins").value = Resp.codusuins;
                                    document.getElementById("etiqnomeusurestit").innerHTML = Resp.nomeusurestit;
                                    if(parseInt(modal) === 1){
                                        document.getElementById("numprocessotransf").innerHTML = Resp.numprocesso;
                                        document.getElementById("etiqprocessoReg").innerHTML = "registrado por "+Resp.nomeusuins+" em "+Resp.datareg+".";
                                        document.getElementById("descdobemtransf").innerHTML = Resp.descdobem;
                                        document.getElementById("relacmodalTransfGuarda").style.display = "block";
                                    }
                                    if(parseInt(modal) === 2){
                                        if(parseInt(Restit) > 0){
                                            document.getElementById('nomeproprietario').disabled = true;
                                            document.getElementById('cpfproprietario').disabled = true;
                                            document.getElementById('telefproprietario').disabled = true;
                                            document.getElementById('botsalvaRestit').disabled = true;
                                            document.getElementById("botsalvaRestit").style.visibility = "hidden";
                                        }else{
                                            document.getElementById('nomeproprietario').disabled = false;
                                            document.getElementById('cpfproprietario').disabled = false;
                                            document.getElementById('telefproprietario').disabled = false;
                                            document.getElementById('botsalvaRestit').disabled = false;
                                            document.getElementById("botsalvaRestit").style.visibility = "visible";
                                        }
                                        document.getElementById("numprocessoRest").innerHTML = Resp.numprocesso;
                                        document.getElementById("etiqprocessoRest").innerHTML = "registrado por "+Resp.nomeusuins+" em "+Resp.datareg+".";
                                        document.getElementById("descdobemRest").innerHTML = Resp.descdobem;
                                        document.getElementById('nomeproprietario').value = Resp.nomepropriet;
                                        document.getElementById('cpfproprietario').value = Resp.cpfpropriet;
                                        document.getElementById('telefproprietario').value = Resp.telefpropriet;
                                        document.getElementById("relacmodalRestit").style.display = "block";
                                    }
                                    if(parseInt(modal) === 3){
                                        if(parseInt(Resp.intervalo) < 0){ // aguardar 3 meses para encaminhar o bem
                                            $.confirm({
                                                title: 'Atenção!',
                                                content: 'Ainda não cumpriu o prazo de 90 dias.',
                                                draggable: true,
                                                buttons: {
                                                    OK: function(){}
                                                }
                                            });
                                            return false;
                                        }else{
                                            document.getElementById("numprocessoEncam").innerHTML = Resp.numprocesso;
                                            document.getElementById("etiqprocessoEncam").innerHTML = "registrado por "+Resp.nomeusuins+" em "+Resp.datareg+".";
                                            document.getElementById("descdobemEncam").innerHTML = Resp.descdobem;
                                            document.getElementById("relacmodalEncam").style.display = "block";
                                        }
                                    }
                                    if(parseInt(modal) === 4){ // destinação do bem requer estar no nível adm selecionado em parâmetros do sistema
                                        if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admEdit").value)){
                                            document.getElementById("numprocessoDest").innerHTML = Resp.numprocesso;
                                            document.getElementById("etiqprocessoDest").innerHTML = "registrado por "+Resp.nomeusuins+" em "+Resp.datareg+".";
                                            document.getElementById("descdobemDest").innerHTML = Resp.descdobem;
                                            document.getElementById('nomeproprietario').value = Resp.nomepropriet;
                                            document.getElementById('cpfproprietario').value = Resp.cpfpropriet;
                                            document.getElementById('telefproprietario').value = Resp.telefpropriet;
                                            document.getElementById("codusudest").value = 0;
                                            document.getElementById("etiqAssinaturaDest").innerHTML = document.getElementById("usuarioNome").value;
                                            document.getElementById("botsalvaregdest").innerHTML = "Encaminhar";
                                            document.getElementById('selecdestino').value = "";
                                            document.getElementById('selecprocesso').value = "";
                                            document.getElementById("relacmodalDest").style.display = "block";
                                        }else{
                                            $.confirm({
                                                title: 'Informação!',
                                                content: 'É requerido maior nível administrativo para consolidar a destinação',
                                                autoClose: 'OK|10000',
                                                draggable: true,
                                                buttons: {
                                                    OK: function(){}
                                                }
                                            });
                                        }
                                    }
                                    if(parseInt(modal) === 5){
                                        document.getElementById("numprocessoReceb").innerHTML = Resp.numprocesso;
                                        document.getElementById("etiqprocessoReceb").innerHTML = "registrado por "+Resp.nomeusuins+" em "+Resp.datareg+".";
                                        document.getElementById("descdobemReceb").innerHTML = Resp.descdobem;
                                        document.getElementById("descdestino").innerHTML = Resp.DescDest;
                                        document.getElementById("descprocesso").innerHTML = Resp.DescProcesso;
                                        document.getElementById("codusureceb").value = 0; //document.getElementById("usuarioID").value;
                                        document.getElementById("etiqfinalidade").innerHTML = " finalidade";
                                        document.getElementById("etiqAssinatura").innerHTML = document.getElementById("usuarioNome").value;
                                        document.getElementById("botsalvaregrec").innerHTML = "Receber";
                                        document.getElementById("selecprocesso").value = Resp.codProcesso;
                                        document.getElementById("relacmodalReceb").style.display = "block";
                                    }
                                    if(parseInt(modal) === 6){
                                        document.getElementById("numprocessoArq").innerHTML = Resp.numprocesso;
                                        document.getElementById("etiqprocessoArq").innerHTML = "registrado por "+Resp.nomeusuins+" em "+Resp.datareg+".";
                                        document.getElementById("descdobemArq").innerHTML = Resp.descdobem;
                                        document.getElementById("descdestinoArq").innerHTML = Resp.DescDest;
                                        document.getElementById("descprocessoArq").innerHTML = Resp.DescProcesso;
                                        document.getElementById("relacmodalArquivar").style.display = "block";
                                    }
                                    //Superusuário corrigindo finalidade 
                                    if(parseInt(modal) === 7){
                                        document.getElementById("numprocessoReceb").innerHTML = Resp.numprocesso;
                                        document.getElementById("etiqprocessoReceb").innerHTML = "registrado por "+Resp.nomeusuins+" em "+Resp.datareg+".";
                                        document.getElementById("descdobemReceb").innerHTML = Resp.descdobem;
                                        document.getElementById("descdestino").innerHTML = Resp.DescDest;
                                        document.getElementById("descprocesso").innerHTML = Resp.DescProcesso+"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
                                        document.getElementById("codusureceb").value = Resp.UsuEncProcesso;
                                        document.getElementById("etiqfinalidade").innerHTML = "modificar finalidade";
                                        document.getElementById("etiqAssinatura").innerHTML = Resp.NomeEncProcesso;
                                        document.getElementById("botsalvaregrec").innerHTML = "Corrigir";
                                        document.getElementById("selecprocesso").value = Resp.codProcesso;
                                        document.getElementById("botsalvaregrec").disabled = true;
                                        let element = document.getElementById('botsalvaregrec');
                                        element.classList.add('botpadrinat');
                                        document.getElementById("relacmodalReceb").style.display = "block";
                                    }
                                    //Superusuário corrigindo destino
                                    if(parseInt(modal) === 8){
                                        document.getElementById("numprocessoDest").innerHTML = Resp.numprocesso;
                                        document.getElementById("etiqprocessoDest").innerHTML = "registrado por "+Resp.nomeusuins+" em "+Resp.datareg+".";
                                        document.getElementById("descdobemDest").innerHTML = Resp.descdobem;
                                        document.getElementById("selecdestino").value = Resp.codSetorDestino;
                                        document.getElementById('nomeproprietario').value = Resp.nomepropriet;
                                        document.getElementById('cpfproprietario').value = Resp.cpfpropriet;
                                        document.getElementById('telefproprietario').value = Resp.telefpropriet;
                                        document.getElementById("botsalvaregdest").innerHTML = "Corrigir";
                                        document.getElementById("codusudest").value = Resp.UsuDestino;
                                        document.getElementById("etiqAssinaturaDest").innerHTML = Resp.NomeUsuDestino;
                                        document.getElementById("botsalvaregdest").disabled = true;

                let element = document.getElementById('botsalvaregdest');
                    element.classList.add('botpadrinat');

                                        document.getElementById("relacmodalDest").style.display = "block";
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            //Aceita a transferência do objeto para guarda
            function salvaModalTransf(){
                $Mensag = "Aceitar a guarda.";
                if(parseInt(document.getElementById("codusuins").value) === parseInt(document.getElementById("usuarioID").value)){
                    $Mensag = "Aceitar a guarda. <br>Mesmo usuário que registrou?";
                }
                $.confirm({
                    title: $Mensag,
                    content: 'Confirma aceitar a guarda deste objeto?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=RcbGuardaBem&codigo="+document.getElementById("guardacod").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $.confirm({
                                                    title: 'Sucesso!',
                                                    content: 'Objeto colocado sob guarda de '+document.getElementById("usuarioNome").value,
                                                    draggable: true,
                                                    buttons: {
                                                        OK: function(){}
                                                    }
                                                });
                                                document.getElementById("relacmodalTransfGuarda").style.display = "none";
                                                $("#carregaBens").load("modulos/bensEncont/relBens.php");
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

            //Restituição do objeto
            function modalRestit(){
                if(document.getElementById("nomeproprietario").value === ""){
                    let element = document.getElementById('nomeproprietario');
                    element.classList.add('destacaBorda');
                    document.getElementById("nomeproprietario").focus();
                    $('#mensagemrest').fadeIn("slow");
                    document.getElementById("mensagemrest").innerHTML = "Insira o nome do proprietário";
                    $('#mensagemrest').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("cpfproprietario").value === ""){
                    let element = document.getElementById('cpfproprietario');
                    element.classList.add('destacaBorda');
                    document.getElementById("cpfproprietario").focus();
                    $('#mensagemrest').fadeIn("slow");
                    document.getElementById("mensagemrest").innerHTML = "Insira o CPF do proprietário";
                    $('#mensagemrest').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("telefproprietario").value === ""){
                    let element = document.getElementById('telefproprietario');
                    element.classList.add('destacaBorda');
                    document.getElementById("telefproprietario").focus();
                    $('#mensagemrest').fadeIn("slow");
                    document.getElementById("mensagemrest").innerHTML = "Insira o número do telefone do proprietário";
                    $('#mensagemrest').fadeOut(2000);
                    return false;
                }
                $.confirm({
                    title: 'Restituição',
                    content: 'Confirma a restituição deste objeto e arquivar o processo?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=restituiBem&codigo="+document.getElementById("guardacod").value
                                +"&nomeproprietario="+encodeURIComponent(document.getElementById("nomeproprietario").value)
                                +"&cpfproprietario="+encodeURIComponent(document.getElementById("cpfproprietario").value)
                                +"&telefproprietario="+encodeURIComponent(document.getElementById("telefproprietario").value), true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $.confirm({
                                                    title: 'Sucesso!',
                                                    content: 'Objeto restituido ao proprietário.',
                                                    draggable: true,
                                                    buttons: {
                                                        OK: function(){}
                                                    }
                                                });
                                                document.getElementById("relacmodalRestit").style.display = "none";
                                                $("#carregaBens").load("modulos/bensEncont/relBens.php?acao=Restituídos");
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

            function modalRcbCSG(){
                $Mensag = "Guarda para destinação.";
                if(parseInt(document.getElementById("codusuins").value) === parseInt(document.getElementById("usuarioID").value)){
                    $Mensag = "Aceitar a guarda. <br>Mesmo usuário que registrou?";
                }
                $.confirm({
                    title: $Mensag,
                    content: 'Confirma aceitar a guarda deste objeto?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=encamBemCsg&codigo="+document.getElementById("guardacod").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $.confirm({
                                                    title: 'Sucesso!',
                                                    content: 'Objeto recebido no SSV por '+document.getElementById("usuarioNome").value,
                                                    draggable: true,
                                                    buttons: {
                                                        OK: function(){}
                                                    }
                                                });
                                                document.getElementById("relacmodalEncam").style.display = "none";
                                                $("#carregaBens").load("modulos/bensEncont/relBens.php?acao=Guardar");
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

            //Aceita a transferência do objeto para guarda
            function modalDestino(){ // encaminha o bem para o destino final
                if(parseInt(document.getElementById("selecdestino").value) === 0 || document.getElementById("selecdestino").value === ""){
                    let element = document.getElementById('selecdestino');
                    element.classList.add('destacaBorda');
                    document.getElementById("selecdestino").focus();
                    $('#mensagemdest').fadeIn("slow");
                    document.getElementById("mensagemdest").innerHTML = "Selecione o destino dado ao objeto";
                    $('#mensagemdest').fadeOut(2000);
                    return false;
                }
//                if(parseInt(document.getElementById("selecprocesso").value) === 1){
//                    let element = document.getElementById('selecprocesso');
//                    element.classList.add('destacaBorda');
//                    document.getElementById("selecprocesso").focus();
//                    $('#mensagemdest').fadeIn("slow");
//                    document.getElementById("mensagemdest").innerHTML = "Selecione o processo adequado";
//                    $('#mensagemdest').fadeOut(2000);
//                    return false;
//                }
                $.confirm({
                    title: 'Destinação',
                    content: 'Confirma a destinação dada ao objeto?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=encdestinaBem&codigo="+document.getElementById("guardacod").value
                                +"&selecdestino="+document.getElementById("selecdestino").value
                                +"&codusudest="+document.getElementById("codusudest").value, true);
//                                +"&selecprocesso="+document.getElementById("selecprocesso").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $.confirm({
                                                    title: 'Sucesso!',
                                                    content: 'Objeto encaminhado.',
                                                    draggable: true,
                                                    buttons: {
                                                        OK: function(){}
                                                    }
                                                });
                                                document.getElementById("relacmodalDest").style.display = "none";
                                                $("#carregaBens").load("modulos/bensEncont/relBens.php?acao=Destinar");
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

            function modalRecebe(){ // recebe o bem encaminhado para destino final
                if(parseInt(document.getElementById("selecprocesso").value) === 1 || document.getElementById("selecprocesso").value === ""){
                    let element = document.getElementById('selecprocesso');
                    element.classList.add('destacaBorda');
                    document.getElementById("selecprocesso").focus();
                    $('#mensagemRec').fadeIn("slow");
                    document.getElementById("mensagemRec").innerHTML = "Selecione o processo adequado";
                    $('#mensagemRec').fadeOut(2000);
                    return false;
                }
                let Mensag = "Confirma corrigir o processo?";
                if(parseInt(document.getElementById("codusureceb").value) === 0){
                    Mensag = "Confirma receber o bem para para a finalidade descrita?";
                }

                $.confirm({
                    title: 'Destinação Final',
                    content: Mensag, 
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=recebeBemDest&codigo="+document.getElementById("guardacod").value+"&selecprocesso="+document.getElementById("selecprocesso").value+"&codusureceb="+document.getElementById("codusureceb").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $.confirm({
                                                    title: 'Sucesso!',
                                                    content: 'Recebimento anotado.',
                                                    draggable: true,
                                                    buttons: {
                                                        OK: function(){}
                                                    }
                                                });
                                                document.getElementById("relacmodalReceb").style.display = "none";
                                                if(parseInt(document.getElementById("guardamodal").value) === 7){ // corrigindo finalidade
                                                    $("#carregaBens").load("modulos/bensEncont/relBens.php?acao=Arquivar");
                                                }else{
                                                    $("#carregaBens").load("modulos/bensEncont/relBens.php?acao=Receber");
                                                }
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

            function modalArquiva(){ // arquiva e encerra o processo
                $.confirm({
                    title: 'Destinação Final',
                    content: 'Confirma o encerramento do processo?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=encerraProcesso&codigo="+document.getElementById("guardacod").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $.confirm({
                                                    title: 'Sucesso!',
                                                    content: 'Processo arquivado.',
                                                    draggable: true,
                                                    buttons: {
                                                        OK: function(){}
                                                    }
                                                });
                                                document.getElementById("relacmodalArquivar").style.display = "none";
                                                $("#carregaBens").load("modulos/bensEncont/relBens.php?acao=Arquivar");
//                                                window.opener.location.reload(true);
//                                                window.location.reload();
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
            
            function ApagarBem(){
                $.confirm({
                    title: 'Confirmação',
                    content: 'Confirma apagar este lançamento?<br>Não haverá possibilidade de recuperação.<br>Continua?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=apagaBem&codigo="+document.getElementById("guardacod").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                document.getElementById("relacmodalRestit").style.display = "none";
                                                document.getElementById("relacmodalRegistro").style.display = "none"; // mesma função em dois modais
                                                $("#carregaBens").load("modulos/bensEncont/relBens.php");
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

            function fechaModalReg(){
                document.getElementById("relacmodalRegistro").style.display = "none";
            }
            function fechaModalReiv(){
                document.getElementById("relacmodalReivindic").style.display = "none";
            }
            function abreImprBens(){
                document.getElementById("relacimprBens").style.display = "block";
            }
            function fechaImprBens(){
                document.getElementById("relacimprBens").style.display = "none";
            }
            function modif(){ // assinala se houve qualquer modificação
                document.getElementById("mudou").value = "1";
            }
            function tiraBorda(id){
                let element = document.getElementById(id);
                element.classList.remove('destacaBorda');
            }
            function imprProcesso(Cod){
                window.open("modulos/bensEncont/imprReg.php?acao=imprProcesso&codigo="+Cod, Cod);
            }
            function resumoUsuBens(){
                window.open("modulos/bensEncont/imprUsuBens.php?acao=listaUsuarios", "BensUsu");
            }
            function imprRestit(){
                if(document.getElementById("nomeproprietario").value === ""){
                    $.confirm({
                        title: 'Confirmação!',
                        content: 'Quer imprimir sem o nome do proprietário?',
                        autoClose: 'Não|10000',
                        draggable: true,
                        buttons: {
                            Sim: function () {
                                window.open("modulos/bensEncont/imprReg.php?acao=imprReciboRest&codigo="+document.getElementById("guardacod").value+"&nomeproprietario="+document.getElementById("nomeproprietario").value+"&cpfproprietario="+document.getElementById("cpfproprietario").value+"&telefproprietario="+document.getElementById("telefproprietario").value, document.getElementById("guardacod").value);
                            },
                            Não: function () {
                                document.getElementById("nomeproprietario").focus();
                            }
                        }
                    });
                }else{
                    window.open("modulos/bensEncont/imprReg.php?acao=imprReciboRest&codigo="+document.getElementById("guardacod").value+"&nomeproprietario="+document.getElementById("nomeproprietario").value+"&cpfproprietario="+document.getElementById("cpfproprietario").value+"&telefproprietario="+document.getElementById("telefproprietario").value, document.getElementById("guardacod").value);
                }
            }

            function marcaBem(obj, Campo){
                if(obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                if(document.getElementById("configSelecBens").value == ""){
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
                if(Campo == "bens"){
                    if(parseInt(Valor) === 0){
                        document.getElementById("encaminhaBens").checked = false;
                        document.getElementById("encaminhaBens").disabled = true;
                    }else{
                        document.getElementById("encaminhaBens").disabled = false;
                    }
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=configMarcafBem&codigo="+document.getElementById("configSelecBens").value
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
                                            content: 'Não restaria outro marcado para gerenciar os Achados e Perdidos.',
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

            function abreBensConfig(){
                document.getElementById("preencheBens").checked = false;
                document.getElementById("fiscBens").checked = false;
                document.getElementById("soPreencheBens").checked = false;
                document.getElementById("configCpfBens").value = "";
                document.getElementById("configSelecBens").value = "";
                document.getElementById("modalBensConfig").style.display = "block";
            }
            function abreReiv(){
                document.getElementById("relacmodalReivindic").style.display = "block";
                $("#faixaReivind").load("modulos/bensEncont/jReivind.php");
            }

            function carregaInsReivind(Cod){
                document.getElementById("guardaid").value = 0;
                document.getElementById("mudou").value = 0;
                document.getElementById("botApagaReiv").style.visibility = "hidden"; 
                document.getElementById("imprReciboReiv").style.visibility = "hidden";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=buscaProcReivind&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("numregistroReiv").value = Resp.numprocesso;
                                    document.getElementById("dataReivind").value = document.getElementById("guardahoje").value;
                                    document.getElementById("dataPerdido").value = document.getElementById("guardahoje").value;
                                    document.getElementById("nomereclamante").value = "";
                                    document.getElementById("emailreclamante").value = "";
                                    document.getElementById("telefreclamante").value = "";
                                    document.getElementById("localperdeu").value = "";
                                    document.getElementById("descdobemPerdeu").value = "";
                                    document.getElementById("obsperdeu").value = "";
                                    document.getElementById("bemEncontrado").checked = false;
                                    document.getElementById("bemEntregue").checked = false;
                                    document.getElementById("relacEditaReivindic").style.display = "block";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function carregaReivind(Cod){
                document.getElementById("guardaid").value = Cod;
                document.getElementById("mudou").value = 0;
                document.getElementById("imprReciboReiv").style.visibility = "visible";
                if(parseInt(document.getElementById("UsuAdm").value) > 6){
                    document.getElementById("botApagaReiv").style.visibility = "visible";
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=buscaReivind&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("numregistroReiv").value = Resp.processo;
                                    document.getElementById("dataReivind").value = Resp.datareiv;
                                    document.getElementById("dataPerdido").value = Resp.dataperdeu;
                                    document.getElementById("nomereclamante").value = Resp.nome;
                                    document.getElementById("emailreclamante").value = Resp.email;
                                    document.getElementById("telefreclamante").value = Resp.telef;
                                    document.getElementById("localperdeu").value = Resp.localperdeu;
                                    document.getElementById("descdobemPerdeu").value = Resp.descdobem;
                                    document.getElementById("obsperdeu").value = Resp.observ;
                                    if(parseInt(Resp.encontrado) === 0){
                                        document.getElementById("bemEncontrado").checked = false;    
                                    }else{
                                        document.getElementById("bemEncontrado").checked = true;
                                    }
                                    if(parseInt(Resp.entregue) === 0){
                                        document.getElementById("bemEntregue").checked = false;    
                                    }else{
                                        document.getElementById("bemEntregue").checked = true;
                                    }
                                    document.getElementById("relacEditaReivindic").style.display = "block";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function salvaReivindic(Op){
                if(parseInt(document.getElementById("mudou").value) === 0){
                    if(parseInt(Op) === 1){ //Salvar e Fechar
                        document.getElementById("relacEditaReivindic").style.display = "none";
                    }else{
                        if(parseInt(document.getElementById("guardaid").value) === 1){ // não está inserindo
                            $('#mensagemReinvOK').fadeIn("slow");
                            document.getElementById("mensagemReinvOK").innerHTML = "Tudo em ordem.";
                            $('#mensagemReinvOK').fadeOut(2000); 
                        }
                    }
                    return false;
                }
                if(document.getElementById("dataReivind").value == ""){
                    document.getElementById("dataReivind").focus();
                    $('#mensagemReinv').fadeIn("slow");
                    document.getElementById("mensagemReinv").innerHTML = "Insira a data do registro.";
                    $('#mensagemReinv').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("dataPerdido").value == ""){
                    document.getElementById("dataPerdido").focus();
                    $('#mensagemReinv').fadeIn("slow");
                    document.getElementById("mensagemReinv").innerHTML = "Insira a data em que o objeto foi perdido.";
                    $('#mensagemReinv').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("descdobemPerdeu").value == ""){
                    document.getElementById("descdobemPerdeu").focus();
                    $('#mensagemReinv').fadeIn("slow");
                    document.getElementById("mensagemReinv").innerHTML = "Descreva o objeto reivindicado.";
                    $('#mensagemReinv').fadeOut(2000); 
                    return false;
                }
                if(document.getElementById("localperdeu").value == ""){
                    document.getElementById("localperdeu").focus();
                    $('#mensagemReinv').fadeIn("slow");
                    document.getElementById("mensagemReinv").innerHTML = "Descreva o local em que o objeto pode ter sido perdido.";
                    $('#mensagemReinv').fadeOut(2000); 
                    return false;
                }
                if(document.getElementById("nomereclamante").value == ""){
                    document.getElementById("nomereclamante").focus();
                    $('#mensagemReinv').fadeIn("slow");
                    document.getElementById("mensagemReinv").innerHTML = "Insira o nome do reclamante.";
                    $('#mensagemReinv').fadeOut(2000); 
                    return false;
                }
                if(document.getElementById("emailreclamante").value == ""){
                    document.getElementById("emailreclamante").focus();
                    $('#mensagemReinv').fadeIn("slow");
                    document.getElementById("mensagemReinv").innerHTML = "Insira o endereço de E-Mail do reclamante.";
                    $('#mensagemReinv').fadeOut(2000); 
                    return false;
                }
                if(document.getElementById("telefreclamante").value == ""){
                    document.getElementById("telefreclamante").focus();
                    $('#mensagemReinv').fadeIn("slow");
                    document.getElementById("mensagemReinv").innerHTML = "Insira o número do telefone do reclamante.";
                    $('#mensagemReinv').fadeOut(2000); 
                    return false;
                }
                Encontr = 0;
                if(document.getElementById("bemEncontrado").checked == true){
                    Encontr = 1;
                }
                Entreg = 0;
                if(document.getElementById("bemEntregue").checked == true){
                    Entreg = 1;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=salvaReivind&codigo="+document.getElementById("guardaid").value
                    +"&numregistro="+document.getElementById("numregistroReiv").value
                    +"&dataReivind="+document.getElementById("dataReivind").value
                    +"&dataPerdido="+document.getElementById("dataPerdido").value
                    +"&nomereclamante="+encodeURIComponent(document.getElementById("nomereclamante").value)
                    +"&emailreclamante="+encodeURIComponent(document.getElementById("emailreclamante").value)
                    +"&telefreclamante="+encodeURIComponent(document.getElementById("telefreclamante").value)
                    +"&localperdeu="+encodeURIComponent(document.getElementById("localperdeu").value)
                    +"&descdobemPerdeu="+encodeURIComponent(document.getElementById("descdobemPerdeu").value)
                    +"&obsperdeu="+encodeURIComponent(document.getElementById("obsperdeu").value)
                    +"&encontrado="+Encontr
                    +"&entregue="+Entreg
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("mudou").value = 0;
                                    document.getElementById("guardaid").value = Resp.codigo; // para o botão salvar sem fechar
                                    if(parseInt(Op) === 1){ //Salvar e Fechar
                                        document.getElementById("relacEditaReivindic").style.display = "none";
                                    }
                                    $('#mensagemReinv').fadeIn("slow");
                                    document.getElementById("mensagemReinv").innerHTML = "Documento Salvo.";
                                    $('#mensagemReinv').fadeOut(2000); 
                                    $("#faixaReivind").load("modulos/bensEncont/jReivind.php");
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function apagarReivindic(){
                $.confirm({
                    title: 'Apagar',
                    content: 'Confirma apagar este lançamento?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=apagaReivind&codigo="+document.getElementById("guardaid").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                document.getElementById("relacEditaReivindic").style.display = "none";
                                                $("#faixaReivind").load("modulos/bensEncont/jReivind.php");
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

            function ImpReiv(Valor){
                window.open("modulos/bensEncont/imprListaReiv.php?acao=listaReivindic&selec="+Valor, "ListaReiv");
            }
            function fechaEditReivind(){
                document.getElementById("relacEditaReivindic").style.display = "none";
            }
            function fechaModalImpr(){
                document.getElementById("relacImprReiv").style.display = "none";
            }
            function fechaBensConfig(){
                document.getElementById("modalBensConfig").style.display = "none";
            }
            function fechaModalTransf(){
                document.getElementById("relacmodalTransfGuarda").style.display = "none";
            }
            function fechaModalRestit(){
                document.getElementById("relacmodalRestit").style.display = "none";
            }
            function fechaModalDest(){
                document.getElementById("relacmodalDest").style.display = "none";
            }
            function fechaModalReceb(){
                document.getElementById("relacmodalReceb").style.display = "none";
            }
            function fechaModalArquivar(){
                document.getElementById("relacmodalArquivar").style.display = "none";
            }
            function foco(id){
                document.getElementById(id).focus();
            }
            function fechaModalEncam(){
                document.getElementById("relacmodalEncam").style.display = "none";
            }
            function carregaHelpBens(){
                document.getElementById("relacHelpBens").style.display = "block";
            }
            function fechaModalHelp(){
                document.getElementById("relacHelpBens").style.display = "none";
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

            function validaCPF(cpf) {
                var Soma = 0
                var Resto
                var strCPF = String(cpf).replace(/[^\d]/g, '')
                if (strCPF.length !== 11)
                    return false
                if ([
                    '00000000000',
                    '11111111111',
                    '22222222222',
                    '33333333333',
                    '44444444444',
                    '55555555555',
                    '66666666666',
                    '77777777777',
                    '88888888888',
                    '99999999999',
                ].indexOf(strCPF) !== -1)
                return false
                for (i=1; i<=9; i++)
                    Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
                    Resto = (Soma * 10) % 11
                    if ((Resto == 10) || (Resto == 11)) 
                        Resto = 0
                    if (Resto != parseInt(strCPF.substring(9, 10)) )
                    return false
                    Soma = 0
                    for (i = 1; i <= 10; i++)
                        Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i)
                        Resto = (Soma * 10) % 11
                        if ((Resto == 10) || (Resto == 11)) 
                            Resto = 0
                        if (Resto != parseInt(strCPF.substring(10, 11) ) )
                            return false
                return true
            }

            //Para validar endereço de e-mail
            function IsEmail(text){
                document.getElementById("mudou").value = "1";// modificou
                if(text !== ""){
                    var atpos = text.indexOf("@");
                    var dotpos = text.lastIndexOf(".");
                    if(atpos < 1 || dotpos < atpos+2 || dotpos+2 >= x.length){
                        $('#mensagemReinv').fadeIn("slow");
                        document.getElementById("mensagemReinv").innerHTML = "Não parece ser um endereço de e-mail válido.";
                        $('#mensagemReinv').fadeOut(2000); 
                        return false;
                    }
                    if(text.search("www") >= 0){
                        $('#mensagemReinv').fadeIn("slow");
                        document.getElementById("mensagemReinv").innerHTML = "Não parece ser um endereço de e-mail válido.";
                        $('#mensagemReinv').fadeOut(2000); 
                        return false;
                    }
                    return true;
                }
            }

            //mostra a ação quando clicar na mensagem da página inicial
            function mostraBens(Valor){
                $("#carregaBens").load("modulos/bensEncont/relBens.php?acao="+Valor);
            }
        </script>
    </head>
    <body class="corClara" onbeforeunload="return mudaTema(0)"> <!-- ao sair retorna os background claros -->
        <?php
            if(!$Conec){
                echo "Sem contato com o Servidor";
                return false;
            }
            $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'poslog'");
            $rowSis = pg_num_rows($rsSis);
            if($rowSis == 0){
                echo "Sem contato com os arquivos do sistema. Informe à ATI.";
                return false;
            }
            $rs = pg_query($Conec, "SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE table_name = 'bensachados'");
            $row = pg_num_rows($rs);
            if($row == 0){
                $Erro = 1;
                echo "Faltam tabelas. Informe à ATI.";
                return false;
            }
            function validaEmail($email){
            if (filter_var($email, FILTER_VALIDATE_EMAIL)){
                echo("$email is a valid email address");
              } else {
                echo("$email is not a valid email address");
              }
            }
//-------------- Provisório
//            pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".bensreivind");
            $rs0 = pg_query($Conec, "SELECT table_name FROM INFORMATION_SCHEMA.tables WHERE table_schema = 'cesb' And table_name = 'bensreivind'");
            $row0 = pg_num_rows($rs0);
            if($row0 == 0){
                pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".bensreivind (
                    id SERIAL PRIMARY KEY, 
                    datareiv date DEFAULT '3000-12-31',
                    dataperdeu date DEFAULT '3000-12-31',
                    processoreiv character varying(50), 
                    nome character varying(100), 
                    email character varying(50), 
                    telef character varying(50),
                    localperdeu text,
                    descdobemperdeu text, 
                    observ text, 
                    encontrado smallint DEFAULT 0 NOT NULL, 
                    entregue smallint DEFAULT 0 NOT NULL, 
                    ativo smallint DEFAULT 1 NOT NULL, 
                    usuins bigint DEFAULT 0 NOT NULL,
                    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                    usuedit bigint DEFAULT 0 NOT NULL,
                    dataedit timestamp without time zone DEFAULT '3000-12-31' 
                    )
                ");
            }
            $rs1 = pg_query($Conec, "SELECT id FROM ".$xProj.".bensreivind LIMIT 1");
            $row1 = pg_num_rows($rs1);
            if($row1 == 0){
                pg_query($Conec, "INSERT INTO ".$xProj.".bensreivind (id, datareiv, dataperdeu, processoreiv, nome, email, telef, localperdeu, descdobemperdeu, ativo, usuins, datains) 
                VALUES (1, '2025-04-18', '2025-04-18', '001/2025', 'Fulano de Tal', 'fulano@gmail.com', '(61) 9 9999-8888', 'Pátio principal', 'Carteira com todos os documentos', 1, 3, '2025-04-18')");
                pg_query($Conec, "INSERT INTO ".$xProj.".bensreivind (id, datareiv, dataperdeu, processoreiv, nome, email, telef, localperdeu, descdobemperdeu, ativo, usuins, datains) 
                VALUES (2, '2025-04-19', '2025-04-19', '002/2025', 'Sicrano Fulanildo da Silva Sauro', 'sicrano@gmail.com', '(61) 9 9999-7777', 'Pátio principal', 'Relógio despertador para uso no pulso ou no pé com campainha de quartzo brilhante revestido com pó de lacraia nova.', 1, 3, '2025-04-19')");
            }


//--------------

            if(isset($_REQUEST["acao"])){
                $Acao = $_REQUEST["acao"];
            }else{
                $Acao = "Todos";
            }

        date_default_timezone_set('America/Sao_Paulo');
        $Hoje = date('d/m/Y');

        $admIns = parAdm("insbens", $Conec, $xProj);   // nível para inserir 
        $admEdit = parAdm("editbens", $Conec, $xProj); // nível para editar -> foi para relBens.php
        $Bens = parEsc("bens", $Conec, $xProj, $_SESSION["usuarioID"]); // está marcado no cadastro de usuários
        $FiscBens = parEsc("fiscbens", $Conec, $xProj, $_SESSION["usuarioID"]);
        $SoInsBens = parEsc("soinsbens", $Conec, $xProj, $_SESSION["usuarioID"]); // está marcado no cadastro de usuários
        $EncBens = parEsc("encbens", $Conec, $xProj, $_SESSION["usuarioID"]);
        $Tema = parEsc("tema", $Conec, $xProj, $_SESSION["usuarioID"]); // Claro(0) Escuro(1)

        //bens, fiscbens, soinsbens, encbens

        $OpDestBens = pg_query($Conec, "SELECT numdest, descdest FROM ".$xProj.".bensdestinos ORDER BY descdest");
        $OpProcesso = pg_query($Conec, "SELECT id, processo FROM ".$xProj.".bensprocessos ORDER BY processo");
        $OpConfig = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
        $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(datareceb, 'MM'), '/', TO_CHAR(datareceb, 'YYYY')) 
        FROM ".$xProj.".bensachados GROUP BY TO_CHAR(datareceb, 'MM'), TO_CHAR(datareceb, 'YYYY') ORDER BY TO_CHAR(datareceb, 'YYYY') DESC, TO_CHAR(datareceb, 'MM') DESC ");
        $OpcoesEscAno = pg_query($Conec, "SELECT EXTRACT(YEAR FROM ".$xProj.".bensachados.datareceb)::text 
        FROM ".$xProj.".bensachados GROUP BY 1 ORDER BY 1 DESC ");

        ?>
        <!-- div três colunas -->
        <div id="tricoluna0" style="margin: 0 auto; text-align: center;">
            <div id="tricoluna1" style="position: relative; float: left; text-align: center; width: 25%; padding-left: 10px;">
                <div class="col quadro" style="text-align: left;">
                    <img src="imagens/settings.png" height="20px;" id="imgBensconfig" style="cursor: pointer; padding-right: 10px;" onclick="abreBensConfig();" title="Configurar o acesso ao processamento de Achados e Perdidos">
                    <button id="botInsReg" class="botpadramarelo" onclick="abreRegistro();" title="Registrar um objeto encontrado.">Novo Registro</button>
                    <div style="position: relative; float: right;">
                        <button id="botabreReiv" style="font-size: 70%; padding-left: 3px; padding-right: 3px;" class="botpadrblue" onclick="abreReiv();" title="Registrar uma reivindicação de objeto perdido na Casa.">Reivindicação</button>
                    </div>
                </div>
            </div>
            <div id="tricoluna2" style="position: relative; float: left; text-align: center; width: 48%;">
                <h5>Registro de Achados e Perdidos</h5>
                <div id="selectBens" style="text-align: center;">
                <button class="resetbot" style="font-size: .9rem;" onclick="mostraBens('Todos');">Todos</button>
                <button class="resetbot" style="font-size: .9rem;" onclick="mostraBens('Restituídos');" title="Bem já restituído">Restituídos</button>
                <button class="resetbot" style="font-size: .9rem;" onclick="mostraBens('Destinar');" title="Pronto para dar destino. Prazo de 90 dias transcorrido. Nível Revisor.">Destinar</button>
                <button class="resetbot" style="font-size: .9rem;" onclick="mostraBens('Receber');" title="Bem já encaminhado. Receber no destino.">Receber</button>
                <button class="resetbot" style="font-size: .9rem;" onclick="mostraBens('Arquivar');" title="Processos que aguardam encerramento. Nível Revisor." >Arquivar</button>
                <button class="resetbot" style="font-size: .9rem;" onclick="mostraBens('Arquivados');" title="Processos encerrados." >Arquivados</button>
                </div>
            </div>
            <div id="tricoluna3" style="position: relative; float: left; text-align: center; width: 25%;">
                <div id="selectTema" style="position: relative; float: left;">
                    <label id="etiqcorFundo" class="etiq" style="color: #6C7AB3; font-size: 80%; padding-left: 5px;">Tema: </label>
                    <input type="radio" name="corFundo" id="corFundo0" value="0" <?php if($Tema == 0){echo 'CHECKED';}; ?> title="Tema claro" onclick="mudaTema(0);" style="cursor: pointer;"><label for="corFundo0" class="etiq" style="cursor: pointer;">&nbsp;Claro</label>
                    <input type="radio" name="corFundo" id="corFundo1" value="1" <?php if($Tema == 1){echo 'CHECKED';}; ?> title="Tema escuro" onclick="mudaTema(1);" style="cursor: pointer;"><label for="corFundo1" class="etiq" style="cursor: pointer;">&nbsp;Escuro</label>
                    <label style="padding-left: 20px;"></label>
                </div>
                <button id="botimpr" class="botpadrred" style="font-size: 80%;" onclick="abreImprBens();">PDF</button>
                <label style="padding-left: 20px;"></label>
                <img id="imgHelpBens" src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpBens();" title="Guia rápido">
            </div>
        </div>

        <input type="hidden" id="guardacod" value="0" /> <!-- id ocorrência -->
        <input type="hidden" id="mudou" value="0" />
        <input type="hidden" id="guardahoje" value="<?php echo $Hoje; ?>" />
        <input type="hidden" id="guardaNumRelat" value="0" />
        <input type="hidden" id="usuarioID" value="<?php echo $_SESSION["usuarioID"]; ?>" />
        <input type="hidden" id="usuarioNome" value="<?php echo $_SESSION["NomeCompl"]; ?>" />
        <input type="hidden" id="codusuins" value="0" />
        <input type="hidden" id="codusudest" value = "0" />
        <input type="hidden" id="codusureceb" value = "0" />
        <input type="hidden" id="guardamodal" value = "0" />
        <input type="hidden" id="guardaEditBens" value="<?php echo $Bens; ?>" />
        <input type="hidden" id="guardaFiscBens" value="<?php echo $FiscBens; ?>" />
        <input type="hidden" id="guardaEncBens" value="<?php echo $EncBens; ?>" />
        <input type="hidden" id="guardaSoInsBens" value="<?php echo $SoInsBens; ?>" />
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>" />
        <input type="hidden" id="admIns" value="<?php echo $admIns; ?>" /> <!-- nível mínimo para inserir  -->
        <input type="hidden" id="admEdit" value="<?php echo $admEdit; ?>" /> <!-- nível mínimo para editar -->
        <input type="hidden" id="guardaIndex" value="<?php echo $Acao; ?>" /> <!-- ordem do índex -->
        <input type="hidden" id="guardaid" value = "" />

        <div style="margin: 80px; border: 2px solid blue; border-radius: 15px; padding: 10px; padding-top: 2px;">
            <div id="carregaBens"></div>
        </div>

        <div id="carregaTema"></div> <!-- carrega a pág modulos/config/carTema.php - onde está a função mudaTema() -->

        <!-- div modal para registrar ocorrência do bem encontrado  -->
        <div id="relacmodalRegistro" class="relacmodal">
            <div class="modal-content-Bens">
                <span class="close" onclick="fechaModalReg();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"><button class="botpadrred" id="botimprReg" style="font-size: 80%;" onclick="imprReg();">Gerar PDF</button></div>
                        <div class="col quadro"><h6 id="titulomodal" style="color: #666;">Registro de Recebimento de Achados e Perdidos</h6></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><!-- <button class="botpadrred" onclick="enviaModalReg(1);">Enviar</button> --> </div> 
                    </div>
                </div>
                <div style="border: 2px solid blue; border-radius: 10px; padding: 10px;">
                    <table style="margin: 0 auto; width:85%;">
                        <tr>
                            <td class="etiqAzul" style="min-width: 150px;">Data do recebimento: </td>
                            <td>
                                <input type="text" id="dataregistro" width="150" onmousedown="tiraBorda(id);" onkeydown="tiraBorda(id);" value="<?php echo $Hoje; ?>" onchange="modif();" placeholder="Data" style="font-size: .9em; text-align: center; border: 1px solid; border-radius: 3px;">
<!--                                <label id="numprocesso" class="etiqAzul" style="padding-left: 30px; color: red;"></label>  -->
                            </td>
                            <td class="etiqAzul">Número do Processo: </td>
                            <td><input disabled type="text" id="numregistro" onmousedown="tiraBorda(id);" onkeydown="tiraBorda(id);" value="<?php echo $Hoje; ?>" onchange="checaNumRegistro();" placeholder="Data" style="font-size: .9em; text-align: center; border: 1px solid; border-radius: 3px;"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Descrição do objeto encontrado: </td>
                            <td colspan="3">
                                <textarea style="border: 1px solid blue; border-radius: 10px; padding: 3px;" rows="3" cols="60" id="descdobem" onchange="modif();"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Data em que foi encontrado: </td>
                            <td><input type="text" id="dataachado" width="150" onmousedown="tiraBorda(id);" onkeydown="tiraBorda(id);" value="<?php echo $Hoje; ?>" onchange="modif();" placeholder="Data" style="font-size: .9em; text-align: center; border: 1px solid; border-radius: 3px;"></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Local em que foi encontrado: </td>
                            <td colspan="3"><textarea style="border: 1px solid blue; border-radius: 10px; padding: 3px;" rows="2" cols="60" id="localachado" onchange="modif();"></textarea></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Nome de quem encontrou: </td>
                            <td colspan="3"><input type="text" id="nomeachou" onmousedown="tiraBorda(id);" onkeydown="tiraBorda(id);" value="" onchange="modif();" placeholder="Nome do colaborador que encontrou" style="font-size: .9em; width: 90%;"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Telefone: </td>
                            <td colspan="3"><input type="text" id="telefachou" onmousedown="tiraBorda(id);" onkeydown="tiraBorda(id);" value="" onchange="modif();" placeholder="Telefone do colaborador que encontrou" style="font-size: .9em; width: 90%;"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul"></td>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <td>
                                <button class="botpadrTijolo" id="botApagaBem" onclick="ApagarBem();">Apagar</button>
                            </td>
                            <td colspan="3"></td>
                        </tr>
                    </table>

                    <div id="mensagem" style="color: red; font-weight: bold; margin: 5px; text-align: center; padding-top: 10px;"></div>
                    
                    <div style="text-align: center; padding-bottom: 20px;">
                        <button class="botpadrblue" id="botsalvareg" onclick="salvaModalRegistro();">Registrar</button>
                    </div>
                </div>
           </div>
        </div> <!-- Fim Modal-->


        <!-- div modal para transferir a guarda do objeto  -->
        <div id="relacmodalTransfGuarda" class="relacmodal">
            <div class="modal-content-Bens">
                <span class="close" onclick="fechaModalTransf();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"></div>
                        <div class="col quadro"><h5 id="titulomodal" style="color: #666;">Registro de Transferência para Guarda</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><!-- <button class="botpadrred" onclick="enviaModalReg(1);">Enviar</button> --> </div> 
                    </div>
                </div>
                <div style="border: 2px solid blue; border-radius: 10px; padding: 10px;">
                    <table style="margin: 0 auto; width:85%;">
                        <tr>
                            <td class="etiqAzul">Processo: </td>
                            <td>
                                <label id="numprocessotransf" class="etiqAzul" style="padding-left: 5px; font-size: 1.1rem;"></label>
                                <label id="etiqprocessoReg"class="etiqAzul"></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Descrição: </td>
                            <td>
                                <div id="descdobemtransf" style="border: 1px solid blue; border-radius: 10px; padding: 3px;"></div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Termo: </td>
                            <td><div style="border: 1px solid blue; border-radius: 10px; padding: 3px;">Declaro que recebi o Bem acima descrito, ao qual efetuarei a guarda pelo período de 90 (noventa) dias. Após esse prazo, a destinação do bem seguirá o caminho estabelecido na NI-4.05-B (DAF).</div></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Depositário: </td>
                            <td style="padding-left: 15px; padding-right: 15px;"><div style="border: 1px solid blue; border-radius: 10px; text-align: center;">(a) <label style="font-weight: bold;"> <?php echo $_SESSION["NomeCompl"]; ?> </label></div></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align: center; padding-top: 25px;"><button class="botpadrblue" id="botsalvaregtransf" onclick="salvaModalTransf();">Objeto Recebido</button></td>
                        </tr>
                    </table>

                    <div id="mensagem" style="color: red; font-weight: bold; margin: 5px; text-align: center; padding-top: 10px;"></div>
                    <br>
                </div>
           </div>
        </div> <!-- Fim Modal-->


        <!-- div modal para restituir o objeto  -->
        <div id="relacmodalRestit" class="relacmodal">
            <div class="modal-content-Bens">
                <span class="close" onclick="fechaModalRestit();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"><button class="botpadrred" onclick="imprRestit();">Recibo PDF</button>  </div>
                        <div class="col quadro"><h5 id="titulomodal" style="color: #666;">Registro de Restituição</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><!-- <button class="botpadrred" onclick="enviaModalReg(1);">Enviar</button> --> </div> 
                    </div>
                </div>
                <div style="color: black; border: 2px solid blue; border-radius: 10px; padding: 10px;">
                    <table style="margin: 0 auto; width:85%;">
                        <tr>
                            <td class="etiqAzul">Processo: </td>
                            <td>
                                <label id="numprocessoRest" class="etiqAzul" style="padding-left: 5px; font-size: 1.1rem;"></label>
                                <label id="etiqprocessoRest"class="etiqAzul"></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Descrição: </td>
                            <td>
                                <div id="descdobemRest" style="border: 1px solid blue; border-radius: 10px; padding: 3px;"></div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Proprietário: </td>
                            <td><input type="text" id="nomeproprietario" onmousedown="tiraBorda(id);" onkeydown="tiraBorda(id);" value="" onchange="modif();" placeholder="Nome do proprietário" style="font-size: .9em; width: 90%;" onkeypress="if(event.keyCode===13){javascript:foco('cpfproprietario');return false;}"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">CPF: </td>
                            <td><input type="text" id="cpfproprietario" onmousedown="tiraBorda(id);" onkeydown="tiraBorda(id);" value="" onchange="modif();" placeholder="CPF do proprietário" style="font-size: .9em; width: 90%;" onkeypress="if(event.keyCode===13){javascript:foco('telefproprietario');return false;}"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Telefone: </td>
                            <td><input type="text" id="telefproprietario" onmousedown="tiraBorda(id);" onkeydown="tiraBorda(id);" value="" onchange="modif();" placeholder="Telefone do proprietário" style="font-size: .9em; width: 90%;" onkeypress="if(event.keyCode===13){javascript:foco('nomeproprietario');return false;}"></td>
                        </tr>

                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Assinatura: </td>

                            <td style="padding-left: 15px; padding-right: 15px;"><div style="border: 1px solid blue; border-radius: 10px; text-align: center;">(a) <label id="etiqnomeusurestit" style="font-weight: bold;"> <?php echo $_SESSION["NomeCompl"]; ?> </label></div></td>
                        </tr>
                        <tr>
                            <td>
<!--                                <button class="botpadrTijolo" id="botApagaBem" onclick="ApagarBem();">Apagar</button> -->
                            </td>
                            <td style="text-align: center; padding-top: 25px;"><button class="botpadrblue" id="botsalvaRestit" onclick="modalRestit();">Objeto Restituido</button></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align: center; padding-top: 25px;"><div id="mensagemrest" style="color: red; font-weight: bold; margin: 5px; text-align: center; padding-top: 10px;"></div></td>
                        </tr>
                    </table>
                    <br>
                </div>
           </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para encaminhar o objeto para SSV (Setor de Serviços) -->
        <div id="relacmodalEncam" class="relacmodal">
            <div class="modal-content-Bens">
                <span class="close" onclick="fechaModalEncam();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"></div>
                        <div class="col quadro"><h5 id="titulomodal" style="color: #666;">Registro de Encaminhamento para SSV</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><!-- <button class="botpadrred" onclick="enviaModalReg(1);">Enviar</button> --> </div> 
                    </div>
                </div>
                <div style="color: black; border: 2px solid blue; border-radius: 10px; padding: 10px;">
                    <table style="margin: 0 auto; width:85%;">
                        <tr>
                            <td class="etiqAzul">Processo: </td>
                            <td>
                                <label id="numprocessoEncam" class="etiqAzul" style="padding-left: 5px; font-size: 1.1rem;"></label>
                                <label id="etiqprocessoEncam"class="etiqAzul"></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Descrição: </td>
                            <td>
                                <div id="descdobemEncam" style="border: 1px solid blue; border-radius: 10px; padding: 3px;"></div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Termo: </td>
                            <td><div style="border: 1px solid blue; border-radius: 10px; padding: 3px;">Declaro que recebi neste SSV, o processo acima identificado para armazenamento, destinação e arquivamento do processo.</div></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>

                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Funcionário do SSV: </td>
                            <td style="padding-left: 15px; padding-right: 15px;"><div style="border: 1px solid blue; border-radius: 10px; text-align: center;">(a) <label style="font-weight: bold;"> <?php echo $_SESSION["NomeCompl"]; ?> </label></div></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align: center; padding-top: 25px;"><button class="botpadrblue" id="botsalvaregenc" onclick="modalRcbCSG();">Objeto Recebido</button></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align: center; padding-top: 25px;"><div id="mensagemrest" style="color: red; font-weight: bold; margin: 5px; text-align: center; padding-top: 10px;"></div></td>
                        </tr>
                    </table>
                    <br>
                </div>
           </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para destinação do objeto  -->
        <div id="relacmodalDest" class="relacmodal">
            <div class="modal-content-Bens">
                <span class="close" onclick="fechaModalDest();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"></div>
                        <div class="col quadro"><h5 id="titulomodal" style="color: #666;">Registro de Destinação</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><!-- <button class="botpadrred" onclick="enviaModalReg(1);">Enviar</button> --> </div> 
                    </div>
                </div>
                <div style="color: black; border: 2px solid blue; border-radius: 10px; padding: 10px;">
                    <table style="margin: 0 auto; width:85%;">
                        <tr>
                            <td class="etiqAzul">Processo: </td>
                            <td>
                                <label id="numprocessoDest" class="etiqAzul" style="padding-left: 5px; font-size: 1.1rem;"></label>
                                <label id="etiqprocessoDest" class="etiqAzul"></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Descrição: </td>
                            <td>
                                <div id="descdobemDest" style="border: 1px solid blue; border-radius: 10px; padding: 3px;"></div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                        </tr>

                        <tr>
                            <td colspan="2"><div style="border: 1px solid blue; border-radius: 10px; padding: 3px;">Após o prazo estabelecido e não tendo sido procurado, o bem acima identificado é agora encaminhado de acordo com o estabelecido na NI-4.05-B (DAF).</div></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 5px;"></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center;"><label class="etiqAzul">Destino: </label>
                                <select id="selecdestino" onclick="tiraBorda(id);" onchange="modif();" style="font-size: 0.9rem; min-width: 100px;" title="Selecione o destino dado ao bem encontrado.">
                                <?php 
                                if($OpDestBens){
                                    while ($Opcoes = pg_fetch_row($OpDestBens)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Assinatura: </td>
                            <td style="padding-left: 15px; padding-right: 15px;"><div style="border: 1px solid blue; border-radius: 10px; text-align: center;">(a) <label id="etiqAssinaturaDest" style="font-weight: bold;"> <?php echo $_SESSION["NomeCompl"]; ?> </label></div></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align: center; padding-top: 25px;"><button class="botpadrblue" id="botsalvaregdest" onclick="modalDestino();">Encaminhar</button></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align: center; padding-top: 25px;"><div id="mensagemdest" style="color: red; font-weight: bold; margin: 5px; text-align: center; padding-top: 10px;"></div></td>
                        </tr>
                    </table>
                    <br>
                </div>
           </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para receber objeto no destino  -->
       <div id="relacmodalReceb" class="relacmodal">
            <div class="modal-content-Bens">
                <span class="close" onclick="fechaModalReceb();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"></div>
                        <div class="col quadro"><h5 id="titulomodal" style="color: #666;">Registro de Recebimento de Bens destinados</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><!-- <button class="botpadrred" onclick="enviaModalReg(1);">Enviar</button> --> </div> 
                    </div>
                </div>
                <div style="color: black; border: 2px solid blue; border-radius: 10px; padding: 10px;">
                    <table style="margin: 0 auto; width:85%;">
                        <tr>
                            <td class="etiqAzul">Processo: </td>
                            <td>
                                <label id="numprocessoReceb" class="etiqAzul" style="padding-left: 5px; font-size: 1.1rem;"></label>
                                <label id="etiqprocessoReceb"class="etiqAzul"></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Descrição: </td>
                            <td>
                                <div id="descdobemReceb" style="border: 1px solid blue; border-radius: 10px; padding: 3px;"></div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 1px;"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Destino: </td>
                            <td>
                                <div style="border: 1px solid blue; border-radius: 10px; padding: 3px; padding-left: 10px; font-size: 110%;">
                                    <label id="descdestino" style="font-weight: bold;"></label>&nbsp;&nbsp; &rarr; &nbsp;
                                    <label id="descprocesso"></label>
                                    

                                    <label id="etiqfinalidade" class="etiqAzul"> finalidade: </label>
                                    <select id="selecprocesso" onclick="tiraBorda(id);" onchange="modif();" style="font-size: 0.9rem; min-width: 100px;" title="Selecione o destino dado ao bem encontrado.">
                                    <?php 
                                    if($OpProcesso){
                                        while ($Opcoes = pg_fetch_row($OpProcesso)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                    </select>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="etiqAzul">Termo: </td>
                            <td><div style="border: 1px solid blue; border-radius: 10px; padding: 3px;">Declaro que recebi o Bem acima descrito, para dispor conforme os interesses da administração, de acordo com o estabelecido na NI-4.05-B (DAF).</div></td>
                        </tr>

                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 1px;"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Assinatura: </td>
                            <td style="padding-left: 15px; padding-right: 15px;"><div style="border: 1px solid blue; border-radius: 10px; text-align: center;">(a) <label id="etiqAssinatura" style="font-weight: bold;"> <?php echo $_SESSION["NomeCompl"]; ?> </label></div></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align: center; padding-top: 25px;"><button class="botpadrblue" id="botsalvaregrec" onclick="modalRecebe();">Receber</button></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><div id="mensagemRec" style="text-align: center; color: red; font-weight: bold; padding-top: 5px;"></div></td>
                        </tr>
                    </table>
                    <br>
                </div>
           </div>
        </div> <!-- Fim Modal-->


        <!-- div modal para arquivar processo  -->
       <div id="relacmodalArquivar" class="relacmodal">
            <div class="modal-content-Bens">
                <span class="close" onclick="fechaModalArquivar();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"></div>
                        <div class="col quadro"><h5 id="titulomodal" style="color: #666;">Registro de Encerramento do Processo</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><!-- <button class="botpadrred" onclick="enviaModalReg(1);">Enviar</button> --> </div> 
                    </div>
                </div>
                <div style="color: black; border: 2px solid blue; border-radius: 10px; padding: 10px;">
                    <table style="margin: 0 auto; width:85%;">
                        <tr>
                            <td class="etiqAzul">Processo: </td>
                            <td>
                                <label id="numprocessoArq" class="etiqAzul" style="padding-left: 5px; font-size: 1.1rem;"></label>
                                <label id="etiqprocessoArq"class="etiqAzul"></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Descrição: </td>
                            <td>
                                <div id="descdobemArq" style="border: 1px solid blue; border-radius: 10px; padding: 3px;"></div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 1px;"></td>
                        </tr>
                        
                        <tr>
                            <td class="etiqAzul">Destinado: </td>
                            <td>
                                <div style="border: 1px solid blue; border-radius: 10px; padding: 3px; padding-left: 10px; font-weight: bold;">
                                    <label id="descdestinoArq"></label>&nbsp;&nbsp; &rarr; &nbsp;&nbsp;
                                    <label id="descprocessoArq"></label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: center; padding-bottom: 1px; padding-top: 10px;">Este processo será encerrado e arquivado nesta data: <?php echo $Hoje; ?>.</td>
                        </tr>
                        <tr>
                            <td colspan="2"><hr></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 1px;"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Assinatura: </td>
                            <td style="padding-left: 15px; padding-right: 15px;"><div style="border: 1px solid blue; border-radius: 10px; text-align: center;">(a) <label style="font-weight: bold;"> <?php echo $_SESSION["NomeCompl"]; ?> </label></div></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align: center; padding-top: 25px;"><button class="botpadrblue" id="botsalvaregarq" onclick="modalArquiva();">Arquivar</button></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align: center; padding-top: 25px;"><div id="mensagemArqv" style="color: red; font-weight: bold; margin: 5px; text-align: center; padding-top: 10px;"></div></td>
                        </tr>
                    </table>
                    <br>
                </div>
           </div>
        </div> <!-- Fim Modal-->


         <!-- Modal configuração-->
         <div id="modalBensConfig" class="relacmodal">
            <div class="modal-content-BensControle corPreta">
                <span class="close" onclick="fechaBensConfig();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"></div>
                        <div class="col quadro"><h5 id="titulomodal" style="text-align: center; color: #666;">Configuração <br>Achados e Perdidos</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><button class="botpadrred" style="font-size: 70%;" onclick="resumoUsuBens();">Resumo em PDF</button></div> 
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
                            <select id="configSelecBens" style="max-width: 230px;" onchange="modif();" title="Selecione um usuário.">
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
                            <input type="text" id="configCpfBens" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('configSelecBens');return false;}" title="Procura por CPF. Digite o CPF."/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                    </tr>
                </table>

                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td class="etiq" title="Registrar recebimento e guardar os Achados e Perdidos">DAF:</td>
                        <td colspan="4">
                            <input type="checkbox" id="preencheBens" title="Registrar e prover a guarda dos Achados e Perdidos." onchange="marcaBem(this, 'bens');" >
                            <label for="preencheBens" title="Registrar e prover a guarda dos Achados e Perdidos.">registrar e prover a guarda dos Achados e Perdidos.</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiq" title="Encaminhar bens após o prazo estabelecido">DAF:</td>
                        <td colspan="4">
                            <input type="checkbox" id="encaminhaBens" title="Dar destino aos Achados e Perdidos após o prazo estabelecido e Arquivar o processo." onchange="marcaBem(this, 'encbens');" >
                            <label for="encaminhaBens" title="Dar destino e arquivar.">dar destino após prazo estabelecido e arquivar o processo.</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiq" title="Apenas registrar o recebimento de Achados e Perdidos. Apropriado para os funcionários da Portaria.">Portaria:</td>
                        <td colspan="4">
                            <input type="checkbox" id="soPreencheBens" title="Apenas registrar recebimento de Achados e Perdidos. Apropriado para os funcionários da Portaria." onchange="marcaBem(this, 'soinsbens');" >
                            <label for="soPreencheBens" title="Apenas registrar recebimento de Achados e Perdidos. Apropriado para os funcionários da Portaria.">apenas registrar Achados e Perdidos</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiq" style="border-bottom: 1px solid;" title="Fiscalizar os registros de Achados e Perdidos - Só fiscaliza. Não pode registrar os Achados e Perdidos.">Administração:</td>
                        <td colspan="4" style="border-bottom: 1px solid;">
                            <input type="checkbox" id="fiscBens" title="Fiscalizar os registros de Achados e Perdidos - Só fiscaliza. Não pode registrar os Achados e Perdidos." onchange="marcaBem(this, 'fiscbens');" >
                            <label for="fiscBens" title="Fiscalizar os registros de Achados e Perdidos - Só fiscaliza. Não pode registrar os Achados e Perdidos.">fiscalizar os registros de Achados e Perdidos</label>
                        </td>
                    </tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para imprimir em pdf  -->
        <div id="relacimprBens" class="relacmodal">
            <div class="modal-content-imprBens">
                <span class="close" onclick="fechaImprBens();">&times;</span>
                <h5 id="titulomodal" style="text-align: center;color: #666;">Controle de Achados e Perdidos</h5>
                <h6 id="titulomodal" style="text-align: center; padding-bottom: 18px; color: #666;">Impressão PDF</h6>
                <div style="border: 2px solid #C6E2FF; border-radius: 10px;">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td style="text-align: right;"><label style="color: black; font-size: 80%;">Mensal - Selecione o Mês/Ano: </label></td>
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
                            <td style="text-align: right;"><label style="color: black; font-size: 80%;">Anual - Selecione o Ano: </label></td>
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
                        <tr>
                            <td colspan="2" style="padding: 10px; text-align: center;">
                                <button class="resetbotazul" style="font-size: 80%;" id="resumoBens" title="Demonstrativo anual dos Achados e Perdidos">Resumo Anual</button>
                            </td>
                        </tr>
                    </table>
                </div>
                <div style="padding-bottom: 20px;"></div>
           </div>
        </div>

        <!-- div modal para registrar reivindicação de bem perdido  -->
        <div id="relacmodalReivindic" class="relacmodal">
            <div class="modal-content-Reivindic">
                <span class="close" onclick="fechaModalReiv();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto; text-align: left">
                            <button id="insReinv" style="font-size: 70%; padding-left: 3px; padding-right: 3px;" class="botpadrblue" onclick="carregaInsReivind();" >Inserir Nova</button>
                        </div>
                        <div class="col quadro"><h5 style="color: #666;">Reclamação de Bens Perdidos</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: right; padding-right: 30px;">
                            <button id="imprReinv" class="botpadrred" style="font-size: 80%;">PDF</button>
                        </div> 
                    </div>
                </div>
                <div id="faixaReivind"></div>
            </div>
        </div>  <!-- Fim Modal Help-->

        <!-- div modal para editar reivindicação  -->
        <div id="relacEditaReivindic" class="relacmodal">
            <div class="modal-content-EditaReinvindic">
                <span class="close" onclick="fechaEditReivind();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"></div>
                        <div class="col quadro"><h5 id="tituloEditReiv" style="color: #666;">Registro de Reclamação</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: right; padding-right: 30px;"><button class="botpadrred" id="imprReciboReiv" style="font-size: 80%;">Recibo</button></div> 
                    </div>
                </div>
                <div style="border: 2px solid blue; border-radius: 10px; padding: 10px;">
                    <table style="margin: 0 auto; width:85%;">
                        <tr>
                            <td class="etiqAzul" style="min-width: 150px;">Data da reclamação: </td>
                            <td>
                                <input type="text" id="dataReivind" width="150" value="<?php echo $Hoje; ?>" onchange="modif();" placeholder="Data" style="font-size: .9em; text-align: center; border: 1px solid; border-radius: 3px;">
                            </td>
                            <td class="etiqAzul">Número do Processo: </td>
                            <td><input disabled type="text" id="numregistroReiv" value="<?php echo $Hoje; ?>" onchange="modif();" placeholder="Data" style="font-size: .9em; text-align: center; border: 1px solid; border-radius: 3px;"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Descrição do objeto perdido: </td>
                            <td colspan="3">
                                <textarea id="descdobemPerdeu" style="border: 1px solid blue; border-radius: 10px; padding: 3px;" rows="3" cols="60" onchange="modif();" placeholder="Descrição"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Data em que foi perdido: </td>
                            <td><input type="text" id="dataPerdido" width="150" value="<?php echo $Hoje; ?>" onchange="modif();" placeholder="Data" style="font-size: .9em; text-align: center; border: 1px solid; border-radius: 3px;"></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Local em que foi perdido: </td>
                            <td colspan="3"><textarea id="localperdeu" style="border: 1px solid blue; border-radius: 10px; padding: 3px;" rows="2" cols="60" onchange="modif();" placeholder="Local"></textarea></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Nome do reclamante: </td>
                            <td colspan="3"><input type="text" id="nomereclamante" style="border: 1px solid blue; border-radius: 5px; font-size: .9em; width: 90%;" value="" onchange="modif();" placeholder="Nome do reclamante"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">E-Mail: </td>
                            <td colspan="3"><input type="text" id="emailreclamante" style="border: 1px solid blue; border-radius: 5px; font-size: .9em; width: 90%;" onchange="IsEmail(value);" placeholder="E-Mail do reclamante"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Telefone: </td>
                            <td colspan="3"><input type="text" id="telefreclamante" style="border: 1px solid blue; border-radius: 5px; font-size: .9em; width: 90%;" onchange="modif();" placeholder="Telefone do reclamante"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Observações: </td>
                            <td colspan="3"><textarea id="obsperdeu" style="border: 1px solid blue; border-radius: 10px; padding: 3px;" rows="2" cols="60" onchange="modif();"></textarea></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul"></td>
                            <td colspan="3"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul"></td>
                            <td colspan="3">
                                <input type="checkbox" id="bemEncontrado" onclick="modif();">
                                <label for="bemEncontrado" style="color: black;">Objeto encontrado</label>
                                    <label style="padding-left: 30px;"></label>
                                <input type="checkbox" id="bemEntregue" onclick="modif();">
                                <label for="bemEntregue" style="color: black;">Objeto entregue</label>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button id="botApagaReiv" class="botpadrTijolo" style="font-size: 70%;" onclick="apagarReivindic();">Apagar</button>
                            </td>
                            <td colspan="3" style="text-align: right;">
                                <button id="salvaReivindc0" class="botpadrblue" onclick="salvaReivindic(0);">Salvar</button>
                                <label style="padding-left: 30px;"></label>
                                <button id="salvaReivindc1" class="botpadrblue" onclick="salvaReivindic(1);">Salvar e Fechar</button>
                        </td>
                        </tr>
                    </table>
                    <div id="mensagemReinv" style="color: red; font-weight: bold; margin: 5px; text-align: center; padding-top: 10px;"></div>
                    <div id="mensagemReinvOK" style="color: green; font-weight: bold; margin: 5px; text-align: center; padding-top: 10px;"></div>
                </div>
           </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para imprimir reivindicações  -->
        <div id="relacImprReiv" class="relacmodal">
            <div class="modal-content-ImprReiv corPreta">
                <span class="close" onclick="fechaModalImpr();">&times;</span>
                <h5 style="text-align: center;color: #666;">Reclamação de Bens</h5>
                <h6 style="text-align: center; padding-bottom: 8px; color: #666;">Impressão PDF</h6>
                <div>
                    <table style="margin: 0 auto;">
                        <tr>
                            <td><div style="margin: 5px; padding: 5px; border: 2px solid #C6E2FF; border-radius: 10px;"><button class="resetbot fundoAmareloCl" style="font-size: .9rem;" onclick="ImpReiv('todos');">Todos</button></div></td>
                            <td><div style="margin: 5px; padding: 5px; border: 2px solid #C6E2FF; border-radius: 10px;"><button class="resetbot fundoAmareloCl" style="font-size: .9rem;" onclick="ImpReiv('encontrado');">Encontrados</button></div></td>
                            <td><div style="margin: 5px; padding: 5px; border: 2px solid #C6E2FF; border-radius: 10px;"><button class="resetbot fundoAmareloCl" style="font-size: .9rem;" onclick="ImpReiv('entregue');">Entregues</button></div></td>
                        </tr>
                    </table>
                </div>
                <div style="padding-bottom: 10px;"></div>
           </div>
           <br><br>
        </div> <!-- Fim Modal-->


        <!-- div modal para leitura instruções -->
        <div id="relacHelpBens" class="relacmodal">
            <div class="modalMsg-content">
                <span class="close" onclick="fechaModalHelp();">&times;</span>
                <h4 style="text-align: center; color: #666;">Informações</h4>
                <h5 style="text-align: center; color: #666;">Achados e Perdidos</h5>
                <div style="color: black; border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px;">
                    Regras inseridas:
                    <ul>
                        <li>1 - Achados e Perdidos no recinto devem ser encaminhados para guarda da Diretoria Administrativa e Financeira (DAF).</li>
                        <li>2 - Apenas usuários selecionados podem ver a relação dos objetos encontrados.</li>
                        <li>3 - Alguns funcionários são autorizados a registrar e dar andamento aos processos.</li>
                        <li>4 - Alguns funcionários são autorizados a registrar apenas.</li>
                        <li>5 - Após noventa dias são abertos os recursos de encaminhamento para doação, descarte, destruição, venda, etc.</li>
                    </ul>
                </div>
            </div>
        </div>  <!-- Fim Modal Help-->

    </body>
</html>