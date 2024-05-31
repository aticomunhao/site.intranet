<?php
session_start();
require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
if(!isset($_SESSION["usuarioID"])){
    header("Location: ../../index.php");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Controle Condicionadores</title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="class/dataTable/datatables.min.js"></script>
        <script src="comp/js/jquery.mask.js"></script>
        <script src="comp/js/jquery-confirm.min.js"></script> 
        <style>
            .modal-content-Controle{
                background: linear-gradient(180deg, white, #86c1eb);
                margin: 15% auto; /* 10% do topo e centrado */
                padding: 20px;
                border: 1px solid #888;
                border-radius: 15px;
                width: 50%; /* acertar de acordo com a tela */
                max-width: 900px;
            }
            .etiqCel{
                text-align: center; 
                border: 1px solid;
                border-radius: 8px;
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
//                document.getElementById("botinserir").style.visibility = "hidden"; // botão de inserir usuário
//                if(parseInt(document.getElementById("UsuAdm").value) === 7){ // superusuário 
//                    document.getElementById("botinserir").style.visibility = "visible";
//                }
                $("#faixacentral").load("modulos/controleAr/relAr.php?acao=todos");
                $("#datavis").mask("99/99/9999");

//                modalEdit = document.getElementById('relacmodalUsu'); //span[0]
//                spanEdit = document.getElementsByClassName("close")[0];
//                window.onclick = function(event){
//                    if(event.target === modalEdit){
//                        modalEdit.style.display = "none";
//                    }
//                };
            });

            function carregaCel(Cel){
                document.getElementById("guardaCel").value = Cel;
            }

            function insAparelho(){
                document.getElementById("guardaid").value = 0;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/controleAr/salvaControle.php?acao=buscanumero", true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                if(parseInt(Resp.coderro) === 0){
                                    document.getElementById("apar").innerHTML = Resp.apar;
                                    document.getElementById("mudou").value = "0";
                                    document.getElementById("relacmodalControle").style.display = "block";

                                }else{
                                    alert("Houve um erro no servidor.")
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }

            function carregaModal(){
                if(document.getElementById("guardaCel").value != "0"){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/controleAr/salvaControle.php?acao=buscadados&codigo="+document.getElementById("guardaid").value+"&celula="+document.getElementById("guardaCel").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 0){
                                        document.getElementById("apar").innerHTML = Resp.apar;
                                        document.getElementById("localap").value = Resp.local;
                                        document.getElementById("datavis").value = Resp.data;
                                        document.getElementById("etiqdatavis").innerHTML = "Mês "+document.getElementById("guardaCel").value+":";
                                        document.getElementById("etiqmes").innerHTML = "Mês "+document.getElementById("guardaCel").value;
                                        document.getElementById("nometec").value = Resp.nome;
                                        document.getElementById("empresa").value = Resp.empresa;
                                        document.getElementById("mudou").value = "0";
                                        document.getElementById("relacmodalControle").style.display = "block";
                                    }else{
                                        alert("Houve um erro no servidor.")
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }
            }

            function salvaModal(){
                if(document.getElementById("mudou").value != "0"){
                    if(document.getElementById("datavis").value !== ""){ // deixa salvar em branco
                        if(!validaData(document.getElementById("datavis").value)){
                            let element = document.getElementById('datavis');
//                        element.classList.add('destacaBorda');
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

                        valor = document.getElementById("datavis").value;
                        const partesData = valor.split('/');
                        const data = { 
                            dia: partesData[0], 
                            mes: partesData[1], 
                            ano: partesData[2] 
                        }
                        if(partesData[1] != document.getElementById("guardaCel").value){
                            $.confirm({
                                title: 'Informação!',
                                content: 'O mês nesta data não correponde ao mês da célula editada.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }
                        if(partesData[2] < 2024){
                            $.confirm({
                                title: 'Informação!',
                                content: 'Verifique o ano nesta data.',
                                draggable: true,
                                buttons: {
                                    OK: function(){}
                                }
                            });
                            return false;
                        }
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/controleAr/salvaControle.php?acao=salvadados&codigo="+document.getElementById("guardaid").value
                        +"&celula="+document.getElementById("guardaCel").value
                        +"&localap="+encodeURIComponent(document.getElementById("localap").value)
                        +"&datavis="+encodeURIComponent(document.getElementById("datavis").value)
                        +"&nometec="+encodeURIComponent(document.getElementById("nometec").value)
                        +"&empresa="+document.getElementById("empresa").value
                        , true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");  //Lê o array que vem
                                    if(parseInt(Resp.coderro) === 1){
                                        alert("Houve um erro no servidor.")
                                    }else{
                                        document.getElementById("relacmodalControle").style.display = "none";
                                        $("#faixacentral").load("modulos/controleAr/relAr.php?acao=todos");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("relacmodalControle").style.display = "none";
                }
            }

            function fechaModal(){
                document.getElementById("guardaid").value = 0;
                document.getElementById("relacmodalControle").style.display = "none";
            }
            function modif(){ // assinala se houve qualquer modificação nos campos do modal durante a edição para evitar salvar desnecessariamente
                document.getElementById("mudou").value = "1";
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

//Provisório
pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".controle_ar (
    id SERIAL PRIMARY KEY, 
    num_ap integer NOT NULL DEFAULT 0,
    localap VARCHAR(50),

    data01 date,
    nome01 VARCHAR(100),
    usuins01 integer DEFAULT 0 NOT NULL,
    datains01 timestamp without time zone DEFAULT '1500-01-01',

    data02 date,
    nome02 VARCHAR(100),
    usuins02 integer DEFAULT 0 NOT NULL,
    datains02 timestamp without time zone DEFAULT '1500-01-01',

    data03 date,
    nome03 VARCHAR(100),
    usuins03 integer DEFAULT 0 NOT NULL,
    datains03 timestamp without time zone DEFAULT '1500-01-01',

    data04 date,
    nome04 VARCHAR(100),
    usuins04 integer DEFAULT 0 NOT NULL,
    datains04 timestamp without time zone DEFAULT '1500-01-01',

    data05 date,
    nome05 VARCHAR(100),
    usuins05 integer DEFAULT 0 NOT NULL,
    datains05 timestamp without time zone DEFAULT '1500-01-01',

    data06 date,
    nome06 VARCHAR(100),
    usuins06 integer DEFAULT 0 NOT NULL,
    datains06 timestamp without time zone DEFAULT '1500-01-01',
    
    data07 date,
    nome07 VARCHAR(100),
    usuins07 integer DEFAULT 0 NOT NULL,
    datains07 timestamp without time zone DEFAULT '1500-01-01',

    data08 date,
    nome08 VARCHAR(100),
    usuins08 integer DEFAULT 0 NOT NULL,
    datains08 timestamp without time zone DEFAULT '1500-01-01',

    data09 date,
    nome09 VARCHAR(100),
    usuins09 integer DEFAULT 0 NOT NULL,
    datains09 timestamp without time zone DEFAULT '1500-01-01',

    data10 date,
    nome10 VARCHAR(100),
    usuins10 integer DEFAULT 0 NOT NULL,
    datains10 timestamp without time zone DEFAULT '1500-01-01',

    data11 date,
    nome11 VARCHAR(100),
    usuins11 integer DEFAULT 0 NOT NULL,
    datains11 timestamp without time zone DEFAULT '1500-01-01',

    data12 date,
    nome12 VARCHAR(100),
    usuins12 integer DEFAULT 0 NOT NULL,
    datains12 timestamp without time zone DEFAULT '1500-01-01',

    empresa_id smallint DEFAULT 0 NOT NULL,
    ativo smallint DEFAULT 1 NOT NULL
    ) 
 ");

 $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".controle_ar LIMIT 3");
 $row = pg_num_rows($rs);
 if($row == 0){
    for ($i = 1; $i <= 68; $i++) {
       pg_query($Conec, "INSERT INTO ".$xProj.".controle_ar (num_ap, empresa_id) VALUES ($i, 1)");
    }
 }

 pg_query($Conec, "CREATE TABLE IF NOT EXISTS ".$xProj.".empresas_ar (
    id SERIAL PRIMARY KEY, 
    empresa VARCHAR(150),
    ativo smallint DEFAULT 1 NOT NULL
    ) 
 ");

 $rs = pg_query($Conec, "SELECT id FROM ".$xProj.".empresas_ar LIMIT 3");
 $row = pg_num_rows($rs);
 if($row == 0){
    pg_query($Conec, "INSERT INTO ".$xProj.".empresas_ar (empresa, ativo) VALUES ('Empresa Contratada', 1)");
 }

//------------------


         $rsEmpr = pg_query($Conec, "SELECT id, empresa FROM ".$xProj.".empresas_ar WHERE ativo = 1");
        ?>
        <div style="margin: 20px; border: 2px solid blue; border-radius: 15px; padding: 20px; min-height: 200px;">
            <div class="box" style="position: relative; float: left; width: 33%;">
                <input type="button" id="botinserir" class="resetbot" style="font-size: 80%;" value="Inserir Novo Aparelho" onclick="insAparelho();">
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: center;">
                <h5>Controle da Manutenção dos Condicionadores de Ar</h5>
            </div>
            <div class="box" style="position: relative; float: left; width: 33%; text-align: left;"></div>

            <div id="faixacentral"></div>
        </div>
        <input type="hidden" id="guardaid" value="0" />
        <input type="hidden" id="guardaCel" value="0" />
        <input type="hidden" id="mudou" value="0" /> <!-- valor 1 quando houver mudança em qualquer campo do modal -->

        <!-- div para edição  -->
        <div id="relacmodalControle" class="relacmodal">  <!-- ("close")[0] -->
            <div class="modal-content-Controle">
                <span class="close" onclick="fechaModal();">&times;</span>
                <h5 id="titulomodal" style="text-align: center; color: #666;">Controle de Manutenção</h5>
                <table style="margin: 0 auto; width: 90%">
                    <tr>
                        <td class="etiq aDir">Aparelho: </td>
                        <td><label class="aCentro" style="padding-left: 5px; font-weight: bold;" id="apar"></label><label id="etiqmes" class="etiq" style="padding-left: 50px; font-size: 90%; font-weight: bold;"></label></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiq aDir">Local de instalação: </td>
                        <td><input type="text" id="localap" valor="" onchange="modif();"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiq aDir">Data da Visita <label id="etiqdatavis" style="font-size: 120%;"></label></td>
                        <td><input type="text" id="datavis" valor="" style="text-align: center; width: 100px;" onchange="modif();"></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="etiq aDir">Nome do Técnico: </td>
                        <td><input type="text" id="nometec" style="width: 100%;" valor="" onchange="modif();"></td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td class="etiq aDir">Empresa: </td>
                        <td>
                        <select id="empresa" onchange="modif();" style="font-size: 1rem; width: 100%;" title="Selecione uma empresa.">
                            <option value="0"></option>
                            <?php 
                            if($rsEmpr){
                                while ($Opcoes = pg_fetch_row($rsEmpr)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="4" style="padding-bottom: 10px;"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center;"><button class="resetbot" style="font-size: .9rem;" onclick="salvaModal();">Salvar</button></td>
                    </tr>
                </table>
            </div>
        </div> <!-- Fim Modal-->

    </body>
</html>