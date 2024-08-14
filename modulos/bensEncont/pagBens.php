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
            .modal-content-BensControle{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%; /* acertar de acordo com a tela */
                max-width: 900px;
            }
            .quadro{
                position: relative; float: left; text-align: center; margin: 5px; width: 95%; padding: 2px; padding-top: 5px;
            }
            .modalMsg-content{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 7% auto;
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
                $("#carregaBens").load("modulos/bensEncont/relBens.php");

                //Impedir a mudança de data do registro de bem encontrado
                DataPr = compareDates ("30/06/2024", dataAtualFormatada()); // se o prazo for maior que a data atual
                if(DataPr == true){ // se for maior que a data atual
                    $('#dataregistro').datepicker({ uiLibrary: 'bootstrap3', locale: 'pt-br', format: 'dd/mm/yyyy' });
                }else{
                    document.getElementById("dataregistro").disabled = true;
                }
                $('#dataachado').datepicker({ uiLibrary: 'bootstrap3', locale: 'pt-br', format: 'dd/mm/yyyy' });

                $("#cpfproprietario").mask("999.999.999-99");
                $("#configCpfBens").mask("999.999.999-99");

                document.getElementById("botimprReg").style.visibility = "hidden"; 
                document.getElementById("botInsReg").style.visibility = "hidden"; 
                document.getElementById("botApagaBem").style.visibility = "hidden";
                document.getElementById("imgBensconfig").style.visibility = "hidden";
                if(parseInt(document.getElementById("guardaescEdit").value) === 1){ // tem que estar autorizado no cadastro de usuários
                    if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admIns").value)){
                        document.getElementById("botInsReg").style.visibility = "visible"; 
                        document.getElementById("imgBensconfig").style.visibility = "visible";
                    }
                    if(parseInt(document.getElementById("UsuAdm").value) > 6){
                        document.getElementById("botInsReg").style.visibility = "visible";
                        document.getElementById("botApagaBem").style.visibility = "visible";
                        document.getElementById("imgBensconfig").style.visibility = "visible";
                    }
                }
                if(parseInt(document.getElementById("guardaInsBens").value) === 1){ // Para pessoal da portaria registrar nos fins de semana
                    document.getElementById("botInsReg").style.visibility = "visible";
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


            });

            function abreRegistro(){
                document.getElementById("guardacod").value = 0;
                document.getElementById("botsalvareg").style.visibility = "visible"; 
                document.getElementById("dataregistro").value = document.getElementById("guardahoje").value;
                document.getElementById("dataachado").value = document.getElementById("guardahoje").value;
                document.getElementById("numprocesso").innerHTML = "";
                document.getElementById("descdobem").value = "";
                document.getElementById("localachado").value = "";
                document.getElementById("nomeachou").value = "";
                document.getElementById("telefachou").value = "";
                document.getElementById("relacmodalRegistro").style.display = "block";
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
                    "&numrelato="+encodeURIComponent(document.getElementById("guardaNumRelat").value)+
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
                                    document.getElementById("guardaNumRelat").value = Resp.numprocesso;
                                    document.getElementById("numprocesso").innerHTML = "Registrado sob nº "+Resp.numprocesso;
                                    document.getElementById("botsalvareg").innerHTML = "Salvar";
                                    document.getElementById("relacmodalRegistro").style.display = "block";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function mostraBem(Cod, modal, Restit){
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
                                        }else{
                                            document.getElementById('nomeproprietario').disabled = false;
                                            document.getElementById('cpfproprietario').disabled = false;
                                            document.getElementById('telefproprietario').disabled = false;
                                            document.getElementById('botsalvaRestit').disabled = false;
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
                                            document.getElementById("selecdestino").value = Resp.destino;
                                            document.getElementById("setordestino").value = Resp.setordestino;
                                            document.getElementById("nomefuncionario").value = Resp.nomerecebeu;
                                            document.getElementById('nomeproprietario').value = Resp.nomepropriet;
                                            document.getElementById('cpfproprietario').value = Resp.cpfpropriet;
                                            document.getElementById('telefproprietario').value = Resp.telefpropriet;
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
                    content: 'Confirma a restituição deste objeto?',
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
                                                    content: 'Objeto recebido na CSG por '+document.getElementById("usuarioNome").value,
                                                    draggable: true,
                                                    buttons: {
                                                        OK: function(){}
                                                    }
                                                });
                                                document.getElementById("relacmodalEncam").style.display = "none";
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

            //Aceita a transferência do objeto para guarda
            function modalDestino(){
                if(document.getElementById("setordestino").value === ""){
                    let element = document.getElementById('setordestino');
                    element.classList.add('destacaBorda');
                    document.getElementById("setordestino").focus();
                    $('#mensagemdest').fadeIn("slow");
                    document.getElementById("mensagemdest").innerHTML = "Insira o nome do setor de destino do objeto";
                    $('#mensagemdest').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("nomefuncionario").value === ""){
                    let element = document.getElementById('nomefuncionario');
                    element.classList.add('destacaBorda');
                    document.getElementById("nomefuncionario").focus();
                    $('#mensagemdest').fadeIn("slow");
                    document.getElementById("mensagemdest").innerHTML = "Insira o nome de quem recebeu";
                    $('#mensagemdest').fadeOut(2000);
                    return false;
                }
                if(document.getElementById("selecdestino").value === "0"){
                    let element = document.getElementById('selecdestino');
                    element.classList.add('destacaBorda');
                    document.getElementById("selecdestino").focus();
                    $('#mensagemdest').fadeIn("slow");
                    document.getElementById("mensagemdest").innerHTML = "Selecione o destino dado ao objeto";
                    $('#mensagemdest').fadeOut(2000);
                    return false;
                }
                $.confirm({
                    title: 'Destinação',
                    content: 'Confirma a destinação data ao objeto e arquivamento do processo?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/bensEncont/salvaBens.php?acao=destinaBem&codigo="+document.getElementById("guardacod").value
                                +"&setordestino="+encodeURIComponent(document.getElementById("setordestino").value)
                                +"&nomefuncionario="+encodeURIComponent(document.getElementById("nomefuncionario").value)
                                +"&selecdestino="+document.getElementById("selecdestino").value, true);
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
                                                    content: 'Processo Arquivado.',
                                                    draggable: true,
                                                    buttons: {
                                                        OK: function(){}
                                                    }
                                                });
                                                document.getElementById("relacmodalDest").style.display = "none";
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
                                            content: 'Não restaria outro marcado para gerenciar os bens encontrados.',
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
   
        </script>
    </head>
    <body>
        <?php
            if(!$Conec){
                echo "Sem contato com o PostGresql";
                return false;
            }

        date_default_timezone_set('America/Sao_Paulo');
        $Hoje = date('d/m/Y');

        $rs = pg_query($Conec, "SELECT column_name, data_type, character_maximum_length FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'livroreg'");
        $row = pg_num_rows($rs);
        if($row == 0){
            $Erro = 1;
            echo "Faltam tabelas. Informe à ATI.";
            return false;
        }
        $admIns = parAdm("insbens", $Conec, $xProj);   // nível para inserir 
        $admEdit = parAdm("editbens", $Conec, $xProj); // nível para editar -> foi para relBens.php
        $escEdit = parEsc("bens", $Conec, $xProj, $_SESSION["usuarioID"]); // está marcado no cadastro de usuários
        $SoInsBens = parEsc("soinsbens", $Conec, $xProj, $_SESSION["usuarioID"]); // está marcado no cadastro de usuários

        $OpDestBens = pg_query($Conec, "SELECT numdest, descdest FROM ".$xProj.".bensdestinos ORDER BY descdest");
        $OpConfig = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
        ?>
        <!-- div três colunas -->
        <div class="container" style="margin: 0 auto;">
            <div class="row">
                <div class="col quadro" style="text-align: left;"><button class="botpadrGr fundoAmarelo" id="botInsReg" onclick="abreRegistro();" >Novo Registro</button>
                <img src="imagens/settings.png" height="20px;" id="imgBensconfig" style="cursor: pointer; padding-left: 30px;" onclick="abreBensConfig();" title="Configurar o acesso ao processamento de bens encontrados">
            </div>
                <div class="col quadro"><h5>Registro de Bens Encontrados</h5></div> <!-- Central - espaçamento entre colunas  -->
                <div class="col quadro"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpBens();" title="Guia rápido"></div> 
            </div>
        </div>
        <br>
