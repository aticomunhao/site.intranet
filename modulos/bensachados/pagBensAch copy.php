<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="comp/js/jquery-confirm.min.js"></script> <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="comp/js/jquery.mask.js"></script>
        <style>
            .quadro{
                position: relative; float: left; text-align: center; margin: 5px; width: 95%; padding: 2px; padding-top: 5px;
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
                $("#carregaBens").load("modulos/bensachados/relBens.php");
                $("#dataregistro").mask("99/99/9999");
                $("#dataachado").mask("99/99/9999");
            });

            function abreRegistro(){
                document.getElementById("guardacod").value = 0;
                document.getElementById("botimprReg").style.visibility = "hidden"; 
                document.getElementById("botsalvareg").style.visibility = "visible"; 
                document.getElementById("relacmodalRegistro").style.display = "block";
            }

            function salvaModalRegistro(){
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
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/bensachados/salvaBens.php?acao=salvaRegBem&codigo="+document.getElementById("guardacod").value+
                    "&dataregistro="+encodeURIComponent(document.getElementById("dataregistro").value)+
                    "&dataachado="+encodeURIComponent(document.getElementById("dataachado").value)+
                    "&descdobem="+encodeURIComponent(document.getElementById("descdobem").value)+
                    "&localachado="+encodeURIComponent(document.getElementById("localachado").value)+
                    "&nomeachou="+encodeURIComponent(document.getElementById("nomeachou").value)+
                    "&telefachou="+encodeURIComponent(document.getElementById("telefachou").value)
                    , true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("guardacod").value = Resp.codigonovo;
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("numregistro").innerHTML = "Registrado sob nº "+Resp.numrelat;
                                    document.getElementById("botsalvareg").style.visibility = "hidden"; 
                                    document.getElementById("botimprReg").style.visibility = "visible"; 
                                    $("#carregaBens").load("modulos/bensachados/relBens.php");
                                    document.getElementById("relacmodalRegistro").style.display = "none";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
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

        </script>
    </head>
    <body>
        <?php
        date_default_timezone_set('America/Sao_Paulo');
        require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
        $Hoje = date('d/m/Y');
        if(!$Conec){
            echo "Sem contato com o PostGresql";
            return false;
        }
        $rs = pg_query($Conec, "SELECT column_name, data_type, character_maximum_length FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'livroreg'");
        $row = pg_num_rows($rs);
        if($row == 0){
            $Erro = 1;
            echo "Faltam tabelas. Informe à ATI.";
            return false;
        }

        ?>
        <!-- div três colunas -->
        <div class="container" style="margin: 0 auto;">
            <div class="row">
                <div class="col quadro"><button class="botpadrGr fundoAmarelo" onclick="abreRegistro();">Registro de Recebimento</button></div>
                <div class="col quadro"><h3>Registro de Bens Encontrados</h3></div> <!-- Central - espaçamento entre colunas  -->
                <div class="col quadro"><img src="imagens/iinfo.png" height="20px;" style="cursor: pointer;" onclick="carregaHelpOcor();" title="Guia rápido"></div> 
            </div>
        </div>
        <br><br>
<!--
        <div class="container" style="margin: 0 auto;">
            <div class="row">
                <div class="col quadro"><button class="botpadrGr" onclick="abreRegistro();">Registro de Recebimento</button></div>
                <div class="col quadro"><button class="botpadrGr" onclick="regTermoRcb();">Termo de Recebimento</button></div>
                <div class="col quadro"><button class="botpadrGr" onclick="regRest();">Registro de Restituição</button></div> 
            </div>
        </div>
        <br><br>

        <div class="container" style="margin: 0 auto;">
            <div class="row">
                <div class="col quadro"><button class="botpadrGr" onclick="regEnc();">Encaminhamento</button></div>
                <div class="col-1" style="width: 1%;"></div>
                <div class="col quadro"><button class="botpadrGr" onclick="regDest();">Destinação</button></div> 
            </div>
        </div>
    -->
<!--<div class="col-12 col-xl-4 col-lg-4 col-md-4 col-sm-12 " style="border: 1px solid;">Teste de coluna</div> -->

        <input type="hidden" id="guardacod" value="0" /> <!-- id ocorrência -->
        <input type="hidden" id="mudou" value="0" />


        <div style="margin: 20px; border: 2px solid blue; border-radius: 15px; padding: 20px;">
            <div id="carregaBens"></div>
        </div>



        <!-- div modal para registrar ocorrência  -->
        <div id="relacmodalRegistro" class="relacmodal">
            <div class="modal-content-Bens">
                <span class="close" onclick="fechaModalReg();">&times;</span>
                <!-- div três colunas -->
                <div class="container" style="margin: 0 auto;">
                    <div class="row">
                        <div class="col quadro" style="margin: 0 auto;"><button class="botpadrred" id="botimprReg" style="font-size: 80%;" id="botimpr" onclick="imprReg();">Gerar PDF</button></div>
                        <div class="col quadro"><h5 id="titulomodal" style="color: #666;">Registro de Recebimento de Bens Encontrados</h5></div> <!-- Central - espaçamento entre colunas  -->
                        <div class="col quadro" style="margin: 0 auto; text-align: center;"><!-- <button class="botpadrred" onclick="enviaModalReg(1);">Enviar</button> --> </div> 
                    </div>
                </div>
                <div style="border: 2px solid blue; border-radius: 10px; padding: 10px; text-align: center;">
                    <div style="paddling-left: 5%; text-align: center;">
                        <div style="text-align: center;">
                            <label class="etiqAzul">Data do recebimento: </label>
                            <input type="text" id="dataregistro" onclick="tiraBorda(id);" value="<?php echo $Hoje; ?>" onchange="modif();" placeholder="Data" style="font-size: .9em; width: 100px; text-align: center;">
                            <label id="numregistro" class="etiqAzul" style="padding-left: 30px; color: red;"></label>
                        </div>

                        <div style="text-align: center; margin: 5px;">
                            <label class="etiqAzul">Descrição do bem encontrado: </label><br>
                            <textarea style="border: 1px solid blue; border-radius: 10px;" rows="3" cols="65" id="descdobem" onchange="modif();"></textarea>
                        </div>
                        

                        <div style="text-align: center; margin: 5px;">
                            <label class="etiqAzul">Data em que foi encontrado: </label>
                            <input type="text" id="dataachado" onclick="tiraBorda(id);" value="<?php echo $Hoje; ?>" onchange="modif();" placeholder="Data" style="font-size: .9em; width: 100px; text-align: center;">
                        </div>
                        <div style="text-align: center; margin: 5px;">
                            <label class="etiqAzul">Local em que foi encontrado: </label><br>
                            <textarea style="border: 1px solid blue; border-radius: 10px;" rows="2" cols="65" id="localachado" onchange="modif();"></textarea>
                        </div>

                        <div style="text-align: center; margin: 5px;">
                            <label class="etiqAzul">Nome do Colaborador que encontrou o bem: </label><br>
                            <input type="text" id="nomeachou" onclick="tiraBorda(id);" value="<?php echo ""; ?>" onchange="modif();" placeholder="Nome do colaborador que encontrou" style="font-size: .9em; width: 70%;">
                        </div>
                        

                        <div style="text-align: center; margin: 5px;">
                            <label class="etiqAzul">Telefone: </label><br>
                            <input type="text" id="telefachou" onclick="tiraBorda(id);" value="<?php echo ""; ?>" onchange="modif();" placeholder="Telefone do colaborador que encontrou" style="font-size: .9em; width: 70%;">
                        </div>
                        
                    </div>
                    <div id="mensagem" style="color: red; font-weight: bold; margin: 5px;"></div>
                    <br>
                    <div style="text-align: center; padding-bottom: 20px;">
                        <button class="botpadrblue" id="botsalvareg" onclick="salvaModalRegistro();">Registrar</button>
                    </div>
                </div>
           </div>
        </div> <!-- Fim Modal-->


    </body>
</html>