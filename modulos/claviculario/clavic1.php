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
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="comp/js/jquery-confirm.min.js"></script> <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="comp/js/jquery.mask.js"></script>
        <style>
            .quadrinho {
                font-size: 90%;
                min-width: 40px;
                min-height: 23px;
                border: 1px solid #C0C0C0;
                border-radius: 3px;
                padding-left: 5px;
                padding-right: 5px;
            }
            .quadrinhoClick {
                font-size: 90%;
                min-width: 50px;
                border: 1px solid;
                border-radius: 3px;
                padding-left: 5px;
                padding-right: 5px;
                cursor: pointer;
            }
            .quadrgrupo {
                font-size: 90%;
                min-width: 33px;
                border: 1px solid #C0C0C0;
                border-radius: 3px;
                padding-left: 5px;
                padding-right: 5px;
            }
            .quadrlista {
                position: relative;
                float: left;
                margin-left: 2px;
                font-size: 80%;
                min-height: 23px;
                border: 1px solid #C0C0C0;
                border-radius: 3px;
                padding-left: 2px;
                padding-right: 2px;
                text-align: left;
            }
            .quadrnomelista {
                margin-left: 2px;
                font-size: 80%;
                min-height: 23px;
                border: 1px solid #C0C0C0;
                border-radius: 3px;
                padding-left: 2px;
                padding-right: 2px;
                text-align: left;
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
                document.getElementById("botinserir").style.visibility = "hidden";
                document.getElementById("botimpr").style.visibility = "hidden";
                if(parseInt(document.getElementById("registrachaves").value) === 1 || parseInt(document.getElementById("editachaves").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){
                    $("#faixacentral").load("modulos/claviculario/jChave1.php?acao=todos");
                    $("#faixamostra").load("modulos/claviculario/kChave1.php?acao=todos");
                    if(parseInt(document.getElementById("editachaves").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){ 
                        document.getElementById("botinserir").style.visibility = "visible";
                        document.getElementById("botimpr").style.visibility = "visible";
                    }
                }else{
                    document.getElementById("faixaMensagem").style.display = "block";
                }

                $("#cpfsolicitante").mask("999.999.999-99");
                $("#cpfentregador").mask("999.999.999-99");
                $("#resulttelef").mask("(61) 9 9999-9999");
                $("#voltatelef").mask("(61) 9 9999-9999");


                $("#selecSolicitante").change(function(){
                    document.getElementById("cpfsolicitante").value = "";
                    document.getElementById("guardaCPF").value = "";
                    document.getElementById("guardaPosCod").value = document.getElementById("selecSolicitante").value;
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/claviculario/salvaChave.php?acao=buscalog&codigo="+document.getElementById("selecSolicitante").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("resultsolicitante").innerHTML = Resp.nomecompl;
                                        document.getElementById("guardaCPF").value = Resp.cpf;
                                        document.getElementById("resultcpf").innerHTML = format_CnpjCpf(Resp.cpf);
                                        document.getElementById("resultsetor").innerHTML = Resp.siglasetor;
                                        document.getElementById("resulttelef").value = Resp.telef;
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });

                $("#cpfsolicitante").click(function(){
                    document.getElementById("resultsolicitante").innerHTML = "";
                    document.getElementById("guardaCPF").value = "";
                    document.getElementById("resultcpf").innerHTML = "";
                    document.getElementById("resultsetor").innerHTML = "";
                    document.getElementById("resulttelef").value = "";
                    document.getElementById("guardaPosCod").value = "";
                });
                $("#cpfsolicitante").change(function(){
                    document.getElementById("selecSolicitante").value = "";
                    document.getElementById("guardaCPF").value = "";
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/claviculario/salvaChave.php?acao=buscacpf&cpf="+encodeURIComponent(document.getElementById("cpfsolicitante").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("resultsolicitante").innerHTML = Resp.nomecompl;
                                        document.getElementById("guardaCPF").value = Resp.cpf;
                                        document.getElementById("resultcpf").innerHTML = format_CnpjCpf(Resp.cpf);
                                        document.getElementById("resultsetor").innerHTML = Resp.siglasetor;
                                        document.getElementById("guardaPosCod").value = Resp.PosCod;

                                    }
                                    if(parseInt(Resp.coderro) === 3){
                                        document.getElementById("resultsolicitante").innerHTML = "Usuário não está autorizado a retirar chaves.";
                                        document.getElementById("cpfsolicitante").focus();
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("resultsolicitante").innerHTML = "Nada foi encontrado.";
                                        document.getElementById("cpfsolicitante").focus();
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

                $("#selecEntregador").change(function(){
                    document.getElementById("cpfentregador").value = "";
                    document.getElementById("guardaCPF").value = "";
                    document.getElementById("guardaPosCod").value = document.getElementById("selecEntregador").value;
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/claviculario/salvaChave.php?acao=buscalog&codigo="+document.getElementById("selecEntregador").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("voltasolicitante").innerHTML = Resp.nomecompl;
                                        document.getElementById("guardaCPF").value = Resp.cpf;
                                        document.getElementById("voltacpf").innerHTML = format_CnpjCpf(Resp.cpf);
                                        document.getElementById("voltasetor").innerHTML = Resp.siglasetor;
                                        document.getElementById("voltatelef").value = Resp.telef;
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                });
                $("#cpfentregador").click(function(){
                    document.getElementById("voltasolicitante").innerHTML = "";
                    document.getElementById("guardaCPF").value = "";
                    document.getElementById("voltacpf").innerHTML = "";
                    document.getElementById("voltasetor").innerHTML = "";
                    document.getElementById("voltatelef").value = "";
                    document.getElementById("guardaPosCod").value = "";
                });
                $("#cpfentregador").change(function(){
                    document.getElementById("selecEntregador").value = "";
                    document.getElementById("guardaCPF").value = "";
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/claviculario/salvaChave.php?acao=buscacpf&cpf="+encodeURIComponent(document.getElementById("cpfentregador").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("voltasolicitante").innerHTML = Resp.nomecompl;
                                        document.getElementById("guardaCPF").value = Resp.cpf;
                                        document.getElementById("voltacpf").innerHTML = format_CnpjCpf(Resp.cpf);
                                        document.getElementById("voltasetor").innerHTML = Resp.siglasetor;
                                        document.getElementById("voltatelef").value = Resp.telef;
                                        document.getElementById("guardaPosCod").value = Resp.PosCod;
                                    }
                                    if(parseInt(Resp.coderro) === 3){
                                        document.getElementById("voltasolicitante").innerHTML = "Usuário não está autorizado a retirar chaves.";
                                        document.getElementById("guardaCPF").value = "";
                                        document.getElementById("voltasetor").innerHTML = "";
                                        document.getElementById("voltatelef").value = "";
                                        document.getElementById("guardaPosCod").value = ""
                                        document.getElementById("cpfentregador").focus();
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("voltasolicitante").innerHTML = "Nada foi encontrado";
                                        document.getElementById("guardaCPF").value = "";
                                        document.getElementById("voltasetor").innerHTML = "";
                                        document.getElementById("voltatelef").value = "";
                                        document.getElementById("guardaPosCod").value = ""
                                        document.getElementById("cpfentregador").focus();
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

            });
            function insChave(){
                document.getElementById("guardaCod").value = 0;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave.php?acao=buscaNumero", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("numchave").value = Resp.chavenum;
                                    document.getElementById("localchave").value = "";
                                    document.getElementById("salachave").value = "";
                                    document.getElementById("complemchave").value = "";
                                    document.getElementById("obschave").value = "";
                                    document.getElementById("editaModalChave").style.display = "block";
                                    document.getElementById("localchave").focus();
                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function editaChave(Cod){
                document.getElementById("guardaCod").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave.php?acao=buscaChave&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("numchave").value = Resp.chavenum;
                                    document.getElementById("localchave").value = Resp.chavelocal;
                                    document.getElementById("salachave").value = Resp.chavesala;
                                    document.getElementById("complemchave").value = Resp.chavenumcompl;
                                    document.getElementById("obschave").value = Resp.chaveobs;
                                    document.getElementById("editaModalChave").style.display = "block";
                               }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function salvaEditChave(){
                if(document.getElementById("mudou").value != "0"){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/claviculario/salvaChave.php?acao=salvaChave&codigo="+document.getElementById("guardaCod").value
                        +"&numchave="+document.getElementById("numchave").value
                        +"&complemchave="+document.getElementById("complemchave").value
                        +"&salachave="+document.getElementById("salachave").value
                        +"&localchave="+document.getElementById("localchave").value
                        +"&obschave="+document.getElementById("obschave").value
                        , true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.");
                                    }else{
                                        document.getElementById("editaModalChave").style.display = "none";
                                        $("#faixacentral").load("modulos/claviculario/jChave1.php?acao=todos");
                                        $("#faixamostra").load("modulos/claviculario/kChave1.php?acao=todos");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("editaModalChave").style.display = "none";
                }
            }

            function saidaChave(Cod){
                document.getElementById("guardaCod").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave.php?acao=buscaChave&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("sainumchave").value = Resp.chavenum;
                                    document.getElementById("sailocalchave").value = Resp.chavelocal;
                                    document.getElementById("saisalachave").value = Resp.chavesala;
                                    document.getElementById("saicomplemchave").value = Resp.chavenumcompl;
                                    document.getElementById("saiobschave").value = Resp.chaveobs;
                                    document.getElementById("registroRetiradaChave").style.display = "block";

                                    document.getElementById("selecSolicitante").value = "";
                                    document.getElementById("cpfsolicitante").value = "";
                                    document.getElementById("resultsolicitante").innerHTML = "";
                                    document.getElementById("resulttelef").value = "";
                                    document.getElementById("guardaCPF").value = "";
                                    document.getElementById("resultcpf").innerHTML = "";
                                    document.getElementById("resultsetor").innerHTML = "";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function entregaChave(){
                if(document.getElementById("guardaCPF").value == ""){
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave.php?acao=entregaChave&codigo="+document.getElementById("guardaCod").value
                    +"&cpf="+document.getElementById("resultcpf").innerHTML 
                    +"&celular="+document.getElementById("resulttelef").value 
                    +"&poscod="+document.getElementById("guardaPosCod").value 
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("registroRetiradaChave").style.display = "none";  
                                    $("#faixacentral").load("modulos/claviculario/jChave1.php?acao=todos");
                                    $("#faixamostra").load("modulos/claviculario/kChave1.php?acao=todos");                                 
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function retornoChave(Cod){
                document.getElementById("guardaCod").value = Cod; // id de chaves_ctl
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave.php?acao=retornoChave&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("voltanumchave").value = Resp.chavenum;
                                    document.getElementById("voltacomplemchave").value = Resp.chavenumcompl;
                                    document.getElementById("voltalocalchave").value = Resp.chavelocal;
                                    document.getElementById("voltasalachave").value = Resp.chavesala;
                                    document.getElementById("voltatelef").value = Resp.telef;
                                    document.getElementById("voltasolicitante").innerHTML = Resp.nomecompl;
                                    document.getElementById("guardaPosCod").value = Resp.codusuretirou;
                                    document.getElementById("voltacpf").innerHTML = format_CnpjCpf(Resp.cpfretirou);
                                    document.getElementById("registroRetornoChave").style.display = "block";
                                    document.getElementById("selecEntregador").value = "";
                                    document.getElementById("cpfentregador").value = "";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function voltaChave___(Cod){
                document.getElementById("guardaCod").value = Cod; // id de chaves
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave.php?acao=voltaChave&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("voltanumchave").value = Resp.chavenum;
                                    document.getElementById("voltacomplemchave").value = Resp.chavenumcompl;
                                    document.getElementById("voltalocalchave").value = Resp.chavelocal;
                                    document.getElementById("voltasalachave").value = Resp.chavesala;
                                    document.getElementById("voltatelef").value = Resp.telef;
                                    document.getElementById("voltasolicitante").innerHTML = Resp.nomecompl;
                                    document.getElementById("guardaCod").value = Resp.guardaCod; // para a func devolveChave
                                    document.getElementById("guardaPosCod").value = Resp.codusuretirou;
                                    document.getElementById("voltacpf").innerHTML = format_CnpjCpf(Resp.cpfretirou);
                                    document.getElementById("registroRetornoChave").style.display = "block";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function devolveChave(){
alert(document.getElementById("guardaCod").value);
alert(document.getElementById("voltacpf").value);
alert(document.getElementById("guardaPosCod").value);

                if(document.getElementById("guardaPosCod").value == ""){
                    return false;
                }
                if(document.getElementById("guardaCod").value == ""){
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave.php?acao=devolveChave&codigo="+document.getElementById("guardaCod").value 
                    +"&cpfdevolve="+encodeURIComponent(document.getElementById("voltacpf").innerHTML)
                    +"&codusudevolve="+document.getElementById("guardaPosCod").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp2 = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                            if(parseInt(Resp.coderro) === 0){
                                                document.getElementById("registroRetornoChave").style.display = "none";
                                                document.getElementById("msgdevolv").innerHTML = "Chave "+Resp2.numchave+" DEVOLVIDA";
                                                document.getElementById("modalDevolvida").style.display = "block";
                                                $("#faixacentral").load("modulos/claviculario/jChave1.php?acao=todos");
                                                $("#faixamostra").load("modulos/claviculario/kChave1.php?acao=todos");
                                            }else{
                                                alert("Houve um erro no servidor.")
                                            }
                                        }
                                    }
                                };
                                ajax.send(null);
                            }
            }

            function retornoChave__(Cod){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/claviculario/salvaChave.php?acao=buscaChave&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    if(Resp.chavelocal == null){
                                        Local = "";
                                    }else{
                                        Local = ' - '+Resp.chavelocal;
                                    }

                                    $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma a devolução da Chave '+Resp.chavenum+Local+'?',
                    autoClose: 'Não|15000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/claviculario/salvaChave.php?acao=devolveChave&codigo="+Cod, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp2 = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                            if(parseInt(Resp.coderro) === 0){
                                                document.getElementById("msgdevolv").innerHTML = "Chave "+Resp2.numchave+" DEVOLVIDA";
                                                document.getElementById("modalDevolvida").style.display = "block";
                                                $("#faixacentral").load("modulos/claviculario/jChave1.php?acao=todos");
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
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function fechaEditaChave(){
                document.getElementById("editaModalChave").style.display = "none";
            }
            function fechaRetiradaChave(){
                document.getElementById("registroRetiradaChave").style.display = "none";
            }
            function fechaRetornoChave(){
                document.getElementById("registroRetornoChave").style.display = "none";
            }
            function fechaDevolv(){
                document.getElementById("modalDevolvida").style.display = "none";
            }
            function modif(){
                document.getElementById("mudou").value = "1";
            }

            function foco(id){
                document.getElementById(id).focus();
            }

        </script>
    </head>
    <body>
        <?php
        if(!$Conec){
            echo "Sem contato com o PostGresql";
            return false;
        }
        date_default_timezone_set('America/Sao_Paulo'); //Um dia = 86.400 seg


        //Provisório
//        pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".chaves");
        pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".chaves (
            id SERIAL PRIMARY KEY, 
            chavenum integer NOT NULL DEFAULT 0,
            chavenumcompl VARCHAR(5),
            chavelocal VARCHAR(100),
            chavesala VARCHAR(50),
            chaveobs text, 
            presente smallint NOT NULL DEFAULT 1, 
            ativo smallint NOT NULL DEFAULT 1, 
            usuins bigint NOT NULL DEFAULT 0,
            datains timestamp without time zone DEFAULT '3000-12-31',
            usuedit bigint NOT NULL DEFAULT 0,
            dataedit timestamp without time zone DEFAULT '3000-12-31' 
            )
        ");

//Tabela antiga - apagar
$rs = pg_query($Conec, "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'chaves_ctl' AND COLUMN_NAME = 'chaves_id'");
$row = pg_num_rows($rs);
if($row == 0){ // não tinha a coluna chaves_id
   pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".chaves_ctl");
}

//        pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".chaves_ctl");
        pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".chaves_ctl (
            id SERIAL PRIMARY KEY, 
            chaves_id integer NOT NULL DEFAULT 0,
            datasaida timestamp without time zone DEFAULT '3000-12-31',
            datavolta timestamp without time zone DEFAULT '3000-12-31',
            funcentrega bigint NOT NULL DEFAULT 0,
            funcrecebe bigint NOT NULL DEFAULT 0,
            usuretira bigint NOT NULL DEFAULT 0,
            usudevolve bigint NOT NULL DEFAULT 0,
            cpfretira VARCHAR(20),
            cpfdevolve VARCHAR(20),
            telef VARCHAR(20),
            ativo smallint NOT NULL DEFAULT 1, 
            usuins bigint NOT NULL DEFAULT 0,
            datains timestamp without time zone DEFAULT '3000-12-31',
            usuedit bigint NOT NULL DEFAULT 0,
            dataedit timestamp without time zone DEFAULT '3000-12-31' 
            )
        ");

        $rs = pg_query($Conec, "SELECT chavenum FROM ".$xProj.".chaves LIMIT 3 ");
        $row = pg_num_rows($rs);
        if($row == 0){
            //Insere as primeiras 10 chaves
            for($i = 1; $i <= 10; $i++){
                $rs0 = pg_query($Conec, "SELECT chavenum FROM ".$xProj.".chaves WHERE chavenum = $i ");
                $row0 = pg_num_rows($rs0);
                if($row0 == 0){
                    pg_query($Conec, "INSERT INTO ".$xProj.".chaves (chavenum, usuins, datains) VALUES ($i, 3, NOW())");
                }
            }
        }

//______________________


        $Clav = parEsc("clav", $Conec, $xProj, $_SESSION["usuarioID"]); // entrega e devolução
        $Chave = parEsc("chave", $Conec, $xProj, $_SESSION["usuarioID"]); // pode pegar chaves
        $FiscClav = parEsc("fisc_clav", $Conec, $xProj, $_SESSION["usuarioID"]); // fiscal de chaves

        $OpUsuSolic = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE chave = 1 And ativo = 1 ORDER BY nomeusual, nomecompl");
        $OpUsuEntreg = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE chave = 1 And ativo = 1 ORDER BY nomeusual, nomecompl");

        ?>
         <div style="margin: 20px; ">
            <div class="box" style="position: relative; float: left; width: 33%;">
                <input type="button" id="botinserir" class="resetbot fundoAzul2" style="font-size: 80%;" value="Inserir Nova Chave" onclick="insChave();">
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: center;">
                <h5><img src="imagens/Chave.png" height="40px;" style="padding-right: 20px; padding-bottom: 10px;" title="Controle de Chaves na Portaria">Controle de Chaves <img src="imagens/Chave.png" height="40px;" style="padding-left: 20px; padding-bottom: 10px;" title="Controle de Chaves na Portaria"></h5>
                
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: left;">
                <label style="padding-left: 20px;"></label>
                <button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="imprAr();">PDF</button>
            </div>

            <div id="faixaMensagem" style="display: none; position: relative; margin: 70px; padding: 20px; text-align: center;">
                <br><br><br>Usuário não cadastrado. <br>O acesso é proporcionado pela ATI.
            </div>
        </div>


        <!-- div três colunas -->
        <div style="margin: 0 auto;">
            <div style="position: relative; float: left; margin: 5px; text-align: center; width: 65%; border: 1px solid; border-radius: 10px;"><div id="faixacentral"></div></div>
            <div style="position: relative; float: left; text-align: center; width: 1%;">&nbsp;</div>
            <div style="position: relative; float: left; margin: 5px; text-align: center; width: 29%; border: 1px solid; border-radius: 10px;"><div id="faixamostra"></div></div>
        </div>

        <input type="hidden" id="guardaCod" value="0" />
        <input type="hidden" id="guardaCPF" value="" />
        <input type="hidden" id="guardaPosCod" value="" />

        <input type="hidden" id="mudou" value="0" />
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"]; ?>" />
        <input type="hidden" id="editachaves" value="<?php echo $FiscClav; ?>" />
        <input type="hidden" id="registrachaves" value="<?php echo $Clav; ?>" />

        
        <div id="editaModalChave" class="relacmodal">
            <div class="modal-content-relacChave">
                <span class="close" onclick="fechaEditaChave();">&times;</span>
                <label style="color: #666;">Edição:</label>
                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td class="etiq" style="padding-bottom: 7px;">Chave: </td>
                        <td style="padding-bottom: 10px;"><input type="text" id="numchave" style="width: 70px; text-align: center;" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('complemchave');return false;}" title="Número da chave. Preferencialmente único."/>
                            <input type="text" id="complemchave" style="width: 70px; text-align: center;" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('localchave');return false;}" title="Complemento para o caso de chaves com o mesmo número"/>
                    </td>
                        <td style="padding-bottom: 10px;"></td>
                    </tr>
                    <tr>
                        <td class="etiq">Local: </td>
                        <td colspan="3" style="width: 100px;"><input type="text" id="localchave" maxlength="70" style="width: 300px; text-align: left;" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('salachave');return false;}" title=""/></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiq">Sala: </td>
                        <td colspan="3" style="width: 100px;"><input type="text" id="salachave" style="width: 100px; text-align: center;" onchange="modif();" onkeypress="if(event.keyCode===13){javascript:foco('numchave');return false;}" title=""/></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiq">Observações: </td>
                        <td colspan="3" style="width: 100px;"><textarea id="obschave" style="margin-top: 3px; border: 1px solid blue; border-radius: 10px; padding: 2px;" rows="2" cols="40" title="Observações" onchange="modif();"></textarea></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="3" style="text-align: center; padding-top: 10px;"><button class="botpadrblue" onclick="salvaEditChave();">Salvar</button></td>
                    </tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->

<!-- Retirada Chave-->
        <div id="registroRetiradaChave" class="relacmodal">
            <div class="modal-content-registroChave"> <!-- background transparent-->
                <span class="close" style="font-size: 250%; color: black;" onclick="fechaRetiradaChave();">&times;</span>
                <label style="color: #666; padding-bottom: 10px;">Registro de Retirada:</label>

                <div style="border: 2px solid red; border-radius: 10px; background: linear-gradient(180deg, white, #f5a7a2)">
                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td colspan="4" style="text-align: center; font-weight: bold;">Registro de Retirada</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding-top: 10px;"></td>
                    </tr>
                    <tr>
                        <td class="etiq" style="padding-bottom: 7px;">Chave: </td>
                        <td><input disabled type="text" id="sainumchave" style="width: 70px; text-align: center; border: 1px solid #666; border-radius: 5px;" />
                            <input disabled type="text" id="saicomplemchave" style="width: 70px; text-align: center; border: 1px solid #666; border-radius: 5px;" />
                        </td>
                        <td colspan="2" style="padding-bottom: 10px;"></td>
                    </tr>
                    <tr>
                        <td class="etiq">Local: </td>
                        <td colspan="3"><input disabled type="text" id="sailocalchave" style="width: 300px; text-align: center; border: 1px solid #666; border-radius: 5px;" /></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiq">Sala: </td>
                        <td colspan="3"><input disabled type="text" id="saisalachave" style="width: 100px; text-align: center; border: 1px solid #666; border-radius: 5px;"/></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiq">Observações: </td>
                        <td colspan="3" style="width: 100px;"><textarea disabled id="saiobschave" style="border: 1px solid blue; border-radius: 10px; padding: 2px;" rows="2" cols="40"></textarea></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                    </tr>
                </table>
                </div>

                <div style="border: 2px solid red; border-radius: 10px; margin-top: 10px; background: linear-gradient(180deg, white, #f5a7a2)">
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
                                <select id="selecSolicitante" style="max-width: 230px;" onchange="modif();" title="Selecione um usuário.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpUsuSolic){
                                        while ($Opcoes = pg_fetch_row($OpUsuSolic)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td class="etiqAzul"><label class="etiqAzul">ou CPF:</label></td>
                            <td>
                                <input type="text" id="cpfsolicitante" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('resulttelef');return false;}" title="Procura por CPF. Digite o CPF do solicitante."/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                    </table>
                </div>

                <div style="border: 2px solid red; border-radius: 10px; margin-top: 10px; text-align: left; background: linear-gradient(180deg, white, #f5a7a2)">
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; font-weight: bold;">Solicitante</td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Nome:</td>
                            <td colspan="3"><label id="resultsolicitante" style="min-width: 200px; padding-left: 3px; font-size: 120%;"></label></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">CPF:</td>
                            <td><label id="resultcpf" style="padding-left: 3px;"></label></td>
                            <td class="etiqAzul">Setor:</td>
                            <td><label id="resultsetor" style="padding-left: 3px;"></label></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Telefone Celular:</td>
                            <td colspan="3"><input type="text" id="resulttelef" style="width: 200px; text-align: center; border: 1px solid #666; border-radius: 5px;" onchange="modif();" /></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"><button class="botpadrred" style="font-size: 80%;" onclick="entregaChave();">Registrar Saída</button></td>
                        </tr>

                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 5px;"></td>
                        </tr>
                    </table>
                </div>

            </div>
        </div> <!-- Fim Modal-->

<!-- Retorno Chave -->
        <div id="registroRetornoChave" class="relacmodal">
            <div class="modal-content-registroChave">
                <span class="close" style="font-size: 250%; color: black;" onclick="fechaRetornoChave();">&times;</span>
                <label style="color: #666; padding-bottom: 10px;">Registro de Retirada:</label>

                <div style="border: 2px solid blue; border-radius: 10px; background: linear-gradient(180deg, white, #89e9eb)">
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="4" style="text-align: center; font-weight: bold;">Registro de Retorno</td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 5px;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td class="etiq" style="padding-bottom: 7px;">Chave: </td>
                            <td><input disabled type="text" id="voltanumchave" style="width: 70px; text-align: center; border: 1px solid #666; border-radius: 5px;" />
                                <input disabled type="text" id="voltacomplemchave" style="width: 70px; text-align: center; border: 1px solid #666; border-radius: 5px;" />
                            </td>
                            <td colspan="2" style="padding-bottom: 10px;"></td>
                        </tr>
                        <tr>
                            <td class="etiq">Local: </td>
                            <td colspan="3"><input disabled type="text" id="voltalocalchave" style="width: 300px; text-align: center; border: 1px solid #666; border-radius: 5px;" /></td>
                            <td></td>
                        </tr>
                        <tr>
                        <td class="etiq">Sala: </td>
                            <td colspan="3"><input disabled type="text" id="voltasalachave" style="width: 100px; text-align: center; border: 1px solid #666; border-radius: 5px;"/></td>
                        <td></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                    </table>
                </div>

               <div style="border: 2px solid blue; border-radius: 10px; margin-top: 10px; text-align: left; background: linear-gradient(180deg, white, #89e9eb)">
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; font-weight: bold;">Devolvido por</td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Nome:</td>
                            <td colspan="3"><label id="voltasolicitante" style="min-width: 200px; padding-left: 3px; font-size: 120%;"></label></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">CPF:</td>
                            <td><label id="voltacpf" style="padding-left: 3px;"></label></td>
                            <td class="etiqAzul">Setor:</td>
                            <td><label id="voltasetor" style="padding-left: 3px;"></label></td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Telefone Celular:</td>
                            <td colspan="3"><input type="text" id="voltatelef" style="width: 200px; text-align: center; border: 1px solid #666; border-radius: 5px;" onchange="modif();" /></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"><button class="botpadrred" style="font-size: 80%;" onclick="devolveChave();">Registrar Retorno</button></td>
                        </tr>

                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 5px;"></td>
                        </tr>
                    </table>
                </div>

                <div style="border: 2px solid blue; border-radius: 10px; margin-top: 10px; background: linear-gradient(180deg, white, #89e9eb)">
                    <table style="margin: 0 auto; width: 85%;">
                        <tr>
                            <td colspan="4" style="text-align: center;"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center;">Busca Nome ou CPF do Entregador</td>
                        </tr>
                        <tr>
                            <td class="etiqAzul">Procura nome: </td>
                            <td style="width: 100px;">
                                <select id="selecEntregador" style="max-width: 230px;" onchange="modif();" title="Selecione um usuário.">
                                    <option value=""></option>
                                    <?php 
                                    if($OpUsuEntreg){
                                        while ($Opcoes = pg_fetch_row($OpUsuEntreg)){ ?>
                                            <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; if($Opcoes[2] != ""){echo " - ".$Opcoes[2];} ?></option>
                                        <?php 
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td class="etiqAzul"><label class="etiqAzul">ou CPF:</label></td>
                            <td>
                                <input type="text" id="cpfentregador" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('selecEntregador');return false;}" title="Procura por CPF. Digite o CPF do entregador."/>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                        </tr>
                    </table>
                </div>

                
            </div>
        </div> <!-- Fim Modal-->








        <div id="modalDevolvida" class="relacmodal">
            <div class="modal-content-tarjaVerm">
                <span class="close" onclick="fechaDevolv();">&times;</span>
                <div id="msgdevolv" style="color: white; font-size: 300%; text-align: center;">Chave DEVOLVIDA</div>
            </div>
        </div> <!-- Fim Modal-->

    </body>
</html>