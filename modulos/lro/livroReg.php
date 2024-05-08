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
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/indlog.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script> <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <script src="comp/js/jquery.mask.js"></script>
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
                $("#carregaReg").load("modulos/lro/relReg.php");
                $("#dataocor").mask("99/99/9999");
                var nHora = new Date();   //   function mostraRelogio()  //em relacao.js da SCer
                var hora = nHora.getHours();
                var minuto = nHora.getMinutes();
                if(hora >= 0){
                    document.getElementById("selecturno").value = "3";
                }
                if(hora >= 7){
                    document.getElementById("selecturno").value = "1";
                }
                if(hora > 13){
                    document.getElementById("selecturno").value = "2";
                }
                if(hora > 19){
                    document.getElementById("selecturno").value = "3";
                }

//                if(parseInt(document.getElementById("UsuAdm").value) >= parseInt(document.getElementById("admEdit").value)){
                if(parseInt(document.getElementById("UsuAdm").value) > 6){
                    document.getElementById("botimpr").style.visibility = "visible"; // botão de imprimir
                    document.getElementById("botedit").style.visibility = "visible"; // botão de editar
                }else{
                    document.getElementById("botimpr").style.visibility = "hidden"; // botão de imprimir
                    document.getElementById("botedit").style.visibility = "hidden"; // botão de editar
                }
                modalMostra = document.getElementById('relacMostramodalReg'); //span[0]
                spanMostra = document.getElementsByClassName("close")[0];
                window.onclick = function(event){
                    if(event.target === modalMostra){
                        modalMostra.style.display = "none";
                    }
                };

            });
            
            function InsRegistro(){ // inserir novo registro 
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=buscaAcessoLro&geradata="+document.getElementById("guardahoje").value+"&geraturno="+document.getElementById("selecturno").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    if(parseInt(Resp.acessoLro) === 1){
                                        document.getElementById("guardacod").value = 0;
                                        document.getElementById("mudou").value = "1";
                                        document.getElementById("dataocor").value = document.getElementById("guardahoje").value;
                                        document.getElementById("nomeusuario").innerHMTL = "";
                                        document.getElementById("selectusuant").value = "";
                                        document.getElementById("relato").value = "";
                                        document.getElementById("relacmodalReg").style.display = "block";

                                        if(parseInt(Resp.jatem) > 0){
//                                            $.confirm({
//                                                title: 'Confirmação!',
//                                                content: 'Parece que este turno já foi lançado. <br>Confirma inserir outro registro?',
//                                                draggable: true,
//                                                buttons: {
//                                                    Sim: function () {
//                                                        //continua
//                                                    },
//                                                    Não: function () {
//                                                        document.getElementById("relacmodalReg").style.display = "none";
//                                                    }
//                                                }
//                                            });

                                        $.confirm({
                                            title: 'Informação!',
                                            content: 'Este turno já foi lançado.',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                    }
                                    }else{
                                        $.confirm({
                                            title: 'Informação!',
                                            content: 'Usuário não cadastrado para acesso ao LRO. <br>Solicite acesso à ATI.',
                                            draggable: true,
                                            buttons: {
                                                OK: function(){}
                                            }
                                        });
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function mostraModal(Cod){ // só mostra após o clique, para editar chama carregaModal()
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=buscaReg&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    if(parseInt(Resp.acessoLro) === 1){
                                        document.getElementById("guardacod").value = Cod;
                                        document.getElementById("mostradataocor").value = Resp.data;
                                        document.getElementById("mostraselecturno").value = Resp.descturno;
                                        document.getElementById("mostranomeusuario").innerHTML = Resp.nomeusuins;
                                        document.getElementById("mostraselectusuant").value = Resp.nomeusuant;
                                        document.getElementById("mostrarelato").value = Resp.relato;
                                        document.getElementById("relacMostramodalReg").style.display = "block";
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function carregaModal(){
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=buscaReg&codigo="+document.getElementById("guardacod").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    document.getElementById("dataocor").value = Resp.data;
                                    document.getElementById("selecturno").value = Resp.turno;
                                    document.getElementById("selectusuant").value = Resp.usuant;
                                    document.getElementById("relato").value = Resp.relato;
                                    document.getElementById("relacMostramodalReg").style.display = "none";
                                    document.getElementById("relacmodalReg").style.display = "block";
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function tiraBorda(id){
                let element = document.getElementById(id);
                element.classList.remove('destacaBorda');
            }

            function salvaModalReg(){
                if(parseInt(document.getElementById("mudou").value) === 0){
                    document.getElementById("relacmodalReg").style.display = "none";
                    return false;
                }

                if(document.getElementById("dataocor").value === ""){
                    let element = document.getElementById('dataocor');
                    element.classList.add('destacaBorda');

                    document.getElementById("dataocor").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Insira a data do registro";
                    $('#mensagem').fadeOut(5000);
                    return false;
                }
                if(document.getElementById("selecturno").value === ""){
                    let element = document.getElementById('selecturno');
                    element.classList.add('destacaBorda');

                    document.getElementById("selecturno").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Selecione o turno do serviço";
                    $('#mensagem').fadeOut(5000);
                    return false;
                }
                if(document.getElementById("selectusuant").value === ""){
                    let element = document.getElementById('selectusuant');
                    element.classList.add('destacaBorda');
                    document.getElementById("selectusuant").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Selecione o funcionário anterior";
                    $('#mensagem').fadeOut(5000);
                    return false;
                }
                if(document.getElementById("relato").value === ""){
                    let element = document.getElementById('relato');
                    element.classList.add('destacaBorda');
                    document.getElementById("relato").focus();
                    $('#mensagem').fadeIn("slow");
                    document.getElementById("mensagem").innerHTML = "Escreva o relato";
                    $('#mensagem').fadeOut(5000);
                    return false;
                }
                if(!validaData(document.getElementById("dataocor").value)){
                    let element = document.getElementById('dataocor');
                    element.classList.add('destacaBorda');
                    $.confirm({
                        title: 'Informação!',
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
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=salvaReg&codigo="+document.getElementById("guardacod").value+
                    "&datareg="+encodeURIComponent(document.getElementById("dataocor").value)+
                    "&turno="+document.getElementById("selecturno").value+
                    "&usuant="+document.getElementById("selectusuant").value+
                    "&relato="+encodeURIComponent(document.getElementById("relato").value), true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else if(parseInt(Resp.coderro) === 2){
                                    alert("A data não está correta.");

                                }else{
                                    document.getElementById("guardacod").value = Resp.codigonovo;
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("relacmodalReg").style.display = "none";
                                    $("#carregaReg").load("modulos/lro/relReg.php");
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function modifTurno(){
                document.getElementById("mudou").value = "1";
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/lro/salvaReg.php?acao=buscaTurno&datareg="+document.getElementById("dataocor").value+"&turnoreg="+document.getElementById("selecturno").value, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) === 1){
                                    alert("Houve um erro no servidor.")
                                }else{
                                    if(parseInt(Resp.jatem) === 1){
                                        $.confirm({
                                            title: 'Confirmação!',
                                            content: 'Parece que este turno já foi lançado. Confirma redigir outro registro?',
                                            draggable: true,
                                            buttons: {
                                                Sim: function () {
                                                    //continua
                                                },
                                                Não: function () {
                                                    document.getElementById("relacmodalReg").style.display = "none";
                                                }
                                            }
                                        });
                                    }
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function modif(){ // assinala se houve qualquer modificação
                document.getElementById("mudou").value = "1";
            }
            function fechaModal(){
                document.getElementById("guardacod").value = 0;
                document.getElementById("relacmodalReg").style.display = "none";
            }
            function fechaMostraModal(){
                document.getElementById("relacMostramodalReg").style.display = "none";
            }
            function imprReg(){
                if(parseInt(document.getElementById("guardacod").value) != 0){
                    if(parseInt(document.getElementById("mudou").value) != 0){
                        $.confirm({
                            title: 'Informação!',
                                content: 'Houve modificação. É necessario salvar antes de imprimir.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }
                    window.open("modulos/lro/imprReg.php?acao=impr&codigo="+document.getElementById("guardacod").value, document.getElementById("guardacod").value);
                }
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
        $admIns = parAdm("insocor", $Conec, $xProj);   // nível para inserir 
        $admEdit = parAdm("editocor", $Conec, $xProj); // nível para editar
        $OpUsuAnt = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE lro = 1 And Ativo = 1 And pessoas_id != ".$_SESSION["usuarioID"]." ORDER BY nomecompl"); // And codsetor = 
        ?>
        <input type="hidden" id="UsuAdm" value="<?php echo $_SESSION["AdmUsu"] ?>" />
        <input type="hidden" id="admIns" value="<?php echo $admIns; ?>" /> <!-- nível mínimo para inserir  -->
        <input type="hidden" id="admEdit" value="<?php echo $admEdit; ?>" /> <!-- nível mínimo para editar -->
        <input type="hidden" id="guardacod" value="0" /> <!-- id ocorrência -->
        <input type="hidden" id="guardahoje" value="<?php echo $Hoje; ?>" /> <!-- data -->
        <input type="hidden" id="mudou" value="0" />

        <div style="margin: 20px; border: 2px solid green; border-radius: 15px; padding: 20px;">
            <div class="box" style="position: relative; float: left; width: 33%;">
                <input type="button" id="botinserir" class="botpadr" value="Inserir Registro" onclick="InsRegistro();">
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: center;">
                <h3>Livro de Registro de Ocorrências</h3>
            </div>
            <div id="carregaReg"></div>
        </div>

        <!-- div modal para mostrar ocorrência  -->
        <div id="relacMostramodalReg" class="relacmodal">
            <div class="modal-content-RegistroLRO">
                <span class="close" onclick="fechaMostraModal();">&times;</span>
                <h3 id="titulomodal" style="text-align: center; color: #666;">Livro de Registro de Ocorrências</h3>

                <div style="border: 2px solid blue; border-radius: 10px; padding: 10px; text-align: center;">
                    <div style="paddling-left: 5%;">
                        <br>
                        <label class="etiqAzul">Escritura do Livro de Registro de Ocorrências em: </label>
                        
                        <input disabled type="text" id="mostradataocor" value="<?php echo $Hoje; ?>" placeholder="Data" style="font-size: .9em; width: 100px; text-align: center;">

                        <label class="etiqAzul"> - Turno: </label><label id="turnosvc" style="font-size: 1.2rem; padding-left: 3px;"></label>
                        <input disabled type="text" id="mostraselecturno" value="" style="font-size: .9em; width: 100px; text-align: center;">
                        <br>
                        <label class="etiqAzul">Registrado por: </label><label id="mostranomeusuario" style="font-size: 1.2rem; padding-left: 3px;"></label>
                        <br>
                        <div style="text-align: center;">
                            <label class="etiqAzul"> - Recebi o serviço de: </label>
                            <input disabled type="text" id="mostraselectusuant" value="" >

                            <label class="etiqAzul"> com as seguintes alterações: </label>
                            <br><br>
                            <textarea disabled id="mostrarelato" style="border: 1px solid blue; border-radius: 10px;" rows="10" cols="85"></textarea>
                        </div>
                        <br>
                    </div>
                    <div id="mostramensagem" style="color: red; font-weight: bold;"></div>
                    <br>
                    <div style="text-align: center; padding-bottom: 20px;">
                        <button id="botedit" class="botpadrblue" onclick="carregaModal();">Editar</button>
                    </div>
                </div>
           </div>
        </div> <!-- Fim Modal-->

        <!-- div modal para registrar ocorrência  -->
        <div id="relacmodalReg" class="relacmodal">
            <div class="modal-content-RegistroLRO">
                <div style="position: absolute;"><button class="botpadrred" style="font-size: 80%;" id="botimpr" onclick="imprReg();">Gerar PDF</button></div>
                <span class="close" onclick="fechaModal();">&times;</span>
                <h3 id="titulomodal" style="text-align: center; color: #666;">Livro de Registro de Ocorrências</h3>

                <div style="border: 2px solid blue; border-radius: 10px; padding: 10px; text-align: center;">
                    <div style="paddling-left: 5%;">
                        <br>
                        <label class="etiqAzul">Escritura do Livro de Registro de Ocorrências em: </label>
                        
                        <input type="text" id="dataocor" onclick="tiraBorda(id);" value="<?php echo $Hoje; ?>" onchange="modif();" placeholder="Data" style="font-size: .9em; width: 100px; text-align: center;">

                        <label class="etiqAzul"> - Turno: </label><label id="turnosvc" style="font-size: 1.2rem; padding-left: 3px;"></label>
                        <select id="selecturno" onclick="tiraBorda(id);" onchange="modifTurno();" style="font-size: 0.8rem;" title="Selecione o turno.">
                            <option value=""></option>
                            <option value="1">07h00 / 13h15</option>
                            <option value="2">13h15 / 19h00</option>
                            <option value="3">19h00 / 07h00</option>
                        </select>
                        <br>
                        <label class="etiqAzul">Titular em serviço: </label><label id="nomeusuario" style="font-size: 1.2rem; padding: 5px;"><?php echo $_SESSION["NomeCompl"]; ?></label>
                        <br>
                        <div style="text-align: center;">
                            <label class="etiqAzul"> - Recebi o serviço de: </label>
                            <select id="selectusuant" style="min-width: 120px;" onclick="tiraBorda(id);" onchange="modif();" title="Selecione um usuário.">
                                <option value=""></option>
                                <?php 
                                if($OpUsuAnt){
                                    while ($Opcoes = pg_fetch_row($OpUsuAnt)){ ?>
                                        <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                    <?php 
                                    }
                                }
                                ?>
                            </select>
                            <label class="etiqAzul"> com as seguintes alterações: </label>
                            <br><br>
                            <textarea style="border: 1px solid blue; border-radius: 10px;" rows="10" cols="85" id="relato" onclick="tiraBorda(id);" onchange="modif();"></textarea>
                        </div>
                        <br>
                    </div>
                    <div id="mensagem" style="color: red; font-weight: bold;"></div>
                    <br>
                    <div style="text-align: center; padding-bottom: 20px;">
                        <button class="botpadrblue" onclick="salvaModalReg();">Salvar</button>
                    </div>
                </div>
           </div>
        </div> <!-- Fim Modal-->
    </body>
</html>