<!--<div class="col-12 col-xl-4 col-lg-4 col-md-4 col-sm-12 " style="border: 1px solid;">Teste de coluna</div> -->

        <input type="hidden" id="guardacod" value="0" /> <!-- id ocorrência -->
        <input type="hidden" id="mudou" value="0" />
        <input type="hidden" id="guardahoje" value="<?php echo $Hoje; ?>" />
        <input type="hidden" id="guardaNumRelat" value="0" />
        <input type="hidden" id="usuarioID" value="<?php echo $_SESSION["usuarioID"]; ?>" />
        <input type="hidden" id="usuarioNome" value="<?php echo $_SESSION["NomeCompl"]; ?>" />
        <input type="hidden" id="codusuins" value="0" />
        <input type="hidden" id="guardaescEdit" value="<?php echo $escEdit; ?>" />
        <input type="hidden" id="guardaInsBens" value="<?php echo $SoInsBens; ?>" />
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="admIns" value="<?php echo $admIns; ?>" /> <!-- nível mínimo para inserir  -->
        <input type="hidden" id="admEdit" value="<?php echo $admEdit; ?>" /> <!-- nível mínimo para editar -->

        <div style="margin: 10px; border: 2px solid blue; border-radius: 15px; padding: 10px;">
            <div id="carregaBens"></div>
        </div>

        <!-- div modal para registrar ocorrência do bem encontrado  -->
        <div id="relacmodalRegistro" class="relacmodal">
            <div class="modal-content-Bens">
                <span class="close" onclick="fechaModalReg();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"><button class="botpadrred" id="botimprReg" style="font-size: 80%;" onclick="imprReg();">Gerar PDF</button></div>
                        <div class="col quadro"><h5 id="titulomodal" style="color: #666;">Registro de Recebimento de Bens Encontrados</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><!-- <button class="botpadrred" onclick="enviaModalReg(1);">Enviar</button> --> </div> 
                    </div>
                </div>
                <div style="border: 2px solid blue; border-radius: 10px; padding: 10px;">
                    <table style="margin: 0 auto; width:85%;">
                        <tr>
                            <td class="etiqAzul">Data do recebimento: </td>
                            <td>
                                <input type="text" id="dataregistro" width="150" onmousedown="tiraBorda(id);" onkeydown="tiraBorda(id);" value="<?php echo $Hoje; ?>" onchange="modif();" placeholder="Data" style="font-size: .9em; text-align: center; border: 1px solid; border-radius: 3px;">
                                <label id="numprocesso" class="etiqAzul" style="padding-left: 30px; color: red;"></label>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Descrição do bem encontrado: </td>
                            <td>
                                <textarea style="border: 1px solid blue; border-radius: 10px; padding: 3px;" rows="3" cols="65" id="descdobem" onchange="modif();"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Data em que foi encontrado: </td>
                            <td><input type="text" id="dataachado" width="150" onmousedown="tiraBorda(id);" onkeydown="tiraBorda(id);" value="<?php echo $Hoje; ?>" onchange="modif();" placeholder="Data" style="font-size: .9em; text-align: center; border: 1px solid; border-radius: 3px;"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Local em que foi encontrado: </td>
                            <td><textarea style="border: 1px solid blue; border-radius: 10px; padding: 3px;" rows="2" cols="65" id="localachado" onchange="modif();"></textarea></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Nome do Colaborador que encontrou o bem: </td>
                            <td><input type="text" id="nomeachou" onmousedown="tiraBorda(id);" onkeydown="tiraBorda(id);" value="" onchange="modif();" placeholder="Nome do colaborador que encontrou" style="font-size: .9em; width: 90%;"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Telefone: </td>
                            <td><input type="text" id="telefachou" onmousedown="tiraBorda(id);" onkeydown="tiraBorda(id);" value="" onchange="modif();" placeholder="Telefone do colaborador que encontrou" style="font-size: .9em; width: 90%;"></td>
                        </tr>
                    </table>

                    <div id="mensagem" style="color: red; font-weight: bold; margin: 5px; text-align: center; padding-top: 10px;"></div>
                    <br>
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
                <div style="border: 2px solid blue; border-radius: 10px; padding: 10px;">
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
                            <td style="padding-left: 15px; padding-right: 15px;"><div style="border: 1px solid blue; border-radius: 10px; text-align: center;">(a) <label style="font-weight: bold;"> <?php echo $_SESSION["NomeCompl"]; ?> </label></div></td>
                        </tr>
                        <tr>
                            <td><button class="botpadrTijolo" id="botApagaBem" onclick="ApagarBem();">Apagar</button></td>
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

        <!-- div modal para encaminhar o objeto para CSG  -->
        <div id="relacmodalEncam" class="relacmodal">
            <div class="modal-content-Bens">
                <span class="close" onclick="fechaModalEncam();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"></div>
                        <div class="col quadro"><h5 id="titulomodal" style="color: #666;">Registro de Encaminhamento para CSG</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><!-- <button class="botpadrred" onclick="enviaModalReg(1);">Enviar</button> --> </div> 
                    </div>
                </div>
                <div style="border: 2px solid blue; border-radius: 10px; padding: 10px;">
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
                            <td><div style="border: 1px solid blue; border-radius: 10px; padding: 3px;">Declaro que recebi nesta CSG, o processo acima identificado para armazenamento, destinação e arquivamento do processo.</div></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>

                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Funcionário da CSG: </td>
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
                <div style="border: 2px solid blue; border-radius: 10px; padding: 10px;">
                    <table style="margin: 0 auto; width:85%;">
                        <tr>
                            <td class="etiqAzul">Processo: </td>
                            <td>
                                <label id="numprocessoDest" class="etiqAzul" style="padding-left: 5px; font-size: 1.1rem;"></label>
                                <label id="etiqprocessoDest"class="etiqAzul"></label>
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
                            <td class="etiqAzul">Setor de Destino: </td>
                            <td><input type="text" id="setordestino" onmousedown="tiraBorda(id);" onkeydown="tiraBorda(id);" value="" onchange="modif();" placeholder="Nome do setor de destino" style="font-size: .9em; width: 90%;" onkeypress="if(event.keyCode===13){javascript:foco('nomefuncionario');return false;}"></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Nome do Funcionário do Setor: </td>
                            <td><input type="text" id="nomefuncionario" onmousedown="tiraBorda(id);" onkeydown="tiraBorda(id);" value="" onchange="modif();" placeholder="Nome de quem recebe" style="font-size: .9em; width: 90%;" onkeypress="if(event.keyCode===13){javascript:foco('setordestino');return false;}"></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding-bottom: 20px;"></td>
                        </tr>

                        <tr>
                            <td colspan="2" style="text-align: center;"><label>O bem descrito neste processo foi destinado a: </label>
