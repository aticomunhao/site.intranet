<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION["usuarioID"])){
    header("Location: /cesb/index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Leituras</title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
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
                margin: 5px; padding: 3px 15px 3px 15px; background-color: #87CEFA; border: 1px solid; border-radius:10px;
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
                    $("#container5").load("modulos/leituras/carAgua.php");
                    $("#container6").load("modulos/leituras/carEstatAgua.php");
//                    if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admIns").value)){
//                        carregaModal($id);
//                    }
                };

                $("#selecMesAno").change(function(){
                    document.getElementById("selecAno").value = "";
                    if(document.getElementById("selecMesAno").value != ""){
                        window.open("modulos/leituras/imprLista.php?acao=listames&mesano="+encodeURIComponent(document.getElementById("selecMesAno").value), document.getElementById("selecMesAno").value);
                        document.getElementById("selecMesAno").value = "";
                        document.getElementById("relacimprLeitura").style.display = "none";
                    }
                });
                $("#selecAno").change(function(){
                    document.getElementById("selecMesAno").value = "";
                    if(document.getElementById("selecAno").value != ""){
                        window.open("modulos/leituras/imprLista.php?acao=listaano&ano="+encodeURIComponent(document.getElementById("selecAno").value), document.getElementById("selecAno").value);
                        document.getElementById("selecAno").value = "";
                        document.getElementById("relacimprLeitura").style.display = "none";
                    }
                });

            });

            function carregaModal(Cod){
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
//                                    document.getElementById("insdata").disabled = true;
                                    document.getElementById("insdata").value = Resp.data;
                                    document.getElementById("insdiasemana").innerHTML = Resp.sem;
                                    document.getElementById("insleitura1").value = Resp.leitura1;
                                    document.getElementById("insleitura2").value = Resp.leitura2;
                                    document.getElementById("insleitura3").value = Resp.leitura3;
                                    document.getElementById("guardacod").value = Cod;
                                    document.getElementById("relacmodalLeitura").style.display = "block";
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
                                        document.getElementById("insdiasemana").innerHTML = Resp.sem;
                                        document.getElementById("insleitura1").value = "";
                                        document.getElementById("insleitura2").value = "";
                                        document.getElementById("insleitura3").value = "";
                                        document.getElementById("relacmodalLeitura").style.display = "block";
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
                                    $("#container6").load("modulos/leituras/carEstatAgua.php");
                                    document.getElementById("relacmodalLeitura").style.display = "none";

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
            function fechaModal(){
                document.getElementById("relacmodalLeitura").style.display = "none";
            }
            function fechaModalImpr(){
                document.getElementById("relacimprLeitura").style.display = "none";
            }
            function modif(){ // assinala se houve qualquer modificação nos campos do modal
                document.getElementById("mudou").value = "1";
            }
            function foco(id){
                document.getElementById(id).focus();
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
    <body>
        <?php
            $Hoje = date('d/m/Y');
            $Erro = 0;
            $rs = pg_query($Conec, "SELECT column_name, data_type, character_maximum_length FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'leitura_agua'");
            $row = pg_num_rows($rs);
            if($row == 0){
                $Erro = 1;
                echo "Faltam tabelas. Informe à ATI.";
            }
            // Preenche caixa de escolha mes/ano para impressão
            $OpcoesEscMes = pg_query($Conec, "SELECT EXTRACT(MONTH FROM ".$xProj.".leitura_agua.dataleitura)::text ||'/'|| EXTRACT(YEAR FROM ".$xProj.".leitura_agua.dataleitura)::text 
            FROM ".$xProj.".leitura_agua GROUP BY 1 ORDER BY 1 DESC ");
            $OpcoesEscAno = pg_query($Conec, "SELECT EXTRACT(YEAR FROM ".$xProj.".leitura_agua.dataleitura)::text 
            FROM ".$xProj.".leitura_agua GROUP BY 1 ORDER BY 1 DESC ");
        ?>
        <input type="hidden" id="guardahoje" value="<?php echo $Hoje; ?>" />
        <input type="hidden" id="guardaerro" value="<?php echo $Erro; ?>" />
        <div style="margin: 5px; border: 2px solid green; border-radius: 15px; padding: 5px;">
        
            <div class="row"> <!-- botões Inserir e Imprimir-->
                <div class="col" style="margin: 0 auto; text-align: center;"><button class="botpadr" onclick="insereModal();">Inserir</button></div> <!-- quadro -->
                <div class="col-1"></div> <!-- espaçamento entre colunas  -->
<!--                <div class="col" style="margin: 0 auto; text-align: center;"><button class="resetbotred" style="padding-left: 12px; padding-right: 12px; font-size: 80%;" onclick="abreImprLeitura();">PDF</button></div>  -->
                <div class="col" style="margin: 0 auto; text-align: center;"><button class="botpadrred" onclick="abreImprLeitura();">PDF</button></div> <!-- quadro -->
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
        <div id="relacimprLeitura" class="relacmodal">
            <div class="modal-content-imprLeitura">
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
                                    
                            </td>
                            
<!--                            <td><label style="padding-left: 15px;"></label><button class="botpadrblue" onclick="imprMesLeitura();">Prosseguir</button></td> -->
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
                            </td>
                            
<!--                            <td><label style="padding-left: 15px;"></label><button class="botpadrblue" onclick="imprAnoLeitura();">Prosseguir</button></td> -->
                        </tr>

                    </table>
                </div>
                <div style="padding-bottom: 20px;"></div>
           </div>
           <br><br>
        </div> <!-- Fim Modal-->
    </body>
</html>