<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        <link rel="stylesheet" type="text/css" media="screen" href="class/dataTable/datatables.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/jquery-confirm.min.css" />
        <link rel="stylesheet" type="text/css" media="screen" href="comp/css/relacmod.css" />
        <script src="comp/js/jquery.min.js"></script> <!-- versão 3.6.3 -->
        <script src="comp/js/jquery.mask.js"></script>
        <script src="class/dataTable/datatables.min.js"></script>  <!-- https://datatables.net/examples/basic_init/filter_only.html -->
        <script src="comp/js/jquery-confirm.min.js"></script>   <!-- https://craftpip.github.io/jquery-confirm/#quickfeatures -->
        <style type="text/css">
            .etiq{
                text-align: right; color: #036; font-size: .9em; font-weight: bold; padding: 3px;
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
                    $("#dataIniAgua").mask("99/99/9999");
                    $("#dataIniEletric").mask("99/99/9999");
                    $("#cardiretoria").load("modulos/config/carDir.php");
                });

                function salvaParam(Valor, Param){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=salvaParam&param="+Param+"&valor="+Valor, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    } 
                }
                function MarcaAdm(obj){
                    if(obj.checked === true){
                        Valor = 1;
                    }else{
                        Valor = 0;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=salvaAdm&valor="+Valor+"&caixa="+obj.value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }

                function salvaLeitIniAgua(Valor){
                    if(document.getElementById("valoriniagua").value === ""){
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=valorleituraAgua&valor="+document.getElementById("valoriniagua").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }else{
                                        alert("Valor anotado.");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }
                function salvaDataIniAgua(Valor){
                    if(document.getElementById("dataIniAgua").value === ""){
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=dataleituraAgua&valor="+encodeURIComponent(document.getElementById("dataIniAgua").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }else{
                                        alert("Valor anotado.");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }
                function salvaLeitIniEletric(Valor){
                    if(document.getElementById("valoriniagua").value === ""){
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=valorleituraEletric&valor="+document.getElementById("valorIniEletric").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }else{
                                        alert("Valor anotado.");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }
                function salvaDataIniEletric(Valor){
                    if(document.getElementById("dataIniAgua").value === ""){
                        return false;
                    }
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=dataleituraEletric&valor="+encodeURIComponent(document.getElementById("dataIniEletric").value), true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }else{
                                        alert("Valor anotado.");
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }

                function zeraAgua(){
                    $.confirm({
                    title: 'Apagar',
                    content: 'Confirma apagar todos os lançamentos do Controle do Consumo de Água?  Não haverá possibilidade de recuperação. Continua?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            zeraAguaDef();
                        },
                        Não: function () {
                        }
                    }
                });
            }
            function zeraAguaDef(){
                $.confirm({
                    title: 'Tem certeza?',
                    content: 'Tem certeza que quer apagar todos os lançamentos?  Não haverá possibilidade de recuperação. Continua?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/config/registr.php?acao=apagaAgua", true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                alert("Arquivos zerados.")
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
            function zeraEletric(){
                    $.confirm({
                    title: 'Apagar',
                    content: 'Confirma apagar todos os lançamentos do Controle do Consumo de Energia Elétrica?  Não haverá possibilidade de recuperação. Continua?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            zeraEletricDef();
                        },
                        Não: function () {
                        }
                    }
                });
            }
            function zeraEletricDef(){
                $.confirm({
                    title: 'Tem certeza?',
                    content: 'Tem certeza que quer apagar todos os lançamentos?  Não haverá possibilidade de recuperação. Continua?',
                    autoClose: 'Não|10000',
                    draggable: true,
                    buttons: {
                        Sim: function () {
                            ajaxIni();
                            if(ajax){
                                ajax.open("POST", "modulos/config/registr.php?acao=apagaEletric", true);
                                ajax.onreadystatechange = function(){
                                    if(ajax.readyState === 4 ){
                                        if(ajax.responseText){
//alert(ajax.responseText);
                                            Resp = eval("(" + ajax.responseText + ")");
                                            if(parseInt(Resp.coderro) === 1){
                                                alert("Houve um erro no servidor.")
                                            }else{
                                                alert("Arquivos zerados.")
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
            function carregaModal(Cod){
                document.getElementById("guardacodsetor").value = Cod;
                ajaxIni();
                if(ajax){
                    ajax.open("POST", "modulos/config/registr.php?acao=buscadir&codigo="+Cod, true);
                    ajax.onreadystatechange = function(){
                        if(ajax.readyState === 4 ){
                            if(ajax.responseText){
//alert(ajax.responseText);
                                Resp = eval("(" + ajax.responseText + ")");
                                if(parseInt(Resp.coderro) > 0){
                                    alert("Houve erro ao salvar");
                                }else{
                                    document.getElementById("sigladir").value = Resp.sigla;
                                    document.getElementById("descdir").value = Resp.desc;
                                    $("#relausuarios").load("modulos/config/relDir.php?codigo="+Cod); // está em relDir.php
                                    document.getElementById("relacmodalDir").style.display = "block"; // está em carDir.php
                                }
                            }
                        }
                    };
                    ajax.send(null);
                }
            }
            function salvaModalDir(){
                if(parseInt(document.getElementById("mudou").value) === 1){
                    ajaxIni();
                    if(ajax){
                        ajax.open("POST", "modulos/config/registr.php?acao=salvadir&codigo="+document.getElementById("guardacod").value
                        +"&sigladir="+document.getElementById("sigladir").value
                        +"&descdir="+document.getElementById("descdir").value, true);
                        ajax.onreadystatechange = function(){
                            if(ajax.readyState === 4 ){
                                if(ajax.responseText){
//alert(ajax.responseText);
                                    Resp = eval("(" + ajax.responseText + ")");
                                    if(parseInt(Resp.coderro) > 0){
                                        alert("Houve erro ao salvar");
                                    }else{
                                        $("#cardiretoria").load("modulos/config/carDir.php");
                                        document.getElementById("relacmodalDir").style.display = "none";
                                    }
                                }
                            }
                        };
                        ajax.send(null);
                    }
                }else{
                    document.getElementById("relacmodalDir").style.display = "none";
                }
            }
            function fechaModalDir(){
                document.getElementById("relacmodalDir").style.display = "none";
            }
            function modif(){ // assinala se houve qualquer modificação nos campos do modal durante a edição para evitar salvar desnecessariamente
                document.getElementById("mudou").value = "1";
            }
        </script>
    </head>
    <body>
        <?php
            require_once("abrealas.php");
            $rsSis = pg_query($Conec, "SELECT admvisu, admedit, admcad, insevento, editevento, instarefa, edittarefa, insramais, editramais, instelef, edittelef, 
            editpagina, insarq, insaniver, editaniver, instroca, edittroca, insocor, editocor, insleituraagua, editleituraagua, TO_CHAR(datainiagua , 'DD/MM/YYYY'), valoriniagua, insleituraeletric, editleituraeletric, TO_CHAR(datainieletric , 'DD/MM/YYYY'), valorinieletric, insaguaindiv, inseletricindiv 
            FROM ".$xProj.".paramsis WHERE idPar = 1");
            $ProcSis = pg_fetch_row($rsSis);
            $admVisu = $ProcSis[0]; // admVisu - administrador visualiza usuários
            $admEdit = $ProcSis[1]; // admEdit - administrador edita usuários
            $admCad = $ProcSis[2];  // admCad - administrador cadastra usuários
            $DataIniAgua = $ProcSis[21]; // controle de consumo de água - leitura do hidrômetro
            $ValorIniAgua = $ProcSis[22];  // controle de consumo de água - data inicial
            $DataIniEletric = $ProcSis[25]; // controle de consumo de eletricidade
            $ValorIniEletric = $ProcSis[26]; 

            $insEvento = $ProcSis[3];   // insEvento - inserção de eventos no calendário
            $rs1 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insEvento");
            $Proc1 = pg_fetch_row($rs1);
            $nomeInsEvento = $Proc1[0];

            $editEvento = $ProcSis[4];   // editEvento - edição de eventos no calendário
            $rs2 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editEvento");
            $Proc2 = pg_fetch_row($rs2);
            $nomeEditEvento = $Proc2[0];

            $insLeituraAgua = $ProcSis[19];   // insLeitura - inserção de leitura do hidrômetro
            $rs1 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insLeituraAgua");
            $Proc1 = pg_fetch_row($rs1);
            $nomeInsLeituraAgua = $Proc1[0];

            $editAgua = $ProcSis[20];   // editLeitura - edição de leitura
            $rs2 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editAgua");
            $Proc2 = pg_fetch_row($rs2);
            $nomeEditAgua = $Proc2[0];

            $InsAguaIndiv = $ProcSis[27]; 
            if($InsAguaIndiv > 0){
                $rs3 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $InsAguaIndiv");
                $Proc3 = pg_fetch_row($rs3);
                $nomeInsAgua = $Proc3[0];
            }else{
                $nomeInsAgua = "";
            }

            $InsEletricIndiv = $ProcSis[28]; 
            if($InsEletricIndiv > 0){
                $rs3 = pg_query($Conec, "SELECT nomecompl FROM ".$xProj.".poslog WHERE pessoas_id = $InsEletricIndiv");
                $Proc3 = pg_fetch_row($rs3);
                $nomeInsEletric = $Proc3[0];
            }else{
                $nomeInsEletric = "";
            }

            $insLeituraEletric = $ProcSis[23];   // insLeitura - inserção de leitura do medidor
            $rs1 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insLeituraEletric");
            $Proc1 = pg_fetch_row($rs1);
            $nomeInsLeituraEletric = $Proc1[0];

            $editEletric = $ProcSis[24];   // editLeitura - edição de leitura
            $rs2 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editEletric");
            $Proc2 = pg_fetch_row($rs2);
            $nomeEditEletric = $Proc2[0];


            $insTarefa = $ProcSis[5];   // insTarefa - inserção de tarefas
            $rs3 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insTarefa");
            $Proc3 = pg_fetch_row($rs3);
            $nomeInsTarefa = $Proc3[0];

            $editTarefa = $ProcSis[6];   // editTarefa - edição de tarefas
            $rs4 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editTarefa");
            $Proc4 = pg_fetch_row($rs4);
            $nomeEditTarefa = $Proc4[0];
            
            $insRamais = $ProcSis[7];   // insRamais - edição de ramais internos
            $rs5 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insRamais");
            $Proc5 = pg_fetch_row($rs5);
            $nomeInsRamais = $Proc5[0];

            $editRamais = $ProcSis[8];   // editRamais - edição de ramais internos
            $rs6 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editRamais");
            $Proc6 = pg_fetch_row($rs6);
            $nomeEditRamais = $Proc6[0];

            $insTelef = $ProcSis[9];   // insTelef - edição de telefones
            $rs7 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insTelef");
            $Proc7 = pg_fetch_row($rs7);
            $nomeInsTelef = $Proc7[0];

            $editTelef = $ProcSis[10];   // editTelef - edição de telefones
            $rs8 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editTelef");
            $Proc8 = pg_fetch_row($rs8);
            $nomeEditTelef = $Proc8[0];

            $editPagina = $ProcSis[11];   // editPagina - edição das páginas das diretorias/assessorias
            $rs9 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editPagina");
            $Proc9 = pg_fetch_row($rs9);
            $nomeEditPagina = $Proc9[0];

            $insArq = $ProcSis[12];   // insArq - edição das páginas das diretorias/assessorias
            $rs10 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insArq");
            $Proc10 = pg_fetch_row($rs10);
            $nomeInsArq = $Proc10[0];

            $insAniver = $ProcSis[13];   // insAniver - edição das páginas das diretorias/assessorias
            $rs11 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insAniver");
            $Proc11 = pg_fetch_row($rs11);
            $nomeInsAniver = $Proc11[0];

            $editAniver = $ProcSis[14];   // editAniver - edição de telefones
            $rs12 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editAniver");
            $Proc12 = pg_fetch_row($rs12);
            $nomeEditAniver = $Proc12[0];
            
            $insTroca = $ProcSis[15];   // insTroca - edição de trocas
            $rs13 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insTroca");
            $Proc13 = pg_fetch_row($rs13);
            $nomeInsTroca = $Proc13[0];

            $editTroca = $ProcSis[16];   // editTroca - edição de trocas
            $rs14 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editTroca");
            $Proc14 = pg_fetch_row($rs14);
            $nomeEditTroca = $Proc14[0];

            $insOcor = $ProcSis[17];   // insOcor - registro de ocorrências
            $rs15 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $insOcor");
            $Proc15 = pg_fetch_row($rs15);
            $nomeInsOcor = $Proc15[0];

            $editOcor = $ProcSis[18];   // editOcor
            $rs16 = pg_query($Conec, "SELECT adm_nome FROM ".$xProj.".usugrupos WHERE adm_fl = $editOcor");
            $Proc16 = pg_fetch_row($rs16);
            $nomeEditOcor = $Proc16[0];


            $OpAdmInsEv = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmEditEv = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            
            $OpAdmInsAgua = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmEditAgua = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");

            $OpInsAguaIndiv = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE Ativo = 1 ORDER BY nomecompl"); 
            $OpInsEletricIndiv = pg_query($Conec, "SELECT pessoas_id, nomecompl FROM ".$xProj.".poslog WHERE Ativo = 1 ORDER BY nomecompl");

            $OpAdmInsEletric = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmEditEletric = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");

            $OpAdmInsTar = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmEditTar = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");

            $OpAdmInsRamais = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmEditRamais = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");

            $OpAdmInsTelef = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmEditTelef = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");

            $OpAdmEditPag = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmInsArq = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");

            $OpAdmInsTroca = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmEditTroca = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");

            $OpAdmInsOcor = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");
            $OpAdmEditOcor = pg_query($Conec, "SELECT adm_fl, adm_nome FROM ".$xProj.".usugrupos WHERE Ativo = 1 ORDER BY adm_fl");

        ?>
        <input type="hidden" id="guardacod" value="0" /> <!-- id ocorrência -->
        <input type="hidden" id="mudou" value="0" /> <!-- valor 1 quando houver mudança em qualquer campo do modal -->
        
        <div style="margin: 0 auto; margin-top: 40px; padding: 20px; border: 2px solid blue; border-radius: 15px; width: 70%; min-height: 200px;">
            <div style="text-align: center;">
                <h4>Parâmetros do Sistema</h4>
            </div>


<!-- Calendário  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Calendário</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR eventos:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insEvento');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insEvento; ?>"><?php echo $nomeInsEvento; ?></option>
                            <?php 
                            if($OpAdmInsEv){
                                while ($Opcoes = pg_fetch_row($OpAdmInsEv)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR eventos:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editEvento');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editEvento; ?>"><?php echo $nomeEditEvento; ?></option>
                            <?php 
                            if($OpAdmEditEv){
                                while ($Opcoes = pg_fetch_row($OpAdmEditEv)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>


<!-- Leitura Hidrômetro  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Controle do Consumo de Água - Leitura do Hidrômetro</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR leitura:</td>
                        <td style="padding-left: 5px;">
                        <select id="selectLeituraAgua" onchange="salvaParam(value, 'insleituraagua');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insLeituraAgua; ?>"><?php echo $nomeInsLeituraAgua; ?></option>
                            <?php 
                            if($OpAdmInsAgua){
                                while ($Opcoes = pg_fetch_row($OpAdmInsAgua)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                        <td title="Autorização para um só usuário realizar as leituras. Sobrepõe-se ao nível mínimo selecionado."> Individual:</td>
                        <td>
                        <select id="selectAguaIndiv" onchange="salvaParam(value, 'insaguaindiv');" style="font-size: 1rem; width: 200px;"  title="Autorização para um só usuário realizar as leituras. Sobrepõe-se ao nível mínimo selecionado. Selecione um usuário.">
                            <option value="<?php echo $InsAguaIndiv; ?>"><?php echo $nomeInsAgua; ?></option>
                            <option value="0"></option>
                            <?php 
                            if($OpInsAguaIndiv){
                                while ($Opcoes = pg_fetch_row($OpInsAguaIndiv)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR leitura:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editleituraagua');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editAgua; ?>"><?php echo $nomeEditAgua; ?></option>
                            <?php 
                            if($OpAdmEditAgua){
                                while ($Opcoes = pg_fetch_row($OpAdmEditAgua)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align: right; font-size: 80%; padding-right: 3px;">Data Inicial:</td>
                        <td style="text-align: left; font-size: 80%; padding-left: 3px;"><input type="text" id="dataIniAgua" value="<?php echo $DataIniAgua; ?>" onchange="salvaDataIniAgua(value);" style="width: 90px; text-align: center;"></td>
                    </tr>
                    <tr>
                        <td style="text-align: right; font-size: 80%; padding-right: 3px;">Leitura Inicial:</td>
                        <td style="text-align: left; font-size: 80%; padding-left: 3px;"><input type="text" id="valoriniagua" value="<?php echo $ValorIniAgua; ?>" onchange="salvaLeitIniAgua(value);" style="width: 90px; text-align: center;"></td>
                    </tr>
                </table>
                <div style="text-align: right;">
                <button class="botpadr" onclick="zeraAgua();">Apagar Tudo</button></div>
            </div>

<!-- Leitura Eletricidade  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Controle do Consumo de Eletricidade - Leitura do Medidor</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR leitura:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insleituraeletric');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insLeituraEletric; ?>"><?php echo $nomeInsLeituraEletric; ?></option>
                            <?php 
                            if($OpAdmInsEletric){
                                while ($Opcoes = pg_fetch_row($OpAdmInsEletric)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>

                        <td title="Autorização para um só usuário realizar as leituras. Sobrepõe-se ao nível mínimo selecionado."> Individual:</td>
                        <td>
                        <select id="selectAguaIndiv" onchange="salvaParam(value, 'inseletricindiv');" style="font-size: 1rem; width: 200px;"  title="Autorização para um só usuário realizar as leituras. Sobrepõe-se ao nível mínimo selecionado. Selecione um usuário.">
                            <option value="<?php echo $InsEletricIndiv; ?>"><?php echo $nomeInsEletric; ?></option>
                            <option value="0"></option>
                            <?php 
                            if($OpInsEletricIndiv){
                                while ($Opcoes = pg_fetch_row($OpInsEletricIndiv)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR leitura:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editleituraeletric');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editEletric; ?>"><?php echo $nomeEditEletric; ?></option>
                            <?php 
                            if($OpAdmEditEletric){
                                while ($Opcoes = pg_fetch_row($OpAdmEditEletric)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td style="text-align: right; font-size: 80%; padding-right: 3px;">Data Inicial:</td>
                        <td style="text-align: left; font-size: 80%; padding-left: 3px;"><input type="text" id="dataIniEletric" value="<?php echo $DataIniEletric; ?>" onchange="salvaDataIniEletric(value);" style="width: 90px; text-align: center;"></td>
                    </tr>
                    <tr>
                        <td style="text-align: right; font-size: 80%; padding-right: 3px;">Leitura Inicial:</td>
                        <td style="text-align: left; font-size: 80%; padding-left: 3px;"><input type="text" id="valorIniEletric" value="<?php echo $ValorIniEletric; ?>" onchange="salvaLeitIniEletric(value);" style="width: 90px; text-align: center;"></td>
                    </tr>
                </table>
                <div style="text-align: right;">
                <button class="botpadr" onclick="zeraEletric();">Apagar Tudo</button></div>
            </div>

<!-- Páginas  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Páginas das Diretorias/Assessorias</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR arquivos:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insArq');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $insArq; ?>"><?php echo $nomeInsArq; ?></option>
                            <?php 
                            if($OpAdmInsArq){
                                while ($Opcoes = pg_fetch_row($OpAdmInsArq)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Nível mínimo para EDITAR página:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editPagina');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editPagina; ?>"><?php echo $nomeEditPagina; ?></option>
                            <?php 
                            if($OpAdmEditPag){
                                while ($Opcoes = pg_fetch_row($OpAdmEditPag)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

<!-- Ramais  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Ramais Internos</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR ramais:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insRamais');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insRamais; ?>"><?php echo $nomeInsRamais; ?></option>
                            <?php 
                            if($OpAdmInsRamais){
                                while ($Opcoes = pg_fetch_row($OpAdmInsRamais)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR ramais:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editRamais');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editRamais; ?>"><?php echo $nomeEditRamais; ?></option>
                            <?php 
                            if($OpAdmEditRamais){
                                while ($Opcoes = pg_fetch_row($OpAdmEditRamais)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>


<!-- Ocorrrências  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Registro de Ocorrêncas</b>: <label style="color: gray; font-size: .8em;">Cada usuário só pode ver as ocorrências que inseriu</label><br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR ocorrência:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insOcor');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $insOcor; ?>"><?php echo $nomeInsOcor; ?></option>
                            <?php 
                            if($OpAdmInsOcor){
                                while ($Opcoes = pg_fetch_row($OpAdmInsOcor)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>Nível mínimo para EDITAR ocorrência:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editOcor');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editOcor; ?>"><?php echo $nomeEditOcor; ?></option>
                            <?php 
                            if($OpAdmEditOcor){
                                while ($Opcoes = pg_fetch_row($OpAdmEditOcor)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>


<!-- Tarefas  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Tarefas</b>: <label style="color: gray; font-size: .8em;">Cada nível insere tarefa para seu nível administrativo ou nível inferior</label><br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR tarefas:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insTarefa');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insTarefa; ?>"><?php echo $nomeInsTarefa; ?></option>
                            <?php 
                            if($OpAdmInsTar){
                                while ($Opcoes = pg_fetch_row($OpAdmInsTar)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR tarefas:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editTarefa');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editTarefa; ?>"><?php echo $nomeEditTarefa; ?></option>
                            <?php 
                            if($OpAdmEditTar){
                                while ($Opcoes = pg_fetch_row($OpAdmEditTar)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>


<!-- Telefones  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Telefones Úteis</b>:<br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR telefones:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insTelef');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insTelef; ?>"><?php echo $nomeInsTelef; ?></option>
                            <?php 
                            if($OpAdmInsTelef){
                                while ($Opcoes = pg_fetch_row($OpAdmInsTelef)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR telefones:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editTelef');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editTelef; ?>"><?php echo $nomeEditTelef; ?></option>
                            <?php 
                            if($OpAdmEditTelef){
                                while ($Opcoes = pg_fetch_row($OpAdmEditTelef)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

<!-- Trocas  -->
            <div style="margin: 5px; border: 1px solid; border-radius: 10px; padding: 15px;">
                - <b>Trocas de Objetos</b>: <label style="color: gray; font-size: .8em;">É editável pelo setor que inseriu</label><br>
                <table style="margin: 0 auto;">
                    <tr>
                        <td>Nível mínimo para INSERIR trocas:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'insTroca');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                            <option value="<?php echo $insTroca; ?>"><?php echo $nomeInsTroca; ?></option>
                            <?php 
                            if($OpAdmInsTroca){
                                while ($Opcoes = pg_fetch_row($OpAdmInsTroca)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                    <td>Nível mínimo para EDITAR trocas:</td>
                        <td style="padding-left: 5px;">
                        <select onchange="salvaParam(value, 'editTroca');" style="font-size: 1rem; width: 200px;" title="Selecione um nível de usuário.">
                        <option value="<?php echo $editTroca; ?>"><?php echo $nomeEditTroca; ?></option>
                            <?php 
                            if($OpAdmEditTroca){
                                while ($Opcoes = pg_fetch_row($OpAdmEditTroca)){ ?>
                                    <option value="<?php echo $Opcoes[0]; ?>"><?php echo $Opcoes[1]; ?></option>
                                <?php 
                                }
                            }
                            ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
            <br>
            <hr>
            <br>
            <div id="cardiretoria"></div> <!-- Mostra as diretores e assessorias mais os seus usuários  -->
        </div>
    </body>
</html>