<!--
                            <select id="selecdestino" onclick="tiraBorda(id);" onchange="modif();" style="font-size: 0.9rem; min-width: 100px;" title="Selecione o destino dado ao bem encontrado.">
                                <option value="0"></option>
                                <option value="1">Descarte</option>
                                <option value="2">Destruição</option>
                                <option value="3">Doação</option>
                                <option value="4">Venda</option>
                            </select>
-->
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

                        </tr>

                        <tr>
                            <td colspan="2" style="text-align: center; padding-bottom: 20px; padding-top: 20px;">Este processo será arquivado nesta data: <?php echo $Hoje; ?></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Assinatura: </td>
                            <td style="padding-left: 15px; padding-right: 15px;"><div style="border: 1px solid blue; border-radius: 10px; text-align: center;">(a) <label style="font-weight: bold;"> <?php echo $_SESSION["NomeCompl"]; ?> </label></div></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td style="text-align: center; padding-top: 25px;"><button class="botpadrblue" id="botsalvaregdest" onclick="modalDestino();">Arquivar Processo</button></td>
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


         <!-- Modal configuração-->
         <div id="modalBensConfig" class="relacmodal">
            <div class="modal-content-BensControle">
                <span class="close" onclick="fechaBensConfig();">&times;</span>

                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"></div>
                        <div class="col quadro"><h5 id="titulomodal" style="text-align: center; color: #666;">Configuração <br>Bens Encontrados</h5></div> <!-- Central - espaçamento entre colunas  -->
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
                        <td colspan="4" style="text-align: center;">Busca Nome ou CPF do Solicitante</td>
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
                        <td class="etiq80" title="Registrar recebimento e destino de bens encontrados">DAF:</td>
                        <td colspan="4">
                            <input type="checkbox" id="preencheBens" title="Administrar, prover a guarda e dar destino aos bens encontrados." onchange="marcaBem(this, 'bens');" >
                            <label for="preencheBens" title="Administrar, prover a guarda e dar destino aos bens encontrados.">administrar, prover a guarda e dar destino aos Bens Encontrados.</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiq80" title="Apenas registrar o recebimento de bens encontrados. Apropriado para os funcionários da Portaria.">Portaria:</td>
                        <td colspan="4">
                            <input type="checkbox" id="soPreencheBens" title="Apenas registrar recebimento de bens encontrados. Apropriado para os funcionários da Portaria." onchange="marcaBem(this, 'soinsbens');" >
                            <label for="soPreencheBens" title="Apenas registrar recebimento de bens encontrados. Apropriado para os funcionários da Portaria.">apenas registrar Bens Encontrados</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiq80" style="border-bottom: 1px solid;" title="Fiscalizar os registros de bens encontrados - Só fiscaliza. Não pode registrar os bens encontrados.">Administração:</td>
                        <td colspan="4" style="border-bottom: 1px solid;">
                            <input type="checkbox" id="fiscBens" title="Fiscalizar os registros de bens encontrados - Só fiscaliza. Não pode registrar os bens encontrados." onchange="marcaBem(this, 'fiscbens');" >
                            <label for="fiscBens" title="Fiscalizar os registros de bens encontrados - Só fiscaliza. Não pode registrar os bens encontrados.">fiscalizar os registros de Bens Encontrados</label>
                        </td>
                    </tr>


                </table>
            </div>
        </div> <!-- Fim Modal-->



        <!-- div modal para leitura instruções -->
        <div id="relacHelpBens" class="relacmodal">
            <div class="modalMsg-content">
                <span class="close" onclick="fechaModalHelp();">&times;</span>
                <h4 style="text-align: center; color: #666;">Informações</h4>
                <h5 style="text-align: center; color: #666;">Bens Encontrados</h5>
                <div style="border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px;">
                    Regras inseridas:
                    <ul>
                        <li>1 - Bens encontrados no recinto devem ser encaminhados para guarda da Diretoria Administrativa e Financeira (DAF).</li>
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