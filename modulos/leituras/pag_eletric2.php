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
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="class/gijgo/css/gijgo.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="class/gijgo/js/gijgo.js"></script>
        <script src="class/gijgo/js/messages/messages.pt-br.js"></script>
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script> 
        <script src="comp/js/jquery.mask.js"></script>
        <style type="text/css">
            .modal-content-Eletric2Controle{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 10% auto;
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 70%;
            }
            .quadro{
                position: relative; float: left; margin: 5px; width: 95%; padding: 2px; padding-top: 5px;
            }
            .etiq{
                text-align: center; font-size: 70%; font-weight: bold; padding-right: 1px; padding-bottom: 1px;
            }
            .titRelat{
                /*  padding-top, padding-right, padding-bottom, padding-left */
                margin: 5px; padding: 3px 15px 3px 15px; background-color: #FFFACD; border: 1px solid; border-radius:10px;
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
                if(parseInt(document.getElementById("guardaerro").value) === 0){
                    document.getElementById("botInserir").style.visibility = "hidden"; 
                    document.getElementById("botImprimir").style.visibility = "hidden"; 
                    document.getElementById("imgEletric2config").style.visibility = "hidden"; 
                    if(parseInt(document.getElementById("InsLeituraEletric").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){ // // se estiver marcado em cadusu para fazer a leitura
                        if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admIns").value)){
                            document.getElementById("botInserir").style.visibility = "visible"; 
                            $("#container5").load("modulos/leituras/carEletric2.php");
                            $("#container6").load("modulos/leituras/carEstatEletric2.php");
                            //para inserir tem que estar marcado no cadastro de usuários e ter o nível adm estabelecido nos parâmetros do sistema
                        }else{
                            $("#container5").load("modulos/leituras/carMsg.php?msgtipo=2");
                            $("#container6").load("modulos/leituras/carMsg.php?msgtipo=2");
                        }
                    }else{
                        $("#container5").load("modulos/leituras/carMsg.php?msgtipo=1");
                        $("#container6").load("modulos/leituras/carMsg.php?msgtipo=1");
                    }
                    //para editar obedece ao nivel administrativo
                    if(parseInt(document.getElementById("InsLeituraEletric").value) === 1 && parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admEdit").value) || parseInt(document.getElementById("UsuAdm").value) > 6){
                        document.getElementById("botImprimir").style.visibility = "visible"; 
                        document.getElementById("imgEletric2config").style.visibility = "visible"; 
                    }else{
                        document.getElementById("botImprimir").style.visibility = "hidden"; 
                    }
                };

                $("#configCpfEletric").mask("999.999.999-99");

                $("#selecMesAnoEletric").change(function(){
                    document.getElementById("selecAnoEletric").value = "";
                    if(document.getElementById("selecMesAnoEletric").value != ""){
                        window.open("modulos/leituras/imprLista.php?acao=listamesEletric&colec=2&mesano="+encodeURIComponent(document.getElementById("selecMesAnoEletric").value), document.getElementById("selecMesAnoEletric").value);
                        document.getElementById("selecMesAnoEletric").value = "";
                        document.getElementById("relacimprLeituraEletric").style.display = "none";
                    }
                });
                $("#selecAnoEletric").change(function(){
                    document.getElementById("selecMesAnoEletric").value = "";
                    if(document.getElementById("selecAnoEletric").value != ""){
                        window.open("modulos/leituras/imprLista.php?acao=listaanoEletric&colec=2&ano="+encodeURIComponent(document.getElementById("selecAnoEletric").value), document.getElementById("selecAnoEletric").value);
                        document.getElementById("selecAnoEletric").value = "";
                        document.getElementById("relacimprLeituraEletric").style.display = "none";
                    }
                });


                $("#configSelecEletric").change(function(){
                    if(document.getElementById("configSelecEletric").value == ""){
                        document.getElementById("configCpfEletric").value = "";
                        document.getElementById("leituraEletric").checked = false;
                        document.getElementById("leituraEletric2").checked = false;
                        document.getElementById("leituraEletric3").checked = false;
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/leituras/salvaLeitura.php?acao=buscausuario&codigo="+document.getElementById("configSelecEletric").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configCpfEletric").value = format_CnpjCpf(Resp.cpf);
                                        if(parseInt(Resp.eletric) === 1){
                                            document.getElementById("leituraEletric").checked = true;
                                        }else{
                                            document.getElementById("leituraEletric").checked = false;
                                        }
                                        if(parseInt(Resp.eletric2) === 1){
                                            document.getElementById("leituraEletric2").checked = true;
                                        }else{
                                            document.getElementById("leituraEletric2").checked = false;
                                        }
                                        if(parseInt(Resp.eletric3) === 1){
                                            document.getElementById("leituraEletric3").checked = true;
                                        }else{
                                            document.getElementById("leituraEletric3").checked = false;
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

                $("#configCpfEletric").click(function(){
                    document.getElementById("configSelecEletric").value = "";
                    document.getElementById("configCpfEletric").value = "";
                    document.getElementById("leituraEletric").checked = false;
                    document.getElementById("leituraEletric2").checked = false;
                    document.getElementById("leituraEletric3").checked = false;
                });
                $("#configCpfEletric").change(function(){
                    document.getElementById("configSelecEletric").value = "";
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/leituras/salvaLeitura.php?acao=buscacpf&cpf="+encodeURIComponent(document.getElementById("configCpfEletric").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("configSelecEletric").value = Resp.PosCod;
                                        if(parseInt(Resp.eletric) === 1){
                                            document.getElementById("leituraEletric").checked = true;
                                        }else{
                                            document.getElementById("leituraEletric").checked = false;
                                        }
                                        if(parseInt(Resp.eletric2) === 1){
                                            document.getElementById("leituraEletric2").checked = true;
                                        }else{
                                            document.getElementById("leituraEletric2").checked = false;
                                        }
                                        if(parseInt(Resp.eletric3) === 1){
                                            document.getElementById("leituraEletric3").checked = true;
                                        }else{
                                            document.getElementById("leituraEletric3").checked = false;
                                        }
                                    }
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.");
                                    }
                                    if(parseInt(Resp.coderro) === 2){
                                        document.getElementById("leituraEletric").checked = false;
                                        document.getElementById("leituraEletric2").checked = false;
                                        document.getElementById("leituraEletric3").checked = false;
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
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/leituras/salvaLeitura2.php?acao=buscaDataEletric&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    document.getElementById("insdata").value = Resp.data;
                                    document.getElementById("insdiasemana").innerHTML = Resp.sem;
                                    document.getElementById("insleitura2").value = Resp.leitura2;
                                    document.getElementById("guardacod").value = Cod;
                                    document.getElementById("relacmodalEletric").style.display = "block";
                                    if(parseInt(document.getElementById("UsuAdm").value) > 6){ // superusuário
                                        document.getElementById("apagaRegEletric").style.visibility = "visible";
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
                    ajax.open("POST", "modulos/leituras/salvaLeitura2.php?acao=ultDataEletric", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else if(parseInt(Resp.coderro) === 2){
                                    document.getElementById("insdata").value = Resp.data;
                                    document.getElementById("insdiasemana").innerHTML = Resp.sem;
                                    document.getElementById("guardacod").value = 0;
                                    document.getElementById("insdata").disabled = false;
                                    document.getElementById("insdata").value = Resp.proximo;  // document.getElementById("guardahoje").value;
                                    document.getElementById("insleitura2").value = "";
                                    document.getElementById("relacmodalEletric").style.display = "block";
                                    document.getElementById("apagaRegEletric").style.visibility = "hidden";
                                    $('#mensagemLeitura').fadeIn("slow");
                                    document.getElementById("mensagemLeitura").innerHTML = "Data inicial para os lançamentos. <br>O valor anterior anotado é: "+Resp.valorini;
                                    $('#mensagemLeitura').fadeOut(10000);
                                    document.getElementById("insleitura2").focus();
                                }else{
                                    document.getElementById("insdata").value = Resp.data;
                                    document.getElementById("insdiasemana").innerHTML = Resp.sem;
                                    document.getElementById("guardacod").value = 0;
                                    document.getElementById("insdata").disabled = false;
                                    document.getElementById("insdata").value = Resp.proximo;  // document.getElementById("guardahoje").value;
                                    document.getElementById("insleitura2").value = "";
                                    document.getElementById("relacmodalEletric").style.display = "block";
                                    document.getElementById("apagaRegEletric").style.visibility = "hidden";
                                    $('#mensagemLeitura').fadeIn("slow");
                                    document.getElementById("mensagemLeitura").innerHTML = "Próxima data para lançamento.";
                                    $('#mensagemLeitura').fadeOut(2000);
                                    document.getElementById("insleitura2").focus();
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
                    ajax.open("POST", "modulos/leituras/salvaLeitura2.php?acao=checaDataEletric&data="+encodeURIComponent(document.getElementById("insdata").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else{
                                    if(parseInt(Resp.jatem) === 1){
                                        document.getElementById("insdata").value = Resp.data;
                                        document.getElementById("insdiasemana").innerHTML = Resp.sem;
                                        document.getElementById("insleitura2").value = Resp.leitura2;
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
                    document.getElementById("relacmodalEletric").style.display = "none";
                    return false;
                }
                Tam = document.getElementById("insdata").value;
                if(Tam.length < 10){
                    document.getElementById("insdata").value = "";
                    document.getElementById("insdata").focus();
                    return false;
                }
                if(document.getElementById("insleitura2").value == ""){
                    $('#mensagemLeitura').fadeIn("slow");
                    document.getElementById("mensagemLeitura").innerHTML = "Nenhuma leitura anotada";
                    $('#mensagemLeitura').fadeOut(3000);
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/leituras/salvaLeitura2.php?acao=salvaDataEletric&colec=2&insdata="+encodeURIComponent(document.getElementById("insdata").value)
                    +"&leitura2="+document.getElementById("insleitura2").value
                    +"&codigo="+document.getElementById("guardacod").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.");
                                }else if(parseInt(Resp.coderro) === 2){
                                    document.getElementById("relacmodalEletric").style.display = "none";
                                    alert("Este primeiro lançamento está diferente da data especificada para o início. \nA estatística ficará prejudicada. Informe à ATI.");
                                }else{
                                    $('#mensagemLeitura').fadeIn("slow");
                                    document.getElementById("mensagemLeitura").innerHTML = "Lançamento salvo.";
                                    $('#mensagemLeitura').fadeOut(1000);
                                    $("#container5").load("modulos/leituras/carEletric2.php");
                                    $("#container6").load("modulos/leituras/carEstatEletric2.php");
                                    document.getElementById("relacmodalEletric").style.display = "none";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function marcaEletric(obj, Campo){
                if(obj.checked === true){
                    Valor = 1;
                }else{
                    Valor = 0;
                }
                if(document.getElementById("configSelecEletric").value == ""){
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
                    ajax.open("POST", "modulos/leituras/salvaLeitura.php?acao=configMarcaEletric&codigo="+document.getElementById("configSelecEletric").value
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

            function apagaModalEletric(){
                $.confirm({
                    title: 'Confirmação!',
                    content: 'Confirma apagar este lançamento?',
                    autoClose: 'Não|20000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/leituras/salvaLeitura2.php?acao=apagareg&codigo="+document.getElementById("guardacod").value, true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                $("#container5").load("modulos/leituras/carEletric2.php");
                                                $("#container6").load("modulos/leituras/carEstatEletric2.php");
                                                document.getElementById("relacmodalEletric").style.display = "none";                                            }
                                        }
                                    }
                                };
                                ajax.send(null);
                            }
                        },
                        Não: function () {
                            document.getElementById("configfatorcorrec").value = document.getElementById("guardafator").value;
                        }
                    }
                });
            }
            

            function abreEletric2Config(){
                document.getElementById("leituraEletric").checked = false;
                document.getElementById("leituraEletric2").checked = false;
                document.getElementById("leituraEletric3").checked = false;
                document.getElementById("configCpfEletric").value = "";
                document.getElementById("configSelecEletric").value = "";
                document.getElementById("modalEletric2Config").style.display = "block";
            }
            function fechaEletric2Config(){
                document.getElementById("modalEletric2Config").style.display = "none";
            }
            function resumoUsuEletric(){
                window.open("modulos/leituras/imprUsuEletric.php?acao=listaUsuarios", "EletricUsu");
            }
 
            function imprMesLeitura(){
                if(document.getElementById("selecMesAno").value == ""){
                    return false;
                }
                window.open("modulos/leituras/imprLista.php?acao=listamesEletric&colec=2&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                document.getElementById("relacimprLeituraEletric").style.display = "none";
            }
            function imprAnoLeitura(){
                if(document.getElementById("selecAno").value == ""){
                    return false;
                }
                window.open("modulos/leituras/imprLista.php?acao=listaanoEletric&colec=2&ano="+encodeURIComponent(document.getElementById("selecAno").value));
                document.getElementById("relacimprLeituraEletric").style.display = "none";
            }

            function abreImprLeitura(){
                document.getElementById("relacimprLeituraEletric").style.display = "block";
            }
            function fechaModal(){
                document.getElementById("relacmodalEletric").style.display = "none";
            }
            function fechaModalImpr(){
                document.getElementById("relacimprLeituraEletric").style.display = "none";
            }
            function modif(){ // assinala se houve qualquer modificação nos campos do modal
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
            modalImpr = document.getElementById('relacimprLeituraEletric'); //span[0]
                spanImpr = document.getElementsByClassName("close")[0];
                window.onclick = function(event){
                    if(event.target === modalImpr){
                        modalImpr.style.display = "none";
                    }
                };
        </script>
    </head>
    <body>
        <?php
            $Hoje = date('d/m/Y');
            $Erro = 0;
            $rs = pg_query($Conec, "SELECT column_name, data_type, character_maximum_length FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'leitura_eletric'");
            $row = pg_num_rows($rs);
            if($row == 0){
                $Erro = 1;
                echo "Faltam tabelas. Informe à ATI.";
                return false;
            }
            $rs1 = pg_query($Conec, "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'leitura_eletric' AND COLUMN_NAME = 'dataleitura'");
            $row1 = pg_num_rows($rs1);
            if($row1 > 0){
                pg_query($Conec, "DROP TABLE IF EXISTS ".$xProj.".leitura_eletric");
            
                pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".leitura_eletric (
                    id SERIAL PRIMARY KEY, 
                    colec smallint NOT NULL DEFAULT 0, 
                    dataleitura1 date DEFAULT CURRENT_TIMESTAMP,
                    leitura1 double precision NOT NULL DEFAULT 0,
                    dataleitura2 date DEFAULT CURRENT_TIMESTAMP,
                    leitura2 double precision NOT NULL DEFAULT 0,
                    dataleitura3 date DEFAULT CURRENT_TIMESTAMP,
                    leitura3 double precision NOT NULL DEFAULT 0,
                    ativo smallint DEFAULT 1 NOT NULL,
                    usuins integer DEFAULT 0 NOT NULL,
                    datains timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                    usumodif integer DEFAULT 0 NOT NULL,
                    datamodif timestamp without time zone DEFAULT CURRENT_TIMESTAMP) 
                ");
            }

            $admIns = parAdm("insleituraeletric", $Conec, $xProj);   // nível para inserir 
            $admEdit = parAdm("editleituraeletric", $Conec, $xProj); // nível para editar
            $InsEletric = parEsc("eletric2", $Conec, $xProj, $_SESSION["usuarioID"]); // procura coluna eletric em poslog 

            $Menu1 = escMenu($Conec, $xProj, 1);
            $Menu2 = escMenu($Conec, $xProj, 2);
            $Menu3 = escMenu($Conec, $xProj, 3);
            $ValorKwh = parAdm("valorkwh", $Conec, $xProj); // é o mesmo para pag_eletric2 e 3

            // Preenche caixa de escolha mes/ano para impressão
            $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataleitura2, 'MM'), '/', TO_CHAR(dataleitura2, 'YYYY')) 
            FROM ".$xProj.".leitura_eletric WHERE colec = 2 GROUP BY TO_CHAR(dataleitura2, 'MM'), TO_CHAR(dataleitura2, 'YYYY') ORDER BY TO_CHAR(dataleitura2, 'YYYY') DESC, TO_CHAR(dataleitura2, 'MM') DESC ");
            $OpcoesEscAno = pg_query($Conec, "SELECT EXTRACT(YEAR FROM ".$xProj.".leitura_eletric.dataleitura2)::text 
            FROM ".$xProj.".leitura_eletric WHERE colec = 2 GROUP BY 1 ORDER BY 1 DESC ");

            $OpConfig = pg_query($Conec, "SELECT pessoas_id, nomecompl, nomeusual FROM ".$xProj.".poslog WHERE ativo = 1 ORDER BY nomecompl, nomeusual");
        ?>
        <input type="hidden" id="guardahoje" value = "<?php echo $Hoje; ?>" />
        <input type="hidden" id="guardaerro" value = "<?php echo $Erro; ?>" />
        <input type="hidden" id="guardaUsuId" value = "<?php echo $_SESSION["usuarioID"]; ?>" />
        <input type="hidden" id="UsuAdm" value = "<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="admIns" value = "<?php echo $admIns; ?>" /> <!-- nível mínimo para inserir  -->
        <input type="hidden" id="admEdit" value = "<?php echo $admEdit; ?>" />
        <input type="hidden" id="InsLeituraEletric" value = "<?php echo $InsEletric; ?>" /> <!-- autorização para um só indivíduo inserir as leituras -->

        <div style="margin: 5px; border: 2px solid green; border-radius: 15px; padding: 5px;">
            <div class="row"> <!-- botões Inserir e Imprimir-->
                <div class="col" style="margin: 0 auto; text-align: left;" title="Inserir leitura do medidor de energia elétrica">
                    <img src="imagens/settings.png" height="20px;" id="imgEletric2config" style="cursor: pointer; padding-left: 30px;" onclick="abreEletric2Config();" title="Configurar o acesso ao processamento">
                    <label style="padding-right: 40px;"></label>
                    <button id="botInserir" class="botpadrblue" onclick="insereModal();">Inserir</button>    
                </div> <!-- quadro -->
                <div class="col" style="text-align: center;">Controle do Consumo de Energia Elétrica<?php echo " - ".$Menu2; ?></div> <!-- espaçamento entre colunas  -->
                <div class="col" style="margin: 0 auto; text-align: center;"><button id="botImprimir" class="botpadrred" onclick="abreImprLeitura();">PDF</button></div> <!-- quadro -->
            </div>

            <div style="padding: 10px; display: flex; align-items: center; justify-content: center;"> 

                <div class="row" style="width: 95%;">
                    <div id="container5" class="col quadro" style="margin: 0 auto; width: 100%;"></div> <!-- quadro -->

                    <div class="col-1" style="width: 1%;"></div> <!-- espaçamento entre colunas  -->

                    <div id="container6" class="col quadro" style="margin: 0 auto; width: 100%;"></div> <!-- quadro -->

                </div> <!-- row  -->
            </div> <!-- container  -->
        </div>

        <!-- div modal para imprimir em pdf  -->
        <div id="relacimprLeituraEletric" class="relacmodal">
            <div class="modal-content-imprLeitura">
                <span class="close" onclick="fechaModalImpr();">&times;</span>
                <h5 style="text-align: center;color: #666;">Controle do Consumo de Eletricidade<?php echo " - ".$Menu2; ?></h5>
                <h6 style="text-align: center; padding-bottom: 18px; color: #666;">Impressão PDF</h6>
                <div style="border: 2px solid #C6E2FF; border-radius: 10px;">
                    <table style="margin: 0 auto; width: 95%;">
                        <tr>
                            <td style="text-align: right;"><label style="font-size: 80%;">Mensal - Selecione o Mês/Ano: </label></td>
                            <td>
                                <select id="selecMesAnoEletric" style="font-size: 1rem; width: 90px;" title="Selecione o período.">
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
                                <select id="selecAnoEletric" style="font-size: 1rem; width: 90px;" title="Selecione o Ano.">
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
         <div id="modalEletric2Config" class="relacmodal">
            <div class="modal-content-Eletric2Controle">
                <span class="close" onclick="fechaEletric2Config();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;">
    
                        </div>
                        <div class="col quadro"><h5 style="text-align: center; color: #666;">Configuração <br>Eletricidade <?php echo $Menu2; ?></h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><button class="botpadrred" style="font-size: 70%;" onclick="resumoUsuEletric();">Resumo em PDF</button></div> 
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
                            <select id="configSelecEletric" style="max-width: 230px;" onchange="modif();" title="Selecione um usuário.">
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
                            <input type="text" id="configCpfEletric" style="width: 130px; text-align: center; border: 1px solid #666; border-radius: 5px;" onkeypress="if(event.keyCode===13){javascript:foco('configSelecEletric');return false;}" title="Procura por CPF. Digite o CPF."/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; padding-top: 10px;"></td>
                    </tr>
                </table>

                <table style="margin: 0 auto; width: 85%;">
                    <tr>
                        <td class="etiq80" title="Pode registrar as leituras diárias do consumo de eletricidade">Energia Elétrica:</td>
                        <td colspan="4">
                            <input type="checkbox" id="leituraEletric" title="Pode registrar as leituras diárias do consumo de energia elétrica" onchange="marcaEletric(this, 'eletric');" >
                            <label for="leituraEletric" title="Pode registrar as leituras diárias do consumo de energia elétrica">registrar leitura do Medidor de Energia Elétrica - <?php echo $Menu1; ?></label>
                        </td>
                    </tr>

                    <tr>
                        <td class="etiq80" title="Pode registrar as leituras diárias do consumo de eletricidade">Energia Elétrica:</td>
                        <td colspan="4">
                            <input type="checkbox" id="leituraEletric2" title="Pode registrar as leituras diárias do consumo de energia elétrica" onchange="marcaEletric(this, 'eletric2');" >
                            <label for="leituraEletric2" title="Pode registrar as leituras diárias do consumo de energia elétrica do medidor da operadora ">registrar leitura do Medidor de Energia Elétrica - <?php echo $Menu2; ?></label>
                        </td>
                    </tr>
                    <tr>
                        <td class="etiq80" style="border-bottom: 1px solid;" title="Pode registrar as leituras diárias do consumo de eletricidade">Energia Elétrica:</td>
                        <td colspan="4" style="border-bottom: 1px solid;">
                            <input type="checkbox" id="leituraEletric3" title="Pode registrar as leituras diárias do consumo de energia elétrica" onchange="marcaEletric(this, 'eletric3');" >
                            <label for="leituraEletric3" title="Pode registrar as leituras diárias do consumo de energia elétrica do medidor da operadora">registrar leitura do Medidor de Energia Elétrica - <?php echo $Menu3; ?></label>
                        </td>
                    </tr>

                </table>
            </div>
        </div> <!-- Fim Modal-->

    </body>
</html>