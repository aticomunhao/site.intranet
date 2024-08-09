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
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="class/gijgo/js/gijgo.js"></script>
        <script src="class/gijgo/js/messages/messages.pt-br.js"></script>
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="comp/js/jquery.mask.js"></script>
        <style type="text/css">
            .quadro{
                position: relative; float: left; margin: 5px; width: 95%; border: 1px solid; border-radius: 10px; padding: 2px; padding-top: 5px;
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
                    if(parseInt(document.getElementById("InsLeituraEletric").value) === 1 || parseInt(document.getElementById("UsuAdm").value) > 6){ // // se estiver marcado em cadusu para fazer a leitura
                        if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admIns").value)){
                            document.getElementById("botInserir").style.visibility = "visible"; 
                            $("#container5").load("modulos/leituras/carEletric3.php");
                            $("#container6").load("modulos/leituras/carEstatEletric3.php");
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
                    }else{
                        document.getElementById("botImprimir").style.visibility = "hidden"; 
                    }
                };

                $("#selecMesAnoEletric").change(function(){
                    document.getElementById("selecAnoEletric").value = "";
                    if(document.getElementById("selecMesAnoEletric").value != ""){
                        window.open("modulos/leituras/imprLista.php?acao=listamesEletric&colec=3&mesano="+encodeURIComponent(document.getElementById("selecMesAnoEletric").value), document.getElementById("selecMesAnoEletric").value);
                        document.getElementById("selecMesAnoEletric").value = "";
                        document.getElementById("relacimprLeituraEletric").style.display = "none";
                    }
                });
                $("#selecAnoEletric").change(function(){
                    document.getElementById("selecMesAnoEletric").value = "";
                    if(document.getElementById("selecAnoEletric").value != ""){
                        window.open("modulos/leituras/imprLista.php?acao=listaanoEletric&colec=3&ano="+encodeURIComponent(document.getElementById("selecAnoEletric").value), document.getElementById("selecAnoEletric").value);
                        document.getElementById("selecAnoEletric").value = "";
                        document.getElementById("relacimprLeituraEletric").style.display = "none";
                    }
                });
            });

            function carregaModal(Cod){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/leituras/salvaLeitura3.php?acao=buscaDataEletric&codigo="+Cod, true);
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
                                    document.getElementById("insleitura3").value = Resp.leitura3;
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
                    ajax.open("POST", "modulos/leituras/salvaLeitura3.php?acao=ultDataEletric", true);
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
                                    document.getElementById("insdiasemana").innerHTML = Resp.sem;
                                    document.getElementById("insleitura3").value = "";
                                    document.getElementById("relacmodalEletric").style.display = "block";
                                    document.getElementById("apagaRegEletric").style.visibility = "hidden";
                                    $('#mensagemLeitura').fadeIn("slow");
                                    document.getElementById("mensagemLeitura").innerHTML = "Data inicial para os lançamentos. <br>O valor anterior anotado é: "+Resp.valorini;
                                    $('#mensagemLeitura').fadeOut(10000);
                                    document.getElementById("insleitura3").focus();
                                }else{
                                    document.getElementById("insdata").value = Resp.data;
                                    document.getElementById("insdiasemana").innerHTML = Resp.sem;
                                    document.getElementById("guardacod").value = 0;
                                    document.getElementById("insdata").disabled = false;
                                    document.getElementById("insdata").value = Resp.proximo;  // document.getElementById("guardahoje").value;
                                    document.getElementById("insdiasemana").innerHTML = Resp.sem;
                                    document.getElementById("insleitura3").value = "";
                                    document.getElementById("relacmodalEletric").style.display = "block";
                                    document.getElementById("apagaRegEletric").style.visibility = "hidden";
                                    $('#mensagemLeitura').fadeIn("slow");
                                    document.getElementById("mensagemLeitura").innerHTML = "Próxima data para lançamento.";
                                    $('#mensagemLeitura').fadeOut(2000);
                                    document.getElementById("insleitura3").focus();
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
                    ajax.open("POST", "modulos/leituras/salvaLeitura3.php?acao=checaDataEletric&data="+encodeURIComponent(document.getElementById("insdata").value), true);
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
                    document.getElementById("relacmodalEletric").style.display = "none";
                    return false;
                }
                Tam = document.getElementById("insdata").value;
                if(Tam.length < 10){
                    document.getElementById("insdata").value = "";
                    document.getElementById("insdata").focus();
                    return false;
                }
                if(document.getElementById("insleitura3").value == ""){
                    $('#mensagemLeitura').fadeIn("slow");
                    document.getElementById("mensagemLeitura").innerHTML = "Nenhuma leitura anotada";
                    $('#mensagemLeitura').fadeOut(3000);
                    return false;
                }
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/leituras/salvaLeitura3.php?acao=salvaDataEletric&colec=3&insdata="+encodeURIComponent(document.getElementById("insdata").value)
                    +"&leitura3="+document.getElementById("insleitura3").value
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
                                    $("#container5").load("modulos/leituras/carEletric3.php");
                                    $("#container6").load("modulos/leituras/carEstatEletric3.php");
                                    document.getElementById("relacmodalEletric").style.display = "none";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function imprMesLeitura(){
                if(document.getElementById("selecMesAno").value == ""){
                    return false;
                }
                window.open("modulos/leituras/imprLista.php?acao=listamesEletric&colec=3&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value));
                document.getElementById("relacimprLeituraEletric").style.display = "none";
            }
            function imprAnoLeitura(){
                if(document.getElementById("selecAno").value == ""){
                    return false;
                }
                window.open("modulos/leituras/imprLista.php?acao=listaanoEletric&colec=3&ano="+encodeURIComponent(document.getElementById("selecAno").value));
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
 
            modalImpr = document.getElementById('relacimprLeituraEletric'); //span[0]
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
    <body>
        <?php
            $Hoje = date('d/m/Y');
            $Erro = 0;
            $rs = pg_query($Conec, "SELECT column_name, data_type, character_maximum_length FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'leitura_eletric'");
            $row = pg_num_rows($rs);
            if($row == 0){
                $Erro = 1;
                echo "Faltam tabelas. Informe à ATI.";
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
            $InsEletric = parEsc("eletric3", $Conec, $xProj, $_SESSION["usuarioID"]); // procura coluna eletric em poslog 
            $Menu3 = escMenu($Conec, $xProj, 3);

            // Preenche caixa de escolha mes/ano para impressão
//            $OpcoesEscMes = pg_query($Conec, "SELECT EXTRACT(MONTH FROM ".$xProj.".leitura_eletric.dataleitura3)::text ||'/'|| EXTRACT(YEAR FROM ".$xProj.".leitura_eletric.dataleitura3)::text 
//            FROM ".$xProj.".leitura_eletric WHERE colec = 3 GROUP BY 1 ORDER BY 1 DESC ");
            $OpcoesEscMes = pg_query($Conec, "SELECT CONCAT(TO_CHAR(dataleitura3, 'MM'), '/', TO_CHAR(dataleitura3, 'YYYY')) 
            FROM ".$xProj.".leitura_eletric WHERE colec = 3 GROUP BY TO_CHAR(dataleitura3, 'MM'), TO_CHAR(dataleitura3, 'YYYY') ORDER BY TO_CHAR(dataleitura3, 'YYYY') DESC, TO_CHAR(dataleitura3, 'MM') DESC ");
            $OpcoesEscAno = pg_query($Conec, "SELECT EXTRACT(YEAR FROM ".$xProj.".leitura_eletric.dataleitura3)::text 
            FROM ".$xProj.".leitura_eletric WHERE colec = 3 GROUP BY 1 ORDER BY 1 DESC ");
        ?>
        <input type="hidden" id="guardahoje" value="<?php echo $Hoje; ?>" />
        <input type="hidden" id="guardaerro" value="<?php echo $Erro; ?>" />
        <input type="hidden" id="guardaUsuId" value="<?php echo $_SESSION["usuarioID"]; ?>" />
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="admIns" value="<?php echo $admIns; ?>" /> <!-- nível mínimo para inserir  -->
        <input type="hidden" id="admEdit" value="<?php echo $admEdit; ?>" />
        <input type="hidden" id="InsLeituraEletric" value="<?php echo $InsEletric; ?>" /> <!-- autorização para um só indivíduo inserir as leituras -->

        <div style="margin: 5px; border: 2px solid green; border-radius: 15px; padding: 5px;">
            <div class="row"> <!-- botões Inserir e Imprimir-->
                <div class="col" style="margin: 0 auto; text-align: center;" title="Inserir leitura do medidor de energia elétrica"><button id="botInserir" class="botpadrblue" onclick="insereModal();">Inserir</button></div> <!-- quadro -->
                <div class="col" style="text-align: center;">Controle do Consumo de Energia Elétrica<?php echo " - ".$Menu3; ?></div> <!-- espaçamento entre colunas  -->
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
                <h5 id="titulomodal" style="text-align: center;color: #666;">Controle do Consumo de Eletricidade - Oi</h5>
                <h6 id="titulomodal" style="text-align: center; padding-bottom: 18px; color: #666;">Impressão PDF</h6>
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
    </body>
</html>