<?php
session_start();
require_once("abrealas.php");
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="class/superfish/js/jquery.js"></script>  <!-- versão 1.12.1 veio com o superfish - tem que usar esta, a versão 3.6 não recarrega a página-->
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script> 
        <style>
            .modal-content-Usu{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 15% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 80%; /* acertar de acordo com a tela */
                max-width: 900px;
            }
            .modal-content-Chaves{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 15% auto; 
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 70%;
                max-width: 900px;
            }
            .modalMsg-content{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 60%; 
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
                //Provisório
                document.getElementById("imgInfo").style.visibility = "hidden";

                document.getElementById("botinserir").style.visibility = "hidden"; // botão de inserir usuário
                if(parseInt(document.getElementById("UsuAdm").value) === 7){ // superusuário 
                    document.getElementById("botinserir").style.visibility = "visible";
                }
                $("#faixacentral").load("modulos/config/jUsu.php?acao=todos");

                modalEdit = document.getElementById('relacmodalUsu'); //span[0]
                spanEdit = document.getElementsByClassName("close")[0];
                window.onclick = function(event){
                    if(event.target === modalEdit){
                        modalEdit.style.display = "none";
                    }
                };
            });
            function format_CnpjCpf(value){
                //https://gist.github.com/davidalves1/3c98ef866bad4aba3987e7671e404c1e
                const CPF_LENGTH = 11;
                const cnpjCpf = value.replace(/\D/g, '');
                if (cnpjCpf.length === CPF_LENGTH) {
                    return cnpjCpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/g, "\$1.\$2.\$3-\$4");
                } 
                  return cnpjCpf.replace(/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/g, "\$1.\$2.\$3/\$4-\$5");
            }

            function carregaModal(id){
                document.getElementById("salvar").disabled = false;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=buscausu&numero="+id+"&cpf="+encodeURIComponent(document.getElementById("guardaid_cpf").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){

//if(document.getElementById("guardausu_cpf").value == "13652176049"){
//alert(ajax.responseText);
//}
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 2){
                                    alert("CPF não encontrado.");
                                }
                                if(parseInt(Resp.coderro) === 0){
                                    if(Resp.usuario == null){
                                        alert("Sem contato com a tabela pessoas. CPF nulo")
                                    }
                                    if(Resp.usuarioNome == null){
                                        alert("Sem contato com a tabela pessoas. Usuário nulo")
                                    }
                                    document.getElementById("usulogin").value = format_CnpjCpf(Resp.usuario);
                                    document.getElementById("usuarioNome").value = Resp.usuarioNome;
                                    document.getElementById("nomeUsuChaves").innerHTML = "Definir chaves autorizadas para: "+Resp.usuarioNome; // para a escolha de chaves
                                    document.getElementById("nomecompl").value = Resp.nomecompl;
                                    document.getElementById("diaAniv").value = Resp.diaAniv;
                                    document.getElementById("mesAniv").value = Resp.mesAniv;
                                    document.getElementById("ultlog").value = Resp.ultlog;
                                    document.getElementById("acessos").value = Resp.acessos;
                                    document.getElementById("flAdm").value = Resp.usuarioAdm;
                                    document.getElementById("setor").value = Resp.setor;
                                    if(parseInt(Resp.ativo) === 1){
                                        document.getElementById("atividade1").checked = true;
                                    }else{
                                        document.getElementById("atividade2").checked = true;
                                    }
                                    if(parseInt(Resp.lroPortaria) === 1){
                                        document.getElementById("preencheLro").checked = true;
                                    }else{
                                        document.getElementById("preencheLro").checked = false;
                                    }

                                    if(parseInt(Resp.lroFiscaliza) === 1){
                                        document.getElementById("fiscalizaLro").checked = true;
                                    }else{
                                        document.getElementById("fiscalizaLro").checked = false;
                                    }

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

                                    if(parseInt(Resp.leituraAgua) === 1){
                                        document.getElementById("leituraAgua").checked = true;
                                    }else{
                                        document.getElementById("leituraAgua").checked = false;
                                    }

                                    if(parseInt(Resp.fisc_agua) === 1){
                                        document.getElementById("fisc_Agua").checked = true;
                                    }else{
                                        document.getElementById("fisc_Agua").checked = false;
                                    }

                                    if(parseInt(Resp.leituraEletric) === 1){
                                        document.getElementById("leituraEletric").checked = true;
                                    }else{
                                        document.getElementById("leituraEletric").checked = false;
                                    }
                                    if(parseInt(Resp.leituraEletric2) === 1){
                                        document.getElementById("leituraEletric2").checked = true;
                                    }else{
                                        document.getElementById("leituraEletric2").checked = false;
                                    }
                                    if(parseInt(Resp.leituraEletric3) === 1){
                                        document.getElementById("leituraEletric3").checked = true;
                                    }else{
                                        document.getElementById("leituraEletric3").checked = false;
                                    }
                                    if(parseInt(Resp.fisc_eletric) === 1){
                                        document.getElementById("fisc_Eletric").checked = true;
                                    }else{
                                        document.getElementById("fisc_Eletric").checked = false;
                                    }

                                    if(parseInt(Resp.regarcond) === 1){
                                        document.getElementById("registroArCond").checked = true;
                                    }else{
                                        document.getElementById("registroArCond").checked = false;
                                    }

                                    if(parseInt(Resp.regarcond2) === 1){
                                        document.getElementById("registroArCond2").checked = true;
                                    }else{
                                        document.getElementById("registroArCond2").checked = false;
                                    }
                                    if(parseInt(Resp.regarcond3) === 1){
                                        document.getElementById("registroArCond3").checked = true;
                                    }else{
                                        document.getElementById("registroArCond3").checked = false;
                                    }

                                    if(parseInt(Resp.fiscarcond) === 1){
                                        document.getElementById("fiscalArCond").checked = true;
                                    }else{
                                        document.getElementById("fiscalArCond").checked = false;
                                    }

                                    if(parseInt(Resp.regelev) === 1){
                                        document.getElementById("registroElev").checked = true;
                                    }else{
                                        document.getElementById("registroElev").checked = false;
                                    }
                                    if(parseInt(Resp.fiscelev) === 1){
                                        document.getElementById("fiscalElev").checked = true;
                                    }else{
                                        document.getElementById("fiscalElev").checked = false;
                                    }

//                                    if(parseInt(Resp.escala) === 1){
//                                        document.getElementById("escalaEft").checked = true;
//                                    }else{
//                                        document.getElementById("escalaEft").checked = false;
//                                    }
                                    document.getElementById("grupoEscala").value = Resp.grupoescala;
                                    if(parseInt(Resp.editaescala) === 1){
                                        document.getElementById("escalante").checked = true;
                                    }else{
                                        document.getElementById("escalante").checked = false;
                                    }
                                    if(parseInt(Resp.fiscescala) === 1){
                                        document.getElementById("fiscalEscalas").checked = true;
                                    }else{
                                        document.getElementById("fiscalEscalas").checked = false;
                                    }

                                    if(parseInt(Resp.claviculario) === 1){
                                        document.getElementById("registroChaves").checked = true;
                                    }else{
                                        document.getElementById("registroChaves").checked = false;
                                    }
                                    if(parseInt(Resp.pegachave) === 1){
                                        document.getElementById("retiraChave").checked = true;
//                                        if(parseInt(document.getElementById("guardaEscolheChaves").value) === 1){ // escolha ativada - Campo esc_chaves1=1 em paramsis
                                            document.getElementById("botaoChaves").style.visibility = "visible";
//                                        }else{
//                                            document.getElementById("botaoChaves").style.visibility = "hidden";
//                                        }
                                    }else{
                                        document.getElementById("retiraChave").checked = false;
                                        document.getElementById("botaoChaves").style.visibility = "hidden";
                                    }
                                    if(parseInt(Resp.fiscchaves) === 1){
                                        document.getElementById("fiscalChaves").checked = true;
                                    }else{
                                        document.getElementById("fiscalChaves").checked = false;
                                    }

                                    if(parseInt(Resp.editChaves) === 1){
                                        document.getElementById("editChaves").checked = true;
                                    }else{
                                        document.getElementById("editChaves").checked = false;
                                    }

                                    if(parseInt(Resp.contrato) === 1){
                                        document.getElementById("insContrato").checked = true;
                                    }else{
                                        document.getElementById("insContrato").checked = false;
                                    }
                                    if(parseInt(Resp.fisccontrato) === 1){
                                        document.getElementById("fiscalContrato").checked = true;
                                    }else{
                                        document.getElementById("fiscalContrato").checked = false;
                                    }
                                    
                                    if(parseInt(Resp.extintor) === 1){
                                        document.getElementById("insExtintor").checked = true;
                                    }else{
                                        document.getElementById("insExtintor").checked = false;
                                    }
                                    if(parseInt(Resp.fisc_extint) === 1){
                                        document.getElementById("fiscalExtintor").checked = true;
                                    }else{
                                        document.getElementById("fiscalExtintor").checked = false;
                                    }
                                    if(parseInt(Resp.mostraAniv) === 1){
                                        document.getElementById("mostrarAniver").checked = true;
                                    }else{
                                        document.getElementById("mostrarAniver").checked = false;
                                    }

                                    document.getElementById("titulomodal").innerHTML = "Edição de Usuários";
                                    document.getElementById("ressetsenha").disabled = false;
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("relacmodalUsu").style.display = "block";
                                    document.getElementById("usulogin").disabled = true;
                                }
                                if(parseInt(Resp.coderro) === 1){
                                    if(Resp.usuario == null){
                                        alert("Sem contato com a tabela pessoas. CPF nulo")
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaModal(){
                if(document.getElementById("usulogin").value === ""){
                    return false;
                }
                if(document.getElementById("setor").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha o campo <u>Setor de Trabalho/Diretoria/Assessoria</u>";
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                if(document.getElementById("flAdm").value === ""){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha o campo <u>Nível Administrativo</u> do usuário";
                    $('#mensagem').fadeOut(3000);
                    return false;
                }
                Lro = 0;
                if(document.getElementById("preencheLro").checked === true){
                    Lro = 1;
                }
                FiscLro = 0;
                if(document.getElementById("fiscalizaLro").checked === true){
                    FiscLro = 1;
                }
                Bens = 0;
                if(document.getElementById("preencheBens").checked === true){
                    Bens = 1;
                }
                FiscBens = 0;
                if(document.getElementById("fiscBens").checked === true){
                    FiscBens = 1;
                }
                SoInsBens = 0;
                if(document.getElementById("soPreencheBens").checked === true){
                    SoInsBens = 1;
                }
                Agua = 0;
                if(document.getElementById("leituraAgua").checked === true){
                    Agua = 1;
                }
                FiscAgua = 0;
                if(document.getElementById("fisc_Agua").checked === true){
                    FiscAgua = 1;
                }
                Eletric = 0;
                if(document.getElementById("leituraEletric").checked === true){
                    Eletric = 1;
                }

                Eletric2 = 0;
                if(document.getElementById("leituraEletric2").checked === true){
                    Eletric2 = 1;
                }
                Eletric3 = 0;
                if(document.getElementById("leituraEletric3").checked === true){
                    Eletric3 = 1;
                }
                FiscEletric = 0;
                if(document.getElementById("fisc_Eletric").checked === true){
                    FiscEletric = 1;
                }
                ArCond = 0;
                if(document.getElementById("registroArCond").checked === true){
                    ArCond = 1;
                }
                ArCond2 = 0;
                if(document.getElementById("registroArCond2").checked === true){
                    ArCond2 = 1;
                }
                ArCond3 = 0;
                if(document.getElementById("registroArCond3").checked === true){
                    ArCond3 = 1;
                }
                FiscAr = 0;
                if(document.getElementById("fiscalArCond").checked === true){
                    FiscAr = 1;
                }
                Elev = 0;
                if(document.getElementById("registroElev").checked === true){
                    Elev = 1;
                }
                FiscElev = 0;
                if(document.getElementById("fiscalElev").checked === true){
                    FiscElev = 1;
                }
//                Escala = 0;
//                if(document.getElementById("escalaEft").checked === true){
//                    Escala = 1;
//                }
//                if(parseInt(Escala) === 1 && parseInt(document.getElementById("grupoEscala").value) === 0){
//                if(parseInt(document.getElementById("grupoEscala").value) === 0){
//                    $('#mensagem').fadeIn("slow");
//                    document.getElementById("mensagem").innerHTML = "Preencha o campo <u>Grupo da Escala</u> do usuário";
//                    $('#mensagem').fadeOut(3000);
//                    document.getElementById("grupoEscala").focus();
//                    return false;
//                }

                Escalante = 0;
                if(document.getElementById("escalante").checked === true){
                    Escalante = 1;
                }
                if(parseInt(Escalante) === 1 && parseInt(document.getElementById("grupoEscala").value) === 0){
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Preencha o campo <u>Grupo da Escala</u> do usuário";
                    $('#mensagem').fadeOut(3000);
                    document.getElementById("grupoEscala").focus();
                    return false;
                }
                FiscEscala = 0;
                if(document.getElementById("fiscalEscalas").checked === true){
                    FiscEscala = 1;
                }
                Clavic = 0;
                if(document.getElementById("registroChaves").checked === true){
                    Clavic = 1;
                }
                PegaChave = 0;
                if(document.getElementById("retiraChave").checked === true){
                    PegaChave = 1;
                }
                FiscChaves = 0;
                if(document.getElementById("fiscalChaves").checked === true){
                    FiscChaves = 1;
                }
                EditaChaves = 0;
                if(document.getElementById("editChaves").checked === true){
                    EditaChaves = 1;
                }
                Contr = 0;
                if(document.getElementById("insContrato").checked === true){
                    Contr = 1;
                }
                FiscContr = 0;
                if(document.getElementById("fiscalContrato").checked === true){
                    FiscContr = 1;
                }
                Extint = 0;
                if(document.getElementById("insExtintor").checked === true){
                    Extint = 1;
                }
                FiscExtint = 0;
                if(document.getElementById("fiscalExtintor").checked === true){
                    FiscExtint = 1;
                }
                MarcaAniv = 0;
                if(document.getElementById("mostrarAniver").checked === true){
                    MarcaAniv = 1;
                }
                if(parseInt(document.getElementById("mudou").value) === 1){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=salvaUsu&numero="+document.getElementById("guardaid_click").value
                        +"&cpf="+encodeURIComponent(document.getElementById("usulogin").value)
                        +"&guardaidpessoa="+document.getElementById("guardaidpessoa").value
                        +"&usulogado="+document.getElementById("guarda_usulogado_id").value
                        +"&usuarioNome="+encodeURIComponent(document.getElementById("usuarioNome").value)
                        +"&ativo="+document.getElementById("guardaAtiv").value
                        +"&setor="+document.getElementById("setor").value
                        +"&flAdm="+document.getElementById("flAdm").value
                        +"&lro="+Lro
                        +"&fisclro="+FiscLro
                        +"&bens="+Bens
                        +"&soinsbens="+SoInsBens
                        +"&fiscbens="+FiscBens
                        +"&agua="+Agua
                        +"&fiscagua="+FiscAgua
                        +"&eletric="+Eletric
                        +"&eletric2="+Eletric2
                        +"&eletric3="+Eletric3
                        +"&fisceletric="+FiscEletric
                        +"&arcond="+ArCond
                        +"&arcond2="+ArCond2
                        +"&arcond3="+ArCond3
                        +"&fiscar="+FiscAr
                        +"&elev="+Elev
                        +"&fiscelev="+FiscElev
                        +"&grupoesc="+document.getElementById("grupoEscala").value
                        +"&escalante="+Escalante
                        +"&fiscalescala="+FiscEscala
                        +"&clavic="+Clavic
                        +"&pegachave="+PegaChave
                        +"&fiscchaves="+FiscChaves
                        +"&editachaves="+EditaChaves
                        +"&contrato="+Contr
                        +"&fisccontrato="+FiscContr
                        +"&Extint="+Extint
                        +"&fiscExtint="+FiscExtint
                        +"&mostraaniver="+MarcaAniv
                        , true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//if(document.getElementById("guardausu_cpf").value == "13652176049"){
//alert(ajax.responseText);
//}
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) === 2){
                                        $('#mensagem').fadeIn("slow");
                                        document.getElementById("mensagem").innerHTML = "Esse nome <u>JÁ EXISTE</u>.";
                                        $('#mensagem').fadeOut("slow");
                                    }else{
                                        document.getElementById("mudou").value = "0";
                                        document.getElementById("guardaid_click").value = 0;
                                        document.getElementById("relacmodalUsu").style.display = "none";
                                    }
                                    $("#faixacentral").load("modulos/config/jUsu.php?acao=todos");
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("mudou").value = "0";
                    document.getElementById("relacmodalUsu").style.display = "none";
                }
            }

            function checaEntrada(){
                checaLogin();
                if(validaCPF(document.getElementById("usulogin").value)){
                    checaLogin();
                }else{
                    document.getElementById("relacmodalUsu").style.display = "none";
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "CPF inválido.";
                    $('#mensagem').fadeOut(3000);
                    insUsu();
                }
            }
            function checaLogin(){
                document.getElementById("guardaid_cpf").value = 0;
                document.getElementById("salvar").disabled = false;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=checaLogin&valor="+encodeURIComponent(document.getElementById("usulogin").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 0){
                                    if(parseInt(Resp.quantiUsu) > 0){
                                        if(parseInt(Resp.quantiUsu) > 1){
                                            $('#mensagem').fadeIn("slow");
                                            document.getElementById("mensagem").innerHTML = "Encontrados "+Resp.quantiUsu+" registros desse CPF. Verifique seus arquivos.";
                                            $('#mensagem').fadeOut(13000);
                                            document.getElementById("usulogin").value = "";
                                        }else{
                                            document.getElementById("usulogin").value = format_CnpjCpf(Resp.cpf);
                                            document.getElementById("usuarioNome").value = Resp.nomeusual;
                                            document.getElementById("nomecompl").value = Resp.nomecompl;
                                            document.getElementById("diaAniv").value = Resp.dianasc;
                                            document.getElementById("mesAniv").value = Resp.mesnasc;
                                            if(parseInt(Resp.ativo) === 1){
                                                document.getElementById("atividade1").checked = true;
                                            }else{
                                                document.getElementById("atividade2").checked = true;
                                            }
                                            document.getElementById("ultlog").value = Resp.ultlog;
                                            document.getElementById("acessos").value = Resp.acessos;
                                            document.getElementById("flAdm").value = Resp.adm;
                                            document.getElementById("setor").value = Resp.setor;
                                            document.getElementById("guardaidpessoa").value = Resp.idpessoa;
//                                            document.getElementById("guardaid_click").value = Resp.idpessoa; // para salvar modif se for procurado por inserção ao invés de click
                                            document.getElementById("usulogin").disabled = true;
                                            if(parseInt(Resp.jatem) === 1){
                                                document.getElementById("guardaid_click").value = Resp.idpessoa; // para salvar modif se for procurado por inserção ao invés de click
                                                $('#mensagemCima').fadeIn("slow");
                                                if(parseInt(Resp.ativo) === 2 && Resp.datainat != ""){
                                                    document.getElementById("mensagemCima").innerHTML = "Usuário já cadastrado no site. Foi bloqueado em "+Resp.datainat;
                                                }else{
                                                    document.getElementById("mensagemCima").innerHTML = "Usuário já cadastrado no site.";
                                                }
                                                $('#mensagemCima').fadeOut(5000);
                                            }
                                        }
                                    }
                                }
                                if(parseInt(Resp.coderro) === 2){
                                    $('#mensagem').fadeIn("slow");
                                    document.getElementById("mensagem").innerHTML = "CPF ("+format_CnpjCpf(document.getElementById("usulogin").value)+") não encontrado na base de dados.";
                                    $('#mensagem').fadeOut(10000);
                                    document.getElementById("usulogin").value = "";
                                }
                                if(parseInt(Resp.coderro) === 3){
                                    document.getElementById("usulogin").value = format_CnpjCpf(Resp.cpf);
                                    document.getElementById("usuarioNome").value = Resp.nomecompl;
                                    document.getElementById("nomecompl").value = Resp.nomecompl;
                                    document.getElementById("diaAniv").value = Resp.dianasc;
                                    document.getElementById("mesAniv").value = Resp.mesnasc;
                                    if(parseInt(Resp.ativo) === 1){
                                        document.getElementById("atividade1").checked = true;
                                    }else{
                                        document.getElementById("atividade2").checked = true;
                                    }
                                    document.getElementById("ultlog").value = Resp.ultlog;
                                    document.getElementById("acessos").value = Resp.acessos;
                                    document.getElementById("flAdm").value = Resp.adm;
                                    document.getElementById("setor").value = Resp.setor;
                                }
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve erro no servidor");
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function insUsu(){
                document.getElementById("guardaid_click").value = 0;
                document.getElementById("botaoChaves").style.visibility = "hidden";
                if(document.getElementById("guardaSiglaSetor").value === "n/d"){
                    document.getElementById("textoMsg").innerHTML = "Administrador sem setor definido.";
                    document.getElementById("relacmensagem").style.display = "block"; // está em modais.php
                    setTimeout(function(){
                        document.getElementById("relacmensagem").style.display = "none";
                    }, 2000);
                    return false;
                }
                document.getElementById("usulogin").disabled = false;
                document.getElementById("usulogin").value = "";
                document.getElementById("usuarioNome").value = "";
                document.getElementById("nomecompl").value = "";
                document.getElementById("setor").value = "";
                document.getElementById("flAdm").value = "";
                document.getElementById("diaAniv").value = "";
                document.getElementById("mesAniv").value = "";
                document.getElementById("acessos").value = "-";
                document.getElementById("ultlog").value = "-";
                document.getElementById("retiraChave").checked = false;
                if(parseInt(document.getElementById("UsuAdm").value) < 7){
                    document.getElementById("setor").disabled = true;
                    document.getElementById("flAdm").value = 2; // usuário registrado
                    document.getElementById("flAdm").disabled = true;
                }
                document.getElementById("atividade1").checked = true;
                document.getElementById("titulomodal").innerHTML = "Inserção de Usuário";
                document.getElementById("ressetsenha").disabled = true;
                document.getElementById("relacmodalUsu").style.display = "block";
                document.getElementById("usulogin").focus();
            }

            function salvaAtiv(Valor){
                document.getElementById("guardaAtiv").value = Valor;
                document.getElementById("mudou").value = "1";
//                if(parseInt(Valor) === 0 && parseInt(document.getElementById("grupoEscala").value) > 0){
//                    ajaxIni();
//                    if(ajax){
//                        ajax.open("POST", "modulos/config/registr.php?acao=buscaEftEscala&cpf="+encodeURIComponent(document.getElementById("guardaid_cpf").value), true);
//                        ajax.onreadystatechange = function(){
//                            if(ajax.readyState === 4 ){
//                                if(ajax.responseText){
//alert(ajax.responseText);
//                                    Resp = eval("(" + ajax.responseText + ")");
//                                    if(parseInt(Resp.coderro) === 1){
//                                        alert("Houve um erro no servidor.")
//                                    }else{
//                                        if(parseInt(Resp.efetivo) === 1){
//                                            alert("Participa da Escala: "+Resp.descgrupo);
//                                        }
//                                    }
//                                }
//                            }
//                        };
//                        ajax.send(null);
//                    }
//                }
            }

            function deletaModal(){
                let Conf = confirm("Não haverá possibilidade de recuperação.\nConfirma deletar os dados deste usuário?");
                if(Conf){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=deletausu&numero="+document.getElementById("guardaid_click").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.")
                                    }else{
                                        document.getElementById("mudou").value = "0";
                                        document.getElementById("relacmodalUsu").style.display = "none";
                                        $("#faixacentral").load("modulos/config/jUsu.php?acao=todos");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }
            }

            function mostraBotChave(Obj, Cod){
                document.getElementById("mudou").value = "1";
                if(parseInt(document.getElementById("guardaid_click").value) > 0){
                    if(Obj.checked === true){
                        document.getElementById("botaoChaves").style.visibility = "visible";
                    }else{
                        document.getElementById("botaoChaves").style.visibility = "hidden";
                    }
                }else{
                    document.getElementById("botaoChaves").style.visibility = "hidden";
                }
            }

            function marcaChaveInd(Obj, Cod){
                if(Obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=marcaChaveUsuario&param="+Valor+"&codigo="+Cod+"&usuario="+document.getElementById("guardaid_click").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("chavesmarcadas").innerHTML = "Marcadas: "+Resp.marcadas;
                                    if(parseInt(Resp.todas) === 1){
                                        document.getElementById("checkGeral").checked = true;
                                    }else{
                                        document.getElementById("checkGeral").checked = false;
                                    }
                                    $("#faixachaves").load("modulos/config/escChave.php?usuario="+document.getElementById("guardaid_click").value);
                                }
                            }
                        }
                    };
                    ajax.send(null);
                } 
            }

            function marcaChaveSemana(Obj, Cod, Marca, Sem, NumDia){
                if(Obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave.php?acao=marcaChaveUsuarioSemana&param="+Valor+"&codigo="+Cod+"&semana="+Sem+"&usuario="+document.getElementById("guardaid_click").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    $("#faixachaves").load("modulos/config/escChave.php?usuario="+document.getElementById("guardaid_click").value);
                                }
                            }
                        }
                    };
                    ajax.send(null);
                } 
            }

            function marcaChaveTodas(Obj){
                if(Obj.checked === true){
                    Valor = 1;
                    Texto = "Confirma marcar todas as chaves?"
                }else{
                    Valor = 0;
                    Texto = "Confirma DESMARCAR todas as chaves?"
                }
                $.confirm({
                    title: 'Confirmação!',
                    content: Texto,
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/config/registr.php?acao=marcaChaveTodas&param="+Valor+"&usuario="+document.getElementById("guardaid_click").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }
                                            $("#faixachaves").load("modulos/config/escChave.php?usuario="+document.getElementById("guardaid_click").value);
                                        }
                                    }
                                };
                                ajax.send(null);
                            }
                        },
                        Não: function () {
                            if(Obj.checked === true){
                                Obj.checked = false;
                            }else{
                                Obj.checked = true;
                            }
                        }
                    }
                });
            }

            function AbreModalChaves(){
                document.getElementById("relacmodalChaves").style.display = "block";
                $("#faixachaves").load("modulos/config/escChave.php?usuario="+document.getElementById("guardaid_click").value);
            }

            function fechaModal(){
                document.getElementById("guardaid_click").value = 0;
                document.getElementById("relacmodalUsu").style.display = "none";
            }
            function fechaModalChaves(){
                document.getElementById("relacmodalChaves").style.display = "none";
            }
            function foco(id){
                document.getElementById(id).focus();
            }
            function modif(){ // assinala se houve qualquer modificação nos campos do modal durante a edição para evitar salvar desnecessariamente
                document.getElementById("mudou").value = "1";
            }

            function resetSenha(){
                $.confirm({
                    title: 'Confirmação!',
                    content: 'A senha deste usuário será modificada para o CPF. Prossegue?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/config/registr.php?acao=resetsenha&numero="+encodeURIComponent(document.getElementById("guardaid_cpf").value), true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                document.getElementById("textoMsg").innerHTML = "Senha modificada para o CPF";
                                                document.getElementById("relacmensagem").style.display = "block"; // está em modais.php
                                                setTimeout(function(){
                                                    document.getElementById("relacmensagem").style.display = "none";
                                                }, 2000);
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

            function mudaSetor(){ // Qdo muda de setor desmarca 
                document.getElementById("mudou").value = "1";
                document.getElementById("preencheLro").checked = false;
                document.getElementById("registroChaves").checked = false;
                document.getElementById("editChaves").checked = false;
                document.getElementById("preencheBens").checked = false;
                document.getElementById("leituraAgua").checked = false;
                document.getElementById("leituraEletric").checked = false;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=checaBoxes&param="+Valor+"&numero="+encodeURIComponent(document.getElementById("guardaid_cpf").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                } 
            }

            function acertaMarca(){
                if(document.getElementById("preencheLro").checked == false){
                    document.getElementById("registroChaves").checked = false;
                }
                if(document.getElementById("preencheLro").checked == true){
                    document.getElementById("registroChaves").checked = true;
                }
            }

            function insExecTarefa(){ // inserir quem pode executar tarefas de outro
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=insexectarefa&usuindiv="+document.getElementById("guardaid_click").value
                    +"&usugrupo="+encodeURIComponent(document.getElementById("exectarefa").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                } 
            }
            function resumoUsu(){
                window.open("modulos/config/imprUsu.php?acao=listaUsuarios", "ListaUsu");
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

            function carregaHelpUsu(Cod){
                if(parseInt(Cod) === 1){
                    Titulo = "Preencher o Livro de Registro de Ocorrências";
                    Texto = "Basta esta marca para acessar e inserir registros no LRO. Essa atividade é bem específica e não é adequado controlar com níveis administrativos.";
                }
                if(parseInt(Cod) === 2){
                    Titulo = "Registrar leitura diária dos Medidores";
                    Texto = "Além desta marca é necessário que o usuário tenha o nível administrativo mínimo para iserir as leituras, previsto em Parâmetros do Sistema.";
                }
                if(parseInt(Cod) === 3){
                    Titulo = "Fiscalizar o Livro de Registro de Ocorrências";
                    Texto = "Com esta marca o usuário tem acesso a todos os registros do LRO. Não precisa ter a marca para preencher o LRO. <br>Se o usuário tiver esta marca poderá gerar PDF dos registros. <br>Nenhum registro pode ser editado.";
                }
                if(parseInt(Cod) === 4){
                    Titulo = "Acesso ao registro de Achados e Perdidos";
                    Texto = "Além desta marca é necessário que o usuário tenha o nível administrativo mínimo previsto em Parâmetros do Sistema.<br>Esta marca se aplica aos funcionários e voluntários da DAF, responsáveis pela guarda dos objetos encontrados. <br>Nos parâmetros do sistema pode ficar apontado o nível mínimo para inserção associado a esta marca. <br>Se o usuário estiver no nível administrativo para editar, poderá gerar PDF do processo completo.";
                }
                if(parseInt(Cod) === 5){
                    Titulo = "Manutenção dos Condicionadores de Ar";
                    Texto = "Somente esta marca dá acesso ao REGISTRO das visitas técnicas para manutenção preventiva ou corretiva dos aparelhos de Ar Condicionado.<br>Este módulo não é controlado por níveis administrativos.";
                }
                if(parseInt(Cod) === 6){
                    Titulo = "Fiscalizar a manutenção dos Condicionadores de Ar";
                    Texto = "Com esta marca o usuário tem acesso a todos os lançamentos das visitas técnicas para manutenção preventiva ou corretiva dos aparelhos de Ar Condicionado.<br>Não pode editar os lançamentos.<br>Este módulo não é controlado por níveis administrativos.";
                }
                if(parseInt(Cod) === 7){
                    Titulo = "Manutenção dos Elevadores";
                    Texto = "Somente esta marca dá acesso ao REGISTRO das visitas técnicas para manutenção preventiva ou corretiva dos Elevadores.<br>Este módulo não é controlado por níveis administrativos.";
                }
                if(parseInt(Cod) === 8){
                    Titulo = "Fiscalizar a manutenção dos Elevadores";
                    Texto = "Com esta marca o usuário tem acesso a todos os lançamentos das visitas técnicas para manutenção preventiva ou corretiva dos Elevadores.<br>Não pode editar os lançamentos.<br>Este módulo não é controlado por níveis administrativos.";
                }
                if(parseInt(Cod) === 9){
                    Titulo = "Escala dos Grupos";
//                    Texto = "A marca da esquerda indica que o usuário participa e pode visualizar a escala de serviço do grupo a que pertence.  <br>A marca da direita indica que o usuário é o escalante do grupo. <br>É necessário escolher o grupo ao lado. Os grupos podem ser criados e editados nos Parâmetros do Sistema.";
                    Texto = "Módulo em desenvolvimento. <br>Só o escalante pode visualizar a escala do grupo a que pertence.<br>É necessário escolher um grupo. <br>Os grupos podem ser criados e editados nos Parâmetros do Sistema.";
                }
                if(parseInt(Cod) === 10){
                    Titulo = "Escala dos Grupos";
                    Texto = "Esta marca permite o acesso a todas as escalas de serviço, para verificação. Não permite a edição.";
                }

                if(parseInt(Cod) === 11){
                    Titulo = "Claviculário da Portaria";
                    Texto = "Para os funcionários da portaria.<br>Esta marca permite registrar a entrega e a devolução das chaves do claviculário na Portaria.";
                }
                if(parseInt(Cod) === 12){
                    Titulo = "Claviculário da Portaria";
                    Texto = "Esta marca dá acesso aos usuários para retirar chaves do claviculário da Portaria. <br>É preciso estar marcado aqui para aparecer no cadastro.";
                }
                if(parseInt(Cod) === 13){
                    Titulo = "Claviculário da Portaria";
                    Texto = "Esta marca permite pleno acesso à edição do claviculário e dá acesso a inserir, modificar e apagar os registros de chaves do claviculário da Portaria.";
                }
                if(parseInt(Cod) === 14){
                    Titulo = "Contratos DAF";
                    Texto = "Esta marca permite colecionar e editar os contratos da casa como Contratante e Contratada.";
                }
                if(parseInt(Cod) === 15){
                    Titulo = "Contratos DAF";
                    Texto = "Esta marca permite apenas verificar e fiscalizar os contratos da casa como Contratante e Contratada. Não pode editar ou modificar.";
                }
                if(parseInt(Cod) === 16){
                    Titulo = "Leitura Hidrômetro";
                    Texto = "Esta marca permite apenas verificar e fiscalizar os registros do consumo de água. Não pode editar ou modificar.";
                }
                if(parseInt(Cod) === 17){
                    Titulo = "Leitura Eletricidade";
                    Texto = "Esta marca permite apenas verificar e fiscalizar os registros do consumo de eletricidade. Não pode editar ou modificar.";
                }
                if(parseInt(Cod) === 18){
                    Titulo = "Extintores";
                    Texto = "Esta marca permite gerenciar a disposição e manutenção dos extintores.";
                }
                if(parseInt(Cod) === 19){
                    Titulo = "Extintores";
                    Texto = "Esta marca permite apenas verificar e fiscalizar a disposição e manutenção dos extintores. Não pode editar ou modificar.";
                }
                if(parseInt(Cod) === 20){
                    Titulo = "Claviculário da Portaria";
                    Texto = "Apenas fiscalizar o funcionamento do claviculário da Portaria.";
                }
                document.getElementById("textoInfo").innerHTML = Texto;
                document.getElementById("textoTitulo").innerHTML = Titulo;
                document.getElementById("infomensagem").style.display = "block"; // está em modais.php
            }

            function mostraUsu(Valor){
                $("#faixacentral").load("modulos/config/jUsu.php?acao="+Valor);
            }

            function carregaHelp(){
                document.getElementById("relacHelp").style.display = "block";
            }
            function fechaHelp(){
                document.getElementById("relacHelp").style.display = "none";
            }
        </script>
    </head>
    <body>
        <?php
            $rsSis = pg_query($Conec, "SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_schema = 'cesb' And TABLE_NAME = 'poslog'");
            $rowSis = pg_num_rows($rsSis);
            if($rowSis == 0){
                echo "Sem contato com os arquivos do sistema. Informe à ATI.";
                return false;
            }
            if(isset($_REQUEST["tipo"])){
                $Tipo = $_REQUEST["tipo"];
            }else{
                $Tipo = 1;
            }
            $rs = pg_query($Conec, "SELECT column_name, data_type, character_maximum_length FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'poslog'");
            $row = pg_num_rows($rs);
            if($row == 0){
                $Erro = 1;
                echo "Faltam tabelas. Informe à ATI.";
                return false;
            }

            require_once("modais.php");
            //Para carregar os select de dia e mês
            $OpcoesMes = pg_query($Conec, "SELECT esc1 FROM ".$xProj.".escolhas WHERE codesc < 14 ORDER BY esc1");
            $OpcoesDia = pg_query($Conec, "SELECT esc1 FROM ".$xProj.".escolhas ORDER BY esc1");

            $Menu1 = escMenu($Conec, $xProj, 1); //abre alas
            $Menu2 = escMenu($Conec, $xProj, 2); 
            $Menu3 = escMenu($Conec, $xProj, 3);
            $Menu4 = escMenu($Conec, $xProj, 4);
            $Menu5 = escMenu($Conec, $xProj, 5);
            $Menu6 = escMenu($Conec, $xProj, 6); 
            if($_SESSION["AdmUsu"] == 7){
                $OpcoesAdm = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 Or adm_fl = 7 ORDER BY adm_fl");
            }else{
                $OpcoesAdm = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE ativo = 1 ORDER BY adm_fl");
            }
            $OpcoesSetor = pg_query($Conec, "SELECT CodSet, siglasetor FROM ".$xProj.".setores ORDER BY siglasetor");
            $OpcoesEscala = pg_query($Conec, "SELECT id, siglagrupo FROM ".$xProj.".escalas_gr ORDER BY siglagrupo");
            $OpExecTarefa = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl");

            $EscChave = parAdm("esc_chaves1", $Conec, $xProj); // marca para aparecer/ocultar escolha de chaves a retirar por usuário 
        ?>

        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="guardaSiglaSetor" value="<?php echo addslashes($_SESSION["SiglaSetor"]) ?>" />
        <input type="hidden" id="guardaCodSetor" value="<?php echo addslashes($_SESSION["CodSetorUsu"]) ?>" />
        <input type="hidden" id="guardaid_click" value="0" />
        <input type="hidden" id="guardaid_cpf" value="0" />
        <input type="hidden" id="mudou" value="0" /> <!-- valor 1 quando houver mudança em qualquer campo do modal -->
        <input type="hidden" id="guarda_usulogado_id" value="<?php echo $_SESSION["usuarioID"]; ?>" />
        <input type="hidden" id="guardausu_cpf" value="<?php echo $_SESSION["usuarioCPF"]; ?>" />
        <input type="hidden" id="guardaidpessoa" value="0" />
        <input type="hidden" id="guardaAtiv" value="1" />
        <input type="hidden" id="guardaLro" value="0" />
        <input type="hidden" id="guardaBens" value="0" />
        <input type="hidden" id="guardaEscolheChaves" value="<?php echo $EscChave; ?>" />
        

        <div style="margin: 20px; border: 2px solid blue; border-radius: 15px; padding: 20px; min-height: 200px;">
            <div class="box" style="position: relative; float: left; width: 33%;">
                <input type="button" id="botinserir" class="resetbot" value="Inserir Novo" onclick="insUsu();">
                <label style="padding-left: 20px;"></label>
                <button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="resumoUsu();">PDF</button>
                <label style="padding-left: 30px;"></label>
                <img src="imagens/iinfo.png" height="20px;" id="imgInfo" style="cursor: pointer;" onclick="carregaHelp();" title="Guia rápido">
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: center;">
                <h3>Usuários Cadastrados</h3>
                <button class="resetbot" style="font-size: .9rem;" onclick="mostraUsu('todos');">Todos</button>
                <button class="resetbot" style="font-size: .9rem;" onclick="mostraUsu('online');">On Line</button>
                <button class="resetbot" style="font-size: .9rem;" onclick="mostraUsu('dehoje');">Usuários de Hoje</button>
                <button class="resetbot" style="font-size: .9rem;" onclick="mostraUsu('inativos');">Bloqueados</button>
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: left;"></div>

            <div id="faixacentral"></div>

        </div>

        <!-- div modal para edição  -->
        <div id="relacmodalUsu" class="relacmodal">  <!-- ("close")[0] -->
            <div class="modal-content-Usu">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h3 id="titulomodal" style="text-align: center; color: #666;">Edição de Usuários</h3>
                <div id="mensagemCima" style="text-align: center; color: red; font-weight: bold;"></div>
                <table style="margin: 0 auto; width: 90%">
                    <tr>
                        <td id="etiqNomelog" class="etiq80">Login:</td>
<!--                        <td><input type="text" disabled id="usulogin" style="text-align: center;" placeholder="Login" onchange="checaEntrada();" onkeypress="if(event.keyCode===13){javascript:foco('salvar');return false;}"></td> -->
                        <td><input type="text" disabled id="usulogin" style="text-align: center;" placeholder="Login" onchange="checaEntrada();" ></td>
                        <td></td>
                        <td></td>
                        <td colspan="2" style="text-align: right;">
                            <label class="etiq80">Último Acesso: </label>
                            <input type="text" disabled id="ultlog" style="text-align: center; font-size: .8rem;">
                        </td>
                    </tr>
                    <tr>
                        <td id="etiqNome" class="etiq80">Nome Usual</td>
                        <td><input type="text" id="usuarioNome" placeholder="Nome usual" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('nomecompl');return false;}"></td>
                        <td></td>
                        <td></td>
                        <td colspan="2" style="text-align: right;">
                            <label class="etiq80">Nº acessos: </label>
                            <input type="text" disabled id="acessos" style="text-align: center; font-size: .8rem; width: 100px;">
                        </td>
                    </tr>
                    <tr>
                        <td id="etiqNomeCompl" class="etiq80">Nome Completo</td>
                        <td style="width: 50%;"><input type="text" disabled id="nomecompl" style="width: 100%;" placeholder="Nome completo" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('usulogin');return false;}"></td>
                        <td></td>
                        <td></td>
                        <td colspan="2" style="text-align: right;">
                            <label style="font-size: 12px;" title="Ativo ou inativo">Situação: </label>
                            <input type="radio" name="atividade" id="atividade1" value="1" title="Ativo no sistema" onclick="salvaAtiv(value);"><label for="atividade1" style="font-size: 12px; padding-left: 3px;"> Ativo</label>
                            <input type="radio" name="atividade" id="atividade2" value="0" title="Bloqueado" onclick="salvaAtiv(value);"><label for="atividade2" style="font-size: 12px; padding-left: 3px;"> Bloqueado</label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiq80">Setor de Trabalho</td>
                        <td>
                            <select id="setor" style="font-size: 1rem;" title="Selecione um local de trabalho." onchange="mudaSetor();">
                            <?php 
                            if($OpcoesSetor){
                                while ($Opcoes = pg_fetch_row($OpcoesSetor)){ ?>
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
                        <td style="text-align: right;"><button disabled id="ressetsenha" class="resetbot" style="font-size: .9rem;" onclick="resetSenha();">Reiniciar Senha</button></td> <!-- https://www.dicionarioinformal.com.br/ressetar/ -->
                    </tr>
                    <tr>
                        <td class="etiq80">Nível Administrativo</td>
                        <td>
                            <select id="flAdm" style="font-size: 1rem;" title="Selecione o nível administrativo do usuário." onchange="modif();">
                            <?php 
                            if($OpcoesAdm){
                                while ($Opcoes = pg_fetch_row($OpcoesAdm)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                        <td colspan="4" style="text-align: right;">
                            <label class="etiq80">Aniversário: -Dia: </label>
                            <input type="text" disabled id="diaAniv" style="text-align: center; font-size: .8rem; width: 25px;">
                            <label class="etiq80"> -Mês: </label>
                            <input type="text" disabled id="mesAniv" style="text-align: center; font-size: .8rem; width: 25px;">
                            <label style="padding-left: 3px;"></label>
                            <input type="checkbox" id="mostrarAniver" title="Mostrar aniversário na página inicial" onchange="modif();"  >
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6"><hr style="margin: 3px; padding: 2px;"></td>
                    </tr>
                </table>

                <table style="margin: 0 auto; width: 95%">
                    <tr>
                        <td class="etiq" title="Pode registrar ocorrências no LRO">Preenchar o LRO:</td>
                        <td colspan="4">
                            <input type="checkbox" id="preencheLro" title="Registrar ocorrências no LRO" onchange="modif();" onclick="acertaMarca()" >
                            <label for="preencheLro" title="Registrar ocorrências no LRO">preencher o Livro de Registro de Ocorrências</label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(1);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" style="border-bottom: 1px solid;" title="Fiscaliza os registros de ocorrências no LRO">Administrar LRO:</td>
                        <td colspan="4" style="padding-left: 20px; border-bottom: 1px solid;">
                            <input type="checkbox" id="fiscalizaLro" title="Fiscalizar os registros de ocorrências no LRO - Só fiscaliza. Não preenche o LRO" onchange="modif();" >
                            <label for="fiscalizaLro" title="Fiscalizar os registros de ocorrências no LRO - Só fiscaliza. Não preenche o LRO">fiscalizar o Livro de Registro de Ocorrências</label>
                        </td>
                        <td style="text-align: center; border-bottom: 1px solid;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(3);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" title="Registrar recebimento e destino de Achados e Perdidos">Bens Achados:</td>
                        <td colspan="4">
                            <input type="checkbox" id="preencheBens" title="Registrar recebimento e destino de Achados e Perdidos" onchange="modif();" >
                            <label for="preencheBens" title="Registrar recebimento e destino de Achados e Perdidos">acesso ao Registro e Destino de Achados e Perdidos</label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(4);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" title="Apenas registrar o recebimento de Achados e Perdidos">Bens Achados:</td>
                        <td colspan="4" style="padding-left: 20px;">
                            <input type="checkbox" id="soPreencheBens" title="Apenas registrar recebimento de Achados e Perdidos" onchange="modif();" >
                            <label for="soPreencheBens" title="Apenas registrar recebimento de Achados e Perdidos">apenas registrar Achados e Perdidos</label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(4);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" style="border-bottom: 1px solid;" title="Fiscalizar os registros de Achados e Perdidos - Só fiscaliza. Não pode registrar os Achados e Perdidos.">Bens Achados:</td>
                        <td colspan="4" style="padding-left: 20px; border-bottom: 1px solid;">
                            <input type="checkbox" id="fiscBens" title="Fiscalizar os registros de Achados e Perdidos - Só fiscaliza. Não pode registrar os Achados e Perdidos." onchange="modif();" >
                            <label for="fiscBens" title="Fiscalizar os registros de Achados e Perdidos">fiscalizar os registros de Achados e Perdidos</label>
                        </td>
                        <td style="text-align: center; border-bottom: 1px solid;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(4);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" title="Pode registrar as leituras diárias do consumo de água">Leitura Água:</td>
                        <td colspan="4">
                            <input type="checkbox" id="leituraAgua" title="Pode registrar as leituras diárias do consumo de água" onchange="modif();" >
                            <label for="leituraAgua" title="Pode registrar as leituras diárias do consumo de água">registrar leitura diária do Hidrômetro</label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(2);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" style="border-bottom: 1px solid;" title="Ver os registros de leituras do consumo de água - Só fiscaliza. Não pode registrar.">Hidrômetro:</td>
                        <td colspan="4" style="padding-left: 20px; border-bottom: 1px solid;">
                            <input type="checkbox" id="fisc_Agua" title="Visualizar os registros de leitura do hidrômetro - Só fiscaliza. Não pode registrar." onchange="modif();" >
                            <label for="fisc_Agua" title="Visualizar os registros de leitura do hidrômetro">acompanhar e fiscalizar as leituras do hidrômetro</label>
                        </td>
                        <td style="text-align: center; border-bottom: 1px solid;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(16);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" title="Pode registrar as leituras diárias do consumo de eletricidade">Energia Elétrica:</td>
                        <td colspan="4">
                            <input type="checkbox" id="leituraEletric" title="Pode registrar as leituras diárias do consumo de energia elétrica" onchange="modif();" >
                            <label for="leituraEletric" title="Pode registrar as leituras diárias do consumo de energia elétrica">registrar leitura do Medidor de Energia Elétrica - <?php echo $Menu1; ?></label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(2);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" title="Pode registrar as leituras diárias do consumo de eletricidade">Energia Elétrica:</td>
                        <td colspan="4">
                            <input type="checkbox" id="leituraEletric2" title="Pode registrar as leituras diárias do consumo de energia elétrica" onchange="modif();" >
                            <label for="leituraEletric2" title="Pode registrar as leituras diárias do consumo de energia elétrica do medidor da operadora ">registrar leitura do Medidor de Energia Elétrica - <?php echo $Menu2; ?></label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(2);" title="Guia rápido"></td>
                    </tr>
                    <tr>
                        <td class="etiq" title="Pode registrar as leituras diárias do consumo de eletricidade">Energia Elétrica:</td>
                        <td colspan="4">
                            <input type="checkbox" id="leituraEletric3" title="Pode registrar as leituras diárias do consumo de energia elétrica" onchange="modif();" >
                            <label for="leituraEletric3" title="Pode registrar as leituras diárias do consumo de energia elétrica do medidor da operadora">registrar leitura do Medidor de Energia Elétrica - <?php echo $Menu3; ?></label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(2);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" style="border-bottom: 1px solid;" title="Ver os registros de leituras do consumo de eletricidade - Só fiscaliza. Não pode registrar.">Eletricidade:</td>
                        <td colspan="4" style="padding-left: 20px; border-bottom: 1px solid;">
                            <input type="checkbox" id="fisc_Eletric" title="Visualizar os registros dos medidores do consumo de eletricidade - Só fiscaliza. Não pode registrar." onchange="modif();" >
                            <label for="fisc_Eletric" title="Visualizar os registros dos medidores do consumo de eletricidade">acompanhar e fiscalizar as leituras dos Medidores de Energia Elétrica</label>
                        </td>
                        <td style="text-align: center; border-bottom: 1px solid;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(17);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" title="Registrar as visitas técnicas da empresa contratada para manutenção dos Condicionadores de Ar">Condicionadores de Ar:</td>
                        <td colspan="4">
                            <input type="checkbox" id="registroArCond" title="Registrar as visitas técnicas da empresa de Ar Condicionado" onchange="modif();" >
                            <label for="registroArCond" title="Registrar as visitas técnicas da empresa contratada para manutenção dos Condicionadores de Ar">registrar Manutenção dos Condicionadores de Ar - <?php echo $Menu4; ?></label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(5);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" title="Registrar as visitas técnicas da empresa contratada para manutenção dos Condicionadores de Ar">Condicionadores de Ar:</td>
                        <td colspan="4">
                            <input type="checkbox" id="registroArCond2" title="Registrar as visitas técnicas da empresa de Ar Condicionado" onchange="modif();" >
                            <label for="registroArCond2" title="Registrar as visitas técnicas da empresa contratada para manutenção dos Condicionadores de Ar">registrar Manutenção dos Condicionadores de Ar - <?php echo $Menu5; ?></label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(5);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" title="Registrar as visitas técnicas da empresa contratada para manutenção dos Condicionadores de Ar">Condicionadores de Ar:</td>
                        <td colspan="4">
                            <input type="checkbox" id="registroArCond3" title="Registrar as visitas técnicas da empresa de Ar Condicionado" onchange="modif();" >
                            <label for="registroArCond3" title="Registrar as visitas técnicas da empresa contratada para manutenção dos Condicionadores de Ar">registrar Manutenção dos Condicionadores de Ar - <?php echo $Menu6; ?></label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(5);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" style="border-bottom: 1px solid;" title="Fiscalizar a manutenção dos Condicionadores de Ar">Condicionadores de Ar:</td>
                        <td colspan="4" style="padding-left: 20px; border-bottom: 1px solid;">
                            <input type="checkbox" id="fiscalArCond" title="Fiscalizar a manutenção dos Condicionadores de Ar" onchange="modif();" >
                            <label for="fiscalArCond" title="Fiscalizar a manutenção dos Condicionadores de Ar">fiscalizar a Manutenção dos Condicionadores de Ar</label>
                        </td>
                        <td style="text-align: center; border-bottom: 1px solid;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(6);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" title="Registrar as visitas técnicas da empresa contratada para manutenção dos Elevadores">Elevadores:</td>
                        <td colspan="4">
                            <input type="checkbox" id="registroElev" title="Registrar as visitas técnicas da empresa de Elevadores" onchange="modif();" >
                            <label for="registroElev" title="Registrar as visitas técnicas da empresa contratada para manutenção dos Elevadores">registrar Manutenção dos Elevadores</label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(7);" title="Guia rápido"></td>
                    </tr>
                    <tr>
                        <td class="etiq" style="border-bottom: 1px solid;" title="Fiscalizar a manutenção dos Elevadoresr">Elevadores:</td>
                        <td colspan="4" style="padding-left: 20px; border-bottom: 1px solid;">
                            <input type="checkbox" id="fiscalElev" title="Fiscalizar a manutenção dos Elevadores" onchange="modif();" >
                            <label for="fiscalElev" title="Fiscalizar a manutenção dos Elevadores">fiscalizar a Manutenção dos Elevadores</label>
                        </td>
                        <td style="text-align: center; border-bottom: 1px solid;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(8);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" style="padding-top: 5px; border-bottom: 1px solid;" title="Faz parte do efetivo da escala">Escala de Serviço:</td>
                        <td colspan="4" style="border-bottom: 1px solid; padding-bottom: 5px;">
<!--                            <input type="checkbox" id="escalaEft" title="Faz parte do efetivo da escala" onchange="modif();" >
                            <label for="escalaEft" style="padding-right: 10px; padding-top: 5px;" title="Faz parte do efetivo da escala">Efetivo da Escala:</label>
 -->
                            <label style="padding-left: 10px; padding-right: 10px; padding-top: 5px;" title="Faz parte do efetivo da escala">Efetivo da Escala:</label>
                            <select id="grupoEscala" style="font-size: 1rem; width: 180px;" title="Selecione um grupo." onchange="modif();">
                            <option value="0"></option>
                            <?php 
                            if($OpcoesEscala){
                                while ($Opcoes = pg_fetch_row($OpcoesEscala)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>                            
                            <label for="escalante" style="padding-left: 10px; padding-top: 5px;" title="Confecciona a escala">Escalante:</label>
                            <input type="checkbox" id="escalante" title="Confecciona a escala do grupo" onchange="modif();" >
                            <label for="fiscalEscalas" style="padding-left: 10px; padding-top: 5px;" title="Fiscalizar as escalas de serviço">Fiscalizar</label>
                            <input type="checkbox" id="fiscalEscalas" title="Fiscalizar as escalas de serviço" onchange="modif();" >

                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(9);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" title="Registrar a entrega e devolução das chaves do claviculário da Portaria">Claviculário Portaria: </td>
                        <td colspan="4">
                            <input type="checkbox" id="registroChaves" title="Registrar a entrega e devolução das chaves do claviculário da Portaria" onchange="modif();" >
                            <label for="registroChaves" title="Registrar a entrega e devolução das chaves do claviculário da Portaria">registrar a entrega e devolução das chaves do claviculário na Portaria</label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(11);" title="Guia rápido"></td>
                    </tr>
                    <tr>
                        <td class="etiq" title="Editar, inserir e apagar as chaves do claviculário da Portaria">Claviculário Portaria:</td>
                        <td colspan="4">
                            <input type="checkbox" id="editChaves" title="Fiscalizar a entrega e devolução das chaves do claviculário da Portaria" onchange="modif();" >
                            <label for="editChaves" title="Editar, inserir e apagar as chaves do claviculário da Portaria">inserir, editar e apagar chaves do claviculário da Portaria</label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(13);" title="Guia rápido"></td>
                    </tr>
                    <tr>
                        <td class="etiq" title="Fiscalizar a entrega e devolução das chaves do claviculário da Portaria">Claviculário Portaria:</td>
                        <td colspan="4">
                            <input type="checkbox" id="fiscalChaves" title="Fiscalizar a entrega e devolução das chaves do claviculário da Portaria" onchange="modif();" >
                            <label for="fiscalChaves" title="Fiscalizar e editar as chaves do claviculário da Portaria">fiscalizar o funcionamento do claviculário da Portaria</label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(20);" title="Guia rápido"></td>
                    </tr>
                    <tr>
                        <td class="etiq" style="border-bottom: 1px solid;" title="Autorizado a retirar chaves do claviculário da Portaria">Claviculário Portaria:</td>
                        <td colspan="4" style="padding-left: 20px; border-bottom: 1px solid;">
                            <input type="checkbox" id="retiraChave" title="Autorizado a retirar chaves do claviculário da Portaria" onchange="mostraBotChave(this, <?php echo $EscChave; ?>);" >
                            <label for="retiraChave" title="Autorizado a retirar chaves do claviculário da Portaria">usuário autorizado a retirar chaves do claviculário da Portaria</label>
                            <label style="padding-left: 5px;"></label>
                            <!-- Há um gatilho em paramsis para requerer ou interromper a exigência de determinar qual chave um usuário pode pegar -->
<!--                            <button id="botaoChaves" class="botpadrblue" style="font-size: 70%;" onclick="AbreModalChaves();" title="Definir quais chaves este usuário pode pegar.">Chaves</button> -->
                            <button id="botaoChaves" <?php if($EscChave == 1){echo "class='botpadrTijolo'";}else{echo "class='botpadrblue'";} ?> style="font-size: 70%;" onclick="AbreModalChaves();" <?php if($EscChave == 1){echo "title='Vínculo ligado - Definir quais chaves este usuário pode pegar.'";}else{echo "title='Vínculo desligado - Definir quais chaves este usuário pode pegar.'";} ?> >Chaves</button>
                        </td>
                        <td style="text-align: center; border-bottom: 1px solid;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(12);" title="Guia rápido"></td>
                    </tr>

                    <?php
                    if($_SESSION["usuarioCPF"] == "13652176049"){
                        ?>
                    <tr>
                        <td class="etiq" style="border-bottom: 1px solid;" title="Pode resolver as tarefas">Tarefas</td>
                        <td colspan="4" style="padding-left: 20px; border-bottom: 1px solid;">
                            <label for="exectarefa">Pode executar minhas tarefas: </label>
                            <select id="exectarefa" style="font-size: 1rem;" title="Selecione um usuário." onchange="insExecTarefa();">
                            <option value="0"></option>
                            <?php 
                            if($OpExecTarefa){
                                while ($Opcoes = pg_fetch_row($OpExecTarefa)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                        <td style="text-align: center; border-bottom: 1px solid;"></td>
                    </tr>

                    <?php
                    }
                    ?>

                    <tr>
                        <td class="etiq" title="Acompanhar e colecionar contratos da casa como Contratado ou Contratante.">Contratos DAF</td>
                        <td colspan="4">
                            <input type="checkbox" id="insContrato" title="Acompanhar e colecionar contratos da casa como Contratado ou Contratante." onchange="modif();" >
                            <label for="insContrato" title="Acompanhar e colecionar contratos da casa como Contratado ou Contratante.">registrar, editar e gerenciar os contratos da casa</label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(14);" title="Guia rápido"></td>
                    </tr>
                    <tr>
                        <td class="etiq" style="border-bottom: 1px solid;" title="Gerenciar os contratos da casa como Contratante ou Contratada.">Contratos DAF:</td>
                        <td colspan="4" style="padding-left: 20px; border-bottom: 1px solid;">
                            <input type="checkbox" id="fiscalContrato" title="Acompanhar e fiscalizar os contratos da casa como Contratante ou Contratada." onchange="modif();" >
                            <label for="fiscalContrato" title="Acompanhar e fiscalizar os contratos da casa como Contratante ou Contratada.">fiscalizar e acompanhar os contratos da casa</label>
                        </td>
                        <td style="text-align: center; border-bottom: 1px solid;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(15);" title="Guia rápido"></td>
                    </tr>


                    <tr>
                        <td class="etiq" title="Gerenciar a disposição e manutenção dos extintores.">Extintores:</td>
                        <td colspan="4">
                            <input type="checkbox" id="insExtintor" title="Gerenciar a disposição e manutenção dos extintores." onchange="modif();" >
                            <label for="insExtintor" title="Gerenciar a disposição e manutenção dos extintores.">gerenciar a disposição e manutenção dos extintores de incêndio</label>
                        </td>
                        <td style="text-align: center;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(18);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td class="etiq" style="border-bottom: 1px solid;" title="Acompanhar e fiscalizar a disposição e manutenção dos extintores.">Extintores:</td>
                        <td colspan="4" style="padding-left: 20px; border-bottom: 1px solid;">
                            <input type="checkbox" id="fiscalExtintor" title="Acompanhar e fiscalizar a disposição e manutenção dos extintores." onchange="modif();" >
                            <label for="fiscalExtintor" title="Acompanhar e fiscalizar a disposição e manutenção dos extintores.">fiscalizar e acompanhar a manutenção dos extintores de incêndio</label>
                        </td>
                        <td style="text-align: center; border-bottom: 1px solid;"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpUsu(19);" title="Guia rápido"></td>
                    </tr>

                    <tr>
                        <td colspan="6"><hr style="margin: 3px; padding: 2px;"></td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align: center;"><div id="mensagem" style="color: red; font-weight: bold;"></div></td>
                    </tr>
                    <tr>
                        <td class="etiq" style="color: red; text-align: left;"></td> 
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right; padding-right: 30px;"><input type="button" class="resetbot" id="salvar" value="Salvar" onclick="salvaModal();"></td>
                    </tr>
                </table>
           </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para escolher chaves por usuário  -->
        <div id="relacmodalChaves" class="relacmodal"> 
            <div class="modal-content-Chaves">
                <span class="close" onclick="fechaModalChaves();">&times;</span>
                <div style="text-align: center;"><h6>Claviculário da Portaria</h6></div>
                <div id="nomeUsuChaves" style="text-align: center;"></div>
                <div style="text-align: center; font-size: 80%;">Se nenhum dia da Semana estiver marcado pode pegar a chave em qualquer dia. <br>Se um ou mais dias da semana estiverem marcados só pode pegar a chave naquele(s) dia(s) marcado(s).</div>
                <div id="faixachaves"></div>
            </div>
        </div> <!-- Fim Modal-->

       <!-- div modal para instruções -->
        <div id="relacHelp" class="relacmodal">
            <div class="modalMsg-content">
                <span class="close" onclick="fechaHelp();">&times;</span>
                <h4 style="text-align: center; color: #666;">Informações</h4>
                <h5 style="text-align: center; color: #666;">Cadastro de Usuários</h5>
                <div style="color: #000000; border: 1px solid; border-radius: 10px; margin: 5px; padding: 5px;">
                    Regras inseridas:
                    <ul>
                        <li>01 - Quando um usuário é bloqueado vai para a relação de Bloqueados, onde fica inativo por 180 dias até desaparecer da relação de Bloqueados.</li>
                        <li>02 - Quando um funcionário tem seu contrato encerrado, o sistema o bloqueia e ele permanece inativo por 180 dias. Os escalantes o retirarão de seu grupo nas escalas de serviço dos meses subsequentes à demissão.</li>
                        <li>03 - Depois dos 180 dias ele não será mais visível porém poderá ser visto nas escalas de serviço anteriores ao término do seu contrato. Se um novo contrato for assinado ele voltará à condição de usuário ativo.</li>
                    </ul>
                </div>
            </div>
        </div>  <!-- Fim Modal Help-->

    </body>
</html>